<?php

declare(strict_types=1);

namespace App\Services\LandingPage;

/**
 * Registry resmi seluruh jenis blok yang diperbolehkan di Landing Page MAGKOM.
 * Menyediakan definisi tunggal server-side yang juga diekspor ke frontend untuk mencegah schema drift.
 */
class BlockRegistry
{
    /**
     * Cache internal daftar definisi blok.
     *
     * @var array<string, BlockDefinition>|null
     */
    protected static ?array $definitions = null;

    /**
     * Memuat dan menginisialisasi seluruh definisi blok resmi sistem.
     *
     * @return array<string, BlockDefinition>
     */
    public static function getAll(): array
    {
        if (self::$definitions !== null) {
            return self::$definitions;
        }

        self::$definitions = [
            'hero' => new BlockDefinition(
                type: 'hero',
                label: 'Hero Banner',
                description: 'Section utama Landing Page berisi judul periode, informasi pendaftaran, berkas, dan foto dokumentasi resmi.',
                icon: '🔷',
                category: 'Layout',
                schemaVersion: 1,
                defaultContent: [
                    'kicker' => 'Kabupaten Sleman',
                    'title' => "Program Magang\nKominfo Sleman",
                    'subtitle' => 'Wujudkan generasi muda yang kompeten di era digital bersama Dinas Komunikasi dan Informatika Kabupaten Sleman.',
                    'year' => '2026',
                    'period_info' => 'Pendaftaran 1–15 Okt 2026 · mulai 2 Nov 2026',
                    'cta_primary_text' => 'Daftar Sekarang',
                    'cta_primary_url' => '#kuota',
                    'cta_secondary_text' => 'Pelajari Lebih Lanjut',
                    'cta_secondary_url' => '#tahapan',
                    'label_persyaratan' => 'Persyaratan Umum',
                    'persyaratan_umum' => "Siswa SMK aktif atau mahasiswa aktif D3/D4/S1 dengan jurusan yang relevan\nBersedia magang secara luring (WFO) di kantor Diskominfo Sleman\nMengikuti jadwal dan durasi magang yang telah ditentukan",
                    'label_dokumen' => 'Dokumen Pendaftaran',
                    'dokumen_pendaftaran' => "Surat Pengantar Sekolah / Perguruan Tinggi\nCurriculum Vitae (CV)\nTranskrip / Rapor Nilai Terakhir\nPas Foto",
                    'image' => '',
                    'overlay_active' => true,
                ],
                defaultStyles: [
                    'desktop' => [
                        'padding_y' => 0,
                        'title_size' => 34,
                        'desc_size' => 15,
                        'image_height' => 340,
                        'text_align' => 'left',
                    ],
                    'tablet' => [
                        'padding_y' => 0,
                        'title_size' => 30,
                        'desc_size' => 14,
                        'image_height' => 300,
                        'text_align' => 'left',
                    ],
                    'mobile' => [
                        'padding_y' => 0,
                        'title_size' => 26,
                        'desc_size' => 14,
                        'image_height' => 260,
                        'text_align' => 'center',
                    ],
                ],
                responsiveSupport: ['padding_y', 'title_size', 'desc_size', 'image_height', 'text_align'],
                allowedOperations: ['move_up', 'move_down', 'hide', 'reset_style'],
                inspectorSchema: [
                    'content' => ['kicker', 'title', 'year', 'period_info', 'cta_primary_text', 'cta_primary_url', 'cta_secondary_text', 'cta_secondary_url', 'image', 'overlay_active'],
                    'styles' => ['title_size', 'desc_size', 'padding_y', 'image_height'],
                ],
                rendererView: 'public.blocks.hero',
                locked: true,
                repeatable: false
            ),

            'informasi' => new BlockDefinition(
                type: 'informasi',
                label: 'Persyaratan & Berkas',
                description: 'Section informasi persyaratan umum dan kelengkapan dokumen administrasi wajib pendaftar.',
                icon: '📋',
                category: 'Konten',
                schemaVersion: 1,
                defaultContent: [
                    'label_persyaratan' => 'Persyaratan Umum',
                    'persyaratan_umum' => "Siswa SMK aktif atau mahasiswa aktif D3/D4/S1 dengan jurusan yang relevan\nBersedia magang secara luring (WFO) di kantor Diskominfo Sleman\nMendapat surat izin resmi dari sekolah / perguruan tinggi",
                    'label_dokumen' => 'Dokumen Pendaftaran',
                    'dokumen_pendaftaran' => "Surat Pengantar Sekolah / Perguruan Tinggi\nCurriculum Vitae (CV) Terbaru\nTranskrip Nilai / Rapor Terakhir\nPas Foto Formal",
                ],
                defaultStyles: [
                    'desktop' => ['padding_y' => 50, 'card_radius' => 12],
                    'tablet' => ['padding_y' => 40, 'card_radius' => 12],
                    'mobile' => ['padding_y' => 30, 'card_radius' => 8],
                ],
                responsiveSupport: ['padding_y', 'card_radius'],
                allowedOperations: ['move_up', 'move_down', 'duplicate', 'delete', 'hide', 'reset_style'],
                inspectorSchema: [
                    'content' => ['label_persyaratan', 'persyaratan_umum', 'label_dokumen', 'dokumen_pendaftaran'],
                    'styles' => ['padding_y', 'card_radius'],
                ],
                rendererView: 'public.blocks.informasi',
                locked: false,
                repeatable: true
            ),

            'kuota' => new BlockDefinition(
                type: 'kuota',
                label: 'Kuota & Formasi',
                description: 'Tabel interaktif ketersediaan kuota formasi bidang, filter jenjang, dan bulan magang.',
                icon: '📊',
                category: 'Konten',
                schemaVersion: 1,
                defaultContent: [
                    'heading' => 'Pilih Penempatan & Ketersediaan Kuota Magang',
                    'subheading' => 'Pilih jenjang pendidikan, tentukan bulan mulai, lalu pilih bidang penempatan dan jurusanmu untuk langsung mengamankan kuota pendaftaran.',
                    'q1_label' => '1. Kamu siapa?',
                    'pill_smk' => 'Siswa SMK',
                    'pill_mhs' => 'Mahasiswa (D3/S1)',
                    'q2_label' => '2. Mau mulai kapan?',
                    'empty_note' => 'Belum ada periode magang aktif.',
                    'q3_label' => '3. Magang sama siapa?',
                    'pill_mandiri' => 'Individu (1 Orang)',
                    'pill_tim' => 'Kelompok (Tim)',
                    'team_label' => 'Berapa orang dalam tim kamu?',
                    'team_note' => 'Cukup 1 perwakilan/ketua yang mendaftar. Data rekan tim dimasukkan langsung di form.',
                    'gauge_desc' => 'Memuat informasi ketersediaan kuota...',
                    'legend_tersedia' => 'Masih banyak',
                    'legend_hampir' => 'Tinggal sedikit',
                    'legend_penuh' => 'Penuh',
                    'btn_tersedia' => 'Daftar Sekarang ➔',
                    'btn_waiting' => 'Daftar Waiting List ➔',
                    'btn_tutup' => 'Pendaftaran Ditutup',
                ],
                defaultStyles: [
                    'desktop' => ['padding_y' => 24, 'section_gap' => 24, 'card_radius' => 12, 'card_padding' => 24],
                    'tablet' => ['padding_y' => 20, 'section_gap' => 20, 'card_radius' => 12, 'card_padding' => 20],
                    'mobile' => ['padding_y' => 16, 'section_gap' => 16, 'card_radius' => 8, 'card_padding' => 16],
                ],
                responsiveSupport: ['padding_y', 'section_gap', 'card_radius', 'card_padding'],
                allowedOperations: ['move_up', 'move_down', 'hide', 'reset_style'],
                inspectorSchema: [
                    'content' => ['heading', 'subheading'],
                    'styles' => ['padding_y', 'section_gap', 'card_radius', 'card_padding'],
                ],
                rendererView: 'public.blocks.kuota',
                locked: false,
                repeatable: false
            ),

            'tahapan' => new BlockDefinition(
                type: 'tahapan',
                label: 'Alur & Tahapan',
                description: 'Pohon alur 4 tahapan seleksi, pelaksanaan logbook, hingga penyelesaian magang.',
                icon: '🔄',
                category: 'Layout',
                schemaVersion: 1,
                defaultContent: [
                    'heading' => 'Bingung cara mendaftarnya?, berikut adalah Tahapan Pendaftaran Magang',
                    'summary' => "Pertama calon peserta membuat akun, mengisi data pribadi, dan menunggu verifikasi akun oleh admin.\nKedua calon peserta memilih bidang dan mengunggah berkas, lalu admin melakukan verifikasi dan seleksi. Hasilnya diumumkan di portal MAGKOM.\nKetiga peserta melaksanakan magang, mengerjakan tugas, dan mengisi logbook sesuai durasi yang ditentukan.\nKeempat setelah magang selesai, peserta menyelesaikan program magang, mengunggah laporan akhir, dan dinyatakan lulus secara resmi.",
                    'btn_pdf_text' => 'Unduh Panduan PDF',
                    'btn_peta_text' => 'Peta Alur Interaktif',
                    'steps' => [
                        ['step' => '01', 'title' => 'Buat Akun & Profil', 'badge' => 'REGISTRASI', 'desc' => 'Daftar akun peserta dengan email aktif, lengkapi identitas diri dan asal institusi pendidikan.'],
                        ['step' => '02', 'title' => 'Pilih Bidang & Berkas', 'badge' => 'SELEKSI', 'desc' => 'Pilih bidang peminatan sesuai jurusan dan unggah seluruh dokumen persyaratan yang diminta.'],
                        ['step' => '03', 'title' => 'Pelaksanaan & Logbook', 'badge' => 'MAGANG', 'desc' => 'Jalani kegiatan magang secara luring, kerjakan tugas dari pembimbing, dan catat logbook harian.'],
                        ['step' => '04', 'title' => 'Selesai Magang', 'badge' => 'KELULUSAN', 'desc' => 'Penyelesaian program magang, pengumpulan laporan akhir, dan pelepasan resmi peserta oleh dinas.'],
                    ],
                ],
                defaultStyles: [
                    'desktop' => ['padding_y' => 64, 'card_radius' => 12],
                    'tablet' => ['padding_y' => 48, 'card_radius' => 12],
                    'mobile' => ['padding_y' => 36, 'card_radius' => 8],
                ],
                responsiveSupport: ['padding_y', 'card_radius'],
                allowedOperations: ['move_up', 'move_down', 'hide', 'reset_style'],
                inspectorSchema: [
                    'content' => ['heading', 'summary', 'btn_pdf_text', 'btn_peta_text'],
                    'styles' => ['padding_y', 'card_radius'],
                ],
                rendererView: 'public.blocks.tahapan',
                locked: false,
                repeatable: false
            ),

            'faq' => new BlockDefinition(
                type: 'faq',
                label: 'FAQ Accordion',
                description: 'Daftar pertanyaan yang sering diajukan lengkap dengan akordeon interaktif per kategori.',
                icon: '❓',
                category: 'Konten',
                schemaVersion: 1,
                defaultContent: [
                    'heading' => 'Bingung? Berikut Beberapa Pertanyaan Yang Sering Ditanyakan',
                    'items' => [],
                ],
                defaultStyles: [
                    'desktop' => ['padding_y' => 64, 'title_size' => 18, 'card_radius' => 12],
                    'tablet' => ['padding_y' => 48, 'title_size' => 17, 'card_radius' => 12],
                    'mobile' => ['padding_y' => 36, 'title_size' => 16, 'card_radius' => 8],
                ],
                responsiveSupport: ['padding_y', 'title_size', 'card_radius'],
                allowedOperations: ['move_up', 'move_down', 'duplicate', 'delete', 'hide', 'reset_style'],
                inspectorSchema: [
                    'content' => ['heading'],
                    'styles' => ['padding_y', 'title_size', 'card_radius'],
                ],
                rendererView: 'public.blocks.faq',
                locked: false,
                repeatable: true
            ),

            'cta_banner' => new BlockDefinition(
                type: 'cta_banner',
                label: 'Banner Promosi CTA',
                description: 'Kotak penutup interaktif dengan tombol ajakan bertindak untuk mendaftar sekarang.',
                icon: '📢',
                category: 'Konten',
                schemaVersion: 1,
                defaultContent: [
                    'heading' => 'Siap Bergabung Bersama Diskominfo Sleman?',
                    'description' => 'Dapatkan pengalaman berharga di instansi pemerintah dan kembangkan keahlian teknologi informasi Anda.',
                    'btn_text' => 'Daftar Sekarang',
                    'btn_url' => '#kuota',
                ],
                defaultStyles: [
                    'desktop' => ['padding_y' => 50, 'card_radius' => 16],
                    'tablet' => ['padding_y' => 40, 'card_radius' => 12],
                    'mobile' => ['padding_y' => 30, 'card_radius' => 10],
                ],
                responsiveSupport: ['padding_y', 'card_radius'],
                allowedOperations: ['move_up', 'move_down', 'duplicate', 'delete', 'hide', 'reset_style'],
                inspectorSchema: [
                    'content' => ['heading', 'description', 'btn_text', 'btn_url'],
                    'styles' => ['padding_y', 'card_radius'],
                ],
                rendererView: 'public.blocks.cta_banner',
                locked: false,
                repeatable: true
            ),

            'features' => new BlockDefinition(
                type: 'features',
                label: 'Keunggulan Program',
                description: 'Tiga kartu sorot keunggulan program magang (mentor profesional, proyek riil, portofolio kedinasan).',
                icon: '⭐',
                category: 'Konten',
                schemaVersion: 1,
                defaultContent: [
                    'heading' => 'Mengapa Memilih Magang di Kominfo Sleman?',
                    'items' => [
                        ['title' => 'Mentor Ahli ASN', 'desc' => 'Bimbingan intensif dari praktisi teknologi dan komunikasi bersertifikasi.', 'icon' => '👥'],
                        ['title' => 'Proyek Riil & Nyata', 'desc' => 'Terlibat langsung dalam layanan publik berbasis digital Kabupaten Sleman.', 'icon' => '💻'],
                        ['title' => 'Penyelesaian & Portofolio', 'desc' => 'Pengalaman kerja riil dan portofolio proyek pemerintahan untuk karir profesional.', 'icon' => '🏆'],
                    ],
                ],
                defaultStyles: [
                    'desktop' => ['padding_y' => 60, 'card_radius' => 12],
                    'tablet' => ['padding_y' => 48, 'card_radius' => 12],
                    'mobile' => ['padding_y' => 36, 'card_radius' => 8],
                ],
                responsiveSupport: ['padding_y', 'card_radius'],
                allowedOperations: ['move_up', 'move_down', 'duplicate', 'delete', 'hide', 'reset_style'],
                inspectorSchema: [
                    'content' => ['heading'],
                    'styles' => ['padding_y', 'card_radius'],
                ],
                rendererView: 'public.blocks.features',
                locked: false,
                repeatable: true
            ),

            'spacer' => new BlockDefinition(
                type: 'spacer',
                label: 'Spacer / Jarak',
                description: 'Pemisah spasi kosong vertikal antar section dengan ukuran pixel yang dapat diatur.',
                icon: '↕️',
                category: 'Layout',
                schemaVersion: 1,
                defaultContent: [
                    'height' => 40,
                ],
                defaultStyles: [
                    'desktop' => ['height' => 40],
                    'tablet' => ['height' => 30],
                    'mobile' => ['height' => 20],
                ],
                responsiveSupport: ['height'],
                allowedOperations: ['move_up', 'move_down', 'duplicate', 'delete', 'hide', 'reset_style'],
                inspectorSchema: [
                    'content' => ['height'],
                    'styles' => ['height'],
                ],
                rendererView: 'public.blocks.spacer',
                locked: false,
                repeatable: true
            ),

            'footer' => new BlockDefinition(
                type: 'footer',
                label: 'Footer & Kontak Dinas',
                description: 'Informasi resmi kantor dinas, jam operasional, link sosial media, dan hak cipta.',
                icon: '🏢',
                category: 'Layout',
                schemaVersion: 1,
                defaultContent: [
                    'alamat' => 'Jl Parasamya (Komplek Kantor Bupati Sleman) Beran, Tridadi, Sleman, Yogyakarta 55511',
                    'telepon' => '(0274) 868405',
                    'email' => 'kominfo@slemankab.go.id',
                    'jam_layanan' => 'Senin sampai Kamis 07.30 sampai 15.30 WIB, Jumat 07.30 sampai 14.30 WIB',
                    'copyright' => 'Dinas Komunikasi dan Informatika Kabupaten Sleman. Seluruh hak cipta dilindungi.',
                ],
                defaultStyles: [
                    'desktop' => ['padding_y' => 64],
                    'tablet' => ['padding_y' => 48],
                    'mobile' => ['padding_y' => 36],
                ],
                responsiveSupport: ['padding_y'],
                allowedOperations: ['move_up', 'move_down', 'hide', 'reset_style'],
                inspectorSchema: [
                    'content' => ['alamat', 'telepon', 'email', 'jam_layanan', 'copyright'],
                    'styles' => ['padding_y'],
                ],
                rendererView: 'public.blocks.footer',
                locked: true,
                repeatable: false
            ),
        ];

        return self::$definitions;
    }

