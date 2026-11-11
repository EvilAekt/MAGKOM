# MagKom — Portal Manajemen Magang Dinas Komunikasi dan Informatika

<div align="center">
  <img src="public/images/logo-kominfo.svg" alt="Logo Kementerian Komunikasi dan Informatika" width="110" height="110" />
  <br/>
  <img src="public/images/logo-magkom.png" alt="Logo MagKom" width="180" />

  <p><strong>Sistem Informasi Manajemen dan Tata Kelola Magang Terpadu</strong><br/>
  Dinas Komunikasi dan Informatika</p>

  <p>
    <a href="#spesifikasi-teknis"><img src="https://img.shields.io/badge/PHP-8.3.x-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP Version" /></a>
    <a href="#spesifikasi-teknis"><img src="https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat-square&logo=laravel&logoColor=white" alt="Laravel Framework" /></a>
    <a href="#spesifikasi-teknis"><img src="https://img.shields.io/badge/Vite-6.x-646CFF?style=flat-square&logo=vite&logoColor=white" alt="Vite Frontend" /></a>
    <a href="#spesifikasi-teknis"><img src="https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat-square&logo=mysql&logoColor=white" alt="MySQL Database" /></a>
    <a href="#pengujian-otomatis-dan-kualitas-kode"><img src="https://img.shields.io/badge/Tests-740%20Passed-22c55e?style=flat-square" alt="PHPUnit Tests" /></a>
    <a href="#pengujian-otomatis-dan-kualitas-kode"><img src="https://img.shields.io/badge/Code%20Style-PSR--12%20Pint-1e293b?style=flat-square" alt="Laravel Pint" /></a>
    <a href="#hak-cipta-dan-ketentuan-penggunaan"><img src="https://img.shields.io/badge/License-Proprietary-dc2626?style=flat-square" alt="Proprietary License" /></a>
  </p>
</div>

---

> [!CAUTION]
> ### Pemberitahuan Hak Cipta dan Kepemilikan Sistem
> **Hak Cipta (c) 2026 Dinas Komunikasi dan Informatika. Seluruh hak cipta dilindungi undang-undang.**
>
> Sistem informasi, basis data, arsitektur teknis, dan dokumentasi **MagKom** merupakan kekayaan intelektual milik Dinas Komunikasi dan Informatika.
>
> Perangkat lunak ini **bukan perangkat lunak domain publik atau open-source umum**. Penggunaan, penggandaan, modifikasi, pendistribusian ulang, atau pemanfaatan sebagian maupun seluruh kode sumber dan aset sistem tanpa izin tertulis resmi dari Dinas Komunikasi dan Informatika dilarang keras dan melanggar hukum berdasarkan **Undang-Undang Nomor 28 Tahun 2014 tentang Hak Cipta** serta **Undang-Undang Informasi dan Transaksi Elektronik (UU ITE)**.

---

## Ringkasan Eksekutif

**MagKom (Magang Kominfo)** adalah portal web yang dibangun untuk mengelola seluruh siklus program Praktik Kerja Lapangan (PKL) dan magang bagi siswa SMK serta mahasiswa di lingkungan Dinas Komunikasi dan Informatika.

Sebelum implementasi MagKom, proses permohonan magang dilakukan secara konvensional melalui pengantaran surat fisik ke sekretariat dinas atau surat elektronik tanpa standardisasi. Hal ini menimbulkan sejumlah kendala operasional, seperti ketiadaan informasi kuota jurusan yang transparan bagi pemohon, penumpukan berkas yang lambat diverifikasi, tidak terstrukturnya plotting bidang dinas teknis, serta kesulitan pembimbing lapangan dalam memantau presensi aktivitas harian peserta.

MagKom menyatukan seluruh mata rantai administrasi tersebut ke dalam satu aplikasi berbasis web yang akuntabel, transparan, dan terintegrasi dari pendaftaran publik hingga arsip pasca-magang.

