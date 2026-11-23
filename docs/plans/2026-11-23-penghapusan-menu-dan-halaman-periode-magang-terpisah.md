# Rencana Kerja: Penghapusan Menu Sidebar dan Halaman Standalone Periode Magang

> **Dokumen Rencana Teknis**  
> **Tanggal**: 23 November 2026  
> **Status**: Menunggu Persetujuan User  
> **Skill Terkait**: `web-planning`  

---

## 1. Tujuan

Menghilangkan redundansi antarmuka pada portal Admin dengan:
1. Menghapus menu navigasi **"Periode Magang"** dari sidebar Admin (baik desktop sidebar maupun mobile drawer).
2. Menghapus / mengalihkan halaman standalone **"Manajemen Periode Magang"** (`/admin/bulan`) karena seluruh fungsionalitasnya (daftar tabel siklus pendaftaran, status periode, modal buat bulan, modal ubah bulan, dan toggle status aktif/nonaktif) telah terintegrasi secara penuh di **Manajemen Kuota** (`/admin/kuota?tab=bulan`).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. Seluruh operasi CRUD periode bulan magang (`store`, `update`, `toggleAktif`) tetap dipertahankan pada backend `Admin\BulanController` karena form modal di halaman Manajemen Kuota (`/admin/kuota`) mem-post aksi ke route tersebut dan me-redirect kembali ke `admin.kuota.index?tab=bulan`.
2. Akses langsung melalui URL `GET /admin/bulan` akan dialihkan (HTTP 302 Redirect) secara mulus menuju `route('admin.kuota.index', ['tab' => 'bulan'])` sehingga tidak ada *broken link* atau 404 bagi pengguna yang menyimpan bookmark lama.
3. Menu **"Periode Magang"** pada sidebar SuperAdmin (jika ada konfigurasi terpisah) disesuaikan agar alur navigasi konsisten.
4. Tautan cepat `+ Bulan Magang` pada dashboard diarahkan langsung ke tab periode di Manajemen Kuota.

---

## 3. Pendekatan Teknis & Evaluasi Opsi

### Evaluasi Pendekatan:
- **Opsi 1 (Rekomendasi - Seamless Redirection & UI Consolidation)**:
  - Menghapus item menu `Periode Magang` di [resources/views/layouts/dashboard.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/layouts/dashboard.blade.php).
  - Mengubah method `index()` pada [app/Http/Controllers/Admin/BulanController.php](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/Admin/BulanController.php) agar me-redirect langsung ke `route('admin.kuota.index', ['tab' => 'bulan'])`.
  - Mengarsipkan / menghapus file view standalone yang redundan [resources/views/admin/bulan/index.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/admin/bulan/index.blade.php).
  - Memperbarui tombol pintas di [resources/views/superadmin/dashboard.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/superadmin/dashboard.blade.php) menuju `admin.kuota.index?tab=bulan`.
  - Memperbarui assertion test terkait eksistensi menu sidebar dan redirect `admin.bulan.index`.
  - *Kelebihan*: Arsitektur sangat bersih, bebas redundansi, tanpa risiko *breaking changes* pada API/kontrak rute form modal.
- **Opsi 2 (Hard Route Deletion & Controller Removal)**:
  - Menghapus rute `GET /admin/bulan`, controller, dan view secara paksa.
  - *Kekurangan*: Berisiko tinggi memecah kontrak form modal di halaman kuota dan memicu kegagalan regresi luas.

