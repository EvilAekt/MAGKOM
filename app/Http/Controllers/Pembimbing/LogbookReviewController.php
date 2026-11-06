<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Enums\RealtimeEventTypeEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewLogbookRequest;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Services\AuditService;
use App\Services\RealtimeEventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LogbookReviewController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Daftar logbook harian seluruh peserta yang dibimbing (AC-09).
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();

        $query = Logbook::with(['penempatan.peserta.profil.jurusan', 'penempatan.bidang'])
            ->whereHas('penempatan', function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            });

        if ($request->filled('penempatan_id')) {
            $query->where('penempatan_id', $request->input('penempatan_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->input('tanggal'));
        }

        if ($request->filled('q')) {
            $search = '%'.$request->input('q').'%';
            $query->where(function ($q) use ($search) {
                $q->where('kegiatan', 'like', $search)
                    ->orWhere('catatan_pembimbing', 'like', $search)
                    ->orWhereHas('penempatan.peserta', function ($qp) use ($search) {
                        $qp->where('nama', 'like', $search);
                    });
            });
        }

        $logbooks = $query->latest('tanggal')->paginate(20)->withQueryString();

        $penempatanList = Penempatan::with(['peserta.profil.jurusan', 'bidang', 'logbooks' => function ($q) {
            $q->latest('tanggal');
        }])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            })
            ->where('status', StatusPenempatanEnum::Aktif)
            ->get();

        $baseCounter = Logbook::whereHas('penempatan', function ($q) use ($user) {
            if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                $q->where('pembimbing_id', $user->id);
            }
        });

        $totalLogbook = (clone $baseCounter)->count();
        $totalMenteeMelapor = (clone $baseCounter)->distinct('penempatan_id')->count('penempatan_id');
        $countDenganCatatan = (clone $baseCounter)->whereNotNull('catatan_pembimbing')->where('catatan_pembimbing', '!=', '')->count();
        $countTanpaCatatan = (clone $baseCounter)->where(function ($q) {
            $q->whereNull('catatan_pembimbing')->orWhere('catatan_pembimbing', '');
        })->count();

        $countMenunggu = (clone $baseCounter)->where('status', StatusLogbookEnum::Terkirim)->count();
        $countDisetujui = (clone $baseCounter)->where('status', StatusLogbookEnum::Disetujui)->count();
        $countRevisi = (clone $baseCounter)->where('status', StatusLogbookEnum::PerluRevisi)->count();

        $persenDisetujui = $totalLogbook > 0 ? (int) round(($countDisetujui / $totalLogbook) * 100) : 0;
        $persenMenunggu = $totalLogbook > 0 ? (int) round(($countMenunggu / $totalLogbook) * 100) : 0;
        $persenRevisi = $totalLogbook > 0 ? (int) round(($countRevisi / $totalLogbook) * 100) : 0;

        $selectedPenempatanId = $request->integer('penempatan_id') ?: null;
        $selectedPenempatan = null;
        if ($selectedPenempatanId) {
            $selectedPenempatan = $penempatanList->firstWhere('id', $selectedPenempatanId);
            if (! $selectedPenempatan) {
                $selectedPenempatan = Penempatan::with(['peserta.profil.jurusan', 'bidang', 'logbooks' => function ($q) {
                    $q->latest('tanggal');
                }])
                    ->where(function ($q) use ($user) {
                        if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                            $q->where('pembimbing_id', $user->id);
                        }
                    })
                    ->firstWhere('id', $selectedPenempatanId);
            }
        }

        $data = [
            'logbooks' => $logbooks,
            'penempatanList' => $penempatanList,
            'totalLogbook' => $totalLogbook,
            'totalMenteeMelapor' => $totalMenteeMelapor,
            'countDenganCatatan' => $countDenganCatatan,
            'countTanpaCatatan' => $countTanpaCatatan,
            'countMenunggu' => $countMenunggu,
            'countDisetujui' => $countDisetujui,
            'countRevisi' => $countRevisi,
            'persenDisetujui' => $persenDisetujui,
            'persenMenunggu' => $persenMenunggu,
            'persenRevisi' => $persenRevisi,
            'selectedPenempatanId' => $selectedPenempatanId,
            'selectedPenempatan' => $selectedPenempatan,
        ];

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        return view('pembimbing.logbook.index', $data);
    }

    /**
     * Berikan catatan logbook peserta oleh pembimbing (Task 02).
     */
    public function review(ReviewLogbookRequest $request, Logbook $logbook): JsonResponse|RedirectResponse
    {
        Gate::authorize('review', $logbook);

        $catatan = $request->validated('catatan_pembimbing');
        $nilaiLama = $logbook->only(['status', 'catatan_pembimbing']);

        $updatePayload = [
            'catatan_pembimbing' => $catatan,
        ];

        if ($request->filled('status')) {
            $updatePayload['status'] = $request->validated('status');
        }

        $logbook->update($updatePayload);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'logbook',
            idData: $logbook->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $updatePayload,
            detail: ['aksi' => 'beri_catatan_logbook']
        );

        // Notifikasi Real-time SSE ke Peserta (TASK-SSE-06)
        $pesertaId = $logbook->penempatan?->peserta_id;
        if ($pesertaId) {
            $isRevisi = $logbook->status === StatusLogbookEnum::PerluRevisi;
            $title = $isRevisi ? 'Logbook Memerlukan Revisi' : 'Logbook Harian Disetujui';
            $message = $isRevisi && filled($logbook->catatan_pembimbing)
                ? "Pembimbing memberikan catatan revisi logbook: {$logbook->catatan_pembimbing}"
                : (filled($catatan)
                    ? "Pembimbing memberikan catatan: {$catatan}"
                    : "Catatan logbook tanggal {$logbook->tanggal->translatedFormat('d F Y')} telah dinilai ({$logbook->status->label()}).");

            app(RealtimeEventService::class)->publish(
                RealtimeEventTypeEnum::LogbookDireviu,
                [
                    'title' => $title,
                    'message' => $message,
                    'status' => $logbook->status->value,
                    'catatan_pembimbing' => $logbook->catatan_pembimbing,
                    'logbook_id' => $logbook->id,
                    'action_url' => route('peserta.logbook.index'),
                    'badge_target' => $isRevisi ? 'logbook-revisi-badge' : null,
                ],
                $pesertaId
            );
        }

        if (! $request->expectsJson() && ! $request->wantsJson()) {
            return back()->with('success', 'Catatan pembimbing pada logbook berhasil disimpan.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Catatan pembimbing pada logbook berhasil disimpan.',
            'data' => $logbook,
        ]);
    }

    /**
     * Persetujuan logbook massal untuk entri berstatus terkirim (Pilar 1).
     */
    public function bulkReview(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'logbook_ids' => ['required', 'array', 'min:1'],
            'logbook_ids.*' => ['required', 'integer', 'exists:logbook,id'],
            'catatan_pembimbing' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $catatan = $request->input('catatan_pembimbing') ?? 'Disetujui secara massal oleh pembimbing lapangan.';
        $ids = $request->input('logbook_ids');

        // Proteksi Otorisasi Anti-IDOR (SEC-11)
        if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
            $unauthorizedCount = Logbook::whereIn('id', $ids)
                ->whereDoesntHave('penempatan', fn ($q) => $q->where('pembimbing_id', $user->id))
                ->count();

            if ($unauthorizedCount > 0) {
                abort(403, 'Akses ditolak: Anda hanya dapat mereview logbook peserta bimbingan Anda.');
            }
        }

        $logbooks = Logbook::with('penempatan')
            ->whereIn('id', $ids)
            ->where('status', StatusLogbookEnum::Terkirim)
            ->get();

        if ($logbooks->isEmpty()) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada entri logbook terkirim yang dapat disetujui.',
                ], 422);
            }

            return redirect()->route('pembimbing.logbook.index')->with('error', 'Tidak ada entri logbook terkirim yang dapat disetujui.');
        }

        $count = 0;
        foreach ($logbooks as $lb) {
            $nilaiLama = $lb->only(['status', 'catatan_pembimbing']);
            $lb->update([
                'status' => StatusLogbookEnum::Disetujui,
                'catatan_pembimbing' => $catatan,
            ]);

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'logbook',
                idData: $lb->id,
                nilaiLama: $nilaiLama,
                nilaiBaru: ['status' => StatusLogbookEnum::Disetujui->value, 'catatan_pembimbing' => $catatan],
                detail: ['aksi' => 'bulk_review_logbook', 'pembimbing_id' => $user->id]
            );
            $count++;
        }

        // Notifikasi Real-time SSE ke setiap peserta yang disetujui logbook-nya
        $pesertaIds = $logbooks->pluck('penempatan.peserta_id')->filter()->unique();
        foreach ($pesertaIds as $pId) {
            app(RealtimeEventService::class)->publish(
                RealtimeEventTypeEnum::LogbookDireviu,
                [
                    'title' => 'Logbook Harian Disetujui',
                    'message' => 'Catatan logbook Anda telah disetujui secara massal oleh pembimbing lapangan.',
                    'status' => StatusLogbookEnum::Disetujui->value,
                    'catatan_pembimbing' => $catatan,
                    'action_url' => route('peserta.logbook.index'),
                ],
                (int) $pId
            );
        }

        $message = "Berhasil menyetujui {$count} entri logbook harian sekaligus.";

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $count,
            ]);
        }

        return redirect()->route('pembimbing.logbook.index')->with('success', $message);
    }
}