    /**
     * Mengambil satu definisi blok berdasarkan tipe.
     */
    public static function get(string $type): ?BlockDefinition
    {
        $all = self::getAll();

        return $all[$type] ?? null;
    }

    /**
     * Memeriksa apakah tipe blok tertentu terdaftar dan sah.
     */
    public static function has(string $type): bool
    {
        return isset(self::getAll()[$type]);
    }

    /**
     * Mengambil daftar tipe blok yang terdaftar secara resmi.
     *
     * @return array<int, string>
     */
    public static function getRegisteredTypes(): array
    {
        return array_keys(self::getAll());
    }

    /**
     * Mengambil konten bawaan (default content) untuk tipe blok tertentu.
     *
     * @return array<string, mixed>
     */
    public static function getDefaultContent(string $type): array
    {
        $def = self::get($type);

        return $def ? $def->defaultContent : [];
    }

    /**
     * Mengambil gaya bawaan (default styles) untuk tipe blok tertentu.
     *
     * @return array<string, mixed>
     */
    public static function getDefaultStyles(string $type): array
    {
        $def = self::get($type);

        return $def ? $def->defaultStyles : [];
    }

    /**
     * Mengambil skema inspector untuk tipe blok tertentu.
     *
     * @return array<string, mixed>
     */
    public static function getSchema(string $type): array
    {
        $def = self::get($type);

        return $def ? $def->inspectorSchema : [];
    }

