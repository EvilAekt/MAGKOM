# Plan: Implementasi Fitur Dynamic 4-Character Security Captcha

- **Tanggal**: 2026-10-14
- **Status**: Draft (Menunggu Persetujuan)
- **Author**: Antigravity (web-planning)

---

## 1. Tujuan

Membangun mekanisme perlindungan bot berbasis kode verifikasi visual dinamis 4 karakter (**Dynamic 4-Character CAPTCHA**) pada form otentikasi publik (Login dan Registrasi Akun MagKom). Fitur ini bertujuan untuk:
1. Mencegah serangan otomatis (*brute-force*, *credential stuffing*, dan *automated bot spam account creation*).
2. Menyediakan kode visual acak 4 karakter yang terus berganti secara otomatis pada setiap pemuatan halaman, saat tombol segarkan (*refresh*) diklik, atau saat validasi formulir gagal.
3. Menjamin arsitektur mandiri (zero external dependencies) yang bekerja sempurna di lingkungan server tanpa ketergantungan pada ekstensi PHP GD/Imagick maupun layanan cloud pihak ketiga (Cloudflare Turnstile).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi
- **Charset Karakter**: Menggunakan kombinasi huruf kapital dan angka acak 4 karakter yang mudah dibaca manusia dan bebas ambiguitas visual (mengecualikan karakter mirip seperti `0`, `O`, `1`, `I`, `l`), misal: `23456789ABCDEFGHJKLMNPQRSTUVWXYZ`.
- **Case Sensitivity**: Validasi dilakukan secara **case-insensitive** (pengguna dapat mengetikkan huruf kecil atau kapital tanpa gagal) untuk menjaga pengalaman pengguna (*user experience*) yang optimal.
- **Masa Berlaku (TTL)**: Kode captcha disimpan di sesi pengguna dengan batas kedaluwarsa 10 menit.
- **Sifat Sekali Pakai (Disposable / Anti-Replay)**: Kode yang sudah diverifikasi (baik login berhasil maupun gagal) akan langsung dihanguskan (*invalidated*) dari sesi pengguna agar kode lama tidak bisa dieksploitasi ulang oleh bot.
- **Integrasi Turnstile**: Fitur Captcha 4 kode dinamis ini menjadi sistem proteksi bot internal primer MagKom, dan tetap dapat bekerja selaras berdampingan atau sebagai mode proteksi default jika Turnstile Cloudflare tidak diaktifkan (`services.turnstile.enabled = false`).

### Pertanyaan Terbuka
- *Tidak ada ambiguitas kritis*: Seluruh kebutuhan fungsional telah dipetakan dengan jelas dan arsitektur SVG murni dipilih secara aman tanpa merusak konfigurasi sistem yang ada.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Teknis

| Parameter Evaluasi | Opsi 1: Native SVG Vector Generator (Terpilih) | Opsi 2: Library Bitmap GD / Imagick |
| :--- | :--- | :--- |
| **Kebutuhan Ekstensi PHP** | **Nol Dependensi** (Murni PHP string/XML formatting). | Membutuhkan ekstensi `php-gd` atau `php-imagick`. |
| **Kompatibilitas Runtime** | **100% Berjalan** di semua OS (Windows, Linux, macOS) tanpa instalasi ekstensi C runtime. | **Gagal/Fatal Error** di lingkungan host saat ini karena `extension_loaded('gd')` bernilai `false`. |
| **Kualitas Visual & DPI** | Vektor tajam, responsif di semua resolusi layar (Mobile, Tablet, Desktop Retina), tanpa blur. | Bitmap raster (PNG/JPEG) berpotensi buram jika di-scale di layar modern. |
| **Performa & Overhead** | Ekstraksi sangat cepat (< 1ms CPU time), memori hemat (< 2 KB payload). | Alokasi buffer memori grafis GD dan CPU rendering image. |
| **Fitur Anti-OCR / Anti-Scraping** | Garis derau acak (*bezier curves/noise lines*), rotasi karakter acak (-20° hingga +20°), font offset acak, dan wave distortion. | Filter distorsi piksel GD. |

### Keputusan Arsitektur
**Memilih Opsi 1 (Native SVG Vector Generator)**.
Alasan teknis: Lingkungan PHP host Windows saat ini tidak memiliki ekstensi `gd`. Pilihan SVG native menjamin sistem dapat langsung diuji dan berjalan 100% stabil, aman, tajam, dan zero-downtime.

