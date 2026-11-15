<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Tests\TestCase;

class TrustProxiesSecurityTest extends TestCase
{
    private ?string $originalTrustedProxiesEnv;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalTrustedProxiesEnv = getenv('TRUSTED_PROXIES') !== false ? (string) getenv('TRUSTED_PROXIES') : null;
    }

    protected function tearDown(): void
    {
        if ($this->originalTrustedProxiesEnv !== null) {
            putenv("TRUSTED_PROXIES={$this->originalTrustedProxiesEnv}");
            $_ENV['TRUSTED_PROXIES'] = $this->originalTrustedProxiesEnv;
            $_SERVER['TRUSTED_PROXIES'] = $this->originalTrustedProxiesEnv;
        } else {
            putenv('TRUSTED_PROXIES');
            unset($_ENV['TRUSTED_PROXIES'], $_SERVER['TRUSTED_PROXIES']);
        }

        parent::tearDown();
    }

    public function test_wildcard_trusted_proxies_resolves_client_ip_from_forwarded_header(): void
    {
        putenv('TRUSTED_PROXIES=*');
        $_ENV['TRUSTED_PROXIES'] = '*';
        $_SERVER['TRUSTED_PROXIES'] = '*';

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
        putenv('TRUSTED_PROXIES=10.0.0.1, 10.0.0.2');
        $_ENV['TRUSTED_PROXIES'] = '10.0.0.1, 10.0.0.2';
        $_SERVER['TRUSTED_PROXIES'] = '10.0.0.1, 10.0.0.2';

        $middleware = new TrustProxies;

        // 1. Request from trusted proxy (10.0.0.1)
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

        // 2. Request from untrusted remote address (192.168.1.50) attempting to spoof header
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
        putenv('TRUSTED_PROXIES');
        unset($_ENV['TRUSTED_PROXIES'], $_SERVER['TRUSTED_PROXIES']);

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
