<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminKebijakanMagangTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $superAdmin;

    protected User $peserta;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);

        $this->admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'password' => Hash::make('AdminPass123!@#'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'password' => Hash::make('SuperPass123!@#'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $this->peserta = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'password' => Hash::make('PesertaPass123!@#'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);
    }

    /**
     * Memastikan rute GET /admin/kebijakan sudah tidak ada dan mengembalikan status 404.
     */
    public function test_kebijakan_page_is_no_longer_accessible(): void
    {
        $responseAdmin = $this->actingAs($this->admin)->get('/admin/kebijakan');
        $responseAdmin->assertNotFound();

        $responseSuperAdmin = $this->actingAs($this->superAdmin)->get('/admin/kebijakan');
        $responseSuperAdmin->assertNotFound();

        $responsePeserta = $this->actingAs($this->peserta)->get('/admin/kebijakan');
        $responsePeserta->assertNotFound();
    }

    /**
     * Memastikan rute PUT /admin/kebijakan sudah tidak ada dan mengembalikan status 404.
     */
    public function test_kebijakan_update_route_is_no_longer_accessible(): void
    {
        $response = $this->actingAs($this->admin)->put('/admin/kebijakan', [
            'kuota_umum_maks_per_bulan' => 25,
        ]);
        $response->assertNotFound();
    }

    /**
     * Memastikan sidebar dashboard administrator tidak lagi menampilkan menu Kebijakan Kuota.
     */
    public function test_dashboard_sidebar_does_not_contain_kebijakan_link(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertDontSee('Kebijakan Kuota');
        $response->assertDontSee('/admin/kebijakan');
    }
}
