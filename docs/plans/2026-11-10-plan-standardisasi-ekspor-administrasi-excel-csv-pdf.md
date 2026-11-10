# Rencana Kerja: Standardisasi & Verifikasi Fitur Ekspor Administrasi (Excel, CSV, PDF)

**Tanggal:** 10 November 2026  
**Status:** Selesai (Completed & Verified)  
**Tautan Dokumen Rencana:** `docs/plans/2026-11-10-plan-standardisasi-ekspor-administrasi-excel-csv-pdf.md`  
**Referensi:**  
- Permintaan Pengguna: Memastikan seluruh tombol ekspor di bagian Administrasi dapat mengekspor dalam format Excel (.xlsx), CSV (.csv), dan PDF (.pdf).  
- Modul Target:
  1. Halaman Penempatan Peserta (`/admin/penempatan`)
  2. Halaman Hub Laporan & Ekspor (`/admin/laporan` — Tab Pendaftaran & Tab Penempatan)
  3. Halaman Verifikasi Berkas Pendaftaran (`/admin/pendaftaran`)
- Aturan Pengembangan: `AGENTS.md` (PHP 8.3, Laravel 10, Vanilla CSS di `resources/css/`, Zero Secret, Audit Trail Logging, Zero Test Regression).

---

## 1. Analisis Kebutuhan & Hasil Audit Eksisting

### 1.1 Kondisi Saat Ini (As-Is):
1. **Halaman Penempatan (`/admin/penempatan`)**:
   - Memiliki tombol `Ekspor Rekap ∨` di Hero Banner.
   - Popover menyediakan pilihan 3 format (Excel, CSV, PDF) yang memanggil `route('admin.penempatan.ekspor-surat-diterima')`.
   - **Evaluasi UX**: Popover saat ini belum memiliki class modifier `.adm-export-popover-right`, sehingga pada layar tertentu dropdown popover dapat meluber (*overflow*) ke sisi kanan layar.
2. **Halaman Hub Laporan & Ekspor (`/admin/laporan`)**:
   - **Tab Pendaftaran**: Memiliki tombol `Ekspor Rekap ∨` dengan pilihan 3 format (Excel, CSV, PDF) yang memanggil `route('admin.laporan.pendaftaran')`.
   - **Tab Penempatan**: Memiliki tombol `Ekspor Rekap ∨` dengan pilihan 3 format (Excel, CSV, PDF) yang memanggil `route('admin.laporan.penempatan')`.
   - Seluruh backend service (`ExportReportService`, `XlsxExportService`, `Pdf`) sudah siap dan teruji.
3. **Halaman Verifikasi Berkas (`/admin/pendaftaran`)**:
   - **Belum Memiliki Tombol Ekspor**: Hero banner di halaman pendaftaran saat ini hanya berisi judul teks, sementara admin sering membutuhkan unduhan rekap antrean pendaftaran langsung dari halaman verifikasi tanpa harus membuka halaman laporan.
4. **Parameter Pencarian (`q`) pada Ekspor Pendaftaran**:
   - `ExportReportService::exportPendaftaran` saat ini menangani filter `bulan_magang_id`, `status`, `jurusan_id`, dan rentang tanggal, namun belum mendukung parameter pencarian teks (`q` untuk nama/email/NIM/institusi).

---

## 2. Solusi & Rencana Perubahan Arsitektur

### A. Penyempurnaan Backend (`ExportReportService.php` & `LaporanController.php`)
- Menambahkan penanganan filter pencarian keyword (`q`) pada `ExportReportService::exportPendaftaran` agar hasil ekspor Excel, CSV, dan PDF presisi mengikuti kata kunci pencarian yang sedang aktif di tabel admin pendaftaran.
- Memastikan `AdminPenempatanController::exportRekapSuratDiterima` dan `LaporanController::penempatan` konsisten menyertakan data jurusan, bidang, dan nomor surat resmi.

### B. Standardisasi Frontend Popover Ekspor
1. **Halaman Penempatan (`resources/views/admin/penempatan/index.blade.php`)**:
   - Tambahkan class `.adm-export-popover-right` dan `.adm-pointer-right` pada `#admExportPopover` agar dropdown menempel rapi ke sisi kanan tombol trigger dan tidak terpotong tepi layar.
