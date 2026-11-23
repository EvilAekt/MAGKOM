# Rencana Teknis: Penyelarasan Panel dan Tabel Daftar Bulan Magang Selaras dengan Kuota Per Bidang

Dokumen ini disusun berdasarkan skill `web-planning` untuk merencanakan standardisasi arsitektur visual dan struktur container tabel **Daftar Bulan Magang** (`#panel-daftar-bulan`) agar memiliki hierarki, panel header Gentelella, tombol aksi, dan container tabel (`card-table-wrap`) yang identik dan harmonis dengan panel **Kuota per bidang**.

---

## 1. Tujuan

Menyelaraskan struktur dan gaya visual pada tab **Daftar Bulan Magang** (`/admin/kuota?tab=bulan`) dengan tab **Kuota per bidang**:
1. Mengadopsi struktur panel standar Gentelella (`<x-gentelella.x-panel title="Daftar bulan magang">`) dengan tombol aksi header "+ Buat bulan baru" di kanan atas (selaras dengan tombol "+ Tambah kuota").
2. Membungkus tabel dengan container `<div class="card-table-wrap">` ber-radius dan berbayang halus di dalam panel.
3. Menyelaraskan tipografi header tabel, padding sel, dan efek hover baris agar persis sama dengan `.table-bidang`.
4. Merapikan footer paginasi agar teks "Menampilkan X sampai Y dari Z entri" tidak tampil duplikat ketika data berada dalam 1 halaman tunggal (`$daftarBulanPaginator->hasPages()`).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
1. Permintaan *"ini juga di buat chart tabel dong seperti di kuota perbidang di sesuaikan"* mengacu pada hierarki container kartu: outer panel Gentelella bergaris biru (`x_panel`) yang memuat inner `card-table-wrap` untuk tabel data, seperti yang diterapkan pada panel Kuota per bidang.
2. Seluruh atribut interaksi modal yang sudah ada (`data-action="open-buat-bulan"`, `.btn-trigger-edit-bulan`, `.btn-trigger-toggle-bulan`, dsb.) wajib dipertahankan 100% agar interaksi JavaScript di `kuota-index.js` tetap berjalan mulus.
3. Perubahan struktur markup Blade pada `resources/views/admin/kuota/index.blade.php` diperbolehkan setelah mendapatkan konfirmasi persetujuan dari pengguna, sesuai aturan pengecualian markup Blade di **AGENTS.md** Bagian 3.3.

---

## 3. Pendekatan Teknis & Evaluasi Opsi

### Perbandingan Pendekatan:

| Kriteria | Opsi 1: Refactor Menggunakan Komponen `<x-gentelella.x-panel>` & `<div class="card-table-wrap">` (Rekomendasi) | Opsi 2: Manipulasi CSS Semata pada Container Standalone Lama |
|---|---|---|
| **Keselarasan Visual** | **100% Identik**: Memiliki header Gentelella (`x_title`), tombol aksi di slot header, border-top biru, serta inner card tabel yang persis sama dengan Kuota per bidang. | **Sebagian**: Sulit meniru tombol header, toolbox collapsible, dan spacing inner card tanpa markup container standar. |
| **Konsistensi Kode** | Menggunakan komponen Blade standar proyek (`<x-gentelella.x-panel>`), mengurangi kode CSS duplikat. | Mempertahankan CSS terpisah yang redundant dan rawan drift desain. |
| **Stabilitas Interaksi** | Seluruh atribut `data-action` dan kelas tombol dipertahankan utuh. | Tetap mempertahankan selektor lama. |
| **Dukungan Dark Mode** | Otomatis mewarisi styling mode gelap resmi dari komponen `.x_panel` dan `.card-table-wrap`. | Harus merawat CSS manual di `kuota-index.css`. |

**Rekomendasi**: Mengadopsi **Opsi 1** dengan membungkus tab Daftar Bulan Magang ke dalam `<x-gentelella.x-panel>` dan `<div class="card-table-wrap">`, serta memperbarui CSS pendukungnya.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
|---|---|---|
| `resources/views/admin/kuota/index.blade.php` | **Modify** | Mengganti container `.table-bulan-card` dengan `<x-gentelella.x-panel title="Daftar bulan magang">`, menambahkan slot action tombol `Buat bulan baru`, membungkus tabel dalam `<div class="card-table-wrap">`, serta merapikan footer paginasi agar tidak duplikat. |
| `resources/css/pages/admin/kuota-index.css` | **Modify** | Menyelaraskan styling header, hover baris, dan footer tabel bulan di dalam `card-table-wrap`, serta memastikan dukungan Dark Mode tetap sempurna. |
| `docs/plans/2026-11-23-standardisasi-panel-dan-tabel-daftar-bulan-magang.md` | **Create** | Dokumen rencana kerja teknis ini. |

