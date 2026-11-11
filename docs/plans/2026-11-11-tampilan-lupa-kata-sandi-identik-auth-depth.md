# Master Engineering Plan: Penyelarasan Desain Visual Halaman Lupa Kata Sandi (Identik Login & Register)

**Tanggal Dokumen**: 2026-11-11  
**Status**: DRAFT FOR APPROVAL (Menunggu Persetujuan Pengguna)  
**Tingkat Prioritas**: P1 (Penyelarasan Visual Parity & Konsistensi UX Autentikasi)  
**Tujuan Permintaan**: Mengubah tampilan halaman **Lupa Kata Sandi** (`/forgot-password`) agar **100% sama persis** dengan tampilan Login dan Register bertema *Auth Depth* (latar belakang kanvas 3D interaktif kamera, kartu putih sudut melengkung 28px dual-panel, panel kiri biru berisi radar animasi Sleman & carousel, serta form pemulihan kata sandi yang disesuaikan di panel kanan).

---

## 1. Tujuan

1. Mengadopsi arsitektur antarmuka **Auth Depth** pada view [`resources/views/auth/forgot-password.blade.php`](file:///g:/MAGKOM/resources/views/auth/forgot-password.blade.php) sehingga mewarisi layout terpadu [`layouts/auth-depth.blade.php`](file:///g:/MAGKOM/resources/views/layouts/auth-depth.blade.php).
2. Menyajikan antarmuka form panel kanan yang selaras secara geometris, tipografi, warna, dan micro-interaction dengan `login.blade.php` dan `register.blade.php`:
   - Judul form: **`Lupa Kata Sandi?`**
   - Subjudul instruksi: **`Masukkan alamat email yang terdaftar di akun Anda untuk menerima tautan pemulihan kata sandi.`**
   - Penanganan status flash alert sukses (*anti-enumeration message* AC-02-6) dan error validasi dengan styling MagKom elegan.
   - Field input email dilengkapi ikon SVG surat terstandarisasi.
   - Field kode verifikasi keamanan (Captcha) dengan tombol refresh interaktif yang ditenagai oleh `auth-depth.js`.
   - Tombol utama submit beraksen emas di bagian bawah: **`Kirim Tautan Pemulihan`**.
   - Tautan navigasi kembali: **`Ingat kata sandi Anda? Masuk kembali`** yang mengarah langsung ke `route('login')`.
3. Menghubungkan link tautan `Lupa kata sandi?` pada form Login ([`resources/views/auth/login.blade.php`](file:///g:/MAGKOM/resources/views/auth/login.blade.php)) dari anchor kosong `#lupa` ke rute aktif `{{ route('password.request') }}`.

---

## 2. Asumsi & Pertanyaan Terbuka

- **Asumsi 1 (Kontrak Rute & Backend Tetap Utuh)**: Alur pemulihan kata sandi tetap menggunakan controller backend yang sudah ada ([`ResetPasswordController@showForgot`](file:///g:/MAGKOM/app/Http/Controllers/Auth/ResetPasswordController.php#L28) dan [`sendResetLink`](file:///g:/MAGKOM/app/Http/Controllers/Auth/ResetPasswordController.php#L36)) dengan method `POST /forgot-password` (`password.email`), rate limiter `throttle:5,1`, token CSRF `@csrf`, dan pencatatan audit log aman.
- **Asumsi 2 (Kompatibilitas Layout & Script)**: Modul JavaScript [`resources/js/pages/auth/auth-depth.js`](file:///g:/MAGKOM/resources/js/pages/auth/auth-depth.js) dan stylesheet [`resources/css/pages/auth/auth-depth.css`](file:///g:/MAGKOM/resources/css/pages/auth/auth-depth.css) sudah mendukung kanvas 3D, interaktivitas captcha, dan efek visual tanpa memerlukan file CSS/JS baru.
- **Asumsi 3 (Panel Kiri Shared Component)**: Komponen panel kiri [`resources/views/auth/partials/_left_panel.blade.php`](file:///g:/MAGKOM/resources/views/auth/partials/_left_panel.blade.php) digunakan langsung tanpa modifikasi kode, dengan atribut `['variant' => 'forgot']`.

---

## 3. Evaluasi Pendekatan Teknis

### Opsi A (Rekomendasi Utama): Mengadopsi Layout `layouts.auth-depth` Secara Terpusat
- **Mekanisme**:
  - Mengubah `@extends('layouts.auth')` pada `forgot-password.blade.php` menjadi `@extends('layouts.auth-depth', ['variant' => 'forgot'])`.
  - Mengimplementasikan section `@section('auth-form')` dengan markup semantik yang mengikuti formula layout panel kanan `login.blade.php`.
- **Kelebihan**:
  - Konsistensi 100% terhadap Login dan Register: Kanvas 3D kamera, logo Pemkab Sleman, dan animasi radar SVG otomatis aktif.
  - Zero code duplication: Tidak ada CSS atau JS baru yang perlu dibuat.
  - Sesuai dengan aturan ketat `AGENTS.md` (mempertahankan Vite asset pipeline dan larangan inline styling/script berlebih).
- **Kekurangan**: Tidak ada.

### Opsi B: Membuat Layout Khusus Mandiri untuk Lupa Kata Sandi
- **Mekanisme**:
  - Membuat file Blade layout baru khusus `layouts/auth-forgot.blade.php`.
- **Kekurangan**:
  - Redundansi kode yang tidak perlu.
  - Menambah beban pemeliharaan jika terjadi penyesuaian aset desain di masa depan.

**Keputusan**: Menggunakan **Opsi A**.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/auth/forgot-password.blade.php`](file:///g:/MAGKOM/resources/views/auth/forgot-password.blade.php) | **Modify** | Migrasi dari `@extends('layouts.auth')` ke `@extends('layouts.auth-depth', ['variant' => 'forgot'])`. Menyusun panel kanan form pemulihan akun yang identik dengan login/register (Header, Input Email, Captcha `data-captcha-display`, Tombol Submit, Alert notifikasi, dan link Masuk kembali). |
| [`resources/views/auth/login.blade.php`](file:///g:/MAGKOM/resources/views/auth/login.blade.php) | **Modify** | Memperbarui link navigasi `Lupa kata sandi?` dari `href="#lupa"` menjadi `href="{{ route('password.request') }}"`. |
| [`tests/Feature/AuthDepthVisualParityTest.php`](file:///g:/MAGKOM/tests/Feature/AuthDepthVisualParityTest.php) | **Modify** | Menambahkan unit/feature test `test_halaman_lupa_kata_sandi_menampilkan_desain_depth_dan_form_pemulihan()` untuk memverifikasi keselarasan visual, rute, dan elemen form. |

---

## 5. Langkah-Langkah Implementasi

- [ ] **Langkah 1: Perbarui View `resources/views/auth/forgot-password.blade.php`**
  - Ganti `@extends('layouts.auth')` dengan `@extends('layouts.auth-depth', ['variant' => 'forgot'])`.
  - Atur judul halaman `@section('title', 'MagKom - Lupa Kata Sandi')`.
  - Susun `@section('auth-form')`:
    - Kontainer `<section>` dengan styling flexbox responsif, background `#FFFFFF`, dan padding `36px 40px 32px`.
    - Header judul `Lupa kata sandi?` (font-size 38px, font-weight 800, color `#003566`) dan deskripsi pembimbing.
    - Penanganan pesan sukses `session('status')` dan error validasi `$errors`.
    - Form `<form action="{{ route('password.email') }}" method="POST">` dengan `@csrf`.
    - Input Email dengan ikon SVG email, autocomplete, dan styling field `.fld`.
    - Verifikasi Captcha dengan elemen `data-captcha-display` dan tombol `data-action="refresh-captcha"`.
    - Tombol utama submit `<button type="submit" class="btn-main">Kirim Tautan Pemulihan</button>`.
    - Footer link `Ingat kata sandi Anda? Masuk kembali` yang mengarah ke `route('login')`.
    - Teks identitas `Magang Kominfo • Dinas Kominfo Sleman • v1.0`.

- [ ] **Langkah 2: Hubungkan Link Lupa Sandi pada `resources/views/auth/login.blade.php`**
  - Ganti `href="#lupa"` pada baris ke-40 menjadi `href="{{ route('password.request') }}"`.

- [ ] **Langkah 3: Perbarui Automated Test di `tests/Feature/AuthDepthVisualParityTest.php`**
  - Tambahkan metode pengujian `test_halaman_lupa_kata_sandi_menampilkan_desain_depth_dan_form_pemulihan()`.
  - Verifikasi respon HTTP 200, view `auth.forgot-password`, elemen kanvas `data-auth-canvas`, aset `auth-depth`, judul form, input email, captcha, dan tautan kembali.

- [ ] **Langkah 4: Jalankan Quality Gates Lengkap**
  - `npm run build`
  - `& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" ./vendor/bin/pint --test`
  - `& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" ./vendor/bin/phpstan analyse --memory-limit=1G`
  - `& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test tests/Feature/AuthDepthVisualParityTest.php`

---

## 6. Strategi Testing

1. **Feature Test Otomatis**:
   - `test_halaman_lupa_kata_sandi_menampilkan_desain_depth_dan_form_pemulihan()`:
     - Akses `GET /forgot-password`.
     - Memastikan status HTTP 200 OK.
     - Memastikan view yang dirender adalah `auth.forgot-password`.
     - Memastikan keberadaan aset `data-auth-canvas` dan `auth-depth`.
     - Memastikan teks "Lupa kata sandi?", "Kirim Tautan Pemulihan", dan link "Masuk kembali".
2. **Regression Test**:
   - Menjalankan kembali seluruh test pada `AuthDepthVisualParityTest` untuk memastikan halaman Login dan Register tidak terganggu.

---

## 7. Risiko & Mitigasi

- **Risiko Keamanan (Email Enumeration)**: Modifikasi hanya pada lapisan markup presentasi (Blade). Logika controller `ResetPasswordController@sendResetLink` yang menerapkan pesan seragam anti-enumeration dan audit logging tetap terjaga utuh.
- **Risiko Responsivitas**: Form menggunakan kontainer fleksibel yang sama persis dengan Login, sehingga adaptif pada smartphone, tablet, maupun layar desktop.

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Halaman `/forgot-password` menampilkan desain *Auth Depth* (kanvas 3D, kartu putih sudut 28px, panel kiri Sleman, panel kanan form) identik dengan Login dan Register.
- [ ] Tombol refresh captcha pada halaman lupa kata sandi berfungsi normal.
- [ ] Tautan `Lupa kata sandi?` di halaman Login berhasil mengarahkan pengguna ke `/forgot-password`.
- [ ] Tautan `Masuk kembali` di halaman Lupa Kata Sandi berhasil mengarahkan pengguna ke `/login`.
- [ ] Kompilasi aset `npm run build` berhasil tanpa error.
- [ ] `vendor/bin/pint --test` lulus standar PSR-12.
- [ ] `vendor/bin/phpstan analyse` bebas error.
- [ ] Seluruh pengujian otomatis di `AuthDepthVisualParityTest.php` lulus 100%.

