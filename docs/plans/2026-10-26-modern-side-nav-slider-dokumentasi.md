# Rencana Kerja: Modernisasi Posisi Navigasi Slider ke Samping & Penerapan Foto Dokumentasi Resmi Eksisting

**Tanggal**: 26 Oktober 2026  
**Status**: Usulan Rencana (Menunggu Persetujuan User)  
**Dokumen**: `docs/plans/2026-10-26-modern-side-nav-slider-dokumentasi.md`  
**Area Terkait**: Slider Hero Section Beranda (`#beranda` pada `resources/views/public/landing.blade.php`)

---

## 1. Tujuan
Menyempurnakan antarmuka Slider Hero Section Beranda sesuai masukan pengguna:
1. **Relokasi Tombol Navigasi ke Samping (Modern Floating Side Arrows)**:
   - Menghilangkan toolbar kontrol navigasi yang sebelumnya berada di atas hero box.
   - Menggantinya dengan sepasang tombol panah navigasi melayang (*floating side navigation buttons*) di sisi kiri dan kanan slider setinggi garis tengah vertikal (*vertical centered*).
   - Tampilan bersih, minimalis modern, dan **tetap tanpa efek animasi hover (*no-hover zoom/jitter*)**.
2. **Penerapan Gambar Dokumentasi Resmi Eksisting**:
   - Memanfaatkan aset foto dokumentasi otentik yang pernah diunggah pada landing page dan desain Figma sebelumnya:
     - `public/hero-diskominfo.png` (Foto dokumentasi utama Diskominfo Sleman).
     - `public/images/hero_office_bg.png` (Foto dokumentasi suasana ruang kerja kantor Diskominfo).
     - `public/images/dashboard_hero_bg.jpg` (Foto kegiatan operasional).
     - `public/images/pendaftaran_banner_bg.jpg` (Foto kegiatan penerimaan & administrasi pemagang).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis (Bekerja Secara Logis)
1. **Posisi & Tampilan Tombol Samping**:
   - Tombol diletakkan di sisi kiri (`left: 0.75rem` atau `-1rem` pada desktop) dan sisi kanan (`right: 0.75rem` atau `-1rem` pada desktop), terpusat secara vertikal (`top: 50%; transform: translateY(-50%)`).
   - Berbentuk lingkaran modern (`44px`), berlatar belakang putih bersih dengan bayangan lembut (*box-shadow: 0 4px 14px rgba(0,0,0,0.12)*), dan ikon chevron yang tajam.
   - Sesuai arahan pengguna (*"tanpa ada hovernya"*), tombol tidak memiliki animasi perbesaran (*scale/zoom/bounce*) saat disentuh kursor.
2. **Kesesuaian Aset Gambar**:
   - Menggunakan foto asli yang telah tersimpan di repositori proyek tanpa memerlukan unduhan aset eksternal baru.
3. **Indikator Titik (*Dot Pagination*)**:
   - Tetap dipertahankan di bagian bawah slider untuk memberikan visualisasi jelas mengenai posisi slide yang aktif.

---

## 3. Pendekatan Teknis & Evaluasi Opsi

### Perbandingan Opsi:
* **Opsi A: Absolute Positioned Floating Side Arrows + Scoped CSS (Rekomendasi Utama)**
  - *Kelebihan*:
    - **Tampilan Modern & Rapi**: Menyerupai standar UI/UX modern (seperti carousel pada situs teknologi global).
    - **Penghematan Ruang Vertikal**: Menghilangkan baris header kontrol atas sehingga hero box langsung terlihat tanpa terpotong.
    - **Responsif & Ergonomis**: Tombol mudah dijangkau di layar sentuh maupun kursor desktop.
    - **No-Hover Compliance**: CSS diatur agar kursor yang melintas tidak memicu perubahan ukuran elemen (*zero layout shift*).
  - *Kekurangan*: Memerlukan penyesuaian padding samping agar tombol tidak menutupi teks penting pada layar mobile berukuran kecil (< 400px). Solusi: Di mobile, tombol ditempatkan sedikit ke dalam dengan latar belakang semi-transparan yang elegan.

* **Opsi B: Swipe/Drag Murni Tanpa Tombol Samping**
  - *Kelebihan*: Area slide bebas dari elemen tombol sama sekali.
  - *Kekurangan*: Kurang intuitif untuk pengguna desktop yang menggunakan mouse, berisiko membuat pengguna tidak menyadari bahwa kotak tersebut adalah slider.

**Keputusan**: Dipilih **Opsi A** untuk estetika modern, kemudahan akses, dan kepatuhan penuh terhadap arahan pengguna.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/landing.blade.php` | Modify | Menghapus toolbar atas (`.hero-slider-header-bar`), memasang tombol floating samping (`.hero-side-nav-btn.prev` dan `.hero-side-nav-btn.next`), dan memperbarui sumber gambar dokumentasi ke aset eksisting (`hero-diskominfo.png`, `hero_office_bg.png`, dll.). |
| `public/css/landing-modern.css` | Modify | Menghapus styling header bar lama, menambahkan styling tombol panah melayang di samping (`position: absolute; top: 50%`), mengoptimalkan rasio aspek foto dokumentasi, dan menonaktifkan efek hover transform. |
| `public/js/landing-interactive.js` | Modify | Memperbarui referensi ID tombol panah samping (`#btnHeroSidePrev` & `#btnHeroSideNext`) pada controller `initHeroSlider()`. |

