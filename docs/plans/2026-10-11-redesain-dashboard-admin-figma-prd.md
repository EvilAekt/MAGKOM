# Plan: Redesain Dashboard Admin (Command Center Operasional Administrasi MagKom ala Figma & PRD)

- **Tanggal**: 2026-10-11
- **Status**: Selesai (Completed & Verified)
- **Author**: Antigravity (Senior Frontend & System Architect / web-planning)
- **Target Pembaca / Pelaksana**: Senior Developer, Junior Developer, Autonomous Coding AI
- **Rujukan Bisnis & Desain**:
  - PRD: [`docs/01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (Bab 2.3, 3.1, 4.1 F-04/F-05/F-07, 5 Aturan Bisnis, 8.2, 9.2)
  - Roadmap: [`docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) (Gelombang 1, Gelombang 2, Rilis 2)
  - Design System: [`docs/07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) & Figma Sleman Blue `#0641CC`
- **Target File**:
  - Controller: [`app/Http/Controllers/Admin/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/Admin/DashboardController.php)
  - Blade View: [`resources/views/admin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/admin/dashboard.blade.php)
  - Test Suite: [`tests/Feature/AdminDashboardEnhancementTest.php`](file:///g:/MAGKOM/tests/Feature/AdminDashboardEnhancementTest.php)
  - Regression Test: [`tests/Feature/AdminSuperAdminPhaseTwoTest.php`](file:///g:/MAGKOM/tests/Feature/AdminSuperAdminPhaseTwoTest.php)

---

## 1. Analisis Kebutuhan Bisnis & Latar Belakang (Executive Summary)

### Masalah Pengguna (User Problem):
Dashboard Admin (`/admin/dashboard`) saat ini masih mengusung layout dasar sederhana (hanya 4 kotak metrik statis, 1 bar kuota sederhana, dan tabel 5 baris). Padahal peran **Administrasi Kominfo** adalah pintu gerbang operasional utama magang yang membutuhkan pengawasan terpadu (*command center*) terhadap:
1. **Target SLA Verifikasi (≤ 5 hari kerja)**: Admin belum memiliki indikator peringatan pendaftaran yang sudah mendekati tenggat SLA peninjauan (PRD Bab 2.3 & 12.3).
2. **Keterisian Kuota Multidimensi**: Admin perlu melihat visualisasi kuota per jurusan (SMK vs Mahasiswa) secara jelas dengan indikator sisa kuota dan peringatan kuota kritis/penuh.
3. **Pengawasan Beban Pembimbing (BR-13)**: Sebelum menempatkan peserta, Admin perlu mengetahui kapasitas dan beban aktif para pembimbing lapangan agar tidak overload.
4. **Alur Antrean Cepat (Waiting List & Promosi)**: Admin membutuhkan akses instan ke antrean pendaftar prioritas tanpa harus berpindah-pindah menu.

### Solusi yang Diusulkan:
Melakukan perombakan visual dan fungsional total pada Dashboard Admin menjadi **Command Center Operasional Administrasi** dengan:
- **Hero Operational Bar**: Ringkasan periode aktif, status buka/tutup pendaftaran, hitung mundur hari, dan filter dinamis periode bulan magang.
- **Top 4 KPI Metrics Cards Interaktif**: Kartu status-coded dengan indikator tren dan peringatan SLA kritis.
- **Tabs Command Center (4 Tab Kerja)**:
  - **Tab 1: Action Hub & Antrean Mendesak**: Tabel prioritas pendaftaran dengan badge SLA (Aman, Perhatian >3 hari, Kritis >5 hari).
  - **Tab 2: Utilisasi Kuota & Kapasitas Jurusan**: Kartu progress bar visual per jurusan dengan rincian jenjang pendidikan.
  - **Tab 3: Monitoring Beban Pembimbing Lapangan (BR-13)**: Pemetaan beban kerja pembimbing aktif untuk memudahkan distribusi penempatan.
  - **Tab 4: Arsip Penempatan & Ekspor Cepat**: Daftar penerimaan terkini, akses instan Surat Keterangan Diterima PDF (F-16), dan shortcut ekspor laporan (F-17).

---

## 2. Spesifikasi Desain & Visual (Figma & DS-MagKom)

Sesuai token resmi [07-DESIGN-SYSTEM-MAGKOM.md](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md):
- **Warna Pokok**: Sleman Blue (`--blue-700: #0641CC`, `--blue-600: #0751FF`, `--blue-tint: #E6EEFF`).
- **Latar Belakang**: `--neutral-150: #F5F7FB`.
- **Kartu & Kontainer**: Putih murni (`#FFFFFF`), sudut melengkung `border-radius: 14px`, border `1px solid var(--neutral-400)` / `#E2E8F0`, shadow halus.
- **Tipografi**: `Open Sans` (semibold 600, bold 700, extrabold 800).
- **Semua Ikon SVG**: Wajib menyertakan atribut eksplisit `width="X"` dan `height="X"` anti-exploding bug.

---

## 3. Rencana Perubahan Data Backend (`Admin\DashboardController.php`)

Controller akan diperkaya untuk mendukung visual command center tanpa membebani performa query:
1. **Dukungan Filter Periode Bulan (`?bulan_id=...`)**:
   - Jika ada parameter `bulan_id`, muat periode tersebut. Jika tidak, ambil bulan aktif terdekat.
   - Sediakan daftar seluruh bulan magang untuk dropdown filter periode.
2. **Kalkulasi Metrik & SLA Verifikasi**:
   - Hitung pendaftaran berstatus `Diajukan` yang dibuat lebih dari 3 hari kerja yang lalu sebagai `$pengajuanSlaWarning`.
   - Ringkasan total kuota umum dan total keterisian periode aktif.
3. **Data Beban Kerja Pembimbing (BR-13)**:
   - Ambil daftar user ber-role `Pembimbing` dengan relasi `bidang` dan `penempatans` aktif (`withCount`).
4. **Data Arsip Penempatan Terkini**:
   - Ambil 5 penempatan terbaru yang berstatus `Aktif` lengkap dengan relasi peserta, institusi, dan pembimbing.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`app/Http/Controllers/Admin/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/Admin/DashboardController.php) | **Modify** | Tambahkan filter `bulan_id`, hitung SLA warning pendaftaran, query beban pembimbing lapangan, dan data penempatan terbaru. |
| [`resources/views/admin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/admin/dashboard.blade.php) | **Modify** | Terapkan Hero Operational Widget, 4 KPI cards dengan SLA alert, dan 4-Tab Command Center (Action Hub, Kuota, Beban Pembimbing, Arsip Penempatan). |
| [`tests/Feature/AdminDashboardEnhancementTest.php`](file:///g:/MAGKOM/tests/Feature/AdminDashboardEnhancementTest.php) | **Create** | Test feature komprehensif menguji akses dashboard admin, metrik SLA, kuota per jurusan, monitoring beban pembimbing, dan proteksi RBAC. |
| [`tests/Feature/AdminSuperAdminPhaseTwoTest.php`](file:///g:/MAGKOM/tests/Feature/AdminSuperAdminPhaseTwoTest.php) | **Verify** | Pastikan test suite regresi eksisting tetap lulus 100%. |

---

## 5. Rincian Antarmuka Pengguna (UI Breakdown)

### A. Topbar, Breadcrumb & Hero Operational Widget
```html
Dashboard / Operasional Administrasi
[Judul: Command Center Administrasi MagKom]  [Dropdown Filter: Periode Oktober 2026 ▾]
```
- **Banner Status Operasional**:
  - Badge Status Landing Page (`TERSEDIA` / `SEGERA` / `TUTUP`).
  - Info Rentang Tanggal Pendaftaran (contoh: *25 Sep - 20 Okt 2026*).
  - Indikator Countdown: *"Sisa 9 Hari Sebelum Pendaftaran Ditutup"*.
  - Mini Progress Kapasitas: *Terisi 8 dari 12 Kuota Umum (66%)* + *2 Slot Khusus Terisi*.

### B. 4 Kartu KPI Metrik Interaktif
1. **Antrean Perlu Verifikasi** (Border Amber `#F59E0B`):
   - Angka besar: misal `7`
   - Sub-badge: `⚠️ 2 Pengajuan Mendekati Batas SLA (>3 Hari)`
2. **Menunggu Perbaikan Berkas** (Border Biru `#0751FF`):
   - Angka besar: misal `3`
   - Subtext: *Menunggu revisi dokumen dari calon peserta*
3. **Antrean Waiting List** (Border Ungu `#7C3AED`):
   - Angka besar: misal `5`
   - Subtext: *Prioritas antrean FIFO (BR-05)*
4. **Peserta Aktif Dibimbing** (Border Hijau `#10B981`):
   - Angka besar: misal `14`
   - Subtext: *Sedang menjalani penempatan magang aktif*

### C. 4-Tab Command Center Workspace
- **Tab 1: Action Hub & Antrean Mendesak**:
  - Menampilkan tabel pendaftaran yang membutuhkan aksi segera dengan badge usia pengajuan:
    - Hijau: `< 2 hari` (Baru Diajukan)
    - Kuning: `2 - 4 hari` (Perhatian SLA)
    - Merah: `≥ 5 hari` (Mendesak / Lewat Target SLA PRD 2.3)
  - Tombol aksi: `[Tinjau Berkas &rarr;]` langsung membuka `/admin/pendaftaran/{id}`.
- **Tab 2: Utilisasi Kuota & Kapasitas Jurusan**:
  - Grid kartu kuota untuk 6 jurusan standar (TKJ, RPL, Sistem Informasi, Teknik Informatika, dll.).
  - Persentase bar linear, angka kuota terisi vs kapasitas, dan status `Tersedia (Sisa N)` atau `Penuh`.
  - Tombol aksi: `[Kelola Alokasi Kuota &rarr;]`.
- **Tab 3: Monitoring Beban Pembimbing (Workload BR-13)**:
  - Menampilkan kartu para pembimbing lapangan Kominfo:
    - Nama pembimbing & Bidang penempatan (misal: *Aplikasi Informatika*).
    - Bar beban mentee: misal `4 Mentee Aktif`.
    - Status ketersediaan: *Siap Menerima Mentee* (jika di bawah batas beban).
  - Tombol aksi: `[Lihat Penempatan &rarr;]`.
- **Tab 4: Arsip Penempatan & Rekap Cepat**:
  - Daftar 5 peserta yang baru diterima dan ditempatkan.
  - Akses unduh instan Surat Keterangan Diterima PDF (`F-16`).
  - Tombol shortcut ke menu ekspor data CSV/Excel (`F-17`).

---

## 6. Langkah Implementasi Bertahap

- [x] **Langkah 1: Pengayaan Backend Controller**:
  - Perbarui [`app/Http/Controllers/Admin/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/Admin/DashboardController.php) untuk menghitung metrik SLA, filter `bulan_id`, query kuota komprehensif, dan beban kerja pembimbing aktif.
- [x] **Langkah 2: Pembuatan Test Suite Baru**:
  - Buat [`tests/Feature/AdminDashboardEnhancementTest.php`](file:///g:/MAGKOM/tests/Feature/AdminDashboardEnhancementTest.php) dengan assertions untuk seluruh metrik, tab, dan filter.
- [x] **Langkah 3: Perombakan Blade View Dashboard Admin**:
  - Tulis ulang [`resources/views/admin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/admin/dashboard.blade.php) dengan layout Command Center 4-Tab, Hero widget, KPI cards, dan token styling Sleman Blue `#0641CC`.
- [x] **Langkah 4: Eksekusi Test Otomatis**:
  - Jalankan `php artisan test --filter="AdminDashboardEnhancementTest"` (3 passed, 15 assertions)
  - Jalankan `php artisan test --filter="AdminSuperAdminPhaseTwoTest"` (9 passed, 36 assertions)
- [x] **Langkah 5: Verifikasi Visual Browser Subagent**:
  - Navigasi ke `http://127.0.0.1:8000/admin/dashboard`.
  - Ambil screenshot dan verifikasi tata letak, keindahan visual, dan responsivitas tab switching.

---

## 7. Kriteria Selesai (Definition of Done)

1. [x] Dashboard Admin menggunakan styling resmi Design System Sleman Blue `#0641CC` yang mewah, rapi, dan konsisten dengan dashboard Pembimbing dan SuperAdmin.
2. [x] Seluruh tag `<svg>` berukuran proporsional dengan atribut `width` dan `height` eksplisit.
3. [x] Tersedia Hero Operational Widget dengan ringkasan periode aktif, status buka/tutup, dan filter periode dinamis.
4. [x] Terdapat 4 kartu KPI metrik dengan penanda SLA warning.
5. [x] Tersedia 4 tab command center interaktif (Action Hub, Kuota Jurusan, Beban Pembimbing, Arsip Penempatan).
6. [x] String uji dari test suite eksisting tetap dipertahankan (*Zero Regression*).
7. [x] Pengujian otomatis lulus 100% (**12 passed, 51 assertions**).
8. [x] Tangkapan layar browser membuktikan antarmuka proporsional, interaktif, dan nyaman digunakan.
