# Rencana Kerja /Goal: Pembaruan & Sinkronisasi Dokumentasi Menyeluruh Branch test-2

> **Mode Eksekusi:** `/goal` Autonomous Execution & Multi-Domain Documentation Synchronization  
> **Status:** Completed & Fully Verified (100% Pass)  
> **Tanggal Pembuatan:** 17 November 2026  
> **Branch Acuan:** `test-2` (Commit: `29a8464`, `ea51e90`, dan seluruh integrasi terkini)  
> **Kerangka Dokumentasi:** Diátaxis Framework (`docs/`)  
> **Single Source of Truth:** 100% presisi terhadap kode sumber aktual dan 13 dokumen rencana kerja terintegrasi  

---

## 1. Tujuan & Latar Belakang

Pembaruan pada branch `test-2` membawa lonjakan fitur, refaktor arsitektur, dan peningkatan antarmuka yang sangat signifikan:
1. **Perampingan Radikal SuperAdmin Site Editor (`29a8464`)**:
   - Penghapusan laci Inserter (`_inserter.blade.php`), tombol `+` di topbar, dan handle tambah blok inline pada kanvas.
   - Transisi ke pola interaksi **Click-to-Edit langsung pada Canvas Iframe**: klik elemen mana pun di kanvas otomatis menyeleksi blok dan membuka panel Inspector kanan dengan tab serta field isian terkait terfokus.
2. **Penyempurnaan Total Halaman Publik & Mobile UX (`ea51e90`)**:
   - Penambahan identitas kemitraan: Logo Pemerintah Kabupaten Sleman (Diskominfo), Universitas AMIKOM Yogyakarta (`logo-amikom.png`), dan D3 Manajemen Informatika (`logo-mi.png`) di atas copyright footer.
   - Tata letak footer desktop 5 kolom simetris (`1.75fr 1fr 1fr 1fr 1fr`), termasuk kolom profil **Tim Pengembang**.
   - Tata letak footer mobile grid 2x2 simetris di bawah profil dinas.
   - **Jalan Lurus Vertikal Tahapan Magang Mobile (`jalan lurus tahapan`)**: Lintasan aspal SVG diubah menjadi garis lurus vertikal ($X = 48$) dengan mobil bergerak tegak lurus ($90^\circ$) dan simpul checkpoint sejajar.
   - **Tombol Aksi Sejajar Tahapan Mobile**: Tombol 'Unduh Panduan PDF' dan 'Peta Alur Interaktif' berdampingan dalam grid 2-kolom ringkas.
   - **Indikator Geser Kuota Carousel Mobile**: Sinergi dots pagination dinamis, floating arrows, dan micro-swipe hint badge ("Geser untuk melihat kejuruan lain").
   - **Hero Carousel 4-Slide Mobile**: Pemisahan slide Pendaftaran (dengan CTA), Persyaratan, Dokumen, dan Foto Kegiatan.
   - **Harmonisasi Kebijakan Privasi (`/kebijakan-privasi`)**: Penghapusan navigasi breadcrumb redundan dan harmonisasi tema container dengan landing page.
3. **Autentikasi & Registrasi Tim Dinamis (`ded39ab`, `1dbeccf`, `ea51e90`)**:
   - **Dynamic Team Roster Cards**: Form pendaftaran kelompok interaktif dengan kartu anggota dinamis di sisi klien (`auth-depth.js`), validasi NISN/NIM, nama, dan email (maks 4 anggota).
   - **Preselected Quota Locking**: Penguncian dropdown tipe dan jurusan saat dibuka via URL landing page (`?bulan=...&jurusan=...`).
   - **Sanitasi Navigasi Login**: Pembersihan parameter query saat mengklik tautan "Masuk / Login" dari halaman registrasi.
   - **Alur Pendaftaran Ulang (Re-apply)**: Peserta yang ditolak dapat mendaftar kembali pada formasi/periode lain (`PesertaReapplyAfterRejectionTest`).
4. **Desain Sistem & Unifikasi Token (`73fa4db`, `2e4b681`, `6309cef`, `3ba3bef`)**:
   - Standarisasi token sidebar navy (`--sidebar-navy-bg`, `--sidebar-navy-surface`) dan perataan kanan footer di seluruh 4 portal peran.
   - Penyelarasan kartu antrean penempatan, chart bar bento standard, dan kartu peringatan dengan palet Bento Navy.
   - Isolasi mutlak Dark Mode & Multi-Bahasa: hanya aktif di 4 dashboard peran, halaman publik/auth terkunci statis ke Mode Terang dan Bahasa Indonesia.
