# TASK / PRD: Penyelarasan Sistem Animasi (Scroll-Craft + GSAP) ke Seluruh Fitur SuperAdmin MagKom

> **Dari:** Senior Developer  
> **Untuk:** Junior Developer / AI Agent pelaksana (Gemini 3.8 Flash, Antigravity, Claude, dsb.)  
> **Prioritas:** HIGH  
> **Karakter Tugas:** Global Motion System Parity & Delightful User Experience  
> **Status Dokumen:** Siap Direview & Disetujui  

---

## 1. Ringkasan Eksekutif (TL;DR 60 Detik)

| Pertanyaan | Jawaban |
|---|---|
| **Apa yang dikerjakan?** | Menerapkan **Sistem Animasi yang sama persis dengan modul Pembimbing** (Scroll-Craft GPU Reveal + GSAP Staggered Entrance + Rolling Numeric Counter + Sparkline Rise + Card Hover Elevation) ke **seluruh fitur dan halaman SuperAdmin lainnya** (Manajemen Staf, Hak Akses & Role, Bulan Magang, Slot Khusus, Master Data Jurusan/Bidang, Arsip, Audit Log, Telemetri Health, Backup, Pengaturan, Profil/Akun). |
| **Kenapa dilakukan?** | Dashboard SuperAdmin dan Pembimbing sudah memiliki animasi halus, profesional, dan menyenangkan saat pertama kali dibuka (*first impression*). Namun, halaman-halaman fitur SuperAdmin lainnya saat ini masih statis (*kaku*) tanpa transisi viewport reveal maupun rolling counter pada kartu statistiknya. |
| **Prinsip Utama** | **DRY (Don't Repeat Yourself) & Non-Intrusive**: Buat mesin animasi terpusat dan reusable di `resources/css/pages/superadmin/superadmin-common.css` dan `resources/js/services/superadmin-motion.js`, lalu panggil secara konsisten di setiap modul JS halaman. |
| **Yang BOLEH diubah** | 1. `resources/css/pages/superadmin/superadmin-common.css`<br>2. `resources/js/services/superadmin-motion.js`<br>3. Modul JS masing-masing fitur SuperAdmin (`staf-index.js`, `role-index.js`, `bulan-index.js`, `slot-khusus.js`, `jurusan-index.js`, `bidang-index.js`, `health-monitor.js`, `akun-index.js`, `profil-show.js`, `arsip-show.js`, dsb.)<br>4. Penambahan atribut markup animasi (`data-scroll-reveal`, `.sc-reveal-*`, `.sc-delay-*`) pada file Blade SuperAdmin terkait. |
| **Yang DILARANG diubah** | Backend logic, Controller, query database, route, migrasi DB, autentikasi, otorisasi RBAC, atau tampilan role lain (`pembimbing`, `admin`, `peserta`). |
| **Definisi "Selesai"** | 1. Setiap halaman SuperAdmin saat dibuka menampilkan animasi masuk berjenjang: Hero banner turun halus, Tile Stat naik berurutan dengan counter angka berputar (*rolling*) dan sparkline naik dari dasar, tabel dan kartu konten memudar masuk (*fade-slide up*).<br>2. Transisi hover pada kartu dan baris data responsif dan luwes.<br>3. Mematuhi aksesibilitas WCAG 2.1 AA (*prefers-reduced-motion*) dan session guard (`sessionStorage`) agar aksi pencarian/filter di halaman yang sama tidak memicu kedipan (*flicker*).<br>4. Seluruh test suite PHPUnit (PHP 8.3) tetap **PASS (100% Green)** dan `npm run build` sukses tanpa error. |

---

## 2. Acuan Baku: Sistem Animasi Pembimbing (The Standard of Truth)

Pelaksana **WAJIB** memahami 3 pilar arsitektur animasi yang ada di modul Pembimbing:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                       SISTEM ANIMASI MODUL PEMBIMBING                       │
├─────────────────────────────────────────────────────────────────────────────┤
│ 1. Scroll-Craft Engine (GPU-Accelerated IntersectionObserver)               │
│    - Atribut elemen: data-scroll-reveal                                     │
│    - Timing function: cubic-bezier(0.16, 1, 0.3, 1) [Apple-spring curve]   │
│    - Varian animasi:                                                        │
│      * .sc-reveal-hero   : Hero banner slide-down halus (-16px -> 0px)     │
│      * .sc-reveal-up     : Tile stats, tabel, panel slide-up (+24px -> 0px) │
│      * .sc-reveal-scale  : Kartu diagram, gauge, widget (skala 0.96 -> 1.0) │
│      * .sc-reveal-side   : Sidebar filter, panel samping (+24px -> 0px)     │
│    - Stagger Delays: .sc-delay-1 (0.08s), .sc-delay-2 (0.16s), .sc-delay-3 │
│    - Lifecyle Lock: target.classList.add('sc-settled') mencegah reflow      │
├─────────────────────────────────────────────────────────────────────────────┤
│ 2. GSAP Motion Engine (Micro-Interactions & Fluid Metrics)                  │
│    - animateTileStats():                                                    │
│      * Tile cards fade & slide up (duration 0.45s, stagger 0.08s)          │
│      * Mini Sparkline bars rise (scaleY 0 -> 1, transformOrigin: bottom)    │
│      * Rolling Numeric Counter (angka 0 -> nilai aktual, durasi 0.65s)      │
│    - Table/Card List Staggered Entrance (opacity 0 -> 1, y: 8 -> 0)         │
│    - Interactive Card Hover: translateY(-2px) & shadow-elevation            │
├─────────────────────────────────────────────────────────────────────────────┤
│ 3. Guardrail Aksesibilitas & Lifecycle Safety                               │
│    - prefers-reduced-motion: Nonaktifkan seluruh animasi jika user meminta  │
│    - sessionStorage Guard: Simpan path terakhir; jika user melakukan aksi   │
│      di halaman yang sama (filter, sorting, search), jangan ulang animasi!  │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Daftar Inventaris Fitur SuperAdmin yang Diberi Animasi

Terdapat **15 fitur/halaman** dalam portal SuperAdmin yang akan diselaraskan:

| No | Fitur / Halaman | URL Route | File Blade Utama | Modul JavaScript | Elemen Kunci yang Dianimasikan |
|:--:|---|---|---|---|---|
| **1** | **Manajemen Staf Internal** | `/superadmin/staf` | `superadmin/staf/index.blade.php` | `staf-index.js` | Hero banner, 4 Tile stats staf, sparklines, tabel daftar akun |
| **2** | **Matriks Hak Akses & Role** | `/superadmin/role` | `superadmin/role/index.blade.php` | `role-index.js` | Hero banner, kartu ringkasan hak akses, tabel matriks RBAC |
| **3** | **Manajemen Bulan Magang** | `/superadmin/bulan` | `superadmin/bulan/index.blade.php` | `bulan-index.js` | Hero banner, 4 Tile stats kuota periode, sparklines, tabel bulan |
| **4** | **Slot Khusus Kemitraan** | `/superadmin/slot-khusus` | `superadmin/slot-khusus/index.blade.php` | `slot-khusus.js` | Hero banner, 4 Tile stats mitra/slot, tabel kuota kemitraan |
| **5** | **Peserta Jalur Khusus** | `/superadmin/peserta-khusus/create` | `superadmin/peserta-khusus/create.blade.php` | Shared / inline | Hero banner, kartu form pendaftaran, badge instruksi |
| **6** | **Master Data Jurusan** | `/superadmin/jurusan` | `superadmin/jurusan/index.blade.php` | `jurusan-index.js` | Hero banner, 4 Tile stats jurusan, sparklines, tabel data |
| **7** | **Master Data Bidang** | `/superadmin/bidang` | `superadmin/bidang/index.blade.php` | `bidang-index.js` | Hero banner, 4 Tile stats bidang, sparklines, tabel data |
| **8** | **Pengaturan Sistem** | `/superadmin/pengaturan` | `superadmin/pengaturan/index.blade.php` | Shared form script | Hero banner, kartu batas waktu, konfigurasi kuota, panel purge |
| **9** | **Arsip Peserta (Index)** | `/superadmin/arsip-peserta` | `superadmin/arsip/index.blade.php` | Shared / inline | Hero banner, filter periode tahun, tabel arsip permanen |
| **10** | **Arsip Peserta (Detail 360)**| `/superadmin/arsip-peserta/{id}` | `superadmin/arsip/show.blade.php` | `arsip-show.js` | Hero profil mentee, kartu riwayat nilai, logbook, dokumen |
| **11** | **Audit Log Trail** | `/superadmin/audit-log` | `superadmin/audit-log/index.blade.php` | `_scripts.blade.php` | Header bar, 4 KPI strip, channels sidebar (slide-side), stream table |
| **12** | **Telemetri Kesehatan Sistem**| `/superadmin/health-monitor` | `superadmin/health/index.blade.php` | `health-monitor.js` | Telemetry cockpit, gauge meter, status tiles, canvas charts |
| **13** | **Cadangan Basis Data** | `/superadmin/backup` | `superadmin/backup/index.blade.php` | Shared / inline | Hero banner, 4 Tile stats snapshot, tabel file arsip DB |
| **14** | **Profil SuperAdmin** | `/superadmin/profil` | `superadmin/profil/show.blade.php` | `profil-show.js` | Hero banner profil, kartu identitas akun, form update data |
| **15** | **Akun & Preferensi** | `/superadmin/akun` | `superadmin/akun/index.blade.php` | `akun-index.js` | Hero banner keamanan, form ganti password, switcher preferensi |

> *Catatan:* Halaman **Dashboard SuperAdmin** (`/superadmin/dashboard`) sudah selesai dianimasikan dengan sempurna pada task sebelumnya.

---

## 4. Desain Arsitektur Teknis: Centralized Motion Engine

Untuk mematuhi aturan repositori (tidak menulis JS/CSS di Blade dan menghindari redundansi kode), kita menerapkan arsitektur terpusat:

### 4.1 Lapisan CSS: `resources/css/pages/superadmin/superadmin-common.css`
Tambahkan utility styling animasi universal yang langsung terwariskan ke semua view SuperAdmin:

```css
/* ==========================================================================
   SUPERADMIN UNIVERSAL SCROLL-CRAFT & MOTION UTILITIES
   ========================================================================== */

/* 1. Base Reveal State */
[data-scroll-reveal] {
    opacity: 0;
    will-change: transform, opacity;
    transition-property: transform, opacity;
    transition-timing-function: cubic-bezier(0.16, 1, 0.3, 1);
}

/* 2. Varian Reveal */
.sc-reveal-hero {
    transform: translateY(-16px);
    transition-duration: 0.65s;
}

.sc-reveal-up {
    transform: translateY(24px);
    transition-duration: 0.55s;
}

.sc-reveal-scale {
    transform: scale(0.96) translateY(16px);
    transition-duration: 0.60s;
}

.sc-reveal-side {
    transform: translateX(24px);
    transition-duration: 0.60s;
}

/* 3. Stagger Delays */
.sc-delay-1 { transition-delay: 0.08s; }
.sc-delay-2 { transition-delay: 0.16s; }
.sc-delay-3 { transition-delay: 0.24s; }
.sc-delay-4 { transition-delay: 0.32s; }
.sc-delay-5 { transition-delay: 0.40s; }

/* 4. Active Revealed State */
[data-scroll-reveal].is-revealed {
    opacity: 1;
    transform: none;
}

/* 5. Freeze Transitions After Reveal (Safety Guard) */
[data-scroll-reveal].sc-settled {
    transition: none !important;
    will-change: auto !important;
}

/* 6. Micro-Interaction Hover Universal */
.sa-card-panel,
.card-box,
.tile_stats_count,
.sa-table-card,
.sa-filter-panel {
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
}

.sa-card-panel:hover,
.card-box:hover,
.tile_stats_count:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(1, 52, 124, 0.08), 0 12px 24px -8px rgba(1, 52, 124, 0.14);
}
```

### 4.2 Lapisan JavaScript: `resources/js/services/superadmin-motion.js`
Refaktor dan perluas `superadmin-motion.js` agar menjadi engine universal yang menyediakan fungsi:
1. `initSuperAdminPageMotion(options)`: Fungsi utama satu pintu (*one-liner entry point*).
   - Memanggil `initScrollCraft()`.
   - Menjalankan GSAP timeline untuk kartu statistik (Tile Stats):
     - Staggered rise pada `.tile_stats_count` / `.sa-stat-card`.
     - Staggered rise pada mini sparkline bars (`.sa-spark i, .stat-spark i`).
     - Rolling numeric counter pada `.stat-value, .count, .audit-kpi-val`.
   - Menjalankan staggered fade-in halus pada baris tabel (`tbody tr`).
   - Memasang hover micro-interactions pada kartu dan elemen interaktif.
   - Menyediakan safeguard `shouldAnimatePage()` berbasis `sessionStorage` dan `prefers-reduced-motion`.

---

## 5. Rencana Kerja Bertahap (TASK 1 s/d TASK 8)

Eksekusi **WAJIB** dilakukan secara bertahap dan teratur:

### TASK 1: Konsolidasi CSS Animasi Terpusat
- **Target File:** `resources/css/pages/superadmin/superadmin-common.css`
- **Pekerjaan:**
  1. Tambahkan deklarasi class `[data-scroll-reveal]`, `.sc-reveal-hero`, `.sc-reveal-up`, `.sc-reveal-scale`, `.sc-reveal-side`, `.sc-delay-1` s/d `.sc-delay-5`.
  2. Tambahkan deklarasi `.is-revealed` dan `.sc-settled`.
  3. Tambahkan micro-interaction hover universal untuk `.sa-card-panel`, `.tile_stats_count`, dsb.
  4. Pastikan `@media (prefers-reduced-motion: reduce)` mematikan seluruh transisi.

### TASK 2: Standarisasi Motion Engine Universal di Service JS
- **Target File:** `resources/js/services/superadmin-motion.js`
- **Pekerjaan:**
  1. Integrasikan import `initScrollCraft` dari `scroll-craft.js`.
  2. Implementasikan fungsi `animateSuperAdminTileStats(container)` dengan:
     - Fade & slide up kartu tile (`duration: 0.45, stagger: 0.07`).
     - Sparkline mini-bars rise (`scaleY: 0` -> `1`, `duration: 0.38, stagger: 0.015`).
     - Rolling numeric counter (`val: 0` -> `targetVal`).
  3. Implementasikan fungsi `animateTableRows(tableSelector)` (`opacity: 0` -> `1`, `y: 8` -> `0`, `stagger: 0.03`).
  4. Ekspor fungsi utama: `initSuperAdminPageMotion(pageConfig)`.
  5. Daftarkan di `window.MagKomSuperAdminMotion` untuk kompatibilitas.

### TASK 3: Animasi Modul Manajemen Staf & Matriks Hak Akses
- **Target Files:**
  - `resources/views/superadmin/staf/index.blade.php`
  - `resources/js/pages/superadmin/staf-index.js`
  - `resources/views/superadmin/role/index.blade.php`
  - `resources/js/pages/superadmin/role-index.js`
- **Pekerjaan:**
  1. Di Blade Staf: Pasang `.sc-reveal-hero` pada hero banner, `.sc-reveal-up` pada `.tiles-grid` dan `.sa-card-panel` tabel staf.
  2. Di JS Staf: Import `initSuperAdminPageMotion` dan panggil di `DOMContentLoaded`.
  3. Di Blade Role: Pasang `.sc-reveal-hero` pada hero banner, `.sc-reveal-up` pada kartu matriks role & panel aksi.
  4. Di JS Role: Import `initSuperAdminPageMotion` dan panggil di `DOMContentLoaded`.

### TASK 4: Animasi Modul Siklus Magang & Kuota (Bulan & Slot Khusus)
- **Target Files:**
  - `resources/views/superadmin/bulan/index.blade.php`
  - `resources/js/pages/superadmin/bulan-index.js`
  - `resources/views/superadmin/slot-khusus/index.blade.php`
  - `resources/js/pages/superadmin/slot-khusus.js`
  - `resources/views/superadmin/peserta-khusus/create.blade.php`
- **Pekerjaan:**
  1. Di Blade Bulan: Pasang `data-scroll-reveal` pada hero banner, `.tiles-grid`, dan tabel bulan.
  2. Di JS Bulan: Panggil `initSuperAdminPageMotion()`.
  3. Di Blade Slot Khusus: Pasang `data-scroll-reveal` pada hero banner, tile stats slot, dan tabel mitra.
  4. Di JS Slot Khusus: Panggil `initSuperAdminPageMotion()`.
  5. Di Blade Peserta Khusus: Berikan animasi masuk pada kartu formulir pendaftaran.

### TASK 5: Animasi Modul Master Data (Jurusan, Bidang & Pengaturan)
- **Target Files:**
  - `resources/views/superadmin/jurusan/index.blade.php`
  - `resources/js/pages/superadmin/jurusan-index.js`
  - `resources/views/superadmin/bidang/index.blade.php`
  - `resources/js/pages/superadmin/bidang-index.js`
  - `resources/views/superadmin/pengaturan/index.blade.php`
- **Pekerjaan:**
  1. Di Blade Jurusan: Pasang kelas reveal pada hero banner, tiles grid statistik, dan tabel jurusan.
  2. Di JS Jurusan: Panggil `initSuperAdminPageMotion()`.
  3. Di Blade Bidang: Pasang kelas reveal pada hero banner, tiles grid statistik, dan tabel bidang.
  4. Di JS Bidang: Panggil `initSuperAdminPageMotion()`.
  5. Di Blade Pengaturan: Pasang kelas reveal pada hero banner dan kartu-kartu konfigurasi sistem.

### TASK 6: Animasi Modul Arsip Peserta & Jejak Audit (Audit Log)
- **Target Files:**
  - `resources/views/superadmin/arsip/index.blade.php`
  - `resources/views/superadmin/arsip/show.blade.php`
  - `resources/js/pages/superadmin/arsip-show.js`
  - `resources/views/superadmin/audit-log/index.blade.php`
  - `resources/views/superadmin/audit-log/partials/_channels_sidebar.blade.php`
  - `resources/views/superadmin/audit-log/partials/_kpi_strip.blade.php`
  - `resources/views/superadmin/audit-log/partials/_scripts.blade.php`
- **Pekerjaan:**
  1. Di Blade Arsip: Pasang animasi reveal pada tabel arsip dan kartu detail profil historis peserta.
  2. Di JS Arsip Show: Aktifkan visual entrance pada linimasa peserta.
  3. Di Audit Log: Pasang `.sc-reveal-side` pada `_channels_sidebar`, `.sc-reveal-up` pada `_kpi_strip` dan tabel audit, serta jalankan rolling counter pada 4 kartu KPI total event/mutasi.

### TASK 7: Animasi Modul Infrastruktur, Backup, Profil & Akun
- **Target Files:**
  - `resources/views/superadmin/health/index.blade.php`
  - `resources/js/pages/superadmin/health-monitor.js`
  - `resources/views/superadmin/backup/index.blade.php`
  - `resources/views/superadmin/profil/show.blade.php`
  - `resources/views/superadmin/akun/index.blade.php`
  - `resources/js/pages/superadmin/profil-show.js`
  - `resources/js/pages/superadmin/akun-index.js`
- **Pekerjaan:**
  1. Di Health Monitor: Integrasikan motion service pada telemetry gauges, cards status, dan laser scanner canvas.
  2. Di Backup: Pasang animasi reveal pada hero banner, tile snapshot stats, dan tabel arsip cadangan.
  3. Di Profil & Akun: Pasang animasi reveal pada hero banner, formulir preferensi, dan kartu keamanan akun.

### TASK 8: Kompilasi Aset, Quality Gate & Verifikasi Visual
- **Pekerjaan:**
  1. Jalankan kompilasi Vite: `npm run build` dan pastikan zero warning/error.
  2. Buka browser pada tiap halaman fitur SuperAdmin (viewport 1430px) untuk memastikan:
     - Hero banner turun halus.
     - Angka statistik berputar (*rolling*) ke target nilai dengan presisi.
     - Mini sparkline tumbuh dari dasar.
     - Tabel data muncul bersih tanpa pergeseran layout mendadak (*layout shift*).
     - Tidak ada error console JavaScript.
  3. Jalankan pengujian PHPUnit dengan PHP 8.3 binary:
     ```powershell
     & "C:\Users\galan\php83\php.exe" artisan test tests/Feature/SuperAdminFigmaDesignAlignmentTest.php tests/Feature/DashboardLayoutTest.php tests/Feature/SuperAdminPortalVisualTest.php
     ```
  4. Buat laporan perbandingan dan verifikasi lengkap.

---

## 6. Checklist Verifikasi Anti-Regresi

Sebelum menyatakan pekerjaan selesai, pelaksana wajib mencentang seluruh poin:

- [ ] **Tidak ada inline CSS/JS di Blade:** Seluruh styling baru ada di `superadmin-common.css` dan interaksi ada di `resources/js/`.
- [ ] **Rolling Counter Akurat:** Angka metrik pada kartu tile menampilkan angka riil dari backend (misalnya Total Staf = 7, bukan macet di 0 atau NaN).
- [ ] **Sparkline Bars Rise:** Mini bar chart pada kartu tile naik staggered dari dasar ke tinggi semestinya.
- [ ] **Session Guard Aktif:** Saat user melakukan aksi di halaman yang sama (misal search nama staf atau filter role), animasi entrance tidak berkedip ulang (*no repeated flash*).
- [ ] **Aksesibilitas Terjaga:** Saat mode OS *reduce motion* aktif, elemen langsung tampil statis tanpa animasi.
- [ ] **Vite Build Hijau:** `npm run build` sukses tanpa error modul atau CSS linting.
- [ ] **Test Suite Pass 100%:** Seluruh 24 test feature tetap lulus (268 assertions).

---

## 7. Instruksi Khusus untuk Junior Developer / AI Pelaksana

> 1. **Dilarang Menghapus Logika Eksisting:** Setiap file JS (`staf-index.js`, `role-index.js`, dsb.) sudah memiliki logika modal, AJAX, validasi, atau filter. **JANGAN HAPUS** logika tersebut! Tambahkan pemanggilan motion di dalam `DOMContentLoaded` tanpa merusak event listener yang sudah ada.
> 2. **Gunakan Utility yang Ada:** Jangan menginstal package NPM baru. GSAP sudah tersedia di repositori dan sudah dikonfigurasi di Vite.
> 3. **Perubahan Blade Minimal:** Perubahan Blade HANYA untuk menambahkan atribut kelas `data-scroll-reveal` dan variannya (`sc-reveal-*`, `sc-delay-*`). Jangan merombak struktur layout atau menghapus elemen.
> 4. **Jalankan Verifikasi Nyata:** Selalu jalankan `npm run build` dan PHPUnit test sebelum melaporkan hasil. Buktikan dengan hasil test aktual!

