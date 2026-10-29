# Rencana Kerja: Revamp Peta Alur Interaktif — Peningkatan Kualitas Visual SVG, Tata Letak Bebas Tabrakan, & Animasi Mobil Interaktif yang Dapat Dikendalikan

Dokumen rencana kerja ini merespons umpan balik pengguna:
> *"saya mau ganti di bagian peta alur interaktif coba deh km liat masih ada yang kurang dan svg animasi nya kurang menarik dan ga jelas dan kemarin katanya ada animasi mobil yang bisa di gerakan juga coba buat plan nya"*

Disusun sesuai panduan **Web Planning Workflow** untuk menganalisis kekurangan saat ini, merumuskan opsi solusi, serta menyusun rencana kerja terstruktur sebelum penulisan kode dimulai.

---

## 1. Analisis Masalah Saat Ini & Kebutuhan Pengguna

Berdasarkan inspeksi visual (tangkapan layar pengguna) dan kode sumber saat ini di [resources/views/public/peta-alur-interaktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php):

| No | Temuan Masalah (Kekurangan Saat Ini) | Akar Masalah Teknis | Solusi yang Diterapkan |
| :-: | :--- | :--- | :--- |
| **1** | **Elemen di Pos 1 Saling Tumpang Tindih & Teks Terpotong** | Meja dan karakter siswa (`charScene1`) ditempatkan di `(140, 260)`, menimpa langsung pill label "Registrasi" dan lingkaran angka `1`. | **Pemisahan Layer Posisi & Halo Bersih**: Posisikan karakter sedikit di samping halte/jalan, berikan *visual breathing room*, dan atur label teks agar tidak pernah menimpa ilustrasi karakter. |
| **2** | **Gedung Diskominfo Tertabrak Garis Jalan Aspal** | Gedung ditaruh di `x=730, y=240` sementara kurva aspal lewat di `x=700..780, y=260..360`. Aspal hitam memotong bagian bawah gedung dan plang teks `DISKOMINFO SLEMAN`. | **Reposisi Gedung Berada di Belakang / Samping Jalan yang Terbuka**: Pindahkan kompleks kantor Diskominfo sedikit lebih ke atas/kanan dengan orientasi halaman masuk yang menghadap langsung ke jalan tanpa terpotong aspal. |
| **3** | **Label Pos 4 ("Kelulusan") Terpotong di Batas Bawah Layar** | Pos 4 di `y=433` dengan pill label di bawahnya (`+32px`) menyentuh batas bawah kanvas dan tertutup bilah *bottom timeline dock*. | Naikkan kurva akhir Pos 4 ke `y=400..415` dan letakkan label pill di posisi yang aman (*viewport-safe margin*). |
| **4** | **Mobil Tidak Terlihat Jelas & Tidak Bisa Digerakkan Bebas** | Mobil berukuran terlalu kecil (36x18 px), tertutup oleh pin GPS raksasa dan lingkaran checkpoint, serta hanya berpindah saat tab diklik. Pengguna tidak bisa "menyetir" atau menggeser mobil. | **Mobil Dinas Sleman yang Gagah & Interaktif**: <br>1. Perbesar dimensi mobil ke ukuran proporsional (~56x28 px) dengan detail khas mobil patroli dinas Sleman (bodi putih-navy, lampu LED sorot, lampu sirip strobo, roda sporty berputar).<br>2. **Fitur Pengendalian Mobil Langsung**: Pengguna bisa menyeret (*drag & scrub*) mobil di sepanjang jalan, menggerakkan via tombol gas/rem atau tombol panah keyboard (`←` / `→`), serta klik titik jalan mana pun untuk melaju ke sana. |
| **5** | **SVG dan Animasi Kurang Menarik & Tidak Jelas** | Ilustrasi terasa terlalu kaku, tidak memiliki kesan halte perhentian nyata di tiap pos, garis marka jalan statis, dan kontrol zoom di sisi kiri terlihat seperti kontrol peta standar yang terlepas dari desain utama. | **Pengayaan Visual Diorama Sleman Hidup**: <br>• Buat halte/pos perhentian bergaya modern ramah civic-tech di setiap pos.<br>• Efek visual hidup: lampu sorot mobil berpendar dinamis di aspal, garis marka jalan bergerak halus (*dash offset animation*), efek rumput dan pepohonan asri bertingkat, serta asap kawah Merapi tipis.<br>• Satukan kontrol zoom dan mode sinema ke dalam floating glass island yang elegan di pojok kiri atas. |

