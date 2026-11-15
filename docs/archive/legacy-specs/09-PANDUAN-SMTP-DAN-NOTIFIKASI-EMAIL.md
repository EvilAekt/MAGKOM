# Panduan Teknis: Konfigurasi SMTP, Notifikasi Email, & Uji Mandiri Lupa Password (MAGKOM)

Dokumen ini merupakan panduan resmi bagi pengembang dan administrator dalam mengonfigurasi, menguji, dan memelihara layanan pengiriman email di portal **MAGKOM (Magang Kominfo)**.

---

## 1. 🚀 Cara Cepat Uji Lupa Password via Artisan Command

Untuk menguji alur lupa password tanpa harus menunggu pengiriman email atau memeriksa folder spam, gunakan perintah Artisan yang telah disediakan:

```bash
# Uji dan langsung cetak URL reset password bertoken ke terminal:
php artisan auth:test-reset adhityagalang553@gmail.com --no-send

# Uji lengkap sekaligus kirim email nyata via SMTP:
php artisan auth:test-reset adhityagalang553@gmail.com
```

### Output yang Dihasilkan:
1. **Pemeriksaan Akun**: Memverifikasi keberadaan user, status aktif (`1`), dan role (`peserta`).
2. **Token Generator**: Membuat token resmi di tabel `password_reset_tokens` (berlaku 60 menit).
3. **URL Reset Langsung**: Mencetak tautan langsung `http://localhost:8000/reset-password/{token}?email={email}` yang dapat langsung disalin ke browser untuk membuat password baru.
4. **Koneksi Transport SMTP**: Menguji handshake SMTP dan melaporkan status keberhasilan atau pesan kesalahan (*exception*).

---

## 2. 🔍 Mengapa Email Gmail SMTP Sering Masuk ke Folder SPAM?

Ketika menggunakan Gmail SMTP (`smtp.gmail.com`) dengan akun pribadi (misal: `@gmail.com`) di lingkungan *local development*:
1. **Ketiadaan Record DNS SPF / DKIM**:
   Email dikirimkan dari server lokal (IP dinamis komputer Anda) atas nama alamat `@gmail.com`. Karena IP lokal tidak terdaftar dalam record SPF resmi Google, filter anti-spam Google otomatis mencurigai email tersebut sebagai *spoofing* / *unverified sender*.
2. **Kategori Folder**:
   Gmail penerima hampir selalu menaruh email otomatis ini ke dalam **Folder SPAM / JUNK** atau tab **Promotions / Updates**.
3. **Solusi di Akun Penerima**:
   * Buka folder **Spam** di Gmail penerima.
   * Temukan email dengan subjek: *"Permintaan Reset Password - Sistem Magang Diskominfo (MAGKOM)"*.
   * Klik tombol **"Bukan Spam" (Report not spam)**.

---

## 3. 🛠️ Pilihan Driver Email untuk Lingkungan Development

Sesuaikan pengaturan pada berkas `.env` sesuai kebutuhan:

### Opsi A: Gmail SMTP Nyata (Google App Password)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=email-anda@gmail.com
MAIL_PASSWORD=16-digit-app-password-dari-google
MAIL_FROM_ADDRESS="email-anda@gmail.com"
MAIL_FROM_NAME="MAGKOM Diskominfo"
```
> [!IMPORTANT]
> Password di atas **wajib** menggunakan *Google App Password* (16 karakter), bukan kata sandi akun Google login biasa. Fitur verifikasi 2 langkah (2-Step Verification) harus aktif di akun Google pengirim.

### Opsi B: Local Mailpit / Mailtrap (Sangat Direkomendasikan untuk Pengujian)
Mailpit menangkap seluruh email keluar tanpa mengirimkannya ke internet dan menampilkannya di dashboard web lokal (`http://localhost:8025`).
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="no-reply@magkom.go.id"
MAIL_FROM_NAME="MAGKOM Diskominfo"
```

### Opsi C: Log Driver (Tanpa Koneksi Internet)
Email dicatat lengkap beserta tautan reset di dalam `storage/logs/laravel.log`:
```env
MAIL_MAILER=log
```

---

## 4. 🏢 Standar Pengiriman Email Produksi (Production Anti-Spam)

Untuk lingkungan *Production / Staging Server* Dinas Kominfo:
1. **Gunakan Domain Resmi**:
   * Alamat pengirim wajib menggunakan domain instansi: `no-reply@slemankab.go.id` atau `magang@kominfo.slemankab.go.id`.
2. **Konfigurasi DNS Authentication**:
   * **SPF**: `v=spf1 include:_spf.google.com ~all` (sesuai gateway mail yang digunakan).
   * **DKIM**: Pasang public key TXT record pada DNS domain pengirim.
   * **DMARC**: Pasang policy DMARC `v=DMARC1; p=quarantine; rua=mailto:dmarc-reports@slemankab.go.id`.
3. **Dedicated Transactional Provider**:
   Disarankan menggunakan layanan transactional mail terkelola (Brevo, Resend, Amazon SES, atau Mailgun) dengan IP reputasi tinggi agar 100% email masuk ke *Primary Inbox* tanpa keterlambatan.

---

## 5. 📌 Kebijakan Branching & Direct Push (test-2)

Branch `test-2` telah dikonfigurasi bebas proteksi gembok (*ruleset direct push allowed*) sehingga developer dapat langsung menjalankan `git push origin test-2` tanpa perlu membuat feature branch atau Pull Request baru.


