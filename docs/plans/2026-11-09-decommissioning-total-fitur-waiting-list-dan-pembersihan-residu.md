# Rencana Kerja: Decommissioning Total Fitur Waiting List dan Pembersihan Residu (Sertifikat & Survei Kepuasan)

Dokumen rencana kerja ini disusun untuk menuntaskan penghapusan (*decommissioning*) fitur **Waiting List** secara menyeluruh dari portal Administrasi dan sistem MagKom, serta membersihkan sisa artefak (*dead code/residu*) dari modul **Survei Kepuasan** dan **Sertifikat** yang sebelumnya telah didecommission pada commit `0e68cc9`.

---

## 1. Tujuan
1. **Decommissioning Total Fitur Waiting List di Portal Administrasi**:
   - Menghapus berkas controller [`app/Http/Controllers/Admin/WaitingListController.php`](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/Admin/WaitingListController.php).
   - Menghapus berkas antarmuka [`resources/views/admin/waiting-list/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/waiting-list/index.blade.php).
   - Menghapus pendaftaran route `admin.waiting-list.*` dari [`routes/web.php`](file:///d:/laragon/www/MAGKOM/routes/web.php).
   - Menghapus console command [`app/Console/Commands/TutupWaitingListExpiredCommand.php`](file:///d:/laragon/www/MAGKOM/app/Console/Commands/TutupWaitingListExpiredCommand.php) dan penjadwalannya di [`app/Console/Kernel.php`](file:///d:/laragon/www/MAGKOM/app/Console/Kernel.php).
   - Menghapus method-method bisnis waiting list di service backend: [`app/Services/PlacementService.php`](file:///d:/laragon/www/MAGKOM/app/Services/PlacementService.php) (`promosikanWaitingList()` & `tutupWaitingListKadaluarsa()`).
   - Menghapus tautan usang ke waiting list pada dashboard seleksi SuperAdmin: [`resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php).
