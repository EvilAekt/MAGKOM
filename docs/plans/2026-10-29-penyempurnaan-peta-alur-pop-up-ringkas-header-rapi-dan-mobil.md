# Rencana Kerja: Penyempurnaan Peta Alur Interaktif — Pop-Up Checkpoint Ringkas Sesuai Gambar Referensi, Header Rapi, Animasi Penuh, & Audio Friendly

Dokumen rencana kerja ini merespons umpan balik pengguna:
> *"animasinya kek kurang penuh ga sih dan kenapa bukan mobil,kemudian header nya masih belom sesuai dengan tema magkom,itu bulan dan tanggalnya di hapus saja terus isi dari setiap cek poin diisi gambar ke dua yang kanan itu aja ga sih nanti kalau sampai di cek poin nya muncul pop up seperti pada gambar ke dua itu jadi yang gambar paling kanan itu nanti di ubah masuk ke dalam animasi interaktif nya dan tolong sound nya di ubah yang lebih friendly,dan itu tampilan di header masih berantakan buat plan nya dulu"*

Dokumen ini disusun sesuai panduan **Web Planning Workflow** untuk merancang perbaikan menyeluruh sebelum penulisan kode dimulai.

---

## 1. Analisis Masalah Saat Ini & Kebutuhan Pengguna

| Aspek | Kondisi Saat Ini (Berdasarkan Screenshot) | Solusi & Hasil yang Diharapkan |
| :--- | :--- | :--- |
| **1. Kanvas Kurang Penuh (*Not Full Bleed*)** | Kanvas menggunakan `object-fit: contain` dengan viewBox 960x540 sehingga muncul ruang kosong (*letterbox*) di kiri/kanan pada layar lebar. | Ubah menjadi **100% Edge-to-Edge Full Viewport**: Perlebar elemen lanskap SVG (langit, Merapi, bukit, dan jalanan) serta gunakan styling `object-fit: cover` / `preserveAspectRatio="xMidYMid slice"` agar pemandangan memenuhi layar secara penuh tanpa ruang kosong. |
| **2. Mobil Dinas Tersangkut di Pojok Kiri Atas** | Mobil diinisialisasi pada `translate(0, 0)` dan tersangkut di balik header kiri atas saat awal render, sehingga pengguna tidak melihat mobil di atas jalan. | Posisikan mobil dinas secara presisi **langsung di Pos 1 pada atas jalan aspal** sejak render pertama (`transform="translate(195, 140) rotate(12)"`), lengkapi detail mobil dinas SUV Kominfo (roda, jendela, sirip, strip navy) yang nyata meluncur di atas aspal. |
| **3. Header Berantakan & Belum Sesuai MAGKOM** | Filter jenjang dan tipe bertumpuk di tengah dengan 2 baris bersama dropdown bulan (`Juli 2026`), tombol saling menimpa dan memotong batas atas. | **Hapus dropdown bulan & filter bertumpuk**. Rancang header bersih satu baris (*Single-Bar Clean Civic Header*):<br>• Kiri: Tombol `[← Beranda]` + Badge `PETA ALUR INTERAKTIF` + Judul `Simulator Magang Diskominfo Sleman`<br>• Tengah: Bersih & lega tanpa elemen bertumpuk<br>• Kanan: Tombol `[🔊 Audio FX]`, `[▶ Tour Otomatis]`, dan `[Daftar Sekarang ➔]` hijau khas MAGKOM. |
| **4. Pop-up Checkpoint Sesuai Gambar 2 (Bukan Kartu Panjang Gambar 3)** | Saat ini ada kartu HUD panjang di kanan layar yang memakan banyak tempat dengan tanggal, checklist, dan notice box tebal (seperti Gambar 3). | **Ganti total dengan Pop-up Mengambang (*Floating Modal Pop-up*) sesuai Gambar 2**:<br>Ketika mobil sampai di checkpoint atau checkpoint diklik, muncul pop-up ringkas di dekat checkpoint berisi:<br>• **Nomor Tahap Besar (01 / 02 / 03 / 04)** berwarna identitas<br>• **JUDUL TAHAP BESAR & JELAS**<br>• **1–2 kalimat deskripsi ringkas padat** (tanpa tanggal & tanpa checklist panjang)<br>• Tombol kecil `[Lanjut ke Pos Berikutnya ➔]` atau `[Daftar Sekarang 🚀]`. |
| **5. Tanggal & Estimasi Waktu** | Terdapat teks tanggal seperti "15 Mei – 31 Mei 2026" di header dan bottom bar yang membuat tampilan penuh. | **Hapus seluruh teks tanggal**. Di bilah bawah (*bottom bar*) cukup tampilkan **01 Registrasi**, **02 Administrasi & Seleksi**, **03 Pelaksanaan Magang**, **04 Kelulusan** tanpa tanggal. |
| **6. Kualitas Suara (*Audio FX*) Kurang Friendly** | Suara synthesizer sebelumnya menggunakan oscillator mentah yang terdengar kaku dan memiliki dengung ambient bising. | Ubah menjadi **Friendly Sound FX**: nada marimba/chime akustik lembut (*gentle pleasant chime chord* ala notifikasi modern/Duolingo), suara ketukan bubble lembut (*soft pop click*), dan hapus dengungan (*ambient drone*) agar ramah di telinga. |

