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

### B. Rekomendasi Penyedia SMTP Produksi Instansi

> [!CAUTION]
> **DILARANG MENGGUNAKAN AKUN GMAIL PERSONAL (@gmail.com) UNTUK PRODUKSI.**  
> Akun Gmail personal memiliki batas pengiriman ketat (500 email/hari), risiko pemblokiran mendadak akibat deteksi otomatis spam Google, dan ketiadaan jaminan SLA atau penyesuaian SPF/DKIM/DMARC dengan domain instansi (`.go.id`).

Untuk kebutuhan produksi instansi pemerintah, gunakan salah satu dari solusi berikut:

1. **SMTP Relay Internal Pemerintah Daerah / Diskominfo (Rekomendasi Utama)**:
   - Terhubung langsung dengan MTA dinas melalui jaringan privat (LAN/VPN).
   - Mendukung domain resmi instansi (misal `@diskominfo.go.id` atau `@slemankab.go.id`).
   - Bebas biaya lisensi pihak ketiga dan memenuhi kedaulatan data SPBE.
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=10.x.x.x (atau mail-relay.pemda.go.id)
   MAIL_PORT=25 (unauthenticated internal) atau 587 (authenticated TLS)
   MAIL_ENCRYPTION=tls
   ```

2. **Transactional Email Service Provider (Mailgun / AWS SES / Brevo)**:
   - Cocok untuk server cloud dengan throughput tinggi.
   - Menyediakan reputasi IP tinggi dan penandatanganan DKIM otomatis.
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.mailgun.org
   MAIL_PORT=587
   MAIL_USERNAME=postmaster@mg.diskominfo.go.id
   MAIL_PASSWORD=api-key-smtp
   MAIL_ENCRYPTION=tls
   ```

3. **Google Workspace Kedinasan**:
   - Hanya diizinkan jika menggunakan domain kustom institusi resmi Google Workspace dengan akun khusus aplikasi (service account / app password dinas).

---

## 4. Validasi Kesiapan Pra-Deployment (Pre-Deployment Validation)

Aplikasi dilengkapi perintah audit otomatis untuk memastikan konfigurasi email layak naik ke production:

```bash
php artisan email:validate-config --strict
```

Perintah ini memverifikasi:
- `MAIL_MAILER` bukan `log`, `array`, atau `null`.
- Host SMTP bukan `localhost` atau loopback `127.0.0.1`.
- Port SMTP terdefinisi dengan benar (25, 465, 587).
- Kredensial username dan password terisi.
- Format `MAIL_FROM_ADDRESS` valid dan tidak menggunakan domain `@example.com`.

Jika konfigurasi belum lengkap, perintah akan menghasilkan kode keluar `FAILURE (1)` dan daftar perbaikan yang harus dipenuhi sebelum deployment diizinkan.

---

## 5. Pengamanan Runtime Driver Log di Production

Untuk mencegah email verifikasi peserta terbuang ke berkas log tanpa diketahui administrator:
- Jika `APP_ENV=production` dan `MAIL_MAILER=log`, pengiriman email apapun akan secara otomatis memicu `RuntimeException` fatal:
  `FATAL: Pengiriman email di lingkungan production dilarang menggunakan driver 'log'. Konfigurasikan MAIL_MAILER=smtp dengan kredensial resmi pada berkas .env server.`
- Pada lingkungan testing dan development lokal, driver `array` dan `log` tetap berfungsi normal.

---

## 6. Perintah Artisan Pengujian Mandiri (CLI Diagnostics)

MAGKOM menyediakan perintah diagnostik internal untuk memverifikasi kesiapan alur surel tanpa perlu membuka browser:

```bash
php artisan auth:test-reset user@gmail.com
```

Perintah ini akan:
1. Menemukan pengguna dengan alamat email yang dimasukkan.
2. Menghasilkan token reset kata sandi resmi Laravel.
3. Mencetak URL reset lengkap langsung ke layar terminal CLI untuk memverifikasi bahwa konfigurasi `APP_URL` dan engine hashing token berfungsi sempurna.
