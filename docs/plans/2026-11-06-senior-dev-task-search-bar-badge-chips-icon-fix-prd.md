# [SENIOR DEV TASK] Penambahan Fitur Pencarian di Filter, Penyesuaian Logo Ganda Tombol Buat Tugas, dan Badge Statistik Logbook Peserta

**ID Task**: `TASK-SEARCH-CHIPS-ICON-FIX-01`  
**Target Eksekutor**: Junior Full-Stack Developer / AI Model Ringan (Gemini 3.8 Flash / Claude Haiku / Cursor)  
**Author / Reviewer**: Senior Lead Full-Stack Architect  
**Repositori**: `MAGKOM` (Portal Manajemen Magang Diskominfo Sleman)  
**Status**: Ready for Implementation (Plan / PRD)  
**Tanggal**: 06 November 2026  

---

## 1. Background & Analisis Kebutuhan Pengguna

Berdasarkan tinjauan UI dan pengujian oleh pengguna, terdapat 3 penyempurnaan penting pada modul Pembimbing:

### 1.1 Masalah 1: Filter Belum Memiliki Fitur Pencarian Teks Fleksibel
- **Kondisi Eksisting**: Di halaman `Logbook Peserta` (`/pembimbing/logbook`) dan `Tugas & Progres Magang` (`/pembimbing/tugas`), filter hanya memiliki dropdown statis "Filter Nama Peserta" dan "Filter Bulan". Pembimbing harus menggulir daftar panjang jika jumlah mentee banyak.
- **Kebutuhan**: Menambahkan input teks pencarian ala halaman `Direktori Peserta Bimbingan`:  
  *`[ 🔍 Cari nama, kampus, NIM... ]`*  
  yang terintegrasi ke dalam form filter di Logbook dan Tugas. Ketika diisi, sistem menyaring kartu mentee berdasarkan kecocokan nama, asal institusi/kampus, atau NIM/NISN peserta.

### 1.2 Masalah 2: Logo Plus Ganda (`+ +`) pada Tombol "Buat Tugas Baru" & "Direktori Peserta Bimbingan"
- **Kondisi Eksisting**: Pada tombol aksi di banner header:
  - Di `tugas/index.blade.php`: `<svg>...</svg> + Buat Tugas Baru`
  - Di `peserta/index.blade.php`: `<svg>...</svg> + Beri Tugas Baru`
  Terdapat tag `<svg>` yang menggambar ikon plus (`+`) **sekaligus** teks diawali karakter plus (`+`), sehingga tombol merender logo plus ganda: **`+ + Buat Tugas Baru`**.
- **Kebutuhan**: Hilangkan karakter plus literal di dalam teks sehingga hanya tersisa satu ikon plus SVG yang proporsional.

### 1.3 Masalah 3: Kartu Peserta di Logbook Belum Memiliki Badge Chips Statistik Seperti di Tugas
- **Kondisi Eksisting**: Di halaman `Tugas`, setiap kartu mentee memiliki pill badges informatif di sisi kanan:  
  *`[ 3 Tugas ]  [ 1 Selesai ]  [ 1 Review ]  >`*  
  Sedangkan di halaman `Logbook`, kartu mentee hanya menampilkan teks biasa di bawah nama dan panah `>` tanpa badge chips status.
- **Kebutuhan**: Tambahkan badge chips serupa di sisi kanan kartu mentee pada `Logbook Peserta`:  
  *`[ X Logbook ]  [ Y Diberi Catatan ]  [ Z Menunggu ]  >`*  
  menggunakan styling CSS `.mentee-stats-badges` dan `.mentee-count-chip` yang sudah tersedia di design system.

---

## 2. Solusi Teknis & Rincian Perubahan (Step-by-Step Task)

### Step 1: Penyesuaian Backend Controller (`LogbookReviewController.php` & `TugasController.php`)

Agar pencarian teks `q` menyaring daftar kartu mentee (`penempatanBaseQuery`):

