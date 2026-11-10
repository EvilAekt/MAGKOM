# Senior Developer Task Specification: Modernisasi, Refaktor & Standarisasi Desain Data Table SuperAdmin (Plan 4 Re-Plan)

- **ID Tiket**: `PRD-TASK-2026-10-28-PLAN4-REVAMP`
- **Target Pelaksana**: Junior Full-Stack Developer / AI Coding Agent (Gemini 3.8 Flash Ready)
- **Komponen Target**: 
  1. Master Layout & Central CSS Tokens (`resources/views/layouts/dashboard.blade.php`)
  2. Stylesheet Global Tabel Gentelella (`public/css/gentelella-theme.css`)
  3. Stylesheet Komponen Tabel SuperAdmin (`public/css/superadmin-dashboard.css`)
  4. 10 File View Blade SuperAdmin (Tabel Utama):
     - `resources/views/superadmin/bulan/index.blade.php` (Bukti Screenshot Utama Sir Alan)
     - `resources/views/superadmin/bidang/index.blade.php`
     - `resources/views/superadmin/jurusan/index.blade.php`
     - `resources/views/superadmin/slot-khusus/index.blade.php`
     - `resources/views/superadmin/rubrik/index.blade.php`
     - `resources/views/superadmin/role/index.blade.php`
     - `resources/views/superadmin/staf/index.blade.php`
     - `resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php` (Tabel Antrean FIFO)
     - `resources/views/superadmin/audit-log/index.blade.php`
     - `resources/views/superadmin/monitoring/index.blade.php` & `arsip/index.blade.php`
- **Dokumen Referensi**: Design System DS-MagKom (Bab 2), WCAG 2.1 AA Compliance (Contrast Ratio ≥ 4.5:1), Gentelella Modern Standards
- **Tingkat Kesulitan**: Sedang (CSS Architecture, Specificity Resolution, Inline Style Cleansing, & DOM Hygiene)
- **Status Tiket**: `Drafted / Ready for Approval` (Menunggu Konfirmasi User / Eksekusi)

---

## 1. Executive Summary & Root Cause Analysis (RCA)

### A. Konteks Masalah & Evaluasi Kegagalan Plan 4 Sebelumnya
Pada pengerjaan sesi sebelumnya, agen pelaksana salah mengartikan maksud dari tangkapan layar `d0bee9e91cb2ace706f090ed56206441f9fc5aa5b3929a7e6922d6b4bcadfa36.png` dan `d61bc7ebcbd93ad5ef16cd767961cc7e8ec09cb9d47c41182f0e9b01ad05d150.png` yang dikirimkan oleh Sir Alan:

1. **Salah Fokus (False Scope Alignment)**:
   - Agen sebelumnya mengira komplain berfokus pada warna judul kartu (`.x_title h2` / `.sa-panel__title`) dan hanya menambahkan class `.has-table` serta mengganti warna font judul kartu menjadi slate `#CBD5E1`.
   - Akibatnya, **elemen utama yang ditunjuk Sir Alan—yakni Header Baris Tabel (`thead tr.headings th`) beserta baris data tabel—sama sekali tidak tersentuh perbaikan nyata ("masi banyak yg blm berubah")**.

2. **Akar Masalah Teknis 1: The Low-Contrast "Slate 400 on Dark Navy" Accessibility Bug**:
   - Di hampir seluruh file Blade SuperAdmin (`bulan`, `jurusan`, `bidang`, `slot-khusus`, `rubrik`, `role`, `staf`), terdapat CSS hack di `@push('styles')`:
     ```css
     [data-theme="dark"] table th {
         color: #94A3B8 !important; /* Slate 400 kusam */
     }
     ```
   - Latar belakang header di mode gelap adalah `#02275D` (Abyssal Navy) atau `#16243C` (Gentelella Charcoal). 
   - Warna teks `#94A3B8` di atas `#02275D` hanya menghasilkan rasio kontras **3.3:1**, yang **GAGAL memenuhi standar WCAG 2.1 AA (minimal 4.5:1 untuk teks normal)**. Teks header (`BULAN MAGANG`, `JADWAL BUKA PENDAFTARAN`, dsb.) terlihat suram, redup, tidak berwibawa, dan sulit dibaca.

3. **Akar Masalah Teknis 2: Inline Style Clutter & Fragmented Markup**:
   - Seluruh tag `<tr class="headings">` pada 8 view modul SuperAdmin dipasangi inline style kaku:
     ```html
     <tr class="headings" style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
     ```
   - Inline style ini mengunci styling DOM, menyebabkan rendering Dark Mode terdistorsi dan stylesheet terpusat tidak bekerja optimal.

