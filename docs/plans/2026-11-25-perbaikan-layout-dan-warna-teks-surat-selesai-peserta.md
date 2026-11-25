# Plan Perbaikan Layout Sidebar Toggle & Warna Teks Banner Halaman Surat Selesai Magang Peserta

**Tanggal:** 2026-11-25  
**Target Branch:** `test-2`  
**Status:** Menunggu Persetujuan Pengguna

---

## 1. Tujuan
Memperbaiki dua masalah antarmuka dan interaksi pada halaman **Surat Selesai Magang** di portal peserta (`/peserta/selesai-magang`):
1. **Tombol garis tiga (hamburger / sidebar toggle) di topbar kembali berfungsi** baik di mode desktop (collapse/expand sidebar) maupun mobile (buka/tutup drawer).
2. **Warna teks judul utama `"Status Kelulusan & Surat Selesai Magang"` pada banner biru berubah menjadi putih (`#FFFFFF`)** sesuai tema MagKom agar kontras dan terbaca jelas di atas latar biru tua.

---

## 2. Analisis Akar Masalah (Root Cause)
Berdasarkan inspeksi codebase:
1. **Tombol Garis Tiga Tidak Merespons Klik**:
   - File `resources/views/peserta/selesai-magang/index.blade.php` saat ini menggunakan `@extends('layouts.dashboard')` dan `@section('topbar_title', ...)`, berbeda dengan seluruh halaman peserta lainnya (`dashboard`, `logbook`, `pendaftaran`, `pengaturan`, `profil`, `tugas`) yang menggunakan `@extends('layouts.peserta')` dan `@section('page_title', ...)`.
   - `layouts.dashboard` memasang `data-action="toggle-sidebar"` yang dikelola oleh `resources/js/layouts/dashboard.js`. Namun `dashboard.js` (baris 7 & 249) secara eksplisit mengabaikan seluruh URL `/peserta/*` (`if (/^\/peserta($|\/)/i.test(window.location.pathname)) return;`) karena interaksi peserta dikelola oleh `resources/js/layouts/peserta-layout.js` melalui `data-action="toggle-sidebar-peserta"`. Akibatnya, klik pada tombol hamburger di halaman `/peserta/selesai-magang` tidak ditangani oleh script mana pun.
2. **Judul Banner Berwarna Hitam**:
   - Pada `resources/views/peserta/selesai-magang/index.blade.php` baris 15, elemen `<h1>Status Kelulusan &amp; Surat Selesai Magang</h1>` tidak memiliki warna teks eksplisit (`color: #FFFFFF`), sehingga mewarisi warna teks heading default yang gelap/hitam di atas latar gradien biru tua (`#01347C` → `#003566`).

---

## 3. Asumsi & Pertanyaan Terbuka
- **Asumsi Kerja Logis**:
  - Halaman `peserta/selesai-magang/index.blade.php` memang seharusnya mengikuti standar layout portal peserta (`layouts.peserta`) agar konsisten dengan halaman peserta lainnya (`dashboard`, `tugas`, `logbook`, `pendaftaran`, `profil`, `pengaturan`).
  - Sesuai aturan proyek (`AGENTS.md` bagian 3 & 4), styling baru atau perbaikan warna ditempatkan di `resources/css/` dan di-import melalui entry point Vite yang sudah ada (`resources/css/app.css`) tanpa membuat entry point Vite baru.
- **Pertanyaan Terbuka**: Tidak ada ambiguitas kritis.

---

## 4. Evaluasi Opsi Arsitektur

| Kriteria | Opsi A: Migrasikan `selesai-magang/index.blade.php` ke `layouts.peserta` + CSS scoped di `resources/css/` **(Rekomendasi)** | Opsi B: Tetap pakai `layouts.dashboard` dan ubah pengecualian di `dashboard.js` + inline `color: #FFFFFF` di Blade |
| :--- | :--- | :--- |
| **Konsistensi Portal** | Selaras 100% dengan seluruh halaman peserta lain (`layouts.peserta` & `peserta-layout.js`) | Halaman peserta terbelah dua layout berbeda (`dashboard` vs `peserta`) |
| **Kepatuhan `AGENTS.md`** | Mematuhi aturan Blade/CSS (styling di `resources/css/`, tanpa inline style baru) | Menambah inline style di Blade dan mengotori logika `dashboard.js` |
| **Risiko Regresi** | Rendah, tidak menyentuh `dashboard.js` yang dipakai Admin/Pembimbing/SuperAdmin | Tinggi, berisiko memengaruhi behavior sidebar role lain |

**Keputusan**: Memilih **Opsi A**.

---

## 5. Daftar Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/selesai-magang/index.blade.php` | Modify | Ubah `@extends('layouts.dashboard')` menjadi `@extends('layouts.peserta')`, `@section('topbar_title', ...)` menjadi `@section('page_title', ...)`, dan tambahkan class CSS ter-scope (`peserta-selesai-hero-title`) pada elemen `<h1>` banner. *(Memerlukan persetujuan perubahan Blade sesuai `AGENTS.md` 3.3)* |
| `resources/css/pages/peserta/selesai-magang.css` | Create | Definisikan style `.peserta-selesai-container .peserta-selesai-hero-title { color: #FFFFFF; }` dengan spesifisitas aman untuk light & dark mode agar teks judul selalu putih di atas banner biru tua MagKom. |
| `resources/css/app.css` | Modify | Tambahkan `@import './pages/peserta/selesai-magang.css';` mengikuti pola `profil-show.css`. |
| `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php` | Modify | Tambahkan feature test untuk memverifikasi halaman `peserta.selesai-magang.index` merender layout peserta (`data-action="toggle-sidebar-peserta"`) dan class judul banner (`peserta-selesai-hero-title`). |

