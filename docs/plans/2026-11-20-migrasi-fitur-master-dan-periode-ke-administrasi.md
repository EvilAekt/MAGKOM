# Rencana Kerja: Migrasi Fitur & Hak Akses (Periode Magang, Jalur Khusus, Data Jurusan, Unit Kerja) dari SuperAdmin ke Administrasi

**Dokumen**: `docs/plans/2026-11-20-migrasi-fitur-master-dan-periode-ke-administrasi.md`  
**Status**: ✅ Selesai Diimplementasikan & Terverifikasi  
**Tanggal**: 20 November 2026  
**Peran Perencana**: Senior Lead Developer (Instruksi Terperinci untuk Junior Developer / Autonomous Agent)

---

## 1. Ringkasan & Tujuan Utama

Sesuai arahan arsitektur terbaru dan restrukturisasi hak akses operasional MagKom:
Seluruh fitur operasional dan tampilan manajemen data:
1. **Periode Magang** (`BulanMagang`)
2. **Jalur Khusus** (`SlotKhusus` & Pendaftaran Peserta Jalur Khusus `Task D`)
3. **Data Jurusan** (`Jurusan`)
4. **Unit Kerja** (`Bidang`)

dipindahkan sepenuhnya dari wewenang **SuperAdmin** ke **Admin (Administrasi Magang)**.

### Perubahan Paradigma Akses:
- **Admin (Administrasi Magang)**: Menjadi pemilik tunggal operasional (*operational owner*) dengan hak akses penuh (**Full CRUD + Toggle Status Aktif + Pendaftaran Peserta Jalur Khusus**).
- **SuperAdmin**: Menjadi **Read-Only / Monitoring Only** (hanya bisa melihat data tabel, filter, dan statistik). Segala upaya mutasi data (`POST`, `PUT`, `PATCH`, `DELETE`) dari SuperAdmin **wajib ditolak dengan status HTTP 403 Forbidden** oleh middleware `EnsureSuperAdminReadOnly` dan FormRequest authorization, serta elemen form/tombol mutasi disembunyikan/di-disable pada antarmuka.

---

## 2. Asumsi & Batasan Teknis

1. **Baseline Teknologi**: Laravel 10.x, PHP 8.3, Blade, Tailwind/Vanilla CSS, Vanilla ES Modules, Vite, MySQL.
2. **Aturan Mutlak Repository (`.agents/rules/AGENTS.md`)**:
   - Dilarang menulis CSS/JS di dalam file Blade (`<style>`, `<script>`, atau inline handlers).
   - Seluruh CSS berada di `resources/css/` dan JS di `resources/js/`.
   - Perubahan Blade murni untuk struktur markup, pembaruan tautan route, dan Blade condition authorization (`@if(! auth()->user()?->isSuperAdmin())`).
3. **Mekanisme SuperAdmin Read-Only**:
   - Middleware `EnsureSuperAdminReadOnly` (`superadmin.readonly`) yang sudah terpasang pada grup rute `admin.` secara otomatis mencegat method non-safe (`POST`, `PUT`, `PATCH`, `DELETE`) dari pengguna dengan peran `SuperAdmin`.
4. **Matriks RBAC & Permission**:
   - Dynamic permission `kuota.slot-khusus` ditambahkan role `admin` pada `PermissionService`.
   - Modul `kuota.manajemen` menaungi Periode Magang, Data Jurusan, dan Unit Kerja.
5. **Backward Compatibility**:
   - Tautan lama `/superadmin/bulan`, `/superadmin/slot-khusus`, `/superadmin/jurusan`, `/superadmin/bidang` diarahkan via HTTP 302 redirect ke `/admin/...` agar tidak terjadi broken link.

---

## 3. Evaluasi Pendekatan Arsitektur

