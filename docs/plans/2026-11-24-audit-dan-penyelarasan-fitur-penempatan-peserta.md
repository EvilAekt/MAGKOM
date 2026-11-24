# Plan: Audit, Analisis Keterhubungan, dan Pengujian Fitur Penempatan Peserta & Pembimbing (Admin)

**Dokumen:** `docs/plans/2026-11-24-audit-dan-penyelarasan-fitur-penempatan-peserta.md`  
**Fitur Utama:** Penempatan Peserta & Pembimbing (`/admin/penempatan`)  
**Penanggung Jawab:** AI Assistant (Antigravity) & Lead Engineer  
**Tanggal:** 24 November 2026  
**Status:** In Progress  

---

## 1. Latar Belakang & Tujuan
Fitur **Penempatan Peserta & Pembimbing** (`/admin/penempatan`) merupakan **Tahap 2** dari alur penerimaan magang di Dinas Komunikasi dan Informatika (Kominfo). 
Setelah calon peserta diverifikasi dan dinyatakan **Diterima** pada Tahap 1 (`/admin/pendaftaran`), admin bertugas:
1. Menetapkan Bidang Dinas definitif dan Pembimbing Lapangan bagi peserta perorangan maupun kelompok.
2. Memantau peserta magang yang sedang aktif, selesai, atau berasal dari jalur khusus/mitra.
3. Melakukan reassignment pembimbing (perorangan maupun bulk/massal) apabila terjadi redistribusi beban kerja bimbingan.
4. Melakukan pembatalan penempatan jika peserta mengundurkan diri dengan penegakan aturan kuota (BR-07, BR-20).

Audit ini bertujuan memastikan:
- **Keterhubungan Lintas Modul:** Terhubung sinkron dan atomik dengan Modul Pendaftaran, Manajemen Kuota, Dashboard Pembimbing (Mentee), Fitur Peserta (Logbook & Penugasan), serta Generator Surat Keterangan Diterima/Selesai.
- **Ketiadaan Bug & Edge Cases:** Memastikan filter pencarian (Nama, NIM/NISN, Kelompok, Bidang, Pembimbing), pagination, modal placement, serta eager loading bebas dari error dan N+1 query.
- **Kepatuhan Aturan Bisnis (BR):**
  - **BR-13:** Pembatasan kuota maksimal bimbingan per pembimbing (`maks_peserta_aktif_per_pembimbing`).
  - **BR-10 & BR-11:** Dukungan otomatisasi penempatan untuk seluruh anggota kelompok jika pendaftar adalah kelompok.
  - **BR-07 & BR-20:** Logika pelepasan kuota jika pembatalan dilakukan sebelum tanggal mulai magang vs kuota hangus jika sudah mulai magang.
  - **Audit Trail:** Seluruh aksi penempatan, reassignment, dan pembatalan tercatat di tabel `audit_log`.

---

## 2. Analisis Keterhubungan Lintas Modul (Cross-Module Traceability)

| Modul Terkait | Titik Hubung (Touchpoint) | Status & Logika Sistem |
| :--- | :--- | :--- |
| **Pendaftaran (`/admin/pendaftaran`)** | Query Antrean Belum Ditempatkan (`PenempatanAntreanQuery`) menyaring `pendaftaran.status = 'diterima'` dan `pendaftaran.penempatan IS NULL`. | **Valid & Sinkron**. Begitu diverifikasi "Diterima", peserta langsung muncul di tab "Belum Ditempatkan". |
| **Pembimbing (`/pembimbing/dashboard`)** | `AssignPesertaAction`, `ReassignPembimbingAction`, `BulkReassignPembimbingAction` mengaitkan `penempatan.pembimbing_id`. | **Valid & Sinkron**. Peserta otomatis muncul di dashboard pembimbing sebagai mentee aktif. Batas kuota bimbingan divalidasi ketat (BR-13). |
| **Peserta (`/peserta/logbook`, dll.)** | `penempatan.status = 'aktif'` menjadi prasyarat pembukaan akses pengisian logbook harian, presensi, dan penugasan. | **Valid & Sinkron**. Jika dibatalkan (`status = nonaktif`/`dibatalkan`), akses logbook terkunci. |
| **Manajemen Kuota (`/admin/kuota`)** | `CancelPenempatanAction` memanggil `lepasKuotaUmum()` jika pembatalan sebelum tanggal mulai, atau membiarkan kuota hangus jika pembatalan setelah tanggal mulai. | **Valid & Sinkron**. Menjamin integritas data kuota bulanan. |
| **Surat Keputusan/Diterima** | `AssignPesertaAction` menyalin relasi `file_surat_diterima` dari pendaftaran ke penempatan untuk unduhan resmi peserta. | **Valid & Sinkron**. |
| **Audit Trail (`audit_log`)** | Setiap mutasi penempatan mencatat `admin_id`, IP, user agent, data sebelum, dan data sesudah. | **Valid & Sinkron**. |

