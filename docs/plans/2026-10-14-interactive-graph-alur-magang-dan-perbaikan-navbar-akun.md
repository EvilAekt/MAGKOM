# Plan: Peta Interaktif Alur Magang (Dedicated Screen & Interactive Graph) & Perbaikan Visual Pop-Up Akun

- **Tanggal**: 2026-10-14
- **Status**: Draft (Menunggu Persetujuan Pengguna)
- **Author**: Antigravity (web-planning)
- **Rujukan Standar Dokumen**:
  - [`01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (Bab 1.2, 5, 6 Alur 4 Fase & Aturan Bisnis BR-01 s/d BR-10, Bab 7 Acceptance Criteria)
  - [`02-TDD-MagKom.md`](file:///g:/MAGKOM/docs/02-TDD-MagKom.md) (Bab 4 Arsitektur Komponen, 8 Keamanan & CSP)
  - [`03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md`](file:///g:/MAGKOM/docs/03-ROADMAP-DAN-PANDUAN-EKSEKUSI.md) (Quality Gates & Pengujian)
  - [`07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Palet Warna Resmi Sleman, Tipografi Open Sans, Elevated Cards, Anti-AI-Slop)

---

## 1. Tujuan

Berdasarkan masukan pengguna dan evaluasi visual:
1. **Perbaikan Navbar & Pop-up Menu Akun (Mengatasi Bug Visual Gambar 1)**:
   - Menghapus elemen kode logout duplikat yang bocor di navbar di luar container dropdown (`resources/views/layouts/app.blade.php:1055-1063`).
   - Memperbaiki pemotongan teks (*truncation*) pada item menu dropdown akun: memperlebar dropdown dari 295px menjadi `320px–330px`, mengatur layout dua baris yang proporsional, sehingga teks deskripsi tidak terpotong kaku ("Status pendaftaran, tugas & log...").
2. **Peta Interaktif Alur Magang di Layar Tersendiri (Dedicated Interactive Screen - Gambar 2)**:
   - Mengubah alur magang dari sekadar modal pop-up menjadi **halaman penuh (dedicated screen)** di rute baru `/panduan-alur` (`panduan-alur`).
   - Menyajikan visualisasi **Interactive Knowledge Graph & Journey Flowchart**:
     - Diagram graf interaktif dengan node-node tahapan magang, garis konektor dinamis beranimasi flow, dan percabangan status seleksi (*Diterima*, *Waiting List*, *Perlu Perbaikan*).
     - **Simulator Jalur Magang (Path Simulator)**: Calon peserta dapat memilih simulator profilnya (Jenjang: SMK vs Mahasiswa, Tipe: Mandiri vs Kelompok) dan diagram graf akan secara cerdas menyorot (*highlight*) jalur serta persyaratan spesifiknya.
     - **Node Detail Inspector (Interactive Side Drawer/Card)**: Mengklik sembarang node akan membuka detail lengkap tahapan, dokumen yang dibutuhkan, aturan bisnis PRD (BR-01 s/d BR-10), SLA waktu, dan tombol aksi cepat (*quick CTA*).
     - **Dual-View Switcher**: Memungkinkan pengguna beralih antara *Interactive Graph Diagram View* dan *Linear Step-by-Step Roadmap View* yang sangat nyaman untuk smartphone/mobile.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi:
1. **Routing & URL**:
   - Rute publik baru didaftarkan pada `/panduan-alur` dengan nama `panduan-alur` yang dapat diakses publik tanpa login.
   - Tombol "Lihat panduan lengkap" di landing page (`#alur-section`) akan menjadi link langsung `href="{{ route('panduan-alur') }}"` dengan label menarik: *"Eksplorasi Peta Alur Interaktif &rarr;"*.
   - Navigasi navbar *"Alur magang"* tetap dapat mengarahkan ke `#alur-section` landing page atau langsung ke halaman peta interaktif `/panduan-alur`.
2. **Teknologi Visualisasi Graph**:
   - Menggunakan kombinasi SVG interaktif murni (pure SVG + CSS animations + vanilla JS) tanpa library eksternal yang berat, sehingga performa kilat (sub-100ms load time), ramah CSP (Content Security Policy), responsif, dan 100% mendukung tema terang/gelap (`[data-theme="dark"]`).
