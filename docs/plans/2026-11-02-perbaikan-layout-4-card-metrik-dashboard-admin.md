# Plan: Perbaikan Layout 4 Card Metrik Hero Banner Dashboard Administrasi

**Tanggal**: 2 November 2026  
**Status**: Menunggu Persetujuan Pengguna (*Pending Approval*)  
**File Rencana**: `docs/plans/2026-11-02-perbaikan-layout-4-card-metrik-dashboard-admin.md`

---

## 1. Tujuan
Memperbaiki layout 4 kartu metrik operasional (*Verifikasi Tertunda*, *Kapasitas Kuota Terisi*, *Antrean Waiting List*, dan *Peserta Magang Aktif*) pada *Hero Banner* Dashboard Administrasi agar:
1. **Menghilangkan sela/ruang kosong di sisi kanan** (sebagaimana terlihat pada tangkapan layar kedua `media_1793603615887_6567022d.png`).
2. Menghasilkan tata letak grid 4-kolom yang simetris, rapi, proporsional, dan merata 100% memenuhi lebar hero banner (*edge-to-edge* yang presisi).
3. Memiliki perilaku responsif adaptif (4 kolom di layar desktop, 2x2 kolom di tablet, dan 1 kolom di mobile).
4. Mempertahankan paritas desain MagKom Sleman (kartu semi-transparan dengan *backdrop-blur*, border halus, tipografi kontras tinggi, dan efek *hover micro-interaction* halus).

---

## 2. Analisis Akar Masalah (Root Cause)
1. **Container Flexbox Tanpa Distribusi Penuh**:
   - Di `resources/views/admin/dashboard/partials/_metrics.blade.php`, kontainer metrik hanya menggunakan kelas generik `<div class="row tile_count">`.
   - `.tile_count` diwarisi dari `dashboard.blade.php` dan `gentelella.css` yang memiliki aturan `display: flex; flex-wrap: wrap;` dengan item `.tile_stats_count` bertarget `flex: 1 1 200px`.
   - Di layar desktop yang lebar (1200px–1920px), 4 kartu tersebut hanya memakan lebar alami masing-masing sekitar ~200px–220px (total ~880px), sehingga menyisakan ruang kosong besar di sebelah kanan kartu ke-4 (*Peserta Magang Aktif*).
2. **Override Negatif Margin di `dashboard.css`**:
   - Selector `.cv-hero-banner .tile_count` di `pages/admin/dashboard.css` memiliki `margin: 1.75rem -2rem 0 -2rem !important;` namun tidak mendefinisikan `display: grid` atau pembagian kolom 4-fraksi (`repeat(4, 1fr)`).
3. **Solusi Standar SuperAdmin yang Terbukti**:
   - Pada SuperAdmin (`_metrics.blade.php`), masalah ini dipecahkan dengan kelas grid `.sa-metrics-dock-grid` yang menerapkan `display: grid !important; grid-template-columns: repeat(4, 1fr) !important; gap: 1.25rem !important; width: 100% !important;` dengan setiap kolom kartu `.sa-metric-card-col` memiliki `width: 100% !important; padding: 0 !important;`.

---

## 3. Opsi Pendekatan Arsitektur

### Opsi A: Mengadopsi Pola CSS Grid SuperAdmin (Direkomendasikan ⭐)
- Menambahkan kelas khusus `.adm-metrics-dock-grid` pada kontainer metrik dan `.adm-metric-card-col` pada tiap kartu di `_metrics.blade.php`.
- Menuliskan aturan layout CSS Grid di `resources/css/pages/admin/dashboard.css`, disinkronkan ke `resources/css/layouts/dashboard-cockpit.css`, `public/css/dashboard-cockpit-unified.css`, dan `public/css/gentelella-theme.css`.
- **Kelebihan**:
  - CSS Grid membagi tepat 4 kolom sama rata (`repeat(4, minmax(0, 1fr))`) sehingga otomatis mengisi 100% lebar kontainer tanpa ada sela kosong di kanan.
  - Perilaku breakpoint sangat stabil (Desktop: 4 kolom, Tablet <= 1100px: 2 kolom, Mobile <= 640px: 1 kolom).
  - Identik dengan arsitektur dashboard SuperAdmin yang sudah teruji stabil.
- **Kekurangan**:
  - Membutuhkan penambahan class attribute pada file Blade partial `_metrics.blade.php`.

### Opsi B: Memaksa Flexbox Stretch `flex: 1 0 0`
- Menggunakan flexbox dengan `justify-content: space-between` dan `flex: 1 1 0px` pada `.tile_stats_count`.
- **Kelebihan**:
  - Tidak mengubah struktur kelas grid.
- **Kekurangan**:
  - Jika teks subtitle berbeda panjangnya (misal kartu 2 memiliki teks panjang "16/30 Kursi • Keterisian Kuota Magang Periode"), flexbox dapat menghasilkan lebar kartu yang tidak sama rata (kartu 2 lebih lebar daripada kartu 1), membuat kartu terlihat tidak simetris.