2. **Halaman Verifikasi Berkas (`resources/views/admin/pendaftaran/index.blade.php`)**:
   - Tambahkan komponen **Unified Export Popover** (`Ekspor Rekap ∨`) di `.adm-hero-actions` pada Hero Banner.
   - Menyediakan pilihan format:
     - **Microsoft Excel (`.xlsx`)**: Rekomendasi, tabel bergaris resmi kedinasan.
     - **CSV Tabular (`.csv`)**: Data teks dengan UTF-8 BOM dan sanitasi formula injection.
     - **Dokumen Resmi (`.pdf`)**: Ber-kop resmi Dinas Kominfo Sleman (A4 Landscape) siap cetak.
   - Menghubungkan trigger unduh ke `route('admin.laporan.pendaftaran')` dengan membawa query filter aktif (`status`, `tipe`, `bulan_magang_id`, `jurusan_id`, `q`).
3. **Halaman Laporan (`resources/views/admin/laporan/index.blade.php`)**:
   - Verifikasi dan pastikan kedua trigger ekspor (Tab Pendaftaran dan Tab Penempatan) berjalan lancar untuk seluruh format.

---

## 3. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Services/ExportReportService.php` | Modify | Menambahkan penanganan filter pencarian keyword `q` pada `exportPendaftaran()`. |
| `resources/views/admin/penempatan/index.blade.php` | Modify | Menambahkan class `.adm-export-popover-right` dan `.adm-pointer-right` pada popover ekspor penempatan. |
| `resources/views/admin/pendaftaran/index.blade.php` | Modify | Memasang tombol `Ekspor Rekap ∨` beserta Unified Export Popover (Excel, CSV, PDF) di Hero Banner dan menambahkan handler JavaScript export. |
| `tests/Feature/AdminUnifiedExportCoverageTest.php` | Create | Test suite PHPUnit komprehensif yang menguji 100% kelulusan ekspor 3 format (XLSX, CSV, PDF) pada semua tombol ekspor Administrasi. |

---

## 4. Langkah Implementasi (Checklist)

- [x] **Langkah 1**: Edit `app/Services/ExportReportService.php`:
  - Tambahkan filter keyword `q` dan `tipe` pada `exportPendaftaran()`.
  - Tambahkan filter `q`, `pembimbing_id`, `bidang_id`, dan `bulan_magang_id` pada `exportPenempatan()`.
- [x] **Langkah 2**: Edit `resources/views/admin/penempatan/index.blade.php`:
  - Update `#admExportPopover` dengan class `.adm-export-popover-right` dan `.adm-pointer-right`.
- [x] **Langkah 3**: Edit `resources/views/admin/pendaftaran/index.blade.php`:
  - Sisipkan `.adm-hero-actions` di dalam `.adm-hero-content` Hero Banner dengan tombol `Ekspor Rekap ∨` dan dropdown popover 3 format (Excel, CSV, PDF).
  - Tambahkan fungsi kontrol JavaScript: `toggleAdmExportPopoverPendaftaran()`, `handleAdmExportPendaftaranFormatChange()`, dan `triggerPendaftaranExport()`.
- [x] **Langkah 4**: Kompilasi Asset:
  - Jalankan `npm run build`.
- [x] **Langkah 5**: Buat & Jalankan Test Otomatis PHPUnit:
  - Buat file tes `tests/Feature/AdminUnifiedExportCoverageTest.php`.
  - Jalankan pengujian: 27/27 tes lolos 100%.

---

## 5. Strategi Pengujian (Testing Strategy)

1. **Pengujian Endpoint & Format Response**:
   - **XLSX**: Memverifikasi response status 200, Content-Type `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`, dan filename berakhiran `.xlsx`.
   - **CSV**: Memverifikasi response status 200, Content-Type `text/csv`, awalan `\xEF\xBB\xBFsep=,\r\n` (UTF-8 BOM), dan sanitasi formula injection.
   - **PDF**: Memverifikasi response status 200, Content-Type `application/pdf`, dan filename berakhiran `.pdf`.
2. **Pengujian Integrasi Filter Aktif**:
   - Memastikan saat pengguna memfilter status/jurusan/bulan/kata kunci, data yang diekspor terfilter dengan benar di ketiga format.
3. **Audit Trail Logging**:
   - Memastikan setiap ekspor data (XLSX, CSV, PDF) dari seluruh tombol secara otomatis mencatat jejak audit pada tabel `audit_log` sesuai standar keamanan SEC-03 & AC-13-4.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Seluruh tombol ekspor di bagian Administrasi (Penempatan, Verifikasi Pendaftaran, dan Hub Laporan) mendukung format:
  - Excel (`.xlsx`)
  - CSV (`.csv`)
  - PDF (`.pdf`)
- [x] Popover ekspor tampil rapi tanpa *horizontal clipping* (*right-aligned*).
- [x] `npm run build` sukses.
- [x] Seluruh unit test ekspor dan feature test PHPUnit lulus 100%.

