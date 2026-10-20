<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Enums\RealtimeEventTypeEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProgresLogRequest;
use App\Models\Penempatan;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Services\AuditService;
use App\Services\RealtimeEventService;
use Carbon\Carbon;
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
    public function index(Request $request): mixed
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
            'tugas' => $tugas,
        ]);
    }

    /**
     * Detail tugas yang diberikan.
     */
    public function show(Request $request, Tugas $tugas): mixed
    {
        Gate::authorize('view', $tugas);

        $tugas->load(['progresLogs' => function ($q) {
            $q->latest();
        }, 'penempatan.pembimbing']);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $tugas,
            ]);
        }

        return view('peserta.tugas.show', [
            'tugas' => $tugas,
        ]);
    }

    /**
     * Mengunggah laporan progres pekerjaan tugas (AC-08-3).
     */
    public function unggahProgres(ProgresLogRequest $request, Tugas $tugas): mixed
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
            'link' => $request->validated('link'),
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
            nilaiBaru: [
                'tugas_id' => $tugas->id,
                'tgl_lapor' => $progres->tgl_lapor->toDateString(),
            ],
            detail: ['aksi' => 'unggah_progres_tugas']
        );

        // Notifikasi Real-time SSE ke Pembimbing (TASK-SSE-06)
        $pembimbingId = $tugas->penempatan?->pembimbing_id;
        if ($pembimbingId) {
            $pesertaNama = $request->user()?->nama ?? 'Peserta';
            app(RealtimeEventService::class)->publish(
                RealtimeEventTypeEnum::TugasDikumpulkan,
                [
                    'title' => 'Pengumpulan Progres Tugas',
                    'message' => "{$pesertaNama} mengumpulkan progres untuk tugas: {$tugas->judul}.",
                    'action_url' => route('pembimbing.tugas.show', $tugas->id),
                ],
                $pembimbingId
            );
        }

        if ($request->expectsJson() || $request->wantsJson()) {

            return response()->json([
                'success' => true,
                'message' => 'Laporan progres tugas berhasil dikirimkan.',
                'data' => $progres,
            ], 201);
        }

        return redirect()->route('peserta.tugas.show', $tugas)
            ->with('success', 'Laporan progres tugas berhasil diunggah.');
    }

    /**
     * Mengunduh berkas fisik acuan tugas yang diberikan oleh pembimbing (AC-08).
     */
    public function unduhLampiran(Request $request, Tugas $tugas): mixed
    {
        Gate::authorize('view', $tugas);

        if (! $tugas->file_lampiran || ! Storage::disk('private')->exists($tugas->file_lampiran)) {
            abort(404, 'Berkas panduan tugas tidak ditemukan.');
        }

        return Storage::disk('private')->download($tugas->file_lampiran, $tugas->nama_file_lampiran ?: 'panduan-tugas');
    }
}
