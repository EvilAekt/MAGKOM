# Rencana Kerja: Penyempurnaan Tampilan & Ergonomi Visual Dashboard Peserta

- **Tanggal Pembuatan**: 2026-11-06
- **Status**: Selesai Diimplementasikan (*Completed*)
- **Penanggung Jawab**: AI Pair Programmer (Antigravity)
- **Referensi Desain**: Tangkapan Layar Pengguna (`media_1793928668390_d1245c1e.png`) & Sleman Navy Design System

---

## 1. Tujuan
Memperbaiki dan menyempurnakan ergonomi visual, tata letak grid responsif, tipografi, dan kerapian komponen kartu pada Dashboard Peserta (`resources/views/peserta/dashboard.blade.php`), partials aktif (`_phase_aktif.blade.php`), dan panel pendamping (`_aside.blade.php`) agar tidak lagi tampak sempit, terpotong (*awkward truncation*), atau kaku.

---

## 2. Identifikasi Masalah Visual (Berdasarkan Bukti Tangkapan Layar)

| Area / Komponen | Masalah Saat Ini | Solusi / Standar yang Diharapkan |
| :--- | :--- | :--- |
| **1. Statusline Ribbon (Atas)** | Tampak datar (*flat*), kosong, teks kecil kaku tanpa ikon visual pendukung, garis atas terlalu mencolok. | Diperkaya dengan micro-badge berikon elegan (Status, Tipe, Penempatan, Pembimbing), background kartu lebih berdimensi lembut (*soft elevation*) dengan border halus. |
| **2. Layout Grid 3 Kolom Sempit** | Lebar kolom utama terbagi menjadi 2 card (Tugas & Logbook) + 1 aside (Pembimbing) di kanan (~330px per kolom), menyebabkan teks saling berhimpitan dan wrap berantakan. | Optimasi proporsi container & responsive grid: card Tugas dan Logbook diberikan ruang lega dengan minmax fleksibel atau stack vertical yang lebih proporsional pada viewport menengah (laptop/desktop standar). |
| **3. Card Tugas Magang Mendesak** | Judul tertekuk (*awkward wrap*), judul tugas terpotong elipsis terlalu dini ("Slicing & Integrasi Fronten..."), baris metadata tanggal dan kategori pecah 2 baris canggung, tombol "Detail" kaku. | Header kartu dirapikan (judul & link lebih proporsional), item list tugas diberi layout 2 baris rapi (Baris 1: Judul Tugas penuh + Tombol Detail, Baris 2: Badge Deadline berikon jam + Kategori pill), tombol dirancang modern dan empuk (*rounded soft button*). |
| **4. Card Logbook Mingguan Terkini** | Judul header tertekuk 3 baris, ringkasan aktivitas terhimpit parah oleh 2 badge di kanan (`[Disetujui]` + `[Lihat]`), elipsis teks kegiatan sangat pendek ("Menjalankan penguji..."). | Tata letak item logbook ditata ulang: Badge Minggu & Status berada di baris atas / header item, teks kegiatan diberikan ruang horizontal penuh (hingga 2 baris bersih), aksi "Lihat" dibuat ringkas berikon. |
| **5. Card Profil Pembimbing** | Nama pembimbing terpotong elipsis ("Budi Santoso, S.Kom (Pembimbi..."), serta terdapat **bug typo teks ganda**: *"Bidang Bidang Aplikasi Informatika (Aptika)"*. | Perbaiki typo teks ganda menjadi *"Bidang Aplikasi Informatika (Aptika)"*, buat nama pembimbing fleksibel membungkus (*wrap*) dengan anggun, percantik kartu kontak & tombol WhatsApp/Helpdesk dengan gradien Sleman Navy. |

---

## 3. Evaluasi Opsi Pendekatan Teknis

### Opsi 1: Rekonfigurasi Tata Letak Grid & Redesain Komponen Scoped (Direkomendasikan)
- **Cara Kerja**:
  1. Menata ulang breakpoint dan proporsi CSS grid di `resources/views/peserta/dashboard.blade.php` agar card Tugas dan Logbook tidak saling menjepit pada resolusi desktop standar (1024px - 1440px).
  2. Merestrukturisasi tata letak internal (*flex-row / flex-col*) item Tugas dan Logbook di partial `_phase_aktif.blade.php` sehingga judul, tanggal, status, dan tombol aksi tertata berjenjang rapi tanpa terpotong kaku.
  3. Memperbaiki bug string literal ganda `Bidang Bidang...` di `_aside.blade.php`.
  4. Meningkatkan estetika visual tombol (*micro-interactions*, padding seimbang, dan badge berstatus warna pastel elegan).
- **Kelebihan**:
  - Tampilan langsung menjadi lapang, modern, dan sangat nyaman dibaca.
  - Bebas dari pemotongan teks prematur.
  - Mempertahankan integritas data dan relasi backend yang sudah ada.
- **Kekurangan**:
  - Memerlukan penyesuaian markup Blade partials untuk merapikan pembagian baris teks.

### Opsi 2: Hanya Mengubah CSS Saja Tanpa Menyentuh Markup Blade
- **Cara Kerja**: Hanya mengatur `font-size`, `letter-spacing`, dan `grid-template-columns` via CSS.
- **Kelebihan**: Tidak menyentuh markup HTML partials.
- **Kekurangan**:
  - Tidak dapat menyelesaikan masalah typo ganda *"Bidang Bidang..."*.
  - Tidak dapat mengatasi keterbatasan struktur flex item pada logbook yang menaruh badge status dan tombol sejajar dengan teks kegiatan di ruang sempit. Tampilan tetap akan terasa penuh sesak.

