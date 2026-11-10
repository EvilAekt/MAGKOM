# Rencana Kerja: Integrasi Interactive Road Map Canvas SVG & Mobil Animasi pada Pop-Up Modal Alur

Dokumen ini merinci rencana transformasi visual sisi kiri pop-up modal alur magang menjadi **Interactive Road Map Canvas** berstandar grafis tinggi sesuai tangkapan layar referensi pengguna: menampilkan jalan raya berkelok (curved highway), pin waypoint GPS dinamis, kontrol zoom in/out/fit, mobil dinas SVG yang melaju beranimasi, serta bilah navigasi tahapan horizontal dengan penghitung langkah aktif.

---

## 1. Tujuan
1. **Perombakan Visual Sisi Kiri Modal**: Menggantikan list vertikal statis di sisi kiri dengan **Interactive Road Map Canvas SVG** (seperti referensi visual pengguna):
   - Kanvas berlatar navy gelap modern (`#0B132B` / `#0F172A`).
   - Jalan raya berkelok dengan garis putus-putus tengah dan jalur progres hijau (*active trail*).
   - Checkpoint nodes 1–4 dengan pin lokasi (GPS Pin) kuning bercahaya di atas node aktif.
   - Tab horizontal 4 tahap di bagian bawah kanvas (1: Registrasi, 2: Pengajuan, 3: Pelaksanaan, 4: Evaluasi) lengkap dengan counter langkah dinamis (`0/4 langkah`, `1/4 langkah`, dst.).
2. **Animasi Mobil SVG Bergerak Ringan**: Mengintegrasikan mobil dinas MagKom berbasis SVG murni yang melaju secara mulus (*smooth path-following transition*) di sepanjang kurva jalan saat pengguna memilih tahap atau menekan tombol navigasi.
3. **Kontrol Interaktif Zoom**: Menyediakan tombol kontrol mengambang di pojok kanan atas kanvas:
   - `+` (Zoom In)
   - `-` (Zoom Out)
   - `Semua` (Reset / Fit All View)
4. **Pelebaran Dimensi Pop-Up**: Memperlebar container modal (`max-width: 1220px` s.d. `1260px`) agar kanvas peta visual di sisi kiri dan panel checklist di sisi kanan tampil proporsional, lapang, dan nyaman dibaca.
5. **Sinkronisasi Dua Arah (*Bi-Directional Sync*)**:
   - Memilih tahap di canvas kiri (klik pin / tab) $\rightarrow$ mobil melaju dan panel kanan memperbarui checklist kesiapan.
   - Mencetak checklist di panel kanan $\rightarrow$ counter langkah di tab bawah canvas kiri langsung ter-update secara *real-time*.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi:
- **Asumsi 1**: Grafis jalan raya dan mobil diimplementasikan 100% menggunakan elemen **SVG vektor murni dan Vanilla JavaScript** tanpa library berat eksternal (seperti Three.js atau Leaflet), sehingga ukuran aset sangat ringan (<15 KB), waktu muat instan (<5 ms), dan performa 60 FPS di semua perangkat.
- **Asumsi 2**: Kontrol zoom in/out menerapkan CSS `transform: scale()` dan `transform-origin` terpusat pada kanvas SVG dengan batas aman (*safe boundaries* min 0.8x s.d. max 1.6x).
- **Asumsi 3**: Pada layar mobile kecil (< 860px), tata letak beralih secara responsif menjadi *stacked layout* vertikal di mana canvas jalan menyesuaikan tinggi proporsional dan tetap dapat di-zoom/geser.

### Pertanyaan Terbuka:
- *(Tidak ada pertanyaan pemblokir; konsep visual dan spesifikasi teknis telah sangat jelas berdasarkan gambar referensi yang dilampirkan pengguna).*

---

## 3. Pendekatan Teknis & Perbandingan Arsitektur

