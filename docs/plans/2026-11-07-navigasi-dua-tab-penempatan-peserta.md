# Rencana Kerja: Navigasi Dua Tombol Data Penempatan Peserta (Sudah vs Belum Ditempatkan)

**Tanggal:** 7 November 2026  
**Target File:** 
- `app/Http/Controllers/Admin/PenempatanController.php`
- `resources/views/admin/penempatan/index.blade.php`
- `tests/Feature/AdminChildPagesVisualTest.php`
- `tests/Feature/AdminPenempatanDuaTahapTest.php`

**Tujuan:** Menyediakan antarmuka yang rapi, terstruktur, dan terintegrasi pada halaman Penempatan Peserta Magang (`admin.penempatan.index`) dengan **2 tombol navigasi utama**:
1. **Sudah Ditempatkan**: Menampilkan data peserta yang telah memiliki pembimbing lapangan & bidang dinas.
2. **Belum Ditempatkan**: Menampilkan antrean peserta lolos seleksi berkas (Tahap 1) yang menunggu penetapan pembimbing & bidang dinas.

---

## 1. Analisis Kebutuhan & Kondisi Saat Ini

![Kondisi Tampilan Saat Ini](/C:/Users/ALDI%20BOYS/.gemini/antigravity/brain/8ac85b61-3384-4008-bb69-7f98f2cbd22d/.user_uploaded/media_1794058323195_bc4df2aa.jpg)

### Masalah pada Antarmuka Saat Ini:
1. **Data Terlihat Tercampur / Bertumpuk**: Di halaman saat ini, data penempatan ditampilkan langsung di satu tabel utama, sementara antrean yang belum ditempatkan (jika ada) berada di kartu kuning terpisah di atasnya. Pengguna merasa alur kerja ini kurang terarah dan ingin pemisahan yang jelas.
2. **Kebutuhan Navigasi Cepat**: Admin verifikator membutuhkan tombol navigasi tegas untuk langsung beralih antara:
   - Peserta yang **Sudah Ditempatkan** (fokus monitoring, penilaian, dan mutasi pembimbing).
   - Peserta yang **Belum Ditempatkan** (fokus tindakan *actionable*: memilih pembimbing dan bidang).

---

## 2. Asumsi Kerja & Penyelarasan Desain

1. **Struktur Data Backend MagKom**:
   - **Sudah Ditempatkan**: Record pada tabel `penempatan` (relasi `peserta`, `pembimbing`, `bidang`, status `aktif`, `selesai`, dll.).
   - **Belum Ditempatkan**: Record pada tabel `pendaftaran` dengan `status = 'diterima'` dan `doesntHave('penempatan')` (sudah lolos berkas, belum di-plot).
2. **Integrasi Modal Penetapan**:
   - Pada tab "Belum Ditempatkan", setiap baris peserta memiliki tombol aksi **`+ Tempatkan`** yang memicu modal penetapan pembimbing (`openTetapkanModal`) yang sudah berfungsi dengan baik.
3. **Preservasi URL & Filter State**:
   - Mendukung parameter URL `?tab=sudah` (default) dan `?tab=belum` sehingga dapat diakses langsung, dibagikan tautannya, dan tetap mempertahankan filter pencarian/bulan magang.

---

## 3. Evaluasi Opsi Desain Tata Letak (Layout Architecture)

| Aspek | Opsi A: Segmented Control Toolbar di Atas Tabel (Rekomendasi) | Opsi B: Halaman Terpisah Berbeda Route |
| :--- | :--- | :--- |
| **Deskripsi** | Menempatkan 2 tombol switcher navigasi modern tepat di bawah Hero Stat Cards, bergaya *Segmented Tab Controller* Sleman Navy. | Membuat route baru `/admin/penempatan/belum-ditempatkan`. |
| **UX & Kecepatan** | **Sangat Cepat & Seamless**: Admin dapat berpindah konteks hanya dengan 1 klik tanpa kehilangan konteks dashboard. | Memecah alur kerja menjadi dua halaman berbeda dan membingungkan breadcrumbs. |
| **Konsistensi** | Selaras dengan pola tab di Laporan MagKom dan antrean pendaftaran. | Berpotensi merusak route contract dan test suite existing. |
| **Aksesibilitas** | Memenuhi WCAG 2.1 AA (`role="tablist"`, `role="tab"`, `aria-selected`). | Perlu maintenance ganda template. |

