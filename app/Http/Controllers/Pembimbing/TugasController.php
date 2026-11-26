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
use App\Models\KelompokMagang;
use App\Models\Penempatan;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Models\TugasTemplate;
use App\Models\User;
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

        // Daftar kelompok/tim aktif di bawah bimbingan pembimbing untuk mode Tugas Tim
        $kelompokIds = $penempatanList
            ->loadMissing('pendaftaran.kelompokMagang')
            ->pluck('pendaftaran.kelompok_magang_id')
            ->filter()
            ->unique();

        $kelompokList = KelompokMagang::with(['ketua.profil', 'anggota.user'])
            ->whereIn('id', $kelompokIds)
            ->get();

        $initialTipe = $request->input('tipe') === 'kelompok' ? 'kelompok' : 'individu';
        $selectedKelompokId = $request->integer('kelompok_id', 0);

        return view('pembimbing.tugas.create', [
            'penempatanList' => $penempatanList,
            'kelompokList' => $kelompokList,
            'selectedPenempatanId' => $selectedPenempatanId > 0 ? $selectedPenempatanId : null,
            'selectedKelompokId' => $selectedKelompokId > 0 ? $selectedKelompokId : null,
            'initialTipe' => $initialTipe,
            'selectedTemplate' => $selectedTemplate,
            'templates' => $templates,
        ]);
    }

    /**
     * Memberikan tugas baru kepada satu atau beberapa peserta bimbingan atau tim (AC-08).
     */
    public function store(TugasRequest $request): JsonResponse|RedirectResponse
    {
        $tipe = $request->input('tipe', 'individu');

        if ($tipe === 'kelompok') {
            $kelompokIds = $request->input('kelompok_magang_ids', []);
            if (empty($kelompokIds) && $request->filled('kelompok_id')) {
                $kelompokIds = [(int) $request->input('kelompok_id')];
            }

            $kelompokList = KelompokMagang::with(['ketua', 'anggota.user'])
                ->whereIn('id', $kelompokIds)
                ->get();

            if ($kelompokList->isEmpty()) {
                if ($request->expectsJson() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Tim bimbingan tidak ditemukan.',
                    ], 422);
                }

                return back()->withErrors(['kelompok_magang_ids' => 'Minimal satu tim magang harus dipilih.'])->withInput();
            }

            $result = $this->createBatchAction->executeForKelompok(
                kelompokList: $kelompokList,
                validated: $request->validated(),
                fileLampiran: $request->file('file_lampiran'),
                user: $request->user()
            );

            if (! $request->expectsJson() && ! $request->wantsJson()) {
                $redirectParams = ['jenis_tugas' => 'kelompok'];
                if ($kelompokList->count() === 1) {
                    $redirectParams['masuk_kelompok'] = $kelompokList->first()->id;
                    $redirectParams['tab_tim'] = 'tugas_tim';
                }

                return redirect()->route('pembimbing.tugas.index', $redirectParams)
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

        $tugasData = $tugas->load([
            'penempatan.peserta.profil.jurusan',
            'penempatan.bidang',
            'kelompokMagang.ketua.profil.jurusan',
            'kelompokMagang.anggota.user.profil.jurusan',
            'pesertaDitugaskan.profil',
            'progresLogs.pelapor',
        ]);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $tugasData,
            ]);
        }

        $user = $request->user();
        if ($tugasData->isKelompok() && $tugasData->kelompok_magang_id) {
            $siblingTugasList = Tugas::with(['kelompokMagang', 'progresLogs.pelapor'])
                ->where('tipe', 'kelompok')
                ->where('kelompok_magang_id', $tugasData->kelompok_magang_id)
                ->oldest('id')
                ->get();
        } else {
            $querySiblings = Tugas::with(['penempatan.peserta.profil', 'progresLogs.pelapor'])
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
        }

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

    /**
     * Halaman Detail Kelompok Bimbingan (Review Individu vs Project Bersama).
     */
    public function kelompokShow(KelompokMagang $kelompok, Request $request): View
    {
        $this->authorizeKelompok($request->user(), $kelompok);

        $kelompok->load(['ketua.profil.jurusan', 'anggota.user.profil.jurusan']);

        // Ambil seluruh user anggota tim (ketua + anggota)
        $memberUsers = collect([$kelompok->ketua])->merge(
            $kelompok->anggota->pluck('user')->filter()
        )->unique('id');

        $memberUserIds = $memberUsers->pluck('id')->all();

        // 1. Tugas Individu masing-masing anggota
        $tugasIndividu = Tugas::where('tipe', 'individu')
            ->whereHas('penempatan', function ($q) use ($memberUserIds) {
                $q->whereIn('peserta_id', $memberUserIds);
            })
            ->with(['penempatan.peserta', 'progresLogs'])
            ->latest()
            ->get();

        // 2. Project Bersama kelompok
        $projectBersama = Tugas::where('tipe', 'kelompok')
            ->where('kelompok_magang_id', $kelompok->id)
            ->with(['pesertaDitugaskan', 'progresLogs'])
            ->latest()
            ->get();

        $activeTab = $request->input('tab', 'individu');

        return view('pembimbing.tugas.kelompok-detail', [
            'kelompok' => $kelompok,
            'memberUsers' => $memberUsers,
            'tugasIndividu' => $tugasIndividu,
            'projectBersama' => $projectBersama,
            'activeTab' => $activeTab,
        ]);
    }

    /**
     * Form pembuatan project bersama baru untuk kelompok.
     */
    public function createProjectBersama(KelompokMagang $kelompok, Request $request): View
    {
        $this->authorizeKelompok($request->user(), $kelompok);

        $kelompok->load(['ketua.profil.jurusan', 'anggota.user.profil.jurusan']);

        $memberUsers = collect([$kelompok->ketua])->merge(
            $kelompok->anggota->pluck('user')->filter()
        )->unique('id');

        return view('pembimbing.tugas.kelompok-project-create', [
            'kelompok' => $kelompok,
            'memberUsers' => $memberUsers,
        ]);
    }

    /**
     * Menyimpan project bersama baru untuk kelompok dengan penugasan spesifik anggota.
     */
    public function storeProjectBersama(KelompokMagang $kelompok, Request $request): RedirectResponse
    {
        $user = $request->user();
        $this->authorizeKelompok($user, $kelompok);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'kategori' => ['required', 'string', 'max:50'],
            'kompleksitas' => ['required', 'in:rendah,sedang,tinggi'],
            'deadline' => ['required', 'date'],
            'link_referensi' => ['nullable', 'string'],
            'peserta_ids' => ['required', 'array', 'min:1'],
            'peserta_ids.*' => ['integer'],
        ], [
            'peserta_ids.required' => 'Paling sedikit satu anggota tim wajib ditugaskan ke dalam project bersama ini.',
            'peserta_ids.min' => 'Paling sedikit satu anggota tim wajib ditugaskan ke dalam project bersama ini.',
        ]);

        $penempatanPerwakilan = Penempatan::where(function ($q) use ($kelompok) {
            $q->where('peserta_id', $kelompok->ketua_user_id)
                ->orWhereIn('peserta_id', $kelompok->anggota()->pluck('user_id'));
        })->first();

        $tugas = Tugas::create([
            'penempatan_id' => $penempatanPerwakilan?->id,
            'tipe' => 'kelompok',
            'kelompok_magang_id' => $kelompok->id,
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'kategori' => $validated['kategori'],
            'kompleksitas' => $validated['kompleksitas'],
            'deadline' => $validated['deadline'],
            'link_referensi' => $validated['link_referensi'] ?? null,
            'status' => StatusTugasEnum::Belum,
            'status_publikasi' => 'dipublikasikan',
        ]);

        foreach ($validated['peserta_ids'] as $pesertaId) {
            $penempatanPeserta = Penempatan::where('peserta_id', $pesertaId)
                ->where('status', StatusPenempatanEnum::Aktif)
                ->first();

            $tugas->tugasPeserta()->create([
                'penempatan_id' => $penempatanPeserta?->id,
                'user_id' => $pesertaId,
            ]);
        }

        return redirect()->route('pembimbing.tugas.kelompok.show', [
            'kelompok' => $kelompok->id,
            'tab' => 'project',
        ])->with('success', 'Project bersama tim berhasil dibuat dan ditugaskan.');
    }

    /**
     * Memperbarui daftar peserta yang ditugaskan dalam project bersama (menambah / menghapus peserta).
     */
    public function updateProjectAssignment(Tugas $tugas, Request $request): RedirectResponse
    {
        Gate::authorize('update', $tugas);

        $validated = $request->validate([
            'peserta_ids' => ['required', 'array', 'min:1'],
            'peserta_ids.*' => ['integer'],
        ], [
            'peserta_ids.required' => 'Paling sedikit satu peserta wajib dilibatkan dalam project bersama.',
        ]);

        $tugas->tugasPeserta()->delete();

        foreach ($validated['peserta_ids'] as $pesertaId) {
            $penempatanPeserta = Penempatan::where('peserta_id', $pesertaId)
                ->where('status', StatusPenempatanEnum::Aktif)
                ->first();

            $tugas->tugasPeserta()->create([
                'penempatan_id' => $penempatanPeserta?->id,
                'user_id' => $pesertaId,
            ]);
        }

        return back()->with('success', 'Daftar penugasan anggota project bersama berhasil disesuaikan.');
    }

    /**
     * Verifikasi kepemilikan bimbingan terhadap kelompok magang.
     */
    protected function authorizeKelompok(User $user, KelompokMagang $kelompok): void
    {
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return;
        }

        $isPembimbing = Penempatan::where('pembimbing_id', $user->id)
            ->where(function ($q) use ($kelompok) {
                $q->where('peserta_id', $kelompok->ketua_user_id)
                    ->orWhereIn('peserta_id', $kelompok->anggota()->pluck('user_id'));
            })
            ->exists();

        if (! $isPembimbing) {
            abort(403, 'Anda tidak memiliki hak akses bimbingan terhadap tim ini.');
        }
    }
}
