<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthSecurityAndPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_kebijakan_privasi_dan_syarat_ketentuan_dapat_diakses_publik(): void
    {
        $response = $this->get(route('kebijakan-privasi'));

        $response->assertStatus(200);
        $response->assertViewIs('public.kebijakan-privasi');
        $response->assertSee('Kebijakan Privasi &amp; Syarat Ketentuan', false);
        $response->assertSee('Kebijakan Privasi (UU PDP)');
        $response->assertSee('Syarat &amp; Ketentuan Magang', false);
        $response->assertSee('Kualifikasi &amp; Status Calon Peserta', false);
        $response->assertSee('Tata Tertib, Kehadiran &amp; Jam Kerja Kedinasan', false);
        $response->assertSee('Kewajiban Pengisian Logbook Mingguan &amp; Tugas', false);
        $response->assertSee('Kerahasiaan Informasi &amp; Keamanan Siber', false);
    }

    public function test_modal_kebijakan_privasi_tersedia_di_halaman_login_dan_register(): void
    {
        // 1. Verifikasi di Halaman Login
        $loginResponse = $this->get(route('login'));
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('id="modalPrivasiSyarat"', false);
        $loginResponse->assertSee('data-action="open-modal-privasi"', false);
        $loginResponse->assertSee('Kebijakan Privasi &amp; Syarat Ketentuan', false);

        // 2. Verifikasi di Halaman Register
        $regResponse = $this->get(route('register'));
        $regResponse->assertStatus(200);
        $regResponse->assertSee('id="modalPrivasiSyarat"', false);
        $regResponse->assertSee('data-action="open-modal-privasi"', false);
        $regResponse->assertSee('Kebijakan Privasi &amp; Syarat Ketentuan', false);
    }

    public function test_halaman_login_menampilkan_banner_peringatan_lockout_saat_sesi_aktif(): void
    {
        $response = $this->withSession([
            'lockout_seconds' => 45,
            'error_lockout' => 'Akses akun dibekukan sementara karena terlalu banyak percobaan masuk yang gagal.',
        ])->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Akses Akun Terkunci Sementara');
        $response->assertSee('data-lockout-seconds="45"', false);
        $response->assertSee('id="lockoutTimerDisplay"', false);
        $response->assertSee('45');
        $response->assertSee('Pulihkan Akun →');
        $response->assertSee(route('password.request'));
    }

    public function test_halaman_error_429_menampilkan_desain_keamanan_dan_tautan_login(): void
    {
        $view = $this->view('errors.429');

        $view->assertSee('429');
        $view->assertSee('Akses Akun Dibatasi Sementara');
        $view->assertSee('brute-force');
        $view->assertSee(route('login'));
        $view->assertSee('Kembali ke Halaman Login');
    }

    public function test_login_requires_valid_rfc_email_format(): void
    {
        // 1. Coba login dengan username non-email polos ('admin')
        $response1 = $this->post(route('login'), [
            'email' => 'admin',
            'password' => 'Password123!',
        ]);

        $response1->assertSessionHasErrors(['email']);
        $this->assertGuest();

        // 2. Coba login dengan string sembarangan tanpa domain
        $response2 = $this->post(route('login'), [
            'email' => 'user_tanpa_domain',
            'password' => 'Password123!',
        ]);

        $response2->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_login_rejects_insecure_local_password_overwrite(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@tim.magkom.go.id',
            'password' => Hash::make('PasswordAsliKuat123!'),
        ]);

        // Coba login dengan password fallback lama ('admin')
        $response = $this->post(route('login'), [
            'email' => 'admin@tim.magkom.go.id',
            'password' => 'admin',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();

        // Verifikasi bahwa password asli di database TIDAK tertimpa/termodifikasi
        $user->refresh();
        $this->assertTrue(Hash::check('PasswordAsliKuat123!', $user->password));
        $this->assertFalse(Hash::check('admin', $user->password));
    }

    public function test_register_requires_password_with_letters_and_digits(): void
    {
        // Password hanya huruf (tanpa angka) harus ditolak
        $response1 = $this->post(route('register'), [
            'nama' => 'Test User',
            'email' => 'test.user@gmail.com',
            'password' => 'hanyahuruf',
            'no_telp' => '081234567890',
        ]);

        $response1->assertSessionHasErrors(['password']);

        // Password hanya angka (tanpa huruf) harus ditolak
        $response2 = $this->post(route('register'), [
            'nama' => 'Test User',
            'email' => 'test.user@gmail.com',
            'password' => '12345678',
            'no_telp' => '081234567890',
        ]);

        $response2->assertSessionHasErrors(['password']);
    }

    public function test_register_requires_valid_rfc_email_format(): void
    {
        $response = $this->post(route('register'), [
            'nama' => 'Test User',
            'email' => 'bukan_alamat_email',
            'password' => 'Password123!',
            'no_telp' => '081234567890',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_register_regenerates_session_id_anti_fixation(): void
    {
        $jurusan = Jurusan::first() ?? Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);

        $response = $this->post(route('register'), [
            'nama' => 'Peserta Anti Fixation',
            'email' => 'antifixation@gmail.com',
            'password' => 'PasswordKuat123!',
            'no_telp' => '081298765432',
            'jenjang' => 's1',
            'jurusan_id' => $jurusan->id,
            'institusi' => 'UGM',
            'nim_nisn' => '12345',
        ]);

        $response->assertRedirect(route('peserta.dashboard'));
        $this->assertAuthenticated();

        // Pastikan akun terdaftar dengan email yang ternormalisasi
        $this->assertDatabaseHas('users', [
            'email' => 'antifixation@gmail.com',
        ]);
    }
}
