# Senior Developer Task Brief (PRD & Implementation Plan)
## Penambahan Animasi Hover Halus (Smooth Micro-Interactions) pada Kartu KPI, Kartu Tugas Kanban, dan Angka Logbook Mingguan

- **Target Persona Pelaksana**: Junior Developer / AI Model (Gemini 3.8 Flash / Claude Haiku / Cursor)
- **Modul**: Pembimbing Portal — Halaman Profil 360° Peserta Bimbingan (`/pembimbing/peserta/{id}`)
- **Dokumen Referensi**: Tangkapan Layar `profil-360-kpi-dan-kanban-cards.png` (`media_1794225403744_566ed87f.png`), PRD MagKom v1.1, Design System MagKom
- **Tanggal Dibuat**: 09 November 2026
- **Status**: Ready for Implementation (Awaiting Confirmation)

---

## 1. Executive Summary & Problem Statements

### 1.1 Masalah Saat Ini (Interaksi Kurang Responsif & Statis)
Berdasarkan tangkapan layar `profil-360-kpi-dan-kanban-cards.png`, terdapat beberapa komponen yang masih terasa kaku atau tidak memberikan umpan balik visual (*tactile feedback*) saat kursor mouse melintas (*hover*):
1. **Tiga Kartu Ringkasan KPI di Atas (Progres Tugas, Periode Magang, Logbook)**:
   - Belum memiliki efek `:hover` sama sekali. Kartu tetap diam di tempatnya tanpa elevasi bayangan atau pergerakan halus, sehingga terasa seperti elemen cetak mati (*static print layout*).
2. **Kotak Angka 1–12 pada Kartu Logbook (`.logbook-week-box`)**:
   - Elemen ini memiliki `transition: transform 0.15s ease;`, namun **tidak ada aturan `:hover`** yang didefinisikan di CSS! Akibatnya, saat pembimbing mengarahkan kursor ke angka minggu 1 sampai 12, tidak ada reaksi visual, padahal setiap kotak merepresentasikan status logbook mingguan mentee yang interaktif.
3. **Kartu Tugas Kanban (`.kanban-task-card`)**:
   - Saat ini hanya memiliki hover sederhana `transform: translateY(-2px); box-shadow: 0 6px 14px rgba(0,0,0,0.1);` dengan timing function `ease` standar. Gerakannya terasa kaku dan belum memanfaatkan *ambient colored glow* sesuai warna aksen kartu (Belum, Proses, Selesai).

### 1.2 Target Solusi (Smooth Micro-Interactions Standard SaaS)
Menerapkan serangkaian *smooth micro-interactions* berbasis CSS murni di `resources/css/pages/pembimbing/peserta-show.css`:
- **Fluid Spring Timing Function**: Menggunakan kurva `cubic-bezier(0.16, 1, 0.3, 1)` untuk pergerakan kartu dan `cubic-bezier(0.34, 1.56, 0.64, 1)` (efek spring pop halus) untuk chip angka logbook.
- **Elevasi Bertingkat & Ambient Glow**: Saat kartu di-hover, kartu terangkat lembut ($-4\text{px}$) dengan bayangan lembut yang menyebar (*soft diffused drop shadow*).
- **Angka Logbook Pop-Out**: Angka kotak 1–12 membesar lembut ($1.12\times$), sedikit terangkat ($-3\text{px}$), memiliki `z-index: 2` agar berada di atas kotak sebelahnya, serta memancarkan pendaran halus sesuai statusnya (Biru Dongker untuk Selesai, Merah untuk Terlambat, Slate untuk Mendatang).

---

## 2. Tabel Spesifikasi Interaksi Hover (Before vs After)