    /**
     * Memeriksa apakah blok tertentu boleh diduplikasi/diulang lebih dari satu instance.
     */
    public static function isRepeatable(string $type): bool
    {
        $def = self::get($type);

        return $def ? $def->repeatable : false;
    }

    /**
     * Memeriksa apakah blok tertentu berstatus locked (wajib ada, tidak boleh dihapus).
     */
    public static function isLocked(string $type): bool
    {
        $def = self::get($type);

        return $def ? $def->locked : false;
    }

    /**
     * Mengambil daftar operasi yang diperbolehkan untuk blok tertentu.
     *
     * @return array<int, string>
     */
    public static function getAllowedOperations(string $type): array
    {
        $def = self::get($type);

        return $def ? $def->allowedOperations : [];
    }

    /**
     * Mengambil nama view Blade parsial untuk rendering blok.
     */
    public static function getRendererView(string $type): ?string
    {
        $def = self::get($type);

        return $def ? $def->rendererView : null;
    }

    /**
     * Membuat struktur Page Document default standar yang memuat 5 section inti.
     *
     * @return array<string, mixed>
     */
    public static function getDefaultDocument(): array
    {
        $defaultOrder = ['hero', 'kuota', 'tahapan', 'faq', 'footer'];
        $blocks = [];
        $order = 1;

        foreach ($defaultOrder as $type) {
            $def = self::get($type);
            if (! $def) {
                continue;
            }

            $blocks[] = [
                'id' => 'block-'.$type.'-'.substr(md5($type.'-seed'), 0, 8),
                'type' => $type,
                'version' => $def->schemaVersion,
                'visible' => true,
                'locked' => $def->locked,
                'order' => $order++,
                'content' => $def->defaultContent,
                'styles' => $def->defaultStyles,
                'settings' => [],
            ];
        }

        return [
            'schema_version' => 1,
            'page_id' => 'landing-magkom',
            'title' => 'Landing Page Portal Magang Kominfo Sleman',
            'meta' => [
                'title' => 'Portal Magang Diskominfo Sleman',
                'description' => 'Sistem Informasi Manajemen Magang Dinas Komunikasi dan Informatika Kabupaten Sleman',
            ],
            'global_styles' => self::getDefaultGlobalStyles(),
            'blocks' => $blocks,
        ];
    }