---

## 2. Evaluasi Opsi Pendekatan Teknis (Interaktivitas Mobil)

Untuk memenuhi kebutuhan *"animasi mobil yang bisa digerakkan"*, berikut perbandingan 2 pendekatan teknis:

### Opsi A: *Hybrid Interactive Vehicle Engine* (Drag along Path + Drive HUD Controls + Keyboard Arrow Navigation) — **(Direkomendasikan)**
- **Cara Kerja**:
  - Mobil diikat ke matematika kurva SVG (`SVGPathElement.getPointAtLength`).
  - **Interaksi 1 (Drag & Scrub)**: Pengguna dapat mengklik dan menarik ikon mobil langsung di layar menyusuri aspal dari Pos 01 hingga Pos 04.
  - **Interaksi 2 (Cockpit Control HUD)**: Disediakan kontrol kemudi sederhana di layar bawah/samping: tombol `[◀ Mundur / Prev]`, slider progress perjalanan, dan `[Gas / Maju ▶]`.
  - **Interaksi 3 (Keyboard Keys)**: Menekan tombol panah kiri (`←`) atau kanan (`→`) di keyboard akan menjalankan mobil maju/mundur dengan animasi suspensi dan suara mesin ramah.
  - **Interaksi 4 (Click-on-Road / Snap-to-Post)**: Mengklik titik mana pun pada jalan atau tombol tab bawah akan menggerakkan mobil secara mulus ke titik tersebut dengan deteksi otomatis pos terdekat.
- **Kelebihan**: Sangat interaktif, memuaskan rasa ingin tahu pengunjung (*gamified experience*), responsif di layar sentuh (mobile/tablet) maupun mouse/keyboard (desktop).
- **Kekurangan**: Memerlukan penanganan pointer event yang rapi (touch & mouse) dan normalisasi rasio panjang lintasan SVG.

### Opsi B: *Pure Step-by-Step Waypoint Clicker Only*
- **Cara Kerja**: Pengguna hanya bisa menekan tombol "Maju" atau "Mundur" untuk menggeser mobil dari satu pos ke pos lainnya, tanpa bisa menarik mobil atau mengeksplorasi posisi di antara dua pos.
- **Kelebihan**: Implementasi lebih sederhana.
- **Kekurangan**: Terasa seperti slider biasa, tidak menjawab ekspektasi pengguna yang menginginkan sensasi mobil yang benar-benar bisa "digerakkan".

> **Keputusan**: Menggunakan **Opsi A (Hybrid Interactive Vehicle Engine)** demi memberikan pengalaman simulator terbaik yang interaktif, menyenangkan, dan berkelas.

---

## 3. Rincian Desain & Tata Letak Baru (Tanpa Tabrakan)

