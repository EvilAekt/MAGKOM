# Rencana Kerja: Jalur Jalan Lurus (Straight Road Timeline) Seksi Tahapan

Dokumen ini merancang transformasi visual jalur jalan pada seksi **Alur & Tahapan Magang** (`#tahapan`), mengubah lintasan aspal yang sebelumnya berbelok-belok (*winding S-curve*) menjadi **jalur jalan lurus vertikal (*straight road*)** yang rapi dan elegan persis seperti pada referensi Gambar 2.

---

## 1. Pahami Permintaan & Kebutuhan

- **Masalah & Kebutuhan Pengguna**:
  Pada tampilan saat ini (Gambar 1), jalur jalan dibuat berbelok-belok (*winding S-curve*) dari kiri ke kanan. Pengguna ingin jalur jalan tersebut dibuat **lurus vertikal** mengacu pada referensi Gambar 2.
- **Batasan Ketat Pengguna**:
  - *"efek font dll tetap di sama pada tampilan sekarang hanya saja untuk jalan di bikin lurus"*: Seluruh tipografi, teks deskripsi, efek animasi mobil yang berjalan saat di-scroll, warna gradien progres, efek kartu aktif/inaktif, dan ikon checkpoint **tetap dipertahankan 100%**.
- **Hasil Akhir**:
  Jalur aspal menjadi satu garis vertikal lurus ke bawah, dengan marka putus-putus di tengah, 4 simpul checkpoint melingkar terpasang tepat di atas jalur lurus, serta bendera START di atas dan FINISH di bawah, berdampingan rapi dengan 4 kartu tahapan di sisi kanan.

---

## 2. Investigasi Codebase (Read-Only)

1. **Struktur SVG** ([tahapan.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/blocks/tahapan.blade.php)):
   - Menggunakan `<svg width="170" height="1000" viewBox="0 -80 170 1000" class="tahapan-svg-canvas">`.
   - Jalur aspal `#roadSvgPath` saat ini meliuk antara koordinat $X=40$ dan $X=120$:
     `d="M40 -80 L40 0 L40 40 C40 75 120 60 120 105 S40 135 40 170 ..."`
   - Checkpoint nodes 1 s/d 4 (`[data-checkpoint-index]`) berada di koordinat meliuk $X=120$:
     - Checkpoint 1: $(120, 105)$
     - Checkpoint 2: $(120, 315)$
     - Checkpoint 3: $(120, 525)$
     - Checkpoint 4: $(120, 735)$
   - START node berada di $(40, -72)$ dan FINISH node berada di $Y=870$.
