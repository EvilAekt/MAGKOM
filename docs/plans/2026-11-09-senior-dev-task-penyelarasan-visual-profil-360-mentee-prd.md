# TASK BRIEF SENIOR DEVELOPER: PANDUAN LENGKAP & STANDARISASI PIXEL-PERFECT PROFIL 360° PESERTA BIMBINGAN (ROLE PEMBIMBING)

> **Dari:** Senior Developer / Lead Software Architect  
> **Kepada:** Junior Developer / AI Agent Pelaksana (Gemini 3.8 Flash / Antigravity Agent)  
> **Target Repositori:** MAGKOM (Portal Manajemen Magang Dinas Kominfo Kabupaten Sleman)  
> **Status:** APPROVED FOR IMPLEMENTATION (ZERO-DEVIATION PIXEL-PERFECT REQUIREMENT)  
> **Tingkat Kesulitan:** Menengah (CSS Contrast Engineering, Color Unification, Blade Slicing, Zero-Hallucination)  
> **Dokumentasi Terkait:** `AGENTS.md`, `.agents/rules/AGENTS.md`, `docs/01-PRD-MagKom.md`, `docs/02-TDD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`  
> **Gambar Acuan Resmi:**  
> 1. `media_1794217787033_30b5e2fc.jpg` (Desain Target Halaman Penuh)  
> 2. `media_1794217855826_3dff7b71.png` (Crop Detail Legenda & Badge Progres Tugas)  
> 3. `media_1794217856880_5b4e039f.png` (Crop Detail Kartu Periode Magang)  
> 4. `media_1794217858003_f244ca48.png` (Crop Detail Segmented Bar Penugasan)  
> 5. `media_1794217859127_0661d6b7.png` (Crop Detail Kolom & Kartu Tugas Proses)

---

## 1. 🔍 LATAR BELAKANG & 3 PERMASALAHAN KRUSIAL

Instruksi tegas dari Project Owner: Tampilan halaman **Profil Peserta Bimbingan** (`pembimbing/peserta/{id}`) **HARUS 100% PERSIS** dengan desain mockup yang telah dibuat tanpa ada perbedaan apapun.

Berdasarkan audit komparasi visual langsung dari 5 gambar acuan yang diunggah:

### Masalah 1: Card Putih Kurang Terlihat Jelas & Belum Ada Latar Belakang Penegas
- **Gejala Visual**: Kartu `LOGBOOK` dan kartu utama `Riwayat Penugasan` keduanya berlatar belakang putih murni (`#FFFFFF`). Ketika halaman dimuat, latar kanvas pembungkus halaman masih mewarisi warna putih/terang dari layout global, sehingga batas-batas kartu putih menyatu dengan kanvas dan tampak "belum ada background-nya".
- **Analisis Acuan Desain**: Pada mockup master ([`media_1794217787033_30b5e2fc.jpg`](file:///C:/Users/galan/.gemini/antigravity/brain/eccea852-443f-4ee4-a11b-827f29382866/.user_uploaded/media_1794217787033_30b5e2fc.jpg)), kanvas halaman di bawah topbar memiliki warna abu-abu kebiruan lembut: **`#E9F0F6`**.
- **Solusi Arsitektural**: Seluruh container pembungkus konten pembimbing (`.right_col.dashboard-main`, `.dashboard-content-area`, dan `.mentee-profile-page`) diatur secara absolut menggunakan background kanvas **`#E9F0F6`**. Kartu-kartu putih (`#FFFFFF`) diberikan border pembatas halus (`1px solid #DCE3EC`) dan bayangan lembut (`box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.08)`), sehingga kartu putih tampil tegas dan memiliki kedalaman visual (*visual depth*).

### Masalah 2: Komposisi Legenda & Background Badge "PROGRES TUGAS"
- **Gejala Visual**: Sebelumnya pada kartu `PROGRES TUGAS`, legenda donat memiliki bulatan dot warna di sebelah kiri teks, dan badge counter transparan samar sehingga menyatu dengan kartu.
- **Analisis Acuan Desain**: Pada crop detail ([`media_1794217855826_3dff7b71.png`](file:///C:/Users/galan/.gemini/antigravity/brain/eccea852-443f-4ee4-a11b-827f29382866/.user_uploaded/media_1794217855826_3dff7b71.png)):
  - Kartu berlatar navy blue solid **`#28487A`**.
  - Teks legenda: `Selesai`, `Dikerjakan`, `Belum` adalah teks putih murni **tanpa bulatan dot warna** di sebelah kirinya.
  - Badge counter angka `3`, `2`, `1` di sebelah kanan memiliki background tersendiri yang nyata dan solid: **`#4E6992`** (slate-blue circular bubble) dengan angka putih tebal berukuran 24px x 24px.

### Masalah 3: Inkonsistensi Warna Kuning Antara Periode Magang & Riwayat Tugas
- **Gejala Visual**: Warna kuning di kartu *Periode Magang*, garis *Segmented Bar*, dan kartu tugas *Proses* sebelumnya menggunakan nuansa kuning yang berbeda (tercampur antara kuning muda `#FBBF24` dan kuning oranye `#F59E0B`).
- **Analisis Acuan Desain**: Hasil sampling piksel digital pada 3 gambar crop:
  - Kartu `PERIODE MAGANG` ([`media_1794217856880_5b4e039f.png`](file:///C:/Users/galan/.gemini/antigravity/brain/eccea852-443f-4ee4-a11b-827f29382866/.user_uploaded/media_1794217856880_5b4e039f.png)) = **`#F59E0B`**
  - Garis `Segmented Bar` Proses ([`media_1794217858003_f244ca48.png`](file:///C:/Users/galan/.gemini/antigravity/brain/eccea852-443f-4ee4-a11b-827f29382866/.user_uploaded/media_1794217858003_f244ca48.png)) = **`#F59E0B`**
  - Kedua kartu tugas kolom `Proses` ([`media_1794217859127_0661d6b7.png`](file:///C:/Users/galan/.gemini/antigravity/brain/eccea852-443f-4ee4-a11b-827f29382866/.user_uploaded/media_1794217859127_0661d6b7.png)) = **`#F59E0B`**
- **Solusi Arsitektural**: Seluruh elemen kuning distandarkan secara mutlak menggunakan satu kode warna: **`#F59E0B`**.

---

## 2. 🎨 KAMUS DATA WARNA RESMI (HEX COLOR SPECIFICATION)

Semua developer junior dan model AI pelaksana **WAJIB** menggunakan kode HEX berikut tanpa modifikasi:

| Token / Variabel | Kode HEX | Elemen Penerapan Visual |
|---|---|---|
| `--mentee-canvas-bg` | **`#E9F0F6`** | Kanvas latar belakang utama halaman yang membungkus semua kartu. |
| `--mentee-amber` | **`#F59E0B`** | Kapsul tanggal, ring avatar, tombol "Tugas", header "DATA PESERTA", donat selesai, kartu "Periode Magang", tombol "Kelola Tugas", segmented bar proses, dan kartu tugas "Proses". |
| `--mentee-amber-dark` | **`#D97706`** | Hover state tombol kuning & tombol kelola tugas. |
| `--mentee-kpi-blue` | **`#28487A`** | Background kartu KPI "PROGRES TUGAS". |
| `--mentee-kpi-badge` | **`#4E6992`** | Background badge counter (3, 2, 1) pada legenda kartu "PROGRES TUGAS". |
| `--mentee-navy-card` | **`#0F3875`** | Background kartu Hero Profil Mentee, background kartu tugas kolom "Belum". |
| `--mentee-navy-deep` | **`#0F2C59`** | Tombol `← Kembali`, badge count kapsul header kanban, kotak minggu logbook selesai. |
| `--mentee-royal-blue` | **`#2563EB`** | Background kartu tugas kolom "Selesai", segmen bar selesai. |
| `--mentee-surface-white` | **`#FFFFFF`** | Background kartu "LOGBOOK", background kartu "Riwayat Penugasan". |
| `--mentee-border` | **`#DCE3EC`** | Border penegas kartu putih terhadap kanvas `#E9F0F6`. |
| `--mentee-text-navy` | **`#0F172A`** | Teks utama, teks kartu kuning, teks judul kanban. |

---

## 3. 📐 STRUKTUR UI & BLUEPRINT DETAIL

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [KANVAS LATAR BELAKANG: #E9F0F6]                                                       │
│                                                                                        │
│ [STRIP BAR] 📅 Jumat, 06 Nov 2026 (#F59E0B)                       [← Kembali] (#0F2C59)│
│                                                                                        │
│ ┌─ KARTU HERO PROFIL MENTEE (#0F3875 - Solid Navy) ──────────────────────────────────┐ │
│ │  (NP) Ring Emas #F59E0B  Naufal Pratama          DATA PESERTA (#F59E0B)            │ │
│ │  SMK Negeri 2 Depok      [Aktif] [Jalur Umum]    EMAIL:         TELEPON / WA:      │ │
│ │  [Logbook] (Outl. Putih) [Tugas] (#F59E0B Solid) PRODI:         NIM:               │ │
│ │                                                  BIDANG:                           │ │
│ │                                                  Data dosen dan kampus belum diisi.│ │
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌─ BARIS 3 KARTU KPI ────────────────────────────────────────────────────────────────┐ │
│ │ ┌─ PROGRES TUGAS (#28487A) ──┐ ┌─ PERIODE MAGANG (#F59E0B) ┐ ┌─ LOGBOOK (#FFFFFF)─┐│ │
│ │ │   ┌─────┐  Selesai     [3] │ │  86                       │ │ 3 dari 12 minggu    ││ │
│ │ │  │ 50%  │  Dikerjakan  [2] │ │  hari tersisa dari 91     │ │ [1][2][3][4][5][6]  ││ │
│ │ │   └─────┘  Belum       [1] │ │  [━━━━━ progress ━━━━━━━] │ │ [7][8][9][10][11][12]││
│ │ │  (Badge: #4E6992, No Dots!)│ │  01 Nov 2026  31 Jan 2027 │ │ • Selesai 2 • Telat 1││ │
│ │ └────────────────────────────┘ └───────────────────────────┘ └─────────────────────┘│ │
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌─ KARTU RIWAYAT PENUGASAN (#FFFFFF - Pure White berdiri di atas kanvas #E9F0F6) ────┐ │
│ │ Riwayat Penugasan                                               [Kelola Tugas →]   │ │
│ │ • Belum 1              • Proses 2                       • Selesai 3 (#F59E0B)      │ │
│ │ [━ Navy 16% ━][━━━━━━ Kuning #F59E0B 33% ━━━━━━][━━━━━━━ Royal Blue 50% ━━━━━━━━]   │ │
│ │                                                                                    │ │
│ │ ┌─ Kolom: Belum (1) ─────────┐ ┌─ Kolom: Proses (2) ────────┐ ┌─ Kolom: Selesai (3)┐│ │
│ │ │ ┌─ KARTU NAVY (#0F3875) ─┐ │ │ ┌─ KARTU KUNING (#F59E0B) ┐│ │ ┌─ KARTU ROYAL BLUE ││ │
│ │ │ │ Uji Fungsional & Dok   │ │ │ │ Slicing & Integrasi     ││ │ │ Analisis Kebutuhan││ │
│ │ │ │ 📅 16 Nov 2026 [Detail]│ │ │ │ 📅 06 Nov • Hari ini    ││ │ │ 📅 30 Okt [Detail]││ │
│ │ │ └────────────────────────┘ │ │ │                 [Detail]││ │ └───────────────────┘│ │
│ │ │                            │ │ └─────────────────────────┘│ │                      │ │
│ │ │                            │ │ ┌─ KARTU KUNING (#F59E0B) ┐│ │                      │ │
│ │ │                            │ │ │ Draf Laporan Akhir      ││ │                      │ │
│ │ │                            │ │ │ 📅 24 Jan 2027  [Detail]││ │                      │ │
│ │ │                            │ │ └─────────────────────────┘│ │                      │ │
│ │ └────────────────────────────┘ └────────────────────────────┘ └──────────────────────┘│ │
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. 🛠️ INSTRUKSI KODE DETAIL UNTUK JUNIOR DEVELOPER & AI MODEL

### 4.1 Bagian Blade (`resources/views/pembimbing/peserta/show.blade.php`)
1. **Deklarasi Kanvas**:
   Tambahkan deklarasi `@section('content_area_class', 'mentee-canvas-bg')` di baris atas view.
2. **Accessible Title**:
   Sertakan `<h1 class="sr-only">Profil 360° Mentee Bimbingan</h1>` agar screen-reader dan test automation PHPUnit tetap mengenali heading halaman.
3. **Markup Donut Chart Legends**:
   ```blade
   <div class="donut-chart-legends">
       <div class="donut-legend-item">
           <span class="legend-text">Selesai</span>
           <span class="legend-count-pill">{{ $tugasSelesaiCount }}</span>
       </div>
       <div class="donut-legend-item">
           <span class="legend-text">Dikerjakan</span>
           <span class="legend-count-pill">{{ $tugasProsesCount }}</span>
       </div>
       <div class="donut-legend-item">
           <span class="legend-text">Belum</span>
           <span class="legend-count-pill">{{ $tugasBelumCount }}</span>
       </div>
   </div>
   ```
   *Catatan penting*: Hapus semua `<span class="legend-color-dot ...">` dari legenda donat.

### 4.2 Bagian CSS (`resources/css/pages/pembimbing/peserta-show.css`)
1. **Latar Belakang Kanvas**:
   ```css
   .dashboard-content-area.mentee-canvas-bg,
   body.role-pembimbing .right_col.dashboard-main,
   body:has(.mentee-profile-page) .dashboard-content-area,
   .mentee-profile-page {
       background-color: var(--mentee-canvas-bg) !important;
   }
   ```
2. **Badge Counter Legenda**:
   ```css
   .donut-legend-item {
       display: flex;
       align-items: center;
       justify-content: space-between;
       font-size: 0.8125rem;
       font-weight: 600;
       color: #FFFFFF;
   }

   .legend-count-pill {
       background-color: var(--mentee-kpi-badge); /* #4E6992 */
       color: #FFFFFF;
       font-weight: 700;
       font-size: 0.75rem;
       width: 24px;
       height: 24px;
       min-width: 24px;
       border-radius: 50%;
       display: inline-flex;
       align-items: center;
       justify-content: center;
       box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
   }
   ```
3. **Elevasi & Border Card Putih**:
   ```css
   .kpi-card-white,
   .mentee-kanban-board-card {
       background-color: var(--mentee-surface); /* #FFFFFF */
       border: 1px solid var(--mentee-border);  /* #DCE3EC */
       box-shadow: 0 4px 18px rgba(15, 23, 42, 0.05);
       border-radius: 16px;
   }
   ```
4. **Warna Kuning Tunggal `#F59E0B`**:
   ```css
   .card-style-proses,
   .kpi-card-yellow,
   .segment-proses,
   .btn-kelola-tugas,
   .btn-pill-tugas,
   .date-pill-amber {
       background-color: var(--mentee-amber); /* #F59E0B */
   }
   ```

---

## 5. 📋 PROSEDUR QUALITY GATE & VERIFIKASI SEBELUM TUGAS DINYATAKAN SELESAI

Setelah kode selesai diedit:
1. Jalankan `npm run build` untuk mengompilasi CSS melalui Vite.
2. Jalankan `& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test tests/Feature/PembimbingPortalStandardizationTest.php` (Wajib 100% PASS).
3. Jalankan `& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test --filter=Pembimbing` (Wajib 128 tests PASS).
4. Verifikasi bahwa tidak ada tag `<style>` atau `<script>` baru di dalam file Blade.
5. Verifikasi visual: Refresh browser dan pastikan kanvas berlatar `#E9F0F6`, kartu putih kontras, legenda donat tanpa dot dengan badge `#4E6992`, dan warna kuning `#F59E0B` seragam di seluruh elemen.

