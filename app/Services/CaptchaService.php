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
     * Dirancang tahan terhadap web scraping dan bot OCR, namun tetap sangat jelas bagi mata manusia.
     */
    public function renderSvg(): string
    {
        $code = $this->generateCode();
        $width = (int) config('captcha.width', 160);
        $height = (int) config('captcha.height', 50);

        // Palet warna kontras tinggi untuk teks (mudah dibaca manusia) dan warna aksen noise
        $textColors = ['#003566', '#0f172a', '#1e293b', '#1e3a8a', '#1e40af', '#0369a1'];
        $noiseColors = ['#64748b', '#94a3b8', '#a5b4fc', '#93c5fd', '#38bdf8'];

        $elements = [];

        // 1. Noise Lines (4-6 garis lengkung & lurus pemecah segmentasi OCR)
        $lineCount = random_int(4, 6);
        for ($i = 0; $i < $lineCount; $i++) {
            $x1 = random_int(5, $width - 5);
            $y1 = random_int(8, $height - 8);
            $x2 = random_int(5, $width - 5);
            $y2 = random_int(8, $height - 8);
            $cx = random_int(20, $width - 20);
            $cy = random_int(8, $height - 8);
            $stroke = $noiseColors[array_rand($noiseColors)];
            $strokeWidth = random_int(12, 22) / 10;
            $opacity = random_int(35, 65) / 100;

            if ($i % 2 === 0) {
                // Bezier quadratic curve
                $elements[] = sprintf(
                    '<path d="M %d %d Q %d %d %d %d" stroke="%s" stroke-width="%.1f" fill="none" opacity="%.2f" stroke-linecap="round" />',
                    $x1, $y1, $cx, $cy, $x2, $y2, $stroke, $strokeWidth, $opacity
                );
            } else {
                $elements[] = sprintf(
                    '<line x1="%d" y1="%d" x2="%d" y2="%d" stroke="%s" stroke-width="%.1f" opacity="%.2f" stroke-linecap="round" />',
                    $x1, $y1, $x2, $y2, $stroke, $strokeWidth, $opacity
                );
            }
        }

        // 2. Noise Dots (35 titik derau acak)
        for ($i = 0; $i < 35; $i++) {
            $cx = random_int(5, $width - 5);
            $cy = random_int(5, $height - 5);
            $r = random_int(8, 16) / 10;
            $fill = $noiseColors[array_rand($noiseColors)];
            $opacity = random_int(25, 60) / 100;

            $elements[] = sprintf(
                '<circle cx="%d" cy="%d" r="%.1f" fill="%s" opacity="%.2f" />',
                $cx, $cy, $r, $fill, $opacity
            );
        }

        // 3. Render 4 Karakter Captcha
        // Posisi visual X diatur teratur dari kiri ke kanan (mudah dibaca manusia),
        // tetapi urutan elemen di dalam SVG diacak (DOM Shuffling) untuk menggagalkan bot parser XML!
        $charCount = strlen($code);
        $slotWidth = ($width - 32) / max(1, $charCount);
        $charElements = [];

        for ($i = 0; $i < $charCount; $i++) {
            $char = htmlspecialchars($code[$i], ENT_QUOTES | ENT_XML1, 'UTF-8');
            $visualX = (int) (18 + ($i * $slotWidth) + ($slotWidth / 2) + random_int(-3, 3));
            $visualY = (int) (31 + random_int(-3, 3));
            $angle = random_int(-15, 15);
            $fontSize = random_int(26, 29);
            $color = $textColors[array_rand($textColors)];

            $charElements[] = sprintf(
                '<text x="%d" y="%d" fill="%s" font-family="ui-monospace, Consolas, \'Liberation Mono\', Monaco, monospace" font-size="%d" font-weight="800" letter-spacing="2" transform="rotate(%d, %d, %d)" text-anchor="middle" dominant-baseline="central">%s</text>',
                $visualX, $visualY, $color, $fontSize, $angle, $visualX, $visualY, $char
            );
        }

        // DOM Shuffling: Acak urutan node dalam XML tanpa mengubah posisi visual
        shuffle($charElements);

        // Sisipkan 2 decoy hidden elements untuk meracuni bot regex/XML scraper
        $decoys = [
            '<text x="-999" y="-999" font-size="0" opacity="0" aria-hidden="true">Z</text>',
            '<text x="-999" y="-999" font-size="0" opacity="0" aria-hidden="true">9</text>',
        ];
        array_splice($charElements, random_int(0, count($charElements)), 0, [$decoys[0]]);
        array_splice($charElements, random_int(0, count($charElements)), 0, [$decoys[1]]);

        foreach ($charElements as $el) {
            $elements[] = $el;
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
    <rect width="100%" height="100%" rx="8" fill="url(#captchaBg)" stroke="#cbd5e1" stroke-width="1.5" />
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
