# Glosarium Istilah Domain & Teknis (Glossary)

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Cakupan | Definisi istilah domain kedinasan MAGKOM dan konsep teknis arsitektur |
| Referensi Implementasi | Seluruh modul sistem |

---

## 1. Glosarium Istilah Domain Kedinasan MAGKOM

### A
- **Agenda Bimbingan**: Pertemuan konsultasi terjadwal antara pembimbing lapangan dan mentee binaan (tatap muka atau daring) yang memuat notulensi arahan teknis.
- **Admin Verifikator (Administrasi Kominfo)**: Peran staf kedinasan yang bertugas memvalidasi berkas pendaftaran, mengelola kuota, dan melakukan plotting penempatan.
- **Akun Pasca-Magang**: Status akun peserta setelah menyelesaikan masa magang yang memasuki masa tenggang 2 bulan sebelum dinonaktifkan permanen.
- **Antrean Belum Ditempatkan**: Kumpulan pendaftar yang telah lolos seleksi berkas administrasi dan sedang menunggu penetapan bidang serta penunjukan pembimbing lapangan oleh admin.
- **Audit Trail**: Rekam jejak forensik seluruh mutasi data dan sesi otentikasi yang disimpan dalam tabel `audit_log` secara append-only.

### B
- **Batch Classroom Assignment**: Kemampuan pembimbing lapangan untuk menerbitkan tugas serentak ke beberapa mentee binaan sekaligus dengan instruksi dan tenggat waktu yang seragam.
- **Bento Grid**: Arsitektur tata letak kartu antarmuka modular pada dashboard Peserta dan Admin berbasis CSS Grid modern.
- **Bidang Dinas**: Lima unit kerja teknis di lingkungan Diskominfo Kabupaten Sleman (Aplikasi Informatika, Informasi dan Komunikasi Publik, Statistik, Persandian, dan Sekretariat).
- **Bulan Magang**: Master periode bulan kalender pembukaan penerimaan peserta magang.

### J
- **Jalur Khusus (Slot Khusus)**: Alokasi kuota diskresi kemitraan kelembagaan dinas yang terisolasi dari kuota umum publik.
- **Jalur Umum**: Alokasi kuota reguler yang dibuka untuk masyarakat umum, siswa SMK, dan mahasiswa melalui portal publik.

### K
- **Kelompok Magang**: Pendaftaran rombongan sekolah/kampus yang terdiri dari 1 ketua dan maksimal 3 anggota tambahan.
- **Kuota Formasi**: Batas kapasitas penerimaan peserta magang per jurusan dan per periode bulan.

### L
- **Logbook Harian**: Presensi dan laporan aktivitas pekerjaan harian peserta magang dengan batas pengisian maksimal 3 hari kerja ke belakang (H-3).

### M
- **Masa Tenggang Akun (Grace Period)**: Durasi akses waktu (default: 2 bulan) bagi peserta yang telah lulus magang untuk mengunduh portofolio berkas sebelum akun dinonaktifkan otomatis.
- **Mentee Limiter**: Pembatasan beban rasio bimbingan maksimal 20 mentee aktif untuk setiap pembimbing lapangan ASN.

### P
- **Pembimbing Lapangan**: Pegawai Aparatur Sipil Negara (ASN) di bidang dinas terkait yang ditugaskan membimbing, memberi tugas, dan mengevaluasi aktivitas mentee.
- **Penempatan Dua Tahap (Two-Stage Placement)**: Pemisahan alur kerja antara kelulusan seleksi berkas permohonan dan penetapan penempatan bidang serta pembimbing.
- **Peserta Magang**: Siswa SMK atau mahasiswa perguruan tinggi (D3/S1) yang terdaftar dan menjalani magang di Diskominfo Sleman.
- **Profil 360 Mentee**: Lembar rekapitulasi data komprehensif mentee yang mencakup kehadiran logbook, penyelesaian tugas, biodata, dan kontak darurat.

### S
- **SLA Watchdog**: Sistem pemantauan Service Level Agreement yang menghitung waktu tunggu berkas pendaftaran terhadap batas standar 5 hari kerja dengan indikator visual Hijau, Kuning, dan Merah.
- **Surat Penerimaan**: Dokumen resmi penetapan penerimaan magang berformat PDF yang dilengkapi tanda tangan digital dan QR Code verifikasi.

### U
- **Unsubmit (Batal Kirim Tugas)**: Hak peserta magang untuk menarik kembali laporan tugas yang telah dikumpulkan guna melakukan revisi berkas selama tugas belum direview atau dinilai oleh pembimbing lapangan.

---

## 2. Glosarium Konsep Teknis Arsitektur Perangkat Lunak

### A
- **Action Class (`app/Actions/`)**: Kelas PHP dengan tanggung jawab tunggal (*Single Responsibility Principle*) yang mengisolasi satu operasi mutasi data sistem.
- **Append-Only**: Karakteristik tabel basis data yang hanya mengizinkan operasi penambahan data (`INSERT`), menolak operasi penyuntingan (`UPDATE`) dan penghapusan (`DELETE`).
- **Atomic Concurrency Locking (`lockForUpdate`)**: Penguncian pesimistik pada tingkat baris database untuk mencegah kondisi perebutan kuota (*race condition*) pada saat pengajuan konkuren.

### C
- **Content Security Policy (CSP)**: Header keamanan HTTP yang membatasi sumber eksekusi skrip peramban; MAGKOM menegakkan `script-src 'self'` tanpa `unsafe-inline`.

### D
- **Dynamic RBAC (Role-Based Access Control)**: Pengelolaan matriks hak akses peran berbasis basis data terhadap 13 modul sistem definitif yang dapat dikonfigurasi melalui panel SuperAdmin.

### F
- **Formula Injection (CSV/DDE Injection)**: Serangan eksploitasi pada aplikasi spreadsheet akibat sel yang diawali karakter berbahaya (`=`, `+`, `-`, `@`); dimitigasi via `XlsxExportService`.

### I
- **Insecure Direct Object Reference (IDOR)**: Kerentanan keamanan akibat manipulasi parameter ID entitas; dimitigasi menggunakan pengecekan kepemilikan objek pada 5 kelas Laravel Policy.

### P
- **Pessimistic Locking**: Pendekatan penguncian transaksi basis data yang menahan akses tulis pihak lain hingga transaksi selesai dieksekusi (`DB::commit()`).

### Q
- **Query Class (`app/Queries/`)**: Kelas PHP yang mengisolasi logika pembacaan data yang memuat filter dinamis, pencarian, dan paginasi.

### S
- **Server-Sent Events (SSE)**: Protokol streaming data satu arah dari server ke peramban klien melalui koneksi HTTP persisten berbasis standar RFC 8895 pada endpoint `/realtime/stream`.
- **Skinny Controller**: Pola perancangan controller yang ramping (rata-rata 15–30 baris per method), hanya bertugas mengorkestrasi request dan memetakan respon.

### T
- **Temporary Signed URL**: Tautan unduhan berkas yang ditandatangani secara kriptografis menggunakan algoritma HMAC SHA-256 dengan batas waktu kedaluwarsa 5 menit.

### Z
- **Zero Inline Blade**: Kebijakan arsitektur mutlak yang melarang penulisan tag `<style>`, tag `<script>`, dan atribut event handler inline pada seluruh berkas Blade views.
