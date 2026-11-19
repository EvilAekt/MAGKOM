<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProductionReadinessCommandTest extends TestCase
{
    use RefreshDatabase;

    protected string $originalEnv;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalEnv = (string) $this->app['env'];
    }

    protected function tearDown(): void
    {
        $this->app['env'] = $this->originalEnv;
        parent::tearDown();
    }

    /**
     * Memastikan preflight check gagal jika lingkungan produksi memiliki konfigurasi tidak aman.
     */
    public function test_preflight_fails_when_production_environment_is_unhardened(): void
    {
        $this->app['env'] = 'production';
        Config::set('app.debug', true);
        Config::set('session.secure', false);
        Config::set('services.turnstile.enabled', false);

        $this->artisan('app:check-production-readiness')
            ->expectsOutputToContain('PREFLIGHT GAGAL: DITEMUKAN KONFIGURASI KEAMANAN KRITIS TIDAK VALID')
            ->assertExitCode(1);
    }

    /**
     * Memastikan preflight check berhasil ketika seluruh parameter keamanan kritis produksi terpenuhi.
     */
    public function test_preflight_passes_when_all_security_parameters_are_valid(): void
    {
        $this->app['env'] = 'production';
        putenv('SEED_DEMO_ACCOUNTS=false');
        $_ENV['SEED_DEMO_ACCOUNTS'] = 'false';
        $_SERVER['SEED_DEMO_ACCOUNTS'] = 'false';
        Config::set('app.debug', false);
        Config::set('session.secure', true);
        Config::set('session.http_only', true);
        Config::set('mail.default', 'smtp');
        Config::set('services.turnstile.enabled', true);
        Config::set('services.turnstile.site_key', '0x4AAAAAAATestSiteKey');
        Config::set('services.turnstile.secret_key', '0x4AAAAAAATestSecretKey');
        Config::set('backup.encryption_key', 'separate_super_secret_backup_key_32');
        Config::set('backup.offsite_disk', 's3');
        Config::set('filesystems.disks.s3', ['driver' => 's3']);
        Config::set('filesystems.disks.private.throw', true);
        Config::set('trustedproxy.proxies', '*');

        // Pastikan tidak ada akun dengan password default pabrikan
        User::query()->delete();
        User::factory()->create([
            'email' => 'admin@resmi.go.id',
            'password' => Hash::make('KombinasiUnik9988!@#'),
        ]);

        $this->artisan('app:check-production-readiness')
            ->expectsOutputToContain('PREFLIGHT SUKSES: APLIKASI SIAP DIJALANKAN DI LINGKUNGAN PRODUCTION')
            ->assertExitCode(0);
    }

    /**
     * Memastikan deteksi akun dengan password default (Password123!) menggagalkan preflight.
     */
    public function test_preflight_detects_default_passwords_and_fails(): void
    {
        $this->app['env'] = 'production';
        Config::set('app.debug', false);
        Config::set('session.secure', true);
        Config::set('services.turnstile.enabled', true);
        Config::set('services.turnstile.site_key', '0x4AAAAAAATestSiteKey');
        Config::set('services.turnstile.secret_key', '0x4AAAAAAATestSecretKey');
        Config::set('backup.encryption_key', 'separate_super_secret_backup_key_32');
        Config::set('backup.offsite_disk', 's3');
        Config::set('filesystems.disks.s3', ['driver' => 's3']);
        Config::set('filesystems.disks.private.throw', true);

        User::factory()->create([
            'email' => 'leaked@magkom.go.id',
            'password' => Hash::make('Password123!'),
        ]);

        $this->artisan('app:check-production-readiness')
            ->expectsOutputToContain('Ditemukan akun aktif menggunakan password default (Password123!)')
            ->assertExitCode(1);
    }
}
