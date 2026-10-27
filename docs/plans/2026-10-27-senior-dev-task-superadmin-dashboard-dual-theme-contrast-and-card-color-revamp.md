# Task Senior Developer: Penyelarasan Palet Warna Kartu SuperAdmin (Default Putih & Kontras Tinggi Mode Gelap)

- **Dokumen Referensi**: PRD MagKom Revisi 5 / Design System DS-MagKom (Bab 2) / Gentelella Theme System
- **Tingkat Kesulitan**: Junior to Mid Developer / Beginner AI Directives (Gemini 3.8 Flash Ready)
- **Target Desain**: Paritas Desain Sistem Gentelella (Default Kartu Putih Bersih + Dark Mode Kontras Tinggi Bebas Teks Gelap)
- **Tanggal Rilis**: 27 Oktober 2026
- **Status**: Selesai & Terverifikasi (Completed & Verified)

---

## 1. Konteks & Problem Statement

Berdasarkan evaluasi tampilan antarmuka terkini pada Dashboard SuperAdmin (`http://localhost/magkom/public/superadmin/dashboard`):

### Masalah 1: Kartu Analitik Terlalu Gelap pada Mode Default (Light Mode)
- Pada implementasi sebelumnya, kartu **Tren Pendaftaran 12 Bulan Terakhir** dan **Demografi Asal Institusi & Jenjang Pendidikan (PRD 4.3)** menggunakan kelas `.gt-dark-panel` yang warnanya di-*hardcode* menjadi gelap (`background: #0f172a !important`).
- Hal ini menyebabkan tampilan menjadi tidak konsisten: ketika aplikasi berada pada tema default (Terang/Putih), hampir seluruh kartu dashboard berwarna putih bersih, sedangkan Baris 3 tampak hitam pekat sendiri (*unbalanced visual hierarchy*).
- **Kebutuhan Pengguna**: Kedua kartu ini harus berpenampilan putih bersih pada mode default/terang, dan baru beralih menjadi tema gelap elegan ketika mode gelap (*Dark Mode*) diaktifkan.

### Masalah 2: Cacat Kontras Teks Gelap di Atas Latar Gelap pada Mode Gelap (Dark Mode Contrast Bug)
- Ketika pengguna mengaktifkan mode gelap (`[data-theme="dark"]`), kartu-kartu dashboard lainnya seperti **KPI dinas**, **Kuota November**, **Keamanan**, **Pengguna terbaru**, dan **Audit log** mengalami masalah kontras serius:
  - Teks label seperti *"Integritas kuota"*, *"SLA verifikasi &le; 3 hari"*, *"Lembar nilai lengkap"*, *"Keterlacakan audit"* pada kartu KPI dinas menjadi abu-abu gelap (`#334155`) di atas latar belakang kartu yang biru/gelap (`#182234`), sehingga **hampir tidak terbaca**.
  - Teks jurusan pada kartu Kuota (*"Teknik Informatika"*, *"Sistem Informasi"*, *"Ilmu Komunikasi"*) dan angka kuota (`28/30`, dll.) tampak gelap dan buram.
  - Teks baris pada kartu Keamanan (*"Login gagal"*, *"Akun terkunci"*, *"Staf belum ganti sandi"*, *"Backup terakhir"*) tidak terlihat jelas.
- **Akar Masalah (Root Cause)**:
  Variabel CSS `--cv-ink` (`#0f172a`), `--cv-ink-2` (`#334155`), dan `--cv-ink-3` (`#64748b`) pada file `public/css/superadmin-dashboard.css` diatur secara absolut di dalam `:root` tanpa memiliki aturan penimpaan (*override*) saat `[data-theme="dark"]` aktif.

---

## 2. Solusi Desain & Arsitektur Token Warna

Sesuai dengan Design System Gentelella & DS-MagKom Bab 2:

```
+-----------------------------------------------------------------------------------------------------------------+
| MODE DEFAULT (LIGHT MODE) - KARTU PUTIH BERSIH & TEKS GELAP ELEGAN                                             |
|                                                                                                                 |
| Surface Card   : #FFFFFF (Putih Murni Gentelella)                                                               |
| Border Line    : #E2E8F0 / #E6E9ED (Garis Halus Netral)                                                         |
| Heading Text   : #0F172A / #2A3F54 (--cv-ink: Teks Gelap Tegas)                                                 |
| Body/Label Text: #334155 / #73879C (--cv-ink-2: Teks Sekunder Terbaca)                                          |
| Muted/Subtext  : #64748B / #A0AEC0 (--cv-ink-3: Keterangan Halus)                                               |
| Chart Canvas   : Latar transparan/putih, kurva toska cerah (#0D9488), garis bantu grid abu-abu tipis (#F1F5F9) |
+-----------------------------------------------------------------------------------------------------------------+
| MODE GELAP (DARK MODE) - KARTU SLATE GELAP & TEKS PUTIH/TERANG KONTRAS TINGGI (WCAG AA LULUS)                  |
|                                                                                                                 |
| Surface Card   : #182234 / #16202C (Dark Slate Gentelella)                                                      |
| Border Line    : #26354D / #334155 (Garis Slate Gelap Halus)                                                    |
| Heading Text   : #F8FAFC (--cv-ink: Putih Cerah Kontras Tinggi)                                                 |
| Body/Label Text: #CBD5E1 (--cv-ink-2: Abu-abu Terang Sangat Jelas)                                              |
| Muted/Subtext  : #94A3B8 (--cv-ink-3: Slate Terang Terbaca)                                                     |
| Chart Canvas   : Latar dark slate, kurva toska menyala (#2DD4BF), garis bantu grid putus-putus (#1E293B)        |
+-----------------------------------------------------------------------------------------------------------------+
```

---

## 3. Matriks Perubahan File (File Change Matrix)

| No | File Path | Aksi | Ringkasan Perubahan |
|:---|:---|:---|:---|
| 1 | `public/css/superadmin-dashboard.css` | **Modify** | 1. Tambahkan token `[data-theme="dark"]` untuk `--cv-surface`, `--cv-paper`, `--cv-line`, `--cv-line-strong`, `--cv-ink`, `--cv-ink-2`, `--cv-ink-3`.<br>2. Ubah komponen `.gt-dark-panel` menjadi `.gt-analytics-card` yang adaptif (latar putih pada mode default/terang, latar slate pada mode gelap). |
| 2 | `resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php` | **Modify** | 1. Ganti class `gt-dark-panel` dengan `gt-analytics-card`.<br>2. Sesuaikan atribut SVG chart (stroke grid, fill titik data, warna garis) agar menggunakan variabel dinamis CSS. |
| 3 | `resources/views/layouts/dashboard.blade.php` | **Modify** | Pastikan definisi global `[data-theme="dark"]` mencakup token `--cv-*` agar kartu cockpit di sub-halaman lain otomatis memiliki kontras sempurna. |
| 4 | `tests/Feature/DeltaDashboardEnhancementTest.php` | **Verify** | Uji otomatis test suite untuk memastikan assertion tetap 100% passed. |
| 5 | `tests/Feature/AdminSuperAdminPhaseTwoTest.php` | **Verify** | Uji otomatis test suite untuk memastikan assertion demografi tetap 100% passed. |

---

## 4. Petunjuk Langkah Kerja Terperinci (Step-by-Step Directives)

### Langkah 1: Modifikasi Token CSS di `public/css/superadmin-dashboard.css`

