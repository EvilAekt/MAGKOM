<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Contracts\Provider as SocialiteProviderContract;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class ReleaseThreeSocialLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Helper untuk membuat objek User secara aman.
     */
    protected function createUser(array $attributes = []): User
    {
        $user = new User();
        $user->nama = $attributes['nama'] ?? 'Pengguna Uji';
        $user->email = $attributes['email'] ?? ('test_' . uniqid() . '@example.com');
        $user->password = Hash::make('Password123!');
        $user->role = $attributes['role'] ?? RoleEnum::Peserta;
        $user->status_aktif = $attributes['status_aktif'] ?? true;
        $user->email_verified_at = array_key_exists('email_verified_at', $attributes)
            ? $attributes['email_verified_at']
            : now();
        $user->provider = $attributes['provider'] ?? null;
        $user->provider_id = $attributes['provider_id'] ?? null;
        $user->save();

        return $user;
    }

    /**
     * Helper untuk mock respon Google Socialite.
     */
    protected function mockGoogleUser(string $id, string $email, string $name = 'Peserta Google'): void
    {
        $socialiteUser = Mockery::mock(SocialiteUserContract::class);
        $socialiteUser->shouldReceive('getId')->andReturn($id);
        $socialiteUser->shouldReceive('getName')->andReturn($name);
        $socialiteUser->shouldReceive('getEmail')->andReturn($email);
        $socialiteUser->shouldReceive('getNickname')->andReturn(null);

        $provider = Mockery::mock(SocialiteProviderContract::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    public function test_google_oauth_redirect_works(): void
    {
        $response = $this->get(route('oauth.redirect', 'google'));

        $response->assertStatus(302);
        $this->assertStringContainsString('accounts.google.com', (string) $response->headers->get('Location'));
    }

    public function test_unsupported_provider_returns_404(): void
    {
        $responseRedirect = $this->get('/auth/unsupported/redirect');
        $responseRedirect->assertStatus(404);

        $responseCallback = $this->get('/auth/unsupported/callback');
        $responseCallback->assertStatus(404);
    }

    public function test_new_user_can_register_and_login_via_google_oauth(): void
    {
        $uniqueEmail = 'peserta_baru_oauth_' . uniqid() . '@example.com';
        $googleId = 'google_id_' . uniqid();

        $this->mockGoogleUser($googleId, $uniqueEmail, 'Calon Peserta OAuth');

        $response = $this->get(route('oauth.callback', 'google'));

        $response->assertRedirect(route('peserta.profil.show'));
        $this->assertAuthenticated();

        $user = User::where('email', $uniqueEmail)->first();
        $this->assertNotNull($user);
        $this->assertEquals('Calon Peserta OAuth', $user->nama);
        $this->assertEquals(RoleEnum::Peserta, $user->role);
        $this->assertEquals('google', $user->provider);
        $this->assertEquals($googleId, $user->provider_id);
        $this->assertTrue($user->status_aktif);
        $this->assertNotNull($user->email_verified_at);

        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'users',
            'entitas_id' => $user->id,
            'tipe' => 'aksi',
        ]);
    }

    public function test_existing_verified_peserta_can_link_account_and_login(): void
    {
        $existingUser = $this->createUser([
            'email' => 'peserta_verified_' . uniqid() . '@example.com',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'provider' => null,
            'provider_id' => null,
        ]);

        $googleId = 'google_id_linked_' . uniqid();
        $this->mockGoogleUser($googleId, $existingUser->email, $existingUser->nama);

        $response = $this->get(route('oauth.callback', 'google'));

        $response->assertRedirect(route('peserta.pendaftaran.index'));
        $this->assertAuthenticatedAs($existingUser);

        $existingUser->refresh();
        $this->assertEquals('google', $existingUser->provider);
        $this->assertEquals($googleId, $existingUser->provider_id);
    }

    public function test_anti_hijacking_unverified_existing_user_cannot_link_oauth(): void
    {
        // Pengguna lokal belum verifikasi email (misalnya registrasi belum konfirmasi)
        $unverifiedUser = $this->createUser([
            'email' => 'peserta_unverified_' . uniqid() . '@example.com',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'email_verified_at' => null,
            'provider' => null,
            'provider_id' => null,
        ]);

        $attackerGoogleId = 'google_attacker_' . uniqid();
        $this->mockGoogleUser($attackerGoogleId, $unverifiedUser->email, 'Attacker User');

        $response = $this->get(route('oauth.callback', 'google'));

        // Harus ditolak dan diarahkan ke login dengan error keamanan
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        // Pastikan akun korban TIDAK tertaut
        $unverifiedUser->refresh();
        $this->assertNull($unverifiedUser->provider);
        $this->assertNull($unverifiedUser->provider_id);
    }

    public function test_staff_roles_cannot_use_social_login(): void
    {
        $adminUser = $this->createUser([
            'email' => 'admin_staff_' . uniqid() . '@example.com',
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'provider' => null,
            'provider_id' => null,
        ]);

        $this->mockGoogleUser('google_admin_' . uniqid(), $adminUser->email, $adminUser->nama);

        $response = $this->get(route('oauth.callback', 'google'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        $adminUser->refresh();
        $this->assertNull($adminUser->provider);
        $this->assertNull($adminUser->provider_id);
    }

    public function test_existing_oauth_peserta_can_login_directly(): void
    {
        $googleId = 'google_existing_' . uniqid();
        $oauthUser = $this->createUser([
            'email' => 'peserta_oauth_' . uniqid() . '@example.com',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'provider' => 'google',
            'provider_id' => $googleId,
        ]);

        $this->mockGoogleUser($googleId, $oauthUser->email, $oauthUser->nama);

        $response = $this->get(route('oauth.callback', 'google'));

        $response->assertRedirect(route('peserta.pendaftaran.index'));
        $this->assertAuthenticatedAs($oauthUser);
    }

    public function test_inactive_oauth_user_cannot_login(): void
    {
        $googleId = 'google_inactive_' . uniqid();
        $inactiveUser = $this->createUser([
            'email' => 'peserta_nonaktif_' . uniqid() . '@example.com',
            'role' => RoleEnum::Peserta,
            'status_aktif' => false,
            'email_verified_at' => now(),
            'provider' => 'google',
            'provider_id' => $googleId,
        ]);

        $this->mockGoogleUser($googleId, $inactiveUser->email, $inactiveUser->nama);

        $response = $this->get(route('oauth.callback', 'google'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_oauth_callback_handles_provider_exception_gracefully(): void
    {
        $provider = Mockery::mock(SocialiteProviderContract::class);
        $provider->shouldReceive('user')->andThrow(new \Exception('Google OAuth authentication failed or user canceled'));

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('oauth.callback', 'google'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_page_renders_google_login_button(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee(route('oauth.redirect', 'google'));
        $response->assertSee('Masuk dengan Google');
    }

    public function test_register_page_renders_google_register_button(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee(route('oauth.redirect', 'google'));
        $response->assertSee('Daftar dengan Google');
    }

    public function test_simplified_registration_renders_exact_fields(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertSee('name="username"', false);
        $response->assertSee('name="email"', false);
        $response->assertSee('name="no_telp"', false);
        $response->assertSee('name="password"', false);
        $response->assertDontSee('name="password_confirmation"', false);
    }

    public function test_user_can_register_with_username_and_phone_number_without_confirmation(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'Peserta Sleman 2026',
            'email' => 'peserta.sleman@test.id',
            'no_telp' => '081234567890',
            'password' => 'SlemanJuara2026',
        ]);

        $response->assertRedirect(route('peserta.profil.show'));

        $this->assertDatabaseHas('users', [
            'nama' => 'Peserta Sleman 2026',
            'email' => 'peserta.sleman@test.id',
            'no_telp' => '081234567890',
        ]);
    }
}

