# Rencana Kerja: Penyelarasan Header Tabel Laporan, Filter Status Keputusan, dan Penjelasan Status Nonaktif

Dokumen rencana kerja ini disusun untuk menuntaskan 4 poin penyelarasan pada antarmuka **Hub Laporan & Ekspor** ([`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php)) serta memberikan penjelasan arsitektural mengenai status *Nonaktif* berdasarkan 4 gambar yang diunggah pengguna.

---

## 1. Analisis & Evaluasi 4 Poin Permintaan

### Poin 1 (Gambar SS 1): Penyelarasan Header Kolom Jurusan Pilihan
- **Kondisi Saat Ini**:
  - Pada tabel Rekapitulasi Pendaftaran (baris 488): `<th style="width: 22%; text-align: left;">Jurusan Pilihan</th>`.
- **Evaluasi**:
  - Kata *"Pilihan"* terkesan bertele-tele. Di lingkungan administrasi MagKom, jurusan yang tertera adalah jurusan/prodi calon peserta yang diverifikasi.
- **Solusi**:
  - Ubah teks header menjadi **`Jurusan`** (`<th style="width: 22%; text-align: left;">Jurusan</th>`).

### Poin 2 (Gambar SS 2): Penyelarasan Dropdown Filter Status Keputusan
- **Kondisi Saat Ini**:
  - Filter `Status Keputusan` saat ini mengambil seluruh nilai `StatusPendaftaranEnum::cases()` (baris 440–444 di view dan baris 167 di `LaporanController.php`).
  - Akibatnya, status warisan/tidak terpakai seperti `Waiting List`, `Perlu Perbaikan`, dan `Berkas Belum Lengkap` ikut muncul di pilihan dropdown.
- **Evaluasi**:
  - Sesuai arsitektur MagKom terkini:
    - `Waiting List` telah dihapus dari sistem kuota.
    - `Perlu Perbaikan` dan `Berkas Belum Lengkap` tidak digunakan dalam alur keputusan verifikasi admin (`KeputusanPendaftaranRequest` hanya memproses `terima` dan `tolak`).
  - Alur status yang nyata ada di database pendaftaran:
    1. **Diajukan** (Berkas masuk, menunggu keputusan verifikasi).
    2. **Diterima** (Lolos verifikasi berkas & kuota).
    3. **Ditolak** (Tidak lolos verifikasi berkas).
    4. **Dibatalkan** (Dibatalkan oleh pemohon atau sistem).
- **Solusi**:
  - Batasi dan selaraskan koleksi `$statusPendaftaran` pada `LaporanController.php` hanya untuk 4 status aktif tersebut:
    ```php
    'statusPendaftaran' => [
        StatusPendaftaranEnum::Diajukan,
        StatusPendaftaranEnum::Diterima,
        StatusPendaftaranEnum::Ditolak,
        StatusPendaftaranEnum::Dibatalkan,
    ],
    ```
  - Dropdown filter kini hanya menampilkan: **Semua Status**, **Diajukan**, **Diterima**, **Ditolak**, dan **Dibatalkan**.

### Poin 3 (Gambar SS 3): Penyelarasan Header Kolom Jalur / Sumber
- **Kondisi Saat Ini**:
  - Pada tabel Rekapitulasi Penempatan (baris 750): `<th style="width: 18%; text-align: left;">Jalur / Sumber</th>`.
  - Pada label filter (baris 703): `<label ...>Jalur Penerimaan</label>`.
- **Evaluasi**:
  - Kata *"/ Sumber"* redundan karena isi datanya adalah nama jalur: `Jalur Umum` dan `Jalur Khusus`.
- **Solusi**:
  - Ubah teks header kolom menjadi **`Jalur`** (`<th style="width: 18%; text-align: left;">Jalur</th>`).
  - Selaraskan juga label filter pada baris 703 menjadi **`Jalur`** agar seragam dan rapi.

### Poin 4 (Gambar SS 4): Penjelasan Arsitektural Status Penempatan "Nonaktif"
- **Pertanyaan Pengguna**: *"gambar ss ke 4 itu kan status nonaktif ya? itu yg nonaktifkan super admin atau siapa"*
- **Jawaban & Penjelasan Sistem**:
  1. **Siapa yang Menonaktifkan?**:
     - **Sistem Otomatis (Scheduler/Cron Job Harian)** melalui service [`app/Services/MasaTenggangService.php`](file:///d:/laragon/www/MAGKOM/app/Services/MasaTenggangService.php) (aturan bisnis **BR-07** di PRD).
  2. **Bagaimana Alurnya?**:
     - **Langkah 1 (Selesai Magang)**: Pembimbing lapangan atau admin menyelesaikan masa magang peserta ketika durasi magang berakhir. Status penempatan berubah menjadi **`Selesai Magang` (`selesai`)**.
     - **Langkah 2 (Masa Tenggang / Grace Period)**: Peserta diberikan masa tenggang (umumnya 14 hari) untuk login mengunduh sertifikat nilai dan menyelesaikan administrasi akhir.
     - **Langkah 3 (Otomatis Nonaktif)**: Setelah tanggal masa tenggang (`tgl_nonaktif`) terlampaui/kedaluwarsa, command scheduler otomatis mengubah status penempatan menjadi **`Nonaktif` (`nonaktif`)** dan akun peserta dinonaktifkan (`status_aktif = false`) demi keamanan.
  3. **Peran Super Admin**:
     - Sesuai aturan keamanan RBAC ([`docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`](file:///d:/laragon/www/MAGKOM/docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md)), Super Admin berkedudukan sebagai *Read-Only Auditor*. Super Admin tidak mengubah status operasional harian peserta, melainkan memantau dan mengarsipkan data di modul Arsip Peserta.
  4. **Kebutuhan Filter di Laporan**:
     - Opsi `Nonaktif` tetap sangat dibutuhkan di laporan penempatan untuk merekapitulasi seluruh riwayat peserta yang sudah lulus dan masa tenggangnya sudah selesai diarsipkan.

---

## 2. Rencana Perubahan Berkas

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php) | **Modify** | 1. Baris 488: Ganti header `Jurusan Pilihan` menjadi `Jurusan`.<br>2. Baris 703: Ganti label filter `Jalur Penerimaan` menjadi `Jalur`.<br>3. Baris 750: Ganti header `Jalur / Sumber` menjadi `Jalur`. |
| [`app/Http/Controllers/Admin/LaporanController.php`](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/Admin/LaporanController.php) | **Modify** | Baris 167: Batasi array `$statusPendaftaran` hanya pada status aktif: `Diajukan`, `Diterima`, `Ditolak`, dan `Dibatalkan` (menghapus `waiting_list`, `perlu_perbaikan`, `berkas_belum_lengkap`). |
| [`tests/Feature/AdminLaporanDanArsipTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminLaporanDanArsipTest.php) | **Modify** | Pastikan test filter laporan dan assertion visual mencerminkan status yang telah diselaraskan. |

---

## 3. Langkah-Langkah Implementasi Bertahap

- [x] **Fase 1: Selaraskan Header Kolom & Label Filter di View Blade**
  - [x] 1.1 Edit `resources/views/admin/laporan/index.blade.php`: Ganti `Jurusan Pilihan` menjadi `Jurusan`.
  - [x] 1.2 Edit `resources/views/admin/laporan/index.blade.php`: Ganti `Jalur / Sumber` menjadi `Jalur` pada header tabel.
  - [x] 1.3 Edit `resources/views/admin/laporan/index.blade.php`: Ganti label filter `Jalur Penerimaan` menjadi `Jalur`.

- [x] **Fase 2: Selaraskan Dropdown Filter Status Keputusan di Controller**
  - [x] 2.1 Edit `app/Http/Controllers/Admin/LaporanController.php`: Saring `$statusPendaftaran` agar hanya mengirim enum `Diajukan`, `Diterima`, `Ditolak`, dan `Dibatalkan`.

- [x] **Fase 3: Verifikasi & Quality Gates**
  - [x] 3.1 Jalankan feature test laporan: `vendor/bin/phpunit tests/Feature/AdminLaporanDanArsipTest.php` (8 passed).
  - [x] 3.2 Jalankan Laravel Pint: `vendor/bin/pint --test` (passed).
  - [x] 3.3 Jalankan Vite build: `npm run build` (sukses).
  - [x] 3.4 Jalankan full PHPUnit suite: `vendor/bin/phpunit` (719 passed, 0 failures).

---

## 4. Kriteria Selesai (Definition of Done)

- [x] Header tabel pendaftaran menampilkan `Jurusan` (tanpa "Pilihan").
- [x] Dropdown status keputusan hanya menampilkan status alur aktif (`Diajukan`, `Diterima`, `Ditolak`, `Dibatalkan`), bersih dari `Waiting List` dan `Perlu Perbaikan`.
- [x] Header tabel penempatan dan label filter menampilkan `Jalur` (tanpa "/ Sumber").
- [x] Pengguna memahami alur penonaktifan peserta berdasarkan sistem masa tenggang otomatis (BR-07).
- [x] 719 test suite PHPUnit tetap 100% hijau.