| Kriteria | Opsi A: Pure SVG Path + CSS Transform Matrix (Rekomendasi) | Opsi B: HTML5 Canvas 2D Bitmap Rendering |
| :--- | :--- | :--- |
| **Deskripsi** | Merender lintasan jalan, mobil, dan waypoint sebagai elemen `<svg>` DOM murni. Animasi mobil menggunakan kalkulasi `path.getPointAtLength()` dan rotasi tangen sudut. | Menggambar lintasan dan mobil frame-by-frame di atas tag `<canvas>` menggunakan JavaScript 2D context loop. |
| **Beban & Bobot Kode** | **Sangat Ringan (<10 KB)**, aset inline tanpa download script eksternal. | Lebih berat, butuh kode manual untuk resolusi Retina/HiDPI dan re-drawing loop. |
| **Ketajaman Grafis** | **Vektor Sempurna (Crisp)** pada semua tingkat zoom dan layar Retina. | Berisiko pecah atau buram saat di-zoom jika resolusi canvas tidak diatur ulang secara dinamis. |
| **Interaktivitas & Aksesibilitas** | **Sangat Baik**; node dapat menerima event klik langsung (`cursor: pointer`), memiliki atribut ARIA, dan mudah disinkronkan dengan state. | Sulit; perlu kalkulasi koordinat raycasting manual `(x, y)` mouse untuk mendeteksi klik pada titik checkpoint. |
| **Rekomendasi** | **Dipilih Opsi A**. Sesuai standar performa tinggi MAGKOM dan arsitektur yang sudah ada pada `landing-interactive.js`. | Tidak dipilih. |

---

## 4. Konsep Visual & Tata Letak Baru (UI/UX)

```
+--------------------------------------------------------------------------------------------------------+
| [HEADER] Peta Alur Interaktif  ·  Simulasi Mandiri                                                 [X] |
+--------------------------------------------------------------------------------------------------------+
| [TOOLBAR] Kategori Jenjang: [Semua | SMK | D3/S1]    Tipe: [Mandiri | Kelompok]    Rencana: [Juli 2026] |
+--------------------------------------------------------------------------------------------------------+
|  KIRI: ROAD MAP CANVAS SVG (Dark Navy Theme)         | KANAN: INSPECTION & CHECKLIST PANEL             |
|  +-------------------------------------------------+ |                                                 |
|  |                                  [+] [-] [Semua]| | Tahap 4: Evaluasi & E-Sertifikat                |
|  |          (S-Curve Highway)                      | | Estimasi: 1 Oktober – 5 Oktober 2026            |
|  |            [1]                                  | |                                                 |
|  |             \  [Mobil SVG melaju]               | | [Description box...]                           |
|  |              [2]                                | |                                                 |
|  |                \                                | | [UJI KESIAPAN & KETENTUAN WAJIB]                |
|  |                 [3]                             | | [x] Menyelesaikan laporan akhir                 |
|  |                   \   (GPS Pin)                 | | [x] Mengisi survei kepuasan layanan             |
|  |                    \    [v]                     | | [ ] Pembimbing menginput nilai akhir            |
|  |                     --> [4] Evaluasi            | | [ ] Mengunduh E-Sertifikat Resmi ber-QR         |
|  +-------------------------------------------------+ |                                                 |
|  | [1 Registrasi] [2 Pengajuan] [3 Pelaksanaan] [4]| | [DOKUMEN TERKAIT]                               |
|  |    0/4 langkah    0/5 langkah    4/4 langkah (4)| | [Laporan Akhir] [Lembar Nilai] [E-Sertifikat]   |
|  +-------------------------------------------------+ |                                                 |
|                                                      | [< Tahap Sebelumnya]        [Mulai Daftar 🚀]   |
+--------------------------------------------------------------------------------------------------------+
| [FOOTER] Klik tahapan atau jalan untuk melihat detail.                     [Tutup] [Mulai Daftar 🚀]  |
+--------------------------------------------------------------------------------------------------------+
```

---

## 5. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/partials/_peta_alur_modal.blade.php` | Modify | 1. Memperlebar container modal (`modal-container-wide`).<br>2. Merestrukturisasi kolom kiri menjadi Interactive Road Map Canvas SVG lengkap dengan lintasan jalan berkelok, animated mini car, pin GPS waypoint, tombol zoom (+, -, Semua), dan tab horizontal 4 tahap di bagian bawah.<br>3. Menambahkan logika animasi mobil SVG berpindah koordinat dan rotasi ke titik checkpoint aktif.<br>4. Menghubungkan counter checklist di panel kanan ke tab horizontal di bawah canvas. |
| `public/css/landing-peta-alur-modal.css` | Modify | 1. Update lebar modal (`max-width: 1240px`).<br>2. Styling kanvas peta jalan (Dark Navy `#0a1120`, border, backdrop glow).<br>3. Styling tombol zoom mengambang (`btn-road-zoom`).<br>4. Styling tab horizontal bawah canvas (`road-step-tab-bar`, badge warna 1-merah, 2-ungu, 3-biru, 4-kuning).<br>5. Animasi pulsa GPS Pin dan rotasi haluan mobil SVG.<br>6. Styling responsif untuk layar HP dan tablet. |
| `tests/Feature/LandingPagePetaAlurModalTest.php` | Modify | Menambahkan assertion untuk memvalidasi komponen canvas jalan (`id="modalRoadCanvas"`), kontrol zoom (`btn-road-zoom`), elemen mobil SVG (`id="modalRoadCar"`), dan tab bar tahapan horizontal. |

