# Plan: Pengembangan Lanjutan Administrasi & SuperAdmin Sesuai PRD (Fase 2)

- **Tanggal**: 2026-10-10
- **Status**: Selesai / Terverifikasi Penuh
- **Author**: Antigravity (web-implementation)

---

## 1. Tujuan

Menyusun rencana kerja teknis untuk menyempurnakan dan melengkapi fitur lanjutan pada **Portal Administrasi (`/admin/*`)** dan **Portal SuperAdmin (`/superadmin/*`)** sesuai spesifikasi [01-PRD-MagKom.md](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (Bab 3.1, 3.3, 4.3, 5, 7, 8, dan 9.2), [02-TDD-MagKom.md](file:///g:/MAGKOM/docs/02-TDD-MagKom.md), dan [07-DESIGN-SYSTEM-MAGKOM.md](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md).

Cakupan 5 area utama pada fase lanjutan ini meliputi:
1. **Executive Dashboard untuk Admin & SuperAdmin (PRD 9.2 & 4.3)**:
   - Membangun antarmuka landing dashboard khusus Admin (`/admin/dashboard`) dengan metrik operasional harian (pengajuan perlu verifikasi, revisi dokumen, antrean waiting list, peserta aktif), visual progress bar kuota bulan berjalan, aksi cepat (quick actions), dan tabel 5 pendaftaran terbaru yang memerlukan tindak lanjut.
   - Membangun antarmuka landing dashboard khusus SuperAdmin (`/superadmin/dashboard`) dengan metrik eksekutif (total peserta aktif vs alumni, utilisasi staf pembimbing vs batas beban BR-13, slot khusus vs kuota umum), grafik/breakdown demografi asal sekolah/kampus dan proporsi jenjang SMK vs D3/S1 (PRD 4.3), feed audit log aktivitas mutasi 24 jam terakhir, dan indikator kesiapan sistem.
   - Mengarahkan login redirect `AuthController` langsung ke masing-masing dashboard.
2. **Monitoring Terpadu Pelaksanaan Magang Aktif untuk SuperAdmin (PRD 3.1, 3.3, AC-07-7, AC-08-5, AC-10-7)**:
   - Membangun modul `/superadmin/monitoring-peserta` agar SuperAdmin dapat memantau penempatan aktif secara menyeluruh (daftar tugas, progres, logbook harian peserta dengan lampiran PDF/tautan https, dan skor rubrik draft/final).
   - Seluruh halaman dan aksi pada modul ini bersifat **strictly read-only** dengan proteksi HTTP 403 Forbidden bila ada upaya mutasi oleh SuperAdmin pada tugas/logbook/penilaian (AC-08-5, AC-10-7).
3. **Penegakan Aturan Bisnis BR-12 & AC-11-2 pada Penonaktifan Master Data & Staf**:
   - Menambahkan guard validasi pada `StafController::toggleAktif`: menolak penonaktifan pembimbing jika masih memiliki peserta magang aktif atau jika merupakan satu-satunya pembimbing aktif dalam sistem (BR-12 & AC-11-2).
   - Menambahkan guard validasi pada `BulanController::toggleAktif`: menolak penonaktifan periode bulan magang jika masih terdapat pendaftaran aktif atau penempatan aktif.
   - Menambahkan guard validasi pada `KuotaController::toggleAktif`: menolak penonaktifan kuota jurusan jika masih memiliki pendaftaran aktif atau penempatan aktif (AC-05-6).
4. **Penyelarasan Navigasi Sidebar SuperAdmin & Akses Laporan (PRD 3.3, 9.2, F-17)**:
   - Menambahkan menu "Dashboard" di posisi teratas sidebar navigasi untuk Admin dan SuperAdmin.
   - Menambahkan tautan menu "Laporan & Ekspor" pada sidebar SuperAdmin (karena SuperAdmin merupakan satu-satunya role yang berhak melihat dan mengunduh Rekap Nilai & Kelulusan per PRD 3.3).
   - Menambahkan menu "Penempatan Magang" dan "Monitoring Peserta" pada sidebar navigasi SuperAdmin.
5. **Penyempurnaan Alur Promosi Waiting List (BR-05, BR-06, AC-04-6)**:
   - Menambahkan filter periode bulan dan jurusan pada halaman `/admin/waiting-list`.
   - Mengintegrasikan modal jalan pintas (shortcut) "Promosikan & Tempatkan Langsung" pada baris antrean teratas ketika kuota jurusan tersedia (sisa kuota > 0), sehingga verifikator dapat menetapkan pembimbing lapangan dan bidang penempatan tanpa alur berbelit.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi:
1. Dashboard Admin berfokus pada operasional pemrosesan berkas harian dan kuota, sedangkan Dashboard SuperAdmin berfokus pada ringkasan tata kelola, beban staf, dan kesehatan sistem.
2. SuperAdmin diizinkan melihat seluruh penempatan peserta aktif (`status: aktif`) melalui modul monitoring baca-saja, melengkapi modul arsip peserta (`/superadmin/arsip-peserta`) yang khusus untuk alumni selesai/nonaktif.
3. Penonaktifan pembimbing, bulan magang, dan kuota harus selalu divalidasi di sisi server (controller) dan memberikan pesan error deskriptif sesuai BR-12.
4. Perhitungan demografi asal institusi pada dashboard SuperAdmin dikelompokkan berdasarkan `institusi` pada profil peserta dengan limit 5 teratas.

### Pertanyaan Terbuka:
- Tidak ada pertanyaan terbuka kritis. Seluruh spesifikasi telah tertuang jelas dalam PRD Bab 3.1, 3.3, 4.3, 5 (BR-05, BR-06, BR-12, BR-13), 8, dan 9.2.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Arsitektur:

| Area | Opsi 1 (Direkomendasikan) | Opsi 2 (Alternatif) | Alasan Pemilihan Opsi 1 |
| :--- | :--- | :--- | :--- |
| **Penyediaan Dashboard Admin & SuperAdmin** | **Dedicated Controller & View terpisah (`Admin\DashboardController` & `SuperAdmin\DashboardController`)** | Menggunakan satu controller dashboard bersama dengan percabangan role | Memisahkan controller menjamin pemisahan tanggung jawab (SoC), memudahkan caching metrik yang berbeda, dan menjaga keamanan otorisasi middleware per-role. |
| **Monitoring Magang Aktif SuperAdmin** | **Modul Khusus `/superadmin/monitoring-peserta` dengan Tab Bimbingan Baca-Saja** | Mengizinkan SuperAdmin masuk ke rute `/pembimbing/*` dengan role tambahan | PRD 3.1 & 3.3 melarang keras SuperAdmin memiliki hak ubah pada fungsi Pembimbing (403 bila membuat tugas/menilai). Memberikan antarmuka khusus menjamin tombol edit/tulis tidak tampil sama sekali di UI. |
| **Penegakan Validasi BR-12** | **Guard Validasi di Controller Sebelum Toggle dengan Query Agregasi Bersih** | Database Trigger / Stored Procedure | Lebih mudah diuji (unit/feature testing), pesan error berbahasa Indonesia dapat disampaikan langsung ke session flash Blade, dan portabel lintas driver database (SQLite/MySQL). |

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`routes/web.php`](file:///g:/MAGKOM/routes/web.php) | Modify | Menambahkan rute `admin.dashboard`, `superadmin.dashboard`, dan rute `superadmin.monitoring.*`. |
| [`app/Http/Controllers/Auth/AuthController.php`](file:///g:/MAGKOM/app/Http/Controllers/Auth/AuthController.php) | Modify | Mengarahkan redirect login SuperAdmin ke `superadmin.dashboard` dan Admin ke `admin.dashboard`. |
| [`app/Http/Controllers/Admin/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/Admin/DashboardController.php) | Create | Controller dashboard eksekutif Admin: agregasi metrik pengajuan, waiting list, utilisasi kuota bulan berjalan, dan 5 pendaftaran terbaru. |
| [`resources/views/admin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/admin/dashboard.blade.php) | Create | Tampilan Blade Dashboard Admin: kartu ringkasan KPI operasional, progress bar kuota jurusan, aksi cepat, dan tabel antrean mendesak. |
| [`app/Http/Controllers/SuperAdmin/DashboardController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/DashboardController.php) | Create | Controller dashboard eksekutif SuperAdmin: agregasi metrik peserta vs alumni, beban pembimbing, utilisasi slot khusus, demografi institusi, dan log audit terkini. |
| [`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php) | Create | Tampilan Blade Dashboard SuperAdmin: kartu metrik eksekutif, status sistem, widget demografi institusi/jenjang, dan feed jejak audit 24 jam. |
| [`app/Http/Controllers/SuperAdmin/MonitoringPesertaController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/MonitoringPesertaController.php) | Create | Controller monitoring pelaksanaan magang aktif untuk SuperAdmin (daftar peserta aktif & detail tugas/logbook/nilai baca-saja). |
| [`resources/views/superadmin/monitoring/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/monitoring/index.blade.php) | Create | Tampilan daftar peserta aktif yang sedang bimbingan dengan filter pembimbing, jurusan, dan bulan. |
| [`resources/views/superadmin/monitoring/show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/monitoring/show.blade.php) | Create | Tampilan detail pemantauan bimbingan (tugas, logbook harian + lampiran, dan nilai berjalan) dalam mode baca-saja. |
| [`app/Http/Controllers/SuperAdmin/StafController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/StafController.php) | Modify | Menambahkan validasi penolakan penonaktifan pembimbing jika memiliki mentee aktif atau merupakan pembimbing tunggal (BR-12 & AC-11-2). |
| [`app/Http/Controllers/SuperAdmin/BulanController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/BulanController.php) | Modify | Menambahkan validasi penolakan penonaktifan bulan magang jika memiliki pendaftaran/penempatan aktif (BR-12). |
| [`app/Http/Controllers/Admin/KuotaController.php`](file:///g:/MAGKOM/app/Http/Controllers/Admin/KuotaController.php) | Modify | Menambahkan validasi penolakan penonaktifan kuota jurusan jika memiliki pendaftaran/penempatan aktif (BR-12, AC-05-6). |
| [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php) | Modify | Menambahkan item "Dashboard" untuk Admin & SuperAdmin, serta menambahkan menu "Laporan & Ekspor", "Penempatan Magang", dan "Monitoring Peserta" pada sidebar SuperAdmin. |
| [`resources/views/admin/waiting-list/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/waiting-list/index.blade.php) | Modify | Menambahkan filter jurusan & bulan, serta modal pintas promosi instan ketika kuota tersedia. |
| [`tests/Feature/AdminSuperAdminPhaseTwoTest.php`](file:///g:/MAGKOM/tests/Feature/AdminSuperAdminPhaseTwoTest.php) | Create | Test feature komprehensif untuk dashboard Admin/SuperAdmin, monitoring peserta aktif, penegakan BR-12 pada toggle staf/bulan/kuota, dan akses laporan SuperAdmin. |

---

## 5. Langkah Implementasi

*Catatan: Seluruh tahapan di bawah ini telah berhasil diimplementasikan dan diverifikasi.*

### Modul 1: Dashboard Operasional Administrasi (`/admin/dashboard`)
- [x] **Langkah 1.1**: Buat controller `app/Http/Controllers/Admin/DashboardController.php`:
  - Agregasikan data: total pendaftaran `diajukan`, total `perlu_perbaikan`, total `waiting_list`, total penempatan `aktif`.
  - Hitung rekapitulasi keterisian kuota bulan berjalan per jurusan (`terisi` vs `jumlah`).
  - Ambil 5 pengajuan terbaru yang berstatus `diajukan` atau `perlu_perbaikan`.
- [x] **Langkah 1.2**: Daftarkan rute di `routes/web.php`: `GET /admin/dashboard` dengan nama `admin.dashboard`.
- [x] **Langkah 1.3**: Buat antarmuka Blade `resources/views/admin/dashboard.blade.php`:
  - Tampilkan 4 summary stat cards (Pengajuan Menunggu, Revisi Berkas, Antrean Waiting List, Peserta Magang Aktif).
  - Tampilkan panel Visualisasi Keterisian Kuota Bulan Berjalan (persentase progress bar per jurusan).
  - Tampilkan Quick Action Buttons (Verifikasi Berkas Baru, Atur Kuota, Input Peserta Manual, Unduh Rekap Laporan).
  - Tampilkan tabel 5 pendaftaran terbaru yang memerlukan tindakan dengan tombol shortcut "Tinjau Berkas".

### Modul 2: Dashboard Eksekutif SuperAdmin (`/superadmin/dashboard`)
- [x] **Langkah 2.1**: Buat controller `app/Http/Controllers/SuperAdmin/DashboardController.php`:
  - Agregasikan data: total peserta aktif, total alumni (`selesai`), total staf aktif (Admin dan Pembimbing).
  - Hitung utilisasi slot khusus bulan berjalan vs kuota umum.
  - Hitung beban rata-rata peserta per pembimbing dan bandingkan dengan pengaturan `maks_peserta_aktif_per_pembimbing`.
  - Hitung 5 institusi/kampus asal peserta terbanyak dan proporsi jenjang pendidikan (SMK vs D3/S1).
  - Ambil 5 entri jejak audit trail mutasi terbaru.
- [x] **Langkah 2.2**: Daftarkan rute di `routes/web.php`: `GET /superadmin/dashboard` dengan nama `superadmin.dashboard`.
- [x] **Langkah 2.3**: Buat antarmuka Blade `resources/views/superadmin/dashboard.blade.php`:
  - Tampilkan kartu metrik eksekutif MagKom Sleman.
  - Tampilkan widget demografi pendidikan & 5 kampus/sekolah teratas.
  - Tampilkan widget beban pembimbing lapangan (indikator kapasitas mentee aktif).
  - Tampilkan feed log aktivitas mutasi 24 jam terakhir dengan tautan ke audit log lengkap.

### Modul 3: Monitoring Pelaksanaan Magang Aktif SuperAdmin (`/superadmin/monitoring-peserta`)
- [x] **Langkah 3.1**: Buat controller `app/Http/Controllers/SuperAdmin/MonitoringPesertaController.php`:
  - Method `index()`: query penempatan berstatus `aktif` dengan relasi `peserta.profil.jurusan`, `pembimbing`, `bidang`, dan `kuota.bulanMagang`. Dukung filter pembimbing, jurusan, dan pencarian nama peserta.
  - Method `show(Penempatan $penempatan)`: tampilkan profil, ringkasan penempatan, daftar tugas (beserta status dan review), logbook harian, dan snapshot penilaian.
  - Pastikan tidak ada method aksi tulis (update/store/destroy) untuk tugas/logbook/penilaian pada controller ini.
- [x] **Langkah 3.2**: Daftarkan rute di `routes/web.php` di bawah grup `role:superadmin`:
  - `GET /superadmin/monitoring-peserta` -> `MonitoringPesertaController@index` (name: `superadmin.monitoring.index`).
  - `GET /superadmin/monitoring-peserta/{penempatan}` -> `MonitoringPesertaController@show` (name: `superadmin.monitoring.show`).
- [x] **Langkah 3.3**: Buat tampilan Blade `resources/views/superadmin/monitoring/index.blade.php` dan `resources/views/superadmin/monitoring/show.blade.php`:
  - Tampilan index dengan badge progress bimbingan, pembimbing lapangan, durasi, dan aksi "Lihat Progres".
  - Tampilan detail dengan tab Bimbingan Baca-Saja (Tugas, Logbook Harian, Penilaian Berjalan) dan tombol unduh Surat Keterangan Diterima & Lembar Nilai.

### Modul 4: Penegakan Aturan Bisnis BR-12 & AC-11-2 pada Penonaktifan Master Data & Staf
- [x] **Langkah 4.1**: Perbarui `app/Http/Controllers/SuperAdmin/StafController.php` method `toggleAktif`:
  - Cek jika `$targetAktif === false` dan role adalah `pembimbing`:
    1. Periksa apakah pembimbing memiliki penempatan berstatus `aktif`. Jika ya, gagalkan dengan pesan error: *"Penonaktifan ditolak (BR-12): Pembimbing masih memiliki penempatan peserta aktif. Pindahkan penempatan terlebih dahulu."*
    2. Periksa apakah jumlah pembimbing aktif di sistem `<= 1`. Jika ya, gagalkan dengan pesan error: *"Penonaktifan ditolak (BR-12): Tidak dapat menonaktifkan satu-satunya pembimbing aktif dalam sistem."*
- [x] **Langkah 4.2**: Perbarui `app/Http/Controllers/SuperAdmin/BulanController.php` method `toggleAktif`:
  - Cek jika `$targetAktif === false`: periksa apakah periode bulan memiliki pendaftaran aktif (`diajukan`, `waiting_list`, `perlu_perbaikan`, `berkas_belum_lengkap`) atau penempatan aktif. Jika ya, gagalkan dengan pesan error: *"Penonaktifan ditolak (BR-12): Periode bulan magang masih memiliki pendaftaran atau penempatan aktif."*
- [x] **Langkah 4.3**: Perbarui `app/Http/Controllers/Admin/KuotaController.php` method `toggleAktif`:
  - Cek jika `$targetAktif === false`: periksa apakah kuota jurusan memiliki pendaftaran aktif atau penempatan aktif. Jika ya, gagalkan dengan pesan error: *"Penonaktifan ditolak (BR-12, AC-05-6): Kuota jurusan masih memiliki pendaftaran atau penempatan aktif."*

### Modul 5: Penyelarasan Navigasi Sidebar & Alur Waiting List
- [x] **Langkah 5.1**: Perbarui `app/Http/Controllers/Auth/AuthController.php`:
  - Ubah target redirect login:
    - SuperAdmin -> `route('superadmin.dashboard')`
    - Admin -> `route('admin.dashboard')`
- [x] **Langkah 5.2**: Perbarui navigasi sidebar di `resources/views/layouts/dashboard.blade.php`:
  - Tambahkan menu "Dashboard" di navigasi Admin (`admin.dashboard`).
  - Tambahkan menu "Dashboard" di navigasi SuperAdmin (`superadmin.dashboard`).
  - Tambahkan menu "Monitoring Peserta" (`superadmin.monitoring.index`) dan "Penempatan Magang" (`admin.penempatan.index`) di navigasi SuperAdmin.
  - Tambahkan menu "Laporan & Ekspor" (`admin.laporan.index`) di navigasi SuperAdmin.
- [x] **Langkah 5.3**: Perbarui `resources/views/admin/waiting-list/index.blade.php`:
  - Tambahkan filter dropdown `bulan_magang_id` dan `jurusan_id`.
  - Lengkapi baris antrean teratas yang memiliki sisa kuota dengan modal/link promosi langsung.

### Modul 6: Automated Testing & Verifikasi
- [x] **Langkah 6.1**: Buat automated feature test `tests/Feature/AdminSuperAdminPhaseTwoTest.php` untuk memverifikasi seluruh fungsionalitas baru.
- [x] **Langkah 6.2**: Jalankan seluruh test suite (`php artisan test`) untuk memastikan tidak terjadi regresi fungsionalitas.
- [x] **Langkah 6.3**: Verifikasi visual browser interaktif pada dashboard Admin, dashboard SuperAdmin, dan modul monitoring bimbingan.

---

## 6. Strategi Testing

### Automated Feature Test (`tests/Feature/AdminSuperAdminPhaseTwoTest.php`):
1. `test_admin_can_access_dashboard_with_operational_metrics`: Memastikan `/admin/dashboard` memuat metrik pengajuan, waiting list, dan progres kuota.
2. `test_superadmin_can_access_dashboard_with_executive_metrics`: Memastikan `/superadmin/dashboard` memuat ringkasan peserta vs alumni, beban pembimbing, demografi, dan log mutasi.
3. `test_superadmin_can_view_monitoring_peserta_aktif_read_only`: Memastikan SuperAdmin dapat meninjau peserta aktif, tugas, dan logbook tanpa hak mutasi.
4. `test_superadmin_cannot_deactivate_pembimbing_with_active_mentees`: Memastikan aturan BR-12 mencegah penonaktifan pembimbing yang memiliki peserta aktif.
5. `test_superadmin_cannot_deactivate_sole_active_pembimbing`: Memastikan aturan BR-12 mencegah penonaktifan bila pembimbing tersebut adalah satu-satunya staf pembimbing aktif.
6. `test_cannot_deactivate_bulan_or_kuota_with_active_registrations`: Memastikan penonaktifan bulan atau kuota ditolak jika ada pendaftaran atau penempatan aktif.
7. `test_superadmin_can_access_laporan_and_rekap_nilai_from_sidebar`: Memastikan SuperAdmin dapat membuka halaman Hub Laporan dan mengunduh rekap nilai.

### UI & Manual Verification:
1. Login sebagai `admin@magkom.go.id`:
   - Verifikasi diarahkan langsung ke `/admin/dashboard`.
   - Periksa kartu metrik operasional dan progres bar kuota bulan aktif.
   - Uji tombol filter pada halaman Waiting List.
2. Login sebagai `superadmin@magkom.go.id`:
   - Verifikasi diarahkan langsung ke `/superadmin/dashboard`.
   - Buka menu **Monitoring Peserta**: periksa daftar bimbingan aktif dan detail baca-saja.
   - Buka menu **Manajemen Staf**: coba nonaktifkan pembimbing yang memiliki mentee aktif, verifikasi alert penolakan BR-12 muncul rapi.
   - Buka menu **Laporan & Ekspor**: verifikasi tab Rekap Nilai dapat diakses.

---

## 7. Risiko & Mitigasi

- **Risiko Beban Kueri Agregasi Dashboard**:
  - *Mitigasi*: Gunakan query agregasi efisien (`withCount`, eager loading, dan pagination) agar waktu respon halaman tetap di bawah 200 ms tanpa overhead memori.
- **Risiko Akses Tulis SuperAdmin pada Bimbingan**:
  - *Mitigasi*: Controller `MonitoringPesertaController` hanya menyediakan method `index` dan `show`. Tidak ada rute POST/PUT/DELETE yang didaftarkan untuk bimbingan di bawah SuperAdmin, menjaga penegakan AC-08-5 dan AC-10-7.
- **Risiko Deadlock pada Pengecekan BR-12**:
  - *Mitigasi*: Gunakan query pembacaan langsung tanpa lock agresif karena operasi toggle aktif hanya dilakukan sesekali oleh SuperAdmin.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Seluruh checklist langkah implementasi tercentang `- [x]`.
- [x] Halaman Dashboard Admin (`/admin/dashboard`) dan SuperAdmin (`/superadmin/dashboard`) berhasil dibangun dan menyajikan data akurat.
- [x] Halaman Monitoring Magang Aktif (`/superadmin/monitoring-peserta`) menyajikan rekam jejak bimbingan peserta aktif secara baca-saja.
- [x] Penegakan aturan bisnis BR-12 dan AC-11-2 terverifikasi aktif pada penonaktifan staf, bulan magang, dan kuota.
- [x] Navigasi sidebar SuperAdmin dan Admin selaras dengan hak akses matriks PRD 3.3 dan 9.2.
- [x] Seluruh automated feature test di `AdminSuperAdminPhaseTwoTest.php` dan test suite eksisting lulus 100% green.
- [x] Desain visual mematuhi token MagKom Sleman (Blue 700 `#0641CC`, Blue 600 `#0751FF`, Neutral `#F5F7FB`).

---

## 9. Catatan Perubahan & Saran Lanjutan

- **Perubahan selama pengerjaan**:
  - **Relasi Model Penempatan**: Model `Penempatan` berelasi ke bulan magang melalui `kuota.bulanMagang`, dan relasi logbook bernama `logbooks()` (plural). Eager loading pada `MonitoringPesertaController` dan template telah diselaraskan.
  - **Relasi Model Tugas**: Log progres pada `Tugas` dinamai `progresLogs()`, disesuaikan pemanggilannya pada penghitungan progres di tampilan detail monitoring.
  - **Enum Jenjang**: Menggunakan `JenjangEnum::Smk` sesuai definisi kasus enum (PascalCase).
- **Saran Lanjutan (Out of Scope)**:
  - Ekspor grafik demografi ke format PDF/PNG.
  - Widget grafik interaktif menggunakan Chart.js/ApexCharts jika diizinkan oleh Product Owner pada rilis mendatang.
