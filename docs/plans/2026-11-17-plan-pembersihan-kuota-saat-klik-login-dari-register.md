# Plan: Pembersihan Pilihan Jurusan/Kuota Saat Mengklik Login dari Halaman Register

Dokumen rencana kerja ini disusun berdasarkan panduan `web-planning` dan aturan repositori `AGENTS.md` untuk menyelesaikan masalah munculnya kartu "Pilihan Magang Anda" di halaman Login ketika pengguna berpindah dari halaman Register.

---

## 1. Analisis Masalah

1. **Alur Eksisting**:
   - Calon peserta memilih kuota/jurusan di landing page dan menekan tombol pendaftaran.
   - Sistem mengarahkan peserta ke halaman register dengan query string:  
     `/register?bulan=X&bidang=Y&jurusan=Z&tipe=mandiri&jumlah=1`.
   - Pada halaman register, kartu "Pilihan Magang Anda" ditampilkan di atas formulir pendaftaran.
   - Di bagian bawah formulir pendaftaran (`resources/views/auth/register.blade.php` baris 326), terdapat tautan untuk pengguna yang sudah memiliki akun:
     ```blade
     <p style="margin: 22px 0 0; text-align: center; font-size: 15px; color: #5F6B76">
       Sudah punya akun? <a href="{{ route('login', request()->query()) }}" style="font-weight: 700; color: #003566; text-decoration: none">Login</a>
     </p>
     ```
   - Tautan tersebut secara eksplisit menyertakan `request()->query()`, sehingga ketika peserta mengklik **Login**, mereka diarahkan ke:  
     `/login?bulan=X&bidang=Y&jurusan=Z&tipe=mandiri&jumlah=1`.
   - Di halaman `resources/views/auth/login.blade.php` (baris 44–49), terdapat blok pengecekan:
     ```blade
     @if(request('bulan') || request('bidang') || request('jurusan'))
       @include('auth.partials._selected_quota_card', [
         'initialTipe' => request('tipe', 'mandiri'),
         'initialJumlah' => (int) request('jumlah', 1),
       ])
     @endif
     ```
   - Akibatnya, halaman Login menampilkan kartu "Pilihan Magang Anda" (seperti pada tangkapan layar user). Padahal halaman Login seharusnya bersih dan fokus hanya pada autentikasi akun terdaftar.

2. **Kebutuhan Pengguna**:
   - Setelah memilih jurusan dan masuk ke menu register, ketika tautan bawah "Login" diklik, halaman Login yang terbuka **tidak boleh menampilkan kartu pilihan jurusan/kuota** tersebut.

---

## 2. Pendekatan Solusi & Evaluasi Alternatif

### Perbandingan Solusi:

| Kriteria | Opsi 1: Bersihkan Tautan di Register (Direkomendasikan) | Opsi 2: Hapus Total Kartu dari Login Blade |
| :--- | :--- | :--- |
| **Deskripsi** | Mengubah tautan di `resources/views/auth/register.blade.php` menjadi `route('login')` (tanpa membawa `request()->query()`). | Menghapus blok `@include('auth.partials._selected_quota_card')` dari `resources/views/auth/login.blade.php` dan menyesuaikan test terkait. |
| **Dampak Visual** | Saat user klik "Login" dari Register, diarahkan ke URL bersih `/login`. Kartu kuota **tidak muncul** sama sekali di halaman login. | Kartu kuota tidak akan pernah muncul di `/login` bahkan jika user mengetik URL berparameter secara manual. |
| **Kompatibilitas Test** | **100% kompatibel**. Tidak ada test yang rusak karena testsuite `LandingPageToRegistrationPrefillTest` tetap valid. | Perlu memperbarui assertion di `test_halaman_login_membawa_parameter_kuota_dan_menyimpannya_saat_login`. |
| **Skop Perubahan** | Minimal, terfokus pada 1 baris di `register.blade.php`. | Lebih luas, menyentuh `login.blade.php` dan file unit test. |
| **Rekomendasi** | **Dipilih (Opsi 1)** sebagai langkah utama. (Opsi 2 dapat digabungkan jika user menginginkan halaman login sama sekali menolak kartu kuota). | Alternatif tambahan jika user ingin kartu dihilangkan permanen dari semua skenario login. |

---

## 3. Pemberitahuan Perubahan Blade (Kepatuhan `AGENTS.md`)

Sesuai aturan `AGENTS.md` Bagian 3 (*Blade read-only secara default*), setiap modifikasi markup Blade memerlukan alasan dan persetujuan eksplisit:

1. **Alasan Perubahan**:
   - Masalah disebabkan secara langsung oleh atribut `href` pada tautan navigasi di file Blade yang menyalin query string register ke login. Hal ini tidak dapat diselesaikan melalui CSS atau JavaScript saja tanpa membuat URL redirection yang tidak perlu.
2. **File Blade yang Akan Diubah**:
   - [resources/views/auth/register.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/auth/register.blade.php) pada baris 326:
     - **Semula**: `<a href="{{ route('login', request()->query()) }}" ...>Login</a>`
     - **Menjadi**: `<a href="{{ route('login') }}" ...>Login</a>`

---

## 4. Rencana Langkah Eksekusi

### Tahap 1: Modifikasi Tautan di `register.blade.php`
- [ ] Buka `resources/views/auth/register.blade.php`.
- [ ] Ubah `route('login', request()->query())` menjadi `route('login')`.

### Tahap 2: Pengujian & Quality Gate
- [ ] Jalankan automated test suite terkait autentikasi:
  ```powershell
  & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test tests/Feature/LandingPageToRegistrationPrefillTest.php
  & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test --filter=AuthDepthVisualParityTest
  ```
- [ ] Jalankan linter dan formatter:
  ```powershell
  & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" vendor/bin/pint --test
  ```
- [ ] Verifikasi manual alur:
  1. Akses halaman register dengan parameter kuota: `http://127.0.0.1:8000/register?bulan=1&jurusan=1`.
  2. Pastikan kartu kuota tampil di halaman register.
  3. Klik tautan **Login** di bagian bawah.
  4. Pastikan browser berpindah ke `http://127.0.0.1:8000/login` (URL bersih).
  5. Pastikan halaman login bersih dari kartu "Pilihan Magang Anda".

---

## 5. Kriteria Selesai (Definition of Done)

- [ ] Tautan "Sudah punya akun? Login" pada halaman register mengarah ke URL bersih `route('login')`.
- [ ] Halaman login yang dibuka dari klik tersebut tidak memuat parameter query kuota dan tidak menampilkan kartu "Pilihan Magang Anda".
- [ ] Seluruh automated test suite (Auth & Prefill) lulus 100% tanpa regresi.
- [ ] Pint check lulus.

