# Rencana Kerja: Perbaikan Proporsi Ukuran Hero Slider & Normalisasi Anomali Jarak Antar Elemen Landing Page

**Tanggal**: 9 November 2026  
**Dokumen**: `docs/plans/2026-11-09-plan-perbaikan-ukuran-hero-slider-dan-anomali-jarak-landing-page.md`  
**Status**: Selesai Diimplementasikan & Terverifikasi (Production Ready)  
**Terkait**:
- Hero Section Slider (`#beranda` pada `resources/views/public/blocks/hero.blade.php`, `resources/css/pages/public/landing.css`, `resources/js/pages/public/landing.js`)
- Dynamic Layout Stylesheet (`<style id="wp-site-editor-dynamic-styles">` pada `resources/views/public/landing.blade.php`)
- Ritme Jarak Section Landing Page (`.landing-section`, `#penempatan`, `#tahapan`, `#faq`, `#footer`)

---

## 1. Tujuan
1. **Mengembalikan Ukuran Hero Slider ke Proporsi Kompak Semula ("Bentuk Sebelum nya yang Agak Kecil")**:
   - Menormalkan ukuran kontainer Hero (Slide 1: Informasi Pendaftaran & Slide 2: Dokumentasi Foto Sertifikasi ISO Diskominfo) agar tidak membengkak raksasa.
   - Mengunci lebar maksimum kontainer hero agar selaras dengan grid section utama (`72rem` / `1152px`).
   - Menormalkan tinggi hero slider pada desktop di kisaran proporsional (~`320px - 340px`) sehingga foto Slide 2 tampil kompak, pas, dan tajam tanpa terkesan ditarik paksa.
2. **Menormalkan Anomali Jarak Antar Elemen (Eliminasi Ruang Putih Raksasa / Whitespace Anomali)**:
   - Menghilangkan akumulasi vertikal ganda yang menyebabkan kekosongan putih raksasa antara indikator dots slider hero dengan judul *"Ketersediaan Kuota Magang"*.
   - Menormalkan ritme vertikal antar section di seluruh landing page (Hero, Penempatan/Kuota, Tahapan, FAQ, Footer) agar padat, rapi, dan seimbang.

---

## 2. Analisis Masalah & Root Cause (Berdasarkan Lampiran Gambar Pengguna)

### A. Root Cause Ukuran Membengkak Terlalu Besar (Gambar 1)
1. **Pemaksaan Max-Width 1280px**: Aturan `.hero-slider-wrapper { max-width: var(--wp-container-max-w) !important; }` di `landing.blade.php` memaksakan kontainer melebar hingga `1280px` (melebihi batas alami section `72rem` = `1152px`).
2. **Pemaksaan Font Title 48px**: Aturan `.hero-title { font-size: var(--wp-hero-title-font-size) !important; }` memaksakan font 48px (nilai proporsional aslinya adalah `clamp(1.85rem, 2.75vw, 2.35rem)` ~32px-36px). Teks judul yang raksasa mendorong tinggi kolom 1 menjadi >420px.
3. **Efek Domino Sinkronisasi JavaScript**: Modul `syncHeroHeight()` di `landing.js` mengukur `slide1Box.offsetHeight` lalu memaksakan nilai tersebut ke `imgSlide.style.height`. Karena Slide 1 membengkak raksasa, Slide 2 foto dokumentasi ikut dipaksa membesar setinggi itu.

### B. Root Cause Anomali Jarak Vertikal Raksasa (Gambar 2)
1. **Padding Ganda pada Slider Wrapper**: Aturan `#beranda .hero-slider-wrapper { padding-top: 80px !important; padding-bottom: 80px !important; }` menyisipkan ruang kosong 80px di dalam `#beranda`.
2. **Margin Bottom Berlebih pada Section**: Aturan `.landing-section { margin-bottom: 64px !important; }` ditumpuk di atas padding bawaan section (`padding: 2.5rem 1.25rem;` = 40px atas & 40px bawah).
3. **Akumulasi Vertikal Menumpuk**:
   - `margin-top dots`: 20px
   - `padding-bottom hero wrapper`: 80px
   - `padding-bottom section #beranda`: 40px
   - `margin-bottom section #beranda`: 64px
   - `padding-top section #penempatan`: 40px
   - **Total Ruang Kosong Putih**: **~244px** (ruang kosong raksasa anomali!).

