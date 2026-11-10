# Rencana Kerja: Pemisahan Navigasi Kategori, Kondisional Tombol Reset, dan Pemulihan File Controller

**Dokumen Rencana**: `docs/plans/2026-11-09-pemisahan-navigasi-kategori-kondisional-reset-dan-solusi-penempatan-controller.md`  
**Tanggal**: 9 November 2026  
**Status**: Draf Perencanaan (Menunggu Persetujuan Pengguna)  
**Target Modul**:
- `resources/views/admin/pendaftaran/index.blade.php` (Pemisahan Navigasi Kategori & Tombol Reset Kondisional)
- `resources/views/admin/penempatan/index.blade.php` (Tombol Reset Kondisional pada Filter Antrean & Riwayat)
- `app/Http/Controllers/Admin/PenempatanController.php` (Pemulihan File Fisik dari Handle Lock Windows)

---

## 1. Analisis Kebutuhan & Masalah (Berdasarkan 3 Tangkapan Layar)

### 1.1 Masalah 1: Pemisahan Navigasi Kategori dari Frame Card Utama (SS 1)
- **Kondisi Saat Ini**:
  - Tombol navigasi kategori (`Semua Kategori`, `Individu`, `Kelompok`) saat ini diletakkan di dalam komponen `<x-gentelella.x-panel>` (frame kartu utama dengan garis border biru di atasnya).
  - Akibatnya, bar navigasi ini menyatu dalam satu kontainer bersama form pencarian dan tabel data, membuat struktur antarmuka terkesan bertumpuk di bawah deretan KPI Tile Card.
- **Kebutuhan Pengguna**:
  - Memisahkan bar navigasi kategori tersebut agar **berdiri sendiri di luar frame kartu utama**, tepat di bawah KPI row dan di atas frame kartu filter/tabel.
  - Frame kartu utama (`x-panel`) di bawahnya hanya akan memuat toolbar pencarian/filter dan tabel data pendaftaran.

---

### 1.2 Masalah 2: Tombol Reset Tampil Kondisional (SS 2)
- **Kondisi Saat Ini**:
  - Tombol `Reset` (berwarna putih di sebelah tombol filter biru) selalu tampil secara statis (*manjer terus*) meskipun form filter masih kosong atau belum ada kata kunci pencarian yang dimasukkan pengguna.
- **Kebutuhan Pengguna**:
  - Tombol `Reset` disembunyikan secara default saat halaman baru dimuat atau tidak ada filter/pencarian yang aktif.
  - Tombol `Reset` **hanya muncul/tampil ketika pengguna sedang melakukan pencarian atau menerapkan filter** (misalnya parameter `q`, `bulan_magang_id`, `jurusan_id`, atau `sort !== 'terbaru'` aktif).
  - Saat tombol Reset diklik atau saat filter dikosongkan, tombol tersebut akan otomatis hilang kembali.

---

### 1.3 Masalah 3: ErrorException Failed to Open Stream PenempatanController.php (SS 3)
- **Gejala pada Browser**:
  ```text
  ErrorException PHP 8.3.33 10.50.3
  include(D:\laragon\www\MAGKOM\vendor\composer/../../app/Http/Controllers/Admin/PenempatanController.php): 
  Failed to open stream: No such file or directory
  at D:\laragon\www\MAGKOM\public\index.php:51
  GET http://127.0.0.1:8000/admin/penempatan
  ```
- **Akar Penyebab (Root Cause)**:
  - File `PenempatanController.php` saat ini sedang terbuka di tab editor aktif Antigravity IDE pengguna (`Active Document: d:\laragon\www\MAGKOM\app\Http\Controllers\Admin\PenempatanController.php`).
  - Ketika proses `git rebase` / sinkronisasi remote berjalan sebelumnya, sistem berkas Windows NTFS menempatkan berkas ini ke dalam status **"Delete-Pending Tombstone"** karena ada *handle* aktif dari editor tab yang menahannya.
  - Seluruh kode controller sebenarnya **sudah tersimpan aman 100% di commit Git lokal (`HEAD`) dan sudah berhasil ter-push ke GitHub remote**. Namun di disk lokal Windows, berkas tersebut berstatus *pending flush*.
