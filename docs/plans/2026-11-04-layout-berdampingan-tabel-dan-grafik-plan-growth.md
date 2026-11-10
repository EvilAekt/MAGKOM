# Rencana Kerja: Penataan Layout Berdampingan Card Tabel Tren Bulanan dan Card Grafik "Plan Growth"

- **Tanggal**: 2026-11-04
- **Status**: Selesai / Terverifikasi 100%
- **Dokumen Referensi**:
  - `resources/views/admin/laporan/tahunan.blade.php`
  - `resources/css/pages/admin/laporan-tahunan.css`
  - `resources/js/components/plan-growth-chart.js`
  - `.agents/rules/AGENTS.md`
  - Permintaan Pengguna:
    - *"card grafik Grafik Pertumbuhan & Penyerapan Magang 2026 di taruh di samping kanan card Tren Penyerapan Bulanan (Januari - Desember 2026), seperti awal asli tadi bedanya cuma card nya terpisah"*

---

## 1. Tujuan

1. **Tata Letak Berdampingan (Side-by-Side 2-Column Grid)**: Menyusun bagian tren bulanan menjadi grid 2 kolom yang seimbang dan simetris:
   - **Kolom Kiri**: Card khusus **Tren Penyerapan Bulanan (Januari - Desember {{ $tahun }})** yang menampilkan rincian tabel tabular 5 kolom bersih (`Bulan`, `Pendaftar`, `Diterima`, `Lulus/Selesai`, `Rasio Penerimaan`).
   - **Kolom Kanan**: Card khusus **Grafik Pertumbuhan & Penyerapan Magang {{ $tahun }}** bergaya *Plan Growth* (kurva area bertingkat SVG multi-series).
2. **Proporsi Grafik Ideal Sesuai Referensi**: Karena berada di kolom kanan (~500px - 620px), grafik tidak lagi membentang gepeng di layar lebar (1200px+), melainkan memiliki rasio aspek dinamis (~16:10) yang padat, presisi, dan serupa dengan estetika referensi awal *Plan Growth*.
3. **Harmonisasi Simetris Dashboard**: Menghasilkan tata letak dashboard yang konsisten dan rapi dari atas ke bawah:
   - Baris 1: Toolbar Filter & Aksi Navigasi (Full Width)
   - Baris 2: 4 Hero KPI Cards (4 Kolom)
   - Baris 3: Tren & Grafik Magang (2 Kolom: Kiri Tabel Data, Kanan Grafik Plan Growth)
   - Baris 4: Mitra & Distribusi Jenjang/Mutu (2 Kolom: Kiri Top 10 Institusi, Kanan Jenjang & Mutu A-20)
4. **Kepatuhan Penuh AGENTS.md**: Mempertahankan Vanilla JS ES Module, CSS di `resources/css/`, keamanan Asumsi A-20, dan suite tes PHPUnit.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Rasio Pembagian Kolom Desktop**: Pada layar desktop lebar (`min-width: 1025px`), grid menggunakan pembagian proporsional `1fr 1fr` (50% : 50%) atau `1.05fr 0.95fr` dengan gap `1.5rem`, sehingga tabel bulanan dan kartu grafik memiliki tinggi visual yang harmonis.
2. **Responsivitas Layar Sempit (Tablet / Mobile)**: Pada breakpoint tablet dan mobile (`max-width: 1024px`), grid secara otomatis *collapse* menjadi 1 kolom vertikal (Card Tabel di atas, Card Grafik di bawahnya) agar tidak terjadi pemotongan teks maupun scroll horizontal sempit.
3. **Dimensi Rendering SVG Chart**: Dimensi kalkulasi SVG di `plan-growth-chart.js` disesuaikan untuk kontainer kolom kanan (`width: 580px, height: 320px`), dengan kurva kubik Bézier yang tegas, sumbu X 12 bulan terbaca jelas, dan interaktivitas hover tooltip tetap responsif.
4. **Pembersihan Kolom Lama**: Kolom `Grafik Proporsi` lama pada tabel tetap dihapus sesuai keputusan sebelumnya, digantikan sepenuhnya oleh visualisasi grafik pada kartu kanan.

