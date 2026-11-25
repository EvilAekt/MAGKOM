<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminSettingsStrippedTest extends TestCase
{
    use RefreshDatabase;

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
    }

    public function test_superadmin_sidebar_does_not_render_pengaturan_menu(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));
        $response->assertOk();
        $response->assertDontSee('Pengaturan Sistem');
        $response->assertSee('Canvas Editor');
    }

    public function test_superadmin_footer_sidebar_points_to_akun_index(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));
        $response->assertOk();
        $response->assertSee(route('superadmin.akun.index'));
    }

    public function test_superadmin_settings_view_no_longer_displays_card_1_and_card_2(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.pengaturan.index'));
        $response->assertOk();

        // Kartu 1 tidak ada di SuperAdmin
        $response->assertDontSee('Kebijakan Kuota, Pendaftaran &amp; Kapasitas Magang', false);
        $response->assertDontSee('name="kuota_umum_maks_per_bulan"', false);

        // Kartu 2 tidak ada di SuperAdmin
        $response->assertDontSee('Aturan Pelaksanaan, Logbook &amp; Dokumen', false);
        $response->assertDontSee('name="logbook_maks_mundur_hari"', false);
    }
}
