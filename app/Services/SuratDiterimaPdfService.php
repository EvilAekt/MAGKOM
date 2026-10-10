<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TipeAuditEnum;
use App\Models\Penempatan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Response;

class SuratDiterimaPdfService
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menghasilkan dokumen PDF Surat Keterangan Diterima Magang secara on-the-fly untuk diunduh (AC-16-1, F-16).
     */
    public function generate(Penempatan $penempatan): Response
    {
        [$pdf, $filename] = $this->renderPdf($penempatan, 'unduh');

        return $pdf->download($filename);
    }

    /**
     * Menyajikan dokumen PDF Surat Keterangan Diterima Magang secara inline stream untuk pratinjau browser/modal (AC-16-1, F-16).
     */
    public function stream(Penempatan $penempatan): Response
    {
        [$pdf, $filename] = $this->renderPdf($penempatan, 'pratinjau');

        return $pdf->stream($filename);
    }

    /**
     * Menyiapkan objek PDF dan mencatat audit log.
     *
     * @return array{\Barryvdh\DomPDF\PDF, string}
     */
    protected function renderPdf(Penempatan $penempatan, string $mode): array
    {
        $penempatan->loadMissing(['peserta.profil.jurusan', 'pembimbing', 'bidang', 'kuota.jurusan']);

        $peserta = $penempatan->peserta;
        $tahun = Carbon::parse($penempatan->tgl_mulai)->format('Y');
        $bulanRomawi = $this->keRomawi((int) Carbon::parse($penempatan->tgl_mulai)->format('m'));
        $nomorSurat = sprintf('%03d/KOMINFO/MAGKOM/%s/%s', $penempatan->id, $bulanRomawi, $tahun);
        $tglTerbit = Carbon::today('Asia/Jakarta')->translatedFormat('d F Y');

        $pdf = Pdf::loadView('pdf.surat-keterangan-diterima', [
            'penempatan' => $penempatan,
            'peserta' => $peserta,
            'nomorSurat' => $nomorSurat,
            'tglTerbit' => $tglTerbit,
        ])->setPaper('a4', 'portrait');

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'penempatan',
            idData: $penempatan->id,
            nilaiLama: null,
            nilaiBaru: [
                'nomor_surat' => $nomorSurat,
                'tgl_terbit' => $tglTerbit,
            ],
            detail: [
                'aksi' => $mode === 'pratinjau' ? 'pratinjau_surat_keterangan_diterima_pdf' : 'unduh_surat_keterangan_diterima_pdf',
                'peserta_id' => $peserta->id,
            ]
        );

        $safeNama = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($peserta->nama));
        $filename = "surat-keterangan-diterima-{$safeNama}.pdf";

        return [$pdf, $filename];
    }

    /**
     * Konversi angka bulan ke format Romawi.
     */
    protected function keRomawi(int $bulan): string
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        return $map[$bulan] ?? 'I';
    }
}
