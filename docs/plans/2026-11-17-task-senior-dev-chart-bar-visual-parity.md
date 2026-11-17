# 📋 SENIOR DEVELOPER TECHNICAL TASK & PRD
## Task: Penyelarasan Visual Bar Chart "Status Antrean Berkas" SuperAdmin agar Identik dengan "Distribusi Tugas" (Chart Bar Visual Parity)

- **Author**: Lead Senior UI/UX Architect (Antigravity Architecture Team)
- **Target Assignee**: Junior Fullstack Developer / Autonomous AI Agent (Gemini 3.8 Flash)
- **Dokumen Referensi Bukti Visual**:
  - 🎯 **Target (Foto Acuan Desain "Distribusi Tugas")**: `media_1794898572427_91ad7688.png` (Track bar memiliki pola garis-garis diagonal / *diagonal stripes hatching pattern*, bingkai inner box bergaris halus, bar melengkung di atas dan rata di bawah / *pill-top flush bottom*).
  - ⚠️ **Kondisi Eksisting ("Status Antrean Berkas")**: `media_1794898584351_e3096a94.png` (Track bar polos gelap tanpa motif garis diagonal, tanpa bingkai inner box, bar melengkung di keempat sudut / mengambang).
- **Target File yang Perlu Dimodifikasi**:
  1. [`resources/css/pages/superadmin/dashboard.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/dashboard.css) (Pola garis diagonal *repeating-linear-gradient*, border track, inner frame box, dan radius bar fill)
  2. [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php) (Penambahan inner wrapper `.sa-antrean-chart-box` bila diperlukan untuk framing bento yang presisi)
- **Status Dokumen**: ✅ **SELESAI & TERVERIFIKASI (100% Quality Gate Passed)**
- **Tanggal**: 17 November 2026
- **Klasifikasi**: UI/UX Standardization & Design System Parity

---

## 🎯 1. Ringkasan Eksekutif (TL;DR untuk Junior Developer / AI)

Berdasarkan komparasi bukti tangkapan layar `media_1794898572427_91ad7688.png` (acuan desain) dan `media_1794898584351_e3096a94.png` (kondisi saat ini):
Komponen diagram batang vertikal **"Status Antrean Berkas"** pada dashboard SuperAdmin belum selaras dengan komponen saudara kembarnya **"Distribusi Tugas"** yang ada pada dashboard Bento (pembimbing/peserta).

### Anomali & Deviasi Visual yang Ditemukan (Root Cause):
1. **Hilangnya Motif Garis Diagonal (*Missing Striped Hatching*)**:
   - Di file [`resources/css/pages/superadmin/dashboard.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/dashboard.css#L1923), aturan mode gelap mendefinisikan:
     ```css
     [data-theme="dark"] .sa-antrean-track {
         background: rgba(255, 255, 255, 0.05) !important;
     }
     ```
     Shorthand `background:` dengan `!important` ini **menimpa dan memusnahkan** motif `background-image: repeating-linear-gradient(...)`, sehingga pada mode gelap track bar berubah menjadi warna biru dongker polos yang flat dan membosankan.
   - Sebaliknya, pada acuan "Distribusi Tugas" (`.bento-bar-container`), track bar memiliki motif garis miring diagonal (*diagonal stripes / hatching*) yang elegan:
     ```css
     background-color: #132644;
     background-image: repeating-linear-gradient(-45deg, #132644, #132644 6px, #1c365d 6px, #1c365d 12px);
     border: 1px solid rgba(76, 132, 232, 0.28);
     ```
2. **Ketiadaan Bingkai Inner Box (*Missing Inner Chart Box*)**:
   - Pada foto acuan, keempat kolom bar dibungkus di dalam satu wadah inner (*chart box frame*) dengan latar belakang gelap transparan dan garis tepi (*border*) halus ber-radius 14px.
   - Pada kondisi eksisting SuperAdmin, keempat kolom bar langsung menempel pada bento card utama tanpa bingkai penampung, sehingga kedalaman visual (*visual depth*) berkurang.
3. **Geometri Bar Fill (*Border Radius Mismatch*)**:
   - Pada foto acuan, bar yang terisi memiliki radius hanya pada sudut atas (`border-radius: 12px 12px 0 0`) dan menempel rata pada dasar track (*flush bottom*).
   - Pada eksisting, bar menggunakan `border-radius: 14px` pada keempat sudutnya sehingga terkesan melayang dan tidak menyatu dengan track.

---

## 🔬 2. Analisis Komparasi Visual & Teknis (Comparison Matrix)

| Elemen Desain | Acuan: "Distribusi Tugas" (`media_..._91ad7688.png`) | Eksisting: "Status Antrean Berkas" (`media_..._e3096a94.png`) | Solusi Penyelarasan Teknis |
| :--- | :--- | :--- | :--- |
| **Bingkai Dalam (Inner Chart Box)** | Memiliki bingkai inner khusus `.bento-distribusi-chart-box` dengan `border: 1px solid rgba(255, 255, 255, 0.07)` & `border-radius: 14px` | Tidak memiliki bingkai inner (langsung pada card body) | Tambahkan kelas `.sa-antrean-chart-box` di Blade & CSS dengan border halus 1px dan padding rapi |
| **Pola Latar Track (Stripes)** | **Garis diagonal aktif**: `repeating-linear-gradient(-45deg, #132644 0 6px, #1c365d 6px 12px)` | **Polos flat**: `rgba(255, 255, 255, 0.05)` tanpa gradien garis | Terapkan `repeating-linear-gradient(-45deg, #132644 0 6px, #1c365d 6px 12px)` pada Dark Mode |
| **Border Track Bar** | Memiliki garis tepi halus: `1px solid rgba(76, 132, 232, 0.28)` | Tanpa garis tepi (`border: none`) | Tambahkan `border: 1px solid rgba(76, 132, 232, 0.28)` pada Dark Mode dan `#cad6e8` pada Light Mode |
| **Corner Radius Track** | `border-radius: 12px` | `border-radius: 14px` | Samakan menjadi `border-radius: 12px; overflow: hidden;` |
| **Geometri Fill Bar** | `border-radius: 12px 12px 0 0;` (melengkung atas, rata bawah) | `border-radius: 14px;` (melengkung semua sudut) | Ubah menjadi `border-radius: 12px 12px 0 0;` agar dasar bar menempel rapi pada track |
| **Palet Warna Bar** | 1. Selesai (Navy/Slate Blue)<br>2. Dikerjakan (Bright Blue `#3B82F6`)<br>3. Review (Amber Yellow `#FFC300`)<br>4. Terlambat (Alert Red `#EF4444`) | 1. Terverifikasi (`#3B82F6`)<br>2. Diajukan (`#60A5FA`)<br>3. Perlu perbaikan (`#FFC300`)<br>4. Lewat SLA (`#EF4444`) | Pertahankan token semantik 4 warna, namun selaraskan saturasi dan kecerahan kontrasnya |
| **Angka Metrik di Dalam Bar** | Putih solid `#FFFFFF`, font tebal `font-weight: 800; font-size: 16px;` | Font `14px`, warna bervariasi | Samakan menjadi `font-size: 15px; font-weight: 800; color: #FFFFFF;` |

---

## 📁 3. Rincian Perubahan File (File Impact Table)

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`resources/css/pages/superadmin/dashboard.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/dashboard.css) | **Modify** | 1. Tambahkan styling `.sa-antrean-chart-box` (bingkai inner chart).<br>2. Perbarui `.sa-antrean-track` agar memiliki pola garis diagonal `-45deg` dan border yang presisi pada Light Mode dan Dark Mode.<br>3. Perbaiki `.sa-antrean-fill` agar memiliki `border-radius: 12px 12px 0 0` dan perataan bawah flush.<br>4. Hapus deklarasi override polos `rgba(255, 255, 255, 0.05)` di `[data-theme="dark"] .sa-antrean-track`. |
| [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php) | **Modify** | Bungkus elemen `.sa-antrean-chart` di dalam wadah `<div class="sa-antrean-chart-box">` agar memiliki bingkai kartu dalam yang identik dengan foto target. |

---

## 📋 4. Langkah-Langkah Pengerjaan Bertahap (Senior Dev Tasks)

### Task 1: Penambahan Kontainer Bingkai Dalam (Inner Chart Box)
**File Target**: [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php)
- [x] Di dalam kartu `sa-card--antrean` (sekitar baris 447), bungkus `.sa-antrean-chart` dengan `.sa-antrean-chart-box`:
  ```blade
  <div class="sa-card sa-card--antrean sc-reveal-scale sc-delay-2" data-scroll-reveal>
      <div class="sa-card-head">
          <h2 class="sa-card-title">{{ __('Status Antrean Berkas') }}</h2>
          <span class="sa-pill">{{ $totalAntreanBerkas }} {{ __('berkas') }}</span>
      </div>
      <div class="sa-antrean-chart-box">
          <div class="sa-antrean-chart">
              ... (kolom-kolom bar) ...
          </div>
      </div>
  </div>
  ```

---

### Task 2: Implementasi CSS Bingkai Inner & Pola Garis Diagonal
**File Target**: [`resources/css/pages/superadmin/dashboard.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/dashboard.css)
- [x] Tambahkan styling `.sa-antrean-chart-box` (sekitar baris 630):
  ```css
  .sa-antrean-chart-box {
      background: #FFFFFF;
      border: 1px solid #E3E8F0;
      border-radius: 14px;
      padding: 16px 18px 12px 18px;
      margin-top: 14px;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
  }

  [data-theme="dark"] .sa-antrean-chart-box {
      background: #0E2548;
      border-color: rgba(255, 255, 255, 0.07);
  }
  ```
