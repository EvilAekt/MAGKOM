<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProgresLogRequest;
use App\Models\Penempatan;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar tugas untuk penempatan aktif peserta.
     */
    public function index(Request $request): \Illuminate\View\View|\Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $tugas = Tugas::with(['progresLogs', 'penempatan.pembimbing'])
            ->whereHas('penempatan', function ($q) use ($user) {
                $q->where('peserta_id', $user->id);
            })
            ->latest('deadline')
            ->paginate(15);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $tugas,
            ]);
        }

        return view('peserta.tugas.index', [
            'tugasList' => $tugas,
        ]);
    }

    /**
     * Detail tugas yang diberikan.
     */
    public function show(Request $request, Tugas $tugas): \Illuminate\View\View|\Illuminate\Http\JsonResponse
    {
        Gate::authorize('view', $tugas);

        $tugasData = $tugas->load(['progresLogs', 'penempatan.pembimbing']);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $tugasData,
            ]);
        }

        return view('peserta.tugas.show', [
            'tugas' => $tugasData,
        ]);
    }

    /**
     * Mengunggah laporan progres pekerjaan tugas (AC-08-3).
     */
    public function unggahProgres(ProgresLogRequest $request, Tugas $tugas): JsonResponse
    {
        Gate::authorize('submitProgres', $tugas);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store("progres/{$tugas->id}", 'private');
        }

        $progres = ProgresLog::create([
            'tugas_id' => $tugas->id,
            'catatan' => $request->validated('catatan'),
            'file' => $filePath,
            'tgl_lapor' => Carbon::today(),
            'status_review' => StatusReviewProgresEnum::Menunggu,
        ]);

        // Jika status tugas masih belum dikerjakan, ubah otomatis menjadi dalam proses
        if ($tugas->status === StatusTugasEnum::Belum) {
            $tugas->update(['status' => StatusTugasEnum::Proses]);
        }

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'progres_log',
            idData: $progres->id,
            nilaiLama: null,
            nilaiBaru: [
                'tugas_id' => $tugas->id,
                'tgl_lapor' => $progres->tgl_lapor->toDateString(),
            ],
            detail: ['aksi' => 'unggah_progres_tugas']
        );

        if (! $request->expectsJson() && ! $request->wantsJson()) {
            return back()->with('success', 'Laporan progres tugas berhasil dikirimkan.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Laporan progres tugas berhasil dikirimkan.',
            'data' => $progres,
        ], 201);
    }
}
