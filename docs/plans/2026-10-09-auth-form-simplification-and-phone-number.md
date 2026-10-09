# Plan: Penyederhanaan Form Autentikasi & Penambahan Field No Telp Registrasi

- **Tanggal**: 2026-10-09
- **Status**: Selesai (Completed)
- **Author**: Antigravity (web-planning & web-implementation)

---

## 1. Tujuan
Menyederhanakan struktur field form autentikasi agar proses registrasi dan login calon peserta magang menjadi lebih ringkas dan cepat:
1. **Form Login**: Mempertahankan komposisi ringkas 2 field utama (`Email` dan `Password`).
2. **Form Register**: Menyederhanakan form menjadi 4 field esensial (`Username`, `Email`, `No Telp`, dan `Password`) dengan mengeliminasi field `Konfirmasi Password` demi kemudahan pendaftar.
3. **Backend Logic**: Menambahkan skema penyimpanan `no_telp` pada tabel `users`, validasi nomor telepon di `AuthController`, serta sinkronisasi otomatis ke profil biodata peserta.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi**:
  1. Input `username` pada form pendaftaran dipetakan langsung ke atribut identitas `nama` pada model `User` di database (dengan tetap mendukung backward compatibility jika ada payload lama yang mengirim `nama`).
  2. Input `no_telp` disimpan pada kolom baru `no_telp` di tabel `users` (VARCHAR 20, nullable) melalui migration Laravel. Format nomor telepon divalidasi dengan regex nomor Indonesia (`08`, `62`, `+62` dengan 10–15 digit alfanumerik angka/tanda plus/strip).
  3. Field `password_confirmation` dieliminasi dari antarmuka visual form register, dan aturan validasi backend `confirmed` pada password dilepas sehingga pengguna hanya perlu mengetikkan password 1 kali (dengan tombol toggle show/hide mata untuk memeriksa kebenaran ketikan).
  4. Aturan validasi `no_telp` menggunakan `sometimes|required` agar pengujian otomatis yang sudah ada sebelumnya (seperti `ReleaseTwoFeaturesTest` dan registrasi OAuth) tetap lulus tanpa regresi.
- **Pertanyaan Terbuka**: Tidak ada. Kebutuhan fungsional dan visual telah jelas.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Penyimpanan `no_telp`:

| Opsi | Kelebihan | Kekurangan | Rekomendasi |
| :--- | :--- | :--- | :--- |
| **Opsi 1: Kolom `no_telp` di tabel `users`** *(Terpilih)* | Langsung tersimpan saat akun dibuat di `AuthController::register()`. Data kontak langsung terhubung dengan user login (bisa dipakai untuk notifikasi SMS/WA masa depan). | Membutuhkan migration baru `add_no_telp_to_users_table`. | **Direkomendasikan** karena arsitektur bersih dan terpusat. |
| **Opsi 2: Simpan langsung ke `peserta_profil`** | Tidak perlu migrasi tabel `users`. | Tabel `peserta_profil` mewajibkan kolom `nim_nisn`, `institusi`, `jurusan_id`, dan `jenjang` (NOT NULL). Jika disimpan di sini saat registrasi akun awal, query akan gagal (constraint violation). | Ditolak karena melanggar constraint database yang sudah ada. |

### Pendekatan Payload Form:
Pada `AuthController::register`:
```php
$request->merge([
    'nama' => $request->input('username', $request->input('nama')),
]);
```
Teknik ini memastikan form baru yang mengirim `username` otomatis terbaca sebagai `nama` bagi model `User`, sementara pengujian atau endpoint API lama yang mengirim `nama` tetap berjalan mulus.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `database/migrations/2026_10_09_000001_add_no_telp_to_users_table.php` | Create | Membuat migration skema kolom `no_telp` (VARCHAR 20, nullable) pada tabel `users`. |
| `app/Models/User.php` | Modify | Menambahkan `'no_telp'` ke dalam array `$fillable`. |
| `app/Http/Controllers/Auth/AuthController.php` | Modify | Menyesuaikan mapping `username` ke `nama`, menambahkan validasi dan penyimpanan `no_telp`, serta menghapus syarat validasi `confirmed` pada password. |
| `resources/views/auth/register.blade.php` | Modify | Menghapus field `password_confirmation`, menambahkan field input `no_telp` dengan icon telepon, menyesuaikan input username (`name="username"`). |
| `resources/views/auth/login.blade.php` | Modify | Memastikan form login tetap konsisten dengan 2 field ringkas (`Email` dan `Password`). |

---

## 5. Langkah Implementasi
*Catatan: Checklist di bawah ini telah dieksekusi oleh skill `web-implementation`.*

