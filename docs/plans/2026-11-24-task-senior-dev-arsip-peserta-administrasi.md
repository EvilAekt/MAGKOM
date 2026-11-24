# TASK & IMPLEMENTATION PLAN: FITUR ARSIP PESERTA UNTUK ADMINISTRASI (ADMIN)

> **Dari:** Lead Software Architect / Senior Developer  
> **Kepada:** Junior Developer / AI Implementation Engineer (Gemini 3.8 Flash & Lightweight Model Compatible)  
> **Target Repositori:** `MAGKOM` (Portal Manajemen Magang Dinas Kominfo Kabupaten Sleman)  
> **Status:** READY FOR IMPLEMENTATION / MENUNGGU PERSETUJUAN PENGGUNA  
> **Tingkat Prioritas:** High (Paritas Fitur Operasional, Separation of Concerns, RBAC Compliance)  
> **Dokumen Terkait:** `docs/01-PRD-MagKom.md` (F-12 Gudang Arsip, AC-12-4, BR-07, BR-16), `docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`, `tests/Feature/AdminLaporanDanArsipTest.php`  
> **Tanggal Dokumen:** 24 November 2026  

---

## 1. 🎯 RINGKASAN EKSEKUTIF & LATAR BELAKANG

### 1.1 Masalah Saat Ini (Current State)
Saat ini pada repositori MagKom:
1. Fitur **Arsip Peserta Magang (Bank Data Alumni / Purna Tugas)** baru tersedia secara eksklusif untuk **SuperAdmin** (`/superadmin/arsip-peserta`, rute `superadmin.arsip.*`).
2. Staf **Administrasi (`admin`)** sebagai garda terdepan pengelola operasional harian magang (Verifikasi Berkas, Kuota, Penempatan, dan Pelaporan) **belum memiliki menu dan rute mandiri** untuk mengakses riwayat arsip peserta purna magang (peserta dengan status penempatan `selesai`, `nonaktif`, atau `mengundurkan_diri`).
3. Ketika Admin perlu melacak riwayat alumni magang (misalnya untuk verifikasi penerbitan surat selesai magang, konfirmasi universitas/instansi asal, atau pengecekan riwayat penempatan masa lalu), Admin terhalang oleh pembatas `role:superadmin` (mengembalikan HTTP `403 Forbidden`).

### 1.2 Target Akhir yang Diinginkan (Target State)
1. **Fitur Arsip Peserta untuk Administrasi (`admin`)**:
   - Staf Administrasi memiliki fitur dan antarmuka yang setara (*feature parity*) dengan SuperAdmin, yaitu melihat daftar arsip (dengan filter pencarian, tahun, dan status), ringkasan 4 metrik kartu statistik Gentelella tile-stat, serta detail riwayat lengkap peserta (biodata, pembimbing, tugas, logbook, dokumen, dan nilai).
2. **Navigasi Sidebar Admin Sesuai UI Mockup**:
   - Menambahkan grup menu collapsible di sidebar Administrasi (`resources/views/layouts/dashboard.blade.php`):
     - **Parent Header**: `Peserta` (dengan ikon `users` dan chevron dropdown).
     - **Submenu**: `Arsip Peserta` (mengarah ke `route('admin.arsip.index')`).
3. **Penerapan Service Layer yang Bersih (Clean Architecture & DRY)**:
   - Mengekstraksi logika query filter, agregasi statistik, dan penarikan relasi arsip ke dalam sebuah service reusable: `App\Services\ArsipPesertaService`.
   - Menghindari duplikasi kode query SQL yang rawan drift antara modul Admin dan SuperAdmin.
4. **Isolasi RBAC & Audit Trail Sesuai Standar Sleman**:
   - Rute Admin dilindungi middleware `['auth', 'verified', 'must_change_password', 'account.active', 'role:admin,superadmin', 'superadmin.readonly', '2fa.enforced']` dan permission `laporan.kinerja-portofolio`.
   - Setiap pembukaan detail arsip oleh Admin wajib mencatat rekam jejak pada tabel `audit_logs` melalui `AuditService::catat()`.

---

## 2. 🏛️ EVALUASI OPSI ARSITEKTUR (ARCHITECTURAL DECISION)

Sebelum implementasi, Lead Developer telah menimbang 2 pendekatan arsitektur:

### Opsi A: Copy-Paste Controller & View ke Namespace Admin (Duplikasi Monolitik)
* **Kelebihan**: Cepat ditulis dalam jangka pendek.
* **Kekurangan**: Melanggar prinsip DRY (*Don't Repeat Yourself*). Jika ada perubahan kriteria status arsip (misal penambahan status baru atau formula agregasi statistik), pengembang harus mengedit 2 controller dan 2 view terpisah. Rawan terjadi *divergence* / *drift* logic.

### Opsi B: Service Layer Pattern (`ArsipPesertaService`) + Dedicated Admin Views & Controller (Rekomendasi Terbaik) ⭐
* **Kelebihan**:
  1. *Skinny Controller*: Controller Admin (`Admin\ArsipPesertaController`) hanya bertugas menangani HTTP request dan response view.
  2. *Single Source of Truth*: Logika query `Penempatan`, pagination (10 per page), sanitasi filter keyword, dan perhitungan 4 tile statistik dikelola terpusat di `ArsipPesertaService`.
  3. *Zero Style Regression*: View `admin.arsip.index` dan `admin.arsip.show` menggunakan token dan komponen Gentelella yang sudah ada, tanpa menulis CSS/JS inline di Blade.
  4. Kompatibilitas AI Ringan (Gemini 3.8 Flash): Struktur modular dan kontrak method yang ketat memudahkan implementasi tanpa halusinasi.
* **Keputusan**: **Memilih Opsi B**.

---

## 3. 🛡️ BENTENG KEAMANAN & INTEGRITAS DATA (SECURITY GUARDRAILS)

Semua agen / developer wajib mematuhi aturan baku berikut:
1. **PHP 8.3 Strict Types**: Setiap file PHP baru wajib diawali `declare(strict_types=1);` dan type hinting lengkap pada argumen serta return value.
2. **Laravel 10 Skinny Controller**: Jangan menaruh query Eloquent kompleks langsung di dalam controller. Delegasikan ke `ArsipPesertaService`.
3. **Blade Read-Only & Zero Inline CSS/JS**:
   - Dilarang keras menulis tag `<style>` atau tag `<script>` di dalam file Blade.
   - Dilarang menulis inline event handler (`onclick="..."`, `onchange="..."`, dsb.).
   - Styling menggunakan class Gentelella / MagKom yang sudah terkompilasi melalui Vite (`resources/css/pages/superadmin/superadmin-common.css`).
4. **Strictly Read-Only (Non-Destructive)**:
   - Fitur arsip adalah *bank data historis permanen*. Tidak boleh ada tombol hapus data penempatan, ubah status alumni, atau mutasi data tanpa otorisasi.
5. **Audit Trail Mandatori**:
   - Mengakses rute `admin.arsip.show` wajib memanggil `app(AuditService::class)->catat(...)` dengan tipe `TipeAuditEnum::AksesData`.
6. **SQL Injection Prevention**:
   - Pencarian query string `search` wajib menggunakan parameter binding Eloquent:
     `$query->where('nama', 'like', '%'.$search.'%')`.
   - Ekstraksi tahun disesuaikan terhadap driver basis data (MySQL `YEAR(tgl_selesai)` vs SQLite `strftime("%Y", tgl_selesai)`).

---

## 4. 📁 SPESIFIKASI BERKAS & STRUKTUR PERUBAHAN

| File | Status | Tanggung Jawab / Peran |
| :--- | :--- | :--- |
| `app/Services/ArsipPesertaService.php` | **[BARU]** | Service layer untuk query filter, pagination 10 data, 4 statistik arsip, dan eager loading relasi penempatan. |
| `app/Http/Controllers/Admin/ArsipPesertaController.php` | **[BARU]** | Controller Admin untuk aksi `index()` dan `show()`, orkestrasi service dan pencatatan audit trail. |
| `routes/web.php` | **[MODIFIKASI]** | Pendaftaran rute `admin.arsip.index` dan `admin.arsip.show` di bawah grup rute `/admin`. |
| `resources/views/layouts/dashboard.blade.php` | **[MODIFIKASI]** | Penambahan dropdown navigasi `Peserta` -> `Arsip Peserta` pada sidebar seksi Admin (`$userRole === 'admin'`). |
| `resources/views/admin/arsip/index.blade.php` | **[BARU]** | View Blade daftar arsip peserta untuk admin, kartu 4 statistik, form filter, dan tabel riwayat. |
| `resources/views/admin/arsip/show.blade.php` | **[BARU]** | View Blade detail rekam jejak arsip peserta untuk admin, tab tugas, logbook, dokumen, dan riwayat penilaian. |
| `tests/Feature/AdminArsipPesertaTest.php` | **[BARU]** | Test suite otomatis (Pest/PHPUnit) pengujian otorisasi, filtering, pagination 10 entri, dan audit trail. |

---

## 5. 🛠️ PANDUAN IMPLEMENTASI LANGKAH DEMI LANGKAH (STEP-BY-STEP TASK HANDOVER)

### Langkah 1: Buat Service Layer `App\Services\ArsipPesertaService`
**Lokasi File:** `app/Services/ArsipPesertaService.php`

**Spesifikasi Teknis:**
1. Namespace: `App\Services`.
2. Model yang digunakan: `App\Models\Penempatan`.
3. Enum yang digunakan: `App\Enums\StatusPenempatanEnum`.
4. Menyediakan 4 method publik:
   - `getArsipPaginated(Request $request, int $perPage = 10): LengthAwarePaginator`
     - Status yang dicakup: `StatusPenempatanEnum::Selesai`, `StatusPenempatanEnum::Nonaktif`, `StatusPenempatanEnum::MengundurkanDiri`.
     - Order: `tgl_selesai` descending.
     - Eager loading wajib: `['peserta.profil', 'pembimbing', 'bidang', 'kuota.jurusan', 'slotKhusus']`.
     - Filter `search`: Mencakup nama peserta, email peserta, institusi profil, dan NIM/NISN profil.
     - Filter `tahun`: `whereYear('tgl_selesai', $tahun)`.
     - Filter `status`: `where('status', $status)`.
     - Pagination: `paginate($perPage)->withQueryString()`.
   - `getStatistikArsip(): array`
     - Menghitung agregasi:
       - `total_arsip`: Total seluruh penempatan dengan status arsip.
       - `total_selesai`: Total status `selesai`.
       - `total_nonaktif`: Total status `nonaktif`.
       - `total_mengundurkan_diri`: Total status `mengundurkan_diri`.
   - `getTahunFilterList(): Collection`
     - Menghasilkan daftar tahun unik dari `tgl_selesai` yang tidak null, diurutkan descending.
     - Mendukung dialek MySQL (`YEAR(tgl_selesai)`) dan SQLite testing (`strftime("%Y", tgl_selesai)`).
   - `getDetailArsip(Penempatan $penempatan): Penempatan`
     - Memastikan status penempatan valid sebagai arsip (jika bukan arsip, lempar `ModelNotFoundException` atau `abort(404)`).
     - Eager loading relasi lengkap:
       `['peserta.profil.jurusan', 'pembimbing.bidang', 'bidang', 'kuota.jurusan', 'slotKhusus', 'tugas.progresLogs', 'logbooks', 'pendaftaran.dokumen.jenisDokumen']`.

---

### Langkah 2: Buat Controller `App\Http\Controllers\Admin\ArsipPesertaController`
**Lokasi File:** `app/Http/Controllers/Admin/ArsipPesertaController.php`

**Spesifikasi Teknis:**
1. Namespace: `App\Http\Controllers\Admin`.
2. Menggunakan `App\Http\Controllers\Controller`.
3. Dependency Injection: Inject `ArsipPesertaService` dan `AuditService` via constructor atau method parameter.
4. Method `index(Request $request): View`:
   - Ambil data arsip terpaginasi dari service: `$arsipList = $this->arsipService->getArsipPaginated($request, 10);`.
   - Ambil statistik arsip: `$stats = $this->arsipService->getStatistikArsip();`.
   - Ambil list tahun filter: `$tahunList = $this->arsipService->getTahunFilterList();`.
   - Return view `admin.arsip.index` dengan variabel:
     - `arsipList`
     - `tahunList`
     - `totalArsip` (`$stats['total_arsip']`)
     - `totalSelesai` (`$stats['total_selesai']`)
     - `totalNonaktif` (`$stats['total_nonaktif']`)
     - `totalMengundurkanDiri` (`$stats['total_mengundurkan_diri']`)
5. Method `show(Penempatan $penempatan): View`:
   - Validasi bahwa `$penempatan->status` adalah salah satu dari status arsip (`selesai`, `nonaktif`, `mengundurkan_diri`). Jika bukan, `abort(404, 'Data penempatan bukan merupakan arsip.');`.
   - Muat relasi melalui `$penempatan = $this->arsipService->getDetailArsip($penempatan);`.
   - Catat audit trail:
     ```php
     app(AuditService::class)->catat(
         tipe: TipeAuditEnum::AksesData,
         tabel: 'penempatan',
         idData: $penempatan->id,
         nilaiLama: null,
         nilaiBaru: null,
         detail: [
             'aksi' => 'Melihat arsip historis permanen peserta magang (Administrasi)',
             'peserta_id' => $penempatan->peserta_id,
         ],
         user: request()->user()?->id
     );
     ```
   - Return view `admin.arsip.show` dengan parameter `['penempatan' => $penempatan]`.

---

### Langkah 3: Daftarkan Rute di `routes/web.php`
**Lokasi File:** `routes/web.php`

**Spesifikasi Penempatan:**
Di dalam grup rute Administrator (`prefix('admin')->name('admin.')`):
```php
// Modul Arsip Peserta Magang (F-12 AC-12-4, PRD 9.2)
Route::middleware('permission:laporan.kinerja-portofolio')->group(function () {
    Route::get('/arsip-peserta', [\App\Http\Controllers\Admin\ArsipPesertaController::class, 'index'])->name('arsip.index');
    Route::get('/arsip-peserta/{penempatan}', [\App\Http\Controllers\Admin\ArsipPesertaController::class, 'show'])->name('arsip.show');
});
```
*Catatan Penting:*
Nama rute yang dihasilkan adalah `admin.arsip.index` dan `admin.arsip.show`.
Rute SuperAdmin yang sudah ada (`superadmin.arsip.*`) **TETAP DIPERTAHANKAN** dan tidak boleh diubah agar tidak merusak *backward compatibility* dan *regression suite*.

---

### Langkah 4: Tambahkan Grup Menu di Sidebar Admin (`dashboard.blade.php`)
**Lokasi File:** `resources/views/layouts/dashboard.blade.php`

**Spesifikasi Markup:**
Cari blok `@elseif($userRole === 'admin')` (sekitar baris 315-380).
Letakkan grup navigasi dropdown `Peserta` -> `Arsip Peserta` setelah tautan `Penempatan Peserta` atau `Laporan & Ekspor`.

Gunakan struktur standar Gentelella accordion berikut:
```blade
@php
    $isAdminPesertaActive = request()->routeIs('admin.arsip.*');
@endphp
<div class="nav-group-container">
    <button type="button" class="nav-group-btn {{ $isAdminPesertaActive ? 'has-active' : '' }}" data-action="toggle-nav-group" aria-expanded="{{ $isAdminPesertaActive ? 'true' : 'false' }}">
        <span class="nav-group-btn-left">
            <span class="nav-item-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </span>
            <span>{{ __('Peserta') }}</span>
        </span>
        <svg class="nav-group-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="9 18 15 12 9 6"></polyline>
        </svg>
    </button>
    <div class="nav-sub-menu {{ $isAdminPesertaActive ? 'show' : '' }}">
        <a href="{{ route('admin.arsip.index') }}" class="nav-sub-item {{ request()->routeIs('admin.arsip.*') ? 'active' : '' }}">{{ __('Arsip Peserta') }}</a>
    </div>
</div>
```
*Kesesuaian Tampilan:*
Markup ini menghasilkan visual yang **100% identik** dengan gambar yang diunggah pengguna (ikon dua orang `Peserta` dengan panah chevron dropdown, dan submenu `Arsip Peserta` di dalamnya).

---

### Langkah 5: Buat View Blade Admin `admin.arsip.index` & `admin.arsip.show`
**Lokasi Direktori:** `resources/views/admin/arsip/`

#### 5.1 `resources/views/admin/arsip/index.blade.php`:
- Extends: `layouts.dashboard`.
- Title: `__('Arsip Peserta Magang - Administrasi MagKom')`.
- Topbar title: `__('Arsip Peserta')`.
- Breadcrumbs:
  `Dashboard (route admin.dashboard) / Arsip Peserta`.
- Styles: `@push('styles') @vite(['resources/css/pages/superadmin/superadmin-common.css']) @endpush` (menggunakan styling token yang sudah ada).
- Scripts: `@push('scripts') @vite(['resources/js/pages/superadmin/staf-index.js']) @endpush`.
- Konten:
  1. **Hero Banner**: Banner Sleman MagKom dengan badge `Strictly Read-Only Bank Data` dan tombol aksi cepat menuju `route('admin.laporan.index', ['tab' => 'peserta'])`.
  2. **Grid 4 Kartu Statistik Tile-Stat**:
     - Total Arsip (`$totalArsip`)
     - Selesai Magang (`$totalSelesai`)
     - Akun Nonaktif (`$totalNonaktif`)
     - Mengundurkan Diri (`$totalMengundurkanDiri`)
  3. **Card Filter Pencarian**:
     - Input text keyword pencarian (`search`)
     - Select option status (`status`)
     - Select option tahun (`tahun` dari `$tahunList`)
     - Tombol Submit Filter dan Reset Filter (`route('admin.arsip.index')`)
  4. **Data Table**:
     - Kolom: Peserta & Institusi, Jurusan & Bidang, Jadwal Magang, Pembimbing Lapangan, Status Arsip, dan Aksi.
     - Tombol Aksi: Tautan menuju `route('admin.arsip.show', $item)`.
     - Pagination: `{{ $arsipList->links() }}` (10 entri per halaman).

#### 5.2 `resources/views/admin/arsip/show.blade.php`:
- Extends: `layouts.dashboard`.
- Title: `__('Detail Arsip Peserta :nama - Administrasi MagKom', ['nama' => $penempatan->peserta?->nama ?? __('Alumni')])`.
- Breadcrumbs:
  `Dashboard (admin.dashboard) / Arsip Peserta (admin.arsip.index) / {nama_peserta}`.
- Tombol Aksi Hero Banner:
  - Tombol Unduh Surat Diterima PDF: `route('admin.penempatan.surat-diterima', $penempatan)`.
  - Tombol Kembali: `route('admin.arsip.index')`.
- Segmented Tab Interaktif (Paritas SuperAdmin):
  - Tab 1: **Ringkasan Biodata & Pembimbing** (Info peserta, institusi, jadwal, pembimbing, dan berkas surat diterima).
  - Tab 2: **Riwayat Tugas Magang** (Daftar tugas yang dikerjakan peserta, status pengumpulan, tautan lampiran).
  - Tab 3: **Riwayat Logbook Harian** (Jurnal aktivitas harian dan status verifikasi pembimbing).
  - Tab 4: **Dokumen Persyaratan & Penilaian** (Berkas pendaftaran pendaftar & sertifikat/nilai).
- Scripts: `@push('scripts') @vite(['resources/js/pages/superadmin/arsip-show.js']) @endpush`.

---

### Langkah 6: Pembuatan Automated Feature Test `AdminArsipPesertaTest`
**Lokasi File:** `tests/Feature/AdminArsipPesertaTest.php`

**Skenario Pengujian Wajib:**
1. `test_admin_dapat_mengakses_halaman_index_arsip_peserta`:
   - Admin login -> `GET route('admin.arsip.index')`.
   - Assert: Status `200 OK`, view `admin.arsip.index`, melihat teks "Arsip Peserta".
2. `test_admin_dapat_mencari_dan_memfilter_arsip_peserta`:
   - Siapkan 3 penempatan dummy (status `selesai`, `nonaktif`, `mengundurkan_diri`).
   - Admin akses filter `?status=selesai` -> data yang tampil hanya peserta selesai.
   - Admin akses filter `?search=NamaPeserta` -> data yang tampil sesuai kata kunci.
3. `test_admin_dapat_melihat_detail_arsip_peserta_dan_mencatat_audit_log`:
   - Admin akses `GET route('admin.arsip.show', $penempatanSelesai)`.
   - Assert: Status `200 OK`, view `admin.arsip.show`, nama peserta & pembimbing tampil.
   - Assert Database: Tabel `audit_logs` memiliki entri dengan tipe `akses_data`, tabel `penempatan`, ID target penempatan, dan `user_id` admin.
4. `test_arsip_peserta_admin_membatasi_sepuluh_entri_per_halaman`:
   - Buat 15 penempatan purna magang.
   - Admin akses `GET route('admin.arsip.index')`.
   - Assert: `$arsipList` berisi tepat 10 item per halaman dan pagination links tersedia.
5. `test_role_peserta_dan_pembimbing_dilarang_mengakses_arsip_admin`:
   - Peserta akses `GET route('admin.arsip.index')` -> Assert `403 Forbidden`.
   - Pembimbing akses `GET route('admin.arsip.index')` -> Assert `403 Forbidden`.
6. `test_superadmin_arsip_tetap_berfungsi_dan_admin_tetap_dilarang_ke_rute_superadmin`:
   - Memastikan pengujian eksisting di `AdminLaporanDanArsipTest` tetap `PASS` (Admin dilarang ke `superadmin.arsip.index`, tapi SuperAdmin bisa akses keduanya).

---

## 6. 🚦 QUALITY GATES & PERINTAH VERIFIKASI (VERIFICATION COMMANDS)

Setelah penulisan kode selesai, developer / AI wajib menjalankan seluruh perintah verifikasi berikut secara berurutan:

```powershell
# 1. Jalankan test suite fitur arsip admin
php artisan test --filter=AdminArsipPesertaTest

# 2. Jalankan test regresi seluruh alur laporan dan arsip eksisting
php artisan test --filter=AdminLaporanDanArsipTest

# 3. Jalankan linter dan format kode sesuai standar PSR-12
./vendor/bin/pint --test

# 4. Jalankan static analysis level tinggi PHPStan / Larastan
./vendor/bin/phpstan analyse --memory-limit=1G

# 5. Pastikan aset frontend terkompilasi tanpa error
npm run build
```

---

## 7. ⚠️ ANALISIS RISIKO & MITIGASI (RISK ASSESSMENT)

| Potensi Risiko | Tingkat | Mitigasi Arsitektural |
| :--- | :---: | :--- |
| **N+1 Query pada Daftar Arsip** | Sedang | Service layer wajib menyertakan eager loading `with(['peserta.profil', 'pembimbing', 'bidang', 'kuota.jurusan', 'slotKhusus'])`. |
| **Penyusupan Akses Peserta Aktif ke Arsip** | Rendah | Query basis service secara ketat hanya menyaring status `selesai`, `nonaktif`, dan `mengundurkan_diri`. Peserta berstatus `aktif` atau `menunggu` tidak akan muncul di arsip. |
| **Regresi Tes Hak Akses SuperAdmin** | Rendah | Rute `superadmin.arsip.*` tidak diubah sama sekali. Rute `admin.arsip.*` ditambahkan sebagai rute independen di bawah prefix admin. |
| **Kebocoran Desain / Inline Styles di Blade** | Tinggi | Dilarang menyisipkan `<style>` di Blade. Semua kelas menggunakan token CSS yang sudah ada di `resources/css/pages/superadmin/superadmin-common.css`. |

---

## 8. ✅ DEFINISI SELESAI (DEFINITION OF DONE - DoD)

Fitur dianggap tuntas apabila checklist berikut terpenuhi:
- [ ] File `app/Services/ArsipPesertaService.php` dibuat dan lolos PHPStan level 5+.
- [ ] File `app/Http/Controllers/Admin/ArsipPesertaController.php` dibuat dengan skinny controller pattern.
- [ ] Rute `admin.arsip.index` dan `admin.arsip.show` terdaftar dan terlindungi middleware RBAC.
- [ ] Sidebar Administrasi di `resources/views/layouts/dashboard.blade.php` memiliki menu dropdown `Peserta` -> `Arsip Peserta` persis seperti tangkapan layar.
- [ ] View `admin.arsip.index` dan `admin.arsip.show` siap pakai dengan data table dan segmented tabs.
- [ ] Audit trail tercatat dengan benar pada saat Admin mengakses detail arsip.
- [ ] Test suite `tests/Feature/AdminArsipPesertaTest.php` dibuat dan 100% berstatus `PASS`.
- [ ] Test regresi `tests/Feature/AdminLaporanDanArsipTest.php` tetap 100% `PASS`.
- [ ] `npm run build` sukses tanpa warning bundler.
- [ ] Working tree Git tetap rapi dan tidak ada file sampah.