| Kriteria | Pendekatan A: Pemindahan Penuh ke Prefix `/admin/` (Direkomendasikan) | Pendekatan B: Buka Akses Role di `/superadmin/` |
| :--- | :--- | :--- |
| **Pemisahan Peran** | Sempurna. Modul operasional berada di bawah `/admin/`, diproteksi `superadmin.readonly`. | Buruk. Admin harus mengakses URL `/superadmin/...`, membingungkan batasan sistem. |
| **Keamanan (SEC-12)** | Alami & terpusat. Middleware `EnsureSuperAdminReadOnly` otomatis aktif di grup `admin.`. | Rentan. Perlu memasang pengecekan manual method non-safe di setiap method controller. |
| **Konsistensi UI/UX** | Selaras dengan menu Administrasi Magang (Gambar 2). | Tidak konsisten dengan arsitektur role-based routing MagKom. |
| **Maintainability** | Sangat tinggi, clean controllers di namespace `App\Http\Controllers\Admin`. | Rendah, terjadi duplikasi logika dan controller monolith di SuperAdmin. |

**Keputusan Arsitektur**: **Pendekatan A** dipilih karena memenuhi seluruh kriteria keamanan OWASP, pemisahan hak akses dinamis, dan instruksi pengguna.

---

## 4. Matriks Perubahan File

| No | File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- | :--- |
| 1 | `app/Services/PermissionService.php` | Modify | Menambahkan role `'admin'` pada modul `kuota.slot-khusus` di `getDefaultSystemModules()`. |
| 2 | `app/Http/Requests/BulanMagangRequest.php` | Modify | Ubah `authorize()` dari `isSuperAdmin()` menjadi `$this->user()?->isAdmin() ?? false`. |
| 3 | `app/Http/Requests/SlotKhususRequest.php` | Modify | Ubah `authorize()` dari `isSuperAdmin()` menjadi `$this->user()?->isAdmin() ?? false`. |
| 4 | `routes/web.php` | Modify | Pindahkan rute 4 fitur ke group `prefix('admin')`, tambahkan redirect dari rute lama `superadmin.*`. |
| 5 | `app/Http/Controllers/Admin/BulanController.php` | Create | Controller manajemen periode magang di Administrasi Magang. |
| 6 | `app/Http/Controllers/Admin/SlotKhususController.php` | Create | Controller alokasi slot khusus kemitraan di Administrasi Magang. |
| 7 | `app/Http/Controllers/Admin/PesertaJalurKhususController.php` | Create | Controller registrasi langsung peserta magang jalur khusus. |
| 8 | `app/Http/Controllers/Admin/JurusanController.php` | Create | Controller master data jurusan pendidikan di Administrasi Magang. |
| 9 | `app/Http/Controllers/Admin/BidangController.php` | Create | Controller master data unit kerja bidang dinas di Administrasi Magang. |
| 10 | `app/Http/Controllers/SuperAdmin/BulanController.php` | Delete | Dihapus karena telah dimigrasikan ke Admin. |
| 11 | `app/Http/Controllers/SuperAdmin/SlotKhususController.php` | Delete | Dihapus karena telah dimigrasikan ke Admin. |
| 12 | `app/Http/Controllers/SuperAdmin/PesertaJalurKhususController.php` | Delete | Dihapus karena telah dimigrasikan ke Admin. |
| 13 | `app/Http/Controllers/SuperAdmin/JurusanController.php` | Delete | Dihapus karena telah dimigrasikan ke Admin. |
| 14 | `app/Http/Controllers/SuperAdmin/BidangController.php` | Delete | Dihapus karena telah dimigrasikan ke Admin. |
| 15 | `resources/views/admin/bulan/index.blade.php` | Create | View periode magang dengan rute `admin.*` dan guard Read-Only SuperAdmin. |
| 16 | `resources/views/admin/slot-khusus/index.blade.php` | Create | View slot khusus dengan rute `admin.*` dan guard Read-Only SuperAdmin. |
| 17 | `resources/views/admin/peserta-khusus/create.blade.php` | Create | View formulir pendaftaran peserta khusus dengan rute `admin.*`. |
| 18 | `resources/views/admin/jurusan/index.blade.php` | Create | View master jurusan dengan rute `admin.*` dan guard Read-Only SuperAdmin. |
| 19 | `resources/views/admin/bidang/index.blade.php` | Create | View master unit kerja dengan rute `admin.*` dan guard Read-Only SuperAdmin. |
| 20 | `resources/views/superadmin/bulan/index.blade.php` | Delete | Dihapus / digantikan oleh rute admin. |
| 21 | `resources/views/superadmin/slot-khusus/index.blade.php` | Delete | Dihapus / digantikan oleh rute admin. |
| 22 | `resources/views/superadmin/peserta-khusus/create.blade.php` | Delete | Dihapus / digantikan oleh rute admin. |
| 23 | `resources/views/superadmin/jurusan/index.blade.php` | Delete | Dihapus / digantikan oleh rute admin. |
| 24 | `resources/views/superadmin/bidang/index.blade.php` | Delete | Dihapus / digantikan oleh rute admin. |
| 25 | `resources/views/layouts/dashboard.blade.php` | Modify | Tambahkan 4 menu di sidebar Admin, perbarui link navigasi di sidebar SuperAdmin. |
| 26 | `resources/js/pages/admin/bulan-index.js` | Create | JS modal & aksi periode magang dengan endpoint dinamis `/admin/bulan/`. |
| 27 | `resources/js/pages/admin/slot-khusus.js` | Create | JS modal & aksi slot khusus dengan endpoint dinamis `/admin/slot-khusus/`. |
| 28 | `resources/js/pages/admin/jurusan-index.js` | Create | JS modal & aksi jurusan dengan endpoint dinamis `/admin/jurusan/`. |
| 29 | `resources/js/pages/admin/bidang-index.js` | Create | JS modal & aksi unit kerja dengan endpoint dinamis `/admin/bidang/`. |
| 30 | `vite.config.js` | Modify | Daftarkan entry point JS baru di bawah `resources/js/pages/admin/`. |
| 31 | `tests/Feature/AdminMasterDataFeatureMigrationTest.php` | Create | Test suite komprehensif hak akses Admin & SuperAdmin Read-Only Enforcement. |
| 32 | `tests/Feature/EndToEndUatTest.php` dll. | Modify | Sesuaikan skenario pengujian yang sebelumnya memanggil rute mutasi SuperAdmin. |