---

## Tim Pengembang, Peran & Kontribusi Nyata (Project Contributors & Impact)

Sistem informasi **MagKom** dibangun, dimodernisasi, dan dipelihara secara kolaboratif oleh 3 pengembang inti dengan pembagian domain arsitektural, peran spesifik, dan jejak kontribusi nyata (*tangible impact*) terhadap keberhasilan proyek:

### 1. Adhitya Galang (`@EvilAekt` / `@Evil_Aekt`)
- **Peran di Proyek**: **Lead Software Architect, Backend Core & Security Engineer** (*Project Owner / Technical Lead*)
- **Impact Nyata Terhadap MagKom**:
  - **Pencegahan Kritis Overbooking Kuota (*Zero Race Condition*)**: Merancang penguncian atomik basis data (`lockForUpdate()`) yang menjamin tidak ada kuota minus saat ribuan request pendaftaran masuk serentak.
  - **Evolusi Modular Actions & Queries**: Mentransformasi controller monolitik ribuan baris menjadi 24+ *Modular Actions* (`app/Actions/`) dan *Specialized Queries* (`app/Queries/`) yang terisolasi dan mudah diuji.
  - **Sistem Keamanan & Disaster Recovery**: Membangun mekanisme cadangan basis data mandiri (`.sql.gz`) dengan checksum SHA-256, isolasi otorisasi RBAC dinamis, signed URLs untuk dokumen privat, dan penegakan sanitasi jejak audit multi-kanal.
  - **Workflow Batal Kirim Tugas (*Unsubmit*)**: Mengembangkan arsitektur penarikan tugas peserta untuk revisi berkas secara aman (*secure file reuse*) sebelum dinilai pembimbing.
  - **Penjaminan Mutu & CI/CD**: Memimpin otomatisasi pengujian dengan capaian **740+ automated tests (111 test suites)** berkondisi 100% *passing* di PHP 8.3 strict types.
- **Jobdesk Utama**:
  - Menetapkan arah arsitektur sistem, regulasi kedinasan, dan aturan bisnis definitif (*SSOT*).
  - Merancang skema tabel database, relasi kunci asing, dan transaksi locking kuota.
  - Mengelola arsitektur kernel, middleware otorisasi, dan service layer inti.
  - Wewenang tunggal persetujuan (*approval*) dan *merge* final Pull Request ke branch `main`.

### 2. Aldi Artha Mida (`@aldiartha`)
- **Peran di Proyek**: **Fullstack Developer & Administrative Systems Specialist** (*Admin & Placement Domain Lead*)
- **Impact Nyata Terhadap MagKom**:
  - **Modernisasi Alur Verifikasi Pendaftar**: Membangun antarmuka verifikasi kandidat modern (`admin-pendaftaran`) yang memangkas waktu seleksi berkas dari hitungan hari menjadi menit dengan integrasi pemantau SLA (*SLA Watchdog*).
  - **Plotting Penempatan Cerdas Dua Tahap**: Mengotomatiskan pemetaan jurusan terhadap bidang dinas teknis serta mengembangkan modal penunjukan pembimbing dengan *1-click reassign* dan *bulk assignment*.
  - **Standardisasi Ekspor Dokumen Kedinasan**: Membangun modul ekspor rekapitulasi data pendaftaran, penempatan, dan kinerja ASN ke format CSV, Excel XLSX, dan PDF resmi ber-QR Code dengan proteksi mitigasi *formula injection*.
  - **Harmonisasi Antarmuka Data Admin**: Merekayasa ulang keselarasan visual, calendar popovers, tipografi, dan spasi kolom pada 5 data tables utama di modul Administrasi Kominfo.
  - **Decommissioning Fitur Redundan**: Membersihkan residu fitur lawas seperti *waiting list* dan analitik tahunan guna menjaga performa database tetap optimal.
