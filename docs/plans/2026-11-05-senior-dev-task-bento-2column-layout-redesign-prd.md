# PRD & Implementation Plan: Restrukturisasi Layout Bento Grid 2 Kolom (Kiri & Kanan) Dashboard Pembimbing MAGKOM

> **Status Dokumen**: Proposal Rencana Kerja (Menunggu Persetujuan Pengguna Sebelum Eksekusi)  
> **Tanggal**: 5 November 2026  
> **Target Surface**: Dashboard Pembimbing — Tab 1: Ringkasan Utama (`route('pembimbing.dashboard')`)  
> **Dasar Kebutuhan**: Evaluasi screenshot layar desktop pengguna (`media_1793839994597_867dcba9.png`) di mana komponen memanjang 100% ke bawah satu per satu sehingga chart meregang canggung secara horizontal dengan ruang kosong berlebih.  
> **Referensi Arsitektur**: Bento Box Grid, Zenius 2-Column Dashboard, EduCare Study Progress  
> **Standar Kualitas**: UI-UX Pro Max, Impeccable (Mode: Operate), Better-Layout, Better-UI (WCAG 2.2 AA)  
> **Aturan Repository**: `AGENTS.md` (No CSS/JS in Blade, Sleman Civic Tech Palette, 100% PHPUnit Test Green)

---

## 1. Analisis Masalah Tampilan Saat Ini (Evidence from User Screenshot)

Berdasarkan tangkapan layar browser pengguna pada monitor desktop lebar (`media_1793839994597_867dcba9.png`):

1. **Susunan 1 Kolom Vertikal Monolitik (`col-12` Memanjang ke Bawah)**:
   - Setiap kartu membentang dari ujung kiri hingga ujung kanan layar (lebar 100% ~1400-1920px).
   - Pengguna harus terus-menerus melakukan scroll jauh ke bawah untuk melihat komponen di urutan 4, 5, 6, dan 7.
2. **Distorsi Visual & Ruang Kosong (Vast Dead Space)**:
   - **Komponen 1 (Logbook Alert)**: Arch Gauge berada di pojok kiri kecil, sedangkan 3 tile KPI terpisah sangat jauh dengan ruang kosong ratusan piksel di tengahnya.
   - **Komponen 2 (Tenggat Waktu Tugas)**: Bar horison membentang menjadi garis tipis yang terlalu panjang (~1500px), mengurangi makna visual "target pin".
   - **Komponen 3 (Aktivitas 7 Hari)**: 7 kapsul hari (Sen, Sel, Rab, Kam, Jum, Sab, Min) terpisah jarak kosong lebih dari 150px per hari, merusak persepsi grafik batang ritmis ala Zenius.
   - **Komponen 4 (Offboarding 14 Hari)**: Lintasan stasiun terentang berlebihan secara horizontal.
3. **Kebutuhan Pengguna**:
   - "*LAYOUTNYA JANGAN KEBAWAH SEMUA DONG DI SESUAIKN JADI PER CARD DI KECILIN KANAN KIRI KEK ATAU GMN YG LANGSUNG KE BAWAH*"
   - Menata ulang layout menjadi **Bento Grid 2 Kolom Seimbang (Kiri & Kanan)** yang compact, proporsional, dan padat informasi tanpa scroll berlebih.

---

## 2. Solusi Arsitektur: Balanced 2-Column Bento Dashboard Grid

Membagi ke-7 komponen ke dalam grid 2 kolom (`col-lg-6 col-md-6 col-sm-12`) dengan pengelompokan fungsi yang harmonis dan perataan tinggi kartu (`h-100` flex layout):

