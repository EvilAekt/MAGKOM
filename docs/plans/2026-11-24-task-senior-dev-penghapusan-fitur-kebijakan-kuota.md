# TASK & PRD PLAN: PENGHAPUSAN FITUR & MENU KEBIJAKAN KUOTA PADA ADMINISTRASI MAGKOM

> **Dari:** Senior Software Architect / Lead Developer  
> **Kepada:** Junior Developer / AI Implementation Engineer (Gemini 3.8 Flash Compatible)  
> **Target Repositori:** `MAGKOM` (Portal Manajemen Magang Diskominfo Kabupaten Sleman)  
> **Status:** IMPLEMENTED / SELESAI  
> **Tingkat Prioritas:** Medium (Code Cleanup, UI Simplification, Dead Feature Removal)  
> **Tanggal Dokumen:** 24 November 2026  
> **Referensi Gambar Permintaan:** Screenshot menu sidebar navigasi `[Sliders Icon] Kebijakan Kuota`

---

## 1. 🎯 RINGKASAN EKSEKUTIF & LATAR BELAKANG

### 1.1 Latar Belakang Masalah (Problem Statement)
Pada sidebar navigasi Administrator (`admin`), saat ini terdapat menu **"Kebijakan Kuota"** (`/admin/kebijakan`). Menu ini menampilkan halaman form untuk mengubah 6 parameter statis sistem:
1. `kuota_umum_maks_per_bulan`
2. `slot_khusus_default`
3. `maks_peserta_aktif_per_pembimbing`
4. `maks_anggota_kelompok`
5. `durasi_magang_maks_bulan`
6. `sla_verifikasi_hari`

Fitur ini **tidak lagi diperlukan** karena:
1. **Redundan**: Pengelolaan kuota magang yang sebenarnya sudah terpusat dan berjalan dinamis pada menu **"Manajemen Kuota"** (`/admin/kuota`) yang mengatur periode bulan magang, kuota per jurusan, sisa slot, dan status buka/tutup pendaftaran.
2. **Konfigurasi Statis**: Nilai default dari ke-6 parameter di atas telah diatur secara permanen melalui *seeder* dan sistem *fallback default* pada helper `Pengaturan::int(...)`. Staf dinas tidak perlu lagi mengubah parameter ini secara berkala melalui antarmuka visual.
3. **Penyederhanaan UI (Less Clutter)**: Menghapus menu ini akan merampingkan navigasi sidebar Administrasi, sehingga fokus staf langsung tertuju pada alur operasional utama: Verifikasi Pendaftaran, Penempatan, Bimbingan, dan Arsip Peserta.

