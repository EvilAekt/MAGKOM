# Senior Developer Technical Directive & PRD: Paritas Visual 100% Telemetri Kesehatan SuperAdmin (Target v7 Reference)

**Dokumen**: `docs/plans/2026-10-31-senior-dev-task-telemetry-v7-pixel-parity-prd.md`  
**Author**: Senior Lead Software Engineer & System Architect  
**Target Pelaksana**: Junior Developer & AI Agent (Gemini 3.8 Flash / Claude Haiku)  
**Tingkat Kesulitan**: L3 (UI/UX Pixel Perfection, High-Density CSS Grid, SVG Waveform Engine, WCAG 2.1 AA)  
**Status**: APPROVED & READY FOR DIRECT IMPLEMENTATION  
**Referensi Visual (SSOT)**: Screenshot Kanvas v7 (`SuperAdmin Telemetri Kesehatan Rede... v7`) yang diunggah oleh User (Images 1-5 & Image Target v7).

---

## 1. Executive Briefing & Post-Mortem

### 1.1 Masalah Nyata (*The "Tailwind Illusion" Incident*)
User membandingkan hasil render di browser (`media_1793460191819_13389628.jpg`) dengan desain mockup v7 (`media_1793460201417_4fb2fac0.png`), dan memberikan feedback:
> *"jujur beda jauh bgt sama tuh"*

Pada screenshot aktual di browser user:
- Teks "MK" hanya teks mentah tanpa kotak biru dongker melengkung.
- Header, HUD, kartu KPI, grafik, klaster, aksi, tabel, dan syslog bertumpuk vertikal ke bawah tanpa warna latar (*background* putih polos), tanpa grid 6-kolom, dan tombol-tombol berbentuk tombol abu-abu default browser.

### 1.2 Akar Masalah Arsitektur (*Root Cause Analysis*)
- Arsitektur antarmuka MagKom menggunakan **Gentelella Bootstrap 3** yang di-*bundle* melalui asset static (`public/css/gentelella-theme.css` dan `public/css/dashboard-cockpit-unified.css`).
- Layout `layouts/dashboard.blade.php` **TIDAK MENGGUNAKAN** Vite runtime compiler `@vite(['resources/css/app.css'])`, dan file `resources/css/app.css` adalah file kosong.
- Blade view yang dibuat sebelumnya mengandalkan ratusan utility class Tailwind (`flex`, `grid`, `grid-cols-6`, `bg-[#003566]`, `rounded-2xl`, `p-4`, `text-emerald-400`, dll.).
- **Hasilnya**: Di runtime browser, seluruh class Tailwind tersebut dievaluasi sebagai *unmatched selectors* (0 properti CSS). Elemen HTML jatuh kembali ke styling dasar user-agent browser.

### 1.3 Solusi Arsitektur (*The Architectural Solution*)
Sesuai dengan spesifikasi `.sokudo/specs/superadmin-telemetry-canvas-parity/design.md` **Principle 2: Zero Runtime Utility Dependency**:
> *"Lingkungan Gentelella Bootstrap 3 tidak mengompilasi utility Tailwind secara dinamis; antarmuka mengandalkan scoped CSS terisolasi dan variabel desain DS-MagKom."*

Kita wajib:
1. Membangun sebuah stylesheet scoped murni: `public/css/telemetry-cockpit-v7.css` yang mendefinisikan seluruh aturan CSS semantik dan class shims yang dibutuhkan.
2. Memuat stylesheet tersebut di `@push('styles')` di `resources/views/superadmin/health/index.blade.php`.
3. Memastikan semua dimensi SVG eksplisit (`width` dan `height`) agar tidak membengkak di Chromium.
4. Menjaga kepatuhan 100% terhadap test otomatis (`RoutePerformanceTelemetryTest`).

---

## 2. Bounded Context & Aturan Ketat untuk Junior / AI

