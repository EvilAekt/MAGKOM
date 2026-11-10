# Rencana Kerja: Perbaikan Proporsi Ukuran Hero Slider, Sinkronisasi Kuota Jurusan Database, dan Penggunaan Logo Resmi Kominfo

**Tanggal**: 26 Oktober 2026  
**Status**: Selesai Diimplementasikan & Terverifikasi (Production Ready)  
**Dokumen**: `docs/plans/2026-10-26-perbaikan-ukuran-hero-database-jurusan-dan-logo-resmi.md`  
**Area Terkait**:
1. Hero Section Slider (`#beranda` pada `resources/views/public/landing.blade.php` & `public/css/landing-modern.css`)
2. Section Kuota Jurusan (`#kuota` pada `resources/views/public/landing.blade.php`, `public/css/landing-modern.css`, `public/js/landing-interactive.js`)
3. Header & Footer Branding (`resources/views/public/landing.blade.php` & `resources/views/partials/logo-sleman.blade.php`)

---

## 1. Latar Belakang & Masalah
Berdasarkan umpan balik dari pengguna:
1. **Ukuran Hero Slider Membengkak Terlalu Besar**:
   - Slide dokumentasi foto penuh yang diberi `min-height: 420px` serta aturan `align-items: stretch` menyebabkan kontainer hero tertarik ke bawah secara berlebihan sehingga tampilan awal Slide 1 (informasi pendaftaran) menjadi terlalu tinggi, renggang, dan terkesan raksasa (*oversized*).
   - Pengguna meminta agar ukurannya disesuaikan kembali ke proporsi tampilan awal yang kompak, rapi, dan pas.
2. **Daftar Jurusan Belum Sinkron dengan Database**:
   - Komponen Kuota Explorer saat ini masih menampilkan 4 Bidang fiktif (Aptika, Statistik, Sandi, Sekretariat) dari dummy data JavaScript, bukan daftar jurusan riil dari database.
   - Padahal di database Laravel sudah terdapat data master lengkap:
     - **Bulan Magang Aktif**: November 2026 dan Desember 2026.
     - **Jurusan SMK**: *Teknik Komputer dan Jaringan (TKJ)* dan *Rekayasa Perangkat Lunak (RPL)*.
     - **Jurusan Mahasiswa (D3/S1)**: *Sistem Informasi*, *Manajemen Informatika*, *Informatika*, dan *Teknik Komputer*.
     - **Kuota & Ketersediaan**: Data kuota kursi, kuota terisi, sisa kursi, serta status (Tersedia / Waiting List / Tutup) yang sudah dihitung oleh `LandingController` melalui `$matriksKetersediaan`.
3. **Logo Belum Menggunakan Logo Resmi Kominfo**:
   - Header saat ini masih memakai logo placeholder huruf 'S' kotak berwarna emas dari template repositori.
   - Pengguna menginstruksikan untuk menggunakan logo resmi yang telah digunakan sebelumnya di sistem, yaitu lambang resmi Pemkab Sleman / Diskominfo melalui `@include('partials.logo-sleman')`.

---

## 2. Rincian Solusi & Pendekatan Teknis

### Bagian A: Menormalkan Ukuran Hero Slider (Proporsional & Kompak)
1. **Hapus Paksaan Tinggi Raksasa**:
   - Hapus `min-height: 420px;` pada `.hero-full-image-slide`.
   - Atur tinggi yang proporsional sesuai tinggi alami `.hero-box` Slide 1 (desktop: ~`320px - 340px`, mobile: responsif).
2. **Kompakkan Padding `.hero-box`**:
   - Kembalikan padding `.hero-box` desktop dari `2.75rem 2.5rem` menjadi `2rem 2.25rem` dengan gap `1.5rem` agar teks, syarat umum, dan berkas pendaftaran tidak renggang kosong.
3. **Konsistensi Slide Foto Penuh**:
   - Gambar foto dokumentasi menggunakan `height: 100%; width: 100%; object-fit: cover; object-position: center; border-radius: 0.75rem;` sehingga gambar mengisi pas sesuai tinggi Slide 1 tanpa meregangkan kontainer secara paksa.

---

### Bagian B: Logo Resmi Diskominfo Sleman pada Header & Branding
1. **Penggantian Logo Header**:
   - Ganti elemen placeholder `<span class="brand-badge">S</span>` di `resources/views/public/landing.blade.php`:
     ```html
     <a href="{{ route('landing') }}" class="landing-brand">
         @include('partials.logo-sleman', ['class' => 'brand-logo-img', 'width' => '38', 'height' => '38', 'style' => 'width: 38px; height: 38px; object-fit: contain;'])
         <span class="brand-text">
             <span class="brand-name">DISKOMINFO SLEMAN</span>
             <span class="brand-sub">Portal Magang · MAGKOM</span>
         </span>
     </a>
     ```
