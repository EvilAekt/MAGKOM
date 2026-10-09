# Plan: Relokasi Posisi Tombol Google OAuth ke Bawah Form Login & Register Sesuai Figma (Node 562-56046)

- **Tanggal**: 2026-10-09
- **Status**: Completed (Selesai & Terverifikasi)
- **Author**: Antigravity (web-planning & web-implementation)
- **Rujukan Desain**:
  - Figma Node `562-56046` / `MAGKOM Kominfo Sleman (1).fig` (`Form Login` & `Form Daftar`)
  - Design System MagKom: `docs/07-DESIGN-SYSTEM-MAGKOM.md`
- **Tautan Figma Pengguna**: [Figma Frame Node 562-56046](https://www.figma.com/design/uKH1OZjwBIi8iRoKQXEbQr/MAGKOM-Kominfo-Sleman?node-id=562-56046&t=3bap827WnVQ5b2y4-4)

---

## 1. Ringkasan & Tujuan

Tujuan dari rencana ini adalah mengubah susunan tata letak (*layout order*) pada halaman **Login** (`/login`) dan **Daftar / Registrasi** (`/register`):
1. **Memindahkan Tombol Google OAuth ke Bawah**:
   - Memindahkan tombol **"Login dengan Google"** dan **"Daftar dengan Google"** beserta garis pemisahnya (*divider*) dari posisi atas (di bawah judul) ke posisi **bawah** (tepat di bawah tombol aksi utama `Login` dan `Daftar`).
2. **Menyelaraskan Hierarki Sesuai Desain Asli Figma (Node 562-56046)**:
   - Pada desain Figma asli, fokus pertama pengguna setelah membaca judul sambutan adalah pengisian field formulir kredensial (Email & Kata Sandi), dilanjutkan tombol aksi utama, lalu opsi login alternatif sosial di bagian bawah sebelum tautan beralih akun.
3. **Penyempurnaan Spacing & Input Icon**:
   - Menyelaraskan padding dan posisi ikon pada input formulir agar tidak terlalu berhimpitan dan tampak proporsional sesuai tangkapan layar.

---

## 2. Asumsi & Ketergantungan

- **Integritas Fungsionalitas Backend**:
  - Backend controller `AuthController` dan `SocialiteController` tidak mengalami perubahan logika.
  - Alur submit email-password dan redirect Google OAuth (`route('oauth.redirect', 'google')`) tetap berfungsi normal.
- **Kompatibilitas Testing**:
  - Pengujian fitur `ReleaseThreeSocialLoginTest.php` yang memverifikasi keberadaan tombol Google OAuth di halaman login dan register tetap dipertahankan 100% hijau.
- **Standar Aksesibilitas**:
  - Urutan tabulasi keyboard (fokus accessibility) menjadi lebih alami: Judul -> Form Email -> Password -> Tombol Submit -> Divider -> Tombol Google OAuth -> Link Footer.

---

## 3. Arsitektur & Pendekatan Desain

### 3.1. Rekonfigurasi Hierarki Halaman Login (`resources/views/auth/login.blade.php`)

**Susunan Saat Ini (Atas)**:
1. Header (`Selamat Datang di MagKom`)
2. *[Tombol Google OAuth]*
3. *[Divider "atau login dengan email"]*
4. Input Email
5. Input Kata Sandi
6. Checkbox Ingat Saya & Lupa Kata Sandi
7. Tombol `Login`
8. Footer (`Belum punya akun magang? Daftar`)

**Susunan Baru Sesuai Figma (Bawah)**:
1. Header Form (`Selamat Datang di MagKom` & Subjudul)
2. Alert Notifikasi Flash (Success / Error jika ada)
3. Form Kredensial Langsung:
   - Input Email (dengan ikon amplop)
   - Input Kata Sandi (dengan ikon gembok & toggle mata)
   - Checkbox `Ingat saya` & Tautan `Lupa Kata Sandi?`
   - Turnstile CAPTCHA (kondisional)
   - **Tombol Submit Utama: `Login`** (Solid Blue 600)
4. **Divider Pemisah**:
   - Garis horizontal dengan teks tengah: *"atau login dengan"* / *"atau"*
5. **Tombol Alternatif Google OAuth**:
   - Tombol: **"Login dengan Google"** (Latar putih, border Neutral 300, ikon resmi Google SVG multi-color)
6. Tautan Footer:
   - *"Belum punya akun magang? **Daftar**"*

---

### 3.2. Rekonfigurasi Hierarki Halaman Daftar (`resources/views/auth/register.blade.php`)

**Susunan Saat Ini (Atas)**:
1. Step Badge (`LANGKAH 01 • Kredensial Akun`)
2. Header (`Buat Akun MagKom`)
3. *[Tombol Google OAuth]*
4. *[Divider "atau daftar dengan email"]*
5. Input Nama Lengkap, Email, Password, Konfirmasi
6. Checkbox Kebijakan Privasi
7. Tombol `Daftar`
8. Footer (`Sudah punya akun? Login`)

**Susunan Baru Sesuai Figma (Bawah)**:
1. Step Badge (`LANGKAH 01 • Kredensial Akun`)
2. Header Form (`Buat Akun MagKom` & Subjudul)
3. Form Registrasi Langsung:
   - Input Nama Lengkap
   - Input Alamat Email Aktif (dengan teks hint verifikasi)
   - Input Kata Sandi / Password (dengan toggle mata & hint 8 karakter)
   - Input Konfirmasi Kata Sandi
   - Checkbox Persetujuan Kebijakan Privasi
   - Turnstile CAPTCHA (kondisional)
   - **Tombol Submit Utama: `Daftar`** (Solid Blue 600)
4. **Divider Pemisah**:
   - Garis horizontal dengan teks tengah: *"atau daftar dengan"* / *"atau"*
5. **Tombol Alternatif Google OAuth**:
   - Tombol: **"Daftar dengan Google"**
6. Tautan Footer:
   - *"Sudah punya akun? **Login**"*

---

### 3.3. Penyelarasan Input Spacing & Alignment

Menyesuaikan styling CSS pada [layouts/auth.blade.php](file:///g:/MAGKOM/resources/views/layouts/auth.blade.php):
- Padding kiri input `.form-input`: Memastikan offset ikon kiri berada pada `left: 0.875rem` dan teks input dimulai dari `padding-left: 2.75rem` agar teks placeholder `nama@email.com` tidak menabrak ikon amplop.
- Mengatur margin divider `.auth-divider`: `margin: 1.25rem 0;` agar jarak vertikal antara tombol submit dan tombol Google seimbang (tidak terlalu renggang).

---

## 4. Rincian Perubahan File

| No | File Path | Aksi | Ringkasan Perubahan |
|---|---|---|---|
| 1 | `resources/views/auth/login.blade.php` | **Modify** | Memindahkan blok tombol Google OAuth dan divider ke bawah tombol submit `Login` |
| 2 | `resources/views/auth/register.blade.php` | **Modify** | Memindahkan blok tombol Google OAuth dan divider ke bawah tombol submit `Daftar` |
| 3 | `resources/views/layouts/auth.blade.php` | **Modify** | Menyesuaikan spacing `.input-icon-left` dan `.auth-divider` untuk tampilan yang lebih presisi |
| 4 | `docs/plans/2026-10-09-reposition-google-auth-button.md` | **Create** | Dokumen rencana kerja relokasi tombol Google |

---

## 5. Langkah-Langkah Implementasi

- [x] **Perbarui `resources/views/auth/login.blade.php`**:
  - Pindahkan blok elemen `<a href="{{ route('oauth.redirect', 'google') }}" ...>` dan `<div class="auth-divider">...</div>` dari atas form email ke bawah tag `</form>` sebelum `<p class="auth-footer-text">`.
  - Ubah teks divider menjadi *"atau"*.
- [x] **Perbarui `resources/views/auth/register.blade.php`**:
  - Pindahkan blok elemen tombol Google OAuth dan divider dari atas form ke bawah tag `</form>` sebelum footer switch akun.
  - Ubah teks divider menjadi *"atau"*.
- [x] **Penyempurnaan Styling di `resources/views/layouts/auth.blade.php`**:
  - Pastikan posisi ikon kiri pada `.input-icon-left` memiliki `left: 1rem` dan `padding-left: 2.75rem` agar tulisan placeholder rapi dan terbaca jelas.
  - Periksa margin divider agar pas dan rapi (`margin: 1.25rem 0`).
- [x] **Verifikasi Kualitas (QA)**:
  - Jalankan `php artisan test --filter=ReleaseThreeSocialLoginTest` untuk memastikan seluruh assertion lolos tanpa regresi (11/11 tests pass).
  - Gunakan browser untuk memverifikasi visual pada layar desktop (1440x900) dan mobile (375x667).

---

## 6. Strategi Pengujian (Testing Strategy)

1. **Automated Tests**:
   - Jalankan `php artisan test --filter=ReleaseThreeSocialLoginTest` — memastikan `$response->assertSee('Masuk dengan Google')` dan `assertSee('Daftar dengan Google')` tetap lulus.
2. **Visual Inspection di Browser**:
   - Halaman `/login`: Memastikan judul langsung diikuti input email, tombol `Login` berada tepat di atas divider, dan tombol `Login dengan Google` berada di bawahnya.
   - Halaman `/register`: Memastikan form kredensial muncul langsung di bawah header, tombol `Daftar` diikuti divider dan tombol `Daftar dengan Google`.
   - Cek responsivitas pada resolusi desktop dan layar ponsel.

---

## 7. Analisis Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| **Kegagalan Test Assertion Social Login** | Test gagal jika atribut atau teks tombol berubah | Tetap mempertahankan `title="Masuk dengan Google"` dan `aria-label="Masuk dengan Google"` pada link tombol Google agar test `ReleaseThreeSocialLoginTest` tetap PASS |
| **Urutan Tabulasi Keyboard Pengguna** | Pengguna yang memakai tombol Tab berpindah ke tombol Google sebelum mengisi form | Dengan menaruh tombol di bawah, urutan tabulasi menjadi lebih natural: Email -> Password -> Login -> Google |
| **Ketinggian Konten Halaman Daftar di Layar Kecil** | Form register cukup panjang, penambahan tombol di bawah membutuhkan scroll vertikal | Layout kanan sudah memiliki `overflow-y: auto`, sehingga pengguna ponsel dapat melakukan scroll dengan mulus |
