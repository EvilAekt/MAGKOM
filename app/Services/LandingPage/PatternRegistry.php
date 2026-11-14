<?php

declare(strict_types=1);

namespace App\Services\LandingPage;

use App\Models\User;
use App\Support\Pengaturan;
use Carbon\CarbonImmutable;
use Illuminate\Validation\ValidationException;

/**
 * Registry Pattern resmi untuk Landing Page MAGKOM.
 * Mengelola koleksi Pola Desain (System Patterns) bawaan Pemkab Sleman dan Custom Patterns.
 */
class PatternRegistry
{
    /**
     * Cache internal daftar pola sistem.
     *
     * @var array<string, array<string, mixed>>|null
     */
    protected static ?array $systemPatterns = null;

    /**
     * Daftar seluruh kategori pola resmi yang didukung.
     *
     * @return array<int, string>
     */
    public static function getCategories(): array
    {
        return ['Hero', 'Informasi', 'FAQ', 'Timeline'];
    }

    /**
     * Dapatkan seluruh pola bawaan sistem MAGKOM (System Patterns).
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getSystemPatterns(): array
    {
        if (self::$systemPatterns !== null) {
            return self::$systemPatterns;
        }

        self::$systemPatterns = [
            // =========================================================================
            // KATEGORI A: HERO PATTERNS
            // =========================================================================
            'hero_split_right' => [
                'id' => 'pattern-hero-split-right',
                'key' => 'hero_split_right',
                'name' => 'Hero Banner Split Kanan',
                'description' => 'Hero dengan teks headline di kiri, tombol aksi, dan foto kantor/dokumentasi di sisi kanan.',
                'category' => 'Hero',
                'thumbnail' => '/images/patterns/hero-split-right.svg',
                'schema_version' => '1.1',
                'is_system' => true,
                'blocks' => [
                    [
                        'type' => 'hero',
                        'title' => 'Hero Banner',
                        'content' => [
                            'kicker' => 'Dinas Kominfo Sleman',
                            'title' => "Program Magang\nKominfo Sleman",
                            'subtitle' => 'Wujudkan generasi muda yang kompeten di era digital bersama Pemerintah Kabupaten Sleman.',
                            'year' => '2026',
                            'period_info' => 'Pendaftaran Semester II 2026 · Terbuka Siswa SMK & Mahasiswa',
                            'cta_primary_text' => 'Daftar Sekarang',
                            'cta_primary_url' => '#kuota',
                            'cta_secondary_text' => 'Pelajari Alur',
                            'cta_secondary_url' => '#tahapan',
                            'overlay_active' => true,
                        ],
                        'styles' => [
                            'desktop' => ['padding_y' => 80, 'title_size' => 48, 'desc_size' => 16, 'image_height' => 420, 'text_align' => 'left'],
                            'tablet' => ['padding_y' => 60, 'title_size' => 38, 'desc_size' => 15, 'image_height' => 340, 'text_align' => 'left'],
                            'mobile' => ['padding_y' => 40, 'title_size' => 28, 'desc_size' => 14, 'image_height' => 260, 'text_align' => 'center'],
                        ],
                    ],
                ],
            ],

            'hero_split_left' => [
                'id' => 'pattern-hero-split-left',
                'key' => 'hero_split_left',
                'name' => 'Hero Banner Split Kiri',
                'description' => 'Hero dengan foto fasilitas di sebelah kiri dan informasi ringkasan persyaratan di sebelah kanan.',
                'category' => 'Hero',
                'thumbnail' => '/images/patterns/hero-split-left.svg',
                'schema_version' => '1.1',
                'is_system' => true,
                'blocks' => [
                    [
                        'type' => 'hero',
                        'title' => 'Hero Banner (Foto Kiri)',
                        'content' => [
                            'kicker' => 'Smart Regency Sleman',
                            'title' => "Pengalaman Magang Nyata\ndi Pemerintahan Daerah",
                            'subtitle' => 'Ikuti magang berbasis proyek riil dengan bimbingan langsung mentor ASN profesional.',
                            'cta_primary_text' => 'Lihat Ketersediaan Kuota',
                            'cta_primary_url' => '#kuota',
                            'cta_secondary_text' => 'Unduh Panduan PDF',
                            'cta_secondary_url' => '#tahapan',
                            'overlay_active' => true,
                        ],
                        'styles' => [
                            'desktop' => ['padding_y' => 80, 'title_size' => 44, 'desc_size' => 16, 'image_height' => 400, 'text_align' => 'left'],
                            'tablet' => ['padding_y' => 60, 'title_size' => 36, 'desc_size' => 15, 'image_height' => 320, 'text_align' => 'left'],
                            'mobile' => ['padding_y' => 40, 'title_size' => 26, 'desc_size' => 14, 'image_height' => 240, 'text_align' => 'center'],
                        ],
                    ],
                ],
            ],

            'hero_background_image' => [
                'id' => 'pattern-hero-bg-image',
                'key' => 'hero_background_image',
                'name' => 'Hero Layar Penuh dengan Background Image',
                'description' => 'Hero berlatar belakang foto penuh dengan filter gelap transparan (navy overlay) untuk keterbacaan optimal.',
                'category' => 'Hero',
                'thumbnail' => '/images/patterns/hero-bg-image.svg',
                'schema_version' => '1.1',
                'is_system' => true,
                'blocks' => [
                    [
                        'type' => 'hero',
                        'title' => 'Hero Layar Penuh',
                        'content' => [
                            'kicker' => 'Kabupaten Sleman · Yogyakarta',
                            'title' => "Transformasi Digital Bersama\nDiskominfo Sleman",
                            'subtitle' => 'Tingkatkan keahlian praktis dalam jaringan, pengembangan sistem, media informasi, dan tata kelola SPBE.',
                            'cta_primary_text' => 'Daftar Sekarang ➔',
                            'cta_primary_url' => '#kuota',
                            'overlay_active' => true,
                        ],
                        'styles' => [
                            'desktop' => ['padding_y' => 100, 'title_size' => 52, 'desc_size' => 18, 'image_height' => 500, 'text_align' => 'center'],
                            'tablet' => ['padding_y' => 80, 'title_size' => 40, 'desc_size' => 16, 'image_height' => 400, 'text_align' => 'center'],
                            'mobile' => ['padding_y' => 50, 'title_size' => 30, 'desc_size' => 14, 'image_height' => 300, 'text_align' => 'center'],
                        ],
                    ],
                ],
            ],

            'hero_centered' => [
                'id' => 'pattern-hero-centered',
                'key' => 'hero_centered',
                'name' => 'Hero Minimalis Rata Tengah',
                'description' => 'Desain hero terfokus dan elegan dengan teks rata tengah serta tombol pendaftaran ganda.',
                'category' => 'Hero',
                'thumbnail' => '/images/patterns/hero-centered.svg',
                'schema_version' => '1.1',
                'is_system' => true,
                'blocks' => [
                    [
                        'type' => 'hero',
                        'title' => 'Hero Minimalis Tengah',
                        'content' => [
                            'kicker' => 'Portal MAGKOM 2026',
                            'title' => 'Tingkatkan Kompetensi Digital Anda',
                            'subtitle' => 'Bergabung dalam ekosistem pemerintahan cerdas dan kembangkan portofolio profesional Anda.',
                            'cta_primary_text' => 'Mulai Registrasi',
                            'cta_primary_url' => '#kuota',
                            'cta_secondary_text' => 'Pelajari Persyaratan',
                            'cta_secondary_url' => '#tahapan',
                            'overlay_active' => false,
                        ],
                        'styles' => [
                            'desktop' => ['padding_y' => 70, 'title_size' => 46, 'desc_size' => 17, 'text_align' => 'center'],
                            'tablet' => ['padding_y' => 50, 'title_size' => 36, 'desc_size' => 15, 'text_align' => 'center'],
                            'mobile' => ['padding_y' => 35, 'title_size' => 26, 'desc_size' => 14, 'text_align' => 'center'],
                        ],
                    ],
                ],
            ],

            'hero_with_kuota_info' => [
                'id' => 'pattern-hero-with-kuota',
                'key' => 'hero_with_kuota_info',
                'name' => 'Hero dengan Highlight Kuota & Periode',
                'description' => 'Hero terpadu dengan highlight ringkasan periode magang dan ketersediaan kuota formasi.',
                'category' => 'Hero',
                'thumbnail' => '/images/patterns/hero-with-kuota.svg',
                'schema_version' => '1.1',
                'is_system' => true,
                'blocks' => [
                    [
                        'type' => 'hero',
                        'title' => 'Hero Kuota',
                        'content' => [
                            'kicker' => 'Pendaftaran Magang Aktif',
                            'title' => "Peluang Magang ASN\nDiskominfo Sleman",
                            'subtitle' => 'Kuota terbatas untuk SMK dan Mahasiswa aktif. Pantau kuota jurusan Anda secara real-time.',
                            'period_info' => 'Periode Pendaftaran Terbuka Sepanjang Tahun Sesuai Kuota',
                            'cta_primary_text' => 'Cek Kuota Jurusan',
                            'cta_primary_url' => '#kuota',
                            'overlay_active' => true,
                        ],
                        'styles' => [
                            'desktop' => ['padding_y' => 80, 'title_size' => 48, 'desc_size' => 16, 'image_height' => 420],
                            'tablet' => ['padding_y' => 60, 'title_size' => 38, 'desc_size' => 15, 'image_height' => 340],
                            'mobile' => ['padding_y' => 40, 'title_size' => 28, 'desc_size' => 14, 'image_height' => 260],
                        ],
                    ],
                ],
            ],

            // =========================================================================
            // KATEGORI B: INFORMATION PATTERNS
            // =========================================================================
            'info_two_columns' => [
                'id' => 'pattern-info-two-columns',
                'key' => 'info_two_columns',
                'name' => 'Dua Kolom Informasi & Berkas',
                'description' => 'Tata letak dua kolom terpisah: kartu persyaratan kualifikasi umum di kiri dan daftar berkas wajib di kanan.',
                'category' => 'Informasi',
                'thumbnail' => '/images/patterns/info-two-columns.svg',
                'schema_version' => '1.1',
                'is_system' => true,
                'blocks' => [
                    [
                        'type' => 'informasi',
                        'title' => 'Persyaratan & Berkas Administrasi',
                        'content' => [
                            'label_persyaratan' => 'Persyaratan Kualifikasi Umum',
                            'persyaratan_umum' => "Siswa SMK aktif atau mahasiswa aktif D3/D4/S1 jurusan relevan (TI, SI, RPL, TKJ, DKV, Komunikasi)\nBersedia magang secara luring (WFO) di kantor Diskominfo Sleman sesuai jam kerja ASN\nMendapat rekomendasi atau surat pengantar resmi dari pihak institusi pendidikan",
                            'label_dokumen' => 'Kelengkapan Dokumen Wajib',
                            'dokumen_pendaftaran' => "Surat Pengantar Sekolah / Kampus (PDF)\nCurriculum Vitae (CV) Terbaru (PDF)\nTranskrip Nilai / Rapor Terakhir (PDF)\nPas Foto Formal Terbaru (JPG/PNG)",
                        ],
                        'styles' => [
                            'desktop' => ['padding_y' => 60, 'card_radius' => 12],
                            'tablet' => ['padding_y' => 45, 'card_radius' => 12],
                            'mobile' => ['padding_y' => 30, 'card_radius' => 8],
                        ],
                    ],
                ],
            ],

            'info_three_cards' => [
                'id' => 'pattern-info-three-cards',
                'key' => 'info_three_cards',
                'name' => 'Tiga Kartu Sorotan Program',
                'description' => 'Tiga kartu ringkas menyajikan pilar utama kegiatan: Mentoring Ahli, Proyek Nyata, dan Portofolio Kedinasan.',
                'category' => 'Informasi',
                'thumbnail' => '/images/patterns/info-three-cards.svg',
                'schema_version' => '1.1',
                'is_system' => true,
                'blocks' => [
                    [
                        'type' => 'features',
                        'title' => 'Sorotan Kegiatan Magang',
                        'content' => [
                            'heading' => 'Pengalaman Belajar Profesional di Lingkungan Pemerintahan',
                            'items' => [
                                ['title' => 'Mentor Ahli ASN', 'desc' => 'Didampingi langsung oleh staf pranata komputer dan pranata humas pemerintah yang berpengalaman.', 'icon' => '👥'],
                                ['title' => 'Implementasi Proyek Riil', 'desc' => 'Berpartisipasi aktif dalam perancangan aplikasi, infrastruktur jaringan, konten publik, dan tata kelola SPBE.', 'icon' => '💻'],
                                ['title' => 'Penyelesaian & Portofolio', 'desc' => 'Portofolio proyek nyata dan pengalaman kedinasan yang diakui instansi pemerintah.', 'icon' => '🏆'],
                            ],
                        ],
                        'styles' => [
                            'desktop' => ['padding_y' => 64, 'card_radius' => 12],
                            'tablet' => ['padding_y' => 48, 'card_radius' => 12],
                            'mobile' => ['padding_y' => 36, 'card_radius' => 8],
                        ],
                    ],
                ],
            ],

            'info_with_icons' => [
                'id' => 'pattern-info-with-icons',
                'key' => 'info_with_icons',
                'name' => 'Informasi Fasilitas & Bimbingan Berikon',
                'description' => 'Kartu informasi yang diperkaya dengan ikon visual untuk menjelaskan fasilitas dan hak peserta.',
                'category' => 'Informasi',
                'thumbnail' => '/images/patterns/info-with-icons.svg',
                'schema_version' => '1.1',
                'is_system' => true,
                'blocks' => [
                    [
                        'type' => 'features',
                        'title' => 'Fasilitas & Pendampingan',
                        'content' => [
                            'heading' => 'Fasilitas Kerja yang Diberikan untuk Peserta',
                            'items' => [
                                ['title' => 'Ruang Kerja Representatif', 'desc' => 'Area kerja nyaman di lingkungan Kompleks Pemda Sleman dengan akses internet berkecepatan tinggi.', 'icon' => '🏢'],
                                ['title' => 'Bimbingan Logbook Harian', 'desc' => 'Sistem evaluasi berkala dan feedback logbook mingguan dari pembimbing teknis ASN.', 'icon' => '📝'],
                                ['title' => 'Peluang Kolaborasi Antar Bidang', 'desc' => 'Dapat berkolaborasi dengan berbagai divisi teknologi informasi, persandian, dan komunikasi publik.', 'icon' => '🤝'],
                            ],
                        ],
                        'styles' => [
                            'desktop' => ['padding_y' => 60, 'card_radius' => 14],
                            'tablet' => ['padding_y' => 45, 'card_radius' => 12],
                            'mobile' => ['padding_y' => 30, 'card_radius' => 8],
                        ],
                    ],
                ],
            ],

            'info_checklist' => [
                'id' => 'pattern-info-checklist',
                'key' => 'info_checklist',
                'name' => 'Informasi Checklist Tata Tertib',
                'description' => 'Format checklist terstruktur untuk tata tertib kehadiran, integritas data, dan komitmen magang.',
                'category' => 'Informasi',
                'thumbnail' => '/images/patterns/info-checklist.svg',
                'schema_version' => '1.1',
                'is_system' => true,
                'blocks' => [
                    [
                        'type' => 'informasi',
                        'title' => 'Checklist Tata Tertib Peserta',
                        'content' => [
                            'label_persyaratan' => 'Ketentuan Disiplin & Kehadiran',
                            'persyaratan_umum' => "Hadir tepat waktu sesuai jadwal kerja kedinasan (07.30 - 15.30 WIB)\nMengisi logbook kegiatan harian secara tertib di portal MAGKOM setiap hari kerja\nMengenakan pakaian sopan dan rapi sesuai ketentuan instansi pemerintah",
                            'label_dokumen' => 'Kerahasiaan & Integritas Data',
                            'dokumen_pendaftaran' => "Menjaga kerahasiaan informasi dan keamanan sistem milik Diskominfo Sleman\nTidak menyalahgunakan hak akses atau data instansi untuk kepentingan pribadi\nMenyerahkan hasil laporan akhir magang kepada pembimbing sebelum batas waktu kelulusan",
                        ],
                        'styles' => [
                            'desktop' => ['padding_y' => 50, 'card_radius' => 10],
                            'tablet' => ['padding_y' => 40, 'card_radius' => 10],
                            'mobile' => ['padding_y' => 30, 'card_radius' => 8],
                        ],
                    ],
                ],
            ],

            'info_requirements' => [
                'id' => 'pattern-info-requirements',
                'key' => 'info_requirements',
                'name' => 'Persyaratan Jenjang SMK & Mahasiswa',
                'description' => 'Perbandingan rincian syarat antara jenjang kejuruan SMK dan jenjang perguruan tinggi D3/S1.',
                'category' => 'Informasi',
                'thumbnail' => '/images/patterns/info-requirements.svg',
                'schema_version' => '1.1',
                'is_system' => true,
                'blocks' => [
                    [
                        'type' => 'informasi',
                        'title' => 'Ketentuan Berdasarkan Jenjang',
                        'content' => [
                            'label_persyaratan' => 'Jenjang Siswa SMK (Kejuruan)',
                            'persyaratan_umum' => "Minimal duduk di kelas XI atau XII SMK dengan jurusan terkait teknologi informasi atau multimedia\nDurasi magang 3 hingga 6 bulan sesuai kurikulum PKL sekolah\nMelampirkan surat permohonan PKL resmi dari Kepala Sekolah",
                            'label_dokumen' => 'Jenjang Mahasiswa (D3 / D4 / S1)',
                            'dokumen_pendaftaran' => "Minimal telah menempuh semester 5 dengan IPK minimal 3.00\nDurasi magang 1 hingga 3 bulan sesuai program studi atau program MBKM\nMelampirkan proposal magang / surat pengantar Dekan atau Ketua Program Studi",
                        ],
                        'styles' => [
                            'desktop' => ['padding_y' => 60, 'card_radius' => 12],
                            'tablet' => ['padding_y' => 45, 'card_radius' => 12],
                            'mobile' => ['padding_y' => 30, 'card_radius' => 8],
                        ],
                    ],
                ],
            ],

            // =========================================================================
            // KATEGORI C: FAQ PATTERNS
            // =========================================================================
            'faq_simple_accordion' => [
                'id' => 'pattern-faq-simple',
                'key' => 'faq_simple_accordion',
                'name' => 'FAQ Accordion Sederhana',
                'description' => 'Akordeon tanya-jawab ringkas 4 pertanyaan utama yang sering ditanyakan oleh calon peserta.',
                'category' => 'FAQ',
                'thumbnail' => '/images/patterns/faq-simple.svg',
                'schema_version' => '1.1',
                'is_system' => true,
                'blocks' => [
                    [
                        'type' => 'faq',
                        'title' => 'Pertanyaan Umum',
                        'content' => [
                            'heading' => 'Pertanyaan yang Sering Diajukan (FAQ)',
                        ],
                        'styles' => [
                            'desktop' => ['padding_y' => 64, 'title_size' => 18, 'card_radius' => 12],
                            'tablet' => ['padding_y' => 48, 'title_size' => 17, 'card_radius' => 12],
                            'mobile' => ['padding_y' => 36, 'title_size' => 16, 'card_radius' => 8],
                        ],
                    ],
                ],
            ],

            'faq_categorized' => [
                'id' => 'pattern-faq-categorized',
                'key' => 'faq_categorized',
                'name' => 'FAQ Berdasarkan Kategori Lengkap',
                'description' => 'Akordeon komprehensif yang dikelompokkan ke dalam kategori Syarat, Akun, dan Pelaksanaan.',
                'category' => 'FAQ',
                'thumbnail' => '/images/patterns/faq-categorized.svg',
                'schema_version' => '1.1',
                'is_system' => true,
                'blocks' => [
                    [
                        'type' => 'faq',
                        'title' => 'Kategori FAQ Lengkap',
                        'content' => [
                            'heading' => 'Temukan Jawaban Cepat Seputar Program Magang Kami',
                        ],
                        'styles' => [
                            'desktop' => ['padding_y' => 70, 'title_size' => 18, 'card_radius' => 14],
                            'tablet' => ['padding_y' => 50, 'title_size' => 17, 'card_radius' => 12],
                            'mobile' => ['padding_y' => 35, 'title_size' => 16, 'card_radius' => 8],
                        ],
                    ],
                ],
            ],

            'faq_two_columns' => [
                'id' => 'pattern-faq-two-columns',
                'key' => 'faq_two_columns',
                'name' => 'FAQ Dua Kolom Terpisah',
                'description' => 'Tata letak tanya jawab dua kolom untuk memisahkan pertanyaan teknis akun dan pertanyaan teknis tugas.',
                'category' => 'FAQ',
                'thumbnail' => '/images/patterns/faq-two-columns.svg',
                'schema_version' => '1.1',
                'is_system' => true,
                'blocks' => [
                    [
                        'type' => 'faq',
                        'title' => 'FAQ Dua Kolom',
                        'content' => [
                            'heading' => 'Pusat Bantuan & Panduan Peserta',
                        ],
                        'styles' => [
                            'desktop' => ['padding_y' => 60, 'title_size' => 17, 'card_radius' => 12],
                            'tablet' => ['padding_y' => 45, 'title_size' => 16, 'card_radius' => 12],
                            'mobile' => ['padding_y' => 30, 'title_size' => 15, 'card_radius' => 8],
                        ],
                    ],
                ],
            ],

            // =========================================================================
            // KATEGORI D: TIMELINE PATTERNS
            // =========================================================================
            'timeline_horizontal' => [
                'id' => 'pattern-timeline-horizontal',
                'key' => 'timeline_horizontal',
                'name' => 'Timeline Horizontal 4 Tahapan',
                'description' => 'Tahapan alur pendaftaran berjejer ke samping dari Registrasi, Seleksi, Pelaksanaan, hingga Kelulusan.',
                'category' => 'Timeline',
                'thumbnail' => '/images/patterns/timeline-horizontal.svg',
                'schema_version' => '1.1',
                'is_system' => true,
                'blocks' => [
                    [
                        'type' => 'tahapan',
                        'title' => 'Alur Pendaftaran Horizontal',
                        'content' => [
                            'heading' => 'Tahapan Lengkap Pelaksanaan Magang di Diskominfo Sleman',
                            'summary' => 'Alur pelaksanaan magang dirancang transparan dan terintegrasi melalui portal MAGKOM dari pendaftaran awal hingga penetapan status kelulusan resmi.',
                            'btn_pdf_text' => 'Unduh Panduan PDF',
                            'btn_peta_text' => 'Peta Alur Interaktif',
                            'steps' => [
                                ['step' => '01', 'title' => 'Buat Akun & Registrasi', 'badge' => 'REGISTRASI', 'desc' => 'Daftarkan akun portal resmi, isi identitas data diri, dan verifikasi email Anda.'],
                                ['step' => '02', 'title' => 'Pilih Formasi & Seleksi', 'badge' => 'SELEKSI', 'desc' => 'Pilih bidang dan periode bulan, unggah berkas, lalu tunggu hasil verifikasi admin.'],
                                ['step' => '03', 'title' => 'Pelaksanaan & Logbook', 'badge' => 'PELAKSANAAN', 'desc' => 'Jalani magang luring di Diskominfo Sleman, kerjakan tugas, dan isi logbook harian.'],
                                ['step' => '04', 'title' => 'Selesai Magang', 'badge' => 'KELULUSAN', 'desc' => 'Penyelesaian program magang, pengesahan laporan akhir, dan pelepasan resmi peserta oleh dinas.'],
                            ],
                        ],
                        'styles' => [
                            'desktop' => ['padding_y' => 64, 'card_radius' => 12],
                            'tablet' => ['padding_y' => 48, 'card_radius' => 12],
                            'mobile' => ['padding_y' => 36, 'card_radius' => 8],
                        ],
                    ],
                ],
            ],

            'timeline_vertical' => [
                'id' => 'pattern-timeline-vertical',
                'key' => 'timeline_vertical',
                'name' => 'Timeline Vertikal Alur Kegiatan',
                'description' => 'Visualisasi tahapan pendaftaran ke bawah dengan penomoran langkah yang jelas dan deskripsi komprehensif.',
                'category' => 'Timeline',
                'thumbnail' => '/images/patterns/timeline-vertical.svg',
                'schema_version' => '1.1',
                'is_system' => true,
                'blocks' => [
                    [
                        'type' => 'tahapan',
                        'title' => 'Alur Kegiatan Vertikal',
                        'content' => [
                            'heading' => 'Langkah Demi Langkah Mengikuti Program Magang Kami',
                            'summary' => 'Ikuti panduan langkah demi langkah untuk mempermudah proses seleksi berkas hingga penetapan jadwal magang.',
                            'btn_pdf_text' => 'Unduh Dokumen Panduan',
                            'btn_peta_text' => 'Lihat Alur Interaktif',
                        ],
                        'styles' => [
                            'desktop' => ['padding_y' => 60, 'card_radius' => 12],
                            'tablet' => ['padding_y' => 45, 'card_radius' => 12],
                            'mobile' => ['padding_y' => 30, 'card_radius' => 8],
                        ],
                    ],
                ],
            ],

            'timeline_tahapan_resmi' => [
                'id' => 'pattern-timeline-resmi',
                'key' => 'timeline_tahapan_resmi',
                'name' => 'Tahapan Pendaftaran Resmi SOP Diskominfo Sleman',
                'description' => 'Tahapan standar sesuai SOP kedinasan Diskominfo Sleman dilengkapi tombol panduan resmi dan peta interaktif.',
                'category' => 'Timeline',
                'thumbnail' => '/images/patterns/timeline-resmi.svg',
                'schema_version' => '1.1',
                'is_system' => true,
                'blocks' => [
                    [
                        'type' => 'tahapan',
                        'title' => 'SOP Tahapan Magang',
                        'content' => [
                            'heading' => 'Alur Resmi Pelaksanaan Magang Mahasiswa & Siswa SMK',
                            'btn_pdf_text' => 'Buku Panduan Magang (PDF)',
                            'btn_peta_text' => 'Buka Peta Alur',
                        ],
                        'styles' => [
                            'desktop' => ['padding_y' => 70, 'card_radius' => 14],
                            'tablet' => ['padding_y' => 50, 'card_radius' => 12],
                            'mobile' => ['padding_y' => 35, 'card_radius' => 8],
                        ],
                    ],
                ],
            ],
        ];

        return self::$systemPatterns;
    }

    /**
     * Mengambil daftar Pola Kustom (Custom Patterns) yang dibuat oleh pengguna.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getCustomPatterns(): array
    {
        $raw = Pengaturan::getLive('landing_custom_patterns');
        if (! $raw) {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Mengambil seluruh pola (System + Custom).
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getAll(): array
    {
        return array_merge(self::getSystemPatterns(), self::getCustomPatterns());
    }

    /**
     * Mengambil daftar pola yang disaring berdasarkan kategori.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getByCategory(string $category): array
    {
        $all = self::getAll();
        $filtered = [];

        foreach ($all as $key => $pattern) {
            if (strcasecmp((string) ($pattern['category'] ?? ''), $category) === 0) {
                $filtered[$key] = $pattern;
            }
        }

        return $filtered;
    }

    /**
     * Mengambil satu pola berdasarkan Key atau ID.
     *
     * @return array<string, mixed>|null
     */
    public static function get(string $keyOrId): ?array
    {
        $all = self::getAll();

        if (isset($all[$keyOrId])) {
            return $all[$keyOrId];
        }

        foreach ($all as $item) {
            if (($item['id'] ?? '') === $keyOrId || ($item['key'] ?? '') === $keyOrId) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Memeriksa apakah sebuah pola merupakan Pola Bawaan Sistem (dilindungi dari penghapusan).
     */
    public static function isSystemPattern(string $keyOrId): bool
    {
        $sys = self::getSystemPatterns();

        if (isset($sys[$keyOrId])) {
            return true;
        }

        foreach ($sys as $item) {
            if (($item['id'] ?? '') === $keyOrId || ($item['key'] ?? '') === $keyOrId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Menyimpan Pola Kustom baru ke basis data.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     *
     * @throws ValidationException
     */
    public static function saveCustomPattern(array $data, ?User $user = null): array
    {
        $name = trim((string) ($data['name'] ?? ''));
        $category = trim((string) ($data['category'] ?? 'Informasi'));
        $description = trim((string) ($data['description'] ?? ''));
        $blocks = is_array($data['blocks'] ?? null) ? $data['blocks'] : [];

        if ($name === '') {
            throw ValidationException::withMessages(['name' => 'Nama pola tidak boleh kosong.']);
        }

        if (empty($blocks)) {
            throw ValidationException::withMessages(['blocks' => 'Pola harus memiliki minimal 1 blok.']);
        }

        // Validasi tipe blok
        foreach ($blocks as $b) {
            $type = (string) ($b['type'] ?? '');
            if (! BlockRegistry::has($type)) {
                throw ValidationException::withMessages(['blocks' => "Tipe blok '{$type}' di dalam pola tidak sah."]);
            }
        }

        $customs = self::getCustomPatterns();
        $id = 'custom-pattern-'.bin2hex(random_bytes(4));
        $key = 'custom_'.preg_replace('/[^a-zA-Z0-9_]/', '_', strtolower($name)).'_'.bin2hex(random_bytes(2));

        $newPattern = [
            'id' => $id,
            'key' => $key,
            'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'description' => htmlspecialchars($description, ENT_QUOTES, 'UTF-8'),
            'category' => in_array($category, self::getCategories(), true) ? $category : 'Informasi',
            'thumbnail' => '/images/patterns/custom-pattern.svg',
            'schema_version' => '1.1',
            'is_system' => false,
            'created_by' => $user?->name ?? 'Super Admin',
            'created_at' => CarbonImmutable::now('Asia/Jakarta')->toIso8601String(),
            'blocks' => $blocks,
        ];

        $customs[$key] = $newPattern;
        Pengaturan::setJson('landing_custom_patterns', $customs);

        return $newPattern;
    }

    /**
     * Menghapus Pola Kustom (Pola Sistem dilindungi dari penghapusan).
     *
     * @throws ValidationException
     */
    public static function deleteCustomPattern(string $keyOrId): bool
    {
        if (self::isSystemPattern($keyOrId)) {
            throw ValidationException::withMessages([
                'pattern' => 'Pola bawaan sistem MAGKOM dilindungi dan tidak dapat dihapus.',
            ]);
        }

        $customs = self::getCustomPatterns();
        $targetKey = null;

        if (isset($customs[$keyOrId])) {
            $targetKey = $keyOrId;
        } else {
            foreach ($customs as $k => $item) {
                if (($item['id'] ?? '') === $keyOrId) {
                    $targetKey = $k;
                    break;
                }
            }
        }

        if ($targetKey === null) {
            return false;
        }

        unset($customs[$targetKey]);
        Pengaturan::setJson('landing_custom_patterns', $customs);

        return true;
    }

    /**
     * Menginstansiasi (Clone) blok-blok dari pola menjadi Block Instances mandiri
     * dengan ID baru yang sepenuhnya unik dan independen.
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws ValidationException
     */
    public static function instantiateBlocks(string $patternKeyOrId): array
    {
        $pattern = self::get($patternKeyOrId);

        if (! $pattern) {
            throw ValidationException::withMessages([
                'pattern' => "Pola '{$patternKeyOrId}' tidak ditemukan di Pattern Registry.",
            ]);
        }

        $rawBlocks = $pattern['blocks'] ?? [];
        $instantiated = [];

        foreach ($rawBlocks as $b) {
            $type = (string) ($b['type'] ?? '');
            if (! BlockRegistry::has($type)) {
                continue;
            }

            $def = BlockRegistry::get($type);
            $newId = 'block-'.$type.'-'.bin2hex(random_bytes(4));

            // Deep clone konten dan styles untuk memastikan 100% independen
            $content = array_merge(
                $def ? $def->defaultContent : [],
                is_array($b['content'] ?? null) ? $b['content'] : []
            );

            $styles = array_merge(
                $def ? $def->defaultStyles : [],
                is_array($b['styles'] ?? null) ? $b['styles'] : []
            );

            $instantiated[] = [
                'id' => $newId,
                'type' => $type,
                'version' => $def ? $def->schemaVersion : 1,
                'visible' => true,
                'locked' => false,
                'user_locked' => false,
                'order' => 1,
                'content' => $content,
                'styles' => $styles,
                'settings' => is_array($b['settings'] ?? null) ? $b['settings'] : [],
            ];
        }

        return $instantiated;
    }
}