4. **Akar Masalah Teknis 3: Konflik Spesifisitas CSS (Gentelella Default vs MagKom Palette)**:
   - File `public/css/gentelella-theme.css` masih menyimpan aturan default Gentelella:
     ```css
     [data-theme="dark"] .table.jambo_table thead tr.headings {
         background: #16243C; /* Charcoal abu-abu kusam */
     }
     [data-theme="dark"] .table.jambo_table thead th {
         border-bottom-color: #26354D;
     }
     ```
   - Warna `#16243C` dan border `#26354D` ini tidak selaras dengan tema utama kartu MagKom (`#003566` / border `#3F51B5`), menciptakan visual "kotak hitam/abu-abu melayang" yang asing di dalam kartu.

5. **Akar Masalah Teknis 4: Zebra Striping & Hover State yang Belum Tersinkronisasi**:
   - Striping genap (`tr:nth-child(even)`) di mode gelap menggunakan latar `#151F32` bawaan Gentelella, dan efek hover baris masih memakai inline JS atau warna hardcoded, alih-alih transparan lembut yang harmonis dengan kanvas dashboard.

---

## 2. Kamus Desain & Spesifikasi Token CSS Tabel (DS-MagKom SSOT)

Untuk memastikan Junior Developer dan AI Coding Agent (Gemini 3.8 Flash) tidak mengarang warna sendiri, tabel berikut adalah **Single Source of Truth (SSOT)**:

| Komponen Tabel | Mode Terang (Light Mode) | Mode Gelap (Dark Mode Target) | Justifikasi Desain & Kontras |
| :--- | :--- | :--- | :--- |
| **Latar Header (`thead tr.headings`)** | `#F1F5F9` (Slate 100) | `#012758` (Deep MagKom Navy) | Kontras tegas dari bodi kartu, memberikan batas visual yang jelas |
| **Teks Header (`thead th`)** | `#1E293B` (Slate 800) | `#FFFFFF` (Pure White) / `#F8FAFC` | Kontras tajam **> 8.5:1** (Lolos WCAG AAA), teks kapital jelas dan terbaca |
| **Garis Pembatas Header (`border-bottom`)** | `2px solid #CBD5E1` (Slate 300) | `2px solid #3F51B5` (Indigo Accent) | Memisahkan header dari data baris dengan elegan |
| **Latar Baris Genap (`zebra stripe`)** | `#F8FAFC` (Neutral 100) | `rgba(255, 255, 255, 0.03)` | Lembut, tidak membuat mata lelah saat membaca data panjang |
| **Latar Baris Saat Hover (`tr:hover`)** | `rgba(1, 52, 124, 0.04)` | `rgba(63, 81, 181, 0.18)` | Respons visual instan via Pure CSS tanpa jeda event loop |
| **Border Sel Data (`tbody td border`)** | `1px solid #EEF0F5` | `1px solid rgba(63, 81, 181, 0.28)` | Garis pemisah halus dan presisi |
| **Teks Data Utama (`td` judul/nama)** | `#05070B` (Neutral 900) | `#FFFFFF` (Pure White) | Keterbacaan informasi kritis tertinggi |
| **Teks Data Sekunder (`td` caption/ID)** | `#64748B` (Slate 500) | `#94A3B8` (Slate 400) | Hierarki tipografi proporsional |

---

## 3. Rincian Teknis Implementasi (Step-by-Step Prescriptive Tasks)

### Task 4.1: Deklarasi Token CSS Sentral Tabel pada Master Layout
- **File Target**: `resources/views/layouts/dashboard.blade.php` (di dalam blok `<style>` `:root` dan `[data-theme="dark"]`).
- **Instruksi untuk Junior Developer / AI**:
  Tambahkan token CSS khusus tabel di root master layout agar dapat diakses oleh seluruh stylesheet dan komponen blade.
- **Snippet Penambahan Token**:
  ```css
  /* Table Tokens - Light Mode */
  :root {
      --table-header-bg: #F1F5F9;
      --table-header-color: #1E293B;
      --table-header-border: #CBD5E1;
      --table-row-border: #EEF0F5;
      --table-row-zebra: #F8FAFC;
      --table-row-hover: rgba(1, 52, 124, 0.04);
      --table-text-primary: #05070B;
      --table-text-secondary: #64748B;
  }

  /* Table Tokens - Dark Mode */
  [data-theme="dark"] {
      --table-header-bg: #012758;
      --table-header-color: #FFFFFF;
      --table-header-border: #3F51B5;
      --table-row-border: rgba(63, 81, 181, 0.28);
      --table-row-zebra: rgba(255, 255, 255, 0.03);
      --table-row-hover: rgba(63, 81, 181, 0.18);
      --table-text-primary: #FFFFFF;
      --table-text-secondary: #94A3B8;
  }
  ```

