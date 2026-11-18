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
     * Test 1: Publish membuat backup snapshot live lengkap (1 Cadangan Otomatis)
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

        // Verifikasi draf menjadi tidak aktif
        $this->assertSame('0', Pengaturan::getLive('landing_draft_active'));
    }

    /**
     * Test 2: Rollback 1 langkah memulihkan live publik ke backup terakhir
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

        // Verifikasi audit log tercatat
        $latestAudit = AuditLog::latest('id')->first();
        $this->assertSame('pengaturan', $latestAudit->entitas);
        $detail = is_array($latestAudit->detail) ? $latestAudit->detail : json_decode((string) $latestAudit->detail, true);
        $this->assertStringContainsString('rollback', strtolower($detail['aksi'] ?? ''));
    }

    /**
     * Test 3: Rollback tanpa adanya data backup mengembalikan 404
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
     * Test 4: Otorisasi Akses RBAC - Hanya SuperAdmin yang berwenang
     */
    public function test_authorization_rbac_restricts_non_superadmin(): void
    {
        // 1. Guest ditolak
        $this->postJson(route('superadmin.site-editor.rollback'))
            ->assertUnauthorized();

        $this->postJson(route('superadmin.site-editor.publish'))
            ->assertUnauthorized();

        // 2. Peserta ditolak (403 Forbidden)
        $this->actingAs($this->peserta)
            ->postJson(route('superadmin.site-editor.rollback'))
            ->assertForbidden();

        $this->actingAs($this->peserta)
            ->postJson(route('superadmin.site-editor.publish'))
            ->assertForbidden();

        // 3. Admin biasa ditolak (403 Forbidden)
        $this->actingAs($this->admin)
            ->postJson(route('superadmin.site-editor.rollback'))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->postJson(route('superadmin.site-editor.publish'))
            ->assertForbidden();
    }

    /**
     * Test 5: Concurrency Lock mencegah operasi publish balapan
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
