# Plan: Zero-Page Reload & Full Input Retention Authentication Flow

**Tanggal:** 25 November 2026  
**Status:** DRAFT - Menunggu Persetujuan Pengguna  
**Ruang Lingkup:** Seluruh modul Otentikasi dan Manajemen Sandi (Superadmin, Admin, Peserta, Pembimbing)  
**Referensi Aturan:** `AGENTS.md` (PHP 8.3, Laravel 10, Vanilla ES Modules, Vite, Tanpa Script/CSS inline di Blade)

---

## 1. Ringkasan Eksekutif & Tujuan
Mengeliminasi *page reload* (full refresh) dan pencegahan *reset input* (kehilangan input yang sudah diketik) pada seluruh formulir otentikasi dan manajemen kata sandi di sistem MagKom. Ketika pengguna melakukan kesalahan pengetikan sandi, format email, atau konfirmasi sandi, antarmuka tetap mempertahankan seluruh nilai form secara utuh, menampilkan pesan validasi dinamis tepat di bawah input terkait atau alert container, memperbarui gambar CAPTCHA (jika ada), serta memberikan indikator *loading state* pada tombol submit guna mencegah *double submission*.

---

## 2. Matriks Audit Form & Endpoint

| No | Modul / Form | Role Terkait | Route / Action | Controller Target | View Terkait | Script JS Pengelola |
|---|---|---|---|---|---|---|
| 1 | **Login** | Semua Role | `POST /login` | `Auth\AuthController@login` | `auth/login.blade.php` | `resources/js/pages/auth/auth-depth.js` |
| 2 | **Otentikasi 2FA (TOTP)** | Semua Role (aktif 2FA) | `POST /two-factor-challenge` | `Auth\TwoFactorAuthController@verifyChallenge` | `auth/two-factor-challenge.blade.php` | `resources/js/pages/auth/auth-depth.js` |
| 3 | **Registrasi Akun** | Peserta (Individu & Kelompok) | `POST /register` | `Auth\AuthController@register` | `auth/register.blade.php` | `resources/js/pages/auth/auth-depth.js` |
| 4 | **Lupa Password** | Peserta / Mahasiswa | `POST /forgot-password` | `Auth\ResetPasswordController@sendResetLink` | `auth/forgot-password.blade.php` | `resources/js/pages/auth/auth-depth.js` |
| 5 | **Reset Password (Token)** | Peserta / Mahasiswa | `POST /reset-password` | `Auth\ResetPasswordController@reset` | `auth/reset-password.blade.php` | `resources/js/pages/auth/auth-depth.js` |
| 6 | **Ganti Sandi Wajib Staf** | Admin, Pembimbing, Superadmin | `POST /password/ganti` | `Auth\PasswordController@updateGanti` | `auth/ganti-password.blade.php` | `resources/js/pages/auth/auth-depth.js` |
| 7 | **Ubah Sandi Admin** | Admin | `PUT /admin/pengaturan/password` | `Admin\PengaturanController@updatePassword` | `admin/pengaturan/index.blade.php` | `resources/js/components/pengaturan-akun.js` |
| 8 | **Ubah Sandi Pembimbing** | Pembimbing | `PUT /pembimbing/pengaturan/password` | `Pembimbing\PengaturanController@updatePassword` | `pembimbing/pengaturan/index.blade.php` | `resources/js/components/pengaturan-akun.js` |
| 9 | **Ubah Sandi Peserta** | Peserta | `PUT /peserta/pengaturan/password` | `Peserta\PengaturanController@updatePassword` | `peserta/pengaturan/index.blade.php` | `resources/js/components/pengaturan-akun.js` |
| 10 | **Ubah Sandi Superadmin** | SuperAdmin | `PUT /superadmin/akun/password` | `SuperAdmin\AkunController@updatePassword` | `superadmin/akun/index.blade.php` | `resources/js/components/pengaturan-akun.js` |

---

## 3. Spesifikasi Arsitektur & Perubahan Teknis

### A. Backend Controller (Standarisasi Response JSON API)
Semua controller yang menangani submit otentikasi/kata sandi harus memeriksa `$request->expectsJson()`:
- **Respon Sukses (HTTP 200):**
  ```json
  {
    "success": true,
    "message": "Pesan sukses pengguna",
    "redirect": "/url-tujuan-dashboard-atau-login"
  }
  ```
- **Respon Validasi Gagal (HTTP 422):**
  ```json
  {
    "success": false,
    "message": "Ringkasan kesalahan validasi",
    "errors": {
      "field_name": ["Pesan spesifik untuk input ini"]
    }
  }
  ```
- **Respon Proteksi Lockout / Rate Limit (HTTP 429):**
  ```json
  {
    "success": false,
    "locked": true,
    "lockout_seconds": 60,
    "message": "Akses akun dibekukan sementara karena terlalu banyak percobaan gagal."
  }
  ```
- **Fallback Non-AJAX:** Tetap dipertahankan (100% backward-compatible untuk automated PHPUnit tests dan browser non-JS).