> [!CAUTION]
> **ATURAN MUTLAK (GUARDRAILS):**
> 1. **Dilarang Menyentuh Backend**: File Controller (`HealthMonitorController.php`), Model (`RouteMetric.php`), dan Route (`routes/web.php`) **STRICTLY READ-ONLY**.
> 2. **Semua Props Wajib Memiliki Fallback Default**: Jika database kosong atau query bernilai null, Blade view **TIDAK BOLEH** menghasilkan teks kosong atau layout ambruk. Gunakan ternary fallback (contoh: `{{ $dbLatency > 0 ? number_format($dbLatency, 2) : '0.39' }}`).
> 3. **Semua Tag `<svg>` Wajib Berdimensi Eksplisit**: Tambahkan atribut `width="..." height="..."` serta style `width: ...px; height: ...px;` untuk mencegah bug pembengkakan kontainer (*container blowout*) di Chromium.
> 4. **Semua Form Aksi Wajib Ber-`@csrf`**: Tombol Sweep, Vacuum, Purge, dan Probe wajib dibungkus dalam tag `<form method="POST">` dengan token CSRF dan konfirmasi JavaScript.
> 5. **Test Wajib Lulus 100%**:
>    ```powershell
>    php artisan test --filter=RoutePerformanceTelemetryTest
>    ```

---

## 3. Blueprint 7 Section & Spesifikasi Visual Presisi

### Section 0: Outer Canvas & Floating Card Framing
- **Container**: `.health-telemetry-canvas-card`
  - Latar: `#FFFFFF` (Dark mode: `#0F172A`)
  - Border radius: `24px` (`border-radius: 1.5rem`)
  - Border: `1px solid #E2E8F0` (Dark: `#1E293B`)
  - Padding: `24px 28px` (responsif)
  - Box Shadow: `0 1px 3px rgba(0, 0, 0, 0.05)`
  - Margin: `0 auto`, max-width `1440px`

---

### Section 1: Header Toolbar & Live Controls
- **Sisi Kiri**:
  - Logo Box "MK": `width: 40px; height: 40px; background: #003566; color: #FFFFFF; border-radius: 12px; font-weight: 700; font-size: 13px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 6px -1px rgba(0, 53, 102, 0.2); ring: 2px solid rgba(59, 130, 246, 0.2);`
  - Judul: `Telemetri Kesehatan Infrastruktur` (`font-size: 19px; font-weight: 700; color: #0F172A; letter-spacing: -0.02em;`)
  - Status Pill: `Live 24ms` (`background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #059669; padding: 2px 10px; border-radius: 9999px; font-size: 11px; font-family: monospace; font-weight: 700;`) + radar green ping dot.
  - Subtitle: `magkom-prod-01` (`#01347C`, font-semibold) `• Sleman DC` (`#64748B`, font-mono).
- **Sisi Kanan**:
  - Pill Stream: `background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.3); color: #0e7490; font-family: monospace; font-size: 12px; font-weight: 600; padding: 6px 10px; border-radius: 8px;` + cyan dot.
  - Time Selector: Kotak abu-abu `background: rgba(226, 232, 240, 0.9); padding: 4px; border-radius: 8px;`. Tombol `15m` aktif: `background: #FFFFFF; color: #0F172A; font-weight: 700; box-shadow: 0 1px 2px rgba(0,0,0,0.06);`. Tombol `1h` dan `24h`: `background: transparent; color: #64748B;`.
  - Tombol `Test SSE`: `background: #01347C; color: #FFFFFF; font-weight: 700; padding: 6px 14px; border-radius: 8px; font-size: 12px;` + ikon petir SVG.

---

### Section 2: Unified Command Cockpit Telemetry HUD (Dark Blue Cockpit)
- **Container**:
  - `background: linear-gradient(135deg, #003566 0%, #012d59 50%, #011f3f 100%);`
  - `border: 1px solid rgba(30, 64, 175, 0.4);`
  - `border-radius: 16px;`
  - `padding: 20px 24px;`
  - `box-shadow: 0 10px 25px -5px rgba(0, 31, 63, 0.5);`
  - `position: relative; overflow: hidden;`
