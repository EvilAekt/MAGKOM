# Plan: Implementasi Backend Gelombang 2 (Akun, Pendaftaran, Berkas Privat & Kuota Atomik)

- **Tanggal**: 2026-10-08
- **Status**: Completed (Lulus Semua Pengujian)
- **Author**: Antigravity (`web-implementation`)
- **Referensi**: [`01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md), [`02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md), [`03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md), [`04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](file:///g:/MAGKOM/docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md)

---

## 1. Tujuan
Mengimplementasikan fungsionalitas inti Gelombang 2 pada portal MagKom:
1. **F-02 & Profil Peserta**: Pendaftaran akun calon peserta (`User` role `peserta`), pengisian data profil lengkap (`PesertaProfil`), verifikasi email, dan perlindungan isolasi profil.
2. **F-03 & Berkas Persyaratan**: Master jenis dokumen dinamis (`JenisDokumen`), pengunggahan berkas fisik ke disk privat dengan validasi magic bytes, penyimpanan checksum SHA-256, dan tautan unduh bertanda tangan sementara (*temporary signed URL*) 5 menit.
3. **F-04 & Seleksi Verifikasi Admin**: Alur review administrasi pendaftaran, permintaan perbaikan berkas, penolakan dengan catatan, dan penanganan status *Waiting List*.
4. **F-07, Transaksi Atomik & Penempatan**: Alur penerimaan peserta dengan alokasi kuota atomik anti-*race-condition* (`QuotaAllocationService`), penentuan pembimbing dan unit bidang, pembuatan catatan penugasan resmi (`Penempatan`), serta pencatatan audit trail menyeluruh.
5. **Penegakan Aturan Kunci**:
   - **BR-17**: Satu peserta hanya boleh memiliki 1 pendaftaran aktif pada satu waktu.
   - **BR-05 / AC-05-4**: Eksekusi atomik `lockForUpdate()` agar tidak terjadi kuota minus (*double claim*).
   - **BR-08**: Perhitungan tanggal mulai (tanggal 1) dan tanggal akhir berdasarkan durasi.

---

## 2. Rincian Komponen & File

| Komponen | File Path | Aksi | Deskripsi |
| :--- | :--- | :---: | :--- |
| **Migrations** | `database/migrations/2026_10_08_000004_create_peserta_profil_table.php` | Create | Tabel profil biodata peserta (FK user, NIM/NISN, institusi, jurusan, jenjang, no_hp) |
| | `database/migrations/2026_10_08_000005_create_jenis_dokumen_table.php` | Create | Master jenis berkas persyaratan (kode unik per jenjang) |
| | `database/migrations/2026_10_08_000006_create_pendaftaran_table.php` | Create | Pengajuan pendaftaran magang dengan check durasi 1..6 dan indeks |
| | `database/migrations/2026_10_08_000007_create_dokumen_table.php` | Create | Tabel berkas fisik privat dengan path, mime, ukuran, sha256 hash |
| | `database/migrations/2026_10_08_000008_create_penempatan_table.php` | Create | Catatan penempatan magang aktif dengan check constraints integritas jalur |
| **Domain Exceptions** | `app/Exceptions/Domain/KuotaPenuhException.php` | Create | Exception ketika kuota tidak mencukupi |
| | `app/Exceptions/Domain/StatusSudahBerubahException.php` | Create | Exception bila status pendaftaran mengalami conflict |
| | `app/Exceptions/Domain/PendaftaranAktifGandaException.php` | Create | Exception pelanggaran aturan BR-17 |
| **Models** | `app/Models/PesertaProfil.php` | Create | Model profil peserta dengan casts enum jenjang |
| | `app/Models/JenisDokumen.php` | Create | Model master jenis dokumen per jenjang |
| | `app/Models/Pendaftaran.php` | Create | Model pendaftaran dengan status enum & relasi |
| | `app/Models/Dokumen.php` | Create | Model berkas fisik di disk privat |
| | `app/Models/Penempatan.php` | Create | Model penempatan resmi magang |
| **Services** | `app/Services/QuotaAllocationService.php` | Create | Alokasi atomik kuota umum dan khusus dengan `lockForUpdate()` |
| | `app/Services/DocumentStorageService.php` | Create | Simpan file ke disk private, verifikasi checksum & signed download |
| | `app/Services/PlacementService.php` | Create | Penerimaan online (jalur umum) & penempatan manual jalur khusus |
| **Policies** | `app/Policies/PendaftaranPolicy.php` | Create | Hak akses lihat/kelola pengajuan pendaftaran |
| | `app/Policies/DokumenPolicy.php` | Create | Hak akses unduh berkas di disk privat |
| | `app/Policies/PenempatanPolicy.php` | Create | Hak akses view/edit penempatan |
| **Requests** | `app/Http/Requests/PesertaProfilRequest.php` | Create | Validasi biodata peserta |
| | `app/Http/Requests/PendaftaranRequest.php` | Create | Validasi pembuatan pendaftaran (cek status bulan & BR-17) |
| | `app/Http/Requests/UnggahDokumenRequest.php` | Create | Validasi tipe file berkas dan batas ukuran MB |
| | `app/Http/Requests/KeputusanPendaftaranRequest.php` | Create | Validasi keputusan admin (terima, tolak, minta perbaikan) |
| | `app/Http/Requests/PenempatanManualRequest.php` | Create | Validasi penempatan manual slot khusus F-06 / F-07 |
| **Controllers** | `app/Http/Controllers/Peserta/ProfilController.php` | Create | Lengkapi/edit data diri peserta |
| | `app/Http/Controllers/Peserta/PendaftaranController.php` | Create | Pembuatan pengajuan & submit pendaftaran lengkap |
| | `app/Http/Controllers/Peserta/BerkasController.php` | Create | Unggah & unduh berkas via signed URL |
| | `app/Http/Controllers/Admin/PendaftaranController.php` | Create | Verifikasi berkas, minta revisi, tolak, & terima online |
| | `app/Http/Controllers/Admin/WaitingListController.php` | Create | Daftar antrean waiting list |
| | `app/Http/Controllers/Admin/PenempatanController.php` | Create | Manajemen penempatan dan alokasi manual khusus |
| **Seeders** | `database/seeders/JenisDokumenSeeder.php` | Create | Master dokumen wajib untuk SMK, D3, dan S1 |
| **Tests** | `tests/Feature/QuotaConcurrencyTest.php` | Create | Pengujian atomik alokasi kuota & pencegahan race condition |
| | `tests/Feature/PendaftaranPesertaTest.php` | Create | Pengujian alur pendaftaran, BR-17, upload berkas, & download URL |

---

## 3. Checklist Eksekusi
- [x] Buat 5 migration database: `peserta_profil`, `jenis_dokumen`, `pendaftaran`, `dokumen`, `penempatan`.
- [x] Buat Domain Exceptions (`KuotaPenuhException`, `StatusSudahBerubahException`, `PendaftaranAktifGandaException`).
- [x] Buat 5 model Eloquent dengan casts enum, accessor, mutator, dan relasi yang lengkap.
- [x] Implementasikan `QuotaAllocationService` dengan transaksi `lockForUpdate()` & `PlacementService`.
- [x] Implementasikan `DocumentStorageService` untuk disk `private` dan *temporary signed URL*.
- [x] Buat Authorization Policies (`PendaftaranPolicy`, `DokumenPolicy`, `PenempatanPolicy`).
- [x] Buat Form Requests dengan validasi aturan bisnis dan batas ukuran MB dari `Pengaturan`.
- [x] Buat Controller layer untuk Peserta dan Admin dengan integrasi `AuditService`.
- [x] Daftarkan rute di `routes/web.php`.
- [x] Buat dan jalankan seeder `JenisDokumenSeeder`.
- [x] Tulis dan jalankan seluruh unit & feature test Gelombang 2 (`QuotaConcurrencyTest`, `PendaftaranPesertaTest`).
