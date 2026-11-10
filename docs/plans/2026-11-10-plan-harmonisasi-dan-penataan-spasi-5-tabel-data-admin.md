# Rencana Kerja: Harmonisasi, Penyejajaran Presisi, dan Penataan Spasi 5 Tabel Data Administrasi

**Tanggal:** 10 November 2026  
**Status:** Selesai (Completed & Verified)  
**Tautan Dokumen Rencana:** `docs/plans/2026-11-10-plan-harmonisasi-dan-penataan-spasi-5-tabel-data-admin.md`  
**Referensi:**
- Permintaan Pengguna: Merapikan dan menyejajarkan 5 tabel data administrasi agar tampilan tabel penuh, terstruktur rapi, tertata, proporsional, serta memiliki jarak napas (space) yang pas antarkolom dan antarbaris teks (tidak saling berdesakan atau terlalu berdekatan):
  1. Tabel Data Registrasi (`/admin/pendaftaran`)
  2. Tabel Kuota per Bidang (`/admin/kuota`)
  3. Tabel Data Penempatan Peserta (`/admin/penempatan` - Tab Ditempatkan)
  4. Tabel Belum Ditempatkan (`/admin/penempatan` - Tab Antrean Penempatan)
  5. Tabel Laporan Rekapitulasi Pendaftaran (`/admin/laporan`)
- Aturan Repositori: `AGENTS.md` (PHP 8.3, Laravel 10, Vanilla CSS di `resources/css/`, Zero Test Regression, Semantic Test Compatibility).

---

## 1. Analisis Kebutuhan & Identifikasi Masalah

Berdasarkan 5 tangkapan layar yang dilampirkan oleh pengguna:

1. **Tabel 1 — Data Registrasi (SS 1 - `/admin/pendaftaran`)**:
   - *Masalah:* Lebar kolom menggunakan nilai pixel statis (`width: 140px`, `150px`) sehingga kolom Calon Peserta dan Institusi melebar secara tidak seimbang di monitor lebar. Teks nama dan badge (`Individu`/`Kelompok`) terlalu mepet secara vertikal. Posisi status rata kiri di kolom lebar sehingga terkesan renggang tanpa struktur yang jelas.
   - *Target:* Menerapkan persentase lebar kolom proporsional (total 100%), memberi jarak napas vertikal antara nama dan badge tipe (`margin-top: 4px`), dan menyelaraskan badge status di tengah kolom (`text-align: center`).
2. **Tabel 2 — Kuota per Bidang (SS 2 - `/admin/kuota`)**:
   - *Masalah:* Kolom metrik (Jenjang, Durasi, Kuota Terisi, Kapasitas, Aksi) rata kiri atau tidak simetris terhadap headernya masing-masing, sehingga data terlihat berdesakan dan tidak sejajar tegak lurus dengan judul kolom.
   - *Target:* Menyeimbangkan rasio lebar kolom, mengalokasikan center-alignment pada kolom metrik ringkas (`Jenjang`, `Durasi`, `Kuota Terisi`, `Kapasitas`, `Aksi`), serta memberi padding sel yang lega (`1.15rem 1.25rem`) agar tabel mengisi kontainer secara elegan dan tertata rapi.
3. **Tabel 3 — Data Penempatan Peserta (SS 3 - `/admin/penempatan`)**:
   - *Masalah:* Kolom utama (`Peserta Magang`, `Pembimbing Lapangan`, `Jalur/Bidang`, `Bulan Magang`, `Status`) tidak memiliki persentase lebar pasti, sehingga browser membagi lebar kolom secara arbitrer. Beberapa kolom tampak berdesakan dan status mepet ke kolom periode.
   - *Target:* Menetapkan persentase lebar 100% yang harmonis (25% Peserta, 23% Pembimbing, 21% Bidang, 14% Bulan Magang, 8% Status, 5% Aksi, 4% Checkbox), menyelaraskan badge status di tengah, dan menata hierarki teks (Nama, Institusi, NIM) dengan margin vertikal yang rapi.
