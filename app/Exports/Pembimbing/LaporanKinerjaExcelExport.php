<?php

declare(strict_types=1);

namespace App\Exports\Pembimbing;

use App\Enums\StatusLogbookEnum;
use App\Enums\StatusTugasEnum;
use App\Models\Penempatan;
use App\Services\XlsxExportService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanKinerjaExcelExport
{
    public function __construct(
        protected XlsxExportService $xlsxExportService
    ) {}

    /**
     * 9 Kolom resmi yang sesuai dengan antarmuka web dan format Excel pembimbing sebelumnya.
     *
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
        'Logbook',
        'Status',
    ];

    /**
     * Warna header royal blue Gentelella khas laporan pembimbing (#0641CC).
     */
    public const HEADER_COLOR = 'FF0641CC';

    /**
     * Menghasilkan berkas .xlsx resmi valid OpenXML dengan format dan styling Sleman MagKom.
     *
     * @param  Collection<int, Penempatan>  $records
     */
    public function download(Collection $records, ?string $filename = null): BinaryFileResponse
    {
        $filename = $filename ?: 'Laporan_Peserta_Bimbingan_'.Carbon::now()->format('Ymd_His').'.xlsx';

        $rows = [];
        $no = 1;

        foreach ($records as $p) {
            $totalTugas = $p->tugas->count();
            $tugasSelesai = $p->tugas->where('status', StatusTugasEnum::Selesai)->count();
            $totalLogbook = $p->logbooks->count();
            $logbookReviewed = $p->logbooks->whereIn('status', [StatusLogbookEnum::Disetujui, StatusLogbookEnum::PerluRevisi])->count();
            $statusVal = is_object($p->status) ? $p->status->value : $p->status;

            $rows[] = [
                $no++,
                $p->peserta?->nama ?? '-',
                $p->peserta?->profil?->nim_nisn ?? '-',
                $p->peserta?->profil?->institusi ?? '-',
                $p->peserta?->profil?->jurusan?->nama_jurusan ?? '-',
                $p->bidang?->nama_bidang ?? 'Umum',
                "{$tugasSelesai} / {$totalTugas}",
                "{$logbookReviewed} / {$totalLogbook}",
                ucfirst((string) $statusVal),
            ];
        }

        return $this->xlsxExportService->downloadXlsx(
            filename: $filename,
            sheetTitle: 'Laporan Peserta',
            headers: self::HEADERS,
            rows: $rows,
            headerColor: self::HEADER_COLOR
        );
    }
}
