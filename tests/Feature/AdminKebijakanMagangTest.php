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

    public function test_admin_can_view_kebijakan_page_with_six_quota_and_sla_parameters(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.kebijakan.index'));

        $response->assertOk();
        $response->assertSee('Kebijakan Kuota, Pendaftaran &amp; Kapasitas Magang', false);
        $response->assertSee('name="kuota_umum_maks_per_bulan"', false);
        $response->assertSee('name="slot_khusus_default"', false);
        $response->assertSee('name="maks_peserta_aktif_per_pembimbing"', false);
        $response->assertSee('name="maks_anggota_kelompok"', false);
        $response->assertSee('name="durasi_magang_maks_bulan"', false);
        $response->assertSee('name="sla_verifikasi_hari"', false);
        $response->assertSee('Target Batas Waktu SLA Verifikasi Berkas');
    }

    public function test_admin_can_update_kebijakan_parameters(): void
    {
        $payload = [
            'kuota_umum_maks_per_bulan' => 25,
            'slot_khusus_default' => 5,
            'maks_peserta_aktif_per_pembimbing' => 15,
            'maks_anggota_kelompok' => 5,
            'durasi_magang_maks_bulan' => 6,
            'sla_verifikasi_hari' => 7,
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.kebijakan.update'), $payload);

        $response->assertRedirect(route('admin.kebijakan.index'));
        $response->assertSessionHas('success');

        $this->assertSame('25', Pengaturan::get('kuota_umum_maks_per_bulan'));
        $this->assertSame('5', Pengaturan::get('slot_khusus_default'));
        $this->assertSame('15', Pengaturan::get('maks_peserta_aktif_per_pembimbing'));
        $this->assertSame('5', Pengaturan::get('maks_anggota_kelompok'));
        $this->assertSame('6', Pengaturan::get('durasi_magang_maks_bulan'));
        $this->assertSame('7', Pengaturan::get('sla_verifikasi_hari'));
    }

    public function test_admin_update_fails_with_invalid_values(): void
    {
        $payload = [
            'kuota_umum_maks_per_bulan' => 0, // min 1
            'slot_khusus_default' => -1, // min 0
            'maks_peserta_aktif_per_pembimbing' => 100, // max 50
            'maks_anggota_kelompok' => 25, // max 10
            'durasi_magang_maks_bulan' => 0, // min 1
            'sla_verifikasi_hari' => 0, // min 1
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.kebijakan.update'), $payload);

        $response->assertSessionHasErrors([
            'kuota_umum_maks_per_bulan',
            'slot_khusus_default',
            'maks_peserta_aktif_per_pembimbing',
            'maks_anggota_kelompok',
            'durasi_magang_maks_bulan',
            'sla_verifikasi_hari',
        ]);
    }

    public function test_superadmin_cannot_update_admin_kebijakan(): void
    {
        $payload = [
            'kuota_umum_maks_per_bulan' => 30,
            'slot_khusus_default' => 6,
            'maks_peserta_aktif_per_pembimbing' => 20,
            'maks_anggota_kelompok' => 4,
            'durasi_magang_maks_bulan' => 6,
        ];

        $response = $this->actingAs($this->superAdmin)->put(route('admin.kebijakan.update'), $payload);

        $response->assertStatus(403);
    }

    public function test_peserta_cannot_access_or_update_kebijakan(): void
    {
        $getResponse = $this->actingAs($this->peserta)->get(route('admin.kebijakan.index'));
        $getResponse->assertStatus(403);

        $putResponse = $this->actingAs($this->peserta)->put(route('admin.kebijakan.update'), [
            'kuota_umum_maks_per_bulan' => 10,
        ]);
        $putResponse->assertStatus(403);
    }
}