---

## 6. Langkah Implementasi
- [x] **Langkah 1 (CSS):** Buat file `resources/css/pages/peserta/selesai-magang.css` dengan rule ter-scope untuk `.peserta-selesai-hero-title` berwarna `#FFFFFF` (termasuk di dark mode agar tidak tertimpa variabel theme).
- [x] **Langkah 2 (CSS Entry Point):** Import `./pages/peserta/selesai-magang.css` ke dalam `resources/css/app.css`.
- [x] **Langkah 3 (Blade Markup - setelah disetujui):** Perbarui `resources/views/peserta/selesai-magang/index.blade.php`:
  - Ubah `@extends('layouts.dashboard')` → `@extends('layouts.peserta')`
  - Ubah `@section('topbar_title', __('Selesai Magang'))` → `@section('page_title', __('Selesai Magang'))`
  - Tambahkan class `peserta-selesai-hero-title` pada tag `<h1>` banner utama.
- [x] **Langkah 4 (Build Aset):** Jalankan `npm run build` menggunakan Node.js v22 (`D:\laragon\bin\nodejs\node-v22`) untuk mengompilasi CSS terbaru ke `public/build/`.
- [x] **Langkah 5 (Testing):** Tambahkan pengujian pada `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php` untuk memastikan halaman `peserta.selesai-magang.index` status 200, memakai tombol `data-action="toggle-sidebar-peserta"`, dan memiliki class `peserta-selesai-hero-title`.
- [x] **Langkah 6 (Quality Gate):** Jalankan PHPUnit (`php artisan test tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php tests/Feature/SuratSelesaiMagangIndividualTest.php`), Pint (`./vendor/bin/pint --test`), dan verifikasi visual di browser (`http://127.0.0.1:8000/peserta/selesai-magang`).

---

## 7. Strategi Testing
- **Happy Path**:
  - Peserta yang sudah ditempatkan mengakses `GET /peserta/selesai-magang` → HTTP 200, memuat atribut `data-action="toggle-sidebar-peserta"`, judul halaman `"Selesai Magang"`, serta class `peserta-selesai-hero-title`.
  - Verifikasi interaksi klik tombol garis tiga (`#sidebarToggleBtn`) pada viewport desktop (`sidebar-collapsed` toggle) dan mobile (`drawer-open` toggle), serta warna komputasi `<h1>` bernilai putih (`rgb(255, 255, 255)`).
- **Sad Path / Regression**:
  - Peserta belum login diarahkan ke halaman login; role non-peserta tetap ditolak (403); fungsi unduh surat (`peserta.selesai-magang.unduh`) tidak terdampak.

---

## 8. Risiko & Mitigasi
- **Risiko**: Perubahan dari `layouts.dashboard` ke `layouts.peserta` dapat mengubah padding kontainer atau tampilan ringan di halaman `selesai-magang`.
  - **Mitigasi**: `layouts.peserta` adalah layout resmi seluruh portal peserta (identik dengan `tugas/index`, `logbook/index`, `dashboard`), dan verifikasi visual langsung di browser akan memastikan tata letak tetap rapi di desktop maupun mobile.
- **Risiko**: Aturan CSS global `h1` atau `[data-theme="dark"] h1` menimpa warna putih.
  - **Mitigasi**: Gunakan selector spesifik `.peserta-selesai-container .peserta-selesai-hero-title` serta selector `html[data-theme="dark"] .peserta-selesai-container .peserta-selesai-hero-title` di `resources/css/pages/peserta/selesai-magang.css`.

---

## 9. Kriteria Selesai (Definition of Done)
- [ ] Tombol garis tiga (`#sidebarToggleBtn`) di halaman `/peserta/selesai-magang` dapat diklik untuk membuka/menutup sidebar di desktop maupun mobile.
- [ ] Teks `"Status Kelulusan & Surat Selesai Magang"` tampil berwarna putih (`#FFFFFF`) di atas banner biru tua MagKom.
- [ ] Aset Vite berhasil di-build tanpa error (`npm run build`).
- [ ] Test suite terkait (`AdminSuratSelesaiIndividuKelompokTest`, `SuratSelesaiMagangIndividualTest`) dan `./vendor/bin/pint --test` lulus 100%.

---

## 10. Saran Lanjutan (Di Luar Scope Utama)
- Pada `resources/views/layouts/peserta.blade.php` baris 194–197 terdapat tag `</span></div>` duplikat sisa dari item menu terkunci pada blok `@else`; dapat dirapikan pada tugas pemeliharaan terpisah.
- Inline `style="..."` yang sudah ada di `resources/views/peserta/selesai-magang/index.blade.php` dapat dipindahkan seluruhnya ke `resources/css/pages/peserta/selesai-magang.css` pada kesempatan terpisah jika diinginkan.

