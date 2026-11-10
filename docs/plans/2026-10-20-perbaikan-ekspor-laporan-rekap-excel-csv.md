# Rencana Kerja: Perbaikan Struktur dan Format Tabel Ekspor CSV Rekapitulasi untuk Microsoft Excel

## 1. Tujuan
Menyelesaikan masalah pada fitur **Ekspor CSV Sanitasi** di mana seluruh data rekapitulasi menumpuk di Kolom A saat dibuka di Microsoft Excel (seperti pada tangkapan layar pengguna), serta merapikan struktur kolom, melengkapi data kosong pada jalur khusus (nama jurusan), dan menormalkan format tanggal serta status agar laporan rapi, terstruktur, dan siap dipresentasikan atau diolah.

---

## 2. Asumsi & Analisis Akar Masalah (Root Cause Analysis)

### A. Akar Masalah Teknis
1. **Perbedaan Pemisah Daftar Windows (Regional Settings `sList`)**:
   - Berdasarkan pemeriksaan langsung pada registri sistem pengguna (`HKCU:\Control Panel\International`), pengaturan regional lokal adalah `en-ID` dengan:
     - `sList = ;` (List Separator default adalah titik koma)
     - `sDecimal = ,` (Decimal Separator default adalah koma)
   - Microsoft Excel pada Windows Indonesia secara otomatis mengabaikan tanda koma (`,`) sebagai pemisah kolom file `.csv` dan menempatkan seluruh baris ke dalam sel Kolom A (`A1`, `A2`, dst.).
2. **Absensi Direktif `sep=,` pada Stream CSV**:
   - Microsoft Excel memiliki standar direktif `sep=,\r\n` (diletakkan tepat setelah UTF-8 BOM `\xEF\xBB\xBF`). Ketika direktif ini hadir, Excel akan memaksa parsing kolom menggunakan koma tanpa menghiraukan pengaturan regional Windows, dan Excel secara cerdas menyembunyikan baris `sep=,` sehingga baris 1 di lembar kerja langsung terisi oleh judul kolom (Header).
3. **Data Jurusan Kosong pada Peserta Jalur Khusus (`,,`)**:
   - Pada baris peserta nomor 3 (*Jarwo*, jalur khusus), kolom Jurusan kosong melompong (`,,`).
   - Penyebabnya: `ExportReportService.php` mengambil jurusan dari `$pen->kuota?->jurusan?->nama_jurusan`. Karena peserta jalur khusus tidak memiliki `kuota_id` (`kuota_id = null`), nilainya menjadi null. Belum ada fallback ke `$pen->peserta?->profil?->jurusan?->nama_jurusan`.
4. **Format Nilai Mentah (Raw Format)**:
   - Status penempatan masih menampilkan string kode teknis mentah (`aktif` bukan `Aktif`, `umum` bukan `Jalur Umum`).
   - Nilai null belum memiliki penanganan fallback yang seragam (misal tanda `-`).

---

## 3. Pendekatan Teknis & Evaluasi Alternatif

| Opsi Pendekatan | Kelebihan | Kekurangan | Rekomendasi |
| :--- | :--- | :--- | :--- |
| **Opsi 1: Peningkatan Mesin CSV Stream dengan Direktif `sep=,` & UTF-8 BOM** | - 100% kompatibel dengan AC-17-1 & AC-17-2 (sanitasi formula injection tetap aktif).<br>- Excel langsung membagi data ke kolom A, B, C, D, dst. secara otomatis di Windows Indonesia.<br>- Sangat hemat memori (streamed response) dan nol dependensi eksternal. | - Tidak menyertakan format styling visual Excel (warna latar header sel, border tebal). | **Sangat Direkomendasikan (Solusi Utama)** |
| **Opsi 2: Konfigurasi Delimiter Titik Koma (`;`) Default dengan Fallback Koma** | - Langsung sesuai dengan `sList` bawaan Windows Indonesia tanpa perlu direktif khusus. | - Berpotensi menumpuk jika dibuka di komputer bersetting US English (`sList = ,`) kecuali disertai `sep=;`. | Sebagai pelengkap / opsi parameter URL |
| **Opsi 3: Menambah Pustaka XLSX (PhpSpreadsheet / OpenSpout)** | - Menghasilkan file `.xlsx` berekstensi asli dengan warna border, header biru, dan auto-width kolom. | - Memerlukan instalasi paket Composer eksternal tambahan dan konsumsi RAM lebih besar saat volume data ribuan. | Saran Lanjutan (Fase Berikutnya) |

