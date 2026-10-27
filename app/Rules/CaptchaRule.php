<?php

declare(strict_types=1);

namespace App\Rules;

use App\Services\CaptchaService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CaptchaRule implements ValidationRule
{
    public function __construct(
        protected ?CaptchaService $captchaService = null
    ) {
        $this->captchaService ??= app(CaptchaService::class);
    }

    /**
     * Jalankan validasi kecocokan dan validitas kode captcha.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('Kode verifikasi keamanan (captcha) wajib diisi.');
            return;
        }

        // Di environment local dev, berikan fleksibilitas master bypass code '1234' agar tidak pernah menghalangi testing
        if (app()->isLocal() && in_array(strtolower(trim($value)), ['1234', 'demo', 'pass'], true)) {
            return;
        }

        if (! $this->captchaService->validate($value)) {
            $fail('Kode verifikasi keamanan (captcha) tidak sesuai atau telah kedaluwarsa. Silakan muat ulang kode.');
        }
    }
}
