<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EnsureTwoFactorEnforcedTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'auth.2fa_enforce' => true,
            'auth.2fa_enforce_pembimbing' => true,
        ]);
        $this->seed(PermissionSeeder::class);
    }

    public function test_superadmin_within_grace_period_can_access_dashboard_with_warning(): void
    {
        $superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'created_at' => Carbon::now()->subDays(2),
            'two_factor_confirmed_at' => null,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('superadmin.dashboard'));
        $response->assertOk();
        $response->assertSessionHas('warning_2fa_grace');
    }

    public function test_superadmin_past_grace_period_without_2fa_is_redirected_to_settings(): void
    {
        $superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'created_at' => Carbon::now()->subDays(10),
            'two_factor_confirmed_at' => null,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('superadmin.dashboard'));
        $response->assertRedirect(route('superadmin.2fa.settings'));
        $response->assertSessionHas('error');
    }

    public function test_superadmin_with_active_2fa_can_access_dashboard(): void
    {
        $superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'created_at' => Carbon::now()->subDays(10),
            'two_factor_secret' => encrypt('JBSWY3DPEHPK3PXP'),
            'two_factor_confirmed_at' => Carbon::now()->subDays(9),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('superadmin.dashboard'));
        $response->assertOk();
        $response->assertSessionMissing('warning_2fa_grace');
    }

    public function test_admin_past_grace_period_without_2fa_is_redirected_to_2fa_settings(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'created_at' => Carbon::now()->subDays(10),
            'two_factor_confirmed_at' => null,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertRedirect(route('2fa.settings'));
        $response->assertSessionHas('error');
    }

    public function test_pembimbing_past_grace_period_without_2fa_is_redirected_to_2fa_settings(): void
    {
        $pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'created_at' => Carbon::now()->subDays(10),
            'two_factor_confirmed_at' => null,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($pembimbing)->get(route('pembimbing.dashboard'));
        $response->assertRedirect(route('2fa.settings'));
        $response->assertSessionHas('error');
    }

    public function test_peserta_is_not_subject_to_2fa_enforcement(): void
    {
        $peserta = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'created_at' => Carbon::now()->subDays(30),
            'two_factor_confirmed_at' => null,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($peserta)->get(route('peserta.dashboard'));
        $response->assertOk();
        $response->assertSessionMissing('warning_2fa_grace');
    }

    public function test_allowed_routes_can_be_accessed_by_staff_even_after_grace_period(): void
    {
        $superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'created_at' => Carbon::now()->subDays(10),
            'two_factor_confirmed_at' => null,
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        // Route superadmin.2fa.settings must be accessible
        $response = $this->actingAs($superAdmin)->get(route('superadmin.2fa.settings'));
        $response->assertOk();

        // Logout route must be accessible
        $logoutResponse = $this->actingAs($superAdmin)->post(route('logout'));
        $logoutResponse->assertRedirect('/');
    }
}
