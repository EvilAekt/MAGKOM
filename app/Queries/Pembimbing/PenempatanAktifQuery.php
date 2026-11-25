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
            'projectBersama' => function ($q) {
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
     * Membatasi query hanya pada penempatan peserta jalur individu (bukan anggota kelompok).
     *
     * @param  Builder<Penempatan>  $query
     * @return Builder<Penempatan>
     */
    public function scopeIndividuOnly(Builder $query): Builder
    {
        return $query->whereDoesntHave('pendaftaran', function ($q) {
            $q->where('tipe', 'kelompok')
                ->orWhereNotNull('kelompok_magang_id');
        });
    }

    /**
     * Membatasi query hanya pada penempatan peserta jalur kelompok.
     *
     * @param  Builder<Penempatan>  $query
     * @return Builder<Penempatan>
     */
    public function scopeKelompokOnly(Builder $query): Builder
    {
        return $query->whereHas('pendaftaran', function ($q) {
            $q->where('tipe', 'kelompok')
                ->orWhereNotNull('kelompok_magang_id');
        });
    }

    /**
     * Membangun query penempatan aktif dengan filter request (jenis_tugas, penempatan_id, dan pencarian q).
     *
     * @return Builder<Penempatan>
     */
    public function buildFiltered(Request $request, User $user): Builder
    {
        $query = $this->baseQuery($user);

        if ($request->filled('masuk_kelompok')) {
            $kelompokId = (int) $request->input('masuk_kelompok');
            $query->whereHas('pendaftaran', fn ($q) => $q->where('kelompok_magang_id', $kelompokId));
        } elseif ($request->input('jenis_tugas', 'semua') === 'kelompok') {
            $this->scopeKelompokOnly($query);
        } else {
            $this->scopeIndividuOnly($query);
        }

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
