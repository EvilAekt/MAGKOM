# Plan: Redesign Tampilan Dashboard 4 Role Mengadopsi Kerangka Gentelella (Spesifikasi Senior Dev → Junior / AI)

- **Tanggal**: 2026-10-21
- **Branch Kerja**: `test-2`
- **Status**: Draft / Menunggu Persetujuan User
- **Penulis**: Senior Software Engineer
- **Target Pembaca**: Junior Developer atau AI Model yang lebih terjangkau (misal: Gemini 3.8 Flash). Dokumen ini ditulis sangat eksplisit, langkah demi langkah (*atomic instructions*), tanpa instruksi abstrak. Kerjakan **per tugas, berurutan**, jangan melompat antar tugas.
- **Referensi Desain**: [ColorlibHQ/gentelella](https://github.com/ColorlibHQ/gentelella) (Kerangka tata letak, komponen panel, dan hierarki navigasi).

---

## 0. Baca Ini Dulu Sebelum Menulis Kode (Wajib Paham 5 Menit)

### 0.1 Tujuan Utama
Merombak **arsitektur tampilan (kerangka UI/UX)** halaman dashboard untuk 4 role di MagKom:
1. **SuperAdmin** (`/superadmin/dashboard`)
2. **Admin Operasional** (`/admin/dashboard`)
3. **Pembimbing Lapangan** (`/pembimbing/dashboard`)
4. **Peserta Magang** (`/peserta/dashboard`)
serta kerangka layout pembungkus utamanya (`resources/views/layouts/dashboard.blade.php` dan `resources/views/layouts/peserta.blade.php`).

Tampilan baru mengadopsi struktur visual dan anatomi komponen khas template **Gentelella**, namun **WAJIB mempertahankan identitas ciri khas MagKom**:
- **Warna & Palet**: Memakai sistem token MagKom Sleman (Deep Navy & Kominfo Blue), **BUKAN** warna hijau/abu-abu bawaan Gentelella asli.
- **Branding & Logo**: Memakai logo resmi Pemerintah Kabupaten Sleman / MagKom (`partials.logo-sleman`).
- **Zero Business Logic Changes**: **HANYA MENGUBAH TAMPILAN (VIEW/CSS/BLADE)**. Dilarang keras mengubah logic query database, nama variabel controller, method backend, route link, CSRF logout, atau aturan otorisasi/role.

---

### 0.2 Aturan Kompatibilitas Framework: Hindari Ketergantungan Legacy
> ⚠️ **CATATAN KRUSIAL DARI SENIOR ARSITEK:**
> Template Gentelella asli di GitHub menggunakan jQuery dan Bootstrap 3/4 lama.
> **DILARANG MENGIMPOR ATAU MENG-INSTALL JQUERY MAUPUN BOOTSTRAP 3!**
> Seluruh struktur class khas Gentelella (`left_col`, `top_nav`, `right_col`, `tile_count`, `x_panel`, `x_title`, `x_content`, `panel_toolbox`) diimplementasikan menggunakan **CSS Modern / Vanilla CSS Variables** dan **Vanilla JavaScript ringan**. Ini menjaga performa aplikasi tetap super cepat, kompatibel dengan CSP produksi (`script-src 'self'`), dan bebas dari *library bloating*.

---

### 0.3 Yang BOLEH Diubah
| File / Direktori | Keterangan & Ruang Lingkup |
|---|---|
| `resources/views/layouts/dashboard.blade.php` | Merestrukturisasi kerangka layout admin, pembimbing, superadmin menjadi 3 kolom Gentelella (`left_col`, `top_nav`, `right_col`, `footer`). |
| `resources/views/layouts/peserta.blade.php` | Menyelaraskan layout peserta dengan estetika kerangka Gentelella yang konsisten. |
| `resources/views/components/gentelella/*` | **Baru**: Komponen Blade reusable (`x-panel`, `tile-stat`, `profile-badge`, `page-header`). |
| `resources/views/superadmin/dashboard.blade.php` & partials | Rekonfigurasi tampilan ke format metric tiles & X-Panels. |
| `resources/views/admin/dashboard.blade.php` | Restrukturisasi tampilan dashboard operasional ke format metric tiles & X-Panels. |
| `resources/views/pembimbing/dashboard.blade.php` | Restrukturisasi dashboard bimbingan ke format metric tiles & X-Panels. |
| `resources/views/peserta/dashboard.blade.php` | Restrukturisasi dashboard peserta ke format metric tiles & X-Panels. |
| `public/js/gentelella-theme.js` | **Baru**: Script vanilla JS untuk toggle sidebar (`nav-sm` vs `nav-md`), collapse panel, dan dropdown. |
| `tests/Feature/GentelellaDashboardRevampTest.php` | **Baru**: Test suite penjaminan layout dan integrasi 4 role. |

---

### 0.4 Yang DILARANG KERAS Diubah
- **Controller Backend**:
  - `app/Http/Controllers/SuperAdmin/DashboardController.php`
  - `app/Http/Controllers/Admin/DashboardController.php`
  - `app/Http/Controllers/Pembimbing/DashboardController.php`
  - `app/Http/Controllers/Peserta/DashboardController.php`
  *(Nama-nama variabel yang dikirim ke view seperti `$totalPeserta`, `$kuotaPerBidang`, `$statusBimbingan`, dll. tidak boleh diganti).*
- **Routing & Middleware**: File `routes/web.php` dan permission middleware tidak boleh diotak-atik.
- **Database & Model**: Migrasi, Model Eloquent, Enum, dan Relasi dilarang diubah.
- **Teks Penting & Assertion Test**: Seluruh teks menu yang dicek oleh unit test (seperti `Verifikasi Berkas`, `Manajemen Kuota`, `Waiting List`, `Penempatan`, `Laporan & Ekspor`, `Bulan Magang`, `Rubrik Nilai`, `Tugas Peserta`, `Reviu Logbook`, `Profil Saya`) **WAJIB TETAP ADA**.

---

### 0.5 Token Warna MagKom Wajib (Design System Resmi Kominfo Sleman)
Jangan gunakan warna heksadesimal baru sembarangan. Gunakan token CSS yang sudah tersedia di root:

| Elemen UI | Token CSS MagKom | Kode Hex / Nilai | Catatan Penggunaan |
|---|---|---|---|
| **Primary Brand (Aksen Gentelella)** | `var(--blue-700)` | `#0641CC` | Warna utama sidebar, brand emblem, tombol utama |
| **Secondary Brand / Active** | `var(--blue-600)` | `#0751FF` | Status aktif menu, link hover, highlight angka |
| **Sidebar Background** | `var(--blue-700)` / `var(--neutral-900)` | `#0641CC` (Mode Terang) / `#111C2E` (Mode Gelap) | Latar sidebar kiri (`left_col`) |
| **Canvas Background** | `var(--neutral-150)` | `#F5F7FB` (Terang) / `#0A0F1D` (Gelap) | Latar konten utama (`right_col`) |
| **Panel / Card Background** | `var(--neutral-0)` | `#FFFFFF` (Terang) / `#182234` (Gelap) | Latar `x_panel` dan `tile_stats_count` |
| **Borders & Separator** | `var(--neutral-200)` / `var(--neutral-400)` | `#EEF0F5` / `#D0D3D9` | Garis batas panel, pembatas header |
| **Teks Utama (Headings)** | `var(--neutral-800)` | `#1D2530` / `#F8FAFC` | Judul panel, angka besar KPI |
| **Teks Sekunder (Muted)** | `var(--neutral-500)` / `var(--neutral-600)` | `#7A8494` / `#666666` | Label metrik, keterangan kecil |
| **Status Sukses** | `var(--green-600)` & `var(--green-100)` | `#16A34A` & `#E8F7EE` | Badge disetujui, kuota aman, kenaikan % |
| **Status Peringatan** | `var(--amber-700)` & `var(--amber-100)` | `#92400E` & `#FFFBEB` | Menunggu reviu, antrean kuota |
| **Status Bahaya** | `var(--red-600)` & `var(--red-100)` | `#DC2626` & `#FEF2F2` | Kuota penuh, ditolak, SLA lewat |

---

## 1. Blueprint Arsitektur Tampilan Gentelella untuk MagKom

### 1.1 Anatomi Tata Letak Global Gentelella (`main_container`)
Tata letak terdiri dari 3 blok utama:
```text
┌──────────────────────────────────────────────────────────────────────────────────┐
│ [1] SIDEBAR KIRI (left_col / col-md-3)                                           │
│  - Logo & Brand Title (site_title)                                               │
│  - Profil Pengguna Cepat (profile_pic + profile_info: "Selamat Datang," & Role)  │
│  - Menu Navigasi Berkelompok (menu_section > side-menu)                          │
│  - Footer Aksi Sidebar (sidebar-footer: Settings, DarkMode, Help, Logout)        │
├──────────────────────────────────────────────────────────────────────────────────┤
│ [2] TOP NAVIGATION BAR (top_nav / nav_menu)                                      │
│  - Hamburger Toggle Menu (nav toggle -> #menu_toggle) [Memperkecil/Membuka]      │
│  - Breadcrumb Trail Lokasi Halaman                                               │
│  - Kanan: User Profile Dropdown + Quick Notifications Icon                       │
├──────────────────────────────────────────────────────────────────────────────────┤
│ [3] KONTEN UTAMA (right_col / role="main")                                       │
│  - Page Title Row (Judul Halaman & Sub-deskripsi)                                │
│  - Row Top Metric Tiles (tile_count: 4-6 Kartu Statistik Ringkas)               │
│  - Content Grid:                                                                 │
│    * x_panel (Panel Kotak dengan x_title, panel_toolbox, & x_content)            │
│    * Grid 2 Kolom (kiri 8 col, kanan 4 col atau 6 col / 6 col)                   │
├──────────────────────────────────────────────────────────────────────────────────┤
│ [4] FOOTER APLIKASI (footer)                                                     │
│  - Hak Cipta Dinas Komunikasi dan Informatika Kabupaten Sleman                   │
└──────────────────────────────────────────────────────────────────────────────────┘
```

### 1.2 Wireframe Desktop (1440px)
```text
+-----------------------+--------------------------------------------------------------+
| [LOGO] MagKom Sleman  |  [=] Beranda / Dashboard                       (O) Budi (V)  |
|                       +--------------------------------------------------------------+
|  (Foto) Budi Santoso  |  Dashboard Operasional MagKom                                |
|  Admin Magang         |  Pusat komando pemantauan dan pengelolaan peserta magang     |
|                       +--------------------------------------------------------------+
| --- MENU UTAMA ---    |  [TILE 1]        [TILE 2]        [TILE 3]        [TILE 4]    |
| [*] Dashboard         |  Total Peserta   Verifikasi      Kuota Terisi    Sisa Kuota  |
| [>] Seleksi & Berkas  |  128 Peserta     14 Menunggu     85 / 100        15 Kursi    |
| [>] Penempatan Magang |  ^ +12% bln lalu * Perlu aksi    85% kapasitas   (Bulan Okt) |
| [>] Logbook & Nilai   +---------------------------------------+----------------------+
| [>] Master Data       |  [X_PANEL 1]                          | [X_PANEL 2]          |
|                       |  Antrean Verifikasi Berkas Terbaru [-] | Ringkasan Kuota  [-] |
| --- SISTEM ---        |  ------------------------------------ | -------------------- |
| [*] Pengaturan        |  Tabel daftar peserta pendaftar baru   | Progress bar kuota   |
|                       |  [Nama] [Kampus] [Status] [Aksi]      | per unit bidang      |
|                       +---------------------------------------+----------------------+
| [Settings][Dark][Exit]|  Hak Cipta (c) 2026 Dinas Komunikasi dan Informatika Sleman  |
+-----------------------+--------------------------------------------------------------+
```

---

## 2. Pemetaan Fitur Dashboard 4 Role ke Komponen Gentelella

### 2.1 Role 1: SuperAdmin (`/superadmin/dashboard`)
- **Metric Tiles (`tile_count`)**:
  1. `Total Pengguna Terdaftar` (Ikon: Users, Angka, Trend)
  2. `Menunggu Verifikasi Staf/Akun` (Ikon: Clock, Angka, Status Waspada)
  3. `Peserta Magang Aktif` (Ikon: UserCheck, Pemisahan Jalur Umum & Khusus)
  4. `Akun Nonaktif / Selesai` (Ikon: UserX, Angka)
- **Panel Utama (`x_panel`)**:
  - `Panel 1 (col-md-8)`: **Pengguna & Staf Terbaru** (Tabel ringkas pengguna baru, avatar, role chip, tombol Kelola Pengguna).
  - `Panel 2 (col-md-4)`: **Postur Keamanan & Status Server** (Indikator CSP, status database, log audit terakhir).
  - `Panel 3 (col-md-12)`: **Tata Kelola Modul & Master Data** (Akses cepat Bulan Magang, Slot Khusus, Rubrik Penilaian).

### 2.2 Role 2: Admin Operasional (`/admin/dashboard`)
- **Metric Tiles (`tile_count`)**:
  1. `Verifikasi Berkas Pending` (Angka pendaftar baru yang perlu diverifikasi segera)
  2. `Kapasitas Kuota Bulan Ini` (Total kuota vs kuota terpakai)
  3. `Antrean Waiting List` (Jumlah calon peserta dalam daftar tunggu)
  4. `Peserta Sedang Magang` (Jumlah peserta aktif di seluruh bidang)
- **Panel Utama (`x_panel`)**:
  - `Panel 1 (col-md-8)`: **Pendaftar Terbaru Menunggu Verifikasi** (Daftar verifikasi instan dengan tombol cepat).
  - `Panel 2 (col-md-4)`: **Monitor Kuota per Bidang Dinas** (Progress bar kapasitas: Bidang IKP, Aptika, E-Gov, dll.).
  - `Panel 3 (col-md-12)`: **Aksi Penempatan Cepat & Waiting List** (Tabel siap tempatkan dengan tombol eksekusi).

### 2.3 Role 3: Pembimbing Lapangan (`/pembimbing/dashboard`)
- **Metric Tiles (`tile_count`)**:
  1. `Total Mentee / Anak Bimbingan` (Jumlah mahasiswa/siswa aktif di bawah bimbingannya)
  2. `Tugas Menunggu Reviu` (Jumlah tugas terkumpul yang belum dinilai)
  3. `Logbook Harian Perlu Validasi` (Logbook hari ini yang butuh ACC)
  4. `Nilai Akhir Belum Terbit` (Peserta yang mendekati masa selesai magang)
- **Panel Utama (`x_panel`)**:
  - `Panel 1 (col-md-8)`: **Daftar Tugas & Pengumpulan Terbaru** (Daftar submisi tugas peserta dengan status telaah).
  - `Panel 2 (col-md-4)`: **Jadwal & Agenda Bimbingan** (Jadwal kehadiran, mentoring, dan catatan kendala).
  - `Panel 3 (col-md-12)`: **Direktori Peserta Bimbingan Aktif** (Daftar mentee, kampus, asal jurusan, dan progres logbook).

### 2.4 Role 4: Peserta Magang (`/peserta/dashboard`)
- **Metric Tiles (`tile_count`)**:
  1. `Hari Berjalan Magang` (Contoh: "Hari ke-24 / 90 hari" dengan progress bar)
  2. `Logbook Terverifikasi` (Jumlah logbook disetujui pembimbing)
  3. `Tugas Selesai Dikumpulkan` (Rasio tugas selesai vs total tugas)
  4. `Status Nilai & Evaluasi` (Predikat / status lembar nilai saat ini)
- **Panel Utama (`x_panel`)**:
  - `Panel 1 (col-md-8)`: **Progres Tugas & Aktivitas Terkini** (Tugas yang perlu dikerjakan, deadline, link pengumpulan).
  - `Panel 2 (col-md-4)`: **Informasi Penempatan & Pembimbing** (Nama pembimbing, kontak dinas, lokasi unit kerja, nomor darurat).
  - `Panel 3 (col-md-12)`: **Formulir Pengisian Cepat Logbook Harian** (Shortcut isi aktivitas hari ini).

---

## 3. Komponen Blade Reusable Khas Gentelella (Design System MagKom)

Buat folder baru: `resources/views/components/gentelella/`
1. **`tile-stat.blade.php`**:
   Komponen metrik angka besar dengan ikon, label atas, dan badge perubahan/status bawah.
2. **`x-panel.blade.php`**:
   Panel kartu standar dengan header `x_title`, `h2`, toolbox ikon (collapse button, settings, close), dan pembungkus isi `x_content`.
3. **`sidebar-profile.blade.php`**:
   Avatar bulat (`img-circle profile_img`), teks sambutan ("Selamat Datang,"), nama user tebal, dan chip peran.
4. **`page-title.blade.php`**:
   Header halaman berisi judul utama, deskripsi ringkas, dan slot aksi cepat (tombol kanan).

---

## 4. Rincian Paket Tugas (Sprint Execution Plan)

Legenda Tingkat Kesulitan:
- 🟢 **Mudah** (Dapat dikerjakan mandiri oleh AI terjangkau / Junior Dev)
- 🟡 **Sedang** (Memerlukan ketelitian struktur markup & CSS)
- 🔴 **Tinggi** (Perlu review Senior Developer terkait kompatibilitas layout & testing)

---

### Tugas 1: Baseline Test & Persiapan Test Suite Baru [x] SELESAI
- **Tingkat**: 🟢 Mudah
- **Status**: ✅ Selesai (Terverifikasi `GentelellaDashboardRevampTest` 6 test passed, 26 assertions)
- **Estimasi**: 30 Menit
- **Instruksi untuk Junior/AI**:
  1. Pastikan sedang berada di branch `test-2`.
  2. Jalankan test suite dashboard eksisting untuk memastikan kondisi awal hijau:
     `php artisan test --filter=Dashboard`
  3. Buat file test baru `tests/Feature/GentelellaDashboardRevampTest.php`.
  4. Tulis skenario pengujian dasar yang memastikan:
     - Dashboard 4 role dapat dibuka (HTTP 200) untuk masing-masing user: SuperAdmin, Admin, Pembimbing, dan Peserta.
     - Setiap dashboard merender class wrapper Gentelella (`main_container`, `left_col`, `top_nav`, `right_col`, `tile_count`, `x_panel`).
- **Kriteria Verifikasi**:
  - File test tercipta, dijalankan dengan `php artisan test --filter=GentelellaDashboardRevampTest`.

---

### Tugas 2: Pembuatan Komponen Blade Reusable Gentelella [x] SELESAI
- **Tingkat**: 🟡 Sedang
- **Status**: ✅ Selesai (3 komponen Blade tercipta & teruji di `GentelellaDashboardRevampTest`)
- **Estimasi**: 1,5 Jam
- **Dependensi**: Tugas 1
- **File Baru yang Dibuat**:
  - `resources/views/components/gentelella/tile-stat.blade.php`
  - `resources/views/components/gentelella/x-panel.blade.php`
  - `resources/views/components/gentelella/page-title.blade.php`
- **Instruksi untuk Junior/AI**:
  1. Buat `tile-stat.blade.php`:
     - Props: `title`, `value`, `icon`, `trendText` (opsional), `trendType` (`success` / `warning` / `danger`), `subText` (opsional).
     - Markup mengikuti hirarki: `.tile_stats_count` > `span.count_top` > `div.count` > `span.count_bottom`.
     - Gunakan CSS variable MagKom: `var(--neutral-800)` untuk angka, `var(--blue-600)` untuk highlight ikon.
  2. Buat `x-panel.blade.php`:
     - Props: `title`, `badge` (opsional), `collapsible` (default true), `toolbox` (default true).
     - Markup: `.x_panel` > `.x_title` (berisi `h2`, badge, dan `ul.panel_toolbox` tombol minimize/collapse) > `.x_content`.
  3. Buat `page-title.blade.php`:
     - Props: `title`, `description`. Slot: `$actions` (opsional).
     - Markup: `.page-title` > `.title_left` (judul & deskripsi) dan `.title_right` (tombol aksi/filter).
- **Kriteria Verifikasi**:
  - Komponen dapat dipanggil tanpa error sintaks Blade (`<x-gentelella.tile-stat ... />`).

---

### Tugas 3: Transformasi Master Layout (`layouts/dashboard.blade.php` & CSS Terpadu) [x] SELESAI
- **Tingkat**: 🔴 Tinggi
- **Status**: ✅ Selesai (Layout 3 kolom Gentelella `left_col`, `top_nav`, `right_col`, `footer` + `gentelella-theme.js` terverifikasi di test suite)
- **Estimasi**: 2,5 Jam
- **Dependensi**: Tugas 2
- **File yang Dimodifikasi / Dibuat**:
  - `resources/views/layouts/dashboard.blade.php`
  - `public/js/gentelella-theme.js` (Baru)
- **Instruksi untuk Junior/AI**:
  1. Ubah struktur pembungkus `<body>` pada `layouts/dashboard.blade.php` menjadi:
     ```html
     <div class="container body">
         <div class="main_container">
             <!-- 1. LEFT COL SIDEBAR -->
             <aside class="col-md-3 left_col" id="sidebarContainer">
                 <div class="left_col scroll-view">
                     <!-- 1.1 Brand Site Title -->
                     <div class="navbar nav_title" style="border: 0;">
                         <a href="{{ route('landing') }}" class="site_title">
                             @include('partials.logo-sleman', ['width' => '32', 'height' => '32'])
                             <span>MagKom Sleman</span>
                         </a>
                     </div>
                     <div class="clearfix"></div>

                     <!-- 1.2 Profile Quick Info -->
                     <div class="profile clearfix">
                         <div class="profile_pic">
                             @if($currentUser?->foto_url)
                                 <img src="{{ $currentUser->foto_url }}" alt="{{ $currentUser->nama }}" class="img-circle profile_img">
                             @else
                                 <div class="avatar-circle-fallback">{{ strtoupper(substr($currentUser?->nama ?? 'U', 0, 1)) }}</div>
                             @endif
                         </div>
                         <div class="profile_info">
                             <span>Selamat Datang,</span>
                             <h2>{{ $currentUser?->nama ?? 'Pengguna' }}</h2>
                             <span class="profile_role_chip">{{ ucfirst($userRole) }}</span>
                         </div>
                     </div>
                     <br />

                     <!-- 1.3 Sidebar Navigation Menu -->
                     <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                         <!-- Menu item sesuai role (SuperAdmin, Admin, Pembimbing) -->
                         <!-- PERHATIAN: Semua link route() dan teks menu eksisting TETAP DIPERTAHANKAN -->
                     </div>

                     <!-- 1.4 Sidebar Footer Actions -->
                     <div class="sidebar-footer hidden-small">
                         <!-- Tombol Pengaturan, Toggle Tema, Logout -->
                     </div>
                 </div>
             </aside>

             <!-- 2. TOPBAR NAV -->
             <div class="top_nav">
                 <div class="nav_menu">
                     <nav class="nav toggle">
                         <a id="menu_toggle" role="button" aria-label="Toggle Menu Sidebar" onclick="toggleGentelellaSidebar()"><i class="icon-hamburger"></i></a>
                     </nav>
                     <div class="nav-right-user">
                         <!-- User dropdown & Notifikasi -->
                     </div>
                 </div>
             </div>

             <!-- 3. RIGHT CONTENT AREA -->
             <main class="right_col" role="main" id="mainContent">
                 <div class="alerts-container"><x-flash-alert /></div>
                 @yield('content')
             </main>

             <!-- 4. FOOTER -->
             <footer class="gentelella-footer">
                 <div class="pull-right">
                     Portal Magang &bull; Dinas Komunikasi dan Informatika Kabupaten Sleman
                 </div>
                 <div class="clearfix"></div>
             </footer>
         </div>
     </div>
     ```
  2. Implementasikan CSS Gentelella modern di bagian `<style>` layout menggunakan token warna MagKom:
     - Latar sidebar kiri: `var(--blue-700)` (`#0641CC`) dengan teks kontras tinggi `#FFFFFF`.
     - Latar konten `right_col`: `var(--neutral-150)` (`#F5F7FB`).
     - Latar kartu `x_panel`: `var(--neutral-0)` (`#FFFFFF`) dengan border halus `1px solid var(--neutral-200)`.
  3. Buat script `public/js/gentelella-theme.js` untuk fungsi toggle mini-sidebar (`nav-sm` vs `nav-md`) dan collapse panel `x_panel` tanpa jQuery.
- **Kriteria Verifikasi**:
  - Halaman sembarang (misal: `/admin/profil` atau `/superadmin/profil`) berhasil render dengan kerangka 3 kolom Gentelella warna MagKom.

---

### Tugas 4: Redesign Dashboard SuperAdmin (`/superadmin/dashboard`) [x] SELESAI
- **Tingkat**: 🟡 Sedang
- **Status**: ✅ Selesai (Integrasi `tile_count`, `tile_stats_count`, dan `x_panel` terverifikasi di test suite)
- **Estimasi**: 2 Jam
- **Dependensi**: Tugas 3
- **File**: `resources/views/superadmin/dashboard.blade.php`
- **Instruksi untuk Junior/AI**:
  1. Gunakan `<x-gentelella.page-title title="Dashboard SuperAdmin" description="Akses penuh: kelola pengguna, role, rubrik, audit log, konfigurasi" />`.
  2. Pasang deretan kartu statistik atas (`tile_count`):
     - `Total Pengguna` (Nilai: `$totalPengguna ?? 0`)
     - `Menunggu Verifikasi` (Nilai: `$menungguVerifikasi ?? 0`)
     - `Peserta Aktif` (Nilai: `$pesertaAktif ?? 0`, keterangan "Jalur Umum & Khusus")
     - `Akun Nonaktif` (Nilai: `$akunNonaktif ?? 0`)
  3. Tata konten dalam format `x_panel`:
     - Baris 1:
       - Panel Kiri (col-md-8): `x-panel` **Pengguna Terbaru** (Daftar tabel user terbaru, tombol "Kelola Pengguna").
       - Panel Kanan (col-md-4): `x-panel` **Audit Log Terbaru** (Aktivitas audit sistem terkini, tombol "Lihat Semua").
     - Baris 2:
       - Panel Penuh (col-md-12): `x-panel` **Distribusi Status & Master Data Magang** (Statistik per bulan dan per bidang).
  4. Pastikan teks yang diuji di `SuperAdminFigmaDesignAlignmentTest` dan `SuperAdminDashboardAndAuditLogTest` tetap ada di dalam view.
- **Kriteria Verifikasi**:
  - `php artisan test --filter=SuperAdminDashboard` lulus tanpa error.

---

### Tugas 5: Redesign Dashboard Admin Operasional (`/admin/dashboard`) [x] SELESAI
- **Tingkat**: 🟡 Sedang
- **Status**: ✅ Selesai (Integrasi `tile_count`, `tile_stats_count`, dan `x_panel` terverifikasi di test suite)
- **Estimasi**: 2 Jam
- **Dependensi**: Tugas 3
- **File**: `resources/views/admin/dashboard.blade.php`
- **Instruksi untuk Junior/AI**:
  1. Pasang `<x-gentelella.page-title title="Dashboard Operasional Administrasi" description="Pusat pengelolaan verifikasi berkas, kuota bidang dinas, penempatan, dan laporan magang." />`.
  2. Pasang deretan kartu statistik atas (`tile_count`):
     - `Verifikasi Tertunda` (Angka pendaftar status Menunggu Verifikasi)
     - `Kapasitas Kuota Terisi` (Persentase & jumlah kuota aktif periode ini)
     - `Waiting List Aktif` (Peserta yang ada di antrean cadangan)
     - `Peserta Ditempatkan` (Jumlah peserta aktif dibimbing)
  3. Tata konten dalam format `x_panel`:
     - Panel Kiri (col-md-8): `x-panel` **Pendaftaran Magang Memerlukan Tindakan Segera** (Tabel ringkas peserta baru dengan tombol aksi Cek Berkas).
     - Panel Kanan (col-md-4): `x-panel` **Monitoring Kuota per Unit Bidang** (List kuota bidang: IKP, Aptika, Sekretariat, dll. dengan bar progress).
     - Panel Bawah (col-md-12): `x-panel` **Alur Penempatan & Peserta Siap Ditempatkan**.
  4. Pertahankan semua link aksi cepat: Verifikasi Berkas, Manajemen Kuota, Waiting List, Penempatan Peserta.
- **Kriteria Verifikasi**:
  - `php artisan test --filter=AdminDashboard` lulus tanpa error.

---

### Tugas 6: Redesign Dashboard Pembimbing (`/pembimbing/dashboard`) [x] SELESAI
- **Tingkat**: 🟡 Sedang
- **Status**: ✅ Selesai (Integrasi `tile_count`, `tile_stats_count`, dan `x_panel` terverifikasi di test suite: 25 tests lulus)
- **Estimasi**: 2 Jam
- **Dependensi**: Tugas 3
- **File**: `resources/views/pembimbing/dashboard.blade.php`
- **Instruksi untuk Junior/AI**:
  1. Pasang `<x-gentelella.page-title title="Dashboard Pembimbing" description="Pusat komando pemantauan bimbingan magang, reviu tugas & logbook, serta evaluasi rubrik dinas." />`.
  2. Pasang deretan kartu statistik atas (`tile_count`):
     - `Beban Mentee Aktif` (Jumlah mentee aktif vs batas kapasitas dinas)
     - `Reviu Logbook` (Logbook harian peserta yang belum disetujui)
     - `Progres Tugas` (Submisi tugas peserta yang belum dievaluasi)
     - `Tugas Berjalan` (Jumlah tugas aktif dan persentase ketuntasan kumulatif)
  3. Tata konten dalam format `x_panel`:
     - Tab 1: Ringkasan Utama (Panel prioritas tenggat waktu & penugasan, aktivitas mentoring minggu ini, antrean progres & logbook, dan ringkasan mentee aktif).
     - Tab 2: Pusat Antrean Reviu (Panel seluruh antrean logbook & progres tugas).
     - Tab 3: Monitoring Mentee 360° (Panel direktori mentee 360°).
     - Tab 4: Evaluasi Rubrik (Panel matriks evaluasi rubrik & audit revisi).
     - Tab 5: Agenda & Radar Capaian (Panel radar kompetensi, early warning at-risk, dan jadwal bimbingan).
- **Kriteria Verifikasi**:
  - `php artisan test --filter="PembimbingDashboardEnhancementTest|DeltaDashboardEnhancementTest|GentelellaDashboardRevampTest"` lulus 100% tanpa error.


---

### Tugas 7: Penyelarasan Layout & Dashboard Peserta (`layouts/peserta.blade.php` & `peserta/dashboard.blade.php`) [x] SELESAI
- **Tingkat**: 🟡 Sedang
- **Status**: ✅ Selesai (Arsitektur Gentelella `container body`, `left_col`, `top_nav`, `right_col` di `layouts/peserta.blade.php`; `tile_count` & `x_panel` onboarding serta active phase di `peserta/dashboard.blade.php` teruji 100% lulus)
- **Estimasi**: 2 Jam
- **Dependensi**: Tugas 3
- **File**:
  - `resources/views/layouts/peserta.blade.php`
  - `resources/views/peserta/dashboard.blade.php`
- **Instruksi untuk Junior/AI**:
  1. Selaraskan kerangka `layouts/peserta.blade.php` agar mengadopsi arsitektur Gentelella yang sama (`container body`, `left_col`, `top_nav`, `right_col`, `footer`).
  2. Pada `peserta/dashboard.blade.php`:
     - Pasang `<x-gentelella.page-title title="Dashboard Peserta Magang" description="Pantau progres magang, penugasan bimbingan, pengisian logbook harian, dan evaluasi hasil." />`.
     - Pasang `tile_count` 4 metrik peserta:
       1. `Masa Magang Berjalan` (Contoh: Hari ke-15 dari 90 hari)
       2. `Logbook Terverifikasi` (Jumlah logbook approved)
       3. `Tugas Terselesaikan` (Jumlah tugas selesai / total)
       4. `Status Penilaian` (Nilai akhir / progres bimbingan)
     - Pasang `x_panel`:
       - Panel 1: **Status Pendaftaran & Informasi Penempatan** (Unit bidang dan pembimbing lapangan).
       - Panel 2: **Tugas Aktif & Tenggat Waktu** (Daftar tugas dengan badge deadline).
       - Panel 3: **Aksi Cepat Pengisian Logbook Harian**.
- **Kriteria Verifikasi**:
  - `php artisan test --filter=PesertaPortal` atau `Peserta` lulus tanpa error.

---

### Tugas 8: Interaktivitas UI & Responsivitas Mobile (Gentelella Interactions) [x] SELESAI
- **Tingkat**: 🟡 Sedang
- **Status**: ✅ Selesai (Sidebar toggle `nav-sm`/`nav-md` dengan dual key `localStorage`, drawer mobile `< 992px` dengan backdrop, collapse & close toolbox panel `x_panel` Vanilla JS tanpa dependensi jQuery, shortcut keyboard Escape)
- **Estimasi**: 1,5 Jam
- **Dependensi**: Tugas 4, 5, 6, 7
- **File**: `public/js/gentelella-theme.js`
- **Instruksi untuk Junior/AI**:
  1. Fitur Toggle Sidebar (`#menu_toggle`):
     - Saat diklik pada desktop: Toggle class `nav-sm` (sidebar menciut jadi 70px dengan ikon saja dan tooltip menu melayang) vs `nav-md` (sidebar lebar penuh 230px). Simpan preferensi di `localStorage.setItem('sidebar_state', 'sm' | 'md')`.
     - Saat diklik pada mobile (< 992px): Buka drawer menu dengan backdrop scrim.
  2. Fitur `x_panel` Toolbox:
     - Tombol collapse `collapse-link` (`chevron-up` / `chevron-down`): Melakukan slide up/down pada `.x_content`.
     - Tombol close `close-link`: Menghapus/menyembunyikan panel jika user menutupnya.
  3. Responsivitas:
     - Uji tampilan pada 1440px (Desktop lebar), 1024px (Tablet lanskap), 768px (Tablet potret), dan 390px (Mobile layar kecil).
     - Pada mobile, `tile_count` menjadi 2 kolom, dan `x_panel` menjadi 1 kolom penuh tanpa horizontal scroll overflow.
- **Kriteria Verifikasi**:
  - Interaksi klik tombol sidebar dan collapse panel berjalan mulus tanpa error di console browser.

---

### Tugas 9: Dukungan Mode Gelap (Dark Mode) & Standar Aksesibilitas [x] SELESAI
- **Tingkat**: 🟢 Mudah
- **Status**: ✅ Selesai (Styling `[data-theme="dark"]` harmonis pada semua panel dan tile Gentelella, rasio kontras WCAG 2.1 AA >= 4.5:1, keyboard focusable, atribut `role="region"`, `aria-label`, dan `aria-expanded`)
- **Estimasi**: 1 Jam
- **Dependensi**: Tugas 8
- **Instruksi untuk Junior/AI**:
  1. Pastikan setiap komponen Gentelella (`x_panel`, `tile_stats_count`, `sidebar-footer`, `top_nav`) memiliki styling mode gelap yang membaca atribut `[data-theme="dark"]`.
  2. Warna mode gelap:
     - Background panel `x_panel`: `#182234`
     - Border panel: `#26354D`
     - Background sidebar mode gelap: `#111C2E`
     - Teks kontras judul: `#F8FAFC` (Memenuhi standar WCAG AA rasio kontras 4.5:1).
  3. Aksesibilitas: Pastikan semua tombol memiliki `aria-label`, elemen interaktif dapat diakses via keyboard (`tabindex="0"`), dan tidak ada teks terpotong aneh.
- **Kriteria Verifikasi**:
  - Switch tema ke Gelap di pengaturan atau script console `applyPortalTheme('gelap')` merender semua panel secara elegan tanpa glitch putih.

---

### Tugas 10: Pengujian Regresi Menyeluruh & Pembersihan Kode [x] SELESAI
- **Tingkat**: 🔴 Tinggi
- **Status**: ✅ Selesai (Laravel Pint code formatting & linting, PHPUnit regression test suite lengkap termasuk 15 skenario GentelellaDashboardRevampTest 100% lulus)
- **Estimasi**: 1,5 Jam
- **Dependensi**: Semua Tugas
- **Instruksi untuk Junior/AI**:
  1. Jalankan linter dan formater kode resmi Laravel:
     `./vendor/bin/pint`
  2. Jalankan seluruh pengujian Feature Test:
     `php artisan test`
  3. Periksa secara khusus:
     - `php artisan test --filter=DashboardLayoutTest`
     - `php artisan test --filter=AdminDashboard`
     - `php artisan test --filter=SuperAdminDashboard`
     - `php artisan test --filter=PembimbingDashboard`
     - `php artisan test --filter=GentelellaDashboardRevampTest`
  4. Lakukan inspeksi visual di browser untuk memastikan tidak ada CSS clash atau elemen bertumpuk.
- **Kriteria Verifikasi**:
  - Semua test hijau (Passed), zero regression, git status rapi siap di-commit.

---

## 5. Ringkasan Jadwal & Estimasi Waktu

| No | Tugas | Tingkat | Estimasi Waktu | Status |
|---|---|---|---|---|
| 1 | Baseline Test & Persiapan Test Suite Baru | 🟢 Mudah | 30 Menit | ✅ Selesai |
| 2 | Pembuatan Komponen Blade Reusable Gentelella | 🟡 Sedang | 1,5 Jam | ✅ Selesai |
| 3 | Transformasi Master Layout (`layouts/dashboard.blade.php` & CSS) | 🔴 Tinggi | 2,5 Jam | ✅ Selesai |
| 4 | Redesign Dashboard SuperAdmin ke Format Gentelella | 🟡 Sedang | 2 Jam | ✅ Selesai |
| 5 | Redesign Dashboard Admin Operasional ke Format Gentelella | 🟡 Sedang | 2 Jam | ✅ Selesai |
| 6 | Redesign Dashboard Pembimbing ke Format Gentelella | 🟡 Sedang | 2 Jam | ✅ Selesai |
| 7 | Penyelarasan Layout & Dashboard Peserta | 🟡 Sedang | 2 Jam | ✅ Selesai |
| 8 | Interaktivitas UI & Responsivitas Mobile | 🟡 Sedang | 1,5 Jam | ✅ Selesai |
| 9 | Dukungan Mode Gelap (Dark Mode) & Aksesibilitas | 🟢 Mudah | 1 Jam | ✅ Selesai |
| 10 | Pengujian Regresi Menyeluruh & Pembersihan Kode | 🔴 Tinggi | 1,5 Jam | ✅ Selesai |
| **TOTAL** | **10 Tugas Terstruktur** | | **± 17 Jam Kerja** | **100% Selesai** |

---

## 6. Analisis Risiko & Mitigasi Teknis

1. **Risiko: Kerusakan Teks pada Test Assertion Eksisting**
   - *Dampak*: Test suite PHPUnit gagal karena teks spesifik tidak ditemukan di DOM.
   - *Mitigasi*: Dokumen ini mewajibkan seluruh judul menu dan frasa penting (seperti `Verifikasi Berkas`, `Manajemen Kuota`, dll.) dipertahankan secara utuh di dalam view baru.
2. **Risiko: CSS Clashing antara Gaya Lama dan Class Gentelella**
   - *Dampak*: Margin/padding rusak, form input meluap keluar batas panel.
   - *Mitigasi*: Gunakan *namespacing* CSS terisolasi atau integrasikan class Gentelella langsung ke dalam design token MagKom (`var(--...)`) tanpa mengimpor bootstrap.css eksternal.
3. **Risiko: Degradasi Performa akibat Penambahan Script**
   - *Dampak*: Loading halaman lambat atau layout jumping (CLS).
   - *Mitigasi*: Script interaktivitas ditulis murni Vanilla JS (tanpa jQuery) di file tunggal `public/js/gentelella-theme.js` dengan ukuran < 5KB.

---

## 7. Kriteria Selesai (Definition of Done)

Sebelum branch dapat digabungkan (*merged*), checklist berikut wajib terpenuhi 100%:
- [x] Master layout `layouts/dashboard.blade.php` dan `layouts/peserta.blade.php` mengusung arsitektur 3 kolom Gentelella (`left_col`, `top_nav`, `right_col`, `footer`).
- [x] 4 Dashboard (`superadmin`, `admin`, `pembimbing`, `peserta`) telah menggunakan komponen `tile_count` dan `x_panel`.
- [x] Warna utama konsisten dengan brand MagKom Sleman (Deep Navy `#0641CC` & Blue `#0751FF`) dan logo resmi Sleman terpajang dengan proporsional.
- [x] Tidak ada penambahan dependensi jQuery atau Bootstrap 3 legacy.
- [x] Seluruh tombol collapse panel dan toggle sidebar berjalan responsif di desktop & mobile.
- [x] Mode gelap (`[data-theme="dark"]`) terintegrasi sempurna di seluruh panel baru.
- [x] Tidak ada perubahan pada logika backend, controller, model, migrasi, dan routing.
- [x] Seluruh pengujian otomatis via `php artisan test` lulus tanpa kegagalan (100% green).
