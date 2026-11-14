# Dokumen Spesifikasi Fitur, Arsitektur, dan Modul Sistem MagKom
## Portal Manajemen Magang Dinas Komunikasi dan Informatika

---

## 1. Ringkasan Eksekutif & Fondasi Arsitektur

Dokumen ini merupakan referensi teknis komprehensif atas seluruh modul, alur bisnis, arsitektur backend, antarmuka frontend, dan fitur-fitur baru yang telah diselesaikan dan diuji pada sistem **MagKom (Magang Kominfo)**.

### 1.1 Stack Teknologi Utama
- **Framework Backend**: Laravel 10.x (Native Eloquent, Form Request, Service Layer, Action/Query Pattern)
- **Runtime PHP**: PHP 8.3.x (PSR-12, Strict Types, Type Hinting)
- **Frontend Presentation**: Laravel Blade Templates (Bebas dari inline `<style>` dan `<script>` demi keamanan CSP)
- **Styling Architecture**: Vanilla CSS Terstruktur (`resources/css/`), Desain Berbasis Design Tokens (`--magkom-primary-blue`, `--neutral-*`, `--yellow-*`, dll.)
- **Client Interactions**: Vanilla JavaScript Modular ES Modules (`resources/js/`), GSAP Animations, Event Delegation
- **Asset Bundler**: Vite 6.x
- **Basis Data**: MySQL 8.0+ / MariaDB (Mendukung transaksi atomic locking `lockForUpdate`)
- **Suite Pengujian**: PHPUnit / Pest Testing Suite (740+ Automated Feature & Unit Tests)

---

## 2. Pola Arsitektur Backend: Modular Actions & Queries

Untuk menjaga controller tetap ringkas (*Skinny Controllers*) dan mematuhi prinsip *Single Responsibility Principle (SRP)*, sistem mengadopsi pemisahan tugas berbasis:

```
HTTP Request → Middleware/Policy → Form Request → Controller → Action / Query / Service → Eloquent Model → Database
```

### 2.1 Direktori Actions (`app/Actions/`)
Tiap mutasi data (Create, Update, Delete, Transition) diisolasi dalam satu kelas Action mandiri yang dapat digunakan ulang:
- **`app/Actions/Peserta/`**:
  - `CreatePendaftaranAction`: Inisialisasi pengajuan magang dan pengisian biodata.
  - `AjukanPendaftaranAction`: Validasi kelengkapan berkas dan penguncian pendaftaran.
  - `BatalkanPendaftaranAction`: Pembatalan pendaftaran oleh peserta.
  - `UpdatePesertaProfilAction`: Pembaruan profil biodata dinamis peserta.
  - `CreateLogbookAction` & `UpdateLogbookAction`: Pengisian dan revisi aktivitas harian/mingguan.
- **`app/Actions/Admin/`**:
  - `PutuskanPendaftaranAction`: Verifikasi berkas (Terima, Tolak, Minta Perbaikan).
  - `StoreKuotaAction`, `UpdateKuotaAction`, `ToggleKuotaAction`: Pengelolaan kuota jurusan.
  - `StoreBulanMagangAction` & `UpdateBulanMagangAction`: Siklus buka/tutup periode magang.
  - `AssignPesertaAction`, `CancelPenempatanAction`: Penempatan peserta ke bidang dinas.
  - `ReassignPembimbingAction` & `BulkReassignPembimbingAction`: Rotasi dan penugasan pembimbing.
- **`app/Actions/Pembimbing/`**:
  - `CreateBatchTugasAction`: Pembuatan tugas serentak multi-mentee ala classroom.
  - `UpdateTugasAction` & `MarkTugasCompletedAction`: Penyesuaian instruksi dan finalisasi tugas.
  - `ReviewTugasProgresAction`: Penilaian dan umpan balik progres tugas.
  - `ReviewLogbookAction` & `BulkReviewLogbookAction`: Validasi dan persetujuan logbook.

### 2.2 Direktori Specialized Queries (`app/Queries/`)
Pengambilan data terisolasi untuk filter kompleks, pencarian, dan paginasi:
- `PendaftaranIndexQuery`, `KuotaIndexQuery`, `PenempatanIndexQuery`, `PenempatanAntreanQuery` (Admin)
- `TugasIndexQuery`, `LogbookIndexQuery`, `PenempatanAktifQuery`, `LaporanKinerjaQuery` (Pembimbing)
- `PesertaProfilQuery`, `PesertaLogbookIndexQuery` (Peserta)

---

## 3. Modul & Fitur 4 Pilar Pengguna

