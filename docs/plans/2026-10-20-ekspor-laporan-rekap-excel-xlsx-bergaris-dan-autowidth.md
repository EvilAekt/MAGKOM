# Rencana Kerja: Fitur Ekspor Laporan Rekapitulasi Format Excel (.xlsx) dengan All Borders & Auto-Width Kolom

## 1. Tujuan
Mengimplementasikan ekspor berkas asli Microsoft Excel (`.xlsx`) pada modul **Laporan & Ekspor** (Tab Pendaftaran, Penempatan, Kuota, dan Nilai) sesuai mandat PRD MagKom (**AC-17-2**: *"Laporan dapat diekspor CSV dan XLSX"*). Berkas `.xlsx` yang dihasilkan akan secara otomatis memiliki:
1. **Garis Kisi Lengkap (*All Borders*)** di setiap sel header dan baris data.
2. **Penyesuaian Lebar Kolom Otomatis (*AutoFit Column Width*)** sehingga kolom tanggal (misal `Tanggal Diajukan`) dan kolom teks panjang (nama, institusi, jurusan) tampil utuh, terbaca jelas, dan **tidak lagi terpotong atau tertutup tanda pagar `########`** di Microsoft Excel.
3. **Desain Header Resmi Dinas**: Latar biru elegan khas Sleman (`#1E3A8A`) dengan teks putih tebal di tengah.
4. **Sanitasi Anti-Formula Injection (AC-17-2)** dan **Audit Trail Akses Data (AC-13-4)** tetap aktif 100%.

---

## 2. Asumsi & Analisis Teknis

1. **Akar Penyebab `########` pada Kolom Tanggal Diajukan**:
   - Di Microsoft Excel, karakter `########` muncul saat sel berisi angka atau tanggal/waktu yang panjangnya melebihi lebar kolom default Excel (default ~8-10 karakter, sedangkan teks tanggal `20/10/2026 14:35` membutuhkan ~17 karakter).
   - Datanya di database **ada dan tidak kosong**.
   - Solusi teknis: Menetapkan tag `<col min="X" max="X" width="Y" customWidth="1"/>` pada OpenXML Sheet dengan perhitungan panjang karakter teks + *padding* (minimal 12, maksimal 50), sehingga kolom tanggal otomatis memiliki lebar ~20 dan langsung tampil utuh.
2. **Keterbatasan Format CSV vs Keunggulan Format XLSX**:
   - Format CSV adalah berkas teks murni yang secara spesifikasi teknis tidak dapat menyimpan metadata visual (border, warna, lebar kolom).
   - Format XLSX (OpenXML Spreadsheet) mendukung penuh penyimpanan struktur visual (border tipis, warna latar, jenis font, tinggi baris, dan lebar kolom) serta dibuka mulus oleh Microsoft Excel, Google Sheets, dan LibreOffice Calc tanpa peringatan format.
3. **Dukungan Lingkungan**:
   - Ekstensi PHP `ZipArchive` sudah terpasang dan aktif di lingkungan server Laragon (`ZipArchive OK`).

---

## 3. Evaluasi Opsi Arsitektur

| Kriteria | Opsi 1: Service Generator XLSX Native (`ZipArchive` & OpenXML) | Opsi 2: Library Eksternal (`phpoffice/phpspreadsheet`) |
| :--- | :--- | :--- |
| **Dependensi Eksternal** | **Nol Dependensi** (Memanfaatkan `ZipArchive` bawaan PHP). | Memerlukan instalasi paket composer berukuran besar (>15 MB vendor). |
| **Performa & Memori** | Sangat ringan, cepat, dan hemat RAM untuk ribuan baris data. | Mengonsumsi RAM relatif besar untuk manipulasi object model. |
| **Tampilan Visual** | Mendukung All Borders, Warna Header `#1E3A8A`, Font Bold, dan AutoFit Width. | Mendukung format styling lengkap. |
| **Kesesuaian PRD** | 100% mematuhi AC-17-2, AC-13-4, dan standar keamanan. | 100% mematuhi AC-17-2. |
| **Rekomendasi** | **Sangat Direkomendasikan (Terpilih)** | Cadangan jika di masa depan dibutuhkan rumus formula kompleks. |