5. **Administrasi & Penjaminan Kualitas (QA) (`adb23dc`, `018ba95`, `570e121`)**:
   - Sanitasi anti-formula injection pada ekspor CSV/Excel (`=`, `+`, `-`, `@`, `\t`, `\r`).
   - Ekspor streaming memori rendah via `StreamedResponse`.
   - Perhitungan SLA 5 hari kerja dinas berbasis hari kerja aktif (`AdminSlaWorkingDaysTest`).
   - Katalog test suite diperbarui mencapai **1.067 test methods / 6.713 assertions (100% Pass)**.

Rencana `/goal` ini mengorkestrasikan pembaruan pada seluruh berkas dokumentasi resmi di `docs/` hingga tuntas, terverifikasi, dan bebas halusinasi.

---

## 2. Tabel Perubahan Berkas Dokumentasi

| No | Berkas Dokumentasi | Aksi | Ruang Lingkup Perubahan |
| :---: | :--- | :---: | :--- |
| 1 | `docs/features/superadmin.md` | Modify | Update BR-22 & alur Canvas Site Editor: penghapusan modul inserter, adopsi alur Click-to-Edit langsung pada iframe kanvas, pembaruan diagram alur, dan pembaruan arsitektur controller/bridge JS. |
| 2 | `docs/features/public-portal.md` | Modify | Tambahkan logo mitra (Diskominfo, AMIKOM, MI), layout grid 5 kolom footer, kolom Tim Pengembang, jalan lurus tahapan mobile, tombol aksi sejajar, indikator geser kuota mobile, hero carousel 4-slide, dan perapian kebijakan privasi tanpa breadcrumb. |
| 3 | `docs/features/registration.md` | Modify | Tambahkan kartu anggota tim dinamis (*Dynamic Team Roster Cards*), penguncian kuota mandiri (*Preselected Quota Locking*), alur re-apply peserta ditolak, dan pembersihan query parameter saat klik login. |
| 4 | `docs/features/administration.md` | Modify | Tambahkan proteksi ekspor data dari formula injection, streaming export memori rendah (`StreamedResponse`), kalkulasi SLA hari kerja dinas, dan pemisahan CSS laporan pendaftaran. |
| 5 | `docs/design/design-system.md` | Modify | Tambahkan token unifikasi sidebar navy dan perataan kanan footer di 4 dashboard peran, spesifikasi visual logo mitra, tata letak grid footer 5 kolom, komponen jalan lurus tahapan mobile, dan tombol aksi sejajar. |
| 6 | `docs/architecture/frontend.md` | Modify | Perbarui inventaris aset Vite 6 (penghapusan inserter styles/scripts, penambahan entri laporan), perbarui runtime canvas bridge (`landing-canvas-runtime.js`, `canvas-bridge.js`), dan modul client-side dynamic team roster. |
| 7 | `docs/development/testing.md` | Modify | Daftarkan test suite baru (`LandingPageToRegistrationPrefillTest`, `PendaftaranKelompokTest`, `AdminSlaWorkingDaysTest`, dll.) dengan metrik aktual (1.067 tests / 6.713 assertions) dan panduan eksekusi CLI. |
| 8 | `docs/README.md` | Modify | Sinkronisasi metadata tanggal verifikasi (2026-11-17), ringkasan fitur terkini, dan validasi seluruh tautan internal Diátaxis. |

---

## 3. Rencana Eksekusi Berbasis Checklist Task-by-Task

### Task 1: Pembaruan Dokumentasi Fitur SuperAdmin (`docs/features/superadmin.md`)
- [x] Buka `docs/features/superadmin.md`.
- [x] Perbarui tabel metadata (*Terakhir Diverifikasi: 2026-11-17*).
- [x] Perbarui Bab 1 (Overview) & Bab 6 (Business Rules) pada **BR-22**:
  - Catat penghapusan total Inserter drawer (`_inserter.blade.php`), tombol `+` di topbar, dan handle inline tambah blok.
  - Dokumentasikan alur kerja **Click-to-Edit langsung pada Canvas Iframe**: klik section/elemen mana pun di kanvas otomatis menyeleksi blok aktif (chip badge) dan membuka panel Inspector kanan ("Pengaturan Blok") dengan form isian konten lengkap terfokus.