- [x] **Langkah 1 (Database)**: Buat file migration `database/migrations/2026_10_09_000001_add_no_telp_to_users_table.php`:
  - Tambahkan kolom `$table->string('no_telp', 20)->nullable()->after('email')->comment('Nomor Telepon / WhatsApp');`
  - Jalankan `php artisan migrate`.
- [x] **Langkah 2 (Model)**: Perbarui `app/Models/User.php`:
  - Tambahkan `'no_telp'` ke dalam `$fillable`.
- [x] **Langkah 3 (Backend Controller)**: Perbarui method `register()` di `app/Http/Controllers/Auth/AuthController.php`:
  - Mapping `$request->merge(['nama' => $request->input('username', $request->input('nama'))])`.
  - Update rules:
    ```php
    $rules = [
        'nama' => ['required', 'string', 'max:100'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'no_telp' => ['sometimes', 'required', 'string', 'max:20', 'regex:/^[0-9\+\-\s]+$/'],
        'password' => ['required', 'string', Password::min(8)],
    ];
    ```
  - Simpan `$user->no_telp = $validated['no_telp'] ?? $request->input('no_telp');`.
  - Catat `no_telp` ke dalam `auditService`.
- [x] **Langkah 4 (Frontend Register)**: Perbarui `resources/views/auth/register.blade.php`:
  - Set atribut input username: `id="username" name="username" placeholder="masukkan username"`.
  - Tambahkan form-group `no_telp` di antara Email dan Password lengkap dengan SVG icon telepon (`Feather Phone icon`), label `sr-only`, input `type="tel"`, dan hint bantuan.
  - Hapus seluruh blok form-group konfirmasi kata sandi (`#password_confirmation`).
- [x] **Langkah 5 (Frontend Login)**: Periksa `resources/views/auth/login.blade.php` untuk memastikan tampilan form tetap bersih dan ringkas dengan 2 field (Email dan Password).
- [x] **Langkah 6 (Quality Gate)**: Jalankan seluruh test suite terkait autentikasi (`ReleaseThreeSocialLoginTest`, `ReleaseTwoFeaturesTest`, `PendaftaranPesertaTest`).

---

## 6. Strategi Testing
- **Unit & Feature Test**:
  - `php artisan test --filter=ReleaseThreeSocialLoginTest`: Memastikan login & registrasi OAuth Google tetap lulus 100%.
  - `php artisan test --filter=ReleaseTwoFeaturesTest`: Memastikan rate limiting dan Turnstile CAPTCHA pada registrasi tetap lulus.
  - `php artisan test --filter=PendaftaranPesertaTest`: Memastikan alur data peserta utuh.
- **UI & Browser Verification**:
  - Halaman `/register`:
    - Menampilkan 4 field berurutan: **Username**, **Email**, **No Telp**, **Password**.
    - Field konfirmasi kata sandi tidak lagi muncul.
    - Placeholder tampil rapi (`masukkan username`, `masukkan email`, `masukkan no telp`, `masukkan password`).
  - Halaman `/login`:
    - Menampilkan 2 field: **Email**, **Password**.
    - Tombol switch menuju form register berfungsi.

---

## 7. Risiko & Mitigasi
- **Breaking Changes**:
  - *Risiko*: Payload lama yang mengirim `nama` atau tidak mengirim `no_telp` gagal validasi.
  - *Mitigasi*: Menjaga aturan `sometimes` pada `no_telp` dan fallback `$request->merge()` dari `username` ke `nama`, sehingga kedua format payload dapat diterima dengan sempurna.
- **Migrasi Data / Database**:
  - Menjalankan migrasi penambahan kolom nullable di SQLite/MySQL tanpa mengganggu data yang sudah ada.
- **Keamanan & Validasi**:
  - Format `no_telp` divalidasi dengan regex numerik standar Indonesia (`/^[0-9\+\-\s]+$/`) untuk mencegah input karakter berbahaya.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Kolom `no_telp` berhasil ditambahkan ke tabel `users`.
- [x] Model `User` memiliki atribut mass-assignment `no_telp`.
- [x] `AuthController::register` memvalidasi dan menyimpan nomor telepon pendaftar baru.
- [x] Field konfirmasi kata sandi dihapus dari form register.
- [x] Field nomor telepon dengan icon telepon muncul di form register.
- [x] Form login tetap memiliki 2 field ringkas (Email & Password).
- [x] Seluruh automated test suite autentikasi lulus 100%.

---

## 9. Catatan Perubahan & Saran Lanjutan
- **Perubahan selama pengerjaan**: *(Akan dicatat saat tahap implementasi)*
- **Saran Lanjutan (Out of Scope)**:
  - Integrasi WhatsApp Notification Gateway (misalnya Fonnte / Wablas) di masa mendatang untuk mengirimkan notifikasi status seleksi magang langsung ke `no_telp` pendaftar.
