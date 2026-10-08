<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\StatusKetersediaanEnum;
use App\Models\BulanMagang;
use App\Models\Kuota;
use Carbon\CarbonImmutable;

class BulanStatusService
{
    /**
     * Menentukan status ketersediaan pendaftaran untuk bulan dan kuota tertentu (BR-02).
     */
    public function status(BulanMagang $bulan, ?Kuota $kuota = null, ?CarbonImmutable $sekarang = null): StatusKetersediaanEnum
    {
        $sekarang ??= CarbonImmutable::now('Asia/Jakarta');

        if (! $bulan->aktif || ($kuota && ! $kuota->aktif)) {
            return StatusKetersediaanEnum::Tutup;
        }

        if ($bulan->override_status !== null) {
            return $bulan->override_status;
        }

        $buka  = CarbonImmutable::parse($bulan->tgl_buka, 'Asia/Jakarta')->startOfDay();
        $tutup = CarbonImmutable::parse($bulan->tgl_tutup, 'Asia/Jakarta')->endOfDay();

        return match (true) {
            $sekarang->lt($buka)   => StatusKetersediaanEnum::Segera,
            $sekarang->lte($tutup) => StatusKetersediaanEnum::Tersedia,
            default                => StatusKetersediaanEnum::Tutup,
        };
    }

    /**
     * Menentukan apakah suatu bulan magang memenuhi syarat untuk tampil di landing page publik (BR-04).
     */
    public function tampilDiLanding(BulanMagang $bulan, ?CarbonImmutable $sekarang = null): bool
    {
        $sekarang ??= CarbonImmutable::now('Asia/Jakarta');

        if (! $bulan->aktif) {
            return false;
        }

        $terlaluAwal = $bulan->override_status === null
            && $sekarang->lt(CarbonImmutable::parse($bulan->tgl_buka, 'Asia/Jakarta')->startOfDay()->subMonth());
        $sudahMulai = $sekarang->gte(CarbonImmutable::parse($bulan->bulan_mulai, 'Asia/Jakarta')->startOfMonth());

        return ! $terlaluAwal && ! $sudahMulai;
    }
}
