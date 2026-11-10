# Rencana Kerja (Plan): Penyesuaian Dimensi Chart, Efek Hover Judul Tugas, dan Standarisasi Warna Merah Status Terlambat

**Tanggal Dibuat:** 10 November 2026  
**Status:** Selesai Diimplementasikan (Verified)  
**Tujuan Dokumen:** Merencanakan penyesuaian visual terfokus pada:
1. Pengecilan dimensi chart yang kebesaran (Dashboard Peserta & Widget Progres Tugas).
2. Perbaikan efek hover pada judul tugas agar tidak terpotong (anti-clipping).
3. Penyelarasan warna konsep status **Terlambat / Lewat Tenggat** menjadi **Merah (`#DC2626` / `#EF4444`)** pada badge tab filter, KPI card, dan penanda penugasan.

---

## 1. Masalah yang Teridentifikasi (Root Cause Analysis)

1. **Chart Terlalu Besar (Kebesaran)**:
   - **Dashboard Peserta (`_bento_charts.blade.php` & `dashboard.css`)**:
     - Box chart Distribusi Tugas memiliki `min-height: 235px` dan wadah batang (`.bento-bar-container`) setinggi `165px` dengan lebar `105px`. Karena kini hanya menampilkan 2 kategori (*Selesai* & *Terlambat*), ukuran batang terlihat terlalu raksasa dan mendominasi ruang bento secara tidak proporsional dibanding metrik mini di atasnya.
     - Kartu radial gauge Capaian Logbook memiliki tinggi `235px` dengan SVG gauge `160x95px` yang membuat kartu terasa terlalu jangkung.
   - **Halaman Tugas Peserta (`tugas-index.css`)**:
     - Kartu widget Progres Tugas pada hero banner memiliki lebar tetap `320px` dengan padding `20px 24px` dan angka persentase `1.75rem`, yang dapat dibuat lebih proporsional dan kompak.

2. **Efek Hover di Judul Terlihat Kepotong**:
   - Seluruh elemen `.assignment-card` dibungkus dalam tag tautan `<a>`. Saat kursor diarahkan ke kartu atau judul, browser/framework (Bootstrap/Gentelella) dapat menerapkan `text-decoration: underline` secara default.
   - Karena judul penugasan menggunakan `font-weight: 800; font-size: 1.05rem; line-height: 1.35;` tanpa padding vertikal, garis bawah (*underline*) memotong huruf berekor (*descenders* seperti **g, j, p, y** pada kata *"Uji"* dan *"Magang"*) sehingga teks terlihat seperti terpotong atau patah-patah (*choppy*).
   - Efek `transform: translateY(-2px)` pada kartu yang dibungkus container `overflow: hidden` juga dapat memotong tepi atas kartu jika headroom terlalu tipis.

3. **Warna Konsep Status Terlambat Menggunakan Kuning, Seharusnya Merah**:
   - Pada filter tab `Lewat Tenggat [1]`, badge count saat ini menggunakan warna kuning emas (`#FEF3C7` / `#B45309`), padahal konsep penanda risiko/terlambat di MagKom adalah merah (`#DC2626` / `#EF4444`).
   - Pada KPI card ke-3 `LEWAT TENGGAT`, kotak ikon menggunakan warna kuning (`#FFC300`) alih-alih warna merah peringatan.

---

## 2. Ruang Lingkup Perubahan (Scope of Work)

Perubahan dirancang terfokus (*minimal, focused change*) tanpa mengubah kontrak route, API, maupun business logic:

1. **Pengecilan Dimensi Chart (Dashboard & Tugas)**:
   - **Dashboard Peserta (`resources/css/pages/peserta/dashboard.css`)**:
     - Perkecil `bento-distribusi-chart-box` dari tinggi `235px` menjadi `160px`.
     - Perkecil tinggi batang chart dari `165px` menjadi `105px` dan lebar maksimum dari `105px` menjadi `65px–70px`.
     - Perkecil box radial gauge Capaian Logbook dan SVG gauge agar simetris dengan box Distribusi Tugas.
   - **Halaman Tugas Peserta (`resources/css/pages/peserta/tugas-index.css`)**:
     - Sesuaikan `.banner-progress-card` dari `320px` menjadi `275px`, padding lebih ramping `16px 20px`, persentase `1.5rem` agar menyatu proporsional di dalam banner.

2. **Perbaikan Hover Judul Tugas (Anti-Clipping & Clean Look)**:
   - Terapkan `text-decoration: none !important;` pada `.assignment-card`, `.assignment-card:hover`, `.assignment-title`, dan child element.
   - Tingkatkan `line-height` pada `.assignment-title` dari `1.35` menjadi `1.45` serta tambahkan `padding: 2px 0` untuk ruang gerak huruf vertikal.
   - Perkecil pergeseran hover kartu menjadi `transform: translateY(-1px)` dengan bayangan lembut, sehingga tidak ada bagian yang terpotong.

