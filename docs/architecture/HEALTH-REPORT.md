> Diperbarui: 2026-10-13 | commit: 1553e56 | mode: A

# Laporan Kesehatan Arsitektur (HEALTH-REPORT.md)

Dokumen ini memuat temuan hasil audit statis terhadap kode sumber, struktur berkas, cakupan testing, dan konfigurasi keamanan sistem.  
*Prinsip Dokumen: Bersifat pelaporan murni (read-only audit). Tidak melakukan modifikasi kode secara otomatis.*

---

## 1. Ringkasan Matriks Temuan

| Severity | Jumlah Temuan | Area Terdampak |
| :--- | :--- | :--- |
| **TINGGI** | 1 | Autentikasi OAuth Pihak Ketiga & Exception Handling |
| **SEDANG** | 2 | Arsitektur Testing (Unit vs Feature), Berkas View Orphan |
| **RENDAH** | 2 | Pipeline Bundling Asset CSS, Meta Tag SEO (Social Share Image) |

---

## 2. Rincian Temuan Audit

### Temuan 1: Penanganan Graceful Fallback Kredensial Google OAuth
- **Severity**: `[TINGGI]`
- **Lokasi**: `app/Http/Controllers/Auth/SocialiteController.php` & `config/services.php`
- **Deskripsi Masalah**:  
  Fitur pendaftaran dan login peserta menyediakan tombol Google OAuth. Namun jika `GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET` belum diisi pada file `.env` lingkungan lokal/staging, pemanggilan rute `/auth/google/redirect` berpotensi memicu *fatal unhandled exception* dari library Socialite.
- **Rekomendasi Perbaikan**:  
  Tambahkan pengecekan kondisi sebelum redirect. Jika konfigurasi OAuth kosong, lakukan redirect kembali ke halaman login dengan pesan peringatan ramah: *"Layanan masuk dengan Google sedang tidak aktif. Silakan gunakan email dan kata sandi."*

---

### Temuan 2: Kesenjangan Cakupan Pengujian Unit (Unit Test Gap)
- **Severity**: `[SEDANG]`
- **Lokasi**: Direktori `tests/Unit/` vs `app/Services/`
- **Deskripsi Masalah**:  
  Repositori memiliki 54 Feature Test suites yang sangat lengkap di `tests/Feature/`. Namun pada direktori `tests/Unit/`, hanya ada 1 berkas `ExampleTest.php`. Terdapat 18 Domain Services penting (`PlacementService`, `ScoringService`, `TurnstileService`, `MasaTenggangService`, dll.) yang memiliki logika bisnis murni dan kalkulasi algoritma yang idealnya diuji secara terisolasi tanpa overhead database boot.
- **Rekomendasi Perbaikan**:  
  Buat Unit Test spesifik untuk memverifikasi logika deterministik, seperti pembobotan rubrik di `ScoringService` dan alokasi antrean FIFO di `PlacementService`.

---

### Temuan 3: Berkas Template Orphan (Tidak Terpakai)
- **Severity**: `[SEDANG]`
- **Lokasi**: `resources/views/welcome.blade.php` (Ukuran: 28.6 KB)
- **Deskripsi Masalah**:  
  Berkas `welcome.blade.php` adalah sisa bawaan dari instalasi awal Laravel. Rute beranda root `/` telah sepenuhnya diarahkan ke `LandingController` yang merender `resources/views/public/landing.blade.php`. Berkas ini tidak dipanggil oleh controller manapun di seluruh aplikasi.
- **Rekomendasi Perbaikan**:  
  Hapus atau arsipkan berkas `welcome.blade.php` ke folder legacy agar tidak memicu kebingungan pada pengembang baru yang mencari template beranda utama.

---

### Temuan 4: Berkas `resources/css/app.css` Kosong (0 Bytes)
- **Severity**: `[RENDAH]`
- **Lokasi**: `resources/css/app.css` & `vite.config.js`
- **Deskripsi Masalah**:  
  Konfigurasi Vite mendaftarkan `resources/css/app.css` sebagai berkas entri build, tetapi saat ini berkas tersebut berukuran 0 bytes karena seluruh token desain dan styling disematkan langsung melalui `<style>` tag di layout Blade (`layouts/app.blade.php` dan `layouts/dashboard.blade.php`).
- **Rekomendasi Perbaikan**:  
  Pindahkan CSS Custom Properties dan global stylesheet ke dalam `resources/css/app.css` agar terkompilasi, terminifikasi, dan memanfaatkan caching browser melalui Vite bundle.

---

### Temuan 5: Kelengkapan Open Graph Meta Image (`og:image`)
- **Severity**: `[RENDAH]`
- **Lokasi**: `resources/views/layouts/app.blade.php:L12-L17`
- **Deskripsi Masalah**:  
  Header publik telah mengimplementasikan tag `og:title`, `og:description`, `og:url`, dan `og:type`, tetapi belum menyertakan meta tag `og:image`. Saat tautan portal dibagikan melalui WhatsApp, Telegram, atau Twitter, kartu pratinjau visual tidak akan menampilkan gambar representatif.
- **Rekomendasi Perbaikan**:  
  Tambahkan `<meta property="og:image" content="{{ asset('images/og-portal-magkom.png') }}">` pada section header `layouts/app.blade.php`.
