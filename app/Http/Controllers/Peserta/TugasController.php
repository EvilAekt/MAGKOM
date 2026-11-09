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
use Illuminate\Support\Str;

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

        $tugasQuery = Tugas::dipublikasikan()
            ->whereHas('penempatan', function ($q) use ($user) {
                $q->where('peserta_id', $user->id);
            });

        $allTugas = (clone $tugasQuery)->get();
        $totalSemua = $allTugas->count();
        $totalBelum = 0;
        $totalProses = 0;
        $totalSelesai = 0;
        $totalTerlambat = 0;
        $now = Carbon::now();

        foreach ($allTugas as $item) {
            $st = $item->status instanceof StatusTugasEnum ? $item->status->value : (string) $item->status;
            $dl = Carbon::parse($item->deadline);

            if ($st === StatusTugasEnum::Selesai->value || $st === 'selesai') {
                $totalSelesai++;
            } else {
                if ($st === StatusTugasEnum::Proses->value || $st === 'proses') {
                    $totalProses++;
                } else {
                    $totalBelum++;
                }

                if ($dl->isPast()) {
                    $totalTerlambat++;
                }
            }
        }

        $tugas = (clone $tugasQuery)
            ->with(['progresLogs', 'penempatan.pembimbing'])
            ->latest('deadline')
            ->paginate(15);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $tugas,
                'meta' => [
                    'totalSemua' => $totalSemua,
                    'totalBelum' => $totalBelum,
                    'totalProses' => $totalProses,
                    'totalSelesai' => $totalSelesai,
                    'totalTerlambat' => $totalTerlambat,
                ],
            ]);
        }

        return view('peserta.tugas.index', [
            'tugasList' => $tugas,
            'tugas' => $tugas,
            'totalSemua' => $totalSemua,
            'totalBelum' => $totalBelum,
            'totalProses' => $totalProses,
            'totalSelesai' => $totalSelesai,
            'totalTerlambat' => $totalTerlambat,
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

        $user = $request->user();
        $recentFiles = collect();
        if ($user) {
            $recentFiles = ProgresLog::whereHas('tugas.penempatan', function ($q) use ($user) {
                $q->where('peserta_id', $user->id);
            })
                ->whereNotNull('file')
                ->latest('id')
                ->limit(12)
                ->get()
                ->filter(function ($log) {
                    return ! empty($log->file) && Storage::disk('private')->exists($log->file);
                })
                ->map(function ($log) {
                    $filename = basename($log->file);
                    $size = Storage::disk('private')->size($log->file);
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    return [
                        'id' => $log->id,
                        'path' => $log->file,
                        'filename' => $filename,
                        'extension' => $ext,
                        'size_formatted' => $size > 1048576
                            ? round($size / 1048576, 2).' MB'
                            : round($size / 1024, 1).' KB',
                        'tgl_lapor' => $log->tgl_lapor ? Carbon::parse($log->tgl_lapor)->translatedFormat('d M Y') : $log->created_at?->translatedFormat('d M Y'),
                    ];
                })
                ->values();
        }

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $tugas,
                'recent_files' => $recentFiles,
            ]);
        }

        return view('peserta.tugas.show', [
            'tugas' => $tugas,
            'recentFiles' => $recentFiles,
        ]);
    }

    /**
     * Mengunggah laporan progres pekerjaan tugas (AC-08-3).
     */
    public function unggahProgres(ProgresLogRequest $request, Tugas $tugas): mixed
    {
        Gate::authorize('submitProgres', $tugas);

        // Kunci jika tugas sudah selesai
        if ($tugas->status === StatusTugasEnum::Selesai) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tugas ini telah ditinjau dan dinyatakan selesai oleh pembimbing.',
                ], 422);
            }

            return back()->with('error', 'Tugas ini telah ditinjau dan dinyatakan selesai oleh pembimbing.');
        }

        // Kunci pengumpulan berulang jika tugas sudah pernah dikumpulkan dan belum berstatus perlu revisi
        $latestProgres = $tugas->progresLogs()->latest('id')->first();
        if ($latestProgres && $latestProgres->status_review !== StatusReviewProgresEnum::PerluRevisi) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tugas ini sudah diserahkan dan sedang menunggu peninjauan dari pembimbing.',
                ], 422);
            }

            return back()->with('error', 'Tugas ini sudah diserahkan dan sedang menunggu peninjauan dari pembimbing.');
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store("progres/{$tugas->id}", 'private');
        } elseif ($request->filled('recent_file_path')) {
            // Anti-IDOR: pastikan file ini benar-benar milik peserta yang login atau berkas tugas aktif miliknya
            $recentPath = (string) $request->validated('recent_file_path');
            $user = $request->user();
            $isOwned = str_starts_with($recentPath, "progres/{$tugas->id}/")
                || ProgresLog::where('file', $recentPath)
                    ->whereHas('tugas.penempatan', function ($q) use ($user) {
                        $q->where('peserta_id', $user?->id);
                    })
                    ->exists();

            if ($isOwned && Storage::disk('private')->exists($recentPath)) {
                if (str_starts_with($recentPath, "progres/{$tugas->id}/")) {
                    $filePath = $recentPath;
                } else {
                    $ext = pathinfo($recentPath, PATHINFO_EXTENSION);
                    $newFilename = Str::random(40).($ext ? ".{$ext}" : '');
                    $filePath = "progres/{$tugas->id}/{$newFilename}";
                    Storage::disk('private')->copy($recentPath, $filePath);
                }
            }
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

    /**
     * Membatalkan pengiriman tugas yang sedang menunggu peninjauan pembimbing (Unsubmit).
     */
    public function batalkanPengiriman(Request $request, Tugas $tugas): mixed
    {
        Gate::authorize('submitProgres', $tugas);

        // Kunci jika tugas sudah selesai
        if ($tugas->status === StatusTugasEnum::Selesai) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tugas ini telah ditinjau dan dinyatakan selesai oleh pembimbing.',
                ], 422);
            }

            return back()->with('error', 'Tugas ini telah ditinjau dan dinyatakan selesai oleh pembimbing.');
        }

        // Ambil progres terakhir yang sedang menunggu review
        $latestProgres = $tugas->progresLogs()->latest('id')->first();
        if (! $latestProgres || $latestProgres->status_review !== StatusReviewProgresEnum::Menunggu) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengiriman tidak dapat dibatalkan karena tugas belum diserahkan atau sudah ditinjau oleh pembimbing.',
                ], 422);
            }

            return back()->with('error', 'Pengiriman tidak dapat dibatalkan karena tugas belum diserahkan atau sudah ditinjau oleh pembimbing.');
        }

        $nilaiLama = $latestProgres->only(['tugas_id', 'file', 'link', 'status_review', 'catatan']);
        $oldFile = $latestProgres->file;
        $oldLink = $latestProgres->link;

        // Hapus record progres pending
        $latestProgres->delete();

        // Catat ke Audit Trail
        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'progres_log',
            idData: $latestProgres->id,
            nilaiLama: $nilaiLama,
            detail: ['aksi' => 'batalkan_pengiriman_tugas']
        );

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengiriman tugas berhasil dibatalkan. Silakan periksa kembali berkas atau tautan Anda.',
                'unsubmitted_file' => $oldFile,
                'unsubmitted_link' => $oldLink,
            ]);
        }

        return redirect()->route('peserta.tugas.show', $tugas)
            ->with('success', 'Pengiriman tugas berhasil dibatalkan. Silakan periksa kembali berkas atau tautan Anda.')
            ->with('unsubmitted_file', $oldFile)
            ->with('unsubmitted_file_name', $oldFile ? basename($oldFile) : null)
            ->with('unsubmitted_link', $oldLink);
    }
}
