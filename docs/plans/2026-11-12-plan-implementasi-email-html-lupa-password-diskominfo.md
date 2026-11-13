# Rencana Implementasi: Fitur Lupa Password & Template Email HTML Diskominfo Sleman

## 1. Konteks & Status Eksisting Repositori

Berdasarkan analisis arsitektur MAGKOM:
- **Tabel & Model Akun**: Menggunakan model `App\Models\User` dan tabel `users`.
- **Role Mahasiswa/Siswa**: Menggunakan enum `App\Enums\RoleEnum::Peserta`.
- **Tabel Token**: Tabel `password_reset_tokens` sudah tersedia di database melalui migrasi bawaan Laravel.
- **Controller & Rute**: Sudah memiliki `App\Http\Controllers\Auth\ResetPasswordController` dengan rute:
  - `GET /forgot-password` (`password.request`)
  - `POST /forgot-password` (`password.email`)
  - `GET /reset-password/{token}` (`password.reset`)
  - `POST /reset-password` (`password.update`)
  - Proteksi anti-enumeration (AC-02-6) sudah aktif: respon seragam selalu diberikan baik email terdaftar maupun tidak.
- **Kekurangan Saat Ini**:
  - Model `User` masih menggunakan notifikasi bawaan Laravel yang mengirimkan plain text generik tanpa identitas Diskominfo.
  - Belum tersedia custom notification `App\Notifications\ResetPasswordNotification`.
  - Belum ada template email HTML profesional bertema Diskominfo Sleman (`resources/views/emails/reset-password.blade.php`).
  - Halaman `resources/views/auth/reset-password.blade.php` masih menggunakan layout legacy `layouts.auth`, belum diselaraskan dengan layout modern `layouts.auth-depth` seperti pada halaman `forgot-password`.

---

## 2. Rencana Perubahan Komponen

### Tahap 1: Pembuatan Template Email HTML Diskominfo Sleman
- **File**: `resources/views/emails/reset-password.blade.php`
- **Desain**:
  - Format tabel HTML inline styling standar kompatibilitas email client (Gmail, Outlook, Apple Mail).
  - Skema warna identitas resmi: Sleman Navy (`#003566`), Sleman Blue (`#01347C`), Emas Sleman (`#FFC300`), dan abu-abu netral (`#F8FAFC`).
  - Header: Identitas instansi "DINAS KOMUNIKASI DAN INFORMATIKA KABUPATEN SLEMAN" & "Sistem Manajemen Magang (MagKom)".
  - Konten:
    - Salam personal: `Halo, {{ $nama }}!`
    - Keterangan permintaan atur ulang kata sandi.
    - Tombol aksi utama: **RESET PASSWORD** yang mencolok dengan tautan terenkripsi.
    - Informasi masa berlaku: Berlaku selama 60 menit.
    - Salinan URL alternatif (jika tombol tidak dapat diklik oleh aplikasi email pengguna).
    - Footer keamanan: Penegasan bahwa email ini dikirim otomatis dan abaikan jika tidak meminta reset.

### Tahap 2: Pembuatan Notifikasi Kustom Laravel
- **File**: `app/Notifications/ResetPasswordNotification.php`
- **Tanggung Jawab**:
  - Mengimplementasikan `Illuminate\Notifications\Notification`.
  - Membangun `MailMessage` dengan template `emails.reset-password`.
  - Mengirim parameter: `nama`, `url` (menuju route `password.reset`), dan `count` (durasi expired token).

### Tahap 3: Integrasi Model `User`
- **File**: `app/Models/User.php`
- **Perubahan**:
  - Override method bawaan `sendPasswordResetNotification($token)` agar memicu `ResetPasswordNotification`.

### Tahap 4: Penyelarasan Halaman Buat Password Baru (Reset Password Form)
- **File**: `resources/views/auth/reset-password.blade.php`
- **Perubahan**:
  - Menyelaraskan tampilan dengan `layouts.auth-depth` agar visualnya serasi dan seamless ketika mahasiswa mengklik tautan dari email masuk.

### Tahap 5: Panduan Konfigurasi Mail `.env` (Gmail SMTP & Mode Log)
- Mode Pengembangan Cepat (Development/Review):
  - Menggunakan `MAIL_MAILER=log` di mana email HTML dan URL reset kata sandi langsung tercatat lengkap di `storage/logs/laravel.log`.
- Mode Pengiriman Nyata (Gmail SMTP):
  - Panduan penyetelan Google App Password (16 karakter) pada `.env`.

### Tahap 6: Automated Feature Testing & Quality Gate
- **File**: `tests/Feature/ForgotPasswordEmailFlowTest.php`
- **Cakupan Pengujian**:
  1. Permintaan reset mengirimkan `ResetPasswordNotification` untuk peserta aktif.
  2. Respon seragam anti-enumeration tetap terlindungi.
  3. Template email HTML ter-render tanpa error dengan link dan nama penerima.
  4. Password baru berhasil disimpan, di-hash dengan aman, dan token langsung hangus (tidak bisa dipakai kedua kali).
  5. Akun staf/admin ditolak dari alur public forgot-password (sesuai aturan AC-02-6).

---

## 3. Langkah Verifikasi
1. Eksekusi `php artisan test tests/Feature/ForgotPasswordEmailFlowTest.php`.
2. Jalankan `php artisan view:clear` dan `npm run build`.
3. Jalankan `vendor/bin/pint --test`.
4. Uji alur pengiriman langsung dan cek catatan email di log / inbox.