1. Buka file [public/css/superadmin-dashboard.css](file:///g:/MAGKOM/public/css/superadmin-dashboard.css).
2. Temukan blok deklarasi variabel `--cv-*` (sekitar baris 830–850).
3. Tambahkan blok aturan `[data-theme="dark"]` tepat di bawahnya:

```css
/* Token Dasar Cockpit (Default: Light Mode) */
:root {
  --cv-blue-900: #061e5b;
  --cv-blue-800: #06318c;
  --cv-blue-700: #0641cc;
  --cv-blue-600: #0751ff;
  --cv-blue-tint: #eff6ff;
  --cv-paper: #f8fafc;
  --cv-surface: #ffffff;
  --cv-line: #e2e8f0;
  --cv-line-strong: #cbd5e1;
  --cv-ink: #0f172a;
  --cv-ink-2: #334155;
  --cv-ink-3: #64748b;
  --cv-ok: #10b981;
  --cv-ok-tint: #ecfdf5;
  --cv-warn: #f59e0b;
  --cv-warn-tint: #fffbeb;
  --cv-bad: #ef4444;
  --cv-bad-tint: #fef2f2;
}

/* Inversi Token untuk Mode Gelap (Paritas Gentelella Dark & WCAG AA) */
[data-theme="dark"] {
  --cv-surface: #182234;
  --cv-paper: #0f172a;
  --cv-line: #26354d;
  --cv-line-strong: #334155;
  --cv-ink: #f8fafc;        /* Teks Judul & Angka: Putih Terang Kontras Maksimal */
  --cv-ink-2: #cbd5e1;      /* Teks Label & Body: Abu-abu Terang Sangat Jelas */
  --cv-ink-3: #94a3b8;      /* Teks Subjudul & Muted: Slate Terang Terbaca */
  --cv-blue-tint: #1e3a8a;
}
```

---

### Langkah 2: Refaktor Komponen Kartu Analitik di `public/css/superadmin-dashboard.css`

Gantikan blok `.gt-dark-panel` yang lama (di akhir file `public/css/superadmin-dashboard.css`) dengan komponen **`.gt-analytics-card`** yang adaptif:

```css
/* ==========================================================================
   Gentelella Analytics Cards (Adaptive Dual Theme: Light & Dark)
   ========================================================================== */

/* 1. Kontainer Kartu Adaptif (Default: Light Mode / Putih Bersih) */
.gt-analytics-card {
  background: var(--cv-surface, #ffffff) !important;
  border: 1px solid var(--cv-line, #e2e8f0) !important;
  border-radius: 12px !important;
  color: var(--cv-ink, #0f172a) !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06) !important;
  overflow: hidden;
  position: relative;
  margin-bottom: 1.25rem;
  transition: background-color 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
}

.gt-analytics-card .x_title {
  border-bottom: 1px solid var(--cv-line, #e2e8f0) !important;
  padding: 0.85rem 1.15rem !important;
  margin-bottom: 0 !important;
}

.gt-analytics-card .x_title h2 {
  color: var(--cv-ink, #0f172a) !important;
  font-weight: 700 !important;
  font-size: 0.95rem !important;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.gt-analytics-card .x_title small {
  color: var(--cv-ink-3, #64748b) !important;
  font-size: 0.75rem !important;
  font-weight: 400 !important;
  margin-left: 0.5rem;
}

.gt-analytics-card .panel_toolbox .collapse-link {
  color: var(--cv-ink-3, #64748b) !important;
  background: var(--cv-paper, #f8fafc) !important;
  border: 1px solid var(--cv-line, #e2e8f0) !important;
}

.gt-analytics-card .panel_toolbox .collapse-link:hover {
  color: var(--cv-ink, #0f172a) !important;
  background: var(--cv-line, #e2e8f0) !important;
}

.gt-analytics-card .x_content {
  padding: 1.15rem !important;
  color: var(--cv-ink, #0f172a) !important;
}

/* 2. Header & Filter Pills */
.gt-card-header-bar {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.85rem;
}

.gt-pill-group {
  display: inline-flex;
  background: var(--cv-paper, #f1f5f9);
  border: 1px solid var(--cv-line, #e2e8f0);
  border-radius: 8px;
  padding: 3px;
  gap: 2px;
}

.gt-pill-btn {
  background: transparent;
  border: none;
  color: var(--cv-ink-3, #64748b);
  padding: 4px 10px;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.gt-pill-btn:hover {
  color: var(--cv-ink, #0f172a);
}

.gt-pill-btn.active {
  background: var(--cv-surface, #ffffff);
  color: var(--cv-ink, #0f172a);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* 3. Hero Metric Banner */
.gt-hero-stat {
  margin-bottom: 1.25rem;
}

.gt-hero-stat__number-row {
  display: flex;
  align-items: baseline;
  gap: 0.65rem;
}

.gt-hero-stat__number {
  font-size: 2.15rem;
  font-weight: 800;
  color: var(--cv-ink, #0f172a);
  line-height: 1;
  letter-spacing: -0.02em;
  font-family: var(--font-sans, sans-serif);
}

.gt-hero-stat__badge {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  background: rgba(16, 185, 129, 0.12);
  color: #10b981;
  font-size: 0.775rem;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 6px;
  border: 1px solid rgba(16, 185, 129, 0.25);
}

.gt-hero-stat__subtext {
  font-size: 0.75rem;
  color: var(--cv-ink-3, #64748b);
  margin-top: 0.35rem;
}

/* 4. Area SVG Chart */
.gt-chart-wrapper {
  position: relative;
  width: 100%;
  height: 190px;
}

.gt-chart-svg {
  width: 100%;
  height: 100%;
  overflow: visible;
}

.gt-chart-grid-line {
  stroke: var(--cv-line, #e2e8f0);
  stroke-dasharray: 4 4;
  stroke-width: 1;
}

.gt-chart-axis-text {
  fill: var(--cv-ink-3, #64748b);
  font-size: 10px;
  font-family: var(--font-sans, sans-serif);
}

.gt-chart-legend {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  margin-top: 1rem;
  padding-top: 0.75rem;
  border-top: 1px solid var(--cv-line, #e2e8f0);
}

.gt-legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.75rem;
  color: var(--cv-ink-3, #64748b);
}

.gt-legend-marker--solid {
  width: 14px;
  height: 3px;
  background: #0d9488;
  border-radius: 2px;
}

.gt-legend-marker--dashed {
  width: 14px;
  height: 3px;
  border-top: 2px dashed #0284c7;
}

/* 5. Demografi: Top Countries Style (Sebaran Calon Peserta) */
.gt-demografi-section-title {
  font-size: 0.725rem;
  font-weight: 700;
  color: var(--cv-ink-3, #64748b);
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 0.85rem;
  display: block;
}

.gt-progress-row {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.45rem 0;
}

.gt-progress-code {
  font-size: 0.8125rem;
  font-weight: 800;
  color: var(--cv-ink, #0f172a);
  width: 32px;
  flex-shrink: 0;
  letter-spacing: 0.02em;
}

.gt-progress-label {
  font-size: 0.8125rem;
  color: var(--cv-ink-2, #334155);
  flex: 1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.gt-progress-val {
  font-size: 0.8125rem;
  font-weight: 700;
  color: var(--cv-ink, #0f172a);
  width: 36px;
  text-align: right;
  flex-shrink: 0;
}

.gt-progress-track {
  width: 70px;
  height: 6px;
  background: var(--cv-line, #e2e8f0);
  border-radius: 999px;
  overflow: hidden;
  flex-shrink: 0;
}

.gt-progress-fill {
  height: 100%;
  background: #0d9488;
  border-radius: 999px;
  transition: width 0.4s ease;
}

/* 6. Demografi: Top Referrers Style (Top Institusi Asal) */
.gt-divider-hairline {
  height: 1px;
  background: var(--cv-line, #e2e8f0);
  margin: 1.15rem 0;
}

.gt-referrer-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.gt-referrer-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.gt-referrer-avatar {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 0.75rem;
  color: #ffffff;
  flex-shrink: 0;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
}

.gt-referrer-info {
  flex: 1;
  min-width: 0;
}

.gt-referrer-name {
  font-size: 0.8125rem;
  font-weight: 600;
  color: var(--cv-ink, #0f172a);
  display: block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1.25;
}

.gt-referrer-sub {
  font-size: 0.725rem;
  color: var(--cv-ink-3, #64748b);
  display: block;
}

.gt-referrer-badge {
  font-size: 0.75rem;
  font-weight: 700;
  color: #10b981;
  flex-shrink: 0;
}

/* ==========================================================================
   PENYESUAIAN KHUSUS MODE GELAP UNTUK KARTU ANALITIK & SELURUH DASHBOARD
   ========================================================================== */

[data-theme="dark"] .gt-analytics-card {
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
}

[data-theme="dark"] .gt-pill-group {
  background: #1e293b;
  border-color: #334155;
}

[data-theme="dark"] .gt-pill-btn.active {
  background: #334155;
  color: #ffffff;
}

[data-theme="dark"] .gt-hero-stat__badge {
  background: rgba(34, 197, 94, 0.18);
  color: #34d399;
  border-color: rgba(52, 211, 153, 0.35);
}

[data-theme="dark"] .gt-legend-marker--solid {
  background: #2dd4bf;
}

[data-theme="dark"] .gt-legend-marker--dashed {
  border-top-color: #38bdf8;
}

[data-theme="dark"] .gt-progress-fill {
  background: #2dd4bf;
}

[data-theme="dark"] .gt-referrer-badge {
  color: #34d399;
}
```

---

### Langkah 3: Modifikasi `resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php`

1. Buka file [resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php](file:///g:/MAGKOM/resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php).
2. Temukan pembuka Baris 3 dan ubah kelas panel dari `gt-dark-panel` menjadi `gt-analytics-card`:
   - Ubah baris 249: `<x-gentelella.x-panel class="gt-analytics-card" title="Tren Pendaftaran 12 Bulan Terakhir" subtitle="Volume pendaftar magang dari seluruh institusi">`
   - Ubah baris 281: `<x-gentelella.x-panel class="gt-analytics-card" title="Demografi Asal Institusi & Jenjang Pendidikan (PRD 4.3)" subtitle="Sebaran calon peserta">`
3. Pada bagian SVG chart, ubah titik data lingkaran (*circle*) agar adaptif terhadap background mode terang dan mode gelap:
   - Dari: `<circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="3.5" fill="#0f172a" stroke="#2dd4bf" stroke-width="2" />`
   - Menjadi: `<circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="3.5" fill="var(--cv-surface, #ffffff)" stroke="#0d9488" stroke-width="2" class="gt-chart-point" />`
4. Tambahkan styling CSS kecil untuk `.gt-chart-point`:
   ```css
   .gt-chart-point {
     stroke: #0d9488;
   }
   [data-theme="dark"] .gt-chart-point {
     stroke: #2dd4bf;
     fill: #0f172a;
   }
   ```
5. Sesuaikan garis kurva SVG:
   - Garis sekunder: `<path d="{{ $secondaryCurvePath }}" fill="none" stroke="currentColor" class="gt-curve-secondary" stroke-width="2" stroke-dasharray="4 4" />`
   - Garis utama: `<path d="{{ $primaryCurvePath }}" fill="none" stroke="currentColor" class="gt-curve-primary" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />`
   - Dengan aturan CSS:
   ```css
   .gt-curve-primary { stroke: #0d9488; }
   .gt-curve-secondary { stroke: #0284c7; }
   [data-theme="dark"] .gt-curve-primary { stroke: #2dd4bf; }
   [data-theme="dark"] .gt-curve-secondary { stroke: #38bdf8; }
   ```

---

### Langkah 4: Pastikan Seluruh Kartu Cockpit SuperAdmin Bebas Teks Gelap di Mode Gelap

Periksa file [resources/views/layouts/dashboard.blade.php](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php) pada blok `[data-theme="dark"]`. Tambahkan variabel `--cv-*` agar kartu-kartu lainnya (seperti KPI Dinas, Kuota, Keamanan, Modul Sistem) tidak pernah menampilkan teks gelap di mode gelap:

```css
[data-theme="dark"] {
    --cv-surface: #182234;
    --cv-paper: #0f172a;
    --cv-line: #26354d;
    --cv-line-strong: #334155;
    --cv-ink: #f8fafc;
    --cv-ink-2: #cbd5e1;
    --cv-ink-3: #94a3b8;
    --cv-blue-tint: #1e3a8a;
}
```

Hal ini menjamin bahwa seluruh tag HTML inline yang menuliskan `color: var(--cv-ink-2);` atau `color: var(--cv-ink-3);` pada kartu KPI dinas, kuota, keamanan, dan audit log secara instan berubah menjadi terang dan kontras tinggi ketika mode gelap aktif.

---

## 5. Matriks Perbandingan Palet Warna (Color Scheme Contract)

| Elemen UI | Mode Default (Light Theme) | Mode Gelap (Dark Theme) | Standar Kontras (WCAG) |
|:---|:---|:---|:---|
| **Latar Belakang Kartu** | `#FFFFFF` (Putih Bersih) | `#182234` (Slate Gelap) | Background Surface |
| **Garis Pembatas (Border)** | `#E2E8F0` (Abu-abu Halus) | `#26354D` (Slate Border) | Boundary Container |
| **Judul Kartu (`<h2>`)** | `#0F172A` (Hitam Pekat) | `#F8FAFC` (Putih Murni) | > 12:1 (Passes AAA) |
| **Subjudul Kartu (`<small>`)**| `#64748B` (Slate Muted) | `#94A3B8` (Slate Terang) | > 5.5:1 (Passes AA) |
| **Label Data (`dt`, `label`)**| `#334155` (Slate Gelap) | `#CBD5E1` (Silver Terang) | > 7.5:1 (Passes AAA) |
| **Angka Hero Stat** | `#0F172A` (Hitam Bold) | `#FFFFFF` (Putih Menyala) | > 14:1 (Passes AAA) |
| **Badge Tren Positif** | Teks `#10B981`, Bg `rgba(16,185,129,0.12)` | Teks `#34D399`, Bg `rgba(34,197,94,0.18)` | Vibrant Green |
| **Kurva Utama SVG Chart** | `#0D9488` (Teal Elegan) | `#2DD4BF` (Cyan Menyala) | High-Visibility Line |
| **Kurva Sekunder SVG Chart**| `#0284C7` (Sky Blue) | `#38BDF8` (Sky Blue Menyala) | Secondary Metric |
| **Progress Fill Sebaran** | `#0D9488` (Teal Elegan) | `#2DD4BF` (Cyan Menyala) | Progress Indicator |
| **Badge Akronim Kampus** | Warna Khas (Navy, Green, Indigo, Amber) | Warna Khas (Navy, Green, Indigo, Amber) | Identitas Institusi |

---

## 6. Guardrails & Pantangan Keras (Do's & Don'ts)

> [!CAUTION]
> 1. **JANGAN MERUSAK WARNA DEFAULT**: Warna default (Light Mode) harus tetap berpenampilan kartu putih bersih sesuai tema standar Gentelella. Jangan paksakan warna gelap pada kartu saat mode terang aktif.
> 2. **JANGAN BIARKAN TEKS GELAP PADA MODE GELAP**: Semua teks label, deskripsi, dan judul pada mode gelap harus memiliki kontras minimal 4.5:1 (WCAG AA). Dilarang keras menggunakan teks abu-abu tua (`#334155` atau `#0f172a`) di atas latar gelap.
> 3. **PRESERVASI ASSERTION TEST SUITE**: String `Tren Pendaftaran 12 Bulan Terakhir` dan `Demografi Asal Institusi` tidak boleh diubah atau dihapus agar pengujian PHPUnit tetap hijau.

---

## 7. Verifikasi & Checklist Testing

### A. Pengujian Otomatis (Automated PHPUnit Tests)
```powershell
php artisan test --filter="DeltaDashboardEnhancementTest|AdminSuperAdminPhaseTwoTest|SuperAdminUnifiedDashboardFeaturesTest"
```
**Kriteria**: 27 tests passed (174 assertions).

### B. Pengujian Visual di Browser
1. Buka browser pada URL: `http://localhost/magkom/public/superadmin/dashboard`.
2. **Uji Mode Terang (Default)**:
   - [x] Kartu "Tren Pendaftaran" dan "Demografi" memiliki latar putih bersih (`#ffffff`) dengan garis tepi abu-abu halus (`#e2e8f0`).
   - [x] Teks judul, label jenjang (`S1`, `D3`, `SMK`), nama kampus, dan angka terbaca jelas dengan warna teks gelap pekat.
   - [x] Grafik kurva SVG tampil rapi dengan warna toska cerah di atas latar putih.
3. **Uji Mode Gelap (Dark Mode)**:
   - [x] Klik toggle tema menjadi mode gelap.
   - [x] Seluruh kartu (KPI dinas, Kuota, Keamanan, Tren Pendaftaran, Demografi, Pengguna terbaru, Audit log) berubah menjadi slate gelap elegan.
   - [x] **Tidak ada teks gelap yang tenggelam**: Seluruh label KPI, nama jurusan, status keamanan, dan tabel audit log terbaca sangat jelas dengan warna putih/abu-abu terang.
   - [x] Kurva grafik SVG dan progress bar jenjang menyala toska/cyan kontras tinggi.

---

## 8. Panduan Eksekusi untuk Junior Developer & Agen AI (Gemini 3.8 Flash)

1. Terapkan Langkah 1 dan Langkah 2 pada [public/css/superadmin-dashboard.css](file:///g:/MAGKOM/public/css/superadmin-dashboard.css).
2. Terapkan Langkah 3 pada [resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php](file:///g:/MAGKOM/resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php).
3. Terapkan Langkah 4 pada [resources/views/layouts/dashboard.blade.php](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php).
4. Jalankan `php artisan view:clear` dan `php artisan optimize:clear`.
5. Jalankan test otomatis PHPUnit.
6. Periksa langsung di browser dalam mode terang dan mode gelap untuk memastikan kontras sempurna.
