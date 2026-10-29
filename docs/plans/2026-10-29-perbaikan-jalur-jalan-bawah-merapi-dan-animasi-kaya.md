# Rencana Kerja: Perbaikan Tata Letak Jalan Raya di Bawah Gunung Merapi & Pengayaan Animasi Peta Alur Interaktif

Dokumen rencana kerja ini merespons umpan balik pengguna:
> *"tolong perbaiki lagi masa jalan ada di gunung dan animasinya kurang"*

Sesuai panduan **Web Planning Workflow**, dokumen ini menganalisis masalah, merancang solusi tata letak jalan yang logis dan proporsional di dataran hijau bawah Merapi, serta merancang sistem animasi interaktif yang kaya, hidup, dan memukau sebelum penulisan kode dimulai.

---

## 1. Analisis Masalah Saat Ini

| Aspek | Kondisi Saat Ini | Masalah & Dampak Visual | Solusi yang Diharapkan |
| :--- | :--- | :--- | :--- |
| **1. Posisi Jalan Menabrak Gunung** | Puncak Merapi di `(480, 95)` alas di `y=410`. Jalan aspal kurva atas melintas di `y=80..135` persis di atas lereng dan kawah Merapi. | Tidak masuk akal secara logika visual lanskap: jalan raya aspal membelah tubuh dan puncak gunung berapi di langit. | **Relokasi Gunung & Jalan**: Posisikan Gunung Merapi di latar belakang horizon jauh (utara) di bagian atas (`y=80..260`). Letakkan jalan raya aspal sepenuhnya di **dataran rendah / lembah hijau Sleman** (`y=240..460`) di bawah kaki gunung, sehingga jalan berkelok alami di pedesaan/kota Sleman tanpa pernah menabrak gunung. |
| **2. Animasi Masih Minim / Kurang Hidup** | Mobil hanya bergeser sepanjang path; tidak ada suspensi dinamis, tidak ada efek roda, tidak ada jejak aspal, pop-up muncul instan tanpa spring pop-in, awan & Merapi statis. | Diorama terasa mati (*flat*), kurang memberikan sensasi visual yang memukau (*WOW effect*) sesuai standar aplikasi modern. | **Pengayaan Animasi Menyeluruh**: Tambahkan animasi suspensi mobil, lampu sorot LED dinamis, garis marka jalan bergerak halus, gelombang riak checkpoint ganda (*pulse ripple*), kepulan kabut puncak Merapi, pepohonan bergoyang, konfeti kelulusan, dan animasi transisi kartu pop-up Gambar 2 yang sangat mulus. |

---

## 2. Rancangan Tata Letak Lanskap & Kurva Jalan Baru

### Komposisi Visual Kanvas (960 x 540)
```
+---------------------------------------------------------------------------------+
| [LANGIT SIANG BIRU CERAH]      ☁ Awan Berarak        ☀ Matahari Hangat Sleman   |
|                                                                                 |
|                        ▲ GUNUNG MERAPI MEGAH (y: 80 - 250)                      |
|                       / \  (Asap Kawah Tipis Melayang Lembut)                   |
|                      /   \                                                      |
|  ~~~~ Perbukitan Hijau Bertingkat Sleman (Rolling Hills y: 220 - 320) ~~~~~~~~ |
|                                                                                 |
|  [POS 1: Registrasi] -----------------> [POS 2: Administrasi & Seleksi]         |
|  (x: 180, y: 240)                       (x: 700, y: 260)                        |
|  (Laptop & Akun)                        (Meja Verifikasi Admin)                 |
|         \                                      /                                |
|          \                                    /                                 |
|           \                                  /                                  |
|  [POS 3: Pelaksanaan Magang] <--------------                                    |
|  (x: 240, y: 410)                                                               |
|  (Mentor & Progres Tugas) ------------> [POS 4: Kelulusan & Gedung DISKOMINFO]  |
|                                         (x: 740, y: 430) (Gedung & Wisudawan)   |
|                                                                                 |
+---------------------------------------------------------------------------------+
```

### Detail Koordinat Kurva Jalan Baru:
- **Jalan Aspal Baru** membentang di dataran hijau:
  `d="M 60, 240 C 220, 220 540, 230 700, 260 C 820, 280 780, 360 540, 370 C 340, 380 140, 385 240, 410 C 360, 435 600, 440 840, 430"`
