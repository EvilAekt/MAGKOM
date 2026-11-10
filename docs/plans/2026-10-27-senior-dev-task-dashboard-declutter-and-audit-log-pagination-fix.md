# Task Senior Developer: Dashboard Declutter & Perbaikan Pagination Audit Log SuperAdmin

- **Dokumen Referensi**: PRD MagKom Revisi 5 / Design System DS-MagKom (Bab 2)
- **Tingkat Kesulitan**: Junior to Mid / Beginner AI Directives (Gemini 3.8 Flash Compatible)
- **Tanggal Rilis**: 27 Oktober 2026
- **Status**: Selesai & Terverifikasi (Completed & Verified)

---

## 1. Konteks & Problem Statement

Berdasarkan tinjauan langsung dari Product Owner / Pengguna:
1. **Visual Clutter pada Dashboard SuperAdmin**:
   - Terdapat baris **"PINTASAN TINDAKAN:"** (chip tombol `+ Akun Staf`, `+ Bulan Magang`, `Tinjau Berkas`, `Penempatan Peserta`, dll.) di bawah kartu periode.
   - Terdapat kartu widget samping **"Aksi cepat"** (tombol `+ Tambah staf`, `Buka periode`, `Backup sekarang`).
   - **Keluhan Pengguna**: Tampilan terasa terlalu ramai, redundant (menu-menu tersebut sudah ada di sidebar kiri dan tab controller), tidak user-friendly, dan mengganggu estetika antarmuka. Kedua elemen ini **harus dihapus total**.
2. **Bug Tampilan Rusak pada Pagination Audit Log (`« Previous Next »`)**:
   - Saat membuka `http://localhost/magkom/public/superadmin/audit-log?page=2`, muncul ikon panah chevron raksasa (`<` atau `>`) berwarna hitam/abu-abu gelap yang membentang menutupi hampir separuh layar, dengan deretan angka `1 2 3 4 5 6 7 8` yang terdistorsi di bawahnya.
   - **Akar Masalah (Root Cause)**: Laravel secara default menggunakan view pagination Tailwind (`pagination::tailwind`). View tersebut merender tag inline `<svg class="w-5 h-5">`. Karena tema Gentelella / MagKom **tidak menggunakan Tailwind CSS**, class `.w-5` dan `.h-5` tidak ada, sehingga tag `<svg>` membesar 100% mengikuti lebar kontainer (`> 1000px`).
   - **Solusi**: Buat template pagination kustom berstandar MagKom Design System di `resources/views/vendor/pagination/default.blade.php`, daftarkan di `AppServiceProvider`, beri aturan pembatas ukuran SVG (*guardrail clamp*), dan berikan styling tombol navigasi yang rapi, bersih, dan mendukung tema Terang/Gelap.

---

## 2. Matriks Perubahan File (File Change Matrix)

| No | File Path | Aksi | Ringkasan Perubahan |
|:---|:---|:---|:---|
| 1 | `resources/views/superadmin/dashboard.blade.php` | **Modify** | Hapus blok `.sa-shortcuts` ("PINTASAN TINDAKAN:") dan widget `<x-gentelella.x-panel title="Aksi cepat">`. |
| 2 | `tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php` | **Modify** | Sesuaikan assertion test `test_dashboard_renders_hero_with_quick_actions_toolbar` agar tidak mencari elemen yang sudah sengaja dihapus, dan pastikan test suite tetap 100% hijau. |
| 3 | `resources/views/vendor/pagination/default.blade.php` | **Create** | Template Blade pagination kustom MagKom (semantik, ramah aksesibilitas, label Bahasa Indonesia, info ringkasan data). |
| 4 | `resources/views/vendor/pagination/simple-default.blade.php` | **Create** | Template Blade pagination sederhana (`simplePaginate`). |
| 5 | `app/Providers/AppServiceProvider.php` | **Modify** | Daftarkan `Paginator::defaultView` dan `Paginator::defaultSimpleView` agar digunakan secara konsisten oleh seluruh aplikasi. |
| 6 | `resources/views/layouts/dashboard.blade.php` | **Modify** | Tambahkan stylesheet komponen `.magkom-pagination-nav` dan pembatas ukuran SVG (`max-width: 14px !important`) agar tidak pernah ada ikon panah yang membengkak lagi. |

---

## 3. Petunjuk Langkah Kerja Terperinci (Step-by-Step Directives)

### Bagian A: Menghapus "Pintasan Tindakan" & "Aksi Cepat" pada Dashboard SuperAdmin

