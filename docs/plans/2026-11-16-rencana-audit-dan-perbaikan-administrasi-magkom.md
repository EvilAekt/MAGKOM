# Rencana Audit, Pengujian, dan Perbaikan Modul Administrasi MAGKOM

| Informasi Dokumen | Nilai |
| :--- | :--- |
| **Tanggal Pembuatan** | 16 November 2026 |
| **Status Dokumen** | Draf Rencana (Menunggu Persetujuan User) |
| **Target Modul** | Administrasi (Pendaftaran, Penempatan, Kuota, Laporan & Ekspor) |
| **Branch Target** | `test-2` |
| **Referensi Utama** | `docs/features/administration.md`, `docs/features/placement.md`, `docs/01-PRD-MagKom.md` |

---

## 1. Tujuan

Rencana kerja ini bertujuan untuk melakukan analisis mendalam, pengujian menyeluruh (*comprehensive testing*), dan perbaikan bertahap (*phased remediation*) terhadap seluruh modul **Administrasi** Portal MAGKOM (Dinas Komunikasi dan Informatika Kabupaten Sleman). 

Pekerjaan difokuskan pada penyelesaian temuan bug nyata, pemenuhan aturan bisnis PRD (khususnya BR-06 SLA Verifikasi dan alur revisi dokumen), penguatan keamanan (eliminasi kebocoran password plaintext pada session penempatan manual), serta penjaminan kestabilan kompilasi aset Vite agar seluruh antarmuka admin bebas dari galat HTTP 500.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja
1. **Baseline Arsitektur**: Baseline teknologi tetap dipertahankan pada PHP 8.3.x, Laravel 10.x, Vanilla CSS di `resources/css/pages/admin/`, dan Vanilla ES Modules di `resources/js/pages/admin/` yang dikelola melalui Vite.
2. **Read-Only SuperAdmin & RBAC**: Hak akses modul administrasi tetap mematuhi otorisasi dinamis (`seleksi.verifikasi-berkas`, `penempatan.pembimbing`, `kuota.manajemen`, `laporan.kinerja-portofolio`) dan SuperAdmin beroperasi dalam mode *Read-Only* pada aksi putusan verifikasi berkas.
3. **Database Tanpa Destructive Operations**: Tidak ada operasi penghapusan skema (`DROP`, `TRUNCATE`, atau `migrate:fresh`) pada database. Penambahan atau penyesuaian kolom (jika ada) dilakukan secara aditif melalui migrasi aman.
4. **Penyelesaian Bertahap**: Eksekusi dilakukan per fase independen yang dapat diverifikasi langsung melalui unit & feature test sebelum melangkah ke fase berikutnya.

### Pertanyaan Terbuka untuk User
1. *Apakah alur "Perlu Perbaikan" (revisi dokumen) ingin menampilkan batas waktu (misal maksimal 3 hari kerja bagi peserta untuk upload ulang berkas), atau cukup menggunakan catatan revisi bebas dari verifikator?* (Secara default akan diterapkan catatan revisi fleksibel sesuai `PlacementService::mintaPerbaikan`).
2. *Apakah alur penempatan manual peserta jalur khusus disetujui untuk mengirimkan kredensial melalui notifikasi email resmi alih-alih menampilkan plain password di flash session browser?*

---

## 3. Pendekatan Teknis & Evaluasi Opsi

### Perbandingan Pendekatan

| Kriteria Evaluasi | Opsi A: All-in-One (Big Bang Fix) | Opsi B: Terstruktur Bertahap 4 Fase (Direkomendasikan) |
| :--- | :--- | :--- |
| **Metodologi** | Mengubah seluruh controller, action, request, dan view administrasi dalam satu waktu. | Membagi pekerjaan ke dalam 4 fase modular: (1) Pendaftaran & SLA, (2) Penempatan, (3) Laporan & Ekspor, (4) Quality Gate. |
| **Risiko Regresi** | **Tinggi**. Sulit mengisolasi penyebab jika terjadi kegagalan test suite. | **Sangat Rendah**. Setiap fase diverifikasi langsung dengan unit test dan feature test terisolasi. |
| **Kepatuhan User Request** | Rendah (user secara eksplisit meminta penyelesaian bertahap). | **100% Sesuai**. Memberikan kendali penuh kepada user untuk mengevaluasi hasil per fase. |
| **Kestabilan Kode** | Rawan memicu bentrok antrean database locking & Vite manifest. | Menjamin integritas transaksi `lockForUpdate` dan konsistensi bundle aset Vite. |