- **Jobdesk Utama**:
  - Mengelola seluruh logika bisnis dan antarmuka modul Administrasi Kominfo (`/admin/*`).
  - Merancang alur verifikasi berkas ber-SLA dan penerbitan Surat Penerimaan resmi.
  - Mengembangkan sistem pembagian beban bimbingan staf dinas (*mentee workload limiter*).
  - Menyusun query pelaporan dan standardisasi format ekspor berkas spreadsheet.

### 3. Naufal Febrian (`@NAUFALFEBRIAN62`)
- **Peran di Proyek**: **Frontend Specialist & UI/UX Interaction Engineer** (*Public Portal & Peserta Domain Lead*)
- **Impact Nyata Terhadap MagKom**:
  - **Pengalaman Pendaftar Terpadu (*Mobile-First*)**: Merancang antarmuka publik dan portal peserta (`/peserta/*`) yang ergonomis, responsif, dan mudah diakses oleh siswa SMK serta mahasiswa via smartphone.
  - **Inovasi Pendaftaran Tim / Kelompok**: Mengembangkan alur registrasi rombongan magang (ketua + anggota) dengan edukasi kuota atomik dan stepper multi-langkah yang transparan.
  - **Modern Bento Grid Dashboard Peserta**: Membangun dashboard modular peserta yang menyatukan ringkasan masa magang, stepper logbook harian berbatas waktu mundur, kalender aktivitas, dan turn-in tugas ala Google Classroom.
  - **Branding Visual Khas Pemkab Sleman**: Menciptakan animasi pembuka *Sleman Splash Preloader*, efek *dynamic scroll reveal*, dan interaktif *peta alur magang (diorama roadmap)* yang memperkuat identitas resmi instansi.
  - **Harmonisasi Dark Mode & Design Tokens**: Mengimplementasikan 11 Master CSS Peeper Tokens (`DS-MagKom`) dan penyeragaman tema gelap lintas seluruh portal sistem.
- **Jobdesk Utama**:
  - Mengembangkan antarmuka Blade dengan kepatuhan mutlak: **Zero Inline CSS/JS**.
  - Mengelola interaktivitas Vanilla JavaScript (ES Modules) dan animasi mikro berbasis GSAP.
  - Memastikan aksesibilitas antarmuka memenuhi standar WCAG 2.1 AA dan kontras warna yang nyaman.
  - Menguji pengalaman pengguna dan responsivitas layout di seluruh resolusi layar (Mobile, Tablet, Desktop).

---

## Alur Kerja Sistem

Sistem mengorganisir interaksi antar-pengguna dalam alur kerja empat pilar aktor:

```mermaid
flowchart TD
    subgraph PortalPublik ["1. Portal Publik"]
        A["Informasi Kuota Real-Time"] --> B["Pendaftaran Mandiri / Tim"]
        B --> C["Unggah Berkas Persyaratan"]
    end

    subgraph AdminVerifikasi ["2. Verifikasi & Penempatan"]
        C --> D["Antrean Verifikasi Berkas"]
        D --> E{"Pemeriksaan Berkas"}
        E -- "Perlu Perbaikan" --> C
        E -- "Ditolak" --> F["Arsip Penolakan"]
        E -- "Disetujui" --> G["Antrean Belum Ditempatkan"]
        G --> H["Plotting Bidang & Pembimbing Lapangan"]
    end

    subgraph PelaksanaanMagang ["3. Pelaksanaan Magang"]
        H --> I["Peserta Aktif"]
        I --> J["Pengisian Logbook Harian (Maks. H-3)"]
        I --> K["Pengerjaan Tugas Ber-deadline"]
        L["Pembimbing Lapangan"] --> M["Review & Validasi Logbook"]
        L --> N["Pemberian Tugas & Umpan Balik"]
        L --> O["Penyelesaian Masa Magang"]
    end

    subgraph TataKelolaSistem ["4. Tata Kelola SuperAdmin"]
        P["Dynamic RBAC Matrix"]
        Q["13 Parameter Operasional Sistem"]
        R["Audit Trail & Forensic Log"]
        S["Jalur Khusus Internal"]
    end

    H -.-> L
    O --> T["Masa Tenggang Akun (2 Bulan)"]
    T --> U["Penonaktifan Otomatis & Arsip Permanen"]
```

