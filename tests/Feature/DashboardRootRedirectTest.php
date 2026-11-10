<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DashboardRootRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_guest_accessing_dashboard_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect(route('login'));
    }

    public function test_superadmin_accessing_dashboard_is_redirected_to_superadmin_dashboard(): void
    {
        $superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($superAdmin)->get('/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect(route('superadmin.dashboard'));
    }

    public function test_admin_accessing_dashboard_is_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_pembimbing_accessing_dashboard_is_redirected_to_pembimbing_dashboard(): void
    {
        $pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($pembimbing)->get('/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect(route('pembimbing.dashboard'));
    }

    public function test_peserta_accessing_dashboard_is_redirected_to_peserta_dashboard(): void
    {
        $peserta = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $response = $this->actingAs($peserta)->get('/dashboard');

        $response->assertStatus(302);
        $response->assertRedirect(route('peserta.dashboard'));
    }
}
