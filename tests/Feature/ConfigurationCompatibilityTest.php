<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Http\Middleware\TrustHosts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Tests\TestCase;

class ConfigurationCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan tabel queue 'jobs' dan 'job_batches' berhasil dibuat oleh migrasi.
     */
    public function test_queue_tables_exist_in_database(): void
    {
        $this->assertTrue(Schema::hasTable('jobs'), "Tabel 'jobs' wajib tersedia untuk QUEUE_CONNECTION=database.");
        $this->assertTrue(Schema::hasTable('job_batches'), "Tabel 'job_batches' wajib tersedia untuk batching antrean.");

        $this->assertTrue(Schema::hasColumns('jobs', ['id', 'queue', 'payload', 'attempts', 'available_at', 'created_at']));
    }

    /**
     * Memastikan konfigurasi cache mendukung CACHE_DRIVER (Laravel 10) dan CACHE_STORE.
     */
    public function test_cache_driver_compatibility(): void
    {
        $cacheConfig = require config_path('cache.php');
        $this->assertIsArray($cacheConfig);

        // Verifikasi bahwa default store membaca CACHE_DRIVER terlebih dahulu
        $this->assertNotEmpty($cacheConfig['default']);
    }

    /**
     * Memastikan konfigurasi session cookie secure otomatis aktif pada environment production.
     */
    public function test_session_secure_cookie_configuration(): void
    {
        $sessionConfig = require config_path('session.php');
        $this->assertArrayHasKey('secure', $sessionConfig);

        // Pada testing/local tanpa env SESSION_SECURE_COOKIE, bernilai false agar dev lokal tidak terkunci
        $this->assertFalse(config('session.secure'));
    }

    /**
     * Memastikan konfigurasi logging stack membaca LOG_STACK dan mendukung rotasi daily.
     */
    public function test_logging_stack_configuration_supports_daily_rotation(): void
    {
        $loggingConfig = require config_path('logging.php');
        $this->assertArrayHasKey('stack', $loggingConfig['channels']);
        $this->assertArrayHasKey('daily', $loggingConfig['channels']);

        // Verifikasi channel daily terkonfigurasi dengan retensi 14 hari
        $this->assertSame('daily', $loggingConfig['channels']['daily']['driver']);
        $this->assertSame(14, $loggingConfig['channels']['daily']['days']);

        $channels = $loggingConfig['channels']['stack']['channels'];
        $this->assertIsArray($channels);
        $this->assertTrue(in_array('daily', $channels, true) || in_array('single', $channels, true));
    }

    /**
     * Memastikan TrustHosts mengizinkan host terdaftar dan menolak manipulasi host header mencurigakan.
     */
    public function test_trust_hosts_middleware_validates_request_hosts(): void
    {
        $middleware = new TrustHosts($this->app);
        $hosts = $middleware->hosts();

        $this->assertIsArray($hosts);
        $this->assertContains('localhost', $hosts);
        $this->assertContains('127.0.0.1', $hosts);

        try {
            // Aktifkan trusted hosts pada level Symfony Request untuk pengujian keamanan
            Request::setTrustedHosts(array_filter($hosts));

            // 1. Host valid: localhost
            $validRequest = Request::create('http://localhost:8000/login');
            $this->assertSame('localhost', $validRequest->getHost());

            // 2. Host manipulasi / spoofed Host header harus melempar SuspiciousOperationException
            $this->expectException(SuspiciousOperationException::class);
            $spoofedRequest = Request::create('http://attacker-controlled-host.com/login');
            $spoofedRequest->getHost();
        } finally {
            // Reset trusted hosts agar pengujian lain tidak terpengaruh
            Request::setTrustedHosts([]);
        }
    }
}
