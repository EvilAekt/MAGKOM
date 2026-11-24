# Plan: Audit Komprehensif, Penyelarasan Alur, dan Verifikasi Master Data Jurusan (Admin)

Dokumen ini disusun untuk merealisasikan instruksi pengujian dan verifikasi logika sistem pada fitur **Master Data Jurusan** (`/admin/jurusan`).

---

## 1. Analisis Alur Penggunaan (User Journey) Admin Data Jurusan

### 1.1 Alur 1: Melihat Ringkasan & Data Master Jurusan
1. **Melihat Indikator Statistik**:
   - Admin membuka `/admin/jurusan`.
   - Admin melihat 4 indikator utama:
     - `TOTAL JURUSAN`: Total seluruh program studi yang terdaftar.
     - `MAHASISWA / PT`: Jumlah jurusan dengan kategori jenjang D3/D4/S1.
     - `SMK / VOKASI`: Jumlah jurusan keahlian SMK/MAK.
     - `STATUS AKTIF`: Jumlah jurusan yang berstatus aktif dan dapat dipilih oleh calon peserta.
2. **Melihat Tabel Master**:
   - Menampilkan kolom: *Nama Jurusan* (dengan ID), *Kategori Pendidikan* (badge ungu untuk SMK, badge biru untuk Perguruan Tinggi), *Status* (Aktif/Nonaktif), dan *Aksi* (Edit & Nonaktifkan/Aktifkan).
   - Terdapat pagination 10 baris per halaman.

### 1.2 Alur 2: Menambah Program Studi / Jurusan Baru
1. **Membuka Form Tambah**:
   - Admin mengklik tombol `+ Tambah Jurusan Baru` di hero banner.
   - Modal dialog `modalTambahJurusan` terbuka.
2. **Validasi & Penyimpanan**:
   - Admin memasukkan `nama_jurusan` dan memilih `kategori` (`smk` atau `mahasiswa`).
   - Sistem melakukan sanitasi `trim()` dan memvalidasi keunikan nama jurusan.
   - Status awal otomatis diset `aktif = true`.
   - Sistem merekam riwayat perubahan ke tabel `audit_log` (`TipeAuditEnum::Aksi`, entitas `jurusan`).
   - Sistem meng-invaliasi cache jurusan aktif (`Jurusan::CACHE_KEY_AKTIF`).
   - Admin diarahkan kembali dengan flash message sukses.

### 1.3 Alur 3: Mengubah Data Program Studi / Jurusan
1. **Membuka Form Edit**:
   - Admin mengklik tombol `Edit` pada baris jurusan.
   - Modal dialog `modalEditJurusan` terbuka dengan data nama dan kategori terisi otomatis sesuai baris yang dipilih.
2. **Validasi & Pembaruan**:
   - Admin mengubah nama atau kategori.
   - Sistem memvalidasi nama unik (mengabaikan ID jurusan yang sedang diedit).
   - Sistem mencatat nilai lama dan nilai baru ke `audit_log`.
   - Cache jurusan aktif diperbarui.
   - Admin diarahkan kembali dengan pesan sukses.

### 1.4 Alur 4: Sakelar Status (Toggle Aktif / Nonaktif) & Aturan Bisnis BR-12
1. **Membuka Konfirmasi Toggle**:
   - Admin mengklik tombol `Nonaktifkan` atau `Aktifkan`.
   - Modal konfirmasi `modalKonfirmasiToggleJurusan` terbuka dengan rincian nama jurusan dan peringatan konsekuensi.
2. **Penegakan Aturan Bisnis BR-12 (Saat Menonaktifkan)**:
   - **Guard 1 (Kuota Aktif)**: Sistem memeriksa apakah ada entri kuota aktif (`kuota.aktif = true` dan `jumlah > 0`) yang terikat pada jurusan ini. Jika ada, penonaktifan ditolak.
   - **Guard 2 (Pendaftaran Aktif)**: Sistem memeriksa apakah ada pendaftaran aktif (`Diajukan`, `WaitingList`, `PerluPerbaikan`, `BerkasBelumLengkap`, `Diterima`) yang memilih jurusan ini baik melalui kuota maupun profil peserta. Jika ada, penonaktifan ditolak.
   - **Guard 3 (Penempatan Magang Aktif)**: Sistem memeriksa apakah ada peserta magang aktif (`Penempatan::status = 'aktif'`) yang terkait jurusan ini, baik melalui relasi kuota reguler maupun profil peserta (termasuk peserta magang jalur khusus kemitraan). Jika ada, penonaktifan ditolak.
