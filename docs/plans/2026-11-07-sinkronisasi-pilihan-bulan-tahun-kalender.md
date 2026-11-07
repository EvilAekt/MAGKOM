# Rencana Kerja: Sinkronisasi Pilihan Bulan & Tahun pada Mini Kalender (Bento & SuperAdmin)

**Dokumen:** Implementation Plan (Web Planning)  
**Tanggal:** 2026-11-07  
**Target:** Kalender Mini Bento (Dashboard Pembimbing) & Kalender Mini Aside (Dashboard SuperAdmin)  
**Baseline Stack:** Laravel 10 | PHP 8.3 | Blade | Vanilla CSS | Vanilla JS (ES Modules) | Vite  
**Status:** Selesai Terverifikasi (*Completed & Verified*)  

---

## 1. Tujuan

Menyelaraskan kontrol pemilihan **Bulan** dan **Tahun** pada widget kalender mini (`_bento_calendar.blade.php` dan `_aside_calendar.blade.php`), sehingga Bulan tidak lagi hanya berupa teks statis, melainkan memiliki komponen dropdown kapsul (*pill trigger button*) dan popover grid interaktif yang **identik secara visual dan perilaku logika** dengan kontrol Tahun (`2026 v`).

Hasil akhir yang diinginkan:
- Header kalender menampilkan dua tombol kapsul elegan yang seimbang: `[ November ▾ ] [ 2026 ▾ ]`, diapit oleh tombol panah navigasi `<` (sebelumnya) dan `>` (berikutnya).
- Mengklik tombol Bulan membuka popover berisi 12 bulan (Januari – Desember) dalam grid terstruktur 3 kolom x 4 baris.
- Mengklik salah satu bulan langsung memperbarui tampilan kalender, menandai bulan terpilih, menutup popover, dan memperbarui teks tombol.
- Logika kalender sepenuhnya sinkron antara navigasi panah (`<` / `>`), pemilih bulan, dan pemilih tahun.
- Mutual exclusivity: Membuka popover bulan otomatis menutup popover tahun, dan sebaliknya.
- Aksesibilitas penuh (keyboard `Escape`, klik di luar untuk menutup, ARIA state `aria-expanded`).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
1. **Target Utama**: Dashboard Pembimbing (`resources/views/pembimbing/dashboard/partials/_bento_calendar.blade.php`), dan diselaraskan ke Dashboard SuperAdmin (`resources/views/superadmin/dashboard/partials/_aside_calendar.blade.php`) agar tidak terjadi disparitas UX di dalam aplikasi.
2. **Format Label Bulan di Popover**: Menggunakan singkatan 3 huruf yang jelas dan ringkas (Jan, Feb, Mar, Apr, Mei, Jun, Jul, Agu, Sep, Okt, Nov, Des) atau nama lengkap ringkas dengan font 12px agar pas di dalam popover grid tanpa merusak batas kartu.
3. **Penyimpanan State Tanggal**: Pemilihan bulan atau tahun tetap mempertahankan tanggal yang sedang dipilih (`selectedDate`) jika tanggal tersebut valid di bulan tujuan.
4. **Kepatuhan Aturan Blade**: Sesuai aturan repositori `.agents/rules/AGENTS.md` pasal 3.3, penyesuaian markup Blade hanya dilakukan untuk menambahkan wrapper dan button semantik baru, tanpa menyisipkan CSS inline atau script JS ke dalam file Blade.

---

## 3. Evaluasi Pendekatan Teknis

### Opsi A: Symmetrical Popover Dropdown Engine (Direkomendasikan - Terpilih)
- **Desain**: Mengubah `span.cal-month-title` menjadi button pill kapsul `.cal-month-trigger-btn` dengan ikon chevron SVG, disandingkan dengan popover `.cal-month-popover` berisi 12 bulan dalam grid 3x4.
- **Styling**: Memanfaatkan rule CSS yang modular di `resources/css/pages/pembimbing/dashboard.css` yang seragam antara Month dan Year (`.cal-month-trigger-btn, .cal-year-trigger-btn`, `.cal-month-popover, .cal-year-popover`).
- **Interaktivitas**: Mengembangkan fungsi modular di `resources/js/pages/pembimbing/dashboard.js` (`renderMonthPopover()`, `toggleMonthPopover()`, `closeMonthPopover()`) yang terintegrasi dengan `updateCalendar()`.
- **Kelebihan**:
  - Tampilan visual 100% konsisten antara bulan dan tahun sesuai tangkapan layar user.
  - Mendukung transisi hover, active state, dan tema gelap (`[data-theme="dark"]`).
  - Standar aksesibilitas WCAG 2.1 AA (ARIA attributes, keyboard navigation).

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/pembimbing/dashboard/partials/_bento_calendar.blade.php` | Modify | Mengubah judul bulan menjadi tombol kapsul trigger `#calMonthTriggerBtn` dengan ikon chevron dan kontainer popover `#calMonthPopover` dengan grid `#calMonthGrid`. |
| `resources/css/pages/pembimbing/dashboard.css` | Modify | Menambahkan styling untuk `.cal-month-trigger-btn`, `.cal-month-popover`, `.cal-month-grid`, `.cal-month-option-btn`, serta adaptasi dark mode. |
| `resources/js/pages/pembimbing/dashboard.js` | Modify | Menambahkan inisialisasi popover bulan (12 bulan), event toggle, seleksi bulan, mutual exclusion dengan popover tahun, dan update kalender. |
| `resources/views/superadmin/dashboard/partials/_aside_calendar.blade.php` | Modify *(Opsi Selaras)* | Menyelaraskan struktur markup kalender SuperAdmin dengan popover bulan `#saCalMonthTriggerBtn` & `#saCalMonthPopover`. |
| `resources/css/pages/superadmin/dashboard.css` | Modify *(Opsi Selaras)* | Menambahkan styling popover bulan SuperAdmin. |
| `resources/js/pages/superadmin/dashboard.js` | Modify *(Opsi Selaras)* | Menambahkan logika popover bulan pada kalender SuperAdmin. |

