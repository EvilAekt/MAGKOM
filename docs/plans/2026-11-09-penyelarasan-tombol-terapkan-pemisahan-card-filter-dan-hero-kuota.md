# Rencana Kerja: Penyelarasan Tombol "Terapkan", Pemisahan Card Filter Verifikasi Berkas, dan Integrasi Teks "Bulan & Kuota" ke Hero Banner

Dokumen rencana kerja ini disusun berdasarkan permintaan pengguna untuk menyelaraskan antarmuka Admin MagKom agar lebih konsisten, modular, dan ringkas:
1. Mengubah penamaan tombol filter dari **"Terapkan Filter"** menjadi **"Terapkan"**.
2. Memisahkan card filter pada halaman **Verifikasi Berkas Pendaftaran** (`admin/pendaftaran`) menjadi card mandiri di luar panel tabel, selaras dengan tata letak pada **Penempatan Peserta** (`admin/penempatan`).
3. Memindahkan teks judul **"Bulan & kuota"** pada halaman Kuota (`admin/kuota`) langsung ke dalam hero card biru (`.hero-banner-blue`) agar tata letak halaman lebih padat dan ringkas (*ringkes*).
4. Menyelaraskan dan mengonsistenkan seluruh elemen terkait di portal Admin.

---

## 1. Tujuan
- Menyediakan pengalaman antarmuka yang seragam di seluruh portal Admin: label tombol aksi pencarian konsisten menggunakan **"Terapkan"**.
- Menata ulang layout halaman Verifikasi Berkas (`admin/pendaftaran`) sehingga memiliki struktur visual 3 tingkat yang modular:
  1. *Navigation Bar Kategori Pendaftaran* (Semua Kategori, Individu, Kelompok) di posisi paling atas.
  2. *Card Filter Mandiri* (`.admin-card.adm-filter-card`) terpisah dengan background putih dan border rapi.
  3. *Panel Tabel Utama* (`<x-gentelella.x-panel>`) dengan aksen garis biru khas Sleman yang memuat switcher status workflow (*Semua Status*, *Diajukan*, *Diterima*, *Ditolak*) dan tabel data calon peserta.
- Mengintegrasikan teks **"Bulan & kuota"** ke dalam kontainer hero banner biru (`.hero-banner-blue`) di `admin/kuota/index.blade.php`, meniadakan header luar yang boros ruang vertikal, tanpa merusak aksesibilitas (WCAG 2.1 AA) maupun pengujian otomatis (*test suite*).

---

## 2. Asumsi & Batasan
1. **Asumsi Parameter HTTP GET**:
   - Pemisahan form filter pada `admin/pendaftaran` tidak mengubah URL tujuan, method GET, parameter filter (`q`, `bulan_magang_id`, `jurusan_id`, `sort`, `status`, `tipe`), maupun logika penanganan backend controller.
2. **Asumsi Aksesibilitas & Headings**:
   - Judul halaman "Bulan & kuota" di dalam hero banner biru tetap mempertahankan tag semantik `<h1>` dengan kontras warna teks putih (`#FFFFFF`) terhadap background gradien biru tua (rasio kontras > 7:1, melebihi standar WCAG AAA). Subtitle deskriptif tetap disertakan dengan class `.sr-only`.
3. **Cakupan Penyelarasan Label**:
   - Perubahan label tombol dari "Terapkan Filter" menjadi "Terapkan" diterapkan pada:
     - `resources/views/admin/penempatan/index.blade.php` (Tab Belum Ditempatkan & Tab Sudah Ditempatkan).
     - `resources/views/admin/laporan/index.blade.php` (Tab Pendaftaran & Tab Penempatan).
     - Halaman `resources/views/admin/pendaftaran/index.blade.php` sudah menggunakan "Terapkan" sehingga dijadikan acuan standar (*single source of truth*).

---

## 3. Pendekatan Teknis & Evaluasi Alternatif

