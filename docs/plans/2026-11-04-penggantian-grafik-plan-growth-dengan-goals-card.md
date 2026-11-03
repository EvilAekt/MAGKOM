# Rencana Kerja: Penggantian Grafik "Plan Growth" dengan Card Metrik "Goals" (Target & Capaian Kinerja)

- **Tanggal**: 2026-11-04
- **Status**: Selesai / Terverifikasi 100%
- **Dokumen Referensi**:
  - `resources/views/admin/laporan/tahunan.blade.php`
  - `resources/css/pages/admin/laporan-tahunan.css`
  - `app/Http/Controllers/Admin/AnalitikTahunanController.php`
  - `tests/Feature/AdminAnalitikTahunanVisualTest.php`
  - `.agents/rules/AGENTS.md`
  - Tangkapan Layar Pengguna:
    - *Gambar*: Card "Goals" dengan 5 indikator target, fraksi nilai `current / target`, dan progress bar warna-warni (`media_1793729747801_30b12c05.png`)

---

## 1. Tujuan

1. **Mengganti Card Grafik "Plan Growth"**: Menghapus kartu grafik kurva area bertingkat (*Plan Growth*) di kolom kanan dan menggantikannya dengan kartu **"Goals" (Target & Capaian Kinerja Magang {{ $tahun }})** yang terinspirasi langsung dari desain referensi pengguna.
2. **5 Indikator Capaian Kinerja Magang**:
   - 🟢 **Penyerapan Kuota Magang** (Teal `#14B8A6`): Realisasi peserta diterima dibandingkan total kuota dinas yang dialokasikan (`$totalDiterima / $totalKuota`).
   - 🟢 **Keterimaan Seleksi Pendaftar** (Green `#22C55E`): Peserta yang lolos verifikasi & diterima dibandingkan total pemohon berkas (`$totalDiterima / $totalPendaftaran`).
   - 🔵 **Penyelesaian Masa Magang** (Blue `#3B82F6`): Peserta yang berhasil menuntaskan masa magang (status selesai) dibandingkan peserta diterima (`$totalSelesai / $totalDiterima`).
   - 🟠 **Jangkauan Institusi Mitra** (Amber `#F59E0B`): Jumlah sekolah & perguruan tinggi mitra aktif dibandingkan target kemitraan tahunan (`count($topInstitusi) / $targetMitra`).
   - 🟣 **Cakupan Periode Aktif** (Purple `#A855F7`): Jumlah bulan magang berjalan yang terisi peserta dibandingkan 12 bulan kalender (`$bulanAktifCount / 12`).
3. **Format Fraksi & Visual Progress Bar Sesuai Desain**:
   - Menampilkan label indikator di kiri dan nilai fraksi `current / target` di kanan (contoh: `2 / 5` atau `87 / 100`).
   - Di bawah setiap indikator terdapat *horizontal rounded progress bar* dengan warna khas masing-masing indikator.
4. **Kepatuhan Penuh AGENTS.md**:
   - Tanpa CSS inline di Blade; seluruh styling kartu Goals dikelola di `resources/css/pages/admin/laporan-tahunan.css`.
   - Menggunakan pure CSS/Blade rendering tanpa overhead JavaScript runtime tambahan.
   - Mempertahankan integritas otorisasi SuperAdmin (Asumsi A-20).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Perhitungan Kuota & Target di Controller**:
   - Controller `AnalitikTahunanController` akan menghitung `$totalKuota` dari tabel `kuota` untuk tahun bersangkutan. Jika total kuota belum diset atau bernilai 0, fallback anggun ke `max($totalDiterima, 1)`.
   - Target kemitraan institusi ditetapkan pada angka standar `10` institusi per tahun (atau sebesar jumlah institusi aktual jika lebih besar dari 10).
   - Periode aktif dihitung dari jumlah bulan dalam setahun yang memiliki data pendaftar atau peserta diterima (`$bulanAktifCount / 12`).
2. **Letak Posisi pada Grid 2-Kolom**:
   - Tetap berada di kolom kanan dari `.tahunan-mid-grid` berdampingan dengan Card Tabel Tren Bulanan di kolom kiri, sehingga tata letak tetap simetris, kompak, dan sejajar.
3. **Dukungan Tema Terang & Gelap**:
   - Komponen Goals mendukung Light Mode (card putih bersih elegan) dan Dark Mode (dark navy/slate `#182234` seperti pada tangkapan layar referensi) secara otomatis.

