# Rencana Kerja: Penghapusan Kontrol Zoom, Reset Mobil Otomatis Saat Sampai Pinggir, & Pengayaan Gedung serta Lampu Jalan di Ujung Lanskap Peta Alur

Dokumen rencana kerja ini merespons umpan balik dan permintaan pengguna:
> *"ini hilangkan saja [tangkapan layar tombol zoom + / - / Semua di kiri atas], dan kalo sudah sampe pingir tolong di reset lagi dan itu tampilannya masih kurang yang ujung, km bisa tambahkan lampu atau gedung2 gitu buat plan nya"*

Disusun sesuai panduan **Web Planning Workflow** sebelum eksekusi penulisan kode dimulai.

---

## 1. Analisis Kebutuhan & Permasalahan Saat Ini

Berdasarkan analisis visual dan kode pada `resources/views/public/peta-alur-interaktif.blade.php`:

| Komponen | Kondisi Saat Ini | Permintaan Pengguna & Solusi Teknis |
| :--- | :--- | :--- |
| **1. Tombol Zoom Kiri Atas (`+`, `-`, `Semua`)** *(Gambar 1)* | Komponen mengambang `.road-zoom-controls` berada di kiri atas layar (di bawah header). Tombol ini jarang digunakan dan sedikit menghalangi pemandangan pegunungan Merapi. | **Hapus total elemen tombol zoom** (`#btnRoadZoomIn`, `#btnRoadZoomOut`, `#btnRoadZoomReset`) dan CSS terkait agar sudut kiri atas kanvas 100% bersih, lega, dan estetis. |
| **2. Perilaku Saat Mobil Sampai Pinggir Jalan** | Saat mobil dikemudikan melewati Pos 4 (Kelulusan) ke arah kanan mentok (`carX >= 2240`), mobil hanya berhenti membentur tepi kanvas tanpa ada siklus lanjut. | **Implementasikan Mekanisme Reset Otomatis (*Auto-Loop / Reset Transition*)**: Ketika mobil meluncur melewati batas akhir Pos 4 / Kompleks Diskominfo Sleman (`carX >= 2220`), sistem menampilkan selebrasi penyelesaian alur (fanfare chime + dialog ucapan sukses) lalu mobil secara halus berputar/meluncur kembali (*seamless loop reset*) ke titik awal Pos 1 (Registrasi, `carX = 280`), kamera kembali mengarah ke Pos 1, dan balon Pos 1 aktif kembali. |
| **3. Ujung Lanskap Terpotong (*Cut-off Blank Edges*)** *(Gambar 2)* | Pada ujung kiri (`x: 0..150`) dan ujung kanan (`x: 2200..2400`), lapisan bukit dan jalan aspal berakhir patah dengan langit kosong di sudutnya, tampak kurang padat dan belum seperti lanskap perkotaan utuh. | **Perkaya Ujung Lanskap dengan Gedung & Tiang Lampu Perkotaan Sleman**: Sesuai arahan pengguna (*"tambahkan lampu atau gedung2 gitu"*):<br>• **Ujung Kiri (Gerbang Masuk Kabupaten Sleman Barat)**: Tambahkan tiang lampu jalan klasik modern berpendar hangat (*ornate street lamps*), gapura selamat datang, pepohonan rindang, dan siluet gedung institusi/kampus pendaftaran.<br>• **Ujung Kanan (Kawasan Pusat Pemerintahan & Diskominfo Sleman)**: Tambahkan deretan gedung perkantoran dinas bertingkat, tiang bendera Merah Putih, tiang lampu jalan kota, taman kantor, dan tower komunikasi berlampu beacon kelap-kelip.<br>• **Penyambungan Lanskap Lebar Penuh**: Perpanjang lapisan aspal, marka, trotoar, rumput, dan bukit dari `x: -100` hingga `x: 2500` agar tidak ada lagi bagian terpotong patah di semua rasio layar monitor. |

---

## 2. Asumsi Teknis & Pertanyaan Terbuka

1. **Asumsi Reset Siklus**:
   - Ketika mobil melaju melewati batas Pos 4 (`carX > 2200`), mobil meluncur menuju gerbang keluar Diskominfo Sleman, audio fanfare berbunyi ramah, kemudian dengan transisi fade/teleportasi halus mobil kembali berada di Pos 1 (`carX = 280`) dengan status reset siap dicoba kembali.
   - Jika pengguna memundurkan mobil hingga batas kiri paling ujung (`carX <= 140`), mobil tertahan di gerbang kedatangan Pos 1 secara natural tanpa glitch.
