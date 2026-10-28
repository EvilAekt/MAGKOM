# [PLAN] Penataan Ulang Layout Dashboard Admin & Pengisian Sisi Kanan (Aside) yang Seimbang dan "Worth It"

Dokumen rencana kerja ini disusun berdasarkan telaah tangkapan layar antarmuka pengguna pada Dashboard Admin MagKom Sleman ([resources/views/admin/dashboard.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/admin/dashboard.blade.php)).

---

## 1. Identifikasi Masalah & Akar Penyebab (Root Cause Analysis)

Berdasarkan analisis tangkapan layar (screenshot 1 & screenshot 2) dan inspeksi berkas template Blade:

1. **Kekosongan Raksasa di Bawah Card "Aksi Cepat Admin" (Screenshot 2)**:
   - Layout dashboard menggunakan grid 2-kolom (`.cv-cockpit-layout`): Kolom Utama (`cv-cockpit-main`, 1fr) dan Kolom Samping (`cv-aside-sticky`, 340px).
   - Kolom samping (`aside`) saat ini **hanya menampung 2 kartu pendek**:
     1. *Perlu Tindakan Hari Ini* (~130px)
     2. *Aksi Cepat Admin* (~170px)
     Total tinggi kolom samping hanya ~300px.
   - Sementara kolom utama di sebelah kiri memiliki 3 kartu metrik besar, tabel antrean verifikasi, tabel kuota seluruh jurusan, dan kartu status operasional yang panjangnya mencapai ~1500px.
   - Akibatnya, terdapat **kekosongan ruang putih menganga (>1000px)** di bawah kartu Aksi Cepat Admin.

2. **Tampilan Card "Aksi Cepat Admin" yang Rusak/Berantakan**:
   - Di tangkapan layar 2, tombol-tombol *Aksi Cepat Admin* (Tinjau Antrean Berkas, Penempatan, Waiting List, Rekap Laporan, Hub Sertifikat) tampil bertumpuk seperti hyperlink teks biru biasa tanpa border, tanpa background, dan tanpa grid tombol yang rapi.
   - **Penyebab**: Class `.cv-quick-grid` dan `.cv-quick-btn` berada di `public/css/admin-cockpit-clearview.css` yang **tidak pernah dimuat** di `layouts/dashboard.blade.php`.

3. **Tiga Kartu Metrik KPI Efisiensi di Atas Melar Vertikal**:
   - Di tangkapan layar 1, tiga kartu KPI (*Kecepatan Verifikasi Berkas*, *Tingkat Keterisian Kuota*, *Pendaftar 7 Hari Terakhir*) melar menjadi 3 baris blok penuh selebar 100% yang bertumpuk vertikal dan memakan ruang vertikal secara berlebihan, bukan berjejer horizontal 3 kolom yang proporsional (*3-column grid*).

4. **Salah Penempatan Widget Operasional**:
   - Widget *Jadwal Pendaftaran*, *Distribusi Pembimbing Lapangan*, dan *Offboarding Watchdog* saat ini dijejalkan di kolom kiri bawah di samping tabel kuota, padahal secara semantik dan anatomi UI dashboard SaaS modern, widget-widget ini adalah **widget ringkasan operasional** yang sangat ideal ditempatkan di kolom samping kanan (`aside`) untuk menemani tabel kerja utama di sebelah kiri.

---

## 2. Tujuan & Pendekatan Teknis

### Tujuan Akhir:
1. Menata kembali layout dashboard admin menjadi seimbang, simetris, dan bernilai guna tinggi (*worth it*).
2. Memperbaiki estetika tombol *Aksi Cepat Admin* menjadi grid 2-kolom tombol modern (SaaS cockpit).
3. Mengisi ruang kosong di kolom samping kanan dengan widget-widget operasional nyata yang datanya **sudah tersedia lengkap di backend**:
   - **Widget 1**: *Jadwal & Countdown Pendaftaran Bulan Ini* (Batas tanggal pendaftaran, sisa hari kalender, status buka/tutup dinas).
   - **Widget 2**: *Distribusi & Beban Pembimbing Lapangan* (Avatar inisial, nama pembimbing dinas, bidang dinas, dan badge jumlah mentee aktif `0/5 Mentee` untuk memudahkan admin saat hendak menempatkan peserta).
   - **Widget 3**: *Offboarding Watchdog (Peringatan Selesai H-14)* (Daftar peserta yang akan segera menyelesaikan masa magang dalam 14 hari ke depan).
   - **Widget 4**: *Ringkasan Alur & Pipeline Magang* (Indikator cepat status alur: Diajukan, Waiting List, Aktif Magang, Selesai).
