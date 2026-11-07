{{-- ==========================================================================
   Bento Mid-Analytics Cards
   1. Distribusi Tugas (Bar Chart 4 Warna: Selesai, Dikerjakan, Review, Terlambat)
   2. Kuota Bimbingan (Semi-Circle Radial Gauge 65% + Tersisa Slot)
   ========================================================================== --}}
@php
    $selesaiVal = $tugasMetrics['selesai'] ?? $tugasSelesaiKumulatif;
    $overdueVal = $tugasMetrics['tugasOverdueCount'] ?? 0;
    $reviewVal = $allPendingProgresLogs->count();
    $prosesVal = max(0, ($tugasMetrics['tugasAktifCount'] ?? 0) - $overdueVal - $reviewVal);
    $totalActiveTugas = max(1, $selesaiVal + $prosesVal + $reviewVal + $overdueVal);

    // Hitung tinggi proporsional dalam persentase (min 20%, max 95%)
    $maxVal = max(1, $selesaiVal, $prosesVal, $reviewVal, $overdueVal);
    $hSelesai = max(24, (int) round(($selesaiVal / $maxVal) * 85));
    $hProses = max(24, (int) round(($prosesVal / $maxVal) * 95));
    $hReview = max(24, (int) round(($reviewVal / $maxVal) * 85));
    $hOverdue = max(24, (int) round(($overdueVal / $maxVal) * 85));

    $sisaSlot = max(0, $maxBebanKapasitas - $totalMenteeAktif);

    // Perhitungan Semi-Circle Gauge SVG (Radius = 60, Perimeter 180° = PI * 60 ≈ 188.5)
    $gaugeRadius = 60;
    $gaugePerimeter = 188.5;
    $gaugeOffset = max(0, min($gaugePerimeter, $gaugePerimeter * (1 - ($persentaseKapasitas / 100))));
@endphp

<div class="bento-mid-row">
    <!-- 1. Distribusi Tugas Card -->
    <div class="bento-card bento-distribusi-card sc-reveal-scale sc-delay-1" data-scroll-reveal>
        <div class="bento-card-header">
            <div class="bento-title-group">
                <span class="bento-title-indicator"></span>
                <h3 class="bento-card-title">Distribusi Tugas</h3>
            </div>
            <span class="bento-pill-badge badge-neutral">{{ $totalTugasKumulatif }} tugas aktif</span>
        </div>

        <div class="bento-distribusi-chart-box">
            <div class="bento-bars-track">
                <!-- Bar 1: Selesai -->
                <div class="bento-bar-col">
                    <div class="bento-bar-container">
                        <div class="bento-bar bar-selesai" style="height: {{ $hSelesai }}%;">
                            <span class="bento-bar-val">{{ $selesaiVal }}</span>
                        </div>
                    </div>
                    <span class="bento-bar-label">Selesai</span>
                </div>

                <!-- Bar 2: Dikerjakan -->
                <div class="bento-bar-col">
                    <div class="bento-bar-container">
                        <div class="bento-bar bar-dikerjakan" style="height: {{ $hProses }}%;">
                            <span class="bento-bar-val">{{ $prosesVal }}</span>
                        </div>
                    </div>
                    <span class="bento-bar-label">Dikerjakan</span>
                </div>

                <!-- Bar 3: Review -->
                <div class="bento-bar-col">
                    <div class="bento-bar-container">
                        <div class="bento-bar bar-review" style="height: {{ $hReview }}%;">
                            <span class="bento-bar-val">{{ $reviewVal }}</span>
                        </div>
                    </div>
                    <span class="bento-bar-label">Review</span>
                </div>

                <!-- Bar 4: Terlambat -->
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

    <!-- 2. Kuota Bimbingan Card -->
    <div class="bento-card bento-kuota-card sc-reveal-scale sc-delay-2" data-scroll-reveal>
        <div class="bento-card-header">
            <div class="bento-title-group">
                <span class="bento-title-indicator"></span>
                <h3 class="bento-card-title">Kuota Bimbingan</h3>
            </div>
            <span class="bento-pill-badge badge-neutral">{{ $totalMenteeAktif }}/{{ $maxBebanKapasitas }}</span>
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
                    <span class="gauge-center-percent">{{ $persentaseKapasitas }}%</span>
                </div>
            </div>
            <div class="bento-gauge-caption">
                <span>kuota terisi &bull; tersisa {{ $sisaSlot }} slot</span>
            </div>
        </div>
    </div>
</div>

