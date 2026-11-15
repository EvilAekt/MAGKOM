<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class TurnstileService
{
    protected const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /**
     * Memverifikasi token Cloudflare Turnstile dengan perlindungan Anti-Replay (SEC-002).
     *
     * @return array{success: bool, reachable: bool, error_codes: array<string>}
     */
    public function verify(?string $token, ?string $remoteIp = null): array
    {
        $enabled = (bool) config('services.turnstile.enabled', false);
        if (! $enabled) {
            // Jika turnstile tidak diaktifkan via config (misal dev lokal murni)
            return [
                'success' => true,
                'reachable' => true,
                'error_codes' => [],
            ];
        }

        if (empty($token) || ! is_string($token)) {
            return [
                'success' => false,
                'reachable' => true,
                'error_codes' => ['missing-input-response'],
            ];
        }

        // 1. Anti-Replay Protection: Cegah penggunaan token yang sama berulang kali
        $tokenHash = hash('sha256', $token);
        $cacheKey = "turnstile_used:{$tokenHash}";
        if (Cache::has($cacheKey)) {
            return [
                'success' => false,
                'reachable' => true,
                'error_codes' => ['token-already-redeemed'],
            ];
        }

        $secret = config('services.turnstile.secret_key');
        if (empty($secret)) {
            // Secret key belum disetel pada environment yang mewajibkan Turnstile
            return [
                'success' => false,
                'reachable' => false,
                'error_codes' => ['missing-secret-key'],
            ];
        }

        try {
            $payload = [
                'secret' => $secret,
                'response' => $token,
            ];

            if ($remoteIp) {
                $payload['remoteip'] = $remoteIp;
            }

            $response = Http::timeout(5)
                ->asForm()
                ->post(self::VERIFY_URL, $payload);

            if (! $response->successful()) {
                return [
                    'success' => false,
                    'reachable' => false,
                    'error_codes' => ['http-error-'.$response->status()],
                ];
            }

            $body = $response->json();
            $success = (bool) ($body['success'] ?? false);

            if ($success) {
                // Tandai token telah digunakan (TTL 10 menit untuk mencegah replay)
                Cache::put($cacheKey, true, now()->addMinutes(10));
            }

            return [
                'success' => $success,
                'reachable' => true,
                'error_codes' => (array) ($body['error-codes'] ?? []),
            ];
        } catch (Throwable $e) {
            // Layanan pihak ketiga Cloudflare timeout atau network down
            return [
                'success' => false,
                'reachable' => false,
                'error_codes' => ['service-unreachable', $e->getMessage()],
            ];
        }
    }
}
