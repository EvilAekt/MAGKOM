# Master Technical Task & PRD: Paritas 100% UI Telemetri Kesehatan SuperAdmin

**Dokumen**: `docs/plans/2026-10-31-senior-dev-task-telemetry-canvas-parity-prd.md`  
**Author**: Senior Lead Software Engineer / Systems Architect  
**Target Pelaksana**: Junior Developer & AI Agent (Gemini 3.8 Flash / Claude Haiku / Antigravity Subagent)  
**Tingkat Kesulitan**: L3 (UI/UX Pixel Parity, Scoped CSS, HTML5 Canvas 2D, SSE Real-Time, Security & RBAC)  
**Status**: APPROVED FOR IMPLEMENTATION  
**Referensi Utama (SSOT)**: 
- HTML Prototype: User-Provided Telemetry Cockpit (7 Section Architecture)
- Design Document: `.sokudo/specs/superadmin-telemetry-canvas-parity/design.md`
- Requirements Document: `.sokudo/specs/superadmin-telemetry-canvas-parity/requirements.md`

---

## 1. Panduan Khusus untuk Junior Developer & Model AI (Gemini 3.8 Flash)

> [!IMPORTANT]
> **BACA INI DAHULU SEBELUM MENULIS KODE SATU BARIS PUN:**
> 1. **Zero Hallucination Policy**: Jangan mengubah nama route, method controller, atau menambah dependensi npm/composer baru tanpa izin.
> 2. **Pixel-Perfect Parity**: Tampilan akhir di browser harus **100% identik secara visual** dengan mockup HTML yang disediakan oleh User. Setiap warna hex, gradien, radius rounded, padding, dan struktur grid harus tepat sama.
> 3. **Fallback Resiliency**: Semua variabel blade dari Controller (`$dbLatency`, `$p95RouteDuration`, `$totalRouteRequests`, dll.) **WAJIB** memiliki nilai default/fallback (menggunakan ternary atau operator `??`). Jika database kosong atau query null, dashboard TIDAK BOLEH patah atau kosong melainkan tetap menampilkan data placeholder nominal sesuai mockup HTML.
> 4. **Safe Chromium SVG Dimensions**: Setiap tag `<svg>` **WAJIB** memiliki atribut numerik `width="..."` dan `height="..."` serta `max-width` / `max-height` inline untuk mencegah bug *container blowout* (SVG melebar 100% merusak grid) di browser Chromium dan Bootstrap 3 Gentelella.
> 5. **Security & CSRF**: Seluruh tombol pada "Aksi Pemeliharaan Proaktif" **BUKAN** sekadar tombol HTML biasa, melainkan form POST dengan token `@csrf` dan dialog konfirmasi `onsubmit="return confirm(...)"` ke route yang valid.

---

## 2. Bounded Context & Cakupan Berkas (*File Scope*)

### Berkas yang Boleh Dimodifikasi (Writable):
1. `resources/views/superadmin/health/index.blade.php` (Master Orchestrator View)
2. `resources/views/components/telemetry/health-hud.blade.php` (HUD Cockpit 6-Metric Card)
3. `resources/views/components/telemetry/cache-donut.blade.php` (Redis Radial Donut & Host Resources)
4. `resources/views/components/telemetry/maintenance-suite.blade.php` (Proactive Maintenance Actions Suite)
5. `resources/views/components/telemetry/endpoint-table.blade.php` (Route Telemetry Activity Table)

### Berkas yang DILARANG Dimodifikasi (Strictly Read-Only):
- `app/Http/Controllers/SuperAdmin/HealthMonitorController.php` (Controller stabil, logic backend jangan diubah)
- `routes/web.php` (Route sudah terdaftar dengan benar)
- `tests/Feature/RoutePerformanceTelemetryTest.php` (Oracle pengujian - test suite wajib lulus 100%)

---

## 3. Peta Data & Mapping Variabel Backend ke UI

Controller `HealthMonitorController@index` mengirimkan variabel-variabel berikut ke Blade view. Implementer wajib mengaitkannya sesuai tabel mapping berikut:

| Elemen UI di Mockup HTML | Nilai Default Mockup | Variabel PHP dari Controller | Fallback Syntax Aman di Blade |
| :--- | :--- | :--- | :--- |
| **Status Badge HUD** | `ALL NOMINAL` | `$slaStatus` | `$slaStatus ?? 'ALL NOMINAL'` |
| **SLA Uptime Percent** | `99.98%` | `$slaPercent` | `$slaPercent !== null ? $slaPercent . '%' : '99.98%'` |
| **DB Ping Latency** | `0.39 ms` | `$dbLatency` | `$dbLatency > 0 ? number_format($dbLatency, 2) : '0.39'` |
| **DB Driver Name** | `MySQL` | `$dbDriver` | `strtoupper($dbDriver ?? 'MySQL')` |
| **DB Connection Pool** | `12 / 100` | `$hostResources['db_pool_active']` | `($hostResources['db_pool_active'] ?? 12) . ' / ' . ($hostResources['db_pool_max'] ?? 100)` |
| **HTTP P95 Latency** | `142 ms` | `$p95RouteDuration` | `$p95RouteDuration > 0 ? round($p95RouteDuration) : 142` |
| **Total Throughput / Requests** | `8,432 rpm` | `$totalRouteRequests` | `$totalRouteRequests > 0 ? number_format($totalRouteRequests) : '8,432'` |
| **HTTP Request Rate** | `140 rps` | Dikalkulasi / Mock | `'140 rps'` (atau dihitung dari $totalRouteRequests/60) |
| **SSE Active Clients** | `18 klien` | Fixed Mock Telemetri | `'18 klien'` / `'18 Klien Aktif'` |
| **Redis Hit Ratio** | `88.6%` | `$cacheStats['hit_ratio']` | `$cacheStats['hit_ratio'] ?? '88.6'` |
| **Redis Hits / Misses** | `18,492 / 2,380` | `$cacheStats['hits']`, `misses` | `number_format($cacheStats['hits'] ?? 18492)` |
| **Host CPU / RAM / Disk** | `62% / 5.8GB / 57.4GB` | `$hostResources`, `$diskUsedPercent` | Menggunakan array `$hostResources` dengan fallback |
| **Storage Subsystem Ping** | `1.1 ms` | `$isWritable` | `$isWritable ? '1.1 ms' : 'Read-Only'` |
| **Failed Jobs / Queue** | `1.2k Jobs` | `$failedJobsCount` | `$failedJobsCount === 0 ? '1.2k Jobs' : $failedJobsCount . ' Gagal'` |
| **Tabel Rute Endpoint** | Array entri rute | `$routeMetrics` | Di-looping dengan `@forelse($routeMetrics as $metric)` |

---

## 4. Rincian Dekomposisi Tugas (Step-by-Step Task Breakdown)

### TUGAS 1: Standardisasi Scoped CSS & Container Layout
**Lokasi Berkas**: `resources/views/superadmin/health/index.blade.php`  
**Instruksi**:
1. Pastikan tag terluar dari view memiliki wrapper identik dengan mockup:
   ```html
   <div class="health-telemetry-container min-h-screen bg-slate-100 text-slate-800 antialiased font-sans p-3 sm:p-5 lg:p-6 dark:bg-slate-950 dark:text-slate-100 transition-colors duration-200">
   ```
2. Pada blok `@push('styles')`, tambahkan class fallback CSS murni untuk memastikan bahwa jika class utility Tailwind tertentu tidak dikompilasi oleh build pipeline Gentelella, tampilan tetap mempertahankan:
   - `.telemetry-command-grid`: `display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px;` (dengan media queries breakpoint 1023px menjadi 2 kolom, dan 639px menjadi 1 kolom).
   - `.telemetry-kpi-row-grid`: `display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;` (breakpoint 992px menjadi 2 kolom, 480px menjadi 1 kolom).
   - `.telemetry-split-8-4`: `display: grid; grid-template-columns: minmax(0, 8fr) minmax(0, 4fr); gap: 16px;` (breakpoint 1023px menjadi 1 kolom).
   - `.telemetry-split-6-6`: `display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 16px;` (breakpoint 1023px menjadi 1 kolom).
3. Terapkan CSS rule khusus Chromium SVG protection:
   ```css
   .health-telemetry-container svg:not([width]) {
       width: 20px;
       height: 20px;
       max-width: 100%;
   }
   ```

---

