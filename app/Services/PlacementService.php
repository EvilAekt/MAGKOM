<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Exceptions\Domain\StatusSudahBerubahException;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\SlotKhusus;
use App\Models\User;
use App\Support\PeriodeMagang;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PlacementService extends AuditedService
{
    public function __construct(
        protected AuditService $auditService,
        protected QuotaAllocationService $quotaAllocationService
    ) {
        parent::__construct($auditService);
    }

    /**
     * Menerima pendaftaran calon peserta magang secara online (jalur umum) (F-04, F-07).
     *
     * @throws StatusSudahBerubahException
     */
    public function terima(
        Pendaftaran $pendaftaran,
        User $admin,
        ?User $pembimbing = null,
        ?Bidang $bidang = null
    ): Penempatan {
        return $this->jalankan(__FUNCTION__, func_get_args(), function () use ($pendaftaran, $admin, $pembimbing, $bidang) {
            return DB::transaction(function () use ($pendaftaran, $admin, $pembimbing, $bidang) {
                // 1. Kunci baris pendaftaran untuk mencegah konkurensi verifikasi
                $p = Pendaftaran::whereKey($pendaftaran->id)->lockForUpdate()->firstOrFail();

                if (! in_array($p->status, [StatusPendaftaranEnum::Diajukan, StatusPendaftaranEnum::WaitingList], true)) {
                    throw new StatusSudahBerubahException();
                }

                // 2. Kunci dan ambil jatah kuota umum secara atomik (AC-05-4)
                $kuota = $this->quotaAllocationService->ambilKuotaUmum($p->kuota_id);

                // 3. Tentukan pembimbing
                $pembimbingTerpilih = $pembimbing ?? User::where('role', RoleEnum::Pembimbing)
                    ->where('status_aktif', true)
                    ->firstOrFail();

                // 4. Hitung rentang periode magang (BR-08)
                [$mulai, $selesai] = PeriodeMagang::hitung($p->bulanMagang->bulan_mulai, $p->durasi_bulan);

                // 5. Buat entri penempatan aktif
                $penempatan = Penempatan::create([
                    'pendaftaran_id' => $p->id,
                    'peserta_id'     => $p->user_id,
                    'kuota_id'       => $p->kuota_id,
                    'slot_khusus_id' => null,
                    'bidang_id'      => $bidang?->id,
                    'pembimbing_id'  => $pembimbingTerpilih->id,
                    'jalur'          => JalurEnum::Umum,
                    'sumber'         => SumberPenempatanEnum::Online,
                    'dibuat_oleh'    => $admin->id,
                    'tgl_mulai'      => $mulai->toDateString(),
                    'tgl_selesai'    => $selesai->toDateString(),
                    'status'         => StatusPenempatanEnum::Aktif,
                ]);

                // 6. Update status pendaftaran menjadi Diterima
                $statusLama = $p->status->value;
                $p->update([
                    'status'         => StatusPendaftaranEnum::Diterima,
                    'verifikator_id' => $admin->id,
                    'diputuskan_at'  => now(),
                ]);

                // 7. Catat jejak audit
                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'pendaftaran',
                    idData: $p->id,
                    nilaiLama: ['status' => $statusLama],
                    nilaiBaru: ['status' => StatusPendaftaranEnum::Diterima->value],
                    detail: [
                        'penempatan_id' => $penempatan->id,
                        'admin_id' => $admin->id,
                        'aksi' => 'Menerima pendaftaran dan membuat penempatan resmi',
                    ]
                );

                return $penempatan;
            }, 3);
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
                    throw new StatusSudahBerubahException();
                }

                $nilaiLama = $p->toArray();

                $p->update([
                    'status'         => StatusPendaftaranEnum::Ditolak,
                    'catatan'        => $alasan,
                    'verifikator_id' => $admin->id,
                    'diputuskan_at'  => now(),
                ]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'pendaftaran',
                    idData: $p->id,
                    nilaiLama: $nilaiLama,
                    nilaiBaru: $p->toArray(),
                    detail: ['alasan' => $alasan, 'aksi' => 'Menolak pengajuan pendaftaran']
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
                    throw new StatusSudahBerubahException();
                }

                $nilaiLama = $p->toArray();

                $p->update([
                    'status'         => StatusPendaftaranEnum::PerluPerbaikan,
                    'catatan'        => $catatan,
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
                    'peserta_id'     => $peserta->id,
                    'kuota_id'       => null,
                    'slot_khusus_id' => $slot->id,
                    'bidang_id'      => $bidang?->id,
                    'pembimbing_id'  => $pembimbing->id,
                    'jalur'          => JalurEnum::Khusus,
                    'sumber'         => SumberPenempatanEnum::Manual,
                    'dibuat_oleh'    => $admin->id,
                    'tgl_mulai'      => $mulai->toDateString(),
                    'tgl_selesai'    => $selesai->toDateString(),
                    'status'         => StatusPenempatanEnum::Aktif,
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
                $peserta = new User();
                $peserta->nama = $userData['nama'];
                $peserta->email = $userData['email'];
                $peserta->password = Hash::make(Str::random(16));
                $peserta->role = RoleEnum::Peserta;
                $peserta->email_verified_at = now();
                $peserta->must_change_password = true;
                $peserta->status_aktif = true;
                $peserta->save();

                // 2. Buat profil peserta
                PesertaProfil::create([
                    'user_id' => $peserta->id,
                    'nim_nisn' => $profilData['nim_nisn'] ?? '-',
                    'institusi' => $profilData['institusi'] ?? '-',
                    'jurusan_id' => $profilData['jurusan_id'] ?? null,
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
                } else {
                    $slot = $this->quotaAllocationService->ambilKuotaKhusus((int) $slotKhususId);
                    $assignedSlotId = $slot->id;
                    $jalurEnum = JalurEnum::Khusus;
                }

                // 4. Hitung periode magang (BR-08)
                [$mulai, $selesai] = PeriodeMagang::hitung($bulanMulai, $durasiBulan);

                // 5. Buat penempatan resmi
                $penempatan = Penempatan::create([
                    'pendaftaran_id' => null,
                    'peserta_id'     => $peserta->id,
                    'kuota_id'       => $assignedKuotaId,
                    'slot_khusus_id' => $assignedSlotId,
                    'bidang_id'      => $bidang?->id,
                    'pembimbing_id'  => $pembimbing->id,
                    'jalur'          => $jalurEnum,
                    'sumber'         => SumberPenempatanEnum::Manual,
                    'dibuat_oleh'    => $admin->id,
                    'tgl_mulai'      => $mulai->toDateString(),
                    'tgl_selesai'    => $selesai->toDateString(),
                    'status'         => StatusPenempatanEnum::Aktif,
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
                $batasBeban = \App\Support\Pengaturan::int('maks_peserta_aktif_per_pembimbing', 0);
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
                $tglMulai = \Carbon\Carbon::parse($p->tgl_mulai);
                $sebelumMulai = $hariIni->lt($tglMulai);

                if ($sebelumMulai) {
                    // Sebelum tanggal mulai: kembalikan kuota & akun peserta tetap aktif
                    if ($p->kuota_id) {
                        \App\Models\Kuota::whereKey($p->kuota_id)->decrement('terisi');
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

    /**
     * Menutup antrean waiting list secara massal untuk bulan magang yang sudah dimulai (AC-04-7).
     */
    public function tutupWaitingListKadaluarsa(BulanMagang $bulan, ?User $admin = null): int
    {
        $hariIni = now('Asia/Jakarta')->startOfDay();
        $tglMulai = \Carbon\Carbon::parse($bulan->bulan_mulai)->timezone('Asia/Jakarta')->startOfMonth();

        if ($hariIni->lt($tglMulai)) {
            return 0;
        }

        return DB::transaction(function () use ($bulan, $admin) {
            $antrean = Pendaftaran::where('bulan_magang_id', $bulan->id)
                ->where('status', StatusPendaftaranEnum::WaitingList)
                ->lockForUpdate()
                ->get();

            $adminId = $admin?->id ?? User::where('role', \App\Enums\RoleEnum::SuperAdmin)->value('id') ?? 1;
            $catatanOtomatis = 'Periode pelaksanaan magang telah dimulai. Antrean waiting list ditutup secara otomatis (AC-04-7).';

            foreach ($antrean as $pendaftaran) {
                $pendaftaran->update([
                    'status' => StatusPendaftaranEnum::Ditolak,
                    'catatan' => $catatanOtomatis,
                    'verifikator_id' => $adminId,
                    'diputuskan_at' => now(),
                ]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'pendaftaran',
                    idData: $pendaftaran->id,
                    nilaiLama: ['status' => StatusPendaftaranEnum::WaitingList->value],
                    nilaiBaru: ['status' => StatusPendaftaranEnum::Ditolak->value],
                    detail: [
                        'aksi' => 'Penutupan antrean waiting list otomatis awal bulan magang (AC-04-7)',
                        'bulan_magang_id' => $bulan->id,
                        'admin_id' => $adminId,
                    ]
                );
            }

            return $antrean->count();
        });
    }
}

