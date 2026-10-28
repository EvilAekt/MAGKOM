# Rencana Kerja: Penataan Spasi & Jarak Bawah 4 Kotak Kartu Metrik Hero Banner Admin

**Dokumen**: `docs/plans/2026-10-28-spasi-bawah-card-box-hero-banner.md`  
**Tanggal**: 28 Oktober 2026  
**Status**: Menunggu Persetujuan User  

---

## 1. Tujuan
Memberikan jarak bawah (*bottom padding/spacing*) yang proporsional, nyaman, dan seimbang pada 4 kotak kartu metrik putih (*Verifikasi Tertunda*, *Kapasitas Kuota Terisi*, *Antrean Waiting List*, dan *Peserta Magang Aktif*) di dalam kontainer Hero Banner biru (`.cv-hero-banner`), sehingga tidak lagi mepet atau menempel ke tepi bawah banner.

---

## 2. Analisis Masalah Visual & Asumsi
1. **Penyebab Utama**:
   - Pada file `public/css/dashboard-cockpit-unified.css` (baris 377), properti padding `.cv-hero-banner, .gentelella-hero-banner` diatur dengan nilai:
     ```css
     padding: 1.75rem 2rem 0 2rem;
     ```
   - Nilai padding bawah disetel ke `0` (nol piksel), sehingga bagian bawah kartu putih langsung bersentuhan dengan lengkungan sudut (*border-radius: 16px*) dari banner biru.
   - Selain itu, wrapper `.cv-hero-banner .tile_count` memiliki `margin: 1.25rem 0 0 0 !important;` (margin bawah juga nol).
2. **Asumsi Kerja**:
   - Warna latar belakang biru gradasi solid bawaan (`linear-gradient(135deg, #1b3860 0%, #162e4f 100%)`) tetap dipertahankan sesuai instruksi user sebelumnya.
   - Keempat kartu metrik tetap berjajar 4 kolom di desktop dan adaptif responsif pada perangkat tablet/mobile.

---

## 3. Evaluasi Opsi Pendekatan Teknis

| Parameter | Opsi 1: Balanced Executive Spacing (Rekomendasi) | Opsi 2: Compact Spacing | Opsi 3: Fixed Offset Spacing |
| :--- | :--- | :--- | :--- |
| **Nilai Padding Bawah** | `1.75rem` (28px) — simetris dengan padding atas (`1.75rem`). | `1.0rem` (16px). | `1.25rem` (20px). |
| **Keseimbangan Visual** | **Sempurna & Simetris**. Jarak atas dan bawah kartu metrik ke tepi banner setara, menghasilkan estetika proporsional. | Agak sempit di bawah (*bottom-heavy*). | Cukup baik, namun sedikit asimetris dengan padding atas. |
| **Kesesuaian Desain** | Mengikuti kaidah *8-point grid* dan *Design System MagKom*. | Kurang leluasa untuk bayangan kartu (*drop-shadow*). | Standar. |

**Rekomendasi**: Memilih **Opsi 1 (Balanced Executive Spacing)** dengan `padding: 1.75rem 2rem 1.75rem 2rem;` (atau `padding: 1.75rem 2rem;`) agar ruang bernapas kartu metrik di dalam banner biru serasi dari segala sisi.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `public/css/dashboard-cockpit-unified.css` | **Modify** | Perbarui nilai padding `.cv-hero-banner, .gentelella-hero-banner` menjadi `1.75rem 2rem;` (menambahkan 28px spasi bawah), dan atur margin bawah banner ke statusline strip (`margin-bottom: 1rem`). |
| `resources/views/admin/dashboard/partials/_header.blade.php` | **Review** | Memastikan wrapper `_metrics` tidak memiliki inline style pengurang margin bawah. |

---

## 5. Langkah Implementasi (Setelah Persetujuan)
- [ ] 1. Buka dan edit `public/css/dashboard-cockpit-unified.css`:
  - Ubah `padding: 1.75rem 2rem 0 2rem;` menjadi `padding: 1.75rem 2rem 1.75rem 2rem;` (atau singkatnya `padding: 1.75rem 2rem;`).
  - Sesuaikan `margin-bottom` hero banner dari `0.5rem` menjadi `1rem` agar jarak antara banner biru ke statusline horizontal di bawahnya tetap lega dan seimbang.
- [ ] 2. Tambahkan aturan responsif pada layar kecil:
  - Pada breakpoint tablet/mobile (`@media (max-width: 768px)`), berikan `padding: 1.25rem 1.25rem 1.25rem 1.25rem;` agar tetap nyaman tanpa memakan ruang vertikal layar kecil secara berlebihan.
- [ ] 3. Jalankan pengujian otomatis untuk memastikan tidak ada regresi:
  ```powershell
  php artisan test --filter="AdminDashboardEnhancementTest|DeltaDashboardEnhancementTest|GentelellaDashboardRevampTest"
  ```
- [ ] 4. Verifikasi tampilan di browser (`http://127.0.0.1:8000/admin/dashboard` dan `http://magkom.test/admin/dashboard`).

---

## 6. Strategi Testing
1. **Regresi Fungsional**:
   - `AdminDashboardEnhancementTest`: Memastikan semua metrik 4 kartu terisi data yang valid.
   - `DeltaDashboardEnhancementTest`: Memastikan komponen pendukung dan statusline tetap utuh.
   - `GentelellaDashboardRevampTest`: Memastikan struktur token kelas Gentelella (`tile_count`, `tile_stats_count`) tetap ter-render secara benar.
2. **Verifikasi Visual**:
   - Cek jarak bawah 4 kartu ke tepi biru pada desktop (minimal 24px - 28px).
   - Cek bayangan kartu (*box-shadow*) tidak terpotong oleh overflow banner.
   - Cek tampilan dalam tema Terang (*Light Mode*) dan Gelap (*Dark Mode*).

---

## 7. Kriteria Selesai (Definition of Done)
- [ ] Keempat kartu metrik memiliki jarak bawah yang lega dan proporsional (tidak menempel ke dasar banner).
- [ ] Jarak simetris antara bagian atas judul banner ke tepi atas dan bagian bawah kartu ke tepi bawah banner.
- [ ] Seluruh automated test suite lulus 100%.
- [ ] Tampilan rapi dan nyaman dilihat di browser.

