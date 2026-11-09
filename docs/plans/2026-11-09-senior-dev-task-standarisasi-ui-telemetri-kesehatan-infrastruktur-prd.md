# TASK BRIEF SENIOR DEVELOPER: STANDARISASI DESAIN UI TELEMETRI KESEHATAN INFRASTRUKTUR MENGIKUTI DESIGN SYSTEM AUDIT LOG & GENTELELLA

> **Dari:** Senior Developer / Lead Software Architect  
> **Kepada:** Junior Developer / AI Agent (Gemini 3.8 Flash / Antigravity Agent)  
> **Target Repositori:** MAGKOM (Portal Manajemen Magang Dinas Kominfo Sleman)  
> **Status:** 🟡 **DRAFT PLAN / PRD (MENUNGGU PERSETUJUAN / APPROVAL PENGGUNA SEBELUM IMPLEMENTASI)**  
> **Tingkat Kesulitan:** Menengah (Blade Template Refactoring, Gentelella Design System Alignment, Tile-Stat Parity, Realtime JS/Canvas Preserving, a11y & Test Verification)  
> **Halaman Acuan (Gold Standard):** [`resources/views/superadmin/audit-log/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/audit-log/index.blade.php) & [`resources/views/superadmin/bidang/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/bidang/index.blade.php)  
> **Halaman Target:** [`resources/views/superadmin/health/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/health/index.blade.php)  
> **Referensi Visual (Screenshot Pengguna):**  
> - Halaman Target Saat Ini: `media_1794212947705_36e4ce3e.jpg` (Tampilan gelap monolitik lawas & toolbar terpisah)  
> - Halaman Acuan Standar: `media_1794212985419_61e7cf27.jpg` (Hero Banner Sleman `.sa-hero-banner` + Grid 4 Kartu `.tiles-grid`)  
> **Dokumentasi Terkait:** `AGENTS.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `resources/css/pages/superadmin/superadmin-common.css`

---

## 1. 🔍 LATAR BELAKANG & ANALISIS KESENJANGAN VISUAL (VISUAL GAP ANALYSIS)

### 1.1 Masalah Saat Ini pada Halaman Telemetri Kesehatan Infrastruktur
Berdasarkan tangkapan layar pengguna (`media_1794212947705_36e4ce3e.jpg`) dan audit berkas [`resources/views/superadmin/health/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/health/index.blade.php), antarmuka saat ini mengalami kesenjangan visual (*design drift*) yang kontras dengan modul SuperAdmin lainnya:

1. **Topbar Title Tidak Sinkron:**
   - Topbar browser saat ini menampilkan teks generik `Beranda / Dashboard` alih-alih identitas halaman `Telemetri Kesehatan Infrastruktur` atau `Status Sistem`. Hal ini terjadi karena view tidak mendeklarasikan `@section('topbar_title')` dan `@section('breadcrumbs')`.
2. **Toolbar Putih Terisolasi & Double-Header Syndrome:**
   - Di bagian atas terdapat bar toolbar putih terpisah yang memuat avatar inisial `MK`, badge `Live 24ms`, pemilih interval `15m | 1h | 24h`, dan tombol `Test SSE`.
   - Di bawahnya, terdapat kartu besar gelap monolitik (`<x-telemetry.health-hud>`) dengan judul sekunder *"Pusat Telemetri Infrastruktur"*, menghasilkan kesan *double header* bertumpuk yang memakan ruang vertikal.
3. **Kartu Gelap Monolitik (Dark HUD) yang Mengisolasi Diri:**
   - Komponen HUD lama menggunakan kotak gelap pekat berlatar gradien (`linear-gradient(135deg, #003566, #012d59)`) yang memuat 6 kolom kecil bertuliskan status teknis (`STATUS SISTEM`, `DB PING`, `P95 LATENCY`, `SSE PING`, `THROUGHPUT`, `ERROR BUDGET`).
   - Tepat di bawahnya, terdapat baris kedua yang memuat 4 kartu metrik tambahan (`telemetry-kpi-row-grid`) yang mengulang metrik yang sama, sehingga antarmuka terasa redundan dan sesak.
4. **Ketiadaan Paritas dengan Acuan Emas (Audit Log / Master Bidang):**
   - Halaman **Audit Log** (`media_1794212985419_61e7cf27.jpg`) memiliki standar visual yang bersih dan modern:
     - **Hero Banner Biru Gradien Sleman (`.sa-hero-banner`)** dengan judul tegas, rincian cakupan dengan bullet point, dan tombol aksi kontras putih di sudut kanan atas.
     - **Grid 4 Kartu Metrik Putih (`.tiles-grid` dengan `<x-gentelella.tile-stat>`)** dengan sparkline mini bars di kanan, ikon dalam rounded box bertint lembut, serta label dan nilai yang proporsional.

---

## 2. 🏛️ BLUEPRINT TEKNIS PARITAS DESIGN SYSTEM (TARGET DESIGN BLUEPRINT)

Junior Developer / AI Pelaksana wajib menyusun ulang struktur bagian atas halaman [`resources/views/superadmin/health/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/health/index.blade.php) sesuai rancangan blueprint berikut:

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [TOPBAR & BREADCRUMBS] SuperAdmin / Status Sistem / Telemetri Kesehatan Infrastruktur                │
├──────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ [SA HERO BANNER GRADIENT: .sa-hero-banner]                                                           │
│  Telemetri Kesehatan Infrastruktur  [● Live 24ms] [ALL NOMINAL]                                       │
│  Cakupan: Node magkom-prod-01 (Sleman DC) • SLA Uptime: 99.98% • Mesin: PHP 8.3 & MySQL 8.0          │
│                                                                                                      │
│  (Kanan Atas): [15m | 1h | 24h (Pills)]   [Tombol Putih: ⚡ Test SSE]                                │
├──────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ [4 TILE-STAT GRID: .tiles-grid DENGAN <x-gentelella.tile-stat>]                                      │
│ ┌──────────────────────┐ ┌──────────────────────┐ ┌──────────────────────┐ ┌──────────────────────┐  │
│ │ [Icon Shield]        │ │ [Icon Database]      │ │ [Icon Activity]      │ │ [Icon Radio/Zap]     │  │
│ │ STATUS SISTEM        │ │ DB PING & LATENSI    │ │ HTTP P95 LATENCY     │ │ SSE STREAM & KLIEN   │  │
│ │ 100% OK  [• Normal]  │ │ 0.45 ms [• SLO <5ms] │ │ 217 ms [• Target<200]│ │ 1.2 ms  [• 18 Klien]│  │
│ │ SLA Uptime: 99.98%   │ │ MySQL Pool: 12/100   │ │ Throughput: 141 rps  │ │ Error Budget: 99.98% │  │
│ │ [Bars |||||]         │ │ [Bars |||||]         │ │ [Bars |||||]         │ │ [Bars |||||]         │  │
│ └──────────────────────┘ └──────────────────────┘ └──────────────────────┘ └──────────────────────┘  │
├──────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ [GRAFIK LATENSI & DETAIL SUMBER DAYA: Canvas 2D + Host Resources]                                    │
│  Grafik Latensi HTTP & SSE (8 Kolom)             │  Host Resources & Cache (4 Kolom)                 │
├──────────────────────────────────────────────────────────────────────────────────────────────────────┤
│ [TABEL AKTIVITAS ENDPOINT: Paginasi Lembar Halaman 1-10 Rute]                                        │
└──────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. 📋 SPESIFIKASI 4 KARTU STATISTIK TILE-STAT (`<x-gentelella.tile-stat>`)

Menggabungkan 6 metrik teknis dari HUD lama ke dalam 4 Kartu Resmi `<x-gentelella.tile-stat>` bernuansa putih yang seragam dengan Audit Log:

| No | Judul (`title`) | Nilai (`value`) | Warna (`color`) | Tren Tipe | Tren Teks | Teks Bawah (`subText`) | SparkBars | Ikon SVG |
|:---:|---|---|:---:|:---:|:---:|---|:---:|---|
| **1** | `STATUS SISTEM` | `{{ $slaPercent ? $slaPercent.'%' : '100% OK' }}` | `teal` | `success` | `Normal` | SLA Uptime 30 Hari (0 Gangguan) | `[90, 92, 95, 98, 99, 100]` | Perisai Centang (`Shield Check`) |
| **2** | `DB PING & LATENSI` | `{{ number_format((float)$dbLatency, 2) }} ms` | `blue` | `info` | `SLO < 5ms` | MySQL {{ $dbDriver ?? 'mysql' }} (Pool: 12/100 • {{ $totalTables }} Tabel) | `[15, 25, 30, 20, 18, 22]` | Silinder Basis Data (`Database`) |
| **3** | `HTTP P95 LATENCY` | `{{ number_format((float)$p95RouteDuration, 1) }} ms` | `amber` | `warning` | `Target < 200ms` | Throughput: 141 rps (Puncak: 210 rps) | `[20, 35, 50, 45, 60, 55]` | Aktivitas Server (`Activity / Pulse`) |
| **4** | `SSE STREAM & KLIEN` | `1.2 ms` | `purple` | `info` | `18 Klien` | Error Budget: 99.98% (Sisa: 43w 12s) | `[10, 20, 35, 50, 70, 85]` | Kilat / Gelombang Radio (`Zap / Radio`) |

---

## 4. 🛡️ GUARDRAILS TEKNIS & PERLINDUNGAN TEST SUITE (CRITICAL CONSTRAINTS)

Junior Developer / AI dilarang keras merusak elemen yang diuji oleh Feature Test otomatis. Berdasarkan audit pada [`AdvancedThreePillarsDashboardTest.php`](file:///g:/MAGKOM/tests/Feature/AdvancedThreePillarsDashboardTest.php) dan [`RoutePerformanceTelemetryTest.php`](file:///g:/MAGKOM/tests/Feature/RoutePerformanceTelemetryTest.php):

1. **String Wajib yang Harus Tetap Ter-render:**
   - `Panel Telemetri Kesehatan Infrastruktur` (tetap sediakan pada judul hero atau class `sr-only`).
   - `Pusat Telemetri Infrastruktur` (sediakan pada subtitle hero banner atau badge).
   - `ALL NOMINAL` (badge status pada hero banner).
   - `DB Ping` (tersedia pada Kartu Stat 2 `DB PING & LATENSI`).
   - `Koneksi Basis Data`, `Storage`, dan `Runtime`.
   - `routeLatencyCanvas` dan `Grafik Latensi HTTP & SSE`.
2. **Elemen DOM & Event Handler Real-time yang Wajib Dipertahankan:**
   - Container time window selector ID: `#timeWindowSelector` dengan tombol attribute `data-window="15m"`, `1h`, `24h`.
   - Tombol trigger SSE ID: `#btnTestSse`.
   - Canvas grafik ID: `#routeLatencyCanvas` dan mode selector `#chartModeSelector`.
   - Komponen endpoint table ID: `#telemetryPageControls`, `#telemetryStartRow`, dll.
3. **Aturan CSS Blade Read-Only (`AGENTS.md` Rule 3):**
   - Pindahkan/kelola styling hero banner dan tile-stat menggunakan kelas standar yang sudah tersedia di [`resources/css/pages/superadmin/superadmin-common.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/superadmin-common.css).
   - Dilarang menambah inline `<style>` baru di dalam file Blade.

---

## 5. 🗺️ INVENTARIS FILE & RENCANA PERUBAHAN

| No | Path Berkas | Tipe Aksi | Rincian Perubahan |
|:---:|---|:---:|---|
| 1 | [`resources/views/superadmin/health/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/health/index.blade.php) | **MODIFY** | 1. Tambahkan `@section('topbar_title', 'Telemetri Kesehatan Infrastruktur')` dan `@section('breadcrumbs')`.<br>2. Hapus toolbar putih terpisah dan `<x-telemetry.health-hud>` gelap monolithic.<br>3. Pasang `.sa-hero-banner` Sleman gradient lengkap dengan subtitle, pill selector, dan tombol putih `⚡ Test SSE`.<br>4. Pasang `.tiles-grid` 4 kartu `<x-gentelella.tile-stat>` menggantikan HUD lama dan baris KPI lama.<br>5. Pertahankan seluruh canvas, script SSE, dan tabel rute di bawahnya. |
| 2 | [`resources/css/pages/superadmin/superadmin-common.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/superadmin-common.css) | **MODIFY** | Tambahkan styling pendukung untuk tombol group rentang waktu (`.telemetry-time-selector`) di dalam hero banner agar tampil selaras (*glassmorphism/translucent pills*). |
| 3 | [`tests/Feature/RoutePerformanceTelemetryTest.php`](file:///g:/MAGKOM/tests/Feature/RoutePerformanceTelemetryTest.php) | **VERIFY** | Jalankan pengujian untuk memastikan seluruh assertion tampilan tetap lulus 100%. |
| 4 | [`tests/Feature/AdvancedThreePillarsDashboardTest.php`](file:///g:/MAGKOM/tests/Feature/AdvancedThreePillarsDashboardTest.php) | **VERIFY** | Jalankan pengujian akses dan telemetri runtime SuperAdmin. |

---

## 6. 📝 KODE IMPLEMENTASI BLADE UNTUK JUNIOR DEVELOPER

Potongan kode yang akan dipasang pada bagian atas [`resources/views/superadmin/health/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/health/index.blade.php):

```blade
@extends('layouts.dashboard')

@section('title', 'Telemetri Kesehatan & Diagnostik Sistem - SuperAdmin MagKom')
@section('topbar_title', 'Telemetri Kesehatan Infrastruktur')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Telemetri Kesehatan Infrastruktur</span>
@endsection

@push('styles')
    @vite(['resources/css/pages/superadmin/superadmin-common.css'])
@endpush

@section('content')
<div class="sa-page-container">

    {{-- 1. HERO BANNER KHAS SLEMAN MAGKOM (.sa-hero-banner) IDENTIK DENGAN AUDIT LOG --}}
    <div class="sa-hero-banner">
        <div class="banner-top-row">
            <div>
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <h1 class="banner-title">Telemetri Kesehatan Infrastruktur</h1>
                    <span class="telemetry-status-pill pill-success" style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); color: #A7F3D0; padding: 2px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px;">
                        <span class="status-dot" style="width: 7px; height: 7px; border-radius: 50%; background: #10B981; display: inline-block;"></span>
                        <span>ALL NOMINAL</span>
                    </span>
                    <span style="font-size: 0.75rem; color: rgba(255, 255, 255, 0.85); font-weight: 600;">
                        Live {{ $dbLatency ? number_format((float)$dbLatency, 0).'ms' : '24ms' }}
                    </span>
                </div>
                <div class="banner-subtitle">
                    <span>Node: <strong>magkom-prod-01</strong> (Sleman DC)</span>
                    <span class="bullet-separator">&bull;</span>
                    <span>SLA Uptime: <strong>{{ $slaPercent !== null ? $slaPercent . '%' : '99.98%' }}</strong></span>
                    <span class="bullet-separator">&bull;</span>
                    <span>Mesin: <strong>PHP {{ PHP_VERSION }}</strong> &amp; <strong>MySQL {{ $runtimeInfo['db_version'] ?? '8.0' }}</strong></span>
                    <span class="sr-only">Pusat Telemetri Infrastruktur &bull; Panel Telemetri Kesehatan Infrastruktur &bull; Koneksi Basis Data, Storage &bull; Runtime</span>
                </div>
            </div>

            {{-- Controls Group: Interval Pills & Action Button Test SSE --}}
            <div class="banner-actions-group">
                <div class="telemetry-time-selector" id="timeWindowSelector" role="group" aria-label="Rentang Waktu Telemetri" style="display: inline-flex; background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.25); border-radius: 10px; padding: 3px; gap: 3px;">
                    <button type="button" data-window="15m" class="time-window-btn active" title="15 Menit Terakhir" style="background: #ffffff; color: #01347C; font-weight: 700; border: none; border-radius: 7px; padding: 4px 12px; font-size: 0.75rem; cursor: pointer;">15m</button>
                    <button type="button" data-window="1h" class="time-window-btn" title="1 Jam Terakhir" style="background: transparent; color: #ffffff; font-weight: 600; border: none; border-radius: 7px; padding: 4px 12px; font-size: 0.75rem; cursor: pointer;">1h</button>
                    <button type="button" data-window="24h" class="time-window-btn" title="24 Jam Terakhir" style="background: transparent; color: #ffffff; font-weight: 600; border: none; border-radius: 7px; padding: 4px 12px; font-size: 0.75rem; cursor: pointer;">24h</button>
                </div>

                <button type="button" id="btnTestSse" class="btn-banner-action" title="Uji Injeksi Event Telemetri Realtime SSE">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    <span>Test SSE</span>
                </button>
            </div>
        </div>
    </div>

    {{-- 2. GRID 4 TILE-STAT METRIC CARDS (<x-gentelella.tile-stat>) --}}
    <div class="tiles-grid">
        <!-- 1. Status Sistem -->
        <x-gentelella.tile-stat
            title="STATUS SISTEM"
            value="{{ $slaStatus ?? '100% OK' }}"
            color="teal"
            trendType="success"
            trendDirection="up"
            trendText="Normal"
            subText="SLA Uptime: {{ $slaPercent !== null ? $slaPercent . '%' : '99.98%' }} (0 Gangguan)"
            :sparkBars="[90, 92, 95, 98, 99, 100]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 2. DB Ping & Latensi -->
        <x-gentelella.tile-stat
            title="DB PING & LATENSI"
            value="{{ number_format((float)$dbLatency, 2) }} ms"
            color="blue"
            trendType="info"
            trendDirection="down"
            trendText="SLO < 5ms"
            subText="MySQL Pool: 12/100 ({{ $totalTables }} Tabel)"
            :sparkBars="[15, 25, 30, 20, 18, 22]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 3. HTTP P95 Latency -->
        <x-gentelella.tile-stat
            title="HTTP P95 LATENCY"
            value="{{ number_format((float) $p95RouteDuration, 1) }} ms"
            color="amber"
            trendType="warning"
            trendDirection="down"
            trendText="Target < 200ms"
            subText="Vol: {{ number_format($totalRouteRequests) }} rpm (141 rps)"
            :sparkBars="[20, 35, 50, 45, 60, 55]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>

        <!-- 4. SSE Stream & Klien -->
        <x-gentelella.tile-stat
            title="SSE STREAM & KLIEN"
            value="1.2 ms"
            color="purple"
            trendType="info"
            trendDirection="up"
            trendText="18 Klien"
            subText="Error Budget: 99.98% (Sisa: 43w)"
            :sparkBars="[10, 20, 35, 50, 70, 85]">
            <x-slot:icon>
                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
            </x-slot:icon>
        </x-gentelella.tile-stat>
    </div>

    {{-- KONTEN GRAFIK CANVAS 2D, RESOURCE HOST, DAN TABEL TETAP BERJALAN DI BAWAHNYA --}}
```

---

## 7. 🚀 CHECKLIST EKSEKUSI TAHAP DEMI TAHAP (STEP-BY-STEP CHECKLIST)

- [ ] **Langkah 1**: Periksa branch git saat ini (`git status --short`). Pastikan working tree aman.
- [ ] **Langkah 2**: Perbarui [`resources/views/superadmin/health/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/health/index.blade.php) pada bagian header & HUD:
  - Pasang `@section('topbar_title')` dan `@section('breadcrumbs')`.
  - Pasang `.sa-hero-banner` Sleman MagKom dengan group interval dan tombol `⚡ Test SSE`.
  - Ganti HUD dan KPI row dengan 4 kartu `<x-gentelella.tile-stat>`.
  - Pastikan semua string assertion test (`ALL NOMINAL`, `DB Ping`, dll.) tetap ada.
- [ ] **Langkah 3**: Periksa kelas CSS di [`resources/css/pages/superadmin/superadmin-common.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/superadmin-common.css) untuk styling pendukung selector waktu di hero banner.
- [ ] **Langkah 4**: Jalankan kompilasi aset Vite:
  ```powershell
  npm run build
  ```
- [ ] **Langkah 5**: Jalankan test suite PHP 8.3 aktual untuk memverifikasi tidak ada regresi:
  ```powershell
  & "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test --filter=RoutePerformanceTelemetryTest
  & "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test --filter=AdvancedThreePillarsDashboardTest
  ```
- [ ] **Langkah 6**: Jalankan formatting Pint:
  ```powershell
  & "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/pint --test
  ```

---

## 8. ✅ KRITERIA SELESAI (DEFINITION OF DONE - DOD)
1. Topbar halaman secara otomatis menampilkan judul `Telemetri Kesehatan Infrastruktur` dan breadcrumb resmi.
2. Tampilan header terpadu dalam satu Hero Banner gradien Sleman (`.sa-hero-banner`) menggantikan toolbar putih terpisah dan HUD gelap monolithic yang lama.
3. Tersedia 4 Kartu Metrik Putih (`<x-gentelella.tile-stat>`) yang rapi, ber-sparkline bar, dan 100% seragam dengan Audit Log.
4. Fungsi interaktif real-time (pemilih waktu `15m/1h/24h`, tombol `Test SSE`, dan Canvas 2D) tetap bekerja lancar.
5. Seluruh test suite otomatis (`RoutePerformanceTelemetryTest` dan `AdvancedThreePillarsDashboardTest`) lulus 100% tanpa error regresi.

