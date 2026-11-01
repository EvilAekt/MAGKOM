# Task Plan: Stabilisasi Backend & Eliminasi Data Mock pada Dashboard SuperAdmin

> **Peran Penulis**: Senior Lead Developer (Antigravity)  
> **Target Pelaksana**: Junior Developer / Model AI Eksekutor (e.g., Gemini 3.8 Flash)  
> **Status**: DRAFT (Menunggu Persetujuan Pengguna)  
> **Prinsip Utama**: **ZERO UI REGRESSION** (Tidak mengubah tampilan, struktur HTML, styling tema Gentelella, posisi grid, maupun estetika dashboard sama sekali. Hanya menghubungkan data riil backend, memperbaiki stabilitas logika, dan mengoptimalkan performa sesuai standar industri).

---

## 1. Ringkasan Eksekutif & Tujuan

Berdasarkan audit mendalam terhadap kode backend dan view pendukung Dashboard SuperAdmin (`/superadmin/dashboard`), ditemukan sejumlah fungsi backend yang **belum berjalan**, **menggunakan data dummy/mock statik**, **fallback data fiktif yang menyesatkan**, serta **potensi beban database tinggi (N+1 query loop)**.

Tujuan dari rencana kerja ini adalah:
1. Menghidupkan seluruh interaktivitas backend yang sebelumnya macet (seperti Slide-over Drawer Jejak Audit dan filter rentang grafik tren).
2. Mengganti semua data tiruan/mock dengan data database aktual secara konsisten.
3. Mengeliminasi bug logika fallback (misal angka 0 yang otomatis berubah menjadi 4 atau 2 karena operator `?:`).
4. Mengoptimalkan kueri agregasi SQL menjadi standar industri (dari 12 kueri sekuensial menjadi 1 kueri teragregasi).
5. Memastikan seluruh proses pembaruan data sistem terlindungi oleh transaksi basis data atomik (`DB::transaction`).

---

## 2. Hasil Audit Backend & Kritik Solusi (Sebelum Diputuskan)

Berikut adalah daftar temuan nyata dari audit kode beserta evaluasi kritis pro-kontra untuk menentukan solusi terbaik:

### Temuan 1: Slide-Over Drawer Jejak Audit Mati Total
- **Lokasi Kode**: `resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php:119` dan `public/js/superadmin-dashboard.js`.
- **Masalah**: Setiap baris audit log memiliki atribut `data-action="open-drawer" data-drawer="#drawerAuditDetail"`, namun skrip `public/js/superadmin-dashboard.js` sama sekali tidak memiliki event handler untuk `open-drawer` maupun `close-drawer`. Pengguna yang mengklik rekaman audit log di dashboard tidak mendapatkan respons apa pun.
- **Kritik Solusi**:
  - *Opsi A (Ditolak)*: Me-load library modal eksternal (Bootstrap JS modal / jQuery UI).  
    *Alasan*: Menambah beban aset, berpotensi konflik dengan modul lain, dan merusak performa.
  - *Opsi B (Ditolak)*: Me-load `public/js/dashboard-cockpit-unified.js`.  
    *Alasan*: File tersebut berisi skrip gabungan untuk role admin/pembimbing yang memiliki listener lain yang tidak relevan dengan SuperAdmin.
  - *Opsi C (Direkomendasikan & Terpilih)*: Tambahkan fungsi pembuka drawer vanilla JS yang bersih langsung ke dalam `public/js/superadmin-dashboard.js`, membaca atribut `data-user`, `data-ip`, `data-action-name`, `data-waktu`, dan memanipulasi class `.active` pada drawer dan backdrop. Ringan, nol dependensi, dan 100% tepat sasaran.

---

### Temuan 2: Data Kuota Jurusan di Tab Ringkasan Masih Hardcoded Statik
- **Lokasi Kode**: `resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php:200-215`.
- **Masalah**: Card 04 "Kuota per jurusan" masih menggunakan array PHP statik:
  ```php
  $sampleKuota = [
      ['Teknik Informatika', 10, 10],
      ['Sistem Informasi', 8, 10],
      ['DKV / Multimedia', 7, 10],
      ['Ilmu Komunikasi', 12, 20],
  ];
  ```
  Padahal controller telah menyediakan `$kuotaList` dari database nyata yang berisi data keterisian kuota bulan aktif.
