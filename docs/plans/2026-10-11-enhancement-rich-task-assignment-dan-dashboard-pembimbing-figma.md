# Plan: Penyempurnaan Menyeluruh Rich Task Assignment & Visual Elevasi Dashboard Pembimbing Sesuai Figma

- **Tanggal**: 2026-10-11
- **Status**: Selesai (Completed)
- **Author**: Antigravity (web-implementation)
- **Target Halaman**: 
  - Dashboard Operasional Pembimbing: `/pembimbing/dashboard` ([`resources/views/pembimbing/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/dashboard.blade.php))
  - Modul Manajemen Penugasan Pembimbing: `/pembimbing/tugas` ([`resources/views/pembimbing/tugas/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/index.blade.php), [`resources/views/pembimbing/tugas/show.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/show.blade.php))
  - Detail Penugasan & Pengunggahan Progres Peserta: `/peserta/tugas/{tugas}` ([`resources/views/peserta/tugas/show.blade.php`](file:///g:/MAGKOM/resources/views/peserta/tugas/show.blade.php))
  - Backend Arsitektur, Controller & Model: 
    - [`app/Http/Controllers/Pembimbing/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/Pembimbing/DashboardController.php)
    - [`app/Http/Controllers/Pembimbing/TugasController.php`](file:///g:/MAGKOM/app/Http/Controllers/Pembimbing/TugasController.php)
    - [`app/Http/Controllers/Peserta/TugasController.php`](file:///g:/MAGKOM/app/Http/Controllers/Peserta/TugasController.php)
    - [`app/Models/Tugas.php`](file:///g:/MAGKOM/app/Models/Tugas.php)
    - [`app/Http/Requests/TugasRequest.php`](file:///g:/MAGKOM/app/Http/Requests/TugasRequest.php)

---

## 1. Latar Belakang & Tujuan

Saat ini, modul penugasan pembimbing di portal MagKom hanya mendukung satu kolom teks deskripsi tugas sederhana (`deskripsi: text`). Hal ini membuat penugasan terasa monoton dan kurang representatif terhadap alur kerja riil di Diskominfo Sleman, di mana pembimbing sering kali perlu:
1. Menyertakan **dokumen acuan kerja** resmi seperti Term of Reference (TOR) berformat PDF, lembar panduan teknis Word (`.docx`), template rekonsiliasi Excel (`.xlsx`), bahan presentasi (`.pptx`), maupun materi/aset dalam arsip (`.zip`).
2. Menyertakan **tautan eksternal** ke repositori GitHub, board Jira/Trello, dokumen kolaboratif Google Docs, atau mockup UI di Figma.
3. Mengakses **pembuatan tugas secara instan langsung dari Dashboard** tanpa harus berpindah ke modul terpisah (`/pembimbing/tugas`), serta memantau status tugas dengan tampilan visual kartu interaktif yang modern sesuai standar desain Figma Node 560-41611 (Sleman Blue `#0641CC`).
4. Menyediakan tampilan di sisi peserta (`/peserta/tugas/show`) yang simetris, jelas, dan kaya informasi, sehingga peserta memahami ekspektasi tugas sebelum mengunggah laporan kemajuan pekerjaannya.

### Tujuan Utama (Key Objectives):
- **Rich Task Assignment Engine**: Memungkinkan pembimbing membuat dan memperbarui tugas dengan menyertakan teks deskripsi terstruktur, URL tautan referensi eksternal, dan berkas lampiran acuan kerja multi-format (PDF, Word, Excel, PPT, ZIP, gambar).
- **Elevasi Visual Dashboard Pembimbing (Figma Sleman Blue)**:
  - Menyematkan **Quick Task Creator Modal/Drawer** interaktif langsung pada Dashboard Pembimbing yang dipicu oleh tombol `+ Beri Tugas Baru` di Hero Header Sleman Blue.
  - Memperkaya kartu tugas pada **Tab 1 (Upcoming Deadlines Tracker)** dengan badge tipe sumber daya (ikon PDF merah, Word biru, Excel hijau, Zip ungu, Link biru), avatar profil mentee, dan countdown sisa waktu pengerjaan.
  - Memperkaya **Tab 2 (Action Hub)** dengan tampilan pembanding antara instruksi tugas pembimbing vs hasil kemajuan yang dikirimkan oleh mentee.
- **Simetrisasi Sisi Peserta (Mentee Experience)**: Menampilkan kartu panduan sumber daya tugas di portal peserta lengkap dengan tombol buka tautan dan unduh berkas lampiran resmi.
- **Keamanan & Otorisasi Ketat (Zero IDOR & Whitelist File Protection)**: Berkas lampiran disimpan di disk privat dengan validasi MIME-type biner yang ketat (mencegah upload skrip berbahaya) dan dilayani lewat endpoint bertanda tangan otorisasi.

---

## 2. Asumsi Teknis & Batasan

1. **Format Berkas Lampiran**:
   - Format yang didukung (whitelist):
     - Dokumen: `.pdf` (`application/pdf`), `.doc` (`application/msword`), `.docx` (`application/vnd.openxmlformats-officedocument.wordprocessingml.document`)
     - Spreadsheet: `.xls` (`application/vnd.ms-excel`), `.xlsx` (`application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`), `.csv` (`text/csv`, `text/plain`)
     - Presentasi: `.ppt` (`application/vnd.ms-powerpoint`), `.pptx` (`application/vnd.openxmlformats-officedocument.presentationml.presentation`)
     - Arsip: `.zip` (`application/zip`, `application/x-zip-compressed`)
     - Gambar acuan: `.png`, `.jpg`, `.jpeg`
   - Ukuran maksimum per berkas adalah **10 MB** (10.240 KB).
   - Lokasi penyimpanan: Disk privat `storage/app/private/tugas/{tugas_id}/` (tidak dapat diakses langsung via URL web publik).
2. **Tautan Referensi Eksternal**:
   - URL wajib berformat valid (`http://` atau `https://`), maksimal 255 karakter.
   - Dirender dengan atribut keamanan `target="_blank" rel="noopener noreferrer"`.
3. **Kompatibilitas Tugas Eksisting (Zero-Breaking Change)**:
   - Kolom database baru bersifat `nullable()`, sehingga tugas-tugas yang telah dibuat sebelumnya tetap dapat diakses dan tidak mengalami galat (*backward compatible*).
4. **Hak Akses & Otorisasi**:
   - Pembimbing hanya dapat membuat tugas untuk peserta yang berada di bawah bimbingan aktifnya (`pembimbing_id == auth()->id()`).
   - Berkas lampiran tugas hanya dapat diunduh oleh pembimbing penugasan, admin/superadmin, dan mentee yang ditugaskan. Percobaan akses oleh peserta lain akan menghasilkan respon `403 Forbidden`.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Teknis:

| Kriteria Evaluasi | Opsi A: Tabel Terpisah `tugas_lampiran` (Multi-attachment Many-to-One) | Opsi B: Kolom Terintegrasi pada Tabel `tugas` + Aksesor Format (Rekomendasi) |
| :--- | :--- | :--- |
| **Kebutuhan Penggunaan Riil** | Kompleksitas tinggi: Membutuhkan tabel relasi baru, foreign key, dynamic file array di JavaScript, dan upload bertahap. | **Sangat Tepat Guna**: Rata-rata penugasan dinas membutuhkan 1 bundel materi acuan utama (misal 1 file TOR/PDF atau 1 file ZIP template) + 1 link referensi eksternal (Figma/Docs/GitHub). |
| **Beban Kueri & Kecepatan Dashboard** | Memerlukan kueri join tambahan `with('lampiran')` pada setiap pemanggilan tugas di Tab 1, Tab 2, dan halaman direktori. | **Sangat Cepat**: Atribut lampiran langsung tersedia dalam objek `Tugas` yang sudah ter-load di memori, zero additional SQL query. |
| **User Experience Pembimbing** | Form menjadi panjang dan rentan gagal validasi parsial. | **Clean & Intuitif**: Form ringkas, jelas, drag-and-drop file picker terpadu dengan feedback nama dan ukuran file instan. |
| **Opsi Cadangan Jika Banyak File** | Menyimpan masing-masing file dalam baris terpisah. | Pembimbing dapat menyatukan beberapa file pendukung ke dalam 1 berkas `.zip` atau mencantumkan folder link Google Drive di kolom `link_referensi`. |

> **Keputusan**: Memilih **Opsi B** dengan menambahkan kolom terintegrasi pada tabel `tugas` (`link_referensi`, `file_lampiran`, `nama_file_lampiran`, `ukuran_file_lampiran`). Skema ini menjamin kestabilan performa, kesederhanaan alur kerja, dan kompatibilitas penuh dengan sistem yang sudah berjalan.

---

## 4. Desain Antarmuka & Spesifikasi Komponen Visual (Figma Node 560-41611)

### 4.1. Modal Cepat "Beri Tugas Baru" di Dashboard Pembimbing
- **Trigger**: Tombol `+ Beri Tugas Baru` pada Hero Header Sleman Blue.
- **Struktur Tampilan Modal**:
  - **Header**: Latar putih bersih dengan judul tegas *"Beri Tugas Baru untuk Mentee"* dan tombol tutup silang (`×`).
  - **Grid Dua Kolom Responsif**:
    - Kolom Kiri:
      1. Dropdown Mentee Bimbingan (menampilkan nama mentee, instansi/jurusan, dan sisa hari magang).
      2. Input Judul Tugas (maksimal 150 karakter dengan placeholder kontekstual).
      3. Dropdown Kategori Bidang & Tingkat Kompleksitas (Rendah / Sedang / Tinggi).
      4. Input Batas Waktu (Deadline) dengan pembatasan otomatis `min: hari ini` dan `max: tgl_selesai magang mentee`.
    - Kolom Kanan:
      5. Textarea Deskripsi & Arahan Tugas (3–4 baris, petunjuk ekspektasi hasil kerja).
      6. **Input Tautan Referensi Eksternal**: Dilengkapi ikon tautan (`🔗`), placeholder `https://github.com/... atau https://docs.google.com/...`.
      7. **Dropzone Berkas Acuan Tugas**: Area drag-and-drop dengan ikon dokumen awan, daftar ekstensi yang didukung (`.pdf, .doc, .docx, .xls, .xlsx, .ppt, .zip`), serta teks nama file dan ukuran otomatis saat dipilih.
  - **Footer Modal**: Tombol `Batal` (abu-abu netral) dan tombol `Simpan & Berikan Tugas` (Sleman Blue `#0641CC` dengan efek hover dan state loading).

### 4.2. Kartu Tugas pada Tab 1 (Upcoming Deadlines Tracker)
- **Tampilan Kartu Tugas Modern**:
  - Badge Kompleksitas: Hijau untuk Rendah, Amber untuk Sedang, Indigo/Ungu untuk Tinggi.
  - Badge Tenggat Waktu: Merah jika jatuh tempo $\le 1$ hari, Kuning jika $\le 3$ hari, Biru jika $> 3$ hari.
  - Badge Sumber Daya:
    - `[PDF] Panduan_Teknis.pdf (1.2 MB)` dengan ikon dokumen merah.
    - `[DOCX] Format_Laporan.docx (450 KB)` dengan ikon dokumen biru.
    - `[XLSX] Data_Uji.xlsx (800 KB)` dengan ikon dokumen hijau.
    - `[ZIP] Aset_Desain.zip (4.5 MB)` dengan ikon dokumen ungu.
    - `[LINK] Referensi Eksternal` dengan ikon globe biru dan link aktif.
  - Profil Mentee: Avatar/foto peserta, nama lengkap, dan instansi asal.
  - Tombol Aksi Cepat: `Reviu Laporan Mentee` (langsung lompat ke Tab 2) atau `Lihat Rincian Tugas`.

### 4.3. Kartu Sumber Daya di Halaman Peserta (`/peserta/tugas/show`)
- Peserta melihat panel khusus **"Materi & Panduan dari Pembimbing"**:
  - Menampilkan deskripsi instruksi pembimbing secara rapi dengan tipografi nyaman dibaca.
  - Kotak tautan referensi eksternal dengan tombol "Buka Tautan Acuan ↗".
  - Kartu lampiran berkas resmi dengan tombol "Unduh Berkas Panduan (PDF/Word/Excel)".

---

## 5. Rincian Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `database/migrations/2026_10_11_000001_add_lampiran_and_link_to_tugas_table.php` | Create | Migrasi penambahan kolom `link_referensi`, `file_lampiran`, `nama_file_lampiran`, `ukuran_file_lampiran` pada tabel `tugas`. |
| [`app/Models/Tugas.php`](file:///g:/MAGKOM/app/Models/Tugas.php) | Modify | 1. Tambahkan kolom baru ke array `$fillable`.<br>2. Tambahkan accessor `tipe_file_lampiran` (ekstrak ekstensi `.pdf`, `.docx`, dll.) dan `ukuran_lampiran_formatted` (konversi byte ke KB/MB).<br>3. Tambahkan accessor `icon_lampiran_color` dan `has_sumber_daya`. |
| [`app/Http/Requests/TugasRequest.php`](file:///g:/MAGKOM/app/Http/Requests/TugasRequest.php) | Modify | 1. Aturan validasi `link_referensi`: `nullable|url|max:255`.<br>2. Aturan validasi `file_lampiran`: `nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,png,jpg,jpeg|max:10240`.<br>3. Pesan kustom jika URL atau file tidak valid. |
| [`app/Http/Controllers/Pembimbing/TugasController.php`](file:///g:/MAGKOM/app/Http/Controllers/Pembimbing/TugasController.php) | Modify | 1. Logika simpan fisik berkas ke disk `private` pada method `store()` dan pergantian berkas pada `update()`.<br>2. Method `unduhLampiran(Tugas $tugas)` untuk download berkas privat dengan validasi otorisasi.<br>3. Pencatatan audit trail saat berkas lampiran diunggah/diperbarui. |
| [`app/Http/Controllers/Peserta/TugasController.php`](file:///g:/MAGKOM/app/Http/Controllers/Peserta/TugasController.php) | Modify | Tambahkan method `unduhLampiran(Tugas $tugas)` yang memverifikasi kepemilikan tugas mentee sebelum mengalirkan (*streaming*) file privat. |
| [`app/Http/Controllers/Pembimbing/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/Pembimbing/DashboardController.php) | Modify | Teruskan variabel `$penempatanList` (daftar mentee aktif pembimbing) ke view dashboard agar modal quick task creator dapat langsung memilih mentee tanpa perlu reload. |
| [`resources/views/pembimbing/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/dashboard.blade.php) | Modify | 1. Pasang Modal Quick Task Creator di root view dashboard.<br>2. Hubungkan tombol Hero `+ Beri Tugas Baru` dengan modal ini.<br>3. Perkaya kartu tugas di Tab 1 (Upcoming Deadlines) dengan badge indikator lampiran/link dan avatar mentee.<br>4. Tambahkan script helper interaktif untuk live preview nama file upload di modal. |
| [`resources/views/pembimbing/tugas/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/index.blade.php) | Modify | Perbarui form modal buat tugas dan tabel penugasan dengan kolom sumber daya (indikator lampiran berkas dan link eksternal). |
| [`resources/views/pembimbing/tugas/show.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/show.blade.php) | Modify | Tampilkan kartu ringkasan lampiran berkas acuan (dengan tombol unduh aman) dan tautan referensi eksternal di atas daftar progres mentee. |
| [`resources/views/peserta/tugas/show.blade.php`](file:///g:/MAGKOM/resources/views/peserta/tugas/show.blade.php) | Modify | Tambahkan panel "Materi & Lampiran dari Pembimbing" yang menampilkan tautan eksternal dan tombol unduh berkas panduan kerja resmi. |
| [`routes/web.php`](file:///g:/MAGKOM/routes/web.php) | Modify | Daftarkan rute: `GET /pembimbing/tugas/{tugas}/lampiran` dan `GET /peserta/tugas/{tugas}/lampiran`. |
| `tests/Feature/RichTaskAssignmentTest.php` | Create | Test suite pengujian otomatis untuk memverifikasi fungsionalitas penugasan kaya, validasi berkas, otorisasi download, dan rendering visual dashboard. |

---

## 6. Langkah Implementasi Terinci

### Fase 1: Skema Basis Data & Model Eloquent
- [ ] **Langkah 1.1**: Buat migration `2026_10_11_000001_add_lampiran_and_link_to_tugas_table.php` dengan kolom:
  ```php
  $table->string('link_referensi', 255)->nullable()->after('deskripsi');
  $table->string('file_lampiran', 255)->nullable()->after('link_referensi');
  $table->string('nama_file_lampiran', 255)->nullable()->after('file_lampiran');
  $table->unsignedInteger('ukuran_file_lampiran')->nullable()->after('nama_file_lampiran');
  ```
- [x] **Langkah 1.2**: Eksekusi `php artisan migrate` secara aman.
- [x] **Langkah 1.3**: Buka [`app/Models/Tugas.php`](file:///g:/MAGKOM/app/Models/Tugas.php) dan daftarkan keempat kolom ke `$fillable`.
- [x] **Langkah 1.4**: Buat aksesor helper di `Tugas.php`:
  - `getEkstensiLampiranAttribute()`: Mengembalikan ekstensi (misal `pdf`, `docx`, `xlsx`, `zip`).
  - `getUkuranFormatAttribute()`: Mengonversi byte ke representasi mudah dibaca (misal `1.4 MB`, `320 KB`).
  - `getBadgeColorLampiranAttribute()`: Mengembalikan skema warna (merah untuk PDF, biru untuk Word, hijau untuk Excel, ungu untuk ZIP).

### Fase 2: Validasi & Backend Controller Service
- [x] **Langkah 2.1**: Perbarui [`app/Http/Requests/TugasRequest.php`](file:///g:/MAGKOM/app/Http/Requests/TugasRequest.php) dengan aturan:
  - `'link_referensi' => ['nullable', 'url', 'max:255']`
  - `'file_lampiran' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,png,jpg,jpeg', 'max:10240']`
  - Pesan kustom ramah pengguna jika ekstensi tidak diizinkan atau ukuran melebihi 10 MB.
- [x] **Langkah 2.2**: Perbarui method `store()` di [`app/Http/Controllers/Pembimbing/TugasController.php`](file:///g:/MAGKOM/app/Http/Controllers/Pembimbing/TugasController.php):
  - Jika request memiliki file lampiran: simpan file ke disk `private` dengan path `tugas/{tugas_id}/{hash_name}.{ext}`.
  - Simpan `nama_file_lampiran` (nama asli yang diunggah pengguna) dan `ukuran_file_lampiran` (ukuran dalam byte).
  - Catat log audit ke tabel `audit_log`.
- [x] **Langkah 2.3**: Buat method `unduhLampiran(Tugas $tugas)` di `Pembimbing\TugasController`:
  - Otorisasi: Pastikan pembimbing yang login adalah pembimbing mentee tugas terkait (atau Admin/SuperAdmin).
  - Periksa eksistensi fisik file di storage private.
  - Return `Storage::disk('private')->download($tugas->file_lampiran, $tugas->nama_file_lampiran)`.
- [x] **Langkah 2.4**: Buat method `unduhLampiran(Tugas $tugas)` di `Peserta\TugasController`:
  - Otorisasi: Pastikan mentee yang login adalah peserta yang ditugaskan (`$tugas->penempatan->peserta_id === auth()->id()`).
  - Tolak peserta lain dengan respon `abort(403)`.
  - Return streaming download berkas.
- [x] **Langkah 2.5**: Daftarkan kedua rute di [`routes/web.php`](file:///g:/MAGKOM/routes/web.php).
- [x] **Langkah 2.6**: Perbarui [`app/Http/Controllers/Pembimbing/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/Pembimbing/DashboardController.php) agar menambahkan `$penempatanList` (mentee aktif) dan `$allPendingProgresLogs` ke variabel yang dikirim ke view `pembimbing.dashboard`.

### Fase 3: Peningkatan Tampilan Dashboard Pembimbing (`pembimbing/dashboard.blade.php`)
- [x] **Langkah 3.1**: Tambahkan **Modal Quick Task Creator** di akhir file view `pembimbing/dashboard.blade.php` dengan atribut `enctype="multipart/form-data"` dan action `route('pembimbing.tugas.store')`.
- [x] **Langkah 3.2**: Hubungkan tombol hero `+ Beri Tugas Baru` (line 166) agar memicu `openModalBuatTugasDashboard()` tanpa perlu reload halaman.
- [x] **Langkah 3.3**: Tambahkan file picker interaktif di dalam modal dengan drag-and-drop zone dan live indicator (nama file, badge ukuran, tombol hapus file sebelum submit).
- [x] **Langkah 3.4**: Perbarui widget **"Tenggat Waktu Tugas Mendatang (Upcoming Deadlines)"** di Tab 1:
  - Tampilkan pill penanda sumber daya:
    - Ikon link biru dengan teks *"Link Referensi"* jika `link_referensi` terisi.
    - Ikon file dengan badge warna dinamis (merah: PDF, biru: DOCX, hijau: XLSX, ungu: ZIP) beserta nama file aslinya.
  - Tambahkan tombol aksi langsung: `Buka Tautan` (jika ada link) dan `Unduh Materi` (jika ada file).
- [x] **Langkah 3.5**: Perbarui Tab 2 (Action Hub): jika ada progres yang dikirimkan oleh mentee untuk tugas tertentu, sediakan tombol perbandingan cepat antara deskripsi tugas asli vs hasil kerja mentee.

### Fase 4: Peningkatan Modul Tugas Pembimbing & Portal Peserta
- [x] **Langkah 4.1**: Perbarui formulir buat tugas di [`resources/views/pembimbing/tugas/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/index.blade.php) agar sinkron dengan input `link_referensi` dan file picker lampiran.
- [x] **Langkah 4.2**: Tambahkan kolom indikator *"Lampiran/Tautan"* pada tabel daftar tugas di `pembimbing/tugas/index.blade.php`.
- [x] **Langkah 4.3**: Perbarui [`resources/views/pembimbing/tugas/show.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/show.blade.php) untuk menampilkan kartu materi acuan tugas secara komprehensif.
- [x] **Langkah 4.4**: Perbarui [`resources/views/peserta/tugas/show.blade.php`](file:///g:/MAGKOM/resources/views/peserta/tugas/show.blade.php):
  - Tambahkan blok visual *"Panduan & Sumber Daya dari Pembimbing"*.
  - Sediakan tombol klik tautan eksternal (`target="_blank"`) dan tombol unduh berkas lampiran resmi pembimbing.

### Fase 5: Pengujian Otomatis & QA Verifikasi
- [x] **Langkah 5.1**: Buat test suite baru [`tests/Feature/RichTaskAssignmentTest.php`](file:///g:/MAGKOM/tests/Feature/RichTaskAssignmentTest.php) dengan skenario pengujian:
  1. Pembimbing berhasil membuat tugas dengan tautan referensi dan berkas PDF/Word/Excel.
  2. Berkas berhasil tersimpan di storage privat dan metadata tersimpan di database.
  3. Validasi menolak file berbahaya (misal `.exe`, `.sh`, `.php`) dan file berukuran $> 10\text{ MB}$.
  4. Validasi menolak tautan non-URL (misal string sembarangan tanpa protokol valid).
  5. Peserta yang ditugaskan berhasil mengunduh berkas lampiran tugas.
  6. Peserta lain yang bukan pemilik tugas ditolak saat mencoba mengunduh berkas (403 IDOR prevention).
  7. Pembimbing lain yang bukan pembimbing mentee terkait ditolak saat mengunduh berkas.
  8. Dashboard pembimbing merender modal quick task creator dan kartu upcoming deadlines dengan badge lampiran secara benar.
- [x] **Langkah 5.2**: Jalankan seluruh 5 test suite pembimbing sebelumnya (`PembimbingDashboardEnhancementTest`, `PembimbingFullJobDeskTest`, `PembimbingPortalFeatureTest`, `TugasLogbookTest`, `ScoringAndCompletionTest`) untuk menjamin **0 regresi** (40 tests lulus, 196 assertions).
- [x] **Langkah 5.3**: Verifikasi visual menggunakan browser subagent pada:
  - Dashboard pembimbing (klik tombol `+ Beri Tugas Baru`, coba pilih file di modal).
  - Tampilan Tab 1 Upcoming Deadlines dengan badge dokumen.
  - Tampilan detail tugas peserta dengan tombol unduh materi acuan.

---

## 7. Arsitektur Keamanan & Mitigasi Risiko

| Kategori Risiko | Potensi Kerentanan | Solusi & Strategi Mitigasi |
| :--- | :--- | :--- |
| **Penyimpanan File Tidak Aman** | Penyerang mengunggah file berbahaya (PHP/shell/script eksekutabel) untuk mengeksekusi *Remote Code Execution (RCE)*. | **Penyimpanan Terisolasi**: File disimpan di storage privat (`storage/app/private/`) di luar direktori publik `public/`. Whitelist ketat pada MIME-type (`pdf, doc, docx, xls, xlsx, ppt, pptx, zip, png, jpg, jpeg`). File diacak namanya (*hash name*) saat disimpan di disk fisik. |
| **Insecure Direct Object References (IDOR)** | Peserta A mengunduh dokumen rahasia milik Peserta B atau pembimbing lain. | **Otorisasi Ketat pada Endpoint**: Endpoint download `/peserta/tugas/{tugas}/lampiran` memeriksa apakah `$tugas->penempatan->peserta_id === auth()->id()`. Jika tidak cocok, request langsung diputus dengan `abort(403)`. |
| **Open Redirect / Malicious Link** | Input tautan diisi URL berbahaya atau javascript pseudo-protocol (`javascript:alert(1)`). | **Validasi Protokol**: Input divalidasi dengan aturan `url` Laravel yang hanya menerima skema `http://` dan `https://`. Render tautan di Blade menggunakan `e()` escaping dan relasi pengaman `rel="noopener noreferrer"`. |
| **Denial of Service (DoS) via File Bloat** | Pembimbing/penyerang mengunggah file sangat besar untuk menghabiskan kuota disk server. | **Kapasitas Dibatasi**: Validasi aturan `max:10240` (10 MB). Streaming download menggunakan chunked response Laravel (`Storage::download()`) agar memori RAM PHP tidak melonjak (*low memory footprint*). |
| **Kompatibilitas Tugas Lama** | Tugas lama yang dibuat tanpa lampiran/link mengalami galat fatal (*null pointer*). | **Null-Safe Blades & Accessors**: Seluruh kolom baru bersifat `nullable()`. Blade view dibungkus pengecekan `@if($tugas->file_lampiran)` dan `@if($tugas->link_referensi)` sehingga data tugas lama tetap berjalan 100% mulus. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Migration penambahan kolom tabel `tugas` berhasil dijalankan tanpa error.
- [x] Model `Tugas` memiliki accessor ekstensi, format ukuran, dan warna badge dokumen.
- [x] Validasi `TugasRequest` menolak file di luar format yang diizinkan dan menolak file berukuran $> 10\text{ MB}$.
- [x] Pembimbing dapat menginput link referensi dan mengunggah berkas acuan (PDF/Word/Excel/PPT/ZIP) saat memberikan tugas baru.
- [x] Tombol `+ Beri Tugas Baru` di Hero Dashboard membuka modal pop-up interaktif secara instan tanpa perlu beralih ke halaman lain.
- [x] Kartu tugas di Tab 1 Dashboard menampilkan badge lampiran berkas dan link eksternal yang estetik sesuai palet Sleman Blue Figma.
- [x] Peserta di portal peserta dapat melihat instruksi lengkap, membuka tautan eksternal, dan mengunduh berkas acuan resmi.
- [x] Hak akses unduhan privat terproteksi dari celah IDOR (403 Forbidden untuk pihak yang tidak berhak).
- [x] Seluruh pengujian otomatis di `RichTaskAssignmentTest` dan suite pembimbing eksisting lulus 100% (**Green**, 40 passed).
- [x] Verifikasi visual melalui browser subagent menunjukkan layout rapi, modern, dan responsif.

---

## 9. Catatan Perubahan & Rencana Pengembangan Masa Depan

- **Catatan Pengerjaan**: 
  - Seluruh fase implementasi (Fase 1 s/d Fase 5) telah selesai dan diverifikasi secara menyeluruh.
  - Penambahan kolom: `link_referensi`, `file_lampiran`, `nama_file_lampiran`, `ukuran_file_lampiran`.
  - Aksesor helper pada `Tugas.php`: `ekstensi_lampiran`, `ukuran_format`, `badge_color_lampiran`.
  - Keamanan zero-IDOR pada endpoint download berkas privat di sisi pembimbing dan peserta.
  - Elevasi visual Dashboard Pembimbing dengan Modal Quick Task Creator berbasis Figma Sleman Blue `#0641CC`.
  - Pengujian otomatis: 40 tests lulus (196 assertions) pada `RichTaskAssignmentTest`, `PembimbingDashboardEnhancementTest`, `PembimbingFullJobDeskTest`, `PembimbingPortalFeatureTest`, `TugasLogbookTest`, `ScoringAndCompletionTest`.
  - Visual verification di Chrome: Modal dan kartu tugas terverifikasi tampil presisi dan responsif.
- **Saran Lanjutan (Out of Scope)**:
  - Integrasi in-app PDF preview modal untuk membaca dokumen acuan tugas langsung di peramban tanpa harus mengunduhnya terlebih dahulu.
  - Multi-file attachment per tugas jika di kemudian hari pembimbing memerlukan lebih dari 1 file panduan terpisah per penugasan.
