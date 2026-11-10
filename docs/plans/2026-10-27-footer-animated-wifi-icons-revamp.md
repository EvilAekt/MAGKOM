# Rencana Kerja: Penggantian Partikel Footer – Dari Cincin Setengah Menjadi Ikon Wi-Fi Melayang ke Atas

**Tanggal**: 27 Oktober 2026  
**Status**: Selesai Diimplementasikan & Terverifikasi (Production Ready)  
**Dokumen**: `docs/plans/2026-10-27-footer-animated-wifi-icons-revamp.md`  
**Area Terkait**: Footer Section (`.landing-footer` pada `resources/views/public/landing.blade.php` & `public/css/landing-modern.css`)

---

## 1. Analisis Masalah & Kebutuhan Pengguna
Berdasarkan perbandingan dua gambar yang dilampirkan oleh pengguna:
1. **Gambar Kanan (Efek Saat Ini)**:
   - Menampilkan partikel berupa "cincin setengah" (*single circular arc / half-ring stroke*).
   - Bentuk ini dirasa kurang representatif dan kurang menggambarkan identitas jaringan Sleman Free WiFi.
2. **Gambar Kiri (Target Efek Baru)**:
   - Menampilkan **ikon sinyal Wi-Fi utuh**: memiliki titik pancaran (*dot*) di bawah dan 3 lengkungan gelombang sinyal di atasnya.
   - Berwarna kuning keemasan (*gold warm glow*) yang selaras dengan tema warna MAGKOM & Sleman Smart City.
3. **Instruksi Pengguna**:
   - *"di bagian footer apakah bisa digani efek nya jadi yang seperi cicncin setengah itu di gani dengan logo wifi aja yang animasi ke atas yang pada gambar di atas buat,jadi yang sebelumnya pada gambar yang di sebelah kanan di ubah jadi wifi yang kiri itu bua plan nya"*

---

## 2. Rincian Solusi & Pendekatan Teknis

### A. Komponen Visual Ikon Wi-Fi (Vektor SVG Bersih)
- Mengganti elemen `.signal-arc-shape` pada setiap `<li>` di `.wifi-signal-nodes` dengan vektor SVG ikon Wi-Fi lengkap yang proporsional:
  - 1 Titik pusat sinyal (*central dot*) di bagian bawah (`cx="12" cy="20" r="1.5"`).
  - 3 Busur gelombang sinyal bertingkat (*concentric signal arcs*) di atasnya (`stroke-width="2"` dengan `stroke-linecap="round"`).
  - Berwarna emas berpendar: `color: var(--gold-bright);` dengan efek `filter: drop-shadow(0 0 8px rgba(245, 158, 11, 0.45));`.

### B. Animasi Partikel Melayang ke Atas (Vertical Upward Floating)
1. **Arah Pergerakan**:
   - Bergerak vertikal dari bawah ke atas (`translateY(0)` menuju `translateY(-320px)` s/d `translateY(-400px)`).
   - Menghilangkan rotasi 180 derajat yang ada sebelumnya, sehingga orientasi ikon Wi-Fi **tetap tegak memancar ke atas** seperti gelombang sinyal radio asli.
   - Ditambahkan sedikit efek goyangan horizontal halus (*gentle drift* `translateX(-6px)` ke `translateX(6px)`) agar terlihat alami bagaikan gelombang sinyal yang mengudara di angkasa.
2. **Siklus Kecerahan (Opacity Cycle)**:
   - `0%`: Muncul dari dasar footer dengan `opacity: 0; scale(0.65);`.
   - `20%`: Menyala terang anggun dengan `opacity: 0.75; scale(0.9);`.
   - `70%`: Berpendar lembut dengan `opacity: 0.5; scale(1.05);`.
   - `100%`: Memudar di bagian atas footer dengan `opacity: 0; scale(1.2);`.
3. **Variasi Partikel (Distributed Floating Nodes)**:
   - Dibuat 8 s/d 10 partikel dengan variasi ukuran:
     - Partikel kecil: `20px x 20px` (latar jauh)
     - Partikel sedang: `28px x 28px` (latar tengah)
     - Partikel besar: `38px x 38px` (latar depan)
   - Disebar merata di posisi horizontal (`left: 6%` s/d `left: 94%`) dengan `animation-delay` (0s s/d 8s) dan durasi (12s s/d 22s) yang acak agar aliran partikel tidak berjalan bersamaan dan menghasilkan nuansa hidup yang terus-menerus.

---

## 3. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/landing.blade.php` | Modify | Mengganti isi setiap `<li>` di dalam `.wifi-signal-nodes` dari `.signal-arc-shape` menjadi SVG ikon Wi-Fi lengkap (`.floating-wifi-svg`). |
| `public/css/landing-modern.css` | Modify | 1) Menghapus aturan CSS `.signal-arc-shape`. 2) Menambahkan styling `.floating-wifi-svg` (warna gold, glow drop-shadow). 3) Memperbarui `@keyframes animateWifiFloating` agar melayang tegak ke atas tanpa rotasi terbalik. |

---

## 4. Langkah-Langkah Implementasi Bertahap

- [x] **Langkah 1**: Perbarui elemen di `resources/views/public/landing.blade.php` dengan menyematkan SVG ikon Wi-Fi utuh pada deretan `<li>` di `.wifi-signal-nodes`.
- [x] **Langkah 2**: Perbarui `@keyframes animateWifiFloating` dan kelas `.floating-wifi-svg` di `public/css/landing-modern.css` untuk animasi melayang ke atas dengan pendaran gold.
- [x] **Langkah 3**: Bersihkan cache view (`php artisan view:clear`) dan verifikasi keindahan animasi melayang di peramban pada `http://127.0.0.1:8000/`.

---

## 5. Kriteria Selesai (Definition of Done)
1. **Bentuk Sesuai Gambar Kiri**: Partikel melayang berwujud ikon Wi-Fi lengkap (dot + 3 lengkungan sinyal), bukan lagi busur cincin setengah.
2. **Animasi Mulus ke Atas**: Ikon Wi-Fi melayang anggun dari bawah ke atas dengan posisi tegak dan efek pendar emas.
3. **Kinerja Ringan (60 FPS)**: Menggunakan akselerasi GPU `transform: translateY` dan `opacity` murni tanpa membebani CPU.
4. **Zero Error**: Tidak ada error tampilan maupun console.
