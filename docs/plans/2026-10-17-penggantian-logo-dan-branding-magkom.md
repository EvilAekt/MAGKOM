# Rencana Kerja: Penggantian Logo Menyeluruh dan Pembaruan Branding "Magkom Kominfo Magang"

**Tanggal**: 17 Oktober 2026  
**Status**: Selesai Diimplementasikan & Lolos Uji  
**Branch Dasar**: `test-1`  

---

## 1. Tujuan
Memperbarui seluruh identitas visual dan branding pada aplikasi MAGKOM dengan:
1. Mengintegrasikan **Logo Resmi MagKom** (logo dual-tone "magkom" + "Magang Kominfo") dan **Lambang Resmi Kabupaten Sleman** sebagai aset grafis terstandarisasi di seluruh portal web.
2. Memperbarui penamaan sistem yang sebelumnya bernama **"Magkom Kominfo portal"** / **"KOMINFO PORTAL"** menjadi **"Magkom Kominfo Magang"** (atau sub-judul **"KOMINFO MAGANG"**) secara menyeluruh dan konsisten di seluruh layout publik, portal peserta, portal dashboard internal (admin/superadmin/pembimbing), dokumen PDF resmi, serta meta tag SEO.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Penyimpanan Aset**: Dua gambar yang disediakan oleh user (`media_1792205375057.jpg` untuk lambang Pemkab Sleman dan `media_1792205615370.jpg` untuk logo MagKom) disimpan ke direktori `public/images/`:
   - `public/images/logo-sleman.png`: Lambang Kabupaten Sleman.
   - `public/images/logo-magkom.png`: Logo brand MagKom resmi.
   - `public/favicon.ico` dan `public/favicon.png`: Favicon browser menggunakan lambang Sleman.
2. **Komposisi Tampilan**:
   - **Navbar Publik (`layouts/app.blade.php`)**: Menampilkan lambang Sleman dan teks brand `MagKom` dengan sub-teks `KOMINFO MAGANG`.
   - **Sidebar Internal (`layouts/dashboard.blade.php`)**: Menampilkan lambang Sleman pada header sidebar, dengan judul `MagKom` dan subtitle `KOMINFO MAGANG`.
   - **Sidebar Peserta (`layouts/peserta.blade.php`)**: Menampilkan lambang Sleman, judul `MagKom`, dan subtitle `Kominfo Magang`.
   - **Halaman Autentikasi (`layouts/auth.blade.php`)**: Menampilkan lambang resmi Sleman di panel samping visual dan header mobile form.
   - **Dokumen PDF (`resources/views/pdf/`)**: Memperbarui kop surat dan watermark validasi dari "Portal MagKom" menjadi "Magkom Kominfo Magang".
3. **Branch & Git**: Seluruh pekerjaan dilakukan di branch `test-1` tanpa menyentuh branch `main`.

---

## 3. Pendekatan Teknis (Evaluasi Opsi)