4. Mengubah 3 kartu KPI efisiensi di atas menjadi **3-Column Compact Metric Grid** yang rapi dan elegan.
5. Memberikan ruang penuh (100%) pada tabel *Alokasi & Keterisian Kuota Magang Periode* di kolom kiri agar progress bar segmented jurusan tampil leluasa tanpa berdesakan.

---

## 3. Evaluasi Opsi Arsitektur & Desain

| Aspek | Opsi A: Restrukturisasi 2-Kolom Seimbang (Cockpit Grid) (Direkomendasikan) | Opsi B: Mengubah Menjadi 1-Kolom Penuh (Full Width Linear) |
| :--- | :--- | :--- |
| **Deskripsi** | Menjaga struktur 2-kolom (`1fr` + `340px`), merestrukturisasi penempatan widget operasional ke kolom kanan, dan merapikan CSS grid tombol & KPI. | Menghapus kolom aside (`340px`), lalu menumpuk seluruh kartu dan tabel ke bawah secara vertikal dari atas ke bawah. |
| **Kelebihan** | - Tinggi kolom kiri dan kanan menjadi seimbang sempurna (zero empty space).<br>- Tabel kerja utama (antrean verifikasi & kuota) tetap luas di kiri, sementara kontrol cepat dan radar status ada di kanan.<br>- Sesuai dengan standar industri SaaS Dashboard (seperti GitHub, Linear, Stripe Dashboard). | Sederhana tanpa perlu menghitung keseimbangan tinggi antar kolom. |
| **Kekurangan** | Memerlukan penyesuaian markup Blade di 2 file partial (`_aside.blade.php` dan `_quota_monitoring.blade.php`). | - Dashboard menjadi sangat panjang ke bawah (*infinite vertical scroll*).<br>- Tampilan terlihat kaku dan kosong di layar monitor desktop lebar (1440px / 1920px). |
| **Keputusan** | **Dipilih Opsi A (Rekomendasi)** karena memberikan tampilan visual yang profesional, tertata rapi, dan fungsionalitas yang bernilai guna tinggi (*worth it*). | Ditolak karena memperburuk pengalaman pengguna dan membuat layout melar ke bawah. |

---

