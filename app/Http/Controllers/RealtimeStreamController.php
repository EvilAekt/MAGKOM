<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\RealtimeEventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RealtimeStreamController extends Controller
{
    public function __construct(
        protected RealtimeEventService $eventService
    ) {}

    /**
     * Endpoint Server-Sent Events (SSE) dengan proteksi anti-starvation PHP-FPM (RFC 8895).
     * Menerapkan Capped Streaming Loop (20 detik) dan session_write_close() agar request lain lancar.
     */
    public function stream(Request $request): StreamedResponse
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Unauthenticated');
        }

        $rawLastId = $request->header('Last-Event-ID', $request->query('last_event_id', '0'));
        $lastEventId = is_numeric($rawLastId) ? (int) $rawLastId : 0;

        // CRITICAL: Lepaskan session lock PHP agar tab lain dari pengguna ini tidak terblokir
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        // Matikan kompresi dan buffer PHP jika didukung lingkungan server
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', '1');
        }
        @ini_set('zlib.output_compression', '0');
        @ini_set('implicit_flush', '1');

        return response()->stream(function () use ($user, $lastEventId) {
            // Instruksikan browser native EventSource untuk menyambung ulang tiap 2500ms
            echo "retry: 2500\n\n";
            if (ob_get_level() > 0) {
                @ob_flush();
            }
            flush();

            $startTime = time();
            $currentLastId = $lastEventId;
            $maxDuration = app()->environment('testing') ? 1 : 20;
            $sleepSeconds = app()->environment('testing') ? 0 : 2;

            // Loop terkontrol maksimal 20 detik (anti-starvation PHP-FPM)
            while ((time() - $startTime) < $maxDuration) {
                if (connection_aborted()) {
                    break;
                }

                $events = $this->eventService->getPendingEvents($user, $currentLastId, 10);

                if ($events->isNotEmpty()) {
                    foreach ($events as $event) {
                        echo "id: {$event->id}\n";
                        echo "event: {$event->event_type}\n";
                        echo "data: " . json_encode($event->payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n\n";
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

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no', // Menghindari buffering pada Nginx Reverse Proxy
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
}
