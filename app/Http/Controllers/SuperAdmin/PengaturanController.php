<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar konfigurasi sistem (AC-11-7, PRD 8.6).
     */
    public function index(): View
    {
        $pengaturan = Pengaturan::all()->pluck('nilai', 'kunci');

        $predikatBatasRaw = $pengaturan['predikat_batas'] ?? null;
        $predikatBatas = $predikatBatasRaw ? json_decode($predikatBatasRaw, true) : null;
        if (! is_array($predikatBatas)) {
            $predikatBatas = [
                'A' => 85,
                'B' => 70,
                'C' => 55,
                'D' => 40,
            ];
        }

        return view('superadmin.pengaturan.index', [
            'pengaturan'    => $pengaturan,
            'predikatBatas' => $predikatBatas,
        ]);
    }

    /**
     * Memperbarui parameter pengaturan sistem (AC-11-7, PRD 8.6).
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'masa_tenggang_bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'kuota_umum_maks_per_bulan' => ['required', 'integer', 'min:1', 'max:100'],
            'slot_khusus_default' => ['nullable', 'integer', 'min:0', 'max:50'],
            'maks_peserta_aktif_per_pembimbing' => ['nullable', 'integer', 'min:1', 'max:50'],
            'logbook_maks_mundur_hari' => ['required', 'integer', 'min:1', 'max:30'],
            'berkas_maks_mb' => ['required', 'integer', 'min:1', 'max:20'],
            'deadline_mendekati_hari' => ['required', 'integer', 'min:1', 'max:14'],
            'batas_predikat_a' => ['nullable', 'integer', 'min:1', 'max:100'],
            'batas_predikat_b' => ['nullable', 'integer', 'min:1', 'max:100'],
            'batas_predikat_c' => ['nullable', 'integer', 'min:1', 'max:100'],
            'batas_predikat_d' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $settingsToSave = [
            'masa_tenggang_bulan' => (string) $validated['masa_tenggang_bulan'],
            'kuota_umum_maks_per_bulan' => (string) $validated['kuota_umum_maks_per_bulan'],
            'maks_peserta_aktif_per_pembimbing' => $validated['maks_peserta_aktif_per_pembimbing'] !== null ? (string) $validated['maks_peserta_aktif_per_pembimbing'] : null,
            'logbook_maks_mundur_hari' => (string) $validated['logbook_maks_mundur_hari'],
            'berkas_maks_mb' => (string) $validated['berkas_maks_mb'],
            'deadline_mendekati_hari' => (string) $validated['deadline_mendekati_hari'],
        ];

        if (isset($validated['slot_khusus_default'])) {
            $settingsToSave['slot_khusus_default'] = (string) $validated['slot_khusus_default'];
        }

        if (isset($validated['batas_predikat_a'], $validated['batas_predikat_b'], $validated['batas_predikat_c'], $validated['batas_predikat_d'])) {
            $limA = (int) $validated['batas_predikat_a'];
            $limB = (int) $validated['batas_predikat_b'];
            $limC = (int) $validated['batas_predikat_c'];
            $limD = (int) $validated['batas_predikat_d'];

            if (! ($limA > $limB && $limB > $limC && $limC > $limD)) {
                return back()->withInput()->withErrors([
                    'batas_predikat_a' => 'Skala batas ambang predikat harus berurutan secara logis: A > B > C > D.',
                ]);
            }

            $settingsToSave['predikat_batas'] = json_encode([
                'A' => $limA,
                'B' => $limB,
                'C' => $limC,
                'D' => $limD,
            ]);
        }

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
                'aksi' => 'Memperbarui parameter konfigurasi sistem (AC-11-7)',
                'superadmin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('superadmin.pengaturan.index')
            ->with('success', 'Konfigurasi sistem berhasil diperbarui.');
    }
}