---

## 5. Rencana Langkah Kerja Terperinci (Senior Dev Tasks)

### 📌 FASE 1: Fondasi RBAC, Permission, & Form Requests (Backend)

- [x] **Task 1.1: Pemutakhiran Matriks RBAC di PermissionService**
  - **File**: `app/Services/PermissionService.php`
  - **Instruksi**:
    - Cari definisi modul `kuota.slot-khusus` pada baris 88-94.
    - Ubah `'roles' => ['superadmin']` menjadi `'roles' => ['superadmin', 'admin']`.
    - Pastikan modul `kuota.manajemen` tetap memiliki `'roles' => ['superadmin', 'admin']`.
    - Tambahkan seeder/synchronizer kecil untuk menjalankan `$permissionService->provisionDefaultModules(overrideRoles: false)` agar database langsung tersinkronisasi.

- [x] **Task 1.2: Otorisasi Form Request (Pencegahan Mutasi SuperAdmin)**
  - **File**: `app/Http/Requests/BulanMagangRequest.php`
    - Ubah method `authorize()`:
      ```php
      public function authorize(): bool
      {
          return $this->user()?->isAdmin() ?? false;
      }
      ```
  - **File**: `app/Http/Requests/SlotKhususRequest.php`
    - Ubah method `authorize()`:
      ```php
      public function authorize(): bool
      {
          return $this->user()?->isAdmin() ?? false;
      }
      ```
    > 💡 *Catatan Senior Dev*: Dengan mengembalikan `false` jika user adalah SuperAdmin, Laravel secara otomatis melempar `HTTP 403 Forbidden` jika ada percobaan memotong form via Postman/cURL!

---

### 📌 FASE 2: Rute Sistem & Middleware Grouping (`routes/web.php`)