- **Solusi Tuntas**:
  1. Pengguna cukup beralih ke tab editor `PenempatanController.php` di Antigravity lalu tekan **Ctrl + S** (Save) atau **Tutup tab tersebut (klik tanda X)**.
  2. Begitu tab ditutup/disimpan, *handle lock* Windows akan segera lepas, dan perintah pemulihan `git checkout HEAD -- app/Http/Controllers/Admin/PenempatanController.php` dapat langsung menulis ulang berkas fisik ke disk dalam hitungan detik.
  3. Setelah berkas fisik kembali ada di disk, halaman `http://127.0.0.1:8000/admin/penempatan` akan langsung berjalan normal dan lancar.

---

## 2. Rencana Perubahan Berkas

| Berkas | Aksi | Ringkasan Perubahan |
|---|:---:|---|
| `resources/views/admin/pendaftaran/index.blade.php` | Modify | 1. Pindahkan blok navigasi `.adm-category-nav-bar` keluar dari `<x-gentelella.x-panel>`, posisikan mandiri di atas panel.<br>2. Tambahkan kondisi `@if($hasActiveSearch)` pada tombol `adm-btn-filter-reset` agar hanya tampil saat ada pencarian/filter aktif. |
| `resources/views/admin/penempatan/index.blade.php` | Modify | Terapkan logika kondisional serupa pada tombol Reset tab `sudah` dan tab `belum` sehingga hanya tampil saat parameter pencarian/filter diisi. |
| `app/Http/Controllers/Admin/PenempatanController.php` | Restore | Pulihkan berkas fisik ke disk lokal setelah handle editor dilepas. |
| `tests/Feature/AdminChildPagesVisualTest.php` | Modify | Pastikan assertion visual test tetap mencakup tombol reset kondisional dan tab kategori yang berdiri sendiri. |

---

## 3. Langkah-Langkah Eksekusi Terstruktur

- [ ] **Langkah 1 (Atasi Masalah SS 3 Terlebih Dahulu)**:
  - Instruksikan pengguna untuk menutup tab `PenempatanController.php` di editor (klik tanda X) atau tekan Ctrl+S.
  - Jalankan pemulihan berkas:
    ```powershell
    git checkout HEAD -- app/Http/Controllers/Admin/PenempatanController.php
    ```
  - Verifikasi bahwa file fisik telah kembali (`Test-Path app/Http/Controllers/Admin/PenempatanController.php` bernilai `True`) dan jalankan kembali dev server `artisan serve`.
- [ ] **Langkah 2 (Pemisahan Navigasi Kategori - SS 1)**:
  - Buka `resources/views/admin/pendaftaran/index.blade.php`.
  - Pindahkan blok PHP pembantu `$makeFilterUrl` dan kontainer `<div class="adm-category-nav-bar">` keluar dari `<x-gentelella.x-panel>`.
  - Letakkan tepat di bawah `<div class="row tile_count adm-tile-count-grid">` dan sebelum `<x-gentelella.x-panel>`.
  - Sesuaikan margin bawah (`margin-bottom: 1rem`) agar proporsional dan tidak menempel ke kartu di bawahnya.
- [ ] **Langkah 3 (Kondisional Tombol Reset - SS 2)**:
  - Definisikan variabel pendeteksi filter aktif:
    ```php
    $hasActiveSearch = request()->filled('q') 
        || request()->filled('bulan_magang_id') 
        || request()->filled('jurusan_id') 
        || (request()->filled('sort') && request('sort') !== 'terbaru');
    ```
  - Bungkus tombol Reset dengan `@if($hasActiveSearch)`:
    ```blade
    @if($hasActiveSearch)
        <a href="{{ $makeFilterUrl(null, null, true) }}" class="adm-btn-filter-reset" title="Kembalikan pencarian ke kondisi awal">
            Reset
        </a>
    @endif
    ```
  - Terapkan logika yang sama di `resources/views/admin/penempatan/index.blade.php`.
- [ ] **Langkah 4 (Pengujian & Quality Gates)**:
  - Jalankan `php artisan test --filter AdminChildPagesVisualTest`
  - Jalankan `php artisan test --filter AdminPenempatanDuaTahapTest`
  - Jalankan `./vendor/bin/pint --test`
  - Jalankan `npm run build`
- [ ] **Langkah 5 (Verifikasi Visual di Browser)**:
  - Buka `http://127.0.0.1:8000/admin/pendaftaran` untuk memastikan bar kategori sudah berdiri sendiri dan tombol reset tersembunyi secara default.
  - Masukkan kata kunci pencarian, klik Terapkan, dan pastikan tombol Reset muncul.
  - Buka `http://127.0.0.1:8000/admin/penempatan` untuk memastikan halaman penempatan terbuka normal tanpa error.