### Pertanyaan Terbuka:
- Tidak ada pertanyaan kritis yang menghambat. Konsep tata letak berdampingan sudah didefinisikan dengan sangat spesifik oleh pengguna.

---

## 3. Pendekatan Teknis

### Perbandingan Opsi Arsitektur:

| Kriteria | Opsi 1: CSS Grid 2-Kolom Terdedikasi `.tahunan-mid-grid` (Direkomendasikan) | Opsi 2: Flexbox dengan inline width / float |
| :--- | :--- | :--- |
| **Kerapian & Kesejajaran** | **Sempurna**: Menggunakan `display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: stretch;` sehingga kedua kartu otomatis memiliki tinggi yang serasi. | Rentan tidak rata tingginya (*mismatched height*) jika isi konten tabel lebih tinggi dari grafik. |
| **Kepatuhan AGENTS.md** | **100% Patuh**: Semua aturan layout didefinisikan di `resources/css/pages/admin/laporan-tahunan.css` tanpa inline style di Blade. | Melanggar jika menggunakan inline CSS pada elemen Blade. |
| **Responsivitas Mobile** | Otomatis berubah menjadi `grid-template-columns: 1fr;` pada media query `@media (max-width: 1024px)`. | Memerlukan override utility class tambahan. |
| **Rekomendasi** | **SANGAT DIREKOMENDASIKAN (Opsi 1)** | Tidak direkomendasikan |

### Detail Perubahan Desain:

#### A. Kontainer Grid Baru (`.tahunan-mid-grid`)
```css
.tahunan-mid-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    align-items: stretch;
}

@media (max-width: 1024px) {
    .tahunan-mid-grid {
        grid-template-columns: 1fr;
    }
}
```

#### B. Kolom Kiri: Card Tabel Tren Penyerapan Bulanan
- Menggunakan `div.tahunan-section-card.tahunan-table-card`.
- Header: `Tren Penyerapan Bulanan (Januari - Desember {{ $tahun }})`, deskripsi ringkas, dan badge `12 Bulan Terpantau`.
- Tabel data bersih 5 kolom (`Bulan`, `Pendaftar`, `Diterima`, `Lulus / Selesai`, `Rasio Penerimaan`, plus opsional `Rata-rata Nilai` jika SuperAdmin).
- Padding tabel proporsional (`0.55rem 0.75rem`), font tabular tajam, scrollbar tipis jika dibuka di viewport kecil.

