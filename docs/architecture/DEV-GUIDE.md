> Diperbarui: 2026-11-02 | audit: Task-DOC-004 | status: Synced

# Panduan Praktis Pengembang (DEV-GUIDE.md)

> [!IMPORTANT]
> **Dokumen Induk Panduan Pengembangan:**  
> Untuk panduan langkah-demi-langkah setup lingkungan, git workflow, dan quality gates (Pint & PHPStan), silakan mengacu langsung pada dokumen induk:  
> 📖 [**`docs/DEVELOPMENT-GUIDE.md`**](../DEVELOPMENT-GUIDE.md)

---

## 1. Menjalankan Lingkungan Lokal

### Prasyarat Wajib
- **PHP 8.3** (Platform locked di `composer.json`)
- **Composer 2.2+**
- **Node.js v20+** & **npm 10+**
- **MySQL 8.0+ / MariaDB 10.4+ (InnoDB)**

### Menjalankan Server Pengembangan:
```bash
# 1. Pastikan dependensi terpasang
composer install
npm install

# 2. Jalankan Laravel & Vite (Hot Module Replacement)
# Terminal 1:
php artisan serve --port=8000
# Terminal 2:
npm run dev
```
Akses antarmuka web di: `http://localhost:8000` (atau port yang ditentukan pada environment Anda).

---

## 2. Konvensi Menambah Fitur Baru

Sesuai aturan arsitektur permanen [`AGENTS.md`](../../.agents/rules/AGENTS.md), patuhi pemisahan tanggung jawab berikut:

### A. Backend Layered Architecture
1. **Route (`routes/web.php`)**: Daftarkan rute dalam grup middleware yang tepat (`role:peserta`, `role:pembimbing`, `role:admin`, atau `role:superadmin`). Selalu beri nama rute eksplisit (`->name(...)`).
2. **Controller (`app/Http/Controllers/`)**: Bersifat tipis (*thin controller*). Hanya bertugas menerima request, validasi via Form Request, otorisasi via Policy, mendelegasikan ke Service, dan mengembalikan View/Redirect.
3. **Form Request (`app/Http/Requests/`)**: Validasi input formulir dan otorisasi dasar.
4. **Service (`app/Services/`)**: Pusat aturan bisnis, mutasi multi-tabel dengan DB transaction (`lockForUpdate`), dan penulisan jejak audit.
5. **Model (`app/Models/`)**: Definisi relasi Eloquent, casting (`$casts`), accessor/mutator, dan scope. Wajib mendaftarkan `$fillable` eksplisit (dilarang `$guarded = []`).

### B. Frontend Asset Architecture
1. **DILARANG MENULIS CSS ATAU JS DI BLADE**:
   - Dilarang membuat tag `<style>` atau `<script>` di dalam file Blade (`resources/views/**/*.blade.php`).
   - Dilarang menggunakan inline event listener (`onclick`, `onchange`, `onsubmit`, dll.).
   - Dilarang menambahkan atribut `style="..."` statis di Blade.
2. **CSS Berada di `resources/css/`**:
   - Manfaatkan design tokens di `resources/css/base/variables.css`.
   - Gunakan file CSS yang scoped sesuai komponen/layout/halaman.
3. **JavaScript Berada di `resources/js/`**:
   - Gunakan ES Modules modular (`import`/`export`).
   - Gunakan `addEventListener` terstruktur dan cegah multiple attachment listener.
4. **Asset Build Vite**:
   - Semua asset frontend dibundle melalui Vite 6 (`vite.config.js`). Jalankan `npm run build` sebelum rilis.

---

## 3. Peringatan Keamanan & Integritas Data

1. **Berkas Privat & UU PDP**:
   - Berkas pendaftar (KTP, KTM, Bukti Asuransi, Proposal) wajib disimpan di private disk lokal (`storage/app/private/documents/`).
   - Akses unduhan hanya melalui controller privat dengan Policy check dan `URL::temporarySignedRoute()`. Jangan pernah mengekspos berkas ke folder publik `public/`.
2. **Pessimistic Locking untuk Kuota**:
   - Setiap mutasi kuota atau slot khusus wajib menggunakan `lockForUpdate()` di dalam transaksi database untuk mencegah kontensi *race condition*.
3. **Audit Trail Masking**:
   - Seluruh mutasi data transaksional otomatis dicatat ke `audit_log`. Parameter sensitif (password, token, credential) otomatis di-mask oleh `AuditService`.