- **Ambient Overlays**: Pendar cyan blur di kanan atas dan pendar biru di kiri bawah.
- **Top Cockpit Header**:
  - Shield Icon Box (36x36px): `background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 8px; color: #34d399;`
  - Judul: `Pusat Telemetri Infrastruktur` (teks putih tebal 16px).
  - Badge: `ALL NOMINAL` (`background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); color: #6ee7b7; font-size: 10px; font-mono font-bold;`)
  - Kanan: `• SLA Uptime: 99.98%` (`background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 8px; padding: 4px 10px; font-mono text-xs; color: #cbd5e1;`)
- **Grid 6 Kolom Komando**:
  - `display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px;`
  - Tile 1 (STATUS SISTEM): `border: 1px solid rgba(16, 185, 129, 0.3); background: rgba(255, 255, 255, 0.05);` | `100% OK` hijau emerald | `✔ 0 Gangguan` | Footer: `Integritas: Lulus`.
  - Tile 2 (DB PING): `MYSQL` badge | `0.39 ms` putih | `SLO < 5.0ms` cyan | Footer: `Pool: 12 / 100`.
  - Tile 3 (P95 LATENCY): `HTTP` badge | `142 ms` amber | `Target < 200ms` | Footer: `Kepatuhan: 99.8%`.
  - Tile 4 (SSE PING - Spesial): `background: rgba(8, 47, 73, 0.45); border: 1px solid rgba(6, 182, 212, 0.45);` | `1.2 ms` cyan | `18 Klien Aktif` | Footer: `Backlog: 0 msg`.
  - Tile 5 (THROUGHPUT): `TRAFIK` badge | `141 rps` putih | `Puncak: 210 rps` | Footer: `Total: 8.4k rpm`.
  - Tile 6 (ERROR BUDGET): `SLA` badge | `99.98%` emerald | `Burn: 0.01x` | Footer: `Sisa: 43m 12s`.
- **Bottom Strip**:
  - `• 6 Klaster Aktif (MySQL, Redis, SSE, Storage, Cron, Audit)` di kiri.
  - `Sinkron: 1s • #98F-24A` di kanan.

---

### Section 3: 4 KPI Cards Row
- `display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 20px;`
- Card 1 (Uptime 30 Hari): `99.98%` + badge `Normal` + emerald smooth curve sparkline.
- Card 2 (HTTP P95 Latency): `142.0 ms` + badge `< 200ms SLO` + blue curve with single peak sparkline.
- Card 3 (Volume Permintaan): `8,432 rpm` + badge `140 rps` + 7 indigo vertical histogram bars.
- Card 4 (SSE Stream Mandiri): Special soft cyan background `rgba(236, 254, 255, 0.4)` & border `rgba(103, 232, 249, 0.8)` | `18 klien` + badge `1.2ms` + 4 cyan equalizer animated pulses.

---

### Section 4: Split Grid 8:4 (Latency Wave Curve vs Cache Redis & Host)
- Container: `display: grid; grid-template-columns: 8fr 4fr; gap: 16px; margin-bottom: 20px;`
- **Left 8 Kolom (Grafik Latensi)**:
  - Header: `• GRAFIK LATENSI HTTP & SSE` (cyan dot) + filter tabs `Semua` (navy aktif), `P95`, `SSE`.
  - Dark Box Container: `background: #030712; border: 1px solid #1E293B; border-radius: 12px; height: 280px; position: relative; overflow: hidden;`
  - Horizontal gridlines: `300ms`, `200ms SLO` (amber dashed), `100ms`, `0ms`.
  - Timestamps: `12:00`, `12:05`, `12:10`, `12:15`, `12:20` (cyan highlight).
  - P50 Green Wave + P95 Sky Blue Curve dengan lonjakan dramatis di 12:06 (`241ms` callout badge merah) + laser vertical scanner cyan di 12:20 + denyut baseline SSE.
  - Legend: `• P50: 24ms`, `• P95: 142ms`, `• SSE: 1.2ms`, `SLO: 99.8%`.