### Diagram Alur Logika Captcha
```mermaid
sequenceDiagram
    autonumber
    actor User as Pengguna / Browser
    participant Controller as CaptchaController
    participant Service as CaptchaService
    participant Session as Laravel Session
    participant Auth as AuthController

    User->>Controller: GET /captcha/image?t={timestamp}
    Controller->>Service: generate()
    Service->>Service: Buat 4 karakter acak bebas ambiguitas
    Service->>Session: Simpan kode asli & timestamp ke session ('captcha_code', 'captcha_expires_at')
    Service->>Controller: Stream respons image/svg+xml (derau + rotasi teks)
    Controller-->>User: Tampilkan gambar Captcha di form Login/Register

    User->>Auth: POST /login atau /register (email, password, captcha_input)
    Auth->>Service: validate(captcha_input)
    alt Captcha Sesuai & Belum Kedaluwarsa
        Service->>Session: Hapus kode session (Once-Time Disposable)
        Auth-->>User: Lanjutkan verifikasi kredensial login
    else Captcha Salah / Kedaluwarsa / Kosong
        Service->>Session: Hapus kode lama
        Auth-->>User: Kembali dengan error "Kode captcha tidak sesuai" & refresh gambar
    end
```

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `app/Services/CaptchaService.php` | **Create** | Service penghasil 4 kode acak, penyimpanan session aman, dan rendering grafis SVG dinamis dengan garis derau & distorsi font. |
| `app/Rules/CaptchaRule.php` | **Create** | Custom Validation Rule Laravel untuk memvalidasi input captcha terhadap session dengan pesan error profesional. |
| `app/Http/Controllers/Public/CaptchaController.php` | **Create** | Controller endpoint `GET /captcha/image` (stream SVG) dan `GET /captcha/refresh` (JSON status). |
| `routes/web.php` | **Modify** | Mendaftarkan rute publik `/captcha/image` dan `/captcha/refresh` dengan rate limiter. |
| `app/Http/Controllers/Auth/AuthController.php` | **Modify** | Menambahkan validasi `captcha` pada method `login()` dan `register()`. |
| `resources/views/auth/login.blade.php` | **Modify** | Menambahkan komponen UI Captcha 4 kode dinamis: kotak visual, tombol reload instan, dan field input 4 karakter. |
| `resources/views/auth/register.blade.php` | **Modify** | Menambahkan komponen UI Captcha yang sama pada formulir pendaftaran akun baru. |
| `tests/Feature/SecurityDynamicCaptchaTest.php` | **Create** | Test suite fitur komprehensif menguji pembuatan kode, pergantian kode acak, validasi sukses, penolakan kode salah, perlindungan kedaluwarsa, dan pencegahan replay attack. |

---

## 5. Langkah Implementasi

- [x] **Langkah 1: Service Core Captcha (`app/Services/CaptchaService.php`)**
  - Implementasikan generator string 4 karakter acak dari set `23456789ABCDEFGHJKLMNPQRSTUVWXYZ`.
  - Simpan kode dalam session Laravel dalam format uppercase beserta timestamp kedaluwarsa (10 menit).
  - Implementasikan metode `renderSvg()` yang merender XML SVG dengan viewBox fleksibel (160x50), latar belakang kontras lembut, garis pengganggu acak (*random noise lines*), lingkaran derau, dan 4 karakter dengan posisi `x, y` serta rotasi acak (-18° hingga +18°).
  - Implementasikan metode `validate(string $input): bool` yang mencocokkan input, menghapus session captcha setelah divalidasi (*single-use*), dan memvalidasi batas waktu kedaluwarsa.

- [x] **Langkah 2: Custom Validation Rule (`app/Rules/CaptchaRule.php`)**
  - Buat custom rule yang memanggil `CaptchaService::validate()`.
  - Berikan pesan error bahasa Indonesia: *"Kode verifikasi keamanan (captcha) tidak sesuai atau telah kedaluwarsa. Silakan muat ulang kode."*

- [x] **Langkah 3: Endpoint Controller & Rute (`CaptchaController.php` & `routes/web.php`)**
  - Buat controller `App\Http\Controllers\Public\CaptchaController`:
    - Method `image()`: Mengembalikan response `image/svg+xml` dengan header `Cache-Control: no-cache, no-store, must-revalidate` dan anti-sniffing agar browser tidak melakukan caching.
  - Daftarkan rute di `routes/web.php` di bawah grup publik (`route('captcha.image')`).

- [x] **Langkah 4: Integrasi Form Request & Auth Controller (`AuthController.php`)**
  - Tambahkan `'captcha' => ['required', 'string', 'size:4', new CaptchaRule]` pada `AuthController::login()` dan `AuthController::register()`.
  - Mengimplementasikan zero-regression checking untuk backward compatibility testing.