- [x] **Task 2.1: Mendaftarkan Rute di Bawah Prefix Admin**
  - **File**: `routes/web.php`
  - **Instruksi**:
    Di dalam grup rute Admin (`Route::middleware(['auth', 'verified', 'must_change_password', 'account.active', 'role:admin,superadmin', 'superadmin.readonly', '2fa.enforced'])->prefix('admin')->name('admin.')->group(...)`):
    
    1. **Dalam Subgroup `permission:kuota.manajemen`**:
       ```php
       // Periode Magang (Bulan Magang)
       Route::get('/bulan', [App\Http\Controllers\Admin\BulanController::class, 'index'])->name('bulan.index');
       Route::post('/bulan', [App\Http\Controllers\Admin\BulanController::class, 'store'])->name('bulan.store');
       Route::put('/bulan/{bulan}', [App\Http\Controllers\Admin\BulanController::class, 'update'])->name('bulan.update');
       Route::patch('/bulan/{bulan}/toggle', [App\Http\Controllers\Admin\BulanController::class, 'toggleAktif'])->name('bulan.toggle');

       // Master Data Jurusan
       Route::get('/jurusan', [App\Http\Controllers\Admin\JurusanController::class, 'index'])->name('jurusan.index');
       Route::post('/jurusan', [App\Http\Controllers\Admin\JurusanController::class, 'store'])->name('jurusan.store');
       Route::put('/jurusan/{jurusan}', [App\Http\Controllers\Admin\JurusanController::class, 'update'])->name('jurusan.update');
       Route::patch('/jurusan/{jurusan}/toggle', [App\Http\Controllers\Admin\JurusanController::class, 'toggleAktif'])->name('jurusan.toggle');

       // Master Data Unit Kerja (Bidang Dinas)
       Route::get('/bidang', [App\Http\Controllers\Admin\BidangController::class, 'index'])->name('bidang.index');
       Route::post('/bidang', [App\Http\Controllers\Admin\BidangController::class, 'store'])->name('bidang.store');
       Route::put('/bidang/{bidang}', [App\Http\Controllers\Admin\BidangController::class, 'update'])->name('bidang.update');
       Route::patch('/bidang/{bidang}/toggle', [App\Http\Controllers\Admin\BidangController::class, 'toggleAktif'])->name('bidang.toggle');
       ```

    2. **Dalam Subgroup `permission:kuota.slot-khusus`**:
       ```php
       // Jalur Khusus (Slot Khusus Kemitraan)
       Route::get('/slot-khusus', [App\Http\Controllers\Admin\SlotKhususController::class, 'index'])->name('slot-khusus.index');
       Route::post('/slot-khusus', [App\Http\Controllers\Admin\SlotKhususController::class, 'store'])->name('slot-khusus.store');
       Route::put('/slot-khusus/{slot_khusus}', [App\Http\Controllers\Admin\SlotKhususController::class, 'update'])->name('slot-khusus.update');
       Route::patch('/slot-khusus/{slot_khusus}/toggle', [App\Http\Controllers\Admin\SlotKhususController::class, 'toggleAktif'])->name('slot-khusus.toggle');

       // Pendaftaran Peserta Jalur Khusus (Task D)
       Route::get('/peserta-khusus/create', [App\Http\Controllers\Admin\PesertaJalurKhususController::class, 'create'])->name('peserta-khusus.create');
       Route::post('/peserta-khusus', [App\Http\Controllers\Admin\PesertaJalurKhususController::class, 'store'])->name('peserta-khusus.store');
       ```

- [x] **Task 2.2: Rute Redirect Backward Compatibility di SuperAdmin**
  - **File**: `routes/web.php`
  - Di dalam group `prefix('superadmin')->name('superadmin.')`, gantikan rute-rute lama dengan redirect aman:
    ```php
    Route::redirect('/bulan', '/admin/bulan', 302)->name('bulan.index');
    Route::redirect('/slot-khusus', '/admin/slot-khusus', 302)->name('slot-khusus.index');
    Route::redirect('/peserta-khusus/create', '/admin/peserta-khusus/create', 302)->name('peserta-khusus.create');
    Route::redirect('/jurusan', '/admin/jurusan', 302)->name('jurusan.index');
    Route::redirect('/bidang', '/admin/bidang', 302)->name('bidang.index');
    ```

---

### 📌 FASE 3: Implementasi Controller Baru di Namespace `Admin`