2. **Konsistensi Visual**:
   - Terapkan styling `.brand-logo-img` dengan `object-fit: contain` dan filter bayangan halus agar lambang resmi Pemkab Sleman tampil tajam dan resmi pada navbar.

---

### Bagian C: Menampilkan Kuota Jurusan Riil dari Database (Blade Native + Interaktif)
1. **Struktur Data yang Digunakan**:
   - Controller (`LandingController.php`) sudah menyediakan:
     - `$bulanTampil`: Koleksi bulan aktif (Nov 2026, Des 2026).
     - `$jurusanSmk`: Jurusan kategori SMK.
     - `$jurusanMahasiswa`: Jurusan kategori D3/S1.
     - `$matriksKetersediaan`: Matriks sisa kursi, total kursi, persen terisi, dan status ketersediaan.
2. **Render Langsung di Blade (`landing.blade.php`)**:
   - **Tombol Jenjang**: Tab aktif "SMK" dan "Mahasiswa (D3/S1)".
   - **Tombol Filter Bulan**: Mengambil langsung dari `$bulanTampil` (November 2026, Desember 2026).
   - **Kartu Kuota Jurusan Riil**:
     - Judul: Nama jurusan resmi dari database (contoh: *Teknik Komputer dan Jaringan (TKJ)*, *Rekayasa Perangkat Lunak (RPL)*, *Sistem Informasi*, dll).
     - Badge Status: *Tersedia*, *Waiting List*, atau *Tutup*.
     - Angka Kapasitas: Sisa kuota dan total kuota riil dari database.
     - Progress Bar: Persentase keterisian kuota riil.
     - Tombol Pendaftaran: Tautan langsung ke `url('/register?bulan=' . $bulan->id . '&jurusan=' . $jurusan->id . '&tipe=mandiri&jumlah=1')` atau tombol antrean jika waiting list.
3. **Interaktivitas JavaScript Ringan (`landing-interactive.js`)**:
   - Mengontrol perpindahan tab jenjang (SMK <-> Mahasiswa), pergantian bulan, dan tombol navigasi carousel scroll kiri/kanan.
   - Gauge lingkaran otomatis menghitung akumulasi total kursi tersisa berdasarkan kartu jurusan yang sedang aktif tampil.

---

## 3. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/landing.blade.php` | Modify | 1) Ganti badge "S" dengan `@include('partials.logo-sleman')`. 2) Render kartu kuota jurusan riil dari `$jurusanSmk` dan `$jurusanMahasiswa` menggunakan `$matriksKetersediaan` dan `$bulanTampil`. |
| `public/css/landing-modern.css` | Modify | 1) Normalkan ukuran `.hero-box` (padding 2rem 2.25rem) dan `.hero-full-image-slide` (tinggi alami proporsional ~320px). 2) Tambahkan style untuk `.brand-logo-img` dan styling kartu jurusan riil (status pill, progress bar, link action). |
| `public/js/landing-interactive.js` | Modify | Sesuaikan interaktivitas Kuota Explorer agar bekerja dinamis dengan elemen kartu jurusan yang dirender Blade dari database (toggle tab jenjang, switch bulan, hitung gauge dinamis). |

---

## 4. Langkah-Langkah Implementasi Bertahap

- [x] **Langkah 1**: Perbaiki ukuran Hero Box dan Hero Image di `landing-modern.css` agar proporsional dan tidak membengkak tinggi.
- [x] **Langkah 2**: Pasang logo resmi Sleman `@include('partials.logo-sleman')` pada header navbar di `landing.blade.php`.
- [x] **Langkah 3**: Rombak section Kuota Explorer di `landing.blade.php` agar menampilkan kartu jurusan riil dari database (SMK: TKJ, RPL; Mahasiswa: SI, MI, Informatika, Tekkom) lengkap dengan sisa kuota, status waiting list, dan link register terintegrasi.
- [x] **Langkah 4**: Sinkronkan logika JS di `landing-interactive.js` untuk navigasi tab jenjang, filter bulan, dan update gauge dari data DOM riil.
- [x] **Langkah 5**: Bersihkan view cache (`php artisan view:clear`) dan lakukan pengujian fungsional serta visual pada `http://127.0.0.1:8000/`.

---

## 5. Kriteria Selesai (Definition of Done)
1. **Ukuran Hero Pas**: Kotak hero Slide 1 dan Slide Dokumentasi memiliki ketinggian proporsional (kompak, rapi, tidak jangkung berlebih).
2. **Logo Resmi Aktif**: Header menampilkan lambang resmi Diskominfo / Pemkab Sleman secara tajam dan benar.
3. **Data Jurusan Riil**: Menampilkan daftar jurusan riil dari database beserta status kuotanya, bukan dummy bidang fiktif.
4. **Link Pendaftaran Valid**: Tombol pada setiap kartu jurusan mengarah ke halaman registrasi dengan parameter jurusan dan bulan yang sesuai.
5. **Zero Error**: Server dan browser berjalan mulus tanpa error JavaScript maupun Blade.