1. **Di `app/Http/Controllers/Pembimbing/LogbookReviewController.php`**:
   Pada `penempatanBaseQuery`, tambahkan pencarian `q` untuk nama peserta, kampus, dan NIM:
   ```php
   if ($request->filled('q')) {
       $search = '%'.$request->input('q').'%';
       $penempatanBaseQuery->whereHas('peserta', function ($qp) use ($search) {
           $qp->where('nama', 'like', $search)
               ->orWhereHas('profil', function ($qprof) use ($search) {
                   $qprof->where('institusi', 'like', $search)
                       ->orWhere('nim_nisn', 'like', $search);
               });
       });
   }
   ```
2. **Di `app/Http/Controllers/Pembimbing/TugasController.php`**:
   Lakukan hal yang sama pada `penempatanBaseQuery` di `index()`:
   ```php
   if ($request->filled('q')) {
       $search = '%'.$request->input('q').'%';
       $penempatanBaseQuery->whereHas('peserta', function ($qp) use ($search) {
           $qp->where('nama', 'like', $search)
               ->orWhereHas('profil', function ($qprof) use ($search) {
                   $qprof->where('institusi', 'like', $search)
                       ->orWhere('nim_nisn', 'like', $search);
               });
       });
   }
   ```

---

### Step 2: Perbaikan Tombol Buat Tugas Baru (Menghapus Plus Ganda `+ +`)

1. **Di `resources/views/pembimbing/tugas/index.blade.php` (Baris 34-38 & 280-282)**:
   Ubah teks `+ Buat Tugas Baru` menjadi `<span>Buat Tugas Baru</span>`:
   ```blade
   <a href="{{ route('pembimbing.tugas.create') }}" class="btn-create-task">
       <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
       <span>Buat Tugas Baru</span>
   </a>
   ```
2. **Di `resources/views/pembimbing/peserta/index.blade.php` (Baris 33-37)**:
   Ubah teks `+ Beri Tugas Baru` menjadi `<span>Beri Tugas Baru</span>`:
   ```blade
   <a href="{{ route('pembimbing.tugas.create') }}" class="btn-create-task">
       <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
       <span>Beri Tugas Baru</span>
   </a>
   ```

---

### Step 3: Integrasi Search Input ke Filter Bar Logbook & Tugas

1. **Di `resources/views/pembimbing/logbook/index.blade.php`**:
   Tambahkan field pencarian teks di samping dropdown nama peserta:
   ```blade
   <div class="filter-group" style="flex: 1.3;">
       <label for="inputSearchLogbook">Pencarian Peserta</label>
       <div class="peserta-search-input-wrap">
           <input type="text" name="q" id="inputSearchLogbook" value="{{ request('q') }}" placeholder="Cari nama, kampus, NIM..." class="peserta-search-input">
           <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="peserta-search-icon">
               <circle cx="11" cy="11" r="8"></circle>
               <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
           </svg>
       </div>
   </div>
   ```
   Dan perbarui tombol reset agar muncul jika `q` diisi:
   ```blade
   @if(request()->anyFilled(['penempatan_id', 'bulan', 'q']))
       <a href="{{ route('pembimbing.logbook.index') }}" class="btn-filter-reset">Reset</a>
   @endif
   ```
2. **Di `resources/views/pembimbing/tugas/index.blade.php`**:
   Tambahkan field pencarian teks yang sama di form filter tugas:
   ```blade
   <div class="filter-group" style="flex: 1.3;">
       <label for="inputSearchTugas">Pencarian Peserta</label>
       <div class="peserta-search-input-wrap">
           <input type="text" name="q" id="inputSearchTugas" value="{{ request('q') }}" placeholder="Cari nama, kampus, NIM..." class="peserta-search-input">
           <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="peserta-search-icon">
               <circle cx="11" cy="11" r="8"></circle>
               <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
           </svg>
       </div>
   </div>
   ```
   Dan perbarui tombol reset:
   ```blade
   @if(request()->anyFilled(['penempatan_id', 'bulan', 'q']))
       <a href="{{ route('pembimbing.tugas.index', request()->except(['penempatan_id', 'bulan', 'q'])) }}" class="btn-filter-reset">Reset</a>
   @endif
   ```

---

