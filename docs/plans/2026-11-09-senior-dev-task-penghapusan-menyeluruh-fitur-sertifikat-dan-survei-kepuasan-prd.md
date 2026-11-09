# TASK BRIEF SENIOR DEVELOPER: PENGHAPUSAN MENYELURUH FITUR SERTIFIKAT DIGITAL DAN MODUL SURVEI KEPUASAN (DECOMMISSIONING & SYSTEM CLEANUP)

> **Dari:** Senior Developer / Lead Software Architect  
> **Kepada:** Junior Developer / AI Agent (Gemini 3.8 Flash / Antigravity Agent)  
> **Target Repositori:** MAGKOM (Portal Manajemen Magang Dinas Kominfo Sleman)  
> **Status:** PROPOSED & READY FOR IMPLEMENTATION APPROVAL  
> **Tingkat Kesulitan:** Menengah - Tinggi (Feature Decommissioning, Route Cleanup, Database Migration Drop Table, View & Layout Pruning, Test Suite Alignment)  
> **Dokumentasi Terkait:** `AGENTS.md`, `docs/01-PRD-MagKom.md`, `docs/02-TDD-MagKom.md`, `docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`

---

## 1. 🔍 LATAR BELAKANG & TUJUAN TASK

### 1.1 Keputusan Arsitektur & Bisnis
Sesuai arahan dan kebutuhan terkini pemangku kepentingan Diskominfo Kabupaten Sleman:
1. **Modul Sertifikat Digital Kelulusan** tidak lagi digunakan dan ditiadakan dari sistem MagKom. Seluruh proses penerbitan, penomoran format surat sertifikat, template PDF sertifikat, serta halaman direktori sertifikat alumni ditiadakan.
2. **Modul Survei Kepuasan & Net Promoter Score (NPS) Alumni** tidak lagi dibutuhkan dan ditiadakan dari seluruh alur MagKom, baik di portal Peserta (pengisian kuesioner) maupun portal Admin (dashboard IKM/NPS dan ekspor CSV survei).
3. **Pembersihan Parameter Konfigurasi SuperAdmin**:
   - Sakelar `fitur_sertifikat_aktif` dan `fitur_survei_aktif` dihapus dari form dan database.
   - Parameter `format_nomor_sertifikat` (`500.12/SRT/`) dihapus dari form dan database.
   - Jumlah parameter operasional sistem disesuaikan dari **16 parameter menjadi 13 parameter operasional aktif**.

### 1.2 Target Task (Clear Objectives)
Junior Developer / AI Agent wajib menghapus seluruh artefak kode kedua fitur tersebut secara bersih, aman, dan tanpa meninggalkan dead code (*zero leftover*), serta memastikan seluruh test suite aplikasi tetap lulus 100%.

---

## 2. 📊 HASIL AUDIT MENYELURUH (COMPREHENSIVE CODE INVENTORY)

Berikut adalah daftar lengkap seluruh artefak kode yang teridentifikasi dan akan dieliminasi/disesuaikan:

### 🔹 A. Modul Sertifikat Digital
| Komponen | Lokasi File | Tindakan |
|---|---|:---:|
| **Controller** | `app/Http/Controllers/Admin/SertifikatController.php` | **DELETE** |
| **View Index** | `resources/views/admin/sertifikat/index.blade.php` | **DELETE** |
| **View PDF** | `resources/views/pdf/sertifikat-magang.blade.php` | **DELETE** |
| **Routes** | `routes/web.php` (Route `admin.sertifikat.index` & `admin.sertifikat.cetak`) | **DELETE** |
| **Menu Sidebar** | `resources/views/layouts/dashboard.blade.php` (Link navigasi sertifikat admin & mobile) | **PRUNE** |
| **Quick Action** | `resources/views/admin/dashboard/partials/_aside.blade.php` (Tombol "Hub Sertifikat" & link offboarding) | **PRUNE** |
| **Verifikasi Dokumen** | `resources/views/public/verifikasi-dokumen.blade.php` (Label sertifikat di halaman verifikasi) | **PRUNE** |

