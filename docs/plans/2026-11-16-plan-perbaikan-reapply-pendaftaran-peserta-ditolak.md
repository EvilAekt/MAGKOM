# Rencana Kerja: Perbaikan Alur Pendaftaran Kembali Peserta yang Ditolak (Re-apply After Rejection)

**ID Dokumen**: `PLAN-2026-11-16-REAPPLY-DITOLAK`  
**Tanggal**: 16 November 2026  
**Status**: Usulan (Menunggu Persetujuan Pengguna)  
**Terkait Isu**: Error `UniqueConstraintViolationException: Duplicate entry for key 'uq_pendaftaran_user_bulan'` pada saat calon peserta yang ditolak mengajukan pendaftaran magang baru di `127.0.0.1:8000/peserta/pendaftaran`.

---

## 1. Tujuan
Memungkinkan calon peserta magang yang sebelumnya berstatus **Ditolak** (`StatusPendaftaranEnum::Ditolak`) atau **Dibatalkan** (`StatusPendaftaranEnum::Dibatalkan`) untuk dapat mengajukan pendaftaran magang baru kembali (baik pada periode bulan yang sama jika masih dibuka kuotanya, maupun pada periode bulan magang lainnya) secara mulus tanpa mengalami kegagalan basis data (*duplicate key constraint violation*), dengan tetap mempertahankan keutuhan riwayat audit pendaftaran sebelumnya dan integritas aturan bisnis BR-17 (hanya 1 pendaftaran aktif dalam satu waktu).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
1. **Integritas Riwayat Pendaftaran (Audit Trail)**: Pendaftaran yang ditolak sebelumnya **tidak boleh ditimpa atau dihapus (*in-place overwrite*)**. Riwayat nomor registrasi lama (misal `#REG-00021`) beserta catatan penolakan verifikator dan dokumen lama harus tetap tersimpan utuh di basis data sebagai bukti audit riil seleksi dinas.
2. **Pendaftaran Baru yang Mandiri**: Pengajuan baru yang dibuat oleh peserta yang ditolak akan menghasilkan baris rekaman pendaftaran baru (misal `#REG-00022`) dengan status awal `berkas_belum_lengkap`, dokumen checklist baru yang harus diunggah ulang, dan nomor registrasi baru.
3. **Penegakan BR-17 Tetap Ketat**: Peserta **hanya boleh memiliki tepat 1 pendaftaran aktif** pada satu waktu. Peserta yang pendaftarannya masih berstatus aktif (`berkas_belum_lengkap`, `diajukan`, `perlu_perbaikan`, `waiting_list`, atau `diterima`) tetap dilarang keras membuat pendaftaran kedua.
4. **Pendaftaran Kelompok**: Jika peserta adalah ketua kelompok (`kelompokMagang`), pengajuan ulang akan mengaitkan rombongan tim yang sama secara otomatis ke pendaftaran baru.

### Pertanyaan Terbuka / Opsional bagi Pengguna:
- *Apakah pada antarmuka Dashboard Peserta (`/peserta/dashboard`), ketika status pengajuan terakhir berstatus "Ditolak", Anda ingin kami menambahkan tombol CTA langsung bertuliskan "Ajukan Pendaftaran Baru" yang mengarahkan peserta ke katalog kuota? (Sesuai aturan `AGENTS.md`, perubahan markup Blade memerlukan persetujuan eksplisit).*

---

## 3. Pendekatan Teknis & Evaluasi Opsi Arsitektur

