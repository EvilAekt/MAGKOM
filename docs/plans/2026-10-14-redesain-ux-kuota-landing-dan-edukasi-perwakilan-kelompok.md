# Rencana Kerja: Redesain UX Bagian "Bidang dan Kuota" & Edukasi Pendaftaran Kelompok (Perwakilan)

**Tanggal**: 14 Oktober 2026  
**Status**: 📋 Menunggu Persetujuan Pengguna  
**Dokumen Terkait**: [landing.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/landing.blade.php), [register.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/auth/register.blade.php), [LandingController.php](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Public/LandingController.php)

---

## 1. Tujuan
Membuat tampilan bagian **"Bidang dan Kuota"** pada Landing Page menjadi jauh lebih **User-Friendly (ramah bagi calon peserta)**, menghilangkan kebingungan informasi (anomali kuota kontradiktif), memperbaiki elemen navigasi yang menutupi konten kartu, serta memberikan penegasan informasi yang sangat jelas bahwa **pada pendaftaran kelompok, hanya satu orang (Ketua / Perwakilan Kelompok) yang membuat akun dan mengisi pendaftaran**.

---

## 2. Analisis Masalah Saat Ini & Temuan UX

Berdasarkan tinjauan visual dan tangkapan layar pengguna:
1. **Tombol Panah Navigasi Menutupi Isi Kartu (`<` dan `>`)**:
   - Tombol panah carousel saat ini melayang vertikal di tengah kartu (`top: 50%`) dan berada di posisi `left: -22px` / `right: -22px`.
   - Akibatnya, lingkaran tombol panah **menimpa langsung teks nama jurusan, deskripsi, dan tombol aksi kartu di pinggir**, membuat calon peserta sulit membaca informasi jurusan.
2. **Informasi Kuota Membingungkan / Kontradiktif pada Mode Kelompok**:
   - Contoh kasus nyata pada screenshot: Tim memilih **Kelompok (4 Orang)** pada jurusan dengan sisa **3 Kursi**.
   - Sistem menampilkan status: `Kuota Penuh (Waiting List)` berwarna kuning, **namun tepat di bawahnya tertulis**: `Sisa: 3 dari 5 Kursi (40% Terisi)`.
   - Calon peserta merasa bingung: *"Mengapa dibilang Kuota Penuh padahal tertulis sisa 3 kursi?"*.
   - **Akar Masalah**: Kuota sebenarnya belum habis (masih ada 3), namun *tidak cukup menampung sekaligus 4 orang anggota tim*. Keterangan ini tidak dijelaskan dengan bahasa manusiawi.
3. **Belum Ada Edukasi Jelas Mengenai Pendaftaran Kelompok**:
   - Calon peserta sering mengira semua anggota kelompok (2-4 orang) harus mendaftar akun sendiri-sendiri, sehingga terjadi duplikasi pendaftaran atau akun terpisah yang membingungkan verifikator.
   - Diperlukan edukasi visual yang tegas dan ramah: **"Hanya Ketua / Perwakilan Kelompok yang mendaftar akun. Data anggota lainnya dimasukkan di dalam formulir pendaftaran."**
4. **Bilah Kontrol Filter Terlalu Kaku**:
   - Filter `Jalur Pendidikan`, `Pendaftaran`, dan pemilihan jumlah anggota berdesakan dalam satu bingkai tebal abu-abu. Perlu tata letak visual modern bertingkat (segmented switchers) dengan tipografi yang bersih dan nyaman dipandang.

---

## 3. Pendekatan Desain & Solusi UX (User-Centric)

### A. Reposisi Tombol Navigasi Panah Carousel (Bebas Tumpang Tindih)
- Pindahkan tombol navigasi panah `<` dan `>` ke **Header Bilah Kontrol (sejajar dengan judul filter / petunjuk geser)** atau berikan padding luar (*safe zone*) sehingga tombol navigasi berada di samping tanpa pernah menimpa permukaan kartu jurusan.
- Tambahkan navigasi titik (*pagination dots*) atau tombol navigasi sudut yang anggun.

### B. Penyelesaian Anomali Kuota Kelompok dengan Bahasa Manusiawi
Ketika calon peserta memilih mode Kelompok dan sisa kuota kurang dari jumlah anggota tim (misal sisa 3, tim 4):
- **Status Badge Baru**: Bukan lagi `"Kuota Penuh"`, melainkan:
  `⚠️ Tersisa 3 Kursi · Butuh 4 Kursi`
- **Keterangan Penjelas**:
  `"Tersisa 3 kursi di periode ini (kurang 1 kursi untuk tim Anda). Anda dapat mendaftar melalui Jalur Waiting List atau menyesuaikan jumlah anggota."`
- **Tombol CTA**:
  `"Daftar Waiting List (Kelompok 4 Orang) ➔"`

### C. Banner Edukasi Menonjol: "Hanya Perwakilan yang Mendaftar"
Ketika tab **"Kelompok / Tim"** dipilih (aktif):
- Munculkan **Banner Kartu Khusus (Edukasi Alur Tim)** dengan ikon tim, gradien lembut biru Sleman, dan teks yang jelas:
  > 👥 **Penting untuk Calon Peserta Kelompok:**  
  > **Cukup 1 akun saja!** Pendaftaran kelompok dilakukan oleh **Ketua / Perwakilan Kelompok**. Data dan berkas anggota tim (2–4 orang) akan diisi langsung di dalam formulir pendaftaran oleh perwakilan. Anggota tim **tidak perlu membuat akun terpisah**.

