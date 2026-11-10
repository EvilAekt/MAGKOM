# PRD & Implementation Plan: Animasi Smooth & Elegan pada Seluruh Chart Dashboard Pembimbing (Initial Load Only)

**Target Audience:** Junior Developers & Budget AI Agents (Gemini 3.8 Flash, Claude Haiku, dsb.)  
**Document Type:** Senior Developer Task Brief / Technical Implementation Plan  
**Repository:** MagKom (Magang Kominfo Sleman)  
**Stack Baseline:** PHP 8.3 | Laravel 10 | Blade | Vanilla CSS | Vanilla JS (ES Modules) | Vite  
**File Target Utama:**  
1. `resources/css/pages/pembimbing/dashboard.css` (CSS Keyframes, Transisi GPU-Accelerated & Staggering)  
2. `resources/js/pages/pembimbing/dashboard.js` (Orkestrasi SVG Gauge Sweep & Percentage Counter Animation)  
**File Referensi Markup (Read-Only / 0% Edit):**  
- `resources/views/pembimbing/dashboard/partials/_bento_charts.blade.php`  
- `resources/views/pembimbing/dashboard/partials/_bento_highlight_roster.blade.php`  
- `resources/views/pembimbing/dashboard/partials/_bento_metrics.blade.php`  
**Modifikasi Blade:** **0% (STRICTLY READ-ONLY - Tidak ada perubahan markup Blade)**  
**Status:** Completed & Verified  

---

## 1. Executive Summary & Objective

Dashboard Pembimbing Lapangan MagKom Sleman mengusung konsep **Bento Grid Executive Modern** yang menyajikan visualisasi data analitik untuk memantau aktivitas mentee secara cepat dan presisi. 

### Masalah Saat Ini:
Seluruh representasi visual analitik (batang grafik Distribusi Tugas, arc meteran Kuota Bimbingan, segmen progress roster, dan dot matrix) dirender secara statis seketika saat halaman selesai dimuat. Hal ini membuat dashboard terasa kaku dan kurang dinamis.

### Tujuan Pekerjaan:
Menerapkan animasi yang **halus (smooth), elegan, dan ringan (60 FPS, GPU-accelerated)** untuk seluruh grafik dan progress bar di Dashboard Pembimbing dengan ketentuan mutlak:
1. **Hanya Berjalan Saat Initial Load (Awal Masuk Halaman):** Animasi hanya terpicu satu kali saat pengguna pertama kali membuka dashboard atau melakukan full refresh. Animasi **DILARANG KERAS** mengulang (*re-trigger*) saat pengguna mengklik filter tugas, membuka modal, memfilter tabel, berpindah tab, atau memilih tanggal di kalender.
2. **Ringan & Bebas Jank/Lag:** Wajib menggunakan akselerasi perangkat keras GPU (`transform: scaleY`, `opacity`, dan Web Animations API). **Dilarang menganimasikan properti CSS `height` secara langsung** karena memicu *layout reflow/thrashing* yang membebani CPU browser.
3. **Mematuhi Arsitektur Proyek:** Menggunakan ekosistem yang sudah ada (`Scroll-Craft` lifecycle: `.is-revealed`, `.sc-settled`, dan `sessionStorage`), tanpa menambah dependency eksternal (NO GSAP, NO Chart.js, NO library luar).
4. **WCAG 2.1 AA Accessibility:** Mendukung `prefers-reduced-motion` secara penuh (langsung tampil statis tanpa animasi bagi pengguna dengan sensitivitas gerakan).

---

## 2. Inventaris Komponen & Matriks Animasi (Chart Inventory)

Berikut adalah daftar seluruh komponen visual yang akan dianimasikan di Dashboard Pembimbing:

| No | Nama Komponen & Kartu | File Markup Acuan | Elemen Target (CSS/DOM) | Tipe Gerakan Animasi | Durasi & Easing | Delay (Stagger) |
|---|---|---|---|---|---|---|
| **1** | **Bar Chart: Distribusi Tugas** (4 Batang: Selesai, Dikerjakan, Review, Terlambat) | `_bento_charts.blade.php` | `.bento-bar`<br>(`.bar-selesai`, `.bar-dikerjakan`, `.bar-review`, `.bar-terlambat`) | Tumbuh vertikal dari bawah ke atas menggunakan GPU `transform: scaleY(0)` ke `scaleY(1)` dengan titik tumpu `transform-origin: bottom center`. | `0.75s`<br>`cubic-bezier(0.16, 1, 0.3, 1)` | Stagger bertingkat:<br>• Selesai: `0.10s`<br>• Dikerjakan: `0.20s`<br>• Review: `0.30s`<br>• Terlambat: `0.40s` |
| **2** | **Badge Angka Nilai Bar** | `_bento_charts.blade.php` | `.bento-bar-val` | Fade-in lembut dengan pergeseran halus `translateY(4px)` ke `translateY(0)` setelah pucuk bar hampir selesai naik. | `0.35s`<br>`ease-out` | Muncul serentak setelah bar masing-masing mencapai ~80% tinggi (`delay: bar-delay + 0.35s`). |
| **3** | **Semi-Circle Radial Gauge (Kuota Bimbingan)** | `_bento_charts.blade.php` | `svg path.gauge-fill` | Busur melingkar menyapu halus (*sweep stroke*) dari kosong (`dashoffset: 188.5`) menuju nilai kuota riil (`dashoffset: target`). | `1.00s`<br>`cubic-bezier(0.16, 1, 0.3, 1)` | `0.25s` setelah kartu muncul. |
| **4** | **Counter Angka Persentase Kuota** | `_bento_charts.blade.php` | `.gauge-center-percent` | Angka persentase berhitung cepat dan mulus (*number rolling counter*) dari `0%` hingga persentase kapasitas riil (misal `65%`). | `1.00s`<br>Ease-out curve | Berjalan sinkron bersamaan dengan sapuan busur SVG gauge. |
| **5** | **Segmented Progress Dashes (Highlight Roster)** | `_bento_highlight_roster.blade.php` | `.roster-dash.dash-filled-navy`, `.dash-filled-white` | Sapuan gelombang (*wave fill*) dengan transisi `opacity` & `scale(0.8)` ke `scale(1)`. | `0.30s` per dash | Stagger berurutan antar dash: `0.03s` per segmen (total ~0.5s). |
| **6** | **KPI Mini Matrix Dots** | `_bento_metrics.blade.php` | `.matrix-dot.dot-filled` | Titik-titik matrix terisi muncul dengan efek fade-scale lembut. | `0.25s`<br>`ease-out` | Stagger `0.02s` per dot. |

---

## 3. Arsitektur Teknis: Mengapa & Bagaimana Solusi Ini Bekerja

### A. Rahasia Performa 60 FPS: GPU Composition vs Layout Reflow
Banyak junior developer atau AI awam melakukan animasi bar chart dengan mengubah nilai properti CSS `height` dari `0%` ke `100%`:
```css
/* CONTOH SALAH / JANGAN DITIRU */
@keyframes wrongBarAnimation {
  from { height: 0%; } /* MEMICU BROWSER REFLOW DI SETIAP FRAME (LAG!) */
  to { height: 80%; }
}
```
Ketika browser mengubah `height`, browser terpaksa menghitung ulang geometri (*layout reflow*), mengecat ulang piksel (*repaint*), dan menyusun ulang seluruh layer halaman pada setiap 16 milidetik. Ini membuat perangkat lambat menjadi patah-patah (*janky*).

**Solusi Senior:**
Kita menggunakan **GPU Transform Scale**:
```css
/* CARA BENAR / SENIOR STANDARD */
@keyframes bentoBarRiseUp {
  0% {
    transform: scaleY(0);
    opacity: 0.3;
  }
  100% {
    transform: scaleY(1);
    opacity: 1;
  }
}
```
- Elemen `.bento-bar` sudah memiliki tinggi final dari Blade (misal `style="height: 65%;"`).
- Dengan mengatur `transform-origin: bottom center;`, kita hanya menginstruksikan GPU untuk meregangkan tekstur yang sudah di-raster dari skala `Y: 0` (di dasar) menjadi skala penuh `Y: 1`.
- Hasilnya: **Nol reflow, 60 FPS mentok, dan konsumsi baterai/CPU minimal.**

### B. Menghindari Distorsi Angka Badge (`.bento-bar-val`)
Karena `.bento-bar` mengalami peregangan skala vertikal, teks angka di dalamnya (`.bento-bar-val`) berpotensi ikut memanjang secara tidak proporsional jika langsung ditampilkan sejak frame pertama.
**Solusi:**
Teks `.bento-bar-val` dibuat `opacity: 0` saat awal animasi, dan baru dimunculkan secara halus (*fade-in slide*) saat bar sudah mendekati ketinggian akhirnya.

### C. Kurva Easing "Apple / Sleman Modern"
Jangan gunakan easing standar bawaan browser seperti `linear` atau `ease` biasa yang terlihat kaku. Kita menggunakan kurva eksponensial deselerasi tinggi:
```css
cubic-bezier(0.16, 1, 0.3, 1)
```
Kurva ini meluncur cepat di awal lalu mengerem secara sangat lembut dan presisi di akhir (*snappy yet silky smooth*).

