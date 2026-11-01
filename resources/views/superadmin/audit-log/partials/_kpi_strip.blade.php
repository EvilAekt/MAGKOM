{{-- SECTION 2: GENTELELLA V4 4-UP AUDIT KPI METRICS STRIP --}}
@php
    /** @var int $total24h */
    /** @var float|int|string $growth24h */
    /** @var int $mutasiData */
    /** @var float|int|string $mutasiPercentage */
    /** @var int $sesiAuth */
    /** @var int $peringatanKritis */

    $growthNum = (float) $growth24h;
    $growthPositive = $growthNum >= 0;
    $growthLabel = rtrim(rtrim(number_format(abs($growthNum), 1, '.', ''), '0'), '.');
@endphp
<section class="audit-kpi-row" aria-label="Ringkasan Jejak Audit">
    {{-- Card 1: Total Log (24 Jam) --}}
    <div class="audit-stat-card">
        <div class="stat">
            <div class="stat-icon green" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Total Log (24 Jam)</div>
                <div class="stat-value-row">
                    <span class="stat-value">{{ number_format($total24h) }}</span>
                    <span class="stat-change {{ $growthPositive ? 'up' : 'down' }}">
                        {!! $growthPositive ? '&uarr;' : '&darr;' !!} {{ $growthLabel }}%
                    </span>
                </div>
                <div class="stat-subtext">24-hr rolling activity stream</div>
            </div>
        </div>
    </div>

    {{-- Card 2: Mutasi Data (CRUD Changes) --}}
    <div class="audit-stat-card">
        <div class="stat">
            <div class="stat-icon blue" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Mutasi Data</div>
                <div class="stat-value-row">
                    <span class="stat-value">{{ number_format($mutasiData) }}</span>
                    <span class="stat-change up">{{ $mutasiPercentage }}%</span>
                </div>
                <div class="stat-subtext">Diff state valid &amp; verified</div>
            </div>
        </div>
    </div>

    {{-- Card 3: Sesi Autentikasi --}}
    <div class="audit-stat-card">
        <div class="stat">
            <div class="stat-icon purple" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <polyline points="17 11 19 13 23 9"></polyline>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Sesi Autentikasi</div>
                <div class="stat-value-row">
                    <span class="stat-value">{{ number_format($sesiAuth) }}</span>
                    <span class="stat-change up">&check; 100%</span>
                </div>
                <div class="stat-subtext">Sah via SSO &amp; RBAC guard</div>
            </div>
        </div>
    </div>

    {{-- Card 4: Peringatan Kritis --}}
    <div class="audit-stat-card">
        <div class="stat">
            <div class="stat-icon {{ $peringatanKritis > 0 ? 'red' : 'yellow' }}" aria-hidden="true">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">Peringatan Kritis</div>
                <div class="stat-value-row">
                    <span class="stat-value">{{ number_format($peringatanKritis) }}</span>
                    <span class="stat-change {{ $peringatanKritis > 0 ? 'down' : 'up' }}">
                        {{ $peringatanKritis > 0 ? 'Perlu Aksi' : '✓ Aman' }}
                    </span>
                </div>
                <div class="stat-subtext">
                    {{ $peringatanKritis > 0 ? 'Perlu tindakan segera' : 'Zero unresolved security alert' }}
                </div>
            </div>
        </div>
    </div>
</section>
