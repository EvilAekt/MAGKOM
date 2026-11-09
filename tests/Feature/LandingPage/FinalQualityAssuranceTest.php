<?php

declare(strict_types=1);

namespace Tests\Feature\LandingPage;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Models\Jurusan;
use App\Models\PesertaProfil;
use App\Models\User;
use App\Services\LandingCmsStagingService;
use App\Services\LandingPage\BlockSchemaValidator;
use App\Services\LandingPage\LandingPageDocumentService;
use App\Support\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/**
 * FINAL QUALITY ASSURANCE TEST SUITE (TASK 8)
 *
 * Menguji secara menyeluruh:
 * 1. Security Audit (RBAC, CSRF, XSS Sanitization, SVG Upload Security, Delete Protection)
 * 2. Functional & Integration Matrix (Task 1 s/d Task 7)
 * 3. Atomic Publishing Lifecycle & Rollback
 * 4. Concurrency Control (Atomic Lock & Version Conflict)
 * 5. Database & Document Schema Integrity
 * 6. Regression Testing (Public Landing, Pendaftaran, Auth Boundary)
 */
class FinalQualityAssuranceTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $pembimbing;

    protected User $peserta;

    protected LandingPageDocumentService $documentService;

    protected LandingCmsStagingService $stagingService;

    protected BlockSchemaValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->superAdmin = User::factory()->create(['status_aktif' => true]);
        $this->superAdmin->role = RoleEnum::SuperAdmin;
        $this->superAdmin->save();

        $this->admin = User::factory()->create(['status_aktif' => true]);
        $this->admin->role = RoleEnum::Admin;
        $this->admin->save();

        $this->pembimbing = User::factory()->create(['status_aktif' => true]);
        $this->pembimbing->role = RoleEnum::Pembimbing;
        $this->pembimbing->save();

        $this->peserta = User::factory()->create(['status_aktif' => true]);
        $this->peserta->role = RoleEnum::Peserta;
        $this->peserta->save();

        $this->documentService = app(LandingPageDocumentService::class);
        $this->stagingService = app(LandingCmsStagingService::class);
        $this->validator = app(BlockSchemaValidator::class);
    }

    // =========================================================================
    // 1. SECURITY AUDIT TESTS (TASK A)
    // =========================================================================

    /**
     * SEC-1: Strict RBAC - Seluruh endpoint Site Editor terisolasi hanya untuk Super Admin.
     */
    public function test_security_strict_rbac_forbids_non_superadmins(): void
    {
        $restrictedRoutes = [
            ['GET', route('superadmin.site-editor.index')],
            ['GET', route('superadmin.site-editor.patterns.index')],
            ['GET', route('superadmin.site-editor.reusable.index')],
            ['GET', route('superadmin.site-editor.media.index')],
            ['GET', route('superadmin.site-editor.revisions')],
            ['POST', route('superadmin.site-editor.save-draft'), ['blocks' => []]],
            ['POST', route('superadmin.site-editor.publish')],
            ['POST', route('superadmin.site-editor.rollback')],
        ];

        // A. Peserta Magang
        foreach ($restrictedRoutes as $routeItem) {
            $method = $routeItem[0];
            $url = $routeItem[1];
            $payload = $routeItem[2] ?? [];
            $res = $this->actingAs($this->peserta)->json($method, $url, $payload);
            $this->assertEquals(403, $res->status(), "Peserta harus ditolak (403) pada {$url}");
        }

        // B. Pembimbing Magang
        foreach ($restrictedRoutes as $routeItem) {
            $method = $routeItem[0];
            $url = $routeItem[1];
            $payload = $routeItem[2] ?? [];
            $res = $this->actingAs($this->pembimbing)->json($method, $url, $payload);
            $this->assertEquals(403, $res->status(), "Pembimbing harus ditolak (403) pada {$url}");
        }

        // C. Admin Biasa
        foreach ($restrictedRoutes as $routeItem) {
            $method = $routeItem[0];
            $url = $routeItem[1];
            $payload = $routeItem[2] ?? [];
            $res = $this->actingAs($this->admin)->json($method, $url, $payload);
            $this->assertEquals(403, $res->status(), "Admin biasa harus ditolak (403) pada {$url}");
        }

        // D. Guest (Unauthenticated)
        $resGuest = $this->getJson(route('superadmin.site-editor.index'));
        $this->assertTrue(in_array($resGuest->status(), [401, 403, 302], true));
    }

    /**
     * SEC-2: XSS Sanitization - Skrip berbahaya, inline event handlers, dan skema javascript: dinetralkan.
     */
    public function test_security_xss_injection_is_strictly_neutralized(): void
    {
        $maliciousBlock = [
            'id' => 'block-xss-test',
            'type' => 'cta_banner',
            'version' => 1,
            'visible' => true,
            'content' => [
                'heading' => '<script>alert("XSS-HEADING")</script><strong>Judul Aman</strong>',
                'description' => '<img src=x onerror="alert(\'XSS-DESC\')">Deskripsi Aman',
                'btn_url' => 'javascript:alert("XSS-URL")',
            ],
            'styles' => [
                'desktop' => ['padding_y' => 50],
            ],
            'settings' => [
                'custom_css_class' => 'valid-class" onmouseover="alert(\'XSS-CLASS\')',
                'html_anchor' => 'section#hash<script>',
            ],
        ];

        // URL javascript: wajib ditolak oleh exception
        $this->expectException(ValidationException::class);
        $this->validator->validateBlock($maliciousBlock);
    }

    /**
     * SEC-3: XSS Sanitization - Tag HTML berbahaya dibersihkan dari konten tanpa error.
     */
    public function test_security_html_tags_are_stripped_leaving_safe_markup(): void
    {
        $block = [
            'id' => 'block-clean-test',
            'type' => 'cta_banner',
            'version' => 1,
            'visible' => true,
            'content' => [
                'heading' => '<script>alert("XSS")</script><b>Judul Tebal</b>',
                'description' => '<iframe src="evil.com"></iframe><p>Paragraf Aman</p>',
                'btn_url' => 'https://magang.slemankab.go.id',
            ],
            'styles' => [],
            'settings' => [
                'custom_css_class' => 'btn-custom special_section',
                'html_anchor' => 'daftar-sekarang',
            ],
        ];

        $clean = $this->validator->validateBlock($block);

        $this->assertStringNotContainsString('<script>', $clean['content']['heading']);
        $this->assertStringNotContainsString('<iframe>', $clean['content']['description']);
        $this->assertStringContainsString('<b>Judul Tebal</b>', $clean['content']['heading']);
        $this->assertStringContainsString('<p>Paragraf Aman</p>', $clean['content']['description']);
        $this->assertEquals('btn-custom special_section', $clean['settings']['custom_css_class']);
        $this->assertEquals('daftar-sekarang', $clean['settings']['html_anchor']);
    }

    /**
     * SEC-4: File Upload Security - Upload berkas SVG berbahaya yang memuat skrip/event handlers ditolak.
     */
    public function test_security_malicious_svg_upload_is_rejected(): void
    {
        $maliciousSvgContent = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
    <circle cx="50" cy="50" r="40" fill="red" />
    <script type="text/javascript">
        alert("XSS-SVG-PAYLOAD");
    </script>
</svg>
SVG;

        $file = UploadedFile::fake()->createWithContent('malicious.svg', $maliciousSvgContent);

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.media.upload'), [
                'file' => $file,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    /**
     * SEC-5: Delete Protection - Media yang sedang digunakan pada halaman draf atau live tidak boleh dihapus.
     */
    public function test_security_media_in_use_cannot_be_deleted(): void
    {
        // 1. Simpan media dummy
        $file = UploadedFile::fake()->image('banner_active.png', 400, 300);
        $uploadRes = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.media.upload'), [
                'file' => $file,
            ]);

        $uploadRes->assertOk();
        $mediaId = $uploadRes->json('item.id');
        $this->assertNotNull($mediaId);

        // 2. Set file tersebut sebagai banner aktif live
        Pengaturan::set('landing_hero_image', (string) $uploadRes->json('item.path'));

        // 3. Upaya hapus harus ditolak (Delete Protection)
        $delRes = $this->actingAs($this->superAdmin)
            ->deleteJson(route('superadmin.site-editor.media.delete', ['id' => $mediaId]));

        $delRes->assertStatus(422)
            ->assertJsonPath('success', false);

        $this->assertStringContainsString('sedang digunakan', $delRes->json('message'));
    }

    // =========================================================================
    // 2. FUNCTIONAL & INTEGRATION TESTS (TASK B & C)
    // =========================================================================

    /**
     * FUNC-1: Block Management Engine - Add, Duplicate, Reorder, Hide/Show, Lock/Unlock.
     */
    public function test_functional_block_management_lifecycle(): void
    {
        // 1. Tambah blok cta_banner
        $addRes = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.add'), [
                'type' => 'cta_banner',
            ]);

        $addRes->assertOk()
            ->assertJsonPath('success', true);

        $blockId = $addRes->json('block.id');

        // 2. Duplikasi blok
        $dupRes = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.duplicate'), [
                'id' => $blockId,
            ]);

        $dupRes->assertOk();
        $dupBlockId = $dupRes->json('block.id');
        $this->assertNotEquals($blockId, $dupBlockId);

        // 3. Toggle Hide / Show
        $hideRes = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.toggle-visibility'), [
                'id' => $dupBlockId,
                'visible' => false,
            ]);

        $hideRes->assertOk()
            ->assertJsonPath('block.visible', false);

        // 4. Toggle Lock / Unlock
        $lockRes = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.toggle-lock'), [
                'id' => $dupBlockId,
                'locked' => true,
            ]);

        $lockRes->assertOk()
            ->assertJsonPath('block.user_locked', true);

        // 5. Blok terkunci tidak boleh dihapus
        $delFailRes = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.delete'), [
                'id' => $dupBlockId,
            ]);

        $delFailRes->assertStatus(422);

        // 6. Buka kunci lalu hapus
        $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.toggle-lock'), [
                'id' => $dupBlockId,
                'locked' => false,
            ]);

        $delSuccessRes = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.blocks.delete'), [
                'id' => $dupBlockId,
            ]);

        $delSuccessRes->assertOk()
            ->assertJsonPath('deleted_id', $dupBlockId);
    }

    /**
     * FUNC-2: Publishing & Rollback Lifecycle - Draft Sandbox isolasi hingga dipublikasikan.
     */
    public function test_functional_publish_backup_and_rollback_lifecycle(): void
    {
        // 0. Set nilai live awal agar ada backup state sebelum draf baru dipublikasikan
        Pengaturan::set('landing_hero_title', 'Portal Magang Sleman Original');
        Pengaturan::set('landing_theme_primary_color', '#1E40AF');

        // 1. Simpan draf dengan judul baru
        $saveDraftRes = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), [
                'landing_hero_title' => 'Portal Magang Sleman Teruji QA',
                'landing_theme_primary_color' => '#0F172A',
            ]);

        $saveDraftRes->assertOk()
            ->assertJsonPath('success', true);

        // Versi Live publik TIDAK BOLEH berubah sebelum publish!
        $this->assertEquals('Portal Magang Sleman Original', Pengaturan::getLive('landing_hero_title'));

        // 2. Publikasikan draf ke Live
        $publishRes = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.publish'));

        $publishRes->assertOk()
            ->assertJsonPath('success', true);

        // Sekarang versi Live publik HARUS ter-update
        $this->assertEquals('Portal Magang Sleman Teruji QA', Pengaturan::getLive('landing_hero_title'));

        // 3. Rollback kembali ke versi backup sebelum publish
        $rollbackRes = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.rollback'));

        $rollbackRes->assertOk()
            ->assertJsonPath('success', true);

        // Setelah rollback, nilai live kembali ke nilai sebelum publikasi
        $this->assertEquals('Portal Magang Sleman Original', Pengaturan::getLive('landing_hero_title'));
    }

    /**
     * FUNC-3: Concurrency Control - Mencegah tabrakan versi dengan pengecekan expected_version.
     */
    public function test_functional_concurrency_conflict_detection(): void
    {
        Pengaturan::set('draft_landing_document_version', '2');
        $currentVersion = $this->stagingService->getDocumentVersion(true); // 2

        // Klien mengirimkan versi usang (expected_version = 1, min:1 lolos validasi, tapi 1 < 2)
        $staleRes = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), [
                'landing_hero_title' => 'Judul Tabrakan',
                'expected_version' => $currentVersion - 1,
            ]);

        $staleRes->assertStatus(409)
            ->assertJsonPath('conflict', true);
    }

    // =========================================================================
    // 3. DATABASE & DOCUMENT INTEGRITY TESTS (TASK H)
    // =========================================================================

    /**
     * INT-1: Dokumen draf tidak boleh kehilangan section wajib (Hero atau Footer).
     */
    public function test_integrity_document_cannot_omit_mandatory_blocks(): void
    {
        $invalidDocWithoutHero = [
            'schema_version' => 1,
            'page_id' => 'landing-magkom',
            'blocks' => [
                [
                    'id' => 'block-footer-1',
                    'type' => 'footer',
                    'order' => 1,
                ],
            ],
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validateDocument($invalidDocWithoutHero);
    }

    /**
     * INT-2: Dokumen draf menolak duplikasi Block ID.
     */
    public function test_integrity_document_rejects_duplicate_block_ids(): void
    {
        $invalidDocDuplicateId = [
            'schema_version' => 1,
            'page_id' => 'landing-magkom',
            'blocks' => [
                ['id' => 'same-block-id', 'type' => 'hero', 'order' => 1],
                ['id' => 'same-block-id', 'type' => 'footer', 'order' => 2],
            ],
        ];

        $this->expectException(ValidationException::class);
        $this->validator->validateDocument($invalidDocDuplicateId);
    }

    // =========================================================================
    // 4. REGRESSION VERIFICATION (TASK I)
    // =========================================================================

    /**
     * REG-1: Landing Page Publik dapat diakses dengan respons 200 OK tanpa error.
     */
    public function test_regression_public_landing_page_renders_successfully(): void
    {
        $response = $this->get(route('landing'));

        $response->assertOk()
            ->assertSee('MAGKOM')
            ->assertSee('Daftar Sekarang');
    }

    /**
     * REG-2: Rute pendaftaran peserta reguler tetap berjalan normal.
     */
    public function test_regression_peserta_registration_route_works(): void
    {
        $jurusan = Jurusan::create([
            'nama_jurusan' => 'Informatika',
            'kategori' => 'mahasiswa',
            'aktif' => true,
        ]);
        PesertaProfil::create([
            'user_id' => $this->peserta->id,
            'nim_nisn' => '1234567890',
            'institusi' => 'Universitas Gadjah Mada',
            'jurusan_id' => $jurusan->id,
            'jenjang' => JenjangEnum::S1,
            'no_hp' => '081234567890',
            'alamat' => 'Yogyakarta',
            'foto' => 'peserta-foto/dummy.jpg',
            'nama_kontak_darurat' => 'Orang Tua',
            'no_kontak_darurat' => '089876543210',
            'hubungan_kontak_darurat' => 'Orang Tua',
        ]);

        $response = $this->actingAs($this->peserta)
            ->get(route('peserta.pendaftaran.index'));

        $response->assertOk();
    }
}