```
+---------------------------------------------------------------------------------------------------+
| TAB 1: RINGKASAN UTAMA (BENTO GRID 2 KOLOM)                                                       |
+-------------------------------------------------+-------------------------------------------------+
| BARIS 1: RADAR KRITIS & PERINGATAN (KIRI)       | BARIS 1: RADAR KRITIS & KELULUSAN (KANAN)       |
| Komponen 1: Peringatan Logbook (Arch Gauge 180°)| Komponen 4: Mentee Selesai Magang (Runway Track)|
| [Arch 23%]  [3 Disiplin | 0 Pasif | 10 Kritis]  | [≤3 Hari: 2 Kritis | 4-7 Hari | 8-14 Hari]      |
| [Tombol Buka Tab Mentee 360°]                   | [Tombol Lembar Nilai Rubrik]                    |
+-------------------------------------------------+-------------------------------------------------+
| BARIS 2: ANALITIK OPERASIONAL (KIRI)            | BARIS 2: HORISON TENGGAT WAKTU TUGAS (KANAN)    |
| Komponen 3: Aktivitas Mentoring Minggu Ini      | Komponen 2: Tenggat Waktu & Distribusi Tugas    |
| - 4 KPI Mini Titik Warna (Logbook, Progres...)  | - Filter: Semua | Mendesak | Perlu Review       |
| - 7 Pilar Kapsul Vertikal Rapat (Zenius 34px)   | - Horizon Bar 14px + Floating Target Pin 25%    |
| - Stacked Volume Bar 8px                        | - Status Ketuntasan 25% tuntas                  |
+-------------------------------------------------+-------------------------------------------------+
| BARIS 3: ACTION HUB - ANTREAN EVALUASI (KIRI)   | BARIS 3: ACTION HUB - ANTREAN VALIDASI (KANAN)  |
| Komponen 5: Antrean Progres Tugas               | Komponen 6: Antrean Review Logbook              |
| [Radial Ring Biru 88px | 3 Antrean]             | [Radial Ring Amber 88px | 4 Logbook]            |
| - Badge: Perlu Evaluasi | Rasio 20%             | - Badge: Perlu Validasi | Rasio 31%             |
| [Evaluasi di Tab Antrean ->]                    | [Validasi di Tab Antrean ->]                    |
+-------------------------------------------------+-------------------------------------------------+
| BARIS 4: EXECUTIVE COHORT FOUNDATION (FULL WIDTH - COL-12)                                        |
| Komponen 7: Daftar Mentee Bimbingan Aktif (Ringkasan)                                             |
| [Kolom Kiri: Durasi Magang Awal/Tengah/Akhir]   | [Kolom Kanan: Status Nilai Final/Draf/Belum]    |
| [Footer Action: Buka Tab Monitoring Mentee 360° ->]                                              |
+---------------------------------------------------------------------------------------------------+
```

---

## 3. Evaluasi Opsi Arsitektur (Web-Planning Requirement)

### Opsi A: Strict Asymmetric 7/5 Grid (Kolom Kiri Lebar 58%, Kolom Kanan Sempit 42%)
* **Kelebihan**: Memberi ruang ekstra untuk grafik besar.
* **Kekurangan**:
  - Kolom kiri menjadi sangat panjang ke bawah (menumpuk 3 kartu besar), sedangkan kolom kanan menumpuk 4 kartu kecil sehingga tinggi halaman tidak seimbang.
  - Sumbu horizontal antar kartu menjadi tidak sejajar.
* **Verdict**: ❌ Ditolak.

### Opsi B (Rekomendasi Terbaik): Balanced 2-Column Dashboard Grid (50% / 50% Pairs)
* **Kelebihan**:
  1. **Simetri & Keselarasan Tinggi**: Setiap baris memiliki 2 kartu dengan tinggi yang sama persis (`d-flex flex-column h-100`), menciptakan ritme visual kelas dunia ala Zenius & Apple Bento.
  2. **Eliminasi Ruang Kosong**: Dalam lebar 50% (~550-650px), 7 pilar kapsul Zenius berada rapat di tengah (~330px), bar horison tugas memiliki panjang ideal (~500px), dan arch gauge logbook terintegrasi harmonis dengan 3 tile KPI-nya.
  3. **Peningkatan Efisiensi Scroll**: Tinggi total halaman berkurang hingga ~50%, memungkinkan pembimbing melihat seluruh metrik krusial tanpa perlu banyak scrolling.
  4. **Responsif Sempurna**: Pada tablet/mobile (`< 992px`), grid otomatis bertransformasi menjadi 1 kolom vertikal yang alami tanpa horizontal scroll.
