<?php

declare(strict_types=1);

namespace Tests\Feature\LandingPage;

use App\Models\Pengaturan as PengaturanModel;
use App\Models\User;
use App\Services\LandingPage\BlockDefinition;
use App\Services\LandingPage\BlockRegistry;
use App\Services\LandingPage\BlockSchemaValidator;
use App\Services\LandingPage\LandingPageDocumentService;
use App\Services\LandingPage\LandingPageRenderer;
use App\Services\LandingPage\LegacyCmsAdapter;
use App\Support\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CoreBlockEngineTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected BlockSchemaValidator $validator;

    protected LandingPageDocumentService $documentService;

    protected LandingPageRenderer $renderer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'role' => 'superadmin',
            'status_aktif' => true,
        ]);

        $this->validator = app(BlockSchemaValidator::class);
        $this->documentService = app(LandingPageDocumentService::class);
        $this->renderer = app(LandingPageRenderer::class);
        Pengaturan::setPreviewMode(false);
    }

    /**
     * TASK A: Block Registry Tests
     */
    public function test_block_registry_registers_all_official_blocks(): void
    {
        $expectedBlocks = [
            'hero',
            'informasi',
            'kuota',
            'tahapan',
            'faq',
            'cta_banner',
            'features',
            'spacer',
            'footer',
        ];

        foreach ($expectedBlocks as $type) {
            $this->assertTrue(BlockRegistry::has($type), "Block '{$type}' should be registered");
            $def = BlockRegistry::get($type);
            $this->assertInstanceOf(BlockDefinition::class, $def);
            $this->assertSame($type, $def->type);
            $this->assertNotEmpty($def->label);
            $this->assertNotEmpty($def->rendererView);
            $this->assertIsArray($def->defaultContent);
            $this->assertIsArray($def->defaultStyles);
        }

        $this->assertFalse(BlockRegistry::has('unknown_random_block'));
        $this->assertNull(BlockRegistry::get('unknown_random_block'));
    }

    public function test_block_registry_locked_and_repeatable_metadata(): void
    {
        // Hero & Footer harus locked dan non-repeatable
        $this->assertTrue(BlockRegistry::isLocked('hero'));
        $this->assertFalse(BlockRegistry::isRepeatable('hero'));

        $this->assertTrue(BlockRegistry::isLocked('footer'));
        $this->assertFalse(BlockRegistry::isRepeatable('footer'));

        // Features, Spacer, CTA Banner harus repeatable dan tidak locked
        $this->assertFalse(BlockRegistry::isLocked('features'));
        $this->assertTrue(BlockRegistry::isRepeatable('features'));

        $this->assertFalse(BlockRegistry::isLocked('spacer'));
        $this->assertTrue(BlockRegistry::isRepeatable('spacer'));
    }

    public function test_block_registry_exports_frontend_configuration(): void
    {
        $config = BlockRegistry::toFrontendConfig();

        $this->assertArrayHasKey('blocks', $config);
        $this->assertArrayHasKey('categories', $config);
        $this->assertArrayHasKey('schema_version', $config);
        $this->assertSame(1, $config['schema_version']);
        $this->assertArrayHasKey('hero', $config['blocks']);
        $this->assertSame('Hero Banner', $config['blocks']['hero']['label']);
    }

    /**
     * TASK B & C: Page Document Structure and Schema Validation Tests
     */
    public function test_page_document_default_structure_is_valid(): void
    {
        $doc = BlockRegistry::getDefaultDocument();

        $this->assertSame(1, $doc['schema_version']);
        $this->assertSame('Landing Page Portal Magang Kominfo Sleman', $doc['title']);
        $this->assertIsArray($doc['blocks']);
        $this->assertNotEmpty($doc['blocks']);

        // Pastikan setiap blok memiliki id unik, content, styles, settings, dan order
        $seenIds = [];
        foreach ($doc['blocks'] as $idx => $block) {
            $this->assertArrayHasKey('id', $block);
            $this->assertArrayHasKey('type', $block);
            $this->assertArrayHasKey('order', $block);
            $this->assertArrayHasKey('visible', $block);
            $this->assertArrayHasKey('content', $block);
            $this->assertArrayHasKey('styles', $block);
            $this->assertFalse(in_array($block['id'], $seenIds), "Duplicate block id {$block['id']}");
            $seenIds[] = $block['id'];
        }
    }

    public function test_schema_validator_enforces_locked_blocks(): void
    {
        $doc = BlockRegistry::getDefaultDocument();

        // Buang blok hero
        $docWithoutHero = $doc;
        $docWithoutHero['blocks'] = array_values(array_filter(
            $doc['blocks'],
            fn ($b) => $b['type'] !== 'hero'
        ));

        $this->expectException(ValidationException::class);
        $this->validator->validateDocument($docWithoutHero);
    }

    public function test_schema_validator_rejects_duplicate_block_ids(): void
    {
        $doc = BlockRegistry::getDefaultDocument();
        // Duplikasi ID blok pertama ke blok kedua
        $doc['blocks'][1]['id'] = $doc['blocks'][0]['id'];

        $this->expectException(ValidationException::class);
        $this->validator->validateDocument($doc);
    }

    public function test_schema_validator_sanitizes_xss_in_content(): void
    {
        $doc = BlockRegistry::getDefaultDocument();
        $doc['blocks'][0]['content']['title'] = 'Judul Bersih <script>alert("hack")</script>';

        $clean = $this->validator->validateDocument($doc);
        $cleanTitle = $clean['blocks'][0]['content']['title'];

        $this->assertStringNotContainsString('<script>', $cleanTitle);
        $this->assertStringNotContainsString('alert', $cleanTitle);
    }

    public function test_schema_validator_rejects_malicious_urls(): void
    {
        $doc = BlockRegistry::getDefaultDocument();
        $doc['blocks'][0]['content']['cta_primary_url'] = 'javascript:alert(1)';

        $this->expectException(ValidationException::class);
        $this->validator->validateDocument($doc);
    }

    public function test_two_repeatable_blocks_have_independent_content(): void
    {
        $doc = BlockRegistry::getDefaultDocument();
        $doc['blocks'][] = [
            'id' => 'block-features-custom-1',
            'type' => 'features',
            'order' => 6,
            'visible' => true,
            'content' => [
                'heading' => 'Fitur Khusus A',
                'subheading' => 'Deskripsi Khusus A',
                'features' => [],
            ],
            'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
            'settings' => [],
        ];
        $doc['blocks'][] = [
            'id' => 'block-features-custom-2',
            'type' => 'features',
            'order' => 7,
            'visible' => true,
            'content' => [
                'heading' => 'Fitur Khusus B',
                'subheading' => 'Deskripsi Khusus B',
                'features' => [],
            ],
            'styles' => ['desktop' => [], 'tablet' => [], 'mobile' => []],
            'settings' => [],
        ];

        $clean = $this->validator->validateDocument($doc);

        $features = array_values(array_filter($clean['blocks'], fn ($b) => $b['type'] === 'features'));
        $this->assertCount(2, $features);
        $this->assertSame('Fitur Khusus A', $features[0]['content']['heading']);
        $this->assertSame('Fitur Khusus B', $features[1]['content']['heading']);
    }

    /**
     * TASK D: Dynamic Block Renderer Tests
     */
    public function test_renderer_renders_all_default_blocks(): void
    {
        $doc = BlockRegistry::getDefaultDocument();
        $html = $this->renderer->renderDocument($doc);

        $this->assertNotEmpty($html);
        $this->assertStringContainsString('id="beranda"', $html);
        $this->assertStringContainsString('id="kuota"', $html);
        $this->assertStringContainsString('id="tahapan"', $html);
        $this->assertStringContainsString('id="faq"', $html);
        $this->assertStringContainsString('id="kontak"', $html);
    }

    public function test_renderer_omits_hidden_blocks_in_public_mode_but_renders_in_editor_mode(): void
    {
        $doc = BlockRegistry::getDefaultDocument();
        // Sembunyikan FAQ
        foreach ($doc['blocks'] as &$b) {
            if ($b['type'] === 'faq') {
                $b['visible'] = false;
            }
        }
        unset($b);

        // Public Mode: FAQ tidak dirender sama sekali
        $publicHtml = $this->renderer->renderDocument($doc, [], isEditorMode: false);
        $this->assertStringNotContainsString('id="faq"', $publicHtml);

        // Editor Mode: FAQ tetap dirender dengan style tersembunyi agar dapat diklik di kanvas editor
        $editorHtml = $this->renderer->renderDocument($doc, [], isEditorMode: true);
        $this->assertStringContainsString('id="faq"', $editorHtml);
        $this->assertStringContainsString('opacity: 0.35', $editorHtml);
    }

    public function test_renderer_catches_block_error_without_crashing_entire_page(): void
    {
        $doc = BlockRegistry::getDefaultDocument();
        // Sisipkan blok yang tidak memiliki view valid
        $doc['blocks'][] = [
            'id' => 'block-corrupt-test',
            'type' => 'unknown_invalid_type',
            'order' => 99,
            'visible' => true,
            'content' => [],
            'styles' => [],
            'settings' => [],
        ];

        // Me-render tidak boleh melempar exception fatal
        $html = $this->renderer->renderBlocks($doc['blocks']);
        $this->assertNotEmpty($html);
        $this->assertStringContainsString('id="beranda"', $html);
        $this->assertStringContainsString("Blok tipe 'unknown_invalid_type' tidak dikenal", $html);
    }

    /**
     * TASK E & F: Page Document Service & Staging Integration Tests
     */
    public function test_document_service_save_draft_publish_and_discard(): void
    {
        $defaultDoc = $this->documentService->getLiveDocument();
        $this->assertNotEmpty($defaultDoc['blocks']);

        // Ubah judul hero di draft
        $modifiedDoc = $defaultDoc;
        $modifiedDoc['blocks'][0]['content']['title'] = 'Judul Draf Berhasil Disimpan';

        $savedDraft = $this->documentService->saveDraftDocument($modifiedDoc, $this->superAdmin);
        $this->assertSame('Judul Draf Berhasil Disimpan', $savedDraft['blocks'][0]['content']['title']);
        $this->assertTrue($this->documentService->hasActiveDraft());

        // Verifikasi getDraftDocument mengembalikan draf
        $draftDoc = $this->documentService->getDraftDocument();
        $this->assertSame('Judul Draf Berhasil Disimpan', $draftDoc['blocks'][0]['content']['title']);

        // Verifikasi getLiveDocument MASIH versi awal (terisolasi sandbox)
        $liveDoc = $this->documentService->getLiveDocument();
        $this->assertNotSame('Judul Draf Berhasil Disimpan', $liveDoc['blocks'][0]['content']['title']);

        // Publikasikan
        $count = $this->documentService->publishDocument($this->superAdmin);
        $this->assertGreaterThan(0, $count);
        $this->assertFalse($this->documentService->hasActiveDraft());

        // Sekarang live document adalah versi terbaru
        $newLiveDoc = $this->documentService->getLiveDocument();
        $this->assertSame('Judul Draf Berhasil Disimpan', $newLiveDoc['blocks'][0]['content']['title']);
    }

    public function test_document_service_discard_draft(): void
    {
        $doc = $this->documentService->getLiveDocument();
        $doc['blocks'][0]['content']['title'] = 'Draf yang akan dibuang';

        $this->documentService->saveDraftDocument($doc, $this->superAdmin);
        $this->assertTrue($this->documentService->hasActiveDraft());

        $this->documentService->discardDraftDocument($this->superAdmin);
        $this->assertFalse($this->documentService->hasActiveDraft());

        $currentDraft = $this->documentService->getDraftDocument();
        $this->assertNotSame('Draf yang akan dibuang', $currentDraft['blocks'][0]['content']['title']);
    }

    /**
     * TASK G: Legacy CMS Adapter Tests
     */
    public function test_legacy_adapter_converts_legacy_settings_to_document(): void
    {
        Pengaturan::set('landing_hero_title', 'Hero Judul Warisan');
        Pengaturan::set('landing_hero_kicker', 'Kicker Sleman');
        Pengaturan::set('landing_kuota_heading', 'Alokasi Formasi Sleman');

        $adapter = app(LegacyCmsAdapter::class);
        $doc = $adapter->convertLegacyToDocument(draft: false);

        $this->assertSame(1, $doc['schema_version']);
        $hero = array_values(array_filter($doc['blocks'], fn ($b) => $b['type'] === 'hero'))[0];
        $this->assertSame('Hero Judul Warisan', $hero['content']['title']);
        $this->assertSame('Kicker Sleman', $hero['content']['kicker']);

        $kuota = array_values(array_filter($doc['blocks'], fn ($b) => $b['type'] === 'kuota'))[0];
        $this->assertSame('Alokasi Formasi Sleman', $kuota['content']['heading']);
    }

    public function test_legacy_adapter_syncs_document_back_to_legacy_keys(): void
    {
        $doc = BlockRegistry::getDefaultDocument();
        $doc['blocks'][0]['content']['title'] = 'Judul Baru dari Document';
        $doc['blocks'][0]['content']['kicker'] = 'Kicker Sleman Baru';

        $adapter = app(LegacyCmsAdapter::class);
        $adapter->syncLegacyKeysFromDocument($doc, draft: false);

        $this->assertSame('Judul Baru dari Document', Pengaturan::get('landing_hero_title'));
        $this->assertSame('Kicker Sleman Baru', Pengaturan::get('landing_hero_kicker'));
    }

    public function test_legacy_adapter_initial_migration_is_idempotent(): void
    {
        // Pastikan belum ada landing_page_document
        PengaturanModel::where('kunci', 'landing_page_document')->delete();

        $adapter = app(LegacyCmsAdapter::class);
        $this->assertTrue($adapter->isInitialMigrationNeeded());

        $firstRun = $adapter->runInitialMigration();
        $this->assertTrue($firstRun);
        $this->assertFalse($adapter->isInitialMigrationNeeded());

        // Jalankan lagi, harus return false (idempotent)
        $secondRun = $adapter->runInitialMigration();
        $this->assertFalse($secondRun);
    }
}
