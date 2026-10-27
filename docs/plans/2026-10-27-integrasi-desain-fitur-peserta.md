# Rencana Kerja: Integrasi Desain Sistem, Warna, Font, & Tata Letak Seluruh Fitur Portal Peserta

**Tanggal Dokumen:** 27 Oktober 2026  
**Status:** ✅ Selesai Diimplementasikan  
**Ruang Lingkup:** Seluruh menu dan fitur pada Portal Peserta Magang ([resources/views/peserta](file:///d:/MAGKOM/MAGKOM/resources/views/peserta) dan [resources/views/layouts/peserta.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/peserta.blade.php))

---

## 1. Tujuan Utama
1. **Menghilangkan Fitur "Lihat Kuota Landing"**: Menghapus item navigasi dan label tautan luar "Lihat Kuota Landing ↗" dari sidebar peserta agar navigasi fokus pada aktivitas magang.
2. **Integrasi Skema Warna Terbaru (Landing Page & Clean SaaS)**: Menyelaraskan seluruh fitur peserta dengan palet Sleman Navy (`#1b3860`), Royal Blue (`#2563eb`), latar canvas lembut (`#f8fafc`), kartu putih bersih dengan border halus (`#e2e8f0`), serta status badge bernuansa soft (menghapus tuntas warna biru neon lama `#0641CC` & `#0751FF`).
3. **Konsistensi Font (Tipografi)**: Menerapkan font `Lexend` untuk semua heading/judul (`h1` s.d. `h5`, kartu, modal, tabel header) dan `Source Sans 3` untuk body teks, formulir input, dan paragraf di seluruh menu peserta secara terpusat.
4. **Konsistensi Tata Letak (Layout Architecture)**: Menerapkan arsitektur halaman seragam pada setiap menu:
   - Header Halaman (Judul Lexend, deskripsi, tombol aksi cepat di kanan atas).
   - Card Modular Clean SaaS (`border: 1px solid #e2e8f0`, `border-radius: 16px`, `box-shadow: 0 1px 3px rgba(15,23,42,0.03)`).
   - Form input & tabel dengan padding proporsional dan border fokus konsisten.

---

## 2. Asumsi & Standar Desain

### A. Palet Warna Baru (Design System MagKom)
- **Primary / Brand**: Sleman Navy `#1b3860` (Hover: `#162e4f`) — Digunakan pada Topbar, Banner Header, Tombol Aksi Utama, Heading Utama.
- **Accent / Interactive**: Royal Blue `#2563eb` (Hover: `#1d4ed8`) — Digunakan pada link aktif, tab terpilih, indikator proses (menggantikan `#0751FF`).
- **Canvas / Background**: Soft Slate Canvas `#f8fafc` & `#f1f5f9`.
- **Card / Surface**: Putih murni `#ffffff` dengan border tipis `1px solid #e2e8f0` dan radius `14px` - `16px`.
- **Status Soft Badges**:
  - *Sukses / Disetujui / Selesai*: Background `#f0fdf4`, Teks `#16a34a`, Border `#bbf7d0`.
  - *Proses / Menunggu / Diajukan*: Background `#fffbeb`, Teks `#d97706`, Border `#fde68a`.
  - *Revisi / Ditolak / Mendesak*: Background `#fef2f2`, Teks `#dc2626`, Border `#fecaca`.
  - *Info / Terkirim / Netral*: Background `#eff6ff`, Teks `#2563eb`, Border `#bfdbfe`.

### B. Tipografi Skala Resmi
- **Display / Heading**: `'Lexend', sans-serif` (letter-spacing: `-0.01em`, font-weight: `600` / `700`).
- **Body / Sans**: `'Source Sans 3', sans-serif` (line-height: `1.5` s.d. `1.6`, font-weight: `400` / `500`).

---

## 3. Rincian Perubahan File & Fitur

| No | File | Aksi | Ringkasan Perubahan Teknis |
| :--- | :--- | :--- | :--- |
| 1 | `resources/views/layouts/peserta.blade.php` | Modify | 1. Hapus grup menu "Tautan Luar" & "Lihat Kuota Landing ↗"<br>2. Kunci pewarisan global font `Lexend` (heading) & `Source Sans 3` (body)<br>3. Standarisasi style active nav menu dengan Royal Blue pill modern<br>4. Standarisasi styling global tombol `.btn-primary` & `.btn-secondary` |
| 2 | `resources/views/peserta/dashboard/partials/_modal_quick_logbook.blade.php` | Modify | Ganti tombol kirim dari warna jadul `#06318C` ke Sleman Navy `#1b3860`, perbarui alert callout ke soft warning card |
| 3 | `resources/views/peserta/profil/show.blade.php` | Modify | Ganti banner cover identity card ke Sleman Navy `#1b3860` & `#162e4f`, perbarui bayangan avatar |
| 4 | `resources/views/peserta/pendaftaran/index.blade.php` | Modify | Ganti banner hero ke Sleman Navy, bersihkan seluruh warna `#0641CC` & `#0751FF` menjadi `#1b3860` & `#2563eb` |
| 5 | `resources/views/peserta/pendaftaran/detail.blade.php` | Modify | Ganti banner hero ke Sleman Navy, bersihkan seluruh `#0751FF` menjadi `#2563eb` |
| 6 | `resources/views/peserta/logbook/index.blade.php` | Modify | Ganti banner penempatan ke Sleman Navy `#1b3860` & `#162e4f` |
| 7 | `resources/views/peserta/tugas/index.blade.php` | Modify | Ganti banner classroom ke Sleman Navy, bersihkan seluruh `#0751FF` menjadi `#2563eb` |
| 8 | `resources/views/peserta/tugas/show.blade.php` | Modify | Bersihkan seluruh `#0751FF` menjadi `#2563eb` |
| 9 | `resources/views/peserta/nilai/show.blade.php` | Modify | Ganti kartu ringkasan nilai ke Sleman Navy `#1b3860` & `#162e4f` |
| 10 | `resources/views/peserta/pengaturan/index.blade.php` | Modify | Bersihkan seluruh `#0641CC` dan `#0751FF` menjadi `#1b3860` & `#2563eb` |
| 11 | `resources/views/peserta/survei/index.blade.php` | Modify | Ganti banner survei dan border form ke Sleman Navy `#1b3860` & Royal Blue `#2563eb` |

---

## 4. Hasil Uji Coba & Verifikasi
- [x] Menu "Lihat Kuota Landing ↗" telah dihapus dari sidebar peserta.
- [x] Seluruh file blade fitur peserta bersih dari warna biru neon lama (`#0641CC` dan `#0751FF` = 0 temuan).
- [x] Pewarisan font global `Lexend` & `Source Sans 3` aktif di layout utama.
- [x] Seluruh controller (Dashboard, Profil, Pendaftaran, Logbook, Tugas, Nilai, Pengaturan) lolos uji render 100%.
- [x] Cache Blade telah dibersihkan (`php artisan view:clear`).
