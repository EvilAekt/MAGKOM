# Plan: Perapian Tampilan Mobile Peta Alur Interaktif (Simulator Magang)

Dokumen rencana kerja ini disusun berdasarkan panduan `web-planning` dan aturan repositori `AGENTS.md` untuk menyelesaikan masalah penumpukan elemen antarmuka (*UI overcrowding/overlapping*) pada tampilan *mobile portrait* halaman Peta Alur Interaktif (`/peta-alur`).

---

## 1. Tujuan

Merapikan dan menata ulang tata letak antarmuka simulator peta alur interaktif pada perangkat ponsel (*mobile portrait* lebar 360px – 430px) agar:
1. **Bebas Tumpukan (*Zero Clutter & Overlap*)**: Elemen *topbar*, balon komik tahapan (*speech bubble*), mobil GT-R, jalan raya, kontrol kemudi analog (*virtual analog joystick HUD*), dan *mini-footer* tidak saling bertumpuk atau menutupi satu sama lain.
2. **Topbar 1 Baris (*Single-Row Header*)**: Mencegah *topbar* terlipat menjadi 2 baris yang memakan ruang vertikal layar.
3. **Kontrol Kemudi Ringkas (*Streamlined Controls*)**: Menghilangkan petunjuk keyboard desktop yang tidak relevan di ponsel layar sentuh dan memposisikan kemudi analog secara ergonomis di area rumput bawah tanpa menabrak mobil/jalan.
4. **Kamera Diorama Proporsional (*Optimized Mobile Camera ViewBox*)**: Mengalibrasi framing kamera 2D di `peta-alur.js` agar balon tahapan dan mobil terlihat utuh dengan ruang napas visual (*visual breathing room*) yang lega.

---

## 2. Asumsi & Analisis Masalah

### 2.1 Analisis Akar Masalah (Berdasarkan Tangkapan Layar User)
Berdasarkan investigasi visual pada tangkapan layar perangkat mobile:
1. **Topbar Lipat 2 Baris**: Pada breakpoint `@media (max-width: 600px)`, `.peta-alur-topbar` mewarisi `flex-wrap: wrap; row-gap: 0.5rem;` dari breakpoint 900px, sehingga tombol navigasi terbagi 2 baris (Baris 1: `[<- Beranda]` + judul, Baris 2: `[Suara: Off]` + `[Daftar Sekarang]`). Hal ini menghabiskan tinggi layar hingga ~95px.
2. **Kontrol Analog Memakan Ruang Vertikal Berlebihan**: Kontrol `.virtual-analog-container` ditumpuk vertikal menjadi 4 layer:
   - Tombol Klakson `[▲ KLAKSON]` (menutupi mobil & aspal).
   - Tuas Slider `[◀ KEMUDI ▶]`.
   - Pil petunjuk `🕹️ Klik / Tahan Panah atau Tarik Tuas`.
   - Petunjuk keyboard `Keyboard: ← / → atau A / D • Klakson: ↑ / W` (tidak relevan untuk layar sentuh ponsel).
   Total tinggi blok ini mencapai ~150px.
3. **Balon Komik (*Speech Bubble*) Terlalu Mendominasi Layar Potret**: Balon komik berukuran lebar 310px dan tinggi 140px di dalam SVG. Pada rasio potret sempit (aspek ~9:19), balon komik mengambil lebih dari 30% area vertikal kanvas yang tersisa.
4. **Footer Mengurangi Ruang Kanvas**: `.peta-alur-mini-footer` setinggi 42px dengan teks 2 baris menyita ruang berharga di layar beresolusi pendek.

### 2.2 Asumsi Kerja
- Tidak mengubah struktur semantik esensial Blade (mematuhi aturan `AGENTS.md` - Blade read-only secara default). Seluruh penyesuaian tata letak diselesaikan melalui file CSS modular di `resources/css/pages/public/peta-alur.css` dan logika framing kamera di `resources/js/pages/public/peta-alur.js`.
- Semua fitur interaktif (kemudi analog geser, tombol maju/mundur, tombol klakson, suara BGM/SFX, tombol aksi balon komik) tetap berfungsi 100% normal.

---

## 3. Pendekatan Teknis & Evaluasi Alternatif

### Perbandingan Opsi Arsitektur:

| Kriteria | Opsi A: Pure CSS & Camera Framing Tuning (Direkomendasikan) | Opsi B: Mengubah Balon Komik Menjadi Bottom Sheet Modal |
| :--- | :--- | :--- |
| **Deskripsi** | Menata ulang media query CSS (topbar 1 baris, analog HUD horizontal/kompak, sembunyikan hint keyboard, footer ringkas) dan mengalibrasi kamera `curCam` SVG di JS agar proporsional. | Membongkar elemen balon SVG dari kanvas diorama dan memindahkannya menjadi modal HTML *bottom sheet* mengambang di luar kanvas saat mobile. |
| **Kelebihan** | - Menjaga estetika simulator game 2D orisinal tanpa merusak Blade.<br>- Tidak memerlukan refactoring besar arsitektur SVG.<br>- Risiko regresi sangat rendah.<br>- Sesuai dengan aturan `AGENTS.md`. | - Balon tahapan terpisah sepenuhnya dari kanvas 2D.<br>- Teks panjang dapat discroll secara native. |
| **Kekurangan** | - Memerlukan presisi kalibrasi padding, scale, dan kamera viewBox. | - Melanggar prinsip *Blade read-only* dan membutuhkan perubahan besar pada markup Blade.<br>- Menghilangkan nuansa diorama interaktif dan mobil yang melintasi halte. |
| **Rekomendasi** | **Dipilih (Opsi A)** karena mempertahankan identitas desain simulator interaktif MAGKOM dengan perubahan terarah (*minimal, focused change*). | Ditolak karena *scope creep* dan risiko merusak layout desktop yang sudah stabil. |

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/public/peta-alur.css` | `Modify` | - Paksa `.peta-alur-topbar` menjadi 1 baris (`flex-wrap: nowrap; height: 52px;`).<br>- Teks tombol disingkat / disembunyikan di mobile (label audio teks disembunyikan, judul truncated, tombol register kompak).<br>- Sembunyikan `.analog-kbd-hint` di mobile (`display: none;`).<br>- Rampingkan `.virtual-analog-container` (posisi rapi di rumput bawah, tombol klakson diperkecil/berdampingan, skala proporsional).<br>- Rampingkan `.peta-alur-mini-footer` (teks 1 baris, tinggi ~28px).<br>- Skala balon komik `.webtoon-speech-bubble` disesuaikan di mobile agar pas dengan lebar layar ponsel. |
| `resources/js/pages/public/peta-alur.js` | `Modify` | - Kalibrasi kamera `curCam.h` dan `curCam.y` pada mode mobile potret agar mobil dan balon tahapan mendapatkan framing vertikal yang seimbang tanpa terpotong atau terlalu sesak. |
| `public/css/peta-alur-fullscreen.css` | `Modify` | - Sinkronisasi CSS build/standalone untuk kompatibilitas pratinjau langsung jika diperlukan. |

---

## 5. Langkah Implementasi Terperinci

### Tahap 1: Restrukturisasi Responsif Topbar (Single Row Header)
- [ ] Buka `resources/css/pages/public/peta-alur.css` pada blok `@media (max-width: 600px)` dan `@media (max-width: 360px)`.
- [ ] Atur `.peta-alur-topbar` agar tidak melipat:
  - `flex-wrap: nowrap !important;`
  - `height: 52px; min-height: 52px;`
  - `padding: 0 0.75rem; gap: 0.5rem;`
- [ ] Pada tombol `.btn-nav-back`: sembunyikan teks `span` di layar `< 480px` sehingga hanya menampilkan ikon panah kembali yang bersih, padding `0.4rem 0.55rem`.
- [ ] Pada `.topbar-title`: tetapkan ukuran font `0.8rem - 0.85rem`, `white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 130px - 160px;`.
- [ ] Pada tombol `.btn-audio-toggle`: sembunyikan `#audioToggleLabel` di mobile sehingga hanya ikon speaker `[🔊]` yang tampil, berbentuk pil bulat kompak (`padding: 0.4rem; border-radius: 50%;`).
- [ ] Pada tombol `.btn-topbar-register`: buat lebih ringkas (`padding: 0.38rem 0.65rem; font-size: 0.75rem;`).

### Tahap 2: Rampingkan Virtual Analog Joystick HUD
- [ ] Pada `@media (max-width: 768px)` dan `@media (max-width: 600px)`:
  - Berikan `.analog-kbd-hint { display: none !important; }` untuk membuang teks shortcut keyboard yang tidak berguna di layar sentuh.
  - Sederhanakan `.analog-hint-text`: perkecil font ke `0.62rem` dengan padding tipis `0.1rem 0.45rem`.
  - Susun ulang atau rampingkan tombol klakson `.analog-horn-btn`: kurangi padding dan font size agar tidak menjulang tinggi ke area aspal mobil.
  - Atur posisi `.virtual-analog-container` agar terkunci rapi di area rumput paling bawah: `bottom: 0.4rem; transform: translateX(-50%) scale(0.82);`.

