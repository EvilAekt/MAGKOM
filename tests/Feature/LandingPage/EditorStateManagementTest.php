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
use Tests\TestCase;

class EditorStateManagementTest extends TestCase
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
     * Test 1: Inisialisasi Editor State Menyertakan Document Version
     */
    public function test_editor_index_provides_document_version(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.site-editor.index'));

        $response->assertOk();
        $response->assertViewHas('documentVersion');
        $this->assertGreaterThanOrEqual(1, $response->viewData('documentVersion'));
    }

    /**
     * Test 2: Save Draft Menaikkan Document Version secara Atomic
     */
    public function test_save_draft_increments_document_version(): void
    {
        $initialVer = $this->stagingService->getDocumentVersion(true);

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), [
                'expected_version' => $initialVer,
                'landing_hero_title' => 'Judul Hero Baru yang Telah Diedit',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'document_version' => $initialVer + 1,
            ]);

        $this->assertEquals($initialVer + 1, $this->stagingService->getDocumentVersion(true));
        $this->assertEquals('Judul Hero Baru yang Telah Diedit', Pengaturan::getLive('draft_landing_hero_title'));
    }

    /**
     * Test 3: Concurrency Protection Mendeteksi Konflik Versi Dokumen (HTTP 409)
     */
    public function test_save_draft_detects_version_conflict_when_server_is_newer(): void
    {
        // 1. Simpan perubahan pertama sehingga versi server naik
        $initialVer = $this->stagingService->getDocumentVersion(true);
        $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), [
                'expected_version' => $initialVer,
                'landing_hero_title' => 'Perubahan Pertama di Tab A',
            ])->assertOk();

        $newerVersion = $this->stagingService->getDocumentVersion(true);
        $this->assertEquals($initialVer + 1, $newerVersion);

        // 2. Tab B mencoba menyimpan dengan expected_version lama (stale version)
        $conflictResponse = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), [
                'expected_version' => $initialVer, // Versi lama
                'landing_hero_title' => 'Perubahan Bersamaan di Tab B',
            ]);

        $conflictResponse->assertStatus(409)
            ->assertJson([
                'success' => false,
                'conflict' => true,
                'server_version' => $newerVersion,
                'client_version' => $initialVer,
            ]);

        // Nilai di server tetap perubahan pertama
        $this->assertEquals('Perubahan Pertama di Tab A', Pengaturan::getLive('draft_landing_hero_title'));
    }

    /**
     * Test 4: Force Save Mengabaikan Konflik Versi jika Diinstruksikan Eksplisit
     */
    public function test_force_save_overrides_version_conflict(): void
    {
        $initialVer = $this->stagingService->getDocumentVersion(true);

        // Simpan versi 1 -> 2
        $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), [
                'expected_version' => $initialVer,
                'landing_hero_title' => 'Versi 2',
            ])->assertOk();

        // Simpan paksa dengan force: true dan expected_version lama
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), [
                'expected_version' => $initialVer,
                'force' => true,
                'landing_hero_title' => 'Dipaksa Simpan Versi 3',
            ]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertEquals('Dipaksa Simpan Versi 3', Pengaturan::getLive('draft_landing_hero_title'));
    }

    /**
     * Test 5: Autosave Tidak Mempengaruhi Versi Live Publik
     */
    public function test_autosave_does_not_alter_public_live_settings(): void
    {
        Pengaturan::set('landing_hero_title', 'Judul Live Publik Stabil');

        // Lakukan autosave perubahan ke draf
        $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), [
                'landing_hero_title' => 'Judul Draf Baru (Autosave)',
            ])->assertOk();

        // Pastikan live publik tetap tidak berubah
        $this->assertEquals('Judul Live Publik Stabil', Pengaturan::getLive('landing_hero_title'));
        $this->assertEquals('Judul Draf Baru (Autosave)', Pengaturan::getLive('draft_landing_hero_title'));
        $this->assertEquals('1', Pengaturan::getLive('landing_draft_active'));
    }

    /**
     * Test 6: Audit Trail Mencatat Versi Dokumen pada Setiap Penyimpanan
     */
    public function test_audit_trail_records_document_version_on_save(): void
    {
        $initialAuditCount = AuditLog::count();

        $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), [
                'landing_hero_title' => 'Pengujian Audit Trail Versioning',
            ])->assertOk();

        $this->assertGreaterThan($initialAuditCount, AuditLog::count());

        $latestLog = AuditLog::latest('id')->first();
        $this->assertNotNull($latestLog);
        $this->assertSame('pengaturan', $latestLog->entitas);

        $detail = is_array($latestLog->detail) ? $latestLog->detail : json_decode((string) $latestLog->detail, true);
        $this->assertArrayHasKey('document_version', $detail);
    }

    /**
     * Test 7: Otorisasi Endpoint Save Draft (Hanya SuperAdmin)
     */
    public function test_save_draft_authorization_guards(): void
    {
        // 1. Guest ditolak
        $this->postJson(route('superadmin.site-editor.save-draft'), ['landing_hero_title' => 'Hack'])
            ->assertUnauthorized();

        // 2. Peserta ditolak (403)
        $this->actingAs($this->peserta)
            ->postJson(route('superadmin.site-editor.save-draft'), ['landing_hero_title' => 'Hack'])
            ->assertForbidden();

        // 3. Admin biasa ditolak (403)
        $this->actingAs($this->admin)
            ->postJson(route('superadmin.site-editor.save-draft'), ['landing_hero_title' => 'Hack'])
            ->assertForbidden();
    }
}