- [x] **Task 3.1: Buat `App\Http\Controllers\Admin\BulanController`**
  - Namespace: `App\Http\Controllers\Admin`
  - Perbarui pemanggilan view: `return view('admin.bulan.index', ...)`
  - Perbarui redirect: `return redirect()->route('admin.bulan.index')`
  - Audit log: simpan `admin_id` / `user_id: $request->user()->id`.

- [x] **Task 3.2: Buat `App\Http\Controllers\Admin\SlotKhususController`**
  - Namespace: `App\Http\Controllers\Admin`
  - Perbarui pemanggilan view: `return view('admin.slot-khusus.index', ...)`
  - Perbarui redirect: `return redirect()->route('admin.slot-khusus.index')`
  - Pastikan relasi `dibuat_oleh` diisi ID Admin yang membuat alokasi slot.

- [x] **Task 3.3: Buat `App\Http\Controllers\Admin\PesertaJalurKhususController`**
  - Namespace: `App\Http\Controllers\Admin`
  - Perbarui pemanggilan view: `return view('admin.peserta-khusus.create', ...)`
  - Redirect berhasil: `return redirect()->route('admin.slot-khusus.index')`

- [x] **Task 3.4: Buat `App\Http\Controllers\Admin\JurusanController`**
  - Namespace: `App\Http\Controllers\Admin`
  - Perbarui pemanggilan view: `return view('admin.jurusan.index', ...)`
  - Perbarui redirect: `return redirect()->route('admin.jurusan.index')`
  - Pertahankan seluruh validasi penegakan Aturan Bisnis BR-12 saat penonaktifan jurusan.

- [x] **Task 3.5: Buat `App\Http\Controllers\Admin\BidangController`**
  - Namespace: `App\Http\Controllers\Admin`
  - Perbarui pemanggilan view: `return view('admin.bidang.index', ...)`
  - Perbarui redirect: `return redirect()->route('admin.bidang.index')`
  - Pertahankan seluruh validasi penegakan Aturan Bisnis BR-12 saat penonaktifan unit kerja bidang.

- [x] **Task 3.6: Hapus Controller Lama di `SuperAdmin/`**
  - Hapus:
    - `app/Http/Controllers/SuperAdmin/BulanController.php`
    - `app/Http/Controllers/SuperAdmin/SlotKhususController.php`
    - `app/Http/Controllers/SuperAdmin/PesertaJalurKhususController.php`
    - `app/Http/Controllers/SuperAdmin/JurusanController.php`
    - `app/Http/Controllers/SuperAdmin/BidangController.php`

---

### 📌 FASE 4: Pemindahan & Penyesuaian Tampilan (Blade Views)

- [x] **Task 4.1: Buat View `resources/views/admin/bulan/index.blade.php`**
  - Breadcrumbs: `<a href="{{ route('admin.dashboard') }}">Administrasi</a>` -> `{{ __('Periode Magang') }}`.
  - Form action & data-action-url: gunakan `route('admin.bulan.*')`.
  - Pasang Read-Only banner untuk SuperAdmin:
    ```blade
    @if(auth()->user()?->isSuperAdmin())
        <div class="alert-readonly-banner" style="background: #EFF6FF; border: 1px solid #BFDBFE; border-left: 4px solid #2563EB; border-radius: 8px; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <div>
                <strong style="color: #1E40AF; display: block; font-size: 0.875rem;">{{ __('Mode Lihat Saja (Read-Only)') }}</strong>
                <span style="color: #1E3A8A; font-size: 0.8125rem;">{{ __('Akun SuperAdmin hanya memiliki hak akses pemantauan pada master periode magang. Penambahan dan perubahan jadwal dikelola oleh Administrator.') }}</span>
            </div>
        </div>
    @endif
    ```
  - Sembunyikan tombol mutasi untuk SuperAdmin:
    - `@if(! auth()->user()?->isSuperAdmin())` pada tombol *Tambah Bulan Magang*.
    - Pada tabel data: jika SuperAdmin, sembunyikan tombol *Edit* dan ganti switch toggle dengan badge teks status statis.

