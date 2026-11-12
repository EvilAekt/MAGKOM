# Plan & Audit: Implementasi Fitur Forgot & Reset Password (MAGKOM)

> **Dokumen Panduan Teknis, Audit Autentikasi, & Rencana Kerja (Senior Developer Plan)**  
> **Target Repositori**: MAGKOM (Portal Manajemen Magang Dinas Kominfo)  
> **Modul Terkait**: Alur Pemulihan Akun (`/forgot-password`, `/reset-password`, `ResetPasswordNotification`, `ResetPasswordController`)  
> **Tingkat Prioritas**: P0 (Fitur Utama Akun Mahasiswa Magang)

---

## 1. 🔍 PHASE 1 & 2: Audit Project & Analisis Autentikasi (Task 0)

Berdasarkan audit mendalam terhadap seluruh struktur file dan konfigurasi repositori MAGKOM:

| Aspek yang Diaudit | Hasil Pemeriksaan Aktual Repositori | Keterangan / Implikasi Teknis |
| :--- | :--- | :--- |
| **Versi Laravel & PHP** | **Laravel 10.x** (`laravel/framework: ^10.0`), **PHP 8.3.x** (`php: ^8.3`) | Menggunakan API standar Laravel 10 native. Tidak boleh menggunakan deprecated syntax. |
| **Model Akun Mahasiswa** | `App\Models\User` (`role = RoleEnum::Peserta`) | Akun mahasiswa/peserta magang menggunakan model `User` terpusat, dengan data profil tambahan tersimpan di relasi `PesertaProfil` (`user_id`). |
| **Tabel Akun Mahasiswa** | Tabel `users` di database MySQL | Kolom identitas: `email` (string, unique), `password` (hashed). |
| **Auth Provider & Guards** | `config/auth.php` &rarr; Guard `web` (driver session, provider `users`) | Provider `users` menggunakan driver `eloquent` yang mengarah ke `App\Models\User::class`. |
| **Password Broker** | `config/auth.php` &rarr; `passwords.users` | Tabel token: `password_reset_tokens`, durasi berlaku: `expire => 60` menit, throttle: `60` detik. Sesuai 100% dengan kebutuhan TASK 2. |
| **Tabel Token Reset** | Tabel `password_reset_tokens` | Migration `2014_10_12_100000_create_password_reset_tokens_table.php` sudah ada di database (`email` [primary], `token`, `created_at`). **Tidak perlu membuat tabel/migration baru**. |
| **Rute Autentikasi Reset** | `routes/web.php` baris 101-105: `password.request`, `password.email`, `password.reset`, `password.update` | Rute sudah dideklarasikan mengarah ke `ResetPasswordController::class`. |
| **Controller Eksisting** | `app/Http/Controllers/Auth/ResetPasswordController.php` | Sudah ada fondasi `Password::broker()`, namun view-nya masih prototype, belum terhubung dengan custom HTML email notifikasi, dan validasi perlu disempurnakan. |
| **Desain Antarmuka Auth** | Layout `layouts.auth-depth.blade.php` | Menggunakan canvas interaktif 3D, panel kiri informasi magang (`_left_panel.blade.php`), dan panel formulir kanan yang modern dan responsif. |
| **Konfigurasi Mail / SMTP** | `config/mail.php` & `.env.example` | Mendukung driver `smtp`, `log`, `array`. Di `.env.example` sudah disiapkan template dasar yang perlu didokumentasikan untuk Google App Password. |

---

## 2. 🏗️ Evaluasi Opsi Pendekatan Arsitektur

| Kriteria | Opsi 1: Laravel Native Password Broker + Custom HTML Notification (DIREKOMENDASIKAN) | Opsi 2: Custom Token Generator Manual di Database Sendiri |
| :--- | :--- | :--- |
| **Mekanisme** | Memanfaatkan `Illuminate\Support\Facades\Password::broker()` bawaan framework yang mengelola hashing token, TTL 60 menit, single-use anti-replay, dan pengiriman notifikasi via custom `ResetPasswordNotification` (Blade HTML template). | Membuat tabel kustom, kolom token sendiri, dan controller manual tanpa broker framework. |
| **Keamanan** | **Standar Industri Tinggi**: Token di-hash menggunakan algoritma HMAC SHA-256 bawaan framework, dilindungi dari timing attacks, dan otomatis di-invalidate setelah digunakan. | Berisiko tinggi terhadap celah keamanan (token reuse, timing attack, race condition). |
| **Maintainability** | **Sangat Bersih**: Mengikuti konvensi resmi Laravel 10, zero duplication, mudah di-test dengan `Notification::fake()`. | Rumit dan menyulitkan pemeliharaan jangka panjang. |
| **Rekomendasi Senior Dev** | **SANGAT DIREKOMENDASIKAN (OPSI 1)**. | DITOLAK. |