### A. Evaluasi Desain Integrasi Judul pada Hero Banner Kuota
| Aspek | Alternatif 1: Eyebrow Tagline Header (Rekomendasi) | Alternatif 2: Stacked Large Heading |
| :--- | :--- | :--- |
| **Deskripsi** | Menjadikan `Bulan & kuota` sebagai heading semantik `h1` bergaya *caps/eyebrow* elegan di baris paling atas `.hero-left-content` berdampingan dengan badge pill `Bulan Aktif`, diikuti oleh nama bulan besar (`November 2026`) dan status badge. | Menjadikan `Bulan & kuota` sebagai judul besar tersendiri di atas badge `Bulan Aktif` dan nama bulan. |
| **Kelebihan** | Sangat ringkas (*ringkes*), menghemat ruang vertikal, hirarki informasi sangat rapi (Entitas -> Status -> Item Aktif), proporsi hero card tetap proporsional dengan kartu statistik kuota di kanan. | Teks judul terlihat besar mencolok. |
| **Kekurangan** | Ukuran font lebih proporsional (1.125rem - 1.25rem) dibanding judul standalone sebelumnya. | Menambah tinggi vertikal banner hero biru secara signifikan, berisiko timpang dengan tinggi kartu okupansi kuota di sebelah kanan. |
| **Keputusan** | **Dipilih Alternatif 1**: Langsung menjawab kebutuhan pengguna agar tampilan "ringkes" dan padat. | Ditolak karena kurang hemat ruang. |

### B. Evaluasi Pemisahan Card Filter pada Verifikasi Berkas
- **Struktur Saat Ini**: Form filter berada di dalam `<x-gentelella.x-panel>` menyatu dengan tabel data.
- **Struktur Target**:
  - `div.adm-category-nav-bar` (Navigasi kategori pendaftaran - berdiri sendiri di atas).
  - `div.admin-card.adm-filter-card` (Card filter mandiri, background `#FFFFFF`, border `#E2E8F0`, border-radius `12px`, padding `1.25rem 1.5rem`, margin-bottom `1.5rem`).
  - `x-gentelella.x-panel :toolbox="false"` (Panel tabel utama dengan header border biru khas, menampung `.adm-table-meta-bar` / tab status alur kerja dan tabel data).

---

## 4. Rencana Perubahan Berkas

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/penempatan/index.blade.php` | Modify | Ubah teks tombol submit filter dari `Terapkan Filter` menjadi `Terapkan` pada tab `sudah` (L967) dan tab `belum` (L1242). |
| `resources/views/admin/laporan/index.blade.php` | Modify | Ubah teks tombol submit filter dari `Terapkan Filter` menjadi `Terapkan` pada tab laporan pendaftaran (L500) dan tab laporan penempatan (L698). |
| `resources/views/admin/pendaftaran/index.blade.php` | Modify | Pindahkan blok `.adm-filter-toolbar` ke luar `<x-gentelella.x-panel>` menjadi kontainer card mandiri `.admin-card.adm-filter-card`, sesuaikan CSS pendukung agar tampilan selaras dengan penempatan peserta. |
| `resources/views/admin/kuota/index.blade.php` | Modify | Hapus wrapper luar `.kuota-page-header`, masukkan `<h1>Bulan &amp; kuota</h1>` dan deskripsi aksesibilitas ke dalam `.hero-banner-blue` (`.hero-left-content`), sesuaikan spasi dan typography. |

---

## 5. Langkah-Langkah Implementasi Bertahap

### Tahap 1: Penyelarasan Penamaan Tombol Filter Menjadi "Terapkan"
- [x] Buka `resources/views/admin/penempatan/index.blade.php`:
  - Ubah tombol submit filter tab `sudah` dari `Terapkan Filter` menjadi `Terapkan`.
  - Ubah tombol submit filter tab `belum` dari `Terapkan Filter` menjadi `Terapkan`.
- [x] Buka `resources/views/admin/laporan/index.blade.php`:
  - Ubah tombol submit filter tab pendaftaran dari `Terapkan Filter` menjadi `Terapkan`.
  - Ubah tombol submit filter tab penempatan dari `Terapkan Filter` menjadi `Terapkan`.

### Tahap 2: Pemisahan Card Filter di Verifikasi Berkas (`admin/pendaftaran`)
- [x] Buka `resources/views/admin/pendaftaran/index.blade.php`:
  - Keluarkan form pencarian dan filter (`.adm-filter-toolbar`) dari dalam tag `<x-gentelella.x-panel>`.
  - Bungkus form tersebut dengan card mandiri berkelas `.admin-card.adm-filter-card` dengan padding dan margin-bottom yang seimbang (`margin-bottom: 1.5rem;`).
  - Pastikan `<x-gentelella.x-panel :toolbox="false">` kini hanya memuat bar status alur kerja (`.adm-table-meta-bar`), tabel data peserta, dan pagination.
  - Perbarui style CSS internal halaman jika diperlukan agar background card filter berwarna putih bersih (`#FFFFFF`) dengan border halus (`#E2E8F0`), persis seperti card filter penempatan peserta.

