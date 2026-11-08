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
        $response->assertSee('Tipografi &amp; Ukuran Huruf', false);
        $response->assertSee('Dimensi &amp; Spasi (Pixel)', false);
        $response->assertSee('Batas &amp; Sudut Melengkung', false);
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

    public function test_superadmin_can_save_responsive_styles_and_preview_reflects_media_queries(): void
    {
        $responsiveStyles = [
            'desktop' => ['landing_hero_title_size_px' => 48, 'landing_hero_padding_y_px' => 80],
            'tablet' => ['landing_hero_title_size_px' => 36, 'landing_hero_padding_y_px' => 60],
            'mobile' => ['landing_hero_title_size_px' => 24, 'landing_hero_padding_y_px' => 40],
        ];

        $payload = [
            'landing_responsive_styles' => $responsiveStyles,
        ];

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), $payload);

        $response->assertStatus(200);

        // Preview editor_mode=1 draf aktif
        $previewResponse = $this->get('/?editor_mode=1');
        $previewResponse->assertStatus(200);
        $previewResponse->assertSee('@media (max-width: 768px)', false);
        $previewResponse->assertSee('@media (max-width: 480px)', false);
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

    public function test_superadmin_can_retrieve_revisions_and_restore_a_revision(): void
    {
        // Buat revisi manual via staging service / save-draft
        $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), [
                'landing_hero_title' => 'Revisi Judul 1',
            ])->assertStatus(200);

        $revisionsResponse = $this->actingAs($this->superAdmin)
            ->getJson(route('superadmin.site-editor.revisions'));

        $revisionsResponse->assertStatus(200);
        $revisions = $revisionsResponse->json('revisions');
        $this->assertNotEmpty($revisions);

        $revisionId = $revisions[0]['id'];

        // Ubah draf saat ini
        Pengaturan::setNilai('draft_landing_hero_title', 'Judul Baru Yang Mau Ditimpa');

        // Restore revisi pertama
        $restoreResponse = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.revisions.restore', ['id' => $revisionId]));

        $restoreResponse->assertStatus(200);
        $restoreResponse->assertJson(['success' => true]);

        $this->assertEquals('Revisi Judul 1', Pengaturan::getNilai('draft_landing_hero_title'));
    }

    public function test_superadmin_media_library_upload_browse_update_and_delete_protection(): void
    {
        Storage::fake('public');

        // 1. Upload berkas baru
        $fakeImage = UploadedFile::fake()->image('banner-sle.png', 600, 400);

        $uploadResponse = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.media.upload'), [
                'file' => $fakeImage,
                'alt_text' => 'Gedung Diskominfo',
                'caption' => 'Kantor Sleman',
            ]);

        $uploadResponse->assertStatus(200);
        $uploadResponse->assertJson(['success' => true]);
        $mediaItem = $uploadResponse->json('item');
        $this->assertNotNull($mediaItem['id']);
        $this->assertEquals('Gedung Diskominfo', $mediaItem['alt_text']);

        // 2. Browse media
        $listResponse = $this->actingAs($this->superAdmin)
            ->getJson(route('superadmin.site-editor.media.index'));

        $listResponse->assertStatus(200);
        $this->assertCount(1, $listResponse->json('media'));

        // 3. Update metadata
        $updateResponse = $this->actingAs($this->superAdmin)
            ->putJson(route('superadmin.site-editor.media.update', ['id' => $mediaItem['id']]), [
                'alt_text' => 'Gedung Diskominfo Sleman Baru',
                'caption' => 'Kantor Sleman Pusat',
            ]);

        $updateResponse->assertStatus(200);

        // 4. Delete protection jika media sedang digunakan
        Pengaturan::setNilai('landing_hero_image', $mediaItem['url']);

        $deleteAttempt = $this->actingAs($this->superAdmin)
            ->deleteJson(route('superadmin.site-editor.media.delete', ['id' => $mediaItem['id']]));

        $deleteAttempt->assertStatus(422);
        $deleteAttempt->assertJson(['success' => false]);

        // Lepas penggunaan lalu hapus
        Pengaturan::setNilai('landing_hero_image', '');

        $deleteSuccess = $this->actingAs($this->superAdmin)
            ->deleteJson(route('superadmin.site-editor.media.delete', ['id' => $mediaItem['id']]));

        $deleteSuccess->assertStatus(200);
        $deleteSuccess->assertJson(['success' => true]);
    }
}