### Pertanyaan Terbuka:
- Tidak ada pertanyaan pemblokir kritis. Desain visual dan indikator kinerja sudah terpetakan secara presisi dari tangkapan layar pengguna.

---

## 3. Pendekatan Teknis

### Perbandingan Opsi Arsitektur:

| Kriteria | Opsi 1: Pure Blade + Scoped CSS di `laporan-tahunan.css` (Direkomendasikan) | Opsi 2: Canvas / JavaScript Bar Generator |
| :--- | :--- | :--- |
| **Kepatuhan AGENTS.md** | **100% Patuh**: Styling terpusat di `resources/css/`, markup bersih di Blade, zero overhead. | Lebih kompleks, memerlukan modul JS terpisah untuk animasi bar. |
| **Kecepatan Render & SEO** | Instan (SSR), tanpa delay kompilasi atau inisialisasi JS di browser. | Bergantung pada lifecycle execution JavaScript. |
| **Kemudahan Pemeliharaan** | Sangat mudah dipelihara dan di-styling dengan CSS custom properties. | Memerlukan sinkronisasi DOM dan event resize. |
| **Rekomendasi** | **SANGAT DIREKOMENDASIKAN (Opsi 1)** | Tidak direkomendasikan |

### Detail Desain Komponen Goals:

#### Struktur Markup (Blade):
```blade
<div class="tahunan-section-card goals-card">
    <div class="goals-header">
        <h3 class="tahunan-section-title">Goals</h3>
        <span class="badge badge-info" style="font-size: 0.6875rem;">Target &amp; Capaian {{ $tahun }}</span>
    </div>

    <div class="goals-list">
        <!-- Item 1: Penyerapan Kuota (Teal) -->
        <div class="goal-item">
            <div class="goal-item-top">
                <span class="goal-label">Penyerapan Kuota Magang</span>
                <span class="goal-value">{{ $totalDiterima }} / {{ $targetKuota }}</span>
            </div>
            <div class="goal-bar-track">
                <div class="goal-bar-fill goal-teal" style="width: {{ $pctKuota }}%;"></div>
            </div>
        </div>

        <!-- Item 2: Kelulusan Seleksi (Green) -->
        <div class="goal-item">
            <div class="goal-item-top">
                <span class="goal-label">Kelulusan Seleksi Pendaftar</span>
                <span class="goal-value">{{ $totalDiterima }} / {{ $targetPendaftar }}</span>
            </div>
            <div class="goal-bar-track">
                <div class="goal-bar-fill goal-green" style="width: {{ $pctSeleksi }}%;"></div>
            </div>
        </div>

        <!-- Item 3: Penyelesaian Masa Magang (Blue) -->
        <div class="goal-item">
            <div class="goal-item-top">
                <span class="goal-label">Penyelesaian Masa Magang</span>
                <span class="goal-value">{{ $totalSelesai }} / {{ $targetSelesai }}</span>
            </div>
            <div class="goal-bar-track">
                <div class="goal-bar-fill goal-blue" style="width: {{ $pctSelesai }}%;"></div>
            </div>
        </div>

        <!-- Item 4: Jangkauan Institusi Mitra (Amber) -->
        <div class="goal-item">
            <div class="goal-item-top">
                <span class="goal-label">Jangkauan Institusi Mitra</span>
                <span class="goal-value">{{ $totalMitra }} / {{ $targetMitra }}</span>
            </div>
            <div class="goal-bar-track">
                <div class="goal-bar-fill goal-amber" style="width: {{ $pctMitra }}%;"></div>
            </div>
        </div>

        <!-- Item 5: Cakupan Periode Aktif (Purple) -->
        <div class="goal-item">
            <div class="goal-item-top">
                <span class="goal-label">Cakupan Periode Aktif</span>
                <span class="goal-value">{{ $bulanAktifCount }} / 12</span>
            </div>
            <div class="goal-bar-track">
                <div class="goal-bar-fill goal-purple" style="width: {{ $pctBulan }}%;"></div>
            </div>
        </div>
    </div>
</div>
```

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `app/Http/Controllers/Admin/AnalitikTahunanController.php` | **Modify** | Menambahkan kalkulasi kuota tahunan (`$totalKuota`), target kemitraan, dan jumlah bulan aktif ke dalam kembalian data tahunan. |
| `resources/views/admin/laporan/tahunan.blade.php` | **Modify** | Mengganti kontainer `.tahunan-chart-card` (Plan Growth) dengan kartu `.goals-card` lengkap dengan 5 indikator progress bar. |
| `resources/css/pages/admin/laporan-tahunan.css` | **Modify** | Menambahkan styling komponen `.goals-card`, `.goal-item`, `.goal-bar-track`, `.goal-bar-fill`, dan 5 varian warna (teal, green, blue, amber, purple) serta dukungan dark mode. |
| `tests/Feature/AdminAnalitikTahunanVisualTest.php` | **Modify** | Menyesuaikan asersi pengujian untuk memeriksa keberadaan elemen `.goals-card` dan 5 item goal target. |

