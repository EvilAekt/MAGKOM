# Rencana Perbaikan Tombol Panah Carousel Kuota yang Terpotong

**Tanggal**: 16 November 2026  
**Dokumen**: `docs/plans/2026-11-16-plan-perbaikan-tombol-panah-carousel-kuota-terpotong.md`  
**Status**: Menunggu Konfirmasi User  
**Referensi Gambar**: User uploaded image (`media_1794808667130_af84b2c1.png` dan `media_1794808674670_5fea608b.png`)

---

## 1. Analisis Akar Masalah (Root Cause Analysis)

Berdasarkan investigasi visual dan penelusuran kode pada `resources/css/pages/public/landing.css`:

1. **Struktur Komponen di Blade (`resources/views/public/blocks/kuota.blade.php`)**:
   ```html
   <div class="kuota-carousel-wrapper">
       <button id="btnCarouselPrev" class="kuota-side-arrow-btn prev">...</button>
       <div id="kuotaCarouselTrack" class="kuota-carousel-track">
           <!-- Kartu-kartu kuota penempatan -->
       </div>
       <button id="btnCarouselNext" class="kuota-side-arrow-btn next">...</button>
   </div>
   ```

2. **Penyebab Terpotongnya Tombol**:
   - Di `resources/css/pages/public/landing.css` baris 1266-1300:
     ```css
     .kuota-carousel-wrapper {
         position: relative;
         width: 100%;
         max-width: 100%;
         margin-top: 1.75rem;
         overflow-x: clip; /* <-- PENYEBAB UTAMA CLIPPING */
     }

     .kuota-side-arrow-btn.prev {
         left: -1.25rem; /* <-- Posisi negatif keluar sejauh 20px dari tepi kiri wrapper */
     }

     .kuota-side-arrow-btn.next {
         right: -1.25rem; /* <-- Posisi negatif keluar sejauh 20px dari tepi kanan wrapper */
     }
     ```
   - Tombol panah memiliki lebar `2.75rem` (44px).
   - Dengan `left: -1.25rem` (-20px), tombol digeser ke kiri menyeberangi garis koordinat `x = 0` dari `.kuota-carousel-wrapper`.
   - Properti `overflow-x: clip` pada wrapper menginstruksikan browser untuk **memotong habis (clipping)** semua piksel yang berada di luar batas kotak wrapper.
   - Akibatnya, separuh sisi kiri tombol panah `prev` terpotong rata oleh garis batas vertikal wrapper persis seperti yang terlihat pada tangkapan layar pengguna.
   - Hal serupa juga terjadi pada tombol `next` di sisi kanan yang berpotensi terpotong di tepi kanan wrapper.

3. **Fakta Arsitektur**:
   - Scrolling horizontal kartu kuota sebenarnya **sudah ditangani secara mandiri** oleh elemen anak `.kuota-carousel-track` (`overflow-x: auto; scroll-behavior: smooth; scroll-snap-type: x mandatory`).
   - Elemen induk `.kuota-carousel-wrapper` **tidak membutuhkan `overflow-x: clip`**, karena keberadaannya hanya sebagai pembungkus relatif bagi tombol panah floating.

---

## 2. Solusi Perbaikan

Kami merancang solusi yang bersih, kokoh, dan konsisten di berbagai ukuran layar:

```
[ KONDISI SAAT INI (TERPOTONG) ]
  Batas Wrapper (overflow-x: clip)
       │
   (   │ < )  <-- Separuh kiri tombol terpotong di koordinat x < 0
       │

                 ▼ DIPERBAIKI MENJADI ▼

[ SOLUSI: OVERFLOW VISIBLE + POSISI IN-BOUNDS ELEGAN ]
       │
     ( < )   <-- Tombol bulat penuh tampak sempurna, melayang anggun di tepi track
       │
```

### Opsi Implementasi Penempatan Tombol:

| Opsi | Penyesuaian CSS | Karakteristik & Kelebihan | Rekomendasi |
|---|---|---|---|
| **Opsi 1 (Floating In-Bounds Aman - Rekomendasi)** | • Ubah wrapper ke `overflow: visible;`<br>• Atur tombol `prev` ke `left: 0.25rem` (atau `0.5rem`) dan `next` ke `right: 0.25rem`<br>• Tingkatkan `z-index: 15` dan pertahankan backdrop blur halus | • **100% aman dari clipping**: Tombol berada di dalam kontainer yang terlihat.<br>• Tidak pernah terpotong oleh tepi layar, viewport sempit, maupun padding kontainer induk.<br>• Tampilan konsisten antara layar desktop, laptop, tablet, dan smartphone. | **(Rekomendasi Utama)** |
| **Opsi 2 (Overflow Visible + Half-Overhang)** | • Ubah wrapper ke `overflow: visible;`<br>• Pertahankan tombol melayang di tepi batas (`left: -1rem; right: -1rem;` pada desktop ≥ 1024px)<br>• Pastikan container section memiliki padding samping yang aman | • Tombol menggantung separuh di luar kartu pada layar lebar.<br>• Membutuhkan media query ekstra agar tidak terpotong tepi layar pada resolusi menengah. | Alternatif |

---

## 3. Rincian Perubahan Kode

### 3.1 File Stylesheet: `resources/css/pages/public/landing.css`
Target perbaikan pada rule baris 1266-1327:
1. **`.kuota-carousel-wrapper`**:
   - Hapus `overflow-x: clip;` dan ganti menjadi `overflow: visible;`.
2. **`.kuota-side-arrow-btn`**:
   - Tingkatkan `z-index: 15;` (dari `z-index: 10`) agar selalu berada di lapisan teratas di atas kartu dan efek bayangannya.
   - Berikan `background-color: rgba(255, 255, 255, 0.95);` dengan `backdrop-filter: blur(6px);` dan `box-shadow: 0 4px 16px rgba(0, 0, 0, 0.14);` agar kontrasnya selalu tajam di atas konten kartu apapun.
3. **`.kuota-side-arrow-btn.prev` & `.kuota-side-arrow-btn.next`**:
   - Setel posisi `left: 0.25rem;` untuk `.prev` dan `right: 0.25rem;` untuk `.next` (pada desktop tetap terlihat utuh dan elegan di atas track tanpa clipping).

### 3.2 File Markup & Skrip JavaScript
- **Markup Blade (`kuota.blade.php`)**: Tidak ada perubahan yang merusak, markup tombol dan track tetap utuh.
- **JavaScript (`landing.js`)**: Logika fungsi scroll dan event listener `checkCarouselButtons()` tetap bekerja normal.

---

## 4. Tahapan Eksekusi

1. **Edit Stylesheet**:
   - Terapkan perbaikan CSS pada `.kuota-carousel-wrapper` dan `.kuota-side-arrow-btn` di `resources/css/pages/public/landing.css`.
2. **Kompilasi Aset Frontend**:
   - Jalankan `npm run build` dengan Vite.
3. **Verifikasi Quality Gates**:
   - Jalankan unit & feature tests: `LandingPageTest` dan `LandingPageToRegistrationPrefillTest`.
   - Cek formatting dengan Laravel Pint (`./vendor/bin/pint --test`).
   - Uji visual via dev server di `http://127.0.0.1:8000/#penempatan` untuk memastikan tombol lingkaran bulat putih terlihat 100% utuh tanpa kepotong di tepi kiri maupun kanan.

