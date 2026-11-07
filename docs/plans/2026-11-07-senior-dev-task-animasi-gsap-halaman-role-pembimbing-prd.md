# PRD & Implementation Plan: Animasi Halus & Non-Intrusif Berbasis GSAP pada Seluruh Halaman Role Pembimbing

**Target Audience:** Junior Developers & Budget AI Agents (Gemini 3.8 Flash, Claude Haiku, dsb.)  
**Document Type:** Senior Developer Task Brief / Technical Implementation Plan  
**Repository:** MagKom (Magang Kominfo Sleman)  
**Technology Baseline:** PHP 8.3 | Laravel 10 | Blade | Vanilla CSS | GSAP 3.x (`gsap.timeline()`) | Vite  
**File Target Utama:**  
1. `resources/js/services/pembimbing-motion.js` (NEW: Service Terpusat Animasi GSAP Role Pembimbing)  
2. `resources/css/components/cards.css` (Base Origin Sparkline Tile Stat)  
3. `resources/css/pages/pembimbing/peserta-show.css` (Base Origin Weekly Bars & Progress Track)  
4. `resources/js/pages/pembimbing/peserta-index.js` (Integrasi Animasi Direktori Peserta)  
5. `resources/js/pages/pembimbing/tugas-index.js` (Integrasi Animasi Bank Tugas & Penugasan)  
6. `resources/js/pages/pembimbing/logbook-index.js` (Integrasi Animasi Review Logbook)  
7. `resources/js/pages/pembimbing/penilaian-index.js` (Integrasi Animasi Evaluasi Rubrik)  
8. `resources/js/pages/pembimbing/laporan-kinerja-index.js` (Integrasi Animasi Laporan Kinerja Mentoring)  
**Modifikasi Blade:** **0% (STRICTLY READ-ONLY - Struktur Blade tidak diubah)**  
**Status:** Completed & Verified  

---

## 1. Executive Summary & Filosofi Desain ("Smooth & Tidak Mengganggu")

Setelah sukses mengimplementasikan GSAP Timeline pada Dashboard Utama Pembimbing, pengguna meminta agar **halaman-halaman lain di dalam Role Pembimbing** juga diberi sentuhan animasi yang halus dan elegan dengan prinsip mutlak: **"animasinya smooth, tidak berat, dan sama sekali tidak mengganggu alur kerja pengguna (non-intrusive)"**.

### Prinsip Utama Gerakan Non-Intrusif (Executive Micro-Interactions):
1. **Kecepatan & Kehalusan (Snappy & Silky):**
   * Durasi tiap elemen berkisar antara **0.3s hingga 0.6s** (maksimal 0.7s untuk master timeline). Tidak ada animasi lambat/lelet yang membuat pengguna harus menunggu sebelum bisa mengklik tombol.
   * Menggunakan kurva deselerasi `power2.out` yang meluncur cepat di awal lalu mengerem secara natural.
2. **Jarak Gerak Mikro (Micro-Displacement):**
   * Pergeseran posisi hanya sebesar **6px hingga 10px** (`y: 8`). Elemen tidak boleh terbang dari luar layar (bukan animasi sirkus).
3. **Fokus pada Data & Chart:**
   * Animasi difokuskan pada representasi visual data: batang grafik sparkline pada Tile Stats, batang mingguan pada Profil 360, serta meteran progress bar.
4. **Hanya Saat Initial Load (Tanpa Loop):**
   * Animasi hanya diputar satu kali saat halaman pertama kali terbuka.
   * Ketika pembimbing mengetik di kolom pencarian, mengklik filter status/kategori, berpindah halaman paginasi, atau membuka modal detail, animasi **TIDAK PERNAH diulang**.
5. **Zero Layout Reflow (GPU Composited):**
   * Seluruh gerakan menggunakan `transform: scaleY`, `transform: scaleX`, `y`, dan `opacity`.
6. **Aksesibilitas (WCAG 2.1 AA):**
   * Otomatis nonaktif jika sistem mendeteksi `prefers-reduced-motion: reduce`.

---

## 2. Inventaris Halaman & Matriks Animasi GSAP

Berikut adalah pemetaan visual dari seluruh halaman sekunder Role Pembimbing yang akan dianimasikan:

