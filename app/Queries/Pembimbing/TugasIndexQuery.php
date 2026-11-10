<?php

declare(strict_types=1);

namespace App\Queries\Pembimbing;

use App\Models\Tugas;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class TugasIndexQuery
{
    /**
     * Membangun query daftar penugasan dengan filter pencarian dan pembatasan hak akses pembimbing.
     *
     * @return Builder<Tugas>
     */
    public function build(Request $request, User $user): Builder
    {
        $query = Tugas::with(['penempatan.peserta.profil.jurusan', 'penempatan.bidang', 'progresLogs'])
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
                $query->where(function ($q) use ($tahun, $bln) {
                    $q->whereYear('deadline', (int) $tahun)
                        ->whereMonth('deadline', (int) $bln)
                        ->orWhere(function ($sub) use ($tahun, $bln) {
                            $sub->whereNull('deadline')
                                ->whereYear('created_at', (int) $tahun)
                                ->whereMonth('created_at', (int) $bln);
                        });
                });
            }
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'perlu_review') {
                $query->whereHas('progresLogs', function ($qp) {
                    $qp->where('status_review', 'menunggu');
                });
            } else {
                $query->where('status', $request->input('status'));
            }
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->input('kategori'));
        }

        if ($request->filled('q')) {
            $search = '%'.$request->input('q').'%';
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', $search)
                    ->orWhere('deskripsi', 'like', $search)
                    ->orWhereHas('penempatan.peserta', function ($qp) use ($search) {
                        $qp->where('nama', 'like', $search);
                    });
            });
        }

        return $query;
    }
}
