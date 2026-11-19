<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuperAdminLandingContentSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
            'must_change_password' => false,
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Payload konfigurasi dasar yang valid.
     */
    protected function validSettingsPayload(array $overrides = []): array
    {
        return array_merge([
            'kuota_umum_maks_per_bulan' => 12,
            'slot_khusus_default' => 3,
            'maks_peserta_aktif_per_pembimbing' => 20,
            'maks_anggota_kelompok' => 4,
            'durasi_magang_maks_bulan' => 6,
            'logbook_maks_mundur_hari' => 3,
            'berkas_maks_mb' => 10,
            'deadline_mendekati_hari' => 3,
            'sla_verifikasi_hari' => 5,
            'masa_tenggang_bulan' => 2,
            'retensi_audit_akses_data_hari' => 365,
        ], $overrides);
    }

    public function test_superadmin_can_view_landing_content_settings(): void
    {
        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.pengaturan.index'));

        $response->assertOk();
        $response->assertSee('Konten Publik Landing Page');
        $response->assertSee('Foto Dokumentasi Hero Carousel');
        $response->assertSee('Berkas Unduh Panduan Pendaftaran');
    }

    public function test_superadmin_can_upload_valid_hero_image(): void
    {
        $file = UploadedFile::fake()->image('hero_banner.jpg', 1920, 600);

        $payload = $this->validSettingsPayload([
            'hero_image' => $file,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.pengaturan.update'), $payload);

        $response->assertRedirect(route('superadmin.pengaturan.index'));
        $response->assertSessionHas('success');

        $savedPath = Pengaturan::getNilai('landing_hero_image');
        $this->assertNotNull($savedPath);
        Storage::disk('public')->assertExists($savedPath);

        // Verifikasi landing page menyertakan URL foto kustom
        $landingResponse = $this->get(route('landing'));
        $landingResponse->assertOk();
        $landingResponse->assertSee($savedPath);
    }

    public function test_hero_image_validation_rejects_non_image_files(): void
    {
        $invalidFile = UploadedFile::fake()->create('script.php', 100, 'text/plain');

        $payload = $this->validSettingsPayload([
            'hero_image' => $invalidFile,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.pengaturan.update'), $payload);

        $response->assertSessionHasErrors(['hero_image']);
    }

    public function test_superadmin_can_reset_hero_image_to_default(): void
    {
        // Pasang foto kustom terlebih dahulu
        $fakePath = 'landing/hero/test_reset.jpg';
        Storage::disk('public')->put($fakePath, 'dummy image content');
        Pengaturan::setNilai('landing_hero_image', $fakePath);

        $this->assertTrue(Storage::disk('public')->exists($fakePath));

        // Panggil endpoint reset
        $response = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.pengaturan.reset-hero'));

        $response->assertRedirect(route('superadmin.pengaturan.index'));
        $response->assertSessionHas('success');

        $this->assertNull(Pengaturan::getNilai('landing_hero_image'));
        $this->assertFalse(Storage::disk('public')->exists($fakePath));
    }

    public function test_superadmin_can_upload_valid_panduan_pdf(): void
    {
        $pdfFile = UploadedFile::fake()->create('Panduan_Magang_Sleman_2026.pdf', 500, 'application/pdf');

        $payload = $this->validSettingsPayload([
            'panduan_pdf' => $pdfFile,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.pengaturan.update'), $payload);

        $response->assertRedirect(route('superadmin.pengaturan.index'));
        $response->assertSessionHas('success');

        $savedPdf = Pengaturan::getNilai('landing_panduan_pdf');
        $this->assertNotNull($savedPdf);
        Storage::disk('public')->assertExists($savedPdf);
        $this->assertEquals('Panduan_Magang_Sleman_2026.pdf', Pengaturan::getNilai('landing_panduan_pdf_name'));

        // Verifikasi endpoint unduh panduan menyajikan file kustom yang diunggah
        $downloadResponse = $this->get(route('panduan.pdf'));
        $downloadResponse->assertOk();
        $this->assertEquals('application/pdf', $downloadResponse->headers->get('Content-Type'));
    }

    public function test_panduan_pdf_validation_rejects_non_pdf_files(): void
    {
        $invalidFile = UploadedFile::fake()->image('gambar.jpg');

        $payload = $this->validSettingsPayload([
            'panduan_pdf' => $invalidFile,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.pengaturan.update'), $payload);

        $response->assertSessionHasErrors(['panduan_pdf']);
    }

    public function test_superadmin_can_reset_panduan_pdf_to_system_generator(): void
    {
        $fakePdf = 'landing/panduan/test_panduan.pdf';
        Storage::disk('public')->put($fakePdf, '%PDF-1.4 dummy pdf');
        Pengaturan::setNilai('landing_panduan_pdf', $fakePdf);
        Pengaturan::setNilai('landing_panduan_pdf_name', 'test_panduan.pdf');

        $this->assertTrue(Storage::disk('public')->exists($fakePdf));

        // Panggil endpoint reset
        $response = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.pengaturan.reset-panduan'));

        $response->assertRedirect(route('superadmin.pengaturan.index'));
        $response->assertSessionHas('success');

        $this->assertNull(Pengaturan::getNilai('landing_panduan_pdf'));
        $this->assertNull(Pengaturan::getNilai('landing_panduan_pdf_name'));
        $this->assertFalse(Storage::disk('public')->exists($fakePdf));
    }
}
