<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Enums\StatusPenilaianEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\PenilaianRequest;
use App\Models\Penempatan;
use App\Models\Penilaian;
use App\Models\Rubrik;
use App\Services\MasaTenggangService;
use App\Services\ScoringService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PenilaianController extends Controller
{
    public function __construct(
        protected ScoringService $scoringService,
        protected MasaTenggangService $masaTenggangService
    ) {}

    /**
     * Daftar seluruh penempatan dan status penilaian magang.
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();

        $query = Penempatan::with(['peserta.profil.jurusan', 'bidang', 'penilaian.details.rubrik'])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            });

        if ($request->filled('status_penempatan')) {
            $query->where('status', $request->input('status_penempatan'));
        }

        if ($request->filled('status_nilai')) {
            $statusNilai = $request->input('status_nilai');
            if ($statusNilai === 'belum') {
                $query->whereDoesntHave('penilaian');
            } else {
                $query->whereHas('penilaian', function ($q) use ($statusNilai) {
                    $q->where('status', $statusNilai);
                });
            }
        }

        if ($request->filled('q')) {
            $search = '%' . $request->input('q') . '%';
            $query->whereHas('peserta', function ($q) use ($search) {
                $q->where('nama', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhereHas('profil', function ($qp) use ($search) {
                        $qp->where('institusi', 'like', $search);
                    });
            });
        }

        $penempatan = $query->latest()->paginate(15)->withQueryString();

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $penempatan,
            ]);
        }

        return view('pembimbing.penilaian.index', [
            'penempatanList' => $penempatan,
        ]);
    }

    /**
     * Lembar instrumen penilaian rubrik untuk peserta terkait.
     */
    public function show(Penempatan $penempatan, Request $request): \Illuminate\View\View|\Illuminate\Http\JsonResponse
    {
        Gate::authorize('view', [Penilaian::class, $penempatan]);

        $rubrikAktif = Rubrik::where('aktif', true)->orderBy('id')->get();
        $penilaian = $penempatan->penilaian()->with(['details.rubrik', 'riwayats.pengubah'])->first();

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'penempatan' => $penempatan->load(['peserta.profil.jurusan', 'bidang']),
                    'rubrik_aktif' => $rubrikAktif,
                    'penilaian' => $penilaian,
                ],
            ]);
        }

        return view('pembimbing.penilaian.show', [
            'penempatan' => $penempatan->load(['peserta.profil.jurusan', 'bidang']),
            'rubrikAktif' => $rubrikAktif,
            'penilaian' => $penilaian,
        ]);
    }

    /**
     * Simpan evaluasi penilaian magang (Draft, Final, atau Revisi Final) - AC-10.
     */
    public function store(PenilaianRequest $request, Penempatan $penempatan): JsonResponse|RedirectResponse
    {
        Gate::authorize('manage', [Penilaian::class, $penempatan]);

        $user = $request->user();
        $skor = $request->validated('skor');
        $catatan = $request->validated('catatan');
        $isFinal = $request->boolean('is_final');

        $penilaianEksisting = $penempatan->penilaian;

        if ($isFinal) {
            if ($penilaianEksisting && $penilaianEksisting->status === StatusPenilaianEnum::Final) {
                // Revisi nilai final dengan alasan
                $alasan = (string) $request->validated('alasan');
                $penilaian = $this->scoringService->revisiFinal(
                    $penilaianEksisting,
                    $user,
                    $skor,
                    $catatan,
                    $alasan
                );
                $pesan = 'Revisi nilai final berhasil disimpan beserta riwayat perubahan.';
            } else {
                // Finalisasi nilai awal
                $penilaian = $this->scoringService->simpanFinal(
                    $penempatan,
                    $user,
                    $skor,
                    $catatan
                );
                $pesan = 'Penilaian magang berhasil difinalisasi.';
            }
        } else {
            // Simpan sebagai draft
            $penilaian = $this->scoringService->simpanDraft(
                $penempatan,
                $user,
                $skor,
                $catatan
            );
            $pesan = 'Draf penilaian magang berhasil disimpan.';
        }

        if (! $request->expectsJson() && ! $request->wantsJson()) {
            return back()->with('success', $pesan);
        }

        return response()->json([
            'success' => true,
            'message' => $pesan,
            'data' => $penilaian,
        ]);
    }

    /**
     * Menandai penempatan selesai magang (BR-10).
     * Mensyaratkan nilai akhir telah berstatus final.
     */
    public function selesai(Request $request, Penempatan $penempatan): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        try {
            $penempatanUpdated = $this->masaTenggangService->tandaiSelesai($penempatan, $request->user());
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            abort(403, $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->withErrors(['penilaian' => $e->getMessage()])->with('error', $e->getMessage());
        }

        if (! $request->expectsJson() && ! $request->wantsJson()) {
            return back()->with('success', 'Status magang peserta berhasil diselesaikan dan masa tenggang akun telah diaktifkan.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Status magang peserta berhasil diselesaikan dan masa tenggang akun telah diaktifkan.',
            'data' => $penempatanUpdated,
        ]);
    }

    /**
     * Mengunduh lembar nilai resmi berformat PDF (AC-12).
     */
    public function unduhPdf(Penempatan $penempatan): Response
    {
        Gate::authorize('downloadPdf', [Penilaian::class, $penempatan]);

        $penempatan->loadMissing(['peserta.profil.jurusan', 'pembimbing', 'bidang', 'penilaian.details.rubrik']);
        $penilaian = $penempatan->penilaian;

        $pdf = Pdf::loadView('pdf.lembar-nilai', [
            'penempatan' => $penempatan,
            'peserta' => $penempatan->peserta,
            'profil' => $penempatan->peserta->profil,
            'pembimbing' => $penempatan->pembimbing,
            'penilaian' => $penilaian,
        ])->setPaper('a4', 'portrait');

        $namaPeserta = $penempatan->peserta->nama ?? ($penempatan->peserta->name ?? 'peserta');
        $namaFile = 'lembar-nilai-' . str_replace(' ', '-', strtolower($namaPeserta)) . '.pdf';

        return $pdf->download($namaFile);
    }
}
