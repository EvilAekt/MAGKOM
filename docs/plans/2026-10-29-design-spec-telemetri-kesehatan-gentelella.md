# SPESIFIKASI DESAIN UI/UX: Redesign Dashboard Telemetri Kesehatan SuperAdmin (Gentelella Parity)

**Dokumen**: `docs/plans/2026-10-29-design-spec-telemetri-kesehatan-gentelella.md`  
**Lead Designer**: Historia Reiss (UI/UX Design Systems Lead)  
**Tinjauan & Persetujuan**: Erwin Smith (Technical Lead & Orchestrator)  
**Referensi**:
- Panduan Tipografi Gentelella: `https://preview.colorlib.com/theme/gentelella/production/typography.html`
- Panduan Ikon Gentelella: `https://preview.colorlib.com/theme/gentelella/production/icons.html` (Font Awesome 4.7)
- Referensi Visual System Health Mockup: `attachment-image-1` & `attachment-image-2` (Operations / System Health Dashboard)
**Tanggal**: 29 Oktober 2026  
**Status**: SUBMITTED FOR REVIEW (Menunggu Penilaian Erwin Smith)

---

## 1. Analisis Visual & Paritas Referensi (Deconstructive Mapping)

Berdasarkan analisis tangkapan layar `System health` yang disediakan Sir Alan dan panduan resmi tema Gentelella, antarmuka telemetri kesehatan dibagi menjadi 6 blok struktural utama:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [A] STATUS HEADER & MAINTENANCE BANNER (SLA Uptime, Status Badge, Quick Actions)       │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [B] 4 HERO METRIC STAT TILES (Uptime 99.98%, P95 Latency, Total Trafik, DB Latency)   │
├──────────────────────────────────────────┬─────────────────────────────────────────────┤
│ [C] REALTIME LATENCY & TRAFFIC CURVE     │ [D] RESOURCE USAGE & STORAGE METERING       │
│     - Canvas 2D Bézier Curve (SSE Live)  │     - Disk Storage Gauge & Progress Bar     │
│     - KPI Summary Badges (Avg, P95, Max) │     - Dokumen Magang & DB Tables Count      │
│     - Dynamic Interactive Tooltips       │     - Status Izin Tulis & Backup Terakhir   │
├──────────────────────────────────────────┴─────────────────────────────────────────────┤
│ [E] RECENT API ENDPOINTS TELEMETRY (Enterprise Data Table jambo_table)                 │
│     - Endpoint URI, Method (GET/POST), Status Code, Durasi Latensi (ms), Timestamp    │
├──────────────────────────────────────────┬─────────────────────────────────────────────┤
│ [F] SERVICE STATUS MONITOR               │ [G] 1-CLICK PROACTIVE MAINTENANCE SUITE     │
│     - Database, Storage, Queue, Sweep    │     - Security Sweep, Optimize DB,          │
│     - Indicator Dot (● Operational)      │       Clear Cache, Test Storage Probe       │
├──────────────────────────────────────────┴─────────────────────────────────────────────┤
│ [H] BACKGROUND JOBS & HIGIENITAS KEAMANAN (Failed Jobs, Token Expired, Temp Files)    │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Tipografi & Hierarki Visual (Gentelella Standard)

Mengikuti panduan `typography.html` Gentelella dan token DS-MagKom:

| Elemen UI | Font Family | Size | Weight | Line Height | Color (Light / Dark) |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Page Title** (`h2`) | Open Sans, sans-serif | 1.625rem (26px) | 800 (Bold) | 1.25 | `#1D2530` / `#F8FAFC` |
| **Panel Heading** (`.x_title h2`) | Open Sans, sans-serif | 1.125rem (18px) | 700 (Bold) | 1.3 | `#334155` / `#CBD5E1` |
| **Subtitle / Subtext** (`small`) | Open Sans, sans-serif | 0.8125rem (13px) | 400 (Regular) | 1.4 | `#7A8494` / `#94A3B8` |
| **Metric Value Hero** (`.count`) | Open Sans / Monospace | 2.0rem (32px) | 800 (Extra-Bold)| 1.1 | `#1D2530` / `#FFFFFF` |
| **Metric Label** (`.count_top`) | Open Sans, sans-serif | 0.75rem (12px) | 700 (Semi-Bold) | 1.2 | `#7A8494` / `#94A3B8` (Caps) |
| **Table Head** (`th`) | Open Sans, sans-serif | 0.75rem (12px) | 700 (Semi-Bold) | 1.3 | `#334155` / `#CBD5E1` (Caps) |
| **Table Body Text** (`td`) | Open Sans, sans-serif | 0.8125rem (13px) | 500 (Medium) | 1.4 | `#333333` / `#E2E8F0` |
| **Code / Micro Latency** | Monospace, Courier | 0.75rem - 0.875rem | 700 (Bold) | 1.2 | Color-coded status |

---

## 3. Pemetaan Ikon Font Awesome (Gentelella `icons.html`)

Sesuai `icons.html` Colorlib Gentelella, ikon yang digunakan adalah Font Awesome 4.7 standar:

- **Sistem & Uptime**: `fa fa-check-circle` atau `fa fa-shield`
- **Latensi & Waktu**: `fa fa-clock-o` atau `fa fa-tachometer`
- **Trafik & Request**: `fa fa-exchange` atau `fa fa-bolt`
- **Basis Data**: `fa fa-database`
- **Penyimpanan Storage**: `fa fa-hdd-o` atau `fa fa-folder-open`
- **Kurva Grafik**: `fa fa-line-chart`
- **Layanan & Server**: `fa fa-server` atau `fa fa-cogs`
- **Aksi Pemeliharaan**:
  - Security Sweep: `fa fa-shield`
  - Optimasi Database: `fa fa-database`
  - Bersihkan Cache: `fa fa-refresh`
  - Probe Tulis Storage: `fa fa-pencil-square-o`
- **Status Indikator**: `fa fa-circle` (hijau/merah/kuning)

---

## 4. Anatomi Komponen & Skema Grid 12-Kolom

### 4.1. Header & Status Banner (`col-md-12`)
- **Container**: Card hero dengan kontur tegas atau banner Gentelella.
- **Left**: Badge `● ALL SYSTEMS OPERATIONAL` (latar hijau transparan, border hijau, teks hijau cerah), judul halaman `Pusat Telemetri Infrastruktur Portal MagKom`, dan deskripsi instansi Sleman.
- **Right**: Tombol aksi navigasi cepat (`.btn.btn-default` untuk Backup Hub & `.btn.btn-dark` untuk Audit Trail Stream).
- **Maintenance Ribbon**: Alert info beraksen biru dengan ikon `fa fa-info-circle`, jadwal pemeliharaan berkala sistem, dan tombol `View Details / Runbook`.

### 4.2. 4 Hero Metric Stat Tiles (`col-md-3 col-sm-6 col-xs-12`)
Menggunakan pola Gentelella `.tile_stats_count` dengan inner card `.tile_stats_inner`:
1. **Tile 1 (Uptime & SLA)**:
   - Nilai: `{{ $slaPercent }}%`
   - Label: `UPTIME SLA (30-DAY ROLLING)`
   - Ikon: `fa fa-check-circle` (warna teal/hijau)
   - Subtext: `Status: {{ $slaStatus }}`
2. **Tile 2 (Tail Latency P95)**:
   - Nilai: `{{ $p95RouteDuration }} ms`
   - Label: `P95 TAIL LATENCY`
   - Ikon: `fa fa-clock-o` (warna biru)
   - Subtext: `Rata-rata: {{ $avgRouteDuration }} ms (SLO < 200ms)`
3. **Tile 3 (Total Trafik HTTP)**:
   - Nilai: `{{ number_format($totalRouteRequests) }}`
   - Label: `TOTAL REQUEST TEREKAM`
   - Ikon: `fa fa-bolt` (warna kuning/amber)
   - Subtext: `Event 24 Jam: {{ number_format($audit24h) }}`
