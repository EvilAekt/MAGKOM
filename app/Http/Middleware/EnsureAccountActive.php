<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class EnsureAccountActive
{
    /**
     * Memastikan akun pengguna masih dalam status aktif (SEC-03).
     * Jika status_aktif bernilai false, sesi langsung diputus secara paksa.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && ! (bool) $user->status_aktif) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda sedang dinonaktifkan. Silakan hubungi Administrator Kominfo.']);
        }

        return $next($request);
    }
}
