<?php

declare(strict_types=1);

namespace App\Actions\Admin\Penempatan;

use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Models\Penempatan;
use App\Models\Pengaturan as PengaturanModel;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;

class BulkReassignPembimbingAction
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Memindahkan pembimbing peserta magang secara massal (bulk reassign) secara atomik (AC-07-4, BR-13, A-12).
     *
     * @param  array<int, int|string>  $ids
     *
     * @throws \RuntimeException
     */
    public function execute(array $ids, User $pembimbingBaru, User $admin): int
    {
        return DB::transaction(function () use ($ids, $pembimbingBaru, $admin) {
            // 1. Kunci User pembimbing tujuan terlebih dahulu (standar urutan locking global)
            $pembimbing = User::query()
                ->whereKey($pembimbingBaru->id)
                ->where('role', RoleEnum::Pembimbing)
                ->where('status_aktif', true)
                ->lockForUpdate()
                ->firstOrFail();

            // Sanitasi dan urutkan ID ascending untuk konsistensi locking multi-row
            $sanitizedIds = array_values(array_unique(array_filter(array_map('intval', $ids))));
            sort($sanitizedIds);

            if (empty($sanitizedIds)) {
                return 0;
            }

            // 2. Kunci seluruh data penempatan aktif dengan urutan ID ascending (mencegah deadlock AB-BA)
            $penempatans = Penempatan::query()
                ->whereIn('id', $sanitizedIds)
                ->where('status', StatusPenempatanEnum::Aktif)
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->get();

            $maxBebanSetting = PengaturanModel::where('kunci', 'maks_peserta_aktif_per_pembimbing')->value('nilai');
            if ($maxBebanSetting !== null && $maxBebanSetting !== '') {
                $maxBeban = (int) $maxBebanSetting;
                if ($maxBeban > 0) {
                    $currentAktif = Penempatan::where('pembimbing_id', $pembimbing->id)
                        ->where('status', StatusPenempatanEnum::Aktif)
                        ->count();

                    $penambahanAktif = $penempatans->filter(fn ($p) => (int) $p->pembimbing_id !== (int) $pembimbing->id)->count();

                    if (($currentAktif + $penambahanAktif) > $maxBeban) {
                        throw new \RuntimeException("Kapasitas pembimbing {$pembimbing->nama} terlampaui (Batas maksimal: {$maxBeban} mentee aktif, saat ini: {$currentAktif}, penambahan: {$penambahanAktif}).");
                    }
                }
            }

            $updated = 0;
            foreach ($penempatans as $p) {
                if ((int) $p->pembimbing_id === (int) $pembimbing->id) {
                    continue;
                }

                $pembimbingLamaId = $p->pembimbing_id;
                $p->update(['pembimbing_id' => $pembimbing->id]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'penempatan',
                    idData: $p->id,
                    nilaiLama: ['pembimbing_id' => $pembimbingLamaId],
                    nilaiBaru: ['pembimbing_id' => $pembimbing->id],
                    detail: [
                        'aksi' => 'Pemindahan pembimbing massal (bulk reassign)',
                        'admin_id' => $admin->id,
                        'pembimbing_baru_id' => $pembimbing->id,
                    ],
                    user: $admin->id
                );

                $updated++;
            }

            return $updated;
        });
    }
}