| Aspek | Opsi A: Hybrid (Gambar Vektor/PNG + Tipografi Semantik) [Direkomendasikan] | Opsi B: Gambar Statis Murni (Hanya Tag `<img>` Penuh) |
| :--- | :--- | :--- |
| **Penerapan** | Logo gambar lambang/emblem dipadukan dengan teks HTML semantik (`<h1>` / `<span>`) berbobot font standar MagKom. | Seluruh teks "MagKom Kominfo Magang" dijadikan satu gambar flat tanpa teks HTML. |
| **Kelebihan** | Sangat tajam di semua DPI layar, teks tetap bisa di-indeks oleh mesin pencari (SEO), ramah aksesibilitas (pembaca layar), mudah diatur ukurannya via CSS. | Cepat dibuat tanpa pengaturan tipografi CSS. |
| **Kekurangan** | Memerlukan penyesuaian markup pada 4 layout utama. | Teks gambar bisa terlihat pecah/buram di layar retina/mobile, buruk untuk SEO & aksesibilitas. |
| **Keputusan** | **Dipilih Opsi A (Hybrid)** untuk menjaga standar estetika premium dan kepatuhan aksesibilitas web pemerintah. | Ditolak karena kualitas visual berisiko buram pada layar resolusi tinggi. |

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `public/images/logo-sleman.png` | `Create` | Menyimpan aset lambang resmi Pemkab Sleman untuk header, kop surat, dan favicon. |
| `public/images/logo-magkom.png` | `Create` | Menyimpan aset logo MagKom ("magkom - Magang Kominfo") untuk brand utama. |
| `public/favicon.ico` | `Modify` | Memperbarui favicon kosong (0 byte) menjadi favicon resmi beresolusi tajam. |
| `resources/views/layouts/app.blade.php` | `Modify` | Mengganti icon 'M' dan teks 'KOMINFO PORTAL' menjadi logo Sleman dan teks 'KOMINFO MAGANG', serta meta title. |
| `resources/views/layouts/dashboard.blade.php` | `Modify` | Mengganti icon sidebar header dan teks 'KOMINFO PORTAL' menjadi 'KOMINFO MAGANG', serta meta title dan favicon link. |
| `resources/views/layouts/peserta.blade.php` | `Modify` | Mengganti icon 'M' dan subtitle menjadi 'Kominfo Magang', serta meta title dan favicon link. |
| `resources/views/layouts/auth.blade.php` | `Modify` | Mengganti visual header SVG dan mobile brand di panel login/register dengan lambang Sleman dan teks 'Magkom Kominfo Magang'. |
| `resources/views/public/landing.blade.php` | `Modify` | Menyesuaikan title tag dan referensi teks nama portal ke Magkom Kominfo Magang. |
| `resources/views/public/kebijakan-privasi.blade.php` | `Modify` | Menyesuaikan judul dan teks brand dari 'Portal MagKom' ke 'Magkom Kominfo Magang'. |
| `resources/views/public/verifikasi-dokumen.blade.php` | `Modify` | Menyesuaikan badge verifikasi ke 'Magkom Kominfo Magang'. |
| `resources/views/pdf/sertifikat-magang.blade.php` | `Modify` | Memperbarui footer/kop surat identitas portal cetak. |
| `resources/views/pdf/sample-document.blade.php` | `Modify` | Memperbarui stempel/badge verifikasi cetak. |
| `resources/views/pdf/lembar-nilai.blade.php` | `Modify` | Memperbarui teks QR code portal ke 'Magkom Kominfo Magang'. |
| `tests/Feature/BrandingDesignSystemTest.php` | `Create` | Feature test otomatis untuk memastikan logo dan teks branding tampil benar di semua portal. |

---

## 5. Langkah Implementasi

### Tahap 1: Persiapan & Penempatan Aset Gambar
- [x] Salin dan siapkan aset `logo-sleman.png` dari gambar masukan user ke `public/images/logo-sleman.png`.
- [x] Salin dan siapkan aset `logo-magkom.png` dari gambar masukan user ke `public/images/logo-magkom.png`.
- [x] Buat dan perbarui `public/favicon.ico` (serta `favicon.png`) agar tab browser menampilkan favicon resmi Sleman/MagKom.

### Tahap 2: Pembaruan Layout Utama (Blade Views)
- [x] **Navbar Publik (`resources/views/layouts/app.blade.php`)**:
  - Ganti elemen `.brand-logo-icon` dengan lambang Sleman.
  - Ubah teks subtitle `<p>KOMINFO PORTAL</p>` menjadi `<p>KOMINFO MAGANG</p>`.
  - Pasang link tag `<link rel="icon">` yang mengarah ke favicon baru.
  - Perbarui `@yield('title')` default ke: `"Magkom Kominfo Magang - Informasi Kuota & Pendaftaran"`.
- [x] **Sidebar Dashboard Internal (`resources/views/layouts/dashboard.blade.php`)**:
  - Ganti icon `.sidebar-brand-emblem` dengan lambang Sleman.
  - Ubah `<span class="sidebar-brand-subtitle">KOMINFO PORTAL</span>` menjadi `KOMINFO MAGANG`.
  - Ubah title default dan deskripsi meta ke `Magkom Kominfo Magang`.
- [x] **Sidebar Portal Peserta (`resources/views/layouts/peserta.blade.php`)**:
  - Ganti `.brand-icon` dengan lambang Sleman.
  - Ubah teks `Portal Peserta` / `KOMINFO PORTAL` menjadi `Kominfo Magang`.
  - Pasang link favicon baru dan perbarui title tag.
