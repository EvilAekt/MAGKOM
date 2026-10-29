<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageFaqRedesignTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memverifikasi struktur tampilan baru FAQ 2-kolom dan kategori bertingkat sesuai referensi gambar.
     */
    public function test_landing_page_renders_redesigned_faq_section_components(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // 1. Verifikasi stylesheet FAQ baru
        $response->assertSee('css/landing-faq-redesign.css');

        // 2. Verifikasi layout 2-kolom container
        $response->assertSee('faq-redesign-container');
        $response->assertSee('faq-visual-col');
        $response->assertSee('faq-content-col');

        // 3. Verifikasi teks judul khas sesuai referensi gambar
        $response->assertSee('faq-accent-bar');
        $response->assertSee('<strong>Bingung?</strong> Berikut Beberapa Pertanyaan Yang Sering Ditanyakan', false);

        // 4. Verifikasi elemen SVG, karakter berseragam Kominfo, animasi tangan menunjuk, dan tipografi 3D FAQ
        $response->assertSee('faq-illustration-card');
        $response->assertSee('faq-svg-canvas');
        $response->assertSee('faq3DTextGroup');
        $response->assertSee('charPointingArm');
        $response->assertSee('faq-pointing-arm');
        $response->assertSee('#1b3860');

        // 5. Verifikasi kontainer kategori bertingkat
        $response->assertSee('id="faqCategoryAccordion"', false);
        $response->assertSee('faq-category-stack');

        // 6. Verifikasi 5 nama kategori data di skrip
        $response->assertSee('Syarat dan Tata Cara Mendaftar');
        $response->assertSee('Akun');
        $response->assertSee('Kuis');
        $response->assertSee('Benefit Magang');
        $response->assertSee('Kewajiban Peserta Magang');
    }

    /**
     * Memverifikasi keberadaan definisi CSS animasi tangan menunjuk dan tema warna Navy MagKom.
     */
    public function test_faq_stylesheet_contains_pointing_gesture_and_navy_theme(): void
    {
        $cssPath = public_path('css/landing-faq-redesign.css');
        $this->assertFileExists($cssPath);

        $cssContent = file_get_contents($cssPath);
        $this->assertStringContainsString('faq-pointing-arm', $cssContent);
        $this->assertStringContainsString('pointingGesture', $cssContent);
        $this->assertStringContainsString('prefers-reduced-motion', $cssContent);
        $this->assertStringContainsString('#1b3860', $cssContent);
    }
}
