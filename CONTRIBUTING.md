# Panduan Kontribusi Pengembang (Contributing Guide)
## MagKom — Portal Manajemen Magang Dinas Komunikasi dan Informatika

Terima kasih atas ketertarikan Anda untuk berkontribusi pada pengembangan sistem **MagKom**. Dokumen ini merangkum alur kerja, standar kode, dan prinsip arsitektur yang wajib dipatuhi oleh seluruh pengembang.

---

## 1. Alur Kerja Git & Percabangan (Git Workflow)

1. **Branch Utama**:
   - `main`: Branch produksi yang selalu berada dalam kondisi stabil, bersih, dan siap rilis.
   - Jangan melakukan commit langsung ke `main`.
2. **Penamaan Branch Fitur / Perbaikan**:
   - Fitur baru: `feat/nama-fitur` (misal: `feat/integrasi-notifikasi-wa`)
   - Perbaikan bug: `fix/deskripsi-bug` (misal: `fix/kuota-race-condition`)
   - Refactoring: `refactor/nama-modul`
3. **Format Pesan Commit (Conventional Commits)**:
   Gunakan format imperatif standar:
   - `feat(...)`: Penambahan fungsionalitas atau modul baru.
   - `fix(...)`: Perbaikan bug atau celah logika.
   - `refactor(...)`: Penataan ulang struktur kode tanpa mengubah fungsionalitas.
   - `style(...)`: Penyesuaian formatting kode, Pint, spasi, CSS tokens.
   - `test(...)`: Penambahan atau penyesuaian unit/feature test.
   - `docs(...)`: Pembaruan dokumentasi teknis atau README.
   - `chore(...)`: Pembaruan dependensi, konfigurasi CI/CD, atau task penunjang.

---

## 2. Standar Kode Backend (PHP 8.3 & Laravel 10)

1. **Pola Arsitektur (Skinny Controllers)**:
   ```
   Route → Middleware/Policy → Form Request → Controller → Action / Query / Service → Model → Database
   ```
   - **Controller**: Hanya menangani orkestrasi HTTP dan pengembalian respons (maksimal 30–50 baris per method).
   - **Form Request**: Validasi data input dan otorisasi dasar pengguna.
   - **Action (`app/Actions/`)**: Mengisolasi operasi mutasi tunggal (Create/Update/Delete).
   - **Query (`app/Queries/`)**: Mengisolasi pemfilteran data, pencarian, dan paginasi yang kompleks.
   - **Service (`app/Services/`)**: Mengelola logika domain lintas-entitas dan transaksi multi-tabel.
2. **Ketik Ketat & Standar PSR-12**:
   - Selalu gunakan `declare(strict_types=1);` di baris pertama setiap file PHP baru.
   - Gunakan *type hinting* eksplisit pada parameter fungsi dan nilai kembali (*return type*).
3. **Audit Trail**:
   - Setiap mutasi data penting pada entitas (Pendaftaran, Penempatan, Kuota, Pengguna) wajib memanggil `AuditService` untuk pencatatan jejak audit forensik.

---

## 3. Standar Frontend & Presentasi Blade

1. **Blade Hanya Lapisan Markup**:
   - **DILARANG** menulis tag `<style>` di dalam file `.blade.php`.
   - **DILARANG** menulis tag `<script>` di dalam file `.blade.php`.
   - **DILARANG** menggunakan inline event handlers (seperti `onclick="..."`, `onchange="..."`).
   - Letakkan seluruh styling di `resources/css/` dan modul interaksi di `resources/js/`.
2. **Sistem Desain & Design Tokens**:
   - Gunakan variabel CSS resmi proyek (`--magkom-primary-blue`, `--neutral-*`, `--yellow-*`, `--blue-*`).
   - Jangan melakukan *hardcode* kode warna HEX baru jika token yang ada sudah mencukupi.
3. **Aset Modular Vite**:
   - Pastikan setiap script JavaScript ditulis menggunakan ES Modules (`import`/`export`) dan didaftarkan melalui `vite.config.js` jika berupa *entry point* independen.

---

## 4. Keamanan & Penanganan Input (OWASP)

- Gunakan parameter binding PDO bawaan Eloquent; hindari *raw SQL concatenation*.
- Validasi kepemilikan objek dan hak akses menggunakan Laravel Policy (`Gate::authorize` / `$this->authorize`).
- Lindungi seluruh form mutasi dengan token CSRF (`@csrf`).
- Terapkan *atomic concurrency locking* (`lockForUpdate()`) di dalam database transaction saat memanipulasi kuota atau slot terbatas.

---

## 5. Pemeriksaan Kualitas & Uji Otomatis

Sebelum mengajukan *Pull Request*, jalankan tahapan pengujian lokal berikut:

```bash
# 1. Jalankan seluruh suite tes otomatis (wajib 100% hijau, 740+ tests)
php artisan test

# 2. Analisis statis PHPStan / Larastan
./vendor/bin/phpstan analyse --memory-limit=1G

# 3. Format kode sesuai standar PSR-12 dengan Laravel Pint
./vendor/bin/pint --test

# 4. Kompilasi aset frontend
npm run build
```
Semua pemeriksaan di atas harus lolos tanpa *warning* kritis sebelum kode dapat digabungkan ke branch utama.

---

## 6. Struktur Tim Pengembang & Kewenangan Review Pull Request

Setiap *Pull Request* yang diajukan wajib mereferensikan issue/task dan di-review oleh kolaborator sesuai domain tanggung jawab:

| Anggota Tim | Peran | Domain Review & Kewenangan |
|---|---|---|
| **User (Lead Architect)** | *Lead Architect & Product Owner* | • **Wewenang Mutlak Approval & Merge ke `main`**.<br>• Review integritas aturan bisnis (`PRD`), arsitektur database, dan security boundary. |
| **Kolaborator 1 (Backend/Security)** | *Senior Backend & Security Engineer* | • Review teknis seluruh kode di `app/Actions/`, `app/Queries/`, `app/Services/`, `database/`, dan `tests/`.<br>• Verifikasi pencegahan celah keamanan OWASP dan concurrency locking. |
| **Kolaborator 2 (Frontend/Docs)** | *Frontend Specialist & UI/UX / Docs Engineer* | • Review teknis antarmuka di `resources/css/`, `resources/js/`, dan `resources/views/`.<br>• Penegakan aturan **Zero Inline CSS/JS di Blade** dan pembaruan living documentation (`docs/`). |
