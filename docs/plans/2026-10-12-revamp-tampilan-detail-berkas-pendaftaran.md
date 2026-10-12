# Rencana Revamp Tampilan Detail & Berkas Pendaftaran Magang Sesuai Tema Utama MagKom

## 1. Tujuan
Merevamp antarmuka (UI/UX) halaman **Detail & Berkas Pendaftaran** (`resources/views/peserta/pendaftaran/detail.blade.php`) agar selaras dengan **Tema Utama MagKom** yang modern, bersih, profesional, dan ramah pengguna (senada dengan Hero Banner Pendaftaran, Halaman Profil Peserta, dan Classroom Tugas).

---

## 2. Asumsi & Analisis Kondisi Saat Ini

### Kondisi Halaman Saat Ini:
1. **Header & Info Registrasi**: Saat ini hanya berupa kartu putih datar dengan breadcrumb sederhana dan informasi teks biasa. Belum memiliki identitas visual khas MagKom.
2. **Timeline Tracker**: Menggunakan garis lurus abu-abu kaku dengan angka 1, 2, 3 tanpa sentuhan estetika modern.
3. **Kartu Dokumen Persyaratan**: Tampilan list dokumen menggunakan border tipis (`var(--neutral-400)`) dengan tata letak tombol aksi yang padat dan belum memiliki hirarki visual yang tegas antara berkas Wajib dan Opsional.
4. **Area Dropzone**: Tampilan seret-lepas berkas belum serasi dengan gaya modern MagKom (`#F0F7FF`, dashed `#93C5FD`, sudut rounded 12px).
5. **Modal Pratinjau & Pembatalan**: Sudah memiliki fungsionalitas pratinjau in-app dan pembatalan, namun styling modal masih menggunakan card dasar tanpa aksen tema MagKom.

### Asumsi Teknis:
- Seluruh logika alur bisnis (BR-03 aturan pembatalan, BR-05 aturan unggah berkas, AC-03 pengajuan pendaftaran, signed preview/download URL, in-app preview iframe/image) **tetap dipertahankan 100% tanpa mengubah alur backend controller**.
- Perubahan difokuskan pada CSS, struktur HTML Blade, komponen tata letak visual, ikon, serta micro-interactions responsif di `resources/views/peserta/pendaftaran/detail.blade.php`.
- Menggunakan design tokens MagKom resmi dari `layouts/peserta.blade.php` (`--blue-600`, `--blue-700`, `--neutral-800`, dll.).

---

## 3. Evaluasi Opsi Pendekatan Desain UI

| Kriteria | Opsi A: Sentuhan Patching CSS Minor | Opsi B (Direkomendasikan): Revamp Komprehensif Berbasis Tema MagKom (Hero + Stepper Card + Modular Doc List) |
| :--- | :--- | :--- |
| **Konsep** | Hanya mengubah beberapa warna border dan font pada elemen yang ada tanpa merombak layout header. | Mengadopsi Hero Banner bergradien MagKom, status badge modern, stepper proses yang interaktif, kartu dokumen modular dengan hirarki jelas, serta bar kelengkapan berkas yang dinamis. |
| **Kelebihan** | Perubahan sedikit baris kode. | **Tampilan konsisten 100% dengan halaman utama MagKom**, jauh lebih berkelas (*wow effect*), meningkatkan kejelasan navigasi peserta magang. |
| **Kekurangan** | Halaman tetap terasa kuno dan tidak mencerminkan tema MagKom secara utuh. | Memerlukan penyusunan ulang struktur Blade template pada bagian presentasional. |
| **Pengalaman Pengguna (UX)** | Standar / biasa saja. | **Sangat modern, intuitif, ramah mobile, dan profesional.** |

> **Rekomendasi**: **Opsi B** untuk memberikan pengalaman visual tingkat tinggi yang terpadu di seluruh portal peserta.

---

## 4. Rincian Desain & Komponen Baru

