<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\Jurusan;
use App\Support\Pengaturan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PanduanPdfController extends Controller
{
    /**
     * Generate dan stream dokumen Buku Panduan Resmi Magang Diskominfo Sleman (Task E).
     */
    public function download(): Response|BinaryFileResponse
    {
        // 0. Periksa apakah ada file PDF panduan yang diunggah oleh SuperAdmin via CMS
        $customPdf = Pengaturan::get('landing_panduan_pdf');
        if ($customPdf && Storage::disk('public')->exists($customPdf)) {
            $fileName = Pengaturan::get('landing_panduan_pdf_name', 'Buku-Panduan-Pendaftaran-Magang-Diskominfo-Sleman.pdf');
            $safeFileName = preg_replace('/[^a-zA-Z0-9_\-\. ]/', '_', basename($fileName));

            return response()->file(Storage::disk('public')->path($customPdf), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$safeFileName.'"',
            ]);
        }

        // 1. Ambil data bidang dan jurusan aktif untuk lampiran informasi formasi
        $bidangList = Bidang::with(['jurusan' => function ($q) {
            $q->where('aktif', true);
        }])->orderBy('nama_bidang', 'asc')->get();

        // 2. Baca base64 logo resmi Sleman
        $logoBase64Path = public_path('images/logo-sleman.base64.txt');
        $logoBase64 = '';
        if (file_exists($logoBase64Path)) {
            $logoBase64 = trim(file_get_contents($logoBase64Path));
        }

        // 3. Render dokumen PDF A4 Portrait
        $pdf = Pdf::loadView('pdf.buku-panduan-magang', [
            'bidangList' => $bidangList,
            'logoBase64' => $logoBase64,
            'nomorJuknis' => '500.12/JUKNIS-MAGKOM/'.date('Y'),
            'tahun' => date('Y'),
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Buku-Panduan-Pendaftaran-Magang-Diskominfo-Sleman.pdf');
    }
}
