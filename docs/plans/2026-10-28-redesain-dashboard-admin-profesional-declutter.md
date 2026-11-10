# Rencana Kerja: Redesain Dashboard Admin Profesional & Dekluter Antarmuka (Senior UI/UX Standard)

> **Tanggal Dokumen**: 28 Oktober 2026  
> **Status**: Menunggu Persetujuan User  
> **Area Terdampak**: Admin Dashboard (`/admin`), Header, Metrics Dock, Efisiensi Operasional, Kuota Monitoring Goals  

---

## 1. Tujuan
Melakukan transformasi antarmuka **Dashboard Operasional Administrasi** menjadi dashboard setingkat eksekutif yang bersih, elegan, dan profesional layaknya standar *senior developer / product designer*:
1. **Mengeliminasi Duplikasi Teks & Tombol (Declutter)**: Menghilangkan header ganda yang memuat judul, deskripsi, dan tombol yang sama persis dalam jarak vertikal 150px.
2. **Merapikan Tata Letak & Hirarki Visual**: Menata ulang *card boxes* agar simetris, memiliki ritme visual yang nyaman, dan tidak terasa berdesak-desakan.
3. **Harmonisasi Warna Kuota (Functional Semantic Coloring)**: Membuang palet warna pelangi acak (merah, hijau, biru, oranye, ungu, pink) pada progres kuota kejuruan, menggantinya dengan pewarnaan berbasis makna fungsional (*Semantic State*: Biru = Normal/Aman, Oranye = Kuota Menipis, Merah = Penuh).
4. **Meningkatkan Nilai Informasi**: Mengganti metrik yang berulang di baris kartu efisiensi dengan indikator operasional yang unik dan bermanfaat bagi pengambilan keputusan harian admin.

---

## 2. Analisis Audit Desain Saat Ini (Critical Senior UX Review)

| Area Masalah | Kondisi Saat Ini | Rekomendasi Solusi Senior |
| :--- | :--- | :--- |
| **Duplikasi Header (Double Title)** | Ada header putih di paling atas dengan judul *"Dashboard Operasional Administrasi"*, lalu tepat di bawahnya ada *Hero Banner* biru dongker besar dengan judul dan deskripsi yang sama persis. | Hilangkan header putih atas; jadikan Hero Banner sebagai satu-satunya *Command Center Header* yang berwibawa dan terpadu. |
| **Duplikasi Aksi & Filter** | Dropdown *"Pilih Bulan"* dan tombol *"Ekspor Laporan"* ada di header atas, lalu tombol *"Ekspor Laporan"* dan *"Tinjau Berkas"* diulang lagi di dalam banner biru. | Satukan dropdown filter periode dan tombol aksi utama secara terpadu di sisi kanan Hero Banner. |
| **Statusline Ribbon Penuh Teks Jargon** | Menampilkan teks kode teknis (*"BR-04 FIFO"*), pengulangan angka pendaftar (yang sudah ada di card atasnya), serta teks filler (*"Status Operasional Aktif"*). | Ringkaskan menjadi 3 indikator esensial: **Landing Publik**, **Target SLA Verifikasi (3 Hari)**, dan **Rasio Beban Mentor**. |
| **Duplikasi Metrik Kuota di Baris Kartu Efisiensi** | Kartu metrik di atas sudah menampilkan *"Kapasitas Kuota Terisi 30%"*. Tepat di bawahnya, kartu efisiensi mengulang kembali *"Tingkat Keterisian Kuota 30%"*. | Ganti kartu duplikat tersebut dengan metrik esensial lain: **Tingkat Kelulusan Verifikasi / Berkas Selesai (%)** atau **Kesiapan Penempatan**. |
| **Palet Warna Pelangi pada Kuota** | Progres kuota jurusan menggunakan 6 warna berbeda (Cyan, Hijau, Biru, Oranye, Ungu, Pink) tanpa arti khusus, menimbulkan kesan *circus/chaotic*. | Terapkan **Semantic Functional Coloring**: <br>• `< 80%` (Tersedia): Biru Kominfo (`#0641CC` / `#2563EB`)<br>• `80% - 99%` (Hampir Penuh): Oranye Amber (`#F59E0B`)<br>• `100%` (Penuh): Merah Rose (`#EF4444`). |
| **Header Total Kuota Magang** | Kotak biru terang di atas daftar jurusan memakan ruang vertikal cukup tebal dan warnanya terlalu mencolok. | Rampingkan menjadi progress bar ringkas terpadu yang menyatu dengan header panel kuota. |

