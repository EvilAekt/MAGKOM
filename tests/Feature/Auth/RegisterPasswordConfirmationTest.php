<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\Jurusan;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterPasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionSeeder::class);
    }

    public function test_register_view_renders_two_password_fields_and_toggle_buttons(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();

        // 1. Field Kata Sandi Utama
        $response->assertSee('id="sandi"', false);
        $response->assertSee('name="password"', false);
        $response->assertSee('data-target="sandi"', false);
        $response->assertSee('class="pw-strength-wrapper"', false);

        // 2. Field Konfirmasi Kata Sandi Baru
        $response->assertSee('id="sandi_confirmation"', false);
        $response->assertSee('name="password_confirmation"', false);
        $response->assertSee('data-target="sandi_confirmation"', false);
        $response->assertSee('id="hint-sandi-confirmation"', false);
        $response->assertSee('Masukkan ulang kata sandi yang sama untuk konfirmasi.', false);

        // 3. Field Kata Sandi Rekan Anggota Tim (Pendaftaran Kelompok)
        $response->assertSee('class="card-anggota-pw-wrap"', false);
        $response->assertSee('id="anggota_0_password"', false);
        $response->assertSee('data-target="anggota_0_password"', false);
        $response->assertSee('id="anggota_0_password_confirmation"', false);
        $response->assertSee('data-target="anggota_0_password_confirmation"', false);
        $response->assertSee('btn-toggle-pw-compact', false);
        $response->assertSee('class="card-anggota-pw-hint"', false);
    }

    public function test_registration_fails_when_password_confirmation_does_not_match(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'Peserta Uji',
            'email' => 'peserta.mismatch@magkom.test',
            'no_telp' => '081234567890',
            'password' => 'Magang2026!',
            'password_confirmation' => 'BedaPassword123!',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'peserta.mismatch@magkom.test',
        ]);
    }

    public function test_registration_fails_when_password_confirmation_is_empty(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'Peserta Uji Empty',
            'email' => 'peserta.emptyconfirm@magkom.test',
            'no_telp' => '081234567890',
            'password' => 'Magang2026!',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'peserta.emptyconfirm@magkom.test',
        ]);
    }

    public function test_registration_succeeds_when_passwords_match(): void
    {
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
        ]);

        $response = $this->post(route('register'), [
            'username' => 'Peserta Cocok',
            'email' => 'peserta.cocok@magkom.test',
            'no_telp' => '081234567891',
            'password' => 'Magang2026!',
            'password_confirmation' => 'Magang2026!',
            'jenjang' => 's1',
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Gadjah Mada',
            'nim_nisn' => '21/123456/TK/12345',
        ]);

        $response->assertRedirect(route('peserta.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'peserta.cocok@magkom.test',
            'nama' => 'Peserta Cocok',
        ]);
    }

    public function test_backward_compatibility_when_post_without_confirmation_field(): void
    {
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Sistem Informasi',
            'kategori' => 'mahasiswa',
        ]);

        $response = $this->post(route('register'), [
            'username' => 'Peserta Legacy Payload',
            'email' => 'peserta.legacy@magkom.test',
            'no_telp' => '081234567892',
            'password' => 'Magang2026!',
            'jenjang' => 's1',
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Negeri Yogyakarta',
            'nim_nisn' => '21/654321/SI/54321',
        ]);

        $response->assertRedirect(route('peserta.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'peserta.legacy@magkom.test',
            'nama' => 'Peserta Legacy Payload',
        ]);
    }

    public function test_kelompok_registration_fails_when_member_password_confirmation_does_not_match(): void
    {
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Komputer',
            'kategori' => 'mahasiswa',
        ]);

        $response = $this->post(route('register'), [
            'username' => 'Ketua Kelompok Mismatch',
            'email' => 'ketua.mismatch@magkom.test',
            'no_telp' => '081234567810',
            'password' => 'Magang2026!',
            'password_confirmation' => 'Magang2026!',
            'tipe_pendaftaran' => 'kelompok',
            'nama_kelompok' => 'Tim Uji Mismatch',
            'jenjang' => 's1',
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Amikom',
            'nim_nisn' => '21/111111/TK/11111',
            'anggota' => [
                [
                    'nama' => 'Rekan Anggota Mismatch',
                    'nim_nisn' => '21/222222/TK/22222',
                    'no_hp' => '081234567811',
                    'email' => 'rekan.mismatch@magkom.test',
                    'password' => 'Magang2026!',
                    'password_confirmation' => 'SandiBedaLain123!',
                ],
            ],
        ]);

        $response->assertSessionHasErrors([
            'anggota.0.password' => 'Konfirmasi kata sandi rekan anggota tim tidak cocok.',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'rekan.mismatch@magkom.test',
        ]);
    }

    public function test_kelompok_registration_succeeds_when_member_password_confirmation_matches(): void
    {
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknologi Informasi',
            'kategori' => 'mahasiswa',
        ]);

        $response = $this->post(route('register'), [
            'username' => 'Ketua Kelompok Match',
            'email' => 'ketua.match@magkom.test',
            'no_telp' => '081234567820',
            'password' => 'Magang2026!',
            'password_confirmation' => 'Magang2026!',
            'tipe_pendaftaran' => 'kelompok',
            'nama_kelompok' => 'Tim Uji Match',
            'jenjang' => 's1',
            'jurusan_id' => $jurusan->id,
            'institusi' => 'Universitas Amikom',
            'nim_nisn' => '21/333333/TI/33333',
            'anggota' => [
                [
                    'nama' => 'Rekan Anggota Match',
                    'nim_nisn' => '21/444444/TI/44444',
                    'no_hp' => '081234567821',
                    'email' => 'rekan.match@magkom.test',
                    'password' => 'Magang2026!',
                    'password_confirmation' => 'Magang2026!',
                ],
            ],
        ]);

        $response->assertRedirect(route('peserta.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'ketua.match@magkom.test',
            'nama' => 'Ketua Kelompok Match',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'rekan.match@magkom.test',
            'nama' => 'Rekan Anggota Match',
        ]);
    }
}
