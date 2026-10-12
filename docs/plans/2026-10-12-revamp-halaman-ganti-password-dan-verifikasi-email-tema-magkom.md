# Rencana Kerja: Revamp Tampilan Halaman Ganti Kata Sandi & Verifikasi Email Sesuai Tema MagKom Sleman

## 1. Ringkasan Masalah & Kebutuhan
Berdasarkan tangkapan layar yang dikirimkan oleh pengguna:
1. Halaman **Ganti Kata Sandi** (`/password/ganti`) dan **Verifikasi Email** (`/email/verify`) saat ini menggunakan styling mandiri *dark mode* pekat (`#0f172a` / `#1e293b`) dengan tampilan terminal/cyber gelap yang berbanding terbalik dari tema utama MagKom Sleman.
2. Identitas visual resmi Portal MagKom Pemkab Sleman mengusung tema **Clean, Modern & Professional Government Design** dengan latar belakang bersih berpadu biru resmi Pemkab Sleman (`--blue-600: #0751FF`), panel visual branding Pemkab Sleman di sebelah kiri, form card elegan di sebelah kanan, serta komponen input yang serasi (seperti pada halaman Login dan Register).
3. Selain kedua halaman utama tersebut, halaman auth terkait seperti **Lupa Kata Sandi** (`/forgot-password`) dan **Reset Kata Sandi** (`/reset-password`) juga masih menggunakan template dark mode usang yang serupa dan perlu diselaraskan agar seluruh alur autentikasi MagKom konsisten 100%.

---

## 2. Analisis Arsitektur & Solusi Desain

### A. Pondasi Layout Induk (`resources/views/layouts/auth.blade.php`)
Portal MagKom telah memiliki master layout auth resmi yang sangat komprehensif:
- **Split-Screen Layout**:
  - **Panel Kiri (Desktop)**: Foto kantor instansi resmi, lencana status *"Sistem Magang Terpadu Sleman"*, headline *"DINAS KOMUNIKASI DAN INFORMATIKA"*, logo resmi MAGKOM, serta copyright resmi Pemkab Sleman.
  - **Panel Kanan**: Area form interaktif bersih (*clean white*), tombol "Kembali ke beranda", form title & subtitle, validasi alert error/success, dan tombol submit primer.
- **Komponen Input Bertema**:
  - `form-group` dengan `form-label sr-only` atau label eksplisit.
  - `input-wrapper` dengan leading icon SVG di sisi kiri (`.input-icon-left`).
  - Input field `.form-input` dengan border halus dan ring focus biru (`var(--blue-600)`).
  - Tombol interaktif show/hide password (`.toggle-password-btn`) dengan transisi icon mata terbuka/tertutup otomatis tanpa dependensi JavaScript luar.
  - Tombol primer `.btn-primary` dengan hover & active state yang konsisten.

---

## 3. Rincian Revisi File

### 1. `resources/views/auth/ganti-password.blade.php` (Fokus Utama User)
- **Extends**: `@extends('layouts.auth')`
- **Title**: `Ganti Kata Sandi - Portal MagKom Sleman`
- **Komponen**:
  - Title: `<h1 class="auth-title">Ganti Kata Sandi</h1>`
  - Subtitle: `<p class="auth-subtitle">Demi standar keamanan instansi, Anda wajib mengganti kata sandi akun sebelum mengakses fitur portal.</p>`
  - Alert error: `.auth-alert-error` saat validasi tidak lolos.
  - Alert sukses: `.auth-alert-success` jika terdapat flash session.
  - Form action: `route('password.ganti.update')` method `POST` dengan `@csrf`.
  - 3 input field bertema MagKom dengan leading icon gembok & tombol toggle eye (show/hide password):
    1. `current_password` (Kata Sandi Saat Ini)
    2. `password` (Kata Sandi Baru - Min. 8 Karakter, Kombinasi Huruf & Angka)
    3. `password_confirmation` (Konfirmasi Kata Sandi Baru)
  - Tombol submit: `.btn-primary` dengan teks *"Perbarui Kata Sandi & Lanjutkan"*.

### 2. `resources/views/auth/verify-email.blade.php` (Fokus Utama User)
- **Extends**: `@extends('layouts.auth')`
- **Title**: `Verifikasi Alamat Email - Portal MagKom Sleman`
- **Komponen**:
  - Badge Status: `<span class="step-badge">Aktivasi Akun</span>`
  - Title: `<h1 class="auth-title">Verifikasi Alamat Email</h1>`
  - Subtitle / Deskripsi: Paragraf ramah dan profesional yang menginformasikan bahwa tautan verifikasi telah dikirimkan ke email peserta.
  - Alert Info: Menampilkan `.auth-alert-success` ketika `session('status') == 'verification-link-sent'` dengan pesan *"Tautan verifikasi baru telah dikirimkan ke alamat email Anda."*
  - Form Kirim Ulang: Action `route('verification.send')` method `POST` dengan tombol primer `.btn-primary` *"Kirim Ulang Email Verifikasi"*.
  - Form Logout: Action `route('logout')` method `POST` dengan link button elegan *"Keluar dari Akun"*.

### 3. Penyelarasan Menyeluruh (Bonus Konsistensi)
- Merevisi `resources/views/auth/forgot-password.blade.php` dan `resources/views/auth/reset-password.blade.php` agar keduanya juga mengadopsi `@extends('layouts.auth')` sehingga tidak ada lagi sisa halaman berdesain *dark mode* di seluruh portal auth MagKom.

---

## 4. Rencana Verifikasi & Validasi
1. **Verifikasi Browser/HTML**:
   - Memeriksa tampilan `/password/ganti` untuk memastikan layout split-screen MagKom ter-render dengan sempurna, card bersih, input field dan toggle mata berfungsi.
   - Memeriksa tampilan `/email/verify` untuk memastikan instruksi verifikasi email tersaji rapi dan profesional.
2. **Automated Feature Tests**:
   - Menjalankan test suite autentikasi: `php artisan test --filter=CoreFoundationTest` dan test otentikasi terkait untuk memastikan route, validasi password, dan flow session tetap 100% berjalan normal tanpa regresi.

---

## 5. Pertanyaan Konfirmasi Pengguna
Apakah rencana revisi tampilan di atas disetujui untuk mulai diimplementasikan?
