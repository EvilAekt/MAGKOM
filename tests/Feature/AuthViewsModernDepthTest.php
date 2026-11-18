<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\PengaturanSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthViewsModernDepthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);
        $this->seed(PengaturanSeeder::class);
    }

    public function test_login_page_renders_with_modern_depth_layout(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('data-auth-canvas', false);
        $response->assertSee('Selamat datang');
        $response->assertSee('MAGKOM');
        $response->assertDontSee('auth-visual-panel');
    }

    public function test_register_page_renders_with_modern_depth_layout(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertSee('data-auth-canvas', false);
        $response->assertDontSee('auth-visual-panel');
    }

    public function test_forgot_password_page_renders_with_modern_depth_layout(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertOk();
        $response->assertSee('data-auth-canvas', false);
        $response->assertSee('Lupa Password?');
        $response->assertDontSee('auth-visual-panel');
    }

    public function test_ganti_password_page_renders_with_modern_depth_layout(): void
    {
        $user = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'must_change_password' => true,
        ]);

        $response = $this->actingAs($user)->get(route('password.ganti'));

        $response->assertOk();
        $response->assertSee('data-auth-canvas', false);
        $response->assertSee('Ganti Kata Sandi');
        $response->assertSee('current_password');
        $response->assertSee('data-action="toggle-password"', false);
        $response->assertSee('Perbarui Kata Sandi & Lanjutkan');
        $response->assertDontSee('auth-visual-panel');
    }

    public function test_verify_email_page_renders_with_modern_depth_layout(): void
    {
        $user = User::factory()->unverified()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($user)->get(route('verification.notice'));

        $response->assertOk();
        $response->assertSee('data-auth-canvas', false);
        $response->assertSee('Verifikasi Alamat Email');
        $response->assertSee('Kirim Ulang Email Verifikasi');
        $response->assertDontSee('auth-visual-panel');
    }

    public function test_two_factor_challenge_page_renders_with_modern_depth_layout(): void
    {
        $user = User::factory()->create([
            'role' => RoleEnum::Admin,
            'two_factor_confirmed_at' => now(),
        ]);

        $response = $this->withSession(['auth.2fa.user_id' => $user->id])
            ->get(route('two-factor.challenge'));

        $response->assertOk();
        $response->assertSee('data-auth-canvas', false);
        $response->assertSee('Otentikasi Dua Faktor');
        $response->assertSee('Kode Autentikasi (6 Digit)');
        $response->assertDontSee('auth-visual-panel');
    }
}
