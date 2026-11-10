# Rencana Kerja: Penyeimbangan Tata Letak dan Perapian Card Verifikasi Pendaftaran Peserta

**Tanggal:** 9 November 2026  
**Status:** Menunggu Persetujuan Pengguna  
**Referensi Bukti Visual:**  
- Tangkapan Layar 1 (`media_1794231811307_f582486c.png`): Card Rekam Jejak Pendaftaran terpotong di bagian bawah akibat pembatasan `max-height` dan `overflow` internal.
- Tangkapan Layar 2 (`media_1794231893793_01478461.png`): Card Verifikasi Berkas di kolom kiri terlalu melebar ke samping dan menyisakan ruang kosong putih raksasa di bawahnya, sementara kolom kanan menumpuk 3 card ke bawah.

---

## 1. Analisis Masalah dan Root Cause (Akar Masalah)

Berdasarkan kedua tangkapan layar yang dikirimkan pengguna:
1. **Ketidakseimbangan Proporsi Grid (Rasio Asimetris Ekstrem)**:
   - *Akar Masalah*: Penggunaan grid `grid-template-columns: minmax(0, 1.85fr) minmax(0, 1.15fr)` (sekitar 62% : 38%).
   - *Dampak*: Kolom kiri melebar sangat jauh ke samping. Saat berkas kosong (*empty state*) atau hanya berisi 4 dokumen, tinggi kolom kiri hanya ~250px–350px.
   - Sementara di kolom kanan, ditumpuk 3 card sekaligus:
     1. Keputusan Verifikasi (~400px)
     2. Rangkuman Calon Peserta (~300px)
     3. Rekam Jejak Pendaftaran (~250px)
   - Akibatnya, di bawah card Verifikasi Berkas kolom kiri terdapat **ruang kosong raksasa** (sekitar 600px–700px ruang putih tak terisi).
2. **Card Rekam Jejak Pendaftaran Terpotong di Bagian Bawah**:
   - *Akar Masalah*: Penggunaan aturan CSS `.adm-sidebar-sticky { max-height: calc(100vh - 3rem); overflow-y: auto; }`.
   - *Dampak*: Pada layar beresolusi standar (tinggi 768px – 900px), card ketiga ("Rekam Jejak Pendaftaran") terpotong garis batas bawahnya dan hanya memunculkan sebaris teks ("Pembaruan Terakhir") dengan scrollbar internal yang kaku dan mengganggu pandangan verifikator.
3. **Titik Indikator Timeline Keluar Batas**:
   - *Akar Masalah*: Offset titik penanda `.adm-timeline-dot { left: -1.68rem; }` bertabrakan dengan batas padding container card Gentelella.

---

## 2. Solusi Desain dan Penataan Ulang Tata Letak

### A. Penyeimbangan Kolom (Balanced 2-Column Split: 1.15fr : 0.85fr)
Membagi beban card secara merata antara kolom kiri dan kolom kanan sehingga tinggi kedua kolom menjadi **seimbang sempurna (*height parity*)**, tidak ada card yang terpotong, dan tidak ada ruang kosong raksasa:

```text
+----------------------------------------------------------------------------------------------------+
| HEADER PROFIL PESERTA (Avatar, Nama, Badge Status, Badge Tipe, Asal Sekolah/Kampus, Tombol Kembali)|
+----------------------------------------------------------------------------------------------------+
| GRID SEIMBANG (1.15fr : 0.85fr, gap: 1.5rem)                                                       |
|                                                                                                    |
| KOLOM KIRI (Kiri ~57%)                             KOLOM KANAN (Kanan ~43%)                         |
| +------------------------------------------------+ +-----------------------------------------------+ |
| | [JIKA KELOMPOK] Card Tim & Anggota             | | Card 1: KEPUTUSAN VERIFIKASI                  | |
| +------------------------------------------------+ | - Status Kuota Jurusan                        | |
| | Card 1: VERIFIKASI BERKAS CALON PESERTA        | | - Tab Aksi: Terima / Tolak                    | |
| | - Subtitle: X Berkas Persyaratan Terlampir     | | - Form Upload SK Diterima (PDF)               | |
| | - Daftar Berkas (Surat, Proposal, CV, Transkrip)| | - Catatan & Tombol Keputusan                  | |
| | - Tiap baris: Ikon, Nama, File size,           | |   (Atau Banner Resmi SK jika sudah diterima)  | |
| |   [Tinjau Berkas] [Unduh (Icon)]               | +-----------------------------------------------+ |
| +------------------------------------------------+ | Card 2: REKAM JEJAK PENDAFTARAN (TIMELINE)     | |
| | Card 2: RANGKUMAN CALON PESERTA (BIODATA)      | | - Pembaruan Terakhir (Dot Hijau Aktif)        | |
| | - Grid 2 Kolom Kompak (Asal Instansi, NIM,     | | - Pengajuan Berkas                            | |
| |   Kontak WhatsApp/Email, Bulan & Sisa Kuota)   | | - Registrasi Akun                             | |
| +------------------------------------------------+ +-----------------------------------------------+ |
+----------------------------------------------------------------------------------------------------+
```

