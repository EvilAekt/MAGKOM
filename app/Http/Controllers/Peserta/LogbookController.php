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
     * Menampilkan daftar riwayat logbook harian peserta.
     */
    public function index(Request $request): mixed
    {
        $user = $request->user();

        $penempatan = Penempatan::with(['pembimbing', 'bidang'])
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

        return view('peserta.logbook.index', [
            'penempatan'        => $penempatan,
            'logbooks'          => $logbooks,
            'logbookMaksMundur' => $logbookMaksMundur,
        ]);
    }

    /**
     * Mengisi catatan logbook kegiatan harian baru (AC-09).
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

        // Validasi keunikan penempatan_id dan tanggal kegiatan
        $sudahAda = Logbook::where('penempatan_id', $penempatan->id)
            ->whereDate('tanggal', $request->validated('tanggal'))
            ->exists();

        if ($sudahAda) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Catatan logbook kegiatan untuk tanggal tersebut sudah pernah dicatat sebelumnya.',
                ], 422);
            }
            return redirect()->route('peserta.logbook.index')
                ->with('error', 'Catatan logbook kegiatan untuk tanggal tersebut sudah pernah dicatat sebelumnya.');
        }

        $filePath = null;
        if ($request->hasFile('file_laporan')) {
            $filePath = $request->file('file_laporan')->store("logbook/{$penempatan->id}", 'private');
        }

        $logbook = Logbook::create([
            'penempatan_id' => $penempatan->id,
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
                'tanggal' => $logbook->tanggal->toDateString(),
            ],
            detail: ['aksi' => 'isi_logbook']
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catatan kegiatan harian logbook berhasil disimpan.',
                'data' => $logbook,
            ], 201);
        }

        return redirect()->route('peserta.logbook.index')
            ->with('success', 'Catatan logbook kegiatan harian berhasil disimpan.');
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

        $nilaiLama = $logbook->only(['kegiatan', 'file_laporan', 'link_laporan', 'status']);

        $logbook->update([
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
            nilaiBaru: $logbook->only(['kegiatan', 'file_laporan', 'link_laporan', 'status']),
            detail: ['aksi' => 'update_logbook']
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catatan logbook berhasil diperbarui.',
                'data' => $logbook,
            ]);
        }

        return redirect()->route('peserta.logbook.index')
            ->with('success', 'Catatan logbook berhasil diperbarui.');
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
