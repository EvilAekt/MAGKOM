<?php

declare(strict_types=1);

namespace Tests\Feature\SuperAdmin;

use App\Enums\RoleEnum;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LandingCmsSiteEditorTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
        ]);

        $this->admin = User::factory()->create([
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
        ]);

        $this->peserta = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);
    }

    public function test_superadmin_can_access_wordpress_site_editor(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.site-editor.index'));

        $response->assertStatus(200);
        $response->assertSee('WordPress Site Editor');
        $response->assertSee('wp-preview-iframe');
        $response->assertSee('wp-site-inspector');
        $response->assertSee('Tipografi &amp; Perataan Teks', false);
        $response->assertSee('Batas &amp; Warna Blok', false);
    }

    public function test_unauthorized_users_cannot_access_site_editor(): void
    {
        // Guest redirect to login
        $guestResponse = $this->get(route('superadmin.site-editor.index'));
        $guestResponse->assertRedirect(route('login'));

        // Admin 403
        $adminResponse = $this->actingAs($this->admin)
            ->get(route('superadmin.site-editor.index'));
        $adminResponse->assertStatus(403);

        // Peserta 403
        $pesertaResponse = $this->actingAs($this->peserta)
            ->get(route('superadmin.site-editor.index'));
        $pesertaResponse->assertStatus(403);
    }

    public function test_superadmin_can_save_draft_with_pixel_dimensions_via_ajax(): void
    {
        $payload = [
            'landing_container_max_width_px' => 1360,
            'landing_hero_title_size_px' => 56,
            'landing_hero_desc_size_px' => 18,
            'landing_hero_padding_y_px' => 90,
            'landing_hero_image_height_px' => 450,
            'landing_section_gap_px' => 70,
            'landing_card_radius_px' => 16,
            'landing_card_padding_px' => 28,
            'landing_faq_title_size_px' => 20,
            'landing_footer_padding_y_px' => 75,
            'landing_hero_title' => 'Judul Baru dari Gutenberg Canvas',
            'landing_theme_primary_color' => '#0F172A',
        ];

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Pastikan tersimpan dengan prefix draft_
        $this->assertEquals(56, (int) Pengaturan::getNilai('draft_landing_hero_title_size_px'));
        $this->assertEquals(1360, (int) Pengaturan::getNilai('draft_landing_container_max_width_px'));
        $this->assertEquals('Judul Baru dari Gutenberg Canvas', Pengaturan::getNilai('draft_landing_hero_title'));
        $this->assertEquals('#0F172A', Pengaturan::getNilai('draft_landing_theme_primary_color'));
        $this->assertEquals('1', Pengaturan::getNilai('landing_draft_active'));

        // Pastikan versi live belum berubah
        $this->assertNotEquals('Judul Baru dari Gutenberg Canvas', Pengaturan::getNilai('landing_hero_title'));
    }

    public function test_save_draft_rejects_out_of_range_pixel_values(): void
    {
        $invalidPayload = [
            'landing_hero_title_size_px' => 999, // Max 72
            'landing_container_max_width_px' => 200, // Min 960
        ];

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), $invalidPayload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'landing_hero_title_size_px',
            'landing_container_max_width_px',
        ]);
    }

    public function test_superadmin_can_publish_site_editor_draft(): void
    {
        // Siapkan draf
        Pengaturan::setNilai('draft_landing_hero_title', 'Judul Terbit WordPress');
        Pengaturan::setNilai('draft_landing_hero_title_size_px', '60');
        Pengaturan::setNilai('landing_draft_active', '1');

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.publish'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Verifikasi terbit ke live
        $this->assertEquals('Judul Terbit WordPress', Pengaturan::getNilai('landing_hero_title'));
        $this->assertEquals('60', Pengaturan::getNilai('landing_hero_title_size_px'));
        $this->assertEquals('0', Pengaturan::getNilai('landing_draft_active'));
    }

    public function test_superadmin_can_discard_site_editor_draft(): void
    {
        Pengaturan::setNilai('draft_landing_hero_title', 'Draf Yang Akan Dibuang');
        Pengaturan::setNilai('landing_draft_active', '1');

        $response = $this->actingAs($this->superAdmin)
            ->deleteJson(route('superadmin.site-editor.discard'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertNull(Pengaturan::getNilai('draft_landing_hero_title'));
        $this->assertEquals('0', Pengaturan::getNilai('landing_draft_active'));
    }

    public function test_landing_page_renders_dynamic_pixel_css_variables_and_editor_runtime_when_requested(): void
    {
        Pengaturan::setNilai('landing_hero_title_size_px', '52');
        Pengaturan::setNilai('landing_container_max_width_px', '1400');

        $response = $this->get('/?editor_mode=1');

        $response->assertStatus(200);
        $response->assertSee('--wp-hero-title-font-size: 52px;', false);
        $response->assertSee('--wp-container-max-w: 1400px;', false);
        $response->assertSee('landing-canvas-runtime');
        $response->assertSee('data-wp-block="hero"', false);
        $response->assertSee('data-wp-block="kuota"', false);
        $response->assertSee('data-wp-block="tahapan"', false);
        $response->assertSee('data-wp-block="faq"', false);
    }

    public function test_superadmin_can_save_blocks_layout_and_reorder_via_ajax(): void
    {
        $blocksLayout = [
            ['id' => 'hero', 'type' => 'hero', 'name' => 'Hero Banner', 'order' => 1, 'visible' => true, 'is_required' => true],
            ['id' => 'faq', 'type' => 'faq', 'name' => 'Pertanyaan Umum (FAQ)', 'order' => 2, 'visible' => true, 'is_required' => false],
            ['id' => 'kuota', 'type' => 'kuota', 'name' => 'Ketersediaan Kuota', 'order' => 3, 'visible' => false, 'is_required' => false],
            ['id' => 'footer', 'type' => 'footer', 'name' => 'Footer & Kontak', 'order' => 4, 'visible' => true, 'is_required' => true],
        ];

        $payload = [
            'landing_blocks_layout' => $blocksLayout,
        ];

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verifikasi draft layout tersimpan
        $savedDraftLayout = json_decode(Pengaturan::getNilai('draft_landing_blocks_layout') ?? '[]', true);
        $this->assertCount(4, $savedDraftLayout);
        $this->assertEquals('faq', $savedDraftLayout[1]['id']);
        $this->assertEquals(2, $savedDraftLayout[1]['order']);
        $this->assertFalse($savedDraftLayout[2]['visible']);

        // Verifikasi publish layout
        $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.publish'))
            ->assertStatus(200);

        $publishedLayout = json_decode(Pengaturan::getNilai('landing_blocks_layout') ?? '[]', true);
        $this->assertEquals('faq', $publishedLayout[1]['id']);
    }

    public function test_superadmin_publish_creates_backup_and_supports_rollback(): void
    {
        // Kondisi awal live
        Pengaturan::setNilai('landing_hero_title', 'Versi Live Asli');

        // Buat draf baru
        Pengaturan::setNilai('draft_landing_hero_title', 'Versi Live Kedua');
        Pengaturan::setNilai('landing_draft_active', '1');

        // Publikasikan draf -> otomatis backup versi lama
        $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.publish'))
            ->assertStatus(200);

        $this->assertEquals('Versi Live Kedua', Pengaturan::getNilai('landing_hero_title'));

        // Rollback ke versi sebelumnya
        $rollbackResponse = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.rollback'));

        $rollbackResponse->assertStatus(200);
        $rollbackResponse->assertJson(['success' => true]);

        // Verifikasi kembali ke 'Versi Live Asli'
        $this->assertEquals('Versi Live Asli', Pengaturan::getNilai('landing_hero_title'));
    }

    public function test_superadmin_media_library_upload_browse_and_delete(): void
    {
        Storage::fake('public');

        // 1. Upload berkas baru
        $fakeImage = UploadedFile::fake()->image('banner-sle.png', 600, 400);

        $uploadResponse = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.media.upload'), [
                'file' => $fakeImage,
            ]);

        $uploadResponse->assertStatus(200);
        $uploadResponse->assertJson(['success' => true]);
        $mediaItem = $uploadResponse->json('item');
        $this->assertNotNull($mediaItem['id']);

        // 2. Browse media
        $listResponse = $this->actingAs($this->superAdmin)
            ->getJson(route('superadmin.site-editor.media.index'));

        $listResponse->assertStatus(200);
        $this->assertCount(1, $listResponse->json('media'));

        // 3. Delete media
        $deleteSuccess = $this->actingAs($this->superAdmin)
            ->deleteJson(route('superadmin.site-editor.media.delete', ['id' => $mediaItem['id']]));

        $deleteSuccess->assertStatus(200);
        $deleteSuccess->assertJson(['success' => true]);
    }

    public function test_superadmin_can_save_and_publish_banner_image_from_media_library_without_path_corruption(): void
    {
        Storage::fake('public');

        // 1. Upload media image
        $fakeImage = UploadedFile::fake()->image('banner-iso-sleman.jpg', 800, 400);
        $uploadResponse = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.media.upload'), [
                'file' => $fakeImage,
            ]);
        $uploadResponse->assertStatus(200);
        $mediaPath = $uploadResponse->json('item.path');

        // 2. Simpan draf dengan landing_hero_image dan judul baru
        $saveDraftResponse = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), [
                'landing_hero_image' => $mediaPath,
                'landing_hero_title' => "Program Magang\nKominfo Sleman",
                'landing_hero_kicker' => 'Pemerintah Kabupaten Sleman',
            ]);
        $saveDraftResponse->assertStatus(200);

        // Pastikan draf tersimpan di database
        $this->assertEquals($mediaPath, Pengaturan::getNilai('draft_landing_hero_image'));
        $this->assertEquals("Program Magang\nKominfo Sleman", Pengaturan::getNilai('draft_landing_hero_title'));

        // 3. Publikasikan draf ke versi live
        $publishResponse = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.publish'));
        $publishResponse->assertStatus(200);

        // Verifikasi file fisik tidak terhapus / berpindah
        $this->assertTrue(Storage::disk('public')->exists($mediaPath));
        $this->assertTrue(Storage::disk('public')->exists('landing/'.basename($mediaPath)));
        $this->assertEquals('landing/'.basename($mediaPath), Pengaturan::getNilai('landing_hero_image'));
        $this->assertEquals("Program Magang\nKominfo Sleman", Pengaturan::getNilai('landing_hero_title'));

        // 4. Verifikasi halaman publik landing me-render teks judul dan tidak fallback ke default jika diisi Program Magang Kominfo Sleman
        $publicLanding = $this->get(route('landing'));
        $publicLanding->assertStatus(200);
        $publicLanding->assertSee('Pemerintah Kabupaten Sleman');
        $publicLanding->assertSee('Program Magang');
        $publicLanding->assertSee('Kominfo Sleman');
        $publicLanding->assertDontSee('Periode II');
    }

    public function test_superadmin_can_update_hero_persyaratan_and_dokumen_content_and_publish_to_live(): void
    {
        $labelPersyaratan = 'Kriteria Pendaftar';
        $persyaratanUmum = "Mahasiswa aktif jenjang D3/D4/S1\nMemiliki laptop pribadi untuk operasional\nBersedia magang selama 3 bulan";
        $labelDokumen = 'Berkas Pendaftaran';
        $dokumenPendaftaran = "Surat Pengantar Resmi Dekan\nCurriculum Vitae Terbaru\nTranskrip Kumulatif\nPas Foto Resmi";

        // 1. Simpan draf via endpoint
        $saveResponse = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), [
                'landing_label_persyaratan' => $labelPersyaratan,
                'landing_persyaratan_umum' => $persyaratanUmum,
                'landing_label_dokumen' => $labelDokumen,
                'landing_dokumen_pendaftaran' => $dokumenPendaftaran,
            ]);

        $saveResponse->assertStatus(200);
        $saveResponse->assertJson(['success' => true]);

        // Verifikasi draf tersimpan di database
        $this->assertEquals($labelPersyaratan, Pengaturan::getNilai('draft_landing_label_persyaratan'));
        $this->assertEquals($persyaratanUmum, Pengaturan::getNilai('draft_landing_persyaratan_umum'));
        $this->assertEquals($labelDokumen, Pengaturan::getNilai('draft_landing_label_dokumen'));
        $this->assertEquals($dokumenPendaftaran, Pengaturan::getNilai('draft_landing_dokumen_pendaftaran'));

        // 2. Terbitkan ke versi live
        $publishResponse = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.publish'));

        $publishResponse->assertStatus(200);
        $publishResponse->assertJson(['success' => true]);

        // Verifikasi nilai live di database
        $this->assertEquals($labelPersyaratan, Pengaturan::getNilai('landing_label_persyaratan'));
        $this->assertEquals($persyaratanUmum, Pengaturan::getNilai('landing_persyaratan_umum'));
        $this->assertEquals($labelDokumen, Pengaturan::getNilai('landing_label_dokumen'));
        $this->assertEquals($dokumenPendaftaran, Pengaturan::getNilai('landing_dokumen_pendaftaran'));

        // 3. Verifikasi halaman publik landing me-render konten persyaratan dan dokumen baru
        $publicResponse = $this->get(route('landing'));
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee('Kriteria');
        $publicResponse->assertSee('Pendaftar');
        $publicResponse->assertSee('Mahasiswa aktif jenjang D3/D4/S1');
        $publicResponse->assertSee('Memiliki laptop pribadi untuk operasional');
        $publicResponse->assertSee('Berkas');
        $publicResponse->assertSee('Pendaftaran');
        $publicResponse->assertSee('Surat Pengantar Resmi Dekan');
        $publicResponse->assertSee('Pas Foto Resmi');
    }

    public function test_superadmin_site_editor_topbar_is_clean_and_renders_floating_notification_container(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.site-editor.index'));

        $response->assertStatus(200);

        // Pastikan tombol-tombol yang diminta dihapus tidak ada di markup
        $response->assertDontSee('id="btn-undo"', false);
        $response->assertDontSee('id="btn-redo"', false);
        $response->assertDontSee('id="btn-list-view"', false);
        $response->assertDontSee('wp-workspace-badge', false);

        // Pastikan dropdown halaman tetap ada
        $response->assertSee('id="select-canvas-page"', false);
        $response->assertSee('Landing Page (/)');
        $response->assertSee('Kebijakan Privasi (/kebijakan-privasi)');

        // Pastikan kontainer floating top notification bar ada
        $response->assertSee('id="wp-top-notification-bar"', false);

        // Pastikan ketentuan ukuran banner resmi ada di inspector
        $response->assertSee('Ketentuan &amp; Rekomendasi Banner', false);
        $response->assertSee('1920 × 600 px', false);
        $response->assertSee('id="btn-add-hero-slide"', false);
    }

    public function test_superadmin_can_save_and_publish_multi_slide_hero_banner_dynamically(): void
    {
        $slidesPayload = [
            [
                'id' => 'slide-1',
                'image' => 'landing/media/banner1.jpg',
                'badge' => 'DOKUMENTASI RESMI',
                'title' => 'Magang Sleman Hebat 2026',
                'description' => 'Pengembangan ekosistem digital SPBE terpadu bersama Diskominfo Sleman.',
                'cta_text' => 'Daftar Sekarang',
                'cta_url' => '#kuota',
            ],
            [
                'id' => 'slide-2',
                'image' => 'landing/media/banner2.jpg',
                'badge' => 'WORKSHOP DIGITAL',
                'title' => 'Pembekalan Mentor ASN Profesional',
                'description' => 'Peserta magang mendapatkan bimbingan intensif langsung dalam proyek kedinasan riil.',
                'cta_text' => 'Pelajari Alur',
                'cta_url' => '#tahapan',
            ],
            [
                'id' => 'slide-3',
                'image' => 'landing/media/banner3.jpg',
                'badge' => 'STUDI LAPANGAN',
                'title' => 'Kunjungan Infrastruktur Jaringan',
                'description' => 'Pengenalan pusat data dan topologi jaringan fiber optic Pemkab Sleman.',
                'cta_text' => 'Lihat Formasi',
                'cta_url' => '#kuota',
            ],
        ];

        // 1. Simpan draf dengan 3 slide banner
        $draftResponse = $this->actingAs($this->superAdmin)->postJson(route('superadmin.site-editor.save-draft'), [
            'landing_hero_slides' => $slidesPayload,
            'landing_hero_image' => 'landing/media/banner1.jpg',
        ]);

        $draftResponse->assertStatus(200);
        $draftResponse->assertJson(['success' => true]);

        // 2. Terbitkan draf
        $publishResponse = $this->actingAs($this->superAdmin)->postJson(route('superadmin.site-editor.publish'));
        $publishResponse->assertStatus(200);
        $publishResponse->assertJson(['success' => true]);

        // 3. Verifikasi halaman publik me-render ketiga slide banner tersebut
        $publicResponse = $this->get(route('landing'));
        $publicResponse->assertStatus(200);
        $publicResponse->assertSee('Magang Sleman Hebat 2026');
        $publicResponse->assertSee('Pembekalan Mentor ASN Profesional');
        $publicResponse->assertSee('Kunjungan Infrastruktur Jaringan');
        $publicResponse->assertSee('WORKSHOP DIGITAL');
        $publicResponse->assertSee('STUDI LAPANGAN');
        $publicResponse->assertSee('hero-dots-desktop');
        $publicResponse->assertSee('data-slide-index="3"', false); // 1 info slide + 3 banner = index 0,1,2,3
    }
}
