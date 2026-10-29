<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\RealtimeEventTypeEnum;
use App\Models\RouteMetric;
use App\Services\RealtimeEventService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RecordRoutePerformance
{
    /**
     * Start execution timer before passing request down the pipeline.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('_exec_start_hrtime', hrtime(true));
        $request->attributes->set('_exec_start_memory', memory_get_usage(false));

        return $next($request);
    }

    /**
     * Terminable execution: persist execution time to database and broadcast to SSE outbox.
     * Runs after HTTP response is already dispatched to the client (Zero latency penalty).
     */
    public function terminate(Request $request, Response $response): void
    {
        // Ignore internal stream loops, debug tools, and static asset routes
        if ($this->shouldIgnore($request)) {
            return;
        }

        $startHrtime = $request->attributes->get('_exec_start_hrtime');
        if (! $startHrtime) {
            return;
        }

        try {
            $durationMs = round((hrtime(true) - $startHrtime) / 1e6, 2);
            $memoryMb = round(memory_get_peak_usage(false) / (1024 * 1024), 2);

            $route = $request->route();
            $routeName = $route ? $route->getName() : null;
            $uri = '/'.ltrim($request->path(), '/');
            $method = strtoupper($request->method());
            $statusCode = $response->getStatusCode();
            $userId = $request->user()?->id;
            $ip = $request->ip();

            $metric = RouteMetric::create([
                'method' => $method,
                'route_name' => $routeName,
                'uri' => $uri,
                'status_code' => $statusCode,
                'duration_ms' => $durationMs,
                'memory_mb' => $memoryMb,
                'user_id' => $userId,
                'ip' => $ip,
                'created_at' => now(),
            ]);

            // Broadcast to SuperAdmin SSE channel for real-time live canvas updates
            app(RealtimeEventService::class)->publish(
                type: RealtimeEventTypeEnum::TelemetriRoute,
                payload: [
                    'id' => $metric->id,
                    'method' => $metric->method,
                    'route_name' => $metric->route_name ?: $metric->uri,
                    'uri' => $metric->uri,
                    'status_code' => $metric->status_code,
                    'duration_ms' => (float) $metric->duration_ms,
                    'memory_mb' => (float) $metric->memory_mb,
                    'time' => now()->format('H:i:s'),
                ],
                role: 'superadmin'
            );
        } catch (Throwable $e) {
            // Fail silently to never disrupt application stability
            report($e);
        }
    }

    /**
     * Determine if request path should be excluded from telemetry metrics.
     */
    protected function shouldIgnore(Request $request): bool
    {
        return $request->is('realtime/stream*')
            || $request->is('_ignition*')
            || $request->is('_debugbar*')
            || $request->is('build/*')
            || $request->is('storage/*')
            || $request->is('favicon.ico');
    }
}