### TUGAS 2: Implementasi Header Toolbar & Live Telemetry Controls (Section 1)
**Lokasi Berkas**: `resources/views/superadmin/health/index.blade.php`  
**Instruksi**:
1. Buat elemen `<header class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between border-b border-slate-200 pb-4 dark:border-slate-800">`.
2. Sisi Kiri:
   - Logo Badge: Box 40x40px (`h-10 w-10`), rounded-xl, background `#003566`, teks tebal putih "MK", ring-2 `ring-blue-500/20`.
   - Judul Halaman: `Telemetri Kesehatan Infrastruktur` (`text-lg sm:text-xl font-bold tracking-tight text-slate-900 dark:text-white`).
   - Lencana Live Status: Pill rounded-full dengan teks `Live 24ms`, background `bg-emerald-500/10`, teks `text-emerald-600 dark:text-emerald-400`, border `border-emerald-500/30`, disertai dot animasi radar ping hijau (`animate-ping`).
   - Subtitle Identitas Server: `magkom-prod-01 • Sleman DC` menggunakan warna token `#01347C` (mode terang) atau `dark:text-blue-400`.
3. Sisi Kanan (Controls Strip):
   - Indikator Kanal: Pill `hidden md:inline-flex items-center gap-2 rounded-lg border border-cyan-500/30 bg-cyan-500/10 px-2.5 py-1.5 text-xs font-mono font-semibold text-cyan-700 dark:text-cyan-300` dengan pulsing cyan dot dan teks `/realtime/stream`.
   - Pemilih Rentang Waktu (Time Window): Container rounded-lg `bg-slate-200/90 dark:bg-slate-800 p-1` dengan 3 tombol (`15m`, `1h`, `24h`). Berikan atribut `data-window="..."` dan id `timeWindowSelector`.
   - Tombol Aksi "Test SSE": Button `id="btnTestSse"` dengan background `#01347C`, hover `#02275D`, icon bolt SVG (16x16px), dan label "Test SSE".

---

### TUGAS 3: Komponen HUD Cockpit Telemetri 6-Metric Grid (Section 2)
**Lokasi Berkas**: `resources/views/components/telemetry/health-hud.blade.php`  
**Instruksi**:
1. Deklarasikan `@props` lengkap dengan nilai default:
   ```php
   @props([
       'slaStatus' => 'ALL NOMINAL',
       'slaPercent' => null,
       'dbLatency' => 0.39,
       'dbDriver' => 'mysql',
       'totalTables' => 0,
       'p95RouteDuration' => 142.0,
       'avgRouteDuration' => 0.0,
       'totalRouteRequests' => 0,
       'audit24h' => 0,
       'totalAudit' => 0,
       'failedJobsCount' => 0,
       'cacheDriver' => 'file',
       'queueDriver' => 'sync',
       'isWritable' => true,
   ])
   ```
2. Kontainer Utama:
   - Rounded 2xl (`rounded-2xl`).
   - Gradien latar: `bg-gradient-to-br from-[#003566] via-[#012d59] to-[#011f3f]`.
   - Border: `border border-blue-800/40`.
   - Bayangan: `shadow-xl`.
   - Efek Pendar Ambient (Ambient Glow): 2 bulatan blur-3xl (`bg-cyan-500/10` di kanan atas, `bg-blue-500/15` di kiri bawah) dengan `pointer-events: none`.
3. Header Cockpit:
   - Shield Icon Box (36x36px) berlatar `bg-emerald-500/20 text-emerald-400 border border-emerald-500/30`.
   - Judul: `Pusat Telemetri Infrastruktur`.
   - Badge Status Sistem: Jika nominal menampilkan `ALL NOMINAL` (emerald), jika degraded menampilkan `DEGRADED` (rose).
   - Indikator SLA Uptime: Pill dengan dot radar ping dan teks `SLA Uptime: 99.98%`.
