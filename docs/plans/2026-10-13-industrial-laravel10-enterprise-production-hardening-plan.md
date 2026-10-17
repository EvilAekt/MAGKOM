# Plan: Master Blueprint & Task Handbook Senior Developer — Hardening Industri Laravel 10 & Kelanjutan Tiga Pilar Dashboard MagKom (Production-Ready)

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

Menyajikan rencana induk teknis (*master engineering blueprint*) dan katalog tiket kerja terperinci (*Senior Work Order Handbook*) untuk menuntaskan, memperkuat, dan memvalidasi **Tiga Pilar Dashboard MagKom** (Dashboard Administrasi, Dashboard Pembimbing, dan Dashboard SuperAdmin) hingga mencapai tingkat kesiapan **100% Production-Grade** sesuai standar industri enterprise Laravel 10 dan regulasi keamanan SPBE/UU PDP.

Rencana ini dirancang khusus agar:
1. **Bebas Ambiguitas Bagi Junior Developer**: Setiap instruksi memiliki target berkas yang pasti, kode acuan konkret, larangan keras (*don'ts*), serta kriteria verifikasi otomatis (*automated test assertion*).
2. **Dapat Dipahami Model AI Berbiaya Rendah (Gemini 3.8 Flash)**: Prompt-ready dan kontekstual tanpa mengharuskan AI memuat seluruh file berulang kali.
3. **Memenuhi Kepatuhan Keamanan Penuh**: Menerapkan mitigasi terhadap ancaman OWASP Top 10 dan klausul addendum keamanan SEC-01 s.d. SEC-74 secara menyeluruh sampai ke akar backend, basis data, dan konfigurasi runtime.

---

## 2. ASUMSI & PERTANYAAN TERBUKA

### 2.1 Asumsi Teknis
1. **Runtime Framework**: Aplikasi berjalan di atas **PHP 8.2+ / 8.3** dengan framework **Laravel 10.x LTS**, basis data MySQL 8.0+ (engine InnoDB dengan dukungan pesimistik lock `SELECT ... FOR UPDATE`), dan web server Nginx + PHP-FPM.
2. **Pola Desain Standar**: Menerapkan arsitektur *Skinny Controller*, *Domain Service Layer*, *Form Request Dedicated*, *PHP 8.1+ Backed Enums*, dan *Defense-in-Depth Authorization* (Middleware + FormRequest + Policy).
3. **Integritas Uji Regresi**: Seluruh 298 tes otomatis eksisting (`tests/Feature/*` dan `tests/Unit/*`) wajib tetap bernilai hijau (100% passed) di setiap tahapan perubahan.
4. **Keamanan Sesi & Kredensial**: Tidak ada data sensitif (password asli, token OAuth, API key pihak ketiga) yang disimpan dalam plain text, tercatat di berkas log, atau ter-commit ke git repository.

### 2.2 Pertanyaan Terbuka & Mitigasi Awal
- *Apakah lingkungan server produksi menggunakan Reverse Proxy / CDN / Cloudflare di depan Nginx?*  
  **Mitigasi**: Middleware `TrustProxies` dikonfigurasikan secara presisi (hanya mempercayai subnet resmi Cloudflare/IP internal, dilarang `*` wildcard guna mencegah IP spoofing pada audit log dan rate limiting NFR-S10).
- *Apakah sistem pemindaian ClamAV (SEC-24) telah terinstal di server produksi?*  
  **Mitigasi**: Modul `DocumentStorageService` disiapkan dengan fallback *fail-closed* saat clamd aktif di production, atau bypass terinspeksi berbasis MIME sniffing ketat (`finfo_file` + magic bytes inspection) pada environment local/staging.

---

## 3. AUDIT KESESUAIAN STANDAR INDUSTRI LARAVEL 10 ENTERPRISE

Sebelum menugaskan tiket kepada junior developer atau AI operasional, berikut audit komparatif antara **Pola Tutorial Pemula (Anti-Pattern)** versus **Standar Industri Enterprise Laravel 10 (Production Best Practice)**:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│             MATRIKS EVALUASI ARSITEKTUR LARAVEL 10: PEMULA VS ENTERPRISE               │
├─────────────────────────┬───────────────────────────┬──────────────────────────────────┤
│ Dimensi Rekayasa        │ Pola Pemula (Dilarang ❌) │ Standar Enterprise MagKom (✅)   │
├─────────────────────────┼───────────────────────────┼──────────────────────────────────┤
│ 1. Controller           │ Fat Controller (query,    │ Skinny Controller: validasi via  │
│                         │ kalkulasi, mutasi di satu │ FormRequest, pendelegasian ke    │
│                         │ method controller)        │ Service, return View/Redirect    │
├─────────────────────────┼───────────────────────────┼──────────────────────────────────┤
│ 2. Type Safety          │ Magic strings ('diterima',│ Strict typing declare(strict_    │
│                         │ 'aktif'), tanpa type hint │ types=1), Backed Enums PHP 8.1+  │
├─────────────────────────┼───────────────────────────┼──────────────────────────────────┤
│ 3. Concurrency Control  │ DB::table update langsung,│ DB::transaction() + lockFor-     │
│                         │ rawan race condition      │ Update() pada mutasi kuota & slot│
├─────────────────────────┼───────────────────────────┼──────────────────────────────────┤
│ 4. Otorisasi (RBAC)     │ Cek role manual di blade/ │ 3 Lapis: Middleware rute,        │
│                         │ if ($user->role == 'admin') FormRequest authorize(), Policy   │
├─────────────────────────┼───────────────────────────┼──────────────────────────────────┤
│ 5. Sanitasi Data Input  │ Request all(), inject     │ Request validated(), escape      │
│                         │ LIKE %$q% mentah          │ wildcard SQL, anti CSV formula   │
├─────────────────────────┼───────────────────────────┼──────────────────────────────────┤
│ 6. Manajemen Berkas     │ Simpan di public/storage, │ Storage disk privat non-public,  │
│                         │ direct link URL publik    │ signed URL temporer 5 menit      │
├─────────────────────────┼───────────────────────────┼──────────────────────────────────┤
│ 7. Query Database       │ N+1 loop di view Blade,   │ Eager loading with(), withCount(),│
│                         │ query tidak terindeks     │ indeks komposit pada filter rute │
├─────────────────────────┼───────────────────────────┼──────────────────────────────────┤
│ 8. Audit & Kepatuhan    │ Log file teks biasa,      │ Append-only audit_logs di DB,    │
│                         │ tanpa riwayat mutasi nilai│ rekam aktor, IP, user-agent      │
├─────────────────────────┼───────────────────────────┼──────────────────────────────────┤
│ 9. UI & Aksesibilitas   │ HTML inline acak-acakan,  │ Reusable Blade Components,       │
│                         │ tombol kecil < 30px       │ 30 Token Figma, target 44px WCAG │
└─────────────────────────┴───────────────────────────┴──────────────────────────────────┘
```

---

## 4. PENDEKATAN DESAIN & ARSITEKTUR

Untuk setiap keputusan struktural, dilakukan evaluasi perbandingan antara 2 pendekatan teknis:

### 4.1 Pendekatan Otorisasi SuperAdmin pada Bimbingan Mentee (SEC-12, AC-08-5, AC-10-7)
- **Opsi A (Hanya Policy Check)**: Melakukan pengecekan `$this->authorize()` di setiap method controller Pembimbing.  
  *Kelemahan*: Jika developer junior lupa memanggil policy pada satu method baru, akses SuperAdmin untuk menulis/mengubah data langsung terbuka (*Single Point of Failure*).
- **Opsi B (Rekomendasi - Pertahanan Berlapis / Defense-in-Depth)**: Mengombinasikan Policy dengan **Middleware `EnsureSuperAdminReadOnly` (`superadmin.readonly`)** yang langsung memblokir `HTTP 403 Forbidden` untuk method selain GET/HEAD di tingkat rute `/pembimbing/*`.  
  *Alasan Pemilihan*: Menjamin keamanan zero-trust di gerbang HTTP sebelum kode controller sempat dieksekusi.

### 4.2 Pendekatan Integritas Kuota dan Mutasi Penempatan (BR-01, BR-05, TDD 5.3)
- **Opsi A (Optimistic Locking via version column)**: Mengandalkan nomor versi pada baris kuota.  
  *Kelemahan*: Pada antrean traffic tinggi saat penutupan gelombang, banyak request pengguna akan mental (*rollback conflict*) sehingga membebani server dan merusak UX calon peserta.
- **Opsi B (Rekomendasi - Pessimistic Locking with Retry)**: Menggunakan `DB::transaction()` dengan `lockForUpdate()` pada model `Kuota` dan `SlotKhusus`, disertai argumen dead-lock retry `3 kali`.  
  *Alasan Pemilihan*: Menjamin konsistensi absolut (*ACID guarantees*), mencegah kuota terisi melebihi kapasitas (*oversold*), dan secara elegan mengantrekan request secara berurutan.

---

## 5. KATALOG TIKET TUGAS SENIOR DEVELOPER (WORK BREAKDOWN STRUCTURE)

Berikut adalah panduan tiket kerja berstandar senior untuk dieksekusi oleh junior developer atau model AI:

```
================================================================================
KATALOG TIKET TASK PENGEMBANGAN DAN HARDENING (WBS)
================================================================================
[PILAR I: HARDENING BACKEND & SISTEM KEAMANAN INTI]
- TASK-SEC-01: Hardening Sesi, Inaktivasi Akun & Password Staf Acak (SEC-01..04)
- TASK-SEC-02: Rate Limiting Berlapis & Sanitasi Header Host/Proxy (SEC-06, SEC-30)
- TASK-SEC-03: Storage Disk Privat & Signed URL Berkas Bimbingan (SEC-20..23)
- TASK-SEC-04: Anti-Injeksi Query, Sanitasi Wildcard & Anti-CSV Injection (SEC-33, AC-17-2)

[PILAR II: KELANJUTAN DASHBOARD ADMINISTRASI (ADMIN)]
- TASK-ADM-01: Antrean Seleksi FIFO & Visualisasi SLA Breached (AC-03, AC-04)
- TASK-ADM-02: Manajemen Kuota Atomik & Otomatisasi Penutupan Waiting List (AC-04-7)
- TASK-ADM-03: Penempatan Peserta, Batas Beban Pembimbing & Pembatalan (AC-07, BR-08)
- TASK-ADM-04: Analitik Tahunan & Rekap Laporan Ekspor Anti-Formula (AC-17)

[PILAR III: KELANJUTAN DASHBOARD PEMBIMBING]
- TASK-PEM-01: Penugasan Mentee, Deadline Enforcement & Multi-Attachment (AC-08)
- TASK-PEM-02: Reviu Logbook Harian, Batas Mundur 3 Hari & SLA Indicator (AC-09, BR-09)
- TASK-PEM-03: Penilaian Akhir, Rubrik Snapshot & Audit Trail Revisi Nilai (AC-10)
- TASK-PEM-04: Lembar Nilai PDF Resmi On-The-Fly & Pencegahan IDOR (AC-10-6)

[PILAR IV: KELANJUTAN DASHBOARD SUPERADMIN]
- TASK-SUP-01: Executive Command Dashboard & Segmented Tab Controller
- TASK-SUP-02: Tata Kelola Master Data (Bulan, Kuota, Slot Khusus, Rubrik) (AC-11)
- TASK-SUP-03: Manajemen Akun Staf, Zero-SuperAdmin Guard & Reset Sesi (SEC-14)
- TASK-SUP-04: Audit Log Viewer Forensik, Sanitasi Payload & Health Radar (AC-13, SEC-36)
================================================================================
```

---

### DETAIL SPESIFIKASI TIKET KERJA (JOB TICKETS)

#### ─── TIKET TASK-SEC-01 ───
- **Judul**: Hardening Sesi, Inaktivasi Akun Realtime & Auto-Generate Password Staf
- **Prioritas**: P0 (Kritis Keamanan)
- **Rujukan**: Addendum SEC-01, SEC-02, SEC-03, SEC-04; PRD 8.2; TDD 8.1
- **Target File**:
  - `app/Http/Middleware/EnsureAccountActive.php`
  - `app/Http/Controllers/SuperAdmin/StafController.php`
  - `resources/views/superadmin/staf/create.blade.php`
  - `tests/Feature/SecurityAccountHardeningTest.php`
- **Instruksi Teknis**:
  1. Pada `EnsureAccountActive.php`, pastikan jika `$user->status_aktif === false`, panggil `Auth::guard('web')->logout()`, `$request->session()->invalidate()`, dan `$request->session()->regenerateToken()` sehingga sesi pengguna yang dinonaktifkan langsung putus seketika tanpa menunggu kedaluwarsa sesi.
  2. Pada `StafController@store`, **dilarang meminta input password manual**. Buat password otomatis sepanjang 16 karakter menggunakan `Str::password(16)` atau `random_int`. Tampilkan password sekali saja di layar pembuatan (*one-time flash alert*), set `must_change_password = true`.
  3. Saat pengguna staf mengubah password, jalankan `Auth::logoutOtherDevices($request->password_baru)` untuk memutus seluruh sesi aktif di perangkat lain (SEC-04).
- **Larangan Keras (Don'ts)**:
  - ❌ DILARANG menyimpan password plain-text ke dalam basis data atau berkas audit log.
  - ❌ DILARANG membiarkan staf dinonaktifkan tetap bisa membuka halaman karena sesinya masih tersimpan di cookies.
- **Verifikasi Test**:
  `php artisan test --filter=SecurityAccountHardeningTest`

---

#### ─── TIKET TASK-SEC-02 ───
- **Judul**: Rate Limiting Berlapis Multi-Key & Proteksi Host Header Poisoning
- **Prioritas**: P0 (Kritis Keamanan)
- **Rujukan**: Addendum SEC-06, SEC-30; TDD 8.4; OWASP A07:2021
- **Target File**:
  - `app/Providers/RouteServiceProvider.php`
  - `app/Http/Middleware/TrustHosts.php`
  - `app/Http/Middleware/TrustProxies.php`
  - `tests/Feature/SecurityRateLimitingTest.php`
- **Instruksi Teknis**:
  1. Pada `RouteServiceProvider.php`, konfigurasikan `RateLimiter::for('login', ...)` dengan 3 lapis kunci proteksi:
     - Kunci 1: `email + IP` (maksimal 5 percobaan per menit).
     - Kunci 2: `IP klien` (maksimal 30 percobaan per menit).
     - Kunci 3: `Email target` (maksimal 20 percobaan per 10 menit di seluruh IP guna mencegah serangan terdistribusi botnet).
  2. Pada `TrustHosts.php`, aktifkan pengecekan domain host resmi instansi:
     ```php
     return [
         $this->allSubdomainsOfApplicationUrl(),
         'localhost',
         '127.0.0.1',
     ];
     ```
  3. Pada `TrustProxies.php`, pastikan `$proxies` tidak bernilai wildcard `*` di lingkungan produksi.
- **Larangan Keras (Don'ts)**:
  - ❌ DILARANG menggunakan satu rate limit berbasis IP saja karena penyerang dapat membajak akun dengan *distributed IP rotating*.
- **Verifikasi Test**:
  `php artisan test --filter=SecurityRateLimitingTest`

---

#### ─── TIKET TASK-SEC-03 ───
- **Judul**: Isolasi Storage Privat, Signed URL Temporer & Pencegahan Akses Berkas IDOR
- **Prioritas**: P0 (Kritis Keamanan Data Pribadi)
- **Rujukan**: Addendum SEC-20, SEC-21, SEC-22, SEC-23; TDD 8.2; PRD 3.3
- **Target File**:
  - `config/filesystems.php`
  - `app/Services/DocumentStorageService.php`
  - `app/Http/Controllers/Public/BerkasDownloadController.php`
  - `tests/Feature/DocumentSecurityTest.php`
- **Instruksi Teknis**:
  1. Pastikan disk `private` terdefinisi di `config/filesystems.php` dengan root di luar folder `public/`:
     ```php
     'private' => [
         'driver' => 'local',
         'root' => storage_path('app/private'),
         'permissions' => ['file' => ['public' => 0600, 'private' => 0600], 'dir' => ['public' => 0700, 'private' => 0700]],
         'throw' => true,
     ],
     ```
  2. Seluruh file lampiran pendaftaran, progres tugas, dan bukti logbook disimpan menggunakan nama file terenkripsi SHA-256 acak, bukan nama asli pengguna (SEC-22).
  3. Pengunduhan berkas **WAJIB** melalui rute bertanda tangan sementara (Signed URL kedaluwarsa 5 menit) dan dilindungi oleh Policy kepemilikan.
  4. Response unduh berkas wajib menyertakan header:
     - `Content-Disposition: attachment; filename="tersanitasi.pdf"`
     - `X-Content-Type-Options: nosniff`
     - `Cache-Control: private, no-store, must-revalidate`
- **Larangan Keras (Don'ts)**:
  - ❌ DILARANG membuat symbolic link (`php artisan storage:link`) ke direktori berkas privat peserta.
  - ❌ DILARANG mengizinkan Pembimbing mengunduh berkas pendaftaran calon peserta (PRD 3.3).
- **Verifikasi Test**:
  `php artisan test --filter=DocumentSecurityTest`

---

#### ─── TIKET TASK-SEC-04 ───
- **Judul**: Sanitasi Wildcard Query SQL (Anti-DoS) & Proteksi Formula Injection CSV
- **Prioritas**: P0 (Integritas Data)
- **Rujukan**: Addendum SEC-33, AC-17-2; TDD 8.3; SEC-61
- **Target File**:
  - `app/Services/ExportReportService.php`
  - `app/Http/Controllers/Admin/PendaftaranController.php`
  - `app/Http/Controllers/Admin/LaporanController.php`
  - `tests/Feature/ExportAndQuerySanitizationTest.php`
- **Instruksi Teknis**:
  1. Pada setiap controller yang menerima input pencarian `$request->query('q')`, lakukan sanitasi karakter khusus SQL LIKE sebelum dimasukkan ke query:
     ```php
     $sanitizedKeyword = addcslashes($keyword, '%_\\');
     $query->where('nama', 'like', "%{$sanitizedKeyword}%");
     ```
  2. Pada `ExportReportService.php`, setiap sel data yang diekspor ke CSV/Excel wajib diperiksa. Jika karakter pertama adalah salah satu dari `=`, `+`, `-`, `@`, `\t`, `\r`, tambahkan tanda petik tunggal (`'`) di depannya untuk mencegah eksekusi macro pada Microsoft Excel (AC-17-2).
- **Larangan Keras (Don'ts)**:
  - ❌ DILARANG menggunakan `DB::raw()` atau `whereRaw()` yang menggabungkan variabel input pengguna secara langsung tanpa parameter binding.
- **Verifikasi Test**:
  `php artisan test --filter=ExportAndQuerySanitizationTest`

---

#### ─── TIKET TASK-ADM-01 ───
- **Judul**: Antrean Verifikasi Seleksi FIFO & Visualisasi SLA Breached Admin
- **Prioritas**: P1 (Core Admin Workflow)
- **Rujukan**: PRD AC-03, AC-04-1; TDD 5.1; BR-05; Design System 07
- **Target File**:
  - `app/Http/Controllers/Admin/PendaftaranController.php`
  - `resources/views/admin/pendaftaran/index.blade.php`
  - `resources/views/admin/pendaftaran/show.blade.php`
  - `tests/Feature/AdminPendaftaranWorkflowTest.php`
- **Instruksi Teknis**:
  1. Pengurutan data antrean pendaftaran wajib menggunakan FIFO murni:
     `orderByRaw('COALESCE(diajukan_at, created_at) ASC')->orderBy('id', 'asc')`.
  2. Tambahkan kolom status SLA: Jika status pendaftaran adalah `Diajukan` dan `now()->diffInWeekdays($pendaftaran->diajukan_at) > 3`, tampilkan `<x-badge variant="danger">SLA Breached (>3 Hari)</x-badge>`.
  3. Pada form keputusan verifikasi, jika admin memilih `Perlu Perbaikan` atau `Ditolak`, input `catatan` wajib diisi minimal 10 karakter.
- **Verifikasi Test**:
  `php artisan test --filter=AdminPendaftaranWorkflowTest`

---

#### ─── TIKET TASK-ADM-02 ───
- **Judul**: Manajemen Kuota Atomik & Otomatisasi Penutupan Waiting List
- **Prioritas**: P0 (Concurrency & Business Rule)
- **Rujukan**: PRD AC-04-4, AC-04-7; TDD 5.3; BR-01, BR-05; Command `magkom:tutup-waiting-list`
- **Target File**:
  - `app/Services/PlacementService.php`
  - `app/Console/Commands/TutupWaitingListExpiredCommand.php`
  - `resources/views/admin/waiting-list/index.blade.php`
  - `tests/Feature/TutupWaitingListCommandTest.php`
- **Instruksi Teknis**:
  1. Seluruh operasi penerimaan peserta dan promosi waiting list **WAJIB** berada di dalam `DB::transaction()` dengan `Kuota::where(...)->lockForUpdate()->firstOrFail()`.
  2. Periksa kapasitas: jika `terisi >= jumlah`, tolak dengan `KuotaPenuhException`. Naikkan kuota via `$kuota->increment('terisi')`.
  3. Pada `TutupWaitingListExpiredCommand.php`, periksa bulan magang yang telah melewati tanggal mulai hari pertama (misal tgl >= 1 di bulan berjalan). Seluruh antrean pendaftaran `waiting_list` pada bulan tersebut otomatis diubah menjadi `ditolak` dengan catatan sistem: *"Waiting List ditutup otomatis karena periode magang telah dimulai (AC-04-7)"*.
- **Verifikasi Test**:
  `php artisan test --filter=TutupWaitingListCommandTest`

---

#### ─── TIKET TASK-ADM-03 ───
- **Judul**: Penempatan Peserta, Batas Beban Pembimbing & Aturan Pembatalan
- **Prioritas**: P0 (Business Rule)
- **Rujukan**: PRD AC-07, BR-08, BR-12, BR-13; TDD 5.4
- **Target File**:
  - `app/Http/Controllers/Admin/PenempatanController.php`
  - `app/Services/PlacementService.php`
  - `resources/views/admin/penempatan/index.blade.php`
  - `tests/Feature/PenempatanBebanPembimbingTest.php`
- **Instruksi Teknis**:
  1. Saat admin menempatkan peserta ke pembimbing, hitung mentee aktif pembimbing tersebut:
     `$bebanAktif = Penempatan::where('pembimbing_id', $pembimbing->id)->where('status', StatusPenempatanEnum::Aktif)->count();`
  2. Jika `$pembimbing->batas_mentee` diset dan `$bebanAktif >= $pembimbing->batas_mentee`, tolak penempatan dengan pesan ramah: *"Beban pembimbing telah mencapai batas maksimal ({$pembimbing->batas_mentee} mentee)"*.
  3. Pembatalan penempatan:
     - Jika dibatalkan **sebelum** `tgl_mulai` (H-1 atau sebelumnya): kembalikan kuota `$kuota->decrement('terisi')`. Status penempatan diubah menjadi `Nonaktif` (BR-05).
     - Jika dibatalkan **pada atau setelah** `tgl_mulai` (peserta mengundurkan diri): kuota **tidak boleh dikembalikan / hangus** (BR-07), status diubah menjadi `MengundurkanDiri`.
- **Verifikasi Test**:
  `php artisan test --filter=PenempatanBebanPembimbingTest`

---

#### ─── TIKET TASK-PEM-01 ───
- **Judul**: Manajemen Tugas Mentee, Penegakan Deadline & Lampiran Dokumen
- **Prioritas**: P1 (Mentoring Workflow)
- **Rujukan**: PRD AC-08-1..7; BR-16; TDD 6.1; Design System 07
- **Target File**:
  - `app/Http/Controllers/Pembimbing/TugasController.php`
  - `app/Models/Tugas.php`
  - `resources/views/pembimbing/tugas/index.blade.php`
  - `resources/views/pembimbing/tugas/create.blade.php`
  - `tests/Feature/RichTaskAssignmentTest.php`
- **Instruksi Teknis**:
  1. Penugasan mentee wajib menyertakan kategori tugas, judul, deskripsi, dan batas waktu (`tenggat_waktu`).
  2. Tanggal batas waktu minimal adalah hari ini pukul 23:59 dan maksimal sama dengan `tgl_selesai` periode magang peserta (AC-08-1).
  3. Tambahkan accessor otomatis pada model `Tugas`:
     `getIsTerlambatAttribute()` bernilai true jika `status !== StatusTugasEnum::Selesai` dan `now()->gt($this->tenggat_waktu)`.
  4. Lampiran tugas dibatasi maksimal 10 MB per berkas dengan ekstensi yang diizinkan (PDF, ZIP, DOCX, XLSX).
- **Verifikasi Test**:
  `php artisan test --filter=RichTaskAssignmentTest`

---

#### ─── TIKET TASK-PEM-02 ───
- **Judul**: Reviu Logbook Harian Mentee & Penegakan Aturan Batas Mundur 3 Hari
- **Prioritas**: P1 (Mentoring Daily Workflow)
- **Rujukan**: PRD AC-09-1..5; BR-09; TDD 6.2
- **Target File**:
  - `app/Http/Controllers/Pembimbing/LogbookReviewController.php`
  - `resources/views/pembimbing/logbook/index.blade.php`
  - `tests/Feature/TugasLogbookTest.php`
- **Instruksi Teknis**:
  1. Peserta hanya dapat mengisi logbook untuk hari ini atau maksimal mundur 3 hari ke belakang (BR-09). Input tanggal sebelum H-3 atau tanggal di masa depan wajib ditolak oleh validasi.
  2. Pada tabel `logbook`, penegakan database unik komposit: `unique('penempatan_id', 'tanggal')`.
  3. Pada dashboard pembimbing, sajikan filter status logbook: `Menunggu Reviu`, `Disetujui`, `Perlu Revisi`.
  4. Jika logbook berstatus `Menunggu Reviu` telah lebih dari 3 hari kerja, tampilkan indikator badge merah *"SLA Reviu Terlewat"*.
- **Verifikasi Test**:
  `php artisan test --filter=TugasLogbookTest`

---

#### ─── TIKET TASK-PEM-03 ───
- **Judul**: Penilaian Akhir Mentee, Snapshot Bobot Rubrik & Audit Trail Perubahan Nilai
- **Prioritas**: P0 (Academic & Certification Core)
- **Rujukan**: PRD AC-10-1..7; BR-10; TDD 6.3; Kamus Data Tabel 16 & 17
- **Target File**:
  - `app/Services/ScoringService.php`
  - `app/Http/Controllers/Pembimbing/PenilaianController.php`
  - `resources/views/pembimbing/penilaian/create.blade.php`
  - `resources/views/pembimbing/penilaian/show.blade.php`
  - `tests/Feature/ScoringAndCompletionTest.php`
- **Instruksi Teknis**:
  1. Skor diinput untuk setiap kriteria rubrik aktif dengan skala 0 s.d. 100.
  2. Saat penilaian disimpan, **WAJIB** menyalin `bobot_snapshot = $rubrik->bobot` ke tabel `penilaian_detail` sehingga jika di masa depan master rubrik diubah, nilai lama peserta tidak berubah (AC-10-4).
  3. Nilai akhir dihitung dengan presisi desimal:
     `Nilai Akhir = SUM(skor * bobot_snapshot) / SUM(bobot_snapshot)`.
     Predikat: A (>=85.00), B (70.00 - 84.99), C (55.00 - 69.99), D (40.00 - 54.99), E (<40.00).
  4. Revisi nilai yang sudah berstatus `Final` wajib menyertakan input alasan minimal 10 karakter dan otomatis dicatat ke tabel `penilaian_riwayat` (AC-10-3).
- **Verifikasi Test**:
  `php artisan test --filter=ScoringAndCompletionTest`

---

#### ─── TIKET TASK-PEM-04 ───
- **Judul**: Penerbitan Lembar Nilai PDF Resmi On-The-Fly Ber-QR Code & Anti-IDOR
- **Prioritas**: P0 (Deliverable Magang)
- **Rujukan**: PRD AC-10-6, AC-12; TDD 8.5; SEC-34
- **Target File**:
  - `app/Http/Controllers/Public/LembarNilaiController.php`
  - `resources/views/pdf/lembar-nilai.blade.php`
  - `tests/Feature/ScoringAndCompletionTest.php`
- **Instruksi Teknis**:
  1. Lembar nilai hanya dapat diterbitkan jika status penilaian sudah `Final`. Status `Draft` dilarang mengunduh lembar nilai resmi.
  2. Menggunakan Dompdf dengan konfigurasi aman: `enable_remote = false`, `chroot = storage_path('fonts')`, tanpa eksekusi skrip internal.
  3. Sertakan kode verifikasi keaslian (UUID hash) dan barcode/QR Code yang memvalidasi bahwa lembar nilai dikeluarkan resmi oleh Dinas Kominfo.
- **Verifikasi Test**:
  `php artisan test --filter=ScoringAndCompletionTest`

---

#### ─── TIKET TASK-SUP-01 ───
- **Judul**: Executive Command Dashboard SuperAdmin & Segmented Tab Controller
- **Prioritas**: P1 (Governance UI)
- **Rujukan**: PRD AC-11; TDD 7.1; Design System 07
- **Target File**:
  - `app/Http/Controllers/SuperAdmin/DashboardController.php`
  - `resources/views/superadmin/dashboard.blade.php`
  - `tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`
- **Instruksi Teknis**:
  1. Dashboard SuperAdmin menyediakan 3 segmented tab:
     - **Tab Seleksi**: Utilisasi kuota per bidang, antrean pendaftaran belum diverifikasi, status SLA verifikasi.
     - **Tab Bimbingan**: Rekap beban aktif per pembimbing, tugas terlambat lintas bidang, mentee offboarding.
     - **Tab Tata Kelola & Sistem**: Status operasional landing page, threat radar audit logs, pintasan ekspor laporan.
  2. Query metrik wajib dioptimalkan dengan eager loading dan `withCount()` guna memastikan eksekusi database di bawah 100 ms (NFR-P1).
- **Verifikasi Test**:
  `php artisan test --filter=SuperAdminUnifiedDashboardFeaturesTest`

---

#### ─── TIKET TASK-SUP-02 ───
- **Judul**: Tata Kelola Master Data & Kebijakan Landing Page Kuota
- **Prioritas**: P1 (Administrative Control)
- **Rujukan**: PRD AC-11-1..8; BR-04; TDD 7.2
- **Target File**:
  - `app/Http/Controllers/SuperAdmin/BulanController.php`
  - `app/Http/Controllers/SuperAdmin/SlotKhususController.php`
  - `app/Http/Controllers/SuperAdmin/RubrikController.php`
  - `app/Http/Controllers/SuperAdmin/PengaturanController.php`
  - `tests/Feature/SuperAdminPortalVisualTest.php`
- **Instruksi Teknis**:
  1. Penambahan master bulan magang: validasi bulan unik dan rentang tanggal valid.
  2. Menonaktifkan master data (Jurusan, Bulan, Kuota) yang masih memiliki relasi pendaftaran atau penempatan aktif **WAJIB ditolak** oleh sistem dengan pesan yang informatif (AC-11-2).
  3. Total bobot master rubrik penilaian aktif wajib berjumlah tepat 100%. Form menolak penyimpanan jika total bobot bukan 100 (AC-10-4).
- **Verifikasi Test**:
  `php artisan test --filter=SuperAdminPortalVisualTest`

---

#### ─── TIKET TASK-SUP-03 ───
- **Judul**: Manajemen Staf, Zero-SuperAdmin Guard & Pemutusan Sesi Realtime
- **Prioritas**: P0 (Access Governance)
- **Rujukan**: Addendum SEC-14, SEC-02, SEC-03; PRD 8.2; BR-12
- **Target File**:
  - `app/Http/Controllers/SuperAdmin/StafController.php`
  - `app/Policies/UserPolicy.php`
  - `tests/Feature/StaffProfileAndSettingsTest.php`
- **Instruksi Teknis**:
  1. SuperAdmin **dilarang menonaktifkan akun miliknya sendiri** atau mengubah role dirinya sendiri.
  2. Sistem wajib mencegah operasi apapun yang menyebabkan jumlah SuperAdmin aktif menjadi 0 (Zero SuperAdmin Guard).
  3. Saat akun staf dinonaktifkan oleh SuperAdmin, catat event ke `audit_logs` dan seluruh sesi aktif user tersebut langsung tidak valid pada request berikutnya (SEC-03).
- **Verifikasi Test**:
  `php artisan test --filter=StaffProfileAndSettingsTest`

---

#### ─── TIKET TASK-SUP-04 ───
- **Judul**: Audit Trail Log Viewer Forensik, Sanitasi Payload & Radar Keamanan
- **Prioritas**: P0 (Compliance & Forensic Readiness)
- **Rujukan**: PRD AC-13; Addendum SEC-36, SEC-71; TDD 9.4
- **Target File**:
  - `app/Http/Controllers/SuperAdmin/AuditLogController.php`
  - `resources/views/superadmin/audit/index.blade.php`
  - `tests/Feature/SuperAdminDashboardAndAuditLogTest.php`
- **Instruksi Teknis**:
  1. Menampilkan seluruh entri `audit_logs` dengan pagination efisien dan filter rentang tanggal, aktor, dan jenis aksi (`login`, `mutasi`, `keputusan`, `nilai`).
  2. Seluruh payload yang dirender di layar (termasuk User-Agent dan IP) **WAJIB di-escape** secara ketat untuk mencegah serangan stored XSS terhadap SuperAdmin (SEC-36).
  3. Tabel `audit_logs` bersifat *strictly append-only*; tidak ada rute edit atau delete pada controller maupun interface SuperAdmin.
- **Verifikasi Test**:
  `php artisan test --filter=SuperAdminDashboardAndAuditLogTest`

---

## 6. DAFTAR PERUBAHAN FILE (FILE INVENTORY)

| Path Berkas | Aksi | Ringkasan Perubahan & Kepatuhan Arsitektur |
| :--- | :---: | :--- |
| `app/Http/Middleware/EnsureAccountActive.php` | Inspect & Maintain | Pemutusan sesi instan untuk akun `status_aktif = false` (SEC-03) |
| `app/Http/Middleware/SecurityHeaders.php` | Inspect & Maintain | Penguncian CSP bertahap, HSTS, no-store, nosniff (SEC-32) |
| `app/Http/Middleware/EnsureSuperAdminReadOnly.php` | Maintain | Enforce read-only 403 bagi SuperAdmin di rute bimbingan (SEC-12) |
| `app/Services/PlacementService.php` | Maintain | Pessimistic locking kuota atomik & penempatan mentee (BR-01, BR-05) |
| `app/Services/ScoringService.php` | Maintain | Perhitungan nilai bulat, snapshot bobot rubrik, audit revisi (AC-10) |
| `app/Services/ExportReportService.php` | Maintain | Sanitasi sel anti CSV Formula Injection (AC-17-2) |
| `app/Console/Commands/TutupWaitingListExpiredCommand.php` | Maintain | Scheduler otomatis penutupan waiting list kedaluwarsa (AC-04-7) |
| `database/seeders/DummyDuaPuluhPesertaSeeder.php` | Maintain | Seeder 20 variasi kasus riil peserta (Pendaftaran, Tugas, Nilai, Alumni) |
| `resources/views/components/badge.blade.php` | Maintain | Reusable token badge UI (Design System 07) |
| `resources/views/components/empty-state.blade.php` | Maintain | Reusable empty state UI (Design System 07) |
| `resources/views/components/stat-card.blade.php` | Maintain | Reusable metric stat card UI (Design System 07) |
| `tests/Feature/*` (18 Test Suites) | Verify | Pemeliharaan 298 regression test suites tetap 100% lulus |

---

## 7. LANGKAH IMPLEMENTASI & EKSEKUSI

*Catatan: Langkah-langkah ini akan dieksekusi secara berurutan dan terverifikasi:*

- [x] **Langkah 1**: Audit Keamanan Tingkat Konfigurasi Server & Middleware (Verifikasi `SecurityHeaders.php`, `EnsureAccountActive.php`, dan `TrustHosts.php`).
- [x] **Langkah 2**: Verifikasi Concurrency Kuota & Pessimistic Locking pada `PlacementService.php` dan `QuotaAllocationService.php`.
- [x] **Langkah 3**: Penegakan Sanitasi Wildcard SQL LIKE dan Formula Injection pada `ExportReportService.php` dan Controller Tabular.
- [x] **Langkah 4**: Pengecekan Kepatuhan Design System Figma (Target sentuh 44px WCAG AA dan Reusable Components) pada seluruh view Admin, Pembimbing, dan SuperAdmin.
- [x] **Langkah 5**: Validasi Integritas Data Seeder 20 Kasus Peserta Magang di lingkungan lokal dan staging.
- [x] **Langkah 6**: Eksekusi Full Regression Test Suite (`php artisan test`) untuk memastikan 298+ assertions lulus tanpa regresi.

---

## 8. STRATEGI TESTING & QUALITY ASSURANCE

### 8.1 Automated Test Execution
- **Unit & Concurrency Tests**: Menguji lock pesimistik kuota paralel, kalkulasi formula skor rubrik, dan token masking.
- **Feature Tests**: Memvalidasi alur bisnis FIFO seleksi, transisi status pendaftaran, submit tugas dan revisi logbook, finalisasi nilai, serta ekspor CSV ber-sanitasi.
- **RBAC Matrix Tests**: Menguji bahwa SuperAdmin ditolak 403 saat POST/PUT ke tugas pembimbing, peserta ditolak mengakses rute staf (403), dan pembimbing ditolak mengunduh berkas pendaftaran (PRD 3.3).

### 8.2 Production Health Check Commands
```bash
# 1. Jalankan seluruh test suite otomatis
php artisan test

# 2. Verifikasi scheduler penutupan waiting list
php artisan magkom:tutup-waiting-list

# 3. Jalankan seeding 20 kasus peserta
php artisan magkom:seed-20-peserta

# 4. Validasi route caching dan config caching siap produksi
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 9. RISIKO & MITIGASI (RISK MANAGEMENT)

| Potensi Risiko | Tingkat | Dampak | Strategi Mitigasi Teruji |
| :--- | :---: | :--- | :--- |
| **Race Condition Mutasi Kuota** | Tinggi | Kuota jurusan terisi melebihi batas (*oversold*) | Transaksi InnoDB + `lockForUpdate()` dengan 3x deadlock retry di `PlacementService` |
| **Kebocoran Berkas Peserta (IDOR)** | Tinggi | Berkas KTP/Rapor dibuka oleh pengguna lain | Rute bertanda tangan sementara (Signed URL 5 menit) + verifikasi Policy kepemilikan berkas |
| **XSS Tersimpan di Audit Log** | Sedang | Skrip berbahaya terinjeksi dari input User-Agent / Catatan | Pemotongan karakter, escaping Blade default `{{ }}`, tanpa `{!! !!}` pada log viewer |
| **Formula Injection pada CSV Ekspor** | Sedang | Perangkat staf terkompromi saat membuka file laporan di Excel | Sanitasi awalan sel berkarakter `=`, `+`, `-`, `@` dengan petik tunggal (`'`) di `ExportReportService` |

---

## 10. KRITERIA SELESAI (DEFINITION OF DONE - DOD)

- [x] Blueprint kesesuaian best practice Laravel 10 industri telah didokumentasikan lengkap dan terstruktur.
- [x] Katalog tiket tugas senior-ke-junior/AI (WBS) untuk Dashboard Admin, Pembimbing, dan SuperAdmin telah didefinisikan preskriptif dan bebas ambiguitas.
- [x] Data 20 peserta magang dummy mencakup seluruh variasi kasus riil telah tersedia dan tervalidasi.
- [x] Seluruh suite pengujian otomatis (`php artisan test`) lulus 100% tanpa kegagalan (298 passed, 1681 assertions).
- [x] Dokumen rencana diajukan kepada pengguna untuk ditinjau dan disetujui sebelum tahap eksekusi kode lanjutan.
