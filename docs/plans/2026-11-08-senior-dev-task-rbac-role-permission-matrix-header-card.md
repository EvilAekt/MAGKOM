# DOKUMEN TUGAS SENIOR DEVELOPER & PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Fitur: Hero Banner Card Header & Dynamic Role-Based Access Control (RBAC) Matrix
### Modul SuperAdmin: Hak Akses dan Role Pengguna — Portal MagKom Sleman

---

**Kepada:** Junior Developer / AI Implementation Agent (Gemini 3.8 Flash, Cursor, Copilot)  
**Dari:** Senior Lead Software Engineer & Security Architect MAGKOM  
**Tanggal:** 8 November 2026  
**Status:** Approved for Implementation (Ready to Execute)  
**Prioritas:** P1 — Core Security & Visual Consistency  
**Target:** 
1. **Modernisasi UI Header**: Mengganti header teks polos pada halaman `superadmin/role/index.blade.php` dengan **Hero Banner Card** berkontur gradien biru khas Sleman MagKom (mengadopsi pola visual halaman `Logbook Peserta`).
2. **Dynamic RBAC & General Role Matrix**: Mengganti hardcoded `$matriksAkses` array di `RoleController` menjadi skema database relasional dinamis (`permissions` & `role_permissions`).
3. **SuperAdmin Permission Management**: SuperAdmin dapat membuat hak akses modul baru secara dinamis serta mencentang/menonaktifkan wewenang tiap role.
4. **General Role Policy**: Setiap perubahan hak akses pada suatu role **otomatis berlaku menyeluruh (general)** bagi semua pengguna yang memegang role tersebut di seluruh sistem.

---

## 1. LATAR BELAKANG & TUJUAN FITUR

