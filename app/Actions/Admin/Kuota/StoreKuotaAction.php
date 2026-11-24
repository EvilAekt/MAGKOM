<?php

declare(strict_types=1);

namespace App\Actions\Admin\Kuota;

use App\Enums\TipeAuditEnum;
use App\Models\Kuota;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StoreKuotaAction
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menyimpan alokasi kuota jurusan pada bulan magang tertentu secara transaksional dengan audit trail dan penegakan BR-12.
     *
     * @param  array<int, int>  $jurusanIds
     * @return array<int, int> Daftar ID kuota yang dibuat/diperbarui
     *
     * @throws ValidationException
     */
    public function execute(int $bulanMagangId, int $jumlah, array $jurusanIds): array
    {
        return DB::transaction(function () use ($bulanMagangId, $jumlah, $jurusanIds) {
            $createdKuotas = [];

            foreach ($jurusanIds as $jId) {
                // Kunci baris jika sudah ada
                $existing = Kuota::where('bulan_magang_id', $bulanMagangId)
                    ->where('jurusan_id', $jId)
                    ->with('jurusan')
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    // Penegakan BR-12: kuota tidak boleh diturunkan di bawah jumlah peserta yang sudah diterima
                    if ($jumlah < $existing->terisi) {
                        $jurusanNama = $existing->jurusan?->nama_jurusan ?? "ID #{$jId}";
                        throw ValidationException::withMessages([
                            'jumlah' => "Jumlah kuota untuk jurusan {$jurusanNama} ({$jumlah}) tidak boleh lebih kecil dari peserta yang sudah diterima ({$existing->terisi}).",
                        ]);
                    }

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