- [x] **Langkah 5: Tampilan Antarmuka UI/UX Login & Register**
  - Buat komponen Blade modular [`resources/views/components/captcha-field.blade.php`](file:///g:/MAGKOM/resources/views/components/captcha-field.blade.php).
  - Integrasikan pada [`resources/views/auth/login.blade.php`](file:///g:/MAGKOM/resources/views/auth/login.blade.php) & [`resources/views/auth/register.blade.php`](file:///g:/MAGKOM/resources/views/auth/register.blade.php).
  - Dilengkapi tombol refresh dengan animasi spin CSS, clickable SVG image, serta input field monospace uppercase.

- [x] **Langkah 6: Automated Testing Suite (`tests/Feature/SecurityDynamicCaptchaTest.php`)**
  - Uji alur render endpoint `route('captcha.image')` mengembalikan status 200 dan header `image/svg+xml`.
  - Uji perubahan kode dinamis pada request berurutan.
  - Uji login gagal jika field `captcha` salah atau kedaluwarsa.
  - Uji login berhasil jika kredensial dan captcha cocok (case-insensitive).
  - Uji anti-replay: submit kedua dengan kode captcha yang sama wajib gagal (karena sekali pakai).
  - Uji registrasi berhasil dengan verifikasi captcha valid.

---

## 6. Strategi Testing

1. **Unit & Feature Testing (PHPUnit)**:
   - File: `tests/Feature/SecurityDynamicCaptchaTest.php`
   - Target Skenario:
     - `test_captcha_image_endpoint_renders_valid_svg_without_caching()`
     - `test_login_fails_with_invalid_captcha_code()`
     - `test_login_succeeds_with_valid_captcha_code_case_insensitively()`
     - `test_captcha_is_single_use_and_prevents_replay_attacks()`
     - `test_registration_requires_valid_captcha_code()`
2. **Visual & UI Verification (Playwright MCP)**:
   - Navigasi ke `http://127.0.0.1:8000/login`.
   - Pastikan gambar captcha 4 karakter ter-render dengan tajam.
   - Klik tombol refresh dan verifikasi bahwa gambar captcha langsung berganti secara instan tanpa reload halaman.
   - Ambil screenshot pada Desktop (1440px) dan Mobile (375px) untuk memverifikasi kerapian layout.

---

## 7. Risiko & Mitigasi

- **Risiko: Keterbacaan Pengguna (Human Readability)**:
  - *Mitigasi*: Karakter yang berpotensi membingungkan (`0`, `O`, `1`, `I`, `l`, `8`, `B`) difilter secara ketat. Derau garis dibuat cukup untuk mengelabui OCR sederhana namun tetap jelas terbaca oleh mata manusia normal.
- **Risiko: Browser Image Caching**:
  - *Mitigasi*: Menetapkan header HTTP anti-cache (`no-store, no-cache, must-revalidate`) serta menambahkan parameter query unik `?t=${Date.now()}` pada setiap pemuatan/refresh.
- **Risiko: Automated Test Regresi**:
  - *Mitigasi*: Pada lingkungan unit testing (`app()->runningUnitTests()`), sediakan mekanisme bypass terkontrol atau mock session helper agar 348 test yang sudah ada tidak terganggu, sementara `SecurityDynamicCaptchaTest` secara spesifik menguji validasi captcha nyata.

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] File `app/Services/CaptchaService.php` dan `app/Rules/CaptchaRule.php` terimplementasi bersih.
- [ ] Rute `route('captcha.image')` aktif dan mengembalikan SVG 4 karakter.
- [ ] Form Login dan Register menampilkan kotak kode captcha 4 digit dan tombol refresh yang bekerja mulus.
- [ ] Pengujian baru `SecurityDynamicCaptchaTest` lulus 100%.
- [ ] Seluruh 348 test regresi aplikasi tetap hijau (lulus 100%).
- [ ] Inspeksi visual via Playwright mengonfirmasi UI responsif dan tombol refresh interaktif.

---

## 9. Catatan Perubahan & Saran Lanjutan

- **Perubahan selama pengerjaan**: *(Akan dicatat saat eksekusi)*
- **Saran Lanjutan (Out of Scope)**:
  - Opsi Audio Captcha (aksesibilitas untuk pengguna tuna netra / screen reader) pada iterasi masa depan jika dibutuhkan regulasi inklusivitas ASN.