4. **Tabel 4 — Belum Ditempatkan (SS 4 - `/admin/penempatan`)**:
   - *Masalah:* Tabel hanya memiliki 5 kolom tanpa persentase lebar, menyebabkan data peserta dan jurusan menumpuk di sisi kiri, menyisakan ruang kosong besar di sisi kanan sebelum tombol `Tempatkan`.
   - *Target:* Mengaplikasikan standarisasi `.adm-table`, mendistribusikan persentase kolom secara merata (30% Peserta, 28% Jurusan, 18% Bulan Magang, 13% Diterima Pada, 11% Aksi), memberi jarak avatar-nama yang proporsional, serta memusatkan tombol aksi `Tempatkan`.
5. **Tabel 5 — Laporan Rekapitulasi Pendaftaran (SS 5 - `/admin/laporan`)**:
   - *Masalah:* Kolom `Durasi` dan `Status` menggunakan perataan kiri standar yang membuat teks durasi dan dot status terlihat menempel dekat ke kolom sebelumnya tanpa pemisah ritme visual yang jelas.
   - *Target:* Menyeimbangkan rasio kolom, mengubah kolom `Durasi` dan `Status` menjadi center-aligned di bawah headernya masing-masing, dan menata stack teks Nama & Institusi dengan jarak vertikal yang bersih.

---

## 2. Rencana Solusi Teknis Per Tabel

### A. Tabel 1: Data Registrasi (`resources/views/admin/pendaftaran/index.blade.php`)
- **Distribusi Kolom (Total 100%)**:
  - `No. Registrasi`: `width: 14%; min-width: 130px; text-align: left;`
  - `Calon Peserta`: `width: 25%; min-width: 210px; text-align: left;`
  - `Institusi & Jurusan`: `width: 25%; min-width: 210px; text-align: left;`
  - `Bulan Magang`: `width: 16%; min-width: 130px; text-align: left;`
  - `Status`: `width: 12%; min-width: 120px; text-align: center;`
  - `Aksi`: `width: 8%; min-width: 90px; text-align: center;`
- **Penyempurnaan Spasi Sel**:
  - User cell: Avatar (38x38px) dengan `margin-right: 0.85rem`, nama font-weight 700, dan badge tipe kelompok/individu diberi `margin-top: 4px`.
  - Institusi cell: Nama institusi (font-weight 600) + jurusan diberi `margin-top: 3px`.
  - Bulan magang cell: Bulan mulai (font-weight 600) + durasi diberi `margin-top: 3px`.
  - Status cell: Dibungkus container flex `justify-content: center;`.
  - Aksi cell: Tombol `Tinjau` terpusat rapi di tengah kolom.

### B. Tabel 2: Kuota per Bidang (`resources/views/admin/kuota/index.blade.php`)
- **Distribusi Kolom (Total 100%)**:
  - `Bidang`: `width: 20%; min-width: 180px; text-align: left;`
  - `Jurusan yang Dibutuhkan`: `width: 34%; min-width: 250px; text-align: left;`
  - `Jenjang`: `width: 10%; min-width: 85px; text-align: center;`
  - `Durasi`: `width: 10%; min-width: 85px; text-align: center;`
  - `Kuota Terisi`: `width: 11%; min-width: 95px; text-align: center;`
  - `Kapasitas`: `width: 9%; min-width: 85px; text-align: center;`
  - `Aksi`: `width: 6%; min-width: 75px; text-align: center;`
- **Penyempurnaan Spasi Sel**:
  - `Jenjang`: Wadah badge dibuat `justify-content: center; gap: 0.35rem;`.
  - `Durasi`: Nilai teks berjarak rapi dan terpusat (`text-align: center;`).
  - `Kuota Terisi` & `Kapasitas`: Format angka bersih terpusat langsung di bawah headernya.
  - `Aksi`: Tombol `[ Ubah ]` berada simetris di tengah kolom aksi.
  - Padding sel baris: Disesuaikan menjadi `padding: 1.1rem 1.25rem;` untuk kenyamanan membaca.

### C. Tabel 3: Data Penempatan Peserta (`resources/views/admin/penempatan/index.blade.php`)
- **Distribusi Kolom (Total 100%)**:
  - `Checkbox`: `width: 4%; min-width: 40px; text-align: center;`
  - `Peserta Magang`: `width: 25%; min-width: 200px; text-align: left;`
  - `Pembimbing Lapangan`: `width: 23%; min-width: 180px; text-align: left;`
  - `Jalur / Bidang Dinas`: `width: 21%; min-width: 170px; text-align: left;`
  - `Bulan Magang`: `width: 14%; min-width: 130px; text-align: left;`
  - `Status`: `width: 8%; min-width: 90px; text-align: center;`
  - `Aksi`: `width: 5%; min-width: 65px; text-align: center;`
