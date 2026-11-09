# Senior Developer Task Brief (PRD & Implementation Plan)
## Penghapusan Teks Fallback Guru Pembimbing & Integrasi Animasi GSAP Timeline pada Halaman Profil Peserta Bimbingan

- **Target Persona Pelaksana**: Junior Developer / AI Model (Gemini 3.8 Flash / Claude Haiku / Cursor)
- **Modul**: Pembimbing Portal — Halaman Profil 360° Peserta Bimbingan (`/pembimbing/peserta/{id}`)
- **Referensi Dokumentasi**: GSAP Timeline Official Docs (`https://gsap.com/docs/v3/GSAP/Timeline/`), PRD MagKom v1.1, Design System MagKom
- **Tanggal Dibuat**: 09 November 2026
- **Status**: Ready for Implementation (Awaiting Confirmation)

---

## 1. Executive Summary & Problem Statements

### 1.1 Masalah 1: Teks Fallback yang Mengganggu di Biodata Mentee
Pada kartu profil mentee (`.mentee-hero-card`), ketika data guru pembimbing (untuk jenjang SMK) atau dosen pembimbing (untuk jenjang Kampus/PT) belum diisi oleh mentee di profilnya, sistem saat ini merender teks fallback dalam format miring (*italics*):
> *"Data guru pembimbing sekolah belum diisi."* (atau *"Data dosen pembimbing kampus belum diisi."*)

**Dampak**:
- Teks ini dinilai redundan dan merusak estetika *clean aesthetic* kartu hero berwarna Deep Navy (`#0F3875`).
- User meminta teks fallback ini **dihapus sepenuhnya**. Jika nama guru/dosen belum diisi, baris keterangan tersebut tidak perlu ditampilkan sama sekali (*zero clutter*). Baris hanya ditampilkan jika nama guru/dosen pembimbing terisi.

### 1.2 Masalah 2: Halaman Terkesan Statis saat Pertama Kali Dimuat (Initial Load)
Portal Pembimbing MagKom telah memiliki dependensi `gsap@^3.15.1` yang terpasang di `package.json`, namun halaman Profil 360° Peserta Bimbingan (`/pembimbing/peserta/{id}`) belum mengimplementasikan animasi pembuka (*initial entrance animation*).

**Target Solusi**:
Mengimplementasikan orkestrasi animasi masuk yang halus, elegan, dan profesional menggunakan **GSAP Timeline** (`gsap.timeline({ defaults: { ... } })`) yang mengalir secara berjenjang (*staggered cascade*) dari atas ke bawah:
1. **Top Action & Date Bar** (meluncur lembut dari atas).
2. **Hero Card Profil Mentee & Avatar Ring** (fade up + aksen pop pada avatar ring emas neon).
3. **3 Kartu Ringkasan KPI** (stagger up: Donut Chart spin-scale, Progress Bar horizontal fill, Logbook 12-weeks chips cascade).
4. **Riwayat Penugasan Kanban Board** (Segmented bar expand, 3 kolom kanban slide up, dan kartu tugas cascade waterfall).

---

## 2. Technical Architecture & Repository Guardrails

Mengikuti aturan ketat **`.agents/rules/AGENTS.md`**:

| Guardrail | Ketentuan Wajib |
| :--- | :--- |
| **No Script in Blade** | **DILARANG KERAS** menulis tag `<script>` atau kode animasi inline di dalam `show.blade.php`. |
| **Modular ES Modules** | Seluruh kode JavaScript animasi ditempatkan pada file modular: `resources/js/pages/pembimbing/peserta-show.js`. |
| **Vite Entry Point** | Daftarkan file baru di `vite.config.js` pada array `plugins -> laravel -> input`. |
| **Blade Inclusion** | Muat modul via direktif resmi `@push('scripts')` di Blade: `@vite(['resources/js/pages/pembimbing/peserta-show.js'])`. |
| **Accessibility (WCAG 2.2)** | Wajib memeriksa `window.matchMedia('(prefers-reduced-motion: reduce)')`. Jika aktif, bypass seluruh animasi transform agar menghormati preferensi pengguna berkebutuhan khusus. |
| **No FOUC / Content Hiding** | Elemen di CSS tetap mempertahankan `opacity: 1` default. Animasi menggunakan `gsap.from()` atau `gsap.fromTo()` sehingga jika JavaScript nonaktif, halaman tetap tampil sempurna 100%. |

