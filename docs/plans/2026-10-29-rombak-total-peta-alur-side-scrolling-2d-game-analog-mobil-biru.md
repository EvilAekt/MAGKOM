# Rencana Kerja: Rombak Total Peta Alur — 2D Side-Scrolling Game, Jalan Lurus Horizontal, Kontrol Analog/Joystick, Mobil Biru Kartun, & Lanskap Pegunungan Merapi Kaya Animasi

Dokumen rencana kerja ini merespons permintaan pengguna:
> *"saya mau rombak full jadi jalanya gausah belok2 hanya lurus dari kiri ke kanan nanti mobilnya bisa di gerakin pake analog mobilnya pakai yang tertera di gambar itu km faham kan konsep nya jadi semacam game 2d dan tolong benarkan gunung nya saya jadi konsepnya nanti bisa digerakin pakai analog dan kan ada space banyak bisa di tambahkan aset2 animasi kan seperti orang2 bergerak dll kan buat plan nya"*

Disusun sesuai panduan **Web Planning Workflow** sebelum eksekusi penulisan kode dimulai.

---

## 1. Analisis Kebutuhan & Konsep Visual Baru

Berdasarkan referensi visual yang dilampirkan pengguna:
1. **Gambar 1 (Karakter Mobil)**: Mobil kartun 2D tampak samping (*side-view compact blue car*) dengan bodi biru bulat ramah (*beetle/hatchback style*), kaca depan dan samping transparan dengan pantulan cahaya, roda hitam ber-velg abu-abu yang bisa berputar, lampu depan bulat kuning, lampu rem belakang merah oval, dan bumper abu-abu.
2. **Gambar 2 (Lanskap Game 2D Side-Scrolling)**:
   - **Latar Belakang (*Far Background*)**: Pegunungan megah berlapis (Gunung Merapi Sleman yang realistis dan berseni) dengan bayangan tebing biru keabuan dan puncak bergradasi kabut.
   - **Latar Tengah (*Midground*)**: Hutan pohon pinus/cemara asri, perbukitan hijau bergelombang (*rolling hills*), dan kompleks Gedung Diskominfo Sleman.
   - **Jalur Utama (*Foreground*)**: Jalan aspal lurus mendatar (*straight horizontal 2-way road*) dari kiri ke kanan dengan garis marka putih putus-putus.
   - **Latar Depan (*Very Foreground*)**: Hamparan rumput hijau segar berhias bunga-bunga liar kuning/putih khas pedesaan Sleman.
3. **Mekanisme Kontrol (*2D Game Driver*)**:
   - **Analog Joystick Interaktif**: Panel kendali analog stick di sudut layar kiri bawah (atau di atas kanvas) yang bisa digerakkan dengan sentuhan sentral/mouse drag ke kiri dan kanan untuk mengemudikan mobil maju dan mundur secara dinamis.
   - **Keyboard & Click Controls**: Tombol panah keyboard (`←` dan `→` atau `A` dan `D`) serta klik halte pos untuk melajukan mobil.
4. **Diorama 4 Pos Perjalanan Magang di Sepanjang Jalan Lurus**:
   - Karena jalan lurus horizontal membentang dari kiri ke kanan, ruang atas dan pinggir jalan sangat lega untuk diisi halte stasiun tematik dan animasi NPC (*non-playable characters*):
     - **Pos 1 (Kiri - Registrasi)**: Halte Sleman Barat, meja pendaftaran, mahasiswa mengetik laptop, papan plang "01 REGISTRASI", partikel bubble akun sukses.
     - **Pos 2 (Tengah Kiri - Administrasi & Seleksi)**: Halte Verifikasi, petugas ASN dengan stempel dan tumpukan berkas terverifikasi, papan plang "02 ADMINISTRASI & SELEKSI".
     - **Pos 3 (Tengah Kanan - Pelaksanaan Magang)**: Halte Bimbingan Lapangan, mentor ASN ramah berdiskusi dengan peserta magang, papan plang "03 PELAKSANAAN MAGANG", bubble logbook harian.
     - **Pos 4 (Kanan - Kelulusan & Kantor Diskominfo)**: Gerbang Utama Kantor Diskominfo Sleman megah dengan tiang bendera Merah Putih, wisudawan melompat gembira membawa e-sertifikat emas, hujan konfeti, papan plang "04 KELULUSAN".
   - **Animasi Hidup Tambahan di Kanvas**: Pejalan kaki/ASN berlalu-lalang santai di trotoar, burung terbang melintasi langit Merapi, pepohonan bergoyang sepoi-sepoi, dan asap tipis kawah Merapi.

