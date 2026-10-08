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
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\SlotKhusus;
use App\Models\User;
use App\Support\PeriodeMagang;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

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
                    nilaiLama: ['status' => StatusPendaftaranEnum::Diajukan->value],
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
}
