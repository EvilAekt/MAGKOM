<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\StatusKetersediaanEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Services\AuditService;
use App\Services\BulanStatusService;
use App\Services\LandingCmsStagingService;
use App\Services\LandingPage\LandingPageDocumentService;
use App\Services\LandingPage\LandingPageRenderer;
use App\Support\Pengaturan;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LandingCmsController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
        protected LandingCmsStagingService $stagingService
    ) {}

    /**
     * Menampilkan antarmuka CMS pengelolaan Landing Page (Task E & Staging Sandbox).
     */
    public function index(Request $request): View|RedirectResponse
    {
        if ($request->boolean('redirect_to_canvas') || (! $request->has('legacy') && ! app()->runningUnitTests())) {
            return redirect()->route('superadmin.site-editor.index')
                ->with('info', 'CMS Pengelolaan Konten Publik kini terpusat sepenuhnya di Canvas Editor.');
        }
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
                        'a' => 'Akun Anda berlaku permanen di portal MAGKOM. Anda dapat menggunakannya kembali untuk melihat riwayat sertifikat atau mendaftar periode berikutnya setelah magang sebelumnya selesai.',
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
                        'a' => 'Peserta mendapatkan bimbingan intensif dari Mentor ASN profesional, akses modul proyek nyata di pemerintahan, sertifikat resmi Diskominfo ber-QR code sah, serta penilaian kompetensi standar industri.',
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
            ['step' => '04', 'title' => 'Kelulusan', 'badge' => 'KELULUSAN', 'desc' => 'Unduh Lembar Nilai Magang dan sertifikat dalam bentuk PDF dari portal.'],
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

        // Status metadata draf saat ini
        $draftMeta = $this->stagingService->getDraftMetadata();

        // Aktifkan mode pratinjau sementara agar form editor membaca draf yang sedang dikerjakan
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
            'kuota_legend_penuh' => Pengaturan::get('landing_kuota_legend_penuh', 'Penuh / Waiting List'),
            'kuota_btn_tersedia' => Pengaturan::get('landing_kuota_btn_tersedia', 'Daftar Sekarang ➔'),
            'kuota_btn_waiting' => Pengaturan::get('landing_kuota_btn_waiting', 'Daftar Waiting List ➔'),
            'kuota_btn_tutup' => Pengaturan::get('landing_kuota_btn_tutup', 'Pendaftaran Ditutup'),

            // Section 3: Tahapan
            'tahapan_heading' => Pengaturan::get('landing_tahapan_heading', 'Bingung cara mendaftarnya?, berikut adalah Tahapan Pendaftaran Magang'),
            'tahapan_summary' => Pengaturan::get('landing_tahapan_summary', "Pertama calon peserta membuat akun, mengisi data pribadi, dan menunggu verifikasi akun oleh admin.\nKedua calon peserta memilih bidang dan mengunggah berkas, lalu admin melakukan verifikasi dan seleksi. Hasilnya diumumkan di portal MAGKOM.\nKetiga peserta melaksanakan magang, mengerjakan tugas, dan mengisi logbook sesuai durasi yang ditentukan.\nKeempat setelah magang selesai, peserta mendapatkan lembar nilai dan sertifikat yang dapat diunduh."),
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

        return view('superadmin.landing.index', [
            'cmsData' => $cmsData,
            'draftMeta' => $draftMeta,
        ]);
    }

    /**
     * Menyimpan perubahan konten CMS berdasarkan bagian (Section), mendukung simpan draf dan publikasi langsung.
     */
    public function update(Request $request): RedirectResponse
    {
        $section = (string) $request->input('section', 'hero');
        $actionType = (string) $request->input('action_type', 'publish');

        if ($section === 'header_seo') {
            $validated = $request->validate([
                'meta_title' => ['required', 'string', 'max:255'],
                'meta_desc' => ['required', 'string', 'max:500'],
                'header_brand_name' => ['required', 'string', 'max:50'],
                'header_brand_sub' => ['required', 'string', 'max:100'],
                'header_nav_json' => ['nullable', 'string'],
            ]);

            $dataToSave = [
                'landing_meta_title' => $validated['meta_title'],
                'landing_meta_desc' => $validated['meta_desc'],
                'landing_header_brand_name' => $validated['header_brand_name'],
                'landing_header_brand_sub' => $validated['header_brand_sub'],
            ];

            if ($request->has('nav_items') && is_array($request->input('nav_items'))) {
                $navItems = [];
                foreach ($request->input('nav_items') as $item) {
                    if (! empty($item['label']) && ! empty($item['url'])) {
                        $navItems[] = [
                            'label' => trim((string) $item['label']),
                            'url' => trim((string) $item['url']),
                        ];
                    }
                }
                $dataToSave['landing_header_nav'] = $navItems;
            } elseif (! empty($validated['header_nav_json'])) {
                $decodedNav = json_decode($validated['header_nav_json'], true);
                if (is_array($decodedNav)) {
                    $dataToSave['landing_header_nav'] = $decodedNav;
                }
            }

            return $this->finalizeSectionUpdate(
                section: 'header_seo',
                data: $dataToSave,
                foto: null,
                pdf: null,
                actionType: $actionType,
                request: $request,
                labelSukses: 'Konfigurasi Header & SEO Meta'
            );
        }

        if ($section === 'kuota') {
            $validated = $request->validate([
                'kuota_heading' => ['required', 'string', 'max:255'],
                'kuota_subheading' => ['required', 'string', 'max:500'],
                'kuota_q1_label' => ['required', 'string', 'max:100'],
                'kuota_pill_smk' => ['required', 'string', 'max:100'],
                'kuota_pill_mhs' => ['required', 'string', 'max:100'],
                'kuota_q2_label' => ['required', 'string', 'max:100'],
                'kuota_empty_note' => ['nullable', 'string', 'max:255'],
                'kuota_q3_label' => ['required', 'string', 'max:100'],
                'kuota_pill_mandiri' => ['required', 'string', 'max:100'],
                'kuota_pill_tim' => ['required', 'string', 'max:100'],
                'kuota_team_label' => ['required', 'string', 'max:150'],
                'kuota_team_note' => ['required', 'string', 'max:500'],
                'kuota_gauge_desc' => ['required', 'string', 'max:255'],
                'kuota_legend_tersedia' => ['required', 'string', 'max:50'],
                'kuota_legend_hampir' => ['required', 'string', 'max:50'],
                'kuota_legend_penuh' => ['required', 'string', 'max:50'],
                'kuota_btn_tersedia' => ['required', 'string', 'max:100'],
                'kuota_btn_waiting' => ['required', 'string', 'max:100'],
                'kuota_btn_tutup' => ['required', 'string', 'max:100'],
            ]);

            $dataToSave = [];
            foreach ($validated as $key => $val) {
                $dataToSave['landing_'.$key] = $val ?? '';
            }

            return $this->finalizeSectionUpdate(
                section: 'kuota',
                data: $dataToSave,
                foto: null,
                pdf: null,
                actionType: $actionType,
                request: $request,
                labelSukses: 'Teks dan instruksi bagian Kuota & Formasi'
            );
        }

        if ($section === 'tahapan') {
            $validated = $request->validate([
                'tahapan_heading' => ['required', 'string', 'max:255'],
                'tahapan_summary' => ['required', 'string', 'max:2000'],
                'step1_title' => ['required', 'string', 'max:100'],
                'step1_desc' => ['required', 'string', 'max:500'],
                'step2_title' => ['required', 'string', 'max:100'],
                'step2_desc' => ['required', 'string', 'max:500'],
                'step3_title' => ['required', 'string', 'max:100'],
                'step3_desc' => ['required', 'string', 'max:500'],
                'step4_title' => ['required', 'string', 'max:100'],
                'step4_desc' => ['required', 'string', 'max:500'],
                'tahapan_btn_pdf' => ['nullable', 'string', 'max:100'],
                'tahapan_btn_peta' => ['nullable', 'string', 'max:100'],
            ]);

            $stepsData = [
                ['step' => '01', 'title' => $validated['step1_title'], 'badge' => strtoupper($validated['step1_title']), 'desc' => $validated['step1_desc']],
                ['step' => '02', 'title' => $validated['step2_title'], 'badge' => strtoupper($validated['step2_title']), 'desc' => $validated['step2_desc']],
                ['step' => '03', 'title' => $validated['step3_title'], 'badge' => strtoupper($validated['step3_title']), 'desc' => $validated['step3_desc']],
                ['step' => '04', 'title' => $validated['step4_title'], 'badge' => strtoupper($validated['step4_title']), 'desc' => $validated['step4_desc']],
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

            return $this->finalizeSectionUpdate(
                section: 'tahapan',
                data: $dataToSave,
                foto: null,
                pdf: null,
                actionType: $actionType,
                request: $request,
                labelSukses: 'Konten Tahapan Alur Magang'
            );
        }

        if ($section === 'faq') {
            $hasVisualFaq = $request->has('faq_categories') && is_array($request->input('faq_categories'));

            $validated = $request->validate([
                'faq_heading' => ['required', 'string', 'max:255'],
                'faq_json' => [$hasVisualFaq ? 'nullable' : 'required', 'string'],
            ]);

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
                    return back()->withInput()->with('error', 'Format JSON FAQ tidak valid. Silakan periksa kembali struktur tanda kurung dan koma.');
                }

                $dataToSave = [
                    'landing_faq_heading' => $validated['faq_heading'],
                    'landing_faq_data' => $decodedFaq,
                ];
            }

            return $this->finalizeSectionUpdate(
                section: 'faq',
                data: $dataToSave,
                foto: null,
                pdf: null,
                actionType: $actionType,
                request: $request,
                labelSukses: 'Konten FAQ (Tanya Jawab)'
            );
        }

        if ($section === 'footer') {
            $validated = $request->validate([
                'footer_alamat' => ['required', 'string', 'max:500'],
                'footer_telepon' => ['required', 'string', 'max:100'],
                'footer_email' => ['required', 'string', 'email', 'max:150'],
                'footer_jam_layanan' => ['required', 'string', 'max:255'],
                'footer_copyright' => ['required', 'string', 'max:255'],
                'footer_medsos_json' => ['nullable', 'string'],
                'footer_tautan_json' => ['nullable', 'string'],
                'footer_akun_json' => ['nullable', 'string'],
            ]);

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
                $decodedMedsos = json_decode($validated['footer_medsos_json'], true);
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
                $decodedTautan = json_decode($validated['footer_tautan_json'], true);
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
                $decodedAkun = json_decode($validated['footer_akun_json'], true);
                if (is_array($decodedAkun)) {
                    $dataToSave['landing_footer_akun'] = $decodedAkun;
                }
            }

            return $this->finalizeSectionUpdate(
                section: 'footer',
                data: $dataToSave,
                foto: null,
                pdf: null,
                actionType: $actionType,
                request: $request,
                labelSukses: 'Informasi Footer & Kontak Dinas'
            );
        }

        if ($section === 'kebijakan') {
            $hasVisualKebijakan = $request->has('kebijakan_sections') && is_array($request->input('kebijakan_sections'));

            $validated = $request->validate([
                'kebijakan_badge' => ['required', 'string', 'max:150'],
                'kebijakan_title' => ['required', 'string', 'max:255'],
                'kebijakan_updated_at' => ['required', 'string', 'max:255'],
                'kebijakan_sections_json' => [$hasVisualKebijakan ? 'nullable' : 'required', 'string'],
            ]);

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
                    return back()->withInput()->with('error', 'Format JSON pasal Kebijakan Privasi tidak valid. Periksa sintaks JSON.');
                }

                $dataToSave = [
                    'kebijakan_privasi_badge' => $validated['kebijakan_badge'],
                    'kebijakan_privasi_title' => $validated['kebijakan_title'],
                    'kebijakan_privasi_updated_at' => $validated['kebijakan_updated_at'],
                    'kebijakan_privasi_sections' => $decodedSections,
                ];
            }

            return $this->finalizeSectionUpdate(
                section: 'kebijakan',
                data: $dataToSave,
                foto: null,
                pdf: null,
                actionType: $actionType,
                request: $request,
                labelSukses: 'Halaman Kebijakan Privasi & Perlindungan Data'
            );
        }

        if ($section === 'theme') {
            $validated = $request->validate([
                'theme_primary_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
                'theme_navy_dark' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
                'theme_accent_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
                'theme_gold_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
                'theme_bg_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
                'theme_font_display' => ['required', 'string', 'in:Lexend,Plus Jakarta Sans,Inter,Montserrat,Poppins'],
                'theme_font_body' => ['required', 'string', 'in:Source Sans 3,Inter,Roboto,Open Sans,Nunito Sans'],
                'theme_border_radius' => ['required', 'string', 'in:0px,6px,12px,16px,24px'],
                'theme_btn_style' => ['required', 'string', 'in:rounded,pill,sharp'],
            ]);

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

            return $this->finalizeSectionUpdate(
                section: 'theme',
                data: $dataToSave,
                foto: null,
                pdf: null,
                actionType: $actionType,
                request: $request,
                labelSukses: 'Tema & Desain Visual Landing Page'
            );
        }

        // Default: Section 'hero'
        $validated = $request->validate([
            'hero_kicker' => ['nullable', 'string', 'max:100'],
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_year' => ['nullable', 'string', 'max:50'],
            'hero_period_info' => ['nullable', 'string', 'max:255'],
            'hero_cta_text' => ['nullable', 'string', 'max:50'],
            'label_persyaratan' => ['nullable', 'string', 'max:100'],
            'persyaratan_umum' => ['nullable', 'string', 'max:2000'],
            'label_dokumen' => ['nullable', 'string', 'max:100'],
            'dokumen_pendaftaran' => ['nullable', 'string', 'max:2000'],
            'hero_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'panduan_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ], [
            'hero_image.max' => 'Ukuran file foto banner maksimal 2 MB.',
            'hero_image.mimes' => 'Format file foto banner harus berupa JPG, PNG, atau WEBP.',
            'panduan_pdf.max' => 'Ukuran file dokumen panduan maksimal 5 MB.',
            'panduan_pdf.mimes' => 'Format file dokumen panduan harus berupa PDF.',
        ]);

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

        return $this->finalizeSectionUpdate(
            section: 'hero',
            data: $dataToSave,
            foto: $request->file('hero_image'),
            pdf: $request->file('panduan_pdf'),
            actionType: $actionType,
            request: $request,
            labelSukses: 'Konten Hero, media foto, dan dokumen panduan'
        );
    }

    /**
     * Memproses penyimpanan draf, pratinjau, atau publikasi langsung ke produksi.
     *
     * @param  array<string, mixed>  $data
     */
    private function finalizeSectionUpdate(
        string $section,
        array $data,
        ?UploadedFile $foto,
        ?UploadedFile $pdf,
        string $actionType,
        Request $request,
        string $labelSukses
    ): RedirectResponse {
        $user = $request->user();

        // 1. Selalu simpan ke dalam kontainer draf (staging)
        $this->stagingService->saveDraft(
            section: $section,
            data: $data,
            foto: $foto,
            pdf: $pdf,
            user: $user
        );

        // 2. Jika aksi adalah 'preview', arahkan ke rute pratinjau interaktif
        if ($actionType === 'preview') {
            return redirect()->route('superadmin.landing.preview')
                ->with('success', "Draf {$labelSukses} berhasil disimpan. Anda berada dalam mode pratinjau interaktif.");
        }

        // 3. Jika aksi adalah 'draft', simpan di sandbox dan kembali ke editor
        if ($actionType === 'draft') {
            return redirect()->route('superadmin.landing.index', ['tab' => $section])
                ->with('success', "Draf {$labelSukses} berhasil disimpan ke kontainer Sandbox (belum dipublikasikan ke publik).");
        }

        // 4. Default / 'publish': Langsung terbitkan draf ke publik
        $this->stagingService->publishDraft($user);

        return redirect()->route('superadmin.landing.index', ['tab' => $section])
            ->with('success', "{$labelSukses} berhasil diperbarui dan dipublikasikan ke publik.");
    }

    /**
     * Mempublikasikan seluruh draf kontainer ke live publik.
     */
    public function publishDraft(Request $request): RedirectResponse
    {
        $count = $this->stagingService->publishDraft($request->user());

        return redirect()->route('superadmin.landing.index')
            ->with('success', "Berhasil mempublikasikan {$count} elemen draf ke versi live publik.");
    }

    /**
     * Membuang (discard) seluruh draf kontainer dan mengembalikan editor ke versi live.
     */
    public function discardDraft(Request $request): RedirectResponse
    {
        $this->stagingService->discardDraft($request->user());

        return redirect()->route('superadmin.landing.index')
            ->with('success', 'Seluruh draf perubahan berhasil dibuang. Editor dikembalikan ke versi live saat ini.');
    }

    /**
     * Menampilkan Pratinjau Interaktif Penuh (Full Interactive Live Preview) Landing Page.
     */
    public function preview(Request $request, BulanStatusService $bulanStatusService): View
    {
        // Aktifkan mode pratinjau untuk request ini
        Pengaturan::setPreviewMode(true);

        $page = $request->query('page', 'landing');

        if ($page === 'peta-alur') {
            return view('public.peta-alur-interaktif', [
                'isPreview' => true,
                'editor_mode' => $request->boolean('editor_mode'),
            ]);
        }

        if ($page === 'kebijakan-privasi') {
            return view('public.kebijakan-privasi', [
                'isPreview' => true,
                'editor_mode' => $request->boolean('editor_mode'),
            ]);
        }

        $sekarang = CarbonImmutable::now('Asia/Jakarta');

        $semuaBulan = BulanMagang::with(['kuota' => function ($query) {
            $query->where('aktif', true);
        }])
            ->where('aktif', true)
            ->orderBy('bulan_mulai', 'asc')
            ->get();

        $bulanTampil = $semuaBulan->filter(function (BulanMagang $bulan) use ($sekarang, $bulanStatusService) {
            return $bulanStatusService->tampilDiLanding($bulan, $sekarang);
        })->values();

        $semuaJurusan = Jurusan::with('bidang')
            ->where('aktif', true)
            ->orderBy('kategori', 'asc')
            ->orderBy('nama_jurusan', 'asc')
            ->get();

        $jurusanSmk = $semuaJurusan->filter(fn (Jurusan $j) => $j->kategori === 'smk');
        $jurusanMahasiswa = $semuaJurusan->filter(fn (Jurusan $j) => $j->kategori === 'mahasiswa');

        $matriksKetersediaan = [];

        foreach ($semuaJurusan as $jurusan) {
            $matriksKetersediaan[$jurusan->id] = [];

            foreach ($bulanTampil as $bulan) {
                $kuota = $bulan->kuota->firstWhere('jurusan_id', $jurusan->id);
                $status = $bulanStatusService->status($bulan, $kuota, $sekarang);
                $sisaKuota = $kuota ? max(0, $kuota->jumlah - $kuota->terisi) : 0;
                $isWaitingList = ($status === StatusKetersediaanEnum::Tersedia && $sisaKuota === 0);

                $totalKuota = $kuota ? (int) $kuota->jumlah : 0;
                $terisiKuota = $kuota ? (int) $kuota->terisi : 0;
                $persenTerisi = $totalKuota > 0 ? (int) min(100, round(($terisiKuota / $totalKuota) * 100)) : 0;

                $matriksKetersediaan[$jurusan->id][$bulan->id] = [
                    'bulan' => $bulan,
                    'kuota' => $kuota,
                    'total_kuota' => $totalKuota,
                    'terisi' => $terisiKuota,
                    'persen_terisi' => $persenTerisi,
                    'status' => $status,
                    'sisa_kuota' => $sisaKuota,
                    'is_waiting_list' => $isWaitingList,
                    'dapat_mendaftar' => ($status === StatusKetersediaanEnum::Tersedia),
                ];
            }
        }

        return view('public.landing', [
            'bulanTampil' => $bulanTampil,
            'jurusanSmk' => $jurusanSmk,
            'jurusanMahasiswa' => $jurusanMahasiswa,
            'matriksKetersediaan' => $matriksKetersediaan,
            'semuaBidang' => Bidang::with(['jurusan' => function ($q) {
                $q->where('aktif', true);
            }])->where('aktif', true)->get(),
            'sekarang' => $sekarang,
            'isPreview' => true,
            'document' => app(LandingPageDocumentService::class)->getDocument(true),
            'renderer' => app(LandingPageRenderer::class),
        ]);
    }

    /**
     * Menghapus foto banner kustom dan kembali ke foto default sistem.
     */
    public function destroyFoto(Request $request): RedirectResponse
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
                'superadmin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('superadmin.landing.index', ['tab' => 'hero'])
            ->with('success', 'Foto banner berhasil direset ke gambar bawaan sistem.');
    }

    /**
     * Menghapus dokumen PDF kustom dan kembali ke generator PDF bawaan sistem.
     */
    public function destroyPanduan(Request $request): RedirectResponse
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
                'superadmin_id' => $request->user()->id,
            ]
        );

        return redirect()->route('superadmin.landing.index', ['tab' => 'hero'])
            ->with('success', 'Dokumen panduan berhasil direset ke generator PDF resmi bawaan sistem.');
    }
}
