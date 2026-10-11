# Plan Roadmap: Redesign Dashboard Eksekutif & Pengelompokan Sidebar SuperAdmin Sesuai Figma

- **Tanggal**: 2026-10-10
- **Status**: Completed (Selesai Diimplementasikan)
- **Author**: Antigravity (web-implementation)
- **Rujukan Figma**:
  - [Figma Node 560-26404 (SA-00 Dashboard SuperAdmin)](https://www.figma.com/design/uKH1OZjwBIi8iRoKQXEbQr/MAGKOM-Kominfo-Sleman?node-id=560-26404&t=lO2UnW9u7qtM1mDq-4)
  - [Figma Node 560-24892 (Komponen SuperAdmin & Sidebar Grouping)](https://www.figma.com/design/uKH1OZjwBIi8iRoKQXEbQr/MAGKOM-Kominfo-Sleman?node-id=560-24892&t=lO2UnW9u7qtM1mDq-4)
- **Modul Terkait**:
  - Layout Dashboard Utama (`resources/views/layouts/dashboard.blade.php`)
  - View Dashboard SuperAdmin (`resources/views/superadmin/dashboard.blade.php`)
  - Controller Dashboard SuperAdmin (`app/Http/Controllers/SuperAdmin/DashboardController.php`)
  - Informasi & Matriks Hak Akses Role (`routes/web.php`, `resources/views/superadmin/role/index.blade.php`)
- **Referensi Aturan & PRD**: PRD Bab 3.3, Bab 4.3, Bab 9.1 & 9.2, Design System `07-DESIGN-SYSTEM-MAGKOM.md`

---

## 1. Ringkasan Eksekutif & Tujuan

Tujuan dari rencana roadmap ini adalah merombak antarmuka **Dashboard Eksekutif SuperAdmin** dan **Sidebar Navigasi SuperAdmin** agar presisi 100% mengikuti desain resmi Figma:
1. **Penyempurnaan Navigasi Sidebar (Figma Node `560-24892`)**:
   - Menghilangkan daftar menu flat yang terlalu panjang dan melelahkan mata.
   - Mengelompokkan navigasi ke dalam dua kelompok utama berlabel tegas:
     - **`MODUL OPERASIONAL`**: `Administrasi` (expandable/shortcut ke alur Verifikasi Berkas, Kuota, Penempatan, Laporan), `Pembimbing`, dan `Peserta`.
     - **`SISTEM`**: `Pengguna` (Kelola Akun), `Role` (Matriks Hak Akses), `Rubrik` (Rubrik Penilaian), `Audit Log` (Rekam Jejak Trail), dan `Konfigurasi` (Bulan Magang, Slot Khusus, Jurusan, Bidang, Parameter Sistem).
   - Menambahkan footer status sistem interaktif (*Live Indicator*: titik hijau berkedip + label *"Sistem internal Kominfo"*).
2. **Desain Ulang Dashboard Eksekutif (Figma Node `560-26404` - Frame `SA-00 Dashboard SuperAdmin`)**:
   - Header sapaan ramah dengan ringkasan hak akses: *"Akses penuh: kelola pengguna, role, rubrik, audit log, konfigurasi, dan seluruh modul Administrasi, Pembimbing, serta Peserta."*
   - 4 Kartu Statistik Ringkasan (*Total Pengguna*, *Menunggu Verifikasi*, *Peserta Aktif*, *Akun Nonaktif*) dengan icon dan aksen visual khas Figma.
   - Layout 2 kolom bawah yang seimbang dan elegan:
     - **Kolom Kiri**: *Pengguna Terbaru* lengkap dengan avatar inisial, nama, email, dan chip badge role warna-warni (`Administrasi`, `Pembimbing`, `Peserta`), dilengkapi tombol aksi cepat *"Kelola pengguna"*.
     - **Kolom Kanan**: *Audit Log Terbaru* ringkas dengan ikon status, deskripsi mutasi, aktor staf, dan jam kejadian, dilengkapi tombol *"Lihat semua"*.
   - Tetap mengintegrasikan panel analitik strategis PRD (Utilisasi Kuota & Slot Khusus, Beban Pembimbing) secara rapi sebagai bagian insight eksekutif pelengkap tanpa merusak hierarki visual Figma.

---

## 2. Analisis Kesenjangan UI/UX (Kondisi Saat Ini vs Figma)

| Aspek UI/UX | Kondisi Saat Ini di Sistem | Standar Mockup Figma (Node 560-26404 & 560-24892) | Solusi yang Direncanakan |
| :--- | :--- | :--- | :--- |
| **Sidebar Menu SuperAdmin** | Daftar flat memanjang ke bawah (9+ item bertumpuk) dengan section label acak ("Tata Kelola Eksekutif", "Konfigurasi & Penempatan", "Master & Keamanan", "Laporan"). | Dikelompokkan rapi ke dalam 2 kluster logis: **`MODUL OPERASIONAL`** dan **`SISTEM`**, dilengkapi icon Lucide konsisten dan indikator status chevron. | Refactor struktur sidebar SuperAdmin pada `layouts/dashboard.blade.php` dengan accordion/dropdown interaktif untuk modul operasional. |
| **Identitas Brand Sidebar** | Logo generic dan teks biasa. | Logo Mark MagKom + Judul `MagKom` (ExtraBold) + Subtitle `KOMINFO PORTAL` (SemiBold 10px tracking). | Sesuaikan tipografi dan branding sidebar header sesuai token Figma. |
| **Footer Sidebar** | Profil mini tanpa status sistem. | Status baris bawah: Titik hijau pulsing + teks *"Sistem internal Kominfo"*. | Tambahkan status pill sistem internal Kominfo di bagian bawah sidebar. |
| **Hero Sapaan Dashboard** | Judul formal kaku dan subteks singkat. | Sapaan ramah dengan deskripsi kapabilitas eksekutif yang jelas dan menonjol. | Perbarui hero section `SA-00 Dashboard SuperAdmin`. |
| **4 Stat Cards Utama** | Peserta Aktif, Total Alumni, Staf Internal, Slot Khusus. | 1. Total pengguna<br>2. Menunggu verifikasi<br>3. Peserta aktif<br>4. Akun nonaktif | Ubah 4 kartu utama ke indikator Figma, dan pindahkan utilisasi slot/kuota ke panel insight operasional. |
| **Section Kolom Bawah** | Tabel audit penuh dan demografi institusi memanjang. | Split 2 kolom seimbang: Kolom Kiri *Pengguna Terbaru* (avatar + role chips) dan Kolom Kanan *Audit Log Terbaru* (timeline style). | Susun ulang grid konten bawah menjadi 2-kolom presisi 50:50 sesuai Figma. |
| **Menu Role** | Belum ada halaman/menu khusus Role SuperAdmin. | Menu `Role` ada di sidebar di bawah kelompok `SISTEM` (frame `SA-02 Role`). | Sediakan view matriks hak akses 4 role (`superadmin.role.index`) yang informatif dan transparan per PRD Bab 3.1 & 3.3. |

---

## 3. Pendekatan Desain & Arsitektur

### 3.1. Struktur Pengelompokan Sidebar SuperAdmin (`Sidebar/SuperAdmin`)

```text
[ BRAND HEADER ]
└── Logo Mark Sleman + MagKom (KOMINFO PORTAL)

[ STANDALONE MENU ]
└── Dashboard (layout-dashboard)

[ GRUP 1: MODUL OPERASIONAL ] (Uppercase Section Label)
├── Administrasi (building-2, chevron-right)
│   └── Dropdown/Modal/Pintasan: Verifikasi Berkas, Kuota, Penempatan, Laporan
├── Pembimbing (clipboard-list, chevron-right)
│   └── Pintasan: Penilaian & Monitoring Mentee
└── Peserta (book-open, chevron-right)
    └── Pintasan: Monitoring Pelaksanaan Magang & Arsip

[ GRUP 2: SISTEM ] (Uppercase Section Label)
├── Pengguna (users) -> /superadmin/staf
├── Role (shield-check) -> /superadmin/role (Matriks Hak Akses)
├── Rubrik (list-checks) -> /superadmin/rubrik
├── Audit Log (scroll-text) -> /superadmin/audit-log
└── Konfigurasi (settings, chevron-right)
    └── Dropdown/Sub-navigasi: Pengaturan Umum, Bulan Magang, Slot Khusus, Jurusan, Bidang

[ SIDEBAR FOOTER ]
└── Status: (● Hijau Pulse) Sistem internal Kominfo
```

### 3.2. Struktur Tampilan Dashboard Eksekutif (`SA-00 Dashboard SuperAdmin`)

```text
┌────────────────────────────────────────────────────────────────────────┐
│ TOPBAR: Breadcrumb (SuperAdmin / Dashboard) + User Profile Pill        │
├────────────────────────────────────────────────────────────────────────┤
│ HERO SAPAAN:                                                           │
│ "Dashboard SuperAdmin"                                                 │
│ "Akses penuh: kelola pengguna, role, rubrik, audit log, konfigurasi..."│
├────────────────────────────────────────────────────────────────────────┤
│ 4 STAT CARDS GRID:                                                     │
│ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌────────────────┐  │
│ │Total Pengguna│ │Menunggu Verif│ │Peserta Aktif │ │ Akun Nonaktif  │  │
│ │   [users]    │ │   [inbox]    │ │ [clipboard]  │ │    [lock]      │  │
│ └──────────────┘ └──────────────┘ └──────────────┘ └────────────────┘  │
├────────────────────────────────────────────────────────────────────────┤
│ DUA KOLOM UTAMA (50% : 50%):                                           │
│ ┌────────────────────────────────┐ ┌─────────────────────────────────┐ │
│ │ PENGGUNA TERBARU               │ │ AUDIT LOG TERBARU               │ │
│ │ [Kelola pengguna ->]           │ │ [Lihat semua ->]                │ │
│ │ • Admin Kominfo [Administrasi] │ │ • Penilaian difinalisasi (10:42)│ │
│ │ • Bpk Pembimbing [Pembimbing]  │ │ • Pendaftaran diterima (10:15)  │ │
│ │ • Aldi Artha [Peserta]         │ │ • Role diubah (09:30)           │ │
│ │ • Rina Wulandari [Peserta]     │ │ • Kuota diperbarui (09:05)      │ │
│ └────────────────────────────────┘ └─────────────────────────────────┘ │
├────────────────────────────────────────────────────────────────────────┤
│ INSIGHT OPERASIONAL & KEPATUHAN PRD (COLLAPSIBLE/ACCORDION):           │
│ • Utilisasi Kuota Umum vs Slot Khusus Kemitraan (PRD 4.3)              │
│ • Beban Pembimbing Lapangan (BR-13) & Demografi Institusi              │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php) | Modifikasi | Restrukturisasi menu navigasi sidebar SuperAdmin menjadi kelompok `MODUL OPERASIONAL` dan `SISTEM`, penambahan animasi pulse status sistem, sub-menu collapsible modul operasional & konfigurasi, serta penyesuaian icon Lucide. |
| [`app/Http/Controllers/SuperAdmin/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/DashboardController.php) | Modifikasi | Query data metrik Figma: `totalPengguna`, `menungguVerifikasi`, `pesertaAktif`, `akunNonaktif`, `penggunaTerbaru` (dengan relasi role dan avatar inisial), dan `auditTerbaru` format ringkas. |
| [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php) | Modifikasi | Redesign layout Blade 100% presisi sesuai Figma Node `560-26404`: hero sapaan, 4 stat cards Figma, 2-kolom (Pengguna Terbaru & Audit Log Terbaru), serta accordion insight operasional. |
| [`app/Http/Controllers/SuperAdmin/RoleController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/RoleController.php) | Pembuatan | Controller untuk menampilkan halaman matriks hak akses 4 role sistem (SuperAdmin, Admin, Pembimbing, Peserta) sesuai frame Figma `SA-02 Role`. |
| [`resources/views/superadmin/role/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/role/index.blade.php) | Pembuatan | Tampilan matriks role dan kapabilitas per PRD 3.1 & frame Figma `SA-02 Role`. |
| [`routes/web.php`](file:///g:/MAGKOM/routes/web.php) | Modifikasi | Daftarkan rute `Route::get('/role', [RoleController::class, 'index'])->name('role.index');` di grup SuperAdmin. |
| [`tests/Feature/SuperAdminFigmaDesignAlignmentTest.php`](file:///g:/MAGKOM/tests/Feature/SuperAdminFigmaDesignAlignmentTest.php) | Pembuatan | Test suite untuk memvalidasi rendering sidebar baru, dashboard eksekutif baru, data pengguna terbaru, dan halaman role. |

---

## 5. Rencana Langkah Implementasi (Roadmap)

### Fase 1: Restrukturisasi Sidebar Navigasi SuperAdmin (Node 560-24892)
- [x] **Langkah 1.1**: Perbarui section sidebar SuperAdmin di [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php):
  - Ubah header brand sidebar: Icon mark + `MagKom` (ExtraBold) + `KOMINFO PORTAL` (10px uppercase tracking).
  - Buat grup menu mandiri `Dashboard`.
  - Buat grup label **`MODUL OPERASIONAL`**:
    - Item menu `Administrasi` dengan submenu collapsible/drawer (Pendaftaran, Kuota, Penempatan, Laporan).
    - Item menu `Pembimbing` (Pintasan Modul Tugas & Nilai).
    - Item menu `Peserta` (Monitoring Pelaksanaan & Arsip).
  - Buat grup label **`SISTEM`**:
    - Item menu `Pengguna` (`route('superadmin.staf.index')`).
    - Item menu `Role` (`route('superadmin.role.index')`).
    - Item menu `Rubrik` (`route('superadmin.rubrik.index')`).
    - Item menu `Audit Log` (`route('superadmin.audit-log.index')`).
    - Item menu `Konfigurasi` dengan submenu (Pengaturan Sistem, Bulan Magang, Slot Khusus, Jurusan, Bidang).
  - Tambahkan footer sidebar: Indikator titik hijau berkedip (pulsing dot CSS) + teks *"Sistem internal Kominfo"*.
  - Tambahkan interaktivitas JavaScript untuk collapse/expand menu grup tanpa ketergantungan library luar.

### Fase 2: Backend Controller & Data Query Dashboard Eksekutif
- [x] **Langkah 2.1**: Perbarui [`app/Http/Controllers/SuperAdmin/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/DashboardController.php):
  - Siapkan metrik Figma: `totalPengguna`, `menungguVerifikasi`, `pesertaAktif`, `akunNonaktif`.
  - Siapkan daftar pengguna terbaru (5 akun terakhir) beserta badge role dan waktu pendaftaran.
  - Siapkan audit log terbaru (5 mutasi terakhir) dengan relasi aktor.
  - Tetap pass data operasional PRD (`slotKhusus`, `pembimbingList`, `maksBeban`, `topInstitusi`, `jenjangRaw`) untuk widget analitik lanjutan.

### Fase 3: Redesign Tampilan Blade Dashboard SuperAdmin (Node 560-26404)
- [x] **Langkah 3.1**: Tulis ulang [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php):
  - **Header Hero**: Judul `Dashboard SuperAdmin` & Subtitle hak akses eksekutif penuh.
  - **4 Stat Cards Figma**: `Total pengguna`, `Menunggu verifikasi`, `Peserta aktif`, `Akun nonaktif`.
  - **Grid Dua Kolom (Kolom Kiri & Kanan)**:
    - **Kolom Kiri - Pengguna terbaru**: Avatar inisial warna pastel, nama, email, chip badge role bertema, link `Kelola pengguna ->`.
    - **Kolom Kanan - Audit log terbaru**: Timeline peristiwa ringkas, nama pelaku, jam/tanggal mutasi, link `Lihat semua ->`.
  - **Panel Insight Operasional Terintegrasi**: Accordion insight utilisasi kuota vs slot khusus kemitraan, beban pembimbing (BR-13), dan demografi institusi.

### Fase 4: Halaman Matriks Hak Akses & Role (Frame SA-02 Role)
- [x] **Langkah 4.1**: Buat controller [`app/Http/Controllers/SuperAdmin/RoleController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/RoleController.php): Mengembalikan daftar role resmi sistem (`SuperAdmin`, `Admin`, `Pembimbing`, `Peserta`), jumlah pengguna per role, dan kapabilitas matriks hak akses.
- [x] **Langkah 4.2**: Daftarkan rute di [`routes/web.php`](file:///g:/MAGKOM/routes/web.php): `Route::get('/role', [RoleController::class, 'index'])->name('role.index');`.
- [x] **Langkah 4.3**: Buat view [`resources/views/superadmin/role/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/role/index.blade.php) yang menampilkan tabel matriks wewenang per PRD Bab 3.1 & frame Figma `SA-02 Role`.

### Fase 5: Automated Testing & Verifikasi Kualitas
- [x] **Langkah 5.1**: Buat test suite [`tests/Feature/SuperAdminFigmaDesignAlignmentTest.php`](file:///g:/MAGKOM/tests/Feature/SuperAdminFigmaDesignAlignmentTest.php):
  - Uji akses dashboard SuperAdmin: verifikasi teks sapaan Figma, 4 kartu metrik baru, widget pengguna terbaru, dan feed audit terbaru.
  - Uji halaman `/superadmin/role`: verifikasi rendering matriks 4 role dan proteksi hak akses role superadmin.
  - Uji rendering link sidebar grup operasional dan sistem.
- [x] **Langkah 5.2**: Jalankan pengujian penuh via `php artisan test` untuk memastikan 100% PASS tanpa regresi.

---

## 6. Kriteria Selesai (Definition of Done)

1. Sidebar SuperAdmin memiliki pengelompokan yang jelas (**`MODUL OPERASIONAL`** dan **`SISTEM`**) serta status sistem internal Kominfo aktif di bagian bawah.
2. Dashboard Eksekutif SuperAdmin memiliki layout dan struktur yang identik dengan mockup Figma Node `560-26404` (Hero sapaan, 4 stat cards, 2-kolom pengguna terbaru dan audit log).
3. Modul Role baru (`/superadmin/role`) dapat diakses oleh SuperAdmin dan memuat matriks hak akses secara transparan.
4. Seluruh fitur tetap responsif pada layar desktop (1440px) dan mobile (<1024px).
5. Seluruh automated test suite (test baru dan 28 test regresi sebelumnya) berstatus 100% green.
