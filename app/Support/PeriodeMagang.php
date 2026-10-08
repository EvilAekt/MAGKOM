<?php

declare(strict_types=1);

namespace App\Support;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final class PeriodeMagang
{
    /**
     * Menghitung tanggal mulai (tanggal 1) dan tanggal akhir bulan (akhir hari bulan terakhir)
     * berdasarkan durasi bulan (BR-08).
     *
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    public static function hitung(CarbonInterface|string $bulanMulai, int $durasi): array
    {
        $mulai = CarbonImmutable::parse($bulanMulai, 'Asia/Jakarta')->startOfMonth();
        $selesai = $mulai->addMonthsNoOverflow(max(1, $durasi) - 1)->endOfMonth();

        return [$mulai, $selesai];
    }
}
