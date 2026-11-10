<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Enums\TipeAuditEnum;
use App\Exports\Pembimbing\LaporanKinerjaCsvExport;
use App\Exports\Pembimbing\LaporanKinerjaExcelExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pembimbing\LaporanKinerjaFilterRequest;
use App\Queries\Pembimbing\LaporanKinerjaQuery;
use App\Services\AuditService;
use App\Services\Pembimbing\LaporanKinerjaService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanKinerjaController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
        protected LaporanKinerjaQuery $laporanQuery,
        protected LaporanKinerjaService $laporanService,
        protected LaporanKinerjaCsvExport $csvExport,
        protected LaporanKinerjaExcelExport $excelExport
    ) {}

    /**
     * Menampilkan rekapitulasi portofolio bimbingan dan kinerja mentoring ASN (PRD F-09, F-10).
     */
    public function index(LaporanKinerjaFilterRequest $request): View
    {
        $user = $request->user();
        $data = $this->laporanService->getLaporanData($request, $user);

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'penempatan',
            idData: null,
            nilaiLama: null,
            nilaiBaru: null,
            detail: ['aksi' => 'lihat_laporan_kinerja_pembimbing', 'total_mentee' => $data['totalMentee']]
        );

        return view('pembimbing.laporan-kinerja.index', $data);
    }

    /**
     * Ekspor rekapitulasi laporan peserta bimbingan (Excel .xlsx resmi, CSV .csv, atau redirect PDF).
     */
    public function export(LaporanKinerjaFilterRequest $request): BinaryFileResponse|StreamedResponse|RedirectResponse
    {
        $user = $request->user();
        $format = strtolower((string) $request->input('format', 'xlsx'));

        if ($format === 'pdf') {
            return redirect()->route('pembimbing.laporan-kinerja.cetak', $request->all());
        }

        $records = $this->laporanQuery->build($request, $user)->latest('tgl_mulai')->get();

        $this->auditService->catat(
            tipe: TipeAuditEnum::AksesData,
            tabel: 'penempatan',
            idData: null,
            nilaiLama: null,
            nilaiBaru: null,
            detail: [
                'aksi' => match ($format) {
                    'xlsx' => 'Ekspor rekapitulasi laporan peserta XLSX',
                    default => 'Ekspor rekapitulasi laporan peserta CSV',
                },
                'format' => $format,
                'jumlah_baris' => $records->count(),
            ]
        );

        $filename = 'Laporan_Peserta_Bimbingan_'.Carbon::now()->format('Ymd_His');

        if ($format === 'csv') {
            return $this->csvExport->download($records, $filename.'.csv');
        }

        // Berkas .xlsx resmi valid OpenXML spreadsheet
        return $this->excelExport->download($records, $filename.'.xlsx');
    }

    /**
     * Tampilan cetak resmi LPJ mentoring ASN ramah printer (Print / PDF).
     */
    public function cetak(LaporanKinerjaFilterRequest $request): View
    {
        $user = $request->user();
        $allPenempatans = $this->laporanQuery->build($request, $user)->latest('tgl_mulai')->get();

        return view('pembimbing.laporan-kinerja.cetak', [
            'allPenempatans' => $allPenempatans,
            'pembimbingUser' => $user,
            'tanggalCetak' => Carbon::now()->translatedFormat('d F Y'),
        ]);
    }
}
