# Plan: Panduan Lengkap Alur Magang Interaktif & Redesign Premium Pop-Up Menu Akun Pengguna

- **Tanggal**: 2026-10-14
- **Status**: Selesai (Completed & Verified)
- **Author**: Antigravity (web-implementation)
- **Rujukan Standar Dokumen**:
  - [`01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (Bab 1.2, 5, 6 Alur & Status, 7 Acceptance Criteria)
  - [`02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) (Bab 4 Arsitektur Komponen, 8 Keamanan & CSP)
  - [`03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) (Quality Gates & Pengujian)
  - [`04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](file:///g:/MAGKOM/docs/04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md) (Enum Status Pendaftaran, Penempatan, Role)
  - [`05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`](file:///g:/MAGKOM/docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md) (Hak Akses Multi-Peran & CSRF Protection)
  - [`06-ADDENDUM-KEAMANAN-DAN-QA.md`](file:///g:/MAGKOM/docs/06-ADDENDUM-KEAMANAN-DAN-QA.md) (Aksesibilitas WCAG 2.1 AA, Focus Trap, Keyboard Nav)
  - [`07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Palet Warna Resmi, Tipografi Open Sans, Spesifikasi Modal Bab 3.4, Spesifikasi Tombol Bab 3.1)

---

## 1. Tujuan
1. **Panduan Lengkap Alur Magang ("Lihat panduan lengkap")**:
   - Mengubah tombol dummy `<a href="#alur-section" class="btn-panduan">Lihat panduan lengkap</a>` pada Section 3 Alur Magang ([resources/views/public/landing.blade.php](file:///g:/MAGKOM/resources/views/public/landing.blade.php)) menjadi **Modal Dialog Interaktif Panduan Lengkap Alur Magang (Interactive Guide Modal)**.
   - Menyajikan informasi alur magang secara terstruktur, komprehensif, dan mendalam berdasarkan 4 Fase Resmi PRD (Konfigurasi & Pendaftaran, Verifikasi & Seleksi, Pelaksanaan & Logbook, Penilaian & Kelulusan) lengkap dengan prasyarat berkas, timeline/SLA, aturan bisnis kuota, dan call-to-action.
2. **Redesign Premium Pop-Up Menu Akun Pengguna**:
   - Mendesain ulang tampilan pop-up menu akun di navbar ([resources/views/layouts/app.blade.php](file:///g:/MAGKOM/resources/views/layouts/app.blade.php)) agar memiliki estetika profesional korporat/instansi pemerintahan modern (DS-MagKom), menghilangkan kesan generik (*anti-AI-slop*).
   - Menghadirkan header akun terpadu (Avatar dengan ring indicator, nama, email, dan chip peran dinamis), item navigasi dengan icon container beraksen visual dan teks deskripsi mini (Dashboard Utama, Profil Saya, Pengaturan Akun), serta tombol Keluar (Logout) yang aman dengan gaya elegan.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi:
1. **Modal Panduan Alur Magang**:
   - Ditempatkan sebagai in-page modal dialog interaktif di `resources/views/public/landing.blade.php` agar calon peserta dapat langsung membacanya tanpa reload atau berpindah halaman.
   - Mengikuti spesifikasi modal DS-MagKom Bab 3.4: Radius 16–20px, scrim `#1D2530` 50% dengan backdrop blur, tombol tutup 44x44px, dukungan tombol `Escape`, dan penjebak fokus keyboard (*focus trap*).
2. **Konten Panduan 4 Fase Sesuai PRD**:
   - **Fase 1: Pendaftaran & Pemilihan Kuota** (Jalur SMK/Mahasiswa, Mandiri vs Kelompok, Verifikasi Email, unggah CV/Surat Pengantar/Transkrip, BR-01).
   - **Fase 2: Seleksi & Verifikasi Berkas** (Review berkas oleh Admin, Status Diajukan/Perlu Perbaikan/Diterima/Waiting List FIFO, penerbitan Surat Keterangan Diterima resmi, BR-05).
   - **Fase 3: Pelaksanaan & Logbook Harian** (Penetapan Pembimbing Lapangan, pengerjaan tugas bertahap, pengisian logbook harian dengan batas mundur maksimal H-1, BR-09).
   - **Fase 4: Penilaian, Nilai Akhir & Kelulusan** (Penilaian objektif rubrik 100 poin, penerbitan Lembar Nilai PDF resmi & Sertifikat, masa tenggang baca-saja 30 hari, pengarsipan otomatis, BR-10).
3. **Pop-up Akun yang Elegan (Anti-AI-Slop)**:
   - Lebar 280px–300px, multi-layer soft shadow, border halus `1px solid var(--neutral-400)` (light) / `var(--neutral-200)` (dark).
   - Setiap item menu memiliki ikon SVG berlatar *icon badge container* (36x36px) dengan warna aksen harmonis, judul tebal, teks bantuan mini, dan micro-animation pergeseran saat di-hover.

### Pertanyaan Terbuka:
- Tidak ada pertanyaan yang memblokir. Seluruh data rute, aturan bisnis, dan token warna sudah terpetakan di dokumen resmi proyek.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi untuk Panduan Lengkap:
- **Opsi A (In-Page Interactive Guide Modal - Direkomendasikan)**:
  - Menyediakan modal dialog tabbed 4-fase di landing page.
  - *Kelebihan*: Pengguna tetap berada di landing page, akses instan dalam 1 klik tanpa loading halaman baru, interaksi tab yang sangat interaktif dan responsif di mobile.
  - *Kekurangan*: Sedikit penambahan markup HTML & CSS di `landing.blade.php`.
- **Opsi B (Halaman Terpisah `/panduan`)**:
  - Membuat route dan view baru terpisah.
  - *Kelebihan*: URL tersendiri yang bisa dibagikan langsung.
  - *Kekurangan*: Memutuskan alur pendaftaran calon peserta di landing page (harus reload halaman dan kembali lagi).
- **Rekomendasi**: **Opsi A** untuk pengalaman pengguna terbaik, dengan penambahan ID anchor `#alur-panduan` sehingga tetap dapat diakses atau dibuka secara langsung.

### Evaluasi Opsi untuk Redesign Pop-Up Akun:
- Menerapkan arsitektur komponen modular berbasis token warna `DS-MagKom`:
  - Menggantikan item teks monoton dengan *Two-Line Rich Menu Items*:
    1. **Dashboard Utama**: Ikon grid dashboard berlatar Blue Tint `#E6EEFF` + Teks "Dashboard Utama" + Subtitle "Ringkasan magang & status pendaftaran".
    2. **Profil Saya**: Ikon kartu identitas/user berlatar Purple Tint `#FAF5FF` + Teks "Profil Saya" + Subtitle "Kelola biodata diri & riwayat berkas".
    3. **Pengaturan Akun**: Ikon gear sliders berlatar Slate Tint `#F1F5F9` + Teks "Pengaturan Akun" + Subtitle "Keamanan sandi & preferensi tampilan".
    4. **Keluar (Logout)**: Ikon exit door berlatar Red Tint `#FEF2F2` + Teks "Keluar (Logout)" + Subtitle "Akhiri sesi login akun ini secara aman".

---

## 4. Rincian Antarmuka & Skema Desain (UI/UX)

### A. Pop-Up Menu Akun Pengguna (Navbar Landing Page)
```
+-------------------------------------------------------------------------+
| [Avatar 40px] Budi Santoso [✓ Aktif]                                    |
| budi.santoso@gmail.com                     [PESERTA MAGANG]             |
+-------------------------------------------------------------------------+
| [ Icon 36px ]  Dashboard Utama                                       >  |
| (Blue Tint)    Ringkasan pendaftaran, tugas & logbook aktif             |
+-------------------------------------------------------------------------+
| [ Icon 36px ]  Profil Saya                                           >  |
| (Purple Tint)  Kelola biodata, instansi & berkas persyaratan            |
+-------------------------------------------------------------------------+
| [ Icon 36px ]  Pengaturan Akun                                       >  |
| (Slate Tint)   Keamanan kata sandi & preferensi tampilan                |
+-------------------------------------------------------------------------+
| -------------------------- (Divider Halus) ---------------------------- |
+-------------------------------------------------------------------------+
| [ Icon 36px ]  Keluar (Logout)                                       →  |
| (Red Tint)     Akhiri sesi pada perangkat ini secara aman               |
+-------------------------------------------------------------------------+
```

### B. Modal Panduan Lengkap Alur Magang
```
+----------------------------------------------------------------------------------------------------+
|  [PANDUAN RESMI DISKOMINFO]                                                                    [X] |
|  Panduan Lengkap Alur Magang & PKL                                                                 |
|  Transparansi tahapan resmi dari pendaftaran hingga penerbitan lembar nilai dan sertifikat         |
|                                                                                                    |
|  [ 1. Pendaftaran ]    [ 2. Seleksi & Penempatan ]    [ 3. Pelaksanaan ]    [ 4. Kelulusan & Nilai ]|
|  ------------------------------------------------------------------------------------------------- |
|                                                                                                    |
|  Fase 1: Pendaftaran Akun & Pemilihan Kuota                                                        |
|                                                                                                    |
|  Langkah-Langkah:                                  Persyaratan Berkas Wajib:                       |
|  1. Pilih Jalur Pendidikan (SMK / Mahasiswa).       - Surat Pengantar Resmi Sekolah/Kampus (PDF)   |
|  2. Tentukan Tipe (Mandiri 1 orang / Kelompok).     - Curriculum Vitae (CV) Terbaru (PDF)          |
|  3. Pantau Sisa Kuota Real-Time pada Kalender.     - Transkrip Nilai / Rapor Terakhir (PDF)        |
|  4. Lengkapi Biodata pada Portal Peserta.           - Proposal Magang / Portofolio (Opsional)      |
|                                                                                                    |
|  +-----------------------------------------------------------------------------------------------+ |
|  | [!] Aturan Bisnis Penting (BR-01 & BR-03):                                                    | |
|  | Pendaftaran hanya dapat diajukan untuk 1 periode bulan yang sedang berstatus 'Buka'.         | |
|  | Jika kuota umum penuh, pendaftaran otomatis diarahkan masuk ke antrean Waiting List (FIFO).   | |
|  +-----------------------------------------------------------------------------------------------+ |
|                                                                                                    |
|  [Tutup Panduan]                                                        [Lihat Kuota Tersedia →]   |
+----------------------------------------------------------------------------------------------------+
```

---

## 5. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/layouts/app.blade.php`](file:///g:/MAGKOM/resources/views/layouts/app.blade.php) | Modify | 1. Redesign styling dan struktur `.landing-dropdown-menu` menjadi *Rich Interactive Account Card* (avatar 40px, status online indicator, icon container 36px, two-line text per action, smooth hover transform, red tint logout).<br>2. Perbarui tampilan mobile drawer agar selaras dengan desain rich profile card. |
| [`resources/views/public/landing.blade.php`](file:///g:/MAGKOM/resources/views/public/landing.blade.php) | Modify | 1. Ubah tombol `.btn-panduan` menjadi trigger pembuka modal `openPanduanAlurModal()`.<br>2. Tambahkan markup HTML Modal Panduan Lengkap (`#panduanAlurModal`) dengan tab 4 Fase PRD, checklist berkas, SLA verifikasi, callout aturan bisnis, dan tombol navigasi.<br>3. Tambahkan CSS modal panduan sesuai token DS-MagKom (radius 20px, scrim blur, Open Sans font, tab switcher, responsive card layout).<br>4. Tambahkan script JS native untuk tab switcher, modal open/close, click outside, dan tombol Escape. |
| [`tests/Feature/LandingPageTest.php`](file:///g:/MAGKOM/tests/Feature/LandingPageTest.php) | Modify | Tambahkan test otomatis untuk memvalidasi: <br>- Tombol panduan lengkap dan elemen modal panduan alur ter-render pada landing page.<br>- Rich menu pop-up akun menampilkan avatar, email, nama, dan link rich item (Dashboard, Profil, Pengaturan, Logout). |

---

## 6. Langkah Implementasi

- [x] **Langkah 1**: Perbarui CSS dan HTML pop-up menu akun di [`resources/views/layouts/app.blade.php`](file:///g:/MAGKOM/resources/views/layouts/app.blade.php) menjadi *Rich Account Popover* dengan icon container 36px, two-line descriptions, role chips, dan styling anti-ai-slop sesuai token DS-MagKom.
- [x] **Langkah 2**: Tambahkan styling CSS Modal Panduan Alur Magang di tag `<style>` pada [`resources/views/public/landing.blade.php`](file:///g:/MAGKOM/resources/views/public/landing.blade.php) (scrim `#1D2530` 50%, blur overlay, dialog surface `#FFFFFF` / dark `#182234`, tab navigation, callout boxes).
- [x] **Langkah 3**: Sisipkan markup HTML Modal Panduan Alur Magang (`#panduanAlurModal`) di [`resources/views/public/landing.blade.php`](file:///g:/MAGKOM/resources/views/public/landing.blade.php) yang memuat tab interaktif 4 Fase Magang resmi (Fase 1 s/d Fase 4) sesuai PRD Bab 6.
- [x] **Langkah 4**: Hubungkan tombol "Lihat panduan lengkap" di section alur magang (`.btn-panduan`) dengan pemanggilan JavaScript `openPanduanAlurModal()`.
- [x] **Langkah 5**: Tambahkan fungsi JavaScript native di [`resources/views/public/landing.blade.php`](file:///g:/MAGKOM/resources/views/public/landing.blade.php) untuk: `openPanduanAlurModal()`, `closePanduanAlurModal()`, `switchPanduanTab(tabIndex)`, penanganan klik di luar modal (*backdrop click*), dan tombol keyboard `Escape`.
- [x] **Langkah 6**: Tambahkan skenario pengujian otomatis baru di [`tests/Feature/LandingPageTest.php`](file:///g:/MAGKOM/tests/Feature/LandingPageTest.php) untuk memverifikasi markup modal panduan alur dan rich pop-up akun saat login.
- [x] **Langkah 7**: Jalankan `php artisan view:clear` dan `php artisan test` untuk memastikan 100% test lulus tanpa regresi (seluruh 372 tests).
- [x] **Langkah 8**: Lakukan inspeksi visual melalui subagent browser untuk memverifikasi keindahan visual modal panduan lengkap dan rich pop-up akun di browser.
- [x] **Langkah 9**: Jalankan `graphify update .` untuk menyinkronkan knowledge graph proyek.

---

## 7. Strategi Testing

### Automated Unit & Feature Test:
- Menjalankan `php artisan test tests/Feature/LandingPageTest.php`.
- Memastikan pengujian memvalidasi:
  - Elemen modal panduan alur (`#panduanAlurModal`, tab fase 1-4, teks persyaratan berkas) ada pada halaman publik tanpa perlu login.
  - Rich user dropdown menu memuat rincian nama, peran, deskripsi mini (Dashboard, Profil, Pengaturan), dan form logout saat login.
  - Seluruh 372 automated tests eksisting tetap berstatus **100% PASS**.

### Visual & Accessibility Verification:
- Buka landing page di browser.
- Klik tombol "Lihat panduan lengkap" $\rightarrow$ Modal panduan terbuka mulus dengan animasi fade/slide.
- Klik tab Fase 1, 2, 3, dan 4 $\rightarrow$ Konten berganti seketika dengan transisi rapi.
- Tekan tombol `Escape` atau klik di luar modal $\rightarrow$ Modal tertutup dan fokus kembali ke tombol.
- Buka dropdown akun di navbar $\rightarrow$ Menu pop-up tampil kaya konten dengan ikon beraksen warna, dua baris teks per menu, dan status aktif.

---

## 8. Risiko & Mitigasi

- **Risiko**: Modal panduan tertutup navbar yang sticky.
  - *Mitigasi*: Gunakan `z-index: 2050` pada modal dialog dan `position: fixed` di atas seluruh layer landing page.
- **Risiko**: Teks deskripsi di menu pop-up terpotong di layar tertentu.
  - *Mitigasi*: Berikan lebar pas 280px–300px dengan padding horizontal seimbang dan line-height optimal.
- **Risiko**: Konten modal terlalu panjang pada layar smartphone.
  - *Mitigasi*: Berikan `max-height: 85vh` dengan `overflow-y: auto` dan styled scrollbar tipis agar nyaman digulir pada layar HP.

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Tombol "Lihat panduan lengkap" membuka modal panduan 4 fase magang yang komprehensif sesuai PRD.
- [x] Pop-up akun di navbar dipercantik menjadi rich account card dengan info pengguna, icon container beraksen warna, dan deskripsi dua baris untuk Dashboard, Profil, dan Pengaturan.
- [x] Tampilan 100% selaras dengan token warna, tipografi Open Sans, dan elevasi DS-MagKom (Bukan desain generik / Anti-AI-Slop).
- [x] Seluruh automated test suite (372 tests) berstatus 100% PASS.
- [x] Verifikasi visual browser berhasil dan terdokumentasi rapi.