### D. Mekanisme "Hanya Saat Initial Load"
Repositori MagKom sudah memiliki modul `Scroll-Craft` (`resources/js/services/scroll-craft.js`) yang mengelola siklus hidup tampilan (*lifecycle*):
1. Saat halaman pertama kali dibuka, kartu bento memiliki kelas `[data-scroll-reveal]`.
2. Saat kartu masuk viewport, Scroll-Craft menambahkan kelas `.is-revealed`.
3. Setelah 900 milidetik, Scroll-Craft menambahkan kelas `.sc-settled`.
4. Aturan CSS yang sudah ada di `dashboard.css`:
   ```css
   [data-scroll-reveal].is-revealed.sc-settled {
     animation: none !important;
     transition: none !important;
   }
   ```
5. Selain itu, Scroll-Craft menyimpan `magkom_last_page_path` di `sessionStorage`. Jika pengguna melakukan aksi in-page pada halaman yang sama, elemen langsung diberi kelas `.sc-instant-reveal` sehingga animasi **TIDAK PERNAH diputar ulang**.

Dengan menyematkan selector animasi pada:
```css
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .bento-bar
```
animasi dijamin **100% hanya berjalan 1 kali pada initial load**, dan terkunci permanen setelah selesai.

---

## 4. Panduan Implementasi Langkah demi Langkah (Step-by-Step)

### Task 1: Modifikasi File CSS (`resources/css/pages/pembimbing/dashboard.css`)

Buka file `resources/css/pages/pembimbing/dashboard.css` dan lakukan pembaruan terarah berikut:

#### 1.1 Perbaiki Keyframe & Animasi Bar Distribusi Tugas
Cari baris ~1625 hingga ~1645 di `resources/css/pages/pembimbing/dashboard.css`. Hapus rule usang `.is-revealed .bento-bar-fill-*` dan ganti dengan blok animasi presisi berikut:

```css
/* ==============================================================================
   Smooth Chart Animations (Initial Load Only via Scroll-Craft Lifecycle)
   ============================================================================== */

/* Keyframe: Bar Chart Vertical Growth (GPU Composited) */
@keyframes bentoBarRiseUp {
  0% {
    transform: scaleY(0);
    opacity: 0.25;
  }
  60% {
    opacity: 1;
  }
  100% {
    transform: scaleY(1);
    opacity: 1;
  }
}

/* Keyframe: Bar Value Badge Fade & Settle */
@keyframes bentoBarValFadeIn {
  0% {
    opacity: 0;
    transform: translateY(6px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Keyframe: Segmented Dash Sequential Glow */
@keyframes rosterDashFadeIn {
  0% {
    opacity: 0.2;
    transform: scale(0.75);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

/* Keyframe: KPI Dot Matrix Wave Entrance */
@keyframes dotMatrixPulse {
  0% {
    opacity: 0.2;
    transform: scale(0.6);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

/* Base Bar Preparation */
.bento-bar {
  transform-origin: bottom center;
  will-change: transform;
}

/* Trigger Animasi Bar Hanya Saat .is-revealed Aktif (Sebelum .sc-settled) */
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .bento-bar {
  animation: bentoBarRiseUp 0.85s cubic-bezier(0.16, 1, 0.3, 1) both;
}

/* Stagger Delay untuk 4 Batang Distribusi Tugas */
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .bar-selesai {
  animation-delay: 0.08s;
}

.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .bar-dikerjakan {
  animation-delay: 0.18s;
}

.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .bar-review {
  animation-delay: 0.28s;
}

.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .bar-terlambat {
  animation-delay: 0.38s;
}

/* Animasi Nilai Angka di dalam Bar */
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .bento-bar-val {
  animation: bentoBarValFadeIn 0.35s ease-out both;
}

.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .bar-selesai .bento-bar-val {
  animation-delay: 0.42s;
}

.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .bar-dikerjakan .bento-bar-val {
  animation-delay: 0.52s;
}

.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .bar-review .bento-bar-val {
  animation-delay: 0.62s;
}

.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .bar-terlambat .bento-bar-val {
  animation-delay: 0.72s;
}

/* Roster Highlight Segments Animation */
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash.dash-filled-navy,
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash.dash-filled-white {
  animation: rosterDashFadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
}

/* Stagger Otomatis untuk 16 Dashes Roster Menggunakan nth-child */
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash:nth-child(1) { animation-delay: 0.05s; }
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash:nth-child(2) { animation-delay: 0.08s; }
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash:nth-child(3) { animation-delay: 0.11s; }
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash:nth-child(4) { animation-delay: 0.14s; }
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash:nth-child(5) { animation-delay: 0.17s; }
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash:nth-child(6) { animation-delay: 0.20s; }
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash:nth-child(7) { animation-delay: 0.23s; }
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash:nth-child(8) { animation-delay: 0.26s; }
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash:nth-child(9) { animation-delay: 0.29s; }
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash:nth-child(10) { animation-delay: 0.32s; }
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash:nth-child(11) { animation-delay: 0.35s; }
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash:nth-child(12) { animation-delay: 0.38s; }
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash:nth-child(13) { animation-delay: 0.41s; }
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash:nth-child(14) { animation-delay: 0.44s; }
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash:nth-child(15) { animation-delay: 0.47s; }
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .roster-dash:nth-child(16) { animation-delay: 0.50s; }

/* KPI Dot Matrix Wave Animation */
.bento-card.is-revealed:not(.sc-instant-reveal):not(.sc-settled) .matrix-dot.dot-filled {
  animation: dotMatrixPulse 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
}
```

