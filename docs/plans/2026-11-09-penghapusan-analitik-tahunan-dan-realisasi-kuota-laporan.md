# Rencana Kerja: Penghapusan Menu Analitik Tahunan & Realisasi Kuota pada Modul Laporan dan Ekspor

Dokumen rencana kerja ini disusun untuk menuntaskan penghapusan (*decommissioning*) menu **Analitik Tahunan** dan **Realisasi Kuota** dari modul **Laporan & Ekspor** Administrasi serta menyelaraskan seluruh komponen terkait (route, controller, view, service, seeder, dan test suite) secara konsisten dan menyeluruh (*zero residual dead code*).

---

## 1. Tujuan
1. **Pembersihan Modul Laporan & Ekspor di Portal Administrasi**:
   - Menghapus tab navigasi **Realisasi Kuota & Slot** dari [`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php).
   - Menghapus tombol navigasi **Analitik Tahunan** dari tab switcher [`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php).
   - Menghapus tampilan konten tabel dan aksi ekspor Tab 3 (Realisasi Kuota Umum vs Slot Khusus) pada [`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php).
   - Menjadikan antarmuka Laporan & Ekspor terfokus secara presisi pada 2 pilar operasional utama: **Rekap Pendaftaran** dan **Data Penempatan**.
2. **Decommissioning Berkas & Route Backend**:
   - Menghapus berkas antarmuka analitik tahunan [`resources/views/admin/laporan/tahunan.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/tahunan.blade.php).
   - Menghapus berkas controller [`app/Http/Controllers/Admin/AnalitikTahunanController.php`](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/Admin/AnalitikTahunanController.php).
   - Menghapus route `admin.laporan.kuota`, `admin.laporan.tahunan`, dan `admin.laporan.tahunan.export` dari [`routes/web.php`](file:///d:/laragon/www/MAGKOM/routes/web.php).
   - Memangkas method `kuota()` dan query tab kuota dari [`app/Http/Controllers/Admin/LaporanController.php`](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/Admin/LaporanController.php).
   - Menghapus method `exportRealisasiKuota()` dari [`app/Services/ExportReportService.php`](file:///d:/laragon/www/MAGKOM/app/Services/ExportReportService.php).
3. **Penyelarasan Dashboard SuperAdmin & Pengaturan Sistem**:
   - Menghapus kartu ekspor "03: Realisasi Kuota" pada Hub Ekspor Instan di [`resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php).
   - Menghapus sakelar toggle `fitur_analitik_aktif` dari formulir pengaturan SuperAdmin di [`resources/views/superadmin/pengaturan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/superadmin/pengaturan/index.blade.php).
   - Membersihkan key konfigurasi `fitur_analitik_aktif` dari [`app/Http/Controllers/SuperAdmin/PengaturanController.php`](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/SuperAdmin/PengaturanController.php) dan [`database/seeders/PengaturanSeeder.php`](file:///d:/laragon/www/MAGKOM/database/seeders/PengaturanSeeder.php).
4. **Penyelarasan Automated Test Suite**:
   - Menghapus berkas test spesifik analitik tahunan [`tests/Feature/AdminAnalitikTahunanVisualTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminAnalitikTahunanVisualTest.php).
   - Menyesuaikan test case ekspor pada `AdminEksporLaporanExcelTest`, `AdminEksporLaporanXlsxTest`, `AdminLaporanDanArsipTest`, `ReleaseTwoFeaturesTest`, `SuperAdminFeatureToggleTest`, dan `SuperAdminUnifiedDashboardFeaturesTest` agar seluruh test suite tetap 100% lulus (hijau).

---

## 2. Asumsi & Batasan
- **Integritas Modul Kuota Master**: Penghapusan ini **HANYA** menyangkut tab/ekspor "Realisasi Kuota" pada menu *Laporan & Ekspor*. Menu utama **Manajemen Kuota Magang** (`admin.kuota.index`) dan fungsionalitas kuota di sistem pendaftaran **TETAP UTUH DAN TIDAK DISENTUH**.
- **Integritas Penempatan & Slot Khusus**: Alur verifikasi pendaftaran, penetapan penempatan, dan slot khusus jalur kemitraan tetap beroperasi normal seperti biasa.
- **Kepatuhan Aturan Blade & Frontend**: Penghapusan elemen Blade dilakukan secara bedah minimal tanpa menyisipkan CSS/JS inline, menjaga styling class BEM vanilla CSS yang sudah dikompilasi oleh Vite.
- **Zero Secrets & Zero Dead Code**: Seluruh file dan baris yang sudah tidak dipanggil dihapus sepenuhnya agar tidak meninggalkan utang teknis (*technical debt*).

---

## 3. Pendekatan Teknis & Evaluasi Alternatif

| Aspek | Alternatif A: Decommissioning Menyeluruh & Pembersihan Tuntas (Rekomendasi) | Alternatif B: Hapus Tombol/Tab Saja (Hidden / Ghost Code) |
| :--- | :--- | :--- |
| **Deskripsi** | Menghapus fisik file controller `AnalitikTahunanController`, view `tahunan.blade.php`, blok markup tab kuota, rute ekspor, method service, dan konfigurasi toggle di seeder. | Hanya menyembunyikan/menghapus tag `<a>` di Blade `index.blade.php`, namun membiarkan controller, rute, dan method service tetap ada. |
| **Kelebihan** | Codebase bersih dari *dead code*, tidak ada endpoint terbengkalai (*orphaned routes*), mengurangi attack surface keamanan, dan menghemat konsumsi memori. | Lebih cepat karena tidak perlu menyesuaikan test suite backend. |
| **Kekurangan** | Memerlukan penyelarasan beberapa file test yang menguji rute yang dihapus. | Menyisakan *ghost endpoints* yang masih bisa diakses via URL langsung, rawan inkonsistensi data, dan membingungkan developer. |
| **Keputusan** | **Dipilih Alternatif A**: Sesuai dengan prinsip arsitektur MAGKOM dalam menjaga stabilitas baseline teknis yang ramping dan konsisten. | Ditolak karena menyisakan residu dan melanggar prinsip kebersihan kode. |

---

## 4. Rencana Perubahan Berkas

### A. Berkas yang Dihapus (Delete)
| File | Aksi | Ringkasan Alasan |
| :--- | :---: | :--- |
| `app/Http/Controllers/Admin/AnalitikTahunanController.php` | **Delete** | Controller fitur analitik tahunan yang didecommission. |
| `resources/views/admin/laporan/tahunan.blade.php` | **Delete** | Template view antarmuka analitik tahunan yang didecommission. |
| `tests/Feature/AdminAnalitikTahunanVisualTest.php` | **Delete** | Test suite spesifik antarmuka analitik tahunan. |

### B. Berkas Backend & Routing yang Diubah (Modify)
| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `routes/web.php` | **Modify** | Hapus import `AnalitikTahunanController`, hapus route `laporan.kuota`, `laporan.tahunan`, dan `laporan.tahunan.export`. |
| `app/Http/Controllers/Admin/LaporanController.php` | **Modify** | Hapus method `kuota()`, pangkas cabang `$tab === 'kuota'`, hapus query kuota/slot data, dan batasi tab hanya `['pendaftaran', 'penempatan']`. |
| `app/Services/ExportReportService.php` | **Modify** | Hapus method `exportRealisasiKuota()`. |
| `app/Http/Controllers/SuperAdmin/PengaturanController.php` | **Modify** | Hapus key `fitur_analitik_aktif` dari validasi dan penanganan form pengaturan. |
| `database/seeders/PengaturanSeeder.php` | **Modify** | Hapus seeder key `fitur_analitik_aktif`. |

### C. Berkas Antarmuka Blade yang Diubah (Modify)
| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/admin/laporan/index.blade.php` | **Modify** | 1. Hapus tab link `Realisasi Kuota & Slot` dan tombol `Analitik Tahunan` pada tab switcher.<br>2. Hapus seluruh blok tab 3 `@if($tab === 'kuota') ... @endif`.<br>3. Sesuaikan kartu tile stat (Card 5: Realisasi Kuota Umum) agar metrik menjadi 4 kartu utama yang seimbang. |
| `resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php` | **Modify** | Hapus kartu ekspor ketiga (03: Realisasi Kuota) pada Hub Ekspor Instan Laporan Resmi Dinas. |
| `resources/views/superadmin/pengaturan/index.blade.php` | **Modify** | Hapus item sakelar toggle `Modul Analitik Tahunan Eksekutif` (`fitur_analitik_aktif`). |

### D. Berkas Test Suite yang Diselaraskan (Modify)
| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `tests/Feature/AdminEksporLaporanExcelTest.php` | **Modify** | Hapus method test `test_ekspor_realisasi_kuota_memuat_sep_directive`. |
| `tests/Feature/AdminEksporLaporanXlsxTest.php` | **Modify** | Hapus method test `test_ekspor_xlsx_kuota`. |
| `tests/Feature/AdminLaporanDanArsipTest.php` | **Modify** | Hapus pemanggilan assertion ekspor kuota pada method `test_export_laporan_pendaftaran_penempatan_kuota_csv`. |
| `tests/Feature/ReleaseTwoFeaturesTest.php` | **Modify** | Hapus assertion ekspor kuota pada `test_f17_laporan_ekspor_csv_dan_anti_formula_injection`. |
| `tests/Feature/SuperAdminFeatureToggleTest.php` | **Modify** | Hapus assertion pengujian `fitur_analitik_aktif`. |
| `tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php` | **Modify** | Selaraskan assertion Hub Ekspor Laporan Resmi Dinas dari 3 laporan menjadi 2 laporan resmi (Pendaftaran & Penempatan). |

---

## 5. Langkah-Langkah Implementasi Bertahap

- [ ] **Fase 1: Backend Services & Routing**
  - [ ] 1.1 Hapus method `exportRealisasiKuota()` pada [`app/Services/ExportReportService.php`](file:///d:/laragon/www/MAGKOM/app/Services/ExportReportService.php).
  - [ ] 1.2 Perbarui [`app/Http/Controllers/Admin/LaporanController.php`](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/Admin/LaporanController.php): hapus method `kuota()`, hapus blok query `$tab === 'kuota'`, dan batasi tab yang valid hanya `['pendaftaran', 'penempatan']`.
  - [ ] 1.3 Bersihkan rute di [`routes/web.php`](file:///d:/laragon/www/MAGKOM/routes/web.php): hapus route `laporan.kuota`, `laporan.tahunan`, `laporan.tahunan.export`, dan import `AnalitikTahunanController`.
  - [ ] 1.4 Hapus fisik file controller [`app/Http/Controllers/Admin/AnalitikTahunanController.php`](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/Admin/AnalitikTahunanController.php).
  - [ ] 1.5 Bersihkan referensi `fitur_analitik_aktif` di [`app/Http/Controllers/SuperAdmin/PengaturanController.php`](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/SuperAdmin/PengaturanController.php) dan [`database/seeders/PengaturanSeeder.php`](file:///d:/laragon/www/MAGKOM/database/seeders/PengaturanSeeder.php).

- [ ] **Fase 2: Presentasi Antarmuka (Blade Markup)**
  - [ ] 2.1 Hapus fisik file view [`resources/views/admin/laporan/tahunan.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/tahunan.blade.php).
  - [ ] 2.2 Edit [`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php):
    - Pangkas tab `Realisasi Kuota & Slot` dan tombol `Analitik Tahunan` dari tab switcher.
    - Hapus blok tampilan Tab 3 `@if($tab === 'kuota') ... @endif`.
    - Pangkas Card 5 Realisasi Kuota Umum pada barisan Tile Stat agar tampilan metrik tetap seimbang dan simetris (4 kartu: Total Pendaftaran, Diterima, Penempatan Aktif, Selesai).
  - [ ] 2.3 Edit [`resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php): hapus kartu ekspor realisasi kuota (Card 03).
  - [ ] 2.4 Edit [`resources/views/superadmin/pengaturan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/superadmin/pengaturan/index.blade.php): hapus blok sakelar toggle `Modul Analitik Tahunan Eksekutif`.

- [ ] **Fase 3: Penyelarasan Test Suite & Quality Gate**
  - [ ] 3.1 Hapus fisik file test [`tests/Feature/AdminAnalitikTahunanVisualTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminAnalitikTahunanVisualTest.php).
  - [ ] 3.2 Selaraskan pengujian ekspor kuota di:
    - [`tests/Feature/AdminEksporLaporanExcelTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminEksporLaporanExcelTest.php)
    - [`tests/Feature/AdminEksporLaporanXlsxTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminEksporLaporanXlsxTest.php)
    - [`tests/Feature/AdminLaporanDanArsipTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminLaporanDanArsipTest.php)
    - [`tests/Feature/ReleaseTwoFeaturesTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/ReleaseTwoFeaturesTest.php)
  - [ ] 3.3 Selaraskan pengujian SuperAdmin di:
    - [`tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php) (sesuaikan dari 3 link laporan resmi menjadi 2 laporan: Pendaftaran & Penempatan).
    - [`tests/Feature/SuperAdminFeatureToggleTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/SuperAdminFeatureToggleTest.php) (hapus assertion `fitur_analitik_aktif`).
  - [ ] 3.4 Jalankan seluruh quality gate:
    - `php artisan test` (harus 100% PASS, 0 fail).
    - `vendor/bin/pint --test` (harus clean/passed).
    - `npm run build` (harus sukses).

---

## 6. Strategi Testing

1. **Uji Fungsional Navigasi Laporan**:
   - Membuka URL `GET /admin/laporan` menghasilkan HTTP 200 dengan tab `pendaftaran` aktif secara default.
   - Mengklik tab `penempatan` membuka URL `GET /admin/laporan?tab=penempatan` dengan data penempatan valid.
   - Mencoba membuka URL lama `GET /admin/laporan?tab=kuota` secara otomatis fallback ke tab `pendaftaran` tanpa error PHP.
   - Mencoba membuka URL lama `GET /admin/laporan/tahunan` menghasilkan HTTP 404 Not Found.
   - Mencoba membuka URL lama `GET /admin/laporan/kuota` menghasilkan HTTP 404 Not Found.
2. **Uji Ekspor Laporan Resmi**:
   - Ekspor Laporan Pendaftaran (`/admin/laporan/pendaftaran`) format CSV dan XLSX berjalan normal.
   - Ekspor Laporan Penempatan (`/admin/laporan/penempatan`) format CSV dan XLSX berjalan normal.
3. **Uji SuperAdmin Dashboard & Pengaturan**:
   - Tab sistem dashboard SuperAdmin menampilkan 2 kartu ekspor instan tanpa tombol kuota yang rusak.
   - Halaman pengaturan SuperAdmin dapat disimpan tanpa key `fitur_analitik_aktif`.

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Link lama tersimpan di riwayat browser / bookmark user mengarah ke `laporan.tahunan` atau `laporan.kuota`. | Pengguna mendapatkan error jika tidak ditangani dengan benar. | Rute lama dihapus bersih sehingga browser mengembalikan HTTP 404 standar yang elegan, atau jika tab kuota diminta via query string `tab=kuota`, controller langsung fallback ke tab `pendaftaran`. |
| Kerusakan tata letak kartu metrik pada Laporan (`index.blade.php`). | Tampilan tidak seimbang jika kartu dihilangkan sembarangan. | Mengadaptasi Tile Stat menjadi 4 kartu metrik utama (Total Pendaftaran, Total Diterima, Penempatan Aktif, Penempatan Selesai) yang pas mengisi grid responsif. |
| Test suite lain gagal karena mencari rute `admin.laporan.kuota`. | CI GitHub Actions menjadi silang merah. | Seluruh referensi pada 6 file test yang relevan disisir dan diselaraskan secara teliti sebelum commit & push. |

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Tab `Realisasi Kuota & Slot` dan tombol `Analitik Tahunan` hilang dari halaman Laporan Administrasi.
- [ ] Konten tabel realisasi kuota dan tombol ekspor kuota tidak lagi ada di view.
- [ ] Berkas `AnalitikTahunanController.php` dan `tahunan.blade.php` telah dihapus dari repositori.
- [ ] Rute `admin.laporan.kuota` dan `admin.laporan.tahunan.*` telah dihapus dari `routes/web.php`.
- [ ] Hub Ekspor Instan di Dashboard SuperAdmin dan Pengaturan Sistem telah diselaraskan bersih.
- [ ] Seluruh unit dan feature tests (`php artisan test`) lulus 100% (0 failures).
- [ ] Format kode lolos uji Laravel Pint (`vendor/bin/pint --test`).
- [ ] Kompilasi aset Vite (`npm run build`) sukses tanpa error.
- [ ] Dibuat Conventional Commit rapi dan push ke branch `test-2`.

