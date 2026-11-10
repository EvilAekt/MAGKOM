<?php

declare(strict_types=1);

namespace App\Exports\Admin\Penempatan;

use App\Enums\JalurEnum;
use App\Enums\TipeAuditEnum;
use App\Models\Penempatan;
use App\Models\User;
use App\Services\AuditService;
use App\Services\XlsxExportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RekapSuratDiterimaExport
{
    public function __construct(
        protected AuditService $auditService,
        protected XlsxExportService $xlsxExportService
    ) {}

    /**
     * Mengekspor rekapitulasi data surat keterangan diterima dalam format XLSX, CSV, atau PDF.
     *
     * @param  Collection<int, Penempatan>  $records
     */
    public function export(Collection $records, Request $request, User $actor): Response
    {
        $format = strtolower((string) $request->input('format', 'csv'));

        // 1. Catat jejak audit akses data ekspor
        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'penempatan',
            idData: null,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => match ($format) {
                    'xlsx' => 'Ekspor rekapitulasi surat diterima magang XLSX (F-16)',
                    'pdf' => 'Ekspor rekapitulasi surat diterima magang PDF (F-16)',
                    default => 'Ekspor rekapitulasi surat diterima magang CSV (F-16)',
                },
                'format' => $format,
                'jumlah_baris' => $records->count(),
                'filter' => $request->only(['status', 'pembimbing_id', 'bidang_id', 'bulan_magang_id', 'q']),
            ],
            user: $actor->id
        );

        $headers = [
            'No Penempatan',
            'Nama Peserta',
            'NIM/NISN',
            'Institusi/Kampus',
            'Jurusan',
            'Bidang Penempatan',
            'Pembimbing Lapangan',
            'Jalur',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Status',
            'No Surat Diterima',
        ];

        // Format PDF
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('pdf.rekap-penempatan', [
                'records' => $records,
                'generatedAt' => now(),
                'filter' => $request->only(['status', 'pembimbing_id', 'bidang_id', 'bulan_magang_id', 'q']),
            ])->setPaper('a4', 'landscape');

            return $pdf->download('rekap-surat-diterima-'.date('Ymd-His').'.pdf');
        }

        // Format XLSX
        if ($format === 'xlsx') {
            $rows = [];
            foreach ($records as $item) {
                $jurusan = $item->kuota?->jurusan?->nama_jurusan
                    ?? ($item->peserta?->profil?->jurusan?->nama_jurusan ?? '-');
                $jalur = strtoupper($item->jalur instanceof JalurEnum ? $item->jalur->value : (string) ($item->jalur ?? 'REGULER'));
                $noSurat = $item->no_surat ?? '500.12/'.str_pad((string) $item->id, 4, '0', STR_PAD_LEFT).'/DISKOMINFO/'.date('Y');

                $rows[] = [
                    $item->id,
                    $item->peserta?->nama ?? '-',
                    $item->peserta?->profil?->nim_nisn ?? '-',
                    $item->peserta?->profil?->institusi ?? '-',
                    $jurusan,
                    $item->bidang?->nama_bidang ?? '-',
                    $item->pembimbing?->nama ?? '-',
                    $jalur,
                    $item->tgl_mulai?->format('d/m/Y') ?? '-',
                    $item->tgl_selesai?->format('d/m/Y') ?? '-',
                    ucfirst($item->status?->value ?? '-'),
                    $noSurat,
                ];
            }

            return $this->xlsxExportService->downloadXlsx(
                'rekap-surat-diterima-'.date('Ymd-His').'.xlsx',
                'Rekap Penempatan & Surat',
                $headers,
                $rows
            );
        }

        // Format CSV (Streaming dengan UTF-8 BOM dan proteksi formula injection)
        $sanitize = function (mixed $val): string {
            if ($val === null) {
                return '';
            }
            $s = (string) $val;
            if ($s !== '' && in_array($s[0], ['=', '+', '-', '@'], true)) {
                return "'".$s;
            }

            return $s;
        };

        $fileName = 'rekap-surat-diterima-'.date('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($records, $sanitize, $headers) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBFsep=,\r\n");
            fputcsv($handle, $headers);

            foreach ($records as $item) {
                $jurusan = $item->kuota?->jurusan?->nama_jurusan
                    ?? ($item->peserta?->profil?->jurusan?->nama_jurusan ?? '-');
                $jalur = strtoupper($item->jalur instanceof JalurEnum ? $item->jalur->value : (string) ($item->jalur ?? 'REGULER'));
                $noSurat = $item->no_surat ?? '500.12/'.str_pad((string) $item->id, 4, '0', STR_PAD_LEFT).'/DISKOMINFO/'.date('Y');

                fputcsv($handle, [
                    $sanitize($item->id),
                    $sanitize($item->peserta?->nama ?? '-'),
                    $sanitize($item->peserta?->profil?->nim_nisn ?? '-'),
                    $sanitize($item->peserta?->profil?->institusi ?? '-'),
                    $sanitize($jurusan),
                    $sanitize($item->bidang?->nama_bidang ?? '-'),
                    $sanitize($item->pembimbing?->nama ?? '-'),
                    $sanitize($jalur),
                    $sanitize($item->tgl_mulai?->format('d/m/Y') ?? '-'),
                    $sanitize($item->tgl_selesai?->format('d/m/Y') ?? '-'),
                    $sanitize(ucfirst($item->status?->value ?? '-')),
                    $sanitize($noSurat),
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