---

## 5. Rincian Pembaruan Slide & Gambar Dokumentasi

1. **Slide 1: Informasi Pendaftaran (Tampilan Utama)**
   - Layout 3-kolom eksisting tetap dipertahankan penuh (Magang Periode II 2026, Syarat Umum, Dokumen Pendaftaran, CTA *"Daftar Sekarang"*).
2. **Slide 2: Dokumentasi Diskominfo Sleman (Foto Utama)**
   - **Gambar**: `asset('hero-diskominfo.png')`
   - **Judul**: *"Aktivitas & Kolaborasi Magang di Lingkungan Diskominfo Sleman"*
   - **Deskripsi**: Dokumentasi suasana kerja kolaboratif peserta magang bersama jajaran aparatur Diskominfo Kabupaten Sleman dalam mendukung transformasi digital daerah.
3. **Slide 3: Dokumentasi Pengembangan Aplikasi (Aptika & Lab Coding)**
   - **Gambar**: `asset('images/hero_office_bg.png')`
   - **Judul**: *"Pengembangan Aplikasi Layanan Publik & Smart City"*
   - **Deskripsi**: Dokumentasi ruang kerja dan asistensi teknis pembuatan fitur aplikasi publik Pemkab Sleman bersama mentor programmer senior.
4. **Slide 4: Dokumentasi Kelulusan & Pengalaman Pemagang**
   - **Gambar**: `asset('images/pendaftaran_banner_bg.jpg')`
   - **Judul**: *"Bimbingan Intensif, Lembar Penilaian, & Sertifikasi Resmi"*
   - **Deskripsi**: Dokumentasi penerimaan dan evaluasi akhir peserta magang hingga penerbitan sertifikat resmi bertanda tangan dinas.

---

## 6. Langkah Implementasi Bertahap

- [x] **Langkah 1**: Perbarui Struktur HTML Hero Slider
  - Buka `resources/views/public/landing.blade.php`.
  - Hapus `.hero-slider-header-bar`.
  - Tambahkan tombol melayang di samping kiri (`#btnHeroSidePrev`) dan kanan (`#btnHeroSideNext`).
  - Pasang gambar `hero-diskominfo.png` dan `images/hero_office_bg.png` pada slide dokumentasi.
- [x] **Langkah 2**: Perbarui Styling CSS Slider Samping Tanpa Hover
  - Di `public/css/landing-modern.css`:
    - Definisikan `.hero-side-nav-btn` dengan `position: absolute; top: 50%; transform: translateY(-50%); z-index: 20;`.
    - Pastikan **tidak ada transform scale** saat `:hover`.
    - Atur posisi responsif pada mobile (`left: 0.5rem; right: 0.5rem`).
- [x] **Langkah 3**: Perbarui Event Listener di JavaScript
  - Di `public/js/landing-interactive.js`:
    - Hubungkan tombol `#btnHeroSidePrev` dan `#btnHeroSideNext` ke logika `goToSlide()`.
- [x] **Langkah 4**: Verifikasi Visual & Fungsional
  - Bersihkan cache view (`php artisan view:clear`).
  - Uji pergeseran slide dengan mengklik tombol panah samping kiri dan kanan.
  - Verifikasi bahwa gambar dokumentasi eksisting termuat dengan tajam dan proporsional.
  - Pastikan kursor yang melintas di atas tombol maupun gambar tidak memicu perbesaran/zoom.
  - Verifikasi console browser bersih dari error (`0 errors`).

---

## 7. Strategi Testing

1. **Pengujian Posisi Tombol**:
   - Periksa posisi panah di desktop: Berada tepat di sisi kiri dan kanan hero box secara seimbang.
   - Periksa posisi panah di mobile: Tetap terlihat rapi tanpa menghalangi keterbacaan teks utama.
2. **Pengujian Aset Gambar**:
   - Pastikan URL `public/hero-diskominfo.png` dan `public/images/hero_office_bg.png` merespons status `200 OK` dan gambar tampil sempurna tanpa broken link.
3. **Pengujian Perilaku "No Hover"**:
   - Letakkan kursor di atas tombol panah samping -> Tidak ada efek zoom in atau pembesaran skala.
   - Letakkan kursor di atas foto dokumentasi -> Gambar tetap diam tanpa efek zoom.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Tombol navigasi slider telah dipindahkan dari atas ke sisi samping kiri dan kanan secara melayang (*floating side arrows*).
- [x] Tampilan tombol navigasi samping modern, bersih, dan bebas dari animasi hover zoom/scale.
- [x] Foto dokumentasi pada Slide 2, 3, dan 4 menggunakan aset otentik yang pernah diunggah (`hero-diskominfo.png`, `hero_office_bg.png`, dll.).
- [x] Transisi slider berjalan mulus saat tombol samping diklik.
- [x] Responsif di semua resolusi layar (mobile, tablet, desktop).
- [x] Konsol browser bebas error (Zero JS Error).