4. Grid 6 Metrik Komando (`grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 sm:gap-4`):
   - **Tile 1 (Status Sistem)**: Nilai `100% OK`, sub-status `0 Gangguan`, footer `Integritas: Lulus`.
   - **Tile 2 (DB Ping)**: Nilai `{{ $dbLatency > 0 ? $dbLatency : '0.39' }} ms`, badge `{{ strtoupper($dbDriver) }}`, sub-status `SLO < 5.0ms`, footer `Pool: 12 / 100`.
   - **Tile 3 (P95 Latency)**: Nilai `{{ round($p95RouteDuration) }} ms`, badge `HTTP`, sub-status `Target < 200ms`, footer `Kepatuhan: 99.8%`.
   - **Tile 4 (SSE Ping)**: Container aksen cyan `bg-cyan-950/40 border-cyan-500/40`, nilai `1.2 ms` dengan radar ping cyan, sub-status `18 Klien Aktif`, footer `Backlog: 0 msg`.
   - **Tile 5 (Throughput)**: Nilai `141 rps`, badge `Trafik`, sub-status `Puncak: 210 rps`, footer `Total: 8.4k rpm`.
   - **Tile 6 (Error Budget)**: Nilai `{{ $slaPercent ?? '99.98' }}%`, badge `SLA`, sub-status `Burn: 0.01x`, footer `Sisa: 43m 12s`.
5. Strip Status Bawah:
   - Green dot: `6 Klaster Aktif (MySQL, Redis, SSE, Storage, Cron, Audit)`.
   - Metadata: `Sinkron: 1s • #98F-24A`.

---

### TUGAS 4: Baris 4 Kartu KPI Kunci (Section 3)
**Lokasi Berkas**: `resources/views/superadmin/health/index.blade.php` (atau diekstrak rapi)  
**Instruksi**:
1. Buat grid 4 kolom: `<div class="mb-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 telemetry-kpi-row-grid">`.
2. Setiap kartu berlatar `bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 shadow-xs hover:shadow-sm transition-all`:
   - **Card 1 (Uptime 30 Hari)**:
     - Header: Label "Uptime 30 Hari" + Badge emerald "Normal".
     - Value: `{{ $slaPercent !== null ? $slaPercent . '%' : '99.98%' }}`.
     - Sparkline SVG: Wajib tepat `viewBox="0 0 80 24" width="80" height="24" class="h-6 w-20 text-emerald-500"` dengan path garis hijau.
     - Footer: `0 insiden tercatat`.
   - **Card 2 (HTTP P95 Latency)**:
     - Header: Label "HTTP P95 Latency" + Text pill `< 200ms SLO`.
     - Value: `{{ number_format((float) $p95RouteDuration, 1) }} ms`.
     - Sparkline SVG: Wajib tepat `viewBox="0 0 80 24" width="80" height="24" class="h-6 w-20 text-blue-500"` dengan path gelombang biru.
     - Footer: `Maks: {{ (int) $maxRouteDuration }}ms` (fallback: `Maks: 241ms`).
   - **Card 3 (Volume Permintaan)**:
     - Header: Label "Volume Permintaan" + Text pill `140 rps`.
     - Value: `{{ number_format($totalRouteRequests) }} rpm` (fallback: `8,432 rpm`).
     - Histogram SVG: Wajib tepat `viewBox="0 0 80 24" width="80" height="24" class="h-6 w-20 text-indigo-500"` dengan 8 elemen `<rect>` bertingkat.
     - Footer: `94% GET • 6% POST`.
   - **Card 4 (SSE Stream Mandiri)**:
     - Border & Latar khusus: `border border-cyan-300/80 bg-cyan-50/40 dark:border-cyan-800 dark:bg-cyan-950/30`.
     - Header: Label "SSE Stream Mandiri" + Badge `1.2ms`.
     - Value: `18 klien`.
     - Equalizer Cluster: 4 batang vertikal rounded (`bg-cyan-500 animate-pulse`) dengan tinggi bervariasi (`h-4`, `h-6`, `h-3`, `h-5`) dan staggered delay (`0ms`, `75ms`, `150ms`, `100ms`).
     - Footer: `Buffer backlog: 0`.

---

### TUGAS 5: Split Grid 8:4 - Kurva Latensi & Redis Donut Suite (Section 4)
**Lokasi Berkas**: 
- Kiri (8 Kolom): `resources/views/superadmin/health/index.blade.php`
- Kanan (4 Kolom): `resources/views/components/telemetry/cache-donut.blade.php`

**Instruksi Kolom Kiri (Grafik Latensi 8 Kolom)**:
1. Container kartu berlatar putih/gelap dengan header toolbar:
   - Pulsing dot cyan + Judul `Grafik Latensi HTTP & SSE`.
   - Tombol filter mode: `Semua` (aktif dengan background `#01347C` teks putih), `P95`, dan `SSE`.