- **Penempatan 4 Checkpoint**:
  - **Pos 1 (Registrasi)**: `x = 180, y = 232` (Dataran Sleman Barat, teduh dengan pepohonan)
  - **Pos 2 (Administrasi & Seleksi)**: `x = 700, y = 260` (Dataran Sleman Timur, dekat area perkantoran)
  - **Pos 3 (Pelaksanaan Magang)**: `x = 240, y = 410` (Area ruang kerja magang, laptop & mentor)
  - **Pos 4 (Kelulusan)**: `x = 740, y = 430` (Halaman utama Kantor Diskominfo Sleman)

Dengan koordinat ini:
1. Jalan raya **100% berada di bawah kaki bukit dan Merapi** (`y >= 230`), tidak ada satu jengkal pun jalan yang naik ke kawah atau badan gunung!
2. Pemandangan Gunung Merapi berdiri bebas, megah, dan utuh sebagai latar belakang ikonik Sleman.

---

## 3. Rencana Pengayaan Animasi (*Animation Suite*)

### A. Animasi Mobil Dinas Kominfo (*Car Dynamics*)
1. **Suspension Bouncing**: Saat mobil bergerak, bodi mobil dinas sedikit bergetar naik-turun halus (efek suspensi aspal ~1px, frekuensi 8Hz).
2. **Lampu Sorot & Strobo Hidup**:
   - Lampu sorot depan menyinari aspal di depan mobil dengan gradasi kuning lembut.
   - Lampu sirine strobo kecil di atap mobil berkedip oranye/biru secara periodik.
3. **Animated Road Dash Line**:
   - Marka putih jalan putus-putus memiliki efek bergerak halus (*dash offset stream*) saat mobil berjalan, memberi efek kecepatan visual yang nyata.
4. **Smooth Heading & Rotation**:
   - Sudut rotasi mobil dihitung presisi secara tangensial terhadap kurva jalan sehingga moncong mobil selalu menghadap ke depan jalan yang dilalui.

### B. Animasi Checkpoint & Interaktivitas
1. **Pulse Ripple Effect**:
   - Checkpoint yang aktif memancarkan gelombang lingkaran berdenyut (*expanding radar wave*) keluar secara berulang.
2. **GPS Pin Drop Bounce**:
   - Pin GPS melayang anggun di atas mobil dinas dengan bayangan dinamis di aspal.
3. **Card Pop-in Spring Transition**:
   - Pop-up Card Gambar 2 muncul dengan animasi *pop-in spring* lembut (`transform: scale(0.92) -> scale(1)`, `opacity: 0 -> 1`) berdurasi 300ms cubic-bezier.
4. **Rhombus Pin Dynamic Shimmer**:
   - Belah ketupat warna di sebelah nomor pos berdenyut halus saat pos aktif.