---

## 3. 📁 Rencana Perubahan & Pembuatan Berkas (File Plan)

| Berkas | Aksi | Deskripsi Perubahan |
| :--- | :---: | :--- |
| `app/Notifications/ResetPasswordNotification.php` | **Create** | Custom Notification Laravel yang merender email HTML bertema MAGKOM dengan link reset bertoken, masa berlaku 60 menit, dan subjek resmi. |
| `resources/views/emails/reset-password.blade.php` | **Create** | Template email HTML profesional responsif dengan inline CSS, palet warna MAGKOM (`#003566`, `#FFC300`), fallback plain URL, dan pesan keamanan. |
| `app/Models/User.php` | **Modify** | Menambahkan override method `sendPasswordResetNotification($token)` untuk memicu `ResetPasswordNotification`. |
| `app/Http/Controllers/Auth/ResetPasswordController.php` | **Modify** | Memperkuat validasi email RFC, penanganan seragam anti-enumeration, pembatasan ketat hanya untuk role Peserta/Mahasiswa, dan validasi password baru (min 8 karakter kombinasi huruf & angka). |
| `resources/views/auth/forgot-password.blade.php` | **Modify** | Menyelaraskan UI Forgot Password dengan layout `auth-depth`, input email bersih, pesan feedback status sukses, dan link kembali ke login. |
| `resources/views/auth/reset-password.blade.php` | **Modify** | Memodernisasi tampilan Reset Password menggunakan layout `auth-depth`, toggle show/hide password, indikator validasi, dan feedback error. |
| `.env.example` | **Modify** | Melengkapi dokumentasi konfigurasi SMTP (Gmail App Password / Mailpit / Log) yang aman tanpa credential nyata. |
| `tests/Feature/AuthForgotPasswordAndResetTest.php` | **Create** | Test suite otomatis komprehensif menguji seluruh alur: forgot password, pengiriman email via `Notification::fake()`, token expiry, reuse rejection, dan login password baru. |

---

## 4. 📝 Spesifikasi Detail Implementasi (Task Breakdown)

### 4.1 Task 1: UI Forgot Password (`resources/views/auth/forgot-password.blade.php`)
- Menggunakan layout `layouts.auth-depth` (`['variant' => 'forgot']`).
- Judul: "Lupa Password?".
- Deskripsi: *"Masukkan alamat email yang terdaftar pada akun Anda. Kami akan mengirimkan tautan untuk mengatur ulang password."*
- Input email: type="email", autofocus, autocomplete="email", placeholder="contoh: nama.peserta@gmail.com".
- Alert feedback: Hijau jika berhasil (`session('status')`), Merah jika validasi gagal (`$errors->first()`).
- Tombol: *"Kirim Tautan Reset"*.
- Link: *"Kembali ke Login"*.

### 4.2 Task 2 & 7: Backend Password Broker & Security (`ResetPasswordController.php`)
- `sendResetLink(Request $request)`:
  - Validasi email RFC: `['required', 'string', 'email:rfc']`.
  - Rate limiting pada route: `throttle:5,1` (maks 5 request per menit per IP).
  - Anti-User-Enumeration: Respons selalu seragam: *"Jika alamat email terdaftar pada sistem, kami akan mengirimkan instruksi untuk mengatur ulang password."*
  - Otorisasi akun: Hanya kirim token jika user aktif dan `role === RoleEnum::Peserta`.
- `reset(Request $request)`:
  - Validasi password: `['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-zA-Z])(?=.*\d)/']`.
  - Hashing password: `Hash::make($password)`.
  - Token invalidation otomatis setelah password disimpan.
  - Redirect ke login dengan pesan sukses.

### 4.3 Task 3: UI Reset Password (`resources/views/auth/reset-password.blade.php`)
- Layout `layouts.auth-depth` (`['variant' => 'reset']`).
- Judul: "Buat Password Baru".
- Deskripsi: *"Silakan masukkan password baru untuk akun Sistem Manajemen Magang Diskominfo Anda."*
- Form fields:
  - Token (hidden)
  - Email (readonly/hidden)
  - Password Baru (dengan toggle show/hide `data-action="toggle-password"`)
  - Konfirmasi Password Baru (dengan toggle show/hide)
- Indikator: *"Minimal 8 karakter kombinasi huruf dan angka."*
- Tombol: *"Simpan Password Baru"*.