2. Canvas & Wave Container (`bg-[#030712] p-4 border border-slate-800 rounded-xl relative overflow-hidden`):
   - Garis ambang batas (Thresholds Grid Overlay) bertingkat:
     - `300ms` (dashed rose border)
     - `200ms SLO` (dashed amber border)
     - `100ms` (dashed emerald border)
     - `0ms` (dashed slate border)
   - Laser Scanner Overlay: Efek pemindaian animasi horizontal (`.telemetry-scanner-laser` dan `.telemetry-scanner-line`).
   - SVG Wave & HTML5 Canvas Parity:
     - Tampilkan elemen `<canvas id="routeLatencyCanvas" height="280"></canvas>` untuk rendering live data interaktif via JS.
     - Sediakan SVG vector curves fallback dengan linearGradient (`p50GradLive`, `p95GradLive`, `sseGlowLive`), spike callout badge merah `241ms` di koordinat (270, 58), dan stream head pulsing dot di ujung kurva.
   - Timeline baris bawah: `12:00`, `12:05`, `12:10`, `12:15`, `12:20` (cyan).
   - Trace Legend: Bulatan P50 (24ms), P95 (142ms), SSE (1.2ms), dan teks persentase kepatuhan SLO (`99.8%`).

**Instruksi Kolom Kanan (`<x-telemetry.cache-donut>`)**:
1. **Card 1: Cache Redis**:
   - Header: Ikon Redis (rose) + teks "Cache Redis" + badge "Optimal" dengan ping dot.
   - Radial Gauge SVG (112x112px, `-rotate-90`):
     - Background dashed circle (`r="46"`).
     - Track circle (`r="38"`).
     - Miss circle (`url(#missGradient)`).
     - Hit progress circle (`url(#redisGradient)`, stroke-width 8.5, stroke-dashoffset sesuai hit ratio 88.6%).
     - Jarum needle dengan ping dot hijau di koordinat rotasi aktif.
     - Teks di tengah: `88.6%` (font-extrabold) dan label `HIT RATIO` (hijau).
   - Micro Breakdown Progress Bars:
     - Hits: `18,492` dengan bar gradien hijau-teal (88.6%).
     - Misses: `2,380` dengan bar slate (11.4%).
     - Metadata RAM: `64.2 / 128 MB` (sky blue).
     - Metadata Keys: `1,420 Tagged`.
   - Footer: `Redis 7.2` dan `0 Evictions`.
2. **Card 2: Host Resources**:
   - Header: `Host` dan subtitle `4 vCPU / 16GB`.
   - 4 Bar Kemajuan (Progress Meters):
     - CPU Load: `62%` (bar warna teal).
     - RAM System: `5.8 / 16 GB` (bar warna `#01347C`, 36%).
     - Disk /berkas: `57.4 / 100 GB` (bar warna emerald, 57%).
     - DB Pool: `12 / 100` (bar warna amber, 12%).

---

### TUGAS 6: Split Grid 6:6 - Klaster Sub-Sistem & Aksi Pemeliharaan (Section 5)
**Lokasi Berkas**:
- Kiri (6 Kolom): `resources/views/superadmin/health/index.blade.php`
- Kanan (6 Kolom): `resources/views/components/telemetry/maintenance-suite.blade.php`

**Instruksi Kolom Kiri (Klaster Sub-Sistem)**:
1. Container kartu dengan header: `Klaster Sub-Sistem` dan badge `6/6 OPERASIONAL`.
2. Grid 6 tile (`grid grid-cols-2 sm:grid-cols-3 gap-2.5 font-mono text-[11px]`):
   - **MySQL**: Dot hijau, latensi `{{ $dbLatency > 0 ? $dbLatency : '0.39' }} ms`.
   - **Storage**: Dot hijau (atau merah jika `!$isWritable`), status `{{ $isWritable ? '1.1 ms' : 'Read-Only' }}`.
   - **SSE Stream**: Container aksen cyan, dot cyan pulse, status `18 Klien`.
   - **Job Queue**: Dot hijau, status `{{ $failedJobsCount === 0 ? '1.2k Jobs' : $failedJobsCount . ' Gagal' }}`.
   - **Cron**: Dot hijau, status `5m Tick`.
   - **Audit Vault**: Dot hijau, status `Sealed`.

