<?php

declare(strict_types=1);

namespace App\Queries\Pembimbing;

use App\Enums\StatusPenempatanEnum;
use App\Models\Penempatan;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class PenempatanLogbookQuery
{
    /**
     * Mengambil daftar penempatan aktif bimbingan pembimbing untuk antarmuka kartu mentee.
     *
     * @return array{penempatanList: LengthAwarePaginator, allPenempatan: Collection<int, Penempatan>, selectedPenempatan: ?Penempatan, masukPenempatan: ?Penempatan, selectedPenempatanId: ?int, masukPenempatanId: ?int}
     */
    public function getMenteeData(User $user, Request $request, int $perPage = 10): array
    {
        $penempatanBaseQuery = Penempatan::with(['peserta.profil.jurusan', 'bidang', 'logbooks' => function ($q) {
            $q->latest('tanggal');
        }])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            })
            ->where('status', StatusPenempatanEnum::Aktif);

        $allPenempatan = (clone $penempatanBaseQuery)->get();

        if ($request->filled('penempatan_id')) {
            $penempatanBaseQuery->where('id', $request->input('penempatan_id'));
        }

        if ($request->filled('q')) {
            $search = '%'.$request->input('q').'%';
            $penempatanBaseQuery->whereHas('peserta', function ($qp) use ($search) {
                $qp->where('nama', 'like', $search)
                    ->orWhereHas('profil', function ($qprof) use ($search) {
                        $qprof->where('institusi', 'like', $search)
                            ->orWhere('nim_nisn', 'like', $search);
                    });
            });
        }

        $penempatanList = (clone $penempatanBaseQuery)->latest('id')->paginate($perPage)->withQueryString();

        $selectedPenempatanId = $request->integer('penempatan_id') ?: null;
        $selectedPenempatan = null;
        if ($selectedPenempatanId) {
            $selectedPenempatan = $penempatanList->firstWhere('id', $selectedPenempatanId);
            if (! $selectedPenempatan) {
                $selectedPenempatan = $allPenempatan->firstWhere('id', $selectedPenempatanId);
            }
        }

        $masukPenempatanId = $request->integer('masuk') ?: null;
        $masukPenempatan = null;
        if ($masukPenempatanId) {
            $masukPenempatan = $penempatanList->firstWhere('id', $masukPenempatanId);
            if (! $masukPenempatan) {
                $masukPenempatan = $allPenempatan->firstWhere('id', $masukPenempatanId);
            }
        }

        return [
            'penempatanList' => $penempatanList,
            'allPenempatan' => $allPenempatan,
            'selectedPenempatan' => $selectedPenempatan,
            'masukPenempatan' => $masukPenempatan,
            'selectedPenempatanId' => $selectedPenempatanId,
            'masukPenempatanId' => $masukPenempatanId,
        ];
    }
}
