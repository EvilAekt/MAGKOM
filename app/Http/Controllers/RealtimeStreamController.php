<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\RealtimeEventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class RealtimeStreamController extends Controller
{
    public function __construct(
        protected RealtimeEventService $eventService
    ) {}

    /**
     * Endpoint Server-Sent Events (SSE) dengan proteksi anti-starvation PHP-FPM (RFC 8895).
     * Menerapkan Capped Streaming Loop (20 detik), batas 1 koneksi aktif per user (Cache Lock),
     * dan session_write_close() agar request lain lancar.
     */
    public function stream(Request $request): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        // PERF-001: Jika SSE dinonaktifkan di konfigurasi server, tolak secara graceful dan instruksikan fallback
        if (! config('services.realtime.sse_enabled', true)) {
            return response()->json([
                'error' => 'SSE Disabled',
                'message' => 'Layanan SSE dinonaktifkan untuk menghemat kapasitas worker PHP-FPM. Gunakan fallback polling.',
                'fallback_poll_url' => route('realtime.poll'),
            ], 503, [
                'Retry-After' => '60',
            ]);
        }

        $userId = (int) $user->id;
        $lockKey = "sse:stream:user:{$userId}";

        // Cegah exhaustion worker PHP-FPM: Maksimal 1 stream SSE aktif per akun
        $lockAcquired = Cache::add($lockKey, true, 30);
        if (! $lockAcquired) {
            return response()->json([
                'error' => 'Too Many Concurrent SSE Connections',
                'message' => 'Hanya satu koneksi real-time aktif yang diizinkan per akun.',
            ], 429, [
                'Retry-After' => '10',
            ]);
        }

        $rawLastId = $request->header('Last-Event-ID', $request->query('last_event_id', '0'));
        $lastEventId = is_numeric($rawLastId) ? (int) $rawLastId : 0;

        // CRITICAL: Lepaskan session lock Laravel & PHP agar navigasi/reload pengguna tidak terblokir
        if ($request->hasSession()) {
            $request->session()->save();
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        // Matikan kompresi dan buffer PHP jika didukung lingkungan server
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', '1');
        }
        @ini_set('zlib.output_compression', '0');
        @ini_set('implicit_flush', '1');

        return response()->stream(function () use ($user, $lastEventId, $lockKey) {
            try {
                $startTime = time();
                $currentLastId = $lastEventId;
                $isLocalOrTesting = app()->environment(['local', 'testing']);
                $maxDuration = $isLocalOrTesting ? 1 : 20;
                $sleepSeconds = $isLocalOrTesting ? 0 : 2;
                $retryMs = $isLocalOrTesting ? 3500 : 2500;

                // Instruksikan browser native EventSource untuk menyambung ulang
                echo "retry: {$retryMs}\n\n";
                if (ob_get_level() > 0) {
                    @ob_flush();
                }
                flush();

                // Loop terkontrol (di local/testing: 1 iterasi burst cepat untuk menghindari worker starvation)
                while ((time() - $startTime) < $maxDuration) {
                    if (connection_aborted()) {
                        break;
                    }

                    // Perbarui TTL heartbeat lock
                    Cache::put($lockKey, true, 30);

                    $events = $this->eventService->getPendingEvents($user, $currentLastId, 10);

                    if ($events->isNotEmpty()) {
                        foreach ($events as $event) {
                            echo "id: {$event->id}\n";
                            echo "event: {$event->event_type}\n";
                            echo 'data: '.json_encode($event->payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n\n";
                            $currentLastId = max($currentLastId, (int) $event->id);
                        }
                    } else {
                        // Heartbeat ping comment untuk mencegah timeout intermediate proxy/Nginx
                        echo ": ping\n\n";
                    }

                    if (ob_get_level() > 0) {
                        @ob_flush();
                    }
                    flush();

                    if ($sleepSeconds > 0) {
                        sleep($sleepSeconds);
                    } else {
                        break;
                    }
                }
            } finally {
                Cache::forget($lockKey);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no', // Menghindari buffering pada Nginx Reverse Proxy
        ]);
    }

    /**
     * Endpoint Polling Fallback hemat resource untuk server PHP-FPM terbatas (PERF-001).
     * Mengembalikan event tertunda dalam satu query cepat tanpa menahan worker FastCGI.
     */
    public function poll(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $rawLastId = $request->query('last_event_id', '0');
        $lastEventId = is_numeric($rawLastId) ? (int) $rawLastId : 0;

        $events = $this->eventService->getPendingEvents($user, $lastEventId, 10);

        $maxId = $lastEventId;
        $eventData = [];
        foreach ($events as $event) {
            $maxId = max($maxId, (int) $event->id);
            $eventData[] = [
                'id' => (int) $event->id,
                'event_type' => $event->event_type,
                'payload' => $event->payload,
            ];
        }

        $intervalSeconds = (int) config('services.realtime.polling_interval_seconds', 15);

        return response()->json([
            'status' => 'ok',
            'last_event_id' => $maxId,
            'polling_interval_ms' => $intervalSeconds * 1000,
            'events' => $eventData,
        ]);
    }

    /**
     * Menandai event notifikasi tertentu sebagai telah dibaca.
     */
    public function markRead(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $success = $this->eventService->markAsRead($id, $user);

        return response()->json([
            'success' => $success,
        ]);
    }

    /**
     * Menandai kumpulan event notifikasi sebagai telah dibaca secara batch (PERF-P3-01).
     */
    public function markReadBatch(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'event_ids' => 'required|array|min:1|max:50',
            'event_ids.*' => 'integer|min:1',
        ]);

        $affected = $this->eventService->markBatchAsRead($validated['event_ids'], $user);

        return response()->json([
            'success' => true,
            'affected' => $affected,
        ]);
    }
}
