<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\KuotaRequest;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KuotaController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function index(Request $request): View
    {
        $bulanList = BulanMagang::where('aktif', true)
            ->orderBy('bulan_mulai', 'asc')
            ->get();

        $selectedBulanId = $request->integer('bulan_magang_id', $bulanList->first()?->id ?? 0);
        $selectedBulan = BulanMagang::with(['kuota.jurusan'])->find($selectedBulanId);

        $jurusanList = Jurusan::where('aktif', true)
            ->orderBy('kategori', 'asc')
            ->orderBy('nama_jurusan', 'asc')
            ->get();

        return view('admin.kuota.index', [
            'bulanList' => $bulanList,
            'selectedBulan' => $selectedBulan,
            'jurusanList' => $jurusanList,
        ]);
    }

    public function store(KuotaRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $kuota = Kuota::create($validated);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'kuota',
            idData: $kuota->id,
            nilaiBaru: $kuota->toArray(),
            detail: ['aksi' => 'Menambahkan kuota jurusan pada bulan magang']
        );

        return redirect()->route('admin.kuota.index', ['bulan_magang_id' => $kuota->bulan_magang_id])
            ->with('success', 'Kuota jurusan berhasil ditambahkan.');
    }

    public function update(KuotaRequest $request, Kuota $kuota): RedirectResponse
    {
        $nilaiLama = $kuota->toArray();
        $validated = $request->validated();

        $kuota->update($validated);

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'kuota',
            idData: $kuota->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: $kuota->toArray(),
            detail: ['aksi' => 'Memperbarui alokasi kuota jurusan']
        );

        return redirect()->route('admin.kuota.index', ['bulan_magang_id' => $kuota->bulan_magang_id])
            ->with('success', 'Alokasi kuota jurusan berhasil diperbarui.');
    }

    public function toggleAktif(Request $request, Kuota $kuota): RedirectResponse
    {
        $targetAktif = ! $kuota->aktif;

        // Penegakan Aturan Bisnis BR-12 & AC-05-6 jika hendak menonaktifkan kuota
        if (! $targetAktif) {
            $hasActivePendaftaran = Pendaftaran::where('kuota_id', $kuota->id)
                ->whereIn('status', [
                    StatusPendaftaranEnum::Diajukan,
                    StatusPendaftaranEnum::WaitingList,
                    StatusPendaftaranEnum::PerluPerbaikan,
                    StatusPendaftaranEnum::BerkasBelumLengkap,
                    StatusPendaftaranEnum::Diterima,
                ])
                ->exists();

            if ($hasActivePendaftaran) {
                return back()->with('error', 'Penonaktifan ditolak (BR-12, AC-05-6): Kuota jurusan masih memiliki pendaftaran aktif.');
            }

            $hasActivePenempatan = Penempatan::where('kuota_id', $kuota->id)
                ->where('status', StatusPenempatanEnum::Aktif)
                ->exists();

            if ($hasActivePenempatan) {
                return back()->with('error', 'Penonaktifan ditolak (BR-12, AC-05-6): Kuota jurusan masih memiliki penempatan peserta aktif.');
            }
        }

        $nilaiLama = ['aktif' => $kuota->aktif];
        $kuota->aktif = $targetAktif;
        $kuota->save();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'kuota',
            idData: $kuota->id,
            nilaiLama: $nilaiLama,
            nilaiBaru: ['aktif' => $kuota->aktif],
            detail: ['aksi' => 'Mengubah status aktif kuota jurusan']
        );

        $statusText = $kuota->aktif ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.kuota.index', ['bulan_magang_id' => $kuota->bulan_magang_id])
            ->with('success', "Kuota jurusan berhasil {$statusText}.");
    }
}
