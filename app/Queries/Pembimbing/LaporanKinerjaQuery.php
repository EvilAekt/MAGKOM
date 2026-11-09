<?php

declare(strict_types=1);

namespace App\Queries\Pembimbing;

use App\Enums\StatusPenempatanEnum;
use App\Models\Penempatan;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LaporanKinerjaQuery
{
    /**
     * Membangun query laporan kinerja peserta bimbingan dengan filter pencarian, status, dan periode.
     *
     * @return Builder<Penempatan>
     */
    public function build(Request $request, User $user): Builder
    {
        $query = Penempatan::with([
            'peserta.profil.jurusan',
            'bidang',
            'tugas',
            'logbooks',
        ])
            ->where(function ($q) use ($user) {
                if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                    $q->where('pembimbing_id', $user->id);
                }
            });

        if ($request->filled('q')) {
            $keyword = trim((string) $request->input('q'));
            $query->whereHas('peserta', function ($sub) use ($keyword) {
                $sub->where('nama', 'like', "%{$keyword}%")
                    ->orWhereHas('profil', function ($p) use ($keyword) {
                        $p->where('institusi', 'like', "%{$keyword}%")
                            ->orWhere('nim_nisn', 'like', "%{$keyword}%");
                    });
            });
        }

        if ($request->filled('penempatan_id')) {
            $query->where('id', $request->input('penempatan_id'));
        }

        if ($request->filled('status')) {
            if ($request->input('status') === 'tidak_aktif') {
                $query->whereIn('status', [StatusPenempatanEnum::Selesai, StatusPenempatanEnum::Nonaktif]);
            } else {
                $query->where('status', $request->input('status'));
            }
        }

        if ($request->filled('bidang_id')) {
            $query->where('bidang_id', $request->integer('bidang_id'));
        }

        if ($request->filled('bulan')) {
            $parts = explode('-', (string) $request->input('bulan'));
            if (count($parts) === 2) {
                [$tahun, $bln] = $parts;
                $query->whereYear('tgl_mulai', (int) $tahun)
                    ->whereMonth('tgl_mulai', (int) $bln);
            }
        } elseif ($request->filled('tahun')) {
            $tahun = (int) $request->input('tahun');
            $query->whereYear('tgl_mulai', $tahun);
        }

        return $query;
    }
}
