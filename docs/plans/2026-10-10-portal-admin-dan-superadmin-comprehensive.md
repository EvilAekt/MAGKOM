# Plan: Penyempurnaan Komprehensif Portal Admin & SuperAdmin (/admin/* & /superadmin/*)

- **Tanggal**: 2026-10-10
- **Status**: Selesai / Terverifikasi
- **Author**: Antigravity (web-implementation)

---

## 1. Tujuan
Menyusun rencana kerja teknis terpadu untuk menyempurnakan, menstandardisasi, dan menyelesaikan seluruh fungsionalitas **Portal Admin & SuperAdmin** (`/admin/*` dan `/superadmin/*`) sesuai dengan dokumen spesifikasi bisnis [01-PRD-MagKom.md](file:///g:/MAGKOM/docs/01-PRD-MagKom.md), arsitektur [02-TDD-MagKom.md](file:///g:/MAGKOM/docs/02-TDD-MagKom.md), roadmap [03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md), dan standar desain [07-DESIGN-SYSTEM-MAGKOM.md](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md).

Cakupan 4 area utama:
1. **Verifikasi Pendaftaran & Berkas (`/admin/pendaftaran`)**: Tinjau dokumen calon peserta langsung dengan **In-App Document Preview Modal** (tanpa perlu membuka tab baru), panel keputusan verifikasi 3-tab (Terima & Tempatkan, Minta Perbaikan, Tolak), dan filter pendaftaran.
2. **Manajemen Kuota & Waiting List (`/admin/kuota` & `/admin/waiting-list`)**: Pengaturan alokasi kuota per jurusan dan bulan dengan penjagaan aturan bisnis atomik (`jumlah >= terisi`), visualisasi okupansi, antrean FIFO waiting list, aksi promosi peserta, dan penutupan massal antrean saat periode magang dimulai.
3. **Penempatan Pembimbing (`/admin/penempatan`)**: Distribusi peserta ke pembimbing lapangan dengan pemantauan beban kerja mentee, mutasi pindah pembimbing, input penempatan manual jalur khusus (F-06/F-07), cetak/pratinjau Surat Keterangan Diterima resmi, dan pembatalan penempatan.
4. **Master Data & Konfigurasi (SuperAdmin - `/superadmin/*`)**: Kelola periode bulan magang (tambah bulan baru & override status yang saat ini belum memiliki modal UI), slot khusus internal instansi, akun staf dinas & reset password dengan flag `must_change_password`, master jurusan & bidang, rubrik penilaian dengan penegakan total bobot 100%, parameter konfigurasi sistem, dan visualizer Jejak Audit Log Trail append-only.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi:
1. Seluruh operasi mutasi data sensitif (persetujuan kuota, pembatalan, reset password, audit log) dieksekusi di dalam transaksi database atomik (`DB::transaction`) dan dicatat melalui `AuditService`.
2. Akun Admin Verifikator dan SuperAdmin beroperasi menggunakan layout bersama [`resources/views/layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php) yang mematuhi token desain MagKom (Blue 700 `#0641CC`, Blue 600 `#0751FF`, Neutral `#F5F7FB`, font Open Sans).
3. Berkas fisik PDF terunggah tersimpan di disk `private` dan disajikan ke modal pratinjau verifikator menggunakan relative signed URL (`signed:relative`) 5 menit yang aman dari isu cross-origin framing (`X-Frame-Options: SAMEORIGIN`).

### Pertanyaan Terbuka:
- Tidak ada pertanyaan terbuka yang menghambat implementasi. Seluruh aturan bisnis (BR-01 s/d BR-17) telah terdokumentasi lengkap di PRD.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Arsitektur:

| Area | Opsi Terpilih | Alasan & Keunggulan |
| :--- | :--- | :--- |
| **Pratinjau Berkas Verifikator Admin** | **In-App Modal Preview dengan Safe Iframe Streaming** (Sama dengan Portal Peserta) | Efisiensi kerja verifikator meningkat drastis: verifikator dapat meninjau 4 berkas calon peserta (CV, Surat Permohonan, Transkrip, Proposal) secara instan di dalam halaman yang sama tanpa harus membuka tab browser terpisah berulang kali. |
| **Manajemen Bulan Magang SuperAdmin** | **Formulir Modal Tambah & Edit Periode dengan Validasi Kalender** | Menambahkan modal pembuatan bulan baru dan edit override status memungkinkan SuperAdmin membuat jadwal bulan mulai, tanggal buka, tanggal tutup, dan override status ketersediaan tanpa perlu akses database langsung. |
| **Antrean Waiting List Admin** | **Tabel FIFO dengan Aksi Promosi Langsung (Quick Placement)** | Admin dapat langsung mempromosikan calon peserta waiting list teratas ke status Diterima ketika terdapat kuota kosong (misal akibat pembatalan peserta lain), lengkap dengan modal penetapan pembimbing lapangan. |
| **Visualisasi Audit Log Trail** | **JSON Diff Inspector Modal** | Data `nilai_lama` dan `nilai_baru` pada audit log ditampilkan dalam format perbandingan nilai sebelum vs sesudah yang mudah dibaca oleh auditor SuperAdmin. |

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/admin/pendaftaran/show.blade.php`](file:///g:/MAGKOM/resources/views/admin/pendaftaran/show.blade.php) | Modify | Mengintegrasikan komponen In-App Document Preview Modal (iframe viewer + zoom/tab baru + escape key handler) untuk berkas verifikasi. |
| [`resources/views/admin/pendaftaran/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/pendaftaran/index.blade.php) | Modify | Menyempurnakan filter toolbar pencarian, filter status, filter bulan, dan styling badge sesuai Design System. |
| [`resources/views/admin/kuota/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/kuota/index.blade.php) | Modify | Menambahkan progress bar okupansi kuota per jurusan (persentase terisi vs kapasitas) bergradasi warna. |
| [`resources/views/admin/waiting-list/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/waiting-list/index.blade.php) | Modify | Menambahkan aksi "Promosikan" jika kuota jurusan terkait tersedia, modal penetapan pembimbing, dan tombol penutupan antrean awal bulan. |
| [`resources/views/admin/penempatan/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/penempatan/index.blade.php) | Modify | Menyempurnakan modal input penempatan manual (jalur khusus), modal pindah pembimbing, dan integrasi in-app preview Surat Diterima. |
| [`app/Http/Controllers/Admin/PenempatanController.php`](file:///g:/MAGKOM/app/Http/Controllers/Admin/PenempatanController.php) | Modify | Menyesuaikan query slot khusus aktif berdasarkan `bulan_mulai` dan memuat relasi mentee aktif pembimbing. |
| [`resources/views/superadmin/bulan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/bulan/index.blade.php) | Modify | **Menambahkan Modal Tambah Bulan Magang** dan **Modal Edit Periode / Override Status** yang sebelumnya hilang dari view. |
| [`resources/views/superadmin/audit-log/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/audit-log/index.blade.php) | Modify | Mempercantik modal inspeksi JSON perbandingan nilai lama vs nilai baru untuk audit trail. |
| [`tests/Feature/AdminSuperAdminPortalTest.php`](file:///g:/MAGKOM/tests/Feature/AdminSuperAdminPortalTest.php) | Create | Feature test komprehensif untuk memverifikasi alur verifikasi berkas, waiting list promotion, kuota lock, dan CRUD master data SuperAdmin. |

---

## 5. Langkah Implementasi
*Catatan: Seluruh checklist di bawah ini telah dieksekusi dan diverifikasi oleh skill `web-implementation`.*

### Modul 1: Verifikasi Pendaftaran & Berkas (`/admin/pendaftaran`)
- [x] **Langkah 1.1**: Perbarui [`resources/views/admin/pendaftaran/show.blade.php`](file:///g:/MAGKOM/resources/views/admin/pendaftaran/show.blade.php):
  - Tambahkan komponen markup `#docPreviewModal` dan JavaScript handler `openDocPreview(previewUrl, downloadUrl, title, mime)`.
  - Ubah tombol "Tinjau Berkas" pada setiap baris dokumen menjadi trigger tombol `openDocPreview()` in-app viewer.
  - Sempurnakan tampilan panel 3-tab keputusan (Terima & Tempatkan, Minta Perbaikan, Tolak) dengan validasi form feedback.
- [x] **Langkah 1.2**: Sempurnakan [`resources/views/admin/pendaftaran/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/pendaftaran/index.blade.php) dengan search bar nama/institusi, filter dropdown status & bulan magang yang reaktif, dan pagination bersih.

### Modul 2: Manajemen Kuota & Waiting List (`/admin/kuota` & `/admin/waiting-list`)
- [x] **Langkah 2.1**: Audit dan poles [`resources/views/admin/kuota/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/kuota/index.blade.php):
  - Pastikan modal Tambah Kuota dan Edit Kuota memvalidasi batas kapasitas bulan (`kuota_umum_maks_per_bulan`) dan aturan `jumlah >= terisi`.
  - Tambahkan progress bar okupansi kuota per jurusan (persentase terisi vs kapasitas).
- [x] **Langkah 2.2**: Perbarui [`resources/views/admin/waiting-list/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/waiting-list/index.blade.php):
  - Tampilkan indikator status sisa kuota jurusan terkait secara real-time.
  - Tambahkan tombol aksi cepat "Promosikan" yang membuka modal pemilihan pembimbing jika kuota kosong tersedia.
  - Perjelas status tombol "Tutup Antrean Massal Awal Bulan" dengan tooltip penjelasan aturan tanggal.

### Modul 3: Penempatan Pembimbing (`/admin/penempatan`)
- [x] **Langkah 3.1**: Perbarui [`resources/views/admin/penempatan/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/penempatan/index.blade.php):
  - Tampilkan indikator beban kerja pembimbing lapangan (`mentee_aktif / maks_beban`).
  - Sempurnakan modal "Input Penempatan Manual Jalur Khusus" (F-06/F-07) dengan dropdown slot khusus aktif dan validasi form.
  - Integrasikan in-app modal pratinjau Surat Keterangan Diterima langsung di tabel penempatan.
  - Sempurnakan modal konfirmasi pembatalan penempatan / pengunduran diri sesuai aturan tanggal mulai (BR-07).

### Modul 4: Master Data & Konfigurasi (SuperAdmin - `/superadmin/*`)
- [x] **Langkah 4.1**: Perbarui [`resources/views/superadmin/bulan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/bulan/index.blade.php):
  - **Buat Modal Tambah Bulan Magang**: input Bulan Mulai (YYYY-MM), Tanggal Buka Pendaftaran, Tanggal Tutup Pendaftaran, dan pilihan Override Status (Otomatis / Tersedia / Tutup / Segera).
  - **Buat Modal Edit Periode & Override Status**: tombol edit pada tabel yang mengisi otomatis data bulan untuk diperbarui.
- [x] **Langkah 4.2**: Poles [`resources/views/superadmin/audit-log/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/audit-log/index.blade.php) dengan modal visualizer inspeksi JSON yang rapi (membedakan nilai lama dan nilai baru dengan highlight visual).
- [x] **Langkah 4.3**: Verifikasi kelengkapan modal dan feedback pada halaman `slot-khusus`, `staf`, `jurusan`, `bidang`, `rubrik`, dan `pengaturan` agar 100% konsisten dengan Design System MagKom.

### Quality Gate & Verifikasi
- [x] **Langkah 5.1**: Buat dan jalankan test suite komprehensif [`tests/Feature/AdminSuperAdminPortalTest.php`](file:///g:/MAGKOM/tests/Feature/AdminSuperAdminPortalTest.php) untuk memvalidasi:
  - Verifikasi pendaftaran dan in-app preview dokumen verifikator.
  - Penambahan dan pembatasan kuota magang.
  - Alur waiting list promotion dan penutupan antrean massal.
  - Penempatan manual jalur khusus dan pindah pembimbing.
  - CRUD Bulan Magang (tambah, edit override status, toggle aktif) dan Master Data SuperAdmin.
- [x] **Langkah 5.2**: Verifikasi visual langsung via browser di portal `/admin/*` dan `/superadmin/*` (termasuk screenshot modal preview, modal slot khusus, modal bulan magang, dan audit JSON inspector).

---

## 6. Strategi Testing

### Automated Feature Test:
- `test_admin_can_access_pendaftaran_index_and_detail_with_preview_links`: Memastikan halaman detail verifikasi pendaftaran memuat data lengkap dan URL pratinjau dokumen.
- `test_admin_decision_flow_terima_tolak_revisi`: Memastikan aksi Terima & Tempatkan mengurangi kuota secara atomik, Tolak mengubah status ke ditolak, dan Perlu Perbaikan menyimpan catatan revisi verifikator.
- `test_admin_cannot_reduce_quota_below_filled_count`: Memastikan kuota tidak bisa diturunkan di bawah jumlah yang sudah terisi.
- `test_admin_waiting_list_promotion_and_bulk_closure`: Memastikan penutupan massal waiting list menolak sisa antrean dengan catatan resmi saat periode magang dimulai.
- `test_superadmin_can_create_and_update_bulan_magang`: Memastikan pembuatan bulan magang baru dan pengubahan override status berfungsi via form request.
- `test_superadmin_can_inspect_audit_logs`: Memastikan log aktivitas tercatat di tabel audit trail dan dapat diakses read-only oleh SuperAdmin.

### UI & Manual Verification:
1. Login sebagai `admin@magkom.go.id` (`Password123!`):
   - Buka `/admin/pendaftaran/2`: Klik tombol **Tinjau Berkas** pada CV calon peserta. Modal in-app PDF viewer terbuka instan dan menampilkan berkas tanpa error.
   - Buka `/admin/kuota`: Visualisasi bar kuota per jurusan dan modal penyesuaian kuota berfungsi normal.
   - Buka `/admin/waiting-list`: Tabel antrean FIFO dan tombol aksi promosi/tinjau tampil rapi.
   - Buka `/admin/penempatan`: Tabel penempatan, modal penempatan manual jalur khusus, dan tombol pratinjau Surat Diterima siap digunakan.
2. Login sebagai `superadmin@magkom.go.id` (`Password123!`):
   - Buka `/superadmin/bulan`: Tombol **Tambah Periode Bulan** dan modal edit override status berfungsi penuh.
   - Buka `/superadmin/audit-log`: Tombol inspeksi menampilkan modal JSON viewer dengan visualisasi nilai lama vs nilai baru.

---

## 7. Risiko & Mitigasi

- **Risiko Race Condition Kuota**:
  - *Mitigasi*: Pengurangan kuota pada aksi Terima & Tempatkan telah dikunci dengan `lockForUpdate()` di dalam `PlacementService` dan `QuotaAllocationService`.
- **Risiko Cross-Origin Frame Iframe**:
  - *Mitigasi*: Gunakan relative signed URL (`signed:relative`) untuk seluruh pratinjau dokumen privat di portal admin, menghilangkan risiko benturan host `127.0.0.1` vs `localhost`.
- **Risiko Keamanan Aksesibilitas Role**:
  - *Mitigasi*: Seluruh rute `/admin/*` dilindungi middleware `role:admin,superadmin` dan rute `/superadmin/*` dilindungi middleware `role:superadmin` murni.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Seluruh checklist langkah implementasi tercentang `- [x]`.
- [x] Admin verifikator dapat meninjau dokumen pendaftaran langsung di dalam modal website (`/admin/pendaftaran/{id}`).
- [x] SuperAdmin dapat menambah dan mengubah periode bulan magang via modal UI (`/superadmin/bulan`).
- [x] Seluruh menu Admin dan SuperAdmin tampil rapi, profesional, dan selaras dengan Design System MagKom.
- [x] Seluruh automated feature tests di `AdminSuperAdminPortalTest` dan suite pengujian eksisting lulus 100% green (22 passed, 112 assertions).
- [x] Tidak ada error di browser console.

---

## 9. Catatan Perubahan & Saran Lanjutan

- **Perubahan selama pengerjaan**:
  - Menyesuaikan query `$slotKhususList` pada `PenempatanController` dan `resources/views/admin/penempatan/index.blade.php` agar menggunakan kolom skema yang valid (`bulan_mulai`, `jumlah`, `terisi`, `sisa`) menggantikan asumsi kolom `nama_mitra`.
  - Memastikan modal In-App Document Preview di `/admin/pendaftaran/show.blade.php` menggunakan safe relative signed URL dan lifecycle listener dinamis untuk kenyamanan verifikator.
- **Saran Lanjutan (Out of Scope)**:
  - Ekspor laporan rekapitulasi data pendaftaran ke format spreadsheet Excel/CSV (Rilis 2 / F-17).
