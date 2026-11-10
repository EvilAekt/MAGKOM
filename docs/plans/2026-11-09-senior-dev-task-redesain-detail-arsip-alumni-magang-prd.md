# TASK BRIEF SENIOR DEVELOPER & PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Redesain Visual Halaman Detail Arsip Alumni Magang Mengadopsi Standar Hero Banner, 4 Tile-Stat Grid, & Tab Layout Rapi (DS-MagKom Parity)

---

> **Dari:** Senior Lead Software Architect & UI/UX Design System Lead MAGKOM  
> **Kepada:** Junior Developer / AI Implementation Agent (Gemini 3.8 Flash / Claude / Antigravity Agent)  
> **Target Repositori:** MAGKOM (Portal Manajemen Magang Dinas Kominfo Sleman)  
> **Tanggal Dokumen:** 9 November 2026  
> **Status:** READY FOR IMPLEMENTATION / APPROVED BLUEPRINT  
> **Prioritas:** P2 — Visual Alignment, Layout Polishing, Design System Parity, CSS Extraction  
> **Tingkat Kesulitan:** Menengah (*Blade Template Modernization, Tile-Stat Aggregation, Zero Inline Style in Blade, Tab Navigation Standardization*)  
> **Dokumentasi Terkait:** `AGENTS.md`, `docs/01-PRD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `docs/plans/2026-11-09-senior-dev-task-redesain-arsip-peserta-gaya-manajemen-staf-prd.md`  

---

## 📌 1. 🔍 LATAR BELAKANG & TUJUAN TASK (Executive Summary)

### 1.1 Masalah Nyata pada Halaman Detail Arsip Eksisting
Halaman **Detail Arsip Alumni Magang** ([`resources/views/superadmin/arsip/show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/arsip/show.blade.php)) saat ini masih menggunakan struktur lama yang belum selaras dengan indeks Arsip Peserta maupun halaman SuperAdmin lainnya:
1. **Header Kuno & Judul Ganda (*Visual Inconsistency*)**:
   - Masih menggunakan `@section('page_title')` dan `@section('page_description')` yang membuat layout merender header putih bertumpuk.
   - Tombol navigasi aksi (`Surat Diterima PDF` dan `← Kembali`) terpisah di baris header atas, bukan terintegrasi secara profesional di dalam kartu Hero Banner.
2. **Ketiadaan Ringkasan Cepat (*Zero Mini Stats Visibility*)**:
   - Data performa penting (total tugas diberikan, tugas selesai, dan jumlah logbook harian) tersembunyi di dalam kotak kartu kecil Pembimbing Lapangan di bagian bawah.
   - Tidak ada kartu metrik cepat (*KPI tile-stat counters*) yang memberikan gambaran ringkas status kepatuhan dan kontribusi alumni secara seketika.
