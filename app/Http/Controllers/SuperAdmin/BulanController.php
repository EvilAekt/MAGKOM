<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\StatusKetersediaanEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\BulanMagangRequest;
use App\Models\BulanMagang;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BulanController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function index(): View
    {
        $bulanList = BulanMagang::orderBy('bulan_mulai', 'desc')->paginate(12);

        return view('superadmin.bulan.index', [
            'bulanList' => $bulanList,
            'statusEnum' => StatusKetersediaanEnum::cases(),
        ]);
    }

    public function store(BulanMagangRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $bulan = BulanMagang::create($validated);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'bulan_magang',
            idData: $bulan->id,
            nilaiBaru: $bulan->toArray(),
            detail: ['aksi' => 'Membuat periode bulan magang baru']
        );

        return redirect()->route('superadmin.bulan.index')
            ->with('success', 'Periode bulan magang berhasil ditambahkan.');
    }

    public function update(BulanMagangRequest $request, BulanMagang $bulan): RedirectResponse
    {
        $nilaiLama = $bulan->toArray();
        $validated = $request->validated();

        $bulan->update($validated);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'bulan_magang',
            idData: $bulan->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $bulan->toArray(),
            detail: ['aksi' => 'Memperbarui periode bulan magang / override status']
        );

        return redirect()->route('superadmin.bulan.index')
            ->with('success', 'Periode bulan magang berhasil diperbarui.');
    }

    public function toggleAktif(Request $request, BulanMagang $bulan): RedirectResponse
    {
        $targetAktif = ! $bulan->aktif;

        // Penegakan Aturan Bisnis BR-12 jika hendak menonaktifkan bulan magang
        if (! $targetAktif) {
            $hasActivePendaftaran = Pendaftaran::where('bulan_magang_id', $bulan->id)
                ->whereIn('status', [
                    StatusPendaftaranEnum::Diajukan,
                    StatusPendaftaranEnum::WaitingList,
                    StatusPendaftaranEnum::PerluPerbaikan,
                    StatusPendaftaranEnum::BerkasBelumLengkap,
                    StatusPendaftaranEnum::Diterima,
                ])
                ->exists();

            if ($hasActivePendaftaran) {
                return back()->with('error', 'Penonaktifan ditolak (BR-12): Periode bulan magang masih memiliki pendaftaran aktif.');
            }

            $hasActivePenempatan = Penempatan::where('status', StatusPenempatanEnum::Aktif)
                ->where(function ($q) use ($bulan) {
                    $q->whereHas('kuota', fn ($qk) => $qk->where('bulan_magang_id', $bulan->id))
                        ->orWhereHas('pendaftaran', fn ($qp) => $qp->where('bulan_magang_id', $bulan->id));
                })
                ->exists();

            if ($hasActivePenempatan) {
                return back()->with('error', 'Penonaktifan ditolak (BR-12): Periode bulan magang masih memiliki penempatan peserta aktif.');
            }
        }

        $nilaiLama = ['aktif' => $bulan->aktif];
        $bulan->aktif = $targetAktif;
        $bulan->save();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'bulan_magang',
            idData: $bulan->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: ['aktif' => $bulan->aktif],
            detail: ['aksi' => 'Mengubah status aktif periode bulan magang']
        );

        $statusText = $bulan->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('superadmin.bulan.index')
            ->with('success', "Periode bulan magang berhasil {$statusText}.");
    }
}
