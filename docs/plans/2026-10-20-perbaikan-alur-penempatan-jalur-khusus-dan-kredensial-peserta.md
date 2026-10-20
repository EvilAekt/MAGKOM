# Rencana Kerja: Perbaikan Alur Penempatan Peserta Jalur Khusus, Otomatisasi Kredensial Login, dan Penyelarasan Filter Bulan Magang

**Tanggal Pembuatan**: 20 Oktober 2026  
**Status**: ✅ Selesai Diimplementasikan  
**Branch Target**: `test-1`  

---

## 1. Tujuan
Menyempurnakan dan memperbaiki alur penempatan peserta **Jalur Khusus (Kemitraan/Slot Khusus)** di Dashboard Admin Diskominfo agar:
1. **Pendaftaran Peserta Baru Jalur Khusus Tersimpan Tanpa Error**: Memperbaiki ketidaksesuaian input form antara frontend (`tgl_mulai`/`tgl_selesai`) dan validasi backend (`bulan_mulai`/`durasi_bulan`).
2. **Kredensial Login Peserta Baru Transparan & Siap Pakai**: Akun peserta baru yang dibuatkan oleh admin memiliki kata sandi awal terstandar (`Magkom2026!`) yang ditampilkan langsung di notifikasi sukses, sehingga admin dapat langsung membagikan informasi login kepada peserta magang.
3. **Penyelarasan Filter Bulan Magang**: Memperbarui query filter `bulan_magang_id` di tabel Penempatan Peserta agar peserta Jalur Khusus (yang tidak memiliki pendaftaran online umum) tetap tersaring secara akurat berdasarkan bulan mulai magangnya.
4. **Peningkatan UX Penanganan Error Form**: Jika input form belum lengkap atau email sudah terdaftar, modal tetap terbuka dan menampilkan pesan kesalahan yang jelas di dalam popup.

---

## 2. Asumsi & Rujukan Desain

1. **Alur Bisnis Jalur Khusus**:
   - Peserta Jalur Khusus tidak melalui pendaftaran online umum (tidak upload berkas mandiri).
   - Penempatan dibuat dengan `jalur = 'khusus'`, `sumber = 'manual'`, dan `pendaftaran_id = null`.
   - Peserta langsung berstatus `aktif` dan langsung terhubung dengan Pembimbing Lapangan dan Bidang Dinas yang dipilih admin.
2. **Keamanan Kredensial (SEC-02, BR-15)**:
   - Kata sandi awal peserta baru diatur secara aman dan terstandar: `Magkom2026!`.
   - Flag `must_change_password = true` tetap aktif sehingga saat peserta pertama kali login, sistem mewajibkan peserta mengganti kata sandi ke kata sandi pribadinya melalui halaman `password.ganti`.
3. **Kompatibilitas Tanggal**:
   - Form mendukung fleksibilitas: jika admin menginput tanggal mulai dan durasi, atau tanggal mulai dan tanggal selesai, sistem backend secara otomatis mengonversi dan menghitung rentang periode magang secara presisi menggunakan `PeriodeMagang::hitung()`.

---

## 3. Evaluasi Opsi Pendekatan Teknis

