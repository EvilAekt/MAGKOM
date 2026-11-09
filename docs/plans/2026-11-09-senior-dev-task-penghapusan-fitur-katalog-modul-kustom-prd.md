# DOKUMEN TUGAS SENIOR DEVELOPER & PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Penghapusan Menyeluruh Fitur Katalog Modul Kustom (Decommissioning Low-Code Studio & Dynamic Modules)

---

**Kepada:** Junior Developer / AI Implementation Agent (Gemini 3.8 Flash, Cursor, Copilot)  
**Dari:** Senior Lead Software Engineer & Backend-Frontend Architect MAGKOM  
**Tanggal:** 9 November 2026  
**Status:** Ready for Implementation / Plan Approved  
**Prioritas:** P1 — High Architecture Cleanliness, Bloat Removal, Security Hardening & Zero Overhead  
**Target Modul:** Modul Dinamis / Low-Code Studio (`modul-builder`, `modul-data`, `public-modul`, `kustom-modul`)  

---

## 📌 1. RINGKASAN EKSEKUTIF (Executive Summary)

Berdasarkan keputusan strategis arsitektur aplikasi dan arahan operasional resmi dari pengelola Portal MagKom Dinas Komunikasi dan Informatika Kabupaten Sleman:
1. Fitur **Katalog Modul Kustom (Low-Code Studio)** yang diakses melalui menu sidebar **"Studio Modul"** (`/superadmin/modul-builder`) dan fitur eksekusi modul kustom dinamis (`/superadmin/modul/*`, `/kustom-modul/*`, `/publik/modul/*`) **RESMI DITIADAKAN DAN DIHAPUS SECARA TOTAL DARI SISTEM MAGKOM**.
2. **Alasan Penghapusan**:
   - Seluruh kebutuhan tata kelola magang Kominfo telah terakomodasi secara terstruktur dan baku pada modul-modul resmi: *Pendaftaran, Verifikasi Berkas, Kuota & Periode Magang, Penempatan & Mentor, Tugas Magang, Logbook & Presensi, Manajemen Pengguna & Hak Akses, serta Konfigurasi Portal*.
   - Fitur Low-Code Studio bersifat eksperimental, tidak digunakan dalam kegiatan operasional riil (0 baris data tersimpan pada basis data operasional), namun membebani sistem dengan *query overhead* pada setiap *page render* di `AppServiceProvider` (`$sidebarDynamicModules`).
   - Menghapus fitur ini akan mengurangi kerentanan celah keamanan (*attack surface area* dari manipulasi schema/tabel dinamis), mempercepat kompilasi build aset, membersihkan menu sidebar antarmuka, dan menghemat konsumsi memori server.

---

## 📸 2. DOKUMENTASI VISUAL & ELEMEN YANG DIHAPUS

Berdasarkan screenshot dari administrator:
1. **Menu Sidebar yang Dihapus**:
   - Item menu sidebar bergaya ikon tumpukan layers: **`Studio Modul`** (di bawah grup `SISTEM`).
   - Grup sub-menu dinamis: **`MODUL KUSTOM`** yang sebelumnya di-render secara otomatis jika terdapat modul aktif.
2. **Halaman Antarmuka yang Dihapus**:
   - Halaman **Katalog Modul Kustom (Low-Code Studio)** (`/superadmin/modul-builder`).
   - Formulir **Buat Modul Studio Baru** (`/superadmin/modul-builder/create`).
   - Formulir **Ubah Desain Modul** (`/superadmin/modul-builder/{modul}/edit`).
   - Antarmuka **Eksekutor Universal Data Modul** (`/superadmin/modul/{slug}`).
   - Halaman tampilan publik read-only modul (`/publik/modul/{slug}`).

---

## 🏗️ 3. INVENTARIS FILE & PEMETAAN DAMPAK (Impact Matrix)

Penghapusan ini mencakup 9 komponen utama di seluruh lapisan stack Laravel & Vite:

```
┌────────────────────────────────────────────────────────────────────────┐
│ 1. ROUTING (routes/web.php)                                            │
│    - Hapus grup rute /publik/modul                                     │
│    - Hapus grup rute /superadmin/modul-builder                         │
│    - Hapus grup rute /superadmin/modul/{slug}                          │
│    - Hapus grup rute /kustom-modul/{slug}                              │
│                                                                        │
│ 2. CONTROLLERS (app/Http/Controllers/)                                 │
│    - Hapus app/Http/Controllers/SuperAdmin/ModulBuilderController.php  │
│    - Hapus app/Http/Controllers/SuperAdmin/ModulDataController.php     │
│    - Hapus app/Http/Controllers/PublicModulController.php              │
│                                                                        │
│ 3. MODELS (app/Models/)                                                │
│    - Hapus app/Models/ModulDinamis.php                                 │
│    - Hapus app/Models/ModulDinamisField.php                            │
│    - Hapus app/Models/ModulDinamisData.php                             │
│                                                                        │
│ 4. SERVICE PROVIDER (app/Providers/AppServiceProvider.php)             │
│    - Hapus view composer query $sidebarDynamicModules                  │
│                                                                        │
│ 5. LAYOUT BLADE (resources/views/layouts/dashboard.blade.php)          │
│    - Hapus item menu Studio Modul di sidebar SuperAdmin               │
│    - Hapus blok loop @foreach($sidebarDynamicModules)                  │
│                                                                        │
│ 6. VIEW BLADE DIRECTORIES (resources/views/)                           │
│    - Hapus direktori resources/views/superadmin/modul-builder/         │
│    - Hapus direktori resources/views/superadmin/modul-data/            │
│                                                                        │
│ 7. ASSET STYLING & BUNDLER (resources/css/ & vite.config.js)           │
│    - Hapus resources/css/pages/superadmin/modul-builder.css            │
│    - Hapus entri modul-builder.css dari vite.config.js                 │
│                                                                        │
│ 8. AUDIT LOG & AUDIT SERVICE                                           │
│    - Hapus kanal 'dynamic-module' dari AuditLogController::CHANNELS    │
│    - Sesuaikan AuditService::resolveChannel()                          │
│                                                                        │
│ 9. DATABASE SCHEMA MIGRATION                                           │
│    - Buat migration drop tables (modul_dinamis_data, field, dinamis)   │
│                                                                        │
│ 10. TEST SUITES (tests/)                                               │
│    - Hapus tests/Feature/ModulDinamisBasefwParityTest.php              │
│    - Hapus TEST 6, 7, 8, 9 dari SuperAdminMasterArchitectureTest.php   │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 🛠️ 4. SPESIFIKASI TEKNIS & PANDUAN LANGKAH (Atomic Tasks)

Junior Developer atau AI Implementation Agent (Gemini 3.8 Flash) **WAJIB** mengikuti langkah implementasi di bawah ini secara presisi dan teratur:

---

### TUGAS 1: Bersihkan Seluruh Rute Modul Dinamis di `routes/web.php`
**Lokasi Berkas:** `routes/web.php`

1. **Hapus import controller berikut di bagian atas berkas**:
   ```php
   use App\Http\Controllers\PublicModulController;
   use App\Http\Controllers\SuperAdmin\ModulBuilderController;
   use App\Http\Controllers\SuperAdmin\ModulDataController;
   ```
2. **Hapus blok rute modul publik** (baris ~100):
   ```php
   // Modul Publik Read-Only (Ekuivalen Basefw PUBLIC_list & PUBLIC_detail)
   Route::prefix('publik/modul')->name('public.modul.')->group(function () {
       Route::get('/{slug}', [PublicModulController::class, 'index'])->name('index');
       Route::get('/{slug}/{id}', [PublicModulController::class, 'show'])->name('show');
   });
   ```
3. **Hapus blok rute SuperAdmin Modul Builder & Modul Data** (baris ~456–475):
   ```php
   // Pembuat Modul Dinamis (Low-Code Engine & Reverse-Engineering Basefw) (Tri-Core SuperAdmin)
   Route::get('/modul-builder/api/tables', [ModulBuilderController::class, 'apiGetTables'])->name('modul-builder.api.tables');
   ...
   Route::delete('/modul/{slug}/{id}', [ModulDataController::class, 'destroy'])->name('modul.destroy');
   ```
4. **Hapus blok rute All-Role kustom-modul** (baris ~491–501):
   ```php
   // Eksekutor Universal Data Modul Dinamis untuk Semua Role Terotorisasi (target_roles)
   Route::middleware(['auth', 'verified', 'must_change_password', 'account.active'])->prefix('kustom-modul')->name('kustom-modul.')->group(function () {
       ...
   });
   ```

---

### TUGAS 2: Hapus Menu "Studio Modul" & Blok Dinamis di `dashboard.blade.php`
**Lokasi Berkas:** `resources/views/layouts/dashboard.blade.php`

1. **Hapus link navigasi "Studio Modul"** di bawah section SISTEM (baris ~3431–3441):
   ```blade
   {{-- HAPUS BLOK INI --}}
   <a href="{{ route('superadmin.modul-builder.index') }}" class="nav-item-link {{ request()->routeIs('superadmin.modul-builder.*') ? 'active' : '' }}">
       <span class="nav-item-icon">
           <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
               <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
               <polyline points="2 17 12 22 22 17"></polyline>
               <polyline points="2 12 12 17 22 12"></polyline>
           </svg>
       </span>
       <span>{{ __('Studio Modul') }}</span>
       <span class="sr-only">Pembuat Modul Low-Code Module Builder</span>
   </a>
   ```

2. **Hapus seluruh blok loop `$sidebarDynamicModules`** (pada sidebar SuperAdmin dan Admin/Pembimbing baris ~3260–3275 dan ~3490–3504):
   ```blade
   {{-- HAPUS BLOK INI --}}
   @if(isset($sidebarDynamicModules) && $sidebarDynamicModules->isNotEmpty())
       <span class="nav-section-label">{{ __('MODUL KUSTOM') }}</span>
       @foreach($sidebarDynamicModules as $customModul)
           ...
       @endforeach
   @endif
   ```

---

### TUGAS 3: Hapus Overhead Query Global di `AppServiceProvider.php`
**Lokasi Berkas:** `app/Providers/AppServiceProvider.php`

Hapus import `use App\Models\ModulDinamis;` dan hapus view composer closure yang melakukan caching/query `$sidebarDynamicModules`:

#### Kode SEBELUM:
```php
        View::composer('layouts.dashboard', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $userRoleLower = strtolower((string) ($user->role?->value ?? $user->role ?? ''));

                $allModules = Cache::remember('sidebar_dynamic_modules_list', 300, function () {
                    try {
                        return ModulDinamis::where('status_aktif', true)->orderBy('urutan')->get();
                    } catch (\Throwable $e) {
                        return collect();
                    }
                });

                $accessibleModules = $allModules->filter(function ($module) use ($userRoleLower) {
                    if ($userRoleLower === 'superadmin') {
                        return true;
                    }
                    $roles = array_map('strtolower', (array) ($module->target_roles ?? []));

                    return in_array($userRoleLower, $roles, true);
                });

                $view->with('sidebarDynamicModules', $accessibleModules);
            }
        });
