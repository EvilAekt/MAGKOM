# TASK BRIEF SENIOR DEVELOPER: PANDUAN PERBESARAN TOMBOL AKSI HERO & OPTIMASI KOTAK GRID DATA PESERTA (PROFIL 360° MENTEE)

> **Dari:** Senior Developer & Lead Software Architect  
> **Kepada:** Junior Developer / AI Model Pelaksana (Gemini 3.8 Flash / Antigravity Agent)  
> **Target Repositori:** MAGKOM (Portal Manajemen Magang Dinas Kominfo Kabupaten Sleman)  
> **Status:** READY FOR REVIEW & IMPLEMENTATION  
> **Tingkat Kesulitan:** Rendah - Menengah (CSS Component Scaling, Button Sizing, Grid Harmony & Ergonomics)  
> **Dokumentasi Terkait:** `AGENTS.md`, `.agents/rules/AGENTS.md`, `docs/01-PRD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`  
> **Target Endpoint & Rute:** `GET /pembimbing/peserta/{id}` (`route('pembimbing.peserta.show')`)  
> **Berkas Acuan Desain Resmi:**  
> 1. `docs/plans/2026-11-09-profil-360-crop-action-buttons-hero.png` (Crop Acuan Tombol Logbook & Tugas)  
> 2. `docs/plans/2026-11-09-profil-360-v3-master.jpg` (Tampilan Keseluruhan Master Mockup)

---

## 1. 🔍 EXECUTIVE SUMMARY & ANALISIS MASALAH

Berdasarkan tinjauan visual langsung terhadap crop tombol yang diunggah pengguna:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 🔴 MASALAH 1: KEDUA TOMBOL AKSI TERLALU KECIL (CRAMPED TOUCH TARGET)                  │
│    - Saat ini tombol [Logbook] dan [Tugas] memiliki tinggi hanya ~34px dengan padding  │
│      0.45rem 1.4rem dan font 13px (0.8125rem).                                         │
│    - Di sebelah avatar berdiameter 84px dan teks nama 24px, kedua tombol tampak        │
│      kekecilan dan kurang memiliki bobot visual sebagai Call-to-Action (CTA) utama.    │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 🔴 MASALAH 2: PROPORSI KOTAK KARTU HERO & DISTRIBUSI GRID TERLALU RAPAT                │
│    - Ruang vertikal dan horizontal pada kotak kartu hero (.mentee-hero-card) serta     │
│      grid sel data peserta (.data-peserta-grid) masih terasa sedikit padat.            │
│    - Jarak baris dan kolom (gap) pada grid data peserta perlu dioptimalkan agar tiap   │
│      entitas data (Email, Telepon, Kompetensi Keahlian, NISN/NIM, Bidang) berdiri      │
│      secara proporsional, rapi, dan mudah dipindai mata (scannable).                   │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. 📐 MATRIKS KOMPARASI SPESIFIKASI (BEFORE VS AFTER)

Semua developer junior dan AI pelaksana wajib menerapkan parameter ukuran berikut:

| Elemen Komponen | Nilai Saat Ini (Current) | Nilai Target Rencana (Planned Target) | Manfaat Desain & UX |
|---|---|---|---|
| **Tinggi Tombol (`height`)** | `~34px` | **`44px`** | Memenuhi standar ukuran sentuh human-interface (WCAG Target Size). |
| **Padding Tombol (`.btn-pill-action`)** | `0.45rem 1.4rem` | **`0.65rem 1.85rem`** | Tombol terlihat lebih panjang, mantap, dan mudah diklik. |
| **Ukuran Font Tombol (`font-size`)** | `0.8125rem` (13px) | **`0.9375rem`** (15px) | Keterbacaan teks label tombol meningkat tajam. |
| **Lebar Minimal Tombol (`min-width`)** | Auto (`~85px`) | **`115px`** | Menghasilkan bentuk kapsul pil yang simetris dan kokoh. |
| **Jarak Antar Tombol (`gap`)** | `0.65rem` | **`0.85rem`** (14px) | Pemisahan tombol lebih lega tanpa risiko salah klik. |
| **Padding Kotak Kartu Hero** | `2rem 2.25rem` | **`2.25rem 2.5rem`** | Kotak kartu hero tampak lebih megah dan berwibawa. |
| **Gap Grid Kartu Hero (`gap`)** | `2.25rem` | **`2.5rem`** | Pembatas vertikal antara biodata dan data peserta lebih lega. |
| **Jarak Grid Data Peserta (`gap`)** | `0.75rem 1.75rem` | **`1rem 2rem`** | Setiap sel data (Email, WA, NISN, Bidang) berdiri teratur. |
| **Font Value Data Peserta** | `0.875rem` (14px) | **`0.9375rem`** (15px, font-weight 600) | Data peserta lebih kontras dan menonjol. |

---

