<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPagePetaAlurModalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Uji keberadaan komponen modal interaktif peta alur pada landing page.
     */
    public function test_landing_page_renders_interactive_peta_alur_modal_components(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Verifikasi stylesheet modal
        $response->assertSee('css/landing-peta-alur-modal.css');

        // Verifikasi tombol menuju halaman penuh peta alur
        $response->assertSee(route('peta-alur'));
        $response->assertSee('Peta Alur Interaktif');

        // Verifikasi dialog modal ARIA
        $response->assertSee('id="petaAlurModal"', false);
        $response->assertSee('role="dialog"', false);
        $response->assertSee('aria-modal="true"', false);
        $response->assertSee('id="modalPetaAlurTitle"', false);
        $response->assertSee('id="btnModalAudioToggle"', false);
        $response->assertSee('id="btnSimAutoplay"', false);

        // Verifikasi bahwa tombol 'Buka Peta Lengkap' sudah DIHILANGKAN sesuai permintaan
        $response->assertDontSee('Buka Peta Lengkap');

        // Verifikasi komponen Road Map Canvas SVG & Mobil Animasi di kanvas panorama penuh (16:9)
        $response->assertSee('id="modalRoadCanvasWrap"', false);
        $response->assertSee('id="modalRoadCanvas"', false);
        $response->assertSee('modal-panoramic-container', false);
        $response->assertSee('panoramic-svg-canvas', false);
        $response->assertSee('id="modalRoadCarGroup"', false);
        $response->assertSee('id="modalRoadGpsPin"', false);

        // Verifikasi elemen lanskap hidup Sleman (Gunung Merapi, Menara Kominfo & Lampu Mobil)
        $response->assertSee('id="modalMerapiBackdrop"', false);
        $response->assertSee('id="diskominfoHubScene"', false);
        $response->assertSee('class="car-headlight-beam"', false);

        // Verifikasi adegan karakter interaktif 4 tahap (Living Characters)
        $response->assertSee('id="modalCharacterScene"', false);
        $response->assertSee('id="charScene1"', false);
        $response->assertSee('id="charScene2"', false);
        $response->assertSee('id="charScene3"', false);
        $response->assertSee('id="charScene4"', false);

        // Verifikasi tombol kontrol Zoom mengambang (+, -, Semua)
        $response->assertSee('id="btnRoadZoomIn"', false);
        $response->assertSee('id="btnRoadZoomOut"', false);
        $response->assertSee('id="btnRoadZoomReset"', false);

        // Verifikasi bilah tab horizontal 4 tahap di bawah canvas
        $response->assertSee('id="roadTab1"', false);
        $response->assertSee('id="roadTab2"', false);
        $response->assertSee('id="roadTab3"', false);
        $response->assertSee('id="roadTab4"', false);
        $response->assertSee('id="roadTabScore1"', false);

        // Verifikasi Floating Mission HUD Overlay & Filter Toolbar
        $response->assertSee('id="modalFilterBulan"', false);
        $response->assertSee('id="missionHudOverlay"', false);
        $response->assertSee('id="modalStepDetailPanel"', false);
    }
}