    /**
     * Dapatkan konfigurasi bawaan Global Styles untuk Landing Page.
     *
     * @return array<string, mixed>
     */
    public static function getDefaultGlobalStyles(): array
    {
        return [
            'colors' => [
                'primary' => '#1B3860',
                'secondary' => '#1E293B',
                'accent' => '#2563EB',
                'gold' => '#F59E0B',
                'background' => '#FFFFFF',
                'surface' => '#F8FAFC',
                'card' => '#FFFFFF',
                'text' => '#1E293B',
                'muted' => '#64748B',
                'text_muted' => '#64748B',
                'border' => '#E2E8F0',
                'success' => '#10B981',
                'warning' => '#F59E0B',
            ],
            'typography' => [
                'heading_font' => 'Lexend',
                'body_font' => 'Plus Jakarta Sans',
                'base_font_size' => 16,
                'base_size_px' => 16,
                'heading_scale' => 1.25,
                'line_height' => 1.6,
                'font_weight' => 'normal',
            ],
            'layout' => [
                'container_width' => 1280,
                'container_max_width_px' => 1280,
                'section_spacing' => 64,
                'section_gap_px' => 64,
                'card_radius' => 12,
                'card_radius_px' => 12,
                'button_radius' => 8,
                'shadow' => 'soft',
                'card_shadow' => 'soft',
            ],
            'button' => [
                'primary_bg' => '#2563EB',
                'primary_text' => '#FFFFFF',
                'secondary_bg' => '#F1F5F9',
                'secondary_text' => '#0F172A',
                'border_radius' => 8,
                'padding_y' => 12,
                'padding_x' => 24,
            ],
            'buttons' => [
                'primary_bg' => '#2563EB',
                'primary_text' => '#FFFFFF',
                'secondary_bg' => '#F1F5F9',
                'secondary_text' => '#0F172A',
                'radius_px' => 8,
                'padding_y_px' => 12,
                'padding_x_px' => 24,
            ],
        ];
    }

