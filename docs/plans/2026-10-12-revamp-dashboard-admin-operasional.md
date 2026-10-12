# Rencana Redesain Profesional: Dashboard Operasional Admin (Enterprise & Human-Centered)

Dokumen ini memuat rencana perombakan total UI/UX **Dashboard Admin** (`admin.dashboard`) berdasarkan evaluasi visual nyata. Tujuannya adalah mengeliminasi kesan kaku template AI generic, menghilangkan elemen canggung (seperti scrollbar horizontal pada tab, label spek internal PRD/BR, dan warna pelangi acak), serta menggantinya dengan layout 2-kolom kelas enterprise (*SaaS / GovTech Modern*) yang ergonomis, elegan, dan ramah pengguna.

---

## 1. Evaluasi Masalah Visual Nyata (Berdasarkan Screenshot)

1. **Efek "Template AI Generic" & Penumpukan Kotak Monoton**:
   - Halaman terdiri dari 5 balok putih memanjang yang ditumpuk vertikal dari atas ke bawah tanpa ritme (*visual rhythm*) dan tanpa hierarki ruang (*spatial hierarchy*).
   - Card warna-warni pelangi (oranye, biru, ungu, hijau) di bagian stat dan footer yang terlihat seperti tema bootstrap kuno, bukan sistem desain pemerintahan modern.
2. **Tab Scrollbar Horizontal yang Merusak Tampilan**:
   - 6 tab dijejerkan secara horizontal di dalam satu kontainer sempit sehingga memicu scrollbar abu-abu tebal di tengah halaman. Ini sangat tidak ramah pengguna (*bad UX*).
3. **Pencemaran Jargon Teknis PRD/BR ke Antarmuka Pengguna**:
   - Muncul teks seperti `(BR-01)`, `(BR-02)`, `(BR-05)`, `(BR-13)`, `(PRD 2.3)`, dan `(F-16)` langsung di muka layar. Admin dinas adalah manusia pengguna sistem, bukan mesin pembaca dokumen spesifikasi.
4. **Hero Banner & Stat Strip Terlalu Banyak Ruang Kosong**:
   - Hero banner kuota menghabiskan ruang vertikal besar hanya untuk menampilkan beberapa baris teks dan meter kecil.
   - Stat cards 4 kolom hanya berisi angka single digit tanpa konteks aksi yang bermakna.

---

## 2. Solusi Desain Baru: Split Grid 2-Kolom (Modern Operational Cockpit)

Alih-alih menumpuk balok dari atas ke bawah dengan tab ber-scrollbar, kita gunakan **Dashboard 2 Kolom Komprehensif (Asymmetric Grid 68% : 32%)**:

```
+-----------------------------------------------------------------------------------+
|  HEADER: Title & Subtitle             [Periode Dropdown] [Ekspor] [Hub Sertifikat]|
+-----------------------------------------------------------------------------------+
|  KPI BAR (4 Metrik Kompak & Terpadu: Verifikasi | Revisi | Waiting List | Aktif)  |
+-----------------------------------------------------------------------------------+
|  KOLOM KIRI (UTAMA - 68%)                 |  KOLOM KANAN (SIDEBAR INSIGHT - 32%)  |
|                                           |                                       |
|  1. ANTREAN VERIFIKASI PRIORITAS          |  1. STATUS PERIODE & KUOTA UMUM       |
|     - Tabel bersih tanpa tab kaku         |     - Card ringkas status buka/tutup  |
|     - Filter filter pill: Semua / Diajukan|     - Meter kapasitas kuota terisi    |
|     - SLA badge manusiawi (e.g. "Hari ini") |                                     |
|     - Tombol Tinjau proporsional          |  2. OFFBOARDING WATCHDOG              |
|                                           |     - List peserta mendekati selesai  |
|  2. MONITORING KUOTA PER JURUSAN          |                                       |
|     - Progress bar kapasitas tiap jurusan |  3. BEBAN PEMBIMBING LAPANGAN         |
|     - Indikator sisa kuota                |     - Mini list workload mentor       |
|                                           |                                       |
|  3. TIMELINE & JADWAL GELOMBANG           |  4. TAUTAN OPERASIONAL CEPAT          |
|     - Ringkasan gelombang tahun berjalan  |     - Navigasi ringkas satu warna     |
+-------------------------------------------+---------------------------------------+
```

---

## 3. Rincian Peningkatan Komponen

### A. Header Bersih & Profesional (No "AI Gimmicks")
- Hapus badge "LIVE COMMAND CENTER" yang berlebihan.
- Buat header berwibawa: Judul "Dashboard Operasional", status titik hijau halus bertuliskan "Sistem Aktif", dan filter bulan yang terintegrasi rapi dengan tombol aksi.
- Tombol aksi menggunakan hierarki tegas: 1 primary solid (`#0641CC` untuk Hub Sertifikat) dan 1 secondary outline untuk Ekspor.

### B. KPI Metrics Bar Terintegrasi
- 4 kartu stat dibuat lebih ramping dan elegan dengan border netral `#E2E8F0` serta accent bar tipis atau background subtle.
- Hapus kode BR-05 / BR-01. Gunakan label manusiawi:
  - **Perlu Verifikasi**: Menampilkan berkas masuk + highlight merah lembut jika ada yang tertahan lama.
  - **Perbaikan Berkas**: Menampilkan jumlah revisi yang ditunggu dari peserta.
  - **Antrean Cadangan**: Peserta dalam daftar tunggu.
  - **Peserta Aktif**: Peserta yang sedang aktif magang di dinas.

