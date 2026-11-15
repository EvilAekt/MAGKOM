<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Contracts\Provider as SocialiteProviderContract;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class AuthSecurityReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Memastikan OAuth callback yang mengembalikan email kosong ditolak secara aman.
     */
    public function test_oauth_callback_with_empty_email_fails_securely(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUserContract::class);
        $socialiteUser->shouldReceive('getId')->andReturn('google_empty_email_id');
        $socialiteUser->shouldReceive('getName')->andReturn('Tanpa Email');
        $socialiteUser->shouldReceive('getEmail')->andReturn('');
        $socialiteUser->shouldReceive('getNickname')->andReturn(null);

        $provider = Mockery::mock(SocialiteProviderContract::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('oauth.callback', 'google'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Memastikan percobaan pengambilalihan akun staf (Admin/Superadmin) via Google OAuth dicegah mutlak.
     */
    public function test_cannot_account_takeover_staff_with_same_email_as_oauth(): void
    {
        $superadmin = User::factory()->create([
            'nama' => 'Superadmin Kedinasan',
            'email' => 'superadmin@magkom.go.id',
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'provider' => null,
            'provider_id' => null,
        ]);

        $socialiteUser = Mockery::mock(SocialiteUserContract::class);
        $socialiteUser->shouldReceive('getId')->andReturn('attacker_google_id_999');
        $socialiteUser->shouldReceive('getName')->andReturn('Attacker Impersonator');
        $socialiteUser->shouldReceive('getEmail')->andReturn('superadmin@magkom.go.id');
        $socialiteUser->shouldReceive('getNickname')->andReturn(null);

        $provider = Mockery::mock(SocialiteProviderContract::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('oauth.callback', 'google'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        // Pastikan akun staf tidak tertaut ke provider penyerang
        $superadmin->refresh();
        $this->assertNull($superadmin->provider);
        $this->assertNull($superadmin->provider_id);

        $this->assertDatabaseHas('audit_log', [
            'entitas' => 'users',
            'tipe' => 'aksi',
        ]);
    }

    /**
     * Memastikan formulir reset password mandiri menolak akun peran staf (SuperAdmin/Admin/Pembimbing).
     */
    public function test_forgot_password_does_not_issue_link_for_staff_accounts(): void
    {
        $pembimbing = User::factory()->create([
            'nama' => 'Pembimbing Lapangan',
            'email' => 'pembimbing@diskominfo.go.id',
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => true,
            'email_verified_at' => now(),
        ]);

        Notification::fake();

        $response = $this->post(route('password.email'), [
            'email' => 'pembimbing@diskominfo.go.id',
        ]);

        // Tetap memberikan respon generik seragam anti-user-enumeration
        $response->assertSessionHas('status', 'Jika alamat email terdaftar pada sistem, kami akan mengirimkan instruksi untuk mengatur ulang password.');

        // Tetapi notification reset password TIDAK boleh terkirim untuk akun staf
        Notification::assertNothingSent();
    }

    /**
     * Memastikan pembuatan kata sandi baru menolak password lemah.
     */
    public function test_password_reset_rejects_weak_passwords(): void
    {
        $peserta = User::factory()->create([
            'nama' => 'Peserta Magang',
            'email' => 'peserta@gmail.com',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'email_verified_at' => now(),
        ]);

        $token = Password::broker()->createToken($peserta);

        // Password hanya huruf (tanpa angka)
        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'peserta@gmail.com',
            'password' => 'hanyaHurufSaja',
            'password_confirmation' => 'hanyaHurufSaja',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertFalse(Hash::check('hanyaHurufSaja', $peserta->fresh()->password));
    }
}