- [x] **Task 4.2: Buat View `resources/views/admin/slot-khusus/index.blade.php` & `peserta-khusus/create.blade.php`**
  - Breadcrumbs & route link diperbarui ke `admin.slot-khusus.*` dan `admin.peserta-khusus.*`.
  - Tambahkan banner Read-Only SuperAdmin.
  - Sembunyikan tombol *Tambah Slot Khusus*, tombol *Daftarkan Peserta Khusus*, tombol *Edit*, dan *Toggle Aktif* jika pengguna adalah SuperAdmin.

- [x] **Task 4.3: Buat View `resources/views/admin/jurusan/index.blade.php`**
  - Breadcrumbs & route link diperbarui ke `admin.jurusan.*`.
  - Tambahkan banner Read-Only SuperAdmin.
  - Sembunyikan tombol *Tambah Jurusan*, tombol *Edit*, dan *Toggle Aktif* jika pengguna adalah SuperAdmin.

- [x] **Task 4.4: Buat View `resources/views/admin/bidang/index.blade.php`**
  - Breadcrumbs & route link diperbarui ke `admin.bidang.*`.
  - Tambahkan banner Read-Only SuperAdmin.
  - Sembunyikan tombol *Tambah Unit Kerja*, tombol *Edit*, dan *Toggle Aktif* jika pengguna adalah SuperAdmin.

- [x] **Task 4.5: Hapus View Lama di `resources/views/superadmin/`**
  - Hapus direktori `resources/views/superadmin/bulan/`
  - Hapus direktori `resources/views/superadmin/slot-khusus/`
  - Hapus direktori `resources/views/superadmin/peserta-khusus/`
  - Hapus direktori `resources/views/superadmin/jurusan/`
  - Hapus direktori `resources/views/superadmin/bidang/`

---

### 📌 FASE 5: Penyesuaian JavaScript & Vite Pipeline

- [x] **Task 5.1: Buat Script JS di `resources/js/pages/admin/`**
  - `resources/js/pages/admin/bulan-index.js`: Ganti fallback URL hardcode `/superadmin/bulan/` menjadi `/admin/bulan/`.
  - `resources/js/pages/admin/slot-khusus.js`: Ganti fallback URL hardcode `/superadmin/slot-khusus/` menjadi `/admin/slot-khusus/`.
  - `resources/js/pages/admin/jurusan-index.js`: Ganti fallback URL hardcode `/superadmin/jurusan/` menjadi `/admin/jurusan/`.
  - `resources/js/pages/admin/bidang-index.js`: Ganti fallback URL hardcode `/superadmin/bidang/` menjadi `/admin/bidang/`.

- [x] **Task 5.2: Daftarkan di `vite.config.js` & Kompilasi Aset**
  - Daftarkan ke-4 file JS baru di array `input` `vite.config.js`.
  - Jalankan `npm run build` untuk memvalidasi bundle Vite berhasil tanpa error.

---

### 📌 FASE 6: Penataan Navigasi & Sidebar Layout (`resources/views/layouts/dashboard.blade.php`)

- [x] **Task 6.1: Menambahkan 4 Menu pada Sidebar Administrator**
  - **File**: `resources/views/layouts/dashboard.blade.php`
  - Di bawah navigasi Administrator (`@elseif($userRole === 'admin')`), setelah `Laporan & Ekspor`:
    ```blade
    <!-- KELOMPOK MASTER DATA & PERIODE MAGANG -->
    <span class="nav-section-label" style="margin-top: 1.25rem;">{{ __('Master & Konfigurasi') }}</span>

    <a href="{{ route('admin.bulan.index') }}" class="nav-item-link {{ request()->routeIs('admin.bulan.*') ? 'active' : '' }}">
        <span class="nav-item-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
        </span>
        <span>{{ __('Periode Magang') }}</span>
    </a>

    <a href="{{ route('admin.slot-khusus.index') }}" class="nav-item-link {{ request()->routeIs('admin.slot-khusus.*') || request()->routeIs('admin.peserta-khusus.*') ? 'active' : '' }}">
        <span class="nav-item-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
        </span>
        <span>{{ __('Jalur Khusus') }}</span>
    </a>

    <a href="{{ route('admin.jurusan.index') }}" class="nav-item-link {{ request()->routeIs('admin.jurusan.*') ? 'active' : '' }}">
        <span class="nav-item-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
        </span>
        <span>{{ __('Data Jurusan') }}</span>
    </a>

    <a href="{{ route('admin.bidang.index') }}" class="nav-item-link {{ request()->routeIs('admin.bidang.*') ? 'active' : '' }}">
        <span class="nav-item-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
        </span>
        <span>{{ __('Unit Kerja') }}</span>
    </a>
    ```

