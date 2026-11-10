<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Actions\Peserta\Logbook\CreateLogbookAction;
use App\Actions\Peserta\Logbook\UpdateLogbookAction;
use App\Enums\StatusPenempatanEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\LogbookRequest;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Queries\Peserta\LogbookIndexQuery;
use App\Services\Peserta\LogbookAttachmentService;
use App\Support\Pengaturan;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class LogbookController extends Controller
{
    public function __construct(
        protected CreateLogbookAction $createLogbookAction,
        protected UpdateLogbookAction $updateLogbookAction,
        protected LogbookAttachmentService $attachmentService,
        protected LogbookIndexQuery $indexQuery
    ) {}

    /**
     * Menampilkan daftar riwayat logbook mingguan peserta.
     */
    public function index(Request $request): mixed
    {
        $user = $request->user();

        $penempatan = $this->indexQuery->getPenempatanAktif($user);
        $logbooks = $this->indexQuery->getLogbooks($user, 20);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $logbooks,
            ]);
        }

        $logbookMaksMundur = Pengaturan::int('logbook_maks_mundur_hari', 3);
        $daftarMinggu = $penempatan ? $penempatan->getDaftarMingguMagang() : [];
        $totalMinggu = $penempatan ? $penempatan->total_minggu : 12;
        $mingguAktif = $penempatan ? $penempatan->minggu_aktif : 1;

        $stats = $this->indexQuery->getStatistik($penempatan, $totalMinggu, $mingguAktif);

        return view('peserta.logbook.index', [
            'penempatan' => $penempatan,
            'logbooks' => $logbooks,
            'logbookMaksMundur' => $logbookMaksMundur,
            'daftarMinggu' => $daftarMinggu,
            'totalMinggu' => $totalMinggu,
            'mingguAktif' => $mingguAktif,
            'totalTerisi' => $stats['totalTerisi'],
            'totalTerkirim' => $stats['totalTerkirim'],
            'persentaseTerisi' => $stats['persentaseTerisi'],
            'mingguAktifSudahDiisi' => $stats['mingguAktifSudahDiisi'],
        ]);
    }

    /**
     * Mengisi catatan logbook mingguan baru.
     */
    public function store(LogbookRequest $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        $penempatan = Penempatan::where('peserta_id', $user->id)
            ->where('status', StatusPenempatanEnum::Aktif)
            ->first();

        if (! $penempatan) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki status penempatan magang aktif.',
                ], 422);
            }

            return redirect()->route('peserta.logbook.index')
                ->with('error', 'Anda tidak memiliki status penempatan magang aktif.');
        }

        Gate::authorize('create', [Logbook::class, $penempatan]);

        try {
            $logbook = $this->createLogbookAction->execute(
                $user,
                $penempatan,
                $request->validated(),
                $request->file('file_laporan')
            );
        } catch (DomainException $e) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->route('peserta.logbook.index')
                ->with('error', $e->getMessage());
        }

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catatan kegiatan logbook mingguan berhasil disimpan.',
                'data' => $logbook,
            ], 201);
        }

        return redirect()->route('peserta.logbook.index')
            ->with('success', 'Catatan logbook mingguan berhasil disimpan.');
    }

    /**
     * Memperbarui catatan logbook yang telah dibuat.
     */
    public function update(LogbookRequest $request, Logbook $logbook): JsonResponse|RedirectResponse
    {
        Gate::authorize('update', $logbook);

        $logbook = $this->updateLogbookAction->execute(
            $request->user(),
            $logbook,
            $request->validated(),
            $request->file('file_laporan')
        );

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catatan logbook mingguan berhasil diperbarui.',
                'data' => $logbook,
            ]);
        }

        return redirect()->route('peserta.logbook.index')
            ->with('success', 'Catatan logbook mingguan berhasil diperbarui.');
    }

    /**
     * Mengunduh berkas PDF lampiran logbook dari disk privat (AC-09-6, F-15).
     */
    public function unduhLampiran(Logbook $logbook): Response
    {
        Gate::authorize('view', $logbook);

        return $this->attachmentService->downloadResponse($logbook);
    }
}
