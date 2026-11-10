# Plan: Animasi Scroll Pertama Kali Landing Page MAGKOM (Scroll Reveal & Header Dinamis)

Dokumen ini merinci rencana implementasi animasi *scroll* pertama kali pada landing page MAGKOM yang terinspirasi dari portal resmi Pemkab Sleman (`https://kominfo.slemankab.go.id/`), menghadirkan kombinasi transisi *header* dinamis (*glassmorphism & shrink*) serta efek kemunculan konten (*scroll reveal / fade-in-up*) yang halus, anggun, dan berbobot.

---

## 1. Tujuan

Menghadirkan pengalaman interaktif saat pengunjung pertama kali menggulir (*scroll*) landing page MAGKOM:
1. **Header Dinamis Mengambang (*Sticky Shrink & Glassmorphism*)**:
   - Saat halaman berada di posisi puncak (`scrollY <= 20px`), header tampil proporsional standar (`height: 4.25rem`).
   - Saat mulai di-*scroll* (`scrollY > 20px`), header bertransformasi halus mengecil secara elegan (`height: 3.65rem`), latar belakang berubah menjadi semi-transparan dengan efek kabut kaca (*frosted glass / backdrop-filter: blur(12px)*), bayangan mengambang (*elevation shadow*), dan skala logo Sleman menyusut lembut.
2. **Scroll Reveal Konten (*Fade-In Up Entrance*)**:
   - Seksi dan kartu konten (kartu penempatan kuota bidang, kartu tahapan alur magang, kartu FAQ, dan header seksi) yang berada di bawah layar akan muncul meluncur naik perlahan (*smooth fade-in up*, translasi Y 28px ke 0) satu kali saat pertama kali terlihat (*viewport entry*).
   - Efek berjenjang (*staggered delay*) antar kartu bersebelahan agar transisi terasa alami dan tidak kaku.
3. **Aksesibilitas & Performa Tinggi**:
   - Mendukung penuh `prefers-reduced-motion: reduce`: Efek animasi otomatis dinonaktifkan bagi pengguna yang sensitif terhadap gerakan.
   - Menggunakan native `IntersectionObserver` dengan pelepasan observasi (`unobserve`) setelah elemen muncul satu kali, sehingga tidak ada beban CPU/GPU berulang selama sesi membaca.

---

## 2. Asumsi & Pertanyaan Terbuka

- **Asumsi**:
  1. Perilaku animasi cukup terjadi satu kali per siklus muat (*reveal once*), konsisten dengan standar UX portal modern dan performa optimal.
  2. **Tanpa Perubahan Markup Blade**: Seluruh selektor elemen sudah ada di DOM (seperti `.landing-header`, `.bidang-kuota-card`, `.tahapan-card`, `.faq-category-card`, `.landing-section-header`). Sesuai `AGENTS.md` Pasal 3.2, file Blade **tidak disentuh** sehingga struktur markup tetap stabil 100%.
  3. Seluruh styling ditambahkan di `resources/css/pages/public/landing.css` dan logika interaksi di `resources/js/pages/public/landing.js`.

---

## 3. Pendekatan Teknis & Evaluasi Alternatif

### Perbandingan Alternatif

| Kriteria | Opsi A: Library Pihak Ketiga (AOS / ScrollReveal.js) | Opsi B (Direkomendasikan): Native IntersectionObserver & CSS Hardware-Accelerated |
| :--- | :--- | :--- |
| **Ukuran Bundle & Dependency** | Menambah dependency npm eksternal (~15-25 KB) | **0 KB tambahan dependency**, memanfaatkan Web API bawaan browser modern |
| **Kepatuhan AGENTS.md** | Rentan merubah konfigurasi Vite & package.json | **100% mematuhi aturan**: tetap Vanilla JS ES Module & Vanilla CSS |
| **Kontrol & Aksesibilitas** | Terikat konfigurasi default vendor | Kontrol penuh terhadap `prefers-reduced-motion`, timing curve cubic-bezier, dan stagger |
| **Performa & FPS** | Script overhead saat scroll | Menggunakan `IntersectionObserver` terisolasi dan properti `will-change: opacity, transform` |

**Keputusan**: Dipilih **Opsi B** karena murni Vanilla JS & CSS, bebas dependensi baru, dan kinerja render 60 FPS tanpa beban tambahan pada peramban.

---

