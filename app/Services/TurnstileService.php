<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Throwable;

class TurnstileService
{
    protected const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    /**
     * Memverifikasi token Cloudflare Turnstile (AC-14-1, AC-14-2).
     *
     * @return array{success: bool, reachable: bool, error_codes: array<string>}
     */
    public function verify(?string $token, ?string $remoteIp = null): array
    {
        $enabled = (bool) config('services.turnstile.enabled', false);
        if (! $enabled) {
            return [
                'success' => true,
                'reachable' => true,
                'error_codes' => [],
            ];
        }

        if (empty($token)) {
            return [
                'success' => false,
                'reachable' => true,
                'error_codes' => ['missing-input-response'],
            ];
        }

        $secret = config('services.turnstile.secret_key');

        try {
            $payload = [
                'secret' => $secret,
                'response' => $token,
            ];

            if ($remoteIp) {
                $payload['remoteip'] = $remoteIp;
            }

            $response = Http::timeout(3)
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

            return [
                'success' => $success,
                'reachable' => true,
                'error_codes' => (array) ($body['error-codes'] ?? []),
            ];
        } catch (Throwable $e) {
            // Layanan pihak ketiga Cloudflare tidak dapat dihubungi (timeout/unreachable)
            return [
                'success' => false,
                'reachable' => false,
                'error_codes' => ['service-unreachable', $e->getMessage()],
            ];
        }
    }
}