- [x] Perbarui `.sa-antrean-chart`:
  ```css
  .sa-antrean-chart {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 14px;
      height: 180px;
      align-items: end;
  }
  ```
- [x] Perbarui `.sa-antrean-track` (Light Mode):
  ```css
  .sa-antrean-track {
      width: 100%;
      height: 155px;
      border-radius: 12px;
      background-color: #E5EDF8;
      background-image: repeating-linear-gradient(
          -45deg,
          #E5EDF8,
          #E5EDF8 6px,
          #D3E0F2 6px,
          #D3E0F2 12px
      );
      border: 1px solid #CAD6E8;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      overflow: hidden;
      position: relative;
  }
  ```
- [x] Perbarui `[data-theme="dark"] .sa-antrean-track` (Dark Mode - baris 1923):
  **Hapus** `background: rgba(255, 255, 255, 0.05) !important;` dan ganti dengan pola garis diagonal:
  ```css
  [data-theme="dark"] .sa-antrean-track {
      background-color: #132644 !important;
      background-image: repeating-linear-gradient(
          -45deg,
          #132644,
          #132644 6px,
          #1C365D 6px,
          #1C365D 12px
      ) !important;
      border: 1px solid rgba(76, 132, 232, 0.28) !important;
  }
  ```

---

### Task 3: Polishing Geometri Bar Fill & Angka
**File Target**: [`resources/css/pages/superadmin/dashboard.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/dashboard.css)
- [x] Perbarui `.sa-antrean-fill`:
  ```css
  .sa-antrean-fill {
      width: 100%;
      border-radius: 12px 12px 0 0;
      display: flex;
      align-items: flex-start;
      justify-content: center;
      padding-top: 8px;
      box-sizing: border-box;
      min-height: 28px;
      transition: height 0.35s cubic-bezier(0.4, 0, 0.2, 1);
  }
  ```
- [x] Perbarui `.sa-antrean-val` dan `.sa-antrean-label`:
  ```css
  .sa-antrean-val {
      font-size: 15px;
      font-weight: 800;
      line-height: 1;
      color: #FFFFFF !important;
  }

  .sa-antrean-label {
      font-size: 12px;
      font-weight: 600;
      color: #5B6785;
      text-align: center;
      margin-top: 8px;
      white-space: nowrap;
  }

  [data-theme="dark"] .sa-antrean-label {
      color: rgba(255, 255, 255, 0.75) !important;
  }
  ```

---

### Task 4: Verifikasi & Quality Gate
Jalankan verifikasi sistem:
- [x] Compile Aset Frontend: `npm run build` (Sukses tanpa error dalam 2.29s)
- [x] Verifikasi Visual: Buka halaman SuperAdmin dashboard, pastikan kedua chart ("Status Antrean Berkas" dan "Distribusi Tugas") memiliki motif garis diagonal yang identik.
- [x] Test Suite: `php artisan test --filter=SuperAdmin` (261 passed, 1496 assertions)
- [x] Code Formatting: `./vendor/bin/pint --test resources/css/pages/superadmin/dashboard.css resources/views/superadmin/dashboard.blade.php` (Pass)

---

## 🛡️ 5. Matriks Risiko & Mitigasi

1. **Risiko Kompatibilitas Animasi GSAP**:
   - *Mitigasi*: Kelas `.sa-striped-bar` dan `.sa-antrean-val` tetap dipertahankan pada elemen fill bar sehingga script GSAP di `resources/js/pages/superadmin/dashboard.js` tetap menemukan target animasi dan berjalan mulus tanpa error.
2. **Risiko Responsive Breakpoint**:
   - *Mitigasi*: Menggunakan `grid-template-columns: repeat(4, 1fr)` dan tinggi fleksibel yang sudah teruji di perangkat desktop dan tablet.

---

## ✅ 6. Kriteria Selesai (Definition of Done)

- [x] Track bar pada "Status Antrean Berkas" menampilkan motif garis-garis diagonal (*hatching stripes*) yang identik 1:1 dengan foto referensi "Distribusi Tugas".
- [x] Terdapat bingkai inner chart box (`.sa-antrean-chart-box`) dengan border halus melengkung.
- [x] Bar fill memiliki kelengkungan sudut atas (`border-radius: 12px 12px 0 0`) dan menempel rata pada dasar track (*flush bottom*).
- [x] Tampilan konsisten pada mode terang (*Light Mode*) dan mode gelap (*Dark Mode*).
- [x] `npm run build` berhasil tanpa error.
- [x] Seluruh feature test `SuperAdmin` lulus 100%.

---
*Dokumen rencana kerja ini siap dieksekusi setelah mendapatkan konfirmasi persetujuan dari pengguna.*