**Rekomendasi:** Gunakan **Opsi A** (Segmented Navigation Toolbar Terpadu).

---

## 4. Desain Antarmuka Terstruktur

### 4.1. Toolbar Navigasi 2 Tombol (Segmented Tabs):
Diletakkan tepat di bawah Hero Stat Cards dan di atas Card Tabel:
```
+---------------------------------------------------------------------------------+
|  [ ✓ Sudah Ditempatkan (3) ]          [ ⏳ Belum Ditempatkan (1) ]              |
+---------------------------------------------------------------------------------+
```
- **Tombol 1: Sudah Ditempatkan**
  - Ikon: User Check / Centang
  - Judul: **Sudah Ditempatkan**
  - Counter Badge: `{{ $penempatanStats['total'] ?? $penempatanList->total() }}`
  - Subteks: *"Peserta magang aktif & riwayat penempatan"*
  - Status Aktif: Latar Biru Sleman (`#0641CC`), teks putih tebal, shadow elevasi halus.
- **Tombol 2: Belum Ditempatkan**
  - Ikon: Jam Pasir / User Alert
  - Judul: **Belum Ditempatkan**
  - Counter Badge: `{{ $penempatanStats['belum_ditempatkan'] ?? 0 }}` (Badge Amber/Oranye cerah jika > 0)
  - Subteks: *"Lolos seleksi berkas, menunggu penetapan pembimbing"*
  - Status Aktif: Latar Biru Sleman / Amber Gold, teks putih/gelap kontras.

---

### 4.2. Tampilan Konten Tab 1: "Sudah Ditempatkan"
1. **Filter Bar**:
   - Pencarian Nama/Email/Kampus (`q`)
   - Dropdown Bulan Magang
   - Dropdown Status Penempatan (`Aktif`, `Selesai`, `Mengundurkan Diri`, `Nonaktif`)
   - Dropdown Pembimbing Lapangan
   - Dropdown Bidang Dinas
   - Tombol `Terapkan Filter` dan `Reset`
2. **Tabel Data Penempatan**:
   - Kolom: `[ ] | PESERTA MAGANG | PEMBIMBING LAPANGAN | JALUR / BIDANG DINAS | BULAN MAGANG | STATUS | AKSI`
   - Aksi: `Detail`, `Surat`, Menu `...` (Pindah Pembimbing, Batalkan/Selesai).
   - Pagination Bar lengkap (5, 10, 25, 50 entri per halaman).

---

### 4.3. Tampilan Konten Tab 2: "Belum Ditempatkan"
1. **Header Banner Informatif**:
   - Kotak informasi lembut: *"Peserta di bawah ini telah disetujui berkasnya pada Tahap 1 dan siap ditetapkan Pembimbing Lapangan serta Bidang Dinas."*
2. **Filter Bar Ringkas**:
   - Pencarian Peserta (`q`)
   - Filter Bulan Magang
3. **Tabel Antrean Menunggu Penetapan**:
   - Kolom:
     1. `NO.`
     2. `CALON PESERTA` (Nama, Email, Avatar Inisial, NIM/NISN)
     3. `INSTITUSI & JURUSAN` (Kampus/Sekolah & Nama Jurusan)
     4. `BULAN MAGANG` (Periode & Durasi)
     5. `REKOMENDASI BIDANG` (Bidang dinas yang sesuai dengan jurusan pendaftar)
     6. `STATUS` (Badge Amber: `• Menunggu Plotting`)
     7. `AKSI` (Tombol Utama: **`+ Tempatkan`** -> membuka modal penetapan pembimbing)
