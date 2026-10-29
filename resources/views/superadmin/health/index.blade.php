@extends('layouts.dashboard')

@section('title', 'Telemetri Kesehatan & Diagnostik Sistem - SuperAdmin MagKom')
@section('page_title', 'Panel Telemetri Kesehatan Infrastruktur')
@section('page_description', 'Pemantauan real-time latensi basis data, kapasitas storage, integritas server, dan suite pemeliharaan proaktif 1-klik.')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Telemetri Kesehatan</span>
@endsection

@section('page_actions')
    <div class="health-header-actions">
        <!-- Live SSE Stream Badge -->
        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span>Live SSE Active</span>
        </span>

        <a href="{{ route('superadmin.backup.index') }}" class="btn btn-default btn-sm health-btn-action focus-visible:ring-2 focus-visible:ring-[#3F51B5]">
            <i class="fa fa-hdd-o" aria-hidden="true"></i>
            <span>Backup Hub</span>
        </a>
        <a href="{{ route('superadmin.audit-log.index') }}" class="btn btn-dark btn-sm health-btn-action focus-visible:ring-2 focus-visible:ring-[#3F51B5]">
            <i class="fa fa-history" aria-hidden="true"></i>
            <span>Audit Trail Stream</span>
        </a>
    </div>
@endsection

