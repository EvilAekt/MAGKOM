# Rencana Kerja: Redesain Peta Alur Interaktif — Tema Terang Resmi MAGKOM, Animasi Vektor Bersih & Tampilan Lega

Dokumen rencana kerja ini merespons masukan pengguna: *"terlalu penuh gasih dan asetnya terlalu ai dan gelap buat versi magkom jadi versi seperti tea utama magkom jangan gelap gitu dan terlalu banyak teks animasinya pun jelek buat yang lebih jelas dan menarik tentunya buat pplan nya dulu"*. 

Rencana ini merombak total visual simulator `/peta-alur` dari konsep malam hari gelap gulita yang terkesan "aset AI aneh" menjadi **Tema Terang Resmi MAGKOM Diskominfo Sleman (*Daylight Clean Civic Tech*)** dengan ilustrasi vektor yang bersih, proporsional, segar, minim teks, dan ruang kanvas yang lega (*uncluttered & spacious*).

---

## 1. Analisis Masalah Saat Ini & Solusi

| Masalah yang Dikeluhkan User | Analisis Kondisi Saat Ini | Solusi Redesain Tema Resmi MAGKOM |
| :--- | :--- | :--- |
| **1. Terlalu Gelap & Terkesan AI** | Kanvas menggunakan tema malam hitam pekat (`#060c17`), bintang berkelip, dan lampu sorot kuning yang tampak seperti grafis AI gelap yang tidak selaras dengan identitas portal instansi pemerintah. | **Ubah ke Tema Siang Hari Cerah Sleman (*Daylight Civic Tech*)**: Langit gradasi biru muda bersih (`#e0f2fe` ke `#f8fafc`), pemandangan Gunung Merapi asri dengan gradasi hijau kebiruan segar, jalan aspal abu-abu rapi (`#334155`), dan Gedung Diskominfo Sleman berwarna putih-biru modern. |
| **2. Terlalu Penuh & Banyak Teks** | Kartu misi di sisi kanan memakan tinggi layar penuh dengan banyak teks, daftar dokumen, notice box panjang, dan tombol berjejal yang menutupi pemandangan. | **Sederhanakan Menjadi Kartu Ramping (*Compact Clean Card*)**: Kartu info melayang diperkecil secara proporsional (lebar ~290px, tinggi ringkas). Hanya menampilkan judul pos, estimasi waktu, serta **2 poin ringkas utama**. Sisanya kanvas leluasa dan lega. |
| **3. Aset Karakter & Animasi Jelek / Tumpang Tindih** | Karakter di pos tampak seperti siluet AI kecil aneh, teks label pos saling tumpang tindih dengan badge lain, mobil tampak canggung di pojok layar. | **Ganti dengan Ilustrasi Vektor Flat Modern yang Jelas & Rapi**: Karakter vektor proporsional dengan kemeja dinas kominfo/baju rapi, ekspresi ceria, animasi gestur yang halus dan jelas (mengetik laptop, stempel verifikasi, mentor ASN berdiskusi, wisudawan memegang sertifikat). Label pos bersih tanpa tumpang tindih. |

---

## 2. Palet Warna Resmi MAGKOM (Diskominfo Kabupaten Sleman)

| Token Desain | Kode Warna | Penggunaan pada Diorama Peta Alur |
| :--- | :--- | :--- |
| **Brand Primary (Navy)** | `#1b3860` & `#122846` | Header topbar, aksen gedung Diskominfo, mobil dinas kominfo, teks judul |
| **Civic Blue Accent** | `#0284c7` & `#38bdf8` | Langit siang cerah, pilar gedung, tombol navigasi aktif, badge pos |
| **Sleman Gold** | `#f59e0b` & `#fbbf24` | Aksen lambang Sleman, bintang prestasi, marka aksen |
| **Daylight Canvas Background** | `#f0f7ff` ke `#ffffff` | Latar kanvas panorama siang hari, awan putih bersih |
| **Clean Road Asphalt** | `#334155` dengan marka `#ffffff` | Jalur jalan aspal abu-abu modern dengan marka putus-putus putih rapi |
| **Nature Sleman Green** | `#10b981` & `#ecfdf5` | Rumput taman asri Sleman, pepohonan hijau teduh di sekitar jalan |
| **Card Surface** | `#ffffff` dengan border `#e2e8f0` | Kartu info mengambang putih bersih dengan bayangan halus (*soft clean shadow*) |

---

## 3. Rancang Bangun Tata Letak Baru (Wireframe Visual Terang & Bersih)