* **Verdict**: ✅ **Dipilih sebagai Solusi Implementasi**.

---

## 4. Rincian Penataan & Dimensi Masing-Masing Kartu (Kiri & Kanan)

### 4.1 Baris 1: Radar Kritis & Peringatan (Kiri & Kanan)
* **Kiri (`col-lg-6 col-md-6 col-sm-12`)**:
  - **Komponen 1: Peringatan Kedisiplinan Logbook**
  - Menggunakan wrapper `<div class="col-lg-6 col-md-6 col-sm-12 mb-3">`.
  - Di dalam kartu: Arch Gauge (140px x 80px) di sebelah kiri, dan 3 KPI Tile ditata secara bertumpuk rapi (`flex-direction: column` atau grid 1-kolom tile) di sebelah kanan. Tidak ada lagi ruang kosong ratusan piksel yang terbuang.
  - Button Sleman Navy berada di footer kartu.
* **Kanan (`col-lg-6 col-md-6 col-sm-12`)**:
  - **Komponen 4: Perhatian Mentee Selesai Magang (&le; 14 Hari)**
  - Menggunakan wrapper `<div class="col-lg-6 col-md-6 col-sm-12 mb-3">`.
  - Lintasan 3 stasiun Runway Track berada dalam rentang lebar ~450px, titik bercahaya dan konektor garis gradien tampak proporsional dan elegan.
  - *Penanganan Kondisi Dinamis*: Jika salah satu peringatan bernilai 0 (misal tidak ada mentee yang offboarding), kartu yang aktif dapat mengisi penuh `col-12` atau menampilkan kartu status tenang "*Semua Mentee On-Track*".

### 4.2 Baris 2: Analitik Aktivitas & Tenggat Waktu (Kiri & Kanan)
* **Kiri (`col-lg-6 col-md-6 col-sm-12`)**:
  - **Komponen 3: Aktivitas Mentoring Minggu Ini**
  - 4 KPI mini di header ditata dalam grid 2x2 yang kompak.
  - 7 pilar kapsul vertikal (34px) berada di tengah container dengan jarak antar pilar 10-12px (persis seperti referensi Zenius).
  - Stacked volume distribution bar di bagian bawah.
* **Kanan (`col-lg-6 col-md-6 col-sm-12`)**:
  - **Komponen 2: Tenggat Waktu Tugas Mendekati Batas Akhir & Penugasan Mentee**
  - Panel header dilengkapi tombol quick filter (`Semua`, `Mendesak`, `Perlu Review`).
  - Bar horison 14px membentang ~480px dengan Target Pin tunggal yang menunjuk presisi ke titik target.
  - Footer kartu memuat tombol aksi direct ke Tab Antrean dan Modul Tugas.

### 4.3 Baris 3: Action Hub - Antrean Evaluasi & Validasi (Kiri & Kanan)
* **Kiri (`col-lg-6 col-md-6 col-sm-12`)**:
  - **Komponen 5: Antrean Progres Tugas**
  - Radial SVG ring biru 88px dengan angka sentral `3`, status badge `Perlu Evaluasi`, dan tombol direct action.
* **Kanan (`col-lg-6 col-md-6 col-sm-12`)**:
  - **Komponen 6: Antrean Review Logbook**
  - Radial SVG ring amber 88px dengan angka sentral `4`, status badge `Perlu Validasi`, dan tombol direct action.

### 4.4 Baris 4: Executive Cohort Matrix (Full Width Anchor - `col-12`)
* **Full Width (`col-12`)**:
  - **Komponen 7: Daftar Mentee Bimbingan Aktif (Ringkasan)**
  - Tetap menjadi kesimpulan eksekutif lebar di bagian bawah yang membagi 2 kolom internal: Tahap Durasi Magang (0-30%, 31-70%, 71-100%) di kiri, dan Kesiapan Nilai Rubrik (Final, Draf, Belum) di kanan.

---

## 5. Rencana Perubahan Berkas (File Modification Plan)