```

#### Kode SESUDAH:
Hapus seluruh method call `View::composer('layouts.dashboard', ...)` tersebut sehingga `boot()` menjadi bersih dan tidak ada query modul dinamis yang berjalan di setiap pemanggilan halaman.

---

### TUGAS 4: Hapus Berkas Controller, Model, dan View
Jalankan penghapusan file berikut:

1. **Controllers**:
   - `app/Http/Controllers/SuperAdmin/ModulBuilderController.php`
   - `app/Http/Controllers/SuperAdmin/ModulDataController.php`
   - `app/Http/Controllers/PublicModulController.php`
2. **Models**:
   - `app/Models/ModulDinamis.php`
   - `app/Models/ModulDinamisField.php`
   - `app/Models/ModulDinamisData.php`
3. **Views**:
   - Direktori `resources/views/superadmin/modul-builder/` beserta seluruh file di dalamnya (`index.blade.php`, `create.blade.php`, `edit.blade.php`).
   - Direktori `resources/views/superadmin/modul-data/` beserta seluruh file di dalamnya (`index.blade.php`).
4. **CSS**:
   - `resources/css/pages/superadmin/modul-builder.css`

---

### TUGAS 5: Bersihkan Entry Point di `vite.config.js`
**Lokasi Berkas:** `vite.config.js`

Hapus baris `'resources/css/pages/superadmin/modul-builder.css',` dari array `input` konfigurasi plugin Laravel Vite.

---

### TUGAS 6: Bersihkan Kanal Audit Log Terkait (`dynamic-module`)
**Lokasi Berkas:**
1. `app/Http/Controllers/SuperAdmin/AuditLogController.php`
2. `app/Services/AuditService.php`

1. **Pada `AuditLogController.php`**:
   Hapus entri `'dynamic-module'` dari konstanta `CHANNELS` (sehingga kini menyisakan **11 Kanal Aktif Inti**).
2. **Pada `AuditService.php`**:
   Hapus deteksi entitas modul dinamis:
   ```php
   // Hapus baris ini:
   if (str_starts_with((string) $entitas, 'modul_dinamis') || str_starts_with((string) $entitas, 'modul_data') || str_contains($text, 'modul')) {
       return 'dynamic-module';
   }
   ```

---

### TUGAS 7: Buat Migration Drop Table untuk Database Sanitasi
Buat migration baru melalui artisan:
```bash
php artisan make:migration drop_dynamic_modules_tables
```

Isi method `up()` dengan drop aman jika tabel tersedia:
```php
public function up(): void
{
    Schema::dropIfExists('modul_dinamis_data');
    Schema::dropIfExists('modul_dinamis_field');
    Schema::dropIfExists('modul_dinamis');
}

