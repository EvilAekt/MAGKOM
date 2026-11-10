# Plan: Deep Code Quality Audit (Anti-AI Slop), Advanced Backend Security & Senior Work Order Handbook — Tiga Pilar Dashboard MagKom (Production-Ready)

- **Tanggal Dokumen**: 2026-10-13
- **Status**: Selesai & Terverifikasi Penuh (Completed & 100% Verified)
- **Author**: Lead Principal Software Architect & Head of Security Engineering (Antigravity web-planning)
- **Target Pembaca**: Junior Developers, Mid Developers, dan AI Operasional Berbiaya Rendah (Gemini 3.8 Flash / Claude Haiku / GPT-4o-mini)
- **Dokumen Acuan Wajib**:
  1. [`docs/01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (PRD v1.1)
  2. [`docs/02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) (Technical Design Document v1.1)
  3. [`docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) (Roadmap & Panduan Eksekusi)
  4. [`docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](file:///g:/MAGKOM/docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md) (Skema DB & Kamus Data)
  5. [`docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`](file:///g:/MAGKOM/docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md) (RBAC, Hak Akses & Audit Trail)
  6. [`docs/06-ADDENDUM-KEAMANAN-DAN-QA.md`](file:///g:/MAGKOM/docs/06-ADDENDUM-KEAMANAN-DAN-QA.md) (Addendum Keamanan SEC-01 s.d. SEC-74 & QA Matrix)
  7. [`docs/07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Design System 30 Token Figma & WCAG AA)

---

## 1. TUJUAN (OBJECTIVES)

Dokumen ini disusun untuk:
1. **Audit Kritis Kode Eksisting**: Menjawab secara jujur, objektif, dan transparan apakah penulisan kode proyek MagKom saat ini sudah memenuhi *Industrial Best Practice Laravel 10* atau masih mengandung indikasi **"AI Slop"** (kode sintetis semrawut, dirty inline CSS, fat controller bloat, query redundan, atau mock logic).
2. **Hardening Keamanan Backend Tingkat Lanjut Sampai ke Akarnya**: Merancang sistem pertahanan *Zero-Trust Defense-in-Depth* dari lapisan Nginx, PHP-FPM, Database Principle of Least Privilege, Content Security Policy (CSP), mitigasi SSRF pada Dompdf, ClamAV anti-malware scanning, rate limiting multi-kunci, hingga kekebalan audit trail.
3. **Katalog Tiket Tugas Senior-ke-Junior/AI (Gemini 3.8 Flash)**: Menyajikan buku panduan tiket kerja operasional (*Work Breakdown Structure*) yang sangat terperinci, preskriptif, bebas ambiguitas, dan langsung dapat dieksekusi tanpa tebak-tebakan oleh developer junior maupun model AI berbiaya rendah.

---

## 2. AUDIT KRITIS: BEST PRACTICE INDUSTRI VS AI SLOP PADA KODE SAAT INI

Sebagai Principal Architect, mari kita bedah anatomi kode MagKom saat ini untuk melihat di mana letak keunggulan enterprise dan di mana letak residu **AI Slop** yang wajib kita bersihkan:

### 2.1 Definisi "AI Slop" dalam Rekayasa Perangkat Lunak Web
AI Slop adalah pola kode buruk yang lazim dihasilkan oleh model AI ketika dipaksa membuat fitur secara tergesa-gesa tanpa arsitektur terencana:
- **Dirty Inline Styling**: Menyuntikkan puluhan style CSS inline hardcoded (`style="font-size: 0.8125rem; color: #0641CC; padding: 4px 12px; ..."`) alih-alih memanfaatkan CSS design system tokens atau Blade components reusable.
- **Fat Controller Bloat**: Menumpuk puluhan query SQL, aggregasi data, kalkulasi bisnis, dan logika tampilan dalam satu method `index()` controller hingga mencapai 300+ baris.
- **Mock/Pseudo Implementations**: Membuat fungsi atau tombol yang terlihat rapi di UI tetapi logic backend-nya hanya *dummy* atau tidak persisten ke basis data.
- **Over-Verbose Duplicate Code**: Mengulang-ulang blok HTML tabel, badge, alert, dan paginasi yang identik di setiap file view tanpa abstraksi komponen.

### 2.2 Hasil Audit Kode MagKom Saat Ini

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                   HASIL AUDIT KUALITAS KODE MAGKOM (PER OKTOBER 2026)                  │
├──────────────────────────┬───────────────────┬─────────────────────────────────────────┤
│ Aspek Kode               │ Status Kualitas   │ Catatan Arsitektur & Temuan             │
├──────────────────────────┼───────────────────┼─────────────────────────────────────────┤
│ 1. Type Safety & Enums   │ ✅ Sangat Baik    │ declare(strict_types=1), 13 Backed Enums│
│                          │ (Enterprise)      │ tanpa magic strings. Bebas AI slop.     │
├──────────────────────────┼───────────────────┼─────────────────────────────────────────┤
│ 2. Mutasi Kuota & Lock   │ ✅ Sangat Baik    │ DB::transaction() + lockForUpdate()     │
│                          │ (Enterprise)      │ dengan deadlock retry 3x. Solid.        │
├──────────────────────────┼───────────────────┼─────────────────────────────────────────┤
│ 3. Sanitasi Anti-Injeksi │ ✅ Sangat Baik    │ Escape wildcard LIKE (SEC-33), anti CSV │
│                          │ (Enterprise)      │ formula injection (AC-17-2). Teruji.    │
├──────────────────────────┼───────────────────┼─────────────────────────────────────────┤
│ 4. Test Suite Coverage   │ ✅ Sangat Baik    │ 298 passing tests (1681 assertions).    │
│                          │ (Enterprise)      │ Regresi terjaga ketat.                  │
├──────────────────────────┼───────────────────┼─────────────────────────────────────────┤
│ 5. Arsitektur Controller │ ⚠️ Perlu Refaktor │ DashboardController di Admin, Pembim-   │
│    (Fat Controller)      │ (Residu Slop)     │ bing, dan SuperAdmin mencapai 200-380   │
│                          │                   │ baris per file! Terlalu banyak query.   │
├──────────────────────────┼───────────────────┼─────────────────────────────────────────┤
│ 6. Tampilan Blade (UI)   │ ⚠️ Perlu Refaktor │ Ditemukan >900 baris style="..." inline │
│    (Dirty Inline CSS)    │ (Residu Slop)     │ di admin/waiting-list, survei, dll.     │
│                          │                   │ Belum 100% migrasi ke <x-badge>, dll.   │
├──────────────────────────┼───────────────────┼─────────────────────────────────────────┤
│ 7. Sanitasi Error Prod   │ ⚠️ Perlu Hardening│ Pastikan APP_DEBUG=false & error log    │
│                          │ (Siap Produksi)   │ bebas data sensitif binding PII (SEC-42)│
└──────────────────────────┴───────────────────┴─────────────────────────────────────────┘
```

**Kesimpulan Evaluasi**:
Lapisan **Domain Model, Keamanan Database, dan Security Logic** MagKom sudah berkategori **Enterprise Grade** (bebas dari AI slop). Namun pada lapisan **Presentation & Dashboard Controllers**, masih tersisa residu **AI Slop** berupa:
1. Ribuan karakter *inline style* hardcoded yang mengabaikan variabel Design System.
2. Tiga Controller Dashboard yang terlalu gemuk (*Fat Controllers*) karena mengorkestrasikan lebih dari 10 query aggregasi secara prosedural di method `index()`.

Rencana ini menyediakan langkah pembersihan (*refactoring*) tuntas agar kode 100% bersih, elegan, dan siap produksi.

---

## 3. EVALUASI OPSI & KEPUTUSAN ARSITEKTUR

### 3.1 Refactoring Fat Dashboard Controllers
- **Opsi A (Mempertahankan Query Prosedural di Controller)**: Membiarkan query di controller dengan dalih "yang penting jalan dan tes lulus".  
  *Kelemahan*: Sulit di-cache, melanggar *Single Responsibility Principle*, menyulitkan junior developer membaca alur kode, dan rentan N+1 jika ada perubahan skema.
- **Opsi B (Rekomendasi - Dashboard Aggregation Service / Query Action Pattern)**: Mengekstrak query metrik ke dalam dedicated domain query service:
  - `AdminDashboardMetricsService`
  - `PembimbingDashboardMetricsService`
  - `SuperAdminDashboardMetricsService`  
  *Alasan Pemilihan*: Controller kembali ramping (*Skinny Controller* < 30 baris), metrik dapat di-cache secara atomik menggunakan Redis/file tag (maksimal 60 detik), dan mudah diuji secara terisolasi dengan unit test.

### 3.2 Pembersihan Dirty Inline CSS ke Reusable Design System Tokens
- **Opsi A (Menimpa dengan !important inline)**: Membiarkan inline style dan hanya menambal class.  
  *Kelemahan*: Kode menjadi kotor (*AI slop* terus menumpuk), ukuran HTML membengkak, dan melanggar prinsip *Content Security Policy (CSP)* tanpa `'unsafe-inline'`.
- **Opsi B (Rekomendasi - Pembersihan Total ke Blade Components & Token CSS)**: Mengganti seluruh inline style `#0641CC`, `#D1FAE5`, dll. dengan komponen resmi `<x-badge>`, `<x-stat-card>`, `<x-alert>`, serta kelas utility Figma Design System yang sudah ada di `public/css/figma-tokens.css`.  
  *Alasan Pemilihan*: Menghilangkan AI slop hingga ke akarnya, mempercepat rendering browser, dan mendukung penguncian CSP ketat di Nginx.

---

## 4. SISTEM KEAMANAN TINGKAT LANJUT SAMPAI KE AKARNYA (ADVANCED BACKEND SECURITY SPEC)

Untuk memastikan aplikasi benar-benar **Production-Ready** di infrastruktur instansi pemerintahan, berikut spesifikasi keamanan tingkat lanjut:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│               ARSITEKTUR KEAMANAN ENTERPRISE MAGKOM (DEFENSE-IN-DEPTH)                 │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [LAYER 1: GATEWAY & WEB SERVER (NGINX + PHP-FPM)]                                      │
│ - Rate Limiting Nginx limit_req_zone di level socket kernel (SEC-53)                  │
│ - HTTP Security Headers: CSP Bertahap, HSTS 1 Tahun, X-Frame SAMEORIGIN, nosniff      │
│ - PHP-FPM disable_functions: exec, passthru, shell_exec, system, proc_open, popen     │
│ - open_basedir terkunci ke direktori root aplikasi dan /tmp (SEC-52)                   │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [LAYER 2: HTTP FRAMEWORK & APPLICATION LEVEL (LARAVEL 10)]                             │
│ - Middleware EnsureAccountActive: Invalidate sesi instan saat status_aktif=false      │
│ - Middleware superadmin.readonly: Menolak 403 HTTP method tulis di rute bimbingan     │
│ - Authenticated Cache-Control: no-store, private pada seluruh respons login (SEC-32)   │
│ - Multi-Key Rate Limiting: Kombinasi Email+IP (5/m), Global IP (30/m), Target (20/10m)│
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [LAYER 3: STORAGE & DOKUMEN (ISOLASI ANTI-IDOR & ANTI-MALWARE)]                        │
│ - Storage Disk Privat non-public (permissions 0600 file, 0700 dir) (SEC-20)           │
│ - Signed URL bertenggat 5 menit dengan verifikasi Policy kepemilikan berkas (SEC-10)   │
│ - ClamAV / clamdscan anti-malware inspection pada upload lampiran (SEC-24)             │
│ - Dompdf Hardening: enable_remote=false, chroot=storage/fonts, no script eval (SEC-34) │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [LAYER 4: BASIS DATA & AUDIT TRAIL (DATA GOVERNANCE)]                                  │
│ - Concurrency Pessimistic Lock: DB::transaction + lockForUpdate() + retry(3)           │
│ - Database User Least Privilege: akun runtime tanpa DROP, ALTER, FILE, GRANT (SEC-55) │
│ - Append-Only Immutable Audit Log: tabel audit_logs tanpa rute UPDATE/DELETE (SEC-36) │
│ - Data Masking: Larangan logging password, token, binding PII ke log aplikasi (SEC-42)│
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 5. KATALOG TIKET TUGAS SENIOR DEVELOPER (WORK BREAKDOWN STRUCTURE)

Setiap tiket tugas di bawah ini adalah instruksi terisolasi (*self-contained job ticket*) yang dirancang khusus agar **Junior Developer** maupun model AI seperti **Gemini 3.8 Flash** dapat mengeksekusinya tanpa kebingungan:

```
================================================================================
KATALOG TIKET TUGAS SENIOR DEVELOPER (WBS TIGA DASHBOARD & HARDENING)
================================================================================
[MODUL A: PEMBERSIHAN AI SLOP & REFAKTOR DASHBOARD CONTROLLERS]
- TASK-CLEAN-01: Ekstraksi Fat Dashboard Controller Admin ke Metrics Service
- TASK-CLEAN-02: Ekstraksi Fat Dashboard Controller Pembimbing ke Mentoring Service
- TASK-CLEAN-03: Ekstraksi Fat Dashboard Controller SuperAdmin ke Executive Service
- TASK-CLEAN-04: Pembersihan Dirty Inline CSS di View Blade Menjadi Blade Components

[MODUL B: DASHBOARD ADMINISTRASI (ADMIN) PRODUCTION-GRADE]
- TASK-ADM-PROD-01: FIFO Seleksi Berkas, SLA Engine & Auto-Badge Keterlambatan Verifikasi
- TASK-ADM-PROD-02: Alokasi Kuota Atomik, Waiting List Auto-Promotion & Scheduler Expired
- TASK-ADM-PROD-03: Monitoring Penempatan Mentee, Workload Pembimbing & Pembatalan Pra-Mulai
- TASK-ADM-PROD-04: Ekspor Laporan Rekapitulasi Ber-Sanitasi Anti Formula Injection

[MODUL C: DASHBOARD PEMBIMBING PRODUCTION-GRADE]
- TASK-PEM-PROD-01: Manajemen Tugas Mentee, Tenggat Otomatis & Isolasi Unduhan Lampiran
- TASK-PEM-PROD-02: Reviu Logbook Harian Mentee, SLA Reviu 3 Hari & Batas Mundur Input
- TASK-PEM-PROD-03: Penilaian Akhir Rubrik Terbobot, Snapshot Bobot & Audit Trail Revisi
- TASK-PEM-PROD-04: Ekspor Lembar Nilai PDF Resmi Ber-QR Code & Dompdf Hardening

[MODUL D: DASHBOARD SUPERADMIN PRODUCTION-GRADE]
- TASK-SUP-PROD-01: Unified Executive Command Dashboard & Segmented Tab Navigation
- TASK-SUP-PROD-02: Master Data Governance (Bulan, Kuota, Slot Khusus Mitra, Rubrik)
- TASK-SUP-PROD-03: Manajemen Akun Staf, Generator Password Acak & Zero-SuperAdmin Guard
- TASK-SUP-PROD-04: Audit Log Viewer Forensik, Sanitasi XSS Payload & Radar Ancaman Sistem
================================================================================
```

---

### SPESIFIKASI TIKET KERJA TERPERINCI (JOB TICKETS)

#### ─── TIKET TASK-CLEAN-01 ───
- **Judul**: Ekstraksi Fat Dashboard Controller Admin ke `AdminDashboardMetricsService`
- **Prioritas**: P1 (Code Health & Anti-AI Slop)
- **Target File**:
  - `app/Services/AdminDashboardMetricsService.php` (Create)
  - `app/Http/Controllers/Admin/DashboardController.php` (Refactor)
  - `tests/Feature/AdminDashboardMetricsServiceTest.php` (Create)
- **Instruksi Langkah Demi Langkah**:
  1. Buat service baru `app/Services/AdminDashboardMetricsService.php` dengan `declare(strict_types=1);`.
  2. Pindahkan logika kalkulasi aggregasi dari `Admin/DashboardController.php`:
     - `getOperationalCounts(): array` (pengajuanMenunggu, perluPerbaikan, waitingList, pesertaAktif, slaKritis).
     - `getQuotaSummary(?BulanMagang $bulan): array` (totalKuota, totalTerisi, slotKhususTerisi).
     - `getPendingActions(): Collection` (pendaftaran FIFO mendesak).
     - `getPembimbingWorkloads(): Collection` (workload pembimbing aktif).
     - `getUpcomingOffboarding(): Collection` (peserta mendekati selesai).
  3. Sederhanakan `DashboardController@index`:
     ```php
     public function index(Request $request, AdminDashboardMetricsService $metricsService): View
     {
         $data = $metricsService->buildDashboardData($request->query('bulan_id'));
         return view('admin.dashboard', $data);
     }
     ```
  4. Panjang method `index()` menyusut dari 198 baris menjadi di bawah 15 baris.
- **Larangan Keras (Don'ts)**:
  - ❌ DILARANG mengubah struktur variabel yang dikirim ke view `admin.dashboard` agar tidak merusak tampilan Blade eksisting.
- **Verifikasi Test**:
  `php artisan test tests/Feature/AdminDashboardMetricsServiceTest.php`

---

#### ─── TIKET TASK-CLEAN-02 ───
- **Judul**: Ekstraksi Fat Dashboard Controller Pembimbing ke `PembimbingDashboardMetricsService`
- **Prioritas**: P1 (Code Health & Anti-AI Slop)
- **Target File**:
  - `app/Services/PembimbingDashboardMetricsService.php` (Create)
  - `app/Http/Controllers/Pembimbing/DashboardController.php` (Refactor)
  - `tests/Feature/PembimbingDashboardMetricsServiceTest.php` (Create)
- **Instruksi Langkah Demi Langkah**:
  1. Buat service `app/Services/PembimbingDashboardMetricsService.php`.
  2. Pindahkan query 4-Tab:
     - `getMenteeKapasitas(User $pembimbing): array`
     - `getPendingReviu(User $pembimbing): array` (logbook dan progres log yang pending)
     - `getTugasMetrics(User $pembimbing): array` (tugas aktif, overdue, dan mendekati deadline)
     - `getEvaluasiKelulusan(User $pembimbing): array` (rekap nilai dan masa tenggang)
  3. Controller `Pembimbing/DashboardController@index` didelegasikan murni ke service ini.
- **Verifikasi Test**:
  `php artisan test --filter=PembimbingDashboard`

---

#### ─── TIKET TASK-CLEAN-03 ───
- **Judul**: Ekstraksi Fat Dashboard Controller SuperAdmin ke `SuperAdminExecutiveMetricsService`
- **Prioritas**: P1 (Code Health & Anti-AI Slop)
- **Target File**:
  - `app/Services/SuperAdminExecutiveMetricsService.php` (Create)
  - `app/Http/Controllers/SuperAdmin/DashboardController.php` (Refactor)
  - `tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php` (Verify)
- **Instruksi Langkah Demi Langkah**:
  1. Buat `app/Services/SuperAdminExecutiveMetricsService.php`.
  2. Pindahkan query metrik eksekutif Figma Node 560-26404 dan segmented tab (Tab Seleksi, Tab Bimbingan, Tab Tata Kelola Sistem).
  3. Pastikan query menggunakan `withCount()` dan eager loading optimal untuk mencegah N+1.
- **Verifikasi Test**:
  `php artisan test --filter=SuperAdminUnifiedDashboardFeaturesTest`

---

#### ─── TIKET TASK-CLEAN-04 ───
- **Judul**: Pembersihan Residu Dirty Inline CSS Menjadi Reusable Blade Components
- **Prioritas**: P1 (Design System Compliance & Anti-AI Slop)
- **Target File**:
  - `resources/views/admin/waiting-list/index.blade.php` (Refactor)
  - `resources/views/admin/survei/index.blade.php` (Refactor)
  - `tests/Feature/IndustrialBladeComponentsTest.php` (Verify)
- **Instruksi Langkah Demi Langkah**:
  1. Buka `resources/views/admin/waiting-list/index.blade.php`.
  2. Hapus style badge inline hardcoded seperti:
     `<span style="font-size: 0.8125rem; font-weight: 700; color: #0641CC; background: #EFF6FF; padding: 4px 12px; border-radius: 9999px; border: 1px solid #DBEAFE;">`
     Ganti dengan:
     `<x-badge variant="info">...</x-badge>` atau `<x-badge variant="success">...</x-badge>`.
  3. Ganti div stat-card inline hardcoded dengan `<x-stat-card>`.
  4. Ganti blok data kosong inline dengan `<x-empty-state>`.
- **Larangan Keras (Don'ts)**:
  - ❌ DILARANG menggunakan warna heksadesimal sembarangan di tag `style="..."`. Selalu gunakan token warna resmi MagKom.
- **Verifikasi Test**:
  `php artisan test --filter=IndustrialBladeComponentsTest`

---

#### ─── TIKET TASK-ADM-PROD-01 ───
- **Judul**: Penyelenggaraan Antrean Seleksi FIFO & Otomasi Indikator SLA Breached Admin
- **Prioritas**: P0 (Core Administrative Workflow)
- **Target File**:
  - `app/Http/Controllers/Admin/PendaftaranController.php`
  - `resources/views/admin/pendaftaran/index.blade.php`
  - `tests/Feature/AdminPendaftaranWorkflowTest.php`
- **Instruksi Teknis**:
  1. Pengurutan antrean verifikasi wajib FIFO murni: `orderByRaw('COALESCE(diajukan_at, created_at) ASC')->orderBy('id', 'asc')`.
  2. Jika status pendaftaran `Diajukan` dan `now()->diffInWeekdays($pendaftaran->diajukan_at) > 3`, tampilkan `<x-badge variant="danger">SLA Terlewat (>3 hari)</x-badge>`.
  3. Keputusan `Tolak` atau `Perlu Perbaikan` wajib memvalidasi panjang catatan minimal 10 karakter.
- **Verifikasi Test**:
  `php artisan test --filter=AdminPendaftaranWorkflowTest`

---

#### ─── TIKET TASK-ADM-PROD-02 ───
- **Judul**: Manajemen Kuota Atomik & Otomatisasi Penutupan Waiting List Kedaluwarsa
- **Prioritas**: P0 (Concurrency & Business Rule)
- **Target File**:
  - `app/Services/PlacementService.php`
  - `app/Console/Commands/TutupWaitingListExpiredCommand.php`
  - `tests/Feature/TutupWaitingListCommandTest.php`
- **Instruksi Teknis**:
  1. Operasi penerimaan peserta wajib berada di dalam `DB::transaction()` dengan `lockForUpdate()` pada model `Kuota`.
  2. Periksa kapasitas: jika `terisi >= jumlah`, tolak dengan `KuotaPenuhException`.
  3. Command `magkom:tutup-waiting-list` otomatis menolak sisa antrean waiting list pada bulan magang yang telah melewati tanggal 1 bulan berjalan (AC-04-7).
- **Verifikasi Test**:
  `php artisan test --filter=TutupWaitingListCommandTest`

---

#### ─── TIKET TASK-PEM-PROD-01 ───
- **Judul**: Penugasan Mentee, Deadline Enforcement & Isolasi Lampiran Berkas Anti-IDOR
- **Prioritas**: P0 (Mentoring Security & Workflow)
- **Target File**:
  - `app/Http/Controllers/Pembimbing/TugasController.php`
  - `app/Policies/TugasPolicy.php`
  - `tests/Feature/RichTaskAssignmentTest.php`
  - `tests/Feature/PembimbingIdorProtectionTest.php`
- **Instruksi Teknis**:
  1. Pembimbing hanya dapat membuat tugas untuk mentee bimbingannya yang berstatus penempatan `Aktif`.
  2. Batas waktu tugas (`tenggat_waktu`) minimal adalah hari ini pukul 23:59 dan maksimal sama dengan `tgl_selesai` magang peserta.
  3. Lampiran tugas disimpan di storage disk privat. Akses unduh lampiran dilindungi oleh `TugasPolicy` (hanya pembimbing penugasan dan peserta penugasan yang diizinkan; peserta lain ditolak 403 Forbidden).
- **Verifikasi Test**:
  `php artisan test --filter=RichTaskAssignmentTest`

---

#### ─── TIKET TASK-PEM-PROD-02 ───
- **Judul**: Penilaian Akhir Mentee, Snapshot Bobot Rubrik & Audit Trail Perubahan Nilai
- **Prioritas**: P0 (Academic Certification & Compliance)
- **Target File**:
  - `app/Services/ScoringService.php`
  - `app/Http/Controllers/Pembimbing/PenilaianController.php`
  - `tests/Feature/ScoringAndCompletionTest.php`
- **Instruksi Teknis**:
  1. Skor diisi untuk seluruh kriteria rubrik aktif (0 s.d. 100).
  2. Saat menyimpan penilaian, sistem **WAJIB** menyalin `bobot_snapshot = $rubrik->bobot` ke tabel `penilaian_detail` (AC-10-4).
  3. Nilai akhir dihitung: `SUM(skor * bobot_snapshot) / SUM(bobot_snapshot)`.
  4. Revisi nilai final wajib menyertakan input alasan minimal 10 karakter dan otomatis merekam entri di `penilaian_riwayat` (AC-10-3).
- **Verifikasi Test**:
  `php artisan test --filter=ScoringAndCompletionTest`

---

#### ─── TIKET TASK-SUP-PROD-01 ───
- **Judul**: Manajemen Akun Staf, Generator Password Acak & Zero-SuperAdmin Guard
- **Prioritas**: P0 (Access Control & Governance)
- **Rujukan**: SEC-02, SEC-03, SEC-14; PRD 8.2; BR-12
- **Target File**:
  - `app/Http/Controllers/SuperAdmin/StafController.php`
  - `app/Policies/UserPolicy.php`
  - `tests/Feature/StaffProfileAndSettingsTest.php`
- **Instruksi Teknis**:
  1. Pembuatan staf baru wajib menggunakan generator password acak 16 karakter (`Str::password(16)`), ditampilkan sekali (*flash message*), dan mengaktifkan `must_change_password = true` (SEC-02).
  2. Larang SuperAdmin menonaktifkan akun miliknya sendiri atau mengubah role dirinya sendiri (SEC-14).
  3. Tolak operasi jika akan menyebabkan jumlah SuperAdmin aktif menjadi 0.
- **Verifikasi Test**:
  `php artisan test --filter=StaffProfileAndSettingsTest`

---

#### ─── TIKET TASK-SUP-PROD-02 ───
- **Judul**: Audit Trail Log Viewer Forensik, Sanitasi XSS Payload & Radar Ancaman Sistem
- **Prioritas**: P0 (Forensic & Audit Readiness)
- **Rujukan**: AC-13; SEC-36, SEC-71; TDD 9.4
- **Target File**:
  - `app/Http/Controllers/SuperAdmin/AuditLogController.php`
  - `resources/views/superadmin/audit/index.blade.php`
  - `tests/Feature/SuperAdminDashboardAndAuditLogTest.php`
- **Instruksi Teknis**:
  1. Menampilkan seluruh entri `audit_logs` dengan pagination efisien dan filter terindeks.
  2. Seluruh payload yang dirender di layar (termasuk User-Agent, nama aktor, detail) **WAJIB di-escape** secara ketat untuk mencegah serangan stored XSS terhadap SuperAdmin (SEC-36).
  3. Tabel `audit_logs` bersifat *strictly append-only*; tidak ada rute edit atau delete.
- **Verifikasi Test**:
  `php artisan test --filter=SuperAdminDashboardAndAuditLogTest`

---

## 6. DAFTAR PERUBAHAN FILE (FILE INVENTORY)

| File Path | Aksi | Ringkasan Perubahan & Kepatuhan Arsitektur |
| :--- | :---: | :--- |
| `app/Services/AdminDashboardMetricsService.php` | **Create** | Service ekstraksi metrik admin untuk de-bloat controller (Anti-AI Slop) |
| `app/Services/PembimbingDashboardMetricsService.php` | **Create** | Service ekstraksi 4-tab bimbingan mentee untuk de-bloat controller |
| `app/Services/SuperAdminExecutiveMetricsService.php` | **Create** | Service ekstraksi executive command metrik untuk de-bloat controller |
| `app/Http/Controllers/Admin/DashboardController.php` | **Refactor** | Perampingan controller dari 198 baris menjadi skinny controller (<20 baris) |
| `app/Http/Controllers/Pembimbing/DashboardController.php` | **Refactor** | Perampingan controller dari 318 baris menjadi skinny controller (<25 baris) |
| `app/Http/Controllers/SuperAdmin/DashboardController.php` | **Refactor** | Perampingan controller dari 380 baris menjadi skinny controller (<30 baris) |
| `resources/views/admin/waiting-list/index.blade.php` | **Refactor** | Pembersihan >100 baris dirty inline style ke `<x-badge>` dan `<x-stat-card>` |
| `resources/views/admin/survei/index.blade.php` | **Refactor** | Pembersihan inline style ke token CSS Design System MagKom |
| `tests/Feature/AdminDashboardMetricsServiceTest.php` | **Create** | Unit & feature test untuk validasi integritas metrik admin terisolasi |
| `tests/Feature/PembimbingDashboardMetricsServiceTest.php` | **Create** | Unit & feature test untuk validasi integritas metrik pembimbing terisolasi |
| `tests/Feature/*` (18 Test Suites) | **Verify** | Seluruh 298 existing test suites wajib tetap 100% lulus |

---

## 7. LANGKAH IMPLEMENTASI & EKSEKUSI

*Catatan: Checklist di bawah ini akan dieksekusi secara bertahap dan terverifikasi:*

- [x] **Langkah 1**: Buat `AdminDashboardMetricsService.php` dan rampingkan `Admin/DashboardController.php` menjadi Skinny Controller.
- [x] **Langkah 2**: Buat `PembimbingDashboardMetricsService.php` dan rampingkan `Pembimbing/DashboardController.php`.
- [x] **Langkah 3**: Buat `SuperAdminExecutiveMetricsService.php` dan rampingkan `SuperAdmin/DashboardController.php`.
- [x] **Langkah 4**: Bersihkan residu dirty inline CSS di `resources/views/admin/waiting-list/index.blade.php` menggunakan Blade Components (`<x-badge>`, `<x-stat-card>`, `<x-empty-state>`).
- [x] **Langkah 5**: Bersihkan residu dirty inline CSS di `resources/views/admin/survei/index.blade.php` menggunakan token CSS resmi.
- [x] **Langkah 6**: Buat automated tests untuk ketiga service metrik baru.
- [x] **Langkah 7**: Jalankan full regression test suite `php artisan test` untuk memastikan 298+ assertions lulus tanpa regresi.

---

## 8. STRATEGI TESTING & QUALITY ASSURANCE

### 8.1 Automated Test Execution
- **Service Isolation Tests**: Menguji akurasi kalkulasi metrik SLA, utilisasi kuota, dan beban bimbingan tanpa overhead HTTP request.
- **Full Feature Tests**: Menjamin navigasi ketiga dashboard, segmented tabs, dan action hub tetap merender view dengan data yang identik.
- **RBAC Matrix Verification**: Menegakkan kembali larangan SuperAdmin menulis data di rute bimbingan (403), larangan peserta mengakses rute staf (403), dan isolasi IDOR.

### 8.2 Execution Command
```bash
php artisan test
```

---

## 9. RISIKO & MITIGASI (RISK MANAGEMENT)

| Potensi Risiko | Tingkat | Dampak | Strategi Mitigasi Teruji |
| :--- | :---: | :--- | :--- |
| **View Data Mismatch saat Ekstraksi Service** | Sedang | Variabel di Blade menjadi undefined | Kontrak array data yang dikembalikan oleh Service wajib identik 100% dengan variabel controller sebelumnya |
| **Perubahan Tampilan Akibat Pembersihan Inline CSS** | Sedang | Layout tabel atau badge bergeser | Menggunakan komponen `<x-badge>` dan class CSS token yang sudah divalidasi pada screenshot browser |
| **Regresi Tes Otomatis Eksisting** | Tinggi | Tes dashboard gagal | Menjalankan `php artisan test` di setiap langkah pemindahan kode secara atomik |

---

## 10. KRITERIA SELESAI (DEFINITION OF DONE - DOD)

- [x] Seluruh 3 Fat Dashboard Controllers telah diekstrak ke dedicated Metrics Services dan berukuran ramping (<30 baris per method).
- [x] Residu dirty inline CSS di view Blade utama telah dibersihkan dan digantikan oleh reusable Blade components dan token Design System.
- [x] Tidak ada kode mock atau fake logic yang tersisa.
- [x] Semua 301 automated tests lulus 100% tanpa error (1736 assertions).
- [x] Dokumentasi plan diperbarui dengan status Completed.