---

### Task 2: Modifikasi File JavaScript (`resources/js/pages/pembimbing/dashboard.js`)

Buka file `resources/js/pages/pembimbing/dashboard.js`. Kita akan menambahkan fungsi modular untuk menganimasikan busur SVG Kuota Bimbingan dan angka persentase rolling.

#### 2.1 Tambahkan Helper Animasi Counter Angka
Di bagian atas file atau sebelum inisialisasi utama:
```javascript
/**
 * Helper animasi penghitung angka mulus (smooth rolling counter)
 * @param {HTMLElement} element - Target elemen DOM
 * @param {number} startVal - Nilai awal (biasanya 0)
 * @param {number} endVal - Nilai target (misal 65)
 * @param {number} duration - Durasi animasi dalam milidetik (misal 1000)
 * @param {string} suffix - Karakter akhiran (misal '%')
 */
function animateNumberRoll(element, startVal, endVal, duration = 1000, suffix = '') {
    if (!element) return;
    const startTime = performance.now();

    function updateCounter(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing curve: easeOutCubic (1 - (1 - t)^3)
        const easeProgress = 1 - Math.pow(1 - progress, 3);
        const currentNumber = Math.round(startVal + (endVal - startVal) * easeProgress);

        element.textContent = `${currentNumber}${suffix}`;

        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = `${endVal}${suffix}`;
        }
    }

    requestAnimationFrame(updateCounter);
}
```

#### 2.2 Tambahkan Fungsi `initChartAnimations()`
```javascript
/**
 * Inisialisasi animasi grafik SVG dan angka (Hanya saat initial load)
 */
function initChartAnimations() {
    // 1. Cek prefers-reduced-motion untuk a11y WCAG 2.1 AA
    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    // 2. Cek apakah ini interaksi in-page (bukan initial entrance)
    let lastPath = null;
    try {
        lastPath = sessionStorage.getItem('magkom_last_page_path');
    } catch (e) {}
    
    // Jika path sama dan bukan pertama kali load, lewati animasi agar tidak mengganggu
    // Catatan: initScrollCraft dipanggil sebelum ini dan sudah mencatat path
    const kuotaCard = document.querySelector('.bento-kuota-card');
    if (!kuotaCard || kuotaCard.classList.contains('sc-instant-reveal')) return;

    // 3. Animasi SVG Semi-Circle Gauge Arc
    const gaugeFill = document.querySelector('.bento-kuota-gauge-box .gauge-fill');
    if (gaugeFill && 'animate' in gaugeFill) {
        const targetOffsetAttr = gaugeFill.getAttribute('stroke-dashoffset');
        const targetOffset = targetOffsetAttr !== null ? parseFloat(targetOffsetAttr) : 0;
        const gaugePerimeter = 188.5;

        // Gunakan Web Animations API (WAAPI) yang berjalan di compositor thread
        gaugeFill.animate([
            { strokeDashoffset: gaugePerimeter },
            { strokeDashoffset: targetOffset }
        ], {
            duration: 1100,
            delay: 150,
            easing: 'cubic-bezier(0.16, 1, 0.3, 1)',
            fill: 'forwards'
        });
    }

    // 4. Animasi Rolling Percentage Number (misal: "65%")
    const percentEl = document.querySelector('.gauge-center-percent');
    if (percentEl) {
        const rawText = percentEl.textContent.replace('%', '').trim();
        const targetPercent = parseInt(rawText, 10);
        if (!isNaN(targetPercent)) {
            percentEl.textContent = '0%';
            setTimeout(() => {
                animateNumberRoll(percentEl, 0, targetPercent, 1100, '%');
            }, 150);
        }
    }
}
```