---

## Fungsionalitas Utama Sistem

### 1. Manajemen Kuota dan Alur Pendaftaran Publik
- **Transparansi Kuota Jurusan**: Publik dapat memantau kuota magang yang tersedia per jurusan dan per periode bulan secara real-time pada halaman utama.
- **Proteksi Konkurensi Atomik**: Alokasi kuota menggunakan mekanisme database locking (`lockForUpdate`) untuk memastikan tidak terjadi kondisi *race condition* atau kelebihan kuota ketika pendaftaran masuk bersamaan.
- **Dukungan Pendaftaran Tim**: Sistem mendukung pendaftaran individu maupun kelompok (ketua dan anggota) dengan pembatasan jumlah anggota yang dapat disesuaikan.
- **Seleksi Administratif Tegas**: Pendaftar yang mengajukan permohonan diverifikasi langsung dengan keputusan Diterima atau Ditolak tanpa perpanjangan antrean cadangan.

### 2. Penempatan Dua Tahap (Two-Stage Placement)
- **Pemisahan Verifikasi dan Alokasi**: Admin memverifikasi kelengkapan berkas terlebih dahulu. Pendaftar yang dinyatakan lolos masuk ke antrean *Belum Ditempatkan*.
- **Plotting Bidang dan Pembimbing**: Admin menempatkan peserta ke unit bidang teknis (Aplikasi Informatika, Informasi dan Komunikasi Publik, Statistik, Persandian, atau Sekretariat) serta menunjuk pembimbing lapangan berdasarkan rasio beban mentee yang tersedia.
- **Jalur Khusus Kedinasan**: Mengakomodasi penugasan magang kerja sama kelembagaan melalui alokasi slot internal yang terisolasi penuh dari kuota publik (*zero leak*).

### 3. Pelaksanaan Magang dan Pengendalian Aktivitas
- **Logbook Harian Berbatas Waktu**: Peserta mencatat uraian pekerjaan dan bukti dokumentasi harian. Untuk mencegah pelaporan rapel di akhir masa magang, sistem memberlakukan batas mundur pengisian maksimal 3 hari kerja (`BR-09`) serta mendukung multi-lampiran dokumen pendukung.
- **Tugas Terstruktur & Penugasan Serentak (Batch Classroom)**: Pembimbing dapat menerbitkan instruksi penugasan tunggal maupun tugas serentak (*batch*) ke multi-mentee sekaligus (`BR-19`) dengan tenggat waktu dan lampiran instruksi yang seragam. Status ketepatan penyerahan tugas dikalkulasi secara dinamis pada saat submission (`BR-16`).
- **Alur Batal Kirim Tugas (Unsubmit Workflow)**: Peserta magang memiliki fleksibilitas untuk membatalkan penyerahan tugas (*unsubmit*) yang telah dikirimkan selama tugas tersebut belum dinilai oleh pembimbing (`BR-18`), memungkinkan revisi berkas tanpa perlu meminta pembatalan manual ke admin.
- **Penyelesaian Masa Magang**: Pembimbing lapangan dapat menandai penyelesaian magang peserta setelah seluruh periode kerja dan target penugasan terpenuhi.

### 4. Pengawasan SLA Verifikasi Berkas (SLA Watchdog)
- Menghitung waktu tunggu berkas pendaftaran terhadap target Service Level Agreement dinas (standar 5 hari kerja).
- Antrean verifikasi menampilkan indikator visual bagi berkas yang mendekati atau telah melampaui batas SLA agar segera ditindaklanjuti oleh verifikator.