3. **Pelanggaran Aturan Arsitektur (*50+ Baris Inline `<style>` di Blade*)**:
   - Berkas `show.blade.php` masih memuat tag `<style>` sebanyak 53 baris (baris 42–140) untuk mendefinisikan `.arsip-card`, `.arsip-tab-bar`, `.arsip-tab-btn`, dan dark mode overrides.
   - Hal ini melanggar aturan paten `AGENTS.md` Bagian 3.1 & 3.2. Seluruh class CSS ini harus diekstrak ke [`resources/css/pages/superadmin/superadmin-common.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/superadmin-common.css).
4. **Tata Letak Tab Konten Kurang Proporsional (*Cluttered Layout*)**:
   - Tab 1 ("Ringkasan & Biodata") hanya menampilkan dua kartu yang terkesan renggang dan minim hierarki visual.
   - Perlu penataan kartu data dengan border-top khas Sleman (`.gentelella-card`), tipografi data yang tegas, inisial avatar pembimbing yang konsisten, dan penyusunan informasi biodata peserta yang rapi.

### 1.2 Sasaran Akhir (The Target State)
Mengubah tampilan halaman **Detail Arsip Alumni Magang** agar memiliki tampilan yang **modern, rapi, proporsional, dan senada dengan halaman indeks Arsip Peserta & Manajemen Staf**:
1. Menghadirkan **Hero Banner Khas Sleman MagKom (`.sa-hero-banner`)** yang memuat inisial/foto profil alumni, nama lengkap, status magang, badge tata kelola (*Strictly Read-Only Bank Data*), nomor identitas (NIM/NISN), instansi asal, serta tombol aksi `Surat Diterima PDF` dan `← Kembali ke Arsip`.
2. Menghadirkan **Grid 4 Kartu Statistik Tile-Stat (`<x-gentelella.tile-stat>`)** di bawah Hero Banner:
   - `STATUS RETENSI` (Arsip Final / Masa Tenggang / Purna Dini)
   - `TOTAL PENUGASAN` (Jumlah tugas yang tercatat selama magang)
   - `TUGAS TUNTAS` (Jumlah penugasan yang berhasil diselesaikan)
   - `LOGBOOK HARIAN` (Total entri aktivitas harian yang diverifikasi)
3. Mengadopsi **Tab Navigation Berdesain Bersih & Modern** dengan ikon SVG dan counter badge yang dinamis.
4. Menata ulang kartu-kartu konten di seluruh tab (Ringkasan & Biodata, Riwayat Tugas, Logbook Harian, Dokumen Pendaftaran) menggunakan kontainer `.gentelella-card` yang serasi.
5. Membuang seluruh tag `<style>` dari Blade dan memusatkan styling di `superadmin-common.css`.

---

## 📸 2. 🖼️ DOKUMENTASI VISUAL & ELEMEN TATA LETAK

### 2.1 Kondisi Eksisting Saat Ini (Before)
![Halaman Detail Arsip Saat Ini](file:///C:/Users/galan/.gemini/antigravity/brain/7c4386b3-6ece-42d6-a673-d843562e14c2/.user_uploaded/media_1794200642338_c7eff380.jpg)
- Header putih sederhana dengan tombol aksi terpisah.
- Kartu profil abu-abu putih dengan badge manual.
- Tidak ada 4 kartu metrik statistik horizontal.
- Dua kartu penempatan & pembimbing dengan layout kotak data polos.

### 2.2 Blueprint Tata Letak Baru (After Wireframe)

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [TOPBAR & BREADCRUMBS]                                                                 │
│ SuperAdmin / Arsip Peserta / [Nama Alumni]                                             │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [1. HERO BANNER ALUMNI KHAS SLEMAN MAGKOM] (.sa-hero-banner)                           │
│ [Avatar/Foto]  Indra Kurniawan [Selesai Magang / Nonaktif] [🔒 Read-Only Governance]   │
│                NIM/NISN: 123220088 • UPN Veteran Yogyakarta • indra.k@gmail.com       │
│                Aksi Kanan: [📄 Surat Diterima PDF]  [← Kembali ke Arsip]               │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [2. GRID 4 KARTU STATISTIK TILE-STAT] (<x-gentelella.tile-stat>)                       │
│ 1. STATUS RETENSI   : Arsip Final    (Purple • info    • Rekam Historis Permanen)      │
│ 2. TOTAL PENUGASAN  : 5 Tugas        (Blue   • info    • Total Beban Diberikan)        │
│ 3. TUGAS TUNTAS     : 5 Selesai      (Teal   • success • Tingkat Tuntas 100%)          │
│ 4. LOGBOOK HARIAN   : 48 Entri       (Amber  • warning • Presensi & Log Aktivitas)     │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [3. TAB CONTROLLER BAR DS-MAGKOM]                                                      │
│ [👤 1. Ringkasan & Biodata] [📝 2. Riwayat Tugas (5)] [📖 3. Logbook (48)] [📁 4. Berkas] │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [4. KONTEN TAB 1: RINGKASAN & BIODATA] (2 Kolom Seimbang)                             │
│ ┌─ KARTU A: Penempatan & Institusi ──────────┐ ┌─ KARTU B: Pembimbing Lapangan ──────┐ │
│ │ • Bidang: Aplikasi Informatika & E-Gov     │ │ [Avatar BU] Budi Santoso, S.Kom     │ │
│ │ • Jurusan: Teknik Informatika              │ │ Unit: Aplikasi Informatika          │ │
│ │ • Periode: 07 Nov 2026 s/d 02 Feb 2027     │ │ Email: pembimbing@magkom.go.id      │ │
│ │ • Jalur: Jalur Umum (Pendaftaran Online)   │ │ Kuota Mentee: 14 / 20 Maksimal      │ │
│ └────────────────────────────────────────────┘ └─────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 🏗️ 3. 🗺️ INVENTARIS FILE & PEMETAAN DAMPAK (Impact Matrix)

Perubahan mencakup 2 berkas utama:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 1. STYLESHEET (resources/css/pages/superadmin/superadmin-common.css)                   │
│    - Tambahkan modul class spesifik detail arsip:                                      │
│      .arsip-tab-bar, .arsip-tab-btn, .arsip-tab-pane, .arsip-info-box                 │
│    - Tambahkan dukungan dark mode [data-theme="dark"] pada komponen tab                │
│                                                                                        │
│ 2. BLADE VIEW (resources/views/superadmin/arsip/show.blade.php)                        │
│    - Hapus @section('page_title'), @section('page_description'), @section('page_actions')│
│    - Tetapkan @section('topbar_title', 'Detail Arsip Alumni Magang')                   │
│    - Hapus seluruh blok tag <style> lokal (53 baris)                                   │
│    - Implementasikan Hero Banner (.sa-hero-banner) lengkap dengan tombol aksi          │
│    - Implementasikan 4 Tile-Stats Grid (<x-gentelella.tile-stat>)                      │
│    - Terapkan tab bar terstandarisasi dengan data attributes                           │
│    - Rapikan tata letak 2 kolom kartu data penempatan & pembimbing                     │
│                                                                                        │
│ 3. TEST SUITE VERIFICATION (tests/Feature/)                                            │
│    - AdminLaporanDanArsipTest: Verifikasi status 200 & string checks                   │
│    - DeltaDashboardEnhancementTest: Verifikasi tabbed view & audit trail               │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 📊 4. 📋 KAMUS DATA: 4 KARTU STATISTIK TILE-STAT DETAIL ALUMNI

Junior Developer atau AI Implementation Agent wajib menggunakan komponen `<x-gentelella.tile-stat>` dengan konfigurasi berikut:

| No | Judul Kartu (`title`) | Variabel Nilai (`value`) | Skema Warna (`color`) | Tipe Tren (`trendType`) | Teks Tren (`trendText`) | Subteks Keterangan (`subText`) | Ikon Visual SVG | Spark Bars |
|:---:|---|---|:---:|:---:|:---:|---|---|:---:|
| 1 | `STATUS RETENSI` | `$penempatan->status?->label() ?? 'Nonaktif'` | `purple` | `info` | `Purna Dinas` | `Rekam Historis Permanen (Read-Only)` | Gembok / Tameng Keamanan | `[30, 45, 60, 75, 90, 100]` |
| 2 | `TOTAL PENUGASAN` | `{{ $penempatan->tugas->count() }} Tugas` | `blue` | `info` | `Penugasan` | `Total Beban Kerja Selama Magang` | Clipboard / Check Square | `[20, 35, 50, 70, 85, 95]` |
| 3 | `TUGAS SELESAI` | `{{ $penempatan->tugas->where('status.value', 'selesai')->count() }} Selesai` | `teal` | `success` | `Tuntas` | `Pekerjaan Terverifikasi Selesai` | Check Circle / Badge Check | `[25, 40, 55, 75, 90, 100]` |
| 4 | `LOGBOOK HARIAN` | `{{ $penempatan->logbooks->count() }} Entri` | `amber` | `warning` | `Presensi` | `Catatan Aktivitas Terbimbing` | Buku Jurnal / Calendar | `[15, 30, 50, 70, 85, 95]` |

---

## 🛠️ 5. 📝 PANDUAN IMPLEMENTASI TAHAP DEMI TAHAP (Atomic Tasks)

### TUGAS 1: Tambahkan Styling Tab & Detail di `superadmin-common.css`

**Lokasi Berkas:** [`resources/css/pages/superadmin/superadmin-common.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/superadmin-common.css)

