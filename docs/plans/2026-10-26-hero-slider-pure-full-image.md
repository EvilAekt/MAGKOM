# Rencana Kerja: Penyederhanaan Slider Hero Section – Dokumentasi Foto Penuh Bersih (Pure Full-Image) Tanpa Tombol & Teks Penjelasan

**Tanggal**: 26 Oktober 2026  
**Status**: Selesai Diimplementasikan & Terverifikasi (Production Ready)  
**Dokumen**: `docs/plans/2026-10-26-hero-slider-pure-full-image.md`  
**Area Terkait**: Hero Slider Beranda (`#beranda` pada `resources/views/public/landing.blade.php`)

---

## 1. Tujuan
Menyederhanakan tampilan slide dokumentasi pada Slider Hero Section sesuai instruksi spesifik pengguna:
1. **Slide 1 (Slide Utama)**: Tetap mempertahankan tampilan awal secara utuh (informasi Magang Periode II 2026, Persyaratan Umum, Dokumen Pendaftaran) **lengkap dengan tombol CTA *"Daftar Sekarang"***.
2. **Slide 2, 3, dan Seterusnya (Slide Dokumentasi)**:
   - **Murni Gambar Penuh (*Pure Full-Image*)**: Menampilkan foto dokumentasi secara penuh memenuhi seluruh dimensi kotak hero (`width: 100%; height: 100%; object-fit: cover`).
   - **Tanpa Tombol Tambahan**: Menghilangkan tombol aksi/CTA pada slide dokumentasi (tombol hanya ada di slide awal).
   - **Tanpa Penjelasan/Teks Tambahan**: Menghilangkan seluruh teks judul, deskripsi, tag, dan ikon poin kegiatan pada slide dokumentasi sehingga foto tampil bersih, elegan, dan fokus.
3. **Navigasi Tetap Mulus**: Navigasi panah samping melayang (`<` dan `>`) serta titik indikator di bawah tetap dipertahankan tanpa animasi hover zoom.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis (Bekerja Secara Logis)
1. **Ketinggian Kotak Seragam (*Consistent Container Height*)**:
   - Slide dokumentasi foto penuh akan diatur mengikuti ketinggian kotak slide 1 (`min-height: 400px` atau menyamai tinggi alami `.hero-box`).
   - Hal ini menjamin saat slider bergeser dari Slide 1 ke Slide 2/3/4, tidak terjadi lonjakan ketinggian layar (*zero layout shift*).
2. **Foto Dokumentasi yang Ditampilkan Penuh**:
   - Slide 2: `public/hero-diskominfo.png` (Foto dokumentasi resmi Diskominfo Sleman).
   - Slide 3: `public/images/hero_office_bg.png` (Foto ruang kerja & lab pemrograman Diskominfo).
   - Slide 4: `public/images/pendaftaran_banner_bg.jpg` (Foto kegiatan penerimaan dan administrasi pemagang).
   - Slide 5 (Opsional/Tambahan): `public/images/dashboard_hero_bg.jpg` (Foto operasional dinas).
3. **Rasio Tampilan Foto**:
   - Menggunakan `object-fit: cover; object-position: center;` dengan radius sudut yang serasi (`border-radius: 0.75rem`) dan border halus yang selaras dengan tema sistem.

---

## 3. Pendekatan Teknis & Evaluasi Opsi

### Perbandingan Opsi:
* **Opsi A: Container Ketinggian Adaptif Fleksibel dengan `object-fit: cover` (Rekomendasi Utama)**
  - *Kelebihan*:
    - **Foto Tampil Maksimal**: Gambar mengisi penuh 100% lebar dan tinggi kotak hero tanpa distorsi atau garis hitam.
    - **Ultra Clean & Minimalis**: Bebas dari clutter teks dan tombol berulang, memberikan panggung visual penuh pada foto kegiatan nyata.
    - **Transisi Sangat Mulus**: Karena ukuran kontainer sama persis, pergeseran slide horizontal `translateX` terasa sangat solid dan premium.
  - *Kekurangan*: Memerlukan aturan CSS tinggi minimum agar gambar tidak menciut saat container kosong dari teks.

* **Opsi B: Menampilkan Foto Bersama Caption Transparan di Bawah**
  - *Kelebihan*: Ada keterangan teks kecil.
  - *Kekurangan*: Tidak sesuai dengan instruksi tegas pengguna: *"cukup gambar saja jangan ada penjelasan dll"*.

