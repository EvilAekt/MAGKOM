# Rencana Kerja: Penyempurnaan Tampilan UI, Tata Letak Metrik, dan Penyelarasan Ekspor/Paginasi Modul Laporan

Dokumen rencana kerja ini disusun untuk memenuhi 5 poin penyelarasan antarmuka dan konsistensi fitur pada modul **Laporan & Ekspor Data** Administrasi (`admin/laporan`) sesuai arahan visual dan tangkapan layar yang diberikan pengguna.

---

## 1. Tujuan
1. **Hero Header Banner (Poin 1 / Gambar 1 & Gambar 2)**:
   - Menggantikan judul teks polos *"Hub Laporan & Ekspor Data"* (Gambar 1) dengan komponen kartu **Signature Navy Blue Hero Banner** (`.adm-hero-banner`) yang konsisten dengan halaman Pendaftaran dan Penempatan (Gambar 2).
   - Menampilkan judul *"Hub Laporan & Ekspor Data"* beserta subjudul kedinasan *"Rekapitulasi Pendaftaran, Penempatan & Arsip Magang • Dinas Komunikasi dan Informatika Kabupaten Sleman"*.
2. **Penyelarasan Kartu Metrik 4 Kolom Sejajar (Poin 2 / Gambar 3)**:
   - Mengubah tata letak grid metrik KPI (`.adm-laporan-kpi-grid` pada `resources/css/components/cards.css`) dari 3 kolom (`repeat(3, 1fr)`) menjadi 4 kolom sejajar dalam satu baris (`repeat(4, 1fr)`).
   - Menghilangkan kartu ke-4 yang sebelumnya turun ke baris baru, sehingga ke-4 kartu (*Total Pengajuan, Peserta Diterima, Penempatan Aktif, Magang Selesai*) tersusun rapi dan simetris dalam satu baris horizontal.
3. **Pembersihan Info Penghitung Duplikat (Poin 3 / Gambar 4)**:
   - Menghapus baris teks info redundan *"Menampilkan 1–4 dari 4 data rekapitulasi"* di atas tabel (`.adm-table-meta-bar`) pada Tab Rekap Pendaftaran dan Tab Data Penempatan.
   - Informasi jumlah data tetap tersaji secara terstandarisasi di bagian bawah tabel melalui komponen `<x-gentelella.pagination-bar>`.
4. **Penyelarasan Ekspor Data Penempatan (Poin 4 / Gambar 5)**:
   - Menggantikan 2 tombol ekspor manual yang terpisah (*Ekspor Excel* hijau dan *Ekspor CSV* putih pada Gambar 5) pada Tab Data Penempatan dengan komponen **Unified Export Popover** (`.adm-export-dropdown-wrap`) seperti yang sudah diterapkan pada Tab Rekap Pendaftaran.
   - Menambahkan dukungan ekspor format Dokumen Resmi PDF pada method `exportPenempatan()` di `ExportReportService` dengan memanfaatkan view template kedinasan yang sudah tersedia (`resources/views/pdf/rekap-penempatan.blade.php`).
5. **Paginasi Dinamis Data Penempatan (Poin 5)**:
   - Menggantikan pagination link bawaan `{{ $penempatanData->links() }}` dengan komponen `<x-gentelella.pagination-bar>` di pojok kanan bawah tabel penempatan dengan opsi pemilih per halaman `5, 10, 25` data.
   - Memperbarui controller `LaporanController` agar memproses parameter `per_page` untuk tab penempatan secara dinamis dan aman.

---

## 2. Asumsi & Batasan
- **Desain & Konsistensi**: Menggunakan design tokens dan kelas CSS yang sudah ada (`.adm-hero-banner`, `.adm-laporan-kpi-grid`, `.adm-export-dropdown-wrap`, `<x-gentelella.pagination-bar>`).
- **Aturan Blade Purity**: Tidak ada inline `<style>` atau inline `<script>` baru yang dimasukkan ke Blade; styling tetap berada di `resources/css/components/cards.css` dan interaksi popover menggunakan event listener terstruktur.
- **Zero Breaking Changes**: Kontrak parameter filter URL aktif (`bulan_magang_id`, `status`, `jurusan_id`, `jalur`, `dari_tanggal`, `sampai_tanggal`) tetap dipertahankan dan disalin secara otomatis ke URL ekspor.

