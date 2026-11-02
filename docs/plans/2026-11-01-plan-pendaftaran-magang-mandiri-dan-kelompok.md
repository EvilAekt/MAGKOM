# Rencana Kerja: Implementasi Pendaftaran Magang 2 Tipe (Mandiri & Kelompok)

- **Tanggal**: 2026-11-01
- **Status**: Draft / Menunggu Persetujuan
- **Dokumen Referensi**: 
  - `resources/views/auth/register.blade.php`
  - `app/Http/Controllers/Auth/AuthController.php`
  - `resources/views/peserta/dashboard/partials/_header.blade.php`
  - `resources/views/layouts/peserta.blade.php`
  - `resources/views/peserta/pendaftaran/detail.blade.php`

---

## 1. Tujuan
Menyediakan fitur pemilihan tipe pendaftaran magang:
1. **Mandiri**: Alur perorangan seperti sistem yang sudah berjalan.
2. **Kelompok**: 
   - Pendaftaran dilakukan oleh 1 akun atas nama ketua kelompok.
   - Mengisi **Nama Kelompok** dan daftar **Anggota Tim** (2 hingga 5 orang: Nama, NIM/NISN, No. HP/WhatsApp, Email).
   - Tampilan Dashboard Peserta menampilkan **Nama Kelompok** sebagai nama utama (dengan keterangan ketua & anggota).
   - Berkas pendaftaran diunggah secara kolektif (1 surat pengantar & berkas gabungan atas nama kelompok).

---

## 2. Asumsi & Kebutuhan Teknis (Berdasarkan Klarifikasi User)
- **Data Anggota Kelompok**: Nama Lengkap, NIM/NISN, No. HP/WhatsApp, dan Email aktif.
- **Batas Anggota**: Fleksibel antara 2 hingga 5 orang (1 ketua + 1 s/d 4 rekan anggota).
- **Pengunggahan Berkas**: Berkas bersifat kolektif/gabungan per kelompok (tidak memerlukan upload terpisah per orang).
- **Autentikasi & Akun**: Hanya ketua tim yang memiliki akun login (email & password ketua); rekan tim tidak perlu membuat akun terpisah.
- **Tampilan Dashboard**: Menampilkan Nama Kelompok pada banner hero dan menu profil, disertai kartu/panel daftar anggota tim.

---

## 3. Pendekatan Arsitektur & Data Model

### A. Skema Database Baru (Relasional & Normal):
1. **Tabel `kelompok_magang`**:
   - `id` (bigint unsigned, primary key)
   - `ketua_user_id` (foreign key ke `users.id`, cascade on delete)
   - `nama_kelompok` (string 150)
   - `timestamps`
2. **Tabel `kelompok_anggota`**:
   - `id` (bigint unsigned, primary key)
   - `kelompok_magang_id` (foreign key ke `kelompok_magang.id`, cascade on delete)
   - `nama` (string 255)
   - `nim_nisn` (string 50, nullable)
   - `no_hp` (string 30, nullable)
   - `email` (string 255, nullable)
   - `timestamps`
3. **Kolom pada Tabel `pendaftaran`**:
   - `tipe` (enum/string: `'mandiri'`, `'kelompok'`, default `'mandiri'`)
   - `kelompok_magang_id` (bigint unsigned, nullable, foreign key ke `kelompok_magang.id`)

### B. Model Eloquent:
- Model `App\Models\KelompokMagang`: Relasi `belongsTo(User::class, 'ketua_user_id')`, `hasMany(KelompokAnggota::class)`.
- Model `App\Models\KelompokAnggota`: Relasi `belongsTo(KelompokMagang::class)`.
- Model `App\Models\User`: Helper `kelompokMagang()`, dan accessor untuk display name (jika kelompok gunakan nama kelompok).
- Model `App\Models\Pendaftaran`: Relasi `belongsTo(KelompokMagang::class)`.

### C. Alur Form Registrasi (`register.blade.php`):
- Pilihan Segmented Toggle: **[ Mandiri ]** vs **[ Kelompok ]**.
- Saat **Kelompok** dipilih:
  - Input teks: `nama_kelompok` (Wajib).
  - Container dinamis Anggota Tim:
    - Baris anggota 1 (otomatis ketua).
    - Baris anggota 2 (wajib).
    - Baris anggota 3, 4, 5 (opsional, ada tombol "+ Tambah Anggota" dan "Hapus").
    - Setiap baris anggota memiliki field: Nama, NIM/NISN, No. HP, Email.

### D. Penyesuaian Dashboard Peserta:
- Hero banner di [_header.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_header.blade.php):
  - Jika tipe kelompok: `Halo, [Nama Kelompok]` dengan badge `Pendaftaran Kelompok (X Anggota)`.
- Tampilan rincian anggota kelompok pada dashboard (panel tim).

---

