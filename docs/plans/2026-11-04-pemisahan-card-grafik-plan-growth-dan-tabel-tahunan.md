# Rencana Kerja: Pemisahan Card Grafik "Plan Growth" dan Tabel Tren Bulanan

- **Tanggal**: 2026-11-04
- **Status**: Selesai / Terverifikasi 100%
- **Dokumen Referensi**:
  - `resources/views/admin/laporan/tahunan.blade.php`
  - `resources/css/pages/admin/laporan-tahunan.css`
  - `resources/js/components/plan-growth-chart.js`
  - `.agents/rules/AGENTS.md`
  - Tangkapan Layar Pengguna:
    - *Gambar 1*: Kondisi grafik saat ini yang membentang terlalu panjang/gepeng di atas tabel (`media_1793727223760_04965c46.png`)
    - *Gambar 2*: Rujukan tampilan tabel bulanan bersih tanpa kolom proporsi (`media_1793727362006_3ca58a2f.png`)

---

## 1. Tujuan

1. **Memisahkan Card Grafik dan Card Tabel**: Memecah kontainer gabungan saat ini menjadi dua kartu (*card*) terpisah:
   - **Card 1**: Kartu khusus terdedikasi untuk visualisasi **Grafik Pertumbuhan & Penyerapan (Plan Growth)**.
   - **Card 2**: Kartu khusus terdedikasi untuk **Tabel Rincian Data Bulanan** (seperti referensi pada tangkapan layar ke-2).
2. **Menghilangkan Kolom Grafik Proporsi Lama pada Tabel**: Menghapus kolom `Grafik Proporsi` dan elemen *bar* proporsi dari tabel bulanan karena fungsinya telah digantikan sepenuhnya oleh grafik kurva *Plan Growth*.
3. **Mengoptimalkan Proporsi Grafik (Tidak Terlalu Panjang / Gepeng)**: Menyesuaikan rasio aspek dan dimensi kontainer grafik *Plan Growth* agar tidak membentang 100% secara berlebihan di layar lebar desktop, sehingga kurva fluktuasi bulanan tampak dinamis, bernafas, dan proporsional sesuai estetika referensi awal.
4. **Kepatuhan Penuh AGENTS.md**: Mempertahankan integritas otorisasi SuperAdmin (Asumsi A-20), pipeline Vite, modularitas Vanilla JS/CSS di `resources/`, dan verifikasi pengujian otomatis.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Pemisahan Semantik**: Card Grafik *Plan Growth* akan ditempatkan di atas Card Tabel Bulanan dengan margin vertikal standar (`1.5rem`), sehingga keduanya memiliki hirarki visual independen dengan judul dan deskripsi yang jelas.
2. **Proporsi Aspek Rasio Grafik**: Agar grafik tidak membentang melar/gepeng di layar monitor resolusi tinggi (>1200px), lebar kontainer grafik akan diberi pembatas proporsional (*max-width* responsif atau rasio aspek vertikal lebih seimbang dengan tinggi 280–300px), sehingga lekukan kurva Bézier tampak tegas dan jelas.
3. **Kolom Tabel Standar**: Tabel bulanan pada Card 2 akan menampilkan 5 kolom bersih sesuai rujukan gambar 2:
   - `BULAN`
   - `PENDAFTAR`
   - `DITERIMA`
   - `LULUS / SELESAI`
   - `RASIO PENERIMAAN`
   *(Kolom `RATA-RATA NILAI` tetap muncul bersyarat untuk SuperAdmin sesuai aturan A-20).*

### Pertanyaan Terbuka (Opsional):
- Tidak ada pertanyaan pemblokir kritis. Rencana ini langsung merespons permintaan spesifik pemisahan kartu dan penghapusan kolom proporsi lama.

---

## 3. Pendekatan Teknis

### Perbandingan Opsi Arsitektur:

| Kriteria | Opsi 1: Card Terpisah Vertikal dengan Aspek Rasio Terkendali (Direkomendasikan) | Opsi 2: Layout Grid 2 Kolom (Grafik di Samping Tabel) |
| :--- | :--- | :--- |
| **Tata Letak** | Card Grafik *Plan Growth* berdiri sendiri di atas Card Tabel dengan batasan *max-width* proporsional dan tinggi kurva 280–300px. | Card Grafik ditaruh di kolom kiri (~50%) dan Tabel Bulanan ditaruh di kolom kanan (~50%). |
| **Keterbacaan Tabel** | **Sangat Baik**: Tabel 5 kolom memiliki ruang horizontal yang lega untuk 12 baris bulan dan angka metrik. | **Kurang Baik**: Tabel 12 baris tertekan dalam 500px, berisiko terpotong (*text overflow*) atau memerlukan scroll horizontal sempit. |
| **Proporsi Grafik** | **Ideal**: Lebar grafik terkontrol dan tinggi proporsional, kurva terlihat halus dan tidak melar/gepeng. | Cukup baik, namun kurva menjadi sangat padat karena lebar hanya 500px. |
| **Dukungan Responsif** | Sempurna di desktop, tablet, maupun layar mobile (fleksibel alami). | Memerlukan breakpoint kompleks untuk *collapse* menjadi 1 kolom di layar tablet. |
| **Rekomendasi** | **SANGAT DIREKOMENDASIKAN (Opsi 1)** | Kurang direkomendasikan |

### Detail Perubahan Desain:

#### A. Card 1: Grafik Pertumbuhan & Penyerapan Magang (Plan Growth Card)
- Dibuat sebagai `div.tahunan-section-card` mandiri yang elegan.
- **Header Card**: Memuat judul `Grafik Pertumbuhan & Penyerapan Magang {{ $tahun }}`, subjudul `Stacked area • Pendaftar • Diterima • Alumni / Lulus`, serta pill badge `Plan growth`.
- **Dimensi & Kurva Proporsional**:
  - Mengubah dimensi rendering SVG di `plan-growth-chart.js` dari `width: 860, height: 210` menjadi `width: 900, height: 280` dengan padding yang lebih proporsional.
  - Membatasi lebar kontainer grafik agar tidak membentang ekstrem (`max-width: 1040px; margin: 0 auto;`), sehingga di monitor ultrawide grafik tetap tampak kokoh, padat, dan seimbang.
- **Legend Card**: Penanda warna pendaftar, diterima, dan alumni tetap berada di bawah kurva dengan susunan rapi terpusat.

#### B. Card 2: Tabel Tren Penyerapan Bulanan (Clean Tabular Card)
- Dibuat sebagai `div.tahunan-section-card` mandiri terpisah.
- **Header Card**: Memuat judul `Tren Penyerapan Bulanan (Januari - Desember {{ $tahun }})`, deskripsi perbandingan volume, dan badge `12 Bulan Terpantau`.
- **Penghapusan Kolom Proporsi**:
  - Menghapus elemen `<th>Grafik Proporsi</th>` pada thead.
  - Menghapus elemen `<td><div class="proportional-bar-wrapper">...</div></td>` pada setiap baris tbody.
