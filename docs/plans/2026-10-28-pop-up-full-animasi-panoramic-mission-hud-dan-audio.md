# Rencana Kerja: Pop-Up Full Animasi Panorama, Integrasi Checklist & Informasi ke Dalam Animasi (HUD Quests), Dynamic Camera Follow-Zoom, dan Audio Ambience

Dokumen rencana kerja ini merespons masukan langsung pengguna untuk mentransformasi pop-up peta alur dari tampilan terpisah (*split layout* dengan kolom putih di kanan yang kaku dan mengganggu) menjadi **Satu Kesatuan Tampilan Full Animasi Penuh (*100% Panoramic Interactive Living World*)**. Seluruh informasi tahapan, dokumen persyaratan, dan checklist kesiapan dimasukkan langsung ke dalam antarmuka animasi secara interaktif (*Game-like Mission HUD*), dilengkapi pergerakan kamera sinematik dinamis (*Dynamic Auto-Zoom & Follow Car*), serta sistem suara/musik (*Web Audio Synthesizer & Sound FX*).

---

## 1. Tujuan
1. **Menghilangkan Panel Kolom Kanan Kaku**:
   - Mengubah struktur modal dari split 2-kolom (kanvas sempit + kartu putih statis) menjadi **Layar Penuh Animasi Panorama (Full Viewport Landscape)** beresolusi tajam dan proporsional.
   - Mengeliminasi kesan sempit yang menyebabkan mobil atau karakter terpotong atau bertabrakan dengan label tahapan.
2. **Mengintegrasikan Informasi & Checklist ke Dalam Dunia Animasi (*Floating Mission HUD*)**:
   - Informasi tahapan, estimasi durasi, dan ketentuan dinas disajikan sebagai **Kartu Misi Interaktif (*Mission Briefing Card*)** melayang berbasis glassmorphism di dalam kanvas animasi yang dapat di-toggle/expand/minimize dengan transisi mulus.
   - Checklist persyaratan dapat dicentang langsung di dalam animasi dengan feedback visual yang seru (partikel centang menyala, progres bar energi/kesiapan bertambah, efek *checkpoint unlocked*).
3. **Kamera Dinamis Sinematik (*Dynamic Camera Zoom & Follow*)**:
   - Saat mobil melaju dari satu tahap ke tahap lain, kanvas viewBox SVG secara dinamis melakukan **Smooth Zoom-In & Auto-Pan** mengikuti koordinat mobil yang berbelok.
   - Saat mobil tiba di checkpoint, kamera melakukan *ease-out reframing* terpusat pada aksi karakter bergerak di pos tersebut.
4. **Sistem Efek Suara & Musik Ambient (*Web Audio Sound FX & Ambience*)**:
   - Menambahkan tombol kontrol audio elegan (*Mute / Unmute / Sound FX*) di pojok atas canvas.
   - Membangun sound synthesizer cerdas berbasis **Web Audio API bawaan browser (zero-dependency)**:
     - Suara *whoosh/engine* saat mobil meluncur.
     - Suara *sparkle/chime ding* merdu saat mencentang checklist kesiapan.
     - Suara *success fanfare* saat mencapai tahap kelulusan dan menerbitkan e-sertifikat.
     - Musik latar ambient futuristik lembut yang menenangkan dan dapat di-mute sewaktu-waktu.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi:
- **Asumsi 1**: Seluruh audio dibangun menggunakan **Web Audio API Oscillator & Gain Nodes bawaan browser**, sehingga tidak memerlukan unduhan file `.mp3` besar yang membebani bandwidth dan bebas masalah lisensi/CORS. Pengguna juga dapat menambahkan file audio kustom di masa depan jika diinginkan.
- **Asumsi 2**: Kontrol preferensi audio tersimpan dalam `localStorage` (default: audio dalam kondisi siap/muted hingga pengguna menekan tombol *Play/Sound*, mematuhi kebijakan autoplay modern browser seperti Chrome/Safari).
- **Asumsi 3**: Tombol checklist dan navigasi tetap memiliki tag aksesibilitas ARIA yang lengkap dan dapat dioperasikan via keyboard (Space/Enter).