public function down(): void
{
    // Feature deprecated & permanently removed — down method intentionally left empty or no-op
}
```
Lalu jalankan migrasi:
```bash
php artisan migrate --force
```

---

### TUGAS 8: Hapus & Sesuaikan Automated Tests
1. **Hapus berkas test spesifik modul dinamis**:
   - `tests/Feature/ModulDinamisBasefwParityTest.php`
2. **Pada `tests/Feature/SuperAdminMasterArchitectureTest.php`**:
   - Hapus pemanggilan kanal `Modul Kustom Dinamis` di TEST 1.
   - Hapus method TEST 6 (`test_superadmin_can_create_custom_dynamic_module_with_various_field_types`).
   - Hapus method TEST 7 (`test_superadmin_can_perform_full_crud_on_dynamic_custom_module_data`).
   - Hapus method TEST 8 (`test_superadmin_can_toggle_module_active_status_and_delete_module`).
   - Hapus method TEST 9 (`test_rbac_restricts_non_superadmin_from_accessing_modul_builder`).

---

## 🛡️ 5. QUALITY GATES & VERIFIKASI SEBELUM SELESAI

Setelah seluruh langkah di atas diselesaikan, lakukan verifikasi wajib:

1. **Kompilasi Aset Frontend (Vite)**:
   ```bash
   npm run build
   ```
   *Ekspektasi:* Sukses 100% tanpa error kehilangan modul `modul-builder.css`.

2. **Automated Unit & Feature Tests**:
   ```bash
   php artisan test --filter=SuperAdminMasterArchitectureTest
   php artisan test --filter=SuperAdminDashboardAndAuditLogTest
   php artisan test --filter=SuperAdminPortalVisualTest
   ```
   *Ekspektasi:* **PASS 100%**.

3. **Verifikasi Browser & HTTP Status**:
   - Buka `http://localhost:8000/superadmin/modul-builder`: Menghasilkan **404 Not Found** (memvalidasi rute telah dicabut total).
   - Buka `http://localhost:8000/superadmin/dashboard`: Menu **"Studio Modul"** sudah tidak ada di sidebar kiri.
   - Buka `http://localhost:8000/superadmin/audit-log`: Kanal menampilkan **11 Kanal Audit Stream**, tanpa kanal `Modul Kustom Dinamis`.

---

## 📋 6. CHECKLIST PENYELESAIAN (Acceptance Criteria)

| No | Kriteria Penerimaan | Status |
|:--:|:--------------------|:------:|
| 1  | Seluruh rute modul builder, modul data, dan modul publik dihapus dari `routes/web.php`. | [ ] |
| 2  | Menu "Studio Modul" dan grup "MODUL KUSTOM" dihapus dari `dashboard.blade.php`. | [ ] |
| 3  | Query overhead `$sidebarDynamicModules` di `AppServiceProvider.php` dihapus bersih. | [ ] |
| 4  | Berkas Controllers, Models, Views Blade, dan CSS modul builder telah dihapus dari repositori. | [ ] |
| 5  | Entri `modul-builder.css` dihapus dari `vite.config.js` dan `npm run build` sukses. | [ ] |
| 6  | Kanal `dynamic-module` dihapus dari Audit Log (menyisakan 11 kanal aktif). | [ ] |
| 7  | Tabel `modul_dinamis`, `modul_dinamis_field`, `modul_dinamis_data` di-drop via migration. | [ ] |
| 8  | Seluruh automated test suite tetap hijau (**PASS 100%**). | [ ] |

---
*Dokumen PRD & Rencana Kerja ini diterbitkan oleh Senior Lead Software Engineer MAGKOM.*

