# Plan: Penyelarasan Tata Letak & Kolom Tabel Laporan Admin (Presisi, Sejajar, & Bersih)

**Tanggal**: 04 November 2026  
**Status**: Menunggu Konfirmasi Pengguna  
**Referensi**: Permintaan User & Tangkapan Layar `media_1793776852241_0f875c98.png`, Aturan `AGENTS.md`.

---

## 1. Analisis Masalah & Akar Masalah (Root Cause)

Berdasarkan tangkapan layar `media_1793776852241_0f875c98.png` yang diunggah pengguna:
1. **Ketidaksejajaran Vertikal Antara Header dan Isi Sel (*Alignment Mismatch*)**:
   - Seluruh header kolom `<th>` (`NO. REGISTRASI`, `NAMA & INSTITUSI`, `JURUSAN PILIHAN`, `BULAN MULAI`, `DURASI`, `STATUS`) me-render posisi teks di tengah (*centered*) akibat default CSS browser/Gentelella pada elemen `th`.
   - Sebaliknya, seluruh sel data `<td>` di bawahnya me-render teks rata kiri (*left-aligned*).
   - Akibatnya, judul kolom berada di tengah-tengah kolom sementara isi datanya menempel di sisi kiri, menyebabkan tampilan tampak meleset, tidak sejajar, dan berantakan.
2. **Ketiadaan Pengaturan Lebar Kolom Proporsional (*Unconstrained Column Widths*)**:
   - Kolom-kolom tabel tidak memiliki spesifikasi lebar pasti (`table-layout: auto`), sehingga browser menghitung lebar kolom secara acak berdasarkan panjang teks. Hal ini menimbulkan spasi kosong raksasa antara kolom *Nama & Institusi* dan *Jurusan Pilihan*, serta mendorong kolom *Durasi* dan *Status* ke sudut kanan yang terputus.
3. **Ketiadaan Meta Bar Header Tabel**:
   - Tabel langsung menempel di garis tepi atas kontainer tanpa baris info metadata tabel (`.adm-table-meta-bar`) seperti yang dimiliki halaman *Pendaftaran* dan *Penempatan*.
4. **Indikator Status Tidak Semantik**:
   - Status masih menggunakan badge abu-abu polos tanpa titik warna semantik (*status dot* hijau untuk Diterima, merah untuk Dibatalkan/Ditolak, kuning untuk Diajukan).
5. **Ketiadaan Inisial Avatar Peserta**:
   - Kolom nama peserta tidak memiliki avatar/ikon inisial seperti standar halaman admin lainnya.

---

## 2. Tujuan Solusi

1. **Penyelarasan Presisi Header vs Data Seluruh Kolom**:
   - Mengunci `text-align: left !important;` pada kolom teks (No. Registrasi, Nama & Institusi, Jurusan Pilihan, Bulan Mulai) baik di header `<th>` maupun sel `<td>`.
   - Mengunci `text-align: center !important;` pada kolom numerik/status terpusat (Durasi, Status).
2. **Standardisasi Lebar Kolom Proporsional**:
   - Menetapkan lebar kolom yang terukur dan seimbang:
     - `No. Registrasi`: `width: 150px; text-align: left;`
     - `Nama & Institusi`: `min-width: 240px; text-align: left;` (fluid)
     - `Jurusan Pilihan`: `width: 220px; text-align: left;`
     - `Bulan Mulai`: `width: 160px; text-align: left;`
     - `Durasi`: `width: 110px; text-align: center;`
     - `Status`: `width: 140px; text-align: center;`
3. **Penyempurnaan Visual & Estetika Rekapitulasi**:
   - Menambahkan `.adm-table-meta-bar` di bagian atas tabel (`Menampilkan 1–X dari Y data rekapitulasi`).
   - Menyematkan avatar inisial (`.cv-avatar-sm`) pada nama peserta.
   - Menggunakan komponen status resmi `.cv-status-dot-badge` berlatar lembut dan bertitik warna semantik (*emerald* untuk Diterima, *rose* untuk Dibatalkan/Ditolak, *amber* untuk Diajukan/Perlu Perbaikan).
4. **Sentuhan Global di `tables.css`**:
   - Mendefinisikan class pondasi `.adm-table` secara global dengan `table-layout: fixed;` atau proporsi terukur agar perbaikan ini juga menguntungkan seluruh tabel lain tanpa perlu styling berulang.

---

## 3. Evaluasi Alternatif Arsitektur

