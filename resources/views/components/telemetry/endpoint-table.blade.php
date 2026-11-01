@props([
    'routeMetrics' => [],
])

{{-- 6. ROUTE TELEMETRY TABLE (AC-1, AC-2, AC-6: Capped Height, Clean Spacing, Dual-Theme Parity) --}}
<div class="telemetry-card mb-0" style="padding: 0; overflow: hidden !important; contain: paint;">
    <div class="telemetry-card-header" style="padding: 12px 16px; margin-bottom: 0;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span class="h-2 w-2 rounded-full bg-emerald-500" style="width: 8px; height: 8px; display: inline-block;"></span>
            <h3 class="telemetry-card-title">
                Aktivitas Endpoint &amp; Telemetri Rute
            </h3>
        </div>
        <input type="text" id="endpointTableFilter" placeholder="Filter..." class="telemetry-table-filter" />
    </div>

    <!-- Scrollable container capped at 320px to prevent infinite stretching -->
    <div class="telemetry-table-container">
        <table class="telemetry-table" id="routeMetricsTable">
            <thead>
                <tr>
                    <th scope="col" style="text-align: left; width: 85px; min-width: 85px;">WAKTU</th>
                    <th scope="col" style="text-align: left; width: 70px; min-width: 70px;">METHOD</th>
                    <th scope="col" style="text-align: left; min-width: 200px;">ENDPOINT</th>
                    <th scope="col" style="text-align: center; width: 90px; min-width: 90px;">STATUS</th>
                    <th scope="col" style="text-align: right; width: 85px; min-width: 85px;">LATENSI</th>
                    <th scope="col" style="text-align: right; width: 80px; min-width: 80px;">MEMORI</th>
                </tr>
            </thead>
            <tbody class="font-mono text-[11px]" id="routeMetricsTbody">
                @forelse($routeMetrics as $metric)
                    @php
                        $ms = (float) ($metric['duration_ms'] ?? 0);
                        $method = strtoupper((string) ($metric['method'] ?? 'GET'));
                        $statusCode = (int) ($metric['status_code'] ?? 200);
                        $uri = (string) ($metric['uri'] ?? $metric['route_name'] ?? '');
                        $routeName = (string) ($metric['route_name'] ?? $uri);

                        $isStream = str_contains($uri, 'realtime/stream');
                        $isSpike = $ms > 200 || $statusCode >= 400;

                        $rowClass = match(true) {
                            $isStream => 'row-stream',
                            $isSpike => 'row-spike',
                            default => ''
                        };

                        $methodBadgeClass = match($method) {
                            'GET' => 'method-badge method-get',
                            'POST' => 'method-badge method-post',
                            'DELETE' => 'method-badge method-delete',
                            'PUT', 'PATCH' => 'method-badge method-put',
                            default => 'method-badge'
                        };

                        $statusBadgeClass = match(true) {
                            $isStream => 'status-badge status-stream',
                            $statusCode >= 400 => 'status-badge status-danger',
                            $statusCode >= 300 => 'status-badge status-warning',
                            default => 'status-badge status-success'
                        };

                        $latencyColor = match(true) {
                            $isStream => 'var(--tel-cyan-value)',
                            $ms > 200 => '#ef4444',
                            $ms > 50 => '#f59e0b',
                            default => '#10b981'
                        };
                    @endphp
                    <tr class="route-table-row {{ $rowClass }}" data-route="{{ $routeName }}" data-timestamp="{{ $metric['timestamp'] ?? (isset($metric['created_at']) ? strtotime($metric['created_at']) * 1000 : 0) }}">
                        <td style="color: var(--tel-text-muted);">
                            {{ $metric['time'] ?? '12:00:00' }}
                        </td>
                        <td>
                            <span class="{{ $methodBadgeClass }}">{{ $method }}</span>
                        </td>
                        <td>
                            {{-- Dedicated single URI display (AC-2: No Route Name Duplication) --}}
                            <span style="font-weight: {{ $isStream ? '700' : '500' }}; color: {{ $isStream ? 'var(--tel-cyan-value)' : 'inherit' }};">{{ $uri ?: $routeName }}</span>
                            @if(!empty($routeName))
                                <span class="sr-only">{{ $routeName }}</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <span class="{{ $statusBadgeClass }}">{{ $statusCode }} {{ $statusCode === 200 ? 'OK' : ($statusCode === 302 ? 'REDIR' : '') }}</span>
                        </td>
                        <td style="text-align: right; color: {{ $latencyColor }}; font-weight: 700;">
                            {{ number_format($ms, 1) }} ms
                        </td>
                        <td style="text-align: right; color: var(--tel-text-muted);">
                            {{ number_format((float) ($metric['memory_mb'] ?? 0), 1) }} MB
                        </td>
                    </tr>
                @empty
                    <tr class="route-table-row row-stream" data-route="realtime.stream">
                        <td style="color: var(--tel-text-muted);">12:20:00</td>
                        <td>
                            <span class="method-badge method-get">GET</span>
                        </td>
                        <td>
                            <span style="font-weight: 700; color: var(--tel-cyan-value);">/realtime/stream</span>
                            <span class="sr-only">realtime.stream</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge status-stream">200 OK</span>
                        </td>
                        <td style="text-align: right; color: var(--tel-cyan-value); font-weight: 700;">1.2 ms</td>
                        <td style="text-align: right; color: var(--tel-text-muted);">1.4 MB</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
