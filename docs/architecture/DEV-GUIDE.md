> Diperbarui: 2026-10-13 | commit: 1553e56 | mode: A

# Panduan Praktis Pengembang (DEV-GUIDE.md)

Panduan ini disusun untuk membantu pengembang baru melakukan orientasi cepat, memahami alur kerja baku di Portal MagKom, dan menghindari kesalahan umum saat berkontribusi.

---

## 1. Menjalankan Lingkungan Lokal

### Prasyarat
- PHP 8.2+ dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `gd`, `zip`
- Composer 2.x
- Node.js 20+ / 24+ & npm
- MySQL / MariaDB Server

### Langkah Menjalankan:
```bash
# 1. Pasang dependensi PHP dan Node
composer install
npm install

# 2. Siapkan file konfigurasi lingkungan
cp .env.example .env
php artisan key:generate

# 3. Jalankan migrasi dan seeder awal
php artisan migrate --seed

# 4. Jalankan Dev Server Laravel & Vite secara paralel
php artisan serve
npm run dev
```
Akses antarmuka web di: `http://localhost:8000`.

---

## 2. Konvensi Menambah Fitur Baru

### A. Menambah Halaman / Route Baru
1. Tentukan namespace domain controller yang sesuai di `app/Http/Controllers/` (`Admin/`, `Pembimbing/`, `Peserta/`, `SuperAdmin/`, atau `Public/`).
2. Buat controller dengan strict typing:
   ```php
   <?php
   declare(strict_types=1);
   namespace App\Http\Controllers\Peserta;
   
   use App\Http\Controllers\Controller;
   use Illuminate\View\View;
   
   class ContohFiturController extends Controller
   {
       public function index(): View
       {
           return view('peserta.contoh-fitur.index');
       }
   }
   ```
3. Daftarkan route di `routes/web.php` dalam grup middleware dan prefix yang tepat:
   - Peserta: grup `role:peserta`
   - Pembimbing: grup `role:pembimbing,admin,superadmin`
   - Admin: grup `role:admin,superadmin`
   - SuperAdmin: grup `role:superadmin`
4. Selalu beri penamaan eksplisit (`->name('peserta.contoh.index')`).

### B. Menambah Komponen UI Baru
1. Buat berkas blade di `resources/views/components/<nama-komponen>.blade.php`.
2. Gunakan directive `@props([...])` untuk mendeklarasikan properti dengan nilai bawaan.
3. Gunakan CSS Custom Properties (`var(--blue-600)`, `var(--neutral-100)`, dll.) agar warna selalu seragam dengan DS-MagKom.
4. Panggil komponen di view menggunakan sintaks `<x-nama-komponen ... />`.

### C. Menambah Endpoint API Baru
1. Daftarkan di `routes/api.php`.
2. Bungkus dengan middleware `auth:sanctum` jika membutuhkan sesi pengguna terotentikasi.
3. Kembalikan response berformat JSON standar:
   ```json
   {
     "success": true,
     "message": "Data berhasil dimuat",
     "data": {}
   }
   ```

### D. Menambah Tabel & Model Database Baru
1. Buat migration dan model sekaligus:
   ```bash
   php artisan make:model NamaEntitas -m
   ```
2. Definisikan kolom dengan tipe data presisi, foreign key ber-cascade atau restrict, dan index pada kolom pencarian sering.
3. Pada Model Eloquent (`app/Models/NamaEntitas.php`):
   - Deklarasikan `$fillable` secara eksplisit (dilarang menggunakan `$guarded = []` kosong untuk mencegah vulnerability Mass Assignment).
   - Definisikan relasi method (`belongsTo`, `hasMany`).
   - Sambungkan event audit trail jika entitas ini bernilai transaksional sensitif.

---

## 3. Jebakan Umum & Peringatan Penting (Common Gotchas)

1. **JANGAN Menaruh Berkas Pelamar di `public/`**:
   - Berkas pendaftaran peserta (KTP, KTM, Bukti Asuransi) mengandung data pribadi sensitif (UU PDP). Simpan selalu via `DocumentStorageService` di private disk (`storage/app/berkas/`), dan hanya sediakan akses via `temporarySignedRoute`.
2. **Jangan Lupa `must_change_password` pada Staf**:
   - Seluruh grup rute staf internal (`admin`, `pembimbing`, `superadmin`) diproteksi middleware `must_change_password`. Jika akun staf di-generate otomatis dengan password default, mereka wajib diarahkan ke rute `/password/ganti` sebelum bisa membuka dashboard.
3. **Format Tanggal & Zona Waktu**:
   - Seluruh perbandingan waktu dan deadline kuota pendaftaran mengacu pada zona waktu `Asia/Jakarta` (WIB). Gunakan `Carbon::now('Asia/Jakarta')` untuk validasi batas waktu pendaftaran.
4. **Pembaruan Dokumentasi Otomatis**:
   - Setelah Anda menambah route, model, atau layout baru, selalu jalankan workflow `/update-docs` agar dokumen di `docs/architecture/` tetap sinkron dengan kode sumber.