| Berkas | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/pembimbing/dashboard/partials/_tab_ringkasan.blade.php` | Modify | 1. Membungkus Komponen 1 & 4 ke dalam `<div class="row">` dengan pasangan `col-lg-6 col-md-6 col-sm-12`.<br>2. Membungkus Komponen 3 & 2 ke dalam `<div class="row">` dengan pasangan `col-lg-6 col-md-6 col-sm-12`.<br>3. Mempertahankan Komponen 5 & 6 dalam `row` 2-kolom yang sudah ada.<br>4. Mempertahankan Komponen 7 dalam `col-12` full width anchor.<br>5. Menjaga 100% data headless semantic layer (`sr-only`) untuk pengujian PHPUnit. |
| `resources/css/pages/pembimbing/dashboard.css` | Modify | 1. Menambahkan utility layout Bento Card: `.bento-card-h100` untuk menyamakan tinggi kartu per baris.<br>2. Menyesuaikan flex layout internal Komponen 1 agar 3 KPI tiles bertumpuk vertikal kompak saat berada di kolom 50%.<br>3. Menyesuaikan padding pilar chart Komponen 3 agar terpusat rapi tanpa ruang kosong berlebih.<br>4. Breakpoint responsif `@media (max-width: 991px)` untuk reflow mulus ke 1 kolom. |

---

## 6. Tahapan Langkah Implementasi (Step-by-Step Implementation)

- [x] **Langkah 1: Restrukturisasi Grid Blade (`_tab_ringkasan.blade.php`)**
  - Buat Baris 1: Row kontainer untuk Komponen 1 (Logbook Arch) dan Komponen 4 (Offboarding Runway) dengan kolom `col-lg-6 col-md-6 col-sm-12`.
  - Buat Baris 2: Row kontainer untuk Komponen 3 (Aktivitas 7 Hari) dan Komponen 2 (Tenggat Waktu Tugas) dengan kolom `col-lg-6 col-md-6 col-sm-12`.
  - Pastikan Baris 3 (Antrean Progres & Logbook) dan Baris 4 (Cohort Matrix) terintegrasi rapi.
  - Pastikan seluruh tag `<div class="sr-only" aria-hidden="true">` tetap utuh.

- [x] **Langkah 2: Penyesuaian Styling Modular CSS (`dashboard.css`)**
  - Tambahkan class `.zenius-bento-row` dan `.zenius-bento-col` dengan display flex and equal height.
  - Sempurnakan `.zenius-stat-tiles-row` di dalam Komponen 1 agar memiliki opsi flex-column yang rapat.
  - Sempurnakan lebar pilar Zenius agar tidak meregang di luar ukuran optimal 34px.

- [x] **Langkah 3: Kompilasi Aset Frontend Vite**
  - Jalankan `npm run build`.

- [x] **Langkah 4: Verifikasi Kualitas Pengujian Otomatis**
  - `php artisan test --filter=PembimbingDashboardEnhancementTest`
  - `php artisan test --filter=GentelellaDashboardRevampTest`
  - `php artisan test --filter=RichTaskAssignmentTest`
  - `php artisan test --filter=Pembimbing` (Seluruh 109 tes wajib 100% green).
  - `./vendor/bin/pint --test`
  - `./vendor/bin/phpstan analyse --memory-limit=1G`
  - `python -m graphify update .`

---

## 7. Kriteria Selesai (Definition of Done)

- [x] Tidak ada lagi kartu yang memanjang 100% ke bawah satu per satu di layar desktop lebar.
- [x] Komponen tertata dalam Bento Grid 2 Kolom (Kiri & Kanan) yang simetris, padat, dan proporsional.
- [x] Grafik pilar 7 hari Zenius, speedometer 180° EduCare, dan runway track memiliki dimensi kompak tanpa celah kosong raksasa.
- [x] Halaman dashboard jauh lebih hemat ruang vertikal (scroll berkurang drastis).
- [x] Seluruh 109 skenario tes pembimbing tetap lulus 100% (591 assertions).
- [x] Kompilasi Vite bersih, Pint formatting lolos, dan PHPStan 0 error.

