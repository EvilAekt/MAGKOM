<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Enums\StatusKetersediaanEnum;
use App\Enums\TipeAuditEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\ReplaceMediaRequest;
use App\Http\Requests\SuperAdmin\SaveLandingSiteEditorRequest;
use App\Http\Requests\SuperAdmin\UpdateMediaRequest;
use App\Http\Requests\SuperAdmin\UploadMediaRequest;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Services\AuditService;
use App\Services\BulanStatusService;
use App\Services\LandingBlockRegistry;
use App\Services\LandingCmsStagingService;
use App\Services\LandingPage\LandingPageDocumentService;
use App\Services\LandingPage\LandingPageRenderer;
use App\Services\LandingPage\LegacyCmsAdapter;
use App\Services\LandingPage\PatternRegistry;
use App\Services\LandingPage\ReusableBlockService;
use App\Support\Pengaturan;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LandingCmsSiteEditorController extends Controller
{
    public function __construct(
        protected AuditService $auditService,
        protected LandingCmsStagingService $stagingService,
        protected LandingPageDocumentService $documentService
    ) {}

    /**
     * Menampilkan antarmuka WordPress.org-style Site Editor (Gutenberg Canvas & Right Inspector).
     */
    public function index(Request $request): View
    {
        $defaultHeaderNav = [
            ['label' => 'Beranda', 'url' => '#beranda'],
            ['label' => 'Penempatan', 'url' => '#penempatan'],
            ['label' => 'Tahapan', 'url' => '#tahapan'],
            ['label' => 'Peta Alur', 'url' => '#peta-alur'],
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
                        'a' => 'Pendaftaran dilakukan secara mandiri atau kelompok melalui portal MAGKOM: (1) Buat akun portal, (2) Pilih formasi bidang dan bulan pelaksanaan yang masih tersedia kuotanya, (3) Unggah berkas persyaratan, (4) Tunggu verifikasi admin.',
                    ],
                ],
            ],
            [
                'id' => 'akun',
                'nama' => 'Akun',
                'items' => [
                    [
                        'q' => 'Bagaimana jika tidak menerima email verifikasi akun?',
                        'a' => 'Silakan periksa folder Spam atau Promosi pada email Anda. Jika dalam 15 menit belum masuk, Anda dapat meminta kirim ulang link verifikasi.',
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
                'konten' => 'Untuk keperluan seleksi administrasi, penempatan bidang, dan penerbitan sertifikat resmi bertanda tangan elektronik (TTE), kami mengumpulkan data yang mencakup identitas diri, kontak aktif, afiliasi akademik, dan dokumen persyaratan resmi.',
                'items' => [
                    'Identitas Diri: Nama lengkap, NISN/NIM, NIK, tempat & tanggal lahir, serta foto profil resmi.',
                    'Kontak: Alamat surel (email) aktif dan nomor telepon/WhatsApp.',
                    'Afiliasi Akademik: Asal sekolah SMK/perguruan tinggi, program studi/jurusan, dan semester/tingkat kelas.',
                    'Dokumen Persyaratan: Surat pengantar resmi institusi pendidikan, proposal/curriculum vitae, transkrip nilai, dan portofolio.',
                ],
            ],
            [
                'nomor' => 3,
                'judul' => 'Keamanan Penyimpanan & Tautan Bertanda Tangan (Signed URLs)',
                'konten' => 'Seluruh berkas dokumen yang diunggah ke dalam sistem MAGKOM disimpan pada media penyimpanan privat yang terenkripsi dan hanya dapat diakses melalui Signed URLs dengan batas waktu kedaluwarsa ketat.',
                'highlight' => 'Verifikator dinas dan pembimbing lapangan hanya dapat mengakses berkas Anda melalui tautan sementara bertanda tangan digital (signed URL) dengan masa kedaluwarsa 30 menit.',
            ],
            [
                'nomor' => 4,
                'judul' => 'Hak Subjek Data Pribadi (UU PDP No. 27/2022)',
                'konten' => 'Sesuai dengan ketentuan Undang-Undang Perlindungan Data Pribadi (UU PDP No. 27 Tahun 2022), Anda memiliki hak untuk mendapatkan kejelasan pemrosesan, memperbarui data pribadi, atau meminta penghapusan berkas setelah masa retensi berakhir.',
            ],
            [
                'nomor' => 5,
                'judul' => 'Narahubung & Hak Akses',
                'konten' => 'Apabila Anda memiliki pertanyaan, permohonan koreksi data, atau kendala terkait privasi informasi, silakan menghubungi helpdesk resmi kami melalui surel kominfo@slemankab.go.id atau datang langsung ke Kantor Diskominfo Kabupaten Sleman.',
            ],
        ];

        // Dapatkan status draf
        $draftMeta = $this->stagingService->getDraftMetadata();

        // Aktifkan preview mode sementara agar data yang dimuat ke editor adalah versi draf terkini
        Pengaturan::setPreviewMode(true);

        $cmsData = [
            // Kontrol Dimensi & Spasi Pixel Eksplisit (WordPress Site Editor Token Specs)
            'landing_container_max_width_px' => (int) Pengaturan::get('landing_container_max_width_px', '1280'),
            'landing_hero_title_size_px' => (int) Pengaturan::get('landing_hero_title_size_px', '48'),
            'landing_hero_desc_size_px' => (int) Pengaturan::get('landing_hero_desc_size_px', '16'),
            'landing_hero_padding_y_px' => (int) Pengaturan::get('landing_hero_padding_y_px', '80'),
            'landing_hero_image_height_px' => (int) Pengaturan::get('landing_hero_image_height_px', '420'),
            'landing_section_gap_px' => (int) Pengaturan::get('landing_section_gap_px', '64'),
            'landing_card_radius_px' => (int) Pengaturan::get('landing_card_radius_px', '12'),
            'landing_card_padding_px' => (int) Pengaturan::get('landing_card_padding_px', '24'),
            'landing_faq_title_size_px' => (int) Pengaturan::get('landing_faq_title_size_px', '18'),
            'landing_footer_padding_y_px' => (int) Pengaturan::get('landing_footer_padding_y_px', '64'),

            // Theming & Colors
            'landing_theme_primary_color' => Pengaturan::get('landing_theme_primary_color', '#1B3860'),
            'landing_theme_navy_dark' => Pengaturan::get('landing_theme_navy_dark', '#122846'),
            'landing_theme_accent_color' => Pengaturan::get('landing_theme_accent_color', '#4F46E5'),
            'landing_theme_gold_color' => Pengaturan::get('landing_theme_gold_color', '#F59E0B'),
            'landing_theme_bg_color' => Pengaturan::get('landing_theme_bg_color', '#FFFFFF'),
            'landing_theme_font_display' => Pengaturan::get('landing_theme_font_display', 'Lexend'),
            'landing_theme_font_body' => Pengaturan::get('landing_theme_font_body', 'Source Sans 3'),

            // Header & Hero Section
            'landing_header_brand_name' => Pengaturan::get('landing_header_brand_name', 'MAGKOM'),
            'landing_header_brand_sub' => Pengaturan::get('landing_header_brand_sub', 'Portal Magang · Diskominfo Sleman'),
            'landing_header_nav' => Pengaturan::json('landing_header_nav', $defaultHeaderNav),
            'landing_hero_kicker' => Pengaturan::get('landing_hero_kicker', 'Kabupaten Sleman'),
            'landing_hero_title' => Pengaturan::get('landing_hero_title', "Magang Kominfo\nPeriode II"),
            'landing_hero_year' => Pengaturan::get('landing_hero_year', '2026'),
            'landing_hero_period_info' => Pengaturan::get('landing_hero_period_info', 'Pendaftaran 1–15 Okt 2026 · mulai 2 Nov 2026'),
            'landing_hero_cta_text' => Pengaturan::get('landing_hero_cta_text', 'Daftar Sekarang'),
            'landing_label_persyaratan' => Pengaturan::get('landing_label_persyaratan', 'Persyaratan Umum'),
            'landing_persyaratan_umum' => Pengaturan::get('landing_persyaratan_umum', "Siswa SMK aktif atau mahasiswa aktif D3/D4/S1 dengan jurusan yang relevan\nBersedia magang secara luring (WFO) di kantor Diskominfo Sleman"),
            'landing_label_dokumen' => Pengaturan::get('landing_label_dokumen', 'Dokumen Pendaftaran'),
            'landing_dokumen_pendaftaran' => Pengaturan::get('landing_dokumen_pendaftaran', "Surat Pengantar Sekolah / Perguruan Tinggi\nCurriculum Vitae (CV)\nTranskrip / Rapor Nilai Terakhir"),
            'landing_hero_image' => Pengaturan::get('landing_hero_image'),

            // Section Kuota
            'landing_kuota_heading' => Pengaturan::get('landing_kuota_heading', 'Pilih Penempatan & Ketersediaan Kuota Magang'),
            'landing_kuota_subheading' => Pengaturan::get('landing_kuota_subheading', 'Pilih jenjang pendidikan, tentukan bulan mulai, lalu pilih bidang penempatan dan jurusanmu untuk langsung mengamankan kuota pendaftaran.'),

            // Section Tahapan
            'landing_tahapan_heading' => Pengaturan::get('landing_tahapan_heading', 'Bingung cara mendaftarnya?, berikut adalah Tahapan Pendaftaran Magang'),
            'landing_tahapan_summary' => Pengaturan::get('landing_tahapan_summary', "Pertama calon peserta membuat akun, mengisi data pribadi, dan menunggu verifikasi akun oleh admin.\nKedua calon peserta memilih bidang dan mengunggah berkas."),
            'landing_tahapan_steps' => Pengaturan::json('landing_tahapan_steps', $defaultTahapanSteps),

            // Section FAQ
            'landing_faq_heading' => Pengaturan::get('landing_faq_heading', 'Bingung? Berikut Beberapa Pertanyaan Yang Sering Ditanyakan'),
            'landing_faq_data' => Pengaturan::json('landing_faq_data', $defaultFaqData),

            // Section Footer
            'landing_footer_alamat' => Pengaturan::get('landing_footer_alamat', 'Jl Parasamya (Komplek Kantor Bupati Sleman) Beran, Tridadi, Sleman, Yogyakarta 55511'),
            'landing_footer_telepon' => Pengaturan::get('landing_footer_telepon', '(0274) 868405'),
            'landing_footer_email' => Pengaturan::get('landing_footer_email', 'kominfo@slemankab.go.id'),
            'landing_footer_jam_layanan' => Pengaturan::get('landing_footer_jam_layanan', 'Senin sampai Kamis 07.30 sampai 15.30 WIB, Jumat 07.30 sampai 14.30 WIB'),
            'landing_footer_copyright' => Pengaturan::get('landing_footer_copyright', 'Dinas Komunikasi dan Informatika Kabupaten Sleman. Seluruh hak cipta dilindungi.'),
            'landing_footer_medsos' => Pengaturan::json('landing_footer_medsos', $defaultFooterMedsos),
            'landing_footer_tautan' => Pengaturan::json('landing_footer_tautan', $defaultFooterTautan),
            'landing_footer_akun' => Pengaturan::json('landing_footer_akun', $defaultFooterAkun),

            // Nilai Tambahan Konten Hero Sesuai UI WordPress Builder
            'landing_hero_cta_primary_url' => Pengaturan::get('landing_hero_cta_primary_url', '#kuota'),
            'landing_hero_cta_secondary_text' => Pengaturan::get('landing_hero_cta_secondary_text', 'Pelajari Lebih Lanjut'),
            'landing_hero_cta_secondary_url' => Pengaturan::get('landing_hero_cta_secondary_url', '#tahapan'),
            'landing_hero_overlay_active' => Pengaturan::get('landing_hero_overlay_active', '1'),

            // Dokumen Panduan PDF & Tombol Navigasi
            'landing_panduan_pdf' => Pengaturan::get('landing_panduan_pdf'),
            'landing_panduan_pdf_name' => Pengaturan::get('landing_panduan_pdf_name', 'Panduan-Resmi-Magang.pdf'),
            'landing_tahapan_btn_pdf' => Pengaturan::get('landing_tahapan_btn_pdf', 'Unduh Panduan PDF'),
            'landing_tahapan_btn_peta' => Pengaturan::get('landing_tahapan_btn_peta', 'Peta Alur Interaktif'),

            // Halaman Kebijakan Privasi
            'kebijakan_privasi_badge' => Pengaturan::get('kebijakan_privasi_badge', 'Undang-Undang PDP No. 27 Tahun 2022'),
            'kebijakan_privasi_title' => Pengaturan::get('kebijakan_privasi_title', 'Kebijakan Privasi & Perlindungan Data'),
            'kebijakan_privasi_updated_at' => Pengaturan::get('kebijakan_privasi_updated_at', 'Terakhir diperbarui: 27 Oktober 2026 • Dinas Komunikasi dan Informatika Kabupaten Sleman'),
            'kebijakan_privasi_sections' => Pengaturan::json('kebijakan_privasi_sections', $defaultKebijakanSections),
        ];

        $blocksLayout = $this->stagingService->getBlocksLayout();
        $blockRegistry = LandingBlockRegistry::getAllBlocks();
        $patterns = PatternRegistry::getAll();
        $patternCategories = PatternRegistry::getCategories();
        $reusableBlocks = app(ReusableBlockService::class)->getAll(withUsage: true);
        $responsiveStyles = $this->stagingService->getResponsiveStyles();
        $revisions = $this->stagingService->getRevisions();
        $mediaLibrary = $this->stagingService->getMediaLibrary();
        $pageDocument = app(LandingPageDocumentService::class)->getDocument(true);

        Pengaturan::setPreviewMode(false);

        return view('superadmin.site-editor.index', [
            'cmsData' => $cmsData,
            'draftMeta' => $draftMeta,
            'blocksLayout' => $blocksLayout,
            'blockRegistry' => $blockRegistry,
            'patterns' => $patterns,
            'patternCategories' => $patternCategories,
            'reusableBlocks' => $reusableBlocks,
            'responsiveStyles' => $responsiveStyles,
            'revisions' => $revisions,
            'mediaLibrary' => $mediaLibrary,
            'pageDocument' => $pageDocument,
            'documentVersion' => $this->stagingService->getDocumentVersion(true),
        ]);
    }

    /**
     * Menyimpan batch konfigurasi draf dari WordPress Site Editor via AJAX JSON.
     */
    public function saveDraft(SaveLandingSiteEditorRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $currentVersion = $this->stagingService->getDocumentVersion(true);
        $expectedVersion = $request->input('expected_version');

        if ($expectedVersion !== null && ! $request->boolean('force')) {
            $expected = (int) $expectedVersion;
            if ($expected < $currentVersion) {
                return response()->json([
                    'success' => false,
                    'conflict' => true,
                    'message' => 'Draf di server telah diperbarui oleh sesi atau pengguna lain.',
                    'server_version' => $currentVersion,
                    'client_version' => $expected,
                ], 409);
            }
        }

        $savedResult = $this->stagingService->saveSiteEditorDraft($validated, $request->user());

        $draftMeta = $this->stagingService->getDraftMetadata();
        $revisions = $this->stagingService->getRevisions();
        $blocksLayout = $this->stagingService->getBlocksLayout();

        return response()->json([
            'success' => true,
            'message' => 'Draf berhasil disimpan ke kontainer Sandbox.',
            'updated_at' => $savedResult['updated_at'] ?? $draftMeta['updated_at'],
            'author_name' => $savedResult['author_name'] ?? $draftMeta['author_name'],
            'document_version' => $savedResult['document_version'] ?? $currentVersion + 1,
            'total_keys' => $draftMeta['total_draft_keys'],
            'blocks_layout' => $blocksLayout,
            'latest_revision' => $revisions[0] ?? null,
        ]);
    }

    /**
     * Mempublikasikan seluruh draf kontainer ke live publik.
     */
    public function publish(Request $request): JsonResponse
    {
        $count = $this->stagingService->publishDraft($request->user());

        return response()->json([
            'success' => true,
            'message' => "Berhasil mempublikasikan {$count} elemen draf ke versi live publik.",
        ]);
    }

    /**
     * Membuang seluruh draf kontainer dan mengembalikan editor ke versi live.
     */
    public function discard(Request $request): JsonResponse
    {
        $count = $this->stagingService->discardDraft($request->user());

        return response()->json([
            'success' => true,
            'message' => "Seluruh draf ({$count} elemen) berhasil dibuang. Editor dikembalikan ke versi live saat ini.",
        ]);
    }

    /**
     * Melakukan rollback versi publik ke backup sebelum publikasi terakhir atau snapshot revisi tertentu.
     */
    public function rollback(Request $request): JsonResponse
    {
        $revisionId = $request->input('revision_id');
        $count = $this->stagingService->rollbackToBackup($request->user(), is_string($revisionId) ? $revisionId : null);

        if ($count === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada rekaman backup versi sebelumnya yang dapat dipulihkan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil melakukan rollback {$count} elemen live ke versi sebelum publikasi terakhir.",
        ]);
    }

    /**
     * Pratinjau snapshot revisi historis secara aman tanpa mengubah draf kerja maupun live publik.
     */
    public function previewRevision(Request $request, string $id): View
    {
        $revision = $this->stagingService->getRevisionById($id);

        if (! $revision) {
            abort(404, 'Revisi historis tidak ditemukan.');
        }

        $document = $revision['document'] ?? null;
        if (! $document && ! empty($revision['snapshot'])) {
            $document = LegacyCmsAdapter::legacySettingsToDocument(draft: false);
        }

        $sekarang = CarbonImmutable::now('Asia/Jakarta');

        $semuaBulan = BulanMagang::with(['kuota' => function ($query) {
            $query->where('aktif', true);
        }])
            ->where('aktif', true)
            ->orderBy('bulan_mulai', 'asc')
            ->get();

        $bulanStatusService = app(BulanStatusService::class);
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

        // Ambil semua bidang aktif beserta relasi jurusan aktif
        $semuaBidang = Bidang::with(['jurusan' => function ($q) {
            $q->where('aktif', true)->orderBy('nama_jurusan', 'asc');
        }])
            ->where('aktif', true)
            ->orderBy('nama_bidang', 'asc')
            ->get();

        $bidangSmk = $semuaBidang->filter(fn (Bidang $b) => $b->jurusan->contains('kategori', 'smk'))->values();
        $bidangMahasiswa = $semuaBidang->filter(fn (Bidang $b) => $b->jurusan->contains('kategori', 'mahasiswa'))->values();

        $matriksBidang = [];
        foreach ($semuaBidang as $bidang) {
            $matriksBidang[$bidang->id] = [];
            foreach (['smk', 'mahasiswa'] as $jenjang) {
                $matriksBidang[$bidang->id][$jenjang] = [];
                $jurusanJenjang = $bidang->jurusan->filter(fn (Jurusan $j) => $j->kategori === $jenjang);
                $jurusanIds = $jurusanJenjang->pluck('id');

                foreach ($bulanTampil as $bulan) {
                    $kuotaList = $bulan->kuota->whereIn('jurusan_id', $jurusanIds);
                    $total = (int) $kuotaList->sum('jumlah');
                    $terisi = (int) $kuotaList->sum('terisi');
                    $sisa = max(0, $total - $terisi);
                    $persen = $total > 0 ? (int) min(100, round(($terisi / $total) * 100)) : 0;

                    $bulanStatus = $bulanStatusService->status($bulan, null, $sekarang);
                    $isAvailable = ($bulanStatus === StatusKetersediaanEnum::Tersedia);
                    $status = $isAvailable ? StatusKetersediaanEnum::Tersedia : StatusKetersediaanEnum::Tutup;

                    $jurusanDetail = [];
                    foreach ($jurusanJenjang as $j) {
                        $k = $bulan->kuota->firstWhere('jurusan_id', $j->id);
                        $jTotal = $k ? (int) $k->jumlah : 0;
                        $jTerisi = $k ? (int) $k->terisi : 0;
                        $jSisa = max(0, $jTotal - $jTerisi);
                        $jPersen = $jTotal > 0 ? (int) min(100, round(($jTerisi / $jTotal) * 100)) : 0;

                        if ($isAvailable && $jSisa > 0) {
                            $badgeClass = ($jTotal > 0 && ($jSisa / $jTotal) <= 0.34) ? 'status-hampir' : 'status-tersedia';
                            $badgeLabel = ($jTotal > 0 && ($jSisa / $jTotal) <= 0.34) ? 'Tinggal sedikit' : 'Tersedia';
                            $barClass = ($jTotal > 0 && ($jSisa / $jTotal) <= 0.34) ? 'bar-hampir' : 'bar-tersedia';
                        } elseif ($isAvailable && $jSisa === 0) {
                            $badgeClass = 'status-penuh';
                            $badgeLabel = 'Penuh';
                            $barClass = 'bar-penuh';
                        } else {
                            $badgeClass = 'status-penuh';
                            $badgeLabel = 'Tutup';
                            $barClass = 'bar-penuh';
                        }

                        $jurusanDetail[$j->id] = [
                            'id' => $j->id,
                            'nama' => $j->nama_jurusan,
                            'total' => $jTotal,
                            'terisi' => $jTerisi,
                            'sisa' => $jSisa,
                            'persen' => $jPersen,
                            'is_available' => $isAvailable,
                            'badge_label' => $badgeLabel,
                            'badge_class' => $badgeClass,
                            'bar_class' => $barClass,
                        ];
                    }

                    $matriksBidang[$bidang->id][$jenjang][$bulan->id] = [
                        'bulan' => $bulan,
                        'total_kuota' => $total,
                        'terisi' => $terisi,
                        'persen_terisi' => $persen,
                        'sisa_kuota' => $sisa,
                        'is_available' => $isAvailable,
                        'status' => $status,
                        'jurusan_list' => $jurusanJenjang,
                        'jurusan_detail' => $jurusanDetail,
                    ];
                }
            }
        }

        return view('public.landing', [
            'bulanTampil' => $bulanTampil,
            'semuaBidang' => $semuaBidang,
            'bidangSmk' => $bidangSmk,
            'bidangMahasiswa' => $bidangMahasiswa,
            'matriksBidang' => $matriksBidang,
            'jurusanSmk' => $jurusanSmk,
            'jurusanMahasiswa' => $jurusanMahasiswa,
            'matriksKetersediaan' => $matriksKetersediaan,
            'sekarang' => $sekarang,
            'isPreview' => true,
            'isRevisionPreview' => true,
            'revision' => $revision,
            'document' => $document,
            'renderer' => app(LandingPageRenderer::class),
        ]);
    }

    /**
     * Mengambil daftar riwayat revisi (Revision History).
     */
    public function revisions(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'revisions' => $this->stagingService->getRevisions(),
        ]);
    }

    /**
     * Memulihkan snapshot revisi tertentu ke dalam kontainer draf.
     */
    public function restoreRevision(Request $request, string $id): JsonResponse
    {
        $success = $this->stagingService->restoreRevision($id, $request->user());

        if (! $success) {
            return response()->json([
                'success' => false,
                'message' => 'Revisi tidak ditemukan atau snapshot data kosong.',
            ], 404);
        }

        $draftMeta = $this->stagingService->getDraftMetadata();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil memulihkan draf dari riwayat versi yang dipilih.',
            'updated_at' => $draftMeta['updated_at'],
            'author_name' => $draftMeta['author_name'],
            'blocks_layout' => $this->stagingService->getBlocksLayout(),
        ]);
    }

    /**
     * Mengambil daftar berkas di Media Library.
     */
    public function mediaIndex(Request $request): JsonResponse
    {
        $type = $request->query('type');
        $search = $request->query('search');

        $media = $this->stagingService->getMediaLibrary(
            is_string($type) ? $type : null,
            is_string($search) ? $search : null
        );

        return response()->json([
            'success' => true,
            'media' => $media,
        ]);
    }

    /**
     * Unggah berkas baru ke Media Library.
     */
    public function uploadMedia(UploadMediaRequest $request): JsonResponse
    {
        $item = $this->stagingService->addMedia(
            file: $request->file('file'),
            metadata: $request->only(['alt_text', 'caption', 'title', 'description']),
            user: $request->user()
        );

        return response()->json([
            'success' => true,
            'message' => 'Berkas berhasil diunggah ke Media Library.',
            'item' => $item,
        ]);
    }

    /**
     * Mengganti berkas media yang sudah ada dengan berkas baru.
     */
    public function replaceMedia(ReplaceMediaRequest $request, string $id): JsonResponse
    {
        $result = $this->stagingService->replaceMedia($id, $request->file('file'), $request->user());

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Perbarui metadata media di Media Library.
     */
    public function updateMedia(UpdateMediaRequest $request, string $id): JsonResponse
    {
        $updated = $this->stagingService->updateMediaMetadata(
            $id,
            $request->only(['alt_text', 'caption', 'title', 'description'])
        );

        if (! $updated) {
            return response()->json([
                'success' => false,
                'message' => 'Berkas media tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Metadata berkas berhasil diperbarui.',
        ]);
    }

    /**
     * Hapus berkas dari Media Library.
     */
    public function deleteMedia(Request $request, string $id): JsonResponse
    {
        $result = $this->stagingService->deleteMedia($id, $request->user());

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Tambah blok baru ke dokumen draf.
     */
    public function addBlock(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string'],
            'target_order' => ['nullable', 'integer', 'min:1'],
            'content' => ['nullable', 'array'],
            'styles' => ['nullable', 'array'],
        ]);

        $result = $this->documentService->addBlock(
            type: $validated['type'],
            targetOrder: $validated['target_order'] ?? null,
            customContent: $validated['content'] ?? [],
            customStyles: $validated['styles'] ?? [],
            user: $request->user()
        );

        $draftMeta = $this->stagingService->getDraftMetadata();

        return response()->json([
            'success' => true,
            'message' => 'Blok berhasil ditambahkan.',
            'block' => $result['block'],
            'document' => $result['document'],
            'draft_meta' => $draftMeta,
        ]);
    }

    /**
     * Duplikasi blok di dokumen draf.
     */
    public function duplicateBlock(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'string'],
        ]);

        $result = $this->documentService->duplicateBlock(
            blockId: $validated['id'],
            user: $request->user()
        );

        $draftMeta = $this->stagingService->getDraftMetadata();

        return response()->json([
            'success' => true,
            'message' => 'Blok berhasil diduplikasi.',
            'block' => $result['block'],
            'document' => $result['document'],
            'draft_meta' => $draftMeta,
        ]);
    }

    /**
     * Hapus blok dari dokumen draf.
     */
    public function deleteBlock(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'string'],
        ]);

        $result = $this->documentService->deleteBlock(
            blockId: $validated['id'],
            user: $request->user()
        );

        $draftMeta = $this->stagingService->getDraftMetadata();

        return response()->json([
            'success' => true,
            'message' => 'Blok berhasil dihapus.',
            'deleted_id' => $result['deleted_id'],
            'document' => $result['document'],
            'draft_meta' => $draftMeta,
        ]);
    }

    /**
     * Susun ulang seluruh blok (Drag & Drop Reorder).
     */
    public function reorderBlocks(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'string'],
        ]);

        $result = $this->documentService->reorderBlocks(
            orderedBlockIds: $validated['order'],
            user: $request->user()
        );

        $draftMeta = $this->stagingService->getDraftMetadata();

        return response()->json([
            'success' => true,
            'message' => 'Urutan blok berhasil diperbarui.',
            'document' => $result['document'],
            'draft_meta' => $draftMeta,
        ]);
    }

    /**
     * Toggle status visibilitas blok (Hide / Show).
     */
    public function toggleVisibility(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'string'],
            'visible' => ['nullable', 'boolean'],
        ]);

        $result = $this->documentService->toggleBlockVisibility(
            blockId: $validated['id'],
            visible: isset($validated['visible']) ? (bool) $validated['visible'] : null,
            user: $request->user()
        );

        $draftMeta = $this->stagingService->getDraftMetadata();

        return response()->json([
            'success' => true,
            'message' => 'Visibilitas blok berhasil diperbarui.',
            'block' => $result['block'],
            'document' => $result['document'],
            'draft_meta' => $draftMeta,
        ]);
    }

    /**
     * Toggle status penguncian blok (Lock / Unlock).
     */
    public function toggleLock(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'string'],
            'locked' => ['nullable', 'boolean'],
        ]);

        $result = $this->documentService->toggleBlockLock(
            blockId: $validated['id'],
            locked: isset($validated['locked']) ? (bool) $validated['locked'] : null,
            user: $request->user()
        );

        $draftMeta = $this->stagingService->getDraftMetadata();

        return response()->json([
            'success' => true,
            'message' => 'Status kunci blok berhasil diperbarui.',
            'block' => $result['block'],
            'document' => $result['document'],
            'draft_meta' => $draftMeta,
        ]);
    }

    /**
     * Dapatkan daftar pola desain (Pattern Library).
     */
    public function patternsIndex(Request $request): JsonResponse
    {
        $category = $request->query('category');
        $patterns = $category
            ? PatternRegistry::getByCategory((string) $category)
            : PatternRegistry::getAll();

        return response()->json([
            'success' => true,
            'categories' => PatternRegistry::getCategories(),
            'patterns' => array_values($patterns),
            'count' => count($patterns),
        ]);
    }

    /**
     * Pratinjau pola desain (Pattern Preview).
     */
    public function patternPreview(Request $request, string $key): JsonResponse
    {
        $pattern = PatternRegistry::get($key);

        if (! $pattern) {
            return response()->json([
                'success' => false,
                'message' => "Pola '{$key}' tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'pattern' => $pattern,
        ]);
    }

    /**
     * Menyisipkan pola desain ke dalam dokumen draf.
     */
    public function insertPattern(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key' => ['required', 'string'],
            'target_order' => ['nullable', 'integer', 'min:1'],
        ]);

        $result = $this->documentService->insertPattern(
            patternKeyOrId: $validated['key'],
            targetOrder: isset($validated['target_order']) ? (int) $validated['target_order'] : null,
            user: $request->user()
        );

        $draftMeta = $this->stagingService->getDraftMetadata();

        return response()->json([
            'success' => true,
            'message' => 'Pola desain berhasil disisipkan.',
            'inserted_blocks' => $result['inserted_blocks'],
            'document' => $result['document'],
            'draft_meta' => $draftMeta,
        ]);
    }

    /**
     * Menyimpan pola kustom baru (Custom Pattern).
     */
    public function saveCustomPattern(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'blocks' => ['required', 'array', 'min:1'],
            'blocks.*.type' => ['required', 'string'],
            'thumbnail' => ['nullable', 'string', 'max:255'],
        ]);

        $savedPattern = PatternRegistry::saveCustomPattern($validated, $request->user());

        return response()->json([
            'success' => true,
            'message' => "Pola kustom '{$savedPattern['name']}' berhasil disimpan.",
            'pattern' => $savedPattern,
        ]);
    }

    /**
     * Menghapus pola kustom.
     */
    public function deleteCustomPattern(Request $request, string $id): JsonResponse
    {
        try {
            $deleted = PatternRegistry::deleteCustomPattern($id);

            if (! $deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pola kustom tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pola kustom berhasil dihapus.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Mengambil daftar Reusable Blocks terdaftar.
     */
    public function reusableBlocksIndex(Request $request): JsonResponse
    {
        $reusableService = app(ReusableBlockService::class);
        $blocks = $reusableService->getAll(withUsage: true);

        return response()->json([
            'success' => true,
            'reusable_blocks' => $blocks,
            'count' => count($blocks),
        ]);
    }

    /**
     * Membuat definisi Reusable Block baru.
     */
    public function createReusableBlock(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'block_type' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:50'],
            'content' => ['required', 'array'],
            'styles' => ['nullable', 'array'],
        ]);

        $reusableService = app(ReusableBlockService::class);
        $newBlock = $reusableService->create($validated, $request->user());

        return response()->json([
            'success' => true,
            'message' => "Reusable Block '{$newBlock['title']}' berhasil dibuat.",
            'reusable_block' => $newBlock,
        ]);
    }

    /**
     * Memperbarui definisi Reusable Block bersama.
     */
    public function updateReusableBlock(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:50'],
            'content' => ['nullable', 'array'],
            'styles' => ['nullable', 'array'],
        ]);

        $reusableService = app(ReusableBlockService::class);
        $updatedBlock = $reusableService->update($id, $validated, $request->user());

        if (! $updatedBlock) {
            return response()->json([
                'success' => false,
                'message' => "Reusable Block '{$id}' tidak ditemukan.",
            ], 404);
        }

        $draftMeta = $this->stagingService->getDraftMetadata();

        return response()->json([
            'success' => true,
            'message' => "Reusable Block '{$updatedBlock['title']}' berhasil diperbarui.",
            'reusable_block' => $updatedBlock,
            'draft_meta' => $draftMeta,
        ]);
    }

    /**
     * Menghapus Reusable Block dengan proteksi keterpakaian (Delete Protection).
     */
    public function deleteReusableBlock(Request $request, string $id): JsonResponse
    {
        $reusableService = app(ReusableBlockService::class);
        $result = $reusableService->delete($id, $request->user());

        if (! $result['success']) {
            return response()->json($result, 422);
        }

        return response()->json($result);
    }

    /**
     * Menyisipkan instance Reusable Block ke dalam dokumen draf.
     */
    public function insertReusableBlock(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reusable_id' => ['required', 'string'],
            'target_order' => ['nullable', 'integer', 'min:1'],
        ]);

        $result = $this->documentService->insertReusableBlock(
            reusableId: $validated['reusable_id'],
            targetOrder: isset($validated['target_order']) ? (int) $validated['target_order'] : null,
            user: $request->user()
        );

        $draftMeta = $this->stagingService->getDraftMetadata();

        return response()->json([
            'success' => true,
            'message' => 'Reusable Block berhasil disisipkan.',
            'block' => $result['block'],
            'document' => $result['document'],
            'draft_meta' => $draftMeta,
        ]);
    }

    /**
     * Mengonversi instance Reusable Block menjadi blok reguler mandiri (Detach).
     */
    public function detachReusableBlock(Request $request, string $blockId): JsonResponse
    {
        $result = $this->documentService->convertReusableToRegularBlock(
            blockId: $blockId,
            user: $request->user()
        );

        $draftMeta = $this->stagingService->getDraftMetadata();

        return response()->json([
            'success' => true,
            'message' => 'Blok berhasil dipisahkan menjadi blok reguler mandiri.',
            'block' => $result['block'],
            'document' => $result['document'],
            'draft_meta' => $draftMeta,
        ]);
    }

    /**
     * Mereset berkas panduan PDF kustom kembali ke template generator PDF resmi bawaan sistem.
     */
    public function resetPanduanPdf(Request $request): JsonResponse
    {
        // Hapus draf PDF jika ada
        $draftPdf = Pengaturan::getLive('draft_landing_panduan_pdf');
        if ($draftPdf && Storage::disk('public')->exists($draftPdf)) {
            Storage::disk('public')->delete($draftPdf);
        }
        Pengaturan::delete('draft_landing_panduan_pdf');
        Pengaturan::delete('draft_landing_panduan_pdf_name');

        // Reset live PDF jika ada
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
                'aksi' => 'Mereset dokumen panduan Canvas Editor ke generator PDF resmi bawaan sistem',
                'superadmin_id' => $request->user()->id,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Dokumen panduan berhasil direset ke generator PDF resmi bawaan sistem.',
        ]);
    }
}
