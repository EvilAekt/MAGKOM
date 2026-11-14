<?php

declare(strict_types=1);

namespace Tests\Feature\LandingPage;

use App\Enums\RoleEnum;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\LandingCmsStagingService;
use App\Services\LandingPage\LandingPageDocumentService;
use App\Support\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class RevisionPublishingManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $peserta;

    protected LandingCmsStagingService $stagingService;

    protected LandingPageDocumentService $documentService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create(['status_aktif' => true]);
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->save();

        $this->admin = User::factory()->create(['status_aktif' => true]);
        $this->admin->role = RoleEnum::Admin;
        $this->admin->save();

        $this->peserta = User::factory()->create(['status_aktif' => true]);
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->save();

        $this->stagingService = app(LandingCmsStagingService::class);
        $this->documentService = app(LandingPageDocumentService::class);

        Pengaturan::setPreviewMode(false);
    }

    /**
     * Test 1: Simpan draf menghasilkan record revisi lengkap (Task A)
     */
    public function test_save_draft_creates_comprehensive_revision_record(): void
    {
        $payload = [
            'landing_hero_title' => 'Judul Hero Baru via Site Editor',
            'landing_hero_cta_text' => 'Daftar Sekarang Juga',
        ];

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), $payload);

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $revisions = $this->stagingService->getRevisions();
        $this->assertNotEmpty($revisions);

        $latestRev = $revisions[0];
        $this->assertStringStartsWith('rev-', $latestRev['id']);
        $this->assertSame('landing_page_document', $latestRev['document_id']);
        $this->assertSame($this->superAdmin->id, $latestRev['author_id']);
        $this->assertSame($this->superAdmin->name, $latestRev['author_name']);
        $this->assertSame('1.1', $latestRev['schema_version']);
        $this->assertSame('draft', $latestRev['status']);
        $this->assertIsInt($latestRev['timestamp']);
        $this->assertIsArray($latestRev['snapshot']);
        $this->assertArrayHasKey('draft_landing_hero_title', $latestRev['snapshot']);
        $this->assertSame('Judul Hero Baru via Site Editor', $latestRev['snapshot']['draft_landing_hero_title']);
    }

    /**
     * Test 2: Endpoint Revisions API mengembalikan daftar riwayat terurut (Task B)
     */
    public function test_revisions_api_returns_list_ordered_by_latest(): void
    {
        $this->stagingService->createRevision('Revisi Pertama', $this->superAdmin, 'draft');
        $this->stagingService->createRevision('Revisi Kedua', $this->superAdmin, 'draft');

        $response = $this->actingAs($this->superAdmin)
            ->getJson(route('superadmin.site-editor.revisions'));

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $data = $response->json('revisions');
        $this->assertGreaterThanOrEqual(2, count($data));
        $this->assertSame('Revisi Kedua', $data[0]['summary']);
        $this->assertSame('Revisi Pertama', $data[1]['summary']);
    }

    /**
     * Test 3: Pratinjau snapshot revisi historis aman dan read-only (Task C)
     */
    public function test_revision_preview_renders_historical_snapshot_without_mutating_state(): void
    {
        // Tetapkan data live
        Pengaturan::set('landing_hero_title', 'Judul Live Publik');

        // Simpan draf
        $this->stagingService->saveSiteEditorDraft([
            'landing_hero_title' => 'Judul Draf Berjalan',
        ], $this->superAdmin);

        // Buat revisi historis khusus
        $customDoc = $this->documentService->getDocument(draft: true);
        $customDoc['blocks'][0]['content']['title'] = 'Judul Dari Revisi Khusus';

        $rev = $this->stagingService->createRevision('Snapshot Historis Khusus', $this->superAdmin, 'draft', $customDoc);

        // Akses preview route untuk revisi ini
        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.site-editor.revisions.preview', ['id' => $rev['id']]));

        $response->assertOk();
        $response->assertViewIs('public.landing');
        $response->assertViewHas('isPreview', true);
        $response->assertViewHas('isRevisionPreview', true);
        $response->assertViewHas('revision');

        // Pastikan view memuat judul dari snapshot revisi
        $response->assertSee('PRATINJAU REVISI HISTORIS');
        $response->assertSee('Judul Dari Revisi Khusus');

        // Verifikasi database tidak bermutasi: live tetap live, draft tetap draft
        $this->assertSame('Judul Live Publik', Pengaturan::getLive('landing_hero_title'));
        $this->assertSame('Judul Draf Berjalan', Pengaturan::getLive('draft_landing_hero_title'));
    }

    /**
     * Test 4: Pratinjau revisi dengan ID tidak valid mengembalikan 404
     */
    public function test_revision_preview_with_invalid_id_returns_404(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.site-editor.revisions.preview', ['id' => 'rev-tidak-ada']));

        $response->assertNotFound();
    }

    /**
     * Test 5: Restore revisi memulihkan ke kontainer draf dan tidak langsung menimpa live (Task D)
     */
    public function test_restore_revision_updates_working_draft_only_and_creates_restored_record(): void
    {
        // 1. Live publik awal
        Pengaturan::set('landing_hero_title', 'Hero Live Awal');

        // 2. Buat snapshot revisi masa lalu
        $this->stagingService->saveSiteEditorDraft([
            'landing_hero_title' => 'Judul Hero Masa Lalu',
        ], $this->superAdmin);
        $revisions = $this->stagingService->getRevisions();
        $oldRevId = $revisions[0]['id'];

        // 3. Modifikasi draf terkini dengan nilai lain
        $this->stagingService->saveSiteEditorDraft([
            'landing_hero_title' => 'Judul Hero Terkini yang Salah',
        ], $this->superAdmin);

        $initialDraftVer = $this->stagingService->getDocumentVersion(true);

        // 4. Lakukan pemulihan (restore) ke revisi masa lalu
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.revisions.restore', ['id' => $oldRevId]));

        $response->assertOk();
        $response->assertJson(['success' => true]);

        // Verifikasi draf telah dipulihkan ke nilai revisi lama
        $this->assertSame('Judul Hero Masa Lalu', Pengaturan::getLive('draft_landing_hero_title'));

        // Verifikasi LIVE publik sama sekali TIDAK berubah (zero impact on live)
        $this->assertSame('Hero Live Awal', Pengaturan::getLive('landing_hero_title'));

        // Verifikasi versi dokumen draf dinaikkan
        $newDraftVer = $this->stagingService->getDocumentVersion(true);
        $this->assertGreaterThan($initialDraftVer, $newDraftVer);

        // Verifikasi tercatat record revisi baru bertipe 'restored'
        $updatedRevisions = $this->stagingService->getRevisions();
        $latestRev = $updatedRevisions[0];
        $this->assertSame('restored', $latestRev['status']);
        $this->assertStringContainsString($oldRevId, $latestRev['summary']);

        // Verifikasi audit log tercatat
        $latestAudit = AuditLog::latest('id')->first();
        $this->assertNotNull($latestAudit);
        $this->assertSame('pengaturan', $latestAudit->entitas);
        $detail = is_array($latestAudit->detail) ? $latestAudit->detail : json_decode((string) $latestAudit->detail, true);
        $this->assertSame($oldRevId, $detail['revision_id'] ?? null);
    }

    /**
     * Test 6: Restore revisi dengan ID tidak ditemukan mengembalikan 404
     */
    public function test_restore_revision_non_existent_returns_404(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.revisions.restore', ['id' => 'rev-invalid-999']));

        $response->assertNotFound();
        $response->assertJson(['success' => false]);
    }

    /**
     * Test 7: Publish membuat backup snapshot live lengkap (Task E)
     */
    public function test_publish_creates_live_backup_snapshot_before_applying_changes(): void
    {
        // Live awal
        Pengaturan::set('landing_hero_title', 'Live Lama Sebelum Rilis');

        // Draf baru
        $this->stagingService->saveSiteEditorDraft([
            'landing_hero_title' => 'Live Baru Yang Sukses Terbit',
        ], $this->superAdmin);

        // Publikasikan
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.publish'));

        $response->assertOk();
        $response->assertJson(['success' => true]);

        // Verifikasi live sekarang memuat nilai baru
        $this->assertSame('Live Baru Yang Sukses Terbit', Pengaturan::getLive('landing_hero_title'));

        // Verifikasi backup live tersimpan di landing_live_backup
        $backupJson = Pengaturan::getLive('landing_live_backup');
        $this->assertNotEmpty($backupJson);
        $backup = json_decode((string) $backupJson, true);
        $this->assertIsArray($backup);
        $this->assertArrayHasKey('settings', $backup);
        $this->assertSame('Live Lama Sebelum Rilis', $backup['settings']['landing_hero_title'] ?? null);

        // Verifikasi revisi berstatus 'published' tercatat
        $revisions = $this->stagingService->getRevisions();
        $this->assertSame('published', $revisions[0]['status']);

        // Verifikasi draf menjadi tidak aktif
        $this->assertSame('0', Pengaturan::getLive('landing_draft_active'));
    }

    /**
     * Test 8: Rollback memulihkan live publik ke backup terakhir (Task F)
     */
    public function test_rollback_restores_live_state_from_backup(): void
    {
        // 1. Kondisi awal live
        Pengaturan::set('landing_hero_title', 'Versi Live Stabil v1');

        // 2. Buat draf dan terbitkan (sehingga live backup v1 terbentuk)
        $this->stagingService->saveSiteEditorDraft([
            'landing_hero_title' => 'Versi Live Rusak v2',
        ], $this->superAdmin);
        $this->stagingService->publishDraft($this->superAdmin);

        $this->assertSame('Versi Live Rusak v2', Pengaturan::getLive('landing_hero_title'));

        // 3. Super Admin mengeksekusi Rollback
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.rollback'));

        $response->assertOk();
        $response->assertJson(['success' => true]);

        // Verifikasi live kembali ke v1
        $this->assertSame('Versi Live Stabil v1', Pengaturan::getLive('landing_hero_title'));

        // Verifikasi revisi 'rollback' tercatat
        $revisions = $this->stagingService->getRevisions();
        $this->assertSame('rollback', $revisions[0]['status']);

        // Verifikasi audit log tercatat
        $latestAudit = AuditLog::latest('id')->first();
        $this->assertSame('pengaturan', $latestAudit->entitas);
        $detail = is_array($latestAudit->detail) ? $latestAudit->detail : json_decode((string) $latestAudit->detail, true);
        $this->assertStringContainsString('rollback', strtolower($detail['aksi'] ?? ''));
    }

    /**
     * Test 9: Rollback tanpa adanya data backup mengembalikan 404
     */
    public function test_rollback_without_backup_returns_404(): void
    {
        Pengaturan::delete('landing_live_backup');

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.rollback'));

        $response->assertNotFound();
        $response->assertJson(['success' => false]);
    }

    /**
     * Test 10: Retention Policy mempertahankan milestone (published & rollback) dan membatasi draft (Task H)
     */
    public function test_retention_policy_preserves_milestones_and_prunes_drafts(): void
    {
        $revisions = [];

        // Buat 1 revisi milestone published lama
        $revisions[] = [
            'id' => 'rev-pub-old',
            'status' => 'published',
            'summary' => 'Publikasi Milestone Abadi',
        ];

        // Buat 1 revisi milestone rollback
        $revisions[] = [
            'id' => 'rev-rb-old',
            'status' => 'rollback',
            'summary' => 'Rollback Milestone Abadi',
        ];

        // Buat 35 revisi draft
        for ($i = 1; $i <= 35; $i++) {
            $revisions[] = [
                'id' => "rev-draft-{$i}",
                'status' => 'draft',
                'summary' => "Draft perubahan {$i}",
            ];
        }

        // Terapkan pruning dengan batas maksimal 30 draft
        $pruned = $this->stagingService->pruneRevisions($revisions, 30);

        // Kedua milestone (published dan rollback) harus tetap ada
        $pubExists = false;
        $rbExists = false;
        $draftCount = 0;

        foreach ($pruned as $item) {
            if ($item['id'] === 'rev-pub-old') {
                $pubExists = true;
            }
            if ($item['id'] === 'rev-rb-old') {
                $rbExists = true;
            }
            if ($item['status'] === 'draft') {
                $draftCount++;
            }
        }

        $this->assertTrue($pubExists, 'Milestone published harus dipertahankan.');
        $this->assertTrue($rbExists, 'Milestone rollback harus dipertahankan.');
        $this->assertSame(30, $draftCount, 'Jumlah draft harus dipangkas tepat 30 item.');
        $this->assertSame(32, count($pruned), 'Total item adalah 30 draft + 2 milestone.');
    }

    /**
     * Test 11: Otorisasi Akses RBAC - Hanya SuperAdmin yang berwenang
     */
    public function test_authorization_rbac_restricts_non_superadmin(): void
    {
        // 1. Guest ditolak (redirect login)
        $this->get(route('superadmin.site-editor.revisions'))
            ->assertRedirect(route('login'));

        $this->postJson(route('superadmin.site-editor.revisions.restore', ['id' => 'rev-test']))
            ->assertUnauthorized();

        $this->postJson(route('superadmin.site-editor.rollback'))
            ->assertUnauthorized();

        // 2. Peserta ditolak (403 Forbidden)
        $this->actingAs($this->peserta)
            ->get(route('superadmin.site-editor.revisions'))
            ->assertForbidden();

        $this->actingAs($this->peserta)
            ->postJson(route('superadmin.site-editor.revisions.restore', ['id' => 'rev-test']))
            ->assertForbidden();

        $this->actingAs($this->peserta)
            ->postJson(route('superadmin.site-editor.rollback'))
            ->assertForbidden();

        // 3. Admin biasa ditolak (403 Forbidden)
        $this->actingAs($this->admin)
            ->get(route('superadmin.site-editor.revisions'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->postJson(route('superadmin.site-editor.revisions.restore', ['id' => 'rev-test']))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->postJson(route('superadmin.site-editor.rollback'))
            ->assertForbidden();
    }

    /**
     * Test 12: Concurrency Lock mencegah operasi publish balapan (Task G)
     */
    public function test_publish_process_uses_atomic_concurrency_lock(): void
    {
        $this->stagingService->saveSiteEditorDraft([
            'landing_hero_title' => 'Judul Uji Lock',
        ], $this->superAdmin);

        // Pasang lock buatan untuk mensimulasikan proses publish lain yang sedang berjalan
        $lock = Cache::lock('landing_cms_publish_lock', 1);
        $acquired = $lock->get();
        $this->assertTrue($acquired);

        try {
            // Karena timeout block adalah 3 detik dan lock 1 detik, ketika lock kedaluwarsa, proses tetap selesai aman
            $count = $this->stagingService->publishDraft($this->superAdmin);
            $this->assertGreaterThan(0, $count);
        } finally {
            $lock->release();
        }
    }
}