- **Right 4 Kolom (Cache Redis & Host)**:
  - Card 1: **CACHE REDIS**: Header icon merah + badge `OPTIMAL` (hijau). Donut radial ring gauge (88.6% HIT RATIO) di kiri + progress bar Hits/Misses & detail RAM/Keys di kanan + footer `Redis 7.2 • 0 Evictions`.
  - Card 2: **HOST**: Header `HOST` + badge `4 vCPU / 16GB`. 4 progress bars: CPU Load (62% cyan), RAM System (5.8/16GB navy), Disk /berkas (57.4/100GB emerald), DB Pool (12/100 amber).

---

### Section 5: Split Grid 6:6 (Klaster Sub-Sistem vs Aksi Pemeliharaan Proaktif)
- Container: `display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;`
- **Left 6 Kolom (Klaster Sub-Sistem)**:
  - Header: `KLASTER SUB-SISTEM` + badge `6/6 OPERASIONAL` (hijau).
  - 3x2 Grid: MySQL (`0.39 ms`), Storage (`1.1 ms`), SSE Stream (`18 Klien` - kotak aksen cyan), Job Queue (`1.2k Jobs`), Cron (`5m Tick`), Audit Vault (`Sealed`).
- **Right 6 Kolom (Aksi Pemeliharaan Proaktif)**:
  - Header: ⚡ `AKSI PEMELIHARAAN PROAKTIF` + badge `SuperAdmin`.
  - 2x2 Grid (4 Aksi Berwarna & Form CSRF):
    1. **Security Sweep**: Ikon perisai rose, badge `AUTH`, tombol merah `Sweep` (`#E11D48`).
    2. **InnoDB Vacuum**: Ikon database biru, badge `DB`, tombol navy `Vacuum` (`#003566`).
    3. **Purge Cache**: Ikon sampah mint, badge `REDIS`, tombol hijau `Purge` (`#059669`).
    4. **Storage Probe**: Ikon dokumen cyan, badge `I/O`, tombol cyan `Probe` (`#0891B2`).
  - Footer: `• Terakhir: 4m lalu` dan `Timeout: 30s`.

---

### Section 6: Section Bawah (Aktivitas Endpoint & Live Syslog)
- **Card 1 (Aktivitas Endpoint & Telemetri Rute)**:
  - Header: `• AKTIVITAS ENDPOINT & TELEMETRI RUTE` + filter input box.
  - Table: Sticky `#003566` Navy Header dengan teks putih tebal (`WAKTU`, `METHOD`, `ENDPOINT`, `STATUS`, `LATENSI`, `MEMORI`).
  - Row 1: `/realtime/stream` (baris cyan).
  - Row 2: `/health` (normal).
  - Row 3: `/superadmin/health-monitor` (`241 ms` merah tebal, latar pink sangat lembut).
  - Row 4: `/login` (POST hijau, 302 REDIR amber).
  - Row 5: `/captcha/image` (normal).
- **Card 2 (Log Realtime / Syslog)**:
  - Header: `LOG REALTIME` + `SYSLOG`.
  - 3 entri monospaced:
    - `12:20:00` | badge `SSE` (cyan) | `/realtime/stream 18 listeners (1.2ms)`
    - `12:18:11` | badge `WARN` (amber `#FEF3C7` teks `#212529`) | `LATENCY_SPIKE /health-monitor 241ms (pulih)`
    - `12:15:00` | badge `CRON` (biru) | `TELEMETRY_AGG 128 routes (12ms)`

---

## 4. Verification & Quality Gates

Junior Developer / AI wajib memverifikasi:
1. File stylesheet dibuat di `public/css/telemetry-cockpit-v7.css`.
2. View di `resources/views/superadmin/health/index.blade.php` memuat stylesheet tersebut via `<link rel="stylesheet">` dan `@push('styles')`.
3. Jalankan suite pengujian:
   ```powershell
   php artisan test --filter=RoutePerformanceTelemetryTest
   ```
   **Kriteria Lolos**: 5 passed, 25 assertions.
4. Jalankan pengujian hak akses SuperAdmin:
   ```powershell
   php artisan test tests/Feature/SecuritySuperAdminAccessControlTest.php
   ```
   **Kriteria Lolos**: 7 passed, 30 assertions.
5. Jalankan AST graph update:
   ```powershell
   graphify update .
   ```
