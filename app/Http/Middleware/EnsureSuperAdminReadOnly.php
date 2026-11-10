<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureSuperAdminReadOnly
{
    /**
     * Memastikan SuperAdmin strictly read-only pada modul operasional role lain (SEC-12).
     * Seluruh method non-safe (POST, PUT, PATCH, DELETE) ditolak dengan HTTP 403.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role === RoleEnum::SuperAdmin && ! $request->isMethodSafe()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'SuperAdmin hanya memiliki hak akses lihat (read-only) pada modul operasional role lain.',
                ], 403);
            }

            abort(403, 'SuperAdmin hanya memiliki hak akses lihat (read-only) pada modul operasional role lain.');
        }

        return $next($request);
    }
}
