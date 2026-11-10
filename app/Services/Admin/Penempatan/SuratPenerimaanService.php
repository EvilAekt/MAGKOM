<?php

declare(strict_types=1);

namespace App\Services\Admin\Penempatan;

use App\Models\Penempatan;
use App\Services\SuratDiterimaPdfService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SuratPenerimaanService
{
    /**
     * Mengalirkan dokumen resmi Surat Keterangan Diterima Magang untuk diunduh.
     */
    public function downloadResponse(Penempatan $penempatan, SuratDiterimaPdfService $pdfService): Response
    {
        $filePath = $penempatan->file_surat_diterima ?: $penempatan->pendaftaran?->file_surat_diterima;

        if ($filePath && Storage::disk('local')->exists($filePath)) {
            $pesertaNama = Str::slug($penempatan->peserta?->nama ?? 'peserta');

            return Storage::disk('local')->download($filePath, "surat-keterangan-diterima-{$pesertaNama}.pdf");
        }

        return $pdfService->generate($penempatan);
    }

    /**
     * Menyajikan pratinjau dokumen resmi Surat Keterangan Diterima Magang secara inline stream di peramban.
     */
    public function streamResponse(Penempatan $penempatan, SuratDiterimaPdfService $pdfService): Response
    {
        $filePath = $penempatan->file_surat_diterima ?: $penempatan->pendaftaran?->file_surat_diterima;

        if ($filePath && Storage::disk('local')->exists($filePath)) {
            return response()->file(Storage::disk('local')->path($filePath), [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="surat-keterangan-diterima.pdf"',
            ]);
        }

        return $pdfService->stream($penempatan);
    }
}