---

### Task 4.2: Refaktor Stylesheet Global `gentelella-theme.css`
- **File Target**: `public/css/gentelella-theme.css` (Baris ~735 s.d. ~805).
- **Instruksi untuk Junior Developer / AI**:
  Hapus background hardcoded `#16243C` dan border `#26354D`. Hubungkan seluruh class `.table.jambo_table` dengan variabel token CSS tabel.
- **Snippet Perubahan**:
  ```css
  /* SEBELUM */
  .table.jambo_table thead tr.headings {
      background: var(--blue-700, #0641CC);
      color: #FFFFFF;
  }
  [data-theme="dark"] .table.jambo_table thead tr.headings {
      background: #16243C;
  }
  [data-theme="dark"] .table.jambo_table thead th {
      border-bottom-color: #26354D;
      color: #F8FAFC;
  }

  /* SESUDAH (STANDAR DS-MAGKOM) */
  .table.jambo_table {
      width: 100%;
      margin-bottom: 0;
      border-collapse: separate;
      border-spacing: 0;
      border-radius: 8px;
      overflow: hidden;
      border: 1px solid var(--table-header-border);
      color: var(--table-text-primary);
      background-color: transparent;
  }

  .table.jambo_table thead tr.headings {
      background: var(--table-header-bg) !important;
      border-bottom: 2px solid var(--table-header-border) !important;
  }

  .table.jambo_table thead th {
      padding: 0.875rem 1rem !important;
      font-size: 0.75rem !important;
      font-weight: 700 !important;
      text-transform: uppercase !important;
      letter-spacing: 0.05em !important;
      color: var(--table-header-color) !important;
      border-bottom: 2px solid var(--table-header-border) !important;
      border-top: none !important;
      vertical-align: middle;
  }

  .table.jambo_table tbody td {
      padding: 0.875rem 1rem !important;
      vertical-align: middle !important;
      border-bottom: 1px solid var(--table-row-border) !important;
      color: var(--table-text-primary);
  }

  .table.jambo_table tbody tr:nth-child(even) {
      background-color: var(--table-row-zebra) !important;
  }

  .table.jambo_table tbody tr:hover {
      background-color: var(--table-row-hover) !important;
      transition: background-color 0.15s ease-in-out;
  }
  ```

---

### Task 4.3: Refaktor Stylesheet Komponen `superadmin-dashboard.css`
- **File Target**: `public/css/superadmin-dashboard.css` (Baris ~450 s.d. ~490).
- **Instruksi untuk Junior Developer / AI**:
  Standarisasi styling `.sa-table` dan `.table-responsive-gentelella` agar selaras dengan token sistem.
- **Snippet Perubahan**:
  ```css
  /* Modern Standard untuk .sa-table */
  .sa-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.875rem;
  }

  .sa-table thead tr {
      background: var(--table-header-bg) !important;
      border-bottom: 2px solid var(--table-header-border) !important;
  }

  .sa-table th {
      text-align: left;
      padding: 0.875rem 1rem;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--table-header-color) !important;
      border-bottom: 2px solid var(--table-header-border);
  }

  .sa-table td {
      padding: 0.875rem 1rem;
      color: var(--table-text-primary);
      border-bottom: 1px solid var(--table-row-border);
      vertical-align: middle;
  }

  .sa-table tbody tr:hover td {
      background-color: var(--table-row-hover) !important;
  }
  ```

---

### Task 4.4: Pembersihan Inline CSS & Eliminasi CSS Hack pada 10 View Blade SuperAdmin

Junior Developer / AI wajib memeriksa dan memperbaiki 10 file berikut:

#### 1. `resources/views/superadmin/bulan/index.blade.php` (Screen 4 - Bukti Utama Sir Alan)
- **Aksi 1 (Hapus CSS Hack)**:
  Di blok `@push('styles')`, hapus aturan berikut yang membuat teks header menjadi kelabu kusam:
  ```css
  /* HAPUS ATURAN INI */
  [data-theme="dark"] table th {
      color: #94A3B8 !important;
  }
  [data-theme="dark"] table thead tr {
      background-color: #02275D !important;
      border-bottom-color: #3F51B5 !important;
      color: #94A3B8 !important;
  }
  ```
- **Aksi 2 (Bersihkan Inline Style pada `<tr class="headings">`)**:
  Ubah baris ~134:
  ```html
  <!-- SEBELUM -->
  <tr class="headings" style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
  
  <!-- SESUDAH -->
  <tr class="headings">
  ```
