<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Enums\RealtimeEventTypeEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewProgresRequest;
use App\Http\Requests\TugasRequest;
use App\Models\Penempatan;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Models\TugasTemplate;
use App\Services\AuditService;
use App\Services\RealtimeEventService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            if ($request->input('status') === 'perlu_review') {
                $query->whereHas('progresLogs', function ($qp) {
                    $qp->where('status_review', 'menunggu');
                });
            } else {
                $query->where('status', $request->input('status'));
            }
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->input('kategori'));
        }

        if ($request->filled('q')) {
            $search = '%'.$request->input('q').'%';
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', $search)
                    ->orWhere('deskripsi', 'like', $search)
                    ->orWhereHas('penempatan.peserta', function ($qp) use ($search) {
                        $qp->where('nama', 'like', $search);
                    });
            });
        }

        $tugasList = $query->latest()->paginate(10)->withQueryString();

        // Ambil daftar penempatan mentee aktif lengkap dengan tugas dan progres untuk stream mentee (10 per halaman)
        $penempatanBaseQuery = Penempatan::with([
            'peserta.profil.jurusan',
            'bidang',
            'tugas' => function ($q) {
                $q->with('progresLogs')->latest();
            },
        ])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            })
            ->where('status', StatusPenempatanEnum::Aktif);

        $penempatanList = (clone $penempatanBaseQuery)->latest('id')->paginate(10)->withQueryString();
        $allPenempatan = (clone $penempatanBaseQuery)->latest('id')->get();

        // Hitung metrik agregat status tugas untuk banner dan tab classroom
        $baseMetricsQuery = Tugas::whereHas('penempatan', function ($q) use ($user) {
            if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                $q->where('pembimbing_id', $user->id);
            }
        });

        if ($request->filled('penempatan_id')) {
            $baseMetricsQuery->where('penempatan_id', $request->input('penempatan_id'));
        }

        $counts = [
            'total' => (clone $baseMetricsQuery)->count(),
            'belum' => (clone $baseMetricsQuery)->where('status', StatusTugasEnum::Belum)->count(),
            'proses' => (clone $baseMetricsQuery)->where('status', StatusTugasEnum::Proses)->count(),
            'selesai' => (clone $baseMetricsQuery)->where('status', StatusTugasEnum::Selesai)->count(),
            'perlu_review' => (clone $baseMetricsQuery)->whereHas('progresLogs', function ($qp) {
                $qp->where('status_review', 'menunggu');
            })->count(),
        ];

        $selectedPenempatanId = $request->integer('penempatan_id') ?: null;
        $selectedPenempatan = null;
        if ($selectedPenempatanId) {
            $selectedPenempatan = $penempatanList->firstWhere('id', $selectedPenempatanId);
            if (! $selectedPenempatan) {
                $selectedPenempatan = (clone $penempatanBaseQuery)->firstWhere('id', $selectedPenempatanId);
            }
        }

        $data = [
            'tugasList' => $tugasList,
            'penempatanList' => $penempatanList,
            'allPenempatan' => $allPenempatan,
            'counts' => $counts,
            'selectedPenempatanId' => $selectedPenempatanId,
            'selectedPenempatan' => $selectedPenempatan,
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

        // Penegakan batasan hak akses & isolasi SuperAdmin (AC-08-5) untuk setiap mentee
        foreach ($penempatans as $penempatan) {
            Gate::authorize('create', [Tugas::class, $penempatan]);
        }

        // Tentukan aksi dan status publikasi
        $aksiPublikasi = $request->input('aksi_publikasi', 'sekarang');
        $statusPublikasi = match ($aksiPublikasi) {
            'terjadwal' => 'terjadwal',
            'draft' => 'draft',
            default => 'dipublikasikan',
        };

        $jadwalPublikasi = null;
        if ($statusPublikasi === 'terjadwal') {
            $tglPub = $request->input('tgl_publikasi');
            $wktPub = $request->input('waktu_publikasi') ?: '08:00';
            $jadwalPublikasi = Carbon::parse("{$tglPub} {$wktPub}", 'Asia/Jakarta');
        }

        // Penanganan lampiran fisik (disimpan 1 kali untuk seluruh batch penugasan)
        $batchId = (string) Str::uuid();
        $filePath = null;
        $fileName = null;
        $fileSize = null;

        if ($request->hasFile('file_lampiran')) {
            $file = $request->file('file_lampiran');
            $filePath = $file->store("tugas/{$batchId}", 'private');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
        }

        $createdTugasList = [];

        DB::transaction(function () use (
            $penempatans,
            $batchId,
            $request,
            $filePath,
            $fileName,
            $fileSize,
            $statusPublikasi,
            $jadwalPublikasi,
            $aksiPublikasi,
            &$createdTugasList
        ) {
            foreach ($penempatans as $penempatan) {
                $tugas = Tugas::create([
                    'batch_id' => $batchId,
                    'penempatan_id' => $penempatan->id,
                    'judul' => $request->validated('judul'),
                    'deskripsi' => $request->validated('deskripsi'),
                    'link_referensi' => $request->validated('link_referensi'),
                    'file_lampiran' => $filePath,
                    'nama_file_lampiran' => $fileName,
                    'ukuran_file_lampiran' => $fileSize,
                    'kategori' => $request->validated('kategori'),
                    'kompleksitas' => $request->validated('kompleksitas'),
                    'deadline' => $request->validated('deadline'),
                    'status' => StatusTugasEnum::Belum,
                    'status_publikasi' => $statusPublikasi,
                    'jadwal_publikasi' => $jadwalPublikasi,
                ]);

                $this->auditService->catat(
                    tipe: TipeAuditEnum::Aksi,
                    tabel: 'tugas',
                    idData: $tugas->id,
                    nilaiLama: null,
                    nilaiBaru: $tugas->only([
                        'batch_id',
                        'judul',
                        'kategori',
                        'kompleksitas',
                        'deadline',
                        'link_referensi',
                        'nama_file_lampiran',
                        'status_publikasi',
                        'jadwal_publikasi',
                    ]),
                    detail: ['aksi' => 'buat_tugas', 'aksi_publikasi' => $aksiPublikasi]
                );

                // Notifikasi Real-time SSE ke Peserta Mentee (jika langsung dipublikasikan)
                if ($statusPublikasi === 'dipublikasikan' && $penempatan->peserta_id) {
                    app(RealtimeEventService::class)->publish(
                        RealtimeEventTypeEnum::TugasBaru,
                        [
                            'title' => 'Tugas Magang Baru',
                            'message' => "Pembimbing memberikan tugas: {$tugas->judul}.",
                            'deadline' => $tugas->deadline?->format('d/m/Y H:i'),
                            'action_url' => route('peserta.tugas.index'),
                        ],
                        $penempatan->peserta_id
                    );
                }

                $createdTugasList[] = $tugas;
            }
        });

        $count = count($createdTugasList);
        $message = match ($statusPublikasi) {
            'terjadwal' => "Tugas berhasil dijadwalkan untuk {$count} mentee pada ".$jadwalPublikasi?->format('d/m/Y H:i').' WIB.',
            'draft' => "Draf tugas untuk {$count} mentee berhasil disimpan.",
            default => "Tugas baru berhasil diberikan kepada {$count} mentee.",
        };

        if (! $request->expectsJson() && ! $request->wantsJson()) {
            return redirect()->route('pembimbing.tugas.index')
                ->with('success', $message);
        }

        $primaryTugas = ! empty($createdTugasList) ? $createdTugasList[0]->load('penempatan.peserta') : null;

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $primaryTugas,
            'items' => collect($createdTugasList)->map(fn ($t) => $t->load('penempatan.peserta')),
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
        Gate::authorize('update', $tugas);

        if ($tugas->status === StatusTugasEnum::Selesai) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tugas yang telah berstatus selesai tidak dapat diedit kembali.',
                ], 422);
            }

            return back()->withErrors(['error' => 'Tugas yang telah berstatus selesai tidak dapat diedit kembali.']);
        }

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

        // Notifikasi Real-time SSE ke Peserta (Progres Direview / Catatan Revisi)
        $pesertaId = $tugas->penempatan?->peserta_id;
        if ($pesertaId) {
            $isRevisi = $progres->status_review === StatusReviewProgresEnum::PerluRevisi;
            $statusLabel = $isRevisi ? 'Perlu Revisi' : 'Diterima';
            $pesan = $isRevisi && filled($progres->feedback)
                ? "Pembimbing meminta revisi untuk tugas \"{$tugas->judul}\": {$progres->feedback}"
                : "Laporan progres untuk tugas \"{$tugas->judul}\" telah diperiksa oleh pembimbing ({$statusLabel}).";

            app(RealtimeEventService::class)->publish(
                RealtimeEventTypeEnum::ProgresDireviu,
                [
                    'title' => "Evaluasi Progres Tugas: {$statusLabel}",
                    'message' => $pesan,
                    'status_review' => $progres->status_review->value,
                    'feedback' => $progres->feedback,
                    'tugas_id' => $tugas->id,
                    'action_url' => route('peserta.tugas.show', $tugas->id),
                    'badge_target' => $isRevisi ? 'tugas-revisi-badge' : null,
                ],
                $pesertaId
            );
        }

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
