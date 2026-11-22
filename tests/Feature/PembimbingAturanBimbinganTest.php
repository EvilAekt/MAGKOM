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

class PembimbingAturanBimbinganTest extends TestCase
{
    use RefreshDatabase;

    protected User $pembimbing;

    protected User $superAdmin;

    protected User $peserta;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);

        $this->pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'password' => Hash::make('PembimbingPass123!@#'),
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

    public function test_pembimbing_can_view_aturan_page_without_logbook_mundur_and_sla_fields(): void
    {
        $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.aturan.index'));

        $response->assertOk();
        $response->assertSee('Aturan Pelaksanaan, Logbook &amp; Dokumen', false);
        $response->assertSee('name="berkas_maks_mb"', false);
        $response->assertSee('name="deadline_mendekati_hari"', false);

        // Verifikasi bahwa batas mundur pengisian logbook dan SLA verifikasi berkas TELAH DIHAPUS dari antarmuka pembimbing
        $response->assertDontSee('logbook_maks_mundur_hari');
        $response->assertDontSee('Batas Mundur Pengisian Logbook');
        $response->assertDontSee('name="sla_verifikasi_hari"', false);
        $response->assertDontSee('Target Batas Waktu SLA Verifikasi Berkas');
    }

    public function test_pembimbing_can_update_aturan_parameters(): void
    {
        $payload = [
            'berkas_maks_mb' => 15,
            'deadline_mendekati_hari' => 5,
        ];

        $response = $this->actingAs($this->pembimbing)->put(route('pembimbing.aturan.update'), $payload);

        $response->assertRedirect(route('pembimbing.aturan.index'));
        $response->assertSessionHas('success');

        $this->assertSame('15', Pengaturan::get('berkas_maks_mb'));
        $this->assertSame('5', Pengaturan::get('deadline_mendekati_hari'));
    }

    public function test_pembimbing_update_fails_with_invalid_values(): void
    {
        $payload = [
            'berkas_maks_mb' => 0, // min 1
            'deadline_mendekati_hari' => 20, // max 14
        ];

        $response = $this->actingAs($this->pembimbing)->put(route('pembimbing.aturan.update'), $payload);

        $response->assertSessionHasErrors([
            'berkas_maks_mb',
            'deadline_mendekati_hari',
        ]);
    }

    public function test_superadmin_cannot_update_pembimbing_aturan(): void
    {
        $payload = [
            'berkas_maks_mb' => 12,
            'deadline_mendekati_hari' => 3,
        ];

        $response = $this->actingAs($this->superAdmin)->put(route('pembimbing.aturan.update'), $payload);

        $response->assertStatus(403);
    }

    public function test_peserta_cannot_access_or_update_aturan(): void
    {
        $getResponse = $this->actingAs($this->peserta)->get(route('pembimbing.aturan.index'));
        $getResponse->assertStatus(403);

        $putResponse = $this->actingAs($this->peserta)->put(route('pembimbing.aturan.update'), [
            'berkas_maks_mb' => 10,
        ]);
        $putResponse->assertStatus(403);
    }
}