---

## 2. Rincian Konten Pop-Up di Tiap Checkpoint (Mengacu Gambar 2)

Sesuai permintaan pengguna dan referensi visual Gambar 2, konten pop-up diubah menjadi sangat bersih dan lugas:

```
+-------------------------------------------------------------+
|  01                                                     [X] |
|  REGISTRASI                                                 |
|                                                             |
|  Calon peserta membuat akun, mengisi data pribadi, dan      |
|  menunggu akun diverifikasi admin.                          |
|                                                             |
|                                      [Lanjut ke Pos 2 ➔]    |
+-------------------------------------------------------------+
```

1. **Pos 1 (Checkpoint 1)**
   - **Nomor**: `01` (Aksen Merah Muda / Crimson Sleman `#e11d48`)
   - **Judul**: `REGISTRASI`
   - **Deskripsi**: *"Calon peserta membuat akun, mengisi data pribadi, dan menunggu akun diverifikasi admin."*
   - **Aksi**: Tombol `[Lanjut ke Pos 2 ➔]`

2. **Pos 2 (Checkpoint 2)**
   - **Nomor**: `02` (Aksen Biru Indigo `#0284c7`)
   - **Judul**: `ADMINISTRASI & SELEKSI`
   - **Deskripsi**: *"Pilih bidang, unggah berkas, lalu admin memverifikasi dan menyeleksi dalam 1–3 hari kerja."*
   - **Aksi**: Tombol `[Lanjut ke Pos 3 ➔]`

3. **Pos 3 (Checkpoint 3)**
   - **Nomor**: `03` (Aksen Biru Muda Sky `#0ea5e9`)
   - **Judul**: `PELAKSANAAN MAGANG`
   - **Deskripsi**: *"Kerjakan tugas, isi logbook harian, dan unggah progres sesuai durasi magang."*
   - **Aksi**: Tombol `[Lanjut ke Pos 4 ➔]`

4. **Pos 4 (Checkpoint 4)**
   - **Nomor**: `04` (Aksen Emas Sleman `#f59e0b`)
   - **Judul**: `KELULUSAN`
   - **Deskripsi**: *"Unduh Lembar Nilai Magang dan sertifikat dalam bentuk PDF dari portal."*
   - **Aksi**: Tombol `[Daftar Sekarang 🚀]`

---

## 3. Evaluasi Opsi Desain Tata Letak

### Opsi A: Pop-Up Melayang Mengambang Bebas di Tengah/Dekat Checkpoint (*Floating Diorama Card*) - **(Direkomendasikan)**
- **Mekanisme**: Pop-up berukuran ramping (320px x 180px) muncul dengan animasi zoom/fade lembut tepat di dekat checkpoint yang sedang aktif. Pop-up dapat ditutup dengan tombol silang (x) atau lanjut ke pos berikutnya.
- **Kelebihan**: Layar kanvas 100% terbuka, tidak ada sidebar permanen yang menghalangi lanskap pemandangan jalan dan Gunung Merapi. Persis seperti kartu di Gambar 2 yang diminta pengguna.
- **Kekurangan**: Memerlukan penyesuaian posisi otomatis agar tidak terpotong di tepi layar pada resolusi kecil (mudah diatasi dengan flex/center anchor di viewport).

### Opsi B: Mempertahankan Sidebar Kanan tapi Mengganti Isinya
- **Mekanisme**: Tetap menggunakan panel HUD di sisi kanan layar tetapi mengganti isinya dengan teks pendek Gambar 2.
- **Kekurangan**: Tetap memakan sepertiga layar di kanan, kanvas tetap terasa sempit dan tidak memberikan sensasi diorama terbuka.