### 🔹 B. Modul Survei Kepuasan & NPS Alumni
| Komponen | Lokasi File | Tindakan |
|---|---|:---:|
| **Controller Admin** | `app/Http/Controllers/Admin/SurveiAdminController.php` | **DELETE** |
| **Controller Peserta**| `app/Http/Controllers/Peserta/SurveiController.php` | **DELETE** |
| **Model Eloquent** | `app/Models/SurveiKepuasan.php` | **DELETE** |
| **View Admin** | `resources/views/admin/survei/index.blade.php` | **DELETE** |
| **View Peserta** | `resources/views/peserta/survei/index.blade.php` | **DELETE** |
| **Routes** | `routes/web.php` (Route `admin.survei.*` & `peserta.survei.*`) | **DELETE** |
| **Menu Sidebar** | `resources/views/layouts/dashboard.blade.php` (Link navigasi survei admin & mobile) | **PRUNE** |
| **Tabel Database** | Tabel `survei_kepuasan` di database | **DROP TABLE VIA MIGRATION** |

### 🔹 C. Parameter Konfigurasi SuperAdmin & Seeder
| Komponen | Lokasi File | Tindakan |
|---|---|:---:|
| **Controller** | `app/Http/Controllers/SuperAdmin/PengaturanController.php` | Pangkas kunci `format_nomor_sertifikat`, `fitur_sertifikat_aktif`, `fitur_survei_aktif`. Ubah total parameter menjadi **13**. |
| **View Form** | `resources/views/superadmin/pengaturan/index.blade.php` | Hapus input nomor sertifikat di Kartu 3; Hapus toggle sertifikat & survei di Kartu 4; Ubah counter tile stat 1 menjadi **13**. |
| **Seeder** | `database/seeders/PengaturanSeeder.php` | Hapus 3 kunci tersebut dan tambahkan pembersihan `Pengaturan::deleteKunci()`. |
| **Database** | Tabel `pengaturan` | Hapus 3 record parameter dari database runtime. |

### 🔹 D. Automated Test Suite (PHPUnit)
| File Test | Rincian Penyesuaian |
|---|---|
| `tests/Feature/SurveiKepuasanAdminVisualTest.php` | **DELETE** (fitur dihapus total) |
| `tests/Feature/SuperAdminFeatureToggleTest.php` | Hapus assertion sertifikat & survei, fokuskan pada analitik & kapasitas mentee. |
| `tests/Feature/AdvancedThreePillarsDashboardTest.php` | Hapus test case `test_admin_sertifikat_index_and_cetak_alumni_terverifikasi` & `test_admin_sertifikat_denies_unfinalized_evaluation`. |
| `tests/Feature/AdvancedAdminAndSuperAdminPhaseFiveTest.php` | Hapus 3 test case survei kepuasan peserta & admin. |
| `tests/Feature/DeltaDashboardEnhancementTest.php` | Hapus assertion link sidebar sertifikat dan survei. |
| `tests/Feature/PredikatBatasValidationTest.php` | Ubah assertion total parameter menjadi **13**, hapus `format_nomor_sertifikat` dari payload testing. |

---

## 3. 🗺️ STRUKTUR BARU PARAMETER KONFIGURASI SISTEM (13 PARAMETER)

Setelah eliminasi 3 parameter tersebut, halaman **SuperAdmin -> Parameter Konfigurasi Sistem** akan memiliki komposisi bersih:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [HERO BANNER] Parameter Konfigurasi Sistem                                             │
│ Tata Kelola: Aturan Bisnis & Ambang Batas • Status: Tersimpan di Database              │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [4 TILE STAT COUNTERS]                                                                 │
│ 1. TOTAL PARAMETER: 13 Konfigurasi (Parameter Operasional Aktif)                       │
│ 2. KUOTA UMUM BULANAN: 12 Kuota (Batas Maksimal Kuota Umum / Bulan)                    │
│ 3. RASIO MENTEE / MENTOR: 20 : 1 (Maks Mentee per Pembimbing)                          │
│ 4. MASA TENGGANG: 2 Bulan (Durasi Akun Pasca Magang)                                   │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ ┌─ KARTU 1: Kebijakan Kuota, Pendaftaran & Kapasitas Magang (5 Parameter) ────────────┐ │
│ │ 1. Batas Kuota Umum Publik Per Bulan (12 Orang)                                     │ │
│ │ 2. Default Alokasi Slot Khusus (3 Orang)                                            │ │
│ │ 3. Beban Maksimal Mentee Per Pembimbing (20 Orang)                                  │ │
│ │ 4. Batas Maksimal Anggota Kelompok (4 Orang)                                        │ │
│ │ 5. Durasi Magang Maksimal yang Diizinkan (6 Bulan)                                  │ │
│ └─────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌─ KARTU 2: Aturan Pelaksanaan, Tugas & Dokumen (4 Parameter) ────────────────────────┐ │
│ │ 6. Batas Mundur Pengisian Logbook (3 Hari)                                          │ │
│ │ 7. Batas Ukuran Maksimal Berkas Upload (10 MB)                                      │ │
│ │ 8. Peringatan Mendekati Deadline Tugas (3 Hari)                                     │ │
│ │ 9. Target Batas Waktu SLA Verifikasi Berkas (5 Hari Kerja)                          │ │
│ └─────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌─ KARTU 3: Kebijakan Retensi Akun & Audit Trail (2 Parameter) ───────────────────────┐ │
│ │ 10. Durasi Masa Tenggang Akun Pasca Magang (2 Bulan)                                │ │
│ │ 11. Masa Retensi Log Akses Data (365 Hari)                                          │ │
│ │ [Tombol]: Jalankan Pemangkasan Log Sekarang                                         │ │
│ └─────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌─ KARTU 4: Sakelar Modul Fitur Sistem (2 Parameter) ─────────────────────────────────┐ │
│ │ 12. [✓] Modul Analitik Tahunan Eksekutif                                            │ │
│ │ 13. [✓] Modul Pendaftaran Kelompok / Tim                                            │ │
│ └─────────────────────────────────────────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. 🛠️ PANDUAN EKSEKUSI TEKNIS LANGKAH DEMI LANGKAH (STEP-BY-STEP)

