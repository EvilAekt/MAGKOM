<?php

declare(strict_types=1);

namespace App\Queries\Pembimbing;

use App\Enums\StatusPenempatanEnum;
use App\Models\KelompokMagang;
use App\Models\Penempatan;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as SupportCollection;

class PenempatanLogbookQuery
{
    /**
     * Mengambil daftar penempatan aktif & kelompok bimbingan pembimbing untuk antarmuka kartu mentee.
     *
     * @return array{
     *     penempatanList: LengthAwarePaginator,
     *     allPenempatan: Collection<int, Penempatan>,
     *     selectedPenempatan: ?Penempatan,
     *     masukPenempatan: ?Penempatan,
     *     selectedPenempatanId: ?int,
     *     masukPenempatanId: ?int,
     *     kelompokList: SupportCollection<int, KelompokMagang>,
     *     filterCounts: array{semua: int, individu: int, kelompok: int},
     *     jenisLogbook: string,
     *     masukKelompokId: ?int,
     *     masukKelompok: ?KelompokMagang,
     *     masukKelompokMembers: SupportCollection<int, User>,
     *     masukKelompokPenempatan: Collection<int, Penempatan>
     * }
     */
    public function getMenteeData(User $user, Request $request, int $perPage = 10): array
    {
        $penempatanBaseQuery = Penempatan::with([
            'peserta.profil.jurusan',
            'bidang',
            'pendaftaran',
            'logbooks' => function ($q) {
                $q->latest('tanggal');
            },
        ])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            })
            ->where('status', StatusPenempatanEnum::Aktif);

        $allPenempatan = (clone $penempatanBaseQuery)->get();

        // Daftar penempatan jalur individu (non-tim)
        $individuQuery = (clone $penempatanBaseQuery)->where(function ($q) {
            $q->whereHas('pendaftaran', fn ($qp) => $qp->whereNull('kelompok_magang_id'))
                ->orWhereDoesntHave('pendaftaran');
        });

        $individuCount = (clone $individuQuery)->count();

        if ($request->filled('penempatan_id')) {
            $individuQuery->where('id', $request->input('penempatan_id'));
        }

        if ($request->filled('q')) {
            $search = '%'.$request->input('q').'%';
            $individuQuery->whereHas('peserta', function ($qp) use ($search) {
                $qp->where('nama', 'like', $search)
                    ->orWhereHas('profil', function ($qprof) use ($search) {
                        $qprof->where('institusi', 'like', $search)
                            ->orWhere('nim_nisn', 'like', $search);
                    });
            });
        }

        $penempatanList = $individuQuery->latest('id')->paginate($perPage)->withQueryString();

        // Daftar tim/kelompok unik binaan pembimbing
        $kelompokIds = $allPenempatan
            ->pluck('pendaftaran.kelompok_magang_id')
            ->filter()
            ->unique()
            ->values();

        $kelompokQuery = KelompokMagang::with(['ketua.profil.jurusan', 'anggota.user.profil.jurusan'])
            ->whereIn('id', $kelompokIds)
            ->withCount(['anggota']);

        if ($request->filled('q')) {
            $search = '%'.$request->input('q').'%';
            $kelompokQuery->where(function ($q) use ($search) {
                $q->where('nama_kelompok', 'like', $search)
                    ->orWhereHas('ketua', fn ($qk) => $qk->where('nama', 'like', $search));
            });
        }

        $kelompokList = $request->filled('penempatan_id')
            ? collect()
            : $kelompokQuery->get();

        $kelompokTotalCount = $kelompokIds->count();
        $filterCounts = [
            'semua' => $individuCount + $kelompokTotalCount,
            'individu' => $individuCount,
            'kelompok' => $kelompokTotalCount,
        ];

        $selectedPenempatanId = $request->integer('penempatan_id') ?: null;
        $selectedPenempatan = null;
        if ($selectedPenempatanId) {
            $selectedPenempatan = $penempatanList->firstWhere('id', $selectedPenempatanId)
                ?? $allPenempatan->firstWhere('id', $selectedPenempatanId);
        }

        $masukPenempatanId = $request->integer('masuk') ?: null;
        $masukPenempatan = null;
        if ($masukPenempatanId) {
            $masukPenempatan = $penempatanList->firstWhere('id', $masukPenempatanId)
                ?? $allPenempatan->firstWhere('id', $masukPenempatanId);
        }

        $masukKelompokId = $request->integer('masuk_kelompok') ?: null;
        $masukKelompok = null;
        $masukKelompokMembers = collect();
        $masukKelompokPenempatan = new Collection;

        if ($masukKelompokId && $kelompokIds->contains($masukKelompokId)) {
            $masukKelompok = KelompokMagang::with([
                'ketua.profil.jurusan',
                'anggota.user.profil.jurusan',
            ])->find($masukKelompokId);

            if ($masukKelompok) {
                $masukKelompokMembers = collect([$masukKelompok->ketua])
                    ->merge($masukKelompok->anggota->pluck('user')->filter())
                    ->filter()
                    ->unique('id');

                $memberIds = $masukKelompokMembers->pluck('id')->all();
                $masukKelompokPenempatan = $allPenempatan->whereIn('peserta_id', $memberIds)->values();
            }
        }

        $jenisLogbook = $request->input('jenis');
        if (! in_array($jenisLogbook, ['semua', 'individu', 'kelompok'], true)) {
            if ($masukKelompokId || $request->filled('dari_kelompok')) {
                $jenisLogbook = 'kelompok';
            } elseif ($masukPenempatan) {
                $jenisLogbook = $masukPenempatan->isKelompok() ? 'kelompok' : 'individu';
            } else {
                $jenisLogbook = 'semua';
            }
        }

        return [
            'penempatanList' => $penempatanList,
            'allPenempatan' => $allPenempatan,
            'selectedPenempatan' => $selectedPenempatan,
            'masukPenempatan' => $masukPenempatan,
            'selectedPenempatanId' => $selectedPenempatanId,
            'masukPenempatanId' => $masukPenempatanId,
            'kelompokList' => $kelompokList,
            'filterCounts' => $filterCounts,
            'jenisLogbook' => $jenisLogbook,
            'masukKelompokId' => $masukKelompokId,
            'masukKelompok' => $masukKelompok,
            'masukKelompokMembers' => $masukKelompokMembers,
            'masukKelompokPenempatan' => $masukKelompokPenempatan,
        ];
    }
}
