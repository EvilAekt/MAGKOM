<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Enums\StatusPenilaianEnum;
use App\Http\Controllers\Controller;
use App\Models\Penempatan;
use App\Models\Penilaian;
use App\Services\DocumentVerificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class NilaiController extends Controller
{
    /**
     * Melihat lembar evaluasi nilai magang saya.
     * Hanya dapat diakses bila nilai telah berstatus 'final'.
     */
    public function show(Request $request): mixed
    {
        $user = $request->user();

        $penempatan = Penempatan::with(['penilaian.details.rubrik', 'pembimbing', 'bidang'])
            ->where('peserta_id', $user->id)
            ->latest()
            ->first();

        $penilaian = $penempatan?->penilaian;
        $isFinal = $penilaian && $penilaian->status === StatusPenilaianEnum::Final;

        if ($request->expectsJson() || $request->wantsJson()) {
            if (! $penempatan) {
                abort(404, 'Penempatan magang tidak ditemukan.');
            }
            Gate::authorize('view', [Penilaian::class, $penempatan]);

            return response()->json([
                'success' => true,
                'data' => [
                    'penempatan' => $penempatan,
                    'penilaian' => $penilaian,
                ],
            ]);
        }

        return view('peserta.nilai.show', [
            'penempatan' => $penempatan,
            'penilaian' => $penilaian,
            'isFinal' => $isFinal,
        ]);
    }

    /**
     * Mengunduh lembar nilai resmi berformat PDF (AC-12).
     */
    public function unduhPdf(Request $request): Response
    {
        $user = $request->user();

        $penempatan = Penempatan::with(['peserta.profil', 'pembimbing', 'bidang', 'penilaian.details.rubrik'])
            ->where('peserta_id', $user->id)
            ->latest()
            ->firstOrFail();

        Gate::authorize('downloadPdf', [Penilaian::class, $penempatan]);

        $penilaian = $penempatan->penilaian;

        $verificationService = app(DocumentVerificationService::class);
        $nomorNilai = '500.12/NILAI/'.str_pad((string) $penempatan->id, 4, '0', STR_PAD_LEFT).'/DISKOMINFO/'.($penempatan->tgl_selesai?->format('Y') ?? date('Y'));
        $dokumenVerifikasi = $verificationService->getOrCreateVerification(
            $penempatan,
            'lembar_nilai',
            $nomorNilai
        );
        $qrCodeSvg = $verificationService->generateQrCodeSvg(
            $verificationService->getVerificationUrl($dokumenVerifikasi->kode_unik),
            75
        );

        $pdf = Pdf::loadView('pdf.lembar-nilai', [
            'penempatan' => $penempatan,
            'peserta' => $penempatan->peserta,
            'profil' => $penempatan->peserta->profil,
            'pembimbing' => $penempatan->pembimbing,
            'penilaian' => $penilaian,
            'dokumenVerifikasi' => $dokumenVerifikasi,
            'qrCodeSvg' => $qrCodeSvg,
        ])->setPaper('a4', 'portrait');

        $namaPeserta = $user->nama ?? ($user->name ?? 'peserta');
        $namaFile = 'lembar-nilai-'.str_replace(' ', '-', strtolower($namaPeserta)).'.pdf';

        return $pdf->download($namaFile);
    }
}
