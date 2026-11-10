<?php

declare(strict_types=1);

namespace App\Actions\Admin\Kuota;

use App\Enums\TipeAuditEnum;
use App\Models\Bidang;
use App\Models\Kuota;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;

class UpdateBidangKuotaAction
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Memperbarui kuota bidang secara transaksional dengan row-level lock dan penegakan BR-12.
     *
     * @param  array<int|string, int|string>  $alokasi
     * @return array{success: bool, message: string}
     */
    public function execute(Bidang $bidang, int $bulanMagangId, array $alokasi, string $statusBidang = 'buka'): array
    {
        return DB::transaction(function () use ($bidang, $bulanMagangId, $alokasi, $statusBidang) {
            // Jika admin memilih status tutup bidang, seluruh kuota jurusan dinolkan (kecuali yang sudah terisi peserta)
            if ($statusBidang === 'tutup') {
                foreach ($bidang->jurusan as $j) {
                    $kuota = Kuota::where('bulan_magang_id', $bulanMagangId)
                        ->where('jurusan_id', $j->id)
                        ->lockForUpdate()
                        ->first();

                    if (! $kuota) {
                        $kuota = new Kuota([
                            'bulan_magang_id' => $bulanMagangId,
                            'jurusan_id' => $j->id,
                        ]);
                    }

                    // Pertahankan sejumlah terisi jika ada peserta aktif agar tidak merusak relasi (BR-12)
                    $kuota->jumlah = $kuota->exists ? $kuota->terisi : 0;
                    $kuota->aktif = ($kuota->jumlah > 0);
                    $kuota->save();
                }

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'kuota',
                    idData: $bidang->id,
                    nilaiBaru: ['status_bidang' => 'tutup', 'alokasi' => []],
                    detail: ['aksi' => "Menutup kuota penempatan bidang {$bidang->nama_bidang}"]
                );

                return [
                    'success' => true,
                    'message' => "Kuota penempatan bidang {$bidang->nama_bidang} berhasil ditutup.",
                ];
            }

            // Jika status buka, proses alokasi dan hitung total kapasitas
            $totalAlokasi = 0;
            foreach ($alokasi as $jurusanId => $jumlah) {
                $jumlah = max(0, (int) $jumlah);
                $totalAlokasi += $jumlah;

                $kuota = Kuota::where('bulan_magang_id', $bulanMagangId)
                    ->where('jurusan_id', (int) $jurusanId)
                    ->lockForUpdate()
                    ->first();

                if (! $kuota) {
                    $kuota = new Kuota([
                        'bulan_magang_id' => $bulanMagangId,
                        'jurusan_id' => (int) $jurusanId,
                    ]);
                }

                // Cegah penurunan di bawah peserta yang sudah diterima (BR-12)
                if ($kuota->exists && $jumlah < $kuota->terisi) {
                    return [
                        'success' => false,
                        'message' => "Jumlah kuota untuk kejuruan tidak boleh lebih kecil dari peserta yang sudah diterima ({$kuota->terisi}).",
                    ];
                }

                $kuota->jumlah = $jumlah;
                $kuota->aktif = ($jumlah > 0);
                $kuota->save();
            }

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'kuota',
                idData: $bidang->id,
                nilaiBaru: ['status_bidang' => 'buka', 'alokasi' => $alokasi, 'total_kapasitas' => $totalAlokasi],
                detail: ['aksi' => "Memperbarui kuota bidang {$bidang->nama_bidang}"]
            );

            return [
                'success' => true,
                'message' => "Alokasi kuota bidang {$bidang->nama_bidang} berhasil diperbarui (Total Kapasitas: {$totalAlokasi} peserta).",
            ];
        });
    }
}