---

## 3. Pendekatan Teknis & Evaluasi Alternatif

| Aspek | Alternatif A: Penyelarasan Penuh & Terpadu (Rekomendasi) | Alternatif B: Perubahan Parsial di View Saja |
| :--- | :--- | :--- |
| **Hero Banner** | Gunakan kartu signature navy banner terpadu tanpa `@section('page_title')` sehingga tidak ada teks ganda. | Menyisipkan kartu di bawah page_title bawaan layout sehingga menghasilkan judul ganda. |
| **Grid Metrik** | Ubah rule `.adm-laporan-kpi-grid` di `resources/css/components/cards.css` menjadi `grid-template-columns: repeat(4, minmax(0, 1fr))` dengan responsive breakpoint tablet (2 kolom) & mobile (1 kolom). | Tambahkan inline style `grid-template-columns: repeat(4, 1fr)` di Blade (melanggar aturan Blade read-only / styling rules). |
| **Ekspor Penempatan** | Gunakan Unified Export Popover dengan 3 format (XLSX, CSV, PDF) terintegrasi ke `ExportReportService`. | Tetap gunakan tombol terpisah atau hanya ganti warna tombol tanpa dropdown popover terpadu. |
| **Paginasi Penempatan** | Integrasikan `<x-gentelella.pagination-bar>` dengan parameter `per_page` dinamis di controller. | Hanya pagination bawaan Laravel tanpa opsi 5, 10, 25. |
| **Keputusan** | **Dipilih Alternatif A**: Memberikan konsistensi antarmuka 100% identik antara Tab Pendaftaran dan Tab Penempatan, tata letak rapi, dan sesuai standar desain sistem MAGKOM. | Ditolak karena menyisakan inkonsistensi antarmuka dan melanggar aturan arsitektur. |

---

