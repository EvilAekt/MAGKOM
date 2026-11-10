<?php

declare(strict_types=1);

namespace App\Actions\Admin\Kuota;

use App\Enums\TipeAuditEnum;
use App\Models\Kuota;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;

class StoreKuotaAction
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menyimpan alokasi kuota jurusan pada bulan magang tertentu secara transaksional dengan audit trail.
     *
     * @param  array<int, int>  $jurusanIds
     * @return array<int, int> Daftar ID kuota yang dibuat/diperbarui
     */
    public function execute(int $bulanMagangId, int $jumlah, array $jurusanIds): array
    {
        return DB::transaction(function () use ($bulanMagangId, $jumlah, $jurusanIds) {
            $createdKuotas = [];

            foreach ($jurusanIds as $jId) {
                // Kunci baris jika sudah ada
                $existing = Kuota::where('bulan_magang_id', $bulanMagangId)
                    ->where('jurusan_id', $jId)
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    $existing->jumlah = $jumlah;
                    $existing->aktif = true;
                    $existing->save();
                    $createdKuotas[] = $existing->id;
                } else {
                    $kuota = Kuota::create([
                        'bulan_magang_id' => $bulanMagangId,
                        'jurusan_id' => $jId,
                        'jumlah' => $jumlah,
                        'terisi' => 0,
                        'aktif' => true,
                    ]);
                    $createdKuotas[] = $kuota->id;
                }
            }

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'kuota',
                idData: $createdKuotas[0] ?? $bulanMagangId,
                nilaiBaru: ['kuota_ids' => $createdKuotas, 'jumlah' => $jumlah],
                detail: ['aksi' => 'Menambahkan kuota jurusan pada bulan magang']
            );

            return $createdKuotas;
        });
    }
}

