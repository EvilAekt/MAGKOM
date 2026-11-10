# Senior Developer Task Brief (PRD & Implementation Plan)
## Penambahan Aksen Warna Top Strip pada Kartu Tugas Kanban (Profil 360° Peserta)

- **Target Persona Pelaksana**: Junior Developer / AI Model (Gemini 3.8 Flash / Claude Haiku / Cursor)
- **Modul**: Pembimbing Portal — Profil 360° Peserta Bimbingan (`/pembimbing/peserta/{id}`)
- **Dokumen Referensi**: Mockup Target `media_1794223011884_51edb580.png` vs Kondisi Saat Ini `media_1794222996028_68bc3a9f.png`, PRD MagKom v1.1
- **Tanggal Dibuat**: 09 November 2026
- **Status**: Ready for Implementation (Awaiting Confirmation)

---

## 1. Executive Summary & Problem Statement

### 1.1 Masalah Saat Ini (Current State)
Pada papan kanban **Riwayat Penugasan** (`.mentee-kanban-board-card`), kartu tugas (`.kanban-task-card`) saat ini dirender sebagai blok warna datar (*flat solid card*) tanpa aksen pembeda di bagian atasnya:
- Kolom **Belum**: Kartu Dark Navy datar polos (`#0F3875`).
- Kolom **Proses**: Kartu Kuning Amber datar polos (`#F59E0B`).
- Kolom **Selesai**: Kartu Royal Blue datar polos (`#2563EB`).

### 1.2 Target Desain Sesuai Mockup Referensi (Target State)
Sesuai mockup resmi pada gambar referensi (`media_1794223011884_51edb580.png`), setiap kartu tugas memiliki **pita aksen warna kontras (*top accent strip / header cap*)** setebal **`6px`** di tepi atas kartu:
1. **Kartu Belum (Dark Navy)**: Memiliki pita aksen atas berwarna **Silver Grey / Slate (`#94A3B8`)** yang selaras dengan dot indikator kolom "Belum".
2. **Kartu Proses (Vibrant Amber)**: Memiliki pita aksen atas berwarna **Deep Navy (`#0A1E3F` / `#0F2C59`)**, memberikan kontras gelap yang tajam di atas kartu kuning.
3. **Kartu Selesai (Royal Blue)**: Memiliki pita aksen atas berwarna **Vibrant Amber (`#F59E0B`)**, menyelaraskan kembali identitas warna aksen utama MagKom di atas biru royal.

---

## 2. Tabel Spesifikasi Desain & Desain Token

| Kolom Status | Kelas CSS Kartu | Warna Latar Kartu (Body) | Warna Aksen Atas (Top Accent Strip) | Nilai Hex / Variabel CSS |
| :--- | :--- | :--- | :--- | :--- |
| **Belum Dimulai** | `.card-style-belum` | Dark Navy (`#0F3875`) | **Silver Slate Grey** | `#94A3B8` (Senada dengan dot status) |
| **Dalam Pengerjaan** | `.card-style-proses` | Vibrant Amber (`#F59E0B`) | **Deep Dark Navy** | `var(--mentee-navy-darker)` (`#0A1E3F`) |
| **Telah Selesai** | `.card-style-selesai` | Royal Blue (`#2563EB`) | **Vibrant Amber Yellow** | `var(--mentee-amber)` (`#F59E0B`) |

### Parameter Dimensi Pita Aksen:
- **Tinggi Pita (*Height*)**: `6px`
- **Posisi (*Positioning*)**: `absolute; top: 0; left: 0; right: 0;`
- **Kelengkungan Sudut (*Border Radius*)**: Mengikuti kelengkungan kartu atas `border-radius: 14px 14px 0 0;` (diproteksi dengan `overflow: hidden;` pada kartu induk).
- **Z-Index**: `1` (memastikan strip berada tepat di atas background kartu dan tidak menutupi teks).

---

## 3. Strict Guardrails & Aturan Repositori

