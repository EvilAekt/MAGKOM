# TASK BRIEF SENIOR DEVELOPER & PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Penghapusan Menyeluruh Fitur Monitoring Pelaksanaan Magang Aktif di Portal SuperAdmin (Decommissioning SuperAdmin Active Mentee Monitoring)

---

> **Dari:** Senior Lead Software Architect & Backend-Frontend Lead MAGKOM  
> **Kepada:** Junior Developer / AI Implementation Agent (Gemini 3.8 Flash / Claude / Antigravity Agent)  
> **Target Repositori:** MAGKOM (Portal Manajemen Magang Dinas Kominfo Sleman)  
> **Tanggal Dokumen:** 9 November 2026  
> **Status:** APPROVED FOR IMPLEMENTATION  
> **Prioritas:** P1 — High Architecture Cleanliness, Bloat Removal, Zero Orphaned Routes/Views  
> **Tingkat Kesulitan:** Rendah - Menengah (*Feature Decommissioning, Route Cleanup, Sidebar Navigation Adjustment, Dashboard Relinking, & Test Suite Maintenance*)  
> **Dokumentasi Terkait:** `AGENTS.md`, `docs/01-PRD-MagKom.md`, `docs/02-TDD-MagKom.md`, `docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`, `docs/architecture/ROUTES.md`  

---

## 📌 1. 🔍 LATAR BELAKANG & TUJUAN TASK (Executive Summary)

### 1.1 Masalah & Alasan Penghapusan Fitur
Dalam evaluasi arsitektur tata kelola Portal MagKom (Dinas Komunikasi dan Informatika Kabupaten Sleman):
1. **Pemisahan Peran (Separation of Concerns / RBAC Boundary)**:
   - Peran **SuperAdmin** berfokus pada **Tata Kelola Sistem Eksekutif, Manajemen Akun Pengguna & Hak Akses, Master Data Konfigurasi, Keamanan, dan Audit Trail**.
   - Pelaksanaan bimbingan harian peserta (tugas, logbook, interaksi harian, dan monitoring progres) merupakan **wewenang penuh Pembimbing Lapangan** (portal `/pembimbing`) serta **Staf Administrasi Magang** (`/admin/penempatan`).
2. **Redundansi Halaman (*Visual & Functional Redundancy*)**:
   - Halaman **"Monitoring Pelaksanaan Magang Aktif"** (`/superadmin/monitoring-peserta`) sebelumnya dibuat sebagai tampilan baca-saja (*read-only mirror*) dari bimbingan peserta.
   - Dalam praktiknya, fitur ini tidak lagi diperlukan oleh SuperAdmin karena SuperAdmin telah memiliki:
     - **Dashboard Eksekutif** (`/superadmin/dashboard`) dengan widget analitik agregat, utilisasi beban pembimbing, dan pelacak penyelesaian magang terdekat.
     - **Menu Arsip Peserta** (`/superadmin/arsip`) untuk riwayat peserta yang telah selesai/lulus.
     - Akses langsung ke modul **Penempatan Peserta** (`/admin/penempatan`) untuk tata kelola penempatan peserta aktif dinas.
3. **Penyederhanaan Antarmuka (*Decluttering UI/UX*)**:
   - Menghapus menu ini membersihkan navigasi sidebar grup **"Peserta"**, menghapus kode pengontrol dan tampilan yang tidak lagi dipelihara (*technical debt*), serta mengeliminasi rute baca-saja yang redundan.

