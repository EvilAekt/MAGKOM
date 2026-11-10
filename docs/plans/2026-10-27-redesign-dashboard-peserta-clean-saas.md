# Rencana Kerja: Redesain Dashboard Peserta (Template Modern Clean SaaS)

**Tanggal:** 27 Oktober 2026  
**Status:** ✅ Selesai Diimplementasikan  
**Pendekatan:** Template 1 — Modern Clean SaaS (Linear / Shadcn UI Inspired)

---

## 1. Tujuan
Mentransformasi antarmuka Dashboard Peserta Magang ([resources/views/peserta/dashboard.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard.blade.php)) menjadi layout yang rapi, bersih, proporsional, dan elegan selaras dengan tema Landing Page baru. Menghilangkan elemen visual berat (gradien tebal, CSS bertumpuk ratusan baris, elemen kaku bergaya AI) dan menggantinya dengan arsitektur kartu modular modern berstandar enterprise SaaS.

---

## 2. Asumsi & Prinsip Desain
- **Desain Linear / Shadcn**: Mengutamakan *white space* yang lega, border tipis (`1px solid #e2e8f0`), bayangan lembut (*subtle micro-shadows*), dan tipografi tajam (`Lexend` untuk heading, `Source Sans 3` untuk teks isi).
- **Struktur Grid 2-Kolom 68% : 32%**: Kolom utama (kiri) fokus pada aktivitas belajar/kerja harian peserta (Logbook & Tugas), sedangkan kolom kanan fokus pada konteks pendukung (Pembimbing Lapangan, Dokumen Resmi, & Jam Layanan).
- **Pembersihan CSS Raksasa**: Mengganti >500 baris CSS usang di `@push('styles')` pada `dashboard.blade.php` dengan styling modular yang ringkas, bersih, dan bebas benturan (*zero style-leak*).

---

## 3. Pendekatan Komponen & Tata Letak

### A. Welcome Card (Header)
- Background putih bersih dengan border halus `1px solid var(--neutral-200)`.
- Status pill lembut di pojok atas ("🟢 Magang Aktif" atau "🔵 Menunggu Seleksi").
- Tombol aksi primer ("+ Catat Logbook Hari Ini") bernuansa Sleman Navy (`#1b3860`) dengan hover mikro yang anggun.

### B. KPI Metrics (4 Card Simetris)
- 4 kartu statistik presisi dengan tinggi seragam:
  1. **Logbook Mingguan** (Total laporan & indikator status review)
  2. **Tugas Bimbingan** (Tugas aktif & selesai)
  3. **Masa Magang** (Hari tersisa & tanggal berakhir)
  4. **Pembimbing Lapangan** (Nama pembimbing & status pendampingan aktif)

### C. Kolom Konten Utama (Kiri ~68%)
- **Panel Tugas Bimbingan**: Tampilan item tugas dengan indikator tenggat waktu, kategori tag, dan tombol buka tugas.
- **Panel Logbook Mingguan**: Riwayat laporan berkala dengan badge status (Disetujui / Menunggu / Perlu Revisi) dan catatan feedback pembimbing.

### D. Kolom Samping (Kanan ~32%)
- **Profil Pembimbing**: Avatar inisial berlatar navy lembut, nama pembimbing, jabatan bidang, dan tombol kontak WhatsApp langsung.
- **Unduhan Berkas & Panduan**: Surat Keterangan Magang (PDF) dan Buku Panduan Magang Sleman (PDF) dengan ikon dokumen yang rapi.
- **Informasi Layanan Diskominfo**: Jadwal jam kerja resmi (Senin–Jumat 08:00–15:30 WIB).

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/dashboard.blade.php` | Modify | Bersihkan CSS lama >500 baris, terapkan layout grid modern 2-kolom responsif |
| `resources/views/peserta/dashboard/partials/_header.blade.php` | Modify | Format header Clean Welcome Card standar Linear/Shadcn |
| `resources/views/peserta/dashboard/partials/_kpi_bar.blade.php` | Modify | Standarisasi 4 kartu metrik dengan tipografi dan ikon minimalis terpadu |
| `resources/views/peserta/dashboard/partials/_phase_aktif.blade.php` | Modify | Rapikan daftar Tugas Bimbingan dan Logbook dengan border & spacing simetris |
| `resources/views/peserta/dashboard/partials/_aside.blade.php` | Modify | Format widget pembimbing, berkas resmi, dan jam layanan menjadi kartu modular modern |

---

## 5. Langkah Implementasi

- [x] **Langkah 1**: Refactor [resources/views/peserta/dashboard.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard.blade.php) — hapus CSS usang yang bertabrakan, tetapkan sistem grid `saas-cockpit-grid` yang rapi dan responsif.
- [x] **Langkah 2**: Refactor [_header.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_header.blade.php) — rapikan kartu selamat datang, nama peserta, info bidang, tanggal, dan tombol aksi cepat.
- [x] **Langkah 3**: Refactor [_kpi_bar.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_kpi_bar.blade.php) — selaraskan ukuran kartu KPI, ikon rounded lembut, dan tipografi angka metrik.
- [x] **Langkah 4**: Refactor [_phase_aktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_phase_aktif.blade.php) — rapikan tabel/list tugas dan logbook mingguan dengan badge status yang elegan.
- [x] **Langkah 5**: Refactor [_aside.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_aside.blade.php) — satukan kartu pembimbing, unduhan berkas (SK & Panduan), dan info kontak layanan agar sejajar dan seimbang dengan konten kiri.
- [x] **Langkah 6**: Jalankan `php artisan view:clear` dan verifikasi visual dashboard peserta pada browser.

---

## 6. Kriteria Selesai (Definition of Done)
- [x] Tampilan dashboard peserta terlihat rapi, simetris, dan modern (tidak ada elemen kaku/bertabrakan).
- [x] Rute dan tautan aksi (Logbook, Tugas, PDF Panduan, SK) berfungsi normal tanpa error.
- [x] Layout responsif dengan baik di layar desktop, tablet, dan smartphone.
