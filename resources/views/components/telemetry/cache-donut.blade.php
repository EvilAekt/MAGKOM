@props([
    'cacheStats' => [
        'driver' => 'redis',
        'hit_ratio' => 88.6,
        'hits' => 18492,
        'misses' => 2380,
        'ram_used_mb' => 64.2,
        'ram_total_mb' => 128,
        'keys_tagged' => 1420,
        'version' => 'Redis 7.2',
        'evictions' => 0,
    ],
    'hostResources' => [
        'vcpu' => 4,
        'ram_total_gb' => 16,
        'ram_used_gb' => 5.8,
        'cpu_load_percent' => 62,
        'disk_used_percent' => 57.4,
        'disk_total_gb' => 100,
        'disk_used_gb' => 57.4,
        'db_pool_active' => 12,
        'db_pool_max' => 100,
    ],
    'diskUsedPercent' => 57.4,
    'diskFreeGb' => 42.6,
    'isWritable' => true,
    'totalDokumenCount' => 0,
    'totalDokumenMb' => 0.0,
])

<div class="telemetry-right-col-suite" style="display: flex; flex-direction: column; gap: 1rem;">

    <!-- 1. REDIS CACHE CARD (Minimal text, sharp visual focus & Canvas Parity) -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900 transition-all hover:shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5 dark:border-slate-800 text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
            <div class="flex items-center gap-2">
                <span class="flex h-6 w-6 items-center justify-center rounded-md bg-rose-500/10 text-rose-600 dark:bg-rose-500/20 dark:text-rose-400 border border-rose-500/30">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor" width="14" height="14" style="width: 14px; height: 14px; max-width: 14px; max-height: 14px;">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </span>
                <span>Cache Redis</span>
            </div>
            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-mono font-bold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400 border border-emerald-500/30">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                Optimal
            </span>
        </div>

        <!-- Radial Donut Visualization -->
        <div class="mt-3.5 flex flex-col sm:flex-row items-center gap-5">

            <!-- Gauge SVG (Strict Width/Height Guardrails) -->
            <div class="relative flex items-center justify-center shrink-0" style="width: 112px; height: 112px;">
                <div class="absolute h-24 w-24 rounded-full bg-emerald-500/15 blur-xl pointer-events-none"></div>

                <svg class="h-28 w-28 -rotate-90 transform" viewBox="0 0 100 100" width="112" height="112" style="width: 112px; height: 112px; max-width: 112px; max-height: 112px;">
                    <defs>
                        <linearGradient id="redisGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#10b981" />
                            <stop offset="50%" stop-color="#06b6d4" />
                            <stop offset="100%" stop-color="#3b82f6" />
                        </linearGradient>
                        <linearGradient id="missGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#94a3b8" />
                            <stop offset="100%" stop-color="#64748b" />
                        </linearGradient>
                    </defs>

                    <circle cx="50" cy="50" r="46" stroke="currentColor" stroke-width="1" stroke-dasharray="2 4" class="text-slate-200 dark:text-slate-800" fill="transparent" />
                    <circle cx="50" cy="50" r="38" stroke="currentColor" stroke-width="8" class="text-slate-100 dark:text-slate-800/80" fill="transparent" />
                    <circle cx="50" cy="50" r="38" stroke="url(#missGradient)" stroke-width="8" stroke-dasharray="238.76" stroke-dashoffset="211.5" stroke-linecap="round" fill="transparent" class="opacity-40" />
                    <circle cx="50" cy="50" r="38" stroke="url(#redisGradient)" stroke-width="8.5" stroke-dasharray="238.76" stroke-dashoffset="27.2" stroke-linecap="round" fill="transparent" class="transition-all duration-1000" />

                    <g transform="translate(50, 50) rotate(318) translate(38, 0)">
                        <circle cx="0" cy="0" r="5" fill="#34d399" class="animate-ping opacity-75" />
                        <circle cx="0" cy="0" r="3" fill="#ffffff" stroke="#10b981" stroke-width="1.5" />
                    </g>
                </svg>

                <!-- Center Core -->
                <div class="absolute flex flex-col items-center justify-center font-mono text-center">
                    <span class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                        {{ $cacheStats['hit_ratio'] ?? '88.6' }}%
                    </span>
                    <span class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">
                        HIT RATIO
                    </span>
                </div>
            </div>

            <!-- Micro Breakdown Bars -->
            <div class="space-y-2 font-mono text-xs w-full">
                <div>
                    <div class="flex justify-between text-[11px] mb-0.5">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-emerald-500" style="width: 8px; height: 8px; display: inline-block;"></span>
                            Hits
                        </span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($cacheStats['hits'] ?? 18492) }}</span>
                    </div>
                    <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400" style="width: {{ $cacheStats['hit_ratio'] ?? 88.6 }}%;"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-[11px] mb-0.5">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <span class="h-2 w-2 rounded-full bg-slate-400 dark:bg-slate-500" style="width: 8px; height: 8px; display: inline-block;"></span>
                            Misses
                        </span>
                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ number_format($cacheStats['misses'] ?? 2380) }}</span>
                    </div>
                    <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <div class="h-full rounded-full bg-slate-400" style="width: {{ 100 - ($cacheStats['hit_ratio'] ?? 88.6) }}%;"></div>
                    </div>
                </div>

                <div class="pt-1.5 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400">
                    <span>RAM:</span>
                    <span class="font-bold text-sky-600 dark:text-sky-400">{{ $cacheStats['ram_used_mb'] ?? '64.2' }} / {{ $cacheStats['ram_total_mb'] ?? '128' }} MB</span>
                </div>
                <div class="flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400">
                    <span>Keys:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ number_format($cacheStats['keys_tagged'] ?? 1420) }} Tagged</span>
                </div>
            </div>

        </div>

        <div class="mt-3 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[10px] font-mono text-slate-400">
            <span>{{ $cacheStats['version'] ?? 'Redis 7.2' }}</span>
            <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ $cacheStats['evictions'] ?? 0 }} Evictions</span>
        </div>
    </div>

    <!-- 2. HOST RESOURCES CARD (AC-4 Canvas Parity: Title 'Host', Subtitle '4 vCPU / 16GB') -->
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900 transition-all hover:shadow-sm">
        <div class="mb-3 flex items-center justify-between border-b border-slate-100 pb-2 dark:border-slate-800 text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">
            <span>Host</span>
            <span class="text-[10px] font-mono text-slate-400 font-normal">{{ $hostResources['vcpu'] ?? 4 }} vCPU / {{ $hostResources['ram_total_gb'] ?? 16 }}GB</span>
        </div>

        <div class="space-y-2.5 font-mono text-xs">
            <!-- CPU Load -->
            <div>
                <div class="flex justify-between mb-1 text-[11px]">
                    <span class="text-slate-500 dark:text-slate-400">CPU Load</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $hostResources['cpu_load_percent'] ?? 62 }}%</span>
                </div>
                <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                    <div class="h-full rounded-full bg-teal-500" style="width: {{ $hostResources['cpu_load_percent'] ?? 62 }}%;"></div>
                </div>
            </div>

            <!-- RAM System -->
            <div>
                <div class="flex justify-between mb-1 text-[11px]">
                    <span class="text-slate-500 dark:text-slate-400">RAM System</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $hostResources['ram_used_gb'] ?? '5.8' }} / {{ $hostResources['ram_total_gb'] ?? 16 }} GB</span>
                </div>
                <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                    <div class="h-full rounded-full bg-[#01347C]" style="width: {{ round((($hostResources['ram_used_gb'] ?? 5.8) / ($hostResources['ram_total_gb'] ?? 16)) * 100) }}%;"></div>
                </div>
            </div>

            <!-- Disk /berkas -->
            <div>
                <div class="flex justify-between mb-1 text-[11px]">
                    <span class="text-slate-500 dark:text-slate-400">Disk /berkas</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $diskUsedPercent }} / {{ $hostResources['disk_total_gb'] ?? 100 }} GB</span>
                </div>
                <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ min(100, $diskUsedPercent) }}%;"></div>
                </div>
            </div>

            <!-- DB Pool -->
            <div>
                <div class="flex justify-between mb-1 text-[11px]">
                    <span class="text-slate-500 dark:text-slate-400">DB Pool</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $hostResources['db_pool_active'] ?? 12 }} / {{ $hostResources['db_pool_max'] ?? 100 }}</span>
                </div>
                <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                    <div class="h-full rounded-full bg-amber-500" style="width: {{ round((($hostResources['db_pool_active'] ?? 12) / ($hostResources['db_pool_max'] ?? 100)) * 100) }}%;"></div>
                </div>
            </div>
        </div>
    </div>

</div>
