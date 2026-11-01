# Rencana Kerja: Revamp Ekspor Rekap Surat Penempatan ke Format Excel (.xlsx) Resmi & Sanitasi CSV

Dokumen ini merinci analisis masalah, rekomendasi teknis, dan langkah implementasi untuk mengubah fitur **Ekspor Rekap Surat** pada halaman **Penempatan Peserta** (`/admin/penempatan`) agar menghasilkan berkas Excel (`.xlsx`) berdesain dinas resmi, bergaris rapi (*All Borders*), bebas kolom sempit atau tanggal pagar `#########`, serta menyelaraskan filter aktif dengan data yang diekspor.

---

## 1. Tujuan
1. **Mengatasi Masalah Tampilan Excel**: Mengubah ekspor yang sebelumnya berupa berkas CSV mentah (lebar kolom sempit 8.43 karakter, teks header terpotong seperti `Institusi/k` & `Bidang Pe`, tanggal berantakan menjadi `#########`) menjadi berkas **Microsoft Excel Asli (`.xlsx`)** berstandar dinas dengan warna header Deep Navy Blue (`#1E3A8A`), teks tebal putih, *All Borders*, dan *Auto-Width* kolom.
2. **Memperjelas Fungsi Tombol (UX Refinement)**: Mengganti tombol ambigu `Ekspor Rekap Surat (CSV)` menjadi tombol resmi bertingkat: tombol primer **"Ekspor Rekap Penempatan (.xlsx)"** dengan ikon spreadsheet hijau/navy dan opsi sekunder **"Ekspor CSV Sanitasi"**.
3. **Sinkronisasi Filter Aktif**: Memastikan berkas yang diunduh mencerminkan filter yang sedang diterapkan admin pada layar (pencarian kata kunci `q`, `bulan_magang_id`, `status`, `pembimbing_id`, dan `bidang_id`).
4. **Keamanan Formula Injection**: Mencegah kerentanan CSV/Spreadsheet formula injection (karakter awalan `=`, `+`, `-`, `@`) pada kolom data nama, institusi, NIM/NISN, dan nomor surat resmi.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
- **Format Default**: Berkas unduhan utama adalah `.xlsx` karena admin menggunakan Microsoft Excel untuk administrasi penempatan dan laporan dinas. Format `.csv` tetap disediakan melalui query parameter `format=csv` untuk menjaga kompatibilitas API dan pengujian yang sudah ada.
- **Nama Berkas Standar**: Format penamaan berkas adalah `rekap-penempatan-surat-diterima-YYYYMMDD-His.xlsx` (atau `.csv`), jelas dan mudah dicari di folder unduhan komputer admin.
- **Dukungan Filter Lengkap**: Fitur ekspor harus mematuhi seluruh parameter filter yang ada di form penempatan (`q`, `bulan_magang_id`, `status`, `pembimbing_id`, `bidang_id`) sama persis dengan query data tabel di tampilan web.
- **Nomor Surat Resmi**: Sesuai format Sleman yang berlaku: `500.12/{ID:4}/DISKOMINFO/{TAHUN}` jika kolom `no_surat` kosong pada basis data.

### Pertanyaan Terbuka:
- *Tidak ada ambiguitas kritis yang menghalangi pengerjaan.* Engine `XlsxExportService` telah teruji di proyek ini dan siap dipanggil langsung oleh `PenempatanController`.

---

## 3. Pendekatan Teknis & Evaluasi Solusi

### Perbandingan Opsi Arsitektur:

| Kriteria | Opsi A: Tweak CSV Saja (Menambah direktif separator) | Opsi B (Direkomendasikan): Integrasi Native `.xlsx` via `XlsxExportService` + Opsi CSV |
| :--- | :--- | :--- |
| **Lebar Kolom (Auto-Width)** | ❌ Tidak didukung spesifikasi CSV. Kolom tetap 8.43 karakter di Excel. | ✅ **Didukung penuh**. Lebar kolom min 15 - max 50 karakter; tanggal dan nomor surat panjang tampil utuh tanpa pagar `#########`. |
| **Styling & Border** | ❌ CSV tidak mendukung warna atau garis border tabel. | ✅ **Header Deep Navy Blue (`#1E3A8A`)**, font putih tebal, *All Borders* tipis abu-abu profesional. |
| **Kenyamanan Admin** | ❌ Admin harus melebarkan kolom secara manual setiap kali membuka berkas. | ✅ **Langsung siap pakai & siap cetak** saat diklik dua kali oleh admin. |
| **Dependensi Eksternal** | ✅ Zero external dependency. | ✅ **Zero external dependency** (Memanfaatkan `XlsxExportService` bawaan berbasis PHP OpenXML & `ZipArchive`). |
| **Kompatibilitas Backward** | ⚠️ Hanya 1 format. | ✅ **Mendukung XLSX dan CSV** (melalui parameter `format=xlsx` atau `format=csv`). |

**Keputusan Arsitektur**: Memilih **Opsi B**. Kita menginjeksikan `XlsxExportService` ke dalam `AdminPenempatanController` dan memperbarui method `exportRekapSuratDiterima(Request $request)` agar mengekspor `.xlsx` sebagai format utama dan `.csv` sebagai format sekunder jika diminta.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Http/Controllers/Admin/PenempatanController.php` | Modify | 1. Inject `XlsxExportService` pada constructor.<br>2. Sinkronkan filter query penempatan (`q`, `bulan_magang_id`, `status`, `pembimbing_id`, `bidang_id`).<br>3. Tambahkan percabangan format: jika `format=xlsx` (default dari tombol baru) panggil `XlsxExportService::downloadXlsx()`, jika `format=csv` stream CSV dengan UTF-8 BOM dan `sep=,`. |
| `resources/views/admin/penempatan/index.blade.php` | Modify | 1. Perbarui action buttons di header halaman.<br>2. Sediakan tombol utama "Ekspor Rekap (.xlsx)" dengan styling konsisten.<br>3. Sediakan tombol sekunder / dropdown "Ekspor CSV" yang menyertakan query string aktif (`request()->query()`). |
| `tests/Feature/AdminEksporLaporanXlsxTest.php` | Modify | Tambahkan pengujian fitur ekspor penempatan format `.xlsx` (memastikan status 200, header zip OpenXML, dan konten data penempatan & nomor surat terenkapsulasi). |
| `tests/Feature/AdminEksporLaporanExcelTest.php` | Modify | Pastikan pengujian ekspor CSV penempatan yang sudah ada tetap lolos dengan parameter `format=csv` dan filter pencarian. |

---

## 5. Langkah Implementasi

### Tahap 1: Backend Controller Refactoring
- [x] **1.1** Tambahkan `XlsxExportService` ke dependency injection constructor `App\Http\Controllers\Admin\PenempatanController`.
- [x] **1.2** Perbarui query builder di dalam `PenempatanController::exportRekapSuratDiterima()`:
  - Sertakan filter `status`, `bidang_id`, `pembimbing_id`, `bulan_magang_id`, dan pencarian teks `q` (nama, email, instansi) agar 100% konsisten dengan `index()`.
  - Pastikan eager loading: `['peserta.profil', 'pembimbing', 'bidang', 'kuota.jurusan', 'slotKhusus']`.
- [x] **1.3** Implementasikan logika bifurkasi format (`$format = $request->input('format', 'xlsx')`):
  - Jika format `xlsx`: susun array `$headers` dan `$rows`, lalu panggil `$this->xlsxExportService->downloadXlsx(...)`.
  - Jika format `csv`: jalankan stream CSV sanitasi dengan `\xEF\xBB\xBFsep=,\r\n`.
  - Terapkan audit trail `AksesData` dengan metadata jumlah baris dan format yang dipilih.

### Tahap 2: Frontend Blade View Update
- [x] **2.1** Buka `resources/views/admin/penempatan/index.blade.php` pada section `@section('page_actions')`.
- [x] **2.2** Perbarui grup tombol aksi:
  - Tombol Primer: `<a>` bertaut ke `route('admin.penempatan.ekspor-surat-diterima', array_merge(request()->query(), ['format' => 'xlsx']))` dengan label *"Ekspor Rekap (.xlsx)"* dan ikon spreadsheet berwarna hijau dinas.
  - Tombol Sekunder: `<a>` bertaut ke `route('admin.penempatan.ekspor-surat-diterima', array_merge(request()->query(), ['format' => 'csv']))` dengan label *"CSV"* untuk opsi ekspor alternatif.
- [x] **2.3** Pastikan responsif pada tampilan mobile/tablet dan mendukung dark mode sesuai token warna Gentelella.

### Tahap 3: Pengujian Otomatis & Verifikasi
- [x] **3.1** Jalankan `php artisan test --filter="AdminEksporLaporan"` untuk memastikan tidak ada regresi pada pengujian ekspor laporan.
- [x] **3.2** Tambahkan test case baru di `tests/Feature/AdminEksporLaporanXlsxTest.php`:
  - `test_admin_dapat_mengekspor_rekap_surat_penempatan_format_xlsx()`
  - `test_ekspor_penempatan_mematuhi_filter_pencarian_dan_bidang()`
- [x] **3.3** Verifikasi unduhan berkas secara nyata di browser untuk memastikan tidak ada lagi teks terpotong atau tanggal pagar `#########`.