> **Keputusan**: Menggunakan **Opsi A (Floating Diorama Card Pop-Up)** untuk memaksimalkan kelegaan kanvas dan memenuhi keinginan pengguna secara presisi.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/peta-alur-interaktif.blade.php` | Modify | 1. **Header**: Hapus filter jenjang/tipe yang berantakan dan dropdown bulan. Rapikan tata letak menjadi satu baris bersih sesuai standar portal MAGKOM.<br>2. **SVG & Mobil**: Perlebar elemen lanskap dan jalan agar membentang penuh (full-bleed). Berikan posisi awal mobil di Pos 1 (`translate(195, 140)`) agar mobil langsung terlihat di atas jalan sejak detik pertama.<br>3. **Pop-up Card**: Hapus kartu panjang lama (HUD Gambar 3), buat komponen pop-up kartu mengambang bersih sesuai Gambar 2 (nomor besar 01-04, judul tebal, 1-2 kalimat deskripsi, tombol lanjut).<br>4. **Bottom Bar**: Hapus teks tanggal estimasi ("15 Mei - 31 Mei 2026"), ganti dengan nomor dan nama pos yang ringkas (01 Registrasi, 02 Administrasi & Seleksi, 03 Pelaksanaan Magang, 04 Kelulusan).<br>5. **Friendly Web Audio**: Rombak synthesizer menjadi chime akustik/marimba ramah bertangga nada mayor yang lembut dan menyenangkan tanpa nada dengung. |
| `public/css/peta-alur-fullscreen.css` | Modify | 1. **Header CSS**: Atur flex layout satu baris yang rapi dan responsif, hilangkan CSS filter bertingkat dan dropdown bulan.<br>2. **Canvas Full Bleed**: Ganti `object-fit: contain` menjadi `object-fit: cover` dan pastikan viewport 100vw x 100vh tanpa sela letterboxing.<br>3. **Floating Pop-up Styling**: Styling kartu pop-up Gambar 2 (background putih bersih, shadow lembut modern, nomor besar 32px warna-warni, tipografi Lexend rapi, animasi pop-in halus).<br>4. **Bottom Bar CSS**: Rapikan bilah tahapan bawah tanpa baris tanggal sekunder agar proporsional dan bersih. |
| `tests/Feature/PetaAlurPageTest.php` | Modify | Sesuaikan assertions test untuk memvalidasi: header bersih, mobil dinas pos 1, pop-up tahapan baru Gambar 2 (01 Registrasi s/d 04 Kelulusan), dan ketiadaan teks tanggal/dropdown bulan yang telah dihapus. |

---

## 5. Langkah Implementasi Terstruktur

### Tahap 1: Pembersihan & Perapihan Header Sesuai Tema MAGKOM
- [x] Buka [resources/views/public/peta-alur-interaktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php).
- [x] Hapus elemen dropdown bulan `#modalFilterBulan` beserta options tanggalnya.
- [x] Hapus filter bertingkat `filter-pill-group` yang menumpuk di header.
- [x] Tata ulang header menjadi struktur 3 kolom yang seimbang dan simetris:
  - Sisi Kiri: Tombol `[← Beranda]` + Lambang/Badge Sleman + Judul `Simulator Magang Diskominfo Sleman`.
  - Sisi Kanan: Tombol `[🔊 Audio FX]`, `[▶ Tour Otomatis]`, dan `[Daftar Sekarang ➔]` hijau MAGKOM.
