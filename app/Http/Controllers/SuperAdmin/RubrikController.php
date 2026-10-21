<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\Rubrik;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RubrikController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar master kriteria rubrik penilaian magang (AC-11-1).
     */
    public function index(): View
    {
        $rubrikList = Rubrik::orderBy('id', 'asc')->get();
        $totalBobotAktif = Rubrik::aktif()->sum('bobot');

        return view('superadmin.rubrik.index', [
            'rubrikList' => $rubrikList,
            'totalBobotAktif' => $totalBobotAktif,
        ]);
    }

    /**
     * Menambahkan kriteria rubrik baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kriteria' => ['required', 'string', 'max:255'],
            'bobot' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $rubrik = Rubrik::create([
            'kriteria' => $validated['kriteria'],
            'bobot' => (int) $validated['bobot'],
            'aktif' => false, // Dibuat nonaktif terlebih dahulu sampai disesuaikan agar total tepat 100
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'rubrik',
            idData: $rubrik->id,
            nilaiBaru: $rubrik->toArray(),
            detail: [
                'aksi' => 'Menambahkan kriteria rubrik penilaian baru (nonaktif)',
                'superadmin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('superadmin.rubrik.index')
            ->with('success', "Kriteria {$rubrik->kriteria} berhasil ditambahkan dalam status nonaktif. Sesuaikan bobot aktif agar total bernilai 100.");
    }

    /**
     * Memperbarui kriteria atau bobot rubrik (AC-10-4).
     */
    public function update(Rubrik $rubrik, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kriteria' => ['required', 'string', 'max:255'],
            'bobot' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $nilaiLama = $rubrik->toArray();
        $bobotBaru = (int) $validated['bobot'];

        // Jika rubrik ini aktif, periksa simulasi total bobot
        if ($rubrik->aktif) {
            $bobotLain = Rubrik::aktif()->where('id', '!=', $rubrik->id)->sum('bobot');
            $simulasiTotal = $bobotLain + $bobotBaru;

            if ($simulasiTotal !== 100) {
                return back()->withInput()->with('error', "Perubahan ditolak (AC-10-4): Total bobot seluruh kriteria aktif harus tepat 100% (simulasi: {$simulasiTotal}%).");
            }
        }

        $rubrik->update([
            'kriteria' => $validated['kriteria'],
            'bobot' => $bobotBaru,
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'rubrik',
            idData: $rubrik->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $rubrik->toArray(),
            detail: [
                'aksi' => 'Memperbarui kriteria rubrik penilaian',
                'superadmin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('superadmin.rubrik.index')
            ->with('success', "Kriteria rubrik {$rubrik->kriteria} berhasil diperbarui.");
    }

    /**
     * Mengaktifkan/menonaktifkan kriteria rubrik (AC-10-4).
     */
    public function toggleAktif(Rubrik $rubrik, Request $request): RedirectResponse
    {
        $targetAktif = ! $rubrik->aktif;

        // Hitung simulasi total jika status berubah
        if ($targetAktif) {
            $totalSekarang = Rubrik::aktif()->sum('bobot');
            $simulasiTotal = $totalSekarang + $rubrik->bobot;
        } else {
            $totalSekarang = Rubrik::aktif()->sum('bobot');
            $simulasiTotal = $totalSekarang - $rubrik->bobot;
        }

        if ($simulasiTotal !== 100) {
            return back()->with('error', "Perubahan ditolak (AC-10-4): Total bobot seluruh rubrik aktif harus tepat 100% (simulasi: {$simulasiTotal}%).");
        }

        $nilaiLama = $rubrik->toArray();
        $rubrik->update(['aktif' => $targetAktif]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'rubrik',
            idData: $rubrik->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $rubrik->toArray(),
            detail: [
                'aksi' => $targetAktif ? 'Mengaktifkan kriteria rubrik' : 'Menonaktifkan kriteria rubrik',
                'superadmin_id' => $request->user()->id,
            ]
        );

        $statusLabel = $targetAktif ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('superadmin.rubrik.index')
            ->with('success', "Kriteria rubrik {$rubrik->kriteria} berhasil {$statusLabel}.");
    }
}