3. **Pengaktifan Kembali**:
   - Jika jurusan dalam keadaan nonaktif dan tidak ada pelanggaran aturan, status diubah menjadi aktif kembali dan dicatat ke `audit_log`.

### 1.5 Alur 5: Keamanan RBAC & Isolasi SuperAdmin Read-Only (SEC-12)
1. SuperAdmin yang mengakses `/admin/jurusan` disajikan banner informatif `Mode Lihat Saja (Read-Only)`.
2. Seluruh tombol mutasi disembunyikan di antarmuka.
3. Percobaan request HTTP langsung (POST, PUT, PATCH) oleh SuperAdmin ditolak dengan status `403 Forbidden`.

---

## 2. Rincian Temuan & Perbaikan

1. **Bug Penegakan Guard Penempatan Aktif pada `toggleAktif`**:
   - *Masalah*: Guard nomor 3 di `JurusanController::toggleAktif` hanya memeriksa `Penempatan::whereHas('kuota', fn($q) => $q->where('jurusan_id', $jurusan->id))`. Peserta magang aktif jalur khusus (yang `kuota_id`-nya null) tidak terdeteksi meskipun terdaftar pada jurusan tersebut!
   - *Solusi*: Perluas query guard penempatan aktif untuk mencakup relasi `orWhereHas('peserta.profil', fn($qp) => $qp->where('jurusan_id', $jurusan->id))` sehingga seluruh peserta aktif (reguler maupun jalur khusus) terlindungi oleh BR-12.
2. **Pembuatan Form Request `JurusanRequest`**:
   - *Solusi*: Buat `app/Http/Requests/JurusanRequest.php` untuk memisahkan validasi dan otorisasi dari controller, menerapkan `prepareForValidation` untuk sanitasi `trim()`, dan memastikan SuperAdmin diblokir dari mutasi data.
3. **Penyempurnaan Proteksi SEC-12 pada `toggleAktif`**:
   - *Solusi*: Tambahkan pengecekan otorisasi `isAdmin()` pada `toggleAktif` untuk mencegah eksekusi PATCH oleh SuperAdmin.
4. **Penyempurnaan Fleksibilitas Filter pada `index()`**:
   - *Solusi*: Tambahkan penanganan parameter query `q` (pencarian nama), `kategori`, dan `status` secara graceful di `index()` dengan pagination query string preserved.

---

## 3. Rencana Pengujian Otomatis (Test Suite)

Buat test suite `tests/Feature/AdminJurusanKonektivitasEndToEndTest.php` yang mencakup:
1. `test_admin_dapat_melihat_halaman_master_jurusan_dan_metriknya()`
2. `test_admin_dapat_mencari_dan_memfilter_data_jurusan()`
3. `test_admin_dapat_menambah_jurusan_baru()`
4. `test_validasi_menolak_nama_jurusan_duplikat()`
5. `test_validasi_menolak_kategori_jurusan_tidak_valid()`
6. `test_admin_dapat_mengubah_nama_dan_kategori_jurusan()`
7. `test_admin_dapat_toggle_aktif_nonaktif_jurusan_tanpa_keterkaitan()`
8. `test_penonaktifan_jurusan_ditolak_jika_ada_alokasi_kuota_aktif_br_12()`
9. `test_penonaktifan_jurusan_ditolak_jika_ada_pendaftaran_aktif_br_12()`
10. `test_penonaktifan_jurusan_ditolak_jika_ada_penempatan_aktif_reguler_maupun_khusus_br_12()`
11. `test_superadmin_hanya_mode_lihat_saja_dan_dilarang_menambah_atau_mengubah_jurusan_sec_12()`

---

## 4. Quality Gates
1. Jalankan PHPUnit test suite baru dan suite-suite regresi terkait.
2. Jalankan `vendor/bin/pint --test` pada file yang diubah.
3. Jalankan `vendor/bin/phpstan analyse --memory-limit=1G` pada file yang diubah.
4. Jalankan `npm run build` untuk memverifikasi asset frontend.

