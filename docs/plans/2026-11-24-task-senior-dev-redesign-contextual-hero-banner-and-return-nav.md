# [TASK / PRD SENIOR DEV] Contextual Sleman Navy Hero Banner & Smart Return Navigation on Detail Penempatan (Surat Selesai Magang Parity)

> **Ticket ID**: `TASK-MAGKOM-SS-009`  
> **Author**: Senior Lead Developer (Antigravity)  
> **Assignee**: Junior Full-Stack Developer / Autonomous Coding AI (Gemini 3.8 Flash)  
> **Status**: `COMPLETED & VERIFIED`  
> **Target Release**: Sprint MagKom v1.1.5  
> **Files Affected**:
> - `resources/views/admin/penempatan/show.blade.php`
> - `resources/views/admin/surat-selesai/index.blade.php`
> - `resources/css/pages/admin/surat-selesai-index.css`
> - `resources/css/pages/admin/penempatan-detail.css`
> - `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php`

---

## 1. Analisis Kebutuhan & Masalah Pengguna (User Request Analysis)

### 1.1 Masukan Pengguna
Pengguna menyampaikan masukan kritis pada halaman detail peserta:
> *"buat ada birunya dong di detail ini dan tampilan ini ketika klik kembali tuh ke surat magang ya bukan penempatan ini beda surat selesai magang kayak gitu"*

Beserta 2 bukti gambar:
1. **Gambar 1 (`media_1795514990676_1ba5ef3c.jpg`)**: Halaman `Detail Penempatan Peserta` yang saat ini memiliki header putih polos standar, dan tombol `[ ← Kembali ]` mengarahkan ke `admin/penempatan` (Daftar Penempatan).
2. **Gambar 2 (`media_1795515070099_85cfdf0d.png`)**: Banner biru Sleman Navy (`.adm-hero-banner`) di halaman `Daftar Surat Selesai Magang` yang diinginkan paritas visualnya di halaman detail.

### 1.2 Root Cause Analysis (RCA)
1. **Masalah Alur Navigasi (Broken Return Context)**:
   - Saat admin sedang mengelola Surat Selesai Magang (misal kelompok `Tim CSIRT & Keamanan Siber Sleman`), admin mengklik tombol `[ Detail ]` pada salah satu anggota tim di tabel untuk melihat rincian individunya.
   - Halaman detail yang terbuka adalah `admin.penempatan.show`.
   - Di halaman tersebut, tombol `[ ← Kembali ]` di-hardcode ke `route('admin.penempatan.index')` (Daftar Penempatan Peserta).
   - Akibatnya: Admin "tersesat" keluar dari alur Surat Selesai Magang dan harus masuk ulang mencari kelompok yang sedang dikerjakannya.
2. **Ketiadaan Aksen Biru Sleman Navy (Visual Inconsistency)**:
   - Halaman `show.blade.php` menggunakan `@section('page_title')` dari layout dashboard biasa yang menghasilkan header putih datar tanpa banner biru Sleman Navy (`.adm-hero-banner`), kontras dengan kemewahan visual halaman Surat Selesai Magang di Gambar 2.

---

## 2. Perbandingan Wireframe (Before vs After)

### 2.1 Kondisi Saat Ini (Before - Header Putih Datar & Navigasi Salah Alur)
```
+-------------------------------------------------------------------------------+
| [Detail Penempatan Peserta]              [← Kembali (ke Penempatan)] [Pratinjau] |
| Rincian data alokasi penempatan peserta...                                    |
| (Latar Putih Polos - Tanpa Aksen Biru Sleman Navy)                            |
+-------------------------------------------------------------------------------+
| [Foto] Farhan Aditya Pratama • Tim CSIRT • Ketua Kelompok                     |
|                                                                               |
| [Informasi Penempatan Peserta]            [Formulir Unggah Surat Selesai]     |
+-------------------------------------------------------------------------------+
```

### 2.2 Desain Baru yang Diinginkan (After - Sleman Navy Hero Banner & Smart Return)
```
+===============================================================================+
| ░░░░░░░░░░ SLEMAN NAVY HERO BANNER (Gradient #01347C -> #003566) ░░░░░░░░░░░░ |
|                                                                               |
|  Unggah Surat Selesai: Farhan Aditya Pratama      [ ← Kembali ke Surat Selesai ]|
|  Tim CSIRT & Keamanan Siber Sleman • Dinas Kominfo  [ 👁️ Pratinjau Surat ]    |
|                                                                               |
+===============================================================================+
| [Foto] Farhan Aditya Pratama • Tim CSIRT • Ketua Kelompok                     |
|                                                                               |
| [Informasi Penempatan Peserta]            [Formulir Unggah Surat Selesai]     |
+-------------------------------------------------------------------------------+
```

---

## 3. Rincian Teknis Implementasi

### 3.1 Passing Query Context dari Halaman Surat Selesai
Di `resources/views/admin/surat-selesai/index.blade.php`:
- Pada tombol `[ Detail ]` di baris anggota kelompok (baris ~685):
  ```blade
  <a href="{{ route('admin.penempatan.show', [$penempatanAnggota, 'ref' => 'surat-selesai', 'tab' => 'kelompok', 'masuk_kelompok' => $pendaftaranKelompok->id]) }}" 
     class="btn-action-small detail" 
     title="{{ __('Lihat Detail Penempatan :nama', ['nama' => $userAnggota?->nama]) }}">
      <span>{{ __('Detail') }}</span>
  </a>
  ```
