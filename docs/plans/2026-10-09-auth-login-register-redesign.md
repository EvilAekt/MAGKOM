# Plan: Desain Ulang Tampilan Login dan Daftar MagKom Sesuai Figma (Node 562-56045) & Penambahan Google OAuth

- **Tanggal**: 2026-10-09
- **Status**: Completed (Selesai & Terverifikasi)
- **Author**: Antigravity (web-planning & web-implementation)
- **Rujukan Desain**: 
  - Figma Node `562-56045` / `MAGKOM Kominfo Sleman (1).fig` (`Login - Desktop 1440x900`, `Daftar - Desktop 1440x900 (scroll)`, dan `Template Auth`)
  - Design System MagKom: `docs/07-DESIGN-SYSTEM-MAGKOM.md`
  - Prinsip Bisnis & Aksesibilitas: `PRD-MagKom.md` Bab 9.1 & 9.5, `06-ADDENDUM-KEAMANAN-DAN-QA.md`
- **Tautan Figma Pengguna**: [Figma Frame Node 562-56045](https://www.figma.com/design/uKH1OZjwBIi8iRoKQXEbQr/MAGKOM-Kominfo-Sleman?node-id=562-56045&t=3bap827WnVQ5b2y4-4)

---

## 1. Ringkasan & Tujuan

Tujuan dari rencana ini adalah memperbarui secara menyeluruh antarmuka halaman **Login** (`/login`) dan **Daftar / Registrasi** (`/register`) agar memiliki tampilan split-screen (50:50) yang modern, konsisten, dan presisi sesuai mockup Figma `Template Auth` & Node `562-56045`.

Selain visual dari Figma, plan ini juga mengintegrasikan tombol **Masuk / Daftar dengan Google (OAuth)** yang diminta pengguna dengan estetika Google Identity resmi yang menyatu harmonis dengan Design System MagKom.

### Masalah pada Tampilan Saat Ini:
1. **Layout Card Tunggal & Ketinggalan Zaman**: Login dan Register saat ini masih menggunakan card putih kotak kecil terpusat (`max-width: 450px; margin: 4rem auto;`) yang dibungkus navbar & footer publik landing page, bukan split-screen layar penuh khas auth portal pemerintah yang berwibawa.
2. **Tidak Menggunakan Layout Auth Tersendiri**: Halaman auth mewarisi `@extends('layouts.app')` sehingga navbar publik yang berat (menu alur, bidang, kuota) dan footer landing page muncul mengganggu fokus form.
3. **Belum Memakai Aset Otentik Figma**: Belum menampilkan foto kantor Dinas Kominfo Kabupaten Sleman berbendera Merah Putih dengan Blue Gradient Overlay elegan yang ada di file Figma `MAGKOM Kominfo Sleman (1).fig`.
4. **Tombol Google OAuth Belum Maksimal**: Penempatan tombol Google belum dirapikan dengan divider `atau masuk dengan email` yang proporsional sesuai standar UX modern.

---

## 2. Asumsi & Ketergantungan

- **Kesiapan Backend**:
  - Rute OAuth Google `route('oauth.redirect', 'google')` dan controller `SocialiteController` telah terpasang dan lolos uji feature test (`ReleaseThreeSocialLoginTest.php`).
  - Controller `AuthController` telah mendukung penuh field registrasi (`nama`, `email`, `password`, `password_confirmation`), field login (`email`, `password`, `remember`), Cloudflare Turnstile CAPTCHA, dan audit logging (`AuditService`).
- **Prinsip Hubungan PRD & Figma (PRD 9.1)**:
  - *"Bila desain Figma bertentangan dengan PRD, PRD yang berlaku untuk perilaku (aturan bisnis dan AC), Figma yang berlaku untuk estetika tampilan."*
  - Notifikasi email dimatikan sesuai BR-14, verifikasi akun otomatis aktif untuk peserta magang.
- **Design System v1.0 (DS-MagKom)**:
  - Font: **Open Sans** (sesuai dokumen Design System bab 2.2).
  - Palet Warna: Token `MagKom/Warna` (Blue 600 `#0751FF` untuk aksi utama, Blue 700 `#0641CC` untuk hover/active, Neutral 50 s/d 900 untuk tipografi dan background).
  - Keamanan CSS: Bebas dari style inline yang melanggar CSP produksi (`style-src 'self'`).

---

## 3. Arsitektur & Pendekatan Desain

### 3.1. Master Layout Khusus Auth (`resources/views/layouts/auth.blade.php`)
Membuat layout dedicated khusus halaman autentikasi untuk memisahkan konteks dari layout landing page publik.
- **Viewport Layout**: Full-height (`min-h-screen`, `w-full`, `overflow-hidden lg:overflow-y-auto`).
- **Pembagian Layar Split-Screen 50:50 pada Desktop (>= 1024px)**:
  1. **Panel Kiri (Branding & Visual, lebar 50% / 720px pada 1440px)**:
     - **Latar Belakang**: Foto kantor asli Dinas Kominfo Kabupaten Sleman (`auth_office_bg.png` diekstrak langsung dari aset Figma `images/65939a953f98f308afd0523c80318867d5d52928`) dengan lapisan **Blue Gradient Overlay** semi-transparan (`linear-gradient(135deg, rgba(7, 81, 255, 0.90) 0%, rgba(6, 65, 204, 0.95) 100%)`).
     - **Header Visual**: Logo MagKom putih dengan teks pendamping:
       - `MAGKOM` · `DINAS KOMINFO KABUPATEN SLEMAN`
     - **Area Headline Tengah**:
       - Judul: `DINAS KOMUNIKASI DAN INFORMATIKA`
       - Deskripsi: `Platform digital terintegrasi untuk administrasi, pemantauan, dan penilaian program magang.`
     - **Status Badge / Pill**:
       - Pill bulat transparan dengan titik status hijau (`bg-emerald-400`): *"Sistem Terintegrasi Khusus Internal & Calon Peserta Magang"*.
     - **Footer Panel Visual**: Hak Cipta Diskominfo Sleman.
  2. **Panel Kanan (Area Form & Interaksi, lebar 50% / 720px)**:
     - Background putih bersih (`#FFFFFF`) atau netral sangat lembut (`#F8FAFC`).
     - Area konten terpusat (`max-w-[480px] w-full mx-auto px-6 py-10`).
     - Tautan navigasi balik di sudut atas: `← Kembali ke beranda` menuju `route('landing')`.
     - Slot konten form dinamis via `@yield('content')`.

### 3.2. Penyelarasan Form Login (`resources/views/auth/login.blade.php`)
Mengadopsi elemen dan micro-copy persis Figma `Form Login` (Node `562-56045`) dengan penyesuaian label tombol:
- **Header Form**:
  - Judul: `Selamat Datang di MagKom` (Open Sans, Bold 28px, warna Neutral 900 `#0F172A`).
  - Subjudul: `Masuk untuk mengakses sistem magang terpadu Kominfo` (Regular 14px, warna Neutral 500 `#64748B`).
- **Integrasi Google OAuth (Tambahan Permintaan Pengguna)**:
  - Tombol prominen di atas form email: **"Login dengan Google"** (`route('oauth.redirect', 'google')`).
  - Desain tombol: Putih, border Neutral 300, shadow halus, ikon SVG multi-color Google resmi.
  - Divider horizontal: Garis abu-abu tipis dengan teks tengah *"atau login dengan email"*.
- **Field Email**:
  - Label: `Email` (SemiBold 14px, Neutral 700).
  - Input: Placeholder `nama@email.com`, ikon SVG amplop di sisi kiri, tinggi 48px, border Neutral 300, focus ring Blue 600.
- **Field Kata Sandi**:
  - Label: `Kata Sandi`.
  - Input: Password dengan ikon gembok di kiri dan toggle *Show/Hide Password* (ikon mata) di kanan.
- **Opsi Tambahan**:
  - Baris sejajar: Checkbox `Ingat saya` di sisi kiri dan tautan `Lupa Kata Sandi?` (`route('password.request')`) di sisi kanan warna Blue 600.
- **Keamanan CAPTCHA**:
  - Cloudflare Turnstile box (jika diaktifkan di konfigurasi).
- **Tombol Submit Utama**:
  - Tombol solid: **"Login"** (Blue 600 `#0751FF`, hover Blue 700 `#0641CC`, tinggi 48px, radius 8px, font SemiBold 15px).
- **Footer Tautan**:
  - Teks: `Belum punya akun magang?` dengan tautan tebal **"Daftar"** menuju `route('register')`.

### 3.3. Penyelarasan Form Daftar (`resources/views/auth/register.blade.php`)
Mengadopsi elemen persis Figma `Form Daftar` (Node `562-56045`) dengan penyesuaian label tombol:
- **Header Form**:
  - Judul: `Buat Akun MagKom` (Bold 28px, Neutral 900).
  - Subjudul: `Daftarkan akun Anda untuk memulai proses seleksi magang di Dinas Kominfo.` (Regular 14px, Neutral 500).
- **Step / Section Badge**:
  - Badge pill: `LANGKAH 01` · `Kredensial Akun` (warna Blue 50 latar Blue 50 text Blue 700).
- **Integrasi Google OAuth (Tambahan Permintaan Pengguna)**:
  - Tombol: **"Daftar dengan Google"** (`route('oauth.redirect', 'google')`).
  - Divider horizontal: *"atau daftar dengan email"*.
- **Field Input**:
  - `Nama Lengkap`: Placeholder `Contoh: Aldi Artha`, ikon profil di sisi kiri.
  - `Alamat Email Aktif`: Placeholder `nama@email.com`, ikon email di kiri, dengan teks bantuan (*hint*): *"Gunakan email aktif untuk verifikasi berkas dan notifikasi seleksi"*.
  - `Kata Sandi`: Input password dengan toggle visibility + indikator minimal 8 karakter.
  - `Konfirmasi Kata Sandi`: Input password confirmation.
- **Persetujuan Kebijakan Privasi**:
  - Checkbox: `Saya menyetujui Kebijakan Privasi Dinas Kominfo Sleman` dengan tautan ke `route('kebijakan-privasi')`.
- **Tombol Submit Utama**:
  - Tombol solid: **"Daftar"** (Blue 600, tinggi 48px, radius 8px, font SemiBold 15px).
- **Footer Tautan**:
  - Teks: `Sudah punya akun?` dengan tautan tebal **"Login"** menuju `route('login')`.

### 3.4. Responsivitas Mobile & Tablet (< 1024px & 360px)
- Pada layar tablet & ponsel:
  - Panel kiri visual disembunyikan (`hidden lg:flex`) demi efisiensi ruang dan kecepatan memuat.
  - Panel kanan menjadi 100% lebar viewport dengan `min-h-screen`, menyajikan compact brand header di bagian atas form (Logo MagKom & Sleman).
  - Target sentuh tombol dan field form minimal 44px (sesuai WCAG 2.1 AA & PRD 9.5).
  - Font-size pada input text dijaga minimal 16px (1rem) untuk mencegah Safari iOS melakukan zoom-in otomatis saat keyboard virtual muncul.

---

## 4. Rincian Perubahan File

| No | File Path | Aksi | Ringkasan Perubahan |
|---|---|---|---|
| 1 | `public/images/auth_office_bg.png` | **Create** | Menyalin foto autentik gedung kantor Diskominfo Sleman dari arsip Figma (`scratch/asset_login_65939a95.png`) ke public folder |
| 2 | `resources/views/layouts/auth.blade.php` | **Create** | Membuat master layout auth split-screen 50:50 dengan panel kiri foto kantor + blue gradient overlay dan panel kanan form dinamis |
| 3 | `resources/views/auth/login.blade.php` | **Modify** | Merombak tampilan login: mengadopsi layout auth split-screen, micro-copy Figma, tombol Google OAuth resmi, Turnstile, show/hide password, dan alert error |
| 4 | `resources/views/auth/register.blade.php` | **Modify** | Merombak tampilan daftar: mengadopsi layout auth split-screen, step badge, hint email aktif, tombol Daftar dengan Google, checkbox privasi, dan validasi |
| 5 | `docs/plans/2026-10-09-auth-login-register-redesign.md` | **Create** | Dokumen rencana implementasi ini |

---

## 5. Langkah-Langkah Implementasi

- [x] **Persiapan Aset Visual**:
  - Salin file gambar autentik resolusi tinggi dari `scratch/asset_login_65939a95.png` ke `public/images/auth_office_bg.png`.
- [x] **Pembuatan Layout Auth (`layouts/auth.blade.php`)**:
  - Susun kerangka Blade dengan standar HTML5 semantik, tag `<title>` dinamis, dan meta description.
  - Implementasikan CSS split-screen responsive (grid 2 kolom pada desktop `lg:grid-cols-2`, 1 kolom pada ponsel).
  - Buat panel visual kiri dengan background foto kantor, gradient overlay token Blue 600 / Blue 700, logo Sleman & MagKom, headline, dan status pill.
  - Siapkan container panel kanan dengan tautan *Kembali ke beranda* dan slot `@yield('content')`.
- [x] **Penyelarasan Tampilan Login (`auth/login.blade.php`)**:
  - Ubah inheritance menjadi `@extends('layouts.auth')`.
  - Pasang header form Figma (*Selamat Datang di MagKom*).
  - Letakkan tombol *Login dengan Google* dengan logo SVG Google resmi dan styling clean.
  - Buat divider teks *"atau login dengan email"*.
  - Pasang form email, password (dengan toggle mata), checkbox *Ingat saya*, dan link *Lupa Kata Sandi?*.
  - Pertahankan integrasi Cloudflare Turnstile dan alert error session.
  - Buat tombol submit utama solid: **"Login"**.
  - Buat tautan footer: *"Belum punya akun magang? Daftar"*.
- [x] **Penyelarasan Tampilan Register (`auth/register.blade.php`)**:
  - Ubah inheritance menjadi `@extends('layouts.auth')`.
  - Pasang header form Figma (*Buat Akun MagKom*) dan badge *LANGKAH 01 · Kredensial Akun*.
  - Letakkan tombol *Daftar dengan Google* dan divider.
  - Pasang field *Nama Lengkap*, *Alamat Email Aktif* (dengan hint verifikasi), *Kata Sandi*, dan *Konfirmasi Kata Sandi*.
  - Tambahkan checkbox persetujuan kebijakan privasi dengan tautan ke halaman Kebijakan Privasi.
  - Pasang tombol submit utama solid: **"Daftar"**.
  - Buat tautan footer: *"Sudah punya akun? Login"*.
- [x] **Verifikasi Browser & Quality Assurance**:
  - Jalankan automated test suite `php artisan test` untuk memastikan seluruh alur registrasi, login, dan socialite Google tetap 100% hijau.
  - Buka browser pada desktop (1440x900) dan mobile (375x667) untuk memverifikasi keselarasan visual, kontras warna WCAG AA, dan fungsionalitas input.

---

## 6. Strategi Pengujian (Testing Strategy)

1. **Automated Unit & Feature Tests**:
   - `tests/Feature/ReleaseThreeSocialLoginTest.php`: Memastikan endpoint `oauth.redirect` dan callback Google OAuth tetap berfungsi tanpa regresi.
   - `tests/Feature/AuthTest.php` / test autentikasi: Memastikan validasi email unik, konfirmasi password, Turnstile, dan hashing password tetap valid.
2. **Pengujian Tampilan & Responsivitas**:
   - Desktop 1440px: Memverifikasi rasio 50:50, ketajaman foto kantor, gradient overlay, dan jarak antar elemen form.
   - Laptop 1024px: Memverifikasi adaptasi layout split-screen tetap proporsional.
   - Mobile 375px - 360px: Memverifikasi panel kiri tersembunyi dengan mulus, form terisi penuh dan nyaman dibaca, serta tombol sentuh memenuhi batas min 44px.
3. **Pengujian Interaktivitas**:
   - Toggle *Show/Hide Password* berfungsi lancar tanpa JavaScript eksternal yang melanggar CSP.
   - Tautan balik *Kembali ke beranda* mengarahkan dengan benar ke landing page.
   - Tautan *Lupa Kata Sandi?* mengarahkan ke form pemulihan password.
   - Klik tombol Google OAuth mengarahkan ke URL redirect Google.

---

## 7. Analisis Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| **CSP Violation (Content Security Policy)** | Browser memblokir inline style atau skrip show/hide password jika inline | Gunakan kelas utilitas CSS murni di file stylesheet/style tag layout, hindari atribut `style=""` inline berlebihan sesuai aturan TDD 8.3 |
| **Ukuran Gambar Background Panel Kiri** | Memuat foto besar (1.9 MB) dapat memperlambat rendering | Gunakan background-size cover dengan optimasi kompresi web, atau versi webp/jpg teroptimasi agar load time < 200ms |
| **Auto-zoom di Browser Safari Mobile** | Input font < 16px menyebabkan iOS melakukan zoom otomatis yang mengacaukan layout | Pastikan ukuran font input di mobile minimal 16px (`text-base` / `1rem`) |
| **Turnstile Gagal Load di Lingkungan Lokal** | Form tertahan jika Turnstile dimatikan atau tanpa key lokal | Kondisional `@if(config('services.turnstile.enabled'))` tetap dipertahankan sehingga tidak menghambat pengujian lokal |
| **Kontras Teks pada Gradient Panel Kiri** | Teks sulit dibaca jika kontras background rendah | Gunakan lapisan dark overlay semi-transparan ekstra pada foto sehingga teks putih memiliki rasio kontras > 7:1 (melebihi WCAG AAA) |
