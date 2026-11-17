# Plan: Penyederhanaan Kartu Daftar Tugas Peserta (Hanya Menampilkan Judul)

Dokumen ini merinci rencana teknis penyederhanaan kartu tugas pada halaman indeks portal Peserta (`resources/views/peserta/tugas/index.blade.php`). Berdasarkan permintaan pengguna dan tangkapan layar antarmuka terbaru, kartu tugas di daftar utama akan disederhanakan agar **hanya menampilkan judul tugas** (beserta metadata tenggat, tag kategori, dan tombol navigasi), sedangkan rincian deskripsi/petunjuk pengerjaan lengkap sepenuhnya diakses di dalam halaman detail tugas (`peserta.tugas.show`).

---

## 1. Tujuan
1. Menghilangkan elemen deskripsi (`.assignment-desc`) dan garis pembatas (`.assignment-divider`) dari kartu daftar tugas peserta di `resources/views/peserta/tugas/index.blade.php`.
2. Menyajikan kartu daftar tugas yang bersih, ringkas, dan proporsional (serupa dengan pola antarmuka Google Classroom di sisi pembimbing), di mana konten utama kartu berfokus pada **Judul Tugas**.
3. Memastikan seluruh rincian petunjuk tugas yang kaya format (bold, italic, list, tautan acuan, berkas lampiran) tetap tersaji lengkap dan aman di halaman detail tugas (`peserta.tugas.show`).

---

## 2. Asumsi & Analisis Antarmuka

### Asumsi Kerja:
1. **Pola Kartu Daftar (Overview vs Detail):**
   Pada kartu daftar tugas (index), peserta membutuhkan pemindaian cepat terhadap tugas apa saja yang harus dikerjakan, tenggat waktu, dan statusnya. Menyertakan paragraf instruksi panjang dan daftar berpoin di dalam kartu membuat ukuran kartu membengkak dan tata letak daftar menjadi tidak seragam.
2. **Kesesuaian dengan Sisi Pembimbing:**
   Pada daftar tugas pembimbing (`resources/views/pembimbing/tugas/index.blade.php`), kartu/baris tugas juga hanya memuat ikon status, judul penugasan, tenggat waktu, dan badge status tanpa menampilkan uraian deskripsi. Perubahan ini menciptakan konsistensi pola desain antarmuka (*UI consistency*) antara modul pembimbing dan modul peserta.
3. **Detail Tetap Utuh:**
   Halaman detail tugas (`resources/views/peserta/tugas/show.blade.php`) sudah dilengkapi dengan panel instruksi lengkap (`.classroom-instruction-body`) bersanitasi `SafeHtmlSanitizer`, tombol acuan, dan pengunduhan berkas panduan.

---

## 3. Pendekatan Teknis & Evaluasi Alternatif

### Perbandingan Opsi:

| Kriteria | Opsi 1: Hapus Deskripsi & Divider dari Kartu (Direkomendasikan) | Opsi 2: Sembunyikan Menggunakan CSS (`display: none`) |
| :--- | :--- | :--- |
| **Deskripsi** | Menghapus blok `<div class="assignment-desc">` dan `<div class="assignment-divider">` langsung dari markup Blade `peserta/tugas/index.blade.php`. | Mempertahankan markup di Blade tetapi menyembunyikannya melalui CSS `.assignment-desc { display: none; }`. |
| **Kelebihan** | - DOM HTML lebih bersih dan ringan.<br>- Tidak merender HTML yang tidak dipakai di halaman.<br>- Ukuran payload HTML berkurang.<br>- Solusi definitif dan bersih. | Mudah dikembalikan jika ingin dimunculkan lagi di kemudian hari. |
| **Kekurangan** | Memerlukan sedikit penyesuaian test assertion pada test suite. | Mengotori DOM dengan markup tersembunyi yang tidak pernah dibaca peserta di halaman list. |
| **Rekomendasi** | **Dipilih (Opsi 1)** karena bersih secara arsitektural dan menghasilkan markup yang optimal. | Tidak direkomendasikan. |

### Penyesuaian Tata Letak Visual (Layout Spacing):
Setelah `.assignment-desc` dan `.assignment-divider` dihapus:
- Header baris kartu (`.assignment-header-row`) yang berisi Judul Tugas dan Tenggat Waktu akan langsung diikuti oleh Footer kartu (`.assignment-footer`) yang berisi Tag Kategori dan tombol "Buka Tugas".
- Pada `resources/css/pages/peserta/tugas-index.css`, margin bawah `.assignment-header-row` disesuaikan dari `3px` menjadi `10px` agar terdapat jarak vertikal yang proporsional dan elegan antara baris judul dan baris footer.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/tugas/index.blade.php` | **Modify** | Menghapus kontainer `.assignment-desc` dan garis pembatas `.assignment-divider` sehingga kartu hanya memuat judul, tenggat, tag, dan tombol buka tugas. |
| `resources/css/pages/peserta/tugas-index.css` | **Modify** | Menyesuaikan `margin-bottom` pada `.assignment-header-row` (menjadi `10px`) untuk spasi optimal antara judul dan footer kartu tanpa pembatas garis. |
| `tests/Feature/TugasDeskripsiSecurityTest.php` | **Modify** | Memperbarui test assertion pada `peserta.tugas.index` untuk memastikan judul tugas tampil, deskripsi tidak dicetak di kartu list, dan rincian deskripsi tetap tampil aman di `peserta.tugas.show`. |

---

## 5. Langkah Implementasi Bertahap

- [x] **Langkah 1: Update Markup Blade `resources/views/peserta/tugas/index.blade.php`**
  - Menghapus kontainer `.assignment-desc` dan `.assignment-divider` sehingga kartu hanya memuat judul, tenggat, tag, dan tombol aksi buka tugas.

- [x] **Langkah 2: Penyesuaian Spacing di `resources/css/pages/peserta/tugas-index.css`**
  - Mengubah `.assignment-header-row` `margin-bottom` menjadi `10px` agar spasi judul dan footer kartu proporsional.

- [x] **Langkah 3: Kompilasi Aset Frontend**
  - Menjalankan `npm run build` sukses (0 errors, 3.35s).

- [x] **Langkah 4: Pembaruan & Verifikasi Test Suite**
  - Memperbarui `test_peserta_tugas_index_shows_only_title_and_details_remain_in_show_view()` pada `tests/Feature/TugasDeskripsiSecurityTest.php`.
  - Menjalankan test suite:
    - `php artisan test --filter=TugasDeskripsiSecurityTest`: 8 passed (66 assertions).
    - `php artisan test --filter=PesertaClassroomTugasLogbookTest`: 7 passed (52 assertions).
    - `php artisan test --filter=PesertaTugasFilterTabsTest`: 2 passed (21 assertions).

- [x] **Langkah 5: Quality Gate & Formatting**
  - `./vendor/bin/pint --test tests/Feature/TugasDeskripsiSecurityTest.php`: PASSED.
  - `./vendor/bin/phpstan analyse tests/Feature/TugasDeskripsiSecurityTest.php`: 0 errors.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Kartu tugas pada halaman daftar tugas peserta hanya menampilkan Judul, Tenggat, Tag Kategori, dan Tombol Buka Tugas.
- [x] Deskripsi / petunjuk teknis tidak lagi muncul di halaman daftar utama.
- [x] Halaman detail tugas (`peserta.tugas.show`) tetap menampilkan instruksi lengkap secara utuh dan aman.
- [x] Jarak vertikal kartu tetap proporsional dan rapi.
- [x] Kompilasi aset Vite sukses (0 errors).
- [x] Seluruh test suite terkait lulus hijau (`PASS`).