### D. Notifikasi Harmonisasi di Halaman Registrasi (`/register`)
- Ketika calon peserta diarahkan dari landing page ke halaman registrasi dengan parameter `tipe=kelompok`:
  Tampilkan kotak info penjelas di bawah kartu jurusan terpilih:
  > ℹ️ **Anda mendaftar sebagai Perwakilan / Ketua Kelompok ({{ $selectedJumlah }} Orang).**  
  > Buat akun ini untuk mengelola pendaftaran kelompok Anda. Nama dan identitas rekan tim akan diisi pada formulir pendaftaran berikutnya.

---

## 4. Rincian Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [resources/views/public/landing.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/landing.blade.php) | Ubah | 1. Memperbaiki posisi tombol panah navigasi agar tidak menutupi kartu.<br>2. Menambahkan Notice Banner interaktif edukasi perwakilan kelompok saat tab Kelompok aktif.<br>3. Memperbarui logika JavaScript `recalculateAllCards()` agar menampilkan status kuota tim yang transparan dan tidak membingungkan.<br>4. Mempercantik segmented control filter jalur & pendaftaran. |
| [resources/views/auth/register.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/auth/register.blade.php) | Ubah | Menambahkan keterangan ramah di header form register bahwa pembuat akun adalah perwakilan/ketua kelompok. |

---

## 5. Langkah Implementasi Terurut

- [x] **Langkah 1: Perbaikan Layout Navigasi Panah Carousel**
  - Tombol navigasi panah floating yang menutupi teks kartu telah dihilangkan.
  - Ditambahkan kontrol navigasi panah header `.carousel-nav-btn-header` di sebelah kanan indikator kuota yang bersih dan tidak pernah menimpa permukaan kartu.

- [x] **Langkah 2: Pembuatan Banner Edukasi "Perwakilan Kelompok" di Landing Page**
  - Ditambahkan banner edukasi `.group-education-card` yang muncul secara dinamis saat calon peserta memilih mode `Kelompok / Tim`.
  - Memberikan edukasi tegas: 1 Akun Ketua/Perwakilan, Anggota tim dimasukkan di formulir pendaftaran.

- [x] **Langkah 3: Perbaikan Teks & Logika Status Kuota Kelompok (`recalculateAllCards`)**
  - Jika `sisa < currentMemberCount` dan `sisa > 0`:
    - Badge: `"Tersisa {sisa} Kursi · Tim Butuh {count}"`
    - Teks info kapasitas: `"Sisa {sisa} kursi (kurang {kekurangan} kursi untuk tim). Masuk Waiting List"`
  - Jika `sisa === 0`:
    - Badge: `"Kuota Penuh (Waiting List)"`
    - Teks info kapasitas: `"0 dari {total} kursi tersisa. Pendaftaran masuk antrean"`

- [x] **Langkah 4: Penyesuaian Halaman Registrasi (`/register`) untuk Alur Kelompok**
  - Ditambahkan notice banner edukasi di halaman registrasi ketika `tipe=kelompok`: pembuat akun adalah perwakilan/ketua kelompok dan anggota tim tidak perlu membuat akun terpisah.

- [x] **Langkah 5: Pengujian Otomatis & Visual**
  - Rangkaian 23 feature automated test dijalankan dan lulus 100% (`23 passed, 136 assertions`).
  - Dukungan mode terang dan mode gelap (*Dark Mode*) terverifikasi.

---

## 6. Strategi Testing

1. **Automated Feature Test**:
   - Jalankan `tests/Feature/SecurityDynamicCaptchaTest.php`, `ReleaseTwoFeaturesTest.php`, `PesertaFullPortalFeatureTest.php`, `TugasLogbookTest.php`.
2. **Visual & UX Verification**:
   - Buka Landing Page di browser.
   - Klik filter **"Kelompok / Tim"** dengan opsi 4 orang.
   - Amati banner edukasi perwakilan kelompok langsung tampil informatif.
   - Periksa kartu jurusan yang kuotanya tersisa 3: pastikan keterangannya berbunyi ramah ("Tersisa 3 Kursi · Tim Butuh 4") tanpa pesan "Kuota Penuh" yang membingungkan.
   - Periksa tombol panah kiri dan kanan: berada di header kontrol dan kartu terbaca 100% tanpa ada elemen yang terhalang.

---

## 7. Kriteria Selesai (Definition of Done)

- [x] Tombol panah navigasi carousel tidak lagi menutupi kartu atau teks jurusan.
- [x] Informasi kuota kelompok menjelaskan secara jujur dan transparan jika sisa kursi kurang dari jumlah anggota (tanpa teks kontradiktif).
- [x] Terdapat panduan jelas bahwa pendaftaran kelompok hanya diisi oleh perwakilan/ketua kelompok (baik di landing page maupun di register).
- [x] Tampilan antarmuka bersih, ramah bagi calon peserta, dan responsif di mobile maupun desktop.
- [x] Seluruh unit & feature tests lulus 100%.
