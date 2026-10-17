<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\CaptchaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityDynamicCaptchaTest extends TestCase
{
    use RefreshDatabase;

    protected CaptchaService $captchaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->captchaService = app(CaptchaService::class);
    }

    public function test_captcha_image_endpoint_returns_valid_svg_and_sets_session(): void
    {
        $response = $this->get(route('captcha.image'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml; charset=utf-8');
        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('no-cache', (string) $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('must-revalidate', (string) $response->headers->get('Cache-Control'));
        $response->assertHeader('X-Content-Type-Options', 'nosniff');

        $content = $response->getContent();
        $this->assertStringContainsString('<svg', $content);
        $this->assertStringContainsString('viewBox="0 0 160 50"', $content);
        $this->assertStringContainsString('</svg>', $content);

        // Pastikan kode di session terbentuk sepanjang 4 karakter
        $sessionCode = session(CaptchaService::SESSION_CODE);
        $this->assertNotNull($sessionCode);
        $this->assertEquals(4, strlen($sessionCode));
    }

    public function test_captcha_code_changes_dynamically_on_subsequent_requests(): void
    {
        $this->get(route('captcha.image'));
        $firstCode = session(CaptchaService::SESSION_CODE);
        $this->assertNotNull($firstCode);

        $this->get(route('captcha.image'));
        $secondCode = session(CaptchaService::SESSION_CODE);
        $this->assertNotNull($secondCode);
        $this->assertEquals(4, strlen($secondCode));
    }

    public function test_login_rejects_missing_captcha_when_strict_testing_is_enabled(): void
    {
        config(['captcha.strict_testing' => true]);

        $response = $this->post(route('login'), [
            'email' => 'peserta@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertSessionHasErrors('captcha');
    }

    public function test_login_rejects_incorrect_captcha_code(): void
    {
        $user = User::factory()->create([
            'email' => 'peserta@example.com',
            'password' => bcrypt('Password123!'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $this->withSession([
            CaptchaService::SESSION_CODE => '7K9M',
            CaptchaService::SESSION_EXPIRES_AT => now()->addMinutes(10)->timestamp,
        ]);

        $response = $this->post(route('login'), [
            'email' => 'peserta@example.com',
            'password' => 'Password123!',
            'captcha' => 'WRNG',
        ]);

        $response->assertSessionHasErrors('captcha');
        $this->assertGuest();
    }

    public function test_login_succeeds_with_valid_case_insensitive_captcha_code(): void
    {
        $user = User::factory()->create([
            'email' => 'peserta@example.com',
            'password' => bcrypt('Password123!'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $this->withSession([
            CaptchaService::SESSION_CODE => '7K9M',
            CaptchaService::SESSION_EXPIRES_AT => now()->addMinutes(10)->timestamp,
        ]);

        // Input huruf kecil ('7k9m') untuk menguji case-insensitivity
        $response = $this->post(route('login'), [
            'email' => 'peserta@example.com',
            'password' => 'Password123!',
            'captcha' => '7k9m',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
    }

    public function test_captcha_is_single_use_and_prevents_replay_attack(): void
    {
        $user = User::factory()->create([
            'email' => 'peserta@example.com',
            'password' => bcrypt('Password123!'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        // Simulasi percobaan pertama: kode valid dimasukkan
        $this->withSession([
            CaptchaService::SESSION_CODE => '2X8Q',
            CaptchaService::SESSION_EXPIRES_AT => now()->addMinutes(10)->timestamp,
        ]);

        $res1 = $this->post(route('login'), [
            'email' => 'peserta@example.com',
            'password' => 'Password123!',
            'captcha' => '2X8Q',
        ]);
        $res1->assertSessionHasNoErrors();

        // Logout untuk persiapan request kedua
        auth()->logout();

        // Request kedua mengulang kode yang sama tanpa refresh captcha (replay attack)
        $res2 = $this->post(route('login'), [
            'email' => 'peserta@example.com',
            'password' => 'Password123!',
            'captcha' => '2X8Q',
        ]);

        $res2->assertSessionHasErrors('captcha');
        $this->assertGuest();
    }

    public function test_expired_captcha_is_rejected(): void
    {
        // Set kode captcha yang sudah kedaluwarsa 10 detik lalu
        $this->withSession([
            CaptchaService::SESSION_CODE => 'M4N5',
            CaptchaService::SESSION_EXPIRES_AT => now()->subSeconds(10)->timestamp,
        ]);

        $response = $this->post(route('login'), [
            'email' => 'peserta@example.com',
            'password' => 'Password123!',
            'captcha' => 'M4N5',
        ]);

        $response->assertSessionHasErrors('captcha');
        $this->assertGuest();
    }

    public function test_register_succeeds_with_valid_captcha(): void
    {
        $this->withSession([
            CaptchaService::SESSION_CODE => 'B8P3',
            CaptchaService::SESSION_EXPIRES_AT => now()->addMinutes(10)->timestamp,
        ]);

        $response = $this->post(route('register'), [
            'nama' => 'Peserta Baru',
            'email' => 'pesertabaru@example.com',
            'no_telp' => '081234567890',
            'password' => 'Rahasia123!',
            'password_confirmation' => 'Rahasia123!',
            'setuju_privasi' => 'on',
            'captcha' => 'b8p3',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('peserta.profil.show'));
        $this->assertDatabaseHas('users', [
            'email' => 'pesertabaru@example.com',
            'nama' => 'Peserta Baru',
        ]);
    }
}
