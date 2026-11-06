{{-- ==========================================================================
   Three-Metric KPI Row - Dashboard Peserta Magang
   Desain Clean Pastel Card DS-MagKom (Gentelella Parity)
   3 Kartu Horizontal Statis (Hanya Dilihat / Read-Only): Logbook, Tugas, Pembimbing
   ========================================================================== --}}
@php
    /** @var \App\Models\Penempatan|null $penempatan */
    /** @var int $logbookSudahDiisi */
    /** @var int $logbookBelumDiisi */
    /** @var int $tugasBelum */
    /** @var int $tugasSelesai */
    /** @var string $namaPembimbingClean */
    $defaultPembimbing = $penempatan?->pembimbing ? 'Budi Santoso, S.Kom' : 'Tim Diskominfo';
    $pembimbingName = $namaPembimbingClean ?? ($penempatan?->pembimbing?->nama ?? $defaultPembimbing);
    if (! $penempatan?->pembimbing && empty($namaPembimbingClean)) {
        $pembimbingName = 'Tim Diskominfo';
    }
    $logbookSudahDiisi = $logbookSudahDiisi ?? ($penempatan?->logbooks?->count() ?? 0);
    $logbookBelumDiisi = $logbookBelumDiisi ?? 0;
    $tugasBelum = $tugasBelum ?? 0;
    $tugasSelesai = $tugasSelesai ?? 0;
@endphp

<div class="saas-kpi-triple-grid saas-kpi-grid">
    <!-- Kartu 1: Logbook Aktivitas -->
    <div class="saas-pastel-kpi-card saas-kpi-card saas-kpi-purple saas-static-card">
        <div class="saas-pastel-card-top">
            <div class="saas-kpi-solid-icon icon-indigo">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <span class="saas-pastel-badge purple">
                {{ $logbookBelumDiisi }} belum diisi
            </span>
        </div>
        <div class="saas-pastel-label">Logbook Aktivitas</div>
        <div class="saas-pastel-value">
            <span class="saas-value-num">{{ $logbookSudahDiisi }}</span>
            <span class="saas-value-unit">laporan</span>
            <span class="sr-only">Laporan</span>
        </div>
        <span class="sr-only">Logbook Mingguan tercatat rapi</span>
        <div class="saas-kpi-inline-track track-indigo">
            <span class="saas-kpi-inline-fill fill-indigo"></span>
        </div>
    </div>

    <!-- Kartu 2: Tugas Magang -->
    <div class="saas-pastel-kpi-card saas-kpi-card saas-kpi-amber saas-static-card">
        <div class="saas-pastel-card-top">
            <div class="saas-kpi-solid-icon icon-gold">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#02275D" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="4"></rect>
                    <path d="m9 12 2 2 4-4"></path>
                </svg>
            </div>
            <span class="saas-pastel-badge amber">
                {{ $tugasSelesai }} selesai
            </span>
        </div>
        <div class="saas-pastel-label">Tugas Magang</div>
        <div class="saas-pastel-value">
            <span class="saas-value-num">{{ $tugasBelum }}</span>
            <span class="saas-value-unit">aktif</span>
        </div>
        <div class="saas-kpi-inline-track track-amber">
            <span class="saas-kpi-inline-fill fill-amber"></span>
        </div>
    </div>

    <!-- Kartu 3: Pembimbing Lapangan -->
    <div class="saas-pastel-kpi-card saas-kpi-card saas-kpi-blue saas-static-card">
        <div class="saas-pastel-card-top">
            <div class="saas-kpi-solid-icon icon-navy">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FFCB05" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="7" r="4"></circle>
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                </svg>
            </div>
            <span class="saas-pastel-badge green">
                <span class="saas-dot-mini green"></span>
                Pendampingan aktif
            </span>
        </div>
        <div class="saas-pastel-label">Pembimbing Lapangan</div>
        <div class="saas-pastel-value saas-value-name" title="{{ $pembimbingName }}">
            {!! str_contains($pembimbingName, ',') ? str_replace(', ', ',<br>', e($pembimbingName)) : e($pembimbingName) !!}
        </div>
    </div>
</div>
