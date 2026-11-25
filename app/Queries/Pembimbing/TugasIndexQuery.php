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
        $query = Tugas::with(['penempatan.peserta.profil.jurusan', 'penempatan.bidang', 'progresLogs.pelapor', 'kelompokMagang.ketua', 'kelompokMagang.anggota.user', 'pesertaDitugaskan'])
            ->where(function ($root) use ($user) {
                $root->whereHas('penempatan', function ($q) use ($user) {
                    if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                        $q->where('pembimbing_id', $user->id);
                    }
                })->orWhere(function ($q) use ($user) {
                    $q->where('tipe', 'kelompok')
                        ->whereHas('kelompokMagang', function ($qk) use ($user) {
                            if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                                $qk->whereHas('pendaftaran.penempatan', function ($qp) use ($user) {
                                    $qp->where('pembimbing_id', $user->id);
                                });
                            }
                        });
                });
            });

        // Filter Jenis Tugas: Semua (default), Individu, atau Kelompok
        // Jika sedang fokus ke penempatan mentee tertentu ('masuk'), biarkan seluruh tugas mentee tersebut tampil.
        if (! $request->filled('masuk') && $request->filled('jenis_tugas') && in_array($request->input('jenis_tugas'), ['individu', 'kelompok'], true)) {
            $query->where('tipe', $request->input('jenis_tugas'));
        }

        if ($request->filled('masuk')) {
            $masukId = (int) $request->input('masuk');
            $query->where(function ($q) use ($masukId) {
                $q->where('penempatan_id', $masukId)
                    ->orWhereHas('tugasPeserta', fn ($tp) => $tp->where('penempatan_id', $masukId));
            });
        } elseif ($request->filled('masuk_kelompok')) {
            $kelompokId = (int) $request->input('masuk_kelompok');
            $query->where('tipe', 'kelompok')
                ->where('kelompok_magang_id', $kelompokId);
        } elseif ($request->filled('penempatan_id')) {
            $penId = (int) $request->input('penempatan_id');
            $query->where(function ($q) use ($penId) {
                $q->where('penempatan_id', $penId)
                    ->orWhereHas('tugasPeserta', fn ($tp) => $tp->where('penempatan_id', $penId));
            });
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
                    })
                    ->orWhereHas('tugasPeserta.peserta', function ($qp) use ($search) {
                        $qp->where('nama', 'like', $search);
                    })
                    ->orWhereHas('kelompokMagang', function ($qk) use ($search) {
                        $qk->where('nama_kelompok', 'like', $search);
                    });
            });
        }

        return $query;
    }
}
