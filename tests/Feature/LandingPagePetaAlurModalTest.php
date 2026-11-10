<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPagePetaAlurModalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uji navigasi peta alur langsung scroll ke tahapan dan verifikasi pop-up modal dihapus dari landing page.
     */
    public function test_landing_page_navigasi_peta_alur_scroll_ke_tahapan_dan_modal_dihapus(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // 1. Verifikasi menu navbar Peta Alur langsung mengarah ke anchor #tahapan
        $response->assertSee('href="#tahapan"', false);
        $response->assertSee('Peta Alur');

        // 2. Verifikasi tombol rute ke halaman mandiri peta alur interaktif tetap tersedia di section tahapan
        $response->assertSee(route('peta-alur'));
        $response->assertSee('Peta Alur Interaktif');

        // 3. Verifikasi pop-up modal dan trigger openPetaAlurModal sudah TIDAK LAGI di-render di landing page
        $response->assertDontSee('id="petaAlurModal"', false);
        $response->assertDontSee('openPetaAlurModal', false);
        $response->assertDontSee('landing-peta-alur-modal.css');

        // 4. Verifikasi section tahapan dan engine road canvas tetap tampil dengan baik
        $response->assertSee('id="tahapan"', false);
        $response->assertSee('id="tahapanRoadWrapper"', false);
    }
}
