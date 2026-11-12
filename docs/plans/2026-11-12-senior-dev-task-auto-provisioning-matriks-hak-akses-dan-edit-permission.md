# Task Brief Senior Developer: Auto-Provisioning Matriks Hak Akses & Fitur Edit Permission (PRD v1.2)

> **Dokumen Panduan Teknis & Rencana Kerja (Plan)**  
> **Target Audiens**: Junior Developer / AI Model Tier Hemat (*Gemini 3.8 Flash / Claude 3.5 Haiku*)  
> **Level Ketelitian**: Sangat Detail, Presisi Arsitektural, Atomic Checklist, Zero Ambiguity  
> **Modul Terkait**: Hak Akses dan Role Pengguna (`/superadmin/role`)  
> **Referensi Kode**: `app/Http/Controllers/SuperAdmin/RoleController.php`, `database/seeders/PermissionSeeder.php`, `resources/views/superadmin/role/index.blade.php`

---

## 1. Ringkasan & Tujuan (Executive Summary)

### 1.1 Masalah Nyata di Lapangan (Pain Point)
Saat ini pada antarmuka **Matriks Wewenang & Hak Akses Modul Dinamis** (`/superadmin/role`), SuperAdmin dihadapkan pada kendala operasional yang sangat merepotkan:
1. **Tidak Ada Auto-Provisioning Otomatis**: Jika aplikasi baru dideploy, database di-reset, atau seeder belum dieksekusi secara manual via CLI (`php artisan db:seed --class=PermissionSeeder`), tabel perizinan kosong melompong.
2. **Beban Input Manual yang Melelahkan (13x Input)**: Karena tabel kosong, SuperAdmin terpaksa menekan tombol **"Tambah Hak Akses"** dan mengetik manual satu per satu 13 modul bawaan sistem (Nama Modul, Slug, Kategori, Deskripsi, Centang Peran). Ini rentan typo dan tidak efisien.
3. **Absennya Fitur Edit Detail Modul**: Modul yang sudah ada saat ini hanya bisa diubah centang hak aksesnya (*checklist matrix*) atau dihapus (jika kustom). Tidak ada mekanisme untuk mengoreksi nama, kategori, atau deskripsi modul tanpa harus menghapusnya terlebih dahulu.
4. **Pendaftaran Seeder Belum Terintegrasi**: `PermissionSeeder::class` belum terdaftar pada `database/seeders/DatabaseSeeder.php`, sehingga perintah standar `php artisan migrate --seed` tidak memuat modul sistem.

### 1.2 Tujuan Akhir (Objective)
1. **Auto-Provisioning Bawaan (Out-of-the-Box)**: Ke-13 modul sistem definitif MagKom beserta pemetaan wewenang perannya **OTOMATIS TERSEDIA** di database dan tampil di layar. SuperAdmin **TIDAK PERLU** menginput satu per satu lagi!
2. **Tombol "Tambah Hak Akses" Hanya untuk Modul Baru**: Tombol tambah tetap ada dan berfungsi khusus jika dinas ingin menambah modul baru di luar 13 modul standar.
3. **Wewenang Tetap Dinamis & Bisa Diedit**: Matriks centang peran (SuperAdmin, Administrasi, Pembimbing, Peserta) tetap dapat diubah kapan saja dan disimpan secara massal per halaman.
4. **Fitur Edit Metadata Modul (Edit Permission)**: Menyediakan dialog modal untuk memperbarui nama, kategori, dan deskripsi modul yang ada, dengan proteksi keamanan ketat (*immutable slug*) untuk modul sistem.
5. **Mekanisme Self-Healing & Sinkronisasi Idempotent**: Jika ada modul sistem yang tidak sengaja terhapus atau belum lengkap, controller/service secara otomatis memulihkannya (*self-healing*) tanpa merusak konfigurasi wewenang yang sudah diatur SuperAdmin.

---

## 2. Asumsi & Invarian Bisnis (Business Invariants)

