<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Actions\Pembimbing\Tugas\CreateBatchTugasAction;
use App\Actions\Pembimbing\Tugas\MarkTugasCompletedAction;
use App\Actions\Pembimbing\Tugas\ReviewTugasProgresAction;
use App\Actions\Pembimbing\Tugas\UpdateTugasAction;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewProgresRequest;
use App\Http\Requests\TugasRequest;
use App\Models\Penempatan;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Models\TugasTemplate;
use App\Services\Pembimbing\TugasDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TugasController extends Controller
{
    public function __construct(
        protected TugasDashboardService $dashboardService,
        protected CreateBatchTugasAction $createBatchAction,
        protected UpdateTugasAction $updateAction,
        protected MarkTugasCompletedAction $markCompletedAction,
        protected ReviewTugasProgresAction $reviewProgresAction
    ) {}

    /**
     * Daftar seluruh penugasan yang diberikan kepada peserta bimbingan.
     */
    public function index(Request $request): View|JsonResponse
    {
        $data = $this->dashboardService->getDashboardData($request, $request->user());

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        }

        return view('pembimbing.tugas.index', $data);
    }

    /**
     * Menampilkan form layar penuh pembuatan tugas baru (Assignment Workspace ala Classroom).
     */
    public function create(Request $request): View
    {
        $user = $request->user();

        $penempatanList = Penempatan::with(['peserta.profil.jurusan', 'bidang'])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            })
            ->where('status', StatusPenempatanEnum::Aktif)
            ->get();

        $selectedPenempatanId = $request->integer('penempatan_id', 0);
        $selectedTemplate = null;
        if ($request->filled('template_id')) {
            $selectedTemplate = TugasTemplate::accessibleBy($user->id)
                ->find($request->integer('template_id'));
        }

        $templates = TugasTemplate::accessibleBy($user->id)->latest()->get();

        return view('pembimbing.tugas.create', [
            'penempatanList' => $penempatanList,
            'selectedPenempatanId' => $selectedPenempatanId > 0 ? $selectedPenempatanId : null,
            'selectedTemplate' => $selectedTemplate,
            'templates' => $templates,
        ]);
    }

    /**
     * Memberikan tugas baru kepada satu atau beberapa peserta bimbingan (AC-08).
     */
    public function store(TugasRequest $request): JsonResponse|RedirectResponse
    {
        $penempatanIds = $request->input('penempatan_ids', []);
        if (empty($penempatanIds) && $request->filled('penempatan_id')) {
            $penempatanIds = [(int) $request->input('penempatan_id')];
        }

        $penempatans = Penempatan::with(['peserta.profil.jurusan', 'bidang'])
            ->whereIn('id', $penempatanIds)
            ->get();

        if ($penempatans->isEmpty()) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Peserta bimbingan tidak ditemukan.',
                ], 422);
            }

            return back()->withErrors(['penempatan_ids' => 'Minimal satu peserta magang harus dipilih.'])->withInput();
        }

        $result = $this->createBatchAction->execute(
            penempatans: $penempatans,
            validated: $request->validated(),
            fileLampiran: $request->file('file_lampiran'),
            user: $request->user()
        );

        if (! $request->expectsJson() && ! $request->wantsJson()) {
            return redirect()->route('pembimbing.tugas.index')
                ->with('success', $result['message']);
        }

        $createdTugasList = $result['tugasList'];
        $primaryTugas = $createdTugasList->isNotEmpty() ? $createdTugasList->first() : null;

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => $primaryTugas,
            'items' => $createdTugasList,
        ], 201);
    }

    /**
     * Detail penugasan dan seluruh laporan progres kerja.
     */
    public function show(Request $request, Tugas $tugas): View|JsonResponse
    {
        Gate::authorize('view', $tugas);

        $tugasData = $tugas->load(['penempatan.peserta.profil.jurusan', 'penempatan.bidang', 'progresLogs']);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $tugasData,
            ]);
        }

        $user = $request->user();
        $querySiblings = Tugas::with(['penempatan.peserta.profil', 'progresLogs'])
            ->whereHas('penempatan', function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            });

        if ($tugas->batch_id) {
            $querySiblings->where('batch_id', $tugas->batch_id);
        } else {
            $querySiblings->where('penempatan_id', $tugas->penempatan_id);
        }

        $siblingTugasList = $querySiblings->oldest('id')->get();
        if ($siblingTugasList->isEmpty()) {
            $siblingTugasList = collect([$tugasData]);
        }

        $currentIndex = $siblingTugasList->search(fn ($item) => $item->id === $tugas->id);
        $prevTugas = ($currentIndex !== false && $currentIndex > 0) ? $siblingTugasList->get($currentIndex - 1) : null;
        $nextTugas = ($currentIndex !== false && $currentIndex < $siblingTugasList->count() - 1) ? $siblingTugasList->get($currentIndex + 1) : null;

        return view('pembimbing.tugas.show', [
            'tugas' => $tugasData,
            'siblingTugasList' => $siblingTugasList,
            'prevTugas' => $prevTugas,
            'nextTugas' => $nextTugas,
        ]);
    }

    /**
     * Mengunduh berkas fisik acuan tugas secara aman (AC-08).
     */
    public function unduhLampiran(Request $request, Tugas $tugas): mixed
    {
        Gate::authorize('view', $tugas);

        if (! $tugas->file_lampiran || ! Storage::disk('private')->exists($tugas->file_lampiran)) {
            abort(404, 'Berkas lampiran acuan tugas tidak ditemukan.');
        }

        return Storage::disk('private')->download($tugas->file_lampiran, $tugas->nama_file_lampiran ?: 'lampiran-tugas');
    }

    /**
     * Menampilkan formulir pengubahan data penugasan (AC-08-7).
     */
    public function edit(Tugas $tugas): View|RedirectResponse
    {
        Gate::authorize('update', $tugas);

        if ($tugas->status === StatusTugasEnum::Selesai) {
            return redirect()->route('pembimbing.tugas.show', $tugas)
                ->withErrors(['error' => 'Tugas yang telah berstatus selesai tidak dapat diedit kembali.']);
        }

        return view('pembimbing.tugas.edit', [
            'tugas' => $tugas->load('penempatan.peserta.profil.jurusan'),
        ]);
    }

    /**
     * Memperbarui informasi atau status penugasan.
     */
    public function update(TugasRequest $request, Tugas $tugas): JsonResponse|RedirectResponse
    {
        $result = $this->updateAction->execute(
            tugas: $tugas,
            validated: $request->validated(),
            fileLampiran: $request->file('file_lampiran')
        );

        if (! $result['success']) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], $result['status']);
            }

            return back()->withErrors(['error' => $result['message']]);
        }

        if (! $request->expectsJson() && ! $request->wantsJson()) {
            return back()->with('success', $result['message']);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'data' => $result['tugas'],
        ]);
    }

    /**
     * Menandai penugasan sebagai selesai oleh pembimbing (A-22).
     */
    public function selesai(Request $request, Tugas $tugas): JsonResponse|RedirectResponse
    {
        $updatedTugas = $this->markCompletedAction->execute($tugas);

        $message = 'Status tugas berhasil ditandai sebagai Selesai.';

        if (! $request->expectsJson() && ! $request->wantsJson()) {
            return back()->with('success', $message);
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $updatedTugas,
        ]);
    }

    /**
     * Melakukan review umpan balik terhadap laporan progres tugas (AC-08-4).
     */
    public function reviewProgres(ReviewProgresRequest $request, ProgresLog $progres): JsonResponse|RedirectResponse
    {
        $updatedProgres = $this->reviewProgresAction->execute($progres, $request->validated());

        if (! $request->expectsJson() && ! $request->wantsJson()) {
            return back()->with('success', 'Evaluasi umpan balik progres tugas berhasil disimpan.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Evaluasi progres tugas berhasil disimpan.',
            'data' => $updatedProgres,
        ]);
    }
}