3. **Data Alur 4 Fase PRD**:
   - Sesuai Bab 6 `01-PRD-MagKom.md`:
     - **Fase 1 (Pendaftaran & Kuota)**: Pilihan SMK vs Mahasiswa, Mandiri vs Kelompok, berkas wajib format PDF (Surat Pengantar, CV, Transkrip/Rapor, Proposal), aturan kuota 1 periode (BR-01, BR-03).
     - **Fase 2 (Verifikasi & Seleksi)**: SLA verifikasi 1–3 hari kerja, percabangan status (*Diajukan*, *Diterima*, *Waiting List FIFO*, *Perlu Perbaikan 3x24 jam*, *Ditolak*), SK Diterima resmi dengan QR Code (BR-05).
     - **Fase 3 (Pelaksanaan & Bimbingan)**: Orientasi kantor dinas, penetapan mentor teknis, pengerjaan tugas, kewajiban logbook harian dengan batas mundur maksimal H-1 (BR-09), jam kerja 07.30–16.00 WIB.
     - **Fase 4 (Evaluasi & Kelulusan)**: Laporan akhir, penilaian rubrik 100 poin, penerbitan Lembar Nilai PDF resmi & Sertifikat Digital QR Code (AC-12), masa tenggang baca-saja 30 hari kalender sebelum pengarsipan otomatis (BR-10).

### Pertanyaan Terbuka:
- Tidak ada pertanyaan yang menghalangi. Kebutuhan user sangat jelas: menghilangkan pop-up yang sempit, memindahkan ke halaman tersendiri yang interaktif dengan diagram alur grafis.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Visualisasi Alur:
- **Opsi A (Dedicated Screen dengan Interactive SVG Node Graph + Side Inspector - Direkomendasikan)**:
  - Membuat halaman `/panduan-alur` yang meng-extend `layouts.app`.
  - Area graf memuat diagram alur bercabang dengan node visual berikon, konektor dinamis, dan efek glow.
  - Terdapat kontrol simulator (SMK / Mahasiswa, Mandiri / Kelompok) yang menyalakan jalur relevan.
  - Terdapat panel inspektor detail yang muncul dinamis saat node diklik.
  - Terdapat tombol toggle mode: `[☊ Diagram Graf]` dan `[☰ Roadmap Linier]`.
  - *Kelebihan*: Tampilan sangat berkelas, interaktif, responsif, tidak membebani performa browser, wow factor tinggi, anti-AI-slop.
- **Opsi B (Library Graf Pihak Ketiga seperti Vis.js atau Cytoscape.js)**:
  - Memasang dependensi npm eksternal untuk rendering canvas graph.
  - *Kelemahan*: Menambah ukuran bundle file, memerlukan penyesuaian CSP script/styles, styling CSS kustom sulit diselaraskan dengan Design System MagKom.
- **Rekomendasi**: **Opsi A** untuk performa terbaik, zero external dependency, dan keselarasan 100% dengan token Design System MagKom.

---

## 4. Rincian Antarmuka & Skema Desain (UI/UX)

### A. Perbaikan Navbar & Dropdown Akun
```
+--------------------------------------------------------------------------+
|  LOGO MAGKOM             Beranda  Tentang  Alur  Kontak   [Avatar] Budi  |
+--------------------------------------------------------------------------+
                                                      |
                                                      v
                                        +----------------------------------+
                                        | [Avatar 42px]  Budi Santoso      |
                                        | budi.santoso@gmail.com           |
                                        | (•) PESERTA MAGANG               |
                                        +----------------------------------+
                                        | [ Icon ] Dashboard Utama       > |
                                        | Ringkasan pendaftaran & tugas    |
                                        +----------------------------------+
                                        | [ Icon ] Profil Saya           > |
                                        | Kelola biodata & riwayat berkas  |
                                        +----------------------------------+
                                        | [ Icon ] Pengaturan Akun       > |
                                        | Keamanan kata sandi & tema       |
                                        +----------------------------------+
                                        | -------------------------------- |
                                        | [ Icon ] Keluar (Logout)       → |
                                        | Akhiri sesi pada perangkat ini   |
                                        +----------------------------------+
```
*(Tidak ada lagi teks logout ganda di luar dropdown, lebar 325px dengan teks deskripsi yang utuh tanpa pemotongan canggung)*.

---

