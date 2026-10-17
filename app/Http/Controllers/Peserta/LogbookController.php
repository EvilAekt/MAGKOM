<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\LogbookRequest;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Services\AuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LogbookController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar riwayat logbook mingguan peserta.
     */
    public function index(Request $request): mixed
    {
        $user = $request->user();

        $penempatan = Penempatan::with(['pembimbing', 'bidang', 'logbooks'])
            ->where('peserta_id', $user->id)
            ->where('status', StatusPenempatanEnum::Aktif)
            ->first();

        $logbooks = Logbook::whereHas('penempatan', function ($q) use ($user) {
            $q->where('peserta_id', $user->id);
        })
            ->latest('tanggal')
            ->paginate(20);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $logbooks,
            ]);
        }

        $logbookMaksMundur = \App\Support\Pengaturan::int('logbook_maks_mundur_hari', 3);

        $daftarMinggu = $penempatan ? $penempatan->getDaftarMingguMagang() : [];
        $totalMinggu = $penempatan ? $penempatan->total_minggu : 12;
        $mingguAktif = $penempatan ? $penempatan->minggu_aktif : 1;
        $totalTerisi = $penempatan ? $penempatan->logbooks()->count() : 0;
        $totalTerkirim = $penempatan ? $penempatan->logbooks()->where('status', StatusLogbookEnum::Terkirim)->count() : 0;
        $persentaseTerisi = $penempatan && $totalMinggu > 0 ? (int) round(($totalTerisi / $totalMinggu) * 100) : 0;
        $mingguAktifSudahDiisi = $penempatan ? $penempatan->logbooks()->where('minggu_ke', $mingguAktif)->exists() : false;

        return view('peserta.logbook.index', [
            'penempatan'            => $penempatan,
            'logbooks'              => $logbooks,
            'logbookMaksMundur'     => $logbookMaksMundur,
            'daftarMinggu'          => $daftarMinggu,
            'totalMinggu'           => $totalMinggu,
            'mingguAktif'           => $mingguAktif,
            'totalTerisi'           => $totalTerisi,
            'totalTerkirim'         => $totalTerkirim,
            'persentaseTerisi'      => $persentaseTerisi,
            'mingguAktifSudahDiisi' => $mingguAktifSudahDiisi,
        ]);
    }

    /**
     * Mengisi catatan logbook mingguan baru.
     */
    public function store(LogbookRequest $request): mixed
    {
        $user = $request->user();

        $penempatan = Penempatan::where('peserta_id', $user->id)
            ->where('status', StatusPenempatanEnum::Aktif)
            ->first();

        if (! $penempatan) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki status penempatan magang aktif.',
                ], 422);
            }
            return redirect()->route('peserta.logbook.index')
                ->with('error', 'Anda tidak memiliki status penempatan magang aktif.');
        }

        Gate::authorize('create', [Logbook::class, $penempatan]);

        $mingguKe = $request->validated('minggu_ke');
        if (! $mingguKe && $penempatan->tgl_mulai) {
            $tglInput = \Carbon\Carbon::parse($request->validated('tanggal'));
            $diffDays = $penempatan->tgl_mulai->diffInDays($tglInput);
            $mingguKe = (int) floor($diffDays / 7) + 1;
        }

        $tglAwalMinggu = null;
        $tglAkhirMinggu = null;
        if ($mingguKe && $penempatan->tgl_mulai) {
            $tglAwalMinggu = $penempatan->tgl_mulai->copy()->startOfDay()->addDays(($mingguKe - 1) * 7);
            $tglAkhirMinggu = $tglAwalMinggu->copy()->addDays(6)->endOfDay();
        }

        // Validasi keunikan penempatan_id dan tanggal kegiatan atau minggu_ke
        $cekQuery = Logbook::where('penempatan_id', $penempatan->id)
            ->where(function ($q) use ($request, $mingguKe, $tglAwalMinggu, $tglAkhirMinggu) {
                $q->whereDate('tanggal', $request->validated('tanggal'));
                if ($mingguKe) {
                    $q->orWhere('minggu_ke', $mingguKe);
                    if ($tglAwalMinggu && $tglAkhirMinggu) {
                        $q->orWhereBetween('tanggal', [$tglAwalMinggu->toDateString(), $tglAkhirMinggu->toDateString()]);
                    }
                }
            });

        if ($cekQuery->exists()) {
            $pesan = $mingguKe
                ? "Catatan logbook kegiatan untuk Minggu ke-{$mingguKe} atau tanggal tersebut sudah pernah dicatat sebelumnya."
                : 'Catatan logbook kegiatan untuk tanggal tersebut sudah pernah dicatat sebelumnya.';

            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $pesan,
                ], 422);
            }
            return redirect()->route('peserta.logbook.index')
                ->with('error', $pesan);
        }

        $filePath = null;
        if ($request->hasFile('file_laporan')) {
            $filePath = $request->file('file_laporan')->store("logbook/{$penempatan->id}", 'private');
        }

        $logbook = Logbook::create([
            'penempatan_id' => $penempatan->id,
            'minggu_ke' => $mingguKe,
            'tanggal' => $request->validated('tanggal'),
            'kegiatan' => $request->validated('kegiatan'),
            'file_laporan' => $filePath,
            'link_laporan' => $request->validated('link_laporan'),
            'status' => StatusLogbookEnum::Terkirim,
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'logbook',
            idData: $logbook->id,
            nilaiLama: null,
            nilaiBaru: [
                'penempatan_id' => $penempatan->id,
                'minggu_ke' => $logbook->minggu_ke,
                'tanggal' => $logbook->tanggal->toDateString(),
            ],
            detail: ['aksi' => 'isi_logbook_mingguan']
        );

        // Notifikasi Real-time SSE ke Pembimbing (TASK-SSE-06)
        if ($penempatan->pembimbing_id) {
            app(\App\Services\RealtimeEventService::class)->publish(
                \App\Enums\RealtimeEventTypeEnum::LogbookBaru,
                [
                    'title' => 'Setoran Logbook Baru',
                    'message' => "{$user->nama} menyerahkan catatan logbook baru.",
                    'action_url' => route('pembimbing.dashboard'),
                ],
                $penempatan->pembimbing_id
            );
        }

        if ($request->expectsJson()) {

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
    public function update(LogbookRequest $request, Logbook $logbook): mixed
    {
        Gate::authorize('update', $logbook);

        $filePath = $logbook->file_laporan;
        if ($request->hasFile('file_laporan')) {
            $filePath = $request->file('file_laporan')->store("logbook/{$logbook->penempatan_id}", 'private');
        }

        $mingguKe = $request->validated('minggu_ke') ?? $logbook->minggu_ke;
        $nilaiLama = $logbook->only(['minggu_ke', 'kegiatan', 'file_laporan', 'link_laporan', 'status']);

        $logbook->update([
            'minggu_ke' => $mingguKe,
            'kegiatan' => $request->validated('kegiatan'),
            'file_laporan' => $filePath,
            'link_laporan' => $request->validated('link_laporan'),
            'status' => StatusLogbookEnum::Terkirim, // Reset ke status terkirim setelah diedit
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'logbook',
            idData: $logbook->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $logbook->only(['minggu_ke', 'kegiatan', 'file_laporan', 'link_laporan', 'status']),
            detail: ['aksi' => 'update_logbook_mingguan']
        );

        if ($request->expectsJson()) {
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
    public function unduhLampiran(Logbook $logbook)
    {
        Gate::authorize('view', $logbook);

        if (! $logbook->file_laporan || ! \Illuminate\Support\Facades\Storage::disk('private')->exists($logbook->file_laporan)) {
            abort(404, 'Berkas lampiran logbook tidak ditemukan.');
        }

        return \Illuminate\Support\Facades\Storage::disk('private')->response(
            $logbook->file_laporan,
            "lampiran-logbook-{$logbook->tanggal->toDateString()}.pdf",
            ['Content-Type' => 'application/pdf']
        );
    }
}
