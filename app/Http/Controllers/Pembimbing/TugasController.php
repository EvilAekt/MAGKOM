<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewProgresRequest;
use App\Http\Requests\TugasRequest;
use App\Models\Penempatan;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TugasController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Daftar seluruh penugasan yang diberikan kepada peserta bimbingan.
     */
    public function index(Request $request): View|JsonResponse
    {
        $user = $request->user();

        $query = Tugas::with(['penempatan.peserta.profil.jurusan', 'penempatan.bidang', 'progresLogs'])
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

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->input('kategori'));
        }

        if ($request->filled('q')) {
            $search = '%' . $request->input('q') . '%';
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', $search)
                    ->orWhere('deskripsi', 'like', $search)
                    ->orWhereHas('penempatan.peserta', function ($qp) use ($search) {
                        $qp->where('nama', 'like', $search);
                    });
            });
        }

        $tugasList = $query->latest()->paginate(15)->withQueryString();

        // Ambil daftar penempatan mentee aktif untuk dropdown buat tugas
        $penempatanList = Penempatan::with(['peserta.profil.jurusan', 'bidang'])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            })
            ->where('status', StatusPenempatanEnum::Aktif)
            ->get();

        $data = [
            'tugasList' => $tugasList,
            'penempatanList' => $penempatanList,
        ];

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
            $selectedTemplate = \App\Models\TugasTemplate::accessibleBy($user->id)
                ->find($request->integer('template_id'));
        }

        $templates = \App\Models\TugasTemplate::accessibleBy($user->id)->latest()->get();

        return view('pembimbing.tugas.create', [
            'penempatanList' => $penempatanList,
            'selectedPenempatanId' => $selectedPenempatanId > 0 ? $selectedPenempatanId : null,
            'selectedTemplate' => $selectedTemplate,
            'templates' => $templates,
        ]);
    }

    /**
     * Memberikan tugas baru kepada peserta bimbingan (AC-08).
     */
    public function store(TugasRequest $request): JsonResponse|RedirectResponse
    {
        $penempatan = Penempatan::findOrFail($request->validated('penempatan_id'));

        // Penegakan batasan hak akses & isolasi SuperAdmin (AC-08-5)
        Gate::authorize('create', [Tugas::class, $penempatan]);

        $payload = $request->validated();
        unset($payload['file_lampiran']);

        $tugas = Tugas::create($payload);

        if ($request->hasFile('file_lampiran')) {
            $file = $request->file('file_lampiran');
            $path = $file->store("tugas/{$tugas->id}", 'private');
            $tugas->update([
                'file_lampiran' => $path,
                'nama_file_lampiran' => $file->getClientOriginalName(),
                'ukuran_file_lampiran' => $file->getSize(),
            ]);
        }

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'tugas',
            idData: $tugas->id,
            nilaiLama: null,
            nilaiBaru: $tugas->only(['judul', 'kategori', 'kompleksitas', 'deadline', 'link_referensi', 'nama_file_lampiran']),
            detail: ['aksi' => 'buat_tugas']
        );

        if (! $request->expectsJson() && ! $request->wantsJson()) {
            return redirect()->route('pembimbing.tugas.index')
                ->with('success', 'Tugas baru berhasil diberikan kepada mentee.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Tugas baru berhasil dibuat.',
            'data' => $tugas->load('penempatan.peserta'),
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

        return view('pembimbing.tugas.show', [
            'tugas' => $tugasData,
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
     * Memperbarui informasi atau status penugasan.
     */
    public function update(TugasRequest $request, Tugas $tugas): JsonResponse|RedirectResponse
    {
        Gate::authorize('update', $tugas);

        $nilaiLama = $tugas->only(['judul', 'deskripsi', 'link_referensi', 'nama_file_lampiran', 'kategori', 'kompleksitas', 'deadline', 'status']);
        $payload = $request->validated();
        unset($payload['file_lampiran']);

        if ($request->hasFile('file_lampiran')) {
            if ($tugas->file_lampiran && Storage::disk('private')->exists($tugas->file_lampiran)) {
                Storage::disk('private')->delete($tugas->file_lampiran);
            }
            $file = $request->file('file_lampiran');
            $path = $file->store("tugas/{$tugas->id}", 'private');
            $payload['file_lampiran'] = $path;
            $payload['nama_file_lampiran'] = $file->getClientOriginalName();
            $payload['ukuran_file_lampiran'] = $file->getSize();
        }

        $tugas->update($payload);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'tugas',
            idData: $tugas->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $tugas->only(['judul', 'link_referensi', 'nama_file_lampiran', 'kategori', 'kompleksitas', 'deadline', 'status']),
            detail: ['aksi' => 'update_tugas']
        );

        if (! $request->expectsJson() && ! $request->wantsJson()) {
            return back()->with('success', 'Data tugas berhasil diperbarui.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Data tugas berhasil diperbarui.',
            'data' => $tugas,
        ]);
    }

    /**
     * Menandai penugasan sebagai selesai oleh pembimbing (A-22).
     */
    public function selesai(Request $request, Tugas $tugas): JsonResponse|RedirectResponse
    {
        Gate::authorize('update', $tugas);

        $nilaiLama = ['status' => $tugas->status->value];
        $tugas->update(['status' => StatusTugasEnum::Selesai]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'tugas',
            idData: $tugas->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: ['status' => StatusTugasEnum::Selesai->value],
            detail: ['aksi' => 'selesaikan_tugas_oleh_pembimbing']
        );

        if (! $request->expectsJson() && ! $request->wantsJson()) {
            return back()->with('success', 'Status tugas berhasil ditandai sebagai Selesai.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Status tugas berhasil ditandai sebagai Selesai.',
            'data' => $tugas,
        ]);
    }

    /**
     * Melakukan review umpan balik terhadap laporan progres tugas (AC-08-4).
     */
    public function reviewProgres(ReviewProgresRequest $request, ProgresLog $progres): JsonResponse|RedirectResponse
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

        if (! $request->expectsJson() && ! $request->wantsJson()) {
            return back()->with('success', 'Evaluasi umpan balik progres tugas berhasil disimpan.');
        }

        return response()->json([
            'success' => true,
            'message' => 'Evaluasi progres tugas berhasil disimpan.',
            'data' => $progres,
        ]);
    }
}