### 1.2 Sasaran Akhir (Definition of Done)
1. Seluruh rute `/superadmin/monitoring-peserta` dan `/superadmin/monitoring-peserta/{penempatan}` dihapus tuntas dari [`routes/web.php`](file:///g:/MAGKOM/routes/web.php).
2. Controller [`app/Http/Controllers/SuperAdmin/MonitoringPesertaController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/MonitoringPesertaController.php) dihapus permanen.
3. Seluruh berkas Blade pada folder [`resources/views/superadmin/monitoring/`](file:///g:/MAGKOM/resources/views/superadmin/monitoring/) (`index.blade.php` dan `show.blade.php`) beserta direktorinya dihapus.
4. Item menu **"Monitoring Peserta"** pada sidebar [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php) dihapus secara rapi tanpa merusak tata letak menu "Arsip Peserta".
5. Seluruh tautan dashboard internal SuperAdmin ([`resources/views/superadmin/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php) dan [`_tab-bimbingan.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php)) yang sebelumnya mengarah ke `superadmin.monitoring.index` dialihkan secara tepat ke tujuan operasional resmi (`admin.waiting-list.index` dan `admin.penempatan.index`).
6. Test suite fitur ([`tests/Feature/AdminSuperAdminPhaseTwoTest.php`](file:///g:/MAGKOM/tests/Feature/AdminSuperAdminPhaseTwoTest.php) dan [`tests/Feature/SuperAdminPortalVisualTest.php`](file:///g:/MAGKOM/tests/Feature/SuperAdminPortalVisualTest.php)) disesuaikan, serta ditambahkan verifikasi bahwa rute yang dihapus kini merespons `404 Not Found`.
7. Seluruh pengujian berjalan hijau (*100% PASS*) dengan PHP 8.3 dan lolos audit Pint/PHPStan.

---

## 📸 2. 🖼️ BUKTI VISUAL ELEMEN YANG DIHAPUS (Evidence Reference)

Berdasarkan screenshot dari antarmuka pengguna:

### Bukti 1: Navigasi Menu Sidebar yang Dihilangkan
![Sidebar Menu Peserta](file:///C:/Users/galan/.gemini/antigravity/brain/7c4386b3-6ece-42d6-a673-d843562e14c2/.user_uploaded/media_1794196741881_07b04ea6.png)
- **Elemen**: Sub-item menu **`Monitoring Peserta`** di dalam accordion menu dropdown **`Peserta`**.
- **Tindakan**: Hapus sub-item `Monitoring Peserta`. Menu dropdown `Peserta` kini hanya memuat `Arsip Peserta` (atau dipertahankan rapi sesuai panduan Tugas 4).

### Bukti 2: Halaman Utama Monitoring yang Dieliminasi
![Halaman Monitoring Pelaksanaan Magang Aktif](file:///C:/Users/galan/.gemini/antigravity/brain/7c4386b3-6ece-42d6-a673-d843562e14c2/.user_uploaded/media_1794196748428_271587f8.jpg)
- **Judul Halaman**: `Monitoring Pelaksanaan Magang Aktif`
- **Subjudul**: *"Pantau seluruh aktivitas penugasan, logbook harian, dan proses bimbingan peserta aktif secara baca-saja (PRD 3.3)."*
- **Breadcrumb**: `Beranda / SuperAdmin / Monitoring Peserta`
- **Fitur Halaman**:
  - Filter pencarian teks nama/email/NIM/kampus
  - Filter dropdown Semua Pembimbing & Semua Jurusan
  - Tabel Daftar Peserta Magang Aktif (Nama Peserta, Institusi & Jurusan, Pembimbing & Bidang, Jadwal Bimbingan, Jalur, dan Tombol `Pantau Detail >`)
  - Halaman detail monitoring baca-saja: `/superadmin/monitoring-peserta/{penempatan}`

---

## 🏗️ 3. 🗺️ INVENTARIS FILE & PEMETAAN DAMPAK (Impact Matrix)

Perubahan ini mencakup 7 komponen di repositori MAGKOM:

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 1. ROUTING (routes/web.php)                                                            │
│    - Hapus import: use App\Http\Controllers\SuperAdmin\MonitoringPesertaController;    │
│    - Hapus rute: superadmin.monitoring.index (GET /superadmin/monitoring-peserta)      │
│    - Hapus rute: superadmin.monitoring.show (GET /superadmin/monitoring-peserta/{pen}) │
│                                                                                        │
│ 2. CONTROLLER (app/Http/Controllers/SuperAdmin/)                                       │
│    - DELETE: app/Http/Controllers/SuperAdmin/MonitoringPesertaController.php           │
│                                                                                        │
│ 3. VIEWS BLADE (resources/views/superadmin/monitoring/)                                │
│    - DELETE: resources/views/superadmin/monitoring/index.blade.php                     │
│    - DELETE: resources/views/superadmin/monitoring/show.blade.php                      │
│    - DELETE DIRECTORY: resources/views/superadmin/monitoring/                          │
│                                                                                        │
│ 4. SIDEBAR NAVIGATION (resources/views/layouts/dashboard.blade.php)                    │
│    - Hapus link "Monitoring Peserta" pada sub-menu dropdown "Peserta" (L3329)          │
│    - Sesuaikan variabel $isOperasionalPesertaActive (L3309)                            │
│                                                                                        │
│ 5. SUPERADMIN DASHBOARD (resources/views/superadmin/)                                  │
│    - dashboard.blade.php (L187): Alihkan link waiting list ke admin.waiting-list.index  │
│    - _tab-bimbingan.blade.php (L117): Hapus tombol sa-link "Monitoring Peserta →"      │
│    - _tab-bimbingan.blade.php (L219): Alihkan :href panel pelacak ke admin.penempatan  │
│                                                                                        │
│ 6. TEST SUITES (tests/Feature/)                                                        │
│    - AdminSuperAdminPhaseTwoTest.php: Hapus pengujian akses monitoring baca-saja,      │
│      gantikan dengan assertion status 404 pada rute legacy                             │
│    - SuperAdminPortalVisualTest.php: Hapus test_superadmin_can_access_monitoring_*     │
│                                                                                        │
│ 7. ROUTE DOCUMENTATION (docs/architecture/ROUTES.md)                                   │
│    - Hapus entri baris tabel /superadmin/monitoring-peserta                            │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 🛠️ 4. 📝 PANDUAN EKSEKUSI TAHAP DEMI TAHAP (Atomic Implementation Steps)

> [!IMPORTANT]
> Ikuti urutan langkah di bawah ini secara disiplin. Jangan melompati langkah agar tidak terjadi dependensi rute yang rusak (*undefined route name exception*).

---

### TUGAS 1: Pembersihan Rute di `routes/web.php`

**Lokasi Berkas:** [`routes/web.php`](file:///g:/MAGKOM/routes/web.php)

#### 1.1 Hapus Import Controller
Cari baris import pada baris ~53:
```php
// BEFORE (HAPUS BARIS INI):
use App\Http\Controllers\SuperAdmin\MonitoringPesertaController;
```

#### 1.2 Hapus Definisi Rute Monitoring
Cari blok rute pada baris ~293-295 di dalam grup `Route::middleware([...])->prefix('superadmin')->name('superadmin.')->group(function () {`:

```php
// BEFORE (HAPUS BLOK INI):
    // Monitoring Pelaksanaan Magang Aktif (PRD 3.3, AC-07-7, AC-08-5)
    Route::get('/monitoring-peserta', [MonitoringPesertaController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring-peserta/{penempatan}', [MonitoringPesertaController::class, 'show'])->name('monitoring.show');
```

```php
// AFTER (SETELAH DIHAPUS):
    // Bulan Magang & Slot Khusus
    Route::get('/bulan', [BulanController::class, 'index'])->name('bulan.index');
```

---

### TUGAS 2: Hapus Berkas Controller `MonitoringPesertaController.php`

**Lokasi Berkas:** [`app/Http/Controllers/SuperAdmin/MonitoringPesertaController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/MonitoringPesertaController.php)

Hapus berkas controller ini dari sistem.

**Perintah CLI (PowerShell):**
```powershell
Remove-Item -Path "g:\MAGKOM\app\Http\Controllers\SuperAdmin\MonitoringPesertaController.php" -Force
```

---

### TUGAS 3: Hapus Berkas dan Direktori Tampilan Blade

**Lokasi Direktori:** [`resources/views/superadmin/monitoring/`](file:///g:/MAGKOM/resources/views/superadmin/monitoring/)

Hapus seluruh isi direktori dan foldernya:
1. `resources/views/superadmin/monitoring/index.blade.php`
2. `resources/views/superadmin/monitoring/show.blade.php`

**Perintah CLI (PowerShell):**
```powershell
Remove-Item -Path "g:\MAGKOM\resources\views\superadmin\monitoring" -Recurse -Force
```

---

### TUGAS 4: Penataan Ulang Navigasi Sidebar di `resources/views/layouts/dashboard.blade.php`

**Lokasi Berkas:** [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php) (sekitar baris 3308–3332)

#### 4.1 Modifikasi Deteksi Rute Aktif
Ubah `$isOperasionalPesertaActive` agar tidak lagi mengecek `superadmin.monitoring.*`.

```blade
{{-- BEFORE --}}
@php
    $isOperasionalPesertaActive = request()->routeIs('superadmin.monitoring.*') || request()->routeIs('superadmin.arsip.*');
@endphp
```

```blade
{{-- AFTER --}}
@php
    $isOperasionalPesertaActive = request()->routeIs('superadmin.arsip.*');
@endphp
```

#### 4.2 Hapus Sub-item "Monitoring Peserta"
Hapus tautan rute `superadmin.monitoring.index` di dalam sub-menu:

```blade
{{-- BEFORE --}}
<div class="nav-sub-menu {{ $isOperasionalPesertaActive ? 'show' : '' }}">
    <a href="{{ route('superadmin.monitoring.index') }}" class="nav-sub-item {{ request()->routeIs('superadmin.monitoring.*') ? 'active' : '' }}">{{ __('Monitoring Peserta') }}</a>
    <a href="{{ route('superadmin.arsip.index') }}" class="nav-sub-item {{ request()->routeIs('superadmin.arsip.*') ? 'active' : '' }}">{{ __('Arsip Peserta') }}</a>
</div>
```

```blade
{{-- AFTER --}}
<div class="nav-sub-menu {{ $isOperasionalPesertaActive ? 'show' : '' }}">
    <a href="{{ route('superadmin.arsip.index') }}" class="nav-sub-item {{ request()->routeIs('superadmin.arsip.*') ? 'active' : '' }}">{{ __('Arsip Peserta') }}</a>
</div>
```

> [!NOTE]
> Struktur kontainer grup `<div class="nav-group-container">` dengan tombol accordion dropdown "Peserta" tetap dipertahankan dengan rapi agar konsisten dengan gaya navigasi sidebar MODUL OPERASIONAL dan tidak merusak skrip `toggleNavGroup(this)`.

---

### TUGAS 5: Pembersihan & Pengalihan Tautan di Dashboard SuperAdmin

#### 5.1 File: `resources/views/superadmin/dashboard.blade.php` (Baris ~186–196)
Di panel agenda todo sidebar "Agenda & Status Perhatian", item waiting list sebelumnya keliru mengarah ke `route('superadmin.monitoring.index')`. Alihkan ke rute antrean seleksi/waiting list resmi `route('admin.waiting-list.index')` dan perbarui labelnya:

```blade
{{-- BEFORE --}}
                <li>
                    <a href="{{ route('superadmin.monitoring.index') }}">
                        <span class="cv-todo-dot {{ $totalWaitingList > 0 ? 'info' : 'ok' }}"></span>
                        <span class="cv-todo-text">
                            <b>{{ $totalWaitingList }} peserta di waiting list</b>
                            <small>Monitoring Peserta</small>
                        </span>
                        <span class="cv-todo-go">Buka &rarr;</span>
                    </a>
                </li>
```

```blade
{{-- AFTER --}}
                <li>
                    <a href="{{ route('admin.waiting-list.index') }}">
                        <span class="cv-todo-dot {{ $totalWaitingList > 0 ? 'info' : 'ok' }}"></span>
                        <span class="cv-todo-text">
                            <b>{{ $totalWaitingList }} peserta di waiting list</b>
                            <small>Antrean Waiting List</small>
                        </span>
                        <span class="cv-todo-go">Buka &rarr;</span>
                    </a>
                </li>
```

#### 5.2 File: `resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php`

**A. Hapus Quick Action Button "Monitoring Peserta" (Baris ~117–120):**

```blade
{{-- BEFORE --}}
            {{-- Quick Navigation Actions --}}
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <a href="{{ route('admin.penempatan.index') }}" class="sa-link">
                    <x-sa.icon name="user-plus" :size="14" />
                    <span>Kelola Penempatan Peserta &rarr;</span>
                </a>
                <a href="{{ route('superadmin.monitoring.index') }}" class="sa-link">
                    <x-sa.icon name="activity" :size="14" />
                    <span>Monitoring Peserta &rarr;</span>
                </a>
            </div>
```

```blade
{{-- AFTER --}}
            {{-- Quick Navigation Actions --}}
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <a href="{{ route('admin.penempatan.index') }}" class="sa-link">
                    <x-sa.icon name="user-plus" :size="14" />
                    <span>Kelola Penempatan Peserta &rarr;</span>
                </a>
            </div>
```

**B. Perbarui Header Panel Pelacak Penyelesaian Magang Terdekat (Baris ~219):**
Alihkan tautan `:href` pada panel ini ke modul penempatan aktif resmi `route('admin.penempatan.index')`:

```blade
{{-- BEFORE --}}
<x-sa.panel title="Pelacak Penyelesaian Magang Terdekat<span class='sr-only'> Pelacak Penyelesaian Magang Terdekat (Off-boarding Tracker)</span>" :href="route('superadmin.monitoring.index')" linkText="Lihat Semua Peserta Aktif &rarr;">
```

```blade
{{-- AFTER --}}
<x-sa.panel title="Pelacak Penyelesaian Magang Terdekat<span class='sr-only'> Pelacak Penyelesaian Magang Terdekat (Off-boarding Tracker)</span>" :href="route('admin.penempatan.index')" linkText="Kelola Penempatan &rarr;">
```

---

### TUGAS 6: Sinkronisasi dan Pemutakhiran Test Suite PHPUnit / Pest

#### 6.1 Berkas: `tests/Feature/AdminSuperAdminPhaseTwoTest.php`
Pada berkas [`tests/Feature/AdminSuperAdminPhaseTwoTest.php`](file:///g:/MAGKOM/tests/Feature/AdminSuperAdminPhaseTwoTest.php), ubah test method `superadmin_can_view_monitoring_peserta_aktif_index_and_detail_read_only()` (baris ~166–192) menjadi pengujian bahwa rute monitoring peserta telah resmi didekomisi dan mengembalikan respon 404 (Not Found):

```php
// BEFORE:
    /** @test */
    public function superadmin_can_view_monitoring_peserta_aktif_index_and_detail_read_only(): void
    {
        // 1. Tambahkan tugas untuk peserta
        Tugas::create([
            'penempatan_id' => $this->penempatan->id,
            'judul' => 'Instalasi Jaringan Server Diskominfo',
            'deskripsi' => 'Konfigurasi switch VLAN dan gateway',
            'kategori' => 'Infrastruktur',
            'kompleksitas' => KompleksitasEnum::Sedang,
            'deadline' => now()->addDays(5)->toDateString(),
            'status' => StatusTugasEnum::Proses,
        ]);

        // 2. Akses index monitoring
        $resIndex = $this->actingAs($this->superAdmin)->get(route('superadmin.monitoring.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertViewIs('superadmin.monitoring.index');
        $resIndex->assertSee('Budi Santoso');
        $resIndex->assertSee('SMK Negeri 2 Depok Sleman');

        // 3. Akses detail monitoring baca-saja
        $resShow = $this->actingAs($this->superAdmin)->get(route('superadmin.monitoring.show', $this->penempatan));
        $resShow->assertStatus(200);
        $resShow->assertViewIs('superadmin.monitoring.show');
        $resShow->assertSee('Mode Pemantauan Baca-Saja (SuperAdmin)');
        $resShow->assertSee('Instalasi Jaringan Server Diskominfo');
    }
```

```php
// AFTER:
    /** @test */
    public function superadmin_monitoring_peserta_routes_are_decommissioned(): void
    {
        // Memastikan rute legacy /superadmin/monitoring-peserta merespons 404 Not Found
        $resIndex = $this->actingAs($this->superAdmin)->get('/superadmin/monitoring-peserta');
        $resIndex->assertStatus(404);

        $resShow = $this->actingAs($this->superAdmin)->get('/superadmin/monitoring-peserta/' . $this->penempatan->id);
        $resShow->assertStatus(404);
    }
```

#### 6.2 Berkas: `tests/Feature/SuperAdminPortalVisualTest.php`
Pada berkas [`tests/Feature/SuperAdminPortalVisualTest.php`](file:///g:/MAGKOM/tests/Feature/SuperAdminPortalVisualTest.php), perbarui test method `test_superadmin_can_access_monitoring_peserta_lintas_bidang()` (baris ~239–245):

```php
// BEFORE:
    public function test_superadmin_can_access_monitoring_peserta_lintas_bidang(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.monitoring.index'));

        $response->assertStatus(200);
        $response->assertSee('Monitoring Peserta');
    }
```

```php
// AFTER:
    public function test_superadmin_monitoring_peserta_route_is_removed(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/superadmin/monitoring-peserta');

        $response->assertStatus(404);
    }
```

---

### TUGAS 7: Pembaruan Dokumentasi Arsitektur Rute

**Lokasi Berkas:** [`docs/architecture/ROUTES.md`](file:///g:/MAGKOM/docs/architecture/ROUTES.md)

Hapus entri rute `/superadmin/monitoring-peserta` pada baris ~191:
```markdown
<!-- BEFORE (HAPUS BARIS INI): -->
| `GET` | `/superadmin/monitoring-peserta`| `SuperAdmin\MonitoringPesertaController@index`| `layouts.dashboard`| Realtime placement tracking |
```

---

## ⚡ 5. 🧪 QUALITY GATE & PERINTAH VERIFIKASI (Verification Suite)

> [!IMPORTANT]
> Proyek MAGKOM menggunakan **PHP 8.3** (bukan PHP 8.2 default sistem). Jalankan semua perintah pengujian dengan path PHP 8.3 yang telah terpasang:
> `& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe"`

Jalankan rangkaian perintah berikut di terminal PowerShell:

### 1. Bersihkan Cache Route & Template Blade
```powershell
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan route:clear
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan view:clear
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan config:clear
```

### 2. Jalankan Test Suite Terkait
```powershell
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test tests/Feature/AdminSuperAdminPhaseTwoTest.php tests/Feature/SuperAdminPortalVisualTest.php
```
*Ekspektasi: 17 tests passed (89 assertions), durasi < 3s.*

### 3. Jalankan Pengujian Seluruh Fitur SuperAdmin
```powershell
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test tests/Feature/SuperAdmin*
```
*Ekspektasi: Semua test suite SuperAdmin passed 100%.*

### 4. Audit Kode dengan Pint (Code Formatting)
```powershell
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/pint --test
```
*Ekspektasi: 0 files formatting violations.*

### 5. Static Analysis dengan PHPStan
```powershell
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" vendor/bin/phpstan analyse --memory-limit=1G
```
*Ekspektasi: [OK] No errors found.*

---

## ✅ 6. 📋 CHECKLIST PENYELESAIAN (Definition of Done)

Junior Developer / AI Agent wajib memastikan seluruh kotak di bawah ini tercentang sebelum melaporkan tugas selesai:

- [x] Import `MonitoringPesertaController` dihapus dari `routes/web.php`.
- [x] Rute `superadmin.monitoring.index` dan `superadmin.monitoring.show` dihapus dari `routes/web.php`.
- [x] Berkas `app/Http/Controllers/SuperAdmin/MonitoringPesertaController.php` berhasil dihapus.
- [x] Direktori `resources/views/superadmin/monitoring/` beserta isinya (`index.blade.php` dan `show.blade.php`) berhasil dihapus.
- [x] Menu sidebar "Monitoring Peserta" pada `resources/views/layouts/dashboard.blade.php` telah dibersihkan.
- [x] Tautan to-do waiting list pada `resources/views/superadmin/dashboard.blade.php` telah dialihkan ke `admin.waiting-list.index`.
- [x] Tombol cepat "Monitoring Peserta &rarr;" pada `resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php` telah dihapus.
- [x] Header panel pelacak akhir magang pada `_tab-bimbingan.blade.php` telah dialihkan ke `admin.penempatan.index`.
- [x] Seluruh unit & feature tests yang terdampak (`AdminSuperAdminPhaseTwoTest` & `SuperAdminPortalVisualTest`) lulus pengujian dengan status hijau.
- [x] Tidak ada referensi rute usang atau *broken link* yang tersisa di seluruh views dan controllers.
- [x] Dokumentasi `docs/architecture/ROUTES.md` telah disinkronkan.

---

> **Catatan Arsitek:**  
> Task ini dirancang agar bersih, bebas efek samping (*zero side-effects*), serta menjaga integritas peran SuperAdmin dan alur operasional magang Dinas Kominfo Sleman. Jalankan implementasi secara tenang, teliti, dan terstruktur!

