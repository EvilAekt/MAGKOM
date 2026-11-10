# Rencana Kerja: Redesain Halaman Pengaturan & Preferensi Seluruh Dashboard Role MagKom

**Tanggal**: 10 November 2026  
**Status**: Menunggu Konfirmasi Persetujuan Pengguna  
**Dokumen Referensi**: `docs/plans/2026-11-10-plan-redesign-pengaturan-akun-seluruh-role.md`  
**Fitur**: Portal Pengaturan Akun & Preferensi Seluruh 4 Peran MagKom:
1. **Pembimbing**: `/pembimbing/pengaturan` (`resources/views/pembimbing/pengaturan/index.blade.php`)
2. **Peserta**: `/peserta/pengaturan` (`resources/views/peserta/pengaturan/index.blade.php`)
3. **Administrator**: `/admin/pengaturan` (`resources/views/admin/pengaturan/index.blade.php`)
4. **SuperAdmin**: `/superadmin/akun` (`resources/views/superadmin/akun/index.blade.php`)  
**Tangkapan Layar Acuan**: `media_1794313542091_9759c8c1.jpg`

---

## 1. Analisis Kebutuhan & Spesifikasi Visual

Sesuai instruksi pengguna dan gambar acuan (`media_1794313542091_9759c8c1.jpg`), tampilan menu pengaturan akun akan distandardisasi dan dimodernisasi ke seluruh 4 dashboard role MagKom dengan ketentuan:
- **Tampilan dan layout 100% identik dengan gambar acuan**: Hero Banner Abyssal Navy, Gold Status Widget, Bento Grid 2 Kolom (Informasi Akun + Ganti Kata Sandi), dan Bottom Full-Width Card (Preferensi Antarmuka & Bahasa dengan 3 kartu miniatur tema interaktif).
- **Isi konten tetap adaptif sesuai role masing-masing**: Variabel data, hak akses, instansi/bidang, rute form password, dan rute preferensi tetap terikat dengan data backend yang sudah berjalan.
- **Kepatuhan AGENTS.md**: **Zero inline `<style>` dan `<script>` di Blade**. Seluruh styling dipusatkan ke `resources/css/components/pengaturan-akun.css` (diimpor di `resources/css/app.css` via Vite), dan interaksi dipusatkan ke `resources/js/components/pengaturan-akun.js` (diimpor di `resources/js/app.js`).

---