3. **Standarisasi Warna Merah Status Terlambat**:
   - **Tab Filter Lewat Tenggat**:
     - Badge tidak aktif: latar merah lembut `#FEE2E2` dengan teks merah tegas `#DC2626`.
     - Tab aktif: latar merah bahaya `#DC2626` dengan badge putih `#FFFFFF`.
   - **KPI Card Lewat Tenggat**:
     - Kotak ikon: latar `#FEE2E2` dengan ikon merah `#DC2626`.
     - Efek hover kartu: border hover merah lembut `rgba(220, 38, 38, 0.4)`.
   - **Kartu Tugas Terlambat**:
     - Berikan aksen garis tepi kiri merah halus (`border-left: 3px solid #DC2626`) saat tugas berstatus lewat tenggat.

---

## 3. Rincian Perubahan File

| File | Tipe Perubahan | Deskripsi Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/peserta/tugas-index.css` | Styling | 1. Reset `text-decoration: none !important;` pada hover judul & kartu.<br>2. Perbaiki `line-height: 1.45` dan padding judul.<br>3. Standarisasi warna merah `#DC2626` untuk badge tab `Lewat Tenggat` & icon box KPI.<br>4. Perkecil dimensi widget Progres Tugas agar lebih proporsional. |
| `resources/css/pages/peserta/dashboard.css` | Styling | 1. Perkecil tinggi chart Distribusi Tugas (`min-height: 160px`).<br>2. Perkecil ukuran batang chart (`height: 105px; max-width: 68px`).<br>3. Perkecil box dan SVG radial gauge Capaian Logbook agar simetris. |
| `resources/views/peserta/tugas/index.blade.php` | Markup (Kecil) | Pastikan kelas penanda `kpi-terlambat` terpasang pada KPI Card ke-3 untuk styling warna merah. |
| `resources/views/peserta/dashboard/partials/_bento_charts.blade.php` | Markup (Kecil) | Sesuaikan atribut dimensi SVG gauge jika diperlukan untuk proporsi visual yang presisi. |

---

## 4. Langkah-Langkah Eksekusi Bertahap

### Fase 1: Perbaikan Styling Halaman Tugas (`resources/css/pages/peserta/tugas-index.css`)
- [x] **Langkah 1.1**: Perbaiki styling `.assignment-title` dan `.assignment-card:hover`:
  - Hilangkan garis bawah dengan `text-decoration: none !important;`.
  - Tingkatkan `line-height: 1.45;` dan `padding: 2px 0;`.
  - Sesuaikan `transform: translateY(-1px);` untuk transisi hover yang halus.
- [x] **Langkah 1.2**: Update palet warna status Terlambat:
  - Tab badge `badge-overdue`: `background: #FEE2E2; color: #DC2626;`.
  - Tab aktif `Lewat Tenggat`: `background: #DC2626; border-color: #DC2626; color: #FFFFFF;`.
  - Kotak ikon KPI `.icon-terlambat`: `background: #FEE2E2; color: #DC2626;`.
  - Aksen kartu tugas lewat tenggat: `border-left: 3px solid #DC2626;`.
- [x] **Langkah 1.3**: Rampingkan widget `.banner-progress-card` (lebar 275px, padding 16px 20px, persentase 1.5rem).

### Fase 2: Pengecilan Dimensi Chart Dashboard (`resources/css/pages/peserta/dashboard.css`)
- [x] **Langkah 2.1**: Sesuaikan tinggi `.bento-distribusi-chart-box` menjadi `155px` dan `.bento-bars-track` menjadi `125px`.
- [x] **Langkah 2.2**: Sesuaikan ukuran wadah batang `.bento-bar-container` menjadi `height: 100px; max-width: 65px;`.
- [x] **Langkah 2.3**: Sesuaikan box radial gauge `.bento-kuota-gauge-box` dan `.bento-kuota-card` agar tinggi dan simetrinya serasi dengan kartu Distribusi Tugas.

### Fase 3: Kompilasi & Verifikasi Quality Gate
- [x] **Langkah 3.1**: Jalankan `npm run build` untuk mengompilasi CSS dan JS Vite.
- [x] **Langkah 3.2**: Jalankan pengujian Feature Test:
  - `PesertaTugasFilterTabsTest`
  - `PesertaClassroomTugasLogbookTest`
- [x] **Langkah 3.3**: Jalankan linter Laravel Pint (`./vendor/bin/pint --test`).
- [x] **Langkah 3.4**: Verifikasi visual di browser pada `/peserta/tugas` dan `/peserta/dashboard`.

---

## 5. Kriteria Selesai (Definition of Done)

- [x] Chart Distribusi Tugas dan Capaian Logbook di dashboard peserta tampil proporsional, kompak, dan tidak lagi terlihat kebesaran.
- [x] Widget Progres Tugas di banner `/peserta/tugas` tampil lebih ramping dan pas.
- [x] Hover pada judul penugasan bersih, mulus, dan tidak lagi ada teks atau garis bawah yang terpotong.
- [x] Status Terlambat / Lewat Tenggat konsisten bernuansa merah (`#DC2626` / `#FEE2E2`) pada badge filter tab, KPI card, dan item tugas.
- [x] Seluruh pengujian fitur dan linter Pint lulus 100%.

