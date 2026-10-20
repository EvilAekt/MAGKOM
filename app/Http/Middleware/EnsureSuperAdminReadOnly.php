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
     * Memastikan SuperAdmin strictly read-only pada modul fungsi Pembimbing (SEC-12).
     * Seluruh method non-safe (POST, PUT, PATCH, DELETE) ditolak dengan HTTP 403.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role === RoleEnum::SuperAdmin && ! $request->isMethodSafe()) {
            abort(403, 'SuperAdmin hanya memiliki akses baca (read-only) pada modul Pembimbing (SEC-12).');
        }

        return $next($request);
    }
}
