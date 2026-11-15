# Panduan Penanganan Masalah (Troubleshooting Guide)

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Cakupan | Solusi error instalasi, basis data, symlink Windows, Vite bundling, pengujian |
| Referensi Implementasi | `composer.json`, `package.json`, `phpunit.xml` |

---

## 1. Masalah Versi PHP & Ekstensi

### Gejala:
```text
Composer detected issues in your platform: Your Composer dependencies require a PHP version ">= 8.3.0". You are running 8.2.x.
```
### Penyebab:
Repositori MAGKOM terkunci pada runtime PHP 8.3.x dengan pengetikan ketat.
### Solusi:
1. Pastikan binary PHP yang aktif di PATH terminal Anda adalah versi 8.3.
2. Jika menggunakan Laragon / XAMPP / Homebrew, alihkan versi PHP aktif ke PHP 8.3.
3. **Peringatan**: Dilarang menggunakan flag `--ignore-platform-req=php` pada lingkungan produksi atau pengembangan permanen karena dapat menyamarkan ketidakcocokan fungsi runtime.

---

## 2. Masalah Tautan Storage (Storage Symlink) pada Windows

### Gejala:
Berkas gambar profil atau dokumen publik tidak tampil (404 Not Found), atau muncul error:
```text
The system cannot find the path specified. (Symlink failed)
```
### Penyebab:
Sistem operasi Windows memerlukan hak akses administrator (*Developer Mode* atau *Elevated Command Prompt*) untuk membuat symlink direktori.
### Solusi:
1. Buka PowerShell atau Command Prompt dengan **Run as Administrator**.
2. Hapus folder symlink lama jika rusak:
   ```powershell
   Remove-Item public/storage -Force -Recurse
   ```
3. Buat ulang symlink melalui Artisan:
   ```bash
   php artisan storage:link
   ```
4. Atau aktifkan **Developer Mode** pada Windows Settings (Settings > Privacy & Security > For Developers > Developer Mode: ON).

---

## 3. Masalah Berkas Manifest Vite Hilang (Vite Manifest Not Found)

### Gejala:
```text
Vite manifest not found at: .../public/build/manifest.json
```
### Penyebab:
Aset frontend belum dikompilasi ke direktori `public/build/` dan dev server Vite sedang tidak berjalan.
### Solusi:
- **Untuk Pengembangan Lokal**: Jalankan server Vite di terminal terpisah:
  ```bash
  npm run dev
  ```
- **Untuk Pengujian / Lingkungan Produksi**: Jalankan kompilasi statis penuh:
  ```bash
  npm run build
  ```

---

## 4. Masalah Eksekusi Pengujian pada SQLite In-Memory

### Gejala:
```text
General error: 1 no such table: ... (saat menjalankan php artisan test)
```
### Penyebab:
Konfigurasi database testing belum termigrasi atau cache konfigurasi mengunci koneksi MySQL lama.
### Solusi:
1. Bersihkan seluruh cache konfigurasi:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```
2. Pastikan berkas `phpunit.xml` memiliki variabel:
   ```xml
   <env name="DB_CONNECTION" value="sqlite"/>
   <env name="DB_DATABASE" value=":memory:"/>
   ```
3. Pastikan class pengujian menggunakan trait `Illuminate\Foundation\Testing\RefreshDatabase;`.

---

## 5. Masalah Seeder Produksi (SEED_SUPERADMIN_PASSWORD Required)

### Gejala:
```text
RuntimeException: Variabel SEED_SUPERADMIN_PASSWORD wajib didefinisikan di .env production
```
### Penyebab:
`SeederProductionSafetyTest` mewajibkan kata sandi aman untuk akun SuperAdmin pertama saat dijalankan di server dengan `APP_ENV=production` untuk mencegah penggunaan kata sandi default `password`.
### Solusi:
Tambahkan nilai kata sandi aman pada berkas `.env` server produksi:
```env
SEED_SUPERADMIN_PASSWORD="KombinasiKataSandiKuatKedinasan2026!"
```

---

## 6. Masalah Halaman Kedaluwarsa (HTTP 419 Page Expired)

### Gejala:
Formulir pendaftaran atau aksi tombol mengembalikan pesan `419 Page Expired`.
### Penyebab:
Token CSRF kedaluwarsa atau hilang dari payload permintaan HTTP POST/PUT/DELETE.
### Solusi:
1. Pastikan formulir Blade menyertakan direktif `@csrf`:
   ```blade
   <form action="{{ route('peserta.logbook.store') }}" method="POST">
       @csrf
       ...
   </form>
   ```
2. Jika menggunakan panggilan AJAX via Axios/Fetch, pastikan header `X-CSRF-TOKEN` terkonfigurasi pada `resources/js/bootstrap.js`:
   ```javascript
   axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
   ```

---

## 7. Masalah Unggah Berkas Ditolak (Ukuran Berkas Melebihi Batas)

### Gejala:
Pendaftar menerima pesan validasi: `Ukuran berkas tidak boleh melebihi 10 MB`.
### Penyebab:
Aturan bisnis `berkas_maks_mb` membatasi ukuran maksimal berkas sebesar 10 MB, atau konfigurasi PHP (`upload_max_filesize` dan `post_max_size`) di `php.ini` bernilai lebih kecil dari 10 MB.
### Solusi:
1. Periksa berkas `php.ini` Anda:
   ```ini
   upload_max_filesize = 12M
   post_max_size = 15M
   memory_limit = 256M
   ```
2. Restart web server / PHP-FPM setelah mengubah konfigurasi `php.ini`.

---

## 8. Masalah Koneksi Realtime SSE (/realtime/stream Timeout)

### Gejala:
Peringatan pada konsol browser: `EventSource's response has a MIME type ("text/html") that is not "text/event-stream"`.
### Penyebab:
Web server (Nginx/Apache) menahan buffer output HTTP stream atau koneksi dihentikan oleh FastCGI buffering.
### Solusi:
Pastikan konfigurasi Nginx mematikan buffering untuk rute realtime:
```nginx
location /realtime/stream {
    proxy_buffering off;
    proxy_cache off;
    proxy_set_header Connection '';
    proxy_http_version 1.1;
    chunked_transfer_encoding off;
    proxy_read_timeout 86400s;
}
```
Lihat konfigurasi lengkap pada [docs/operations/nginx-performance-optimization.conf](../operations/nginx-performance-optimization.conf).
