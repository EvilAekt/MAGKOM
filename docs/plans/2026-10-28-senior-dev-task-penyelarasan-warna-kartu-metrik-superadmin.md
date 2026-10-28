# Task Senior Developer: Penyelarasan Palet Warna 4 Kartu Metrik SuperAdmin (Mode Terang Putih & Mode Gelap Navy #003566)

- **ID Tiket**: `TASK-002`
- **Target Pelaksana**: Junior Full-Stack Developer / AI Coding Agent (Gemini 3.8 Flash Ready)
- **Komponen Target**: 4 Kartu Metrik Utama SuperAdmin (`_metrics.blade.php`):
  1. *Total pengguna* (`32 +14% / +14 minggu ini / Seluruh akun terdaftar`)
  2. *Menunggu verifikasi* (`3 3 SLA / 3 lewat SLA · FIFO / Berkas perlu ditinjau`)
  3. *Peserta aktif* (`10 Aktif / 7 masa tenggang · 9 selesai ≤ 14 hari / 9 Jalur Umum • 1 Jalur Khusus / 1 Alumni Selesai • 1 Mengundurkan Diri`)
  4. *Akun nonaktif* (`2 Nonaktif / Akses ditangguhkan`)
- **Dokumen Referensi**: Design System DS-MagKom (Bab 2), Gentelella Modern Dashboard Standard, Figma Node 560-26404
- **Tingkat Kesulitan**: Mudah ke Sedang (Pure CSS & Styling Refinement)
- **Tanggal Diterbitkan**: 28 Oktober 2026
- **Status Tiket**: Siap Dikerjakan (Ready for Implementation)

---

## 1. Latar Belakang & Ringkasan Masalah (Problem Statement)

Berdasarkan laporan evaluasi antarmuka pada Dashboard SuperAdmin (`http://localhost/magkom/public/superadmin/dashboard`) serta bukti tangkapan layar terlampir:

```
[KONDISI TERCATAT PADA DASHBOARD SAAT INI]
──────────────────────────────────────────────────────────────────────────────────
A. MODE GELAP (Dark Mode):
   - Seluruh kartu panel di dashboard ("Perlu tindakan hari ini", "Pengguna terbaru",
     "Audit log terbaru", panel KPI, dan kartu-kartu tab) menggunakan tema Navy resmi
     MagKom: background #003566 / #02275D dengan kontur border halus #3F51B5.
   - NAMUN, 4 kartu metrik utama di bagian atas justru berlatar belakang abu-abu
     gelap/hitam kusam (#161d2d) dengan border transparan tipis.
   - Dampak UX: 4 kartu tampak seperti elemen asing ("belang"), memecah kesatuan
     desain dashboard gelap yang elegan.

B. MODE TERANG (Light Mode):
   - Seluruh panel data di dashboard ("Pengguna terbaru", "Audit log terbaru", tabel,
     dan form) menggunakan latar belakang PUTIH BERSIH (#FFFFFF) dengan border abu-abu
     netral (#BBBBBB / #E2E8F0) dan aksen elegan.
   - NAMUN, 4 kartu metrik utama di bagian atas justru dipaksa berlatar belakang BIRU TUA
     PEKAT (#01347C) dengan teks putih solid tebal.
   - Dampak UX: 4 kartu terlihat terlalu berat (heavy blocky), membebani hierarki visual,
     dan tidak konsisten dengan konsep kanvas putih Gentelella.
──────────────────────────────────────────────────────────────────────────────────
```

### Kebutuhan Pengguna (User Requirement)
1. **Pada Mode Gelap**: Samakan warna latar dan kontur 4 kartu metrik dengan kartu-kartu lain di dashboard (`#003566` dengan border `#3F51B5`), sehingga harmonis dan tidak lagi berbeda.
2. **Pada Mode Terang**: Samakan warna latar dan kontur 4 kartu metrik dengan kartu-kartu lain di dashboard (`#FFFFFF` dengan border netral `#E2E8F0`), angka metrik tegas gelap (`#0F172A`), label abu-abu slate (`#64748B`), dan tetap mempertahankan badge icon berwarna tematik.

---

## 2. Analisis Teknis & Akar Masalah (Root Cause Analysis)

Setelah dilakukan audit kode terhadap file CSS dan Blade terkait, ditemukan dua titik tabrakan aturan (*CSS Collision & Specificity Conflict*):