---

## 2. Evaluasi Opsi Pendekatan Arsitektur

### Opsi A: *Panoramic Horizontal Side-Scrolling Camera World* (Panjang Dunia 2400px dengan Kamera Dinamis Mengikuti Mobil) — **(Direkomendasikan)**
- **Mekanisme**:
  - Kanvas SVG/HTML5 memiliki lebar dunia luas (misal `2400 x 600 px`), sedangkan viewport kamera menampilkan jendela pandang `960 x 540 px`.
  - Kamera bergerak secara dinamis (*smooth parallax camera tracking*) mengikuti posisi mobil saat dikemudikan dengan analog/tombol dari kiri (Pos 1) hingga kanan (Pos 4).
  - Background pegunungan Merapi bergerak dengan kecepatan *parallax* lambat, memberikan ilusi kedalaman 2.5D game yang sangat hidup dan memukau (*WOW effect*).
- **Kelebihan**: Ruang di antara Pos 1, 2, 3, dan 4 menjadi sangat lega, bebas dari kesan sempit/padat. Mobil terasa benar-benar menjelajahi perjalanan nyata dari Sleman Barat ke Kantor Diskominfo Sleman.
- **Kekurangan**: Memerlukan logika kamera smooth damping (`viewBox` interpolation).

### Opsi B: *Fixed Single-Screen Horizontal Stage* (960px Tanpa Kamera Scrolling)
- **Mekanisme**: Seluruh lintasan Pos 1 s/d Pos 4 dimuat dalam 1 layar tetap selebar 960px.
- **Kelebihan**: Implementasi kamera lebih sederhana.
- **Kekurangan**: Jarak antar 4 pos menjadi agak rapat (hanya ~200px per pos) dan ruang untuk animasi gedung Diskominfo serta orang berjalan menjadi terbatas.

> **Keputusan**: Menggunakan **Opsi A (Panoramic Horizontal Side-Scrolling Camera World)** karena memberikan ruang yang sangat lega sesuai permintaan pengguna (*"kan ada space banyak bisa di tambahkan aset2 animasi kan seperti orang2 bergerak dll"*), serta memberikan pengalaman game 2D sejati.

---

## 3. Rincian Teknis Kontrol Analog (*Virtual Joystick*)

1. **Komponen Analog Stick (`#gameAnalogJoy`)**:
   - Terdiri dari **Base Ring** (lingkaran luar semi-transparan berdiameter 80px) dan **Thumb Knob** (lingkaran dalam berdiameter 38px bertekstur grip dengan warna aksen biru Sleman).
   - Mendukung input **Pointer Events** (`pointerdown`, `pointermove`, `pointerup`), berfungsi mulus di mouse desktop maupun layar sentuh mobile/tablet.
   - Menggerakkan thumb ke kanan (`X > 0`) -> mobil melaju maju ke kanan, roda berputar searah jarum jam, suspensi mobil memantul ringan.
   - Menggerakkan thumb ke kiri (`X < 0`) -> mobil melaju mundur ke kiri, roda berputar berlawanan arah jarum jam.
   - Semakin jauh thumb ditarik dari titik tengah, laju mobil semakin cepat (*proportional velocity*).
2. **Keyboard Backup**:
   - Menekan tombol `ArrowRight` atau `D` otomatis menggerakkan knob analog ke kanan.
   - Menekan tombol `ArrowLeft` atau `A` otomatis menggerakkan knob analog ke kiri.