2. **Asumsi Desain Lampu & Gedung**:
   - Gaya visual gedung dan tiang lampu diselaraskan 100% dengan gaya seni vektor kartun modern (*flat cartoon illustration*) yang sudah ada (menggunakan palet warna Sleman: biru navy `#1b3860`, biru toska/sky `#0284c7`, kuning hangat `#facc15` untuk cahaya lampu jalan).

---

## 3. Evaluasi Opsi Arsitektur

### Evaluasi Perilaku Reset Pinggir Jalan:
- **Opsi A: Auto Seamless Reset dengan Selebrasi Halus (Direkomendasikan)**:
  - Saat mobil melewati Pos 4 (Kelulusan, `carX = 2080`) dan mencapai gerbang akhir (`carX >= 2200`):
    1. Sistem memicu bunyi fanfare gembira.
    2. Mobil melaju keluar gerbang kanan perlahan, lalu kamera bergeser mulus (*smooth pan transition*) kembali ke Pos 1 di sisi kiri (`carX = 280`).
    3. Mobil muncul kembali di Pos 1 dalam keadaan siap jalan lagi, balon webtoon Pos 1 otomatis terbuka menyambut pengguna.
  - *Kelebihan*: Pengalaman simulator terasa seperti alur berkelanjutan yang menyenangkan (*continuous journey loop*), tanpa rasa tersangkut di dinding jalan.
- **Opsi B: Hard Wall Barrier (Hanya Ditahan Mentok)**:
  - Mobil hanya tertahan di koordinat `ROAD_MAX_X = 2240` dan tidak bisa maju lagi kecuali dimundurkan secara manual.
  - *Kelemahan*: Pengguna merasa mobilnya mogok atau macet di pinggir seperti yang dikeluhkan saat ini.

