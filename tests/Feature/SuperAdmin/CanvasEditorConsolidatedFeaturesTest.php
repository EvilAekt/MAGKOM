<?php

declare(strict_types=1);

namespace Tests\Feature\SuperAdmin;

use App\Enums\RoleEnum;
use App\Models\Pengaturan as PengaturanModel;
use App\Models\User;
use App\Services\LandingCmsStagingService;
use App\Support\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CanvasEditorConsolidatedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected User $admin;

    protected User $pembimbing;

    protected User $peserta;

    protected LandingCmsStagingService $stagingService;

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

        $this->pembimbing = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'status_aktif' => true,
        ]);

        $this->peserta = User::factory()->create([
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $this->stagingService = app(LandingCmsStagingService::class);
    }

    public function test_superadmin_can_access_canvas_editor_with_rebranded_title_and_page_selector(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.site-editor.index'));

        $response->assertStatus(200);
        $response->assertSee('Canvas Editor', false);
        $response->assertSee('select-canvas-page');
        $response->assertSee('value="landing"', false);
        $response->assertSee('value="peta-alur"', false);
        $response->assertSee('value="kebijakan-privasi"', false);
        $response->assertSee('block-form-kebijakan');
        $response->assertSee('block-form-tahapan');
        $response->assertSee('btn-choose-pdf-media');
        $response->assertSee('btn-reset-panduan-pdf');
    }

    public function test_canvas_preview_supports_switching_between_landing_peta_alur_and_kebijakan(): void
    {
        // 1. Landing Page preview
        $landingResponse = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.landing.preview'));
        $landingResponse->assertStatus(200);
        $landingResponse->assertSee('MAGKOM', false);

        // 2. Peta Alur Interaktif preview
        $petaResponse = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.landing.preview', ['page' => 'peta-alur']));
        $petaResponse->assertStatus(200);
        $petaResponse->assertSee('Peta Alur', false);

        // 3. Kebijakan Privasi preview
        $kebijakanResponse = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.landing.preview', ['page' => 'kebijakan-privasi']));
        $kebijakanResponse->assertStatus(200);
        $kebijakanResponse->assertSee('Kebijakan Privasi', false);
    }

    public function test_legacy_cms_redirects_to_canvas_editor_with_param(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.landing.index', ['redirect_to_canvas' => 1]));

        $response->assertRedirect(route('superadmin.site-editor.index'));
        $response->assertSessionHas('info');
    }

    public function test_canvas_editor_can_save_draft_for_tahapan_steps_and_kebijakan_sections(): void
    {
        $payload = [
            'landing_tahapan_heading' => 'Tahapan Pendaftaran Magang 2026',
            'landing_tahapan_steps' => [
                ['label' => '01', 'title' => 'Buat Akun Mandiri', 'desc' => 'Daftar menggunakan email aktif siswa atau mahasiswa.'],
                ['label' => '02', 'title' => 'Lengkapi Berkas', 'desc' => 'Unggah CV, transkrip nilai, dan surat pengantar.'],
                ['label' => '03', 'title' => 'Pilih Formasi Bidang', 'desc' => 'Pilih kuota dan unit kerja yang sesuai keahlian.'],
                ['label' => '04', 'title' => 'Verifikasi & Mulai', 'desc' => 'Tunggu pengumuman seleksi dari administrator dinas.'],
            ],
            'landing_panduan_pdf' => 'uploads/panduan/Panduan_Resmi_2026.pdf',
            'landing_panduan_pdf_name' => 'Panduan_Resmi_2026.pdf',
            'kebijakan_privasi_badge' => 'Kepatuhan UU PDP No. 27/2022',
            'kebijakan_privasi_title' => 'Kebijakan Privasi & Perlindungan Data Peserta',
            'kebijakan_privasi_updated_at' => '10 November 2026',
            'kebijakan_privasi_sections' => [
                [
                    'nomor' => 1,
                    'judul' => 'Landasan Hukum Pemrosesan Data',
                    'konten' => 'Pemrosesan data peserta magang tunduk pada UU Pelindungan Data Pribadi.',
                ],
                [
                    'nomor' => 2,
                    'judul' => 'Hak Subjek Data Peserta',
                    'konten' => 'Peserta berhak meminta salinan, pembetulan, atau pembaruan data pribadi.',
                ],
            ],
        ];

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // Verifikasi draf tersimpan di staging
        $draftMeta = $this->stagingService->getDraftMetadata();
        $this->assertTrue($draftMeta['active']);
        $this->assertEquals('Tahapan Pendaftaran Magang 2026', Pengaturan::getLive('draft_landing_tahapan_heading'));
        $this->assertEquals('Panduan_Resmi_2026.pdf', Pengaturan::getLive('draft_landing_panduan_pdf_name'));
        $this->assertEquals('Kebijakan Privasi & Perlindungan Data Peserta', Pengaturan::getLive('draft_kebijakan_privasi_title'));

        $savedSteps = Pengaturan::json('draft_landing_tahapan_steps');
        $this->assertCount(4, $savedSteps);
        $this->assertEquals('Buat Akun Mandiri', $savedSteps[0]['title'] ?? null);

        $savedSections = Pengaturan::json('draft_kebijakan_privasi_sections');
        $this->assertCount(2, $savedSections);
        $this->assertEquals('Landasan Hukum Pemrosesan Data', $savedSections[0]['judul'] ?? null);
    }

    public function test_canvas_editor_can_publish_draft_including_kebijakan_privasi_and_rollback(): void
    {
        // 0. Set data live awal agar tersimpan di backup saat publish
        Pengaturan::set('landing_hero_title', 'Versi Live Awal');
        Pengaturan::set('kebijakan_privasi_title', 'Kebijakan Awal 2025');

        // 1. Simpan draf baru
        $payload = [
            'landing_hero_title' => 'Portal Magang Kominfo Terkini',
            'kebijakan_privasi_title' => 'Kebijakan Privasi Terpublikasi 2026',
            'kebijakan_privasi_badge' => 'Sertifikasi Keamanan ISO 27001',
        ];

        $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), $payload)
            ->assertStatus(200);

        // 2. Publish draf
        $publishResponse = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.publish'));

        $publishResponse->assertStatus(200);
        $publishResponse->assertJson(['success' => true]);

        // Verifikasi tabel Pengaturan live
        $savedKebijakanTitle = PengaturanModel::where('kunci', 'kebijakan_privasi_title')->first();
        $this->assertNotNull($savedKebijakanTitle);
        $this->assertEquals('Kebijakan Privasi Terpublikasi 2026', $savedKebijakanTitle->nilai);

        $savedBadge = PengaturanModel::where('kunci', 'kebijakan_privasi_badge')->first();
        $this->assertNotNull($savedBadge);
        $this->assertEquals('Sertifikasi Keamanan ISO 27001', $savedBadge->nilai);

        // 3. Rollback
        $rollbackResponse = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.rollback'));

        $rollbackResponse->assertStatus(200);
        $rollbackResponse->assertJson(['success' => true]);

        // Verifikasi nilai live kembali ke nilai awal
        $this->assertEquals('Versi Live Awal', Pengaturan::getLive('landing_hero_title'));
        $this->assertEquals('Kebijakan Awal 2025', Pengaturan::getLive('kebijakan_privasi_title'));
    }

    public function test_canvas_editor_can_reset_panduan_pdf(): void
    {
        // 1. Set draf panduan PDF kustom
        $payload = [
            'landing_panduan_pdf' => 'uploads/panduan/Custom_Book.pdf',
            'landing_panduan_pdf_name' => 'Custom_Book.pdf',
        ];

        $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.save-draft'), $payload)
            ->assertStatus(200);

        $this->assertEquals('Custom_Book.pdf', Pengaturan::getLive('draft_landing_panduan_pdf_name'));

        // 2. Reset panduan PDF via AJAX
        $resetResponse = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.site-editor.reset-panduan'));

        $resetResponse->assertStatus(200);
        $resetResponse->assertJson([
            'success' => true,
            'message' => 'Dokumen panduan berhasil direset ke generator PDF resmi bawaan sistem.',
        ]);

        // Verifikasi draf telah tereset
        $this->assertEmpty(Pengaturan::getLive('draft_landing_panduan_pdf'));
        $this->assertEmpty(Pengaturan::getLive('draft_landing_panduan_pdf_name'));
    }

    public function test_unauthorized_users_cannot_access_canvas_editor_endpoints(): void
    {
        // 1. Guest
        $this->get(route('superadmin.site-editor.index'))
            ->assertRedirect(route('login'));

        $this->postJson(route('superadmin.site-editor.reset-panduan'))
            ->assertStatus(401);

        // 2. Admin (bukan SuperAdmin)
        $this->actingAs($this->admin)
            ->get(route('superadmin.site-editor.index'))
            ->assertStatus(403);

        $this->actingAs($this->admin)
            ->postJson(route('superadmin.site-editor.reset-panduan'))
            ->assertStatus(403);

        $this->actingAs($this->admin)
            ->postJson(route('superadmin.site-editor.save-draft'), ['landing_hero_title' => 'Hacked'])
            ->assertStatus(403);

        // 3. Pembimbing
        $this->actingAs($this->pembimbing)
            ->get(route('superadmin.site-editor.index'))
            ->assertStatus(403);

        $this->actingAs($this->pembimbing)
            ->postJson(route('superadmin.site-editor.reset-panduan'))
            ->assertStatus(403);

        // 4. Peserta
        $this->actingAs($this->peserta)
            ->get(route('superadmin.site-editor.index'))
            ->assertStatus(403);

        $this->actingAs($this->peserta)
            ->postJson(route('superadmin.site-editor.reset-panduan'))
            ->assertStatus(403);
    }
}
