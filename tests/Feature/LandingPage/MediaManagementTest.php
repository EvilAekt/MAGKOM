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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaManagementTest extends TestCase
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
        Storage::fake('public');

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
     * Test 1: Proteksi Otorisasi Endpoint Media (SuperAdmin Only)
     */
    public function test_authorization_protection_for_media_endpoints(): void
    {
        // 1. Guest ditolak (Redirect ke login atau 401)
        $this->getJson(route('superadmin.site-editor.media.index'))
            ->assertUnauthorized();

        // 2. Peserta ditolak dengan 403 Forbidden
        $this->actingAs($this->peserta)
            ->getJson(route('superadmin.site-editor.media.index'))
            ->assertForbidden();

        // 3. Admin instansi biasa ditolak dengan 403 Forbidden
        $this->actingAs($this->admin)
            ->getJson(route('superadmin.site-editor.media.index'))
            ->assertForbidden();

        // 4. SuperAdmin diizinkan (200 OK)
        $this->actingAs($this->superAdmin)
            ->getJson(route('superadmin.site-editor.media.index'))
            ->assertOk()
            ->assertJsonStructure(['success', 'media']);
    }

    /**
     * Test 2: Unggah Gambar Valid (JPG/PNG) dengan Ekstraksi Dimensi & Metadata
     */
    public function test_superadmin_can_upload_valid_image_with_dimensions_and_metadata(): void
    {
        $file = UploadedFile::fake()->image('banner_hero.png', 1280, 720);

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.media.upload'), [
                'file' => $file,
                'alt_text' => 'Gedung Diskominfo Sleman',
                'caption' => 'Kantor Utama Sleman',
                'title' => 'Banner Gedung',
                'description' => 'Foto dokumentasi tampak depan',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'item' => [
                    'original_name' => 'banner_hero.png',
                    'alt_text' => 'Gedung Diskominfo Sleman',
                    'caption' => 'Kantor Utama Sleman',
                    'title' => 'Banner Gedung',
                    'description' => 'Foto dokumentasi tampak depan',
                    'dimensions' => '1280 x 720',
                    'mime_type' => 'image/png',
                ],
            ]);

        $item = $response->json('item');
        $this->assertNotEmpty($item['path']);
        Storage::disk('public')->assertExists($item['path']);

        // Verifikasi tercatat di pengaturan landing_media_library
        $library = $this->stagingService->getMediaLibrary();
        $this->assertCount(1, $library);
        $this->assertEquals($item['id'], $library[0]['id']);

        // Verifikasi pencatatan Audit Log
        $latestLog = AuditLog::latest('id')->first();
        $this->assertNotNull($latestLog);
        $this->assertSame('pengaturan', $latestLog->entitas);
    }

    /**
     * Test 3: Unggah Dokumen PDF Valid dengan Magic Bytes %PDF-
     */
    public function test_superadmin_can_upload_valid_pdf_document(): void
    {
        // PDF valid dengan header magic bytes %PDF-1.4
        $pdfContent = "%PDF-1.4\n1 0 obj\n<< /Title (Buku Panduan Magang) >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF";
        $file = UploadedFile::fake()->createWithContent('buku_panduan_2026.pdf', $pdfContent);

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.media.upload'), [
                'file' => $file,
                'title' => 'Buku Panduan Magang 2026',
                'description' => 'Panduan lengkap tata cara pendaftaran',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'item' => [
                    'original_name' => 'buku_panduan_2026.pdf',
                    'mime_type' => 'application/pdf',
                    'title' => 'Buku Panduan Magang 2026',
                ],
            ]);

        $item = $response->json('item');
        Storage::disk('public')->assertExists($item['path']);
    }

    /**
     * Test 4: Penolakan Fake PDF (Magic Bytes Mismatch)
     */
    public function test_upload_rejects_fake_pdf_with_magic_bytes_mismatch(): void
    {
        // Berkas teks biasa yang diberi nama .pdf palsu
        $fakePdfContent = 'Halo ini teks biasa bukan PDF asli sama sekali.';
        $file = UploadedFile::fake()->createWithContent('fake_document.pdf', $fakePdfContent);

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.media.upload'), [
                'file' => $file,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    /**
     * Test 5: Penolakan Format Terlarang dan File Terlalu Besar (> 5 MB)
     */
    public function test_upload_rejects_disallowed_extensions_and_oversized_files(): void
    {
        // 1. Ekstensi terlarang (executable script .php)
        $phpFile = UploadedFile::fake()->createWithContent('exploit.php', '<?php echo "evil"; ?>');
        $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.media.upload'), ['file' => $phpFile])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['file']);

        // 2. Berkas melebihi batas 5 MB (5120 KB) -> buat 6000 KB
        $largeImage = UploadedFile::fake()->create('large_poster.png', 6000, 'image/png');
        $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.media.upload'), ['file' => $largeImage])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    /**
     * Test 6: Filter Jenis Media (Semua, Image, PDF) dan Pencarian
     */
    public function test_media_filtering_by_type_and_search(): void
    {
        // Buat 1 gambar dan 1 PDF
        $img = UploadedFile::fake()->image('kominfo_gedung.jpg', 800, 600);
        $this->stagingService->addMedia($img, ['alt_text' => 'Gedung Depan', 'title' => 'Foto Gedung']);

        $pdfContent = "%PDF-1.5\nContoh berkas panduan PDF valid\n%%EOF";
        $pdf = UploadedFile::fake()->createWithContent('panduan_teknis.pdf', $pdfContent);
        $this->stagingService->addMedia($pdf, ['title' => 'Panduan Teknis Sistem']);

        // 1. Ambil semua media (total 2)
        $all = $this->actingAs($this->superAdmin)
            ->getJson(route('superadmin.site-editor.media.index'))
            ->assertOk()
            ->json('media');
        $this->assertCount(2, $all);

        // 2. Filter hanya gambar
        $imagesOnly = $this->actingAs($this->superAdmin)
            ->getJson(route('superadmin.site-editor.media.index', ['type' => 'image']))
            ->assertOk()
            ->json('media');
        $this->assertCount(1, $imagesOnly);
        $this->assertStringStartsWith('image/', $imagesOnly[0]['mime_type']);

        // 3. Filter hanya PDF
        $pdfsOnly = $this->actingAs($this->superAdmin)
            ->getJson(route('superadmin.site-editor.media.index', ['type' => 'pdf']))
            ->assertOk()
            ->json('media');
        $this->assertCount(1, $pdfsOnly);
        $this->assertEquals('application/pdf', $pdfsOnly[0]['mime_type']);

        // 4. Pencarian kata kunci "teknis"
        $searchRes = $this->actingAs($this->superAdmin)
            ->getJson(route('superadmin.site-editor.media.index', ['search' => 'teknis']))
            ->assertOk()
            ->json('media');
        $this->assertCount(1, $searchRes);
        $this->assertEquals('Panduan Teknis Sistem', $searchRes[0]['title']);
    }

    /**
     * Test 7: Penghapusan Berkas Media Berhasil Menghapus Fisik Storage
     */
    public function test_delete_media_succeeds_and_removes_physical_file(): void
    {
        $img = UploadedFile::fake()->image('unused_photo.jpg', 500, 500);
        $item = $this->stagingService->addMedia($img);

        Storage::disk('public')->assertExists($item['path']);

        $response = $this->actingAs($this->superAdmin)
            ->deleteJson(route('superadmin.site-editor.media.delete', $item['id']));

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Berkas media berhasil dihapus dari sistem.',
            ]);

        // Berkas fisik di storage harus terhapus
        Storage::disk('public')->assertMissing($item['path']);

        // Pustaka media kosong
        $library = $this->stagingService->getMediaLibrary();
        $this->assertCount(0, $library);
    }

    /**
     * Test 12: Isolasi Perubahan Media antara Draf Sandbox dan Live Publik
     */
    public function test_draft_vs_live_media_isolation(): void
    {
        // 1. Set media live awal
        Pengaturan::set('landing_hero_image', 'landing/media/live_original.jpg');

        // 2. Unggah media draf baru
        $draftImg = UploadedFile::fake()->image('draft_banner.jpg', 1000, 500);
        $item = $this->stagingService->addMedia($draftImg);

        // 3. Simpan perubahan ke draf via saveSiteEditorDraft
        $this->stagingService->saveSiteEditorDraft([
            'landing_hero_image' => $item['path'],
        ], $this->superAdmin);

        // 4. Pastikan versi live publik TIDAK berubah sebelum dipublikasikan
        $this->assertEquals('landing/media/live_original.jpg', Pengaturan::getLive('landing_hero_image'));

        // 5. Versi draf sandbox memiliki gambar baru
        $this->assertEquals($item['path'], Pengaturan::getLive('draft_landing_hero_image'));

        // 6. Saat dipublikasikan, baru diterapkan ke live publik
        $this->stagingService->publishDraft($this->superAdmin);
        $this->assertEquals('landing/'.basename($item['path']), Pengaturan::getLive('landing_hero_image'));
    }
}
