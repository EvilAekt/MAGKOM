<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TipeAuditEnum;
use App\Models\User;
use App\Support\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LandingCmsService
{
    public function __construct(
        protected AuditService $auditService,
        protected LandingCmsStagingService $stagingService
    ) {}

    /**
     * Mengambil metadata draf staging saat ini.
     *
     * @return array<string, mixed>
     */
    public function getDraftMetadata(): array
    {
        return $this->stagingService->getDraftMetadata();
    }

    /**
     * Mengambil seluruh data konfigurasi Landing CMS beserta nilai default-nya.
     *
     * @return array<string, mixed>
     */
    public function getCmsData(): array
    {
        $defaultHeaderNav = [
            ['label' => 'Beranda', 'url' => '#beranda'],
            ['label' => 'Kuota', 'url' => '#kuota'],
            ['label' => 'Tahapan', 'url' => '#tahapan'],
            ['label' => 'Peta Alur', 'url' => '#tahapan'],
            ['label' => 'FAQ', 'url' => '#faq'],
        ];

        $defaultFooterAkun = [
            ['label' => 'Masuk Peserta', 'url' => '/login'],
            ['label' => 'Masuk Pembimbing', 'url' => '/login'],
            ['label' => 'Masuk Admin', 'url' => '/login'],
        ];

        $defaultFaqData = [
            [
                'id' => 'syarat',
                'nama' => 'Syarat dan Tata Cara Mendaftar',
                'items' => [
                    [
                        'q' => 'Bagaimana alur dan tata cara mendaftar magang di Diskominfo Sleman?',
                        'a' => 'Pendaftaran dilakukan secara mandiri atau kelompok melalui portal MAGKOM: (1) Buat akun portal, (2) Pilih formasi bidang dan bulan pelaksanaan yang masih tersedia kuotanya, (3) Unggah berkas persyaratan (Surat Pengantar Institusi, CV, Proposal, dan Transkrip/Rapor), (4) Tunggu verifikasi admin.',
                    ],
                    [
                        'q' => 'Apakah siswa SMK dan mahasiswa perguruan tinggi memiliki syarat yang berbeda?',
                        'a' => 'Secara umum sama, namun untuk siswa SMK surat permohonan ditandatangani oleh Kepala Sekolah / Waka Hubin dan dilengkapi izin orang tua, sedangkan mahasiswa melampirkan surat pengantar resmi dari Fakultas/Jurusan.',
                    ],
                    [
                        'q' => 'Apakah bisa mendaftar lebih dari satu bidang atau bulan magang?',
                        'a' => 'Tidak. Setiap peserta hanya dapat memilih satu bidang formasi dalam satu periode magang agar alokasi kuota adil bagi seluruh pendaftar.',
                    ],
                ],
            ],
            [
                'id' => 'akun',
                'nama' => 'Akun',
                'items' => [
                    [
                        'q' => 'Bagaimana jika tidak menerima email verifikasi akun?',
                        'a' => 'Silakan periksa folder Spam atau Promosi pada email Anda. Jika dalam 15 menit belum masuk, Anda dapat meminta kirim ulang link verifikasi melalui halaman login.',
                    ],
                    [
                        'q' => 'Apakah akun portal bisa digunakan untuk mendaftar periode berikutnya?',
                        'a' => 'Akun Anda berlaku permanen di portal MAGKOM. Anda dapat menggunakannya kembali untuk melihat riwayat aktivitas atau mendaftar periode berikutnya setelah magang sebelumnya selesai.',
                    ],
                ],
            ],
            [
                'id' => 'kuis',
                'nama' => 'Kuis',
                'items' => [
                    [
                        'q' => 'Apakah ada kuis atau tes kompetensi saat proses seleksi?',
                        'a' => 'Beberapa bidang teknis seperti Aplikasi Informatika (Aptika) dan Persandian dapat menyertakan tes kompetensi dasar atau kuis teknis singkat untuk memastikan kecocokan dengan proyek yang akan dikerjakan.',
                    ],
                    [
                        'q' => 'Berapa lama proses verifikasi berkas oleh admin?',
                        'a' => 'Proses verifikasi dan kurasi berkas administrasi membutuhkan waktu 1 hingga 3 hari kerja setelah pengajuan dikirimkan.',
                    ],
                ],
            ],
            [
                'id' => 'benefit',
                'nama' => 'Benefit Magang',
                'items' => [
                    [
                        'q' => 'Apa saja fasilitas dan benefit yang didapatkan peserta magang?',
                        'a' => 'Peserta mendapatkan bimbingan intensif dari Mentor ASN profesional, akses modul proyek nyata di pemerintahan, portofolio kerja kedinasan riil, serta pengalaman kerja standar industri.',
                    ],
                    [
                        'q' => 'Apakah program magang di Diskominfo Sleman berbayar atau digaji?',
                        'a' => 'Program magang Diskominfo Sleman 100% GRATIS (tidak dipungut biaya apa pun) dan bersifat non-komersial tanpa kompensasi uang saku (unpaid government internship).',
                    ],
                ],
            ],
            [
                'id' => 'kewajiban',
                'nama' => 'Kewajiban Peserta Magang',
                'items' => [
                    [
                        'q' => 'Apa saja kewajiban dari peserta magang?',
                        'a' => 'Peserta wajib: (1) Mematuhi jam kerja ASN Diskominfo (Senin–Kamis 07.30–15.30 WIB, Jumat 07.30–14.30 WIB), (2) Mengenakan seragam sekolah/almamater rapi dan bersepatu, (3) Mengerjakan penugasan proyek tepat waktu, (4) Mengisi logbook mingguan secara disiplin, dan (5) Menjaga etika serta kerahasiaan data kedinasan.',
                    ],
                    [
                        'q' => 'Apakah magang bisa dilaksanakan secara daring / WFH?',
                        'a' => 'Tidak. Seluruh pelaksanaan magang di Diskominfo Sleman dilaksanakan secara luring (Work From Office) di lingkungan kantor Pemkab Sleman untuk memastikan pembimbingan maksimal.',
                    ],
                ],
            ],
        ];

        $defaultTahapanSteps = [
            ['step' => '01', 'title' => 'Registrasi', 'badge' => 'REGISTRASI', 'desc' => 'Calon peserta membuat akun resmi, mengisi data pribadi, dan menunggu akun diverifikasi admin.'],
            ['step' => '02', 'title' => 'Administrasi & Seleksi', 'badge' => 'ADMINISTRASI & SELEKSI', 'desc' => 'Pilih bidang, unggah berkas, lalu admin memverifikasi dan menyeleksi dalam 1–3 hari kerja.'],
            ['step' => '03', 'title' => 'Pelaksanaan Magang', 'badge' => 'PELAKSANAAN MAGANG', 'desc' => 'Kerjakan tugas, isi logbook harian, dan unggah progres sesuai durasi magang.'],
            ['step' => '04', 'title' => 'Selesai Magang', 'badge' => 'KELULUSAN', 'desc' => 'Penyelesaian program magang, pengesahan laporan akhir, dan penetapan status kelulusan resmi.'],
        ];

        $defaultFooterMedsos = [
            ['platform' => 'Instagram', 'label' => 'kominfo_sleman', 'url' => 'https://instagram.com/kominfo_sleman'],
            ['platform' => 'Twitter / X', 'label' => 'diskominfosleman', 'url' => 'https://twitter.com/diskominfosleman'],
        ];

        $defaultFooterTautan = [
            ['label' => 'Kebijakan Privasi', 'url' => '/kebijakan-privasi'],
            ['label' => 'Situs Kominfo Sleman', 'url' => 'https://kominfo.slemankab.go.id'],
            ['label' => 'Sleman Digital', 'url' => 'https://slemankab.go.id'],
            ['label' => 'Verifikasi Dokumen Sah', 'url' => '/verifikasi-dokumen/cek'],
        ];

        $defaultKebijakanSections = [
            [
                'nomor' => 1,
                'judul' => 'Komitmen Perlindungan Data Pribadi',
                'konten' => 'Dinas Komunikasi dan Informatika (Diskominfo) Kabupaten Sleman berkomitmen penuh untuk melindungi privasi dan keamanan data pribadi calon peserta, peserta aktif, dan alumni program magang (MAGKOM). Kebijakan ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan melindungi informasi pribadi Anda.',
            ],
            [
                'nomor' => 2,
                'judul' => 'Data yang Kami Kumpulkan',
                'konten' => 'Untuk keperluan seleksi administrasi, penempatan bidang, dan penerbitan sertifikat resmi bertanda tangan elektronik (TTE), kami mengumpulkan data yang mencakup:',
                'items' => [
                    'Identitas Diri: Nama lengkap, NISN/NIM, NIK, tempat & tanggal lahir, serta foto profil resmi.',
                    'Kontak: Alamat surat elektronik (email) aktif dan nomor telepon/WhatsApp.',
                    'Afiliasi Akademik: Asal sekolah SMK/perguruan tinggi, program studi/jurusan, dan semester/tingkat kelas.',
                    'Dokumen Persyaratan: Surat pengantar resmi institusi pendidikan, proposal/curriculum vitae, transkrip nilai, dan portofolio keahlian.',
                ],
            ],
            [
                'nomor' => 3,
                'judul' => 'Keamanan Penyimpanan & Tautan Bertanda Tangan (Signed URLs)',
                'konten' => 'Seluruh berkas dokumen yang diunggah ke dalam sistem MAGKOM disimpan pada media penyimpanan yang aman (private cloud storage). Setiap berkas dokumen persyaratan dan surat diterima hanya dapat diakses melalui Signed URLs dengan batas waktu kedaluwarsa 30 menit serta diverifikasi hak kepemilikannya oleh sistem otorisasi berlapis.',
            ],
            [
                'nomor' => 4,
                'judul' => 'Hak Subjek Data Pribadi (UU PDP No. 27/2022)',
                'konten' => 'Sesuai dengan ketentuan Undang-Undang Perlindungan Data Pribadi (UU PDP No. 27 Tahun 2022), Anda memiliki hak untuk:',
                'items' => [
                    'Mendapatkan kejelasan identitas dan akuntabilitas pihak yang memproses data Anda.',
                    'Memperbarui atau memperbaiki data pribadi Anda jika terjadi kekeliruan.',
                    'Meminta penghapusan atau pemusnahan data pribadi setelah masa retensi administrasi magang berakhir.',
                ],
            ],
            [
                'nomor' => 5,
                'judul' => 'Narahubung & Hak Akses',
                'konten' => 'Apabila Anda memiliki pertanyaan, permohonan koreksi data, atau kendala terkait privasi informasi, silakan menghubungi helpdesk resmi kami melalui surel kominfo@slemankab.go.id atau datang langsung ke Kantor Diskominfo Kabupaten Sleman.',
            ],
        ];

        // Aktifkan mode pratinjau sementara agar pembacaan membaca draf yang sedang dikerjakan
        Pengaturan::setPreviewMode(true);

        $cmsData = [
            // Section 0: Header & SEO Meta
            'meta_title' => Pengaturan::get('landing_meta_title', 'MAGKOM – Portal Magang Diskominfo Sleman 2026'),
            'meta_desc' => Pengaturan::get('landing_meta_desc', 'Daftar magang di Diskominfo Sleman: lihat persyaratan, kuota per bidang, dan tahapan pendaftaran.'),
            'header_brand_name' => Pengaturan::get('landing_header_brand_name', 'MAGKOM'),
            'header_brand_sub' => Pengaturan::get('landing_header_brand_sub', 'Portal Magang · Diskominfo Sleman'),
            'header_nav' => Pengaturan::json('landing_header_nav', $defaultHeaderNav),

            // Section 1: Hero
            'hero_kicker' => Pengaturan::get('landing_hero_kicker', 'Kabupaten Sleman'),
            'hero_title' => Pengaturan::get('landing_hero_title', "Magang Kominfo\nPeriode II"),
            'hero_year' => Pengaturan::get('landing_hero_year', '2026'),
            'hero_period_info' => Pengaturan::get('landing_hero_period_info', 'Pendaftaran 1–15 Okt 2026 · mulai 2 Nov 2026'),
            'hero_cta_text' => Pengaturan::get('landing_hero_cta_text', 'Daftar Sekarang'),
            'label_persyaratan' => Pengaturan::get('landing_label_persyaratan', 'Persyaratan Umum'),
            'persyaratan_umum' => Pengaturan::get('landing_persyaratan_umum', "Siswa SMK aktif atau mahasiswa aktif D3/D4/S1 dengan jurusan yang relevan\nBersedia magang secara luring (WFO) di kantor Diskominfo Sleman\nMengikuti jadwal dan durasi magang yang telah ditentukan"),
            'label_dokumen' => Pengaturan::get('landing_label_dokumen', 'Dokumen Pendaftaran'),
            'dokumen_pendaftaran' => Pengaturan::get('landing_dokumen_pendaftaran', "Surat Pengantar Sekolah / Perguruan Tinggi\nCurriculum Vitae (CV)\nTranskrip / Rapor Nilai Terakhir\nPas Foto"),
            'hero_image' => Pengaturan::get('landing_hero_image'),
            'panduan_pdf' => Pengaturan::get('landing_panduan_pdf'),
            'panduan_pdf_original_name' => Pengaturan::get('landing_panduan_pdf_name', 'Panduan-Resmi-Magang.pdf'),

            // Section 2: Kuota & Formasi Copy
            'kuota_heading' => Pengaturan::get('landing_kuota_heading', 'Ketersediaan Kuota Magang'),
            'kuota_subheading' => Pengaturan::get('landing_kuota_subheading', 'Pilih jenjang pendidikan dan bulan mulai magang untuk melihat kuota jurusan yang tersedia.'),
            'kuota_q1_label' => Pengaturan::get('landing_kuota_q1_label', '1. Kamu siapa?'),
            'kuota_pill_smk' => Pengaturan::get('landing_kuota_pill_smk', 'Siswa SMK'),
            'kuota_pill_mhs' => Pengaturan::get('landing_kuota_pill_mhs', 'Mahasiswa (D3/S1)'),
            'kuota_q2_label' => Pengaturan::get('landing_kuota_q2_label', '2. Mau mulai kapan?'),
            'kuota_empty_note' => Pengaturan::get('landing_kuota_empty_note', 'Belum ada periode magang aktif.'),
            'kuota_q3_label' => Pengaturan::get('landing_kuota_q3_label', '3. Magang sama siapa?'),
            'kuota_pill_mandiri' => Pengaturan::get('landing_kuota_pill_mandiri', 'Individu (1 Orang)'),
            'kuota_pill_tim' => Pengaturan::get('landing_kuota_pill_tim', 'Kelompok (Tim)'),
            'kuota_team_label' => Pengaturan::get('landing_kuota_team_label', 'Berapa orang dalam tim kamu?'),
            'kuota_team_note' => Pengaturan::get('landing_kuota_team_note', 'Cukup 1 perwakilan/ketua yang mendaftar. Data rekan tim dimasukkan langsung di form.'),
            'kuota_gauge_desc' => Pengaturan::get('landing_kuota_gauge_desc', 'Memuat informasi ketersediaan kuota...'),
            'kuota_legend_tersedia' => Pengaturan::get('landing_kuota_legend_tersedia', 'Masih banyak'),
            'kuota_legend_hampir' => Pengaturan::get('landing_kuota_legend_hampir', 'Tinggal sedikit'),
            'kuota_legend_penuh' => Pengaturan::get('landing_kuota_legend_penuh', 'Penuh'),
            'kuota_btn_tersedia' => Pengaturan::get('landing_kuota_btn_tersedia', 'Daftar Sekarang ➔'),
            'kuota_btn_waiting' => Pengaturan::get('landing_kuota_btn_waiting', 'Daftar Waiting List ➔'),
            'kuota_btn_tutup' => Pengaturan::get('landing_kuota_btn_tutup', 'Pendaftaran Ditutup'),

            // Section 3: Tahapan
            'tahapan_heading' => Pengaturan::get('landing_tahapan_heading', 'Bingung cara mendaftarnya?, berikut adalah Tahapan Pendaftaran Magang'),
            'tahapan_summary' => Pengaturan::get('landing_tahapan_summary', "Pertama calon peserta membuat akun, mengisi data pribadi, dan menunggu verifikasi akun oleh admin.\nKedua calon peserta memilih bidang dan mengunggah berkas, lalu admin melakukan verifikasi dan seleksi. Hasilnya diumumkan di portal MAGKOM.\nKetiga peserta melaksanakan magang, mengerjakan tugas, dan mengisi logbook sesuai durasi yang ditentukan.\nKeempat setelah magang selesai, peserta menyelesaikan program magang, mengunggah laporan akhir, dan dinyatakan lulus secara resmi."),
            'tahapan_steps' => Pengaturan::json('landing_tahapan_steps', $defaultTahapanSteps),
            'tahapan_btn_pdf' => Pengaturan::get('landing_tahapan_btn_pdf', 'Unduh Panduan PDF'),
            'tahapan_btn_peta' => Pengaturan::get('landing_tahapan_btn_peta', 'Peta Alur Interaktif'),

            // Section 4: FAQ
            'faq_heading' => Pengaturan::get('landing_faq_heading', 'Bingung? Berikut Beberapa Pertanyaan Yang Sering Ditanyakan'),
            'faq_data' => Pengaturan::json('landing_faq_data', $defaultFaqData),

            // Section 5: Footer
            'footer_alamat' => Pengaturan::get('landing_footer_alamat', 'Jl Parasamya (Komplek Kantor Bupati Sleman) Beran, Tridadi, Sleman, Yogyakarta 55511'),
            'footer_telepon' => Pengaturan::get('landing_footer_telepon', '(0274) 868405'),
            'footer_email' => Pengaturan::get('landing_footer_email', 'kominfo@slemankab.go.id'),
            'footer_jam_layanan' => Pengaturan::get('landing_footer_jam_layanan', 'Senin sampai Kamis 07.30 sampai 15.30 WIB, Jumat 07.30 sampai 14.30 WIB'),
            'footer_medsos' => Pengaturan::json('landing_footer_medsos', $defaultFooterMedsos),
            'footer_tautan' => Pengaturan::json('landing_footer_tautan', $defaultFooterTautan),
            'footer_akun' => Pengaturan::json('landing_footer_akun', $defaultFooterAkun),
            'footer_copyright' => Pengaturan::get('landing_footer_copyright', 'Dinas Komunikasi dan Informatika Kabupaten Sleman. Seluruh hak cipta dilindungi.'),

            // Section 6: Kebijakan Privasi
            'kebijakan_badge' => Pengaturan::get('kebijakan_privasi_badge', 'Undang-Undang PDP No. 27 Tahun 2022'),
            'kebijakan_title' => Pengaturan::get('kebijakan_privasi_title', 'Kebijakan Privasi & Perlindungan Data'),
            'kebijakan_updated_at' => Pengaturan::get('kebijakan_privasi_updated_at', 'Terakhir diperbarui: 27 Oktober 2026 • Dinas Komunikasi dan Informatika Kabupaten Sleman'),
            'kebijakan_sections' => Pengaturan::json('kebijakan_privasi_sections', $defaultKebijakanSections),

            // Section 7: Tema & Desain Visual (Theming Engine)
            'theme_primary_color' => Pengaturan::get('landing_theme_primary_color', '#1B3860'),
            'theme_navy_dark' => Pengaturan::get('landing_theme_navy_dark', '#122846'),
            'theme_accent_color' => Pengaturan::get('landing_theme_accent_color', '#4F46E5'),
            'theme_gold_color' => Pengaturan::get('landing_theme_gold_color', '#F59E0B'),
            'theme_bg_color' => Pengaturan::get('landing_theme_bg_color', '#FFFFFF'),
            'theme_font_display' => Pengaturan::get('landing_theme_font_display', 'Lexend'),
            'theme_font_body' => Pengaturan::get('landing_theme_font_body', 'Source Sans 3'),
            'theme_border_radius' => Pengaturan::get('landing_theme_border_radius', '12px'),
            'theme_btn_style' => Pengaturan::get('landing_theme_btn_style', 'rounded'),
        ];

        // Nonaktifkan kembali preview mode
        Pengaturan::setPreviewMode(false);

        return $cmsData;
    }

    /**
     * Memproses pembaruan section CMS dan menyimpannya ke kontainer draf (staging) / publikasi.
     *
     * @param  array<string, mixed>  $validated
     * @return array{
     *     type: string,
     *     route?: string,
     *     params?: array<string, mixed>,
     *     message: string,
     *     status: string
     * }
     */
    public function updateSection(
        string $section,
        array $validated,
        Request $request,
        User $user,
        string $actionType
    ): array {
        $foto = null;
        $pdf = null;
        $labelSukses = 'Konten';

        if ($section === 'header_seo') {
            $dataToSave = [
                'landing_meta_title' => $validated['meta_title'],
                'landing_meta_desc' => $validated['meta_desc'],
                'landing_header_brand_name' => $validated['header_brand_name'],
                'landing_header_brand_sub' => $validated['header_brand_sub'],
            ];

            if ($request->has('nav_items') && is_array($request->input('nav_items'))) {
                $navItems = [];
                /** @var array<int, array<string, mixed>> $navInput */
                $navInput = $request->input('nav_items');
                foreach ($navInput as $item) {
                    if (! empty($item['label']) && ! empty($item['url'])) {
                        $navItems[] = [
                            'label' => trim((string) $item['label']),
                            'url' => trim((string) $item['url']),
                        ];
                    }
                }
                $dataToSave['landing_header_nav'] = $navItems;
            } elseif (! empty($validated['header_nav_json'])) {
                $decodedNav = json_decode((string) $validated['header_nav_json'], true);
                if (is_array($decodedNav)) {
                    $dataToSave['landing_header_nav'] = $decodedNav;
                }
            }

            $labelSukses = 'Konfigurasi Header & SEO Meta';
        } elseif ($section === 'kuota') {
            $dataToSave = [];
            foreach ($validated as $key => $val) {
                if (in_array($key, ['section', 'action_type'], true)) {
                    continue;
                }
                $dataToSave['landing_'.$key] = $val ?? '';
            }

            $labelSukses = 'Teks dan instruksi bagian Kuota & Formasi';
        } elseif ($section === 'tahapan') {
            $stepsData = [
                ['step' => '01', 'title' => $validated['step1_title'], 'badge' => strtoupper((string) $validated['step1_title']), 'desc' => $validated['step1_desc']],
                ['step' => '02', 'title' => $validated['step2_title'], 'badge' => strtoupper((string) $validated['step2_title']), 'desc' => $validated['step2_desc']],
                ['step' => '03', 'title' => $validated['step3_title'], 'badge' => strtoupper((string) $validated['step3_title']), 'desc' => $validated['step3_desc']],
                ['step' => '04', 'title' => $validated['step4_title'], 'badge' => strtoupper((string) $validated['step4_title']), 'desc' => $validated['step4_desc']],
            ];

            $dataToSave = [
                'landing_tahapan_heading' => $validated['tahapan_heading'],
                'landing_tahapan_summary' => $validated['tahapan_summary'],
                'landing_tahapan_steps' => $stepsData,
            ];

            if (! empty($validated['tahapan_btn_pdf'])) {
                $dataToSave['landing_tahapan_btn_pdf'] = $validated['tahapan_btn_pdf'];
            }
            if (! empty($validated['tahapan_btn_peta'])) {
                $dataToSave['landing_tahapan_btn_peta'] = $validated['tahapan_btn_peta'];
            }

            $labelSukses = 'Konten Tahapan Alur Magang';
        } elseif ($section === 'faq') {
            $hasVisualFaq = $request->has('faq_categories') && is_array($request->input('faq_categories'));

            if ($hasVisualFaq) {
                $faqData = [];
                /** @var array<int, array<string, mixed>> $categoriesInput */
                $categoriesInput = $request->input('faq_categories', []);
                foreach ($categoriesInput as $cat) {
                    if (! empty($cat['nama'])) {
                        $catName = trim((string) $cat['nama']);
                        $catId = ! empty($cat['id']) ? trim((string) $cat['id']) : Str::slug($catName);
                        $items = [];
                        if (! empty($cat['items']) && is_array($cat['items'])) {
                            foreach ($cat['items'] as $item) {
                                if (! empty($item['q']) && ! empty($item['a'])) {
                                    $items[] = [
                                        'q' => trim((string) $item['q']),
                                        'a' => trim((string) $item['a']),
                                    ];
                                }
                            }
                        }
                        $faqData[] = [
                            'id' => $catId,
                            'nama' => $catName,
                            'items' => $items,
                        ];
                    }
                }
                $dataToSave = [
                    'landing_faq_heading' => $validated['faq_heading'],
                    'landing_faq_data' => $faqData,
                ];
            } else {
                $decodedFaq = json_decode((string) $validated['faq_json'], true);
                if (! is_array($decodedFaq)) {
                    return [
                        'type' => 'back',
                        'status' => 'error',
                        'message' => 'Format JSON FAQ tidak valid. Silakan periksa kembali struktur tanda kurung dan koma.',
                    ];
                }

                $dataToSave = [
                    'landing_faq_heading' => $validated['faq_heading'],
                    'landing_faq_data' => $decodedFaq,
                ];
            }

            $labelSukses = 'Konten FAQ (Tanya Jawab)';
        } elseif ($section === 'footer') {
            $dataToSave = [
                'landing_footer_alamat' => $validated['footer_alamat'],
                'landing_footer_telepon' => $validated['footer_telepon'],
                'landing_footer_email' => $validated['footer_email'],
                'landing_footer_jam_layanan' => $validated['footer_jam_layanan'],
                'landing_footer_copyright' => $validated['footer_copyright'],
            ];

            // Medsos visual vs JSON
            if ($request->has('footer_medsos') && is_array($request->input('footer_medsos'))) {
                $medsosList = [];
                /** @var array<int, array<string, mixed>> $medsosInput */
                $medsosInput = $request->input('footer_medsos', []);
                foreach ($medsosInput as $m) {
                    if (! empty($m['platform']) && ! empty($m['label']) && ! empty($m['url'])) {
                        $medsosList[] = [
                            'platform' => trim((string) $m['platform']),
                            'label' => trim((string) $m['label']),
                            'url' => trim((string) $m['url']),
                        ];
                    }
                }
                $dataToSave['landing_footer_medsos'] = $medsosList;
            } elseif (! empty($validated['footer_medsos_json'])) {
                $decodedMedsos = json_decode((string) $validated['footer_medsos_json'], true);
                if (is_array($decodedMedsos)) {
                    $dataToSave['landing_footer_medsos'] = $decodedMedsos;
                }
            }

            // Tautan terkait visual vs JSON
            if ($request->has('footer_tautan') && is_array($request->input('footer_tautan'))) {
                $tautanList = [];
                /** @var array<int, array<string, mixed>> $tautanInput */
                $tautanInput = $request->input('footer_tautan', []);
                foreach ($tautanInput as $t) {
                    if (! empty($t['label']) && ! empty($t['url'])) {
                        $tautanList[] = [
                            'label' => trim((string) $t['label']),
                            'url' => trim((string) $t['url']),
                        ];
                    }
                }
                $dataToSave['landing_footer_tautan'] = $tautanList;
            } elseif (! empty($validated['footer_tautan_json'])) {
                $decodedTautan = json_decode((string) $validated['footer_tautan_json'], true);
                if (is_array($decodedTautan)) {
                    $dataToSave['landing_footer_tautan'] = $decodedTautan;
                }
            }

            // Tautan kolom akun visual vs JSON
            if ($request->has('footer_akun') && is_array($request->input('footer_akun'))) {
                $akunList = [];
                /** @var array<int, array<string, mixed>> $akunInput */
                $akunInput = $request->input('footer_akun', []);
                foreach ($akunInput as $a) {
                    if (! empty($a['label']) && ! empty($a['url'])) {
                        $akunList[] = [
                            'label' => trim((string) $a['label']),
                            'url' => trim((string) $a['url']),
                        ];
                    }
                }
                $dataToSave['landing_footer_akun'] = $akunList;
            } elseif (! empty($validated['footer_akun_json'])) {
                $decodedAkun = json_decode((string) $validated['footer_akun_json'], true);
                if (is_array($decodedAkun)) {
                    $dataToSave['landing_footer_akun'] = $decodedAkun;
                }
            }

            $labelSukses = 'Informasi Footer & Kontak Dinas';
        } elseif ($section === 'kebijakan') {
            $hasVisualKebijakan = $request->has('kebijakan_sections') && is_array($request->input('kebijakan_sections'));

            if ($hasVisualKebijakan) {
                $sectionsList = [];
                /** @var array<int, array<string, mixed>> $sectionsInput */
                $sectionsInput = $request->input('kebijakan_sections', []);
                foreach ($sectionsInput as $sec) {
                    if (! empty($sec['judul']) && ! empty($sec['konten'])) {
                        $secData = [
                            'nomor' => (int) ($sec['nomor'] ?? (count($sectionsList) + 1)),
                            'judul' => trim((string) $sec['judul']),
                            'konten' => trim((string) $sec['konten']),
                        ];
                        if (! empty($sec['items']) && is_array($sec['items'])) {
                            $cleanItems = [];
                            foreach ($sec['items'] as $itemStr) {
                                if (trim((string) $itemStr) !== '') {
                                    $cleanItems[] = trim((string) $itemStr);
                                }
                            }
                            if (! empty($cleanItems)) {
                                $secData['items'] = $cleanItems;
                            }
                        }
                        if (! empty($sec['highlight']) && trim((string) $sec['highlight']) !== '') {
                            $secData['highlight'] = trim((string) $sec['highlight']);
                        }
                        $sectionsList[] = $secData;
                    }
                }

                $dataToSave = [
                    'kebijakan_privasi_badge' => $validated['kebijakan_badge'],
                    'kebijakan_privasi_title' => $validated['kebijakan_title'],
                    'kebijakan_privasi_updated_at' => $validated['kebijakan_updated_at'],
                    'kebijakan_privasi_sections' => $sectionsList,
                ];
            } else {
                $decodedSections = json_decode((string) $validated['kebijakan_sections_json'], true);
                if (! is_array($decodedSections)) {
                    return [
                        'type' => 'back',
                        'status' => 'error',
                        'message' => 'Format JSON pasal Kebijakan Privasi tidak valid. Periksa sintaks JSON.',
                    ];
                }

                $dataToSave = [
                    'kebijakan_privasi_badge' => $validated['kebijakan_badge'],
                    'kebijakan_privasi_title' => $validated['kebijakan_title'],
                    'kebijakan_privasi_updated_at' => $validated['kebijakan_updated_at'],
                    'kebijakan_privasi_sections' => $decodedSections,
                ];
            }

            $labelSukses = 'Halaman Kebijakan Privasi & Perlindungan Data';
        } elseif ($section === 'theme') {
            $dataToSave = [
                'landing_theme_primary_color' => $validated['theme_primary_color'],
                'landing_theme_navy_dark' => $validated['theme_navy_dark'],
                'landing_theme_accent_color' => $validated['theme_accent_color'],
                'landing_theme_gold_color' => $validated['theme_gold_color'],
                'landing_theme_bg_color' => $validated['theme_bg_color'],
                'landing_theme_font_display' => $validated['theme_font_display'],
                'landing_theme_font_body' => $validated['theme_font_body'],
                'landing_theme_border_radius' => $validated['theme_border_radius'],
                'landing_theme_btn_style' => $validated['theme_btn_style'],
            ];

            $labelSukses = 'Tema & Desain Visual Landing Page';
        } else {
            // Default section: 'hero'
            $dataToSave = [
                'landing_hero_kicker' => $validated['hero_kicker'] ?? 'Kabupaten Sleman',
                'landing_hero_title' => $validated['hero_title'],
                'landing_hero_year' => $validated['hero_year'] ?? '2026',
                'landing_hero_period_info' => $validated['hero_period_info'] ?? '',
                'landing_hero_cta_text' => $validated['hero_cta_text'] ?? 'Daftar Sekarang',
                'landing_label_persyaratan' => $validated['label_persyaratan'] ?? 'Persyaratan Umum',
                'landing_persyaratan_umum' => $validated['persyaratan_umum'] ?? '',
                'landing_label_dokumen' => $validated['label_dokumen'] ?? 'Dokumen Pendaftaran',
                'landing_dokumen_pendaftaran' => $validated['dokumen_pendaftaran'] ?? '',
            ];

            /** @var UploadedFile|null $uploadedFoto */
            $uploadedFoto = $request->file('hero_image');
            $foto = $uploadedFoto;

            /** @var UploadedFile|null $uploadedPdf */
            $uploadedPdf = $request->file('panduan_pdf');
            $pdf = $uploadedPdf;

            $labelSukses = 'Konten Hero, media foto, dan dokumen panduan';
        }

        // 1. Selalu simpan ke dalam kontainer draf (staging sandbox)
        $this->stagingService->saveDraft(
            section: $section,
            data: $dataToSave,
            foto: $foto,
            pdf: $pdf,
            user: $user
        );

        // 2. Jika aksi adalah 'preview', arahkan ke rute pratinjau interaktif
        if ($actionType === 'preview') {
            return [
                'type' => 'redirect',
                'route' => 'superadmin.landing.preview',
                'params' => [],
                'status' => 'success',
                'message' => "Draf {$labelSukses} berhasil disimpan. Anda berada dalam mode pratinjau interaktif.",
            ];
        }

        // 3. Jika aksi adalah 'draft', simpan di sandbox dan kembali ke editor
        if ($actionType === 'draft') {
            return [
                'type' => 'redirect',
                'route' => 'superadmin.landing.index',
                'params' => ['tab' => $section],
                'status' => 'success',
                'message' => "Draf {$labelSukses} berhasil disimpan ke kontainer Sandbox (belum dipublikasikan ke publik).",
            ];
        }

        // 4. Default / 'publish': Langsung terbitkan draf ke publik
        $this->stagingService->publishDraft($user);

        return [
            'type' => 'redirect',
            'route' => 'superadmin.landing.index',
            'params' => ['tab' => $section],
            'status' => 'success',
            'message' => "{$labelSukses} berhasil diperbarui dan dipublikasikan ke publik.",
        ];
    }

    /**
     * Mempublikasikan seluruh draf kontainer ke live publik.
     */
    public function publishDraft(User $user): int
    {
        return $this->stagingService->publishDraft($user);
    }

    /**
     * Membuang (discard) seluruh draf kontainer dan mengembalikan editor ke versi live.
     */
    public function discardDraft(User $user): void
    {
        $this->stagingService->discardDraft($user);
    }

    /**
     * Menghapus foto banner kustom dan kembali ke foto default sistem.
     */
    public function destroyFoto(User $user): void
    {
        // Hapus draf foto jika ada
        $draftImage = Pengaturan::getLive('draft_landing_hero_image');
        if ($draftImage && Storage::disk('public')->exists($draftImage)) {
            Storage::disk('public')->delete($draftImage);
        }
        Pengaturan::delete('draft_landing_hero_image');

        // Hapus foto live jika ada
        $oldImage = Pengaturan::getLive('landing_hero_image');
        if ($oldImage && Storage::disk('public')->exists($oldImage)) {
            Storage::disk('public')->delete($oldImage);
        }

        Pengaturan::set('landing_hero_image', '');
        Pengaturan::clearCache();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'pengaturan',
            idData: 0,
            detail: [
                'aksi' => 'Mereset foto banner landing page ke default bawaan',
                'superadmin_id' => $user->id,
            ]
        );
    }

    /**
     * Menghapus dokumen PDF kustom dan kembali ke generator PDF bawaan sistem.
     */
    public function destroyPanduan(User $user): void
    {
        // Hapus draf PDF jika ada
        $draftPdf = Pengaturan::getLive('draft_landing_panduan_pdf');
        if ($draftPdf && Storage::disk('public')->exists($draftPdf)) {
            Storage::disk('public')->delete($draftPdf);
        }
        Pengaturan::delete('draft_landing_panduan_pdf');
        Pengaturan::delete('draft_landing_panduan_pdf_name');

        // Hapus PDF live jika ada
        $oldPdf = Pengaturan::getLive('landing_panduan_pdf');
        if ($oldPdf && Storage::disk('public')->exists($oldPdf)) {
            Storage::disk('public')->delete($oldPdf);
        }

        Pengaturan::set('landing_panduan_pdf', '');
        Pengaturan::set('landing_panduan_pdf_name', '');
        Pengaturan::clearCache();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'pengaturan',
            idData: 0,
            detail: [
                'aksi' => 'Mereset dokumen panduan landing page ke template PDF bawaan',
                'superadmin_id' => $user->id,
            ]
        );
    }
}
