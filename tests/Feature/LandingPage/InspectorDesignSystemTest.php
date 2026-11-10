<?php

declare(strict_types=1);

namespace Tests\Feature\LandingPage;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\LandingCmsStagingService;
use App\Services\LandingPage\BlockRegistry;
use App\Services\LandingPage\BlockSchemaValidator;
use App\Services\LandingPage\LandingPageDocumentService;
use App\Services\LandingPage\LandingPageRenderer;
use App\Services\LandingPage\LegacyCmsAdapter;
use App\Support\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InspectorDesignSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $peserta;

    protected LandingPageRenderer $renderer;

    protected BlockSchemaValidator $validator;

    protected LandingPageDocumentService $documentService;

    protected LandingCmsStagingService $stagingService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin.inspector@magkom.test',
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
        ]);

        $this->admin = User::factory()->create([
            'email' => 'admin.inspector@magkom.test',
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
        ]);

        $this->peserta = User::factory()->create([
            'email' => 'peserta.inspector@magkom.test',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $this->renderer = app(LandingPageRenderer::class);
        $this->validator = app(BlockSchemaValidator::class);
        $this->documentService = app(LandingPageDocumentService::class);
        $this->stagingService = app(LandingCmsStagingService::class);
    }

    /**
     * TEST 1: Block Registry menyediakan Style Presets resmi (Colors, Buttons, Cards, Typography).
     */
    public function test_block_registry_provides_all_style_presets(): void
    {
        $presets = BlockRegistry::getStylePresets();

        $this->assertArrayHasKey('colors', $presets);
        $this->assertArrayHasKey('buttons', $presets);
        $this->assertArrayHasKey('cards', $presets);
        $this->assertArrayHasKey('typography', $presets);

        // Color Presets
        $this->assertArrayHasKey('magkom_official', $presets['colors']);
        $this->assertArrayHasKey('navy_professional', $presets['colors']);
        $this->assertArrayHasKey('blue_modern', $presets['colors']);
        $this->assertArrayHasKey('neutral_government', $presets['colors']);

        // Button Presets
        $this->assertArrayHasKey('rounded', $presets['buttons']);
        $this->assertArrayHasKey('pill', $presets['buttons']);
        $this->assertArrayHasKey('sharp', $presets['buttons']);

        // Card Presets
        $this->assertArrayHasKey('standard', $presets['cards']);
        $this->assertArrayHasKey('soft', $presets['cards']);
        $this->assertArrayHasKey('minimal', $presets['cards']);

        // Typography Presets
        $this->assertArrayHasKey('official', $presets['typography']);
        $this->assertArrayHasKey('modern', $presets['typography']);
        $this->assertArrayHasKey('compact', $presets['typography']);
    }

    /**
     * TEST 2: Block Registry mengekspor Global Styles bawaan dan Presets ke konfigurasi frontend.
     */
    public function test_block_registry_exports_global_styles_and_presets_to_frontend(): void
    {
        $config = BlockRegistry::toFrontendConfig();

        $this->assertArrayHasKey('global_styles', $config);
        $this->assertArrayHasKey('presets', $config);
        $this->assertEquals('#1B3860', $config['global_styles']['colors']['primary']);
        $this->assertEquals('Lexend', $config['global_styles']['typography']['heading_font']);
    }

    /**
     * TEST 3: Block Schema Validator memvalidasi dan membersihkan Global Styles.
     */
    public function test_schema_validator_sanitizes_and_clamps_global_styles(): void
    {
        $input = [
            'colors' => [
                'primary' => '#123456',
                'accent' => 'invalid-color-not-hex', // harus disaring / di-fallback
                'background' => '#ABC',
            ],
            'typography' => [
                'heading_font' => 'Comic Sans MS', // di luar whitelist
                'body_font' => 'Inter',
                'base_size_px' => 999, // melebihi batas max 24px
                'line_height' => 0.2, // di bawah batas min 1.0
            ],
            'layout' => [
                'container_max_width_px' => 3000, // max 1920
                'section_gap_px' => -10, // min 0
                'card_radius_px' => 50, // max 32
                'card_shadow' => 'malicious_shadow_code',
            ],
            'buttons' => [
                'radius_px' => 99, // max 40
                'padding_y_px' => 100, // max 30
                'primary_bg' => '#FF0000',
            ],
        ];

        $sanitized = $this->validator->sanitizeGlobalStyles($input);

        // Hex Colors
        $this->assertEquals('#123456', $sanitized['colors']['primary']);
        $this->assertNotEquals('invalid-color-not-hex', $sanitized['colors']['accent']);
        $this->assertEquals('#AABBCC', $sanitized['colors']['background']);

        // Typography Font Whitelist
        $this->assertEquals('Lexend', $sanitized['typography']['heading_font']); // fallback
        $this->assertEquals('Inter', $sanitized['typography']['body_font']);
        $this->assertEquals(24, $sanitized['typography']['base_size_px']); // clamped to max 24
        $this->assertEquals(1.0, $sanitized['typography']['line_height']); // clamped to min 1.0

        // Layout Clamping
        $this->assertEquals(1920, $sanitized['layout']['container_max_width_px']); // clamped
        $this->assertEquals(0, $sanitized['layout']['section_gap_px']); // clamped
        $this->assertEquals(32, $sanitized['layout']['card_radius_px']); // clamped
        $this->assertEquals('soft', $sanitized['layout']['card_shadow']); // fallback

        // Buttons Clamping
        $this->assertEquals(40, $sanitized['buttons']['radius_px']);
        $this->assertEquals(30, $sanitized['buttons']['padding_y_px']);
        $this->assertEquals('#FF0000', $sanitized['buttons']['primary_bg']);
    }

    /**
     * TEST 4: Renderer menghasilkan Global CSS Custom Properties (:root) dari Global Styles.
     */
    public function test_renderer_generates_valid_global_css_root_properties(): void
    {
        $globalStyles = [
            'colors' => [
                'primary' => '#0F172A',
                'accent' => '#3B82F6',
            ],
            'typography' => [
                'heading_font' => 'Plus Jakarta Sans',
                'base_size_px' => 18,
            ],
            'layout' => [
                'container_max_width_px' => 1400,
                'card_radius_px' => 16,
            ],
        ];

        $css = $this->renderer->generateGlobalCss($globalStyles);

        $this->assertStringContainsString(':root {', $css);
        $this->assertStringContainsString('--wp-color-primary: #0F172A;', $css);
        $this->assertStringContainsString('--wp-color-accent: #3B82F6;', $css);
        $this->assertStringContainsString("--wp-font-display: 'Plus Jakarta Sans', sans-serif;", $css);
        $this->assertStringContainsString('--wp-base-font-size: 18px;', $css);
        $this->assertStringContainsString('--wp-container-max-w: 1400px;', $css);
        $this->assertStringContainsString('--wp-card-border-radius: 16px;', $css);
    }

    /**
     * TEST 5: Block Style Isolation — Dua blok dengan tipe sama memiliki scoped styles terisolasi.
     */
    public function test_block_style_isolation_prevents_style_leaks_between_instances(): void
    {
        $heroA = [
            'padding_y' => 120,
            'margin_y' => 10,
            'font_size' => 48,
            'bg_color' => '#1B3860',
            'text_color' => '#FFFFFF',
            'text_align' => 'center',
            'card_radius' => 16,
        ];

        $heroB = [
            'padding_y' => 40,
            'margin_y' => 0,
            'font_size' => 28,
            'bg_color' => '#FFFFFF',
            'text_color' => '#0F172A',
            'text_align' => 'left',
            'card_radius' => 4,
        ];

        $cssA = $this->renderer->generateScopedCss('block-hero-alpha', $heroA);
        $cssB = $this->renderer->generateScopedCss('block-hero-beta', $heroB);

        // Selector A hanya menarget ID block alpha
        $this->assertStringContainsString('[data-wp-block-id="block-hero-alpha"]', $cssA);
        $this->assertStringNotContainsString('block-hero-beta', $cssA);
        $this->assertStringContainsString('--block-padding-y: 120px;', $cssA);
        $this->assertStringContainsString('--block-bg-color: #1B3860;', $cssA);
        $this->assertStringContainsString('--block-text-align: center;', $cssA);

        // Selector B hanya menarget ID block beta
        $this->assertStringContainsString('[data-wp-block-id="block-hero-beta"]', $cssB);
        $this->assertStringNotContainsString('block-hero-alpha', $cssB);
        $this->assertStringContainsString('--block-padding-y: 40px;', $cssB);
        $this->assertStringContainsString('--block-bg-color: #FFFFFF;', $cssB);
        $this->assertStringContainsString('--block-text-align: left;', $cssB);
    }

    /**
     * TEST 6: Responsive Breakpoints — Scoped style menghasilkan media queries Desktop, Tablet, dan Mobile.
     */
    public function test_scoped_styles_generate_desktop_tablet_and_mobile_media_queries(): void
    {
        $styles = [
            'desktop' => [
                'padding_y' => 100,
                'font_size' => 48,
            ],
            'tablet' => [
                'padding_y' => 60,
                'font_size' => 36,
            ],
            'mobile' => [
                'padding_y' => 30,
                'font_size' => 24,
            ],
        ];

        $scopedCss = $this->renderer->generateScopedCss('block-test-responsive', $styles);

        // Base Desktop rule
        $this->assertStringContainsString('[data-wp-block-id="block-test-responsive"] {', $scopedCss);
        $this->assertStringContainsString('--block-padding-y: 100px;', $scopedCss);

        // Tablet Media Query (< 1024px)
        $this->assertStringContainsString('@media (max-width: 1023px)', $scopedCss);
        $this->assertStringContainsString('--block-padding-y: 60px;', $scopedCss);
        $this->assertStringContainsString('--block-font-size: 36px;', $scopedCss);

        // Mobile Media Query (< 768px)
        $this->assertStringContainsString('@media (max-width: 767px)', $scopedCss);
        $this->assertStringContainsString('--block-padding-y: 30px;', $scopedCss);
        $this->assertStringContainsString('--block-font-size: 24px;', $scopedCss);
    }

    /**
     * TEST 7: Advanced Settings Validator menyaring Custom CSS Class dan HTML Anchor (XSS Protection).
     */
    public function test_advanced_settings_sanitizes_custom_css_class_and_anchor(): void
    {
        $maliciousSettings = [
            'custom_css_class' => 'my-class <script>alert(1)</script> another-class; drop table',
            'html_anchor' => 'anchor#with?bad=chars<img src=x onerror=alert(1)>',
            'visible_desktop' => true,
            'visible_tablet' => false,
            'visible_mobile' => 'invalid_bool',
        ];

        $sanitized = $this->validator->sanitizeSettings($maliciousSettings);

        // Karakter berbahaya dibersihkan
        $this->assertStringNotContainsString('<script>', $sanitized['custom_css_class']);
        $this->assertStringNotContainsString(';', $sanitized['custom_css_class']);
        $this->assertEquals('my-class alert1 another-class drop table', $sanitized['custom_css_class']);

        $this->assertStringNotContainsString('#', $sanitized['html_anchor']);
        $this->assertStringNotContainsString('?', $sanitized['html_anchor']);
        $this->assertStringNotContainsString('<', $sanitized['html_anchor']);
        $this->assertEquals('anchorwithbadchars', $sanitized['html_anchor']);

        // Booleans
        $this->assertTrue($sanitized['visible_desktop']);
        $this->assertFalse($sanitized['visible_tablet']);
        $this->assertTrue($sanitized['visible_mobile']); // fallback true
    }

    /**
     * TEST 8: SuperAdmin dapat menyimpan batch Global Styles dan Scoped Block Styles via Save Draft.
     */
    public function test_superadmin_can_save_global_styles_and_scoped_block_styles(): void
    {
        $payload = [
            'landing_global_styles' => [
                'colors' => [
                    'primary' => '#1E40AF',
                    'accent' => '#0284C7',
                ],
                'typography' => [
                    'heading_font' => 'Plus Jakarta Sans',
                    'base_size_px' => 17,
                ],
                'layout' => [
                    'container_max_width_px' => 1360,
                    'card_radius_px' => 16,
                ],
            ],
            'landing_page_document' => [
                'schema_version' => '1.1',
                'global_styles' => [
                    'colors' => ['primary' => '#1E40AF'],
                ],
                'blocks' => [
                    [
                        'id' => 'block-hero-scoped',
                        'type' => 'hero',
                        'title' => 'Hero Banner Scoped',
                        'visible' => true,
                        'order' => 1,
                        'content' => [
                            'title' => 'Judul Hero Scoped Test',
                            'kicker' => 'Kicker Test',
                        ],
                        'styles' => [
                            'padding_y' => 110,
                            'bg_color' => '#1E40AF',
                        ],
                        'settings' => [
                            'html_anchor' => 'hero-utama',
                            'custom_css_class' => 'hero-featured-box',
                            'visible_desktop' => true,
                            'visible_tablet' => true,
                            'visible_mobile' => false,
                        ],
                    ],
                    [
                        'id' => 'block-footer-scoped',
                        'type' => 'footer',
                        'title' => 'Footer Dinas',
                        'visible' => true,
                        'order' => 2,
                        'content' => [
                            'alamat' => 'Jl Parasamya Sleman',
                        ],
                        'styles' => [
                            'padding_y' => 50,
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verifikasi tersimpan di draft pengaturan
        $draftDoc = Pengaturan::json('draft_landing_page_document');
        $this->assertNotEmpty($draftDoc);
        $this->assertEquals('block-hero-scoped', $draftDoc['blocks'][0]['id']);
        $this->assertEquals(110, $draftDoc['blocks'][0]['styles']['padding_y']);
        $this->assertEquals('hero-utama', $draftDoc['blocks'][0]['settings']['html_anchor']);
        $this->assertFalse($draftDoc['blocks'][0]['settings']['visible_mobile']);

        // Verifikasi sinkronisasi legacy keys dari global styles
        $this->assertEquals('#1E40AF', Pengaturan::get('draft_landing_theme_primary_color'));
    }

    /**
     * TEST 9: Legacy CMS Adapter menyinkronkan Global Styles secara dua arah.
     */
    public function test_legacy_cms_adapter_bidirectional_sync_for_global_styles(): void
    {
        // 1. Dari Document ke Legacy
        $document = [
            'global_styles' => [
                'colors' => [
                    'primary' => '#0F766E',
                    'accent' => '#0D9488',
                ],
                'typography' => [
                    'heading_font' => 'Poppins',
                ],
                'layout' => [
                    'container_max_width_px' => 1440,
                ],
            ],
            'blocks' => BlockRegistry::getDefaultDocument()['blocks'],
        ];

        LegacyCmsAdapter::syncDocumentToLegacySettings($document, draft: true);

        $this->assertEquals('#0F766E', Pengaturan::get('draft_landing_theme_primary_color'));
        $this->assertEquals('#0D9488', Pengaturan::get('draft_landing_theme_accent_color'));
        $this->assertEquals('Poppins', Pengaturan::get('draft_landing_theme_font_display'));
        $this->assertEquals('1440', Pengaturan::get('draft_landing_container_max_width_px'));

        // 2. Dari Legacy ke Document
        $derivedDoc = LegacyCmsAdapter::legacySettingsToDocument(draft: true);

        $this->assertArrayHasKey('global_styles', $derivedDoc);
        $this->assertEquals('#0F766E', $derivedDoc['global_styles']['colors']['primary']);
        $this->assertEquals('Poppins', $derivedDoc['global_styles']['typography']['heading_font']);
        $this->assertEquals(1440, $derivedDoc['global_styles']['layout']['container_max_width_px']);
    }

    /**
     * TEST 10: Publikasi Draf (Publish) memindahkan Global Styles dan Scoped Block Styles ke Live.
     */
    public function test_publish_moves_global_styles_and_scoped_blocks_to_live(): void
    {
        // Setup draft
        Pengaturan::setJson('draft_landing_global_styles', [
            'colors' => ['primary' => '#15803D'],
            'typography' => ['heading_font' => 'Montserrat'],
        ]);

        $customBlocks = [
            [
                'id' => 'block-hero-live',
                'type' => 'hero',
                'title' => 'Hero Live',
                'visible' => true,
                'order' => 1,
                'content' => ['title' => 'Hero Live Publik'],
                'styles' => ['padding_y' => 90],
                'settings' => ['html_anchor' => 'beranda'],
            ],
            [
                'id' => 'block-footer-live',
                'type' => 'footer',
                'title' => 'Footer Live',
                'visible' => true,
                'order' => 2,
                'content' => ['copyright' => 'Hak Cipta 2026'],
                'styles' => [],
            ],
        ];

        Pengaturan::setJson('draft_landing_blocks_layout', $customBlocks);
        Pengaturan::setJson('draft_landing_page_document', [
            'schema_version' => '1.1',
            'global_styles' => ['colors' => ['primary' => '#15803D']],
            'blocks' => $customBlocks,
        ]);
        Pengaturan::set('landing_draft_active', '1');

        // Publish
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.publish'));

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verifikasi di Pengaturan live
        $rawLive = Pengaturan::getLive('landing_page_document');
        $liveDoc = json_decode((string) $rawLive, true);
        $this->assertNotEmpty($liveDoc);
        $this->assertEquals('block-hero-live', $liveDoc['blocks'][0]['id']);
        $this->assertEquals(90, $liveDoc['blocks'][0]['styles']['padding_y']);
        $this->assertEquals('#15803D', $liveDoc['global_styles']['colors']['primary']);
    }

    /**
     * TEST 11: Hak Akses — Hanya SuperAdmin yang berwenang menyimpan dan mempublikasikan desain landing.
     */
    public function test_only_superadmin_can_modify_inspector_design_system(): void
    {
        $payload = [
            'landing_global_styles' => ['colors' => ['primary' => '#000000']],
        ];

        // Guest -> 401/302
        $guestResponse = $this->postJson(route('superadmin.site-editor.save-draft'), $payload);
        $guestResponse->assertStatus(401);

        // Admin -> 403 Forbidden
        auth()->logout();
        $adminResponse = $this->actingAs($this->admin)
            ->postJson(route('superadmin.site-editor.save-draft'), $payload);
        $adminResponse->assertStatus(403);

        // Peserta -> 403 Forbidden
        auth()->logout();
        $pesertaResponse = $this->actingAs($this->peserta)
            ->postJson(route('superadmin.site-editor.save-draft'), $payload);
        $pesertaResponse->assertStatus(403);
    }
}