| Komponen & Elemen | Kondisi Saat Ini (Current) | Target Desain Baru (Target) | Easing & Timing |
| :--- | :--- | :--- | :--- |
| **Kartu 1: Progres Tugas (`.kpi-card-blue`)** | Tidak ada hover | `translateY(-4px)` + deep blue glow `box-shadow: 0 12px 28px rgba(40, 72, 122, 0.4)` + donut chart scale $1.03\times$ | `0.25s cubic-bezier(0.16, 1, 0.3, 1)` |
| **Kartu 2: Periode Magang (`.kpi-card-yellow`)** | Tidak ada hover | `translateY(-4px)` + golden amber glow `box-shadow: 0 12px 28px rgba(255, 195, 0, 0.45)` + angka hari scale $1.02\times$ | `0.25s cubic-bezier(0.16, 1, 0.3, 1)` |
| **Kartu 3: Logbook (`.kpi-card-white`)** | Tidak ada hover | `translateY(-4px)` + clean shadow `box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12)` + border highlight `#CBD5E1` | `0.25s cubic-bezier(0.16, 1, 0.3, 1)` |
| **Chip Angka Logbook 1–12 (`.logbook-week-box`)** | Tidak ada efek hover (hanya transition kosong) | `cursor: pointer; translateY(-3px) scale(1.14); z-index: 2;` + pendaran warna status | `0.2s cubic-bezier(0.34, 1.56, 0.64, 1)` (Spring Pop) |
| - *Kotak Selesai (`.box-selesai`)* | Statis Navy | Hover: `filter: brightness(1.2); box-shadow: 0 4px 12px rgba(10, 30, 63, 0.4);` | `0.2s ease` |
| - *Kotak Terlambat (`.box-terlambat`)* | Statis Red | Hover: `filter: brightness(1.15); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.45);` | `0.2s ease` |
| - *Kotak Pending (`.box-pending`)* | Statis Gray | Hover: Background `#CBD5E1`, text `#0A1E3F`, `box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);` | `0.2s ease` |
| **Kartu Tugas Kanban (`.kanban-task-card`)** | `translateY(-2px)`, shadow kaku | `translateY(-4px)` + ambient glow spesifik status (Slate untuk Belum, Gold untuk Proses, Blue untuk Selesai) | `0.25s cubic-bezier(0.16, 1, 0.3, 1)` |

---

## 3. Strict Repository Guardrails

1. **Blade Read-Only**: File `resources/views/pembimbing/peserta/show.blade.php` **TIDAK PERLU DIUBAH SAMA SEKALI**. Seluruh markup HTML dan class sudah lengkap.
2. **Satu Sumber Perubahan**: Seluruh perubahan dieksekusi secara eksklusif pada:
   `resources/css/pages/pembimbing/peserta-show.css`.
3. **No Breaking Layout**: Hover hanya menggunakan `transform` dan `box-shadow` (tidak mengubah `margin`, `padding`, atau dimensi kotak) sehingga layout halaman tidak bergeser (*zero layout thrashing* / Cumulative Layout Shift = 0).
4. **Hardware Acceleration**: Gunakan `will-change: transform, box-shadow;` pada elemen hover agar rendering dijalankan oleh GPU secara 60fps mentok (*butter smooth*).
5. **Asset Compilation**: Wajib menjalankan `npm run build` setelah perubahan CSS.
6. **Verifikasi Test Suite**: Jalankan `tests/Feature/PembimbingPortalStandardizationTest.php` dengan runtime PHP 8.3 aktual.

---

## 4. Step-by-Step Implementation Instructions

### Langkah 1: Tambahkan Hover State pada Tiga Kartu KPI (`.kpi-card`)

Buka `resources/css/pages/pembimbing/peserta-show.css` sekitar baris 320:
Perbarui `.kpi-card` dan tambahkan transition:

```css
.kpi-card {
    border-radius: 16px;
    padding: 1.35rem 1.5rem;
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.25s ease;
    will-change: transform, box-shadow;
}

.kpi-card:hover {
    transform: translateY(-4px);
}
```

Tambahkan spesifikasi shadow khusus untuk masing-masing varian kartu:

```css
/* Kartu 1: Progres Tugas (Deep Blue) */
.kpi-card-blue:hover {
    box-shadow: 0 12px 28px rgba(40, 72, 122, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.12);
}

.kpi-card-blue:hover .donut-chart-circle {
    transform: scale(1.03);
}

/* Donut Chart smooth transition */
.donut-chart-circle {
    /* ... existing properties ... */
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

/* Kartu 2: Periode Magang (Vibrant Yellow) */
.kpi-card-yellow:hover {
    box-shadow: 0 12px 28px rgba(255, 195, 0, 0.45), 0 0 0 1px rgba(10, 30, 63, 0.08);
}

.kpi-card-yellow:hover .periode-metric-huge {
    transform: scale(1.02);
}

/* Periode metric huge smooth transition */
.periode-metric-huge {
    /* ... existing properties ... */
    transform-origin: left bottom;
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

/* Kartu 3: Logbook (White Surface) */
.kpi-card-white:hover {
    border-color: #CBD5E1;
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
}
```

