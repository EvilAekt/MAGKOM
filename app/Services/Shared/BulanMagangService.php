<?php

declare(strict_types=1);

namespace App\Services\Shared;

use App\Models\BulanMagang;
use Carbon\Carbon;

class BulanMagangService
{
    /**
     * Dapatkan daftar bulan magang untuk opsi filter pencarian.
     * Menggabungkan daftar bulan dari basis data dan 12 bulan terakhir.
     *
     * @return array<string, string> Key: 'Y-m', Value: 'Nama Bulan Tahun'
     */
    public function getDaftarBulan(): array
    {
        $daftar = [];
        $timezone = config('app.timezone', 'Asia/Jakarta');

        $bulanMagangs = BulanMagang::orderBy('bulan_mulai', 'desc')->get();
        foreach ($bulanMagangs as $bm) {
            if ($bm->bulan_mulai) {
                $key = Carbon::parse($bm->bulan_mulai)->setTimezone($timezone)->format('Y-m');
                $daftar[$key] = $bm->nama_bulan;
            }
        }

        for ($i = 0; $i < 12; $i++) {
            $dt = Carbon::now()->subMonths($i);
            $key = $dt->format('Y-m');
            if (! isset($daftar[$key])) {
                $daftar[$key] = $dt->locale('id')->translatedFormat('F Y');
            }
        }

        krsort($daftar);

        return $daftar;
    }
}
