# Rencana Kerja: Penyesuaian Navigasi Header Scroll Anchor & Normalisasi Spacing Atas Hero Banner

**Tanggal**: 9 November 2026  
**Dokumen**: `docs/plans/2026-11-09-plan-penyesuaian-navigasi-header-scroll-dan-spacing-hero.md`  
**Status**: Selesai Dikerjakan (Completed & Verified)  
**Terkait**:
- Header Sticky Navigation (`resources/views/public/landing.blade.php`, `resources/css/pages/public/landing.css`, `resources/js/pages/public/landing.js`)
- Hero Section Top Spacing (`resources/views/public/blocks/hero.blade.php`, `resources/css/pages/public/landing.css`, `app/Services/LandingPage/BlockRegistry.php`, `app/Services/LandingPage/LegacyCmsAdapter.php`)
- Target Grid Section Anchors: `#beranda`, `#penempatan`, `#tahapan`, `#peta-alur`, `#faq`

---

## 1. Tujuan
1. **Navigasi Header Scroll Akurat & Presisi (Grid-grid Langsung Menuju ke Titik yang Tepat)**:
   - Ketika pengunjung mengklik menu di navbar sticky (**Beranda**, **Penempatan**, **Tahapan**, **Peta Alur**, dan **FAQ**), halaman melakukan scroll mulus (*smooth scroll*) langsung menuju ke section target.
   - Posisi pendaratan scroll diperhitungkan secara akurat dengan `scroll-margin-top: 5.5rem` (88px) sehingga judul dan konten section mendarat rapi dan lega di bawah navbar sticky tanpa tertutup atau terpotong.
   - Menghubungkan menu **Peta Alur** secara spesifik ke anchor visual simulator alur interaktif SVG (`#peta-alur` / `#tahapanRoadWrapper`) di dalam section Tahapan.
2. **Normalisasi Jarak Atas Hero Banner (Tidak Mepet Menempel ke Header)**:
   - Menambahkan ruang bernapas (*breathing space*) atas yang proporsional pada `#beranda` (`padding-top: 2.25rem` / 36px di desktop dan `1.5rem` di mobile), sehingga kotak hero tidak lagi menempel mepet ke tepi bawah navbar sticky.
   - Menyelaraskan seluruh ritme padding vertikal antar section di seluruh landing page (Beranda, Penempatan, Tahapan, FAQ, Footer) agar padat, seimbang, dan konsisten.

---

## 2. Analisis Kebutuhan & Masalah Saat Ini

1. **Hero Terlalu Mepet ke Header Sticky**:
   - Pada perbaikan sebelumnya, `padding_y` disetel ke 0px untuk menghilangkan ruang kosong raksasa 80px. Namun hal ini menyebabkan sisi atas kotak hero `.hero-box` langsung menempel mepet ke bawah navbar sticky (tinggi navbar 68px).
   - Dibutuhkan `padding-top: 2.25rem` (36px) khusus pada kontainer `#beranda` agar kotak hero memiliki jarak vertikal yang nyaman dari navbar.
2. **Scroll Offset Navigasi Header Belum Presisi**:
   - Nilai `scroll-margin-top` saat ini adalah `4.5rem` (72px), sangat mepet dengan tinggi header `4.25rem` (68px) dengan sisa hanya 4px.
   - Ketika diklik, judul section mendarat hampir bersentuhan langsung dengan bayangan header.
   - Nilai ideal adalah `5.5rem` (88px), memberikan ruang lapang 20px di atas heading tiap section saat mendarat dari klik navigasi.
3. **Target Menu "Peta Alur"**:
   - Menu header saat ini:
     - `Beranda` -> `#beranda`
     - `Penempatan` -> `#penempatan`
     - `Tahapan` -> `#tahapan`
     - `Peta Alur` -> `#tahapan` (masih mengarah ke target yang sama persis dengan Tahapan)
     - `FAQ` -> `#faq`
   - Agar klik "Peta Alur" memiliki target grid yang spesifik dan langsung tertuju ke simulator alur, kita sematkan anchor `<span id="peta-alur" class="sr-only" aria-hidden="true"></span>` pada elemen simulator alur jalan SVG di sisi kanan tahapan (`#tahapanRoadWrapper`), dan arahkan URL `Peta Alur` ke `#peta-alur`.

