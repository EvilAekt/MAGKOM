# [PLAN] Redesain Kartu Kuota Magang Menjadi Format Modern "Goals / Smooth Progress Track"

Dokumen rencana kerja ini disusun berdasarkan permintaan pengguna untuk mentransformasi tampilan kotak-kotak kuota jurusan (Gambar 1) menjadi format baris progres tunggal yang menyatu, mulus, dan elegan seperti kartu target / *Goals* pada sistem antarmuka modern (Gambar 2).

---

## 1. Analisis Perbandingan Visual (Gambar 1 vs Gambar 2)

| Parameter | Tampilan Saat Ini (Gambar 1) | Target Desain Baru (Gambar 2) |
| :--- | :--- | :--- |
| **Struktur Kontainer** | Setiap jurusan dibungkus dalam kotak (*box card*) individual terpisah dengan border abu-abu (`.adm-quota-row-item`), sehingga tampilan terasa terfragmentasi dan kaku. | Seluruh jurusan berada dalam **satu kontainer kartu terpadu (*unified card*)** tanpa kotak-kotak pembungkus kecil di dalamnya. |
| **Gaya Indikator Progres** | Menggunakan garis terputus-putus titik segmen (*segmented seat bars* `.cv-seg`) yang sering terpotong tidak beraturan jika jumlah kursi ganjil atau banyak. | Menggunakan **Continuous Smooth Progress Bar** (garis progres solid, mulus, dan melengkung bulat di kedua ujungnya / *pill rounded*). |
| **Tata Letak Baris (*Row Layout*)** | Baris jurusan tersebar dalam kotak-kotak grid dengan banyak garis pembatas dan padding tebal. | Format **Sleek Metric Row**: Nama Jurusan di sebelah kiri, Angka Realisasi (`X / Y Kursi`) di sebelah kanan, dan Bar Progres Mulus membentang tepat di bawahnya. |
| **Aksen Warna (*Color Palette*)** | Warna kaku (hanya biru/amber/merah seragam). | **Vibrant Multi-Color Palette** (Teal `#06B6D4`, Emerald `#10B981`, Blue `#3B82F6`, Amber `#F59E0B`, Purple `#8B5CF6`, Rose `#EC4899`) yang memberikan visualisasi data kaya dan hidup seperti pada gambar referensi. |
| **Adaptasi Tema Gelap / Terang** | Track bar putih/abu-abu netral sederhana. | Track bar adaptif: latar rel abu-abu halus (`rgba(0,0,0,0.06)`) pada mode terang dan gelap transparan (`rgba(255,255,255,0.08)`) pada mode gelap. |

---

## 2. Tujuan & Pendekatan Teknis

### Tujuan Akhir:
1. Mengubah tampilan tabel kuota jurusan di [resources/views/admin/dashboard/partials/_quota_monitoring.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/admin/dashboard/partials/_quota_monitoring.blade.php) menjadi list baris progres bergaya **"Goals / Performance Track"** yang bersih, minimalis, dan sangat mudah dipindai secara visual.
2. Menghapus kotak-kotak pembungkus kecil individual (`adm-quota-row-item`) dan garis segmen putus-putus (`.cv-seg`), menggantikannya dengan garis progres mulus dengan *pill-radius* setinggi `6px`.
3. Menghadirkan palet warna dinamis yang bergantian secara estetik antar jurusan, serta indikator visual lembut untuk status kursi penuh (`100% Penuh`).
4. Mempertahankan integrasi bar kapasitas total (*Total Kuota Umum Terisi*) di bagian atas dengan estetika yang selaras.

---

## 3. Evaluasi Pendekatan Alternatif

| Aspek | Opsi A: Vertical Stack Unified List (Persis Gambar 2) (Direkomendasikan) | Opsi B: Dual-Column Goals Grid (2 Kolom Baris Mulus) |
| :--- | :--- | :--- |
| **Deskripsi** | Menampilkan seluruh jurusan dalam satu daftar vertikal terpadu dari atas ke bawah, persis mengikuti tata letak Gambar 2. | Membagi daftar jurusan menjadi 2 kolom (kiri & kanan) di dalam satu kartu. |
| **Kelebihan** | - 100% identik dengan referensi Gambar 2 yang diinginkan pengguna.<br>- Keterbacaan teks nama jurusan panjang (misal: *Teknik Komputer dan Jaringan*) sangat maksimal tanpa risiko terpotong.<br>- Bar progres memiliki bentang lebar yang panjang dan proporsional untuk membedakan persentase okupansi. | Menghemat tinggi vertikal jika jumlah jurusan sangat banyak (> 10). |
| **Kekurangan** | Menambah sedikit tinggi vertikal (namun saat ini kolom samping kanan sudah diisi widget terpadu sehingga tingginya akan sangat seimbang). | Nama jurusan yang panjang berpotensi mengalami pemotongan teks (*ellipsis*). |
| **Keputusan** | **Dipilih Opsi A (Rekomendasi)** karena sangat elegan, natural, dan persis dengan keinginan pengguna pada Gambar 2. | Ditolak untuk saat ini agar tidak mempersempit bentang bar progres. |