- **Aksi 3 (Standarisasi `<th>`)**:
  Hapus inline styling yang menimpa padding/color, cukup pertahankan width dan alignment jika diperlukan:
  ```html
  <th style="width: 25%;">Bulan Magang</th>
  <th style="width: 30%;">Jadwal Buka Pendaftaran</th>
  <th style="width: 20%;">Override Status</th>
  <th style="width: 12%; text-align: center;">Status Aktif</th>
  <th style="width: 13%; text-align: right;">Aksi</th>
  ```

#### 2. `resources/views/superadmin/bidang/index.blade.php` (Screen 1)
- Hapus `[data-theme="dark"] table th { color: #94A3B8 !important; }` di `@push('styles')`.
- Bersihkan inline style pada `<tr class="headings" style="...">` menjadi murni `<tr class="headings">`.

#### 3. `resources/views/superadmin/jurusan/index.blade.php` (Screen 2)
- Hapus `[data-theme="dark"] table th { color: #94A3B8 !important; }` di `@push('styles')`.
- Bersihkan inline style pada `<tr class="headings" style="...">` menjadi murni `<tr class="headings">`.

#### 4. `resources/views/superadmin/slot-khusus/index.blade.php` (Screen 3)
- Hapus `[data-theme="dark"] table th { color: #94A3B8 !important; }` di `@push('styles')`.
- Bersihkan inline style pada `<tr class="headings" style="...">` menjadi murni `<tr class="headings">`.

#### 5. `resources/views/superadmin/rubrik/index.blade.php` (Screen 8)
- Hapus `[data-theme="dark"] table th { color: #94A3B8 !important; }` di `@push('styles')`.
- Bersihkan inline style pada `<tr class="headings" style="...">` menjadi murni `<tr class="headings">`.

#### 6. `resources/views/superadmin/role/index.blade.php` (Screen 9)
- Hapus `[data-theme="dark"] table th { color: #94A3B8 !important; }` di `@push('styles')`.
- Bersihkan inline style pada `<tr class="headings" style="...">` menjadi murni `<tr class="headings">`.

#### 7. `resources/views/superadmin/staf/index.blade.php` (Screen 10)
- Hapus `[data-theme="dark"] table th { background-color: #02275D !important; }` di `@push('styles')`.
- Bersihkan inline style pada `<tr class="headings" style="...">` menjadi murni `<tr class="headings">`.

#### 8. `resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php` (Tabel Antrean FIFO)
- Pastikan class `thead tr` pada `.sa-table` otomatis mewarisi `--table-header-bg` dan `--table-header-color`.
- Verifikasi kolom `No.`, `Pendaftar & Institusi`, `Jurusan & Bulan`, `Waktu Ajuan`, `Status`, `Aksi` memiliki kontras teks putih jernih di Dark Mode.

#### 9. `resources/views/superadmin/audit-log/index.blade.php`
- Bersihkan styling inline pada thead tabel log audit.

#### 10. `resources/views/superadmin/monitoring/index.blade.php` & `arsip/index.blade.php`
- Pasang class `.table.jambo_table` atau `.sa-table` yang telah distandarisasi untuk tabel monitoring dan arsip peserta.

---

### Task 4.5: Standarisasi Kolom Khusus & Action Buttons
- **Pill Badge Status (`Aktif` / `Nonaktif`)**:
  Pastikan badge status menggunakan class terstandarisasi (`sa-badge-green`, `sa-badge-red`, `sa-badge-blue`) tanpa inline CSS manual.
- **Action Buttons (`Edit` / `Nonaktifkan`)**:
  Pastikan tombol aksi menggunakan `sa-btn-action-outline` dan `sa-btn-action-danger` dengan padding konsisten (`0.45rem 0.875rem`) dan border-radius (`6px`).
- **ID Monospace (`ID #12`)**:
  Terapkan warna `--table-text-secondary` (`#64748B` Light / `#94A3B8` Dark) dengan font-weight 500 dan ukuran 0.75rem.

---

## 4. Panduan Khusus Junior Developer / AI Agent (Do's and Don'ts)

### DO:
1. **Gunakan CSS Variables**: Selalu rujuk ke `--table-header-bg`, `--table-header-color`, `--table-header-border` yang terdefinisi di `:root`.
2. **Gunakan Pure CSS Hover**: Pastikan hover baris tabel dihandle oleh `:hover` CSS, bukan script inline `onmouseover/onmouseout`.
3. **Pastikan Kontras Tinggi (WCAG AA)**: Teks header tabel di Dark Mode **WAJIB** berwarna Putih (`#FFFFFF`) atau Slate Sangat Terang (`#F8FAFC`). Jangan pernah menggunakan Slate Kusam (`#94A3B8`) untuk teks header!
4. **Jalankan Uji Regresi**: Pastikan suite test SuperAdmin tetap 100% passed setelah perubahan markup.