```
+---------------------------------------------------------------------------------------------------+
| [← Beranda] [PETA ALUR INTERAKTIF] Simulator Magang Sleman           [🔊 Suara] [▶ Tour] [Daftar] |
+---------------------------------------------------------------------------------------------------+
|  [+ / - / Reset]                                                                                  |
|                        ▲ GUNUNG MERAPI MEGAH & ASRI (y: 60 - 220)                                 |
|                       / \  (Asap kawah putih tipis melayang)                                      |
|                                                                                                   |
|  [POS 1: REGISTRASI] ==========================> [POS 2: ADMINISTRASI & SELEKSI]                  |
|  • Halte Sleman Barat (x: 180, y: 240)           • Halte Verifikasi Sleman Timur (x: 700, y: 250) |
|  • Karakter di sisi aman                         • Di samping Gedung Diskominfo Sleman            |
|       \                                                /                                          |
|        \        ===> [MOBIL DINAS KOMINFO SLEMAN] ===>  (Dapat di-drag / dikemudikan)             |
|         \                                            /                                            |
|  [POS 3: PELAKSANAAN MAGANG] <======================/                                             |
|  • Halte Bimbingan & Logbook (x: 230, y: 390)                                                     |
|       \                                                                                           |
|        ========================================> [POS 4: KELULUSAN & DISKOMINFO SLEMAN]           |
|                                                  • Halaman Utama Diskominfo (x: 730, y: 405)      |
|                                                  • Wisudawan Ceria & Sertifikat Emas              |
+---------------------------------------------------------------------------------------------------+
| [Kontrol Kemudi: ◀ Mundur | =====🔘======== Slider Perjalanan | Maju / Gas ▶]                     |
| [Tab 01 Registrasi]  [Tab 02 Administrasi]  [Tab 03 Pelaksanaan Magang]  [Tab 04 Kelulusan]      |
+---------------------------------------------------------------------------------------------------+
```

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/peta-alur-interaktif.blade.php` | Modify | 1. **Tata Letak Bebas Tabrakan**: Geser posisi Pos 1 agar karakter dan label tidak bertumpuk; reposisi Gedung Diskominfo agar tidak terpotong jalan aspal; naikkan Pos 4 agar tidak terpotong bilah bawah.<br>2. **Mobil Dinas Interaktif**: Redesain mobil dinas menjadi lebih besar, gagah, dan proporsional (~56x28px) dengan sorot lampu dinamis.<br>3. **Engine Gerak Mobil**: Tambahkan logika pointer drag & touch di sepanjang kurva SVG, navigasi keyboard panah (`←`/`→`), slider perjalanan interaktif, dan deteksi snap otomatis ke pos terdekat.<br>4. **Peningkatan Kualitas SVG**: Buat halte/checkpoint berdesain modern, rapikan floating controls di kiri atas menjadi panel kaca bersih. |
| `public/css/peta-alur-fullscreen.css` | Modify | 1. Styling visual baru untuk mobil dinas dan efek sorot lampu (*headlight beam*).<br>2. Styling kontrol kemudi mini (*interactive drive scrubber & controls*).<br>3. Perbaikan z-index dan layout floating zoom control agar menyatu harmonis dengan antarmuka portal.<br>4. Micro-animations untuk putaran roda mobil dan feedback visual saat mobil disentuh/di-drag. |
| `tests/Feature/PetaAlurPageTest.php` | Modify | Tambahkan pengujian fitur kontrol mobil baru, verifikasi ketiadaan tabrakan posisi, dan pastikan seluruh test skenario lolos 100%. |

---

## 5. Langkah Implementasi Terstruktur

### Tahap 1: Koreksi Tata Letak & Pembersihan Tabrakan Koordinat SVG
- [x] Buka [resources/views/public/peta-alur-interaktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php).
- [x] Perbarui koordinat jalan aspal (`#svgRoadBase`, `#svgRoadProgress`, `#svgRoadBorder`, `#svgRoadDashed`) agar kurva mengalir luwes dengan ruang bebas yang cukup di tiap pos.
- [x] Atur ulang koordinat Pos 1 (`roadCp1`), pisahkan karakter siswa meja kerja (`charScene1`) ke koordinat yang rapi di samping pos tanpa menutupi nomor atau label "Registrasi".
- [x] Reposisi kompleks Gedung Diskominfo Sleman (`#diskominfoHubScene`) ke posisi aman di atas dataran kanan (`y: 190..230`) sehingga jalan raya melintas rapi di depan halaman gedung tanpa memotong plang atau dinding gedung.
- [x] Pindahkan Pos 4 (`roadCp4`) ke area yang aman dari tepi bawah (`y: 405`), sehingga badge dan label "Kelulusan" tampil utuh tanpa terpotong bottom bar.

### Tahap 2: Redesain Visual Mobil Dinas Diskominfo Sleman
- [x] Perbesar dan percantik grup SVG mobil `#modalRoadCarGroup`:
  - Dimensi proporsional: ~56px panjang x 28px lebar.
  - Warna: Putih mutiara dipadu striping Navy Blue Diskominfo dan aksen emas Sleman.
  - Detail: Kaca depan biru cerah transparan, sirip strobo atap, lampu depan LED ganda dengan proyeksi cahaya berpendar ke aspal, 4 roda sporty dengan bayangan realistis di bawah aspal.
  - Cursor pointer saat diarahkan (`cursor: grab` / `cursor: grabbing` saat di-drag).

### Tahap 3: Implementasi Mesin Interaktivitas Mobil (Dapat Digerakkan)
- [x] Terapkan fungsi drag-and-drop / touch gesture pada mobil `#modalRoadCarGroup`:
  - Mengonversi posisi pointer (mouse/touch) ke koordinat terdekat pada kurva SVG aspal (`roadPath`).
  - Mengupdate rotasi sudut mobil mengikuti kemiringan jalan aspal secara real-time saat diseret.
