# Plan: Peningkatan Lanjutan Fitur Pembimbing, Administrasi, & SuperAdmin Sesuai PRD (Fase 6)

- **Tanggal**: 2026-10-12
- **Status**: Draft (Menunggu Persetujuan Pengguna)
- **Author**: Antigravity (web-planning)
- **Target Modul**:
  1. **Portal Pembimbing Lapangan (`/pembimbing/*`)**: Bank Template Penugasan Mentoring (F-08), Early-Warning Mentee Pasif & Overdue Task Action Center (BR-09, BR-16), serta Rekapitulasi Jurnal Bimbingan & Laporan Kinerja ASN (F-09, F-10, Bab 11.3 R-03).
  2. **Portal Administrasi Kominfo (`/admin/*`)**: One-Click FIFO Promote Waiting List (F-04 AC-04-4, AC-04-6, BR-05), Ekspor Bundel Dokumen Berkas Pendaftaran ZIP (F-03, F-04, Bab 8.4, NFR-S6), dan Pemindahan Pembimbing Massal (Bulk Reassign) Berbasis Kuota (AC-07-4, BR-13, A-12).
  3. **Portal SuperAdmin (`/superadmin/*`)**: Audit Kepatuhan Password Staf (BR-15, NFR-S2), Snapshot Master Data JSON Exporter & Backup Konfigurasi (F-11, Bab 10.2), serta Automated Security & Dormant Token Sweeper (NFR-S3, NFR-S4, NFR-A5).