### B. Halaman Dedicated `/panduan-alur` (Peta Interaktif Alur Magang)
```
+---------------------------------------------------------------------------------------------------+
|  [PANDUAN RESMI DISKOMINFO SLEMAN]                                                                |
|  Peta Interaktif Alur & Tahapan Magang                                                            |
|  Simulasi visual langkah demi langkah dari pendaftaran hingga penerbitan sertifikat resmi          |
+---------------------------------------------------------------------------------------------------+
|  SIMULATOR JALUR ANDA:                                                                            |
|  Jenjang: [ Semua (Full Map) ]  [ Siswa SMK ]  [ Mahasiswa D3/S1 ]                                |
|  Tipe:    [ Mandiri (1 Orang) ]  [ Kelompok (2-5 Orang) ]          [ ☊ Graf View ] [ ☰ Roadmap ]  |
+---------------------------------------------------------------------------------------------------+
|                                                                 |                                 |
|  [INTERACTIVE SVG NODE GRAPH]                                   |  [INSPECTOR DETAIL TAHAP]       |
|                                                                 |                                 |
|  (Node 1: Pilih Jalur & Kuota)                                  |  Fase 2: Verifikasi & Seleksi   |
|         │                                                       |  Status: Wajib Semua Peserta    |
|         ▼                                                       |                                 |
|  (Node 2: Registrasi Akun)                                      |  ⏱ SLA: 1–3 Hari Kerja          |
|         │                                                       |  Aktor: Tim Admin Diskominfo    |
|         ▼                                                       |                                 |
|  (Node 3: Unggah Berkas Persyaratan)                            |  Pemeriksaan Dokumen:           |
|         │                                                       |  • Surat Pengantar Kampus (PDF) |
|         ▼                                                       |  • CV & Portofolio (PDF)        |
|  (Node 4: Verifikasi Admin Diskominfo)                          |  • Transkrip Nilai (PDF)        |
|         ├───► [ 🟢 Diterima (SK Resmi Digital) ] ──► (Fase 3)  |                                 |
|         ├───► [ 🟡 Waiting List (FIFO) ]                       |  ⚠️ Aturan Bisnis (BR-05):      |
|         └───► [ 🔴 Perlu Perbaikan (3x24 Jam) ]                 |  Konfirmasi kehadiran wajib     |
|                                                                 |  dilakukan setelah SK terbit.   |
|                                                                 |                                 |
|                                                                 |  [ Cek Kuota Magang Sekarang → ]|
+---------------------------------------------------------------------------------------------------+
|  [RINGKASAN ATURAN BISNIS & PERTANYAAN UMUM (FAQ ACCORDION)]                                      |
+---------------------------------------------------------------------------------------------------+
```

---

