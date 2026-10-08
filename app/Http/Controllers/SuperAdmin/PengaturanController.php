<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar konfigurasi sistem (AC-11-7).
     */
    public function index(): View
    {
        $pengaturan = Pengaturan::all()->pluck('nilai', 'kunci');

        return view('superadmin.pengaturan.index', [
            'pengaturan' => $pengaturan,
        ]);
    }

    /**
     * Memperbarui parameter pengaturan sistem (AC-11-7).
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'masa_tenggang_bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'kuota_umum_maks_per_bulan' => ['required', 'integer', 'min:1', 'max:100'],
            'slot_khusus_default' => ['required', 'integer', 'min:0', 'max:50'],
            'maks_peserta_aktif_per_pembimbing' => ['nullable', 'integer', 'min:1', 'max:50'],
            'logbook_maks_mundur_hari' => ['required', 'integer', 'min:1', 'max:30'],
            'berkas_maks_mb' => ['required', 'integer', 'min:1', 'max:20'],
            'deadline_mendekati_hari' => ['required', 'integer', 'min:1', 'max:14'],
        ]);

        $nilaiLama = [];
        $nilaiBaru = [];

        foreach ($validated as $kunci => $nilai) {
            $record = Pengaturan::firstOrNew(['kunci' => $kunci]);
            $nilaiLama[$kunci] = $record->nilai;

            $record->nilai = $nilai !== null ? (string) $nilai : null;
            $record->save();

            $nilaiBaru[$kunci] = $record->nilai;
        }

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'pengaturan',
            idData: 0,
            nilaiLama: $nilaiLama,
            nilaiBaru: $nilaiBaru,
            detail: [
                'aksi' => 'Memperbarui parameter konfigurasi sistem',
                'superadmin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('superadmin.pengaturan.index')
            ->with('success', 'Konfigurasi sistem berhasil diperbarui.');
    }
}