---

### 🔹 FASE 1: Decommissioning Modul Sertifikat Digital
1. **Hapus File**:
   - `app/Http/Controllers/Admin/SertifikatController.php`
   - `resources/views/admin/sertifikat/index.blade.php`
   - `resources/views/pdf/sertifikat-magang.blade.php`
2. **Pangkas Route** di `routes/web.php`:
   - Hapus `use App\Http\Controllers\Admin\SertifikatController as AdminSertifikatController;`
   - Hapus grup rute:
     ```php
     Route::get('/sertifikat', [AdminSertifikatController::class, 'index'])->name('sertifikat.index');
     Route::get('/sertifikat/{penempatan}/cetak', [AdminSertifikatController::class, 'cetak'])->name('sertifikat.cetak');
     ```
3. **Pangkas Navigasi Admin**:
   - Di `resources/views/layouts/dashboard.blade.php`: Hapus blok menu sidebar Sertifikat (desktop dan mobile).
   - Di `resources/views/admin/dashboard/partials/_aside.blade.php`: Hapus tombol aksi cepat "Hub Sertifikat", dan ubah link pada panel Offboarding Watchdog menjadi `{{ route('admin.penempatan.index') }}` (label: "Penempatan &rarr;").
4. **Pembersihan Verifikasi Dokumen**:
   - Di `resources/views/public/verifikasi-dokumen.blade.php`: Hapus blok percabangan `@elseif($dokumen->tipe_dokumen === 'sertifikat')`.

---

### 🔹 FASE 2: Decommissioning Modul Survei Kepuasan & Migrasi Database
1. **Hapus File**:
   - `app/Http/Controllers/Admin/SurveiAdminController.php`
   - `app/Http/Controllers/Peserta/SurveiController.php`
   - `app/Models/SurveiKepuasan.php`
   - `resources/views/admin/survei/index.blade.php`
   - `resources/views/peserta/survei/index.blade.php`
2. **Pangkas Route** di `routes/web.php`:
   - Hapus `use App\Http\Controllers\Admin\SurveiAdminController;`
   - Hapus `use App\Http\Controllers\Peserta\SurveiController;`
   - Hapus rute survei peserta:
     ```php
     Route::get('/survei', [SurveiController::class, 'show'])->name('survei.show');
     Route::post('/survei', [SurveiController::class, 'store'])->name('survei.store');
     ```
   - Hapus rute survei admin:
     ```php
     Route::get('/survei', [SurveiAdminController::class, 'index'])->name('survei.index');
     Route::get('/survei/export', [SurveiAdminController::class, 'export'])->name('survei.export');
     ```
3. **Pangkas Navigasi Admin**:
   - Di `resources/views/layouts/dashboard.blade.php`: Hapus blok menu sidebar Survei Kepuasan (desktop dan mobile).
   - Hapus `request()->routeIs('admin.survei.*')` dari variabel `$isOperasionalAdminActive`.
4. **Buat Migrasi Database Drop Table**:
   - Buat migrasi: `database/migrations/2026_11_09_300000_drop_survei_kepuasan_table.php`
   - Isi:
     ```php
     public function up(): void
     {
         Schema::dropIfExists('survei_kepuasan');
     }
     ```
   - Jalankan migrasi via Artisan.