3. **Deteksi Pos & Pop-Up Info**:
   - Saat mobil mencapai area checkpoint (radius toleransi ~50px dari halte), pop-up kartu ringkas Gambar 2 otomatis terbuka menampilkan informasi pos tersebut, bottom tab aktif tersinkronisasi, dan chime audio berbunyi ramah.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/peta-alur-interaktif.blade.php` | Modify | 1. **Scene 2D Side-Scroller Baru**: Rombak struktur SVG menjadi lanskap horizontal lurus (`width: 2400, height: 600`) dengan pegunungan Merapi megah berlapis, perbukitan hijau, pepohonan pinus, dan jalan aspal mendatar di bagian bawah.<br>2. **Desain Mobil Kartun Biru**: Implementasikan aset SVG mobil biru tampak samping sesuai Gambar 1 pengguna (bodi bulat, roda berputar, lampu sorot depan, refleksi kaca).<br>3. **4 Halte Pos Berjajar Rapi**: Letakkan Pos 1 (Registrasi), Pos 2 (Administrasi), Pos 3 (Pelaksanaan), dan Pos 4 (Kelulusan & Gedung Diskominfo Sleman) secara berurutan dari kiri ke kanan di sepanjang jalan.<br>4. **Animasi Orang Bergerak & Aset Hidup**: Tambahkan ASN Sleman berjalan, siswa membawa tas, burung merpati terbang, awan, dan dedaunan bergoyang.<br>5. **Virtual Joystick Analog HUD**: Tambahkan komponen analog joystick interaktif di pojok kiri bawah layar.<br>6. **Game Engine JS**: Logika fisika gerak mobil 2D, kamera parallax horizontal, putaran roda, dan sinkronisasi checkpoint. |
| `public/css/peta-alur-fullscreen.css` | Modify | 1. Styling Virtual Joystick (base ring, thumb knob, active dragging state).<br>2. Animasi putaran roda mobil 2D kartun (`rotateWheelForward` & `rotateWheelBackward`).<br>3. Parallax layering CSS dan animasi orang berjalan (*walking cycle animation*).<br>4. Responsivitas layar sentuh dan tata letak kontrol game. |
| `tests/Feature/PetaAlurPageTest.php` | Modify | Sesuaikan test assertions untuk memvalidasi: kontrol analog joystick, mobil kartun 2D tampak samping, halte pos 1-4 horizontal, gedung Diskominfo, dan pop-up informasi yang tetap lengkap. |

---

## 5. Langkah Implementasi Terstruktur

### Tahap 1: Rekonstruksi Lanskap Pegunungan Merapi & Jalan Aspal Lurus Horizontal
- [ ] Buka [resources/views/public/peta-alur-interaktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php).
- [ ] Ubah kanvas SVG menjadi panorama mendatar lebar (`viewBox="0 0 2400 600"`):
  - Latar langit biru gradasi pagi cerah dengan awan dan matahari hangat.
  - Lapisan pegunungan Merapi megah bergradasi biru-abu berlapis (*layered mountains*) sesuai referensi Gambar 2.
  - Lapisan perbukitan hijau asri dengan jajaran pohon cemara/pinus.
  - Jalan aspal lurus mendatar di `y: 420..490` dengan marka garis putih putus-putus membentang dari `x: 0` hingga `x: 2400`.
  - Tepian rumput depan (*foreground meadow*) dengan bunga liar kuning/putih.

### Tahap 2: Implementasi Aset Mobil Kartun Biru Tampak Samping (Sesuai Gambar 1)
- [ ] Buat grup SVG `#gamePlayerCar` berbentuk mobil kartun biru tampak samping:
  - Bodi bulat biru muda dengan lis biru tua (*compact beetle shape*).
  - Kaca depan dan samping abu-abu muda beraksen pantulan sinar putih.
  - Roda depan dan belakang bulat hitam dengan velg abu-abu (`#carWheelFront`, `#carWheelRear`).
  - Lampu depan kuning cerah yang memancarkan sorotan cahaya LED ke jalan kanan.
  - Lampu belakang merah oval dan bumper abu-abu klasik.
  - Plat/stempel resmi Diskominfo Sleman.

### Tahap 3: Penataan 4 Halte Pos & Aset Orang Bergerak (Animasi Kaya)
- [ ] Tata 4 halte perhentian di sepanjang jalur mendatar:
  - **Pos 1 (x: 250)**: Halte Registrasi Akun Mandiri, meja pendaftaran, mahasiswa mengetik laptop, banner selamat datang.
  - **Pos 2 (x: 800)**: Halte Verifikasi Administrasi, meja verifikasi ASN, tempel cap berkas sukses.
  - **Pos 3 (x: 1450)**: Halte Pelaksanaan Magang, mentor ASN berdiskusi, papan logbook & progres tugas.
  - **Pos 4 (x: 2100)**: Kompleks Gedung Kantor Diskominfo Sleman megah, gapura selamat datang, wisudawan melompat dengan toga dan e-sertifikat emas, bendera merah putih berkibar.