**Instruksi Kolom Kanan (`<x-telemetry.maintenance-suite>`)**:
1. Header: Ikon petir box `#003566`, judul `Aksi Pemeliharaan Proaktif`, badge `SuperAdmin`.
2. Grid 4 Kartu Aksi (`grid grid-cols-1 sm:grid-cols-2 gap-3`):
   - **Aksi 1: Security Sweep**:
     - Ikon shield rose, badge `AUTH`.
     - Form: `<form action="{{ route('superadmin.health.security-sweep') }}" method="POST">`
     - Button: `<button type="submit" class="bg-rose-600 hover:bg-rose-700">Sweep</button>`
     - Dialog Konfirmasi: `"Jalankan pembersihan higienitas keamanan sistem sekarang? Token kedaluwarsa, berkas arsip sementara (>2 jam), dan pendaftar unverified (>72 jam) akan dibersihkan."`
   - **Aksi 2: InnoDB Vacuum**:
     - Ikon database biru, badge `DB`.
     - Form: `<form action="{{ route('superadmin.health.optimize-db') }}" method="POST">`
     - Button: `<button type="submit" class="bg-[#01347C] hover:bg-[#02275D]">Vacuum</button>`
     - Dialog Konfirmasi: `"Jalankan optimasi dan defragmentasi basis data sekarang? Perintah VACUUM/OPTIMIZE akan dijalankan pada tabel-tabel utama."`
   - **Aksi 3: Purge Cache**:
     - Ikon trash/cleanup emerald, badge `REDIS`.
     - Form: `<form action="{{ route('superadmin.health.clear-cache') }}" method="POST">`
     - Button: `<button type="submit" class="bg-emerald-600 hover:bg-emerald-700">Purge</button>`
     - Dialog Konfirmasi: `"Bersihkan seluruh cache aplikasi, views, konfigurasi, dan rute sekarang? Data cache akan di-refresh otomatis."`
   - **Aksi 4: Storage Probe**:
     - Ikon file/folder cyan, badge `I/O`.
     - Form: `<form action="{{ route('superadmin.health.test-storage') }}" method="POST">`
     - Button: `<button type="submit" class="bg-cyan-600 hover:bg-cyan-700">Probe</button>`
     - Dialog Konfirmasi: `"Jalankan uji integritas storage baca/tulis berkas sementara sekarang?"`
3. Footer: Metadata `Terakhir: 4m lalu` (dot hijau) dan `Timeout: 30s`.

---

### TUGAS 7: Tabel Telemetri Rute Endpoint (Section 6)
**Lokasi Berkas**: `resources/views/components/telemetry/endpoint-table.blade.php`  
**Instruksi**:
1. Header Card:
   - Green dot + Judul `Aktivitas Endpoint & Telemetri Rute`.
   - Input Filter: `<input type="text" id="endpointTableFilter" placeholder="Filter..." class="w-32 sm:w-40 rounded-lg border border-slate-300 bg-slate-50 px-2.5 py-1 text-xs font-mono dark:border-slate-700 dark:bg-slate-800 dark:text-white" />`.
2. Struktur Tabel:
   - Header `<thead>`: Background `#003566`, teks putih uppercase font-bold 9px tracking-wider: `WAKTU`, `METHOD`, `ENDPOINT`, `STATUS`, `LATENSI`, `MEMORI`.
   - Kolom:
     - `WAKTU`: Format `12:20:00` (text-slate-500).
     - `METHOD`: Semantic color (GET = blue/cyan, POST = emerald, DELETE = rose).
     - `ENDPOINT`: Teks URI path. Jika berisi `realtime/stream`, beri style font-bold warna cyan.
     - `STATUS`: Center text. 200 OK (emerald), 302 REDIR (amber), 4xx/5xx (rose).
     - `LATENSI`: Right align font-bold. Hijau jika normal, rose jika > 200ms, cyan jika stream.
     - `MEMORI`: Right align dalam satuan MB dengan 1 desimal.
3. Looping Data: Gunakan `@forelse($routeMetrics as $metric)` dan sediakan 5 baris mockup fallback yang identik dengan desain HTML user jika data kosong!