---

## 3. Pendekatan Arsitektur & Perbandingan

| Kriteria | Opsi A: Full Panoramic Canvas + Glassmorphism Mission HUD (Rekomendasi) | Opsi B: Tabbed Modal Split View (Status Saat Ini) | Opsi C: Canvas WebGL 3D Game Engine |
| :--- | :--- | :--- | :--- |
| **Pengalaman Pengguna (*Immersion*)** | **Luar biasa (*100% WOW Factor*)**; seluruh layar terasa seperti simulator game dunia magang interaktif tanpa gangguan panel putih. | Kaku; panel putih di kanan memakan 55% ruang layar dan membuat kanvas kiri sempit terpotong. | Sangat rumit, berisiko lag pada perangkat tanpa GPU diskrit. |
| **Integrasi Checklist & Info** | Berada di dalam antarmuka animasi sebagai *Mission HUD* semi-transparan dengan micro-animation interaktif. | Terpisah di luar kanvas dalam kartu formulir biasa. | Butuh rendering teks bitmap/font texture 3D yang berat. |
| **Fitur Kamera (Zoom & Follow)** | **Sangat mulus** via dynamic SVG `viewBox` interpolation (requestAnimationFrame). | Terbatas pada tombol zoom statis (+ / -) yang kaku. | Membutuhkan perputaran matriks kamera 3D. |
| **Kinerja & Kecepatan Muat** | **Sangat Cepat (<30 KB total)**, nol file audio besar (Web Audio API synthetizer murni). | Cepat, namun tampilan visual terpotong. | Sangat lambat (+1 MB library). |
| **Rekomendasi** | **Dipilih Opsi A**. Memenuhi 100% keinginan pengguna, membuat modal benar-benar bernuansa *full animasi*, sinematik, dan modern. | Ditinggalkan sesuai komplain pengguna. | Tidak dipilih. |

---

## 4. Desain Antarmuka: Panorama Living World & Floating Mission HUD

```
+---------------------------------------------------------------------------------------------------------+
| [TOP BAR HUD: SLEMAN DISKOMINFO MAGANG SIMULATOR]                              [SOUND: ON/OFF] [TUTUP X]|
| Status Filter: [Semua Jenjang / SMK / Mahasiswa]  |  [Mandiri / Kelompok]  |  [Target: Juli 2026]       |
+---------------------------------------------------------------------------------------------------------+
|                                                                                                         |
|  [VIEWPORT KANVAS PANORAMA FULL ANIMASI (16:9 / FULL DIORAMA)]                                          |
|                                                                                                         |
|       (Gunung Merapi Megah & Awan Melayang)                                (Menara Diskominfo)          |
|                                                                                                         |
|        [Checkpoint 1] ===== [Checkpoint 2]                                                              |
|              \                     \                                                                    |
|               \                     ===== [Checkpoint 3] ===== [Checkpoint 4: Finish]                   |
|                \                                                                                        |
|         (Kamera Auto-Zoom & Follow Mobil Melaju)                                                        |
|                                                                                                         |
|   +-------------------------------------------------------------+                                       |
|   | [FLOATING MISSION HUD CARD (GLASSMORPHISM)]                 | [MINIMIZE / EXPAND]                   |
|   | Tahap 2: Validasi Berkas & Seleksi Administratif            |                                       |
|   | Estimasi: 3 - 5 Hari Kerja                                  |                                       |
|   | ----------------------------------------------------------- |                                       |
|   | [x] Surat Permohonan Pengantar Resmi (Klik untuk Cek Tips)  | (Sound: Ding!)                        |
|   | [x] Proposal Rencana Magang Terstruktur                     | (Sound: Ding!)                        |
|   | [ ] Transkrip Nilai / Rapor Terakhir                        | (Progress: 66% + Energy Glow)         |
|   | ----------------------------------------------------------- |                                       |
|   | [< Tahap Sebelumnya]          [Status: Siap 2/3]            [Lanjut ke Tahap 3 >]                   |
|   +-------------------------------------------------------------+                                       |
|                                                                                                         |
+---------------------------------------------------------------------------------------------------------+
| [BOTTOM DOCK: TIMELINE CHECKPOINTS 1, 2, 3, 4 DENGAN PROGRESS INDIKATOR & TOMBOL AUTOPLAY SIMULASI]      |
+---------------------------------------------------------------------------------------------------------+
```

