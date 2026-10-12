# Plan: Delta Enhancement Dashboard Admin, Pembimbing & SuperAdmin

- **Tanggal**: 2026-10-12
- **Status**: Selesai (Terverifikasi 256/256 Test PASS)
- **Author**: Antigravity (web-planning)
- **Acuan**: PRD (`01-PRD-MagKom.md`), TDD (`02-TDD-MagKom.md`), Roadmap (`03-ROADMAP`), Skema DB (`04-SKEMA`), RBAC (`05-KEAMANAN`), Addendum QA (`06-ADDENDUM`), Design System (`07-DESIGN-SYSTEM`)
- **Figma**: [MAGKOM-Kominfo-Sleman](https://www.figma.com/design/uKH1OZjwBIi8iRoKQXEbQr/MAGKOM-Kominfo-Sleman)

---

## 1. Tujuan

Menambahkan fitur-fitur dan halaman yang ada di desain Figma dan PRD tetapi **belum diimplementasikan** dalam codebase saat ini. Cakupan:
- Dashboard **Administrasi**: halaman detail penempatan individual (show view), sidebar menu sertifikat & survei, stat cards enhancement
- Dashboard **Pembimbing**: chart visualisasi progress peserta, badge notifikasi sidebar, widget aktivitas mentoring mingguan
- Dashboard **SuperAdmin**: system health widget di dashboard, grafik tren pendaftar 12 bulan, enriched arsip detail, sidebar menu sertifikat & survei

Prinsip: **MENAMBAH, BUKAN MENGUBAH** kode yang sudah berfungsi.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi
- A1: Semua 249 test suite yang sudah ada akan tetap PASS setelah penambahan fitur ini.
- A2: Tidak ada perubahan skema database (migrasi) yang diperlukan — semua data sudah tersedia di tabel yang ada.
- A3: Desain Figma diinterpretasikan untuk estetika; PRD untuk perilaku bisnis (sesuai PRD 9.1).
- A4: Setiap halaman baru mengikuti layout `dashboard.blade.php` yang sudah ada.
- A5: SuperAdmin sidebar sudah memiliki menu Sertifikat, Survei (pada route admin), Health Monitor, dan Backup yang bisa diakses.

### Pertanyaan Terbuka
- Q1: Apakah halaman detail penempatan admin memerlukan inline-edit kemampuan pindah pembimbing, atau cukup dari halaman index? *(Asumsi: cukup dari index + modal di show page)*
- Q2: Apakah SuperAdmin sidebar perlu menu "Sertifikat" dan "Survei" terpisah? *(Asumsi: ya, sebagai sub-menu di grup Administrasi)*

---

## 3. Pendekatan Desain & Arsitektur

### Opsi Terpilih: Incremental Blade View + Controller Method Addition
- Menambah halaman/view Blade baru tanpa mengubah controller yang sudah ada (kecuali menambah method baru jika diperlukan).
- Menambah route baru di `web.php` tanpa menghapus route yang sudah ada.
- Menambah nav item di sidebar `dashboard.blade.php` hanya untuk menu yang belum ada.

### Alasan Pemilihan
- Risiko regresi minimal karena tidak memodifikasi file yang sudah berfungsi.
- Konsisten dengan pola arsitektur yang sudah diterapkan (Controller → Service → View).
- Backward compatible — semua fitur lama tetap utuh.

### Alternatif yang Dipertimbangkan
- **Full Livewire refactor**: Ditolak karena codebase saat ini Blade-only, scope creep.
- **API-first + SPA frontend**: Ditolak karena bertentangan dengan keputusan D-01 (server-rendered Blade).

---

## 4. Inventaris Delta: Figma vs Code

### 4.1 Dashboard Administrasi (Admin)

| # | Fitur di Figma / PRD | Status Code | Delta |
|:--|:--|:--|:--|
| A1 | Dashboard statistik overview (stat cards) | ✅ Ada | — |
| A2 | Verifikasi Berkas — list & detail | ✅ Ada | — |
| A3 | Kuota Magang — CRUD inline | ✅ Ada | — |
| A4 | Waiting List — promosi FIFO, tutup antrean | ✅ Ada | — |
| A5 | Penempatan — list, manual input, pindah pembimbing | ✅ Ada (index) | **Belum ada: halaman show detail** |
| A6 | Laporan & Ekspor — sub-reports | ✅ Ada | — |
| A7 | Sertifikat — list + cetak | ✅ Ada | — |
| A8 | Survei — list + export | ✅ Ada | — |
| A9 | **Detail Penempatan** — show view individual | ❌ Belum ada | **BARU** |
| A10 | **Sidebar: Sertifikat & Survei terpisah** | ❌ Belum di sidebar Admin | **BARU** |
| A11 | **Quick Stats Widget** — avg proses, realisasi kuota % | Parsial | **Enhancement** |

### 4.2 Dashboard Pembimbing

| # | Fitur di Figma / PRD | Status Code | Delta |
|:--|:--|:--|:--|
| B1 | Dashboard overview | ✅ Ada | — |
| B2 | Peserta Bimbingan — list & detail | ✅ Ada | — |
| B3 | Tugas Peserta — CRUD, template picker | ✅ Ada | — |
| B4 | Bank Template Tugas | ✅ Ada | — |
| B5 | Reviu Logbook — list, review, bulk review | ✅ Ada | — |
| B6 | Agenda Mentoring — CRUD | ✅ Ada | — |
| B7 | Penilaian Magang | ✅ Ada | — |
| B8 | Laporan Kinerja ASN | ✅ Ada | — |
| B9 | **Chart Visualisasi Progress Peserta** | ❌ Belum ada | **BARU** |
| B10 | **Badge Notifikasi Sidebar** | ❌ Belum ada | **BARU** |
| B11 | **Widget Aktivitas Mentoring Mingguan** | ❌ Belum ada | **BARU** |

### 4.3 Dashboard SuperAdmin

| # | Fitur di Figma / PRD | Status Code | Delta |
|:--|:--|:--|:--|
| C1 | Dashboard overview | ✅ Ada | — |
| C2–C8 | Modul Operasional, Staf, Role, Rubrik, Audit, Config, Health, Backup | ✅ Ada | — |
| C9 | **Enriched Arsip Detail** — timeline + tabs | Parsial | **Enhancement** |
| C10 | Audit Log Ekspor | ✅ Ada | — |
| C11 | **System Health Summary Cards di Dashboard** | ❌ Belum ada | **BARU** |
| C12 | **Sidebar Menu Sertifikat & Survei** | ❌ Belum di sidebar SA | **BARU** |
| C13 | **Grafik Tren Pendaftar Bulanan** | ❌ Belum ada | **BARU** |

---

## 5. Langkah Implementasi

### Fase 1: Admin — Halaman Detail Penempatan Individual (A9)

- [x] **1.1**: Tambah method `show(Penempatan $penempatan)` di `app/Http/Controllers/Admin/PenempatanController.php` — return view `admin.penempatan.show` dengan eager load: `peserta.profilPeserta.jurusan`, `pembimbing`, `tugas`, `logbooks`, `penilaian.details.rubrik`, `pendaftaran`
- [x] **1.2**: Buat `resources/views/admin/penempatan/show.blade.php` — Card profil peserta, Card info penempatan (bulan, durasi, jalur, status badge), Card pembimbing, Timeline status, Summary (tugas selesai/total, logbook count, nilai akhir), Tombol aksi: Surat Diterima, Pindah Pembimbing, Batalkan
- [x] **1.3**: Tambah route `Route::get('/penempatan/{penempatan}', ...)->name('penempatan.show')` di blok admin `web.php`
- [x] **1.4**: Di `admin/penempatan/index.blade.php`, tambah link "Detail" per baris tabel menuju `admin.penempatan.show`

### Fase 2: Admin & SuperAdmin — Sidebar Menu Sertifikat & Survei (A10, C12)

- [x] **2.1**: Di `dashboard.blade.php` navigasi Admin, tambah 2 nav-item: "Sertifikat" → `admin.sertifikat.index`, "Survei" → `admin.survei.index`
- [x] **2.2**: Di sidebar SuperAdmin grup "Administrasi", tambah sub-menu: "Sertifikat" → `admin.sertifikat.index`, "Survei" → `admin.survei.index`

### Fase 3: Admin — Dashboard Quick Stats Enhancement (A11)

- [x] **3.1**: Di `Admin/DashboardController.php` method `index()`, tambah: `$avgVerifikasiHari`, `$realisasiKuotaPersen`, `$pendaftaranMingguIni`
- [x] **3.2**: Di `admin/dashboard.blade.php`, tambah 3 stat cards baru setelah cards yang ada: Rata-rata Proses, Realisasi Kuota, Pendaftar Minggu Ini

### Fase 4: Pembimbing — Chart Visualisasi Detail Peserta (B9)

- [x] **4.1**: Di `Pembimbing/PesertaController.php` method `show()`, tambah: `$tugasStats` (array count per status), `$logbookPerMinggu` (4 minggu), `$completionRate`
- [x] **4.2**: Di `pembimbing/peserta/show.blade.php`, tambah section: Donut chart CSS tugas, Bar chart CSS logbook mingguan, Completion rate badge

### Fase 5: Pembimbing — Badge Notifikasi & Widget Timeline (B10, B11)

- [x] **5.1**: Di `Pembimbing/DashboardController.php`, tambah: `$progresBaruCount`, `$logbookPerluReviewCount`, `$aktivitasMingguIni`
- [x] **5.2**: Di `dashboard.blade.php` navigasi Pembimbing, tambah badge `.nav-badge-count` pada "Tugas Peserta" dan "Reviu Logbook" via View Composer atau share data
- [x] **5.3**: Di `pembimbing/dashboard.blade.php`, tambah section "Aktivitas Mentoring Minggu Ini" — timeline cards vertikal

### Fase 6: SuperAdmin — Dashboard System Health & Tren (C11, C13)

- [x] **6.1**: Di `SuperAdmin/DashboardController.php`, tambah: `$systemSummary` (disk, DB, backup, sessions), `$trenPendaftar` (12 bulan)
- [x] **6.2**: Di `superadmin/dashboard.blade.php`, tambah: Section "Kesehatan Sistem" (4 mini cards), Section "Tren Pendaftar" (bar chart CSS)

### Fase 7: SuperAdmin — Enriched Arsip Detail (C9)

- [x] **7.1**: Di `SuperAdmin/ArsipPesertaController.php` method `show()`, enriched eager loading: tugas+progres, logbooks, penilaian+detail+riwayat, pendaftaran+dokumen
- [x] **7.2**: Di `superadmin/arsip/show.blade.php`, buat tabbed view: Tab Profil, Tab Timeline, Tab Tugas & Progres, Tab Logbook, Tab Penilaian — semua read-only + tombol "Unduh Bundle ZIP"

---

## 6. Strategi Testing

### Unit & Integration Test
- `AdminPenempatanShowTest`: akses show oleh admin ✅, superadmin ✅, pembimbing 403, peserta 403
- `PembimbingBadgeCountTest`: verifikasi hitung badge count berdasarkan data progres/logbook
- `SuperAdminDashboardEnhancementTest`: verifikasi `systemSummary` dan `trenPendaftar` format
- `ArsipEnrichedShowTest`: verifikasi semua tab data dimuat (read-only)

### UI & Manual Verification
- Visual check pada 1920x1080 dan 768px untuk setiap halaman baru
- Sidebar badge count update setelah ada progres baru
- Chart render dan responsif
- Navigasi sidebar tidak rusak di semua role

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|:--|:--|:--|
| Regresi test lama | Fitur lama rusak | Full test suite wajib PASS sebelum merge setiap fase |
| N+1 query pada halaman enriched | Performa lambat | Mandatory `with()` eager loading; Laravel Debugbar check |
| Sidebar overflow pada mobile | UX buruk | Scrollable sidebar (sudah ada), test responsif |
| Badge count menambah query per request | Beban server | View Composer hanya aktif untuk role Pembimbing, cache 60 detik |

### Aksesibilitas
- Chart: `aria-label` + fallback teks
- Badge: `aria-live="polite"`
- Nav items baru: focus state, min 44px touch target

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Semua langkah implementasi tercentang `- [x]`
- [x] Full test suite (249+ existing) PASS tanpa regresi (Total 256/256 PASS)
- [x] Test baru untuk fitur delta PASS (`DeltaDashboardEnhancementTest` 7/7 PASS)
- [x] Lint dan analisis statis PASS
- [x] Sidebar tidak rusak di semua role (Admin, Pembimbing, SuperAdmin)
- [x] Halaman baru responsif 768px+ dan sesuai token CSS Design System
- [x] Tidak ada N+1 query (eager loading optimal & view composer cached)
- [x] Audit log mencatat akses halaman baru (Penempatan Show & Arsip Show)

---

## 9. Catatan Perubahan & Saran Lanjutan

### Perubahan selama pengerjaan
1. **Model & Relasi**:
   - Model `Tugas` menggunakan relasi `progresLogs` (model `ProgresLog`).
   - Enum `StatusTugasEnum` menggunakan status `Proses` dan `Belum` untuk klasifikasi runtime.
   - Kolom `jalur` (`JalurEnum`) dan `sumber` (`SumberPenempatanEnum`) pada model `Penempatan` dipastikan terisi sesuai skema database NOT NULL.
2. **Kinerja & Integritas**:
   - Badge counter sidebar Pembimbing di-cache selama 60 detik via View Composer di `AppServiceProvider` guna mencegah overhead query berulang.
   - Backward compatibility halaman `superadmin.arsip.show` dijaga penuh agar kompatibel dengan suite tes lama (`AdminLaporanDanArsipTest`).

### Saran Lanjutan (Out of Scope)
1. **Real-time notification via WebSocket** — Upgrade badge ke real-time via Laravel Echo
2. **Interactive Chart Library (Chart.js)** — Upgrade CSS-only chart ke library interaktif
3. **Bulk export semua data peserta** — Ekspor massal Excel peserta + tugas + logbook + nilai
4. **Dark mode dashboard** — Design System belum mendefinisikan dark mode tokens
5. **Email digest mingguan Pembimbing** — Di luar scope (PRD: tanpa notifikasi selain verifikasi/reset)

---

## Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
|:---|:---|:---|
| `app/Http/Controllers/Admin/PenempatanController.php` | Modify | Tambah method `show()` |
| `resources/views/admin/penempatan/show.blade.php` | Create | View detail penempatan |
| `routes/web.php` | Modify | Tambah route penempatan show |
| `resources/views/admin/penempatan/index.blade.php` | Modify | Tambah link "Detail" per baris |
| `resources/views/layouts/dashboard.blade.php` | Modify | Tambah nav: Sertifikat, Survei (admin/SA), badge (pembimbing) |
| `app/Http/Controllers/Admin/DashboardController.php` | Modify | Tambah 3 variabel statistik |
| `resources/views/admin/dashboard.blade.php` | Modify | Tambah 3 stat cards |
| `app/Http/Controllers/Pembimbing/PesertaController.php` | Modify | Tambah data chart |
| `resources/views/pembimbing/peserta/show.blade.php` | Modify | Tambah chart section |
| `app/Http/Controllers/Pembimbing/DashboardController.php` | Modify | Tambah badge + aktivitas |
| `resources/views/pembimbing/dashboard.blade.php` | Modify | Tambah timeline widget |
| `app/Http/Controllers/SuperAdmin/DashboardController.php` | Modify | Tambah system health + tren |
| `resources/views/superadmin/dashboard.blade.php` | Modify | Tambah health cards + chart |
| `app/Http/Controllers/SuperAdmin/ArsipPesertaController.php` | Modify | Enriched eager loading |
| `resources/views/superadmin/arsip/show.blade.php` | Modify | Enriched tabbed view |
| `tests/Feature/DeltaDashboardEnhancementTest.php` | Create | Test suite fitur delta |
