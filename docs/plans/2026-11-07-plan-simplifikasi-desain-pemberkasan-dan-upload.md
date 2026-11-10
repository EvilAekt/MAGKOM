# Rencana Kerja: Simplifikasi Desain Pemberkasan dan Perampingan Area Upload (Anti-Kepanjangan & Simpel)

**Tanggal**: 7 November 2026  
**Status**: Usulan / Menunggu Persetujuan Pengguna  
**Konteks**: Berdasarkan masukan pengguna terhadap tampilan pemberkasan pendaftaran magang di mana antarmuka saat ini dinilai "terlalu complicated" (banyak nesting, alert kontradiktif, dan tab terpisah) serta area upload dropzone yang "kek kepanjangan" (membentang horizontal 100% selebar kontainer sehingga menyisakan ruang kosong besar di tengah).

---

## 1. Analisis Masalah Saat Ini

### A. Area Upload "Kek Kepanjangan" (Stretched Dropzone)
- **Fakta Tampilan**: Elemen `.dropzone-compact-row` saat ini memiliki `width: 100%` di dalam kontainer yang lebarnya 900px – 1100px.
- **Dampak Visual**: Teks *"Tarik berkas ke sini atau Pilih File"* menempel di ujung kiri, dan label *"PDF, JPG, PNG Maks 2 MB"* menempel di ujung kanan. Di tengah-tengah terdapat garis putus-putus kosong yang sangat melar, kaku, dan tidak proporsional (seperti pita panjang kosong).

### B. Alur Kelompok "Terlalu Complicated" (Visual Clutter & Tab Nesting)
- **Alert Basi**: Masih ada alert biru lawas di paling atas yang menuliskan: *"Seluruh berkas ... diunggah secara kolektif (1 file gabungan resmi) yang memuat identitas seluruh anggota tim"*. Teks ini kontradiktif dengan alur baru di mana CV & Transkrip diunggah per anggota.
- **Nesting Berlebih**: Pemisahan dokumen menjadi dua judul section besar ("Berkas Bersama Tim" dan "Berkas Individu Anggota Tim"), lalu di dalam "Berkas Individu" ada tab switcher (`Ketua [Anda]` dan `Anggota 1`), lalu di dalam tab ada card lagi yang membungkus dropzone. Pengguna harus mengklik tab berulang kali hanya untuk memeriksa apakah berkas anggota sudah ada atau belum.

---

## 2. Solusi Desain Baru: Simpel, Rapi, & Proporsional

### A. Perampingan Area Upload (Tidak Melar / Anti-Kepanjangan)
Mengubah pola upload dari *full-width empty dashed box* menjadi **List Baris Dokumen yang Kompak dengan Tombol Upload Berukuran Proporsional**:
1. **Lebar Terkontrol (*Fixed/Constrained Width*)**:
   - Area aksi upload tidak membentang 100%, melainkan memiliki lebar terkontrol (`max-width: 240px–280px` atau berupa tombol aksi inline `[+ Pilih File]` berukuran ~130px).
2. **Layout Baris Dokumen Bersih**:
   - **Kiri**: Ikon tipe dokumen + Nama Dokumen + Badge (Wajib/Opsional) + Info format/maksimal ukuran (misal: "PDF, JPG, PNG • Maks 2 MB").
   - **Kanan**:
     - **Kondisi Belum Diunggah**: Tombol aksi upload yang ringkas dan tegas:
       - Tombol `[ + Pilih File ]` (dengan event drop/file picker).
       - Ketika berkas dipilih, muncul mini-konfirmasi: `[Nama File] [Batal] [Unggah]`.
     - **Kondisi Sudah Diunggah**: Status checklist hijau `✓ Terunggah` beserta detail file ringkas dan tombol `[Lihat]`, `[Unduh]`, `[Ganti]`.
   - **Hasil**: Tidak ada lagi garis putus-putus membentang kosong melompat dari ujung kiri ke kanan layar.

### B. Penyederhanaan Alur Kelompok (*Unified & Flat Member Cards*)
1. **Pembersihan Alert Basi**:
   - Hapus teks alert gabungan lama. Ganti dengan petunjuk singkat 1 baris yang informatif dan ramah:
     > *"ℹ️ **Pemberkasan Tim**: Unggah 2 berkas utama tim, serta berkas CV dan Transkrip Nilai untuk masing-masing anggota."*
2. **Pemberkasan Anggota Tanpa Tab Tersembunyi (*Direct Visibility*)**:
   - Alih-alih menyembunyikan berkas anggota di balik tab-tab klik yang rumit, kita tampilkan dalam **Dua Blok Ringkas**:
     1. **Blok 1: Berkas Tim (2 Berkas)**:
        - Surat Pengantar Resmi
        - Proposal Kegiatan Magang & Riset
     2. **Blok 2: Berkas Anggota Tim (Tersusun Rapi per Nama)**:
        - Dikelompokkan per personil dengan header ringkas yang memuat progress:
          - 👤 **Ketua Tim: [Nama Anda]** &bull; `[ 2/2 Berkas Terunggah ]`
            - Baris Dokumen: CV & Portofolio
            - Baris Dokumen: Transkrip Nilai Sementara
          - 👤 **Anggota 1: [Nama Anggota]** &bull; `[ 0/2 Berkas ]`
            - Baris Dokumen: CV & Portofolio
            - Baris Dokumen: Transkrip Nilai Sementara
   - **Keuntungan**:
     - Sangat simpel, bersih, dan transparan.
     - Pengguna dan ketua tim langsung melihat siapa saja yang berkasnya belum lengkap dalam satu pandangan mata tanpa perlu klik tab bolak-balik.

---

## 3. Rincian Perubahan File

| File | Komponen | Perubahan |
|---|---|---|
| `resources/css/layouts/dashboard-cockpit.css` | Styling CSS | - Mengunci lebar elemen upload (`max-width: 260px` atau inline action).<br>- Menata layout baris dokumen agar sejajar (flexbox nama di kiri, aksi di kanan).<br>- Menghapus visual melar pada `.dropzone-compact-row`.<br>- Styling card/header anggota tim yang minimalis dan elegan. |
| `resources/views/peserta/pendaftaran/partials/_doc_card_compact.blade.php` | Partial Item Dokumen | - Mengganti layout card membentang menjadi baris dokumen ringkas.<br>- Tombol `[+ Pilih File]` kompak dan responsif.<br>- Transisi state pemilihan berkas (`selected file bar`) yang pas dan tidak makan tempat. |
| `resources/views/peserta/pendaftaran/detail.blade.php` | Halaman Detail Pemberkasan | - Menghapus alert biru lawas yang misleading.<br>- Menyederhanakan hierarki judul dan grouping kelompok (tampilan langsung per personil yang ringkas, tanpa tab berbelit). |

---

## 4. Rencana Pengujian & Quality Gate

1. **Uji Fungsionalitas Unggah**:
   - Verifikasi unggah berkas tim (Surat Pengantar & Proposal).
   - Verifikasi unggah berkas ketua (CV & Transkrip).
   - Verifikasi unggah berkas anggota (CV & Transkrip).
   - Verifikasi ganti/unduh/lihat pratinjau berkas tetap berfungsi 100%.
2. **Uji Tampilan & Responsivitas**:
   - Pastikan area upload di layar desktop tidak lagi memanjang melar kosong di tengah.
   - Pastikan di layar tablet dan ponsel tampilan tetap rapi, adaptif, dan mudah ditekan.
3. **Automated Testing & Code Standards**:
   - `php artisan test --filter=PendaftaranPesertaTest`
   - `./vendor/bin/pint --test`
   - `npm run build`

