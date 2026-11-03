# Rencana Kerja: Standardisasi Penamaan Istilah "Selesai" (Menghilangkan Istilah "Lulus" dan "Alumni")

- **Tanggal**: 2026-11-04
- **Status**: Selesai / Terverifikasi 100%
- **Dokumen Referensi**:
  - `resources/views/admin/laporan/tahunan.blade.php`
  - `resources/js/components/plan-growth-chart.js`
  - `tests/Feature/AdminAnalitikTahunanVisualTest.php`
  - `.agents/rules/AGENTS.md`
  - Tangkapan Layar Pengguna:
    - *Gambar 1*: Header kolom tabel bertuliskan `LULUS / SELESAI` (`media_1793728862293_f94f65c5.png`)
    - *Gambar 2*: Label legend grafik bertuliskan `Alumni Selesai` (`media_1793728878163_572ed724.png`)

---

## 1. Tujuan

Melakukan standardisasi istilah pada seluruh antarmuka **Dashboard Analitik Rekapitulasi Tahunan** (`admin.laporan.tahunan`):
1. **Header Kolom Tabel**: Mengubah teks `LULUS / SELESAI` menjadi **`Selesai`**.
2. **Legend Grafik "Plan Growth"**: Mengubah teks `Alumni Selesai` menjadi **`Selesai`** (atau `Peserta Selesai`).
3. **Hero KPI Card Metrik**: Menyelaraskan kartu ketiga yang sebelumnya bertuliskan `Alumni Magang Lulus` menjadi **`Peserta Selesai Magang`** (atau `Total Selesai Magang`), badge `...% Selesai`, dan sublabel `Tingkat Penyelesaian: ...%`.
4. **Subtitle & Deskripsi Bagian**: Menyelaraskan teks deskripsi `Stacked area • Pendaftar • Diterima • Alumni / Lulus` menjadi `Stacked area • Pendaftar • Diterima • Selesai`.
5. **Kepatuhan AGENTS.md**: Memastikan seluruh tes feature dan assertions PHPUnit diperbarui dan tetap hijau 100%.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Penyeragaman Terminologi**: Istilah yang digunakan seragam adalah kata tunggal **"Selesai"** (untuk label ringkas pada tabel dan legend grafik) dan **"Peserta Selesai Magang"** (untuk kartu KPI utama agar tetap informatif dan berimbang dengan kartu lain seperti "Total Pendaftar Masuk" dan "Peserta Diterima Magang").
2. **Zero Breaking Changes pada Logic/Backend**: Perubahan ini murni pada lapisan presentasi (Blade text, legend label di Blade/JS, dan test assertion). Kontrak data backend (`$b['selesai']`, `$completionRate`, dsb.) tidak diubah sama sekali.

### Pertanyaan Terbuka:
- Tidak ada pertanyaan yang menghambat; instruksi pengguna sangat jelas untuk menghilangkan kata "lulus" dan "alumni" dan menggantikannya dengan "selesai".

---

## 3. Pendekatan Teknis

### Perbandingan Opsi Penamaan:

| Lokasi Elemen | Kondisi Saat Ini | Opsi 1: Selesai Bersih & Konsisten (Direkomendasikan) | Opsi 2: Hanya Ubah yang di Screenshot |
| :--- | :--- | :--- | :--- |
| **Header Kolom Tabel** | `LULUS / SELESAI` | **`Selesai`** | `Selesai` |
| **Legend Grafik Plan Growth** | `Alumni Selesai` | **`Selesai`** | `Selesai` |
| **Hero KPI Card 3** | `Alumni Magang Lulus` | **`Peserta Selesai Magang`** | Dibiarkan `Alumni Magang Lulus` (inkonsisten) |
| **KPI Badge & Subtext** | `...% Lulus`, `Tingkat Kelulusan:` | **`...% Selesai`**, **`Tingkat Penyelesaian:`** | Dibiarkan (inkonsisten) |
| **Subtitle Plan Growth** | `... • Alumni / Lulus` | **`... • Selesai`** | Dibiarkan `Alumni / Lulus` |
| **Rekomendasi** | - | **SANGAT DIREKOMENDASIKAN (Opsi 1)** agar antarmuka profesional dan terpadu | Tidak direkomendasikan |

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/admin/laporan/tahunan.blade.php` | **Modify** | Mengganti string `LULUS / SELESAI` -> `Selesai`, `Alumni Selesai` -> `Selesai`, `Alumni Magang Lulus` -> `Peserta Selesai Magang`, `...% Lulus` -> `...% Selesai`, dan `Tingkat Kelulusan` -> `Tingkat Penyelesaian`. |
| `resources/js/components/plan-growth-chart.js` | **Modify** | Memastikan komentar kode dan label tooltip hover (`Alumni: ...` -> `Selesai: ...`) konsisten dengan istilah baru. |
| `tests/Feature/AdminAnalitikTahunanVisualTest.php` | **Modify** | Memperbarui asersi teks dari `Alumni Magang Lulus` menjadi `Peserta Selesai Magang` dan memastikan `assertDontSee('Lulus / Selesai')`. |

---

## 5. Langkah Implementasi

### Tahap 1: Pembaruan Blade Template (`tahunan.blade.php`)
- [x] **Langkah 1**: Pada kartu KPI ke-3: ubah label `Alumni Magang Lulus` menjadi `Peserta Selesai Magang`, badge `...% Lulus` menjadi `...% Selesai`, dan sublabel `Tingkat Kelulusan:` menjadi `Tingkat Penyelesaian:`.
- [x] **Langkah 2**: Pada header kolom tabel tren bulanan: ubah `<th style="text-align: center;">Lulus / Selesai</th>` menjadi `<th style="text-align: center;">Selesai</th>`.
- [x] **Langkah 3**: Pada deskripsi section tren: ubah `dan alumni selesai per bulan` menjadi `dan peserta selesai per bulan`.
- [x] **Langkah 4**: Pada header grafik Plan Growth: ubah subtitle `Stacked area • Pendaftar • Diterima • Alumni / Lulus` menjadi `Stacked area • Pendaftar • Diterima • Selesai`.
- [x] **Langkah 5**: Pada legend grafik Plan Growth: ubah `<span>Alumni Selesai</span>` menjadi `<span>Selesai</span>`.

### Tahap 2: Penyelarasan Tooltip JavaScript (`plan-growth-chart.js`)
- [x] **Langkah 6**: Pada markup HTML tooltip hover di `plan-growth-chart.js`, pastikan baris ketiga berlabel `Selesai:` (sebelumnya sudah `Selesai:`, verifikasi dan pertahankan).

### Tahap 3: Build & Quality Gate
- [x] **Langkah 7**: Jalankan `npm run build` untuk kompilasi aset Vite.
- [x] **Langkah 8**: Jalankan `./vendor/bin/pint --test` untuk verifikasi format kode.
- [x] **Langkah 9**: Perbarui dan jalankan `tests/Feature/AdminAnalitikTahunanVisualTest.php` dan `AdvancedAdminAndSuperAdminPhaseFiveTest.php` dengan `./vendor/bin/phpunit`.

---

## 6. Strategi Testing

1. **Uji Otomatis (PHPUnit)**:
   - Verifikasi bahwa halaman merespons HTTP 200.
   - Verifikasi kehadiran teks baru: `Peserta Selesai Magang`, `Selesai`, `Tingkat Penyelesaian`.
   - Verifikasi ketidakhadiran teks lama: `assertDontSee('Alumni Magang Lulus')` dan `assertDontSee('Lulus / Selesai')`.
   - Verifikasi batasan keamanan A-20 tetap berjalan normal.
2. **Uji Visual**:
   - Memastikan header kolom tabel menampilkan kata `Selesai` yang rapi dan sejajar.
   - Memastikan legend grafik menampilkan `Selesai` di samping titik kuning/oranye.

---

## 7. Risiko & Mitigasi

- **Risiko**: Kegagalan test assertion yang sebelumnya memeriksa kata `Alumni Magang Lulus`.
- **Mitigasi**: Menyesuaikan assertion di `AdminAnalitikTahunanVisualTest.php` pada saat yang sama sehingga seluruh pengujian tetap 100% lulus.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Tidak ada lagi kata `LULUS / SELESAI` di tabel bulanan (telah menjadi `Selesai`).
- [x] Tidak ada lagi kata `Alumni Selesai` di legend grafik (telah menjadi `Selesai`).
- [x] Kartu KPI ke-3 telah diselaraskan menjadi `Peserta Selesai Magang` dan `Tingkat Penyelesaian`.
- [x] Subtitle grafik Plan Growth selaras tanpa kata `Alumni / Lulus`.
- [x] `npm run build` berhasil tanpa error.
- [x] `./vendor/bin/pint --test` lulus tanpa pelanggaran style.
- [x] Seluruh pengujian PHPUnit lulus 100% (Hijau).