> **Rekomendasi**: Dipilih **Opsi B (Terstruktur Bertahap 4 Fase)** karena menjamin stabilitas sistem, meminimalisir efek samping (*side effects*), dan memastikan setiap perubahan dapat diuji secara atomik.

---

## 4. Rangkuman Temuan Masalah & Bug Hasil Audit

Dari hasil audit kode, dokumen, GitHub PR/Issues, dan verifikasi test runner, teridentifikasi 5 temuan utama pada modul Administrasi:

1. **Vite Manifest Drift pada Halaman Admin (BUG KRITIS TERBUKTI)**:
   - *Lokasi*: `resources/views/admin/pendaftaran/index.blade.php`, `resources/views/admin/penempatan/index.blade.php`.
   - *Masalah*: File CSS `pendaftaran-index.css` dan `penempatan-index.css` dimuat via `@vite`, namun bundle `public/build/manifest.json` belum terkompilasi, menyebabkan HTTP 500 error `ViteManifestNotFoundException` saat diakses tanpa dev server.
2. **Ketiadaan Opsi Putusan "Perlu Perbaikan" (BR-06 PRD Gap)**:
   - *Lokasi*: `app/Http/Requests/KeputusanPendaftaranRequest.php`, `app/Actions/Admin/Pendaftaran/PutuskanPendaftaranAction.php`, `resources/views/admin/pendaftaran/show.blade.php`.
   - *Masalah*: Backend service `PlacementService::mintaPerbaikan()` sudah ada dan enum `StatusPendaftaranEnum::PerluPerbaikan` tersedia, namun `KeputusanPendaftaranRequest` hanya memvalidasi `in:terima,tolak`, dan view admin hanya menyediakan tombol Terima/Tolak. Verifikator terpaksa menolak berkas pendaftar jika berkas buram/salah.
3. **Kalkulasi SLA Watchdog Belum Memperhitungkan Hari Kerja (BR-06 Gap)**:
   - *Lokasi*: `app/Queries/Admin/Pendaftaran/PendaftaranIndexQuery.php`.
   - *Masalah*: Filter SLA menggunakan `now()->subDays(5)` (hari kalender mentah). Pendaftaran hari Jumat sore sudah terhitung mendekati batas SLA pada hari Senin (karena Sabtu-Minggu terhitung), padahal baru berjalan 1 hari kerja.
4. **Kebocoran Password Plaintext pada Session Penempatan Manual (Security Risk)**:
   - *Lokasi*: `app/Http/Controllers/Admin/PenempatanController.php` (baris 249-257).
   - *Masalah*: Menyimpan `$plainPassword` di flash session browser (`with('password_baru', ...)`), bertentangan dengan Aturan 8 AGENTS.md mengenai isolasi kredensial.
5. **Potensi Kerentanan CSV/Formula Injection pada Ekspor Dokumen**:
   - *Lokasi*: `app/Services/ExportReportService.php`, `app/Exports/Admin/Penempatan/RekapSuratDiterimaExport.php`.
   - *Masalah*: Kolom teks bebas multiline (`catatan`, `institusi`) perlu proteksi sanitasi karakter awal (`=`, `+`, `-`, `@`) dan penanganan enclosure tanda kutip RFC 4180 yang lebih ketat.

---

## 5. Rencana Perubahan File