## 4. Perubahan File
| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `database/migrations/2026_11_01_100000_create_kelompok_magang_tables.php` | Create | Migrasi tabel `kelompok_magang`, `kelompok_anggota`, dan kolom di `pendaftaran`. |
| `app/Models/KelompokMagang.php` | Create | Model Eloquent untuk data kelompok magang. |
| `app/Models/KelompokAnggota.php` | Create | Model Eloquent untuk anggota kelompok. |
| [app/Models/User.php](file:///d:/MAGKOM/MAGKOM/app/Models/User.php) | Modify | Tambahkan relasi `kelompokMagang()` dan accessor nama kelompok. |
| [app/Models/Pendaftaran.php](file:///d:/MAGKOM/MAGKOM/app/Models/Pendaftaran.php) | Modify | Tambahkan relasi `kelompokMagang()` dan field `tipe`. |
| [app/Http/Controllers/Auth/AuthController.php](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Auth/AuthController.php) | Modify | Validasi dan penyimpanan data kelompok & anggota saat registrasi. |
| [resources/views/auth/register.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/auth/register.blade.php) | Modify | UI toggle Mandiri/Kelompok, input nama kelompok, dan repeater anggota dinamis (2-5 orang). |
| [resources/views/peserta/dashboard/partials/_header.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_header.blade.php) | Modify | Tampilkan nama kelompok di salam pembuka hero banner. |
| [resources/views/peserta/dashboard.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard.blade.php) | Modify | Tambahkan widget kartu anggota tim jika tipe pendaftaran adalah kelompok. |
| [resources/views/peserta/pendaftaran/detail.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/pendaftaran/detail.blade.php) | Modify | Tampilkan label status kelompok & instruksi berkas kolektif. |
| `tests/Feature/PendaftaranKelompokTest.php` | Create | Feature test untuk registrasi kelompok, validasi anggota 2-5 orang, dan tampilan dashboard. |

---

## 5. Langkah Implementasi
- [x] **Langkah 1**: Buat migrasi tabel `kelompok_magang` dan `kelompok_anggota`, serta modifikasi tabel `pendaftaran`.
- [x] **Langkah 2**: Jalankan migrasi via Laragon PHP (`php artisan migrate`).
- [x] **Langkah 3**: Buat Model `KelompokMagang` dan `KelompokAnggota`, perbarui relasi pada `User` dan `Pendaftaran`.
- [x] **Langkah 4**: Perbarui `AuthController::register` untuk menangani payload pendaftaran kelompok dan penyimpanan anggotanya.
- [x] **Langkah 5**: Perbarui antarmuka `register.blade.php` dengan UI toggle mandiri/kelompok, input nama kelompok, dan script repeater anggota dinamis (2-5 orang) yang responsif dan aman.
- [x] **Langkah 6**: Sesuaikan tampilan dashboard peserta ([_header.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_header.blade.php) dan rincian tim) untuk menampilkan nama kelompok jika terdaftar sebagai tim.
- [x] **Langkah 7**: Sesuaikan view pengisian berkas ([detail.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/pendaftaran/detail.blade.php)) untuk menampilkan konteks berkas kolektif kelompok.
- [x] **Langkah 8**: Buat Feature Test otomatis (`PendaftaranKelompokTest.php`) untuk memverifikasi alur pendaftaran kelompok dan mandiri secara menyeluruh.
- [x] **Langkah 9**: Uji coba langsung di browser (`http://magkom.test/register` dan `http://magkom.test/peserta/dashboard`).

---

## 6. Strategi Testing
- **Feature Tests**:
  - `test_registrasi_mandiri_berhasil_seperti_biasa()`
  - `test_registrasi_kelompok_valid_menyimpan_nama_kelompok_dan_anggota()`
  - `test_registrasi_kelompok_gagal_jika_nama_kelompok_kosong()`
  - `test_dashboard_menampilkan_nama_kelompok_untuk_akun_kelompok()`
- **Validasi Sad Path**:
  - Submit kelompok tanpa nama kelompok -> Error validasi.
  - Submit kelompok dengan email anggota tidak valid -> Error validasi.

---

## 7. Risiko & Mitigasi
- **Risiko**: Data pendaftaran lama yang sudah berstatus mandiri tidak memiliki record kelompok.
  - **Mitigasi**: Nilai default kolom `tipe` adalah `'mandiri'`, dan `kelompok_magang_id` bernilai `null` sehingga seluruh data pendaftaran sebelumnya tetap 100% aman (*backward compatible*).
- **Risiko**: Perubahan kuota (jika 1 kelompok beranggotakan 3 orang, bagaimana dengan hitungan kuota?).
  - **Mitigasi**: Informasi jumlah anggota kelompok dicatat jelas sehingga verifikator admin melihat beban kuota riil dari kelompok tersebut saat seleksi.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Calon peserta dapat memilih antara pendaftaran Mandiri atau Kelompok di halaman register.
- [x] Pendaftaran kelompok dapat menginput Nama Kelompok dan 2–5 anggota tim (Nama, NIM/NISN, No. HP, Email).
- [x] Akun kelompok berhasil login dan dashboard menampilkan Nama Kelompok beserta rincian anggotanya.
- [x] Seluruh automated test suite (`php artisan test`) lulus 100% tanpa error regresi.