- **Penyempurnaan Spasi Sel**:
  - Peserta: Avatar (38x38px) + gap `0.85rem` + Nama (700) + Institusi (`margin-top: 3px`) + NIM (`margin-top: 2px`).
  - Pembimbing: Nama (700) + Badge Mentee Aktif (`margin-top: 4px`).
  - Bidang: Nama bidang (600) + Badge Jalur Umum/Khusus (`margin-top: 4px`).
  - Status: Pill status terpusat simetris (`display: inline-flex; justify-content: center;`).
  - Aksi: Tombol ganti pembimbing terpusat rapi.

### D. Tabel 4: Belum Ditempatkan (`resources/views/admin/penempatan/index.blade.php`)
- **Distribusi Kolom (Total 100%)**:
  - `Peserta Magang`: `width: 30%; min-width: 220px; text-align: left;`
  - `Jurusan`: `width: 28%; min-width: 200px; text-align: left;`
  - `Bulan Magang`: `width: 18%; min-width: 140px; text-align: left;`
  - `Diterima Pada`: `width: 13%; min-width: 110px; text-align: center;`
  - `Aksi`: `width: 11%; min-width: 100px; text-align: center;`
- **Penyempurnaan Spasi Sel**:
  - Mengubah class tabel dari `.admin-table` lama menjadi standarisasi `.adm-table`.
  - Peserta cell: Avatar (36x36px) + gap `0.85rem` + Nama (700) + NIM (`margin-top: 3px`).
  - Jurusan cell: Jurusan (600) + Institusi (`margin-top: 3px`).
  - Periode cell: Bulan magang bold (700) + Durasi (`margin-top: 3px`).
  - Diterima Pada: Tanggal keputusan terpusat simetris.
  - Aksi: Tombol `[ + Tempatkan ]` terpusat rapi di kolom aksi.

### E. Tabel 5: Laporan Rekapitulasi Pendaftaran (`resources/views/admin/laporan/index.blade.php`)
- **Distribusi Kolom (Total 100%)**:
  - `No. Registrasi`: `width: 15%; min-width: 130px; text-align: left;`
  - `Nama & Institusi`: `width: 26%; min-width: 210px; text-align: left;`
  - `Jurusan`: `width: 23%; min-width: 190px; text-align: left;`
  - `Bulan Mulai`: `width: 15%; min-width: 130px; text-align: left;`
  - `Durasi`: `width: 10%; min-width: 85px; text-align: center;`
  - `Status`: `width: 11%; min-width: 105px; text-align: center;`
- **Penyempurnaan Spasi Sel**:
  - Nama & Institusi: Stack nama bold + institusi (`margin-top: 3px`).
  - Durasi: Nilai `X Bulan` terpusat langsung di bawah header `DURASI`.
  - Status: Dot badge dibungkus container flex `justify-content: center;` sehingga rapi terpusat di bawah header `STATUS`.

---

## 3. Rencana Berkas yang Disentuh

