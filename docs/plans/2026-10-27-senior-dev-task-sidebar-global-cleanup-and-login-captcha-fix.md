# Task Senior Developer: Global Sidebar Optimization & Perbaikan Bug Login CAPTCHA

- **Dokumen Referensi**: PRD MagKom Revisi 5 / Design System DS-MagKom (Bab 2) / Security Specification (Bab 10)
- **Tingkat Kesulitan**: Junior to Mid / Beginner AI Directives (Gemini 3.8 Flash Compatible)
- **Tanggal Rilis**: 27 Oktober 2026
- **Status**: Selesai & Terverifikasi (Completed & Verified)

---

## 1. Konteks & Problem Statement

Berdasarkan tinjauan langsung dan laporan kendala dari Product Owner / Pengguna:
1. **Teks Label Section Rusak saat Mini Sidebar (`nav-sm`) Aktif (Global All Roles)**:
   - Ketika sidebar diciutkan menjadi mode mini/ikon (`nav-sm` lebar 70px) melalui tombol toggle desktop di kiri atas, teks label section seperti `MODUL OPERASIONAL`, `SISTEM`, `PENGATURAN`, `Area Peserta`, `Bimbingan Magang`, dll. tidak tersembunyi.
   - Karena kontainer sidebar mengecil ke 70px, teks panjang tersebut terbungkus vertikal secara terpotong (`MODUL \n OPERASIO`), tidak dapat dibaca, dan merusak tata letak ikon. Padahal mini sidebar pada dasarnya hanya ditujukan untuk menampilkan ikon navigasi yang bersih dan terpusat (*icon-only mode*).
   - **Akar Masalah (Root Cause)**: Di stylesheet master layout dan `gentelella-theme.css`, aturan `body.nav-sm` menyembunyikan `.menu_section h3`, sedangkan di markup HTML Blade yang digunakan adalah class `.nav-section-label` (`<span class="nav-section-label">`). Ketidakcocokan selector (*selector mismatch*) ini menyebabkan label section tetap dirender paksa di dalam mini sidebar 70px.
2. **Redundansi Menu Akun Pribadi / Pengaturan di Sidebar**:
   - Di sidebar kiri terdapat grup menu `AKUN PRIBADI` (pada SuperAdmin) atau `PENGATURAN` (pada Admin dan Pembimbing) yang berisi `Profil Saya` dan `Akun & Preferensi` / `Pengaturan Akun`.
   - Di topbar kanan atas, avatar pengguna sudah memiliki menu dropdown resmi yang memuat `Profil Saya`, `Akun & Preferensi`, `Ke Beranda Publik`, dan `Keluar (Logout)`.
   - **Kebutuhan Pengguna**: Hapus bagian `AKUN PRIBADI` / `PENGATURAN` dari sidebar agar menu samping fokus pada navigasi kerja operasional, tidak menduplikasi fungsi topbar, dan membuat antarmuka lebih ringkas.
3. **Bug Tidak Bisa Login & Hilangnya Fitur CAPTCHA**:
   - Form login di `/login` kehilangan gambar SVG CAPTCHA 4 karakter dan kolom input verifikasinya.
   - Pengguna tidak dapat login ke sistem.
   - **Akar Masalah (Root Cause)**:
     - Hilangnya CAPTCHA disebabkan oleh pengaturan `CAPTCHA_ENABLED=false` pada file `.env`. Komponen Blade `<x-captcha-field />` mendeteksi flag ini bernilai false sehingga tidak merender elemen captcha.
     - Kegagalan login disebabkan oleh ketidaksinkronan password akun `superadmin@magkom.go.id` pada database lokal yang sebelumnya sempat ter-overwrite saat sesi testing, sehingga tidak cocok dengan kredensial resmi `.env` (`SuperAdminMagKom123!`).

---

## 2. Matriks Perubahan File (File Change Matrix)