---

### Langkah 2: Tambahkan Animasi Hover Pop Halus pada Angka Logbook (`.logbook-week-box`)

Buka `resources/css/pages/pembimbing/peserta-show.css` sekitar baris 540:
Perbarui `.logbook-week-box` dan variannya:

```css
.logbook-week-box {
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8125rem;
    font-weight: 700;
    cursor: pointer;
    position: relative;
    user-select: none;
    transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease, filter 0.2s ease, background-color 0.2s ease;
    will-change: transform, box-shadow;
}

.logbook-week-box:hover {
    transform: translateY(-3px) scale(1.14);
    z-index: 2;
}

/* Pendaran saat hover pada kotak yang sudah selesai */
.box-selesai:hover {
    filter: brightness(1.2);
    box-shadow: 0 4px 12px rgba(10, 30, 63, 0.4);
}

/* Pendaran saat hover pada kotak yang terlambat */
.box-terlambat:hover {
    filter: brightness(1.15);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.45);
}

/* Interaksi saat hover pada kotak yang belum diisi (pending) */
.box-pending:hover {
    background-color: #CBD5E1;
    color: var(--mentee-navy-deep);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
}
```

---

### Langkah 3: Sempurnakan Hover pada Kartu Tugas Kanban (`.kanban-task-card`)

Buka `resources/css/pages/pembimbing/peserta-show.css` sekitar baris 740–765:
Perbarui transition dan tambahkan ambient glow untuk masing-masing status:

```css
.kanban-task-card {
    /* ... existing properties ... */
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease;
    will-change: transform, box-shadow;
}

.kanban-task-card:hover {
    transform: translateY(-4px);
}

/* Ambient glow kartu Belum */
.card-style-belum:hover {
    box-shadow: 0 10px 24px rgba(10, 30, 63, 0.35), 0 0 0 1px rgba(148, 163, 184, 0.3);
}

/* Ambient glow kartu Proses */
.card-style-proses:hover {
    box-shadow: 0 10px 24px rgba(255, 195, 0, 0.38), 0 0 0 1px rgba(10, 30, 63, 0.18);
}

/* Ambient glow kartu Selesai */
.card-style-selesai:hover {
    box-shadow: 0 10px 24px rgba(37, 99, 235, 0.38), 0 0 0 1px rgba(255, 195, 0, 0.3);
}
```

---

## 5. Quality Gate & Verifikasi Pengujian

Jalankan perintah berikut:

```bash
# 1. Kompilasi asset Vite
npm run build

# 2. Jalankan unit/feature test portal pembimbing
php artisan test tests/Feature/PembimbingPortalStandardizationTest.php

# 3. Jalankan suite pembimbing lengkap
php artisan test --filter=Pembimbing

# 4. Periksa code style Pint
./vendor/bin/pint --test
```

---

## 6. Checklist Verifikasi Akhir

- [ ] Kartu Progres Tugas terangkat halus saat di-hover dan memiliki pendaran biru tua.
- [ ] Kartu Periode Magang terangkat halus saat di-hover dan memiliki pendaran kuning emas Sleman.
- [ ] Kartu Logbook terangkat halus saat di-hover dan bayangannya menyebar rapi.
- [ ] Angka 1 sampai 12 pada kotak logbook membesar halus (*spring pop*) saat kursor diarahkan, dengan kursor pointer dan bayangan lembut.
- [ ] Kartu tugas Kanban terangkat $-4\text{px}$ secara elastis dengan ambient shadow yang sesuai dengan warna masing-masing kolom.
- [ ] Tidak ada pergeseran tata letak halaman (CLS = 0).
- [ ] Tidak ada perubahan pada file Blade (`show.blade.php` tetap read-only).
- [ ] `npm run build` sukses 100%.
- [ ] Semua tes PHPUnit hijau.