### 1.1 Masalah Saat Ini
1. **Kesenjangan Visual (Visual Inconsistency)**:
   - Halaman bimbingan seperti *Logbook Peserta* ([Gambar Referensi 1](#gambar-1-referensi-hero-banner-logbook-peserta)) sudah memiliki **Hero Banner Card** gradien biru Sleman yang elegan dengan metadata informatif.
   - Sebaliknya, halaman *Hak Akses dan Role Pengguna* ([Gambar Eksisting 2](#gambar-2-kondisi-eksisting-hak-akses-polos)) saat ini masih menggunakan header teks polos tanpa kontainer visual, membuat halaman terlihat kosong dan tidak mencerminkan tingkat kepentingan modul keamanan SuperAdmin.
2. **Keterbatasan Hardcoded Matrix**:
   - Matriks hak akses 11 modul saat ini di-*hardcode* di dalam `App\Http\Controllers\SuperAdmin\RoleController::index()`.
   - SuperAdmin tidak dapat menambah wewenang baru, tidak dapat mengubah hak akses role secara dinamis, dan perubahan wewenang memerlukan intervensi kode sumber (*code deployment*).

### 1.2 Solusi yang Diterapkan
1. **Penerapan Hero Banner Card**:
   - Membangun kontainer banner gradien biru dengan judul tebal, baris metadata terstruktur (Wewenang, Total Role, Total Akun Aktif, Mode Kebijakan), serta tombol aksi cepat `[+ Tambah Hak Akses]`.
2. **Mesin RBAC Dinamis Terpusat**:
   - Membangun tabel `permissions` (master modul/hak akses) dan `role_permissions` (tabel perantara peran dan izin).
   - Memigrasikan 11 modul bawaan sistem melalui database seeder tanpa kehilangan riwayat matriks sebelumnya.
   - Menyediakan endpoint RESTful + Modal Dialog bagi SuperAdmin untuk:
     - Menambah hak akses modul baru secara dinamis.
     - Menyimpan pembaruan matriks (toggle switch/checkbox) untuk role `superadmin`, `admin`, `pembimbing`, dan `peserta`.
   - Menghubungkan pengecekan hak akses ke Laravel `Gate` / method `$user->hasPermission($slug)`.
   - Mencatat setiap mutasi wewenang ke dalam `audit_log` sistem (Audit Trail).

---

## 2. DOKUMENTASI VISUAL & WIREFRAME UI

### 2.1 Perbandingan Tangkapan Layar
- **Gambar 1 (Referensi Desain - Logbook Peserta)**:
  Hero banner dengan gradien biru royal (`#01347C` s/d `#003566`), border-radius `16px`, judul putih tebal, dan deretan metadata dipisah tanda *bullet* (`•`).
- **Gambar 2 (Kondisi Saat Ini - Hak Akses Polos)**:
  Teks `Hak Akses dan Role Pengguna` hitam polos di atas latar belakang putih standar.

### 2.2 UI Wireframe Hero Card Baru

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ [TOPBAR SISTEM: Breadcrumb: Beranda / SuperAdmin / Role & Hak Akses]                   [User Profile ▾]│
├────────────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                        │
│   ┌── [HERO BANNER CARD: HAK AKSES DAN ROLE PENGGUNA] ─────────────────────────────────────────────┐   │
│   │                                                                                                │   │
│   │   Hak Akses dan Role Pengguna                                     [ + Tambah Hak Akses ]       │   │
│   │                                                                                                │   │
│   │   Tata Kelola: Wewenang & Keamanan Sistem (RBAC)  •  4 Role Sistem                             │   │
│   │   •  142 Pengguna Terdaftar  •  Kebijakan Berlaku Menyeluruh (Global)                          │   │
│   │                                                                                                │   │
│   └────────────────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                        │
│   ┌── [KARTU STATISTIK 4 ROLE] ────────────────────────────────────────────────────────────────────┐   │
│   │  [SuperAdmin: 3]     [Admin Dinas: 8]     [Pembimbing: 24]     [Peserta Magang: 107]           │   │
│   └────────────────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                        │
│   ┌── [MATRIKS WEWENANG & HAK AKSES MODUL] ────────────────────────────────────────────────────────┐   │
│   │  Modul & Cakupan Fitur            │ SuperAdmin │ Administrasi │ Pembimbing │ Peserta │ Aksi    │   │
│   │ ──────────────────────────────────┼────────────┼──────────────┼────────────┼─────────┼──────── │   │
│   │  Portal Publik & Registrasi       │    [✓]     │     [✓]      │    [✓]     │   [✓]   │ System  │   │
│   │  Verifikasi Berkas & Seleksi      │    [✓]     │     [✓]      │    [ ]     │   [ ]   │ System  │   │
│   │  Logbook Harian & Progres Tugas   │    [✓]*    │     [ ]      │    [✓]     │   [✓]   │ System  │   │
│   │  Modul Tambahan Kustom Baru       │    [✓]     │     [✓]      │    [ ]     │   [ ]   │ [Hapus] │   │
│   │ ──────────────────────────────────┴────────────┴──────────────┴────────────┴─────────┴──────── │   │
│   │  [ Tombol Simpan Perubahan Matriks ]                                                           │   │
│   └────────────────────────────────────────────────────────────────────────────────────────────────┘   │
│                                                                                                        │
└────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. ARSITEKTUR DATABASE & SKEMA MIGRATION

Kita memerlukan 2 tabel baru untuk mengelola master izin dan relasi peran ke izin secara efisien:

### 3.1 Skema Tabel `permissions` & `role_permissions`
File migration baru: `database/migrations/2026_11_08_100000_create_permissions_and_role_permissions_tables.php`

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Master Hak Akses / Modul
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('nama_modul', 150);
            $table->string('slug', 100)->unique(); // Contoh: 'pendaftaran.verifikasi', 'logbook.review'
            $table->string('kategori', 50)->default('Umum'); // Kategori grouping di UI
            $table->text('deskripsi')->nullable();
            $table->boolean('is_system')->default(false); // Modul bawaan tidak boleh dihapus sembarangan
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['kategori', 'urutan']);
        });

        // 2. Tabel Relasi Hak Akses ke Role (General/Global Assignment)
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role', 30); // Enum: superadmin, admin, pembimbing, peserta
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['role', 'permission_id'], 'uq_role_permission');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
    }
};
```

### 3.2 Seeder Migrasi Data Bawaan Sistem (Backward Compatibility)
File seeder baru: `database/seeders/PermissionSeeder.php`

Mengisi 11 modul eksisting agar data sistem tetap utuh dan matriks langsung terisi saat pertama kali dijalankan:

| No | Nama Modul | Slug | SuperAdmin | Admin | Pembimbing | Peserta |
|---|---|---|:---:|:---:|:---:|:---:|
| 1 | Portal Publik & Registrasi Calon Peserta | `publik.portal-registrasi` | ✓ | ✓ | ✓ | ✓ |
| 2 | Pemberkasan & Pendaftaran Magang Mandiri | `pendaftaran.mandiri` | ✓ | ✗ | ✗ | ✓ |
| 3 | Verifikasi Berkas & Seleksi Administratif | `seleksi.verifikasi-berkas` | ✓ | ✓ | ✗ | ✗ |
| 4 | Manajemen Kuota Bulanan & Jurusan | `kuota.manajemen` | ✓ | ✓ | ✗ | ✗ |
| 5 | Penetapan Penempatan & Pembimbing Lapangan | `penempatan.pembimbing` | ✓ | ✓ | ✗ | ✗ |
| 6 | Logbook Harian & Pengelolaan Tugas Proyek | `bimbingan.logbook-tugas` | ✓ | ✗ | ✓ | ✓ |
| 7 | Penilaian Kinerja & Pengesahan Sertifikat | `penilaian.kinerja-sertifikat` | ✓ | ✓ | ✓ | ✗ |
| 8 | Manajemen Staf Internal & Akun Pegawai | `staf.manajemen` | ✓ | ✗ | ✗ | ✗ |
| 9 | Slot Khusus Kemitraan & Override Kuota | `kuota.slot-khusus` | ✓ | ✗ | ✗ | ✗ |
| 10 | Konfigurasi Parameter Sistem & Rubrik Bobot | `konfigurasi.parameter` | ✓ | ✗ | ✗ | ✗ |
| 11 | Jejak Audit Trail & Log Keamanan Sistem | `keamanan.audit-trail` | ✓ | ✗ | ✗ | ✗ |

Semua modul awal ini ditandai dengan `is_system = true`.

---

## 4. ARSITEKTUR BACKEND & LOGIKA BISNIS

### 4.1 Model & Relasi Eloquent
1. **Model `App\Models\Permission`**:
   - Fillable: `['nama_modul', 'slug', 'kategori', 'deskripsi', 'is_system', 'urutan']`.
   - Casts: `is_system => boolean`, `urutan => integer`.
   - Relasi `roles()`: relasi HasMany ke model `RolePermission`.

2. **Model `App\Models\RolePermission`**:
   - Fillable: `['role', 'permission_id']`.
   - Relasi `permission()`: BelongsTo ke `Permission`.

3. **Integrasi ke Model `App\Models\User`**:
   Tambahkan method helper untuk pengecekan hak akses instan:
   ```php
   /**
    * Cek apakah role pengguna memiliki hak akses spesifik.
    * Berlaku general untuk semua pengguna dengan role tersebut.
    */
   public function hasPermission(string $permissionSlug): bool
   {
       // SuperAdmin selalu bypass untuk modul sistem (kecuali mutasi bimbingan yang dilindungi Policy)
       if ($this->role === \App\Enums\RoleEnum::SuperAdmin) {
           return true;
       }

       return \Illuminate\Support\Facades\Cache::remember(
           "role_perms_{$this->role->value}",
           now()->addMinutes(30),
           fn () => \App\Models\RolePermission::where('role', $this->role->value)
               ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
               ->pluck('permissions.slug')
               ->toArray()
       ) && in_array($permissionSlug, \Illuminate\Support\Facades\Cache::get("role_perms_{$this->role->value}", []), true);
   }
   ```

### 4.2 Route Kontrak Baru
Di dalam grup route `routes/web.php` pada middleware `auth` dan `role:superadmin`:

```php
Route::prefix('superadmin/role')->name('superadmin.role.')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('index');
    Route::post('/permission', [RoleController::class, 'storePermission'])->name('permission.store');
    Route::put('/matrix', [RoleController::class, 'updateMatrix'])->name('matrix.update');
    Route::delete('/permission/{permission}', [RoleController::class, 'destroyPermission'])->name('permission.destroy');
});
```

### 4.3 Form Request Validation
1. **`App\Http\Requests\SuperAdmin\StorePermissionRequest`**:
   - `nama_modul`: `required|string|max:150`
   - `slug`: `nullable|string|max:100|alpha_dash|unique:permissions,slug` (otomatis di-generate dari nama jika kosong)
   - `kategori`: `required|string|max:50`
   - `deskripsi`: `nullable|string|max:500`
   - `roles`: `nullable|array`
   - `roles.*`: `in:superadmin,admin,pembimbing,peserta`

2. **`App\Http\Requests\SuperAdmin\UpdateRoleMatrixRequest`**:
   - `matrix`: `required|array`
   - `matrix.*.permission_id`: `required|exists:permissions,id`
   - `matrix.*.roles`: `nullable|array`
   - `matrix.*.roles.*`: `in:superadmin,admin,pembimbing,peserta`

### 4.4 Controller Orchestration (`RoleController`)
- **`index()`**:
  Mengambil statistik pengguna per role, total pengguna keseluruhan, serta daftar seluruh permissions beserta relasi `role_permissions` terpetakan.
- **`storePermission()`**:
  1. Buat record permission baru (`is_system = false`).
  2. Sisipkan relasi role yang dipilih ke tabel `role_permissions`.
  3. Bersihkan cache wewenang role (`Cache::forget("role_perms_{$role}")`).
  4. Catat aktivitas ke `AuditService` (Aksi: `TAMBAH_HAK_AKSES`).
  5. Kembalikan response redirect dengan pesan sukses / JSON jika AJAX.
- **`updateMatrix()`**:
  1. Mulai database transaction (`DB::transaction`).
  2. Perbarui pemetaan `role_permissions` sesuai data checkbox yang dikirimkan.
  3. Jaga wewenang mutlak SuperAdmin pada modul-modul sistem inti (*core invariant*).
  4. Hapus cache semua role (`role_perms_superadmin`, `role_perms_admin`, dll.).
  5. Catat aktivitas ke `AuditService` (Aksi: `PERUBAHAN_MATRIKS_HAK_AKSES`).
  6. Commit transaction dan kembalikan feedback visual.
- **`destroyPermission()`**:
  1. Validasi: jika `is_system == true`, tolak dengan status 403 Forbidden.
  2. Hapus modul dan relasinya (`cascadeOnDelete`).
  3. Bersihkan cache & catat audit log.

---

## 5. SPESIFIKASI FRONTEND & VIEW BLADE

### 5.1 Perubahan Markup Blade (`resources/views/superadmin/role/index.blade.php`)
> **PENTING (Aturan AGENTS.md Bagian 3 & 4)**:
> - Dilarang menulis tag `<style>` atau `<script>` di dalam Blade.
> - Hapus tag `<style>` dark-mode override yang saat ini ada di dalam file Blade, pindahkan seluruh styling ke `resources/css/pages/superadmin/role-index.css`.
> - Markup Blade hanya berisi elemen semantik, form modal, dan token CSS class.

Struktur Hero Banner baru di Blade:
```html
<div class="sa-role-container">
    {{-- 1. Hero Banner Khas Sleman MagKom --}}
    <div class="sa-role-banner">
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">Hak Akses dan Role Pengguna</h1>
                <div class="banner-subtitle">
                    <span>Tata Kelola: <strong>Wewenang & Keamanan Sistem (RBAC)</strong></span>
                    <span class="bullet-separator">&bull;</span>
                    <span>Cakupan: <strong>4 Role Utama</strong></span>
                    <span class="bullet-separator">&bull;</span>
                    <span>Total Pengguna: <strong>{{ $totalPengguna }} Akun Terdaftar</strong></span>
                    <span class="bullet-separator">&bull;</span>
                    <span>Kebijakan: <strong>Berlaku Global per Role</strong></span>
                </div>
            </div>
            <div class="banner-actions">
                <button type="button" class="btn-banner-action" id="btnBukaModalTambah">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Hak Akses
                </button>
            </div>
        </div>
    </div>

    {{-- 2. Kartu Statistik Ringkasan 4 Role --}}
    ...

    {{-- 3. Form Matriks Hak Akses (Interactive Checkboxes + Save Button) --}}
    ...
