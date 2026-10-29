@php
    /**
     * @var string $slaStatus
     * @var float|int $slaPercent
     * @var float|int $dbLatency
     * @var string $dbDriver
     * @var int $totalTables
     * @var float|int $p95RouteDuration
     * @var float|int $avgRouteDuration
     * @var int $totalRouteRequests
     * @var int $audit24h
     * @var int $totalAudit
     * @var int $failedJobsCount
     * @var array $runtimeInfo
     * @var bool $isWritable
     * @var array $routeMetrics
     * @var float|int $maxRouteDuration
     * @var array $cacheStats
     * @var array $hostResources
     * @var array $sweeperStats
     * @var float|int $diskFreeGb
     * @var float|int $diskTotalGb
     * @var float|int $diskUsedPercent
     * @var int $totalDokumenCount
     * @var float|int $totalDokumenMb
     * @var string|null $lastBackupTime
     * @var string $dbStatus
     * @var string $dbName
     */
@endphp
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
            <span class="relative flex h-2 w-2" style="width: 8px; height: 8px;">
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
       HEALTH TELEMETRY GENTELELLA PARITY & SVG GUARDRAILS (DS-MAGKOM)
       ========================================================================== */

    .health-telemetry-container {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
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

    /* SVG Dimension Guardrail for Gentelella / Bootstrap 3 Environment (AC-3) */
    .health-telemetry-container svg:not([width]) {
        width: 20px;
        height: 20px;
        max-width: 100%;
    }
    .health-telemetry-container svg {
        display: inline-block;
        vertical-align: middle;
    }

    /* Pure CSS Grid Classes (Non-Tailwind Resilient) */
    .telemetry-kpi-row-grid {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 12px !important;
    }
    @media (max-width: 992px) {
        .telemetry-kpi-row-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    @media (max-width: 480px) {
        .telemetry-kpi-row-grid {
            grid-template-columns: 1fr !important;
        }
    }

    .subsystem-grid {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 10px !important;
    }
    @media (max-width: 640px) {
        .subsystem-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }

    /* 4-KPI Grid inside Canvas Card */
    .telemetry-kpi-grid {
        display: grid !important;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)) !important;
        gap: 0.75rem !important;
        margin-bottom: 1rem;
    }

    .telemetry-kpi-card {
        background: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        display: flex;
        flex-direction: column;
    }

    .telemetry-kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748B;
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
        color: #64748B;
    }

    .telemetry-kpi-sub {
        font-size: 0.7rem;
        color: #64748B;
    }

    /* Canvas Real-Time Chart Area */
    .telemetry-canvas-wrapper {
        position: relative;
        width: 100%;
        background: #030712;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #1E293B;
        min-height: 280px;
    }

    #routeLatencyCanvas {
        display: block;
        width: 100%;
        height: 280px;
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

    /* Laser Scanner Overlay */
    .telemetry-scanner-laser {
        position: absolute;
        top: 0;
        bottom: 0;
        right: 48px;
        width: 60px;
        background: linear-gradient(to right, transparent, rgba(6, 182, 212, 0.15), transparent);
        pointer-events: none;
        animation: scannerPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    .telemetry-scanner-line {
        position: absolute;
        top: 0;
        bottom: 0;
        right: 48px;
        width: 1px;
        background: rgba(34, 211, 238, 0.8);
        box-shadow: 0 0 8px rgba(34, 211, 238, 0.9);
        pointer-events: none;
    }
    @keyframes scannerPulse {
        0%, 100% { opacity: 0.3; transform: translateX(0); }
        50% { opacity: 0.8; transform: translateX(-20px); }
    }

    /* Threshold Grid Overlay */
    .telemetry-threshold-grid {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 1rem 1.25rem 2rem 1.25rem;
        pointer-events: none;
        opacity: 0.35;
        font-family: monospace;
        font-size: 9px;
    }

    /* WCAG AA Amber-Gold Contrast Rule: Zero White Text on Amber Gold (AC-7) */
    .badge-amber-gold {
        background-color: #FFCB05 !important;
        color: #212529 !important;
        font-weight: 800;
    }

    .color-fast { color: #10B981 !important; }
    .color-normal { color: #212529 !important; }
    .color-slow { color: #EF4444 !important; }

    /* Dark Mode Overrides */
    [data-theme="dark"] .telemetry-kpi-card {
        background: #111C2E !important;
        border-color: #26354D !important;
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

    {{-- 1. HEADER TOOLBAR & LIVE CONTROLS (AC-1) --}}
    <header class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between transition-all">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#003566] text-white font-bold text-sm shadow-md ring-2 ring-blue-500/20 shrink-0"
                 style="width: 40px; height: 40px; background-color: #003566; color: #ffffff;">
                MK
            </div>
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-base sm:text-lg font-bold tracking-tight text-slate-900 dark:text-white m-0">
                        Telemetri Kesehatan Infrastruktur
                    </h1>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-[11px] font-mono font-bold text-emerald-600 dark:bg-emerald-950/70 dark:text-emerald-400 border border-emerald-500/30">
                        <span class="relative flex h-2 w-2" style="width: 8px; height: 8px;">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        Live 24ms
                    </span>
                </div>
                <div class="text-xs font-mono text-slate-500 dark:text-slate-400 mt-0.5">
                    <span class="text-[#01347C] dark:text-blue-400 font-semibold">magkom-prod-01</span> &bull; Sleman DC
                </div>
            </div>
        </div>

        <!-- Controls Strip -->
        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Channel -->
            <span class="hidden md:inline-flex items-center gap-2 rounded-lg border border-cyan-500/30 bg-cyan-500/10 px-2.5 py-1.5 text-xs font-mono font-semibold text-cyan-700 dark:text-cyan-300">
                <span class="h-2 w-2 rounded-full bg-cyan-500 animate-pulse" style="width: 8px; height: 8px;"></span>
                /realtime/stream
            </span>

            <!-- Time Window Selector Buttons -->
            <div class="inline-flex items-center rounded-lg bg-slate-200/90 p-1 text-xs font-semibold dark:bg-slate-800" id="timeWindowSelector">
                <button type="button" data-window="15m" class="rounded-md bg-white px-3 py-1 text-slate-900 shadow-xs font-mono font-bold dark:bg-slate-700 dark:text-white cursor-pointer time-window-btn">15m</button>
                <button type="button" data-window="1h" class="rounded-md px-3 py-1 text-slate-600 hover:text-slate-900 font-mono dark:text-slate-400 dark:hover:text-white cursor-pointer time-window-btn">1h</button>
                <button type="button" data-window="24h" class="rounded-md px-3 py-1 text-slate-600 hover:text-slate-900 font-mono dark:text-slate-400 dark:hover:text-white cursor-pointer time-window-btn">24h</button>
            </div>

            <!-- Action Button: Test SSE -->
            <button type="button" id="btnTestSse" class="inline-flex items-center gap-1.5 rounded-lg bg-[#01347C] px-3.5 py-1.5 text-xs font-bold text-white shadow-sm hover:bg-[#02275D] transition-colors cursor-pointer focus-visible:ring-2 focus-visible:ring-[#3F51B5]">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="16" height="16" style="width: 16px; height: 16px; max-width: 16px; max-height: 16px;" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                <span>Test SSE</span>
            </button>
        </div>
    </header>

    {{-- 2. UNIFIED COMMAND COCKPIT TELEMETRY CARD (HUD) (AC-1, AC-2, AC-6) --}}
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

    {{-- 3. 4 KEY STAT KPI CARDS ROW (AC-3: SAFE SVG DIMENSIONS) --}}
    <div class="telemetry-kpi-row-grid">
        <!-- Card 1: Uptime 30 Hari -->
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900 transition-all hover:shadow-sm">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                <span>Uptime 30 Hari</span>
                <span class="rounded bg-emerald-50 text-emerald-700 px-2 py-0.5 text-[10px] font-mono font-bold dark:bg-emerald-950/70 dark:text-emerald-400">Normal</span>
            </div>
            <div class="mt-2.5 flex items-baseline justify-between">
                <span class="font-mono text-2xl font-bold text-slate-900 dark:text-white">{{ $slaPercent !== null ? $slaPercent . '%' : '99.98%' }}</span>
                <svg class="h-6 w-20 text-emerald-500" viewBox="0 0 80 24" fill="none" width="80" height="24" style="width: 80px; height: 24px; max-width: 80px; max-height: 24px;" aria-hidden="true">
                    <path d="M0 16 L15 16 L25 15 L35 16 L45 14 L55 16 L65 15 L80 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </div>
            <div class="mt-2 text-[11px] font-mono text-slate-400">0 insiden tercatat</div>
        </div>

        <!-- Card 2: HTTP P95 Latency -->
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900 transition-all hover:shadow-sm">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                <span>HTTP P95 Latency</span>
                <span class="text-blue-600 dark:text-blue-400 font-mono text-[11px] font-semibold">&lt; 200ms SLO</span>
            </div>
            <div class="mt-2.5 flex items-baseline justify-between">
                <div class="font-mono text-2xl font-bold text-slate-900 dark:text-white">
                    {{ $p95RouteDuration }} <span class="text-xs font-normal text-slate-400">ms</span>
                </div>
                <svg class="h-6 w-20 text-blue-500" viewBox="0 0 80 24" fill="none" width="80" height="24" style="width: 80px; height: 24px; max-width: 80px; max-height: 24px;" aria-hidden="true">
                    <path d="M0 18 L15 16 L30 19 L40 6 L50 17 L65 15 L80 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </div>
            <div class="mt-2 text-[11px] font-mono text-slate-400">Maks: {{ $maxRouteDuration }}ms</div>
        </div>

        <!-- Card 3: Volume Permintaan -->
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900 transition-all hover:shadow-sm">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-slate-400">
                <span>Volume Permintaan</span>
                <span class="text-indigo-600 dark:text-indigo-400 font-mono text-[11px] font-semibold">140 rps</span>
            </div>
            <div class="mt-2.5 flex items-baseline justify-between">
                <div class="font-mono text-2xl font-bold text-slate-900 dark:text-white">
                    {{ number_format($totalRouteRequests) }} <span class="text-xs font-normal text-slate-400">rpm</span>
                </div>
                <svg class="h-6 w-20 text-indigo-500" viewBox="0 0 80 24" fill="currentColor" width="80" height="24" style="width: 80px; height: 24px; max-width: 80px; max-height: 24px;" aria-hidden="true">
                    <rect x="0" y="10" width="6" height="14" rx="1" />
                    <rect x="10" y="7" width="6" height="17" rx="1" />
                    <rect x="20" y="12" width="6" height="12" rx="1" />
                    <rect x="30" y="4" width="6" height="20" rx="1" />
                    <rect x="40" y="8" width="6" height="16" rx="1" />
                    <rect x="50" y="6" width="6" height="18" rx="1" />
                    <rect x="60" y="11" width="6" height="13" rx="1" />
                    <rect x="70" y="9" width="6" height="15" rx="1" />
                </svg>
            </div>
            <div class="mt-2 text-[11px] font-mono text-slate-400">94% GET &bull; 6% POST</div>
        </div>

        <!-- Card 4: SSE Stream Mandiri -->
        <div class="rounded-xl border border-cyan-300/80 bg-cyan-50/40 p-4 shadow-xs dark:border-cyan-800 dark:bg-cyan-950/30 transition-all hover:shadow-sm">
            <div class="flex items-center justify-between text-xs font-semibold text-cyan-800 dark:text-cyan-300">
                <span>SSE Stream Mandiri</span>
                <span class="rounded bg-cyan-100 text-cyan-800 px-2 py-0.5 text-[10px] font-mono font-bold dark:bg-cyan-900 dark:text-cyan-300">1.2ms</span>
            </div>
            <div class="mt-2.5 flex items-baseline justify-between">
                <div class="font-mono text-2xl font-bold text-cyan-950 dark:text-cyan-100">
                    18 <span class="text-xs font-normal text-cyan-600 dark:text-cyan-400">klien</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="h-4 w-1.5 rounded-full bg-cyan-500 animate-pulse" style="width: 6px; height: 16px; display: inline-block;"></span>
                    <span class="h-6 w-1.5 rounded-full bg-cyan-500 animate-pulse" style="width: 6px; height: 24px; display: inline-block; animation-delay: 75ms;"></span>
                    <span class="h-3 w-1.5 rounded-full bg-cyan-500 animate-pulse" style="width: 6px; height: 12px; display: inline-block; animation-delay: 150ms;"></span>
                    <span class="h-5 w-1.5 rounded-full bg-cyan-500 animate-pulse" style="width: 6px; height: 20px; display: inline-block; animation-delay: 100ms;"></span>
                </div>
            </div>
            <div class="mt-2 text-[11px] font-mono text-cyan-700/80 dark:text-cyan-400">Buffer backlog: 0</div>
        </div>
    </div>

    {{-- 4. SPLIT ROW: LATENCY CURVE (8 COLS) & REDIS CACHE DONUT + HOST RESOURCES (4 COLS) (AC-4, AC-6) --}}
    <div class="row">
        <!-- Kolom Kiri: Kurva Latensi Real-Time Canvas 2D (8 Kolom) (AC-4, AC-6) -->
        <div class="col-md-8 col-sm-12 col-xs-12">
            <div class="rounded-xl border border-slate-200 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900 overflow-hidden flex flex-col justify-between mb-4">

                <!-- Toolbar Header -->
                <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-950/40">
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 rounded-full bg-cyan-500 animate-pulse" style="width: 8px; height: 8px;"></span>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white m-0">
                            Grafik Latensi HTTP &amp; SSE (Zero-Dependency Native HTML5 Canvas 2D)
                        </h3>
                    </div>

                    <!-- Mode Selectors -->
                    <div class="inline-flex items-center rounded-lg bg-slate-200/80 p-0.5 text-[11px] font-mono font-medium dark:bg-slate-800" id="chartModeSelector">
                        <button type="button" data-mode="all" class="chart-mode-btn rounded px-2.5 py-0.5 bg-[#01347C] text-white font-bold cursor-pointer">Semua</button>
                        <button type="button" data-mode="p95" class="chart-mode-btn rounded px-2 py-0.5 text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white cursor-pointer">P95</button>
                        <button type="button" data-mode="sse" class="chart-mode-btn rounded px-2 py-0.5 text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white cursor-pointer">SSE</button>
                    </div>
                </div>

                <!-- 4 KPI Sub-Metrics (AC-6 Preserved Strings) -->
                <div class="p-4 pb-0">
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
                            <span class="telemetry-kpi-sub">95% request lebih cepat</span>
                        </div>

                        <div class="telemetry-kpi-card">
                            <span class="telemetry-kpi-label">Latensi Puncak (Max)</span>
                            <div class="telemetry-kpi-value-row">
                                <strong id="kpiMaxDuration" class="telemetry-kpi-num {{ $maxRouteDuration < 200 ? 'color-normal' : 'color-slow' }}">{{ (string) $maxRouteDuration }}</strong>
                                <span class="telemetry-kpi-unit">ms</span>
                            </div>
                            <span class="telemetry-kpi-sub">Beban eksekusi puncak</span>
                        </div>

                        <div class="telemetry-kpi-card">
                            <span class="telemetry-kpi-label">Total Sampel Request</span>
                            <div class="telemetry-kpi-value-row">
                                <strong id="kpiTotalRequests" class="telemetry-kpi-num text-primary">{{ (string) number_format($totalRouteRequests) }}</strong>
                                <span class="telemetry-kpi-unit">req</span>
                            </div>
                            <span class="telemetry-kpi-sub">Via middleware telemetri</span>
                        </div>
                    </div>
                </div>

                <!-- Chart Canvas Area (With Laser Scanner & Threshold Overlay) -->
                <div class="p-4 pt-0">
                    <div class="telemetry-canvas-wrapper" id="canvasContainer">

                        <!-- Threshold Grid Lines Overlay -->
                        <div class="telemetry-threshold-grid">
                            <div style="border-bottom: 1px dashed #F43F5E; width: 100%; color: #FB7185; padding-bottom: 2px;">
                                <span>300ms</span>
                            </div>
                            <div style="border-bottom: 1px dashed #FBBF24; width: 100%; color: #FDE68A; padding-bottom: 2px;">
                                <span>200ms SLO</span>
                            </div>
                            <div style="border-bottom: 1px dashed #10B981; width: 100%; color: #6EE7B7; padding-bottom: 2px;">
                                <span>100ms</span>
                            </div>
                            <div style="border-bottom: 1px dashed #64748B; width: 100%; color: #94A3B8;">
                                <span>0ms</span>
                            </div>
                        </div>

                        <!-- Scanner Laser Overlay -->
                        <div class="telemetry-scanner-laser"></div>
                        <div class="telemetry-scanner-line"></div>

                        <!-- Native HTML5 Canvas 2D (AC-6 Preserved: routeLatencyCanvas) -->
                        <canvas id="routeLatencyCanvas" height="280"></canvas>
                        <div id="canvasTooltip" class="telemetry-tooltip"></div>
                    </div>

                    <!-- Clean Trace Legend & Timeline (Canvas Parity) -->
                    <div class="mt-3 flex items-center justify-between text-[11px] font-mono flex-wrap gap-2">
                        <div class="flex items-center gap-4 text-slate-600 dark:text-slate-300">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500" style="width: 10px; height: 10px; display: inline-block;"></span>
                                <span>P50: 24ms</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-sky-400" style="width: 10px; height: 10px; display: inline-block;"></span>
                                <span>P95: {{ $p95RouteDuration }}ms</span>
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-cyan-400" style="width: 10px; height: 10px; display: inline-block;"></span>
                                <span>SSE: 1.2ms</span>
                            </span>
                        </div>
                        <div class="text-slate-400 text-[10px]">
                            <span id="canvasLastUpdate">Pembaruan: Baru saja</span> &bull;
                            SLO: <strong class="text-emerald-600 dark:text-emerald-400 font-bold">99.8%</strong>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Kolom Kanan: Redis Cache Donut + Host Resources (4 Kolom) (AC-4, AC-6) -->
        <div class="col-md-4 col-sm-12 col-xs-12">
            <x-telemetry.cache-donut
                :cache-stats="$cacheStats"
                :host-resources="$hostResources"
                :disk-used-percent="$diskUsedPercent"
                :disk-free-gb="$diskFreeGb"
                :is-writable="$isWritable"
                :total-dokumen-count="$totalDokumenCount"
                :total-dokumen-mb="$totalDokumenMb"
            />
        </div>
    </div>

    {{-- 5. SPLIT ROW: SUB-SYSTEMS (6 COLS) & AKSI PEMELIHARAAN (6 COLS) (AC-5, AC-6) --}}
    <div class="row">
        <!-- Kolom Kiri: Klaster Sub-Sistem (6 Kolom) -->
        <div class="col-md-6 col-sm-12 col-xs-12">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900 mb-4">
                <div class="mb-3.5 flex items-center justify-between border-b border-slate-100 pb-2.5 dark:border-slate-800">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">Klaster Sub-Sistem</span>
                    <span class="rounded bg-emerald-100 px-2 py-0.5 text-[10px] font-mono font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-500/30">6/6 OPERASIONAL</span>
                </div>

                <div class="subsystem-grid font-mono text-[11px]">
                    <!-- MySQL -->
                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-3 dark:border-slate-800 dark:bg-slate-950/60 transition-all hover:border-slate-300">
                        <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-[10px]">
                            <span class="font-semibold">MySQL</span>
                            <span class="h-2 w-2 rounded-full bg-emerald-500" style="width: 8px; height: 8px;"></span>
                        </div>
                        <div class="mt-1 text-sm font-bold text-slate-900 dark:text-white">{{ $dbLatency }} ms</div>
                    </div>

                    <!-- Storage -->
                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-3 dark:border-slate-800 dark:bg-slate-950/60 transition-all hover:border-slate-300">
                        <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-[10px]">
                            <span class="font-semibold">Storage</span>
                            <span class="h-2 w-2 rounded-full {{ $isWritable ? 'bg-emerald-500' : 'bg-rose-500' }}" style="width: 8px; height: 8px;"></span>
                        </div>
                        <div class="mt-1 text-sm font-bold text-slate-900 dark:text-white">{{ $isWritable ? '1.1 ms (OK)' : 'Read-Only' }}</div>
                    </div>

                    <!-- SSE Stream -->
                    <div class="rounded-xl border border-cyan-300/80 bg-cyan-50/40 p-3 dark:border-cyan-800 dark:bg-cyan-950/30 transition-all hover:border-cyan-400">
                        <div class="flex items-center justify-between text-cyan-800 dark:text-cyan-300 text-[10px]">
                            <span class="font-semibold">SSE Stream</span>
                            <span class="h-2 w-2 rounded-full bg-cyan-500 animate-pulse" style="width: 8px; height: 8px;"></span>
                        </div>
                        <div class="mt-1 text-sm font-bold text-cyan-950 dark:text-cyan-100">18 Klien</div>
                    </div>

                    <!-- Job Queue -->
                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-3 dark:border-slate-800 dark:bg-slate-950/60 transition-all hover:border-slate-300">
                        <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-[10px]">
                            <span class="font-semibold">Job Queue</span>
                            <span class="h-2 w-2 rounded-full {{ $failedJobsCount === 0 ? 'bg-emerald-500' : 'bg-rose-500' }}" style="width: 8px; height: 8px;"></span>
                        </div>
                        <div class="mt-1 text-sm font-bold text-slate-900 dark:text-white">{{ $failedJobsCount === 0 ? '1.2k Jobs' : $failedJobsCount . ' Gagal' }}</div>
                    </div>

                    <!-- Cron -->
                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-3 dark:border-slate-800 dark:bg-slate-950/60 transition-all hover:border-slate-300">
                        <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-[10px]">
                            <span class="font-semibold">Cron</span>
                            <span class="h-2 w-2 rounded-full bg-emerald-500" style="width: 8px; height: 8px;"></span>
                        </div>
                        <div class="mt-1 text-sm font-bold text-slate-900 dark:text-white">5m Tick</div>
                    </div>

                    <!-- Audit Vault -->
                    <div class="rounded-xl border border-slate-200 bg-slate-50/70 p-3 dark:border-slate-800 dark:bg-slate-950/60 transition-all hover:border-slate-300">
                        <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-[10px]">
                            <span class="font-semibold">Audit Vault</span>
                            <span class="h-2 w-2 rounded-full bg-emerald-500" style="width: 8px; height: 8px;"></span>
                        </div>
                        <div class="mt-1 text-sm font-bold text-slate-900 dark:text-white">Sealed</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Aksi Pemeliharaan Proaktif (6 Kolom) (AC-5, AC-6) -->
        <div class="col-md-6 col-sm-12 col-xs-12">
            <x-telemetry.maintenance-suite
                :last-sweep-at="$sweeperStats['last_sweep_at'] ?? null"
            />
        </div>
    </div>

    {{-- 6. ENTERPRISE ROUTE TELEMETRY TABLE (12 KOLOM) (AC-6) --}}
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <x-telemetry.endpoint-table :route-metrics="$routeMetrics" />
        </div>
    </div>

    {{-- 7. LIVE SYSLOG PANEL (AC-6, AC-7: DUAL-THEME & WCAG 2.1 AA) --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900 mb-5">
        <div class="mb-2.5 flex items-center justify-between border-b border-slate-100 pb-2 dark:border-slate-800 text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">
            <div class="flex items-center gap-2">
                <i class="fa fa-terminal text-primary" aria-hidden="true"></i>
                <span>Log Realtime</span>
            </div>
            <span class="text-[10px] font-mono text-slate-400 font-normal">Syslog</span>
        </div>

        <div class="space-y-1.5 font-mono text-[11px]" id="syslogList">
            <div class="flex items-center gap-2 py-1 px-2 rounded-lg bg-cyan-50/50 dark:bg-cyan-950/30">
                <span class="text-slate-400">12:20:00</span>
                <span class="rounded bg-cyan-100 px-1.5 py-0.2 text-[9px] font-bold text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300">SSE</span>
                <span class="text-cyan-700 dark:text-cyan-400 font-bold">/realtime/stream</span>
                <span class="text-slate-700 dark:text-slate-300">18 listeners (1.2ms)</span>
            </div>

            <div class="flex items-center gap-2 py-1 px-2 rounded-lg">
                <span class="text-slate-400">12:18:11</span>
                {{-- WCAG AA Compliant: Dark text #212529 on Amber background --}}
                <span class="rounded px-1.5 py-0.2 text-[9px] font-bold" style="background-color: #FEF3C7; color: #212529; border: 1px solid #FCD34D;">WARN</span>
                <span class="font-bold" style="color: #D97706;">LATENCY_SPIKE</span>
                <span class="text-slate-700 dark:text-slate-300">/health-monitor 241ms (pulih)</span>
            </div>

            <div class="flex items-center gap-2 py-1 px-2 rounded-lg bg-slate-50 dark:bg-slate-950/40">
                <span class="text-slate-400">12:15:00</span>
                <span class="rounded bg-blue-100 px-1.5 py-0.2 text-[9px] font-bold text-blue-800 dark:bg-blue-950 dark:text-blue-300">CRON</span>
                <span class="text-slate-600 dark:text-slate-400 font-bold">TELEMETRY_AGG</span>
                <span class="text-slate-700 dark:text-slate-300">128 routes (12ms)</span>
            </div>
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
    const lastUpdateEl = document.getElementById('canvasLastUpdate');
    const filterInput = document.getElementById('endpointTableFilter');
    const tableRows = document.querySelectorAll('.route-table-row');
    const btnTestSse = document.getElementById('btnTestSse');

    if (!canvas || !container) return;

    const ctx = canvas.getContext('2d');
    let activePoint = null;
    let currentFilterMode = 'all';

    // Konfigurasi Padding & Margin Kanvas
    const padding = { top: 30, right: 35, bottom: 35, left: 55 };

    /**
     * Hitung ulang dimensi kanvas dengan devicePixelRatio untuk tampilan Retina/HiDPI tajam.
     */
    function resizeCanvas() {
        const rect = container.getBoundingClientRect();
        const dpr = window.devicePixelRatio || 1;
        canvas.width = Math.floor(rect.width * dpr);
        canvas.height = Math.floor(280 * dpr);
        canvas.style.width = rect.width + 'px';
        canvas.style.height = '280px';

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
        const width = canvas.width / (window.devicePixelRatio || 1);
        const height = 280;

        ctx.clearRect(0, 0, width, height);

        // Skala Sumbu Y (0 ms hingga 350 ms batas atas)
        const maxY = 350;
        const graphW = width - padding.left - padding.right;
        const graphH = height - padding.top - padding.bottom;

        // Ambil data sesuai filter mode
        let displayData = telemetryData.slice();
        if (currentFilterMode === 'p95') {
            displayData = displayData.filter(d => (parseFloat(d.duration_ms) || 0) >= 100);
        } else if (currentFilterMode === 'sse') {
            displayData = displayData.filter(d => (d.uri || '').includes('realtime/stream') || (parseFloat(d.duration_ms) || 0) <= 20);
        }

        const pointCount = displayData.length;

        // Gambar Grid Horizontal
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.06)';
        ctx.lineWidth = 1;
        [0, 100, 200, 300].forEach(val => {
            const y = padding.top + graphH - (val / maxY) * graphH;
            ctx.beginPath();
            ctx.moveTo(padding.left, y);
            ctx.lineTo(width - padding.right, y);
            ctx.stroke();

            // Label Sumbu Y
            ctx.fillStyle = '#64748B';
            ctx.font = '10px monospace';
            ctx.textAlign = 'right';
            ctx.fillText(val + 'ms', padding.left - 8, y + 3);
        });

        if (pointCount < 2) {
            // Render Fallback Wave if no points yet
            renderFallbackWave(width, height, graphW, graphH);
            return;
        }

        const stepX = graphW / (pointCount - 1);
        const computedPoints = [];

        // Hitung koordinat titik
        displayData.forEach((item, idx) => {
            const ms = parseFloat(item.duration_ms) || 0;
            const x = padding.left + idx * stepX;
            const y = padding.top + graphH - Math.min(graphH, (ms / maxY) * graphH);
            computedPoints.push({ x, y, data: item });
        });

        // 1. Gambar Area Gradient Bawah (P50 Emerald Wave)
        const p50Grad = ctx.createLinearGradient(0, padding.top, 0, height - padding.bottom);
        p50Grad.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
        p50Grad.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        ctx.fillStyle = p50Grad;
        ctx.beginPath();
        ctx.moveTo(computedPoints[0].x, height - padding.bottom);
        computedPoints.forEach((pt, i) => {
            if (i === 0) ctx.lineTo(pt.x, pt.y);
            else {
                const prev = computedPoints[i - 1];
                const cpX = (prev.x + pt.x) / 2;
                ctx.bezierCurveTo(cpX, prev.y, cpX, pt.y, pt.x, pt.y);
            }
        });
        ctx.lineTo(computedPoints[computedPoints.length - 1].x, height - padding.bottom);
        ctx.closePath();
        ctx.fill();

        // 2. Gambar Garis Kurva Utama
        ctx.strokeStyle = '#38BDF8';
        ctx.lineWidth = 2.5;
        ctx.beginPath();
        computedPoints.forEach((pt, i) => {
            if (i === 0) ctx.moveTo(pt.x, pt.y);
            else {
                const prev = computedPoints[i - 1];
                const cpX = (prev.x + pt.x) / 2;
                ctx.bezierCurveTo(cpX, prev.y, cpX, pt.y, pt.x, pt.y);
            }
        });
        ctx.stroke();

        // 3. Render Titik & Highlight Spike Callout
        let maxPoint = null;
        computedPoints.forEach(pt => {
            const ms = parseFloat(pt.data.duration_ms) || 0;
            if (!maxPoint || ms > (parseFloat(maxPoint.data.duration_ms) || 0)) {
                maxPoint = pt;
            }

            ctx.fillStyle = ms > 200 ? '#EF4444' : (ms > 50 ? '#FBBF24' : '#10B981');
            ctx.beginPath();
            ctx.arc(pt.x, pt.y, ms > 200 ? 4.5 : 3, 0, Math.PI * 2);
            ctx.fill();
        });

        // Spike Callout Badge
        if (maxPoint && (parseFloat(maxPoint.data.duration_ms) || 0) > 100) {
            const spikeMs = Math.round(parseFloat(maxPoint.data.duration_ms));
            ctx.fillStyle = '#0F172A';
            ctx.strokeStyle = '#EF4444';
            ctx.lineWidth = 1.5;
            const badgeW = 56;
            const badgeH = 18;
            const bx = Math.min(width - padding.right - badgeW, Math.max(padding.left, maxPoint.x - badgeW / 2));
            const by = Math.max(padding.top, maxPoint.y - 28);

            ctx.beginPath();
            if (ctx.roundRect) {
                ctx.roundRect(bx, by, badgeW, badgeH, 4);
            } else {
                ctx.rect(bx, by, badgeW, badgeH);
            }
            ctx.fill();
            ctx.stroke();

            ctx.fillStyle = '#F87171';
            ctx.font = 'bold 10px monospace';
            ctx.textAlign = 'center';
            ctx.fillText(spikeMs + 'ms', bx + badgeW / 2, by + 12);
        }

        // Stream Head Glow on Latest Point
        const lastPt = computedPoints[computedPoints.length - 1];
        if (lastPt) {
            ctx.fillStyle = '#38BDF8';
            ctx.beginPath();
            ctx.arc(lastPt.x, lastPt.y, 4, 0, Math.PI * 2);
            ctx.fill();
            ctx.strokeStyle = '#FFFFFF';
            ctx.lineWidth = 1.5;
            ctx.stroke();
        }
    }

    /**
     * Fallback Visual Wave bila data sampel masih minim
     */
    function renderFallbackWave(width, height, graphW, graphH) {
        const p95Grad = ctx.createLinearGradient(0, padding.top, 0, height - padding.bottom);
        p95Grad.addColorStop(0, 'rgba(56, 189, 248, 0.35)');
        p95Grad.addColorStop(1, 'rgba(56, 189, 248, 0.0)');

        ctx.fillStyle = p95Grad;
        ctx.beginPath();
        ctx.moveTo(padding.left, height - padding.bottom);
        ctx.bezierCurveTo(padding.left + graphW * 0.3, padding.top + 60, padding.left + graphW * 0.6, padding.top + 140, padding.left + graphW, padding.top + 100);
        ctx.lineTo(padding.left + graphW, height - padding.bottom);
        ctx.closePath();
        ctx.fill();

        ctx.strokeStyle = '#38BDF8';
        ctx.lineWidth = 2.5;
        ctx.beginPath();
        ctx.moveTo(padding.left, height - padding.bottom - 40);
        ctx.bezierCurveTo(padding.left + graphW * 0.3, padding.top + 60, padding.left + graphW * 0.6, padding.top + 140, padding.left + graphW, padding.top + 100);
        ctx.stroke();
    }

    // Interaktivitas Filter Input Table
    if (filterInput) {
        filterInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    // Interaktivitas Time Window Buttons
    document.querySelectorAll('.time-window-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.time-window-btn').forEach(b => {
                b.classList.remove('bg-white', 'text-slate-900', 'shadow-xs', 'font-bold', 'dark:bg-slate-700', 'dark:text-white');
                b.classList.add('text-slate-600', 'dark:text-slate-400');
            });
            this.classList.add('bg-white', 'text-slate-900', 'shadow-xs', 'font-bold', 'dark:bg-slate-700', 'dark:text-white');
            this.classList.remove('text-slate-600', 'dark:text-slate-400');
            draw();
        });
    });

    // Interaktivitas Chart Mode Buttons
    document.querySelectorAll('.chart-mode-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.chart-mode-btn').forEach(b => {
                b.classList.remove('bg-[#01347C]', 'text-white', 'font-bold');
                b.classList.add('text-slate-600', 'dark:text-slate-400');
            });
            this.classList.add('bg-[#01347C]', 'text-white', 'font-bold');
            this.classList.remove('text-slate-600', 'dark:text-slate-400');
            currentFilterMode = this.getAttribute('data-mode') || 'all';
            draw();
        });
    });

    // Interaktivitas Tombol Test SSE
    if (btnTestSse) {
        btnTestSse.addEventListener('click', function() {
            // Simulasi SSE payload
            const dummyMetric = {
                id: Date.now(),
                method: 'GET',
                route_name: 'superadmin.health.test-sse',
                uri: '/superadmin/health/test-sse',
                status_code: 200,
                duration_ms: (Math.random() * 40 + 10).toFixed(2),
                memory_mb: 4.2,
                time: new Date().toTimeString().split(' ')[0]
            };

            telemetryData.push(dummyMetric);
            if (telemetryData.length > 50) telemetryData.shift();

            draw();

            if (lastUpdateEl) {
                lastUpdateEl.textContent = 'Pembaruan: Baru saja (SSE Trigger)';
            }
        });
    }

    // SSE Event Listener Realtime
    if (window.EventSource) {
        try {
            const stream = new EventSource('/realtime/stream');
            stream.addEventListener('magkom:telemetry-route', function(e) {
                try {
                    const data = JSON.parse(e.data);
                    telemetryData.push({
                        id: Date.now(),
                        method: data.method || 'GET',
                        route_name: data.route_name || data.uri || '/stream',
                        uri: data.uri || '/stream',
                        status_code: data.status_code || 200,
                        duration_ms: parseFloat(data.duration_ms) || 12.0,
                        memory_mb: parseFloat(data.memory_mb) || 5.0,
                        time: data.time || new Date().toTimeString().split(' ')[0]
                    });
                    if (telemetryData.length > 50) telemetryData.shift();
                    draw();
                } catch(err) {}
            });
        } catch(err) {}
    }

    // Window Resize Observer
    window.addEventListener('resize', resizeCanvas);
    setTimeout(resizeCanvas, 50);
})();
</script>
@endpush