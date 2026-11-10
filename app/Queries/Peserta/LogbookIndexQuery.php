<?php

declare(strict_types=1);

namespace App\Queries\Peserta;

use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPenempatanEnum;
use App\Models\Logbook;
use App\Models\Penempatan;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LogbookIndexQuery
{
    /**
     * Mengambil penempatan aktif peserta beserta relasi terkait.
     */
    public function getPenempatanAktif(User $user): ?Penempatan
    {
        return Penempatan::with(['pembimbing', 'bidang', 'logbooks'])
            ->where('peserta_id', $user->id)
            ->where('status', StatusPenempatanEnum::Aktif)
            ->first();
    }

    /**
     * Mengambil riwayat logbook peserta dengan paginasi.
     */
    public function getLogbooks(User $user, int $perPage = 20): LengthAwarePaginator
    {
        return Logbook::whereHas('penempatan', function ($q) use ($user) {
            $q->where('peserta_id', $user->id);
        })
            ->latest('tanggal')
            ->paginate($perPage);
    }

    /**
     * Menghitung metrik keterisian logbook peserta secara efisien dari relasi yang telah dimuat.
     *
     * @return array{totalTerisi: int, totalTerkirim: int, persentaseTerisi: int, mingguAktifSudahDiisi: bool}
     */
    public function getStatistik(?Penempatan $penempatan, int $totalMinggu, int $mingguAktif): array
    {
        if (! $penempatan) {
            return [
                'totalTerisi' => 0,
                'totalTerkirim' => 0,
                'persentaseTerisi' => 0,
                'mingguAktifSudahDiisi' => false,
            ];
        }

        if ($penempatan->relationLoaded('logbooks')) {
            $logbooks = $penempatan->logbooks;
            $totalTerisi = $logbooks->count();
            $totalTerkirim = $logbooks->where('status', StatusLogbookEnum::Terkirim)->count();
            $persentaseTerisi = $totalMinggu > 0 ? (int) round(($totalTerisi / $totalMinggu) * 100) : 0;
            $mingguAktifSudahDiisi = $logbooks->contains('minggu_ke', $mingguAktif);
        } else {
            $totalTerisi = $penempatan->logbooks()->count();
            $totalTerkirim = $penempatan->logbooks()->where('status', StatusLogbookEnum::Terkirim)->count();
            $persentaseTerisi = $totalMinggu > 0 ? (int) round(($totalTerisi / $totalMinggu) * 100) : 0;
            $mingguAktifSudahDiisi = $penempatan->logbooks()->where('minggu_ke', $mingguAktif)->exists();
        }

        return [
            'totalTerisi' => $totalTerisi,
            'totalTerkirim' => $totalTerkirim,
            'persentaseTerisi' => $persentaseTerisi,
            'mingguAktifSudahDiisi' => $mingguAktifSudahDiisi,
        ];
    }
}
