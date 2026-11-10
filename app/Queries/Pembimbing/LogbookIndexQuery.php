<?php

declare(strict_types=1);

namespace App\Queries\Pembimbing;

use App\Models\Logbook;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class LogbookIndexQuery
{
    /**
     * Mengambil daftar logbook terfilter dengan eager loading dan paginasi.
     */
    public function getPaginated(User $user, Request $request, int $perPage = 10): LengthAwarePaginator
    {
        $query = Logbook::with(['penempatan.peserta.profil.jurusan', 'penempatan.bidang'])
            ->whereHas('penempatan', function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            });

        if ($request->filled('masuk')) {
            $query->where('penempatan_id', $request->input('masuk'));
        } elseif ($request->filled('penempatan_id')) {
            $query->where('penempatan_id', $request->input('penempatan_id'));
        }

        if ($request->filled('bulan')) {
            $parts = explode('-', (string) $request->input('bulan'));
            if (count($parts) === 2) {
                [$tahun, $bln] = $parts;
                $query->whereYear('tanggal', (int) $tahun)
                    ->whereMonth('tanggal', (int) $bln);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->input('tanggal'));
        }

        if ($request->filled('q')) {
            $search = '%'.$request->input('q').'%';
            $query->where(function ($q) use ($search) {
                $q->where('kegiatan', 'like', $search)
                    ->orWhere('catatan_pembimbing', 'like', $search)
                    ->orWhereHas('penempatan.peserta', function ($qp) use ($search) {
                        $qp->where('nama', 'like', $search);
                    });
            });
        }

        return $query->latest('tanggal')->paginate($perPage)->withQueryString();
    }
}