### Akar Masalah 1: Mode Gelap Ter-override oleh `superadmin-dashboard.css`
Pada file `public/css/superadmin-dashboard.css` baris 1267–1271:
```css
[data-theme="dark"] .sa-metric-card-col .tile_stats_inner {
  background: #161d2d !important;
  border-color: rgba(255, 255, 255, 0.08) !important;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25) !important;
}
```
Aturan ini menimpa (*override*) aturan tema gelap global di `resources/views/layouts/dashboard.blade.php` (baris 2396–2405) yang seharusnya menetapkan `.tile_stats_inner` ke `#003566 !important` dan border `#3F51B5 !important`. Akibatnya, kartu metrik menjadi abu-abu gelap `#161d2d`.

### Akar Masalah 2: Mode Terang Dipaksa Biru oleh `dashboard.blade.php`
Pada file `resources/views/layouts/dashboard.blade.php` baris 2336–2344:
```css
html:not([data-theme="dark"]) .card-highlight,
html:not([data-theme="dark"]) .stat-card-accent,
html:not([data-theme="dark"]) .cv-stat-card,
html:not([data-theme="dark"]) .tile_stats_inner {
    background-color: #01347C !important;
    border: 1px solid #3F51B5 !important;
    box-shadow: 0 10px 20px -3px rgba(1, 52, 124, 0.22), 0 4px 6px -4px rgba(1, 52, 124, 0.15) !important;
    color: #FFFFFF !important;
}
```
Aturan `html:not([data-theme="dark"]) .tile_stats_inner` menggunakan `!important`. Meskipun di `superadmin-dashboard.css` sudah dideklarasikan `background: var(--neutral-0, #ffffff);`, aturan biru pekat `#01347C !important` tetap menang karena deklarasi lokal tidak memiliki `!important`.

---

## 3. Matriks Spesifikasi Desain (Current vs Target Specification)

| Elemen Styling | Mode Terang: Saat Ini (SALAH) | Mode Terang: Target (BENAR) | Mode Gelap: Saat Ini (SALAH) | Mode Gelap: Target (BENAR) |
| :--- | :--- | :--- | :--- | :--- |
| **Latar Belakang Card** | `#01347C` (Biru Pejal Berat) | **`#FFFFFF` (Putih Bersih Gentelella)** | `#161d2d` (Abu-abu Gelap Kusam) | **`#003566` (Navy MagKom Seragam)** |
| **Garis Batas (Border)** | `1px solid #3F51B5` | **`1px solid #E2E8F0` / `#D0D3D9`** | `1px solid rgba(255,255,255,0.08)` | **`1px solid #3F51B5` (Kontur Indigo)** |
| **Bayangan (Shadow)** | `0 10px 20px rgba(1,52,124,0.22)` | **`0 1px 3px rgba(15,23,42,0.06)`** | `0 2px 6px rgba(0,0,0,0.25)` | **`0 8px 24px rgba(0,0,0,0.5)`** |
| **Angka Nilai (`.count`)** | `#FFFFFF` (Putih) | **`#0F172A` (Slate 900 Sangat Tegas)** | `#FFFFFF` (Putih) | **`#FFFFFF` (Putih Bersih Tegas)** |
| **Judul/Label (`.count_top`)**| `#BBBBBB` (Abu kusam) | **`#64748B` (Slate 500 Terbaca Jelas)** | `#94A3B8` (Slate Muted) | **`#CBD5E1` / `#BBBBBB` (Terang Jelas)**|
| **Subtext (`.count_bottom`)** | `rgba(255,255,255,0.8)` | **`#64748B` (Keterangan Rapi)** | `#94A3B8` | **`#CBD5E1` (Kontras Tinggi)** |
| **Ikon Badge Kiri** | Background solid / kuning emas | **Soft tint transparan (Teal/Yellow/Blue/Red)** | Redup di abu-abu | **Transparan kontras tinggi di atas Navy** |
| **Sparkline Bar Kanan** | Terdistorsi palet pekat | **Toska/Kuning/Biru/Merah Lembut** | Kusam | **Menyala Kontras di atas Navy** |

---

## 4. Matriks Perubahan File (File Change Matrix)