| File Target | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `app/Http/Requests/KeputusanPendaftaranRequest.php` | Modify | Menambahkan `'perlu_perbaikan'` ke aturan validasi keputusan dan mewajibkan catatan revisi |
| `app/Actions/Admin/Pendaftaran/PutuskanPendaftaranAction.php` | Modify | Menghubungkan keputusan `'perlu_perbaikan'` ke `PlacementService::mintaPerbaikan()` |
| `app/Queries/Admin/Pendaftaran/PendaftaranIndexQuery.php` | Modify | Menyesuaikan logika SLA Watchdog agar mengabaikan akhir pekan (kalkulasi 5 hari kerja) |
| `app/Http/Controllers/Admin/PenempatanController.php` | Modify | Mengamankan alur kredensial penempatan manual (menghapus penyimpanan plaintext di session publik) |
| `app/Services/ExportReportService.php` | Modify | Memperketat sanitasi formula injection dan handling multiline teks pada ekspor CSV |
| `app/Exports/Admin/Penempatan/RekapSuratDiterimaExport.php` | Modify | Memastikan konsistensi format header dan sanitasi baris ekspor |
| `resources/views/admin/pendaftaran/show.blade.php` | Modify | Menambahkan tab/tombol keputusan "Perlu Perbaikan" dengan textarea catatan revisi wajib |
| `resources/js/pages/admin/pendaftaran-show.js` | Modify | Menangani interaksi toggle tab "Perlu Perbaikan" pada form keputusan |
| `resources/css/pages/admin/pendaftaran-index.css` | Modify/Verify | Penyelarasan styling indikator status revisi dan lencana SLA hari kerja |
| `tests/Feature/AdminPendaftaranPerbaikanFlowTest.php` | Create | Feature test untuk alur lengkap putusan "Perlu Perbaikan", notifikasi peserta, dan resubmission |
| `tests/Feature/AdminSlaWorkingDaysTest.php` | Create | Feature test kalkulasi SLA Watchdog 5 hari kerja (mengabaikan Sabtu/Minggu) |
| `tests/Feature/AdminPenempatanManualSecurityTest.php` | Create | Feature test pencegahan kebocoran plaintext password pada penempatan manual |

---

## 6. Langkah Implementasi Bertahap (Checklist)

### Fase 1: Baseline Stability & Modul Administrasi Pendaftaran (SLA & Revisi Berkas)
- [x] **Langkah 1.1**: Verifikasi ulang kompilasi aset frontend via `npm run build` untuk memastikan seluruh CSS/JS admin terdaftar di `public/build/manifest.json`.
- [x] **Langkah 1.2**: Perbarui `app/Http/Requests/KeputusanPendaftaranRequest.php` untuk mengizinkan `'perlu_perbaikan'` pada `Rule::in(['terima', 'tolak', 'perlu_perbaikan'])` dan mewajibkan catatan perbaikan (minimal 10 karakter).
- [x] **Langkah 1.3**: Perbarui `app/Actions/Admin/Pendaftaran/PutuskanPendaftaranAction.php` agar mengeksekusi `PlacementService::mintaPerbaikan()` saat keputusan adalah `'perlu_perbaikan'`.
- [x] **Langkah 1.4**: Perbarui `resources/views/admin/pendaftaran/show.blade.php` untuk menampilkan tab opsi ketiga: **Perlu Perbaikan (Revisi)**, beserta integrasi event handler di `resources/js/pages/admin/pendaftaran-show.js`.
- [x] **Langkah 1.5**: Perbaiki kalkulasi SLA Watchdog pada `app/Queries/Admin/Pendaftaran/PendaftaranIndexQuery.php` agar menggunakan selisih hari kerja (Sabtu & Minggu dikecualikan dari perhitungan batas 5 hari).
- [x] **Langkah 1.6**: Buat feature test baru `tests/Feature/AdminPendaftaranPerbaikanFlowTest.php` dan `tests/Feature/AdminSlaWorkingDaysTest.php`.
- [x] **Verifikasi Fase 1**: Jalankan `vendor/bin/phpunit tests/Feature/AdminPendaftaran* tests/Feature/AdminAdministrasiExportTest.php` (Target: 100% Pass).