---

### TUGAS 8: Panel Live Syslog & Aksesibilitas WCAG 2.1 AA (Section 7)
**Lokasi Berkas**: `resources/views/superadmin/health/index.blade.php`  
**Instruksi**:
1. Buat kontainer panel: `<div class="rounded-xl border border-slate-200 bg-white p-4 shadow-xs dark:border-slate-800 dark:bg-slate-900 mb-5">`.
2. Header: Judul `Log Realtime` dan subtitle `Syslog`.
3. Daftar Entri Log (`#syslogList`):
   - **Item 1 (SSE Stream)**: Background tint cyan (`bg-cyan-50/50 dark:bg-cyan-950/30`), badge `SSE` cyan, teks `18 listeners (1.2ms)`.
   - **Item 2 (Latency Spike)**: 
     - > [!IMPORTANT]
     - **WCAG 2.1 AA Contrast Rule**: Badge `WARN` **HARUS** menggunakan teks abu-abu gelap `#212529` di atas latar kuning/amber `#FEF3C7` (border `#FCD34D`). DILARANG menggunakan teks putih di atas kuning karena gagal kontras rasio 4.5:1.
     - Label `LATENCY_SPIKE` berwarna amber `text-amber-700 dark:text-amber-400`.
     - Keterangan `/health-monitor 241ms (pulih)`.
   - **Item 3 (Cron Telemetry)**: Badge `CRON` biru (`bg-blue-100 text-blue-800`), teks `128 routes (12ms)`.

---

### TUGAS 9: Client-Side Interactive Engine (JavaScript)
**Lokasi Berkas**: Bagian `@push('scripts')` di `resources/views/superadmin/health/index.blade.php`  
**Instruksi**:
1. **Filter Pencarian Real-Time**:
   - Pasang event listener `input` pada `#endpointTableFilter`.
   - Lakukan pencarian case-insensitive terhadap seluruh baris `.route-table-row`.
   - Sembunyikan baris yang tidak cocok dengan `row.style.display = 'none'`, tampilkan yang cocok dengan `row.style.display = ''`.
2. **Tombol "Test SSE"**:
   - Pasang click listener pada `#btnTestSse`.
   - Ketika diklik, lakukan simulasi paket injeksi: tambahkan item baru ke array telemetri, picu re-render kanvas, perbarui `#canvasLastUpdate`, dan lakukan animasi getar/flash halus pada indikator status.
3. **Pemberitahuan Waktu Nyata via SSE (Server-Sent Events)**:
   - Inisialisasi koneksi `new EventSource('/realtime/stream')`.
   - Dengarkan event `magkom:telemetry-route`.
   - Saat event tiba, prepend elemen log baru ke `#syslogList` dan perbarui kurva kanvas tanpa me-reload halaman.

---

## 5. Checklist Verifikasi & Quality Gate (Definition of Done)

Sebelum menyatakan tugas selesai, jalankan checklist verifikasi berikut:

- [ ] **Visual Parity**: Buka halaman di browser dan bandingkan side-by-side dengan mockup HTML. Pastikan warna gradien, border, radius, dan tipografi 100% identik.
- [ ] **Dual-Theme Check**: Uji dalam mode terang (Light Mode) dan mode gelap (Dark Mode `[data-theme="dark"]`). Pastikan semua kartu, teks, dan tabel terbaca jelas tanpa teks mati.
- [ ] **No Container Blowout**: Periksa semua SVG pada resolusi mobile (375px), tablet (768px), dan desktop (1440px). Pastikan tidak ada SVG yang melar melebihi dimensinya.
- [ ] **Form Security**: Cek kode HTML yang dirender untuk 4 tombol aksi pemeliharaan. Pastikan masing-masing berada di dalam form ber-`@csrf` dan memicu popup konfirmasi sebelum terkirim.
- [ ] **WCAG 2.1 AA**: Pastikan lencana `WARN` di panel syslog menggunakan teks gelap `#212529` di atas latar `#FEF3C7`.
- [ ] **Automated Tests**: Jalankan pengujian backend:
  ```powershell
  php artisan test --filter=RoutePerformanceTelemetryTest
  ```
  Semua test **WAJIB** lulus hijau (100% pass) tanpa regresi.

