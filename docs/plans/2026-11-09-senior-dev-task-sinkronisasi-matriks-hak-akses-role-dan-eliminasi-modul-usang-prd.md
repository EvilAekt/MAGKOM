# Task Brief Senior Developer: Sinkronisasi Matriks Hak Akses & Eliminasi Modul Usang (PRD)

Dokumen ini disusun oleh Senior Developer untuk memandu pengembang junior maupun AI model tier hemat (seperti Gemini 3.8 Flash) dalam menyelaraskan katalog permission pada modul **Hak Akses dan Role Pengguna** (`/superadmin/role`) dengan arsitektur, basis data, dan aturan bisnis MagKom PRD v1.2.

---

## 1. Latar Belakang & Identifikasi Masalah

Pada halaman `/superadmin/role`, ditemukan 3 anomali kritis yang tidak lagi mencerminkan logika sistem aktual:
1. **`Logbook Harian & Pengelolaan Tugas Proyek`**:
   - *Fakta Sistem*: MagKom menggunakan **Logbook Mingguan** (`minggu_ke` 1 s/d 12, PRD BR-09). Istilah *"Harian"* sudah usang.
2. **`Penilaian Kinerja & Pengesahan Sertifikat`**:
   - *Fakta Sistem*: Tabel `penilaian`, `penilaian_detail`, `penilaian_riwayat`, dan `rubrik` telah **DIHAPUS PERMANEN** dari sistem melalui migrasi `2026_11_09_200000_drop_penilaian_and_rubrik_tables.php`. MagKom beralih ke portofolio bimbingan (`pembimbing/laporan-kinerja`) dan penyelesaian magang ber-grace period 30 hari. Tidak ada pengesahan sertifikat digital mandiri di sistem.
3. **`Konfigurasi Parameter Sistem & Rubrik Bobot`**:
   - *Fakta Sistem*: Rubrik bobot telah dihapus. `PengaturanController` hanya mengelola 13 parameter operasional kebijakan sistem.
4. **Modul Kritis Baru Belum Terdaftar**:
   - `sistem.cadangan-database` (Pusat Cadangan & Pemulihan Basis Data / Disaster Recovery)
   - `sistem.telemetri-kesehatan` (Telemetri Kesehatan & Diagnostik Sistem)

---

## 2. Kriteria Keberhasilan (Acceptance Criteria)

- **AC-01 (Eliminasi Modul Zombie)**: Modul fiktif `penilaian.kinerja-sertifikat` dihapus dari tabel `permissions` dan `role_permissions`.
- **AC-02 (Koreksi Semantik Logbook)**: Modul bimbingan diubah menjadi `Logbook Mingguan & Manajemen Penugasan Proyek` dengan deskripsi mingguan.
- **AC-03 (Pembersihan Rubrik Parameter)**: Modul konfigurasi diubah menjadi `Konfigurasi Kebijakan & Parameter Sistem` tanpa embel-embel rubrik bobot.
- **AC-04 (Pendaftaran Mandiri & Kelompok)**: Modul pendaftaran diselaraskan mencakup pendaftaran mandiri dan rombongan kelompok (`pendaftaran.magang`).
- **AC-05 (Registrasi Modul Baru)**: Menambahkan `sistem.cadangan-database` dan `sistem.telemetri-kesehatan` ke dalam daftar modul bawaan SuperAdmin.
- **AC-06 (Proteksi Invarian SuperAdmin)**: Memastikan SuperAdmin terkunci permanen (*immutable checkbox*) pada modul-modul sistem inti (`audit-trail`, `staf.manajemen`, `konfigurasi.parameter`, `sistem.cadangan-database`, `sistem.telemetri-kesehatan`).
- **AC-07 (Quality Gate & Testing)**: Seluruh test suite PHPUnit berjalan hijau (100% lulus) dengan PHP 8.3 aktual.

---

## 3. Langkah-Langkah Eksekusi

1. **Update Seeder `database/seeders/PermissionSeeder.php`**:
   - Definisikan 13 modul definitif.
   - Bersihkan slug usang `penilaian.kinerja-sertifikat`.
   - Update transisi slug `pendaftaran.mandiri` ke `pendaftaran.magang`.
2. **Update Controller `app/Http/Controllers/SuperAdmin/RoleController.php`**:
   - Tambahkan `'sistem.cadangan-database'` dan `'sistem.telemetri-kesehatan'` pada pengecekan proteksi invarian SuperAdmin di `updateMatrix()`.
3. **Update View Blade `resources/views/superadmin/role/index.blade.php`**:
   - Tambahkan `'sistem.cadangan-database'` dan `'sistem.telemetri-kesehatan'` pada array `$isCoreSystem`.
4. **Jalankan Seeding pada Database Lokal**:
   - Eksekusi `php artisan db:seed --class=PermissionSeeder` dengan PHP 8.3.
5. **Jalankan Test Suite**:
   - Jalankan `SuperAdminPaginationTenEntriesTest`, `SuperAdminPortalVisualTest`, dan `SuperAdminMasterArchitectureTest`.

