# Rencana Kerja: Revamp Komponen Progres Logbook & Peningkatan UX bagi Pengguna Awal

## 1. Analisis Masalah Pengguna & UX Saat Ini
Berdasarkan masukan dan tangkapan layar dari pengguna:
1. **Kurangnya Legenda Keterangan Status**:
   - Deretan chip minggu menggunakan simbol/emoji (`⏳`, `✓`, `⚠️`, `📍`, `📅`) tanpa adanya baris legenda keterangan (*status legend*). Pengguna baru tidak mengerti arti dari simbol-simbol tersebut.
2. **Singkatan Terlalu Kering (`M-1`, `M-2`)**:
   - Singkatan `M-1` hingga `M-13` membingungkan pengguna pemula (apakah "Minggu" atau "Bulan"), dan tanggal rentang periode (misal `11 Okt – 17 Okt`) hanya tersembunyi di dalam *hover tooltip* native browser yang tidak bisa diakses di layar sentuh (*touch screen*).
3. **Scrollbar Horizontal Kaku & Kurang Rapi**:
   - Deretan 13–24 minggu menghasilkan *horizontal scrollbar* browser yang kaku di bawah chip.
4. **Chip Kurang Fungsional (Tidak Dapat Diklik)**:
   - Saat ini chip minggu hanya berupa elemen teks pasif. Pengguna mengharapkan ketika sebuah minggu diklik, sistem langsung mengarahkan (*auto-scroll*) ke laporan minggu tersebut atau membuka modal detail/pengisian.
5. **Tidak Ada Panduan Singkat Alur Pelaporan**:
   - Pengguna pemula membutuhkan kartu petunjuk ringkas (*info tip*) tentang kapan logbook harus diisi (misal: diisi setiap akhir pekan, tenggat waktu, dan persetujuan pembimbing).

---

## 2. Solusi Desain Baru yang Ramah Pengguna (User-Friendly & Intuitif)

### A. Penyempurnaan Tracker Progres Logbook
1. **Legenda Status Jelas & Elegan**:
   - Menambahkan baris legenda status di bawah progress bar:
     - 🟢 **Disetujui** (Selesai dievaluasi)
     - 🔵 **Terkirim** (Menunggu review pembimbing)
     - 🟡 **Perlu Revisi** (Perlu diperbaiki)
     - 🔷 **Minggu Berjalan** (Sedang aktif)
     - ⚪ **Belum Tiba** (Masa mendatang)
2. **Desain Chip yang Lebih Deskriptif & Bersih**:
   - Mengganti singkatan kaku `M-1` menjadi format yang lebih manusiawi: **`Minggu 1`**, **`Minggu 2`** atau badge berpenanda tanggal ringkas.
   - Menghilangkan scrollbar kasar native browser dengan *custom styled seamless scroll* atau navigasi swipe yang mulus.
3. **Interaktivitas Klik (Click-to-Navigate)**:
   - Menjadikan chip minggu dapat diklik (`cursor: pointer`). Ketika diklik:
     - Jika minggu tersebut sudah terisi: halaman otomatis melakukan *smooth-scroll* ke kartu logbook minggu bersangkutan dan memberikan efek *highlight*.
     - Jika minggu tersebut adalah minggu aktif dan belum diisi: membuka modal form *"Isi Logbook Mingguan"*.

### B. Menambahkan Banner Panduan Singkat Pengguna Awal (*Onboarding Guide Strip*)
- Di bawah Tracker Progres, ditambahkan kotak panduan ringkas 3 langkah:
  - **Langkah 1**: Catat ringkasan aktivitas kerja dan capaian mingguan Anda.
  - **Langkah 2**: Unggah dokumentasi foto/PDF bukti kegiatan magang (jika ada).
  - **Langkah 3**: Kirim laporan ke pembimbing sebelum akhir pekan untuk dievaluasi.
- Terdapat tombol *"Tutup Panduan"* (dismissible) agar tidak mengganggu jika pengguna sudah terbiasa.

---

## 3. Rincian File yang Akan Dimodifikasi
- **Target File**: [`resources/views/peserta/logbook/index.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/logbook/index.blade.php)
- **Komponen**:
  1. Blok `.progress-tracker-card` (CSS dan Blade HTML).
  2. Penambahan baris status legend `.tracker-legend-bar`.
  3. Penambahan script interaktivitas klik chip menuju kartu logbook.
  4. Penambahan banner panduan onboarding ringkas bagi peserta baru.

---

## 4. Rencana Verifikasi
1. **Uji Render Sintaks Blade**: Memastikan tidak ada error sintaks PHP atau tag HTML tak tertutup.
2. **Automated Feature Tests**: Menjalankan test feature logbook peserta (`php artisan test --filter=Logbook`).
3. **Pengecekan Responsivitas**: Memastikan chip tracker dan legenda tampil rapi di layar desktop maupun mobile.

---

## 5. Pertanyaan Konfirmasi Pengguna
Apakah rencana perbaikan tampilan dan UX progres logbook ini disetujui untuk mulai diimplementasikan?
