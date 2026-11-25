<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusPenempatanEnum;
use App\Models\Penempatan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ArsipPesertaService
{
    /**
     * Mendapatkan daftar status penempatan yang dikategorikan sebagai arsip (purna tugas/alumni).
     *
     * @return array<int, StatusPenempatanEnum>
     */
    public function getArsipStatuses(): array
    {
        return [
            StatusPenempatanEnum::Selesai,
            StatusPenempatanEnum::Nonaktif,
            StatusPenempatanEnum::MengundurkanDiri,
        ];
    }

    /**
     * Mengambil data arsip penempatan terpaginasi dengan filter pencarian, status, dan tahun.
     */
    public function getArsipPaginated(Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $query = Penempatan::with(['peserta.profil', 'pembimbing', 'bidang', 'kuota.jurusan', 'slotKhusus'])
            ->whereIn('status', $this->getArsipStatuses())
            ->orderBy('tgl_selesai', 'desc');

        if ($request->filled('search')) {
            $keyword = trim((string) $request->input('search'));
            if ($keyword !== '') {
                $search = '%'.$keyword.'%';
                $query->whereHas('peserta', function ($q) use ($search): void {
                    $q->where('nama', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhereHas('profil', function ($qp) use ($search): void {
                            $qp->where('institusi', 'like', $search)
                                ->orWhere('nim_nisn', 'like', $search);
                        });
                });
            }
        }

        if ($request->filled('tahun')) {
            $query->whereYear('tgl_selesai', $request->input('tahun'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Mengambil 4 agregasi metrik statistik kartu arsip (Total Arsip, Selesai, Nonaktif, Mengundurkan Diri).
     *
     * @return array{total_arsip: int, total_selesai: int, total_nonaktif: int, total_mengundurkan_diri: int}
     */
    public function getStatistikArsip(): array
    {
        $arsipBaseQuery = Penempatan::whereIn('status', $this->getArsipStatuses());

        return [
            'total_arsip' => (clone $arsipBaseQuery)->count(),
            'total_selesai' => (clone $arsipBaseQuery)->where('status', StatusPenempatanEnum::Selesai)->count(),
            'total_nonaktif' => (clone $arsipBaseQuery)->where('status', StatusPenempatanEnum::Nonaktif)->count(),
            'total_mengundurkan_diri' => (clone $arsipBaseQuery)->where('status', StatusPenempatanEnum::MengundurkanDiri)->count(),
        ];
    }

    /**
     * Mengambil daftar tahun unik dari tgl_selesai penempatan arsip untuk dropdown filter.
     *
     * @return Collection<int, mixed>
     */
    public function getTahunFilterList(): Collection
    {
        $driver = DB::connection()->getDriverName();
        $yearExpr = $driver === 'sqlite' ? 'strftime("%Y", tgl_selesai)' : 'YEAR(tgl_selesai)';

        return Penempatan::whereIn('status', $this->getArsipStatuses())
            ->whereNotNull('tgl_selesai')
            ->selectRaw("DISTINCT {$yearExpr} as tahun")
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->filter();
    }

    /**
     * Memuat seluruh relasi detail penempatan arsip untuk halaman show.
     */
    public function loadDetailArsip(Penempatan $penempatan): Penempatan
    {
        return $penempatan->load([
            'peserta.profil.jurusan',
            'pembimbing.bidang',
            'bidang',
            'kuota.jurusan',
            'slotKhusus',
            'tugas.progresLogs',
            'logbooks',
            'pendaftaran.dokumen.jenisDokumen',
        ]);
    }
}
