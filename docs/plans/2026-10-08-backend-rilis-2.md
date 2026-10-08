# Rencana Implementasi Rilis 2 (R2 — Should Have Features)

Dokumen rencana kerja ini memandu penambahan fitur **Rilis 2** pada portal **MagKom (Magang Kominfo)** sesuai spesifikasi [PRD v1.1](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) Bab 4, Bab 7, dan Bab 8.

---

## 🎯 Sasaran Fitur Rilis 2

| Kode Fitur | Nama Fitur | Dokumen Acuan & Acceptance Criteria |
|---|---|---|
| **F-14** | **Captcha Proteksi Bot** | PRD AC-14-1 & AC-14-2, TDD Bab 2: Cloudflare Turnstile pada registrasi & login, fail-closed pada registrasi, fail-open pada login. |
| **F-15** | **Lampiran Logbook Harian** | PRD AC-09-6 & Bab 8.4: Unggah berkas PDF lampiran (maks 2MB) ke private disk & tautan hasil kerja valid skema `https://` (`rel="noopener noreferrer"`). |
| **F-16** | **Surat Keterangan Diterima PDF** | PRD AC-16-1 & Bab 8.5: Generator instan dokumen resmi PDF Surat Keterangan Diterima berkop Kominfo untuk peserta yang telah berstatus `Diterima`. |
| **F-17** | **Laporan & Ekspor Data (CSV/XLSX)** | PRD AC-17-1 & AC-17-2: Rekap pendaftaran, penempatan, kuota, serta rekapitulasi nilai (khusus SuperAdmin), sanitasi CSV Formula Injection (`=`, `+`, `-`, `@`), dan pencatatan audit log `AC-13-4`. |

---

## 📋 Tahapan Eksekusi Bertahap

### Tahap 1: F-15 (Lampiran Logbook Harian)
1. **Validasi Request & Service**:
   - Perbarui `app/Http/Requests/LogbookRequest.php`:
     - `file_laporan`: nullable, file, mimes:pdf, max:2048 (2MB).
     - `link_laporan`: nullable, string, max:500, regex skema `^https://`.
   - Perbarui `app/Http/Controllers/Peserta/LogbookController.php` untuk menangani penyimpanan file PDF ke disk privat (`private/documents/logbooks/`).
   - Sediakan rute dan controller unduh lampiran logbook aman dengan otorisasi Policy (`LogbookPolicy@view`).
2. **Unit & Feature Test**:
   - Uji penolakan link dengan skema bukan https (misal `http://` atau `javascript:`).
   - Uji penolakan file bukan PDF atau ukuran > 2MB.
   - Uji penyimpanan ke disk privat dan unduhan aman.

---

### Tahap 2: F-16 (Surat Keterangan Diterima PDF)
1. **Template Blade PDF**:
   - Buat `resources/views/pdf/surat-keterangan-diterima.blade.php` berkop resmi Dinas Kominfo, nomor surat, identitas lengkap peserta, periode, dan unit penempatan.
2. **Service & Controller**:
   - Buat `app/Services/SuratDiterimaPdfService.php` berbasis DomPDF.
   - Buat rute dan aksi di `app/Http/Controllers/Peserta/PenempatanController.php` dan `app/Http/Controllers/Admin/PenempatanController.php` untuk unduh PDF surat penerimaan.
   - Tambahkan Policy otorisasi di `app/Policies/PenempatanPolicy.php`.
3. **Unit & Feature Test**:
   - Uji peserta hanya bisa unduh surat miliknya sendiri jika status penempatan aktif/selesai.
   - Uji Admin & SuperAdmin dapat mengunduh surat peserta manapun.
   - Uji konten PDF memuat data penting (NIM/NISN, nama, institusi, jurusan, periode).

---

### Tahap 3: F-17 (Laporan & Ekspor CSV/XLSX dengan Anti Formula Injection)
1. **Service Ekspor & Sanitasi Formula**:
   - Buat `app/Services/ExportReportService.php`:
     - Method sanitasi cell `sanitizeCsvFormula(string $value): string` (prefix petik tunggal jika diawali `=`, `+`, `-`, atau `@`).
     - Generator CSV streaming untuk:
       1) Rekap Pendaftaran (filter bulan, status, jurusan).
       2) Rekap Penempatan (filter status, periode).
       3) Realisasi Kuota Umum vs Slot Khusus.
       4) Rekap Nilai Akhir (terotentikasi & diotorisasi khusus SuperAdmin).
     - Pencatatan otomatis ke `AuditService` untuk setiap aksi ekspor data (`AC-13-4`).
2. **Controller & Rute**:
   - Buat `app/Http/Controllers/Admin/LaporanController.php` untuk rekapitulasi data dan unduh CSV.
   - Proteksi akses: tolak Admin non-SuperAdmin saat meminta ekspor nilai (`AC-17-1`).
3. **Unit & Feature Test**:
   - Uji filter laporan.
   - Uji sanitasi formula injection (karakter berbahaya diawali `'`).
   - Uji SuperAdmin vs Admin RBAC pada ekspor nilai.
   - Uji audit log mencatat satu baris per ekspor.

---

### Tahap 4: F-14 (Captcha Cloudflare Turnstile)
1. **Service & Custom Validation Rule**:
   - Buat `app/Services/TurnstileService.php` yang berinteraksi dengan API Cloudflare Turnstile.
   - Buat rule validasi `app/Rules/TurnstileRule.php`.
   - Implementasi perilaku *Fail Mode*:
     - Registrasi: *fail-closed* jika server Cloudflare tidak dapat dihubungi.
     - Login: *fail-open* jika server Cloudflare down, tetap mengandalkan rate limiter NFR-S2.
2. **Integrasi Form & Test**:
   - Pasang rule di form registrasi dan login.
   - Buat pengujian otomatis dengan mock HTTP client untuk skenario valid, token invalid, dan service error timeout.

---

## 🧪 Strategi Verifikasi
Seluruh 4 fitur Rilis 2 akan diuji dengan automated feature test suite terdedikasi `tests/Feature/ReleaseTwoFeaturesTest.php` serta full regression test untuk memastikan seluruh 34 tests Rilis 1 tetap lulus 100%.