- [x] Sesuaikan styling di [public/css/peta-alur-fullscreen.css](file:///d:/MAGKOM/MAGKOM/public/css/peta-alur-fullscreen.css) agar header 60px kokoh, rapi, dan tidak ada elemen berantakan.

### Tahap 2: Tampilan Penuh (Edge-to-Edge) & Mobil Dinas di Atas Jalan
- [x] Buka [public/css/peta-alur-fullscreen.css](file:///d:/MAGKOM/MAGKOM/public/css/peta-alur-fullscreen.css) dan ubah `.panoramic-svg-canvas` agar memenuhi viewport secara penuh (`object-fit: cover` / aspect ratio terjaga tanpa letterbox putih).
- [x] Buka [resources/views/public/peta-alur-interaktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php):
  - Perlebar latar langit, gunung, dan tanah SVG agar membentang leluasa melampaui batas tepi kanvas.
  - Perbaiki posisi awal default `#modalRoadCarGroup` agar tidak di `(0, 0)` di pojok kiri atas, melainkan langsung di Pos 1 di atas jalan aspal.
  - Sempurnakan detail visual mobil dinas agar tampak jelas sebagai mobil dinas Diskominfo (bodi putih-navy, roda hitam di aspal, lampu strobo halus).
  - Pastikan fungsi JavaScript `initRoadGeometry()` memposisikan mobil langsung di checkpoint 1 saat halaman selesai dimuat.

### Tahap 3: Implementasi Pop-Up Checkpoint Sesuai Gambar 2 & Pembersihan Tanggal
- [x] Di [resources/views/public/peta-alur-interaktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php), ganti struktur `#missionHudOverlay` lama dengan **Floating Checkpoint Pop-Up Card** yang mengacu pada Gambar 2.
- [x] Perbarui dataset JavaScript `stepData`:
  - Masukkan teks resmi persis dari Gambar 2 untuk Pos 01, 02, 03, dan 04.
  - Hapus dataset `timelineEstimates` dan tanggal-tanggal (15 Mei, 1 Juni, dll).
- [x] Buat logika JS: setiap kali mobil sampai di suatu checkpoint (atau tab diklik), kartu pop-up checkpoint tersebut langsung muncul di atas diorama dengan animasi pop yang mulus.
- [x] Berikan tombol `[Lanjut ke Pos Berikutnya ➔]` di dalam pop-up (dan tombol `[Daftar Sekarang 🚀]` di pos 4).
- [x] Bersihkan bilah tab bawah (`.peta-alur-bottom-bar`): hapus `#roadTabScore1` s/d `#roadTabScore4` (tanggal) dan ganti dengan label tahapan yang bersih dan proporsional.

### Tahap 4: Audio FX Baru yang Ramah & Menyenangkan (*Friendly Sound FX*)
- [x] Rombak synthesizer Web Audio API pada objek `MagkomSound`:
  - Hapus nada oscillator drone latar belakang yang bising.
  - Buat efek suara **Klik**: nada lembut bubble pop (sinewave hangat ~500Hz ke 250Hz, durasi 40ms, ramah di telinga).
  - Buat efek suara **Sampai di Pos (Arrival Chime)**: arpeggio tangga nada mayor yang ceria dan menenangkan (C5 - E5 - G5 - C6, volume lembut, decay halus ala chime mobile app).
  - Buat efek suara **Selesai (Success Chime)**: melodi penyelesaian yang hangat dan memuaskan.
  - Pastikan tombol audio toggle memiliki status jelas (Audio: Aktif / Senyap).

### Tahap 5: Pengujian Otomatis & Verifikasi
- [x] Perbarui assertions di [tests/Feature/PetaAlurPageTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/PetaAlurPageTest.php) agar memverifikasi:
  - Header bersih tanpa dropdown bulan.
  - Keberadaan mobil dinas dan checkpoint pop-up baru.
  - Teks tahapan Gambar 2 (Registrasi, Administrasi & Seleksi, Pelaksanaan Magang, Kelulusan).
  - Bottom tab bar tanpa tanggal.
- [x] Jalankan suite automated testing:
  - `php artisan test --filter=PetaAlurPageTest`
  - `php artisan test --filter=LandingPagePetaAlurModalTest`
  - `php artisan test --filter=AlurMagangPageTest`
- [x] Jalankan Pint code styling: `vendor/bin/pint tests/Feature/PetaAlurPageTest.php --test`.
- [x] Verifikasi respon HTTP server lokal: `curl -I -H "Host: magkom.test" http://127.0.0.1/peta-alur`.

---

## 6. Kriteria Selesai (Definition of Done)
- [x] Kanvas pemandangan diorama membentang penuh (*edge-to-edge full bleed*) tanpa ada ruang kosong atau letterboxing yang aneh.
- [x] Mobil dinas berada tepat di atas jalan aspal sejak awal halaman dibuka (tidak tersangkut di pojok kiri atas (0, 0)) dan melaju mulus menyusuri jalan ke tiap checkpoint.
- [x] Header tampil rapi, bersih satu baris, simetris, bebas dari elemen bertumpuk, serta dropdown bulan dan filter yang berantakan telah dihapus.
- [x] Kartu HUD panjang lama dihilangkan, digantikan oleh pop-up melayang di tiap checkpoint yang memuat nomor besar (01-04), judul tahap, dan 1-2 kalimat ringkas persis seperti Gambar 2.
- [x] Seluruh tanggal estimasi ("15 Mei - 31 Mei 2026", dsb) telah dihapus dari header dan bilah bawah.
- [x] Efek suara (*audio FX*) terdengar ramah, hangat, dan menyenangkan (chime lembut ala mobile app modern) tanpa suara dengung/sirene.
- [x] Seluruh automated test lolos 100% hijau.
