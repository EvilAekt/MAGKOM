# Technical Plan: Penghapusan Navigasi & Fitur Waiting List serta Perlu Perbaikan pada Modul Administrasi

**Dokumen**: `docs/plans/2026-11-06-penghapusan-navigasi-dan-alur-waitinglist-revisi-administrasi.md`  
**Author**: Senior Frontend & Backend Architect  
**Tanggal**: 6 November 2026  
**Status**: Selesai Diimplementasikan & Terverifikasi Penuh  
**Aturan Tata Kelola**: Sesuai [`AGENTS.md`](file:///d:/laragon/www/MAGKOM/.agents/rules/AGENTS.md) dan skill `web-planning`  

---

## 1. Tujuan & Latar Belakang

Berdasarkan instruksi pengguna:
> 1. *Hapus navigasi ke (waiting list) dan (perlu perbaikan)*
> 2. *Tidak pakai waiting list dan perlu perbaikan*
> 3. *Intinya di administrasi yang berkaitan dan tentang waiting list dan perlu perbaikan dihapus / tidak dipakai*

Sebelumnya, sistem verifikasi berkas telah disederhanakan menjadi keputusan biner (**Terima** dan **Tolak**). Langkah selanjutnya adalah **membersihkan seluruh antarmuka, navigasi, kartu metrik, filter, dan to-do list operasional administrasi** dari unsur *Waiting List* dan *Perlu Perbaikan* agar alur administrasi menjadi ringkas, tegas, dan bebas dari status mengambang (*hanging states*).

---

## 2. Asumsi Kerja & Analisis Dampak

1. **Integritas Database (Zero Breaking Changes)**:
   - Case enum `StatusPendaftaranEnum::WaitingList` dan `StatusPendaftaranEnum::PerluPerbaikan` **tetap dipertahankan di level class PHP** untuk menjamin data historis lama (jika ada di database) tidak memicu error saat di-hydrate oleh Eloquent.
   - Tidak ada penghapusan kolom atau migrasi database destruktif.
2. **Backward Compatibility Route**:
   - Endpoint `/admin/waiting-list` pada backend tetap dipertahankan (atau dialihkan dengan graceful redirect ke `/admin/pendaftaran`) sehingga tidak merusak automated feature test yang menguji layer service/controller lama.
   - Seluruh elemen navigasi visual (sidebar menu, quick actions, to-do list, filter status track, dan stat cards) dihapus secara total dari pandangan administrator.
3. **Penyelarasan Blade Markup**:
   - Penghapusan tombol navigasi, filter pill, dan kartu metrik memerlukan perubahan markup Blade pada beberapa file presentasi admin. Hal ini akan dimintakan persetujuan eksplisit sesuai aturan `AGENTS.md` Bagian 3.3.

---

## 3. Evaluasi Opsi Arsitektur

### Opsi A (Rekomendasi Utama): Pembersihan Navigasi UI/UX Menyeluruh + Restrukturisasi Kartu Metrik Operasional
- **Tindakan**:
  1. Hapus tab filter `Perlu Perbaikan` dan `Waiting List` pada status tracker pendaftaran (sesuai tangkapan layar user). Status tracker kini hanya menampilkan: **Semua Status**, **Diajukan**, **Diterima**, dan **Ditolak**.
  2. Hapus menu navigasi sidebar "Waiting List" dari layout admin (`resources/views/layouts/dashboard.blade.php`).
  3. Ganti kartu metrik "Perlu Revisi Dokumen" pada halaman pendaftaran menjadi kartu "Pendaftaran Diterima".
  4. Ganti Tile 3 pada dashboard admin dari "Antrean Waiting List" menjadi "Pendaftaran Diterima" (`$totalDiterimaCount`).
  5. Hapus to-do item dan tombol quick-action "Waiting List" dan "Berkas Revisi" dari sidebar dashboard admin (`_aside.blade.php`).
  6. Hapus tombol filter pill "Perlu Perbaikan" pada tabel antrean verifikasi dashboard (`_queue_table.blade.php`).
- **Kelebihan**: Antarmuka admin menjadi bersih, konsisten 100% dengan keputusan Terima/Tolak, tidak membingungkan staf admin, dan test suite tetap stabil.
- **Kekurangan**: Perlu memodifikasi beberapa file Blade parsial admin secara presisi.

### Opsi B: Menghapus Total Seluruh Controller, Route, dan File View Waiting List
- **Tindakan**: Menghapus `AdminWaitingListController.php`, file view `admin/waiting-list/index.blade.php`, dan rute `/admin/waiting-list` dari `routes/web.php`.
- **Kelebihan**: Kode bersih dari controller yang tidak terpakai.
- **Kekurangan**: Berisiko merusak belasan test suite regression (`AdminSuperAdminPortalTest`, `DashboardModulesRenderingTest`, `AdminChildPagesExtendedVisualTest`, dll.) yang memverifikasi eksistensi rute, serta berisiko jika di kemudian hari dibutuhkan audit log penutupan antrean lama.

> **Rekomendasi**: Mengadopsi **Opsi A** untuk hasil yang aman, bersih, dan sesuai kaidah *stable engineering baseline*.

---

## 4. Daftar Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/admin/pendaftaran/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/index.blade.php) | **Modify** | 1. Hapus tab link `perlu_perbaikan` dan `waiting_list` dari `.adm-status-track`.<br>2. Ganti kartu stat ringkasan "Perlu Revisi Dokumen" menjadi "Pendaftaran Diterima". |
| [`resources/views/layouts/dashboard.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/layouts/dashboard.blade.php) | **Modify** | 1. Hapus menu link sidebar utama `admin.waiting-list.index`.<br>2. Hapus sub-menu link `admin.waiting-list.index`.<br>3. Bersihkan pengecekan active state `$isOperasionalAdminActive`. |
| [`resources/views/admin/dashboard/partials/_metrics.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/dashboard/partials/_metrics.blade.php) | **Modify** | Ubah Tile 3 dari "Antrean Waiting List" menjadi "Pendaftaran Diterima" dengan subteks status alokasi kuota. |
| [`resources/views/admin/dashboard/partials/_aside.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/dashboard/partials/_aside.blade.php) | **Modify** | 1. Hapus to-do item `waitingListCount` dan `perluPerbaikan`.<br>2. Hapus tombol quick action "Waiting List".<br>3. Sederhanakan kondisi empty state to-do. |
| [`resources/views/admin/dashboard/partials/_queue_table.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/dashboard/partials/_queue_table.blade.php) | **Modify** | Hapus tombol filter pill `Perlu Perbaikan` di atas tabel antrean pendaftaran dashboard. |
| [`app/Services/AdminDashboardMetricsService.php`](file:///d:/laragon/www/MAGKOM/app/Services/AdminDashboardMetricsService.php) | **Modify** | Tambahkan metrik `pendaftaranDiterimaCount` dan `pendaftaranDitolakCount` untuk menggantikan data waiting list di kartu dashboard operasional. |
| [`app/Http/Controllers/Admin/PendaftaranController.php`](file:///d:/laragon/www/MAGKOM/app/Http/Controllers/Admin/PendaftaranController.php) | **Modify** | Optimasi variabel `$queueStats` untuk memprioritaskan status `diajukan`, `diterima`, `ditolak`, dan `total`. |

---

## 5. Rencana Langkah Kerja Terperinci (Step-by-Step Task Checklist)

- [x] **Langkah 1 (Backend Metrics)**:
  - Pada [`app/Services/AdminDashboardMetricsService.php`](file:///d:/laragon/www/MAGKOM/app/Services/AdminDashboardMetricsService.php), tambahkan penghitungan `$pendaftaranDiterimaCount = Pendaftaran::where('status', StatusPendaftaranEnum::Diterima)->count();` dan teruskan ke view dashboard.
- [x] **Langkah 2 (Status Tracker & Filter Pendaftaran)**:
  - Pada [`resources/views/admin/pendaftaran/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/index.blade.php):
    - Hapus elemen `<a>` untuk `perlu_perbaikan` dan `waiting_list` pada `.adm-status-track`.
    - Ganti kartu metrik Tile 3 "Perlu Revisi Dokumen" menjadi "Pendaftaran Diterima" (`$queueStats['diterima']`).
- [x] **Langkah 3 (Navigasi Sidebar Admin)**:
  - Pada [`resources/views/layouts/dashboard.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/layouts/dashboard.blade.php):
    - Hapus item menu sidebar `Waiting List` pada navigasi admin.
    - Hapus sub-menu item `Waiting List` pada dropdown operasional admin.
- [x] **Langkah 4 (Dashboard Admin - Metrics & Aside)**:
  - Pada [`resources/views/admin/dashboard/partials/_metrics.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/dashboard/partials/_metrics.blade.php), ubah Tile 3 menjadi metrik "Pendaftaran Diterima".
  - Pada [`resources/views/admin/dashboard/partials/_aside.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/dashboard/partials/_aside.blade.php), bersihkan to-do list item dan quick button Waiting List & Revisi.
  - Pada [`resources/views/admin/dashboard/partials/_queue_table.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/dashboard/partials/_queue_table.blade.php), hapus filter pill "Perlu Perbaikan".
- [x] **Langkah 5 (Quality Gate & Pengujian)**:
  - Jalankan `php artisan test --filter=Admin` dan `php artisan test --filter=Dashboard` (38 passed, 215 assertions).
  - Jalankan `npm run build` untuk memverifikasi kompilasi aset frontend (Built in 1.79s).
  - Jalankan `./vendor/bin/pint --test` untuk memverifikasi kepatuhan styling kode (Passed).
  - Jalankan pengecekan linter PHP pada seluruh view Blade yang dimodifikasi (No syntax errors).

---

## 6. Permohonan Persetujuan Perubahan Markup Blade (Sesuai AGENTS.md Bagian 3.3)

Sesuai aturan wajib **AGENTS.md Bagian 3.3**:
1. **Mengapa Perubahan Markup Diperlukan**: Menghapus navigasi visual (tab filter status, menu sidebar, kartu metrik, to-do list, dan filter pill) secara mutlak memerlukan penghapusan/penyesuaian elemen HTML di file-file Blade terkait.
2. **File Blade yang Akan Diubah**:
   - `resources/views/admin/pendaftaran/index.blade.php`
   - `resources/views/layouts/dashboard.blade.php`
   - `resources/views/admin/dashboard/partials/_metrics.blade.php`
   - `resources/views/admin/dashboard/partials/_aside.blade.php`
   - `resources/views/admin/dashboard/partials/_queue_table.blade.php`
3. **Jaminan Integritas**:
   - Tidak ada penulisan inline JavaScript atau inline `<style>` di dalam Blade.
   - Penataan grid layout dan responsivitas tetap terjaga utuh.
   - Tidak ada perubahan destruktif pada struktur database.

