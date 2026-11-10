# Task Senior Developer: Penyelarasan Komprehensif Palet Warna & Kontras WCAG Seluruh Tab Cockpit SuperAdmin

- **ID Tiket**: `TASK-003`
- **Target Pelaksana**: Full-Stack Developer / AI Coding Agent (Gemini 3.8 Flash Ready)
- **Komponen Target**:
  1. *Statusline Horizontal Ribbon* (`.sa-statusline`) di [dashboard.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/superadmin/dashboard.blade.php)
  2. *Single Hazard SLA Attention Card* (`.sa-attn-hazard`) di [_attention.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/superadmin/dashboard/partials/_attention.blade.php)
  3. *Tab 2: Monitoring Seleksi & Kuota* di [_tab-seleksi.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php)
  4. *Tab 3: Pelaksanaan & Bimbingan* di [_tab-bimbingan.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php)
  5. *Tab 4: Tata Kelola Sistem & Laporan* di [_tab-sistem.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php)
  6. *Styling Terpusat & CSS Tokens*: [superadmin-dashboard.css](file:///d:/MAGKOM/MAGKOM/public/css/superadmin-dashboard.css)
- **Dokumen Referensi**: PRD MagKom Revisi 5 / Design System DS-MagKom Bab 2 / Gentelella Modern Dashboard Standard / WCAG 2.1 AA Contrast (4.5:1)
- **Tingkat Kesulitan**: Sedang (CSS Tokens Harmonization, Button Class Regularization, & Strict Contrast Fix)
- **Tanggal Diterbitkan**: 28 Oktober 2026
- **Status Tiket**: Draft Plan Diperbarui Komprehensif (Awaiting User Confirmation)

---

## 1. Latar Belakang & Analisis Masalah

Berdasarkan 4 tangkapan layar visual terkini pada Dashboard SuperAdmin di lingkungan Gentelella (mode gelap & mode terang), ditemukan inkonsistensi palet warna, kegagalan kontras teks (WCAG AA), dan masalah perenderan tombol aksi interaktif di seluruh tab:

```
[AUDIT VISUAL TEMUAN MASALAH PER TAB & KOMPONEN COCKPIT]
──────────────────────────────────────────────────────────────────────────────────
1. RINGKASAN UTAMA (Statusline Ribbon & Hazard SLA Card):
   - Statusline ribbon (.sa-statusline) dan Hazard Attention card (.sa-attn-hazard)
     menggunakan latar kusam #182234 yang tidak serasi dengan palet Navy MagKom
     (#003566 & border #3F51B5).
   - Chip pendaftar (.sa-hazard-chip) berlatar krem terang (#fffbeb) menyilaukan di dark mode.
   - Tombol "Buka antrean ->" dituliskan class="btn-primary" tanpa prefix .btn,
     sehingga di Gentelella dirender sebagai tautan teks mentah tanpa background dengan
     warna ungu gelap (#7C3AED / default unstyled) yang tidak terbaca di latar gelap.

2. TAB 2: MONITORING SELEKSI & KUOTA (_tab-seleksi.blade.php):
   - Tabel Antrean FIFO: Tombol "Tinjau & Putuskan >" menggunakan class="btn-primary sa-btn-sm"
     tanpa .btn -> menjadi tautan mentah berwarna biru/ungu gelap di atas navy gelap.
   - Badge "Perlu Perbaikan": Menggunakan warna teks dan latar biru pekat identik
     sehingga teks tidak terbaca (contrast drop).
   - Banner Pagu Kuota Dinas Bulanan: Menggunakan inline style background putih
     linear-gradient(#F8FAFC, #FFFFFF). Di mode gelap, teks .sa-table-strong mewarisi
     warna putih (--cv-ink: #f8fafc) -> TEKS PUTIH DI ATAS LATAR PUTIH (Hilang Total!).
   - Angka Keterisian Jurusan: Menggunakan var(--cv-blue-800) yang di dark mode
     merupakan biru dongker gelap, tenggelam di atas latar navy.
   - Slot Khusus Kemitraan: Angka "1 / 5" menggunakan style color: #7C3AED (ungu gelap),
     nyaris hitam dan tidak terbaca di dark mode. Badge ungu pucat #EDE9FE menyilaukan.
   - Tombol "Kelola Antrean Waiting List" & "Atur Slot Khusus": class="btn-secondary" tanpa .btn.

3. TAB 3: PELAKSANAAN & BIMBINGAN (_tab-bimbingan.blade.php):
   - 4 Kartu Metrik Bimbingan: Angka "Total Tugas Aktif" (nilai 4) menggunakan
     var(--cv-blue-800) -> biru tua pekat di atas navy pekat (tak terlihat).
     Angka "Penilaian Final Selesai" (nilai 2) menggunakan #059669 -> hijau gelap tenggelam.
   - Pengawasan Tugas (Overdue & Deadline): Icon background #FEF2F2 & #FFFBEB adalah
     warna pastel terang mode terang yang menyilaukan di dark mode.
   - Matriks Beban Pembimbing: Tautan "Kelola Penempatan" & "Monitoring Peserta" menggunakan
     var(--cv-blue-700) (biru pekat tak terbaca). Badge "10 Mentee" berwarna gelap di navy gelap.
   - Pelacak Off-boarding: Badge Bidang Penempatan "Aplikasi Informatika & E-Government (EGOV)"
     menggunakan .sa-badge--info dengan teks biru tua di atas pill biru tua (unreadable!).

4. TAB 4: TATA KELOLA SISTEM & LAPORAN (_tab-sistem.blade.php):
   - Hub Ekspor 4 Laporan: Badge nomor "01", "02", "03", "04" menggunakan background
     var(--cv-blue-tint) dan text var(--cv-blue-800) (biru pekat di navy gelap).
     Tombol "Unduh CSV ..." menggunakan class="btn-primary" tanpa .btn -> link mentah tanpa tombol.
   - Threat Radar & Keamanan: Nilai "Vault Dokumen Privat" (Maks. 2 MB) menggunakan
     var(--cv-blue-800) yang gelap dan tenggelam di background navy.
   - Pintasan Kontrol Master Data & Konfigurasi: Seluruh link "Lihat ->", "Kelola ->",
     "Atur ->", "Ubah ->", "Buka ->" menggunakan var(--cv-blue-700) pekat tak terbaca.
   - Integritas Infrastruktur: Tautan "Buka Healthcheck API ->" gelap tak terbaca;
     icon box butuh perlakuan transparan halus (alpha tint) di mode gelap.
──────────────────────────────────────────────────────────────────────────────────
```

---

## 2. Matriks Spesifikasi Desain (Current vs Target Specification)

| Area & Komponen | Mode Terang (Light Mode Target) | Mode Gelap: Kondisi Saat Ini (SALAH) | Mode Gelap: Target Penyelarasan (BENAR) |
| :--- | :--- | :--- | :--- |
| **Statusline Ribbon** | Bg `#FFFFFF`, Border `#E2E8F0`, Teks `#0F172A` | Bg `#182234` (Slate kusam), Border `#26354D` | **Bg `#003566` (Navy MagKom), Border `#3F51B5`, Teks `#FFFFFF` & `#CBD5E1`** |
| **Hazard SLA Card** | Bg `#FFFFFF`, Border `#E2E8F0`, Tombol solid Sleman Blue | Bg `#182234`, Chip krem `#fffbeb`, Tombol ungu mentah | **Bg `#003566`, Border `#3F51B5`, SLA Gold `#FFCB05`, Chip `rgba(245,158,11,0.2)` & Teks `#FDE68A`, Tombol solid `#3F51B5`** |
| **Tombol Aksi Interaktif (Semua Tab)** | `.btn.btn-primary`: Bg `#01347C`, Teks `#FFFFFF`<br>`.btn.btn-secondary`: Bg `#F1F5F9`, Teks `#334155` | `btn-primary` / `btn-secondary` tanpa `.btn` -> Tautan teks unstyled ungu/biru tanpa background | **`.btn.btn-primary`: Bg `#3F51B5`, Hover `#01347C`, Teks `#FFFFFF`, Padding tegas & solid** |
| **Tab 2: Banner Pagu Kuota (BR-01)** | Bg Gradient `#F8FAFC` ke `#FFFFFF`, Teks Gelap `#0F172A` | Inline style putih statis + teks mewarisi `#f8fafc` -> Teks putih di atas putih | **Bg `#003566` (atau `#02275D`), Border `#3F51B5`, Teks Label `#CBD5E1`, Angka `#FFFFFF`** |
| **Tab 2: Keterisian Kuota Jurusan** | Angka `#01347C` / Teks `#1E293B` | `var(--cv-blue-800)` (#01347C gelap di navy) | **Teks `#CBD5E1`, Angka `#93C5FD` / `#FFFFFF` (Kontras Tinggi)** |
| **Tab 2: Slot Khusus Kemitraan** | Angka `#7C3AED` (Ungu), Badge `#EDE9FE` / `#6D28D9` | Nilai `1 / 5` hitam/gelap (`#7C3AED`), Badge silau | **Angka `#C4B5FD` (Ungu Terang) atau `#FFCB05` (Gold), Badge `rgba(167,139,250,0.18)` & Teks `#DDD6FE`** |
| **Tab 2: Badge Perlu Perbaikan** | Bg `#EFF6FF`, Border `#BFDBFE`, Teks `#1D4ED8` | Teks dan background biru gelap nyaris identik | **Bg `rgba(59,130,246,0.2)`, Border `rgba(59,130,246,0.4)`, Teks `#93C5FD`** |
| **Tab 3: 4 Metrik Bimbingan** | Bg `#FFFFFF`, Nilai `#01347C` / `#D97706` / `#059669` | Angka Tugas `var(--cv-blue-800)` pekat, Nilai Selesai `#059669` pekat | **Tugas Aktif: `#38BDF8` / `#FFFFFF`, Pending: `#FFCB05`, Selesai: `#34D399`, Staf: `#FFFFFF`** |
| **Tab 3: Card Tugas Overdue & Deadline** | Overdue: Border `#FECACA`, Icon `#FEF2F2`<br>Deadline: Border `#FDE68A`, Icon `#FFFBEB` | Icon bg pastel terang menyilaukan di atas dark mode | **Overdue: Bg `rgba(239,68,68,0.15)`, Border `#EF4444`, Teks `#F87171`<br>Deadline: Bg `rgba(245,158,11,0.15)`, Teks `#FBBF24`** |
| **Tab 3: Matriks Pembimbing & Offboarding** | Pill Mentee & Badge EGOV biru rapi | Pill Mentee & Badge EGOV teks biru gelap di navy gelap | **Pill Mentee & Badge EGOV: Bg `rgba(59,130,246,0.25)`, Teks `#93C5FD`, Border `rgba(147,197,253,0.3)`** |
| **Tab 4: Hub Ekspor 4 Laporan** | Badge angka `01`-`04` biru Sleman, Tombol unduh solid | Badge angka biru pekat, tombol unduh teks mentah | **Badge angka `01`-`04`: Bg `rgba(59,130,246,0.2)`, Teks `#93C5FD`<br>Tombol unduh: `.btn.btn-primary` solid `#3F51B5`** |
| **Tab 4: Threat Radar (Vault 2 MB)** | Nilai `Maks. 2 MB` Sleman Blue `#01347C` | Nilai `Maks. 2` biru tua pekat tenggelam | **Nilai `Maks. 2 MB`: `#38BDF8` / `#FFFFFF` tegas bercahaya** |
| **Tautan Navigasi (Seluruh Panel & List)** | `.sa-link`: `#0751FF` / `#01347C` | `var(--cv-blue-700)` pekat tidak terbaca di navy | **`.sa-link`: `#60A5FA` / `#93C5FD` (Hover: `#BAE6FD` dengan underline)** |

---

## 3. Matriks Perubahan File (File Change Matrix)

| No | File Path | Aksi | Ringkasan Perubahan |
|:---|:---|:---|:---|
| 1 | `public/css/superadmin-dashboard.css` | **Modify** | 1. Tambahkan CSS tokens dark mode terpadu (`--cv-warn-tint`, `--cv-danger-tint`, `--cv-info-tint`, `--cv-link-color`).<br>2. Definisikan styling eksplisit `.sa-statusline` dan `.sa-attn-hazard` pada mode terang dan gelap.<br>3. Standarisasi selector tombol `.btn-primary` dan `.btn-secondary` dalam konteks `.sa-page` agar menjamin styling solid.<br>4. Perbaiki styling box `.sa-pagu-box`, `.sa-export-card`, metrik bimbingan, hazard cards, dan badge dark mode.<br>5. Override warna `.sa-link` di mode gelap ke `#60A5FA` / `#93C5FD`. |
| 2 | `resources/views/superadmin/dashboard/partials/_attention.blade.php` | **Modify** | Perbaiki class tombol dari `class="btn-primary"` menjadi `class="btn btn-primary"`. |
| 3 | `resources/views/superadmin/dashboard/partials/_tab-seleksi.blade.php` | **Modify** | 1. Tambahkan class `.btn` pada tombol `Tinjau & Putuskan` (`btn btn-primary sa-btn-sm`) dan tombol waiting list/slot khusus.<br>2. Pindahkan inline background gradient pada `.sa-pagu-box` ke class CSS responsif dual-theme.<br>3. Ganti warna hardcoded ungu `#7C3AED` pada slot khusus dan `var(--cv-blue-800)` kuota jurusan ke token CSS adaptif.<br>4. Selaraskan badge status `Perlu Perbaikan` ke styling semantik yang kontras. |
| 4 | `resources/views/superadmin/dashboard/partials/_tab-bimbingan.blade.php` | **Modify** | 1. Ganti pewarnaan inline hardcoded pada 4 nilai metrik bimbingan ke class CSS semantik adaptif.<br>2. Perbaiki styling icon container & border kartu kedisiplinan tugas agar tidak menyilaukan di dark mode.<br>3. Perbaiki class badge mentee dan badge bidang penempatan di tabel off-boarding agar terbaca jelas. |
| 5 | `resources/views/superadmin/dashboard/partials/_tab-sistem.blade.php` | **Modify** | 1. Tambahkan class `.btn` pada 4 tombol `Unduh CSV ...` (`btn btn-primary sa-btn-sm sa-btn-full`).<br>2. Ganti inline background & color pada nomor badge ekspor `01`-`04` menjadi class CSS adaptif.<br>3. Ganti inline color `var(--cv-blue-800)` pada kartu Vault Dokumen Privat ke class metrik adaptif.<br>4. Selaraskan seluruh link pintasan kontrol master data dan status layanan agar menggunakan token link terang di dark mode. |
| 6 | `resources/views/superadmin/dashboard.blade.php` | **Modify** | Sesuaikan warna teks dropdown periode `#select-bulan` agar adaptif dan jelas di dark mode. |
| 7 | `tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php` | **Verify** | Jalankan regression test suite untuk memastikan 11 feature tests tetap lulus 100% tanpa regresi. |

---

## 4. Rencana Langkah Eksekusi Terstruktur

- [ ] **Langkah 1: Perluasan Token & Utilitas CSS Terpusat di `superadmin-dashboard.css`**
  - Definisikan token warna adaptif untuk mode terang dan gelap:
    - Statusline & Attention Hazard Card (`#003566` & `#3F51B5`)
    - Aturan tombol global `.btn-primary` dan `.btn-secondary`
    - Aturan link `.sa-link` adaptif di dark mode (`#60A5FA`)
    - Aturan `.sa-pagu-box` adaptif (light: gradient abu-putih, dark: surface `#003566` + border `#3F51B5`)
    - Aturan `.sa-badge--info`, `.sa-badge--warning`, `.sa-badge--danger` di dark mode menggunakan translucent background dengan border dan teks cerah
- [ ] **Langkah 2: Perbaikan Komponen Attention Hazard Card & Dropdown Periode**
  - Perbaiki class button di `_attention.blade.php` (`class="btn btn-primary"`)
  - Pastikan dropdown `#select-bulan` di `dashboard.blade.php` memiliki warna teks kontras di dark mode
- [ ] **Langkah 3: Perbaikan Komponen Tab 2 (Seleksi & Kuota)**
  - Standarisasi tombol `Tinjau & Putuskan` menjadi `btn btn-primary sa-btn-sm`
  - Hilangkan inline background statis `#FFFFFF` pada `.sa-pagu-box` dan hubungkan ke styling CSS dual-theme
  - Standarisasi tombol `Kelola Antrean Waiting List` dan `Atur Slot Khusus Kemitraan`
  - Perbaiki kontras teks angka keterisian kuota dan angka slot khusus kemitraan
- [ ] **Langkah 4: Perbaikan Komponen Tab 3 (Pelaksanaan & Bimbingan)**
  - Standarisasi warna teks 4 kartu metrik bimbingan di dark mode
  - Adaptasi visual kartu overdue dan deadline (icon container & border dark mode)
  - Perbaiki kontras badge `10 Mentee` dan badge bidang penempatan di tabel off-boarding
- [ ] **Langkah 5: Perbaikan Komponen Tab 4 (Tata Kelola Sistem & Laporan)**
  - Standarisasi 4 tombol unduh CSV di Hub Ekspor Laporan (`btn btn-primary sa-btn-sm sa-btn-full`)
  - Perbaiki badge nomor `01`-`04` dan nilai Vault Dokumen Privat `Maks. 2 MB`
  - Selaraskan seluruh tautan pintasan kontrol sistem ke styling link terang
- [ ] **Langkah 6: Verifikasi & Testing Komprehensif**
  - Jalankan `php artisan test --filter=SuperAdminUnifiedDashboardFeaturesTest`
  - Verifikasi tidak ada class atau atribut yang memicu regresi pada test assertion
  - Pastikan kepatuhan kontras WCAG AA (rasio minimal 4.5:1 untuk teks biasa dan 3:1 untuk teks besar/badge)

---

## 5. Pertimbangan & Guardrail Keamanan

1. **Integritas Atribut Testing**:
   - Seluruh atribut `role="meter"`, `aria-valuenow`, `aria-label`, data test, dan teks kunci (seperti `Kepatuhan Pagu Kuota Dinas (BR-01)`, `Antrean Waiting List (BR-05)`, `Hub Ekspor Instan Laporan Resmi Dinas`, dll.) **TIDAK BOLEH DIUBAH** karena di-assert langsung oleh `SuperAdminUnifiedDashboardFeaturesTest.php`.
2. **Backward Compatibility**:
   - Perubahan hanya berfokus pada layer presentasional (Blade templates & CSS stylesheet). Tidak ada logika controller, query Eloquent, atau route yang dimodifikasi.
3. **Atomic Changes**:
   - Setiap modifikasi Blade dan CSS dilakukan secara terstruktur dan terverifikasi per tab.
