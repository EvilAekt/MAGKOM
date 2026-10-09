# Plan: Penyelarasan Presisi Desain dari File Figma `MAGKOM Kominfo Sleman (1).fig` (Header & Bidang Kuota)

- **Tanggal**: 2026-10-08
- **Status**: Draft (Menunggu Persetujuan)
- **Author**: Antigravity (web-planning)
- **Referensi Desain**: `MAGKOM Kominfo Sleman (1).fig` (Bab 9.1 PRD: *PRD berlaku untuk aturan bisnis/AC, Figma berlaku untuk estetika tampilan*)

---

## 1. Tujuan
Memperbaiki tata letak visual landing page portal MagKom dengan mengacu langsung pada file Figma asli `g:\MAGKOM\MAGKOM Kominfo Sleman (1).fig`:
1. **Header Spacing (Node 562-54980)**: Mengatasi permasalahan logo dan menu navigasi yang terlalu berhimpitan (*cramped/clumped*). Menyelaraskan layout header 1440px sesuai Figma:
   - Lebar container penuh (`width: 100%`, `max-width: 1360px`, `padding: 0 3rem;`).
   - Jarak lega antara Brand Logo (kiri) dan Menu Navigasi (tengah/seimbang) sebesar ~120px–150px.
   - Jarak antar tautan menu (`gap: 2.25rem` / 36px).
   - Penempatan aksi di sisi kanan (`Masuk` dan tombol rounded solid blue `Daftar magang`).
2. **Aset Autentik Hero Section (Node 562-54996)**: Menggunakan foto kantor/seremoni Diskominfo Kabupaten Sleman asli yang diekstrak langsung dari arsip `.fig` (`scratch/fig_images/b81e2849a2a2c81e789566edcd745cc36a29e36a.png`), menggantikan gambar generik sementara.
3. **Bagian Bidang & Kuota Multi-Major (Figma Frame & PRD AC-01-1)**:
   - Di file Figma, ketersediaan kuota disajikan dalam 4 kartu Bidang:
     - **Aplikasi Informatika**: Jenjang `SMK · D3 · S1` (Jurusan: *Teknik Informatika, Sistem Informasi, RPL*).
     - **Statistik**: Jenjang `D3 · S1` (Jurusan: *Statistika, Matematika, Sistem Informasi*).
     - **Persandian**: Jenjang `S1` (Jurusan: *Informatika, Teknik Komputer, Keamanan Siber*).
     - **Sekretariat**: Jenjang `SMK · D3 · S1` (Jurusan: *Administrasi, Manajemen, Ilmu Komunikasi*).
   - Di PRD AC-01-1, sistem mensyaratkan pemisahan/penyaringan berdasarkan jenjang: **Jalur SMK** dan **Jalur Mahasiswa (D3 / S1)**.
   - Solusi: Menyediakan tab segmented pill modern (`Jalur SMK (Sekolah Menengah Kejuruan)` dan `Jalur Mahasiswa (D3 / S1)`) dengan tampilan kartu presisi Figma (ikon kotak biru, judul bidang/jurusan, daftar major keahlian, badge jenjang, progress bar status ketersediaan, sisa kuota / waiting list, dan tombol aksi per bulan serta tombol utama *Daftar ke bidang pilihan*).
4. **Master Data Lokal**: Memastikan `php artisan db:seed --class=JurusanSeeder` dan `PeriodeKuotaSeeder` aktif di runtime server lokal agar seluruh jurusan dan kuota tampil hidup di browser.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi**:
  - Penyelarasan mengikuti prinsip PRD Bab 9.1: *"Bila desain Figma bertentangan dengan PRD, PRD yang berlaku untuk perilaku (aturan bisnis dan AC), Figma yang berlaku untuk estetika tampilan"*.
  - Fungsionalitas backend AC-01-1 s/d AC-01-6 (ketersediaan real-time, waiting list button, zero leak slot khusus) dan seluruh automated feature test suite tetap dipertahankan 100% hijau.
- **Pertanyaan Terbuka**: Tidak ada; file `.fig` telah diekstrak dan aset serta tata letak aslinya telah diverifikasi.

---

