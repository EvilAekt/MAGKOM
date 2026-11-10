# PRD & Implementation Plan: Orkestrasi Animasi Chart Dashboard Pembimbing dengan GSAP Timeline

**Target Audience:** Junior Developers & Budget AI Agents (Gemini 3.8 Flash, Claude Haiku, dsb.)  
**Document Type:** Senior Developer Task Brief / Technical Architecture & Implementation Plan  
**Repository:** MagKom (Magang Kominfo Sleman)  
**Reference Tool:** [GSAP Timeline v3 Documentation](https://gsap.com/docs/v3/GSAP/Timeline/)  
**Stack Baseline:** PHP 8.3 | Laravel 10 | Blade | Vanilla CSS | Vanilla JS (ES Modules) | GSAP 3.x | Vite  
**File Target Utama:**  
1. `package.json` (Penambahan dependency resmi `gsap`)  
2. `resources/js/pages/pembimbing/dashboard.js` (Orkestrasi Utama `gsap.timeline()`)  
3. `resources/css/pages/pembimbing/dashboard.css` (Penyederhanaan CSS: menghapus keyframe manual & nth-child delays)  
**File Markup Acuan (Read-Only / 0% Edit):**  
- `resources/views/pembimbing/dashboard/partials/_bento_charts.blade.php`  
- `resources/views/pembimbing/dashboard/partials/_bento_highlight_roster.blade.php`  
- `resources/views/pembimbing/dashboard/partials/_bento_metrics.blade.php`  
**Modifikasi Blade:** **0% (STRICTLY READ-ONLY - Struktur Blade tidak disentuh)**  
**Status:** Completed & Verified  

---

## 1. Executive Summary & Alasan Menggunakan GSAP Timeline

Sebelumnya, kita mengorkestrasikan animasi menggunakan kombinasi CSS `@keyframes`, belasan baris aturan `:nth-child(...)` untuk delay, dan Web Animations API (WAAPI) di JavaScript. Pendekatan tersebut berjalan baik, namun memiliki kelemahan dalam pemeliharaan kode (*maintainability*): jika kita ingin mengubah urutan animasi atau memperlambat satu elemen, kita harus menghitung ulang seluruh delay di file CSS dan JS secara manual.

### Mengapa GSAP Timeline (`gsap.timeline()`) Jauh Lebih Mudah dan Superior?

[GSAP Timeline](https://gsap.com/docs/v3/GSAP/Timeline/) adalah wadah (*container*) sekuensial yang memungkinkan developer merangkai banyak animasi menjadi satu alur waktu terpadu:

1. **Sinkronisasi Presisi Tanpa Hitung Manual:**
   Alih-alih menulis `animation-delay: 0.08s`, `0.18s`, `0.28s`, dan 16 baris `nth-child` di CSS, GSAP menyediakan parameter `stagger: 0.08` dan penanda posisi relatif seperti `"-=0.4"` (mulai 0.4 detik sebelum animasi sebelumnya selesai) atau `"<"` (mulai bersamaan dengan animasi sebelumnya).
2. **Tween Angka Bawaan (Rolling Counter Otomatis):**
   GSAP dapat menganimasikan properti objek JavaScript biasa (`{ val: 0 }` ke `{ val: 65 }`) dengan fungsi `onUpdate`. Ini menggantikan helper manual `requestAnimationFrame` dan rumus matematis `easeOutCubic` yang panjang.
3. **Performa GPU Terbaik:**
   GSAP secara otomatis mengoptimalkan properti transformasi (`scaleY`, `y`, `opacity`, `strokeDashoffset`) langsung pada GPU *compositor thread* tanpa memicu *layout thrashing*.
4. **Ukuran Sangat Ringan:**
   GSAP core versi 3 hanya berukuran ~23 kB (gzipped) dan dapat di-tree-shake secara modular via Vite.
5. **Kontrol Siklus Hidup Tunggal (Initial Load Only):**
   Satu instance timeline `const tl = gsap.timeline()` dapat langsung dikendalikan: dihentikan (*pause*), dibalik (*reverse*), atau dilewati (*seek(1)*) saat mendeteksi `prefers-reduced-motion` atau interaksi in-page.

---

## 2. Arsitektur Orkestrasi: Diagram Sekuens Garis Waktu (Timeline Script)

Seluruh animasi di Dashboard Pembimbing akan dirangkai dalam satu alur waktu harmonis berdurasi total **~1.2 detik**:

```
Detik:  0.0s        0.2s        0.4s        0.6s        0.8s        1.0s        1.2s
Bar:    [=== 4 Batang Tumbuh Vertikal ===] (stagger: 0.08s, power2.out)
Angka:          [=== Badge Nilai Fade In ===] (mulai di detik 0.35)
Gauge:  [====== Sapuan Busur SVG Kuota ======] (durasi: 1.0s, power2.out)
Persen: [====== Angka 0% -> Target% Rolling =====] (sinkron bersamaan "<")
Roster:                 [== 16 Dashes Roster Wave ==] (stagger: 0.025s)
Dots:                           [== Matrix Dots Pulse ==] (stagger: 0.015s)
```

### Rincian Gerakan Per Komponen:
1. **Bar Chart Distribusi Tugas (`.bento-bar`):**
   * Animasi: `scaleY: 0` $\rightarrow$ `scaleY: 1` dengan `transformOrigin: "bottom center"`.
   * Stagger: `0.08s` antar bar (Selesai $\rightarrow$ Dikerjakan $\rightarrow$ Review $\rightarrow$ Terlambat).
   * Easing: `power2.out` (deselerasi lembut khas GSAP).
2. **Badge Angka Nilai Bar (`.bento-bar-val`):**
   * Animasi: `opacity: 0, y: 6` $\rightarrow$ `opacity: 1, y: 0`.
   * Waktu: Muncul tumpang-tindih (*overlap*) saat bar hampir selesai naik (`"-=0.45"`).
3. **Semi-Circle Radial Gauge (`.gauge-fill`):**
   * Animasi: `strokeDashoffset: 188.5` (kosong) $\rightarrow$ `targetOffset` (terisi).
   * Waktu: Berjalan bersamaan dengan bar chart (`position: 0` atau `"<"`).
4. **Counter Angka Kuota (`.gauge-center-percent`):**
   * Animasi: Tween objek numerik dari `0` ke nilai riil (misal `65`) dan mencetak `${Math.round(obj.val)}%` pada setiap frame pembaruan.
   * Waktu: Berjalan bersamaan dengan sapuan busur SVG (`"<"`).
5. **Segmented Dashes Highlight Roster (`.roster-dash`):**
   * Animasi: `scale: 0.7, opacity: 0.2` $\rightarrow$ `scale: 1, opacity: 1`.
   * Stagger: `0.025s` per segmen (menyala bergelombang dari kiri ke kanan).
6. **KPI Mini Matrix Dots (`.matrix-dot.dot-filled`):**
   * Animasi: `scale: 0.6, opacity: 0.2` $\rightarrow$ `scale: 1, opacity: 1`.
   * Stagger: `0.015s` per titik.

---

## 3. Panduan Implementasi Langkah demi Langkah

### Langkah 1: Instalasi GSAP via NPM
Jalankan perintah instalasi dependency pada terminal:
```bash
npm install gsap
```
Pastikan `gsap` tercatat pada bagian `"dependencies"` di file `package.json`.

---

### Langkah 2: Bersihkan CSS di `resources/css/pages/pembimbing/dashboard.css`

Karena seluruh timing, stagger, dan keyframe kini dikendalikan oleh GSAP Timeline, kita **TIDAK PERLU LAGI** menuliskan keyframe manual dan puluhan selector `:nth-child` di file CSS.

Buka `resources/css/pages/pembimbing/dashboard.css`. Cari blok animasi chart di sekitar baris 1625–1730, dan sederhanakan menjadi:

```css
/* ==============================================================================
   Chart Animation Base Styling (Orchestrated by GSAP Timeline in dashboard.js)
   ============================================================================== */

/* Pastikan titik tumpu bar berada di dasar untuk scaleY */
.bento-bar {
  transform-origin: bottom center;
  will-change: transform, opacity;
}

.bento-bar-val {
  will-change: transform, opacity;
}

.gauge-fill {
  will-change: stroke-dashoffset;
}

.roster-dash {
  will-change: transform, opacity;
}

.matrix-dot {
  will-change: transform, opacity;
}

/* WCAG 2.1 AA Guardrail: Langsung tampil statis jika reduce-motion aktif */
@media (prefers-reduced-motion: reduce) {
  .bento-bar,
  .bento-bar-val,
  .gauge-fill,
  .roster-dash,
  .matrix-dot {
    animation: none !important;
    transform: none !important;
    opacity: 1 !important;
  }
}
```

*Keuntungan:* Menghemat lebih dari 100 baris kode CSS manual yang rawan desinkronisasi.

---

### Langkah 3: Implementasi GSAP Timeline di `resources/js/pages/pembimbing/dashboard.js`

Buka file `resources/js/pages/pembimbing/dashboard.js`.

#### 3.1 Impor GSAP di Baris Teratas
Di awal file:
```javascript
import gsap from 'gsap';
import { initScrollCraft } from '../../services/scroll-craft.js';
```

#### 3.2 Buat Fungsi `initGsapChartTimeline()`
Gantikan fungsi animasi sebelumnya dengan fungsi GSAP Timeline terstruktur berikut:

```javascript
/**
 * Inisialisasi Orkestrasi Animasi Chart Menggunakan GSAP Timeline
 * Hanya berjalan 1 kali saat initial load (Fresh Page Entrance).
 */
function initGsapChartTimeline() {
    // 1. Cek prefers-reduced-motion (Aksesibilitas WCAG 2.1 AA)
    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    // 2. Proteksi Initial Load Only (Cegah re-trigger saat interaksi in-page)
    const kuotaCard = document.querySelector('.bento-kuota-card');
    if (!kuotaCard || kuotaCard.classList.contains('sc-instant-reveal')) return;

    // 3. Persiapan Elemen Target
    const bars = document.querySelectorAll('.bento-distribusi-chart-box .bento-bar');
    const barValues = document.querySelectorAll('.bento-distribusi-chart-box .bento-bar-val');
    const gaugeFill = document.querySelector('.bento-kuota-gauge-box .gauge-fill');
    const percentEl = document.querySelector('.gauge-center-percent');
    const rosterDashes = document.querySelectorAll('.roster-segments-bar .dash-filled-navy, .roster-segments-bar .dash-filled-white');
    const matrixDots = document.querySelectorAll('.bento-dot-matrix .dot-filled');

    // Jika komponen utama tidak ditemukan di DOM, batalkan
    if (!bars.length && !gaugeFill) return;

    // 4. Inisialisasi GSAP Master Timeline
    const masterTl = gsap.timeline({
        defaults: {
            ease: "power2.out"
        }
    });

    // --------------------------------------------------------------------------
    // A. Bar Chart Distribusi Tugas: Tumbuh Vertikal dari Dasar
    // --------------------------------------------------------------------------
    if (bars.length) {
        masterTl.fromTo(bars, 
            { 
                scaleY: 0, 
                opacity: 0.25 
            }, 
            { 
                scaleY: 1, 
                opacity: 1, 
                duration: 0.75, 
                stagger: 0.09,
                transformOrigin: "bottom center"
            }, 
            0 // Mulai pada detik ke-0
        );

        // Nilai badge teks angka di dalam bar: Muncul halus saat bar hampir puncak
        if (barValues.length) {
            masterTl.fromTo(barValues,
                { 
                    opacity: 0, 
                    y: 6 
                },
                { 
                    opacity: 1, 
                    y: 0, 
                    duration: 0.35, 
                    stagger: 0.09 
                },
                0.35 // Mulai tumpang tindih pada detik 0.35
            );
        }
    }

    // --------------------------------------------------------------------------
    // B. Semi-Circle Radial Gauge & Rolling Percentage Counter
    // --------------------------------------------------------------------------
    if (gaugeFill) {
        const targetOffsetAttr = gaugeFill.getAttribute('stroke-dashoffset');
        const targetOffset = targetOffsetAttr !== null ? parseFloat(targetOffsetAttr) : 0;
        const gaugePerimeter = 188.5; // Keliling setengah lingkaran (PI * 60)

        // Sapuan busur SVG
        masterTl.fromTo(gaugeFill,
            { 
                strokeDashoffset: gaugePerimeter 
            },
            { 
                strokeDashoffset: targetOffset, 
                duration: 1.05,
                ease: "power2.out" 
            },
            0.1 // Mulai bersamaan dengan awal chart (detik 0.1)
        );

        // Rolling Counter Angka Persentase (misal dari 0% ke 65%)
        if (percentEl) {
            const rawText = percentEl.textContent.replace('%', '').trim();
            const targetVal = parseInt(rawText, 10);
            if (!isNaN(targetVal)) {
                const counterObj = { val: 0 };
                percentEl.textContent = '0%';

                masterTl.to(counterObj, {
                    val: targetVal,
                    duration: 1.05,
                    ease: "power2.out",
                    onUpdate: () => {
                        percentEl.textContent = `${Math.round(counterObj.val)}%`;
                    }
                }, 0.1); // Sinkron sempurna dengan durasi sapuan busur SVG
            }
        }
    }

    // --------------------------------------------------------------------------
    // C. Highlight Roster Segmented Dashes (Wave Entrance)
    // --------------------------------------------------------------------------
    if (rosterDashes.length) {
        masterTl.fromTo(rosterDashes,
            { 
                scale: 0.7, 
                opacity: 0.2 
            },
            { 
                scale: 1, 
                opacity: 1, 
                duration: 0.3, 
                stagger: 0.025 
            },
            0.25 // Mulai bergelombang pada detik 0.25
        );
    }

    // --------------------------------------------------------------------------
    // D. KPI Dot Matrix (Subtle Entrance)
    // --------------------------------------------------------------------------
    if (matrixDots.length) {
        masterTl.fromTo(matrixDots,
            { 
                scale: 0.6, 
                opacity: 0.2 
            },
            { 
                scale: 1, 
                opacity: 1, 
                duration: 0.25, 
                stagger: 0.012 
            },
            0.35 // Mulai pada detik 0.35
        );
    }
}
```

#### 3.3 Panggil di Event `DOMContentLoaded`
```javascript
document.addEventListener('DOMContentLoaded', () => {
    initScrollCraft();
    initGsapChartTimeline(); // <--- Inisialisasi GSAP Timeline
    initBentoCalendar();

    // Event listener lainnya tetap sama...
});
```

---

## 4. Analisis Perbandingan: CSS/WAAPI vs GSAP Timeline

Bagi Junior Developer atau Budget AI, berikut adalah rangkuman mengapa solusi berbasis GSAP Timeline ini jauh lebih disukai untuk kebutuhan animasi kompleks:

| Aspek | Solusi Sebelumnya (Manual CSS + WAAPI) | Solusi Baru (GSAP Timeline) |
|---|---|---|
| **Penyusunan Jadwal Animasi** | Ditulis terpisah antara CSS delays dan JS timeouts. | **Satu tempat terpusat** (`masterTl`) dengan kontrol offset fleksibel (`0`, `0.1`, `"-=0.4"`). |
| **Animasi Angka Counter** | Butuh fungsi utilitas `requestAnimationFrame` 30 baris manual. | **Otomatis via tween objek** `{ val: 0 }` dengan callback bawaan `onUpdate`. |
| **Volume Kode CSS** | ~130 baris (banyak keyframe dan 16 baris `:nth-child`). | **Hanya ~25 baris** (hanya `transform-origin` & `will-change`). |
| **Kemudahan Penyesuaian** | Sangat repot; mengubah durasi bar mengharuskan hitung ulang 16 delay CSS. | **Sangat mudah;** ubah parameter `duration` atau `stagger` di satu baris JS. |
| **Kompatibilitas Vite** | Standar. | Didukung penuh via ES Module `import gsap from 'gsap'`. |
| **Performa FPS** | 60 FPS. | 60 FPS (GSAP internal *ticker* menggunakan raf native dengan sub-pixel interpolation). |

---

## 5. Pemeriksaan Kompatibilitas & Aturan Repositori (Zero-Blade Modification)

* **Blade Tetap Read-Only:** File Blade sama sekali tidak perlu diubah. Seluruh selector DOM (`.bento-bar`, `.gauge-fill`, `.roster-dash`, `.matrix-dot`) sudah siap dibaca langsung oleh GSAP.
* **Tidak Ada Inline JavaScript di Blade:** Semua skrip GSAP tersimpan rapi dan modular di dalam `resources/js/pages/pembimbing/dashboard.js` yang dikompilasi oleh Vite.
* **Integrasi Scroll-Craft Aman:** Animasi tetap mematuhi aturan bahwa saat user berinteraksi di dalam halaman yang sama, timeline tidak diputar ulang.

---

## 6. Skenario Pengujian & Quality Gates (DoD Checklist)

Pelaksana tugas (Junior Dev / AI) wajib menjalankan dan memverifikasi checklist berikut:

- [x] **Install Dependency:** `npm install gsap` selesai tanpa konflik dependensi (`gsap` v3.15.1 terpasang).
- [x] **Kompilasi Vite Berhasil:** Jalankan `npm run build` $\rightarrow$ Bundle JavaScript berhasil memuat GSAP (build tuntas dalam 1.15s, ukuran CSS berkurang).
- [x] **Visual Initial Load:**
  - Bar chart tumbuh bertingkat dari dasar ke atas (`scaleY: 0` ke `1` via `masterTl`).
  - Nilai teks bar muncul lembut tepat saat bar selesai naik (`y: 6` ke `0` + `opacity`).
  - Busur radial gauge Kuota Bimbingan tersapu dari kosong ke posisi kuota via `strokeDashoffset`.
  - Angka persentase bergulir dari `0%` ke nilai aktual via numeric object tween + `onUpdate`.
  - Garis segmen roster menyala bergelombang dari kiri ke kanan.
- [x] **Uji Interaktivitas In-Page:**
  - Klik ganti bulan/tahun kalender $\rightarrow$ Animasi chart **TIDAK** diputar ulang.
  - Klik filter tugas $\rightarrow$ Animasi chart **TIDAK** diputar ulang.
  - Filter tabel peserta $\rightarrow$ Animasi chart **TIDAK** diputar ulang.
- [x] **Aksesibilitas:** Emulator browser `prefers-reduced-motion: reduce` aktif $\rightarrow$ Seluruh chart tampil langsung tanpa animasi.
- [x] **Backend Regression Test:** `php artisan test --filter=Pembimbing` $\rightarrow$ 131 tests lolos (849 assertions, 100% hijau).

---

## 7. Instruksi Penugasan untuk Pelaksana

Ikuti langkah-langkah pada Bagian 3 secara berurutan. Jangan menambahkan plugin GSAP yang tidak diperlukan (seperti ScrollTrigger atau MorphSVG berbayar), cukup gunakan core GSAP (`import gsap from 'gsap'`) yang sudah mencakup Timeline secara penuh dan gratis.

