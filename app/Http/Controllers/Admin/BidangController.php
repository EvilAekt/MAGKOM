<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\BidangRequest;
use App\Models\Bidang;
use App\Models\Penempatan;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BidangController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Menampilkan daftar master bidang kerja dinas Kominfo (AC-11-1).
     */
    public function index(Request $request): View
    {
        $query = Bidang::withCount('users');

        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_bidang', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi', 'like', "%{$keyword}%");
            });
        }

        if ($request->has('status') && $request->input('status') !== '' && $request->input('status') !== null) {
            $statusValue = $request->input('status');
            if ($statusValue === '1' || $statusValue === 'aktif' || $statusValue === 'true') {
                $query->where('aktif', true);
            } elseif ($statusValue === '0' || $statusValue === 'nonaktif' || $statusValue === 'false') {
                $query->where('aktif', false);
            }
        }

        $bidangList = $query->orderBy('nama_bidang', 'asc')->paginate(10)->withQueryString();

        $totalBidang = Bidang::count();
        $bidangAktif = Bidang::where('aktif', true)->count();
        $bidangNonaktif = Bidang::where('aktif', false)->count();
        $totalPembimbing = User::where('role', RoleEnum::Pembimbing->value)->whereNotNull('bidang_id')->count();

        return view('admin.bidang.index', [
            'bidangList' => $bidangList,
            'totalBidang' => $totalBidang,
            'bidangAktif' => $bidangAktif,
            'bidangNonaktif' => $bidangNonaktif,
            'totalPembimbing' => $totalPembimbing,
        ]);
    }

    /**
     * Menyimpan master bidang baru.
     */
    public function store(BidangRequest $request): RedirectResponse
    {
        $validated = $request->validated();

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
                'admin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('admin.bidang.index')
            ->with('success', "Bidang {$bidang->nama_bidang} berhasil ditambahkan.");
    }

    /**
     * Memperbarui data bidang.
     */
    public function update(Bidang $bidang, BidangRequest $request): RedirectResponse
    {
        $validated = $request->validated();

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
                'admin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('admin.bidang.index')
            ->with('success', "Data bidang {$bidang->nama_bidang} berhasil diperbarui.");
    }

    /**
     * Mengaktifkan/menonaktifkan bidang.
     */
    public function toggleAktif(Bidang $bidang, Request $request): RedirectResponse
    {
        abort_if(! $request->user()?->isAdmin(), 403, 'Aksi ini hanya dapat dilakukan oleh Administrator.');

        $targetAktif = ! $bidang->aktif;

        // Penegakan Aturan Bisnis BR-12 jika hendak menonaktifkan bidang
        if (! $targetAktif) {
            $hasActiveStaf = User::where('bidang_id', $bidang->id)->where('status_aktif', true)->exists();
            if ($hasActiveStaf) {
                return back()->with('error', 'Penonaktifan ditolak: Masih terdapat staf aktif yang bertugas di bidang dinas ini.');
            }

            $hasActivePenempatan = Penempatan::where('bidang_id', $bidang->id)
                ->where('status', StatusPenempatanEnum::Aktif)
                ->exists();
            if ($hasActivePenempatan) {
                return back()->with('error', 'Penonaktifan ditolak: Masih terdapat peserta magang aktif yang ditempatkan di bidang dinas ini.');
            }
        }

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
                'admin_id' => $request->user()->id,
            ]
        );

        $statusLabel = $targetAktif ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.bidang.index')
            ->with('success', "Bidang {$bidang->nama_bidang} berhasil {$statusLabel}.");
    }
}