### B. Perbandingan Keseimbangan Tinggi Kolom
- **Kolom Kiri**:
  - Card Verifikasi Berkas: ~320px (atau ~200px jika empty state)
  - Card Rangkuman Biodata: ~240px
  - *Total tinggi kolom kiri*: **~440px – 560px**
- **Kolom Kanan**:
  - Card Keputusan Verifikasi: ~360px
  - Card Rekam Jejak Pendaftaran: ~200px
  - *Total tinggi kolom kanan*: **~560px**
- **Hasil**: **Kedua kolom sejajar penuh, simetris, rapi, dan tidak ada ruang kosong di kiri maupun kanan.**

### C. Penghapusan Overflow & Truncation
1. Menghilangkan `max-height: calc(100vh - 3rem);` dan `overflow-y: auto;` pada container sidebar sehingga card Rekam Jejak Pendaftaran **tampil utuh 100% tanpa terpotong**.
2. Memperbaiki posisi garis timeline (`border-left`) dan titik milestone (`.adm-timeline-dot`) agar berada di dalam area padding kartu secara presisi.
3. Card Verifikasi Berkas disesuaikan proporsinya agar tidak terlalu memanjang ke samping, dengan padding selaras dan layout tombol aksi yang teratur.

---

## 3. Rencana Perubahan Kode Bertahap

### File 1: `resources/css/pages/admin/pendaftaran-show.css`
1. **Grid Container**:
   - Mengubah `.adm-split-grid` dari `1.85fr 1.15fr` menjadi `minmax(0, 1.15fr) minmax(0, 0.85fr)` (proporsional dan seimbang).
2. **Sidebar**:
   - Mengubah `.adm-sidebar-sticky` menjadi container fleksibel tanpa batasan `max-height` dan tanpa `overflow-y: auto`, sehingga tidak ada konten yang terpotong.
3. **Card Verifikasi Berkas**:
   - Merapikan padding dan jarak item `.adm-doc-item` agar padat, proporsional, dan pas di ukuran layar laptop maupun desktop besar.
4. **Card Rangkuman Biodata (Kiri Bawah)**:
   - Menggunakan grid 2 kolom kompak (`grid-template-columns: repeat(2, 1fr)`) dengan border pemisah halus dan kartu info yang bersih.
5. **Timeline Audit**:
   - Menyelaraskan margin timeline `.adm-timeline-ol` dan posisi `.adm-timeline-dot` tepat di atas garis konektor (`left: -19px` atau padding terkontrol).

### File 2: `resources/views/admin/pendaftaran/show.blade.php`
1. **Penyusunan Ulang Blok**:
   - **Kolom Kiri**:
     - Info Kelompok (jika ada).
     - Card Verifikasi Berkas Calon Peserta.
     - Card Rangkuman Calon Peserta (Biodata) diposisikan di kolom kiri bawah ini dengan layout 2 kolom rapi.
   - **Kolom Kanan**:
     - Card Keputusan Verifikasi.
     - Card Rekam Jejak Pendaftaran (Timeline).
2. **Verifikasi Markup**:
   - Menjaga seluruh form submit, CSRF token, signed download/preview URLs, dan modal `#docPreviewModal`.

---

## 4. Rencana Pengujian dan Quality Gates

1. **Kompilasi Aset Frontend**:
   - Menjalankan `npm run build` untuk memverifikasi bundle CSS Vite.
2. **Feature Tests Modul Pendaftaran**:
   - Menjalankan `vendor/bin/phpunit tests/Feature/PendaftaranPesertaTest.php tests/Feature/AdminEksporLaporanPendaftaranPopoverTest.php tests/Feature/PendaftaranKelompokTest.php`.
3. **Laravel Pint**:
   - Menjalankan `vendor/bin/pint --test` pada file terkait.
4. **Full Test Suite**:
   - Menjalankan `vendor/bin/phpunit` (741 tests harus tetap 100% HIJAU).
5. **Visual Inspection**:
   - Memastikan tidak ada card yang terpotong di bagian bawah.
   - Memastikan tidak ada ruang kosong raksasa di bawah kolom kiri.