**Keputusan**: Dipilih **Opsi A** karena menjamin lebar ke-4 kartu tepat 25% (simetris sempurna), rapi, dan konsisten dengan SuperAdmin.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/dashboard/partials/_metrics.blade.php` | Modify | Menambahkan kelas `.adm-metrics-dock-grid` pada kontainer dan `.adm-metric-card-col` pada tiap `<x-gentelella.tile-stat>` (Permintaan izin Blade). |
| `resources/css/pages/admin/dashboard.css` | Modify | Mendefinisikan `.adm-metrics-dock-grid` (`display: grid`, 4 kolom, gap `1rem`/`1.25rem`, width 100%) dan `.adm-metric-card-col` beserta styling interior kartu yang proporsional. |
| `resources/css/layouts/dashboard-cockpit.css` | Modify | Menyelaraskan aturan grid dock 4-kartu agar konsisten di level layout dashboard. |
| `public/css/dashboard-cockpit-unified.css` | Modify | Sinkronisasi fallback stylesheet untuk paritas non-Vite legacy asset loading. |
| `public/css/gentelella-theme.css` | Modify | Sinkronisasi fallback theme stylesheet. |

---

## 5. Permohonan Izin Perubahan Blade (Aturan AGENTS.md Bagian 3.3)
Sesuai aturan **AGENTS.md Bagian 3.3**, perubahan markup Blade hanya boleh dilakukan jika mutlak diperlukan dan wajib meminta persetujuan pengguna terlebih dahulu:
- **File**: [`resources/views/admin/dashboard/partials/_metrics.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/dashboard/partials/_metrics.blade.php)
- **Bagian yang Diubah**:
  1. Kontainer pembungkus:
     ```html
     <!-- Dari: -->
     <div class="row tile_count" role="region" aria-label="Metrik Operasional Administrasi">
     <!-- Menjadi: -->
     <div class="row tile_count adm-metrics-dock-grid" role="region" aria-label="Metrik Operasional Administrasi">
     ```
  2. Atribut `col` pada masing-masing 4 komponen `<x-gentelella.tile-stat>`:
     ```html
     <!-- Dari: -->
     col="col-md-3 col-sm-6 col-xs-12"
     <!-- Menjadi: -->
     col="col-md-3 col-sm-6 col-xs-12 adm-metric-card-col"
     ```
- **Alasan**: Diperlukan untuk mengaitkan selector CSS Grid khusus dashboard admin tanpa merusak pemakaian `.tile_count` di halaman legacy lainnya. Tidak ada tag `<style>`, tag `<script>`, maupun inline style yang disisipkan di Blade.

---

## 6. Langkah Implementasi Terstruktur

- [x] **Langkah 1**: Edit [`resources/views/admin/dashboard/partials/_metrics.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/dashboard/partials/_metrics.blade.php) untuk menyematkan kelas hook `adm-metrics-dock-grid` dan `adm-metric-card-col`.
- [x] **Langkah 2**: Perbarui [`resources/css/pages/admin/dashboard.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/dashboard.css) dengan aturan:
  - `.adm-metrics-dock-grid`: `display: grid !important; grid-template-columns: repeat(4, minmax(0, 1fr)) !important; gap: 1rem !important; width: 100% !important; margin: 1.25rem 0 0 0 !important;`
  - Responsive breakpoints: 2 kolom pada `<= 1100px`, 1 kolom pada `<= 640px`.
  - `.adm-metric-card-col`: `width: 100% !important; min-width: 0 !important; padding: 0 !important; flex: initial !important;`
  - `.adm-metric-card-col .tile_stats_inner`: `height: 100% !important; width: 100% !important; display: flex !important; flex-direction: column !important; justify-content: space-between !important;`
- [x] **Langkah 3**: Sinkronkan aturan tersebut ke:
  - [`resources/css/layouts/dashboard-cockpit.css`](file:///d:/laragon/www/MAGKOM/resources/css/layouts/dashboard-cockpit.css)
  - [`public/css/dashboard-cockpit-unified.css`](file:///d:/laragon/www/MAGKOM/public/css/dashboard-cockpit-unified.css)
  - [`public/css/gentelella-theme.css`](file:///d:/laragon/www/MAGKOM/public/css/gentelella-theme.css)
- [x] **Langkah 4**: Jalankan build asset Vite:
  - `npm run build`
  - Verifikasi bahwa `public/hot` tidak ada (`False`).
- [x] **Langkah 5**: Jalankan Quality Gates:
  - `./vendor/bin/pint --test`
  - `php artisan test --filter Admin` (pastikan 240 assertions lulus tanpa regresi).

---

## 7. Strategi Pengujian (Quality Gate)
1. **Pemeriksaan Visual**:
   - Di layar desktop: 4 kartu metrik terbagi rata persis 25% setiap kolom, memenuhi seluruh lebar banner dari tepi kiri hingga tepi kanan, menghilangkan sela kosong.
   - Di tablet (<= 1100px): Membagi menjadi 2 baris x 2 kolom yang rapi.
   - Di mobile (<= 640px): Menumpuk rapi 1 kolom vertikal.
2. **Pengujian Otomatis**:
   - Jalankan `php artisan test --filter AdminDashboardEnhancementTest` untuk memverifikasi seluruh assertion DOM dan token aksesibilitas tetap valid.
   - Jalankan `php artisan test --filter Admin` (seluruh suite admin).

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Tidak ada lagi sela kosong di sebelah kanan kartu ke-4 pada dashboard admin.
- [x] Keempat kartu memiliki lebar yang sama persis (simetris) dan mengisi 100% lebar hero banner.
- [x] Teks dan angka di dalam kartu tertata rapi, tidak terpotong, dan memiliki ruang yang cukup.
- [x] Responsive grid berjalan mulus saat ukuran jendela diubah.
- [x] Mode gelap (`[data-theme="dark"]`) tetap mempertahankan kontras dan estetika yang selaras.
- [x] Quality gate `pint` dan `php artisan test --filter Admin` lulus 100%.