---

## 3. Identifikasi Masalah & Perbaikan Terarah (Targeted Improvements)

### 3.1 Masalah 1: Pencarian Berdasarkan NIM/NISN Tidak Bekerja pada Filter "Cari Nama"
- **Lokasi:** `app/Queries/Admin/Penempatan/PenempatanIndexQuery.php` & `app/Queries/Admin/Penempatan/PenempatanAntreanQuery.php`.
- **Temuan:** Filter `q` hanya mencari di `peserta.nama`, `peserta.email`, dan `profil.institusi`. Padahal tabel antarmuka menampilkan nomor registrasi dan `NIM: <nomor>`.
- **Solusi:** Tambahkan pencarian kolom `profil.nim_nisn` pada kedua query class.

### 3.2 Masalah 2: Pencarian Nama Tim / Kelompok pada Antrean Belum Ditempatkan
- **Lokasi:** `app/Queries/Admin/Penempatan/PenempatanAntreanQuery.php`.
- **Temuan:** Ketika admin mencari nama kelompok (misal: "Tim Web Kominfo") pada tab antrean belum ditempatkan, filter pencarian belum memeriksa `kelompokMagang.nama_kelompok` dan anggota kelompok.
- **Solusi:** Tambahkan kondisi `orWhereHas('kelompokMagang', ...)` dan pencarian anggota kelompok pada query antrean.

### 3.3 Masalah 3: Eager Loading Jurusan untuk Mencegah N+1
- **Lokasi:** `app/Queries/Admin/Penempatan/PenempatanIndexQuery.php` & `app/Queries/Admin/Penempatan/PenempatanAntreanQuery.php`.
- **Temuan:** Di view penempatan, nama jurusan ditampilkan untuk keperluan badge/rekomendasi bidang.
- **Solusi:** Eager load relasi `'peserta.profil.jurusan'` dan `'user.profil.jurusan'`.

---

## 4. Rencana Pengujian (Testing Strategy)
1. **Regression Testing:** Menjalankan 7 test suite penempatan yang sudah ada untuk memastikan 0 regresi:
   - `AdminEksporPenempatanPopoverTest`
   - `AdminPenempatanArchitectureAndSecurityTest`
   - `AdminPenempatanDuaTahapFeatureTest`
   - `AdminPenempatanFaseDuaSecurityTest`
   - `AdminPenempatanHeroStatsVisualTest`
   - `AdminPenempatanJalurKhususTest`
   - `AdminPenempatanLifecycleFeatureTest`
2. **New Test Suite:** Membuat `tests/Feature/AdminPenempatanKonektivitasEndToEndTest.php`:
   - Verifikasi pencarian NIM/NISN pada tab "Sudah Ditempatkan".
   - Verifikasi pencarian NIM/NISN dan Nama Kelompok pada tab "Belum Ditempatkan".
   - Verifikasi eager loading relasi tanpa N+1 query.
   - Verifikasi alur penempatan kelompok otomatis mencakup semua anggota kelompok.
   - Verifikasi pencatatan audit log saat penempatan dilakukan.
3. **Quality Gates:**
   - PHPStan (Level max/configured memory limit).
   - Laravel Pint (Code formatting).
   - Vite asset build (`npm run build`).

---

## 5. Kepatuhan Aturan Pengembangan (AGENTS.md)
- **PHP 8.3 aktual** digunakan untuk semua eksekusi backend.
- **Blade Read-Only**: Tidak ada perubahan markup Blade yang tidak perlu.
- **Tanpa Inline Style / Script**: Mempertahankan arsitektur CSS di `resources/css` dan JS di `resources/js`.
- **Perubahan Terfokus & Minimal**: Hanya menyentuh query/action/service yang dibutuhkan.
- **Lindungi Perubahan Lokal Pengguna**: File uncommitted milik user tetap terjaga utuh.

