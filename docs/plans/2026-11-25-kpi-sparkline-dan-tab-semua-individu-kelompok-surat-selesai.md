# Rencana Kerja: Penambahan KPI Card Bergrafik (Sparkline) & Tab Pilihan Semua / Individu / Kelompok pada Surat Selesai Magang

**Tanggal:** 2026-11-25  
**Target Branch:** `test-2`  
**Cakupan:** Halaman Daftar Surat Selesai Magang (`admin.surat-selesai.index`) — `SuratSelesaiController`, `index.blade.php`, dan `surat-selesai-index.css`

---

## 1. Tujuan
Menyelaraskan halaman daftar utama **Surat Selesai Magang** (`admin.surat-selesai.index`) dengan pola visual halaman **Verifikasi Berkas Pendaftaran Peserta** (`admin.pendaftaran.index`):
1. Menambahkan baris **4 KPI Card bergrafik mini bar (*sparkline*)** di bawah hero banner (hanya tampil pada mode daftar utama Tier 1).
2. Mengubah tab kategori dari 2 pilihan (`Peserta Individu`, `Rombongan Kelompok`) menjadi **3 pilihan kategori** bergaya segmented pill bar (`.adm-category-nav-bar`):
   - **Semua** (`tab=semua`, default — menampilkan daftar gabungan Peserta Individu & Rombongan Kelompok)
   - **Peserta Individu** (`tab=individu`)
   - **Rombongan Kelompok** (`tab=kelompok`)

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi Kerja Logis:**
  1. **KPI Card (4 Kartu):** Menggunakan komponen Blade eksisting `<x-gentelella.tile-stat>` dengan `:sparkBars` seperti di `admin/pendaftaran/index.blade.php`, menampilkan metrik real-time dari penempatan berstatus `Selesai`:
     - **Total Selesai** (`color="blue"`): Total entri individu + rombongan kelompok yang berstatus `Selesai`.
     - **Belum Diunggah** (`color="yellow"`): Jumlah entri (individu + kelompok) yang belum memiliki `file_surat_selesai`.
     - **Surat Sudah Terbit** (`color="green"`): Jumlah entri (individu + kelompok) yang sudah memiliki `file_surat_selesai`.
     - **Total Peserta Selesai** (`color="red"` atau `"blue"`): Total seluruh individu + seluruh anggota dalam rombongan kelompok yang berstatus `Selesai`.
  2. **Tab `Semua` (Default):** Ketika `tab` tidak diisi atau `tab=semua`, halaman menampilkan daftar gabungan antara entri **Peserta Individu** dan **Rombongan Kelompok** (diurutkan terbaru), lengkap dengan badge kategori (`Individu` / `Kelompok`) di tiap baris. Mengklik baris individu tetap membuka `?tab=individu&masuk_individu={id}`, dan mengklik baris kelompok tetap membuka `?tab=kelompok&masuk_kelompok={pendaftaran_id}`.
  3. **Kompatibilitas Pencarian & Filter:** Pencarian teks (`q`) dan filter status surat (`status_surat`: `Semua Status`, `Sudah Terbit`, `Belum Diunggah`) tetap bekerja pada ketiga tab (`semua`, `individu`, `kelompok`).
  4. **Persetujuan Perubahan Blade:** Sesuai aturan proyek (`.agents/rules/AGENTS.md` bagian 3.3), perubahan markup di `resources/views/admin/surat-selesai/index.blade.php` memerlukan persetujuan eksplisit pengguna sebelum eksekusi.

---

## 3. Pendekatan Teknis

### Perbandingan Opsi Implementasi Tab `Semua`
1. **Opsi A (Direkomendasikan): Agregasi Terstruktur di `SuratSelesaiController@index` + Pagination Manual (`LengthAwarePaginator`) untuk Tab `semua`**
   - Pada `tab=semua`, controller mengambil daftar `Penempatan` individu selesai dan `Pendaftaran` kelompok selesai (dengan eager loading yang sama dan filter `q` serta `status_surat` yang sama), menormalkan keduanya ke koleksi item seragam (`type => 'individu'|'kelompok'`, `sort_date`, `model`), mengurutkan descending berdasarkan tanggal/ID, lalu mem-paginate dengan `LengthAwarePaginator` (`perPage = 10`).
   - Pada `tab=individu` dan `tab=kelompok`, query paginasi Eloquent eksisting tetap dipertahankan tanpa perubahan perilaku.
   - **Kelebihan:** Setiap rombongan kelompok tetap tampil sebagai 1 baris kelompok (konsisten dengan tab `Rombongan Kelompok`), bukan terpecah per anggota; seluruh test eksisting tetap kompatibel; tidak ada perubahan skema database.
   - **Kekurangan:** Untuk tab `semua`, penggabungan dua query dilakukan di memory sebelum paginasi (aman untuk skala ratusan data magang; tetap memakai eager loading sehingga bebas N+1).

