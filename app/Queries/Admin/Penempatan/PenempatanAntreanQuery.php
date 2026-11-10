<?php

declare(strict_types=1);

namespace App\Queries\Admin\Penempatan;

use App\Enums\StatusPendaftaranEnum;
use App\Models\Pendaftaran;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class PenempatanAntreanQuery
{
    /**
     * Membangun query antrean pendaftaran berstatus diterima yang belum memiliki penempatan.
     */
    public function buildQuery(Request $request): Builder
    {
        $query = Pendaftaran::with(['user.profil', 'bulanMagang', 'kuota.jurusan.bidang'])
            ->where('status', StatusPendaftaranEnum::Diterima)
            ->doesntHave('penempatan')
            ->orderBy('diputuskan_at', 'desc');

        if ($request->filled('bulan_magang_id')) {
            $query->where('bulan_magang_id', $request->integer('bulan_magang_id'));
        }

        if ($request->filled('q')) {
            $search = '%'.trim((string) $request->input('q')).'%';
            $query->whereHas('user', function ($qu) use ($search) {
                $qu->where('nama', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhereHas('profil', fn ($qp) => $qp->where('institusi', 'like', $search));
            });
        }

        if ($request->filled('jurusan_id')) {
            $jurusanId = $request->integer('jurusan_id');
            $query->whereHas('kuota', function ($qk) use ($jurusanId) {
                $qk->where('jurusan_id', $jurusanId);
            });
        }

        if ($request->filled('bidang_id')) {
            $bidangId = $request->integer('bidang_id');
            $query->whereHas('kuota.jurusan.bidang', function ($qb) use ($bidangId) {
                $qb->where('bidang.id', $bidangId);
            });
        }

        return $query;
    }

    /**
     * Menghitung total pendaftaran diterima yang belum ditempatkan (tanpa filter pencarian/halaman).
     */
    public function getTotalBelumDitempatkan(): int
    {
        return Pendaftaran::where('status', StatusPendaftaranEnum::Diterima)
            ->doesntHave('penempatan')
            ->count();
    }

    /**
     * Mengambil daftar antrean terfilter.
     *
     * @return Collection<int, Pendaftaran>
     */
    public function getAntrean(Request $request): Collection
    {
        return $this->buildQuery($request)->get();
    }
}
