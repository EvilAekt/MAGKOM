<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Memaksa staf atau peserta khusus mengganti password default saat login pertama (BR-15).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            // Izinkan rute ganti password dan logout
            if (! $request->routeIs('password.ganti*') && ! $request->routeIs('logout')) {
                return redirect()->route('password.ganti')
                    ->with('warning', 'Demi keamanan, Anda wajib mengganti kata sandi awal sebelum melanjutkan.');
            }
        }

        return $next($request);
    }
}
