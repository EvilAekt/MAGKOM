# Plan: Modernisasi Dashboard Jalur B (Admin), Jalur C (Pembimbing & Peserta Aktif), dan Jalur D (SuperAdmin)

- **Tanggal**: 2026-10-09
- **Status**: Selesai (Completed & Verified)
- **Author**: Antigravity (web-implementation)
- **Dokumen Acuan**: 
  - [`01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (Bab 4, 8, 9, 10, 11)
  - [`07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md)
  - [`layouts/dashboard.blade.php`](file:///g:/MAGKOM/resources/views/layouts/dashboard.blade.php)

---

## 1. Tujuan
Menyelesaikan seluruh sisa antarmuka web (Frontend Blade UI) pada portal MagKom dengan mengadopsi Design System MagKom token-based secara menyeluruh ke dalam 3 kluster besar:
1. **Jalur B (Admin Dinas)**: Verifikasi Berkas Calon Peserta dengan split-screen/preview dokumen & modal keputusan atomik (`admin.pendaftaran.show`), Antrean Waiting List dengan FIFO & aksi tutup antrean massal (`admin.waiting-list.index`), dan Manajemen Penempatan Peserta dengan pengalihan pembimbing & pembatalan penempatan (`admin.penempatan.index`).
2. **Jalur C (Pelaksanaan Magang Aktif: Peserta & Pembimbing)**: Logbook Harian Peserta dengan aturan batasan mundur 3 hari & lampiran PDF (`peserta.logbook.index`), Reviu Logbook Pembimbing (`pembimbing.logbook.index`), Manajemen & Reviu Progres Tugas Proyek (`peserta.tugas.show` & `pembimbing.tugas.show`), serta Form Penilaian 5 Rubrik Kompetensi Pembimbing & Unduh Lembar Nilai PDF (`pembimbing.penilaian.show` & `peserta.nilai.show`).
3. **Jalur D (Tata Kelola SuperAdmin)**: Modernisasi tampilan CRUD Staf Internal (`superadmin.staf.index`), Slot Khusus (`superadmin.slot-khusus.index`), Master Jurusan, Bidang Dinas, Rubrik Penilaian, Parameter Pengaturan Sistem, dan Penampil Jejak Audit Trail (`superadmin.audit-log.index`).

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi**:
  1. Seluruh controller yang sebelumnya mengembalikan `JsonResponse` (seperti `LogbookController`, `LogbookReviewController`, `PenilaianController`, `TugasController`) disesuaikan agar mengembalikan `View` Blade jika request datang dari web browser (`!$request->expectsJson()`), dan tetap menyediakan `JsonResponse` jika diminta via API/AJAX.
  2. Dokumen privat calon peserta ditinjau admin menggunakan temporary signed URL 5 menit (`route('berkas.download')`).
  3. Desain split-screen verifikasi berkas membagi layar menjadi: panel kiri (profil calon peserta & berkas lampiran) dan panel kanan (formulir keputusan keputusan: *Terima*, *Perlu Perbaikan*, *Tolak*).
  4. Seluruh form mutasi dilindungi token CSRF dan modal konfirmasi untuk aksi destruktif (tolak, batalkan, nonaktifkan).
- **Pertanyaan Terbuka**: Tidak ada ambiguitas kritis. Logika bisnis di backend service (`PlacementService`, `ScoringService`, `MasaTenggangService`, `AuditService`) telah lulus 100% pada pengujian UAT.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Pendekatan UI:

| Aspek | Opsi 1: Halaman Form & Tabel Terpisah-pisah | Opsi 2: Antarmuka Terpadu Komponen Design System (Terpilih) |
| :--- | :--- | :--- |
| **Pola Verifikasi Admin** | Admin membuka dokumen di tab baru lalu kembali ke halaman form lain untuk mengisi keputusan. | **Split-Screen Reviewer Card**: Metadata peserta dan daftar berkas di sebelah kiri, form keputusan langsung di sebelah kanan dalam 1 viewport. |
| **Pola Logbook & Tugas** | Menggunakan reload halaman penuh untuk setiap submit progres. | **Interactive Card & Modal**: Kalender status logbook dan kartu progres tugas dengan indikator keterlambatan runtime (BR-16). |
| **Pola Master SuperAdmin** | Menulis kode HTML tabel mentah per menu. | **Standard Data-Table Card**: Menggunakan pola layout tabel terpadu dengan chip badge status, modal tambah/edit data, dan tombol toggle status aktif. |

- **Opsi Terpilih**: **Opsi 2 (Antarmuka Terpadu Komponen Design System)**.
- **Alasan**: Menghilangkan gesekan kerja (*cognitive load*) staf verifikator dan pembimbing lapangan, meminimalisir kesalahan klik, serta menjaga konsistensi visual 100% terhadap Figma & PRD.

---

## 4. Perubahan File

### A. Jalur B (Admin)
| File Path | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`resources/views/admin/pendaftaran/show.blade.php`](file:///g:/MAGKOM/resources/views/admin/pendaftaran/show.blade.php) | **Create** | Halaman split-screen verifikasi berkas: Ringkasan profil, tabel berkas dengan tombol preview/unduh, dan panel aksi keputusan (*Terima* dengan penugasan pembimbing & bidang, *Perlu Perbaikan* dengan catatan wajib, *Tolak*). |
| [`resources/views/admin/waiting-list/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/waiting-list/index.blade.php) | **Modify** | Upgrade dari stub ke layout dashboard: Tabel antrean urutan FIFO, status antrean, dan tombol/modal tutup antrean massal awal bulan (`AC-04-7`). |
| [`resources/views/admin/penempatan/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/penempatan/index.blade.php) | **Modify** | Upgrade dari stub ke layout dashboard: Tabel penempatan peserta aktif/selesai, tombol ganti pembimbing beban kerja (BR-13), dan aksi pembatalan/pengunduran diri (BR-07). |
| [`resources/views/admin/kuota/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/kuota/index.blade.php) | **Create** | Halaman manajemen kuota umum per jurusan: Tabel alokasi kuota per bulan, modal tambah kuota, dan validasi anti-penurunan kuota di bawah `terisi`. |

### B. Jalur C (Pembimbing & Peserta Aktif)
| File Path | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`app/Http/Controllers/Peserta/LogbookController.php`](file:///g:/MAGKOM/app/Http/Controllers/Peserta/LogbookController.php) | **Modify** | Tambahkan return view `peserta.logbook.index` pada method `index()` saat diakses browser. |
| [`app/Http/Controllers/Pembimbing/LogbookReviewController.php`](file:///g:/MAGKOM/app/Http/Controllers/Pembimbing/LogbookReviewController.php) | **Modify** | Tambahkan return view `pembimbing.logbook.index` pada method `index()` saat diakses browser. |
| [`app/Http/Controllers/Pembimbing/PenilaianController.php`](file:///g:/MAGKOM/app/Http/Controllers/Pembimbing/PenilaianController.php) | **Modify** | Tambahkan return view `pembimbing.penilaian.index` dan `pembimbing.penilaian.show`. |
| [`app/Http/Controllers/Peserta/NilaiController.php`](file:///g:/MAGKOM/app/Http/Controllers/Peserta/NilaiController.php) | **Modify** | Tambahkan return view `peserta.nilai.show` saat diakses browser. |
| [`app/Http/Controllers/Peserta/TugasController.php`](file:///g:/MAGKOM/app/Http/Controllers/Peserta/TugasController.php) | **Modify** | Tambahkan return view `peserta.tugas.index` dan `peserta.tugas.show`. |
| [`resources/views/peserta/logbook/index.blade.php`](file:///g:/MAGKOM/resources/views/peserta/logbook/index.blade.php) | **Create** | Halaman logbook peserta: Kalender/daftar logbook, formulir catat kegiatan (validasi mundur maks 3 hari BR-09, unggah PDF maks 2MB, tautan eksternal), dan riwayat reviu pembimbing. |
| [`resources/views/pembimbing/logbook/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/logbook/index.blade.php) | **Create** | Halaman reviu logbook pembimbing: Filter peserta & tanggal, kartu catatan kegiatan peserta, tombol unduh lampiran, dan form reviu (*Terkirim* / *Perlu Revisi*). |
| [`resources/views/pembimbing/tugas/show.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/show.blade.php) | **Create** | Detail penugasan pembimbing: Metadata tugas, indikator deadline runtime (BR-16), riwayat progres peserta, dan form reviu umpan balik (AC-08-4). |
| [`resources/views/peserta/tugas/show.blade.php`](file:///g:/MAGKOM/resources/views/peserta/tugas/show.blade.php) | **Create** | Detail tugas peserta: Deskripsi proyek, tenggat waktu, kartu lapor kemajuan kerja, dan umpan balik pembimbing. |
| [`resources/views/pembimbing/penilaian/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/penilaian/index.blade.php) | **Create** | Daftar peserta bimbingan dengan status evaluasi (*Draft* / *Final* / *Belum Diisi*) dan tombol buka instrumen nilai. |
| [`resources/views/pembimbing/penilaian/show.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/penilaian/show.blade.php) | **Create** | Form instrumen penilaian 5 rubrik kompetensi terbobot (skor 0-100), kalkulator nilai akhir otomatis (predikat A-E), tombol simpan draf / finalisasi, form revisi nilai final (dengan alasan), dan tombol selesai magang (BR-10). |
| [`resources/views/peserta/nilai/show.blade.php`](file:///g:/MAGKOM/resources/views/peserta/nilai/show.blade.php) | **Create** | Lembar evaluasi nilai resmi peserta: Rincian 5 rubrik nilai terbobot, skor akhir & predikat huruf, dan tombol unduh Lembar Nilai PDF resmi. |

### C. Jalur D (SuperAdmin Master Data & Audit)
| File Path | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`resources/views/superadmin/staf/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/staf/index.blade.php) | **Modify** | Upgrade ke dashboard layout: Tabel staf (Admin & Pembimbing), modal tambah staf baru, reset kata sandi staf (`must_change_password`), dan toggle aktif/nonaktif. |
| [`resources/views/superadmin/slot-khusus/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/slot-khusus/index.blade.php) | **Create** | Halaman manajemen slot khusus internal dinas (F-06): Tabel slot, kuota terpakai, modal tambah slot, dan toggle aktif. |
| [`resources/views/superadmin/jurusan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/jurusan/index.blade.php) | **Modify** | Upgrade ke dashboard layout: Master jurusan (SMK & Perguruan Tinggi), penegakan BR-12, modal tambah/edit jurusan, dan toggle aktif. |
| [`resources/views/superadmin/bidang/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/bidang/index.blade.php) | **Modify** | Upgrade ke dashboard layout: Master unit kerja bidang dinas Kominfo Sleman, modal tambah/edit bidang, dan toggle aktif. |
| [`resources/views/superadmin/rubrik/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/rubrik/index.blade.php) | **Modify** | Upgrade ke dashboard layout: Master 5 rubrik penilaian kompetensi, penegakan total bobot 100%, modal tambah/edit kriteria. |
| [`resources/views/superadmin/pengaturan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/pengaturan/index.blade.php) | **Modify** | Upgrade ke dashboard layout: Parameter konfigurasi sistem (masa tenggang, batas logbook mundur, ukuran berkas maks, batas predikat A-E) dengan tombol simpan & auto-cache invalidation. |
| [`resources/views/superadmin/audit-log/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/audit-log/index.blade.php) | **Modify** | Upgrade ke dashboard layout: Penampil jejak audit komprehensif, filter tipe (`auth`, `aksi`, `service`, `akses_data`), filter rentang tanggal, paginasi 25 baris, dan penampil detail JSON perubahan. |

---

## 5. Langkah Implementasi Terstruktur

### Gelombang 1: Jalur B (Admin Verifikasi & Penempatan)
- [x] **Langkah 1.1**: Buat view [`resources/views/admin/pendaftaran/show.blade.php`](file:///g:/MAGKOM/resources/views/admin/pendaftaran/show.blade.php) dengan layout split-screen, preview berkas signed link, modal/panel keputusan (*Terima*, *Perlu Perbaikan*, *Tolak*).
- [x] **Langkah 1.2**: Revamp view [`resources/views/admin/waiting-list/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/waiting-list/index.blade.php) meng-extend layout dashboard dengan tabel FIFO dan modal penutupan antrean massal.
- [x] **Langkah 1.3**: Revamp view [`resources/views/admin/penempatan/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/penempatan/index.blade.php) meng-extend layout dashboard dengan tabel penempatan, modal ganti pembimbing, dan modal pembatalan penempatan.
- [x] **Langkah 1.4**: Buat view [`resources/views/admin/kuota/index.blade.php`](file:///g:/MAGKOM/resources/views/admin/kuota/index.blade.php) untuk manajemen kuota umum per jurusan.

### Gelombang 2: Jalur C (Pelaksanaan Magang & Penilaian)
- [x] **Langkah 2.1**: Update controller pendukung (`Peserta\LogbookController`, `Pembimbing\LogbookReviewController`, `Pembimbing\PenilaianController`, `Peserta\NilaiController`, `Peserta\TugasController`) untuk mereturn View Blade pada browser request.
- [x] **Langkah 2.2**: Buat view [`resources/views/peserta/logbook/index.blade.php`](file:///g:/MAGKOM/resources/views/peserta/logbook/index.blade.php) dengan form pengisian kegiatan, validasi mundur maks 3 hari, unggah lampiran PDF, dan daftar riwayat logbook.
- [x] **Langkah 2.3**: Buat view [`resources/views/pembimbing/logbook/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/logbook/index.blade.php) untuk reviu logbook bimbingan.
- [x] **Langkah 2.4**: Buat view detail tugas [`resources/views/pembimbing/tugas/show.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/show.blade.php) dan [`resources/views/peserta/tugas/show.blade.php`](file:///g:/MAGKOM/resources/views/peserta/tugas/show.blade.php).
- [x] **Langkah 2.5**: Buat view instrumen nilai pembimbing [`resources/views/pembimbing/penilaian/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/penilaian/index.blade.php) dan [`resources/views/pembimbing/penilaian/show.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/penilaian/show.blade.php).
- [x] **Langkah 2.6**: Buat view lembar nilai peserta [`resources/views/peserta/nilai/show.blade.php`](file:///g:/MAGKOM/resources/views/peserta/nilai/show.blade.php) dengan tombol unduh PDF resmi.

### Gelombang 3: Jalur D (SuperAdmin Master Data & Audit Trail)
- [x] **Langkah 3.1**: Revamp view [`resources/views/superadmin/staf/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/staf/index.blade.php) dengan tabel staf, modal tambah staf, reset password, dan toggle status.
- [x] **Langkah 3.2**: Buat view [`resources/views/superadmin/slot-khusus/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/slot-khusus/index.blade.php) untuk kuota internal.
- [x] **Langkah 3.3**: Revamp master views: [`superadmin/jurusan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/jurusan/index.blade.php), [`superadmin/bidang/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/bidang/index.blade.php), dan [`superadmin/rubrik/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/rubrik/index.blade.php).
- [x] **Langkah 3.4**: Revamp view parameter sistem [`resources/views/superadmin/pengaturan/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/pengaturan/index.blade.php).
- [x] **Langkah 3.5**: Revamp view penampil jejak audit [`resources/views/superadmin/audit-log/index.blade.php`](file:///g:/MAGKOM/resources/views/superadmin/audit-log/index.blade.php) dengan filter dan modal inspeksi detail JSON.

### Gelombang 4: Validasi & Pengujian Otomatis
- [x] **Langkah 4.1**: Jalankan `php artisan view:cache` untuk memverifikasi sintaks seluruh template Blade.
- [x] **Langkah 4.2**: Buat dan jalankan test suite fitur `tests/Feature/DashboardModulesRenderingTest.php` untuk memastikan seluruh route menghasilkan status HTTP 200 dengan layout yang benar.
- [x] **Langkah 4.3**: Jalankan verifikasi browser langsung untuk masing-masing peran (Admin, Pembimbing, SuperAdmin).

---

## 6. Strategi Testing
- **Automated Feature Test**:
  - Test render halaman `admin.pendaftaran.show`, `admin.waiting-list.index`, `admin.penempatan.index`.
  - Test render halaman `peserta.logbook.index`, `pembimbing.logbook.index`, `pembimbing.penilaian.show`, `peserta.nilai.show`.
  - Test render halaman `superadmin.staf.index`, `superadmin.jurusan.index`, `superadmin.pengaturan.index`, `superadmin.audit-log.index`.
  - Test eksekusi keputusan admin (*terima*, *tolak*, *perlu_perbaikan*).
- **Manual / Visual Browser Test**:
  - Uji responsive layout pada desktop (1440px) dan ponsel (390px).
  - Pastikan token warna dan kontras WCAG 2.1 AA terpenuhi di semua status badge dan tombol.

---

## 7. Risiko & Mitigasi
- **Beban Template Blade**: Banyak view baru yang dibuat.
  - *Mitigasi*: Manfaatkan komponen layout terpadu `layouts.dashboard` dan token CSS kustom yang sudah terpasang.
- **Dual Response (View vs JSON)**: Memastikan API/automated test lama yang mengandalkan JSON tidak patah.
  - *Mitigasi*: Pertahankan blok `if ($request->expectsJson() || $request->wantsJson()) return response()->json(...)` pada seluruh controller.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Seluruh file view pada Jalur B, Jalur C, dan Jalur D meng-extend `layouts.dashboard`.
- [x] Tidak ada lagi tampilan raw HTML stub (`<!DOCTYPE html>...`) di dalam proyek.
- [x] Kompilasi view `php artisan view:cache` berjalan tanpa error.
- [x] Rangkaian uji otomatis `tests/Feature/DashboardModulesRenderingTest.php` lulus 100% (`PASS`).
- [x] Seluruh 4 akun testing peran (Admin, Pembimbing, SuperAdmin, Peserta) dapat mengakses modul masing-masing tanpa error runtime.

---

## 9. Catatan Perubahan & Saran Lanjutan
- **Perubahan selama pengerjaan**: *(Akan dicatat saat eksekusi)*
- **Saran Lanjutan (Out of Scope)**:
  - Penambahan ekspor tabel data ke format Excel/Spreadsheet pada rilis lanjutan.