| Parameter | Opsi 1: Otomatisasi Input via `prepareForValidation` + Password Default Standar [Rekomendasi] | Opsi 2: Rombak Total Form Modal Menjadi Dropdown Bulan Saja |
| :--- | :--- | :--- |
| **Deskripsi** | Menjaga fleksibilitas input tanggal di form, menambahkan normalisasi di `prepareForValidation()` pada `PenempatanManualRequest`, menetapkan password awal `Magkom2026!`, dan menampilkan kredensial di flash alert. | Mengubah form modal agar hanya memilih `bulan_magang_id` dan `durasi_bulan` (dropdown angka 1-6), menghilangkan input date picker. |
| **Kelebihan** | 1. **Backward-compatible**: Tidak merusak API atau form yang sudah ada.<br>2. Admin memiliki kendali tanggal mulai aktual (misal magang mulai tanggal 15 atau tanggal 1).<br>3. Peserta langsung mendapat kata sandi awal yang jelas dan mudah diinformasikan. | Form frontend lebih sederhana. |
| **Kekurangan** | Memerlukan sedikit logika kalkulasi tanggal di form request. | Mengurangi fleksibilitas admin jika kemitraan khusus memiliki tanggal mulai spesifik di luar kalender bulan magang reguler. |
| **Keputusan** | **DIPILIH (Opsi 1)**. Lebih fleksibel, tahan error (resilient), dan memberikan kenyamanan maksimal bagi Admin. | Ditolak karena kurang fleksibel untuk kebutuhan MoU kemitraan dinas. |

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Http/Requests/PenempatanManualRequest.php` | `Modify` | Tambahkan `prepareForValidation()` untuk mengonversi `tgl_mulai` & `tgl_selesai` menjadi `bulan_mulai` & `durasi_bulan` secara otomatis jika salah satu format dikirimkan. Buat aturan validasi fleksibel. |
| `app/Services/PlacementService.php` | `Modify` | Di method `tempatkanPesertaBaruManual`: gunakan kata sandi default `Magkom2026!` (atau password dari input jika disediakan), set `must_change_password = true`, dan kembalikan plain password dalam response/array untuk ditampilkan ke admin. |
| `app/Http/Controllers/Admin/PenempatanController.php` | `Modify` | 1. Di `storeManual`: teruskan plain password ke flash message session (`password_baru`, `email_baru`, `nama_peserta`).<br>2. Di `index`: perbaiki query filter `bulan_magang_id` agar mencocokkan `tgl_mulai` penempatan jalur khusus dengan bulan magang yang dipilih. |
| `resources/views/admin/penempatan/index.blade.php` | `Modify` | 1. Tambahkan banner info kredensial di modal/toast notifikasi saat penempatan peserta baru berhasil.<br>2. Tambahkan input durasi bulan & sinkronisasi field `bulan_mulai` di form modal penempatan manual.<br>3. Tambahkan penanganan pesan error validation di dalam modal popup dan buka modal otomatis jika terjadi error (`@if($errors->any())`). |
| `tests/Feature/AdminPenempatanJalurKhususTest.php` | `Create` | Test suite otomatis untuk menguji: pendaftaran peserta baru jalur khusus, kredensial password awal, kemunculan di tabel penempatan, dan penyaringan filter bulan magang. |

---

## 5. Langkah Implementasi Terstruktur

- [x] **Fase 1: Backend - Sinkronisasi Request & Password Default**
  - [x] Di `app/Http/Requests/PenempatanManualRequest.php`:
    - Tambahkan method `prepareForValidation()`:
      - Jika ada `tgl_mulai` tapi `bulan_mulai` kosong: isi `bulan_mulai` dari tanggal 1 bulan `tgl_mulai`.
      - Jika ada `tgl_mulai` dan `tgl_selesai` tapi `durasi_bulan` kosong: hitung selisih bulan secara akurat (minimal 1, maksimal 6).
      - Jika `bulan_mulai` ada tapi `tgl_mulai` kosong: isi `tgl_mulai = bulan_mulai`.
    - Jadikan aturan `bulan_mulai` dan `tgl_mulai` saling melengkapi (opsional salah satu terpenuhi).
  - [x] Di `app/Services/PlacementService.php`:
    - Di `tempatkanPesertaBaruManual`: buat password awal standar `Magkom2026!` (atau dari input jika admin mengisi).
    - Simpan dan cantumkan informasi kredensial pada model user & audit log.
  - [x] Di `app/Http/Controllers/Admin/PenempatanController.php`:
    - Di `storeManual`: tambahkan flash session kredensial (`success` memuat nama, email, dan kata sandi awal).
    - Di `index`: perbarui filter `bulan_magang_id` agar mencakup penempatan jalur khusus berbasis tanggal mulai.

- [x] **Fase 2: Frontend - Tampilan Modal Penempatan & Notifikasi Kredensial**
  - [x] Di `resources/views/admin/penempatan/index.blade.php`:
    - Tambahkan opsi pilihan `durasi_bulan` (1 - 6 Bulan) pada form modal penempatan manual.
    - Tambahkan hidden input `bulan_mulai` yang otomatis sinkron dengan `tgl_mulai`.
    - Tambahkan blok notifikasi/alert khusus di bagian atas halaman saat session memiliki `password_baru` (lengkap dengan kartu salin email & kata sandi).
    - Tambahkan script deteksi error validasi: jika session memiliki error terkait modal jalur khusus, otomatis buka modal kembali (`openManualModal()`) dan tampilkan pesan error per field.

- [x] **Fase 3: Testing & Verifikasi**
  - [x] Buat test `tests/Feature/AdminPenempatanJalurKhususTest.php`:
    1. Admin dapat mendaftarkan peserta baru via modal jalur khusus dengan `tgl_mulai` & `tgl_selesai`.
    2. Akun peserta baru berhasil dibuat dengan email terverifikasi dan password awal `Magkom2026!`.
    3. Penempatan jalur khusus berhasil tersimpan dan tampil di tabel data penempatan dengan badge Jalur Khusus.
    4. Penempatan jalur khusus dapat difilter berdasarkan `bulan_magang_id`.
    5. Peserta baru dapat login dengan password awal dan diarahkan mengganti password.
  - [x] Jalankan test suite:
    - `php artisan test --filter=AdminPenempatanJalurKhususTest`
    - `php artisan test --filter=AdminPenempatanDuaTahapTest`
  - [x] Format kode dengan `vendor\bin\pint`.

---

## 6. Strategi Testing

| Kasus Uji | Jenis | Ekspektasi |
| :--- | :--- | :--- |
| Simpan Peserta Baru Jalur Khusus (input `tgl_mulai` & `tgl_selesai`) | Feature Test | HTTP 302 redirect ke index dengan session success, record user & penempatan terbentuk di database. |
| Login Peserta Baru Jalur Khusus | Feature Test | Peserta baru dapat login menggunakan email dan password `Magkom2026!`, kemudian diredirect ke `/password/ganti` (karena `must_change_password = true`). |
| Filter Bulan Magang Jalur Khusus | Feature Test | Data penempatan jalur khusus muncul saat filter `bulan_magang_id` yang sesuai dipilih. |
| Validasi Gagal (Email Duplikat) | Feature Test | HTTP 302 kembali ke halaman dengan error validasi `email.unique`. |

---

## 7. Risiko & Mitigasi

- **Risiko**: Peserta yang sudah pernah ada di database jika diinput lagi via form baru akan error unique email.
  - *Mitigasi*: Validasi `email: unique:users,email` menangkap hal ini dengan jelas dan menampilkan pesan: *"Email tersebut sudah terdaftar di sistem. Silakan pilih tab 'Peserta Terdaftar' di atas."*
- **Risiko**: Perubahan query filter penempatan mempengaruhi performa.
  - *Mitigasi*: Kolom `tgl_mulai` pada tabel `penempatan` sudah memiliki index, query `whereYear` dan `whereMonth` dieksekusi secara efisien.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Admin dapat mendaftarkan dan menempatkan peserta baru jalur khusus tanpa error validasi.
- [x] Kata sandi awal peserta baru terstandar (`Magkom2026!`) dan ditampilkan di layar admin setelah berhasil disimpan.
- [x] Peserta jalur khusus langsung muncul di tabel Penempatan Peserta dengan badge `Jalur Khusus (Mitra / MoU)`.
- [x] Peserta jalur khusus dapat disaring dengan benar menggunakan filter dropdown Bulan Magang.
- [x] Peserta baru dapat langsung login menggunakan email dan kata sandi awal tersebut di halaman login.
- [x] Seluruh pengujian otomatis (`AdminPenempatanJalurKhususTest` & regresi) lolos 100%.