---

## 3. Evaluasi Opsi Solusi

| Parameter | Opsi 1: Pembersihan Selektif Aturan Dinamis & Penegakan Spacing Native di CSS (Rekomendasi) | Opsi 2: Mengubah Nilai Parameter di Database Saja |
| :--- | :--- | :--- |
| **Pendekatan** | Menghapus selektor yang salah sasaran (`#beranda .hero-slider-wrapper { padding: ... }` dan `.landing-section { margin-bottom: ... }`) serta mengembalikan ukuran font & tinggi hero ke nilai proporsional di `landing.css`. | Mengubah isi database `landing_hero_padding_y_px = 0` dan `landing_section_gap_px = 0`. |
| **Ketahanan Layout** | **Sangat Kokoh & Permanen**. Tidak akan rusak lagi jika database di-seed ulang atau jika user mereset pengaturan CMS. | **Rapuh**. Pengaturan dapat ter-reset ke default 80px jika ada proses migrasi atau ekspor-impor data CMS. |
| **Keseimbangan Visual** | Menghasilkan layout kompak persis seperti baseline stabil yang diinginkan pengguna. | Membutuhkan banyak penyesuaian nilai acak di DB tanpa memperbaiki arsitektur styling. |
| **Kepatuhan Aturan** | Sesuai aturan: memusatkan styling CSS di `resources/css/` dan menjaga Blade tetap bersih dari styling agresif. | Nilai tetap di-inject melalui inline `<style>`. |