2. **Opsi B: Mengubah Tab `Semua` Menjadi Daftar Mentah `Penempatan` (Menggabungkan Anggota Kelompok sebagai Baris Terpisah)**
   - **Kelebihan:** Hanya 1 query Eloquent.
   - **Kekurangan:** Membingungkan admin karena 1 kelompok muncul sebagai 3–5 baris terpisah di tab `Semua`, berbeda dengan perilaku tab `Rombongan Kelompok`.

**Keputusan:** Memilih **Opsi A** agar representasi individu (per orang) dan kelompok (per tim) konsisten di semua tab.

---

## 4. Perubahan File

| File | Aksi (`Create`/`Modify`/`Delete`) | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Http/Controllers/Admin/SuratSelesaiController.php` | Modify | Mendukung `$activeTab` bernilai `'semua'` (default), `'individu'`, dan `'kelompok'`; menghitung `$kpiStats` (`total_entri`, `belum_diunggah`, `sudah_terbit`, `total_peserta_selesai`) dan `$totalSemua`; menyusun daftar gabungan terpaginasi saat `$activeTab === 'semua'`. |
| `resources/views/admin/surat-selesai/index.blade.php` | Modify | Pada mode daftar utama (`empty($masukPenempatan) && empty($masukPendaftaran)`): tambahkan 4 `<x-gentelella.tile-stat>` bergrafik *sparkline*, ubah toolbar tab menjadi 3 pilihan (`Semua`, `Peserta Individu`, `Rombongan Kelompok`) bergaya `.adm-category-nav-bar`, serta tambahkan rendering panel daftar gabungan untuk `$activeTab === 'semua'`. |
| `resources/css/pages/admin/surat-selesai-index.css` | Modify | Menambahkan styling scoped `.adm-tile-count-grid` & `.adm-category-nav-bar` (konsisten dengan `pendaftaran-index.css`) serta badge kategori `Individu` / `Kelompok` pada daftar tab `Semua`. |
| `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php` | Modify | Menambahkan skenario pengujian KPI card bergrafik, tab `Semua`, filter `q` & `status_surat` di tab `Semua`, serta memastikan tab `Peserta Individu` dan `Rombongan Kelompok` tetap terfilter akurat. |

---

## 5. Langkah Implementasi

- [x] **Persetujuan Perubahan Blade**: Memastikan persetujuan eksplisit user atas perubahan markup di `resources/views/admin/surat-selesai/index.blade.php`.
- [x] **Update `app/Http/Controllers/Admin/SuratSelesaiController.php`**:
  - Ubah default `$activeTab` menjadi `'semua'` dan izinkan `['semua', 'individu', 'kelompok']`.
  - Hitung `$totalSemua = $totalIndividu + $totalKelompok` dan `$kpiStats` (`belum_diunggah`, `sudah_terbit`, `total_peserta_selesai`, serta array `sparkBars` dinamis atau proporsional).
  - Tambahkan cabang `$activeTab === 'semua'` yang menggabungkan hasil query individu & kelompok (termasuk filter `q` dan `status_surat`) menjadi `LengthAwarePaginator`.
  - Teruskan `totalSemua` dan `kpiStats` ke view `admin.surat-selesai.index`.
- [x] **Update `resources/views/admin/surat-selesai/index.blade.php`**:
  - Di bawah hero banner (saat mode daftar utama Tier 1), tambahkan `<div class="row tile_count adm-tile-count-grid">` berisi 4 `<x-gentelella.tile-stat ... :sparkBars="..." />`.
  - Ganti strip tab 2-tombol menjadi `<div class="adm-category-nav-bar" role="tablist">` dengan 3 tombol: `Semua` (`tab=semua`), `Peserta Individu` (`tab=individu`), dan `Rombongan Kelompok` (`tab=kelompok`) lengkap dengan badge angka masing-masing.
  - Tambahkan blok `@if($activeTab === 'semua')` yang merender form pencarian/filter dan daftar baris gabungan (menampilkan badge kategori + link fokus ke `?tab=individu&masuk_individu=...` atau `?tab=kelompok&masuk_kelompok=...`).
- [x] **Update `resources/css/pages/admin/surat-selesai-index.css`**:
  - Tambahkan rule `.adm-tile-count-grid` (grid 4 kolom desktop, 2 kolom tablet, 1 kolom mobile) dan `.adm-category-nav-bar` / `.adm-category-nav-item` / `.adm-category-nav-badge` agar identik dengan halaman Verifikasi Berkas tanpa bergantung pada `pendaftaran-index.css`.
- [x] **Update Feature Test**:
  - Tambahkan test pada `tests/Feature/AdminSuratSelesaiIndividuKelompokTest.php` untuk memverifikasi 4 KPI card bergrafik, 3 tab (`Semua`, `Peserta Individu`, `Rombongan Kelompok`), serta isi daftar gabungan pada tab `Semua`.
- [x] **Quality Gate & Verifikasi**:
  - Jalankan `npm run build`, `php artisan test --filter=AdminSuratSelesai`, dan `./vendor/bin/pint --test` pada file PHP yang diubah.

---

## 6. Strategi Testing
- **Feature Test (`php artisan test --filter=AdminSuratSelesai`):**
  - *Happy Path:* Mengakses `admin.surat-selesai.index` menampilkan 4 KPI card (`Total Selesai`, `Belum Diunggah`, `Surat Sudah Terbit`, `Total Peserta Selesai`), 3 tombol tab (`Semua`, `Peserta Individu`, `Rombongan Kelompok`), serta daftar gabungan individu dan kelompok di tab `Semua`.
  - *Filter & Pencarian:* Parameter `q` dan `status_surat` bekerja pada tab `semua`, `individu`, dan `kelompok`.
  - *Regression Check:* Seluruh skenario fokus individu, fokus kelompok, upload, download, orphan cleanup, dan RBAC tetap 100% lulus.
- **Asset Build & Code Style:**
  - `npm run build` dan `./vendor/bin/pint --test` lulus.

---

## 7. Risiko & Mitigasi
- **Risiko 1 — Test Eksisting yang Mengakses `route('admin.surat-selesai.index')` Tanpa Parameter `tab`:**  
  Karena default `tab` berubah menjadi `semua` yang menampilkan gabungan individu + kelompok, test yang mengharapkan hanya individu di URL root harus tetap aman.  
  *Mitigasi:* Pada tab `semua`, peserta individu tetap tampil beserta link `tab=individu&masuk_individu=...`, sehingga assertion eksisting (`assertSee('Siti Nurhaliza')`, `assertSee('Rahmat Hidayat')`, `assertSee('Peserta Individu')`, `assertSee('Rombongan Kelompok')`) tetap terpenuhi.
- **Risiko 2 — CSS `.adm-tile-count-grid` atau `.adm-category-nav-bar` Tidak Termuat di Halaman Surat Selesai:**  
  Halaman `surat-selesai/index.blade.php` hanya memuat `surat-selesai-index.css` (bukan `pendaftaran-index.css`).  
  *Mitigasi:* Menambahkan rule `.adm-tile-count-grid` dan `.adm-category-nav-bar` langsung di `resources/css/pages/admin/surat-selesai-index.css` dan menjalankan `npm run build`.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] 4 KPI card bergrafik (*sparkline*) tampil di halaman daftar utama Surat Selesai Magang.
- [x] Tersedia 3 tombol pilihan tab: **Semua**, **Peserta Individu**, dan **Rombongan Kelompok** dengan badge jumlah masing-masing.
- [x] Tab **Semua** menampilkan gabungan daftar individu & kelompok dengan pencarian dan filter status surat yang berfungsi normal.
- [x] `npm run build`, `php artisan test --filter=AdminSuratSelesai`, dan `./vendor/bin/pint --test` seluruhnya lulus.