| Berkas | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/admin/pendaftaran/index.blade.php` | **Modify** | Menstandarisasi persentase lebar 6 kolom tabel registrasi, memperluas padding, dan memusatkan kolom Status & Aksi. |
| `resources/views/admin/kuota/index.blade.php` | **Modify** | Menyeimbangkan lebar 7 kolom tabel kuota per bidang dan memusatkan kolom metrik (Jenjang, Durasi, Kuota Terisi, Kapasitas, Aksi). |
| `resources/views/admin/penempatan/index.blade.php` | **Modify** | 1. Menstandarisasi persentase lebar 7 kolom tabel data penempatan (tab Ditempatkan) & memusatkan Status/Aksi.<br>2. Menstandarisasi persentase lebar 5 kolom tabel antrean belum ditempatkan & memusatkan Diterima Pada/Aksi. |
| `resources/views/admin/laporan/index.blade.php` | **Modify** | Menyelaraskan persentase 6 kolom tabel rekapitulasi laporan, serta memusatkan kolom Durasi dan Status. |
| `resources/css/components/tables.css` | **Modify** | Memastikan styling utilitas perataan tabel (`th.th-center`, `td.td-center`, padding lega `0.95rem 1.25rem`) terterapkan secara seragam. |

---

## 4. Langkah Implementasi (Checklist)

- [x] **Langkah 1**: Edit `resources/views/admin/pendaftaran/index.blade.php`:
  - Perbarui header dan sel data dengan persentase lebar proporsional (14%, 25%, 25%, 16%, 12%, 8%).
  - Rapikan spasi nama-badge dan posisikan status serta tombol Tinjau di tengah (*center-aligned*).
- [x] **Langkah 2**: Edit `resources/views/admin/kuota/index.blade.php`:
  - Perbarui header dan sel data dengan persentase lebar (20%, 34%, 10%, 10%, 11%, 9%, 6%).
  - Pusatkan kolom Jenjang, Durasi, Kuota Terisi, Kapasitas, dan Aksi.
- [x] **Langkah 3**: Edit `resources/views/admin/penempatan/index.blade.php`:
  - Perbarui tabel penempatan ditempatkan dengan persentase (4%, 25%, 23%, 21%, 14%, 8%, 5%) dan pusatkan status/aksi.
  - Perbarui tabel belum ditempatkan dengan persentase (30%, 28%, 18%, 13%, 11%) dan pusatkan Diterima Pada serta tombol Tempatkan.
- [x] **Langkah 4**: Edit `resources/views/admin/laporan/index.blade.php`:
  - Perbarui persentase kolom (15%, 26%, 23%, 15%, 10%, 11%) dan pusatkan kolom Durasi serta Status.
- [x] **Langkah 5**: Edit `resources/css/components/tables.css`:
  - Pastikan styling `th.th-center`, `td.td-center`, serta spasi vertikal sel tabel tertata harmonis.
- [x] **Langkah 6**: Kompilasi Asset:
  - Jalankan `npm run build` via Vite.
- [x] **Langkah 7**: Pengujian & Quality Gate (Zero-Regression):
  - Jalankan seluruh test suite PHPUnit:
    `vendor/bin/phpunit tests/Feature/AdminChildPagesVisualTest.php tests/Feature/AdminKuotaRevampTest.php tests/Feature/AdminDashboardEnhancementTest.php tests/Feature/AdminAdministrasiExportTest.php tests/Feature/AdminSuperAdminPortalTest.php`
  - Jalankan Laravel Pint: `vendor/bin/pint --test`.

---

## 5. Strategi Pengujian (Testing Strategy)

1. **Pengujian Tampilan (Visual Inspection)**:
   - Memastikan kelima tabel data mengisi kontainer secara penuh (100% width) tanpa kolom yang tertekan atau menyisakan celah kosong yang timpang.
   - Memastikan teks nama, institusi, jurusan, dan periode memiliki jarak vertikal yang nyaman dibaca (`margin-top: 3px`–`4px`).
   - Memastikan badge status, durasi, dan tombol aksi sejajar rapi secara simetris di tengah kolom masing-masing.
2. **Pengujian Fungsional & Regresi**:
   - Memastikan seluruh aksi (Tinjau Berkas, Ubah Kuota, Pindah Pembimbing, Tempatkan Peserta) tetap berfungsi normal dengan payload/data attribute yang utuh.
   - Memastikan seluruh automated test assertions lulus 100%.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Tabel Data Registrasi tampil penuh, terstruktur, rapi, dengan status & aksi terpusat simetris.
- [x] Tabel Kuota per Bidang memiliki distribusi kolom seimbang dan kolom metrik terpusat rapi.
- [x] Tabel Data Penempatan (Ditempatkan) tertata proporsional tanpa kolom yang berdesakan.
- [x] Tabel Belum Ditempatkan mengisi kontainer secara seimbang dan tombol Tempatkan terpusat rapi.
- [x] Tabel Laporan Rekapitulasi memiliki kolom Durasi dan Status yang terpusat dan mudah dibaca.
- [x] Seluruh baris teks dalam sel memiliki jarak napas (*space*) yang pas dan tidak menumpuk rapat.
- [x] `npm run build` sukses dan seluruh test suite PHPUnit (48+ tests) lulus 100% tanpa regresi.