### Tahap 3: Optimasi Framing Kamera di JavaScript (`peta-alur.js`)
- [ ] Periksa kalkulasi kamera mobile di baris 535-555:
  ```javascript
  const isMobile = window.innerWidth < 768;
  if (isMobile) {
      // Kalibrasi tinggi viewport kamera agar balon dan mobil tidak saling berhimpitan
      curCam.h = 620; // Ruang vertikal lebih lega
      curCam.y = -50; // Naikkan sedikit sudut pandang atas agar balon tidak terpotong
      ...
  }
  ```
- [ ] Pastikan posisi transisi mobil dan trigger kamera tetap mulus saat analog digeser atau tombol panah ditekan.

### Tahap 4: Optimasi Balon Komik (*Speech Bubble*) & Mini Footer
- [ ] Pada `.webtoon-speech-bubble` di mobile: pastikan balon tidak meluap ke luar kanvas dengan menetapkan `transform-origin` dan penskalaan responsif yang pas.
- [ ] Pada `.peta-alur-mini-footer`:
  - Ubah tinggi menjadi `height: 28px;` pada mobile.
  - Teks `.peta-footer-copy`: `font-size: 0.65rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 95vw;`.
  - Teks ringkas: *"© 2026 Diskominfo Sleman • MAGKOM"*.

### Tahap 5: Kompilasi Aset & Quality Gate
- [ ] Jalankan build aset Vite: `npm run build`.
- [ ] Periksa formatting kode menggunakan Laravel Pint:
  `& "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" vendor/bin/pint --test`.
- [ ] Verifikasi konsistensi tanpa error JavaScript di console browser.

---

## 6. Strategi Testing & Verifikasi

### 6.1 Uji Responsif Berbagai Viewport Ponsel (Chrome DevTools Mobile Emulation)
1. **iPhone SE (375 × 667 px)**: Layar terkecil; pastikan topbar 1 baris, tidak ada teks bertumpuk, analog kontrol berada di rumput tanpa menutupi roda mobil.
2. **iPhone 14 / 15 Pro (393 × 852 px)**: Aspek rasio modern 19.5:9; pastikan jarak antara balon komik dan mobil proporsional.
3. **Samsung Galaxy S20 / S22 (360 × 800 px)**: Lebar 360px sempit; pastikan judul topbar terpotong rapi dengan ellipsis tanpa merusak tombol aksi.
4. **Mobile Landscape (Tinggi < 500px)**: Pastikan simulator otomatis menyesuaikan tata letak lanskap dengan analog di kiri dan stage card di kanan.

### 6.2 Uji Interaksi Layar Sentuh
- Uji sentuhan pada tuas analog (drag ke kiri & kanan).
- Uji sentuhan tombol Klakson `[▲ KLAKSON]` (suara dan animasi gelombang aktif).
- Uji tombol `[Lanjut Pos ➔]` pada balon komik (mobil melaju otomatis ke pos berikutnya).
- Uji toggle audio `[🔊]` (on/off berfungsi lancar).

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| Tombol topbar terlalu kecil untuk disentuh (*touch target size*) | Aksesibilitas WCAG menurun | Jaga area tap minimum 40×40px dengan padding transparan meskipun ikon visualnya kompak. |
| Kamera diorama terlalu jauh (*zoomed out*) membuat karakter kecil | Estetika visual berkurang | Batasi `curCam.h` maksimal di 620px-640px, hanya menaikkan offset `curCam.y` secukupnya agar balon komik memiliki ruang atas. |
| Konflik cache CSS Vite | Perubahan tidak langsung terlihat di browser | Vite dev server aktif dengan HMR (Hot Module Replacement) pada port 5173. Tambahkan query timestamp pada stylesheet statis jika diperlukan. |

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Topbar pada layar ponsel (`< 600px`) tampil rapi dalam 1 baris mendatar tanpa *wrapping*.
- [ ] Tombol pintasan keyboard pada kontrol analog dihilangkan pada tampilan mobile.
- [ ] Kontrol kemudi analog tidak lagi menutupi mobil GT-R atau lajur jalan aspal.
- [ ] Balon komik tahapan pos magang tampil utuh dan terbaca jelas tanpa menutupi mobil atau terpotong di tepi atas/samping.
- [ ] Mini footer berukuran ramping (~28px) dan tidak memakan ruang kanvas diorama.
- [ ] Semua fungsi interaktif (analog joystick, klakson, next pos, audio toggle, link daftar) beroperasi 100% normal.
- [ ] `npm run build` dan `vendor/bin/pint --test` lulus tanpa error.