### B. Frontend JavaScript (Modular ES Modules)
1. **`resources/js/pages/auth/auth-depth.js`**:
   - `initAjaxLoginForm()`: Memperkuat integrasi penanganan login asinkron tanpa mereset input kata sandi saat gagal, refresh CAPTCHA otomatis, dan countdown lockout dinamis.
   - `initAjaxRegisterForm()`: Menangani submit form registrasi via AJAX fetch, mempertahankan nilai input semua field (termasuk kata sandi ketua & anggota), merender error inline per field dan alert container.
   - `initAjaxForgotPasswordForm()`: Mengirim tautan lupa password asinkron dan menampilkan notifikasi status tanpa reload.
   - `initAjaxResetPasswordForm()`: Mengirim pembuatan kata sandi baru dari token email via asinkron, menjaga nilai input sandi bila validasi regex/min karakter gagal.
   - `initAjaxGantiPasswordForm()`: Memproses ganti sandi staf pertama kali via AJAX fetch, mempertahankan kata sandi jika ada kesalahan konfirmasi atau kata sandi lama tidak cocok.
   - `initAjaxTwoFactorForm()`: Mengirim verifikasi 6-digit TOTP / kode recovery via AJAX tanpa reload.
2. **`resources/js/components/pengaturan-akun.js`**:
   - `initAjaxPasswordUpdateForm()`: Mencegat submit form `#passwordForm` (di Admin, Pembimbing, Peserta, Superadmin):
     - Mencegah default submit (`e.preventDefault()`).
     - State loading tombol: teks `"Menyimpan..."` dan `disabled = true`.
     - Mengirim request `PUT` dengan header `Accept: application/json` dan `X-CSRF-TOKEN`.
     - **Saat Gagal (HTTP 422):** Pertahankan seluruh isi field (`current_password`, `password`, `password_confirmation`), tandai input dengan border merah / kelas `is-invalid`, dan tampilkan teks error inline di bawah field yang salah.
     - **Saat Berhasil (HTTP 200):** Tampilkan pesan sukses (toast atau alert), kosongkan field hanya saat sukses (`form.reset()`), kembalikan tombol ke keadaan semula.
3. **Show/Hide Password Toggle Verification**:
   - Memastikan tombol toggle mata berfungsi pada semua field sandi (`current_password`, `password`, `password_confirmation`, serta sandi rekan anggota kelompok).

### C. Layout Blade
- **`resources/views/layouts/auth-depth.blade.php`**: Menambahkan tag `<meta name="csrf-token" content="{{ csrf_token() }}">` di dalam `<head>` agar konsisten dengan `layouts/dashboard.blade.php`.

---

## 4. Daftar File yang Akan Dimodifikasi

### Backend:
1. `app/Http/Controllers/Auth/AuthController.php` (tambahkan penanganan JsonResponse pada `register()`, verifikasi konsistensi JSON pada `login()`)
2. `app/Http/Controllers/Auth/ResetPasswordController.php` (tambahkan penanganan JsonResponse pada `sendResetLink()` dan `reset()`)
3. `app/Http/Controllers/Auth/PasswordController.php` (tambahkan penanganan JsonResponse pada `updateGanti()`)
4. `app/Http/Controllers/Auth/TwoFactorAuthController.php` (tambahkan penanganan JsonResponse pada `verifyChallenge()`)
5. `app/Http/Controllers/Admin/PengaturanController.php` (tambahkan penanganan JsonResponse pada `updatePassword()`)
6. `app/Http/Controllers/Pembimbing/PengaturanController.php` (tambahkan penanganan JsonResponse pada `updatePassword()`)
7. `app/Http/Controllers/Peserta/PengaturanController.php` (tambahkan penanganan JsonResponse pada `updatePassword()`)
8. `app/Http/Controllers/SuperAdmin/AkunController.php` (tambahkan penanganan JsonResponse pada `updatePassword()`)

### Frontend:
9. `resources/js/pages/auth/auth-depth.js` (implementasi AJAX submit & input retention untuk Register, Forgot Password, Reset Password, Ganti Password, dan 2FA Challenge)
10. `resources/js/components/pengaturan-akun.js` (implementasi AJAX submit & input retention untuk Form Ganti Password Profil semua role)

### Markup / Layout (Minimal & Hanya Meta CSRF):
11. `resources/views/layouts/auth-depth.blade.php` (penambahan `<meta name="csrf-token">`)

---

## 5. Rencana Pengujian (Quality Gate & Verification)
1. **Verifikasi Linter & Static Analysis**:
   - `./vendor/bin/pint --test`
   - `./vendor/bin/phpstan analyse --memory-limit=1G`
2. **Kompilasi Aset Frontend**:
   - `npm run build`
3. **Pengujian Fungsional & Unit/Feature Tests**:
   - `php artisan test tests/Feature/Auth/ tests/Feature/PasswordTest.php`
4. **Skenario Uji Interaksi (Manual / Browser Verification)**:
   - Form Login: submit dengan kata sandi salah -> halaman tidak reload, email dan password tetap berada di field, alert error muncul, CAPTCHA berganti.
   - Form Register: submit dengan password tidak cocok -> halaman tidak reload, seluruh field (nama, email, nomor HP, password) tetap terisi, error inline muncul.
   - Form Ganti Sandi (Admin/Peserta/Pembimbing/Superadmin): submit dengan sandi lama salah -> halaman tidak reload, sandi tidak terhapus, pesan error tampil di bawah input `current_password`.

