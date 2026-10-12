<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePesertaDiterima
{
    /**
     * Memastikan hanya peserta magang yang telah resmi diterima dan ditempatkan (aktif/selesai)
     * yang dapat mengakses fitur pelaksanaan magang (Logbook, Tugas, dan Lembar Nilai).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isPeserta() || ! $user->hasPenempatanMagang()) {
            if ($request->expectsJson() || $request->wantsJson() || $request->routeIs('*.lampiran') || $request->routeIs('*.unduh*')) {
                abort(403, 'Akses ditolak. Fitur ini hanya tersedia bagi peserta magang yang telah resmi diterima dan ditempatkan.');
            }

            return redirect()
                ->route('peserta.dashboard')
                ->with('warning', 'Fitur ini terkunci. Logbook, Tugas, dan Lembar Nilai hanya dapat diakses setelah permohonan magang Anda disetujui dan ditempatkan ke unit kerja.');
        }

        return $next($request);
    }
}
