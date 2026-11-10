# Rencana Kerja: Full Animasi Sinematik Tanpa Checklist — Poin Ringkas di Dalam Scene Animasi

Dokumen rencana kerja ini merespons masukan pengguna: *"kalo ngga ceklist nya di hapus aja nanti ganti teks nya masukin ke dalam animasinya sedikit aja ppoin2 nya"*. Rencana ini mentransformasikan halaman simulator `/peta-alur` menjadi **Pengalaman Animasi Penuh 100% Layar (*100% Immersive Diorama View*)** dengan menghapus sidebar checklist yang memakan ruang, dan menggantikannya dengan **Kartu Info Poin Ringkas (*In-Scene Holographic Stage HUD*)** yang melayang manis langsung di dalam lanskap animasi.

---

## 1. Tujuan
1. **Total Full Screen Animasi (100% Layar Bersih & Sinematik)**:
   - Menghapus sidebar checklist di sisi kiri sehingga kanvas panorama SVG mengisi 100% area kerja (*edge-to-edge* antara topbar dan bottom timeline bar).
   - Seluruh diorama Sleman (Gunung Merapi, Menara & Kantor Diskominfo Sleman, lampu sorot mobil SUV, dan 4 karakter animasi) tampil sangat leluasa, megah, dan spektakuler.
2. **Penyajian Informasi Ringkas di Dalam Animasi (*In-Scene Stage Points*)**:
   - Menghilangkan checklist centang panjang yang rumit.
   - Menggantikannya dengan kartu holografis ringkas (*In-Scene Glass HUD*) yang melayang elegan di dalam kanvas diorama.
   - Menampilkan hanya **2–3 poin penting tahapan** secara to-the-point dan mudah dicerna sekilas oleh calon peserta magang.
3. **Interaktivitas Tetap Seru & Dinamis**:
   - Saat mobil melaju dan tiba di tiap pos (atau saat tab bawah diklik), kartu info poin bertransisi secara halus (*fade-slide animation*) menyajikan poin tahap tersebut.
   - Dilengkapi tombol navigasi cepat `[◀ Pos Sebelumnya]` dan `[Lanjut Pos Berikutnya ▶]` atau tombol aksi utama `[Mulai Pendaftaran 🚀]`.
   - Opsi tombol *Hide/Show Info* untuk menyembunyikan kartu menjadi pil minimalis jika pengguna ingin menikmati pemandangan animasi secara murni (*pure cinema*).

---

## 2. Perbandingan Pendekatan Desain

| Aspek | Opsi A (Direkomendasikan): In-Scene Glassmorphism Stage Card | Opsi B: Teks di Dalam Balun SVG (Speech Bubble di Karakter) |
| :--- | :--- | :--- |
| **Bentuk Tampilan** | Kartu semi-transparan ramping (*glass HUD*, lebar ~330px) melayang anggun di dalam viewport kanvas, dilengkapi animasi pendar. | Balun percakapan SVG `<text>` / `<foreignObject>` tepat di atas masing-masing karakter di dalam gambar SVG. |
| **Keterbacaan Teks** | **Sangat Baik**. Teks HTML/CSS tajam (*crisp*), tidak terdistorsi oleh resolusi SVG atau perbesaran kamera (kamera zoom in/out tidak membuat teks pecah/mengecil drastis). | **Kurang Optimal**. Saat kamera di-zoom out untuk melihat seluruh panorama, teks di dalam balun SVG mengecil dan sulit dibaca. |
| **Poin-poin Tahap** | Ringkas: 2–3 butir poin kunci per tahap + estimasi waktu + tombol navigasi pos berikutnya. | Sangat terbatas: hanya 1 kalimat pendek per karakter agar balun SVG tidak menutupi karakter itu sendiri. |
| **Responsif Mobile** | Sangat fleksibel; otomatis mengecil atau menempel rapi di bagian bawah layar tanpa menutupi mobil. | Rentan terpotong oleh `viewBox` dan *aspect ratio* layar sempit. |

