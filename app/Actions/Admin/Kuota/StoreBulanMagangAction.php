<?php

declare(strict_types=1);

namespace App\Actions\Admin\Kuota;

use App\Enums\TipeAuditEnum;
use App\Models\BulanMagang;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;

class StoreBulanMagangAction
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Membuat bulan magang baru secara transaksional dengan audit trail.
     *
     * @param  array<string, mixed>  $validated
     */
    public function execute(array $validated, string $statusAwal = 'draft'): BulanMagang
    {
        return DB::transaction(function () use ($validated, $statusAwal) {
            $isAktif = ($statusAwal === 'dibuka');

            $bulan = BulanMagang::create([
                'nama_bulan' => $validated['nama_bulan'] ?? null,
                'bulan_mulai' => $validated['bulan_mulai'],
                'tgl_buka' => $validated['tgl_buka'],
                'tgl_tutup' => $validated['tgl_tutup'],
                'aktif' => $isAktif,
            ]);

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'bulan_magang',
                idData: $bulan->id,
                nilaiBaru: $bulan->toArray(),
                detail: ['aksi' => 'Membuat bulan magang baru dari kontrol kuota']
            );

            return $bulan;
        });
    }
}

