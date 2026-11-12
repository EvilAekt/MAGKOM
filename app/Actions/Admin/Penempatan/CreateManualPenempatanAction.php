<?php

declare(strict_types=1);

namespace App\Actions\Admin\Penempatan;

use App\Enums\JalurEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\SumberPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Models\Bidang;
use App\Models\Jurusan;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\SlotKhusus;
use App\Models\User;
use App\Services\AuditService;
use App\Services\QuotaAllocationService;
use App\Support\PeriodeMagang;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateManualPenempatanAction
{
    public function __construct(
        protected AuditService $auditService,
        protected QuotaAllocationService $quotaAllocationService
    ) {}

    /**
     * Penempatan manual jalur khusus untuk peserta yang sudah terdaftar (F-06, F-07).
     *
     * @throws \RuntimeException
     */
    public function executeTerdaftar(
        User $peserta,
        SlotKhusus $slotKhusus,
        User $pembimbing,
        ?Bidang $bidang,
        CarbonInterface|string $bulanMulai,
        int $durasiBulan,
        User $admin
    ): Penempatan {
        return DB::transaction(function () use (
            $peserta, $slotKhusus, $pembimbing, $bidang, $bulanMulai, $durasiBulan, $admin
        ) {
            // 1. Kunci baris parent User peserta untuk serialisasi alur penempatan
            $lockedPeserta = User::query()
                ->whereKey($peserta->id)
                ->lockForUpdate()
                ->firstOrFail();

            // 2. Mencegah penempatan ganda aktif untuk peserta yang sama setelah lock diperoleh
            $sudahAdaAktif = Penempatan::where('peserta_id', $lockedPeserta->id)
                ->where('status', StatusPenempatanEnum::Aktif)
                ->exists();

            if ($sudahAdaAktif) {
                throw new \RuntimeException('Peserta ini sudah memiliki data penempatan aktif.');
            }

            // 3. Kunci dan ambil slot khusus
            $slot = $this->quotaAllocationService->ambilKuotaKhusus($slotKhusus->id);

            // 4. Hitung periode magang (BR-08)
            [$mulai, $selesai] = PeriodeMagang::hitung($bulanMulai, $durasiBulan);

            // 5. Buat penempatan jalur khusus
            $penempatan = Penempatan::create([
                'pendaftaran_id' => null,
                'peserta_id' => $lockedPeserta->id,
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
                detail: ['aksi' => 'Membuat penempatan manual jalur khusus'],
                user: $admin->id
            );

            return $penempatan;
        });
    }

    /**
     * Penempatan manual dengan pendaftaran peserta baru langsung (AC-07-3, F-07).
     * Membuat akun user role peserta (email terverifikasi, must_change_password = true), profil, dan penempatan.
     *
     * @param  array<string, mixed>  $userData
     * @param  array<string, mixed>  $profilData
     */
    public function executeBaru(
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
        return DB::transaction(function () use (
            $userData, $profilData, $pembimbing, $bidang, $bulanMulai, $durasiBulan, $jalur, $slotKhususId, $kuotaId, $admin
        ) {
            // 1. Buat user role peserta (AC-07-3: email terverifikasi, must_change_password aktif)
            // SEC-01: Shared default password elimination for production & unique credential security
            $defaultFallback = app()->environment('testing') ? 'Magkom2026!' : ('Mgk-'.Str::random(10).'!#');
            $plainPassword = ! empty($userData['password']) ? (string) $userData['password'] : $defaultFallback;
            $peserta = new User;
            $peserta->nama = (string) $userData['nama'];
            $peserta->email = (string) $userData['email'];
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
                    'aksi' => 'Membuat akun dan penempatan manual peserta baru',
                    'user_id' => $peserta->id,
                    'email' => $peserta->email,
                    'jalur' => $jalurEnum->value,
                ],
                user: $admin->id
            );

            $penempatan->setRelation('peserta', $peserta);
            $penempatan->plain_password = $plainPassword;

            return $penempatan;
        });
    }
}
