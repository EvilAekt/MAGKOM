# Rencana Kerja: Single Image Hero Dokumentasi & Penguncian Ukuran Presisi (Zero Layout Shift)

**Tanggal**: 26 Oktober 2026  
**Status**: Selesai Diimplementasikan & Terverifikasi (Production Ready)  
**Dokumen**: `docs/plans/2026-10-26-single-hero-image-and-exact-height-lock.md`  
**Area Terkait**: Hero Section Slider (`#beranda` pada `resources/views/public/landing.blade.php`, `public/css/landing-modern.css`, `public/js/landing-interactive.js`)

---

## 1. Masalah & Kebutuhan Pengguna
Berdasarkan instruksi dan lampiran gambar dari pengguna:
1. **Hanya 1 Gambar Dokumentasi Saja**:
   - Pengguna meminta: *"gambarnya 1 saja yang ini ambil yang itu aja"*.
   - Tidak perlu beberapa slide foto (Slide 3 dan 4 dihilangkan).
   - Menggunakan foto resmi Diskominfo yang diunggah pengguna (foto tim Diskominfo Sleman memegang spanduk sertifikasi ISO/IEC 27001:2022).
2. **Ukuran Gambar Harus Sama Persis dengan Informasi Awal**:
   - Pengguna meminta: *"ubah ukuran gambar sesuai dengan informasi awal itu jangan pas di slide berubah ukuran"*.
   - Saat slider digeser dari Slide 1 ke Slide 2, kontainer tidak boleh berubah ukuran sama sekali (*zero layout shift*, lebar dan tinggi 100% konsisten).
   - Gambar foto dokumentasi harus mengisi penuh kotak dengan tinggi dan lebar yang identik terhadap kotak `.hero-box` Slide 1.

---

## 2. Solusi & Pendekatan Teknis

### A. Pengelolaan Aset Gambar
1. Mengambil file gambar yang diunggah pengguna:
   - Sumber: `brain/.../.user_uploaded/media_1793031859482.png` (Foto resmi rekomendasi audit sertifikasi ISO 27001 Diskominfo Sleman).
2. Menyimpan aset ke folder proyek resmi:
   - Target: `public/images/hero_dokumentasi_diskominfo.png`.
   - File beresolusi tinggi, tajam, dan bebas dari watermark atau elemen figma luar.

### B. Struktur Slider 2-Slide
1. **Slide 1**: Informasi Pendaftaran (Tampilan Awal)
   - Layout 3 kolom: Informasi Magang Periode II, Persyaratan Umum, Dokumen Pendaftaran, dan tombol *"Daftar Sekarang"*.
2. **Slide 2**: Satu-satunya Slide Dokumentasi Foto Penuh (*Pure Full-Image*)
   - Murni foto `hero_dokumentasi_diskominfo.png`.
   - Tanpa teks/judul tambahan dan tanpa tombol, bersih dan elegan.
3. **Indikator Titik (Dots)**:
   - Disesuaikan menjadi **2 dot saja** (Slide 1 dan Slide 2).
4. **Navigasi Panah Samping**:
   - Navigasi melayang kiri `<` dan kanan `>` bergantian antara Slide 1 dan Slide 2 secara mulus.

### C. Penguncian Ukuran Presisi (Anti Perubahan Ukuran Saat Slide)
1. **CSS Height & Overflow Architecture**:
   - Kontainer `.hero-full-image-slide` diatur agar memiliki border, border-radius (`0.75rem`), dan background `#0f172a` yang serasi dengan `.hero-box`.
   - Elemen `<img>` memakai `width: 100%; height: 100%; object-fit: cover; object-position: center; display: block; border-radius: 0.75rem;`.
2. **Dynamic Height Synchronization (JavaScript Engine)**:
   - Untuk menjamin tinggi Slide 2 tidak meleset 1 piksel pun dari Slide 1 di berbagai resolusi layar (desktop, tablet, mobile), kita tambahkan fungsi sinkronisasi tinggi di `landing-interactive.js`:
     ```javascript
     function syncHeroHeight() {
         const heroBox = document.querySelector('.hero-slide-item:first-child .hero-box');
         const imageSlide = document.querySelector('.hero-full-image-slide');
         if (heroBox && imageSlide) {
             imageSlide.style.height = heroBox.offsetHeight + 'px';
         }
     }
     ```
   - Dipanggil saat inisialisasi awal, saat gambar selesai dimuat (`load`), serta saat jendela browser diubah ukurannya (`window.resize`).
   - Dengan pendekatan ini, saat slide bergeser (transition `translateX`), ukuran kotak tetap **100% kokoh dan identik**, tidak ada lonjakan naik/turun layar.

---

## 3. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `public/images/hero_dokumentasi_diskominfo.png` | Create | Salin foto resmi dari `.user_uploaded/media_1793031859482.png`. |
| `resources/views/public/landing.blade.php` | Modify | 1) Sederhanakan slider menjadi 2 slide saja (Slide 1: Info Pendaftaran, Slide 2: Foto dokumentasi tunggal `hero_dokumentasi_diskominfo.png`). 2) Ubah indikator dot menjadi 2 tombol dot. |
| `public/css/landing-modern.css` | Modify | Pastikan styling `.hero-full-image-slide` dan `.hero-slider-track` terkunci sempurna mengikuti dimensi `.hero-box`. |
| `public/js/landing-interactive.js` | Modify | Tambahkan fungsi sinkronisasi tinggi `syncHeroHeight()` otomatis untuk menjamin nol pergeseran ukuran (zero layout shift). |

---

## 4. Langkah-Langkah Implementasi Bertahap

- [x] **Langkah 1**: Salin file gambar yang diunggah pengguna ke `public/images/hero_dokumentasi_diskominfo.png`.
- [x] **Langkah 2**: Perbarui markup di `resources/views/public/landing.blade.php`:
  - Hapus Slide 3 dan Slide 4.
  - Gunakan `hero_dokumentasi_diskominfo.png` pada Slide 2.
  - Kurangi indikator dot menjadi 2 dot (index 0 dan index 1).
- [x] **Langkah 3**: Tambahkan fungsi penguncian tinggi presisi `syncHeroHeight()` di `landing-interactive.js` dan CSS pendukung di `landing-modern.css`.
- [x] **Langkah 4**: Bersihkan cache view (`php artisan view:clear`) dan lakukan pengujian transisi slide pada `http://127.0.0.1:8000/`.

---

## 5. Kriteria Selesai (Definition of Done)
1. **Hanya 1 Gambar Dokumentasi**: Slider hanya memiliki 2 slide (Slide 1: Informasi Pendaftaran, Slide 2: Foto sertifikasi ISO yang diunggah user).
2. **Ukuran Identik & Tidak Berubah**: Saat berpindah dari Slide 1 ke Slide 2 atau sebaliknya, tinggi dan lebar kotak tetap sama persis tanpa lompatan layout.
3. **Navigasi Mulus**: Tombol panah samping dan 2 dot indikator berfungsi mulus tanpa bug.
4. **Zero Error**: Bebas dari error console dan layout anomali.
