<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Support\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingCmsVisualBuilderAndThemeTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'nama' => 'Super Administrator MagKom',
            'email' => 'superadmin@slemankab.go.id',
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
        ]);

        $this->peserta = User::factory()->create([
            'nama' => 'Calon Peserta Magang',
            'email' => 'peserta@student.ac.id',
            'role' => RoleEnum::Peserta,
            'status_aktif' => true,
        ]);
    }

    public function test_superadmin_can_update_navbar_items_via_visual_array_input(): void
    {
        $navItems = [
            ['label' => 'Beranda Utama', 'url' => '#beranda'],
            ['label' => 'Alokasi Kuota', 'url' => '#kuota'],
            ['label' => 'Tahapan Lengkap', 'url' => '#tahapan'],
            ['label' => 'Tanya Jawab', 'url' => '#faq'],
        ];

        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'header_seo',
                'action_type' => 'publish',
                'meta_title' => 'Portal Magang Sleman 2026',
                'meta_desc' => 'Deskripsi meta portal magang terbaru',
                'header_brand_name' => 'MAGKOM SLEMAN',
                'header_brand_sub' => 'Diskominfo Sleman',
                'nav_items' => $navItems,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $savedNav = Pengaturan::json('landing_header_nav');
        $this->assertCount(4, $savedNav);
        $this->assertEquals('Beranda Utama', $savedNav[0]['label']);
        $this->assertEquals('#beranda', $savedNav[0]['url']);
        $this->assertEquals('Alokasi Kuota', $savedNav[1]['label']);
    }

    public function test_superadmin_can_update_faq_categories_and_questions_via_nested_builder(): void
    {
        $faqCategories = [
            [
                'id' => 'syarat_umum',
                'nama' => 'Persyaratan Umum',
                'items' => [
                    [
                        'q' => 'Apakah terbuka untuk siswa SMK?',
                        'a' => 'Ya, untuk program keahlian TKJ, RPL, dan Multimedia.',
                    ],
                    [
                        'q' => 'Berapa durasi magang minimal?',
                        'a' => 'Durasi magang minimal 1 bulan hingga 6 bulan.',
                    ],
                ],
            ],
            [
                'id' => 'sertifikat',
                'nama' => 'Sertifikat & Penilaian',
                'items' => [
                    [
                        'q' => 'Apakah sertifikat memiliki tanda tangan elektronik?',
                        'a' => 'Ya, diterbitkan resmi dengan verifikasi QR code dan TTE Kepala Dinas.',
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'faq',
                'action_type' => 'publish',
                'faq_heading' => 'Pertanyaan Seputar Magang Diskominfo Sleman',
                'faq_categories' => $faqCategories,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $savedFaq = Pengaturan::json('landing_faq_data');
        $this->assertCount(2, $savedFaq);
        $this->assertEquals('syarat_umum', $savedFaq[0]['id']);
        $this->assertEquals('Persyaratan Umum', $savedFaq[0]['nama']);
        $this->assertCount(2, $savedFaq[0]['items']);
        $this->assertEquals('Apakah terbuka untuk siswa SMK?', $savedFaq[0]['items'][0]['q']);
        $this->assertEquals('Sertifikat & Penilaian', $savedFaq[1]['nama']);
    }

    public function test_superadmin_can_update_footer_medsos_tautan_and_akun_via_visual_repeaters(): void
    {
        $footerMedsos = [
            ['platform' => 'Instagram', 'label' => '@kominfo_sleman', 'url' => 'https://instagram.com/kominfo_sleman'],
            ['platform' => 'YouTube', 'label' => 'Diskominfo Sleman TV', 'url' => 'https://youtube.com/@diskominfosleman'],
        ];

        $footerTautan = [
            ['label' => 'Portal Satu Data', 'url' => 'https://satudata.slemankab.go.id'],
            ['label' => 'PPID Sleman', 'url' => 'https://ppid.slemankab.go.id'],
        ];

        $footerAkun = [
            ['label' => 'Login Mahasiswa', 'url' => '/login'],
            ['label' => 'Login Mentor ASN', 'url' => '/login'],
        ];

        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'footer',
                'action_type' => 'publish',
                'footer_alamat' => 'Jl. Parasamya No. 1, Beran, Sleman',
                'footer_telepon' => '0274-868405',
                'footer_email' => 'kominfo@slemankab.go.id',
                'footer_jam_layanan' => 'Senin - Jumat 07.30 - 15.30 WIB',
                'footer_copyright' => 'Diskominfo Kabupaten Sleman 2026',
                'footer_medsos' => $footerMedsos,
                'footer_tautan' => $footerTautan,
                'footer_akun' => $footerAkun,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $savedMedsos = Pengaturan::json('landing_footer_medsos');
        $this->assertCount(2, $savedMedsos);
        $this->assertEquals('YouTube', $savedMedsos[1]['platform']);

        $savedTautan = Pengaturan::json('landing_footer_tautan');
        $this->assertCount(2, $savedTautan);
        $this->assertEquals('Portal Satu Data', $savedTautan[0]['label']);

        $savedAkun = Pengaturan::json('landing_footer_akun');
        $this->assertCount(2, $savedAkun);
        $this->assertEquals('Login Mahasiswa', $savedAkun[0]['label']);
    }

    public function test_superadmin_can_update_kebijakan_privasi_sections_via_visual_builder(): void
    {
        $kebijakanSections = [
            [
                'nomor' => 1,
                'judul' => 'Dasar Hukum & Akuntabilitas',
                'konten' => 'Pengelolaan data pribadi tunduk pada UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi.',
                'items' => [
                    'Transparansi pemrosesan data pelamar.',
                    'Penyimpanan data hanya selama masa evaluasi.',
                ],
            ],
            [
                'nomor' => 2,
                'judul' => 'Hak Peserta Magang',
                'konten' => 'Setiap calon peserta berhak meminta klarifikasi dan pembaruan data yang tidak akurat.',
            ],
        ];

        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'kebijakan',
                'action_type' => 'publish',
                'kebijakan_badge' => 'UU PDP NO. 27/2022',
                'kebijakan_title' => 'Kebijakan Perlindungan Informasi',
                'kebijakan_updated_at' => 'November 2026',
                'kebijakan_sections' => $kebijakanSections,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $savedSections = Pengaturan::json('kebijakan_privasi_sections');
        $this->assertCount(2, $savedSections);
        $this->assertEquals('Dasar Hukum & Akuntabilitas', $savedSections[0]['judul']);
        $this->assertCount(2, $savedSections[0]['items']);
        $this->assertEquals('Hak Peserta Magang', $savedSections[1]['judul']);
    }

    public function test_superadmin_can_update_and_preview_visual_theming_tokens(): void
    {
        // 1. Simpan perubahan tema ke draf
        $response = $this->actingAs($this->superAdmin)
            ->put(route('superadmin.landing.update'), [
                'section' => 'theme',
                'action_type' => 'draft',
                'theme_primary_color' => '#0A2540',
                'theme_navy_dark' => '#071829',
                'theme_accent_color' => '#635BFF',
                'theme_gold_color' => '#FFC107',
                'theme_bg_color' => '#F4F7FB',
                'theme_font_display' => 'Plus Jakarta Sans',
                'theme_font_body' => 'Inter',
                'theme_border_radius' => '16px',
                'theme_btn_style' => 'pill',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Publik belum melihat tema draf
        $publicRes = $this->get(route('landing'));
        $publicRes->assertDontSee('#0A2540');
        $publicRes->assertDontSee('data-theme-primary="#0A2540"', false);

        // Pratinjau Staging melihat tema draf
        $previewRes = $this->actingAs($this->superAdmin)->get(route('superadmin.landing.preview'));
        $previewRes->assertSee('data-theme-primary="#0A2540"', false);
        $previewRes->assertSee('data-theme-accent="#635BFF"', false);
        $previewRes->assertSee('data-theme-font-display="Plus Jakarta Sans"', false);
        $previewRes->assertSee('data-theme-radius="16px"', false);
        $previewRes->assertSee('data-theme-btn-style="pill"', false);

        // 2. Publikasi perubahan tema ke produksi
        $publishRes = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.landing.publish'));
        $publishRes->assertRedirect();

        // Sekarang publik melihat tema yang sudah dipublikasikan
        $publicAfterPublish = $this->get(route('landing'));
        $publicAfterPublish->assertSee('data-theme-primary="#0A2540"', false);
        $publicAfterPublish->assertSee('data-theme-accent="#635BFF"', false);
        $publicAfterPublish->assertSee('data-theme-font-display="Plus Jakarta Sans"', false);
    }

    public function test_non_superadmin_cannot_access_or_update_visual_theme_settings(): void
    {
        $response = $this->actingAs($this->peserta)
            ->put(route('superadmin.landing.update'), [
                'section' => 'theme',
                'action_type' => 'publish',
                'theme_primary_color' => '#FF0000',
            ]);

        $response->assertStatus(403);
    }
}
