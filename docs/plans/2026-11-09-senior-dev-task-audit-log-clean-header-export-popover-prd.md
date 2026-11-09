# DOKUMEN TUGAS SENIOR DEVELOPER & PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Refinement Modul SuperAdmin: Standarisasi Teks "Audit Log", Pembersihan Header, dan Perbaikan Dropdown Popover Export CSV

---

**Kepada:** Junior Developer / AI Implementation Agent (Gemini 3.8 Flash, Cursor, Copilot)  
**Dari:** Senior Lead Software Engineer & Frontend Architect MAGKOM  
**Tanggal:** 9 November 2026  
**Status:** Ready for Implementation  
**Prioritas:** P1 — High Usability, Accessibility, & Visual Polish  
**Target Modul:** Modul SuperAdmin — `Audit Log` (`superadmin.audit-log.*`)  

---

## 📌 RINGKASAN EKSEKUTIF (Executive Summary)

Berdasarkan tinjauan visual UI/UX terbaru pada halaman SuperAdmin Audit Log, terdapat 3 temuan kritis yang harus diselesaikan segera:

1. **Redundansi & Cluttering Teks Breadcrumb di Hero Card**:  
   Pada kartu hero utama terdapat teks breadcrumb manual `Portal MagKom / SuperAdmin / Jejak Audit` tepat di atas judul halaman. Bagian ini tidak diperlukan karena navigasi breadcrumb global sudah tersedia di topbar layout. Teks ini harus **dihapus seutuhnya** agar tampilan hero banner lebih bersih dan modern.

2. **Clipped Popover (Bug Visual Kritis pada Ekspor CSV)**:  
   Ketika tombol dropdown panah pada *Unduh Audit CSV* diklik, menu popover opsi format (*Ekspor Log Cepat*) terpotong secara horizontal di bagian bawahnya sehingga pengguna tidak dapat memilih format file (NDJSON, PDF) atau menekan tombol konfirmasi unduhan. Hal ini disebabkan oleh `overflow: hidden;` pada kontainer `.sa-hero-banner`.

3. **Inkonsistensi Nomenklatur ("Jejak Audit & Kepatuhan" $\rightarrow$ "Audit Log")**:  
   Semua teks judul, topbar navbar, header hero card, dan menu navigasi sidebar (slidebar) yang saat ini menggunakan istilah bahasa Indonesia panjang *"Jejak Audit & Kepatuhan"* atau *"Jejak Audit"* harus distandarisasi secara ringkas menjadi **"Audit Log"**.

---

## 🔍 ANALISIS MASALAH & BUKTI VISUAL (Root Cause Analysis)

