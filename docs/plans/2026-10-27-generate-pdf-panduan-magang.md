# Rencana Kerja: Implementasi Generator PDF Buku Panduan Resmi Magang MAGKOM

**Tanggal**: 27 Oktober 2026  
**Status**: Selesai Diimplementasikan & Terverifikasi (Production Ready)  
**Dokumen**: `docs/plans/2026-10-27-generate-pdf-panduan-magang.md`  
**Area Terkait**: Fitur Unduh Panduan PDF (`resources/views/public/landing.blade.php`, `routes/web.php`, `app/Http/Controllers/Public/PanduanPdfController.php`, `resources/views/pdf/buku-panduan-magang.blade.php`)

---

## 1. Analisis Masalah & Kebutuhan Pengguna
1. **Masalah Saat Ini**:
   - Tombol **"Unduh Panduan PDF"** pada landing page (`.btn-tahapan-download` di section `#tahapan`) saat ini masih mengarah ke rute web `route('panduan-alur')` (`/panduan-alur`).
   - Akibatnya, saat pengunjung mengklik tombol tersebut, mereka tidak menerima file PDF melainkan dialihkan (*redirect*) ke tampilan web alur pendaftaran lama.
2. **Kebutuhan Pengguna**:
   - Mengubah tombol agar mengunduh (*download*) / membuka langsung dokumen PDF resmi berformat rapi dan profesional.
   - Menjelaskan dan merancang **materi/isi yang akan dimasukkan ke dalam dokumen PDF Panduan Resmi**.

---

## 2. Rincian Materi yang Dimasukkan ke Dokumen PDF Panduan

Dokumen PDF dirancang berformat **Buku Panduan & Petunjuk Teknis Resmi (Juknis)** Dinas Komunikasi dan Informatika Kabupaten Sleman ukuran A4 dengan struktur:

```
[KOP RESMI PEMKAB SLEMAN & DISKOMINFO]
PANDUAN PENDAFTARAN & TAHAPAN PELAKSANAAN MAGANG / PRAKTEK KERJA LAPANGAN (PKL)
SISTEM INFORMASI MAGKOM KABUPATEN SLEMAN
```

### Rincian Konten Tiap Bagian:
1. **Identitas & Kop Surat Resmi**:
   - Logo resmi Kabupaten Sleman.
   - Kop dinas lengkap: Dinas Komunikasi dan Informatika Kabupaten Sleman, alamat Jl. Parasamya No. 1 Beran, Tridadi, Sleman, kontak email dan portal resmi.
2. **Bab I: Informasi Umum & Jalur Pendaftaran**:
   - **Tujuan Program**: Wadah peningkatan kompetensi digital dan pengalaman kerja riil bagi peserta didik.
   - **Sasaran Peserta**:
     - Siswa SMK (Prakerin/PKL 3 bulan s/d 6 bulan).
     - Mahasiswa D3/D4/S1 (Magang Riset/Industri/Studi Independen).
   - **Jalur Pendaftaran**:
     - **Mandiri (1 Orang)**: Pendaftaran perseorangan.
     - **Kelompok (2–4 Orang)**: Pendaftaran tim/rombongan yang **cukup diwakili oleh 1 akun Ketua**. Rekan tim tidak perlu membuat akun terpisah; berkas tim diunggah bersamaan pada formulir pendaftaran.
3. **Bab II: Persyaratan Berkas Administrasi**:
   - **Tabel Persyaratan Berkas per Jenjang**:
     - *Jenjang SMK*: Surat Pengantar Resmi Sekolah (bertanda tangan Kepala Sekolah & stempel basah/digital), CV/Biodata Siswa, Salinan Rapor semester terakhir (PDF maks. 2MB).
     - *Jenjang Mahasiswa*: Surat Pengantar Resmi Fakultas/Jurusan, CV lengkap memuat portofolio/keahlian, Transkrip Nilai Kumulatif resmi (PDF maks. 2MB).
4. **Bab III: Tahapan Alur Pendaftaran (SOP 4 Langkah)**:
   - **Langkah 1: Registrasi Akun & Pengecekan Kuota**:
     Pengecekan sisa kuota jurusan per periode di portal MAGKOM, pendaftaran akun peserta/ketua, aktivasi akun.
   - **Langkah 2: Pemilihan Bidang & Unggah Berkas**:
     Pemilihan bidang penempatan (Aptika, Statistik, Persandian, Sekretariat), pengisian data diri, dan pengunggahan berkas administrasi. Masa verifikasi 1–3 hari kerja oleh tim sekretariat.
   - **Langkah 3: Pelaksanaan Magang & Logbook Harian**:
     Pengenalan pembimbing lapangan, kehadiran kerja kedinasan (Luring/WFO), pengisian logbook aktivitas harian setiap hari kerja, dan pengerjaan tugas proyek dinas.
   - **Langkah 4: Evaluasi, Lembar Nilai & Sertifikat**:
     Pemberian nilai akhir oleh pembimbing dinas, penerbitan Lembar Nilai resmi, serta unduhan Sertifikat Magang ber-QR Code validasi resmi Diskominfo Sleman.
5. **Bab IV: Tata Tertib & Ketentuan Kedinasan**:
   - Jam kerja magang: Senin–Kamis (07.30–15.30 WIB), Jumat (07.30–14.30 WIB).
   - Berpakaian rapi/seragam sekolah/almamater kampus.
   - Menjaga kerahasiaan data dan etika birokrasi pemerintahan.