### 5. Matriks Perizinan Dinamis (Dynamic RBAC)
- SuperAdmin dapat mengelola hak akses perizinan secara granular untuk seluruh peran pengguna melalui matriks perizinan visual.
- Penambahan hak akses baru dan penyesuaian peran dapat dilakukan langsung melalui antarmuka administratif tanpa perlu mengubah berkas konfigurasi statis.

### 6. Rekam Jejak Audit (Audit Trail)
- Seluruh mutasi entitas utama (pendaftaran, penempatan, kuota, peran, dan konfigurasi) dicatat dalam tabel audit append-only yang kebal manipulasi.
- Sanitasi otomatis dijalankan terhadap informasi sensitif seperti token, kata sandi, dan kredensial sebelum dicatat ke dalam log.
- Dilengkapi fasilitas ekspor riwayat audit ke format spreadsheet yang terproteksi dari risiko formula injection.

### 7. Konfigurasi Aturan Bisnis Terpusat (13 Parameter Baku)
Aturan operasional sistem dikendalikan terpusat melalui panel SuperAdmin tanpa memerlukan deployment ulang:
1. Batas kuota umum publik per bulan.
2. Default alokasi slot jalur khusus.
3. Batas maksimal mentee per pembimbing lapangan.
4. Batas maksimal anggota pendaftaran kelompok.
5. Durasi magang maksimal yang diizinkan (bulan).
6. Batas mundur maksimal pengisian logbook (hari kerja).
7. Batas ukuran maksimal berkas unggahan (MB).
8. Ambang batas peringatan mendekati deadline tugas (hari).
9. Target SLA verifikasi berkas pendaftaran (hari kerja).
10. Durasi masa tenggang akun pasca-magang selesai (bulan).
11. Masa retensi data audit log (hari).
12. Sakelar fitur analitik eksekutif tahunan.
13. Sakelar fitur pendaftaran kelompok/tim.

### 8. Desain Antarmuka Bento Grid Modern & Paritas Akun
- **Bento Grid Architecture**: Tata letak dashboard interaktif modern pada modul Peserta dan Admin yang mengintegrasikan metrik utama, kalender presensi, linimasa progres, dan status tugas secara ergonomis.
- **Sleman Splash Preloader & Micro-interactions**: Pengalaman transisi antar-halaman yang mulus dengan animasi pembuka berlogo khas Pemerintah Kabupaten Sleman serta interaksi berbasis GSAP.
- **Paritas Pengaturan Akun & Profil**: Penyeragaman antarmuka profil pengguna fluid fullwidth, preferensi notifikasi, dan dropdown navigasi akun secara konsisten di seluruh 4 peran aktor.

---

## Peran Pengguna dan Hak Akses

| Peran | Deskripsi Tanggung Jawab | Lingkup Antarmuka |
| :--- | :--- | :--- |
| **SuperAdmin** | Mengelola konfigurasi aturan bisnis sistem, matriks perizinan RBAC, akun staf kedinasan, pemantauan telemetri, alokasi slot khusus, dan inspeksi jejak audit. | `/superadmin/*` |
| **Admin Verifikator** | Memeriksa berkas permohonan pendaftar, mengelola kuota jurusan per periode, melakukan penempatan dua tahap, dan memantau antrean SLA. | `/admin/*` |
| **Pembimbing Lapangan** | Mengawasi peserta bimbingan, meninjau dan memvalidasi logbook kegiatan harian, memberikan penugasan teknis, serta menandai penyelesaian magang. | `/pembimbing/*` |
| **Peserta Magang** | Melakukan pendaftaran (individu atau kelompok), mengunggah berkas persyaratan, mengisi logbook kegiatan harian, dan menyerahkan hasil pengerjaan tugas. | `/peserta/*` |

---

## Spesifikasi Teknis