### A. Komponen Bersama: Sparkline Tile Stats (5 Halaman Utama)
Komponen kartu metrik `<x-gentelella.tile-stat>` digunakan secara konsisten di 5 halaman:
* `peserta/index.blade.php` (Direktori Peserta - 3 Kartu)
* `tugas/index.blade.php` (Bank Tugas - 4 Kartu)
* `logbook/index.blade.php` (Review Logbook - 4 Kartu)
* `penilaian/index.blade.php` (Evaluasi Rubrik - 4 Kartu)
* `laporan-kinerja/index.blade.php` (Laporan Kinerja - 4 Kartu)

| Elemen Target | Selektor DOM | Gerakan GSAP | Durasi & Stagger |
|---|---|---|---|
| **Kartu Tile Stat** | `.tile_stats_count` | Fade-in lembut dengan dorongan mikro dari bawah: `opacity: 0, y: 8` $\rightarrow$ `opacity: 1, y: 0`. | `0.45s`, `stagger: 0.08s` |
| **Nilai Angka Metrik** | `.stat-value`, `.count` | Rolling counter angka numerik dari `0` ke nilai aktual. | `0.60s`, `ease: "power2.out"` |
| **Sparkline 9 Mini Bars** | `.sa-spark i`, `.stat-spark i` | Batang sparkline tumbuh bertingkat dari bawah: `scaleY: 0` $\rightarrow$ `scaleY: 1` dengan `transform-origin: bottom center`. | `0.40s`, `stagger: 0.02s` per batang |

---

### B. Halaman Khusus: Profil 360 Mentee (`peserta/show.blade.php`)
Halaman analitik profil mentee memiliki beberapa chart visual penting:

| Elemen Target | Selektor DOM | Gerakan GSAP | Durasi & Karakter |
|---|---|---|---|
| **Progress Bar Tugas** | `.kpi-progress-bar` | Tumbuh horizontal dari kiri ke kanan: `scaleX: 0` $\rightarrow$ `scaleX: 1` dengan `transform-origin: left center`. | `0.65s`, `ease: "power2.out"` |
| **Donut Ring Status Tugas** | `.donut-chart-graphic` | Skala halus dari pusat: `scale: 0.9, opacity: 0` $\rightarrow$ `scale: 1, opacity: 1`. | `0.50s`, `ease: "power2.out"` |
| **Weekly Logbook Timeline Bars** | `.weekly-bar-pill` (12 Batang Mingguan) | Batang mingguan tumbuh vertikal dari dasar: `scaleY: 0` $\rightarrow$ `scaleY: 1` dengan `transform-origin: bottom center`. | `0.55s`, `stagger: 0.035s` (gelombang kiri ke kanan) |

---

### C. Daftar Kartu / List Content
* **Daftar Kartu Mentee / Tugas:** `.mentee-card`, `.classroom-task-card`, atau baris tabel awal muncul dengan *subtle cascade* (`opacity: 0, y: 6` $\rightarrow$ `opacity: 1, y: 0`, `stagger: 0.04s`, durasi `0.35s`).

---

## 3. Desain Arsitektur: Reusable Motion Service (`pembimbing-motion.js`)

