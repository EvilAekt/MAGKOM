# Rencana Kerja: Penghapusan Informasi Selesai di Kartu Tugas & Perbaikan Hover Terpotong

**Tanggal**: 10 November 2026  
**Status**: Draf Usulan (Menunggu Persetujuan User)  
**Dokumen Referensi**: `docs/plans/2026-11-10-plan-hapus-status-selesai-dan-perbaikan-hover-tugas-peserta.md`  
**Fitur**: Portal Peserta Magang — Modul Penugasan & Proyek Magang (`/peserta/tugas`)

---

## 1. Latar Belakang & Analisis Masalah

Berdasarkan 3 tangkapan layar yang dilampirkan oleh pengguna:

1. **Informasi "Selesai" Redundan pada Kartu Tugas** (`media_1794311197998_ec675827.png` & `media_1794311208368_36a88fb6.png`):
   - Di sisi kiri kartu tugas, sudah terdapat **lingkaran ikon status** (`.assignment-status-icon`) dengan latar Sleman Navy (`#003566`) dan ikon centang putih `✓` yang secara tegas dan semantik menandai bahwa tugas telah selesai dikerjakan.
   - Namun, di footer kanan kartu tugas (tepat di samping tombol aksi kuning `Buka Tugas >`), terdapat kapsul status tambahan bertuliskan `✓ Selesai` (`.assignment-badge-status`).
   - Keberadaan badge ini membuat bagian bawah kanan kartu terasa penuh, redundan dengan ikon di sebelah kiri, dan pengguna meminta untuk **menghapus informasi status "Selesai" tersebut**.

2. **Efek Hover Terpotong (*Clipping*) di Bagian Atas Tugas** (`media_1794311219142_c847dfbd.png`):
   - Pada baris navigasi tab filter segmented pill (`[Semua Tugas (4)]`, `[Selesai (3)]`, `[Lewat Tenggat (1)]`), kurva tepi atas pil terlihat **terpotong rata (*clipped*)**.
   - **Akar Masalah (*Root Cause*)**:
     - Kontainer `.classroom-tabs` memiliki properti `overflow-x: auto;` untuk scroll horizontal mobile. Properti overflow ini secara otomatis memotong konten yang melampaui batas *bounding box* vertikal.
     - Setiap `.tab-pill` memiliki efek `transform: translateY(-1px);` saat hover, serta tab aktif memancarkan bayangan elevasi `box-shadow: 0 3px 12px rgba(0, 53, 102, 0.25);` dengan `translateY(-1px)`.
     - Karena kontainer `.classroom-tabs` hanya memiliki `padding-bottom: 4px;` dan **sama sekali tidak memiliki `padding-top`**, maka pergeseran 1px ke atas dan bayangan elevasi atas langsung terpotong flat oleh batas overflow kontainer.
     - Selain itu, kartu tugas teratas pada `.assignment-list` juga memerlukan ruang elevasi agar bayangan saat hover `translateY(-1px)` tidak terpotong tepi atas daftar tugas.

---

## 2. Asumsi & Batasan Kerja

1. **Ikon Centang Selesai Tetap Dipertahankan**: Lingkaran status di sebelah kiri kartu tugas (`.assignment-status-icon`) yang memuat centang putih tetap dipertahankan sebagai indikator utama penyelesaian tugas.
2. **Tab Filter "Selesai" Tetap Berfungsi**: Tab filter `[Selesai (3)]` di bilah filter atas tetap dipertahankan agar peserta tetap dapat memfilter tugas yang berstatus selesai.
3. **Kompatibilitas Feature Test**: Assertion pengujian fungsional pada `PesertaClassroomTugasLogbookTest` dan `PesertaTugasFilterTabsTest` (seperti pengecekan tombol `Buka Tugas`, tab filter, dan helper accessibility `sr-only`) tidak boleh terganggu dan harus tetap lulus 100%.

---

## 3. Evaluasi Opsi Teknis