---

## 3. Choreography & Timeline Sequence Specification

Berdasarkan spesifikasi resmi [GSAP Timeline v3](https://gsap.com/docs/v3/GSAP/Timeline/):

```
Time (s):  0.0s       0.1s       0.2s       0.3s       0.45s      0.6s       0.8s      1.0s
Top Bar:   [===Fade/Slide===]
Hero Card:            [========Slide Up & Fade========]
Avatar Ring:                   [====Pop & Glow====]
KPI Cards:                                [======Stagger 3 Cards======]
Donut Chart:                                         [===Scale & Rotate===]
Progress Bar:                                        [====ScaleX Fill====]
Logbook 12:                                                 [==12 Chips==]
Kanban Board:                                                          [=====Columns & Cards=====]
```

### Parameter Detail Setiap Tahapan:

#### Tahap 1: Top Action & Date Bar ($t = 0.0s$)
- **Target**: `.mentee-date-action-bar`
- **Animasi**: `gsap.fromTo(el, { opacity: 0, y: -16 }, { opacity: 1, y: 0, duration: 0.45, ease: 'power2.out' })`
- **Aksen**: Pop halus pada `.date-pill-amber` dan `.btn-back-navy` (`scale: 0.95 -> 1`).

#### Tahap 2: Hero Card & Avatar Ring ($t = 0.1s$, overlap)
- **Target Kartu**: `.mentee-hero-card`
- **Animasi Kartu**: `gsap.fromTo(el, { opacity: 0, y: 24 }, { opacity: 1, y: 0, duration: 0.55, ease: 'power3.out' })`
- **Target Avatar**: `.mentee-avatar-gold-ring`
- **Animasi Avatar**: `gsap.fromTo(el, { opacity: 0, scale: 0.75, rotation: -10 }, { opacity: 1, scale: 1, rotation: 0, duration: 0.5, ease: 'back.out(1.6)' })`
- **Target Tombol Aksi Hero**: `.btn-pill-action` (`.btn-pill-logbook`, `.btn-pill-tugas`)
- **Animasi Tombol**: `stagger: 0.08, duration: 0.35, y: 10 -> 0, opacity: 0 -> 1`
- **Target Data Cells**: `.data-peserta-cell`
- **Animasi Data**: `stagger: 0.04, duration: 0.3, x: 12 -> 0, opacity: 0 -> 1`

#### Tahap 3: Tiga Kartu Ringkasan KPI ($t = 0.28s$, overlap)
- **Target Kartu**: `.mentee-kpi-summary-grid .kpi-card` (3 kartu: Blue, Yellow, White)
- **Animasi Kartu**: `gsap.fromTo(el, { opacity: 0, y: 28 }, { opacity: 1, y: 0, stagger: 0.12, duration: 0.5, ease: 'power2.out' })`
- **Mikro-Animasi Kartu 1 (Donut Chart)**:
  - `.donut-chart-circle`: `scale: 0.75 -> 1, rotation: -45 -> 0, duration: 0.6, ease: 'back.out(1.4)'`
  - `.donut-legend-pill`: `stagger: 0.06, x: -10 -> 0, opacity: 0 -> 1`
- **Mikro-Animasi Kartu 2 (Periode Magang)**:
  - `.periode-progress-fill`: `scaleX: 0 -> 1, transformOrigin: 'left center', duration: 0.7, ease: 'power2.out'`
- **Mikro-Animasi Kartu 3 (Logbook 12-Weeks)**:
  - `.logbook-week-box`: `stagger: 0.025, scale: 0.7 -> 1, opacity: 0 -> 1, ease: 'back.out(2)'`

#### Tahap 4: Riwayat Penugasan Kanban ($t = 0.5s$, overlap)
- **Target Header & Board**: `.mentee-kanban-board-card`
- **Animasi Board**: `opacity: 0, y: 20 -> opacity: 1, y: 0, duration: 0.45`
- **Target Segmented Bar**: `.kanban-segmented-bar .segment-fill`
- **Animasi Segment**: `scaleX: 0 -> 1, transformOrigin: 'left center', stagger: 0.06, duration: 0.55`
- **Target Kolom Kanban**: `.kanban-column` (3 kolom: Belum, Proses, Selesai)
- **Animasi Kolom**: `stagger: 0.1, y: 20 -> 0, opacity: 0 -> 1, duration: 0.45`
- **Target Kartu Tugas**: `.kanban-task-card`
- **Animasi Kartu Tugas**: `stagger: 0.04, y: 12 -> 0, opacity: 0 -> 1, duration: 0.35`

---

## 4. Step-by-Step Implementation Instructions

### Langkah 1: Perbarui `resources/views/pembimbing/peserta/show.blade.php`

#### 1.1 Hapus Teks Fallback Guru/Dosen Pembimbing
Buka baris 91–97 di `resources/views/pembimbing/peserta/show.blade.php`:

**Sebelum:**
```blade
<div class="data-peserta-dosen-note">
    @if($isSmk)
        {{ $profil?->nama_pembimbing_sekolah ? 'Guru Pembimbing: ' . $profil->nama_pembimbing_sekolah : 'Data guru pembimbing sekolah belum diisi.' }}
    @else
        {{ $profil?->nama_pembimbing_sekolah ? 'Dosen Pembimbing: ' . $profil->nama_pembimbing_sekolah : 'Data dosen pembimbing kampus belum diisi.' }}
    @endif
</div>
```

**Sesudah:**
```blade
@if($profil?->nama_pembimbing_sekolah)
    <div class="data-peserta-dosen-note">
        {{ ($isSmk ? 'Guru Pembimbing: ' : 'Dosen Pembimbing: ') . $profil->nama_pembimbing_sekolah }}
    </div>
@endif
```

#### 1.2 Tambahkan `@push('scripts')` untuk Modul GSAP
Pada bagian bawah file sebelum `@endsection`, tambahkan:
```blade
@push('scripts')
    @vite(['resources/js/pages/pembimbing/peserta-show.js'])
@endpush
```

---

### Langkah 2: Buat File JavaScript `resources/js/pages/pembimbing/peserta-show.js`

Buat file baru di `resources/js/pages/pembimbing/peserta-show.js`:

```javascript
/**
 * ==============================================================================
 * MagKom Sleman - Profil 360° Peserta Bimbingan GSAP Timeline Entrance Engine
 * Clean, lightweight, professional animated entrance for /pembimbing/peserta/{id}
 * ==============================================================================
 */

import { gsap } from 'gsap';

document.addEventListener('DOMContentLoaded', () => {
    // 1. Accessibility Check: Hormati preferensi reduced-motion
    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
        return; // Jangan jalankan animasi jika pengguna meminta reduced motion
    }

    const pageContainer = document.querySelector('.mentee-profile-page');
    if (!pageContainer) return;

    // 2. Inisialisasi Master Timeline
    const tl = gsap.timeline({
        defaults: {
            ease: 'power2.out',
            duration: 0.5
        }
    });

    // --------------------------------------------------------------------------
    // STAGE 1: Top Action & Date Bar (Slide down & fade)
    // --------------------------------------------------------------------------
    const topBar = pageContainer.querySelector('.mentee-date-action-bar');
    if (topBar) {
        tl.fromTo(topBar, 
            { opacity: 0, y: -16 }, 
            { opacity: 1, y: 0, duration: 0.45 },
            0
        );

        const datePill = topBar.querySelector('.date-pill-amber');
        const backBtn = topBar.querySelector('.btn-back-navy');
        if (datePill && backBtn) {
            tl.fromTo([datePill, backBtn],
                { scale: 0.94, opacity: 0 },
                { scale: 1, opacity: 1, duration: 0.35, stagger: 0.06 },
                0.1
            );
        }
    }

    // --------------------------------------------------------------------------
    // STAGE 2: Mentee Hero Card (Slide up, avatar pop & details reveal)
    // --------------------------------------------------------------------------
    const heroCard = pageContainer.querySelector('.mentee-hero-card');
    if (heroCard) {
        tl.fromTo(heroCard,
            { opacity: 0, y: 24 },
            { opacity: 1, y: 0, duration: 0.55, ease: 'power3.out' },
            0.08
        );

        const avatarRing = heroCard.querySelector('.mentee-avatar-gold-ring');
        if (avatarRing) {
            tl.fromTo(avatarRing,
                { opacity: 0, scale: 0.75, rotation: -10 },
                { opacity: 1, scale: 1, rotation: 0, duration: 0.5, ease: 'back.out(1.6)' },
                0.18
            );
        }

        const actionBtns = heroCard.querySelectorAll('.btn-pill-action');
        if (actionBtns.length) {
            tl.fromTo(actionBtns,
                { opacity: 0, y: 10, scale: 0.92 },
                { opacity: 1, y: 0, scale: 1, stagger: 0.08, duration: 0.35 },
                0.25
            );
        }

        const dataCells = heroCard.querySelectorAll('.data-peserta-cell');
        if (dataCells.length) {
            tl.fromTo(dataCells,
                { opacity: 0, x: 12 },
                { opacity: 1, x: 0, stagger: 0.04, duration: 0.35 },
                0.22
            );
        }
    }

    // --------------------------------------------------------------------------
    // STAGE 3: 3-Column KPI Summary Cards & Mini-Visuals
    // --------------------------------------------------------------------------
    const kpiCards = pageContainer.querySelectorAll('.mentee-kpi-summary-grid .kpi-card');
    if (kpiCards.length) {
        tl.fromTo(kpiCards,
            { opacity: 0, y: 28 },
            { opacity: 1, y: 0, stagger: 0.12, duration: 0.5, ease: 'power2.out' },
            0.28
        );

        // Donut Chart Pop & Rotate
        const donutCircle = pageContainer.querySelector('.donut-chart-circle');
        if (donutCircle) {
            tl.fromTo(donutCircle,
                { opacity: 0, scale: 0.75, rotation: -50 },
                { opacity: 1, scale: 1, rotation: 0, duration: 0.6, ease: 'back.out(1.4)' },
                0.38
            );
        }

        const donutLegends = pageContainer.querySelectorAll('.donut-legend-pill');
        if (donutLegends.length) {
            tl.fromTo(donutLegends,
                { opacity: 0, x: -10 },
                { opacity: 1, x: 0, stagger: 0.06, duration: 0.35 },
                0.42
            );
        }

        // Periode Magang Progress Fill
        const periodeFill = pageContainer.querySelector('.periode-progress-fill');
        if (periodeFill) {
            tl.fromTo(periodeFill,
                { scaleX: 0, transformOrigin: 'left center' },
                { scaleX: 1, duration: 0.7, ease: 'power2.out' },
                0.4
            );
        }

        // Logbook 12-Weeks Boxes Cascade
        const weekBoxes = pageContainer.querySelectorAll('.logbook-week-box');
        if (weekBoxes.length) {
            tl.fromTo(weekBoxes,
                { opacity: 0, scale: 0.7 },
                { opacity: 1, scale: 1, stagger: 0.025, duration: 0.3, ease: 'back.out(2)' },
                0.36
            );
        }
    }

    // --------------------------------------------------------------------------
    // STAGE 4: Riwayat Penugasan (Kanban Board & Task Cards)
    // --------------------------------------------------------------------------
    const kanbanCard = pageContainer.querySelector('.mentee-kanban-board-card');
    if (kanbanCard) {
        tl.fromTo(kanbanCard,
            { opacity: 0, y: 22 },
            { opacity: 1, y: 0, duration: 0.5 },
            0.48
        );

        const segmentFills = kanbanCard.querySelectorAll('.kanban-segmented-bar .segment-fill');
        if (segmentFills.length) {
            tl.fromTo(segmentFills,
                { scaleX: 0, transformOrigin: 'left center' },
                { scaleX: 1, duration: 0.6, stagger: 0.06, ease: 'power2.out' },
                0.55
            );
        }

        const kanbanCols = kanbanCard.querySelectorAll('.kanban-column');
        if (kanbanCols.length) {
            tl.fromTo(kanbanCols,
                { opacity: 0, y: 18 },
                { opacity: 1, y: 0, stagger: 0.09, duration: 0.45 },
                0.58
            );
        }

        const taskCards = kanbanCard.querySelectorAll('.kanban-task-card');
        if (taskCards.length) {
            tl.fromTo(taskCards,
                { opacity: 0, y: 12 },
                { opacity: 1, y: 0, stagger: 0.04, duration: 0.35 },
                0.65
            );
        }
    }
});
```

---

### Langkah 3: Daftarkan File di `vite.config.js`

Buka `vite.config.js`, tambahkan `'resources/js/pages/pembimbing/peserta-show.js'` ke dalam array input (setelah `peserta-index.js`):

```javascript
                'resources/js/pages/pembimbing/peserta-index.js',
                'resources/js/pages/pembimbing/peserta-show.js',
                'resources/js/pages/pembimbing/logbook-index.js',
```

---

### Langkah 4: Perbarui Test Suite di `tests/Feature/PembimbingPortalStandardizationTest.php`

Buka `tests/Feature/PembimbingPortalStandardizationTest.php`:
1. Pada baris 204:
   Ganti:
   ```php
   $response->assertSee('Data guru pembimbing sekolah belum diisi.');
   ```
   Menjadi:
   ```php
   $response->assertDontSee('Data guru pembimbing sekolah belum diisi.');
   ```
2. Pastikan baris 273 tetap terjaga:
   ```php
   $response->assertDontSee('Data guru pembimbing sekolah belum diisi.');
   ```
3. Tambahkan assertion bahwa script `peserta-show.js` termuat via `@vite`:
   ```php
   $response->assertSee('peserta-show.js');
   ```

---

## 5. Quality Gate & Verifikasi Pengujian

Jalankan perintah berikut secara berurutan:

```bash
# 1. Kompilasi asset Vite untuk memverifikasi entri baru
npm run build

# 2. Jalankan test suite portal pembimbing
php artisan test tests/Feature/PembimbingPortalStandardizationTest.php

# 3. Jalankan seluruh suite tes Pembimbing untuk memastikan regresi nol
php artisan test --filter=Pembimbing

# 4. Periksa code style dengan Pint
./vendor/bin/pint --test
```

---

## 6. Checklist Verifikasi Akhir

- [ ] Teks `"Data guru pembimbing sekolah belum diisi."` telah dihapus total dari halaman bila data kosong.
- [ ] Teks `"Data dosen pembimbing kampus belum diisi."` juga tidak muncul bila data kosong.
- [ ] Bila mentee memiliki nama guru pembimbing, teks `"Guru Pembimbing: [Nama]"` tetap muncul dengan benar.
- [ ] File `resources/js/pages/pembimbing/peserta-show.js` terbuat dengan ES Modules resmi dan mengimpor GSAP.
- [ ] `vite.config.js` mendaftarkan `peserta-show.js`.
- [ ] `resources/views/pembimbing/peserta/show.blade.php` memuat script via `@push('scripts')`. Tidak ada inline `<script>` di Blade.
- [ ] Animasi GSAP Timeline menghormati `prefers-reduced-motion`.
- [ ] `npm run build` sukses tanpa warning bundling.
- [ ] Semua test PHPUnit hijau 100%.