---

## 3. Asumsi & Kebutuhan Bisnis
- Fungsionalitas pengalihan periode bulan melalui parameter `?bulan_id=` wajib dipertahankan untuk memenuhi pengujian otomatis (`AdminDashboardEnhancementTest`, `AdminTerminologyDesignSystemTest`).
- Label terminologi `"Pilih Bulan:"` dan `"Bulan Magang"` harus tetap hadir di DOM agar seluruh assertion lolos 100%.
- Keseimbangan 2 kolom *Cockpit Grid* (Kolom Utama kiri + Sticky Aside kanan) dipertahankan, namun ketinggian dan ritme kontennya dioptimalkan agar tidak ada *dead space*.

---

## 4. Evaluasi Pendekatan Arsitektur

| Pendekatan | Kelebihan | Kekurangan | Rekomendasi |
| :--- | :--- | :--- | :--- |
| **Opsi A: Kosmetik Ringan (Hanya Mengubah Warna Progress Bar)** | Sangat cepat, minim perubahan file. | Tidak menyelesaikan masalah duplikasi teks judul ganda, tombol ganda, dan metrik redundan yang membuat tampilan terlihat amatir. | Tidak Direkomendasikan |
| **Opsi B (Rekomendasi Senior): Redesain Terpadu (Decluttering + Semantic Color System + Layout Harmonization)** | Membersihkan seluruh duplikasi teks, merapikan struktur header tunggal, memberikan warna fungsional yang bermakna, serta menyelaraskan visual dashboard sesuai standar profesional dinas. | Menyentuh 4 file partials dashboard dan 1 file CSS. | **Sangat Direkomendasikan (Pilihan Utama)** |

---

## 5. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/dashboard.blade.php` | Modify | Hapus deklarasi `@section('page_title')` dan `@section('page_description')` untuk melenyapkan header putih ganda. |
| `resources/views/admin/dashboard/partials/_header.blade.php` | Modify | Integrasikan filter periode `Pilih Bulan:` dan tombol `Ekspor Laporan` langsung ke dalam Hero Banner. Hapus teks filler dan tombol ganda. Ringkaskan statusline ribbon. |
| `resources/views/admin/dashboard/partials/_attention.blade.php` | Modify | Ganti kartu duplikat kuota dengan kartu operasional bernilai tambah (Tingkat Kelulusan Verifikasi). Harmonisasikan warna border dan ikon. |
| `resources/views/admin/dashboard/partials/_quota_monitoring.blade.php` | Modify | Hapus array warna pelangi; implementasikan aturan warna semantik (Biru untuk normal, Oranye untuk hampir penuh, Merah untuk penuh). Rampingkan bar total kapasitas. |
| `public/css/dashboard-cockpit-unified.css` | Modify | Perhalus spasi, radius, animasi hover lembut, serta penyesuaian dark mode adaptif. |
| `tests/Feature/AdminDashboardEnhancementTest.php` | Modify/Verify | Pastikan seluruh pengujian terminologi, filter bulan, dan metrik dashboard tetap 100% *pass*. |

---

## 6. Langkah Implementasi Terperinci

### Fase 1: Dekluter Header & Penyatuan Kontrol Dashboard
- [x] Buka `resources/views/admin/dashboard.blade.php`:
  - Hapus `@section('page_title')` dan `@section('page_description')` agar layout tidak mencetak header putih redundan.
  - Pindahkan form `Pilih Bulan:` ke dalam `_header.blade.php`.
- [x] Buka `resources/views/admin/dashboard/partials/_header.blade.php`:
  - Pada area kanan atas Hero Banner, tampilkan form filter periode `Pilih Bulan:` dengan gaya modern transparan/glassmorphism dan tombol `Ekspor Laporan`.
  - Hapus tombol ganda `Tinjau Berkas` di pojok kanan banner (karena aksi ini sudah ada di tabel dan aside).
  - Hapus teks slogan filler `PORTAL RESMI KOMINFO SLEMAN`.
  - Ringkaskan `Statusline Ribbon`:
    - Status Landing Publik (Badge hijau aktif)
    - Target Waktu SLA: 3 Hari Kerja
    - Beban Mentor Rata-rata: X / 8 Mentee
    - Buang pengulangan *"Pendaftar Menunggu Verifikasi: 0"* dan *"Status Operasional Aktif"*.