**Keputusan**: Dipilih **Opsi A** untuk memenuhi permintaan pengguna secara presisi dan menghasilkan tampilan yang bersih.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/landing.blade.php` | Modify | Menghapus seluruh markup teks judul, deskripsi, poin kegiatan, dan tombol dari Slide 2, 3, dan 4; menggantinya dengan elemen `<img>` penuh berkelas `.hero-full-img`. |
| `public/css/landing-modern.css` | Modify | Menambahkan styling `.hero-full-image-slide` dan `.hero-full-img` (`width: 100%; height: 100%; min-height: 410px; object-fit: cover; border-radius: 0.75rem; border: 1px solid var(--border)`). Menghapus sisa styling deskripsi dokumentasi yang tidak lagi digunakan. |
| `public/js/landing-interactive.js` | Maintain | Memastikan logika slider tetap sinkron dengan total slide foto penuh. |

---

## 5. Rincian Tampilan Setiap Slide

### Slide 1: Informasi Pendaftaran (Tampilan Awal)
- Tetap memuat layout 3 kolom lengkap:
  - Kolom 1: Kicker *Kabupaten Sleman*, Headline *Magang Kominfo Periode II 2026*, tanggal pendaftaran, dan **Tombol *"Daftar Sekarang"***.
  - Kolom 2: *Persyaratan Umum* (SMK/D3/S1, WFO kantor Sleman, jadwal).
  - Kolom 3: *Dokumen Pendaftaran* (Surat Pengantar, CV, Transkrip/Rapor, Pas Foto).

### Slide 2: Dokumentasi 1 (Foto Penuh)
- **Konten**: Murni foto `hero-diskominfo.png` yang memenuhi seluruh kotak secara penuh (*full bleed*).
- **Elemen Tambahan**: Tidak ada tombol, tidak ada judul, tidak ada teks penjelasan.

### Slide 3: Dokumentasi 2 (Foto Penuh)
- **Konten**: Murni foto `images/hero_office_bg.png` yang memenuhi seluruh kotak secara penuh (*full bleed*).
- **Elemen Tambahan**: Tidak ada tombol, tidak ada judul, tidak ada teks penjelasan.

### Slide 4: Dokumentasi 3 (Foto Penuh)
- **Konten**: Murni foto `images/pendaftaran_banner_bg.jpg` yang memenuhi seluruh kotak secara penuh (*full bleed*).
- **Elemen Tambahan**: Tidak ada tombol, tidak ada judul, tidak ada teks penjelasan.

---

## 6. Langkah Implementasi Bertahap

- [x] **Langkah 1**: Perbarui Markup HTML di Blade
  - Buka `resources/views/public/landing.blade.php`.
  - Pada Slide 2, 3, dan 4: Hapus seluruh elemen `.hero-doc-info-col`, teks penjelasan, poin grid, dan tombol CTA.
  - Bungkus elemen gambar `<img>` dengan kontainer `.hero-full-image-slide` yang bersih.
- [x] **Langkah 2**: Perbarui Styling CSS
  - Di `public/css/landing-modern.css`:
    - Buat class `.hero-full-image-slide` dengan tinggi seragam terhadap slide 1 (`height: 100%; min-height: 420px;`).
    - Atur `.hero-full-img` dengan `width: 100%; height: 100%; object-fit: cover; border-radius: 0.75rem; display: block; border: 1px solid var(--border);`.
    - Pastikan tidak ada efek hover zoom pada gambar.
- [x] **Langkah 3**: Verifikasi Browser & Responsivitas
  - Jalankan `php artisan view:clear`.
  - Buka `http://127.0.0.1:8000/`.
  - Uji geser dari Slide 1 ke Slide 2, 3, 4:
    - Pastikan Slide 1 tetap memiliki tombol *"Daftar Sekarang"* dan informasi syarat pendaftaran.
    - Pastikan saat digeser ke Slide 2, 3, 4, yang tampil murni gambar penuh tanpa tombol dan tanpa teks.
    - Pastikan tinggi kotak slider stabil tanpa lompatan (*no layout shift*).
    - Verifikasi console browser bersih dari error (`0 errors`).

---

## 7. Strategi Testing

1. **Pengujian Tampilan Slide 1**:
   - Pastikan informasi pendaftaran dan tombol *"Daftar Sekarang"* tetap ada dan berfungsi normal mengarah ke rute registrasi.
2. **Pengujian Tampilan Slide 2, 3, 4**:
   - Pastikan gambar memenuhi seluruh kontainer slide secara penuh (*cover*).
   - Pastikan tidak ada tombol ataupun teks penjelasan apa pun pada slide dokumentasi.
3. **Pengujian Transisi Slider**:
   - Geser menggunakan tombol panah samping kiri/kanan dan indikator titik di bawah: pergeseran berjalan mulus dan stabil.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Slide 1 memuat informasi pendaftaran lengkap dengan tombol *"Daftar Sekarang"*.
- [x] Slide 2, 3, dan 4 hanya menampilkan gambar dokumentasi penuh tanpa tombol dan tanpa teks penjelasan.
- [x] Tinggi kontainer slider stabil dan tidak terjadi lonjakan tampilan saat berganti slide.
- [x] Tombol navigasi samping melayang tetap berfungsi mulus tanpa efek hover yang mengganggu.
- [x] Responsif di semua resolusi layar (mobile, tablet, desktop).
- [x] Konsol browser bebas error (Zero JS Error).