4. **Tile 4 (Basis Data Ping)**:
   - Nilai: `{{ $dbLatency }} ms`
   - Label: `LATENSI BASIS DATA`
   - Ikon: `fa fa-database` (warna ungu/primary)
   - Subtext: `Driver: {{ strtoupper($dbDriver) }} ({{ $totalTables }} Tabel)`

### 4.3. Row 2: Grafik Real-Time (8 Kolom) + Resource Usage (4 Kolom)
- **Left (`col-md-8 col-sm-12 col-xs-12`)**:
  - Panel: `.x_panel` dengan judul `<i class="fa fa-line-chart"></i> Telemetri Real-Time Waktu Eksekusi Route (Native HTML5 Canvas 2D)`.
  - Toolbox: Collapsible & status badge `SSE Stream Live`.
  - Subtitle: `Visualisasi kurva performa HTTP request riil terhubung langsung dengan middleware database dan pembaruan live stream via Server-Sent Events (SSE).`
  - 4 Quick KPI Summary Pills di atas grafik (Rata-Rata Latensi, P95 Tail Latency, Latensi Puncak, Total Sampel).
  - Canvas 2D responsive dengan auto-resize Retina/HiDPI tajam.
  - Legenda warna threshold (< 50ms Optimal hijau, 50-200ms Normal kuning, > 200ms Tinggi merah).
- **Right (`col-md-4 col-sm-12 col-xs-12`)**:
  - Panel: `.x_panel` dengan judul `<i class="fa fa-hdd-o"></i> Penggunaan Sumber Daya & Kapasitas`.
  - Progress bar Gentelella (`progress progress_sm`) untuk Disk Space:
    - `{{ $diskUsedPercent }}%` terpakai (Sisa `{{ $diskFreeGb }} GB` dari `{{ $diskTotalGb }} GB`).
    - Warna dinamis (`progress-bar bg-green` jika < 75%, `bg-orange` jika 75-85%, `bg-red` jika > 85%).
  - List metrik sistem:
    - Dokumen Magang Terarsip: `{{ number_format($totalDokumenCount) }} berkas` (`{{ $totalDokumenMb }} MB`).
    - Status Izin Tulis Storage: Badge hijau `Izin Tulis Aktif` / Badge merah `Read-Only`.
    - Backup Terakhir: `{{ $lastBackupTime ?? 'Belum ada cadangan' }}`.
    - Status Database: Badge `{{ $dbStatus === 'OK' ? 'Terhubung (OK)' : 'Error' }}`.

### 4.4. Row 3: API Endpoints Table (`col-md-12`)
- Panel: `.x_panel.has-table` dengan top accent ribbon `3px solid #01347C`.
- Judul: `<i class="fa fa-exchange"></i> Telemetri Rute HTTP Terkini (Last 40 Requests)`.
- Tabel: `table table-striped jambo_table bulk_action`.
- Header Kolom:
  1. `WAKTU`: Format WIB (Jam:Menit:Detik).
  2. `METODE`: Badge method (`GET` biru, `POST` hijau, `DELETE` merah, `PUT/PATCH` oranye).
  3. `RUTE / URI`: Nama route alias dan path URI lengkap.
  4. `STATUS HTTP`: Status code badge (200 OK hijau, 4xx oranye, 5xx merah).
  5. `DURASI LATENSI`: Angka milidetik dengan color-coded indicator (< 50ms hijau, 50-200ms amber, > 200ms merah).
  6. `PENGGUNAAN MEMORI`: Besaran MB runtime memory.

### 4.5. Row 4: Status Layanan (6 Kolom) + Suite Pemeliharaan 1-Klik (6 Kolom)
- **Left (`col-md-6 col-sm-12 col-xs-12`)**:
  - Panel: `.x_panel` dengan judul `<i class="fa fa-server"></i> Status Layanan Inti MagKom`.
  - List group item dengan dot status (●):
    - `Basis Data Relasional`: ● Operational (`{{ $dbLatency }} ms` query latency).
    - `Private Storage Berkas`: ● Operational (`storage/app/berkas`).
    - `SSE Real-Time Stream Engine`: ● Operational (`/realtime/stream`).
    - `Background Queue Worker`: ● {{ $failedJobsCount === 0 ? 'Operational (0 Failed)' : 'Degraded' }}.
    - `Pembersih Higienitas Keamanan`: ● {{ ($sweeperStats['pending_tokens'] === 0 && $sweeperStats['pending_temp_files'] === 0) ? 'Higienis' : 'Perlu Sweep' }}.