```
+----------------------------------------------------------------------------------------------------------------+
| [DISKOMINFO SLEMAN · MAGKOM]  [← Beranda]   [Filter: Jenjang | Periode]     [🔊 Audio: On]  [▶ Tour]  [Daftar 🚀] |
+----------------------------------------------------------------------------------------------------------------+
| (LANGIT BIRU CERAH SLEMAN DENGAN AWAN VEKTOR PUTIH BERSIH & MATAHARI LEMBUT)                                    |
|                                                                                                                |
|  [Zoom Controls]                                    +----------------------------------------------------+     |
|   (+) (-) (Semua)                                   | 📍 POS 1 · LANGKAH AWAL                            |     |
|                                                     | Tahap 1: Registrasi Akun      [ ⏱️ 1–2 Hari Kerja ] |     |
|     (Siluet Gunung Merapi Hijau Asri Sleman)        | -------------------------------------------------- |     |
|                                                     | • Lengkapi biodata (NIK/NIM, kontak, & pas foto)   |     |
|       [Pos 1: Registrasi]                           | • Aktivasi akun via tautan email resmi             |     |
|       (Karakter Mahasiswa Ceria)                    | -------------------------------------------------- |     |
|             \                                       | [Pos 1 dari 4]               [Lanjut ke Pos 2 ➔]   |     |
|              \ ===> Mobil Dinas Kominfo Putih-Navy  +----------------------------------------------------+     |
|               \                                                                                                |
|            [Pos 2: Pengajuan] ---------> [Pos 3: Pelaksanaan] ---------> [Pos 4: Gedung Diskominfo Sleman]     |
|            (Stempel Verified)            (Mentor ASN Mendampingi)        (Karakter Wisuda & E-Sertifikat)      |
|                                                                                                                |
| (TAMAN HIJAU SLEMAN & JALAN ASPAL ABU-ABU BERSIH DENGAN MARKA PUTIH RAPI)                                      |
+----------------------------------------------------------------------------------------------------------------+
| [BILAH TAHAPAN BAWAH: 1. Registrasi (1-2 Hari) | 2. Pengajuan (3-7 Hari) | 3. Pelaksanaan | 4. Evaluasi]       |
+----------------------------------------------------------------------------------------------------------------+
```

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/peta-alur-interaktif.blade.php` | Modify | 1. Rombak defs & layer SVG: ganti langit malam gelap/bintang dengan langit siang biru muda cerah, siluet Gunung Merapi hijau asri, taman rumput Sleman, dan jalan aspal abu-abu rapi.<br>2. Perbaiki 4 set karakter SVG menjadi ilustrasi vektor flat yang proporsional, ramah, dan jelas (bukan siluet AI gelap).<br>3. Perbaiki mobil dinas menjadi mobil dinas Kominfo warna putih-navy yang bersih dan proporsional.<br>4. Ringkaskan kartu info (`stage-hud`) menjadi kartu putih bersih (*light theme card*) yang ramping, tidak memakan tempat, dengan 2 poin ringkas to-the-point.<br>5. Perbaiki topbar menjadi putih/navy bersih sesuai landing page MAGKOM. |
| `public/css/peta-alur-fullscreen.css` | Modify | 1. Ganti seluruh variabel warna dari dark theme (`#060c17`) ke light government theme (`#ffffff`, `#f8fafc`, `#1b3860`, `#0284c7`, `#e2e8f0`).<br>2. Styling kartu mengambang putih bersih dengan *clean drop shadow* (`box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08)`).<br>3. Perbaiki tata letak topbar agar rapi, simetris, dan tidak padat bertumpukan.<br>4. Styling animasi karakter vektor yang mulus dan alami. |
| `tests/Feature/PetaAlurPageTest.php` | Modify | Pastikan assertions memvalidasi tema terang, scene diorama siang hari, mobil dinas, 4 karakter vektor, dan in-scene stage card ringkas (100% hijau). |

---

## 5. Rincian Poin-Poin Ringkas per Pos (Sangat Singkat & Jelas)

1. **Pos 1: Registrasi Akun** (`1–2 Hari Kerja`)
   - • Isi biodata diri (NIK/NIM, pas foto, dan nomor WhatsApp aktif).
   - • Verifikasi akun melalui tautan yang dikirimkan ke email.
2. **Pos 2: Pengajuan Bidang** (`3–7 Hari Kerja`)
   - • Pilih bulan pelaksanaan magang & bidang yang kuotanya masih tersedia.
   - • Unggah Surat Pengantar Sekolah/Kampus, Proposal, CV, dan Transkrip.
3. **Pos 3: Pelaksanaan & Bimbingan** (`1–6 Bulan`)
   - • Mengikuti orientasi kerja dan bimbingan langsung dari mentor ASN.
   - • Mengerjakan tugas proyek dan mengisi logbook mingguan rutin.
4. **Pos 4: Evaluasi & E-Sertifikat** (`1–3 Hari Kerja`)
   - • Pembimbing memberikan evaluasi penilaian akhir disiplin & teknis.
   - • Unduh E-Sertifikat resmi bertanda tangan digital (TTE & QR Code).

