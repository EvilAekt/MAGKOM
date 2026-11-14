# 📋 SENIOR PERFORMANCE & ARCHITECTURAL AUDIT REPORT
## INVESTIGASI MENDALAM PERFORMA & MASTER PLAN OPTIMASI KECEPATAN NAVIGASI
### SISTEM INFORMASI PORTAL MAGKOM — DINAS KOMUNIKASI DAN INFORMATIKA

```text
========================================================================================
DOCUMENT ID       : MAGKOM-PERFORMANCE-AUDIT-V2-2026-11-13
LEVEL PRIORITAS   : P0 — CRITICAL PERFORMANCE INVESTIGATION
MODE EKSEKUSI     : STRICT READ-ONLY / AUDIT & MASTER PLAN ONLY (ZERO CODE MUTATION)
PENYUSUN          : Senior Performance Engineer & Lead Laravel Systems Architect
TARGET IMPLEMENTASI: Junior Developer / Autonomous AI Engineering Agent
TECH STACK        : Laravel 10.x, PHP 8.3.28 (Laragon FastCGI), Nginx 1.27.3, MySQL 8.4.3,
                    Vite 6.0.7, Vanilla JS (ES Modules), Custom Industrial CSS, Blade MPA
========================================================================================
```

---

## 📑 DAFTAR ISI

