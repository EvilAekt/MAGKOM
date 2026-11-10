<?php

namespace Tests\Feature;

use App\Models\BulanMagang;
use App\Models\Jurusan;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TypographyDesignSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-10-08 10:00:00', 'Asia/Jakarta'));
        CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-08 10:00:00', 'Asia/Jakarta'));

        if (Jurusan::count() === 0 || BulanMagang::count() === 0) {
            $this->seed();
        }
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    /**
     * Memastikan master layout menyertakan bobot font Google Fonts yang sesuai spesifikasi.
     */
    public function test_master_layouts_import_open_sans_with_medium_weight_500(): void
    {
        $response = $this->get(route('landing'));
        $response->assertStatus(200);

        // Landing page memuat Google Fonts Lexend & Source Sans 3 dengan bobot 400, 500, 600, 700
        $response->assertSee('family=Lexend:wght@400;500;600;700', false);
        $response->assertSee('family=Source+Sans+3:wght@400;500;600;700', false);

        // Layout auth memuat Google Fonts Open Sans dengan bobot 400, 500, 600, 700
        $authResponse = $this->get(route('login'));
        $authResponse->assertStatus(200);
        $authResponse->assertSee('family=Open+Sans:ital,wght@0,400;0,500;0,600;0,700', false);
    }

    /**
     * Memastikan token bobot font (--fw-regular, --fw-medium, --fw-semibold, --fw-bold) didefinisikan di CSS design system.
     */
    public function test_typography_tokens_defined_in_root(): void
    {
        $variablesCss = file_get_contents(resource_path('css/base/variables.css'));
        $this->assertNotFalse($variablesCss);

        $this->assertStringContainsString('--fw-regular: 400;', $variablesCss);
        $this->assertStringContainsString('--fw-medium: 500;', $variablesCss);
        $this->assertStringContainsString('--fw-semibold: 600;', $variablesCss);
        $this->assertStringContainsString('--fw-bold: 700;', $variablesCss);

        // Memastikan app.css mengimpor variables.css ke dalam pipeline Vite
        $appCss = file_get_contents(resource_path('css/app.css'));
        $this->assertNotFalse($appCss);
        $this->assertStringContainsString("@import './base/variables.css';", $appCss);

        // Memastikan landing page memuat asset bundle Vite
        $response = $this->get(route('landing'));
        $response->assertStatus(200);
        $response->assertSee('build/assets/landing', false);
    }

    /**
     * Memastikan judul kolom footer menggunakan font display bobot bold (700) dan teks link footer terstruktur.
     */
    public function test_footer_typography_rules_match_spec(): void
    {
        $landingCss = file_get_contents(resource_path('css/pages/public/landing.css'));
        $this->assertNotFalse($landingCss);

        // Judul kolom footer: font display, bold (700)
        $this->assertStringContainsString('.footer-col-title {', $landingCss);
        $this->assertStringContainsString('font-family: var(--font-display);', $landingCss);
        $this->assertStringContainsString('font-weight: 700;', $landingCss);

        // Teks biasa dan navigasi tautan di footer
        $this->assertStringContainsString('.footer-address-block {', $landingCss);
        $this->assertStringContainsString('.footer-links-clean {', $landingCss);

        // Memastikan elemen footer dirender di halaman landing publik
        $response = $this->get(route('landing'));
        $response->assertStatus(200);
        $response->assertSee('class="footer-col-title"', false);
    }

    /**
     * Memastikan header dan heading menggunakan Bold (700) atau SemiBold (600), serta body menggunakan Regular (400).
     */
    public function test_header_and_body_typography_rules_match_spec(): void
    {
        $resetCss = file_get_contents(resource_path('css/base/reset.css'));
        $this->assertNotFalse($resetCss);

        // Body: Regular (400)
        $this->assertStringContainsString('body {', $resetCss);
        $this->assertStringContainsString('font-weight: var(--fw-regular);', $resetCss);

        // Headings: Bold (700) untuk h1, h2 dan SemiBold (600) untuk h3-h6
        $this->assertStringContainsString('h1, h2 {', $resetCss);
        $this->assertStringContainsString('font-weight: var(--fw-bold);', $resetCss);
        $this->assertStringContainsString('h3, h4, h5, h6 {', $resetCss);
        $this->assertStringContainsString('font-weight: var(--fw-semibold);', $resetCss);

        // Body text: Regular (400)
        $this->assertStringContainsString('p, span.text-body, td, li {', $resetCss);
        $this->assertStringContainsString('font-weight: var(--fw-regular);', $resetCss);

        // Memastikan reset.css diimpor oleh app.css
        $appCss = file_get_contents(resource_path('css/app.css'));
        $this->assertNotFalse($appCss);
        $this->assertStringContainsString("@import './base/reset.css';", $appCss);
    }
}
