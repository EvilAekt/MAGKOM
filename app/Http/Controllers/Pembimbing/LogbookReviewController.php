<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewLogbookRequest;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Services\AuditService;
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
            $search = '%' . $request->input('q') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('kegiatan', 'like', $search)
                    ->orWhere('catatan_pembimbing', 'like', $search)
                    ->orWhereHas('penempatan.peserta', function ($qp) use ($search) {
                        $qp->where('nama', 'like', $search);
                    });
            });
        }

        $logbooks = $query->latest('tanggal')->paginate(20)->withQueryString();

        $penempatanList = Penempatan::with(['peserta.profil.jurusan', 'bidang'])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            })
            ->where('status', StatusPenempatanEnum::Aktif)
            ->get();

        $data = [
            'logbooks' => $logbooks,
            'penempatanList' => $penempatanList,
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
     * Review catatan logbook peserta oleh pembimbing (AC-09-3, AC-09-4).
     */
    public function review(ReviewLogbookRequest $request, Logbook $logbook): JsonResponse|RedirectResponse
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

        if (! $request->expectsJson() && ! $request->wantsJson()) {
            return back()->with('success', 'Review catatan logbook harian berhasil disimpan.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Review catatan logbook harian berhasil disimpan.',
            'data' => $logbook,
        ]);
    }
}
