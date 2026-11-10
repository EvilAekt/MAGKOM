<?php

declare(strict_types=1);

namespace App\Services\Pembimbing;

use App\Models\Logbook;
use App\Models\User;

class LogbookDashboardService
{
    /**
     * Menghitung statistik ringkasan logbook pembimbing menggunakan conditional aggregation tunggal.
     *
     * @return array{
     *     totalLogbook: int,
     *     totalMenteeMelapor: int,
     *     countDenganCatatan: int,
     *     countTanpaCatatan: int,
     *     countMenunggu: int,
     *     countDisetujui: int,
     *     countRevisi: int,
     *     persenDisetujui: int,
     *     persenMenunggu: int,
     *     persenRevisi: int
     * }
     */
    public function getStatistics(User $user): array
    {
        $baseCounter = Logbook::whereHas('penempatan', function ($q) use ($user) {
            if (! $user->isAdmin() && ! $user->isSuperAdmin()) {
                $q->where('pembimbing_id', $user->id);
            }
        });

        $stats = (clone $baseCounter)
            ->selectRaw("
                COUNT(*) as total_logbook,
                COUNT(DISTINCT penempatan_id) as total_mentee_melapor,
                SUM(CASE WHEN catatan_pembimbing IS NOT NULL AND catatan_pembimbing != '' THEN 1 ELSE 0 END) as count_dengan_catatan,
                SUM(CASE WHEN catatan_pembimbing IS NULL OR catatan_pembimbing = '' THEN 1 ELSE 0 END) as count_tanpa_catatan,
                SUM(CASE WHEN status = 'terkirim' THEN 1 ELSE 0 END) as count_menunggu,
                SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as count_disetujui,
                SUM(CASE WHEN status = 'perlu_revisi' THEN 1 ELSE 0 END) as count_revisi
            ")
            ->first();

        $totalLogbook = (int) ($stats->total_logbook ?? 0);
        $totalMenteeMelapor = (int) ($stats->total_mentee_melapor ?? 0);
        $countDenganCatatan = (int) ($stats->count_dengan_catatan ?? 0);
        $countTanpaCatatan = (int) ($stats->count_tanpa_catatan ?? 0);
        $countMenunggu = (int) ($stats->count_menunggu ?? 0);
        $countDisetujui = (int) ($stats->count_disetujui ?? 0);
        $countRevisi = (int) ($stats->count_revisi ?? 0);

        $persenDisetujui = $totalLogbook > 0 ? (int) round(($countDisetujui / $totalLogbook) * 100) : 0;
        $persenMenunggu = $totalLogbook > 0 ? (int) round(($countMenunggu / $totalLogbook) * 100) : 0;
        $persenRevisi = $totalLogbook > 0 ? (int) round(($countRevisi / $totalLogbook) * 100) : 0;

        return [
            'totalLogbook' => $totalLogbook,
            'totalMenteeMelapor' => $totalMenteeMelapor,
            'countDenganCatatan' => $countDenganCatatan,
            'countTanpaCatatan' => $countTanpaCatatan,
            'countMenunggu' => $countMenunggu,
            'countDisetujui' => $countDisetujui,
            'countRevisi' => $countRevisi,
            'persenDisetujui' => $persenDisetujui,
            'persenMenunggu' => $persenMenunggu,
            'persenRevisi' => $persenRevisi,
        ];
    }
}
