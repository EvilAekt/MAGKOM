<?php

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminAdministrasiMenuAlignmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'password' => Hash::make('SuperSecret123!@#'),
            'status_aktif' => true,
            'email_verified_at' => now(),
            'must_change_password' => false,
        ]);
    }

    public function test_superadmin_sidebar_renders_aligned_nine_administrasi_items(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertOk();

        // 6 Modul Operasional Magang
        $response->assertSee('Verifikasi Berkas');
        $response->assertSee('Manajemen Kuota');
        $response->assertSee('Penempatan Peserta');
        $response->assertSee('Surat Selesai Magang');
        $response->assertSee('Laporan & Ekspor');
        $response->assertSee('Arsip Peserta');

        // Divider
        $response->assertSee('MASTER & KONFIGURASI');

        // 3 Modul Master
        $response->assertSee('Jalur Khusus');
        $response->assertSee('Data Jurusan');
        $response->assertSee('Unit Kerja');

        // Memastikan Periode Magang sudah tidak muncul di sidebar
        $response->assertDontSee('Periode Magang');

        // Memastikan URL Surat Selesai Magang terpasang di sidebar Superadmin
        $response->assertSee(route('admin.surat-selesai.index'));
        $response->assertSee(route('superadmin.arsip.index'));
    }

    public function test_superadmin_can_access_surat_selesai_and_nav_group_remains_active(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.surat-selesai.index'));

        $response->assertOk();
        $response->assertSee('nav-group-btn has-active', false);
        $response->assertSee('aria-expanded="true"', false);
        $response->assertSee('nav-sub-menu show', false);
        $response->assertSee('Surat Selesai Magang');
    }

    public function test_superadmin_can_access_arsip_peserta_and_nav_group_remains_active(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.arsip.index'));

        $response->assertOk();
        $response->assertSee('nav-group-btn has-active', false);
        $response->assertSee('aria-expanded="true"', false);
        $response->assertSee('nav-sub-menu show', false);
        $response->assertSee('Arsip Peserta');
    }
}