## 3. Pendekatan Desain & Arsitektur
- **Opsi Terpilih**: 
  - **Header Layout 3-Zona Lega**: Menggunakan Flexbox dengan `width: 100%`, `max-width: 1360px`, `padding: 0 3rem`, pembagian ruang antara Brand Logo (kiri), Nav Links dengan `gap: 2.25rem` (tengah/seimbang), dan Action Buttons (kanan).
  - **Hero Background Asli dari `.fig`**: Menggunakan foto sertifikasi ISO 27001 Diskominfo Sleman dari arsip `.fig` dengan dark gradient overlay agar teks kontras dan mewah.
  - **Multi-Major Bidang & Kuota Grid**: Menyajikan kartu bidang dan jurusan dengan badge jenjang `SMK` dan `D3 · S1`, daftar keahlian major, dan rincian periode kuota aktif per bulan (November & Desember 2026).
  - **Integrasi Seeder Database**: Menjalankan seeder untuk mengisi master jurusan (`TKJ`, `RPL`, `Sistem Informasi`, `Manajemen Informatika`, `Informatika`, `Teknik Komputer`) dan alokasi kuotanya.

---

## 4. Perubahan File
| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/layouts/app.blade.php` | Modify | Memperbaiki styling `.header-nav`, `.header-nav .container` (`width: 100%`, `max-width: 1360px`, `padding: 0 3rem`), `.nav-inner`, jarak `.nav-menu` (`gap: 2.25rem`), dan pemisahan dari logo |
| `resources/views/public/landing.blade.php` | Modify | Menyesuaikan background hero ke aset asli `.fig`, menyelaraskan styling kartu Bidang dan Kuota dengan 4 bidang Figma dan tab SMK vs Mahasiswa (AC-01-1) |
| `public/images/hero_office_bg.png` | Create | Aset foto asli Diskominfo Sleman yang diekstrak dari `MAGKOM Kominfo Sleman (1).fig` |
| `docs/plans/2026-10-08-landing-header-and-kuota-revamp.md` | Update | Dokumen rencana teknis termutakhir |

---

## 5. Langkah Implementasi
- [x] **Langkah 1**: Salin aset foto autentik dari `scratch/fig_images/b81e2849a2a2c81e789566edcd745cc36a29e36a.png` ke `public/images/hero_office_bg.png`.
- [x] **Langkah 2**: Perbaiki layout CSS Header pada [app.blade.php](file:///g:/MAGKOM/resources/views/layouts/app.blade.php):
  - Deklarasikan `width: 100%; max-width: 1360px; margin: 0 auto; padding: 0 2.5rem;` pada container header.
  - Atur `.nav-inner` dengan `justify-content: space-between; width: 100%;`.
  - Berikan jarak lega antara logo dan menu (`margin-left: 4.5rem;`), serta `gap: 2.25rem` antar item menu.
- [x] **Langkah 3**: Jalankan seeder database lokal (`JurusanSeeder` dan `PeriodeKuotaSeeder`):
  - `php artisan db:seed --class=JurusanSeeder`
  - `php artisan db:seed --class=PeriodeKuotaSeeder`
- [x] **Langkah 4**: Perbaiki struktur & CSS bagian Bidang dan Kuota pada [landing.blade.php](file:///g:/MAGKOM/resources/views/public/landing.blade.php):
  - Perbarui hero background ke `/images/hero_office_bg.png`.
  - Terapkan tab selector SMK vs Mahasiswa (D3/S1) yang rapi.
  - Terapkan kartu bidang/jurusan presisi Figma: ikon kotak biru, judul bidang/jurusan, daftar major keahlian, badge jenjang (`SMK`, `D3 · S1`), status badge kuota (*Tersedia*, *Waiting List*, *Segera Dibuka*), serta tombol aksi pendaftaran langsung.
- [x] **Langkah 5**: Verifikasi visual via tangkapan layar browser dan jalankan test suite `php artisan test --filter=LandingPageTest` (100% PASS: 6 passed, 28 assertions).

---

## 6. Strategi Testing
- **Unit & Integration Test**: `php artisan test --filter=LandingPageTest` (6 passed, 28 assertions - 100% LULUS).
- **UI & Manual Verification**:
  - Pengecekan visual resolusi desktop pada browser `http://127.0.0.1:8000`.
  - Spacing header lega dan proporsional (Logo di kiri, Menu di tengah dengan jarak lega ~72px, Action Buttons di kanan).
  - Foto hero autentik Diskominfo Sleman dari `.fig` tampil tajam dan elegan.
  - Tab SMK (2 major) dan Mahasiswa (4 major) menampilkan kuota, status badge, dan tombol aksi interaktif dengan presisi.

---

## 7. Kriteria Selesai (Definition of Done)
- [x] Spacing header lega dan proporsional sesuai Figma (logo di kiri, menu di tengah dengan gap 36px, tombol aksi di kanan).
- [x] Hero background menggunakan aset asli Diskominfo Sleman dari file `.fig`.
- [x] Bagian Bidang dan Kuota menampilkan tab SMK dan Mahasiswa (D3/S1) dengan beberapa major per tab secara terstruktur dan estetik sesuai Figma.
- [x] Test suite `LandingPageTest` lulus 100% tanpa regresi.
