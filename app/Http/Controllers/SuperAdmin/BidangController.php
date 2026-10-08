<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BidangController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar master bidang kerja dinas Kominfo (AC-11-1).
     */
    public function index(): View
    {
        $bidangList = Bidang::withCount('users')->orderBy('nama_bidang', 'asc')->paginate(20);

        return view('superadmin.bidang.index', [
            'bidangList' => $bidangList,
        ]);
    }

    /**
     * Menyimpan master bidang baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_bidang' => ['required', 'string', 'max:255', 'unique:bidang,nama_bidang'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ]);

        $bidang = Bidang::create([
            'nama_bidang' => $validated['nama_bidang'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'aktif' => true,
        ]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'bidang',
            idData: $bidang->id,
            nilaiBaru: $bidang->toArray(),
            detail: [
                'aksi' => 'Menambahkan master data bidang dinas baru',
                'superadmin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('superadmin.bidang.index')
            ->with('success', "Bidang {$bidang->nama_bidang} berhasil ditambahkan.");
    }

    /**
     * Memperbarui data bidang.
     */
    public function update(Bidang $bidang, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_bidang' => ['required', 'string', 'max:255', Rule::unique('bidang', 'nama_bidang')->ignore($bidang->id)],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ]);

        $nilaiLama = $bidang->toArray();
        $bidang->update($validated);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'bidang',
            idData: $bidang->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $bidang->toArray(),
            detail: [
                'aksi' => 'Memperbarui master data bidang dinas',
                'superadmin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('superadmin.bidang.index')
            ->with('success', "Data bidang {$bidang->nama_bidang} berhasil diperbarui.");
    }

    /**
     * Mengaktifkan/menonaktifkan bidang.
     */
    public function toggleAktif(Bidang $bidang, Request $request): RedirectResponse
    {
        $targetAktif = ! $bidang->aktif;
        $nilaiLama = $bidang->toArray();
        $bidang->update(['aktif' => $targetAktif]);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'bidang',
            idData: $bidang->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $bidang->toArray(),
            detail: [
                'aksi' => $targetAktif ? 'Mengaktifkan bidang dinas' : 'Menonaktifkan bidang dinas',
                'superadmin_id' => $request->user()->id,
            ]
        );

        $statusLabel = $targetAktif ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('superadmin.bidang.index')
            ->with('success', "Bidang {$bidang->nama_bidang} berhasil {$statusLabel}.");
    }
}