2. **Interactive Engine** ([landing.js](file:///d:/MAGKOM/MAGKOM/resources/js/pages/public/landing.js)):
   - Fungsi `initTahapanRoad()` membaca panjang total `#roadSvgPath` (`getTotalLength()`).
   - Mobil `#roadCarGroup` diposisikan secara dinamis menggunakan `roadPath.getPointAtLength(currentLen)` dan sudut rotasi `atan2(dy, dx)`.
   - Checkpoint aktif dihitung berdasarkan posisi $Y$ (`cpY(i) = i * 210 + 105`).
   - Karena kalkulasi berbasis koordinat parametrik SVG path, mengganti path kurva menjadi path lurus akan membuat mobil otomatis melaju lurus ke bawah dengan rotasi tegak $90^\circ$ secara sempurna tanpa merusak logika scroll.
3. **CSS** ([landing.css](file:///d:/MAGKOM/MAGKOM/resources/css/pages/public/landing.css)):
   - Mengatur skala SVG di mobile (`transform: scale(0.72)`) dan padding kartu `.tahapan-cards-list` (`padding: 50px 0 50px 125px`).

---

## 3. Evaluasi Opsi Pendekatan Teknis

| Parameter | Opsi A: Jalan Lurus Terpadu (Mobile & Desktop) — Direkomendasikan | Opsi B: Jalan Lurus Khusus Mobile, Desktop Tetap Berbelok |
| :--- | :--- | :--- |
| **Deskripsi** | Mengubah lintasan jalan menjadi lurus vertikal pada file SVG utama, sehingga tampilan mobile persis seperti Gambar 2 dan tampilan desktop juga rapi, teratur, dan modern. | Menyediakan dua lintasan SVG terpisah (atau kalkulasi JS bersyarat) agar desktop tetap meliuk dan mobile lurus. |
| **Konsistensi UI/UX** | **Sangat Konsisten & Bersih**: Gaya visual seragam di semua perangkat, navigasi tahapan menjadi linier dan mudah dicerna. | Berbeda antara desktop dan mobile. |
| **Kompleksitas Kode** | **Rendah & Sangat Andal**: Menggunakan 1 path SVG lurus yang bersih; engine mobil di JS berjalan 100% mulus tanpa perlu percabangan kondisi. | Tinggi (memerlukan 2 path SVG berbeda dan penanganan JS terpisah per breakpoint). |
| **Rekomendasi** | **Sangat Direkomendasikan (Opsi A)** | Alternatif |

---

## 4. Rincian Desain Jalur Jalan Lurus (Berdasarkan Gambar 2)

### A. Geometri Sumbu X Terpusat ($X = 48$)
- **Jalur Aspal Utama (`#roadSvgPath`)**:
  `d="M48 -80 L48 920"` dengan lebar `stroke-width="32"` dan warna `var(--road)` (navy aspal gelap).
- **Garis Marka Putus-Putus Tengah**:
  `d="M48 -80 L48 920"` dengan warna kuning/putih `var(--road-line)` (`stroke-width="2.5"` dan `stroke-dasharray="10 9"`).
- **Garis Gradien Progres Aktif (`#roadGradProgressPath`)**:
  `d="M48 -80 L48 920"` dengan gradien warna 4 tahap (`#e11d48`, `#6366f1`, `#0284c7`, `#eab308`).

### B. Simpul Checkpoint Sejajar pada Sumbu ($X = 48$)
Semua checkpoint berada persis di tengah jalur lurus ($X = 48$):
1. **Checkpoint 01 (Buat Akun & Profil)**:
   - Titik pusat: $(48, 105)$
   - Lingkaran halo $r=32$, lingkaran utama $r=24$ dengan border merah `#e11d48`.
   - Ikon UserPlus di tengah: `transform="translate(36, 93)"`.
2. **Checkpoint 02 (Pilih Bidang & Berkas)**:
   - Titik pusat: $(48, 315)$
   - Lingkaran utama $r=24$ dengan border ungu `#6366f1`.
   - Ikon FileCheck di tengah: `transform="translate(36, 303)"`.
3. **Checkpoint 03 (Pelaksanaan & Logbook)**:
   - Titik pusat: $(48, 525)$
   - Lingkaran utama $r=24$ dengan border biru `#0284c7`.
   - Ikon Briefcase di tengah: `transform="translate(36, 513)"`.
4. **Checkpoint 04 (Selesai Magang / Kelulusan)**:
   - Titik pusat: $(48, 735)$
   - Lingkaran utama $r=24$ dengan border kuning emas `#eab308`.
   - Ikon GraduationCap di tengah: `transform="translate(36, 723)"`.

### C. Penanda START & FINISH (Sesuai Gambar 2)
- **START (Bagian Atas)**:
  - Lingkaran hijau lembut berpusat di $(48, -60)$ dengan ikon bendera di dalamnya.
  - Teks tebal hijau `START` di samping kanan: `x="82" y="-55"`.
- **FINISH (Bagian Bawah)**:
  - Lingkaran biru gelap berpusat di $(48, 880)$ dengan ikon bendera kotak-kotak.
  - Teks tebal `FINISH` di samping kanan: `x="82" y="885"`.

### D. Animasi Mobil (#roadCarGroup)
- Karena path adalah garis lurus vertikal, mobil akan meluncur lurus ke bawah pada $X = 48$ menghadap tepat ke arah bawah (`rotate(90)`), mengikuti progres scroll pengguna.

---

## 5. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/blocks/tahapan.blade.php` | Modify | 1. Perbarui `d` atribut pada `#roadSvgPath`, `#roadGradProgressPath`, dan center dashed line menjadi garis lurus `M48 -80 L48 920`.<br>2. Pindahkan koordinat X checkpoint nodes dari 120 ke 48 (beserta translasi ikonnya).<br>3. Posisikan penanda START dan FINISH agar sejajar di sumbu $X=48$ dengan label teks di sampingnya persis Gambar 2. |
| `resources/css/pages/public/landing.css` | Modify | Sesuaikan padding kiri kartu `.tahapan-cards-list` di mobile agar kartu menempel rapi di sebelah kanan simpul lurus dengan jarak bernapas yang pas. |
| `public/css/landing-modern.css` | Modify | Sinkronisasi aturan CSS fallback. |
| `resources/js/pages/public/landing.js` | Modify | Pastikan perhitungan `initTahapanRoad()` membaca panjang garis lurus dan memperbarui progres mobil & checkpoint secara mulus. |

---

## 6. Langkah Implementasi Terstruktur

- [ ] **Langkah 1: Perbarui Geometri SVG di `tahapan.blade.php`**
  - [ ] Ubah `#roadSvgPath` dan `#roadGradProgressPath` menjadi `d="M48 -80 L48 920"`.
  - [ ] Ubah garis putus-putus tengah menjadi `d="M48 -80 L48 920"`.
  - [ ] Pindahkan 4 checkpoint nodes ke $X = 48$ (dengan translasi ikon $36 = 48 - 12$).
  - [ ] Pindahkan penanda START ke $X = 48$ dan FINISH ke $X = 48$.
- [ ] **Langkah 2: Penyesuaian Spacing CSS**
  - [ ] Di `landing.css`, atur padding kiri `.tahapan-cards-list` di mobile menjadi `padding: 40px 0 40px 105px !important;` agar kartu berada tepat di sebelah kanan jalur lurus tanpa ruang kosong berlebih.
- [ ] **Langkah 3: Sinkronisasi JavaScript `initTahapanRoad()`**
  - [ ] Verifikasi bahwa mobil meluncur di $X=48$ dengan sudut rotasi lurus ke bawah (`90deg`).
- [ ] **Langkah 4: Kompilasi & Quality Gate**
  - [ ] Kompilasi aset via Vite: `npm run build`.
  - [ ] Uji fungsional: `php artisan test --filter="LandingPageTest"`.
  - [ ] Uji gaya kode: `./vendor/bin/pint --test`.
- [ ] **Langkah 5: Verifikasi Visual Browser (CDP Headless Edge)**
  - [ ] Ambil tangkapan layar mobile ($390 \times 844\text{px}$) pada seksi alur tahapan untuk memastikan jalan lurus, ikon, START/FINISH, dan kartu tersusun presisi sesuai referensi Gambar 2.
  - [ ] Ambil tangkapan layar desktop ($1280 \times 800\text{px}$) untuk memastikan tampilan desktop tetap stabil dan indah.

---

## 7. Kriteria Selesai (Definition of Done)

- [ ] Lintasan jalan pada seksi tahapan berupa garis lurus vertikal (*straight road*) sesuai referensi Gambar 2.
- [ ] Simpul 4 checkpoint berada tepat di atas jalur jalan lurus pada sumbu yang sama ($X=48$).
- [ ] Penanda START di atas dan FINISH di bawah sejajar dengan jalur jalan lurus.
- [ ] Seluruh efek font, teks, warna kartu, dan animasi scroll mobil tetap berfungsi normal 100%.
- [ ] Seluruh automated test (`LandingPageTest` & `Pint`) lulus 100%.

