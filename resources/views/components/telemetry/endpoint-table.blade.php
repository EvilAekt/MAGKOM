@props([
    'routeMetrics' => [],
])

{{-- ENTERPRISE JAMBO DATA TABLE: TELEMETRI RUTE HTTP TERKINI (AC-4, AC-6) --}}
<div class="telemetry-table-card rounded-xl border border-slate-200 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900 overflow-hidden">

    <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2"
         style="border-top: 3px solid #01347C;">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">Enterprise Jambo Table</span>
                <span class="rounded bg-indigo-100 px-2 py-0.5 text-[10px] font-bold text-indigo-800 dark:bg-indigo-950 dark:text-indigo-300">Last 40 Requests</span>
            </div>
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white mt-1 m-0">
                <i class="fa fa-exchange text-primary" aria-hidden="true"></i> Telemetri Rute HTTP Terkini (Last 40 Requests)
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 m-0">
                Rekaman aliran request real-time dari terminable middleware untuk profiling performa endpoint.
            </p>
        </div>
        <!-- 3-Band Semantic Legend (Harmonized with Canvas 2D & Historia Spec §4.4) -->
        <div class="flex items-center gap-3 text-xs font-mono text-slate-500 dark:text-slate-400">
            <span class="inline-flex items-center gap-1.5">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Optimal (&lt;50ms)
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="h-2 w-2 rounded-full bg-amber-500"></span> Normal (50-200ms)
            </span>
            <span class="inline-flex items-center gap-1.5">
                <span class="h-2 w-2 rounded-full bg-rose-500 animate-pulse"></span> Latensi Tinggi (&gt;200ms)
            </span>
        </div>
    </div>

    <div class="table-responsive overflow-x-auto">
        <table class="table table-striped jambo_table bulk_action w-full text-left text-xs mb-0">
            <thead class="bg-slate-800 text-slate-100 dark:bg-slate-950 uppercase tracking-wider text-[11px] font-bold">
                <tr class="headings">
                    <th scope="col" class="column-title py-2.5 px-3" style="width: 90px;">WAKTU</th>
                    <th scope="col" class="column-title py-2.5 px-3" style="width: 80px;">METODE</th>
                    <th scope="col" class="column-title py-2.5 px-3">RUTE / ENDPOINT URI</th>
                    <th scope="col" class="column-title py-2.5 px-3 text-center" style="width: 110px;">STATUS HTTP</th>
                    <th scope="col" class="column-title py-2.5 px-3 text-right" style="width: 130px;">DURASI LATENSI</th>
                    <th scope="col" class="column-title py-2.5 px-3 text-right" style="width: 110px;">MEMORI RUNTIME</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse($routeMetrics as $metric)
                    @php
                        $ms = (float) $metric['duration_ms'];
                        $method = strtoupper((string) ($metric['method'] ?? 'GET'));
                        $statusCode = (int) ($metric['status_code'] ?? 200);

                        // Method Badges
                        $methodBadgeClass = match($method) {
                            'GET' => 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 border border-blue-300 dark:border-blue-800',
                            'POST' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800',
                            'DELETE' => 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-300 dark:border-rose-800',
                            'PUT', 'PATCH' => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-300 dark:border-amber-800',
                            default => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200'
                        };

                        // HTTP Status Code Badges
                        $statusBadgeClass = match(true) {
                            $statusCode >= 500 => 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-300 border border-rose-300 dark:border-rose-800',
                            $statusCode >= 400 => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-300 dark:border-amber-800',
                            $statusCode >= 300 => 'bg-amber-50 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 border border-amber-200 dark:border-amber-800',
                            default => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800'
                        };

                        // 3-Band Latency Semantic Thresholds (Harmonized with Canvas 2D: <50ms, 50-200ms, >200ms)
                        $latencyClass = match(true) {
                            $ms < 50 => 'text-emerald-600 dark:text-emerald-400 font-bold',
                            $ms <= 200 => 'text-amber-600 dark:text-amber-400 font-bold',
                            default => 'text-rose-600 dark:text-rose-400 font-bold animate-pulse'
                        };
                    @endphp
                    <tr class="even pointer hover:bg-blue-50/50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="py-2.5 px-3 font-mono text-slate-500 dark:text-slate-400" style="font-size: 0.75rem;">
                            <i class="fa fa-clock-o text-muted" aria-hidden="true"></i> {{ $metric['time'] }}
                        </td>
                        <td class="py-2.5 px-3">
                            <span class="inline-flex items-center rounded-sm px-2 py-0.5 text-[10px] font-black tracking-wide {{ $methodBadgeClass }}">
                                {{ $method }}
                            </span>
                        </td>
                        <td class="py-2.5 px-3">
                            <div class="font-bold text-slate-900 dark:text-white" style="font-size: 0.8125rem;">
                                {{ $metric['route_name'] }}
                            </div>
                            <div class="font-mono text-slate-400 text-[11px] truncate max-w-md">
                                {{ $metric['uri'] }}
                            </div>
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-black {{ $statusBadgeClass }}">
                                {{ $statusCode }} {{ $statusCode === 200 ? 'OK' : ($statusCode === 302 ? 'Found' : '') }}
                            </span>
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono {{ $latencyClass }}" style="font-size: 0.8125rem;">
                            {{ number_format($ms, 2) }} ms
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono text-slate-500 dark:text-slate-400" style="font-size: 0.75rem;">
                            {{ number_format((float) ($metric['memory_mb'] ?? 0), 2) }} MB
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

    <!-- Table Footer Clean UI (No internal token leaks) -->
    <div class="p-3 bg-slate-50 dark:bg-slate-950/60 border-t border-slate-200 dark:border-slate-800 text-[11px] text-slate-500 dark:text-slate-400 flex items-center justify-between font-mono">
        <span>Aliran Telemetri Rute HTTP &bull; Auto-Update via SSE</span>
        <span class="font-bold text-slate-700 dark:text-slate-300">Gentelella .jambo_table</span>
    </div>

</div>