| Komponen | Rincian Spesifikasi |
| :--- | :--- |
| **Bahasa Pemrograman** | PHP 8.3.x (Type Hinting Ketat, `declare(strict_types=1);`) |
| **Framework Backend** | Laravel 10.x (Pola *Modular Actions & Specialized Queries Pattern*) |
| **Basis Data** | MySQL 8.0+ / MariaDB (InnoDB Storage Engine, Foreign Keys, Transactions) |
| **Frontend Pipeline** | Vite 6.x dengan Vanilla CSS Design Tokens dan Vanilla ES Modules |
| **Penyimpanan Berkas** | Storage Disk Privat terproteksi Temporary Signed URLs |
| **Standar Keamanan** | Proteksi CSRF, Anti-IDOR Authorization, Input Sanitization, Anti-Formula Injection, Header HTTP Terenkapsulasi (CSP, X-Frame-Options, X-Content-Type-Options) |
| **Linter dan Formatter** | Laravel Pint (Standar PSR-12) |
| **Analisis Statis** | Larastan / PHPStan Level 2 (Memory Limit 1G) |
| **Framework Pengujian** | PHPUnit 10.x (740+ Automated Feature & Unit Tests, 111 Test Suites dengan basis data SQLite in-memory) |

---

## Panduan Instalasi dan Deployment

### 1. Prasyarat Lingkungan
- PHP versi 8.3 dengan ekstensi aktif: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip`.
- Composer 2.x
- Node.js versi 18 atau 20 LTS dan NPM
- Database MySQL Server 8.0 atau yang lebih baru

### 2. Pemasangan Kode Sumber dan Dependensi
```bash
git clone https://github.com/EvilAekt/MAGKOM.git
cd MAGKOM

# Pasang dependensi backend
composer install --optimize-autoloader --no-interaction

# Pasang dependensi frontend dan kompilasi aset
npm ci
npm run build
```

### 3. Konfigurasi Lingkungan
```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan parameter koneksi basis data pada berkas `.env`:
```env
APP_NAME=MagKom
APP_ENV=production
APP_DEBUG=false
APP_URL=https://magang.kominfo.go.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=magkom_db
DB_USERNAME=user_database
DB_PASSWORD=kata_sandi_database
```

### 4. Migrasi Basis Data dan Seeding Awal
```bash
# Menjalankan skema basis data dan master data konfigurasi
php artisan migrate --seed --force

# Menghubungkan direktori penyimpanan
php artisan storage:link
```

### 5. Optimasi Kinerja Produksi
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Pengujian Otomatis dan Kualitas Kode

Setiap perubahan kode diverifikasi melalui rangkaian pengujian otomatis dan pipeline CI/CD GitHub Actions:

```bash
# Menjalankan seluruh test suite otomatis (740 tests)
php artisan test

# Menjalankan analisis statis PHPStan
./vendor/bin/phpstan analyse --memory-limit=1G

# Memeriksa standar penulisan kode PSR-12
./vendor/bin/pint --test
```

### Komponen Pengujian Utama
- **`CoreFoundationTest`**: Pengujian integritas model, casting enumerasi, dan immutability audit log.
- **`QuotaConcurrencyTest`**: Pengujian integritas transaksi atomik alokasi kuota pendaftaran.
- **`AdminPenempatanDuaTahapTest` & `AdminPenempatanArchitectureAndSecurityTest`**: Pengujian alur penempatan peserta dua tahap, locking atomik, dan rotasi pembimbing massal.
- **`TugasLogbookTest` & `PesertaTugasBatalKirimTest`**: Pengujian batas mundur logbook, alur unsubmit tugas peserta, dan pencegahan manipulasi status tugas.
- **`PembimbingTugasArchitectureSafetyTest` & `PesertaTugasClassroomUploadTest`**: Pengujian pemisahan controller ke Actions/Queries, penugasan batch classroom, dan penggunaan ulang lampiran.
- **`PembimbingLaporanKinerjaExportArchitectureTest`**: Pengujian sanitasi formula injection pada ekspor CSV/XLSX kinerja peserta.
- **`PredikatBatasValidationTest` & `SuperAdminRolePermissionTest`**: Pengujian batasan validasi 13 parameter operasional dan isolasi otorisasi RBAC dinamis.
- **`EndToEndUatTest`**: Pengujian end-to-end skenario alur magang dari registrasi hingga penonaktifan akun pasca-magang.

