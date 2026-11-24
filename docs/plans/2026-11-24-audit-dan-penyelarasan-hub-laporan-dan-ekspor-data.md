# Plan: Audit, Analisis Keterhubungan, dan Pengujian Fitur Hub Laporan & Ekspor Data (Admin)

**Dokumen:** `docs/plans/2026-11-24-audit-dan-penyelarasan-hub-laporan-dan-ekspor-data.md`  
**Fitur Utama:** Hub Laporan & Ekspor Data (`/admin/laporan`, `/admin/laporan/pendaftaran`, `/admin/laporan/penempatan`)  
**Penanggung Jawab:** AI Assistant (Antigravity) & Lead Engineer  
**Tanggal:** 24 November 2026  
**Status:** In Progress  

---

## 1. Latar Belakang & Tujuan
Fitur **Hub Laporan & Ekspor Data** (`/admin/laporan`) merupakan pusat intelijen eksekutif dan rekapitulasi data operasional portal MagKom Dinas Komunikasi dan Informatika Kabupaten Sleman. Fitur ini melayani admin dan superadmin untuk:
1. Memantau KPI operasional (Total Pengajuan, Peserta Diterima, Penempatan Aktif, dan Magang Selesai).
2. Memvisualisasikan analitik keterisian kuota (*Live Capacity*) per jurusan serta demografi jenjang pendidikan calon peserta (SMK, D3, S1).
3. Melakukan penelusuran data tabular rekapitulasi terfilter melalui dua tab utama:
   - **Rekap Pendaftaran**: Seluruh pengajuan berkas pendaftaran calon peserta magang.
   - **Data Penempatan**: Seluruh penetapan peserta magang ke bidang dinas dan pembimbing lapangan.
4. Mengekspor rekap data terfilter dalam 3 format berstandar:
   - **Microsoft Excel (.xlsx)**: Tabel resmi bergaris rapi dengan styling header kedinasan Sleman.
   - **CSV Tabular (.csv)**: Data mentah berformat UTF-8 BOM dengan direktif `sep=,` dan proteksi formula injection (AC-17-2).
   - **Dokumen Resmi (.pdf)**: Berkas cetak ber-kop resmi Pemerintah Kabupaten Sleman (DomPDF landscape A4).

Audit ini bertujuan memastikan alur pengguna (*user journey*), ketepatan logika sistem (*business logic*), kebersihan bug, dan integritas audit trail berjalan 100% sempurna tanpa error.

---

## 2. Analisis Keterhubungan Lintas Modul (Cross-Module Traceability)

| Modul Terkait | Titik Hubung (Touchpoint) | Status & Logika Sistem |
| :--- | :--- | :--- |
| **Pendaftaran (`/admin/pendaftaran`)** | Agregasi KPI `total_pendaftaran` dan `total_diterima`, visualisasi demografi jenjang calon peserta, serta tabel tab `pendaftaran`. | **Valid & Sinkron**. Mencerminkan kondisi pendaftaran terkini. |
| **Manajemen Kuota (`/admin/kuota`)** | Widget *Live Capacity*: `SUM(jumlah)` dan `SUM(terisi)` per jurusan dari tabel `kuota`. | **Valid & Sinkron**. Persentase keterisian dan status (Penuh / Padat / Tersedia) terhitung real-time. |
| **Penempatan (`/admin/penempatan`)** | Agregasi KPI `penempatan_aktif` dan `penempatan_selesai`, serta tabel tab `penempatan`. | **Valid & Sinkron**. Data penempatan jalur umum dan jalur khusus/mitra terhubung lengkap. |
| **Audit Trail (`audit_log`)** | Setiap unduhan rekap pendaftaran dan penempatan (XLSX, CSV, PDF) otomatis dicatat sebagai event `AksesData`. | **Valid & Sinkron**. Detail format, filter, dan jumlah baris tercatat akurat. |
| **Keamanan Spreadsheet (AC-17-2)** | Seluruh sel pada ekspor XLSX dan CSV melalui fungsi `sanitizeCell()` untuk mencegah formula injection (`=`, `+`, `-`, `@`). | **Valid & Aman**. |

