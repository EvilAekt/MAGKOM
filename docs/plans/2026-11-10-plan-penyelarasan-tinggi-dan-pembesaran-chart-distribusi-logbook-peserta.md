# Rencana Kerja (Plan): Penyelarasan Tinggi & Pembesaran Tampilan Distribusi Tugas dan Capaian Logbook Peserta

**Tanggal Dibuat:** 10 November 2026  
**Status:** Selesai Diimplementasikan (Verified)  
**Tujuan Dokumen:** Perencanaan terstruktur untuk memperbesar tampilan visual grafik **Distribusi Tugas** dan **Capaian Logbook** serta menyelaraskan ketinggian kolom utama (`.bento-col-main`) dengan kolom samping (`.bento-col-side`) agar tidak tersisa ruang kosong (*dead space*) di bagian bawah, sesuai tangkapan layar dan instruksi pengguna (`media_1794307102366_344f598f.jpg`).

---

## 1. Analisis Masalah & Penyebab (*Root Cause Analysis*)

Berdasarkan inspeksi berkas [`resources/css/pages/peserta/dashboard.css`](file:///d:/MAGKOM/MAGKOM/resources/css/pages/peserta/dashboard.css) dan tangkapan layar pengguna:
1. **Ketimpangan Ketinggian (*Height Mismatch*)**:
   - Kolom kanan (`.bento-col-side`) memuat widget kalender bulanan (`.bento-calendar-card` setinggi ~320px) ditambah kartu peringatan amber sisa hari magang (`.bento-alert-card` setinggi ~150px) dengan total akumulasi tinggi mencapai **~490px - 510px**.
   - Kolom kiri (`.bento-col-main`) hanya memiliki baris KPI mini (~110px) dan baris grafik analitik (`.bento-mid-row` dengan `min-height: 230px`), dengan total akumulasi tinggi hanya **~360px - 380px**.
   - Akibatnya, terdapat celah ruang kosong mengambang (*dead void*) setinggi **~120px - 140px** di bawah kartu *Distribusi Tugas* dan *Capaian Logbook*.
2. **Skala Grafik Terlalu Kecil**:
   - Pada kartu *Distribusi Tugas*, kontainer chart `.bento-distribusi-chart-box` dibatasi setinggi `155px`, dan tabung bar chart `.bento-bar-container` hanya `100px` dengan lebar `65px`.
   - Pada kartu *Capaian Logbook*, gauge radial SVG hanya berdimensi `160x95px` dengan wrapper `200x115px`, sehingga elemen grafik tampak mengkerut dan meninggalkan banyak ruang kosong vertikal di sekelilingnya.

---

## 2. Tujuan & Sasaran Perubahan

1. **Menghilangkan Ruang Kosong di Bawah Kartu (*Zero Void Guarantee*)**:
   - Menjadikan `.bento-col-main` memanfaatkan tinggi penuh grid layout (`height: 100%`).
   - Menerapkan `flex: 1` pada `.bento-mid-row` sehingga baris kartu *Distribusi Tugas* dan *Capaian Logbook* otomatis meregang (*auto-stretch*) ke bawah menyamai garis batas bawah kolom kalender dan sisa hari magang.
2. **Memperbesar Skala Tampilan Grafik (*Grand & Legible Visuals*)**:
   - **Distribusi Tugas**:
     - Memperbesar tinggi tabung bar chart dari `100px` menjadi `150px - 165px` dan lebar batang dari `65px` menjadi `75px - 80px`.
     - Memperbesar font angka nilai batang `.bento-bar-val` dari `13px` ke `15px` (tebal & kontras).
     - Menjadikan area canvas chart box `.bento-distribusi-chart-box` fleksibel mengisi ruang vertikal kartu (`flex: 1`).
   - **Capaian Logbook**:
     - Memperbesar skala radial gauge SVG dari `160x95px` menjadi `220x130px` (wrapper `240x140px`).
     - Memperbesar teks persentase tengah `.gauge-center-percent` dari `36px` menjadi `44px` (bold executive look).
     - Meratakan posisi gauge dan teks caption ke tengah kontainer secara proporsional.
3. **Penyelarasan Desain & Responsivitas**:
   - Mempertahankan keselarasan warna Sleman Navy (`#003566`, `#01347C`, `#FFC300`, `#d64545`).
   - Menjaga perilaku mobile responsif (`@media (max-width: 1024px)` dan `@media (max-width: 640px)`).

---

## 3. Asumsi & Batasan Teknis

1. **Prinsip Bebas Perubahan Blade**:
   - Struktur semantik HTML di [`resources/views/peserta/dashboard/partials/_bento_charts.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_bento_charts.blade.php) sudah ideal dan menggunakan vector SVG.
   - SVG vector dapat diperbesar secara elastis melalui CSS tanpa distorsi piksel.
   - Seluruh perubahan dipusatkan di lapisan CSS [`resources/css/pages/peserta/dashboard.css`](file:///d:/MAGKOM/MAGKOM/resources/css/pages/peserta/dashboard.css).
2. **Data & Nilai Realtime**:
   - Nilai perhitungan persentase progres logbook (`$pctLogbook`) dan formula `$hSelesai`/`$hOverdue` pada Blade tetap berjalan presisi mengikuti data penempatan peserta.

---

## 4. Evaluasi Opsi Pendekatan Teknis

### Opsi A (Direkomendasikan): Flexbox Auto-Stretch + Scale-Up Graphic Vectors
- **Deskripsi**:
  1. Menetapkan `height: 100%` pada `.bento-col-main` dan `flex: 1` pada `.bento-mid-row`.
  2. Menjadikan `.bento-distribusi-card` dan `.bento-kuota-card` memiliki `height: 100%` dengan `min-height: 290px` (sebelumnya `230px`).
  3. Memperbesar ukuran batang chart menjadi `height: 155px` dan gauge wrapper menjadi `240x135px`, dengan persentase tengah `44px`.
- **Kelebihan**:
  - Kolom kiri dan kanan selalu sejajar rata bawah secara otomatis pada semua skenario tinggi konten (dinamis terhadap ada/tidaknya alert).
  - Tampilan chart menjadi lebih megah, mudah dibaca (*glanceable*), dan mengisi ruang secara seimbang tanpa kesan kosong.
  - Bersih dan aman tanpa risiko merusak Blade atau JavaScript.
- **Kekurangan**:
  - Memerlukan pengujian media query agar tidak terjadi lonjakan tinggi saat kartu berubah menjadi 1 kolom di layar tablet.

### Opsi B: Menambahkan Card Ketiga di Kolom Kiri (Misal: Daftar Pengumuman/Tips)
- **Deskripsi**:
  - Menambahkan modul baru di bawah *Distribusi Tugas* untuk mengisi ruang kosong.
- **Kelebihan**:
  - Ruang terisi dengan komponen baru.
- **Kekurangan**:
  - Menyalahi permintaan pengguna yang secara spesifik meminta *"memperbesar tampilan distribusi tugas dan capaian logbook"*.
  - Menambah kompleksitas backend/query data yang tidak diperlukan.

> **Rekomendasi**: Dipilih **Opsi A** karena secara langsung mewujudkan keinginan pengguna untuk memperbesar tampilan kedua grafik analitik tersebut sekaligus mengunci alignment rata bawah grid secara harmonis.

---

## 5. Rincian Perubahan Berkas

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/css/pages/peserta/dashboard.css`](file:///d:/MAGKOM/MAGKOM/resources/css/pages/peserta/dashboard.css) | Modify | 1. Set `.bento-col-main` `height: 100%` dan `.bento-mid-row` `flex: 1` agar mengisi penuh tinggi grid.<br>2. Naikkan `min-height` `.bento-distribusi-card` & `.bento-kuota-card` dari `230px` menjadi `290px`.<br>3. Perbesar `.bento-distribusi-chart-box` (flex: 1, min-height 210px) dan tabung `.bento-bar-container` (tinggi 150px, lebar 75px, font nilai 15px).<br>4. Perbesar gauge SVG `.bento-gauge-wrapper` (240x135px) dan font `.gauge-center-percent` (44px).<br>5. Pastikan responsivitas mobile tetap rapi. |

---

## 6. Langkah Implementasi Terstruktur

- [x] **Langkah 1**: Backup & inspeksi selector grid pada `resources/css/pages/peserta/dashboard.css`.
- [x] **Langkah 2**: Terapkan peregangan vertikal layout kolom utama:
  - [x] Set `.bento-col-main { height: 100%; display: flex; flex-direction: column; }`.
  - [x] Set `.bento-mid-row { flex: 1; min-height: 0; }`.
  - [x] Set `.bento-distribusi-card` dan `.bento-kuota-card` `{ height: 100%; min-height: 290px; }`.
- [x] **Langkah 3**: Perbesar tampilan visual *Distribusi Tugas*:
  - [x] Ubah `.bento-distribusi-chart-box` menjadi `flex: 1; display: flex; flex-direction: column; justify-content: flex-end; min-height: 210px;`.
  - [x] Ubah `.bento-bars-track` menjadi `height: 100%; min-height: 170px;`.
  - [x] Perbesar `.bento-bar-container` menjadi `height: 150px; max-width: 78px;`.
  - [x] Perbesar `.bento-bar-val` dari `13px` ke `15px` dan label `.bento-bar-label` ke `12px`.
- [x] **Langkah 4**: Perbesar tampilan visual *Capaian Logbook*:
  - [x] Ubah `.bento-kuota-gauge-box` menjadi `flex: 1; justify-content: center; min-height: 210px;`.
  - [x] Perbesar `.bento-gauge-wrapper` menjadi `width: 240px; height: 135px;`.
  - [x] Perbesar `.bento-gauge-svg` menjadi `width: 240px; height: 135px;`.
  - [x] Perbesar font persentase `.gauge-center-percent` dari `36px` menjadi `44px`.
  - [x] Sesuaikan margin dan posisi caption `.bento-gauge-caption`.
- [x] **Langkah 5**: Sesuaikan media query tablet/mobile (`@media (max-width: 1024px)`):
  - [x] Pastikan saat layout beralih ke 1 kolom, kartu kembali ke tinggi wajar tanpa peregangan berlebih.
- [x] **Langkah 6**: Kompilasi aset Vite & verifikasi view:
  - [x] Jalankan `npm run build`.
  - [x] Jalankan `php artisan view:clear`.
- [x] **Langkah 7**: Quality Gate Testing:
  - [x] Jalankan `./vendor/bin/pint --test`.
  - [x] Jalankan `php artisan test tests/Feature/GentelellaDashboardRevampTest.php`.

---

## 7. Strategi Testing

1. **Automated Feature Test**:
   - `php artisan test tests/Feature/GentelellaDashboardRevampTest.php`
   - Memastikan seluruh modul dashboard peserta tetap render 100% valid tanpa regresi.
2. **Visual Verification**:
   - Memastikan tidak ada lagi sisa ruang kosong mengambang di bawah kartu *Distribusi Tugas* dan *Capaian Logbook*.
   - Garis bawah kartu *Capaian Logbook* dan *Distribusi Tugas* sejajar sempurna dengan garis bawah kartu *Sisa Hari Magang*.
   - Batang chart tampak lebih kokoh, gagah, dan nilai angkanya terbaca jelas.
   - Setengah lingkaran gauge logbook tampak lebih besar, seimbang, dan proporsional.
3. **Linter**:
   - `./vendor/bin/pint --test`

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Tinggi kartu *Distribusi Tugas* dan *Capaian Logbook* meregang penuh mengisi kolom utama hingga sejajar rata bawah dengan kolom samping.
- [x] Ruang kosong di bawah kedua kartu hilang sepenuhnya.
- [x] Visual batang chart dan gauge persentase membesar secara proporsional (~30%–40% lebih besar).
- [x] Kompilasi Vite `npm run build` sukses.
- [x] Seluruh unit/feature test dashboard lulus (PASS).
- [ ] Pengguna mengonfirmasi tampilan baru telah sesuai keinginan.

