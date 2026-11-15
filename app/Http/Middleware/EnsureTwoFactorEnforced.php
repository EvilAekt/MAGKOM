<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorEnforced
{
    /**
     * Menegakkan kewajiban 2FA untuk peran administratif (SuperAdmin, Admin, Pembimbing) dengan masa tenggang (AUTH-002).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        // Periksa apakah peran pengguna termasuk yang wajib 2FA
        $rolesWajib = [
            RoleEnum::SuperAdmin,
            RoleEnum::Admin,
        ];

        if ((bool) config('auth.2fa_enforce_pembimbing', true)) {
            $rolesWajib[] = RoleEnum::Pembimbing;
        }

        if (! in_array($user->role, $rolesWajib, true)) {
            return $next($request);
        }

        // Jika 2FA sudah aktif, izinkan lanjut
        if ($user->hasEnabledTwoFactor()) {
            return $next($request);
        }

        // Izinkan rute esensial untuk penyelesaian aktivasi 2FA, logout, dan ganti password
        $allowedRoutes = [
            '2fa.settings',
            '2fa.enable',
            '2fa.confirm',
            '2fa.disable',
            '2fa.regenerate-recovery-codes',
            'superadmin.2fa.settings',
            'superadmin.2fa.enable',
            'superadmin.2fa.confirm',
            'superadmin.2fa.disable',
            'superadmin.2fa.regenerate-recovery-codes',
            'logout',
            'password.ganti',
            'password.ganti.update',
        ];

        $currentRoute = (string) $request->route()?->getName();
        if (in_array($currentRoute, $allowedRoutes, true)) {
            return $next($request);
        }

        // Cek masa tenggang aktivasi (Grace Period) untuk akun baru (default: 7 hari)
        $graceDays = (int) config('auth.2fa_grace_days', 7);
        $createdAt = $user->created_at ?? now();
        $graceDeadline = $createdAt->copy()->addDays($graceDays);

        if (now()->lessThan($graceDeadline)) {
            // Masih dalam masa tenggang: Izinkan akses dengan peringatan
            $sisaHari = max(1, now()->diffInDays($graceDeadline));
            session()->flash('warning_2fa_grace', "Pemberitahuan Keamanan: Peran akun Anda mewajibkan aktivasi Autentikasi Dua Faktor (2FA). Masa tenggang tersisa {$sisaHari} hari sebelum akses dashboard dibatasi.");

            return $next($request);
        }

        // Masa tenggang habis: Arahkan paksa ke halaman pengaturan 2FA
        $targetRoute = $user->isSuperAdmin() ? 'superadmin.2fa.settings' : '2fa.settings';

        return redirect()->route($targetRoute)
            ->with('error', 'Kewajiban Keamanan: Peran akun Anda mewajibkan penggunaan Autentikasi Dua Faktor (2FA) untuk melindungi data pribadi dan dokumen peserta magang. Harap selesaikan aktivasi 2FA di bawah ini untuk melanjutkan.');
    }
}
