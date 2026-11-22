<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Pengaturan;
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

    public function test_superadmin_settings_no_longer_displays_card_1_and_card_2(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.pengaturan.index'));

        $response->assertOk();

        // Kartu 1 tidak ada di SuperAdmin
        $response->assertDontSee('Kebijakan Kuota, Pendaftaran &amp; Kapasitas Magang', false);
        $response->assertDontSee('name="kuota_umum_maks_per_bulan"', false);
        $response->assertDontSee('name="slot_khusus_default"', false);
        $response->assertDontSee('name="maks_peserta_aktif_per_pembimbing"', false);
        $response->assertDontSee('name="maks_anggota_kelompok"', false);
        $response->assertDontSee('name="durasi_magang_maks_bulan"', false);

        // Kartu 2 tidak ada di SuperAdmin
        $response->assertDontSee('Aturan Pelaksanaan, Logbook &amp; Dokumen', false);
        $response->assertDontSee('name="logbook_maks_mundur_hari"', false);
        $response->assertDontSee('name="berkas_maks_mb"', false);
        $response->assertDontSee('name="deadline_mendekati_hari"', false);
        $response->assertDontSee('name="sla_verifikasi_hari"', false);

        // Hanya Kartu 3 (Retensi Akun & Audit Trail) dan Kartu 4 (Konten Publik) yang tampil
        $response->assertSee('Kebijakan Retensi Akun &amp; Audit Trail', false);
        $response->assertSee('Konten Publik Landing Page', false);
    }

    public function test_superadmin_update_only_processes_retention_and_landing_parameters(): void
    {
        $payload = [
            'masa_tenggang_bulan' => 3,
            'retensi_audit_akses_data_hari' => 180,
            // Coba kirim field yang sudah dipindah (harus diabaikan / tidak divalidasi oleh SuperAdmin)
            'kuota_umum_maks_per_bulan' => 99,
            'logbook_maks_mundur_hari' => 10,
        ];

        $response = $this->actingAs($this->superAdmin)->put(route('superadmin.pengaturan.update'), $payload);

        $response->assertRedirect();

        $this->assertSame('3', Pengaturan::get('masa_tenggang_bulan'));
        $this->assertSame('180', Pengaturan::get('retensi_audit_akses_data_hari'));

        // Nilai kuota_umum_maks_per_bulan dan logbook_maks_mundur_hari TIDAK tersimpan menjadi 99 / 10 via SuperAdmin
        $this->assertNotSame('99', Pengaturan::get('kuota_umum_maks_per_bulan'));
        $this->assertNotSame('10', Pengaturan::get('logbook_maks_mundur_hari'));
    }
}
