# Rencana Kerja: Revamp Header Tema MAGKOM, Balon Webtoon Diagonal ke Kanan, dan Audio Efek Mobil

- **Tanggal Dokumen**: 2026-10-29
- **Terkait Fitur**: Peta Alur Interaktif Simulator 2D MAGKOM (`/peta-alur`)
- **Dokumen Referensi**: `resources/views/public/peta-alur-interaktif.blade.php`, `public/css/peta-alur-fullscreen.css`

---

## 1. Tujuan
Memperbarui tampilan dan pengalaman interaktif pada halaman simulator peta alur:
1. **Header Tema MAGKOM**: Mengubah tampilan header topbar dari latar putih menjadi warna tema resmi MAGKOM (Deep Navy `#1b3860` berpadu dengan aksen Biru Sleman `#0284c7`, teks putih berbobot tinggi, dan badge Sleman emas `#f59e0b`).
2. **Pembersihan Tombol**: Menghapus tombol "Tour Otomatis" dari topbar dan skrip agar tampilan bersih dan terfokus pada eksplorasi mandiri.
3. **Balon Komik Webtoon Diagonal ke Kanan**: Memposisikan kotak teks balon komik agak ke kanan (offset $X \approx +60\text{px}$) dengan ekor lancip berada di sisi kiri bawah balon yang menunjuk diagonal ke halte checkpoint di trotoar pinggir jalan, sehingga pemandangan karakter di samping kiri halte tetap lapang dan tidak terhalang.
4. **Efek Suara Notifikasi Checkpoint**: Menambahkan suara notifikasi chime manis yang jernih saat mobil tiba di halte pos baru.
5. **Efek Suara Mesin Mobil Halus**: Mengimplementasikan sintesis suara mesin mobil kartun halus (*gentle engine hum/purr*) menggunakan Web Audio API oscillator yang aktif saat mobil melaju dan hening saat mobil berhenti.

---

## 2. Asumsi & Analisis Teknis

### Asumsi Kerja:
1. **Warna Tema MAGKOM**: Mengikuti identitas visual MAGKOM di landing page dan sistem desain:
   - Latar Topbar: `linear-gradient(135deg, #1b3860 0%, #0f2444 100%)` dengan border bawah `1px solid rgba(2, 132, 199, 0.35)`.
   - Judul & Teks: Teks putih `#ffffff` dengan subtitle abu-abu perak `#94a3b8`.
   - Tombol Navigasi Kembali: Navy gelap beraksen border dengan hover glow biru Sleman.
   - Tombol Toggle Suara: Navy transparan dengan aksen aktif biru Sleman bercahaya.
2. **Geometri Balon Komik Webtoon**:
   - Kotak balon digeser ke kanan: $X$ dari $-150 \dots +150$ menjadi $-30 \dots +290$ (lebar 320px).
   - Ekor lancip diletakkan di sisi kiri-bawah kotak ($X = 15 \dots 45$) menjulur ke titik $(X = 0, Y = 50)$ mengarah langsung ke tiang/lingkaran halte di bawahnya.
3. **Audio Synthesizer Mandiri (Web Audio API)**:
   - Tidak memerlukan file MP3 eksternal (100% sintetis, offline, zero network latency, tanpa isu CORS).
   - Mesin Mobil: Nada rendah frekuensi $\approx 65 \dots 95\text{Hz}$ dengan low-pass filter dan gain lembut $\approx 0.025$ yang berosilasi saat `inputDirection !== 0` atau mobil bergerak, lalu fade-out saat mobil diam.
   - Notifikasi Checkpoint: Chime ramah bertingkat (dual sine bell 659Hz - 880Hz / E5 - A5 chime).

---

## 3. Evaluasi Opsi Arsitektur