---

### 🔹 FASE 3: Pembaruan Parameter Konfigurasi Sistem SuperAdmin
1. **File Controller:** `app/Http/Controllers/SuperAdmin/PengaturanController.php`:
   - Di `index()`: Pangkas `$kunciOperasional` menjadi 13 kunci (tanpa `format_nomor_sertifikat`, `fitur_sertifikat_aktif`, `fitur_survei_aktif`).
   - Di `update()`: Hapus validasi dan penyimpanan untuk ketiga kunci tersebut.
2. **File View:** `resources/views/superadmin/pengaturan/index.blade.php`:
   - Tile Stat 1: Value `{{ $totalParameter }}` (otomatis bernilai 13).
   - Kartu 3: Ubah judul menjadi `Kebijakan Retensi Akun & Audit Trail`, hapus input `format_nomor_sertifikat`.
   - Kartu 4: Hapus toggle `Modul Sertifikat Digital Kelulusan` dan `Modul Survei Kepuasan & NPS Alumni`.
3. **Seeder & Database Sanitation**:
   - Di `database/seeders/PengaturanSeeder.php`: Hapus 3 kunci dari array seeding, dan tambahkan:
     ```php
     Pengaturan::deleteKunci('format_nomor_sertifikat');
     Pengaturan::deleteKunci('fitur_sertifikat_aktif');
     Pengaturan::deleteKunci('fitur_survei_aktif');
     ```
   - Jalankan scratch script untuk menghapus ketiga record tersebut dari tabel `pengaturan` di database runtime.

---

### 🔹 FASE 4: Penyesuaian Automated Test Suite
1. **Hapus Test**:
   - `tests/Feature/SurveiKepuasanAdminVisualTest.php`
2. **Perbarui Test Case Terkait**:
   - `tests/Feature/PredikatBatasValidationTest.php`:
     - Assert `$totalParameter` bernilai 13.
     - Hapus assertion untuk nomor sertifikat dan toggle survei/sertifikat.
   - `tests/Feature/SuperAdminFeatureToggleTest.php`:
     - Hapus assertion untuk `fitur_sertifikat_aktif` dan `fitur_survei_aktif`.
     - Pertahankan pengujian untuk `fitur_analitik_aktif`.
   - `tests/Feature/AdvancedThreePillarsDashboardTest.php`:
     - Hapus method `test_admin_sertifikat_index_and_cetak_alumni_terverifikasi` dan `test_admin_sertifikat_denies_unfinalized_evaluation`.
   - `tests/Feature/AdvancedAdminAndSuperAdminPhaseFiveTest.php`:
     - Hapus 3 method test survei kepuasan.
   - `tests/Feature/DeltaDashboardEnhancementTest.php`:
     - Hapus assertion keberadaan route sertifikat dan survei pada sidebar.

---

### 🔹 FASE 5: Quality Gate & Verifikasi Mandiri
Jalankan verifikasi lengkap menggunakan PHP 8.3 aktual:
```powershell
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan migrate
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/pint --test
npm run build
```

---

## 5. 🎯 DEFINITION OF DONE (KRITERIA PENERIMAAN)

- [x] Seluruh file controller, view, dan model terkait Sertifikat dan Survei Kepuasan telah dihapus bersih.
- [x] Rute `admin.sertifikat.*`, `admin.survei.*`, dan `peserta.survei.*` telah dihapus dari `routes/web.php`.
- [x] Navigasi sidebar, quick action dashboard, dan mobile menu telah dibersihkan dari kedua modul tersebut.
- [x] Tabel `survei_kepuasan` telah di-drop dari database melalui migrasi resmi.
- [x] Halaman Parameter Konfigurasi Sistem SuperAdmin menampilkan **13 Konfigurasi Operasional Aktif** (Tile Stat 13).
- [x] Form SuperAdmin tidak lagi memuat input nomor sertifikat, toggle modul sertifikat, maupun toggle modul survei.
- [x] Database runtime telah dibersihkan dari kunci `format_nomor_sertifikat`, `fitur_sertifikat_aktif`, dan `fitur_survei_aktif`.
- [x] Seluruh automated test suite (PHPUnit) lulus 100% tanpa ada test yang gagal atau skipped sembarangan (735 passed).
- [x] Lulus validasi Laravel Pint (`pint --test`) dan build Vite (`npm run build`).

---
*Dokumen ini dirancang sebagai panduan kerja mandiri (self-contained) untuk dieksekusi oleh developer atau agen AI.*

