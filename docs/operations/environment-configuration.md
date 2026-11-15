# Konfigurasi Lingkungan (.env Reference)

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Cakupan | Kamus parameter .env, kebutuhan produksi, manajemen kredensial aman |
| Referensi Implementasi | `.env.example`, `config/` |

---

## 1. Pendahuluan

Seluruh konfigurasi dinamis MAGKOM dikendalikan melalui berkas `.env` di root repositori. Berkas `.env` bersifat lokal dan rahasia, serta **dilarang keras** di-commit ke dalam sistem version control Git.

Gunakan berkas template `.env.example` sebagai acuan saat menyusun konfigurasi pada lingkungan baru.

---

## 2. Kamus Variabel Konfigurasi

### 2.1 Konfigurasi Dasar Aplikasi (Application Core)
| Variabel | Tipe Data | Nilai Default | Keterangan & Aturan |
|---|---|---|---|
| `APP_NAME` | String | `MAGKOM` | Nama resmi aplikasi. |
| `APP_ENV` | String | `local` | Lingkungan runtime: `local`, `testing`, `production`. |
| `APP_KEY` | String | (Kosong) | Kunci enkripsi 32-karakter base64 (`php artisan key:generate`). |
| `APP_DEBUG` | Boolean | `true` (dev) / `false` (prod) | Wajib bernilai `false` di production untuk mencegah kebocoran stack trace. |
| `APP_TIMEZONE` | String | `Asia/Jakarta` | Zona waktu operasional (WIB). |
| `APP_URL` | URL | `http://localhost:8000` | URL basis aplikasi (gunakan HTTPS di production). |
| `APP_LOCALE` | String | `id` | Bahasa standar aplikasi (Bahasa Indonesia). |

---

### 2.2 Basis Data (Database Configuration)
| Variabel | Tipe Data | Nilai Default | Keterangan |
|---|---|---|---|
| `DB_CONNECTION` | String | `mysql` | Driver basis data (gunakan `mysql` untuk MySQL/MariaDB). |
| `DB_HOST` | String / IP | `127.0.0.1` | Alamat host server MySQL. |
| `DB_PORT` | Integer | `3306` | Port koneksi MySQL. |
| `DB_DATABASE` | String | `magkom_db` | Nama database. |
| `DB_USERNAME` | String | `root` | Pengguna database. |
| `DB_PASSWORD` | String | (Kosong) | Kata sandi database. |

---

### 2.3 Sesi, Cache & Antrean (Session, Cache & Queue)
| Variabel | Tipe Data | Nilai Default | Keterangan |
|---|---|---|---|
| `SESSION_DRIVER` | String | `database` | Penyimpanan sesi (disarankan `database` agar sesi konsisten saat multi-worker). |
| `SESSION_LIFETIME` | Integer | `120` | Masa berlaku sesi dalam menit sebelum otomatis logout jika idle. |
| `CACHE_STORE` | String | `database` | Driver cache aplikasi (alternatif: `file`, `redis`). |
| `QUEUE_CONNECTION` | String | `database` | Driver antrean background jobs (alternatif: `sync`). |

---

### 2.4 Surel & Layanan Notifikasi (Mail Configuration)
| Variabel | Tipe Data | Nilai Default | Keterangan |
|---|---|---|---|
| `MAIL_MAILER` | String | `log` | Driver surel: `log` (lokal), `smtp` (production/staging). |
| `MAIL_HOST` | String | `127.0.0.1` | Host server SMTP (misal: `smtp.gmail.com` atau host relay dinas). |
| `MAIL_PORT` | Integer | `2525` / `587` | Port SMTP (587 untuk TLS). |
| `MAIL_USERNAME` | String | (Kosong) | Alamat akun pengirim SMTP. |
| `MAIL_PASSWORD` | String | (Kosong) | Kredensial / App Password SMTP. |
| `MAIL_ENCRYPTION` | String | `tls` | Protokol enkripsi transmisi surel (`tls` atau `ssl`). |
| `MAIL_FROM_ADDRESS`| Email | `no-reply@diskominfo.go.id` | Alamat pengirim resmi pada header surel. |
| `MAIL_FROM_NAME` | String | `MAGKOM - Diskominfo` | Nama representasi pengirim. |

---

### 2.5 Integrasi Eksternal & Layanan Tambahan
| Variabel | Tipe Data | Keterangan & Penggunaan |
|---|---|---|
| `GOOGLE_CLIENT_ID` | String | Client ID Google OAuth dari Google Cloud Console (untuk login peserta). |
| `GOOGLE_CLIENT_SECRET`| String | Client Secret Google OAuth. |
| `GOOGLE_REDIRECT_URL` | URL | Callback URL: `${APP_URL}/auth/google/callback`. |
| `SENTRY_LARAVEL_DSN` | URL | Data Source Name untuk pelacakan error dan crash report via Sentry. |
| `REALTIME_SSE_ENABLED`| Boolean | Sakelar pengaktifan Server-Sent Events (`true`/`false`). |
| `CAPTCHA_ENABLED` | Boolean | Sakelar verifikasi captcha SVG dinamis pada halaman login (`true`/`false`). |
| `TRUSTED_PROXIES` | String | Daftar IP reverse proxy / Cloudflare tepercaya, atau `*` jika di belakang ALB. |

---

### 2.6 Konfigurasi Inisialisasi Seeder Produksi
| Variabel | Wajib di Prod? | Keterangan |
|---|---|---|
| `SEED_SUPERADMIN_NAME` | Tidak | Nama display untuk akun SuperAdmin pertama. |
| `SEED_SUPERADMIN_EMAIL`| Tidak | Alamat email resmi SuperAdmin (`superadmin@magkom.go.id`). |
| `SEED_SUPERADMIN_PASSWORD` | **YA** | **Wajib diisi di production**. Kata sandi kuat minimal 12 karakter untuk SuperAdmin pertama. |

> [!WARNING]
> Jika `APP_ENV=production` dan `SEED_SUPERADMIN_PASSWORD` kosong saat menjalankan perintah `migrate --seed`, eksekusi seeder otomatis dihentikan dengan exception untuk mencegah pembuatan akun ber-password default yang rentan dibobol.

---

## 3. Prosedur Rotasi Rahasia (Secret Rotation)

Jika terjadi insiden kebocoran kredensial atau pergantian staf:
1. **Rotasi APP_KEY**:
   ```bash
   php artisan key:generate --force
   ```
   *Catatan: Mengubah APP_KEY akan membatalkan seluruh sesi aktif saat ini dan membuat token signed URL lama menjadi tidak valid.*
2. **Rotasi Kata Sandi Database**:
   - Perbarui kata sandi di server MySQL.
   - Perbarui variabel `DB_PASSWORD` pada `.env`.
   - Jalankan `php artisan config:cache` untuk menyegarkan cache konfigurasi.
