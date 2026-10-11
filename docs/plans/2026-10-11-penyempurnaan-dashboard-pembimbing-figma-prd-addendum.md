# Plan: Penyempurnaan Komprehensif Dashboard Pembimbing Sesuai Figma, PRD, TDD, dan Addendum Keamanan/QA

- **Tanggal**: 2026-10-11
- **Status**: Completed (Terverifikasi Penuh & Green Test Suite)
- **Author**: Antigravity (web-implementation)
- **Target Area**: Dashboard Pembimbing (`/pembimbing/dashboard`) & Ekosistem Terkait
- **Dokumen Acuan**:
  - [Figma Mockup Portal Pembimbing Node 560-41611](https://www.figma.com/design/uKH1OZjwBIi8iRoKQXEbQr/MAGKOM-Kominfo-Sleman?node-id=560-41611&t=y08kZClFvlEyXLj3-4)
  - [`docs/01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (Fitur F-08, F-09, F-10, F-11, F-12, Aturan Bisnis BR-09, BR-10, BR-11, BR-13, BR-15, BR-16)
  - [`docs/02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) (Bab 4 Struktur, Bab 5 Otorisasi Policy, Bab 6 Skema, Bab 7 Peta Rute, Bab 8 Keamanan)
  - [`docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) (Gelombang 3 & 4)
  - [`docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](file:///g:/MAGKOM/docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md) (Tabel `penempatan`, `tugas`, `progres_log`, `logbook`, `rubrik`, `penilaian`, `penilaian_detail`, `penilaian_riwayat`, `pengaturan`)
  - [`docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`](file:///g:/MAGKOM/docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md) (Jejak audit append-only dan sanitasi data)
  - [`docs/06-ADDENDUM-KEAMANAN-DAN-QA.md`](file:///g:/MAGKOM/docs/06-ADDENDUM-KEAMANAN-DAN-QA.md) (SEC-03, SEC-10, SEC-11, SEC-12, SEC-32, SEC-71, Matriks Hak Akses TCS-01, UAT-6, UAT-7, UAT-8)
  - [`docs/07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Sleman Blue `#0641CC`, font Open Sans, status tokens WCAG AA)

---

## 1. Tujuan

Menyusun rencana kerja terperinci untuk menyempurnakan **Dashboard Operasional Pembimbing (`/pembimbing/dashboard`)** agar memiliki tampilan dan interaksi yang presisi setara dengan standar mockup [Figma Node 560-41611](https://www.figma.com/design/uKH1OZjwBIi8iRoKQXEbQr/MAGKOM-Kominfo-Sleman?node-id=560-41611&t=y08kZClFvlEyXLj3-4), mengadopsi arsitektur *Segmented Multi-Tab Executive Hub* (sebagaimana sukses diterapkan pada Dashboard SuperAdmin), mengintegrasikan pemantauan 360° mentee dan pusat tindakan evaluasi cepat (*Action Hub*), serta menegakkan seluruh kontrol keamanan tingkat tinggi dari [06-ADDENDUM-KEAMANAN-DAN-QA.md](file:///g:/MAGKOM/docs/06-ADDENDUM-KEAMANAN-DAN-QA.md) (SEC-03, SEC-10, SEC-11, SEC-12, SEC-32, dan audit trail NFR-A1).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis & Fungsional:
1. **Batas Beban Pembimbing (BR-13)**: Parameter `maks_peserta_aktif_per_pembimbing` pada tabel `pengaturan` (default 10) berfungsi sebagai batas kuota dinas aktif. Dashboard menyajikan indikator visual kapasitas dengan peringatan warna (Hijau $<70\%$, Oranye $70-89\%$, Merah $\ge 90\%$).
2. **Arsitektur Tampilan Multi-Tab**: Mengingat luasnya tanggung jawab pembimbing (reviu tugas, pemeriksaan logbook harian berbatas waktu, evaluasi rubrik, dan offboarding), antarmuka dashboard dibagi ke dalam 4 Tab Terpadu tanpa reload halaman menggunakan JavaScript tabs murni:
   - **Tab 1: Ringkasan & Tindakan Cepat (Default Figma)**
   - **Tab 2: Pusat Antrean Reviu Mendesak (Action Hub)**
   - **Tab 3: Monitoring Mentee 360° & Progres Magang**
   - **Tab 4: Evaluasi Rubrik & Capaian Akhir (Kelulusan)**
3. **Isolasi Keamanan Dua Lapis (SEC-12)**: SuperAdmin memiliki hak akses baca (read-only) pada seluruh data dashboard pembimbing, namun dilarang keras (HTTP 403 Forbidden) melakukan tindakan reviu progres, reviu logbook, simpan nilai, maupun penetapan selesai magang. Pengamanan dijaga melalui Policy dan Middleware `EnsureSuperAdminReadOnly`.
4. **Keamanan Sesi & Penonaktifan Akun (SEC-03)**: Pembimbing yang berstatus `status_aktif = false` langsung diputus sesinya dan dialihkan ke login dengan pesan error informatif.

### Pertanyaan Terbuka:
- Tidak ada pertanyaan terbuka kritis. Seluruh logika dan acuan bisnis telah terkunci di PRD Bab 8, TDD Bab 5 & 8, serta Addendum Bagian A & B.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Desain Dashboard Pembimbing:

| Kriteria | Opsi A: Monolitik Vertikal Satu Halaman Panjang | Opsi B: Segmented Multi-Tab Executive Hub (Terpilih - Recommended) |
| :--- | :--- | :--- |
| **Pengalaman Pengguna (UX)** | Halaman sangat panjang (scroll $\ge 3500\text{px}$), pembimbing harus terus menggulir untuk mencari antrean reviu atau mentee tertentu. | Bersih, elegan, dan langsung fokus ke tugas utama. Bebas dari scrollbar vertikal berlebihan. |
| **Kepatuhan Desain Figma** | Tampilan utama rentan berantakan dan kehilangan estetika grid Figma Node 560-41611. | **100% Presisi Figma**: Tab pertama menyajikan tata letak default Figma (Hero, 4 Stat Cards, Pending Actions, Mentee Summary). |
| **Efisiensi Tindakan Operasional** | Pembimbing harus berpindah-pindah menu sidebar untuk mengevaluasi tugas atau logbook. | Dilengkapi *Action Hub* pada Tab 2 untuk meninjau detail logbook dan progres langsung dari dashboard. |
| **Beban Kueri Server** | Kueri flat berulang saat berpindah-pindah halaman menu. | Kueri komprehensif berindeks `pembimbing_id` sekali load dengan eager loading hemat N+1. |

> **Keputusan**: Mengadopsi **Opsi B (Segmented Multi-Tab Executive Hub)** karena menggabungkan estetika murni Figma Node 560-41611 dengan efisiensi kerja dinas yang produktif.

---

## 4. Struktur Antarmuka & Modul Dashboard Pembimbing

```text
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ HEADER / TOPBAR (Breadcrumbs, Profil Pembimbing, Status Staf)                           │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ HERO SECTION SLEMAN BLUE (#0641CC)                                                     │
│ ├── Sapaan Pembimbing, Bidang Dinas, Deskripsi Operasional                             │
│ └── Indikator Beban Bimbingan (BR-13): Progress Bar Kapasitas Aktif X / 10 Mentee     │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ TAB CONTROLLER (Segmented Tabs bergradasi Sleman Blue):                                │
│ [ 1. Ringkasan Utama ]  [ 2. Antrean Reviu Cepat ]  [ 3. Mentee 360° ]  [ 4. Evaluasi ] │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ TAB 1: RINGKASAN UTAMA (Figma Default Node 560-41611)                                  │
│ ├── 4 Kartu Metrik KPI:                                                                │
│ │   ├── 1. Mentee Aktif (Total mentee bimbingan aktif)                                 │
│ │   ├── 2. Reviu Logbook (Logbook terkirim menunggu tinjauan - BR-09)                   │
│ │   ├── 3. Laporan Progres Tugas (Menunggu evaluasi pembimbing - AC-08)                │
│ │   └── 4. Tugas Berjalan (Total tugas aktif vs overdue terlambat - BR-16)             │
│ ├── Pelacak Off-boarding Mentee (≤ 14 Hari menuju batas selesai - BR-10, BR-11)        │
│ ├── Pending Action Center (Split 2-Kolom):                                             │
│ │   ├── Kolom Kiri: 5 Progres Tugas Terbaru Menunggu Evaluasi                          │
│ │   └── Kolom Kanan: 5 Logbook Harian Terbaru Menunggu Reviu                           │
│ └── Ringkasan Cepat Mentee Bimbingan Aktif (Tabel Ringkas & Shortcut Profil)           │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ TAB 2: PUSAT ANTREAN REVIU CEPAT (Action Hub)                                          │
│ ├── Daftar Lengkap Logbook Harian Berstatus 'terkirim' (Filter tanggal & peserta)      │
│ ├── Daftar Lengkap Progres Tugas Berstatus 'menunggu' (Pintasan modal reviu)           │
│ └── Pratinjau Berkas Privat Terintegrasi (Signed URL 5 menit - SEC-10)                 │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ TAB 3: MONITORING MENTEE 360° & PROGRES MAGANG                                         │
│ ├── Kartu/Tabel Profil Mentee Komprehensif: Institusi, Jurusan, Pembimbing Dosen       │
│ ├── Rasio Kedisiplinan: % Logbook Terisi, % Tugas Selesai, Status Overdue              │
│ └── Progress Bar Masa Dinas: Tanggal Mulai s/d Selesai                                 │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ TAB 4: EVALUASI RUBRIK & CAPAIAN MAGANG                                                │
│ ├── Status Evaluasi Rubrik: Belum Dinilai, Draft, Final (Skor & Predikat A–E)          │
│ ├── Indikator Masa Tenggang Penonaktifan Akun (BR-11, AC-10-5)                         │
│ ├── Riwayat Perubahan Nilai Final (AC-10-3, NFR-A1)                                    │
│ └── Tombol Unduh Lembar Nilai PDF Resmi On-the-Fly (AC-12)                             │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 5. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `app/Http/Controllers/Pembimbing/DashboardController.php` | Modify | Memperkaya dataset dashboard untuk mendukung 4-tab: agregasi KPI, analitik kedisiplinan mentee, data antrean penuh, status rubrik final, dan pelacak masa tenggang. |
| `resources/views/pembimbing/dashboard.blade.php` | Modify | Redesign total layout Blade presisi Figma Node 560-41611 dengan navigasi Segmented Tabs, 4 stat cards modern, Action Hub, In-App Quick View, dan styling tanpa scrollbar berlebih. |
| `app/Http/Middleware/EnsureAccountActive.php` | Create | Middleware penegak keamanan SEC-03: memutus sesi aktif akun staf/peserta yang dinonaktifkan (`status_aktif = false`). |
| `app/Http/Middleware/EnsureSuperAdminReadOnly.php` | Create | Middleware penegak keamanan SEC-12: SuperAdmin strictly read-only (tolak 403 untuk method non-GET/HEAD pada rute pembimbing). |
| `app/Http/Kernel.php` | Modify | Mendaftarkan alias middleware `account.active` dan `superadmin.readonly`. |
| `routes/web.php` | Modify | Memasang middleware `account.active` dan `superadmin.readonly` pada grup rute `/pembimbing/*`. |
| `tests/Feature/PembimbingDashboardEnhancementTest.php` | Create | Test suite komprehensif menguji rendering 4 tab dashboard pembimbing, kalkulasi KPI, filter antrean reviu, penegakan SEC-03, dan proteksi dua lapis SEC-12. |

---

## 6. Langkah Implementasi Bertahap

*Catatan: Checklist di bawah ini akan dieksekusi oleh skill `web-implementation` setelah persetujuan rencana.*

### Tahap 1: Penguatan Keamanan Middleware (SEC-03 & SEC-12)
- [x] **Langkah 1.1**: Buat middleware `app/Http/Middleware/EnsureAccountActive.php` (SEC-03):
  - Periksa `$user->status_aktif`. Jika `false`, panggil `Auth::logout()`, `$request->session()->invalidate()`, regenerasi CSRF token, dan redirect ke route login dengan flash error.
- [x] **Langkah 1.2**: Buat middleware `app/Http/Middleware/EnsureSuperAdminReadOnly.php` (SEC-12):
  - Periksa apakah `$user->role === RoleEnum::SuperAdmin` dan request bukan method aman (`isMethodSafe()`). Jika benar, lempar `abort(403, 'SuperAdmin hanya memiliki hak baca pada fungsi Pembimbing (SEC-12).')`.
- [x] **Langkah 1.3**: Daftarkan kedua middleware di `app/Http/Kernel.php` pada array `$middlewareAliases`.
- [x] **Langkah 1.4**: Pasang middleware `account.active` dan `superadmin.readonly` pada grup rute `pembimbing` di `routes/web.php`.

### Tahap 2: Penyempurnaan Controller Data Dashboard (`DashboardController.php`)
- [x] **Langkah 2.1**: Optimasi kueri analitik Tab 1 (Ringkasan):
  - Total mentee aktif vs kuota kapasitas dinas (`maks_peserta_aktif_per_pembimbing` BR-13).
  - Jumlah logbook pending reviu (`status = 'terkirim'`).
  - Jumlah progres tugas pending reviu (`status_review = 'menunggu'`).
  - Total tugas aktif berjalan dan rincian tugas yang mengalami keterlambatan (`terlambat` runtime accessor BR-16).
  - Daftar mentee $\le 14$ hari menuju tanggal selesai magang (*Off-boarding Tracker*).
- [x] **Langkah 2.2**: Kueri dataset Tab 2 (Pusat Antrean Reviu Cepat):
  - Seluruh logbook berstatus `terkirim` dengan relasi `penempatan.peserta.profil`.
  - Seluruh progres log berstatus `menunggu` dengan relasi `tugas.penempatan.peserta`.
- [x] **Langkah 2.3**: Kueri dataset Tab 3 (Monitoring Mentee 360°):
  - Daftar seluruh mentee aktif dengan agregasi count tugas selesai, tugas aktif, logbook terkirim, logbook revisi, serta persentase durasi magang berjalan.
- [x] **Langkah 2.4**: Kueri dataset Tab 4 (Evaluasi Rubrik & Capaian Magang):
  - Status penilaian setiap mentee (belum dinilai, draft, final), nilai akhir, predikat huruf (A–E), masa tenggang nonaktif (`tgl_nonaktif` BR-11), dan jejak riwayat revisi nilai final (`penilaian_riwayat` AC-10-3).

### Tahap 3: Redesign Antarmuka Blade Dashboard (`dashboard.blade.php`)
- [x] **Langkah 3.1**: Bangun Hero Eksekutif Sleman Blue (`#0641CC`):
  - Badge bidang dinas dinamis, sapaan personal pembimbing, subteks kapabilitas bimbingan.
  - Widget interaktif kapasitas beban dinas (BR-13) dengan progress bar warna kondisional.
- [x] **Langkah 3.2**: Bangun Segmented Tab Controller responsif:
  - Tab 1: Ringkasan Utama
  - Tab 2: Antrean Reviu Cepat (dengan badge count pending)
  - Tab 3: Monitoring Mentee 360°
  - Tab 4: Evaluasi Rubrik & Capaian
- [x] **Langkah 3.3**: Rancang Konten Tab 1 (Figma Default):
  - 4 Kartu KPI dengan ikon Lucide, aksen warna token Sleman Blue, dan hover elevation.
  - Banner peringatan Off-boarding Mentee $\le 14$ hari.
  - Pending Action Center split 2-kolom seimbang untuk 5 progres tugas dan 5 logbook terbaru.
  - Tabel ringkas mentee bimbingan aktif dengan tombol aksi langsung.
- [x] **Langkah 3.4**: Rancang Konten Tab 2 (Pusat Antrean Reviu Cepat):
  - Tabel interaktif antrean logbook dan progres tugas dilengkapi tombol modal reviu cepat (catatan feedback instan).
- [x] **Langkah 3.5**: Rancang Konten Tab 3 (Monitoring Mentee 360°):
  - Grid kartu/tabel mentee dengan foto avatar, NIM/NISN, kampus/sekolah, nomor kontak, serta progress bar masa magang dan kedisiplinan.
- [x] **Langkah 3.6**: Rancang Konten Tab 4 (Evaluasi Rubrik & Capaian):
  - Matriks evaluasi rubrik, badge predikat nilai terbobot, pelacak masa tenggang akun (BR-11), riwayat perubahan nilai final, dan tombol cetak Lembar Nilai PDF resmi.
- [x] **Langkah 3.7**: Polishing CSS & Ergonomi Antarmuka:
  - Bersihkan scrollbar native yang mengganggu dengan CSS styling token Sleman Blue.
  - Pastikan responsivitas sempurna di desktop (1920px, 1440px, 1280px) hingga tablet/laptop.

### Tahap 4: Automated Testing & Verifikasi Keamanan
- [x] **Langkah 4.1**: Buat test suite `tests/Feature/PembimbingDashboardEnhancementTest.php`:
  - Uji rendering view dashboard pembimbing dan kelengkapan 4 tab.
  - Uji perhitungan kuota kapasitas beban bimbingan (BR-13).
  - Uji penegakan middleware `EnsureAccountActive` (SEC-03) saat status akun diubah ke false.
  - Uji penegakan middleware `EnsureSuperAdminReadOnly` (SEC-12): SuperAdmin 200 pada GET dashboard, namun 403 saat mencoba POST/PUT pada rute pembimbing.
  - Uji isolasi IDOR: Pembimbing hanya melihat antrean dan mentee miliknya sendiri (SEC-11).
- [x] **Langkah 4.2**: Jalankan seluruh feature test eksisting (`PembimbingPortalFeatureTest`, `TugasLogbookTest`, `ScoringAndCompletionTest`, `DashboardModulesRenderingTest`) untuk memastikan zero regression.
- [x] **Langkah 4.3**: Verifikasi visual langsung antarmuka dashboard menggunakan browser subagent pada lingkungan aplikasi aktif.

---

## 7. Strategi Testing

1. **Automated Feature Tests**:
   - `php artisan test tests/Feature/PembimbingDashboardEnhancementTest.php` (5 tests, 24 assertions PASSED)
   - `php artisan test tests/Feature/PembimbingPortalFeatureTest.php` (8 tests PASSED)
   - `php artisan test tests/Feature/TugasLogbookTest.php` (4 tests PASSED)
   - `php artisan test tests/Feature/ScoringAndCompletionTest.php` (7 tests PASSED)
2. **Skenario Pengujian Unit & Otorisasi**:
   - Pembimbing aktif login $\rightarrow$ diarahkan ke `/pembimbing/dashboard` dengan metrik akurat.
   - Pembimbing dinonaktifkan (`status_aktif = false`) saat sesi aktif $\rightarrow$ request berikutnya otomatis logout dan redirect ke login (SEC-03).
   - SuperAdmin membuka `/pembimbing/dashboard` $\rightarrow$ status 200 OK (read-only).
   - SuperAdmin mencoba POST atau PUT ke rute pembimbing $\rightarrow$ status 403 Forbidden (SEC-12).
   - Pembimbing berpindah antar tab (Tab 1, Tab 2, Tab 3, Tab 4) $\rightarrow$ transisi mulus dan data tersaji instan.
3. **Verifikasi Visual Browser**:
   - Capture screenshot dashboard pembimbing dan seluruh tab untuk memastikan kepatuhan visual dengan Figma Node 560-41611.

---

## 8. Risiko & Mitigasi

| Potensi Risiko | Tingkat Dampak | Strategi Mitigasi |
| :--- | :---: | :--- |
| **Kueri dashboard melambat jika data mentee banyak** | Rendah | Menggunakan eager loading relasi (`with(['peserta.profil', 'tugas', 'penilaian'])`), `withCount`, dan filter berindeks `pembimbing_id`. |
| **SuperAdmin tidak sengaja terblokir melihat dashboard pembimbing** | Sedang | Middleware `superadmin.readonly` secara eksplisit hanya memblokir method non-safe (`!$request->isMethodSafe()`), method GET dan HEAD tetap diizinkan 200 OK. |
| **Transisi tab terganggu jika JavaScript dinonaktifkan** | Rendah | Menggunakan atribut CSS display standar dan fallback tab aktif pertama secara default. |
| **Akun staf terputus tiba-tiba tanpa pesan yang jelas** | Rendah | Middleware `EnsureAccountActive` menyertakan flash message berbahasa Indonesia yang jelas: *"Akun Anda sedang dinonaktifkan. Silakan hubungi Administrator Kominfo."* |

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Dashboard Pembimbing (`/pembimbing/dashboard`) memiliki desain visual elegan, bersih, dan selaras 100% dengan Figma Node 560-41611 serta Design System MagKom.
- [x] Arsitektur Segmented Multi-Tab (Tab 1 Ringkasan, Tab 2 Antrean Reviu Cepat, Tab 3 Monitoring Mentee 360°, Tab 4 Evaluasi Rubrik) terpasang dan berfungsi interaktif.
- [x] Indikator beban bimbingan (BR-13) menampilkan kapasitas mentee aktif vs kuota dinas secara real-time.
- [x] Pending Action Center dan Off-boarding Tracker mentee ($\le$ 14 hari) menyajikan peringatan dini yang akurat.
- [x] Middleware keamanan `EnsureAccountActive` (SEC-03) dan `EnsureSuperAdminReadOnly` (SEC-12) aktif dan teruji.
- [x] Seluruh hak akses anti-IDOR (SEC-11) dan isolasi data pembimbing terlindungi.
- [x] Tampilan bebas dari scrollbar abu-abu native tebal yang mengganggu.
- [x] 100% automated test suite lulus tanpa kegagalan atau regresi.
- [x] Verifikasi visual melalui tangkapan layar browser berhasil mengonfirmasi keselarasan desain.

---

## 10. Catatan Perubahan & Saran Lanjutan

- **Catatan Eksekusi**:
  1. Penambahan direktif `@stack('styles')` dan `@stack('scripts')` pada layout utama `resources/views/layouts/dashboard.blade.php` agar komponen halaman anak dapat menyematkan CSS/JS khusus tanpa konflik.
  2. Penyesuaian query pada model `PenilaianRiwayat` di `DashboardController.php` untuk mengurutkan berdasarkan kolom timestamp kustom `waktu` (`latest('waktu')`).
  3. Penanganan tampilan nama jurusan pada tabel ringkasan mentee agar memanggil atribut `nama_jurusan` dari relasi profil.
  4. Penambahan suite pengujian otomatis `tests/Feature/PembimbingDashboardEnhancementTest.php` yang memverifikasi 4 tab, kuota beban mentee (BR-13), SEC-03, SEC-12, dan isolasi data anti-IDOR (SEC-11). Seluruh 24 assertions berhasil (green).
- **Saran Lanjutan (Out of Scope)**:
  - Notifikasi suara halus (audio ping) atau web notification saat ada logbook baru yang masuk di browser pembimbing.
- **Saran Lanjutan (Out of Scope)**:
  - Notifikasi suara halus (audio ping) atau web notification saat ada logbook baru yang masuk di browser pembimbing.
