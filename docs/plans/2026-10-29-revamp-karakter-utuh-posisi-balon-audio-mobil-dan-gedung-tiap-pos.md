# Rencana Kerja: Revamp Karakter Animasi Utuh (Full-Body), Reposisi Balon Webtoon di Atas Halte, Penguatan Suara Mesin Mobil, Hamparan Rumput Hijau Segar, & Gedung Tematik di Setiap Pos

Dokumen rencana kerja ini merespons umpan balik dan permintaan pengguna:
> *"udah bagus sekarang saya mau karakter di setiap chek poin di ubah jadi kaya gambar animasi manusia utuh jangan kepotong2 gitu atau setengah2, kemudian deskripsi di setiap chek poin menutupi chekpoin nya apa begitu kurang di atas in atau bagaimana biar kelihatan, kemudian suara mobil di perbagus sedikit sedikit agak keras gapapa kemudian rumput hijau nya di ubah jadi agak kelihatan dong dan gedung2 di setiap chek poin tambahkan juga yaa biar keren buat plan nya"*

Disusun sesuai panduan **Web Planning Workflow** sebelum eksekusi penulisan kode dimulai.

---

## 1. Analisis Kebutuhan & Permasalahan Saat Ini

Berdasarkan tangkapan layar dan pemeriksaan kode pada `resources/views/public/peta-alur-interaktif.blade.php`:

| Komponen | Kondisi Saat Ini | Permintaan Pengguna & Solusi Teknis |
| :--- | :--- | :--- |
| **1. Karakter di Setiap Checkpoint** | Karakter hanya tampak setengah badan atas (`charScene1..4`) di balik meja/laptop, sehingga terkesan terpotong kaku dan kurang hidup. | **Rombak Menjadi Karakter Manusia Utuh (*Full-Body Animated Characters*)**: Buat ilustrasi vektor utuh (kepala, ekspresi wajah, pakaian seragam/kasual, tangan beraktivitas, celana/rok, sepatu) berdiri kokoh di atas trotoar:<br>• **Pos 1 (Registrasi)**: Mahasiswa muda berdiri utuh membawa map formulir & ransel, melambaikan tangan menyapa.<br>• **Pos 2 (Administrasi & Seleksi)**: Petugas ASN Sleman berseragam khaki/batik dinas berdiri utuh memegang papan klip (*clipboard*) berkas lolos seleksi.<br>• **Pos 3 (Pelaksanaan Magang)**: Mentor ASN dan peserta magang berdiri berdampingan sedang berdiskusi membimbing proyek laptop/tablet.<br>• **Pos 4 (Kelulusan)**: Wisudawati/wisudawan berdiri utuh memakai jubah & topi toga wisuda, mengangkat gulungan sertifikat emas gembira. |
| **2. Balon Webtoon Menutupi Checkpoint** | Kotak dialog komik Webtoon berada di koordinat yang terlalu rendah (`translate(0, -95)`), sehingga bagian bawah kotak menutupi lingkaran plang halte (misal "01 REGISTRASI"), ikon pos, dan mobil. | **Reposisi Balon Webtoon Naik ke Atas (*Elevated Position*)**: Naikkan posisi kotak balon ke `translate(40, -170)` dengan ekor diagonal yang menjulur anggun ke bawah menunjuk tepat ke halte plang. Dengan demikian plang halte, ikon stempel, karakter utuh, dan mobil terlihat bebas 100% tanpa tertutup sama sekali. |
| **3. Suara Mesin Mobil Terlalu Pelan** | `startEngineHum` menggunakan gain sangat rendah (`0.022`) dengan 1 oscillator mono, sehingga nyaris tidak terdengar di laptop/ponsel. | **Perbagus Sintesis Audio Mesin Mobil (*Dual-Tone Warm Engine Rumble*)**: Tingkatkan gain ke level yang lebih renyah (`0.060`), gunakan kombinasi dual oscillator (rumble 68Hz sawtooth + harmonic purr 136Hz) dengan lowpass filter 240Hz, sehingga suara mobil terdengar mantap, halus, jelas, dan menyenangkan tanpa memekakkan telinga. |
| **4. Rumput Hijau Kurang Kelihatan** | Hamparan rumput bawah hanya berupa garis hijau polos tipis di perbatasan footer, kurang kontras dan kurang menyegarkan. | **Revamp Hamparan Rumput Hijau Depan (*Vibrant Meadow & Grass Tufts*)**: Lebarkan dan naikkan permukaan rumput (`y: 445 s/d 540`), buat dua lapisan gradasi hijau segar (`#22c55e` dan `#15803d`), tambahkan rumpun rumput daun (*grass tufts*) dan taburan bunga-bunga liar cerah warna-warni (kuning matahari, putih melati, merah muda). |
| **5. Gedung Tematik di Setiap Pos Checkpoint** | Gedung saat ini hanya ada di ujung kiri (kampus) dan ujung kanan (kantor Diskominfo). Di antara Pos 1, 2, dan 3 hanya berupa bukit/pohon, sehingga latar belakang pos terasa agak kosong. | **Tambahkan Gedung Tematik Bersejarah & Modern di Setiap Pos**: Di belakang trotoar tiap pos tambahkan gedung representatif:<br>• **Pos 1**: Gedung Pusat Pendaftaran Akademik Sleman (*Registration Center*) 2 lantai bernuansa kanopi merah-putih.<br>• **Pos 2**: Gedung Verifikasi Berkas & PTSP Diskominfo Sleman bergaya minimalis biru Sleman.<br>• **Pos 3**: Gedung Sleman Cyber Smart Hub & Innovation Lab bertingkat kaca modern dengan panel surya dan antena pemancar.<br>• **Pos 4**: Gedung Auditorium Utama Diskominfo Sleman berhias ornamen pita & spanduk kelulusan. |

