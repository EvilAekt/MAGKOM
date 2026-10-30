{{-- SECTION 2: PURE AUDIT KPI METRICS STRIP (ZERO TELEMETRY, CLEAN ABOVE-THE-FOLD) --}}
@php
    /** @var int $total24h */
    /** @var float|int|string $growth24h */
    /** @var int $mutasiData */
    /** @var float|int|string $mutasiPercentage */
    /** @var int $sesiAuth */
    /** @var int $peringatanKritis */
@endphp
<section class="audit-kpi-grid" aria-label="Ringkasan Jejak Audit">
    {{-- Card 1: Total Peristiwa (24 Jam) --}}
    <div class="audit-kpi-card">
        <div class="audit-kpi-info">
            <span class="audit-kpi-label">Total Log (24 Jam)</span>
            <div class="audit-kpi-value">
                {{ number_format($total24h) }}
            </div>
            <div class="audit-kpi-subtext audit-subtext-emerald">
                <span>&uarr; {{ $growth24h }}%</span>
                <span class="audit-kpi-subtext-muted">24 Jam</span>
            </div>
        </div>
        <div class="audit-kpi-icon-wrap audit-icon-blue">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
        </div>
    </div>

    {{-- Card 2: Mutasi Data (CRUD Changes) --}}
    <div class="audit-kpi-card">
        <div class="audit-kpi-info">
            <span class="audit-kpi-label">Mutasi Data</span>
            <div class="audit-kpi-value">
                {{ number_format($mutasiData) }}
            </div>
            <div class="audit-kpi-subtext audit-subtext-blue">
                <span class="badge-dot-blue"></span>
                <span>{{ $mutasiPercentage }}%</span>
                <span class="audit-kpi-subtext-muted">Diff Valid</span>
            </div>
        </div>
        <div class="audit-kpi-icon-wrap audit-icon-indigo">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
            </svg>
        </div>
    </div>

    {{-- Card 3: Sesi Autentikasi --}}
    <div class="audit-kpi-card">
        <div class="audit-kpi-info">
            <span class="audit-kpi-label">Sesi Autentikasi</span>
            <div class="audit-kpi-value">
                {{ number_format($sesiAuth) }}
            </div>
            <div class="audit-kpi-subtext audit-subtext-emerald">
                <span>&check; 100% Sah</span>
                <span class="audit-kpi-subtext-muted">SSO &amp; RBAC</span>
            </div>
        </div>
        <div class="audit-kpi-icon-wrap audit-icon-emerald">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
        </div>
    </div>

    {{-- Card 4: Peringatan Kritis --}}
    <div class="audit-kpi-card">
        <div class="audit-kpi-info">
            <span class="audit-kpi-label">Peringatan Kritis</span>
            <div class="audit-kpi-value">
                {{ number_format($peringatanKritis) }}
            </div>
            <div class="audit-kpi-subtext audit-subtext-amber">
                <span class="badge-dot-amber"></span>
                <span>{{ $peringatanKritis > 0 ? 'Perlu Perhatian' : 'Terselesaikan' }}</span>
            </div>
        </div>
        <div class="audit-kpi-icon-wrap audit-icon-amber">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
    </div>
</section>