1. **Blade Read-Only Rule**:
   - Struktur Blade di `resources/views/pembimbing/peserta/show.blade.php` **SUDAH BENAR & SEMPURNA**:
     ```blade
     <div class="kanban-task-card card-style-belum"> ... </div>
     <div class="kanban-task-card card-style-proses"> ... </div>
     <div class="kanban-task-card card-style-selesai"> ... </div>
     ```
   - **DILARANG KERAS** mengubah file Blade atau menambahkan elemen HTML tambahan seperti `<div>` kosong di dalam Blade.
   - Perubahan diimplementasikan murni menggunakan pseudo-element CSS `::before`.
2. **Satu Sumber Perubahan**:
   - Seluruh perubahan dilakukan pada file stylesheet `resources/css/pages/pembimbing/peserta-show.css`.
3. **Asset Build Mandatory**:
   - Wajib menjalankan `npm run build` setelah perubahan CSS.
4. **Verifikasi Test Suite**:
   - Jalankan `tests/Feature/PembimbingPortalStandardizationTest.php` menggunakan runtime PHP 8.3 aktual.

---

## 4. Instruksi Langkah Demi Langkah untuk Junior Developer / AI

### Langkah 1: Modifikasi `.kanban-task-card` di `peserta-show.css`
Buka file `resources/css/pages/pembimbing/peserta-show.css` sekitar baris 715–735:
Pastikan `.kanban-task-card` memiliki `position: relative;` dan `overflow: hidden;`:

```css
.kanban-task-card {
    position: relative;
    overflow: hidden;
    border-radius: 14px;
    padding: 1.25rem 1.25rem 1.15rem; /* Tambah sedikit padding atas untuk memberi ruang napas dari strip 6px */
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

/* Pseudo-element dasar untuk pita aksen atas */
.kanban-task-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 6px;
    border-radius: 14px 14px 0 0;
    z-index: 1;
}
```

### Langkah 2: Tambahkan Warna Aksen Spesifik per Status Tugas
Pada bagian rule gaya kartu (`.card-style-belum`, `.card-style-proses`, `.card-style-selesai`), tambahkan penugasan warna untuk pseudo-element `::before`:

```css
/* Card Style: Belum (Navy) + Top Strip Slate Grey */
.card-style-belum {
    background-color: var(--mentee-navy-card);
    color: #FFFFFF;
}
.card-style-belum::before {
    background-color: #94A3B8;
}

/* Card Style: Proses (Amber) + Top Strip Deep Navy */
.card-style-proses {
    background-color: var(--mentee-amber);
    color: var(--mentee-text-navy);
}
.card-style-proses::before {
    background-color: var(--mentee-navy-darker);
}

/* Card Style: Selesai (Royal Blue) + Top Strip Vibrant Amber */
.card-style-selesai {
    background-color: var(--mentee-royal-blue);
    color: #FFFFFF;
}
.card-style-selesai::before {
    background-color: var(--mentee-amber);
}
```

### Langkah 3: Kompilasi Asset Frontend
Jalankan kompilasi Vite:
```bash
npm run build
```
Pastikan kompilasi sukses 100%.

### Langkah 4: Pengujian & Quality Gate
Jalankan test suite menggunakan runtime PHP 8.3:
```bash
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test tests/Feature/PembimbingPortalStandardizationTest.php
```
Pastikan seluruh 5 test (73 assertions) lolos tanpa error.

---

## 5. Kriteria Penerimaan (Acceptance Criteria)

- [ ] Setiap kartu tugas `.kanban-task-card` memiliki pita aksen atas setebal `6px`.
- [ ] Pita aksen pada kartu Belum berwarna `#94A3B8` (Slate Grey).
- [ ] Pita aksen pada kartu Proses berwarna `#0A1E3F` (Deep Navy).
- [ ] Pita aksen pada kartu Selesai berwarna `#F59E0B` (Vibrant Amber).
- [ ] Pita melengkung sempurna mengikuti sudut kartu `14px` tanpa overflow/bocor.
- [ ] Saat di-hover, efek animasi kartu (`transform: translateY(-2px)`) berjalan mulus bersama pita aksennya.
- [ ] File Blade `show.blade.php` tidak mengalami penambahan markup/script (100% pure CSS).
- [ ] Kompilasi `npm run build` sukses 100%.
- [ ] Seluruh test feature PHPUnit lolos 100%.

