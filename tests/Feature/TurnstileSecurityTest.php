<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Rules\TurnstileRule;
use App\Services\TurnstileService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TurnstileSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Config::set('services.turnstile.enabled', true);
        Config::set('services.turnstile.site_key', 'test-site-key-turnstile');
        Config::set('services.turnstile.secret_key', 'test-secret-key-turnstile');
        Config::set('services.turnstile.strict_testing', true);
    }

    /**
     * Memastikan token Turnstile yang valid berhasil diverifikasi oleh server-side.
     */
    public function test_turnstile_valid_token_verifies_successfully(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
                'challenge_ts' => now()->toIso8601String(),
                'hostname' => 'magkom.diskominfo.go.id',
            ], 200),
        ]);

        $service = app(TurnstileService::class);
        $result = $service->verify('valid-test-token-12345', '127.0.0.1');

        $this->assertTrue($result['success']);
        $this->assertTrue($result['reachable']);
        $this->assertEmpty($result['error_codes']);
    }

    /**
     * Memastikan token kosong atau hilang ditolak dengan aman.
     */
    public function test_turnstile_missing_token_fails(): void
    {
        $service = app(TurnstileService::class);
        $result = $service->verify(null, '127.0.0.1');

        $this->assertFalse($result['success']);
        $this->assertContains('missing-input-response', $result['error_codes']);
    }

    /**
     * Memastikan Anti-Replay: Token yang sama tidak dapat digunakan dua kali.
     */
    public function test_turnstile_token_cannot_be_replayed(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([
                'success' => true,
            ], 200),
        ]);

        $service = app(TurnstileService::class);
        $token = 'unique-one-time-token-abcdef';

        // Penggunaan pertama: Sukses
        $result1 = $service->verify($token, '127.0.0.1');
        $this->assertTrue($result1['success']);

        // Penggunaan kedua dengan token yang sama persis: Ditolak oleh cache anti-replay
        $result2 = $service->verify($token, '127.0.0.1');
        $this->assertFalse($result2['success']);
        $this->assertContains('token-already-redeemed', $result2['error_codes']);
    }

    /**
     * Memastikan Fail-Closed: Jika Cloudflare mengalami timeout atau error, sistem tidak membiarkan bot lolos.
     */
    public function test_turnstile_fail_closed_when_provider_is_unreachable(): void
    {
        Http::fake([
            'https://challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(null, 500),
        ]);

        $rule = new TurnstileRule(isRegistration: true);
        $failed = false;
        $errorMessage = '';

        $rule->validate('cf-turnstile-response', 'some-token', function ($message) use (&$failed, &$errorMessage) {
            $failed = true;
            $errorMessage = $message;
        });

        $this->assertTrue($failed);
        $this->assertStringContainsString('mengalami gangguan', $errorMessage);
    }
}
