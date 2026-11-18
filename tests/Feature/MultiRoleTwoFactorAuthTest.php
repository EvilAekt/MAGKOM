<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\TwoFactorAuthService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
