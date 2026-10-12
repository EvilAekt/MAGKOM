# Rencana Kerja: Revamp Desain Modul Manajemen Kuota Admin (Menu 3: Manajemen Kuota)

Dokumen ini merinci rencana transformasi antarmuka pengguna (UI/UX) untuk menu ketiga pada sidebar admin, yaitu **Manajemen Kuota Magang** ([`resources/views/admin/kuota/index.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/admin/kuota/index.blade.php)). Revamp ini menyelaraskan bahasa desain dengan portal resmi MagKom Sleman: Signature Hero Banner, Unified KPI Strip, Integrated Period Selector & Toolbar, Spacious Table, serta Modal Alokasi Kuota yang bersih dan modern.

---

## 1. Ruang Lingkup & File Terkait

- **Menu**: Manajemen Kuota (Sidebar Menu 3)
- **Controller**: [`app/Http/Controllers/Admin/KuotaController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Admin/KuotaController.php)
- **Halaman Target**: [`resources/views/admin/kuota/index.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/admin/kuota/index.blade.php)

---

## 2. Masalah UI/UX Saat Ini

1. **Belum Ada Hero Banner Signature**:
   - Halaman dimulai dengan header datar tanpa identitas visual resmi MagKom, berbeda dengan Dashboard Admin dan Verifikasi Berkas yang sudah berkesan premium.
2. **Kotak Metrik Terpisah-pisah**:
   - Menggunakan 4 kartu stat terpisah (*Total Kapasitas*, *Kursi Terisi*, *Sisa Kursi*, *Okupansi Global*) yang memakan ruang vertikal dan terasa berkotak-kotak kaku.
3. **Toolbar Pemilihan Periode Datar**:
   - Dropdown periode bulan dan rentang jadwal pendaftaran terbungkus dalam form sederhana yang kurang terintegrasi secara visual.
4. **Tabel Alokasi Kurang Bernapas**:
   - Baris tabel rapat, progress bar okupansi sederhana, dan tombol aksi (*Edit* & *Toggle Aktif/Nonaktif*) bernuansa kaku.
5. **Modal Tambah/Edit Kuota Standar**:
   - Styling form modal terasa konvensional dan belum mengadopsi standar desain MagKom terkini.

---

## 3. Pendekatan Desain Baru (Tema Resmi MagKom Kominfo Sleman)

### A. Komponen Antarmuka Baru

1. **Signature Hero Banner**:
   - Latar belakang resmi MagKom `images/dashboard_hero_bg.jpg` dilapisi gradien biru Kominfo Sleman (`#0641CC` ke `#0533A8`).
   - Sapaan & Judul: *"Alokasi & Manajemen Kuota Magang"*.
   - Subtitle: *"Kelola kuota kursi magang per kejuruan secara transparan, akurat, dan adaptif terhadap kapasitas dinas."*
   - Meta chips: Nama periode bulan aktif, rentang tanggal pendaftaran, dan tombol aksi cepat *"Tambah Kuota Jurusan"*.

2. **Panel Metrik Terpadu (*Unified KPI Strip*)**:
   - Menyatukan 4 metrik kuota ke dalam **1 single strip card** dengan divider vertikal tipis:
     - **Total Kapasitas Kursi**: Jumlah akumulasi kursi seluruh jurusan.
     - **Kursi Terisi**: Total peserta yang telah diterima resmi.
     - **Sisa Kursi Tersedia**: Sisa kuota umum siap daftar (indikator hijau/merah).
     - **Tingkat Okupansi Global**: Persentase keterisian dengan mini progress bar dinamis.

3. **Selector Periode Magang Terpadu**:
   - Control bar modern untuk berganti periode bulan magang tanpa reload penuh yang membingungkan.
   - Dilengkapi badge status periode (misal: *"Periode Aktif Pendaftaran"*, *"Pendaftaran Ditutup"*, atau *"Mendatang"*).

4. **Tabel Alokasi Kuota Lapang (*Spacious Table*)**:
   - Penataan kolom ergonomis:
     - **Nama Kejuruan & ID Kuota**: Tipografi tebal dan kode ID kuota monospace.
     - **Kategori Jenjang**: Badge pill kontras (`SMK` atau `Mahasiswa D3/S1`).
     - **Total Kuota**: Angka tebal di tengah.
     - **Okupansi & Terisi**: Visual bar progres okupansi bergradien halus (hijau <75%, amber 75-99%, merah 100%).
     - **Sisa Kursi**: Badge status ketersediaan kursi yang komunikatif (*"Tersedia X Kursi"* atau *"Penuh"*).
     - **Status Alokasi**: Badge status toggle aktif/nonaktif yang elegan.
     - **Tombol Aksi**: Tombol *"Edit Kuota"* dan tombol switch *"Aktifkan/Nonaktifkan"* yang proporsional.

5. **Modal Dialog Tambah & Edit Kuota Modern**:
   - Header dialog ber-icon, input field dengan ring fokus biru halus, informasi batasan kapasitas (AC-05-3), dan tombol simpan kontras tinggi.

---

## 4. Rincian Checklist Implementasi Terurut

- [x] **Langkah 1: Pasang Signature Hero Banner & Unified KPI Strip**
  - Implementasikan banner dengan background `images/dashboard_hero_bg.jpg` dan chips periode aktif.
  - Pasang Unified KPI Strip Card 4-metrik (Total Kursi, Terisi, Sisa, Okupansi).
- [x] **Langkah 2: Revamp Selector Periode & Toolbar Kontrol**
  - Buat selector periode yang menyatu dan elegan dengan jadwal pendaftaran.
- [x] **Langkah 3: Tata Ulang Tabel Alokasi Kuota (Spacious Table)**
  - Terapkan desain tabel lapang, progress bar okupansi modern, dan badge ketersediaan.
  - Perhalus tombol aksi edit dan toggle status aktif kuota.
- [x] **Langkah 4: Perbarui Modal Tambah & Edit Kuota**
  - Perbaiki styling modal dialog, input form, validasi batas kuota, dan backdrop blur.
- [x] **Langkah 5: Validasi Render & Fungsionalitas**
  - Uji rendering Blade via script PHP test runner (Render sukses: 106.0 KB).
  - Pastikan operasi Create, Update, Toggle Aktif, dan pergantian periode via dropdown berjalan 100% normal tanpa error.

---

## 5. Kriteria Selesai (Definition of Done)

- [x] Halaman Manajemen Kuota memiliki signature visual resmi MagKom yang selaras dengan Dashboard dan Verifikasi Berkas.
- [x] Metrik disajikan dalam 1 strip terpadu yang hemat ruang dan estetik.
- [x] Tabel alokasi kuota lapang, mudah dipantau, dan tidak ada teks terpotong.
- [x] Seluruh fungsi (tambah kuota, edit alokasi, toggle aktif, ganti bulan) berfungsi 100% normal.
- [x] Rencana disetujui pengguna sebelum eksekusi dimulai.