| Opsi | Kelebihan | Kekurangan | Rekomendasi |
| :--- | :--- | :--- | :--- |
| **Opsi A: Web Audio API Synthesizer (Kode Mandiri)** | 100% self-contained, tidak butuh file audio tambahan di server, pitch dan volume mesin bisa adaptif mengikuti laju mobil, nol latensi. | Butuh perancangan kalkulasi Web Audio API (OscillatorNode, BiquadFilter, GainNode). | **(Direkomendasikan)** |
| **Opsi B: File Audio Eksternal (MP3/WAV Asset)** | Suara rekaman riil. | Membutuhkan file aset baru, potensi gagal memuat (404/CORS), konsumsi kuota bandwidth, looping sering terdengar terputus (seam glitch). | Tidak disarankan |

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/peta-alur-interaktif.blade.php` | Modify | 1. Hapus tombol `#btnSimAutoplay` dari topbar.<br>2. Perbarui path SVG balon webtoon (`webtoonBubble1..4`) menjadi kotak condong ke kanan dengan ekor mengarah ke kiri-bawah.<br>3. Tambahkan sound fx notifikasi checkpoint dan engine sound loop saat mobil bergerak di skrip JS.<br>4. Hapus fungsi `toggleSimAutoplay` yang sudah tidak digunakan. |
| `public/css/peta-alur-fullscreen.css` | Modify | 1. Styling `.peta-alur-topbar` dengan tema warna MAGKOM (Navy `#1b3860`, border biru Sleman, teks putih, tombol kontras).<br>2. Hapus CSS selector `.btn-sim-autoplay`.<br>3. Sesuaikan CSS `.webtoon-speech-bubble` untuk transform-origin ekor kiri-bawah. |
| `tests/Feature/PetaAlurPageTest.php` | Modify | Perbarui assertion: pastikan ketiadaan `#btnSimAutoplay`, verifikasi tema topbar, dan verifikasi struktur balon baru. |

---

## 5. Langkah-Langkah Implementasi

- [ ] **Langkah 1: Restrukturisasi SVG Balon Webtoon di `peta-alur-interaktif.blade.php`**
  - Ubah path SVG pada `#webtoonBubble1` s/d `#webtoonBubble4`:
    - Kotak balon: $X = -20 \dots 290$, $Y = -135 \dots -15$.
    - Ekor lancip: Menjulur dari sisi kiri bawah kotak ($X = 35$) mengarah miring ke titik $(X = 0, Y = 50)$ tepat di atas tiang halte.
    - Konten teks (badge pos, judul, deskripsi, dan tombol aksi) digeser ke dalam kotak kanan baru.
- [ ] **Langkah 2: Hapus Tombol Tour Otomatis**
  - Hapus elemen `<button id="btnSimAutoplay" ...>` dari topbar HTML.
  - Hapus fungsi `toggleSimAutoplay()` dan variabel `isAutoplayRunning` dari JavaScript.
- [ ] **Langkah 3: Terapkan Tema Warna MAGKOM pada Topbar di `peta-alur-fullscreen.css`**
  - Berikan latar `.peta-alur-topbar`: Deep Navy `#1b3860` berpadu aksen border bawah `#0284c7`.
  - Teks judul putih `#ffffff`, subtitle perak `#94a3b8`, badge Sleman `#f59e0b`.
  - Tombol navigasi kembali & tombol suara diselaraskan dengan estetika tema biru gelap yang elegan.
- [ ] **Langkah 4: Implementasi Suara Notifikasi Checkpoint & Mesin Mobil Halus**
  - Tambahkan fungsi audio di objek `MagkomSound`:
    - `playNotificationChime()`: Notifikasi lonceng ramah saat mobil memasuki area checkpoint baru.
    - `startEngineHum()`: Deru mesin mobil lembut (*low hum synthesizer*).
    - `stopEngineHum()`: Fade-out suara mesin secara halus saat mobil berhenti melaju.
  - Panggil `startEngineHum()` saat tombol panah ditekan atau tuas analog digerakkan, dan panggil `stopEngineHum()` saat input dilepas.
  - Panggil `playNotificationChime()` saat mobil tiba di halte baru.
- [ ] **Langkah 5: Pengujian & Verifikasi Kualitas**
  - Jalankan test `PetaAlurPageTest` dengan PHP 8.3 Laragon:
    `& "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test --filter=PetaAlurPageTest`.
  - Jalankan Pint code style check:
    `& "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" vendor/bin/pint tests/Feature/PetaAlurPageTest.php --test`.

---

## 6. Kriteria Selesai (Definition of Done)

- [ ] Topbar peta alur tampil elegan dengan warna tema MAGKOM (Deep Navy `#1b3860` + Biru Sleman `#0284c7`).
- [ ] Tombol Tour Otomatis tidak lagi muncul di topbar.
- [ ] Kotak balon pop-up Webtoon bergeser ke kanan dan ekor lancipnya menunjuk miring secara presisi ke tiang halte checkpoint di sebelah kiri bawahnya.
- [ ] Karakter di samping halte tetap terlihat leluasa dan tidak tertutup balon teks.
- [ ] Suara deru mobil terdengar halus saat mobil melaju dan hening saat berhenti.
- [ ] Suara notifikasi chime berbunyi manis saat mobil tiba di halte checkpoint.
- [ ] Seluruh unit/feature test lolos 100% tanpa error.