### C. Animasi Karakter & Living World (*Living Diorama*)
1. **Pos 1 (Registrasi)**: Tangan mengetik pada laptop, layar laptop menyala bergantian, partikel ikon akun melayang lembut ke atas.
2. **Pos 2 (Administrasi & Seleksi)**: Tangan petugas dinas menstempel dokumen (*rubber stamp action*), berkas diverifikasi dengan centang hijau bercahaya.
3. **Pos 3 (Pelaksanaan Magang)**: Mentor berdiskusi dengan peserta, kepala mengangguk (*nodding*), layar monitor menyala, grafik progres kerja meningkat.
4. **Pos 4 (Kelulusan)**: Peserta wisudawan melompat riang (*jumping celebration*), membawa map sertifikat emas, diiringi hujan konfeti warna-warni berputar melayang.
5. **Living Environment**:
   - Asap kawah tipis Gunung Merapi melayang perlahan ke kanan atas.
   - Awan putih berarak perlahan melintasi langit Sleman.
   - Bendera merah putih di atas kantor Diskominfo berkibar lembut.
   - Lampu tower pemancar komunikasi berkedip hijau-merah.
   - Dedaunan pohon di tepi jalan bergoyang lembut ditiup angin sepoi-sepoi.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/peta-alur-interaktif.blade.php` | Modify | 1. **Relokasi Gunung & Jalan**: Tata ulang SVG agar Merapi berada di latar belakang atas (`y: 80 - 250`), dan jalan aspal berkelok indah di bawah Merapi (`y: 230 - 440`).<br>2. **Vignette Karakter**: Sesuaikan posisi 4 vignette karakter di samping masing-masing pos baru di dataran hijau.<br>3. **Living SVG Elements**: Tambahkan kepulan asap kawah Merapi, bendera berkibar, dan dedaunan pohon bergoyang.<br>4. **Car Dynamics**: Sempurnakan detail mobil dinas Kominfo dengan lampu sorot, suspensi, dan kalkulasi sudut rotasi halus.<br>5. **Pop-up Card**: Berikan transisi spring pop-in yang mulus sesuai Gambar 2. |
| `public/css/peta-alur-fullscreen.css` | Modify | 1. Tambahkan keyframes animasi: `merapiSteamDrift`, `treeSway`, `carRoadBounce`, `pulseRippleWave`, `popupSpringIn`, `dashStream`.<br>2. Atur styling pop-up card agar melayang proporsional di samping pos tanpa menutupi mobil/pos.<br>3. Pastikan kanvas membentang penuh 100% tanpa sela letterbox. |
| `tests/Feature/PetaAlurPageTest.php` | Modify | Sesuaikan assertions test untuk memvalidasi elemen visual baru, jalan aspal di bawah gunung, 4 vignette karakter, dan kelulusan. |

---

## 5. Langkah Implementasi Terstruktur

### Tahap 1: Restrukturisasi SVG Lanskap & Jalan di Bawah Gunung
- [x] Buka [resources/views/public/peta-alur-interaktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php).
- [x] Tata ulang Gunung Merapi di horizon atas: puncak di `(480, 75)`, alas di `y=230`, tanpa tertabrak jalan raya.
- [x] Buat kurva jalan raya aspal baru di dataran rendah: `d="M 60, 240 C 220, 220 540, 230 700, 260 C 820, 280 780, 360 540, 370 C 340, 380 140, 385 240, 410 C 360, 435 600, 440 840, 430"`.
- [x] Pindahkan posisi 4 checkpoint di atas jalan baru:
  - Pos 1: `(180, 232)`
  - Pos 2: `(700, 260)`
  - Pos 3: `(240, 410)`
  - Pos 4: `(740, 433)`
- [x] Sesuaikan posisi 4 vignette karakter dan gedung kantor Diskominfo agar selaras dengan checkpoint baru.
- [x] Posisikan Mobil Dinas awal di Pos 1 `(180, 232)`.

### Tahap 2: Pengayaan Animasi Karakter, Mobil, & Lanskap Hidup
- [x] Di [public/css/peta-alur-fullscreen.css](file:///d:/MAGKOM/MAGKOM/public/css/peta-alur-fullscreen.css), tambahkan keyframe animations:
  - Animasi asap kawah Merapi melayang tipis (`merapiPlume`).
  - Animasi suspensi bodi mobil dinas saat melaju (`carSuspension`).
  - Animasi marka jalan meluncur dinamis (`roadDashStream`).
  - Animasi dedaunan pohon bergoyang lembut (`treeSway`).
  - Animasi ripple ganda checkpoint yang memikat (`cpRippleWave`).
  - Animasi spring pop-in kartu informasi Gambar 2 (`popupSpringIn`).
- [x] Perbarui kalkulasi rotasi tangensial mobil di JavaScript agar mobil meliuk secara alami mengikuti kurva S baru.

### Tahap 3: Penyempurnaan Pop-up Card Gambar 2
- [x] Pastikan posisi pop-up card Gambar 2 melayang elegan di area yang tidak menutupi mobil dinas yang sedang aktif.
- [x] Berikan transisi pergantian kartu yang lembut dan responsif.

### Tahap 4: Pengujian & Validasi
- [x] Uji sintaks JavaScript menggunakan Node.js: pastikan 100% valid tanpa syntax error.
- [x] Jalankan automated test: `php artisan test --filter=PetaAlurPageTest`.
- [x] Jalankan linter Pint: `vendor/bin/pint tests/Feature/PetaAlurPageTest.php --test`.
- [x] Verifikasi respon HTTP: `curl -I -H "Host: magkom.test" http://127.0.0.1/peta-alur`.

---

## 6. Kriteria Selesai (Definition of Done)
- [x] Jalan raya aspal 100% berada di dataran rendah di bawah kaki Gunung Merapi, tidak ada jalan yang melintasi badan atau kawah gunung.
- [x] Gunung Merapi berdiri megah dan anggun di latar belakang horizon atas dengan kepulan asap kawah tipis.
- [x] Mobil dinas melaju mulus menyusuri jalan baru dari Pos 1 ke Pos 2 ke Pos 3 ke Pos 4 dengan suspensi dinamis, sorot lampu LED, dan marka jalan hidup.
- [x] Seluruh 4 karakter animasi diorama (laptop mengetik, stempel admin, mentor berdiskusi, wisudawan melompat & konfeti) aktif dan hidup.
- [x] Pop-up Card Gambar 2 muncul dengan transisi spring yang halus tanpa menutupi mobil atau checkpoint.
- [x] Seluruh automated test lolos 100% hijau.
