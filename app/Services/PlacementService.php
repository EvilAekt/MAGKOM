<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\JalurEnum;
use App\Enums\RealtimeEventTypeEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Exceptions\Domain\StatusSudahBerubahException;
use App\Models\Bidang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\Pengaturan as PengaturanModel;
use App\Models\PesertaProfil;
use App\Models\SlotKhusus;
use App\Models\User;
use App\Support\Pengaturan;
use App\Support\PeriodeMagang;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PlacementService extends AuditedService
{
    public function __construct(
        protected AuditService $auditService,
        protected QuotaAllocationService $quotaAllocationService,
        protected ?RealtimeEventService $realtimeService = null
    ) {
        parent::__construct($auditService);
        $this->realtimeService = $realtimeService ?? app(RealtimeEventService::class);
    }

    /**
     * Menerima dan meloloskan berkas pendaftaran calon peserta magang (Tahap 1 Verifikasi).
     *
     * @throws StatusSudahBerubahException
     */
    public function terimaBerkas(
        Pendaftaran $pendaftaran,
        User $admin,
        ?string $catatan = null,
        ?UploadedFile $fileSurat = null
    ): Pendaftaran {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use ($pendaftaran, $admin, $catatan, $fileSurat) {
            return DB::transaction(function () use ($pendaftaran, $admin, $catatan, $fileSurat) {
                // 1. Kunci baris pendaftaran untuk mencegah konkurensi verifikasi
                $p = Pendaftaran::whereKey($pendaftaran->id)->lockForUpdate()->firstOrFail();

                if (! in_array($p->status, [StatusPendaftaranEnum::Diajukan, StatusPendaftaranEnum::WaitingList, StatusPendaftaranEnum::PerluPerbaikan], true)) {
                    throw new StatusSudahBerubahException;
                }

                // 2. Kunci dan ambil jatah kuota umum secara atomik (AC-05-4)
                $this->quotaAllocationService->ambilKuotaUmum($p->kuota_id);

                // 3. Simpan berkas surat keterangan diterima yang diunggah jika ada
                $filePath = null;
                if ($fileSurat) {
                    $filePath = $fileSurat->store('surat_diterima', 'local');
                }

                // 4. Update status pendaftaran menjadi Diterima
                $statusLama = $p->status->value;
                $updateData = [
                    'status' => StatusPendaftaranEnum::Diterima,
                    'verifikator_id' => $admin->id,
                    'diputuskan_at' => now(),
                    'catatan' => $catatan,
                ];
                if ($filePath) {
                    $updateData['file_surat_diterima'] = $filePath;
                    $updateData['surat_diterima_uploaded_at'] = now();
                }
                $p->update($updateData);

                // 5. Catat jejak audit
                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'pendaftaran',
                    idData: $p->id,
                    nilaiLama: ['status' => $statusLama],
                    nilaiBaru: ['status' => StatusPendaftaranEnum::Diterima->value],
                    detail: [
                        'admin_id' => $admin->id,
                        'aksi' => 'Menerima dan meloloskan berkas pendaftaran calon peserta (Tahap 1)',
                        'catatan' => $catatan,
                        'file_surat_diterima' => $filePath,
                    ]
                );

                // 6. Notifikasi Real-time SSE (TASK-SSE-05)
                $this->realtimeService->publish(
                    RealtimeEventTypeEnum::StatusPendaftaran,
                    [
                        'title' => 'Selamat! Berkas Pendaftaran Diterima',
                        'message' => 'Berkas pendaftaran magang Anda telah disetujui. Penugasan pembimbing sedang diproses oleh dinas.',
                        'status' => 'diterima',
                        'action_url' => route('peserta.dashboard'),
                    ],
                    $p->user_id
                );

                $this->realtimeService->publish(
                    RealtimeEventTypeEnum::KuotaBerubah,
                    [
                        'title' => 'Pembaruan Kuota Magang',
                        'message' => 'Kuota jurusan telah dialokasikan untuk peserta baru.',
                        'kuota_id' => $p->kuota_id,
                    ],
                    null,
                    'admin'
                );

                return $p;
            }, 3);
        });
    }

    /**
     * Menetapkan pembimbing lapangan dan bidang dinas untuk peserta yang telah diterima (Tahap 2 Penempatan).
     *
     * @throws StatusSudahBerubahException
     */
    public function tetapkanPenempatan(
        Pendaftaran $pendaftaran,
        User $admin,
        User $pembimbing,
        ?Bidang $bidang = null
    ): Penempatan {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use ($pendaftaran, $admin, $pembimbing, $bidang) {
            return DB::transaction(function () use ($pendaftaran, $admin, $pembimbing, $bidang) {
                $p = Pendaftaran::whereKey($pendaftaran->id)->lockForUpdate()->firstOrFail();

                if ($p->status !== StatusPendaftaranEnum::Diterima) {
                    throw new StatusSudahBerubahException('Penetapan penempatan hanya dapat dilakukan untuk pendaftaran yang telah berstatus Diterima.');
                }

                if ($p->penempatan()->exists()) {
                    throw new StatusSudahBerubahException('Peserta ini sudah memiliki data penempatan aktif.');
                }

                // Hitung rentang periode magang (BR-08)
                [$mulai, $selesai] = PeriodeMagang::hitung($p->bulanMagang->bulan_mulai, $p->durasi_bulan);

                // Tentukan bidang: input form, default dari relasi jurusan, atau dari bidang pembimbing
                $bidangId = $bidang?->id
                    ?? $p->kuota?->jurusan?->bidang?->first()?->id
                    ?? $pembimbing->bidang_id
                    ?? $p->kuota?->jurusan?->bidang_id;

                $penempatan = Penempatan::create([
                    'pendaftaran_id' => $p->id,
                    'peserta_id' => $p->user_id,
                    'kuota_id' => $p->kuota_id,
                    'slot_khusus_id' => null,
                    'bidang_id' => $bidangId,
                    'pembimbing_id' => $pembimbing->id,
                    'jalur' => JalurEnum::Umum,
                    'sumber' => SumberPenempatanEnum::Online,
                    'dibuat_oleh' => $admin->id,
                    'tgl_mulai' => $mulai->toDateString(),
                    'tgl_selesai' => $selesai->toDateString(),
                    'file_surat_diterima' => $p->file_surat_diterima,
                    'status' => StatusPenempatanEnum::Aktif,
                ]);

                // Catat jejak audit
                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'penempatan',
                    idData: $penempatan->id,
                    nilaiLama: null,
                    nilaiBaru: ['status' => StatusPenempatanEnum::Aktif->value],
                    detail: [
                        'pendaftaran_id' => $p->id,
                        'admin_id' => $admin->id,
                        'pembimbing_id' => $pembimbing->id,
                        'bidang_id' => $bidangId,
                        'aksi' => 'Menetapkan pembimbing lapangan dan bidang dinas penempatan peserta (Tahap 2)',
                    ]
                );

                // Notifikasi Real-time SSE ke peserta bahwa penempatan & surat diterima siap
                $this->realtimeService->publish(
                    RealtimeEventTypeEnum::StatusPendaftaran,
                    [
                        'title' => 'Penempatan Magang Ditetapkan',
                        'message' => 'Pembimbing lapangan Anda telah ditugaskan. Surat keterangan diterima sudah dapat diunduh.',
                        'status' => 'penempatan_aktif',
                        'action_url' => route('peserta.dashboard'),
                    ],
                    $p->user_id
                );

                return $penempatan;
            }, 3);
        });
    }

    /**
     * Menerima pendaftaran calon peserta magang secara langsung (backward-compatibility wrapper).
     *
     * @throws StatusSudahBerubahException
     */
    public function terima(
        Pendaftaran $pendaftaran,
        User $admin,
        ?User $pembimbing = null,
        ?Bidang $bidang = null
    ): Penempatan {
        return DB::transaction(function () use ($pendaftaran, $admin, $pembimbing, $bidang) {
            $p = $this->terimaBerkas($pendaftaran, $admin);

            $pembimbingTerpilih = $pembimbing ?? User::where('role', RoleEnum::Pembimbing)
                ->where('status_aktif', true)
                ->firstOrFail();

            return $this->tetapkanPenempatan($p, $admin, $pembimbingTerpilih, $bidang);
        });
    }

    /**
     * Menolak pengajuan pendaftaran magang dengan catatan alasan (F-04).
     */
    public function tolak(Pendaftaran $pendaftaran, User $admin, string $alasan): Pendaftaran
    {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use ($pendaftaran, $admin, $alasan) {
            return DB::transaction(function () use ($pendaftaran, $admin, $alasan) {
                $p = Pendaftaran::whereKey($pendaftaran->id)->lockForUpdate()->firstOrFail();

                if (! in_array($p->status, [StatusPendaftaranEnum::Diajukan, StatusPendaftaranEnum::WaitingList], true)) {
                    throw new StatusSudahBerubahException;
                }

                $nilaiLama = $p->toArray();

                $p->update([
                    'status' => StatusPendaftaranEnum::Ditolak,
                    'catatan' => $alasan,
                    'verifikator_id' => $admin->id,
                    'diputuskan_at' => now(),
                ]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'pendaftaran',
                    idData: $p->id,
                    nilaiLama: $nilaiLama,
                    nilaiBaru: $p->toArray(),
                    detail: ['alasan' => $alasan, 'aksi' => 'Menolak pengajuan pendaftaran']
                );

                // Notifikasi Real-time SSE ke peserta (TASK-SSE-05)
                $this->realtimeService->publish(
                    RealtimeEventTypeEnum::StatusPendaftaran,
                    [
                        'title' => 'Pendaftaran Tidak Diterima',
                        'message' => 'Mohon maaf, pengajuan pendaftaran Anda belum dapat diterima.',
                        'status' => 'ditolak',
                        'alasan' => $alasan,
                        'action_url' => route('peserta.dashboard'),
                    ],
                    $p->user_id
                );

                return $p;
            });
        });
    }

    /**
     * Meminta peserta memperbaiki berkas persyaratan (F-04).
     */
    public function mintaPerbaikan(Pendaftaran $pendaftaran, User $admin, string $catatan): Pendaftaran
    {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use ($pendaftaran, $admin, $catatan) {
            return DB::transaction(function () use ($pendaftaran, $admin, $catatan) {
                $p = Pendaftaran::whereKey($pendaftaran->id)->lockForUpdate()->firstOrFail();

                if (! in_array($p->status, [StatusPendaftaranEnum::Diajukan, StatusPendaftaranEnum::WaitingList], true)) {
                    throw new StatusSudahBerubahException;
                }

                $nilaiLama = $p->toArray();

                $p->update([
                    'status' => StatusPendaftaranEnum::PerluPerbaikan,
                    'catatan' => $catatan,
                    'verifikator_id' => $admin->id,
                ]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'pendaftaran',
                    idData: $p->id,
                    nilaiLama: $nilaiLama,
                    nilaiBaru: $p->toArray(),
                    detail: ['catatan' => $catatan, 'aksi' => 'Meminta revisi/perbaikan berkas pendaftaran']
                );

                // Notifikasi Real-time SSE ke peserta (TASK-SSE-05)
                $this->realtimeService->publish(
                    RealtimeEventTypeEnum::StatusPendaftaran,
                    [
                        'title' => 'Permintaan Perbaikan Berkas',
                        'message' => 'Verifikator meminta perbaikan berkas pendaftaran Anda. Silakan cek catatan revisi.',
                        'status' => 'perlu_perbaikan',
                        'catatan' => $catatan,
                        'action_url' => route('peserta.pendaftaran.detail', $p->id),
                    ],
                    $p->user_id
                );

                return $p;
            });
        });
    }

    /**
     * Penempatan manual jalur khusus (SuperAdmin / Admin) (F-06, F-07).
     */
    public function tempatkanManual(
        User $peserta,
        SlotKhusus $slotKhusus,
        User $pembimbing,
        ?Bidang $bidang,
        CarbonInterface|string $bulanMulai,
        int $durasiBulan,
        User $admin
    ): Penempatan {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use (
            $peserta, $slotKhusus, $pembimbing, $bidang, $bulanMulai, $durasiBulan, $admin
        ) {
            return DB::transaction(function () use (
                $peserta, $slotKhusus, $pembimbing, $bidang, $bulanMulai, $durasiBulan, $admin
            ) {
                // 1. Kunci dan ambil slot khusus
                $slot = $this->quotaAllocationService->ambilKuotaKhusus($slotKhusus->id);

                // 2. Hitung periode magang (BR-08)
                [$mulai, $selesai] = PeriodeMagang::hitung($bulanMulai, $durasiBulan);

                // 3. Buat penempatan jalur khusus
                $penempatan = Penempatan::create([
                    'pendaftaran_id' => null,
                    'peserta_id' => $peserta->id,
                    'kuota_id' => null,
                    'slot_khusus_id' => $slot->id,
                    'bidang_id' => $bidang?->id,
                    'pembimbing_id' => $pembimbing->id,
                    'jalur' => JalurEnum::Khusus,
                    'sumber' => SumberPenempatanEnum::Manual,
                    'dibuat_oleh' => $admin->id,
                    'tgl_mulai' => $mulai->toDateString(),
                    'tgl_selesai' => $selesai->toDateString(),
                    'file_surat_diterima' => null,
                    'status' => StatusPenempatanEnum::Aktif,
                ]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'penempatan',
                    idData: $penempatan->id,
                    nilaiBaru: $penempatan->toArray(),
                    detail: ['aksi' => 'Membuat penempatan manual jalur khusus']
                );

                return $penempatan;
            });
        });
    }

    /**
     * Penempatan manual dengan pendaftaran peserta baru langsung (AC-07-3, F-07).
     * Membuat akun user role peserta (email terverifikasi, must_change_password = true), profil, dan penempatan.
     * Mendukung jalur khusus (via slot khusus) atau jalur umum (via kuota jurusan).
     */
    public function tempatkanPesertaBaruManual(
        array $userData,
        array $profilData,
        User $pembimbing,
        ?Bidang $bidang,
        CarbonInterface|string $bulanMulai,
        int $durasiBulan,
        string $jalur,
        ?int $slotKhususId,
        ?int $kuotaId,
        User $admin
    ): Penempatan {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use (
            $userData, $profilData, $pembimbing, $bidang, $bulanMulai, $durasiBulan, $jalur, $slotKhususId, $kuotaId, $admin
        ) {
            return DB::transaction(function () use (
                $userData, $profilData, $pembimbing, $bidang, $bulanMulai, $durasiBulan, $jalur, $slotKhususId, $kuotaId, $admin
            ) {
                // 1. Buat user role peserta (AC-07-3: email terverifikasi, must_change_password aktif)
                // SEC-01: Shared default password elimination for production & unique credential security
                $defaultFallback = app()->environment('testing') ? 'Magkom2026!' : ('Mgk-'.Str::random(10).'!#');
                $plainPassword = ! empty($userData['password']) ? (string) $userData['password'] : $defaultFallback;
                $peserta = new User;
                $peserta->nama = $userData['nama'];
                $peserta->email = $userData['email'];
                $peserta->password = Hash::make($plainPassword);
                $peserta->role = RoleEnum::Peserta;
                $peserta->email_verified_at = now();
                $peserta->must_change_password = true;
                $peserta->status_aktif = true;
                $peserta->password_awal = $plainPassword;
                $peserta->save();

                // 2. Buat profil peserta
                $jurusanId = $profilData['jurusan_id'] ?? null;
                if (! $jurusanId) {
                    $jurusanId = Jurusan::aktif()->first()?->id ?? Jurusan::first()?->id;
                }

                PesertaProfil::create([
                    'user_id' => $peserta->id,
                    'nim_nisn' => $profilData['nim_nisn'] ?? '-',
                    'institusi' => $profilData['institusi'] ?? '-',
                    'jurusan_id' => $jurusanId,
                    'jenjang' => $profilData['jenjang'] ?? 's1',
                    'no_hp' => $profilData['no_hp'] ?? '-',
                ]);

                // 3. Alokasikan kapasitas (slot khusus atau kuota umum)
                $assignedSlotId = null;
                $assignedKuotaId = null;
                $jalurEnum = JalurEnum::Khusus;

                if ($jalur === 'umum' && $kuotaId) {
                    $kuota = $this->quotaAllocationService->ambilKuotaUmum((int) $kuotaId);
                    $assignedKuotaId = $kuota->id;
                    $jalurEnum = JalurEnum::Umum;
                } elseif ($slotKhususId) {
                    $slot = $this->quotaAllocationService->ambilKuotaKhusus((int) $slotKhususId);
                    $assignedSlotId = $slot->id;
                    $jalurEnum = JalurEnum::Khusus;
                } else {
                    $assignedSlotId = null;
                    $jalurEnum = JalurEnum::Khusus;
                }

                // 4. Hitung periode magang (BR-08)
                [$mulai, $selesai] = PeriodeMagang::hitung($bulanMulai, $durasiBulan);

                // 5. Buat penempatan resmi
                $penempatan = Penempatan::create([
                    'pendaftaran_id' => null,
                    'peserta_id' => $peserta->id,
                    'kuota_id' => $assignedKuotaId,
                    'slot_khusus_id' => $assignedSlotId,
                    'bidang_id' => $bidang?->id,
                    'pembimbing_id' => $pembimbing->id,
                    'jalur' => $jalurEnum,
                    'sumber' => SumberPenempatanEnum::Manual,
                    'dibuat_oleh' => $admin->id,
                    'tgl_mulai' => $mulai->toDateString(),
                    'tgl_selesai' => $selesai->toDateString(),
                    'file_surat_diterima' => null,
                    'status' => StatusPenempatanEnum::Aktif,
                ]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'penempatan',
                    idData: $penempatan->id,
                    nilaiBaru: $penempatan->toArray(),
                    detail: [
                        'aksi' => 'Membuat akun dan penempatan manual peserta baru (AC-07-3)',
                        'user_id' => $peserta->id,
                        'email' => $peserta->email,
                        'jalur' => $jalurEnum->value,
                    ]
                );

                $penempatan->setRelation('peserta', $peserta);
                $penempatan->plain_password = $plainPassword;

                return $penempatan;
            });
        });
    }

    /**
     * Memindahkan pembimbing peserta magang aktif (AC-07-4, BR-13, A-12).
     */
    public function pindahPembimbing(Penempatan $penempatan, User $pembimbingBaru, User $admin): Penempatan
    {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use ($penempatan, $pembimbingBaru, $admin) {
            return DB::transaction(function () use ($penempatan, $pembimbingBaru, $admin) {
                $p = Penempatan::whereKey($penempatan->id)->lockForUpdate()->firstOrFail();

                if ($p->status !== StatusPenempatanEnum::Aktif) {
                    throw new \RuntimeException('Pembimbing hanya dapat dipindahkan untuk penempatan yang berstatus aktif.');
                }

                // Cek batas beban pembimbing (BR-13)
                $batasBeban = Pengaturan::int('maks_peserta_aktif_per_pembimbing', 0);
                if ($batasBeban > 0) {
                    $bebanAktif = Penempatan::where('pembimbing_id', $pembimbingBaru->id)
                        ->where('status', StatusPenempatanEnum::Aktif)
                        ->count();

                    if ($bebanAktif >= $batasBeban) {
                        throw new \RuntimeException("Pembimbing {$pembimbingBaru->nama} telah mencapai batas beban maksimum ({$batasBeban} peserta aktif).");
                    }
                }

                $pembimbingLamaId = $p->pembimbing_id;
                $p->update(['pembimbing_id' => $pembimbingBaru->id]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'penempatan',
                    idData: $p->id,
                    nilaiLama: ['pembimbing_id' => $pembimbingLamaId],
                    nilaiBaru: ['pembimbing_id' => $pembimbingBaru->id],
                    detail: [
                        'aksi' => 'Memindahkan pembimbing peserta magang',
                        'pembimbing_lama_id' => $pembimbingLamaId,
                        'pembimbing_baru_id' => $pembimbingBaru->id,
                        'admin_id' => $admin->id,
                    ]
                );

                return $p;
            });
        });
    }

    /**
     * Memindahkan pembimbing peserta magang secara massal (bulk reassign).
     *
     * @param  array<int, int|string>  $ids
     */
    public function alihkanBatch(array $ids, User $pembimbingBaru, User $admin): int
    {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use ($ids, $pembimbingBaru, $admin) {
            $maxBebanSetting = PengaturanModel::where('kunci', 'maks_peserta_aktif_per_pembimbing')->value('nilai');
            if ($maxBebanSetting !== null && $maxBebanSetting !== '') {
                $maxBeban = (int) $maxBebanSetting;
                $currentAktif = Penempatan::where('pembimbing_id', $pembimbingBaru->id)
                    ->where('status', StatusPenempatanEnum::Aktif)
                    ->count();

                $penambahanAktif = Penempatan::whereIn('id', $ids)
                    ->where('status', StatusPenempatanEnum::Aktif)
                    ->where('pembimbing_id', '!=', $pembimbingBaru->id)
                    ->count();

                if (($currentAktif + $penambahanAktif) > $maxBeban) {
                    throw new \RuntimeException("Kapasitas pembimbing {$pembimbingBaru->nama} terlampaui (Batas maksimal: {$maxBeban} mentee aktif, saat ini: {$currentAktif}, penambahan: {$penambahanAktif}).");
                }
            }

            return DB::transaction(function () use ($ids, $pembimbingBaru, $admin) {
                $penempatans = Penempatan::whereIn('id', $ids)->get();
                $updated = 0;

                foreach ($penempatans as $p) {
                    if ($p->pembimbing_id === $pembimbingBaru->id) {
                        continue;
                    }

                    $pembimbingLamaId = $p->pembimbing_id;
                    $p->update(['pembimbing_id' => $pembimbingBaru->id]);

                    $this->auditService->catat(
                        tipe: TipeAuditEnum::Aksi,
                        tabel: 'penempatan',
                        idData: $p->id,
                        nilaiLama: ['pembimbing_id' => $pembimbingLamaId],
                        nilaiBaru: ['pembimbing_id' => $pembimbingBaru->id],
                        detail: [
                            'aksi' => 'Pemindahan pembimbing massal (bulk reassign)',
                            'admin_id' => $admin->id,
                            'pembimbing_baru_id' => $pembimbingBaru->id,
                        ]
                    );

                    $updated++;
                }

                return $updated;
            });
        });
    }

    /**
     * Membatalkan penerimaan atau mencatat pengunduran diri peserta (AC-07-5, BR-07).
     */
    public function batalkanPenempatan(Penempatan $penempatan, User $admin, string $alasan): Penempatan
    {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use ($penempatan, $admin, $alasan) {
            return DB::transaction(function () use ($penempatan, $admin, $alasan) {
                $p = Penempatan::whereKey($penempatan->id)->lockForUpdate()->firstOrFail();

                if ($p->status !== StatusPenempatanEnum::Aktif) {
                    throw new \RuntimeException('Hanya penempatan aktif yang dapat dibatalkan atau dicatat pengunduran dirinya.');
                }

                $hariIni = today('Asia/Jakarta');
                $tglMulai = Carbon::parse($p->tgl_mulai);
                $sebelumMulai = $hariIni->lt($tglMulai);

                if ($sebelumMulai) {
                    // Sebelum tanggal mulai: kembalikan kuota & akun peserta tetap aktif
                    if ($p->kuota_id) {
                        Kuota::whereKey($p->kuota_id)->decrement('terisi');
                    } elseif ($p->slot_khusus_id) {
                        SlotKhusus::whereKey($p->slot_khusus_id)->decrement('terisi');
                    }

                    if ($p->pendaftaran_id) {
                        Pendaftaran::whereKey($p->pendaftaran_id)->update([
                            'status' => StatusPendaftaranEnum::Dibatalkan,
                            'catatan' => $alasan,
                        ]);
                    }
                } else {
                    // Sejak tanggal mulai: kuota tidak dikembalikan, akun peserta dinonaktifkan
                    $p->peserta()->update(['status_aktif' => false]);
                }

                $statusLama = $p->status->value;
                $p->update(['status' => StatusPenempatanEnum::MengundurkanDiri]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'penempatan',
                    idData: $p->id,
                    nilaiLama: ['status' => $statusLama],
                    nilaiBaru: ['status' => StatusPenempatanEnum::MengundurkanDiri->value],
                    detail: [
                        'aksi' => $sebelumMulai ? 'Pembatalan sebelum tanggal mulai (kuota dikembalikan)' : 'Pengunduran diri setelah mulai (kuota hangus, akun nonaktif)',
                        'alasan' => $alasan,
                        'admin_id' => $admin->id,
                    ]
                );

                return $p;
            });
        });
    }
}