| Parameter | Opsi A: CSS Terpusat `.adm-table` + Restrukturisasi Presisi Kolom Blade (Rekomendasi) | Opsi B: Inline CSS Quick Fix di Blade |
| :--- | :--- | :--- |
| **Kesejajaran** | 100% sejajar presisi dan terstandarisasi untuk semua breakpoint. | Rawan patah saat konten bertambah panjang. |
| **Kepatuhan AGENTS.md** | Sangat Patuh — Aturan styling masuk ke `resources/css/components/tables.css`. | Melanggar aturan pemisahan CSS dan Blade. |
| **Dampak Masa Depan** | Reusable untuk semua tabel rekapitulasi data. | Menumpuk utang teknis (*technical debt*). |

**Keputusan**: Memilih **Opsi A**.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/components/tables.css` | Modify | Menambahkan styling resmi `.adm-table`, `.adm-table th`, `.adm-table td`, `.adm-table-meta-bar`, serta modifier alignment `.th-center`, `.td-center`. |
| `resources/views/admin/laporan/index.blade.php` | Modify | Menambahkan `.adm-table-meta-bar`, menyelaraskan lebar dan alignment kolom tabel di Tab 1 (serta Tab 2, 3, dan 4), menambahkan avatar inisial peserta, dan menggunakan `.cv-status-dot-badge`. |

---

## 5. Langkah Implementasi Bertahap

### Fase 1: Standarisasi CSS `.adm-table` di `resources/css/components/tables.css`
- [ ] Tambahkan aturan tata letak presisi `.adm-table`:
  ```css
  .adm-table {
      width: 100%;
      border-collapse: collapse;
      text-align: left;
      font-size: 0.875rem;
  }
  .adm-table th {
      background: #F8FAFC !important;
      padding: 0.875rem 1.25rem !important;
      font-size: 0.75rem !important;
      font-weight: 700 !important;
      text-transform: uppercase !important;
      letter-spacing: 0.05em !important;
      color: #64748B !important;
      border-bottom: 1.5px solid #CBD5E1 !important;
      white-space: nowrap !important;
      text-align: left !important;
      vertical-align: middle !important;
  }
  .adm-table th.th-center, .adm-table td.td-center {
      text-align: center !important;
  }
  .adm-table td {
      padding: 0.95rem 1.25rem !important;
      border-bottom: 1px solid #E2E8F0 !important;
      vertical-align: middle !important;
      color: #1E293B !important;
      background: #FFFFFF !important;
      text-align: left !important;
  }
  ```
- [ ] Tambahkan styling `.adm-table-meta-bar` dan dark mode parity.

### Fase 2: Penataan Ulang Kolom & Kesejajaran di `resources/views/admin/laporan/index.blade.php`
- [ ] Pada Tab 1 (Rekapitulasi Pendaftaran):
  - Tambahkan `.adm-table-meta-bar` di dalam `.adm-table-container` sebelum tabel.
  - Terapkan lebar dan alignment eksplisit pada `<th>`:
    - `No. Registrasi` (150px, rata kiri)
    - `Nama & Institusi` (rata kiri, dengan avatar inisial dan institusi terstruktur)
    - `Jurusan Pilihan` (220px, rata kiri)
    - `Bulan Mulai` (160px, rata kiri)
    - `Durasi` (110px, rata tengah)
    - `Status` (140px, rata tengah)
  - Terapkan kelas `td-center` pada sel Durasi dan Status.
  - Ganti badge status dengan `.cv-status-dot-badge` (dengan warna semantik `success`, `danger`, `warning`, dsb.).
- [ ] Selaraskan hal serupa pada tabel di Tab 2 (Penempatan) dan Tab 4 (Nilai).

### Fase 3: Kompilasi Aset & Verifikasi Kualitas
- [ ] Kompilasi aset Vite: `npm run build`.
- [ ] Jalankan feature tests terkait:
  - `php artisan test tests/Feature/AdminLaporanDanArsipTest.php tests/Feature/AdminEksporLaporanPendaftaranPopoverTest.php tests/Feature/AdminTablePaginationStandardTest.php`
- [ ] Jalankan pengecekan linter Pint: `./vendor/bin/pint --test`.

---

## 6. Kriteria Selesai (Definition of Done)
- [ ] Semua judul kolom (header) dan sel data di tabel Laporan sejajar sempurna secara vertikal (*aligned*).
- [ ] Jarak antarkolom proporsional, rapi, dan tidak ada ruang kosong janggal.
- [ ] Badge status menggunakan titik warna semantik (*emerald/rose/amber*).
- [ ] `npm run build` sukses.
- [ ] Seluruh automated test lolos hijau tanpa regresi.

