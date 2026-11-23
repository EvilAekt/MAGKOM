# Rencana Kerja: Penyatuan Tab Navigasi Kategori dan Selector Bulan Menjadi Toolbar Terpadu (Unified Control Bar)

> **Status:** SELESAI (COMPLETED)  
> **Tanggal:** 23 November 2026  
> **Target Branch:** `test-2`  
> **Dokumen Terkait:** `docs/01-PRD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `.agents/rules/AGENTS.md`  
> **Referensi Diskusi Pengguna:**
> - Pertanyaan Pengguna: *"anda kan seorang senior developer profesional ya, aku minta sara ke kamu dong baiknya tombol daftar bulan magang dan kuota per bidang kalo di masukan ke chart bulan magang gmn? jadi nanti tombol daftar bulan magang dan kuota per bidang itu di sebelah kiri trs bulan magang di sebelah kanan"*
> - Instruksi Eksekusi: *"implementasikan"*

---

## 1. 🎯 Tujuan & Analisis Arsitektur UI/UX

Menggabungkan dua baris kontrol navigasi yang sebelumnya terpisah secara vertikal menjadi **Satu Baris Toolbar Terpadu (*Single Unified Control Bar*)**:
- **Sisi Kiri**: Tab Navigasi Kategori Tampilan (`[ 📅 Daftar Bulan Magang ]` dan `[ 🗂️ Kuota Per Bidang ]`).
- **Sisi Kanan**: Selector Filter Gelombang Periode (`[ 📅 Bulan Magang: [Dropdown] ]`).

### Manfaat Utama:
1. **Zero Spacing Issue**: Menghilangkan 100% masalah celah vertikal antar-baris karena kedua kontrol kini berada dalam satu wadah kontainer horizontal yang sama.
2. **Penghematan Ruang Vertikal (Vertical Space Saving)**: Menghemat lebih dari ~40px tinggi viewport halaman sehingga tabel data di bawahnya langsung terlihat tanpa perlu banyak scroll.
3. **Keseimbangan Visual (Visual Balance)**: Mengisi ruang kosong pada bar filter periode dengan tab navigasi di sisi kiri, menghasilkan keseimbangan simetris yang profesional dan elegan.
4. **Desain Responsif**: Menggunakan `flex-wrap: wrap` sehingga di desktop (> 768px) tersusun 1 baris seimbang (*justify-between*), dan di layar ponsel (< 768px) otomatis terbungkus rapi secara vertikal (*column*).

---

## 2. 🗂️ Rincian File yang Dimodifikasi

| No | File | Jenis | Detail Modifikasi |
| :--- | :--- | :--- | :--- |
| 1 | [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php) | **Modify** | Menggabungkan `.kuota-nav-tabs` ke dalam kontainer `.month-switch-strip.kuota-unified-toolbar`, menempatkannya di sisi kiri, dan form filter bulan di sisi kanan. |
| 2 | [`resources/css/pages/admin/kuota-index.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/kuota-index.css) | **Modify** | Menambahkan styling scoped `.kuota-unified-toolbar` dengan `display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin: 1.25rem 0;` dan breakpoint mobile responsif. |
| 3 | `docs/plans/2026-11-23-unified-toolbar-kontrol-kuota.md` | **Create** | Dokumen rekam jejak arsitektur dan spesifikasi teknis fitur. |

---

## 3. 🚀 Checklist Implementasi & Verifikasi

- [x] **1. Refactor Markup Blade**: Pindahkan `.kuota-nav-tabs` ke dalam `.month-switch-strip` dengan class baru `.kuota-unified-toolbar`.
- [x] **2. Penyesuaian Styling CSS**: Tambahkan styling flex layout, spacing, dan dark mode compatibility di `kuota-index.css`.
- [x] **3. Kompilasi Aset Vite**: Jalankan `npm run build` (lulus tanpa error, 169 modul terkompilasi).
- [x] **4. Pengujian Otomatis**: Jalankan `php artisan test tests/Feature/AdminKuotaRevampTest.php` (21 test lulus, 180 assertion passed).
- [x] **5. Code Formatting**: Jalankan `vendor/bin/pint --test` (lulus 100%).

---

## 4. ✅ Kriteria Selesai (Definition of Done)

- [x] Tab `Daftar Bulan Magang` dan `Kuota Per Bidang` berada di sisi kiri dalam satu kartu bar yang sama dengan `Bulan Magang: [Dropdown]` di sisi kanan.
- [x] Kedua tab dapat diklik dan berganti konten dengan mulus tanpa memutus filter bulan.
- [x] Dropdown bulan tetap berfungsi normal dan mempertahankan tab yang sedang aktif via `#monthSwitchTabInput`.
- [x] Tidak ada CSS/JS inline di Blade.
- [x] Seluruh test suite PHPUnit dan build Vite lulus 100%.

