# Rencana Kerja: Perbaikan & Standardisasi Ekspor Laporan Rekapitulasi Pendaftaran (Excel .xlsx & CSV)

**Dokumen**: `docs/plans/2026-11-01-laporan-rekapitulasi-pendaftaran-ekspor-excel.md`  
**Tanggal**: 1 November 2026  
**Status**: Selesai Diimplementasikan & Terverifikasi (100% Pass)  

---

## 1. Tujuan
Menyediakan fitur ekspor **Laporan Rekapitulasi Pendaftaran** (`/admin/laporan?tab=pendaftaran`) yang menghasilkan berkas spreadsheet terstruktur, rapi, dan profesional:
1. Menghasilkan berkas **Microsoft Excel asli (`.xlsx`)** dengan struktur tabel resmi:
   - Header tabel berwarna Sleman Deep Blue (`#1E3A8A`) dengan teks tebal putih (`#FFFFFF`).
   - Garis batas tabel tipis (*All Borders* `#D1D5DB`) pada setiap sel data.
   - Kolom-kolom terpisah rapi dengan lebar otomatis (*auto-width*) sehingga tidak ada teks terpotong atau tanggal bertumpuk `#####`.
   - Terbebas dari masalah gambar ke-2 (di mana data CSV koma menumpuk berantakan di dalam satu kolom A1).
2. Memperbaiki tombol aksi pada antarmuka admin agar menyediakan opsi utama **Ekspor Excel (.xlsx)** dan opsi **Ekspor CSV Sanitasi** yang kompatibel dengan Excel Indonesia (`sep=,` directive).
3. Menerapkan pengamanan sanitasi terhadap ancaman *CSV / Spreadsheet Formula Injection* (OWASP) untuk seluruh karakter diawali `=`, `+`, `-`, atau `@`.

---

## 2. Analisis Masalah Visual & Asumsi

### A. Investigasi Akar Masalah
1. **Mengapa Data di Excel Masuk ke Kolom A1 (Gambar 2)**:
   - Saat ini tombol *"Ekspor CSV Sanitasi"* mengunduh file berekstensi `.csv` dengan pemisah koma (`,`).
   - Pada sistem operasi Windows dengan pengaturan regional Indonesia (*Indonesian Locale*), Microsoft Excel menggunakan titik koma (`;`) sebagai pemisah daftar (*list separator*), bukan koma (`,`).
   - Akibatnya, saat admin membuka berkas `.csv`, Microsoft Excel tidak memecah baris menjadi kolom-kolom terpisah, melainkan memasukkan seluruh string ke dalam kolom **A1**:
     ```text
     ID,"Nama Peserta",Email,NIM/NISN,Institusi,Jurusan,"Bulan Mulai","Durasi (Bulan)",Status,"Tanggal Diajukan"
     ```
   - Selain itu, format `.csv` adalah teks polos murni yang tidak memiliki konsep format tabel, warna header, garis sel (*borders*), maupun lebar kolom.
2. **Ketiadaan Berkas Excel (.xlsx) Native**:
   - Di codebase telah dirancang fondasi layanan `app/Services/XlsxExportService.php` berbasis `ZipArchive` & OpenXML standar tanpa dependensi pihak ketiga berat, namun belum diintegrasikan ke dalam `ExportReportService` dan `LaporanController`.
3. **Penyelarasan UI Laporan Rekapitulasi**:
   - Header tab pendaftaran saat ini hanya memiliki satu tombol hijau *"Ekspor CSV Sanitasi"*. Perlu diperbarui dengan tombol utama **"Ekspor Excel (.xlsx)"** yang elegan dan opsi pendamping **"Ekspor CSV"**.

### B. Asumsi Kerja
- Ekspor Excel berbasis OpenXML murni (`.xlsx`) menggunakan ekstensi bawaan PHP `ZipArchive` yang sudah aktif di server lokal (`ZIP_OK`), tanpa memerlukan instalasi library berat eksternal (`phpoffice/phpspreadsheet`).
- Seluruh filter yang sedang aktif pada halaman (Bulan Magang, Status Keputusan, Jurusan, Dari Tanggal, Sampai Tanggal) tetap diteruskan ke berkas ekspor.
- Tab laporan lainnya (Penempatan, Realisasi Kuota, Rekap Nilai SuperAdmin) juga akan diselaraskan agar mendukung ekspor `.xlsx` dan `sep=,` CSV.

---

## 3. Evaluasi Opsi Pendekatan Teknis