---

## 5. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/partials/_peta_alur_modal.blade.php` | Modify | 1. Rombak layout modal dari grid split 2-kolom menjadi **Single Full Panoramic Canvas Viewport** (`viewBox="0 0 960 540"` panorama 16:9).<br>2. Pindahkan konten informasi, checklist interaktif, dan panduan dokumen ke dalam **Floating Mission HUD Overlay** di dalam kanvas yang semi-transparan elegan.<br>3. Implementasikan fungsi kamera sinematik `cameraFollowStep(stepNum)` dengan auto-zoom dan pan mulus.<br>4. Tambahkan **Web Audio Sound FX Manager** (engine hum, chime ding saat checklist dicentang, applause/fanfare saat sertifikat terbit, ambient toggle). |
| `public/css/landing-peta-alur-modal.css` | Modify | 1. Hapus layout split kolom kaku, ubah menjadi panorama full viewport.<br>2. Styling Glassmorphism Mission HUD (backdrop-filter blur, border neon biru/emas, tombol minimize/expand, glow checklist).<br>3. Styling sound toggle button dan camera zoom transition.<br>4. Perbaiki tata letak karakter bergerak agar memiliki panggung khusus di tiap pos tanpa tertabrak teks checkpoint. |
| `tests/Feature/LandingPagePetaAlurModalTest.php` | Modify | Perbarui assertion test untuk memverifikasi komponen Panoramic Canvas, Floating Mission HUD, Sound Controller, dan Checklist terintegrasi di dalam kanvas animasi. |

---

## 6. Langkah Implementasi Terstruktur

### Tahap 1: Restrukturisasi Layout Modal Menjadi Layar Penuh Panorama (16:9)
- [x] Buka [resources/views/public/partials/_peta_alur_modal.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/partials/_peta_alur_modal.blade.php).
- [x] Hapus struktur `.road-split-layout` dan `.peta-alur-detail-column` putih terpisah.
- [x] Perluas kanvas SVG menjadi panorama beresolusi tinggi `viewBox="0 0 960 540"`:
  - Bentangkan jalan raya berkelok megah Sleman melintasi area yang lega.
  - Berikan panggung khusus terpisah untuk masing-masing dari 4 adegan karakter hidup (pos 1 di kiri atas, pos 2 di kanan atas, pos 3 di kiri bawah, pos 4 di pelataran kantor Diskominfo kanan bawah) sehingga tidak ada teks atau karakter yang bertabrakan.

### Tahap 2: Integrasi Floating Mission HUD & Checklist Interaktif di Dalam Animasi
- [x] Buat kontainer `<div class="mission-hud-overlay" id="missionHudOverlay">` yang melayang anggun di dalam kanvas simulator.
- [x] Sertakan kontrol HUD:
  - Header misi: Badge tahap, estimasi jadwal, tombol minimize/expand (agar pengguna bisa menyembunyikan kartu jika ingin menikmati pemandangan penuh).
  - Daftar checklist kesiapan interaktif: Saat item dicentang, picu efek glow, update skor energi/kesiapan, dan bunyikan efek suara.
  - Tombol aksi panduan dokumen dan navigasi tahap (Sebelumnya / Selanjutnya).
- [x] Tambahkan status bar progres misi keseluruhan (*Mission Readiness Meter*) di bagian atas HUD.

### Tahap 3: Implementasi Kamera Dinamis (Dynamic Auto-Zoom & Follow Car)
- [x] Kembangkan fungsi JavaScript `interpolateViewBox(targetViewBox, duration)`:
  - Saat mobil berada di Tahap 1: Kamera menyorot area pos 1 dengan zoom `1.3x`.
  - Saat berpindah ke Tahap 2: Kamera mengikuti laju mobil secara halus (*cinematic tracking shot*).
  - Saat mobil tiba di Tahap 2: Kamera melembut dan membuka ruang bagi *Mission HUD* pos 2.
  - Tombol zoom manual (+ / - / Semua) tetap tersedia untuk pengguna yang ingin mengendalikan kamera secara manual.

