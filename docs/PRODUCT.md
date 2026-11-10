# Product Context & System Boundaries

<!-- impeccable:product-schema 1 -->

## Platform

Web Application (Desktop & Responsive Mobile)

## Users

Empat peran aktor resmi dalam sistem (*Role-Based Access Control*), dengan batasan wewenang terisolasi:

- **Peserta Magang**: Siswa SMK dan mahasiswa perguruan tinggi (D3/S1). Volume: ~15 pendaftar per periode formasi bulanan (12 kuota reguler + 3 slot khusus instansi), dengan durasi aktif 1–6 bulan (~90 peserta aktif simultan). Karakteristik: dominan mengakses via perangkat mobile/smartphone; aktivitas harian mencakup pengunggahan berkas persyaratan, pemantauan status pendaftaran, pengerjaan tugas ber-deadline, unggah progres kerja, dan pengisian presensi logbook mingguan.
- **Pembimbing Lapangan**: Pegawai Aparatur Sipil Negara (ASN) di lingkungan dinas teknis terkait. Aktivitas utama: mengawasi peserta bimbingan, menerbitkan instruksi tugas proyek (didukung library template tugas), mereview laporan progres, memvalidasi logbook mingguan (mendukung *bulk review*), meninjau Profil 360 Mentee, dan menetapkan status selesai magang. Akses dominan via desktop.
- **Admin Verifikator (Administrasi Kominfo)**: Staf sekretariat/tata usaha kedinasan. Aktivitas utama: memverifikasi validitas berkas pendaftar dalam batas SLA (5 hari kerja), memutuskan persetujuan/penolakan permohonan, mengalokasikan kuota formasi bulanan dan bidang dinas, melakukan plotting penempatan dua tahap, menunjuk pembimbing lapangan, dan mengunduh berkas bundel ZIP pendaftar.
- **SuperAdmin (Administrator Utama)**: Pengelola sistem tingkat tinggi. Aktivitas utama: konfigurasi 12 parameter operasional aplikasi, pengelolaan akun pegawai/staf, pemantauan jejak audit (*audit trail multi-channel*), pengawasan telemetri kesehatan sistem & diagnostik server, pengelolaan jalur khusus kemitraan dinas, manajemen pencadangan basis data (*disaster recovery*), pengelolaan matriks izin akses dinamis (*dynamic RBAC*), serta pengelolaan landing page publik melalui *Visual Site Editor* berbasis blok. Pada ranah pembimbingan (tugas, progres, logbook mentee), SuperAdmin berstatus **Read-Only mutlak** (tidak memiliki hak mutasi data).

## Product Purpose

Portal web terpadu untuk mengelola seluruh mata rantai siklus Praktik Kerja Lapangan (PKL) dan magang di Dinas Komunikasi dan Informatika secara transparan, akuntabel, dan efisien:  
`Informasi Kuota Real-Time` → `Pendaftaran Online (Mandiri / Kelompok)` → `Verifikasi Berkas & Kuota Atomik` → `Penempatan Dua Tahap & Plotting Pembimbing` → `Pelaksanaan Magang (Tugas Terstruktur & Logbook Mingguan)` → `Penyelesaian Bimbingan 1-Klik` → `Masa Tenggang Akun (2 Bulan)` → `Penonaktifan Akun & Pengarsipan Permanen`.

Keberhasilan sistem diukur dari:
1. Ketiadaan kondisi kuota minus pada akses pendaftaran konkuren (*zero quota race condition*).
2. Transparansi posisi dan status bagi pendaftar tanpa ketergantungan konfirmasi manual.
3. Efisiensi pembimbing lapangan dalam memantau puluhan mentee secara terpusat melalui Profil 360 Mentee dan Laporan Kinerja ASN.
4. Kepatuhan audit trail forensik terhadap seluruh aksi mutasi dan akses data kedinasan.

## Positioning & Interface Principles

- **Status Sebagai Antarmuka Utama**: Sistem sengaja tidak menggunakan notifikasi email blast, SMS gateway, atau WhatsApp API (satu-satunya email yang dikirim adalah verifikasi akun saat registrasi dan tautan reset password). Status pendaftaran, badge tahapan, catatan verifikator, dan linimasa dashboard adalah instrumen informasi utama bagi pengguna.
- **Visual Parity & Zero UI Redesign**: Antarmuka mempertahankan kesetaraan visual 100% dari design tokens yang telah disepakati (`DS-MagKom`), menggunakan palet warna korporat Kominfo Sleman, dan tipografi resmi Open Sans.
- **Strict Separation of Concerns**: Pemisahan tegas antara markup presentasi Blade, stylesheet scoped di `resources/css/pages/`, dan modul interaksi ES Modules di `resources/js/pages/`.

## Operating Context

