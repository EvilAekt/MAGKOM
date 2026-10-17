<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PesertaUuPdpPersetujuanWaliTest extends TestCase
{
    use RefreshDatabase;

    public function test_smk_student_requires_persetujuan_wali_under_uu_pdp(): void
    {
        $user = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'email_verified_at' => now(),
        ]);
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Komputer',
            'kategori' => 'siswa',
            'aktif' => true,
        ]);

        $response = $this->actingAs($user)->post(route('peserta.profil.simpan'), [
            'nim_nisn' => '12345678',
            'institusi' => 'SMKN 1 Yogyakarta',
            'jurusan_id' => $jurusan->id,
            'jenjang' => 'smk',
            'no_hp' => '081234567890',
            'persetujuan_wali' => '0',
        ]);

        $response->assertSessionHasErrors(['persetujuan_wali']);
        $this->assertDatabaseMissing('peserta_profil', [
            'user_id' => $user->id,
            'jenjang' => 'smk',
        ]);
    }

    public function test_smk_student_with_persetujuan_wali_records_timestamp(): void
    {
        $user = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'email_verified_at' => now(),
        ]);
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Komputer',
            'kategori' => 'siswa',
            'aktif' => true,
        ]);

        $response = $this->actingAs($user)->post(route('peserta.profil.simpan'), [
            'nim_nisn' => '12345678',
            'institusi' => 'SMKN 1 Yogyakarta',
            'jurusan_id' => $jurusan->id,
            'jenjang' => 'smk',
            'no_hp' => '081234567890',
            'persetujuan_wali' => '1',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('peserta.profil.show'));

        $this->assertDatabaseHas('peserta_profil', [
            'user_id' => $user->id,
            'jenjang' => 'smk',
        ]);

        $profil = $user->fresh()->profil;
        $this->assertNotNull($profil->persetujuan_wali_at);
    }

    public function test_non_smk_student_does_not_require_persetujuan_wali(): void
    {
        $user = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'email_verified_at' => now(),
        ]);
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Sistem Informasi',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $response = $this->actingAs($user)->post(route('peserta.profil.simpan'), [
            'nim_nisn' => '2100018001',
            'institusi' => 'Universitas Indonesia',
            'jurusan_id' => $jurusan->id,
            'jenjang' => 's1',
            'no_hp' => '081234567890',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('peserta.profil.show'));

        $this->assertDatabaseHas('peserta_profil', [
            'user_id' => $user->id,
            'jenjang' => 's1',
        ]);
    }
}
