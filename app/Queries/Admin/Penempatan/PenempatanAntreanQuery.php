<?php

declare(strict_types=1);

namespace App\Queries\Admin\Penempatan;

use App\Enums\StatusPendaftaranEnum;
use App\Models\Pendaftaran;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
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
        $query = Pendaftaran::with(['user.profil.jurusan', 'bulanMagang', 'kuota.jurusan.bidang', 'kelompokMagang.anggota'])
            ->where('status', StatusPendaftaranEnum::Diterima)
            ->doesntHave('penempatan')
            ->orderBy('diputuskan_at', 'desc');

        if ($request->filled('bulan_magang_id')) {
            $query->where('bulan_magang_id', $request->integer('bulan_magang_id'));
        }

        if ($request->filled('q')) {
            $rawSearch = trim((string) $request->input('q'));
            $cleanId = ltrim(str_ireplace(['#', 'REG-'], '', $rawSearch), '0');
            $escapedSearch = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $rawSearch);
            $search = '%'.$escapedSearch.'%';

            $query->where(function ($q) use ($search, $cleanId) {
                if ($cleanId !== '' && is_numeric($cleanId)) {
                    $q->orWhere('id', (int) $cleanId);
                }

                $q->orWhereHas('user', function ($qu) use ($search) {
                    $qu->where('nama', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhereHas('profil', function ($qp) use ($search) {
                            $qp->where('institusi', 'like', $search)
                                ->orWhere('nim_nisn', 'like', $search);
                        });
                })->orWhereHas('kelompokMagang', function ($qk) use ($search) {
                    $qk->where('nama_kelompok', 'like', $search)
                        ->orWhereHas('anggota', function ($qa) use ($search) {
                            $qa->where('nama', 'like', $search)
                                ->orWhere('nim_nisn', 'like', $search);
                        });
                });
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
     * Mengambil daftar antrean terfilter dengan paginasi.
     */
    public function getPaginated(Request $request, int $defaultPerPage = 5): LengthAwarePaginator
    {
        $perPage = (int) $request->input('per_page', $defaultPerPage);
        if (! in_array($perPage, [5, 10, 25, 50], true)) {
            $perPage = $defaultPerPage;
        }

        return $this->buildQuery($request)->paginate($perPage)->withQueryString();
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
