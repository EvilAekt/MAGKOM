<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use RuntimeException;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

class ProductionEmailConfigurationTest extends TestCase
{
    protected string $originalEnv;

    protected ?string $originalMailer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalEnv = (string) $this->app['env'];
        $this->originalMailer = config('mail.default');
    }

    protected function tearDown(): void
    {
        $this->app['env'] = $this->originalEnv;
        Config::set('mail.default', $this->originalMailer);
        parent::tearDown();
    }

    /**
     * Memastikan perintah email:validate-config mendeteksi konfigurasi tidak layak saat strict/production.
     */
    public function test_validate_email_config_detects_inadequate_production_settings(): void
    {
        // Dalam testing env, default mailer adalah array/log
        Config::set('mail.default', 'log');

        $this->artisan('email:validate-config', ['--strict' => true])
            ->expectsOutputToContain('GAGAL: Konfigurasi email tidak memenuhi kriteria kelayakan production')
            ->assertExitCode(1);
    }

    /**
     * Memastikan perintah email:validate-config sukses jika konfigurasi SMTP production valid.
     */
    public function test_validate_email_config_passes_with_valid_smtp_settings(): void
    {
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', 'smtp.diskominfo.go.id');
        Config::set('mail.mailers.smtp.port', 587);
        Config::set('mail.mailers.smtp.username', 'notifikasi@diskominfo.go.id');
        Config::set('mail.mailers.smtp.password', 'SecretSmtpPass2026!');
        Config::set('mail.mailers.smtp.encryption', 'tls');
        Config::set('mail.from.address', 'no-reply@diskominfo.go.id');
        Config::set('mail.from.name', 'MAGKOM Diskominfo');

        $this->artisan('email:validate-config', ['--strict' => true])
            ->expectsOutputToContain('SELURUH VALIDASI EMAIL & SMTP MEMENUHI STANDAR PRODUCTION')
            ->assertExitCode(0);
    }

    /**
     * Memastikan pengiriman email di environment production melempar RuntimeException jika driver log digunakan.
     */
    public function test_production_environment_blocks_mail_log_driver_at_runtime(): void
    {
        // Simulasikan app environment production
        $this->app['env'] = 'production';
        Config::set('mail.default', 'log');

        $user = new User([
            'nama' => 'Test User',
            'email' => 'user@test.com',
        ]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("FATAL: Pengiriman email di lingkungan production dilarang menggunakan driver 'log'");

        $event = new MessageSending(
            new Email
        );
        Event::dispatch($event);
    }
}
