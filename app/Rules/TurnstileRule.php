<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TurnstileRule implements ValidationRule
{
    /**
     * @param bool $isRegistration True untuk registrasi (fail-closed), false untuk login (fail-open) (AC-14-2).
     */
    public function __construct(
        protected bool $isRegistration = false
    ) {}

    /**
     * Jalankan validasi token Turnstile.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $turnstileService = app(TurnstileService::class);
        $result = $turnstileService->verify(is_string($value) ? $value : null, request()->ip());

        // Jika layanan pihak ketiga tidak dapat dihubungi (timeout / network error):
        if (! $result['reachable']) {
            if ($this->isRegistration) {
                // AC-14-2: Registrasi ditolak jika captcha down (Fail-Closed)
                $fail('Layanan verifikasi keamanan (captcha) sedang mengalami gangguan. Silakan coba beberapa saat lagi.');
            }
            // AC-14-2: Login tetap diizinkan berjalan dengan perlindungan rate limit (Fail-Open)
            return;
        }

        // Jika layanan dapat dihubungi tetapi token tidak valid / bot terdeteksi
        if (! $result['success']) {
            $fail('Verifikasi keamanan (captcha) tidak valid atau telah kedaluwarsa. Silakan ulangi kembali.');
        }
    }
}