4. **Empty State Khusus Tab Belum Ditempatkan**:
   - Jika antrean bernilai 0: Tampilan kartu kosong dengan ikon centang hijau:
     `Semua Peserta Sudah Ditempatkan`
     *"Tidak ada calon peserta yang menunggu penetapan pembimbing lapangan saat ini."*

---

## 5. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `app/Http/Controllers/Admin/PenempatanController.php` | Modify | Menambahkan penanganan `$activeTab = $request->input('tab', 'sudah')`, memfilter query antrean belum ditempatkan, dan mengoper variabel tab aktif ke view. |
| `resources/views/admin/penempatan/index.blade.php` | Modify | Menambahkan komponen navigasi 2 tombol, membungkus section "Sudah Ditempatkan" dan "Belum Ditempatkan" secara terstruktur, dan merapikan layout. |
| `tests/Feature/AdminPenempatanDuaTahapTest.php` | Modify | Memastikan pengujian alur penetapan dua tahap dan tab navigasi tetap lulus 100%. |
| `tests/Feature/AdminChildPagesVisualTest.php` | Modify | Menambahkan automated test untuk verifikasi tombol navigasi tab dan rendering antrean kedua tab. |

---

## 6. Langkah Implementasi Bertahap

- [x] **Langkah 1**: Perbarui `PenempatanController.php`:
  - Ambil parameter `$tab = $request->input('tab', 'sudah')`.
  - Terapkan filter pencarian `q` dan `bulan_magang_id` juga pada `$antreanBelumDitempatkan`.
  - Teruskan variabel `$activeTab` dan `$penempatanStats` ke view `admin.penempatan.index`.
- [x] **Langkah 2**: Perbarui `resources/views/admin/penempatan/index.blade.php`:
  - Buat container 2 tombol navigasi segmented switcher di bawah Stat Cards (`Sudah Ditempatkan` & `Belum Ditempatkan`).
  - Buat pemisahan panel modular (`#panel-penempatan-sudah` dan `#panel-penempatan-belum`).
  - Sediakan filter bar dan empty state khusus yang elegan pada tab antrean belum ditempatkan.
  - Pasang script Vanilla JS switcher terintegrasi dengan URL sync query param.
  - Pastikan modal `openTetapkanModal` tetap terhubung sempurna pada tombol `+ Tempatkan`.
- [x] **Langkah 3**: Jalankan pemeriksaan linter:
  ```bash
  vendor\bin\pint.bat --test
  ```
- [x] **Langkah 4**: Jalankan test suite PHPUnit:
  ```bash
  php artisan test --filter "AdminPenempatan|AdminChildPagesVisualTest|DashboardModulesRenderingTest"
  ```
- [x] **Langkah 5**: Kompilasi aset Vite:
  ```bash
  npm run build
  ```
- [x] **Langkah 6**: Verifikasi manual & automated visual assertions:
  - Uji tab "Sudah Ditempatkan" dan "Belum Ditempatkan".
  - Uji empty state saat tidak ada antrean.
  - Uji rendering antrean saat ada peserta lolos berkas.

---

## 7. Kriteria Selesai (Definition of Done)

- [x] Terdapat 2 tombol navigasi yang jelas dan kontras: **Sudah Ditempatkan** dan **Belum Ditempatkan** lengkap dengan badge counter jumlah peserta.
- [x] Tab "Sudah Ditempatkan" hanya menampilkan peserta yang sudah memiliki record penempatan beserta filter lengkap dan paginasi.
- [x] Tab "Belum Ditempatkan" menampilkan antrean peserta lolos berkas yang siap di-plot pembimbingnya beserta tombol `+ Tempatkan` dan empty state jika kosong.
- [x] Layout rapi, proporsional, terintegrasi, dan responsif di desktop maupun mobile.
- [x] Pint passed (100% PSR-12).
- [x] PHPUnit Feature Tests passed (26 passed, 174 assertions, 100% green).
- [x] Vite build sukses tanpa warning.

