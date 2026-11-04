# Plan: Penyelarasan Presisi Tabel Laporan Sesuai Tata Letak Asli (Merata & Lurus Sempurna)

**Tanggal**: 04 November 2026  
**Status**: Selesai Terverifikasi  
**Referensi**: Permintaan User & Tangkapan Layar `media_1793804032654_f88536d0.png` (Hasil Saat Ini) vs `media_1793804162653_eab39373.png` (Tampilan Asli yang Diinginkan).  
**Aturan Proyek**: `AGENTS.md` (Larangan CSS di Blade, pertahankan UI/UX baseline, perubahan terfokus).

---

## 1. Analisis Masalah & Kebutuhan Pengguna

Berdasarkan keluhan dan dua tangkapan layar yang diunggah pengguna:
1. **Area Tengah Terlihat Kosong & Aneh (*Awkward Blank Space in Center*)**:
   - Pada tangkapan layar pertama (hasil perubahan terakhir), kolom 1 (*No. Registrasi*) dan kolom 2 (*Calon Peserta*) menempel ke kiri, sementara kolom 3 hingga 6 terdorong ke kanan.
   - Hal ini disebabkan oleh pembatasan lebar piksel kaku (`width: 150px`, `width: 220px`, dll.) pada monitor layar lebar, sehingga tabel meregang dan menyisakan celah kosong besar di tengah.
2. **Kebutuhan Mengembalikan Tata Letak Asli (*Original Layout Preservation*)**:
   - Pengguna menegaskan: *"jujur aku pengen tata letaknya tu tetap seperti asli sblm di ubah cuma di rapiin di lurusin aja kan gambar ss an kedua ini sebenernya dah bagus"*.
   - Pada tangkapan layar kedua (tampilan asli):
     - Kolom terdistribusi secara seimbang dan proporsional membentang merata dari ujung kiri ke ujung kanan kartu (*balanced horizontal spread*).
     - Kolom nama peserta menggunakan format teks bertingkat (*Nama* tebal di atas, *Asal Institusi* di bawahnya), **tanpa** avatar inisial kotak/lingkaran yang mempersempit teks.
3. **Akar Masalah Ketidaksejajaran pada Tampilan Asli (*Header vs Data Misalignment*)**:
   - Alasan mengapa tampilan asli sebelumnya terasa kurang rapi:
     - Header kolom (`<th>`) memiliki teks rata tengah (*centered*) akibat gaya bawaan, sementara isi data di bawahnya (`<td>`) memiliki teks rata kiri (*left-aligned*).
     - Akibatnya: Judul `NO. REGISTRASI`, `NAMA & INSTITUSI`, `JURUSAN PILIHAN`, `BULAN MULAI`, `DURASI`, dan `STATUS` melayang di tengah-tengah kolom, tidak lurus vertikal dengan data di bawahnya.
4. **Solusi yang Diinginkan Pengguna**:
   - Pertahankan struktur kolom dan penyebaran merata seperti tampilan asli (tangkapan layar kedua).
   - Buat judul kolom di atas (*atasannya*) dan isi data di bawahnya (*bawahannya*) **100% lurus sejajar pada garis vertikal yang sama** dari atas sampai bawah.

---

## 2. Tujuan Solusi

1. **Mengembalikan Distribusi Lebar Kolom Merata & Proporsional**:
   - Menghapus lebar piksel kaku (`width: 150px`, `width: 220px`, dsb.) pada elemen `<th>`.
   - Menggunakan distribusi persentase proporsional yang membentang penuh 100% lebar kontainer tanpa ada ruang kosong aneh di tengah:
     - **No. Registrasi**: ~16%
     - **Nama & Institusi**: ~26% (teks nama tebal + institusi bertingkat tanpa avatar)
     - **Jurusan Pilihan**: ~22%
     - **Bulan Mulai**: ~16%
     - **Durasi**: ~10%
     - **Status**: ~10%
2. **Kesejajaran Vertikal Lurus Sempurna (*Straight Vertical Datum Line*)**:
   - Menyamakan `text-align: left !important;` serta padding horizontal identik (`padding: 0.875rem 1rem !important;`) pada seluruh header `<th>` dan seluruh sel `<td>`.
   - Huruf pertama judul kolom (misal: 'N' pada `NO. REGISTRASI`) akan persis berada pada koordinat horizontal yang sama dengan karakter pertama data di bawahnya (misal: '#' pada `#REG-00003`).
3. **Pembersihan Kolom Calon Peserta**:
   - Menghapus avatar inisial (`.cv-avatar-sm`) pada tabel laporan dan kembali ke struktur teks bertingkat yang bersih dan lapang seperti tangkapan layar 2.
4. **Penyelarasan Konsisten untuk Seluruh Tab Laporan**:
   - Terapkan prinsip yang sama (distribusi merata + pelurusan lurus vertikal) pada Tab 2 (Penempatan), Tab 3 (Kuota & Slot), dan Tab 4 (Rekap Nilai).

---

## 3. Evaluasi Pendekatan Alternatif

| Parameter | Opsi A: Distribusi Proporsional 100% + Pelurusan Rata Kiri Sempurna (Rekomendasi) | Opsi B: Tetap Menggunakan Lebar Piksel Kaku (`px`) |
| :--- | :--- | :--- |
| **Kesesuaian dengan Keinginan Pengguna** | **Sangat Sesuai**: Menghilangkan ruang kosong di tengah, kembali ke layout asli tangkapan layar 2, dan lurus sejajar. | **Tidak Sesuai**: Tetap menyisakan ruang kosong besar di layar monitor desktop lebar. |
| **Kesejajaran Header vs Data** | **100% Presisi Lurus**: Header `<th>` dan sel `<td>` berbagi garis awal yang persis sama. | Header dan sel berisiko tidak sejajar jika kolom meregang tak beraturan. |
| **Kerapian Tipografi** | Bersih, elegan, dan proporsional untuk semua resolusi layar. | Kaku dan tampak canggung pada layar lebar. |