---

## 3. Identifikasi Masalah & Perbaikan Terarah (Targeted Improvements)

### 3.1 Masalah 1: Status `PerluPerbaikan` Belum Tersedia di Opsi Filter Web
- **Lokasi:** `app/Http/Controllers/Admin/LaporanController.php`.
- **Temuan:** Array `$statusPendaftaran` di controller hanya mencakup `Diajukan`, `Diterima`, `Ditolak`, `Dibatalkan`. Status `PerluPerbaikan` (`perlu_perbaikan`) yang sah dari modul verifikasi berkas tidak dapat dipilih oleh admin pada filter laporan.
- **Solusi:** Tambahkan `StatusPendaftaranEnum::PerluPerbaikan` ke dalam array opsi status.

### 3.2 Masalah 2: Potensi N+1 Query pada Relasi Jurusan Profil
- **Lokasi:** `app/Http/Controllers/Admin/LaporanController.php`.
- **Temuan:** `$pQuery` pada tab pendaftaran hanya meng-eager load `user.profil`, dan `$penQuery` pada tab penempatan hanya meng-eager load `peserta.profil`. Saat baris tabel atau fallback memerlukan nama jurusan pendaftar (`user.profil.jurusan`), terjadi query tambahan ke tabel `jurusan`.
- **Solusi:** Eager load `'user.profil.jurusan'` dan `'peserta.profil.jurusan'`.

### 3.3 Masalah 3: Inkonsistensi Pencarian NIM/NISN dan ID Registrasi pada Ekspor Penempatan
- **Lokasi:** `app/Services/ExportReportService.php`.
- **Temuan:** Pada `exportPenempatan()`, filter `q` hanya memeriksa `nama`, `email`, dan `institusi`. Kolom `profil.nim_nisn`, nama pembimbing, dan pencarian nomor ID (#REG-XXXXX) belum terakomodasi.
- **Solusi:** Selaraskan filter `q` pada `exportPenempatan()` agar mendukung NIM/NISN, nomor ID registrasi, dan nama pembimbing lapangan.

---

## 4. Rencana Pengujian (Testing Strategy)
1. **Regression Testing:** Menjalankan 4 test suite laporan yang sudah ada:
   - `AdminEksporLaporanExcelTest`
   - `AdminEksporLaporanPendaftaranPopoverTest`
   - `AdminEksporLaporanXlsxTest`
   - `AdminLaporanDanArsipTest`
2. **New Test Suite:** Membuat `tests/Feature/AdminLaporanKonektivitasEndToEndTest.php`:
   - Verifikasi render metrik KPI dan visualisasi analitik kuota & demografi jenjang.
   - Verifikasi tab switching (`pendaftaran` vs `penempatan`) beserta paginasi dan filter tanggal/status.
   - Verifikasi pilihan filter status `Perlu Perbaikan`.
   - Verifikasi unduhan 3 format (XLSX, CSV, PDF) untuk rekap pendaftaran dan rekap penempatan.
   - Verifikasi sanitasi formula injection pada sel spreadsheet.
   - Verifikasi pencatatan audit log saat ekspor dilakukan.
3. **Quality Gates:**
   - PHPStan (Level max/configured memory limit).
   - Laravel Pint (Code formatting).
   - Vite asset build (`npm run build`).

---

## 5. Kepatuhan Aturan Pengembangan (AGENTS.md)
- **PHP 8.3 aktual** digunakan untuk semua eksekusi backend.
- **Blade Read-Only**: Tidak mengubah file Blade tanpa kebutuhan markup yang nyata.
- **Tanpa Inline Style / Script**: Mempertahankan arsitektur CSS di `resources/css` dan JS di `resources/js`.
- **Lindungi Perubahan Lokal Pengguna**: File uncommitted milik user tetap terjaga utuh.