### 3.1 Portal Publik & Calon Peserta
1. **Landing Page Interaktif**:
   - Tampilan visual responsif dengan Preloader Animasi Sleman (`public/js/landing-interactive.js`).
   - Peta Alur Magang Interaktif (Modal DAG Alur Seleksi).
   - Indikator Ketersediaan Kuota Real-time per jurusan dengan status Penuh/Tersedia.
   - Pilihan pendaftaran mandiri (Individu) maupun Kelompok.
2. **Autentikasi & Keamanan Akun**:
   - Registrasi calon peserta dengan validasi email standar Google/Gmail.
   - Fitur Ganti Password, Verifikasi Email, dan Pembatasan Sesi.
3. **Pemberkasan & Pendaftaran Magang**:
   - Multi-step wizard pendaftaran interaktif.
   - Validasi dokumen persetujuan sesuai regulasi UU PDP.
   - Pratinjau berkas in-app (PDF viewer aman tanpa unduh paksa).
4. **Portal Peserta Terpadu**:
   - Dashboard Bento Grid adaptif tema terang/gelap (Status Pengajuan, Kuota, Timeline).
   - Pengumpulan Tugas Classroom dengan kemampuan **Batal Kirim (Unsubmit)** sebelum tenggat waktu.
   - Logbook mingguan terstruktur dengan multi-lampiran dokumen pendukung.

---

### 3.2 Portal Administrasi (Admin Operasional)
1. **Dashboard Operasional Bento Grid**:
   - Cockpit telemetri antrean pendaftaran dengan deteksi *SLA Kritis* (Early Warning System).
   - Widget ringkasan kuota per jurusan dan slot khusus wilayah Sleman.
2. **Verifikasi Berkas Calon Peserta**:
   - Tabel seleksi cepat dengan filter multi-kategori dan status berkas.
   - Halaman detail verifikasi komprehensif dengan pratinjau berkas side-by-side.
   - Aksi terpadu: Terima, Tolak, atau Minta Perbaikan dengan pencatatan alasan resmi.
3. **Manajemen Kuota & Periode Magang**:
   - Pengaturan kapasitas peserta per bidang dinas dan jurusan yang dibutuhkan.
   - Penjadwalan periode buka/tutup gelombang magang berbasis kalender.
4. **Penempatan Peserta 2-Tahap**:
   - Plotting peserta diterima ke bidang teknis sesuai kesesuaian jurusan.
   - Penugasan pembimbing lapangan secara individual maupun massal (*Bulk Assign*).
   - Manajemen Jalur Khusus (Slot Khusus Sleman) dengan auto-generated credentials.
5. **Pusat Rekapitulasi & Ekspor Laporan**:
   - Ekspor data pendaftaran, penempatan, dan statistik operasional dalam format `.xlsx`, `.csv`, dan `.pdf`.
   - Pembuatan Surat Penerimaan Magang digital resmi dilengkapi verifikasi QR Code.

---

### 3.3 Portal Pembimbing Lapangan
1. **Dashboard Kinerja Pembimbing**:
   - Banner sambutan dinamis dengan identitas mentee yang dibimbing.
   - Kartu KPI pemantauan tugas, logbook tertunda, dan tingkat kehadiran.
2. **Tugas Mentee Ala Classroom**:
   - Pembuatan tugas komprehensif: judul, instruksi teks kaya, tenggat waktu, dan lampiran berkas acuan.
   - Opsi penugasan: ke seluruh mentee atau peserta tertentu secara fleksibel.
   - Pelacakan status: Ditugaskan, Diserahkan, Dinilai, atau Dibatalkan.
   - Riwayat revisi dan dialog umpan balik langsung pada tugas.
3. **Validasi & Penilaian Logbook**:
   - Tinjauan jurnal aktivitas harian/mingguan mentee.
   - Validasi satu per satu atau validasi massal (*Bulk Review*).
4. **Profil 360° Mentee & Rekap Kinerja**:
   - Tampilan profil lengkap riwayat pendidikan, progres tugas, kehadiran, dan kontak darurat mentee.
   - Ekspor Laporan Kinerja Peserta Bimbingan ke lembar kerja Excel/CSV siap cetak.

---

### 3.4 Portal SuperAdmin Eksekutif
1. **Cockpit Telemetri Eksekutif**:
   - Monitoring kesehatan sistem: status aplikasi, penggunaan memori, responsivitas database.
   - Visualisasi tren pendaftaran tahunan dan demografi asal institusi pendidikan.
2. **Audit Trail Terpadu Multi-Kanal**:
   - Pencatatan seluruh mutasi data penting (Create, Update, Delete, Login, Approval).
   - Log mencatat: Aktor, Alamat IP, User Agent, Resource Target, Nilai Sebelum & Sesudah.
   - Fitur inspeksi mendalam dan ekspor log audit terenkripsi.
