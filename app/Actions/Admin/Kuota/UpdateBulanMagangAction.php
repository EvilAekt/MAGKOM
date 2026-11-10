<?php

declare(strict_types=1);

namespace App\Actions\Admin\Kuota;

use App\Enums\TipeAuditEnum;
use App\Models\BulanMagang;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;

class UpdateBulanMagangAction
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Memperbarui jadwal bulan magang aktif secara transaksional dengan audit trail.
     *
     * @param  array<string, mixed>  $validated
     */
    public function execute(BulanMagang $bulanMagang, array $validated, ?string $statusAwal = null): BulanMagang
    {
        return DB::transaction(function () use ($bulanMagang, $validated, $statusAwal) {
            /** @var BulanMagang $locked */
            $locked = BulanMagang::whereKey($bulanMagang->id)->lockForUpdate()->firstOrFail();

            if ($statusAwal) {
                $validated['aktif'] = ($statusAwal === 'dibuka');
            }

            $nilaiLama = $locked->toArray();
            $locked->update($validated);

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'bulan_magang',
                idData: $locked->id,
                nilaiLama: $nilaiLama,
                nilaiBaru: $locked->toArray(),
                detail: ['aksi' => 'Memperbarui jadwal bulan magang dari kontrol kuota']
            );

            return $locked;
        });
    }
}