Berdasarkan MagKom PRD v1.2 Bab 3.1 & 3.3, aturan baku berikut **HARUS DIPERTAHANKAN**:
1. **Katalog 13 Modul Sistem Definitif**:
   - `publik.portal-registrasi` (Publik) &rarr; Default: SuperAdmin, Admin, Pembimbing, Peserta
   - `pendaftaran.magang` (Pendaftaran) &rarr; Default: SuperAdmin, Peserta
   - `seleksi.verifikasi-berkas` (Seleksi) &rarr; Default: SuperAdmin, Admin
   - `kuota.manajemen` (Kuota) &rarr; Default: SuperAdmin, Admin
   - `penempatan.pembimbing` (Penempatan) &rarr; Default: SuperAdmin, Admin
   - `bimbingan.logbook-tugas` (Bimbingan) &rarr; Default: SuperAdmin, Pembimbing, Peserta
   - `laporan.kinerja-portofolio` (Pelaporan) &rarr; Default: SuperAdmin, Admin, Pembimbing
   - `staf.manajemen` (Tata Kelola) &rarr; Default: SuperAdmin *(Core System)*
   - `kuota.slot-khusus` (Kuota) &rarr; Default: SuperAdmin
   - `konfigurasi.parameter` (Tata Kelola) &rarr; Default: SuperAdmin *(Core System)*
   - `sistem.cadangan-database` (Tata Kelola) &rarr; Default: SuperAdmin *(Core System)*
   - `sistem.telemetri-kesehatan` (Keamanan) &rarr; Default: SuperAdmin *(Core System)*
   - `keamanan.audit-trail` (Keamanan) &rarr; Default: SuperAdmin *(Core System)*
2. **Proteksi Invarian SuperAdmin**: 5 modul sistem inti (`staf.manajemen`, `konfigurasi.parameter`, `sistem.cadangan-database`, `sistem.telemetri-kesehatan`, `keamanan.audit-trail`) **WAJIB** memiliki hak akses SuperAdmin permanen (checkbox terdisable dan selalu tersimpan bernilai 1).
3. **Proteksi Integritas Slug Sistem**: Modul dengan flag `is_system = true` dilarang diubah `slug`-nya atau diubah menjadi `is_system = false`. Slug modul sistem terikat langsung dengan *route guard* dan otorisasi aplikasi.

---

## 3. Evaluasi Opsi Arsitektur

Berikut adalah komparasi minimal 2 pendekatan teknis untuk menyelesaikan masalah ini:

| Kriteria Evaluasi | Opsi 1: Idempotent Service Auto-Provisioning + DatabaseSeeder (Direkomendasikan) | Opsi 2: Migrasi Hardcoded SQL (Static Migration Insert) |
| :--- | :--- | :--- |
| **Mekanisme** | 1. Registrasi `PermissionSeeder` di `DatabaseSeeder`.<br>2. Logika *self-healing* di `PermissionService::ensureSystemPermissionsExist()` dipanggil di `RoleController::index()`.<br>3. Tombol Sinkronisasi Manual untuk SuperAdmin. | Memasukkan query `INSERT INTO permissions ...` langsung di dalam method `up()` file database migration baru. |
| **Kelebihan** | - Bersifat *self-healing*: Jika data hilang atau tidak lengkap, sistem otomatis memperbaikinya tanpa perlu deploy/migrasi ulang.<br>- Idempotent (`firstOrCreate` / `updateOrCreate`): tidak akan menimpa modifikasi centang wewenang yang sudah diatur SuperAdmin.<br>- Mengikuti standar arsitektur Laravel (pemisahan schema dan seed data). | - Sekali jalan saat migrasi database dijalankan di server. |
| **Kekurangan** | - Memerlukan penambahan 1 service class / method helper kecil yang terstruktur. | - **Kaku & Rawan Konflik**: Jika data terhapus di staging/dev, migrasi tidak bisa diulang tanpa rollback.<br>- Melanggar konvensi Laravel di mana migration hanya untuk struktur DDL, bukan master data yang dapat dikelola dinamis. |
| **Rekomendasi** | **Sangat Direkomendasikan (Opsi 1)** karena menjamin SuperAdmin tidak pernah melihat tabel kosong, aman dari data loss, dan ramah automasi CI/CD. | Tidak direkomendasikan. |