    /**
     * Dapatkan preset gaya desain siap pakai (Style Presets).
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getStylePresets(): array
    {
        return [
            'colors' => [
                'magkom_official' => [
                    'name' => 'MAGKOM Official',
                    'description' => 'Palet resmi Kabupaten Sleman dengan Navy formal dan aksen biru modern.',
                    'values' => [
                        'primary' => '#0F172A',
                        'secondary' => '#1E293B',
                        'accent' => '#2563EB',
                        'gold' => '#F59E0B',
                        'background' => '#FFFFFF',
                        'surface' => '#F8FAFC',
                        'text' => '#1E293B',
                        'muted' => '#64748B',
                        'border' => '#E2E8F0',
                    ],
                ],
                'navy_professional' => [
                    'name' => 'Navy Professional',
                    'description' => 'Kesan korporat kedinasan yang terpercaya dengan kontras tajam.',
                    'values' => [
                        'primary' => '#1E3A8A',
                        'secondary' => '#172554',
                        'accent' => '#3B82F6',
                        'gold' => '#D97706',
                        'background' => '#F8FAFC',
                        'surface' => '#FFFFFF',
                        'text' => '#0F172A',
                        'muted' => '#475569',
                        'border' => '#CBD5E1',
                    ],
                ],
                'blue_modern' => [
                    'name' => 'Blue Modern',
                    'description' => 'Nuansa digital cerah dan energik untuk generasi muda tech-savvy.',
                    'values' => [
                        'primary' => '#0369A1',
                        'secondary' => '#075985',
                        'accent' => '#0284C7',
                        'gold' => '#EAB308',
                        'background' => '#F0F9FF',
                        'surface' => '#FFFFFF',
                        'text' => '#0C4A6E',
                        'muted' => '#64748B',
                        'border' => '#BAE6FD',
                    ],
                ],
                'neutral_government' => [
                    'name' => 'Neutral Government',
                    'description' => 'Palet monokromatik netral standar portal pelayanan publik pemerintah.',
                    'values' => [
                        'primary' => '#334155',
                        'secondary' => '#1E293B',
                        'accent' => '#475569',
                        'gold' => '#B45309',
                        'background' => '#F1F5F9',
                        'surface' => '#FFFFFF',
                        'text' => '#1E293B',
                        'muted' => '#64748B',
                        'border' => '#E2E8F0',
                    ],
                ],
            ],
            'buttons' => [
                'rounded' => [
                    'name' => 'Rounded',
                    'border_radius' => 8,
                    'padding_y' => 12,
                    'padding_x' => 24,
                ],
                'pill' => [
                    'name' => 'Pill',
                    'border_radius' => 9999,
                    'padding_y' => 12,
                    'padding_x' => 28,
                ],
                'sharp' => [
                    'name' => 'Sharp',
                    'border_radius' => 2,
                    'padding_y' => 12,
                    'padding_x' => 24,
                ],
            ],
            'cards' => [
                'standard' => [
                    'name' => 'Standard',
                    'card_radius' => 12,
                    'shadow' => 'soft',
                ],
                'soft' => [
                    'name' => 'Soft Flow',
                    'card_radius' => 16,
                    'shadow' => 'medium',
                ],
                'minimal' => [
                    'name' => 'Minimalist',
                    'card_radius' => 4,
                    'shadow' => 'none',
                ],
            ],
            'typography' => [
                'official' => [
                    'name' => 'Official Sleman',
                    'heading_font' => 'Lexend',
                    'body_font' => 'Plus Jakarta Sans',
                    'base_font_size' => 16,
                ],
                'modern' => [
                    'name' => 'Modern Tech',
                    'heading_font' => 'Inter',
                    'body_font' => 'Inter',
                    'base_font_size' => 16,
                ],
                'compact' => [
                    'name' => 'Compact Clean',
                    'heading_font' => 'Poppins',
                    'body_font' => 'Plus Jakarta Sans',
                    'base_font_size' => 15,
                ],
            ],
        ];
    }

    /**
     * Mengekspor konfigurasi registry ke struktur array aman untuk konsumsi frontend JavaScript.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function toFrontendConfig(): array
    {
        $result = [];
        foreach (self::getAll() as $type => $def) {
            $result[$type] = [
                'type' => $def->type,
                'label' => $def->label,
                'description' => $def->description,
                'icon' => $def->icon,
                'category' => $def->category,
                'schema_version' => $def->schemaVersion,
                'locked' => $def->locked,
                'repeatable' => $def->repeatable,
                'allowed_operations' => $def->allowedOperations,
                'responsive_support' => $def->responsiveSupport,
                'default_content' => $def->defaultContent,
                'default_styles' => $def->defaultStyles,
            ];
        }

        return [
            'schema_version' => 1,
            'categories' => ['Tata Letak', 'Konten', 'Media', 'Navigasi'],
            'blocks' => $result,
            'global_styles' => self::getDefaultGlobalStyles(),
            'presets' => self::getStylePresets(),
        ];
    }
}
