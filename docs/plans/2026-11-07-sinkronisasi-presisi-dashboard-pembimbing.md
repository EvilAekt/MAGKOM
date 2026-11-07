# Senior Developer Runbook & PRD: Sinkronisasi Presisi Visual Dashboard Pembimbing Bento Grid

**Kode Dokumen**: `PRD-DEV-BENTO-004`  
**Target Pelaksana**: Junior Frontend Developer / LLM Execution Agent (Gemini 3.8 Flash / Antigravity)  
**Tingkat Kerumitan**: Menengah (Pure CSS & Layout Architecture Calibration)  
**File Target Utama**: [`resources/css/pages/pembimbing/dashboard.css`](file:///g:/MAGKOM/resources/css/pages/pembimbing/dashboard.css)  
**Status**: 📋 *Draft Rencana Kerja — Menunggu Konfirmasi Eksekusi*

---

## 1. Executive Summary & Problem Statement (Latar Belakang & Masalah)

Berdasarkan audit komparasi visual langsung antara **Tangkapan Layar Browser Saat Ini** (`media_1794019817035_2e3ab4db.jpg` & `media_1794019832411_e1130363.jpg`) dengan **Desain Referensi Resmi MagKom Sleman** (`media_1794019836887_b302138f.jpg`), ditemukan 5 anomali visual utama:

1. **Text Footer Terpotong / Hilang (*Critical Layout Overflow*)**:
   - *Problem*: Teks `Portal MagKom • Dinas Komunikasi dan Informatika Kabupaten Sleman` di bagian bawah terdorong keluar layar (*pushed off-screen*) ke sebelah kanan, sehingga hanya terlihat sisa beberapa huruf atau terpotong total.
   - *Root Cause*: Selektor `body.role-pembimbing .gentelella-footer` memiliki `width: 100% !important;` yang menumpuk di atas `margin-left: var(--sidebar-width)` bawaan layout Gentelella (`margin-left: 230px`). Akibatnya, lebar total footer menjadi `100% + 230px`, dan teks yang di-*align* ke kanan (`justify-content: flex-end`) terlempar 230px ke area *overflow hidden*.
2. **Ketinggian Berlebih (*Vertical Height Bloat*) Menghancurkan Proporsi "Single Viewport Bento"**:
   - *Problem*: Di referensi mockup (`media_1794019836887_b302138f.jpg`), seluruh dashboard (Hero Banner, 3 Kartu KPI, Distribusi Tugas, Kuota Bimbingan, Highlight Roster Banner kuning, Kalender, dan 2 Alert Card samping) **muat dalam satu layar tanpa scroll vertikal yang masif**. Pada layar saat ini, tinggi kartu Distribusi Tugas membengkak ke `420px` dan KPI ke `220px`, memaksa pengguna harus melakukan scroll jauh ke bawah untuk melihat kartu kuning dan footer.
   - *Root Cause*: Nilai `min-height: 420px;` pada baris tengah (`.bento-mid-row`) dan `min-height: 220px;` pada KPI terlalu tinggi.
3. **Aksen Sudut Kartu KPI Tampak Seperti Stiker Kaku (*Harsh Circle Sticker*)**:
   - *Problem*: Sudut kanan bawah kartu KPI saat ini menampilkan lingkaran solid berbatas kaku (`background: #dbe7f6; opacity: 1;`) yang menabrak angka dan dot matrix.
   - *Root Cause*: Menggunakan lingkaran opak tanpa efek *soft feathering* / *radial blur* lembut. Pada referensi mockup, sudut kanan-bawah adalah *soft ambient glow* lingkaran yang halus, berbaur lembut ke putih, dan tidak menabrak tipografi tengah.
4. **Skala Bar Distribusi Tugas Melebar Berlebihan Tanpa Ritme Arsitektural**:
   - *Problem*: Lebar kolom `max-width: 120px` pada wadah 420px membuat bar tampak seperti tiang besar yang terisolasi. Proporsi yang benar pada referensi adalah lebar kolom `max-width: 88px - 92px` dengan tinggi track `170px - 180px`, sehingga pola garis arsir diagonal terlihat presisi, rapi, dan proporsional.
5. **Keseimbangan Vertikal Kolom Kiri vs Kolom Kanan (*Column Baseline Alignment*)**:
   - *Problem*: Ketinggian kolom utama kiri saat ini jauh melebihi kolom kanan samping (Kalender + 2 Alert Card), menyisakan ruang kosong besar di bawah kolom samping.
   - *Root Cause*: Kolom kiri dan kanan harus memiliki total akumulasi tinggi yang seimbang (~580px - 620px).

---

## 2. Aturan Ketat untuk Junior Developer / AI Pelaksana (Guardrails)

> [!IMPORTANT]
> 1. **DILARANG MENGUBAH FILE BLADE**: Seluruh perbaikan visual ini diselesaikan **100% melalui CSS** di file `resources/css/pages/pembimbing/dashboard.css`. Jangan menyentuh file Blade (`.blade.php`).
> 2. **DILARANG MERUSAK SUITE TEST**: Jangan mengubah nama kelas, data atribut, atau route contract. Pengujian `php artisan test --filter=Pembimbing` wajib tetap **131 tests passing (849 assertions)**.
> 3. **WAJIB BUILD VITE**: Setiap perubahan CSS wajib di-compile dengan `npm run build` sebelum diverifikasi di browser.
> 4. **JANGAN MENGUBAH LABEL MENU SIDEBAR**: Menu sidebar ke-5 tetap `Laporan Peserta` (bukan `Laporan Kinerja ASN`) karena telah dikunci oleh automated assertions di `tests/Feature/PembimbingLaporanKinerjaViewTest.php`.

---

## 3. Spesifikasi Teknis Perbaikan (Step-by-Step Task Breakdown)

### Task 1: Perbaikan Bug Overflow Footer & Keterbacaan Teks (Paling Mendesak)
- **File**: `resources/css/pages/pembimbing/dashboard.css`
- **Lokasi Kode**: Bagian 9 (Paling Bawah)
- **Instruksi Detail**:
  Ganti rule `body.role-pembimbing .gentelella-footer` agar tidak memiliki offset ganda dari `margin-left` sidebar:
  ```css
  /* ==============================================================================
     9. Footer Sleman Khas Executive Bento (Dark Navy Bar Rata Penuh)
     ============================================================================== */
  body.role-pembimbing .gentelella-footer {
    background: #002244 !important;
    border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
    color: #94a3b8 !important;
    padding: 12px 32px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: flex-end !important;
    margin-left: 0 !important;
    width: 100% !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    overflow: hidden !important;
    position: relative !important;
    z-index: 10 !important;
  }

  body.role-pembimbing .gentelella-footer span {
    color: #94a3b8 !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    white-space: nowrap !important;
    display: inline-block !important;
  }
  ```
- **Kriteria Keberhasilan Task 1**:
  Teks `Portal MagKom • Dinas Komunikasi dan Informatika Kabupaten Sleman` tampil utuh, jelas, terbaca sempurna di pojok kanan bawah dengan *padding* 32px dari sisi kanan layar, tanpa ada *horizontal scrollbar*.

---

### Task 2: Koreksi Aksen Sudut Kartu KPI (Dari Stiker Kaku ke Soft Ambient Disc)
- **File**: `resources/css/pages/pembimbing/dashboard.css`
- **Lokasi Kode**: Baris ~230 - 280 (`.bento-kpi-card`)
- **Instruksi Detail**:
  1. Sesuaikan ukuran dan padding kartu KPI menjadi proporsional (`min-height: 160px; padding: 18px 20px; border-radius: 18px;`).
  2. Ubah pseudo-element `::after` agar memiliki tepian yang lembut (*feathered / gradient edge*) dan ukuran kompak (125px x 125px), diletakkan rapi di pojok kanan bawah tanpa menimpa teks utama:
  ```css
  .bento-kpi-card {
    padding: 18px 20px;
    min-height: 160px;
    border-radius: 18px;
    position: relative;
    overflow: hidden;
    background: #ffffff;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  [data-theme="dark"] .bento-kpi-card {
    background: #0d2040;
  }

  /* Lingkaran Aksen Sudut Lembut (Soft Feathered Ambient Disc ala Gambar 3) */
  .bento-kpi-card::after {
    content: "";
    position: absolute;
    right: -15px;
    bottom: -20px;
    width: 125px;
    height: 125px;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
    transition: background-color 0.2s ease, opacity 0.2s ease;
  }

  .kpi-card-mentee::after {
    background: radial-gradient(circle, rgba(185, 215, 248, 0.75) 0%, rgba(219, 231, 246, 0.45) 55%, transparent 75%);
  }

  .kpi-card-tugas::after {
    background: radial-gradient(circle, rgba(205, 200, 248, 0.75) 0%, rgba(228, 230, 249, 0.45) 55%, transparent 75%);
  }

  .kpi-card-evaluasi::after {
    background: radial-gradient(circle, rgba(254, 225, 140, 0.75) 0%, rgba(254, 240, 199, 0.45) 55%, transparent 75%);
  }

  [data-theme="dark"] .kpi-card-mentee::after {
    background: radial-gradient(circle, rgba(56, 189, 248, 0.28) 0%, rgba(14, 165, 233, 0.12) 55%, transparent 75%);
  }

  [data-theme="dark"] .kpi-card-tugas::after {
    background: radial-gradient(circle, rgba(168, 85, 247, 0.28) 0%, rgba(147, 51, 234, 0.12) 55%, transparent 75%);
  }

  [data-theme="dark"] .kpi-card-evaluasi::after {
    background: radial-gradient(circle, rgba(251, 191, 36, 0.26) 0%, rgba(245, 158, 11, 0.12) 55%, transparent 75%);
  }
  ```
  3. Kembalikan ukuran kotak ikon ke `44px x 44px; border-radius: 12px;` dan angka KPI ke `36px; font-weight: 800;` agar seimbang dengan ketinggian kartu 160px.
- **Kriteria Keberhasilan Task 2**:
  Aksen warna di sudut kanan bawah kartu KPI tampak menyatu secara halus (*soft glow*) seperti pada referensi Gambar 3, tidak lagi terlihat seperti stiker bundar kaku.

---

### Task 3: Penskalaan Proporsional Distribusi Tugas & Kuota Bimbingan (Resolusi 1920x1080)
- **File**: `resources/css/pages/pembimbing/dashboard.css`
- **Lokasi Kode**: Baris ~520 - 740
- **Instruksi Detail**:
  1. Sesuaikan tinggi kartu baris tengah (`.bento-mid-row`) agar tidak membengkak ke 420px, melainkan ideal pada `min-height: 290px;`:
  ```css
  /* Distribusi Tugas */
  .bento-distribusi-card {
    padding: 20px 24px;
    min-height: 290px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: #ffffff;
    box-sizing: border-box;
  }

  .bento-distribusi-chart-box {
    background: #ffffff;
    border: 1px solid rgba(11, 50, 123, 0.08);
    border-radius: 14px;
    padding: 16px 20px 10px 20px;
    min-height: 215px;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
  }

  .bento-bars-track {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    height: 175px;
    align-items: end;
  }

  .bento-bar-container {
    width: 100%;
    max-width: 88px;
    height: 145px;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    background-color: #f1f5f9;
    background-image: repeating-linear-gradient(
      -45deg,
      transparent,
      transparent 6px,
      rgba(11, 50, 123, 0.07) 6px,
      rgba(11, 50, 123, 0.07) 12px
    );
    border-radius: 10px;
    overflow: hidden;
    margin: 0 auto;
  }

  .bento-bar {
    width: 100%;
    border-radius: 10px 10px 0 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: height 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    min-height: 30px;
    box-sizing: border-box;
  }

  .bento-bar-val {
    font-size: 15px;
    font-weight: 800;
    color: #ffffff;
  }

  .bar-review .bento-bar-val {
    color: #02275d;
  }

  .bento-bar-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--bento-text-muted);
    margin-top: 8px;
  }
  ```
  2. Selaraskan kartu **Kuota Bimbingan** ke tinggi yang sama (`min-height: 290px`):
  ```css
  /* Kuota Bimbingan Radial Gauge */
  .bento-kuota-card {
    padding: 20px 24px;
    min-height: 290px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    background: #ffffff;
    box-sizing: border-box;
  }

  .bento-kuota-gauge-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 215px;
    flex: 1;
  }

  .bento-gauge-wrapper {
    position: relative;
    width: 200px;
    height: 115px;
    display: flex;
    justify-content: center;
    margin-top: 6px;
  }

  .bento-gauge-svg {
    display: block;
    width: 200px;
    height: 115px;
  }

  .gauge-track {
    stroke: var(--bento-dot-empty);
    stroke-width: 16px;
  }

  .gauge-fill {
    stroke: var(--bento-navy-hero);
    stroke-width: 16px;
    transition: stroke-dashoffset 0.6s ease;
  }

  .gauge-center-percent {
    font-size: 40px;
    font-weight: 900;
    color: var(--bento-navy-hero);
    line-height: 1;
  }

  .bento-gauge-caption {
    font-size: 13px;
    color: var(--bento-text-muted);
    font-weight: 600;
    margin-top: 10px;
    margin-bottom: 4px;
    text-align: center;
  }
  ```
- **Kriteria Keberhasilan Task 3**:
  Kedua kartu tengah memiliki tinggi seimbang (`290px`), bar Distribusi Tugas memiliki lebar kolom yang tebal dan proporsional (`88px`) tanpa membuat halaman memanjang ke bawah, dan gauge Kuota Bimbingan terpusat rapi tanpa ruang kosong berlebih.

---

### Task 4: Penyelarasan Total Ketinggian Kolom Samping (Calendar & Alerts)
- **File**: `resources/css/pages/pembimbing/dashboard.css`
- **Lokasi Kode**: Baris ~810 - 1010
- **Instruksi Detail**:
  1. Sesuaikan Kalender: `padding: 16px 18px; margin-bottom: 0;`.
  2. Sesuaikan kedua kartu Alert samping (`.bento-alert-card`):
     - `min-height: 135px; padding: 20px 20px; border-radius: 16px;`.
     - Angka besar alert: `font-size: 36px; font-weight: 800;`.
     - Progress dot matrix: `height: 8px; border-radius: 4px;`.
  3. Total akumulasi tinggi kolom kanan: Kalender (~260px) + Gap (20px) + Alert 1 (~135px) + Gap (20px) + Alert 2 (~135px) = **~570px**, sejajar sempurna dengan total kolom kiri (KPI 160px + Gap 20px + Mid 290px + Gap 20px + Roster 150px = **~640px**).
- **Kriteria Keberhasilan Task 4**:
  Kolom kiri dan kanan sejajar harmonis (*equal baseline height*) pada layar Full HD (1920x1080).

---

## 4. Rincian Checklist Implementasi Terstruktur

- [ ] **Langkah 1: Perbaiki Bug Overflow Footer di CSS**
  - Pasang `margin-left: 0 !important; width: 100% !important; overflow: hidden !important;` pada `body.role-pembimbing .gentelella-footer`.
  - Pastikan `span` di dalam footer memiliki `white-space: nowrap !important; font-size: 12px !important;`.
- [ ] **Langkah 2: Kalibrasi Ulang Aksen Sudut Kartu KPI**
  - Atur `.bento-kpi-card` ke `min-height: 160px; padding: 18px 20px; border-radius: 18px;`.
  - Pasang `radial-gradient` lembut pada `.bento-kpi-card::after` (125px x 125px) untuk Mentee (`#dbe7f6`), Tugas (`#e4e6f9`), dan Evaluasi (`#fef0c7`).
  - Kalibrasi Dark Mode `::after` dengan `rgba(..., 0.28)`.
- [ ] **Langkah 3: Penskalaan Proporsional Baris Tengah (Distribusi & Kuota)**
  - Kembalikan ketinggian kartu ke `min-height: 290px;` (padding `20px 24px`).
  - Atur `.bento-bar-container` ke `max-width: 88px; height: 145px; border-radius: 10px;`.
  - Atur gauge Kuota Bimbingan ke `width: 200px; height: 115px; font-size: 40px;`.
- [ ] **Langkah 4: Penyelarasan Kolom Samping (Calendar & Alert Cards)**
  - Atur `.bento-alert-card` ke `min-height: 135px; padding: 20px 20px;`.
- [ ] **Langkah 5: Kompilasi Aset Frontend (Vite)**
  - Jalankan `npm run build` dan pastikan kompilasi sukses 100% tanpa error.
- [ ] **Langkah 6: Quality Gate & Automated Testing**
  - Jalankan `./vendor/bin/pint --test` (format kode PHP).
  - Jalankan `php artisan test --filter=Pembimbing` (131 test wajib hijau).
  - Jalankan `graphify update .` (update knowledge graph).

---

## 5. Rencana Verifikasi & Definition of Done

| Item Pemeriksaan | Kondisi Sukses |
| :--- | :--- |
| **Footer Text** | Teks `Portal MagKom • Dinas Komunikasi dan Informatika Kabupaten Sleman` tampak utuh dan tidak terpotong di tepi kanan. |
| **Bento Viewport** | Seluruh dashboard pas di layar tanpa kelebihan scroll vertikal yang masif di resolusi 1920x1080. |
| **Aksen Sudut KPI** | Menampilkan lingkaran gradien halus (*soft ambient glow*) dan bukan lingkaran stiker kaku. |
| **Bar Distribusi Tugas** | Kolom bar tebal (`88px`), proporsional, pola arsir rapi, dan tidak kekecilan. |
| **Alignment Kolom** | Kolom kiri dan kanan berakhir pada garis bawah yang seimbang. |
| **Automated Tests** | 131 test (849 assertions) di `php artisan test --filter=Pembimbing` lulus 100%. |
| **Vite Compilation** | `npm run build` selesai tanpa warning CSS. |