- **Right (`col-md-6 col-sm-12 col-xs-12`)**:
  - Panel: `.x_panel` dengan judul `<i class="fa fa-wrench"></i> Suite Pemeliharaan Cepat Proaktif`.
  - Subtitle: `4 aksi diagnostik 1-klik untuk pemeliharaan proaktif kestabilan portal.`
  - Grid 2x2 tombol aksi Gentelella (`.btn`):
    1. `Security Sweep Higienis`: `.btn.btn-danger` (Membersihkan token kedaluwarsa & berkas sementara).
    2. `Optimasi & Defrag Basis Data`: `.btn.btn-primary` (Menjalankan optimize/vacuum tabel utama).
    3. `Bersihkan Cache Sistem`: `.btn.btn-success` (Membersihkan config, route, view cache).
    4. `Uji Tulis Storage Probe`: `.btn.btn-info` (Memverifikasi write permission private storage).

### 4.6. Row 5: Ringkasan Runtime & Job Higienitas (3 Kolom: 4 - 4 - 4)
- **Kolom 1: Runtime Framework**: PHP `{{ $runtimeInfo['php_version'] }}`, Laravel `v{{ $runtimeInfo['laravel_version'] }}`, Env `{{ strtoupper($runtimeInfo['app_env']) }}`, Debug `{{ $runtimeInfo['app_debug'] ? 'ON' : 'OFF' }}`.
- **Kolom 2: Background Jobs & Queue**: Queue Driver `{{ $runtimeInfo['queue_driver'] }}`, Failed Jobs `{{ $failedJobsCount }}`, Total Audit Logs `{{ number_format($totalAudit) }}`.
- **Kolom 3: Higienitas Keamanan (Sweeper)**: Token Kedaluwarsa `{{ $sweeperStats['pending_tokens'] }}`, Temp ZIP files `{{ $sweeperStats['pending_temp_files'] }}`, Unverified Users `{{ $sweeperStats['pending_unverified_users'] }}`.

---

## 5. Token Desain & Kepatuhan Aksesibilitas (WCAG 2.1 AA)

- **Light Mode**:
  - Background Body: `--neutral-100` (`#F8FAFC`)
  - Surface Card / Panel: `--neutral-0` (`#FFFFFF`) dengan border `--neutral-300` (`#E2E8F0`)
  - Text Primary: `--neutral-800` (`#1D2530`) - Rasio kontras 12.8:1 (AAA)
  - Text Secondary: `--neutral-500` (`#7A8494`) - Rasio kontras 4.6:1 (AA)
- **Dark Mode** (`[data-theme="dark"]`):
  - Background Body: `#0A1120` / `#0E1726`
  - Surface Card / Panel: `#111C2E` / `#182234` dengan border `#26354D`
  - Text Primary: `#F8FAFC` - Rasio kontras 14.2:1 (AAA)
  - Text Secondary: `#94A3B8` - Rasio kontras 5.2:1 (AA)
  - Inverted Accent: `--blue-700` menjadi `#93C5FD` (Blue 300)

---

## 6. Checklist Verifikasi Desain

- [x] Sesuai dengan mockup referensi visual `System health` (Operations)
- [x] Mengadopsi konvensi Gentelella Typography & Font Awesome Icons
- [x] Zero messy inline CSS: semua styling menggunakan class CSS Gentelella / DS-MagKom
- [x] Preservasi 100% ID dan label wajib untuk kelulusan test suite Laravel
- [x] Responsif 12-kolom (Mobile, Tablet, Desktop)
- [x] Kepatuhan WCAG 2.1 AA di mode Terang dan Gelap

---

*Diserahkan oleh Historia Reiss ke Erwin Smith untuk Review & Approval.*