### A. Hero Banner MagKom "Detail Pendaftaran"
- **Background**: Gradien khas MagKom (`linear-gradient(95deg, rgba(6, 65, 204, 0.95), rgba(7, 81, 255, 0.88))`) dengan watermark samar `MAGKOM` di sudut kanan.
- **Top Row**: Tombol navigasi kembali yang rapi: `← Kembali ke Riwayat Pendaftaran` dengan pill glassmorphism.
- **Badge ID & Status**: 
  - Tag pill ID Registrasi: `#REG-0000X`.
  - Status Lencana Registrasi (misal: *Menunggu Verifikasi Admin*, *Berkas Belum Lengkap*, *Diterima Magang*) dengan warna semantik yang kontras dan teks putih/terbaca jelas.
- **Metadata Badges**: Periode Mulai, Jurusan Kuota, Durasi Bulan Magang, dan Waktu Pengajuan.
- **Tombol Batalkan Pengajuan**: Diposisikan di header secara elegan dengan tombol secondary soft-red outline untuk pengajuan yang masih dapat dibatalkan.

### B. Modern Stepper Tracker (Alur Pendaftaran)
- Desain kartu stepper terpisah di bawah hero:
  - **Tahap 1**: *Kelengkapan Berkas Persyaratan* (Aktif/Selesai)
  - **Tahap 2**: *Verifikasi Tim Admin & Wawancara* (Menunggu/Sedang Berlangsung)
  - **Tahap 3**: *Hasil Keputusan Akhir* (Diterima / Perlu Perbaikan / Ditolak)
- Indikator visual berupa lingkaran ikon SVG halus dengan shadow lembut, garis penghubung yang aktif sesuai progres, serta catatan waktu status.

### C. Bar Meter Kelengkapan Berkas (Progress Tracker)
- Banner informatif kelengkapan berkas:
  - Lingkaran persentase atau bar kemajuan dinamis (`X dari Y Berkas Wajib Terunggah`).
  - Lencana status: `Siap Diajukan` (hijau) jika semua berkas wajib telah lengkap, atau `Belum Lengkap` (amber) jika masih ada yang kurang.

### D. Kartu Dokumen Persyaratan (Enhanced Doc Cards)
- Setiap item dokumen dibungkus dalam kartu modern (`border-radius: 12px`, hover lift effect):
  - **Header Dokumen**: Ikon berkas, Nama Jenis Dokumen, Badge status `Wajib` (merah muda lembut) atau `Opsional` (biru abu-abu lembut), serta deskripsi ringkas dokumen.
  - **Status Berkas**: Indikator apakah sudah diunggah atau belum.
  - **Tampilan Berkas Terunggah (Uploaded Pill)**:
    - Ikon tipe berkas (PDF merah / Gambar biru).
    - Nama asli berkas (bold, rapi), ukuran berkas, dan tanggal pengunggahan.
    - Grup aksi: Tombol `👁 Lihat Berkas` (primary/blue), `⬇ Unduh` (secondary), dan `🔄 Ganti Berkas`.
  - **Dropzone Area**:
    - Border dashed lembut (`#93C5FD`), latar biru muda (`#F0F7FF`).
    - Ikon cloud-upload modern dengan animasi hover halus.
    - Input drag & drop responsif.
    - Bar preview file terpilih dengan tombol konfirmasi `Unggah Berkas Sekarang`.

### E. Panel Aksi Finalisasi Pendaftaran (Sticky / Standout Card)
- Di bagian bawah daftar dokumen:
  - Ringkasan instruksi pengajuan.
  - Tombol utama `Ajukan Pendaftaran Sekarang` dengan aksen MagKom Blue, ikon pesawat kertas / kirim, disabled otomatis jika berkas wajib belum lengkap, dengan tooltip penjelasan.

### F. Penyelarasan Modal
- **Modal Pratinjau Dokumen**: Header modal dengan background putih/netral bersih, indikator loading spinner modern, dan iframe/gambar yang nyaman dilihat pada desktop maupun tablet/ponsel.
- **Modal Pembatalan**: Alert amber yang sopan, penjelasan konsekuensi pembatalan yang jelas, dan tombol konfirmasi yang terlindungi dari klik tidak sengaja.

---

