# [SENIOR DEV TASK] Menyembunyikan Filter Saat Masuk Peserta & Paginasi 10 Entri per Halaman pada Logbook & Tugas

**ID Task**: `TASK-FILTER-HIDE-AND-10-PAGINATION-01`  
**Target Eksekutor**: Junior Full-Stack Developer / AI Model Ringan (Gemini 3.8 Flash / Claude Haiku / Cursor)  
**Author / Reviewer**: Senior Lead Full-Stack Architect  
**Repositori**: `MAGKOM` (Portal Manajemen Magang Diskominfo Sleman)  
**Status**: Ready for Implementation (Plan / PRD)  
**Tanggal**: 06 November 2026  

---

## 1. Background & Analisis Root Cause

### 1.1 Masalah Pengguna (*User Problem*)
Berdasarkan temuan pengujian antarmuka oleh pengguna:
1. **Filter Masih Muncul di Mode Detail Mentee**:
   Ketika pembimbing mengklik kartu peserta (URL memuat parameter `?masuk={penempatan_id}`), kartu filter nama peserta & filter bulan (`#logbookFilterCard` di Logbook dan `#tugasFilterCard` di Tugas) masih terpampang di atas kartu peserta. Padahal pembimbing sedang fokus memeriksa satu peserta. Dropdown "Filter Nama Peserta" di atas detail individu membingungkan pengguna dan membuang area vertikal layar.
2. **Paginasi di Tugas Menampilkan Entri Mentee (13 Entri), Bukan Entri Tugas Peserta**:
   Pada tampilan detail tugas peserta (misalnya Zulfikar Ali yang belum memiliki tugas), di bagian bawah halaman justru muncul:
   *`Menampilkan 1 sampai 10 dari 13 entri    « Sebelumnya [ 1 ] [ 2 ] Berikutnya »`*
   Paginasi tersebut ternyata adalah paginasi daftar mentee (13 peserta bimbingan), bukan paginasi penugasan milik peserta yang sedang dilihat!
3. **Paginasi Logbook Belum Ada**:
   Pada tampilan logbook peserta, entri logbook di-loop dari relasi secara mentah tanpa paginasi 10 per halaman dan tanpa navigasi halaman (halaman 1 ke halaman 2 jika entri > 10).

---

### 1.2 Root Cause Analysis (RCA)

1. **Komponen Filter (`logbook/index.blade.php` & `tugas/index.blade.php`)**:
   - Kontainer `<div id="logbookFilterCard" class="logbook-filter-card">` dan `<div id="tugasFilterCard" class="tugas-filter-card">` belum disematkan class `d-none` saat `$isMasukMode` bernilai `true`.
   - Padahal JavaScript di `logbook-index.js` dan `tugas-index.js` sudah memiliki logika `filterCard.classList.remove('d-none')` saat kembali ke mode semua mentee.
2. **Paginasi di Tugas (`tugas/index.blade.php`)**:
   - Pengecekan paginasi di baris 554 mengandalkan `$isFocusActive = !empty($selectedPenempatan)`. Saat parameter `?masuk=` digunakan, `$selectedPenempatan` bernilai `null` sehingga `$isFocusActive` bernilai `false`.
   - Akibatnya, Blade mengeksekusi blok `@if(!$isFocusActive) {{ $penempatanList->links() }}`, yang merender daftar 13 mentee bimbingan di bawah detail tugas Zulfikar Ali!
   - Di dalam kartu, perulangan tugas mengiterasi `$allTasks = $masukPenempatan->tugas ?? collect()`, bukan `$tugasList` (yang sudah terpaginasi 10 entri per halaman di controller).
3. **Paginasi di Logbook (`LogbookReviewController.php` & `logbook/index.blade.php`)**:
   - Di Controller, `$logbooks` dipaginasi `paginate(20)` (bukan 10).
   - Di Blade, perulangan entri logbook menggunakan `$masukLogbooks = $masukPenempatan->logbooks ?? collect()`, bukan `$logbooks`, dan tidak ada pemanggilan `{{ $logbooks->links() }}` di bagian bawah halaman.

---

## 2. Target Solusi & Perilaku yang Diharapkan (Expected Behavior)

