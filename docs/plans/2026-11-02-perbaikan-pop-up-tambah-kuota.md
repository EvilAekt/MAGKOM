# Rencana Kerja: Perbaikan Pop-Up Modal Tambah Kuota (Penyelesaian Overflow Dropdown Bidang)

**Tanggal Dokumen**: 2026-11-02  
**Modul**: Administrasi MagKom — Manajemen Kuota Magang (`admin/kuota`)  
**Status**: ✅ Selesai Diimplementasikan & Diverifikasi  

---

## 1. Tujuan
Memperbaiki tata letak dan visual pop-up modal **"Tambah kuota"** (`#modalTambahKuota`) pada halaman Manajemen Kuota Magang (`resources/views/admin/kuota/index.blade.php`), khususnya pada elemen dropdown **"Pilih Bidang"** (`#tambahKuotaBidangSelect`) yang saat ini melebar dan menembus keluar dari batas sisi kanan kartu pop-up (*overflow*), sehingga form modal tertata rapi, simetris, dan proporsional di dalam batas padding modal.

---

## 2. Visual Masalah Saat Ini

![Overflow Dropdown Bidang pada Modal Tambah Kuota](C:/Users/ALDI%20BOYS/.gemini/antigravity/brain/8ac85b61-3384-4008-bb69-7f98f2cbd22d/tambah-kuota-overflow.png)

### Analisis Akar Masalah (Root Cause):
1. **Ketiadaan Aturan Boundary pada Elemen Form**:
   Pada styling `.form-row-group input` dan `.form-row-group select` di `resources/views/admin/kuota/index.blade.php` (baris 597–605), belum dideklarasikan properti:
   - `width: 100%;`
   - `max-width: 100%;`
   - `min-width: 0;`
   - `box-sizing: border-box;`
2. **Auto-Expansion Track CSS Grid `1fr`**:
   Baris input Bulan dan Bidang menggunakan `display: grid; grid-template-columns: 1fr 1fr; gap: 0.875rem;`. Di dalam CSS Grid, fraksi `1fr` secara default berperilaku sebagai `minmax(auto, 1fr)`.
3. **Panjang Teks Opsi Dropdown Bidang**:
   Opsi dropdown memuat string panjang seperti *"Aplikasi Informatika (EGOV)"* (28 karakter) dan *"Infrastruktur TI & Persandian (ITI)"* (35 karakter). Karena select dan grid column tidak dibatasi `min-width: 0`, browser mengkalkulasi lebar minimum kolom berdasarkan panjang teks opsi terpanjang. Hal ini memaksa kolom kedua melebar ke kanan hingga menembus dinding kartu putih modal (`.modal-card`).
4. **Potensi Overflow Serupa pada Baris 3 Kolom**:
   Baris 4 (Jumlah kuota, Durasi minimum, Durasi maksimum) menggunakan `grid-template-columns: 1fr 1fr 1fr;` yang rentan mengalami isu serupa pada viewport yang lebih padat.

---

## 3. Evaluasi Pendekatan Teknis

| Kriteria | Opsi A: Robust CSS Grid Sizing & Control Contained (Direkomendasikan) | Opsi B: Stacked Vertikal 1 Kolom Penuh |
| :--- | :--- | :--- |
| **Penyelesaian Overflow** | **100% Tuntas**: Menggunakan `repeat(2, minmax(0, 1fr))` dan `min-width: 0; width: 100%` mengunci elemen form di dalam batas grid. | Tuntas, namun mengubah komposisi layout form. |
| **Tinggi Modal & Ergonomi** | **Ideal**: Form tetap ringkas dalam 2 kolom tanpa menambah tinggi modal, tidak memerlukan scroll. | **Buruk**: Modal menjadi lebih panjang ke bawah, berpotensi terpotong di layar laptop. |
| **Konsistensi Desain** | **Sangat Baik**: Selaras dengan grid 2-kolom pada modal *Ubah Bulan* dan *Buat Bulan Baru*. | **Kurang Baik**: Berbeda sendiri dari modal-modal lainnya di halaman yang sama. |

> **Rekomendasi**: Menggunakan **Opsi A** (memperkuat constraint CSS Grid dan form controls).

---

## 4. Rincian Teknis Perubahan

### A. CSS Box Constraint & Input Sizing
Memperbarui aturan styling form di `resources/views/admin/kuota/index.blade.php` dan `resources/css/pages/admin/dashboard.css`:
```css
.form-row-group {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    margin-bottom: 1rem;
    min-width: 0; /* Kunci: mencegah grid item melar keluar */
}

.form-row-group input,
.form-row-group select {
    height: 42px;
    width: 100% !important;
    max-width: 100% !important;
    min-width: 0 !important;
    box-sizing: border-box !important;
    border: 1px solid #CBD5E1;
    border-radius: 8px;
    padding: 0 0.875rem;
    font-size: 0.875rem;
    font-family: inherit;
    text-overflow: ellipsis; /* Menjaga teks rapi saat dropdown tertutup */
}
```