### Tahap 3: Integrasi Teks "Bulan & Kuota" ke Dalam Hero Banner Biru (`admin/kuota`)
- [x] Buka `resources/views/admin/kuota/index.blade.php`:
  - Hapus div luar `.kuota-page-header`.
  - Di dalam `.hero-banner-blue`, tepat di awal `.hero-left-content`:
    - Tambahkan elemen heading semantik `h1` dan deskripsi aksesibilitas `sr-only` ke dalam `.hero-banner-blue`.
    - Pastikan jika `$selectedBulan` kosong (fallback kondisi), judul "Bulan & kuota" tetap tampil di banner informatif.
  - Perbarui style CSS di bagian `<style>` agar typography `.kuota-page-title` di dalam hero banner memiliki warna putih tegas (`#FFFFFF`) dan tidak bertabrakan dengan dark mode.

### Tahap 4: Verifikasi & Pengujian
- [x] Jalankan pengujian visual halaman admin: `php artisan test tests/Feature/AdminChildPagesVisualTest.php`.
- [x] Jalankan pengujian kuota magang: `php artisan test tests/Feature/AdminKuotaRevampTest.php`.
- [x] Jalankan pengujian pendaftaran: `php artisan test --filter Pendaftaran`.
- [x] Jalankan pengujian penempatan: `php artisan test tests/Feature/AdminPenempatanDuaTahapTest.php`.
- [x] Jalankan linting PHP: `vendor/laravel/pint/builds/pint --test`.
- [x] Jalankan build aset Vite: `npm run build`.

---

## 6. Strategi Pengujian (Testing Strategy)
1. **Feature Test**:
   - `AdminKuotaRevampTest`: Memastikan `assertSee('Bulan &amp; kuota', false)` dan `assertSee('Atur bulan pendaftaran dan kuota magang per bidang.', false)` tetap lulus 100%.
   - `AdminChildPagesVisualTest`: Memastikan komponen filter dan antrean pendaftaran tetap dirender sempurna.
   - `AdminPenempatanDuaTahapTest`: Memastikan filter penempatan tetap berfungsi normal dengan form submit berlabel "Terapkan".
2. **Visual & Interaction Check**:
   - Memastikan card filter verifikasi berkas terpisah rapi dengan jarak margin yang proporsional di atas panel tabel.
   - Memastikan tombol filter di penempatan, pendaftaran, dan laporan semuanya bertuliskan "Terapkan".
   - Memastikan hero banner biru di kuota memuat teks "Bulan & kuota" yang ringkas, kontras, dan rapi.

---

## 7. Risiko & Mitigasi
- **Risiko**: Assertion pengujian mencari elemen judul di luar banner hero.
  - **Mitigasi**: Tag `<h1>` dan class `.kuota-page-title` tetap dipertahankan dengan teks `Bulan &amp; kuota` serta deskripsi `.sr-only` yang sama persis sehingga assertion `assertSee` tetap terpenuhi tanpa kegagalan.
- **Risiko**: Pergeseran tata letak responsif pada resolusi layar mobile saat card filter dipisah.
  - **Mitigasi**: Mempertahankan container responsif grid `adm-filter-grid` yang sudah memiliki `flex-wrap` dan media queries teruji.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Tombol submit filter di penempatan peserta dan laporan bertuliskan **"Terapkan"** (bukan "Terapkan Filter").
- [x] Form filter verifikasi berkas berada di dalam card mandiri tersendiri di luar panel tabel, persis seperti penempatan peserta.
- [x] Teks "Bulan & kuota" terintegrasi langsung di dalam hero card biru, menghilangkan header luar yang memakan tempat.
- [x] Seluruh pengujian otomatis terkait (`AdminKuotaRevampTest`, `AdminChildPagesVisualTest`, `AdminPenempatanDuaTahapTest`) berstatus HIJAU (PASS).
- [x] Asset Vite terkompilasi bersih tanpa error.