---

## 5. Langkah Implementasi Terperinci

### Tahap 1: Penyesuaian Markup Blade (Dengan Izin Pengguna)
- [x] **1.1** Edit `resources/views/pembimbing/dashboard/partials/_bento_calendar.blade.php`:
  - Membungkus pemilih bulan dalam `.cal-month-dropdown-wrap`.
  - Mengubah pemilih bulan menjadi button kapsul `#calMonthTriggerBtn` dengan ikon chevron dan popover grid `#calMonthGrid`.
  - Tetap mempertahankan ID `calMonthTitle` di dalam label tombol agar backward-compatible.

### Tahap 2: Penambahan CSS di `resources/css/pages/pembimbing/dashboard.css`
- [x] **2.1** Tambahkan selector bersama atau styling terarah untuk tombol bulan:
  - `.cal-month-dropdown-wrap`: `position: relative;`
  - `.cal-month-trigger-btn`: Menyamakan ukuran, padding (`2px 7px`), font-size (`13px`), font-weight (`700`), radius (`6px`), warna teks `#003566`, border `#cfdaf0`, background `rgba(0, 53, 102, 0.07)`, dan transisi hover.
  - `.cal-month-popover`: Posisi absolute, z-index 60, min-width 175px, padding 10px, background putih, shadow, border radius 10px, `left: 0;`.
  - `.cal-month-grid`: CSS Grid 3 kolom x 4 baris (`grid-template-columns: repeat(3, 1fr); gap: 6px;`).
  - `.cal-month-option-btn`: Tombol opsi bulan dengan padding nyaman, radius 6px, hover rona biru muda, dan status `.active` berwarna Sleman Deep Navy `#003566` dengan teks putih.
- [x] **2.2** Tambahkan varian Dark Mode:
  - `[data-theme="dark"] .cal-month-trigger-btn`
  - `[data-theme="dark"] .cal-month-popover`
  - `[data-theme="dark"] .cal-month-option-btn`

### Tahap 3: Penyempurnaan Logika JavaScript di `resources/js/pages/pembimbing/dashboard.js`
- [x] **3.1** Mengambil referensi elemen baru di dalam fungsi `initBentoCalendar()` (`calMonthTriggerBtn`, `calMonthPopover`, `calMonthGrid`).
- [x] **3.2** Membuat fungsi `renderMonthPopover()` dengan 12 bulan (Jan–Des), tanda `.active` untuk bulan terpilih, dan penanganan klik opsi bulan.
- [x] **3.3** Membuat fungsi `closeMonthPopover()`, `toggleMonthPopover()`, dan mutual exclusion dengan `toggleYearPopover()`.
- [x] **3.4** Modifikasi event listener global (outside click dan keyboard `Escape`).

### Tahap 4: Penyelarasan Kalender SuperAdmin (Opsional/Selaras)
- [x] **4.1** Terapkan perubahan serupa pada `resources/views/superadmin/dashboard/partials/_aside_calendar.blade.php`.
- [x] **4.2** Tambahkan styling di `resources/css/pages/superadmin/dashboard.css`.
- [x] **4.3** Update logika kalender di `resources/js/pages/superadmin/dashboard.js`.

### Tahap 5: Build Aset & Verifikasi
- [x] **5.1** Jalankan build aset Vite: `npm run build` (Sukses tanpa error).
- [x] **5.2** Jalankan automated feature tests: `php artisan test --filter=PembimbingDashboardEnhancementTest` (7 passed, 44 assertions).
- [x] **5.3** Jalankan automated feature tests: `php artisan test --filter=AdminDashboardEnhancementTest` (8 passed, 36 assertions).
- [x] **5.4** Jalankan linter formatting: `./vendor/bin/pint --test` (Passed).

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Tombol Bulan berwujud capsule pill dengan ikon chevron identik dengan tombol Tahun.
- [x] Popover Bulan berfungsi mulus membuka 12 pilihan bulan dalam grid 3x4.
- [x] Pemilihan bulan memperbarui tanggal kalender dan label tombol secara instan tanpa reload halaman.
- [x] Popover bulan dan tahun saling menutup secara eksklusif (tidak saling tumpang tindih).
- [x] Tombol panah navigasi `<` dan `>` tetap sinkron memperbarui label bulan dan tahun.
- [x] Tidak ada script atau style yang disisipkan ke dalam file Blade.
- [x] `npm run build` sukses tanpa warning/error.
- [x] `php artisan test` sukses dan lulus seluruh pengujian.
