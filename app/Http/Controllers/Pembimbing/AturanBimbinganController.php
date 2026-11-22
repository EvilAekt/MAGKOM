<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pembimbing;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAturanBimbinganRequest;
use App\Models\Pengaturan;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AturanBimbinganController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan halaman konfigurasi aturan pelaksanaan, tugas, dan dokumen bimbingan.
     */
    public function index(): View
    {
        $kunciAturan = [
            'berkas_maks_mb',
            'deadline_mendekati_hari',
        ];

        $pengaturan = Pengaturan::whereIn('kunci', $kunciAturan)->pluck('nilai', 'kunci');

        return view('pembimbing.aturan.index', [
            'pengaturan' => $pengaturan,
            'berkasMaksMb' => (int) ($pengaturan['berkas_maks_mb'] ?? 10),
            'deadlineMendekatiHari' => (int) ($pengaturan['deadline_mendekati_hari'] ?? 3),
        ]);
    }

    /**
     * Memperbarui parameter aturan pelaksanaan dokumen dan tugas oleh Pembimbing.
     */
    public function update(UpdateAturanBimbinganRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $settingsToSave = [
            'berkas_maks_mb' => (string) $validated['berkas_maks_mb'],
            'deadline_mendekati_hari' => (string) $validated['deadline_mendekati_hari'],
        ];

        DB::transaction(function () use ($settingsToSave, $request) {
            $nilaiLama = [];
            $nilaiBaru = [];

            foreach ($settingsToSave as $kunci => $nilai) {
                $record = Pengaturan::firstOrNew(['kunci' => $kunci]);
                $nilaiLama[$kunci] = $record->nilai;

                $record->nilai = $nilai;
                $record->save();

                $nilaiBaru[$kunci] = $record->nilai;
            }

            Pengaturan::clearCache();

            $this->auditService->catat(
                tipe: TipeAuditEnum::Aksi,
                tabel: 'pengaturan',
                idData: 0,
                nilaiLama: $nilaiLama,
                nilaiBaru: $nilaiBaru,
                detail: [
                    'aksi' => 'Memperbarui aturan pelaksanaan dokumen dan tenggat tugas oleh Pembimbing',
                    'pembimbing_id' => $request->user()?->id,
                ],
                channel: 'settings',
                severity: 'info'
            );
        });

        return redirect()->route('pembimbing.aturan.index')
            ->with('success', 'Aturan pelaksanaan dokumen dan tenggat tugas bimbingan berhasil diperbarui.');
    }
}
