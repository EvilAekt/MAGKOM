# Rencana Kerja: Standardisasi Tipografi & Bobot Font (Font Weight) MagKom

**Tanggal**: 16 Oktober 2026  
**Status**: Menunggu Persetujuan Pengguna  
**Dokumen Terkait**: [07-DESIGN-SYSTEM-MAGKOM.md](file:///d:/laragon/www/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md)

---

## 1. Tujuan

Menyelaraskan dan menstandarisasi bobot font (*font-weight*) di seluruh aplikasi MagKom sesuai dengan hierarki visual yang diminta:
1. **Header & Judul**: Menggunakan **SemiBold (600)** atau **Bold (700)** agar tegas, jelas, dan memiliki kontras hierarki yang kuat sebagai judul.
2. **Footer**:
   - **Judul Kolom Footer**: Menggunakan **Medium (500)** agar terstruktur rapi dan tidak terlalu mendominasi.
   - **Teks Biasa / Link Footer**: Menggunakan **Regular (400)**.
3. **Teks Seluruh Web (Body)**: Menggunakan **Regular (400)** untuk kenyamanan membaca optimal (*readability*), termasuk paragraf, deskripsi, tabel isi, dan teks informasi.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
1. **Keluarga Huruf Utama**: Tetap menggunakan **Open Sans** sesuai Design System resmi MagKom Bab 2.2, dengan cadangan font sistem (*system fallback*).
2. **Pemuatan Aset Font Google**: Bobot font `500` (Medium) belum termuat di sebagian layout (`app.blade.php`, `dashboard.blade.php`, `auth.blade.php`). Kita perlu memperbarui URL Google Fonts untuk menyertakan `wght@0,400;0,500;0,600;0,700;0,800`.
3. **Sub-komponen Form & Tombol**: Label form dan tombol (*buttons/pills*) tetap mempertahankan bobot fungsionalnya (SemiBold 600) untuk kejelasan interaksi (*affordance*), sementara placeholder dan input teks menggunakan Regular (400).
4. **Hierarki Judul Header**:
   - H1 / Hero Title / Page Title: **Bold (700)**
   - H2 / Section Heading: **Bold (700)**
   - H3 / Card Title / Modal Header: **SemiBold (600)**
   - H4, H5, H6 / Sub-section: **SemiBold (600)**
   - Brand Logo Text: **Bold (700)**
   - Header Nav Links: **SemiBold (600)**

---

## 3. Pendekatan Teknis & Evaluasi Alternatif

### Opsi A: Standarisasi Terpusat via CSS Custom Properties / Tokens (Direkomendasikan)
- **Deskripsi**: Menambahkan token bobot font di `:root` CSS di semua master layout:
  ```css
  --fw-regular: 400;
  --fw-medium: 500;
  --fw-semibold: 600;
  --fw-bold: 700;
  ```
  Kemudian menetapkan aturan dasar (*base rules*) global:
  ```css
  body, p, span, td, li { font-weight: var(--fw-regular); }
  h1, h2 { font-weight: var(--fw-bold); }
  h3, h4, h5, h6 { font-weight: var(--fw-semibold); }
  .footer-col-title { font-weight: var(--fw-medium); }
  .footer-links a, .footer-address { font-weight: var(--fw-regular); }
  ```
- **Kelebihan**:
  - *Single Source of Truth*: Perubahan di masa depan cukup dilakukan di satu tempat token.
  - Sangat konsisten di seluruh halaman publik, auth, dan dashboard.
  - Sesuai dengan spesifikasi Design System MagKom (`07-DESIGN-SYSTEM-MAGKOM.md`).
- **Kekurangan**: Perlu audit teliti pada master layout agar tidak terjadi konflik spesifisitas CSS.

### Opsi B: Penyesuaian Ad-Hoc di Masing-Masing File Blade View
- **Deskripsi**: Mengubah inline style atau class utilitas satu per satu di setiap view komponen.
- **Kelebihan**: Cepat untuk komponen terisolasi.
- **Kekurangan**: Risiko inkonsistensi sangat tinggi, duplikasi kode besar, dan sulit dipertahankan dalam jangka panjang.

> **Keputusan**: Menggunakan **Opsi A** untuk kebersihan arsitektur, kepatuhan Design System, dan kemudahan pemeliharaan.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/layouts/app.blade.php` | Modify | 1. Tambah weight 500 pada link Google Fonts.<br>2. Tambah token `--fw-*` di `:root`.<br>3. Standarisasi heading (Bold 700 / SemiBold 600), body (Regular 400), footer col title (Medium 500), footer text (Regular 400). |
| `resources/views/layouts/dashboard.blade.php` | Modify | 1. Tambah weight 500 pada link Google Fonts.<br>2. Tambah token `--fw-*` di `:root`.<br>3. Pastikan body, konten tabel, dan modal deskripsi menggunakan Regular 400; judul halaman dan kartu menggunakan Bold/SemiBold. |
| `resources/views/layouts/peserta.blade.php` | Modify | 1. Tambah token `--fw-*` di `:root`.<br>2. Sinkronkan heading, body text, dan footer/bottom bar ke bobot standar. |
| `resources/views/layouts/auth.blade.php` | Modify | 1. Tambah weight 500 pada link Google Fonts.<br>2. Tambah token `--fw-*` di `:root`.<br>3. Standarisasi judul card auth (Bold 700 / SemiBold 600), helper text & body (Regular 400). |
| `resources/views/public/landing.blade.php` | Modify | Pastikan styling lokal tidak meng-override hierarki: hero title (Bold 700), section titles (Bold 700 / SemiBold 600), deskripsi dan teks biasa (Regular 400). |
| `tests/Feature/TypographyDesignSystemTest.php` | Create | Feature test untuk memverifikasi bahwa link Google Fonts memuat weight 400, 500, 600, 700 dan token CSS terdefinisi di layout utama. |

---

## 5. Langkah Implementasi

### Tahap 1: Pembaharuan Asset Font Google (Google Fonts Preconnect & Weights)
- [x] 1.1 Perbarui link Google Fonts di [layouts/app.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/layouts/app.blade.php) menjadi:
  `family=Open+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap`
- [x] 1.2 Perbarui link Google Fonts di [layouts/dashboard.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/layouts/dashboard.blade.php) menyertakan weight 500.
- [x] 1.3 Perbarui link Google Fonts di [layouts/auth.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/layouts/auth.blade.php) menyertakan weight 500.
- [x] 1.4 Verifikasi link Google Fonts di [layouts/peserta.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/layouts/peserta.blade.php) sudah memuat weight 400, 500, 600, 700.

### Tahap 2: Definisi Token CSS Bobot Tipografi
- [x] 2.1 Tambahkan token CSS bobot font pada blok `:root` di [layouts/app.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/layouts/app.blade.php):
  - `--fw-regular: 400;`
  - `--fw-medium: 500;`
  - `--fw-semibold: 600;`
  - `--fw-bold: 700;`
- [x] 2.2 Sinkronkan token tersebut ke [layouts/dashboard.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/layouts/dashboard.blade.php), [layouts/peserta.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/layouts/peserta.blade.php), dan [layouts/auth.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/layouts/auth.blade.php).

### Tahap 3: Implementasi Aturan Footer & Header
- [x] 3.1 Ubah selector `.footer-col-title` pada [layouts/app.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/layouts/app.blade.php):
  - Ubah `font-weight: 700;` menjadi `font-weight: var(--fw-medium); /* 500 */`
- [x] 3.2 Pastikan link footer (`.footer-links a`) dan teks alamat (`.footer-address`) secara eksplisit diatur ke `font-weight: var(--fw-regular); /* 400 */`.
- [x] 3.3 Pastikan elemen header navbar (`.brand-text h1`, `.header-title`) diatur ke `font-weight: var(--fw-bold); /* 700 */` dan link menu navigasi (`.nav-link`) ke `font-weight: var(--fw-semibold); /* 600 */`.

### Tahap 4: Implementasi Aturan Body & Headings Global
- [x] 4.1 Tetapkan aturan base typography di `layouts/app.blade.php`:
  ```css
  body {
      font-weight: var(--fw-regular); /* 400 */
  }
  h1, h2 {
      font-weight: var(--fw-bold); /* 700 */
  }
  h3, h4, h5, h6 {
      font-weight: var(--fw-semibold); /* 600 */
  }
  p, span.text-body, td, li {
      font-weight: var(--fw-regular); /* 400 */
  }
  ```
- [x] 4.2 Tinjau [public/landing.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/public/landing.blade.php) untuk memastikan `.hero-title` (700 Bold), `.section-heading-center h2` (700 Bold), sub-heading (600 SemiBold), dan teks penjelasan hero/kartu (400 Regular) berjalan selaras.
- [x] 4.3 Terapkan aturan base heading & body yang sama pada [layouts/dashboard.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/layouts/dashboard.blade.php) dan [layouts/peserta.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/layouts/peserta.blade.php).

### Tahap 5: Testing & Validasi
- [x] 5.1 Buat unit/feature test `tests/Feature/TypographyDesignSystemTest.php` untuk memastikan master layouts merender font weight yang tepat dan token CSS yang lengkap.
- [x] 5.2 Jalankan suite pengujian: `php artisan test --filter=TypographyDesignSystemTest`.
- [x] 5.3 Lakukan visual audit melalui inspeksi elemen browser untuk memastikan font weight 400, 500, 600, dan 700 ter-render secara native (bukan synthetic faux-bold).

---

## 6. Strategi Testing

1. **Automated Feature Test**:
   - `test_master_layouts_import_open_sans_with_medium_weight_500()`: Memastikan respons HTTP layout utama memuat query Google Fonts yang menyertakan `500`. (Passed)
   - `test_typography_tokens_defined_in_root()`: Memastikan token `--fw-*` lengkap di `:root`. (Passed)
   - `test_footer_typography_rules_match_spec()`: Memastikan CSS footer mendefinisikan font-weight 500 untuk title kolom dan 400 untuk link/teks. (Passed)
   - `test_header_and_body_typography_rules_match_spec()`: Memastikan body rule default adalah 400 dan headings adalah Bold/SemiBold. (Passed)
2. **Regression Testing**:
   - Menjalankan seluruh test feature yang sudah ada (`LandingPageTest`, dll) untuk memastikan tidak ada UI test atau assertion teks yang rusak. (Passed)

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| Font Weight 500 tidak di-render jika koneksi Google Fonts offline/lambat | Sedang | Tetap sediakan fallbacks font sistem (`Arial, -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, sans-serif`) di token `--font-sans`. |
| Penurunan kontras visual jika judul kolom footer terlalu tipis | Rendah | Menggunakan warna kontras tinggi (`#38bdf8` atau `#FFFFFF`) pada latar gelap `#111827` sehingga bobot Medium (500) tetap sangat jelas terbaca dan memenuhi standar aksesibilitas WCAG 2.1 AA. |
| Konflik spesifisitas CSS pada class Tailwind / utility | Rendah | Menggunakan token CSS `:root` dan base selector tanpa menggunakan `!important`, serta memastikan utility class spesifik tetap dihormati jika diperlukan. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Google Fonts link di seluruh master layout memuat bobot 400, 500, 600, 700.
- [x] Header dan seluruh judul utama (`h1`, `h2`, hero, section headers) berbobot **Bold (700)** atau **SemiBold (600)**.
- [x] Judul kolom footer berbobot **Medium (500)**.
- [x] Teks biasa / navigasi link pada footer berbobot **Regular (400)**.
- [x] Teks isi di seluruh halaman web (body, paragraf, deskripsi, tabel isi) default ke **Regular (400)**.
- [x] Pengujian otomatis via `php artisan test` berhasil lulus (100% Green).
