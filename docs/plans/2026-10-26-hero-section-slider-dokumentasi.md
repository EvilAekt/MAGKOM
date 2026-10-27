# Rencana Kerja: Slider Hero Section Beranda dengan Dokumentasi Magang Kominfo Sebelumnya

**Tanggal**: 26 Oktober 2026  
**Status**: Usulan Rencana (Menunggu Persetujuan User)  
**Dokumen**: `docs/plans/2026-10-26-hero-section-slider-dokumentasi.md`  
**Referensi Area**: Hero Box Beranda (`#beranda` pada `resources/views/public/landing.blade.php`)

---

## 1. Tujuan
Mengubah kotak Hero Beranda (*Hero Box*) pada halaman beranda landing page menjadi **slider / carousel multi-slide** yang mulus dan elegan:
- **Slide 1**: Menampilkan informasi pendaftaran magang saat ini (informasi Magang Kominfo Periode II 2026, Persyaratan Umum, Dokumen Pendaftaran, dan CTA *"Daftar Sekarang"*).
- **Slide 2 & Seterusnya**: Menampilkan galeri dokumentasi kegiatan magang Diskominfo Sleman sebelumnya (suasana magang di kantor Diskominfo Sleman, pengembangan aplikasi Aptika, monitoring jaringan/persandian, dan penyerahan sertifikat kelulusan).
- **Desain Tanpa Efek Hover (*No-Hover*)**: Sesuai permintaan pengguna, interaksi transisi dan kartu tidak menggunakan efek animasi *hover/scale/jitter* yang mengganggu saat kursor melintas, melainkan dikendalikan secara bersih melalui tombol navigasi panah (*Prev/Next*) dan indikator titik (*bullet dots*).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis (Bekerja Secara Logis)
1. **Navigasi Bersih (Tanpa Hover)**: Navigasi murni digerakkan oleh klik pengguna pada tombol panah (`<` dan `>`) serta titik pagination (`•`). State hover dinonaktifkan atau hanya berupa perubahan warna latar tombol yang sangat halus tanpa transform perbesaran/zoom.
2. **Struktur Multi-Slide**:
   - **Slide 1**: *Pendaftaran Magang Periode II 2026* (Layout 3 kolom yang ada saat ini: Detail & CTA, Persyaratan Umum, Dokumen Pendaftaran).
   - **Slide 2**: *Dokumentasi Kegiatan: Pengembangan Aplikasi & Sistem Informasi (Aptika)* (Dokumentasi kolaborasi koding, asistensi mentor, dan pengembangan inovasi layanan publik Sleman).
   - **Slide 3**: *Dokumentasi Kegiatan: Infrastruktur Jaringan & Pengamanan Siber (Persandian)* (Dokumentasi konfigurasi server NOC, keamanan data, dan pemeliharaan perangkat jaringan Kominfo).
   - **Slide 4**: *Dokumentasi Kegiatan: Bimbingan & Kelulusan Pemagang* (Dokumentasi presentasi akhir, penerimaan sertifikat resmi, dan testimoni alumni magang).
3. **Aset Dokumentasi**: Menggunakan aset gambar resmi yang relevan di `public/images/` dengan format kartu galeri yang rapi dan terstruktur.
4. **Performa & Animasi**: Dibuat secara native menggunakan CSS Flexbox + `transform: translateX(...)` yang diakselerasi oleh GPU, sehingga transisi sangat mulus 60 FPS di desktop maupun perangkat mobile.

---

## 3. Pendekatan Teknis & Evaluasi Opsi

### Perbandingan Opsi:
* **Opsi A: Native CSS + Vanilla JS Slider Engine (Rekomendasi Utama)**
  - *Kelebihan*:
    - **Zero External Dependencies**: Tidak memerlukan library tambahan (Slick / Swiper), menjaga ukuran halaman tetap ringan.
    - **Presisi Perilaku "No Hover"**: Kontrol 100% terhadap styling CSS tanpa ada efek bawaan library (seperti hover zoom kartu atau tilt).
    - **Responsif & Aksesibel**: Mendukung navigasi tombol keyboard (panah kiri/kanan), touch-swipe di mobile, dan tombol ARIA yang ramah screen-reader.
  - *Kekurangan*: Memerlukan penulisan logika slider di `landing-interactive.js`.