- **Dokumen Rujukan**:
  - [`docs/01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (Bab 2.3 Metrik Keberhasilan, Bab 3.2 Akun Staf, Bab 3.3 Matriks Hak Akses, Bab 4.3 Fase Lanjutan, Bab 7 F-03, F-04, F-07, F-08, F-09, F-10, F-11, F-13, F-16, F-17; Bab 8.4 Berkas; Bab 8.6 Pengaturan; Bab 10 NFR-S, NFR-D, NFR-A; Bab 11.3 R-02, R-03, R-06, R-10; Bab 12 OQ-01, OQ-03).
  - [`docs/02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) (Bab 6 Skema Database, Bab 8 RBAC & Keamanan, Bab 9 Prosedur Pemeliharaan).
  - [`docs/07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Sleman Blue `#0641CC`, font Inter/Open Sans, token visual, `.clean-scrollbar`).

---

## 1. Tujuan

Menyusun rencana kerja teknis yang terperinci, terstruktur, dan terukur untuk mengeksekusi kelanjutan fitur-fitur produktivitas, tata kelola, dan otomatisasi operasional pada tiga pilar utama sistem MagKom:

1. **Efisiensi & Produktivitas Pembimbing Lapangan ASN (`/pembimbing/*`)**:
   - **Bank Template Penugasan (Assignment Template Library - PRD F-08)**: Memfasilitasi pembimbing untuk menyimpan, mengelola, dan memilih template tugas berulang (contoh: "Orientasi Infrastruktur Server", "Setup Environment", "Laporan Mingguan") sehingga proses pemberian tugas ke banyak peserta baru menjadi instan tanpa perlu input manual berulang kali.
   - **Early-Warning Mentee Pasif & Overdue Task Action Center (PRD 8.6, BR-09, BR-16, Bab 11.3 R-03)**: Menyediakan deteksi proaktif mentee yang tidak mengisi logbook selama $\ge 3$ hari (mendekati batas toleransi gugur `logbook_maks_mundur_hari`) atau yang memiliki tugas terlambat, lengkap dengan tombol tindakan cepat bimbingan.
   - **Rekapitulasi Jurnal Bimbingan & Laporan Kinerja Mentoring ASN (PRD F-09, F-10)**: Menyediakan dashboard ringkasan jam terbang bimbingan dan kemampuan ekspor/cetak laporan rekapitulasi kinerja pembimbing lapangan ASN untuk kelengkapan administrasi SKP dan laporan kedinasan.

2. **Otomasi Operasional & Manajemen Berkas Administrasi (`/admin/*`)**:
   - **One-Click FIFO Promote Waiting List (PRD F-04 AC-04-4, AC-04-6, BR-05)**: Menyediakan tombol otomatisasi satu klik untuk menaikkan pendaftar antrean waiting list teratas (berdasarkan urutan submit `diajukan_at` terlama) menjadi diterima saat ada kuota kosong, dengan validasi atomik dan penugasan pembimbing otomatis.
   - **Ekspor Bundel Dokumen Berkas Pendaftaran ZIP (PRD F-03, F-04, Bab 8.4, Bab 10.1 NFR-S6)**: Memungkinkan admin mengunduh seluruh berkas pendaftaran (Surat Pengantar, CV, Transkrip/Rapor) milik peserta diterima dalam satu file arsip `.zip` yang terorganisasi per folder nama/NIM peserta untuk arsip luar jaringan dinas.
   - **Pemindahan Pembimbing Massal (Bulk Reassign Mentors) (PRD AC-07-4, BR-13, A-12)**: Memungkinkan admin memilih beberapa peserta sekaligus untuk dialihkan ke pembimbing baru jika pembimbing asal berhalangan/mutasi, dengan pengecekan ketat batas kapasitas pembimbing baru (`maks_peserta_aktif_per_pembimbing`).

3. **Tata Kelola Keamanan & Ketahanan Sistem SuperAdmin (`/superadmin/*`)**:
   - **Audit Kepatuhan Password Staf & Reset Kredensial Instan (PRD 3.2, 8.2, 11.1 D-07, BR-15)**: Menampilkan status kepatuhan penggantian password awal staf (`must_change_password`), indikator staf dormant, dan tombol regenerasi password sementara sekali pakai dengan pencatatan audit.
   - **Snapshot Master Data JSON Exporter & Config Backup (PRD F-11, Bab 10.2 Integritas Data)**: Memungkinkan SuperAdmin mengunduh salinan snapshot konfigurasi master data (jurusan, bidang, rubrik, kuota, pengaturan sistem) ke format JSON terstruktur untuk kebutuhan disaster recovery konfigurasi sebelum siklus tahun ajaran baru.
   - **Automated Security & Dormant Data Sweeper (PRD Bab 10.1 NFR-S3, NFR-S4, NFR-A5)**: Perintah terjadwal dan antarmuka monitoring untuk membersihkan token reset kedaluwarsa dan akun peserta tidak terverifikasi yang tertinggal lama.

---

## 2. Asumsi & Batasan

### Asumsi Teknis & Fungsional:
1. **Aturan Hak Akses & Pembatasan Nilai (PRD 3.3, Asumsi A-20)**:
   - Admin **tetap dilarang** melihat/mengakses nilai evaluasi magang peserta; ekspor dan rekapitulasi nilai evaluasi tetap eksklusif bagi Pembimbing (terhadap mentee bimbingannya) dan SuperAdmin.
2. **Kemandirian Dependensi (PHP Native First)**:
   - Pembuatan arsip `.zip` bundel berkas menggunakan ekstensi `ZipArchive` bawaan PHP 8.3 tanpa menambahkan paket Composer pihak ketiga yang berpotensi membebani repositori atau menimbulkan kerentanan keamanan (NFR-S12).
3. **Isolasi Berkas Privat (NFR-S6)**:
   - Penarikan berkas untuk bundel zip dilakukan langsung dari disk privat (`storage/app/private`), hanya oleh Admin/SuperAdmin yang telah melewati otorisasi Laravel Policy, dan dicatat dalam audit log tipe `akses_data`.
4. **Anti-Spam Email (PRD 11.1 D-02)**:
   - Sistem tidak mengirim email notifikasi apa pun terkait perubahan penugasan, peringatan logbook, atau promosi waiting list. Seluruh pembaruan status hanya diakses oleh pengguna melalui dashboard internal.
5. **Kapasitas Pembimbing (BR-13, OQ-01)**:
   - Pemindahan pembimbing massal wajib memvalidasi bahwa penambahan jumlah mentee tidak membuat pembimbing tujuan melebihi batas `maks_peserta_aktif_per_pembimbing` di tabel pengaturan (jika disetel tidak bernilai null).

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Pendekatan Teknis:

| Aspek Pertimbangan | Opsi A: Native Modular Architecture & Synchronous Stream (Terpilih - Recommended) | Opsi B: Async Worker Queue & Micro-Service Exporter |
| :--- | :--- | :--- |
| **Kompleksitas Infrastruktur** | **Sederhana & Andal**: Berjalan langsung di atas ekosistem Laravel 10 native. Tidak membutuhkan daemon queue Redis/RabbitMQ terpisah atau server worker tambahan di lingkungan Kominfo. | Membutuhkan worker queue terpisah (Supervisor, Redis), monitoring worker failure, dan penyimpanan file sementara berbasis cloud object storage. |
| **Kecepatan & Responsivitas** | **Tinggi**: Untuk volume magang dinas (12-15 peserta per bulan), bundel zip dan snapshot JSON diproses dalam hitungan detik (rata-rata 1-3 detik) langsung via stream response. | Lebih lambat untuk volume kecil karena overhead antrean job, polling websocket/polling HTTP ke browser. |
| **Keamanan & Otorisasi** | **Terpusat**: Dilindungi penuh oleh Session Auth, Gate/Policy Laravel, Anti-IDOR, dan pencatatan audit log terintegrasi dalam request lifecycle yang sama. | Rentan kebocoran data jika file arsip disimpan di storage publik/sementara tanpa manajemen masa kedaluwarsa token download yang ketat. |
| **Kepatuhan D-01 & D-09** | **100% Sesuai PRD**: Memaksimalkan kemampuan bawaan framework Laravel 10 dan PHP 8.3, mudah diuji dengan PHPUnit/Pest Feature Test. | Memperkenalkan arsitektur baru yang tidak tercakup dalam spesifikasi TDD Bab 10. |

> **Keputusan Arsitektur**: Mengadopsi **Opsi A**. Pendekatan ini paling aman, sangat cepat, teruji stabil, dan selaras dengan standar pengembangan internal Diskominfo Sleman.

---

## 4. Perubahan File

| Modul / Komponen | File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :---: | :--- |
| **Template Tugas (Pembimbing)** | `database/migrations/2026_10_12_000004_create_tugas_template_table.php` | Create | Tabel template tugas (`id`, `pembimbing_id`, `judul`, `deskripsi`, `kategori`, `kompleksitas`, `link_referensi`, `is_global`, `timestamps`). |
| **Template Tugas (Pembimbing)** | `app/Models/TugasTemplate.php` | Create | Model Eloquent template penugasan magang dengan relasi ke user pembimbing. |
| **Template Tugas (Pembimbing)** | `app/Http/Controllers/Pembimbing/TugasTemplateController.php` | Create | Controller CRUD template tugas & endpoint JSON picker untuk form pembuatan tugas. |
| **Template Tugas (Pembimbing)** | `resources/views/pembimbing/tugas/template/index.blade.php` | Create | Antarmuka manajemen bank template tugas pembimbing. |
| **Template Tugas (Pembimbing)** | `resources/views/pembimbing/tugas/create.blade.php` | Modify | Menambahkan tombol & modal dropdown "Gunakan Template" untuk auto-fill form tugas instan. |
| **Mentee Alert & Laporan (Pembimbing)** | `app/Http/Controllers/Pembimbing/PesertaController.php` | Modify | Menambahkan filter "Perlu Perhatian" ($\ge 3$ hari absen logbook / tugas overdue) dan aksi cepat catatan bimbingan. |
| **Mentee Alert & Laporan (Pembimbing)** | `resources/views/pembimbing/peserta/index.blade.php` | Modify | Menampilkan tab status kedisiplinan mentee, badge peringatan pasif, dan tombol aksi langsung. |
| **Laporan Kinerja Mentoring (Pembimbing)** | `app/Http/Controllers/Pembimbing/LaporanKinerjaController.php` | Create | Controller rekapitulasi portofolio bimbingan, log review, dan ekspor/cetak laporan ASN. |
| **Laporan Kinerja Mentoring (Pembimbing)** | `resources/views/pembimbing/laporan-kinerja/index.blade.php` | Create | Tampilan rekapitulasi jam bimbingan, rasio tugas selesai, dan format cetak resmi LPJ ASN. |
| **Waiting List FIFO (Admin)** | `app/Http/Controllers/Admin/WaitingListController.php` | Modify | Menambahkan method `promoteFifo(BulanMagang, Jurusan)` untuk menaikkan pendaftar antrean nomor 1 secara atomik. |
| **Waiting List FIFO (Admin)** | `resources/views/admin/waiting-list/index.blade.php` | Modify | Menambahkan tombol aksi "Promote Pendaftar Teratas (FIFO)" dengan validasi sisa kuota dinamis. |
| **Bundel Berkas ZIP (Admin)** | `app/Services/DocumentArchiveService.php` | Create | Layanan pengemasan berkas pendaftaran peserta (surat pengantar, CV, transkrip) ke arsip ZIP terstruktur menggunakan `ZipArchive`. |
| **Bundel Berkas ZIP (Admin)** | `app/Http/Controllers/Admin/PendaftaranController.php` | Modify | Menambahkan endpoint `downloadBerkasBatchZip(Request)` untuk mengunduh paket berkas pendaftar per periode/pilihan. |
| **Bundel Berkas ZIP (Admin)** | `resources/views/admin/pendaftaran/index.blade.php` | Modify | Menambahkan tombol "Unduh Bundel Berkas (ZIP)" pada tabel pendaftaran dan penempatan. |
| **Reassign Pembimbing Massal (Admin)** | `app/Http/Controllers/Admin/PenempatanController.php` | Modify | Menambahkan method `bulkReassignPembimbing(Request)` dengan validasi kapasitas `maks_peserta_aktif_per_pembimbing`. |
| **Reassign Pembimbing Massal (Admin)** | `resources/views/admin/penempatan/index.blade.php` | Modify | Menambahkan checkbox multi-select dan modal aksi massal "Pindahkan Pembimbing Terpilih". |
| **Audit Kepatuhan Password (SuperAdmin)** | `app/Http/Controllers/SuperAdmin/StafController.php` | Modify | Menambahkan filter `must_change_password`, status aktivitas login, dan aksi regenerasi password sementara. |
| **Audit Kepatuhan Password (SuperAdmin)** | `resources/views/superadmin/staf/index.blade.php` | Modify | Menambahkan indikator visual "Menunggu Ganti Password" (BR-15) dan modal salin password sekali pakai. |
| **Snapshot Master Data JSON (SuperAdmin)** | `app/Http/Controllers/SuperAdmin/PengaturanController.php` | Modify | Menambahkan method `exportMasterSnapshotJson()` untuk mengunduh arsip konfigurasi JSON sistem. |
| **Snapshot Master Data JSON (SuperAdmin)** | `resources/views/superadmin/pengaturan/index.blade.php` | Modify | Menambahkan kartu aksi "Cadangkan Konfigurasi Master (JSON)" pada halaman pengaturan sistem. |
| **Security Sweeper (SuperAdmin)** | `app/Console/Commands/SecuritySweeperCommand.php` | Create | Command Artisan `magkom:security-sweep` untuk membersihkan token reset & verifikasi kedaluwarsa. |
| **Security Sweeper (SuperAdmin)** | `app/Console/Kernel.php` | Modify | Mendaftarkan jadwal rutin command pembersihan token keamanan. |
| **Security Sweeper (SuperAdmin)** | `app/Http/Controllers/SuperAdmin/HealthMonitorController.php` | Modify | Menambahkan telemetri status token kedaluwarsa & tombol pemicu pembersihan instan. |
| **Security Sweeper (SuperAdmin)** | `resources/views/superadmin/health/index.blade.php` | Modify | Menambahkan kartu diagnostik "Higienitas Keamanan & Pembersihan Token". |
| **Rute & Routing Web** | `routes/web.php` | Modify | Mendaftarkan seluruh rute baru untuk modul Pembimbing, Administrasi, dan SuperAdmin. |
| **Pengujian Otomatis (TDD)** | `tests/Feature/AdvancedRoleEnhancementPhaseSixTest.php` | Create | Suite pengujian otomatis komprehensif menguji seluruh fungsionalitas Fase 6 dan validasi RBAC/keamanan. |

---

## 5. Langkah Implementasi Bertahap

### Fase 1: Peningkatan Modul Pembimbing Lapangan (`/pembimbing/*`)
- [ ] **Langkah 1.1: Database & Model Bank Template Tugas**:
  - Buat migrasi `database/migrations/2026_10_12_000004_create_tugas_template_table.php` dengan kolom: `pembimbing_id` (nullable untuk global), `judul`, `deskripsi`, `kategori`, `kompleksitas`, `link_referensi`, `is_global` (boolean, default false).
  - Buat model `app/Models/TugasTemplate.php` lengkap dengan relasi ke `User` pembimbing dan scope query `forPembimbing($userId)`.
- [ ] **Langkah 1.2: Controller & View Template Tugas**:
  - Buat `app/Http/Controllers/Pembimbing/TugasTemplateController.php` (method: `index`, `store`, `update`, `destroy`, dan `pickerJson`).
  - Buat antarmuka `resources/views/pembimbing/tugas/template/index.blade.php` dengan desain Sleman Blue `#0641CC`.
  - Integrasikan modal/dropdown "Gunakan Template" pada halaman form `resources/views/pembimbing/tugas/create.blade.php` yang mengisi form via JavaScript ringan tanpa reload.
- [ ] **Langkah 1.3: Early-Warning Mentee Pasif & Overdue Action Center**:
  - Modifikasi `app/Http/Controllers/Pembimbing/PesertaController.php` untuk menghitung status kedisiplinan mentee:
    - Kriteria pasif: Tidak mengisi logbook $\ge 3$ hari (mendekati batas gugur `logbook_maks_mundur_hari`).
    - Kriteria overdue: Memiliki tugas yang telah melewati deadline namun belum berstatus selesai.
  - Perbarui view `resources/views/pembimbing/peserta/index.blade.php` dengan badge status visual ("Perhatian: Logbook Absen 3 Hari", "Tugas Terlambat") dan filter cepat.
- [ ] **Langkah 1.4: Rekapitulasi Jurnal Bimbingan & Laporan Kinerja ASN**:
  - Buat controller `app/Http/Controllers/Pembimbing/LaporanKinerjaController.php`.
  - Buat view `resources/views/pembimbing/laporan-kinerja/index.blade.php` lengkap dengan metrik total jam pembimbingan, rasio tugas terselesaikan, distribusi nilai kompetensi mentee, serta tombol Cetak / PDF resmi format dinas.

---

### Fase 2: Peningkatan Modul Administrasi (`/admin/*`)
- [ ] **Langkah 2.1: One-Click FIFO Promote Waiting List (PRD F-04 AC-04-4, AC-04-6, BR-05)**:
  - Modifikasi `app/Http/Controllers/Admin/WaitingListController.php`:
    - Tambahkan method `promoteFifo(Request $request)` yang menerima parameter `bulan_magang_id` dan `jurusan_id`.
    - Di dalam `DB::transaction()` dengan `lockForUpdate`:
      1. Cek apakah sisa kuota umum untuk kombinasi bulan-jurusan tersebut masih $> 0$.
      2. Ambil 1 pendaftaran berstatus `waiting_list` dengan `diajukan_at` terlama.
      3. Ubah status menjadi `diterima`, tambah `terisi` pada kuota, buat record `Penempatan` aktif, dan tetapkan pembimbing aktif secara otomatis (AC-07-2).
      4. Catat transaksi ke `AuditLog`.
  - Tambahkan tombol visual "Promote FIFO Teratas" pada `resources/views/admin/waiting-list/index.blade.php` dengan badge sisa kuota yang jelas.
- [ ] **Langkah 2.2: Layanan & Fitur Unduh Bundel Berkas Pendaftaran ZIP**:
  - Buat service `app/Services/DocumentArchiveService.php` dengan method `createRegistrationBatchZip(Collection $pendaftarans): string`.
  - Service membaca berkas fisik dari `Storage::disk('private')` dan mengemasnya ke dalam file ZIP sementara di `storage/app/temp/` dengan struktur:
    `[NIM_NISN] - [Nama_Peserta]/01_Surat_Pengantar.pdf`, dsb.
  - Tambahkan method `downloadBerkasBatchZip(Request $request)` di `app/Http/Controllers/Admin/PendaftaranController.php` dengan stream download dan penghapusan file sementara setelah stream selesai.
  - Tambahkan tombol "Unduh Bundel Berkas (ZIP)" di `resources/views/admin/pendaftaran/index.blade.php`.
- [ ] **Langkah 2.3: Pemindahan Pembimbing Massal (Bulk Reassign Mentors)**:
  - Modifikasi `app/Http/Controllers/Admin/PenempatanController.php` dengan method `bulkReassignPembimbing(Request $request)`:
    - Input: `penempatan_ids` (array), `pembimbing_id` (target).
    - Validasi: Pembimbing target aktif, dan penambahan mentee tidak melampaui `maks_peserta_aktif_per_pembimbing`.
    - Update `pembimbing_id` pada seluruh penempatan terpilih dalam satu transaksi database dan catat audit log per penempatan.
  - Tambahkan checkbox pemilihan massal dan floating action bar modal "Pindahkan Pembimbing" pada `resources/views/admin/penempatan/index.blade.php`.

---

### Fase 3: Peningkatan Modul SuperAdmin (`/superadmin/*`)
- [ ] **Langkah 3.1: Audit Kepatuhan Password Staf & Regenerasi Kredensial (BR-15)**:
  - Modifikasi `app/Http/Controllers/SuperAdmin/StafController.php`:
    - Tambahkan filter status password: `semua`, `menunggu_ganti` (`must_change_password = true`), `sudah_ganti` (`must_change_password = false`).
    - Tambahkan method `regeneratePassword(User $user, Request $request)` yang membuat password acak baru, mengaktifkan `must_change_password = true`, mencatat audit log, dan mengembalikan flash message berisi kredensial sementara untuk diserahkan manual.
  - Perbarui view `resources/views/superadmin/staf/index.blade.php` dengan badge status keamanan password dan modal salin password sekali pakai.
- [ ] **Langkah 3.2: Snapshot Master Data JSON Exporter**:
  - Modifikasi `app/Http/Controllers/SuperAdmin/PengaturanController.php`:
    - Tambahkan method `exportMasterSnapshotJson()` yang mengumpulkan konfigurasi: `jurusan`, `bidang`, `rubrik` (kriteria & bobot), `bulan_magang` (jadwal buka-tutup), dan master `pengaturan`.
    - Menghasilkan file JSON terformat rapi dengan checksum SHA-256 untuk verifikasi integritas arsip konfigurasi TI dinas.
  - Tambahkan tombol aksi "Cadangkan Master Data (JSON)" pada view `resources/views/superadmin/pengaturan/index.blade.php`.
- [ ] **Langkah 3.3: Automated Security & Dormant Data Sweeper**:
  - Buat command Artisan `app/Console/Commands/SecuritySweeperCommand.php` (`magkom:security-sweep`):
    - Membersihkan baris token pada `password_resets` / `password_reset_tokens` yang berumur $> 60$ menit.
    - Menghapus akun pendaftar calon peserta yang tidak memverifikasi email selama $> 72$ jam tanpa aktivitas berkas.
    - Bersifat idempotent dan aman.
  - Daftarkan command di `app/Console/Kernel.php` untuk dijalankan harian.
  - Perbarui `app/Http/Controllers/SuperAdmin/HealthMonitorController.php` dan view `resources/views/superadmin/health/index.blade.php` dengan kartu telemetri kebersihan token keamanan dan tombol eksekusi manual sweeper.

---

### Fase 4: Pengujian Komprehensif (TDD) & Verifikasi Kualitas
- [ ] **Langkah 4.1: Feature Test Suite Fase 6**:
  - Buat file pengujian `tests/Feature/AdvancedRoleEnhancementPhaseSixTest.php`:
    - Test CRUD & otorisasi Bank Template Tugas (Pembimbing vs SuperAdmin vs Peserta).
    - Test filter Early Warning mentee pasif & overdue tasks.
    - Test One-Click FIFO Promote Waiting List (memastikan kuota atomik, antrean FIFO terlama terambil, dan penolakan saat kuota habis).
    - Test Ekspor Bundel Dokumen Berkas ZIP (struktur arsip, header nosniff, audit log).
    - Test Pemindahan Pembimbing Massal dengan pengecekan batas kapasitas `maks_peserta_aktif_per_pembimbing`.
    - Test Kepatuhan Password Staf & Regenerasi Kredensial BR-15.
    - Test Snapshot Master Data JSON Exporter.
    - Test Artisan Command `magkom:security-sweep`.
- [ ] **Langkah 4.2: Regresi Test Suite Lengkap**:
  - Jalankan seluruh test suite (`php artisan test`) untuk memastikan seluruh 240+ test eksisting tetap berstatus 100% PASS / Green tanpa regresi.

---

## 6. Strategi Testing

### 1. Automated Feature Tests (`AdvancedRoleEnhancementPhaseSixTest.php`):
- **Otorisasi & RBAC**:
  - Memastikan hanya Pembimbing yang dapat membuat template tugas miliknya; Admin & Peserta ditolak 403.
  - Memastikan SuperAdmin tetap read-only terhadap data tugas pembimbing.
  - Memastikan Admin dan SuperAdmin memiliki hak mengeksekusi One-Click Promote Waiting List dan Ekspor Bundel ZIP; Pembimbing & Peserta ditolak 403.
  - Memastikan hanya SuperAdmin yang dapat mengekspor Snapshot Master Data JSON dan meregenerasi password staf.
- **Logika Bisnis & Integritas Data**:
  - Uji FIFO Waiting List: Saat 2 pendaftar berstatus waiting list, verifikasi bahwa pendaftar dengan `diajukan_at` lebih lampau selalu dipromosikan lebih dahulu.
  - Uji Kapasitas Pembimbing: Saat pembimbing memiliki 9 mentee aktif dan batas kapasitas adalah 10, transfer massal 2 mentee baru ke pembimbing tersebut wajib ditolak dengan pesan kesalahan yang informatif.
  - Uji Pembuatan ZIP: Verifikasi integritas ZIP stream dan sanitasi nama file dalam arsip untuk mencegah *Zip Slip / Path Traversal*.
  - Uji Security Sweeper: Verifikasi bahwa token reset berusia 10 menit tidak terhapus, sedangkan token berusia 120 menit berhasil dipangkas.

### 2. Manual & Visual Quality Inspection:
- Verifikasi antarmuka modal dropdown template tugas pada layar mobile dan desktop (responsif).
- Verifikasi tampilan indikator badge peringatan mentee pasif pada daftar peserta bimbingan.
- Verifikasi tombol unduh bundel berkas ZIP menghasilkan arsip yang dapat diekstrak dengan struktur folder yang rapi.
- Verifikasi tampilan kartu telemetri keamanan pada halaman Health Monitor SuperAdmin.

---

## 7. Risiko & Mitigasi

| No | Risiko Teknis / Operasional | Tingkat | Dampak | Strategi Mitigasi |
| :---: | :--- | :---: | :--- | :--- |
| **1** | Memory exhaustion saat membuat bundel ZIP berkas pendaftaran dalam jumlah besar. | Sedang | Server mengalami error 500 jika banyak file berukuran besar dimuat sekaligus ke memori. | Gunakan stream file sementara (`sys_get_temp_dir()`) dan pagination/chunking per 50 berkas. Tetapkan batas maksimal 100 berkas per unduhan bundel. |
| **2** | Pelanggaran kapasitas pembimbing saat pemindahan massal. | Rendah | Beban kerja pembimbing melebihi batas ketentuan kedinasan (BR-13). | Hitung kalkulasi sisa kapasitas secara real-time sebelum transaksi database dieksekusi; batalkan seluruh transaksi jika kapasitas terlampaui. |
| **3** | Race condition pada promosi FIFO Waiting List jika 2 admin menekan tombol secara bersamaan. | Sedang | Kuota menjadi minus atau pendaftar yang sama dipromosikan dua kali. | Gunakan transaksi database `DB::transaction()` dengan `lockForUpdate()` pada record kuota dan antrean pendaftaran terkait (NFR-D1). |
| **4** | Pembersihan token yang tidak sengaja menghapus token yang masih aktif. | Rendah | Pengguna yang sedang dalam alur reset password gagal memproses password baru. | Filter ketat dengan ambang batas kedaluwarsa eksplisit (`created_at < now()->subMinutes(60)`). |

---

## 8. Kriteria Selesai (Definition of Done)

Implementasi Fase 6 dinyatakan selesai apabila:
1. Seluruh 6 fitur utama (Bank Template Tugas, Mentee Alert & Laporan Kinerja ASN, One-Click FIFO Promote, Ekspor Bundel Berkas ZIP, Bulk Reassign Mentors, Audit Password Staf & Security Sweeper) selesai diimplementasikan.
2. Seluruh acceptance criteria PRD terkait (F-03, F-04, F-07, F-08, F-09, F-10, F-11, BR-05, BR-13, BR-15, BR-16) terpenuhi.
3. Seluruh aksi mutasi dan ekspor data tercatat secara akurat di tabel `audit_log` (NFR-A1, NFR-A2).
4. Automated Test Suite `AdvancedRoleEnhancementPhaseSixTest.php` lulus 100% tanpa kegagalan.
5. Seluruh test suite regresi aplikasi (`php artisan test`) lulus 100% (Green).
6. Tampilan antarmuka Blade konsisten mengikuti standar visual Sleman Blue `#0641CC`, font Inter/Open Sans, dan bebas scrollbar duplikat.
7. Tidak ada perubahan pada aturan bisnis mendasar: Admin tetap tidak memiliki akses terhadap nilai magang peserta.

---

## 9. Catatan & Saran Lanjutan

- Fitur Bank Template Tugas Pembimbing dapat diperkaya pada rilis berikutnya dengan fitur import/export rubrik penugasan antar bidang dinas.
- Untuk ekspor bundel berkas ZIP, jika di masa depan volume peserta melonjak melebihi 200 berkas per bulan, dapat dipertimbangkan pemindahan proses pembuatan arsip ke background job dengan link unduhan bertanda tangan yang dikirimkan ke notifikasi dashboard admin.
