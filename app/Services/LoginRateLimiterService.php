<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class LoginRateLimiterService
{
    /**
     * Ambang batas maksimum percobaan login per layer.
     */
    public const MAX_ATTEMPTS_PAIR = 3;       // Layer 3: Kombinasi Akun + IP (3x salah)
    public const MAX_ATTEMPTS_ACCOUNT = 5;    // Layer 1: Per Akun dari berbagai IP (5x salah)
    public const MAX_ATTEMPTS_IP = 20;        // Layer 2: Per IP menyerang berbagai akun (20x salah)
    public const MAX_ATTEMPTS_GLOBAL = 300;   // Layer 4: Ambang batas global seluruh aplikasi (300x/menit)

    /**
     * Durasi cooldown dasar dalam detik (1 menit).
     */
    public const DECAY_SECONDS = 60;

    /**
     * Menghasilkan throttle key untuk Layer 1 (Per-Account terlepas dari IP).
     */
    public function getAccountThrottleKey(string $normalizedEmail): string
    {
        return 'login_act:'.sha1(strtolower(trim($normalizedEmail)));
    }

    /**
     * Menghasilkan throttle key untuk Layer 2 (Per-IP).
     */
    public function getIpThrottleKey(string $clientIp): string
    {
        return 'login_ip:'.$clientIp;
    }

    /**
     * Menghasilkan throttle key untuk Layer 3 (Per-Account + IP pair).
     */
    public function getPairThrottleKey(string $normalizedEmail, string $clientIp): string
    {
        return 'login_pair:'.sha1(strtolower(trim($normalizedEmail)).'|'.$clientIp);
    }

    /**
     * Menghasilkan throttle key untuk Layer 4 (Global endpoint).
     */
    public function getGlobalThrottleKey(): string
    {
        return 'login_global_throttle';
    }

    /**
     * Memeriksa apakah permintaan login saat ini terblokir oleh salah satu dari 4 layer rate limiting.
     *
     * @return array{locked: bool, seconds: int, layer: string|null, reason: string|null}
     */
    public function checkLockout(string $normalizedEmail, string $clientIp): array
    {
        $pairKey = $this->getPairThrottleKey($normalizedEmail, $clientIp);
        $accountKey = $this->getAccountThrottleKey($normalizedEmail);
        $ipKey = $this->getIpThrottleKey($clientIp);
        $globalKey = $this->getGlobalThrottleKey();

        // Layer 3: Cek pasangan Akun + IP
        if (RateLimiter::tooManyAttempts($pairKey, self::MAX_ATTEMPTS_PAIR)) {
            $seconds = RateLimiter::availableIn($pairKey);

            return [
                'locked' => true,
                'seconds' => $seconds,
                'layer' => 'pair',
                'reason' => 'Percobaan kata sandi salah 3 kali untuk kombinasi akun dan IP ini.',
            ];
        }

        // Layer 1: Cek batas Akun (Distributed Brute-force protection)
        if (RateLimiter::tooManyAttempts($accountKey, self::MAX_ATTEMPTS_ACCOUNT)) {
            $seconds = RateLimiter::availableIn($accountKey);

            return [
                'locked' => true,
                'seconds' => $seconds,
                'layer' => 'account',
                'reason' => 'Akun ini sedang mengalami terlalu banyak percobaan masuk dari berbagai IP (Distributed Brute-force detected).',
            ];
        }

        // Layer 2: Cek batas IP (Credential Stuffing protection)
        if (RateLimiter::tooManyAttempts($ipKey, self::MAX_ATTEMPTS_IP)) {
            $seconds = RateLimiter::availableIn($ipKey);

            return [
                'locked' => true,
                'seconds' => $seconds,
                'layer' => 'ip',
                'reason' => 'Alamat IP ini melakukan terlalu banyak percobaan masuk ke berbagai akun.',
            ];
        }

        // Layer 4: Cek batas Global endpoint
        if (RateLimiter::tooManyAttempts($globalKey, self::MAX_ATTEMPTS_GLOBAL)) {
            $seconds = RateLimiter::availableIn($globalKey);

            return [
                'locked' => true,
                'seconds' => $seconds,
                'layer' => 'global',
                'reason' => 'Lonjakan lalu lintas autentikasi terdeteksi di seluruh sistem.',
            ];
        }

        return [
            'locked' => false,
            'seconds' => 0,
            'layer' => null,
            'reason' => null,
        ];
    }

    /**
     * Mencatat kegagalan percobaan login pada seluruh layer rate limiting.
     */
    public function recordFailure(string $normalizedEmail, string $clientIp): void
    {
        $pairKey = $this->getPairThrottleKey($normalizedEmail, $clientIp);
        $accountKey = $this->getAccountThrottleKey($normalizedEmail);
        $ipKey = $this->getIpThrottleKey($clientIp);
        $globalKey = $this->getGlobalThrottleKey();

        // Hitung progressive backoff berdasarkan riwayat lockout berulang
        $repeatLockKey = "login_streak:{$accountKey}";
        $streak = (int) Cache::get($repeatLockKey, 0);
        $cooldown = self::DECAY_SECONDS;
        if ($streak >= 2) {
            $cooldown = 300; // 5 menit jika berulang kali terblokir
        }

        RateLimiter::hit($pairKey, $cooldown);
        RateLimiter::hit($accountKey, $cooldown);
        RateLimiter::hit($ipKey, $cooldown);
        RateLimiter::hit($globalKey, self::DECAY_SECONDS);
    }

    /**
     * Membersihkan counter rate limit setelah pengguna berhasil masuk.
     */
    public function clear(string $normalizedEmail, string $clientIp): void
    {
        $pairKey = $this->getPairThrottleKey($normalizedEmail, $clientIp);
        $accountKey = $this->getAccountThrottleKey($normalizedEmail);

        RateLimiter::clear($pairKey);
        RateLimiter::clear($accountKey);
        Cache::forget("login_streak:{$accountKey}");
        // Catatan: $ipKey dan $globalKey sengaja tidak dibersihkan total agar IP yang sama tidak bisa membajak akun lain
    }
}
