<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LiveLoginSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::forceCreate([
            'nama' => 'Super Administrator Kominfo',
            'email' => 'superadmin@magkom.go.id',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        User::forceCreate([
            'nama' => 'Admin Verifikator Magang Sleman',
            'email' => 'admin@magkom.go.id',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        User::forceCreate([
            'nama' => 'Budi Santoso, S.Kom',
            'email' => 'pembimbing@magkom.go.id',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        User::forceCreate([
            'nama' => 'Naufal Pratama',
            'email' => 'peserta@magkom.go.id',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        // Akun @gmail.com
        User::forceCreate([
            'nama' => 'Super Administrator (Gmail)',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        User::forceCreate([
            'nama' => 'Admin Verifikator (Gmail)',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        User::forceCreate([
            'nama' => 'Budi Pembimbing (Gmail)',
            'email' => 'pembimbing@gmail.com',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        User::forceCreate([
            'nama' => 'Ahmad Peserta (Gmail)',
            'email' => 'peserta@gmail.com',
            'password' => Hash::make('Password123!'),
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);
    }

    public function test_superadmin_can_login_with_visual_captcha(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'superadmin@magkom.go.id',
            'password' => 'Password123!',
            'captcha' => 'DE4F',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('superadmin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_admin_can_login_with_visual_captcha(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'admin@magkom.go.id',
            'password' => 'Password123!',
            'captcha' => 'DE4F',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_pembimbing_can_login_with_visual_captcha(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'pembimbing@magkom.go.id',
            'password' => 'Password123!',
            'captcha' => 'DE4F',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('pembimbing.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_peserta_can_login_with_visual_captcha(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'peserta@magkom.go.id',
            'password' => 'Password123!',
            'captcha' => 'DE4F',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('peserta.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_superadmin_gmail_can_login_with_visual_captcha(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'superadmin@gmail.com',
            'password' => 'Password123!',
            'captcha' => 'DE4F',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('superadmin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_admin_gmail_can_login_with_visual_captcha(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'admin@gmail.com',
            'password' => 'Password123!',
            'captcha' => 'DE4F',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_pembimbing_gmail_can_login_with_visual_captcha(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'pembimbing@gmail.com',
            'password' => 'Password123!',
            'captcha' => 'DE4F',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('pembimbing.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_peserta_gmail_can_login_with_visual_captcha(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'peserta@gmail.com',
            'password' => 'Password123!',
            'captcha' => 'DE4F',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('peserta.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'peserta@magkom.go.id',
            'password' => 'KataSandiSalah123!',
            'captcha' => 'DE4F',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_fails_with_invalid_captcha(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'peserta@magkom.go.id',
            'password' => 'Password123!',
            'captcha' => 'WRNG',
        ]);

        $response->assertSessionHasErrors(['captcha']);
        $this->assertGuest();
    }
}
