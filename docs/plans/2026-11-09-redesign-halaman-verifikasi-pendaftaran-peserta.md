# Rencana Kerja: Redesign Halaman Detail Verifikasi Pendaftaran Peserta (Admin MagKom)

**Tanggal:** 9 November 2026  
**Status:** In Progress / Disetujui  
**Target File:**  
- `resources/css/pages/admin/pendaftaran-show.css` (Baru)
- `resources/css/app.css` (Import stylesheet baru)
- `resources/views/admin/pendaftaran/show.blade.php` (Refactoring markup semantik & penataan layout)

---

## 1. Latar Belakang & Tujuan
Halaman "Detail Verifikasi Pendaftaran Magang" (`admin/pendaftaran/{id}`) merupakan antarmuka krusial bagi staf verifikator/administrasi untuk memeriksa berkas calon peserta, menentukan status penerimaan/penolakan, dan melihat jejak audit pendaftaran.

Evaluasi tampilan saat ini:
1. Header profil memiliki visual terbalik (badge dan nomor registrasi di atas nama, avatar inisial kotak kaku).
2. Tombol "Tinjau Berkas" menggunakan solid button dark blue tebal yang berulang 4 kali, menciptakan visual fatigue.
3. Card "Rangkuman Biodata & Bulan Magang" diletakkan melebar penuh di bagian paling bawah, memaksa verifikator melakukan scrolling bolak-balik.
4. "Rekam Jejak Pendaftaran" disajikan dalam tabel statis dua kolom tanpa visual timeline alur waktu.
5. Tag styling CSS besar tertanam langsung di dalam Blade (`<style>` 380 baris), tidak mematuhi arsitektur CSS Vite modular proyek.

## 2. Rencana Perubahan Arsitektur & Antarmuka
1. **Pemisahan CSS ke Vite Pipeline**:
   - Memindahkan seluruh CSS dari view Blade ke `resources/css/pages/admin/pendaftaran-show.css`.
   - Meng-import stylesheet ke `resources/css/app.css`.
2. **Penyelarasan Header Profil**:
   - Avatar bulat inisial (`rounded-full`).
   - Nama peserta sebagai anchor utama (`font-weight: 800`, `font-size: 1.25rem - 1.5rem`).
   - Soft-pill status badge dengan dot indikator (Emerald untuk Diterima, Amber untuk Menunggu, Rose untuk Ditolak).
   - Metadata tersusun rapi: Asal Institusi • Jenjang & Jurusan • Nomor Registrasi.
3. **Konten Utama (Kolom Kiri 8 Kolom / 65%)**:
   - Card Verifikasi Berkas Calon Peserta.
   - List berkas dengan padding lapang, ikon file PDF/Image representatif, teks nama file ber-elipsis aman.
   - Tombol sekunder "Tinjau Berkas" (latar putih, border halus, teks slate) dan tombol unduh.
4. **Sidebar Kanan (Kolom Kanan 4 Kolom / 35% Sticky)**:
   - **Card Keputusan Verifikasi**: Banner status keputusan final lembut (soft emerald) atau form tindakan Terima/Tolak.
   - **Card Rangkuman Calon Peserta**: Dipindahkan dari bawah ke sidebar menggunakan semantic `<dl>`, `<dt>`, `<dd>`.
   - **Card Rekam Jejak Pendaftaran**: Dikonversi menjadi timeline vertikal semantik `<ol>`, `<li>` dengan garis konektor dan milestone dot.
5. **Integritas Fungsional**:
   - Mempertahankan seluruh alur form verifikasi, CSRF token, signed URLs (preview & download), modal `#docPreviewModal`, dan logika kelompok magang kolektif.

---

## 3. Tahapan Eksekusi & Quality Gate
1. Buat `resources/css/pages/admin/pendaftaran-show.css` dan daftarkan di `resources/css/app.css`.
2. Refactor markup Blade di `resources/views/admin/pendaftaran/show.blade.php`.
3. Kompilasi aset frontend via `npm run build`.
4. Uji seluruh Feature Test pendaftaran (`PendaftaranPesertaTest`, `PendaftaranKelompokTest`, `AdminEksporLaporanPendaftaranPopoverTest`).
5. Uji format kode dengan Laravel Pint.
6. Uji full PHPUnit suite.

