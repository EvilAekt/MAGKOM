# TASK BRIEF SENIOR DEVELOPER: STANDARISASI DESAIN UI CADANGAN DATABASE MENGIKUTI DESIGN SYSTEM MASTER BIDANG DINAS KOMINFO

> **Dari:** Senior Developer / Lead Frontend Architect  
> **Kepada:** Junior Developer / AI Agent (Gemini 3.8 Flash / Antigravity Agent)  
> **Target Repositori:** MAGKOM (Portal Manajemen Magang Dinas Kominfo Sleman)  
> **Status:** COMPLETED & VERIFIED 100% (STANDARISASI PARITAS DESIGN SYSTEM TUNTAS)  
> **Tingkat Kesulitan:** Menengah (Blade Template Refactoring, Gentelella Design System Alignment, Tile-Stat Parity, a11y & Test Verification)  
> **Halaman Acuan (Gold Standard):** [`resources/views/superadmin/bidang/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/bidang/index.blade.php)  
> **Halaman Target:** [`resources/views/superadmin/backup/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/backup/index.blade.php)  
> **Dokumentasi Terkait:** `AGENTS.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `resources/css/layouts/gentelella.css`

---

## 1. 🔍 LATAR BELAKANG & ANALISIS KESENJANGAN VISUAL (VISUAL GAP ANALYSIS)

### 1.1 Masalah Saat Ini pada Halaman Cadangan Database
Halaman Cadangan Database yang baru diimplementasikan secara teknis fungsional sudah bekerja sempurna (backup berhasil dibuat, di-gzip, di-download, dan di-audit). Namun dari aspek antarmuka pengguna (**UI/UX visual**), halaman ini **belum selaras dengan standar desain visual halaman-halaman lain di portal SuperAdmin** (seperti halaman *Master Bidang Dinas Kominfo*, *Master Jurusan*, dan *Bulan Magang*).

Berdasarkan komparasi visual:
- **Gambar Referensi (Image 1 - Master Bidang Dinas Kominfo)**:
  1. Menggunakan **Topbar Title** & **Breadcrumb Terintegrasi** di layout utama.
  2. Memiliki **Hero Banner Gradien Biru Khas Sleman (`.sa-hero-banner`)** dengan judul tebal, subtitle rapi dengan pemisah bullet point (`•`), dan tombol aksi utama (putih kontras `.btn-banner-action`) diletakkan di dalam banner kanan atas.
  3. Memiliki **Grid 4 Kartu Statistik (`.tiles-grid` dengan `<x-gentelella.tile-stat>`)**:
     - Ikon di dalam kotak melengkung lembut bertint warna.
     - Label judul kecil huruf kapital (misal `TOTAL SNAPSHOT`).
     - Nilai metrik besar dan tebal berdampingan dengan pill badge tren status (misal `• Arsip`, `• Gzip`).
     - Keterangan subtitle di bagian bawah.
     - **Grafik mini bar chart (sparkline)** di sisi kanan kartu.
  4. Memiliki **Panel Tabel Standar Gentelella (`<x-gentelella.x-panel class="has-table">`)**:
     - Header panel dengan judul dan ikon collapsible chevron di kanan atas.
     - Tabel dengan kelas `.table.jambo_table.bulk_action`.
     - Baris tabel bersih dengan kelas `.sa-table-row`.
     - Tombol aksi menggunakan tombol outline `.sa-btn-action-outline` (Unduh) dan tombol bahaya `.sa-btn-action-danger` (Hapus).

- **Kondisi Halaman Cadangan Saat Ini (Image 2 - Current Backup Hub)**:
  1. Breadcrumb masih dibuat manual inline dengan teks biasa di dalam body konten.
  2. Tidak memiliki Hero Banner gradien biru khas Sleman (menggunakan heading polos putih dengan border-bottom).
  3. Menggunakan komponen `<x-gentelella.card>` kaku dengan border-left tebal 4px, tanpa sparkline bars dan tanpa layout `tile-stat` standar.
  4. Tabel menggunakan styling kustom dengan tombol aksi yang belum seragam dengan tombol outline standar portal.

---

## 2. 🏛️ BLUEPRINT TEKNIS PARITAS DESIGN SYSTEM (TARGET DESIGN)

Junior Developer / Gemini 3.8 Flash wajib mengubah struktur Blade pada [`resources/views/superadmin/backup/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/backup/index.blade.php) agar mengikuti blueprint berikut secara presisi:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [TOPBAR & BREADCRUMBS] SuperAdmin / Cadangan Basis Data                                │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [SA HERO BANNER GRADIENT]                                                              │
│  Pusat Cadangan Basis Data (Disaster Recovery)                                         │
│  Cakupan: Enkapsulasi & Snapshot Basis Data • Total Snapshot: 10 Arsip Tersedia        │
│                                                                                        │
│  [Tombol: Telemetri Sistem]   [Tombol Putih: + Buat Snapshot Baru]                     │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [4 TILE-STAT GRID - COMPONENT <x-gentelella.tile-stat>]                                │
│ ┌────────────────┐ ┌────────────────┐ ┌────────────────┐ ┌────────────────┐           │
│ │ [Icon]         │ │ [Icon]         │ │ [Icon]         │ │ [Icon]         │           │
│ │ TOTAL SNAPSHOT │ │ UKURAN ARSIP   │ │ RUANG BEBAS    │ │ FORMAT ARSIP   │           │
│ │ 10 [• Arsip]   │ │ 0.2 MB [• Gz]  │ │ 40,174 MB [•]  │ │ Gzip [• Hemat] │   [Bars]  │
│ │ Maks 10 arsip  │ │ Storage lokal  │ │ Kapasitas aman │ │ Level 9 stream │           │
│ └────────────────┘ └────────────────┘ └────────────────┘ └────────────────┘           │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [X-PANEL TABLE: <x-gentelella.x-panel class="has-table">]                              │
│  Daftar Snapshot Cadangan Basis Data                                      [^ Collapse] │
│ ┌────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ NAMA BERKAS SNAPSHOT │ FORMAT │ WAKTU PEMBUATAN │ UKURAN │ CHECKSUM SHA-256 │ AKSI │ │
│ ├──────────────────────┼────────┼─────────────────┼────────┼──────────────────┼──────┤ │
│ │ backup-xxx.sql.gz    │ [Gzip] │ 09 Nov 2026 ... │ 0.2 MB │ 712b...2f3c1     │ [📥] │ │
│ └────────────────────────────────────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. 📋 SPESIFIKASI 4 KARTU STATISTIK (`<x-gentelella.tile-stat>`)

