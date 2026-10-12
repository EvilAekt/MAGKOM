# Rencana Desain UI/UX: Dashboard Admin Simpel, Estetis & Bernapas Lega (Minimalist & Clean)

Dokumen ini memuat rencana perombakan tata letak bagian bawah **Dashboard Admin** (`admin.dashboard`) berdasarkan evaluasi screenshot pengguna. Tujuannya adalah menghilangkan kesan berantakan, meniadakan kartu-kartu kecil yang berjejal dan mepet, serta menggantinya dengan antarmuka yang **bersih, simpel, estetis, dan memiliki ruang bernapas (*generous breathing room*)**.

---

## 1. Identifikasi Masalah Visual Nyata (Berdasarkan Screenshot)

1. **Terlalu Banyak Kotak Bertumpuk (Visual Overload - 7 Card Terpisah)**:
   - Ada 3 kartu di kolom kiri dan 4 kartu di kolom kanan. Mata pengguna lelah melihat begitu banyak kotak bergaris dengan ukuran yang tidak seragam.
2. **Bagian Kuota Jurusan Terlalu Ramai & Penuh Sesak**:
   - 6 kotak kecil dijejer 3x2 dengan teks tebal, jenjang, persentase, dan bar warna-warni yang padat membuat area ini terlihat ruwet (*complicated*).
3. **Kolom Kanan Terasa Compang-Camping**:
   - Banner biru menumpuk di atas 3 kartu putih yang isinya sedikit (bahkan ada kartu yang hanya berisi 1 baris teks "Tidak ada peserta...").
   - Teks nama pembimbing terpotong (`Budi Santoso, S.Kom (Pembimbing La...`) karena kartu terlalu sempit.
4. **Layout Terjepit (Mepet)**:
   - Pembagian 68% : 32% membuat kolom kanan terlalu sempit untuk data dinamis, sementara kolom kiri terasa dipaksakan menampung kartu-kartu grid kecil.

---

## 2. Solusi Desain Baru: Estetis, Simpel & Humanis

Alih-alih menumpuk 7 kotak berantakan, kita sederhanakan menjadi **2 Zona Utama yang Proporsional & Lega**:

```
+----------------------------------------------------------------------------------------------------+
|  HERO BANNER (Background Gambar MagKom + Gradient Sleman)                                          |
+----------------------------------------------------------------------------------------------------+
|  UNIFIED KPI STRIP (Panel Putih 1 Kartu: 4 Metrik)                                                 |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|  ZONA 1: ANTREAN VERIFIKASI UTAMA (Full Width Card - Lapang & Elegan)                              |
|  - Card lebar dengan padding bernapas lega (16px 24px)                                             |
|  - Filter pills minimalis: Semua (1) | Diajukan | Perbaikan                                        |
|  - Tabel lapang: Avatar bersih, nama lengkap, institusi & jurusan, usia pengajuan, status, tombol |
|  - Tidak ada sidebar yang menjepit tabel ini                                                       |
|                                                                                                    |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|  ZONA 2: DUA KOLOM SEIMBANG (50% : 50% Grid Simpel)                                                |
|                                                                                                    |
|  KOLOM KIRI: MONITORING KUOTA JURUSAN         |  KOLOM KANAN: STATUS TIM & OPERASIONAL             |
|  - Satu kartu putih bersih                    |  - Satu kartu putih bersih                         |
|  - Header: Periode Aktif & Progress Bar Total |  - Sub-Section 1: Beban Pembimbing Lapangan        |
|  - List Row Minimalis (Bukan 6 Kotak 3x2!):   |    (List rapi, avatar bulat, nama utuh, mentee)    |
|    Setiap jurusan tampil dalam 1 baris sleek: |  - Sub-Section 2: Tenggat Selesai / Jadwal Gelombang|
|    [Nama Jurusan]  [Bar Ramping]  [Sisa Kursi]|    (Ringkasan bersih dan informatif)               |
|                                                                                                    |
+----------------------------------------------------------------------------------------------------+
```

---

## 3. Rincian Peningkatan Komponen