## 4. Ruang Lingkup Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`resources/css/pages/public/landing.css`](file:///d:/MAGKOM/MAGKOM/resources/css/pages/public/landing.css) | **Modify** | Menambahkan styling `.landing-header.is-scrolled` (blur, elevation shadow, shrink) dan styling Scroll Reveal (`.scroll-reveal`, `.is-revealed`, stagger delay, dan `@media (prefers-reduced-motion)`). |
| [`resources/js/pages/public/landing.js`](file:///d:/MAGKOM/MAGKOM/resources/js/pages/public/landing.js) | **Modify** | Menambahkan `initHeaderScrollState()` untuk toggle class `.is-scrolled` dan `initScrollReveal()` menggunakan `IntersectionObserver`. |
| [`public/css/landing-modern.css`](file:///d:/MAGKOM/MAGKOM/public/css/landing-modern.css) & [`public/js/landing-interactive.js`](file:///d:/MAGKOM/MAGKOM/public/js/landing-interactive.js) | **Sync** | Sinkronisasi kompilasi aset statis publik. |

> **Catatan Integritas Blade**: Tidak ada file Blade yang disentuh dalam implementasi ini.

---

## 5. Langkah-Langkah Implementasi Bertahap

- [x] **Langkah 1**: Tambahkan styling di `resources/css/pages/public/landing.css`:
  - Styling transisi `.landing-header` saat memiliki class `.is-scrolled`:
    - `background-color: rgba(27, 56, 96, 0.90);`
    - `backdrop-filter: blur(14px); -webkit-backdrop-filter: blur(14px);`
    - `box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.25), 0 8px 10px -6px rgba(0, 0, 0, 0.15);`
    - `.landing-header-container` menyusut dari `4.25rem` menjadi `3.65rem` dengan transisi mulus `0.35s ease`.
    - Logo Sleman berskala halus (`transform: scale(0.92);`).
  - Styling Scroll Reveal (`.scroll-reveal`):
    - Initial state: `opacity: 0; transform: translateY(28px); transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);`
    - Target revealed: `.is-revealed { opacity: 1; transform: translateY(0); }`
    - Variasi stagger antar item berturutan (`transition-delay: 0.08s, 0.16s, 0.24s`).
    - Aturan a11y: `@media (prefers-reduced-motion: reduce)` yang mematikan transisi seketika.
- [x] **Langkah 2**: Tambahkan interaktivitas di `resources/js/pages/public/landing.js`:
  - Buat fungsi `initHeaderScrollState()` yang memantau event `scroll` (passive) dan menyematkan `.is-scrolled` saat `window.scrollY > 20`.
  - Buat fungsi `initScrollReveal()`:
    - Menyeleksi elemen konten: header seksi (`.section-heading-lg`, `.section-desc-sub`, `.kuota-questions-grid`), kartu penempatan (`.kuota-highlight-banner`, `.kuota-carousel-wrapper`), kartu tahapan (`.tahapan-sticky-left`, `#tahapanRoadWrap`), dan kartu FAQ (`.faq-visual-col`, `.faq-content-col`).
    - Membuat `IntersectionObserver` dengan ambang `threshold: 0.08` dan margin pemicu `rootMargin: '0px 0px -40px 0px'`.
    - Ketika elemen masuk ke layar, tambahkan class `.is-revealed` lalu lakukan `observer.unobserve(entry.target)`.
    - Mengintegrasikannya pada inisialisasi `DOMContentLoaded`.
- [x] **Langkah 3**: Sinkronisasi Aset & Kompilasi:
  - Salin perubahan ke `public/css/landing-modern.css` dan `public/js/landing-interactive.js`.
  - Jalankan kompilasi Vite: `npm run build`.
  - Jalankan pembersihan view cache: `php artisan view:clear`.
- [x] **Langkah 4**: Automated Feature Tests & Formatting:
  - Jalankan test suite landing page (`LandingPageTest`, `BidangJurusanMappingTest`, `LandingPageFaqRedesignTest`, dsb.).
  - Jalankan `vendor/bin/pint --test` untuk memastikan kode PHP tetap bersih.

---

## 6. Strategi Pengujian (Verification Strategy)

1. **Pengujian Visual & Interaksi**:
   - Membuka landing page (`GET /`) di browser:
     - Header berada pada ukuran default di posisi atas.
     - Saat scroll ke bawah dimulai, header langsung bertransisi mulus ke mode sticky mengambang (*frosted glass*).
     - Saat scroll mencapai seksi kuota, tahapan, dan FAQ, kartu-kartu muncul meluncur naik (*fade-in up*) secara bertahap dan hanya sekali.
     - Menguji bahwa klik tautan navbar tetap melakukan smooth-scroll ke posisi seksi yang presisi tanpa tertutup header.
2. **Automated Feature Tests**:
   - Memastikan seluruh 53+ test suite landing page lulus 100% tanpa error.

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Mitigasi |
| :--- | :--- |
| **Elemen tersembunyi jika JavaScript nonaktif / gagal** | Kelas `.scroll-reveal` disematkan secara dinamis oleh JavaScript. Jika JS tidak aktif atau gagal, elemen tetap tampil normal (`fallback gracefully`). |
| **Offset navigasi anchor berubah akibat header mengecil** | Navigasi `initHeaderScrollNav()` yang sudah ada menghitung tinggi header aktual secara dinamis (`header.offsetHeight`), sehingga posisi scroll tetap akurat. |
| **Gerakan mengganggu bagi pengguna sensitif** | Disertakan aturan `@media (prefers-reduced-motion: reduce)` yang membatalkan seluruh transformasi translasi. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Header bertransisi dinamis (efek *frosted glass*, bayangan mengambang, penyusutan ketinggian) saat halaman di-scroll ke bawah.
- [x] Kartu dan seksi konten memiliki animasi masuk (*fade-in up*) halus saat pertama kali masuk ke viewport.
- [x] Tidak ada file Blade yang diubah.
- [x] Asset dikompilasi bersih via Vite tanpa error.
- [x] Seluruh automated feature tests lulus 100%.