**Keputusan**: Dipilih **Opsi 1**.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/layouts/dashboard.blade.php` | Modify | Menghapus item navigasi sidebar desktop `Periode Magang` (baris 392-402) dan sub-item mobile drawer (baris 489-492). |
| `app/Http/Controllers/Admin/BulanController.php` | Modify | Mengubah method `index()` untuk mengembalikan redirect 302 ke `route('admin.kuota.index', ['tab' => 'bulan'])`. |
| `resources/views/superadmin/dashboard.blade.php` | Modify | Memperbarui href `+ Bulan Magang` ke `route('admin.kuota.index', ['tab' => 'bulan'])`. |
| `resources/views/admin/bulan/index.blade.php` | Delete / Deprecate | Menghapus view standalone yang redundan karena antarmuka sudah menyatu di `resources/views/admin/kuota/index.blade.php`. |
| `tests/Feature/AdminMasterDataFeatureMigrationTest.php` | Modify | Menyesuaikan pengujian akses `admin.bulan.index` agar memverifikasi redirect ke `admin.kuota.index?tab=bulan`. |
| `tests/Feature/DashboardLayoutTest.php` | Modify | Menyesuaikan assertion sidebar admin agar memastikan menu `Periode Magang` sudah tidak muncul di sidebar terpisah. |
| `tests/Feature/SuperAdminNavigationReorganizationTest.php` | Modify | Menyesuaikan pengujian navigasi sidebar agar selaras dengan penghapusan menu standalone `Periode Magang`. |

---

## 5. Langkah Implementasi Bertahap

- [ ] **Langkah 1: Pembersihan Navigasi Sidebar Blade**
  - Buka [resources/views/layouts/dashboard.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/layouts/dashboard.blade.php).
  - Hapus link menu desktop `admin.bulan.index` ("Periode Magang") pada blok `MASTER & KONFIGURASI`.
  - Hapus link sub-menu mobile `admin.bulan.index` ("Periode Magang") pada accordion `Administrasi`.
- [ ] **Langkah 2: Redirect Rute `admin.bulan.index` ke Manajemen Kuota**
  - Buka [app/Http/Controllers/Admin/BulanController.php](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/Admin/BulanController.php).
  - Ubah method `index()` dari merender view `admin.bulan.index` menjadi `return redirect()->route('admin.kuota.index', ['tab' => 'bulan']);`.
  - Pastikan method `store`, `update`, dan `toggleAktif` default redirect-nya tetap mengarah ke `admin.kuota.index?tab=bulan`.
- [ ] **Langkah 3: Pembaruan Tautan Quick Action Dashboard**
  - Buka [resources/views/superadmin/dashboard.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/superadmin/dashboard.blade.php).
  - Arahkan tombol `+ Bulan Magang` ke `route('admin.kuota.index', ['tab' => 'bulan'])`.
- [ ] **Langkah 4: Penghapusan View Redundan `admin/bulan/index.blade.php`**
  - Hapus berkas [resources/views/admin/bulan/index.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/admin/bulan/index.blade.php) yang sudah tidak digunakan.
- [ ] **Langkah 5: Penyelarasan Test Suite & Quality Gate**
  - Perbarui assertion pada file test terkait (`AdminMasterDataFeatureMigrationTest`, `DashboardLayoutTest`, `SuperAdminNavigationReorganizationTest`).
  - Jalankan test suite PHPUnit dengan runtime PHP 8.3 aktual:
    `& "D:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" artisan test --filter=Bulan`
    `& "D:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" artisan test tests/Feature/DashboardLayoutTest.php tests/Feature/AdminKuotaRevampTest.php`
  - Jalankan `vendor/bin/pint --test` dan `vendor/bin/phpstan analyse --memory-limit=1G`.
  - Jalankan `npm run build`.

---

## 6. Strategi Testing

1. **Test Redirect Otomatis**:
   - `GET /admin/bulan` mengembalikan HTTP status 302 yang me-redirect pengguna ke `/admin/kuota?tab=bulan`.
2. **Test Sidebar Admin**:
   - `GET /admin/dashboard` dan `GET /admin/kuota` mengembalikan status 200 dan meng-assert `assertDontSee('Periode Magang')` di dalam elemen navigasi samping.
   - Menu lain di blok MASTER & KONFIGURASI (`Jalur Khusus`, `Data Jurusan`, `Unit Kerja`) tetap tampil utuh.
3. **Test Fungsionalitas Daur Hidup Bulan di Manajemen Kuota**:
   - Form pembuatan bulan baru, pembaruan bulan, dan tombol nonaktifkan/aktifkan periode di `/admin/kuota?tab=bulan` tetap berjalan sukses dan mencatat jejak audit.

---

## 7. Risiko & Mitigasi

- **Risiko**: Ada bookmark lama atau tautan lawas yang mengarah langsung ke `/admin/bulan`.  
  **Mitigasi**: Alihkan secara transparan melalui redirect 302 ke tab `bulan` di Manajemen Kuota sehingga pengguna langsung melihat tabel periode tanpa error 404.
- **Risiko**: Regression test yang sebelumnya mengasumsikan teks 'Periode Magang' di sidebar menjadi gagal.  
  **Mitigasi**: Seluruh file test terkait diinventarisasi dan diselaraskan pada langkah 5.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Menu "Periode Magang" tidak lagi tampil di sidebar desktop maupun mobile drawer Admin.
- [x] Halaman standalone `/admin/bulan` dialihkan secara mulus ke tab "Daftar Bulan Magang" pada `/admin/kuota`.
- [x] View `admin/bulan/index.blade.php` berhasil dihapus tanpa menimbulkan error view missing.
- [x] Seluruh operasi tambah periode, ubah periode, dan aktivasi bulan magang di Manajemen Kuota tetap berfungsi 100%.
- [x] Seluruh test suite terkait lulus hijau (`PASS`), Pint lulus, dan PHPStan lolos tanpa error.

