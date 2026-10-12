# Rencana Kerja: Relokasi Tombol Aksi Cepat ke Atas Ringkasan Informasi Dashboard Peserta

## 1. Analisis Kebutuhan & Masalah Visual
- **Observasi Masalah Saat Ini**:
  - Saat ini, ketiga tombol aksi cepat (*Quick Actions*: Isi Logbook, Lihat Daftar Tugas, Cek Lembar Nilai) diletakkan di **bawah 4 kotak statistik KPI**.
  - Posisi ini membuat ketiga tombol terlihat "menggantung" sendirian di sisi kanan bawah kartu metrik sebelum daftar tugas/logbook, sehingga hierarki visual terasa kurang pas dan peserta harus melihat metrik terlebih dahulu sebelum menemukan aksi utamanya.
- **Tujuan Revisi**:
  - Memindahkan (*relokasi*) ketiga tombol aksi cepat ke **bagian atas informasi** agar alur visual lebih intuitif, fungsional, dan estetis.

---

## 2. Opsi Penempatan yang Diusulkan

### Opsi A (Sangat Direkomendasikan): Diletakkan Tepat di Atas Grid 4 Kartu KPI (Di Bawah Banner Penempatan)
- **Struktur Alur**:
  1. *Hero Greeting Banner* (Sambutan & Tanggal)
  2. *Banner Informasi Penempatan Magang* (Bidang, Pembimbing, Periode, Tombol Pratinjau SK)
  3. **Baris Aksi Cepat (Quick Actions Bar)**: 3 tombol biru MagKom (*Isi Logbook Mingguan*, *Lihat Daftar Tugas*, *Cek Lembar Nilai*) berada di baris tersendiri dengan alignment kanan yang rapi.
  4. *Grid 4 Kartu KPI* (Tugas Selesai, Logbook, Sisa Hari, Status Evaluasi)
  5. *Dua Kolom Konten* (Tugas Mendesak & Logbook Terbaru)
- **Kelebihan**:
  - Hirarki halaman menjadi sangat logis: Peserta melihat informasi penempatan & tombol aksi prioritas terlebih dahulu, kemudian langsung melihat metrik progres KPI di bawahnya.
  - Tampilan tombol tidak lagi terisolasi di bawah kartu KPI.

### Opsi B: Diintegrasikan Langsung ke Dalam Banner Informasi Penempatan (Di Samping Pratinjau SK)
- **Struktur Alur**:
  - Di dalam kartu Banner Penempatan sebelah kanan (di samping judul Bidang & Pembimbing):
    - Terdapat kelompok tombol: tombol *"Pratinjau SK Diterima"* disandingkan dengan tombol *"Isi Logbook"*, *"Daftar Tugas"*, dan *"Lembar Nilai"*.
- **Kelebihan**:
  - Sangat ringkas dan menyatu dalam satu kartu info penempatan.
- **Kekurangan**:
  - Pada layar laptop/desktop berukuran sedang, sisi kanan kartu banner bisa terasa agak padat jika menampung 4 tombol sekaligus.

---

## 3. Detail Perubahan Kode (Jika Memilih Opsi A)
- **File Target**: [`resources/views/peserta/dashboard.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard.blade.php)
- **Pemindahan Blok**:
  - Memindahkan blok `<div class="quick-actions-bar">...</div>` (baris 600–620) ke atas blok `<div class="kpi-grid">` (baris 536).
  - Menyesuaikan margin bawah (`margin-bottom: 20px;`) agar jarak antara tombol aksi cepat dan kartu KPI terlihat proporsional dan elegan.

---

## 4. Rencana Verifikasi
1. **Verifikasi Sintaks Blade**: Memastikan struktur tag HTML dan kondisi `@if($fase === 'aktif' && $penempatan)` tetap utuh.
2. **Uji Render & Testing**:
   - Menjalankan `php artisan test --filter=DashboardModulesRenderingTest` untuk memvalidasi rendering dashboard.
3. **Verifikasi Responsivitas Mobile/Desktop**: Memastikan tombol tetap tertata rapi saat dilihat pada berbagai ukuran layar.

---

## 5. Pertanyaan Konfirmasi Pengguna
Apakah rencana relokasi tombol aksi cepat ini disetujui (dengan rekomendasi **Opsi A**: tepat di atas 4 kartu KPI dan di bawah banner informasi penempatan)?
