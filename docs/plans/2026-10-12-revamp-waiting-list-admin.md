# Rencana Kerja: Revamp Desain Modul Waiting List Admin (Menu 4: Waiting List)

Dokumen ini merinci rencana transformasi antarmuka pengguna (UI/UX) untuk menu keempat pada sidebar admin, yaitu **Antrean Waiting List (Daftar Tunggu)** ([`resources/views/admin/waiting-list/index.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/admin/waiting-list/index.blade.php)). Revamp ini menyelaraskan bahasa desain dengan portal resmi MagKom Sleman: Signature Hero Banner berlatar gambar resmi, Unified KPI Strip, Integrated Search Toolbar, Spacious Table FIFO yang ergonomis, serta Panel Penutupan Antrean Awal Bulan (AC-04-7) yang tertata rapi.

---

## 1. Ruang Lingkup & File Terkait

- **Menu**: Antrean Waiting List (Sidebar Menu 4)
- **Controller**: [`app/Http/Controllers/Admin/WaitingListController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Admin/WaitingListController.php)
- **Halaman Target**: [`resources/views/admin/waiting-list/index.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/admin/waiting-list/index.blade.php)

---

## 2. Masalah UI/UX Saat Ini

1. **Belum Ada Hero Banner Signature**:
   - Halaman dimulai secara datar tanpa identitas resmi MagKom Sleman, berbeda dengan menu Dashboard, Verifikasi Berkas, dan Manajemen Kuota.
2. **Tidak Ada Ringkasan Metrik Terpadu (KPI Strip)**:
   - Admin tidak memiliki gambaran cepat mengenai total antrean waiting list, berapa calon yang sudah siap dipromosikan (karena ada kursi kuota terbuka), dan berapa periode magang yang memiliki daftar tunggu.
3. **Panel Edukasi FIFO & Penutupan Massal Kaku**:
   - Penjelasan aturan bisnis BR-05 dan tombol penutupan antrean awal bulan (AC-04-7) disajikan dalam kartu datar bertumpuk yang memakan ruang pandang.
4. **Toolbar Filter Datar**:
   - Search bar dan dropdown periode/jurusan menggunakan form standar tanpa grid proporsional.
5. **Tabel Antrean Kurang Bernapas**:
   - Urutan antrean FIFO belum memiliki penanda visual prioritas (#1, #2, dst.) yang elegan, jarak antar-baris rapat, dan badge status ketersediaan kuota masih kaku.

---

## 3. Pendekatan Desain Baru (Tema Resmi MagKom Kominfo Sleman)

### A. Komponen Antarmuka Baru

1. **Signature Hero Banner**:
   - Latar belakang resmi MagKom `images/dashboard_hero_bg.jpg` dilapisi gradien biru Kominfo Sleman (`#0641CC` ke `#0533A8`).
   - Sapaan & Judul: *"Antrean Daftar Tunggu (Waiting List)"*.
   - Subtitle: *"Manajemen pendaftaran cadangan saat kuota penuh dengan prioritas ketat berprinsip First-In, First-Out (FIFO)."*
   - Meta chips: Total pendaftar menunggu, jumlah calon yang siap dipromosikan, dan periode antrean aktif.

2. **Panel Metrik Terpadu (*Unified KPI Strip*)**:
   - Menyatukan 4 metrik antrean ke dalam **1 single strip card** bergaris sekat vertikal tipis:
     - **Total Antrean Waiting List**: Jumlah calon peserta yang tertahan dalam antrean.
     - **Siap Dipromosikan**: Calon pendaftar yang kuotanya telah terbuka kembali dan siap diterima.
     - **Kuota Penuh / Tertahan**: Calon yang masih menunggu pembukaan kuota atau penambahan kursi.
     - **Periode Terdampak**: Jumlah bulan magang yang memiliki antrean aktif.

3. **Smart Control Toolbar (Search, Filter & Penutupan Massal)**:
   - Tata letak grid yang lapang:
     - Input pencarian calon peserta lapang dengan ikon kaca pembesar di sisi kiri dalam input.
     - Dropdown Periode Bulan Magang.
     - Dropdown Kejuruan Pilihan.
     - Tombol Terapkan & Reset.
   - Sisi bawah/kanan: Dropdown/tombol aksi ringkas penutupan antrean awal bulan (AC-04-7) yang hanya aktif saat periode telah memasuki tanggal 1 bulan mulai magang.

4. **Spacious Table (Tabel Antrean FIFO Lapang & Ergonomis)**:
   - **Badge Prioritas FIFO**: Urutan `#1` ditandai dengan badge khusus bernuansa prioritas utama (warna biru tua/emas cerah), disusul `#2`, `#3`, dst.
   - **Nomor Registrasi**: Monospace chip `#REG-XXXXX`.
   - **Identitas Calon Peserta**: Avatar inisial bersih, nama lengkap tebal, email, instansi, dan program studi.
   - **Periode & Ketersediaan Kuota**: Badge status ketersediaan kuota dinamis:
     - Jika kuota terbuka: Badge hijau terang *"Tersedia X Kursi (Siap Promosi)"*.
     - Jika kuota penuh: Badge abu-abu netral *"Kuota Penuh"*.
   - **Stempel Waktu Submit (FIFO)**: Format tanggal dan jam lengkap terurut.
   - **Tombol Aksi**:
     - Jika kuota tersedia: Tombol hijau emerald menonjol **"Promosikan"** &rarr; mengarahkan ke halaman verifikasi detail untuk penetapan pembimbing & penerimaan.
     - Jika kuota penuh: Tombol biru outline **"Tinjau"** &rarr; memeriksa berkas pendaftaran.

---

## 4. Rincian Checklist Implementasi Terurut

- [x] **Langkah 1: Pasang Signature Hero Banner & Unified KPI Strip**
  - Implementasikan banner dengan background `images/dashboard_hero_bg.jpg`.
  - Pasang Unified KPI Strip Card 4-metrik (Total Waiting List, Siap Promosi, Tertahan, Periode Terdampak).
- [x] **Langkah 2: Rancang Ulang Filter Bar & Panel Penutupan Massal**
  - Tata form pencarian ber-CSS Grid lapang dengan left-aligned search icon.
  - Rapikan tombol aksi penutupan antrean massal awal bulan (AC-04-7).
- [x] **Langkah 3: Tata Ulang Tabel Antrean FIFO (Spacious Table)**
  - Terapkan badge prioritas urutan FIFO (#1, #2, dst).
  - Tampilkan avatar inisial, identitas lengkap, dan badge ketersediaan kuota real-time.
  - Sempurnakan tombol aksi "Promosikan" (hijau) dan "Tinjau" (biru).
- [x] **Langkah 4: Validasi Render & Fungsionalitas**
  - Uji rendering Blade via script PHP test runner (Render sukses: 70.6 KB).
  - Pastikan filter pencarian, pagination, tombol promosi, dan penutupan antrean massal tetap berfungsi 100% normal.

---

## 5. Kriteria Selesai (Definition of Done)

- [x] Halaman Waiting List memiliki visual signature resmi MagKom yang selaras dengan menu sebelumnya.
- [x] Urutan FIFO terlihat jelas dan transparan bagi verifikator.
- [x] Calon peserta yang kuotanya sudah terbuka ditandai dengan jelas untuk siap dipromosikan.
- [x] Seluruh fungsionalitas (filter, pagination, link promosi/tinjau, aksi tutup antrean) berjalan normal tanpa error.
- [x] Rencana disetujui pengguna sebelum eksekusi dimulai.
