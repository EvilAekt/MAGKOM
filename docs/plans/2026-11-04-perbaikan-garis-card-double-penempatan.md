# Plan: Perbaikan Garis Card Ganda (Double Border) pada Tabel Penempatan Admin

**Tanggal**: 04 November 2026  
**Status**: Selesai Terverifikasi  
**Referensi**: Permintaan Pengguna, Bukti Tangkapan Layar `media_1793807691877_b4b4185f.png`, Aturan `AGENTS.md`.

---

## 1. Analisis Masalah (Root Cause Analysis)

Berdasarkan tangkapan layar yang dikirimkan oleh pengguna (`media_1793807691877_b4b4185f.png`):

1. **Perbandingan Visual (Atas vs Bawah)**:
   - **Gambar Atas (Normal / Rapi di Laporan)**:
     Diambil dari [`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php). Garis pembatas antara baris tabel terakhir dan footer paginasi hanya berupa satu garis horizontal tipis yang mulus (`border-top: 1.5px solid #CBD5E1;`), tanpa ada garis lengkung ganda atau kotak bersarang di dalamnya.
   - **Gambar Bawah (Bermasalah / Garis Ke-double di Penempatan)**:
     Diambil dari [`resources/views/admin/penempatan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/penempatan/index.blade.php). Tabel terlihat seperti kotak tersendiri berbingkai abu-abu dengan sudut melengkung (*rounded corners*) yang mengambang di dalam kartu, lalu di bawahnya terdapat celah (*gap*) dan garis horizontal footer tambahan. Ini menciptakan kesan **garis ganda (*double borders*)** dan **kartu bersarang di dalam kartu (*nested card box*)**.

2. **Akar Penyebab Teknis**:
   - Di `resources/views/admin/penempatan/index.blade.php` (baris 880–883):
     ```blade
     <div class="adm-table-container">
         <div class="table-responsive-gentelella" style="overflow-x: auto;">
             <table class="admin-table table jambo_table table-striped bulk_action">
     ```
   - Tiga lapisan elemen ini saling menimpa border dan border-radius:
     - Lapisan 1: `.adm-table-container` memiliki `border: 1.5px solid #CBD5E1; border-radius: 12px; overflow: hidden;`.
     - Lapisan 2: `.table-responsive-gentelella` di `resources/css/layouts/gentelella.css` memiliki `border: 1px solid var(--neutral-200, #E2E8F0); border-radius: 8px;`.
     - Lapisan 3: `.table.jambo_table` memiliki `border: 1px solid var(--table-header-border, #E2E8F0); border-radius: 8px; border-collapse: separate;`.
     - Lapisan 4: Baris terakhir tabel `tbody tr:last-child td` memiliki `border-bottom: 1px solid #E2E8F0;`, tepat di atas `.adm-table-footer` yang memiliki `border-top: 1.5px solid #CBD5E1;`.
   - Kombinasi tersebut membuat tabel terisolasi dengan sudut membulat di bawahnya, menciptakan garis dobel dan celah sebelum footer paginasi.

---

## 2. Tujuan Solusi

1. **Menghilangkan Garis Ganda & Sudut Membulat Bersarang**:
   - Menghapus border dan border-radius pada wrapper tabel di dalam `.adm-table-container`, sehingga tabel menempel rapat dari sisi kiri ke kanan (*flush edge-to-edge*).
   - Menghilangkan `border-bottom` pada baris terakhir tabel (`tbody tr:last-child td`) sehingga tepat bertemu dengan `border-top: 1.5px solid #CBD5E1` milik `.adm-table-footer` sebagai satu garis pemisah tunggal yang solid dan rapi.
2. **Kesesuaian Standar Desain Seragam (Design Parity)**:
   - Tampilan tabel Penempatan akan 100% konsisten dengan tabel Pendaftaran dan Hub Laporan.
3. **Kepatuhan Terhadap Aturan MAGKOM (`AGENTS.md`)**:
   - Styling diselesaikan melalui file CSS Vite (`resources/css/components/tables.css`).
   - Tidak menambahkan CSS atau `<style>` baru di dalam file Blade.
   - Markup Blade diselaraskan secara minimal dan terarah.

---

## 3. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/css/components/tables.css` | **Modify** | Tambahkan aturan CSS penjinak container `.adm-table-container` agar me-reset border, border-radius, dan margin pada `.table-responsive-gentelella`, `.table.jambo_table`, serta menonaktifkan border-bottom pada baris terakhir tabel. |
| `resources/views/admin/penempatan/index.blade.php` | **Modify** | Selaraskan wrapper tabel menjadi `<div style="overflow-x: auto; width: 100%;">` dan kelas tabel menjadi `<table class="adm-table admin-table bulk_action">` agar seragam dengan `pendaftaran` dan `laporan`. |

---

## 4. Langkah Implementasi Terperinci

### Langkah 1: Perbarui CSS di `resources/css/components/tables.css`
Tambahkan aturan berikut pada bagian `.adm-table-container`:
```css
/* Eliminasi Double Border & Nested Rounded Corners di dalam Container Kartu Tabel Admin */
.adm-table-container .table-responsive-gentelella {
    border: none !important;
    border-radius: 0 !important;
    margin: 0 !important;
}

.adm-table-container .table.jambo_table,
.adm-table-container .admin-table,
.adm-table-container .adm-table {
    border: none !important;
    border-radius: 0 !important;
    border-collapse: collapse !important;
    margin-bottom: 0 !important;
}

.adm-table-container table thead th {
    border-top: none !important;
}

.adm-table-container tbody tr:last-child td {
    border-bottom: none !important;
}
```

### Langkah 2: Selaraskan Markup di `resources/views/admin/penempatan/index.blade.php`
- Pada baris 881: Ubah pembungkus tabel:
  ```blade
  {{-- Sebelum --}}
  <div class="table-responsive-gentelella" style="overflow-x: auto;">
      <table class="admin-table table jambo_table table-striped bulk_action">

  {{-- Sesudah --}}
  <div style="overflow-x: auto; width: 100%;">
      <table class="adm-table admin-table bulk_action">
  ```
- Pertahankan seluruh atribut bulk checkbox (`#checkAllPenempatan`, `.penempatan-checkbox`, `toggleAllPenempatan`, `updateBulkUI`), badge status, tombol dropdown aksi, dan modal jalur khusus tanpa perubahan fungsi apapun.

### Langkah 3: Kompilasi Asset & Pengujian
1. Jalankan `npm run build` untuk memproses bundle Vite asset CSS/JS.
2. Jalankan test otomatis terkait tabel admin:
   ```bash
   php artisan test tests/Feature/AdminTablePaginationStandardTest.php tests/Feature/AdminLaporanDanArsipTest.php
   ```
3. Jalankan pengecekan format kode Laravel Pint:
   ```bash
   ./vendor/bin/pint --test
   ```

---

## 5. Rencana Pengujian & Kriteria Keberhasilan

- [x] Tidak ada lagi garis dobel di sisi kiri, kanan, atas, maupun bawah tabel Penempatan.
- [x] Garis antara baris tabel paling bawah dan footer paginasi menyatu menjadi satu garis pembatas horizontal tunggal `#CBD5E1`.
- [x] Sudut melengkung tabel mengikuti kontainer kartu utama (`border-radius: 12px;`), tanpa ada sudut lengkung 8px yang aneh di dalam tabel.
- [x] Fitur checkbox bulk action dan tombol aksi (Detail, Surat, Dropdown) tetap berfungsi normal 100%.
- [x] Tampilan responsif pada layar laptop/HP tetap aman tanpa meluap (*overflow-x: auto*).
- [x] Seluruh pengujian otomatis berstatus hijau (PASS).

