{{-- ==========================================================================
   Four-Metric KPI Bar - Dashboard Peserta Magang
   Desain Modern Clean SaaS & DS-MagKom (Gentelella Parity)
   Informational Static Widget (Read-Only)
   ========================================================================== --}}
@php
    /** @var \App\Models\Penempatan|null $penempatan */
    /** @var bool $isFinalNilai */
    /** @var int $totalLogbook */
    /** @var int $logbookPerluRevisi */
    /** @var int $totalTugas */
    /** @var int $tugasPending */
    /** @var int $sisaHari */
    /** @var int $durasiHari */
    /** @var \App\Models\Penilaian|null $penilaian */

    $tglSelesaiCarbon = $penempatan ? \Carbon\Carbon::parse($penempatan->tgl_selesai ?? $penempatan->tanggal_selesai) : null;
@endphp

<div class="saas-kpi-grid tile_count">
    <!-- Metrik 1: Logbook Aktivitas / Logbook Mingguan (Statis) -->
    <div class="tile_stats_count" style="padding: 0;">
        <div class="tile_stats_inner">
            <span class="count_top">
                <span class="count_icon" aria-hidden="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </span>
                <span>Logbook Aktivitas</span>
            </span>
            <div class="count">
                {{ $totalLogbook }} <span style="font-size: 0.95rem; font-weight: 500; opacity: 0.85;">Laporan</span>
            </div>
            <span class="count_bottom">
                @if($logbookPerluRevisi > 0)
                    <span style="color: var(--magkom-amber-gold, #FFC300); font-weight: 700;">&bull; {{ $logbookPerluRevisi }} perlu revisi</span>
                @else
                    <span style="color: #4ADE80; font-weight: 600;">&check; Logbook Mingguan tercatat rapi</span>
                @endif
            </span>
        </div>
    </div>

    <!-- Metrik 2: Tugas Magang / Tugas Bimbingan (Statis) -->
    <div class="tile_stats_count" style="padding: 0;">
        <div class="tile_stats_inner">
            <span class="count_top">
                <span class="count_icon" aria-hidden="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </span>
                <span>Tugas Magang</span>
            </span>
            <div class="count">
                {{ $tugasBelum }} <span style="font-size: 0.95rem; font-weight: 500; opacity: 0.85;">Aktif</span>
            </div>
            <span class="count_bottom">
                <span style="color: #93C5FD; font-weight: 600;">&bull; {{ $tugasSelesai }} Tugas Selesai</span>
            </span>
        </div>
    </div>

    <!-- Metrik 3: Sisa Hari Magang (Statis) -->
    <div class="tile_stats_count" style="padding: 0;">
        <div class="tile_stats_inner">
            <span class="count_top">
                <span class="count_icon" aria-hidden="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </span>
                <span>Sisa Hari Magang</span>
            </span>
            <div class="count">
                {{ $sisaHari !== null ? $sisaHari : '-' }} <span style="font-size: 0.95rem; font-weight: 500; opacity: 0.85;">Hari</span>
            </div>
            <span class="count_bottom">
                <span style="color: var(--magkom-sun-yellow, #FFCB05); font-weight: 600;">Selesai {{ $tglSelesaiCarbon ? $tglSelesaiCarbon->isoFormat('D MMM Y') : '-' }}</span>
            </span>
        </div>
    </div>

    <!-- Metrik 4: Pembimbing Lapangan (Statis) -->
    <div class="tile_stats_count" style="padding: 0;">
        <div class="tile_stats_inner">
            <span class="count_top">
                <span class="count_icon" aria-hidden="true">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </span>
                <span>Pembimbing Lapangan</span>
            </span>
            <div class="count" style="font-size: 1.15rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-top: 0.35rem;">
                {{ $penempatan->pembimbing->nama ?? 'Tim Diskominfo' }}
            </div>
            <span class="count_bottom">
                <span style="color: {{ $isFinalNilai ? '#4ADE80' : '#93C5FD' }}; font-weight: 600;">
                    {{ $isFinalNilai ? 'Nilai resmi diterbitkan' : 'Pendampingan aktif' }}
                </span>
            </span>
        </div>
    </div>
</div>
