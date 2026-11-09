# Senior Developer Task Brief (PRD & Implementation Plan)
## Perbesaran Circular Avatar Ring Inisial Peserta pada Mentee Hero Card (Profil 360° Peserta)

- **Target Persona Pelaksana**: Junior Developer / AI Model (Gemini 3.8 Flash / Claude Haiku / Cursor)
- **Modul**: Pembimbing Portal — Profil 360° Peserta Bimbingan (`/pembimbing/peserta/{id}`)
- **Dokumen Referensi**: Mockup Visual `media_1794221628599_1b7074be.png` (Crop Avatar Ring "NA"), PRD MagKom v1.1, Design System MagKom
- **Tanggal Dibuat**: 09 November 2026
- **Status**: Ready for Implementation (Awaiting Confirmation)

---

## 1. Executive Summary & Problem Statement

### 1.1 Konteks Masalah
Pada kartu utama **Mentee Hero Card** (`.mentee-hero-card`), elemen grafis melingkar berupa **Avatar Inisial Peserta dengan Ring Neon Amber** (`.mentee-avatar-gold-ring`) saat ini berukuran diameter **`84px` x `84px`** dengan ketebalan border `3.5px` dan ukuran inisial `1.875rem` (30px).

Setelah pembaruan sebelumnya:
1. Kartu hero telah diperluas padding-nya menjadi `2.25rem 2.5rem` (total tinggi kartu mencapai ~240px–260px).
2. Tumpukan identitas di samping avatar (Nama `1.5rem`, Institusi, status badges, dan dua tombol aksi pill `[Logbook]` & `[Tugas]` dengan tinggi `44px`) memiliki rentang vertikal lebih dari `150px`.
3. Donut chart pada kartu *Progres Tugas* di baris bawahnya telah diskalakan ke ukuran `124px` x `124px`.

Akibatnya, lingkaran inisial avatar (`84px`) terlihat terlalu kecil, tenggelam (*sunken*), dan tidak proporsional terhadap luas kartu hero navy `#0F3875` maupun tumpukan teks dan tombol di sampingnya. Pengguna secara spesifik meminta agar elemen melingkar ini diperbesar dan diselaraskan proporsinya terhadap kartu.

### 1.2 Target Solusi
Meningkatkan skala diameter avatar melingkar dari **`84px`** menjadi **`120px` x `120px`** (menyesuaikan secara harmonis dengan tinggi tumpukan teks hero dan ukuran chart donut 124px), menebalkan cincin neon amber menjadi **`4.5px`**, memperbesar tipografi inisial menjadi **`2.5rem` (40px)**, memperlebar pancaran cahaya (*box-shadow glow*), serta memberikan margin pernapasan horizontal yang cukup terhadap blok identitas teks.

---

## 2. Tabel Perbandingan Metrik (Current vs Target)

| Parameter Desain | Nilai Saat Ini (Current) | Nilai Target (Target Sizing) | Alasan Teknis & Desain |
| :--- | :--- | :--- | :--- |
| **Diameter Lingkaran** | `84px` x `84px` (`min-width: 84px`) | **`120px` x `120px`** (`min-width: 120px`) | Proporsional dengan tinggi blok teks + tombol (150px) dan chart donut (124px) |
| **Ketebalan Ring Amber** | `3.5px solid var(--mentee-amber)` | **`4.5px solid var(--mentee-amber)`** | Cincin tidak terlihat tipis pada diameter 120px; menyamai visual weight chart |
| **Warna Ring & Inisial** | `#F59E0B` (`var(--mentee-amber)`) | **`#F59E0B` (`var(--mentee-amber)`)** | Mempertahankan Single Source of Truth warna kuning/amber MagKom |
| **Warna Latar Lingkaran** | `#0A1E3F` (`var(--mentee-navy-darker)`) | **`#0A1E3F` (`var(--mentee-navy-darker)`)** | Kontras tinggi terhadap kartu navy `#0F3875` |
| **Ukuran Font Inisial (`<span>`)** | `1.875rem` (30px, 800) | **`2.5rem` (40px, 800, `letter-spacing: -0.02em`)** | Keterbacaan inisial (mis. "NA") tegas dan mengisi ruang dalam lingkaran secara pas |
| **Efek Neon Glow (`box-shadow`)** | `0 0 16px rgba(245, 158, 11, 0.28)` | **`0 0 24px rgba(245, 158, 11, 0.35)`** | Pancaran cahaya amber lebih halus dan dimensional pada diameter yang lebih besar |
| **Jarak Kolom Kiri (`.mentee-hero-left gap`)** | `1.5rem` (24px) | **`1.75rem` (28px)** | Memberikan ruang pernapasan visual antara avatar besar dan teks nama |
| **Responsif Mobile (`<= 640px`)** | (Default 84px) | **`100px` x `100px`** (`font-size: 2.125rem`, `border: 4px`) | Tetap proporsional pada tampilan mobile portrait tanpa memakan layar berlebih |