| No | File Path | Aksi | Ringkasan Perubahan |
|:---|:---|:---|:---|
| 1 | `resources/views/layouts/dashboard.blade.php` | **Modify** | 1. Tambahkan `body.nav-sm .nav-section-label` ke `display: none !important;` pada media query `nav-sm`.<br>2. Hapus blok HTML `AKUN PRIBADI` / `PENGATURAN` pada navigasi sidebar SuperAdmin, Admin, dan Pembimbing. |
| 2 | `public/css/gentelella-theme.css` | **Modify** | Tambahkan `body.nav-sm .nav-section-label` ke aturan `display: none !important;` pada breakpoint desktop `nav-sm`. |
| 3 | `.env` | **Modify** | Ubah `CAPTCHA_ENABLED=false` menjadi `CAPTCHA_ENABLED=true` agar widget CAPTCHA aktif kembali. |
| 4 | `.env.example` | **Modify** | Pastikan dokumentasi `CAPTCHA_ENABLED=true` terdokumentasi jelas dengan nilai default aktif. |
| 5 | Database (User Table) | **Action** | Sinkronkan password akun `superadmin@magkom.go.id` menjadi `SuperAdminMagKom123!` via artisan tinker / seeder. |

---

## 3. Petunjuk Langkah Kerja Terperinci (Step-by-Step Directives)

### Bagian A: Perbaikan Mini Sidebar Global (`nav-sm`)