---

## 6. Langkah Implementasi Terstruktur

### Tahap 1: Redesain Scene SVG Menjadi Siang Hari Cerah & Vektor Bersih
- [x] Buka [resources/views/public/peta-alur-interaktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php).
- [x] Ganti gradient langit malam (`#060c17`) dengan gradasi langit siang cerah Sleman (`#bae6fd` ➔ `#e0f2fe` ➔ `#f8fafc`).
- [x] Ganti siluet gunung malam dengan Gunung Merapi siang hari bernuansa asri (`#64748b` ➔ `#38bdf8` ➔ `#0284c7`).
- [x] Tambahkan bukit taman hijau Sleman (`#dcfce7` dan `#bbf7d0`) serta pohon-pohon vektor sederhana yang teduh di pinggir jalan.
- [x] Buat jalur jalan aspal abu-abu modern (`#334155`) dengan marka tengah putih putus-putus yang bersih dan presisi.
- [x] Redesain 4 titik checkpoint dan karakter vektor menjadi flat illustration yang jelas, rapi, dan proporsional:
  - Pos 1: Karakter pelajar/mahasiswa pakaian rapi di samping meja laptop dengan bubble checklist verifikasi hijau.
  - Pos 2: Meja seleksi verifikasi dokumen dengan stempel resmi *Verified*.
  - Pos 3: Mentor ASN Kominfo Sleman ramah mendampingi peserta magang di depan meja kerja.
  - Pos 4: Gedung Diskominfo Sleman putih-biru megah, karakter wisudawan membawa sertifikat dengan taburan konfeti ceria.
- [x] Redesain mobil dinas Kominfo menjadi mobil SUV putih-navy berlogo yang melaju mulus di jalurnya.

### Tahap 2: Penyederhanaan Kartu Info Menjadi Ramping & Bertema Bersih
- [x] Ubah kartu info menjadi kartu putih bersih (`background: #ffffff`, `border: 1px solid #e2e8f0`, `box-shadow: 0 10px 25px rgba(27, 56, 96, 0.08)`).
- [x] Buat ukuran kartu lebih ramping (lebar 300px), hilangkan tumpukan teks panjang, dan tampilkan hanya 2 poin ringkas.
- [x] Rapikan topbar dengan latar putih/biru lembut, tombol navigasi kembali, dan filter yang teratur rapi.
- [x] Rapikan bottom bar menjadi warna biru navy resmi MAGKOM (`#1b3860`) dengan teks putih dan aksen emas Sleman (`#f59e0b`).

### Tahap 3: Styling CSS Tema Terang MAGKOM di `peta-alur-fullscreen.css`
- [x] Buka [public/css/peta-alur-fullscreen.css](file:///d:/MAGKOM/MAGKOM/public/css/peta-alur-fullscreen.css).
- [x] Ganti background utama aplikasi dari radial gelap menjadi cerah bersih (`#f8fafc`).
- [x] Terapkan warna teks gelap kontras tinggi (`#0f172a`, `#334155`) agar nyaman dibaca tanpa silau.
- [x] Atur animasi mobil dan karakter agar bergerak halus, stabil, dan menyenangkan ditonton.

### Tahap 4: Pengujian Otomatis & Verifikasi
- [x] Perbarui assertions di [tests/Feature/PetaAlurPageTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/PetaAlurPageTest.php).
- [x] Jalankan feature tests:
  - `php artisan test --filter=PetaAlurPageTest`
  - `php artisan test --filter=LandingPagePetaAlurModalTest`
  - `php artisan test --filter=AlurMagangPageTest`
- [x] Jalankan Pint linter: `vendor/bin/pint tests/Feature/PetaAlurPageTest.php --test`.
- [x] Verifikasi HTTP response: `curl -I -H "Host: magkom.test" http://127.0.0.1/peta-alur` (200 OK).

---

## 7. Kriteria Selesai (Definition of Done)
- [x] Kanvas pemandangan berubah 100% menjadi tema siang hari cerah khas Sleman (langit biru cerah, Gunung Merapi asri, rumput hijau, jalan aspal bersih).
- [x] Tidak ada lagi latar belakang hitam pekat, siluet AI aneh, atau lampu sorot malam hari yang gelap.
- [x] Seluruh aset karakter digambar dengan vektor flat yang bersih, proporsional, rapi, dan mudah dikenali.
- [x] Kartu info tahapan berukuran ramping, bertema putih bersih, dan hanya memuat 2 poin ringkas utama sehingga kanvas tetap lega.
- [x] Topbar dan bilah bawah selaras dengan identitas visual resmi MAGKOM Diskominfo Sleman (`#1b3860`, `#0284c7`, `#f59e0b`).
- [x] Seluruh automated test lolos 100% hijau.