### 4.4 Task 4: Template Email HTML Profesional (`resources/views/emails/reset-password.blade.php`)
- Menggunakan layout email card berstandar industri dengan inline CSS (kompatibel 100% dengan Gmail, Apple Mail, Outlook).
- Header: Logo / Tulisan DISKOMINFO • Sistem Manajemen Magang (MAGKOM) dengan warna `#003566` dan aksen garis `#FFC300`.
- Body:
  - Sapaan: *"Halo, Mahasiswa Magang!"*
  - Isi: *"Kami menerima permintaan untuk mengatur ulang password akun Sistem Manajemen Magang Diskominfo Anda."*
  - Call to Action: Tombol tombol utama *"RESET PASSWORD"* dengan URL absolut yang valid.
  - Keterangan masa berlaku: *"Tautan reset password ini berlaku selama 60 menit. Demi keamanan akun, tautan hanya dapat digunakan satu kali."*
  - Peringatan: *"Jika Anda tidak merasa meminta pengaturan ulang password, abaikan email ini. Password akun Anda tidak akan berubah."*
  - Fallback: Link teks cadangan jika tombol tidak dapat diklik.
- Footer: *"Sistem Manajemen Magang Diskominfo"* & *"Email ini dikirim secara otomatis. Mohon tidak membalas email ini."*

### 4.5 Task 5: Panduan Konfigurasi SMTP
- Dokumentasi setting `.env`:
  ```ini
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.gmail.com
  MAIL_PORT=587
  MAIL_USERNAME=your_email@gmail.com
  MAIL_PASSWORD=your_google_app_password
  MAIL_ENCRYPTION=tls
  MAIL_FROM_ADDRESS=your_email@gmail.com
  MAIL_FROM_NAME="Sistem Magang Diskominfo (MAGKOM)"
  ```
- Panduan pembuatan 16 karakter Google App Password (2-Step Verification).

---

## 5. 📋 Checklist Langkah Kerja Bertahap (Implementation Steps)

### Fase 1: Backend Notification & Mail Template
- [x] **Langkah 1.1**: Buat `app/Notifications/ResetPasswordNotification.php`.
- [x] **Langkah 1.2**: Buat template email `resources/views/emails/reset-password.blade.php`.
- [x] **Langkah 1.3**: Tambahkan override `sendPasswordResetNotification($token)` pada `app/Models/User.php`.

### Fase 2: Controller & Validasi Backend
- [x] **Langkah 2.1**: Perbarui `app/Http/Controllers/Auth/ResetPasswordController.php` (`sendResetLink` & `reset`).
- [x] **Langkah 2.2**: Pastikan audit log mencatat aksi reset dengan detail IP.

### Fase 3: Antarmuka UI (Forgot & Reset Password)
- [x] **Langkah 3.1**: Perbarui `resources/views/auth/forgot-password.blade.php` agar selaras dengan `auth-depth`.
- [x] **Langkah 3.2**: Perbarui `resources/views/auth/reset-password.blade.php` menggunakan layout `auth-depth` dan toggle password.
- [x] **Langkah 3.3**: Pastikan link "Lupa kata sandi?" di `resources/views/auth/login.blade.php` terhubung dengan benar ke `route('password.request')`.

### Fase 4: Konfigurasi & Dokumentasi
- [x] **Langkah 4.1**: Perbarui `.env.example` dengan dokumentasi SMTP.

### Fase 5: Pengujian Otomatis & Quality Gates
- [x] **Langkah 5.1**: Buat test suite `tests/Feature/AuthForgotPasswordAndResetTest.php`.
- [x] **Langkah 5.2**: Jalankan pengujian: `php artisan test tests/Feature/AuthForgotPasswordAndResetTest.php`.
- [x] **Langkah 5.3**: Jalankan seluruh security test suite untuk memastikan regression test 100% lulus.
- [x] **Langkah 5.4**: Jalankan `./vendor/bin/pint --test` dan `./vendor/bin/phpstan analyse`.
- [x] **Langkah 5.5**: Jalankan `npm run build`.

---

## 6. 🏁 Kriteria Selesai (Definition of Done / DoD)

- [x] Link "Lupa Password?" di `/login` mengarah ke `/forgot-password`.
- [x] Halaman `/forgot-password` memvalidasi format email dan menampilkan pesan konfirmasi seragam.
- [x] Email notifikasi HTML profesional dikirimkan dengan identitas visual MAGKOM.
- [x] Tautan dalam email membawa token valid dan mengarah ke `/reset-password/{token}`.
- [x] Token reset hanya berlaku selama 60 menit dan kedaluwarsa setelah dipakai satu kali.
- [x] Formulir `/reset-password/{token}` memvalidasi kata sandi baru (min 8 karakter kombinasi huruf & angka) serta konfirmasi.
- [x] Kata sandi baru disimpan secara terenkripsi (`Hash::make`) di database.
- [x] Pengguna berhasil login dengan kata sandi baru, dan gagal jika menggunakan kata sandi lama.
- [x] Seluruh automated test lulus 100% tanpa regresi.
- [x] Linter Pint, static analysis PHPStan, dan build Vite lulus tanpa error.
