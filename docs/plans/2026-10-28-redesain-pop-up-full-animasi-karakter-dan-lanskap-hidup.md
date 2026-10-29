# Rencana Kerja: Transformasi Pop-Up Alur Magang Menjadi "Full Animasi" (Lanskap Hidup Sleman, Karakter Bergerak & Mobil Sinematik)

Dokumen ini merinci rencana perombakan menyeluruh pop-up modal peta alur menjadi pengalaman visual **penuh animasi (*Full Animated Living World*)**, menggantikan tampilan kotak gelap kaku dengan lanskap ilustrasi kota Sleman yang hidup, karakter orang (siswa/mahasiswa & mentor ASN) yang bergerak dinamis di setiap tahap, mobil dinas dengan lampu sorot dan efek laju ekspresif, serta integrasi visual yang memukau (*WOW-factor*).

---

## 1. Tujuan
1. **Transformasi Menjadi "Full Animasi" yang Hidup & Menarik**:
   - Menghilangkan kesan "kotak hitam polos dan kaku" pada kanvas jalan.
   - Mengubah kanvas kiri menjadi **Diorama Lanskap Interaktif Sleman Digital**:
     - Siluet anggun Gunung Merapi di kejauhan dengan awan SVG melayang perlahan (*floating ambient clouds*).
     - Gedung Kantor Diskominfo Sleman di titik finish dengan tiang antena pemancar berkedip (*pulsing radio beacon*).
     - Lampu jalan kota dengan pendaran cahaya (*warm streetlamps glow*) dan pepohonan yang bergoyang lembut (*gentle wind sway*).
2. **Animasi Karakter Orang Bergerak (Living Characters per Tahap)**:
   - Menambahkan ilustrasi karakter vektor SVG yang bergerak sesuai tahapan aktif:
     - **Tahap 1 (Registrasi)**: Calon peserta sedang mengetik di laptop dengan layar berpendar dan partikel ikon identitas melayang.
     - **Tahap 2 (Pengajuan Berkas)**: Karakter membawa map dokumen dinas, disertai animasi stempel stempel verifikasi *"BERKAS LENGKAP"*.
     - **Tahap 3 (Pelaksanaan Magang)**: Karakter mengenakan seragam dinas MagKom berdiskusi di samping Mentor ASN Diskominfo Sleman yang tersenyum dan mengangguk ramah.
     - **Tahap 4 (Evaluasi & Kelulusan)**: Karakter melompat gembira memegang gulungan E-Sertifikat resmi bertabur partikel konfeti emas (*shimmering celebration*).
3. **Penyempurnaan Animasi Mobil Dinas MagKom**:
   - Redesain mobil dinas SVG dengan detail tinggi: Lampu sorot depan menyala (*headlight beam rays*) yang membelah jalan, sirine amber berputar lembut (*strobe beacon*), efek hembusan kecepatan (*wind speed trail*), dan suspensi mengayun saat berbelok.
4. **Pelebaran & Keselarasan Estetika Pop-Up**:
   - Container pop-up modal dibuat proporsional dan mewah (`max-width: 1260px`), dengan latar belakang gelap terintegrasi (*seamless dark gradient*), kartu checklist yang modern, dan transisi antar-tahap yang memanjakan mata.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi:
- **Asumsi 1**: Seluruh aset animasi dibangun menggunakan **SVG murni, CSS Keyframes, dan requestAnimationFrame Vanilla JS** tanpa dependensi library eksternal (seperti Lottie atau Three.js), sehingga tetap sangat ringan (<25 KB total), instan dimuat, dan hemat baterai/GPU.
- **Asumsi 2**: Kontrol zoom in/out (`+`, `-`, `Semua`) tetap dipertahankan dan diintegrasikan secara presisi pada kanvas lanskap hidup ini.
- **Asumsi 3**: Pengguna dengan preferensi `@media (prefers-reduced-motion: reduce)` akan menerima versi animasi yang lebih tenang tanpa mengorbankan keindahan grafis.

### Pertanyaan Terbuka:
- *(Tidak ada pertanyaan pemblokir; arah keinginan pengguna sangat jelas: menginginkan pengalaman visual kaya animasi, ada karakter orang bergerak, mobil lebih bagus, dan background lanskap yang artistik).*

---

## 3. Pendekatan Teknis & Perbandingan Arsitektur