#### Langkah A.1: Modifikasi `resources/views/superadmin/dashboard.blade.php`
1. Buka file [resources/views/superadmin/dashboard.blade.php](file:///g:/MAGKOM/resources/views/superadmin/dashboard.blade.php).
2. Temukan dan **HAPUS** blok HTML berikut (sekitar baris 85–99):
```blade
        {{-- Quick Action Shortcuts Strip (Untuk Preservasi Assertions Test Suite) --}}
        <div class="sa-shortcuts" style="background: var(--cv-surface); border: 1px solid var(--cv-line); border-radius: 8px; padding: 0.65rem 1rem; margin-top: 0;">
            <div class="sa-shortcuts__list">
                <span class="sa-shortcuts__label">Pintasan Tindakan:</span>
                <a href="{{ route('superadmin.staf.index') }}" class="sa-shortcut-chip"><x-sa.icon name="user-plus" :size="13" /><span>+ Akun Staf</span></a>
                <a href="{{ route('superadmin.bulan.index') }}" class="sa-shortcut-chip"><x-sa.icon name="calendar" :size="13" /><span>+ Bulan Magang</span></a>
                <a href="{{ route('admin.pendaftaran.index') }}" class="sa-shortcut-chip"><x-sa.icon name="clipboard-check" :size="13" /><span>Tinjau Berkas</span></a>
                <a href="{{ route('admin.penempatan.index') }}" class="sa-shortcut-chip"><x-sa.icon name="clock" :size="13" /><span>Penempatan Peserta</span></a>
                <a href="{{ route('admin.laporan.index') }}" class="sa-shortcut-chip"><x-sa.icon name="download" :size="13" /><span>Ekspor Laporan</span></a>
                <a href="{{ route('superadmin.health.index') }}" class="sa-shortcut-chip"><x-sa.icon name="activity" :size="13" /><span>Telemetri Sistem</span></a>
                <a href="{{ route('superadmin.backup.index') }}" class="sa-shortcut-chip"><x-sa.icon name="database" :size="13" /><span>Backup Hub</span></a>
                <a href="{{ route('superadmin.staf.index') }}" class="sa-sr-only">Kelola Pengguna</a>
                <a href="{{ route('superadmin.audit-log.index') }}" class="sa-sr-only">Audit Trail</a>
            </div>
        </div>
```
3. Temukan dan **HAPUS** panel widget berikut di dalam `<aside>` kolom samping (sekitar baris 193–207):
```blade
        {{-- Widget 2: Aksi Cepat --}}
        <x-gentelella.x-panel title="Aksi cepat">
            <div class="cv-quick-actions">
                <a href="{{ route('superadmin.staf.index') }}" class="btn-primary btn-block" style="padding: 0.6rem 1rem; border-radius: 8px; font-weight: 700; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.35rem;">
                    <x-sa.icon name="user-plus" :size="16" />
                    <span>+ Tambah staf</span>
                </a>
                <a href="{{ route('superadmin.bulan.index') }}" class="btn-secondary" style="padding: 0.5rem 0.75rem; border-radius: 8px; font-weight: 600; text-decoration: none; text-align: center; font-size: 0.8125rem;">
                    Buka periode
                </a>
                <a href="{{ route('superadmin.backup.index') }}" class="btn-secondary" style="padding: 0.5rem 0.75rem; border-radius: 8px; font-weight: 600; text-decoration: none; text-align: center; font-size: 0.8125rem;">
                    Backup sekarang
                </a>
            </div>
        </x-gentelella.x-panel>
```

#### Langkah A.2: Perbarui Assertion Test Suite
1. Buka file [tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php](file:///g:/MAGKOM/tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php).
2. Perbarui method `test_dashboard_renders_hero_with_quick_actions_toolbar` (sekitar baris 215–237) agar tidak lagi mencari teks pintasan yang sudah dihapus, melainkan memastikan dashboard bersih dari noise (*decluttered*):
```php
    /**
     * Memastikan hero section bersih tanpa toolbar pintasan yang redundant (Declutter UX).
     */
    public function test_dashboard_renders_hero_with_clean_decluttered_layout(): void
    {
        $this->withoutExceptionHandling();
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Eksekutif Tata Kelola MagKom');
        $response->assertSee('Dashboard SuperAdmin');

        // Memastikan baris pintasan dan card aksi cepat tidak dirender (Clean UX)
        $response->assertDontSee('Pintasan Tindakan:');
        $response->assertDontSee('Aksi cepat');
    }
```

---

### Bagian B: Perbaikan Total Pagination Audit Log SuperAdmin

#### Langkah B.1: Buat Template Pagination Standar MagKom
1. Buat direktori `resources/views/vendor/pagination/` jika belum ada.
2. Buat file baru [resources/views/vendor/pagination/default.blade.php](file:///g:/MAGKOM/resources/views/vendor/pagination/default.blade.php) dengan isi kode berikut:
```blade
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="magkom-pagination-nav">
        {{-- Ringkasan Jumlah Data --}}
        <div class="magkom-pagination-summary">
            <span>
                Menampilkan
                <strong>{{ $paginator->firstItem() ?? 0 }}</strong>
                sampai
                <strong>{{ $paginator->lastItem() ?? 0 }}</strong>
                dari
                <strong>{{ $paginator->total() }}</strong>
                entri
            </span>
        </div>

        {{-- Tombol Halaman --}}
        <ul class="magkom-pagination-list">
            {{-- Tombol Sebelumnya --}}
            @if ($paginator->onFirstPage())
                <li class="magkom-page-item disabled" aria-disabled="true">
                    <span class="magkom-page-link">&laquo; Sebelumnya</span>
                </li>
            @else
                <li class="magkom-page-item">
                    <a class="magkom-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Sebelumnya</a>
                </li>
            @endif

            {{-- Elemen Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Separator Titik Tiga (...) --}}
                @if (is_string($element))
                    <li class="magkom-page-item disabled" aria-disabled="true">
                        <span class="magkom-page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Tautan Nomor Halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="magkom-page-item active" aria-current="page">
                                <span class="magkom-page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="magkom-page-item">
                                <a class="magkom-page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Berikutnya --}}
            @if ($paginator->hasMorePages())
                <li class="magkom-page-item">
                    <a class="magkom-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya &raquo;</a>
                </li>
            @else
                <li class="magkom-page-item disabled" aria-disabled="true">
                    <span class="magkom-page-link">Berikutnya &raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
```

3. Buat file baru [resources/views/vendor/pagination/simple-default.blade.php](file:///g:/MAGKOM/resources/views/vendor/pagination/simple-default.blade.php) dengan isi kode berikut:
```blade
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman Sederhana" class="magkom-pagination-nav">
        <ul class="magkom-pagination-list" style="width: 100%; justify-content: space-between;">
            @if ($paginator->onFirstPage())
                <li class="magkom-page-item disabled" aria-disabled="true">
                    <span class="magkom-page-link">&laquo; Sebelumnya</span>
                </li>
            @else
                <li class="magkom-page-item">
                    <a class="magkom-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo; Sebelumnya</a>
                </li>
            @endif

            @if ($paginator->hasMorePages())
                <li class="magkom-page-item">
                    <a class="magkom-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya &raquo;</a>
                </li>
            @else
                <li class="magkom-page-item disabled" aria-disabled="true">
                    <span class="magkom-page-link">Berikutnya &raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
```

#### Langkah B.2: Daftarkan View di `app/Providers/AppServiceProvider.php`
1. Buka [app/Providers/AppServiceProvider.php](file:///g:/MAGKOM/app/Providers/AppServiceProvider.php).
2. Tambahkan import di bagian atas:
```php
use Illuminate\Pagination\Paginator;
```
3. Di dalam method `boot()`, tambahkan konfigurasi view default:
```php
        // Daftarkan Custom Pagination MagKom (Cegah Tailwind SVG Blowup)
        Paginator::defaultView('vendor.pagination.default');
        Paginator::defaultSimpleView('vendor.pagination.simple-default');
```

#### Langkah B.3: Tambahkan Styling CSS & SVG Guardrail Clamp
1. Buka [resources/views/layouts/dashboard.blade.php](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php).
2. Pada blok `<style>` (sekitar baris 515), tambahkan CSS berikut:
```css
        /* =====================================================================
           MAGKOM DESIGN SYSTEM: PAGINATION COMPONENT
           ===================================================================== */
        .magkom-pagination-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 0.25rem 0;
            font-size: 0.8125rem;
            color: var(--neutral-700);
        }

        .magkom-pagination-summary {
            color: var(--neutral-600);
            font-size: 0.8125rem;
        }

        .magkom-pagination-summary strong {
            color: var(--neutral-900);
            font-weight: 700;
        }

        .magkom-pagination-list {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            list-style: none;
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }

        .magkom-page-item {
            display: inline-flex;
        }

        .magkom-page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 0.65rem;
            border-radius: 6px;
            font-size: 0.8125rem;
            font-weight: 600;
            text-decoration: none;
            color: var(--neutral-700);
            background: var(--neutral-0);
            border: 1px solid var(--neutral-300, #D0D3D9);
            transition: all 0.15s ease;
            user-select: none;
        }

        a.magkom-page-link:hover {
            background: var(--neutral-100);
            color: var(--blue-700);
            border-color: var(--blue-tint-line);
        }

        .magkom-page-item.active .magkom-page-link {
            background: var(--blue-600);
            color: #FFFFFF !important;
            border-color: var(--blue-600);
            font-weight: 700;
        }

        .magkom-page-item.disabled .magkom-page-link {
            color: var(--neutral-400);
            background: var(--neutral-150);
            border-color: var(--neutral-200);
            cursor: not-allowed;
            pointer-events: none;
        }

        /* GUARDRAIL: Cegah SVG pagination membengkak ke ukuran raksasa */
        .magkom-pagination-nav svg,
        .pagination svg,
        nav[role="navigation"] svg {
            width: 14px !important;
            height: 14px !important;
            max-width: 14px !important;
            max-height: 14px !important;
            display: inline-block !important;
            vertical-align: middle;
        }

        /* Tema Gelap (Dark Mode Parity) */
        [data-theme="dark"] .magkom-pagination-nav {
            color: #94A3B8;
        }
        [data-theme="dark"] .magkom-pagination-summary {
            color: #94A3B8;
        }
        [data-theme="dark"] .magkom-pagination-summary strong {
            color: #F8FAFC;
        }
        [data-theme="dark"] .magkom-page-link {
            background: var(--neutral-0);
            border-color: var(--neutral-300);
            color: #CBD5E1;
        }
        [data-theme="dark"] a.magkom-page-link:hover {
            background: var(--neutral-100);
            color: #93C5FD;
            border-color: rgba(59, 130, 246, 0.4);
        }
        [data-theme="dark"] .magkom-page-item.active .magkom-page-link {
            background: var(--blue-600);
            color: #FFFFFF !important;
            border-color: var(--blue-600);
        }
        [data-theme="dark"] .magkom-page-item.disabled .magkom-page-link {
            background: rgba(30, 41, 59, 0.5);
            border-color: rgba(51, 65, 85, 0.5);
            color: #64748B;
        }
```

---

## 4. Strategi Pengujian & Verifikasi

### Skenario Otomatis (PHPUnit / Pest)
1. Jalankan pembersihan cache view dan konfigurasi:
   ```bash
   php artisan view:clear
   php artisan config:clear
   ```
2. Jalankan test suite relevan:
   ```bash
   php artisan test tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php
   php artisan test tests/Feature/SuperAdminDashboardAndAuditLogTest.php
   ```
3. Pastikan seluruh pengujian lulus dengan status **Green (OK)** tanpa ada regresi.

### Skenario Verifikasi Visual & Interaktif di Browser
1. **Verifikasi Dashboard SuperAdmin** (`http://localhost/magkom/public/superadmin/dashboard`):
   - Periksa di bawah kartu periode: Teks dan chip **"PINTASAN TINDAKAN:"** sudah **hilang total**.
   - Periksa di kolom samping kanan: Widget **"Aksi cepat"** sudah **hilang total**.
   - Hanya widget **"Perlu tindakan"** yang tersisa di kolom kanan dengan rapi dan seimbang.
2. **Verifikasi Audit Log SuperAdmin** (`http://localhost/magkom/public/superadmin/audit-log` atau `.../audit-log?page=2`):
   - Ikon panah chevron raksasa yang menutupi separuh layar sudah **hilang 100%**.
   - Di bagian bawah tabel log, pagination tampil rapi:
     - Di sisi kiri: Ringkasan data `"Menampilkan 1 sampai 30 dari 238 entri"`.
     - Di sisi kanan: Tombol navigasi bertingkat `« Sebelumnya`, nomor halaman `1`, `2`, `3`, ..., dan tombol `Berikutnya »`.
     - Halaman aktif disorot dengan latar biru khas Sleman/MagKom (`#0751FF`).
     - Tombol dapat diklik dan berhasil berpindah antar halaman secara mulus.
3. **Uji Responsif & Tema**:
   - Ganti tema ke Dark Mode: Tombol pagination memiliki kontras yang nyaman dan terbaca jelas.

---

## 5. Kriteria Selesai (Definition of Done)

- [x] Blok `.sa-shortcuts` ("PINTASAN TINDAKAN:") terhapus dari `resources/views/superadmin/dashboard.blade.php`.
- [x] Widget `<x-gentelella.x-panel title="Aksi cepat">` terhapus dari `resources/views/superadmin/dashboard.blade.php`.
- [x] Test assertion pada `SuperAdminUnifiedDashboardFeaturesTest.php` diperbarui dan lulus 100%.
- [x] Template `resources/views/vendor/pagination/default.blade.php` dan `simple-default.blade.php` berhasil dibuat.
- [x] `AppServiceProvider.php` mendaftarkan template pagination default.
- [x] CSS class `.magkom-pagination-nav` dan guardrail SVG clamp terpasang di `resources/views/layouts/dashboard.blade.php`.
- [x] Halaman Audit Log (`/superadmin/audit-log?page=2`) bebas dari chevron raksasa dan pagination tampil estetik.
- [x] Seluruh halaman tabel lain yang menggunakan pagination (`/superadmin/staf`, `/superadmin/monitoring`, dll.) otomatis tertata rapi.
