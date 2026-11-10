<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeExternalUrlRule implements ValidationRule
{
    /**
     * Jalankan validasi tautan eksternal yang aman (HTTPS Enforced, Anti-SSRF, & Anti-Crash).
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('Format tautan harus berupa teks URL yang valid.');

            return;
        }

        $url = trim($value);

        if ($url === '') {
            return;
        }

        if (strlen($url) > 2048) {
            $fail('Tautan tidak boleh melebihi 2048 karakter.');

            return;
        }

        // 1. Larang karakter baris baru / pemisah yang merusak parsing HTTP
        if (preg_match('/[\r\n\t]/', $url)) {
            $fail('Tautan tidak boleh mengandung karakter kontrol atau spasi baris baru.');

            return;
        }

        // 2. Wajib menggunakan skema aman HTTPS
        if (! str_starts_with(strtolower($url), 'https://')) {
            $fail('Tautan wajib menggunakan protokol aman HTTPS (https://).');

            return;
        }

        // 3. Validasi sintaks URL standar RFC
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            $fail('Format tautan tidak valid atau tidak dapat diuraikan.');

            return;
        }

        // 4. Penguraian komponen host
        $parsed = parse_url($url);
        if (! is_array($parsed) || empty($parsed['host'])) {
            $fail('Format nama domain tautan tidak valid.');

            return;
        }

        $host = strtolower((string) $parsed['host']);

        // 5. Anti-SSRF: Blokir hostname internal, metadata cloud, dan domain lokal
        $blockedHosts = [
            'localhost',
            '127.0.0.1',
            '0.0.0.0',
            '169.254.169.254', // AWS, GCP, Azure metadata service
            'metadata.google.internal',
            'instance-data',
        ];

        if (in_array($host, $blockedHosts, true)
            || str_ends_with($host, '.localhost')
            || str_ends_with($host, '.test')
            || str_ends_with($host, '.local')
            || str_ends_with($host, '.internal')
        ) {
            $fail('Tautan tidak boleh mengarah ke alamat internal atau lokal (Anti-SSRF).');

            return;
        }

        // 6. Anti-SSRF: Blokir IP address privat / reserved (RFC 1918, RFC 3927)
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            if (! filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                $fail('Tautan tidak boleh mengarah ke alamat IP privat atau reserved (Anti-SSRF).');

                return;
            }
        }
    }
}
