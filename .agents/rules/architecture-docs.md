# Architecture Documentation & Inspection Rule

Aturan ini mengikat agen AI saat berinteraksi dengan arsitektur proyek dan dokumentasi di repositori ini.

## 1. Konsultasi Dokumentasi Awal (Pre-Flight Check)
- Sebelum memulai tugas pengembangan, refaktorisasi, atau pemecahan bug yang menyentuh lebih dari 2 file, agen **WAJIB** membaca:
  - `docs/architecture/OVERVIEW.md`
  - `docs/architecture/ROUTES.md` (jika tersedia)
- Pahami konteks arsitektur, rute yang ada, dan konvensi proyek sebelum merancang perubahan kode.

## 2. Verifikasi Integritas Kode (Trust but Verify)
- Jangan pernah menganggap dokumentasi selalu 100% mutakhir.
- Selalu verifikasi temuan dokumentasi dengan file kode aktual (`app/`, `resources/`, `routes/`, dll.) sebelum mengambil keputusan penting.

## 3. Sinkronisasi Pasca Perubahan Struktural
- Setelah menyelesaikan perubahan struktural yang mencakup:
  - Penambahan atau perubahan Route (`routes/web.php`, `routes/api.php`)
  - Penambahan/modifikasi komponen utama atau layout (`resources/views/`)
  - Perubahan skema database (`database/migrations/`, Model Eloquent)
  - Penambahan variabel environment baru di konfigurasi
- Agen **WAJIB** mengingatkan pengguna untuk menjalankan `/update-docs`, atau secara proaktif menawarkan untuk memperbarui dokumen di `docs/architecture/`.

## 4. Keamanan & Kerahasiaan
- Dilarang keras membaca atau menampilkan isi nilai rahasia (secrets, passwords, API keys) dari file `.env`.
- Dokumentasi hanya boleh mencatat nama kunci environment variable beserta kegunaannya.