### 1. Masalah Dropdown Popover Terpotong (Clipped Popover)
* **Bukti Visual:**  
  ![Bug Popover Export Terpotong](file:///g:/MAGKOM/docs/plans/2026-11-09-audit-log-export-csv-clipped.png)
* **Akar Masalah (Root Cause):**  
  File `resources/css/pages/superadmin/superadmin-common.css` menetapkan class `.sa-hero-banner` dengan properti:
  ```css
  .sa-hero-banner {
      ...
      position: relative;
      overflow: hidden; /* <-- BIANG KEROK: Memotong semua child element berposisi absolute yang keluar dari batas kartu */
  }
  ```
  Elemen menu popover `.audit-export-popover` diatur dengan `position: absolute; top: calc(100% + 8px); right: 0;`. Karena posisinya menjulur ke bawah melewati batas bawah kartu hero banner, browser memotong bagian bawah popover tersebut (*clipped*).
* **Solusi Senior:**  
  Ubah perilaku overflow pada kartu hero banner di halaman audit log menjadi `overflow: visible;` dan pastikan `z-index` banner berada di layer aman di atas elemen statistik di bawahnya (`z-index: 30;`).

### 2. Masalah Cluttering Breadcrumb & Nomenklatur Judul
* **Bukti Visual:**  
  ![Review Halaman Full](file:///g:/MAGKOM/docs/plans/2026-11-09-audit-log-full-page-review.jpg)
* **Akar Masalah:**  
  - File `resources/views/superadmin/audit-log/partials/_header.blade.php` baris 16-18 masih memuat inline text `Portal MagKom / SuperAdmin / Jejak Audit`.
  - Teks judul di topbar layout (`@section('topbar_title')`), judul hero (`h1.banner-title`), dan label menu sidebar (`layouts/dashboard.blade.php`) masih menggunakan istilah inkonsisten (*Jejak Audit & Kepatuhan* vs *Jejak Audit*).
* **Solusi Senior:**  
  Hapus baris breadcrumb manual di `_header.blade.php`, dan ubah semua label navigasi, title, serta header menjadi **"Audit Log"**.

---

## 🛠️ SPESIFIKASI TEKNIS & PERUBAHAN KODE (Step-by-Step Implementation)

Junior Developer / AI Agent **WAJIB** mengikuti langkah implementasi secara atomik di bawah ini:

```
┌────────────────────────────────────────────────────────────────────────┐
│ DAFTAR FILE YANG DISENTUH:                                             │
│ 1. resources/views/superadmin/audit-log/partials/_header.blade.php     │
│ 2. resources/views/superadmin/audit-log/index.blade.php                │
│ 3. resources/views/layouts/dashboard.blade.php                         │
│ 4. resources/views/superadmin/audit-log/partials/_styles.blade.php     │
│ 5. resources/css/pages/superadmin/superadmin-common.css                │
│ 6. resources/views/superadmin/audit-log/partials/_kpi_strip.blade.php   │
│ 7. resources/views/superadmin/audit-log/partials/_stream_table.blade.php│
└────────────────────────────────────────────────────────────────────────┘
```

---

### TUGAS 1: Hapus Breadcrumb Manual & Ubah Judul Hero Card
**Lokasi File:** `resources/views/superadmin/audit-log/partials/_header.blade.php`

#### A. Perubahan yang Diperlukan:
1. Hapus elemen `div` yang berisi teks breadcrumb `Portal MagKom / SuperAdmin / Jejak Audit`.
2. Ubah `h1.banner-title` dari `Jejak Audit &amp; Kepatuhan` menjadi `Audit Log`.
3. Sesuaikan teks screen reader `sr-only` dari `Jejak Audit Sistem` menjadi `Audit Log Sistem`.

#### B. Kode Diff (Sebelum vs Sesudah):

**SEBELUM:**
```blade
<div class="sa-hero-banner" style="margin-bottom: 1.5rem;">
    <div class="banner-top-row">
        <div>
            <div style="font-size: 0.8125rem; opacity: 0.8; margin-bottom: 0.25rem;">
                <span>Portal MagKom</span> / <span>SuperAdmin</span> / <span>Jejak Audit</span>
            </div>
            <h1 class="banner-title">Jejak Audit &amp; Kepatuhan</h1>
            <span class="sr-only">Jejak Audit Sistem</span>
            <label for="headerSearchInput" class="sr-only">Aktor / Staf</label>
            <div class="banner-subtitle">
                <span>Cakupan: <strong>15 Kanal Audit Stream</strong></span>
                <span class="bullet-separator">&bull;</span>
                <span>Integritas: <strong>SHA-256 Valid &bull; Retensi 365 Hari</strong></span>
            </div>
        </div>
```

**SESUDAH (Wajib Tepat):**
```blade
<div class="sa-hero-banner" style="margin-bottom: 1.5rem;">
    <div class="banner-top-row">
        <div>
            <h1 class="banner-title">Audit Log</h1>
            <span class="sr-only">Audit Log Sistem</span>
            <label for="headerSearchInput" class="sr-only">Aktor / Staf</label>
            <div class="banner-subtitle">
                <span>Cakupan: <strong>15 Kanal Audit Stream</strong></span>
                <span class="bullet-separator">&bull;</span>
                <span>Integritas: <strong>SHA-256 Valid &bull; Retensi 365 Hari</strong></span>
            </div>
        </div>
```

---

### TUGAS 2: Perbaiki Bug Popover Export Terpotong (Fix Clipping CSS)
**Lokasi File:**
1. `resources/css/pages/superadmin/superadmin-common.css`
2. `resources/views/superadmin/audit-log/partials/_styles.blade.php`

#### A. Perubahan di `resources/css/pages/superadmin/superadmin-common.css`:
Cari definisi `.sa-hero-banner` (baris ~15) dan ubah `overflow: hidden;` menjadi `overflow: visible;`. Tambahkan dukungan agar banner yang memiliki popover/dropdown tidak memotong elemen mengambang.

**SEBELUM:**
```css
.sa-hero-banner {
    background: radial-gradient(80% 120% at 100% 0%, rgba(63, 81, 181, 0.45), transparent 60%),
                linear-gradient(135deg, #01347C 0%, #003566 100%);
    border-radius: 16px;
    padding: 28px 32px;
    color: #ffffff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px -2px rgba(1, 52, 124, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
```

**SESUDAH:**
```css
.sa-hero-banner {
    background: radial-gradient(80% 120% at 100% 0%, rgba(63, 81, 181, 0.45), transparent 60%),
                linear-gradient(135deg, #01347C 0%, #003566 100%);
    border-radius: 16px;
    padding: 28px 32px;
    color: #ffffff;
    position: relative;
    overflow: visible;
    box-shadow: 0 4px 20px -2px rgba(1, 52, 124, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}
```

#### B. Perubahan di `resources/views/superadmin/audit-log/partials/_styles.blade.php`:
Tambahkan rule pengaman berlapis (*fail-safe*) untuk `.audit-workspace-root .sa-hero-banner` dan pastikan `.audit-export-popover` memiliki `z-index` yang kuat.

Cari bagian `/* 9. EXPORT POPOVER (Pola A Node 68749aa8) */` (sekitar baris 1860) atau pada bagian paling atas `_styles.blade.php`, tambahkan rule:

```css
/* Pastikan Hero Banner Audit Log tidak memotong menu floating popover */
.audit-workspace-root .sa-hero-banner {
    overflow: visible !important;
    position: relative;
    z-index: 40;
}

.audit-export-popover {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 320px;
    background: var(--sa-audit-surface);
    border: 1px solid var(--sa-audit-border);
    border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    padding: 1rem;
    z-index: 1050;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
```

---

### TUGAS 3: Standarisasi Teks "Audit Log" di Sidebar, Topbar, dan View Page
**Lokasi File:**
1. `resources/views/layouts/dashboard.blade.php`
2. `resources/views/superadmin/audit-log/index.blade.php`
3. `resources/views/superadmin/audit-log/partials/_kpi_strip.blade.php`
4. `resources/views/superadmin/audit-log/partials/_stream_table.blade.php`

#### A. File `resources/views/layouts/dashboard.blade.php` (Menu Navigasi Sidebar/Slidebar):
Cari navigasi menu Jejak Audit di sidebar (sekitar baris 3417):

**SEBELUM:**
```blade
    <a href="{{ route('superadmin.audit-log.index') }}" class="nav-item-link {{ request()->routeIs('superadmin.audit-log.*') ? 'active' : '' }}">
        <span class="nav-item-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
        </span>
        <span>{{ __('Jejak Audit') }}</span>
        <span class="sr-only">Audit Log (15 Kanal) Jejak Audit Log Multi Kanal Log Audit</span>
    </a>
```

**SESUDAH:**
```blade
    <a href="{{ route('superadmin.audit-log.index') }}" class="nav-item-link {{ request()->routeIs('superadmin.audit-log.*') ? 'active' : '' }}">
        <span class="nav-item-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
        </span>
        <span>{{ __('Audit Log') }}</span>
        <span class="sr-only">Audit Log (15 Kanal) Multi Kanal Log Audit</span>
    </a>
```

#### B. File `resources/views/superadmin/audit-log/index.blade.php`:
Ubah baris 3-14 dan baris 36:

**SEBELUM:**
```blade
@section('title', 'Jejak Audit & Kepatuhan - SuperAdmin MagKom')
@section('topbar_title', 'Jejak Audit & Kepatuhan')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Jejak Audit</span>
...
    <main class="audit-stream-col" aria-label="Aliran Jejak Audit">
```

**SESUDAH:**
```blade
@section('title', 'Audit Log - SuperAdmin MagKom')
@section('topbar_title', 'Audit Log')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Audit Log</span>
    @if(isset($activeChannel) && $activeChannel !== 'all-actions-stream')
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">{{ $activeChannelMeta['label'] ?? $activeChannel }}</span>
    @endif
@endsection
...
    <main class="audit-stream-col" aria-label="Aliran Audit Log">
```

#### C. File `resources/views/superadmin/audit-log/partials/_kpi_strip.blade.php`:
Ubah baris 14:
```blade
{{-- SEBELUM: --}}
<section class="tiles-grid" style="margin-bottom: 1.5rem;" aria-label="Ringkasan Jejak Audit">

{{-- SESUDAH: --}}
<section class="tiles-grid" style="margin-bottom: 1.5rem;" aria-label="Ringkasan Audit Log">
```

#### D. File `resources/views/superadmin/audit-log/partials/_stream_table.blade.php`:
Ubah baris 172:
```blade
{{-- SEBELUM: --}}
<table class="audit-table" aria-label="Tabel Jejak Audit">

{{-- SESUDAH: --}}
<table class="audit-table" aria-label="Tabel Audit Log">
```

---

## 🛡️ QUALITY GATE & VERIFIKASI (Testing & Verification)

Setelah melakukan perubahan, Junior Developer / AI Agent **wajib** menjalankan langkah verifikasi berikut:

### 1. Kompilasi Aset Frontend (Vite)
Jalankan perintah build untuk memastikan styling termutakhir dikompilasi tanpa galat:
```bash
npm run build
```
*Kriteria Lulus:* Selesai dengan exit code 0 tanpa syntax error pada CSS.

### 2. Jalankan Automated Regression Test
Jalankan test suite terkait SuperAdmin:
```bash
php artisan test --filter=RolePermissionTest
```
*Kriteria Lulus:* Semua unit & feature test hijau (PASS).

### 3. Verifikasi Visual di Browser (Checklist UI)
Buka browser pada halaman SuperAdmin Audit Log: `http://localhost:8000/superadmin/audit-log` atau `http://magkom.test:8080/superadmin/audit-log`
- [ ] Teks breadcrumb `Portal MagKom / SuperAdmin / Jejak Audit` **sudah hilang sepenuhnya**.
- [ ] Judul banner kartu hero menampilkan **Audit Log** dengan jelas dan proporsional.
- [ ] Topbar title di navbar atas bertuliskan **Audit Log**.
- [ ] Menu navigasi sidebar bertuliskan **Audit Log**.
- [ ] Klik tombol panah caret pada tombol **Unduh Audit CSV**:
  - Popover dropdown terbuka secara utuh.
  - Kartu opsi radio (CSV, NDJSON, PDF), toggle HMAC & Diff, serta tombol *Unduh CSV* terlihat lengkap tanpa terpotong batas bawah kartu hero.

---

## 📋 CHECKLIST PENYELESAIAN TUGAS (Acceptance Criteria)

| No | Kriteria Penerimaan | Status |
|:--:|:--------------------|:------:|
| 1  | Teks `Portal MagKom / SuperAdmin / Jejak Audit` dihapus dari `_header.blade.php`. | [ ] |
| 2  | Judul hero card diubah dari `Jejak Audit & Kepatuhan` menjadi `Audit Log`. | [ ] |
| 3  | Topbar title, Page title, dan breadcrumb di `index.blade.php` diubah menjadi `Audit Log`. | [ ] |
| 4  | Menu sidebar pada `dashboard.blade.php` diubah menjadi `Audit Log`. | [ ] |
| 5  | Bug clipping popover export CSV diperbaiki via `overflow: visible` dan `z-index`. | [ ] |
| 6  | `npm run build` berhasil dijalankan tanpa galat. | [ ] |
| 7  | Tidak ada regresi pada fungsionalitas unduh CSV/NDJSON/PDF. | [ ] |

---
*Dokumen ini disusun oleh Senior Lead Software Engineer MAGKOM sebagai instruksi resmi implementasi.*