### Fase 2: Modul Administrasi Penempatan Peserta (Keamanan & Beban Pembimbing)
- [x] **Langkah 2.1**: Audit alur kredensial dan penempatan manual: Saat pengguna mengganti kata sandi wajib di `PasswordController.php`, field `password_awal` langsung dibersihkan menjadi `null` dan `must_change_password` dimatikan.
- [x] **Langkah 2.2**: Verifikasi integritas pembatalan penempatan pada `CancelPenempatanAction.php`: Kuota dikembalikan dengan benar jika sebelum tanggal mulai, dan status peserta berubah menjadi `mengundurkan_diri` tanpa meninggalkan orphaned records. Sejak tanggal mulai, kuota hangus dan akun peserta dinonaktifkan.
- [x] **Langkah 2.3**: Verifikasi validasi batas maksimal 20 mentee aktif per ASN pembimbing (`maks_peserta_aktif_per_pembimbing`) pada `AssignPesertaAction`, `CreateManualPenempatanAction`, dan `ReassignPembimbingAction`.
- [x] **Langkah 2.4**: Buat test suite keamanan `tests/Feature/AdminPenempatanFaseDuaSecurityTest.php`.
- [x] **Verifikasi Fase 2**: Jalankan pengujian penempatan: `AdminPenempatanFaseDuaSecurityTest`, `AdminPenempatanDuaTahapFeatureTest`, `AdminPenempatanLifecycleFeatureTest` (100% Pass).

### Fase 3: Modul Administrasi Laporan & Ekspor (Sanitasi & Performa) *(Status: Ditunda / On Hold)*
> *Catatan: Fase 3 dan 4 ditunda sementara atas permintaan pengguna untuk mengerjakan tugas lain terlebih dahulu. Progres Fase 1 dan Fase 2 telah tersimpan rapi dan terverifikasi.*

- [ ] **Langkah 3.1**: Tinjau sanitasi formula injection pada `app/Services/ExportReportService.php` dan `app/Exports/Admin/Penempatan/RekapSuratDiterimaExport.php` untuk mencegah eksekusi ekspresi berbahaya pada aplikasi spreadsheet (Excel/LibreOffice).
- [ ] **Langkah 3.2**: Periksa penanganan teks multiline pada kolom catatan atau alamat agar tidak merusak formatting kolom CSV RFC 4180.
- [ ] **Langkah 3.3**: Pastikan query ekspor pada `LaporanController::pendaftaran` dan `LaporanController::penempatan` menggunakan *chunking* atau *lazy loading* jika volume data melampaui 1.000 baris untuk mencegah memory exhaustion.
- [ ] **Verifikasi Fase 3**: Jalankan `vendor/bin/phpunit tests/Feature/AdminEkspor* tests/Feature/AdminLaporan*` (Target: 100% Pass).

### Fase 4: Quality Gate, Regression Suite, & Security Audit
- [ ] **Langkah 4.1**: Jalankan seluruh rangkaian test suite administrasi secara menyeluruh:
  ```powershell
  & "d:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" vendor/bin/phpunit --testsuite Feature --filter Admin
  ```
- [ ] **Langkah 4.2**: Jalankan static analysis PHPStan untuk memastikan kepatuhan type hinting tanpa error:
  ```powershell
  & "d:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" vendor/bin/phpstan analyse app/Http/Controllers/Admin app/Actions/Admin app/Queries/Admin --memory-limit=1G
  ```
- [ ] **Langkah 4.3**: Jalankan linter Laravel Pint untuk memastikan standar PSR-12:
  ```powershell
  & "d:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" vendor/bin/pint --test
  ```
- [ ] **Langkah 4.4**: Jalankan build akhir frontend:
  ```powershell
  npm run build
  ```
