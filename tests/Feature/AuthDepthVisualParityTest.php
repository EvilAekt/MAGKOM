<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthDepthVisualParityTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_login_menampilkan_desain_depth_dan_aset_yang_sesuai(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
        $response->assertSee('Selamat datang');
        $response->assertSee('Masuk untuk mengakses sistem magang terpadu Kominfo.');
        $response->assertSee('MAGKOM');
        $response->assertSee('DINAS KOMINFO KAB. SLEMAN');
        $response->assertSee('Kelola magang lebih terarah');
        $response->assertSee('Logbook mingguan lebih rapi');
        $response->assertSee('Daftar magang lebih mudah');
        $response->assertSee('DE4F');
        $response->assertSee('Masuk dengan Google');
        $response->assertSee('Belum punya akun magang?');
        $response->assertSee(route('register'));
        $response->assertSee(route('password.request'));
        $response->assertSee('data-auth-canvas', false);
        $response->assertSee('auth-depth', false);
    }

    public function test_halaman_register_menampilkan_desain_depth_dan_form_lengkap(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
        $response->assertSee('Register Akun');
        $response->assertSee('Daftarkan akun Anda untuk memulai proses seleksi magang di Dinas Kominfo.');
        $response->assertSee('Tipe Pendaftaran Magang');
        $response->assertSee('Individu (1 Orang)');
        $response->assertSee('Kelompok (2–4 Orang)');
        $response->assertSee('Identitas Akun Calon Peserta');
        $response->assertSee('Informasi Pendidikan &amp; Pilihan Jurusan', false);
        $response->assertSee('Cek Kuota Terbuka →');
        $response->assertSee('-- Pilih Jenjang Pendidikan --');
        $response->assertSee('-- Pilih Bidang Peminatan / Penempatan --');
        $response->assertSee('Pilih bidang penempatan di Dinas Kominfo yang sesuai dengan peminatan magang Anda.');
        $response->assertSee('-- Pilih Jurusan / Program Studi --');
        $response->assertSee('Saya menyetujui');
        $response->assertSee('Kebijakan Privasi');
        $response->assertSee('Kode Verifikasi Keamanan');
        $response->assertSee('Sudah punya akun?');
        $response->assertSee(route('login'));
        $response->assertSee('Magang Kominfo • Dinas Kominfo Sleman • v1.0');
        $response->assertSee('Logbook mingguan lebih rapi');
        $response->assertSee('register-jurusan-data');
        $response->assertSee('data-auth-canvas', false);
        $response->assertSee('auth-depth', false);
    }

    public function test_halaman_lupa_kata_sandi_menampilkan_desain_depth_dan_form_pemulihan(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.forgot-password');
        $response->assertSee('Lupa Password?');
        $response->assertSee('Masukkan alamat email yang terdaftar pada akun Anda.');
        $response->assertSee('Email Akun Terdaftar');
        $response->assertSee('Kirim Tautan Reset');
        $response->assertSee('Kembali ke Login');
        $response->assertSee(route('login'));
        $response->assertSee('MAGKOM');
        $response->assertSee('DINAS KOMINFO KAB. SLEMAN');
        $response->assertSee('Logbook mingguan lebih rapi');
        $response->assertSee('data-auth-canvas', false);
        $response->assertSee('auth-depth', false);
    }

    public function test_form_lupa_kata_sandi_dapat_mengirim_permohonan_dan_menampilkan_pesan_status(): void
    {
        $response = $this->from(route('password.request'))->post(route('password.email'), [
            'email' => 'calon.peserta@example.com',
        ]);

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHas('status');

        $followed = $this->followRedirects($response);
        $followed->assertSee('Jika alamat email terdaftar pada sistem, kami akan mengirimkan instruksi untuk mengatur ulang password.');
    }
}
