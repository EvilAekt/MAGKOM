# Rencana Kerja: Perbaikan Teks Bocor (Overflow) pada Segmented Control Tipe Slide Hero Inspector

**Dokumen**: `docs/plans/2026-11-24-perbaikan-overflow-tipe-slide-hero-inspector.md`  
**Tanggal**: 24 November 2026  
**Status**: Selesai Diimplementasikan  
**Terkait**: Perbaikan Antarmuka Inspector Canvas Editor (MAGKOM Site Editor)

---

## 1. Tujuan
Memperbaiki masalah tata letak antarmuka (*UI overflow*) pada kartu slide Hero Banner di panel kanan (Inspector), di mana tombol ketiga (`Pengumuman`) bocor/keluar menembus batas kontainer kartu slide (`.wp-slide-card`), sehingga tampilan menjadi rapi, proporsional, dan tidak ada teks yang terpotong atau bocor keluar.

---

## 2. Asumsi & Analisis Masalah
- **Penyebab Utama**:
  1. Lebar panel kanan Inspector dibatasi sekitar `350px`. Setelah dikurangi *padding* panel dan *padding* kartu slide (`.wp-slide-card`), ruang horizontal yang tersedia untuk komponen switcher hanya sekitar `286px`.
  2. Tombol segmented control (`.wp-slide-type-btn`) memuat teks panjang: `Banner Poster` + `Foto & Info` + `Pengumuman` dengan ikon 13px dan `white-space: nowrap`.
  3. Total lebar gabungan dari 3 tombol tersebut melebihi lebar kontainer (`~305px > 286px`), dan karena tidak ada `min-width: 0` serta label teks terlalu panjang, tombol ketiga terdorong keluar menembus garis tepi kartu (*card border*).
- **Asumsi**:
  - Penjelasan tipe slide yang detail ("Banner Poster: Gambar desain jadi Canva/Photoshop...", "Foto & Info: Foto dokumentasi...") sudah tertulis lengkap di kotak catatan info (`.wp-slide-type-note`) di bawahnya.
  - Oleh karena itu, label tombol dapat disingkat menjadi ringkas, jelas, dan proporsional tanpa mengurangi pemahaman pengguna.

---

## 3. Evaluasi Pendekatan Teknis

### Opsi A: Label Ringkas + Defensive Flexbox CSS (Direkomendasikan)
- **Deskripsi**:
  - Ringkaskan label pada tombol:
    - `Banner Poster` $\rightarrow$ **`Banner`**
    - `Foto & Info` $\rightarrow$ **`Foto & Teks`**
    - `Pengumuman` $\rightarrow$ **`Pengumuman`**
  - Terapkan aturan *defensive CSS* pada `.wp-slide-type-btn`:
    - `min-width: 0;` (mencegah *flex overflow* bawaan browser)
    - `font-size: 11px;`
    - `padding: 5px 4px;`
    - `gap: 4px;`
    - Terapkan `overflow: hidden; text-overflow: ellipsis; white-space: nowrap;` pada elemen `span` label teks.
  - Tambahkan `box-sizing: border-box; overflow: hidden;` pada `.wp-slide-card`.
- **Kelebihan**:
  - Tampilan segmented control 3 tombol tetap berada dalam 1 baris yang ramping dan presisi.
  - Tidak memakan tinggi vertikal.
  - Aman 100% dari kebocoran teks di resolusi atau zoom apa pun.
- **Kekurangan**: Tidak ada.

### Opsi B: Tata Letak Vertikal / Stack Icon + Text
- **Deskripsi**: Menempatkan ikon di atas teks (*flex-direction: column*) dengan label tetap panjang.
- **Kelebihan**: Teks tidak horizontal.
- **Kekurangan**: Segmented control menjadi terlalu tinggi (memakan ruang vertikal), terasa kaku, dan tidak sejalan dengan estetika segmented control modern DS-MagKom.

> **Keputusan**: Menggunakan **Opsi A**.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/js/pages/superadmin/site-editor/inspector.js` | `Modify` | Menyesuaikan label tombol segmented control menjadi `Banner`, `Foto & Teks`, dan `Pengumuman`. |
| `resources/css/pages/superadmin/site-editor.css` | `Modify` | Memperbarui gaya CSS `.wp-slide-type-switcher`, `.wp-slide-type-btn`, dan `.wp-slide-card` dengan *defensive CSS* (`min-width: 0`, `text-overflow: ellipsis`, padding presisi). |

---

## 5. Langkah Implementasi

- [x] **Langkah 1**: Perbarui markup generator tombol di `resources/js/pages/superadmin/site-editor/inspector.js` agar label tombol lebih ringkas dan proporsional.
- [x] **Langkah 2**: Perbarui stylesheet `resources/css/pages/superadmin/site-editor.css` untuk mencegah overflow dan memastikan tombol fleksibel menyesuaikan kontainer kartu.
- [x] **Langkah 3**: Jalankan kompilasi Vite (`npm run build`) untuk memperbarui aset *bundle*.
- [x] **Langkah 4**: Jalankan linter dan test suite (`vendor/bin/pint --test` & `php artisan test --filter=LandingCmsSiteEditorTest`).
- [x] **Langkah 5**: Verifikasi visual di browser untuk memastikan ketiga tombol pas berada di dalam kartu tanpa bocor keluar.

---

## 6. Strategi Testing
- **Visual Inspection**: Memastikan tombol slide 1, slide 2, dst. memiliki padding proporsional dan teks `Pengumuman` berada sepenuhnya di dalam kontainer.
- **Interaksi Switching**: Memastikan klik pada tombol `Banner`, `Foto & Teks`, dan `Pengumuman` tetap berganti tipe secara responsif ke iframe kanvas dan form input terkait.
- **Automated Tests**: Memastikan 19 test fitur pada `LandingCmsSiteEditorTest` tetap hijau (pass).

---

## 7. Risiko & Mitigasi
- **Risiko**: Teks terpotong pada monitor dengan rendering font yang lebih tebal.
- **Mitigasi**: Menerapkan `text-overflow: ellipsis` dan `min-width: 0`, serta memastikan tooltip `title` bawaan tetap memuat penjelasan lengkap saat tombol di-hover.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Ketiga tombol tipe slide (`Banner`, `Foto & Teks`, `Pengumuman`) pas berada 100% di dalam bingkai `.wp-slide-card` tanpa ada teks atau border yang bocor keluar.
- [x] Tooltip penjelasan tipe slide tetap aktif saat di-hover.
- [x] Aset Vite berhasil di-build tanpa error.
- [x] Tidak ada aturan linter atau test yang gagal.