- [x] Perbarui diagram alur Mermaid Site Editor agar mencerminkan seleksi klik kanvas langsung.
- [x] Perbarui Bab 7 (Technical Implementation): modul `editor-app.js`, `canvas-bridge.js`, dan `landing-canvas-runtime.js`.

### Task 2: Pembaruan Dokumentasi Portal Publik & Tampilan Mobile (`docs/features/public-portal.md`)
- [x] Buka `docs/features/public-portal.md`.
- [x] Perbarui tabel metadata (*Terakhir Diverifikasi: 2026-11-17*).
- [x] Tambahkan dokumentasi **Identitas Kemitraan & Tata Letak Footer Sleman Modern**:
  - Penempatan 3 logo institusi: Diskominfo Sleman (`logo-sleman.png`), Universitas AMIKOM Yogyakarta (`logo-amikom.png`), dan D3 Manajemen Informatika (`logo-mi.png`).
  - Struktur grid desktop 5 kolom: Info Dinas (1.75fr), Tautan Terkait (1fr), Layanan Publik (1fr), Tim Pengembang (1fr), dan Kontak/Lokasi (1fr).
  - Struktur grid mobile 2x2 kolom: Tautan Terkait, Akun, Media Sosial, dan Pengembang.
- [x] Tambahkan dokumentasi **Pembaruan Pengalaman Pengguna Seluler (Mobile UX)**:
  - **Jalan Lurus Vertikal Tahapan Magang**: Geometri sumbu $X = 48$, mobil melaju tegak lurus $90^\circ$, dan 4 simpul checkpoint sejajar.
  - **Tombol Aksi Sejajar Tahapan**: Tombol 'Unduh Panduan PDF' dan 'Peta Alur Interaktif' dalam grid 2-kolom seimbang di mobile.
  - **Indikator Geser Kuota Carousel Mobile**: Sinergi dots dinamis, floating arrows, dan kicker badge "Geser untuk melihat kejuruan lain".
  - **Hero Carousel 4-Slide Mobile**: Pembagian menjadi 4 slide (Pendaftaran + CTA, Persyaratan, Dokumen, dan Foto Kegiatan Resmi).
- [x] Tambahkan dokumentasi **Harmonisasi Kebijakan Privasi (`/kebijakan-privasi`)**:
  - Penghapusan navigasi breadcrumb redundan.
  - Penyelarasan container kartu dan tipografi dengan tema terang landing page.

### Task 3: Pembaruan Dokumentasi Pendaftaran & Registrasi (`docs/features/registration.md`)
- [x] Buka `docs/features/registration.md`.
- [x] Perbarui tabel metadata (*Terakhir Diverifikasi: 2026-11-17*).
- [x] Tambahkan dokumentasi **Dynamic Team Roster Cards**:
  - Alur pendaftaran jalur kelompok (`jalur = kelompok`): render kartu dinamis anggota tim di sisi klien (`auth-depth.js`), validasi NISN/NIM unik, nama, dan email (maks 4 anggota).
- [x] Tambahkan dokumentasi **Preselected Quota Locking**:
  - Saat calon peserta mengakses `/register` melalui parameter landing page (`?bulan=...&jurusan=...`), dropdown jurusan dan periode terkunci dan kartu ringkasan kuota ditampilkan.
- [x] Tambahkan dokumentasi **Sanitasi Navigasi Login**:
  - Tautan login di halaman registrasi mengarah ke URL bersih `/login` tanpa query string kuota.
- [x] Catat pengujian otomatis pendukung: `LandingPageToRegistrationPrefillTest` (10 test methods, 72 assertions) dan `PendaftaranKelompokTest`.

### Task 4: Pembaruan Dokumentasi Administrasi & Seleksi (`docs/features/administration.md`)
- [x] Buka `docs/features/administration.md`.
- [x] Perbarui tabel metadata (*Terakhir Diverifikasi: 2026-11-17*).
- [x] Tambahkan dokumentasi **Keamanan & Performa Ekspor Data**:
  - Sanitasi anti-formula injection pada CSV/Excel export (`=`, `+`, `-`, `@`, `\t`, `\r` diawali `'`).
  - Pemanfaatan `StreamedResponse` untuk ekspor bertahap dengan memori di bawah 20 MB.
- [x] Tambahkan dokumentasi **Kalkulasi Hari Kerja SLA**:
  - Perhitungan SLA 5 hari kerja mengecualikan akhir pekan dan libur nasional (`AdminSlaWorkingDaysTest`).
- [x] Catat pemisahan aset stylesheet ke `resources/css/pages/admin/laporan.css`.