**Rekomendasi**: Menggunakan **Opsi 1** (disertai pembaruan nilai default fallback pada service/pengaturan agar sinkron).

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/public/landing.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/public/landing.blade.php) | **Modify** | 1) Hapus pemaksaan padding vertikal pada `#beranda .hero-slider-wrapper`. 2) Hapus pemaksaan `margin-bottom: var(--wp-section-gap-y)` pada `.landing-section`. 3) Selaraskan max-width container ke `72rem` (1152px) agar seragam dengan seluruh section. 4) Batasi font-size hero title agar proporsional. |
| [`resources/css/pages/public/landing.css`](file:///d:/MAGKOM/MAGKOM/resources/css/pages/public/landing.css) | **Modify** | 1) Kunci tinggi proporsional `.hero-box` dan `.hero-full-image-slide` di rentang optimal `320px - 340px` (desktop). 2) Pastikan jarak vertikal `.landing-section` bersih dan rapi (`padding: 2rem 1.25rem; margin: 0 auto;`). 3) Rapatkan spasi antara `.hero-slider-dots` dengan batas bawah hero section (`margin-top: 1rem;`). |
| [`resources/js/pages/public/landing.js`](file:///d:/MAGKOM/MAGKOM/resources/js/pages/public/landing.js) | **Modify** | Pastikan `syncHeroHeight()` membatasi tinggi maksimum foto Slide 2 pada angka proporsional (max-height `360px` di desktop) untuk mencegah lonjakan ukuran tak terkendali. |
| [`public/css/landing-modern.css`](file:///d:/MAGKOM/MAGKOM/public/css/landing-modern.css) | **Sync** | Sinkronisasi asset hasil kompilasi Vite untuk lingkungan pengujian. |
| [`public/js/landing-interactive.js`](file:///d:/MAGKOM/MAGKOM/public/js/landing-interactive.js) | **Sync** | Sinkronisasi asset JS interaktif hasil pembaruan. |

---

## 5. Langkah-Langkah Implementasi Bertahap

- [x] **Langkah 1**: Perbaiki blok `<style id="wp-site-editor-dynamic-styles">` di `resources/views/public/landing.blade.php`:
  - Hapus pemaksaan padding vertikal pada wrapper: `#beranda .hero-slider-wrapper { padding-top: ...; padding-bottom: ... }` -> Ganti menjadi `padding: 0 !important;`.
  - Hapus pemaksaan `margin-bottom: var(--wp-section-gap-y)` pada `.landing-section` yang menyebabkan tumpukan jarak berlebih.
  - Setel default `--wp-hero-title-font-size` ke `34px` (sebelumnya `48px`).
  - Setel default `--wp-section-gap-y` ke `0px` atau biarkan padding section alami yang mengaturnya.
- [x] **Langkah 2**: Perbaiki CSS Hero & Layout di `resources/css/pages/public/landing.css`:
  - Rapikan dimensi `.hero-box`: padding desktop `1.75rem 2rem;`, gap `1.5rem;`.
  - Rapikan `.hero-title`: ukuran font `clamp(1.75rem, 2.5vw, 2.25rem)` dengan line-height `1.15`.
  - Rapikan `.hero-full-image-slide`: batasi tinggi alami pada `min-height: 280px; max-height: 350px;` di desktop.
  - Rapikan `.hero-slider-dots`: `margin-top: 0.85rem; margin-bottom: 0;`.
  - Rapikan `.landing-section`: `padding: 2rem 1.25rem; margin: 0 auto;` (jarak antar section menjadi bersih ~64px tanpa kekosongan putih anomali).
- [x] **Langkah 3**: Sinkronisasi Tinggi di JavaScript (`resources/js/pages/public/landing.js`):
  - Pada `syncHeroHeight()`, sesuaikan agar tinggi Slide 2 mengambil tinggi presisi Slide 1 dengan batasan batas atas yang wajar (`Math.min(h, 360)` di desktop) agar kedua slide selalu identik dan kompak.
- [x] **Langkah 4**: Kompilasi Asset & Pengujian:
  - Jalankan `npm run build` melalui Node v22.
  - Jalankan seluruh rangkaian tes feature landing page:
    ```powershell
    & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test tests/Feature/LandingPageTest.php tests/Feature/LandingPageFaqRedesignTest.php tests/Feature/LandingCmsStagingTest.php tests/Feature/LandingCmsVisualBuilderAndThemeTest.php tests/Feature/LandingPage/FinalQualityAssuranceTest.php
    ```
  - Verifikasi formatting dengan Laravel Pint:
    ```powershell
    & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" ./vendor/bin/pint --test
    ```

---

## 6. Strategi Testing & Verifikasi

1. **Automated Feature Tests**:
   - `LandingPageTest`: Memastikan landing page publik tetap merender elemen penting, tombol aksi, dan status kuota secara sempurna.
   - `FinalQualityAssuranceTest`: Memastikan tidak ada regresi pada RBAC, sanitasi input, dan respons rendering.
   - `LandingCmsVisualBuilderAndThemeTest`: Memastikan modul CMS Site Editor tetap berjalan lancar.
2. **Visual & Layout Verification**:
   - Slide 1 dan Slide 2 memiliki tinggi yang pas, seimbang, dan kompak (~320px - 340px di desktop).
   - Saat bergeser dari Slide 1 ke Slide 2, tidak ada lonjakan tinggi (*zero layout shift*).
   - Jarak antara indikator titik (dots) hero ke heading *"Ketersediaan Kuota Magang"* menjadi proporsional (~32px - 40px), menghilangkan celah putih kosong raksasa 244px.
   - Jarak antar section berikutnya (Penempatan -> Tahapan -> FAQ -> Footer) mengalir secara konsisten dan nyaman dipandang.

---

## 7. Kriteria Selesai (Definition of Done)
- [x] Ukuran Hero Slide (Slide 1 dan Slide 2) kembali kompak dan proporsional sesuai bentuk awal yang lebih kecil.
- [x] Celah putih raksasa di bawah Hero Slider telah hilang; jarak ke section Penempatan menjadi proporsional dan rapi.
- [x] Tidak ada layout shift saat transisi antar slide (Slide 1 <-> Slide 2).
- [x] Seluruh automated test suite (47 tests) lulus 100% (hijau).
- [x] Asset Vite terkompilasi bersih tanpa error.
- [x] Kode mematuhi standar PSR-12 dan aturan ketat MAGKOM (tidak ada CSS/JS inline baru di Blade).