| Kriteria | Opsi A: Pure SVG Scene Graph + CSS Animated Layers (Rekomendasi) | Opsi B: Lottie JSON Animation Files | Opsi C: Canvas WebGL 3D |
| :--- | :--- | :--- | :--- |
| **Deskripsi** | Lanskap jalan, karakter orang, mobil, dan awan disusun dalam layer-layer SVG modular dengan CSS Keyframe animations. | Memuat file animasi JSON dari After Effects menggunakan library `lottie-web`. | Membangun scene 3D dengan Three.js / WebGL canvas. |
| **Ukuran Aset & Kecepatan** | **Sangat Ringan (<25 KB)**, nol request HTTP tambahan, tidak ada overhead download script library eksternal. | Berat (+280 KB untuk library Lottie player + file JSON animasi per tahap). | Sangat berat (>600 KB script + tekstur), risiko lag pada laptop tanpa GPU dedicated. |
| **Kustomisasi & Sinkronisasi** | **100% Fleksibel**; karakter, mobil, dan warna langsung tersambung dengan state interaktif (tahap 1–4, checklist progres, dll). | Kaku; sulit memodifikasi teks, warna, atau trigger step di dalam file JSON animasi secara dinamis. | Rumit; butuh maintenance shader dan 3D camera matrices. |
| **Kesesuaian Desain** | Selaras sempurna dengan estetika portal resmi Diskominfo Sleman dan desain responsif MAGKOM. | Format visual bisa tidak konsisten jika file Lottie berasal dari sumber berbeda. | Terlalu berlebihan (*overkill*) untuk kebutuhan pop-up modal. |
| **Rekomendasi** | **Dipilih Opsi A**. Terbukti paling mulus, tajam di semua layar, sangat ringan, dan kontrol interaktif penuh. | Tidak dipilih. | Tidak dipilih. |

---

## 4. Rincian Lapisan Visual & Animasi (*Scene Architecture*)

```
+---------------------------------------------------------------------------------------------------------+
| [LAYER 1: SKY & MERAPI BACKDROP]                                                                        |
| - Gradien Langit Malam Sleman (Deep Indigo #0b1528 -> Royal Navy #122846)                               |
| - Siluet Gunung Merapi megah di kejauhan dengan bayangan ambient starlight                              |
| - Awan SVG mengambang lembut ke kanan (CSS infinite floating loop)                                     |
+---------------------------------------------------------------------------------------------------------+
| [LAYER 2: CITYSCAPE & DISKOMINFO HUB]                                                                   |
| - Siluet Gedung Kantor Diskominfo Sleman dengan jendela berlampu dan tower transmisi berkedip           |
| - Lampu jalan kota memancarkan kerucut cahaya temaram ke aspal jalan                                    |
+---------------------------------------------------------------------------------------------------------+
| [LAYER 3: HIGHWAY ROAD WITH GLOW TRAIL]                                                                 |
| - Aspal jalan raya berkelok lebar dengan trotoar pembatas dan marka tengah menyala                      |
| - Jalur hijau progres aktif (active trail) yang membentang seiring posisi tahapan                       |
+---------------------------------------------------------------------------------------------------------+
| [LAYER 4: ANIMATED CAR WITH LIGHT BEAMS & SUSPENSION]                                                   |
| - Mobil dinas MagKom dengan lampu depan menyinari aspal, sirine amber berputar, dan rotasi kurva halus  |
+---------------------------------------------------------------------------------------------------------+
| [LAYER 5: LIVING CHARACTER DISPLAY (ORANG BERGERAK SESUAI TAHAP)]                                       |
| - Tahap 1: Siswa mengetik di laptop (tangan bergerak, partikel kode/akun melayang)                      |
| - Tahap 2: Siswa menyerahkan map berkas + stempel "APPROVED" beranimasi cap                             |
| - Tahap 3: Siswa berseragam dinas bersama Mentor ASN (mentor mengangguk & memberi jempol)               |
| - Tahap 4: Siswa melompat gembira memegang sertifikat + hujan partikel konfeti emas                     |
+---------------------------------------------------------------------------------------------------------+
| [LAYER 6: FLOATING GPS PIN & BOTTOM TAB BAR WITH STEP COUNTERS]                                         |
| - GPS Pin kuning 3D berayun di atas node aktif                                                          |
| - Bilah tab horizontal 1, 2, 3, 4 di bawah kanvas dengan progress counter (0/4, 4/4)                    |
+---------------------------------------------------------------------------------------------------------+
```