### Task 5: Pembaruan Sistem Desain (`docs/design/design-system.md`)
- [x] Buka `docs/design/design-system.md`.
- [x] Perbarui tabel metadata (*Terakhir Diverifikasi: 2026-11-17*).
- [x] Tambahkan Sub-bab **Unifikasi Sidebar Navy Tokens & Footer Alignment**:
  - Standardisasi token `--sidebar-navy-bg: #0B132B`, `--sidebar-navy-surface: #1C2541` di 4 dashboard peran.
  - Perataan kanan teks copyright footer (`text-align: right`) di seluruh layout aplikasi.
- [x] Tambahkan Sub-bab **Spesifikasi Aset Logo Kemitraan Footer**:
  - Dimensi dan rasio `logo-sleman.png` (38x46px), `logo-amikom.png` (46x46px), dan `logo-mi.png` (58x46px).
- [x] Tambahkan Sub-bab **Komponen Jalan Lurus Tahapan & Tombol Aksi Sejajar Mobile**:
  - Geometri SVG path lurus dan spesifikasi grid tombol 2-kolom mobile (`1fr 1fr`, gap 8px).

### Task 6: Pembaruan Arsitektur Frontend (`docs/architecture/frontend.md`)
- [x] Buka `docs/architecture/frontend.md`.
- [x] Perbarui tabel metadata (*Terakhir Diverifikasi: 2026-11-17*).
- [x] Perbarui Bab 2 (Pipeline Vite 6):
  - Catat pembersihan aset inserter dan penambahan entri stylesheet admin laporan (`laporan.css`).
- [x] Perbarui Sub-bab Canvas Bridge Runtime:
  - Dokumentasikan alur koordinasi event antara `landing-canvas-runtime.js`, `canvas-bridge.js`, dan `editor-app.js` tanpa perantara inserter drawer.
- [x] Dokumentasikan arsitektur modul `auth-depth.js` dalam mengelola reactive state kartu anggota tim registrasi kelompok.

### Task 7: Pembaruan Dokumentasi Pengujian Kualitas (`docs/development/testing.md`)
- [x] Buka `docs/development/testing.md`.
- [x] Perbarui tabel metadata (*Terakhir Diverifikasi: 2026-11-17*).
- [x] Daftarkan test suite baru branch `test-2`:
  - `Tests\Feature\LandingPageToRegistrationPrefillTest`: Pengujian 10 skenario prefill dan locking formasi kuota.
  - `Tests\Feature\PendaftaranKelompokTest`: Pengujian pembuatan tim dinamis dan alokasi kapasitas kelompok.
  - `Tests\Feature\AdminSlaWorkingDaysTest`: Pengujian batas waktu SLA verifikasi berkas berbasis hari kerja aktif dinas.
- [x] Konfirmasi metrik pengujian: **1.067 test methods**, **6.713 assertions**, **100% Pass**.

### Task 8: Pembaruan Indeks Utama Repositori (`docs/README.md`)
- [x] Buka `docs/README.md`.
- [x] Perbarui tanggal verifikasi (*2026-11-17*) dan status branch `test-2`.
- [x] Perbarui ringkasan fitur terbaru dan lakukan verifikasi validitas seluruh tautan internal.

---

## 4. Strategi Pengujian & Verifikasi Akhir
- Menjalankan `php artisan test tests/Feature/LandingPageToRegistrationPrefillTest.php`.
- Menjalankan `php artisan test tests/Feature/LanguageLocalizationTest.php`.
- Menjalankan `php artisan test tests/Feature/DarkModeTokenConsistencyTest.php`.
- Memverifikasi tidak ada tautan internal dokumen yang rusak (*link validation*).
- Menjalankan `graphify update .` untuk memperbarui indeks pengetahuan repositori.

---

## 5. Kriteria Selesai (Definition of Done)
- [x] Seluruh 8 dokumen rujukan (`docs/features/superadmin.md`, `docs/features/public-portal.md`, `docs/features/registration.md`, `docs/features/administration.md`, `docs/design/design-system.md`, `docs/architecture/frontend.md`, `docs/development/testing.md`, `docs/README.md`) selesai dimutakhirkan.
- [x] Seluruh butir checklist task telah diverifikasi dan ditandai selesai `[x]`.
- [x] Tidak ada inkonsistensi data atau angka palsu dalam dokumentasi.
- [x] Semua pengujian PHPUnit terkait lulus 100%.
- [x] Output akhir menyertakan tag `<!-- GOAL_COMPLETE -->`.
