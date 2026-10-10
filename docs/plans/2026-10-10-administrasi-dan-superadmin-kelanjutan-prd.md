# Plan: Penyempurnaan Kelanjutan Administrasi & SuperAdmin Sesuai PRD

- **Tanggal**: 2026-10-10
- **Status**: Selesai / Terverifikasi Penuh
- **Author**: Antigravity (web-implementation)

---

## 1. Tujuan
Menyusun rencana kerja teknis untuk melengkapi sisa kebutuhan fungsional dan operasional pada **Portal Administrasi (`/admin/*`)** dan **Portal SuperAdmin (`/superadmin/*`)** yang dispesifikasikan dalam [01-PRD-MagKom.md](file:///g:/MAGKOM/docs/01-PRD-MagKom.md), [02-TDD-MagKom.md](file:///g:/MAGKOM/docs/02-TDD-MagKom.md), dan [07-DESIGN-SYSTEM-MAGKOM.md](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md), yang saat ini belum memiliki antarmuka pengguna (UI) lengkap atau alur kerja terpadu.

Cakupan 4 area pengembangan kelanjutan:
1. **Hub Laporan & Ekspor Data Interaktif (F-17 / PRD 3.3, AC-17-1, AC-17-2, 9.2)**:
   - Membangun antarmuka visual halaman Hub Laporan (`/admin/laporan`) dengan kartu metrik ringkasan eksekutif, tabel pratinjau rekapitulasi data (pendaftaran, penempatan, realisasi kuota vs slot), filter dinamis (periode bulan, jurusan, status), dan tombol unduh CSV ber-sanitasi anti formula injection (`'`, `=`, `+`, `-`, `@`).
   - Penegakan matriks hak akses PRD 3.3 & A-20: Tab Laporan Rekapitulasi Nilai & Kelulusan hanya dapat diakses dan diunduh oleh **SuperAdmin** (Admin verifikator dibatasi tanpa rekap nilai).
2. **Modul Arsip Peserta Magang SuperAdmin (F-12 AC-12-4, PRD 7 F-12, 9.2)**:
   - Membangun halaman antarmuka baca-saja Arsip Peserta (`/superadmin/arsip-peserta`) untuk meninjau data historis peserta magang yang telah menyelesaikan masa magang (`selesai`), melewati masa tenggang, atau dinonaktifkan (`nonaktif`).
   - Halaman detail arsip menampilkan profil lengkap alumni, riwayat penempatan & pembimbing lapangan, rekap tugas & logbook harian, dan Lembar Nilai resmi yang diterbitkan.
3. **Penyempurnaan Input Peserta Manual (F-07 AC-07-3)**:
   - Memfasilitasi pendaftaran manual di mana staf dinas dapat mendaftarkan peserta baru yang belum memiliki akun (nama, email, NIM/NISN, institusi, jurusan, jenjang).
   - Sistem secara otomatis membuat akun user peserta terverifikasi dengan flag `must_change_password = true`, profil peserta, dan penempatan aktif dalam satu transaksi atomik, baik melalui alokasi Jalur Khusus (Slot Khusus) maupun Jalur Umum (Kuota Jurusan).
4. **Penyempurnaan Parameter Pengaturan & Filter Audit Log (F-11 AC-11-7, F-13 AC-13-6, PRD 8.6)**:
   - Menambahkan konfigurasi editor batas ambang predikat nilai (`predikat_batas` JSON: A ≥ 85, B ≥ 70, C ≥ 55, D ≥ 40) pada halaman `/superadmin/pengaturan`.
   - Menambahkan filter rentang tanggal, filter tipe audit (`login`, `aksi`, `ekspor`), dan pencarian `request_id` pada viewer Jejak Audit Trail SuperAdmin.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi:
1. Hub Laporan (`/admin/laporan`) dapat diakses oleh Admin Verifikator dan SuperAdmin, namun tab **Rekapitulasi Nilai** diproteksi dengan `@can` / Policy / Gate `superadmin-only` sesuai aturan bisnis PRD 3.3 dan A-20.
2. Seluruh ekspor CSV menggunakan stream download memory-efficient (`Symfony\Component\HttpFoundation\StreamedResponse`) yang telah dilengkapi sanitasi formula injection (`ExportReportService::sanitizeCell`).
3. Akun peserta yang dibuat lewat input manual staf dinas memiliki password awal acak yang aman dan diserahkan manual di luar sistem (PRD 3.2), dengan kewajiban mengganti kata sandi saat login pertama (`must_change_password = true`).
4. Data arsip peserta bersifat **strictly read-only**: SuperAdmin tidak dapat mengubah nilai, logbook, atau profil alumni magang dari halaman arsip.

### Pertanyaan Terbuka:
- Tidak ada pertanyaan terbuka yang menghambat implementasi. Seluruh aturan bisnis (BR-01 s/d BR-17, AC-07-3, AC-12-4, AC-17-1, AC-17-2) telah tertuang jelas di PRD.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Arsitektur:

| Area | Opsi 1 (Direkomendasikan) | Opsi 2 (Alternatif) | Alasan Pemilihan Opsi 1 |
| :--- | :--- | :--- | :--- |
| **Antarmuka Laporan & Ekspor** | **Single Hub Multi-Tab View (`/admin/laporan`) dengan Live Preview & Ekspor CSV** | 4 Halaman URL terpisah untuk masing-masing laporan | Pengalaman pengguna jauh lebih cepat dan terpadu; Admin & SuperAdmin dapat beralih antara rekap pendaftaran, penempatan, kuota, dan nilai dalam satu workspace tanpa berpindah rute berulang kali. |
| **Arsip Peserta SuperAdmin** | **Dedicated Route & Controller (`/superadmin/arsip-peserta`) dengan Tab Riwayat Detail** | Menggabungkan ke tabel penempatan umum dengan filter status | PRD 9.2 secara eksplisit memisahkan menu "Arsip Peserta" dari "Penempatan". Memisahkan modul arsip memastikan penempatan aktif tetap fokus pada peserta yang sedang magang, sementara arsip menjadi bank data alumni & kepatuhan audit. |
| **Input Peserta Manual** | **Modal 2-Mode: "Pilih Akun Terdaftar" ATAU "Buat Akun Peserta Baru"** | Mewajibkan calon peserta mendaftar akun sendiri terlebih dahulu | Memenuhi spesifikasi AC-07-3: jika ada tamu dinas / titipan khusus tanpa akses web sebelumnya, admin dapat langsung menginput identitas peserta sekaligus menerbitkan penempatan instan. |

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`routes/web.php`](file:///g:/MAGKOM/routes/web.php) | Modify | Menambahkan rute antarmuka Hub Laporan (`admin.laporan.index`), rute Arsip Peserta SuperAdmin (`superadmin.arsip.index`, `superadmin.arsip.show`), dan rute input manual peserta baru. |
| [`app/Http/Controllers/Admin/LaporanController.php`](file:///g:/MAGKOM/app/Http/Controllers/Admin/LaporanController.php) | Modify | Menambahkan method `index()` untuk merender dashboard Hub Laporan & Ekspor dengan data ringkasan dan pratinjau tabel. |
| [`resources/views/admin/laporan/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/laporan/index.blade.php) | Create | Tampilan Blade Hub Laporan & Ekspor (Tab Rekap Pendaftaran, Penempatan, Kuota, dan Nilai) dengan filter dan kartu statistik. |
| [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php) | Modify | Mengarahkan menu sidebar "Laporan & Ekspor" ke `admin.laporan.index` dan menambahkan menu "Arsip Peserta" di navigasi SuperAdmin. |
| [`app/Http/Controllers/SuperAdmin/ArsipPesertaController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/ArsipPesertaController.php) | Create | Controller arsip peserta magang nonaktif/selesai untuk SuperAdmin (index & detail baca-saja). |
| [`resources/views/superadmin/arsip/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/arsip/index.blade.php) | Create | Tampilan daftar arsip alumni magang dengan pencarian institusi, jurusan, dan tahun lulus. |
| [`resources/views/superadmin/arsip/show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/arsip/show.blade.php) | Create | Tampilan detail arsip peserta (profil, ringkasan penempatan, tugas, logbook, dan nilai akhir). |
| [`app/Services/PlacementService.php`](file:///g:/MAGKOM/app/Services/PlacementService.php) | Modify | Menambahkan fungsionalitas pembuatan user peserta baru saat penempatan manual sesuai AC-07-3. |
| [`resources/views/admin/penempatan/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/penempatan/index.blade.php) | Modify | Menyempurnakan modal penempatan manual dengan opsi pembuatan akun peserta baru (nama, email, nim/nisn, institusi, jurusan, jenjang). |
| [`app/Http/Controllers/SuperAdmin/PengaturanController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/PengaturanController.php) | Modify | Mendukung penyimpanan dan validasi ambang batas predikat nilai (`predikat_batas` JSON) sesuai PRD 8.6. |
| [`resources/views/superadmin/pengaturan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/pengaturan/index.blade.php) | Modify | Menambahkan input form batas ambang predikat A, B, C, D di kartu kebijakan penilaian. |
| [`resources/views/superadmin/audit-log/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/audit-log/index.blade.php) | Modify | Menambahkan filter rentang tanggal, filter tipe audit (Login, Mutasi, Ekspor), dan input pencarian `request_id`. |
| [`tests/Feature/AdminLaporanDanArsipTest.php`](file:///g:/MAGKOM/tests/Feature/AdminLaporanDanArsipTest.php) | Create | Automated feature test untuk Hub Laporan, otorisasi rekap nilai SuperAdmin, Arsip Peserta baca-saja, dan input manual peserta baru. |

---

## 5. Langkah Implementasi
*Catatan: Checklist di bawah ini telah dieksekusi secara lengkap dan terverifikasi.*

### Modul 1: Hub Laporan & Ekspor Data Interaktif (`/admin/laporan`)
- [x] **Langkah 1.1**: Perbarui `app/Http/Controllers/Admin/LaporanController.php`:
  - Tambahkan method `index(Request $request)` yang mengumpulkan ringkasan angka statistik (total diajukan, diterima, ditolak, penempatan aktif, selesai) dan sampel data rekapitulasi.
  - Berikan otorisasi ketat pada tab/ekspor rekap nilai (`nilai()`): jika bukan SuperAdmin, lempar `AuthorizationException` (403 Forbidden).
- [x] **Langkah 1.2**: Buat tampilan Blade [`resources/views/admin/laporan/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/laporan/index.blade.php):
  - Buat tab switcher: *Rekap Pendaftaran*, *Data Penempatan*, *Realisasi Kuota & Slot*, dan *Rekap Nilai Peserta (SuperAdmin)*.
  - Tampilkan 4 summary metric cards di bagian atas (Total Pengajuan, Peserta Aktif, Alokasi Kuota Terisi, Tingkat Kelulusan).
  - Tampilkan form filter periode bulan & status dengan tombol **"Terapkan Filter"** dan tombol **"Unduh CSV"**.
  - Tampilkan pratinjau tabel data 10 baris terbaru dengan paginasi atau link unduh penuh.
- [x] **Langkah 1.3**: Perbarui tautan di [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php):
  - Ubah link menu sidebar `Laporan & Ekspor` dari `admin.laporan.pendaftaran` menjadi `admin.laporan.index`.

### Modul 2: Arsip Peserta Magang SuperAdmin (`/superadmin/arsip-peserta`)
- [x] **Langkah 2.1**: Buat controller `app/Http/Controllers/SuperAdmin/ArsipPesertaController.php`:
  - Method `index()`: query penempatan berstatus `selesai` atau peserta berstatus nonaktif dengan pencarian nama, institusi, NIM, dan filter tahun/jurusan.
  - Method `show($penempatan)`: sajikan data profil, riwayat pembimbing, snapshot nilai final, dan tautan unduh Lembar Nilai PDF.
- [x] **Langkah 2.2**: Daftarkan rute di `routes/web.php` di bawah grup `role:superadmin`:
  - `GET /superadmin/arsip-peserta` -> `ArsipPesertaController@index` (name: `superadmin.arsip.index`).
  - `GET /superadmin/arsip-peserta/{penempatan}` -> `ArsipPesertaController@show` (name: `superadmin.arsip.show`).
- [x] **Langkah 2.3**: Buat antarmuka Blade [`resources/views/superadmin/arsip/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/arsip/index.blade.php) dan [`resources/views/superadmin/arsip/show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/arsip/show.blade.php):
  - Tabel index alumni dengan badge status, nilai akhir, predikat, dan tombol "Lihat Arsip Lengkap".
  - Halaman detail dengan kartu identitas peserta, rekapitulasi penilaian rubrik, dan log aktivitas baca-saja.
- [x] **Langkah 2.4**: Tambahkan menu **"Arsip Peserta"** pada sidebar SuperAdmin di `resources/views/layouts/dashboard.blade.php`.

### Modul 3: Input Peserta Manual Mandiri (F-07 AC-07-3)
- [x] **Langkah 3.1**: Perbarui `app/Services/PlacementService.php`:
  - Kembangkan method `tempatkanPesertaBaruManual(...)` yang menerima data akun (nama, email, password acak), data profil (nim_nisn, institusi, jurusan_id, jenjang), alokasi penempatan (pembimbing, bidang, bulan mulai, durasi), dan pilihan jalur (`khusus` via slot atau `umum` via kuota jurusan).
  - Pastikan user dibuat dengan role `peserta`, `email_verified_at = now()`, `must_change_password = true`, dan `status_aktif = true`.
- [x] **Langkah 3.2**: Perbarui `app/Http/Requests/PenempatanManualRequest.php` dan `app/Http/Controllers/Admin/PenempatanController.php`:
  - Tambahkan validasi kondisional: jika `mode === 'baru'`, validasi kolom nama, email (unik), nim_nisn, institusi, jurusan_id, dan jenjang.
- [x] **Langkah 3.3**: Perbarui modal di [`resources/views/admin/penempatan/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/penempatan/index.blade.php):
  - Tambahkan toggle opsi di dalam modal: **"Pilih Peserta Terdaftar"** atau **"Daftarkan Peserta Baru Secara Langsung"**.

### Modul 4: Pengaturan Ambang Batas Predikat & Filter Audit Trail
- [x] **Langkah 4.1**: Perbarui `app/Http/Controllers/SuperAdmin/PengaturanController.php` & `resources/views/superadmin/pengaturan/index.blade.php`:
  - Tambahkan input field untuk `predikat_batas` (Batas Minimum Nilai A, B, C, D) dengan validasi urutan logika: `A > B > C > D`.
  - Simpan format JSON ke database dan bersihkan cache pengaturan sistem.
- [x] **Langkah 4.2**: Perbarui `resources/views/superadmin/audit-log/index.blade.php` & `app/Http/Controllers/SuperAdmin/AuditLogController.php`:
  - Tambahkan filter rentang tanggal (`dari_tanggal`, `sampai_tanggal`), filter tipe aktivitas (`tipe`), dan input filter `request_id`.

### Quality Gate & Pengujian
- [x] **Langkah 5.1**: Buat test suite komprehensif [`tests/Feature/AdminLaporanDanArsipTest.php`](file:///g:/MAGKOM/tests/Feature/AdminLaporanDanArsipTest.php) untuk memverifikasi:
  - Hub Laporan index memuat summary cards dan pratinjau tabel.
  - Admin verifikator dapat mengunduh CSV pendaftaran, penempatan, dan kuota.
  - Admin verifikator DITOLAK (403) saat mencoba mengakses atau mengunduh rekap nilai.
  - SuperAdmin dapat mengakses dan mengunduh seluruh jenis laporan termasuk rekap nilai.
  - Arsip Peserta SuperAdmin menampilkan alumni magang selesai dan detailnya secara baca-saja.
  - Penempatan manual peserta baru membuat akun, profil, dan penempatan aktif secara atomik.
- [x] **Langkah 5.2**: Verifikasi visual interaktif melalui peramban web pada `/admin/laporan`, `/superadmin/arsip-peserta`, modal penempatan manual baru, dan pengaturan predikat nilai.

---

## 6. Strategi Testing

### Automated Feature Test:
1. `test_admin_and_superadmin_can_access_laporan_hub`: Memastikan halaman `/admin/laporan` dapat diakses dan menampilkan ringkasan data.
2. `test_admin_forbidden_from_accessing_rekap_nilai`: Memastikan proteksi otorisasi PRD 3.3 & A-20 menolak Admin biasa yang mencoba membuka atau mengekspor rekap nilai.
3. `test_superadmin_can_export_all_reports_with_sanitized_csv`: Memastikan ekspor CSV pendaftaran, penempatan, kuota, dan nilai menghasilkan output valid dengan sel tersanitasi dari formula injection.
4. `test_superadmin_can_view_arsip_peserta_index_and_detail`: Memastikan daftar alumni magang selesai/nonaktif tampil dan detailnya dapat diinspeksi.
5. `test_admin_can_place_newly_created_manual_participant`: Memastikan input peserta baru manual membentuk User, PesertaProfil, dan Penempatan secara simultan.
6. `test_superadmin_can_update_predikat_thresholds`: Memastikan konfigurasi ambang batas huruf mutu A, B, C, D tersimpan dan tervalidasi.

### UI & Manual Verification:
1. Login sebagai `admin@magkom.go.id`:
   - Buka menu **Laporan & Ekspor**: verifikasi 3 tab aktif (Pendaftaran, Penempatan, Kuota) dan tab Nilai tersembunyi / terkunci.
   - Buka menu **Penempatan**: buka modal penempatan, pilih mode "Daftarkan Peserta Baru", verifikasi form identitas lengkap tampil.
2. Login sebagai `superadmin@magkom.go.id`:
   - Buka menu **Laporan & Ekspor**: verifikasi tab **Rekapitulasi Nilai Peserta** aktif dan dapat diunduh.
   - Buka menu **Arsip Peserta**: periksa tabel peserta selesai/nonaktif dan buka halaman detail arsip salah satu alumni.
   - Buka menu **Pengaturan Sistem**: periksa form batas predikat A, B, C, D dan simpan perubahan.
   - Buka menu **Jejak Audit Log**: uji filter tanggal dan tipe audit log.

---

## 7. Risiko & Mitigasi

- **Risiko Formula Injection pada Spreadsheet**:
  - *Mitigasi*: Seluruh string CSV yang diekspor melalui `ExportReportService` otomatis disanitasi dengan prefix kutip satu (`'`) jika diawali karakter `=`, `+`, `-`, atau `@` (AC-17-2).
- **Risiko Akses Nilai oleh Admin**:
  - *Mitigasi*: Penegakan hak akses pada level controller dan rute dengan middleware/policy otorisasi ketat (`abort_unless($user->isSuperAdmin(), 403)`).
- **Risiko Inkonsistensi Ambang Batas Nilai**:
  - *Mitigasi*: Validasi form memastikan `nilai_A > nilai_B > nilai_C > nilai_D > 0` dan tidak saling tumpang tindih.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Seluruh checklist langkah implementasi tercentang `- [x]`.
- [x] Halaman Hub Laporan & Ekspor (`/admin/laporan`) menyajikan pratinjau tabel dan opsi filter sebelum unduh CSV.
- [x] Tab dan file ekspor Rekapitulasi Nilai terlindungi khusus untuk role SuperAdmin.
- [x] Halaman Arsip Peserta (`/superadmin/arsip-peserta`) menyajikan rekam jejak alumni magang secara baca-saja.
- [x] Modal penempatan manual mendukung pendaftaran peserta baru tanpa akun sebelumnya.
- [x] Seluruh automated test di `AdminLaporanDanArsipTest` dan test suite eksisting lulus 100% green.
- [x] Tampilan konsisten dengan token Desain MagKom (Blue 700 `#0641CC`, Blue 600 `#0751FF`, Neutral `#F5F7FB`).

---

## 9. Catatan Perubahan & Saran Lanjutan

- **Perubahan selama pengerjaan**:
  - Penyesuaian query filter tahun pada `ArsipPesertaController` agar bersifat agnostik driver database (kompatibel baik MySQL `YEAR()` maupun SQLite `strftime()`).
  - Pembuatan akun peserta manual di `PlacementService` menggunakan instansiasi `new User()` untuk menjamin pengisian kolom `role = 'peserta'` dan `email_verified_at = now()` yang sengaja tidak dicantumkan di `$fillable` demi memenuhi guard keamanan NFR-S8.
  - Verifikasi otomatis fitur baru diuji melalui automated feature test [`tests/Feature/AdminLaporanDanArsipTest.php`](file:///g:/MAGKOM/tests/Feature/AdminLaporanDanArsipTest.php) dan seluruh 16 pengujian pada modul portal Admin/SuperAdmin lulus 100% green.
  - Verifikasi visual browser interaktif dilakukan pada `/admin/laporan`, `/superadmin/arsip-peserta`, dan `/superadmin/pengaturan` dengan status 200 OK dan responsif tanpa error.
- **Saran Lanjutan (Out of Scope)**:
  - Ekspor format binary `.xlsx` bawaan menggunakan library spreadsheet engine (jika diperlukan pada Rilis 3).
  - Grafik visual interaktif berbasis canvas/SVG untuk demografi asal kampus/sekolah peserta.
