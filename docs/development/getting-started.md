# Panduan Memulai Cepat (Getting Started)

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Cakupan | Setup lingkungan lokal, instalasi dependensi, migrasi basis data, akun pengujian awal |
| Referensi Implementasi | `composer.json`, `package.json`, `.env.example`, `database/seeders/` |

---

## 1. Prasyarat Perangkat Lunak

Sebelum memasang MAGKOM pada komputer lokal, pastikan perangkat lunak berikut telah terpasang:

- **PHP**: Versi **8.3.x** (Platform locked). Ekstensi wajib aktif: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `zip`.
- **Composer**: Versi **2.2+**
- **Node.js & NPM**: Node.js versi **18.x** atau **20.x LTS**, dan NPM versi **10.x+**
- **Basis Data**: **MySQL 8.0+** atau MariaDB 10.4+ (dengan engine default InnoDB)
- **Git**: Versi 2.30+

---

## 2. Langkah Instalasi Langkah-demi-Langkah

### Langkah 1: Kloning Repositori
Buka terminal dan lakukan kloning repositori MAGKOM ke direktori kerja Anda:

```bash
git clone https://github.com/EvilAekt/MAGKOM.git
cd MAGKOM
```

### Langkah 2: Pemasangan Dependensi Backend (Composer)
Pasang seluruh pustaka backend Laravel:

```bash
composer install --optimize-autoloader
```

### Langkah 3: Pemasangan Dependensi Frontend (NPM)
Pasang dependensi Vite dan kompilasi aset frontend:

```bash
npm ci
npm run build
```

### Langkah 4: Konfigurasi Berkas Lingkungan (.env)
Salin berkas sampel `.env.example` menjadi berkas konfigurasi lokal `.env`:

```bash
# Untuk Linux / macOS / Git Bash
cp .env.example .env

# Untuk Windows PowerShell
Copy-Item .env.example .env
```

Buka berkas `.env` menggunakan teks editor Anda dan sesuaikan konfigurasi koneksi basis data:

```env
APP_NAME=MagKom
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=magkom_db
DB_USERNAME=root
DB_PASSWORD=
```

> [!NOTE]
> Pastikan basis data kosong dengan nama `magkom_db` telah dibuat di MySQL Server Anda sebelum melanjutkan ke langkah migrasi.

### Langkah 5: Pembuatan Kunci Aplikasi (Application Key)
Generate encryption key aplikasi:

```bash
php artisan key:generate
```

### Langkah 6: Migrasi Basis Data dan Seeding Master Data
Jalankan seluruh migrasi skema basis data dan masukkan data awal (master jurusan, master bidang, 12 parameter operasional, 13 modul sistem, dan akun pengujian lokal):

```bash
php artisan migrate --seed
```

> [!IMPORTANT]
> Pada lingkungan pengembangan lokal (`APP_ENV=local`), perintah seeder otomatis mengisi data master dan membuat akun pengujian resmi. Seeder dummy otomatis dicegah jika aplikasi dijalankan pada lingkungan `APP_ENV=production`.

### Langkah 7: Tautkan Direktori Penyimpanan (Storage Link)
Hubungkan direktori publik ke direktori storage:

```bash
php artisan storage:link
```

---

## 3. Menjalankan Server Pengembangan Lokal

Untuk menjalankan aplikasi secara penuh dengan fitur Hot Module Replacement (HMR) pada aset CSS dan JavaScript, buka **dua jendela terminal**:

### Terminal 1: Server Backend Laravel
```bash
php artisan serve --port=8000
```
Server backend akan aktif di: `http://localhost:8000`

### Terminal 2: Vite Dev Server (Aset Frontend)
```bash
npm run dev
```
Vite akan berjalan di background dan secara instan memperbarui tampilan saat berkas CSS atau JavaScript diubah.

---

## 4. Akun Pengujian Lokal Bawaan (Default Seeded Accounts)

Setelah menjalankan `php artisan migrate --seed` (via `TestingUserSeeder` atau `PurgeAndSeedCleanGmailAccountsSeeder`), sistem menyediakan akun pengujian resmi untuk keempat peran pengguna:

| Peran | Alamat Email | Kata Sandi Default | Lingkup Akses Antarmuka |
|---|---|---|---|
| **SuperAdmin** | `superadmin@gmail.com` | `password` | `/superadmin/dashboard` |
| **Admin Verifikator** | `admin@gmail.com` | `password` | `/admin/dashboard` |
| **Pembimbing Lapangan** | `pembimbing@gmail.com` | `password` | `/pembimbing/dashboard` |
| **Peserta Magang** | `peserta@gmail.com` | `password` | `/peserta/dashboard` |

> [!TIP]
> Jika Anda ingin menguji alur pendaftaran dari awal sebagai pemohon baru, buka rute `/register` dan lakukan registrasi mandiri menggunakan alamat email baru.

---

## 5. Pemeriksaan Kesiapan Awal (Health Check)

Setelah server menyala, buka peramban Anda dan periksa endpoint berikut:
1. **Portal Publik**: `http://localhost:8000/` (Pastikan landing page memuat kuota real-time dan animasi preloader berjalan).
2. **Kesehatan Sistem**: `http://localhost:8000/health` (Harus mengembalikan status HTTP 200 OK dengan laporan konektivitas database).
3. **Login Akun**: Masuk menggunakan akun `superadmin@gmail.com` untuk memverifikasi dashboard administratif.