---

## 4. Perubahan Berkas (Files to Modify)

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [resources/views/admin/dashboard/partials/_quota_monitoring.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/admin/dashboard/partials/_quota_monitoring.blade.php) | **Modify** | Rombak markup dari pembungkus grid kotak individual menjadi baris daftar progres mulus (*Goals Style*) dengan nama jurusan di kiri, angka kursi di kanan, dan progress bar di bawahnya. |
| [public/css/dashboard-cockpit-unified.css](file:///d:/laragon/www/MAGKOM/public/css/dashboard-cockpit-unified.css) | **Modify** | Tambahkan class styling khusus `.cv-goal-list`, `.cv-goal-item`, `.cv-goal-header`, `.cv-goal-title`, `.cv-goal-val`, `.cv-goal-track`, dan `.cv-goal-fill` lengkap dengan palet multi-warna dan dukungan dark mode. |

---

## 5. Langkah Implementasi Terstruktur

- [x] **Langkah 1: Tambahkan Utility Styling "Goals Track" di `dashboard-cockpit-unified.css`**
  - Menentukan class `.cv-goal-list`, `.cv-goal-item`, `.cv-goal-header`, `.cv-goal-title`, `.cv-goal-val`, `.cv-goal-track`, dan `.cv-goal-fill` lengkap dengan palet multi-warna dan dukungan tema terang/gelap.

- [x] **Langkah 2: Rombak Markup Template di `_quota_monitoring.blade.php`**
  - Mengganti perulangan kuota dari yang semula menggunakan `adm-quota-row-item` dan `.cv-seg` (Gambar 1) menjadi format baris progres tunggal menyatu bergaya Goals (Gambar 2).

- [x] **Langkah 3: Pembersihan Cache View & Pengujian Otomatis**
  - Menjalankan `php artisan view:clear`.
  - Menjalankan automated tests:
    - `php artisan test --filter=AdminDashboardEnhancementTest` (PASS)
    - `php artisan test --filter=AdvancedThreePillarsDashboardTest` (PASS)
    - `php artisan test --filter=AdminChildPagesVisualTest` (PASS)

- [x] **Langkah 4: Verifikasi Visual**
  - Memverifikasi tampilan kartu kuota kini mulus, terpadu tanpa kotak terpisah, dan identik dengan Gambar 2.

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - Memastikan `AdminDashboardEnhancementTest` (`assertSee('Keterisian Kuota Magang Periode')`) tetap lolos 100% (PASS, 15 assertions).
   - Memastikan `AdvancedThreePillarsDashboardTest` tetap lolos 100% (PASS, 43 assertions).
2. **Visual Inspection**:
   - Tidak ada lagi kotak-kotak putih kecil individual di dalam kartu kuota.
   - Garis progres solid, mulus, dan memiliki warna-warna yang kontras dan menyenangkan dilihat pada mode terang dan mode gelap.

---

## 7. Risiko & Mitigasi

- **Risiko Responsivitas Layar Kecil**: Teks nama jurusan dan angka kursi harus tetap fleksibel dan tidak saling bertabrakan pada layar smartphone (< 400px).
  - *Mitigasi*: Menggunakan `flex-wrap: wrap` pada `.cv-goal-header`.
- **Risiko Dark Mode**: Warna teks dan background track harus tetap memiliki kontras tinggi saat pengguna mengaktifkan mode gelap.
  - *Mitigasi*: Menyediakan selector `[data-theme="dark"] .cv-goal-track { background: rgba(255, 255, 255, 0.08); }` dan token tema adaptif.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Tampilan kartu kuota jurusan berubah dari kotak-kotak terfragmentasi (Gambar 1) menjadi format baris progres tunggal yang menyatu dan mulus (Gambar 2).
- [x] Setiap jurusan memiliki baris progres solid rounded dengan palet multi-warna estetik (Teal, Green, Blue, Amber, Purple, Rose, dan Red untuk penuh).
- [x] Total kapasitas magang di bagian atas tetap terjaga dan terpadu rapi.
- [x] Seluruh automated feature tests lolos 100% tanpa kegagalan.