## 2. Struktur Komponen Desain Acuan

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ HERO BANNER (Sleman Abyssal Navy #003566 / #02275D)                                    │
│ ┌───────────────────────────────────────────────┐  ┌─────────────────────────────────┐ │
│ │ Akun & Preferensi Saya                        │  │ STATUS AKUN                     │ │
│ │ Kelola informasi kredensial, keamanan...      │  │ Aktif                           │ │
│ │                                               │  │ Portal [Peran Pengguna]         │ │
│ │ [KAPSUL 1: NAMA & PERAN]  [KAPSUL 2: HAK AKSES]│  │ [ Lihat Profil Lengkap ] (Navy) │ │
│ └───────────────────────────────────────────────┘  └─────────────────────────────────┘ │
└────────────────────────────────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────┐  ┌───────────────────────────────────────────────┐
│ INFORMASI AKUN (Kolom Kiri)          │  │ GANTI KATA SANDI (Kolom Kanan)                │
│ ┌────┐ Nama Pengguna                 │  │ Kata sandi saat ini       [Input + 👁️ toggle]  │
│ │AVAT│ [CHIP ROLE]                   │  │ Kata sandi baru           [Input + 👁️ toggle]  │
│ └────┘                               │  │ Konfirmasi kata sandi baru[Input + 👁️ toggle]  │
│ ------------------------------------ │  │                                               │
│ Nama Lengkap       : [Nama]          │  │ (i) Minimal 8 karakter, kombinasi huruf & ... │
│ Alamat Email       : [Email]         │  │                                               │
│ Nomor Kontak       : [No Telp]       │  │ [ Perbarui Kata Sandi ]  [ Batal ]            │
│ Hak Akses/Instansi : [Nilai Role]    │  │                                               │
│ Status Akun        : [Aktif (Kuning)]│  │                                               │
│ ------------------------------------ │  │                                               │
│ ℹ️ Foto profil & telp di Profil Saya │  │                                               │
└──────────────────────────────────────┘  └───────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────────────────────────────────┐
│ PREFERENSI ANTARMUKA & BAHASA (Full-Width Bottom Card)                                 │
│ ┌───────────────────────────┐  ┌─────────────────────────────────────────────────────┐ │
│ │ Bahasa Antarmuka          │  │ Mode Tampilan Tema                                  │ │
│ │ [ Indonesia (Default)  ▼] │  │ ┌──────────────┐  ┌──────────────┐  ┌─────────────┐ │ │
│ │                           │  │ │ [• ─── ]     │  │ │ [• ─── ]     │  │ │ [• ─── ]    │ │ │
│ │ Bahasa yang digunakan ... │  │ │ Terang       │  │ │ Gelap        │  │ │ Ikuti Sistem│ │ │
│ └───────────────────────────┘  │ └──────────────┘  └──────────────┘  └─────────────┘ │ │
│                                └─────────────────────────────────────────────────────┘ │
│ [ Simpan Preferensi Tampilan ]                                                         │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Matriks Data & Variasi Tiap Role

| Fitur / Elemen | Pembimbing | Peserta | Administrator | SuperAdmin |
| :--- | :--- | :--- | :--- | :--- |
| **Route Index** | `pembimbing.pengaturan.index` | `peserta.pengaturan.index` | `admin.pengaturan.index` | `superadmin.akun.index` |
| **Layout Blade** | `@extends('layouts.dashboard')` | `@extends('layouts.peserta')` | `@extends('layouts.dashboard')` | `@extends('layouts.dashboard')` |
| **Kapsul 1** | `PEMBIMBING`: `{{ $user->nama }}` | `PESERTA`: `{{ $user->nama }}` | `ADMINISTRATOR`: `{{ $user->nama }}` | `SUPER ADMIN`: `{{ $user->nama }}` |
| **Kapsul 2** | `HAK AKSES`: `Pembimbing Magang` | `PENEMPATAN`: `{{ $penempatan->bidang->nama_bidang ?? 'Dinas Komunikasi dan Informatika' }}` | `HAK AKSES`: `Administrasi Dinas` | `HAK AKSES`: `Super Administrator` |
| **Subtext Gold Widget** | `Portal pembimbing lapangan` | `Portal peserta magang` | `Portal operasional dinas` | `Portal tata kelola sistem` |
| **Link Tombol Widget** | `route('pembimbing.profil.show')` | `route('peserta.profil.show')` | `route('admin.profil.show')` | `route('superadmin.profil.show')` |
| **Role Chip Kiri** | `PEMBIMBING LAPANGAN` | `PESERTA MAGANG` | `ADMINISTRASI DINAS` | `SUPER ADMINISTRATOR` |
| **Baris Khusus Kredensial** | `Hak Akses`: Pembimbing Magang | `Instansi`: `{{ $profil->asal_instansi ?? 'Belum diisi' }}` | `Hak Akses`: Administrator | `Hak Otoritas`: Pengelola Utama |
| **Form Password Action** | `route('pembimbing.pengaturan.password.update')` | `route('peserta.pengaturan.password.update')` | `route('admin.pengaturan.password.update')` | `route('superadmin.akun.password.update')` |
| **Form Preferensi Action**| `route('pembimbing.pengaturan.preferensi.update')`| `route('peserta.pengaturan.preferensi.update')`| `route('admin.pengaturan.preferensi.update')`| `route('superadmin.akun.preferensi.update')`|
| **Catatan Callout Khusus** | Link ke Profil Saya | Link ke Profil Saya | Link ke Profil Saya | Link ke Profil Saya & link ke Pengaturan Sistem (`superadmin.pengaturan.index`) |

---

## 4. Rincian Perubahan File & Arsitektur

### 4.1 Pembuatan Shared CSS Component
- **File**: `resources/css/components/pengaturan-akun.css`
- **Isi**:
  - Hero banner container (`.settings-hero-banner`), gradien navy Sleman (`#003566` ke `#02275D`), border-radius 18px.
  - Kapsul konteks semi-transparan (`.hero-context-pill`).
  - Widget status kuning Sleman (`.banner-gold-widget`, `#FFCB05` / `#FFC300`, rounded 14px, teks tebal).
  - Tombol aksi widget (`.btn-gold-action`, navy `#003566`).
  - Bento Grid 2 kolom (`.settings-grid-2col`), kartu putih (`.settings-white-card`), border `#E2E8F0`, rounded 16px.
  - Profile identity mini header, inisial avatar (`.avatar-circle`), role chip pill.
  - Baris kredensial (`.credential-row`, flex justify-between, label `#64748B`, value `#1E293B`).
  - Status badge pill (`.badge-status-gold`, kuning `#FFC300`).
  - Alert info box (`.alert-info-box`, latar `#EFF6FF`, border `#BFDBFE`).
  - Form group password, wrapper eye toggle button (`.toggle-eye-btn`).
  - Tombol aksi: `.btn-action-navy` (`#003566`) dan `.btn-action-outline`.
  - Preferensi layout (`.pref-horizontal-grid`), dropdown select bahasa.
  - Mode tema cards (`.theme-cards-grid`, `.theme-preview-card`, `.theme-mini-box`).
  - Dark mode harmonisasi (`[data-theme="dark"]`) untuk seluruh kartu, teks, input, dan kontras WCAG 2.1 AA.
- **Pendaftaran**: Diimpor ke `resources/css/app.css` via `@import './components/pengaturan-akun.css';`.

### 4.2 Pembuatan Shared JavaScript Component
- **File**: `resources/js/components/pengaturan-akun.js`
- **Fungsi**:
  - `initPasswordToggles()`: Event delegation untuk toggle melihat/menyembunyikan teks kata sandi (`data-toggle-target`), mengganti ikon mata terbuka / tercoret tanpa inline script.
  - `initThemeSelection()`: Event listener kartu tema (`.theme-preview-card`), otomatis mencentang radio input terkait dan menambahkan class `.active` saat diklik.
- **Pendaftaran**: Diimpor ke `resources/js/app.js` dan dijalankan saat `DOMContentLoaded`.

### 4.3 Pembaruan Markup Blade (Permohonan Izin Sesuai AGENTS.md Section 3.3)
Struktur markup Blade lama di keempat file berikut akan dibersihkan dari tag `<style>` dan `<script>` inline, dan digantikan dengan markup semantik baru yang mengacu ke CSS/JS di atas:
1. `resources/views/pembimbing/pengaturan/index.blade.php`
2. `resources/views/peserta/pengaturan/index.blade.php`
3. `resources/views/admin/pengaturan/index.blade.php`
4. `resources/views/superadmin/akun/index.blade.php`

---

## 5. Rencana Verifikasi & Quality Gates

1. **Vite Asset Compilation**:
   - Jalankan `npm run build` untuk memverifikasi kompilasi CSS & JS tanpa sintaks error.
2. **Clear Cache Tampilan**:
   - `php artisan view:clear`
3. **Automated Feature Tests**:
   - `tests/Feature/StaffProfileAndSettingsTest.php` (Pengujian password & preferensi Admin, Pembimbing, SuperAdmin).
   - `tests/Feature/LanguageLocalizationTest.php` (Pengujian preferensi bahasa & tema seluruh role).
   - `tests/Feature/DashboardLayoutTest.php` (Pengujian layout & sidebar footer theme toggle).
4. **Code Quality & Formatting**:
   - `./vendor/bin/pint --test`
5. **Visual Inspection**:
   - Memastikan kesesuaian visual 100% dengan mockup gambar di desktop dan responsive di perangkat mobile.

---

## 6. Persetujuan & Langkah Lanjutan

Mohon berikan persetujuan untuk mengeksekusi rencana ini dengan menekan tombol **Proceed** atau membalas **"gas"** / **"lanjutkan"**. Setelah disetujui, kami akan segera:
1. Membuat `resources/css/components/pengaturan-akun.css` dan mengimpornya ke `resources/css/app.css`.
2. Membuat `resources/js/components/pengaturan-akun.js` dan mengimpornya ke `resources/js/app.js`.
3. Memperbarui markup Blade di 4 file view terkait.
4. Menjalankan pengujian otomatis, kompilasi build, dan verifikasi Pint.

