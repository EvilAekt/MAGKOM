<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestResetPasswordCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_fails_if_email_not_found(): void
    {
        $this->artisan('auth:test-reset notfound@example.com --no-send')
            ->expectsOutputToContain("Akun dengan email 'notfound@example.com' TIDAK DITEMUKAN di database!")
            ->assertExitCode(1);
    }

    public function test_command_fails_if_user_inactive(): void
    {
        User::factory()->create([
            'email' => 'inactive@example.com',
            'role' => RoleEnum::Peserta,
            'status_aktif' => false,
        ]);

        $this->artisan('auth:test-reset inactive@example.com --no-send')
            ->expectsOutputToContain('Akun ini berstatus non-aktif.')
            ->assertExitCode(1);
    }

    public function test_command_generates_token_and_displays_reset_url_with_no_send(): void
    {
        $user = User::factory()->create([
            'email' => 'peserta.aktif@example.com',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $this->artisan('auth:test-reset peserta.aktif@example.com --no-send')
            ->expectsOutputToContain('Token berhasil dibuat dan disimpan di tabel password_reset_tokens!')
            ->expectsOutputToContain('TAUTAN RESET PASSWORD LANGSUNG')
            ->expectsOutputToContain('peserta.aktif')
            ->assertExitCode(0);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'peserta.aktif@example.com',
        ]);
    }
}