> **Keputusan**: Menggunakan **Opsi A (Auto Seamless Reset)** untuk memberikan kepuasan interaksi pengguna yang tuntas.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/peta-alur-interaktif.blade.php` | Modify | 1. **Hapus Elemen Zoom**: Hapus markup `.road-zoom-controls` (tombol `btnRoadZoomIn`, `btnRoadZoomOut`, `btnRoadZoomReset`).<br>2. **Tambah Aset Tiang Lampu Jalan Klasik Sleman**: Tambahkan deretan tiang lampu jalan berpendar hangat di sepanjang trotoar (di ujung kiri `x: 60, 180`, tengah `x: 580, 1180, 1780`, dan ujung kanan `x: 2180, 2340`).<br>3. **Tambah Siluet Gedung Perkotaan & Kantor Pemerintahan**: Bangun kompleks gedung bertingkat modern di ujung kanan (Diskominfo Hub & Pemkab Sleman) serta gedung kampus/gerbang di ujung kiri.<br>4. **Perpanjang Lintasan Menyeluruh**: Perlebar elemen jalan, trotoar, dan bukit dari `x: -100` hingga `x: 2500` agar ujung kanvas tidak terpotong.<br>5. **Logika Auto-Reset di Game Loop**: Tambahkan deteksi jika `carX >= 2220`, mobil otomatis di-reset secara mulus ke Pos 1 (`carX = 280`). |
| `public/css/peta-alur-fullscreen.css` | Modify | 1. Bersihkan selector `.road-zoom-controls` dan `.btn-road-zoom`.<br>2. Tambahkan styling animasi pendaran cahaya tiang lampu jalan (`.street-lamp-glow`, `.street-lamp-beam`).<br>3. Tambahkan styling arsitektur gedung perkotaan Sleman dan gapura. |
| `tests/Feature/PetaAlurPageTest.php` | Modify | Perbarui test assertions: pastikan kontrol zoom (`btnRoadZoomIn`, `btnRoadZoomOut`, `btnRoadZoomReset`) telah dihapus (`assertDontSee`), serta pastikan elemen lampu jalan dan gedung-gedung ujung terverifikasi (`assertSee`). |

---

## 5. Langkah Implementasi Terstruktur

### Tahap 1: Pembersihan Tombol Zoom di Kiri Atas
- [x] Buka [resources/views/public/peta-alur-interaktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php).
- [x] Hapus markup blok `.road-zoom-controls` (baris 65–69).
- [x] Hapus fungsi JavaScript `window.modalRoadZoomIn`, `window.modalRoadZoomOut`, dan `window.modalRoadZoomReset` di bagian script.
- [x] Buka [public/css/peta-alur-fullscreen.css](file:///d:/MAGKOM/MAGKOM/public/css/peta-alur-fullscreen.css), hapus rule CSS `.road-zoom-controls`, `.btn-road-zoom`, `.btn-road-fit`.

### Tahap 2: Pengayaan Visual Ujung Kiri & Kanan (Gedung-Gedung & Tiang Lampu Jalan)
- [x] Di [resources/views/public/peta-alur-interaktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php):
  - **Perpanjangan Landasan Horizontal**: Ubah koordinat awal/akhir bukit, trotoar, dan aspal dari `x="0" width="2400"` menjadi membentang dari `x="-80" width="2560"`.
  - **Gedung Perkotaan di Ujung Kiri (`x: -60 s/d 180`)**: Tambahkan gedung kampus/akademik, pepohonan rimbun, dan Gapura Gerbang Selamat Datang Sleman Barat.
  - **Gedung Perkantoran Megah di Ujung Kanan (`x: 2150 s/d 2450`)**: Tambahkan blok Gedung Smart City Diskominfo Sleman 3 lantai, jendela kaca modern dengan pantulan cahaya, tiang bendera Merah Putih berkibar, taman bunga kantor, dan tower komunikasi BTS dengan lampu suar.
  - **Jajaran Tiang Lampu Jalan (*Street Lamps*)**: Pasang grup SVG tiang lampu jalan kota berpendar kuning hangat di trotoar di sepanjang jalur:
    - Lampu 1 di `x: 80` (Ujung Barat)
    - Lampu 2 di `x: 580` (Antara Pos 1 dan Pos 2)
    - Lampu 3 di `x: 1180` (Antara Pos 2 dan Pos 3)
    - Lampu 4 di `x: 1780` (Antara Pos 3 dan Pos 4)
    - Lampu 5 di `x: 2160` (Depan Gerbang Kantor Diskominfo)
    - Lampu 6 di `x: 2360` (Ujung Timur)
- [x] Di [public/css/peta-alur-fullscreen.css](file:///d:/MAGKOM/MAGKOM/public/css/peta-alur-fullscreen.css):
  - Tambahkan animasi pendaran halus bohlam lampu jalan malam/senja (`@keyframes streetLampGlow`).

### Tahap 3: Implementasi Mekanisme Reset Otomatis Saat Mencapai Pinggir Jalan
- [x] Di [resources/views/public/peta-alur-interaktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php) pada `gameLoop` dan fungsi pergerakan:
  - Buat fungsi `triggerAutoLoopReset()`:
    1. Kunci input sejenak (`isAutoLooping = true`).
    2. Bunyikan efek suara fanfare gembira.
    3. Tampilkan pesan selebrasi ringan atau transisi mulus.
    4. Kembalikan posisi mobil ke Pos 1 (`carX = 280`), sinkronkan kamera ke awal (`targetCamX = 0`), buka balon webtoon Pos 1, dan buka kunci input kembali.
  - Integrasikan pemicu:
    - Jika `carX >= 2220` (melewati ujung kantor Diskominfo): jalankan `triggerAutoLoopReset()`.
    - Batasi batas mundur agar mobil tidak bisa keluar ke kiri sebelum `x = 160`.

### Tahap 4: Verifikasi & Pengujian
- [x] Perbarui pengujian pada [tests/Feature/PetaAlurPageTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/PetaAlurPageTest.php):
  - Ubah assertion `assertSee` untuk `btnRoadZoomIn` menjadi `assertDontSee`.
  - Tambahkan assertion `assertSee` untuk elemen lampu jalan (`street-lamp-group`) dan gedung perkantoran ujung.
- [x] Jalankan pengujian PHPUnit:
  `& "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test --filter=PetaAlurPageTest`
- [x] Ambil screenshot tampilan baru melalui skrip headless Chrome untuk memverifikasi ujung lanskap yang padat dan hilangnya tombol zoom.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Tombol zoom mengambang (`+`, `-`, `Semua`) di pojok kiri atas telah dihilangkan sepenuhnya dari halaman dan CSS.
- [x] Ujung kiri dan ujung kanan lanskap tidak lagi tampak terpotong segitiga kosong; kini dihiasi tiang lampu jalan berpendar dan gedung-gedung pemerintahan/perkotaan yang megah.
- [x] Ketika mobil dikemudikan melewati Pos 4 hingga ujung jalan, mobil secara otomatis di-reset kembali ke Pos 1 (Registrasi) dengan transisi mulus dan selebrasi fanfare.
- [x] Semua kontrol tuas analog, tombol panah maju/mundur, keyboard, klik halte, dan tombol balon webtoon tetap berjalan 100% responsif.
- [x] Seluruh pengujian otomatis di `PetaAlurPageTest.php` lulus 100% tanpa error.
