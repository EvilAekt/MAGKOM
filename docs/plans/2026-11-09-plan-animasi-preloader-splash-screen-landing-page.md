# Plan: Animasi Preloader / Splash Screen Logo Sleman Landing Page MAGKOM

Dokumen ini merinci rencana implementasi animasi *preloader* / *splash screen* transisi satu kali saat halaman landing page dibuka atau di-refresh, menghadirkan identitas resmi Pemkab Sleman & Diskominfo secara elegan, modern, dan berbobot.

---

## 1. Latar Belakang & Kebutuhan

- **Kebutuhan Pengguna**:
  Pengguna meminta agar landing page utama memiliki animasi pembuka saat web di-reload atau di-refresh (cukup berjalan satu kali per refresh), terinspirasi dari portal resmi Diskominfo Sleman (`https://kominfo.slemankab.go.id/`).
- **Pilihan yang Disetujui Pengguna**:
  **Preloader / Splash Screen Logo Sleman** — layar transisi anggun dengan logo Pemkab Sleman & MAGKOM yang melakukan fade-out mulus begitu seluruh aset halaman selesai dimuat.

---

## 2. Pendekatan Desain & Arsitektur

### 2.1 Spesifikasi Visual & Komponen
1. **Overlay Penuh (`.site-preloader`)**:
   - Posisi tetap (*fixed full-screen* `inset: 0`) dengan `z-index: 999999`.
   - Latar belakang bersih elegan: putih halus bertransisi lembut (`#ffffff` / `rgba(255, 255, 255, 0.98)`).
   - Efek penutupan (*dismiss*): Transisi `opacity 0.5s cubic-bezier(0.16, 1, 0.3, 1)` dan `visibility 0.5s ease`, diikuti pelepasan elemen dari interaksi pointer (`pointer-events: none`).
2. **Identitas Resmi Sleman (`.preloader-content`)**:
   - Logo resmi Kabupaten Sleman (`partials/logo-sleman.blade.php`) berukuran 72px dengan bayangan lembut dan animasi denyut halus (*gentle pulse*).
   - Tipografi resmi: Judul **MAGKOM** dalam warna navy `#1B3860` dengan subjudul *"Dinas Komunikasi dan Informatika Sleman"*.
   - Progress bar tipis (3px) dengan gradien navy-emas khas Sleman yang terisi anggun (0% ke 100%).
3. **Aksesibilitas (a11y)**:
   - Dukungan penuh `prefers-reduced-motion`: Jika pengguna mengaktifkan mode minim gerak pada sistem operasi, preloader langsung ditutup tanpa animasi berlebih.

### 2.2 Arsitektur Kode (Mematuhi `AGENTS.md`)
- **Blade**: Lapisan markup semantik minimal kontainer preloader di [`resources/views/public/landing.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/public/landing.blade.php). Tanpa inline style dan tanpa inline script.
- **CSS**: Seluruh styling dan animasi `@keyframes` berada di [`resources/css/pages/public/landing.css`](file:///d:/MAGKOM/MAGKOM/resources/css/pages/public/landing.css).
- **JavaScript**: Controller inisialisasi preloader berada di [`resources/js/pages/public/landing.js`](file:///d:/MAGKOM/MAGKOM/resources/js/pages/public/landing.js).
- **Asset Pipeline**: Dikompilasi via Vite (`npm run build`) dan disinkronkan ke `public/css/` & `public/js/`.

---

## 3. Ruang Lingkup Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`resources/views/public/landing.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/public/landing.blade.php) | **Modify** | Menyisipkan kontainer semantik preloader `#sitePreloader` di awal body. |
| [`resources/css/pages/public/landing.css`](file:///d:/MAGKOM/MAGKOM/resources/css/pages/public/landing.css) | **Modify** | Menambahkan styling `.site-preloader`, logo wrap, teks identitas, progress line, dan animasi fade-out. |
| [`resources/js/pages/public/landing.js`](file:///d:/MAGKOM/MAGKOM/resources/js/pages/public/landing.js) | **Modify** | Menambahkan fungsi `initSitePreloader()` untuk menangani dismissal mulus saat load selesai. |
| [`public/css/landing-modern.css`](file:///d:/MAGKOM/MAGKOM/public/css/landing-modern.css) & [`public/js/landing-interactive.js`](file:///d:/MAGKOM/MAGKOM/public/js/landing-interactive.js) | **Sync** | Sinkronisasi asset statis publik. |

---

## 4. Langkah-Langkah Implementasi Bertahap

- [x] **Langkah 1**: Tambahkan markup preloader di `resources/views/public/landing.blade.php`:
  - Kontainer `#sitePreloader` dengan logo Sleman, nama brand, dan bar loading.
- [x] **Langkah 2**: Tambahkan styling di `resources/css/pages/public/landing.css`:
  - Atur layout centering, z-index tertinggi, animasi pulse, dan kelas penutup `.is-loaded`.
  - Tambahkan media query `@media (prefers-reduced-motion: reduce)`.
- [x] **Langkah 3**: Tambahkan interaktivitas di `resources/js/pages/public/landing.js`:
  - Tambahkan fungsi `initSitePreloader()` yang mendengarkan event `load` dan timer minimal (~650ms) agar transisi terasa alami dan tidak berkedip.
  - Tambahkan fallback safety timeout (maksimal 2.5 detik) agar preloader selalu tertutup meskipun ada aset eksternal yang lambat.
- [x] **Langkah 4**: Sinkronisasi Aset & Kompilasi:
  - Salin file CSS dan JS ke direktori `public/`.
  - Jalankan `npm run build` Vite.
  - Jalankan `php artisan view:clear`.
- [x] **Langkah 5**: Automated Feature Tests & Formatting:
  - Jalankan test suite `LandingPageTest`, `BidangJurusanMappingTest`, dan lainnya.
  - Jalankan `vendor/bin/pint --test`.

---

## 5. Strategi Pengujian (Verification Strategy)

1. **Pengujian Tampilan & Perilaku Refresh**:
   - Membuka halaman landing page (`GET /`) dan memeriksa bahwa preloader muncul sejenak saat halaman pertama kali dibuka, lalu memudar secara halus (fade-out) mengungkap landing page.
   - Memastikan tidak ada glitch atau layout shift setelah preloader menghilang.
   - Memastikan interaktivitas tombol hero, tab kuota, dan link header berfungsi 100% normal tanpa terhalang preloader.
2. **Automated Feature Tests**:
   - Memastikan 72+ test suite yang ada tetap lulus 100% tanpa regresi.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Saat halaman landing page di-refresh atau dibuka, muncul animasi transisi logo Pemkab Sleman dan MAGKOM.
- [x] Preloader memudar secara halus (fade out) dalam ~0.5 detik begitu halaman siap.
- [x] Seluruh aset frontend dikompilasi bersih via Vite.
- [x] Seluruh automated test lulus 100%.

