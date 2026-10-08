<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewLogbookRequest;
use App\Models\Logbook;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LogbookReviewController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Daftar logbook harian seluruh peserta yang dibimbing.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Logbook::with(['penempatan.peserta', 'penempatan.bidang'])
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

        $logbooks = $query->latest('tanggal')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $logbooks,
        ]);
    }

    /**
     * Review catatan logbook peserta oleh pembimbing (AC-09-3).
     */
    public function review(ReviewLogbookRequest $request, Logbook $logbook): JsonResponse
    {
        Gate::authorize('review', $logbook);

        $nilaiLama = $logbook->only(['status', 'catatan_pembimbing']);

        $logbook->update([
            'status' => $request->validated('status'),
            'catatan_pembimbing' => $request->validated('catatan_pembimbing'),
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'logbook',
            idData: $logbook->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $request->validated(),
            detail: ['aksi' => 'review_logbook']
        );

        return response()->json([
            'success' => true,
            'message' => 'Review catatan logbook harian berhasil disimpan.',
            'data' => $logbook,
        ]);
    }
}
