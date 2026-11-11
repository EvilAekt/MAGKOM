# Catatan Perubahan (Changelog)
Seluruh perubahan penting pada sistem **MagKom** akan didokumentasikan dalam berkas ini.

Format berkas ini mengacu pada panduan [Keep a Changelog](https://keepachangelog.com/id/1.0.0/) dan menganut prinsip [Semantic Versioning](https://semver.org/lang/id/).

---

## [1.2.0] - 2026-11-11

### Ditambahkan
- **Penugasan Serentak Multi-Mentee (Batch Classroom Assignment)**: Pembimbing lapangan dapat menerbitkan tugas batch serentak ke beberapa mentee sekaligus dengan opsi instruksi dan lampiran yang terintegrasi (`CreateBatchTugasAction`).
- **Pemisahan Arsitektur Landing CMS (`LandingCmsService`)**: Dekomposisi 700+ baris logika CMS dari controller ke service layer mandiri yang dilengkapi validasi form request terisolasi.
- **Pengamanan CSP & Migrasi Health Monitor**: Penghapusan total 570+ baris script inline pada antarmuka telemetry SuperAdmin ke dalam Vanilla ES Module terisolasi (`resources/js/pages/superadmin/health-monitor.js`).
- **Tata Kelola Tim Kolaborator & Repositori Resmi**: Penerbitan lisensi proprietary resmi Diskominfo (`LICENSE.md`), panduan kontribusi tim (`CONTRIBUTING.md`), kebijakan pelaporan keamanan (`SECURITY.md`), dan spesifikasi tim 3 kolaborator (User + 2 Rekan).
- **Paritas Antarmuka Profil & Akun**: Penyeragaman layout profil fluid fullwidth dan struktur dropdown akun pada topbar di keempat peran pengguna.

---

## [1.1.0] - 2026-11-10

### Ditambahkan
- **Modular Actions & Specialized Queries**: Pemecahan controller menjadi kelas aksi tunggal (`app/Actions/`) dan query terspesialisasi (`app/Queries/`) untuk meningkatkan skalabilitas dan pemeliharaan kode.
- **Fitur Batal Kirim Tugas Peserta (Unsubmit)**: Memungkinkan peserta magang membatalkan penyerahan tugas sebelum dinilai atau sebelum tenggat waktu berakhir.
- **Dukungan Multi-Lampiran Logbook**: Penambahan fleksibilitas unggah berkas pendukung pada logbook mingguan peserta.
- **Peta Alur Magang Interaktif**: Visualisasi DAG alur seleksi dan tahapan magang secara langsung pada landing page publik.
- **Preloader Animasi Sleman**: Pengalaman splash screen visual khas Pemkab Sleman pada portal publik dan peserta.
- **Ekspor Dokumen Resmi**: Kemampuan unduh rekapitulasi data dalam format `.xlsx`, `.csv`, dan `.pdf`, serta penerbitan Surat Penerimaan ber-QR Code valid.
- **Cockpit Telemetri SuperAdmin**: Pemantauan kesehatan infrastruktur server, penggunaan memori, dan responsivitas query secara real-time.

### Diubah
- **Harmonisasi Desain Bento Grid**: Penyelarasan antarmuka dashboard pada seluruh 4 peran aktor (Peserta, Admin, Pembimbing, SuperAdmin) berbasis token desain `DS-MagKom`.
- **Standardisasi Paginasi**: Penerapan seragam batas 10 baris per halaman pada seluruh master data SuperAdmin.
- **Penyelarasan Terminologi Operasional**: Lokalisasi istilah teknis seperti penyesuaian label SLA ke Bahasa Indonesia baku.

### Keamanan
- Penguatan mitigasi celah OWASP Top 10: Isolasi IDOR berbasis Laravel Policy pada seluruh berkas lampiran.
- Penguncian kuota konkurensi atomik menggunakan `lockForUpdate()` dalam transaksi basis data untuk mencegah *quota race condition*.
- Penerapan pembatasan laju (*rate limiting*) pada endpoint otentikasi dan aliran server-sent events (`/realtime/stream`).

---

## [1.0.0] - 2026-10-14

### Ditambahkan
- Peluncuran versi produksi awal (*Initial Release*) **Portal Manajemen Magang (MagKom)** Dinas Komunikasi dan Informatika Kabupaten Sleman.
- Otentikasi mandiri pendaftar dengan verifikasi email dan integrasi Google OAuth (Socialite).
- Wizard pendaftaran magang online multi-langkah (Individu & Kelompok).
- Manajemen kuota formasi bulanan dan pemetaan jurusan teknis.
- Alur penempatan peserta dua tahap dan penunjukan pembimbing lapangan ASN.
- Modul tugas terstruktur ala classroom dan pelaporan logbook mingguan.
- Pencatatan jejak audit forensik multi-kanal pada setiap mutasi data kedinasan.