---

## 2. Asumsi Teknis & Parameter Desain

1. **Koordinat dan Layering SVG**:
   - Lapisan Gedung Tematik diletakkan di **Layer 4** (di belakang trotoar `y: 315` dan di depan bukit hijau `y: 380`), sehingga tidak menghalangi pejalan kaki maupun mobil di jalan.
   - Posisi karakter utuh berdiri tegak di trotoar pada `y = 310 s/d 375` (kaki menapak di atas ubin trotoar).
   - Dimensi Webtoon Bubble disesuaikan: lebar 300px, tinggi 115px, dinaikkan ke atas dengan margin aman minimal 35px di atas plang bundar halte.
2. **Audio Synthesizer Engine**:
   - Tetap menggunakan Web Audio API murni (tanpa file mp3 eksternal) agar loading 0 milidetik dan bebas hambatan koneksi.
   - Volume mesin dinaikkan secara proporsional saat tombol maju/mundur ditekan, dan fade out lembut saat berhenti.

---

## 3. Evaluasi Opsi Arsitektur

### Evaluasi Penempatan Balon Webtoon & Halte:
- **Opsi A: Balon Melayang Tinggi di Atas Halte dengan Ekor Panjang Melengkung (Direkomendasikan)**:
  - Kotak balon dinaikkan 75px ke atas langit biru, ekor balon menunjuk diagonal dari dasar balon langsung ke kepala plang halte.
  - *Kelebihan*: Plang halte, karakter manusia utuh, mobil dinas biru, dan marka aspal 100% tampak utuh dan estetik tanpa tumpang tindih. Persis seperti komik strip Webtoon profesional.
- **Opsi B: Balon Digeser Jauh ke Samping Kanan**:
  - *Kelemahan*: Pada layar monitor tertentu, balon bisa menabrak pos berikutnya (misal balon Pos 1 menabrak Pos 2).

> **Keputusan**: Menggunakan **Opsi A (Balon Melayang Tinggi di Atas Halte)**.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/peta-alur-interaktif.blade.php` | Modify | 1. **Karakter Utuh (Full Body)**: Rombak `charScene1`, `charScene2`, `charScene3`, `charScene4` menjadi ilustrasi manusia berdiri utuh yang proporsional dan ekspresif.<br>2. **Gedung Tematik Tiap Pos**: Bangun 4 blok gedung tematik di belakang Pos 1, 2, 3, dan 4.<br>3. **Reposisi Balon Webtoon**: Naikkan posisi balon webtoon di atas halte plang dan panjangkan ekor lancipnya ke bawah.<br>4. **Hamparan Rumput Segar**: Pertebal dan percantik rumput depan dengan gradasi 2 warna, semak daun, dan bunga-bunga mekar.<br>5. **Audio Engine Rumbler**: Tingkatkan gain dan harmonik suara mobil di `startEngineHum`. |
| `public/css/peta-alur-fullscreen.css` | Modify | 1. Tambahkan animasi pernapasan halus / gestur karakter manusia utuh (`@keyframes charBreathing`, `@keyframes charWave`).<br>2. Sesuaikan transisi balon Webtoon yang dinaikkan.<br>3. Tambahkan styling rumput dan semak bunga depan. |
| `tests/Feature/PetaAlurPageTest.php` | Modify | Tambahkan assertions untuk memverifikasi keberadaan gedung-gedung tematik di tiap pos, karakter utuh, dan elemen visual rumput baru. |

---

## 5. Langkah Implementasi Terstruktur

### Tahap 1: Reposisi Balon Webtoon ke Atas Halte Checkpoint
- [x] Buka [resources/views/public/peta-alur-interaktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php).
- [x] Ubah koordinat `transform` dan `path` ekor pada `#webtoonBubble1`, `#webtoonBubble2`, `#webtoonBubble3`, `#webtoonBubble4`:
  - Naikkan posisi balon via CSS transform `translate(25px, -115px) scale(1)` (dan `translate(0px, -115px)` di Pos 4).
  - Sesuaikan path ekor balon agar memanjang ke bawah mengarah tepat ke puncak plang halte bundar (`cx: 0, cy: -12`).
  - Pastikan plang teks "01 REGISTRASI", "02 ADMINISTRASI", "03 PELAKSANAAN", "04 KELULUSAN" terlihat utuh dan tidak terhalang.

