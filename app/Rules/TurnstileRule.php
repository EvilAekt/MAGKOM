<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TurnstileRule implements ValidationRule
{
    /**
     * @param  bool  $isRegistration  Indikator konteks formulir registrasi vs login
     */
    public function __construct(
        protected bool $isRegistration = false
    ) {}

    /**
     * Jalankan validasi token Turnstile (SEC-002: Fail-Closed Protection).
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 1. Bypass pengujian otomatis hanya jika diizinkan config
        if (app()->runningUnitTests() && ! config('services.turnstile.strict_testing', false)) {
            if (in_array($value, ['10000000-aaaa-bbbb-cccc-000000000001', 'mock-turnstile-token', 'pass', 'test-turnstile-pass'], true)) {
                return;
            }
        }

        // 2. Bypass lokal pengembang hanya jika opt-in eksplisit (TURNSTILE_DEV_BYPASS=true) dan bukan di production
        if (app()->isLocal() && (bool) config('services.turnstile.dev_bypass', false)) {
            return;
        }

        $turnstileService = app(TurnstileService::class);
        $result = $turnstileService->verify(is_string($value) ? $value : null, request()->ip());

        // 3. SEC-002: Kebijakan kegagalan pihak ketiga (Cloudflare down / unreachable)
        if (! $result['reachable']) {
            if ($this->isRegistration || (bool) config('services.turnstile.login_fail_closed', false)) {
                $fail('Layanan verifikasi bot keamanan (Cloudflare Turnstile) sedang mengalami gangguan jaringan atau batas waktu. Harap muat ulang halaman dan coba kembali.');

                return;
            }

            // Pada login, fail-open saat layanan pihak ketiga down untuk menjamin ketersediaan akses pengguna,
            // tetap dilindungi oleh multi-layer rate limiting dan verifikasi kredensial (AC-14-2).
            return;
        }

        // 4. Token tidak valid, expired, atau terdeteksi bot
        if (! $result['success']) {
            if (in_array('token-already-redeemed', $result['error_codes'], true)) {
                $fail('Token verifikasi keamanan telah digunakan sebelumnya (anti-replay). Silakan ulangi verifikasi.');

                return;
            }

            $fail('Verifikasi keamanan bot tidak valid atau telah kedaluwarsa. Silakan centang kembali kotak verifikasi.');
        }
    }
}