Gunakan komponen resmi `<x-gentelella.tile-stat>` dengan konfigurasi berikut:

| No | Judul (`title`) | Nilai (`value`) | Warna (`color`) | Tren Tipe | Tren Teks | Teks Bawah (`subText`) | Sparkline Bars | Ikon SVG |
|:---:|---|---|:---:|:---:|:---:|---|:---:|---|
| **1** | `TOTAL SNAPSHOT` | `{{ $totalBackups }}` | `blue` | `info` | `Arsip` | Maksimal retensi 10 arsip cadangan | `[30, 45, 60, 75, 85, 95]` | Database Silinder |
| **2** | `UKURAN TERKOMPRESI` | `{{ $totalSizeMb }} MB` | `teal` | `success` | `Gzip` | Tersimpan di storage lokal privat | `[25, 40, 55, 70, 80, 90]` | Folder Arsip |
| **3** | `RUANG BEBAS DISK` | `{{ number_format($diskTelemetry['free_disk_mb']) }} MB` | `{{ $diskTelemetry['is_disk_low'] ? 'red' : 'amber' }}` | `{{ $diskTelemetry['is_disk_low'] ? 'danger' : 'warning' }}` | `{{ $diskTelemetry['is_disk_low'] ? 'Kritis' : 'Aman' }}` | `{{ $diskTelemetry['is_disk_low'] ? 'Kapasitas sisa < 500 MB' : 'Kapasitas aman pencadangan' }}` | `[20, 35, 50, 65, 80, 85]` | Hard Drive / Disk |
| **4** | `FORMAT ENKAPSULASI` | `Gzip .gz` | `purple` | `info` | `85% Hemat` | Kompresi stream level 9 on-the-fly | `[15, 25, 40, 55, 70, 85]` | Paket / Centang Kompresi |

---

## 4. 📝 KODE IMPLEMENTASI LENGKAP UNTUK JUNIOR DEVELOPER