**Keputusan**: Dipilih **Opsi A (In-Scene Glassmorphism Stage Card)** karena memberikan keseimbangan terbaik antara visual 100% full animasi, tipografi yang sangat tajam dan mudah dibaca, serta pengalaman interaktif yang modern.

---

## 3. Rancang Bangun Tata Letak (Wireframe Antarmuka Baru)

```
+----------------------------------------------------------------------------------------------------------------+
| [TOPBAR: SLEMAN DISKOMINFO]  [← Beranda]   [Filter: Jenjang | Tipe | Bulan]   [Audio FX: On] [Tour] [Daftar 🚀]|
+----------------------------------------------------------------------------------------------------------------+
| [KANVAS PANORAMA 100% LEBAR PENUH - TANPA SIDEBAR KIRI]                                                       |
|                                                                                                                |
|  [Zoom Controls]                                    +----------------------------------------------------+     |
|   (+) (-) (Semua)                                   | 🌟 TAHAP 1: REGISTRASI AKUN  •  1–2 Hari Kerja      |     |
|                                                     +----------------------------------------------------+     |
|                                                     | Poin Penting:                                      |     |
|     (Siluet Gunung Merapi & Bintang Sleman)         | • Lengkapi data diri (NIK/NIM, pas foto, HP aktif) |     |
|                                                     | • Verifikasi akun via tautan email resmi           |     |
|                                                     |                                                    |     |
|      [Pos 1] -------------------> [Pos 2]           | [◀ Prev]                  [Lanjut ke Pos 2 ➔]      |     |
|       (Karakter Mengetik)           (Stempel Dokumen) +----------------------------------------------------+     |
|         \                                                                                                      |
|          \ ===> Mobil Melaju Lampu Sorot                                                                       |
|           \                                                                                                    |
|            [Pos 3] ------------> [Pos 4: Finish Diskominfo]                                                    |
|          (Mentor ASN)               (Karakter Wisuda & Konfeti)                                                |
|                                                                                                                |
+----------------------------------------------------------------------------------------------------------------+
| [BOTTOM DOCK: TIMELINE CHECKPOINTS 1, 2, 3, 4]                                                                 |
+----------------------------------------------------------------------------------------------------------------+
```

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/peta-alur-interaktif.blade.php` | Modify | Hapus `<aside class="peta-alur-sidebar">` checklist. Ubah `peta-alur-viewport` menjadi kontainer 100% penuh layar. Tambahkan komponen kartu info mengambang di dalam viewport (`<div class="in-scene-stage-card" id="modalStepDetailPanel">`) yang menyajikan 2-3 poin ringkas per tahap dengan tombol navigasi pos. |
| `public/css/peta-alur-fullscreen.css` | Modify | Hapus styling sidebar 390px. Terapkan tata letak 100% full screen pada viewport kanvas. Buat styling kartu pendar holografis in-scene (`.in-scene-stage-card`, `.stage-points-list`, `.btn-stage-nav`) yang ramping, elegan, dan semi-transparan. |
| `tests/Feature/PetaAlurPageTest.php` | Modify | Perbarui assertions: pastikan tidak ada sidebar pemotong, pastikan kartu poin ringkas in-scene dan seluruh diorama panorama tetap terverifikasi 100% hijau. |

---

## 5. Rincian Poin-Poin Ringkas per Tahapan

1. **Tahap 1: Registrasi Akun** (`1–2 Hari Kerja`)
   - • Lengkapi identitas (NIK/NIM, kontak aktif, dan pas foto formal).
   - • Verifikasi tautan aktivasi akun yang dikirim ke email terdaftar.
2. **Tahap 2: Pengajuan Formasi Bidang** (`3–7 Hari Kerja`)
   - • Pilih bulan pelaksanaan & bidang yang kuotanya masih tersedia.
   - • Unggah surat pengantar resmi, proposal, CV, dan transkrip nilai.
3. **Tahap 3: Pelaksanaan & Bimbingan** (`1–6 Bulan`)
   - • Pembekalan orientasi kerja & pendampingan oleh mentor Diskominfo.
   - • Pelaksanaan penugasan proyek dan pengisian logbook mingguan rutin.
4. **Tahap 4: Kelulusan & E-Sertifikat** (`1–3 Hari Kerja`)
   - • Evaluasi penilaian kinerja menyeluruh dari pembimbing lapangan.
   - • Unduh E-Sertifikat Resmi bertanda tangan digital (TTE & QR Code).

---

## 6. Langkah Implementasi Terstruktur

### Tahap 1: Pembaruan Blade Template
- [x] Buka [resources/views/public/peta-alur-interaktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php).
- [x] Hapus elemen sidebar misi (`<aside class="peta-alur-sidebar">`) dan jadikan `<main class="peta-alur-viewport">` mengisi 100% ruang kerja.
- [x] Tempatkan kontainer `<div class="in-scene-stage-hud" id="missionHudOverlay">` di dalam `peta-alur-viewport`.
- [x] Perbarui dataset JavaScript `stepData` dengan menambahkan properti `keyPoints: [...]` (2-3 poin per tahap).
- [x] Perbarui fungsi `renderStepDetail(stepNum)` untuk merender kartu poin ringkas in-scene (chip tahap, jadwal, poin bullet elegan, dan tombol navigasi).

### Tahap 2: Styling CSS Full Canvas & In-Scene Glass HUD
- [x] Buka [public/css/peta-alur-fullscreen.css](file:///d:/MAGKOM/MAGKOM/public/css/peta-alur-fullscreen.css).
- [x] Atur `.peta-alur-viewport` agar mengisi 100% lebar layar (`width: 100%`, `height: 100%`).
- [x] Terapkan desain *glassmorphism HUD* pada `.in-scene-stage-hud`:
  - `position: absolute; top: 1.5rem; right: 1.5rem; width: 340px;`
  - Latar semi-transparan `rgba(10, 18, 34, 0.85)` dengan `backdrop-filter: blur(14px)` dan aksen neon biru.
  - Tampilan bullet poin dengan ikon pendar dan tipografi yang tajam.
  - Opsi tombol minimize menjadi badge pil kecil agar kanvas bisa dilihat 100% murni tanpa teks.
- [x] Penyesuaian responsif mobile agar kartu info in-scene menempel di bagian bawah secara proporsional.

### Tahap 3: Pengujian Otomatis & Verifikasi
- [x] Perbarui assertions di [tests/Feature/PetaAlurPageTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/PetaAlurPageTest.php) sesuai antarmuka baru.
- [x] Jalankan automated feature tests:
  - `php artisan test --filter=PetaAlurPageTest`
  - `php artisan test --filter=LandingPagePetaAlurModalTest`
  - `php artisan test --filter=AlurMagangPageTest`
- [x] Jalankan pengecekan code style: `vendor/bin/pint tests/Feature/PetaAlurPageTest.php --test`.
- [x] Verifikasi respons web server: `curl -I -H "Host: magkom.test" http://127.0.0.1/peta-alur` (200 OK).

---

## 7. Kriteria Selesai (Definition of Done)
- [x] Checklist panjang dan sidebar pemotong layar di sisi kiri telah dihapus 100%.
- [x] Kanvas pemandangan diorama SVG tampil 100% penuh layar (*edge-to-edge*).
- [x] Setiap tahapan menampilkan 2–3 poin ringkas di dalam kartu melayang (*in-scene stage card*) yang tidak menutupi jalan atau animasi.
- [x] Mobil sinematik melaju dengan lampu sorot halogen dan kamera auto-zoom tetap bekerja mulus.
- [x] Seluruh automated test lulus 100% hijau.