- [ ] Tambahkan animasi orang bergerak (*walking NPC*):
  - Pejalan kaki ASN Sleman dan mahasiswa magang berjalan santai di trotoar.
  - Burung-burung terbang melintasi langit di atas pegunungan.
  - Efek dedaunan bergoyang dan asap kawah tipis di puncak Merapi.

### Tahap 4: Implementasi Virtual Joystick Analog & Game Loop Interaktif
- [ ] Buat markup HUD kontrol analog stick `#gameAnalogJoy` di sudut kiri bawah layar:
  - Lingkaran analog luar (`.analog-base`) dan tombol tuas tengah (`.analog-thumb`).
- [ ] Tulis logika JavaScript Game Engine:
  - Deteksi sentuhan pointer/mouse pada analog thumb dengan kalkulasi vektor offset X (-1.0 s/d +1.0).
  - Game loop berbasis `requestAnimationFrame`:
    - Menggerakkan posisi X mobil sesuai kecepatan analog/keyboard.
    - Memutar roda mobil ke depan saat melaju ke kanan, dan berputar mundur saat mundur ke kiri.
    - Kamera viewport (`viewBox`) otomatis meluncur mengikuti posisi mobil (*smooth tracking camera*) dengan clamping batas kiri (`x: 0`) dan kanan (`x: 2400`).
  - Integrasikan navigasi keyboard (`ArrowRight`/`D` dan `ArrowLeft`/`A`).
  - Sinkronisasi dengan 4 pos: saat mobil mendekati salah satu dari 4 pos, aktifkan tab pos, bunyikan chime ramah, dan buka kartu pop-up info ringkas pos Gambar 2.

### Tahap 5: Styling & Polish di [peta-alur-fullscreen.css](file:///d:/MAGKOM/MAGKOM/public/css/peta-alur-fullscreen.css)
- [ ] Tambahkan styling untuk analog joystick HUD dengan efek glassmorphism yang responsif di desktop dan mobile.
- [ ] Tambahkan keyframe animations untuk orang berjalan kaki (*walking stride*), putaran roda mobil, dan riak checkpoint.

### Tahap 6: Automated Testing & Verifikasi Kualitas
- [ ] Perbarui pengujian di [tests/Feature/PetaAlurPageTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/PetaAlurPageTest.php) untuk memverifikasi seluruh komponen baru (analog joystick, mobil biru tampak samping, 4 halte mendatar, gedung Diskominfo).
- [ ] Jalankan test suite:
  - `& "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test --filter=PetaAlurPageTest`
  - `& "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test --filter=LandingPagePetaAlurModalTest`
  - `& "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test --filter=AlurMagangPageTest`
- [ ] Jalankan linter Pint: `vendor/bin/pint tests/Feature/PetaAlurPageTest.php --test`.

---

## 6. Kriteria Selesai (Definition of Done)

- [ ] Jalan raya berubah menjadi jalan aspal lurus horizontal dari kiri ke kanan (tanpa kurva berkelok-kelok).
- [ ] Latar belakang menampilkan pegunungan Merapi megah dan artistik sesuai referensi Gambar 2.
- [ ] Mobil pemain menggunakan model mobil kartun biru tampak samping (*side-view blue beetle*) sesuai persis dengan referensi Gambar 1.
- [ ] Pengguna dapat mengendalikan mobil secara bebas menggunakan **Virtual Joystick Analog** di layar, serta tombol panah keyboard.
- [ ] Roda mobil berputar secara nyata sesuai arah pergerakan mobil (maju / mundur).
- [ ] Kamera viewport bergerak mulus mengikuti laju mobil menyusuri dunia 2D dari kiri (Pos 1) hingga kanan (Pos 4).
- [ ] Terdapat aset animasi orang-orang bergerak/berjalan di sepanjang trotoar dan halte.
- [ ] Pop-up ringkas informasi tahapan tetap muncul otomatis saat mobil sampai di tiap pos.
- [ ] Seluruh automated test lolos 100% hijau.