| Kondisi / Halaman | Tampilan Filter Card | Tampilan Konten Utama | Tampilan Paginasi di Bawah |
|---|---|---|---|
| **A. Direktori Utama Logbook** (`/pembimbing/logbook`) | **Muncul Normal** (`#logbookFilterCard`) | Daftar kartu seluruh mentee bimbingan | Paginasi mentee bimbingan jika mentee > 10 (`{{ $penempatanList->links() }}`) |
| **B. Masuk Detail Logbook Mentee** (`/pembimbing/logbook?masuk={id}`) | **Hilang / Tersembunyi** (`class="... d-none"`) | 10 entri logbook teratas milik mentee tersebut, tombol kembali (`<`) aktif | Jika logbook mentee > 10 entri: muncul *"Menampilkan 1 sampai 10 dari X entri"* dengan tombol `[ 1 ] [ 2 ] Berikutnya »`. Jika <= 10 entri: paginasi bersih tidak muncul. |
| **C. Pindah Halaman Logbook Mentee** (`/pembimbing/logbook?masuk={id}&page=2`) | **Hilang / Tersembunyi** (`class="... d-none"`) | Entri logbook ke 11 sampai 20 milik mentee tersebut | Navigasi halaman tetap berada di konteks mentee tersebut (`?masuk={id}&page=2`) |
| **D. Direktori Utama Tugas** (`/pembimbing/tugas`) | **Muncul Normal** (`#tugasFilterCard`) | Daftar kartu seluruh mentee bimbingan | Paginasi mentee bimbingan (`{{ $penempatanList->links() }}`) |
| **E. Masuk Detail Tugas Mentee** (`/pembimbing/tugas?masuk={id}`) | **Hilang / Tersembunyi** (`class="... d-none"`) | 10 tugas teratas milik mentee tersebut | Jika tugas mentee > 10 entri: paginasi tugas muncul `{{ $tugasList->links() }}`. Jika mentee memiliki 0 tugas (seperti Zulfikar Ali): paginasi mentee 13 entri TIDAK BOLEH MUNCUL! |

---

## 3. Spesifikasi Teknis Perubahan (Step-by-Step Task)

### Step 1: Penyesuaian Backend Controller Logbook (`LogbookReviewController.php`)

Ubah limit paginasi query logbook dari 20 menjadi **10 entri per halaman**:

**Lokasi Berkas**: [`app/Http/Controllers/Pembimbing/LogbookReviewController.php`](file:///g:/MAGKOM/app/Http/Controllers/Pembimbing/LogbookReviewController.php)

```php
// Baris 79: Ubah paginate(20) menjadi paginate(10)
$logbooks = $query->latest('tanggal')->paginate(10)->withQueryString();
```

Dan pastikan `$penempatanList` di direktori utama juga dipaginasi 10 per halaman:
```php
$penempatanList = (clone $penempatanBaseQuery)->latest('id')->paginate(10)->withQueryString();
```

---

### Step 2: Penyesuaian Blade Logbook (`resources/views/pembimbing/logbook/index.blade.php`)

1. **Sembunyikan Filter Card saat `$isMasukMode` Aktif**:
   ```blade
   <div id="logbookFilterCard" class="logbook-filter-card {{ $isMasukMode ? 'd-none' : '' }}">
   ```
2. **Gunakan Data Terpaginasi `$logbooks` untuk Render Entri Logbook**:
   Pada blok `@if($isMasukMode)`:
   ```blade
   @php
       $masukMentee = $masukPenempatan->peserta;
       $masukNama = $masukMentee?->nama ?? 'Peserta Magang';
       $masukLogbooks = $logbooks; // Menggunakan Paginator 10 item per halaman
   @endphp
   ```
3. **Tambahkan Container Paginasi di Bagian Bawah Panel**:
   Di bawah penutup `menteeStreamContainer`, sebelum `</x-gentelella.x-panel>`:
   ```blade
   {{-- Paginasi Halaman Logbook / Mentee --}}
   @if($isMasukMode)
       @if($logbooks->hasPages())
           <div id="logbookPaginationWrapper" style="margin-top: 24px;">
               {{ $logbooks->links() }}
           </div>
       @endif
   @else
       @if(method_exists($penempatanList, 'hasPages') && $penempatanList->hasPages())
           <div id="menteePaginationWrapper" style="margin-top: 24px;">
               {{ $penempatanList->links() }}
           </div>
       @endif
   @endif
   ```

---

### Step 3: Penyesuaian Blade Tugas (`resources/views/pembimbing/tugas/index.blade.php`)

1. **Sembunyikan Filter Card saat `$isMasukMode` Aktif**:
   ```blade
   <div id="tugasFilterCard" class="tugas-filter-card {{ $isMasukMode ? 'd-none' : '' }}">
   ```
2. **Gunakan Data Terpaginasi `$tugasList` untuk Render Entri Tugas Mentee**:
   Pada blok `@if($isMasukMode)`:
   ```blade
   @php
       $masukMentee = $masukPenempatan->peserta;
       $masukNama = $masukMentee?->nama ?? 'Peserta Magang';
       $allTasks = $tugasList; // Menggunakan Paginator 10 item per halaman
   @endphp
   ```
3. **Perbaiki Kondisi Paginasi di Bagian Bawah Halaman (Baris 553-562)**:
   ```blade
   {{-- 5. Paginasi Halaman: Pisahkan Tegas Antara Mode Masuk Peserta vs Mode Semua Mentee --}}
   @if($isMasukMode)
       @if($tugasList->hasPages())
           <div id="tugasPaginationWrapper" style="margin-top: 24px;">
               {{ $tugasList->links() }}
           </div>
       @endif
   @else
       @if(method_exists($penempatanList, 'hasPages') && $penempatanList->hasPages())
           <div id="menteePaginationWrapper" style="margin-top: 24px;">
               {{ $penempatanList->links() }}
           </div>
       @endif
   @endif
   ```
   *Catatan penting*: Dengan logika ini, jika mentee yang dipilih memiliki 0 tugas (seperti Zulfikar Ali), `$tugasList->hasPages()` bernilai `false`, sehingga paginasi mentee 13 entri tidak akan pernah bocor lagi ke tampilan detail peserta!

---

### Step 4: Penyesuaian Feature Test (`tests/Feature/`)

1. **`PembimbingFocusedMenteeLogbookViewTest.php`**:
   - Verifikasi bahwa saat masuk peserta (`?masuk={id}`), elemen `#logbookFilterCard` memiliki class `d-none`.
   - Buat 15 entri logbook untuk seorang mentee, lalu verifikasi bahwa halaman 1 menampilkan teks `"Menampilkan 1 sampai 10 dari 15 entri"` dan tautan halaman 2.
   - Akses halaman 2 (`?masuk={id}&page=2`), pastikan entri ke-11 tampil dan filter card tetap `d-none`.
2. **`PembimbingFocusedMenteeTugasViewTest.php`**:
   - Verifikasi bahwa saat masuk peserta (`?masuk={id}`), elemen `#tugasFilterCard` memiliki class `d-none`.
   - Verifikasi bahwa jika mentee memiliki 0 tugas, paginasi 13 mentee TIDAK muncul (`assertDontSee('id="menteePaginationWrapper"')`).
   - Buat 12 tugas untuk seorang mentee, lalu verifikasi bahwa halaman 1 menampilkan `"Menampilkan 1 sampai 10 dari 12 entri"` dari `$tugasList`.

---

## 4. Matriks Pengujian & Quality Gates

| Komponen Uji | Perintah | Target Hasil |
|---|---|---|
| **Vite Asset** | `npm run build` | Sukses tanpa error kompilasi. |
| **Code Style** | `./vendor/bin/pint --test` | Lolos formatting PSR-12. |
| **Feature Test Logbook** | `php artisan test tests/Feature/PembimbingFocusedMenteeLogbookViewTest.php` | 100% Passed. |
| **Feature Test Tugas** | `php artisan test tests/Feature/PembimbingFocusedMenteeTugasViewTest.php` | 100% Passed. |
| **Regression Suite** | `php artisan test --filter=FocusedMentee` | Semua test passed (0 failed). |

---

## 5. Panduan Prompt Eksekusi untuk AI Ringan (Gemini 3.8 Flash)

```markdown
Tolong eksekusi perubahan sesuai rencana docs/plans/2026-11-06-senior-dev-task-filter-hide-and-10-item-pagination-prd.md:
1. Di app/Http/Controllers/Pembimbing/LogbookReviewController.php:
   - Ubah paginate logbook dari 20 menjadi 10 ($query->latest('tanggal')->paginate(10)->withQueryString()).
   - Paginate penempatanList menjadi 10 ($penempatanBaseQuery->latest('id')->paginate(10)->withQueryString()).
2. Di resources/views/pembimbing/logbook/index.blade.php:
   - Tambahkan class d-none pada filter card saat $isMasukMode (<div id="logbookFilterCard" class="logbook-filter-card {{ $isMasukMode ? 'd-none' : '' }}">).
   - Gunakan $logbooks untuk $masukLogbooks.
   - Tambahkan paginasi logbook di bagian bawah panel ({{ $logbooks->links() }} saat $isMasukMode dan {{ $penempatanList->links() }} saat !$isMasukMode).
3. Di resources/views/pembimbing/tugas/index.blade.php:
   - Tambahkan class d-none pada filter card saat $isMasukMode (<div id="tugasFilterCard" class="tugas-filter-card {{ $isMasukMode ? 'd-none' : '' }}">).
   - Gunakan $tugasList untuk $allTasks saat $isMasukMode.
   - Perbaiki paginasi di bawah agar hanya menampilkan {{ $tugasList->links() }} saat $isMasukMode dan {{ $penempatanList->links() }} saat !$isMasukMode.
4. Jalankan `npm run build`, `./vendor/bin/pint --test`, dan `php artisan test --filter=FocusedMentee`.
```