---

## 6. Langkah Implementasi Terstruktur

### Tahap 1: Pembesaran Container Modal & Struktur HTML Canvas Kiri
- [x] Buka [resources/views/public/partials/_peta_alur_modal.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/partials/_peta_alur_modal.blade.php).
- [x] Ubah struktur `.peta-alur-steps-column` menjadi `.peta-alur-road-column` yang memuat:
  - Header kontrol zoom mengambang: Tombol `+` (Zoom In), `-` (Zoom Out), dan `Semua` (Reset Zoom).
  - Elemen SVG Viewport Peta Jalan (`viewBox="0 0 520 340"`):
    - Kurva aspal jalan raya (`path` berkelok melintang halus dari kiri atas ke kanan bawah).
    - Lapisan garis hijau progres aktif (*glowing trail*).
    - Garis putus-putus marka tengah jalan raya.
    - 4 Checkpoint Nodes melingkar dengan nomor 1, 2, 3, dan 4.
    - Marker Waypoint GPS Pin kuning bercahaya di atas node aktif.
    - Elemen mobil SVG (*Mini MagKom Car*) dengan transform koordinat & rotasi.
  - Bilah Tab Horizontal 4 Tahap di bawah canvas (1: Registrasi, 2: Pengajuan, 3: Pelaksanaan, 4: Evaluasi) dengan indikator garis aktif dan counter langkah (`0/4 langkah`, dll).

### Tahap 2: Logika Animasi Mobil SVG & Kontrol Zoom (JavaScript)
- [x] Di dalam script `_peta_alur_modal.blade.php`:
  - Definisikan koordinat kurva path jalan dan persentase panjang path untuk masing-masing Checkpoint 1, 2, 3, 4.
  - Buat fungsi `animateCarToStep(targetStep)`:
    - Menggerakkan posisi mobil SVG menggunakan `requestAnimationFrame` atau transisi lerp halus di sepanjang path SVG.
    - Menghitung sudut tangen rotasi mobil `Math.atan2(dy, dx)` agar moncong mobil selalu menghadap ke arah belokan jalan.
    - Memindahkan posisi marker GPS Pin ke atas checkpoint yang aktif dengan animasi lompat/bounce mikro.
    - Mengupdate panjang garis hijau progres jalan.
  - Buat fungsi kontrol Zoom: `modalRoadZoomIn()`, `modalRoadZoomOut()`, dan `modalRoadZoomReset()` dengan batas skala `0.85x` hingga `1.5x`.
  - Hubungkan fungsi checklist `updateChecklistUI()` agar counter langkah pada tab bawah canvas kiri selalu ter-update secara otomatis saat checkbox dicentang.

