<?php

declare(strict_types=1);

namespace App\Services\LandingPage;

use App\Models\Pengaturan as PengaturanModel;
use App\Support\Pengaturan;
use Illuminate\Support\Facades\DB;

/**
 * Adapter dua arah antara sistem konfigurasi legacy CMS MAGKOM (8-tab Form Builder & key landing_*)
 * dengan arsitektur modern Page Document.
 * Memastikan Single Source of Truth tanpa risiko kehilangan data lama atau desinkronisasi.
 */
class LegacyCmsAdapter
{
    /**
     * Membaca pengaturan legacy dan mengonversinya menjadi Page Document lengkap.
     *
     * @param  bool  $draft  Apakah membaca dari kontainer draf (draft_*) atau live
     * @return array<string, mixed>
     */
    public function convertLegacyToDocument(bool $draft = false): array
    {
        $prefix = $draft ? 'draft_' : '';
        $getter = function (string $key, ?string $default = null) use ($prefix, $draft): ?string {
            if ($draft) {
                $val = PengaturanModel::getNilai($prefix.$key);
                if ($val !== null && $val !== '') {
                    return $val;
                }
            }

            return PengaturanModel::getNilai($key, $default);
        };

        $jsonGetter = function (string $key, array $default = []) use ($prefix, $draft): array {
            $raw = null;
            if ($draft) {
                $raw = PengaturanModel::getNilai($prefix.$key);
            }
            if ($raw === null || $raw === '') {
                $raw = PengaturanModel::getNilai($key);
            }
            if ($raw !== null && $raw !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }

            return $default;
        };

        // Ambil layout urutan jika sudah pernah disimpan
        $savedLayout = $jsonGetter('landing_blocks_layout', []);
        $savedResponsive = $jsonGetter('landing_responsive_styles', []);

        // Bangun blok Hero dari legacy keys
        $heroDef = BlockRegistry::get('hero');
        $heroContent = array_merge($heroDef?->defaultContent ?? [], [
            'kicker' => $getter('landing_hero_kicker', 'Kabupaten Sleman'),
            'title' => $getter('landing_hero_title', "Program Magang\nKominfo Sleman"),
            'subtitle' => 'Wujudkan generasi muda yang kompeten di era digital bersama Dinas Komunikasi dan Informatika Kabupaten Sleman.',
            'year' => $getter('landing_hero_year', '2026'),
            'period_info' => $getter('landing_hero_period_info', 'Pendaftaran 1–15 Okt 2026 · mulai 2 Nov 2026'),
            'cta_primary_text' => $getter('landing_hero_cta_text', 'Daftar Sekarang'),
            'cta_primary_url' => $getter('landing_hero_cta_primary_url', '#kuota'),
            'cta_secondary_text' => $getter('landing_hero_cta_secondary_text', 'Pelajari Lebih Lanjut'),
            'cta_secondary_url' => $getter('landing_hero_cta_secondary_url', '#tahapan'),
            'label_persyaratan' => $getter('landing_label_persyaratan', 'Persyaratan Umum'),
            'persyaratan_umum' => $getter('landing_persyaratan_umum', $heroDef?->defaultContent['persyaratan_umum'] ?? ''),
            'label_dokumen' => $getter('landing_label_dokumen', 'Dokumen Pendaftaran'),
            'dokumen_pendaftaran' => $getter('landing_dokumen_pendaftaran', $heroDef?->defaultContent['dokumen_pendaftaran'] ?? ''),
            'image' => $getter('landing_hero_image', ''),
            'overlay_active' => (bool) $getter('landing_hero_overlay_active', '1'),
        ]);

        $heroStyles = $heroDef?->defaultStyles ?? [];
        if ($titleSize = $getter('landing_hero_title_size_px')) {
            $parsedTitleSize = (int) $titleSize;
            $heroStyles['desktop']['title_size'] = ($parsedTitleSize >= 44) ? 34 : $parsedTitleSize;
        }
        if ($descSize = $getter('landing_hero_desc_size_px')) {
            $heroStyles['desktop']['desc_size'] = (int) $descSize;
        }
        if ($paddingY = $getter('landing_hero_padding_y_px')) {
            $parsedPaddingY = (int) $paddingY;
            $heroStyles['desktop']['padding_y'] = ($parsedPaddingY >= 60) ? 0 : $parsedPaddingY;
        }
        if ($imgHeight = $getter('landing_hero_image_height_px')) {
            $parsedImgHeight = (int) $imgHeight;
            $heroStyles['desktop']['image_height'] = ($parsedImgHeight >= 400) ? 340 : $parsedImgHeight;
        }

        // Terapkan override responsive jika ada
        if (! empty($savedResponsive['tablet']['landing_hero_title_size_px'])) {
            $heroStyles['tablet']['title_size'] = (int) $savedResponsive['tablet']['landing_hero_title_size_px'];
        }
        if (! empty($savedResponsive['mobile']['landing_hero_title_size_px'])) {
            $heroStyles['mobile']['title_size'] = (int) $savedResponsive['mobile']['landing_hero_title_size_px'];
        }

        // Bangun blok Kuota dari legacy keys
        $kuotaDef = BlockRegistry::get('kuota');
        $kuotaContent = array_merge($kuotaDef?->defaultContent ?? [], [
            'heading' => $getter('landing_kuota_heading', 'Pilih Penempatan & Ketersediaan Kuota Magang'),
            'subheading' => $getter('landing_kuota_subheading', 'Pilih jenjang pendidikan, tentukan bulan mulai, lalu pilih bidang penempatan dan jurusanmu untuk langsung mengamankan kuota pendaftaran.'),
            'q1_label' => $getter('landing_kuota_q1_label', '1. Kamu siapa?'),
            'pill_smk' => $getter('landing_kuota_pill_smk', 'Siswa SMK'),
            'pill_mhs' => $getter('landing_kuota_pill_mhs', 'Mahasiswa (D3/S1)'),
            'q2_label' => $getter('landing_kuota_q2_label', '2. Mau mulai kapan?'),
            'empty_note' => $getter('landing_kuota_empty_note', 'Belum ada periode magang aktif.'),
            'q3_label' => $getter('landing_kuota_q3_label', '3. Magang sama siapa?'),
            'pill_mandiri' => $getter('landing_kuota_pill_mandiri', 'Individu (1 Orang)'),
            'pill_tim' => $getter('landing_kuota_pill_tim', 'Kelompok (Tim)'),
            'team_label' => $getter('landing_kuota_team_label', 'Berapa orang dalam tim kamu?'),
            'team_note' => $getter('landing_kuota_team_note', 'Cukup 1 perwakilan/ketua yang mendaftar. Data rekan tim dimasukkan langsung di form.'),
            'gauge_desc' => $getter('landing_kuota_gauge_desc', 'Memuat informasi ketersediaan kuota...'),
            'legend_tersedia' => $getter('landing_kuota_legend_tersedia', 'Masih banyak'),
            'legend_hampir' => $getter('landing_kuota_legend_hampir', 'Tinggal sedikit'),
            'legend_penuh' => $getter('landing_kuota_legend_penuh', 'Penuh / Waiting List'),
            'btn_tersedia' => $getter('landing_kuota_btn_tersedia', 'Daftar Sekarang ➔'),
            'btn_waiting' => $getter('landing_kuota_btn_waiting', 'Daftar Waiting List ➔'),
            'btn_tutup' => $getter('landing_kuota_btn_tutup', 'Pendaftaran Ditutup'),
        ]);

        $kuotaStyles = $kuotaDef?->defaultStyles ?? [];
        $kuotaStyles['desktop']['padding_y'] = 24;
        if ($gap = $getter('landing_section_gap_px')) {
            $parsedGap = (int) $gap;
            $kuotaStyles['desktop']['section_gap'] = ($parsedGap >= 60) ? 24 : $parsedGap;
        }
        if ($radius = $getter('landing_card_radius_px')) {
            $kuotaStyles['desktop']['card_radius'] = (int) $radius;
        }
        if ($cardPad = $getter('landing_card_padding_px')) {
            $kuotaStyles['desktop']['card_padding'] = (int) $cardPad;
        }

        // Bangun blok Tahapan dari legacy keys
        $tahapanDef = BlockRegistry::get('tahapan');
        $tahapanSteps = $jsonGetter('landing_tahapan_steps', $tahapanDef?->defaultContent['steps'] ?? []);
        $tahapanContent = array_merge($tahapanDef?->defaultContent ?? [], [
            'heading' => $getter('landing_tahapan_heading', 'Bingung cara mendaftarnya?, berikut adalah Tahapan Pendaftaran Magang'),
            'summary' => $getter('landing_tahapan_summary', $tahapanDef?->defaultContent['summary'] ?? ''),
            'btn_pdf_text' => $getter('landing_tahapan_btn_pdf', 'Unduh Panduan PDF'),
            'btn_peta_text' => $getter('landing_tahapan_btn_peta', 'Peta Alur Interaktif'),
            'steps' => $tahapanSteps,
        ]);

        // Bangun blok FAQ dari legacy keys
        $faqDef = BlockRegistry::get('faq');
        $faqItems = $jsonGetter('landing_faq_data', []);
        $faqContent = [
            'heading' => $getter('landing_faq_heading', 'Bingung? Berikut Beberapa Pertanyaan Yang Sering Ditanyakan'),
            'items' => $faqItems,
        ];
        $faqStyles = $faqDef?->defaultStyles ?? [];
        if ($faqTitleSize = $getter('landing_faq_title_size_px')) {
            $faqStyles['desktop']['title_size'] = (int) $faqTitleSize;
        }

        // Bangun blok Footer dari legacy keys
        $footerDef = BlockRegistry::get('footer');
        $footerContent = [
            'alamat' => $getter('landing_footer_alamat', 'Jl Parasamya (Komplek Kantor Bupati Sleman) Beran, Tridadi, Sleman, Yogyakarta 55511'),
            'telepon' => $getter('landing_footer_telepon', '(0274) 868405'),
            'email' => $getter('landing_footer_email', 'kominfo@slemankab.go.id'),
            'jam_layanan' => $getter('landing_footer_jam_layanan', 'Senin sampai Kamis 07.30 sampai 15.30 WIB, Jumat 07.30 sampai 14.30 WIB'),
            'copyright' => $getter('landing_footer_copyright', 'Dinas Komunikasi dan Informatika Kabupaten Sleman. Seluruh hak cipta dilindungi.'),
            'medsos' => $jsonGetter('landing_footer_medsos', []),
            'tautan' => $jsonGetter('landing_footer_tautan', []),
            'akun' => $jsonGetter('landing_footer_akun', []),
        ];
        $footerStyles = $footerDef?->defaultStyles ?? [];
        if ($footerPad = $getter('landing_footer_padding_y_px')) {
            $footerStyles['desktop']['padding_y'] = (int) $footerPad;
        }

        // Susun blok sesuai urutan tersimpan atau urutan standar
        $typeDataMap = [
            'hero' => ['content' => $heroContent, 'styles' => $heroStyles, 'def' => $heroDef],
            'kuota' => ['content' => $kuotaContent, 'styles' => $kuotaStyles, 'def' => $kuotaDef],
            'tahapan' => ['content' => $tahapanContent, 'styles' => $tahapanDef?->defaultStyles ?? [], 'def' => $tahapanDef],
            'faq' => ['content' => $faqContent, 'styles' => $faqStyles, 'def' => $faqDef],
            'footer' => ['content' => $footerContent, 'styles' => $footerStyles, 'def' => $footerDef],
        ];

        $blocks = [];
        $order = 1;

        if (! empty($savedLayout) && is_array($savedLayout)) {
            foreach ($savedLayout as $savedItem) {
                $type = (string) ($savedItem['type'] ?? '');
                if (! BlockRegistry::has($type)) {
                    continue;
                }

                $blockData = $typeDataMap[$type] ?? null;
                $def = BlockRegistry::get($type);

                $blocks[] = [
                    'id' => (string) ($savedItem['id'] ?? 'block-'.$type.'-'.uniqid()),
                    'type' => $type,
                    'version' => $def?->schemaVersion ?? 1,
                    'visible' => isset($savedItem['visible']) ? (bool) $savedItem['visible'] : true,
                    'locked' => $def ? $def->locked : false,
                    'order' => $order++,
                    'content' => is_array($savedItem['content'] ?? null) && ! empty($savedItem['content'])
                        ? array_merge($blockData['content'] ?? ($def?->defaultContent ?? []), $savedItem['content'])
                        : ($blockData['content'] ?? ($def?->defaultContent ?? [])),
                    'styles' => is_array($savedItem['styles'] ?? null) && ! empty($savedItem['styles'])
                        ? array_merge($blockData['styles'] ?? ($def?->defaultStyles ?? []), $savedItem['styles'])
                        : ($blockData['styles'] ?? ($def?->defaultStyles ?? [])),
                    'settings' => is_array($savedItem['settings'] ?? null) ? $savedItem['settings'] : [],
                ];
            }
        }

        // Jika layout kosong, gunakan 5 section standar
        if (empty($blocks)) {
            foreach (['hero', 'kuota', 'tahapan', 'faq', 'footer'] as $type) {
                $blockData = $typeDataMap[$type];
                $def = $blockData['def'];

                $blocks[] = [
                    'id' => 'block-'.$type.'-'.substr(md5($type.'-seed'), 0, 8),
                    'type' => $type,
                    'version' => $def?->schemaVersion ?? 1,
                    'visible' => true,
                    'locked' => $def ? $def->locked : false,
                    'order' => $order++,
                    'content' => $blockData['content'],
                    'styles' => $blockData['styles'],
                    'settings' => [],
                ];
            }
        }

        // Bangun Global Styles dari legacy keys
        $globalStyles = $jsonGetter('landing_global_styles', []);
        if (empty($globalStyles)) {
            $globalStyles = BlockRegistry::getDefaultGlobalStyles();
        }
        if ($primaryColor = $getter('landing_theme_primary_color')) {
            $globalStyles['colors']['primary'] = $primaryColor;
        }
        if ($accentColor = $getter('landing_theme_accent_color')) {
            $globalStyles['colors']['accent'] = $accentColor;
        }
        if ($goldColor = $getter('landing_theme_gold_color')) {
            $globalStyles['colors']['gold'] = $goldColor;
        }
        if ($fontDisplay = $getter('landing_theme_font_display')) {
            $globalStyles['typography']['heading_font'] = $fontDisplay;
        }
        if ($fontBody = $getter('landing_theme_font_body')) {
            $globalStyles['typography']['body_font'] = $fontBody;
        }
        if ($containerW = $getter('landing_container_max_width_px')) {
            $globalStyles['layout']['container_max_width_px'] = (int) $containerW;
            $globalStyles['layout']['container_width'] = (int) $containerW;
        }
        if ($sectionGap = $getter('landing_section_gap_px')) {
            $globalStyles['layout']['section_gap_px'] = (int) $sectionGap;
            $globalStyles['layout']['section_spacing'] = (int) $sectionGap;
        }
        if ($cardRad = $getter('landing_card_radius_px')) {
            $globalStyles['layout']['card_radius_px'] = (int) $cardRad;
            $globalStyles['layout']['card_radius'] = (int) $cardRad;
        }

        return [
            'schema_version' => 1,
            'page_id' => 'landing-magkom',
            'meta' => [
                'title' => $getter('landing_seo_title', 'Portal Magang Diskominfo Sleman'),
                'description' => $getter('landing_seo_description', 'Pendaftaran dan Pengelolaan Magang Dinas Kominfo Kabupaten Sleman'),
                'brand_name' => $getter('landing_header_brand_name', 'MAGKOM'),
                'brand_sub' => $getter('landing_header_brand_sub', 'Portal Magang · Diskominfo Sleman'),
            ],
            'global_styles' => $globalStyles,
            'blocks' => $blocks,
        ];
    }