### 1.2 Sasaran Akhir (Target State)
1. **Menu Dihapus Total**: Elemen menu **"Kebijakan Kuota"** di sidebar dashboard admin ([resources/views/layouts/dashboard.blade.php](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php)) dihilangkan secara bersih.
2. **Rute Dinonaktifkan**: Rute `GET /admin/kebijakan` dan `PUT /admin/kebijakan` di [routes/web.php](file:///g:/MAGKOM/routes/web.php) dihapus, sehingga rute tersebut menghasilkan respon `404 Not Found`.
3. **File Kode Dihapus**: Controller [KebijakanMagangController.php](file:///g:/MAGKOM/app/Http/Controllers/Admin/KebijakanMagangController.php), Form Request [UpdateKebijakanMagangRequest.php](file:///g:/MAGKOM/app/Http/Requests/UpdateKebijakanMagangRequest.php), dan view [resources/views/admin/kebijakan/index.blade.php](file:///g:/MAGKOM/resources/views/admin/kebijakan/index.blade.php) dihapus permanen.
4. **Database & Helper Tetap Aman**: Tabel `pengaturan` **tidak di-drop** dan nilai parameter yang ada di database dibiarkan tetap ada sebagai konstanta sistem, sehingga *backend logic* yang membaca parameter ini (misal di `SuperAdminExecutiveMetricsService` atau `PembimbingDashboardMetricsService`) tetap berjalan normal tanpa error.
5. **Test Suite Diperbarui**: Seluruh tes yang menguji rute `admin.kebijakan` disesuaikan atau dibersihkan sehingga test suite tetap berstatus hijau (100% pass).

---

## 2. 🛡️ PRINSIP ARSITEKTUR & KEAMANAN (GUARDRAILS)

1. **Non-Destructive Database Operation**:
   - Dilarang membuat migrasi drop table atau drop column pada tabel `pengaturan`.
   - Data konfigurasi di database tetap berfungsi sebagai konstanta aplikasi.
2. **Minimal & Focused Change**:
   - Hanya sentuh berkas yang berhubungan langsung dengan fitur Kebijakan Kuota. Jangan merombak layout menu lain (seperti Arsip Peserta, Jalur Khusus, atau Profil).
3. **Strict Route Contract**:
   - Setelah dihapus, jika ada request ke `/admin/kebijakan`, sistem harus merespons dengan `404 Not Found`, bukan `500 Internal Server Error`.
4. **Vite & Frontend Pipeline**:
   - Halaman kebijakan kuota sebelumnya menggunakan stylesheet `@vite(['resources/css/pages/superadmin/pengaturan.css'])`. File CSS ini digunakan bersama oleh halaman pengaturan SuperAdmin, sehingga **file CSS tidak boleh dihapus**.

---

## 3. 📁 INVENTARISASI & MATRIKS PERUBAHAN BERKAS

| No | Lokasi Berkas | Aksi | Alasan & Ringkasan Perubahan |
| :--- | :--- | :---: | :--- |
| 1 | `resources/views/layouts/dashboard.blade.php` | **Modify** | Hapus markup link navigasi menu `admin.kebijakan.index` (ikon sliders dan label "Kebijakan Kuota"). |
| 2 | `routes/web.php` | **Modify** | Hapus import `KebijakanMagangController` dan rute `kebijakan.index` & `kebijakan.update`. |
| 3 | `app/Http/Controllers/Admin/KebijakanMagangController.php` | **Delete** | File controller tidak lagi digunakan. |
| 4 | `app/Http/Requests/UpdateKebijakanMagangRequest.php` | **Delete** | Form request validasi kebijakan tidak lagi digunakan. |
| 5 | `resources/views/admin/kebijakan/index.blade.php` | **Delete** | Template Blade formulir kebijakan kuota dihapus beserta direktorinya. |
| 6 | `tests/Feature/AdminKebijakanMagangTest.php` | **Modify / Delete** | Hapus atau alihkan untuk memverifikasi bahwa rute `/admin/kebijakan` kini mengembalikan `404 Not Found`. |
| 7 | `tests/Feature/AdminLaporanDanArsipTest.php` | **Modify** | Hapus blok pemanggilan `put(route('admin.kebijakan.update'))` pada baris 306-317. |
| 8 | `tests/Feature/BackendSupplementalFeaturesTest.php` | **Modify** | Hapus blok pemanggilan `put(route('admin.kebijakan.update'))` pada baris 177-188. |
| 9 | `tests/Feature/SuperAdminFeatureToggleTest.php` | **Modify** | Hapus method test `test_admin_can_update_mentee_limit_and_operational_parameters` yang menguji rute yang telah dihapus. |
| 10 | `tests/Feature/DashboardLayoutTest.php` | **Modify** | Pastikan assertion navigasi admin memastikan rute `admin.kebijakan.index` tidak lagi dirender. |

---

## 4. 📝 PANDUAN LANGKAH-DEMI-LANGKAH UNTUK DEVELOPER / AI (STEP-BY-STEP)

### Langkah 1: Hapus Menu dari Sidebar Dashboard
**File:** [resources/views/layouts/dashboard.blade.php](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php)  
**Target Lokasi:** Sekitar baris 390 - 398.

**Teks yang Harus Dihapus:**
```blade
                        <a href="{{ route('admin.kebijakan.index') }}" class="nav-item-link {{ request()->routeIs('admin.kebijakan.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 21v-7m0-4V3m8 18v-9m0-4V3m8 18v-5m0-4V3M1 14h6m2-6h6m2 8h6"></path>
                                </svg>
                            </span>
                            <span>{{ __('Kebijakan Kuota') }}</span>
                            <span class="sr-only">{{ __('Kebijakan Kuota & Kapasitas Magang') }}</span>
                        </a>
```

*Verifikasi:* Pastikan transisi antar menu di sidebar tetap rapi dan tidak ada tag penutup `</a>` yang terpotong atau rusak.

---

### Langkah 2: Bersihkan Rute pada routes/web.php
**File:** [routes/web.php](file:///g:/MAGKOM/routes/web.php)

1. **Hapus Import Controller:**
   Cari baris import di bagian atas:
   ```php
   use App\Http\Controllers\Admin\KebijakanMagangController;
   ```
   Hapus baris tersebut.

2. **Hapus Definisi Rute:**
   Cari blok rute kebijakan kuota di dalam grup middleware `auth`, `verified`, `role:admin`:
   ```php
           // Parameter Kebijakan Kuota & Kapasitas Magang
           Route::get('/kebijakan', [KebijakanMagangController::class, 'index'])->name('kebijakan.index');
           Route::put('/kebijakan', [KebijakanMagangController::class, 'update'])->name('kebijakan.update');
   ```
   Hapus kedua rute tersebut secara bersih.

*Verifikasi:* Jalankan perintah `php artisan route:list | grep kebijakan` dan pastikan rute `admin.kebijakan.*` sudah tidak terdaftar lagi.

---

### Langkah 3: Hapus Controller, Form Request, dan View Template
Jalankan penghapusan berkas-berkas berikut:

1. Hapus Controller:
   ```bash
   Remove-Item app/Http/Controllers/Admin/KebijakanMagangController.php
   ```
2. Hapus Form Request:
   ```bash
   Remove-Item app/Http/Requests/UpdateKebijakanMagangRequest.php
   ```
3. Hapus View Template dan Direktori:
   ```bash
   Remove-Item -Recurse resources/views/admin/kebijakan
   ```

*Verifikasi:* Pastikan tidak ada berkas yang tertinggal pada direktori `resources/views/admin/kebijakan/`.

---

### Langkah 4: Sesuaikan Test Suite
Karena rute `admin.kebijakan.index` dan `admin.kebijakan.update` telah dihapus, lakukan pembaruan pada berkas pengujian:

1. **`tests/Feature/AdminKebijakanMagangTest.php`**:
   Ubah isi tes ini untuk memastikan rute yang dihapus kini merespons `404 Not Found` dan link tidak lagi muncul di sidebar:
   ```php
   public function test_kebijakan_page_is_no_longer_accessible(): void
   {
       $response = $this->actingAs($this->admin)->get('/admin/kebijakan');
       $response->assertStatus(404);
   }

   public function test_dashboard_sidebar_does_not_contain_kebijakan_link(): void
   {
       $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
       $response->assertDontSee('Kebijakan Kuota');
       $response->assertDontSee('/admin/kebijakan');
   }
   ```

2. **`tests/Feature/AdminLaporanDanArsipTest.php`**:
   Hapus pemanggilan `route('admin.kebijakan.update')` pada baris 306-317 yang sebelumnya menguji update parameter kuota.

3. **`tests/Feature/BackendSupplementalFeaturesTest.php`**:
   Hapus pemanggilan `route('admin.kebijakan.update')` pada baris 177-188.

4. **`tests/Feature/SuperAdminFeatureToggleTest.php`**:
   Hapus method `test_admin_can_update_mentee_limit_and_operational_parameters` yang memanggil `route('admin.kebijakan.update')`.

---

### Langkah 5: Verifikasi Quality Gate
Jalankan pengujian dan kompilasi untuk memastikan tidak ada efek samping:
```bash
# 1. Linting PHP
php -l routes/web.php
php -l resources/views/layouts/dashboard.blade.php

# 2. Jalankan Seluruh Test Suite Terkait
./vendor/bin/phpunit tests/Feature/AdminKebijakanMagangTest.php
./vendor/bin/phpunit tests/Feature/AdminLaporanDanArsipTest.php
./vendor/bin/phpunit tests/Feature/BackendSupplementalFeaturesTest.php
./vendor/bin/phpunit tests/Feature/SuperAdminFeatureToggleTest.php
./vendor/bin/phpunit tests/Feature/DashboardLayoutTest.php

# 3. Kompilasi Aset Frontend
npm run build
```

---

## 5. 🔍 STRATEGI PENGUJIAN & REGRESI (TESTING PLAN)

1. **Uji Akses Rute (Negative Assertion)**:
   - Akses rute `/admin/kebijakan` oleh role `admin`, `pembimbing`, `peserta`, maupun `guest` wajib menghasilkan kode HTTP **404 Not Found**.
2. **Uji Navigasi Antarmuka (UI Inspection)**:
   - Login sebagai `admin@magkom.go.id`.
   - Buka halaman Dashboard Admin (`/admin/dashboard`).
   - Periksa menu sidebar: Pastikan item **"Kebijakan Kuota"** sudah tidak terlihat di bawah menu Arsip Peserta.
   - Pastikan menu lain seperti **Laporan & Ekspor**, **Arsip Peserta**, dan **Jalur Khusus** tetap berfungsi dan tidak tergeser posisinya.
3. **Uji Nilai Parameter Sistem (Zero Logic Breakdown)**:
   - Buka dashboard pembimbing atau jalankan `PembimbingDashboardMetricsServiceTest` untuk membuktikan bahwa pembacaan kapasitas mentee per pembimbing (`maks_peserta_aktif_per_pembimbing`) tetap berjalan mulus menggunakan nilai fallback default di database.

---

## 6. ⚠️ ANALISIS RISIKO & MITIGASI

| Potensi Risiko | Tingkat Dampak | Strategi Mitigasi |
| :--- | :---: | :--- |
| Test suite lain gagal karena mencari route name `admin.kebijakan.index` | Low | Telah diinventarisasi 3 file tes terkait dan disiapkan panduan modifikasi eksplisit di Langkah 4. |
| Service backend error karena mengira setting dihapus | Low | Data di tabel `pengaturan` tidak dihapus. Helper `Pengaturan::int(...)` tetap membaca nilai dari database/fallback default. |
| Struktur layout sidebar rusak saat menghapus tag HTML | Low | Kode target HTML telah dipetakan secara presisi (baris 390-398) dengan batas tag `<a>` yang jelas. |

---

## 7. ✅ DEFINISI SELESAI (DEFINITION OF DONE)

- [x] Menu `Kebijakan Kuota` tidak lagi muncul di sidebar dashboard admin.
- [x] Rute `admin.kebijakan.index` dan `admin.kebijakan.update` telah dihapus dari `routes/web.php`.
- [x] File controller `KebijakanMagangController.php` telah dihapus.
- [x] File form request `UpdateKebijakanMagangRequest.php` telah dihapus.
- [x] File view `resources/views/admin/kebijakan/index.blade.php` beserta foldernya telah dihapus.
- [x] Pengujian rute `/admin/kebijakan` mengembalikan status 404 (Not Found).
- [x] Seluruh unit & feature test yang relevan lulus (100% green).
- [x] Perintah `npm run build` berjalan sukses tanpa error.
- [x] Status git bersih dan terdokumentasi rapi.