- **Kritik Solusi**:
  - *Opsi Terpilih*: Hubungkan langsung perulangan ke `$kuotaList` (relasi `jurusan`). Tampilkan `nama_jurusan`, `terisi`, dan `jumlah` (kapasitas). Jika pada bulan tersebut belum ada kuota jurusan, tampilkan pesan informatif kosong (`empty state`) tanpa merusak layout kartu.

---

### Temuan 3: Fallback Palsu Nilai Nol (`?: 4`, `?: 2`) dan Teks Dummy
- **Lokasi Kode**:
  - `resources/views/superadmin/dashboard.blade.php:146, 156, 166, 176`
  - `resources/views/superadmin/dashboard/partials/_attention.blade.php:34-36`
  - `resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php:245`
- **Masalah**:
  - `{{ $antreanMelebihiSla ?: 4 }}` -> Jika kondisi operasional sempurna (0 berkas lewat SLA), kode justru menampilkan angka fiktif `4`!
  - `{{ $stafPendingGantiPassword ?: 2 }}` -> Jika 0 staf pending, sistem justru menampilkan `2`!
  - `<b>Kuota DKV penuh (10/10)</b>` -> Ditulis hardcoded mati, padahal belum tentu jurusan DKV yang penuh.
  - Chip nama pada hazard attention card (`Rizky Ananda +2h`, `Nadia Putri +1h`, `Yoga Pradana +1h`) di-hardcode mati sebagai teks statis.
- **Kritik Solusi**:
  - *Opsi Terpilih*:
    1. Ganti operator `?:` dengan nilai sebenarnya (`{{ $antreanMelebihiSla }}` dan `{{ $stafPendingGantiPassword }}`).
    2. Pada to-do list kuota penuh, ambil kuota jurusan yang memang sudah penuh (`terisi >= jumlah`) dari database. Jika tidak ada yang penuh, tampilkan ringkasan pemanfaatan kuota umum riil.
    3. Pada chip berkas terlama di attention card, ambil 3 pendaftar terlama di antrean verifikasi secara riil dari database (`$pendaftaranMenunggu->take(3)`), hitung selisih jamnya (`diffForHumans` / `diffInHours`), sehingga nama yang muncul adalah pendaftar yang benar-benar menunggu.

---

### Temuan 4: Cadangan Terakhir & Status Scheduler Bersifat Mock/Fiktif
- **Lokasi Kode**: `app/Services/SuperAdminExecutiveMetricsService.php:175, 374` dan `_tab-ringkasan.blade.php:251`.
- **Masalah**:
  - `'scheduler_active' => true` selalu di-hardcode `true` tanpa memvalidasi apakah cron job server aktif.
  - `'last_backup' => now()->subHours(6)->translatedFormat(...)` di-generate fiktif mundur 6 jam dari sekarang, dan di blade ditulis statik `09:47`.
  - Sementara itu, di `HealthMonitorController.php:95-103`, pembacaan file cadangan riil dari `storage/app/backups` sudah berjalan dengan benar.
- **Kritik Solusi**:
  - *Opsi Terpilih*:
    1. Samakan logika pembacaan cadangan di `SuperAdminExecutiveMetricsService` dengan `HealthMonitorController`: periksa file di `storage_path('app/backups')`. Jika ada file, ambil timestamp file terbarunya; jika kosong, tampilkan "Belum ada cadangan".
    2. Tampilkan waktu cadangan riil tersebut di Card Keamanan Tab Ringkasan menggantikan teks `09:47`.
    3. Cek status scheduler menggunakan cache heartbeat (misal `Cache::has('magkom:scheduler-heartbeat')`) atau fallback ke pengecekan file lock/tugas berkala, bukan `true` buta.

---

### Temuan 5: Filter Waktu Tren Pendaftaran (12 Bulan / 6 Bulan / 30 Hari) Macet
- **Lokasi Kode**: `resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php:342-346` dan `public/js/superadmin-dashboard.js:71-79`.
- **Masalah**: Tombol pill `12 Bulan`, `6 Bulan`, `30 Hari` hanya memancarkan event `magkom:trend-filter-changed`, namun tidak ada yang mendengarkan event tersebut. Kurva SVG digambar statis di Blade pada saat page load.
- **Kritik Solusi**:
  - *Opsi A (Ditolak)*: Mengubah seluruh chart menjadi Chart.js / ApexCharts.  
    *Alasan*: Melanggar prinsip *Zero UI Regression*, menambah dependensi JS eksternal, dan merusak paritas tema Gentelella yang sudah ada.
  - *Opsi B (Direkomendasikan & Terpilih)*: Sediakan payload data untuk 12m, 6m, dan 30d di backend atau endpoint JSON ringan `/superadmin/dashboard/trend-data`, lalu tambahkan fungsi kalkulasi ulang koordinat kurva SVG di JavaScript. Pengguna dapat berganti periode dengan transisi mulus tanpa reload halaman dan tanpa dependensi eksternal.

