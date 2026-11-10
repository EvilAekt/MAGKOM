<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Support\Pengaturan as PengaturanHelper;
use Database\Seeders\JenisDokumenSeeder;
use Database\Seeders\PengaturanSeeder;
use Database\Seeders\RubrikSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminFeatureToggleTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(JenisDokumenSeeder::class);
        $this->seed(RubrikSeeder::class);
        $this->seed(PengaturanSeeder::class);

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);

        $this->admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'must_change_password' => false,
        ]);
    }

    public function test_pengaturan_seeder_sets_expected_defaults(): void
    {
        $this->assertSame(20, PengaturanHelper::int('maks_peserta_aktif_per_pembimbing'));
        $this->assertTrue(PengaturanHelper::bool('fitur_pendaftaran_kelompok_aktif'));
    }

    public function test_superadmin_can_update_mentee_limit_and_toggle_features_off(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.pengaturan.update'), [
                'masa_tenggang_bulan' => 2,
                'kuota_umum_maks_per_bulan' => 15,
                'slot_khusus_default' => 5,
                'maks_peserta_aktif_per_pembimbing' => 25,
                'maks_anggota_kelompok' => 4,
                'durasi_magang_maks_bulan' => 6,
                'logbook_maks_mundur_hari' => 5,
                'berkas_maks_mb' => 4,
                'deadline_mendekati_hari' => 4,
                'sla_verifikasi_hari' => 5,
                'retensi_audit_akses_data_hari' => 180,
                'fitur_pendaftaran_kelompok_aktif' => 0,
            ]);

        $response->assertRedirect(route('superadmin.pengaturan.index'));
        $response->assertSessionHas('success');

        $this->assertSame(25, PengaturanHelper::int('maks_peserta_aktif_per_pembimbing'));
        $this->assertFalse(PengaturanHelper::bool('fitur_pendaftaran_kelompok_aktif'));
    }

    public function test_decommissioned_analitik_tahunan_returns_404(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/laporan/tahunan')
            ->assertNotFound();
    }
}
