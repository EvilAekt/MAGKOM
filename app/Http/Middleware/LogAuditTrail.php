<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LogAuditTrail
{
    /**
     * Inisialisasi request_id dan pencatatan audit trail (AC-13-1, TDD 9.2).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = (string) Str::uuid();
        $request->attributes->set('request_id', $requestId);
        $request->attributes->set('mulai', hrtime(true));

        $response = $next($request);

        $response->headers->set('X-Request-Id', $requestId);

        return $response;
    }

    /**
     * Terminable execution setelah response dikirim ke browser (NFR-P3, TDD 9.2).
     */
    public function terminate(Request $request, Response $response): void
    {
        try {
            app(AuditService::class)->tulisAksesData($request, $response);
        } catch (Throwable $e) {
            report($e);
        }
    }
}