* **Opsi B: Integrasi Swiper.js / Splide via CDN**
  - *Kelebihan*: Fitur bawaan lengkap.
  - *Kekurangan*:
    - Menambah beban unduhan JavaScript & CSS pihak ketiga dari CDN.
    - Sering memiliki styling bawaan dengan efek hover yang harus dioverride secara agresif.
    - Berpotensi gagal dimuat di lingkungan intranet/offline.

**Keputusan**: Dipilih **Opsi A (Native Slider Engine)** untuk performa instan dan kebebasan kustomisasi visual tanpa efek hover.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/landing.blade.php` | Modify | Membungkus `.hero-box` ke dalam slider track dengan 3-4 slide (Slide 1: info pendaftaran, Slide 2-4: dokumentasi kegiatan sebelumnya), kontrol panah, dan titik indikator. |
| `public/css/landing-modern.css` | Modify | Menambahkan styling slider: `.hero-slider-wrapper`, `.hero-slider-track`, `.hero-slide-item`, `.hero-doc-grid`, kontrol tombol navigasi, dan memastikan tidak ada efek hover zoom. |
| `public/js/landing-interactive.js` | Modify | Menambahkan controller `initHeroSlider()` untuk menangani transisi slide, auto-slide opsional, update indikator aktif, dan navigasi tombol. |

---

## 5. Rincian Layout & Konten Tiap Slide

### Slide 1: Informasi Pendaftaran (Tampilan Eksisting)
- **Kolom 1**: *Magang Kominfo Periode II 2026*, tanggal pendaftaran, CTA *"Daftar Sekarang"*.
- **Kolom 2**: *Persyaratan Umum* (SMK/D3/S1, WFO kantor Sleman, jadwal).
- **Kolom 3**: *Dokumen Pendaftaran* (Surat Pengantar, CV, Transkrip/Rapor, Pas Foto).

### Slide 2: Dokumentasi Bidang Aplikasi Informatika (Aptika)
- Header: Badge *"Dokumentasi Magang Sebelumnya"* + Judul *"Pengembangan Aplikasi & Smart City Sleman"*.
- Konten Grid Dokumentasi:
  - Foto/ilustrasi ruang kerja & tim pemagang Aptika.
  - Deskripsi kegiatan: Pembuatan fitur layanan publik Pemkab Sleman, code review bersama mentor senior, dan eksplorasi framework modern.
  - Statistik pencapaian: *"12+ Aplikasi Dikembangkan oleh Peserta Magang"*.

### Slide 3: Dokumentasi Bidang Persandian & Infrastruktur Jaringan
- Header: Badge *"Dokumentasi Magang Sebelumnya"* + Judul *"Infrastruktur Jaringan & Pengamanan Siber"*.
- Konten Grid Dokumentasi:
  - Foto/ilustrasi Network Operation Center (NOC) Diskominfo Sleman.
  - Deskripsi kegiatan: Monitoring server intra-pemerintah, instalasi sertifikat elektronik, dan simulasi pengamanan lalu lintas data.
  - Statistik pencapaian: *"Pemeliharaan Jaringan di 86 Titik Layanan Sleman"*.

### Slide 4: Dokumentasi Kelulusan & Pengalaman Pemagang
- Header: Badge *"Dokumentasi Magang Sebelumnya"* + Judul *"Bimbingan Intensif & Sertifikasi Resmi"*.
- Konten Grid Dokumentasi:
  - Foto/ilustrasi penyerahan lembar penilaian & sertifikat magang.
  - Deskripsi: Evaluasi langsung bersama pembimbing dinas, portofolio profesional, dan peluang networking industri.
  - Testimoni ringkas alumni magang Diskominfo Sleman.

### Navigasi Slider (Bersih & Tanpa Hover):
- Tombol Panah `<` (Sebelumnya) dan `>` (Selanjutnya) yang terpasang di pojok kanan atas atau samping slider.
- Dot Indicators di bagian bawah slider (menunjukkan posisi slide aktif: 1, 2, 3, 4).
- Bebas dari animasi hover zoom pada gambar/kartu (gambar tetap stabil saat kursor berada di atasnya).

---

## 6. Langkah Implementasi Bertahap

- [x] **Langkah 1**: Desain Markup HTML Slider di Blade
  - Ubah area `#beranda` di `resources/views/public/landing.blade.php` menjadi struktur `.hero-slider-wrapper` yang menampung Slide 1 (informasi pendaftaran eksisting) dan Slide 2–4 (dokumentasi kegiatan sebelumnya).
  - Tambahkan tombol navigasi panah dan container bullet dots.
