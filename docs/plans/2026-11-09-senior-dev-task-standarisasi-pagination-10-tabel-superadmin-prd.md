# Senior Developer Task / PRD: Standarisasi Pagination 10 Entri per Halaman pada 10 Tabel & Tampilan SuperAdmin

**Nomor Dokumen**: PRD-MAGKOM-2026-PAGINATION-10  
**Tanggal**: 9 November 2026  
**Penulis**: Senior Full-Stack Software Architect (Antigravity)  
**Target Pelaksana**: Junior Developer / Autonomous AI Agent (Gemini 3.8 Flash / Claude / Cursor)  
**Tingkat Kesulitan**: Medium (Perhatian khusus pada Gotcha Matriks Hak Akses & Tab State)  
**Status**: Ready for Implementation (Menunggu Persetujuan Eksekusi)  

---

## 1. Ringkasan Eksekutif & Latar Belakang

Pada portal internal **MagKom (Portal Manajemen Magang Diskominfo)**, SuperAdmin mengelola berbagai entitas master data, alokasi penempatan, matriks wewenang, pengguna internal, dan bank data arsip historis alumni magang.

Saat ini, batas maksimal data per halaman pada tabel-tabel di panel SuperAdmin belum seragam:
- Sebagian tabel menggunakan `paginate(20)`.
- Sebagian tabel menggunakan `paginate(15)`.
- Sebagian tabel menggunakan `paginate(12)`.
- Sebagian tabel/matriks memuat seluruh data sekaligus dengan `get()` tanpa pagination.
- Pada detail arsip alumni (`arsip/show.blade.php`), tab Riwayat Tugas dan tab Logbook Harian menampilkan seluruh data sekaligus dalam daftar panjang tanpa pemotongan halaman.

### Kebutuhan Pengguna
Standarisasi seluruh tabel dan daftar data pada **10 tampilan SuperAdmin** agar:
1. Maksimal data yang ditampilkan adalah **10 data per halaman**.
2. Jika data lebih dari 10, terdapat kontrol navigasi halaman berurutan (`1, 2, 3, 4, ...`) dengan tombol `« Sebelumnya` dan `Berikutnya »`.
3. Tampilan footer navigasi pagination wajib seragam dan presisi mengikuti referensi visual yang ditentukan:
   - Sisi Kiri: Teks ringkasan entri: `Menampilkan X sampai Y dari Z entri` (dengan angka dicetak tebal / bold).
   - Sisi Kanan: Tombol paginasi interaktif `« Sebelumnya`, nomor halaman dengan nomor aktif berwarna biru navy (`#0B3B70` / `var(--blue-600)`), dan `Berikutnya »`.

---

## 2. Referensi Desain & Sumber Kebenaran (Single Source of Truth)

