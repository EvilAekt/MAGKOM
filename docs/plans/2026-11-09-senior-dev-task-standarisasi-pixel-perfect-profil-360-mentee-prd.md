# TASK BRIEF SENIOR DEVELOPER: PANDUAN IMPLEMENTASI PIXEL-PERFECT PROFIL 360° PESERTA BIMBINGAN (ROLE PEMBIMBING)

> **Dari:** Senior Developer & Lead Software Architect  
> **Kepada:** Junior Developer / AI Model Pelaksana (Gemini 3.8 Flash / Antigravity Agent)  
> **Target Repositori:** MAGKOM (Portal Manajemen Magang Dinas Kominfo Kabupaten Sleman)  
> **Status:** APPROVED FOR IMPLEMENTATION (MANDAT MUTLAK: ZERO-DEVIATION PIXEL-PERFECT)  
> **Tingkat Kesulitan:** Menengah (CSS Contrast Engineering, Color Unification, Blade Structural Integrity, Zero-Hallucination)  
> **Dokumentasi Terkait:** `AGENTS.md`, `.agents/rules/AGENTS.md`, `docs/01-PRD-MagKom.md`, `docs/02-TDD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`  
> **Target Endpoint & Rute:** `GET /pembimbing/peserta/{id}` (`route('pembimbing.peserta.show')`)  
> **Berkas Acuan Desain Resmi:**  
> 1. `docs/plans/2026-11-09-profil-360-target-mockup-halaman-penuh.jpg` (Tangkapan Layar Utuh Master Halaman)  
> 2. `docs/plans/2026-11-09-profil-360-crop-progres-tugas-legenda-badge.png` (Crop Detail Legenda & Badge Progres Tugas)  
> 3. `docs/plans/2026-11-09-profil-360-crop-periode-magang-kuning.png` (Crop Detail Kartu Periode Magang Kuning)  
> 4. `docs/plans/2026-11-09-profil-360-crop-segmented-bar-kuning.png` (Crop Detail Segmented Bar Penugasan)  
> 5. `docs/plans/2026-11-09-profil-360-crop-tugas-proses-kuning.png` (Crop Detail Kolom & Kartu Tugas Proses)

---

## 1. 🔍 LATAR BELAKANG & 3 PERMASALAHAN KRUSIAL (AUDIT DESAIN)

Dokumen ini disusun sebagai instruksi resmi dan definitif dari Senior Developer untuk Junior Developer dan AI pelaksana. Tampilan halaman **Profil Peserta Bimbingan** (`pembimbing/peserta/{id}`) **WAJIB 100% PERSIS DAN IDENTIK** dengan desain visual acuan. Tidak diperbolehkan ada perbedaan warna, jarak, struktur legenda, maupun kontras kartu putih.

Berdasarkan analisis visual dan sampling piksel digital terhadap 5 gambar acuan:

### Permasalahan 1: Card Putih Kurang Terlihat Jelas & Kanvas Latar Belakang Kurang Kontras
* **Gejala:** Kartu `LOGBOOK` dan kartu utama `Riwayat Penugasan` keduanya menggunakan background putih murni (`#FFFFFF`). Jika latar kanvas halaman pembimbing masih mewarisi background putih atau abu-abu transparan dari layout dasar, batas kartu-kartu putih tersebut menyatu dengan kanvas dan tampak "belum ada background-nya" serta kehilangan kedalaman visual (*visual depth*). Selain itu, baris aksi paling atas (tanggal & tombol kembali) pada desain master berada di dalam container putih melengkung yang elegan.
* **Analisis Acuan Desain:** Pada mockup master ([`media utuh`](file:///g:/MAGKOM/docs/plans/2026-11-09-profil-360-target-mockup-halaman-penuh.jpg)), latar kanvas halaman di bawah topbar memiliki warna abu-abu kebiruan lembut: **`#E8F1FA`** (atau `#E9F0F8`).
* **Solusi Arsitektur:** 
  1. Seluruh container kanvas halaman pembimbing (`.dashboard-content-area.mentee-canvas-bg`, `body.role-pembimbing .right_col.dashboard-main`, dan `.mentee-profile-page`) diatur secara absolut menggunakan background kanvas **`#E8F1FA`**.
  2. Seluruh kartu berlatar putih (`.kpi-card-white`, `.mentee-kanban-board-card`, dan container bar aksi atas `.mentee-date-action-bar`) diberikan latar **`#FFFFFF`**, border pembatas halus **`1px solid #DCE3EC`**, dan bayangan lembut **`box-shadow: 0 4px 18px rgba(15, 23, 42, 0.05)`** dengan sudut melengkung `border-radius: 14px` hingga `16px`. Hasilnya, kartu putih tampil tegas dan kontras tinggi di atas kanvas.

---

### Permasalahan 2: Background Badge Counter & Komposisi Legenda "PROGRES TUGAS"
* **Gejala:** Pada implementasi sebelumnya, legenda donat tugas terkadang menampilkan bulatan titik warna (*color dots*) di sebelah kiri teks, serta badge angka counter yang transparan samar atau tidak memiliki background nyata.
* **Analisis Acuan Desain:** Sesuai crop piksel resolusi tinggi ([`crop progres tugas`](file:///g:/MAGKOM/docs/plans/2026-11-09-profil-360-crop-progres-tugas-legenda-badge.png)):
  1. Kartu `PROGRES TUGAS` berlatar belakang Deep Slate Navy **`#28487A`** (RGB `40, 72, 122`).
  2. Teks legenda: `Selesai`, `Dikerjakan`, `Belum` adalah teks putih solid (`#FFFFFF`) tanpa bulatan titik warna di sebelah kirinya!
  3. Badge angka counter `3`, `2`, `1` di sebelah kanan memiliki background tersendiri berbentuk lingkaran pill padat berwarna Slate Blue **`#4E6992`** (RGB `78, 105, 146`) dengan ukuran 24px x 24px dan angka putih tebal berpusat tepat di tengah.
* **Solusi Arsitektur:** 
  - Hapus elemen `<span class="legend-color-dot ...">` pada legenda Donut Chart di file Blade.
  - Berikan styling khusus `.legend-count-pill` dengan background `#4E6992`, color `#FFFFFF`, border-radius `50%`, ukuran `24px x 24px`, font-size `0.75rem` (12px), dan font-weight `700`.

---

### Permasalahan 3: Inkonsistensi Warna Kuning Antara Periode Magang & Riwayat Tugas
* **Gejala:** Terjadi perbedaan nada warna kuning antara kartu *Periode Magang* (terkadang memakai `#FBBF24` atau `#EAB308`), kartu tugas kolom *Proses*, segmen bar *Proses*, tombol *Kelola Tugas*, dan tombol *Tugas*. User memberikan instruksi tegas: **"warnanya harus sama, ga boleh beda sama sekali!"**
* **Analisis Acuan Desain:** Hasil sampling piksel digital dengan pustaka Python PIL membuktikan:
  - Background kartu `PERIODE MAGANG` ([`crop periode magang`](file:///g:/MAGKOM/docs/plans/2026-11-09-profil-360-crop-periode-magang-kuning.png)) = **`#F59E0B`** (terdeteksi 82.770 piksel murni).
  - Background seluruh kartu tugas di kolom `Proses` ([`crop tugas proses`](file:///g:/MAGKOM/docs/plans/2026-11-09-profil-360-crop-tugas-proses-kuning.png)) = **`#F59E0B`** (terdeteksi 107.708 piksel murni).
  - Garis tengah `Segmented Bar` (Proses) ([`crop segmented bar`](file:///g:/MAGKOM/docs/plans/2026-11-09-profil-360-crop-segmented-bar-kuning.png)) = **`#F59E0B`**.
* **Solusi Arsitektur:** 
  - Tetapkan satu token tunggal mutlak: `--mentee-amber: #F59E0B` (Amber / Warm Gold).
  - Seluruh elemen berunsur kuning di halaman ini **WAJIB 100% menggunakan kode `#F59E0B`**. Dilarang keras menggunakan kode hex kuning lain.

---

## 2. 🎨 KAMUS DATA TOKEN WARNA RESMI (HEX COLOR SPECIFICATION)

Semua developer junior dan AI model pelaksana **WAJIB** merujuk pada kamus token CSS berikut tanpa membuat variabel duplikat:

| Nama Variabel CSS | Kode HEX | Nilai RGB | Elemen Penerapan Visual |
|---|---|---|---|
| `--mentee-canvas-bg` | **`#E8F1FA`** | `rgb(232, 241, 250)` | Kanvas latar belakang utama seluruh area konten dashboard pembimbing. |
| `--mentee-amber` | **`#F59E0B`** | `rgb(245, 158, 11)` | **Single Source of Truth Kuning**: Kapsul tanggal, ring avatar, tombol "Tugas", judul "DATA PESERTA", kartu "Periode Magang", tombol "Kelola Tugas →", segmented bar proses, dot status proses, dan kedua kartu tugas di kolom "Proses". |
| `--mentee-amber-dark` | **`#D97706`** | `rgb(217, 119, 6)` | State hover pada tombol dan tautan bernuansa kuning. |
| `--mentee-kpi-blue` | **`#28487A`** | `rgb(40, 72, 122)` | Background kartu KPI "PROGRES TUGAS" dan lingkaran dalam donat chart. |
| `--mentee-kpi-badge` | **`#4E6992`** | `rgb(78, 105, 146)` | Background badge angka counter bulat (3, 2, 1) pada legenda kartu "PROGRES TUGAS". |
| `--mentee-navy-card` | **`#0F3875`** | `rgb(15, 56, 117)` | Background kartu Hero Profil Mentee, background kartu tugas kolom "Belum". |
| `--mentee-navy-deep` | **`#0F2C59`** | `rgb(15, 44, 89)` | Tombol "← Kembali", badge angka header kolom kanban (1, 2, 3), kotak logbook status disetujui, dan segmen bar belum. |
| `--mentee-royal-blue` | **`#2563EB`** | `rgb(37, 99, 235)` | Background kartu tugas kolom "Selesai", segmen bar tugas selesai. |
| `--mentee-surface` | **`#FFFFFF`** | `rgb(255, 255, 255)` | Background kartu "LOGBOOK", background kartu "Riwayat Penugasan", dan container bar aksi tanggal/kembali. |
| `--mentee-border` | **`#DCE3EC`** | `rgb(220, 227, 236)` | Garis batas (*border*) penegas kartu putih terhadap kanvas `#E8F1FA`. |
| `--mentee-text-navy` | **`#0F172A`** | `rgb(15, 23, 42)` | Teks judul utama, teks pada kartu kuning, teks tombol aksi outline gelap. |
| `--mentee-gray-track` | **`#F1F5F9`** | `rgb(241, 245, 249)` | Background kapsul header kolom kanban dan kotak minggu logbook belum berjalan. |

---

## 3. 📐 VISUAL BLUEPRINT & STRUKTUR HIERARKI KOMPONEN

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [KANVAS HALAMAN: #E8F1FA]                                                              │
│                                                                                        │
│ ┌─ CONTAINER BAR AKSI ATAS (#FFFFFF - Rounded 12px, Border #DCE3EC) ─────────────────┐ │
│ │  📅 Jumat, 06 Nov 2026 (#F59E0B Solid)                           [← Kembali] (#0F2C59)│ │
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌─ KARTU HERO PROFIL MENTEE (#0F3875 - Solid Navy, Radius 16px) ─────────────────────┐ │
│ │  ┌─────────┐   Naufal Pratama                       DATA PESERTA (#F59E0B)           │ │
│ │  │   NP    │   SMK Negeri 2 Depok Sleman            EMAIL:         TELEPON / WA:     │ │
│ │  │(Ring    │   [Aktif] [Jalur Umum]                 PRODI:         NIM / NISN:       │ │
│ │  │ #F59E0B)│   [Logbook] (Outl. Putih)              BIDANG:                          │ │
│ │  └─────────┘   [Tugas] (#F59E0B Solid)              Data dosen dan kampus belum diisi│ │
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌─ BARIS 3 KARTU KPI (Grid 3 Kolom) ─────────────────────────────────────────────────┐ │
│ │ ┌─ PROGRES TUGAS (#28487A) ──┐ ┌─ PERIODE MAGANG (#F59E0B) ┐ ┌─ LOGBOOK (#FFFFFF)─┐│ │
│ │ │   ┌─────┐  Selesai     [3] │ │  86                       │ │ 3 dari 12 minggu    ││ │
│ │ │  │ 50%  │  Dikerjakan  [2] │ │  hari tersisa dari 91     │ │ [1][2][3][4][5][6]  ││ │
│ │ │   └─────┘  Belum       [1] │ │  [━━━━━ progress ━━━━━━━] │ │ [7][8][9][10][11][12]││
│ │ │  (Badge: #4E6992, No Dots!)│ │  01 Nov 2026  31 Jan 2027 │ │ • Selesai 2 • Telat 1││ │
│ │ └────────────────────────────┘ └───────────────────────────┘ └─────────────────────┘│ │
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌─ KARTU RIWAYAT PENUGASAN (#FFFFFF - Kontras Jelas di atas Kanvas #E8F1FA) ─────────┐ │
│ │ Riwayat Penugasan                                               [Kelola Tugas →]   │ │
│ │ • Belum 1              • Proses 2                       • Selesai 3 (#F59E0B)      │ │
│ │ [━ Navy 16% ━][━━━━━━ Kuning #F59E0B 33% ━━━━━━][━━━━━━━ Royal Blue 50% ━━━━━━━━]   │ │
│ │                                                                                    │ │
│ │ ┌─ Kolom: Belum (1) ─────────┐ ┌─ Kolom: Proses (2) ────────┐ ┌─ Kolom: Selesai (3)┐│ │
│ │ │ ┌─ KARTU NAVY (#0F3875) ─┐ │ │ ┌─ KARTU KUNING (#F59E0B) ┐│ │ ┌─ KARTU ROYAL BLUE ││ │
│ │ │ │ Uji Fungsional & Dok   │ │ │ │ Slicing & Integrasi     ││ │ │ Analisis Kebutuhan││ │
│ │ │ │ Testing & Laporan      │ │ │ │ Frontend Development    ││ │ │ Analisis & Riset  ││ │
│ │ │ │ 📅 16 Nov 2026 [Detail]│ │ │ │ 📅 06 Nov • Hari ini    ││ │ │ 📅 30 Okt [Detail]││ │
│ │ │ └────────────────────────┘ │ │ │                 [Detail]││ │ └───────────────────┘│ │
│ │ │                            │ │ └─────────────────────────┘│ │                      │ │
│ │ │                            │ │ ┌─ KARTU KUNING (#F59E0B) ┐│ │ ┌─ KARTU ROYAL BLUE ││ │
│ │ │                            │ │ │ Draf Laporan Akhir      ││ │ │ Orientasi & Arsite││ │
│ │ │                            │ │ │ Laporan Akhir • 1 lap   ││ │ │ Orientasi Teknis  ││ │
│ │ │                            │ │ │ 📅 24 Jan 2027  [Detail]││ │ │ 📅 11 Nov [Detail]││ │
│ │ │                            │ │ └─────────────────────────┘│ │ └───────────────────┘│ │
│ │ └────────────────────────────┘ └────────────────────────────┘ └──────────────────────┘│ │
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. 🛠️ SPESIFIKASI RINCI PER-KOMPONEN & ATURAN IMPLEMENTASI

### 4.1 Container Bar Aksi Atas (Top Date & Action Bar)
* **Wadah Pembungkus**: `.mentee-date-action-bar`
  - Background: `#FFFFFF`
  - Border: `1px solid #DCE3EC`
  - Border-radius: `12px`
  - Padding: `0.75rem 1.25rem`
  - Display: `flex`, `justify-content: space-between`, `align-items: center`
  - Box-shadow: `0 2px 8px rgba(15, 23, 42, 0.04)`
* **Kapsul Tanggal (`.date-pill-amber`)**:
  - Background: `#F59E0B` (Amber)
  - Color: `#0F172A` (Navy gelap)
  - Font-size: `0.875rem` (14px), font-weight: `700`
  - Border-radius: `8px`
  - Padding: `0.45rem 1rem`
  - Ikon: SVG Calendar warna `#0F172A`
* **Tombol Kembali (`.btn-back-navy`)**:
  - Background: `#0F2C59` (Deep Navy)
  - Color: `#FFFFFF`
  - Font-size: `0.875rem` (14px), font-weight: `600`
  - Border-radius: `8px`
  - Padding: `0.45rem 1.15rem`
  - Hover: background `#0A1E3F`

---

### 4.2 Kartu Hero Profil Mentee (`.mentee-hero-card`)
* Background: `#0F3875` (Navy Card)
* Border-radius: `16px`
* Padding: `2rem 2.25rem`
* Kolom Kiri:
  - Avatar Inisial: Lingkaran 84px x 84px, border `3.5px solid #F59E0B`, text `#F59E0B`, font-weight `800`, font-size `1.875rem`.
  - Nama: `#FFFFFF`, font-weight `800`, font-size `1.5rem`.
  - Institusi: `#CBD5E1`, font-size `0.875rem`.
  - Badge Status: `[Aktif]` (hijau emerald `#10B981`), `[Jalur Umum]` (outline border `#F59E0B`, text `#FDE68A`).
  - Tombol Aksi:
    - `[Logbook]`: Outline putih `border: 1.5px solid rgba(255, 255, 255, 0.7)`, text `#FFFFFF`.
    - `[Tugas]`: Solid `#F59E0B`, text `#0F172A`, font-weight `700`.
* Kolom Kanan:
  - Header: `DATA PESERTA` dengan warna `#F59E0B`, font-weight `800`, letter-spacing `0.08em`.
  - Grid Data: Email, Telepon/WA, Program Studi, NIM/NISN, Bidang Penempatan. Label `#93C5FD`, Value `#FFFFFF`.
  - Catatan Dosen: Teks italic abu-abu `#94A3B8`.

---

### 4.3 Tiga Kartu KPI Ringkasan (`.mentee-kpi-summary-grid`)

#### Kartu 1: PROGRES TUGAS (`.kpi-card-blue`)
* Background: `#28487A` (Slate Navy)
* Title: `PROGRES TUGAS` warna `#C7D2FE`, font-size `0.75rem`, font-weight `800`.
* Donut Chart:
  - Outer circle: Conic gradient dengan segmen `#F59E0B` (Selesai), `#FFFFFF` (Proses), dan `#4E6992` (Belum).
  - Inner hole: Lingkaran diameter 64px, background `#28487A`, teks angka persentase tengah `50%` warna `#FFFFFF` tebal.
* Legenda Donut (PERHATIAN KHUSUS JUNIOR DEV / AI):
  - **DILARANG MENYISIPKAN BULATAN TITIK WARNA (NO DOTS)!**
  - Teks: `Selesai`, `Dikerjakan`, `Belum` warna `#FFFFFF`, font-weight `600`, font-size `0.8125rem`.
  - Counter Badge Pill:
    - Selector: `.legend-count-pill`
    - Background: **`#4E6992`**
    - Color: `#FFFFFF`
    - Shape: Lingkaran penuh 24px x 24px
    - Font-weight: `700`, font-size `0.75rem` (12px)

#### Kartu 2: PERIODE MAGANG (`.kpi-card-yellow`)
* Background: **`#F59E0B`** (Identik 100% dengan kartu tugas Proses)
* Title: `PERIODE MAGANG` warna `#0F172A`, font-size `0.75rem`, font-weight `800`.
* Angka Sisa Hari: Font-size `3.25rem`, font-weight `900`, line-height `1`, warna `#0F172A`.
* Subtitle: `hari tersisa dari 91` warna `#1E293B`, font-size `0.8125rem`.
* Progress Bar: Track abu-abu transparan `rgba(15, 23, 42, 0.15)`, fill bar `#0F172A` solid.
* Rentang Tanggal: `01 Nov 2026` s/d `31 Jan 2027` warna `#1E293B`.

#### Kartu 3: LOGBOOK (`.kpi-card-white`)
* Background: **`#FFFFFF`** (Pure White kontras di atas kanvas `#E8F1FA`)
* Border: `1px solid #DCE3EC`
* Box-shadow: `0 4px 18px rgba(15, 23, 42, 0.05)`
* Header: `LOGBOOK` (teks `#0F172A`) dan ringkasan `3 dari 12 minggu` (teks `#64748B`).
* Grid 12 Minggu: 12 kotak berukuran 32px:
  - Kotak 1 & 3 (Selesai): `#0F2C59`, angka putih.
  - Kotak 2 (Terlambat/Revisi): `#EF4444`, angka putih.
  - Kotak 4-12 (Mendatang): `#F1F5F9`, angka abu-abu `#64748B`.
* Legenda Bawah: Titik dot biru `#0F2C59` untuk "Selesai 2" dan titik dot merah `#EF4444` untuk "Terlambat 1".

---

### 4.4 Riwayat Penugasan Kanban Board (`.mentee-kanban-board-card`)
* Background: **`#FFFFFF`** (Pure White kontras di atas kanvas `#E8F1FA`)
* Border: `1px solid #DCE3EC`
* Border-radius: `16px`
* Box-shadow: `0 4px 18px rgba(15, 23, 42, 0.05)`
* Header:
  - Judul: `Riwayat Penugasan` font-size `1.375rem`, font-weight `800`, warna `#0F172A`.
  - Tombol: `[Kelola Tugas →]` background `#F59E0B`, text `#0F172A`, font-weight `700`, border-radius pill 9999px.
* Segmented Bar:
  - Indikator Status: `• Belum 1` (dot `#0F2C59`), `• Proses 2` (dot `#F59E0B`), `• Selesai 3` (dot `#2563EB`).
  - Bar Garis Berwarna:
    - Segmen 1 (Belum): `#0F2C59`
    - Segmen 2 (Proses): **`#F59E0B`**
    - Segmen 3 (Selesai): `#2563EB`
* 3 Kolom Kanban:
  1. **Kolom Belum (1 Tugas)**:
     - Header pill: `#F1F5F9`, badge count `1` (`#0F2C59`).
     - Kartu Tugas: Background `#0F3875` (Navy), teks judul putih, subtitle biru muda `#93C5FD`, tanggal batas waktu, tombol `[Detail]` outline putih `border: 1.5px solid rgba(255, 255, 255, 0.7)`.
  2. **Kolom Proses (2 Tugas)**:
     - Header pill: `#F1F5F9`, badge count `2` (`#0F2C59`).
     - Kartu Tugas 1 & 2: Background **`#F59E0B`** (WARNA WAJIB PERSIS PERIODE MAGANG), teks judul `#0F172A` bold, subtitle `#334155`, tanggal tugas, tombol `[Detail]` outline gelap `border: 1.5px solid #0F172A`, text `#0F172A`.
  3. **Kolom Selesai (3 Tugas)**:
     - Header pill: `#F1F5F9`, badge count `3` (`#0F2C59`).
     - Kartu Tugas 1, 2 & 3: Background `#2563EB` (Royal Blue), teks judul putih, subtitle `#BFDBFE`, tombol `[Detail]` outline putih.

---

## 5. 👨‍💻 PANDUAN STEP-BY-STEP IMPLEMENTASI KODE (UNTUK JUNIOR DEV & AI)

### Step 1: Penyesuaian Markup Blade (`resources/views/pembimbing/peserta/show.blade.php`)
Pastikan struktur markup Blade bersih tanpa inline style dan tanpa inline event listener:

1. **Top Section Directive**:
   ```blade
   @extends('layouts.dashboard')

   @section('title', 'Profil Peserta Bimbingan: ' . ($penempatan->peserta?->nama ?? 'Peserta') . ' - Portal MagKom')
   @section('topbar_title', 'Profil Peserta Bimbingan')
   @section('content_area_class', 'mentee-canvas-bg')

   @push('styles')
       @vite(['resources/css/pages/pembimbing/peserta-show.css'])
   @endpush
   ```

2. **Top Action & Date Bar Container**:
   ```blade
   <div class="mentee-date-action-bar">
       <div class="date-pill-amber">
           <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
               <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
               <line x1="16" y1="2" x2="16" y2="6"></line>
               <line x1="8" y1="2" x2="8" y2="6"></line>
               <line x1="3" y1="10" x2="21" y2="10"></line>
           </svg>
           <span>{{ \Carbon\Carbon::now()->isoFormat('dddd, DD MMM Y') }}</span>
       </div>
       <a href="{{ route('pembimbing.peserta.index') }}" class="btn-back-navy" title="Kembali ke Direktori Peserta Bimbingan">
           <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
               <line x1="19" y1="12" x2="5" y2="12"></line>
               <polyline points="12 19 5 12 12 5"></polyline>
           </svg>
           <span>Kembali</span>
       </a>
   </div>
   ```

3. **Struktur Legenda Donut Chart (PROGRES TUGAS)**:
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
   > ⚠️ **Catatan Penting untuk Junior Dev**: Jangan masukkan `<span class="legend-color-dot ...">` di dalam `donut-legend-item`. Teks langsung bersebelahan dengan `legend-count-pill`.

---

### Step 2: Penyesuaian Stylesheet CSS (`resources/css/pages/pembimbing/peserta-show.css`)

1. **Definisi Token CSS**:
   ```css
   :root {
       --mentee-amber: #F59E0B;
       --mentee-amber-dark: #D97706;
       --mentee-navy-card: #0F3875;
       --mentee-navy-deep: #0F2C59;
       --mentee-navy-darker: #0A1E3F;
       --mentee-kpi-blue: #28487A;
       --mentee-kpi-badge: #4E6992;
       --mentee-royal-blue: #2563EB;
       --mentee-danger: #EF4444;
       --mentee-success: #10B981;
       --mentee-surface: #FFFFFF;
       --mentee-canvas-bg: #E8F1FA;
       --mentee-gray-track: #F1F5F9;
       --mentee-border: #DCE3EC;
       --mentee-text-navy: #0F172A;
       --mentee-text-slate: #1E293B;
       --mentee-text-muted: #64748B;
       --mentee-text-light-blue: #93C5FD;
   }
   ```

2. **Styling Kanvas & Top Action Bar**:
   ```css
   .dashboard-content-area.mentee-canvas-bg,
   body.role-pembimbing .right_col.dashboard-main,
   body:has(.mentee-profile-page) .dashboard-content-area {
       background-color: var(--mentee-canvas-bg) !important;
   }

   .mentee-profile-page {
       padding: 1.5rem;
       max-width: 1440px;
       margin: 0 auto;
       color: var(--mentee-text-navy);
       background-color: var(--mentee-canvas-bg);
       min-height: 100%;
   }

   .mentee-date-action-bar {
       display: flex;
       justify-content: space-between;
       align-items: center;
       background-color: var(--mentee-surface);
       border: 1px solid var(--mentee-border);
       border-radius: 12px;
       padding: 0.75rem 1.25rem;
       margin-bottom: 1.25rem;
       box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
       gap: 1rem;
   }
   ```

3. **Styling Kartu Putih yang Tegas (Kontras Tinggi)**:
   ```css
   .kpi-card-white,
   .mentee-kanban-board-card {
       background-color: var(--mentee-surface);
       border: 1px solid var(--mentee-border);
       box-shadow: 0 4px 18px rgba(15, 23, 42, 0.05);
       border-radius: 16px;
   }
   ```

4. **Styling Badge Counter Legenda Donut Chart**:
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

5. **Penyatuan Mutlak Warna Kuning `#F59E0B`**:
   ```css
   .kpi-card-yellow,
   .card-style-proses,
   .segment-proses,
   .btn-kelola-tugas,
   .date-pill-amber,
   .btn-pill-tugas {
       background-color: var(--mentee-amber); /* #F59E0B */
   }

   .card-style-proses .task-card-title,
   .card-style-proses .task-card-date,
   .btn-kelola-tugas,
   .date-pill-amber {
       color: var(--mentee-text-navy); /* #0F172A */
   }

   .btn-card-action-dark {
       border: 1.5px solid var(--mentee-text-navy);
       color: var(--mentee-text-navy);
       background-color: transparent;
       border-radius: 6px;
       font-weight: 700;
       font-size: 0.75rem;
       padding: 0.25rem 0.85rem;
   }

   .btn-card-action-dark:hover {
       background-color: var(--mentee-text-navy);
       color: var(--mentee-amber);
   }
   ```

---

## 6. 🛡️ ATURAN & GUARDRAIL ARSITEKTUR MAGKOM (WAJIB DIPATUHI)

Junior Developer dan AI pelaksana wajib membaca dan mematuhi aturan berikut dari `.agents/rules/AGENTS.md`:
1. **DILARANG MENULIS TAG `<style>` DI BLADE**: Seluruh deklarasi CSS harus masuk ke `resources/css/pages/pembimbing/peserta-show.css`.
2. **DILARANG MENULIS TAG `<script>` DI BLADE**: Tidak boleh ada inline script atau inline handler seperti `onclick="..."`.
3. **DILARANG MENGGUNAKAN INLINE STYLING**: Dilarang menambahkan atribut `style="background: ..."` statis di file Blade.
4. **BLADE READ-ONLY EXCEPTION**: Perubahan Blade hanya sebatas penyesuaian markup container bar aksi atas dan legenda donut chart yang telah dispesifikasikan di atas.
5. **ASSET PIPELINE VITE**: Semua perubahan CSS wajib dikompilasi ulang menggunakan `npm run build`.

---

## 7. 🚦 QUALITY GATE & PROTOKOL VERIFIKASI SEBELUM TUGAS DINYATAKAN SELESAI

Sebelum melaporkan pekerjaan selesai, Junior Developer atau AI model wajib menjalankan dan mendokumentasikan 4 langkah verifikasi:

### Verifikasi 1: Kompilasi Aset Frontend (Vite)
Jalankan di shell terminal:
```bash
npm run build
```
*Kriteria Lolos:* Tidak ada sintaks error CSS, dan manifest build tergenerasi sukses.

### Verifikasi 2: Eksekusi Test Suite Terkait (PHPUnit)
Jalankan perintah PHP 8.3 aktual:
```powershell
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test tests/Feature/PembimbingPortalStandardizationTest.php
```
*Kriteria Lolos:* 4 tests, 54 assertions **100% PASSED**.

### Verifikasi 3: Eksekusi Seluruh Test Pembimbing
```powershell
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test --filter=Pembimbing
```
*Kriteria Lolos:* Seluruh unit & feature tests pembimbing hijau (0 failure, 0 error).

### Verifikasi 4: Inspeksi Visual (Pixel-Perfect Acceptance Checklist)
Lakukan inspeksi visual di browser:
- [ ] Latar belakang seluruh area konten halaman adalah abu-abu kebiruan lembut **`#E8F1FA`**.
- [ ] Kartu putih `LOGBOOK` dan `Riwayat Penugasan` terlihat sangat kontras, memiliki batas border yang tajam, dan tidak menyatu dengan background.
- [ ] Baris aksi atas (tanggal & kembali) berada di dalam wadah putih rapi berborder.
- [ ] Pada kartu `PROGRES TUGAS`, tidak ada titik bulat warna di sebelah kiri teks `Selesai`, `Dikerjakan`, `Belum`.
- [ ] Badge angka counter `3`, `2`, `1` di sebelah kanan legenda berlatar bulat warna **`#4E6992`** dengan angka putih solid.
- [ ] Warna kuning pada kartu `PERIODE MAGANG` dan kedua kartu tugas kolom `Proses` **100% IDENTIK MENGGUNAKAN `#F59E0B`**.
- [ ] Tidak ada regresi fungsional pada tombol aksi Logbook, Tugas, dan Detail.