### A. Zona 1: Antrean Verifikasi (Full Width & Spacious)
- **Mengapa Full Width?**
  Karena antrean verifikasi adalah **tugas nomor 1 admin**. Memberinya ruang selebar halaman membuat tabel tidak terjepit, nama calon peserta tidak terpotong, institusi terbaca nyaman, dan status terlihat kontras serta anggun.
- **Styling**:
  - Border `#E2E8F0`, rounded `16px`, soft shadow.
  - Spasi vertikal antar baris lega (`padding: 14px 20px`).
  - Avatar lingkaran inisial berwarna pastel lembut.
  - Tombol aksi `Tinjau Berkas` bergaya minimalis elegan.

### B. Zona 2: Kolom Kiri – Monitoring Kuota Jurusan (Sleek List Row)
- **Mengapa List Row, Bukan Kotak 3x2?**
  Kotak-kotak kecil 3x2 terlihat seperti widget kalkulator yang bising. Dengan menggantinya menjadi **daftar baris (*sleek list rows*)**:
  - Di kiri: Nama jurusan dan badge jenjang (SMK / Kuliah).
  - Di tengah: Progress bar ramping (`height: 6px`) dengan persentase keterisian.
  - Di kanan: Badge status simpel (*Sisa 3 Kursi* / *Penuh*).
  - Tampilan menjadi sangat rapi, mudah discan dari atas ke bawah, dan tidak membuat mata pusing.

### C. Zona 2: Kolom Kanan – Status Operasional & Pembimbing Terpadu
- Menggabungkan konten sidebar yang tadinya terpecah 4 kotak menjadi **1 Card Komprehensif Bersekat Halus**:
  1. **Status Periode Aktif & Countdown**: Banner ringkas di bagian atas dengan pill status pendaftaran dan sisa hari.
  2. **Beban Pembimbing Lapangan**: Daftar mentor dengan avatar inisial, nama lengkap (tanpa terpotong!), bidang tugas, dan counter mentee aktif.
  3. **Tenggat Selesai Magang / Offboarding**: Alert minimalis jika ada peserta yang mendekati akhir magang (≤ 14 hari).
  4. **Pintasan Cepat Minimalis**: Baris link navigasi bersih dengan chevron halus.

---

## 4. Keunggulan Desain Baru Dibanding Sebelumnya

| Aspek | Desain Sebelumnya (di SS) | Desain Baru yang Direncanakan |
|---|---|---|
| **Jumlah Kotak** | 7 kartu terpisah-pisah (bising & berantakan) | **Hanya 2 zona kartu utama** (simpel & fokus) |
| **Keterisian Kuota** | Grid 6 kotak kecil 3x2 mepet & padat | **Daftar baris ramping (sleek list rows)** |
| **Tabel Verifikasi** | Terjepit di kolom 68% dengan tombol sempit | **Full-width lega & berwibawa** |
| **Keterbacaan Nama** | Terpotong (`Budi Santoso, S.Kom (Pembimbing La...`) | **Nama utuh terbaca jelas** |
| **Ruang Kosong** | Banyak kartu kosong yang mubazir | **Komposisi seimbang (50% : 50%)** |

---

## 5. File yang Terdampak

| No | File | Deskripsi Rencana Perubahan |
|----|------|-----------------------------|
| 1 | [`resources/views/admin/dashboard.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/admin/dashboard.blade.php) | Restrukturisasi bagian bawah dashboard dari grid asimetris 7-card menjadi arsitektur 2-zona (Zona 1: Full-width Verifikasi, Zona 2: 50%:50% Kuota & Operasional). |

---

## 6. Definition of Done (DoD)

1. Tampilan bawah dashboard tidak lagi terpecah menjadi banyak kotak kecil.
2. Tidak ada teks nama atau institusi yang terpotong.
3. Desain terasa lapang (*spacious*), simpel, bersih (*clean*), dan berkelas.
4. Semua data fungsional dan aksi tombol tetap berjalan 100% normal.
5. Rencana ini disetujui terlebih dahulu oleh pengguna sebelum diterapkan.