---

### Temuan 6: Loop 12 Query Sekuensial & Tanpa Caching Metrik
- **Lokasi Kode**: `app/Services/SuperAdminExecutiveMetricsService.php:394-410`.
- **Masalah**:
  ```php
  for ($i = 11; $i >= 0; $i--) {
      // Menjalankan kueri COUNT(*) ke database 12 KALI berturut-turut pada setiap page load!
      $count = Pendaftaran::whereBetween('created_at', [$start, $end])->count();
  }
  ```
  Total ada 40+ kueri database pada setiap reload dashboard SuperAdmin.
- **Kritik Solusi**:
  - *Opsi Terpilih*:
    1. Ganti loop 12 kueri dengan **1 kueri tunggal teragregasi**:
       ```sql
       SELECT DATE_FORMAT(created_at, '%Y-%m') as periode, COUNT(*) as total
       FROM pendaftarans
       WHERE created_at >= ?
       GROUP BY DATE_FORMAT(created_at, '%Y-%m')
       ```
       (dengan dukungan syntax SQLite `strftime` untuk environment testing).
    2. Pasang micro-caching (misal 60 detik) untuk metrik agregat yang berat menggunakan `Cache::remember()`, yang di-invalidate secara instan jika ada aksi mutasi audit log.

---

### Temuan 7: Snapshot Basis Data MySQL Kurang Lengkap
- **Lokasi Kode**: `app/Http/Controllers/SuperAdmin/BackupController.php:87-98`.
- **Masalah**: Pada environment MySQL, controller hanya menulis komentar nama tabel dan jumlah baris ke file `.sql`, tanpa mengekspor struktur DDL (`CREATE TABLE`) maupun baris DML (`INSERT INTO`). File cadangan tersebut tidak dapat digunakan untuk pemulihan bencana (disaster recovery).
- **Kritik Solusi**:
  - *Opsi Terpilih*: Lengkapi generator snapshot agar mengambil DDL tabel via `SHOW CREATE TABLE` dan mengekspor baris data tabel dalam bentuk query SQL valid secara aman (chunked records agar tidak terjadi Out of Memory / OOM).

---

### Temuan 8: Non-Atomic Update pada Pengaturan Sistem
- **Lokasi Kode**: `app/Http/Controllers/SuperAdmin/PengaturanController.php:148-156`.
- **Masalah**: Penyimpanan pengaturan dilakukan satu per satu dalam perulangan `foreach` tanpa dibungkus `DB::transaction()`. Jika ada kegagalan di tengah jalan, konfigurasi sistem berada dalam status korup/tidak lengkap.
- **Kritik Solusi**:
  - *Opsi Terpilih*: Bungkus seluruh blok penyimpanan pengaturan dan pencatatan audit log ke dalam `DB::transaction(function() { ... })`.

---

## 3. Rencana Perubahan File

| File | Aksi | Ringkasan Modifikasi |
| :--- | :---: | :--- |
| `public/js/superadmin-dashboard.js` | **Modify** | Tambah event handler `open-drawer` & `close-drawer`, serta handler kalkulasi kurva SVG filter tren (12m, 6m, 30d). |
| `app/Services/SuperAdminExecutiveMetricsService.php` | **Modify** | 1) Ganti loop 12 query di `resolveTrenPendaftar()` dengan single query agregasi `GROUP BY`.<br>2) Ambil cadangan terakhir aktual dari folder backup.<br>3) Sediakan 3 pendaftar tertunda terlama untuk attention hazard chips.<br>4) Ambil kuota penuh riil untuk todo-list. |
| `resources/views/superadmin/dashboard.blade.php` | **Modify** | Hapus fallback palsu `?: 4` dan `?: 2`. Hubungkan todo-list kuota dengan kuota jurusan penuh yang riil. |
| `resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php` | **Modify** | 1) Ganti `$sampleKuota` statis dengan `$kuotaList` dinamis.<br>2) Ganti jam backup hardcoded `09:47` dengan waktu riil dari service.<br>3) Hubungkan filter tren interaktif ke data dinamis. |
| `resources/views/superadmin/dashboard/partials/_attention.blade.php` | **Modify** | Ganti teks nama statis "Rizky Ananda" dengan pendaftar terlama aktual dari antrean database. |
| `app/Http/Controllers/SuperAdmin/PengaturanController.php` | **Modify** | Bungkus update konfigurasi dalam `DB::transaction()`. |
| `app/Http/Controllers/SuperAdmin/BackupController.php` | **Modify** | Lengkapi backup generator MySQL dengan DDL `SHOW CREATE TABLE` dan DML data. |
| `tests/Feature/SuperAdminBackendStabilityTest.php` | **Create** | Feature test komprehensif untuk memverifikasi seluruh perbaikan backend & data dinamis dashboard. |