    /**
     * Menyinkronkan kunci-kunci legacy di tabel pengaturan dari sebuah Page Document.
     * Mencegah CMS Form Builder 8-tab menampilkan data kadaluwarsa ketika editor mengubah dokumen.
     *
     * @param  array<string, mixed>  $document
     * @param  bool  $draft  Apakah menyimpan ke draf (draft_*) atau live
     */
    public function syncLegacyKeysFromDocument(array $document, bool $draft = false): void
    {
        $prefix = $draft ? 'draft_' : '';
        $blocks = $document['blocks'] ?? [];

        foreach ($blocks as $block) {
            $type = $block['type'] ?? '';
            $content = is_array($block['content'] ?? null) ? $block['content'] : [];
            $styles = is_array($block['styles'] ?? null) ? $block['styles'] : [];

            if ($type === 'hero') {
                if (isset($content['kicker'])) {
                    Pengaturan::set($prefix.'landing_hero_kicker', (string) $content['kicker']);
                }
                if (isset($content['title'])) {
                    Pengaturan::set($prefix.'landing_hero_title', (string) $content['title']);
                }
                if (isset($content['year'])) {
                    Pengaturan::set($prefix.'landing_hero_year', (string) $content['year']);
                }
                if (isset($content['period_info'])) {
                    Pengaturan::set($prefix.'landing_hero_period_info', (string) $content['period_info']);
                }
                if (isset($content['cta_primary_text'])) {
                    Pengaturan::set($prefix.'landing_hero_cta_text', (string) $content['cta_primary_text']);
                }
                if (isset($content['cta_primary_url'])) {
                    Pengaturan::set($prefix.'landing_hero_cta_primary_url', (string) $content['cta_primary_url']);
                }
                if (isset($content['cta_secondary_text'])) {
                    Pengaturan::set($prefix.'landing_hero_cta_secondary_text', (string) $content['cta_secondary_text']);
                }
                if (isset($content['cta_secondary_url'])) {
                    Pengaturan::set($prefix.'landing_hero_cta_secondary_url', (string) $content['cta_secondary_url']);
                }
                if (isset($content['label_persyaratan'])) {
                    Pengaturan::set($prefix.'landing_label_persyaratan', (string) $content['label_persyaratan']);
                }
                if (isset($content['persyaratan_umum'])) {
                    Pengaturan::set($prefix.'landing_persyaratan_umum', (string) $content['persyaratan_umum']);
                }
                if (isset($content['label_dokumen'])) {
                    Pengaturan::set($prefix.'landing_label_dokumen', (string) $content['label_dokumen']);
                }
                if (isset($content['dokumen_pendaftaran'])) {
                    Pengaturan::set($prefix.'landing_dokumen_pendaftaran', (string) $content['dokumen_pendaftaran']);
                }
                if (isset($content['image']) && is_string($content['image']) && $content['image'] !== '') {
                    Pengaturan::set($prefix.'landing_hero_image', (string) $content['image']);
                }

                // Styles
                if (isset($styles['desktop']['title_size'])) {
                    Pengaturan::set($prefix.'landing_hero_title_size_px', (string) $styles['desktop']['title_size']);
                }
                if (isset($styles['desktop']['desc_size'])) {
                    Pengaturan::set($prefix.'landing_hero_desc_size_px', (string) $styles['desktop']['desc_size']);
                }
                if (isset($styles['desktop']['padding_y'])) {
                    Pengaturan::set($prefix.'landing_hero_padding_y_px', (string) $styles['desktop']['padding_y']);
                }
                if (isset($styles['desktop']['image_height'])) {
                    Pengaturan::set($prefix.'landing_hero_image_height_px', (string) $styles['desktop']['image_height']);
                }
            } elseif ($type === 'kuota') {
                if (isset($content['heading'])) {
                    Pengaturan::set($prefix.'landing_kuota_heading', (string) $content['heading']);
                }
                if (isset($content['subheading'])) {
                    Pengaturan::set($prefix.'landing_kuota_subheading', (string) $content['subheading']);
                }
                if (isset($styles['desktop']['section_gap'])) {
                    Pengaturan::set($prefix.'landing_section_gap_px', (string) $styles['desktop']['section_gap']);
                }
                if (isset($styles['desktop']['card_radius'])) {
                    Pengaturan::set($prefix.'landing_card_radius_px', (string) $styles['desktop']['card_radius']);
                }
                if (isset($styles['desktop']['card_padding'])) {
                    Pengaturan::set($prefix.'landing_card_padding_px', (string) $styles['desktop']['card_padding']);
                }
            } elseif ($type === 'tahapan') {
                if (isset($content['heading'])) {
                    Pengaturan::set($prefix.'landing_tahapan_heading', (string) $content['heading']);
                }
                if (isset($content['summary'])) {
                    Pengaturan::set($prefix.'landing_tahapan_summary', (string) $content['summary']);
                }
                if (isset($content['steps']) && is_array($content['steps'])) {
                    Pengaturan::setJson($prefix.'landing_tahapan_steps', $content['steps']);
                }
            } elseif ($type === 'faq') {
                if (isset($content['heading'])) {
                    Pengaturan::set($prefix.'landing_faq_heading', (string) $content['heading']);
                }
                if (isset($content['items']) && is_array($content['items'])) {
                    Pengaturan::setJson($prefix.'landing_faq_data', $content['items']);
                }
                if (isset($styles['desktop']['title_size'])) {
                    Pengaturan::set($prefix.'landing_faq_title_size_px', (string) $styles['desktop']['title_size']);
                }
            } elseif ($type === 'footer') {
                if (isset($content['alamat'])) {
                    Pengaturan::set($prefix.'landing_footer_alamat', (string) $content['alamat']);
                }
                if (isset($content['telepon'])) {
                    Pengaturan::set($prefix.'landing_footer_telepon', (string) $content['telepon']);
                }
                if (isset($content['email'])) {
                    Pengaturan::set($prefix.'landing_footer_email', (string) $content['email']);
                }
                if (isset($content['jam_layanan'])) {
                    Pengaturan::set($prefix.'landing_footer_jam_layanan', (string) $content['jam_layanan']);
                }
                if (isset($content['copyright'])) {
                    Pengaturan::set($prefix.'landing_footer_copyright', (string) $content['copyright']);
                }
                if (isset($styles['desktop']['padding_y'])) {
                    Pengaturan::set($prefix.'landing_footer_padding_y_px', (string) $styles['desktop']['padding_y']);
                }
            }
        }

        // Sinkronisasi Meta Dokumen
        if (isset($document['meta']['brand_name'])) {
            Pengaturan::set($prefix.'landing_header_brand_name', (string) $document['meta']['brand_name']);
        }
        if (isset($document['meta']['brand_sub'])) {
            Pengaturan::set($prefix.'landing_header_brand_sub', (string) $document['meta']['brand_sub']);
        }

        // Sinkronisasi Global Styles ke Legacy Keys
        if (! empty($document['global_styles']) && is_array($document['global_styles'])) {
            $gs = $document['global_styles'];
            Pengaturan::setJson($prefix.'landing_global_styles', $gs);
            if (! empty($gs['colors']['primary'])) {
                Pengaturan::set($prefix.'landing_theme_primary_color', (string) $gs['colors']['primary']);
            }
            if (! empty($gs['colors']['accent'])) {
                Pengaturan::set($prefix.'landing_theme_accent_color', (string) $gs['colors']['accent']);
            }
            if (! empty($gs['colors']['gold'])) {
                Pengaturan::set($prefix.'landing_theme_gold_color', (string) $gs['colors']['gold']);
            }
            if (! empty($gs['typography']['heading_font'])) {
                Pengaturan::set($prefix.'landing_theme_font_display', (string) $gs['typography']['heading_font']);
            }
            if (! empty($gs['typography']['body_font'])) {
                Pengaturan::set($prefix.'landing_theme_font_body', (string) $gs['typography']['body_font']);
            }
            $cW = $gs['layout']['container_max_width_px'] ?? ($gs['layout']['container_width'] ?? null);
            if ($cW !== null) {
                Pengaturan::set($prefix.'landing_container_max_width_px', (string) $cW);
            }
            $sGap = $gs['layout']['section_gap_px'] ?? ($gs['layout']['section_spacing'] ?? null);
            if ($sGap !== null) {
                Pengaturan::set($prefix.'landing_section_gap_px', (string) $sGap);
            }
            $cRad = $gs['layout']['card_radius_px'] ?? ($gs['layout']['card_radius'] ?? null);
            if ($cRad !== null) {
                Pengaturan::set($prefix.'landing_card_radius_px', (string) $cRad);
            }
        }
    }