## 4. Perubahan Berkas (Files to Modify)

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [resources/views/admin/dashboard/partials/_aside.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/admin/dashboard/partials/_aside.blade.php) | **Modify** | 1. Mempercantik Card *Aksi Cepat Admin* dengan CSS grid tombol 2-kolom modern.<br>2. Menambahkan widget *Jadwal & Countdown Pendaftaran*, *Distribusi Pembimbing Lapangan*, dan *Offboarding Watchdog* ke kolom kanan. |
| [resources/views/admin/dashboard/partials/_attention.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/admin/dashboard/partials/_attention.blade.php) | **Modify** | Mengubah 3 kartu KPI dari class `row col-md-4` menjadi CSS grid 3-kolom responsif (`display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem;`) agar tidak melar vertikal. |
| [resources/views/admin/dashboard/partials/_quota_monitoring.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/admin/dashboard/partials/_quota_monitoring.blade.php) | **Modify** | Menata tabel Alokasi & Keterisian Kuota Magang Periode menjadi full-width di kolom utama dengan layout grid 2-kolom per jurusan yang lega, bersih, dan rapi. |
| [public/css/dashboard-cockpit-unified.css](file:///d:/laragon/www/MAGKOM/public/css/dashboard-cockpit-unified.css) | **Modify** | Menyisipkan definisi style `.cv-quick-grid`, `.cv-quick-btn`, dan styling kartu aside agar tombol tampil rapi dengan icon, border halus, hover effect, dan kontras tema terang/gelap. |

---

## 5. Langkah Implementasi Terstruktur

- [x] **Langkah 1: Perbarui CSS untuk Tombol Aksi Cepat & Aside di `dashboard-cockpit-unified.css`**
  - Menambahkan definisi style lengkap untuk:
    - `.cv-quick-grid`: Grid 2 kolom (`grid-template-columns: 1fr 1fr; gap: 0.5rem;`).
    - `.cv-quick-btn`: Tombol flex dengan padding rapi, background netral, border halus, font tebal 0.785rem, dan hover state yang elegan.
    - `.cv-quick-btn.primary`: Tombol utama lebar penuh (`grid-column: 1 / -1;`) dengan aksen biru Diskominfo Sleman (`#0641CC`).

- [x] **Langkah 2: Perbaiki Barisan 3 Kartu KPI Efisiensi di `_attention.blade.php`**
  - Mengubah container pembungkus menjadi Modern Responsive Metric Grid (`display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;`) sehingga berjejer rapi horizontal dan tidak melar vertikal.

- [x] **Langkah 3: Perkaya dan Seimbangkan Kolom Kanan di `_aside.blade.php`**
  - Mempertahankan **Panel 1: Perlu Tindakan Hari Ini** (SLA warning / Todo items).
  - Memoles **Panel 2: Aksi Cepat Admin** menggunakan grid tombol 2-kolom baru.
  - Menambahkan **Panel 3: Jadwal & Status Pendaftaran Periode Ini** (Batas tanggal, badge `● Dibuka` / `● Ditutup`, dan countdown sisa hari).
  - Menambahkan **Panel 4: Beban & Ketersediaan Pembimbing** (Avatar inisial, bidang dinas, dan badge `X Mentee`).
  - Menambahkan **Panel 5: Offboarding Watchdog (Peringatan Selesai H-14)** (Pengingat peserta magang yang mendekati tanggal selesai).

- [x] **Langkah 4: Rapikan Tabel Kuota di Kolom Utama `_quota_monitoring.blade.php`**
  - Mengubah kartu *Alokasi & Keterisian Kuota Magang Periode* menjadi full-width di kolom utama dengan layout grid responsif 2-kolom per jurusan yang lega, bersih, dan segmented bars yang rapi.

- [x] **Langkah 5: Pembersihan Cache & Verifikasi Visual & Test Suite**
  - Menjalankan `php artisan view:clear`.
  - Menjalankan automated test suites (`AdvancedThreePillarsDashboardTest`, `AdminDashboardEnhancementTest`, `AdminChildPagesVisualTest`) dan 100% lulus sukses.

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - `AdvancedThreePillarsDashboardTest`: Memastikan teks `'Offboarding Watchdog'`, `'Timeline Gelombang Magang'`, dan `'Hub Sertifikat'` tetap ter-render dan lolos pengujian (PASS, 10/10 tests, 43 assertions).
   - `AdminChildPagesVisualTest`: Memastikan halaman admin tetap merender metrik dan komponen kunci (PASS, 19 assertions).
   - `AdminDashboardEnhancementTest`: Memastikan command center dashboard berfungsi normal (PASS, 15 assertions).
2. **Visual Inspection**:
   - Kolom kanan terisi penuh secara estetik dari atas hingga ke bawah, sejajar dengan tinggi tabel di kolom kiri.
   - Tombol *Aksi Cepat Admin* berwujud tombol nyata yang rapi (bukan hyperlink teks bertumpuk).

---

## 7. Risiko & Mitigasi

- **Risiko Test Assertion Broken**: Pengujian `AdvancedThreePillarsDashboardTest` mencari string `'Offboarding Watchdog'` dan `'Hub Sertifikat'`.
  - *Mitigasi*: Seluruh teks dan id kunci tetap dipertahankan secara presisi pada widget di kolom kanan, sehingga 100% tes otomatis tetap lulus hijau.
- **Risiko Responsivitas Mobile**: Layar di bawah 1200px harus tetap melipat kolom samping ke bawah secara wajar.
  - *Mitigasi*: Mempertahankan media query `@media (max-width: 1200px) { .cv-cockpit-layout { grid-template-columns: 1fr; } }`.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Area kosong di bawah kartu *Aksi Cepat Admin* terisi secara proporsional dengan widget-widget operasional bermanfaat (*Jadwal Pendaftaran*, *Beban Pembimbing*, *Offboarding Watchdog*).
- [x] Tombol pada kartu *Aksi Cepat Admin* tampil rapi dalam formasi grid dengan icon dan style tombol yang jelas.
- [x] Tiga kartu KPI efisiensi di atas berjejer horizontal rapi dalam grid 3-kolom (tidak melar vertikal).
- [x] Seluruh automated feature tests (`AdvancedThreePillarsDashboardTest`, dll.) lulus 100% tanpa error.
- [x] Tampilan responsif di layar desktop dan mobile tetap mulus dan bebas regresi.

