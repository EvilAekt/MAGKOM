{{-- ==========================================================================
   Bento Mid-Analytics Cards (Dashboard Peserta)
   1. Distribusi Tugas (2 Bar: Selesai & Terlambat + Total Tugas Badge)
   2. Capaian Logbook (Semi-Circle Radial Gauge 180° + Tersisa Minggu)
   ========================================================================== --}}
@php
    /** @var int $totalTugas */
    /** @var int $tugasSelesai */
    /** @var int $tugasBelum */
    /** @var int $tugasLewatTenggat */
    /** @var int $logbookSudahDiisi */
    /** @var int $totalMinggu */
    /** @var int $mingguAktif */

    $selesaiVal = $tugasSelesai ?? 0;
    $overdueVal = $tugasLewatTenggat ?? 0;
    $totalTugasVal = $totalTugas ?? ($selesaiVal + ($tugasBelum ?? 0));

    $maxVal = max(1, $selesaiVal, $overdueVal);
    $hSelesai = max(24, (int) round(($selesaiVal / $maxVal) * 85));
    $hOverdue = max(24, (int) round(($overdueVal / $maxVal) * 85));

    $totMinggu = max(1, $totalMinggu ?? 13);
    $pctLogbook = min(100, (int) round(($logbookSudahDiisi / $totMinggu) * 100));
    $sisaMinggu = max(0, $totMinggu - $logbookSudahDiisi);

    // Perhitungan Semi-Circle Gauge SVG (Radius = 60, Perimeter 180° = PI * 60 ≈ 188.5)
    $gaugeRadius = 60;
    $gaugePerimeter = 188.5;
    $gaugeOffset = max(0, min($gaugePerimeter, $gaugePerimeter * (1 - ($pctLogbook / 100))));
@endphp

<div class="bento-mid-row">
    <!-- 1. Distribusi Tugas Card (2 Bar: Selesai & Terlambat) -->
    <div class="bento-card bento-distribusi-card">
        <div class="bento-card-header">
            <div class="bento-title-group">
                <span class="bento-title-indicator"></span>
                <h3 class="bento-card-title">Distribusi Tugas</h3>
            </div>
            <span class="bento-pill-badge badge-neutral">{{ $totalTugasVal }} total tugas</span>
        </div>

        <div class="bento-distribusi-chart-box">
            <div class="bento-bars-track bento-bars-track-2col">
                <!-- Bar 1: Selesai -->
                <div class="bento-bar-col">
                    <div class="bento-bar-container">
                        <div class="bento-bar bar-selesai" style="height: {{ $hSelesai }}%;">
                            <span class="bento-bar-val">{{ $selesaiVal }}</span>
                        </div>
                    </div>
                    <span class="bento-bar-label">Selesai</span>
                </div>

                <!-- Bar 2: Terlambat -->
                <div class="bento-bar-col">
                    <div class="bento-bar-container">
                        <div class="bento-bar bar-terlambat" style="height: {{ $hOverdue }}%;">
                            <span class="bento-bar-val">{{ $overdueVal }}</span>
                        </div>
                    </div>
                    <span class="bento-bar-label">Terlambat</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Capaian Logbook Card (Semi-Circle Radial Gauge) -->
    <div class="bento-card bento-kuota-card">
        <div class="bento-card-header">
            <div class="bento-title-group">
                <span class="bento-title-indicator"></span>
                <h3 class="bento-card-title">Capaian Logbook</h3>
            </div>
            <span class="bento-pill-badge badge-neutral">{{ $logbookSudahDiisi }}/{{ $totMinggu }} minggu</span>
        </div>

        <div class="bento-kuota-gauge-box">
            <div class="bento-gauge-wrapper">
                <svg class="bento-gauge-svg" viewBox="0 0 160 95" width="160" height="95">
                    <!-- Gauge Background Track -->
                    <path class="gauge-track"
                          d="M 20 85 A 60 60 0 0 1 140 85"
                          fill="none"
                          stroke-width="14"
                          stroke-linecap="round" />
                    <!-- Gauge Active Progress Arc -->
                    <path class="gauge-fill"
                          d="M 20 85 A 60 60 0 0 1 140 85"
                          fill="none"
                          stroke-width="14"
                          stroke-linecap="round"
                          stroke-dasharray="188.5"
                          stroke-dashoffset="{{ $gaugeOffset }}" />
                </svg>
                <div class="bento-gauge-center">
                    <span class="gauge-center-percent">{{ $pctLogbook }}%</span>
                </div>
            </div>
            <div class="bento-gauge-caption">
                <span>{{ $logbookSudahDiisi }} dari {{ $totMinggu }} jurnal dilaporkan &bull; tersisa {{ $sisaMinggu }} minggu</span>
            </div>
        </div>
    </div>
</div>