### DON'T:
1. **Jangan Pasang Inline Style**: Jangan menuliskan `style="background: ...; color: ...;"` langsung pada tag `<tr>` atau `<th>`.
2. **Jangan Gunakan Hardcoded Hex di File Blade**: Hindari menulis `#16243C` atau `#26354D` secara sporadis di file Blade.
3. **Jangan Ubah Logika Backend**: Jangan mengubah controller, model, route, atau method backend saat merefaktor tampilan tabel.

---

## 5. Checklist Verifikasi & Quality Gate

Junior Developer atau AI Agent dinyatakan selesai jika memenuhi seluruh kriteria verifikasi berikut:

- [ ] **Visual Test - Header Table Dark Mode**:
  - Teks header di seluruh tabel modul SuperAdmin tampil putih bersih (`#FFFFFF` / `#F8FAFC`), tajam, dan memiliki rasio kontras > 8:1 terhadap background `#012758`.
  - Border bawah thead tampil tegas dengan aksen Indigo `#3F51B5`.
- [ ] **Visual Test - Header Table Light Mode**:
  - Teks header tampil Slate 800 (`#1E293B`) di atas background Slate 100 (`#F1F5F9`) dengan border Slate 300 (`#CBD5E1`).
- [ ] **Visual Test - Zebra & Hover State**:
  - Baris selang-seling tampil transparan halus tanpa menabrak warna background kartu (`#003566` di Dark Mode).
  - Hover baris aktif secara instan tanpa glitch/delay JavaScript.
- [ ] **Code Hygiene**:
  - Inline style pada `<tr class="headings">` di 8 file blade terhapus 100%.
  - Blok CSS hack `[data-theme="dark"] table th { color: #94A3B8 !important; }` di `@push('styles')` tereliminasi.
- [ ] **Automated Tests**:
  - Menjalankan `php artisan test --filter=SuperAdmin` dan memastikan **142 tests passed (833 assertions)** tanpa kegagalan.
- [ ] **Vite Asset Compilation**:
  - Menjalankan `npm run build` dan memastikan aset terkompilasi tanpa warning/error.
- [ ] **AST Knowledge Graph**:
  - Menjalankan `graphify update .` untuk memperbarui graph keterhubungan kode.

---

## 6. Manifest File Perubahan

| No | Path File | Jenis Perubahan | Deskripsi Singkat |
| :--- | :--- | :--- | :--- |
| 1 | `resources/views/layouts/dashboard.blade.php` | Blade Layout / CSS Variables | Menambahkan deklarasi token `--table-*` (Light & Dark) |
| 2 | `public/css/gentelella-theme.css` | Stylesheet Global | Standarisasi `.table.jambo_table`, `.headings`, `th`, `td`, zebra, hover |
| 3 | `public/css/superadmin-dashboard.css` | Stylesheet Komponen | Standarisasi `.sa-table`, padding sel, dan scroll container |
| 4 | `resources/views/superadmin/bulan/index.blade.php` | Blade View Refactor | Bersihkan inline style thead, eliminasi CSS hack `#94A3B8` |
| 5 | `resources/views/superadmin/bidang/index.blade.php` | Blade View Refactor | Bersihkan inline style thead, eliminasi CSS hack `#94A3B8` |
| 6 | `resources/views/superadmin/jurusan/index.blade.php` | Blade View Refactor | Bersihkan inline style thead, eliminasi CSS hack `#94A3B8` |
| 7 | `resources/views/superadmin/slot-khusus/index.blade.php` | Blade View Refactor | Bersihkan inline style thead, eliminasi CSS hack `#94A3B8` |
| 8 | `resources/views/superadmin/rubrik/index.blade.php` | Blade View Refactor | Bersihkan inline style thead, eliminasi CSS hack `#94A3B8` |
| 9 | `resources/views/superadmin/role/index.blade.php` | Blade View Refactor | Bersihkan inline style thead, eliminasi CSS hack `#94A3B8` |
| 10 | `resources/views/superadmin/staf/index.blade.php` | Blade View Refactor | Bersihkan inline style thead, eliminasi CSS hack `#94A3B8` |
| 11 | `resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php` | Blade View Refactor | Harmonisasi tabel antrean verifikasi FIFO |
| 12 | `resources/views/superadmin/audit-log/index.blade.php` | Blade View Refactor | Standarisasi header dan baris tabel audit log |