| No | File Path | Aksi | Deskripsi Perubahan |
|:---|:---|:---|:---|
| 1 | `public/css/superadmin-dashboard.css` | **Modify** | 1. Berikan prioritas tinggi `!important` pada mode terang agar `.tile_stats_inner` menjadi putih murni (`#ffffff`), border `#e2e8f0`, dan teks gelap.<br>2. Perbarui aturan `[data-theme="dark"]` agar background menjadi `#003566 !important` dan border `#3F51B5 !important`.<br>3. Sesuaikan kontras teks label, subtext, dan icon agar memenuhi standar aksesibilitas WCAG AA. |
| 2 | `resources/views/layouts/dashboard.blade.php` | **Modify** | Pastikan selektor global `html:not([data-theme="dark"]) .tile_stats_inner` tidak memaksakan background biru `#01347C !important` pada kartu dashboard yang membutuhkan tema putih bersih (lepaskan `.tile_stats_inner` dari grup blok tersebut atau beri pengecualian khusus). |
| 3 | `tests/Feature/SuperAdminFigmaDesignAlignmentTest.php` | **Verify** | Menjalankan test suite untuk memastikan assertion teks 4 kartu metrik tetap 100% lolos. |
| 4 | `tests/Feature/DeltaDashboardEnhancementTest.php` | **Verify** | Menjalankan test suite untuk memastikan metrik quick stats dashboard tetap valid. |

---

## 5. Petunjuk Langkah Kerja Terperinci (Step-by-Step Implementation Guide)

### Langkah 1: Modifikasi `public/css/superadmin-dashboard.css`

