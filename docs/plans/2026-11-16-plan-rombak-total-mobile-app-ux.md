# Rencana Rombak Total Tampilan Mobile MagKom (Native Mobile App Experience)

Berdasarkan permintaan terbaru pengguna:
1. **Latar Animasi Biru di Login/Register**: Harus tetap muncul dan aktif, tanpa terpotong atau menyisakan ruang gelap kosong di bawah.
2. **Hero Banner & Foto Dokumentasi**: Sembunyikan Slide 2 (foto dokumentasi) dan dot indikator di mode mobile, jadikan Hero sebagai 1 kartu statis yang solid, informatif, dan ringkas layaknya aplikasi mobile.
3. **Penempatan & Kuota (Carousel)**: Ubah setiap kartu penempatan & jurusan menjadi **1 halaman penuh (100% width)** yang rapi, sehingga tidak ada kartu kedua yang terpotong di tepi kanan saat dibaca, dan pengguna dapat menggeser (*swipe*) halaman demi halaman layaknya Instagram carousel atau Google Card.

---

## 1. Detail Rencana Perubahan per Bagian

### A. Halaman Login & Register: Mobile App Bottom Sheet dengan Animasi Biru Aktif
- **Masalah Saat Ini**:
  - Animasi kanvas biru sempat dinonaktifkan (`display: none`), sehingga layar menjadi putih polos biasa.
  - Sebelumnya, kartu login mengambang seperti kotak modal desktop dengan latar kosong gelap yang luas di bagian bawah.
- **Solusi Rombak (Mobile App Sheet Style)**:
  1. **Aktifkan Kembali 3D Particle Canvas (`data-auth-canvas`)**:
     - Canvas tetap aktif di latar belakang mobile dengan gradien biru Sleman resmi (`#003566` ke `#01183A`).
     - Jalankan partikel bintang/kamera secara mulus di mobile.
  2. **Desain Mobile App Top Header + Bottom Sheet**:
     - **Bagian Atas (Header Area ~80px)**:
       - Memperlihatkan latar belakang animasi biru Sleman yang hidup.
       - Terdapat tombol navigasi `← Beranda` dan logo lambang Pemkab Sleman dengan teks putih elegan (`MAGKOM SLEMAN`).
     - **Bagian Bawah (White App Sheet)**:
       - Kartu formulir putih dimulai dari bawah header dengan sudut melengkung modern (`border-radius: 28px 28px 0 0`).
       - Lembaran putih ini memanjang ke bawah hingga dasar layar (`min-height: calc(100dvh - 80px)`), dengan bayangan lembut (*elevation shadow*).
       - Menampung judul *"Selamat datang"*, input formulir, captcha keamanan yang rapi, tombol *"Masuk"*, dan Google SSO.
  3. **Keuntungan**:
     - Animasi biru terlihat jelas dan memukau di bagian atas.
     - Formulir login tidak lagi menggantung seperti modal desktop, melainkan menyatu kokoh seperti aplikasi mobile modern (Gojek/BCA/Lentera Sleman).

---

### B. Landing Page Hero: Hapus Slide 2 di Mobile, 1 Kartu Statis Native App
- **Masalah Saat Ini**:
  - Slide 2 (foto dokumentasi) membuat Hero di HP terasa canggung karena perbedaan tinggi dengan Slide 1, serta navigasi geser 2 slide yang tidak diperlukan calon pendaftar.
- **Solusi Rombak**:
  1. Pada tampilan mobile ($\le 767.98\text{px}$):
     - **Sembunyikan Slide 2 sepenuhnya** (`.hero-slide-item:nth-child(2) { display: none !important; }`).
     - **Sembunyikan dot indikator & tombol panah** (`.hero-slider-dots, .hero-side-nav-btn { display: none !important; }`).
     - Set track slider menjadi 100% statis (`transform: none !important; width: 100% !important;`).
  2. **Susunan 1 Kartu Hero Mobile**:
     - Header Sleman: *"KABUPATEN SLEMAN"* & *"Magang Kominfo Periode II 2026"*.
     - Sub-kartu 1: *"Persyaratan Umum"* (putih bersih dengan garis aksen biru).
     - Sub-kartu 2: *"Dokumen Pendaftaran"* (putih bersih dengan garis aksen biru).
     - Tombol CTA: *"Daftar Sekarang"* tunggal full-width di bagian bawah.
  3. **Desktop Parity**:
     - Di layar desktop ($\ge 768\text{px}$), carousel 2 slide dan foto penuh dokumentasi kantor Diskominfo tetap aktif 100%.

---

### C. Carousel Penempatan & Kuota: 1 Halaman Penuh (100% Width) per Kartu
- **Masalah Saat Ini**:
  - Seperti terlihat pada gambar `media_1794832539755_b60c9f73.png`, kartu kuota berukuran `88%`, sehingga kartu kedua mengintip dan terpotong vertikal di sisi kanan layar.
- **Solusi Rombak (Full-Page Card Swiper)**:
  1. Atur lebar kartu kuota di mobile ($\le 768\text{px}$) menjadi **100% penuh**:
     ```css
     .kuota-carousel-wrapper {
         overflow: hidden !important;
         width: 100% !important;
         padding: 0 !important;
     }
     .kuota-carousel-track {
         gap: 0 !important;
         padding: 0.5rem 0 1rem 0 !important;
         scroll-snap-type: x mandatory !important;
         -webkit-overflow-scrolling: touch !important;
     }
     .kuota-card {
         flex: 0 0 100% !important;
         width: 100% !important;
         min-width: 100% !important;
         max-width: 100% !important;
         scroll-snap-align: start !important;
         scroll-snap-stop: always !important;
         box-sizing: border-box !important;
     }
     ```
  2. **Hasil Visual**:
     - Setiap kartu (misal: *Aplikasi Informatika & E-Government*) tampil penuh 100% di layar HP tanpa ada kartu sebelah yang terpotong.
     - Pengguna menggeser (*swipe*) kartu per kartu secara presisi (*snap-stop*), seperti melihat slide story / kartu mobile app.
     - Teks judul, jumlah kursi tersisa, deskripsi, dan tombol klik jurusan terlihat sangat lega dan rapi.

---

## 2. Alur Eksekusi & File yang Disentuh

1. [resources/css/pages/public/landing.css](file:///d:/MAGKOM/MAGKOM/resources/css/pages/public/landing.css) & [public/css/landing-modern.css](file:///d:/MAGKOM/MAGKOM/public/css/landing-modern.css):
   - Hero mobile: sembunyikan Slide 2 dan dots di mobile, pastikan 1 kartu solid.
   - Kuota carousel: ubah `.kuota-card` menjadi `flex: 0 0 100% !important; width: 100% !important;`.
2. [resources/css/pages/auth/auth-depth.css](file:///d:/MAGKOM/MAGKOM/resources/css/pages/auth/auth-depth.css) & [resources/views/layouts/auth-depth.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/auth-depth.blade.php):
   - Pertahankan canvas partikel aktif di mobile.
   - Tata letak Top Header transparan/navy + White App Sheet melengkung ke bawah.
3. **Quality Gate**:
   - `npm run build`
   - `./vendor/bin/pint --test`
   - `php artisan test --filter="Landing|AuthDepth|PesertaPendaftaran"`