| Parameter | Opsi 1: Hanya Perbaiki Delimiter CSV (`sep=,` / titik-koma `;`) | Opsi 2: Dual Export (Native .xlsx Berdesain Tabel + CSV Ber-direktif) — **Rekomendasi** |
| :--- | :--- | :--- |
| **Bentuk Output** | Berkas `.csv` biasa. | Berkas `.xlsx` asli dengan tabel bergaris + berkas `.csv` kompatibel. |
| **Tampilan di Excel** | Terpisah kolom, namun tetap polos tanpa warna header, tanpa border, tanpa penyesuaian lebar kolom. | **Tabel Profesional**: Header Sleman Blue `#1E3A8A`, teks putih tebal, *All Borders*, auto-width kolom. |
| **Pengalaman Admin** | Masih membuka format teks CSV mentah. | Terbuka instan di Microsoft Excel/Spreadsheet sebagai tabel dokumen dinas resmi. |
| **Kompatibilitas** | Terkadang masih memunculkan prompt impor teks di beberapa versi Excel. | **100% Native**: Tidak ada dialog impor, langsung terbuka rapi. |
| **Keamanan (OWASP)** | Sanitasi sel standar. | Sanitasi sel formula injection di kedua format. |

**Keputusan**: Memilih **Opsi 2**. Memberikan berkas **`.xlsx` resmi ber-styling tabel** sebagai format utama yang diminta user, serta menyempurnakan format `.csv` dengan direktif `sep=,\r\n` untuk kompatibilitas penuh.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Services/XlsxExportService.php` | **Modify** | Pastikan generator OpenXML memproduksi workbook, styles, dan worksheet dengan styling header navy Sleman `#1E3A8A`, all borders `#D1D5DB`, auto-width kolom min 13 max 50, dan sanitasi formula. |
| `app/Services/ExportReportService.php` | **Modify** | - Tambahkan direktif `\xEF\xBB\xBFsep=,\r\n` pada ekspor CSV agar tidak pernah menumpuk di kolom A.<br>- Tambahkan metode `exportPendaftaranXlsx()`, `exportPenempatanXlsx()`, `exportRealisasiKuotaXlsx()`, dan `exportNilaiXlsx()` yang memanggil `XlsxExportService`. |
| `app/Http/Controllers/Admin/LaporanController.php` | **Modify** | - Terima parameter `format` (`xlsx` atau `csv`). Default: `xlsx` untuk hasil tabel rapi.<br>- Return `Response` (`BinaryFileResponse` untuk XLSX, `StreamedResponse` untuk CSV). |
| `resources/views/admin/laporan/index.blade.php` | **Modify** | - Perbarui tombol aksi di Tab Rekapitulasi Pendaftaran: Tombol utama berkelas **"Ekspor Excel (.xlsx)"** dengan ikon spreadsheet/tabel hijau emerald, serta tombol opsi **"Ekspor CSV"**.<br>- Selaraskan juga tombol ekspor pada Tab Penempatan, Kuota, dan Nilai. |
| `tests/Feature/AdminEksporLaporanXlsxTest.php` | **Review & Verify** | Pastikan seluruh skenario pengujian XLSX lolos verifikasi `ZipArchive`, header, styles, dan sanitasi. |
| `tests/Feature/AdminEksporLaporanExcelTest.php` | **Review & Verify** | Pastikan pengujian kompatibilitas direktif `sep=,` CSV lolos 100%. |

---

## 5. Langkah Implementasi (Setelah Persetujuan)

### Tahap 1: Penyempurnaan Engine OpenXML XLSX & CSV Sanitasi
- [x] 1. Validasi dan selaraskan `app/Services/XlsxExportService.php`:
  - Format Header: Row height 28pt, Fill `#1E3A8A`, Font Bold Calibri 11pt White.
  - Format Data: Row height 22pt, All Borders thin `#D1D5DB`, Font Calibri 11pt.
  - Auto-Width: Hitung panjang karakter tiap kolom dengan margin `+ 5`, `min: 13`, `max: 50`.
  - Sanitasi: Nilai yang diawali `=`, `+`, `-`, `@` diberi prefix `'`.
- [x] 2. Perbarui `app/Services/ExportReportService.php`:
  - Tambahkan `sep=,\r\n` pada baris pertama CSV setelah BOM UTF-8.
  - Implementasikan delegasi pembuatan data baris ke `XlsxExportService` untuk pendaftaran, penempatan, kuota, dan nilai.

### Tahap 2: Integrasi Controller & Penanganan Parameter Format
- [x] 3. Perbarui `app/Http/Controllers/Admin/LaporanController.php`:
  - Periksa parameter `$request->input('format', 'xlsx')`.
  - Jika format `xlsx`, panggil metode XLSX. Jika format `csv`, panggil metode CSV streaming.
  - Catat riwayat pada Audit Log (`AksesData`).

