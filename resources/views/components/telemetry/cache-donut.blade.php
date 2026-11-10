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

<div class="telemetry-right-col-suite" style="display: flex; flex-direction: column; gap: 16px;">

    <!-- 1. REDIS CACHE CARD (Minimal text, sharp visual focus & Canvas Parity) -->
    <div class="telemetry-card mb-0">
        <div class="telemetry-card-header">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span class="flex h-6 w-6 items-center justify-center rounded-md"
                      style="width: 24px; height: 24px; min-width: 24px; min-height: 24px; display: inline-flex; align-items: center; justify-content: center; background: rgba(244, 63, 94, 0.15); color: #fb7185; border: 1px solid rgba(244, 63, 94, 0.35); border-radius: 6px;">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor" width="14" height="14" style="width: 14px; height: 14px; max-width: 14px; max-height: 14px;">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </span>
                <span class="telemetry-card-title">Cache Redis</span>
            </div>
            {{-- Modern Standard Status Pill (AC-3: Natural & Symmetric) --}}
            <span class="telemetry-status-pill pill-success">
                <span class="status-dot"></span>
                <span>Optimal</span>
            </span>
        </div>

        <!-- Radial Donut Visualization -->
        <div class="telemetry-donut-layout">

            <!-- Gauge SVG (Strict Width/Height Guardrails) -->
            <div class="telemetry-donut-svg-wrapper">
                <div class="absolute h-24 w-24 rounded-full pointer-events-none" style="position: absolute; width: 96px; height: 96px; background: rgba(16, 185, 129, 0.12); filter: blur(20px); border-radius: 9999px; pointer-events: none;"></div>

                <svg class="h-28 w-28 -rotate-90 transform" viewBox="0 0 100 100" width="112" height="112" style="width: 112px; height: 112px; max-width: 112px; max-height: 112px; transform: rotate(-90deg);">
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

                    <circle cx="50" cy="50" r="46" stroke="var(--tel-card-border)" stroke-width="1" stroke-dasharray="2 4" fill="transparent" />
                    <circle cx="50" cy="50" r="38" stroke="var(--tel-progress-track-bg)" stroke-width="8" fill="transparent" />
                    <circle cx="50" cy="50" r="38" stroke="url(#missGradient)" stroke-width="8" stroke-dasharray="238.76" stroke-dashoffset="211.5" stroke-linecap="round" fill="transparent" opacity="0.4" />
                    <circle cx="50" cy="50" r="38" stroke="url(#redisGradient)" stroke-width="8.5" stroke-dasharray="238.76" stroke-dashoffset="27.2" stroke-linecap="round" fill="transparent" />

                    <g transform="translate(50, 50) rotate(318) translate(38, 0)">
                        <circle cx="0" cy="0" r="4.5" fill="#10b981" opacity="0.75" />
                        <circle cx="0" cy="0" r="2.5" fill="#ffffff" stroke="#10b981" stroke-width="1.2" />
                    </g>
                </svg>

                <!-- Center Core (Strict Absolute Isolation) -->
                <div class="telemetry-donut-center-text">
                    <span class="telemetry-donut-center-val">
                        {{ $cacheStats['hit_ratio'] ?? '88.6' }}%
                    </span>
                    <span class="telemetry-donut-center-lbl">
                        HIT RATIO
                    </span>
                </div>
            </div>

            <!-- Micro Breakdown Bars -->
            <div class="telemetry-breakdown-list">
                <div>
                    <div class="telemetry-breakdown-item">
                        <span style="color: var(--tel-text-muted); display: flex; align-items: center; gap: 6px;">
                            <span class="h-2 w-2 rounded-full" style="width: 8px; height: 8px; background-color: #10b981; display: inline-block; border-radius: 9999px;"></span>
                            Hits
                        </span>
                        <span style="font-weight: 700; color: #10b981;">{{ number_format($cacheStats['hits'] ?? 18492) }}</span>
                    </div>
                    <div class="telemetry-progress-track">
                        <div class="telemetry-progress-fill" style="width: {{ $cacheStats['hit_ratio'] ?? 88.6 }}%; background: linear-gradient(to right, #10b981, #06b6d4);"></div>
                    </div>
                </div>

                <div>
                    <div class="telemetry-breakdown-item">
                        <span style="color: var(--tel-text-muted); display: flex; align-items: center; gap: 6px;">
                            <span class="h-2 w-2 rounded-full" style="width: 8px; height: 8px; background-color: #64748b; display: inline-block; border-radius: 9999px;"></span>
                            Misses
                        </span>
                        <span style="font-weight: 700; color: var(--tel-text-muted);">{{ number_format($cacheStats['misses'] ?? 2380) }}</span>
                    </div>
                    <div class="telemetry-progress-track">
                        <div class="telemetry-progress-fill" style="width: {{ 100 - ($cacheStats['hit_ratio'] ?? 88.6) }}%; background-color: #64748b;"></div>
                    </div>
                </div>

                <div style="padding-top: 6px; border-top: 1px solid var(--tel-card-border); display: flex; align-items: center; justify-content: space-between; font-size: 10px; color: var(--tel-text-muted);">
                    <span>RAM:</span>
                    <span style="font-weight: 700; color: var(--tel-accent-link);">{{ $cacheStats['ram_used_mb'] ?? '64.2' }} / {{ $cacheStats['ram_total_mb'] ?? '128' }} MB</span>
                </div>
                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 10px; color: var(--tel-text-muted);">
                    <span>Keys:</span>
                    <span style="font-weight: 700; color: var(--tel-text-primary);">{{ number_format($cacheStats['keys_tagged'] ?? 1420) }} Tagged</span>
                </div>
            </div>

        </div>

        <div style="margin-top: 14px; padding-top: 10px; border-top: 1px solid var(--tel-card-border); display: flex; align-items: center; justify-content: space-between; font-size: 10px; font-family: monospace; color: var(--tel-text-muted);">
            <span>{{ $cacheStats['version'] ?? 'Redis 7.2' }}</span>
            <span style="color: #10b981; font-weight: 600;">{{ $cacheStats['evictions'] ?? 0 }} Evictions</span>
        </div>
    </div>

    <!-- 2. HOST RESOURCES CARD (AC-4 Canvas Parity: Title 'Host', Subtitle '4 vCPU / 16GB') -->
    <div class="telemetry-card mb-0">
        <div class="telemetry-card-header">
            <span class="telemetry-card-title">Host</span>
            <span class="text-[10px] font-mono font-normal" style="color: var(--tel-text-muted);">{{ $hostResources['vcpu'] ?? 4 }} vCPU / {{ $hostResources['ram_total_gb'] ?? 16 }}GB</span>
        </div>

        <div class="telemetry-breakdown-list">
            <!-- CPU Load -->
            <div>
                <div class="telemetry-breakdown-item">
                    <span style="color: var(--tel-text-muted);">CPU Load</span>
                    <span style="font-weight: 700; color: var(--tel-text-primary);">{{ $hostResources['cpu_load_percent'] ?? 62 }}%</span>
                </div>
                <div class="telemetry-progress-track">
                    <div class="telemetry-progress-fill" style="width: {{ $hostResources['cpu_load_percent'] ?? 62 }}%; background-color: #14b8a6;"></div>
                </div>
            </div>

            <!-- RAM System -->
            <div>
                <div class="telemetry-breakdown-item">
                    <span style="color: var(--tel-text-muted);">RAM System</span>
                    <span style="font-weight: 700; color: var(--tel-text-primary);">{{ $hostResources['ram_used_gb'] ?? '5.8' }} / {{ $hostResources['ram_total_gb'] ?? 16 }} GB</span>
                </div>
                <div class="telemetry-progress-track">
                    <div class="telemetry-progress-fill" style="width: {{ round((($hostResources['ram_used_gb'] ?? 5.8) / ($hostResources['ram_total_gb'] ?? 16)) * 100) }}%; background-color: #0284c7;"></div>
                </div>
            </div>

            <!-- Disk /berkas -->
            <div>
                <div class="telemetry-breakdown-item">
                    <span style="color: var(--tel-text-muted);">Disk /berkas</span>
                    <span style="font-weight: 700; color: var(--tel-text-primary);">{{ $diskUsedPercent }} / {{ $hostResources['disk_total_gb'] ?? 100 }} GB</span>
                </div>
                <div class="telemetry-progress-track">
                    <div class="telemetry-progress-fill" style="width: {{ min(100, $diskUsedPercent) }}%; background-color: #10b981;"></div>
                </div>
            </div>

            <!-- DB Pool -->
            <div>
                <div class="telemetry-breakdown-item">
                    <span style="color: var(--tel-text-muted);">DB Pool</span>
                    <span style="font-weight: 700; color: var(--tel-text-primary);">{{ $hostResources['db_pool_active'] ?? 12 }} / {{ $hostResources['db_pool_max'] ?? 100 }}</span>
                </div>
                <div class="telemetry-progress-track">
                    <div class="telemetry-progress-fill" style="width: {{ round((($hostResources['db_pool_active'] ?? 12) / ($hostResources['db_pool_max'] ?? 100)) * 100) }}%; background-color: #f59e0b;"></div>
                </div>
            </div>
        </div>
    </div>

</div>
