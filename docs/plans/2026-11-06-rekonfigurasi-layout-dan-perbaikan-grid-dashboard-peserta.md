# Rencana Kerja: Rekonfigurasi Tata Letak Grid & Perbaikan Ketidakteraturan Tampilan Dashboard Peserta

- **Tanggal Pembuatan**: 2026-11-06
- **Status**: Selesai Diimplementasikan (*Completed*)
- **Penanggung Jawab**: AI Pair Programmer (Antigravity)
- **Referensi Desain**: Bukti Gambar Pengguna (`media_1793929391661_739dd291.jpg`)

---

## 1. Tujuan
Menyelesaikan akar masalah tampilan tidak beraturan, judul terpotong elipsis berlebihan (`Tugas Magang ...`, `Logbook Mingg...`), teks kegiatan terpotong di sisi kiri (`gsional menyeluruh...`), dan layout yang berhimpitan sempit pada Dashboard Peserta.

---

## 2. Analisis Akar Masalah (*Root Cause Analysis*)

Berdasarkan investigasi inspeksi visual dan CSS pada `resources/views/peserta/dashboard.blade.php`:

1. **Penyebab Utama: Pembagian Grid Bertingkat yang Terlalu Sempit (*Nested Grid Bottleneck*)**:
   - Struktur saat ini:
     - `.saas-cockpit-grid` membagi layout menjadi **Kolom Utama (Main)** dan **Kolom Samping (Aside 310px)**.
     - Di dalam Kolom Utama, `.saas-phase-grid` kembali membagi dirinya menjadi **2 kolom kartu berdampingan** (`repeat(auto-fit, minmax(320px/340px, 1fr))`).
   - Akibatnya, pada resolusi laptop standar (1024px – 1366px), setelah dikurangi sidebar navigasi (230px) dan padding luar:
     - Kolom Utama hanya memiliki sisa ruang sekitar **480px – 520px**.
     - Ketika ruang 500px tersebut dipaksa menampung 2 kartu berdampingan, masing-masing kartu (Tugas dan Logbook) hanya mendapatkan lebar bersih **~240px – 260px** (hampir selebar layar HP kecil!).
2. **Dampak Langsung pada Elemen UI**:
   - **Header**: Judul kartu *"Tugas Magang Mendesak"* dan tombol *"Lihat Semua →"* memperebutkan ruang 240px sehingga judul terpotong menjadi *"Tugas Magang ..."* dan *"Logbook Mingg..."*.
   - **Isi Tugas**: Judul dan kategori terpotong elipsis dini (*"Slicing & Integrasi Fronten..."*, *"Frontend Devel"*).
   - **Isi Logbook**: Teks cuplikan kegiatan mengalami *text clipping* di sebelah kiri (*"gsional menyeluruh..."*) karena `text-overflow: ellipsis` dan `overflow: hidden` pada kontainer flex yang terhimpit.
   - **Tampilan Keseluruhan**: Tiga kolom terasa sangat sesak, tidak seimbang, dan tidak beraturan.

---

## 3. Evaluasi Opsi Pendekatan Teknis

### Opsi 1: Rekonfigurasi Kolom Utama Menjadi Stack Vertikal Lega (Direkomendasikan)
- **Cara Kerja**:
  1. Pada `.saas-cockpit-main` (Kolom Utama), kartu **Tugas Magang Mendesak** dan kartu **Logbook Mingguan Terkini** disusun bertumpuk (*stacked vertically*) selebar 100% kolom utama (`grid-template-columns: 1fr`).
  2. Kolom Utama berdampingan secara harmonis dengan Kolom Samping (Aside Profil Pembimbing) di sebelah kanan.
  3. Dengan lebar kolom utama mencapai **550px – 700px**:
     - Judul header tampil utuh dan tombol *"Lihat Semua →"* berada di kanan atas dengan sangat lega.
     - Item Tugas memiliki ruang lebar: Judul tugas tidak terpotong, badge tenggat dan kategori tampil utuh dalam 1 baris rapi.
     - Item Logbook memiliki ruang leluasa: Badge Minggu, Tanggal, Status (`Disetujui`), dan tombol `Lihat` tertata rapi di kanan, serta teks deskripsi kegiatan terbaca utuh dari awal sampai akhir tanpa terpotong di kiri.