@push('styles')
<style>
    /* ==========================================================================
       HEALTH TELEMETRY GENTELELLA PARITY STYLESHEET (DS-MAGKOM)
       ========================================================================== */

    .health-telemetry-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        width: 100%;
    }

    .health-header-actions {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .health-btn-action {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-weight: 700;
        border-radius: 6px;
        padding: 0.45rem 0.9rem;
    }

    /* Maintenance Alert Banner */
    .health-maintenance-banner {
        background: rgba(7, 81, 255, 0.06);
        border: 1px solid rgba(7, 81, 255, 0.2);
        border-left: 4px solid var(--blue-600, #0751FF);
        border-radius: 8px;
        padding: 0.875rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .health-maintenance-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 0.8125rem;
        color: var(--neutral-800, #1D2530);
    }

    .health-maintenance-icon {
        color: var(--blue-600, #0751FF);
        font-size: 1.125rem;
    }

    /* Hero Metric Cards (Gentelella Parity) */
    .health-metrics-row {
        margin-left: -10px;
        margin-right: -10px;
    }

    .health-metric-col {
        padding-left: 10px;
        padding-right: 10px;
        margin-bottom: 1rem;
    }

    .health-tile-card {
        background: var(--neutral-0, #FFFFFF);
        border: 1px solid var(--neutral-300, #E2E8F0);
        border-radius: 10px;
        padding: 1.125rem 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 110px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .health-tile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
    }

    .health-tile-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
    }

    .health-tile-card.border-teal::before { background: #10B981; }
    .health-tile-card.border-blue::before { background: #0751FF; }
    .health-tile-card.border-amber::before { background: #F59E0B; }
    .health-tile-card.border-purple::before { background: #7C3AED; }

    .health-tile-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
    }

    .health-tile-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--neutral-500, #7A8494);
        letter-spacing: 0.04em;
    }

    .health-tile-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
    }

    .icon-box-teal { background: rgba(16, 185, 129, 0.12); color: #059669; }
    .icon-box-blue { background: rgba(7, 81, 255, 0.12); color: #0751FF; }
    .icon-box-amber { background: rgba(245, 158, 11, 0.15); color: #212529; }
    .icon-box-purple { background: rgba(124, 58, 237, 0.12); color: #7C3AED; }

    .health-tile-value {
        font-size: 1.625rem;
        font-weight: 800;
        color: var(--neutral-900, #05070B);
        font-family: monospace;
        line-height: 1.15;
    }

    .health-tile-subtext {
        font-size: 0.75rem;
        color: var(--neutral-500, #7A8494);
        margin-top: 0.35rem;
    }

    /* Canvas Real-Time Chart & Tooltip */
    .telemetry-kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .telemetry-kpi-card {
        background: var(--neutral-100, #F8FAFC);
        border: 1px solid var(--neutral-300, #E2E8F0);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        display: flex;
        flex-direction: column;
    }

    .telemetry-kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--neutral-500, #7A8494);
    }

    .telemetry-kpi-value-row {
        display: flex;
        align-items: baseline;
        gap: 0.35rem;
        margin: 0.2rem 0;
    }

    .telemetry-kpi-num {
        font-size: 1.375rem;
        font-weight: 800;
        font-family: monospace;
    }

    .telemetry-kpi-unit {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--neutral-500, #7A8494);
    }

    .telemetry-kpi-sub {
        font-size: 0.7rem;
        color: var(--neutral-500, #7A8494);
    }

    .telemetry-canvas-wrapper {
        position: relative;
        width: 100%;
        background: #0B1120;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #1E293B;
    }

    #routeLatencyCanvas {
        display: block;
        width: 100%;
    }

    .telemetry-tooltip {
        position: absolute;
        display: none;
        background: rgba(15, 23, 42, 0.95);
        border: 1px solid rgba(56, 189, 248, 0.4);
        color: #F8FAFC;
        padding: 0.75rem;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        font-size: 0.75rem;
        pointer-events: none;
        z-index: 50;
        min-width: 220px;
        backdrop-filter: blur(6px);
    }

    .telemetry-legend-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-top: 0.875rem;
        padding-top: 0.75rem;
        border-top: 1px dashed var(--neutral-300, #E2E8F0);
        font-size: 0.75rem;
        color: var(--neutral-500, #7A8494);
    }

    .telemetry-legend-items {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .legend-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .chip-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .dot-fast { background: #10B981; }
    .dot-normal { background: #F59E0B; }
    .dot-slow { background: #EF4444; }

    .color-fast { color: #10B981 !important; }
    .color-normal { color: #212529 !important; }
    .color-slow { color: #EF4444 !important; }

    /* Resource Usage & Metering Panel */
    .resource-stat-list {
        display: flex;
        flex-direction: column;
        gap: 0.875rem;
        font-size: 0.8125rem;
    }

    .resource-stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 0.65rem;
        border-bottom: 1px dashed var(--neutral-200, #EEF0F5);
    }

    .resource-stat-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .resource-stat-label {
        color: var(--neutral-600, #4B5563);
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .resource-stat-val {
        font-weight: 700;
        color: var(--neutral-900, #05070B);
    }

    /* Top Accent Ribbon for Tables & Panels */
    .x_panel.has-table {
        border-top: 3px solid #01347C !important;
    }

    /* WCAG AA Amber-Gold Contrast Rule: Zero White Text on Amber Gold */
    .badge-amber-gold {
        background-color: #FFCB05 !important;
        color: #212529 !important;
        font-weight: 800;
    }

    /* Dark Mode Overrides */
    [data-theme="dark"] .health-tile-card {
        background: #111C2E !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] .health-tile-value {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .telemetry-kpi-card {
        background: #111C2E !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] .health-maintenance-banner {
        background: rgba(7, 81, 255, 0.1) !important;
        border-color: rgba(7, 81, 255, 0.3) !important;
        color: #F1F5F9 !important;
    }

    [data-theme="dark"] .resource-stat-item {
        border-bottom-color: #26354D !important;
    }

    [data-theme="dark"] .resource-stat-label {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .resource-stat-val {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .color-normal {
        color: #FDE68A !important;
    }
</style>
@endpush

@section('content')
<div class="health-telemetry-container">

    {{-- ALERT FEEDBACK NOTIFIKASI --}}
    @if(session('status_keberhasilan'))
        <x-gentelella.callout type="success" title="Operasi Berhasil">
            {{ session('status_keberhasilan') }}
        </x-gentelella.callout>
    @endif

    @if(session('status_kesalahan'))
        <x-gentelella.callout type="danger" title="Operasi Gagal">
            {{ session('status_kesalahan') }}
        </x-gentelella.callout>
    @endif

    {{-- 1. MASTER COCKPIT HUD & 6-METRIC COMMAND GRID (AC-1, AC-6) --}}
    <x-telemetry.health-hud
        :sla-status="$slaStatus"
        :sla-percent="$slaPercent"
        :db-latency="$dbLatency"
        :db-driver="$dbDriver"
        :total-tables="$totalTables"
        :p95-route-duration="$p95RouteDuration"
        :avg-route-duration="$avgRouteDuration"
        :total-route-requests="$totalRouteRequests"
        :audit24h="$audit24h"
        :total-audit="$totalAudit"
        :failed-jobs-count="$failedJobsCount"
        :cache-driver="$runtimeInfo['cache_driver'] ?? 'file'"
        :queue-driver="$runtimeInfo['queue_driver'] ?? 'sync'"
        :is-writable="$isWritable"
    />

    {{-- 2. SCHEDULED MAINTENANCE RUNBOOK ALERT BANNER --}}
    <div class="health-maintenance-banner">
        <div class="health-maintenance-content">
            <i class="fa fa-info-circle health-maintenance-icon" aria-hidden="true"></i>
            <div>
                <strong>Jadwal Pemeliharaan Berkala:</strong> Siklus pembersihan cache otomatis dan security hygiene sweep dijadwalkan setiap <strong>Jumat pukul 02:00 WIB</strong> (estimasi 15 detik non-degradasi).
            </div>
        </div>
        <div>
            <a href="#maintenanceSuite" class="btn btn-default btn-xs focus-visible:ring-2 focus-visible:ring-[#3F51B5]" style="font-weight: 700; border-radius: 4px;">
                <i class="fa fa-wrench text-primary" aria-hidden="true"></i> Suite Pemeliharaan
            </a>
        </div>
    </div>

    {{-- 3. 4 HERO METRIC STAT TILES (GENTELELLA STANDARDS) --}}
    <div class="row health-metrics-row">
        <!-- Tile 1: Uptime SLA -->
        <div class="col-md-3 col-sm-6 col-xs-12 health-metric-col">
            <div class="health-tile-card border-teal">
                <div class="health-tile-header">
                    <span class="health-tile-label">UPTIME SLA (30-DAY)</span>
                    <div class="health-tile-icon-box icon-box-teal">
                        <i class="fa fa-check-circle" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="health-tile-value text-success">{{ $slaPercent !== null ? $slaPercent . '%' : 'N/A' }}</div>
                <div class="health-tile-subtext">
                    <i class="fa fa-shield text-success" aria-hidden="true"></i> Status: {{ $slaStatus }}
                </div>
            </div>
        </div>

        <!-- Tile 2: P95 Tail Latency -->
        <div class="col-md-3 col-sm-6 col-xs-12 health-metric-col">
            <div class="health-tile-card border-blue">
                <div class="health-tile-header">
                    <span class="health-tile-label">P95 TAIL LATENCY</span>
                    <div class="health-tile-icon-box icon-box-blue">
                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="health-tile-value text-primary">{{ $p95RouteDuration }} ms</div>
                <div class="health-tile-subtext">
                    <i class="fa fa-line-chart text-info" aria-hidden="true"></i> Rata-rata: {{ $avgRouteDuration }} ms (SLO &lt; 200ms)
                </div>
            </div>
        </div>

        <!-- Tile 3: Total Request Trafik -->
        <div class="col-md-3 col-sm-6 col-xs-12 health-metric-col">
            <div class="health-tile-card border-amber">
                <div class="health-tile-header">
                    <span class="health-tile-label">TOTAL REQUEST TEREKAM</span>
                    <div class="health-tile-icon-box icon-box-amber">
                        <i class="fa fa-bolt" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="health-tile-value" style="color: #212529;">{{ number_format($totalRouteRequests) }}</div>
                <div class="health-tile-subtext">
                    <i class="fa fa-history text-warning" aria-hidden="true"></i> Event 24 Jam: {{ number_format($audit24h) }} log
                </div>
            </div>
        </div>

        <!-- Tile 4: Koneksi Basis Data & Ping (AC-6 Preserved) -->
        <div class="col-md-3 col-sm-6 col-xs-12 health-metric-col">
            <div class="health-tile-card border-purple">
                <div class="health-tile-header">
                    <span class="health-tile-label">KONEKSI BASIS DATA</span>
                    <div class="health-tile-icon-box icon-box-purple">
                        <i class="fa fa-database" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="health-tile-value" style="color: #7C3AED;">{{ $dbLatency }} ms</div>
                <div class="health-tile-subtext">
                    <i class="fa fa-server text-purple" aria-hidden="true"></i> Driver: {{ strtoupper($dbDriver) }} ({{ $totalTables }} Tabel)
                </div>
            </div>
        </div>
    </div>

    {{-- 4. ROW UTAMA: KURVA LATENSI 2D (8 KOLOM) & CACHE DONUT + STORAGE (4 KOLOM) --}}
    <div class="row">
        <!-- Kolom Kiri: Kurva Latensi Real-Time Canvas 2D SSE (8 Kolom) (AC-5, AC-6) -->
        <div class="col-md-8 col-sm-12 col-xs-12">
            <x-gentelella.x-panel
                title="<i class='fa fa-line-chart text-primary'></i> Telemetri Real-Time Waktu Eksekusi Route (Zero-Dependency Native HTML5 Canvas 2D)"
                subtitle="Visualisasi kurva performa HTTP request riil terhubung langsung dengan middleware database dan pembaruan live stream via Server-Sent Events (SSE)."
            >
                <x-slot:actions>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span id="telemetryPulseBadge" class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold border"
                              style="background: rgba(16, 185, 129, 0.12); color: #059669; border-color: rgba(16, 185, 129, 0.25);">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            <span id="telemetryStatusText">SSE Stream Live</span>
                        </span>
                    </div>
                </x-slot:actions>

                {{-- 4-KOLOM METRIK HERO TELEMETRI (AC-6 Preserved) --}}
                <div class="telemetry-kpi-grid">
                    <div class="telemetry-kpi-card">
                        <span class="telemetry-kpi-label">Rata-Rata Latensi</span>
                        <div class="telemetry-kpi-value-row">
                            <strong id="kpiAvgDuration" class="telemetry-kpi-num {{ $avgRouteDuration < 50 ? 'color-fast' : ($avgRouteDuration < 200 ? 'color-normal' : 'color-slow') }}">{{ (string) $avgRouteDuration }}</strong>
                            <span class="telemetry-kpi-unit">ms</span>
                        </div>
                        <span class="telemetry-kpi-sub">Mean runtime seluruh rute</span>
                    </div>

                    <div class="telemetry-kpi-card">
                        <span class="telemetry-kpi-label">P95 Tail Latency</span>
                        <div class="telemetry-kpi-value-row">
                            <strong id="kpiP95Duration" class="telemetry-kpi-num {{ $p95RouteDuration < 50 ? 'color-fast' : ($p95RouteDuration < 200 ? 'color-normal' : 'color-slow') }}">{{ (string) $p95RouteDuration }}</strong>
                            <span class="telemetry-kpi-unit">ms</span>
                        </div>
                        <span class="telemetry-kpi-sub">95% request lebih cepat dari ini</span>
                    </div>

                    <div class="telemetry-kpi-card">
                        <span class="telemetry-kpi-label">Latensi Puncak (Max)</span>
                        <div class="telemetry-kpi-value-row">
                            <strong id="kpiMaxDuration" class="telemetry-kpi-num {{ $maxRouteDuration < 200 ? 'color-normal' : 'color-slow' }}">{{ (string) $maxRouteDuration }}</strong>
                            <span class="telemetry-kpi-unit">ms</span>
                        </div>
                        <span class="telemetry-kpi-sub">Titik beban eksekusi tertinggi</span>
                    </div>

                    <div class="telemetry-kpi-card">
                        <span class="telemetry-kpi-label">Total Sampel Request</span>
                        <div class="telemetry-kpi-value-row">
                            <strong id="kpiTotalRequests" class="telemetry-kpi-num text-primary">{{ (string) number_format($totalRouteRequests) }}</strong>
                            <span class="telemetry-kpi-unit">req</span>
                        </div>
                        <span class="telemetry-kpi-sub">Terekam via terminable middleware</span>
                    </div>
                </div>

                {{-- KANVAS GRAFIK 2D & TOOLTIP INTERAKTIF --}}
                <div class="telemetry-canvas-wrapper" id="canvasContainer">
                    <canvas id="routeLatencyCanvas" height="280"></canvas>
                    <div id="canvasTooltip" class="telemetry-tooltip"></div>
                    <div id="canvasEmptyState" style="{{ count($routeMetrics) > 0 ? 'display: none;' : '' }} padding: 3rem; text-align: center; color: #94A3B8;">
                        <i class="fa fa-info-circle fa-2x mb-2 text-info" aria-hidden="true"></i>
                        <div>Memulai pencatatan telemetri... Silakan navigasikan rute atau refresh untuk melihat kurva secara real-time.</div>
                    </div>
                </div>

                {{-- LEGENDA THRESHOLD & STATUS BARIS --}}
                <div class="telemetry-legend-bar">
                    <div class="telemetry-legend-items">
                        <span class="legend-chip"><span class="chip-dot dot-fast"></span> Optimal (&lt; 50 ms)</span>
                        <span class="legend-chip"><span class="chip-dot dot-normal"></span> Normal (50 - 200 ms)</span>
                        <span class="legend-chip"><span class="chip-dot dot-slow"></span> Latensi Tinggi (&gt; 200 ms)</span>
                    </div>
                    <div>
                        <span id="canvasLastUpdate">Pembaruan: Baru saja</span> &bull;
                        <span id="canvasPointCount">{{ (string) count($routeMetrics) }} Titik Sampel Aktif</span>
                    </div>
                </div>

                {{-- TABEL AKSESIBILITAS WCAG AA TERSEMBUNYI UNTUK SCREEN READER --}}
                <div class="sa-sr-only">
                    <table>
                        <caption>Tabel Riwayat Latensi Route Telemetri Terakhir</caption>
                        <thead>
                            <tr>
                                <th scope="col">Waktu</th>
                                <th scope="col">Metode</th>
                                <th scope="col">Rute</th>
                                <th scope="col">Status</th>
                                <th scope="col">Durasi (ms)</th>
                            </tr>
                        </thead>
                        <tbody id="srTableBody">
                            @foreach($routeMetrics as $m)
                                <tr>
                                    <td>{{ $m['time'] }}</td>
                                    <td>{{ $m['method'] }}</td>
                                    <td>{{ $m['route_name'] }}</td>
                                    <td>{{ (string) $m['status_code'] }}</td>
                                    <td>{{ (string) $m['duration_ms'] }} ms</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-gentelella.x-panel>
        </div>

        <!-- Kolom Kanan: Penggunaan Sumber Daya & Storage (4 Kolom) (AC-4, AC-6) -->
        <div class="col-md-4 col-sm-12 col-xs-12">
            <!-- Storage & Kapasitas Disk (AC-6 Preserved) -->
            <x-gentelella.x-panel
                title="<i class='fa fa-hdd-o text-success'></i> Storage & Kapasitas Disk"
                subtitle="Alokasi disk server, cache, dan volume penyimpanan privat MagKom."
            >
                <!-- Metering Disk Bar -->
                <div style="margin-bottom: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; font-weight: 700; margin-bottom: 0.4rem;">
                        <span style="color: var(--neutral-600, #4B5563);">Kapasitas Disk Terpakai:</span>
                        <span class="text-primary">{{ $diskUsedPercent }}% (Sisa {{ $diskFreeGb }} GB)</span>
                    </div>
                    <div class="progress progress_sm" style="height: 10px; margin-bottom: 0;">
                        <div class="progress-bar {{ $diskUsedPercent > 85 ? 'bg-red' : ($diskUsedPercent > 70 ? 'bg-orange' : 'bg-green') }}"
                             role="progressbar"
                             style="width: {{ min(100, $diskUsedPercent) }}%;"
                             aria-valuenow="{{ $diskUsedPercent }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>

                <!-- Rincian Kapasitas & Berkas -->
                <div class="resource-stat-list">
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">
                            <i class="fa fa-folder-open text-primary" aria-hidden="true"></i> Total Berkas Dokumen:
                        </span>
                        <span class="resource-stat-val">{{ number_format($totalDokumenCount) }} Berkas</span>
                    </div>
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">
                            <i class="fa fa-file-pdf-o text-danger" aria-hidden="true"></i> Ukuran Berkas Dokumen:
                        </span>
                        <span class="resource-stat-val text-primary">{{ $totalDokumenMb }} MB</span>
                    </div>
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">
                            <i class="fa fa-pencil text-warning" aria-hidden="true"></i> Status Izin Tulis:
                        </span>
                        <span class="badge {{ $isWritable ? 'badge-success' : 'badge-danger' }}" style="font-size: 0.72rem;">
                            {{ $isWritable ? 'Izin Tulis Aktif' : 'Read-Only' }}
                        </span>
                    </div>
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">
                            <i class="fa fa-bolt text-warning" aria-hidden="true"></i> Driver Cache Sistem:
                        </span>
                        <span class="badge badge-primary" style="font-size: 0.72rem;">
                            {{ strtoupper($runtimeInfo['cache_driver']) }}
                        </span>
                    </div>
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">
                            <i class="fa fa-database text-info" aria-hidden="true"></i> Tabel Basis Data:
                        </span>
                        <span class="resource-stat-val">{{ $totalTables }} Tabel</span>
                    </div>
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">
                            <i class="fa fa-archive text-purple" aria-hidden="true"></i> Snapshot Backup:
                        </span>
                        <span class="resource-stat-val" style="font-size: 0.75rem;">{{ $lastBackupTime ?? 'Belum ada cadangan' }}</span>
                    </div>
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">
                            <i class="fa fa-check-square-o text-success" aria-hidden="true"></i> Status Basis Data:
                        </span>
                        <span class="badge {{ $dbStatus === 'OK' ? 'badge-success' : 'badge-danger' }}" style="font-size: 0.72rem;">
                            {{ $dbStatus === 'OK' ? 'Terhubung (OK)' : 'Bermasalah' }}
                        </span>
                    </div>
                </div>
            </x-gentelella.x-panel>
        </div>
    </div>

    {{-- 5. ROW ENTERPRISE DATA TABLE: TELEMETRI RUTE HTTP TERKINI (12 KOLOM) (AC-4) --}}
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <x-telemetry.endpoint-table :route-metrics="$routeMetrics" />
        </div>
    </div>

    {{-- 6. ROW STATUS LAYANAN (6 KOLOM) & SUITE PEMELIHARAAN 1-KLIK (6 KOLOM) (AC-3, AC-6) --}}
    <div class="row">
        <!-- Kolom Kiri: Status Layanan Inti MagKom (6 Kolom) -->
        <div class="col-md-6 col-sm-12 col-xs-12">
            <x-gentelella.x-panel
                title="<i class='fa fa-server text-info'></i> Status Layanan Inti MagKom"
                subtitle="Kondisi ketersediaan sub-sistem utama aplikasi."
            >
                <div class="resource-stat-list">
                    <div class="resource-stat-item">
                        <div>
                            <strong style="color: var(--neutral-900, #05070B);">Basis Data MySQL/MariaDB</strong>
                            <div class="text-muted" style="font-size: 0.72rem;">Driver: {{ strtoupper($dbDriver) }} &bull; Latensi Ping: {{ $dbLatency }} ms</div>
                        </div>
                        <span class="badge badge-success" style="font-size: 0.72rem;">
                            <i class="fa fa-circle text-white" aria-hidden="true"></i> {{ $dbStatus === 'OK' ? 'Operasional' : 'Degraded' }}
                        </span>
                    </div>

                    <div class="resource-stat-item">
                        <div>
                            <strong style="color: var(--neutral-900, #05070B);">Private Storage Berkas</strong>
                            <div class="text-muted" style="font-size: 0.72rem;">Path: storage/app/berkas &bull; {{ number_format($totalDokumenCount) }} Dokumen</div>
                        </div>
                        <span class="badge {{ $isWritable ? 'badge-success' : 'badge-danger' }}" style="font-size: 0.72rem;">
                            <i class="fa fa-circle text-white" aria-hidden="true"></i> {{ $isWritable ? 'Operasional' : 'Read-Only' }}
                        </span>
                    </div>

                    <div class="resource-stat-item">
                        <div>
                            <strong style="color: var(--neutral-900, #05070B);">SSE Real-Time Stream Engine</strong>
                            <div class="text-muted" style="font-size: 0.72rem;">Endpoint: /realtime/stream &bull; Push Notification Aktif</div>
                        </div>
                        <span class="badge badge-success" style="font-size: 0.72rem;">
                            <i class="fa fa-circle text-white" aria-hidden="true"></i> Operasional
                        </span>
                    </div>

                    <div class="resource-stat-item">
                        <div>
                            <strong style="color: var(--neutral-900, #05070B);">Background Queue Worker</strong>
                            <div class="text-muted" style="font-size: 0.72rem;">Driver: {{ $runtimeInfo['queue_driver'] }} &bull; Antrean Eksekusi</div>
                        </div>
                        <span class="badge {{ $failedJobsCount === 0 ? 'badge-success' : 'badge-danger' }}" style="font-size: 0.72rem;">
                            <i class="fa fa-circle text-white" aria-hidden="true"></i> {{ $failedJobsCount === 0 ? 'Operasional (0 Gagal)' : "$failedJobsCount Gagal" }}
                        </span>
                    </div>

                    <div class="resource-stat-item">
                        <div>
                            <strong style="color: var(--neutral-900, #05070B);">Pembersih Higienitas Keamanan</strong>
                            <div class="text-muted" style="font-size: 0.72rem;">Sanitasi token dan berkas sementara kadaluarsa</div>
                        </div>
                        <span class="badge {{ ($sweeperStats['pending_tokens'] === 0 && $sweeperStats['pending_temp_files'] === 0) ? 'badge-success' : 'badge-warning' }}" style="font-size: 0.72rem;">
                            <i class="fa fa-circle text-white" aria-hidden="true"></i> {{ ($sweeperStats['pending_tokens'] === 0 && $sweeperStats['pending_temp_files'] === 0) ? 'Higienis' : 'Perlu Sweep' }}
                        </span>
                    </div>
                </div>
            </x-gentelella.x-panel>
        </div>

        <!-- Kolom Kanan: Suite Pemeliharaan Cepat Proaktif (6 Kolom) (AC-3, AC-6) -->
        <div class="col-md-6 col-sm-12 col-xs-12">
            <x-telemetry.maintenance-suite
                :last-sweep-at="$sweeperStats['last_sweep_at'] ?? null"
            />
        </div>
    </div>

    {{-- 7. ROW RUNTIME & HIGIENITAS KEAMANAN (3 KOLOM: 4 - 4 - 4) --}}
    <div class="row">
        <!-- Kolom 1: Runtime Framework -->
        <div class="col-md-4 col-sm-6 col-xs-12">
            <x-gentelella.x-panel title="<i class='fa fa-terminal text-primary'></i> Runtime Framework" subtitle="Versi interpreter & environment">
                <div class="resource-stat-list">
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">PHP Runtime:</span>
                        <span class="resource-stat-val">v{{ $runtimeInfo['php_version'] }}</span>
                    </div>
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">Laravel Framework:</span>
                        <span class="resource-stat-val">v{{ $runtimeInfo['laravel_version'] }}</span>
                    </div>
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">Environment:</span>
                        <span class="badge badge-primary">{{ strtoupper($runtimeInfo['app_env']) }}</span>
                    </div>
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">Debug Mode:</span>
                        <span class="badge {{ $runtimeInfo['app_debug'] ? 'badge-warning' : 'badge-success' }}">
                            {{ $runtimeInfo['app_debug'] ? 'AKTIF' : 'NONAKTIF' }}
                        </span>
                    </div>
                </div>
            </x-gentelella.x-panel>
        </div>

        <!-- Kolom 2: Background Jobs & Queue -->
        <div class="col-md-4 col-sm-6 col-xs-12">
            <x-gentelella.x-panel title="<i class='fa fa-tasks text-info'></i> Antrean & Background Jobs" subtitle="Pemrosesan task asynchronous">
                <div class="resource-stat-list">
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">Queue Driver:</span>
                        <span class="resource-stat-val font-mono">{{ $runtimeInfo['queue_driver'] }}</span>
                    </div>
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">Failed Jobs:</span>
                        <span class="badge {{ $failedJobsCount > 0 ? 'badge-danger' : 'badge-success' }}">
                            {{ $failedJobsCount }} Gagal
                        </span>
                    </div>
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">Session Driver:</span>
                        <span class="resource-stat-val font-mono">{{ $runtimeInfo['session_driver'] }}</span>
                    </div>
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">Total Log Audit:</span>
                        <span class="resource-stat-val text-success">{{ number_format($totalAudit) }} Log</span>
                    </div>
                </div>
            </x-gentelella.x-panel>
        </div>

        <!-- Kolom 3: Higienitas Keamanan (Sweeper) -->
        <div class="col-md-4 col-sm-12 col-xs-12">
            <x-gentelella.x-panel title="<i class='fa fa-shield text-danger'></i> Higienitas Keamanan" subtitle="Pembersihan residu berkas & token">
                <div class="resource-stat-list">
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">Token Reset Expired:</span>
                        <span class="badge {{ $sweeperStats['pending_tokens'] > 0 ? 'badge-warning' : 'badge-success' }}">
                            {{ $sweeperStats['pending_tokens'] }} Token
                        </span>
                    </div>
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">Berkas ZIP Sementara (>2h):</span>
                        <span class="badge {{ $sweeperStats['pending_temp_files'] > 0 ? 'badge-warning' : 'badge-success' }}">
                            {{ $sweeperStats['pending_temp_files'] }} File
                        </span>
                    </div>
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">Pendaftar Unverified (>72h):</span>
                        <span class="badge {{ $sweeperStats['pending_unverified_users'] > 0 ? 'badge-danger' : 'badge-success' }}">
                            {{ $sweeperStats['pending_unverified_users'] }} User
                        </span>
                    </div>
                    <div class="resource-stat-item">
                        <span class="resource-stat-label">Sweep Terakhir:</span>
                        <span class="resource-stat-val" style="font-size: 0.72rem;">{{ $sweeperStats['last_sweep_at'] ?? 'Belum ada' }}</span>
                    </div>
                </div>
            </x-gentelella.x-panel>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function() {
    'use strict';

    // Inisialisasi Data Awal dari Server
    let telemetryData = @json($routeMetrics);
    let totalRequestsCount = {{ $totalRouteRequests }};

    const canvas = document.getElementById('routeLatencyCanvas');
    const container = document.getElementById('canvasContainer');
    const tooltip = document.getElementById('canvasTooltip');
    const emptyHint = document.getElementById('canvasEmptyState');
    const pointCountEl = document.getElementById('canvasPointCount');
    const lastUpdateEl = document.getElementById('canvasLastUpdate');

    if (!canvas || !container) return;

    const ctx = canvas.getContext('2d');
    let activePoint = null;
    let computedPoints = [];

    // Konfigurasi Padding & Margin Kanvas
    const padding = { top: 35, right: 35, bottom: 45, left: 60 };

    /**
     * Hitung ulang dimensi kanvas dengan devicePixelRatio untuk tampilan Retina/HiDPI tajam.
     */
    function resizeCanvas() {
        const rect = container.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;
        canvas.width = Math.floor(rect.width * dpr);
        canvas.height = Math.floor(rect.height * dpr);

        if (ctx.resetTransform) {
            ctx.resetTransform();
        } else {
            ctx.setTransform(1, 0, 0, 1, 0, 0);
        }
        ctx.scale(dpr, dpr);
        draw();
    }

    /**
     * Engine Penggambaran Native 2D Canvas (Zero-Dependency)
     */
    function draw() {
        const rect = container.getBoundingClientRect();
        const width = rect.width;
        const height = rect.height;

        ctx.clearRect(0, 0, width, height);

        if (!telemetryData || telemetryData.length === 0) {
            drawGrid(width, height, 100);
            if (emptyHint) emptyHint.style.display = 'block';
            return;
        } else {
            if (emptyHint) emptyHint.style.display = 'none';
        }

        const plotW = width - padding.left - padding.right;
        const plotH = height - padding.top - padding.bottom;
        const bottomY = height - padding.bottom;

        // Tentukan batas skala sumbu Y dinamis
        const rawMax = Math.max(...telemetryData.map(d => Number(d.duration_ms) || 0), 40);
        const ceilVal = Math.max(50, Math.ceil((rawMax * 1.25) / 25) * 25);

        // 1. Gambar Garis Grid Horizontal & Sumbu
        drawGrid(width, height, ceilVal);

        // 2. Pemetaan Titik Koordinat (X, Y)
        computedPoints = telemetryData.map((d, i) => {
            const ratioX = telemetryData.length > 1 ? (i / (telemetryData.length - 1)) : 0.5;
            const x = padding.left + (ratioX * plotW);
            const ratioY = Math.min(1, Math.max(0, (Number(d.duration_ms) || 0) / ceilVal));
            const y = bottomY - (ratioY * plotH);
            return {
                ...d,
                x: x,
                y: y
            };
        });

        // 3. Gambar Kurva Bézier & Gradien Area Bawah
        if (computedPoints.length >= 2) {
            ctx.beginPath();
            ctx.moveTo(computedPoints[0].x, bottomY);
            ctx.lineTo(computedPoints[0].x, computedPoints[0].y);

            for (let i = 0; i < computedPoints.length - 1; i++) {
                const p0 = computedPoints[i];
                const p1 = computedPoints[i + 1];
                const cx = p0.x + (p1.x - p0.x) / 2;
                ctx.bezierCurveTo(cx, p0.y, cx, p1.y, p1.x, p1.y);
            }

            ctx.lineTo(computedPoints[computedPoints.length - 1].x, bottomY);
            ctx.closePath();

            const areaGrad = ctx.createLinearGradient(0, padding.top, 0, bottomY);
            areaGrad.addColorStop(0, 'rgba(7, 81, 255, 0.38)');
            areaGrad.addColorStop(1, 'rgba(7, 81, 255, 0.0)');
            ctx.fillStyle = areaGrad;
            ctx.fill();

            // Garis Utama Kurva (Sky Blue / Cyan Neon)
            ctx.beginPath();
            ctx.moveTo(computedPoints[0].x, computedPoints[0].y);
            for (let i = 0; i < computedPoints.length - 1; i++) {
                const p0 = computedPoints[i];
                const p1 = computedPoints[i + 1];
                const cx = p0.x + (p1.x - p0.x) / 2;
                ctx.bezierCurveTo(cx, p0.y, cx, p1.y, p1.x, p1.y);
            }
            ctx.strokeStyle = '#38BDF8';
            ctx.lineWidth = 2.5;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.stroke();
        }

        // 4. Gambar Titik-titik Data (Color-Coded Thresholds)
        computedPoints.forEach((p, idx) => {
            const isLatest = idx === computedPoints.length - 1;
            const ms = Number(p.duration_ms) || 0;
            let pointColor = '#10B981'; // < 50ms Optimal
            if (ms >= 200) {
                pointColor = '#EF4444'; // > 200ms Tinggi
            } else if (ms >= 50) {
                pointColor = '#F59E0B'; // 50-200ms Normal
            }

            // Lingkaran Titik Data
            ctx.beginPath();
            ctx.arc(p.x, p.y, isLatest ? 4.5 : 3.5, 0, Math.PI * 2);
            ctx.fillStyle = pointColor;
            ctx.fill();
            ctx.strokeStyle = '#0F172A';
            ctx.lineWidth = 1.5;
            ctx.stroke();

            // Efek Suar Beacon pada Titik Terakhir (Real-Time Pulse)
            if (isLatest) {
                ctx.beginPath();
                ctx.arc(p.x, p.y, 8, 0, Math.PI * 2);
                ctx.strokeStyle = 'rgba(56, 189, 248, 0.55)';
                ctx.lineWidth = 1.5;
                ctx.stroke();
            }
        });

        // 5. Gambar Label Sumbu X (Waktu Sampel)
        drawXAxisLabels(width, height);

        // 6. Gambar Crosshair Interaktif jika Kursor Berada di Titik Data
        if (activePoint) {
            drawCrosshair(activePoint, width, height);
        }
    }

    /**
     * Menggambar Garis Grid Horizontal & Skala Sumbu Y
     */
    function drawGrid(width, height, ceilVal) {
        const plotW = width - padding.left - padding.right;
        const plotH = height - padding.top - padding.bottom;
        const gridSteps = 4;

        ctx.font = '11px monospace';
        ctx.fillStyle = '#64748B';
        ctx.textAlign = 'right';
        ctx.textBaseline = 'middle';

        for (let i = 0; i <= gridSteps; i++) {
            const val = Math.round((ceilVal / gridSteps) * (gridSteps - i));
            const y = padding.top + (i / gridSteps) * plotH;

            ctx.beginPath();
            ctx.setLineDash([4, 4]);
            ctx.strokeStyle = 'rgba(51, 65, 85, 0.45)';
            ctx.lineWidth = 1;
            ctx.moveTo(padding.left, y);
            ctx.lineTo(width - padding.right, y);
            ctx.stroke();
            ctx.setLineDash([]);

            ctx.fillText(`${val} ms`, padding.left - 8, y);
        }
    }

    /**
     * Menggambar Label Waktu Sumbu X secara Proporsional
     */
    function drawXAxisLabels(width, height) {
        if (computedPoints.length < 2) return;

        const bottomY = height - padding.bottom;
        ctx.font = '10px monospace';
        ctx.fillStyle = '#64748B';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';

        const step = Math.max(1, Math.floor(computedPoints.length / 4));
        for (let i = 0; i < computedPoints.length; i += step) {
            const p = computedPoints[i];
            if (p && p.time) {
                ctx.fillText(p.time, p.x, bottomY + 12);
            }
        }

        const lastP = computedPoints[computedPoints.length - 1];
        if (lastP && lastP.time) {
            ctx.fillText(lastP.time, lastP.x, bottomY + 12);
        }
    }

    /**
     * Menggambar Crosshair Vertikal dan Highlight pada Titik Terpilih
     */
    function drawCrosshair(p, width, height) {
        const bottomY = height - padding.bottom;

        ctx.beginPath();
        ctx.setLineDash([3, 3]);
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.45)';
        ctx.lineWidth = 1;
        ctx.moveTo(p.x, padding.top);
        ctx.lineTo(p.x, bottomY);
        ctx.stroke();
        ctx.setLineDash([]);

        ctx.beginPath();
        ctx.arc(p.x, p.y, 9, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(56, 189, 248, 0.35)';
        ctx.fill();

        ctx.beginPath();
        ctx.arc(p.x, p.y, 4.5, 0, Math.PI * 2);
        ctx.fillStyle = '#FFFFFF';
        ctx.fill();
    }

    /**
     * Deteksi Interaksi Mouse Hover untuk Menampilkan Tooltip
     */
    container.addEventListener('mousemove', function(e) {
        if (computedPoints.length === 0) return;

        const rect = container.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;

        let closest = null;
        let minDist = 40;

        for (let p of computedPoints) {
            const dist = Math.hypot(p.x - mouseX, p.y - mouseY);
            if (dist < minDist) {
                minDist = dist;
                closest = p;
            }
        }

        if (closest) {
            activePoint = closest;
            showTooltip(closest, rect.width, rect.height);
            requestAnimationFrame(draw);
        } else if (activePoint) {
            activePoint = null;
            hideTooltip();
            requestAnimationFrame(draw);
        }
    });

    container.addEventListener('mouseleave', function() {
        if (activePoint) {
            activePoint = null;
            hideTooltip();
            requestAnimationFrame(draw);
        }
    });

    /**
     * Render dan Posisikan Tooltip Interaktif (XSS-Safe DOM Nodes)
     */
    function showTooltip(p, containerWidth, containerHeight) {
        const ms = Number(p.duration_ms) || 0;
        const statusCode = Number(p.status_code) || 200;
        const method = String(p.method || 'GET').toUpperCase();

        let statusBadgeClass = statusCode >= 500 ? 'badge-danger' : (statusCode >= 400 ? 'badge-warning' : 'badge-success');
        let methodBadgeClass = method === 'POST' ? 'badge-success' : (method === 'DELETE' ? 'badge-danger' : (method === 'PUT' ? 'badge-warning' : 'badge-primary'));

        // Clear existing children safely
        while (tooltip.firstChild) {
            tooltip.removeChild(tooltip.firstChild);
        }

        const header = document.createElement('div');
        header.style.cssText = 'display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem; border-bottom: 1px solid rgba(255,255,255,0.12); padding-bottom: 0.35rem;';

        const methodBadge = document.createElement('span');
        methodBadge.className = 'badge ' + methodBadgeClass;
        methodBadge.style.fontSize = '0.6875rem';
        methodBadge.textContent = method;

        const timeSpan = document.createElement('span');
        timeSpan.style.cssText = 'font-family: monospace; font-size: 0.72rem; color: #94A3B8;';
        timeSpan.textContent = String(p.time || '');

        header.appendChild(methodBadge);
        header.appendChild(timeSpan);

        const routeDiv = document.createElement('div');
        routeDiv.style.cssText = 'font-weight: 700; color: #F8FAFC; font-size: 0.8125rem; word-break: break-all; margin-bottom: 0.25rem;';
        routeDiv.textContent = String(p.route_name || p.uri || '-');

        const uriDiv = document.createElement('div');
        uriDiv.style.cssText = 'font-family: monospace; font-size: 0.6875rem; color: #94A3B8; margin-bottom: 0.5rem;';
        uriDiv.textContent = String(p.uri || '');

        const durBox = document.createElement('div');
        durBox.style.cssText = 'display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.3); padding: 0.35rem 0.5rem; border-radius: 6px;';
        const durLabel = document.createElement('span');
        durLabel.textContent = 'Durasi Eksekusi:';
        const durVal = document.createElement('strong');
        durVal.style.cssText = 'font-size: 0.875rem; font-family: monospace; color: ' + (ms < 50 ? '#34D399' : (ms < 200 ? '#FBBF24' : '#F87171')) + ';';
        durVal.textContent = ms.toFixed(2) + ' ms';
        durBox.appendChild(durLabel);
        durBox.appendChild(durVal);

        const metaRow = document.createElement('div');
        metaRow.style.cssText = 'display: flex; justify-content: space-between; align-items: center; margin-top: 0.35rem; font-size: 0.72rem; color: #CBD5E1;';
        const statusSpan = document.createElement('span');
        statusSpan.textContent = 'Status: ';
        const statusStrong = document.createElement('strong');
        statusStrong.className = 'badge ' + statusBadgeClass;
        statusStrong.textContent = String(statusCode);
        statusSpan.appendChild(statusStrong);

        const memSpan = document.createElement('span');
        memSpan.textContent = 'Memori: ';
        const memStrong = document.createElement('strong');
        memStrong.textContent = (Number(p.memory_mb) || 0).toFixed(2) + ' MB';
        memSpan.appendChild(memStrong);

        metaRow.appendChild(statusSpan);
        metaRow.appendChild(memSpan);

        tooltip.appendChild(header);
        tooltip.appendChild(routeDiv);
        tooltip.appendChild(uriDiv);
        tooltip.appendChild(durBox);
        tooltip.appendChild(metaRow);

        let tx = p.x + 15;
        if (tx + 230 > containerWidth) {
            tx = p.x - 240;
        }
        let ty = Math.max(10, Math.min(p.y - 50, containerHeight - 140));

        tooltip.style.left = `${tx}px`;
        tooltip.style.top = `${ty}px`;
        tooltip.style.display = 'block';
    }

    function hideTooltip() {
        tooltip.style.display = 'none';
    }

    /**
     * Hitung Ulang Metrik Ringkasan KPI di DOM saat ada Event Realtime
     */
    function recalculateKPIs(newItem) {
        totalRequestsCount++;
        const totalReqEl = document.getElementById('kpiTotalRequests');
        if (totalReqEl) totalReqEl.textContent = new Intl.NumberFormat().format(totalRequestsCount);

        const durations = telemetryData.map(d => Number(d.duration_ms) || 0).sort((a, b) => a - b);
        const count = durations.length;
        if (count === 0) return;

        const sum = durations.reduce((a, b) => a + b, 0);
        const avg = (sum / count).toFixed(2);
        const max = Math.max(...durations).toFixed(2);

        const p95Idx = Math.floor(count * 0.95);
        const p95 = (durations[Math.min(p95Idx, count - 1)] || 0).toFixed(2);

        const avgEl = document.getElementById('kpiAvgDuration');
        if (avgEl) {
            avgEl.textContent = avg;
            avgEl.className = 'telemetry-kpi-num ' + (avg < 50 ? 'color-fast' : (avg < 200 ? 'color-normal' : 'color-slow'));
        }

        const p95El = document.getElementById('kpiP95Duration');
        if (p95El) {
            p95El.textContent = p95;
            p95El.className = 'telemetry-kpi-num ' + (p95 < 50 ? 'color-fast' : (p95 < 200 ? 'color-normal' : 'color-slow'));
        }

        const maxEl = document.getElementById('kpiMaxDuration');
        if (maxEl) {
            maxEl.textContent = max;
            maxEl.className = 'telemetry-kpi-num ' + (max < 200 ? 'color-normal' : 'color-slow');
        }

        if (pointCountEl) {
            pointCountEl.textContent = `${count} Titik Sampel Aktif`;
        }
        if (lastUpdateEl) {
            lastUpdateEl.textContent = `Pembaruan: ${newItem.time || 'Baru saja'}`;
        }
    }

    /**
     * Animasi Visual Feedback Live Badge saat Event SSE Tiba
     */
    function flashLiveBadge() {
        const badge = document.getElementById('telemetryPulseBadge');
        const text = document.getElementById('telemetryStatusText');
        if (!badge || !text) return;

        text.textContent = 'Data Live Masuk!';
        badge.style.background = 'rgba(56, 189, 248, 0.25)';
        badge.style.color = '#38BDF8';

        setTimeout(() => {
            text.textContent = 'SSE Stream Live';
            badge.style.background = 'rgba(16, 185, 129, 0.12)';
            badge.style.color = '#059669';
        }, 1200);
    }

    /**
     * Tambahkan Baris Data ke Tabel Tersembunyi Aksesibilitas WCAG (XSS-Safe DOM Nodes)
     */
    function prependSrTableRow(item) {
        const tbody = document.getElementById('srTableBody');
        if (!tbody) return;

        const tr = document.createElement('tr');

        const tdTime = document.createElement('td');
        tdTime.textContent = String(item.time || '');

        const tdMethod = document.createElement('td');
        tdMethod.textContent = String(item.method || '');

        const tdRoute = document.createElement('td');
        tdRoute.textContent = String(item.route_name || item.uri || '');

        const tdStatus = document.createElement('td');
        tdStatus.textContent = String(item.status_code || '');

        const tdDuration = document.createElement('td');
        tdDuration.textContent = String(item.duration_ms || '') + ' ms';

        tr.appendChild(tdTime);
        tr.appendChild(tdMethod);
        tr.appendChild(tdRoute);
        tr.appendChild(tdStatus);
        tr.appendChild(tdDuration);

        tbody.insertBefore(tr, tbody.firstChild);

        while (tbody.children.length > 50) {
            tbody.removeChild(tbody.lastChild);
        }
    }

    /**
     * Listener Real-Time Server-Sent Events (SSE)
     */
    window.addEventListener('magkom:telemetry-route', function(event) {
        const item = event.detail;
        if (!item || item.duration_ms === undefined) return;

        telemetryData.push(item);
        if (telemetryData.length > 50) {
            telemetryData.shift();
        }

        recalculateKPIs(item);
        flashLiveBadge();
        prependSrTableRow(item);
        requestAnimationFrame(draw);
    });

    // Inisialisasi Event Resize & First Render
    window.addEventListener('resize', resizeCanvas);
    document.addEventListener('DOMContentLoaded', resizeCanvas);

    setTimeout(resizeCanvas, 50);

    // Pantau Perubahan Tema (Dark/Light Mode) untuk Redraw Warna Kanvas
    const themeObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(m) {
            if (m.attributeName === 'data-theme') {
                requestAnimationFrame(draw);
            }
        });
    });
    themeObserver.observe(document.documentElement, { attributes: true });

})();
</script>
@endpush