---

## 4. Perubahan File (File Modification Table)

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `database/seeders/DatabaseSeeder.php` | Modify | Daftarkan `PermissionSeeder::class` dalam array `$this->call([...])`. |
| `app/Services/PermissionService.php` | Create / Modify | Buat service dengan method `ensureSystemPermissionsExist()` dan `syncDefaultPermissions()` yang aman dan idempotent. |
| `app/Http/Requests/SuperAdmin/UpdatePermissionRequest.php` | Create | Form Request untuk validasi update modul (nama, kategori, deskripsi; slug opsional & unik). |
| `app/Http/Controllers/SuperAdmin/RoleController.php` | Modify | 1. Injeksi `PermissionService` dan panggil `ensureSystemPermissionsExist()` di `index()`.<br>2. Tambahkan method `updatePermission()` untuk update detail modul.<br>3. Tambahkan method `syncDefaults()` untuk tombol sinkronisasi modul bawaan. |
| `routes/web.php` | Modify | Tambahkan rute `PUT /superadmin/role/permission/{permission}` dan `POST /superadmin/role/sync`. |
| `resources/views/superadmin/role/index.blade.php` | Modify | 1. Tambahkan tombol edit (ikon pensil) pada tiap baris modul.<br>2. Tambahkan tombol "Sinkronkan Modul Standar" di toolbox/banner.<br>3. Tambahkan dialog modal Edit Hak Akses (`#modalEditPermission`). |
| `resources/js/pages/superadmin/role-index.js` | Modify | Tambahkan event listener untuk mengisi form modal edit dan konfirmasi tombol sinkronisasi. |
| `resources/css/pages/superadmin/role-index.css` | Modify | Styling tombol edit inline dan penyesuaian modal edit agar seragam dengan desain sistem Gentelella. |
| `tests/Feature/SuperAdmin/RolePermissionTest.php` | Modify | Tambahkan pengujian auto-provisioning saat tabel kosong, update modul, proteksi slug sistem, dan audit trail. |

---

## 5. Rincian Desain Teknis untuk Junior Developer / AI

### 5.1 Service Auto-Provisioning (`app/Services/PermissionService.php`)
Junior Dev / AI harus membuat service method yang **idempotent**:
```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Support\Facades\DB;

class PermissionService
{
    /**
     * Memastikan ke-13 modul sistem bawaan selalu terdaftar di database.
     * Bersifat idempotent: Hanya menambah yang belum ada, TIDAK merusak konfigurasi wewenang yang sudah diubah.
     */
    public function ensureSystemPermissionsExist(): void
    {
        $systemCount = Permission::where('is_system', true)->count();
        if ($systemCount >= 13) {
            return;
        }

        $this->provisionDefaultModules(overrideRoles: false);
    }

    /**
     * Sinkronisasi penuh modul bawaan sistem.
     * Jika $overrideRoles = true, wewenang role akan direset ke konfigurasi default PRD.
     */
    public function provisionDefaultModules(bool $overrideRoles = false): void
    {
        $defaultModules = config('magkom_permissions.defaults', [
            // 13 Modul Sistem Definitif sesuai PRD v1.2
            [
                'nama_modul' => 'Portal Publik & Registrasi Calon Peserta',
                'slug' => 'publik.portal-registrasi',
                'kategori' => 'Publik',
                'deskripsi' => 'Akses informasi formasi, alur pendaftaran, dan registrasi akun calon peserta magang.',
                'roles' => ['superadmin', 'admin', 'pembimbing', 'peserta'],
                'urutan' => 1,
            ],
            // ... (12 modul lainnya dari PermissionSeeder)
        ]);

        DB::transaction(function () use ($defaultModules, $overrideRoles): void {
            foreach ($defaultModules as $item) {
                $permission = Permission::firstOrCreate(
                    ['slug' => $item['slug']],
                    [
                        'nama_modul' => $item['nama_modul'],
                        'kategori' => $item['kategori'],
                        'deskripsi' => $item['deskripsi'],
                        'is_system' => true,
                        'urutan' => $item['urutan'],
                    ]
                );

                // Jika modul sudah ada namun flag is_system belum true, pastikan diset true
                if (! $permission->is_system) {
                    $permission->update(['is_system' => true]);
                }

                // Inisialisasi role permission jika belum ada, atau jika diminta override
                if ($overrideRoles) {
                    RolePermission::where('permission_id', $permission->id)->delete();
                    foreach ($item['roles'] as $role) {
                        RolePermission::create([
                            'role' => $role,
                            'permission_id' => $permission->id,
                        ]);
                    }
                } else {
                    // Hanya tambahkan jika role permission untuk modul ini masih 0 (baru di-provisioning)
                    if ($permission->rolePermissions()->count() === 0) {
                        foreach ($item['roles'] as $role) {
                            RolePermission::firstOrCreate([
                                'role' => $role,
                                'permission_id' => $permission->id,
                            ]);
                        }
                    }
                }
            }
        });
    }
}
```

