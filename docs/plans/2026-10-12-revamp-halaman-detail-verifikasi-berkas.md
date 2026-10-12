# Rencana Kerja: Revamp Desain Halaman Detail Verifikasi Berkas (Show View)

Dokumen ini merinci rencana transformasi antarmuka (UI/UX) untuk **Halaman Detail Verifikasi Berkas Calon Peserta** ([`resources/views/admin/pendaftaran/show.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/admin/pendaftaran/show.blade.php)) yang muncul ketika admin mengklik tombol *"Tinjau Berkas"* pada antrean pendaftaran. Tujuannya adalah menghadirkan tampilan yang **simpel, bersih, bernapas lega, dan profesional** sesuai kebutuhan verifikator dinas.

---

## 1. Analisis Masalah Tampilan Saat Ini (Berdasarkan Feedback Pengguna)

1. **Header Banner Terlalu Tebal & Berat Visual**:
   - Banner hero biru atas terlalu tinggi dan menggunakan background gambar yang kurang sesuai untuk lembar kerja verifikasi perorangan.
   - Memakan banyak ruang vertikal di layar sehingga verifikator harus menggulir (*scroll*) ke bawah untuk melihat dokumen yang ingin diperiksa.
2. **Hierarki Data Terbalik (Dokumen Persyaratan di Bawah)**:
   - Verifikator membuka halaman ini dengan satu tujuan utama: **memeriksa keabsahan berkas (PDF/gambar)**. Namun berkas ditaruh di urutan paling bawah setelah kotak biodata dan kotak periode magang.
3. **Terlalu Banyak Kotak Card Terpisah**:
   - Biodata kontak, periode magang, dokumen, panel keputusan, dan riwayat dipecah menjadi 5 kotak terpisah dengan border abu-abu yang membuat layar tampak terkotak-kotak dan ramai.
4. **Label Data Kaku**:
   - Penggunaan huruf kapital penuh (`NAMA LENGKAP`, `ASAL INSTANSI PENDIDIKAN`, dll.) dengan font kecil abu-abu di atas teks hitam tebal terasa seperti formulir kertas konvensional.

---

## 2. Pendekatan Desain Baru: *Clean Verification Workspace*

### A. Perbandingan Opsi Desain

| Aspek | Opsi A: Mempertahankan Banner Tebal & Menata Ulang Isi Card | Opsi B: Clean Compact Header + Document-First Split Layout (Rekomendasi) |
|---|---|---|
| **Header** | Tetap menggunakan hero banner biru besar, hanya mengecilkan padding. | **Compact Identity Bar**: Header profil ramping, putih bersih dengan aksen biru MagKom, avatar inisial modern, dan navigasi terpadu. |
| **Hierarki Dokumen** | Dokumen tetap di bawah biodata. | **Document & Overview Terpadu**: Dokumen persyaratan diletakkan strategis berdampingan dengan ringkasan akademik calon peserta. |
| **Efisiensi Kerja** | Admin harus scroll cukup jauh untuk klik pratinjau. | **Langsung terlihat pada layar pertama (*above the fold*)** tanpa scroll berlebih. |
| **Keputusan** | *Ditolak* (masih terasa berat dan ramai). | **DIPILIH**: Lebih simpel, lapang, fokus pada tugas verifikasi, dan sangat estetis. |

---

### B. Rincian Komponen Desain Baru

1. **Compact Identity Bar (Header Ramping & Bersih)**:
   - Menggantikan hero tebal dengan kartu profil putih bersih berborder halus `#E2E8F0`, dengan aksen biru elegan Kominfo.
   - **Kiri**: Avatar bulat/rounded-rect inisial (52px), Nama Lengkap calon peserta, chip nomor registrasi monospace `#REG-XXXXX`, badge status berkas yang kontras, dan asal institusi.
   - **Kanan**: Waktu pengajuan pendaftaran dan tombol navigasi outline *"&larr; Kembali ke Antrean"* yang ramping dan proporsional.

2. **Kolom Kiri (60%): Dokumen Persyaratan & Ringkasan Calon Peserta**:
   - **Card Dokumen Persyaratan (Fokus Utama)**:
     - Tampil di posisi atas agar admin langsung melihat berkas yang perlu ditinjau.
     - Tiap dokumen disajikan dalam item baris yang modern:
       - Icon format (PDF merah / Gambar biru).
       - Judul dokumen persyaratan (misal: *Surat Permohonan Instansi*, *Proposal Rencana Magang*), nama file asli, dan ukuran file.
       - Tombol *"Tinjau Berkas"* (membuka in-app modal preview langsung) dan tombol *"Unduh Berkas"*.
   - **Card Ringkasan Akademik & Magang**:
     - Menyatukan identitas akademik dan periode magang dalam 1 kartu terpadu yang rapi.
     - Grid 4 sel informasi bersih (Label ramah abu-abu, teks nilai hitam yang jelas):
       1. Instansi & Jurusan Asal
       2. NIM/NISN & Jenjang Pendidikan
       3. Email & Nomor WhatsApp
       4. Periode Magang & Durasi (dengan mini progress kuota jurusan)

3. **Kolom Kanan (40%): Panel Keputusan & Timeline Ringkas**:
   - **Card Keputusan Verifikasi**:
     - *Jika sudah final (misal: Diterima)*: Card hasil keputusan bernuansa lembut (hijau untuk diterima, merah untuk ditolak) lengkap dengan stempel waktu dan kutipan catatan verifikator yang rapi.
     - *Jika masih diajukan*:
       - Status ketersediaan kuota real-time.
       - Tab switcher keputusan (Terima, Waiting List, Minta Revisi, Tolak) yang bersih dan intuitif.
       - Formulir penetapan pembimbing (saat terima), catatan revisi (saat minta revisi), atau alasan penolakan.
   - **Card Jejak Riwayat (Timeline)**:
     - Timeline mini vertikal: Registrasi Akun &rarr; Berkas Diajukan &rarr; Verifikasi Terakhir.

4. **Modal In-App Pratinjau Dokumen**:
   - Mempertahankan fungsionalitas pratinjau streaming PDF/gambar in-app yang aman dan sudah teruji dengan desain modal yang bersih dan responsif.

---

## 3. Rencana Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
|---|---|---|
| [`resources/views/admin/pendaftaran/show.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/admin/pendaftaran/show.blade.php) | **Ubah** | Revamp total tampilan header, layout split 2-kolom, hierarki kartu dokumen & biodata, serta styling panel keputusan. |

---

## 4. Langkah-Langkah Implementasi Terurut

- [x] **Langkah 1: Perbarui Header Profil Ramping (Compact Identity Bar)**
  - Ganti hero banner tebal dengan kartu profil ramping, bersih, beraksen elegan, dan hemat ruang vertikal.
  - Rapikan peletakan avatar, nama, nomor registrasi, status badge, dan tombol kembali.
- [x] **Langkah 2: Rancang Ulang Kolom Kiri (Dokumen-First & Biodata Terpadu)**
  - Letakkan Dokumen Persyaratan di bagian atas dengan card item yang lapang dan tombol tinjau berkas yang mencolok.
  - Padukan data identitas akademik, kontak, dan kuota magang ke dalam kartu ringkasan terpadu.
- [x] **Langkah 3: Rapikan Kolom Kanan (Panel Keputusan & Timeline)**
  - Perhalus desain kotak keputusan (baik saat status final maupun saat formulir aktif).
  - Tampilkan timeline vertikal riwayat pendaftaran yang rapi.
- [x] **Langkah 4: Validasi Render & Fungsionalitas**
  - Uji render view dengan scratch PHP test script (Render sukses: 90.5 KB).
  - Pastikan pengiriman form keputusan (`putuskan`) dan modal preview PDF/gambar berjalan 100% normal.

---

## 5. Kriteria Selesai (Definition of Done)

- [x] Tampilan halaman detail verifikasi pendaftaran terasa simpel, lapang, dan estetis (tidak berat atau berantakan).
- [x] Verifikator dapat langsung melihat dan meninjau dokumen persyaratan tanpa harus scroll jauh ke bawah.
- [x] Panel keputusan intuitif, jelas, dan aman dari salah klik.
- [x] Modal in-app preview PDF/gambar berfungsi normal.
- [x] Dokumen disetujui pengguna sebelum dieksekusi.