- **Kelebihan**:
  - Mengeliminasi 100% masalah teks terpotong dan berhimpitan.
  - Sesuai dengan standar hierarki visual dashboard industri (SuperAdmin MagKom & Google Workspace).
  - Tampilan menjadi sangat teratur, seimbang, dan sedap dipandang.
- **Kekurangan**:
  - Konten kolom utama tersusun ke bawah (namun dengan tinggi kartu yang padat/kompak, seluruh halaman tetap muat dalam 1 viewport tanpa scroll berlebih).

### Opsi 2: Mengubah Menjadi Grid 3 Kolom Sejajar Penuh (Full-Width 3 Columns)
- **Cara Kerja**: Menggabungkan Tugas, Logbook, dan Aside ke dalam 1 level grid 3 kolom (`1fr 1fr 1fr`).
- **Kelebihan**: Ketiga kartu sejajar secara horizontal.
- **Kekurangan**:
  - Pada layar laptop 1366px atau monitor kecil, tiap kolom tetap hanya selebar ~290px, sehingga judul dan teks panjang tetap rentan terpotong elipsis.

> **Keputusan**: Menggunakan **Opsi 1** karena memberikan keteraturan visual maksimal, kenyamanan membaca (*readability*), dan bebas dari clipping teks.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/dashboard.blade.php` | Modify | Ubah `.saas-phase-grid` menjadi `grid-template-columns: 1fr;` (stack vertikal lega di kolom utama) dengan gap 20px, perbaiki overflow clipping |
| `resources/views/peserta/dashboard/partials/_phase_aktif.blade.php` | Modify | Rapikan header dan item list dengan ruang horizontal penuh, hilangkan overflow clipping pada teks kegiatan logbook |
| `docs/plans/2026-11-06-rekonfigurasi-layout-dan-perbaikan-grid-dashboard-peserta.md` | Create | Dokumen rencana kerja terstruktur |

---

## 5. Langkah Implementasi (Checklist)

- [x] **Langkah 1: Rekonfigurasi Grid Kolom Utama di `dashboard.blade.php`**
  - Ubah `.saas-phase-grid` dari `repeat(auto-fit, minmax(...))` menjadi `display: flex; flex-direction: column; gap: 20px;` sehingga kartu Tugas dan kartu Logbook memanfaatkan 100% lebar kolom utama.
  - Atur gap antar kartu utama sebesar `20px`.

- [x] **Langkah 2: Perbaiki Overflow Clipping Teks di `_phase_aktif.blade.php`**
  - Hapus pembatas teks yang menyebabkan huruf depan terpotong (`text-align: left; overflow: visible` atau `display: block; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; direction: ltr;`).
  - Pastikan teks kegiatan logbook mulai dari huruf pertama tanpa terpotong di kiri.

- [x] **Langkah 3: Format Ulang Header Kartu yang Lega**
  - Berikan ruang bebas untuk judul *"Tugas Magang Mendesak"* dan *"Logbook Mingguan Terkini"* bersama subtitle tanpa pemotongan dini.
  - Posisikan tombol link *"Lihat Semua →"* dengan rapi di kanan atas.

- [x] **Langkah 4: Format Baris Item Tugas & Logbook yang Proporsional**
  - Pada Tugas: Judul tugas ditampilkan penuh, baris bawah memuat indikator tenggat dan kategori pill tanpa wrap sempit.
  - Pada Logbook: Tampilkan baris kompak berisi `Minggu X`, tanggal, badge status (`Disetujui`/`Terkirim`), tombol `Lihat`, dan teks kegiatan utuh.

- [x] **Langkah 5: Quality Gate & Verifikasi**
  - Jalankan build Vite: `npm run build`.
  - Jalankan test PHPUnit: `php artisan test --filter=PendaftaranPesertaTest`.
  - Verifikasi di browser bahwa seluruh teks terbaca sempurna dan tampilan simetris teratur.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Judul kartu *"Tugas Magang Mendesak"* dan *"Logbook Mingguan Terkini"* tampil utuh tanpa terpotong `...`.
- [x] Huruf awal teks kegiatan logbook tidak lagi terpotong di sebelah kiri.
- [x] Kartu Tugas dan Logbook memiliki ruang horizontal yang lega dan tidak saling menjepit.
- [x] Tata letak dashboard terlihat teratur, seimbang, dan proporsional.
- [x] `npm run build` dan PHPUnit test berstatus PASS.

