<?php

declare(strict_types=1);

namespace App\Http\Controllers\Peserta;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\Penempatan;
use App\Models\SurveiKepuasan;
use App\Services\AuditService;
use App\Support\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SurveiController extends Controller
{
    /**
     * Tampilkan formulir survei evaluasi mutu magang (PRD 4.3 Poin 4).
     */
    public function show(Request $request): View|RedirectResponse
    {
        if (! Pengaturan::bool('fitur_survei_aktif', true)) {
            abort(404, 'Fitur survei kepuasan saat ini dinonaktifkan oleh administrator.');
        }

        $user = $request->user();
        $penempatan = Penempatan::with(['pembimbing', 'bidang'])
            ->where('peserta_id', $user->id)
            ->latest()
            ->first();

        if (! $penempatan) {
            return redirect()->route('peserta.dashboard')
                ->with('error', 'Anda belum memiliki riwayat penempatan magang untuk mengisi survei.');
        }

        $existingSurvei = SurveiKepuasan::where('penempatan_id', $penempatan->id)->first();

        return view('peserta.survei.index', [
            'penempatan' => $penempatan,
            'survei' => $existingSurvei,
        ]);
    }

    /**
     * Simpan kuesioner evaluasi dan penilaian NPS peserta.
     */
    public function store(Request $request, AuditService $auditService): RedirectResponse
    {
        if (! Pengaturan::bool('fitur_survei_aktif', true)) {
            abort(404, 'Fitur survei kepuasan saat ini dinonaktifkan oleh administrator.');
        }

        $user = $request->user();
        $penempatan = Penempatan::where('peserta_id', $user->id)
            ->latest()
            ->firstOrFail();

        $validated = $request->validate([
            'skor_mentoring' => ['required', 'integer', 'between:1,5'],
            'skor_tugas' => ['required', 'integer', 'between:1,5'],
            'skor_fasilitas' => ['required', 'integer', 'between:1,5'],
            'skor_nps' => ['required', 'integer', 'between:0,10'],
            'umpan_balik' => ['nullable', 'string', 'max:1000'],
            'saran' => ['nullable', 'string', 'max:1000'],
        ], [
            'skor_mentoring.required' => 'Penilaian bimbingan mentor wajib dipilih.',
            'skor_tugas.required' => 'Penilaian relevansi tugas wajib dipilih.',
            'skor_fasilitas.required' => 'Penilaian fasilitas dinas wajib dipilih.',
            'skor_nps.required' => 'Skor Net Promoter Score (rekomendasi) wajib dipilih.',
        ]);

        $survei = SurveiKepuasan::updateOrCreate(
            ['penempatan_id' => $penempatan->id],
            $validated
        );

        $auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'survei_kepuasan',
            idData: $survei->id,
            nilaiLama: null,
            nilaiBaru: $validated,
            detail: [
                'aksi' => 'submit_survei_kepuasan_magang',
                'peserta_id' => $user->id,
                'penempatan_id' => $penempatan->id,
            ]
        );

        return redirect()->route('peserta.survei.show')
            ->with('success', 'Terima kasih! Umpan balik evaluasi magang Anda berhasil disimpan dan sangat bermanfaat bagi peningkatan mutu dinas.');
    }
}