## 5. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/layouts/app.blade.php`](file:///g:/MAGKOM/resources/views/layouts/app.blade.php) | Modify | 1. Hapus markup tombol logout duplikat di baris 1055-1063.<br>2. Perbaiki lebar dropdown menu akun dari 295px menjadi 325px, atur padding dan line-height teks deskripsi agar tampil rapi tanpa terpotong kasar.<br>3. Perbarui link navbar alur magang jika diperlukan. |
| [`routes/web.php`](file:///g:/MAGKOM/routes/web.php) | Modify | Daftarkan rute publik baru: `Route::get('/panduan-alur', [AlurMagangController::class, 'index'])->name('panduan-alur');` (atau controller publik yang sesuai). |
| [`app/Http/Controllers/Public/AlurMagangController.php`](file:///g:/MAGKOM/app/Http/Controllers/Public/AlurMagangController.php) | Create | Controller publik untuk menangani rendering halaman peta interaktif alur magang dengan data fase, tahapan, berkas, dan aturan bisnis PRD. |
| [`resources/views/public/panduan-alur.blade.php`](file:///g:/MAGKOM/resources/views/public/panduan-alur.blade.php) | Create | Template Blade halaman baru yang memuat: Hero section interaktif, simulator filter (SMK/Mahasiswa, Mandiri/Kelompok), Interactive Node Graph SVG dengan animated connectors, Side Inspector panel, Timeline view toggle, dan FAQ alur magang. |
| [`resources/views/public/landing.blade.php`](file:///g:/MAGKOM/resources/views/public/landing.blade.php) | Modify | 1. Hapus modal popup dialog lama `#panduanAlurModal` beserta JS terkait yang tidak lagi dibutuhkan.<br>2. Ubah tombol *"Lihat panduan lengkap"* pada Section 3 menjadi tautan langsung menuju halaman `/panduan-alur` dengan style CTA yang memikat. |
| [`tests/Feature/AlurMagangPageTest.php`](file:///g:/MAGKOM/tests/Feature/AlurMagangPageTest.php) | Create | Pengujian otomatis untuk rute `/panduan-alur`: memastikan halaman memuat 4 fase PRD, diagram node graph, simulator jenjang, aturan bisnis kuota, dan responsive view toggle. |
| [`tests/Feature/LandingPageTest.php`](file:///g:/MAGKOM/tests/Feature/LandingPageTest.php) | Modify | Sesuaikan assertion tombol alur magang yang kini mengarah ke `route('panduan-alur')`. |

---

## 6. Langkah Implementasi

- [x] **Langkah 1**: Perbaiki bug navbar di [`resources/views/layouts/app.blade.php`](file:///g:/MAGKOM/resources/views/layouts/app.blade.php):
  - Hapus sisa kode duplikat logout di baris 1055-1063.
  - Sesuaikan CSS `.landing-dropdown-menu` (lebar 325px, padding proporsional, deskripsi multi-line nyaman tanpa terpotong kasar).
- [x] **Langkah 2**: Buat controller publik [`app/Http/Controllers/Public/AlurMagangController.php`](file:///g:/MAGKOM/app/Http/Controllers/Public/AlurMagangController.php) dan daftarkan route `Route::get('/panduan-alur', AlurMagangController::class)->name('panduan-alur');` di [`routes/web.php`](file:///g:/MAGKOM/routes/web.php).
- [x] **Langkah 3**: Buat view baru [`resources/views/public/panduan-alur.blade.php`](file:///g:/MAGKOM/resources/views/public/panduan-alur.blade.php) dengan spesifikasi:
  - Hero header dengan badge resmi Diskominfo dan ringkasan 4 fase alur.
  - Simulator bar interaktif (Jenjang: Semua, SMK, Mahasiswa; Tipe: Mandiri, Kelompok; View: Graph vs Timeline).
  - Visualisasi Interactive Node Graph berbasis SVG responsif dengan pulsing node circles, connectors dinamis, dan percabangan status (Diterima / Waiting List / Perlu Perbaikan).
  - Interactive Side Inspector Panel yang menampilkan detail tahapan, berkas wajib, SLA, dan aturan bisnis PRD saat sebuah node diklik.
  - Tampilan Roadmap Linier alternatif untuk smartphone/mobile yang nyaman di-scroll.
  - Accordion Tanya Jawab / Aturan Bisnis Kunci Diskominfo (BR-01, BR-05, BR-09, BR-10, AC-12).
  - Quick CTA banner menuju kalender ketersediaan kuota.
- [x] **Langkah 4**: Perbarui Section 3 Alur Magang di [`resources/views/public/landing.blade.php`](file:///g:/MAGKOM/resources/views/public/landing.blade.php):
  - Ubah tombol *"Lihat panduan lengkap"* menjadi tautan navigasi langsung ke `route('panduan-alur')`.
  - Bersihkan modal popup dialog `#panduanAlurModal` dan script lama agar landing page tetap bersih dan ringan.
- [x] **Langkah 5**: Buat automated feature test baru di [`tests/Feature/AlurMagangPageTest.php`](file:///g:/MAGKOM/tests/Feature/AlurMagangPageTest.php) dan perbarui [`tests/Feature/LandingPageTest.php`](file:///g:/MAGKOM/tests/Feature/LandingPageTest.php).
- [x] **Langkah 6**: Jalankan `php artisan view:clear` dan eksekusi seluruh automated test suite (`php artisan test`) untuk memastikan 100% test lulus tanpa regresi.
- [x] **Langkah 7**: Lakukan inspeksi visual menggunakan browser subagent untuk menguji:
  - Tampilan navbar dan dropdown profil akun yang sudah rapi dan tidak bocor.
  - Navigasi dari landing page ke `/panduan-alur`.
  - Interaktivitas graph: klik node, pergantian simulator SMK/Mahasiswa, pergantian tampilan Graph vs Timeline.
- [x] **Langkah 8**: Sinkronkan knowledge graph proyek dengan `graphify update .`.

---

## 7. Strategi Testing

### Automated Unit & Feature Test:
- Memastikan `GET /panduan-alur` mengembalikan status 200 dengan struktur view lengkap.
- Memastikan simulator filter, node-node 4 fase PRD, dan ketentuan berkas ter-render dengan benar.
- Memastikan tombol di landing page mengarah ke `/panduan-alur`.
- Memastikan seluruh test suite eksisting (372 tests) tetap **100% PASS**.

### Visual & Browser Verification:
- Tangkap screenshot navbar saat login untuk memverifikasi hilangnya tombol logout ganda dan terbacanya teks dropdown secara utuh.
- Tangkap screenshot halaman `/panduan-alur` saat graph aktif, saat node di-klik dan panel inspector terbuka, serta saat simulator SMK/Mahasiswa diaktifkan.

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Tombol logout duplikat di navbar berhasil dibuang dan tampilan dropdown akun rapi serta tidak terpotong.
- [ ] Tombol "Lihat panduan lengkap" di landing page mengarahkan pengguna ke halaman baru `/panduan-alur` (bukan modal pop-up sempit).
- [ ] Halaman `/panduan-alur` menghadirkan peta interaktif (Interactive Node Graph & Journey Flowchart) dengan simulator jalur (SMK/Mahasiswa, Mandiri/Kelompok) dan panel detail inspector yang kaya informasi.
- [ ] Tersedia toggle tampilan Graph View dan Timeline View yang responsif di desktop maupun mobile.
- [ ] Seluruh aturan bisnis resmi PRD (BR-01 s/d BR-10, AC-12) dan token DS-MagKom terintegrasi secara konsisten.
- [ ] Seluruh automated test suite (372+ tests) berstatus 100% PASS.
- [ ] Verifikasi visual browser berhasil dan terdokumentasi dengan baik.