Buka file [public/css/superadmin-dashboard.css](file:///g:/MAGKOM/public/css/superadmin-dashboard.css):

#### A. Bagian Mode Terang (Default):
Cari blok `.sa-metric-card-col .tile_stats_inner` (sekitar baris 1087–1107):
Pastikan deklarasi menggunakan `!important` untuk menembus aturan layout:
```css
/* =========================================================================
   1. KARTU METRIK MODE TERANG (Default: Putih Bersih Seragam dengan Card Lain)
   ========================================================================= */
.sa-metric-card-col .tile_stats_inner,
html:not([data-theme="dark"]) .sa-metric-card-col .tile_stats_inner {
  background-color: #ffffff !important;
  background: #ffffff !important;
  border: 1px solid var(--neutral-300, #e2e8f0) !important;
  border-radius: 10px !important;
  padding: 1rem 1.25rem !important;
  box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06) !important;
  display: flex !important;
  flex-direction: row !important;
  align-items: center !important;
  gap: 0.875rem !important;
  position: relative !important;
  overflow: hidden !important;
  height: 100% !important;
  min-height: 94px !important;
  transition: transform 0.2s ease, box-shadow 0.2s ease !important;
}

.sa-metric-card-col .tile_stats_inner:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08) !important;
}

/* Pastikan garis vertikal kiri lama tidak muncul */
.sa-metric-card-col .tile_stats_inner::before {
  display: none !important;
}

/* Tipografi Mode Terang */
html:not([data-theme="dark"]) .sa-metric-card-col .count,
html:not([data-theme="dark"]) .sa-metric-card-col .stat-value {
  color: #0f172a !important;
  font-weight: 700 !important;
}

html:not([data-theme="dark"]) .sa-metric-card-col .count_top,
html:not([data-theme="dark"]) .sa-metric-card-col .count_title,
html:not([data-theme="dark"]) .sa-metric-card-col .stat-label {
  color: #64748b !important;
  font-weight: 600 !important;
}

html:not([data-theme="dark"]) .sa-metric-card-col .count_bottom,
html:not([data-theme="dark"]) .sa-metric-card-col .stat-subtext {
  color: #64748b !important;
}
```

#### B. Bagian Mode Gelap (Dark Mode Parity):
Cari blok `[data-theme="dark"] .sa-metric-card-col .tile_stats_inner` (sekitar baris 1267–1308):
Ganti dari latar abu-abu `#161d2d` menjadi Navy `#003566` dan border `#3F51B5`:
```css
/* =========================================================================
   2. KARTU METRIK MODE GELAP (Navy #003566 Seragam dengan Card Lain di Dashboard)
   ========================================================================= */
[data-theme="dark"] .sa-metric-card-col .tile_stats_inner {
  background-color: #003566 !important;
  background: #003566 !important;
  border: 1px solid #3f51b5 !important;
  border-radius: 10px !important;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5) !important;
}

[data-theme="dark"] .sa-metric-card-col .tile_stats_inner:hover {
  border-color: #5c6bc0 !important;
  box-shadow: 0 10px 28px rgba(0, 0, 0, 0.6) !important;
}

/* Tipografi Mode Gelap */
[data-theme="dark"] .sa-metric-card-col .count,
[data-theme="dark"] .sa-metric-card-col .stat-value {
  color: #ffffff !important;
  font-weight: 700 !important;
}

[data-theme="dark"] .sa-metric-card-col .count_top,
[data-theme="dark"] .sa-metric-card-col .count_title,
[data-theme="dark"] .sa-metric-card-col .stat-label {
  color: #cbd5e1 !important;
  font-weight: 600 !important;
}

[data-theme="dark"] .sa-metric-card-col .count_bottom,
[data-theme="dark"] .sa-metric-card-col .stat-subtext {
  color: #cbd5e1 !important;
}

/* Icon Badges di Mode Gelap */
[data-theme="dark"] .stat-icon.teal {
  background: rgba(26, 187, 156, 0.22) !important;
  color: #20c997 !important;
}

[data-theme="dark"] .stat-icon.yellow {
  background: rgba(245, 159, 0, 0.22) !important;
  color: #fbbf24 !important;
}

[data-theme="dark"] .stat-icon.blue {
  background: rgba(6, 111, 209, 0.22) !important;
  color: #38bdf8 !important;
}

[data-theme="dark"] .stat-icon.red {
  background: rgba(214, 57, 57, 0.22) !important;
  color: #f87171 !important;
}
```

---

### Langkah 2: Audit & Penyesuaian di `resources/views/layouts/dashboard.blade.php`

Buka file [resources/views/layouts/dashboard.blade.php](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php):

Periksa baris 2336–2344:
```css
/* SEBELUM: */
html:not([data-theme="dark"]) .card-highlight,
html:not([data-theme="dark"]) .stat-card-accent,
html:not([data-theme="dark"]) .cv-stat-card,
html:not([data-theme="dark"]) .tile_stats_inner {
    background-color: #01347C !important;
    border: 1px solid #3F51B5 !important;
    ...
}
```
**Tindakan**: Hapus `.tile_stats_inner` dari blok tersebut, ATAU kecualikan jika berada di dalam `.sa-metric-card-col` / `.tile_count`:
```css
/* SESUDAH (Opsi Bersih): */
html:not([data-theme="dark"]) .card-highlight,
html:not([data-theme="dark"]) .stat-card-accent,
html:not([data-theme="dark"]) .cv-stat-card {
    background-color: #01347C !important;
    border: 1px solid #3F51B5 !important;
    box-shadow: 0 10px 20px -3px rgba(1, 52, 124, 0.22), 0 4px 6px -4px rgba(1, 52, 124, 0.15) !important;
    color: #FFFFFF !important;
}
```
*Catatan Penting*: Dengan menghapus `.tile_stats_inner` dari pemaksaan `#01347C !important`, komponen `.tile_stats_inner` secara alami mengikuti style kartu putih Gentelella di mode terang, dan mengikuti `#003566` di mode gelap!

---

### Langkah 3: Verifikasi Komponen Blade `_metrics.blade.php`

Buka file [resources/views/superadmin/dashboard/partials/_metrics.blade.php](file:///g:/MAGKOM/resources/views/superadmin/dashboard/partials/_metrics.blade.php):

Pastikan kode HTML dan teks bawaan **TIDAK DIUBAH**:
- Tetap memuat atribut `title="Total pengguna"`
- Tetap memuat atribut `title="Menunggu verifikasi"`
- Tetap memuat atribut `title="Peserta aktif"`
- Tetap memuat atribut `title="Akun nonaktif"`
- Tetap memuat slot teks bantuan (seperti `Seluruh akun terdaftar`, `3 lewat SLA · FIFO`, dsb.)

---

## 6. Batasan Kritis & Anti-Regresi (Guardrails)

```
[PERINGATAN KERAS KUALITAS KODE - SENIOR DEV GUARDRAILS]
1. DILARANG MENGUBAH STRING TEKS UJI COBA:
   Teks 'Total pengguna', 'Menunggu verifikasi', 'Peserta aktif', 'Akun nonaktif'
   diperiksa secara otomatis oleh PHPUnit / Pest. Jangan ubah huruf besar/kecilnya.

2. DILARANG MENGUBAH CONTROLLER ATAU QUERY DATABASE:
   Task ini murni pekerjaan UI/Styling CSS & Blade Layout. Jangan menyentuh
   SuperAdminDashboardController atau Model apapun.

3. DILARANG MENGGUNAKAN INLINE STYLE DI BLADE:
   Jangan menambahkan style="background: ..." langsung di tag HTML komponen Blade.
   Gunakan file stylesheet terpusat di public/css/superadmin-dashboard.css.

4. PASTIKAN ZERO JITTER PADA HOVER:
   Gunakan transisi halus translateY(-2px) dan box-shadow tanpa mengubah ukuran box (box-sizing).
```

---

## 7. Rencana Pengujian & Prosedur Validasi (Verification Plan)

Junior Developer atau AI Agent pelaksana wajib memvalidasi hal-hal berikut sebelum mengajukan *pull request* / *code review*:

### A. Pengujian Otomatis (Automated Tests)
Jalankan perintah pengujian berikut di terminal:
```bash
php artisan test tests/Feature/SuperAdminFigmaDesignAlignmentTest.php
php artisan test tests/Feature/DeltaDashboardEnhancementTest.php
```
**Kriteria Lolos**: Seluruh assertion (55 assertions pada Figma alignment dan 48 assertions pada Delta) harus **100% PASSED**.

### B. Validasi Visual Manual (Manual Visual Inspection)
1. **Mode Terang (Light Mode)**:
   - Masuk sebagai SuperAdmin (`superadmin@magkom.test` / password default).
   - Pastikan toggle tema di pojok kanan atas berada pada **Mode Terang** (ikon matahari).
   - Periksa 4 kartu metrik: Latar belakang harus **PUTIH BERSIH (`#FFFFFF`)**, sama dengan kartu *Pengguna Terbaru* dan *Audit Log Terbaru*.
   - Angka metrik (32, 3, 10, 2) harus berwarna hitam/slate gelap (`#0F172A`) tebal dan terbaca jelas.
   - Ikon badge (User, Clipboard, User-check, Lock) berwarna toska, kuning, biru, dan merah dengan background lembut.
2. **Mode Gelap (Dark Mode)**:
   - Klik toggle tema ke **Mode Gelap** (ikon bulan).
   - Periksa 4 kartu metrik: Latar belakang harus **NAVY (`#003566`)** dengan garis tepi **INDIGO (`#3F51B5`)**, **PERSIS SAMA** dengan kartu *Perlu tindakan hari ini* di sebelah kanan dan kartu *Pengguna terbaru* di bawahnya.
   - Angka metrik harus berwarna **PUTIH (`#FFFFFF`)**.
   - Teks judul (*Total Pengguna*, dll.) harus berwarna abu-abu terang (`#CBD5E1`), **TIDAK BOLEH GELAP / BURAM**.
3. **Penyusutan Layar (Responsive Breakpoints)**:
   - Uji pada resolusi Mobile (375px), Tablet (768px), dan Desktop (1440px): 4 kartu harus tersusun rapi tanpa ada teks yang terpotong (*no overflow*).

---

## 8. Ringkasan Tugas Siap Eksekusi (Ready-to-Execute Checklist)

- [x] Hapus pemaksaan `.tile_stats_inner` dari blok `#01347C !important` pada `resources/views/layouts/dashboard.blade.php`.
- [x] Atur `.sa-metric-card-col .tile_stats_inner` di `public/css/superadmin-dashboard.css` agar default-nya berlatar putih `#FFFFFF` dan border `#E2E8F0` dengan teks gelap.
- [x] Atur `[data-theme="dark"] .sa-metric-card-col .tile_stats_inner` di `public/css/superadmin-dashboard.css` agar berlatar navy `#003566` dan border `#3F51B5` dengan teks putih.
- [x] Jalankan test suite `php artisan test tests/Feature/SuperAdminFigmaDesignAlignmentTest.php`.
- [x] Lakukan verifikasi visual di browser (Mode Terang vs Mode Gelap).
