# Catatan Perubahan (Changelog)
Seluruh perubahan penting pada sistem **MagKom** akan didokumentasikan dalam berkas ini.

Format berkas ini mengacu pada panduan [Keep a Changelog](https://keepachangelog.com/id/1.0.0/) dan menganut prinsip [Semantic Versioning](https://semver.org/lang/id/).

---

## [1.3.0] - 2026-11-14

### Ditambahkan
- **Navigasi Menu Melayang Sidebar Mini (*Mini-Sidebar Floating Flyout*)**: Integrasi portal fixed menu melayang (`position: fixed; left: 72px; z-index: 1060;`) saat sidebar diciutkan (`body.nav-sm`), dilengkapi styling Sleman Navy Blue, penanda aktif royal navy, titik cyan bercahaya, dan tooltip interaktif untuk tautan tunggal.
- **Standarisasi Modal Konfirmasi Aksi Kritis**: Penggantian dialog browser native (`confirm()`, `prompt()`) dengan dialog modal interaktif berbasis Plus Jakarta Sans, backdrop blur, dan animasi smooth pada Master Bidang, Master Bulan, Master Jurusan, Slot Khusus, dan Pusat Cadangan Basis Data.
- **Dynamic Anti-Bot SVG Captcha & DOM Shuffling**: Generator kode captcha SVG dinamis internal dengan decoy honeypot fields dan validasi server-side `CaptchaRule` untuk proteksi brute force login tanpa dependensi library eksternal.
- **Seeder Akun Bersih Gmail (`PurgeAndSeedCleanGmailAccountsSeeder`)**: Otomasi penyelarasan data akun uji coba resmi berbasis format `@gmail.com` lengkap dengan foto profil resmi dan riwayat aktivitas 4 peran.
- **Perintah Artisan Diagnostik Reset Password (`auth:test-reset`)**: Pengujian mandiri alur pembuatan token lupa password dan cetak URL reset langsung ke CLI.
- **Panduan Teknis SMTP & Notifikasi Email**: Penerbitan panduan konfigurasi pengiriman email resmi (`docs/09-PANDUAN-SMTP-DAN-NOTIFIKASI-EMAIL.md`).

### Diubah
- **Optimasi Responsif Universal & Bento Layout**: Penataan ulang kartu antrean penempatan admin, bento hero portal peserta, dan tata letak tabel data adaptif di semua resolusi layar (mobile, tablet, desktop).
- **Perbaikan Alur Penempatan & Kuota Kelompok**: Sinkronisasi penghitungan sisa kuota kelompok dan penuntasan magang.

### Keamanan
- Penegakan alur validasi registrasi wajib sebelum login (`WajibRegistrasiSebelumLoginTest`).
- Penguatan penanganan *progressive account lockout* dan isolasi error autentikasi.

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