### Tahap 3: Pembaruan Tampilan Antarmuka (Blade View)
- [x] 4. Edit `resources/views/admin/laporan/index.blade.php`:
  - Pada Tab Rekapitulasi Pendaftaran: Ganti tombol tunggal *"Ekspor CSV Sanitasi"* menjadi:
    - Tombol Utama: **Ekspor Excel (.xlsx)** dengan background hijau tua khas spreadsheet (`#166534` / `#15803D`) dan ikon tabel/spreadsheet SVG.
    - Tombol Pendamping: **Ekspor CSV** dengan outline rapi.
  - Pastikan seluruh parameter filter yang aktif di form (`bulan_magang_id`, `status`, `jurusan_id`, `dari_tanggal`, `sampai_tanggal`) otomatis terikut pada URL unduhan.

### Tahap 4: Pengujian & Verifikasi Visual Berkas
- [x] 5. Jalankan automated test suite:
  ```powershell
  php artisan test --filter="AdminEksporLaporanXlsxTest|AdminEksporLaporanExcelTest"
  ```
- [x] 6. Buka browser dan lakukan simulasi unduh berkas:
  - Buka `/admin/laporan?tab=pendaftaran`.
  - Klik tombol "Ekspor Excel (.xlsx)".
  - Buka berkas hasil unduhan untuk memverifikasi bahwa tabel tertata dengan kolom terpisah, header biru gelap berteks putih, garis tabel rapi (*borders*), dan data tidak menumpuk di kolom A1.

---

## 6. Struktur Kolom Rekapitulasi Pendaftaran di Excel

| No | Nama Kolom Excel | Format Sel | Contoh Isi |
| :---: | :--- | :---: | :--- |
| 1 | **ID** | Text / Center | `1` |
| 2 | **Nama Peserta** | Text / Left | `Naufal Pratama (Peserta Magang)` |
| 3 | **Email** | Text / Left | `peserta@magkom.go.id` |
| 4 | **NIM/NISN** | Text / Center | `0054321987` |
| 5 | **Institusi** | Text / Left | `SMK Negeri 2 Depok Sleman` |
| 6 | **Jurusan** | Text / Left | `Teknik Komputer dan Jaringan (TKJ)` |
| 7 | **Bulan Mulai** | Text / Center | `November 2026` |
| 8 | **Durasi (Bulan)** | Number / Center | `3` |
| 9 | **Status** | Text / Center | `Diterima` / `Diajukan` |
| 10 | **Tanggal Diajukan** | Date-Time / Center | `2026-09-26 14:30:00` |

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Tingkat Risiko | Mitigasi |
| :--- | :--- | :--- |
| Ketergantungan pustaka eksternal (*composer dependency*) | Nol | Menggunakan OpenXML murni dengan PHP `ZipArchive` bawaan, tanpa perlu instalasi package tambahan. |
| CSV Injection / Formula Injection | Sedang | Dilindungi fungsi `sanitizeCell()` yang otomatis menambahkan apostrof `'` jika teks sel diawali simbol formula berbahaya. |
| Memory Exhaustion saat data ribuan | Rendah | Query menggunakan relasi eager loading terarah (`with(['user.profil', 'bulanMagang', 'kuota.jurusan'])`). |
| Kompatibilitas Excel versi lama / WPS Office | Rendah | Menggunakan skema OpenXML Office 2006 standar (`application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`) yang didukung semua versi Office, WPS, dan Google Sheets. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Berkas hasil unduhan pendaftaran berekstensi `.xlsx` resmi yang dapat langsung dibuka di Microsoft Excel tanpa peringatan korup.
- [x] Di dalam Excel, data terdistribusi rapi ke masing-masing kolom (A s.d. J), tidak menumpuk di kolom A1 seperti pada gambar ke-2.
- [x] Header tabel memiliki latar belakang warna Sleman Blue `#1E3A8A`, teks tebal putih, dan tinggi baris proporsional.
- [x] Setiap sel tabel memiliki border pembatas yang jelas (*All Borders*).
- [x] Lebar kolom otomatis menyesuaikan panjang isi data sehingga tidak ada teks terpotong atau tanggal bertumpuk `#####`.
- [x] Tombol pada UI Laporan menyajikan opsi **Ekspor Excel (.xlsx)** dan **Ekspor CSV**.
- [x] Seluruh automated test (`AdminEksporLaporanXlsxTest` & `AdminEksporLaporanExcelTest`) lulus 100%.

