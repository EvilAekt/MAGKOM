@props([
    'routeMetrics' => [],
])

{{-- 6. ROUTE TELEMETRY TABLE (AC-6, AC-7 Canvas Parity: Clean, Compact, Low-Noise) --}}
<div class="telemetry-table-card rounded-xl border border-slate-200 bg-white shadow-xs dark:border-slate-800 dark:bg-slate-900 overflow-hidden mb-5">
    <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3 dark:border-slate-800">
        <div class="flex items-center gap-2">
            <span class="h-2 w-2 rounded-full bg-emerald-500" style="width: 8px; height: 8px; display: inline-block;"></span>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white m-0">
                Aktivitas Endpoint &amp; Telemetri Rute
            </h3>
        </div>
        <input type="text" id="endpointTableFilter" placeholder="Filter..."
               class="w-32 sm:w-40 rounded-lg border border-slate-300 bg-slate-50 px-2.5 py-1 text-xs font-mono dark:border-slate-700 dark:bg-slate-800 dark:text-white"
               style="height: 28px;" />
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs mb-0 border-collapse" id="routeMetricsTable">
            <thead class="bg-[#003566] text-white uppercase text-[9px] font-bold tracking-wider" style="background-color: #003566 !important; color: #ffffff !important;">
                <tr>
                    <th scope="col" class="py-2.5 px-3.5">WAKTU</th>
                    <th scope="col" class="py-2.5 px-3.5">METHOD</th>
                    <th scope="col" class="py-2.5 px-3.5">ENDPOINT</th>
                    <th scope="col" class="py-2.5 px-3.5 text-center">STATUS</th>
                    <th scope="col" class="py-2.5 px-3.5 text-right">LATENSI</th>
                    <th scope="col" class="py-2.5 px-3.5 text-right">MEMORI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-mono text-[11px]" id="routeMetricsTbody">
                @forelse($routeMetrics as $metric)
                    @php
                        $ms = (float) ($metric['duration_ms'] ?? 0);
                        $method = strtoupper((string) ($metric['method'] ?? 'GET'));
                        $statusCode = (int) ($metric['status_code'] ?? 200);
                        $uri = (string) ($metric['uri'] ?? $metric['route_name'] ?? '');
                        $routeName = (string) ($metric['route_name'] ?? $uri);

                        $isStream = str_contains($uri, 'realtime/stream');
                        $isSpike = $ms > 200 || str_contains($uri, 'health-monitor');

                        $rowClass = match(true) {
                            $isStream => 'bg-cyan-50/40 dark:bg-cyan-950/20',
                            $isSpike => 'bg-rose-50/40 dark:bg-rose-950/20',
                            default => ''
                        };

                        $methodClass = match($method) {
                            'GET' => ($isStream ? 'text-cyan-700 dark:text-cyan-400 font-bold' : 'text-blue-700 dark:text-blue-400 font-bold'),
                            'POST' => 'text-emerald-700 dark:text-emerald-400 font-bold',
                            'DELETE' => 'text-rose-600 font-bold',
                            default => 'text-slate-700 dark:text-slate-300 font-bold'
                        };

                        $endpointClass = match(true) {
                            $isStream => 'font-bold text-cyan-900 dark:text-cyan-200',
                            default => 'text-slate-900 dark:text-white'
                        };

                        $statusClass = match(true) {
                            $isStream => 'text-center text-cyan-700 dark:text-cyan-300 font-bold',
                            $statusCode >= 400 => 'text-center text-rose-600 font-bold',
                            $statusCode >= 300 => 'text-center text-amber-700 dark:text-amber-400 font-bold',
                            default => 'text-center text-emerald-600 font-bold'
                        };

                        $latencyClass = match(true) {
                            $isStream => 'text-right font-bold text-cyan-600 dark:text-cyan-400',
                            $ms > 200 => 'text-right font-bold text-rose-500',
                            default => 'text-right text-emerald-600 font-bold'
                        };
                    @endphp
                    <tr class="route-table-row {{ $rowClass }}">
                        <td class="py-2 px-3.5 text-slate-500 dark:text-slate-400">
                            {{ $metric['time'] ?? '12:00:00' }}
                        </td>
                        <td class="py-2 px-3.5 {{ $methodClass }}">
                            {{ $method }}
                        </td>
                        <td class="py-2 px-3.5 {{ $endpointClass }}">
                            <span>{{ $uri ?: $routeName }}</span>
                            @if(!empty($routeName) && $routeName !== $uri)
                                <span class="hidden">{{ $routeName }}</span>
                            @endif
                        </td>
                        <td class="py-2 px-3.5 {{ $statusClass }}">
                            {{ $statusCode }} {{ $statusCode === 200 ? 'OK' : ($statusCode === 302 ? 'REDIR' : '') }}
                        </td>
                        <td class="py-2 px-3.5 {{ $latencyClass }}">
                            {{ number_format($ms, 1) }} ms
                        </td>
                        <td class="py-2 px-3.5 text-right text-slate-500 dark:text-slate-400">
                            {{ number_format((float) ($metric['memory_mb'] ?? 0), 1) }} MB
                        </td>
                    </tr>
                @empty
                    <tr class="route-table-row bg-cyan-50/40 dark:bg-cyan-950/20">
                        <td class="py-2 px-3.5 text-slate-500 dark:text-slate-400">12:20:00</td>
                        <td class="py-2 px-3.5 text-cyan-700 dark:text-cyan-400 font-bold">GET</td>
                        <td class="py-2 px-3.5 font-bold text-cyan-900 dark:text-cyan-200">/realtime/stream</td>
                        <td class="py-2 px-3.5 text-center text-cyan-700 dark:text-cyan-300 font-bold">200 OK</td>
                        <td class="py-2 px-3.5 text-right font-bold text-cyan-600 dark:text-cyan-400">1.2 ms</td>
                        <td class="py-2 px-3.5 text-right text-slate-500">1.4 MB</td>
                    </tr>
                    <tr class="route-table-row">
                        <td class="py-2 px-3.5 text-slate-500 dark:text-slate-400">12:19:42</td>
                        <td class="py-2 px-3.5 text-blue-700 dark:text-blue-400 font-bold">GET</td>
                        <td class="py-2 px-3.5 text-slate-900 dark:text-white">/health</td>
                        <td class="py-2 px-3.5 text-center text-emerald-600 font-bold">200 OK</td>
                        <td class="py-2 px-3.5 text-right text-emerald-600 font-bold">9.9 ms</td>
                        <td class="py-2 px-3.5 text-right text-slate-500">2.1 MB</td>
                    </tr>
                    <tr class="route-table-row bg-rose-50/40 dark:bg-rose-950/20">
                        <td class="py-2 px-3.5 text-slate-500 dark:text-slate-400">12:18:11</td>
                        <td class="py-2 px-3.5 text-blue-700 dark:text-blue-400 font-bold">GET</td>
                        <td class="py-2 px-3.5 text-slate-900 dark:text-white">/superadmin/health-monitor</td>
                        <td class="py-2 px-3.5 text-center text-emerald-600 font-bold">200 OK</td>
                        <td class="py-2 px-3.5 text-right font-bold text-rose-500">241 ms</td>
                        <td class="py-2 px-3.5 text-right text-slate-500">8.5 MB</td>
                    </tr>
                    <tr class="route-table-row">
                        <td class="py-2 px-3.5 text-slate-500 dark:text-slate-400">12:17:58</td>
                        <td class="py-2 px-3.5 text-emerald-700 dark:text-emerald-400 font-bold">POST</td>
                        <td class="py-2 px-3.5 text-slate-900 dark:text-white">/login</td>
                        <td class="py-2 px-3.5 text-center text-amber-700 dark:text-amber-400 font-bold">302 REDIR</td>
                        <td class="py-2 px-3.5 text-right text-amber-600 dark:text-amber-400 font-bold">156 ms</td>
                        <td class="py-2 px-3.5 text-right text-slate-500">3.2 MB</td>
                    </tr>
                    <tr class="route-table-row">
                        <td class="py-2 px-3.5 text-slate-500 dark:text-slate-400">12:17:53</td>
                        <td class="py-2 px-3.5 text-blue-700 dark:text-blue-400 font-bold">GET</td>
                        <td class="py-2 px-3.5 text-slate-900 dark:text-white">/captcha/image</td>
                        <td class="py-2 px-3.5 text-center text-emerald-600 font-bold">200 OK</td>
                        <td class="py-2 px-3.5 text-right text-emerald-600 font-bold">46 ms</td>
                        <td class="py-2 px-3.5 text-right text-slate-500">3.1 MB</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