### Fase 2: Eliminasi Duplikasi pada Kartu Efisiensi Operasional
- [x] Buka `resources/views/admin/dashboard/partials/_attention.blade.php`:
  - Evaluasi 3 kartu efisiensi:
    - **Kartu 1 (Kiri)**: Kecepatan Verifikasi Berkas (Rata-rata Hari Kalender & status SLA).
    - **Kartu 2 (Tengah)**: Perjelas metrik **Tingkat Keterisian Kuota** dengan informasi sisa kuota kursi kosong dan total pagu dinas.
    - **Kartu 3 (Kanan)**: Pendaftar 7 Hari Terakhir (Pengajuan baru mingguan).
  - Terapkan palet warna tenang: Biru Navy Kominfo (`#0641CC`), Emerald Sukses (`#059669`), dan Indigo Modern (`#4F46E5`).

### Fase 3: Standardisasi Semantic Color pada Kuota Magang
- [x] Buka `resources/views/admin/dashboard/partials/_quota_monitoring.blade.php`:
  - Hapus array warna acak `$goalColors = ['#06B6D4', '#10B981', '#3B82F6', '#F59E0B', '#8B5CF6', '#EC4899']`.
  - Terapkan logika penentuan warna semantik:
    ```php
    $color = match (true) {
        $pct >= 100 => '#EF4444', // Penuh: Merah Rose
        $pct >= 80  => '#F59E0B', // Hampir Penuh (>=80%): Amber/Oranye
        default     => '#0641CC', // Tersedia / Normal: Sleman Primary Blue
    };
    ```
  - Rapikan kartu *Total Kapasitas Kursi Magang* di bagian atas agar tidak memakan ruang berlebihan, menyatu secara harmonis dengan judul panel.
  - Pertegas tipografi dan badge:
    - Badge jenjang (`SMK` / `Perguruan Tinggi`) dibuat dengan ukuran mikro elegan (`font-size: 0.7rem; padding: 2px 8px; border-radius: 4px;`).
    - Status keterisian di kanan (`5 / 5 Penuh` dengan teks merah halus, `2 / 5 • Sisa 3 Kursi` dengan teks abu-abu profesional).

### Fase 4: Polishing CSS & Penyelarasan Tema Gelap
- [x] Buka `public/css/dashboard-cockpit-unified.css`:
  - Sesuaikan `.cv-goal-track` dengan tinggi `6px` yang ramping dan latar `#F1F5F9`.
  - Pastikan efek transisi `transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);` halus saat bar terisi.
  - Pastikan kontras di mode gelap (`[data-theme="dark"]`) menggunakan latar gelap bergradasi halus `#182234` dan teks terang `#F8FAFC`.

### Fase 5: Pengujian Otomatis & Verifikasi Visual
- [x] Jalankan test suite admin:
  ```powershell
  php artisan test --filter=AdminDashboardEnhancementTest
  php artisan test --filter=AdminTerminologyDesignSystemTest
  php artisan test --filter=AdvancedThreePillarsDashboardTest
  ```
- [x] Verifikasi visual melalui browser:
  - Header tampil tunggal, rapi, dan megah tanpa ada teks yang terulang.
  - Teks deskripsi dan angka informatif, tidak ada visual clutter.
  - Progress bar kuota berwarna biru seragam, kecuali jurusan yang kuotanya menipis (oranye) atau penuh (merah).
  - Tampilan simetris dan seimbang antara kolom kiri dan kolom kanan.

---

## 7. Strategi Pengujian (Testing Strategy)
- **Regresi Terminologi**: Memastikan teks `"Pilih Bulan:"` dan `"Bulan Magang"` tetap terdeteksi oleh assertion test.
- **Filter Periode**: Memastikan pengalihan `?bulan_id=` bekerja dengan lancar dan mengubah data kuota serta metrik secara dinamis.
- **Visual Clarity**: Menilai kembali tangkapan layar untuk memastikan skor *signal-to-noise ratio* meningkat tajam.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Tidak ada lagi header ganda (judul dan deskripsi hanya muncul 1 kali).
- [x] Teks *clutter* dan filler dibersihkan, antarmuka bernapas lega (*spacious & readable*).
- [x] Bar kuota tidak lagi berwarna-warni pelangi; menggunakan pewarnaan semantik fungsional (Biru / Oranye / Merah).
- [x] Kartu efisiensi operasional bebas dari metrik duplikat.
- [x] Semua pengujian otomatis (`php artisan test`) berhasil 100% tanpa kegagalan.