- **Instansi**: Dinas Komunikasi dan Informatika Kabupaten Sleman. Bahasa antarmuka: Bahasa Indonesia formal, lugas, dan profesional.
- **Autentikasi**: Formulir registrasi lokal dengan email terverifikasi serta integrasi Google OAuth via Laravel Socialite.
- **Manajemen Akun Staf & Jalur Khusus**: SuperAdmin membuat akun staf pegawai dan pendaftar jalur khusus dengan kredensial awal (`password_awal`). Pemilik akun diwajibkan mengganti kata sandi saat sesi login perdana (`must_change_password`).
- **Alur Penyelesaian Bimbingan**: Menggantikan mekanisme lembar nilai rubrik dan skala predikat lama yang telah dihapus permanen. Pembimbing menyelesaikan masa bimbingan mentee secara langsung melalui tombol penyelesaian 1-klik setelah seluruh target tugas terpenuhi.
- **Presensi Logbook Mingguan**: Pengisian logbook dibatasi maksimal mundur 3 hari kerja (`logbook_maks_mundur_hari = 3`) untuk mencegah pelaporan rapel di akhir masa magang.
- **Tenggat Tugas Terstruktur**: Wajib berjarak antara hari penugasan hingga sebelum tanggal berakhirnya penempatan mentee. Ambang batas peringatan mendekati deadline dihitung dinamis (default ≤ 3 hari).
- **Dokumen Sumber**:
  - [`01-PRD-MagKom.md`](./01-PRD-MagKom.md) (Product Requirements Document)
  - [`02-TDD-MagKom.md`](./02-TDD-MagKom.md) (Technical Design Document)
  - [`04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md`](./04-SKEMA-DATABASE-DAN-DATA-DICTIONARY.md) (Kamus Data & Skema Basis Data)
  - [`05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`](./05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md) (Spesifikasi Keamanan & Matriks Perizinan)
  - [`07-DESIGN-SYSTEM-MAGKOM.md`](./07-DESIGN-SYSTEM-MAGKOM.md) (Design System MagKom)
  - [`08-DOKUMENTASI-LENGKAP-FITUR-DAN-MODUL-MAGKOM.md`](./08-DOKUMENTASI-LENGKAP-FITUR-DAN-MODUL-MAGKOM.md) (Dokumentasi Fitur & Modul Sistem)
  - [`DEVELOPMENT-GUIDE.md`](./DEVELOPMENT-GUIDE.md) (Panduan Pengembangan & Setup)
  - [`FRONTEND-ARCHITECTURE.md`](./FRONTEND-ARCHITECTURE.md) (Arsitektur Aset Frontend Vite)

## Capabilities and Constraints

- **Stack Terkunci**: **PHP 8.3.x**, **Laravel 10.x**, **Vite 6.x**, **MySQL 8.0+** (InnoDB). Pengujian otomatis menggunakan **PHPUnit 10.x** dengan SQLite `:memory:`.
- **Kepatuhan CSP & Asset Management**: Seluruh aset dikelola melalui Vite. Dilarang menyisipkan blok `<style>` atau `<script>` inline di Blade, serta dilarang menambahkan atribut inline event handler (`onclick`, dll.) dan styling statis (`style="..."`).
- **Penerimaan Atomik**: Alokasi kuota umum dan slot khusus wajib diapit transaksi database dengan `lockForUpdate()`.
- **Penyimpanan Berkas Privat**: Berkas persyaratan pendaftar disimpan pada disk `private` dan diakses hanya lewat temporary signed URL berumur 5 menit yang dilindungi otorisasi Policy.
- **Modul Visual Site Editor**: SuperAdmin dapat mengelola tata letak dan konten landing page publik melalui antarmuka editor blok visual (WordPress Gutenberg style) yang dilengkapi *Pattern Library*, *Reusable Blocks*, *Media Library*, serta *Revisions History & Rollback*.
- **Pusat Cadangan & Pemulihan Basis Data**: Fasilitas backup instan basis data format `.sql.gz` terkompresi dengan verifikasi checksum SHA-256 dan perlindungan unduhan privat.
- **Telemetri Diagnostik Server**: Command center pemantauan latensi HTTP, konektivitas database, integritas storage privat, serta 4 tombol aksi operasional (Security Sweep, Optimize DB, Clear Cache, Storage Write Test).

## Brand Commitments

- **Identitas**: MagKom (Magang Kominfo) — Dinas Komunikasi dan Informatika Kabupaten Sleman.
- **Design Tokens**: 11 Master CSS Peeper Tokens (`resources/css/base/variables.css`): `#02275D`, `#01347C`, `#003566`, `#3F51B5`, `#FFC300`, `#FFCB05`, `#212529`, `#000000`, `#AAAAAA`, `#BBBBBB`, `#FFFFFF`.
- **Tipografi**: **Open Sans** (400, 600, 700) untuk seluruh antarmuka teks dan **JetBrains Mono** untuk representasi data kode/telemetri.
- **Aset Logo**: Logo resmi Kominfo dan MagKom tersimpan pada direktori `public/images/`.