### C. Kolom Utama (Kiri): Fokus Aksi & Data Kerja Admin
1. **Tabel Antrean Verifikasi Prioritas**:
   - Langsung terlihat di atas tanpa perlu klik tab atau scroll samping!
   - Desain tabel modern: Avatar inisial dengan warna pastel dinamis, nama, email, institusi, jurusan, dan usia berkas.
   - Badge SLA manusiawi: "Hari ini", "2 hari lalu", atau peringatan amber jika "≥ 3 hari".
   - Tombol "Tinjau Berkas" berdesain kompak dan ergonomis.
   - Filter pill cepat (*Semua*, *Diajukan*, *Perlu Perbaikan*) menggunakan filter Javascript instan pada tabel tanpa reload halaman.
2. **Keterisian Kuota per Jurusan**:
   - Ditempatkan di bawah tabel utama.
   - Menampilkan card grid 2 kolom dengan bar keterisian kuota tiap jurusan, persentase, dan badge sisa kursi.
3. **Timeline Gelombang Magang**:
   - Tabel ringkas jadwal gelombang magang dengan tanggal buka/tutup dan persentase kuota.

### D. Kolom Samping (Kanan): Operational Watchlist & Kontrol Ringkas
1. **Status Periode Berjalan**:
   - Card elegan yang merangkum jadwal buka/tutup pendaftaran bulan aktif, sisa hari pendaftaran, serta progress bar akumulasi kuota.
2. **Offboarding Watchdog (Peringatan Kelulusan)**:
   - Menampilkan list peserta yang akan selesai magang dalam 14 hari ke depan dengan countdown badge rapi.
3. **Workload Pembimbing Lapangan**:
   - List pembimbing aktif dengan kapasitas bimbingan saat ini (misal: "X Peserta Aktif").
4. **Pintasan Cepat Menu Admin**:
   - Daftar tautan cepat yang sleek dan monokromatik (tema biru Dinas Kominfo), bukan kotak-kotak pelangi yang acak.

---

## 4. Rincian Perubahan File

| No | File | Deskripsi Rencana Perubahan |
|----|------|-----------------------------|
| 1 | [`resources/views/admin/dashboard.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/admin/dashboard.blade.php) | Mengganti struktur balok bertumpuk & 6 tab ber-scrollbar dengan **Grid 2 Kolom Asimetris**. Menghapus teks kode PRD/BR teknis. Menerapkan typography & spacing modern yang bersih, humanis, dan berkelas enterprise. |

---

## 5. Checklist Implementasi Terurut

- [ ] **Langkah 1: Penyusunan Layout Grid 2 Kolom Responsif**
  - Buat container `.adm-grid-layout` dengan rasio `1fr` pada mobile dan `minmax(0, 2fr) minmax(0, 1fr)` (sekitar 68% : 32%) pada desktop/laptop.
- [ ] **Langkah 2: Redesain Header & KPI Strip**
  - Bersihkan badge berlebihan, pasang breadcrumb rapi, dropdown filter periode, dan 4 kartu metrik dengan tipografi bersih.
- [ ] **Langkah 3: Implementasi Kolom Utama (Kiri)**
  - Card 1: Tabel Antrean Verifikasi Mendesak dengan filter pill instan (Semua / Diajukan / Perlu Perbaikan), avatar pendaftar, SLA status, dan aksi tinjau.
  - Card 2: Visualisasi Alokasi Kuota Jurusan dengan progress bar persentase dan badge ketersediaan.
  - Card 3: Kalender / Timeline Gelombang Magang yang rapi.
- [ ] **Langkah 4: Implementasi Kolom Samping (Kanan)**
  - Widget 1: Card Ringkasan Periode Aktif & Progress Kuota Total.
  - Widget 2: Offboarding Watchdog (Peserta mendekati selesai magang ≤ 14 hari).
  - Widget 3: Beban Pembimbing Lapangan (Mini list beban mentee aktif).
  - Widget 4: Akses Cepat Menu Administrasi dengan styling seragam dan profesional.
- [ ] **Langkah 5: Pembersihan Teks Teknis (Human-Centered Copywriting)**
  - Hapus semua tulisan `(BR-01)`, `(BR-02)`, `(BR-05)`, `(BR-13)`, `(PRD 2.3)`, `(F-16)`. Ganti dengan penjelasan operasional yang natural.
- [ ] **Langkah 6: Pengujian Render & Responsivitas**
  - Jalankan script verifikasi rendering untuk memastikan semua variabel Blade terpasang dengan benar dan tata letak responsif di berbagai resolusi layar.

---

## 6. Definition of Done (DoD)

1. Tidak ada scrollbar horizontal yang canggung di tengah layar.
2. Tidak ada teks teknis PRD/BR yang mengotori UI.
3. Tata letak menggunakan grid 2-kolom yang seimbang, tidak ada tumpukan kotak membosankan.
4. Warna konsisten dengan tema resmi Kominfo Sleman (Primary Blue `#0641CC`, Slate `#0F172A`, netral bersih `#F8FAFC`).
5. Seluruh data operasional (verifikasi, kuota, pembimbing, penempatan, offboarding, gelombang) tetap dapat diakses dengan mudah dan terstruktur.