- Pada nama anggota kelompok (baris ~645):
  ```blade
  <a href="{{ route('admin.penempatan.show', [$penempatanAnggota, 'ref' => 'surat-selesai', 'tab' => 'kelompok', 'masuk_kelompok' => $pendaftaranKelompok->id]) }}" class="member-name-link">
  ```
- Pada link detail peserta di tab individu (jika ada):
  ```blade
  <a href="{{ route('admin.penempatan.show', [$penempatan, 'ref' => 'surat-selesai', 'tab' => 'individu', 'masuk_individu' => $penempatan->id]) }}" ...>
  ```

### 3.2 Smart Back URL & Sleman Navy Hero Banner di `penempatan/show.blade.php`
1. **Deteksi Konteks & URL Kembali Cerdas**:
   ```php
   $isFromSuratSelesai = request('ref') === 'surat-selesai' 
       || !empty(request('masuk_kelompok')) 
       || !empty(request('masuk_individu'));

   if ($isFromSuratSelesai) {
       if (request('tab') === 'kelompok' || !empty(request('masuk_kelompok')) || $penempatan->isKelompok()) {
           $kelompokId = request('masuk_kelompok', $penempatan->pendaftaran_id);
           $backUrl = route('admin.surat-selesai.index', ['tab' => 'kelompok', 'masuk_kelompok' => $kelompokId]);
           $backLabel = __('Kembali ke Surat Selesai Tim');
       } else {
           $backUrl = route('admin.surat-selesai.index', ['tab' => 'individu', 'masuk_individu' => request('masuk_individu', $penempatan->id)]);
           $backLabel = __('Kembali ke Surat Selesai Peserta');
       }
   } else {
       $backUrl = route('admin.penempatan.index');
       $backLabel = __('Kembali');
   }
   ```
2. **Hero Banner Biru Sleman Navy (`.adm-hero-banner`)**:
   - Jika `$isFromSuratSelesai` bernilai `true`:
     - Tampilkan `.adm-hero-banner` di bagian paling atas `@section('content')`:
       - **Judul**: `Detail Penempatan & Surat Selesai: :nama`
       - **Subtitle**: `Rombongan Tim :nama_tim` (jika kelompok) &bull; `Dinas Komunikasi dan Informatika Kabupaten Sleman`
       - **Actions**:
         - Tombol `[ ← Kembali ke Surat Selesai Tim ]` (`.adm-hero-btn-secondary`) yang langsung membawa admin kembali ke tab & kelompok yang tepat di Surat Selesai Magang!
         - Tombol `[ 👁️ Pratinjau Surat ]` (`.adm-hero-btn-secondary`).
         - Tombol `[ ⭳ Unduh Surat Diterima PDF ]` (jika ada).
   - Jika bukan dari Surat Selesai (dibuka dari Penempatan biasa):
     - Tetap gunakan layout standar penempatan dengan tombol kembali ke `admin.penempatan.index`.

### 3.3 CSS Tambahan (`resources/css/pages/admin/surat-selesai-index.css`)
- Styling `.adm-hero-banner`, `.adm-hero-content`, `.adm-hero-text`, `.adm-hero-title`, `.adm-hero-subtitle`, dan `.adm-hero-btn-secondary` ter-load dengan baik di `show.blade.php`.

---

## 4. Checklist Rencana Kerja (Implementation Checklist)

- [x] **Step 1**: Perbarui tautan `[ Detail ]` dan nama anggota di `resources/views/admin/surat-selesai/index.blade.php` agar membawa parameter query `ref=surat-selesai`, `tab=kelompok`, dan `masuk_kelompok={id}`.
- [x] **Step 2**: Modifikasi `resources/views/admin/penempatan/show.blade.php`:
  - Tambahkan kalkulasi `$isFromSuratSelesai`, `$backUrl`, dan `$backLabel`.
  - Pasang Hero Banner Biru Sleman Navy (`adm-hero-banner`) di bagian atas konten ketika dibuka dari konteks Surat Selesai Magang.
  - Sembunyikan `page-header-row` putih jika Hero Banner aktif.
- [x] **Step 3**: Pastikan tombol `[ ← Kembali ]` mengembalikan admin tepat ke view Surat Selesai Magang dengan tab dan fokus kelompok yang aktif.
- [x] **Step 4**: Jalankan `npm run build` untuk validasi asset frontend.
- [x] **Step 5**: Jalankan pengujian otomatis PHPUnit dengan PHP 8.3 (`php artisan test tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php`).
- [x] **Step 6**: Jalankan `./vendor/bin/pint app tests routes database --test` dan update Graphify AST.

---

## 5. Kriteria Selesai (Definition of Done)

- [x] Saat halaman detail peserta dibuka dari Surat Selesai Magang, bagian atas menampilkan **Hero Banner Biru Sleman Navy** (`adm-hero-banner`) yang elegan dan konsisten dengan Gambar 2.
- [x] Tombol `[ ← Kembali ]` di banner biru mengarahkan admin kembali ke **Surat Selesai Magang** (bukan ke Penempatan).
- [x] Konteks kelompok atau peserta yang sedang difokuskan di Surat Selesai Magang tetap terjaga saat tombol kembali diklik.
- [x] Halaman penempatan biasa tetap berfungsi normal jika diakses langsung dari menu Penempatan.
- [x] Tidak ada tag `<style>` atau `<script>` baru di Blade.
- [x] Seluruh automated test (14 tests, 123 assertions) lulus 100% di PHP 8.3.
- [x] Build Vite `npm run build` berhasil tanpa error.
