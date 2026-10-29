@props([
    'routeMetrics' => [],
])

{{-- ENTERPRISE ROUTE TELEMETRY TABLE (AC-6, AC-7) --}}
<div class="telemetry-table-card rounded-xl border border-slate-200 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900 overflow-hidden mb-5">

    <!-- Header & Filter Strip -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 px-4 py-3 dark:border-slate-800 gap-2"
         style="border-top: 3px solid #003566;">
        <div class="flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-emerald-500" style="width: 8px; height: 8px; display: inline-block;"></span>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white m-0">
                Aktivitas Endpoint &amp; Telemetri Rute
            </h3>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden md:flex items-center gap-3 text-[11px] font-mono text-slate-500 dark:text-slate-400">
                <span class="inline-flex items-center gap-1">
                    <span class="h-2 w-2 rounded-full bg-emerald-500" style="width: 8px; height: 8px; display: inline-block;"></span> &lt;50ms
                </span>
                <span class="inline-flex items-center gap-1">
                    <span class="h-2 w-2 rounded-full bg-amber-500" style="width: 8px; height: 8px; display: inline-block;"></span> 50-200ms
                </span>
                <span class="inline-flex items-center gap-1">
                    <span class="h-2 w-2 rounded-full bg-rose-500" style="width: 8px; height: 8px; display: inline-block;"></span> &gt;200ms
                </span>
            </div>
            <input type="text" id="endpointTableFilter" placeholder="Filter..."
                   class="w-32 sm:w-40 rounded-lg border border-slate-300 bg-slate-50 px-2.5 py-1 text-xs font-mono dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                   style="height: 28px;" />
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="table table-striped jambo_table w-full text-left text-xs mb-0" id="routeMetricsTable">
            <thead class="bg-[#003566] text-white uppercase text-[9px] font-bold tracking-wider" style="background-color: #003566 !important; color: #ffffff !important;">
                <tr>
                    <th scope="col" class="py-2.5 px-3.5" style="width: 95px;">WAKTU</th>
                    <th scope="col" class="py-2.5 px-3.5" style="width: 80px;">METHOD</th>
                    <th scope="col" class="py-2.5 px-3.5">ENDPOINT</th>
                    <th scope="col" class="py-2.5 px-3.5 text-center" style="width: 100px;">STATUS</th>
                    <th scope="col" class="py-2.5 px-3.5 text-right" style="width: 110px;">LATENSI</th>
                    <th scope="col" class="py-2.5 px-3.5 text-right" style="width: 100px;">MEMORI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-mono text-[11px]" id="routeMetricsTbody">
                @forelse($routeMetrics as $metric)
                    @php
                        $ms = (float) $metric['duration_ms'];
                        $method = strtoupper((string) ($metric['method'] ?? 'GET'));
                        $statusCode = (int) ($metric['status_code'] ?? 200);

                        // Method Badges
                        $methodColor = match($method) {
                            'GET' => 'color: #0284c7; font-weight: 700;',
                            'POST' => 'color: #059669; font-weight: 700;',
                            'DELETE' => 'color: #e11d48; font-weight: 700;',
                            'PUT', 'PATCH' => 'color: #d97706; font-weight: 700;',
                            default => 'color: #475569; font-weight: 700;'
                        };

                        // HTTP Status Code Badges (WCAG AA: text #212529 on amber)
                        $statusBadgeStyle = match(true) {
                            $statusCode >= 500 => 'background: rgba(244, 63, 94, 0.15); color: #e11d48; border: 1px solid rgba(244, 63, 94, 0.3);',
                            $statusCode >= 400 => 'background: #FDE68A; color: #212529; border: 1px solid #F59E0B; font-weight: 800;',
                            $statusCode >= 300 => 'background: #FEF3C7; color: #212529; border: 1px solid #FCD34D; font-weight: 700;',
                            default => 'background: rgba(16, 185, 129, 0.15); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3);'
                        };

                        // Latency Semantic Class
                        $latencyColor = match(true) {
                            $ms < 50 => 'color: #059669; font-weight: 700;',
                            $ms <= 200 => 'color: #d97706; font-weight: 700;',
                            default => 'color: #e11d48; font-weight: 800;'
                        };

                        $isHighlighted = str_contains((string)($metric['uri'] ?? ''), 'realtime/stream');
                        $isSpike = $ms > 200;
                    @endphp
                    <tr class="route-table-row {{ $isHighlighted ? 'bg-cyan-50/40 dark:bg-cyan-950/20' : ($isSpike ? 'bg-rose-50/40 dark:bg-rose-950/20' : '') }}">
                        <td class="py-2 px-3.5 text-slate-500 dark:text-slate-400">
                            {{ $metric['time'] }}
                        </td>
                        <td class="py-2 px-3.5" style="{{ $methodColor }}">
                            {{ $method }}
                        </td>
                        <td class="py-2 px-3.5 font-bold {{ $isHighlighted ? 'text-cyan-900 dark:text-cyan-200' : 'text-slate-900 dark:text-white' }}">
                            <div>{{ $metric['route_name'] }}</div>
                            @if(!empty($metric['uri']) && $metric['uri'] !== $metric['route_name'])
                                <div class="text-[10px] text-slate-400 font-normal truncate max-w-sm">{{ $metric['uri'] }}</div>
                            @endif
                        </td>
                        <td class="py-2 px-3.5 text-center">
                            <span class="inline-flex items-center rounded px-2 py-0.5 text-[10px] font-mono font-bold"
                                  style="{{ $statusBadgeStyle }}">
                                {{ $statusCode }} {{ $statusCode === 200 ? 'OK' : ($statusCode === 302 ? 'REDIR' : '') }}
                            </span>
                        </td>
                        <td class="py-2 px-3.5 text-right font-mono" style="{{ $latencyColor }}">
                            {{ number_format($ms, 1) }} ms
                        </td>
                        <td class="py-2 px-3.5 text-right text-slate-500 dark:text-slate-400 font-mono">
                            {{ number_format((float) ($metric['memory_mb'] ?? 0), 1) }} MB
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-6 text-slate-400 font-mono">
                            <i class="fa fa-info-circle text-info" aria-hidden="true"></i> Belum ada rekaman metrik rute HTTP tersimpan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Table Footer Clean UI -->
    <div class="px-4 py-2.5 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 text-[11px] text-slate-500 dark:text-slate-400 flex items-center justify-between font-mono">
        <span>Aliran Telemetri Rute HTTP &bull; Auto-Update via SSE Event <code class="text-primary font-bold">magkom:telemetry-route</code></span>
        <span class="font-bold text-slate-700 dark:text-slate-300">Gentelella Table Parity</span>
    </div>

</div>