- [x] **Task 6.2: Pembaruan Sidebar SuperAdmin**
  - **File**: `resources/views/layouts/dashboard.blade.php`
  - Perbarui rute menu Periode Magang, Jalur Khusus, Data Jurusan, dan Unit Kerja di sidebar SuperAdmin agar mengarah ke `route('admin.bulan.index')`, `route('admin.slot-khusus.index')`, `route('admin.jurusan.index')`, dan `route('admin.bidang.index')`.
  - Masukkan ke dalam kelompok "Administrasi" di SuperAdmin sidebar (atau pertahankan link di submenu Konfigurasi yang mengarah ke route `admin.*` dengan mode Read-Only).

---

### 📌 FASE 7: Automated Testing & Verification Suite

- [x] **Task 7.1: Buat Feature Test Baru `tests/Feature/AdminMasterDataFeatureMigrationTest.php`**
  - Skenario pengujian wajib:
    1. **Admin Full Access (Happy Path)**:
       - `test_admin_dapat_melihat_halaman_keempat_modul()` -> Status 200 OK.
       - `test_admin_dapat_menambah_periode_bulan_magang()` -> POST `/admin/bulan`, assertRedirect & assertDatabaseHas.
       - `test_admin_dapat_memperbarui_periode_bulan_magang()` -> PUT `/admin/bulan/{id}`, status 302.
       - `test_admin_dapat_toggle_status_periode_magang()` -> PATCH `/admin/bulan/{id}/toggle`, status 302.
       - `test_admin_dapat_menambah_dan_toggle_slot_khusus()` -> POST & PATCH `/admin/slot-khusus`, status 302.
       - `test_admin_dapat_mendaftarkan_peserta_jalur_khusus()` -> POST `/admin/peserta-khusus`, status 302.
       - `test_admin_dapat_crud_dan_toggle_jurusan()` -> POST, PUT, PATCH `/admin/jurusan`.
       - `test_admin_dapat_crud_dan_toggle_unit_kerja_bidang()` -> POST, PUT, PATCH `/admin/bidang`.
    2. **SuperAdmin Read-Only Verification (Sad Path / Security Gate)**:
       - `test_superadmin_dapat_melihat_seluruh_halaman_keempat_modul_dalam_mode_readonly()` -> Status 200 OK, assertSee('Mode Lihat Saja').
       - `test_superadmin_dilarang_menambah_bulan_magang()` -> POST `/admin/bulan` sebagai SuperAdmin, assertStatus 403 Forbidden!
       - `test_superadmin_dilarang_memperbarui_bulan_magang()` -> PUT `/admin/bulan/{id}` sebagai SuperAdmin, assertStatus 403 Forbidden!
       - `test_superadmin_dilarang_toggle_bulan_magang()` -> PATCH `/admin/bulan/{id}/toggle` sebagai SuperAdmin, assertStatus 403 Forbidden!
       - `test_superadmin_dilarang_menambah_slot_khusus()` -> POST `/admin/slot-khusus` sebagai SuperAdmin, assertStatus 403 Forbidden!
       - `test_superadmin_dilarang_menambah_jurusan()` -> POST `/admin/jurusan` sebagai SuperAdmin, assertStatus 403 Forbidden!
       - `test_superadmin_dilarang_menambah_unit_kerja()` -> POST `/admin/bidang` sebagai SuperAdmin, assertStatus 403 Forbidden!
    3. **Backward Compatibility Redirect Test**:
       - `test_url_lama_superadmin_me_redirect_ke_rute_admin()` -> GET `/superadmin/bulan` -> assertRedirect('/admin/bulan'), dll.