- **Tampilan Bersih**: Menghasilkan tabel data yang rapi dan terstruktur persis seperti pada tangkapan layar ke-2 yang dikirimkan pengguna.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/admin/laporan/tahunan.blade.php` | **Modify** | Memisahkan kontainer grafik dan tabel menjadi 2 section card terpisah; menghapus kolom `Grafik Proporsi` dan elemen segmented bar dari tabel. |
| `resources/css/pages/admin/laporan-tahunan.css` | **Modify** | Menyesuaikan styling `.plan-growth-card-container` dengan max-width dan aspect ratio seimbang; membersihkan rule CSS proporsional bar yang tidak terpakai. |
| `resources/js/components/plan-growth-chart.js` | **Modify** | Menyesuaikan parameter kalkulasi SVG (tinggi render dinaikkan menjadi 280px agar kurva dinamis dan tidak gepeng). |
| `tests/Feature/AdminAnalitikTahunanVisualTest.php` | **Modify** | Memperbarui asersi pengujian untuk memastikan kedua card terpisah hadir dan kolom tabel terverifikasi bersih. |

---

## 5. Langkah Implementasi

### Tahap 1: Restrukturisasi Markup Blade (`tahunan.blade.php`)
- [x] **Langkah 1**: Buat kontainer `<div class="tahunan-section-card plan-growth-section">` khusus untuk komponen grafik *Plan Growth* (memindahkan wrapper grafik ke luar card tabel).
- [x] **Langkah 2**: Buat kontainer `<div class="tahunan-section-card tabel-bulanan-section">` mandiri khusus untuk tabel data tren bulanan.
- [x] **Langkah 3**: Hapus kolom `<th>Grafik Proporsi</th>` pada header tabel serta seluruh blok `<td><div class="proportional-bar-wrapper">...</div></td>` pada isi tabel.
- [x] **Langkah 4**: Pastikan kolom `Rata-rata Nilai` dengan guard `@if($canViewNilai)` untuk Asumsi A-20 tetap berada di posisi yang benar.

### Tahap 2: Penyesuaian Dimensi & Styling (CSS & JS)
- [x] **Langkah 5**: Perbarui `resources/js/components/plan-growth-chart.js` agar tinggi plot area SVG ditingkatkan dari 210px ke 280px untuk amplitudo kurva yang lebih tegas dan elegan.
- [x] **Langkah 6**: Perbarui `resources/css/pages/admin/laporan-tahunan.css` untuk mengatur proporsi lebar card grafik (`max-width: 1040px; margin: 0 auto;`) dan merapikan spasi antar card.

### Tahap 3: Verifikasi, Build & Quality Gate
- [x] **Langkah 7**: Jalankan `npm run build` untuk mengompilasi bundel Vite.
- [x] **Langkah 8**: Jalankan `./vendor/bin/pint --test` untuk verifikasi kerapian kode.
- [x] **Langkah 9**: Perbarui dan jalankan `tests/Feature/AdminAnalitikTahunanVisualTest.php` serta suite regresi `AdvancedAdminAndSuperAdminPhaseFiveTest.php` dengan `./vendor/bin/phpunit`.

---

## 6. Strategi Testing

1. **Uji Otomatis (PHPUnit)**:
   - Verifikasi respons HTTP 200 untuk peran Admin dan SuperAdmin.
   - Verifikasi keberadaan kontainer grafik Plan Growth pada card tersendiri.
   - Verifikasi bahwa teks `Grafik Proporsi` tidak lagi muncul di header tabel bulanan.
   - Verifikasi bahwa seluruh data bulanan (Januari - Desember) tetap tampil lengkap.
   - Verifikasi bahwa batasan keamanan Asumsi A-20 tetap bekerja (admin tidak dapat melihat nilai).
2. **Uji Visual**:
   - Memastikan Card Grafik dan Card Tabel tampil terpisah secara visual dengan rapi.
   - Memastikan bentuk grafik kurva *Plan Growth* tidak lagi melar horizontal (lebih padat dan proporsional).

---

## 7. Risiko & Mitigasi

- **Risiko 1: Tampilan responsif di layar sempit (Mobile/Tablet)**:
  - *Mitigasi*: Penggunaan `max-width: 1040px; width: 100%;` dan SVG `viewBox` adaptif memastikan grafik tetap menyusut secara elegan di layar ponsel tanpa scroll horizontal.
- **Risiko 2: Kerusakan Kontrak Data Controller**:
  - *Mitigasi*: Perubahan murni pada level presentasi (Blade markup, CSS, dan kalkulasi dimensi SVG JS), tanpa mengubah struktur array `$trenBulanan` dari controller.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Card Grafik *Plan Growth* berdiri sendiri terpisah dari Card Tabel Data Bulanan.
- [x] Grafik kurva tidak lagi melar/gepeng berlebihan (proporsi visual seimbang).
- [x] Kolom grafik proporsi lama pada tabel bulanan telah dihapus sepenuhnya (sesuai rujukan gambar kedua).
- [x] Tabel tren bulanan tampil bersih dan elegan dengan 5 kolom standar.
- [x] Tidak ada CSS atau JS inline baru di Blade (`AGENTS.md` dipatuhi 100%).
- [x] Asset Vite sukses di-build (`npm run build`).
- [x] Pengujian otomatis PHPUnit lulus 100% (`OK`).