- [x] **Langkah 2**: Styling CSS Slider Tanpa Hover
  - Di `public/css/landing-modern.css`, tambahkan class flex-track, slide item width 100%, transisi halus `transform 0.45s ease-in-out`.
  - Atur layout kartu dokumentasi (grid 2–3 kolom dokumentasi dengan deskripsi kegiatan yang rapi).
  - Pastikan styling bebas dari `:hover { transform: scale(...) }` atau animasi jitter pada kartu.
- [x] **Langkah 3**: Logika Interaktif di JavaScript
  - Di `public/js/landing-interactive.js`, buat fungsi `initHeroSlider()` untuk mengelola indeks slide aktif, pergeseran track `translateX(-${index * 100}%)`, aktivasi indikator dots, dan auto-slide yang lembut (interval 7 detik, pause saat pengguna berinteraksi).
- [x] **Langkah 4**: Verifikasi Browser & Responsivitas
  - Uji pergeseran slide melalui klik panah kiri/kanan dan klik dots.
  - Pastikan tampilan di layar mobile (375px), tablet (768px), dan desktop (1440px) tetap proporsional dan tidak ada overflow.
  - Pastikan konsol browser tetap bersih (*0 errors*).

---

## 7. Strategi Testing

1. **Pengujian Fungsional**:
   - Klik tombol panah kanan `>` -> Slide berpindah mulus ke dokumentasi kegiatan magang sebelumnya.
   - Klik tombol panah kiri `<` -> Slide kembali ke informasi pendaftaran magang.
   - Klik salah satu titik indikator (dot) -> Slider langsung berpindah ke slide target yang sesuai.
   - Hover kursor di atas kartu -> Pastikan tidak ada gerakan melompat, zoom in, atau jitter pada gambar maupun teks.
2. **Pengujian Responsif**:
   - Mobile: Slider tetap rapi, teks dokumentasi mudah dibaca, dan tombol navigasi mudah ditekan dengan jari.
   - Desktop: Slider mengisi container secara proporsional dengan grid dokumentasi yang seimbang.
3. **Pengujian Error**:
   - Console DevTools: Nol error JavaScript (`0 errors`).
   - Server Response: Status `200 OK`.

---

## 8. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Keterbacaan Informasi Utama Berkurang** | Calon pendaftar kesulitan menemukan syarat pendaftaran | Slide 1 tetap menjadi slide utama (*default*) saat halaman pertama kali dibuka, dengan tombol *"Daftar Sekarang"* yang selalu terlihat jelas |
| **Beban Ukuran Gambar Dokumentasi** | Waktu muat halaman bertambah | Gunakan gambar yang dioptimasi dan format WebP/JPG terkompresi |
| **Pergerakan Slide Otomatis Mengganggu Pengguna** | Teks bergeser saat pengguna sedang membaca syarat | Berikan jeda yang cukup panjang (7–8 detik) dan jeda otomatis (*pause*) begitu pengguna menekan tombol kontrol |

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Hero Section berhasil diubah menjadi slider interaktif multi-slide.
- [x] Slide 1 memuat informasi pendaftaran magang saat ini (tanpa ada informasi yang hilang).
- [x] Slide 2–4 memuat dokumentasi kegiatan magang Kominfo sebelumnya (Aptika, Persandian/Jaringan, Bimbingan & Kelulusan).
- [x] Navigasi slider menggunakan tombol panah dan titik indikator yang responsif.
- [x] Bebas dari efek hover/zoom pada kartu dokumentasi (*No Hover Jitter*).
- [x] Berjalan mulus di mobile, tablet, dan desktop.
- [x] Konsol browser bebas error (Zero JS Error).