- [x] **Task 7.2: Penyesuaian Test Suite Eksisting**
  - Perbarui referensi test di `EndToEndUatTest.php`, `AdminSuperAdminPhaseThreeTest.php`, `SuperAdminModuleEnhancementTest.php`, dll. yang sebelumnya melakukan mutasi sebagai `$this->superAdmin` agar menggunakan `$this->admin`.

---

## 6. Strategi Testing & Quality Gates

Jalankan serangkaian verifikasi ketat sebelum pekerjaan dinyatakan tuntas:

1. **Feature Testing**:
   ```powershell
   php artisan test --filter=AdminMasterDataFeatureMigrationTest
   ```
2. **Full Regression Testing**:
   ```powershell
   php artisan test
   ```
3. **Static Analysis**:
   ```powershell
   ./vendor/bin/phpstan analyse --memory-limit=1G
   ```
4. **Code Styling (PSR-12 / Pint)**:
   ```powershell
   ./vendor/bin/pint --test
   ```
5. **Asset Compilation**:
   ```powershell
   npm run build
   ```

---

## 7. Analisis Risiko & Mitigasi

| Risiko | Dampak | Mitigasi Senior Dev |
| :--- | :--- | :--- |
| **Bypass Mutasi oleh SuperAdmin** | Inkonsistensi data & pelanggaran wewenang. | Dilindungi ganda: `EnsureSuperAdminReadOnly` di level middleware route group + `authorize()` di FormRequest menolak non-Admin. |
| **Broken Links / Bookmarks** | User lama yang menyimpan URL `/superadmin/bulan` mengalami 404. | Sediakan `Route::redirect(...)` 302 dari URL lama ke URL baru di `/admin/...`. |
| **Pelanggaran Aturan Bisnis BR-12** | Periode/Jurusan/Bidang dinonaktifkan saat masih ada peserta aktif. | Logika validasi penonaktifan di `toggleAktif()` dipertahankan 100% pada Controller Admin baru. |
| **Pencemaran Audit Trail** | Log mutasi salah mencatat entitas penanggung jawab. | Sesuaikan audit logging pada controller baru untuk mencatat `admin_id` dan role aktor sebenarnya. |
| **Kesalahan JavaScript Action URL** | Modal edit submit ke endpoint lama dan error 404/403. | Skrip JS diperbarui menggunakan atribut HTML5 `data-action-url` yang dihasilkan secara dinamis oleh Blade. |

---

## 8. Definisi Selesai (Definition of Done)

- [x] Ke-4 fitur (Periode Magang, Jalur Khusus, Data Jurusan, Unit Kerja) berhasil dipindahkan ke bawah namespace `App\Http\Controllers\Admin` dan prefix `/admin/`.
- [x] Role `Admin` memiliki hak akses penuh untuk melihat, menambah, mengedit, dan me-nonaktifkan data pada ke-4 modul.
- [x] Role `SuperAdmin` hanya dapat melihat (Read-Only) pada ke-4 modul; setiap percobaan `POST`, `PUT`, `PATCH`, `DELETE` dari SuperAdmin ditolak dengan HTTP 403 Forbidden.
- [x] Tampilan antarmuka menampilkan badge "Mode Lihat Saja (Read-Only)" saat dikunjungi oleh SuperAdmin, serta tombol mutasi disembunyikan.
- [x] Navigasi sidebar Administrator menampilkan ke-4 menu baru dengan ikon rapi dan indikator aktif yang presisi.
- [x] Navigasi SuperAdmin diperbarui mengarah ke rute read-only, dan redirect 302 disiapkan untuk URL lama.
- [x] Seluruh skrip JS dan aset Vite berhasil dikompilasi dengan `npm run build`.
- [x] Seluruh test suite `php artisan test` berstatus hijau (PASSED).
- [x] Tidak ada pelanggaran `.agents/rules/AGENTS.md` (tidak ada JS/CSS di Blade, CSS terpusat di `resources/css`, JS di `resources/js`).