## 5. Rencana Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/pendaftaran/detail.blade.php` | Ubah | Revamp stylesheet in-page dan struktur markup HTML Blade: implementasi Hero Banner MagKom, Modern Stepper, Enhanced Doc Cards, Progress Meter, dan Modal polish. |
| `tests/Feature/PesertaPendaftaranTest.php` / Feature Tests Terkait | Uji | Memverifikasi seluruh skenario pengunggahan berkas, pratinjau dokumen, pengajuan pendaftaran, dan pembatalan tetap lulus 100%. |
| `docs/plans/2026-10-12-revamp-tampilan-detail-berkas-pendaftaran.md` | Buat | Dokumen rencana implementasi master. |

---

## 6. Langkah Implementasi Terurut

- [x] **Langkah 1**: Buat dan selaraskan token CSS scoped di `@push('styles')` pada `resources/views/peserta/pendaftaran/detail.blade.php` (Hero, Stepper, Modern Cards, Dropzone, Progress Bar, Action Footer).
- [x] **Langkah 2**: Perbarui struktur **Header Banner**: Terapkan Hero Banner MagKom dengan breadcrumb, badge status registrasi, metadata pills, dan tombol pembatalan.
- [x] **Langkah 3**: Perbarui tampilan **Status Stepper Tracker**: Terapkan card stepper visual dengan badge dan ikon status real-time.
- [x] **Langkah 4**: Perbarui widget **Progress Bar Kelengkapan Berkas**: Terapkan progress bar yang terintegrasi rapi dengan indikator counter berkas wajib.
- [x] **Langkah 5**: Perbarui komponen **Daftar Dokumen Persyaratan**: Rancang ulang kartu dokumen (`.doc-card`), kartu berkas terunggah (`.uploaded-box`), dan area drag-and-drop (`.dropzone-box`) agar simetris, modern, dan konsisten dengan tema utama MagKom.
- [x] **Langkah 6**: Perbarui kartu **Aksi Finalisasi & Catatan Verifikator**: Terapkan visual alert revisi admin yang tegas serta tombol kirim pendaftaran yang jelas.
- [x] **Langkah 7**: Polish **Modal Pratinjau & Modal Konfirmasi Pembatalan**: Sesuaikan tipografi, radius sudut, dan estetika tombol modal.
- [x] **Langkah 8**: Jalankan automated test suite `PesertaPendaftaranTest` atau `PendaftaranTest` untuk memastikan seluruh alur dan interaksi backend tetap berjalan sempurna tanpa regresi.

---

## 7. Strategi Testing
1. **Automated Feature Tests**:
   - Jalankan `php artisan test --filter=Pendaftaran` untuk memastikan route, form submission, upload berkas, dan pembatalan berjalan normal.
2. **Manual Visual QA**:
   - Buka `http://localhost:8000/peserta/pendaftaran/{id}` di browser.
   - Periksa keindahan Hero Banner pada berbagai status pendaftaran (`berkas_belum_lengkap`, `diajukan`, `perlu_perbaikan`, `diterima`, `ditolak`, `waiting_list`).
   - Coba unggah berkas baru melalui dropzone, verifikasi preview file pill dan tombol aksi.
   - Buka modal pratinjau dokumen (PDF dan Gambar), pastikan modal tampil rapi dan responsif.
   - Periksa tampilan mobile responsive (lebar layar < 768px).

---

## 8. Risiko & Mitigasi
- **Risiko**: ID elemen JavaScript terganggu sehingga fungsionalitas drag & drop atau modal tidak berfungsi.
  - **Mitigasi**: Pertahankan seluruh ID HTML (`#card_doc_{id}`, `#file_input_{id}`, `#dropzone_box_{id}`, `#selected_bar_{id}`, `#cancelModal`, `#docPreviewModal`, fungsi JS `openDocPreview`, `toggleDropzone`, dll.) secara utuh tanpa ada yang diubah namanya.

---

## 9. Kriteria Selesai (Definition of Done)
- [x] Halaman `Detail & Berkas Pendaftaran` memiliki Hero Banner khas MagKom dengan tipografi dan warna yang serasi.
- [x] Stepper alur pendaftaran tampil modern dan informatif.
- [x] Kartu dokumen persyaratan memiliki hierarki visual yang jelas, rapi, dan simetris.
- [x] Dropzone unggah berkas memiliki visual yang bersih dan interaktif.
- [x] Fitur pratinjau dokumen dan aksi pembatalan tetap berjalan lancar 100%.
- [x] Seluruh automated feature tests lulus tanpa ada error atau regresi.