**Keputusan Arsitektur**: Membangun kelas mandiri `App\Services\XlsxExportService` berbasis `ZipArchive` native yang menghasilkan berkas OpenXML Spreadsheet (`.xlsx`) standar industri.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`app/Services/XlsxExportService.php`](file:///d:/laragon/www/MAGKOM/app/Services/XlsxExportService.php) | Create | Service baru untuk menghasilkan berkas `.xlsx` dengan XML styling (All Borders `#D1D5DB`, Header fill `#1E3A8A`, font putih tebal, Auto-width calculation, dan streaming response). |
| [`app/Services/ExportReportService.php`](file:///d:/laragon/www/MAGKOM/app/Services/ExportReportService.php) | Modify | Tambahkan parameter `$format = 'csv'` (atau `'xlsx'`) pada `exportPendaftaran`, `exportPenempatan`, `exportRealisasiKuota`, `exportNilai`, dan integrasikan dengan `XlsxExportService`. |
| [`app/Http/Controllers/Admin/LaporanController.php`](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/Admin/LaporanController.php) | Modify | Teruskan parameter `request('format', 'csv')` dari rute ke `ExportReportService`. |
| [`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php) | Modify | Tambahkan tombol berdampingan **"Ekspor Excel (.xlsx)"** (warna hijau tua/emerald dengan ikon Excel) dan tombol sekunder **"Ekspor CSV"** di setiap tab (Pendaftaran, Penempatan, Kuota, Nilai). |
| [`tests/Feature/AdminEksporLaporanXlsxTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminEksporLaporanXlsxTest.php) | Create | Pengujian otomatis validasi response MIME type `.xlsx`, keterisian tanggal diajukan, border, lebar kolom, dan formula injection protection. |

---

## 5. Langkah Implementasi

### Tahap 1: Pembuatan `XlsxExportService`
- [x] Buat file `app/Services/XlsxExportService.php`:
  - Metode `generate(string $filename, array $headers, array $rows): BinaryFileResponse | Response`.
  - Susun struktur zip OpenXML standar:
    - `[Content_Types].xml`
    - `_rels/.rels`
    - `xl/_rels/workbook.xml.rels`
    - `xl/workbook.xml`
    - `xl/styles.xml` (mendefinisikan Font 0=Normal, Font 1=Bold Putih, Fill 2=Solid `#1E3A8A`, Border 1=Thin All Borders `#D1D5DB`, CellXf 1=Header, CellXf 2=Data Row bergaris).
    - `xl/worksheets/sheet1.xml` (menghitung lebar kolom otomatis berdasarkan panjang teks terbesar tiap kolom, tinggi baris header 28pt, tinggi baris data 22pt).
  - Terapkan sanitasi formula injection (`AC-17-2`) pada setiap nilai sel.

### Tahap 2: Integrasi ke `ExportReportService` & `LaporanController`
- [x] Di `ExportReportService`:
  - Suntikkan dependency `XlsxExportService`.
  - Dukung pemilihan format `'xlsx'` atau `'csv'` pada:
    - `exportPendaftaran($user, $filters, $format = 'csv')`
    - `exportPenempatan($user, $filters, $format = 'csv')`
    - `exportRealisasiKuota($user, $format = 'csv')`
    - `exportNilai($user, $format = 'csv')`
  - Catat format ekspor pada audit trail `detail['format'] = $format`.
- [x] Di `LaporanController`:
  - Tangkap parameter `$request->input('format', 'csv')` dan teruskan ke `exportService`.

### Tahap 3: Pembaruan Antarmuka (UI Blade)
- [x] Di `resources/views/admin/laporan/index.blade.php`:
  - Pada Tab Pendaftaran: Tambahkan tombol `Ekspor Excel (.xlsx)` berdampingan dengan `Ekspor CSV`.
  - Pada Tab Penempatan: Tambahkan tombol `Ekspor Excel (.xlsx)` berdampingan dengan `Ekspor CSV`.
  - Pada Tab Kuota: Tambahkan tombol `Ekspor Excel (.xlsx)` berdampingan dengan `Ekspor CSV`.
  - Pada Tab Nilai: Tambahkan tombol `Ekspor Excel (.xlsx)` berdampingan dengan `Ekspor CSV`.

### Tahap 4: Pengujian & Verifikasi
- [x] Buat test suite `tests/Feature/AdminEksporLaporanXlsxTest.php`:
  - Uji response download mengembalikan `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`.
  - Uji berkas zip XLSX memuat file XML yang valid (`styles.xml`, `sheet1.xml`).
  - Uji kolom tanggal diajukan terisi dan memiliki lebar kolom custom width.
  - Uji seluruh test suite ekspor dan penempatan yang ada: `php artisan test`.
  - Jalankan `vendor/bin/pint` untuk menjaga standar PSR-12.
- [x] Uji berkas hasil unduhan dengan Microsoft Excel COM di lingkungan Windows untuk memastikan:
  - Seluruh garis border tabel (*All Borders*) muncul rapi.
  - Tanggal diajukan langsung terbaca jelas tanpa tanda pagar `########`.

---

## 6. Strategi Testing

1. **Automated Feature Test**:
   - `test_ekspor_xlsx_pendaftaran_mengembalikan_header_dan_konten_valid()`
   - `test_ekspor_xlsx_penempatan_memuat_jurusan_dan_all_borders()`
   - `test_ekspor_xlsx_mencegah_formula_injection()`
   - `test_ekspor_xlsx_mencatat_audit_trail_akses_data()`
2. **Verifikasi Integrasi Excel Nyata**:
   - Eksekusi pengujian otomasi PowerShell COM Excel untuk membuka berkas `.xlsx` hasil generate dan memverifikasi properti:
     - `Border.LineStyle = 1`
     - `Header.Interior.Color = 9058846` (`#1E3A8A`)
     - `ColumnWidth` pada tanggal diajukan > 16 (tidak ada tanda `########`)

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Tingkat | Rencana Mitigasi |
| :--- | :--- | :--- |
| File XLSX corrupt jika karakter spesial XML (`&`, `<`, `>`, `"`) tidak di-escape. | Sedang | Seluruh teks sel string wajib di-escape menggunakan `htmlspecialchars($val, ENT_XML1, 'UTF-8')`. |
| Konflik dengan fitur CSV yang sudah berjalan. | Nol | Format CSV tetap tersedia dan berjalan seperti biasa; opsi XLSX menjadi format default atau pilihan pelengkap resmi (*dual-format export*). |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Pengguna dapat mengunduh berkas laporan dalam format resmi Microsoft Excel (`.xlsx`).
- [x] Saat dibuka di Microsoft Excel, tabel otomatis memiliki garis kotak-kotak (*All Borders*) di seluruh sel.
- [x] Header tabel tampil menarik dengan warna biru dinas (`#1E3A8A`) dan teks putih tebal.
- [x] Kolom tanggal diajukan (dan kolom lainnya) memiliki lebar otomatis yang pas, sehingga isi tanggal langsung tampil jelas tanpa tanda pagar `########`.
- [x] Kolom jurusan untuk peserta Jalur Khusus tetap terisi lengkap.
- [x] Sanitasi Anti-Formula Injection (AC-17-2) dan Audit Log Akses Data (AC-13-4) tetap berfungsi 100%.
- [x] Seluruh pengujian otomatis di `php artisan test` dan linter `pint` lulus 100%.
