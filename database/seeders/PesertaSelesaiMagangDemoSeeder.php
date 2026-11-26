<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusReviewProgresEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\AgendaBimbingan;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\KelompokAnggota;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\ProgresLog;
use App\Models\Tugas;
use App\Models\TugasPeserta;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class PesertaSelesaiMagangDemoSeeder extends Seeder
{
    /**
     * Seed akun dan data komplit peserta (Individu dan Tim) yang telah berstatus SELESAI magang:
     * - Lengkap dengan riwayat tugas individu & kelompok yang telah selesai dikerjakan,
     * - Riwayat laporan progres (progres_log) yang disetujui beserta feedback pembimbing,
     * - Riwayat logbook mingguan (minggu 1 - 12) yang telah disetujui pembimbing,
     * - Riwayat agenda bimbingan resmi yang telah selesai,
     * - Menunggu administrasi mengunggah surat keterangan selesai magang individual/tim.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('Password123!');

        // Ambil / pastikan relasi penunjang tersedia
        $jurusan = Jurusan::first() ?? Jurusan::create([
            'nama_jurusan' => 'Teknologi Informasi',
            'kategori' => 'informatika',
            'aktif' => true,
        ]);

        $bidang = Bidang::first() ?? Bidang::create([
            'nama_bidang' => 'Aplikasi Informatika',
            'deskripsi' => 'Bidang Aptika',
            'aktif' => true,
        ]);

        $bulanMagang = BulanMagang::first() ?? BulanMagang::create([
            'nama_bulan' => 'Januari - Maret 2026',
            'bulan_mulai' => Carbon::now()->subMonths(3)->startOfMonth(),
            'tgl_buka' => Carbon::now()->subMonths(4),
            'tgl_tutup' => Carbon::now()->subMonths(3),
            'aktif' => true,
        ]);

        $kuota = Kuota::first() ?? Kuota::create([
            'bulan_magang_id' => $bulanMagang->id,
            'jurusan_id' => $jurusan->id,
            'jumlah' => 20,
            'terisi' => 5,
            'aktif' => true,
        ]);

        $admin = User::where('role', RoleEnum::Admin->value)->first() ?? User::create([
            'nama' => 'Administrator MagKom',
            'email' => 'admin.magkom@magkom.test',
            'password' => $defaultPassword,
            'role' => RoleEnum::Admin->value,
            'email_verified_at' => Carbon::now(),
        ]);

        $pembimbing = User::where('role', RoleEnum::Pembimbing->value)->first() ?? User::create([
            'nama' => 'Budi Santoso, S.Kom., M.Cs.',
            'email' => 'pembimbing.kominfo@magkom.test',
            'password' => $defaultPassword,
            'role' => RoleEnum::Pembimbing->value,
            'email_verified_at' => Carbon::now(),
        ]);

        $tglMulai = Carbon::now()->subMonths(3)->startOfMonth();
        $tglSelesai = Carbon::now()->subDays(2);

        // =========================================================================
        // 1. PESERTA INDIVIDU (Rian Pratama & Dinda Lestari)
        // =========================================================================
        $individuUsers = [
            'rian' => [
                'email' => 'rian.selesai@magkom.test',
                'nama' => 'Rian Pratama',
                'nim_nisn' => '20520241099',
                'institusi' => 'Universitas Gadjah Mada',
                'no_hp' => '081234567801',
                'tugas' => [
                    [
                        'judul' => 'Analisis Kebutuhan Sistem & Skema Basis Data Satu Data Sleman',
                        'kategori' => 'Backend Architecture',
                        'kompleksitas' => KompleksitasEnum::Sedang,
                        'deadline' => $tglMulai->copy()->addWeeks(3),
                        'deskripsi' => 'Melakukan pemetaan skema database relasional 3NF untuk modul agregasi data lintas OPD Kabupaten Sleman.',
                        'link_referensi' => 'https://kominfo.slemankab.go.id/satudata-architecture-spec',
                        'progres_catatan' => 'ERD skema database dan dokumentasi relasi tabel telah selesai disusun dan disimulasikan.',
                        'progres_link' => 'https://github.com/kominfo-sleman/database-satudata-schema',
                        'feedback' => 'Analisis dan normalisasi skema database sangat rapi, relasi foreign key dan indexing sudah optimal.',
                    ],
                    [
                        'judul' => 'Pengembangan RESTful API Microservices Integrasi Layanan Publik',
                        'kategori' => 'API Development',
                        'kompleksitas' => KompleksitasEnum::Tinggi,
                        'deadline' => $tglMulai->copy()->addWeeks(7),
                        'deskripsi' => 'Mengembangkan endpoint API CRUD dengan autentikasi Bearer Token, request throttling, dan FormRequest validation.',
                        'link_referensi' => 'https://kominfo.slemankab.go.id/api-guidelines',
                        'progres_catatan' => 'Seluruh endpoint API publik berhasil diimplementasikan dan lolos uji automated PHPUnit test suite.',
                        'progres_link' => 'https://github.com/kominfo-sleman/layanan-publik-api',
                        'feedback' => 'Implementasi API sangat bersih (clean code) dan penanganan HTTP response code sudah sesuai standar REST.',
                    ],
                    [
                        'judul' => 'Optimasi Performa Query Database & Implementasi Redis Caching',
                        'kategori' => 'Performance & Caching',
                        'kompleksitas' => KompleksitasEnum::Sedang,
                        'deadline' => $tglMulai->copy()->addWeeks(10),
                        'deskripsi' => 'Mencegah bottleneck N+1 query problem dan mengaktifkan query caching Redis pada endpoint katalog statistik.',
                        'link_referensi' => 'https://redis.io/docs/manual/patterns',
                        'progres_catatan' => 'Response time rata-rata turun dari 480ms menjadi 42ms setelah penerapan eager loading dan Redis tags caching.',
                        'progres_link' => 'https://github.com/kominfo-sleman/caching-benchmark-report',
                        'feedback' => 'Peningkatan performa sangat signifikan. Solusi caching terbukti stabil pada beban benchmark.',
                    ],
                    [
                        'judul' => 'Penyusunan Dokumentasi OpenAPI Swagger & Panduan Integrasi OPD',
                        'kategori' => 'Dokumentasi & QA',
                        'kompleksitas' => KompleksitasEnum::Rendah,
                        'deadline' => $tglMulai->copy()->addWeeks(12),
                        'deskripsi' => 'Menyusun spesifikasi OpenAPI 3.0 dan panduan teknis integrasi endpoint bagi operator OPD Sleman.',
                        'link_referensi' => 'https://swagger.io/specification/',
                        'progres_catatan' => 'Spesifikasi OpenAPI Swagger dan Postman Collection lengkap telah diekspor dan siap pakai.',
                        'progres_link' => 'https://kominfo-sleman.github.io/api-docs',
                        'feedback' => 'Dokumentasi interaktif Swagger sangat memudahkan tim OPD lain untuk mulai mengintegrasikan sistem.',
                    ],
                ],
                'logbook_kegiatan' => [
                    1 => 'Orientasi magang di Bidang Aptika Kominfo Sleman, pengenalan sistem kerja, dan penyiapan dev environment lokal.',
                    2 => 'Mempelajari arsitektur sistem eksisting Satu Data Sleman dan mengidentifikasi kebutuhan interoperabilitas data.',
                    3 => 'Merancang ERD basis data terpusat dan menyusun kamus data (data dictionary) tabel master.',
                    4 => 'Mendiskusikan rancangan skema database dengan tim pembimbing dan melakukan normalisasi ke bentuk 3NF.',
                    5 => 'Membuat skeleton repository Laravel 10 dan menyusun struktur folder service, repository, dan form requests.',
                    6 => 'Mengimplementasikan modul autentikasi JWT token dan manajemen hak akses role pegawai instansi.',
                    7 => 'Mengembangkan endpoint API integrasi data kependudukan dan layanan perizinan terpadu.',
                    8 => 'Menulis unit testing dan feature testing pada controller API untuk menguji skenario validasi data.',
                    9 => 'Melakukan profiling query SQL, mendeteksi potensi N+1 problem, dan menambahkan indexing pada kolom filter.',
                    10 => 'Menerapkan Redis cache layer untuk query agregasi statistik pelayanan publik.',
                    11 => 'Penyusunan dokumentasi OpenAPI Swagger dan pengujian endpoint menggunakan Postman collection.',
                    12 => 'Finalisasi laporan akhir magang, presentasi capaian kerja di hadapan kepala bidang, dan serah terima kode sumber.',
                ],
            ],
            'dinda' => [
                'email' => 'dinda.selesai@magkom.test',
                'nama' => 'Dinda Lestari',
                'nim_nisn' => '20520241088',
                'institusi' => 'Universitas Negeri Yogyakarta',
                'no_hp' => '081234567802',
                'tugas' => [
                    [
                        'judul' => 'Audit Aksesibilitas Web Portal Pemkab Sleman Berstandar WCAG 2.1 AA',
                        'kategori' => 'Accessibility & UX Audit',
                        'kompleksitas' => KompleksitasEnum::Sedang,
                        'deadline' => $tglMulai->copy()->addWeeks(3),
                        'deskripsi' => 'Mengidentifikasi isu rasio kontras warna, keyboard navigation trap, dan aria-labels pada form pengaduan masyarakat.',
                        'link_referensi' => 'https://www.w3.org/WAI/WCAG21/quickref/',
                        'progres_catatan' => 'Laporan audit komprehensif berhasil disusun beserta rekomendasi perbaikan token desain antarmuka.',
                        'progres_link' => 'https://figma.com/@dinda/sleman-wcag-audit',
                        'feedback' => 'Hasil audit sangat mendalam dan langsung dapat ditindaklanjuti oleh tim front-end.',
                    ],
                    [
                        'judul' => 'Desain Sistem dan Wireframing Komponen Dashboard Pengaduan Publik',
                        'kategori' => 'UI/UX Design',
                        'kompleksitas' => KompleksitasEnum::Tinggi,
                        'deadline' => $tglMulai->copy()->addWeeks(7),
                        'deskripsi' => 'Merancang reusable UI components di Figma lengkap dengan state default, hover, active, error, dan dark mode.',
                        'link_referensi' => 'https://m3.material.io/components',
                        'progres_catatan' => 'High-fidelity prototype dan komponen design system Sleman SmartGov siap diintegrasikan.',
                        'progres_link' => 'https://figma.com/@dinda/smartgov-design-system',
                        'feedback' => 'Visual hierarchy sangat proporsional, pemilihan warna elegan dan konsisten dengan brand Kominfo.',
                    ],
                    [
                        'judul' => 'Slicing Antarmuka Responsif Blade & Tailwind CSS Halaman Pelaporan',
                        'kategori' => 'Frontend Slicing',
                        'kompleksitas' => KompleksitasEnum::Tinggi,
                        'deadline' => $tglMulai->copy()->addWeeks(10),
                        'deskripsi' => 'Mengimplementasikan desain antarmuka responsif ke template Blade Laravel tanpa inline-style dan ramah layar ponsel.',
                        'link_referensi' => 'https://tailwindcss.com/docs/responsive-design',
                        'progres_catatan' => 'Slicing halaman selesai, lulus uji uji responsif pada breakpoint mobile 360px hingga 4K desktop.',
                        'progres_link' => 'https://github.com/kominfo-sleman/frontend-portal-pengaduan',
                        'feedback' => 'Hasil slicing sangat presisi (pixel-perfect) dan semantic markup HTML5 digunakan dengan baik.',
                    ],
                    [
                        'judul' => 'Usability Testing Pengguna Warga & Perhitungan Skor System Usability Scale (SUS)',
                        'kategori' => 'User Research',
                        'kompleksitas' => KompleksitasEnum::Sedang,
                        'deadline' => $tglMulai->copy()->addWeeks(12),
                        'deskripsi' => 'Melakukan pengujian usabilitas tatap muka kepada perwakilan warga dan menghitung nilai SUS score portal pengaduan.',
                        'link_referensi' => 'https://measuringu.com/sus/',
                        'progres_catatan' => 'Pengujian dengan 15 responden menghasilkan skor SUS 84.5 (kategori Grade A / Excellent).',
                        'progres_link' => 'https://kominfo-sleman.github.io/ux-research-report',
                        'feedback' => 'Metodologi testing sangat valid dan menghasilkan insight berharga bagi pengembangan portal publik ke depan.',
                    ],
                ],
                'logbook_kegiatan' => [
                    1 => 'Orientasi magang dan pengenalan alur kerja tim desain grafis dan multimedia Diskominfo Sleman.',
                    2 => 'Analisis arsitektur informasi (information architecture) portal layanan aspirasi warga saat ini.',
                    3 => 'Melakukan audit aksesibilitas WCAG 2.1 AA menggunakan automated tools dan evaluasi manual.',
                    4 => 'Mendokumentasikan pain-points pengguna dan menyusun user persona pemohon layanan publik.',
                    5 => 'Membuat wireframe lo-fi alur pelaporan aspirasi dari input laporan hingga tracking status.',
                    6 => 'Membangun token desain warna, tipografi, dan grid layout pada Figma Design System Kominfo.',
                    7 => 'Merancang prototipe high-fidelity interaktif untuk halaman dashboard warga dan detail laporan.',
                    8 => 'Slicing layout utama ke dalam partials Blade Laravel dengan dukungan CSS terstruktur.',
                    9 => 'Optimasi antarmuka untuk pengalaman mobile-first dan memastikan kompatibilitas screen reader.',
                    10 => 'Menyusun skenario usability testing tugas terpandu (moderated task test) bersama responden warga.',
                    11 => 'Menganalisis hasil usability testing dan melakukan iterasi micro-copy serta tombol navigasi.',
                    12 => 'Penyusunan laporan akhir, demonstrasi prototipe interaktif kepada dinas, dan pengarsipan aset desain.',
                ],
            ],
        ];

        foreach ($individuUsers as $item) {
            $user = User::updateOrCreate(
                ['email' => $item['email']],
                [
                    'nama' => $item['nama'],
                    'password' => $defaultPassword,
                    'role' => RoleEnum::Peserta->value,
                    'email_verified_at' => Carbon::now()->subMonths(4),
                ]
            );

            PesertaProfil::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'jurusan_id' => $jurusan->id,
                    'institusi' => $item['institusi'],
                    'nim_nisn' => $item['nim_nisn'],
                    'jenjang' => JenjangEnum::S1->value,
                    'no_hp' => $item['no_hp'],
                    'alamat' => 'Kabupaten Sleman, D.I. Yogyakarta',
                ]
            );

            $pendaftaran = Pendaftaran::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'tipe' => 'individu',
                ],
                [
                    'bulan_magang_id' => $bulanMagang->id,
                    'kuota_id' => $kuota->id,
                    'kelompok_magang_id' => null,
                    'durasi_bulan' => 3,
                    'status' => StatusPendaftaranEnum::Diterima->value,
                    'verifikator_id' => $admin->id,
                    'diajukan_at' => Carbon::now()->subMonths(4),
                    'diputuskan_at' => Carbon::now()->subMonths(3)->subDays(10),
                ]
            );

            $penempatan = Penempatan::updateOrCreate(
                [
                    'peserta_id' => $user->id,
                ],
                [
                    'pendaftaran_id' => $pendaftaran->id,
                    'bidang_id' => $bidang->id,
                    'pembimbing_id' => $pembimbing->id,
                    'jalur' => JalurEnum::Umum->value,
                    'sumber' => SumberPenempatanEnum::Online->value,
                    'dibuat_oleh' => $admin->id,
                    'tgl_mulai' => $tglMulai->toDateString(),
                    'tgl_selesai' => $tglSelesai->toDateString(),
                    'tgl_ditetapkan_selesai' => $tglSelesai->toDateString(),
                    'status' => StatusPenempatanEnum::Selesai->value,
                    'file_surat_selesai' => null, // Belum diunggah oleh administrasi
                    'tgl_upload_surat_selesai' => null,
                ]
            );

            // 1.1 Buat Tugas Individu + ProgresLog
            $this->seedTugasIndividu($penempatan, $user, $item['tugas']);

            // 1.2 Buat Logbook Mingguan (1 - 12 Minggu)
            $this->seedLogbookMingguan($penempatan, $tglMulai, $item['logbook_kegiatan']);

            // 1.3 Buat Agenda Bimbingan Resmi
            $this->seedAgendaBimbingan($penempatan, $pembimbing, $tglMulai);
        }

        // =========================================================================
        // 2. TIM / KELOMPOK (Tim Cyber Security & CSIRT Sleman)
        // =========================================================================
        $timMembers = [
            'fajar' => [
                'email' => 'fajar.ketua@magkom.test',
                'nama' => 'Fajar Nugraha (Ketua Tim)',
                'nim_nisn' => '21520244011',
                'no_hp' => '081234567811',
                'peran' => 'Ketua Tim & Security Analyst',
                'tugas_mandiri' => [
                    [
                        'judul' => 'Audit Konfigurasi Keamanan Jaringan dan Router Gateway OPD Sleman',
                        'kategori' => 'Network Security',
                        'kompleksitas' => KompleksitasEnum::Sedang,
                        'deadline' => $tglMulai->copy()->addWeeks(4),
                        'deskripsi' => 'Melakukan inspeksi rules firewall dan menutup port yang tidak digunakan pada router edge gateway OPD.',
                        'link_referensi' => 'https://kominfo.slemankab.go.id/network-hardening-guide',
                        'progres_catatan' => 'Inspeksi 12 router OPD selesai, celah open-telnet dan default SNMP community telah ditutup.',
                        'progres_link' => 'https://github.com/kominfo-sleman/network-audit-opd',
                        'feedback' => 'Laporan audit topologi jaringan sangat jelas dengan rekomendasi pengamanan firewall yang solutif.',
                    ],
                    [
                        'judul' => 'Penyusunan Security Incident Response Playbook CSIRT Sleman',
                        'kategori' => 'Incident Response',
                        'kompleksitas' => KompleksitasEnum::Tinggi,
                        'deadline' => $tglMulai->copy()->addWeeks(9),
                        'deskripsi' => 'Menyusun alur penanganan insiden ransomware dan serangan distributed denial of service (DDoS).',
                        'link_referensi' => 'https://bssn.go.id/panduan-csirt',
                        'progres_catatan' => 'Playbook penanganan darurat insiden siber selesai dan diverifikasi oleh tim internal.',
                        'progres_link' => 'https://kominfo-sleman.github.io/csirt-playbook',
                        'feedback' => 'SOP tanggap darurat insiden telah memenuhi standar nasional BSSN dan siap diujicobakan.',
                    ],
                ],
                'logbook' => [
                    1 => 'Orientasi tim siber di ruang CSIRT Sleman, pemetaan aset server, dan pengenalan firewall internal.',
                    2 => 'Scanning topologi jaringan internal Pemkab Sleman dan inventarisasi port terbuka pada edge server.',
                    3 => 'Melakukan evaluasi konfigurasi router gateway OPD dan menganalisis log traffic mikrotik.',
                    4 => 'Menyusun rekomendasi penutupan port rentan dan berkoordinasi dengan tim infrastruktur jaringan.',
                    5 => 'Kolaborasi tim dalam proyek Vulnerability Assessment server web portal resmi OPD Sleman.',
                    6 => 'Menganalisis hasil scan vulnerability scanner dan memvalidasi potensi False Positive.',
                    7 => 'Review tengah magang bersama pembimbing mengenai temuan celah keamanan prioritas kritis.',
                    8 => 'Penyusunan draft alur response insiden keamanan siber ransomware dan defacement portal.',
                    9 => 'Finalisasi dokumen incident response playbook untuk tim CSIRT Pemerintah Kabupaten Sleman.',
                    10 => 'Simulasi penerapan Web Application Firewall (WAF) bersama anggota tim di server uji coba.',
                    11 => 'Penyusunan laporan komprehensif penanganan siber dan bahan presentasi penutupan magang.',
                    12 => 'Presentasi akhir hasil pengujian penetrasi dan serah terima dokumen SOP kepada tim CSIRT Sleman.',
                ],
            ],
            'siti' => [
                'email' => 'siti.anggota@magkom.test',
                'nama' => 'Siti Aisyah (Anggota Tim 1)',
                'nim_nisn' => '21520244022',
                'no_hp' => '081234567812',
                'peran' => 'Anggota Tim & AppSec Auditor',
                'tugas_mandiri' => [
                    [
                        'judul' => 'Penetration Testing Black-Box pada Aplikasi Web Presensi Magang',
                        'kategori' => 'Application Security',
                        'kompleksitas' => KompleksitasEnum::Tinggi,
                        'deadline' => $tglMulai->copy()->addWeeks(5),
                        'deskripsi' => 'Pengujian kerentanan OWASP Top 10 seperti SQL Injection, Stored XSS, dan Broken Access Control (IDOR).',
                        'link_referensi' => 'https://owasp.org/www-project-top-ten/',
                        'progres_catatan' => 'Ditemukan 2 temuan medium IDOR pada download berkas dan langsung diberikan patch mitigasinya.',
                        'progres_link' => 'https://github.com/kominfo-sleman/appsec-presensi-audit',
                        'feedback' => 'Temuan IDOR sangat tajam beserta PoC eksploitasi yang terbukti valid. Patch mitigasi telah lolos retest.',
                    ],
                    [
                        'judul' => 'Analisis Log Keamanan & Konfigurasi Alerting Monitoring SIEM',
                        'kategori' => 'Security Monitoring',
                        'kompleksitas' => KompleksitasEnum::Sedang,
                        'deadline' => $tglMulai->copy()->addWeeks(10),
                        'deskripsi' => 'Membangun filter query Elasticsearch SIEM untuk mendeteksi anomali lonjakan login gagal secara otomatis.',
                        'link_referensi' => 'https://elastic.co/guide/en/security/current',
                        'progres_catatan' => 'Dashboard SIEM dan notifikasi Telegram bot untuk deteksi brute-force attack telah aktif.',
                        'progres_link' => 'https://github.com/kominfo-sleman/siem-alerting-rules',
                        'feedback' => 'Alerting rule terbukti responsif mendeteksi simulasi serangan login secara real-time.',
                    ],
                ],
                'logbook' => [
                    1 => 'Orientasi tim di Seksi Keamanan Informasi, setup Burp Suite Professional dan environment uji.',
                    2 => 'Mempelajari arsitektur otentikasi aplikasi web presensi dan modul RBAC peserta magang.',
                    3 => 'Melakukan pemetaan endpoint API sensitif yang memerlukan validasi otorisasi bertingkat.',
                    4 => 'Eksplorasi pengujian kerentanan SQL Injection dan Cross-Site Scripting (XSS) pada form input.',
                    5 => 'Kolaborasi tim dalam proyek Vulnerability Assessment server web portal resmi OPD Sleman.',
                    6 => 'Menemukan celah IDOR pada endpoint unduh berkas dan menyusun rekomendasi mitigasi scoped-query.',
                    7 => 'Review tengah magang bersama pembimbing mengenai efektivitas patching keamanan aplikasi.',
                    8 => 'Melakukan integrasi log server web Nginx ke dalam pipeline log aggregator Elasticsearch.',
                    9 => 'Menulis rules pendeteksi brute-force login berbasis threshold waktu dan alamat IP pengirim.',
                    10 => 'Simulasi penerapan Web Application Firewall (WAF) bersama anggota tim di server uji coba.',
                    11 => 'Dokumentasi komprehensif celah keamanan web aplikasi dan re-testing remedi celah IDOR.',
                    12 => 'Presentasi penutupan hasil audit keamanan aplikasi web di hadapan pembimbing dan staf CSIRT.',
                ],
            ],
            'bagas' => [
                'email' => 'bagas.anggota@magkom.test',
                'nama' => 'Bagas Wicaksono (Anggota Tim 2)',
                'nim_nisn' => '21520244033',
                'no_hp' => '081234567813',
                'peran' => 'Anggota Tim & Cloud Hardening Specialist',
                'tugas_mandiri' => [
                    [
                        'judul' => 'Hardening Server Linux Ubuntu & Konfigurasi Fail2Ban Anti Brute-Force',
                        'kategori' => 'Server Hardening',
                        'kompleksitas' => KompleksitasEnum::Sedang,
                        'deadline' => $tglMulai->copy()->addWeeks(4),
                        'deskripsi' => 'Pengamanan akses SSH via public key, penonaktifan root password login, dan konfigurasi fail2ban.',
                        'link_referensi' => 'https://ubuntu.com/server/docs/security-hardening',
                        'progres_catatan' => 'Konfigurasi SSH key authentication aktif, service fail2ban sukses memblokir IP penyerang.',
                        'progres_link' => 'https://github.com/kominfo-sleman/server-hardening-ansible',
                        'feedback' => 'Prosedur hardening server sangat terstandarisasi dan dapat direplikasi ke node server lainnya.',
                    ],
                    [
                        'judul' => 'Implementasi SSL/TLS Cipher Suites Hardening & HTTP Security Headers',
                        'kategori' => 'Web Server Security',
                        'kompleksitas' => KompleksitasEnum::Sedang,
                        'deadline' => $tglMulai->copy()->addWeeks(9),
                        'deskripsi' => 'Menerapkan HSTS, Content-Security-Policy (CSP), dan menonaktifkan protokol TLS 1.0/1.1 pada reverse proxy.',
                        'link_referensi' => 'https://ssl-config.mozilla.org/',
                        'progres_catatan' => 'Skor pengujian SSL Labs mencapai Grade A+ dengan dukungan forward secrecy lengkap.',
                        'progres_link' => 'https://github.com/kominfo-sleman/nginx-security-headers',
                        'feedback' => 'Capaian Grade A+ SSL Labs sangat memuaskan, security headers CSP terkonfigurasi ketat dan aman.',
                    ],
                ],
                'logbook' => [
                    1 => 'Orientasi tim di Divisi Infrastruktur & Keamanan Siber, pemahaman topologi server data center.',
                    2 => 'Audit baseline konfigurasi sistem operasi Linux server staging dan review paket aplikasi terpasang.',
                    3 => 'Mengonfigurasi autentikasi SSH berbasis RSA 4096-bit dan mematikan akses root password login.',
                    4 => 'Menerapkan jail fail2ban pada service SSH dan web proxy untuk mitigasi serangan brute-force.',
                    5 => 'Kolaborasi tim dalam proyek Vulnerability Assessment server web portal resmi OPD Sleman.',
                    6 => 'Analisis cipher suites SSL/TLS pada domain pemerintah kabupaten dan mendeteksi cipher lemah.',
                    7 => 'Review tengah magang bersama pembimbing mengenai implementasi secure communication channel.',
                    8 => 'Menerapkan Security Headers (HSTS, CSP, X-Frame-Options) pada konfigurasi reverse proxy Nginx.',
                    9 => 'Menjalankan verifikasi hasil security headers dan benchmark performa handshake TLS 1.3.',
                    10 => 'Simulasi penerapan Web Application Firewall (WAF) bersama anggota tim di server uji coba.',
                    11 => 'Penyusunan dokumentasi teknis checklist hardening server untuk panduan tim sysadmin dinas.',
                    12 => 'Presentasi penutupan magang mengenai hasil peningkatan skor keamanan infrastruktur server.',
                ],
            ],
        ];

        // 2.1 Buat Kelompok Magang
        $ketuaUser = User::updateOrCreate(
            ['email' => $timMembers['fajar']['email']],
            [
                'nama' => $timMembers['fajar']['nama'],
                'password' => $defaultPassword,
                'role' => RoleEnum::Peserta->value,
                'email_verified_at' => Carbon::now()->subMonths(4),
            ]
        );

        $kelompok = KelompokMagang::updateOrCreate(
            ['nama_kelompok' => 'Tim Cyber Security & CSIRT Sleman'],
            ['ketua_user_id' => $ketuaUser->id]
        );
        $kelompok->update(['ketua_user_id' => $ketuaUser->id]);

        $pendaftaranKelompok = Pendaftaran::updateOrCreate(
            ['kelompok_magang_id' => $kelompok->id],
            [
                'user_id' => $ketuaUser->id,
                'bulan_magang_id' => $bulanMagang->id,
                'kuota_id' => $kuota->id,
                'tipe' => 'kelompok',
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::Diterima->value,
                'verifikator_id' => $admin->id,
                'diajukan_at' => Carbon::now()->subMonths(4),
                'diputuskan_at' => Carbon::now()->subMonths(3)->subDays(10),
            ]
        );

        $timCreatedData = [];

        foreach ($timMembers as $key => $member) {
            $userObj = User::updateOrCreate(
                ['email' => $member['email']],
                [
                    'nama' => $member['nama'],
                    'password' => $defaultPassword,
                    'role' => RoleEnum::Peserta->value,
                    'email_verified_at' => Carbon::now()->subMonths(4),
                ]
            );

            PesertaProfil::updateOrCreate(
                ['user_id' => $userObj->id],
                [
                    'jurusan_id' => $jurusan->id,
                    'institusi' => 'UPN "Veteran" Yogyakarta',
                    'nim_nisn' => $member['nim_nisn'],
                    'jenjang' => JenjangEnum::S1->value,
                    'no_hp' => $member['no_hp'],
                    'alamat' => 'Kabupaten Sleman, D.I. Yogyakarta',
                ]
            );

            KelompokAnggota::updateOrCreate(
                [
                    'kelompok_magang_id' => $kelompok->id,
                    'user_id' => $userObj->id,
                ],
                [
                    'nama' => $userObj->nama,
                    'nim_nisn' => $member['nim_nisn'],
                    'email' => $userObj->email,
                    'no_hp' => $member['no_hp'],
                ]
            );

            $penempatanMember = Penempatan::updateOrCreate(
                [
                    'peserta_id' => $userObj->id,
                ],
                [
                    'pendaftaran_id' => $pendaftaranKelompok->id,
                    'bidang_id' => $bidang->id,
                    'pembimbing_id' => $pembimbing->id,
                    'jalur' => JalurEnum::Umum->value,
                    'sumber' => SumberPenempatanEnum::Online->value,
                    'dibuat_oleh' => $admin->id,
                    'tgl_mulai' => $tglMulai->toDateString(),
                    'tgl_selesai' => $tglSelesai->toDateString(),
                    'tgl_ditetapkan_selesai' => $tglSelesai->toDateString(),
                    'status' => StatusPenempatanEnum::Selesai->value,
                    'file_surat_selesai' => null, // Belum diunggah oleh administrasi
                    'tgl_upload_surat_selesai' => null,
                ]
            );

            $timCreatedData[$key] = [
                'user' => $userObj,
                'penempatan' => $penempatanMember,
                'meta' => $member,
            ];

            // 2.2 Tugas Mandiri Per Anggota Tim
            $this->seedTugasIndividu($penempatanMember, $userObj, $member['tugas_mandiri']);

            // 2.3 Logbook Mingguan (1 - 12 Minggu) Per Anggota Tim
            $this->seedLogbookMingguan($penempatanMember, $tglMulai, $member['logbook']);

            // 2.4 Agenda Bimbingan Resmi Per Anggota Tim
            $this->seedAgendaBimbingan($penempatanMember, $pembimbing, $tglMulai);
        }

        // 2.5 Tugas Kelompok / Project Bersama Tim (Di-assign ke seluruh anggota tim)
        $tugasKelompokList = [
            [
                'judul' => '[Project Tim] Vulnerability Assessment & Security Audit Portal Resmi OPD Sleman',
                'kategori' => 'Cyber Security Audit',
                'kompleksitas' => KompleksitasEnum::Tinggi,
                'deadline' => $tglMulai->copy()->addWeeks(6),
                'deskripsi' => 'Pengujian keamanan kolaboratif tim siber pada portal web instansi pemerintah kabupaten mencakup deteksi kerentanan CVE dan miskonfigurasi server.',
                'link_referensi' => 'https://cve.mitre.org/',
                'pelapor_user_id' => $timCreatedData['fajar']['user']->id,
                'progres_catatan' => 'Laporan audit kolektif tim selesai: 4 kerentanan low-medium telah diperbaiki, re-scanning menunjukkan status clean.',
                'progres_link' => 'https://github.com/kominfo-sleman/csirt-audit-report',
                'feedback' => 'Kerja sama tim sangat solid. Dokumentasi laporan audit sangat rapi dan rekomendasi perbaikan tepat sasaran.',
            ],
            [
                'judul' => '[Project Tim] Implementasi Web Application Firewall (WAF) & Rate Limiting Simulasi Serangan',
                'kategori' => 'Defensive Security',
                'kompleksitas' => KompleksitasEnum::Tinggi,
                'deadline' => $tglMulai->copy()->addWeeks(11),
                'deskripsi' => 'Membangun pertahanan lapis aplikasi menggunakan WAF ModSecurity dan simulasi serangan injeksi untuk menguji ketahanan server.',
                'link_referensi' => 'https://coreruleset.org/',
                'pelapor_user_id' => $timCreatedData['bagas']['user']->id,
                'progres_catatan' => 'Ruleset OWASP CRS terpasang pada reverse proxy Nginx, pengujian simulasi serangan SQLi dan XSS berhasil di-block dengan HTTP 403 Forbidden.',
                'progres_link' => 'https://github.com/kominfo-sleman/waf-deployment-guide',
                'feedback' => 'Pertahanan perimeter aplikasi terkonfigurasi dengan sangat baik. Pengujian simulasi serangan memberikan bukti empiris ketahanan sistem.',
            ],
        ];

        $this->seedTugasKelompok($kelompok, $timCreatedData, $tugasKelompokList);
    }

    /**
     * Helper membuat tugas individu beserta TugasPeserta dan ProgresLog yang disetujui.
     *
     * @param  array<int, array<string, mixed>>  $tugasList
     */
    private function seedTugasIndividu(Penempatan $penempatan, User $user, array $tugasList): void
    {
        foreach ($tugasList as $item) {
            $tugas = Tugas::updateOrCreate(
                [
                    'penempatan_id' => $penempatan->id,
                    'judul' => $item['judul'],
                ],
                [
                    'tipe' => 'individu',
                    'kelompok_magang_id' => null,
                    'deskripsi' => $item['deskripsi'],
                    'link_referensi' => $item['link_referensi'],
                    'kategori' => $item['kategori'],
                    'kompleksitas' => $item['kompleksitas'],
                    'deadline' => $item['deadline']->toDateString(),
                    'status' => StatusTugasEnum::Selesai,
                    'status_publikasi' => 'dipublikasikan',
                    'jadwal_publikasi' => $item['deadline']->copy()->subWeeks(2),
                ]
            );

            TugasPeserta::updateOrCreate(
                [
                    'tugas_id' => $tugas->id,
                    'user_id' => $user->id,
                ],
                [
                    'penempatan_id' => $penempatan->id,
                ]
            );

            ProgresLog::updateOrCreate(
                [
                    'tugas_id' => $tugas->id,
                    'user_id' => $user->id,
                ],
                [
                    'catatan' => $item['progres_catatan'],
                    'link' => $item['progres_link'],
                    'tgl_lapor' => $item['deadline']->copy()->subDays(1)->toDateString(),
                    'status_review' => StatusReviewProgresEnum::Diterima,
                    'feedback' => $item['feedback'],
                ]
            );
        }
    }

    /**
     * Helper membuat tugas kelompok / project bersama yang di-assign ke seluruh anggota tim.
     *
     * @param  array<string, array{user: User, penempatan: Penempatan, meta: array<string, mixed>}>  $timCreatedData
     * @param  array<int, array<string, mixed>>  $tugasList
     */
    private function seedTugasKelompok(KelompokMagang $kelompok, array $timCreatedData, array $tugasList): void
    {
        foreach ($tugasList as $item) {
            $tugas = Tugas::updateOrCreate(
                [
                    'kelompok_magang_id' => $kelompok->id,
                    'judul' => $item['judul'],
                ],
                [
                    'tipe' => 'kelompok',
                    'penempatan_id' => null,
                    'deskripsi' => $item['deskripsi'],
                    'link_referensi' => $item['link_referensi'],
                    'kategori' => $item['kategori'],
                    'kompleksitas' => $item['kompleksitas'],
                    'deadline' => $item['deadline']->toDateString(),
                    'status' => StatusTugasEnum::Selesai,
                    'status_publikasi' => 'dipublikasikan',
                    'jadwal_publikasi' => $item['deadline']->copy()->subWeeks(2),
                ]
            );

            // Assign ke setiap anggota tim di tugas_peserta
            foreach ($timCreatedData as $member) {
                TugasPeserta::updateOrCreate(
                    [
                        'tugas_id' => $tugas->id,
                        'user_id' => $member['user']->id,
                    ],
                    [
                        'penempatan_id' => $member['penempatan']->id,
                    ]
                );
            }

            // ProgresLog pengumpulan tugas kelompok
            ProgresLog::updateOrCreate(
                [
                    'tugas_id' => $tugas->id,
                    'user_id' => $item['pelapor_user_id'],
                ],
                [
                    'catatan' => $item['progres_catatan'],
                    'link' => $item['progres_link'],
                    'tgl_lapor' => $item['deadline']->copy()->subDays(1)->toDateString(),
                    'status_review' => StatusReviewProgresEnum::Diterima,
                    'feedback' => $item['feedback'],
                ]
            );
        }
    }

    /**
     * Helper membuat riwayat logbook mingguan 1 s/d 12 yang disetujui pembimbing.
     *
     * @param  array<int, string>  $kegiatanMingguan
     */
    private function seedLogbookMingguan(Penempatan $penempatan, Carbon $tglMulai, array $kegiatanMingguan): void
    {
        for ($m = 1; $m <= 12; $m++) {
            $tglJumat = $tglMulai->copy()->addWeeks($m - 1)->addDays(4);

            Logbook::updateOrCreate(
                [
                    'penempatan_id' => $penempatan->id,
                    'tanggal' => $tglJumat->toDateString(),
                ],
                [
                    'minggu_ke' => $m,
                    'kegiatan' => $kegiatanMingguan[$m] ?? "Pelaksanaan kegiatan dan penugasan magang operasional pada minggu ke-{$m}.",
                    'link_laporan' => 'https://kominfo.slemankab.go.id/logbook-dokumen/minggu-'.$m,
                    'status' => StatusLogbookEnum::Disetujui,
                    'catatan_pembimbing' => "Laporan kegiatan minggu ke-{$m} telah ditinjau dan terverifikasi dengan hasil memuaskan.",
                ]
            );
        }
    }

    /**
     * Helper membuat agenda bimbingan resmi (orientasi, mid-term, evaluasi penutup).
     */
    private function seedAgendaBimbingan(Penempatan $penempatan, User $pembimbing, Carbon $tglMulai): void
    {
        $sesiBimbingan = [
            [
                'judul_kegiatan' => 'Orientasi Peserta Magang & Pemaparan Rencana Kerja Program',
                'tipe' => 'tatap_muka',
                'tgl' => $tglMulai->copy()->addDays(2),
                'jam_mulai' => '09:00',
                'lokasi_ruang' => 'Ruang Rapat Aptika Lantai 2 Diskominfo Sleman',
                'notulensi' => 'Pemaparan aturan kedisiplinan kerja ASN, pembagian jadwal mentoring mingguan, dan target luaran kerja magang.',
            ],
            [
                'judul_kegiatan' => 'Review Tengah Magang (Mid-Term Progress Evaluation)',
                'tipe' => 'tatap_muka',
                'tgl' => $tglMulai->copy()->addWeeks(5)->addDays(2),
                'jam_mulai' => '10:00',
                'lokasi_ruang' => 'Ruang Diskusi Inovasi Diskominfo Sleman',
                'notulensi' => 'Evaluasi capaian tugas 50% masa magang, pembahasan kendala teknis lapangan, dan penyesuaian jadwal penyelesaian.',
            ],
            [
                'judul_kegiatan' => 'Evaluasi Akhir Penutupan Magang & Penyerahan Hasil Kerja',
                'tipe' => 'tatap_muka',
                'tgl' => $tglMulai->copy()->addWeeks(11)->addDays(3),
                'jam_mulai' => '13:30',
                'lokasi_ruang' => 'Ruang Rapat Utama Diskominfo Sleman',
                'notulensi' => 'Presentasi demonstrasi produk luaran magang, serah terima kode sumber dan dokumentasi, serta evaluasi performa bimbingan.',
            ],
        ];

        foreach ($sesiBimbingan as $sesi) {
            AgendaBimbingan::updateOrCreate(
                [
                    'penempatan_id' => $penempatan->id,
                    'judul_kegiatan' => $sesi['judul_kegiatan'],
                ],
                [
                    'pembimbing_id' => $pembimbing->id,
                    'tipe' => $sesi['tipe'],
                    'tgl_bimbingan' => $sesi['tgl']->toDateString(),
                    'jam_mulai' => $sesi['jam_mulai'],
                    'lokasi_ruang' => $sesi['lokasi_ruang'],
                    'notulensi_arahan' => $sesi['notulensi'],
                    'status' => 'selesai',
                ]
            );
        }
    }
}
