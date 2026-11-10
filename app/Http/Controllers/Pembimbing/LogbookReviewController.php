<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Actions\Pembimbing\Logbook\BulkReviewLogbookAction;
use App\Actions\Pembimbing\Logbook\ReviewLogbookAction;
use App\Enums\StatusLogbookEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Pembimbing\BulkReviewLogbookRequest;
use App\Http\Requests\ReviewLogbookRequest;
use App\Models\Logbook;
use App\Queries\Pembimbing\LogbookIndexQuery;
use App\Queries\Pembimbing\PenempatanLogbookQuery;
use App\Services\Pembimbing\LogbookDashboardService;
use App\Services\Shared\BulanMagangService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class LogbookReviewController extends Controller
{
    public function __construct(
        protected ReviewLogbookAction $reviewAction,
        protected BulkReviewLogbookAction $bulkReviewAction,
        protected LogbookIndexQuery $logbookIndexQuery,
        protected PenempatanLogbookQuery $penempatanQuery,
        protected LogbookDashboardService $dashboardService,
        protected BulanMagangService $bulanMagangService
    ) {}

    /**
     * Daftar logbook harian seluruh peserta yang dibimbing (AC-09).
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();

        $logbooks = $this->logbookIndexQuery->getPaginated($user, $request, 10);
        $menteeData = $this->penempatanQuery->getMenteeData($user, $request, 10);
        $statistics = $this->dashboardService->getStatistics($user);
        $daftarBulan = $this->bulanMagangService->getDaftarBulan();

        $data = array_merge([
            'logbooks' => $logbooks,
            'daftarBulan' => $daftarBulan,
        ], $menteeData, $statistics);

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

        $rawCatatan = $request->validated('catatan_pembimbing');
        $catatan = filled($rawCatatan) ? trim((string) $rawCatatan) : null;

        $status = null;
        if ($request->filled('status')) {
            $statusInput = $request->validated('status');
            $status = $statusInput instanceof StatusLogbookEnum
                ? $statusInput
                : StatusLogbookEnum::tryFrom((string) $statusInput);
        }

        $logbook = $this->reviewAction->execute(
            $request->user(),
            $logbook,
            $catatan,
            $status
        );

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
    public function bulkReview(BulkReviewLogbookRequest $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $catatan = $request->input('catatan_pembimbing') ?? 'Disetujui secara massal oleh pembimbing lapangan.';
        $ids = (array) $request->validated('logbook_ids');

        $count = $this->bulkReviewAction->execute($user, $ids, $catatan);

        if ($count === 0) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada entri logbook terkirim yang dapat disetujui.',
                ], 422);
            }

            return redirect()->route('pembimbing.logbook.index')
                ->with('error', 'Tidak ada entri logbook terkirim yang dapat disetujui.');
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
