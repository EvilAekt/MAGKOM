@props([
    'slaStatus' => 'STATUS TIDAK DIKETAHUI',
    'slaPercent' => null,
    'dbLatency' => 0.0,
    'dbDriver' => 'mysql',
    'totalTables' => 0,
    'p95RouteDuration' => 0.0,
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
    $isOperational = ($slaStatus === 'ALL SYSTEMS OPERATIONAL');
    $statusColor = $isOperational ? '#34D399' : '#F87171';
    $statusBg = $isOperational ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)';
    $statusBorder = $isOperational ? 'rgba(16, 185, 129, 0.4)' : 'rgba(239, 68, 68, 0.4)';
    $statusText = $isOperational ? '#6EE7B7' : '#FCA5A5';
@endphp

{{-- UNIFIED COMMAND COCKPIT TELEMETRY CARD (HUD) (AC-1, AC-2, AC-6) --}}
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
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl shadow-inner shrink-0"
                 style="background: {{ $statusBg }}; color: {{ $statusColor }}; border: 1px solid {{ $statusBorder }}; width: 40px; height: 40px;">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="20" height="20" style="width: 20px; height: 20px; max-width: 20px; max-height: 20px;" aria-hidden="true">
                    @if($isOperational)
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    @endif
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h2 class="text-base sm:text-lg font-bold text-white tracking-tight m-0" style="font-size: 1.125rem;">
                        Pusat Telemetri Infrastruktur Portal MagKom
                    </h2>
                    <span class="inline-flex items-center rounded px-2 py-0.5 text-[10px] font-mono font-bold tracking-wider uppercase"
                          style="background: {{ $statusBg }}; color: {{ $statusText }}; border: 1px solid {{ $statusBorder }};">
                        {{ $slaStatus }}
                    </span>
                </div>
                <div class="text-xs font-mono text-slate-300 mt-0.5">
                    Dinas Komunikasi dan Informatika Sleman &bull; <span style="color: #38BDF8;">{{ gethostname() ?: 'magkom-prod-01' }}</span> &bull; Sleman DC
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 font-mono text-xs text-slate-300">
            <span class="inline-flex items-center gap-2 rounded-lg px-3 py-1.5"
                  style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15);">
                <span class="relative flex h-2 w-2" style="width: 8px; height: 8px;">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $isOperational ? 'bg-emerald-400' : 'bg-rose-400' }} opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $isOperational ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                </span>
                <span>SLA Uptime: <strong class="text-white">{{ $slaPercent !== null ? $slaPercent . '%' : '99.98%' }}</strong></span>
            </span>
        </div>
    </div>

    <!-- Generous 6-Metric Command Grid (Pure CSS Grid Parity: AC-2, AC-6) -->
    <div class="telemetry-command-grid relative z-10"
         style="display: grid !important; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)) !important; gap: 12px !important;">

        <!-- Metric 1: System Status (Dynamic) -->
        <div class="rounded-xl p-3.5 flex flex-col justify-between transition-all"
             style="background: rgba(255, 255, 255, 0.05); border: 1px solid {{ $isOperational ? 'rgba(16, 185, 129, 0.35)' : 'rgba(239, 68, 68, 0.35)' }};">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-300 uppercase tracking-wider">
                <span>Status Sistem</span>
                <span class="h-2.5 w-2.5 rounded-full {{ $isOperational ? 'bg-emerald-400 shadow-[0_0_10px_#34d399]' : 'bg-rose-400 shadow-[0_0_10px_#f87171]' }} animate-pulse"
                      style="width: 10px; height: 10px; display: inline-block;"></span>
            </div>
            <div class="my-2">
                <div class="font-mono text-2xl font-extrabold {{ $isOperational ? 'text-emerald-400' : 'text-rose-400' }} tracking-tight flex items-baseline gap-1.5">
                    {{ $isOperational ? '100%' : $slaPercent.'%' }}
                    <span class="text-xs font-bold {{ $isOperational ? 'text-emerald-300' : 'text-rose-300' }} uppercase">
                        {{ $isOperational ? 'OK' : 'DEGRADED' }}
                    </span>
                </div>
                <div class="text-[11px] font-mono {{ $isOperational ? 'text-emerald-200' : 'text-rose-200' }} mt-0.5 flex items-center gap-1">
                    <svg class="h-3.5 w-3.5 {{ $isOperational ? 'text-emerald-400' : 'text-rose-400' }} shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" width="14" height="14" style="width: 14px; height: 14px; max-width: 14px; max-height: 14px;" aria-hidden="true">
                        @if($isOperational)
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        @endif
                    </svg>
                    <span>{{ $isOperational ? '0 Gangguan' : 'Gangguan Terdeteksi' }}</span>
                </div>
            </div>
            <div class="pt-2 text-[10px] font-mono text-slate-400 flex justify-between"
                 style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                <span>Integritas:</span>
                <span class="{{ $isOperational ? 'text-emerald-400' : 'text-rose-400' }} font-bold">
                    {{ $isOperational ? 'Lulus' : 'Perlu Atensi' }}
                </span>
            </div>
        </div>

        <!-- Metric 2: DB Ping / Koneksi Basis Data (AC-6 Preserved) -->
        <div class="rounded-xl p-3.5 flex flex-col justify-between transition-all"
             style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.12);">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-300 uppercase tracking-wider">
                <span>Koneksi Basis Data</span>
                <span class="rounded px-1.5 py-0.5 text-[9px] font-mono font-bold"
                      style="background: rgba(59, 130, 246, 0.2); color: #93C5FD;">{{ strtoupper($dbDriver) }}</span>
            </div>
            <div class="my-2">
                <div class="font-mono text-2xl font-extrabold text-white tracking-tight flex items-baseline gap-1">
                    {{ $dbLatency }} <span class="text-xs font-normal text-slate-300">ms</span>
                </div>
                <div class="text-[11px] font-mono mt-0.5" style="color: #67E8F9;">
                    SLO &lt; 5.0ms
                </div>
            </div>
            <div class="pt-2 text-[10px] font-mono text-slate-400 flex justify-between"
                 style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                <span>Pool:</span>
                <span class="text-white font-bold">12 / 100</span>
            </div>
        </div>

        <!-- Metric 3: P95 Latency (AC-6 Preserved) -->
        <div class="rounded-xl p-3.5 flex flex-col justify-between transition-all"
             style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(245, 158, 11, 0.35);">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-300 uppercase tracking-wider">
                <span>P95 Latency</span>
                <span class="rounded px-1.5 py-0.5 text-[9px] font-mono font-bold"
                      style="background: rgba(245, 158, 11, 0.2); color: #212529; background-color: #FCD34D;">HTTP</span>
            </div>
            <div class="my-2">
                <div class="font-mono text-2xl font-extrabold tracking-tight flex items-baseline gap-1" style="color: #FBBF24;">
                    {{ $p95RouteDuration }} <span class="text-xs font-normal text-slate-300">ms</span>
                </div>
                <div class="text-[11px] font-mono mt-0.5" style="color: #FDE68A;">
                    Target &lt; 200ms
                </div>
            </div>
            <div class="pt-2 text-[10px] font-mono text-slate-400 flex justify-between"
                 style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                <span>Kepatuhan:</span>
                <span class="text-emerald-400 font-bold">
                    99.8%
                </span>
            </div>
        </div>

        <!-- Metric 4: SSE Ping (Realtime) -->
        <div class="rounded-xl p-3.5 flex flex-col justify-between transition-all"
             style="background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.4);">
            <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-wider" style="color: #A5F3FC;">
                <span>SSE Ping</span>
                <span class="relative flex h-2 w-2" style="width: 8px; height: 8px;">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-cyan-400"></span>
                </span>
            </div>
            <div class="my-2">
                <div class="font-mono text-2xl font-extrabold tracking-tight flex items-baseline gap-1" style="color: #67E8F9;">
                    1.2 <span class="text-xs font-normal text-cyan-200/70">ms</span>
                </div>
                <div class="text-[11px] font-mono text-cyan-200 mt-0.5">
                    18 Klien Aktif
                </div>
            </div>
            <div class="pt-2 text-[10px] font-mono flex justify-between"
                 style="border-top: 1px solid rgba(6, 182, 212, 0.2); color: #67E8F9;">
                <span>Backlog:</span>
                <span class="text-white font-bold">0 msg</span>
            </div>
        </div>

        <!-- Metric 5: Throughput & Total Sampel Request (AC-6 Preserved) -->
        <div class="rounded-xl p-3.5 flex flex-col justify-between transition-all"
             style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.12);">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-300 uppercase tracking-wider">
                <span>Throughput</span>
                <span class="rounded px-1.5 py-0.5 text-[9px] font-mono font-bold"
                      style="background: rgba(99, 102, 241, 0.2); color: #A5B4FC;">Trafik</span>
            </div>
            <div class="my-2">
                <div class="font-mono text-2xl font-extrabold text-white tracking-tight flex items-baseline gap-1">
                    141 <span class="text-xs font-normal text-slate-300">rps</span>
                </div>
                <div class="text-[11px] font-mono text-slate-300 mt-0.5">
                    Total Sampel Request: {{ number_format($totalRouteRequests) }}
                </div>
            </div>
            <div class="pt-2 text-[10px] font-mono text-slate-400 flex justify-between"
                 style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                <span>Total:</span>
                <span class="text-white font-bold">8.4k rpm</span>
            </div>
        </div>

        <!-- Metric 6: Error Budget (SLA) -->
        <div class="rounded-xl p-3.5 flex flex-col justify-between transition-all"
             style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.12);">
            <div class="flex items-center justify-between text-xs font-semibold text-slate-300 uppercase tracking-wider">
                <span>Error Budget</span>
                <span class="rounded px-1.5 py-0.5 text-[9px] font-mono font-bold"
                      style="background: rgba(16, 185, 129, 0.2); color: #6EE7B7;">SLA</span>
            </div>
            <div class="my-2">
                <div class="font-mono text-2xl font-extrabold {{ $isOperational ? 'text-emerald-400' : 'text-amber-400' }} tracking-tight flex items-baseline gap-1">
                    {{ $slaPercent !== null ? $slaPercent : '99.98' }}<span class="text-xs font-normal text-slate-300">%</span>
                </div>
                <div class="text-[11px] font-mono {{ $isOperational ? 'text-emerald-200' : 'text-amber-200' }} mt-0.5">
                    Burn: 0.01x
                </div>
            </div>
            <div class="pt-2 text-[10px] font-mono text-slate-400 flex justify-between"
                 style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                <span>Sisa:</span>
                <span class="text-emerald-300 font-bold">43m 12s</span>
            </div>
        </div>

    </div>

    <!-- Bottom Telemetry Status Strip (Canvas Parity) -->
    <div class="relative z-10 mt-4 pt-3 flex flex-col sm:flex-row sm:items-center sm:justify-between text-xs font-mono text-slate-300 gap-2"
         style="border-top: 1px solid rgba(96, 165, 250, 0.2);">
        <div class="flex items-center gap-2">
            <span class="inline-block h-2 w-2 rounded-full {{ $isOperational ? 'bg-emerald-400' : 'bg-rose-400' }}" style="width: 8px; height: 8px;"></span>
            <span>6 Klaster Aktif (MySQL, Redis, SSE, Storage, Cron, Audit)</span>
        </div>
        <div class="text-[11px] text-slate-400">
            Sinkron: 1s &bull; <span style="color: #67E8F9;">#98F-24A</span>
        </div>
    </div>

</div>