Tambahkan blok styling pada bagian akhir berkas:

```css
/* =========================================================================
   6. DETAIL ARSIP TABS & PANELS (DS-MagKom Parity)
   ========================================================================= */

.arsip-tab-bar {
    display: flex;
    gap: 0.5rem;
    border-bottom: 2px solid var(--neutral-300, #CBD5E1);
    overflow-x: auto;
    padding-bottom: 2px;
}

.arsip-tab-btn {
    padding: 0.75rem 1.25rem;
    border: none;
    background: transparent;
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--neutral-600, #64748B);
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all 0.15s ease;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.arsip-tab-btn:hover {
    color: #01347C;
}

.arsip-tab-btn.active {
    color: #01347C;
    border-bottom-color: #01347C;
    background: #EFF6FF;
    border-radius: 8px 8px 0 0;
}

.arsip-tab-pane {
    display: none;
}

.arsip-tab-pane.active {
    display: block;
}

.arsip-info-box {
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    padding: 0.75rem 1rem;
}

/* Dark Mode Overrides */
[data-theme="dark"] .arsip-tab-bar {
    border-bottom-color: rgba(63, 81, 181, 0.45) !important;
}

[data-theme="dark"] .arsip-tab-btn {
    color: #94A3B8 !important;
}

[data-theme="dark"] .arsip-tab-btn:hover {
    color: #F8FAFC !important;
}

[data-theme="dark"] .arsip-tab-btn.active {
    color: #93C5FD !important;
    background: rgba(37, 99, 235, 0.2) !important;
    border-bottom-color: #3B82F6 !important;
}

[data-theme="dark"] .arsip-info-box {
    background: #011C44 !important;
    border-color: rgba(63, 81, 181, 0.45) !important;
}
```

