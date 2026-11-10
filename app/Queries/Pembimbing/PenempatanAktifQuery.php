<?php

declare(strict_types=1);

namespace App\Queries\Pembimbing;

use App\Enums\StatusPenempatanEnum;
use App\Models\Penempatan;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PenempatanAktifQuery
{
    /**
     * Membangun query dasar penempatan aktif peserta bimbingan dengan eager loading tugas dan progres.
     *
     * @return Builder<Penempatan>
     */
    public function baseQuery(User $user): Builder
    {
        return Penempatan::with([
            'peserta.profil.jurusan',
            'bidang',
            'tugas' => function ($q) {
                $q->with('progresLogs')->latest();
            },
        ])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            })
            ->where('status', StatusPenempatanEnum::Aktif);
    }

    /**
     * Membangun query penempatan aktif dengan filter request (penempatan_id dan pencarian q).
     *
     * @return Builder<Penempatan>
     */
    public function buildFiltered(Request $request, User $user): Builder
    {
        $query = $this->baseQuery($user);

        if ($request->filled('penempatan_id')) {
            $query->where('id', $request->input('penempatan_id'));
        }

        if ($request->filled('q')) {
            $search = '%'.$request->input('q').'%';
            $query->whereHas('peserta', function ($qp) use ($search) {
                $qp->where('nama', 'like', $search)
                    ->orWhereHas('profil', function ($qprof) use ($search) {
                        $qprof->where('institusi', 'like', $search)
                            ->orWhere('nim_nisn', 'like', $search);
                    });
            });
        }

        return $query;
    }
}