### Tahap 4: Sistem Web Audio (Sound FX & Ambience Synthesizer)
- [x] Buat objek JavaScript `MagkomAudioSynth` yang mengoperasikan Web Audio API tanpa aset eksternal:
  - `playClickSound()`: Suara ketukan antarmuka lembut.
  - `playCarEngineSound()`: Suara frekuensi modulasi lembut (*whoosh / acceleration*).
  - `playChecklistDing()`: Notasi melodi dua nada bernada tinggi (*chime ting-tong*) saat pengguna mencentang persyaratan.
  - `playFanfareSound()`: Akord melodi kemenangan ceria saat mencapai Tahap 4 (Penerbitan E-Sertifikat).
  - `playAmbientLoop()` / `stopAmbientLoop()`: Lapisan suara synth ambient Sleman digital yang tenang.
- [x] Tambahkan tombol audio toggle di toolbar atas: ikon speaker dengan indikator gelombang suara.

### Tahap 5: Styling CSS Panorama & Micro-Animations
- [x] Buka [public/css/landing-peta-alur-modal.css](file:///d:/MAGKOM/MAGKOM/public/css/landing-peta-alur-modal.css).
- [x] Rancang tema gelap elegan bergaya *tactical sci-fi / smart city dashboard*:
  - Card Glassmorphism dengan `background: rgba(15, 23, 42, 0.88)` dan `backdrop-filter: blur(12px)`.
  - Checklist item dengan checkbox bercahaya hijau toska saat aktif.
  - Responsivitas mobile: pada layar HP, HUD mengapung rapi di bagian bawah (*bottom sheet*) tanpa menutupi jalur mobil.

### Tahap 6: Pengujian Otomatis & Verifikasi Visual
- [x] Perbarui pengujian di [tests/Feature/LandingPagePetaAlurModalTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/LandingPagePetaAlurModalTest.php):
  - Validasi keberadaan canvas panorama 960x540.
  - Validasi komponen `missionHudOverlay` dan elemen checklist di dalamnya.
  - Validasi controller audio `btnModalAudioToggle`.
  - Validasi hilangnya panel split putih terpisah.
- [x] Eksekusi suite test: `php artisan test --filter=LandingPagePetaAlurModalTest`.
- [x] Validasi zero-regression pada seluruh test portal: `php artisan test --filter=AlurMagang`.

---

## 7. Strategi Testing
- **Automated Feature Testing**: Memastikan seluruh struktur DOM baru (Panorama SVG, Mission HUD, Audio Buttons, Checklists) ter-render sempurna dari server.
- **Interactivity & State Integrity**: Memastikan interaksi klik checklist, pemilihan jenjang (SMK vs Mahasiswa), dan pergantian bulan tetap memperbarui teks panduan di dalam Mission HUD secara instan.
- **Audio Autoplay Policy Safety**: Memastikan audio tidak berbunyi otomatis sebelum ada interaksi pengguna (*user gesture requirement*) guna mencegah *browser policy warnings*.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Panel putih kaku di sisi kanan telah dihilangkan sepenuhnya; modal menjadi satu layar panorama animasi penuh (*Full Panoramic Living World*).
- [x] Informasi tahapan, ketentuan dinas, dan checklist kesiapan telah terintegrasi di dalam dunia animasi sebagai **Floating Mission HUD** yang elegan dan interaktif.
- [x] Setiap kali checklist dicentang, muncul animasi visual dan efek suara *chime ting-tong*.
- [x] Kamera kanvas SVG memiliki efek **Dynamic Follow & Auto-Zoom** yang bergerak mengikuti perjalanan mobil dinas.
- [x] Terdapat kontrol audio terpadu (Sound FX on/off & Musik Ambient digital).
- [x] Seluruh karakter bergerak ditempatkan di panggung masing-masing tanpa bertabrakan dengan teks atau ikon.
- [x] Seluruh automated test `LandingPagePetaAlurModalTest` dan `AlurMagangPageTest` lulus 100% hijau.