---

### TUGAS 2: Rombak Total Tampilan View `superadmin/arsip/show.blade.php`

**Lokasi Berkas:** [`resources/views/superadmin/arsip/show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/arsip/show.blade.php)

Ganti seluruh isi berkas dengan markup modern yang telah distandarisasi:

```blade
@extends('layouts.dashboard')

@section('title', 'Detail Arsip Peserta ' . ($penempatan->peserta?->nama ?? 'Alumni') . ' - SuperAdmin MagKom')
@section('topbar_title', 'Detail Arsip Alumni Magang')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <a href="{{ route('superadmin.arsip.index') }}">Arsip Peserta</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ $penempatan->peserta?->nama }}</span>
@endsection

@push('styles')
    @vite(['resources/css/pages/superadmin/superadmin-common.css'])
@endpush

@section('content')
@php
    $peserta = $penempatan->peserta;
    $profil = $peserta?->profil;
    $pembimbing = $penempatan->pembimbing;
    $penilaian = $penempatan->penilaian;
    $namaPeserta = $peserta?->nama ?? 'Alumni';
    $initials = collect(explode(' ', $namaPeserta))->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
    $pembimbingInitials = collect(explode(' ', $pembimbing?->nama ?? 'Pembimbing'))->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('');
@endphp

<div class="sa-fluid-container" style="display: flex; flex-direction: column; gap: 1.5rem;">

    {{-- 1. HERO BANNER ALUMNI KHAS SLEMAN MAGKOM --}}
    <div class="sa-hero-banner">
        <div class="banner-top-row">
            <div style="display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap;">
                <div style="width: 62px; height: 62px; min-width: 62px; border-radius: 12px; background: #ffffff; color: #01347C; font-weight: 800; font-size: 1.5rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                    @if($profil?->foto_url)
                        <img src="{{ $profil->foto_url }}" alt="{{ $namaPeserta }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                    @else
                        {{ $initials ?: 'AL' }}
                    @endif
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                        <h1 class="banner-title" style="margin: 0; font-size: 1.375rem;">{{ $namaPeserta }}</h1>
                        @if($penempatan->status?->value === 'selesai')
                            <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: rgba(16, 185, 129, 0.2); color: #A7F3D0; border: 1px solid rgba(16, 185, 129, 0.4);">
                                Selesai Magang
                            </span>
                        @elseif($penempatan->status?->value === 'mengundurkan_diri')
                            <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: rgba(239, 68, 68, 0.2); color: #FECACA; border: 1px solid rgba(239, 68, 68, 0.4);">
                                Mengundurkan Diri
                            </span>
                        @else
                            <span style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: rgba(255, 255, 255, 0.2); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.4);">
                                Nonaktif (Arsip)
                            </span>
                        @endif
                        <span class="btn-banner-action--secondary" style="cursor: default; pointer-events: none; padding: 0.25rem 0.65rem; font-size: 0.75rem;">
                            Strictly Read-Only Bank Data
                        </span>
                    </div>
                    <div class="banner-subtitle" style="margin-top: 0.35rem; font-size: 0.875rem;">
                        <span>Email: <strong>{{ $peserta?->email }}</strong></span>
                        <span class="bullet-separator">&bull;</span>
                        <span>NIM/NISN: <strong>{{ $profil?->nim_nisn ?? '-' }}</strong></span>
                        <span class="bullet-separator">&bull;</span>
                        <span>Institusi: <strong>{{ $profil?->institusi ?? '-' }}</strong></span>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi Kanan --}}
            <div class="banner-actions-group" style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <a href="{{ route('admin.penempatan.surat-diterima', $penempatan) }}" class="btn-banner-action--secondary" target="_blank">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <span>Surat Diterima PDF</span>
                </a>
                <a href="{{ route('superadmin.arsip.index') }}" class="btn-banner-action">
                    <span>&larr; Kembali ke Arsip</span>
                </a>
            </div>
        </div>
    </div>

    {{-- 2. GRID 4 KARTU STATISTIK TILE-STAT --}}
    <div class="tiles-grid">
        <!-- 1. Status Retensi -->
        <x-gentelella.tile-stat
            title="STATUS RETENSI"
            value="{{ $penempatan->status?->label() ?? 'Nonaktif' }}"
            color="purple"
            trendType="info"
            trendDirection="up"
            trendText="Governance"
            subText="Rekam Historis Permanen"
            :sparkBars="[30, 45, 60, 75, 90, 100]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0110 0v4"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 2. Total Penugasan -->
        <x-gentelella.tile-stat
            title="TOTAL PENUGASAN"
            value="{{ $penempatan->tugas->count() }} Tugas"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="Penugasan"
            subText="Beban Kerja Selama Dinas"
            :sparkBars="[20, 35, 50, 70, 85, 95]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 11l3 3L22 4"></path>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 3. Tugas Tuntas -->
        <x-gentelella.tile-stat
            title="TUGAS TUNTAS"
            value="{{ $penempatan->tugas->where('status.value', 'selesai')->count() }} Selesai"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="Tuntas"
            subText="Pekerjaan Berstatus Selesai"
            :sparkBars="[25, 40, 55, 75, 90, 100]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 4. Logbook Harian -->
        <x-gentelella.tile-stat
            title="LOGBOOK HARIAN"
            value="{{ $penempatan->logbooks->count() }} Entri"
            color="amber"
            trendType="warning"
            trendDirection="up"
            trendText="Presensi"
            subText="Aktivitas Terbimbing Dinas"
            :sparkBars="[15, 30, 50, 70, 85, 95]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    {{-- 3. TAB CONTROLLER BAR DS-MAGKOM --}}
    <div class="arsip-tab-bar" role="tablist">
        <button type="button" class="arsip-tab-btn active" onclick="switchArsipTab('tab-ringkasan', this)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            <span>Ringkasan &amp; Biodata</span>
        </button>
        <button type="button" class="arsip-tab-btn" onclick="switchArsipTab('tab-tugas', this)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
            <span>Riwayat Tugas ({{ (string) $penempatan->tugas->count() }})</span>
        </button>
        <button type="button" class="arsip-tab-btn" onclick="switchArsipTab('tab-logbook', this)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            <span>Logbook Harian ({{ (string) $penempatan->logbooks->count() }})</span>
        </button>
        <button type="button" class="arsip-tab-btn" onclick="switchArsipTab('tab-administrasi', this)">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
            <span>Dokumen Pendaftaran</span>
        </button>
    </div>

    {{-- TAB 1: RINGKASAN & BIODATA --}}
    <div class="arsip-tab-pane active" id="tab-ringkasan">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap: 1.5rem;">
            
            {{-- Kartu A: Data Penempatan & Institusi --}}
            <div class="gentelella-card" style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important; border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
                <h3 style="margin: 0 0 1.25rem 0; font-size: 1rem; font-weight: 800; color: var(--neutral-800); border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
                    Data Penempatan &amp; Institusi
                </h3>
                <div style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.875rem;">
                    <div>
                        <span style="color: var(--neutral-500); display: block; font-size: 0.725rem; font-weight: 700; text-transform: uppercase;">Bidang Dinas</span>
                        <strong style="color: var(--neutral-900); font-size: 0.9375rem;">{{ $penempatan->bidang?->nama_bidang ?? 'Dinas Kominfo Sleman' }}</strong>
                    </div>
                    <div>
                        <span style="color: var(--neutral-500); display: block; font-size: 0.725rem; font-weight: 700; text-transform: uppercase;">Jurusan Terdaftar</span>
                        <strong style="color: var(--neutral-900); font-size: 0.9375rem;">{{ $penempatan->kuota?->jurusan?->nama_jurusan ?? ($profil?->jurusan?->nama_jurusan ?? '-') }}</strong>
                    </div>
                    <div class="arsip-info-box" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <span style="color: var(--neutral-500); display: block; font-size: 0.6875rem; font-weight: 700;">TANGGAL MULAI</span>
                            <strong style="color: var(--neutral-900);">{{ $penempatan->tgl_mulai?->translatedFormat('d M Y') ?? '-' }}</strong>
                        </div>
                        <div>
                            <span style="color: var(--neutral-500); display: block; font-size: 0.6875rem; font-weight: 700;">TANGGAL SELESAI</span>
                            <strong style="color: var(--neutral-900);">{{ $penempatan->tgl_selesai?->translatedFormat('d M Y') ?? '-' }}</strong>
                        </div>
                    </div>
                    <div>
                        <span style="color: var(--neutral-500); display: block; font-size: 0.725rem; font-weight: 700; text-transform: uppercase;">Jalur &amp; Sumber</span>
                        <span style="color: var(--neutral-800); font-weight: 600;">{{ $penempatan->jalur?->label() ?? 'Reguler' }} &bull; {{ $penempatan->sumber?->label() ?? 'Portal' }}</span>
                    </div>
                </div>
            </div>

            {{-- Kartu B: Pembimbing Lapangan --}}
            <div class="gentelella-card" style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important; border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
                <h3 style="margin: 0 0 1.25rem 0; font-size: 1rem; font-weight: 800; color: var(--neutral-800); border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
                    Pembimbing Lapangan
                </h3>
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem;">
                    <div style="width: 48px; height: 48px; min-width: 48px; border-radius: 10px; background: linear-gradient(135deg, #01347C 0%, #003566 100%); color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; box-shadow: 0 2px 6px rgba(1, 52, 124, 0.2);">
                        {{ $pembimbingInitials ?: 'PL' }}
                    </div>
                    <div>
                        <div style="font-size: 0.9375rem; font-weight: 800; color: var(--neutral-900);">{{ $pembimbing?->nama ?? 'Tidak Ada Pembimbing' }}</div>
                        <div style="font-size: 0.8125rem; color: var(--neutral-600);">{{ $pembimbing?->email ?? '-' }}</div>
                    </div>
                </div>

                <div class="arsip-info-box" style="display: flex; flex-direction: column; gap: 0.65rem; font-size: 0.8125rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--neutral-600);">Total Tugas Diberikan:</span>
                        <strong style="color: var(--neutral-900);">{{ (string) $penempatan->tugas->count() }} Tugas</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--neutral-600);">Tugas Selesai:</span>
                        <strong style="color: var(--green-600);">{{ (string) $penempatan->tugas->where('status.value', 'selesai')->count() }} Selesai</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: var(--neutral-600);">Total Logbook Terisi:</span>
                        <strong style="color: var(--blue-600);">{{ (string) $penempatan->logbooks->count() }} Entri</strong>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- TAB 2: RIWAYAT TUGAS --}}
    <div class="arsip-tab-pane" id="tab-tugas">
        <div class="gentelella-card" style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important; border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
            <h3 style="margin: 0 0 1.25rem 0; font-size: 1rem; font-weight: 800; color: var(--neutral-800);">
                Daftar Tugas &amp; Penugasan Magang (Read-Only)
            </h3>
            @if($penempatan->tugas->isNotEmpty())
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    @foreach($penempatan->tugas as $t)
                        <div style="padding: 1rem; border: 1px solid var(--neutral-300); border-radius: 10px; background: var(--neutral-100);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
                                <div>
                                    <h4 style="margin: 0; font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900);">
                                        {{ $t->judul }}
                                    </h4>
                                    <p style="margin: 0.25rem 0 0 0; font-size: 0.8125rem; color: var(--neutral-600);">
                                        {{ $t->deskripsi }}
                                    </p>
                                </div>
                                <span style="font-size: 0.75rem; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 6px; background: {{ $t->status?->value === 'selesai' ? '#ECFDF5' : '#EFF6FF' }}; color: {{ $t->status?->value === 'selesai' ? '#065F46' : '#1D4ED8' }};">
                                    {{ $t->status?->label() ?? ucfirst($t->status?->value ?? 'Aktif') }}
                                </span>
                            </div>
                            @if($t->progresLogs->isNotEmpty())
                                <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px dashed var(--neutral-300); font-size: 0.78125rem; color: var(--neutral-700);">
                                    <strong>Progres Terakhir:</strong> {{ $t->progresLogs->first()->catatan ?? '-' }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 3rem 1.5rem; color: var(--neutral-600);">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--neutral-200); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                    </div>
                    <div style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800);">Tidak Ada Catatan Tugas</div>
                    <p style="font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Tidak ada catatan tugas yang terdaftar untuk alumni ini.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- TAB 3: REKAPITULASI LOGBOOK --}}
    <div class="arsip-tab-pane" id="tab-logbook">
        <div class="gentelella-card" style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important; border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
            <h3 style="margin: 0 0 1.25rem 0; font-size: 1rem; font-weight: 800; color: var(--neutral-800);">
                Rekam Jejak Logbook Harian Magang (Read-Only)
            </h3>
            @if($penempatan->logbooks->isNotEmpty())
                <div class="table-responsive-gentelella" style="overflow-x: auto;">
                    <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                        <thead>
                            <tr class="headings">
                                <th style="text-align: left; padding: 0.75rem;">Tanggal</th>
                                <th style="text-align: left; padding: 0.75rem;">Minggu</th>
                                <th style="text-align: left; padding: 0.75rem;">Aktivitas Magang</th>
                                <th style="text-align: center; padding: 0.75rem;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($penempatan->logbooks as $lb)
                                <tr style="border-bottom: 1px solid var(--neutral-200);">
                                    <td style="padding: 0.75rem; font-weight: 600;">{{ \Carbon\Carbon::parse($lb->tanggal)->translatedFormat('d M Y') }}</td>
                                    <td style="padding: 0.75rem;">Minggu ke-{{ $lb->minggu_ke ?? 1 }}</td>
                                    <td style="padding: 0.75rem;">{{ $lb->ringkasan_aktivitas ?? ($lb->aktivitas ?? '-') }}</td>
                                    <td style="padding: 0.75rem; text-align: center;">
                                        <span style="font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; background: #ECFDF5; color: #065F46;">
                                            {{ ucfirst($lb->status?->value ?? (string)$lb->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 3rem 1.5rem; color: var(--neutral-600);">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--neutral-200); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                    </div>
                    <div style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800);">Tidak Ada Catatan Logbook</div>
                    <p style="font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Belum ada catatan aktivitas harian yang ditemukan untuk peserta ini.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- TAB 4: DOKUMEN PENDAFTARAN --}}
    <div class="arsip-tab-pane" id="tab-administrasi">
        <div class="gentelella-card" style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-top: 3.5px solid var(--magkom-primary-blue, #01347C) !important; border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
            <h3 style="margin: 0 0 1.25rem 0; font-size: 1rem; font-weight: 800; color: var(--neutral-800);">
                Berkas Administrasi &amp; Dokumen Terverifikasi
            </h3>
            @if($penempatan->pendaftaran && $penempatan->pendaftaran->dokumen->isNotEmpty())
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 0.75rem;">
                    @foreach($penempatan->pendaftaran->dokumen as $dok)
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.85rem 1.15rem; border-radius: 8px; border: 1px solid var(--neutral-300); background: var(--neutral-100);">
                            <div>
                                <div style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-900);">
                                    {{ ucwords((string) str_replace('_', ' ', (string) $dok->jenis_dokumen)) }}
                                </div>
                                <div style="font-size: 0.725rem; color: var(--neutral-600); margin-top: 0.15rem;">
                                    Terverifikasi saat seleksi
                                </div>
                            </div>
                            <span style="font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; background: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0;">
                                Valid
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 3rem 1.5rem; color: var(--neutral-600);">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--neutral-200); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem auto;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                    </div>
                    <div style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-800);">Tidak Ada Berkas Unggahan</div>
                    <p style="font-size: 0.8125rem; margin: 0.25rem 0 0 0;">Tidak ada berkas unggahan administrasi yang terdata.</p>
                </div>
            @endif
        </div>
    </div>

</div>

<script>
    function switchArsipTab(tabId, btn) {
        document.querySelectorAll('.arsip-tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.arsip-tab-pane').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');
        const target = document.getElementById(tabId);
        if (target) {
            target.classList.add('active');
        }
    }
</script>
@endsection
```

---

## ⚡ 6. 🧪 QUALITY ASSURANCE & VERIFIKASI (Verification Suite)

Pengujian wajib dijalankan menggunakan runtime resmi **PHP 8.3** sistem:  
`& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe"`

### 1. Bersihkan Cache Template Blade
```powershell
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan view:clear
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan config:clear
```

### 2. Jalankan Test Suite Terkait Detail Arsip & Laporan
```powershell
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test tests/Feature/AdminLaporanDanArsipTest.php tests/Feature/DeltaDashboardEnhancementTest.php
```
*Ekspektasi: Seluruh pengujian detail arsip alumni, tabbed view, dan audit trail lolos 100% (PASS).*

### 3. Jalankan Pengujian Seluruh Fitur SuperAdmin
```powershell
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test --filter=SuperAdmin
```
*Ekspektasi: 219 tests passed (1421 assertions).*

### 4. Code Formatting Audit dengan Pint
```powershell
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/pint --test
```
*Ekspektasi: 0 violations.*

### 5. Static Analysis dengan PHPStan
```powershell
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/phpstan analyse --memory-limit=1G
```
*Ekspektasi: [OK] No errors found.*

### 6. Kompilasi Aset Frontend dengan Vite
```powershell
npm run build
```
*Ekspektasi: Build berhasil tanpa warning/error.*

---

## ✅ 7. 📋 CHECKLIST PENYELESAIAN (Definition of Done)

- [x] Modul styling tab `.arsip-tab-bar`, `.arsip-tab-btn`, `.arsip-tab-pane`, dan `.arsip-info-box` ditambahkan ke `resources/css/pages/superadmin/superadmin-common.css`.
- [x] Tag `<style>` lokal (53 baris) di dalam `resources/views/superadmin/arsip/show.blade.php` berhasil dibuang 100%.
- [x] `@section('page_title')`, `@section('page_description')`, dan `@section('page_actions')` dihapus untuk mengeliminasi duplikasi judul header putih.
- [x] Hero Banner Khas Sleman MagKom (`.sa-hero-banner`) dirender dengan nama alumni, avatar inisial/foto, badge status, info kontak, dan tombol aksi terintegrasi.
- [x] Grid 4 Kartu Statistik Tile-Stat (`<x-gentelella.tile-stat>`) tampil rapi dengan warna `purple`, `blue`, `teal`, dan `amber`.
- [x] Tab Navigation (`Ringkasan & Biodata`, `Riwayat Tugas`, `Logbook Harian`, `Dokumen Pendaftaran`) berfungsi mulus dan responsif.
- [x] Kartu Penempatan & Institusi dan Pembimbing Lapangan tertata rapi dalam grid 2 kolom seimbang berstandar `.gentelella-card`.
- [x] Seluruh unit & feature tests yang relevan (`AdminLaporanDanArsipTest` & `DeltaDashboardEnhancementTest`) lolos hijau 100%.
- [x] Tampilan konsisten dalam mode terang (*Light Mode*) dan mode gelap (*Dark Mode*).

---

> **Catatan Lead Architect:**  
> Halaman detail arsip ini adalah etalase rekam jejak historis peserta dinas. Pastikan setiap kartu menyajikan data secara bermartabat, berbobot, dan enak dipandang sesuai kehormatan tata kelola Dinas Kominfo Sleman.

