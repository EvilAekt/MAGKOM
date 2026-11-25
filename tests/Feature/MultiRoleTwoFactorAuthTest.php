<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\TwoFactorAuthService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MultiRoleTwoFactorAuthTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $pembimbing;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'password' => Hash::make('SuperPass123!@#'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $this->admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'password' => Hash::make('AdminPass123!@#'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $this->pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'password' => Hash::make('PembimbingPass123!@#'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);
    }

    /**
     * Memastikan Admin dapat mengakses dashboard pengaturan 2FA dan memulai enrollment.
     */
    public function test_admin_can_access_2fa_settings_and_initiate_enrollment(): void
    {
        $response = $this->actingAs($this->admin)->get(route('2fa.settings'));
        $response->assertOk();
        $response->assertViewIs('superadmin.security.two-factor');

        $enrollResponse = $this->actingAs($this->admin)->post(route('2fa.enable'));
        $enrollResponse->assertOk();
        $enrollResponse->assertViewIs('superadmin.security.two-factor-enroll');
        $enrollResponse->assertSessionHas('auth.2fa.enroll_secret');
    }

    /**
     * Memastikan SuperAdmin dapat mengakses dashboard pengaturan 2FA (/superadmin/keamanan/2fa).
     */
    public function test_superadmin_can_access_2fa_settings_and_initiate_enrollment(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.2fa.settings'));
        $response->assertOk();
        $response->assertViewIs('superadmin.security.two-factor');

        $enrollResponse = $this->actingAs($this->superAdmin)->post(route('superadmin.2fa.enable'));
        $enrollResponse->assertOk();
        $enrollResponse->assertViewIs('superadmin.security.two-factor-enroll');
        $enrollResponse->assertSessionHas('auth.2fa.enroll_secret');
    }

    /**
     * Memastikan Pembimbing dapat mengakses dashboard pengaturan 2FA dan memulai enrollment.
     */
    public function test_pembimbing_can_access_2fa_settings_and_initiate_enrollment(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('2fa.settings'));
        $response->assertOk();

        $enrollResponse = $this->actingAs($this->pembimbing)->post(route('2fa.enable'));
        $enrollResponse->assertOk();
        $enrollResponse->assertViewIs('superadmin.security.two-factor-enroll');
    }

    /**
     * Memastikan Admin dapat mengonfirmasi TOTP valid dan mengaktifkan 2FA.
     */
    public function test_admin_can_confirm_2fa_and_receive_recovery_codes(): void
    {
        $twoFactorService = app(TwoFactorAuthService::class);
        $secret = $twoFactorService->generateSecretKey();
        $validOtp = $twoFactorService->getCurrentOtp($secret);

        $response = $this->actingAs($this->admin)
            ->withSession(['auth.2fa.enroll_secret' => $secret])
            ->post(route('2fa.confirm'), [
                'code' => $validOtp,
            ]);

        $response->assertOk();
        $response->assertViewIs('superadmin.security.two-factor-recovery-codes');

        $this->admin->refresh();
        $this->assertTrue($this->admin->hasEnabledTwoFactor());
        $this->assertNotEmpty($this->admin->two_factor_recovery_codes);
    }

    /**
     * Memastikan login akun Admin dengan 2FA aktif diarahkan ke tantangan 2FA.
     */
    public function test_admin_with_2fa_is_intercepted_at_login(): void
    {
        $twoFactorService = app(TwoFactorAuthService::class);
        $secret = $twoFactorService->generateSecretKey();

        $this->admin->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $response = $this->post(route('login'), [
            'email' => $this->admin->email,
            'password' => 'AdminPass123!@#',
        ]);

        $response->assertRedirect(route('two-factor.challenge'));
        $this->assertGuest();
        $this->assertSame($this->admin->id, session('auth.2fa.user_id'));
    }

    /**
     * Memastikan Peserta dapat mengakses halaman pengaturan dan melihat kartu 2FA opsional.
     */
    public function test_peserta_can_access_pengaturan_and_initiate_2fa_enrollment(): void
    {
        $peserta = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'password' => Hash::make('PesertaPass123!@#'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($peserta)->get(route('peserta.pengaturan.index'));
        $response->assertOk();
        $response->assertSee('Autentikasi Dua Faktor (2FA / TOTP)');
        $response->assertSee('Belum Aktif');

        $enrollResponse = $this->actingAs($peserta)->post(route('2fa.enable'));
        $enrollResponse->assertOk();
        $enrollResponse->assertViewIs('superadmin.security.two-factor-enroll');
        $enrollResponse->assertSessionHas('auth.2fa.enroll_secret');
    }

    /**
     * Memastikan Peserta dapat mengonfirmasi 2FA dan login dengan tantangan TOTP.
     */
    public function test_peserta_can_confirm_2fa_and_is_intercepted_at_login(): void
    {
        $peserta = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'password' => Hash::make('PesertaPass123!@#'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $twoFactorService = app(TwoFactorAuthService::class);
        $secret = $twoFactorService->generateSecretKey();
        $validOtp = $twoFactorService->getCurrentOtp($secret);

        $response = $this->actingAs($peserta)
            ->withSession(['auth.2fa.enroll_secret' => $secret])
            ->post(route('2fa.confirm'), [
                'code' => $validOtp,
            ]);

        $response->assertOk();
        $response->assertViewIs('superadmin.security.two-factor-recovery-codes');

        $peserta->refresh();
        $this->assertTrue($peserta->hasEnabledTwoFactor());

        // Verifikasi intersepsi login
        Auth::logout();
        $loginResponse = $this->post(route('login'), [
            'email' => $peserta->email,
            'password' => 'PesertaPass123!@#',
        ]);
        $loginResponse->assertRedirect(route('two-factor.challenge'));
        $this->assertSame($peserta->id, session('auth.2fa.user_id'));
    }

    /**
     * Memastikan Admin dapat menonaktifkan 2FA dengan konfirmasi kata sandi yang valid.
     */
    public function test_admin_can_disable_2fa_with_valid_password(): void
    {
        $twoFactorService = app(TwoFactorAuthService::class);
        $secret = $twoFactorService->generateSecretKey();

        $this->admin->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $response = $this->actingAs($this->admin)->post(route('2fa.disable'), [
            'current_password' => 'AdminPass123!@#',
        ]);

        $response->assertRedirect(route('admin.pengaturan.index'));
        $response->assertSessionHas('success');

        $this->admin->refresh();
        $this->assertFalse($this->admin->hasEnabledTwoFactor());
    }

    /**
     * Memastikan penonaktifan 2FA gagal jika kata sandi salah.
     */
    public function test_disable_2fa_fails_with_invalid_password(): void
    {
        $twoFactorService = app(TwoFactorAuthService::class);
        $secret = $twoFactorService->generateSecretKey();

        $this->admin->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $response = $this->actingAs($this->admin)->post(route('2fa.disable'), [
            'current_password' => 'SandiSalah123!@#',
        ]);

        $response->assertSessionHasErrors('current_password');

        $this->admin->refresh();
        $this->assertTrue($this->admin->hasEnabledTwoFactor());
    }

    /**
     * Memastikan SuperAdmin dapat menonaktifkan 2FA dengan konfirmasi kata sandi yang valid.
     */
    public function test_superadmin_can_disable_2fa_with_valid_password(): void
    {
        $twoFactorService = app(TwoFactorAuthService::class);
        $secret = $twoFactorService->generateSecretKey();

        $this->superAdmin->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $response = $this->actingAs($this->superAdmin)->post(route('2fa.disable'), [
            'current_password' => 'SuperPass123!@#',
        ]);

        $response->assertRedirect(route('superadmin.akun.index'));
        $response->assertSessionHas('success');

        $this->superAdmin->refresh();
        $this->assertFalse($this->superAdmin->hasEnabledTwoFactor());
    }

    /**
     * Memastikan penonaktifan 2FA SuperAdmin gagal jika kata sandi salah.
     */
    public function test_superadmin_disable_2fa_fails_with_invalid_password(): void
    {
        $twoFactorService = app(TwoFactorAuthService::class);
        $secret = $twoFactorService->generateSecretKey();

        $this->superAdmin->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $response = $this->actingAs($this->superAdmin)->post(route('2fa.disable'), [
            'current_password' => 'SandiSalah123!@#',
        ]);

        $response->assertSessionHasErrors('current_password');

        $this->superAdmin->refresh();
        $this->assertTrue($this->superAdmin->hasEnabledTwoFactor());
    }

    /**
     * Memastikan regenerasi recovery codes membutuhkan kata sandi saat ini yang valid.
     */
    public function test_regenerate_recovery_codes_requires_valid_password(): void
    {
        $twoFactorService = app(TwoFactorAuthService::class);
        $secret = $twoFactorService->generateSecretKey();

        $this->admin->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();

        // Coba dengan password salah
        $failResponse = $this->actingAs($this->admin)->post(route('2fa.regenerate-recovery-codes'), [
            'current_password' => 'SandiSalah',
        ]);
        $failResponse->assertSessionHasErrors('current_password');

        // Coba dengan password benar
        $successResponse = $this->actingAs($this->admin)->post(route('2fa.regenerate-recovery-codes'), [
            'current_password' => 'AdminPass123!@#',
        ]);
        $successResponse->assertOk();
        $successResponse->assertViewIs('superadmin.security.two-factor-recovery-codes');
    }
}
