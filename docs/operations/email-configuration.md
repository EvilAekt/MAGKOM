# Panduan Konfigurasi Surel & SMTP (Email Configuration)

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Cakupan | Pengaturan SMTP, verifikasi surel, reset kata sandi, diagnostik CLI |
| Referensi Implementasi | `.env.example`, `config/mail.php` |

---

## 1. Prinsip Penggunaan Surel pada MAGKOM

Sistem MAGKOM menganut prinsip **"Status Sebagai Antarmuka Utama"**.

Sistem **sengaja tidak menggunakan** notifikasi surel massal (*email blast*), gateway SMS berbayar, atau WhatsApp API untuk pembaruan status harian. Seluruh pembaruan posisi antrean verifikasi, keputusan penempatan, penugasan proyek, dan review logbook disampaikan langsung melalui antarmuka web dan badge status dashboard.

Pengiriman surel dibatasi secara ketat hanya untuk keperluan keamanan dan otentikasi penting:
1. **Surel Verifikasi Akun Baru**: Mengirimkan tautan bertanda tangan digital saat calon peserta mendaftar via form lokal.
2. **Surel Pengaturan Ulang Kata Sandi**: Mengirimkan token reset kata sandi aman saat pengguna lupa kredensial.

Pembatasan ini dirancang untuk menjaga reputasi IP server surel kedinasan dari risiko masuk ke daftar hitam spam (*spam blacklist*).

---

## 2. Parameter Konfigurasi Lingkungan (`.env`)

```env
# Driver Pilihan: log (lokal), smtp (produksi / staging)
MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=notifikasi.magang@diskominfo.go.id
MAIL_PASSWORD=kombinasi-app-password-16-digit
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="notifikasi.magang@diskominfo.go.id"
MAIL_FROM_NAME="MAGKOM - Diskominfo Sleman"
```

---

## 3. Opsi Konfigurasi Berdasarkan Lingkungan

### A. Lingkungan Pengembangan Lokal (Local Development)
Untuk pengembangan lokal tanpa koneksi internet atau server SMTP nyata, gunakan driver `log`:
```env
MAIL_MAILER=log
```
Seluruh isi surel yang dikirimkan sistem otomatis dicatat ke dalam berkas log lokal `storage/logs/laravel.log`, sehingga pengembang dapat menyalin tautan verifikasi atau token reset langsung dari berkas log.

Alternatifnya, pengembang dapat menggunakan utilitas lokal seperti **Mailpit** (`MAIL_PORT=1025`) atau **Mailtrap** (`MAIL_PORT=2525`).

### B. Lingkungan Produksi Google Workspace / Gmail SMTP
Jika dinas menggunakan Google Workspace:
1. Masuk ke Google Account Keamanan Dinas.
2. Aktifkan **2-Step Verification**.
3. Buat **App Password** khusus 16 digit untuk aplikasi MAGKOM.
4. Gunakan `MAIL_HOST=smtp.gmail.com`, `MAIL_PORT=587`, `MAIL_ENCRYPTION=tls`.

### C. Server SMTP Relay Internal Kedinasan
Jika server web berada dalam jaringan intranet tertutup Pemerintah Daerah:
```env
MAIL_MAILER=smtp
MAIL_HOST=10.10.1.50
MAIL_PORT=25
MAIL_ENCRYPTION=null
```

---

## 4. Perintah Artisan Pengujian Mandiri (CLI Diagnostics)

MAGKOM menyediakan perintah diagnostik internal untuk memverifikasi kesiapan alur surel tanpa perlu membuka browser:

```bash
php artisan auth:test-reset user@gmail.com
```

Perintah ini akan:
1. Menemukan pengguna dengan alamat email yang dimasukkan.
2. Menghasilkan token reset kata sandi resmi Laravel.
3. Mencetak URL reset lengkap langsung ke layar terminal CLI untuk memverifikasi bahwa konfigurasi `APP_URL` dan engine hashing token berfungsi sempurna.