---

## 3. Evaluasi Opsi Pendekatan Teknis

| Parameter | Opsi 1: Unified CSS Scroll Offset & JS Smooth Navigation (Rekomendasi) | Opsi 2: Mengubah Margin Inline Tiap Section di Blade |
| :--- | :--- | :--- |
| **Pendekatan** | Memusatkan offset scroll di `resources/css/pages/public/landing.css` dengan `scroll-margin-top: 5.5rem;`, menyesuaikan padding `#beranda` ke 36px, dan menambahkan smooth handler ringan di `landing.js`. | Menambahkan inline style margin dan padding pada tag `<section>` di masing-masing file view blade block. |
| **Presisi Scroll** | **Sangat Akurat di Semua Browser**. Memperhitungkan tinggi navbar secara dinamis dan memberikan easing mulus. | Bergantung pada browser rendering dan rentan terpotong di browser berbasis WebKit/Safari mobile. |
| **Kerapian Kode** | Bersih dan modular sesuai aturan MAGKOM (styling di `resources/css/`, interaksi di `resources/js/`). | Mengotori file Blade dengan inline styles yang berpotensi melanggar pedoman repo. |

**Rekomendasi**: Memilih **Opsi 1**.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/css/pages/public/landing.css`](file:///d:/MAGKOM/MAGKOM/resources/css/pages/public/landing.css) | **Modify** | 1) Berikan jarak atas pada `#beranda` (`padding-top: 2.25rem; padding-bottom: 1.25rem;`). 2) Tingkatkan `scroll-margin-top` seluruh section dan anchor menjadi `5.5rem` (88px). 3) Tambahkan styling khusus untuk anchor `#peta-alur`. |
| [`resources/views/public/blocks/tahapan.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/public/blocks/tahapan.blade.php) | **Modify** | Tambahkan penanda anchor `<span id="peta-alur" class="sr-only" aria-hidden="true"></span>` pada pembungkus simulator visual alur jalan SVG (`#tahapanRoadWrapper`). |
| [`resources/views/public/landing.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/public/landing.blade.php) | **Modify** | 1) Perbarui `$defaultHeaderNav` untuk `Peta Alur` menjadi `['label' => 'Peta Alur', 'url' => '#peta-alur']`. 2) Pastikan scoped token dinamis tidak menimpa padding atas hero. |
| [`app/Http/Controllers/SuperAdmin/LandingCmsController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/SuperAdmin/LandingCmsController.php) | **Modify** | Selaraskan `$defaultHeaderNav` untuk `Peta Alur` menjadi `#peta-alur`. |
| [`app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php) | **Modify** | Selaraskan `$defaultHeaderNav` untuk `Peta Alur` menjadi `#peta-alur`. |
| [`resources/js/pages/public/landing.js`](file:///d:/MAGKOM/MAGKOM/resources/js/pages/public/landing.js) | **Modify** | Tambahkan modul `initHeaderScrollNav()` yang menangani klik anchor header secara mulus, memperhitungkan offset navbar sticky, dan mengaktifkan kelas active navigasi. |
| [`public/css/landing-modern.css`](file:///d:/MAGKOM/MAGKOM/public/css/landing-modern.css) | **Sync** | Sinkronisasi asset stylesheet hasil kompilasi. |
| [`public/js/landing-interactive.js`](file:///d:/MAGKOM/MAGKOM/public/js/landing-interactive.js) | **Sync** | Sinkronisasi asset JS interaktif hasil kompilasi. |

---

## 5. Langkah-Langkah Implementasi Bertahap

- [x] **Langkah 1**: Perbarui CSS Spacing & Scroll Offset di `resources/css/pages/public/landing.css`:
  - Ubah aturan `.landing-section`: `scroll-margin-top: 5.5rem; padding: 2rem 1.25rem; margin: 0 auto;`.
  - Berikan aturan khusus untuk `#beranda`: `padding-top: 2.25rem; padding-bottom: 1.25rem;` (desktop) dan `@media (max-width: 768px) { #beranda { padding-top: 1.5rem; } }`.
  - Tambahkan `#peta-alur` dengan `scroll-margin-top: 5.5rem;`.
- [x] **Langkah 2**: Perbarui Markup Anchor di Blade:
  - Di `resources/views/public/blocks/tahapan.blade.php`, letakkan `<span id="peta-alur" class="sr-only" aria-hidden="true"></span>` tepat di atas `#tahapanRoadWrapper`.
  - Di `resources/views/public/landing.blade.php`, ubah default link navbar `Peta Alur` dari `#tahapan` ke `#peta-alur`.
  - Di `LandingCmsController.php` & `LandingCmsSiteEditorController.php`, ubah item navbar default `Peta Alur` ke `#peta-alur`.
- [x] **Langkah 3**: Tambahkan Handler Smooth Scroll di JavaScript (`resources/js/pages/public/landing.js`):
  - Buat fungsi `initHeaderScrollNav()` yang mengintersepsi klik pada `.landing-nav-link[href^="#"]`.
  - Hitung offset presisi (`targetElement.getBoundingClientRect().top + window.scrollY - 88`).
  - Jalankan `window.scrollTo({ top: targetOffset, behavior: 'smooth' })`.
  - Panggil `initHeaderScrollNav()` pada `DOMContentLoaded`.
- [x] **Langkah 4**: Build Aset & Pengujian:
  - Jalankan `npm run build`.
  - Jalankan test suite landing page:
    ```powershell
    & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test tests/Feature/LandingPageTest.php tests/Feature/LandingPageFaqRedesignTest.php tests/Feature/LandingCmsStagingTest.php tests/Feature/LandingCmsVisualBuilderAndThemeTest.php tests/Feature/LandingPage/FinalQualityAssuranceTest.php tests/Feature/LandingPage/InspectorDesignSystemTest.php
    ```
  - Verifikasi formatting dengan Laravel Pint:
    ```powershell
    & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" ./vendor/bin/pint --test
    ```

---

## 6. Strategi Testing & Verifikasi

1. **Automated Feature Tests**:
   - `LandingPageTest`: Memastikan seluruh anchor `#beranda`, `#penempatan`, `#tahapan`, `#peta-alur`, `#faq` ada dan rute publik dapat diakses 100%.
   - `FinalQualityAssuranceTest`: Memastikan tidak ada regresi pada RBAC dan rendering publik.
2. **Verifikasi Interaksi Visual**:
   - Kotak Hero memiliki jarak bernapas yang lega (~36px) di bawah navbar, tidak lagi menempel mepet ke atas.
   - Klik **Beranda** -> scroll ke atas dengan jarak pas.
   - Klik **Penempatan** -> scroll ke judul Kuota & Penempatan, tidak tertutup navbar.
   - Klik **Tahapan** -> scroll ke judul Tahapan Pendaftaran di sisi kiri.
   - Klik **Peta Alur** -> scroll langsung ke simulator jalan/mobil interaktif di sisi kanan.
   - Klik **FAQ** -> scroll ke judul FAQ pertanyaan yang sering ditanyakan.

---

## 7. Kriteria Selesai (Definition of Done)
- [x] Tampilan Hero Banner tidak lagi mepet ke atas (memiliki jarak yang proporsional dan nyaman dari navbar sticky).
- [x] Navigasi header (Beranda, Penempatan, Tahapan, Peta Alur, FAQ) berfungsi mulus dan mendarat tepat pada grid masing-masing tanpa tertutup navbar.
- [x] Menu Peta Alur langsung menuju ke visual simulator alur interaktif.
- [x] Seluruh automated test suite (47 tests) lulus 100% tanpa regresi.
- [x] Asset Vite terkompilasi bersih tanpa error.

