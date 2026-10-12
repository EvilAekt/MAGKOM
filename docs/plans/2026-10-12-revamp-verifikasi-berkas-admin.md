# Rencana Kerja: Revamp Tampilan Modul Verifikasi Berkas Admin (Menu 2: Verifikasi Berkas)

Dokumen ini merinci rencana transformasi antarmuka pengguna (UI/UX) untuk menu kedua pada sidebar admin, yaitu **Verifikasi Berkas** (`admin.pendaftaran.*`). Revamp ini mengadopsi bahasa desain signature MagKom Sleman (Hero Banner dengan gambar background resmi, Panel Metrik Terpadu, Filter Terintegrasi yang Rapi, serta Tabel Kerja Lapang dan Ergonomis).

---

## 1. Ruang Lingkup & File Terkait

- **Menu**: Verifikasi Berkas (Sidebar Menu 2)
- **Controller**: [`app/Http/Controllers/Admin/PendaftaranController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Admin/PendaftaranController.php)
- **Halaman yang Direvamp**:
  1. **Halaman Antrean Verifikasi (Index)**: [`resources/views/admin/pendaftaran/index.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/admin/pendaftaran/index.blade.php)
  2. **Halaman Detail & Eksekusi Verifikasi (Show)**: [`resources/views/admin/pendaftaran/show.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/admin/pendaftaran/show.blade.php)

---

## 2. Masalah UI/UX Saat Ini

### A. Pada Halaman Index (`index.blade.php`):
1. **Kotak-Kotak Bertumpuk Kuno**: Menggunakan 4 stat card terpisah dengan border kiri warna-warni yang terasa bising.
2. **Pill Filter Bertumpuk Dua Baris**: Baris *Filter Status* dan *Watchdog SLA* terpisah horizontal dan memanjang sehingga memakan banyak ruang vertikal dan berkesan berantakan.
3. **Form Pencarian Kaku**: Kotak search, dropdown periode, dan dropdown jurusan dipasang dalam form datar tanpa hierarki visual yang jelas.
4. **Tabel Kerja Kurang Bernapas**: Jarak antar-baris sempit, avatar calon peserta belum seragam, dan tombol aksi terasa kaku.

### B. Pada Halaman Detail (`show.blade.php`):
1. **Header Berantakan**: Nomor registrasi dan badge status mengambang dalam kartu metadata datar.
2. **Dokumen Persyaratan Kaku**: Daftar dokumen berjejer tanpa visual pratinjau yang nyaman bagi admin verifikator.
3. **Panel Formulir Keputusan Kaku**: Tombol keputusan (Terima, Perlu Perbaikan, Waiting List, Tolak) memiliki formulir yang berat dan rentan salah klik.

---

## 3. Pendekatan Desain Baru (Tema Resmi MagKom Kominfo)

### A. Halaman Index: Antrean Verifikasi Berkas
1. **Signature Hero Banner**:
   - Memakai background resmi MagKom `images/dashboard_hero_bg.jpg` dilapisi gradien biru Kominfo Sleman.
   - Sapaan & Judul: *"Antrean Verifikasi Berkas Pendaftaran"*.
   - Subtitle: *"Tinjau berkas pendaftaran calon peserta magang dan tetapkan keputusan secara transparan & tertib FIFO."*
   - Glass meta chips: Jumlah berkas menunggu, peringatan SLA kritis (> 3 hari), dan tanggal aktif.
2. **Panel Metrik Terpadu (*Unified KPI Strip*)**:
   - Menyatukan 4 metrik (*Total Pengajuan*, *Menunggu Verifikasi*, *Perlu Perbaikan*, dan *SLA Kritis*) ke dalam **1 single strip card** dengan divider vertikal tipis, persis seperti di Dashboard Admin dan Dashboard Peserta.
3. **Control Bar & Filter Terintegrasi**:
   - Satu baris filter elegan: Search bar calon peserta berikon kaca pembesar, dropdown periode bulan, dropdown jurusan, dan segmented pill status ringkas (*Semua*, *Diajukan*, *Perlu Perbaikan*, *Waiting List*, *Diterima*).
4. **Tabel Antrean Lapang (*Spacious Table*)**:
   - Avatar inisial ber-background pastel dinamis.
   - Identitas lengkap: Nama pendaftar, nomor registrasi (monospace), asal institusi & jurusan, periode magang yang dipilih.
   - Usia pengajuan ber-SLA badge ramah (*"Hari ini"*, *"2 hari lalu"*, atau badge merah jika tertahan > 3 hari).
   - Badge status berkas yang kontras dan elegan.
   - Tombol aksi *"Tinjau Berkas"* yang proporsional.

---

### B. Halaman Show: Detail & Eksekusi Keputusan Verifikasi
1. **Executive Profile Header**:
   - Banner profil calon peserta lengkap dengan avatar besar, nama lengkap, institusi, NIM/NISN, nomor pendaftaran, dan status terkini.
   - Tombol navigasi *"&larr; Kembali ke Antrean"* yang elegan.
2. **Layout 2 Kolom Seimbang (65% : 35%)**:
   - **Kolom Kiri (65% - Berkas & Biodata Lengkap)**:
     - Card 1: Biodata Akademik & Kontak (Institusi, Jurusan, Jenjang, No. WhatsApp, Kontak Darurat).
     - Card 2: Periode & Kuota Magang (Bulan mulai, durasi magang, kuota jurusan tersedia).
     - Card 3: Dokumen Persyaratan Terunggah (Surat Permohonan, Proposal, Transkrip/CV) dengan indikator status berkas, ukuran file, tombol *Lihat Pratinjau*, dan tombol *Unduh File*.
   - **Kolom Kanan (35% - Panel Keputusan & Tindak Lanjut Verifikasi)**:
     - Card Penetapan Keputusan yang intuitif dengan tab aksi bersih:
       - **1. Terima & Loloskan**: Form pemilihan bidang penempatan staf dan opsi penugasan pembimbing dinas.
       - **2. Minta Perbaikan (Revisi)**: Form catatan revisi spesifik bagi calon peserta (misal: "Surat permohonan belum ditandatangani Dekan").
       - **3. Masukkan Waiting List**: Keputusan antrean cadangan jika kuota sedang penuh.
       - **4. Tolak Pengajuan**: Form alasan penolakan yang komunikatif.
     - Riwayat Log Audit Verifikasi di bagian bawah.

---

## 4. Rincian Checklist Implementasi Terurut

- [x] **Langkah 1: Revamp Halaman Index (`resources/views/admin/pendaftaran/index.blade.php`)**
  - Pasang Signature Hero Banner dengan background image `images/dashboard_hero_bg.jpg`.
  - Pasang Unified KPI Strip Card 4 metrik terpadu.
  - Sederhanakan filter bar menjadi 1 toolbar kontrol terpadu yang bersih.
  - Tata tabel antrean verifikasi menjadi format lapang (*spacious*), bersih, dan ber-avatar rapi.
- [x] **Langkah 2: Revamp Halaman Detail (`resources/views/admin/pendaftaran/show.blade.php`)**
  - Tata executive profile header calon peserta.
  - Rancang kolom kiri: Biodata, informasi kuota, dan daftar dokumen persyaratan ber-preview modern.
  - Rancang kolom kanan: Panel penetapan keputusan (Terima, Revisi, Waiting List, Tolak) yang ergonomis dan aman dari salah klik.
- [x] **Langkah 3: Validasi Render & Interaktivitas**
  - Uji rendering view Blade via scratch script (Index: 79.3 KB, Show: 96.3 KB teruji sukses).
  - Pastikan pengiriman formulir keputusan (`putuskan`) dan filter pencarian tetap bekerja 100% normal.

---

## 5. Definition of Done (DoD)

1. Tampilan Verifikasi Berkas (Index & Detail) memiliki visual signature resmi MagKom Sleman yang selaras dengan Dashboard.
2. Desain lapang, bersih, bernapas lega, dan tidak ada elemen berjejal atau teks terpotong.
3. Seluruh fitur fungsional (pencarian, filter, pembukaan berkas PDF, eksekusi keputusan pendaftaran) berjalan normal tanpa error.
4. Rencana disetujui terlebih dahulu oleh pengguna sebelum dieksekusi.
