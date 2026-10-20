<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PredikatBatasValidationTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = new User;
        $this->superAdmin->nama = 'SuperAdmin Test';
        $this->superAdmin->email = 'superadmin.predikat@test.com';
        $this->superAdmin->password = 'secret123';
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->email_verified_at = now();
        $this->superAdmin->status_aktif = true;
        $this->superAdmin->save();
    }

    public function test_rejects_non_descending_predikat_thresholds(): void
    {
        $response = $this->actingAs($this->superAdmin)->put(route('superadmin.pengaturan.update'), [
            'masa_tenggang_bulan' => 1,
            'kuota_umum_maks_per_bulan' => 20,
            'logbook_maks_mundur_hari' => 3,
            'berkas_maks_mb' => 2,
            'deadline_mendekati_hari' => 3,
            'batas_predikat_a' => 70, // invalid: A less than B
            'batas_predikat_b' => 85,
            'batas_predikat_c' => 55,
            'batas_predikat_d' => 40,
        ]);

        $response->assertSessionHasErrors(['batas_predikat_a']);
    }

    public function test_rejects_incomplete_predikat_thresholds(): void
    {
        $response = $this->actingAs($this->superAdmin)->put(route('superadmin.pengaturan.update'), [
            'masa_tenggang_bulan' => 1,
            'kuota_umum_maks_per_bulan' => 20,
            'logbook_maks_mundur_hari' => 3,
            'berkas_maks_mb' => 2,
            'deadline_mendekati_hari' => 3,
            'batas_predikat_a' => 85,
            'batas_predikat_b' => 70,
            // C and D missing
        ]);

        $response->assertSessionHasErrors(['batas_predikat_a']);
    }

    public function test_accepts_strictly_descending_predikat_thresholds(): void
    {
        $response = $this->actingAs($this->superAdmin)->put(route('superadmin.pengaturan.update'), [
            'masa_tenggang_bulan' => 1,
            'kuota_umum_maks_per_bulan' => 20,
            'logbook_maks_mundur_hari' => 3,
            'berkas_maks_mb' => 2,
            'deadline_mendekati_hari' => 3,
            'batas_predikat_a' => 88,
            'batas_predikat_b' => 75,
            'batas_predikat_c' => 60,
            'batas_predikat_d' => 45,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('pengaturan', [
            'kunci' => 'predikat_batas',
            'nilai' => json_encode(['A' => 88, 'B' => 75, 'C' => 60, 'D' => 45]),
        ]);
    }

    public function test_rejects_invalid_json_predikat_batas(): void
    {
        $response = $this->actingAs($this->superAdmin)->put(route('superadmin.pengaturan.update'), [
            'masa_tenggang_bulan' => 1,
            'kuota_umum_maks_per_bulan' => 20,
            'logbook_maks_mundur_hari' => 3,
            'berkas_maks_mb' => 2,
            'deadline_mendekati_hari' => 3,
            'predikat_batas' => '{"A": 50, "B": 70, "C": 30, "D": 20}', // non-descending
        ]);

        $response->assertSessionHasErrors(['predikat_batas']);
    }
}