---

## Struktur Repositori

```text
MAGKOM/
├── app/
│   ├── Actions/               # Kelas mutasi bisnis tunggal (Admin, Pembimbing, Peserta)
│   ├── Console/Commands/      # Perintah CLI dan tugas terjadwal
│   ├── Exports/               # Format ekspor spreadsheet terproteksi (CSV, Excel)
│   ├── Http/
│   │   ├── Controllers/       # Skinny HTTP controllers
│   │   ├── Middleware/        # Middleware autentikasi, verifikasi peran, dan CSP
│   │   └── Requests/          # Validasi form request terpusat
│   ├── Models/                # Model Eloquent dan relasi basis data
│   ├── Queries/               # Specialized query objects (filter, sorting, paginasi)
│   └── Services/              # Logika bisnis terisolasi (Placement, Audit, Metrics, CMS)
├── database/
│   ├── migrations/            # Berkas migrasi skema basis data
│   └── seeders/               # Seeder data master dan pengguna awal
├── docs/
│   ├── README.md              # Peta navigasi pusat dokumentasi
│   ├── PRODUCT.md             # Spesifikasi produk & batasan sistem
│   ├── 01-PRD-MagKom.md       # Product Requirements Document (PRD v1.3)
│   ├── 02-TDD-MagKom.md       # Technical Design Document (TDD v1.4)
│   ├── 04-SKEMA-DATABASE...   # Skema basis data dan kamus data
│   ├── 05-KEAMANAN-RBAC...    # Matriks keamanan dan otorisasi
│   ├── 08-DOKUMENTASI-LENGKAP... # Dokumentasi fitur & modul sistem komprehensif
│   ├── DEVELOPMENT-GUIDE.md   # Panduan instalasi dan quality gates
│   └── FRONTEND-ARCHITECTURE.md # Arsitektur aset Vite dan panduan Blade
├── resources/
│   ├── css/                   # Stylesheet Vanilla CSS scoped per modul (DS-MagKom)
│   ├── js/                    # Modul Vanilla JavaScript (ES Modules & GSAP)
│   └── views/                 # Template Blade modular (Zero inline CSS/JS)
├── routes/
│   └── web.php                # Definisi rute aplikasi
├── tests/
│   └── Feature/               # 740+ automated feature tests (111 test suites)
├── CHANGELOG.md               # Catatan rilis dan riwayat versi
├── CONTRIBUTING.md            # Panduan kontribusi pengembang & struktur tim
├── LICENSE.md                 # Lisensi kepemilikan dan hak cipta
├── README.md                  # Halaman utama repositori
└── SECURITY.md                # Kebijakan pelaporan kerentanan keamanan
```

---

## Hak Cipta dan Ketentuan Penggunaan

Seluruh hak cipta, hak kekayaan intelektual, dan kepemilikan hukum atas perangkat lunak **MagKom (Magang Kominfo)**, termasuk kode sumber, desain antarmuka, struktur basis data, aset grafis, dan dokumentasi terkait adalah milik mutlak **Dinas Komunikasi dan Informatika**.

Dilarang keras menyalin, memodifikasi, menggandakan, menerbitkan ulang, mendistribusikan, menyewakan, atau mengomersialkan sistem ini baik sebagian maupun seluruhnya tanpa persetujuan tertulis resmi dari pihak Dinas Komunikasi dan Informatika.

Hak Cipta (c) 2026 Dinas Komunikasi dan Informatika. Seluruh hak cipta dilindungi undang-undang.
