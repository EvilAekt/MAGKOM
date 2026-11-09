<?php

declare(strict_types=1);

namespace Tests\Feature\LandingPage;

use App\Enums\RoleEnum;
use App\Models\AuditLog;
use App\Models\Pengaturan as PengaturanModel;
use App\Models\User;
use App\Services\LandingPage\LandingPageDocumentService;
use App\Support\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class BlockManagementEngineTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $peserta;

    protected User $admin;

    protected LandingPageDocumentService $documentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'status_aktif' => true,
        ]);
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->save();

        $this->admin = User::factory()->create([
            'status_aktif' => true,
        ]);
        $this->admin->role = RoleEnum::Admin;
        $this->admin->save();

        $this->peserta = User::factory()->create([
            'status_aktif' => true,
        ]);
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->save();

        $this->documentService = app(LandingPageDocumentService::class);
        Pengaturan::setPreviewMode(false);
    }

    /**
     * Test 1: Inserter / Add Block Berhasil untuk Blok Repeatable
     */
    public function test_superadmin_can_add_repeatable_block_via_service_and_http(): void
    {
        // 1. Lewat Service
        $result = $this->documentService->addBlock('cta_banner', user: $this->superAdmin);

        $this->assertNotNull($result['block']);
        $this->assertSame('cta_banner', $result['block']['type']);
        $this->assertTrue($result['block']['visible']);
        $this->assertFalse($result['block']['user_locked']);
        $this->assertNotEmpty($result['block']['id']);

        // Verifikasi urutan sekuensial
        $blocks = $result['document']['blocks'];
        foreach ($blocks as $idx => $b) {
            $this->assertSame($idx + 1, $b['order']);
        }

        // 2. Lewat HTTP API
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.add'), [
                'type' => 'spacer',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Blok berhasil ditambahkan.',
            ])
            ->assertJsonPath('block.type', 'spacer');

        $this->assertSame('1', PengaturanModel::getNilai('landing_draft_active'));
    }

    /**
     * Test 2: Inserter / Add Block Menolak Blok Non-Repeatable Duplikat
     */
    public function test_add_block_rejects_duplicate_non_repeatable_blocks(): void
    {
        // Hero sudah ada di default document
        $this->expectException(ValidationException::class);
        $this->documentService->addBlock('hero', user: $this->superAdmin);
    }

    public function test_http_add_block_returns_422_for_duplicate_non_repeatable(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.add'), [
                'type' => 'footer',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    /**
     * Test 3: Inserter / Add Block Menolak Blok yang Tidak Terdaftar
     */
    public function test_add_block_rejects_unregistered_block_type(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.add'), [
                'type' => 'unregistered_alien_block',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    /**
     * Test 4: Duplicate Block Berhasil untuk Blok Repeatable
     */
    public function test_superadmin_can_duplicate_repeatable_block(): void
    {
        // Tambahkan satu blok cta_banner dulu
        $addResult = $this->documentService->addBlock('cta_banner', user: $this->superAdmin);
        $sourceId = $addResult['block']['id'];

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.duplicate'), [
                'id' => $sourceId,
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Blok berhasil diduplikasi.',
            ]);

        $duplicateBlock = $response->json('block');
        $this->assertNotSame($sourceId, $duplicateBlock['id']);
        $this->assertSame('cta_banner', $duplicateBlock['type']);
        $this->assertFalse($duplicateBlock['user_locked']);
    }

    /**
     * Test 5: Duplicate Block Menolak Duplikasi Blok Non-Repeatable / System Locked
     */
    public function test_duplicate_block_rejects_non_repeatable_or_system_locked(): void
    {
        $doc = $this->documentService->getDraftDocument();
        $heroBlock = $this->documentService->getBlocksByType($doc, 'hero')[0];

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.duplicate'), [
                'id' => $heroBlock['id'],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['block']);
    }

    /**
     * Test 6: Delete Block Berhasil untuk Blok Normal
     */
    public function test_superadmin_can_delete_unlocked_block(): void
    {
        $addResult = $this->documentService->addBlock('spacer', user: $this->superAdmin);
        $targetId = $addResult['block']['id'];

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.delete'), [
                'id' => $targetId,
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Blok berhasil dihapus.',
                'deleted_id' => $targetId,
            ]);

        $docAfter = $this->documentService->getDraftDocument();
        $this->assertNull($this->documentService->getBlockById($docAfter, $targetId));

        // Urutan blok tetap sekuensial kontinu 1..N
        foreach ($docAfter['blocks'] as $idx => $b) {
            $this->assertSame($idx + 1, $b['order']);
        }
    }

    /**
     * Test 7: Delete Block Menolak Penghapusan Blok Wajib Sistem (System Locked)
     */
    public function test_delete_block_rejects_system_locked_blocks(): void
    {
        $doc = $this->documentService->getDraftDocument();
        $heroBlock = $this->documentService->getBlocksByType($doc, 'hero')[0];
        $footerBlock = $this->documentService->getBlocksByType($doc, 'footer')[0];

        // 1. Tolak Hapus Hero
        $responseHero = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.delete'), [
                'id' => $heroBlock['id'],
            ]);
        $responseHero->assertStatus(422)->assertJsonValidationErrors(['block']);

        // 2. Tolak Hapus Footer
        $responseFooter = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.delete'), [
                'id' => $footerBlock['id'],
            ]);
        $responseFooter->assertStatus(422)->assertJsonValidationErrors(['block']);
    }

    /**
     * Test 8: Delete Block Menolak Penghapusan Blok yang Dikunci User (User Locked)
     */
    public function test_delete_block_rejects_user_locked_block(): void
    {
        $addResult = $this->documentService->addBlock('cta_banner', user: $this->superAdmin);
        $blockId = $addResult['block']['id'];

        // Kunci blok oleh user
        $this->documentService->toggleBlockLock($blockId, locked: true, user: $this->superAdmin);

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.delete'), [
                'id' => $blockId,
            ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['block']);

        // Buka kunci dan coba lagi -> harus berhasil
        $this->documentService->toggleBlockLock($blockId, locked: false, user: $this->superAdmin);
        $responseRetry = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.delete'), [
                'id' => $blockId,
            ]);
        $responseRetry->assertOk();
    }

    /**
     * Test 9: Susun Ulang Blok (Reorder & Move Up/Down)
     */
    public function test_superadmin_can_reorder_blocks(): void
    {
        $doc = $this->documentService->getDraftDocument();
        $initialBlocks = $doc['blocks'];
        $blockIds = array_column($initialBlocks, 'id');

        // Balikkan urutan 2 blok pertama yang bukan locked jika memungkinkan
        $reversedIds = $blockIds;
        $temp = $reversedIds[1];
        $reversedIds[1] = $reversedIds[2];
        $reversedIds[2] = $temp;

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.reorder'), [
                'order' => $reversedIds,
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Urutan blok berhasil diperbarui.',
            ]);

        $docAfter = $this->documentService->getDraftDocument();
        $this->assertSame($reversedIds[1], $docAfter['blocks'][1]['id']);
        $this->assertSame($reversedIds[2], $docAfter['blocks'][2]['id']);
        $this->assertSame(2, $docAfter['blocks'][1]['order']);
        $this->assertSame(3, $docAfter['blocks'][2]['order']);
    }

    /**
     * Test 10: Reorder Menolak Pemindahan Posisi Blok yang Sedang Terkunci (User Locked)
     */
    public function test_reorder_rejects_moving_user_locked_block(): void
    {
        $doc = $this->documentService->getDraftDocument();
        $targetBlock = $doc['blocks'][1];
        $targetId = $targetBlock['id'];

        // Kunci blok posisi 1
        $this->documentService->toggleBlockLock($targetId, locked: true, user: $this->superAdmin);

        $blockIds = array_column($doc['blocks'], 'id');
        $swappedIds = $blockIds;
        $swappedIds[1] = $blockIds[2];
        $swappedIds[2] = $blockIds[1];

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.reorder'), [
                'order' => $swappedIds,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['order']);
    }

    /**
     * Test 11: Toggle Visibilitas Blok (Hide / Show)
     */
    public function test_superadmin_can_toggle_block_visibility(): void
    {
        $doc = $this->documentService->getDraftDocument();
        $targetBlock = $doc['blocks'][1];
        $targetId = $targetBlock['id'];

        $this->assertTrue($targetBlock['visible']);

        // Toggle menjadi false
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.toggle-visibility'), [
                'id' => $targetId,
                'visible' => false,
            ]);

        $response->assertOk()
            ->assertJsonPath('block.visible', false);

        $docAfter = $this->documentService->getDraftDocument();
        $blockAfter = $this->documentService->getBlockById($docAfter, $targetId);
        $this->assertFalse($blockAfter['visible']);

        // Toggle kembali menjadi true
        $response2 = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.toggle-visibility'), [
                'id' => $targetId,
            ]);

        $response2->assertOk()
            ->assertJsonPath('block.visible', true);
    }

    /**
     * Test 12: Toggle Status Penguncian Blok (Lock / Unlock)
     */
    public function test_superadmin_can_toggle_user_lock_on_standard_blocks(): void
    {
        $doc = $this->documentService->getDraftDocument();
        $targetBlock = $doc['blocks'][1];
        $targetId = $targetBlock['id'];

        // Kunci blok
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.toggle-lock'), [
                'id' => $targetId,
                'locked' => true,
            ]);

        $response->assertOk()
            ->assertJsonPath('block.user_locked', true);

        // Buka kunci blok
        $response2 = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.toggle-lock'), [
                'id' => $targetId,
                'locked' => false,
            ]);

        $response2->assertOk()
            ->assertJsonPath('block.user_locked', false);
    }

    /**
     * Test 13: Toggle Lock Menolak Mengubah Status Kunci Blok Wajib Sistem
     */
    public function test_toggle_lock_rejects_system_locked_blocks(): void
    {
        $doc = $this->documentService->getDraftDocument();
        $heroBlock = $this->documentService->getBlocksByType($doc, 'hero')[0];

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.toggle-lock'), [
                'id' => $heroBlock['id'],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['block']);
    }

    /**
     * Test 14: Keamanan Otorisasi RBAC — Non-SuperAdmin Ditolak
     */
    public function test_non_superadmin_is_strictly_forbidden_from_block_operations(): void
    {
        $endpoints = [
            ['post', route('superadmin.site-editor.blocks.add'), ['type' => 'spacer']],
            ['post', route('superadmin.site-editor.blocks.duplicate'), ['id' => 'block-hero-1']],
            ['post', route('superadmin.site-editor.blocks.delete'), ['id' => 'block-informasi-1']],
            ['post', route('superadmin.site-editor.blocks.reorder'), ['order' => ['a', 'b']]],
            ['post', route('superadmin.site-editor.blocks.toggle-visibility'), ['id' => 'block-informasi-1']],
            ['post', route('superadmin.site-editor.blocks.toggle-lock'), ['id' => 'block-informasi-1']],
        ];

        foreach ($endpoints as [$method, $url, $payload]) {
            // Pastikan tidak ada user tersisa dari iterasi sebelumnya
            auth()->logout();

            // 1. Guest -> 401 Unauthorized
            $guestRes = $this->json($method, $url, $payload);
            $guestRes->assertUnauthorized();

            // 2. Peserta -> 403 Forbidden
            $pesertaRes = $this->actingAs($this->peserta)->json($method, $url, $payload);
            $pesertaRes->assertForbidden();

            // 3. Admin biasa -> 403 Forbidden
            $adminRes = $this->actingAs($this->admin)->json($method, $url, $payload);
            $adminRes->assertForbidden();
        }
    }

    /**
     * Test 15: Isolasi Draf — Operasi Blok Tidak Mengubah Halaman Live Sebelum Dipublikasikan
     */
    public function test_block_operations_remain_isolated_in_draft_until_published(): void
    {
        // 1. Simpan kondisi live awal
        $liveBefore = PengaturanModel::getNilai('landing_page_document');

        // 2. Tambah blok di draf
        $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.add'), [
                'type' => 'cta_banner',
            ]);

        // 3. Halaman live belum tersentuh
        $liveAfter = PengaturanModel::getNilai('landing_page_document');
        $this->assertSame($liveBefore, $liveAfter);

        // 4. Publikasikan draf ke live
        $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.publish'));

        // 5. Sekarang halaman live memiliki blok baru
        $livePublished = json_decode(PengaturanModel::getNilai('landing_page_document') ?? '{}', true);
        $this->assertNotEmpty($this->documentService->getBlocksByType($livePublished, 'cta_banner'));
    }

    /**
     * Test 16: Audit Trail Mencatat Setiap Operasi Blok
     */
    public function test_audit_trail_is_recorded_for_block_modifications(): void
    {
        $initialAuditCount = AuditLog::count();

        $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.add'), [
                'type' => 'features',
            ]);

        $this->assertGreaterThan($initialAuditCount, AuditLog::count());

        $latestLog = AuditLog::latest('id')->first();
        $this->assertNotNull($latestLog);
        $this->assertSame('pengaturan', $latestLog->entitas);
    }
}