#### Langkah A.1: Tambahkan Selector CSS di `public/css/gentelella-theme.css`
1. Buka file [public/css/gentelella-theme.css](file:///g:/MAGKOM/public/css/gentelella-theme.css).
2. Temukan blok aturan `/* Desktop Menciut (nav-sm) */` di sekitar baris 604–615:
```css
/* Desktop Menciut (nav-sm) */
    body.nav-sm .sidebar-brand-text,
    body.nav-sm .profile_info,
    body.nav-sm .menu_section h3,
    body.nav-sm .nav-item-link span:not(.nav-item-icon),
    body.nav-sm .nav-group-btn span.nav-group-btn-left span:not(.nav-item-icon),
    body.nav-sm .nav-group-chevron,
    body.nav-sm .nav-sub-menu {
        display: none !important;
    }
```
3. Tambahkan selector `body.nav-sm .nav-section-label` ke dalam daftar tersebut sehingga menjadi:
```css
/* Desktop Menciut (nav-sm) */
    body.nav-sm .sidebar-brand-text,
    body.nav-sm .profile_info,
    body.nav-sm .menu_section h3,
    body.nav-sm .nav-section-label,
    body.nav-sm .nav-item-link span:not(.nav-item-icon),
    body.nav-sm .nav-group-btn span.nav-group-btn-left span:not(.nav-item-icon),
    body.nav-sm .nav-group-chevron,
    body.nav-sm .nav-sub-menu {
        display: none !important;
    }
```

#### Langkah A.2: Tambahkan Selector CSS di `resources/views/layouts/dashboard.blade.php`
1. Buka file [resources/views/layouts/dashboard.blade.php](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php).
2. Temukan blok `/* Mode Menciut Desktop (nav-sm) */` di sekitar baris 2108–2117:
```css
        /* Mode Menciut Desktop (nav-sm) */
        @media (min-width: 992px) {
            body.nav-sm .sidebar-brand-text,
            body.nav-sm .profile_info,
            body.nav-sm .menu_section h3,
            body.nav-sm .nav-item-link span:not(.nav-item-icon),
            body.nav-sm .nav-group-btn span.nav-group-btn-left span:not(.nav-item-icon),
            body.nav-sm .nav-group-chevron,
            body.nav-sm .nav-sub-menu {
                display: none !important;
            }
```
3. Tambahkan selector `body.nav-sm .nav-section-label` ke dalam aturan tersebut:
```css
        /* Mode Menciut Desktop (nav-sm) */
        @media (min-width: 992px) {
            body.nav-sm .sidebar-brand-text,
            body.nav-sm .profile_info,
            body.nav-sm .menu_section h3,
            body.nav-sm .nav-section-label,
            body.nav-sm .nav-item-link span:not(.nav-item-icon),
            body.nav-sm .nav-group-btn span.nav-group-btn-left span:not(.nav-item-icon),
            body.nav-sm .nav-group-chevron,
            body.nav-sm .nav-sub-menu {
                display: none !important;
            }
```

---

### Bagian B: Hapus "AKUN PRIBADI" / "PENGATURAN" dari Sidebar Global

#### Langkah B.1: Hapus pada Navigasi SuperAdmin
1. Buka [resources/views/layouts/dashboard.blade.php](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php).
2. Temukan dan **HAPUS** blok menu berikut di bagian bawah navigasi SuperAdmin (sekitar baris 3028–3049):
```blade
                        <!-- GRUP 3: AKUN & PROFIL PRIBADI -->
                        <span class="nav-section-label">{{ __('AKUN PRIBADI') }}</span>

                        <a href="{{ route('superadmin.profil.show') }}" class="nav-item-link {{ request()->routeIs('superadmin.profil.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </span>
                            <span>{{ __('Profil Saya') }}</span>
                        </a>

                        <a href="{{ route('superadmin.akun.index') }}" class="nav-item-link {{ request()->routeIs('superadmin.akun.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                            </span>
                            <span>{{ __('Akun & Preferensi') }}</span>
                        </a>
```

#### Langkah B.2: Hapus pada Navigasi Admin
1. Temukan dan **HAPUS** blok menu berikut di bagian navigasi Admin (sekitar baris 2770–2791):
```blade
                        <span class="nav-section-label">{{ __('PENGATURAN') }}</span>

                        <a href="{{ route('admin.profil.show') }}" class="nav-item-link {{ request()->routeIs('admin.profil.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </span>
                            <span>{{ __('Profil Saya') }}</span>
                        </a>

                        <a href="{{ route('admin.pengaturan.index') }}" class="nav-item-link {{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                            </span>
                            <span>{{ __('Pengaturan Akun') }}</span>
                        </a>
```

#### Langkah B.3: Hapus pada Navigasi Pembimbing
1. Temukan dan **HAPUS** blok menu berikut di bagian navigasi Pembimbing (sekitar baris 2651–2672):
```blade
                        <span class="nav-section-label">{{ __('PENGATURAN') }}</span>

                        <a href="{{ route('pembimbing.profil.show') }}" class="nav-item-link {{ request()->routeIs('pembimbing.profil.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </span>
                            <span>{{ __('Profil Saya') }}</span>
                        </a>

                        <a href="{{ route('pembimbing.pengaturan.index') }}" class="nav-item-link {{ request()->routeIs('pembimbing.pengaturan.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                            </span>
                            <span>{{ __('Pengaturan Akun') }}</span>
                        </a>
```

---

### Bagian C: Perbaikan Bug Login & Pemulihan CAPTCHA

#### Langkah C.1: Aktifkan CAPTCHA di Environment
1. Buka file [.env](file:///g:/MAGKOM/.env).
2. Ubah `CAPTCHA_ENABLED=false` menjadi:
```dotenv
CAPTCHA_ENABLED=true
```
3. Buka file [.env.example](file:///g:/MAGKOM/.env.example) dan pastikan baris `CAPTCHA_ENABLED=true` terdokumentasi rapi.
4. Jalankan perintah pembersihan cache konfigurasi:
```bash
php artisan config:clear
```

#### Langkah C.2: Sinkronisasi Password Akun SuperAdmin
1. Jalankan perintah Artisan Tinker untuk menyetel ulang password `superadmin@magkom.go.id` sesuai kredensial resmi `.env`:
```bash
php artisan tinker --execute="\$u = App\Models\User::where('email', 'superadmin@magkom.go.id')->first(); \$u->password = bcrypt('SuperAdminMagKom123!'); \$u->save(); echo 'SuperAdmin Password Restored';"
```
2. Pastikan akun demo staf lainnya (`admin@magkom.go.id`, `pembimbing@magkom.go.id`, `peserta@magkom.go.id`) tetap dapat login menggunakan password default `Password123!`.

---

## 4. Strategi Pengujian & Verifikasi

### Skenario Otomatis (PHPUnit / Pest)
1. Jalankan pengujian CAPTCHA:
   ```bash
   php artisan test tests/Feature/SecurityDynamicCaptchaTest.php
   ```
   *Expected*: Seluruh 8 test suite CAPTCHA berstatus **PASS (100% Hijau)**.
2. Jalankan pengujian layout sidebar & menu:
   ```bash
   php artisan test tests/Feature/DashboardLayoutTest.php
   php artisan test tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php
   ```
   *Expected*: Seluruh test berstatus **PASS (100% Hijau)**.

### Skenario Verifikasi Browser Interaktif
1. **Verifikasi Login & CAPTCHA** (`http://localhost/magkom/public/login`):
   - Widget gambar CAPTCHA 4 karakter dan tombol refresh tampil dengan jelas.
   - Kolom input kode verifikasi keamanan aktif dan wajib diisi.
   - Coba login dengan:
     - Email: `superadmin@magkom.go.id`
     - Password: `SuperAdminMagKom123!`
     - Kode CAPTCHA: 4 huruf/angka yang tertera pada gambar.
   - Hasil: Berhasil masuk ke dashboard tanpa error autentikasi!
2. **Verifikasi Sidebar Normal (Expanded 230px)**:
   - Di sidebar kiri: bagian `AKUN PRIBADI` / `PENGATURAN` sudah **hilang total**.
   - Di topbar kanan atas: klik avatar pengguna, menu dropdown `Profil Saya` dan `Akun & Preferensi` tetap dapat diakses dengan normal.
3. **Verifikasi Mini Sidebar (`nav-sm` 70px)**:
   - Klik tombol burger menu di sebelah logo untuk menciutkan sidebar ke mode mini.
   - Periksa tampilan sidebar:
     - Teks `MODUL OPERASIONAL`, `SISTEM`, `PENGATURAN`, dll. sudah **hilang 100%**.
     - Hanya ikon-ikon navigasi yang tampil secara rapi, simetris, dan terpusat tanpa ada teks terpotong (`MODUL \n OPERASIO`).

---

## 5. Kriteria Selesai (Definition of Done)

- [x] `body.nav-sm .nav-section-label` ditambahkan ke `display: none !important;` di `dashboard.blade.php` dan `gentelella-theme.css`.
- [x] Bagian `AKUN PRIBADI` dihapus dari sidebar SuperAdmin di `dashboard.blade.php`.
- [x] Bagian `PENGATURAN` dihapus dari sidebar Admin di `dashboard.blade.php`.
- [x] Bagian `PENGATURAN` dihapus dari sidebar Pembimbing di `dashboard.blade.php`.
- [x] Menu `Profil Saya` dan `Akun & Preferensi` / `Pengaturan Akun` tetap berfungsi penuh melalui topbar user dropdown.
- [x] `CAPTCHA_ENABLED=true` aktif di `.env` dan `.env.example`.
- [x] Password SuperAdmin `superadmin@magkom.go.id` disinkronkan kembali menjadi `SuperAdminMagKom123!`.
- [x] Form login di `/login` menampilkan widget gambar CAPTCHA 4 karakter dan dapat digunakan untuk login normal.
- [x] Seluruh test suite (`SecurityDynamicCaptchaTest`, `DashboardLayoutTest`, `SuperAdminUnifiedDashboardFeaturesTest`) lulus 100% hijau.
