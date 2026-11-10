# TASK BRIEF SENIOR DEVELOPER: STANDARISASI PARAMETER KONFIGURASI SISTEM, PENAMBAHAN PARAMETER BARU, DAN PENATAAN KARTU TAMPILAN SUPERADMIN

> **Dari:** Senior Developer / Lead Software Architect  
> **Kepada:** Junior Developer / AI Agent (Gemini 3.8 Flash / Antigravity Agent)  
> **Target Repositori:** MAGKOM (Portal Manajemen Magang Dinas Kominfo Sleman)  
> **Status:** APPROVED FOR IMPLEMENTATION (OPSI A: STANDARISASI LENGKAP & PARAMETER BARU)  
> **Tingkat Kesulitan:** Menengah (System Configuration, UI/UX Card Reorganization, Backend Service Decoupling, Audit Trail & Validation)  
> **Dokumentasi Terkait:** `AGENTS.md`, `docs/01-PRD-MagKom.md`, `docs/02-TDD-MagKom.md`, `docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`

---

## 1. 🔍 LATAR BELAKANG & TUJUAN TASK

### 1.1 Masalah Nyata di Antarmuka SuperAdmin
Pada halaman **SuperAdmin -> Parameter Konfigurasi Sistem** ([`resources/views/superadmin/pengaturan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/pengaturan/index.blade.php)):
1. **Tile Stat Menyesatkan (*Misleading Metric*)**:
   - Kartu statistik menampilkan: `TOTAL PARAMETER: 63 Sistem (Konfigurasi Aktif Database)`.
   - Namun di layar bawahnya hanya terlihat beberapa input konfigurasi.
   - **Penyebab**: Angka `63` diperoleh dari query `Pengaturan::count()`, yang secara keliru mencampuradukkan **51 data draft visual CMS/Site Editor** (seperti `draft_landing_hero_*`, `draft_landing_page_document`) dengan parameter operasional dinas.
2. **Keterbatasan Parameter yang Bisa Diatur Lewat Web**:
   - Beberapa aturan bisnis dinas yang krusial masih di-*hardcode* di dalam Controller dan Service (seperti SLA Verifikasi 5 hari di watchdog antrean, batas maksimal anggota kelompok 4 orang, format nomor surat/sertifikat kelulusan `500.12/SRT/`, dan sakelar pendaftaran tim).
   - Akibatnya, jika ada penyesuaian aturan kerja dinas, staf Diskominfo tidak dapat mengubahnya secara mandiri tanpa bantuan developer untuk mengubah kode.

### 1.2 Tujuan Solusi (Opsi A - Rekomendasi Lead Architect)
1. **Membersihkan Stat Counter**: Mengubah Tile Stat `TOTAL PARAMETER` agar secara akurat menghitung jumlah konfigurasi operasional sistem yang aktif dan dikelola di halaman ini (16 parameter sistem), bukan menghitung dump JSON Site Editor.
2. **Menambahkan Parameter Baru yang Dibutuhkan Dinas**:
   - Ambang batas SLA Verifikasi Antrean Pendaftaran (Hari Kerja)
   - Batas Maksimal Anggota Pendaftaran Kelompok (Orang)
   - Durasi Magang Maksimal yang Diizinkan (Bulan)
   - Format Prefix Nomor Sertifikat Kelulusan Resmi (`500.12/SRT/`)
   - Sakelar Fitur Pendaftaran Kelompok / Tim (Aktif / Nonaktif)
3. **Menata Ulang 4 Kartu Kebijakan yang Rapi & Responsif**:
   - Memastikan form mudah dipahami, tidak sesak, dilengkapi icon SVG tematik, label deskriptif, dan satuan yang jelas (`Orang`, `Hari`, `Bulan`, `MB`).
4. **Menghubungkan Nilai Parameter ke Logika Backend**:
   - Service metrik (`SuperAdminExecutiveMetricsService`) membaca SLA dari pengaturan.
   - Penerbitan sertifikat (`SertifikatController`) membaca template prefix nomor surat dari pengaturan.
   - Form pendaftaran peserta membaca batasan anggota kelompok dari pengaturan.

---

## 2. 📋 KAMUS DATA LENGKAP: 16 PARAMETER KONFIGURASI SISTEM

Berikut adalah spesifikasi teknis 16 parameter yang dikelompokkan ke dalam 4 domain kartu:

| No | Kunci (`kunci`) | Tipe Data | Nilai Bawaan | Satuan | Batasan Validasi | Keterangan & Aturan Bisnis |
|:---:|---|---|:---:|:---:|---|---|
| **KARTU 1: KEBIJAKAN KUOTA, PENDAFTARAN & KAPASITAS** |
| 1 | `kuota_umum_maks_per_bulan` | integer | `12` | Orang | `min:1, max:100` | Batas pagu kuota umum pendaftaran publik per bulan (PRD BR-01). |
| 2 | `slot_khusus_default` | integer | `3` | Orang | `min:0, max:50` | Nilai awal alokasi slot khusus kemitraan instansi/kampus (PRD BR-02). |
| 3 | `maks_peserta_aktif_per_pembimbing` | integer | `20` | Orang | `min:1, max:50` | Beban kapasitas bimbingan mentee aktif per pembimbing (PRD BR-13). |
| 4 | `maks_anggota_kelompok` | integer | `4` | Orang | `min:1, max:10` | Batas maksimum rombongan tim (1 ketua + rekan anggota) (PRD BR-03). `[BARU]` |
| 5 | `durasi_magang_maks_bulan` | integer | `6` | Bulan | `min:1, max:12` | Batas maksimum lama magang yang diizinkan sistem (PRD BR-05). `[BARU]` |
| **KARTU 2: ATURAN PELAKSANAAN, TUGAS & DOKUMEN** |
| 6 | `logbook_maks_mundur_hari` | integer | `3` | Hari | `min:1, max:30` | Toleransi batas mundur pengisian logbook oleh peserta (PRD BR-09). |
| 7 | `berkas_maks_mb` | integer | `10` | MB | `min:1, max:20` | Batas maksimal ukuran berkas pendaftaran yang diunggah (PRD Bab 8.4). |
| 8 | `deadline_mendekati_hari` | integer | `3` | Hari | `min:1, max:14` | Batas hari sebelum deadline untuk memicu badge kuning tugas (PRD BR-16). |
| 9 | `sla_verifikasi_hari` | integer | `5` | Hari Kerja | `min:1, max:30` | Target batas waktu verifikasi berkas sebelum ditandai breached SLA. `[BARU]` |
| **KARTU 3: KEBIJAKAN RETENSI AKUN, SERTIFIKAT & AUDIT TRAIL** |
| 10 | `masa_tenggang_bulan` | integer | `2` | Bulan | `min:1, max:12` | Durasi akun alumni tetap baca-saja pasca magang selesai (PRD BR-11). |
| 11 | `retensi_audit_akses_data_hari` | integer | `365` | Hari | `min:30, max:1825` | Batas masa simpan log akses data sebelum dipangkas otomatis (NFR-A5). |
| 12 | `format_nomor_sertifikat` | string | `500.12/SRT/` | Teks | `min:3, max:50` | Prefix kode klasifikasi arsip penomoran sertifikat resmi Diskominfo. `[BARU]` |
| **KARTU 4: SAKELAR FITUR SISTEM (FEATURE TOGGLES)** |
| 13 | `fitur_sertifikat_aktif` | boolean | `'1'` | Switch | `'0'` atau `'1'` | Master toggle modul penerbitan & pencetakan sertifikat kelulusan. |
| 14 | `fitur_survei_aktif` | boolean | `'1'` | Switch | `'0'` atau `'1'` | Master toggle modul survei kepuasan & evaluasi mutu alumni magang. |
| 15 | `fitur_analitik_aktif` | boolean | `'1'` | Switch | `'0'` atau `'1'` | Master toggle modul dashboard laporan analitik tahunan eksekutif. |
| 16 | `fitur_pendaftaran_kelompok_aktif` | boolean | `'1'` | Switch | `'0'` atau `'1'` | Master toggle mengaktifkan/menonaktifkan opsi pendaftaran tim. `[BARU]` |

---

## 3. 🗺️ ARSITEKTUR KARTU TAMPILAN BARU (UI BLUEPRINT)

Susunan halaman [`resources/views/superadmin/pengaturan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/pengaturan/index.blade.php):

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ [HERO BANNER] Parameter Konfigurasi Sistem                                             │
│ Tata Kelola: Aturan Bisnis & Ambang Batas • Status: Tersimpan di Database              │
│ Tombol: [Ekspor Snapshot JSON]  [Simpan Perubahan]                                     │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [4 TILE STAT COUNTERS]                                                                 │
│ 1. TOTAL PARAMETER: 16 Konfigurasi (Parameter Operasional Aktif)                       │
│ 2. KUOTA UMUM BULANAN: 12 Kuota (Batas Maksimal Kuota Umum / Bulan)                    │
│ 3. RASIO MENTEE / MENTOR: 20 : 1 (Maks Mentee per Pembimbing)                          │
│ 4. MASA TENGGANG: 2 Bulan (Durasi Akun Pasca Nonaktif)                                 │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [FORM PENGATURAN]                                                                      │
│                                                                                        │
│ ┌─ KARTU 1: Kebijakan Kuota, Pendaftaran & Kapasitas Magang ─────────────────────────┐ │
│ │ • Batas Kuota Umum Publik (12 Orang)                                                │ │
│ │ • Default Alokasi Slot Khusus (3 Orang)                                             │ │
│ │ • Beban Maksimal Mentee Per Pembimbing (20 Orang)                                   │ │
│ │ • Batas Maksimal Anggota Kelompok (4 Orang) [BARU]                                  │ │
│ │ • Durasi Magang Maksimal (6 Bulan) [BARU]                                           │ │
│ └─────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌─ KARTU 2: Aturan Pelaksanaan, Tugas & Dokumen ──────────────────────────────────────┐ │
│ │ • Batas Mundur Pengisian Logbook (3 Hari)                                           │ │
│ │ • Batas Ukuran Maksimal Berkas Upload (10 MB)                                       │ │
│ │ • Peringatan Mendekati Deadline Tugas (3 Hari)                                      │ │
│ │ • Target Batas Waktu SLA Verifikasi Berkas (5 Hari Kerja) [BARU]                    │ │
│ └─────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌─ KARTU 3: Kebijakan Retensi Akun, Sertifikat & Audit Trail ─────────────────────────┐ │
│ │ • Durasi Masa Tenggang Akun Pasca Magang (2 Bulan)                                  │ │
│ │ • Masa Retensi Log Akses Data Audit (365 Hari)                                      │ │
│ │ • Prefix Format Nomor Surat Sertifikat (500.12/SRT/) [BARU]                         │ │
│ │ ── Pemeliharaan: [Jalankan Pemangkasan Log Sekarang]                                │ │
│ └─────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ ┌─ KARTU 4: Sakelar Modul Fitur Sistem (Feature Toggles) ─────────────────────────────┐ │
│ │ [✓] Modul Sertifikat Digital Kelulusan                                              │ │
│ │ [✓] Modul Survei Kepuasan & NPS Alumni                                              │ │
│ │ [✓] Modul Analitik Tahunan Eksekutif                                                │ │
│ │ [✓] Modul Pendaftaran Kelompok / Tim [BARU]                                         │ │
│ └─────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                        │
│ [TOMBOL AKSI BAWAH: Simpan Perubahan Konfigurasi]                                      │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. 🛠️ PANDUAN EKSEKUSI TEKNIS LANGKAH DEMI LANGKAH (STEP-BY-STEP)

Junior Developer / AI Agent wajib mengikuti tahapan berurutan berikut:

---

### 🔹 FASE 1: Backend Controller & Validasi Form Pengaturan

1. **File Controller:** [`app/Http/Controllers/SuperAdmin/PengaturanController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/PengaturanController.php)
   - **Method `index()`:**
     - Ambil daftar kunci parameter operasional terdaftar (16 kunci):
       ```php
       $kunciOperasional = [
           'kuota_umum_maks_per_bulan',
           'slot_khusus_default',
           'maks_peserta_aktif_per_pembimbing',
           'maks_anggota_kelompok',
           'durasi_magang_maks_bulan',
           'logbook_maks_mundur_hari',
           'berkas_maks_mb',
           'deadline_mendekati_hari',
           'sla_verifikasi_hari',
           'masa_tenggang_bulan',
           'retensi_audit_akses_data_hari',
           'format_nomor_sertifikat',
           'fitur_sertifikat_aktif',
           'fitur_survei_aktif',
           'fitur_analitik_aktif',
           'fitur_pendaftaran_kelompok_aktif',
       ];
       $totalParameter = count($kunciOperasional);
       ```
     - Kirimkan `$totalParameter`, `$kuotaBulanan`, `$rasioMentee`, `$masaTenggang` ke view.
   - **Method `update(Request $request)`:**
     - Tambahkan aturan validasi:
       ```php
       'maks_anggota_kelompok' => ['required', 'integer', 'min:1', 'max:10'],
       'durasi_magang_maks_bulan' => ['required', 'integer', 'min:1', 'max:12'],
       'sla_verifikasi_hari' => ['required', 'integer', 'min:1', 'max:30'],
       'format_nomor_sertifikat' => ['required', 'string', 'min:3', 'max:50'],
       'fitur_pendaftaran_kelompok_aktif' => ['nullable', 'boolean'],
       ```
     - Masukkan ke dalam array `$settingsToSave`:
       ```php
       'maks_anggota_kelompok' => (string) $validated['maks_anggota_kelompok'],
       'durasi_magang_maks_bulan' => (string) $validated['durasi_magang_maks_bulan'],
       'sla_verifikasi_hari' => (string) $validated['sla_verifikasi_hari'],
       'format_nomor_sertifikat' => (string) $validated['format_nomor_sertifikat'],
       'fitur_pendaftaran_kelompok_aktif' => $request->boolean('fitur_pendaftaran_kelompok_aktif') ? '1' : '0',
       ```
     - Pastikan transaksi penyimpanan `DB::transaction()` menyimpan setiap setting, memanggil `Pengaturan::clearCache()`, dan mencatat Audit Log dengan detail `Memperbarui parameter konfigurasi sistem`.

---

### 🔹 FASE 2: Pembaruan Blade View SuperAdmin Pengaturan

1. **File View:** [`resources/views/superadmin/pengaturan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/pengaturan/index.blade.php)
   - **Tile Stat 1 (Total Parameter):**
     - Ubah `subText` menjadi `"Parameter Operasional Aktif"`.
     - Value: `{{ $totalParameter }}`.
   - **Kartu 1 (Kebijakan Kuota & Kapasitas):**
     - Tambahkan field:
       - `Batas Maksimal Anggota Kelompok (Orang) *` (input `number`, name `maks_anggota_kelompok`, min 1, max 10, default 4).
       - `Durasi Magang Maksimal yang Diizinkan (Bulan) *` (input `number`, name `durasi_magang_maks_bulan`, min 1, max 12, default 6).
   - **Kartu 2 (Aturan Pelaksanaan, Tugas & Dokumen):**
     - Tambahkan field:
       - `Target Batas Waktu SLA Verifikasi Berkas (Hari Kerja) *` (input `number`, name `sla_verifikasi_hari`, min 1, max 30, default 5).
   - **Kartu 3 (Kebijakan Retensi Akun, Sertifikat & Audit Trail):**
     - Tambahkan field:
       - `Prefix Format Nomor Surat Sertifikat *` (input `text`, name `format_nomor_sertifikat`, maxlength 50, default `500.12/SRT/`).
   - **Kartu 4 (Sakelar Modul Fitur Sistem):**
     - Tambahkan sakelar switch toggle ke-4:
       - `Modul Pendaftaran Kelompok / Tim` (checkbox `fitur_pendaftaran_kelompok_aktif`, value 1).

---

### 🔹 FASE 3: Integrasi Parameter ke Backend Services & Controller

1. **Ambang Batas SLA Verifikasi pada Dashboard Watchdog:**
   - File: [`app/Services/SuperAdminExecutiveMetricsService.php`](file:///g:/MAGKOM/app/Services/SuperAdminExecutiveMetricsService.php)
   - Pada baris 321:
     - Cari kode hardcode:
       ```php
       $antreanMelebihiSla = Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)
           ->where('diajukan_at', '<', now()->subDays(5))
           ->count();
       ```
     - Ganti nilai `5` hari dengan membaca pengaturan dinamis:
       ```php
       $slaHari = Pengaturan::int('sla_verifikasi_hari', 5);
       $antreanMelebihiSla = Pendaftaran::where('status', StatusPendaftaranEnum::Diajukan)
           ->where('diajukan_at', '<', now()->subDays($slaHari))
           ->count();
       ```

2. **Format Penomoran Surat Sertifikat Kelulusan:**
   - File: [`app/Http/Controllers/Admin/SertifikatController.php`](file:///g:/MAGKOM/app/Http/Controllers/Admin/SertifikatController.php)
   - Pada method pembuatan/preview sertifikat (di mana nomor dokumen digenerate):
     - Cari string format hardcode `'500.12/SRT/'`.
     - Gunakan `Pengaturan::get('format_nomor_sertifikat', '500.12/SRT/')` sebagai prefix nomor surat.

3. **Validasi Pendaftaran Kelompok & Durasi Magang:**
   - File Form Request: [`app/Http/Requests/Peserta/PendaftaranStoreRequest.php`](file:///g:/MAGKOM/app/Http/Requests/Peserta/PendaftaranStoreRequest.php) (atau request terkait pendaftaran):
     - Pastikan validasi durasi magang menggunakan `min:1, max:' . Pengaturan::int('durasi_magang_maks_bulan', 6)`.
     - Pastikan validasi anggota kelompok menggunakan `max:' . (Pengaturan::int('maks_anggota_kelompok', 4) - 1)` untuk anggota tambahan.
     - Jika `Pengaturan::bool('fitur_pendaftaran_kelompok_aktif', true)` bernilai `false`, tolak pengajuan dengan tipe `kelompok` dan kembalikan pesan informatif bahwa pendaftaran kelompok sedang dinonaktifkan dinas.

---

### 🔹 FASE 4: Default Seeder & Database Sanitation

1. **Pembersihan Dead Data `predikat_batas`:**
   - Di database lokal dan seeder, hapus key `predikat_batas` dari tabel `pengaturan` karena fitur penilaian sudah dihapus total.
2. **File Seeder:** [`database/seeders/DatabaseSeeder.php`](file:///g:/MAGKOM/database/seeders/DatabaseSeeder.php) (atau seeder pengaturan terkait):
   - Pastikan default 16 parameter operasional diinisialisasi jika tabel kosong.

---

### 🔹 FASE 5: Pembuatan Automated Test Suite (PHPUnit)

1. **File Test:** [`tests/Feature/PredikatBatasValidationTest.php`](file:///g:/MAGKOM/tests/Feature/PredikatBatasValidationTest.php) (atau test baru `SuperAdminOperationalSettingsTest.php`):
   - **Test 1**: Memverifikasi SuperAdmin dapat melihat halaman pengaturan dengan Tile Stat `16` parameter.
   - **Test 2**: Memverifikasi validasi boundaries untuk parameter baru (misal `maks_anggota_kelompok` min 1, `sla_verifikasi_hari` min 1).
   - **Test 3**: Memverifikasi penyimpanan nilai baru berhasil dan tersimpan di database serta terekam di tabel `audit_log`.
   - **Test 4**: Memverifikasi perubahan `sla_verifikasi_hari` mempengaruhi perhitungan watchdog antrean pada `SuperAdminExecutiveMetricsService`.
   - **Test 5**: Memverifikasi non-superadmin (admin/pembimbing/peserta/guest) ditolak mengakses atau mengubah pengaturan (403/302).

---

### 🔹 FASE 6: Quality Gate & Verifikasi Standar

Jalankan seluruh perintah verifikasi mandiri menggunakan PHP 8.3 aktual:
```powershell
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/pint --test
npm run build
```

---

## 5. 🎯 DEFINITION OF DONE (KRITERIA PENERIMAAN)

- [x] Tile Stat `TOTAL PARAMETER` pada antarmuka menampilkan angka riil parameter operasional sistem (`16 Konfigurasi`), bukan 63.
- [x] 5 parameter baru (`maks_anggota_kelompok`, `durasi_magang_maks_bulan`, `sla_verifikasi_hari`, `format_nomor_sertifikat`, `fitur_pendaftaran_kelompok_aktif`) hadir di form antarmuka SuperAdmin Pengaturan.
- [x] Setiap input memiliki label jelas, icon deskriptif, petunjuk satuan (Orang, Hari, Bulan, Format Teks), dan validasi sisi backend yang ketat.
- [x] Logika backend (`SuperAdminExecutiveMetricsService`, `SertifikatController`, pendaftaran kelompok) terintegrasi dinamis dengan nilai parameter baru.
- [x] Dead-key `predikat_batas` dihapus bersih dari tabel `pengaturan`.
- [x] Seluruh perubahan parameter terekam di jejak `audit_log` sistem.
- [x] Seluruh automated test suite lulus 100% menggunakan PHP 8.3 aktual (744 passed, 5127 assertions).
- [x] Lulus validasi styling Laravel Pint (`pint --test`) dan build Vite (`npm run build`).

---
*Dokumen ini dirancang sebagai panduan implementasi presisi, mandiri, dan bebas ambigu untuk dieksekusi langsung.*