**Keputusan**: Memilih **Opsi A**.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/css/components/tables.css` | Modify | Menyesuaikan aturan `.adm-table` agar menggunakan `table-layout: auto` dengan padding horizontal seragam antara `th` dan `td`, serta memastikan gaya rata kiri vertikal lurus sempurna. |
| `resources/views/admin/laporan/index.blade.php` | Modify | Menghapus pembatasan lebar piksel kaku, menghapus avatar inisial pada tabel laporan (kembali ke layout asli), dan mengunci pelurusan horizontal judul vs data pada Tab 1, 2, 3, dan 4. |

---

## 5. Rincian Teknis Implementasi

### 5.1 Tab 1: Rekapitulasi Pendaftaran
- **Struktur Kolom**:
  - `No. Registrasi` (`width: 16%; text-align: left;`): Badge registrasi monospace + tanggal pengajuan di bawahnya.
  - `Nama & Institusi` (`width: 26%; text-align: left;`): Nama peserta (tebal `#0F172A`) di baris atas, Asal Institusi (`#64748B`) di baris bawah. Tanpa avatar inisial.
  - `Jurusan Pilihan` (`width: 22%; text-align: left;`): Nama jurusan magang.
  - `Bulan Mulai` (`width: 16%; text-align: left;`): Bulan dan tahun mulai magang.
  - `Durasi` (`width: 10%; text-align: left;`): Durasi magang (misal: "3 Bulan").
  - `Status` (`width: 10%; text-align: left;`): Badge status berlatar lembut dan bertitik semantik, sejajar lurus dengan judul kolom `STATUS`.

### 5.2 Tab 2: Data Penempatan
- Mengembalikan proporsi merata tanpa avatar:
  - `Peserta Magang` (`width: 26%; text-align: left;`): Nama peserta + institusi bertingkat.
  - `Pembimbing Lapangan` (`width: 22%; text-align: left;`): Nama pembimbing + bidang dinas.
  - `Jalur / Sumber` (`width: 18%; text-align: left;`): Badge jalur + sumber.
  - `Rentang Waktu Magang` (`width: 20%; text-align: left;`): Tanggal mulai s/d selesai.
  - `Status` (`width: 14%; text-align: left;`): Badge status penempatan rata kiri sejajar.

### 5.3 Tab 3 & Tab 4: Kuota & Rekap Nilai
- Penyelarasan header dan data sel pada tabel realisasi kuota umum, slot khusus kemitraan, dan rekap penilaian agar judul kolom dan isinya lurus tegak lurus sempurna.

---

## 6. Langkah Implementasi Bertahap

### Fase 1: Penyesuaian CSS di `resources/css/components/tables.css`
- [x] Pastikan `.adm-table` menggunakan tata letak proporsional alami tanpa pemaksaan layout yang memicu celah kosong.
- [x] Pastikan padding horizontal antara `.adm-table th` dan `.adm-table td` bernilai identik (`padding: 0.875rem 1.25rem !important;`).
- [x] Pastikan alignment default pada seluruh `th` dan `td` adalah `text-align: left !important;`.

### Fase 2: Penataan Ulang Blade di `resources/views/admin/laporan/index.blade.php`
- [x] Pada Tab 1 (Pendaftaran):
  - Hapus pembatasan piksel kaku (`150px`, `240px`, dll.) dan gunakan persentase seimbang.
  - Hapus avatar inisial (`.cv-avatar-sm`) sehingga nama dan institusi kembali ke format aslinya yang bersih.
  - Pastikan seluruh judul kolom `<th>` dan sel data `<td>` rata kiri lurus sejajar sempurna.
- [x] Pada Tab 2 (Penempatan):
  - Hapus avatar inisial dan terapkan pelurusan rata kiri proporsional merata.
- [x] Pada Tab 3 (Kuota) dan Tab 4 (Nilai):
  - Selaraskan judul kolom dengan sel data di bawahnya.

### Fase 3: Kompilasi & Verifikasi Kualitas
- [x] Jalankan `npm run build` untuk mengompilasi CSS.
- [x] Jalankan pengujian otomatis:
  ```bash
  php artisan test tests/Feature/AdminLaporanDanArsipTest.php tests/Feature/AdminEksporLaporanPendaftaranPopoverTest.php tests/Feature/AdminTablePaginationStandardTest.php
  ```
- [x] Jalankan linter Pint: `./vendor/bin/pint --test`.

---

## 7. Kriteria Selesai (Definition of Done)

- [x] Tidak ada lagi ruang kosong aneh di tengah tabel; kolom membentang merata dan proporsional seperti tampilan asli tangkapan layar 2.
- [x] Seluruh judul kolom (`<th>`) dan isi data sel (`<td>`) di bawahnya 100% lurus sejajar pada garis vertikal yang sama di setiap kolom.
- [x] Kolom nama peserta kembali bersih tanpa avatar inisial yang mempersempit teks institusi.
- [x] Mode terang dan mode gelap tampil kontras dan rapi.
- [x] `npm run build` berhasil tanpa error.
- [x] Seluruh automated test lolos hijau tanpa regresi.

