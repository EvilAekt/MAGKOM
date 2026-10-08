<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureParticipantWritable
{
    /**
     * Memastikan aksi tulis peserta ditolak (403) jika masa magang selesai (masa tenggang) atau nonaktif (AC-08-6, AC-09-5).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Hanya method yang memutasi data (POST, PUT, PATCH, DELETE) yang diperiksa
        if ($user && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            if (! $user->status_aktif) {
                abort(403, 'Akun Anda telah dinonaktifkan. Seluruh aksi penulisan dinonaktifkan.');
            }
        }

        return $next($request);
    }
}