> **Keputusan**: Menggunakan **Opsi 1** untuk perbaikan menyeluruh yang tuntas secara estetika dan fungsional.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/dashboard.blade.php` | Modify | Perbaiki grid template `.saas-cockpit-grid` & `.saas-phase-grid`, percantik Ribbon Statusline dengan micro-card berikon, update styling tombol & card header |
| `resources/views/peserta/dashboard/partials/_phase_aktif.blade.php` | Modify | Rapikan struktur item Tugas (judul penuh di baris atas, deadline & kategori di baris bawah) dan item Logbook (badge minggu & status di atas, teks kegiatan leluasa di bawah) |
| `resources/views/peserta/dashboard/partials/_aside.blade.php` | Modify | Perbaiki typo string ganda `Bidang Bidang...`, izinkan nama pembimbing membungkus natural tanpa pemotongan canggung |
| `docs/plans/2026-11-06-penyempurnaan-tampilan-dashboard-peserta.md` | Create | Dokumen rencana kerja terstruktur |

---

## 5. Langkah Implementasi (Checklist)

- [x] **Langkah 1: Perbaikan Typo & Kartu Profil Pembimbing (`_aside.blade.php`)**
  - Periksa `<span>{{ Str::startsWith($penempatan->bidang->nama_bidang, 'Bidang') ? $penempatan->bidang->nama_bidang : 'Bidang ' . $penempatan->bidang->nama_bidang }}</span>` agar tidak menghasilkan kata "Bidang" berulang.
  - Hilangkan pembatasan `white-space: nowrap` berlebih pada nama pembimbing agar gelar dan nama lengkap terbaca utuh.

- [x] **Langkah 2: Rekonfigurasi Grid & Header Card di `dashboard.blade.php`**
  - Sesuaikan `saas-cockpit-grid` agar aside berukuran fleksibel (310px) dan kolom utama mendapatkan porsi maksimal.
  - Perbaiki header card (`.saas-card-header`) dengan tata letak flex yang tidak memaksa teks tertekuk 3 baris.
  - Percantik ribbon statusline horizontal atas dengan ikon semantik Sleman Navy dan elevation lembut.

- [x] **Langkah 3: Tata Ulang Item Tugas Magang (`_phase_aktif.blade.php`)**
  - Format ulang layout item tugas menjadi 2 baris bersih:
    - **Baris 1**: Judul tugas (font bobot 600, kontras tinggi) + Tombol `Detail` (pill modern berhover halus).
    - **Baris 2**: Indikator tenggat waktu berikon jam kalender + Badge kategori tugas.

- [x] **Langkah 4: Tata Ulang Item Logbook Mingguan (`_phase_aktif.blade.php`)**
  - Format ulang layout item logbook:
    - **Baris 1**: Badge `Minggu X` + Tanggal + Badge Status (`Disetujui` / `Terkirim` / `Perlu Revisi`) + Link/Tombol `Lihat`.
    - **Baris 2**: Cuplikan isi kegiatan logbook yang membentang horizontal penuh (hingga 2 baris teks yang leluasa terbaca).

- [x] **Langkah 5: Penyelarasan Tombol & Micro-Interactions**
  - Update styling tombol `.saas-btn-detail` dan `.saas-link-more` agar memiliki border radius proporsional (8px), warna Sleman Navy (`#01347C`), dan hover effect elegan.

- [x] **Langkah 6: Quality Gate & Verifikasi**
  - Uji build Vite: `npm run build`.
  - Uji test suite: `php artisan test --filter=PendaftaranPesertaTest`.
  - Inspeksi visual tampilan dashboard peserta di mode terang dan mode gelap.

---

## 6. Strategi Testing

1. **Pengujian Tampilan Visual**:
   - Buka `http://127.0.0.1:8000/peserta/dashboard`.
   - Pastikan tidak ada judul atau teks penting yang terpotong canggung (...).
   - Pastikan teks *"Bidang Aplikasi Informatika (Aptika)"* tidak lagi dobel kata "Bidang".
   - Periksa responsivitas pada viewport laptop standar (1366x768) dan layar lebar (1920x1080).
2. **Pengujian Fungsional**:
   - Pastikan tombol "Detail" pada tugas mengarah tepat ke `route('peserta.tugas.show', $t)`.
   - Pastikan tombol "Lihat" pada logbook mengarah ke `route('peserta.logbook.index')`.
   - Pastikan tombol "Hubungi via WhatsApp / Helpdesk" tetap membuka tautan kontak pembimbing yang valid.
3. **Automated Test**:
   - Jalankan `php artisan test` untuk memastikan seluruh alur peserta tetap PASS.

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Perubahan Struktur Item Logbook/Tugas** | Rendah | Menggunakan data model yang sama persis (`$tugasMendesak`, `$logbookTerbaru`), hanya menata ulang markup layout agar fleksibel dan lapang. |
| **Dampak pada Mode Gelap** | Rendah | Menggunakan token CSS yang sudah mendukung `[data-theme="dark"]` sehingga warna background dan teks otomatis menyesuaikan. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Statusline atas tampil rapi, modern, dan informatif dengan ikon semantik.
- [x] Card Tugas Magang menampilkan judul dan metadata secara proporsional tanpa teks terpotong aneh.
- [x] Card Logbook menampilkan ringkasan aktivitas secara leluasa dengan badge status tertata rapi.
- [x] Typo string ganda *"Bidang Bidang..."* pada profil pembimbing diperbaiki.
- [x] Tampilan responsif dan proporsional pada desktop maupun mobile.
- [x] `npm run build` dan automated test berjalan sukses tanpa error.

