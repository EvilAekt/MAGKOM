# Plan: Redesain Halaman Detail Penempatan Peserta (Terstruktur, Tertata Rapi, & Elegan)

**Tanggal**: 05 November 2026  
**Status**: Selesai Terverifikasi  
**Referensi**: Permintaan Pengguna, Bukti Tangkapan Layar `media_1793816239563_9aff83da.png`, Aturan `AGENTS.md`.

---

## 1. Analisis Masalah UI/UX Saat Ini

Berdasarkan analisis visual tangkapan layar `media_1793816239563_9aff83da.png` dan berkas [`resources/views/admin/penempatan/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/penempatan/show.blade.php):

1. **Ketidakseimbangan Grid Bawah (3 Kolom Dipaksakan Sejajar)**:
   - Panel bawah saat ini menggunakan `grid-template-columns: repeat(auto-fit, minmax(360px, 1fr))` yang memecah layar menjadi 3 kolom berlebar sama.
   - Panel 1 (*Informasi Penempatan*) memiliki 6 bidang data (tinggi).
   - Panel 2 (*Pembimbing Lapangan*) hanya memuat 2 baris data kecil, menyisakan 60% ruang putih kosong melompong di bawahnya.
   - Panel 3 (*Berkas & Dokumen Magang*) hanya memuat 1 kotak surat, menyisakan 60% ruang putih kosong melompong di bawahnya.
   - Hasilnya: halaman terlihat bolong, asimetris tak beraturan, dan tidak terstruktur.
2. **Hero Header Kurang Elegan & Kering**:
   - Kartu identitas peserta menggunakan kotak putih biasa dengan avatar bundar biru polos berinisial dua huruf.
   - Rincian metadata peserta (`NIM/NISN • Institusi • No. Surat`) hanya berupa sebaris teks mentah dipisah titik peluru (*bullet*).
   - Tombol *"Pratinjau Surat"* terisolasi sendirian di pojok kanan kartu.
3. **Metrik Stat Cards Monoton & Indikator Semu**:
   - 4 kartu metrik Gentelella (`Tugas Selesai`, `Total Logbook`, `Nilai Akhir`, `Durasi Magang`) menampilkan panah tren naik semu (`+ Tugas`, `+ Jurnal`, `+ Periode`) dan grafik batang statis hardcode yang tidak bermakna fungsional.
   - Tampilan terlihat generic dan kaku.
4. **Hierarki Data Kurang Menonjol**:
   - Label data menggunakan uppercase kecil abu-abu (`field-label`) yang monoton.
   - Nilai data kedinasan (Bidang, Jurusan, Periode, Surat Keputusan) tidak memiliki pembeda visual atau ikon penjelas.

---

## 2. Evaluasi Opsi Arsitektur Layout

| Kriteria Evaluasi | Opsi A (Rekomendasi Utama): Asymmetric Split-Grid (65% : 35%) + Modern Cockpit | Opsi B: Full-Width Stacked Sections |
| :--- | :--- | :--- |
| **Pola Tata Letak** | **Kolom Utama (65%)** di kiri (Data Penempatan, Timeline Magang, Progres Tugas) & **Kolom Sidebar (35%)** di kanan (Profil Pembimbing, Tile Dokumen PDF Resmi). | Semua panel ditumpuk penuh melebar 100% dari atas ke bawah. |
| **Keseimbangan Visual** | **Sangat Sempurna**: Kolom kiri dan kanan memiliki ketinggian yang proporsional, tidak ada area putih kosong melompong. | Kurang optimal di layar widescreen (terlalu melebar ke samping). |
| **Efisiensi Membaca (Scanability)** | **Tinggi**: Admin dapat melihat identitas kedinasan di sisi kiri dan langsung memeriksa pembimbing/dokumen di sisi kanan dalam satu pandangan (*above the fold*). | Rendah, admin harus menggulir jauh ke bawah (*heavy vertical scroll*). |
| **Kesesuaian Desain MAGKOM** | Selaras dengan halaman detail verifikasi pendaftaran admin (`admin/pendaftaran/show.blade.php`). | Berbeda dengan pola halaman show lainnya. |
| **Kompatibilitas Tes** | **100% PASS**: Mempertahankan semua route, ID modal, controller logic, dan teks assertion. | 100% PASS. |

### Rekomendasi: Opsi A
Opsi A memberikan tata letak kelas dunia level enterprise, menyeimbangkan ruang vertikal dan horizontal, serta menyatukan identitas peserta dan dokumen resmi secara berwibawa.

---

## 3. Rincian Desain & Spesifikasi Baru (Opsi A)

### 3.1 Profil Hero Banner (`.penempatan-hero-banner`)
- **Visual Identity**:
  - Avatar peserta berbentuk rounded square modern (`56px x 56px`, border 2px putih, subtle shadow). Jika ada foto profil, menampilkan foto; jika tidak, monogram tajam dengan inisial dua huruf.
  - Nama Peserta (font-size `1.25rem`, font-weight `800`, text `#0F172A`).
  - Badge Status Penempatan Dinamis dengan dot status (Aktif = Hijau, Selesai = Biru, Batal/Mengundurkan Diri = Merah/Rose).
  - Badge Jalur (Reguler = Slate / Khusus = Amber).
  - Meta Tags Pill:
    - `NIM/NISN: 0054321987`
    - `Institusi: SMK Negeri 2 Depok Sleman`
    - `Jurusan: Teknik Komputer dan Jaringan (TKJ)`
- **Toolbar Aksi Terpadu**:
  - Tombol *"Pratinjau Surat"* (outline dengan icon preview mata).
  - Tombol *"Pindah Pembimbing"* (jika status aktif).
  - Tombol *"Batalkan Penempatan"* (jika status aktif, warna merah halus).

### 3.2 Metrik Kinerja & Progres Magang (4 Modern Stat Tiles)
Mengganti tile generic dengan 4 kartu metrik presisi:
1. **Tugas Selesai**: Nilai `1 / 3 Tugas` + Mini progress bar (33%) + Subteks status tugas.
2. **Total Logbook**: Nilai `3 Minggu` + Badge Jurnal mingguan terisi.
3. **Nilai Akhir**: Nilai `Belum Ada` atau angka format dua desimal + Predikat evaluasi.
4. **Durasi Magang**: Nilai `13 Minggu` + Tanggal rentang periode.

### 3.3 Kolom Utama Kiri (~65%): Alokasi Penempatan & Periode Kedinasan
1. **Surat Keputusan Kedinasan Banner**:
   - Box aksen biru dongker dengan Nomor Surat Keputusan tebal bergaya monospace: `500.12/0001/DISKOMINFO/2026`.
2. **Grid Informasi Penempatan (2x2 Modular Field Cards)**:
   - **Bidang Penempatan**: Nama bidang kedinasan dengan icon instansi.
   - **Jalur Pendaftaran**: Reguler / Jalur Khusus mitra.
   - **Jurusan Terdaftar**: Nama jurusan yang sesuai kualifikasi kuota.
   - **Periode Magang**: Tanggal mulai s/d tanggal selesai.
3. **Timeline Periode Magang Visual**:
   - Baris milestone linear yang menunjukkan:
     - `Titik Mulai`: 02 Okt 2026
     - `Titik Berjalan`: Hari Ini (jika dalam rentang waktu)
     - `Titik Selesai`: 31 Des 2026

### 3.4 Kolom Sidebar Kanan (~35%): Pembimbing & Dokumen Resmi
1. **Kartu Pembimbing Lapangan**:
   - Profil pembimbing: Avatar inisial elegan, Nama lengkap bergelar, Email dinas, NIP resmi.
   - **Kapasitas Bimbingan Meter**: Indikator beban bimbingan (`X Mentee Aktif`).
   - Tombol cepat *"Alihkan Pembimbing"* (jika aktif).
2. **Kartu Berkas & Dokumen Resmi**:
   - **Surat Keterangan Diterima Magang**: Tile dokumen PDF resmi dengan icon PDF merah/biru, tombol *"Lihat"* dan *"Unduh"*.
   - **Berkas Persyaratan Terlampir**: Daftar dokumen pendaftaran terverifikasi dengan badge status hijau.

---

## 4. Perubahan File

| No | File Target | Aksi | Ringkasan Perubahan |
| :-: | :--- | :---: | :--- |
| 1 | `resources/css/pages/admin/penempatan-detail.css` | **Create** | Modul stylesheet terpusat khusus untuk halaman detail penempatan (hero banner, split-grid, metric tiles, document tile, dark mode). |
| 2 | `resources/css/app.css` | **Modify** | Mendaftarkan import `@import './pages/admin/penempatan-detail.css';` ke asset pipeline Vite. |
| 3 | `resources/views/admin/penempatan/show.blade.php` | **Modify** | 1. Merestrukturisasi markup Blade ke dalam format split-grid (65% : 35%).<br>2. Menata ulang hero card menjadi Executive Identity Banner.<br>3. Menghubungkan kelas-kelas styling modular dari stylesheet Vite.<br>4. Mempertahankan seluruh modal logic (`openPindahModal`, `openBatalModal`) dan rute aksi. |

---

## 5. Permohonan Persetujuan Perubahan Markup Blade (Sesuai AGENTS.md Bagian 3.3)

- **Alasan Markup Diperlukan**: Mengubah tata letak dari 3 kolom sejajar (yang menyebabkan 2 kolom kosong) menjadi split-grid asimetris 2 kolom (65% : 35%) serta merombak profil hero card membutuhkan penataan struktur HTML semantik baru di Blade.
- **File & Bagian Target**: [`resources/views/admin/penempatan/show.blade.php:112-355`](file:///d:/laragon/www/MAGKOM/resources/views/admin/penempatan/show.blade.php#L112-L355).
- **Jaminan Integritas**:
  - Seluruh route (`admin.penempatan.index`, `admin.penempatan.surat-diterima`, `admin.penempatan.surat-diterima.pratinjau`, `admin.penempatan.pindah-pembimbing`, `admin.penempatan.batalkan`) tetap utuh 100%.
  - Modal ID dan fungsi JavaScript (`openPindahModal`, `closePindahModal`, `openBatalModal`, `closeBatalModal`) tetap utuh 100%.
  - Controller dan data binding tidak diubah.

---

## 6. Langkah Implementasi Terperinci

- [x] **Langkah 1**: Buat stylesheet baru di `resources/css/pages/admin/penempatan-detail.css` yang memuat seluruh styling layout, komponen hero, metrik, timeline, dan dark mode.
- [x] **Langkah 2**: Daftarkan file CSS baru tersebut di `resources/css/app.css`.
- [x] **Langkah 3**: Perbarui markup Blade di `resources/views/admin/penempatan/show.blade.php` dengan struktur split-grid asimetris dan komponen visual yang baru.
- [x] **Langkah 4**: Jalankan build asset dengan `npm run build`.
- [x] **Langkah 5**: Jalankan automated feature tests:
  ```bash
  php artisan test tests/Feature/DeltaDashboardEnhancementTest.php tests/Feature/AdminPenempatanLifecycleTest.php
  ```
- [x] **Langkah 6**: Jalankan code formatting dengan `./vendor/bin/pint --test`.

---

## 7. Kriteria Selesai (Definition of Done)

- [x] Halaman Detail Penempatan Peserta tampil terstruktur rapi, elegan, dan profesional.
- [x] Tidak ada lagi kotak kosong melompong pada area pembimbing dan dokumen.
- [x] Tata letak asimetris 2 kolom (65% : 35%) seimbang dan nyaman dilihat.
- [x] Hero banner menyajikan identitas peserta, status, dan tombol aksi dengan kontras tinggi.
- [x] 4 kartu metrik progres menyajikan informasi kuantitatif yang bersih dan jelas.
- [x] Dukungan dark mode (*tema gelap*) terintegrasi sempurna.
- [x] Seluruh fungsi interaktif (pratinjau surat, unduh PDF, modal alihkan pembimbing, modal batalkan) bekerja normal.
- [x] Seluruh automated feature tests PHPUnit tetap 100% PASS.
- [x] Build Vite dan Laravel Pint sukses tanpa peringatan.

