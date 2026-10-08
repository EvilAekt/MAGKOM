<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewProgresRequest;
use App\Http\Requests\TugasRequest;
use App\Models\Penempatan;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TugasController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Daftar seluruh penugasan yang diberikan kepada peserta bimbingan.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Tugas::with(['penempatan.peserta', 'penempatan.bidang', 'progresLogs'])
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

        $tugas = $query->latest()->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $tugas,
        ]);
    }

    /**
     * Memberikan tugas baru kepada peserta bimbingan (AC-08).
     */
    public function store(TugasRequest $request): JsonResponse
    {
        $penempatan = Penempatan::findOrFail($request->validated('penempatan_id'));

        // Penegakan batasan hak akses & isolasi SuperAdmin (AC-08-5)
        Gate::authorize('create', [Tugas::class, $penempatan]);

        $tugas = Tugas::create($request->validated());

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'tugas',
            idData: $tugas->id,
            nilaiLama: null,
            nilaiBaru: $request->validated(),
            detail: ['aksi' => 'buat_tugas']
        );

        return response()->json([
            'success' => true,
            'message' => 'Tugas baru berhasil dibuat.',
            'data' => $tugas->load('penempatan.peserta'),
        ], 201);
    }

    /**
     * Detail penugasan dan seluruh laporan progres kerja.
     */
    public function show(Request $request, Tugas $tugas): JsonResponse
    {
        Gate::authorize('view', $tugas);

        return response()->json([
            'success' => true,
            'data' => $tugas->load(['penempatan.peserta', 'penempatan.bidang', 'progresLogs']),
        ]);
    }

    /**
     * Memperbarui informasi atau status penugasan.
     */
    public function update(TugasRequest $request, Tugas $tugas): JsonResponse
    {
        Gate::authorize('update', $tugas);

        $nilaiLama = $tugas->only(['judul', 'deskripsi', 'kategori', 'kompleksitas', 'deadline', 'status']);
        $tugas->update($request->validated());

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'tugas',
            idData: $tugas->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $request->validated(),
            detail: ['aksi' => 'update_tugas']
        );

        return response()->json([
            'success' => true,
            'message' => 'Data tugas berhasil diperbarui.',
            'data' => $tugas,
        ]);
    }

    /**
     * Melakukan review umpan balik terhadap laporan progres tugas (AC-08-4).
     */
    public function reviewProgres(ReviewProgresRequest $request, ProgresLog $progres): JsonResponse
    {
        $tugas = $progres->tugas;
        Gate::authorize('reviewProgres', $tugas);

        $nilaiLama = $progres->only(['status_review', 'feedback']);

        $progres->update([
            'status_review' => $request->validated('status_review'),
            'feedback' => $request->validated('feedback'),
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'progres_log',
            idData: $progres->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $request->validated(),
            detail: ['aksi' => 'review_progres_tugas']
        );

        return response()->json([
            'success' => true,
            'message' => 'Evaluasi progres tugas berhasil disimpan.',
            'data' => $progres,
        ]);
    }
}
