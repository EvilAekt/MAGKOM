# Plan: Audit Menyeluruh dan Penyelarasan Fitur Arsip Peserta Magang (`/admin/arsip-peserta`)

## 1. Ringkasan Eksekutif & Karakteristik Fitur
Fitur **Arsip Peserta** (`/admin/arsip-peserta`) merupakan bank data historis terpadu yang menyimpan seluruh rekam jejak alumni magang di Dinas Komunikasi dan Informatika Kabupaten Sleman.
Sesuai prinsip perancangan sistem (*Bank Data Strictly Read-Only*):
- Data arsip mencakup 3 status penempatan purna:
  1. `StatusPenempatanEnum::Selesai` (peserta yang menyelesaikan seluruh masa bimbingan dinas).
  2. `StatusPenempatanEnum::Nonaktif` (akun peserta yang telah melewati masa retensi dan dinonaktifkan permanen).
  3. `StatusPenempatanEnum::MengundurkanDiri` (peserta yang menghentikan magang sebelum periode selesai).
- Fitur ini bersifat *read-only* bagi admin untuk mencegah manipulasi data historis, evaluasi tugas, maupun dokumen resmi alumni.

## 2. Keterhubungan Lintas Modul (System Connectivity)
1. **Modul Penempatan & Rekam Jejak**:
   - Menghubungkan data alumni dengan bidang dinas penempatan, pembimbing lapangan, dan masa bimbingan.
2. **Modul Tugas, Progres, & Logbook**:
   - Menampilkan total beban kerja yang pernah diselesaikan alumni selama dinas serta rekam jejak presensi logbook harian.
3. **Modul Hub Laporan & Ekspor Data (`/admin/laporan`)**:
   - Tombol "Ekspor Laporan" di hero banner terhubung langsung ke Hub Laporan Rekapitulasi Data Magang (`/admin/laporan?tab=peserta`).
4. **Modul Berkas Resmi (Surat Penerimaan & Surat Selesai)**:
   - Terhubung dengan generator Surat Penerimaan PDF (`admin.penempatan.surat-diterima`).
   - Dapat diperkaya dengan tombol unduh Surat Keterangan Selesai Magang PDF (`admin.penempatan.surat-selesai.unduh`) jika peserta telah memiliki dokumen terbit.
5. **Modul Audit Trail (`AuditService`)**:
   - Setiap akses ke detail arsip alumni dicatat transaksional ke tabel `audit_log` dengan tipe `AksesData`.

## 3. Temuan Hasil Audit Logika & Ruang Penyempurnaan
1. **Ketahanan Sanitasi Pencarian (`ArsipPesertaService`)**:
   - Input `search` pada filter query saat ini belum melewati `trim()`. Jika pengguna menginput spasi ekstra di awal/akhir, pencarian dapat menghasilkan 0 data. Perlu ditambahkan `trim((string) $request->input('search'))`.
2. **Dukungan Filter Status & Tahun yang Kuat**:
   - Memastikan filter tahun dan status arsip bekerja mulus pada SQLite (environment testing) maupun MySQL (environment production).
3. **Integrasi Dokumen Surat Selesai di Halaman Detail**:
   - Pada `admin/arsip/show.blade.php`, jika alumni berstatus `Selesai` dan memiliki `file_surat_selesai`, sediakan tombol akses cepat untuk mengunduh Surat Selesai Magang PDF selain Surat Diterima PDF.
4. **Cakupan Pengujian End-to-End**:
   - Buat test suite komprehensif baru `tests/Feature/AdminArsipPesertaKonektivitasEndToEndTest.php` yang memverifikasi seluruh alur:
     - Tinjauan metrik 4 kartu arsip.
     - Filter pencarian teks dengan spasi, filter tahun, dan filter status.
     - Paginasi 10 entri per halaman.
     - Inspeksi detail arsip dengan relasi tugas, logbook, dan dokumen pendaftaran.
     - Pencatatan jejak audit log akses data.
     - Penolakan akses untuk role non-admin (Peserta dan Pembimbing).
     - Penolakan penempatan yang masih aktif / belum berstatus arsip (HTTP 404).

## 4. Rencana Implementasi Bertahap
- **Langkah 1**: Perkuat sanitasi input pada `app/Services/ArsipPesertaService.php` dengan menambahkan `trim()` pada parameter `search`.
- **Langkah 2**: Ajukan usulan penyempurnaan markup Blade `resources/views/admin/arsip/show.blade.php` untuk menampilkan tombol unduh Surat Selesai PDF jika berkas tersedia.
- **Langkah 3**: Buat automated test suite komprehensif `tests/Feature/AdminArsipPesertaKonektivitasEndToEndTest.php`.
- **Langkah 4**: Jalankan verifikasi quality gates (PHPUnit, Pint, PHPStan, Vite build).

## 5. Kriteria Keberhasilan (Acceptance Criteria)
- Seluruh pengujian fitur arsip peserta lulus 100%.
- PHPStan menganalisis tanpa error (`no errors found`).
- Pint formatting lolos (`passed`).
- Seluruh mutasi & akses data tercatat di Audit Trail.

