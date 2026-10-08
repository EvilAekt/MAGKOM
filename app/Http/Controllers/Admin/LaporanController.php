<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExportReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function __construct(
        protected ExportReportService $exportService
    ) {}

    /**
     * Ekspor rekapitulasi data pendaftaran peserta (AC-17-1, F-17).
     */
    public function pendaftaran(Request $request): StreamedResponse
    {
        return $this->exportService->exportPendaftaran(
            $request->user(),
            $request->only(['bulan_magang_id', 'status'])
        );
    }

    /**
     * Ekspor rekapitulasi data penempatan peserta (AC-17-1, F-17).
     */
    public function penempatan(Request $request): StreamedResponse
    {
        return $this->exportService->exportPenempatan(
            $request->user(),
            $request->only(['status', 'jalur'])
        );
    }

    /**
     * Ekspor realisasi kuota umum vs slot khusus (AC-17-1, F-17).
     */
    public function kuota(Request $request): StreamedResponse
    {
        return $this->exportService->exportRealisasiKuota($request->user());
    }

    /**
     * Ekspor rekapitulasi nilai peserta (Hanya SuperAdmin, AC-17-1, A-20).
     */
    public function nilai(Request $request): StreamedResponse
    {
        return $this->exportService->exportNilai($request->user());
    }
}
