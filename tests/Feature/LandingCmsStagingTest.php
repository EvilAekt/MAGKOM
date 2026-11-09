<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\User;
use App\Support\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LandingCmsStagingTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $adminVerifikator;

    private User $peserta;

    private Bidang $bidang;

    private Jurusan $jurusan;

    private BulanMagang $bulanMagang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Bidang Aplikasi Informatika',
            'kode_bidang' => 'APTIKA',
            'deskripsi' => 'Pengembangan aplikasi dan tata kelola e-gov',
            'aktif' => true,
        ]);

        $this->jurusan = Jurusan::create([
            'nama_jurusan' => 'Teknik Informatika',
            'kategori' => 'mahasiswa',
            'bidang_id' => $this->bidang->id,
            'aktif' => true,
        ]);

        $this->superAdmin = User::factory()->create([
            'nama' => 'Super Administrator MagKom',
            'email' => 'superadmin@slemankab.go.id',
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
        ]);

        $this->adminVerifikator = User::factory()->create([
            'nama' => 'Admin Verifikator',
            'email' => 'admin@slemankab.go.id',
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
        ]);

        $this->peserta = User::factory()->create([
            'nama' => 'Calon Peserta Magang',
            'email' => 'peserta@gmail.com',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);

        $this->bulanMagang = BulanMagang::create([
            'bulan_mulai' => '2026-11',
            'nama_bulan' => 'November 2026',
            'tgl_buka' => '2026-10-01',
            'tgl_tutup' => '2026-10-25',
            'aktif' => true,
        ]);

        // Nilai awal stabil (live)
        Pengaturan::set('landing_hero_title', 'Judul Live Awal 2026');
        Pengaturan::set('landing_hero_kicker', 'Kabupaten Sleman');
        Pengaturan::setPreviewMode(false);
        Pengaturan::clearCache();
    }

    /**
     * Skenario 1: Super Admin dapat menyimpan perubahan sebagai draf (sandbox),
     * dan perubahan TIDAK bocor ke publik sebelum dipublikasikan.
     */
    public function test_superadmin_can_save_draft_and_it_does_not_affect_public_landing_page(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'hero',
                'action_type' => 'draft',
                'hero_title' => 'Judul Draf Eksklusif Sandbox Sleman',
                'hero_kicker' => 'Sleman Istimewa Draf',
            ]);

        $response->assertRedirect(route('superadmin.landing.index', ['tab' => 'hero']));
        $response->assertSessionHas('success');

        // Pastikan nilai live tetap 'Judul Live Awal 2026'
        $this->assertEquals('Judul Live Awal 2026', Pengaturan::getLive('landing_hero_title'));

        // Pastikan nilai draf tersimpan di 'draft_landing_hero_title'
        $this->assertEquals('Judul Draf Eksklusif Sandbox Sleman', Pengaturan::getDraft('landing_hero_title'));

        // Verifikasi Pengunjung Publik TIDAK melihat judul draf
        $publicLanding = $this->get(route('landing'));
        $publicLanding->assertOk();
        $publicLanding->assertSee('Judul Live Awal 2026');
        $publicLanding->assertDontSee('Judul Draf Eksklusif Sandbox Sleman');
    }

    /**
     * Skenario 2: Super Admin dapat mengakses halaman pratinjau interaktif
     * dan melihat konten draf beserta Floating Preview Control Bar.
     */
    public function test_superadmin_can_access_preview_and_sees_draft_content_and_preview_control_bar(): void
    {
        // Simpan draf
        $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'hero',
                'action_type' => 'draft',
                'hero_title' => 'Judul Draf Eksklusif Sandbox Sleman',
                'hero_kicker' => 'Sleman Istimewa Draf',
            ]);

        // Akses route pratinjau
        $previewResponse = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.landing.preview'));

        $previewResponse->assertOk();
        $previewResponse->assertSee('Judul Draf Eksklusif Sandbox Sleman');
        $previewResponse->assertSee('Sleman Istimewa Draf');

        // Memastikan bar kontrol pratinjau muncul
        $previewResponse->assertSee('MODE PRATINJAU INTERAKTIF');
        $previewResponse->assertSee('Kembali ke Editor');
        $previewResponse->assertSee('Buang Draf');
        $previewResponse->assertSee('Publikasikan ke Publik');
    }

    /**
     * Skenario 3: Tamu anonim atau user non-SuperAdmin tidak dapat mengakses route preview
     * dan tidak dapat mengaktifkan mode pratinjau lewat parameter URL.
     */
    public function test_unauthenticated_or_non_superadmin_cannot_access_preview(): void
    {
        // 1. Tamu anonim mengakses preview diarahkan ke login
        $guestResponse = $this->get(route('superadmin.landing.preview'));
        $guestResponse->assertRedirect(route('login'));

        // 2. User biasa (Peserta) mengakses preview ditolak 403 Forbidden
        $pesertaResponse = $this->actingAs($this->peserta)
            ->get(route('superadmin.landing.preview'));
        $pesertaResponse->assertForbidden();

        // 3. User Admin biasa mengakses preview ditolak 403 Forbidden
        $adminResponse = $this->actingAs($this->adminVerifikator)
            ->get(route('superadmin.landing.preview'));
        $adminResponse->assertForbidden();

        // 4. Tamu anonim mencoba membubuhkan query ?preview=1 pada landing page publik tetap melihat versi live
        Pengaturan::set('draft_landing_hero_title', 'Draf Rahasia Super Admin');
        Pengaturan::clearCache();

        $landingWithParam = $this->get('/?preview=1');
        $landingWithParam->assertOk();
        $landingWithParam->assertDontSee('Draf Rahasia Super Admin');
        $landingWithParam->assertDontSee('MODE PRATINJAU INTERAKTIF');
    }

    /**
     * Skenario 4: Super Admin mempublikasikan draf, sehingga konten draf
     * resmi menjadi versi live yang dilihat oleh publik.
     */
    public function test_superadmin_can_publish_draft_and_it_becomes_visible_on_public_landing(): void
    {
        // Simpan draf
        $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'hero',
                'action_type' => 'draft',
                'hero_title' => 'Judul Terbit Resmi 2026',
            ]);

        // Publikasikan draf
        $publishResponse = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.landing.publish'));

        $publishResponse->assertRedirect(route('superadmin.landing.index'));
        $publishResponse->assertSessionHas('success');

        // Pastikan nilai live sudah berganti
        $this->assertEquals('Judul Terbit Resmi 2026', Pengaturan::getLive('landing_hero_title'));

        // Pastikan draf sudah dihapus
        $this->assertFalse(Pengaturan::hasDraft('landing_hero_title'));

        // Pengunjung umum sekarang melihat konten baru
        $publicLanding = $this->get(route('landing'));
        $publicLanding->assertOk();
        $publicLanding->assertSee('Judul Terbit Resmi 2026');
    }

    /**
     * Skenario 5: Super Admin dapat membuang (discard) draf dan mengembalikan
     * sistem ke versi live tanpa ada jejak perubahan.
     */
    public function test_superadmin_can_discard_draft_and_reverts_to_live_content(): void
    {
        // Simpan draf
        $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'hero',
                'action_type' => 'draft',
                'hero_title' => 'Draf Salah Ketik Yang Mau Dibuang',
            ]);

        $this->assertTrue(Pengaturan::hasDraft('landing_hero_title'));

        // Buang draf
        $discardResponse = $this->actingAs($this->superAdmin)
            ->delete(route('superadmin.landing.discard'));

        $discardResponse->assertRedirect(route('superadmin.landing.index'));
        $discardResponse->assertSessionHas('success');

        // Pastikan draf terhapus
        $this->assertFalse(Pengaturan::hasDraft('landing_hero_title'));

        // Mode pratinjau sekarang kembali menampilkan versi live awal
        $previewResponse = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.landing.preview'));
        $previewResponse->assertDontSee('Draf Salah Ketik Yang Mau Dibuang');
        $previewResponse->assertSee('Judul Live Awal 2026');
    }

    /**
     * Skenario 6: Super Admin dapat menyimpan draf foto banner dan PDF,
     * yang tersimpan di direktori sementara lalu dipromosikan saat dipublikasikan.
     */
    public function test_superadmin_can_save_draft_with_photo_and_pdf_and_promotes_them_on_publish(): void
    {
        Storage::fake('public');

        $fakeImage = UploadedFile::fake()->image('banner_draft.png', 1000, 500);
        $fakePdf = UploadedFile::fake()->create('Buku_Panduan_Draft.pdf', 500, 'application/pdf');

        // Simpan draf dengan media
        $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'hero',
                'action_type' => 'draft',
                'hero_title' => 'Hero Banner Baru Draf',
                'hero_image' => $fakeImage,
                'panduan_pdf' => $fakePdf,
            ]);

        $draftImagePath = Pengaturan::getDraft('landing_hero_image');
        $this->assertNotEmpty($draftImagePath);
        $this->assertStringContainsString('landing/draft', $draftImagePath);
        Storage::disk('public')->assertExists($draftImagePath);

        // Publikasikan
        $this->actingAs($this->superAdmin)
            ->post(route('superadmin.landing.publish'));

        $liveImagePath = Pengaturan::getLive('landing_hero_image');
        $this->assertNotEmpty($liveImagePath);
        $this->assertStringNotContainsString('draft', $liveImagePath);
        Storage::disk('public')->assertExists($liveImagePath);
    }

    /**
     * Skenario 7: Halaman index CMS menampilkan draft alert banner
     * saat terdapat draf aktif di dalam kontainer.
     */
    public function test_cms_index_shows_draft_alert_banner_when_draft_active(): void
    {
        // 1. Sebelum ada draf: Tampilkan banner hijau tersinkronisasi
        $indexBefore = $this->actingAs($this->superAdmin)->get(route('superadmin.landing.index'));
        $indexBefore->assertOk();
        $indexBefore->assertSee('Versi Live Terpublikasi &amp; Tersinkronisasi', false);

        // 2. Simpan draf
        $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'header_seo',
                'action_type' => 'draft',
                'meta_title' => 'Judul SEO Draf 2027',
                'meta_desc' => 'Deskripsi Draf 2027',
                'header_brand_name' => 'MAGKOM DRAF',
                'header_brand_sub' => 'Diskominfo Sleman Draf',
            ]);

        // 3. Setelah ada draf: Tampilkan banner peringatan oranye sandbox aktif
        $indexAfter = $this->actingAs($this->superAdmin)->get(route('superadmin.landing.index'));
        $indexAfter->assertOk();
        $indexAfter->assertSee('Ada Perubahan Draf Belum Terpublikasi (Kontainer Sandbox Aktif)');
        $indexAfter->assertSee('Buka Pratinjau Interaktif');
        $indexAfter->assertSee('Buang Draf');
        $indexAfter->assertSee('Publikasikan ke Publik');
    }
}