#### 2.3 Panggil di dalam `DOMContentLoaded`
Di akhir file `resources/js/pages/pembimbing/dashboard.js`, panggil `initChartAnimations()` tepat setelah `initScrollCraft()`:
```javascript
// Inisialisasi saat DOM siap
document.addEventListener('DOMContentLoaded', () => {
    initScrollCraft();
    initChartAnimations(); // <--- TAMBAHKAN DI SINI
    initBentoCalendar();

    // ... sisanya tetap sama ...
});
```

---

## 5. Pemeriksaan Kompatibilitas Blade & Arsitektur (Zero-Blade Modification)

Pastikan pemahaman ini dipatuhi oleh Junior Dev & Budget AI:
- File `_bento_charts.blade.php`, `_bento_metrics.blade.php`, dan `_bento_highlight_roster.blade.php` **TIDAK PERLU DIUBAH SAMA SEKALI**.
- Seluruh selector HTML sudah cocok secara sempurna:
  - Kartu sudah memiliki `class="bento-card bento-distribusi-card sc-reveal-scale sc-delay-1" data-scroll-reveal`.
  - Bar sudah memiliki kelas `.bento-bar` dan `.bar-selesai`, `.bar-dikerjakan`, `.bar-review`, `.bar-terlambat`.
  - Teks nilai sudah memiliki kelas `.bento-bar-val`.
  - SVG path sudah memiliki kelas `.gauge-fill` dan atribut `stroke-dashoffset`.
  - Teks persentase sudah memiliki kelas `.gauge-center-percent`.
- Pendekatan ini menjaga integritas aturan proyek MAGKOM: **"Jangan menyentuh Blade jika masalah dapat diselesaikan secara bersih di CSS dan JS"**.

---

## 6. Skenario Uji & Quality Gates (DoD Checklist)

Sebelum menyatakan tugas selesai, Junior Developer atau AI eksekutor wajib menjalankan dan memverifikasi seluruh poin berikut:

### Checklist Verifikasi Fungsional:
- [x] **Kompilasi Aset Berhasil:** Jalankan `npm run build` di terminal. Pastikan tidak ada syntax error pada CSS atau JS, dan manifest ter-update tanpa kegagalan (Lolos: build selesai dalam 998ms).
- [x] **Animasi Initial Load Halus:** Buka halaman dashboard pembimbing di browser. 4 batang Distribusi Tugas tumbuh dari bawah ke atas secara bertingkat (*staggered*). Teks angka muncul setelah batang naik.
- [x] **Animasi Gauge Berputar Mulus:** Busur SVG Kuota Bimbingan tersapu dari kosong menuju nilai terisi, dan angka persentase berhitung dari `0%` menuju persentase riil.
- [x] **Segmen Roster Bergelombang:** 16 segmen pada Highlight Roster menyala secara berurutan.
- [x] **Tidak Re-Trigger pada Interaksi In-Page:**
  - Klik tombol navigasi bulan/tahun pada Kalender Bento $\rightarrow$ Animasi chart **TIDAK** berulang.
  - Klik filter tugas ("Mendesak", "Perlu Review") $\rightarrow$ Animasi chart **TIDAK** berulang.
  - Ketik di input pencarian tabel $\rightarrow$ Animasi chart **TIDAK** berulang.
- [x] **Bebas Reflow / 60 FPS:** Inspeksi menggunakan Chrome DevTools Performance Panel $\rightarrow$ Tidak ada *forced synchronous layout* atau lonjakan CPU berlebih.
- [x] **Aksesibilitas (WCAG 2.1 AA):** Aktifkan opsi OS *Reduce Motion* (atau emulator Chrome DevTools Rendering `prefers-reduced-motion: reduce`) $\rightarrow$ Semua chart tampil langsung tanpa animasi.
- [x] **Regression Test:** Jalankan `php artisan test --filter=Pembimbing` $\rightarrow$ Seluruh assertion hijau (131 tests, 849 assertions lolos 100%).

---

## 7. Kesimpulan untuk Developer / AI Pelaksana

Ikuti petunjuk di atas secara harfiah. Jangan mencoba menambahkan library luar, jangan mengubah file Blade, dan gunakan kode yang telah disediakan tanpa improvisasi berlebih yang keluar dari ruang lingkup tugas ini. Tugas ini dirancang agar dapat selesai dengan cepat, presisi, dan menghasilkan tampilan dashboard berkualitas tinggi.

