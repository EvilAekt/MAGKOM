<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Tests\TestCase;

class TrustProxiesSecurityTest extends TestCase
{
    private ?string $originalConfig;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalConfig = config('trustedproxy.proxies');
    }

    protected function tearDown(): void
    {
        config(['trustedproxy.proxies' => $this->originalConfig]);
        parent::tearDown();
    }

    public function test_middleware_does_not_call_env_directly(): void
    {
        $middlewareFile = file_get_contents(app_path('Http/Middleware/TrustProxies.php'));
        $this->assertNotFalse($middlewareFile);

        // Memastikan tidak ada pemanggilan env('TRUSTED_PROXIES') di middleware agar aman dari config:cache
        $this->assertDoesNotMatchRegularExpression('/\benv\s*\(/', $middlewareFile, 'TrustProxies middleware dilarang memanggil env() secara langsung.');
    }

    public function test_config_file_exists_and_binds_to_environment(): void
    {
        $this->assertFileExists(config_path('trustedproxy.php'));
        $config = require config_path('trustedproxy.php');
        $this->assertIsArray($config);
        $this->assertArrayHasKey('proxies', $config);
        $this->assertArrayHasKey('headers', $config);
    }

    public function test_wildcard_trusted_proxies_resolves_client_ip_from_forwarded_header(): void
    {
        config(['trustedproxy.proxies' => '*']);

        $middleware = new TrustProxies;
        $request = Request::create('/test-ip', 'GET', [], [], [], [
            'REMOTE_ADDR' => '10.0.0.1',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.195',
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ]);

        $resolvedIp = null;
        $isSecure = false;

        $middleware->handle($request, function (Request $req) use (&$resolvedIp, &$isSecure) {
            $resolvedIp = $req->ip();
            $isSecure = $req->isSecure();

            return response('OK');
        });

        $this->assertSame('203.0.113.195', $resolvedIp);
        $this->assertTrue($isSecure);
    }

    public function test_specific_trusted_proxy_ip_accepts_valid_proxy_and_rejects_untrusted_source(): void
    {
        config(['trustedproxy.proxies' => '10.0.0.1, 10.0.0.2']);

        $middleware = new TrustProxies;

        // 1. Request dari trusted proxy (10.0.0.1)
        $trustedRequest = Request::create('/test-ip', 'GET', [], [], [], [
            'REMOTE_ADDR' => '10.0.0.1',
            'HTTP_X_FORWARDED_FOR' => '198.51.100.50',
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ]);

        $resolvedTrustedIp = null;
        $middleware->handle($trustedRequest, function (Request $req) use (&$resolvedTrustedIp) {
            $resolvedTrustedIp = $req->ip();

            return response('OK');
        });
        $this->assertSame('198.51.100.50', $resolvedTrustedIp);

        // 2. Request dari alamat remote tidak terpercaya (192.168.1.50) yang mencoba spoofing header
        $untrustedRequest = Request::create('/test-ip', 'GET', [], [], [], [
            'REMOTE_ADDR' => '192.168.1.50',
            'HTTP_X_FORWARDED_FOR' => '1.2.3.4',
        ]);

        $resolvedUntrustedIp = null;
        $middleware->handle($untrustedRequest, function (Request $req) use (&$resolvedUntrustedIp) {
            $resolvedUntrustedIp = $req->ip();

            return response('OK');
        });
        $this->assertSame('192.168.1.50', $resolvedUntrustedIp);
    }

    public function test_null_trusted_proxies_ignores_forwarded_headers(): void
    {
        config(['trustedproxy.proxies' => null]);

        $middleware = new TrustProxies;
        $request = Request::create('/test-ip', 'GET', [], [], [], [
            'REMOTE_ADDR' => '192.168.1.88',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.1',
        ]);

        $resolvedIp = null;
        $middleware->handle($request, function (Request $req) use (&$resolvedIp) {
            $resolvedIp = $req->ip();

            return response('OK');
        });

        $this->assertSame('192.168.1.88', $resolvedIp);
    }
}