### B. Grid Definition Modal Tambah Kuota
Memperbarui container grid baris 1 dan baris 4 pada modal `#modalTambahKuota`:
- **Baris 1 (Bulan & Bidang)**:
  `grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.875rem; width: 100%;`
- **Baris 4 (Jumlah Kuota, Durasi Min, Durasi Max)**:
  `grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.75rem; width: 100%;`

---

## 5. Permohonan Izin Perubahan Markup Blade (Sesuai Aturan AGENTS.md Bagian 3.3)

> [!IMPORTANT]
> **Alasan Perubahan Diperlukan**:  
> Grid container Baris 1 dan Baris 4 pada `#modalTambahKuota` saat ini menggunakan inline `style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.875rem;"` dan `grid-template-columns: 1fr 1fr 1fr;`. Diperlukan penyesuaian markup inline style tersebut ke `repeat(2, minmax(0, 1fr))` dan `repeat(3, minmax(0, 1fr))` atau penyediaan class semantik grid agar kolom tidak melar melebihi lebar kartu.

- **File yang akan diubah**: `resources/views/admin/kuota/index.blade.php`
- **Bagian**:
  1. Baris 584–605 (Style tag internal untuk `.form-row-group input, .form-row-group select`).
  2. Baris 1448 (Grid 2 kolom Baris 1 Bulan & Bidang).
  3. Baris 1510 (Grid 3 kolom Baris 4 Kuota & Durasi).

---

## 6. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/kuota/index.blade.php` | Modify | Menambahkan `width: 100%`, `min-width: 0`, `box-sizing: border-box` pada `.form-row-group select/input`, serta memperbarui deklarasi grid baris 1 dan baris 4 pada `#modalTambahKuota`. |
| `resources/css/pages/admin/dashboard.css` | Modify | Menambahkan class utility `.modal-grid-2col` dan `.modal-grid-3col` serta aturan prevent overflow pada modal-card. |
| `public/css/dashboard-cockpit-unified.css` | Modify | Sinkronisasi fallback styling untuk modal form controls. |
| `tests/Feature/AdminKuotaRevampTest.php` | Modify / Verify | Memastikan seluruh assertion elemen modal `tambahKuotaBidangSelect` dan form submit tetap lulus 100%. |

---

## 7. Langkah-Langkah Implementasi Bertahap

- [x] **Fase 1: Persetujuan Pengguna**
  - [x] Mendapatkan konfirmasi eksplisit dari pengguna untuk memulai perbaikan.
- [x] **Fase 2: Perbaikan Styling & Markup Form Control (`admin/kuota/index.blade.php`)**
  - [x] Terapkan `min-width: 0`, `width: 100%`, `max-width: 100%`, `box-sizing: border-box`, dan `text-overflow: ellipsis` pada `.form-row-group select` dan `.form-row-group input`.
  - [x] Perbarui grid Baris 1 (`Bulan` & `Bidang`) menjadi `grid-template-columns: repeat(2, minmax(0, 1fr))`.
  - [x] Perbarui grid Baris 4 (`Jumlah`, `Min`, `Max`) menjadi `grid-template-columns: repeat(3, minmax(0, 1fr))`.
- [x] **Fase 3: Sinkronisasi CSS Global & Fallback**
  - [x] Tambahkan class `.modal-grid-2col` dan `.modal-grid-3col` pada `resources/css/pages/admin/dashboard.css`.
  - [x] Sinkronisasikan ke `public/css/dashboard-cockpit-unified.css`.
- [x] **Fase 4: Asset Compilation & Quality Gate**
  - [x] Jalankan `npm run build`.
  - [x] Jalankan `php vendor/bin/pint --dirty --test`.
  - [x] Jalankan pengujian PHPUnit `tests/Feature/AdminKuotaRevampTest.php`.
- [x] **Fase 5: Verifikasi Visual dengan Playwright MCP**
  - [x] Navigasikan browser ke `http://magkom.test/admin/kuota`.
  - [x] Buka pop-up Tambah Kuota (`openTambahKuotaModal()`).
  - [x] Ambil tangkapan layar modal untuk memastikan dropdown Bidang berada 100% di dalam batas kartu dan sejajar simetris dengan input Bulan di sebelah kirinya.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Dropdown "Bidang" (`#tambahKuotaBidangSelect`) tidak lagi keluar/menembus batas kanan kartu modal `#modalTambahKuota`.
- [x] Jarak margin dan padding di sisi kanan modal terlihat rapi, simetris, dan konsisten (padding 1.75rem merata di semua sisi).
- [x] Baris 3 kolom (Jumlah kuota, Durasi minimum, Durasi maksimum) tertata rapi dan proporsional.
- [x] Interaksi buka/tutup modal dan pemilihan bidang via JavaScript (`onBidangSelected`) tetap berfungsi normal.
- [x] Semua 12 pengujian PHPUnit `AdminKuotaRevampTest` lulus (100% green).
- [x] Code formatting Pint bersih.

