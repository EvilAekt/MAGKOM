# Rencana Kerja: Peningkatan Kontras & Penyelarasan Track Kosong Bar Chart "Distribusi Tugas"

**Dokumen:** Implementation Plan (Web Planning)  
**Tanggal:** 2026-11-07  
**Target:** Komponen Bar Chart "Distribusi Tugas" pada Dashboard Pembimbing  
**File Terdampak:** `resources/css/pages/pembimbing/dashboard.css`  
**Blade:** Tidak memerlukan perubahan markup Blade (*Zero Blade Changes*)  
**Status:** Selesai Terverifikasi (*Completed & Verified*)  

---

## 1. Tujuan

Meningkatkan keterbacaan (*visibility*) dan konsistensi visual pada area wadah bar yang **belum terisi** (*empty/unfilled bar track container*) di kartu **Distribusi Tugas** pada Dashboard Pembimbing:
1. Mengatasi masalah di mana pada **Light Mode (Default)**, arsiran bar kosong tampak sangat pudar dan hampir tidak terlihat karena garis putih berbaur dengan latar belakang kartu putih.
2. Menyelaraskan perbedaan persepsi visual antara **Default (Light Mode)** dan **Dark Mode** agar kedua tema menampilkan pola arsir blueprint diagonal (*two-tone architectural hatch*) yang jelas, kontras proporsional, dan memiliki batas kontur (*subtle border*) yang tegas.
3. Menjaga hierarki visual agar track kosong tetap berfungsi sebagai latar belakang yang jelas tanpa menenggelamkan bar data terisi (Selesai, Dikerjakan, Review, Terlambat).

---

## 2. Asumsi & Investigasi Masalah Saat Ini

### Analisis Kondisi Eksisting (`resources/css/pages/pembimbing/dashboard.css` L652-680):
- **Light Mode Sebelumnya:**
  - `background-color: #eef1f6;`
  - `background-image: repeating-linear-gradient(-45deg, transparent, transparent 6px, #ffffff 6px, #ffffff 12px);`
  - Masalah: Kartu `.bento-distribusi-chart-box` berwarna putih `#FFFFFF`. Ketika garis arsir menggunakan `#ffffff`, separuh dari wadah track menghilang menyatu dengan kartu, sehingga track tampak berantakan dan samar. Tanpa border penutup, wadah tidak memiliki bentuk yang jelas.
- **Dark Mode Sebelumnya:**
  - `background-color: #16335F;`
  - `background-image: repeating-linear-gradient(-45deg, transparent, transparent 6px, #0E2548 6px, #0E2548 12px);`
  - Masalah: Warna celah `#0E2548` sama persis dengan background kartu `#0E2548`, menghasilkan sensasi visual yang berbeda dibandingkan light mode.

---

## 3. Evaluasi Pendekatan Teknis

### Opsi A: Two-Tone Slate & Navy Blueprint Hatch with Refined Outline (Terpilih & Terimplementasi)
- **Konsep:** Memberikan kontur batas halus (`border: 1px solid`) dan warna dua-nada (*two-tone*) yang sejuk dan tegas pada kedua tema tanpa menggunakan warna putih polos `#ffffff` yang memudarkan kontras.
- **Spesifikasi Light Mode (Default):**
  - Border: `1px solid #cad6e8;`
  - Background Base: `#e5edf8;` (rona slate-blue sejuk yang terdefinisi di atas kartu putih `#FFFFFF`)
  - Hatch Pattern: `repeating-linear-gradient(-45deg, #e5edf8, #e5edf8 6px, #d3e0f2 6px, #d3e0f2 12px);`
  - Kontras: Jelas terlihat sebagai wadah kapsul berarsir diagonal arsitektural, rapi, dan estetis.
- **Spesifikasi Dark Mode:**
  - Border: `1px solid rgba(76, 132, 232, 0.28);`
  - Background Base: `#132644;` (kontras bertingkat di atas kartu `#0E2548`)
  - Hatch Pattern: `repeating-linear-gradient(-45deg, #132644, #132644 6px, #1c365d 6px, #1c365d 12px);`
  - Kontras: Memiliki kedalaman visual yang serasi dengan light mode, tidak terlalu gelap dan tidak buram.
- **Kelebihan:** 
  - Masalah keterlihatan bar kosong langsung teratasi di kedua tema.
  - Mempertahankan identitas desain *blueprint architectural hatch* yang disukai.
  - Bersih, terfokus hanya di CSS, zero risk ke backend/logika sistem.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/pembimbing/dashboard.css` | Modify | Memperbarui styling `.bento-bar-container` dan `[data-theme="dark"] .bento-bar-container` dengan formula two-tone hatch dan outline border. |

---

## 5. Langkah Implementasi Terperinci

### Tahap 1: Pembaruan CSS Track Bar
- [x] **1.1** Edit `resources/css/pages/pembimbing/dashboard.css` pada rule `.bento-bar-container`:
  ```css
  .bento-bar-container {
    width: 100%;
    max-width: 105px;
    height: 165px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    background-color: #e5edf8;
    background-image: repeating-linear-gradient(
      -45deg,
      #e5edf8,
      #e5edf8 6px,
      #d3e0f2 6px,
      #d3e0f2 12px
    );
    border: 1px solid #cad6e8;
    border-radius: 12px;
    overflow: hidden;
  }
  ```
- [x] **1.2** Edit `resources/css/pages/pembimbing/dashboard.css` pada rule `[data-theme="dark"] .bento-bar-container`:
  ```css
  [data-theme="dark"] .bento-bar-container {
    background-color: #132644;
    background-image: repeating-linear-gradient(
      -45deg,
      #132644,
      #132644 6px,
      #1c365d 6px,
      #1c365d 12px
    );
    border: 1px solid rgba(76, 132, 232, 0.28);
  }
  ```

### Tahap 2: Build Aset & Verifikasi
- [x] **2.1** Jalankan build aset Vite: `npm run build` (Sukses tanpa error).
- [x] **2.2** Jalankan automated feature tests: `php artisan test --filter=PembimbingDashboardEnhancementTest` (7 passed, 44 assertions).
- [x] **2.3** Verifikasi tampilan kontras pada Default (Light Mode) dan Dark Mode.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Area kosong di atas bar pada kartu Distribusi Tugas terlihat jelas (*clearly visible*) pada tema Default (Light Mode).
- [x] Area kosong di atas bar pada kartu Distribusi Tugas terlihat jelas (*clearly visible*) pada Dark Mode.
- [x] Pola dan nuansa visual konsisten antara Light Mode dan Dark Mode (sama-sama menggunakan two-tone hatch arsitektural dengan outline border).
- [x] Tidak ada perubahan atau penambahan style di file Blade.
- [x] `npm run build` berhasil tanpa warning/error.
- [x] Seluruh test `php artisan test` tetap hijau (passing).