### Step 4: Menambahkan Badge Chips Statistik pada Kartu Logbook Peserta

**Lokasi**: [`resources/views/pembimbing/logbook/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/logbook/index.blade.php)  
Di dalam perulangan kartu mentee:
1. Bersihkan subtitle kartu:  
   `<p class="mentee-sub-inst">{{ $institusi }}</p>` (menghilangkan bullet & teks manual).
2. Hitung statistik logbook per mentee dan sematkan badge chips di `mentee-meta-right`:
   ```blade
   @php
       $totalLogsCount = $menteeLogbooks->count();
       $catatanCount = $menteeLogbooks->filter(fn($l) => !empty($l->catatan_pembimbing))->count();
       $menungguCount = $menteeLogbooks->filter(fn($l) => in_array($l->status->value ?? (string)$l->status, ['terkirim', 'menunggu']))->count();
   @endphp

   <div class="mentee-meta-right">
       @if($totalLogsCount > 0)
           <div class="mentee-stats-badges">
               <span class="mentee-count-chip">{{ $totalLogsCount }} Logbook</span>
               @if($catatanCount > 0)
                   <span class="mentee-count-chip selesai">{{ $catatanCount }} Diberi Catatan</span>
               @endif
               @if($menungguCount > 0)
                   <span class="mentee-count-chip review">{{ $menungguCount }} Menunggu</span>
               @endif
           </div>
       @endif
       <div class="mentee-chevron-icon" aria-hidden="true">
           <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
               <polyline points="9 18 15 12 9 6"></polyline>
           </svg>
       </div>
   </div>
   ```

---

### Step 5: Penyesuaian Styling CSS (`logbook-index.css` & `tugas-index.css`)

Pastikan class `.peserta-search-input-wrap`, `.peserta-search-input`, dan `.peserta-search-icon` tersedia dan memiliki style responsif di `logbook-index.css` dan `tugas-index.css` agar tampak rapi dan sejajar dengan kontrol dropdown.

---

## 3. Matriks Pengujian & Quality Gates

| Komponen Uji | Perintah | Target Hasil |
|---|---|---|
| **Vite Asset** | `npm run build` | Sukses tanpa error kompilasi. |
| **Code Style** | `./vendor/bin/pint --test` | Lolos formatting PSR-12. |
| **Feature Test Logbook** | `php artisan test tests/Feature/PembimbingFocusedMenteeLogbookViewTest.php` | 100% Passed. |
| **Feature Test Tugas** | `php artisan test tests/Feature/PembimbingFocusedMenteeTugasViewTest.php` | 100% Passed. |
| **Regression Suite** | `php artisan test --filter=FocusedMentee` | Semua test passed (0 failed). |

---

## 4. Panduan Prompt Eksekusi untuk AI Ringan (Gemini 3.8 Flash)

```markdown
Tolong eksekusi perubahan sesuai rencana docs/plans/2026-11-06-senior-dev-task-search-bar-badge-chips-icon-fix-prd.md:
1. Di app/Http/Controllers/Pembimbing/LogbookReviewController.php dan TugasController.php:
   - Tambahkan filter pencarian 'q' pada penempatanBaseQuery untuk mencari nama, institusi, atau nim_nisn peserta.
2. Di resources/views/pembimbing/tugas/index.blade.php dan resources/views/pembimbing/peserta/index.blade.php:
   - Hapus karakter '+' literal pada teks tombol 'Buat Tugas Baru' dan 'Beri Tugas Baru' sehingga tidak double icon (+ +).
3. Di resources/views/pembimbing/logbook/index.blade.php dan resources/views/pembimbing/tugas/index.blade.php:
   - Tambahkan input text pencarian 'q' dengan class peserta-search-input dan icon pencarian di form filter.
4. Di resources/views/pembimbing/logbook/index.blade.php:
   - Tambahkan badge chips statistik logbook (.mentee-stats-badges, .mentee-count-chip) pada kartu mentee persis seperti di Tugas.
5. Jalankan `npm run build`, `./vendor/bin/pint --test`, dan `php artisan test --filter=FocusedMentee`.
```