---

## 4. Instruksi Langkah Kerja untuk Junior Developer / AI Assistant

Lakukan pekerjaan ini secara berurutan dan terstruktur:

### Task 1: Aktifkan Slide-Over Drawer Jejak Audit pada JS Dashboard
- [ ] Buka [public/js/superadmin-dashboard.js](file:///g:/MAGKOM/public/js/superadmin-dashboard.js).
- [ ] Tambahkan helper function `openDrawer(drawerSelector, triggerEl)` dan `closeAllDrawers()`.
- [ ] Petakan atribut:
  - `data-user` -> `.cv-dyn-user`
  - `data-action-name` -> `.cv-dyn-action`
  - `data-ip` -> `.cv-dyn-ip`
  - `data-waktu` -> `.cv-dyn-waktu`
- [ ] Tambahkan listener global pada `click` untuk elemen dengan `data-action="open-drawer"` dan `data-action="close-drawer"`, serta tombol keyboard `Escape`.
- [ ] Pastikan tidak ada dependensi eksternal yang diimpor.

### Task 2: Optimasi Kueri Agregasi Tren & Waktu Cadangan Riil di Metrics Service
- [ ] Buka [app/Services/SuperAdminExecutiveMetricsService.php](file:///g:/MAGKOM/app/Services/SuperAdminExecutiveMetricsService.php).
- [ ] Di metode `resolveTrenPendaftar()`:
  - Hilangkan perulangan `for ($i = 11; $i >= 0; $i--)` yang memanggil `Pendaftaran::whereBetween(...)->count()`.
  - Gunakan query builder teragregasi yang kompatibel dengan MySQL dan SQLite:
    - Ambil data dalam rentang 12 bulan terakhir.
    - Kelompokkan per bulan dan format labelnya (`M Y`).
- [ ] Di metode `resolveKpiAndSecurityData()`:
  - Cek folder `storage_path('app/backups')`.
  - Jika ada file backup, ambil `mtime` file terbaru dan format tanggalnya (`d M Y, H:i WIB`). Jika tidak ada, kembalikan `'Belum ada cadangan'`.
- [ ] Di metode `buildDashboardData()`:
  - Ambil 3 pendaftar tertua yang belum diverifikasi (`status = diajukan`) untuk mengisi data chip hazard attention (`pendaftarTertua`).
  - Identifikasi kuota jurusan yang paling kritis (`terisi >= jumlah` atau persentase tertinggi) untuk widget to-do list.

### Task 3: Hubungkan Data Kuota Riil & Eliminasi Fallback Palsu di Blade
- [ ] Buka [resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php](file:///g:/MAGKOM/resources/views/superadmin/dashboard/partials/_tab-ringkasan.blade.php):
  - Cari baris `$sampleKuota = [...]`. Hapus array statik tersebut.
  - Ganti dengan looping `$kuotaList` (maksimal 4-5 jurusan teratas). Tampilkan nama jurusan, jumlah terisi, dan kapasitas pagu secara riil.
  - Ganti nilai `09:47` pada baris "Backup terakhir" dengan variabel waktu riil dari `systemSummary['last_backup_short']`.
- [ ] Buka [resources/views/superadmin/dashboard.blade.php](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php):
  - Ubah `{{ $antreanMelebihiSla ?: 4 }}` menjadi `{{ $antreanMelebihiSla }}`.
  - Ubah `{{ $stafPendingGantiPassword ?: 2 }}` menjadi `{{ $stafPendingGantiPassword }}`.
  - Ubah `{{ $totalWaitingList ?: 4 }}` menjadi `{{ $totalWaitingList }}`.
  - Ubah item to-do kuota penuh agar menggunakan kuota kritis aktual dari service (atau sembunyikan bullet jika tidak ada kuota yang bermasalah).
- [ ] Buka [resources/views/superadmin/dashboard/partials/_attention.blade.php](file:///g:/MAGKOM/resources/views/superadmin/dashboard/partials/_attention.blade.php):
  - Ganti nama statik `Rizky Ananda`, `Nadia Putri`, `Yoga Pradana` dengan perulangan dari `$pendaftarTertua`. Tampilkan nama pendaftar dan durasi keterlambatan SLA aktualnya.

### Task 4: Fungsikan Filter Periode Tren Pendaftaran (12m, 6m, 30d)
- [ ] Di `SuperAdminExecutiveMetricsService.php`, siapkan array tren untuk periode 12 bulan, 6 bulan, dan 30 hari dalam format yang mudah diproses frontend.
- [ ] Di `public/js/superadmin-dashboard.js`, tangani event saat tombol `.gt-pill-btn` diklik:
  - Update titik kurva SVG (`primaryCurvePath`, `secondaryCurvePath`, dan `areaPath`) sesuai dataset periode yang dipilih tanpa mereload halaman.

### Task 5: Transaksi Atomik pada Pengaturan Sistem
- [ ] Buka [app/Http/Controllers/SuperAdmin/PengaturanController.php](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/PengaturanController.php).
- [ ] Di metode `update()`, bungkus seluruh penyimpanan data pengaturan dan pencatatan audit log di dalam `DB::transaction(function () use (...) { ... })`.

### Task 6: Penyempurnaan Backup Basis Data MySQL
- [ ] Buka [app/Http/Controllers/SuperAdmin/BackupController.php](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/BackupController.php).
- [ ] Perbaiki bagian driver non-sqlite (`MySQL`) pada metode `triggerSnapshot()`:
  - Lakukan loop tabel, ambil DDL skema menggunakan `DB::select("SHOW CREATE TABLE `{$tableName}`")`.
  - Ambil baris data per tabel secara terpotong (`chunk`) dan buat pernyataan `INSERT INTO` standar.
  - Tuliskan ke dalam file target backup.

---

## 5. Strategi Pengujian & Verifikasi

Junior Developer / AI harus memvalidasi hasil pekerjaan dengan checklist berikut:

1. **Pengujian Fungsional Otomatis**:
   - Buat file test baru: `tests/Feature/SuperAdminBackendStabilityTest.php`.
   - Uji skenario:
     - Dashboard SuperAdmin memuat tanpa error HTTP 500.
     - Metrik kuota per jurusan menampilkan data database riil dan bukan dummy `Teknik Informatika 10/10`.
     - Metrik fallback tidak lagi memunculkan angka palsu `4` atau `2` ketika nilai database adalah `0`.
     - Waktu backup terdeteksi dari file riil di folder backup.
     - Update pengaturan berhasil secara atomik dengan rollback jika terjadi exception.
2. **Pengujian Regresi Keseluruhan**:
   - Jalankan perintah:
     ```bash
     php artisan test --filter="SuperAdmin"
     ```
   - Seluruh 146+ pengujian yang sudah ada sebelumnya **wajib tetap lolos 100% (PASS / GREEN)**.
3. **Pemeriksaan Visual (Zero UI Regression)**:
   - Pastikan tidak ada elemen CSS yang bergeser, font yang berubah, atau warna yang melenceng dari standar tema Gentelella MagKom Sleman.

---

## 6. Kriteria Selesai (Definition of Done)

- [ ] Tidak ada lagi array `$sampleKuota` hardcoded di tab ringkasan.
- [ ] Tidak ada lagi operator `?: 4` atau fallback angka palsu di dashboard dan to-do list.
- [ ] Chip nama pendaftar di attention card menampilkan data pendaftar riil dari database.
- [ ] Slide-over drawer audit log dapat terbuka saat item audit diklik dan dapat ditutup kembali secara normal.
- [ ] Waktu backup dan status scheduler terhubung ke sistem riil tanpa data mock.
- [ ] Query tren bulanan teroptimasi menjadi 1 kueri teragregasi.
- [ ] Penyimpanan pengaturan terlindungi `DB::transaction()`.
- [ ] Backup MySQL menghasilkan file SQL yang memuat struktur dan data.
- [ ] Tampilan UI 100% identik dengan tampilan saat ini (Zero UI Regression).
- [ ] Seluruh unit test dan feature test SuperAdmin lulus 100% *green*.

