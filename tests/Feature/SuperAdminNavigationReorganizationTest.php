<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminNavigationReorganizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'password' => Hash::make('SuperAdminPass123!@#'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);
    }

    /**
     * Memastikan sidebar SuperAdmin merender 8 item lengkap di dalam grup dropdown Administrasi.
     */
    public function test_sidebar_superadmin_renders_master_data_under_administrasi_group(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertOk();

        // 4 Item Operasional Reguler
        $response->assertSee('Verifikasi Berkas');
        $response->assertSee('Kuota Magang');
        $response->assertSee('Penempatan Peserta');
        $response->assertSee('Laporan & Ekspor');

        // 4 Item Master yang dipindahkan ke dropdown Administrasi
        $response->assertSee('Periode Magang');
        $response->assertSee('Jalur Khusus');
        $response->assertSee('Data Jurusan');
        $response->assertSee('Unit Kerja');
    }

    /**
     * Memastikan saat SuperAdmin mengakses salah satu master data, grup Administrasi berstatus has-active dan expanded.
     */
    public function test_administrasi_group_is_active_when_superadmin_accesses_periode_magang(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.bulan.index'));
        $response->assertRedirect(route('admin.kuota.index', ['tab' => 'bulan']));

        $target = $this->actingAs($this->superAdmin)->get(route('admin.kuota.index', ['tab' => 'bulan']));
        $target->assertOk();
        $target->assertSee('nav-group-btn has-active', false);
        $target->assertSee('aria-expanded="true"', false);
    }

    /**
     * Memastikan sidebar Admin merender 3 item master & konfigurasi (setelah periode magang diintegrasikan ke kuota).
     */
    public function test_sidebar_admin_renders_master_data_items(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'password' => Hash::make('AdminPass123!@#'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertDontSee('Periode Magang');
        $response->assertSee('Jalur Khusus');
        $response->assertSee('Data Jurusan');
        $response->assertSee('Unit Kerja');
    }

    /**
     * Memastikan saat Admin mengakses rute Periode Magang (bulan), diarahkan ke tab bulan di kuota dengan navigasi aktif.
     */
    public function test_admin_nav_link_is_active_when_accessing_periode_magang(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'password' => Hash::make('AdminPass123!@#'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.bulan.index'));
        $response->assertRedirect(route('admin.kuota.index', ['tab' => 'bulan']));

        $target = $this->actingAs($admin)->get(route('admin.kuota.index', ['tab' => 'bulan']));
        $target->assertOk();
        $target->assertSee('nav-item-link active', false);
    }

    /**
     * Memastikan saat Admin mengakses rute Jalur Khusus (slot-khusus), tautan navigasi berstatus active.
     */
    public function test_admin_nav_link_is_active_when_accessing_jalur_khusus(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'password' => Hash::make('AdminPass123!@#'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.slot-khusus.index'));

        $response->assertOk();
        $response->assertSee('nav-item-link active', false);
    }

    /**
     * Memastikan saat Admin mengakses rute Data Jurusan, tautan navigasi berstatus active.
     */
    public function test_admin_nav_link_is_active_when_accessing_data_jurusan(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'password' => Hash::make('AdminPass123!@#'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.jurusan.index'));

        $response->assertOk();
        $response->assertSee('nav-item-link active', false);
    }

    /**
     * Memastikan saat Admin mengakses rute Unit Kerja, tautan navigasi berstatus active.
     */
    public function test_admin_nav_link_is_active_when_accessing_unit_kerja(): void
    {
        $admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'password' => Hash::make('AdminPass123!@#'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.bidang.index'));

        $response->assertOk();
        $response->assertSee('nav-item-link active', false);
    }

    /**
     * Memastikan menu Pengaturan dihapus total dari sidebar SuperAdmin dan Canvas Editor menjadi nav item mandiri.
     */
    public function test_superadmin_sidebar_no_longer_renders_pengaturan_menu_and_shows_canvas_editor(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertOk();
        $response->assertDontSee('Pengaturan Sistem');
        $response->assertSee('Canvas Editor');
        $response->assertSee(route('superadmin.site-editor.index'));
    }
}