---

## 5. Langkah Implementasi

### Tahap 1: Backend Controller (`AnalitikTahunanController.php`)
- [x] **Langkah 1**: Pada method `hitungMetrikTahunan`, tambahkan kalkulasi `$totalKuota` dari model `Kuota` untuk tahun kalender aktif, serta `$bulanAktifCount` dari array bulanan.
- [x] **Langkah 2**: Lewatkan variabel data tambahan tersebut ke view.

### Tahap 2: Styling CSS (`resources/css/pages/admin/laporan-tahunan.css`)
- [x] **Langkah 3**: Buat class styling `.goals-card`, `.goals-header`, `.goals-list`, `.goal-item`, `.goal-item-top`, `.goal-label`, `.goal-value`.
- [x] **Langkah 4**: Buat styling progress bar `.goal-bar-track` (rounded pill) dan varian warna `.goal-teal`, `.goal-green`, `.goal-blue`, `.goal-amber`, `.goal-purple`.
- [x] **Langkah 5**: Tambahkan dukungan warna gelap (*dark mode*) untuk `.goals-card` sesuai tampilan pada tangkapan layar.

### Tahap 3: Blade Markup (`tahunan.blade.php`)
- [x] **Langkah 6**: Ganti markup kolom kanan dari kartu grafik Plan Growth menjadi kartu `.goals-card` dengan 5 baris indikator kinerja.
- [x] **Langkah 7**: Pastikan tidak ada script atau inline style terlarang yang disisipkan.

### Tahap 4: Build Asset, Linting & Verifikasi Pengujian
- [x] **Langkah 8**: Jalankan `npm run build` untuk mengompilasi CSS Vite.
- [x] **Langkah 9**: Jalankan `./vendor/bin/pint --test` untuk verifikasi formatting PHP.
- [x] **Langkah 10**: Perbarui dan jalankan `tests/Feature/AdminAnalitikTahunanVisualTest.php` serta `AdvancedAdminAndSuperAdminPhaseFiveTest.php` dengan `./vendor/bin/phpunit`.

---

## 6. Strategi Testing

1. **Uji Otomatis (PHPUnit)**:
   - Verifikasi respons HTTP 200 untuk peran Admin dan SuperAdmin.
   - Verifikasi keberadaan komponen `.goals-card`.
   - Verifikasi kehadiran 5 indikator target: Penyerapan Kuota Magang, Kelulusan Seleksi Pendaftar, Penyelesaian Masa Magang, Jangkauan Institusi Mitra, Cakupan Periode Aktif.
   - Verifikasi kehadiran fraksi nilai target (`/`).
   - Verifikasi batasan keamanan A-20 tetap aman.
2. **Uji Visual**:
   - Memastikan tampilan kartu Goals di kolom kanan berdampingan dengan tabel bulanan di kolom kiri secara seimbang.
   - Memastikan progress bar memiliki 5 warna berbeda (teal, green, blue, amber, purple) persis seperti tangkapan layar referensi.

---

## 7. Risiko & Mitigasi

- **Risiko: Total Kuota bernilai 0 jika tahun kalender baru belum diatur kuotanya**:
  - *Mitigasi*: Menggunakan operator fallback `max($totalKuota, $totalDiterima, 1)` sehingga pembagian tidak menghasilkan *division by zero* dan progress bar tetap ter-render aman.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Kartu grafik Plan Growth berhasil digantikan oleh kartu **Goals**.
- [x] Terdapat 5 indikator capaian kinerja magang dengan fraksi `current / target` dan progress bar warna-warni (teal, green, blue, amber, purple).
- [x] Tata letak berdampingan dengan tabel tren bulanan tetap rapi dan responsif.
- [x] Kepatuhan AGENTS.md terpenuhi 100% (CSS di `resources/css/`, tanpa inline style).
- [x] Kompilasi aset Vite berhasil (`npm run build`).
- [x] Seluruh unit dan feature test PHPUnit lulus 100% (Hijau).