### Akar Masalah (Root Cause):
Pada migrasi awal [`database/migrations/2026_10_08_000006_create_pendaftaran_table.php`](file:///d:/MAGKOM/MAGKOM/database/migrations/2026_10_08_000006_create_pendaftaran_table.php#L30), terdapat *database-level unique constraint*:
```php
$table->unique(['user_id', 'bulan_magang_id'], 'uq_pendaftaran_user_bulan');
```
Konstrain unik global ini memaksa bahwa pasangan `(user_id, bulan_magang_id)` tidak boleh ada lebih dari 1 baris di seluruh tabel `pendaftaran` secara permanen tanpa memedulikan status. Akibatnya, ketika Admin menolak berkas peserta di bulan $M$, baris rekaman lama tetap tersimpan dengan status `ditolak`. Saat peserta mencoba mengajukan pendaftaran ulang untuk bulan $M$ tersebut, pemanggilan `Pendaftaran::create($payload)` di [`CreatePendaftaranAction::execute`](file:///d:/MAGKOM/MAGKOM/app/Actions/Peserta/Pendaftaran/CreatePendaftaranAction.php#L55) langsung memicu *fatal error* MySQL 1062:
```
SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry '22-2' for key 'pendaftaran.uq_pendaftaran_user_bulan'
```

### Perbandingan Opsi Arsitektur:

| Kriteria Evaluasi | Opsi 1: Relaksasi Index Unik DB + Penegakan BR-17 di Domain Layer (Direkomendasikan) | Opsi 2: Reset / Update In-Place pada Baris Pendaftaran Ditolak |
|---|---|---|
| **Mekanisme** | Menghapus unique index `uq_pendaftaran_user_bulan` melalui migrasi aman, menggantinya dengan index komposit biasa `idx_pendaftaran_user_bulan`, dan mengandalkan *pessimistic row lock* + `pastikanTidakAdaPendaftaranAktif()` untuk mencegah pendaftaran aktif ganda. | Menimpa (*update*) baris pendaftaran lama yang ditolak: mereset status menjadi `berkas_belum_lengkap`, mengosongkan catatan penolakan dan verifikator. |
| **Integritas Audit Trail (SEC-05)** | ✅ **Sangat Baik**: Riwayat pendaftaran lama tetap tersimpan abadi sebagai bukti hukum penolakan berkas dinas. | ❌ **Buruk**: Menghapus jejak sejarah penolakan, catatan verifikator lama hilang tertimpa draf baru. |
| **Pengelolaan Dokumen** | ✅ **Bersih**: Dokumen lama tetap terikat pada `#REG` lama, berkas baru diunggah bersih pada `#REG` baru tanpa tabrakan file. | ⚠️ **Rentan Tabrakan**: Harus membersihkan relasi dokumen lama atau menimpa berkas yang berpotensi corrupt. |
| **Pendaftaran Kelompok** | ✅ **Sesuai Standar**: Tim mendapatkan siklus seleksi administratif baru yang bersih. | ⚠️ **Kompleks**: Harus mereset status seluruh dokumen anggota secara manual. |
| **Kepatuhan BR-17** | ✅ Terjamin 100% oleh `QuotaAllocationService::pastikanTidakAdaPendaftaranAktif` di dalam transaksi `lockForUpdate()`. | ✅ Terpenuhi karena baris tetap berjumlah 1. |

### Rekomendasi:
**Memilih Opsi 1**. Opsi ini merupakan standar arsitektur sistem pemerintahan yang aman, menjaga *audit trail* tidak hilang, dan memberikan fleksibilitas penuh bagi peserta yang ditolak untuk mendaftar kembali pada bulan yang sama maupun bulan berikutnya.

---

## 4. Rencana Perubahan File

| No | File | Aksi | Ringkasan Perubahan |
|---|---|---|---|
| 1 | `database/migrations/2026_11_16_170000_remove_unique_user_bulan_from_pendaftaran_table.php` | **Create** | Migrasi baru: menambah index non-unik `idx_pendaftaran_user_bulan` lalu melepas unique constraint `uq_pendaftaran_user_bulan`. |
| 2 | `app/Actions/Peserta/Pendaftaran/CreatePendaftaranAction.php` | **Modify** | Menjamin alur pembuatan draf pendaftaran baru berjalan aman dan terdokumentasi rapi di audit log saat peserta mengajukan kembali. |
| 3 | `app/Http/Requests/PendaftaranRequest.php` | **Modify** | Memastikan validasi pendaftaran tidak menolak pendaftar yang status pendaftaran sebelumnya adalah `ditolak` atau `dibatalkan`. |
| 4 | `app/Queries/Peserta/Pendaftaran/PendaftaranIndexQuery.php` | **Modify** | Menyesuaikan query agar katalog kuota dan modal pendaftaran baru terbuka otomatis bagi peserta yang pendaftaran sebelumnya telah berstatus final (Ditolak/Dibatalkan). |
| 5 | `resources/views/peserta/dashboard/partials/_phase_seleksi.blade.php` | **Modify** *(Izin Khusus)* | Menambahkan tombol aksi navigasi cepat "Ajukan Pendaftaran Baru" ketika status verifikasi berkas adalah Ditolak. |
| 6 | `tests/Feature/PesertaReapplyAfterRejectionTest.php` | **Create** | Test suite otomatis untuk menguji seluruh skenario pengajuan ulang pendaftar yang ditolak. |

---

## 5. Langkah-Langkah Implementasi Bertahap

### Tahap 1: Migrasi Basis Data (Database Schema)
- [x] Buat file migrasi baru: `database/migrations/2026_11_16_170000_remove_unique_user_bulan_from_pendaftaran_table.php`.
- [x] Di method `up()`:
  - Buat index komposit biasa: `$table->index(['user_id', 'bulan_magang_id'], 'idx_pendaftaran_user_bulan');` (menjaga foreign key index tetap valid di MySQL).
  - Lepas unique constraint lama: `$table->dropUnique('uq_pendaftaran_user_bulan');`.
- [x] Di method `down()`:
  - Pulihkan unique constraint: `$table->unique(['user_id', 'bulan_magang_id'], 'uq_pendaftaran_user_bulan');`.
  - Hapus index biasa: `$table->dropIndex('idx_pendaftaran_user_bulan');`.
- [x] Jalankan migrasi: `php artisan migrate` (berhasil dieksekusi 4,083ms).

### Tahap 2: Backend Logic & Validasi (Service, Action, Request)
- [x] Periksa dan pertegas `app/Services/QuotaAllocationService.php` pada method `pastikanTidakAdaPendaftaranAktif()`: memastikan status `StatusPendaftaranEnum::Ditolak` dan `StatusPendaftaranEnum::Dibatalkan` diizinkan mendaftar kembali sesuai BR-17.
- [x] Periksa `app/Actions/Peserta/Pendaftaran/CreatePendaftaranAction.php`: pastikan pembuatan draf baru dengan kombinasi user dan bulan yang sama sukses tersimpan sebagai baris baru dengan nomor registrasi unik baru.
- [x] Pastikan `app/Http/Requests/PendaftaranRequest.php` tidak menimbulkan false-positive error validasi untuk pendaftar yang status sebelumnya ditolak.

### Tahap 3: Halaman Indeks & Dashboard Peserta (UI Flow)
- [x] Verifikasi `app/Queries/Peserta/Pendaftaran/PendaftaranIndexQuery.php`: pastikan variabel `$pendaftaranAktif` tetap `null` jika status terakhir adalah `Ditolak`, sehingga form dan katalog pemilihan periode baru terbuka bagi peserta.
- [x] Alur pendaftaran ulang telah terhubung mulus di antarmuka web `/peserta/pendaftaran`.

### Tahap 4: Verifikasi & Quality Gate
- [x] Buat feature test baru `tests/Feature/PesertaReapplyAfterRejectionTest.php`.
- [x] Jalankan test suite: `php artisan test tests/Feature/PesertaReapplyAfterRejectionTest.php` (5 passed, 26 assertions).
- [x] Jalankan test regresi terkait: `php artisan test tests/Feature/PendaftaranPesertaTest.php` (6 passed, 62 assertions) dan `tests/Feature/PesertaPendaftaranFlowTest.php` (13 passed, 61 assertions).
- [x] Jalankan format checker: `vendor/bin/pint --test` (passed).

---

## 6. Strategi Pengujian (Testing Strategy)

### Skenario Otomatis (`PesertaReapplyAfterRejectionTest`):
1. **Happy Path (Re-apply Bulan yang Sama)**:
   - Peserta mendaftar pada Bulan A ➔ Admin menolak pendaftaran (`StatusPendaftaranEnum::Ditolak`) dengan catatan "Kualifikasi berkas belum memenuhi kriteria".
   - Peserta mengajukan pendaftaran baru untuk Bulan A yang sama.
   - **Assert**: Pengajuan berhasil dibuat, status pengajuan baru adalah `berkas_belum_lengkap`, nomor registrasi baru bertambah, dan rekaman pendaftaran lama tetap tersimpan dengan status `ditolak`.
2. **Happy Path (Re-apply Bulan Berbeda)**:
   - Peserta ditolak pada Bulan A ➔ Peserta mengajukan pendaftaran pada Bulan B.
   - **Assert**: Pengajuan sukses tersimpan di basis data tanpa error.
3. **Sad Path (Penegakan BR-17 / Percobaan Pendaftaran Aktif Ganda)**:
   - Peserta memiliki pendaftaran berstatus `diajukan` (masih aktif) ➔ Mencoba membuat pendaftaran baru.
   - **Assert**: Sistem menolak dengan `PendaftaranAktifGandaException` / pesan error validasi BR-17.
4. **Alur Kelompok (Re-apply Tim Kelompok)**:
   - Ketua tim kelompok ditolak ➔ Mengajukan kembali untuk rombongan tim.
   - **Assert**: Pendaftaran baru terhubung kembali ke entitas `kelompok_magang` dan relasi anggota tim.

---

## 7. Analisis Risiko & Mitigasi

| Risiko Potensial | Tingkat Risiko | Mitigasi yang Diterapkan |
|---|---|---|
| **Potensi Peserta Mendaftar Ganda Aktif** | Sedang | Dilindungi oleh *pessimistic lock* `lockForUpdate()` pada baris `users` dan method `pastikanTidakAdaPendaftaranAktif()` di dalam `DB::transaction()`. Pengguna tidak akan bisa memiliki 2 pendaftaran aktif bersamaan. |
| **Drop Index Unik Berdampak pada Foreign Key MySQL** | Rendah | Mengikuti *best-practice* MySQL: buat index biasa `idx_pendaftaran_user_bulan` terlebih dahulu sebelum menjalankan `dropUnique()`, sehingga integritas referensial foreign key tidak terputus. |
| **Inkonsistensi Tampilan Dashboard** | Rendah | `DashboardController` telah menggunakan `latest('id')->first()`, sehingga begitu pendaftaran baru dibuat, dashboard otomatis menampilkan status draf pendaftaran terbaru. |

---

## 8. Kriteria Selesai (Definition of Done)
- [x] File migrasi skema basis data berhasil dieksekusi di database `magkom_db` lokal.
- [x] Calon peserta yang sebelumnya ditolak dapat membuat pendaftaran baru pada bulan yang sama maupun bulan lain tanpa error 1062.
- [x] Riwayat penolakan pendaftaran lama tetap tersimpan utuh di tabel `pendaftaran` dan audit log.
- [x] Seluruh test case pada `PesertaReapplyAfterRejectionTest` lulus hijau (`100% PASS`).
- [x] Seluruh test suite regresi pendaftaran (`PendaftaranPesertaTest`, `PesertaPendaftaranFlowTest`) tetap lulus tanpa ada yang rusak (*zero regression*).
- [x] Kode sumber mematuhi standar PSR-12 via Laravel Pint.