- [x] **Halaman Autentikasi (`resources/views/layouts/auth.blade.php`)**:
  - Ganti SVG placeholder kotak `M` di sidebar kiri dengan lambang resmi Sleman & logo MagKom.
  - Ubah mobile brand header dan footer teks hak cipta ke: `"Magkom Kominfo Magang • Dinas Komunikasi dan Informatika Kabupaten Sleman"`.

### Tahap 3: Pembaruan Teks Halaman Terkait & Dokumen PDF
- [x] Perbarui `resources/views/public/landing.blade.php` (title tag dan hero text).
- [x] Perbarui `resources/views/public/kebijakan-privasi.blade.php` dan `resources/views/public/verifikasi-dokumen.blade.php`.
- [x] Perbarui dokumen PDF (`sertifikat-magang.blade.php`, `sample-document.blade.php`, `lembar-nilai.blade.php`) pada teks footer validasi ke `Magkom Kominfo Magang`.

### Tahap 4: Pengujian & Validasi
- [x] Buat file test baru `tests/Feature/BrandingDesignSystemTest.php` untuk memvalidasi:
  - Halaman landing, login, register, dashboard, dan portal peserta merespons HTTP 200.
  - Tidak ada teks usang `"KOMINFO PORTAL"`.
  - Teks `"KOMINFO MAGANG"` atau `"Magkom Kominfo Magang"` ada di halaman terkait.
  - Aset file `logo-sleman.png` dan `logo-magkom.png` dapat diakses langsung via HTTP.
- [x] Jalankan pengujian otomatis: `php artisan test --filter=BrandingDesignSystemTest`.
- [x] Verifikasi layout view dan styling responsif.

---

## 6. Strategi Testing

1. **Automated Feature Testing**:
   - `tests/Feature/BrandingDesignSystemTest.php`:
     - Test 1: `test_landing_page_displays_new_logo_and_magkom_kominfo_magang_branding` (PASS)
     - Test 2: `test_auth_pages_display_new_branding_and_logo` (PASS)
     - Test 3: `test_internal_dashboard_sidebar_displays_kominfo_magang_subtitle` (PASS)
     - Test 4: `test_peserta_portal_sidebar_displays_kominfo_magang_subtitle` (PASS)
     - Test 5: `test_brand_assets_exist_in_public_directory` (PASS)
2. **Regression Testing**:
   - `tests/Feature/TypographyDesignSystemTest.php`: 4 passed (PASS).
3. **Visual Verification**:
   - Header, sidebar, auth visual panel, dan footer terstandarisasi rapi dan responsif.

---

## 7. Risiko & Mitigasi

| Risiko | Tingkat | Mitigasi |
| :--- | :--- | :--- |
| **Aset Gambar Tidak Muncul (Broken Image)** | Rendah | Menggunakan helper `asset('images/...')` standar Laravel dan menyertakan `alt` tag semantik lengkap. |
| **Distorsi Aspek Rasio Logo pada Mobile** | Sedang | Menerapkan `height: auto; max-height: 40px; object-fit: contain;` pada CSS styling agar proporsional di segala ukuran layar. |
| **Regresi di Branch `main`** | Kritis | Seluruh pengerjaan dan validasi dilakukan secara eksklusif di branch `test-1`, tanpa push ke `main`. |

---

## 8. Kriteria Selesai (Definition of Done)
- [x] File gambar `logo-sleman.png` dan `logo-magkom.png` tersedia di `public/images/`.
- [x] Favicon resmi aktif di semua tab browser aplikasi.
- [x] Header navbar publik menampilkan logo baru dan teks "KOMINFO MAGANG".
- [x] Sidebar internal SuperAdmin, Admin, dan Pembimbing menampilkan logo baru dan subtitle "KOMINFO MAGANG".
- [x] Sidebar portal Peserta menampilkan logo baru dan subtitle "Kominfo Magang".
- [x] Halaman login/register autentikasi memuat logo resmi Sleman & MagKom.
- [x] Tidak ada lagi kemunculan teks usang "KOMINFO PORTAL".
- [x] Seluruh test di `php artisan test --filter=BrandingDesignSystemTest` lulus 100%.
- [x] Branch tetap berada di `test-1` tanpa commit/push tanpa persetujuan pengguna.