### 5.2 Form Request Validasi Update (`UpdatePermissionRequest.php`)
Aturan validasi ketat:
```php
public function rules(): array
{
    /** @var \App\Models\Permission|null $permission */
    $permission = $this->route('permission');

    return [
        'nama_modul' => ['required', 'string', 'max:150'],
        'slug' => $permission?->is_system 
            ? ['nullable', 'string'] // Modul sistem: slug diabaikan dan tidak boleh diubah
            : ['required', 'string', 'max:100', 'regex:/^[a-zA-Z0-9._-]+$/', 'unique:permissions,slug,' . $permission?->id],
        'kategori' => ['required', 'string', 'max:50'],
        'deskripsi' => ['nullable', 'string', 'max:500'],
    ];
}
```

### 5.3 Controller Action Update & Proteksi Slug (`RoleController.php`)
```php
public function updatePermission(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
{
    $validated = $request->validated();

    DB::transaction(function () use ($validated, $permission): void {
        $oldValues = $permission->only(['nama_modul', 'slug', 'kategori', 'deskripsi']);

        // Jika modul sistem, pertahankan slug asli
        if ($permission->is_system) {
            unset($validated['slug']);
        }

        $permission->update($validated);

        $this->clearPermissionsCache();

        $this->auditService->catat(
            tipe: TipeAuditEnum::Aksi,
            tabel: 'permissions',
            idData: $permission->id,
            nilaiLama: $oldValues,
            nilaiBaru: $permission->only(['nama_modul', 'slug', 'kategori', 'deskripsi']),
            detail: [
                'aksi' => 'EDIT_HAK_AKSES_MODUL',
                'is_system' => $permission->is_system,
            ]
        );
    });

    return redirect()->route('superadmin.role.index')
        ->with('success', "Informasi hak akses modul '{$permission->nama_modul}' berhasil diperbarui.");
}
```

---

## 6. Checklist Langkah Implementasi Bertahap (Execution Steps)

Langkah-langkah di bawah ini disusun secara berurutan dan terstruktur (*strictly sequential*):

### Fase 1: Pendaftaran Seeder & Service Auto-Provisioning (Backend)
- [x] **Langkah 1.1**: Buka `database/seeders/DatabaseSeeder.php`, tambahkan `PermissionSeeder::class` ke dalam daftar `$this->call([...])` agar terpanggil otomatis saat `php artisan db:seed`.
- [x] **Langkah 1.2**: Buat service class baru `app/Services/PermissionService.php` yang mengelola data definitif 13 modul sistem dan menyediakan method `ensureSystemPermissionsExist()` serta `provisionDefaultModules(bool $overrideRoles)`.
- [x] **Langkah 1.3**: Refactor `database/seeders/PermissionSeeder.php` agar memanfaatkan `PermissionService::provisionDefaultModules(overrideRoles: false)` untuk menghilangkan duplikasi kode (*DRY principle*).

