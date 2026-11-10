# Plan: Standardisasi Card Footer Paginasi Tabel Admin (Opsi 1: Integrated Table Card Footer)

**Tanggal**: 04 November 2026  
**Status**: Selesai Terverifikasi  
**Referensi**: Permintaan User & Tangkapan Layar `media_1793805445363_35a4269b.png`, Aturan `AGENTS.md`.  
**Keputusan Desain**: Pengguna memilih **Opsi 1** (Paginasi menyatu rapi sebagai kaki kartu tabel di dalam `.adm-table-container`).

---

## 1. Analisis Masalah & Kebutuhan

1. **Masalah Elemen Paginasi "Melayang Tanpa Wadah Kartu"**:
   - Pada halaman Hub Laporan ([`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php)), blok navigasi paginasi dan pengatur jumlah data per halaman (`x-gentelella.pagination-bar`) saat ini diletakkan di **luar** kontainer kartu tabel (`.adm-table-container`).
   - Akibatnya, seperti terlihat pada tangkapan layar `media_1793805445363_35a4269b.png`, area paginasi tampak terputus, mengambang bebas di bawah tabel hanya dengan garis tipis, dan tidak memiliki batas kartu (*card boundary*) yang kokoh.
2. **Ketiadaan Kelas Standar Footer Tabel**:
   - Di halaman Pendaftaran, Penempatan, dan Waiting List, beberapa pembungkus footer menggunakan *inline style* ad-hoc seperti `style="padding: 1rem 1.25rem; border-top: 1px solid #E2E8F0; background: #ffffff;"`.
   - Diperlukan satu kelas standar CSS resmi `.adm-table-footer` yang terdefinisi di `resources/css/components/tables.css` dengan dukungan kontras mode terang dan mode gelap (*dark mode parity*).
3. **Penyelarasan Menyeluruh (Global Admin Parity)**:
   - Sesuai permintaan pengguna: *"semua di bagian admin kalo ada yang pake seperti itu di buat yaa"*, penyelarasan ini akan diterapkan pada seluruh modul tabel admin yang memiliki navigasi data.

---

## 2. Tujuan Solusi

1. **Menyatukan Paginasi Menjadi Satu Kesatuan Kartu Tabel (*Integrated Table Card*)**:
   - Setiap tabel admin akan dibungkus oleh satu kartu kontainer kokoh (`.adm-table-container`) dengan garis tepi berbobot 1.5px (`#CBD5E1`) dan sudut melengkung halus (`border-radius: 12px`):
     - **Atas**: `.adm-table-meta-bar` (Informasi rentang data aktif & filter).
     - **Tengah**: `.adm-table` (Baris data bergaris rapi dan sejajar presisi).
     - **Bawah (Footer)**: `.adm-table-footer` (Latar belakang lembut `#F8FAFC` dibatasi `border-top: 1.5px solid #CBD5E1`, membungkus komponen paginasi dan pilihan per-page).
2. **Standarisasi CSS Terpusat di `resources/css/components/tables.css`**:
   - Membuat kelas `.adm-table-footer` resmi:
     ```css
     .adm-table-footer {
         padding: 0.875rem 1.25rem;
         background: #F8FAFC;
         border-top: 1.5px solid #CBD5E1;
     }
     [data-theme="dark"] .adm-table-footer {
         background: #0E1726;
         border-top-color: #26354D;
     }
     .adm-table-footer .kuota-pagination-wrapper {
         margin-top: 0 !important;
         padding-top: 0 !important;
         border-top: none !important;
     }
     ```
   - Mengeliminasi garis dobel dan margin berlebih di dalam footer.
3. **Kepatuhan Aturan MAGKOM (`AGENTS.md`)**:
   - Zero CSS in Blade; seluruh styling berada di file CSS Vite.
   - Tidak mengubah kontrak logic, URL query parameter (`per_page`, `page`), maupun alur sistem.

---

## 3. Evaluasi Pendekatan Arsitektur

| Parameter | Opsi 1: Integrated Table Card Footer (Terpilih) | Opsi 2: Standalone Floating Card |
| :--- | :--- | :--- |
| **Keterpaduan Visual** | **Sangat Tinggi**: Tabel dan paginasi berada dalam 1 kartu utuh berbingkai 1.5px. | Sedang: Terbagi menjadi 2 kartu bertumpuk dengan celah kosong di antaranya. |
| **Kesesuaian Standar Desain** | Mengikuti standar desain modern (Stripe, GitHub Tables, Tailwind UI DataTables). | Terlihat seperti widget terpisah di bawah tabel. |
| **Efisiensi Ruang** | Ringkas, rapi, dan tidak memakan ruang vertikal berlebih. | Membutuhkan ruang vertikal ekstra karena ada jarak antar-kartu. |
| **Kesesuaian Pilihan User** | **100% Sesuai** dengan persetujuan pengguna (*"opsi 1"*). | Ditolak oleh pengguna. |

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/css/components/tables.css` | Modify | Menambahkan kelas utilitas `.adm-table-footer` dengan background lembut `#F8FAFC`, border-top 1.5px `#CBD5E1`, dark mode `#0E1726`, serta reset border/margin untuk `.kuota-pagination-wrapper`. |
| `resources/views/admin/laporan/index.blade.php` | Modify | Memindahkan blok paginasi Tab 1 (Pendaftaran), Tab 2 (Penempatan), dan Tab 4 (Nilai) ke dalam `.adm-table-container` dan membungkusnya dengan `.adm-table-footer`. |
| `resources/views/admin/pendaftaran/index.blade.php` | Modify | Mengganti wrapper inline-style footer menjadi `.adm-table-footer`. |
| `resources/views/admin/penempatan/index.blade.php` | Modify | Memastikan tabel dan paginasi berada dalam `.adm-table-container` terpadu dengan `.adm-table-footer`. |
| `resources/views/admin/waiting-list/index.blade.php` | Modify | Mengganti wrapper inline-style footer menjadi `.adm-table-footer`. |

---

## 5. Langkah Implementasi Bertahap

### Fase 1: Standarisasi CSS `.adm-table-footer` di `resources/css/components/tables.css`
- [x] Tambahkan class `.adm-table-footer` dengan latar belakang `#F8FAFC` dan garis atas `1.5px solid #CBD5E1`.
- [x] Tambahkan aturan dark mode untuk `.adm-table-footer` (`background: #0E1726; border-top-color: #26354D;`).
- [x] Tambahkan aturan reset untuk `.adm-table-footer .kuota-pagination-wrapper` (`margin-top: 0 !important; padding-top: 0 !important; border-top: none !important;`).

### Fase 2: Penyatuan Paginasi ke Dalam Card Tabel di Blade Views
- [x] **`resources/views/admin/laporan/index.blade.php`**:
  - Pada Tab 1 (Pendaftaran): Pindahkan blok `@if($pendaftaranData->isNotEmpty())` ke dalam `.adm-table-container` menggunakan pembungkus `<div class="adm-table-footer">`.
  - Pada Tab 2 (Penempatan): Pindahkan blok `@if($penempatanData->hasPages())` ke dalam `.adm-table-container` menggunakan `<div class="adm-table-footer">`.
  - Pada Tab 4 (Rekap Nilai): Pindahkan blok `@if($nilaiData->hasPages())` ke dalam `.adm-table-container` menggunakan `<div class="adm-table-footer">`.
- [x] **`resources/views/admin/pendaftaran/index.blade.php`**:
  - Ganti wrapper footer paginasi menjadi `<div class="adm-table-footer">`.
- [x] **`resources/views/admin/penempatan/index.blade.php`**:
  - Bungkus tabel dan pagination ke dalam `.adm-table-container` utuh dengan `<div class="adm-table-footer">`.
- [x] **`resources/views/admin/waiting-list/index.blade.php`**:
  - Ganti wrapper footer paginasi menjadi `<div class="adm-table-footer">`.

### Fase 3: Kompilasi & Verifikasi Kualitas
- [x] Jalankan kompilasi Vite: `npm run build`.
- [x] Jalankan automated tests:
  ```bash
  php artisan test tests/Feature/AdminTablePaginationStandardTest.php tests/Feature/AdminLaporanDanArsipTest.php
  ```
- [x] Jalankan linter Pint: `./vendor/bin/pint --test`.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Seluruh area paginasi tabel admin menyatu mulus di dalam kontainer kartu tabel (`.adm-table-container`).
- [x] Area footer paginasi memiliki latar belakang abu-abu sangat lembut (`#F8FAFC`) dengan garis pemisah atas 1.5px (`#CBD5E1`) yang jelas dan tegas.
- [x] Tidak ada lagi paginasi yang mengambang bebas di luar kartu.
- [x] Mode gelap (*dark mode*) tampil kontras dan konsisten.
- [x] `npm run build` berhasil tanpa kendala.
- [x] Seluruh automated test lulus 100% tanpa regresi.

