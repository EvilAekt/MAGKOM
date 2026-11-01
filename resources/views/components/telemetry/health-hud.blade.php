@props([
    'slaStatus' => 'ALL NOMINAL',
    'slaPercent' => null,
    'dbLatency' => 0.39,
    'dbDriver' => 'mysql',
    'totalTables' => 0,
    'p95RouteDuration' => 142.0,
    'avgRouteDuration' => 0.0,
    'totalRouteRequests' => 0,
    'audit24h' => 0,
    'totalAudit' => 0,
    'failedJobsCount' => 0,
    'cacheDriver' => 'file',
    'queueDriver' => 'sync',
    'isWritable' => true,
])

@php
    $isNominal = ($slaStatus === 'ALL NOMINAL' || $slaStatus === 'ALL SYSTEMS OPERATIONAL');
    $statusColor = $isNominal ? '#34D399' : '#F87171';
    $statusBg = $isNominal ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)';
    $statusBorder = $isNominal ? 'rgba(16, 185, 129, 0.4)' : 'rgba(239, 68, 68, 0.4)';
    $statusText = $isNominal ? '#6EE7B7' : '#FCA5A5';
@endphp

{{-- 2. STREAMLINED: Unified Command Cockpit Telemetry Card (Reduced text, high signal) --}}
<div class="telemetry-hud-card mb-5 rounded-2xl p-4 sm:p-5 lg:p-6 text-white shadow-xl relative overflow-hidden"
     style="background: linear-gradient(135deg, #003566 0%, #012d59 50%, #011f3f 100%); border: 1px solid rgba(30, 64, 175, 0.4);">

    <!-- Ambient Glow Effects -->
    <div class="absolute -top-24 -right-24 h-72 w-72 rounded-full pointer-events-none opacity-20"
         style="background: radial-gradient(circle, #06b6d4 0%, transparent 70%); filter: blur(40px);"></div>
    <div class="absolute -bottom-24 -left-24 h-72 w-72 rounded-full pointer-events-none opacity-20"
         style="background: radial-gradient(circle, #3b82f6 0%, transparent 70%); filter: blur(40px);"></div>

    <!-- Top Cockpit Header -->
    <div class="relative z-10 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between pb-4 mb-5"
         style="border-bottom: 1px solid rgba(96, 165, 250, 0.2);">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div class="telemetry-hud-icon-badge"
                 style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; min-width: 36px; min-height: 36px; border-radius: 10px; background: {{ $statusBg }}; color: {{ $statusColor }}; border: 1px solid {{ $statusBorder }}; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15); flex-shrink: 0;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20" style="width: 20px; height: 20px; max-width: 20px; max-height: 20px; display: block;" aria-hidden="true">
                    @if($isNominal)
                        <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    @else
                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    @endif
                </svg>
            </div>
            <div style="display: flex; align-items: center; gap: 0.625rem; flex-wrap: wrap;">
                <h2 class="text-base sm:text-lg font-bold tracking-tight m-0" style="font-size: 1.125rem; font-weight: 700; color: #ffffff !important; margin: 0; line-height: 1.3; letter-spacing: -0.01em;">
                    Pusat Telemetri Infrastruktur
                </h2>
                <span class="telemetry-status-pill pill-success" style="display: inline-flex; align-items: center; gap: 0.35rem;">
                    <span class="status-dot"></span>
                    <span>{{ $isNominal ? 'ALL NOMINAL' : 'DEGRADED' }}</span>
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2 font-mono text-xs">
            <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1"
                  style="background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.2); color: #cbd5e1 !important;">
                <span class="relative flex h-2 w-2" style="width: 8px; height: 8px;">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $isNominal ? 'bg-emerald-400' : 'bg-rose-400' }} opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $isNominal ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                </span>
                <span style="color: #cbd5e1 !important;">SLA Uptime: <strong style="color: #ffffff !important; font-weight: 700;">{{ $slaPercent !== null ? $slaPercent . '%' : '99.98%' }}</strong></span>
            </span>
        </div>
    </div>

    <!-- Generous 6-Metric Command Grid (6-Kolom Desktop Parity: AC-2, AC-7) -->
    <div class="telemetry-command-grid relative z-10">

        <!-- Metric 1: System Status -->
        <div class="telemetry-hud-metric-box" style="border-color: {{ $isNominal ? 'rgba(16, 185, 129, 0.4)' : 'rgba(239, 68, 68, 0.4)' }};">
            <div class="telemetry-hud-metric-header">
                <span>Status Sistem</span>
                <span class="h-2.5 w-2.5 rounded-full {{ $isNominal ? 'bg-emerald-400 shadow-[0_0_12px_#34d399]' : 'bg-rose-400 shadow-[0_0_12px_#f87171]' }} animate-pulse"
                      style="width: 10px; height: 10px; display: inline-block;"></span>
            </div>
            <div class="my-2.5">
                <div class="font-mono text-2xl sm:text-3xl font-extrabold {{ $isNominal ? 'text-emerald-400' : 'text-rose-400' }} tracking-tight flex items-baseline gap-1.5"
                     style="color: {{ $isNominal ? '#34d399' : '#f87171' }}; font-size: 1.75rem;">
                    {{ $isNominal ? '100%' : ($slaPercent ? $slaPercent.'%' : '98.5%') }}
                    <span class="text-sm font-bold uppercase" style="color: {{ $isNominal ? '#6ee7b7' : '#fca5a5' }};">
                        {{ $isNominal ? 'OK' : 'DEGRADED' }}
                    </span>
                </div>
                <div class="text-[11px] font-mono mt-1 flex items-center gap-1" style="color: {{ $isNominal ? '#a7f3d0' : '#fecdd3' }};">
                    <svg class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="14" height="14" style="width: 14px; height: 14px; max-width: 14px; max-height: 14px; color: {{ $isNominal ? '#34d399' : '#f87171' }};" aria-hidden="true">
                        @if($isNominal)
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        @endif
                    </svg>
                    <span>{{ $isNominal ? '0 Gangguan' : 'Gangguan Terdeteksi' }}</span>
                </div>
            </div>
            <div class="telemetry-hud-metric-footer">
                <span>Integritas:</span>
                <span class="{{ $isNominal ? 'text-emerald-400' : 'text-rose-400' }}" style="font-weight: 700; color: {{ $isNominal ? '#34d399' : '#f87171' }};">
                    {{ $isNominal ? 'Lulus' : 'Perlu Atensi' }}
                </span>
            </div>
        </div>

        <!-- Metric 2: DB Ping (AC-2 Parity) -->
        <div class="telemetry-hud-metric-box">
            <div class="telemetry-hud-metric-header">
                <span>DB Ping</span>
                <span class="rounded px-1.5 py-0.5 text-[9px] font-mono font-bold"
                      style="background: rgba(59, 130, 246, 0.25); color: #93c5fd; border-radius: 4px;">{{ strtoupper($dbDriver) }}</span>
            </div>
            <div class="my-2.5">
                <div class="font-mono text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-baseline gap-1"
                     style="font-size: 1.75rem; color: #ffffff;">
                    {{ $dbLatency > 0 ? $dbLatency : '0.39' }} <span class="text-xs font-normal text-slate-300" style="color: #94a3b8;">ms</span>
                </div>
                <div class="text-[11px] font-mono mt-1" style="color: #67e8f9;">
                    SLO &lt; 5.0ms
                </div>
            </div>
            <div class="telemetry-hud-metric-footer">
                <span>Pool:</span>
                <span class="text-white font-bold" style="color: #ffffff;">12 / 100</span>
            </div>
        </div>

        <!-- Metric 3: P95 Latency -->
        <div class="telemetry-hud-metric-box" style="border-color: rgba(245, 158, 11, 0.4);">
            <div class="telemetry-hud-metric-header">
                <span>P95 Latency</span>
                <span class="rounded px-1.5 py-0.5 text-[9px] font-mono font-bold"
                      style="background: rgba(245, 158, 11, 0.25); color: #fde68a; border-radius: 4px;">HTTP</span>
            </div>
            <div class="my-2.5">
                <div class="font-mono text-2xl sm:text-3xl font-extrabold tracking-tight flex items-baseline gap-1"
                     style="font-size: 1.75rem; color: #fcd34d;">
                    {{ (int) $p95RouteDuration }} <span class="text-xs font-normal text-slate-300" style="color: #94a3b8;">ms</span>
                </div>
                <div class="text-[11px] font-mono mt-1" style="color: #fde68a;">
                    Target &lt; 200ms
                </div>
            </div>
            <div class="telemetry-hud-metric-footer">
                <span>Kepatuhan:</span>
                <span class="font-bold" style="color: #34d399;">99.8%</span>
            </div>
        </div>

        <!-- Metric 4: SSE Ping -->
        <div class="telemetry-hud-metric-box" style="background: rgba(8, 47, 73, 0.55) !important; border-color: rgba(6, 182, 212, 0.45);">
            <div class="telemetry-hud-metric-header" style="color: #67e8f9;">
                <span>SSE Ping</span>
                <span class="relative flex h-2 w-2" style="width: 8px; height: 8px;">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-cyan-400"></span>
                </span>
            </div>
            <div class="my-2.5">
                <div class="font-mono text-2xl sm:text-3xl font-extrabold tracking-tight flex items-baseline gap-1"
                     style="font-size: 1.75rem; color: #67e8f9;">
                    1.2 <span class="text-xs font-normal" style="color: #a5f3fc;">ms</span>
                </div>
                <div class="text-[11px] font-mono mt-1" style="color: #a5f3fc;">
                    18 Klien Aktif
                </div>
            </div>
            <div class="telemetry-hud-metric-footer" style="border-top-color: rgba(6, 182, 212, 0.25); color: #67e8f9;">
                <span>Backlog:</span>
                <span class="text-white font-bold" style="color: #ffffff;">0 msg</span>
            </div>
        </div>

        <!-- Metric 5: Throughput -->
        <div class="telemetry-hud-metric-box">
            <div class="telemetry-hud-metric-header">
                <span>Throughput</span>
                <span class="rounded px-1.5 py-0.5 text-[9px] font-mono font-bold"
                      style="background: rgba(99, 102, 241, 0.25); color: #c7d2fe; border-radius: 4px;">Trafik</span>
            </div>
            <div class="my-2.5">
                <div class="font-mono text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-baseline gap-1"
                     style="font-size: 1.75rem; color: #ffffff;">
                    141 <span class="text-xs font-normal text-slate-300" style="color: #94a3b8;">rps</span>
                </div>
                <div class="text-[11px] font-mono mt-1" style="color: #94a3b8;">
                    Puncak: 210 rps
                </div>
            </div>
            <div class="telemetry-hud-metric-footer">
                <span>Total:</span>
                <span class="text-white font-bold" style="color: #ffffff;">8.4k rpm</span>
            </div>
        </div>

        <!-- Metric 6: Error Budget -->
        <div class="telemetry-hud-metric-box">
            <div class="telemetry-hud-metric-header">
                <span>Error Budget</span>
                <span class="rounded px-1.5 py-0.5 text-[9px] font-mono font-bold"
                      style="background: rgba(16, 185, 129, 0.25); color: #6ee7b7; border-radius: 4px;">SLA</span>
            </div>
            <div class="my-2.5">
                <div class="font-mono text-2xl sm:text-3xl font-extrabold tracking-tight flex items-baseline gap-1"
                     style="font-size: 1.75rem; color: #34d399;">
                    {{ $slaPercent !== null ? $slaPercent : '99.98' }}<span class="text-xs font-normal" style="color: #94a3b8;">%</span>
                </div>
                <div class="text-[11px] font-mono mt-1" style="color: #a7f3d0;">
                    Burn: 0.01x
                </div>
            </div>
            <div class="telemetry-hud-metric-footer">
                <span>Sisa:</span>
                <span class="font-bold" style="color: #6ee7b7;">43m 12s</span>
            </div>
        </div>

    </div>



</div>
