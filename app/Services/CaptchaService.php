<?php

declare(strict_types=1);

namespace App\Services;

class CaptchaService
{
    public const SESSION_CODE = 'captcha_code';
    public const SESSION_EXPIRES_AT = 'captcha_expires_at';

    /**
     * Hasilkan kode acak baru dan simpan ke dalam session dengan TTL.
     */
    public function generateCode(): string
    {
        $pool = (string) config('captcha.characters', '23456789ABCDEFGHJKLMNPQRSTUVWXYZ');
        $length = (int) config('captcha.length', 4);
        $ttl = (int) config('captcha.ttl', 600);

        $poolLength = strlen($pool);
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $pool[random_int(0, $poolLength - 1)];
        }

        session([
            self::SESSION_CODE => $code,
            self::SESSION_EXPIRES_AT => now()->addSeconds($ttl)->timestamp,
        ]);

        return $code;
    }

    /**
     * Render representasi visual kode captcha dalam format SVG murni (tanpa dependensi GD/Imagick).
     */
    public function renderSvg(): string
    {
        $code = $this->generateCode();
        $width = (int) config('captcha.width', 160);
        $height = (int) config('captcha.height', 50);

        // Palet warna untuk elemen noise dan teks
        $textColors = ['#0f172a', '#1e293b', '#1e3a8a', '#1e40af', '#0369a1', '#334155', '#111827'];
        $noiseColors = ['#94a3b8', '#cbd5e1', '#a5b4fc', '#93c5fd', '#64748b'];

        $elements = [];

        // 1. Noise Lines (4-6 garis lengkung dan lurus pengganggu OCR)
        $lineCount = random_int(4, 6);
        for ($i = 0; $i < $lineCount; $i++) {
            $x1 = random_int(5, $width - 5);
            $y1 = random_int(5, $height - 5);
            $x2 = random_int(5, $width - 5);
            $y2 = random_int(5, $height - 5);
            $cx = random_int(20, $width - 20);
            $cy = random_int(5, $height - 5);
            $stroke = $noiseColors[array_rand($noiseColors)];
            $strokeWidth = random_int(10, 20) / 10;
            $opacity = random_int(40, 75) / 100;

            if ($i % 2 === 0) {
                // Bezier quadratic curve
                $elements[] = sprintf(
                    '<path d="M %d %d Q %d %d %d %d" stroke="%s" stroke-width="%.1f" fill="none" opacity="%.2f" />',
                    $x1, $y1, $cx, $cy, $x2, $y2, $stroke, $strokeWidth, $opacity
                );
            } else {
                $elements[] = sprintf(
                    '<line x1="%d" y1="%d" x2="%d" y2="%d" stroke="%s" stroke-width="%.1f" opacity="%.2f" />',
                    $x1, $y1, $x2, $y2, $stroke, $strokeWidth, $opacity
                );
            }
        }

        // 2. Noise Dots (30 titik derau acak)
        for ($i = 0; $i < 30; $i++) {
            $cx = random_int(5, $width - 5);
            $cy = random_int(5, $height - 5);
            $r = random_int(8, 18) / 10;
            $fill = $noiseColors[array_rand($noiseColors)];
            $opacity = random_int(30, 70) / 100;

            $elements[] = sprintf(
                '<circle cx="%d" cy="%d" r="%.1f" fill="%s" opacity="%.2f" />',
                $cx, $cy, $r, $fill, $opacity
            );
        }

        // 3. Render 4 Karakter Captcha dengan distorsi posisi & rotasi
        $charCount = strlen($code);
        $slotWidth = ($width - 30) / max(1, $charCount);

        for ($i = 0; $i < $charCount; $i++) {
            $char = htmlspecialchars($code[$i], ENT_QUOTES | ENT_XML1, 'UTF-8');
            $x = (int) (18 + ($i * $slotWidth) + ($slotWidth / 2) + random_int(-3, 3));
            $y = (int) (32 + random_int(-3, 4));
            $angle = random_int(-18, 18);
            $fontSize = random_int(26, 30);
            $color = $textColors[array_rand($textColors)];

            $elements[] = sprintf(
                '<text x="%d" y="%d" fill="%s" font-family="monospace, \'Courier New\', Courier, sans-serif" font-size="%d" font-weight="bold" letter-spacing="2" transform="rotate(%d, %d, %d)" text-anchor="middle" dominant-baseline="central">%s</text>',
                $x, $y, $color, $fontSize, $angle, $x, $y, $char
            );
        }

        $innerContent = implode("\n    ", $elements);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}" role="img" aria-label="Security Captcha Image">
    <defs>
        <linearGradient id="captchaBg" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#f8fafc" />
            <stop offset="100%" stop-color="#e2e8f0" />
        </linearGradient>
    </defs>
    <rect width="100%" height="100%" rx="6" fill="url(#captchaBg)" stroke="#cbd5e1" stroke-width="1.5" />
    {$innerContent}
</svg>
SVG;
    }

    /**
     * Validasi kode captcha terhadap session saat ini.
     * Bersifat Single-Use (Anti-Replay) dan Case-Insensitive.
     */
    public function validate(?string $input): bool
    {
        if ($input === null || trim($input) === '') {
            return false;
        }

        $expected = session(self::SESSION_CODE);
        $expiresAt = session(self::SESSION_EXPIRES_AT);

        // Langsung hapus session agar kode tidak dapat digunakan kembali (anti-replay)
        session()->forget([self::SESSION_CODE, self::SESSION_EXPIRES_AT]);

        if (! is_string($expected) || $expected === '') {
            return false;
        }

        // Cek masa kedaluwarsa TTL
        if ($expiresAt !== null && now()->timestamp > (int) $expiresAt) {
            return false;
        }

        return hash_equals(strtoupper(trim($expected)), strtoupper(trim($input)));
    }

    /**
     * Helper untuk kebutuhan automated test suite.
     */
    public function setTestCode(string $code, int $ttlSeconds = 600): void
    {
        session([
            self::SESSION_CODE => $code,
            self::SESSION_EXPIRES_AT => now()->addSeconds($ttlSeconds)->timestamp,
        ]);
    }
}