---

## 5. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/partials/_peta_alur_modal.blade.php` | Modify | 1. Implementasikan SVG Scene Graph kaya visual: Lanskap Gunung Merapi, awan melayang, menara Diskominfo, lampu jalan.<br>2. Integrasikan komponen **Karakter Orang Bergerak (SVG)** dengan 4 pose animasi berbeda untuk setiap tahap (mengetik laptop, stempel berkas, diskusi mentor, selebrasi sertifikat).<br>3. Perbarui mobil dinas dengan headlight beam, strobe light, dan animasi laju ekspresif.<br>4. Perluas kontainer modal menjadi `1260px` agar leluasa. |
| `public/css/landing-peta-alur-modal.css` | Modify | 1. Tambahkan keyframes animasi: `@keyframes cloudDrift`, `@keyframes beaconBlink`, `@keyframes charTyping`, `@keyframes stampThud`, `@keyframes mentorNod`, `@keyframes confetiFall`, `@keyframes carSway`.<br>2. Styling lanskap Sleman, glow lampu jalan, beam mobil, dan kartu karakter.<br>3. Penyesuaian tema gelap dan responsivitas tablet/mobile. |
| `tests/Feature/LandingPagePetaAlurModalTest.php` | Modify | Perbarui assertion test untuk memvalidasi keberadaan elemen karakter hidup (`modalCharacterScene`), elemen lanskap (`modalMerapiBackdrop`), mobil dengan headlight, dan kontrol alur. |

---

## 6. Langkah Implementasi Terstruktur

### Tahap 1: Pembuatan Vektor SVG Lanskap Hidup Sleman & Menara Diskominfo
- [x] Buka [resources/views/public/partials/_peta_alur_modal.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/partials/_peta_alur_modal.blade.php).
- [x] Tambahkan grup `<g id="modalLandscapeScene">` di dalam SVG:
  - Siluet Gunung Merapi bergradasi halus (`#0e1a2e` ke `#162844`).
  - Tiga lapisan awan SVG mengambang dengan durasi animasi berbeda (20s, 30s, 45s).
  - Vektor Gedung Kantor Diskominfo Sleman mini dengan tiang antena pemancar sinyal WiFi/TTE berkedip merah-hijau.
  - Vektor lampu jalan kota yang memancarkan efek cahaya gradasi kuning transparan ke aspal.

### Tahap 2: Pembuatan Komponen Karakter Orang Bergerak (4 State Animasi)
- [x] Di dalam SVG peta alur, tambahkan grup `<g id="modalCharacterScene">` yang posisinya dinamis di dekat checkpoint aktif:
  - **Karakter State 1 (Registrasi)**: Siswa SMK/Mahasiswa duduk memegang laptop, lengan kanan-kiri mengetik beranimasi (`@keyframes charTyping`), partikel ikon verified keluar dari layar.
  - **Karakter State 2 (Seleksi)**: Karakter memegang map berkas, stempel merah/biru melayang lalu mengecap meja dengan efek hentakan (`@keyframes stampThud`).
  - **Karakter State 3 (Pelaksanaan)**: Karakter berseragam dinas bersama Mentor ASN Kominfo Sleman (kepala mentor mengangguk lembut `@keyframes mentorNod`, tangan menunjuk modul).
  - **Karakter State 4 (Evaluasi & Kelulusan)**: Karakter melompat dengan pose kemenangan memegang map sertifikat ber-QR, taburan partikel bintang/konfeti berputar `@keyframes confetiFall`.
- [x] Buat fungsi JavaScript `updateCharacterScene(stepNum)` untuk mengalihkan tampilan karakter sesuai tahap yang aktif dengan transisi fade-in halus.

### Tahap 3: Redesain Mobil Dinas MagKom Sinematik
- [x] Perbarui `<g id="modalRoadCarGroup">`:
  - Bentuk bodi mobil SUV dinas Sleman aerodinamis dengan ban bertekstur, jendela gelap ber-tint biru, sirine di atap berkedip.
  - Tambahkan poligon kerucut cahaya lampu depan (`<polygon class="car-headlight-beam" points="..."/>`) dengan gradasi kuning transparan menerangi aspal di depan mobil.
  - Tambahkan efek bayangan mobil dinamis dan animasi getaran suspensi halus (`@keyframes carSway`).

