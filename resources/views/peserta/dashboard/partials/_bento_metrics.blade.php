{{-- ==========================================================================
   Bento Metrics 3 Mini-Cards (Dashboard Peserta)
   Reference: Precision Visual Parity with Pembimbing Dashboard
   ========================================================================== --}}
@php
    /** @var \App\Models\Penempatan|null $penempatan */
    /** @var int $logbookSudahDiisi */
    /** @var int $logbookBelumDiisi */
    /** @var int $totalMinggu */
    /** @var int $totalTugas */
    /** @var int $tugasSelesai */
    /** @var int $tugasBelum */
    /** @var string $namaPembimbingClean */

    $defaultPembimbing = $penempatan?->pembimbing ? 'Budi Santoso, S.Kom' : 'Tim Diskominfo';
    $pembimbingName = $namaPembimbingClean ?? ($penempatan?->pembimbing?->nama ?? $defaultPembimbing);
    if (! $penempatan?->pembimbing && empty($namaPembimbingClean)) {
        $pembimbingName = 'Tim Diskominfo';
    }

    $totMinggu = max(1, $totalMinggu ?? 13);
    $filledDotsLogbook = max(1, (int) round(($logbookSudahDiisi / $totMinggu) * 20));
    $pctLogbook = min(100, (int) round(($logbookSudahDiisi / $totMinggu) * 100));

    $totTugas = max(1, $totalTugas ?? 1);
    $filledDotsTugas = $totalTugas > 0 ? max(1, (int) round(($tugasBelum / $totTugas) * 20)) : 1;
    $persenTugasSelesai = $totalTugas > 0 ? min(100, (int) round(($tugasSelesai / $totTugas) * 100)) : 0;
@endphp

<div class="bento-kpi-row">
    <!-- Card 1: Logbook Mingguan (Blue) -->
    <div class="bento-card bento-kpi-card kpi-card-logbook">
        <div class="bento-kpi-top">
            <div class="bento-icon-box bento-icon-blue">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <span class="bento-pill-badge badge-green">{{ $pctLogbook }}% terisi</span>
        </div>
        <div class="bento-kpi-body">
            <span class="bento-kpi-label">LOGBOOK TERKIRIM</span>
            <div class="bento-kpi-val-row">
                <span class="bento-kpi-number">{{ $logbookSudahDiisi }}</span>
                <span class="bento-kpi-sub">/{{ $totMinggu }}</span>
            </div>
            <div class="bento-dot-matrix matrix-kpi-bars matrix-blue" aria-label="{{ $logbookSudahDiisi }} dari {{ $totMinggu }} logbook terisi">
                @for($i = 1; $i <= 20; $i++)
                    <span class="matrix-dot {{ $i <= $filledDotsLogbook ? 'dot-filled' : 'dot-empty' }}"></span>
                @endfor
            </div>
        </div>
    </div>

    <!-- Card 2: Total Tugas (Indigo) -->
    <div class="bento-card bento-kpi-card kpi-card-tugas">
        <div class="bento-kpi-top">
            <div class="bento-icon-box bento-icon-indigo">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 11 12 14 22 4"></polyline>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                </svg>
            </div>
            <span class="bento-pill-badge badge-indigo">{{ $persenTugasSelesai }}% tuntas</span>
        </div>
        <div class="bento-kpi-body">
            <span class="bento-kpi-label">TOTAL TUGAS</span>
            <div class="bento-kpi-val-row">
                <span class="bento-kpi-number">{{ $totalTugas }}</span>
                <span class="bento-kpi-sub">tugas</span>
            </div>
            <div class="bento-dot-matrix matrix-kpi-bars matrix-indigo" aria-label="{{ $totalTugas }} total tugas, {{ $tugasSelesai }} tugas tuntas">
                @for($i = 1; $i <= 20; $i++)
                    <span class="matrix-dot {{ $i <= $filledDotsTugas ? 'dot-filled' : 'dot-empty' }}"></span>
                @endfor
            </div>
        </div>
    </div>

    <!-- Card 3: Pembimbing Lapangan (Amber) -->
    <div class="bento-card bento-kpi-card kpi-card-pembimbing">
        <div class="bento-kpi-top">
            <div class="bento-icon-box bento-icon-amber">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
            </div>
            <span class="bento-pill-badge badge-amber">Aktif</span>
        </div>
        <div class="bento-kpi-body">
            <span class="bento-kpi-label">PEMBIMBING LAPANGAN</span>
            <div class="bento-kpi-val-row">
                <span class="bento-kpi-number bento-kpi-name-truncate" title="{{ $pembimbingName }}">{{ $pembimbingName }}</span>
            </div>
            <div class="bento-dot-matrix matrix-kpi-bars matrix-amber" aria-label="Status pembimbing aktif">
                @for($i = 1; $i <= 20; $i++)
                    <span class="matrix-dot dot-filled"></span>
                @endfor
            </div>
        </div>
    </div>
</div>