| Parameter | Opsi A: CSS Display None | Opsi B: Hybrid Semantic Clean Markup + CSS Tuning (Direkomendasikan) |
|---|---|---|
| **Penanganan Badge Selesai** | Menyembunyikan badge via `.assignment-badge-status { display: none !important; }` di CSS | Menghapus blok kondisional `@if($isSelesai) ... @endif` dari file Blade, dan membersihkan rule CSS-nya |
| **Kerapian DOM** | Elemen markup SVG dan span tetap dirender di DOM | DOM lebih bersih, bebas dari elemen markup mati |
| **Perbaikan Hover Terpotong** | Memberikan `padding-top: 6px` & `margin-top: -6px` pada `.classroom-tabs` | Sama: Memberikan ruang hembus (*breathing room*) vertikal pada `.classroom-tabs` dan `.assignment-list` |
| **Kepatuhan Aturan Repositori** | 0 modifikasi Blade | Memerlukan persetujuan penghapusan elemen visual Blade sesuai AGENTS.md 3.3 |
| **Rekomendasi** | Kurang optimal karena menyisakan markup redundan di DOM | **Sangat Direkomendasikan** untuk kode yang bersih, semantik, dan terawat |

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
|---|---|---|
| [`resources/views/peserta/tugas/index.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/tugas/index.blade.php) | **Modify** | Menghapus blok `@if($isSelesai) <span class="assignment-badge-status">...</span> @endif` pada baris 219–226, sembari tetap menjaga span accessibility `sr-only` dan tombol `btn-tugas-action`. |
| [`resources/css/pages/peserta/tugas-index.css`](file:///d:/MAGKOM/MAGKOM/resources/css/pages/peserta/tugas-index.css) | **Modify** | 1. Memperbarui `.classroom-tabs` dengan `padding: 6px 4px 8px 4px; margin-top: -6px;` untuk mencegah clipping hover/shadow.<br>2. Menambahkan `padding-top: 4px; margin-top: -4px;` pada `.assignment-list` agar kartu teratas bebas bergerak saat hover.<br>3. Menghapus / menyesuaikan styling `.assignment-badge-status` yang sudah tidak digunakan. |

---

## 5. Rencana Langkah Implementasi Terstruktur

- [ ] **Langkah 1**: Perbarui `resources/views/peserta/tugas/index.blade.php` untuk meniadakan render `.assignment-badge-status` bertuliskan "Selesai" di footer kartu tugas.
- [ ] **Langkah 2**: Perbarui `resources/css/pages/peserta/tugas-index.css`:
  - Atur padding & margin pada `.classroom-tabs`:
    ```css
    .classroom-tabs {
        display: flex;
        gap: 10px;
        margin-top: -6px;
        margin-bottom: 20px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding: 6px 4px 8px 4px;
        align-items: center;
    }
    ```
  - Berikan ruang hembus pada `.assignment-list`:
    ```css
    .assignment-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding-top: 4px;
        margin-top: -4px;
    }
    ```
- [ ] **Langkah 3**: Kompilasi aset Vite frontend dengan Node.js v22 (`npm run build`).
- [ ] **Langkah 4**: Bersihkan cache tampilan Blade (`php artisan view:clear`).
- [ ] **Langkah 5**: Jalankan pengujian otomatis:
  - `php artisan test tests/Feature/PesertaTugasFilterTabsTest.php`
  - `php artisan test tests/Feature/PesertaClassroomTugasLogbookTest.php`
- [ ] **Langkah 6**: Jalankan code style verification dengan Laravel Pint (`./vendor/bin/pint --test`).

---

## 6. Strategi Pengujian & Quality Gates

1. **Visual Testing**:
   - Memastikan tidak ada lagi badge `✓ Selesai` di sebelah tombol "Buka Tugas >" pada kartu penugasan berstatus selesai.
   - Memastikan tombol pil tab `Semua Tugas`, `Selesai`, dan `Lewat Tenggat` saat aktif maupun saat di-hover tidak terpotong tepi atasnya maupun bayangannya.
2. **Automated Feature Tests**:
   - `PesertaTugasFilterTabsTest`: Memastikan 3 tab filter berfungsi normal.
   - `PesertaClassroomTugasLogbookTest`: Memastikan halaman index penugasan dan relasinya tetap lulus dengan 73 assertions hijau.
3. **Code Quality**:
   - Laravel Pint lulus 100% tanpa pelanggaran PSR-12.

---

## 7. Kriteria Selesai (Definition of Done)

- [ ] Badge status "✓ Selesai" di samping tombol "Buka Tugas >" telah terhapus dari kartu tugas.
- [ ] Tepi atas tab filter dan bayangan hover tidak lagi terpotong pada berbagai resolusi layar.
- [ ] Kartu tugas teratas dapat melakukan translasi hover dengan mulus tanpa terpotong batas panel.
- [ ] `npm run build` sukses 0 error / warning.
- [ ] Seluruh unit/feature test terkait tugas lulus 100%.