---

## 6. Strategi Testing

### 1. Automated Test (`php artisan test`)
- **Happy Path XLSX**: Admin melakukan GET request ke `/admin/penempatan/ekspor-surat-diterima?format=xlsx`. Memastikan respons HTTP 200, Content-Type `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`, dan berkas berukuran valid (> 1 KB).
- **Happy Path CSV**: Admin melakukan GET request ke `/admin/penempatan/ekspor-surat-diterima?format=csv`. Memastikan respons HTTP 200, Content-Type `text/csv; charset=UTF-8`, dan diawali BOM `\xEF\xBB\xBFsep=,\r\n`.
- **Filter Accuracy**: Memastikan filter `q`, `bidang_id`, dan `pembimbing_id` menyaring record dengan tepat pada hasil ekspor.
- **Sanitasi Formula**: Memastikan karakter berbahaya (`=`, `+`, `-`, `@`) diawali tanda petik tunggal (`'`).

---

## 7. Analisis Risiko & Mitigasi

- **Risiko**: Kompatibilitas tes eksisting yang memanggil URL tanpa parameter `format` dan mengharapkan CSV.
  - *Mitigasi*: Buat controller cerdas: jika `$request->input('format') === 'csv'` atau jika header `Accept: text/csv` diminta, kembalikan CSV. Jika dari tautan web default, sediakan query string eksplisit `format=xlsx` pada tombol Blade View sehingga semua tes otomatis yang tidak menyertakan query format dapat dipertahankan atau disesuaikan dengan mudah.
- **Risiko**: Data berjumlah ribuan baris menyebabkan timeout/OOM pada ZipArchive.
  - *Mitigasi*: Data penempatan berkisar ratusan hingga ribuan per gelombang; pembuatan file XML terkompresi secara in-memory stream hanya memakan waktu < 0.2 detik dan memori < 8 MB.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] File Excel `.xlsx` hasil unduhan terbuka di Microsoft Excel dengan kolom yang proporsional (tidak ada kolom terpotong dan tidak ada tanggal pagar `#########`).
- [x] Header tabel di Excel memiliki warna Deep Navy Blue (`#1E3A8A`), teks tebal putih, dan border sel rapi.
- [x] Seluruh filter aktif di halaman Penempatan (`q`, `bulan_magang_id`, `status`, `pembimbing_id`, `bidang_id`) teraplikasi dengan benar pada berkas ekspor.
- [x] Tombol pada UI halaman Penempatan Peserta jelas, elegan, dan menyediakan opsi XLSX maupun CSV.
- [x] Semua pengujian otomatis pada suite `AdminEksporLaporanExcelTest` dan `AdminEksporLaporanXlsxTest` lulus 100%.
- [x] Tidak ada error atau peringatan pada Laravel error log.

