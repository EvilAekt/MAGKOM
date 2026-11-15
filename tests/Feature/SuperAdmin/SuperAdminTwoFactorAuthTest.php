<?php

declare(strict_types=1);

namespace Tests\Feature\SuperAdmin;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\TwoFactorAuthService;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminTwoFactorAuthTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected TwoFactorAuthService $twoFactorService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
        $this->twoFactorService = app(TwoFactorAuthService::class);

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'password' => Hash::make('SuperAdminPassword123!'),
            'email_verified_at' => now(),
            'must_change_password' => false,
            'status_aktif' => true,
        ]);
    }

    public function test_superadmin_can_access_2fa_settings_and_initiate_enrollment(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.2fa.settings'));

        $response->assertOk();
        $response->assertSee('Autentikasi Dua Faktor (TOTP)');

        $enableResponse = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.2fa.enable'));

        $enableResponse->assertOk();
        $enableResponse->assertSee('Langkah 1: Pindai QR Code atau Input Kunci Rahasia');
        $this->assertTrue(session()->has('auth.2fa.enroll_secret'));
    }

    public function test_superadmin_can_confirm_2fa_and_receive_recovery_codes(): void
    {
        $secret = $this->twoFactorService->generateSecretKey();
        session(['auth.2fa.enroll_secret' => $secret]);

        $google2fa = new \PragmaRX\Google2FA\Google2FA;
        $validCode = $google2fa->getCurrentOtp($secret);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.2fa.confirm'), [
                'code' => $validCode,
            ]);

        $response->assertOk();
        $response->assertSee('PENTING: Simpan Kode Pemulihan Ini Sekarang!');

        $this->superAdmin->refresh();
        $this->assertTrue($this->superAdmin->hasEnabledTwoFactor());
        $this->assertNotNull($this->superAdmin->two_factor_confirmed_at);
        $this->assertNotEmpty($this->superAdmin->two_factor_recovery_codes);
    }

    public function test_login_intercepts_user_with_2fa_and_requires_challenge(): void
    {
        // Aktifkan 2FA pada akun
        $secret = $this->twoFactorService->generateSecretKey();
        $this->superAdmin->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();

        // Coba login dengan kredensial yang valid
        $response = $this->post('/login', [
            'email' => $this->superAdmin->email,
            'password' => 'SuperAdminPassword123!',
        ]);

        // Harus diarahkan ke 2FA challenge, belum login ke session Auth
        $response->assertRedirect(route('two-factor.challenge'));
        $this->assertFalse(Auth::check());
        $this->assertEquals($this->superAdmin->id, session('auth.2fa.user_id'));

        // Coba kode salah
        $failResponse = $this->post(route('two-factor.challenge.verify'), [
            'code' => '000000',
        ]);
        $failResponse->assertSessionHasErrors('code');
        $this->assertFalse(Auth::check());

        // Masukkan kode TOTP yang benar
        $google2fa = new \PragmaRX\Google2FA\Google2FA;
        $validCode = $google2fa->getCurrentOtp($secret);

        $successResponse = $this->post(route('two-factor.challenge.verify'), [
            'code' => $validCode,
        ]);

        $successResponse->assertRedirect(route('superadmin.dashboard'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($this->superAdmin->id, Auth::id());
    }

    public function test_recovery_code_allows_login_and_is_single_use(): void
    {
        $secret = $this->twoFactorService->generateSecretKey();
        $this->superAdmin->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $plainCodes = $this->superAdmin->generateRecoveryCodes();
        $firstCode = $plainCodes[0];

        session(['auth.2fa.user_id' => $this->superAdmin->id]);

        // Gunakan recovery code pertama
        $response = $this->post(route('two-factor.challenge.verify'), [
            'recovery_code' => $firstCode,
        ]);

        $response->assertRedirect(route('superadmin.dashboard'));
        $this->assertTrue(Auth::check());

        // Verifikasi bahwa kode yang sama tidak bisa digunakan untuk kedua kalinya (Single-Use)
        $this->superAdmin->refresh();
        $this->assertFalse($this->superAdmin->verifyRecoveryCode($firstCode));
    }

    public function test_emergency_cli_command_resets_2fa(): void
    {
        $secret = $this->twoFactorService->generateSecretKey();
        $this->superAdmin->forceFill([
            'two_factor_secret' => $secret,
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->assertTrue($this->superAdmin->hasEnabledTwoFactor());

        $this->artisan('auth:2fa-reset', [
            'email' => $this->superAdmin->email,
            '--force' => true,
        ])->assertSuccessful();

        $this->superAdmin->refresh();
        $this->assertFalse($this->superAdmin->hasEnabledTwoFactor());
        $this->assertNull($this->superAdmin->two_factor_secret);
        $this->assertNull($this->superAdmin->two_factor_confirmed_at);
    }
}
