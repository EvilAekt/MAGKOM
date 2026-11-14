# 📋 SENIOR DEVELOPER TECHNICAL DIRECTIVE & PRD
## AUDIT INVESTIGATIF & RENCANA KERJA OPTIMASI KECEPATAN NAVIGASI WEB (PORTAL MAGKOM)

- **Dokumen ID**: `MAGKOM-PERF-AUDIT-2026-11-13`
- **Target Audience**: Junior Developer / AI Model (Gemini 3.8 Flash, Autonomous Agent)
- **Tingkat Prioritas**: P0 - Critical Performance Degradation
- **Mode Eksekusi Saat Ini**: 🟡 **AUDIT & PLAN ONLY (READ-ONLY)** — Belum ada kode yang diubah.
- **Lokasi Berkas**: [`docs/plans/2026-11-13-audit-performa-dan-optimasi-kecepatan-navigasi.md`](file:///g:/MAGKOM/docs/plans/2026-11-13-audit-performa-dan-optimasi-kecepatan-navigasi.md)

---

## 🎯 1. Ringkasan Eksekutif (TL;DR 60 Detik)

| Parameter | Keterangan Teknis |
| :--- | :--- |
| **Gejala Masalah** | Pengguna merasakan website sangat berat. Setiap kali mengklik menu atau halaman baru, terjadi proses reload yang lambat sekali (layar macet berputar 1–3 detik sebelum halaman terbuka). |
| **Akar Masalah #1 (Server Choke)** | **Worker Starvation pada Single-Threaded Server (Windows PHP CLI)**: Saat pengujian lokal menggunakan `php artisan serve`, PHP bekerja secara single-threaded. Koneksi streaming SSE (`EventSource`) yang tidak terputus saat navigasi menyumbat worker, memaksa request halaman berikutnya mengantre di buffer TCP sampai timeout/terputus. |
| **Akar Masalah #2 (Payload Bloat)** | **Monolithic Blade HTML (Uncached CSS/JS)**: Masih ditemukan puluhan ribu karakter (20–50 KB) CSS dan JavaScript inline yang tertanam langsung di file template Blade (seperti `superadmin/audit-log/_styles.blade.php`, `admin/kuota/index.blade.php`, `admin/penempatan/index.blade.php`, dan `layouts/app.blade.php`). Browser **tidak dapat meng-cache** styling ini, memaksa download ulang ratusan KB HTML mentah dan re-parsing CSSOM di setiap klik. |
| **Akar Masalah #3 (Query Duplication)** | **Master Data Sinkronus**: Setiap pembukaan halaman modul administrasi memicu 6–9 query database untuk data referensi statis (daftar bidang, jurusan, bulan magang aktif) tanpa adanya *micro-caching*. |
| **Solusi Arsitektural** | Terapkan 3 Fase Perbaikan: (1) Guardrail pemutusan koneksi SSE saat navigasi, (2) Diet payload Blade dengan mengekstrak CSS/JS ke pipeline Vite agar browser caching aktif 100%, (3) Micro-caching data referensi statis di backend. |

---

## 🔬 2. Hasil Audit Komprehensif & Bukti Empiris (4 Pilar RCA)

Berdasarkan inspeksi statis dan dinamis pada codebase MagKom, berikut adalah temuan audit empiris:

### 2.1 Pilar 1: Jaringan & Background Request Loop (Network Bottlenecks)
- **Status Audit**:
  - Sebelumnya terdapat middleware `RecordRoutePerformance` yang mencatat setiap request ke database dan mem-broadcast event `telemetri_route`, yang memicu request loop konfirmasi baca balik (`POST /realtime/read/{id}`).
  - **Hasil**: Fitur telemetri tersebut telah resmi didecommission dan dibersihkan dari `Kernel.php`, sehingga server terbebas dari badai request telemetri.
  - Namun, di sisi client (`resources/js/services/realtime.js`), tetap diperlukan guardrail agar event SSE reguler (seperti notifikasi tugas atau pendaftaran) tidak pernah memicu cascade HTTP request otomatis secara beruntun.

### 2.2 Pilar 2: Worker Concurrency & Lifecycle Koneksi (Local Development Starvation)
- **Bukti Empiris**:
  - Server development bawaan PHP (`php artisan serve`) di Windows berjalan dalam model single-process non-forking.
  - Ketika browser membuka koneksi Server-Sent Events (`/realtime/stream`), worker PHP terkunci untuk melayani stream tersebut.
  - Jika pengguna mengklik link menu navigasi baru tanpa memutus koneksi `EventSource` sebelumnya, request HTTP halaman baru akan **tertahan di antrean (stalled / pending)** selama 1–3 detik hingga timeout koneksi lama terjadi.
- **Kebutuhan Solusi**:
  - Penegakan pemutusan koneksi instan via listener `beforeunload` dan `pagehide` di semua master layout.

### 2.3 Pilar 3: Payload Diet & Rendering Frontend (The "Monolithic HTML" Choke)
Pemeriksaan ukuran dan isi berkas Blade menemukan kantong-kantong kode raksasa yang belum termigrasi ke pipeline aset:

| Lokasi Berkas Blade | Ukuran / Baris | Isi Inline yang Ditemukan | Dampak Performa |
| :--- | :--- | :--- | :--- |
| `resources/views/superadmin/audit-log/partials/_styles.blade.php` | **49,7 KB** (2.365 baris) | Blok `<style>` CSS murni audit log | Di-load ulang setiap buka audit log, zero browser cache |
| `resources/views/admin/kuota/index.blade.php` | **2.255 baris** | 25,2 KB inline CSS + 19,3 KB inline JS | 44,5 KB payload ekstra per request |
| `resources/views/admin/penempatan/index.blade.php` | **2.236 baris** | 20,6 KB inline CSS + 20,1 KB inline JS | 40,7 KB payload ekstra per request |
| `resources/views/layouts/app.blade.php` | **1.278 baris** | 26,2 KB inline CSS | Ditarik di seluruh halaman publik/landing |
| `resources/views/layouts/auth.blade.php` | **1.058 baris** | 25,4 KB inline CSS | Ditarik di seluruh alur login/register |

> [!WARNING]
> **Pelanggaran Aturan Repositori**:
> Sesuai dokumen aturan proyek [`.agents/rules/AGENTS.md`](file:///g:/MAGKOM/.agents/rules/AGENTS.md) Bagian 3: *"Blade bukan tempat menulis CSS atau JavaScript. Blade read-only secara default untuk styling."*
> Menanamkan puluhan kilobyte CSS/JS di Blade merusak performa karena browser tidak dapat memanfaatkan header `Cache-Control: public, max-age=31536000` dari Vite bundle.

### 2.4 Pilar 4: Efisiensi Query & Backend
Pada controller modul administrasi, misalnya [`Admin\PenempatanController@index`](file:///g:/MAGKOM/app/Http/Controllers/Admin/PenempatanController.php):
```php
$bulanList = BulanMagang::orderBy('bulan_mulai', 'desc')->get();
$slotKhususList = SlotKhusus::where('aktif', true)->orderBy('bulan_mulai')->get();
$bidangList = Bidang::where('aktif', true)->orderBy('nama_bidang')->get();
$jurusanList = Jurusan::where('aktif', true)->orderBy('nama_jurusan')->get();
$kuotaList = Kuota::with(['bulanMagang', 'jurusan'])->where('aktif', true)->get();
```
Query-query ini menarik data referensi dropdown yang sangat statis. Tanpa *micro-caching*, setiap klik filter atau pagination mengeksekusi 5–7 query SQL yang sama berulang-ulang ke disk database.

---

## 💡 3. Perumusan Rencana Solusi Bertahap (Step-by-Step Directives)

Junior Developer atau AI penerus wajib mengikuti 3 Fase implementasi di bawah ini secara ketat:

### FASE 1: Stop The Bleeding (Lifecycle Koneksi & Concurrency Guardrail)
> **Tujuan**: Menjamin bahwa navigasi antarhalaman tidak pernah tertahan oleh koneksi SSE lama.

- [ ] **Task 1.1: Pastikan Graceful Disconnect SSE pada Navigasi**
  - **File**: [`resources/js/services/realtime.js`](file:///g:/MAGKOM/resources/js/services/realtime.js) dan [`public/js/realtime-sse.js`](file:///g:/MAGKOM/public/js/realtime-sse.js)
  - **Implementasi**:
    Pastikan listener berikut terdaftar di konstruktor:
    ```javascript
    window.addEventListener('beforeunload', () => this.disconnect());
    window.addEventListener('pagehide', () => this.disconnect());
    ```
  - **Efek**: Saat user mengklik menu apa pun, EventSource langsung ditutup sebelum browser mengirim HTTP request halaman baru. Worker server PHP langsung bebas melayani halaman baru dalam < 100 ms.

---

### FASE 2: Payload Diet (Pembersihan Kantong CSS & JS Inline ke Pipeline Vite)
> **Tujuan**: Memangkas ukuran dokumen HTML mentah hingga 80% dan mengaktifkan browser HTTP caching permanen.

- [ ] **Task 2.1: Ekstraksi CSS Audit Log SuperAdmin**
  - **File Sumber**: [`resources/views/superadmin/audit-log/partials/_styles.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/audit-log/partials/_styles.blade.php) (2.365 baris CSS).
  - **Tindakan**: Pindahkan seluruh isi CSS ke [`resources/css/pages/superadmin/audit-log.css`](file:///g:/MAGKOM/resources/css/pages/superadmin/audit-log.css) dan import di `resources/css/app.css`. Kosongkan atau hapus file partial `_styles.blade.php`.
  - **Hasil**: Halaman Audit Log SuperAdmin berkurang 49,7 KB per load.

- [ ] **Task 2.2: Ekstraksi CSS & JS Modul Admin Kuota & Penempatan**
  - **File Sumber**:
    - [`resources/views/admin/kuota/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/kuota/index.blade.php) (25 KB CSS, 19 KB JS)
    - [`resources/views/admin/penempatan/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/penempatan/index.blade.php) (20 KB CSS, 20 KB JS)
  - **Tindakan**:
    - Pindahkan blok `<style>` ke `resources/css/pages/admin/kuota-index.css` dan `resources/css/pages/admin/penempatan-index.css`.
    - Pindahkan blok `<script>` ke `resources/js/pages/admin/kuota-index.js` dan `resources/js/pages/admin/penempatan-index.js`.
    - Daftarkan ke `resources/css/app.css` atau entry point Vite.
  - **Hasil**: Ukuran file Blade berkurang dari 2.200 baris menjadi < 500 baris markup murni.

- [ ] **Task 2.3: Ekstraksi CSS dari Layout Master (`app.blade.php` & `auth.blade.php`)**
  - **File Sumber**:
    - [`resources/views/layouts/app.blade.php`](file:///g:/MAGKOM/resources/views/layouts/app.blade.php) (26 KB CSS)
    - [`resources/views/layouts/auth.blade.php`](file:///g:/MAGKOM/resources/views/layouts/auth.blade.php) (25 KB CSS)
  - **Tindakan**: Pindahkan ke `resources/css/layouts/app-layout-tokens.css` dan `resources/css/layouts/auth-layout-tokens.css`, lalu import di `resources/css/app.css`.
  - **Hasil**: Navigasi halaman publik dan autentikasi menjadi instan karena CSS di-cache oleh browser pada load pertama.

---

### FASE 3: Backend & Query Optimization (Micro-Caching Master Data)
> **Tujuan**: Memangkas Time to First Byte (TTFB) query database dari > 500 ms menjadi < 50 ms.

- [ ] **Task 3.1: Micro-Caching pada Dropdown Master Data Statis**
  - **File**: [`app/Http/Controllers/Admin/PenempatanController.php`](file:///g:/MAGKOM/app/Http/Controllers/Admin/PenempatanController.php) dan controller terkait.
  - **Tindakan**:
    Bungkus data statis dengan cache durasi 5 menit (300 detik):
    ```php
    $bidangList = app()->environment('testing')
        ? Bidang::where('aktif', true)->orderBy('nama_bidang')->get()
        : Cache::remember('master_bidang_aktif', 300, fn () => Bidang::where('aktif', true)->orderBy('nama_bidang')->get());

    $jurusanList = app()->environment('testing')
        ? Jurusan::where('aktif', true)->orderBy('nama_jurusan')->get()
        : Cache::remember('master_jurusan_aktif', 300, fn () => Jurusan::where('aktif', true)->orderBy('nama_jurusan')->get());
    ```
  - **Hasil**: Eliminasi 4–6 query SQL disk per request di halaman manajemen admin.

---

## ⚖️ 4. Kritisi Tajam Terhadap Solusi (Risks, Trade-Offs & Edge Cases)

Sebagai Senior Architect, setiap solusi yang diajukan wajib dikritisi sebelum diimplementasikan:

### 4.1 Mengapa Kita TIDAK BOLEH Mengubah Aplikasi Menjadi SPA (Inertia.js / Vue / React / Turbo)?
- **Risiko Scope Explosion**: Mengubah aplikasi Laravel MPA yang sudah memiliki ratusan fitur ke Inertia/SPA memerlukan penulisan ulang ratusan file Blade ke komponen React/Vue, merombak controller response dari `view()` ke `Inertia::render()`, dan memakan waktu berbulan-bulan.
- **Memory Leak & Script Lifecycle Collision**: Pada sistem MagKom, ada script canvas, modal bootstrap/gentelella, dan Chart.js. Jika dipasang Turbo/PJAX secara instan tanpa perombakan total, script `DOMContentLoaded` tidak akan terpicu saat navigasi dan listener akan terpasang ganda (*duplicate listeners*).
- **Aturan Repositori**: Aturan ketat proyek ([`AGENTS.md`](file:///g:/MAGKOM/.agents/rules/AGENTS.md) Bagian 2) secara eksplisit melarang penambahan framework frontend baru.
- **Kesimpulan**: Aplikasi Laravel MPA dengan **browser caching yang aktif (Vite bundle)** dan response server **< 100 ms** akan terasa sangat instan bagi pengguna (klik langsung terbuka tanpa jeda). Masalah lambat saat ini bukan salah MPA, melainkan karena **payload HTML raksasa tanpa cache** dan **worker starvation**.

### 4.2 Risiko Caching Data Backend (*Stale Data & Test Pollution*)
- **Risiko**: Jika kita meng-cache query tanpa penanganan lingkungan testing, maka test suite PHPUnit yang membuat data baru di database akan membaca data basi dari cache sehingga terjadi *false failure*.
- **Mitigasi Wajib**:
  Selalu sertakan pengecekan:
  ```php
  if (app()->environment('testing')) {
      return $this->computeFresh();
  }
  ```
  serta pasang durasi micro-cache yang wajar (30–300 detik) untuk operasional dev/prod.

### 4.3 Keterbatasan `beforeunload` pada Mobile Browser
- **Catatan**: Beberapa mobile browser (seperti iOS Safari) terkadang mengoptimalkan lifecycle navigasi dengan BFCache (Back-Forward Cache) dan mengabaikan `beforeunload`.
- **Mitigasi**: Selalu padukan `beforeunload` dengan event `pagehide` (`window.addEventListener('pagehide', ...)`).

---

## 📋 5. Checklist Definisi Selesai (Definition of Done / DoD)

Rencana ini dianggap berhasil dieksekusi apabila:
- [ ] Ukuran HTML mentah untuk halaman Admin dan SuperAdmin terpangkas > 70% (tidak ada lagi tag `<style>` ribuan baris di Blade).
- [ ] Seluruh aset CSS dan JS baru dikelola melalui Vite (`resources/css/` dan `resources/js/`).
- [ ] Perintah `npm run build` sukses tanpa error dan tanpa aset yang hilang.
- [ ] Browser caching aktif: status transfer aset di browser network tab menunjukkan `(disk cache)` atau `304 Not Modified`.
- [ ] Waktu reload / navigasi antarhalaman terasa instan (< 300 ms).
- [ ] Seluruh test suite PHPUnit (`php artisan test`) lulus 100% HIJAU (PASS).
- [ ] Laravel Pint dan PHPStan lulus tanpa error.

---

## 💬 6. Arahan Lanjutan

Dokumen rencana kerja di atas telah siap. 
Untuk memulai eksekusi implementasi perbaikan performa ini secara tuntas, cukup berikan perintah:
> **`/goal implementasi`** atau **`lanjutkan implementasi`**