### Fase 2: Form Request, Controller, & Routing (Backend)
- [x] **Langkah 2.1**: Buat Form Request `app/Http/Requests/SuperAdmin/UpdatePermissionRequest.php` dengan otorisasi SuperAdmin dan validasi update (dengan proteksi slug sistem).
- [x] **Langkah 2.2**: Update `app/Http/Controllers/SuperAdmin/RoleController.php`:
  - Injeksi `PermissionService $permissionService` ke constructor.
  - Pada method `index()`, panggil `$this->permissionService->ensureSystemPermissionsExist()` sebelum query pagination dijalankan.
  - Implementasikan method `updatePermission(UpdatePermissionRequest $request, Permission $permission)`.
  - Implementasikan method `syncDefaultPermissions(Request $request)` dengan proteksi CSRF dan audit log.
- [x] **Langkah 2.3**: Buka `routes/web.php`, daftarkan rute baru di dalam grup `superadmin`:
  - `PUT /superadmin/role/permission/{permission}` &rarr; `RoleController@updatePermission` (name: `superadmin.role.permission.update`)
  - `POST /superadmin/role/sync` &rarr; `RoleController@syncDefaultPermissions` (name: `superadmin.role.sync`)

### Fase 3: Tampilan Antarmuka & Interaktivitas Modal (Frontend)
- [x] **Langkah 3.1**: Buka `resources/views/superadmin/role/index.blade.php`:
  - Pada kolom pertama tabel (*Modul & Cakupan Fitur*), tambahkan tombol edit kecil berikon pensil (`.btn-edit-permission`) yang memuat data attributes: `data-id`, `data-nama`, `data-slug`, `data-kategori`, `data-deskripsi`, `data-system`.
  - Di sebelah tombol *"Tambah Hak Akses"*, sediakan tombol *"Sinkronkan Modul Bawaan"* dengan form POST kecil untuk pemulihan cepat.
  - Buat modal dialog baru `#modalEditPermission` lengkap dengan field: Nama Modul, Kode Unik / Slug (tampilkan disabled dengan keterangan jika `is_system`), Kategori, Deskripsi, serta tombol Simpan Perubahan.
- [x] **Langkah 3.2**: Buka `resources/js/pages/superadmin/role-index.js`:
  - Buat fungsi `initEditPermissionModal()` yang menangkap klik tombol `.btn-edit-permission`, mengisi form modal dengan data atribut modul, mengubah action URL form sesuai ID permission, dan membuka modal.
  - Tambahkan konfirmasi dialog pada tombol sinkronisasi default modul.
- [x] **Langkah 3.3**: Buka `resources/css/pages/superadmin/role-index.css`:
  - Berikan styling pada `.btn-edit-permission` agar selaras dengan Gentelella design system (hover effect, accessible tap target, dark mode support).

### Fase 4: Pengujian & Quality Gate (Testing & Verification)
- [x] **Langkah 4.1**: Jalankan kompilasi aset frontend via terminal: `npm run build` untuk memverifikasi CSS dan JS valid tanpa error.
- [x] **Langkah 4.2**: Tambahkan test cases baru di `tests/Feature/SuperAdmin/RolePermissionTest.php`:
  - `test_system_permissions_are_auto_provisioned_when_accessing_index_on_empty_database()`
  - `test_superadmin_can_update_permission_metadata()`
  - `test_cannot_modify_slug_of_system_permission()`
  - `test_superadmin_can_sync_default_permissions_via_post_route()`
- [x] **Langkah 4.3**: Jalankan test suite relevan: `php artisan test tests/Feature/SuperAdmin/RolePermissionTest.php`.
- [x] **Langkah 4.4**: Jalankan regression test pagination: `php artisan test tests/Feature/SuperAdminPaginationTenEntriesTest.php`.
- [x] **Langkah 4.5**: Jalankan code linter: `./vendor/bin/pint --test`.

---

## 7. Strategi Pengujian (Test Scenarios)

Junior Developer / AI harus memastikan skenario pengujian berikut tercover dan berstatus **PASS**:

### 7.1 Happy Path
1. **Auto-Provisioning**: Ketika database kosong (tanpa seed), mengakses `GET /superadmin/role` otomatis menghasilkan 13 data `permissions` dan relasi `role_permissions` awal, serta merender tabel tanpa tulisan "Belum ada data".
2. **Update Metadata Modul**: Mengirim `PUT /superadmin/role/permission/{id}` dengan nama baru atau deskripsi baru berhasil memperbarui data di tabel `permissions` dan mencatat audit trail `EDIT_HAK_AKSES_MODUL`.
3. **Penyimpanan Matriks**: Mengubah centang peran pada halaman 1 dan menekan tombol simpan berhasil memperbarui tabel `role_permissions` tanpa menghilangkan hak akses di halaman 2.
4. **Tambah Modul Kustom**: Tombol "Tambah Hak Akses" tetap berfungsi normal untuk modul baru dengan `is_system = false`.

### 7.2 Sad Path & Security Guardrails
1. **Proteksi Slug Modul Sistem**: Jika pengguna mencoba memodifikasi parameter `slug` pada modul sistem (`is_system = true`), nilai `slug` di database tidak boleh berubah.
2. **Non-SuperAdmin Forbidden**: User dengan peran Admin, Pembimbing, atau Peserta yang mencoba mengakses endpoint update permission atau sync ditolak dengan status HTTP **403 Forbidden**.
3. **Validasi Input**: Validasi gagal (HTTP 422 / redirect with errors) jika `nama_modul` kosong atau karakter melebihi batas.
4. **Proteksi Invarian SuperAdmin**: Centang wewenang SuperAdmin pada 5 modul inti sistem (`audit-trail`, `staf.manajemen`, `konfigurasi.parameter`, `sistem.cadangan-database`, `sistem.telemetri-kesehatan`) tidak dapat dicabut via matrix update.

---

## 8. Analisis Risiko & Mitigasi (Risk Analysis)

| Potensi Risiko | Tingkat | Dampak | Mitigasi Senior Dev |
| :--- | :---: | :--- | :--- |
| **Overwriting Custom Matrix** | Sedang | Perubahan centang wewenang yang sudah disimpan SuperAdmin tertimpa kembali ke default. | Gunakan logika `ensureSystemPermissionsExist()` yang hanya memasukkan modul yang belum ada, dan jangan menghapus atau menimpa relasi role eksisting kecuali tombol "Reset ke Default" ditekan secara eksplisit. |
| **Performa Index Load Slowdown** | Rendah | Pengecekan count database di `index()` menambah latensi respons. | Query hanya berupa `Permission::where('is_system', true)->count()`. Jika count &ge; 13, pemrosesan selesai dalam &lt; 2ms tanpa overhead. |
| **Breaking Authorization Cache** | Sedang | Perubahan wewenang tidak langsung dirasakan pengguna karena cache wewenang masih aktif. | Selalu panggil `$this->clearPermissionsCache()` setelah mutasi permissions atau matriks wewenang. |

---

## 9. Kriteria Selesai (Definition of Done / DoD)

Tugas ini dinyatakan selesai jika:
- [x] Database fresh install / migration langsung memuat 13 modul sistem tanpa perlu input manual.
- [x] Mengakses halaman `/superadmin/role` menampilkan 13 modul sistem dengan pembagian halaman (pagination 10 entri per halaman).
- [x] SuperAdmin **tidak perlu** mengetik satu per satu modul default lagi.
- [x] Tombol "Tambah Hak Akses" tetap tersedia khusus untuk menambahkan hak akses baru di masa mendatang.
- [x] Seluruh hak akses yang ada memiliki tombol edit untuk mengubah nama modul, kategori, dan deskripsi.
- [x] Slug modul sistem terkunci dan tidak bisa diubah demi integritas keamanan.
- [x] Seluruh test case pada `RolePermissionTest.php` dan `SuperAdminPaginationTenEntriesTest.php` lulus 100% (Green).
- [x] Aset Vite terkompilasi bersih tanpa syntax error.
- [x] Kode mematuhi standar PSR-12 dan lolos uji linter Laravel Pint.