#### C. Kolom Kanan: Card Grafik Pertumbuhan & Penyerapan Magang (Plan Growth)
- Menggunakan `div.tahunan-section-card.tahunan-chart-card`.
- Header: Judul `Grafik Pertumbuhan & Penyerapan Magang {{ $tahun }}`, subtitle `Stacked area • Pendaftar • Diterima • Alumni / Lulus`, badge `Plan growth`.
- Kontainer SVG kurva area multi-series dengan tinggi proporsional `300px - 320px`.
- Legend di bagian bawah kurva: Pendaftar Masuk (Hijau), Peserta Diterima (Biru), Alumni Selesai (Amber).
- Tooltip interaktif saat hover tetap aktif dan presisi.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/admin/laporan/tahunan.blade.php` | **Modify** | Membungkus Card Tabel (kiri) dan Card Grafik Plan Growth (kanan) dalam container grid `.tahunan-mid-grid`. |
| `resources/css/pages/admin/laporan-tahunan.css` | **Modify** | Menambahkan definisi `.tahunan-mid-grid`, styling `.tahunan-table-card`, `.tahunan-chart-card`, dan penyesuaian tinggi kontainer SVG untuk layout kolom samping. |
| `resources/js/components/plan-growth-chart.js` | **Modify** | Menyesuaikan ukuran render viewBox default SVG (`width: 600, height: 300`) agar kurva ter-render tajam dan dinamis di kolom kanan. |
| `tests/Feature/AdminAnalitikTahunanVisualTest.php` | **Modify** | Menambahkan asersi verifikasi untuk container `.tahunan-mid-grid` dan posisi berdampingan. |

---

## 5. Langkah Implementasi

### Tahap 1: Restrukturisasi Layout Blade (`tahunan.blade.php`)
- [x] **Langkah 1**: Buat kontainer `<div class="tahunan-mid-grid">` di bawah baris 4 kartu KPI.
- [x] **Langkah 2**: Tempatkan Card Tabel Tren Penyerapan Bulanan di urutan pertama (kolom kiri).
- [x] **Langkah 3**: Tempatkan Card Grafik Pertumbuhan & Penyerapan Magang (*Plan Growth*) di urutan kedua (kolom kanan).
- [x] **Langkah 4**: Pastikan tidak ada inline style baru yang masuk ke Blade.

### Tahap 2: Styling CSS & Dimensi JS
- [x] **Langkah 5**: Tambahkan rule `.tahunan-mid-grid` di `resources/css/pages/admin/laporan-tahunan.css` lengkap dengan breakpoint responsif `@media (max-width: 1024px)`.
- [x] **Langkah 6**: Sesuaikan styling `.plan-growth-wrapper` di dalam card kanan agar memenuhi tinggi kartu dengan padding seimbang.
- [x] **Langkah 7**: Sesuaikan dimensi rendering SVG di `resources/js/components/plan-growth-chart.js` menjadi `width: 600` dan `height: 300` agar kurva naik-turun terlihat anggun dan tidak gepeng.

### Tahap 3: Build Asset, Linting & Verifikasi Pengujian
- [x] **Langkah 8**: Jalankan `npm run build` untuk mengompilasi CSS dan JS Vite.
- [x] **Langkah 9**: Jalankan `./vendor/bin/pint --test` untuk verifikasi kerapian kode PHP.
- [x] **Langkah 10**: Perbarui dan jalankan `tests/Feature/AdminAnalitikTahunanVisualTest.php` dan `AdvancedAdminAndSuperAdminPhaseFiveTest.php` dengan `./vendor/bin/phpunit`.

---

## 6. Strategi Testing

1. **Uji Otomatis (PHPUnit)**:
   - Verifikasi respons HTTP 200 untuk Admin dan SuperAdmin.
   - Verifikasi keberadaan kontainer `.tahunan-mid-grid`.
   - Verifikasi Card Tabel dan Card Grafik hadir berdampingan di dalam grid.
   - Verifikasi batasan keamanan A-20 tetap aktif (Admin tidak melihat mutu nilai).
2. **Uji Tampilan & Responsivitas**:
   - Tampilan Desktop (>1024px): 2 kolom berdampingan (Kiri: Tabel, Kanan: Grafik).
   - Tampilan Mobile/Tablet (≤1024px): Menumpuk vertikal 1 kolom secara elegan.
   - Uji tema terang (*Light Mode*) dan gelap (*Dark Mode*).

---

## 7. Risiko & Mitigasi

- **Risiko 1: Ruang horizontal tabel bulanan sempit di desktop resolusi 1024px-1200px**:
  - *Mitigasi*: Menyesuaikan padding cell tabel (`padding: 0.5rem 0.625rem`) dan font size (`0.8125rem`) serta membungkus tabel dalam `.clean-scrollbar` overflow-x auto sebagai pengaman.
- **Risiko 2: Tooltip hover grafik terpotong di tepi kanan layar**:
  - *Mitigasi*: Script `plan-growth-chart.js` telah menghitung bounding rect container, dan kita akan tambahkan logic pengaman offset tooltip jika mendekati tepi kanan (`screenX > plotWidth - 100`).

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Card Tabel dan Card Grafik Plan Growth tertata rapi berdampingan (kiri: tabel data, kanan: grafik kurva) di layar desktop.
- [x] Grafik Plan Growth memiliki proporsi kurva yang padat dan tidak melar.
- [x] Tata letak responsif runtuh (*collapse*) secara mulus menjadi 1 kolom di layar tablet/mobile.
- [x] Kepatuhan AGENTS.md terpenuhi 100% (tanpa CSS/JS inline di Blade).
- [x] Asset Vite sukses ter-build (`npm run build`).
- [x] Seluruh unit/feature test PHPUnit lulus 100% (`OK`).

