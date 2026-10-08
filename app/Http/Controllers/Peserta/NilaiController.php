<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Penempatan;
use App\Models\Penilaian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class NilaiController extends Controller
{
    /**
     * Melihat lembar evaluasi nilai magang saya.
     * Hanya dapat diakses bila nilai telah berstatus 'final'.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $penempatan = Penempatan::with(['penilaian.details.rubrik', 'pembimbing', 'bidang'])
            ->where('peserta_id', $user->id)
            ->latest()
            ->firstOrFail();

        Gate::authorize('view', [Penilaian::class, $penempatan]);

        return response()->json([
            'success' => true,
            'data' => [
                'penempatan' => $penempatan,
                'penilaian' => $penempatan->penilaian,
            ],
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

        $pdf = Pdf::loadView('pdf.lembar-nilai', [
            'penempatan' => $penempatan,
            'peserta' => $penempatan->peserta,
            'profil' => $penempatan->peserta->profil,
            'pembimbing' => $penempatan->pembimbing,
            'penilaian' => $penilaian,
        ])->setPaper('a4', 'portrait');

        $namaPeserta = $user->nama ?? ($user->name ?? 'peserta');
        $namaFile = 'lembar-nilai-' . str_replace(' ', '-', strtolower($namaPeserta)) . '.pdf';

        return $pdf->download($namaFile);
    }
}
