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
     * Memastikan seluruh master layout menyertakan bobot 500 (Medium) pada Google Fonts.
     */
    public function test_master_layouts_import_open_sans_with_medium_weight_500(): void
    {
        $response = $this->get(route('landing'));
        $response->assertStatus(200);

        // Harus memuat bobot 400, 500, 600, 700
        $response->assertSee('family=Open+Sans:ital,wght@0,400;0,500;0,600;0,700', false);

        // Periksa juga layout auth
        $authResponse = $this->get(route('login'));
        $authResponse->assertStatus(200);
        $authResponse->assertSee('family=Open+Sans:ital,wght@0,400;0,500;0,600;0,700', false);
    }

    /**
     * Memastikan token bobot font (--fw-regular, --fw-medium, --fw-semibold, --fw-bold) didefinisikan di :root.
     */
    public function test_typography_tokens_defined_in_root(): void
    {
        $response = $this->get(route('landing'));
        $response->assertStatus(200);

        $response->assertSee('--fw-regular: 400;', false);
        $response->assertSee('--fw-medium: 500;', false);
        $response->assertSee('--fw-semibold: 600;', false);
        $response->assertSee('--fw-bold: 700;', false);
    }

    /**
     * Memastikan judul kolom footer menggunakan bobot Medium (500) dan teks footer menggunakan Regular (400).
     */
    public function test_footer_typography_rules_match_spec(): void
    {
        $response = $this->get(route('landing'));
        $response->assertStatus(200);

        // Judul kolom footer: Medium (500)
        $response->assertSee('.footer-col-title {', false);
        $response->assertSee('font-weight: var(--fw-medium);', false);

        // Teks biasa di footer: Regular (400)
        $response->assertSee('.footer {', false);
        $response->assertSee('font-weight: var(--fw-regular);', false);
        $response->assertSee('.footer-links a {', false);
        $response->assertSee('font-weight: var(--fw-regular);', false);
    }

    /**
     * Memastikan header dan heading menggunakan Bold (700) atau SemiBold (600), serta body menggunakan Regular (400).
     */
    public function test_header_and_body_typography_rules_match_spec(): void
    {
        $response = $this->get(route('landing'));
        $response->assertStatus(200);

        // Body: Regular (400)
        $response->assertSee('body {', false);
        $response->assertSee('font-weight: var(--fw-regular);', false);

        // Headings: Bold (700) untuk h1, h2 dan SemiBold (600) untuk h3-h6
        $response->assertSee('h1, h2 {', false);
        $response->assertSee('font-weight: var(--fw-bold);', false);
        $response->assertSee('h3, h4, h5, h6 {', false);
        $response->assertSee('font-weight: var(--fw-semibold);', false);

        // Body text: Regular (400)
        $response->assertSee('p, span.text-body, td, li {', false);
    }
}