6. **Bab V: Kanal Informasi & Bantuan Teknis**:
   - Kontak Helpdesk Sekretariat MAGKOM Diskominfo Sleman (WhatsApp/Telepon).
   - Tautan portal resmi: `http://magkom.slemankab.go.id/`.

---

## 3. Evaluasi Pendekatan Teknis Pembuatan PDF

### Opsi A: Dynamic PDF Generation via `barryvdh/laravel-dompdf` (Rekomendasi)
- **Cara Kerja**:
  - Dibuat controller khusus `PanduanPdfController` yang memuat view Blade khusus cetak `resources/views/pdf/buku-panduan-magang.blade.php`.
  - Menggunakan library `Barryvdh\DomPDF\Facade\Pdf::loadView(...)`.
  - Disajikan dengan opsi `stream()` (langsung terbuka di browser viewer) atau `download()` (otomatis mengunduh file `Panduan-Magang-Diskominfo-Sleman-2026.pdf`).
- **Kelebihan**:
  - Data selalu dinamis (misal daftar bidang aktif, nama kepala dinas, atau informasi kuota dapat disinkronkan otomatis dari basis data jika diinginkan).
  - Menggunakan library resmi `barryvdh/laravel-dompdf` yang sudah terpasang di `composer.json` proyek ini.
  - Tampilan visual presisi ukuran A4 dengan header kop surat dan footer nomor halaman otomatis.
- **Kekurangan**: Memerlukan render waktu singkat (~300ms) saat pertama kali diklik.

### Opsi B: File PDF Statis Pre-rendered di Folder `public/docs/`
- **Cara Kerja**: Menyimpan file PDF statis di `public/docs/panduan-magang.pdf` dan tombol langsung mengarah ke file tersebut.
- **Kekurangan**: Tidak dinamis; jika ada perubahan syarat berkas, narasi jurusan, atau kontak dinas di database, PDF harus diekspor ulang secara manual di luar aplikasi.
- **Kesimpulan Rekomendasi**: Dipilih **Opsi A** karena fleksibel, otomatis, dan seragam dengan dokumen kedinasan lain di MAGKOM (seperti surat keterangan diterima dan sertifikat).

---

## 4. Perubahan File yang Direncanakan

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `routes/web.php` | Modify | Menambahkan route publik `GET /panduan/download-pdf` dengan nama route `panduan.pdf`. |
| `app/Http/Controllers/Public/PanduanPdfController.php` | Create | Controller untuk merender view PDF, menyetel metadata (A4 portrait), dan mengalirkan file PDF ke pengguna. |
| `resources/views/pdf/buku-panduan-magang.blade.php` | Create | Template Blade cetak PDF berisi Kop Resmi Diskominfo, Bab I s/d V, tabel berkas, infografik tahapan, dan tata tertib. |
| `resources/views/public/landing.blade.php` | Modify | Mengubah `href="{{ route('panduan-alur') }}"` pada tombol `.btn-tahapan-download` menjadi `href="{{ route('panduan.pdf') }}"` dengan target `_blank`. |

---

## 5. Langkah-Langkah Implementasi Bertahap

- [ ] **Langkah 1**: Buat template Blade PDF `resources/views/pdf/buku-panduan-magang.blade.php` lengkap dengan Kop Surat Pemkab Sleman, format layout cetak A4, bab ketentuan, tabel berkas SMK & Mahasiswa, alur 4 tahap, serta tata tertib.
- [ ] **Langkah 2**: Buat controller `app/Http/Controllers/Public/PanduanPdfController.php` yang memanggil `Pdf::loadView('pdf.buku-panduan-magang')` dengan ukuran kertas A4 portrait dan nama file `Panduan-Pendaftaran-Magang-Diskominfo-Sleman.pdf`.
- [ ] **Langkah 3**: Daftarkan route di `routes/web.php`:
  `Route::get('/panduan-magang-pdf', [PanduanPdfController::class, 'download'])->name('panduan.pdf');`
- [ ] **Langkah 4**: Perbarui tombol di `resources/views/public/landing.blade.php`:
  Ganti `href="{{ route('panduan-alur') }}"` menjadi `href="{{ route('panduan.pdf') }}" target="_blank"`.
- [ ] **Langkah 5**: Bersihkan cache view dan route (`php artisan view:clear` & `php artisan route:clear`), lalu uji download langsung via HTTP request untuk memastikan output PDF valid (*application/pdf*).

---

## 6. Kriteria Selesai (Definition of Done)
1. **Unduhan PDF Berfungsi Nyata**: Saat tombol "Unduh Panduan PDF" diklik, peramban langsung membuka/mengunduh dokumen PDF resmi, bukan membuka halaman web alur lama.
2. **Kualitas Dokumen Standar Kedinasan**: Memuat Kop Resmi Sleman, tipografi bersih standar cetak A4, bab alur 4 tahap, panduan jalur Mandiri vs Kelompok (1 ketua), tabel berkas, dan ketentuan kedinasan.
3. **Format Standar**: Menggunakan Dompdf terpasang dengan output respon `application/pdf` berkecepatan tinggi tanpa error 500.