---

## 3. Strict Guardrails & Aturan Repositori

1. **Aturan Blade Read-Only**:
   - Struktur HTML di `resources/views/pembimbing/peserta/show.blade.php` **SUDAH BENAR & SEMPURNA**:
     ```html
     <div class="mentee-avatar-gold-ring">
         <span>{{ strtoupper(substr($penempatan->peserta?->nama ?? 'P', 0, 2)) }}</span>
     </div>
     ```
   - **DILARANG KERAS** menambahkan inline style (`style="..."`), tag `<style>`, atau script apa pun di dalam file Blade!
   - File Blade **TIDAK PERLU DIUBAH SAMA SEKALI**.
2. **Satu Sumber Perubahan**:
   - Seluruh perubahan murni dilakukan pada file stylesheet `resources/css/pages/pembimbing/peserta-show.css`.
3. **Pilar Token Warna**:
   - Warna amber ring wajib tetap menggunakan variabel CSS `var(--mentee-amber)` (`#F59E0B`).
   - Warna latar belakang lingkaran tetap `var(--mentee-navy-darker)` (`#0A1E3F`).
4. **Wajib Build Asset**:
   - Setiap modifikasi CSS wajib diakhiri dengan eksekusi `npm run build`.
5. **Verifikasi Test Suite**:
   - Jalankan `tests/Feature/PembimbingPortalStandardizationTest.php` menggunakan PHP 8.3 aktual.

---

## 4. Instruksi Langkah Demi Langkah untuk Junior Developer / AI

### Langkah 1: Modifikasi `.mentee-hero-left` dan `.mentee-avatar-gold-ring` di CSS
Buka file `resources/css/pages/pembimbing/peserta-show.css` pada baris 120–145, perbarui rule berikut:

```css
.mentee-hero-left {
    display: flex;
    align-items: center;
    gap: 1.75rem; /* Ditingkatkan dari 1.5rem untuk memberi ruang avatar 120px */
}

.mentee-avatar-gold-ring {
    width: 120px;
    height: 120px;
    min-width: 120px;
    border-radius: 50%;
    background-color: var(--mentee-navy-darker);
    border: 4.5px solid var(--mentee-amber);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--mentee-amber);
    font-size: 2.5rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    box-shadow: 0 0 24px rgba(245, 158, 11, 0.35);
    flex-shrink: 0;
}
```

### Langkah 2: Tambahkan Penyesuaian Mobile pada Breakpoint `@media (max-width: 640px)`
Pada bagian responsive di akhir file `resources/css/pages/pembimbing/peserta-show.css`, di dalam block `@media (max-width: 640px)`, pastikan avatar mengecil secara wajar:

```css
@media (max-width: 640px) {
    /* ... rules lain tetap dipertahankan ... */
    .mentee-avatar-gold-ring {
        width: 100px;
        height: 100px;
        min-width: 100px;
        font-size: 2.125rem;
        border-width: 4px;
        margin-bottom: 0.5rem;
    }
}
```

### Langkah 3: Kompilasi Asset Frontend
Jalankan perintah kompilasi Vite:
```bash
npm run build
```
Pastikan kompilasi sukses tanpa error, dan output manifest Vite ter-generate bersih.

### Langkah 4: Verifikasi Pengujian Otomatis
Jalankan test suite menggunakan runtime PHP 8.3:
```bash
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test tests/Feature/PembimbingPortalStandardizationTest.php
```
Pastikan seluruh 5 test (70 assertions) berstatus **PASSED**.

---

## 5. Kriteria Penerimaan (Acceptance Criteria)

- [ ] Avatar melingkar `.mentee-avatar-gold-ring` memiliki dimensi `120px` x `120px` pada layar desktop.
- [ ] Ring border berwarna `#F59E0B` dengan ketebalan `4.5px solid`.
- [ ] Inisial peserta di dalam lingkaran berukuran `2.5rem` dengan ketebalan font `800` dan berada persis di tengah (*centered*).
- [ ] Efek glow amber memancar lembut dengan radius `24px` (`rgba(245, 158, 11, 0.35)`).
- [ ] Jarak horizontal antara avatar dan nama peserta pas dan lega (`gap: 1.75rem`).
- [ ] Pada layar mobile (`<= 640px`), avatar responsif mengecil ke `100px` x `100px` dengan inisial `2.125rem`.
- [ ] File Blade `show.blade.php` tidak mengalami penambahan inline style atau script.
- [ ] `npm run build` sukses 100%.
- [ ] Test feature PHPUnit lolos 100%.