## 3. 🎨 BLUEPRINT VISUAL & KOMPONEN HERO CARD

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ KARTU HERO PROFIL MENTEE (#0F3875) ── Padding: 2.25rem 2.5rem ─────────────────────────┐
│                                                                                        │
│ ┌─ KOLOM KIRI: BIODATA (46%) ───────────┐ ┌─ KOLOM KANAN: DATA PESERTA (54%) ──────────┐│
│ │                                       │ │                                            ││
│ │ ┌──────────┐  Naufal Pratama          │ │ DATA PESERTA                               ││
│ │ │    NA    │  SMK Negeri 2 Depok      │ │                                            ││
│ │ │ (Avatar  │  [Aktif] [Jalur Umum]    │ │ EMAIL:              TELEPON / WA:          ││
│ │ │   84px   │                          │ │ peserta@magkom.go.id 081273523851          ││
│ │ │ #F59E0B) │  ┌──────────┐┌──────────┐│ │ (Gap baris: 1rem, Gap kolom: 2rem)        ││
│ │ └──────────┘  │ Logbook  ││  Tugas   ││ │                                            ││
│ │               │ (Outl)   ││ (#F59E0B)││ │ KOMPETENSI KEAHLIAN: NISN:                 ││
│ │               │ H: 44px  ││ H: 44px  ││ │ TKJ                 0054321987             ││
│ │               └──────────┘└──────────┘│ │                                            ││
│ │                                       │ │ BIDANG:                                    ││
│ │               (Kedua Tombol Lebih     │ │ Aplikasi Informatika & E-Government        ││
│ │                Besar & Nyaman)        │ │                                            ││
│ │                                       │ │ Data guru pembimbing sekolah belum diisi.  ││
│ └───────────────────────────────────────┘ └────────────────────────────────────────────┘│
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. 🛠️ INSTRUKSI KODE DETAIL UNTUK JUNIOR DEVELOPER & AI MODEL

Semua modifikasi dilakukan secara terpusat pada stylesheet CSS tanpa perlu merusak struktur Blade.

### Bagian CSS (`resources/css/pages/pembimbing/peserta-show.css`):

1. **Perbesaran Dimensi & Ergonomi Tombol Aksi**:
   ```css
   .mentee-action-buttons-row {
       display: flex;
       gap: 0.85rem;
       margin-top: 1rem;
       align-items: center;
   }

   .btn-pill-action {
       display: inline-flex;
       align-items: center;
       justify-content: center;
       min-width: 115px;
       min-height: 44px;
       padding: 0.65rem 1.85rem;
       border-radius: 9999px;
       font-size: 0.9375rem;
       font-weight: 700;
       letter-spacing: 0.01em;
       text-decoration: none;
       transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
       cursor: pointer;
       box-sizing: border-box;
   }

   .btn-pill-logbook {
       background-color: transparent;
       border: 1.5px solid rgba(255, 255, 255, 0.75);
       color: #FFFFFF;
   }

   .btn-pill-logbook:hover {
       background-color: rgba(255, 255, 255, 0.18);
       border-color: #FFFFFF;
       color: #FFFFFF;
       transform: translateY(-2px);
       box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
   }

   .btn-pill-tugas {
       background-color: var(--mentee-amber); /* #F59E0B */
       border: 1.5px solid var(--mentee-amber);
       color: var(--mentee-text-navy);        /* #0F172A */
   }

   .btn-pill-tugas:hover {
       background-color: var(--mentee-amber-dark);
       border-color: var(--mentee-amber-dark);
       color: var(--mentee-text-navy);
       transform: translateY(-2px);
       box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35);
   }
   ```

2. **Penyempurnaan Kotak Kartu Hero & Spasi Grid**:
   ```css
   .mentee-hero-card {
       background-color: var(--mentee-navy-card);
       border-radius: 16px;
       padding: 2.25rem 2.5rem;
       color: #FFFFFF;
       display: grid;
       grid-template-columns: 1fr 1.25fr;
       gap: 2.5rem;
       box-shadow: 0 10px 25px -5px rgba(15, 56, 117, 0.35);
       margin-bottom: 1.25rem;
       align-items: center;
   }

   .data-peserta-grid {
       display: grid;
       grid-template-columns: repeat(2, 1fr);
       gap: 1rem 2rem;
   }

   .data-peserta-cell {
       display: flex;
       flex-direction: column;
       gap: 0.25rem;
   }

   .data-peserta-val {
       font-size: 0.9375rem;
       font-weight: 600;
       color: #FFFFFF;
       word-break: break-word;
       line-height: 1.35;
   }
   ```

---

## 5. 🚦 PROTOKOL QUALITY GATE & VERIFIKASI

1. **Build Aset Vite**:
   ```bash
   npm run build
   ```
   *Ekspektasi:* Kompilasi sukses tanpa warning/error.
2. **Feature Testing (PHPUnit)**:
   ```powershell
   & "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test tests/Feature/PembimbingPortalStandardizationTest.php
   ```
   *Ekspektasi:* Seluruh 5 assertions feature test lolos 100%.
3. **Verifikasi Visual**:
   - [ ] Tombol `[Logbook]` dan `[Tugas]` berukuran lebih besar (tinggi 44px, min-width 115px, font 15px bold).
   - [ ] Kotak kartu hero lebih lega dengan padding 2.25rem 2.5rem.
   - [ ] Grid sel data peserta memiliki jarak baris 1rem dan kolom 2rem yang rapi dan terstruktur.

