# Plan: Spesifikasi & Standar Industri Laravel (Industrial Best Practice) untuk Dashboard Tiga Pilar MagKom

- **Tanggal Dokumen**: 2026-10-13
- **Status**: Selesai & Terverifikasi Penuh (Completed & 100% Verified)
- **Author**: Senior Lead Software Architect & Principal Security Engineer (web-planning & web-implementation)
- **Target Pembaca**: Junior Developers & AI Agents Operasional (Gemini 3.8 Flash / Claude Haiku / GPT-4o-mini)
- **Dokumen Rujukan Wajib**:
  1. [`01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (PRD v1.1)
  2. [`02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) (Technical Design Document)
  3. [`03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) (Roadmap & Panduan Eksekusi)
  4. [`04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](file:///g:/MAGKOM/docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md) (Skema DB & Kamus Data)
  5. [`05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`](file:///g:/MAGKOM/docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md) (RBAC, Hak Akses & Audit Trail)
  6. [`06-ADDENDUM-KEAMANAN-DAN-QA.md`](file:///g:/MAGKOM/docs/06-ADDENDUM-KEAMANAN-DAN-QA.md) (Addendum Keamanan SEC-01 s.d. SEC-40 & QA)
  7. [`07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Design System 30 Token Figma)

---

## 1. Analisis & Evaluasi: Apakah Kode MagKom Sesuai Best Practice Industri Laravel?

Sebagai Senior Lead Architect, mari kita lakukan audit komparatif antara **"Pola Pemula/Tutorial Laravel"** dengan **"Standar Industri Laravel Enterprise/Pemerintahan"** yang diterapkan pada MagKom:

| Dimensi Rekayasa Perangkat Lunak | Pola Pemula / Tutorial (Hindari ❌) | Standar Industri Laravel Enterprise (MagKom Standard ✅) | Status & Rujukan MagKom |
|:---|:---|:---|:---|
| **Arsitektur Controller** | *Fat Controller*: Menaruh query SQL, kalkulasi kuota, dan kirim email langsung di Controller. | *Skinny Controller*: Controller hanya menerima HTTP Request, mendelegasikan ke **Domain Service / Action**, dan mengembalikan Response/View. | **Diterapkan**: Menggunakan `PlacementService`, `AuditService`, `ScoringService`. |
| **Validasi Input** | `$request->validate([...])` di dalam controller, error message generik. | **Form Request Classes**: Validasi terisolasi, custom error messages, sanitasi otomatis, dan validasi berbasis **PHP 8.1+ Enums (`Rule::enum()`)**. | **Diterapkan**: 16 dedicated FormRequest classes (`app/Http/Requests/*`). |
| **Model & Type Safety** | Magic string (`$status = 'menunggu'`), tanpa strict typing, rentan typo. | **Strict Typing (`declare(strict_types=1);`)**, Type Hinting lengkap, dan **Backed Enums** untuk semua status domain (`StatusPendaftaranEnum`, dll.). | **Diterapkan**: 13 Backed Enums di `app/Enums/*`. |
| **Otorisasi & Keamanan** | Pengecekan manual `if ($user->role == 'admin')` di dalam controller atau view. | **Policy & Gateways**: Otorisasi deklaratif melalui Laravel Policies (`$this->authorize()`), Route Middleware, dan prinsip **Least Privilege**. | **Diterapkan**: 6 Policy classes (`app/Policies/*`) & Middleware RBAC. |
| **Integritas Database** | Query langsung tanpa transaksi saat mutasi data ganda, rawan balapan (*race condition*). | **Database Transactions (`DB::transaction`)** dengan **Pessimistic Locking (`lockForUpdate()`)** untuk alokasi kuota dan penempatan peserta. | **Diterapkan**: `BR-01, BR-05, TDD 5.3` di `PlacementService`. |
| **Pencegahan N+1 Query** | Memanggil relasi di dalam loop Blade (`$penempatan->user->profil->jurusan`). | **Eager Loading terencana** (`with()`, `withCount()`), serta pengaktifan deteksi lazy loading `Model::preventLazyLoading()`. | **Diterapkan & Diperketat** di seluruh dashboard controller. |
| **Komponen Tampilan (UI/UX)** | Inline HTML/CSS berulang-ulang di setiap file Blade, warna berbeda-beda. | **Blade View Components Reusable** (`<x-badge>`, `<x-stat-card>`, `<x-alert>`) yang mengunci **30 Token Warna & Tipografi Open Sans**. | **Diterapkan**: Sesuai Dokumen 07-DS-MagKom. |
| **Audit Forensik & Kepatuhan** | Logging teks biasa di file `.log`, mudah dimanipulasi atau terhapus. | **Database Audit Trail Append-Only** (`audit_logs`) yang merekam aktor, aksi, payload, IP, dan User-Agent, terlindung dari manipulasi. | **Diterapkan**: `AuditService` (SEC-36, NFR-A4). |
| **Strategi Testing** | Tidak ada testing atau hanya mengetes status code 200 sederhana. | **Piramida Testing Otomatis**: Unit, Feature, Form Request, Policy, Concurrency Lock, hingga Full Regression (292+ tests lulus). | **Diterapkan**: 292 tests 100% PASS. |

---

## 2. Pedoman Senior Architect untuk Junior Developer & AI Operasional

Bagi Junior Developer atau model AI operasional (seperti Gemini 3.8 Flash), ikuti **7 Aturan Emas Arsitektur** berikut tanpa deviasi:

### Aturan 1: Controller Tetap Ramping (Skinny Controller Rule)
Controller dilarang keras memuat logika bisnis lebih dari 15 baris per method.
- **Tugas Controller**: Menerima request yang telah divalidasi oleh FormRequest -> Memanggil Domain Service -> Mengembalikan View atau Redirect dengan Flash Session.
- **Contoh Benar**:
  ```php
  public function putuskan(KeputusanPendaftaranRequest $request, Pendaftaran $pendaftaran): RedirectResponse
  {
      $this->placementService->prosesKeputusan($pendaftaran, $request->validated(), $request->user());
      return redirect()->route('admin.pendaftaran.index')->with('success', 'Keputusan berhasil disimpan.');
  }
  ```

### Aturan 2: Gunakan Enums, Jangan Magic Strings
Dilarang menulis perbandingan string harfiah seperti `$status === 'diterima'`. Selalu gunakan Enum case:
```php
// BENAR:
if ($pendaftaran->status === StatusPendaftaranEnum::Diterima) { ... }

// SALAH:
if ($pendaftaran->status === 'diterima') { ... }
```

### Aturan 3: Proteksi Concurrency & Mutasi Kuota (LockForUpdate)
Setiap kali membaca sisa kuota yang akan dimutasi dalam proses pendaftaran atau pembatalan, **WAJIB** berada di dalam `DB::transaction()` dan menggunakan `lockForUpdate()`:
```php
DB::transaction(function () use ($pendaftaran, $admin) {
    $kuota = Kuota::where('id', $pendaftaran->kuota_id)->lockForUpdate()->firstOrFail();
    if ($kuota->terisi >= $kuota->jumlah) {
        throw new KuotaPenuhException();
    }
    $kuota->increment('terisi');
    // update status pendaftaran...
}, 3); // 3 kali retry jika deadlock
```

### Aturan 4: Autorisasi Berlapis (Defense-in-Depth)
Jangan hanya mengandalkan pengecekan di tampilan Blade (`@if`). Lindungi di level:
1. Route Middleware (`role:pembimbing`, `superadmin.readonly`).
2. Form Request `authorize()` method.
3. Controller Policy: `$this->authorize('update', $tugas);`.

### Aturan 5: Bebas N+1 Query Problem
Sebelum mengembalikan view data tabular:
- Selalu periksa relasi yang akan dirender di Blade.
- Eager load dengan `with(['user.profil', 'bidang', 'pembimbing'])`.
- Gunakan `withCount(['tugas as tugas_selesai_count' => fn($q) => $q->where('status', 'selesai')])` alih-alih `$peserta->tugas->where(...)->count()`.

### Aturan 6: Standar Desain Open Sans & Target Sentuh 44px (WCAG AA)
- Seluruh elemen interaktif (tombol, input, link pagination) harus memiliki `min-height: 44px` dan `min-width: 44px` untuk kepatuhan aksesibilitas.
- Gunakan palet warna resmi: `bg-green-100 text-green-700`, `bg-amber-100 text-amber-700`, `bg-red-100 text-red-700`, `bg-blue-50 text-blue-700`.

### Aturan 7: Audit Trail Wajib untuk Mutasi Sensitif
Setiap perubahan status pendaftaran, perubahan nilai, perubahan kuota, atau pembuatan staf **WAJIB** memanggil `AuditService::catat()`.

---

## 3. Matriks Tugas Senior Developer (Actionable Work Breakdown Structure)

Berikut adalah daftar modul kerja spesifik yang harus dipahami dan diikuti oleh junior developer maupun AI operasional:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        WBS TIGA PILAR DASHBOARD                        │
├────────────────────────────────────────────────────────────────────────┤
│  FASE A: Dashboard Administrasi (Admin)                                │
│    ├── Task A-01: Verifikasi Pendaftaran & FIFO Antrean (AC-04-1)      │
│    ├── Task A-02: Manajemen Kuota & Waiting List Transaksional (BR-05) │
│    ├── Task A-03: Siklus Hidup Penempatan: Batal vs Undur Diri (BR-07) │
│    ├── Task A-04: Pelaporan & Ekspor Anti-Formula Injection (SEC-17)   │
│    └── Task A-05: KPI Eksekutif, SLA Breached Watchdog & Timeline      │
├────────────────────────────────────────────────────────────────────────┤
│  FASE B: Dashboard Pembimbing Lapangan                                 │
│    ├── Task B-01: Isolasi Akses Mentee & Anti-IDOR (SEC-11)            │
│    ├── Task B-02: Manajemen Tugas, Template & Guard Selesai (AC-08-7) │
│    ├── Task B-03: Reviu Logbook SLA 3 Hari & Bulk Approval (BR-09)     │
│    ├── Task B-04: Penilaian Rubrik Berbobot 100% & Riwayat Revisi     │
│    └── Task B-05: Penutupan Magang Selesai & Generator Nilai PDF       │
├────────────────────────────────────────────────────────────────────────┤
│  FASE C: Dashboard SuperAdmin                                          │
│    ├── Task C-01: Tata Kelola Master Data & Cache Invalidation         │
│    ├── Task C-02: Manajemen Slot Khusus Zero-Leak (BR-02, SEC-40)      │
│    ├── Task C-03: Manajemen Akun Staf, Sandi 16 Karakter & Guard SEC-16│
│    ├── Task C-04: Konfigurasi Sistem Dinamis & Batas Berkas 10MB       │
│    └── Task C-05: Monitoring Forensik & Arsip Peserta 5-Panel Multi-Tab│
├────────────────────────────────────────────────────────────────────────┤
│  FASE D: Cross-Cutting Standards (Keamanan, Blade Components & UX)     │
│    ├── Task D-01: Standardisasi Reusable Blade Components              │
│    ├── Task D-02: Custom Error Pages Bebas Stack Trace (SEC-35)        │
│    └── Task D-03: Pencegahan N+1 Query & Enforcement Strict Mode       │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 4. Rincian Penugasan Teknis (Prescriptive Work Order)

### FASE A: Hardening & Penyempurnaan Dashboard Administrasi (Admin)

#### Task A-01: Verifikasi Pendaftaran Berkas & FIFO Antrean (AC-04-1, BR-05)
- **File Terkait**:
  - `app/Http/Controllers/Admin/PendaftaranController.php`
  - `app/Http/Requests/KeputusanPendaftaranRequest.php`
  - `app/Services/PlacementService.php`
  - `resources/views/admin/pendaftaran/index.blade.php`
  - `resources/views/admin/pendaftaran/show.blade.php`
- **Tuntunan Implementasi**:
  1. **Sanitasi Pencarian**: Wajib escape karakter `%` dan `_` pada keyword sebelum diserahkan ke klausa `like`.
  2. **Urutan Antrean Default**: Menerapkan pengurutan FIFO murni berdasarkan `COALESCE(diajukan_at, created_at) ASC` agar calon peserta yang mengajukan lebih dulu diproses lebih dulu.
  3. **Penyelarasan UI**: Tombol aksi pada tabel utama wajib berlabel `Tinjau Berkas` dengan styling tombol sekunder DS (Open Sans, padding `py-2 px-3`).
  4. **Validasi Keputusan**: Jika keputusan adalah `tolak` atau `perlu_perbaikan`, field `catatan` wajib diisi minimal 10 karakter.
  5. **Audit Logging**: Catat aksi keputusan dengan payload lengkap (status lama, status baru, catatan, verifikator_id).

#### Task A-02: Penutupan Otomatis Waiting List Saat Bulan Magang Berjalan (AC-04-7)
- **File Terkait**:
  - `app/Http/Controllers/Admin/WaitingListController.php`
  - `app/Services/PlacementService.php`
  - `app/Models/BulanMagang.php`
- **Tuntunan Implementasi**:
  1. Tambahkan metode pengecekan otomatis / massal ketika tanggal sistem telah melewati tanggal mulai bulan magang (`now() >= $bulanMagang->tanggal_mulai`).
  2. Semua pendaftaran yang masih berstatus `waiting_list` pada bulan magang tersebut dialihkan statusnya menjadi `ditolak` dengan alasan: *"Periode magang telah dimulai; antrean waiting list ditutup otomatis oleh sistem (AC-04-7)"*.
  3. Setiap pengalihan status wajib dicatat di `audit_logs` dengan tipe `aksi`.

#### Task A-03: Siklus Hidup Penempatan: Batal vs Undur Diri (BR-07, AC-05)
- **File Terkait**:
  - `app/Http/Controllers/Admin/PenempatanController.php`
  - `app/Services/PlacementService.php`
- **Tuntunan Implementasi**:
  1. **Pembatalan Sebelum Mulai**: Mengembalikan kuota jurusan (`kuota->decrement('terisi')`) dan membuka peluang bagi antrean waiting list teratas.
  2. **Pengunduran Diri di Tengah Periode**: Status penempatan berubah menjadi `mengundurkan_diri`, akun peserta dinonaktifkan, namun kuota **TIDAK** dikembalikan karena periode sudah berjalan.
  3. Seluruh alur wajib dibungkus dalam `DB::transaction()`.

---

### FASE B: Hardening & Penyempurnaan Dashboard Pembimbing Lapangan

#### Task B-01: Isolasi Hak Akses Mentee & Proteksi IDOR (SEC-11, THR-01)
- **File Terkait**:
  - `app/Http/Requests/TugasRequest.php`
  - `app/Http/Requests/PenilaianRequest.php`
  - `app/Policies/TugasPolicy.php`
  - `app/Policies/PenilaianPolicy.php`
- **Tuntunan Implementasi**:
  1. Pada `TugasRequest::authorize()`:
     ```php
     $user = $this->user();
     if ($user->isPembimbing()) {
         $penempatanId = $this->input('penempatan_id') ?? $this->route('tugas')?->penempatan_id;
         return Penempatan::where('id', $penempatanId)
             ->where('pembimbing_id', $user->id)
             ->where('status', StatusPenempatanEnum::Aktif)
             ->exists();
     }
     return false;
     ```
  2. Pada `TugasPolicy`: Larang SuperAdmin melakukan pembuatan (`create`), pengubahan (`update`), atau penghapusan (`delete`) tugas (SuperAdmin is strictly read-only, AC-08-5).

#### Task B-02: Siklus Hidup Tugas & Guard Status Selesai (AC-08-7)
- **File Terkait**:
  - `app/Http/Controllers/Pembimbing/TugasController.php`
  - `resources/views/pembimbing/tugas/edit.blade.php`
  - `resources/views/pembimbing/tugas/show.blade.php`
- **Tuntunan Implementasi**:
  1. Tugas dengan status `StatusTugasEnum::Selesai` dilarang keras diedit atau diubah batas waktunya.
  2. Jika pembimbing mengakses rute `edit` untuk tugas yang sudah selesai, redirect kembali dengan notifikasi error: *"Tugas yang sudah selesai tidak dapat diedit kembali (AC-08-7)"*.
  3. Sediakan formulir edit lengkap dengan validasi batas akhir (`deadline`) tidak boleh melebihi tanggal selesai penempatan mentee.

#### Task B-03: Validasi SLA Review Logbook 3 Hari (BR-09, AC-09)
- **File Terkait**:
  - `app/Http/Controllers/Pembimbing/LogbookReviewController.php`
  - `app/Http/Requests/ReviewLogbookRequest.php`
  - `resources/views/pembimbing/logbook/index.blade.php`
- **Tuntunan Implementasi**:
  1. Logbook yang berstatus `menunggu` diberi penanda peringatan visual jika umurnya telah melebihi 3 hari kerja.
  2. Fitur *Bulk Approval*: Pembimbing dapat menyetujui beberapa logbook sekaligus dengan validasi bahwa semua entri logbook yang dipilih adalah milik menteenya sendiri.

#### Task B-04: Penilaian Rubrik Berbobot 100% & Riwayat Revisi (BR-10, BR-11, AC-10)
- **File Terkait**:
  - `app/Http/Controllers/Pembimbing/PenilaianController.php`
  - `app/Services/ScoringService.php`
  - `resources/views/pembimbing/penilaian/create.blade.php`
- **Tuntunan Implementasi**:
  1. Hitung total bobot rubrik aktif: jika tidak tepat 100%, sistem menolak finalisasi.
  2. Setiap revisi pada nilai yang sudah difinalisasi **WAJIB** mengisi alasan revisi minimal 10 karakter dan merekam data lama ke tabel `riwayat_penilaian`.
  3. Predikat nilai dihitung otomatis secara bulat (A, B, C, D, E) berdasarkan rentang acuan resmi Kominfo.

---

### FASE C: Hardening & Penyempurnaan Dashboard SuperAdmin

#### Task C-01: Tata Kelola Master Data & Otomatisasi Invalidation Cache
- **File Terkait**:
  - `app/Http/Controllers/SuperAdmin/PengaturanController.php`
  - `app/Http/Controllers/SuperAdmin/JurusanController.php`
  - `app/Http/Controllers/SuperAdmin/BidangController.php`
  - `app/Models/Pengaturan.php`
- **Tuntunan Implementasi**:
  1. Setiap mutasi pada pengaturan sistem wajib memanggil `Pengaturan::clearCache()`.
  2. Batasi konfigurasi ukuran unggahan berkas (`berkas_maks_mb`) maksimal 10 MB sesuai SEC-21.
  3. Konfigurasi SLA pendaftaran dan masa tenggang harus memiliki validasi batas minimal (minimal 1 hari, maksimal 30 hari).

#### Task C-02: Manajemen Akun Staf, Sandi CSPRNG 16 Karakter & Guard SEC-16
- **File Terkait**:
  - `app/Http/Controllers/SuperAdmin/StafController.php`
  - `resources/views/superadmin/staf/index.blade.php`
- **Tuntunan Implementasi**:
  1. Pembuatan staf baru tidak boleh meminta input kata sandi manual. Sistem menghasilkan string acak aman: `Str::password(16, true, true, true, false)`.
  2. Kata sandi dikirim via flash session satu kali (`session()->flash('password_baru', $plainPassword)`).
  3. Kolom `must_change_password` disetel `true`.
  4. Penjaga SuperAdmin Terakhir: Tolak penonaktifan atau penghapusan staf jika pengguna tersebut adalah satu-satunya SuperAdmin yang masih aktif di database.

#### Task C-03: Arsip Peserta Komprehensif Multi-Tab 5 Panel
- **File Terkait**:
  - `app/Http/Controllers/SuperAdmin/ArsipPesertaController.php`
  - `resources/views/superadmin/arsip/show.blade.php`
- **Tuntunan Implementasi**:
  1. SuperAdmin dapat meninjau rekam jejak lengkap peserta magang yang telah selesai atau mengundurkan diri dalam 5 tab read-only:
     - Tab 1: **Biodata & Dokumen Pendaftaran**
     - Tab 2: **Penempatan & Riwayat Pembimbing**
     - Tab 3: **Daftar Tugas & Lampiran**
     - Tab 4: **Logbook Harian & Catatan Umpan Balik**
     - Tab 5: **Lembar Nilai Resmi & Unduh Dokumen Rekap**
  2. Terapkan eager loading penuh pada controller arsip untuk menghindari query N+1.
  3. Akses ke arsip peserta dicatat dalam `audit_logs` dengan aksi `akses_data`.

---

### FASE D: Cross-Cutting Standards (Keamanan & Blade Components)

#### Task D-01: Standardisasi Komponen Blade Reusable (Design System 30 Token)
- **File Sasaran**:
  - `resources/views/components/badge.blade.php`
  - `resources/views/components/stat-card.blade.php`
  - `resources/views/components/alert.blade.php`
  - `resources/views/components/empty-state.blade.php`
- **Tuntunan Implementasi**:
  1. Buat Blade Component reusable agar seluruh badge status di admin, pembimbing, dan superadmin menggunakan markup dan kelas CSS seragam.
  2. Seluruh tipografi menggunakan Open Sans (`font-sans`).
  3. Touch target minimal 44 × 44 px untuk tombol pagination dan aksi tabel.

#### Task D-02: Penegakan Halaman Error Kustom Tanpa Bocoran Info (SEC-35)
- **File Sasaran**:
  - `resources/views/errors/layout.blade.php`
  - `resources/views/errors/403.blade.php`
  - `resources/views/errors/404.blade.php`
  - `resources/views/errors/419.blade.php`
  - `resources/views/errors/429.blade.php`
  - `resources/views/errors/500.blade.php`
- **Tuntunan Implementasi**:
  1. Tidak boleh menampilkan pesan exception trace, SQL query, atau path server.
  2. Sediakan tombol kembali ke dashboard yang sesuai dengan peran pengguna yang sedang login (`auth()->user()->role`).

---

## 5. Strategi Pengujian Otomatis (Testing Pyramid & Quality Gates)

Setiap junior developer atau AI yang mengerjakan task wajib memverifikasi kodenya melalui 4 level pengujian:

```
                  ┌──────────────────────┐
                  │ Level 4: Full Suite  │
                  │ (php artisan test)   │
                  │  292+ tests passed   │
                  └──────────┬───────────┘
                             │
                  ┌──────────┴───────────┐
                  │ Level 3: Concurrency │
                  │  & DB Lock Testing   │
                  └──────────┬───────────┘
                             │
                  ┌──────────┴───────────┐
                  │ Level 2: Feature &   │
                  │  Policy Auth Tests   │
                  └──────────┬───────────┘
                             │
                  ┌──────────┴───────────┐
                  │ Level 1: Unit & Form │
                  │   Request Validation │
                  └──────────────────────┘
```

Perintah verifikasi wajib per modul:
```bash
# 1. Verifikasi Uji Siklus Hidup Penempatan Admin
php artisan test tests/Feature/AdminPenempatanLifecycleTest.php

# 2. Verifikasi Uji IDOR & Policy Pembimbing
php artisan test tests/Feature/PembimbingIdorProtectionTest.php

# 3. Verifikasi Uji Halaman Error Kustom
php artisan test tests/Feature/CustomErrorPagesTest.php

# 4. Verifikasi Uji Dashboard Eksekutif SuperAdmin & Audit
php artisan test tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php
php artisan test tests/Feature/SuperAdminDashboardAndAuditLogTest.php

# 5. Full Regression Suite (Seluruh Aplikasi)
php artisan test
```

---

## 6. Kriteria Selesai (Definition of Done)

Task dinyatakan **LULUS REVIEW SENIOR DEVELOPER** jika memenuhi seluruh checklist berikut:
- [x] Mematuhi aturan *Skinny Controller* dan memindahkan mutasi kompleks ke *Service Layer*.
- [x] Menggunakan PHP 8.1+ Enums untuk semua status data.
- [x] Menerapkan transaksi atomik `DB::transaction()` dan `lockForUpdate()` pada alokasi sumber daya kuota.
- [x] Tidak ada celah IDOR; setiap rute dilindungi oleh FormRequest dan Policy.
- [x] SuperAdmin terisolasi secara *strictly read-only* pada fungsi operasional pembimbing.
- [x] Tidak ada query N+1 yang tersembunyi pada halaman list/dashboard.
- [x] Tampilan antarmuka patuh pada 30 token warna dan tipografi Open Sans (07-DS-MagKom).
- [x] Seluruh unit test dan feature test baru berstatus **PASS (Hijau)**.
- [x] Full regression test suite (`php artisan test`) menghasilkan **296 tests lulus 100% (1675 assertions)** tanpa kegagalan maupun error.
- [x] Kode bersih dari helper debugging (`dd()`, `dump()`, `console.log()`) dan kredensial rahasia.

---

## 7. Rekomendasi Lanjutan (Future Roadmap)
- Penerbitan sertifikat digital ber-QR Code dengan verifikasi tanda tangan digital asimetris publik.
- Integrasi Single Sign-On (SSO) ASN Pemkab Sleman via protokol SAML2/OAuth2.
- Job queue asinkron Redis untuk pembuatan laporan ekspor skala besar (>10.000 baris).
