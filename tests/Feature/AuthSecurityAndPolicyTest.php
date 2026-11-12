<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