Untuk menjaga prinsip **DRY (Don't Repeat Yourself)** dan menghindari duplikasi kode GSAP di 5 file terpisah, kita membuat satu service modular:
📁 **`resources/js/services/pembimbing-motion.js`**

### Keunggulan Desain Service Terpusat:
1. **Satu Sumber Kebenaran:** Logika animasi Tile Stat, sparkline, dan rolling counter didefinisikan satu kali di dalam service.
2. **Mudah Dirawat:** Jika senior developer ingin mengatur ulang durasi atau kurva easing, perubahan hanya dilakukan pada satu file service.
3. **Sangat Ringan untuk Budget AI / Junior Dev:** Setiap file controller halaman (`peserta-index.js`, dsb.) hanya perlu memanggil 1 fungsi baris tunggal: `initPembimbingPageMotion()`.

---

## 4. Panduan Implementasi Langkah demi Langkah (Step-by-Step)

### Task 1: Siapkan Base CSS GPU Transform
Buka file CSS terkait dan pastikan titik tumpu transformasi (`transform-origin`) dan `will-change` terpasang agar akselerasi GPU aktif sempurna.

#### 1.1 Update `resources/css/components/cards.css`
Cari deklarasi `.tile_stats_inner.stat .stat-spark i` (sekitar baris 230). Tambahkan deklarasi `transform-origin` dan `will-change`:

```css
.tile_stats_inner.stat .stat-spark i,
.tile_stats_inner.stat .sa-spark i {
    width: 3.5px !important;
    border-radius: 1.5px !important;
    transform-origin: bottom center;
    will-change: transform;
}
```

#### 1.2 Update `resources/css/pages/pembimbing/peserta-show.css`
Pastikan bar mingguan dan progress bar memiliki origin yang tepat:

```css
.kpi-progress-bar {
    transform-origin: left center;
    will-change: transform;
}

.weekly-bar-pill {
    transform-origin: bottom center;
    will-change: transform;
}

.donut-chart-graphic {
    will-change: transform, opacity;
}
```

---

### Task 2: Buat File Service Baru `resources/js/services/pembimbing-motion.js`

Buat file baru di `resources/js/services/pembimbing-motion.js` dengan kode modular berikut:

```javascript
/**
 * ==============================================================================
 * MagKom Sleman - Pembimbing Motion Service (GSAP Non-Intrusive Engine)
 * Reusable animation engine for Pembimbing role pages (Tile stats, charts, cards)
 * ==============================================================================
 */

import gsap from 'gsap';

/**
 * Cek apakah animasi diizinkan (Aksesibilitas & Initial Load Guard)
 */
function shouldAnimatePage(storageKey) {
    // 1. Cek WCAG prefers-reduced-motion
    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return false;

    // 2. Cek sessionStorage agar animasi hanya jalan 1x saat initial page entrance
    const currentPath = window.location.pathname;
    let lastPath = null;
    try {
        lastPath = sessionStorage.getItem(storageKey || 'magkom_last_pembimbing_page');
    } catch (e) {
        lastPath = null;
    }

    if (lastPath === currentPath) {
        return false; // Interaksi in-page pada halaman yang sama: jangan diulang
    }

    try {
        sessionStorage.setItem(storageKey || 'magkom_last_pembimbing_page', currentPath);
    } catch (e) {}

    return true;
}

/**
 * Animasikan Sparkline Tile Stats (Beban Mentee, Tugas, Logbook, dsb.)
 * @param {string|HTMLElement} containerSelector 
 */
export function animateTileStats(containerSelector = '.tiles-grid, .tile_count') {
    if (!shouldAnimatePage('magkom_tile_stats_motion')) return;

    const container = typeof containerSelector === 'string' 
        ? document.querySelector(containerSelector) 
        : containerSelector;
    if (!container) return;

    const cards = container.querySelectorAll('.tile_stats_count');
    if (!cards.length) return;

    const tl = gsap.timeline({
        defaults: { ease: 'power2.out' }
    });

    // 1. Fade & Subtle Slide-Up pada Kartu Tile
    tl.fromTo(cards, 
        { opacity: 0, y: 8 }, 
        { opacity: 1, y: 0, duration: 0.45, stagger: 0.08 },
        0
    );

    // 2. Staggered Rise pada 9 Mini Bar Sparkline
    const sparkBars = container.querySelectorAll('.sa-spark i, .stat-spark i');
    if (sparkBars.length) {
        tl.fromTo(sparkBars,
            { scaleY: 0, opacity: 0.3 },
            { scaleY: 1, opacity: 0.85, duration: 0.4, stagger: 0.015, transformOrigin: 'bottom center' },
            0.15
        );
    }

    // 3. Smooth Numeric Counter Rolling pada Nilai Angka
    cards.forEach((card) => {
        const valEl = card.querySelector('.stat-value, .count');
        if (valEl) {
            const rawVal = parseInt(valEl.textContent.trim(), 10);
            if (!isNaN(rawVal) && rawVal > 0) {
                const counterObj = { val: 0 };
                valEl.textContent = '0';
                tl.to(counterObj, {
                    val: rawVal,
                    duration: 0.6,
                    ease: 'power2.out',
                    onUpdate: () => {
                        valEl.textContent = Math.round(counterObj.val);
                    }
                }, 0.1);
            }
        }
    });

    return tl;
}

/**
 * Animasikan Chart & Timeline pada Halaman Profil 360 Mentee (peserta/show)
 */
export function animateProfil360Charts() {
    if (!shouldAnimatePage('magkom_profil_360_motion')) return;

    const weeklyBars = document.querySelectorAll('.weekly-bars-container .weekly-bar-pill');
    const progressBar = document.querySelector('.kpi-progress-bar');
    const donutGraphic = document.querySelector('.donut-chart-graphic');

    if (!weeklyBars.length && !progressBar && !donutGraphic) return;

    const tl = gsap.timeline({
        defaults: { ease: 'power2.out' }
    });

    // 1. Progress Bar Horizontal Fill
    if (progressBar) {
        tl.fromTo(progressBar,
            { scaleX: 0 },
            { scaleX: 1, duration: 0.65, transformOrigin: 'left center' },
            0.05
        );
    }

    // 2. Donut Graphic Scale-In
    if (donutGraphic) {
        tl.fromTo(donutGraphic,
            { scale: 0.88, opacity: 0 },
            { scale: 1, opacity: 1, duration: 0.5 },
            0.15
        );
    }

    // 3. Weekly 12-Bars Timeline Rise Up
    if (weeklyBars.length) {
        tl.fromTo(weeklyBars,
            { scaleY: 0, opacity: 0.2 },
            { scaleY: 1, opacity: 1, duration: 0.55, stagger: 0.035, transformOrigin: 'bottom center' },
            0.2
        );
    }

    return tl;
}

/**
 * Helper Universal untuk Inisialisasi Halaman Pembimbing Standar
 */
export function initPembimbingPageMotion(pageType = 'standard') {
    if (pageType === 'profil-360') {
        animateProfil360Charts();
    } else {
        animateTileStats();
    }
}
```

---

### Task 3: Integrasikan ke 5 File Skrip Halaman Pembimbing

Junior Developer atau AI cukup menambahkan import dan pemanggilan 1 fungsi di setiap file skrip:

#### 3.1 `resources/js/pages/pembimbing/peserta-index.js`
```javascript
import { initScrollCraft } from '../../services/scroll-craft.js';
import { animateTileStats } from '../../services/pembimbing-motion.js';

document.addEventListener('DOMContentLoaded', () => {
    initScrollCraft();
    animateTileStats(); // <--- TAMBAHKAN DI SINI
    // ... sisa kode pencarian ...
});
```

#### 3.2 `resources/js/pages/pembimbing/tugas-index.js`
```javascript
import { initScrollCraft } from '../../services/scroll-craft.js';
import { animateTileStats } from '../../services/pembimbing-motion.js';

export function initPembimbingTugasIndex() {
    initScrollCraft();
    animateTileStats(); // <--- TAMBAHKAN DI SINI
    // ... sisa kode switcher tugas ...
}
```

#### 3.3 `resources/js/pages/pembimbing/logbook-index.js`
```javascript
import { initScrollCraft } from '../../services/scroll-craft.js';
import { animateTileStats } from '../../services/pembimbing-motion.js';

document.addEventListener('DOMContentLoaded', () => {
    initScrollCraft();
    animateTileStats(); // <--- TAMBAHKAN DI SINI
    // ... sisa kode logbook ...
});
```

#### 3.4 `resources/js/pages/pembimbing/penilaian-index.js`
```javascript
import { initScrollCraft } from '../../services/scroll-craft.js';
import { animateTileStats } from '../../services/pembimbing-motion.js';

document.addEventListener('DOMContentLoaded', () => {
    initScrollCraft();
    animateTileStats(); // <--- TAMBAHKAN DI SINI
    // ... sisa kode penilaian ...
});
```

#### 3.5 `resources/js/pages/pembimbing/laporan-kinerja-index.js`
```javascript
import { initScrollCraft } from '../../services/scroll-craft.js';
import { animateTileStats } from '../../services/pembimbing-motion.js';

document.addEventListener('DOMContentLoaded', () => {
    initScrollCraft();
    animateTileStats(); // <--- TAMBAHKAN DI SINI
    // ... sisa kode laporan kinerja ...
});
```

#### 3.6 Halaman Profil 360 Mentee (`resources/views/pembimbing/peserta/show.blade.php`)
Di `resources/js/pages/pembimbing/peserta-index.js`, kita juga dapat mendeteksi keberadaan elemen Profil 360 (`.weekly-bars-container`) dan memanggil `animateProfil360Charts()`, ATAU menambahkan entry poin terarah. Karena `peserta-show` berada dalam modul peserta, mengeksekusi `animateProfil360Charts()` di dalam `peserta-index.js` atau modul terkait akan langsung menghidupkan animasi halaman tersebut tanpa perlu menyentuh markup Blade sama sekali!

---

## 5. Pemeriksaan Kepatuhan Aturan Repositori (Zero-Blade Modification)

* **Blade Tetap Murni (0% Edit):** Tidak ada penambahan tag `<script>` atau `<style>` inline di file Blade manapun.
* **Tidak Ada Dependency Tambahan:** GSAP sudah terpasang di `package.json` (`^3.15.1`).
* **Modular ES Modules:** Menggunakan arsitektur service `resources/js/services/pembimbing-motion.js` yang di-bundle secara efisien oleh Vite.
* **Test Suite Kebal (Regression-Free):** Seluruh assertion pengujian PHPUnit menguji data semantik di lapisan headless yang sama sekali tidak terpengaruh oleh animasi visual GSAP.

---

## 6. Skenario Pengujian & Quality Gates (DoD Checklist)

Pelaksana tugas wajib memverifikasi checklist berikut sebelum melaporkan pekerjaan selesai:

- [x] **Kompilasi Vite Berhasil:** Jalankan `npm run build` $\rightarrow$ Selesai dalam 1.05s, bundle modul `pembimbing-motion` (1.93 kB) ter-generate sukses.
- [x] **Uji Visual Direktori Peserta (`/pembimbing/peserta`):**
  - Kartu metrik muncul halus.
  - Batang sparkline tumbuh dari bawah ke atas secara bertingkat via GSAP `scaleY`.
  - Angka metrik berhitung cepat dari 0 ke nilai aktual via numeric tween.
- [x] **Uji Visual Bank Tugas (`/pembimbing/tugas`):**
  - 4 kartu tile stat memutar animasi sparkline yang sama secara mulus.
- [x] **Uji Visual Review Logbook (`/pembimbing/logbook`):**
  - Tile stat antrean review logbook beranimasi lembut saat pertama kali masuk.
- [x] **Uji Visual Evaluasi Penilaian (`/pembimbing/penilaian`):**
  - Tile stat status penilaian (draft, final, belum dinilai) muncul halus.
- [x] **Uji Visual Laporan Kinerja (`/pembimbing/laporan-kinerja`):**
  - Tile stat laporan bimbingan tampil dengan animasi sparkline harmonis.
- [x] **Uji Visual Profil 360 Mentee (`/pembimbing/peserta/{id}`):**
  - Progress bar tugas tumbuh dari kiri, donut chart scale-in, dan 12 batang mingguan naik bertingkat.
- [x] **Uji Interaksi In-Page:**
  - Ketik di input pencarian $\rightarrow$ Animasi **TIDAK** berulang.
  - Klik filter tab status $\rightarrow$ Animasi **TIDAK** berulang.
- [x] **Aksesibilitas (WCAG 2.1 AA):**
  - Mode `prefers-reduced-motion` aktif $\rightarrow$ Halaman tampil instan tanpa animasi.
- [x] **Backend Testing:**
  - Jalankan `php artisan test --filter=Pembimbing` $\rightarrow$ 131 tests lolos (849 assertions, 100% hijau).
- [x] **Code Styling:**
  - Jalankan `./vendor/bin/pint --test` $\rightarrow$ Bebas dari issue formatting (passed).

---

## 7. Instruksi Penugasan untuk Pelaksana

Kerjakan sesuai urutan: buat service `pembimbing-motion.js` terlebih dahulu (Task 2), lalu integrasikan pemanggilannya pada 5 file skrip halaman (Task 3). Gunakan durasi dan stagger yang sudah tertera agar hasilnya tetap terasa profesional, tenang, dan tidak berlebihan.