### Tahap 2: Rancang Karakter Manusia Animasi Utuh (*Full Body*)
- [x] Rombak `#modalCharacterScene`:
  - **Pos 1 (Registrasi)**: Mahasiswa berkaos kasual rapi dan celana panjang berdiri utuh di trotoar, tersenyum menyapa, memegang map formulir pendaftaran.
  - **Pos 2 (Administrasi)**: Petugas ASN Sleman wanita/pria berseragam dinas rapi (baju dinas khaki/biru dinas, lencana Korpri, celana panjang, sepatu pantofel) berdiri utuh membawa papan klip checklist verifikasi.
  - **Pos 3 (Pelaksanaan)**: Mentor ASN pria dan peserta magang berdiri berdampingan di trotoar sedang aktif berdiskusi membahas tugas proyek.
  - **Pos 4 (Kelulusan)**: Wisudawan berdiri utuh memakai jubah toga hitam bersampir kuning emas Sleman, topi toga wisuda bertali, dan memegang tabung sertifikat kelulusan.

### Tahap 3: Bangun Gedung Tematik di Setiap Pos Checkpoint
- [x] Di Layer 4 (di belakang trotoar `y: 200..315`):
  - **Gedung Pos 1 (`x: 230..360`)**: Gedung Pusat Pendaftaran Calon Magang (*Registration Hall*) berlantai dua dengan kanopi merah-putih, pintu kaca, dan plang informasi.
  - **Gedung Pos 2 (`x: 820..960`)**: Gedung Layanan Seleksi & Verifikasi Berkas Diskominfo dengan fasad biru Sleman modern dan jendela kaca berjejer.
  - **Gedung Pos 3 (`x: 1420..1560`)**: Gedung Innovation & Cyber Command Center Sleman bertingkat dengan aksen panel kaca toska, antena mini, dan pencahayaan modern.
  - **Gedung Pos 4 (`x: 1960..2400`)**: Pertahankan dan rapikan kompleks Gedung Utama Diskominfo Sleman dan Plaza Pemkab yang sudah megah.

### Tahap 4: Perkaya Hamparan Rumput Hijau Segar Depan
- [x] Naikkan batas rumput depan dari `y: 454` ke `y: 438 s/d 540`.
- [x] Tambahkan lapisan kurva rumput hijau terang bergelombang (`fill="#22c55e"`) di atas lapisan rumput hijau tua (`fill="#15803d"`).
- [x] Tambahkan kelompok semak rumput (*grass tufts*) bergerigi halus dan perbanyak bunga liar aneka warna (kuning, putih, ungu, oranye) di sepanjang tepi aspal depan.

### Tahap 5: Peningkatan Kualitas & Volume Suara Mesin Mobil
- [x] Di [resources/views/public/peta-alur-interaktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php) pada fungsi `MagkomSound.startEngineHum()`:
  - Naikkan target gain dari `0.022` ke `0.062`.
  - Tambahkan oscillator kedua (harmonis 136Hz) untuk menghasilkan getaran mesin (*warm engine purr*) yang lebih realistis dan berbobot saat mobil melaju.

### Tahap 6: Efek Klakson Mobil (*Car Horn*) Saat Klik Atas / Panah Atas
- [x] Tambahkan synthesizer audio klakson mobil `MagkomSound.playCarHorn()` (dual-frequency automotive horn 349Hz F4 + 440Hz A4).
- [x] Tambahkan tombol kontrol klakson/atas pada kontrol kemudi (`btnSteerUp` / `btnSteerHorn`) dengan ikon klakson/panah atas.
- [x] Tangani event keyboard: `ArrowUp`, `KeyW`, dan klik pada tombol atas untuk membunyikan klakson, disertai animasi visual kilatan lampu/gelombang suara dari mobil.

### Tahap 7: Verifikasi & Audit Tampilan
- [x] Sesuaikan dan jalankan pengujian PHPUnit:
  `& "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test --filter=PetaAlurPageTest`
- [x] Jalankan skrip evaluasi browser Node.js headless untuk mengambil screenshot di Pos 1, 2, 3, dan 4 guna memastikan karakter utuh, balon webtoon melayang bebas di atas plang, gedung tiap pos berdiri megah, klakson berfungsi, dan rumput hijau terlihat tebal segar.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Seluruh karakter di 4 pos tampil utuh dari kepala hingga kaki (*full body*), tidak ada lagi karakter yang terpotong setengah badan di balik meja.
- [x] Balon komik Webtoon melayang tinggi di atas halte sehingga plang nomor dan nama pos tidak lagi tertutupi.
- [x] Setiap pos (Pos 1 s/d Pos 4) memiliki gedung tematik yang relevan dan megah di latar belakangnya.
- [x] Hamparan rumput hijau depan terlihat jelas, tebal, bertekstur, dan dihiasi bunga-bunga segar.
- [x] Efek suara mobil terdengar lebih mantap, renyah, dan berkarakter saat dijalankan.
- [x] Efek klakson mobil berbunyi merdu saat tombol atas / panah atas diklik.
- [x] Seluruh pengujian otomatis di `PetaAlurPageTest.php` lulus 100%.