    /**
     * Helper statis untuk kemudahan akses konversi legacy ke dokumen.
     */
    public static function legacySettingsToDocument(bool $draft = false): array
    {
        return app(self::class)->convertLegacyToDocument($draft);
    }

    /**
     * Helper statis untuk menyinkronkan dokumen ke pengaturan legacy.
     */
    public static function syncDocumentToLegacySettings(array $document, bool $draft = false): void
    {
        app(self::class)->syncLegacyKeysFromDocument($document, $draft);
    }

    /**
     * Memeriksa apakah migrasi awal dari legacy ke Page Document diperlukan.
     */
    public function isInitialMigrationNeeded(): bool
    {
        $liveDoc = Pengaturan::getLive('landing_page_document');

        return empty($liveDoc);
    }

    /**
     * Menjalankan migrasi awal idempotent dari legacy keys ke landing_page_document.
     */
    public function runInitialMigration(): bool
    {
        if (! $this->isInitialMigrationNeeded()) {
            return false; // Sudah termigrasi, abaikan
        }

        return DB::transaction(function () {
            $document = $this->convertLegacyToDocument(draft: false);
            $validator = new BlockSchemaValidator;
            $cleanDoc = $validator->validateDocument($document);

            Pengaturan::setJson('landing_page_document', $cleanDoc);
            Pengaturan::clearCache();

            return true;
        });
    }
}
