<?php

declare(strict_types=1);

namespace App\Exports\Pembimbing;

use App\Enums\StatusLogbookEnum;
use App\Enums\StatusTugasEnum;
use App\Models\Penempatan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanKinerjaCsvExport
{
    /**
     * @var array<int, string>
     */
    public const HEADERS = [
        'No',
        'Nama Mentee',
        'NIM/NISN',
        'Institusi',
        'Jurusan',
        'Bidang Magang',
        'Tugas Selesai',
        'Total Tugas',
        'Logbook Direview',
        'Total Logbook',
        'Status',
    ];

    /**
     * Menghasilkan StreamedResponse unduhan CSV ber-BOM UTF-8.
     *
     * @param  Collection<int, Penempatan>  $records
     */
    public function download(Collection $records, ?string $filename = null): StreamedResponse
    {
        $filename = $filename ?: 'Laporan_Peserta_Bimbingan_'.Carbon::now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($records) {
            $handle = fopen('php://output', 'w');
            if ($handle === false) {
                return;
            }

            // UTF-8 BOM agar terbaca sempurna di Microsoft Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, self::HEADERS);

            $no = 1;
            foreach ($records as $p) {
                $totalTugas = $p->tugas->count();
                $tugasSelesai = $p->tugas->where('status', StatusTugasEnum::Selesai)->count();
                $totalLogbook = $p->logbooks->count();
                $logbookReviewed = $p->logbooks->whereIn('status', [StatusLogbookEnum::Disetujui, StatusLogbookEnum::PerluRevisi])->count();
                $statusVal = is_object($p->status) ? $p->status->value : $p->status;

                fputcsv($handle, [
                    $no++,
                    $p->peserta?->nama ?? '-',
                    $p->peserta?->profil?->nim_nisn ?? '-',
                    $p->peserta?->profil?->institusi ?? '-',
                    $p->peserta?->profil?->jurusan?->nama_jurusan ?? '-',
                    $p->bidang?->nama_bidang ?? 'Umum',
                    $tugasSelesai,
                    $totalTugas,
                    $logbookReviewed,
                    $totalLogbook,
                    ucfirst((string) $statusVal),
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
