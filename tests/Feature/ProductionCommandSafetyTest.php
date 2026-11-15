<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionCommandSafetyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan auth:test-reset dilarang berjalan di lingkungan production.
     */
    public function test_auth_test_reset_is_blocked_in_production(): void
    {
        $this->app['env'] = 'production';

        $this->artisan('auth:test-reset', ['email' => 'peserta@test.com'])
            ->expectsOutputToContain('PERINTAH DIAGNOSTIK INI DILARANG DIJALANKAN DI LINGKUNGAN PRODUCTION')
            ->assertExitCode(1);
    }

    /**
     * Memastikan magkom:seed-20-peserta dilarang berjalan di lingkungan production.
     */
    public function test_seed_dummy_peserta_is_blocked_in_production(): void
    {
        $this->app['env'] = 'production';

        $this->artisan('magkom:seed-20-peserta')
            ->expectsOutputToContain('PERINTAH SEEDER INI DILARANG KERAS DI LINGKUNGAN PRODUCTION')
            ->assertExitCode(1);
    }

    /**
     * Memastikan magkom:generate-sample-docs dilarang berjalan di lingkungan production.
     */
    public function test_generate_sample_docs_is_blocked_in_production(): void
    {
        $this->app['env'] = 'production';

        $this->artisan('magkom:generate-sample-docs', ['pendaftaran_id' => 1])
            ->expectsOutputToContain('PERINTAH INI DILARANG DIJALANKAN DI LINGKUNGAN PRODUCTION')
            ->assertExitCode(1);
    }
}
