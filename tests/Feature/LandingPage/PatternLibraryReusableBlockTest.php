<?php

declare(strict_types=1);

namespace Tests\Feature\LandingPage;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\LandingPage\LandingPageDocumentService;
use App\Services\LandingPage\LandingPageRenderer;
use App\Services\LandingPage\PatternRegistry;
use App\Services\LandingPage\ReusableBlockService;
use App\Support\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * Feature Test Suite: TASK 7 — PATTERN LIBRARY & REUSABLE BLOCKS (P1)
 *
 * Menguji seluruh kapabilitas:
 * 1. Pattern Library (16 Pola Sistem, 4 Kategori, Schema 1.1)
 * 2. Pattern Preview & Filter Kategori
 * 3. Pattern Insertion & Deep Clone (ID Unik & Independensi Blok)
 * 4. Custom Pattern Management (Pembuatan & Proteksi Pola Sistem)
 * 5. Reusable Blocks Engine (Pembuatan & Sinkronisasi Bersama / Shared State)
 * 6. Reusable Blocks Renderer Hydration
 * 7. Usage Tracking & Delete Protection
 * 8. Detach / Convert Reusable to Regular Block
 * 9. Circular Reference Prevention
 * 10. RBAC Authorization (Super Admin Only)
 */
class PatternLibraryReusableBlockTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $peserta;

    protected User $adminBiasa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'status_aktif' => true,
        ]);
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->save();

        $this->peserta = User::factory()->create([
            'status_aktif' => true,
        ]);
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->save();

        $this->adminBiasa = User::factory()->create([
            'status_aktif' => true,
        ]);
        $this->adminBiasa->role = RoleEnum::Admin;
        $this->adminBiasa->save();
    }

    /**
     * TEST 1: Memverifikasi 16 Pola Sistem resmi terdaftar dengan 4 kategori lengkap.
     */
    public function test_system_patterns_contain_16_patterns_across_4_categories(): void
    {
        $categories = PatternRegistry::getCategories();
        $this->assertEqualsCanonicalizing(['Hero', 'Informasi', 'FAQ', 'Timeline'], $categories);

        $systemPatterns = PatternRegistry::getSystemPatterns();
        $this->assertCount(16, $systemPatterns);

        // Pastikan masing-masing kategori memiliki pola
        $heroPatterns = PatternRegistry::getByCategory('Hero');
        $infoPatterns = PatternRegistry::getByCategory('Informasi');
        $faqPatterns = PatternRegistry::getByCategory('FAQ');
        $timelinePatterns = PatternRegistry::getByCategory('Timeline');

        $this->assertGreaterThanOrEqual(2, count($heroPatterns));
        $this->assertGreaterThanOrEqual(2, count($infoPatterns));
        $this->assertGreaterThanOrEqual(2, count($faqPatterns));
        $this->assertGreaterThanOrEqual(2, count($timelinePatterns));

        // Pastikan atribut pola sesuai schema 1.1
        foreach ($systemPatterns as $pat) {
            $this->assertArrayHasKey('id', $pat);
            $this->assertArrayHasKey('key', $pat);
            $this->assertArrayHasKey('name', $pat);
            $this->assertArrayHasKey('description', $pat);
            $this->assertArrayHasKey('category', $pat);
            $this->assertArrayHasKey('blocks', $pat);
            $this->assertEquals('1.1', $pat['schema_version']);
            $this->assertTrue($pat['is_system']);
            $this->assertNotEmpty($pat['blocks']);
        }
    }

    /**
     * TEST 2: Endpoint API daftar pattern dan filter kategori via AJAX.
     */
    public function test_api_can_list_and_filter_patterns(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->getJson(route('superadmin.site-editor.patterns.index'));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(16, 'patterns');

        // Filter kategori FAQ
        $faqResponse = $this->actingAs($this->superAdmin)
            ->getJson(route('superadmin.site-editor.patterns.index', ['category' => 'FAQ']));

        $faqResponse->assertOk()
            ->assertJsonPath('success', true);

        $faqPatterns = $faqResponse->json('patterns');
        $this->assertNotEmpty($faqPatterns);
        foreach ($faqPatterns as $item) {
            $this->assertEquals('FAQ', $item['category']);
        }
    }

    /**
     * TEST 3: Endpoint API pratinjau pola (Pattern Preview).
     */
    public function test_api_can_preview_pattern(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->getJson(route('superadmin.site-editor.patterns.preview', ['key' => 'hero_split_right']));

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('pattern.key', 'hero_split_right')
            ->assertJsonPath('pattern.category', 'Hero');
    }

    /**
     * TEST 4: Menyisipkan Pola menghasilkan Block Instance dengan ID unik baru (Deep Clone).
     */
    public function test_inserting_pattern_generates_unique_block_ids_and_clones_content(): void
    {
        // 1. Sisipkan pola 'faq_simple_accordion'
        $response1 = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.patterns.insert'), [
                'key' => 'faq_simple_accordion',
            ]);

        $response1->assertOk()
            ->assertJsonPath('success', true);

        $insertedBlocks1 = $response1->json('inserted_blocks');
        $this->assertNotEmpty($insertedBlocks1);
        $firstBlockId = $insertedBlocks1[0]['id'];
        $this->assertStringStartsWith('block-faq-', $firstBlockId);

        // 2. Sisipkan pola yang sama untuk kedua kalinya
        $response2 = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.patterns.insert'), [
                'key' => 'faq_simple_accordion',
            ]);

        $response2->assertOk();
        $insertedBlocks2 = $response2->json('inserted_blocks');
        $secondBlockId = $insertedBlocks2[0]['id'];

        // ID kedua instansiasi harus berbeda (100% independen)
        $this->assertNotEquals($firstBlockId, $secondBlockId);

        // Dokumen draf harus memuat kedua blok secara berurutan
        $doc = app(LandingPageDocumentService::class)->getDraftDocument();
        $blockIds = array_column($doc['blocks'], 'id');
        $this->assertContains($firstBlockId, $blockIds);
        $this->assertContains($secondBlockId, $blockIds);
    }

    /**
     * TEST 5: Pembuatan dan penghapusan Custom Pattern.
     */
    public function test_custom_pattern_lifecycle_and_system_protection(): void
    {
        // 1. Buat Custom Pattern baru
        $saveResponse = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.patterns.save-custom'), [
                'name' => 'Section Pengumuman Hasil Seleksi',
                'category' => 'Informasi',
                'description' => 'Pola khusus menampilkan jadwal rilis dan countdown pengumuman.',
                'blocks' => [
                    [
                        'type' => 'informasi',
                        'title' => 'Pengumuman Seleksi',
                        'content' => [
                            'persyaratan_label' => 'Jadwal Pengumuman',
                            'persyaratan_text' => 'Hasil seleksi diumumkan pada 25 Oktober 2026.',
                        ],
                    ],
                ],
            ]);

        $saveResponse->assertOk()
            ->assertJsonPath('success', true);

        $customPat = $saveResponse->json('pattern');
        $customId = $customPat['id'];
        $this->assertFalse($customPat['is_system']);

        // 2. Pastikan muncul di daftar seluruh pattern
        $all = PatternRegistry::getAll();
        $this->assertArrayHasKey($customPat['key'], $all);

        // 3. Sistem tidak boleh mengizinkan penghapusan pola sistem (System Pattern Protected)
        $delSystemResponse = $this->actingAs($this->superAdmin)
            ->deleteJson(route('superadmin.site-editor.patterns.delete-custom', ['id' => 'pattern-hero-split-right']));

        $delSystemResponse->assertStatus(422)
            ->assertJsonPath('success', false);

        // 4. Hapus Custom Pattern yang telah dibuat
        $delCustomResponse = $this->actingAs($this->superAdmin)
            ->deleteJson(route('superadmin.site-editor.patterns.delete-custom', ['id' => $customId]));

        $delCustomResponse->assertOk()
            ->assertJsonPath('success', true);

        $allAfter = PatternRegistry::getAll();
        $this->assertArrayNotHasKey($customPat['key'], $allAfter);
    }

    /**
     * TEST 6: Reusable Block Lifecycle (Create, Insert, Shared Sync, and Render).
     */
    public function test_reusable_block_creation_insertion_and_shared_rendering_sync(): void
    {
        // 1. Buat Reusable Block baru
        $createResponse = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.reusable.create'), [
                'title' => 'Banner Kontak Darurat Helpdesk',
                'category' => 'CTA',
                'block_type' => 'cta_banner',
                'content' => [
                    'heading' => 'Kontak Helpdesk Magang Sleman',
                    'description' => 'Hubungi via WhatsApp di nomor 0812-3456-7890.',
                    'btn_text' => 'Hubungi Sekarang',
                    'btn_url' => 'https://wa.me/6281234567890',
                ],
                'styles' => [
                    'desktop' => ['padding_y' => 60],
                ],
            ]);

        $createResponse->assertOk()
            ->assertJsonPath('success', true);

        $reusableData = $createResponse->json('reusable_block');
        $reusableId = $reusableData['id'];

        // 2. Sisipkan Reusable Block ke dalam dokumen draf
        $insertResponse = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.reusable.insert'), [
                'reusable_id' => $reusableId,
            ]);

        $insertResponse->assertOk()
            ->assertJsonPath('success', true);

        $insertedBlock = $insertResponse->json('block');
        $this->assertEquals($reusableId, $insertedBlock['reusable_id']);
        $instanceBlockId = $insertedBlock['id'];

        // 3. Render dokumen: Pastikan teks awal dari Reusable Block ter-render
        $renderer = app(LandingPageRenderer::class);
        $renderedHtml1 = $renderer->renderBlock($insertedBlock, isEditorMode: false);
        $this->assertStringContainsString('Kontak Helpdesk Magang Sleman', $renderedHtml1);
        $this->assertStringContainsString('0812-3456-7890', $renderedHtml1);

        // 4. Update Reusable Block secara sentral (misal nomor WhatsApp berganti)
        $updateResponse = $this->actingAs($this->superAdmin)
            ->putJson(route('superadmin.site-editor.reusable.update', ['id' => $reusableId]), [
                'title' => 'Banner Kontak Darurat Helpdesk (Diperbarui)',
                'content' => [
                    'heading' => 'Pusat Pengaduan & Informasi Magang',
                    'description' => 'Hubungi Call Center Sleman di 1500-123.',
                    'btn_text' => 'Hubungi Call Center',
                    'btn_url' => 'tel:1500123',
                ],
            ]);

        $updateResponse->assertOk()
            ->assertJsonPath('success', true);

        // 5. Render ulang instance blok tadi: konten baru HARUS otomatis tersinkronisasi tanpa edit instance!
        $renderedHtml2 = $renderer->renderBlock($insertedBlock, isEditorMode: false);
        $this->assertStringContainsString('Pusat Pengaduan', $renderedHtml2);
        $this->assertStringContainsString('1500-123', $renderedHtml2);
        $this->assertStringNotContainsString('Kontak Helpdesk Magang Sleman', $renderedHtml2);
    }

    /**
     * TEST 7: Reusable Block Usage Tracking dan Delete Protection.
     */
    public function test_reusable_block_usage_tracking_and_delete_protection(): void
    {
        $reusableService = app(ReusableBlockService::class);
        $docService = app(LandingPageDocumentService::class);

        // 1. Buat Reusable Block
        $newRb = $reusableService->create([
            'title' => 'Section Berkas Wajib',
            'block_type' => 'informasi',
            'content' => ['persyaratan_label' => 'Syarat Berkas'],
        ], $this->superAdmin);

        $reusableId = $newRb['id'];

        // Sebelum disisipkan, usage_count harus 0
        $all1 = $reusableService->getAll(withUsage: true);
        $item1 = collect($all1)->firstWhere('id', $reusableId);
        $this->assertEquals(0, $item1['usage_count']);
        $this->assertFalse($item1['is_in_use']);

        // 2. Sisipkan ke draf
        $docService->insertReusableBlock($reusableId, user: $this->superAdmin);

        // Setelah disisipkan, usage_count harus 1
        $all2 = $reusableService->getAll(withUsage: true);
        $item2 = collect($all2)->firstWhere('id', $reusableId);
        $this->assertEquals(1, $item2['usage_count']);
        $this->assertTrue($item2['is_in_use']);
        $this->assertEquals('draft', $item2['usages'][0]['scope']);

        // 3. Hapus Reusable Block: Harus DITOLAK oleh Delete Protection!
        $delResponse = $this->actingAs($this->superAdmin)
            ->deleteJson(route('superadmin.site-editor.reusable.delete', ['id' => $reusableId]));

        $delResponse->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertStringContainsString('masih digunakan', $delResponse->json('message'));

        // Pastikan masih tersimpan di DB pengaturan
        $this->assertNotNull($reusableService->getById($reusableId));
    }

    /**
     * TEST 8: Detach Reusable Block (Konversi ke Blok Reguler Mandiri).
     */
    public function test_detach_reusable_block_converts_it_to_regular_block(): void
    {
        $reusableService = app(ReusableBlockService::class);
        $docService = app(LandingPageDocumentService::class);

        $newRb = $reusableService->create([
            'title' => 'Blok Pengumuman Sementara',
            'block_type' => 'cta_banner',
            'content' => ['title' => 'Pengumuman Libur Nasional'],
        ], $this->superAdmin);

        $inserted = $docService->insertReusableBlock($newRb['id'], user: $this->superAdmin);
        $blockId = $inserted['block']['id'];

        // Pastikan awalnya memiliki reusable_id
        $this->assertEquals($newRb['id'], $inserted['block']['reusable_id']);

        // Detach Reusable Block via API
        $detachResponse = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.reusable.detach', ['blockId' => $blockId]));

        $detachResponse->assertOk()
            ->assertJsonPath('success', true);

        $updatedBlock = $detachResponse->json('block');
        $this->assertArrayNotHasKey('reusable_id', $updatedBlock);
        $this->assertEquals('Pengumuman Libur Nasional', $updatedBlock['content']['title']);

        // Sekarang usage_count kembali menjadi 0
        $usages = $reusableService->getUsage($newRb['id']);
        $this->assertEmpty($usages);

        // Sekarang Reusable Block dapat dihapus dengan sukses
        $delResponse = $this->actingAs($this->superAdmin)
            ->deleteJson(route('superadmin.site-editor.reusable.delete', ['id' => $newRb['id']]));

        $delResponse->assertOk()
            ->assertJsonPath('success', true);
    }

    /**
     * TEST 9: Pencegahan Circular Reference pada Reusable Block.
     */
    public function test_circular_reference_prevention(): void
    {
        $reusableService = app(ReusableBlockService::class);

        // 1. Buat Reusable Block awal
        $newRb = $reusableService->create([
            'title' => 'Blok Referensi Mandiri',
            'block_type' => 'cta_banner',
            'content' => ['title' => 'Konten Normal'],
        ], $this->superAdmin);

        $this->expectException(ValidationException::class);

        // 2. Simulasi update konten yang memuat ID dirinya sendiri
        $reusableService->update($newRb['id'], [
            'content' => [
                'nested_reusable' => $newRb['id'],
            ],
        ], $this->superAdmin);
    }

    /**
     * TEST 10: RBAC Authorization — Hanya Super Admin yang berhak mengakses Site Editor.
     */
    public function test_rbac_non_superadmin_forbidden_from_pattern_and_reusable_routes(): void
    {
        // 1. Peserta
        $this->actingAs($this->peserta)
            ->getJson(route('superadmin.site-editor.patterns.index'))
            ->assertForbidden();

        $this->actingAs($this->peserta)
            ->postJson(route('superadmin.site-editor.patterns.insert'), ['key' => 'hero_split_right'])
            ->assertForbidden();

        $this->actingAs($this->peserta)
            ->getJson(route('superadmin.site-editor.reusable.index'))
            ->assertForbidden();

        // 2. Admin Biasa (Bukan Super Admin)
        $this->actingAs($this->adminBiasa)
            ->getJson(route('superadmin.site-editor.patterns.index'))
            ->assertForbidden();

        $this->actingAs($this->adminBiasa)
            ->getJson(route('superadmin.site-editor.reusable.index'))
            ->assertForbidden();

        // 3. Tamu Publik (Unauthenticated)
        $unauthRes = $this->getJson(route('superadmin.site-editor.patterns.index'));
        $this->assertTrue(in_array($unauthRes->status(), [401, 403, 302], true));
    }
}
