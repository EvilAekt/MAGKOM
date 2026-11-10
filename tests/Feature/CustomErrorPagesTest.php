<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CustomErrorPagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Daftarkan rute simulasi untuk menguji halaman error kustom
        Route::get('/simulasi-403', fn () => abort(403));
        Route::get('/simulasi-419', fn () => abort(419));
        Route::get('/simulasi-429', fn () => abort(429));
        Route::get('/simulasi-500', fn () => abort(500));
    }

    public function test_halaman_403_kustom_dirender_dengan_benar_dan_tanpa_kebocoran_data(): void
    {
        $response = $this->get('/simulasi-403');

        $response->assertStatus(403);
        $response->assertSee('Akses Ditolak');
        $response->assertSee('403');
        $response->assertDontSee('SQLSTATE');
        $response->assertDontSee('Stack trace');
    }

    public function test_halaman_404_kustom_dirender_saat_rute_tidak_ditemukan(): void
    {
        $response = $this->get('/rute-acak-pasti-tidak-ada-'.uniqid());

        $response->assertStatus(404);
        $response->assertSee('Halaman Tidak Ditemukan');
        $response->assertSee('404');
        $response->assertDontSee('SQLSTATE');
    }

    public function test_halaman_419_kustom_dirender_saat_sesi_kedaluwarsa(): void
    {
        $response = $this->get('/simulasi-419');

        $response->assertStatus(419);
        $response->assertSee('Sesi Anda Telah Kedaluwarsa');
        $response->assertSee('419');
    }

    public function test_halaman_429_kustom_dirender_saat_terlalu_banyak_permintaan(): void
    {
        $response = $this->get('/simulasi-429');

        $response->assertStatus(429);
        $response->assertSee('Terlalu Banyak Permintaan');
        $response->assertSee('429');
    }

    public function test_halaman_500_kustom_dirender_tanpa_membocorkan_detail_internal_server(): void
    {
        $response = $this->get('/simulasi-500');

        $response->assertStatus(500);
        $response->assertSee('Terjadi Kesalahan pada Server');
        $response->assertSee('500');
        $response->assertDontSee('SQLSTATE');
        $response->assertDontSee('app/Http');
    }
}