- [ ] **Langkah 4.5**: Periksa status `git status -sb` untuk memastikan working tree bersih dan bebas file sampah.

---

## 7. Strategi Testing

### 1. Feature Test Skenario Happy Path
- **Verifikasi Berkas Disetujui**: Admin dapat menyetujui berkas pendaftaran status `diajukan`, kuota berkurang secara atomik, pendaftar berpindah ke antrean belum ditempatkan.
- **Verifikasi Berkas Perlu Perbaikan**: Admin dapat memilih opsi perbaikan dengan catatan revisi, status pendaftaran berubah menjadi `perlu_perbaikan`, peserta menerima notifikasi dan dapat mengunggah berkas pengganti.
- **Penetapan Penempatan**: Admin menempatkan pendaftar berstatus `diterima` ke bidang dan pembimbing ASN (mentee < 20), surat penerimaan PDF berhasil diterbitkan.
- **Ekspor Data**: Admin dapat mengunduh rekapitulasi data pendaftaran dan penempatan dalam format XLSX, CSV, dan PDF dengan filter aktif.

### 2. Skenario Sad Path & Security
- **Kuota Penuh saat Diterima**: Menghasilkan pesan kesalahan ramah dan rollback transaksi tanpa merusak integritas kuota formasi.
- **Konkurensi Putusan**: Jika dua admin memutuskan pendaftaran yang sama secara bersamaan, `lockForUpdate` mencegah putusan ganda dan memicu `StatusSudahBerubahException`.
- **Batas Beban Pembimbing Melampaui 20**: Menolak penetapan dengan pesan validasi yang jelas.
- **Otorisasi SuperAdmin**: Memastikan SuperAdmin tidak dapat melakukan mutasi status pendaftaran (mode Read-Only aktif, HTTP 403).
- **Sanitasi Formula Injection**: Seluruh formula diawali tanda `=`, `@`, `+`, `-` di-escape dengan kutip tunggal (`'`) saat diekspor ke CSV/Excel.

---

## 8. Risiko & Mitigasi

| Risiko Potensial | Tingkat Risiko | Mitigasi Pencegahan |
| :--- | :---: | :--- |
| **Race Condition pada Pengambilan Kuota** | Tinggi | Wajib menggunakan pesimistik locking (`lockForUpdate()`) di dalam transaksi database atomik via `QuotaAllocationService`. |
| **Vite Manifest Hilang di Server Produksi** | Sedang | Memastikan `npm run build` dijalankan sebelum pengujian dan deployment, serta memverifikasi manifest berisi seluruh bundle admin. |
| **Peserta Tidak Menyelesaikan Revisi Berkas** | Rendah | Status `perlu_perbaikan` tetap mengikat kuota reservasi dengan timer SLA Watchdog khusus hingga batas waktu terlewati. |
| **Kebocoran Informasi Sensitif pada Log** | Rendah | Audit service mengabaikan nilai sensitif dan tidak mencatat kredensial/token pada field audit trail. |

---

## 9. Kriteria Selesai (Definition of Done)

- [ ] Seluruh 5 temuan audit administrasi telah ditangani dan diverifikasi solusinya.
- [ ] Opsi keputusan "Perlu Perbaikan" berfungsi penuh pada backend, request validasi, dan UI admin.
- [ ] Kalkulasi SLA Watchdog 5 hari kerja akurat dan tidak menghitung hari libur akhir pekan.
- [ ] Tidak ada kebocoran plaintext password pada session penempatan manual.
- [ ] Seluruh file CSS dan JavaScript admin dikelola rapi melalui Vite dan terkompilasi dalam manifest.
- [ ] 100% test suite modul administrasi lulus pengujian (`PHPUnit PASS`, 0 failures).
- [ ] PHPStan menunjukkan 0 errors pada modul administrasi.
- [ ] Laravel Pint 100% lolos tanpa pelanggaran style kode.
- [ ] Dokumentasi fitur di `docs/features/administration.md` diperbarui mencerminkan alur perbaikan berkas.