### Tahap 4: Styling CSS Animasi Penuh (Fluid Micro-Animations)
- [x] Buka [public/css/landing-peta-alur-modal.css](file:///d:/MAGKOM/MAGKOM/public/css/landing-peta-alur-modal.css).
- [x] Buat keyframes CSS:
  - `cloudDrift`: perpindahan translasi awan horizontal mulus dari kiri ke kanan.
  - `towerBeaconPulse`: kedipan cahaya menara pemancar sinyal Diskominfo.
  - `charTyping` & `charBounce`: gerakan tangan mengetik dan tubuh karakter bernafas alami.
  - `confettiShower`: partikel konfeti berputar dan berjatuhan lembut.
  - `headlightPulse`: pendaran cahaya lampu depan mobil saat melaju.
- [x] Poles latar belakang kanvas agar menyatu elegan dengan modal container lebar (`1260px`).

### Tahap 5: Pengujian Otomatis & Verifikasi Visual
- [x] Perbarui test di [tests/Feature/LandingPagePetaAlurModalTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/LandingPagePetaAlurModalTest.php):
  - Assert keberadaan elemen lanskap Merapi (`modalMerapiBackdrop`).
  - Assert keberadaan grup karakter dinamis (`modalCharacterScene`).
  - Assert keberadaan mobil dengan headlight beam (`car-headlight-beam`).
  - Assert keberadaan tombol zoom dan tab alur.
- [x] Jalankan test suite: `php artisan test --filter=LandingPagePetaAlurModalTest`.
- [x] Uji responsivitas dan kehalusan animasi di browser.

---

## 7. Strategi Testing

1. **Automated Feature Testing**:
   - `LandingPagePetaAlurModalTest`: Memastikan seluruh elemen scene lanskap, karakter SVG per tahap, mobil sinematik, dan tombol kontrol ter-render dengan benar.
   - `AlurMagangPageTest`: Memastikan halaman dan alur publik tetap 100% stabil (zero regression).
2. **Visual & Performance Quality Gates**:
   - Kecepatan render scene grafik stabil di 60 FPS tanpa lonjakan penggunaan CPU/RAM.
   - Pergantian tahap (1 $\rightarrow$ 2 $\rightarrow$ 3 $\rightarrow$ 4) memicu mobil melaju dengan lampu menyala, karakter berganti pose aksi, dan stempel/konfeti beranimasi mulus.
   - Kontrol zoom in/out/reset tetap bekerja sempurna mencakup seluruh lanskap dan karakter.

---

## 8. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Banyak Elemen SVG Membebani DOM** | Potensi frame drop pada browser lawas | Optimalkan path SVG menjadi bentuk geometris sederhana dan gunakan GPU acceleration (`will-change: transform`, `transform: translate3d`). |
| **Kepadatan Informasi pada Layar HP Kecil** | Karakter dan lanskap bertumpuk di layar <600px | Sembunyikan detail lanskap mikro di mobile menggunakan media query, prioritaskan jalan, mobil, dan karakter utama agar tetap lega. |
| **Aksesibilitas & Pengguna Sensitif Gerakan** | Pusing bagi pengguna dengan vestibular disorder | Bungkus animasi dalam `@media (prefers-reduced-motion: reduce)` yang mematikan awan melayang dan getaran mobil, mempertahankan visual statis yang tetap anggun. |

---

## 9. Kriteria Selesai (Definition of Done)
- [x] Background kanvas kiri berubah menjadi lanskap hidup Sleman (Merapi, awan melayang, kantor Diskominfo).
- [x] Terdapat animasi **Karakter Orang Bergerak (SVG)** di setiap tahapan (mengetik laptop, stempel berkas, diskusi mentor, dan selebrasi kelulusan).
- [x] Mobil dinas SVG tampil sinematik dengan lampu sorot depan (*headlight beam*), sirine menyala, dan laju yang ekspresif.
- [x] Kontrol zoom (+, -, Semua) dan bilah tab horizontal bawah tetap berfungsi secara sinkron.
- [x] Panel inspeksi & checklist kesiapan di sisi kanan tetap terhubung dua arah secara instan.
- [x] Tampilan pop-up modal luas, mewah, berkesan *WOW*, dan tidak kaku/gelap mati.
- [x] Seluruh automated test `php artisan test --filter=LandingPagePetaAlurModalTest` lulus 100% hijau.