</div>

{{-- 4. Modal Dialog Tambah Hak Akses --}}
...
```

### 5.2 CSS Terpusat (`resources/css/pages/superadmin/role-index.css`)
Buat file stylesheet baru yang scoped dan daftarkan di `vite.config.js` atau di-include melalui `@vite`:

```css
/* Hero Banner Styling - Sleman MagKom Industrial Blue */
.sa-role-banner {
    background: radial-gradient(80% 120% at 100% 0%, rgba(63, 81, 181, 0.45), transparent 60%),
                linear-gradient(135deg, #01347C 0%, #003566 100%);
    border-radius: 16px;
    padding: 28px 32px;
    color: #ffffff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px -2px rgba(1, 52, 124, 0.25);
    border: 1px solid rgba(255, 255, 255, 0.12);
}

.banner-top-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1.25rem;
    flex-wrap: wrap;
}

.banner-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #ffffff;
    margin: 0 0 6px 0;
    letter-spacing: -0.01em;
    line-height: 1.3;
}

.banner-subtitle {
    font-size: 0.9375rem;
    color: rgba(255, 255, 255, 0.92);
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    line-height: 1.5;
}

.bullet-separator {
    opacity: 0.6;
}

.btn-banner-action {
    background: #ffffff;
    color: #01347C;
    border: none;
    border-radius: 10px;
    padding: 0.65rem 1.25rem;
    font-size: 0.875rem;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.btn-banner-action:hover {
    background: #f0f4ff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}
```

### 5.3 JavaScript Interaktif ES Module (`resources/js/pages/superadmin/role-index.js`)
- Menangani pembukaan & penutupan Modal Tambah Hak Akses (ESC key, klik luar, tombol tutup).
- Menangani konfirmasi penghapusan modul non-sistem.
- Menangani tombol simpan matriks wewenang dengan AJAX/Fetch API atau form POST standar dilengkapi CSRF Token.

---

## 6. TAHAPAN EKSEKUSI TUGAS (STEP-BY-STEP CHECKLIST)

Untuk dikerjakan secara berurutan oleh Junior Developer / AI Agent:

### Tahap 1: Migrasi Database & Seeder
- [ ] Buat file migration: `database/migrations/2026_11_08_100000_create_permissions_and_role_permissions_tables.php`.
- [ ] Jalankan pretend run `php artisan migrate --pretend` untuk memastikan SQL aman.
- [ ] Eksekusi migration `php artisan migrate`.
- [ ] Buat file seeder: `database/seeders/PermissionSeeder.php` dengan data 11 modul eksisting.
- [ ] Jalankan seeder: `php artisan db:seed --class=PermissionSeeder`.

### Tahap 2: Backend Models, Requests & Controller
- [ ] Buat Eloquent Model `App\Models\Permission` dan `App\Models\RolePermission`.
- [ ] Tambahkan method `hasPermission(string $slug)` pada model `App\Models\User`.
- [ ] Buat Form Request `App\Http\Requests\SuperAdmin\StorePermissionRequest` dan `UpdateRoleMatrixRequest`.
- [ ] Tambahkan method `storePermission`, `updateMatrix`, dan `destroyPermission` pada `App\Http\Controllers\SuperAdmin\RoleController`.
- [ ] Daftarkan route baru di `routes/web.php` di bawah prefix `superadmin/role`.

### Tahap 3: Styling & Asset Pipeline (CSS & JS)
- [ ] Buat stylesheet `resources/css/pages/superadmin/role-index.css` (termasuk Dark Mode support dan token warna DS-MagKom).
- [ ] Buat skrip modul `resources/js/pages/superadmin/role-index.js` untuk modal dan interaksi tabel matriks.
- [ ] Daftarkan kedua aset tersebut ke dalam array Vite atau include via `@vite` di Blade.
- [ ] Jalankan `npm run build` untuk memvalidasi kompilasi aset tidak menghasilkan error.

### Tahap 4: Refactoring View Blade
- [ ] Buka `resources/views/superadmin/role/index.blade.php`.
- [ ] Hapus inline `<style>` lama dan load aset Vite.
- [ ] Pasang komponen **Hero Banner Card** dengan token visual baru.
- [ ] Perbarui tabel matriks agar menampilkan checkbox wewenang dinamis untuk setiap role.
- [ ] Tambahkan tombol Simpan Matriks dan Modal Tambah Hak Akses.

### Tahap 5: Testing & Quality Gate
- [ ] Buat Feature Test Pest/PHPUnit: `tests/Feature/SuperAdmin/RolePermissionTest.php`.
  - Uji SuperAdmin dapat melihat matriks wewenang.
  - Uji SuperAdmin dapat menambah modul hak akses baru.
  - Uji SuperAdmin dapat memperbarui matriks wewenang role.
  - Uji perubahan wewenang pada role otomatis berpengaruh pada user dengan role tersebut (`hasPermission`).
  - Uji modul sistem (`is_system = true`) tidak dapat dihapus.
  - Uji user non-SuperAdmin (Admin, Pembimbing, Peserta) ditolak mengakses endpoint ini (403 Forbidden).
- [ ] Jalankan PHPStan analyse: `./vendor/bin/phpstan analyse --memory-limit=1G`.
- [ ] Jalankan Laravel Pint: `./vendor/bin/pint --test`.

---

## 7. CHECKLIST KEAMANAN & INTEGRITAS (OWASP & MAGKOM RULES)

1. **RBAC Isolation**: Hanya role `SuperAdmin` yang boleh mengakses route, memanggil endpoint, atau mengubah matriks hak akses (`role:superadmin`).
2. **Audit Logging**: Setiap aksi penambahan hak akses modul baru atau pengubahan checkbox matriks wajib dicatat di tabel `audit_log` via `AuditService`.
3. **Core Invariant**: Role `superadmin` tidak boleh dicabut haknya pada modul-modul sistem krusial (manajemen staf, audit trail, parameter sistem).
4. **SuperAdmin Read-Only Restriction**: Pembatasan bahwa SuperAdmin tetap read-only pada bimbingan peserta (tidak boleh melakukan penilaian langsung atau persetujuan logbook) tetap dijaga di Policy masing-masing.
5. **No Blade Styling**: Tidak ada kode CSS `<style>` atau JavaScript inline `<script>` di dalam file Blade. Semua styling murni di `resources/css/` dan interaksi di `resources/js/`.

---

**Approval:**  
*Lead Architect & Security Specialist MAGKOM*  
*Status: Siap Ditugaskan & Dieksekusi.*