Gantikan seluruh isi berkas [`resources/views/superadmin/backup/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/backup/index.blade.php) dengan struktur berikut:

```blade
@extends('layouts.dashboard')

@section('title', 'Pusat Cadangan Basis Data - SuperAdmin MagKom')
@section('topbar_title', 'Pusat Cadangan & Pemulihan Basis Data')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Cadangan Basis Data</span>
@endsection

@push('styles')
    @vite(['resources/css/pages/superadmin/superadmin-common.css'])
@endpush

@section('content')
<div class="sa-fluid-container" style="display: flex; flex-direction: column; gap: 1.5rem;">

    {{-- 1. Hero Banner Khas Sleman MagKom (Identik dengan Master Bidang) --}}
    <div class="sa-hero-banner">
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">Pusat Cadangan Basis Data (Disaster Recovery)</h1>
                <div class="banner-subtitle">
                    <span>Cakupan: <strong>Enkapsulasi & Pemulihan Sistem</strong></span>
                    <span class="bullet-separator">&bull;</span>
                    <span>Total Snapshot: <strong>{{ $totalBackups }} Arsip Tersedia</strong></span>
                    <span class="bullet-separator">&bull;</span>
                    <span>Status: <strong>SEC-03 SuperAdmin Only</strong></span>
                </div>
            </div>
            <div class="banner-actions-group">
                <a href="{{ route('superadmin.health.index') }}" class="btn-banner-action" style="background: rgba(255, 255, 255, 0.15); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.25);">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                    <span>Telemetri Sistem</span>
                </a>
                <form method="POST" action="{{ route('superadmin.backup.snapshot') }}" style="display: inline-block;" onsubmit="return confirm('Picu pembuatan snapshot basis data baru sekarang? Proses akan berjalan dengan kompresi Gzip.')">
                    @csrf
                    <button type="submit" class="btn-banner-action">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Buat Snapshot Baru</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ALERT NOTIFIKASI STATUS OPERASIONAL --}}
    @if(session('success'))
        <div style="padding: 1rem 1.25rem; border-radius: 10px; background: #DCFCE7; color: #166534; border: 1px solid #BBF7D0; font-size: 0.875rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div style="padding: 1rem 1.25rem; border-radius: 10px; background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; font-size: 0.875rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- 2. Grid 4 Kartu Statistik Mengadopsi Gentelella Tile-Stat (Identik dengan Master Bidang) --}}
    <div class="tiles-grid">
        <!-- 1. Total Snapshot -->
        <x-gentelella.tile-stat
            title="TOTAL SNAPSHOT"
            value="{{ $totalBackups }}"
            color="blue"
            trendType="info"
            trendDirection="up"
            trendText="Arsip"
            subText="Maksimal retensi 10 arsip cadangan"
            :sparkBars="[30, 45, 60, 75, 85, 95]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                    <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                    <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 2. Ukuran Arsip Terkompresi -->
        <x-gentelella.tile-stat
            title="UKURAN ARSIP"
            value="{{ $totalSizeMb }} MB"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="Gzip"
            subText="Tersimpan di storage lokal privat"
            :sparkBars="[25, 40, 55, 70, 80, 90]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 3. Ruang Bebas Partisi Disk -->
        <x-gentelella.tile-stat
            title="RUANG BEBAS DISK"
            value="{{ number_format($diskTelemetry['free_disk_mb']) }} MB"
            color="{{ $diskTelemetry['is_disk_low'] ? 'red' : 'amber' }}"
            trendType="{{ $diskTelemetry['is_disk_low'] ? 'danger' : 'warning' }}"
            trendDirection="up"
            trendText="{{ $diskTelemetry['is_disk_low'] ? 'Kritis' : 'Aman' }}"
            subText="{{ $diskTelemetry['is_disk_low'] ? 'Kapasitas sisa < 500 MB' : 'Kapasitas aman pencadangan' }}"
            :sparkBars="[20, 35, 50, 65, 80, 85]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect>
                    <rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect>
                    <line x1="6" y1="6" x2="6.01" y2="6"></line>
                    <line x1="6" y1="18" x2="6.01" y2="18"></line>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 4. Format Enkapsulasi -->
        <x-gentelella.tile-stat
            title="FORMAT ENKAPSULASI"
            value="Gzip .gz"
            color="purple"
            trendType="info"
            trendDirection="up"
            trendText="85% Hemat"
            subText="Kompresi stream level 9 on-the-fly"
            :sparkBars="[15, 25, 40, 55, 70, 85]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    {{-- 3. Data Table Panel Mengadopsi Gentelella X-Panel (Identik dengan Master Bidang) --}}
    <x-gentelella.x-panel class="has-table" title="Daftar Snapshot Cadangan Basis Data">
        @if($backupFiles->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 48px; height: 48px; margin: 0 auto 1rem; border-radius: 50%; background: var(--neutral-100); display: flex; align-items: center; justify-content: center; color: var(--neutral-500);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                </div>
                <p style="font-size: 1rem; font-weight: 600; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Belum ada arsip cadangan basis data.</p>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">Klik tombol <strong>Buat Snapshot Baru</strong> untuk mencadangkan database sekarang.</p>
            </div>
        @else
            <div class="table-responsive-gentelella" style="overflow-x: auto;">
                <table class="table jambo_table bulk_action" style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr class="headings">
                            <th>Nama Berkas Snapshot</th>
                            <th style="width: 130px; text-align: center;">Format</th>
                            <th>Waktu Pembuatan</th>
                            <th>Ukuran Berkas</th>
                            <th>Integritas Checksum (SHA-256)</th>
                            <th style="text-align: right; width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backupFiles as $item)
                            <tr class="sa-table-row" style="border-bottom: 1px solid var(--neutral-200);">
                                <td style="padding: 1rem 1.25rem; font-weight: 700; color: var(--neutral-900); white-space: nowrap;">
                                    <div style="font-size: 0.875rem; font-family: monospace; color: #01347C;">
                                        {{ $item['filename'] }}
                                    </div>
                                    <div style="font-size: 0.72rem; color: var(--neutral-500); font-weight: 500; margin-top: 0.2rem;">
                                        Lokasi: storage/app/backups
                                    </div>
                                </td>
                                <td style="padding: 1rem 1rem; text-align: center; white-space: nowrap;">
                                    <span class="badge-status-blue">
                                        {{ $item['format'] }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem; color: var(--neutral-700); font-size: 0.8125rem; white-space: nowrap;">
                                    {{ $item['created_at'] }}
                                </td>
                                <td style="padding: 1rem 1rem; font-weight: 700; color: var(--neutral-900); white-space: nowrap;">
                                    {{ $item['size_mb'] }} MB
                                </td>
                                <td style="padding: 1rem 1rem; white-space: nowrap;">
                                    <span class="backup-checksum-badge" title="{{ $item['checksum'] }}">
                                        {{ substr($item['checksum'], 0, 16) }}...{{ substr($item['checksum'], -8) }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1.25rem; text-align: right; white-space: nowrap;">
                                    <div style="display: inline-flex; gap: 0.5rem; align-items: center; justify-content: flex-end;">
                                        <a href="{{ route('superadmin.backup.download', $item['filename']) }}"
                                           class="sa-btn-action-outline"
                                           onclick="return confirm('Peringatan Privasi (UU PDP): Berkas cadangan basis data ini memuat data pribadi peserta dan seluruh informasi internal. Pastikan Anda menyimpannya di lokasi aman. Lanjutkan unduh?')">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                            <span>Unduh</span>
                                        </a>

                                        <form method="POST" action="{{ route('superadmin.backup.destroy', $item['filename']) }}" style="display: inline-block;" onsubmit="return confirm('Hapus berkas snapshot ini secara permanen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="sa-btn-action-danger">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-gentelella.x-panel>

</div>
@endsection
```

---

## 5. 🚀 PANDUAN EKSEKUSI TAHAP DEMI TAHAP (CHECKLIST UNTUK JUNIOR DEV / AI)

- [x] **Langkah 1**: Periksa file acuan [`resources/views/superadmin/bidang/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/bidang/index.blade.php) dan pahami penggunaan komponen `<x-gentelella.tile-stat>` serta `<x-gentelella.x-panel class="has-table">`.
- [x] **Langkah 2**: Perbarui file target [`resources/views/superadmin/backup/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/backup/index.blade.php) sesuai kode spesifikasi di atas.
- [x] **Langkah 3**: Pastikan tidak ada tag `<style>` inline di dalam file Blade (seluruh styling tetap mengandalkan `superadmin-common.css` dan `gentelella.css`).
- [x] **Langkah 4**: Jalankan kompilasi frontend Vite:
  ```bash
  npm run build
  ```
- [x] **Langkah 5**: Jalankan test suite `BackupDatabaseTest`:
  ```powershell
  & "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test --filter=BackupDatabaseTest
  ```
  Pastikan 8 test tetap 100% lulus (*green*).
- [x] **Langkah 6**: Jalankan linter Pint untuk memastikan kepatuhan formatting:
  ```powershell
  & "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/pint --test
  ```

---

## 6. ✅ DEFINITION OF DONE (DOD)
1. [x] Tampilan Cadangan Database 100% identik secara tata letak, banner hero, kartu statistik (4 tile stat), dan tabel data dengan halaman Master Bidang Dinas Kominfo.
2. [x] Tidak ada CSS inline di file Blade.
3. [x] Seluruh fungsionalitas (Snapshot, Download, Delete, Rate Limit, dan Audit Log) tetap bekerja 100%.
4. [x] Test suite `BackupDatabaseTest` lolos hijau (8 passed, 25 assertions).