### 2.1 Referensi Visual
Referensi tangkapan layar tampilan pagination yang menjadi acuan:  
![Referensi Pagination 10 Entri](file:///g:/MAGKOM/docs/plans/2026-11-09-referensi-pagination-10-entri.png)

### 2.2 Template & Styling Eksisting yang Wajib Digunakan
Proyek MagKom **SUDAH** memiliki template Blade dan CSS khusus untuk pagination ini. **JANGAN membuat CSS atau class baru dari nol!**
1. **Template Blade**: [`resources/views/vendor/pagination/default.blade.php`](file:///g:/MAGKOM/resources/views/vendor/pagination/default.blade.php)
   - Otomatis dirender setiap kali memanggil `$paginator->links()`.
   - Menggunakan markup semantik `<nav class="magkom-pagination-nav">`.
   - Menghasilkan ringkasan `<div class="magkom-pagination-summary">Menampilkan <strong>X</strong> sampai <strong>Y</strong> dari <strong>Z</strong> entri</div>`.
   - Menghasilkan list nomor halaman `<ul class="magkom-pagination-list">` dengan class `.magkom-page-item` dan `.magkom-page-link`.
2. **Stylesheet CSS**: [`resources/css/components/pagination.css`](file:///g:/MAGKOM/resources/css/components/pagination.css)
   - Sudah memuat styling `.magkom-pagination-nav`, `.magkom-page-item.active .magkom-page-link` (latar biru, teks putih, border biru), tombol disabled, serta dukungan Dark Mode (`[data-theme="dark"]`).
3. **Konfigurasi Global**: [`app/Providers/AppServiceProvider.php`](file:///g:/MAGKOM/app/Providers/AppServiceProvider.php)
   - Sudah terkonfigurasi `Paginator::defaultView('vendor.pagination.default');`.

---

## 3. Inventaris 10 Modul / Tampilan Target

Berikut adalah tabel pemetaan lengkap 10 modul target yang wajib distandarisasi ke **10 data per halaman**:

| No | Nama Modul / Tampilan | Lokasi Controller | Lokasi View Blade | Status Saat Ini | Target Perubahan |
|---|---|---|---|---|---|
| **1** | Master Program Studi & Jurusan | `app/Http/Controllers/SuperAdmin/JurusanController.php` | `resources/views/superadmin/jurusan/index.blade.php` | `paginate(20)` | Ubah ke `paginate(10)` |
| **2** | Daftar Bidang & Unit Kerja Diskominfo | `app/Http/Controllers/SuperAdmin/BidangController.php` | `resources/views/superadmin/bidang/index.blade.php` | `paginate(20)` | Ubah ke `paginate(10)` |
| **3** | Daftar Alokasi Slot Khusus Mitra | `app/Http/Controllers/SuperAdmin/SlotKhususController.php` | `resources/views/superadmin/slot-khusus/index.blade.php` (Tabel 1) | `paginate(10, ['*'], 'slot_page')` | Pertahankan 10 & pastikan footer selalu muncul |
| **4** | Daftar Peserta Magang Jalur Khusus | `app/Http/Controllers/SuperAdmin/SlotKhususController.php` | `resources/views/superadmin/slot-khusus/index.blade.php` (Tabel 2) | `paginate(10, ['*'], 'peserta_page')` | Pertahankan 10 & pastikan footer selalu muncul |
| **5** | Daftar Bulan Magang | `app/Http/Controllers/SuperAdmin/BulanController.php` | `resources/views/superadmin/bulan/index.blade.php` | `paginate(12)` | Ubah ke `paginate(10)` |
| **6** | Matriks Wewenang & Hak Akses Modul Dinamis | `app/Http/Controllers/SuperAdmin/RoleController.php` | `resources/views/superadmin/role/index.blade.php` | `get()` (tanpa pagination) | Ubah query ke `paginate(10)` + Tambah Footer Pagination + **Perbaiki logic updateMatrix** |
| **7** | Daftar Akun Pengguna Internal | `app/Http/Controllers/SuperAdmin/StafController.php` | `resources/views/superadmin/staf/index.blade.php` | `paginate(15)` | Ubah ke `paginate(10)` |
| **8** | Bank Data Riwayat & Arsip Alumni Magang | `app/Http/Controllers/SuperAdmin/ArsipPesertaController.php` | `resources/views/superadmin/arsip/index.blade.php` | `paginate(15)` | Ubah ke `paginate(10)` |
| **9** | Daftar Tugas & Penugasan Magang (Read-Only) | `app/Http/Controllers/SuperAdmin/ArsipPesertaController.php@show` | `resources/views/superadmin/arsip/show.blade.php` (Tab `tab-tugas`) | Seluruh item dirender langsung | Paginasi 10 item per halaman (Client-Side JS atau Server-Side Tab-Aware) |
| **10** | Rekam Jejak Logbook Harian Magang (Read-Only) | `app/Http/Controllers/SuperAdmin/ArsipPesertaController.php@show` | `resources/views/superadmin/arsip/show.blade.php` (Tab `tab-logbook`) | Seluruh baris dirender langsung | Paginasi 10 item per halaman (Client-Side JS atau Server-Side Tab-Aware) |

---

## 4. Analisis Teknis Senior & 4 Jebakan Kritis (Senior Dev Gotchas)

> [!CAUTION]
> **PERHATIAN KHUSUS UNTUK JUNIOR DEVELOPER / AI AGENT:**
> Jangan terburu-buru hanya mengganti angka pagination! Terdapat 4 jebakan logika sistem yang dapat merusak aplikasi jika tidak dipahami dengan teliti:

### ⚠️ Gotcha #1: Jebakan Terhapusnya Hak Akses pada Matriks Role (`RoleController::updateMatrix`) — SANGAT KRITIS!
- **Kondisi Eksisting**: Saat ini form matriks di `role/index.blade.php` merender seluruh permissions (`Permission::all()`). Method `updateMatrix()` melakukan looping `foreach (Permission::all() as $permission)` dan mencocokkan ID modul dengan data form input `$matrixData = $request->input('matrix', [])`.
- **Bahaya Fatal**: Jika permissions dipaginasi menjadi 10 data per halaman, halaman 1 hanya memuat form input untuk modul 1 s/d 10. Jika SuperAdmin menyimpan perubahan pada halaman 1, modul 11 s/d N **TIDAK AKAN IKUT TERKIRIM** dalam `$matrixData`. Jika kode looping memeriksa seluruh permission di database, modul 11 s/d N akan dianggap "tidak dicentang sama sekali", sehingga hak akses seluruh modul di halaman 2, 3, dan seterusnya **AKAN TERHAPUS / TERCABUT SECARA TIDAK SENGAJA!**
- **Solusi Senior**:
  Pada `RoleController::updateMatrix()`, jangan lakukan looping terhadap `Permission::all()`. Ubah agar **HANYA** memproses permission yang ada di dalam key `$matrixData`:
  ```php
  // Ambil hanya ID permission yang disubmit pada halaman aktif
  $submittedPermissionIds = array_map('intval', array_keys($matrixData));
  $targetPermissions = Permission::whereIn('id', $submittedPermissionIds)->get();

  foreach ($targetPermissions as $permission) {
      // Update role permissions hanya untuk permission yang disubmit di halaman ini!
  }
  ```
  Dengan guardrail ini, mengedit hak akses di halaman 1 tidak akan pernah menyentuh atau menghapus hak akses di halaman 2 atau 3!

---

### ⚠️ Gotcha #2: State Tab Ter-reset pada Detail Arsip (`arsip/show.blade.php`)
- **Kondisi Eksisting**: Halaman `show.blade.php` memiliki 4 tab navigasi interaktif: `Ringkasan & Biodata`, `Riwayat Tugas (X)`, `Logbook Harian (Y)`, dan `Dokumen Pendaftaran (Z)`. Tab default yang aktif adalah Tab 1 (`Ringkasan & Biodata`).
- **Bahaya UX**: Jika kita menerapkan pagination server-side biasa pada Tab 2 (Tugas) atau Tab 3 (Logbook) melalui URL link seperti `?tugas_page=2` atau `?logbook_page=2`, browser akan me-reload seluruh halaman dan kembali menampilkan Tab 1 (`Ringkasan & Biodata`). SuperAdmin harus mengklik ulang tab tugas/logbook setiap kali berpindah halaman. Ini adalah pengalaman pengguna (UX) yang sangat buruk.
- **Solusi Senior (Rekomendasi Utama: Pure Vanilla JS Tab Paginator)**:
  Karena data tugas dan logbook untuk **satu orang peserta alumni** umumnya berkisar antara 1 s/d 40 entri, data sudah dieager-load oleh controller (`tugas.progresLogs` dan `logbooks`).
  Implementasikan fungsi paginasi Vanilla JS modular (mirip `kuota-pagination.js`) yang:
  1. Membagi elemen kartu tugas atau baris tabel logbook menjadi halaman-halaman berisi 10 data.
  2. Merender markup DOM yang **100% identik** dengan `vendor.pagination.default`:
     ```html
     <div class="magkom-pagination-nav">
         <div class="magkom-pagination-summary">
             <span>Menampilkan <strong>1</strong> sampai <strong>10</strong> dari <strong>27</strong> entri</span>
         </div>
         <ul class="magkom-pagination-list">
             <!-- Tombol Sebelumnya, 1, 2, 3, Berikutnya -->
         </ul>
     </div>
     ```
  3. Berpindah halaman seketika (*instant response*) tanpa reload halaman, tanpa kehilangan tab aktif, dan tanpa request database baru!
  *(Jika memilih opsi Server-Side, wajib menambahkan fragment `#tab-tugas` atau `#tab-logbook` pada link paginator dan script onload untuk mengaktifkan tab yang sesuai).*

---

### ⚠️ Gotcha #3: Penampilan Footer Ringkasan Saat Data Kurang dari atau Sama Dengan 10 Entri
- **Kondisi Eksisting**: Pada template `resources/views/vendor/pagination/default.blade.php`, baris pertama berbunyi `@if ($paginator->hasPages())`.
- **Masalah**: Jika data berjumlah 5 entri (<= 10), `$paginator->hasPages()` bernilai `false`, sehingga seluruh footer (termasuk teks `"Menampilkan 1 sampai 5 dari 5 entri"`) tersembunyi.
- **Solusi Senior**:
  Periksa apakah pengguna ingin ringkasan entri tetap muncul meski hanya 1 halaman:
  Di `vendor/pagination/default.blade.php`, kita dapat membungkus seluruh `<nav>` dengan `@if ($paginator->total() > 0)` dan hanya menyembunyikan list tombol `<ul class="magkom-pagination-list">` jika `$paginator->hasPages()` bernilai `false`.
  Di view blade, ganti pengecekan `@if($list->hasPages())` menjadi `@if($list->total() > 0)` agar baris informasi entri selalu tampil konsisten di bagian bawah tabel.

---

### ⚠️ Gotcha #4: Isolasi Parameter Paginasi Ganda pada Satu Halaman (`slot-khusus/index.blade.php`)
- **Kondisi Eksisting**: Halaman `slot-khusus/index.blade.php` memiliki **Dua Tabel Sekaligus**:
  1. Tabel Alokasi Slot Khusus (`slotList`).
  2. Tabel Peserta Jalur Khusus (`pesertaKhususList`).
- **Aturan Wajib**:
  Kedua tabel ini **TIDAK BOLEH** menggunakan parameter URL default `page`.
  - Tabel 1 wajib menggunakan `slot_page`: `$slotList->paginate(10, ['*'], 'slot_page')->withQueryString()`.
  - Tabel 2 wajib menggunakan `peserta_page`: `$pesertaKhususList->paginate(10, ['*'], 'peserta_page')->withQueryString()`.
  - Pemanggilan di Blade wajib menyertakan `.withQueryString()` atau `->appends(request()->query())->links()` agar saat pengguna berpindah halaman di Tabel 2, posisi halaman di Tabel 1 tidak ter-reset ke halaman 1!

---

## 5. Rencana Kerja Detail per Modul (Step-by-Step Implementation Guide)

### Modul 1: Master Program Studi & Jurusan
1. **Controller**: [`app/Http/Controllers/SuperAdmin/JurusanController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/JurusanController.php)
   - Buka method `index()`.
   - Ubah baris 34 dari `->paginate(20);` menjadi `->paginate(10)->withQueryString();`.
2. **View**: [`resources/views/superadmin/jurusan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/jurusan/index.blade.php)
   - Ubah `@if($jurusanList->hasPages())` menjadi `@if($jurusanList->total() > 0)`.
   - Pastikan pemanggilan pagination adalah `{{ $jurusanList->links() }}` di dalam wrapper ber-padding.

---

### Modul 2: Daftar Bidang & Unit Kerja Diskominfo
1. **Controller**: [`app/Http/Controllers/SuperAdmin/BidangController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/BidangController.php)
   - Buka method `index()`.
   - Ubah baris 31 dari `->paginate(20);` menjadi `->paginate(10)->withQueryString();`.
2. **View**: [`resources/views/superadmin/bidang/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/bidang/index.blade.php)
   - Ubah `@if($bidangList->hasPages())` menjadi `@if($bidangList->total() > 0)`.
   - Pastikan pemanggilan pagination adalah `{{ $bidangList->links() }}`.

---

### Modul 3 & Modul 4: Slot Khusus Mitra & Peserta Magang Jalur Khusus
1. **Controller**: [`app/Http/Controllers/SuperAdmin/SlotKhususController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/SlotKhususController.php)
   - Periksa query:
     - Line 30: `$slotList = SlotKhusus::with('creator')->orderBy('bulan_mulai', 'desc')->paginate(10, ['*'], 'slot_page')->withQueryString();` (Pastikan sudah 10 dan `withQueryString()`).
     - Line 49: `$pesertaKhususList = Penempatan::with([...])->orderBy('tgl_mulai', 'desc')->paginate(10, ['*'], 'peserta_page')->withQueryString();` (Pastikan sudah 10 dan `withQueryString()`).
2. **View**: [`resources/views/superadmin/slot-khusus/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/slot-khusus/index.blade.php)
   - Tabel 1: Ganti `@if($slotList->hasPages())` menjadi `@if($slotList->total() > 0)` dan render `{{ $slotList->links() }}`.
   - Tabel 2: Ganti `@if($pesertaKhususList->hasPages())` menjadi `@if($pesertaKhususList->total() > 0)` dan render `{{ $pesertaKhususList->links() }}`.

---

### Modul 5: Daftar Bulan Magang
1. **Controller**: [`app/Http/Controllers/SuperAdmin/BulanController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/BulanController.php)
   - Buka method `index()`.
   - Ubah baris 29 dari `->paginate(12);` menjadi `->paginate(10)->withQueryString();`.
2. **View**: [`resources/views/superadmin/bulan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/bulan/index.blade.php)
   - Ubah `@if($bulanList->hasPages())` menjadi `@if($bulanList->total() > 0)`.
   - Pastikan pemanggilan pagination adalah `{{ $bulanList->links() }}`.

---

### Modul 6: Matriks Wewenang & Hak Akses Modul Dinamis
1. **Controller**: [`app/Http/Controllers/SuperAdmin/RoleController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/RoleController.php)
   - Pada method `index()`:
     Ubah:
     ```php
     $permissions = Permission::with('rolePermissions')
         ->orderBy('urutan')
         ->orderBy('id')
         ->paginate(10)
         ->withQueryString();
     ```
   - Pada method `updateMatrix()`:
     Terapkan proteksi **Gotcha #1**:
     ```php
     $matrixData = $request->input('matrix', []);
     // Hanya proses permissions yang disubmit pada halaman aktif
     $targetIds = array_map('intval', array_keys($matrixData));
     $targetPermissions = Permission::whereIn('id', $targetIds)->get();

     foreach ($targetPermissions as $permission) {
         // Update hak akses hanya untuk permission di halaman aktif
     }
     ```
2. **View**: [`resources/views/superadmin/role/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/role/index.blade.php)
   - Di bawah tabel matriks wewenang, tambahkan pagination container:
     ```blade
     @if($permissions->total() > 0)
         <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--neutral-200); background: var(--neutral-100);">
             {{ $permissions->links() }}
         </div>
     @endif
     ```

---

### Modul 7: Daftar Akun Pengguna Internal
1. **Controller**: [`app/Http/Controllers/SuperAdmin/StafController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/StafController.php)
   - Buka method `index()`.
   - Ubah baris 63 dari `$stafList = $query->paginate(15)->withQueryString();` menjadi:
     `$stafList = $query->paginate(10)->withQueryString();`.
2. **View**: [`resources/views/superadmin/staf/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/staf/index.blade.php)
   - Ubah `@if($stafList->hasPages())` menjadi `@if($stafList->total() > 0)`.
   - Pastikan pemanggilan pagination adalah `{{ $stafList->links() }}`.

---

### Modul 8: Bank Data Riwayat & Arsip Alumni Magang
1. **Controller**: [`app/Http/Controllers/SuperAdmin/ArsipPesertaController.php`](file:///g:/MAGKOM/app/Http/Controllers/SuperAdmin/ArsipPesertaController.php)
   - Buka method `index()`.
   - Ubah baris 47 dari `$arsipList = $query->paginate(15)->withQueryString();` menjadi:
     `$arsipList = $query->paginate(10)->withQueryString();`.
2. **View**: [`resources/views/superadmin/arsip/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/arsip/index.blade.php)
   - Ubah `@if($arsipList->hasPages())` menjadi `@if($arsipList->total() > 0)`.
   - Pastikan pemanggilan pagination adalah `{{ $arsipList->links() }}`.

---

### Modul 9 & Modul 10: Daftar Tugas & Rekam Jejak Logbook Harian (Detail Arsip Alumni)
1. **Lokasi View**: [`resources/views/superadmin/arsip/show.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/arsip/show.blade.php)
2. **Implementasi Komponen Client-Side Vanilla JS Pagination** (Rekomendasi Senior untuk Tab):
   - Tambahkan container pembungkus pagination di bawah daftar kartu tugas:
     ```html
     <div id="paginationTugasContainer" style="margin-top: 1.25rem;"></div>
     ```
   - Tambahkan container pembungkus pagination di bawah tabel logbook harian:
     ```html
     <div id="paginationLogbookContainer" style="margin-top: 1.25rem;"></div>
     ```
   - Buat helper modular Vanilla JS (atau integrasikan ke script halaman `show.blade.php` / `resources/js/components/tab-pagination.js`) yang menginisialisasi 10 item per halaman:
     - Menyembunyikan item index di luar `(currentPage - 1) * 10` s/d `currentPage * 10`.
     - Merender DOM `magkom-pagination-nav`, `magkom-pagination-summary`, dan `magkom-pagination-list`.
     - Memberikan aksi klik pada tombol nomor halaman tanpa me-reload halaman.
   - Alternatif Server-Side (jika pengguna mewajibkan query server-side):
     - Di `ArsipPesertaController@show`, buat paginator terpisah:
       `$tugasList = $penempatan->tugas()->with('progresLogs')->paginate(10, ['*'], 'tugas_page')->fragment('tab-tugas');`
       `$logbookList = $penempatan->logbooks()->paginate(10, ['*'], 'logbook_page')->fragment('tab-logbook');`
     - Di `show.blade.php`, tambahkan logika script saat `DOMContentLoaded` untuk membaca URL parameter `tugas_page` atau `logbook_page` atau `window.location.hash`, lalu otomatis mengaktifkan tab yang bersangkutan.

---

## 6. Penyesuaian Global pada Template Pagination Default

Untuk memastikan teks ringkasan `Menampilkan 1 sampai X dari X entri` selalu muncul (meskipun data kurang dari atau sama dengan 10):
Buka [`resources/views/vendor/pagination/default.blade.php`](file:///g:/MAGKOM/resources/views/vendor/pagination/default.blade.php):
```blade
{{-- Ubah dari @if ($paginator->hasPages()) menjadi @if ($paginator->total() > 0) --}}
@if ($paginator->total() > 0)
    <nav role="navigation" aria-label="Navigasi Halaman" class="magkom-pagination-nav">
        {{-- Ringkasan Jumlah Data (Selalu Tampil jika ada data) --}}
        <div class="magkom-pagination-summary">
            <span>
                Menampilkan
                <strong>{{ $paginator->firstItem() ?? 0 }}</strong>
                sampai
                <strong>{{ $paginator->lastItem() ?? 0 }}</strong>
                dari
                <strong>{{ $paginator->total() }}</strong>
                entri
            </span>
        </div>

        {{-- Tombol Navigasi Halaman (Hanya Tampil jika halaman > 1) --}}
        @if ($paginator->hasPages())
            <ul class="magkom-pagination-list">
                {{-- Tombol Sebelumnya, Angka Halaman, dan Tombol Berikutnya --}}
                ...
            </ul>
        @endif
    </nav>
@endif
```
Perubahan ini membuat seluruh tabel yang memiliki pagination otomatis menampilkan baris ringkasan yang elegan dan konsisten, persis seperti pada tangkapan layar referensi.

---

## 7. Kriteria Penerimaan (Acceptance Criteria / Checklist)

Setiap item harus diverifikasi secara manual maupun otomatis sebelum tugas dinyatakan selesai:

- [ ] **AC-01 (Jurusan)**: Halaman Master Program Studi & Jurusan menampilkan maksimal 10 entri per halaman dengan footer pagination yang sesuai.
- [ ] **AC-02 (Bidang)**: Halaman Daftar Bidang & Unit Kerja Diskominfo menampilkan maksimal 10 entri per halaman.
- [ ] **AC-03 (Slot Khusus)**: Tabel Alokasi Slot Khusus menampilkan maksimal 10 entri per halaman dengan parameter `slot_page`.
- [ ] **AC-04 (Peserta Khusus)**: Tabel Peserta Magang Jalur Khusus menampilkan maksimal 10 entri per halaman dengan parameter `peserta_page` tanpa saling menimpa dengan Tabel Slot.
- [ ] **AC-05 (Bulan Magang)**: Halaman Daftar Bulan Magang menampilkan maksimal 10 entri per halaman.
- [ ] **AC-06 (Matriks Role)**: Tabel Matriks Wewenang & Hak Akses menampilkan maksimal 10 modul per halaman. Menyimpan centang wewenang di halaman 1 **TIDAK MENGHAPUS** wewenang di halaman 2 atau 3.
- [ ] **AC-07 (Staf Internal)**: Halaman Akun Pengguna Internal (Admin & Pembimbing) menampilkan maksimal 10 entri per halaman beserta filter pencarian yang tetap berjalan (`withQueryString`).
- [ ] **AC-08 (Bank Data Arsip)**: Halaman Bank Data Riwayat & Arsip Alumni Magang menampilkan maksimal 10 entri per halaman.
- [ ] **AC-09 (Tugas di Arsip)**: Tab Riwayat Tugas pada detail alumni membatasi tampilan 10 tugas per halaman dan memiliki tombol navigasi halaman tanpa mereset tab.
- [ ] **AC-10 (Logbook di Arsip)**: Tab Logbook Harian pada detail alumni membatasi tampilan 10 entri logbook per halaman dan memiliki tombol navigasi halaman tanpa mereset tab.
- [ ] **AC-11 (Paritas Desain)**: Tampilan pagination memiliki teks `Menampilkan X sampai Y dari Z entri`, tombol nomor aktif berlatar belakang biru navy (`#0B3B70`), tombol pasif berlatar putih dengan border tipis, dan tombol disabled tidak dapat diklik.
- [ ] **AC-12 (Quality Gates)**: Seluruh unit/feature test (`php artisan test`), analisis statis (`phpstan`), formatting (`pint`), dan build aset (`npm run build`) berhasil 100% tanpa error.

---

## 8. Verifikasi & Perintah Quality Gate

Jalankan perintah-perintah berikut setelah seluruh kode diimplementasikan:

```powershell
# 1. Jalankan test suite feature SuperAdmin & Peserta
php artisan test --filter=SuperAdmin
php artisan test --filter=ArsipPesertaTest
php artisan test

# 2. Jalankan static analysis PHPStan (Level ketat)
./vendor/bin/phpstan analyse --memory-limit=1G

# 3. Jalankan Laravel Pint untuk memastikan standar PSR-12 / Laravel
./vendor/bin/pint --test

# 4. Jalankan Vite build untuk memastikan integritas CSS & JS
npm run build
```

---

## 9. Definisi Selesai (Definition of Done)

Tugas ini dinyatakan selesai jika:
1. Kesepuluh modul menampilkan batas maksimal 10 data per halaman.
2. Tidak ada regresi logika (terutama permission matrix tetap aman dan tab arsip tetap aktif).
3. Footer pagination konsisten 100% dengan referensi visual `docs/plans/2026-11-09-referensi-pagination-10-entri.png`.
4. Seluruh quality gate testing, linting, dan build pass tanpa peringatan.
5. Dokumen panduan ini ditinjau dan disetujui oleh Senior Developer / User.
