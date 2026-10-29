# Rencana Kerja: Revamp Pop-Up Speech Bubble Webtoon, Tata Letak Mobil Bersih, Ikonografi Checkpoint Estetik, dan Background Music BGM

**Tanggal**: 2026-10-29  
**Status**: Usulan Rencana (Menunggu Persetujuan Pengguna)  
**Dokumen Terkait**: [resources/views/public/peta-alur-interaktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php), [public/css/peta-alur-fullscreen.css](file:///d:/MAGKOM/MAGKOM/public/css/peta-alur-fullscreen.css)

---

## 1. Tujuan
Memperbaiki dan merombak 6 aspek kunci pada halaman Peta Alur Interaktif (`/peta-alur`) sesuai masukan pengguna:
1. **Footer Disesuaikan dengan Landing Page Utama**: Menghapus bilah tab navigasi bawah yang tebal (`peta-alur-bottom-bar`) yang selama ini memotong layar dan menutupi badan mobil. Footer diselaraskan dengan gaya footer bersih landing page utama atau kanvas dijadikan bersih lega.
2. **Pop-Up Informasi Checkpoint Bergaya Komik / Webtoon (In-Scene Speech Bubble)**: Menggantikan kartu pop-up kaku di pojok kanan atas (`#missionHudOverlay`) dengan **Speech Bubble Komik Webtoon** interaktif yang muncul dinamis tepat di atas halte checkpoint/karakter yang sedang dikunjungi mobil.
3. **Penyelarasan Tata Letak Lintasan & Mobil Utuh**: Menyesuaikan koordinat vertikal lintasan aspal dan mobil (menaikkan posisi jalan ke area pandang optimal dan mengatur aspect ratio SVG) sehingga seluruh bodi mobil kartun biru, roda berputar, dan sorotan lampu LED terlihat utuh tanpa terpotong. Menghilangkan pin GPS oranye raksasa dan badge teks canggung yang menutupi atap mobil.
4. **Membersihkan Anomali Bola-Bola Biru**: Menghilangkan efek halo berdenyut raksasa (`cp-pulse-halo` / `cp-ripple`) yang selama ini bocor dan membengkak menjadi bola biru transparan mengambang di atas bukit.
5. **Merapikan Ikonografi Checkpoint**: Mengganti tumpukan badge angka dan elemen karakter yang saling berhimpitan menjadi halte pos yang bersih, jelas, dan memiliki ilustrasi ikon tematik yang bermakna (Pos 1: Form Registrasi Akun, Pos 2: Dokumen Verifikasi Berkas, Pos 3: Laptop Penugasan & Mentor, Pos 4: Toga Kelulusan & Sertifikat Resmi).
6. **Background Music (BGM) Ceria & Ramah**: Mengaktifkan instrumen musik latar (BGM loop santai bertema petualangan ceria instansi/edukatif) menggunakan Web Audio API Synthesizer (chime, marimba, dan bassline lembut yang ramah) saat tombol suara dinyalakan (*Suara: On*).

---

## 2. Asumsi & Evaluasi Pendekatan

### Evaluasi Opsi 1: Menampilkan Pop-Up Webtoon di Canvas vs Overlay HTML Dinamis
- **Opsi A (HTML Floating Speech Bubble Mengikuti Koordinat Dunia)**: Menggunakan elemen HTML dengan posisi absolut yang dikonversi dari koordinat dunia SVG ($X: 280, 880, 1480, 2080$) ke pixel layar.
  - *Kelebihan*: Render teks sangat tajam, tombol interaktif lebih mudah diakses keyboard/screen-reader.
  - *Kekurangan*: Membutuhkan perhitungan matrix CTM terus-menerus saat kamera digeser.
- **Opsi B (Native SVG Speech Bubble Group di Dalam Layer Checkpoint - Rekomendasi)**:
  - Speech bubble digambar langsung di dalam grup checkpoint SVG pada koordinat masing-masing halte ($y: 200..320$ di atas karakter/tiang pos).
  - Menggunakan bentuk balon percakapan komik elegan berlatar putih dengan ekor runcing mengarah ke bawah (ke arah halte/karakter), nomor tahap tegas berwarna, judul tahapan, deskripsi ringkas, dan tombol aksi berikutnya.
  - Balon ini dianimasikan dengan efek pop-in khas komik webtoon (*scale spring pop*).
  - *Alasan Pemilihan*: 100% tersinkronisasi otomatis dengan pergerakan kamera geser 2D side-scrolling tanpa jeda lag koordinat atau glitch transformasi layar.

### Evaluasi Opsi 2: Implementasi Background Music (BGM)
- **Opsi A (Audio File MP3)**: Membutuhkan file eksternal `.mp3` di folder `public/audio/`. Jika file tidak ada atau gagal dimuat, audio tidak bersuara.
- **Opsi B (Web Audio API Algorithmic Melody Synthesizer - Rekomendasi)**:
  - Menulis BGM loop ramah berbasis Web Audio API (melodi akord mayor santai C - G - Am - F berulang lembut dengan tangga nada marimba/lofi).
  - Nol ketergantungan file eksternal, ukuran 0 KB, langsung berbunyi ketika pengguna mengklik tombol "Suara: On", dan berhenti dengan fade-out saat dimatikan.

---

## 3. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/peta-alur-interaktif.blade.php` | Modify | Hapus bottom bar tebal, buat Pop-Up Balon Komik Webtoon di atas checkpoint, rapikan layout aspal & mobil, hapus pin oranye raksasa di atap mobil, integrasikan Web Audio BGM Synthesizer, dan sesuaikan footer. |
| `public/css/peta-alur-fullscreen.css` | Modify | Styling Speech Bubble Webtoon (animasi balon pop-in komik, ekor segitiga, tipografi tajam), hapus rule bottom bar yang menutupi kanvas, hapus animasi halo bola biru. |
| `tests/Feature/PetaAlurPageTest.php` | Modify | Perbarui assertion test untuk speech bubble webtoon, ketiadaan anomali bola biru, dan verifikasi status audio BGM. |

---

## 4. Rincian Langkah Implementasi

### Tahap 1: Merapikan Viewport, Aspal, dan Menghilangkan Tab Bar Tebal Bawah
- [ ] Hapus `<nav class="peta-alur-bottom-bar">` yang selama ini menempel di bawah kanvas dan menutupi bodi mobil.
- [ ] Sesuaikan footer halaman agar rapi dan tidak mengganggu area pandang permainan (halaman menjadi kanvas simulator lega dengan topbar di atas dan footer bersih jika halaman di-scroll atau fixed-fit).
- [ ] Atur koordinat Y lintasan aspal: dari $y = 440..508$ dinaikkan sedikit ke $y = 390..458$ sehingga mobil di $y = 415$ berada di tengah bidang pandang bawah yang lapang, bebas dari potongan browser.

### Tahap 2: Menghapus Anomali Bola Biru & Pin GPS Raksasa
- [ ] Hapus elemen `<circle class="cp-pulse-halo cp-ripple-1">` dan `<circle class="cp-pulse-halo cp-ripple-2">` di seluruh 4 halte checkpoint yang memicu bola biru raksasa mengambang di bukit.
- [ ] Hapus pin GPS oranye raksasa (`#modalRoadGpsPin`) dan badge tooltip teks canggung (`#modalRoadCarTag`) yang selama ini menumpuk di atap mobil.
- [ ] Mobil kini tampil bersih, elegan, dan fokus pada detail mobil kartun biru Sleman.

### Tahap 3: Merancang Balon Pop-Up Speech Bubble Webtoon di Atas Tiap Pos
- [ ] Di setiap Halte Checkpoint (Pos 1 s/d Pos 4), tambahkan elemen SVG `<g class="webtoon-speech-bubble" id="webtoonBubble1..4">`:
  - Bentuk balon percakapan komik elegan: `<path>` rounded bubble dengan ekor lancip komik di bagian bawah tengah yang mengarah tepat ke karakter/tiang halte pos.
  - Header balon: Nomor tahap berbentuk badge lingkaran kecil (misal "01", "02") dan Judul Pos bold (misal "REGISTRASI").
  - Body balon: Teks deskripsi ringkas tahapan alur magang yang mudah dibaca dengan kontras tinggi.
  - Tombol aksi: Tombol "Lanjut ke Pos Berikutnya ➔" di dalam balon yang bisa diklik langsung.
- [ ] Hapus kartu HUD statis lama di pojok kanan atas (`#missionHudOverlay`).
- [ ] Di JavaScript: Saat mobil berhenti/mendekati suatu pos, balon Webtoon pada pos tersebut muncul dengan animasi pop-spring (`transform: scale(0) -> scale(1)`), sedangkan balon pos lain ditutup secara halus.

### Tahap 4: Merapikan Ikonografi dan Vignette Checkpoint
- [ ] Tata ulang elemen di Pos 1, 2, 3, dan 4 agar memiliki jarak proporsional dan tidak saling tumpuk:
  - Pos 1: Tiang plang halte "Pos 1" dengan ikon formulir registrasi, di sampingnya meja pendaftaran laptop mandiri.
  - Pos 2: Tiang plang halte "Pos 2" dengan ikon dokumen verifikasi & stempel ASN.
  - Pos 3: Tiang plang halte "Pos 3" dengan ikon bimbingan mentor magang.
  - Pos 4: Tiang plang halte "Pos 4" dengan gedung Diskominfo Sleman dan ikon sertifikat kelulusan.

### Tahap 5: Implementasi Musik Latar Belakang (BGM Audio Synthesizer)
- [ ] Pada objek `MagkomSound`, implementasikan fungsi `startAmbient()` dan `stopAmbient()`:
  - Menggunakan Web Audio API oscillator melodi santai (tangga nada C - G - Am - F lembut bertempo 96 BPM) yang berulang (*looping*).
  - Dilengkapi *gain node* volume lembut (0.08) agar nyaman didengar tanpa mengagetkan pengguna.
- [ ] Hubungkan dengan tombol `#btnModalAudioToggle`:
  - Saat diklik menjadi *Suara: On*, musik BGM langsung mulai berputar.
  - Saat dimatikan, musik berhenti secara halus (*soft fade-out*).

### Tahap 6: Pengujian & Validasi
- [ ] Jalankan automated testing: `php artisan test --filter=PetaAlurPageTest`.
- [ ] Pastikan seluruh 62+ assertion lulus 100%.
- [ ] Jalankan Laravel Pint untuk memastikan standar kode PHP rapi.

---

## 5. Kriteria Selesai (Definition of Done)
- [ ] Bilah tab navigasi tebal bawah tidak lagi menutupi mobil atau kanvas.
- [ ] Mobil kartun biru tampak utuh dari roda hingga atap di atas aspal tanpa terhalang teks/pin canggung.
- [ ] Anomali bola biru transparan mengambang di atas bukit sudah hilang bersih.
- [ ] Pop-up info di pojok kanan atas digantikan oleh Speech Bubble Webtoon yang muncul tepat di atas halte pos yang aktif.
- [ ] Checkpoint dan karakter tertata rapi tanpa elemen yang saling menumpuk.
- [ ] Musik BGM santai berbunyi saat tombol suara diaktifkan.
- [ ] Tes PHPUnit lulus 100%.