## 4. Rencana Perubahan Berkas

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/css/components/cards.css` | **Modify** | Ubah `.adm-laporan-kpi-grid` dari 3 kolom menjadi 4 kolom sejajar (`repeat(4, minmax(0, 1fr))`), sesuaikan breakpoint tablet ke `repeat(2, 1fr)`. |
| `resources/views/admin/laporan/index.blade.php` | **Modify** | 1. Hapus `@section('page_title')` dan tambahkan `.adm-hero-banner` di bagian atas konten.<br>2. Hapus teks redundan `Menampilkan ... data` di atas tabel pendaftaran & penempatan.<br>3. Ganti tombol ekspor penempatan manual dengan Unified Export Popover (Excel, CSV, PDF).<br>4. Ganti footer pagination penempatan dengan `<x-gentelella.pagination-bar :perPageOptions="[5, 10, 25]">`.<br>5. Tambahkan interaksi JavaScript popover penempatan di section script. |
| `app/Http/Controllers/Admin/LaporanController.php` | **Modify** | Dukung pembacaan `$request->input('per_page', 5)` dengan whitelist `[5, 10, 25, 50]` pada tab penempatan. |
| `app/Services/ExportReportService.php` | **Modify** | Tambahkan penanganan format `pdf` pada `exportPenempatan()` menggunakan view `pdf.rekap-penempatan`. |
| `tests/Feature/AdminLaporanDanArsipTest.php` | **Modify** | Tambahkan verifikasi test untuk: 1) paginasi dinamis per_page penempatan, 2) ekspor PDF penempatan, 3) keberadaan hero banner dan unified export penempatan. |

---

## 5. Langkah-Langkah Implementasi Bertahap

- [x] **Fase 1: Backend & Service Ekspor PDF Penempatan**
  - [x] 1.1 Perbarui `app/Services/ExportReportService.php` pada method `exportPenempatan()` untuk menangani `$format === 'pdf'` dengan memuat view `pdf.rekap-penempatan`.
  - [x] 1.2 Perbarui `app/Http/Controllers/Admin/LaporanController.php` pada tab `penempatan` agar membaca parameter query `per_page` (default 5, opsi valid: 5, 10, 25, 50) dengan `$penQuery->paginate($perPage)->withQueryString()`.

- [x] **Fase 2: Penyesuaian CSS Grid Metrik KPI**
  - [x] 2.1 Edit `resources/css/components/cards.css` pada rule `.adm-laporan-kpi-grid`:
    - Ubah `grid-template-columns: repeat(3, minmax(0, 1fr)) !important;` menjadi `repeat(4, minmax(0, 1fr)) !important;`.
    - Atur media query `@media (max-width: 1200px)` menjadi `repeat(2, minmax(0, 1fr)) !important;`.
    - Atur media query `@media (max-width: 640px)` menjadi `1fr !important;`.

- [x] **Fase 3: Antarmuka Blade `admin/laporan/index.blade.php`**
  - [x] 3.1 Kosongkan `@section('page_title', '')` agar layout tidak mencetak teks polos *"Hub Laporan & Ekspor Data"*, lalu tambahkan markup `.adm-hero-banner` di awal `@section('content')`.
  - [x] 3.2 Hapus teks redundan `Menampilkan 1–x dari y data rekapitulasi / penempatan` pada `.adm-table-meta-bar` di Tab Pendaftaran dan Tab Penempatan.
  - [x] 3.3 Ganti tombol ekspor Gambar 5 di Tab Penempatan dengan **Unified Export Popover Penempatan** (Excel, CSV, PDF).
  - [x] 3.4 Ganti `{{ $penempatanData->links() }}` di Tab Penempatan dengan `<x-gentelella.pagination-bar :paginator="$penempatanData" :perPageOptions="[5, 10, 25]" entity="data penempatan" badgeText="Penempatan Magang" />`.
  - [x] 3.5 Tambahkan fungsi JavaScript untuk toggle dan trigger ekspor popover penempatan (`toggleAdmExportPopoverPenempatan`, `handleAdmExportPenempatanFormatChange`, `triggerPenempatanExport`).

- [x] **Fase 4: Verifikasi & Quality Gates**
  - [x] 4.1 Perbarui & jalankan test di `tests/Feature/AdminLaporanDanArsipTest.php` dan `tests/Feature/AdminEksporLaporanXlsxTest.php`.
  - [x] 4.2 Jalankan PHPUnit penuh: `vendor/bin/phpunit` (wajib 100% lulus).
  - [x] 4.3 Jalankan Laravel Pint: `vendor/bin/pint --test` (wajib format rapi).
  - [x] 4.4 Jalankan Vite Build: `npm run build` (wajib sukses).

---

## 6. Strategi Testing
1. **Uji Tampilan & Hero Banner**:
   - Membuka `/admin/laporan` menampilkan kartu hero banner dengan judul *"Hub Laporan & Ekspor Data"* dan subjudul resmi Sleman.
   - Memastikan tidak ada teks judul ganda di atas hero banner.
2. **Uji 4 Kartu Metrik Sejajar**:
   - Memastikan 4 kartu KPI (*Total Pengajuan, Peserta Diterima, Penempatan Aktif, Magang Selesai*) terender dalam 1 baris di layar desktop.
3. **Uji Hapus Teks Redundan**:
   - Memastikan teks redundan di atas tabel telah bersih dan info jumlah baris hanya tampil di komponen paginasi bawah.
4. **Uji Ekspor Penempatan**:
   - Ekspor Penempatan via Popover format XLSX mengembalikan file `.xlsx`.
   - Ekspor Penempatan format CSV mengembalikan stream `.csv`.
   - Ekspor Penempatan format PDF mengembalikan file `.pdf`.
5. **Uji Paginasi Dinamis Penempatan**:
   - Mengakses `/admin/laporan?tab=penempatan&per_page=5` membatasi data per halaman menjadi 5 baris.
   - Paginasi selector `[5, 10, 25]` berfungsi normal mengubah tampilan tabel.

---

## 7. Kriteria Selesai (Definition of Done)
- [x] Kartu Signature Navy Hero Banner terpasang menggantikan judul polos.
- [x] Ke-4 kartu KPI metrik tersusun sejajar 4 kolom dalam satu baris.
- [x] Teks redundan penghitung data di atas tabel telah dihilangkan.
- [x] Tab Penempatan menggunakan Unified Export Popover (Excel, CSV, PDF) yang konsisten dengan Tab Pendaftaran.
- [x] Pojok kanan bawah tabel penempatan menampilkan selector `5, 10, 25` dan paginasi berfungsi dinamis.
- [x] Seluruh test PHPUnit lulus 100% (0 error, 0 failure).
- [x] Laravel Pint dan Vite build lolos tanpa kendala.

