# Plan: Audit dan Penyelarasan Logika Bisnis Master Unit Kerja / Bidang Dinas Kominfo

## 1. Ringkasan Eksekutif
Fitur Master Unit Kerja / Bidang (`/admin/bidang`) merupakan modul esensial dalam administrasi MagKom yang mengelola struktur organisasi unit kerja di lingkungan Dinas Komunikasi dan Informatika Kabupaten Sleman. Unit kerja ini menjadi jangkar penempatan peserta magang, penugasan pembimbing dinas, dan pemetaan relevansi jurusan/program studi.

Berdasarkan audit terhadap arsitektur kode dan visual antarmuka (`media_1795576624825_06e742c5.jpg`), alur penggunaan admin saat ini mencakup:
1. Pemantauan 4 metrik kartu statistik (Total Bidang, Bidang Aktif, Total Pembimbing, Bidang Arsip/Nonaktif).
2. Tinjauan tabel daftar bidang (Nama Bidang & ID, Deskripsi Tugas & Fungsi, Jumlah Staf bertugas, Status Aktif/Nonaktif, dan Aksi).
3. Penambahan bidang baru via modal.
4. Pembaruan data bidang (nama & deskripsi) via modal edit.
5. Pengubahan status operasional (Aktif/Nonaktif) dengan proteksi aturan bisnis penonaktifan (BR-12).

## 2. Temuan Hasil Audit Logika & Arsitektur
1. **Form Request Separation (Architecture Gap)**:
   - `BidangController@store` dan `BidangController@update` masih menggunakan `$request->validate()` secara inline di controller.
   - Belum memiliki sanitasi otomatis `trim()` untuk mencegah data string nama bidang berisi leading/trailing spaces atau spasi berlebih.
   - Solusi: Buat `app/Http/Requests/BidangRequest.php` dengan autorisasi ketat `isAdmin()`, `prepareForValidation()` untuk trim, dan pesan kesalahan berbahasa Indonesia yang jelas.
2. **Penegakan Hak Akses SuperAdmin Read-Only (SEC-12)**:
   - Pada `BidangController@toggleAktif`, perlu eksplisit guard `abort_if(! $request->user()?->isAdmin(), 403)` sebagai lapisan pertahanan mendalam (*defense in depth*) selain middleware `superadmin.readonly`.
3. **Resiliensi Query Index**:
   - `BidangController@index` diperkuat dengan kemampuan menerima filter pencarian `q` (nama_bidang/deskripsi) dan `status` (aktif/nonaktif) secara elegan.
4. **Kelengkapan Relasi Model**:
   - Model `Bidang` diperkaya relasi `penempatan(): HasMany` agar konsisten dengan `User` dan `Jurusan`.
5. **Cakupan Pengujian Otomatis (Testing Gap)**:
   - Belum ada pengujian feature end-to-end terdedikasi untuk operasi CRUD dan aturan bisnis penonaktifan Master Bidang (`tests/Feature/AdminBidangKonektivitasEndToEndTest.php`).

## 3. Rencana Implementasi Bertahap
1. **Langkah 1**: Buat Form Request `app/Http/Requests/BidangRequest.php`.
2. **Langkah 2**: Perkaya model `app/Models/Bidang.php` dengan relasi `penempatan()`.
3. **Langkah 3**: Refactor `app/Http/Controllers/Admin/BidangController.php` untuk memanfaatkan `BidangRequest`, menambahkan guard SEC-12 di `toggleAktif`, dan mendukung query filtering.
4. **Langkah 4**: Buat Automated Feature Test Suite `tests/Feature/AdminBidangKonektivitasEndToEndTest.php` yang mencakup 8 skenario pengujian komprehensif:
   - Melihat halaman master bidang & metrik agregasi.
   - Filter pencarian nama/deskripsi bidang.
   - Tambah bidang baru dengan validasi dan sanitasi trim.
   - Tolak duplikasi nama bidang.
   - Edit bidang yang sudah ada.
   - Toggle status bidang aktif <-> nonaktif.
   - Tolak penonaktifan bidang jika ada staf aktif (BR-12).
   - Tolak penonaktifan bidang jika ada peserta magang aktif (BR-12).
   - Pastikan SuperAdmin read-only (SEC-12) dan tidak bisa menambah/mengubah/toggle data bidang.
5. **Langkah 5**: Jalankan pemeriksaan quality gates:
   - PHPUnit testing.
   - PHPStan static analysis.
   - Pint formatting check.
   - Vite asset build check (`npm run build`).

## 4. Kriteria Keberhasilan (Acceptance Criteria)
- Seluruh 8 skenario pengujian otomatis lolos 100%.
- PHPStan menganalisis tanpa error (`no errors found`).
- Tidak ada perusakan pada file Blade yang melanggar batasan `AGENTS.md`.
- Seluruh mutasi data tercatat di Audit Trail dengan benar.