**Pilihan Arsitektur Terpilih**: **Opsi 1 (dengan dukungan direktif `sep=,`, UTF-8 BOM, fallback relasi jurusan, dan formatting label)**, karena langsung menyelesaikan masalah tampilan di Excel secara tuntas, aman dari formula injection (AC-17-2), lolos seluruh test audit log (AC-13-4), dan bekerja tanpa instalasi dependensi pihak ketiga.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`app/Services/ExportReportService.php`](file:///d:/laragon/www/MAGKOM/app/Services/ExportReportService.php) | Modify | 1. Tambahkan output direktif `sep=,\r\n` setelah UTF-8 BOM pada semua metode ekspor (`exportPendaftaran`, `exportPenempatan`, `exportRealisasiKuota`, `exportNilai`, `downloadCsvStream`).<br>2. Perbaiki query eager-loading `peserta.profil.jurusan`.<br>3. Tambahkan fallback jurusan peserta jalur khusus (`$pen->kuota?->jurusan?->nama_jurusan ?? ($pen->peserta?->profil?->jurusan?->nama_jurusan ?? '-')`).<br>4. Rapikan format tanggal (`d/m/Y`) dan label status/jalur. |
| [`app/Http/Controllers/Admin/PenempatanController.php`](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/Admin/PenempatanController.php) | Modify | Sinkronkan metode `exportRekapSuratDiterima` agar menyertakan direktif `sep=,\r\n` setelah UTF-8 BOM dan sanitasi kolom. |
| [`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php) | Modify | Perjelas tooltip/petunjuk pada tombol "Ekspor CSV Sanitasi" bahwa berkas telah dioptimalkan untuk Microsoft Excel & Spreadsheet. |
| [`tests/Feature/AdminEksporLaporanExcelTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminEksporLaporanExcelTest.php) | Create | Test suite baru untuk memverifikasi struktur berkas CSV (UTF-8 BOM, direktif `sep=,`, pemisahan kolom, keterisian nama jurusan jalur khusus, dan sanitasi formula). |

---

## 5. Langkah Implementasi

### Tahap 1: Backend Service (`app/Services/ExportReportService.php`)
- [x] Modifikasi baris output stream pada:
  - `exportPendaftaran`: Tulis `fwrite($handle, "\xEF\xBB\xBFsep=,\r\n");`
  - `exportPenempatan`: Tulis `fwrite($handle, "\xEF\xBB\xBFsep=,\r\n");`
  - `exportRealisasiKuota`: Tulis `fwrite($handle, "\xEF\xBB\xBFsep=,\r\n");`
  - `exportNilai`: Tulis `fwrite($handle, "\xEF\xBB\xBFsep=,\r\n");`
  - `downloadCsvStream`: Tulis `fwrite($handle, "\xEF\xBB\xBFsep=,\r\n");`
- [x] Perbarui eager-loading pada `exportPenempatan`:
  - Ubah `Penempatan::with(['peserta.profil', ...])` menjadi `Penempatan::with(['peserta.profil.jurusan', 'pembimbing', 'bidang', 'kuota.jurusan'])`.
- [x] Perbaiki pemetaan nilai kolom `Jurusan`:
  - Gunakan fallback `$this->sanitizeCell($pen->kuota?->jurusan?->nama_jurusan ?? ($pen->peserta?->profil?->jurusan?->nama_jurusan ?? '-'))`.
- [x] Perbaiki pemetaan nilai `Jalur` dan `Status`:
  - Gunakan format rapi berhuruf kapital: `$this->sanitizeCell(ucwords((string) ($pen->jalur?->value ?? $pen->jalur ?? '-')))` dan status penempatan dengan label ramah pengguna.
- [x] Perbaiki format tanggal `Tanggal Mulai` dan `Tanggal Selesai`:
  - Tampilkan dengan format standar tanggal Indonesia atau ISO (`d/m/Y` atau `Y-m-d`).

### Tahap 2: Sinkronisasi Controller Pendukung
- [x] Perbarui `app/Http/Controllers/Admin/PenempatanController.php` pada fungsi `exportRekapSuratDiterima()`:
  - Tulis `fwrite($handle, "\xEF\xBB\xBFsep=,\r\n");` sebelum `fputcsv`.

### Tahap 3: Pembuatan Pengujian Otomatis (Testing)
- [x] Buat file test baru `tests/Feature/AdminEksporLaporanExcelTest.php`:
  - Uji ekspor pendaftaran menghasilkan UTF-8 BOM dan `sep=,`.
  - Uji ekspor penempatan memastikan peserta jalur khusus menampilkan nama jurusan (tidak kosong `,,`).
  - Uji seluruh kolom terpisah dengan delimiter koma yang valid tanpa merusak sanitasi formula injection (`AC-17-2`).
  - Uji kompatibilitas COM Excel (memastikan Range A1, B1, C1 terpisah di Excel).

### Tahap 4: Verifikasi & Formatting Kode
- [x] Jalankan `php artisan test --filter="AdminEksporLaporanExcelTest|AdminSuperAdminPhaseFourTest|ReleaseTwoFeaturesTest"`.
- [x] Jalankan linter `vendor/bin/pint` untuk menjaga standar PSR-12.

---

## 6. Strategi Testing

1. **Unit / Feature Tests**:
   - `test_ekspor_csv_memuat_utf8_bom_dan_direktif_sep_excel`: Memastikan response memiliki header `\xEF\xBB\xBFsep=,\r\n`.
   - `test_ekspor_penempatan_jalur_khusus_memuat_nama_jurusan`: Memastikan data peserta dengan `kuota_id = null` tetap terisi nama jurusannya dari profil peserta.
   - `test_sanitasi_formula_injection_tetap_aktif_dengan_direktif_sep`: Memastikan nilai yang diawali `=`, `+`, `-`, atau `@` tetap diawali petik tunggal (`'`).
   - `test_seluruh_ekspor_mencatat_audit_log_akses_data`: Memastikan audit log `AksesData` tetap tercatat sesuai PRD `AC-13-4`.
2. **Pengujian Integrasi Excel Nyata**:
   - Menjalankan skrip otomasi PowerShell COM Excel untuk membuka berkas hasil ekspor dan memastikan sel `A1`, `B1`, `C1`, `D1` terisi kolom-kolom terpisah (bukan menumpuk di `A1`).

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Tingkat | Rencana Mitigasi |
| :--- | :--- | :--- |
| Perangkat lunak non-Excel (misal text editor biasa atau parser Python) membaca baris 1 sebagai `sep=,`. | Rendah | Format `sep=,` adalah standar de-facto Microsoft CSV. Sebagian besar library seperti Python Pandas atau Excel secara cerdas mengabaikan atau mengenali direktif ini. Format ini tidak mengubah integritas data baris-baris berikutnya. |
| Konflik dengan pengujian lama yang memeriksa `assertStringStartsWith("\xEF\xBB\xBF", $content)`. | Nol | Konten tetap diawali oleh byte UTF-8 BOM `\xEF\xBB\xBF` terlebih dahulu sebelum `sep=,`, sehingga assertion `assertStringStartsWith` di seluruh test suite yang ada tetap 100% valid dan lulus. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] File CSV hasil unduhan dari tombol "Ekspor CSV Sanitasi" otomatis terpecah rapi ke kolom A, B, C, D, E, dst. saat dibuka langsung di Microsoft Excel.
- [x] Tidak ada lagi baris data yang menumpuk di Kolom A.
- [x] Kolom Jurusan untuk peserta Jalur Khusus terisi nama jurusannya (tidak ada lagi `,,` kosong).
- [x] Nilai tanggal dan status ditampilkan rapi dan terformat.
- [x] Perlindungan Anti-Formula Injection (AC-17-2) dan Audit Log Akses Data (AC-13-4) tetap berfungsi 100%.
- [x] Seluruh pengujian otomatis di `php artisan test` lulus 100%.