- [x] Tambahkan dukungan keyboard:
  - Tombol `ArrowRight` / `KeyD` untuk menjalankan mobil maju ke depan.
  - Tombol `ArrowLeft` / `KeyA` untuk menjalankan mobil mundur.
- [x] Sediakan mini control dock / drive scrubber:
  - Tombol `[◀ Pos Sebelumnya]`, slider lintasan jalan, dan `[Pos Berikutnya ▶]`.
- [x] Buat logika auto-snap: saat mobil berhenti dekat suatu checkpoint, mobil menepi halus ke checkpoint tersebut, kartu pop-up info pos terbuka otomatis, dan chime audio berbunyi ramah.

### Tahap 4: Pengayaan Visual & Detail Animasi SVG
- [x] Perbarui visual checkpoint menjadi *Civic Station Halte* yang modern dengan ikon visual masing-masing tahap (1: Akun Mandiri, 2: Seleksi Berkas, 3: Pelaksanaan Magang & Logbook, 4: Kelulusan & Nilai).
- [x] Rapikan widget zoom kontrol di sisi kiri atas dengan panel glassmorphism kompak.
- [x] Pastikan transisi kamera panorama (`viewBox zoom/pan`) mengikuti pergerakan mobil secara lembut saat di-drag atau dikemudikan.

### Tahap 5: Pengujian Otomatis & Verifikasi Kualitas
- [x] Perbarui file pengujian di [tests/Feature/PetaAlurPageTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/PetaAlurPageTest.php).
- [x] Jalankan test suite:
  - `php artisan test --filter=PetaAlurPageTest`
  - `php artisan test --filter=LandingPagePetaAlurModalTest`
  - `php artisan test --filter=AlurMagangPageTest`
- [x] Jalankan linter dan code styling: `vendor/bin/pint tests/Feature/PetaAlurPageTest.php --test`.

---

## 6. Strategi Testing

1. **Feature Test Otomatis (`php artisan test`)**:
   - Memastikan rute `/peta-alur` merespons HTTP 200 dengan seluruh markup mobil interaktif, kontrol kemudi, dan elemen SVG bebas tabrakan.
   - Memastikan tidak ada teks yang hilang atau overlap.
2. **Pengujian Interaksi Manual**:
   - Pengujian drag mobil menggunakan mouse dan touch gesture.
   - Pengujian tombol panah keyboard (`ArrowLeft` / `ArrowRight`).
   - Pengujian tour otomatis (*autoplay*).
   - Pengujian respon audio saat mobil sampai di tiap pos.
3. **Pengujian Tampilan Lintas Resolusi**:
   - Memastikan tampilan penuh 100vw x 100vh tanpa scrollbar ganda atau teks terpotong di resolusi 1920x1080, 1366x768, dan layar tablet/laptop.

---

## 7. Risiko & Mitigasi

- **Risiko Performa pada SVG Dragging**: Perhitungan `getPointAtLength` secara berulang saat drag berpotensi menyebabkan frame drop jika tidak dioptimalkan.
  - *Mitigasi*: Gunakan `requestAnimationFrame` untuk throttling pergerakan mobil saat diseret pointer.
- **Risiko Responsivitas Touch**: Di perangkat sentuh, gesture drag bisa memicu scroll layar browser.
  - *Mitigasi*: Tambahkan `touch-action: none` pada elemen mobil dan kanvas SVG interaktif.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Tidak ada lagi elemen bertumpuk di Pos 1 (karakter dan label terpisah rapi).
- [x] Gedung Diskominfo Sleman berdiri kokoh di samping jalan tanpa tertabrak aspal jalan raya.
- [x] Pos 4 dan label "Kelulusan" berada dalam batas aman layar dan tidak terpotong bottom bar.
- [x] Mobil dinas berukuran proporsional, tampak jelas di atas jalan aspal, memiliki lampu sorot dan bayangan realistis.
- [x] Pengguna dapat menggerakkan mobil secara bebas: via drag/sentuhan di layar, via tombol kontrol di HUD, serta via tombol panah keyboard.
- [x] Ketika mobil mendekati atau mencapai pos, pop-up info pos terkait muncul otomatis dan chime ramah berbunyi.
- [x] Seluruh unit & feature test lolos 100% hijau.
