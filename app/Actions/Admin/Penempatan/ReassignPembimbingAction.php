<?php

declare(strict_types=1);

namespace App\Actions\Admin\Penempatan;

use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Models\Penempatan;
use App\Models\User;
use App\Services\AuditService;
use App\Support\Pengaturan;
use Illuminate\Support\Facades\DB;

class ReassignPembimbingAction
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Memindahkan pembimbing peserta magang aktif (AC-07-4, BR-13, A-12).
     *
     * @throws \RuntimeException
     */
    public function execute(Penempatan $penempatan, User $pembimbingBaru, User $admin): Penempatan
    {
        return DB::transaction(function () use ($penempatan, $pembimbingBaru, $admin) {
            // 1. Kunci User pembimbing tujuan terlebih dahulu (standar urutan locking global)
            $pembimbing = User::query()
                ->whereKey($pembimbingBaru->id)
                ->where('role', RoleEnum::Pembimbing)
                ->where('status_aktif', true)
                ->lockForUpdate()
                ->firstOrFail();

            // 2. Kunci baris Penempatan yang diproses
            $p = Penempatan::query()
                ->whereKey($penempatan->id)
                ->lockForUpdate()
                ->firstOrFail();

            // 3. Validasi ulang status dan kewenangan
            if ($p->status !== StatusPenempatanEnum::Aktif) {
                throw new \RuntimeException('Pembimbing hanya dapat dipindahkan untuk penempatan yang berstatus aktif.');
            }

            if ((int) $p->pembimbing_id === (int) $pembimbing->id) {
                return $p;
            }

            // 4. Cek batas beban pembimbing (BR-13)
            $batasBeban = Pengaturan::int('maks_peserta_aktif_per_pembimbing', 0);
            if ($batasBeban > 0) {
                $bebanAktif = Penempatan::where('pembimbing_id', $pembimbing->id)
                    ->where('status', StatusPenempatanEnum::Aktif)
                    ->count();

                if ($bebanAktif >= $batasBeban) {
                    throw new \RuntimeException("Pembimbing {$pembimbing->nama} telah mencapai batas beban maksimum ({$batasBeban} peserta aktif).");
                }
            }

            // 5. Jalankan mutasi
            $pembimbingLamaId = $p->pembimbing_id;
            $p->update(['pembimbing_id' => $pembimbing->id]);

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'penempatan',
                idData: $p->id,
                nilaiLama: ['pembimbing_id' => $pembimbingLamaId],
                nilaiBaru: ['pembimbing_id' => $pembimbing->id],
                detail: [
                    'aksi' => 'Memindahkan pembimbing peserta magang',
                    'pembimbing_lama_id' => $pembimbingLamaId,
                    'pembimbing_baru_id' => $pembimbing->id,
                    'admin_id' => $admin->id,
                ],
                user: $admin->id
            );

            return $p;
        });
    }
}