3. **Manajemen Pengguna & RBAC**:
   - Pengelolaan akun untuk 4 role utama: `SuperAdmin`, `Admin`, `Pembimbing`, `Peserta`.
   - Isolasi hak akses berbasis Laravel Policy dan Spatie-style permission gates.
   - Seeder akun bersih Gmail (`PurgeAndSeedCleanGmailAccountsSeeder`) untuk standarisasi kredensial uji mandiri lintas peran.
4. **Manajemen Master Data & Backup**:
   - Pengelolaan Master Bidang Dinas dan Jurusan.
   - Modul Cadangan Basis Data (Database Backup & Restore) otomatis dan manual.
   - Standarisasi dialog modal konfirmasi aksi kritis (penggantian kata sandi, penonaktifan bidang/jurusan/bulan, dan penghapusan backup) menggantikan dialog native browser.
5. **Navigasi Menu Melayang Sidebar Mini (*Mini-Sidebar Floating Flyout*)**:
   - Arsitektur portal mengambang fixed (`position: fixed; left: 72px; z-index: 1060;`) yang aktif saat sidebar diciutkan (`body.nav-sm`).
   - Penyelarasan palet warna resmi Sleman Navy Blue (`var(--sidebar-bg, #02275D)` pada mode terang dan `#011C44` pada mode gelap).
   - Penanda kapsul aktif royal navy (`#013D8D`), titik cyan bercahaya (`#38BDF8`), serta tooltip interaktif otomatis untuk tautan menu tunggal.

---

## 4. Keamanan Sistem & Kepatuhan OWASP

Sistem telah dirancang dengan pertahanan berlapis (*Defense in Depth*) yang mengimplementasikan mitigasi risiko:
- **SEC-01 (CSRF Protection)**: Seluruh aksi mutasi data via HTTP POST/PUT/DELETE diwajibkan validasi token CSRF.
- **SEC-02 (IDOR & Authorization Isolation)**: Setiap akses dokumen berkas, tugas, dan logbook divalidasi melalui Policy kepemilikan (`can('view', $model)`).
- **SEC-03 (SQL Injection Prevention)**: Menggunakan PDO parameter binding bawaan Eloquent; bebas dari raw string concatenation.
- **SEC-04 (Cross-Site Scripting / XSS)**: Seluruh keluaran Blade menggunakan `{{ }}` auto-escaping; sanitasi input HTML pada deskripsi tugas.
- **SEC-05 (Brute Force & Rate Limiting)**: Throttle middleware aktif pada login, registrasi, pengunggahan berkas, dan endpoint SSE (`/realtime/stream`).
- **SEC-06 (Atomic Concurrency Locking)**: Pengurangan kuota dan klaim slot khusus menggunakan `lockForUpdate()` di dalam transaksi basis data untuk mencegah *race condition*.
- **SEC-07 (Dynamic Anti-Bot SVG Captcha & DOM Shuffling)**: Generator captcha berbasis vektor SVG dinamis internal dengan rotasi noise, decoy field honeypot, dan validasi server-side `CaptchaRule` tanpa ketergantungan library eksternal.
- **SEC-08 (Progressive Lockout & Mandatory Registration)**: Validasi kepatuhan alur registrasi sebelum login, penanganan lockout bertahap anti brute-force, dan diagnostik mandiri reset password via Artisan CLI (`auth:test-reset`).

---

## 5. Panduan Instalasi & Pengujian

### 5.1 Kebutuhan Lingkungan
- PHP `>= 8.3` dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `xml`, `curl`, `gd`, `zip`.
- Composer `2.x`
- Node.js `v20+` & NPM
- MySQL `8.0+`

### 5.2 Langkah Instalasi Standar
```bash
# 1. Kloning repositori
git clone https://github.com/EvilAekt/MAGKOM.git
cd MAGKOM

# 2. Instalasi dependensi backend
composer install --no-dev --optimize-autoloader

# 3. Instalasi dependensi frontend
npm install

# 4. Salin environment dan generate app key
cp .env.example .env
php artisan key:generate

# 5. Konfigurasi kredensial basis data pada .env, kemudian jalankan migrasi & seeding
php artisan migrate --seed

# 6. Kompilasi aset antarmuka produksi
npm run build

# 7. Jalankan server lokal
php artisan serve
```

### 5.3 Menjalankan Pengujian Otomatis
```bash
# Menjalankan seluruh test suite
php artisan test

# Menjalankan analisis statis PHPStan
./vendor/bin/phpstan analyse --memory-limit=1G

# Memastikan standar formatting PSR-12
./vendor/bin/pint --test
```