### Tahap 3: Styling Visual Canvas Jalan & Komponen Animasi (CSS)
- [x] Buka [public/css/landing-peta-alur-modal.css](file:///d:/MAGKOM/MAGKOM/public/css/landing-peta-alur-modal.css).
- [x] Ubah `max-width` container modal menjadi `1240px` dan atur rasio grid kolom kiri & kanan (contoh: `530px 1fr`).
- [x] Tambahkan styling untuk:
  - `.modal-road-canvas-wrap` (background dark navy `#0a1120`, border halus, overflow hidden).
  - `.road-zoom-controls` (tombol floating semi-transparan `+`, `-`, dan pill `Semua`).
  - `.road-gps-pin` (pin lokasi kuning dengan bayangan drop shadow dan animasi bouncing lembut).
  - `.modal-road-tab-bar` (tata letak horizontal 4 kolom dengan flexbox/grid).
  - Warna aksen masing-masing tab:
    - Tab 1: Aksen Merah `#e11d48`
    - Tab 2: Aksen Ungu `#6366f1`
    - Tab 3: Aksen Biru `#0284c7`
    - Tab 4: Aksen Kuning Emas `#eab308`
  - Efek garis bawah aktif (*active indicator bar*) pada tab yang sedang dipilih.
  - Penyesuaian responsif layar HP (canvas proporsional dan tab horizontal dapat di-scroll).

### Tahap 4: Verifikasi & Test Suite
- [x] Perbarui file [tests/Feature/LandingPagePetaAlurModalTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/LandingPagePetaAlurModalTest.php):
  - Assert keberadaan elemen kanvas jalan (`modalRoadCanvas`).
  - Assert keberadaan tombol zoom (`btn-road-zoom-in`, `btn-road-zoom-out`, `btn-road-zoom-reset`).
  - Assert keberadaan grup mobil SVG (`modalRoadCarGroup`).
  - Assert keberadaan 4 tab tahapan horizontal dan counter langkah.
- [x] Jalankan pengujian test suite via `php artisan test --filter=LandingPagePetaAlurModalTest`.
- [x] Verifikasi kestabilan tampilan di browser Laragon `http://magkom.test`.

---

## 7. Strategi Testing

1. **Automated Feature Testing**:
   - `LandingPagePetaAlurModalTest`: Memastikan komponen kanvas peta jalan, mobil SVG, tombol zoom, dan tab tahapan horizontal ter-render dengan benar.
   - `LandingPageTest` & `AlurMagangPageTest`: Memastikan landing page dan rute panduan alur tidak mengalami regresi.
2. **Interactive Functional & Visual Testing**:
   - Klik Tab 1, 2, 3, 4 $\rightarrow$ Mobil SVG melaju mulus di lintasan jalan menuju titik yang dipilih dan pin GPS melompat ke atas node aktif.
   - Klik tombol `+` $\rightarrow$ Kanvas jalan membesar halus (*zoom in*).
   - Klik tombol `-` $\rightarrow$ Kanvas jalan mengecil halus (*zoom out*).
   - Klik tombol `Semua` $\rightarrow$ Kanvas jalan kembali ke skala normal (*1.0x*).
   - Centang checkbox di panel kanan $\rightarrow$ Angka counter pada tab horizontal bawah (misal `0/4 langkah` menjadi `1/4 langkah`) langsung ter-update secara *real-time*.

---

## 8. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Kinerja Animasi pada Perangkat Rendah** | Animasi mobil terasa patah-patah | Gunakan koordinat kalkulasi berbasis CSS transform GPU-accelerated (`transform: translate(...) rotate(...)`) dan requestAnimationFrame. |
| **Keterbatasan Ruang pada Layar Mobile (<768px)** | Kanvas dan panel kanan berhimpitan | Terapkan media query responsif di mana kanvas jalan berada di atas dengan tinggi terkontrol (`height: 240px`) dan panel checklist di bawahnya dengan scroll mandiri. |
| **Aksesibilitas Pengguna Keyboard** | Pengguna yang tidak menggunakan mouse kesulitan menavigasi peta jalan | Sediakan navigasi tab keyboard native (`role="tab"`, panah kiri/kanan, tombol Enter/Space) pada tab horizontal di bawah canvas. |

---

## 9. Kriteria Selesai (Definition of Done)
- [x] Pop-up modal alur diperlebar secara elegan (`max-width: 1240px`).
- [x] Sisi kiri modal menampilkan **Interactive Road Map Canvas** berlatar dark navy sesuai gambar referensi pengguna.
- [x] Lintasan jalan berkelok SVG terpasang dengan garis tengah putus-putus dan garis progres aktif.
- [x] Terdapat **Mobil Dinas SVG** yang beranimasi melaju mengikuti belokan jalan saat berpindah tahap.
- [x] Terdapat **Pin Lokasi (GPS Marker)** kuning bercahaya di atas node tahap yang aktif.
- [x] Kontrol zoom mengambang (`+`, `-`, `Semua`) berfungsi dengan mulus.
- [x] Bilah tab horizontal 4 tahap di bawah kanvas menampilkan status langkah (`0/4 langkah`, dll) yang sinkron dengan checklist di panel kanan.
- [x] Panel inspeksi di sisi kanan tetap berfungsi penuh (checklist, dokumen tips, estimasi jadwal).
- [x] Seluruh automated test `php artisan test --filter=LandingPagePetaAlurModalTest` berhasil lulus 100% hijau.