2. **Pembersihan Residu Modul Survei Kepuasan & Sertifikat**:
   - Menghapus import dan pemanggilan model `SurveiKepuasan` di [`database/seeders/ProductionRealisticDataSeeder.php`](file:///d:/laragon/www/MAGKOM/database/seeders/ProductionRealisticDataSeeder.php) (mencegah error fatal `Class "App\Models\SurveiKepuasan" not found`).
   - Membersihkan teks deskripsi dan referensi survei kepuasan / sertifikat pada modal panduan alur publik ([`resources/views/public/partials/_peta_alur_modal.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/public/partials/_peta_alur_modal.blade.php)).
3. **Penyelarasan Automated Test Suite**:
   - Menghapus/menyesuaikan test case lama yang masih menguji endpoint `admin.waiting-list.*` dan artisan command `magkom:tutup-waiting-list` agar seluruh test suite aplikasi tetap hijau 100%.

---

## 2. Asumsi & Batasan
- **Integritas Riwayat Database**: Nilai enum `StatusPendaftaranEnum::WaitingList` tetap dipertahankan di file enum sebagai nilai pasif guna mencegah breaking data jika terdapat database historis lama, namun tidak ada lagi alur sistem aktif yang menetapkan pendaftaran baru ke status waiting list.
- **Prinsip Zero Dead Code**: Menghapus seluruh file dan blok kode yang sudah tidak lagi terpanggil oleh aplikasi (*clean codebase*).
- **Zero Secrets**: Tidak ada kredensial atau rahasia yang diubah dalam proses ini.

---

## 3. Pendekatan Teknis & Evaluasi Alternatif

| Aspek | Alternatif A: Decommissioning Menyeluruh & Pembersihan Tuntas (Rekomendasi) | Alternatif B: Soft Deactivation (Redirect Route Saja) |
| :--- | :--- | :--- |
| **Deskripsi** | Menghapus fisik file controller, view, route, command, method service, dan seeder yang sudah tidak relevan. | Membiarkan file tetap ada di codebase, tetapi route diarahkan (*redirect*) ke dashboard atau diberi respons 404. |
| **Kelebihan** | Codebase bersih dari *dead code*, mengurangi beban maintenance jangka panjang, tidak menimbulkan kebingungan bagi tim pengembang, menghilangkan risiko class hilang di seeder. | Tidak perlu menyentuh banyak berkas testing lama. |
| **Kekurangan** | Memerlukan penyelarasan test suite yang menguji route/command yang dihapus. | Meninggalkan berkas mubazir (*bloat/technical debt*) dan rawan error saat class yang dihapus dipanggil kembali. |
| **Keputusan** | **Dipilih Alternatif A**: Sesuai dengan arahan arsitektur MAGKOM untuk menjaga sistem tetap ramping, cepat, dan terpercaya. | Ditolak karena menyisakan utang teknis (*technical debt*). |

---

## 4. Rencana Perubahan Berkas

### A. Modul Waiting List yang Dihapus / Diubah
| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `app/Http/Controllers/Admin/WaitingListController.php` | **Delete** | Hapus berkas controller antrean waiting list admin. |
| `resources/views/admin/waiting-list/index.blade.php` | **Delete** | Hapus berkas template view waiting list admin. |
| `app/Console/Commands/TutupWaitingListExpiredCommand.php` | **Delete** | Hapus console command penutupan antrean waiting list kedaluwarsa. |
| `routes/web.php` | **Modify** | Hapus impor dan pendaftaran grup route `admin.waiting-list.*`. |
| `app/Console/Kernel.php` | **Modify** | Hapus baris penjadwalan command `magkom:tutup-waiting-list`. |
| `app/Services/PlacementService.php` | **Modify** | Hapus method `promosikanWaitingList()` dan `tutupWaitingListKadaluarsa()`. |
| `resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php` | **Modify** | Hapus link dan referensi ke `route('admin.waiting-list.index')`. |
| `app/Http/Controllers/Admin/PendaftaranController.php` | **Modify** | Pangkas perhitungan stat `'waiting_list'` yang tidak lagi ditampilkan. |

### B. Pembersihan Residu Survei Kepuasan & Sertifikat
| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `database/seeders/ProductionRealisticDataSeeder.php` | **Modify** | Hapus impor `use App\Models\SurveiKepuasan;` dan blok `SurveiKepuasan::updateOrCreate()`. |
| `resources/views/public/partials/_peta_alur_modal.blade.php` | **Modify** | Bersihkan teks deskripsi pengisian survei kepuasan & sertifikat, ganti dengan penerbitan lembar nilai/evaluasi resmi. |

### C. Penyesuaian Automated Test Suite
| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `tests/Feature/TutupWaitingListCommandTest.php` | **Delete** | Hapus test file karena command telah ditiadakan. |
| `tests/Feature/AdminChildPagesExtendedVisualTest.php` | **Modify** | Hapus test case `test_admin_can_view_waiting_list_index`. |
| `tests/Feature/AdminSuperAdminPhaseTwoTest.php` | **Modify** | Hapus test case pengujian route `admin.waiting-list.index`. |
| `tests/Feature/AdminSuperAdminPortalTest.php` | **Modify** | Hapus test case penutupan dan akses antrean waiting list. |
| `tests/Feature/AdminTablePaginationStandardTest.php` | **Modify** | Hapus test case pagination waiting list. |
| `tests/Feature/AdminTerminologyDesignSystemTest.php` | **Modify** | Hapus assertion view waiting list. |
| `tests/Feature/AdvancedRoleEnhancementPhaseSixTest.php` | **Modify** | Hapus test case promosi waiting list. |
| `tests/Feature/BackendSupplementalFeaturesTest.php` | **Modify** | Hapus test case penutupan antrean waiting list. |
| `tests/Feature/DashboardModulesRenderingTest.php` | **Modify** | Hapus test case perenderan view waiting list. |
| `tests/Feature/NextLevelEnhancementsTest.php` | **Modify** | Hapus test case penutupan waiting list bulan lalu. |

---

## 5. Langkah-Langkah Implementasi Bertahap

### Tahap 1: Penghapusan Rute, Controller, dan View Waiting List
- [x] Buka `routes/web.php`:
  - Hapus impor `use App\Http\Controllers\Admin\WaitingListController as AdminWaitingListController;`.
  - Hapus deklarasi route:
    - `Route::get('/waiting-list', ...)`
    - `Route::post('/waiting-list/promote', ...)`
    - `Route::post('/waiting-list/{bulanMagang}/tutup', ...)`
- [x] Hapus berkas controller: `app/Http/Controllers/Admin/WaitingListController.php`.
- [x] Hapus berkas view: `resources/views/admin/waiting-list/index.blade.php` (serta direktori `resources/views/admin/waiting-list/`).

### Tahap 2: Penghapusan Console Command & Pemangkasan Service Backend
- [x] Buka `app/Console/Kernel.php`:
  - Hapus penjadwalan `magkom:tutup-waiting-list`.
- [x] Hapus berkas command: `app/Console/Commands/TutupWaitingListExpiredCommand.php`.
- [x] Buka `app/Services/PlacementService.php`:
  - Hapus method `promosikanWaitingList()`.
  - Hapus method `tutupWaitingListKadaluarsa()`.
- [x] Buka `resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php`:
  - Hapus tautan link `route('admin.waiting-list.index')`.
- [x] Buka `app/Http/Controllers/Admin/PendaftaranController.php`:
  - Hapus baris hitungan `'waiting_list' => ...` pada array `$queueStats`.

### Tahap 3: Pembersihan Residu Survei Kepuasan & Sertifikat
- [x] Buka `database/seeders/ProductionRealisticDataSeeder.php`:
  - Hapus baris `use App\Models\SurveiKepuasan;`.
  - Hapus blok seeder `SurveiKepuasan::updateOrCreate(...)`.
  - Sesuaikan string entitas audit log dari `'SurveiKepuasan'` menjadi nama entitas aktif (misal: `'Pendaftaran'`).
- [x] Buka `resources/views/public/partials/_peta_alur_modal.blade.php`:
  - Perbarui deskripsi langkah evaluasi akhir: hapus teks kuesioner survei kepuasan dan sertifikat, fokuskan pada penilaian kompetensi dan penerbitan lembar nilai resmi.

### Tahap 4: Penyesuaian Automated Test Suite & Verifikasi
- [x] Hapus berkas test yang usang: `tests/Feature/TutupWaitingListCommandTest.php`.
- [x] Sesuaikan test suite yang memuat route `admin.waiting-list.*`:
  - `tests/Feature/AdminChildPagesExtendedVisualTest.php`
  - `tests/Feature/AdminSuperAdminPhaseTwoTest.php`
  - `tests/Feature/AdminSuperAdminPortalTest.php`
  - `tests/Feature/AdminTablePaginationStandardTest.php`
  - `tests/Feature/AdminTerminologyDesignSystemTest.php`
  - `tests/Feature/AdvancedRoleEnhancementPhaseSixTest.php`
  - `tests/Feature/BackendSupplementalFeaturesTest.php`
  - `tests/Feature/DashboardModulesRenderingTest.php`
  - `tests/Feature/NextLevelEnhancementsTest.php`
- [x] Jalankan pengujian visual admin: `php artisan test tests/Feature/AdminChildPagesVisualTest.php`.
- [x] Jalankan seluruh suite test pendaftaran: `php artisan test --filter Pendaftaran`.
- [x] Jalankan seluruh suite test penempatan: `php artisan test tests/Feature/AdminPenempatanDuaTahapTest.php`.
- [x] Jalankan full test suite aplikasi: `php artisan test`.
- [x] Jalankan linting PHP: `vendor/laravel/pint/builds/pint --test`.
- [x] Jalankan kompilasi aset Vite: `npm run build`.

---

## 6. Strategi Pengujian (Testing Strategy)
1. **Pemeriksaan Link dan Rute**:
   - Memastikan tidak ada link 404 atau `RouteNotFoundException` pada navigasi admin maupun superadmin.
2. **Pemeriksaan Seeder**:
   - Memastikan `php artisan db:seed --class=ProductionRealisticDataSeeder` (atau dry run) tidak lagi gagal karena pemanggilan class `SurveiKepuasan`.
3. **Automated Regression Suite**:
   - Seluruh test suite `php artisan test` wajib berjalan hijau (PASS) membuktikan bahwa penghapusan tidak merusak alur verifikasi berkas, kuota, maupun penempatan.

---

## 7. Risiko & Mitigasi
- **Risiko**: Terdapat file view lain yang masih memanggil `route('admin.waiting-list.index')` sehingga memicu error `RouteNotFoundException`.
  - **Mitigasi**: Audit menyeluruh via `git grep -n "waiting-list"` telah dilakukan; seluruh titik pemanggil sudah dipetakan dan akan dibersihkan tuntas di Tahap 1 dan 2.
- **Risiko**: Test suite PHPUnit gagal karena mencari route waiting list yang sudah dihapus.
  - **Mitigasi**: Seluruh 10 file test terkait sudah diinventarisasi dan akan diselaraskan pada Tahap 4.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Berkas `WaitingListController.php`, template `waiting-list/index.blade.php`, dan command `TutupWaitingListExpiredCommand.php` telah terhapus.
- [x] Rute `admin.waiting-list.*` telah terhapus dari `routes/web.php`.
- [x] Referensi model `SurveiKepuasan` telah dibersihkan dari `ProductionRealisticDataSeeder.php`.
- [x] Teks deskripsi survei dan sertifikat pada modal peta alur publik telah diselaraskan.
- [x] Seluruh test suite (`php artisan test`) berjalan 100% HIJAU (PASS).
- [x] Kompilasi aset `npm run build` sukses tanpa error.