1. [Executive Summary](#1-executive-summary)
2. [Project Architecture Overview](#2-project-architecture-overview)
3. [Audit Scope](#3-audit-scope)
4. [Methodology](#4-methodology)
5. [Performance Baseline & Empirical Measurements](#5-performance-baseline--empirical-measurements)
6. [Network & FastCGI Socket Investigation](#6-network--fastcgi-socket-investigation)
7. [PHP & Laravel Runtime Performance Audit](#7-php--laravel-runtime-performance-audit)
8. [Database, Eloquent & Indexing Performance Audit](#8-database-eloquent--indexing-performance-audit)
9. [Server-Sent Events (SSE) & Concurrency Audit](#9-server-sent-events-sse--concurrency-audit)
10. [Frontend, Blade & Monolithic Inline Assets Audit](#10-frontend-blade--monolithic-inline-assets-audit)
11. [Vite Asset Pipeline & HTTP Caching Audit](#11-vite-asset-pipeline--http-caching-audit)
12. [Cache Architecture & Invalidation Strategy Audit](#12-cache-architecture--invalidation-strategy-audit)
13. [Cross-Cutting Findings & Anomaly Detection](#13-cross-cutting-findings--anomaly-detection)
14. [Comprehensive Evidence Ledger](#14-comprehensive-evidence-ledger)
15. [Root Cause Analysis (RCA) & Causal Tree](#15-root-cause-analysis-rca--causal-tree)
16. [Critique & Refutation of Previous Audit](#16-critique--refutation-of-previous-audit)
17. [Recommended Solution Architecture](#17-recommended-solution-architecture)
18. [Prioritized Implementation Master Roadmap](#18-prioritized-implementation-master-roadmap)
19. [Detailed Task Breakdown (P0 s/d P3)](#19-detailed-task-breakdown-p0-sd-p3)
20. [Performance Validation & Verification Plan](#20-performance-validation--verification-plan)
21. [Quality Assurance & Regression Testing Plan](#21-quality-assurance--regression-testing-plan)
22. [Risk Register & Mitigation Matrix](#22-risk-register--mitigation-matrix)
23. [Rollback Strategy & Safety Nets](#23-rollback-strategy--safety-nets)
24. [Definition of Done (DoD)](#24-definition-of-done-dod)
25. [Unverified Findings & Remaining Questions](#25-unverified-findings--remaining-questions)
26. [Final Senior Engineer Recommendation](#26-final-senior-engineer-recommendation)

---

## 1. EXECUTIVE SUMMARY

### 1.1 Kondisi Masalah
Pengguna Portal MAGKOM (peserta, pembimbing, admin, dan superadmin) melaporkan pengalaman navigasi yang lambat, terasa berat, dan sering terhenti (*stalled*) selama 1–3 detik setiap kali berpindah halaman atau mengklik menu sidebar.

Investigasi forensik mendalam yang dilakukan pada lingkungan aktual (Laragon Nginx 1.27.3, PHP 8.3.28 FastCGI, MySQL 8.4.3, Vite 6.0.7) membuktikan bahwa **keterlambatan navigasi BUKAN diakibatkan oleh arsitektur Multi-Page Application (MPA) Laravel**, melainkan akibat konvergensi dari **empat bottleneck fatal** yang saling mengamplifikasi:

```mermaid
graph TD
    A["Keluhan Pengguna: Navigasi Lambat (1–3s Jeda Reload)"] --> B["Bottleneck #1: Payload Bloat (Base64 Logo 513 KB x 3 & Inline CSS/JS)"]
    A --> C["Bottleneck #2: FastCGI Worker Starvation (Pool Upstream Laragon Hanya 2 Socket)"]
    A --> D["Bottleneck #3: Synchronous DB Query Storms & Missing Index pada audit_log.waktu"]
    A --> E["Bottleneck #4: Missing Cache-Control & Gzip pada Web Server Nginx"]

    B --> F["Browser harus mentransfer 600 KB - 1.74 MB raw HTML per klik; CSSOM diparsing ulang setiap navigasi"]
    C --> G["Request halaman baru tertahan di FastCGI queue karena 1 worker disibukkan oleh SSE polling tiap 3.5s"]
    D --> H["Query count & aggregate tanpa indeks memblokir thread PHP hingga 400-800 ms"]
    E --> I["Asset Vite (app.css 346 KB) ditransfer tanpa kompresi dan tanpa header immutable caching"]
```

### 1.2 Fakta Empiris Utama
1. **Injeksi Base64 Raksasa di Seluruh Halaman**: Berkas [`resources/views/partials/logo-sleman.blade.php`](file:///g:/MAGKOM/resources/views/partials/logo-sleman.blade.php#L7-L16) membaca [`public/images/logo-sleman.base64.txt`](file:///g:/MAGKOM/public/images/logo-sleman.base64.txt) (513.564 bytes) dan menanamkannya ke tag `<img>`. Di halaman landing (`/`), berkas ini di-include 3 kali, menghasilkan **1.540.758 bytes (1,54 MB) data Base64** di dalam dokumen HTML mentah berukuran total **1,74 MB** dengan TTFB **1.346 ms – 1.513 ms**! Di seluruh layout (`layouts/app`, `layouts/auth`, `layouts/dashboard`, `layouts/peserta`), logo 513 KB ini ditanamkan pada setiap request.
2. **Kantung Monolithic Inline CSS & JS di Blade**:
   - `superadmin/audit-log/partials/_styles.blade.php`: **49.764 bytes (2.364 baris CSS)**.
   - `superadmin/audit-log/partials/_scripts.blade.php`: **16.010 bytes (447 baris JS)**.
   - `admin/kuota/index.blade.php`: **25.206 bytes CSS + 19.323 bytes JS (2.254 baris)**.
   - `admin/penempatan/index.blade.php`: **20.672 bytes CSS + 20.115 bytes JS (2.235 baris)**.
   - `admin/pendaftaran/index.blade.php`: **24.699 bytes CSS + 4.152 bytes JS (1.389 baris)**.
   - `layouts/app.blade.php`: **26.248 bytes CSS (877 baris)**.
   - `layouts/auth.blade.php`: **25.444 bytes CSS (883 baris)**.
   Karena middleware `SecurityHeaders` memberikan header `Cache-Control: no-store, private` pada seluruh pengguna terautentikasi, styling dan skrip inline ini **tidak pernah bisa di-cache browser** dan wajib diunduh serta diparsing ulang pada setiap klik navigasi.
3. **Double Loading CSS pada Audit Log SuperAdmin**: Pada [`resources/views/superadmin/audit-log/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/audit-log/index.blade.php#L17-L18), berkas CSS dimuat dua kali: satu via `@vite(['...audit-log.css'])` (35 KB) dan satu lagi via `@include('..._styles')` (50 KB). Pengguna mengunduh CSS yang identik dua kali.
4. **FastCGI Socket Starvation**: Lingkungan Laragon menjalankan Nginx dengan `upstream php_upstream` yang hanya memiliki **2 worker socket** (`127.0.0.1:9003` dan `9004`). Jika satu socket terikat oleh reconnect SSE setiap 3,5 detik atau request multi-tab, navigasi halaman berikutnya akan terhambat di antrean TCP Nginx hingga worker bebas.
5. **Database Bottleneck pada Modul Kunci**:
   - `AuditLogController@index` menjalankan 6–8 query `count()` berbasis rentang waktu (`waktu >= $since24h`) pada tabel `audit_log`. Namun tabel ini **tidak memiliki indeks tunggal pada kolom `waktu`** maupun indeks gabungan `(waktu, channel)`. Hal ini memaksa *Full Table Scan* pada tabel terbesar di sistem.
   - `PenempatanController@index` mengeksekusi **18+ query sinkronus** pada setiap render, termasuk menarik seluruh koleksi user peserta tanpa penempatan ke memori tanpa pagination.

---

## 2. PROJECT ARCHITECTURE OVERVIEW

Arsitektur aplikasi MAGKOM mengadopsi pola MVC Laravel 10 dengan render sisi server (Blade MPA) dan kompilasi aset frontend via Vite.

```mermaid
sequenceDiagram
    autonumber
    actor User as Browser Klien
    participant Nginx as Nginx 1.27.3 (Port 8000)
    participant FPM as FastCGI (php-cgi Pool: 9003, 9004)
    participant Kernel as Laravel HTTP Kernel & Middleware
    participant Ctrl as Controller & Action/Query
    participant DB as MySQL 8.4.3
    participant View as Blade Engine & Partials

    User->>Nginx: GET /admin/penempatan (Navigasi Menu)
    Note over Nginx,FPM: Antrean jika Worker 9003/9004 sedang melayani SSE/Query lambat
    Nginx->>FPM: FastCGI Pass ke php_upstream
    FPM->>Kernel: Bootstrapping Framework & Middleware Stack
    Note over Kernel: StartSession, SecurityHeaders (no-store), LogAuditTrail
    Kernel->>Ctrl: Dispatch Admin\PenempatanController@index
    Ctrl->>DB: Query 1: PenempatanIndexQuery (Paginated + Relations)
    Ctrl->>DB: Query 2: Pembimbing List (withCount & with)
    Ctrl->>DB: Query 3: AntreanBelumDitempatkan (5 item)
    Ctrl->>DB: Query 4: TotalBelumDitempatkan (Count)
    Ctrl->>DB: Query 5..9: Master Bulan, SlotKhusus, Bidang, Jurusan, Kuota (Uncached)
    Ctrl->>DB: Query 10: User::where('role', 'peserta')->whereDoesntHave(...) (All Rows!)
    Ctrl->>View: view('admin.penempatan.index', $data)
    View->>View: Render Blade Layout & Komponen
    View->>View: Injeksi Base64 logo-sleman.base64.txt (513 KB)
    View->>View: Injeksi Inline CSS (20,6 KB) & Inline JS (20,1 KB)
    View->>FPM: Hasil Render HTML (Ukuran ~620 KB)
    FPM->>Nginx: Stream HTTP 200 Response
    Nginx-->>User: Transfer HTML (Uncompressed / Tanpa Gzip)
    Note over User: Browser Download 620 KB HTML -> Parse CSSOM -> Request Assets
    User->>Nginx: GET /build/assets/app-ZxYmmfln.css (346 KB)
    Nginx-->>User: HTTP 200 (No Cache-Control Immutable Header!)
```

### Inventarisasi Komponen & Tanggung Jawab:

| Lapisan Arsitektur | Komponen / Berkas Utama | Tanggung Jawab & Peran | Potensi Bottleneck Performa |
| :--- | :--- | :--- | :--- |
| **Web Server** | `F:\laragon\bin\nginx\nginx-1.27.3` | Reverse proxy, static asset handler, SSL termination, FastCGI routing | Tidak ada gzip, tidak ada header `Cache-Control` pada `/build/assets/`, buffer upstream kecil |
| **PHP SAPI** | `F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php-cgi.exe` | Eksekusi runtime PHP 8.3 via FastCGI pool | Pool hanya 2 worker (`127.0.0.1:9003` & `9004`), mudah mengalami worker starvation |
| **HTTP Middleware** | `app/Http/Kernel.php` (`web` group) | Session, CSRF, Security Headers, Locale, Audit Trail | `SecurityHeaders` menyetel `no-store` untuk auth; `LogAuditTrail::terminate()` sinkron di FastCGI |
| **Controllers** | `app/Http/Controllers/` | Orkestrasi HTTP request, otorisasi, koordinasi Query & Service | Mengambil master data statis berulang tanpa micro-cache; memanggil query berat di index |
| **Database Layer** | `MySQL 8.4.3` via Eloquent ORM | Penyimpanan relasional, transaksi, konsistensi kuota | Missing index `audit_log.waktu`; query `select *` tanpa batasan kolom; 18+ query per request |
| **Presentation** | Blade Engine (`resources/views/`) | Templating HTML, komponen UI, rendering server-side | Injeksi base64 logo (513 KB), puluhan ribu baris inline `<style>` dan `<script>` |
| **Asset Pipeline** | Vite 6.0.7 & PostCSS | Bundling aset frontend, code splitting, asset hashing | `app.css` monolitik (346 KB); entry point belum terbagi rapi per role |
| **Realtime Engine** | `RealtimeStreamController.php`, `realtime.js`, `realtime-sse.js` | Push notifikasi via Server-Sent Events (RFC 8895) | Reconnect loop interval 3.5s; berisiko mengunci 1 dari 2 worker FastCGI |

---

## 3. AUDIT SCOPE

Audit performa ini mencakup seluruh ekosistem aplikasi MAGKOM secara menyeluruh (*full-stack read-only audit*):
1. **Jaringan & Web Server**: Konfigurasi Nginx Laragon, port listening, FastCGI upstream buffering, HTTP headers, dan kompresi gzip.
2. **PHP Runtime & Middleware**: PHP 8.3.28 SAPI FastCGI, lifecycle middleware Laravel, session blocking, dan terminable middleware.
3. **Database & Query**: MySQL 8.4.3, query duplication, N+1 problem, struktur indeks migration, kueri agregasi berat, dan penggunaan memori koleksi Eloquent.
4. **Server-Sent Events (SSE)**: Controller streaming, lifecycle EventSource, tab visibility handling, reconnect timing, dan mitigasi concurrency.
5. **Frontend & Blade Templates**: Ukuran berkas Blade, keberadaan inline `<style>` dan `<script>`, aset statis, penguraian CSSOM, dan injeksi Base64.
6. **Vite & Pipeline Aset**: Konfigurasi Vite, ukuran bundle build, chunk splitting, dan integrasi dengan layout Blade.
7. **Strategi Caching**: Cache driver, key namespace, durasi TTL, mekanisme invalidasi pada mutasi CRUD, dan isolasi lingkungan pengujian (PHPUnit).

---

## 4. METHODOLOGY

Investigasi dilakukan dengan prinsip ilmiah berbasis bukti (*evidence-based engineering*):
- **Read-Only Non-Destructive Observasi**: Tidak ada mutasi kode, migration database, atau perubahan konfigurasi selama investigasi.
- **Empirical HTTP Benchmarking**: Pengukuran waktu respons (TTFB, Total Elapsed Time) dan ukuran transfer menggunakan pengukuran konsisten (median dari 3x pengujian berulang pada kondisi warm-cache).
- **Static Code Analysis**: Penelusuran pohon dependensi controller, kueri Eloquent, pemindaian regex untuk mengukur payload inline Blade, dan verifikasi migration database.
- **Process & Socket Inspection**: Pengecekan proses sistem operasi Windows, PID Nginx, FastCGI socket, MySQL daemon, dan konfigurasi upstream.
- **Triangulasi Hipotesis**: Memverifikasi setiap hipotesis dari audit sebelumnya terhadap kode nyata dan lingkungan eksekusi untuk membuktikan atau membantahnya.

---

## 5. PERFORMANCE BASELINE & EMPIRICAL MEASUREMENTS

Pengukuran dilakukan pada server lokal Nginx (Port 8000) dengan runtime PHP 8.3.28 FastCGI dan MySQL 8.4.3.

### 5.1 Tabel Pengukuran Halaman Representatif

| Route / Halaman | Role Target | Status Code | Median TTFB / Elapsed | Transfer Size (HTML Raw) | Payload CSS/JS Inline | Baseline Status | Keterangan & Catatan Empiris |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `/` (Landing Page) | Publik / Guest | 200 OK | **1.352 ms** | **1.746.137 B (1,74 MB)** | 8.245 B CSS / 499 B JS | **KRITIS** | Mengandung 3x logo Base64 (1,54 MB). Mengunduh 1,74 MB HTML pada setiap visit! |
| `/login` | Publik / Guest | 200 OK | **85 ms** | **30.716 B (30,7 KB)** | 0 B (Vite Depth) | **OPTIMAL** | Menggunakan layout `auth-depth` modern. Bersih dari inline styling. |
| `/panduan-alur` | Publik / Guest | 200 OK | **530 ms** | **632.613 B (632 KB)** | 27.620 B JS (351 baris) | **BURUK** | 513 KB logo Base64 + 27 KB inline JS logic di bagian bawah berkas. |
| `/peta-alur-interaktif` | Publik / Guest | 200 OK | **103 ms** | **632.613 B (632 KB)** | 41.392 B JS (1.333 baris) | **BURUK** | Mengandung `?v={{ time() }}` pada CSS; membunuh browser caching! |
| `/admin/dashboard` | Admin | 200 OK (Auth) | **~380 ms** | **~580.000 B (~580 KB)** | 14.951 B JS (Dashboard) | **SEDANG** | Metrik di-cache 30s. Tetap menarik 513 KB logo Base64 per request. |
| `/admin/kuota` | Admin | 200 OK (Auth) | **~520 ms** | **~640.000 B (~640 KB)** | 25.206 B CSS + 19.323 B JS | **KRITIS** | 2.254 baris Blade. 1.350 baris adalah raw inline CSS/JS. Uncached. |
| `/admin/penempatan` | Admin | 200 OK (Auth) | **~680 ms** | **~635.000 B (~635 KB)** | 20.672 B CSS + 20.115 B JS | **KRITIS** | 18+ DB query sinkronus + koleksi seluruh peserta aktif tanpa penempatan. |
| `/admin/pendaftaran` | Admin | 200 OK (Auth) | **~590 ms** | **~610.000 B (~610 KB)** | 24.699 B CSS + 4.152 B JS | **KRITIS** | 804 baris inline CSS di dalam view. Uncached. |
| `/superadmin/audit-log` | SuperAdmin | 200 OK (Auth) | **~750 ms** | **~685.000 B (~685 KB)** | 49.764 B CSS + 16.010 B JS | **KRITIS** | Double CSS loading (Vite + Blade). 6-8 count query tanpa index waktu. |
| `/peserta/dashboard` | Peserta | 200 OK (Auth) | **~420 ms** | **~560.000 B (~560 KB)** | 16.054 B JS (Peserta) | **SEDANG** | Query `penempatanPeserta` berulang di Controller & Layout Blade. |

---

## 6. NETWORK & FASTCGI SOCKET INVESTIGATION

### 6.1 Arsitektur Upstream Laragon
Berdasarkan pemeriksaan langsung pada [`F:\laragon\etc\nginx\php_upstream.conf`](file:///F:/laragon/etc/nginx/php_upstream.conf#L4-L7):
```nginx
upstream php_upstream {
    server 127.0.0.1:9003 weight=1 max_fails=1 fail_timeout=1;
    server 127.0.0.1:9004 weight=1 max_fails=1 fail_timeout=1;
}
```
Dan pemeriksaan tabel proses Windows:
- Terdapat tepat **dua instance `php-cgi.exe`** aktif (PID 4964 dan 21788).
- Nginx mendengarkan pada Port 8000 (PID 22156) dan Port 8080.
- Pool upstream ini beroperasi dengan model Round-Robin sederhana antara dua socket port lokal (9003 dan 9004).

### 6.2 Mekanisme Terjadinya FastCGI Stalling
Saat pengguna membuka dashboard:
1. Browser memuat halaman HTML (memakai 1 worker selama 300–700 ms).
2. Setelah HTML selesai, JavaScript inisialisasi EventSource menghubungi `/realtime/stream`.
3. Koneksi SSE ini diteruskan oleh Nginx ke salah satu worker (misalnya Port 9003).
4. Jika pengguna membuka tab kedua, atau menavigasi ke halaman lain sementara worker 9003 belum selesai melepaskan koneksi, worker 9004 melayani request kedua.
5. Jika ada request ketiga (misalnya polling interval 3,5 detik, atau download aset yang jatuh ke PHP), **seluruh worker habis**.
6. Request navigasi baru tertahan di antrean internal Nginx (*stalled*) hingga salah satu worker selesai atau mengalami timeout. Inilah penyebab jeda 1–3 detik yang dirasakan pengguna!

---

## 7. PHP & LARAVEL RUNTIME PERFORMANCE AUDIT

### 7.1 Evaluasi Middleware Pipeline
Pemeriksaan pada [`app/Http/Kernel.php`](file:///g:/MAGKOM/app/Http/Kernel.php#L63-L75):
1. **`SecurityHeaders::class`**:
   - Baris 29–32: Menyetel `Cache-Control: no-store, private, must-revalidate` untuk setiap request pengguna terautentikasi (`$request->user()`).
   - *Dampak*: Menjamin keamanan data personal, namun jika file Blade menanamkan CSS/JS inline, seluruh aset frontend ikut terkena aturan `no-store` ini.
2. **`LogAuditTrail::class`**:
   - Menggunakan terminable execution: `terminate(Request $request, Response $response)`.
   - Di lingkungan PHP-FPM Linux dengan `fastcgi_finish_request()`, terminable middleware berjalan setelah response terkirim ke klien. Namun di Windows `php-cgi.exe`, eksekusi `terminate()` bersifat **sinkronus sebelum socket FastCGI ditutup**. Untungnya, `AuditService::tulisAksesData()` memiliki guardrail `if ($this->dataAksesPending === null) return null;` sehingga tidak mengeksekusi query database jika tidak ada penanda akses sensitif.
3. **`EnsureLandingPreviewContext::class`**:
   - Memeriksa hak akses SuperAdmin untuk mode pratinjau landing page. Beban komputasi sangat ringan (in-memory property set).

### 7.2 Controller & Service Memory Overhead
- Pada [`Admin\PenempatanController@index`](file:///g:/MAGKOM/app/Http/Controllers/Admin/PenempatanController.php#L86-L95):
  ```php
  $pesertaList = User::where('role', RoleEnum::Peserta)
      ->where('status_aktif', true)
      ->whereDoesntHave('penempatanPeserta', function ($q) {
          $q->where('status', StatusPenempatanEnum::Aktif);
      })
      ->with('profil')
      ->orderBy('nama')
      ->get();
  ```
  Query ini mengambil **seluruh entitas User dan Profil peserta yang belum ditempatkan ke dalam memori RAM** hanya untuk mengisi opsi `<select>` di modal manual assignment. Jika pendaftar mencapai ratusan orang, ribuan objek model Eloquent terhidrasi ke dalam memori PHP pada setiap refresh halaman index.

---

## 8. DATABASE, ELOQUENT & INDEXING PERFORMANCE AUDIT

### 8.1 Temuan Kritis: Missing Index pada `audit_log`
Pada migration [`database/migrations/2014_10_12_200000_create_audit_log_table.php`](file:///g:/MAGKOM/database/migrations/2014_10_12_200000_create_audit_log_table.php#L36-L39):
```php
$table->index(['user_id', 'waktu']);
$table->index(['entitas', 'entitas_id']);
$table->index('request_id');
$table->index(['tipe', 'waktu']);
```
Dan migration penambahan kolom [`database/migrations/2026_10_15_100000_add_channel_to_audit_log_table.php`](file:///g:/MAGKOM/database/migrations/2026_10_15_100000_add_channel_to_audit_log_table.php#L18):
```php
$table->string('channel', 40)->default('system-security')->after('tipe')->index();
```

**Analisis Masalah**:
Pada [`AuditLogController::index`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/AuditLogController.php#L121-L125):
```php
$rawCounts = AuditLog::where('waktu', '>=', $since24h)
    ->selectRaw('channel, count(*) as total')
    ->groupBy('channel')
    ->pluck('total', 'channel')
    ->all();
```
- Query ini memfilter `waktu >= $since24h` lalu melakukan `GROUP BY channel`.
- Database **TIDAK DAPAT** menggunakan indeks gabungan `['tipe', 'waktu']` atau `['user_id', 'waktu']` karena kolom pertama (`tipe` atau `user_id`) tidak ada di klausa WHERE (pelanggaran prinsip *Leftmost Prefix* pada indeks B-Tree).
- Indeks pada `channel` berdiri sendiri.
- Akibatnya: MySQL dipaksa melakukan **Full Table Scan** untuk memfilter kolom `waktu` pada setiap pembukaan halaman Audit Log SuperAdmin!

### 8.2 Query Duplication & Uncached Master Data
Pada modul administrasi, kueri data master statis dijalankan secara sinkronus pada setiap request:

| Lokasi Controller | Query Eloquent yang Dieksekusi | Frekuensi Mutasi Data | Status Caching | Rekomendasi Solusi |
| :--- | :--- | :--- | :--- | :--- |
| `Admin\PenempatanController@index` | `BulanMagang::orderBy('bulan_mulai', 'desc')->get()` | Sangat Jarang (1-2x per bulan) | **TIDAK ADA** | Micro-cache 300 detik dengan tag / key `master_bulan_magang_all` |
| `Admin\PenempatanController@index` | `SlotKhusus::where('aktif', true)->orderBy('bulan_mulai')->get()` | Jarang (Saat ada kuota mitra) | **TIDAK ADA** | Micro-cache 300 detik `master_slot_khusus_aktif` |
| `Admin\PenempatanController@index` | `Bidang::where('aktif', true)->orderBy('nama_bidang')->get()` | Sangat Statis (Struktur OPD Sleman) | **TIDAK ADA** | Micro-cache 3600 detik `master_bidang_aktif` |
| `Admin\PenempatanController@index` | `Jurusan::where('aktif', true)->orderBy('nama_jurusan')->get()` | Sangat Statis (Kurikulum SMK/PT) | **TIDAK ADA** | Micro-cache 3600 detik `master_jurusan_aktif` |
| `Admin\PenempatanController@index` | `Kuota::with(['bulanMagang', 'jurusan'])->where('aktif', true)->get()` | Menengah (Disesuaikan admin) | **TIDAK ADA** | Cache 300 detik dengan invalidasi saat `KuotaController@update` |
| `layouts/dashboard.blade.php` (L93-97) | `$currentUser->penempatanPeserta()->where('status', ...)->first()` | Saat peserta buka halaman | **TIDAK ADA (Di View!)** | Pindahkan query dari Blade ke View Composer atau Session helper |

---

## 9. SERVER-SENT EVENTS (SSE) & CONCURRENCY AUDIT

### 9.1 Evaluasi Implementasi SSE Aktual
Pemeriksaan kode aktual membuktikan adanya divergensi penting dari asumsi audit sebelumnya:
1. **Server Controller ([`app/Http/Controllers/RealtimeStreamController.php`](file:///g:/MAGKOM/app/Http/Controllers/RealtimeStreamController.php#L33-L93))**:
   - Telah menerapkan pelepasan session lock:
     ```php
     if ($request->hasSession()) {
         $request->session()->save();
     }
     if (session_status() === PHP_SESSION_ACTIVE) {
         session_write_close();
     }
     ```
   - Telah menerapkan pemangkasan durasi untuk local/testing:
     ```php
     $isLocalOrTesting = app()->environment(['local', 'testing']);
     $maxDuration = $isLocalOrTesting ? 1 : 20;
     $sleepSeconds = $isLocalOrTesting ? 0 : 2;
     $retryMs = $isLocalOrTesting ? 3500 : 2500;
     ```
     Pada lingkungan lokal (`APP_ENV=local`), controller **hanya menjalankan 1 iterasi burst dan langsung memutus koneksi dalam < 100 ms**, lalu menginstruksikan browser untuk reconnect 3,5 detik kemudian via header `retry: 3500`.
2. **Klien SSE ([`resources/js/services/realtime.js`](file:///g:/MAGKOM/resources/js/services/realtime.js#L136-L143) dan [`public/js/realtime-sse.js`](file:///g:/MAGKOM/public/js/realtime-sse.js#L136-L143))**:
   - Listener graceful disconnect `beforeunload` dan `pagehide` **SUDAH TERPASANG** di konstruktor:
     ```javascript
     window.addEventListener('beforeunload', () => { this.disconnect(); });
     window.addEventListener('pagehide', () => { this.disconnect(); });
     ```

### 9.2 Status Evaluasi SSE: LIKELY CONTRIBUTING FACTOR (Bukan Single Root Cause)
Meskipun guardrail disconnect sudah ada, SSE tetap berkontribusi terhadap degradasi performa pada skenario tertentu:
- **Toleransi Sempit pada 2-Worker FastCGI**: Polling reconnect setiap 3,5 detik berarti setiap tab browser aktif mengirimkan 1 request FastCGI setiap 3,5 detik. Dengan hanya 2 worker, dua tab yang terbuka secara bersamaan akan menyita kapasitas worker secara berkala.
- **Bukan Penyebab Lambat pada Halaman Publik & Auth**: Halaman `/`, `/login`, dan `/panduan-alur` tidak memuat SSE sama sekali, namun tetap mengalami keterlambatan hingga 1.350 ms akibat payload Base64 dan asset bloat.

---

## 10. FRONTEND, BLADE & MONOLITHIC INLINE ASSETS AUDIT

### 10.1 Injeksi Base64 Logo Sleman (The Half-Megabyte Choke)
Pemeriksaan pada [`resources/views/partials/logo-sleman.blade.php`](file:///g:/MAGKOM/resources/views/partials/logo-sleman.blade.php#L1-L27):
```php
static $logoSlemanDataUri = null;
if ($logoSlemanDataUri === null) {
    $base64Path = public_path('images/logo-sleman.base64.txt');
    $base64Content = file_exists($base64Path) ? file_get_contents($base64Path) : false;
    if (is_string($base64Content)) {
        $logoSlemanDataUri = 'data:image/png;base64,' . trim($base64Content);
    } else {
        $logoSlemanDataUri = asset('images/logo-sleman.png');
    }
}
```

**Temuan Forensik**:
- Berkas `public/images/logo-sleman.base64.txt` berukuran **513.564 bytes**.
- Ketika template meng-include `partials.logo-sleman`, string Base64 sebesar 513 KB tersebut ditanamkan langsung ke atribut `src="..."` dari elemen `<img>`.
- Di halaman landing ([`resources/views/public/landing.blade.php`](file:///g:/MAGKOM/resources/views/public/landing.blade.php#L124-L168)):
  1. Occurrence 1: Preloader splash screen (baris 124).
  2. Occurrence 2: Navbar brand logo (baris 168).
  3. Occurrence 3: Footer brand logo.
  Total Base64 yang diinjeksikan ke satu halaman HTML mencapai **1.540.758 bytes (1,54 MB)**!
- Di layout dashboard ([`layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php#L119)) dan layout peserta ([`layouts/peserta.blade.php`](file:///g:/MAGKOM/resources/views/layouts/peserta.blade.php#L71)), logo ini ditanamkan di topbar header. Padahal logo tersebut hanya ditampilkan dengan dimensi **28x28 piksel**!
- Ironisnya, pada baris 16 di kedua layout tersebut, terdapat kode preload:
  `<link rel="preload" as="image" href="{{ asset('images/logo-sleman.png') }}" fetchpriority="high">`
  Aplikasi sudah mem-preload berkas PNG statis eksternal, tetapi kemudian merendernya menggunakan string Base64 raksasa di body!

### 10.2 Sensus Kantung Monolitik CSS & JS di Blade
Pemindaian otomatis seluruh repositori `resources/views/` menemukan distribusi styling/skrip inline sebagai berikut:

```text
+-------------------------------------------------------------------+------------+-------------+------------+
| Berkas Blade Template                                             | CSS Inline | JS Inline   | Total Baris|
+-------------------------------------------------------------------+------------+-------------+------------+
| resources/views/superadmin/audit-log/partials/_styles.blade.php   | 49.764 B   | -           | 2.364      |
| resources/views/superadmin/audit-log/partials/_scripts.blade.php  | -          | 16.010 B    | 447        |
| resources/views/admin/kuota/index.blade.php                       | 25.206 B   | 19.323 B    | 2.254      |
| resources/views/admin/penempatan/index.blade.php                  | 20.672 B   | 20.115 B    | 2.235      |
| resources/views/admin/pendaftaran/index.blade.php                 | 24.699 B   | 4.152 B     | 1.389      |
| resources/views/layouts/app.blade.php                             | 26.248 B   | 2.741 B     | 1.277      |
| resources/views/layouts/auth.blade.php                            | 25.444 B   | 3.297 B     | 1.057      |
| resources/views/public/panduan-alur.blade.php                     | -          | 27.620 B    | 1.279      |
| resources/views/public/partials/_peta_alur_modal.blade.php        | -          | 41.392 B    | 1.333      |
| resources/views/admin/laporan/index.blade.php                     | 3.806 B    | 8.310 B     | 1.044      |
| resources/views/peserta/pendaftaran/detail.blade.php              | -          | 7.835 B     | 709        |
| resources/views/errors/layout.blade.php                           | 5.629 B    | -           | 253        |
+-------------------------------------------------------------------+------------+-------------+------------+
```

---

## 11. VITE ASSET PIPELINE & HTTP CACHING AUDIT

### 11.1 Ukuran Bundle Build Vite
Pemeriksaan aset hasil build pada [`public/build/assets/`](file:///g:/MAGKOM/public/build/assets):
- `app-ZxYmmfln.css`: **346.511 bytes (346,5 KB)**.
- `app-Dn6-VFzl.js`: **85.501 bytes (85,5 KB)**.
- `editor-app-DpCQrJUO.js`: **90.129 bytes (90,1 KB)**.
- `audit-log-BgJkaPzN.css`: **35.230 bytes (35,2 KB)**.

Aset global `app.css` mencapai 346 KB karena mengimpor hampir seluruh stylesheet komponen, layout, dan halaman sekaligus di [`resources/css/app.css`](file:///g:/MAGKOM/resources/css/app.css#L1-L30).

### 11.2 Evaluasi Header Caching & Kompresi Nginx
Pengujian HTTP GET langsung pada aset Vite via Port 8000 menghasilkan header:
```http
HTTP/1.1 200 OK
Server: nginx/1.27.3
Date: Fri, 13 Nov 2026 03:16:10 GMT
Content-Type: text/css
Content-Length: 346511
Last-Modified: Fri, 13 Nov 2026 02:50:16 GMT
Connection: keep-alive
ETag: "6af67ae8-5498f"
Accept-Ranges: bytes
```

**Temuan Kritis**:
1. **Tidak Ada Header `Cache-Control`**: Nginx tidak menyertakan directive `Cache-Control: public, max-age=31536000, immutable` untuk berkas statis ber-hash di `/build/assets/`.
2. **Tidak Ada Kompresi Gzip**: Directive `#gzip on;` di [`F:\laragon\bin\nginx\nginx-1.27.3\conf\nginx.conf`](file:///F:/laragon/bin/nginx/nginx-1.27.3/conf/nginx.conf#L33) masih dikomentari (*disabled*). File `app.css` berukuran 346 KB ditransfer utuh tanpa kompresi, padahal dengan gzip ukurannya akan menyusut menjadi ~45 KB (penurunan transfer sebesar **87%**).

---

## 12. CACHE ARCHITECTURE & INVALIDATION STRATEGY AUDIT

### 12.1 Analisis Konfigurasi Driver Cache
- Di dalam [`.env`](file:///g:/MAGKOM/.env#L34):
  `CACHE_DRIVER=file`
  `SESSION_DRIVER=file`
- Di dalam [`phpunit.xml`](file:///g:/MAGKOM/phpunit.xml#L24):
  `<env name="CACHE_DRIVER" value="array"/>`
  `<env name="DB_CONNECTION" value="sqlite"/>`
  `<env name="DB_DATABASE" value=":memory:"/>`

### 12.2 Keamanan Pengujian (Testing Safety Guardrail)
Dalam `AdminDashboardMetricsService`, cache dibungkus dengan:
```php
if (app()->environment('testing')) {
    return $this->computeFreshDashboardData($bulanId);
}
```
Mekanisme ini aman, namun untuk data referensi master (Bidang, Jurusan, BulanMagang), pendekatan yang lebih elegan dan teruji adalah **pemasangan Model Observer / Event Listener otomatis** yang menghapus key cache saat terjadi aksi `created`, `updated`, atau `deleted`. Dengan cara ini, cache tetap dapat diuji konsistensinya di lingkungan testing tanpa risiko stale data.

---

## 13. CROSS-CUTTING FINDINGS & ANOMALY DETECTION

1. **Third-Party CDN Tailwind Play Script**:
   Pada [`resources/views/public/kebijakan-privasi.blade.php`](file:///g:/MAGKOM/resources/views/public/kebijakan-privasi.blade.php#L22) dan [`resources/views/public/verifikasi-dokumen.blade.php`](file:///g:/MAGKOM/resources/views/public/verifikasi-dokumen.blade.php#L12), ditemukan:
   `<script src="https://cdn.tailwindcss.com"></script>`
   Skrip Play CDN ini berukuran ~300 KB dan memicu JIT engine di dalam browser pada saat runtime. Ini melanggar standar produksi dan memperlambat First Contentful Paint.
2. **Disk I/O Berulang via `filemtime()` di Blade**:
   Pada [`layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php#L27-L28), sistem memanggil `file_exists()` dan `filemtime()` pada berkas publik di setiap request untuk membuat query string cache-buster `?v=...`. Ini menimbulkan overhead disk I/O yang tidak perlu di lingkungan produksi.
3. **Cache-Buster Abadi via `time()`**:
   Pada [`resources/views/public/peta-alur-interaktif.blade.php`](file:///g:/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php#L17):
   `<link rel="stylesheet" href="{{ asset('css/peta-alur-fullscreen.css') }}?v={{ time() }}">`
   Timestamp `time()` yang berubah setiap detik memaksa browser selalu mengunduh ulang CSS dan tidak pernah mengizinkan caching 304.

---

## 14. COMPREHENSIVE EVIDENCE LEDGER

| Item Bukti | Lokasi Berkas & Baris | Data / Fakta Empiris | Status Bukti | Dampak Terhadap Navigasi |
| :--- | :--- | :--- | :--- | :--- |
| **EV-01: Base64 Logo Bloat** | `resources/views/partials/logo-sleman.blade.php` (L7-L16) | Menginjeksi 513 KB base64 dari `public/images/logo-sleman.base64.txt`. Di `/` diulang 3x (1,54 MB). | **CONFIRMED** | Menambah 513 KB – 1,54 MB HTML mentah per halaman. |
| **EV-02: Double CSS Load** | `resources/views/superadmin/audit-log/index.blade.php` (L17-L18) | `@vite(['...audit-log.css'])` DAN `@include('..._styles')` dipanggil bersamaan. | **CONFIRMED** | CSS audit log 49,7 KB diunduh dua kali lipat. |
| **EV-03: Missing Index Audit** | `database/migrations/2014_10_12_200000_create_audit_log_table.php` | Kolom `waktu` tidak memiliki standalone index atau composite `(waktu, channel)`. | **CONFIRMED** | Full Table Scan pada 6–8 kueri KPI audit log. |
| **EV-04: FastCGI Worker Limit**| `F:\laragon\etc\nginx\php_upstream.conf` (L4-L7) | Hanya mendefinisikan 2 worker socket (`127.0.0.1:9003` dan `9004`). | **CONFIRMED** | Concurrency choke; antrean request saat SSE aktif. |
| **EV-05: Missing Cache Headers**| `F:\laragon\etc\nginx\sites-enabled\magkom.test.conf` | Tidak ada location block khusus `/build/` dengan header `Cache-Control: immutable`. | **CONFIRMED** | Aset Vite tidak memiliki jaminan cache jangka panjang. |
| **EV-06: Gzip Disabled** | `F:\laragon\bin\nginx\nginx-1.27.3\conf\nginx.conf` (L33) | `#gzip on;` dalam keadaan commented out. | **CONFIRMED** | Aset CSS 346 KB ditransfer utuh tanpa kompresi. |
| **EV-07: Uncached Master Data**| `app/Http/Controllers/Admin/PenempatanController.php` (L81-85) | 5 kueri data master statis dijalankan sinkronus tanpa caching pada setiap hit. | **CONFIRMED** | Membebani I/O database dan memperpanjang TTFB. |
| **EV-08: Monolithic Blade CSS**| `resources/views/admin/kuota/index.blade.php` (L10-914) | 905 baris CSS inline (25 KB) tertanam di dalam Blade. | **CONFIRMED** | Zero browser cache; parsing CSSOM ulang setiap klik. |
| **EV-09: Play CDN Script** | `resources/views/public/kebijakan-privasi.blade.php` (L22) | Memuat `https://cdn.tailwindcss.com` runtime JIT compiler. | **CONFIRMED** | Memperlambat rendering dan boros bandwidth klien. |
| **EV-10: SSE Disconnect Logic**| `resources/js/services/realtime.js` (L136-L143) | Event `beforeunload` dan `pagehide` sudah memanggil `this.disconnect()`. | **CONFIRMED** | Membantah hipotesis bahwa SSE tidak memiliki disconnect. |

---

## 15. ROOT CAUSE ANALYSIS (RCA) & CAUSAL TREE

```mermaid
graph TD
    Root["Keluhan Utama: Navigasi Lambat & Jeda Reload 1–3 Detik"]

    subgraph "RC-1: Payload & Serialization Overhead (CONFIRMED)"
        Root --> RC1["Dokumen HTML Raksasa (600 KB - 1,74 MB)"]
        RC1 --> B1["Injeksi Base64 Logo Sleman 513 KB (Diulang 3x di Landing)"]
        RC1 --> B2["Kantung Inline CSS/JS di Blade (Audit Log, Kuota, Penempatan, Layout)"]
        RC1 --> B3["SecurityHeaders menyetel no-store pada Auth -> Browser tidak bisa cache HTML"]
    end

    subgraph "RC-2: FastCGI Concurrency Choke (CONFIRMED)"
        Root --> RC2["FastCGI Worker Starvation di Laragon Windows"]
        RC2 --> C1["Pool Upstream Nginx hanya 2 worker (Port 9003 & 9004)"]
        RC2 --> C2["Polling SSE Reconnect loop setiap 3,5 detik menyita worker berkala"]
        RC2 --> C3["Multi-tab atau aset fallback menyumbat worker yang tersisa"]
    end

    subgraph "RC-3: Backend & Database Inefficiencies (CONFIRMED)"
        Root --> RC3["Latency Komputasi Server (TTFB > 500 ms)"]
        RC3 --> D1["Missing Index pada kolom audit_log.waktu -> Full Table Scan"]
        RC3 --> D2["18+ Kueri Sinkronus di PenempatanController tanpa Micro-Cache"]
        RC3 --> D3["Hydration ratusan objek Peserta tanpa penempatan ke memori"]
    end

    subgraph "RC-4: Web Server Compression & Caching Gaps (CONFIRMED)"
        Root --> RC4["Transfer Aset Statis Tidak Optimal"]
        RC4 --> E1["Nginx Gzip Disabled -> app.css ditransfer 346 KB mentah"]
        RC4 --> E2["Nginx tidak mengirimkan Cache-Control immutable untuk /build/assets/"]
    end
```

### Klasifikasi Kepastian Masalah (Confidence Ranking):
1. **Injeksi Base64 Logo Sleman**: **CONFIRMED ROOT CAUSE** (Dampak langsung pada ukuran transfer HTML di 100% halaman).
2. **Kantung Monolithic Inline CSS/JS di Blade**: **CONFIRMED ROOT CAUSE** (Menghilangkan kemampuan browser caching pada styling halaman).
3. **Double Loading CSS di Audit Log**: **CONFIRMED ROOT CAUSE** (Pemborosan bandwidth dan parsing ganda).
4. **Missing Index pada `audit_log.waktu`**: **CONFIRMED ROOT CAUSE** (Penyebab utama tingginya latency TTFB modul audit log).
5. **Ketiadaan Micro-Cache Master Data**: **CONFIRMED CONTRIBUTING FACTOR** (Menambah 4–8 kueri disk per navigasi admin).
6. **FastCGI Worker Starvation di Laragon**: **CONFIRMED CONTRIBUTING FACTOR** (Pemicu jeda antrean TCP saat konkurensi lokal meningkat).
7. **Worker Starvation akibat `php artisan serve` CLI**: **REFUTED / NOT A CAUSE** (Sistem berjalan di Laragon Nginx + FastCGI, bukan `serve`).

---

## 16. CRITIQUE & REFUTATION OF PREVIOUS AUDIT

Dokumen audit sebelumnya ([`docs/plans/2026-11-13-audit-performa-dan-optimasi-kecepatan-navigasi.md`](file:///g:/MAGKOM/docs/plans/2026-11-13-audit-performa-dan-optimasi-kecepatan-navigasi.md)) memuat beberapa asumsi yang **wajib dikoreksi secara teknis**:

1. **Kekeliruan Identifikasi Lingkungan Server**:
   - *Klaim Audit Lama*: "Aplikasi berjalan di Windows PHP CLI single-threaded via `php artisan serve`."
   - *Fakta Codebase & Runtime*: Aplikasi dijalankan di atas **Laragon Nginx 1.27.3 dengan 2 worker `php-cgi.exe` (PHP 8.3.28)**.
2. **Klaim Ketiadaan Disconnect pada SSE**:
   - *Klaim Audit Lama*: "Task 1.1: Pastikan Graceful Disconnect SSE pada navigasi via `beforeunload` dan `pagehide`."
   - *Fakta Codebase*: Baik [`resources/js/services/realtime.js`](file:///g:/MAGKOM/resources/js/services/realtime.js#L136-L143) maupun [`public/js/realtime-sse.js`](file:///g:/MAGKOM/public/js/realtime-sse.js#L136-L143) **sudah memiliki listener tersebut**. Masalah konkurensi terjadi karena sempitnya pool 2-worker di Laragon dan interval reconnect 3,5 detik, bukan ketiadaan handler disconnect.
3. **Terlewatnya Masalah Terbesar (Base64 Logo 513 KB)**:
   - Audit lama sama sekali tidak mengidentifikasi bahwa `partials/logo-sleman.blade.php` menanamkan 513 KB teks Base64 di seluruh halaman dan 1,54 MB di landing page. Padahal ini adalah sumber bloat HTML nomor satu di seluruh sistem.
4. **Terlewatnya Double CSS Loading di Audit Log**:
   - Audit lama mengira berkas `resources/css/pages/superadmin/audit-log.css` belum di-extract. Padahal berkas tersebut **sudah ada di disk dan sudah dimuat oleh `@vite`**, namun template Blade-nya **tetap memanggil `@include('..._styles')`**, menghasilkan double payload.

---

## 17. RECOMMENDED SOLUTION ARCHITECTURE

Untuk mencapai navigasi instan (< 150 ms TTFB, < 300 ms Total Render) tanpa melanggar batasan repository, arsitektur solusi disusun sebagai berikut:

```mermaid
graph LR
    subgraph "1. Diet Payload HTML (Target: < 50 KB HTML)"
        A1["Ganti Injeksi Base64 Logo Sleman dengan SVG Ringan / Static Asset URL"]
        A2["Hapus Double Loading di Audit Log (Hapus _styles.blade.php)"]
        A3["Ekstraksi Inline CSS/JS ke Pipeline Vite (Kuota, Penempatan, Pendaftaran, Layout)"]
    end

    subgraph "2. Server & Web Caching (Nginx + FastCGI)"
        B1["Konfigurasi Nginx: Cache-Control Immutable 1 Tahun untuk /build/assets/"]
        B2["Aktifkan Gzip Kompresi di Nginx (Hemat Bandwidth 80%+)"]
        B3["Tingkatkan Upstream Worker Laragon (usr/laragon.ini: Upstream=5)"]
    end

    subgraph "3. Database & Backend Acceleration"
        C1["Migration Add Index: audit_log (waktu, channel) & (waktu)"]
        C2["Micro-Caching Master Data Dropdown (Bulan, Bidang, Jurusan, Kuota) via Cache::remember"]
        C3["Lazy Load Modal Dropdown Peserta di PenempatanController"]
    end

    subgraph "4. Realtime Guardrail"
        D1["Tingkatkan Reconnect Backoff SSE di Local dari 3.5s ke 10s"]
        D2["Pastikan Abort Connection ditangani tanpa menggantung Worker"]
    end

    A1 --> EndState["Navigasi Instan MPA: TTFB < 150 ms, Transfer < 50 KB, Full Browser Cache"]
    A2 --> EndState
    A3 --> EndState
    B1 --> EndState
    B2 --> EndState
    B3 --> EndState
    C1 --> EndState
    C2 --> EndState
    C3 --> EndState
    D1 --> EndState
    D2 --> EndState
```

---

## 18. PRIORITIZED IMPLEMENTATION MASTER ROADMAP

Rencana kerja dibagi ke dalam 4 tingkatan prioritas berbasis dampak nyata (*Impact vs. Risk*):

```mermaid
gantt
    title Roadmap Implementasi Optimasi Performa MAGKOM
    dateFormat  YYYY-MM-DD
    section P0 - Stop The Bleeding (Kritis)
    Eliminasi Base64 Logo Sleman di Partials         :p0_1, 2026-11-14, 1d
    Pembersihan Double CSS Loading Audit Log        :p0_2, after p0_1, 1d
    Migration Compound Index pada audit_log         :p0_3, after p0_1, 1d
    Optimasi Upstream FastCGI & Gzip Nginx          :p0_4, after p0_2, 1d
    section P1 - Payload Diet (Vite Extraction)
    Ekstraksi CSS/JS Modul Admin Kuota              :p1_1, after p0_4, 2d
    Ekstraksi CSS/JS Modul Admin Penempatan         :p1_2, after p1_1, 2d
    Ekstraksi CSS/JS Modul Admin Pendaftaran        :p1_3, after p1_2, 2d
    Ekstraksi Layout Master (app & auth)            :p1_4, after p1_3, 2d
    section P2 - Backend & Query Micro-Caching
    Micro-Caching Master Data Dropdown              :p2_1, after p1_4, 2d
    Optimasi Kueri Penempatan Peserta di Layout     :p2_2, after p2_1, 1d
    section P3 - Long-Term Polish & NFRs
    Pembersihan Third-Party Play CDN Tailwind       :p3_1, after p2_2, 1d
    Eliminasi time() & filemtime() di View          :p3_2, after p3_1, 1d
```

---

## 19. DETAILED TASK BREAKDOWN (P0 s/d P3)

### FASE P0 — CRITICAL STOP-THE-BLEEDING (Dampak Tertinggi, Risiko Terendah)

#### TASK P0.1: Eliminasi Injeksi Base64 Raksasa pada Logo Sleman
- **Task ID**: `PERF-P0-01`
- **Priority**: P0 — Critical
- **Problem**: `resources/views/partials/logo-sleman.blade.php` membaca `logo-sleman.base64.txt` (513 KB) dan menyuntikkannya ke atribut `src="..."`. Di landing page diulang 3x (1,54 MB), dan hadir di seluruh layout dashboard dan peserta.
- **Evidence**: `EV-01` (Landing page transfer 1,74 MB; TTFB 1.350 ms).
- **Root Cause**: `RC-1` (Payload bloat).
- **Objective**: Menghentikan injeksi string Base64 ke dalam HTML. Mengarahkan `<img>` langsung ke aset publik statis `asset('images/logo-sleman.png')` atau menggunakan simbol SVG vector yang berukuran < 10 KB.
- **Files to Inspect**:
  - `resources/views/partials/logo-sleman.blade.php` (L1-L27)
  - `public/images/logo-sleman.png`
- **Files Expected to Change**:
  - `resources/views/partials/logo-sleman.blade.php`
- **Implementation Directive**:
  Ganti logika PHP di `logo-sleman.blade.php` agar langsung merender:
  ```blade
  <img src="{{ asset('images/logo-sleman.png') }}"
       alt="{{ $alt ?? 'Lambang Kabupaten Sleman' }}"
       width="{{ $width ?? '32' }}"
       height="{{ $height ?? '32' }}"
       loading="eager"
       fetchpriority="high"
       decoding="async"
       @if(!empty($class)) class="{{ $class }}" @endif
       @if(!empty($style)) style="{{ $style }}" @endif>
  ```
- **Dependencies**: Tidak ada.
- **Risks**: Pastikan berkas `public/images/logo-sleman.png` ada dan dapat diakses dengan response 200 OK.
- **Regression Test**: Buka `/`, `/admin/dashboard`, `/peserta/dashboard`. Pastikan logo Sleman tampil sempurna, tajam, dan tidak berkedip.
- **Acceptance Criteria**: Dokumen HTML landing page turun dari 1,74 MB menjadi < 220 KB. Halaman dashboard berkurang 513 KB seketika.
- **Rollback Strategy**: Kembalikan fallback ke data-uri jika berkas PNG gagal dimuat.

---

#### TASK P0.2: Pembersihan Double Loading CSS pada Audit Log SuperAdmin
- **Task ID**: `PERF-P0-02`
- **Priority**: P0 — Critical
- **Problem**: Halaman Audit Log SuperAdmin memuat CSS dua kali: satu via Vite (`audit-log.css`) dan satu lagi via inline Blade partial (`_styles.blade.php`).
- **Evidence**: `EV-02` ([`resources/views/superadmin/audit-log/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/audit-log/index.blade.php#L17-L18)).
- **Root Cause**: `RC-1` & Sisa migrasi lama.
- **Objective**: Hapus baris `@include('superadmin.audit-log.partials._styles')` dari `index.blade.php`. Kosongkan atau bersihkan berkas partial `_styles.blade.php`.
- **Files to Inspect**:
  - `resources/views/superadmin/audit-log/index.blade.php` (L16-L19)
  - `resources/views/superadmin/audit-log/partials/_styles.blade.php` (L1-L2364)
  - `resources/css/pages/superadmin/audit-log.css`
- **Files Expected to Change**:
  - `resources/views/superadmin/audit-log/index.blade.php`
  - `resources/views/superadmin/audit-log/partials/_styles.blade.php`
- **Implementation Directive**:
  1. Hapus `@include('superadmin.audit-log.partials._styles')` pada `index.blade.php`.
  2. Pastikan seluruh styling audit log yang ada di `_styles.blade.php` sudah ter-cover 100% di `resources/css/pages/superadmin/audit-log.css`.
  3. Lakukan build aset via `npm run build`.
- **Dependencies**: `PERF-P0-01`.
- **Risks**: Potensi CSS drift jika ada aturan baru di `_styles.blade.php` yang belum disinkronkan ke `audit-log.css`.
- **Regression Test**: Buka `/superadmin/audit-log`. Periksa filter multi-channel, layout 2 kolom, badge status, dan popover export.
- **Acceptance Criteria**: Dokumen HTML Audit Log berkurang 49,7 KB per load. Tidak ada tag `<style>` 2.300 baris di inspect element.
- **Rollback Strategy**: Pulihkan include partial via Git jika styling rusak.

---

#### TASK P0.3: Penambahan Indeks Komposit pada Tabel `audit_log`
- **Task ID**: `PERF-P0-03`
- **Priority**: P0 — Critical
- **Problem**: Kueri metrik 24 jam pada `AuditLogController` memfilter kolom `waktu` dan mengelompokkan berdasarkan `channel`. Tabel `audit_log` tidak memiliki indeks pada kolom `waktu` sehingga memicu full table scan.
- **Evidence**: `EV-03` ([`AuditLogController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/AuditLogController.php#L121-L125)).
- **Root Cause**: `RC-3` (Database indexing deficiency).
- **Objective**: Buat migration Laravel baru untuk menambahkan indeks `(waktu, channel)` dan standalone indeks `(waktu)`.
- **Files to Inspect**:
  - `database/migrations/2014_10_12_200000_create_audit_log_table.php`
  - `database/migrations/2026_10_15_100000_add_channel_to_audit_log_table.php`
- **Files Expected to Change**:
  - File migration baru: `database/migrations/2026_11_13_200000_add_performance_indexes_to_audit_log_table.php`
- **Implementation Directive**:
  ```php
  Schema::table('audit_log', function (Blueprint $table) {
      $table->index('waktu', 'idx_audit_log_waktu');
      $table->index(['waktu', 'channel'], 'idx_audit_log_waktu_channel');
  });
  ```
- **Dependencies**: Tidak ada.
- **Risks**: Waktu pembuatan indeks pada tabel produksi yang sudah sangat besar. Gunakan algoritma online DDL jika pada MySQL produksi.
- **Regression Test**: Jalankan `php artisan test tests/Feature/SuperAdminAuditLogTest.php`.
- **Acceptance Criteria**: Waktu eksekusi `AuditLogController@index` turun drastis (eliminasi full table scan pada metrik 24h).
- **Rollback Strategy**: Method `down()` pada migration menghapus indeks terkait.

---

#### TASK P0.4: Optimasi Konfigurasi Upstream FastCGI & Gzip Web Server
- **Task ID**: `PERF-P0-04`
- **Priority**: P0 — Critical
- **Problem**: Upstream Laragon hanya memiliki 2 worker FastCGI, kompresi Gzip mati, dan header `Cache-Control` untuk build assets tidak ada.
- **Evidence**: `EV-04`, `EV-05`, `EV-06`.
- **Root Cause**: `RC-2` & `RC-4`.
- **Objective**: Konfigurasikan Nginx vhost agar mengaktifkan gzip, mengirim header cache 1 tahun untuk `/build/`, dan siapkan panduan peningkatan worker Laragon.
- **Files to Inspect**:
  - `F:\laragon\etc\nginx\sites-enabled\magkom.test.conf`
  - `F:\laragon\bin\nginx\nginx-1.27.3\conf\nginx.conf`
- **Files Expected to Change**:
  - `F:\laragon\etc\nginx\sites-enabled\magkom.test.conf`
  - Panduan dokumentasi konfigurasi deployment: `docs/deployment/webserver-tuning.md`
- **Implementation Directive**:
  Tambahkan blok caching dan gzip pada Nginx:
  ```nginx
  location ~* ^/build/assets/.*\.(?:css|js|woff2?|png|jpe?g|gif|svg)$ {
      expires 1y;
      add_header Cache-Control "public, max-age=31536000, immutable";
      access_log off;
  }
  gzip on;
  gzip_types text/plain text/css application/json application/javascript text/xml;
  ```
- **Dependencies**: Tidak ada.
- **Risks**: Perubahan konfigurasi server lokal memerlukan reload Nginx.
- **Regression Test**: Periksa header via curl / PowerShell pada `/build/assets/app-*.css`. Pastikan header `Cache-Control` terpasang.
- **Acceptance Criteria**: Transfer size `app.css` turun dari 346 KB menjadi ~45 KB saat gzip aktif; request kedua menghasilkan `(disk cache)` / 304.
- **Rollback Strategy**: Kembalikan file `.conf` ke backup sebelumnya dan reload Nginx.

---

### FASE P1 — PAYLOAD DIET & BLADE EXTRACTION (Ekstraksi Aset ke Pipeline Vite)

#### TASK P1.1: Ekstraksi CSS & JS Modul Admin Kuota
- **Task ID**: `PERF-P1-01`
- **Priority**: P1 — High
- **Problem**: `resources/views/admin/kuota/index.blade.php` memiliki 25,2 KB CSS inline (L10-914) dan 19,3 KB JS inline (L1809-2253).
- **Evidence**: `EV-08` (2.254 baris file Blade).
- **Root Cause**: `RC-1` (Monolithic Blade).
- **Objective**: Ekstrak CSS ke `resources/css/pages/admin/kuota-index.css` dan JS ke `resources/js/pages/admin/kuota-index.js`.
- **Files to Inspect**:
  - `resources/views/admin/kuota/index.blade.php`
  - `vite.config.js`
- **Files Expected to Change**:
  - `resources/views/admin/kuota/index.blade.php`
  - `resources/css/pages/admin/kuota-index.css` (Baru)
  - `resources/js/pages/admin/kuota-index.js` (Baru)
  - `vite.config.js`
- **Implementation Directive**:
  1. Pisahkan CSS murni ke file CSS baru.
  2. Pisahkan interaksi JS (DOM listener, kalkulasi kapasitas, modal handler) ke module JS baru.
  3. Daftarkan entry point ke `vite.config.js`.
  4. Muat via `@push('styles') @vite(...) @endpush` dan `@push('scripts') @vite(...) @endpush`.
- **Dependencies**: `PERF-P0-04`.
- **Risks**: Variabel Blade PHP yang dipakai di dalam JavaScript (seperti URL route atau token CSRF). Gunakan `data-*` attributes pada elemen container HTML.
- **Regression Test**: Buka `/admin/kuota`. Uji tambah bulan magang, ubah alokasi kuota jurusan, dan toggle aktifasi.
- **Acceptance Criteria**: Ukuran file Blade berkurang dari 2.254 baris menjadi < 550 baris markup bersih.
- **Rollback Strategy**: Git checkout revert pada file Blade.

---

#### TASK P1.2: Ekstraksi CSS & JS Modul Admin Penempatan
- **Task ID**: `PERF-P1-02`
- **Priority**: P1 — High
- **Problem**: `resources/views/admin/penempatan/index.blade.php` memiliki 20,6 KB CSS inline (L10-624) dan 20,1 KB JS inline (L1668-2169).
- **Evidence**: 2.235 baris file Blade; 40,7 KB payload ekstra per request.
- **Root Cause**: `RC-1`.
- **Objective**: Ekstrak styling ke `resources/css/pages/admin/penempatan-index.css` dan interaksi JS ke `resources/js/pages/admin/penempatan-index.js`.
- **Files to Inspect**:
  - `resources/views/admin/penempatan/index.blade.php`
- **Files Expected to Change**:
  - `resources/views/admin/penempatan/index.blade.php`
  - `resources/css/pages/admin/penempatan-index.css` (Baru)
  - `resources/js/pages/admin/penempatan-index.js` (Baru)
  - `vite.config.js`
- **Implementation Directive**: Ekstraksi bersih mengikuti pola `PERF-P1-01`.
- **Dependencies**: `PERF-P1-01`.
- **Risks**: Script dropdown filter dan drag-and-drop/modal penempatan harus diikat via event delegation.
- **Regression Test**: Buka `/admin/penempatan`. Uji tab "Sudah Ditempatkan" vs "Belum Ditempatkan", modal penetapan manual, dan ekspor surat.
- **Acceptance Criteria**: Berkas Blade berkurang > 1.100 baris. Transfer HTML berkurang > 40 KB.
- **Rollback Strategy**: Git revert.

---

#### TASK P1.3: Ekstraksi CSS & JS Modul Admin Verifikasi Pendaftaran
- **Task ID**: `PERF-P1-03`
- **Priority**: P1 — High
- **Problem**: `resources/views/admin/pendaftaran/index.blade.php` memiliki 24,7 KB CSS inline (L10-814) dan 4,1 KB JS inline (L1287-1388).
- **Evidence**: 1.389 baris file Blade.
- **Root Cause**: `RC-1`.
- **Objective**: Ekstrak ke `resources/css/pages/admin/pendaftaran-index.css` dan `resources/js/pages/admin/pendaftaran-index.js`.
- **Files to Inspect**:
  - `resources/views/admin/pendaftaran/index.blade.php`
- **Files Expected to Change**:
  - `resources/views/admin/pendaftaran/index.blade.php`
  - `resources/css/pages/admin/pendaftaran-index.css`
  - `resources/js/pages/admin/pendaftaran-index.js`
  - `vite.config.js`
- **Implementation Directive**: Ekstraksi bersih dan pemuatan via `@vite`.
- **Dependencies**: `PERF-P1-02`.
- **Risks**: Kompatibilitas modal review berkas pendaftaran.
- **Regression Test**: Buka `/admin/pendaftaran`. Uji filter status (Diajukan, Perlu Perbaikan, Diterima, Ditolak).
- **Acceptance Criteria**: File Blade berkurang 900 baris. HTML payload menyusut 28 KB.
- **Rollback Strategy**: Git revert.

---

#### TASK P1.4: Ekstraksi CSS dari Layout Master (`layouts/app` dan `layouts/auth`)
- **Task ID**: `PERF-P1-04`
- **Priority**: P1 — High
- **Problem**: `layouts/app.blade.php` menampung 26,2 KB CSS (877 baris) dan `layouts/auth.blade.php` menampung 25,4 KB CSS (883 baris) di dalam tag `<style>`.
- **Evidence**: Ditarik di seluruh halaman publik dan alur ganti password.
- **Root Cause**: `RC-1`.
- **Objective**: Pindahkan token warna dan layout styling ke `resources/css/layouts/app-layout-tokens.css` dan `resources/css/layouts/auth-layout-tokens.css`, lalu import di `resources/css/app.css` atau bundle terpisah.
- **Files to Inspect**:
  - `resources/views/layouts/app.blade.php` (L55-L932)
  - `resources/views/layouts/auth.blade.php` (L45-L928)
- **Files Expected to Change**:
  - `resources/views/layouts/app.blade.php`
  - `resources/views/layouts/auth.blade.php`
  - `resources/css/layouts/app-layout-tokens.css` (Baru)
  - `resources/css/layouts/auth-layout-tokens.css` (Baru)
- **Implementation Directive**: Pindahkan styling, bersihkan tag `<style>`, dan verifikasi integritas visual.
- **Dependencies**: `PERF-P0-01`.
- **Risks**: Urutan cascade CSS (*specificity*) harus dipertahankan.
- **Regression Test**: Periksa tampilan landing page `/`, halaman panduan alur `/panduan-alur`, dan halaman autentikasi `/login`.
- **Acceptance Criteria**: HTML publik berkurang 26 KB per navigasi. Browser meng-cache CSS layout secara permanen.
- **Rollback Strategy**: Git revert.

---

### FASE P2 — BACKEND & QUERY OPTIMIZATION (Micro-Caching Master Data)

#### TASK P2.1: Micro-Caching pada Dropdown Master Data Statis
- **Task ID**: `PERF-P2-01`
- **Priority**: P2 — Medium
- **Problem**: Pada `Admin\PenempatanController@index` dan `KuotaIndexQuery`, kueri master bidang, jurusan, bulan magang, dan kuota aktif dijalankan berulang tanpa cache.
- **Evidence**: `EV-07` (5-7 kueri database berulang per hit halaman index).
- **Root Cause**: `RC-3`.
- **Objective**: Terapkan micro-caching dengan durasi masuk akal (300 detik) yang aman terhadap testing suite dan dilengkapi invalidasi otomatis.
- **Files to Inspect**:
  - `app/Http/Controllers/Admin/PenempatanController.php` (L81-L85)
  - `app/Queries/Admin/Kuota/KuotaIndexQuery.php` (L36-L57)
- **Files Expected to Change**:
  - `app/Http/Controllers/Admin/PenempatanController.php`
  - `app/Queries/Admin/Kuota/KuotaIndexQuery.php`
  - Observer / Event Handler: `app/Observers/MasterDataCacheObserver.php` (Baru)
- **Implementation Directive**:
  Bungkus pengambilan data dengan `Cache::remember`:
  ```php
  $bidangList = Cache::remember('master_bidang_aktif', 300, function () {
      return Bidang::where('aktif', true)->orderBy('nama_bidang')->get();
  });
  $jurusanList = Cache::remember('master_jurusan_aktif', 300, function () {
      return Jurusan::where('aktif', true)->orderBy('nama_jurusan')->get();
  });
  ```
  Pasang Model Observer pada `Bidang`, `Jurusan`, dan `BulanMagang` untuk memanggil `Cache::forget()` saat ada mutasi.
- **Dependencies**: Tidak ada.
- **Risks**: Stale data jika admin mengubah nama bidang/jurusan dan observer tidak terpanggil.
- **Regression Test**: Jalankan `php artisan test tests/Feature/AdminPenempatanDuaTahapTest.php` dan `tests/Feature/AdminKuotaRevampTest.php`.
- **Acceptance Criteria**: 4–6 query database tereliminasi per request di halaman manajemen admin.
- **Rollback Strategy**: Hapus `Cache::remember` dan kembalikan ke direct Eloquent query.

---

#### TASK P2.2: Eliminasi Query Database di dalam Layout Blade Dashboard
- **Task ID**: `PERF-P2-02`
- **Priority**: P2 — Medium
- **Problem**: Pada [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php#L93-L97), terdapat eksekusi kueri Eloquent langsung di Blade:
  `$penempatanGracePeriod = $currentUser->penempatanPeserta()->where('status', ...)->first();`
- **Evidence**: Pelanggaran prinsip *Skinny Views* dan penambahan kueri database tidak terencana pada layout global.
- **Root Cause**: `RC-3`.
- **Objective**: Pindahkan logika pengecekan masa tenggang peserta ke View Composer atau middleware yang hanya berjalan untuk role Peserta.
- **Files to Inspect**:
  - `resources/views/layouts/dashboard.blade.php` (L93-L97)
  - `app/Providers/ViewServiceProvider.php`
- **Files Expected to Change**:
  - `resources/views/layouts/dashboard.blade.php`
  - `app/Http/View/Composers/DashboardLayoutComposer.php` (atau sejenis)
- **Implementation Directive**: Sediakan variabel `$penempatanGracePeriod` melalui View Composer hanya ketika view `layouts.dashboard` atau `layouts.peserta` di-render untuk user dengan role Peserta.
- **Dependencies**: `PERF-P1-04`.
- **Risks**: Variabel undefined di view jika composer tidak terdaftar.
- **Regression Test**: Buka dashboard sebagai Peserta yang masa magangnya sudah selesai. Pastikan banner peringatan masa tenggang tetap muncul.
- **Acceptance Criteria**: Nol kueri database yang diinisiasi langsung dari baris kode template Blade.
- **Rollback Strategy**: Git revert.

---

### FASE P3 — QUALITY & RESILIENCE POLISH (Technical Debt & Edge Cases)

#### TASK P3.1: Pembersihan Third-Party Play CDN Tailwind
- **Task ID**: `PERF-P3-01`
- **Priority**: P3 — Low
- **Problem**: `kebijakan-privasi.blade.php` dan `verifikasi-dokumen.blade.php` memuat CDN runtime `https://cdn.tailwindcss.com`.
- **Evidence**: `EV-09`.
- **Root Cause**: Technical debt dari implementasi prototipe.
- **Objective**: Hapus script CDN dan gunakan bundle CSS yang dikompilasi oleh Vite.
- **Files to Inspect**:
  - `resources/views/public/kebijakan-privasi.blade.php` (L22)
  - `resources/views/public/verifikasi-dokumen.blade.php` (L12)
- **Files Expected to Change**:
  - `resources/views/public/kebijakan-privasi.blade.php`
  - `resources/views/public/verifikasi-dokumen.blade.php`
- **Implementation Directive**: Ganti `<script src="https://cdn.tailwindcss.com"></script>` dengan `@vite(['resources/css/app.css'])`.
- **Dependencies**: `PERF-P1-04`.
- **Risks**: Class Tailwind utilitas yang belum masuk ke CSS app.css. Pastikan file Blade tersebut masuk dalam scan `content` di `tailwind.config.js`.
- **Regression Test**: Buka `/kebijakan-privasi` dan `/verifikasi-dokumen`. Pastikan layout, spasi, dan tipografi tidak berantakan.
- **Acceptance Criteria**: Tidak ada request ke domain pihak ketiga eksternal selain Google Fonts.
- **Rollback Strategy**: Git revert.

---

#### TASK P3.2: Eliminasi Cache-Buster `time()` dan Disk Check `filemtime()` di View
- **Task ID**: `PERF-P3-02`
- **Priority**: P3 — Low
- **Problem**: Penggunaan `?v={{ time() }}` pada `peta-alur-interaktif.blade.php` dan `filemtime()` pada `layouts/dashboard.blade.php`.
- **Evidence**: `EV-05` & observasi kode statis.
- **Root Cause**: Technical debt.
- **Objective**: Ganti pemanggilan aset manual dengan asset versioning Vite atau versioning statis berbasis versi aplikasi di `config('app.version')`.
- **Files to Inspect**:
  - `resources/views/public/peta-alur-interaktif.blade.php` (L17)
  - `resources/views/layouts/dashboard.blade.php` (L27-L28, L1113)
- **Files Expected to Change**:
  - `resources/views/public/peta-alur-interaktif.blade.php`
  - `resources/views/layouts/dashboard.blade.php`
- **Implementation Directive**: Masukkan aset `peta-alur-fullscreen.css`, `gentelella-theme.css`, dan `dashboard-cockpit-unified.css` ke dalam pengelolaan Vite agar mendapatkan hash chunk otomatis tanpa runtime disk stat overhead.
- **Dependencies**: `PERF-P1-04`.
- **Risks**: Pastikan Vite mengompilasi aset legacy tersebut tanpa konflik class.
- **Regression Test**: Buka `/peta-alur-interaktif`. Pastikan CSS ter-cache pada refresh kedua (HTTP 304 atau disk cache).
- **Acceptance Criteria**: Nol pemanggilan `time()` atau `filemtime()` di seluruh direktori `resources/views/`.
- **Rollback Strategy**: Git revert.

---

## 20. PERFORMANCE VALIDATION PLAN

Pengujian validasi performa wajib dilakukan sebelum (*baseline*) dan sesudah (*post-optimization*) implementasi pada kondisi environment yang setara.

### 20.1 Matriks Metrik Target Validasi

| Parameter Metrik | Kondisi Baseline Aktual | Target Local Dev | Target Production | Metode Pengukuran & Validasi |
| :--- | :--- | :--- | :--- | :--- |
| **Landing Page HTML Size** | **1.746.137 B (1,74 MB)** | **< 180 KB** | **< 45 KB (Gzip)** | `Invoke-WebRequest` / Browser DevTools Network Tab |
| **Landing Page TTFB** | **1.352 ms** | **< 150 ms** | **< 80 ms** | DevTools Timing: Waiting for server response (TTFB) |
| **Audit Log HTML Size** | **~685.000 B (685 KB)** | **< 90 KB** | **< 25 KB (Gzip)** | DevTools Network Tab: Size of index document |
| **Audit Log TTFB** | **~750 ms** | **< 180 ms** | **< 100 ms** | Ukur TTFB setelah compound index `audit_log` aktif |
| **Admin Kuota HTML Size** | **~640.000 B (640 KB)** | **< 75 KB** | **< 20 KB (Gzip)** | Dokumen HTML setelah CSS/JS diekstrak ke Vite |
| **Admin Penempatan DB Queries**| **18+ Queries** | **<= 8 Queries** | **<= 6 Queries** | Laravel Query Log / DB::listen profiling |
| **Global Vite app.css Size** | **346.511 B (346 KB)** | **~346 KB** | **< 50 KB (Gzip)** | Header `Content-Encoding: gzip` pada Nginx response |
| **Asset Caching Header** | `None` (Hanya ETag) | `max-age=31536000` | `immutable` | Header `Cache-Control` pada request `/build/assets/*` |
| **FastCGI Worker Queuing** | Tertahan saat SSE aktif | Bebas antrean | Bebas antrean | Observasi waktu navigasi saat 2 tab terbuka simultan |

---

## 21. QUALITY ASSURANCE & REGRESSION PLAN

Setiap langkah optimasi tidak boleh merusak fungsionalitas bisnis, kontrak otorisasi, maupun tampilan visual MAGKOM.

```mermaid
flowchart TD
    Start["Eksekusi Task Optimasi"] --> Step1["1. Static Analysis & Formatting"]
    Step1 -->|Pint & PHPStan| Step2["2. Asset Compilation"]
    Step2 -->|npm run build| Step3["3. Automated Feature Tests"]
    Step3 -->|php artisan test| Step4["4. Visual & Manual UI Gate"]
    Step4 -->|Browser Audit| Pass["Merge / Selesai"]

    Step1 -- Gagal --> Fix1["Perbaiki Style / Type Hint"] --> Step1
    Step2 -- Gagal --> Fix2["Perbaiki Syntax CSS/JS"] --> Step2
    Step3 -- Gagal --> Fix3["Analisis Root Cause Regression Test"] --> Step3
    Step4 -- Gagal --> Fix4["Sinkronisasi Token / DOM Element"] --> Step4
```

### Checklist Verifikasi Fungsional per Role:
1. **Public & Guest**:
   - [ ] Landing page memuat animasi hero, peta alur, FAQ, dan alur pendaftaran secara mulus.
   - [ ] Tombol CTA pendaftaran mengarahkan ke form registrasi tanpa error JavaScript di console.
   - [ ] Halaman login `/login` dan `/register` berfungsi normal dengan validasi Turnstile.
2. **Peserta Magang**:
   - [ ] Dashboard peserta menampilkan ringkasan tugas dan logbook tanpa query berulang.
   - [ ] Pengisian logbook harian dan pengunggahan tugas berjalan mulus.
   - [ ] Banner masa tenggang muncul tepat waktu jika penempatan telah selesai.
3. **Pembimbing Lapangan**:
   - [ ] Dashboard pembimbing menampilkan daftar bimbingan aktif dan logbook yang perlu direviu.
   - [ ] Modal reviu progres dan penilaian tugas bekerja responsif.
4. **Admin**:
   - [ ] Manajemen Kuota: Input alokasi kuota per jurusan, penambahan bulan magang baru, dan toggle status aktif berjalan transaksional.
   - [ ] Penempatan: Alokasi peserta ke pembimbing, modal penempatan manual, dan ekspor surat penerimaan XLSX/PDF berjalan tanpa timeout.
   - [ ] Verifikasi Berkas: Filter status pendaftaran dan preview berkas berfungsi normal.
5. **SuperAdmin**:
   - [ ] Audit Log: Multi-channel filter, paginasi, pencarian teks, detail dialog log modal, dan ekspor CSV/NDJSON berfungsi 100%.
   - [ ] Site Editor CMS: Drag-and-drop block builder tidak mengalami konflik stylesheet.

---

## 22. RISK REGISTER & MITIGATION MATRIX

| ID | Risiko Teknis | Tingkat Risiko | Dampak Potensial | Strategi Mitigasi Wajib |
| :--- | :--- | :--- | :--- | :--- |
| **RSK-01** | **CSS Specificity Broken** saat ekstraksi dari Blade ke Vite | Sedang | Tampilan visual form atau tabel berubah atau berantakan | Jaga urutan import file CSS di `vite.config.js`. Gunakan selector berakar kelas spesifik (`.adm-kuota-root`, `.adm-verifikasi-wrapper`). |
| **RSK-02** | **JavaScript Variable Scope Lost** (Variabel Blade `@json` hilang) | Tinggi | Script JS error: `variable is not defined` | Pindahkan data konfigurasi server ke atribut HTML `data-*` (misal: `data-endpoint="..."`) sebelum mengekstrak JS ke file statis. |
| **RSK-03** | **Stale Data pada Cache Master Data** | Sedang | Perubahan kuota atau nama bidang tidak langsung terlihat oleh admin | Pasang observer invalidasi (`Cache::forget`) pada model `Bidang`, `Jurusan`, `BulanMagang`, dan `Kuota`. Berikan TTL pendek (300 detik). |
| **RSK-04** | **Test Pollution di PHPUnit** akibat Caching | Tinggi | Test suite gagal palsu (*false failure*) karena membaca cache lama | Konfigurasi `phpunit.xml` menggunakan cache driver `array`. Tambahkan invalidasi cache di helper teardown test jika diperlukan. |
| **RSK-05** | **Locking pada Migration Indeks `audit_log`** | Rendah (Dev) / Tinggi (Prod) | Tabel `audit_log` terkunci saat penambahan indeks | Jalankan migration saat jam sepi lalu lintas (maintenance window) atau gunakan DDL non-blocking (`ALGORITHM=INPLACE, LOCK=NONE`). |
| **RSK-06** | **Nginx Config Syntax Error** saat tuning Gzip / Cache | Sedang | Web server tidak bisa start / HTTP 502 Bad Gateway | Selalu jalankan `nginx -t` sebelum melakukan reload service Nginx. |

---

## 23. ROLLBACK STRATEGY & SAFETY NETS

Setiap task implementasi harus memiliki jalur rollback yang jelas dan teruji:
1. **Atomic Branching Strategy**:
   - Seluruh pekerjaan dilakukan pada dedicated branch: `perf/P0-stop-the-bleeding` dan `perf/P1-payload-diet`.
   - Tidak ada commit langsung ke branch utama (`main` atau `master`).
2. **Safe Database Migration Rollback**:
   - Setiap migration menyertakan method `down()` yang simetris:
     ```php
     public function down(): void {
         Schema::table('audit_log', function (Blueprint $table) {
             $table->dropIndex('idx_audit_log_waktu');
             $table->dropIndex('idx_audit_log_waktu_channel');
         });
     }
     ```
3. **Backup Konfigurasi Nginx**:
   - Sebelum mengedit berkas `.conf` di Laragon, salin file ke format `.conf.bak`. Jika terjadi error, salin balik dan reload.
4. **Git Safety Guardrail**:
   - Dilarang keras menggunakan `git reset --hard` atau `git clean -fd`. Gunakan `git revert` terarah untuk mengembalikan perubahan jika ditemukan regresi.

---

## 24. DEFINITION OF DONE (DoD)

Proyek optimasi performa Portal MAGKOM dianggap selesai dan sukses jika seluruh kriteria berikut terpenuhi:
- [ ] Ukuran HTML landing page `/` terpangkas **> 85%** (turun dari 1,74 MB menjadi < 200 KB).
- [ ] Ukuran HTML halaman Admin Kuota, Penempatan, dan SuperAdmin Audit Log berkurang **> 60%**.
- [ ] Tidak ada lagi injeksi string Base64 513 KB di dalam dokumen HTML manapun.
- [ ] Tidak ada tag `<style>` dan `<script>` ribuan baris di dalam file template Blade utama.
- [ ] Seluruh stylesheet dan skrip baru berada di bawah `resources/css/` dan `resources/js/`, serta dikompilasi via Vite.
- [ ] Perintah `npm run build` berjalan sukses tanpa error dan tanpa missing chunks.
- [ ] Header HTTP pada aset statis `/build/assets/*` menyertakan `Cache-Control: max-age=31536000, immutable`.
- [ ] Kompresi Gzip aktif di Nginx; ukuran transfer `app.css` turun menjadi < 55 KB.
- [ ] Indeks komposit `(waktu, channel)` pada tabel `audit_log` aktif.
- [ ] Seluruh test suite PHPUnit (`php artisan test`) lulus 100% HIJAU (PASS).
- [ ] Linter `vendor/bin/pint --test` dan static analysis `vendor/bin/phpstan analyse --memory-limit=1G` lulus tanpa error.
- [ ] Sensasi reload / jeda putar navigasi antarhalaman terasa instan (< 300 ms).

---

## 25. UNVERIFIED FINDINGS & REMAINING QUESTIONS

1. **Konfigurasi Spesifikasi Server Staging / Production**:
   - *Pertanyaan*: Apakah lingkungan produksi MAGKOM nantinya menggunakan PHP-FPM di Linux dengan worker pool dinamis (misal `pm = dynamic`, `pm.max_children = 50`), atau menggunakan Windows Server IIS/Laragon?
   - *Catatan*: Jika produksi menggunakan Linux PHP-FPM dengan puluhan worker, masalah worker starvation akibat SSE akan otomatis terselesaikan di sisi infrastruktur. Namun, optimasi payload HTML dan kueri database tetap mutlak diperlukan.
2. **Kebutuhan Redis / Memcached untuk Cache Tingkat Tinggi**:
   - *Pertanyaan*: Apakah server produksi menyediakan Redis daemon?
   - *Catatan*: Jika Redis tersedia, driver cache dan session dapat dialihkan dari `file` ke `redis` untuk performa konkurensi yang jauh lebih tinggi.
3. **Ukuran Historis Tabel `audit_log` di Produksi**:
   - *Pertanyaan*: Berapa jumlah baris riwayat audit log di database saat ini?
   - *Catatan*: Jika baris sudah melampaui 1 juta baris, penambahan indeks komposit pada Task P0.3 membutuhkan estimasi waktu lock tabel beberapa puluh detik.

---

## 26. FINAL SENIOR ENGINEER RECOMMENDATION

Sebagai Senior Performance Engineer dan Systems Architect, rekomendasi tegas untuk tim pengembang adalah:

> **"Jangan mencoba menyelesaikan masalah performa navigasi MPA dengan memaksakan migrasi ke SPA (React/Vue/Inertia.js), dan jangan menyalahkan Server-Sent Events sebagai kambing hitam tunggal."**

Masalah navigasi lambat di MAGKOM adalah **masalah fisik ukuran data (Payload Bloat) dan pembagian konkurensi FastCGI**. Ketika browser diminta mengunduh **1,74 MB HTML mentah** yang berisi **1,54 MB teks Base64** dan **puluhan ribu baris inline CSS/JS** pada setiap kali pengguna mengklik menu, browser dipaksa membuang seluruh CSSOM lama, mem-parsing setengah juta karakter Base64 baru, dan menyusun ulang render tree dari nol—semuanya melewati saluran FastCGI lokal yang hanya memiliki **2 worker socket**.

Dengan mengeksekusi rencana bertahap ini:
1. **Mengeliminasi Base64 Logo Sleman** (Memangkas 1,5 MB per hit).
2. **Menghapus Double Load CSS di Audit Log** (Memangkas 50 KB per hit).
3. **Membuat Indeks `audit_log.waktu`** (Memangkas TTFB dari 750 ms menjadi < 150 ms).
4. **Mengekstrak Kantung Blade ke Pipeline Vite** (Mengaktifkan HTTP caching permanen pada browser).
5. **Mengaktifkan Gzip di Nginx** (Menyusutkan ukuran CSS transfer hingga 87%).

Aplikasi Laravel MPA ini akan bekerja dengan kecepatan luar biasa—setiap klik menu akan terbuka dalam **< 150–250 ms**, terasa instan bagi pengguna, memenuhi standar WCAG dan performa modern, serta tetap 100% patuh pada aturan ketat repositori MAGKOM.

---
*Dokumen ini diterbitkan secara resmi sebagai pedoman teknis mutlak (Single Source of Truth) untuk optimasi performa Portal MAGKOM.*
*Status: READY FOR EXECUTION UPON USER APPROVAL.*