---

## 5. Langkah Implementasi

- [x] **Langkah 1: Penyesuaian Markup Blade di `resources/views/admin/kuota/index.blade.php`**
  - Pada `#panel-daftar-bulan`:
    - Bungkus menggunakan `<x-gentelella.x-panel title="Daftar bulan magang">`.
    - Tambahkan `<x-slot:actions>` berisi tombol aksi `open-buat-bulan` jika bukan SuperAdmin.
    - Bungkus tabel dalam `<div class="card-table-wrap"><div style="overflow-x: auto;"><table class="table-bidang table-bulan-grid">...</div></div>`.
    - Rapikan footer tabel: tampilkan teks ringkasan di kiri, dan paginasi hanya jika `$daftarBulanPaginator->hasPages()` di kanan.
- [x] **Langkah 2: Harmonisasi CSS di `resources/css/pages/admin/kuota-index.css`**
  - Sesuaikan margin/padding footer tabel bulan agar menyatu rapi di dalam `card-table-wrap`.
  - Pastikan style warna header, baris hover, badge override, dan tombol aksi berbaur harmonis baik di Light Mode maupun Dark Mode.
- [x] **Langkah 3: Kompilasi Aset Frontend (Vite Build)**
  - Jalankan `npm run build` untuk memverifikasi kompilasi bundel CSS/JS tanpa error.
- [x] **Langkah 4: Jalankan Quality Gates & Verifikasi Otomatis**
  - Jalankan `php artisan test tests/Feature/AdminKuotaRevampTest.php` dan `tests/Feature/AdminChildPagesVisualTest.php`.
  - Jalankan `vendor/bin/pint --test` dan `vendor/bin/phpstan analyse --memory-limit=1G`.
- [x] **Langkah 5: Verifikasi Browser / Visual Check**
  - Pastikan tampilan tabel Daftar Bulan Magang memiliki header Gentelella, tombol aksi "Buat bulan baru", inner border rounded `card-table-wrap`, dan footer bersih tanpa teks ganda.

---

## 6. Strategi Testing

1. **Uji Fungsional & Interaksi**:
   - Klik tombol "Buat bulan baru" di header panel membuka modal pembuat bulan dengan benar.
   - Klik tombol "Edit" pada baris bulan membuka modal edit dengan data periode yang sesuai.
   - Klik tombol toggle aktif/nonaktif membuka modal konfirmasi dan mengirim permintaan dengan aman.
2. **Uji Regresi Otomatis**:
   - `AdminKuotaRevampTest` (21 tests) lolos 100%.
   - `AdminChildPagesVisualTest` (7 tests) lolos 100%.
   - `AdminSuperAdminPortalTest` (8 tests) lolos 100%.
3. **Uji Kualitas Kode**:
   - Pint dan PHPStan lolos tanpa pelanggaran (0 error).

---

## 7. Risiko & Mitigasi

- **Risiko**: Selector JavaScript untuk modal bulan (`open-buat-bulan`, `.btn-trigger-edit-bulan`) terganggu jika atribut hilang saat refactor markup.  
  **Mitigasi**: Seluruh atribut data (`data-action="open-buat-bulan"`, `data-id`, `data-action-url`, dsb.) dan kelas CSS interaksi dipertahankan persis sama.
- **Risiko**: Duplikasi teks footer jika paginasi Laravel tidak memiliki penjaga kondisi.  
  **Mitigasi**: Gunakan `@if($daftarBulanPaginator->hasPages()) {{ $daftarBulanPaginator->links() }} @endif` sehingga navigasi halaman hanya dirender saat jumlah data melebihi 1 halaman.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Tab Daftar Bulan Magang menggunakan struktur `<x-gentelella.x-panel>` bergaris biru atas dengan tombol "+ Buat bulan baru" di header panel.
- [x] Tabel periode magang berada di dalam `<div class="card-table-wrap">` dengan border-radius 12px dan gaya visual yang serasi dengan tabel Kuota per bidang.
- [x] Teks footer jumlah entri tidak lagi tampil duplikat saat data kurang dari atau sama dengan 10 entri.
- [x] Seluruh tombol aksi (Buat bulan, Edit, Aktifkan/Nonaktifkan) tetap berfungsi normal 100%.
- [x] `npm run build` sukses, Pint lulus, PHPStan lulus, dan seluruh unit/feature test lulus hijau (PASS).

