# Rencana Kerja: Redesain & Implementasi UI/UX Halaman Profil & Biodata Peserta

> **Dokumen Rencana**: `docs/plans/2026-11-06-redesign-biodata-profil-peserta.md`  
> **Tanggal**: 06 November 2026  
> **Status**: Menunggu Persetujuan Pengguna (Draft)  
> **Referensi Desain**: `Biodata & Profil Peserta.png` (terlampir oleh user)

---

## 1. Tujuan
Membangun ulang antarmuka (UI/UX) dan interaktivitas halaman **Biodata & Profil Peserta** (`/peserta/profil`) pada Portal Peserta MAGKOM agar memiliki visual yang identik dan presisi dengan referensi desain `Biodata & Profil Peserta.png`. Halaman ini didesain bersih, modern, profesional (standar enterprise pemerintahan), responsif, serta terintegrasi penuh secara fungsional ke database tanpa merusak arsitektur atau aturan MAGKOM (`AGENTS.md`).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
1. **Pemisahan Asset (Kepatuhan Penuh AGENTS.md)**: Seluruh kode CSS (~660 baris) dan JavaScript (~100 baris) yang saat ini bersarang di dalam Blade `resources/views/peserta/profil/show.blade.php` akan diekstraksi ke file terdedikasi di `resources/css/pages/peserta/profil-show.css` dan `resources/js/pages/peserta/profil-show.js`, lalu dikompilasi melalui Vite.
2. **Preservasi Logika Bisnis & Keamanan**:
   - Relasi data profil (`PesertaProfil`), jurusan (`Jurusan`), user (`User`), dan penempatan (`Penempatan`) tetap dipertahankan utuh.
   - Fitur kepatuhan hukum **UU PDP No. 27/2022 (SEC-41)** untuk persetujuan orang tua/wali khusus jenjang SMK tetap dipertahankan secara kondisional.
   - Jika peserta terdaftar dalam kelompok magang (`$kelompokMagang`), data roster tim tetap dapat diakses/ditampilkan secara elegan tanpa mengganggu tata letak utama profil personal.
3. **Perilaku Tombol "Edit" pada Card**:
   - Tombol "Edit" di bagian header card "Informasi Akademik & Pendidikan" dan "Kontak Utama & Jalur Darurat" akan memfokuskan kursor ke input pertama pada masing-masing section secara halus (*smooth scroll & focus*), memberikan affordance UX yang jelas bagi peserta.
4. **Validasi Persetujuan & Tombol Simpan**:
   - Tombol utama "Simpan Biodata Profil" akan otomatis terkunci (*disabled*) sampai checkbox "Data yang saya isikan sudah benar dan siap digunakan untuk administrasi magang" dicentang dan field-field wajib telah terisi.

---

## 3. Pendekatan Teknis & Evaluasi Arsitektur

### Evaluasi Opsi Penanganan Frontend:
- **Opsi A (Menyisipkan CSS/JS langsung di Blade)**:
  - *Kelebihan*: Cepat ditulis.
  - *Kekurangan*: Melanggar keras aturan `AGENTS.md` (bagian 3.1 & 3.2), menyebabkan file Blade bengkak (>1300 baris), sulit di-cache oleh browser, dan tidak termonitor oleh Vite bundler.
- **Opsi B (Ekstraksi ke Vite Assets ES Module & Scoped CSS — OPSI TERPILIH)**:
  - *Kelebihan*:
    - Sesuai 100% dengan standar arsitektur MAGKOM (`AGENTS.md`).
    - File Blade menjadi murni markup semantik bersih (~250-300 baris).
    - Performa tinggi, CSS/JS ter-minify dan di-cache secara efisien oleh Vite.
    - Script interaktif modular (realtime progress recalculation, file picker, toast feedback, double-submit lock).
  - *Kekurangan*: Memerlukan pendaftaran entry point di `vite.config.js` dan kompilasi `npm run build`.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `vite.config.js` | Modify | Menambahkan entry point `resources/css/pages/peserta/profil-show.css` dan `resources/js/pages/peserta/profil-show.js`. |
| `resources/css/pages/peserta/profil-show.css` | Create | Stylesheet khusus halaman profil selaras referensi desain: container 800px, navy identity banner (#073B82), status badge (#F59E0B), kelengkapan card, checklist grid, card section form dengan icon badge, helper texts, dan bottom approval bar. |
| `resources/js/pages/peserta/profil-show.js` | Create | Script modular ES Module untuk penanganan: upload & preview foto, dynamic real-time progress & checklist recalculation, tombol Edit scroll/focus, validasi checkbox persetujuan, dan proteksi double-submit form. |
| `resources/views/peserta/profil/show.blade.php` | Modify | Restrukturisasi markup HTML semantik agar identik dengan referensi visual `Biodata & Profil Peserta.png`. Menghapus seluruh tag `<style>` dan `<script>` inline, mengimpor asset via `@vite`. |

---

## 5. Langkah Implementasi Terstruktur

### Fase 1: Pendaftaran Vite Pipeline
- [ ] Daftarkan path `resources/css/pages/peserta/profil-show.css` dan `resources/js/pages/peserta/profil-show.js` ke dalam array `input` di `vite.config.js`.

### Fase 2: Pembangunan Stylesheet Terdedikasi (`profil-show.css`)
- [ ] Buat `resources/css/pages/peserta/profil-show.css`:
  - Outer background wrapper `#EEF3FA`.
  - Main container card max-width 820px, radius 16px, background putih `#ffffff`, subtle shadow.
  - Page title "Biodata & Profil Peserta" tipografi tegas navy.
  - Header panel identitas navy `#073B82`: avatar lingkaran 72px dengan badge kamera kuning/oranye di sudut kanan bawah, nama lengkap teks putih, pill badge kuning `CALON PESERTA MAGANG`, email transparan putih, tombol putih "Pilih Pasfoto" dengan ikon upload.
  - Card "KELENGKAPAN BIODATA": progress bar navy dengan background track abu-abu, teks persetase tebal, checklist 5 indikator dengan ikon lingkaran centang navy dan lingkaran kosong.
  - Card "Informasi Akademik & Pendidikan": icon cap navy, tombol edit, form grid responsif 1-kolom & 2-kolom, styling input/select border halus, placeholder dan helper text.
  - Card "Kontak Utama & Jalur Darurat": icon telepon navy, tombol edit, input nomor WA, email, textarea alamat domisili, kontak darurat.
  - Checkbox persetujuan administrasi magang & tombol submit "Simpan Biodata Profil" dengan ikon centang serta animasi status loading.
  - Breakpoint responsif untuk mobile dan tablet (stacking menjadi 1 kolom tanpa overflow).

### Fase 3: Pembangunan Logika Interaktif ES Module (`profil-show.js`)
- [ ] Buat `resources/js/pages/peserta/profil-show.js`:
  - Listener upload foto: trigger file dialog saat tombol "Pilih Pasfoto" atau tombol kamera avatar diklik.
  - Validasi ukuran file (maksimal 2MB) dan tipe format gambar (JPEG/PNG/WEBP).
  - Live preview avatar secara instan saat berkas dipilih serta tombol pembatalan/hapus foto.
  - Live recalculation engine: fungsi dinamis yang memantau perubahan input form dan memperbarui persentase serta status centang pada 5 kategori checklist secara instan tanpa perlu reload halaman.
  - Handler tombol "Edit": memfokuskan kursor ke input pertama pada card masing-masing.
  - Handler validasi tombol simpan: menghubungkan checkbox persetujuan dengan status enable/disable tombol "Simpan Biodata Profil".
  - Double submit prevention: menonaktifkan tombol dan memunculkan animasi spinner saat form dikirimkan.

### Fase 4: Restrukturisasi Markup Blade (`show.blade.php`)
- [ ] Bersihkan tag `<style>` dan `<script>` yang ada di `resources/views/peserta/profil/show.blade.php`.
- [ ] Hubungkan aset Vite via `@push('styles') @vite(['resources/css/pages/peserta/profil-show.css']) @endpush` dan `@push('scripts') @vite(['resources/js/pages/peserta/profil-show.js']) @endpush`.
- [ ] Susun struktur markup HTML sesuai layout referensi `Biodata & Profil Peserta.png`.
- [ ] Pertahankan old value form, feedback `@error`, csrf protection, flash notification, serta preservasi informasi kelompok magang.

### Fase 5: Kompilasi Asset & Verifikasi
- [ ] Jalankan kompilasi aset frontend menggunakan Node/Vite (`npm run build`).
- [ ] Jalankan test platform (`php artisan test` dengan PHP 8.3 Laragon).
- [ ] Verifikasi halaman di browser (`http://127.0.0.1:8000/peserta/profil`) untuk memastikan tampilan 100% presisi dengan gambar referensi.

---

## 6. Strategi Testing

1. **Happy Path**:
   - Peserta membuka halaman `/peserta/profil`: semua data yang tersimpan sebelumnya terisi dengan benar pada form.
   - Indikator persentase dan checklist kelengkapan menampilkan status akurat sesuai kelengkapan data.
   - Peserta memilih foto baru: pratinjau foto langsung berubah, checklist "Pasfoto Resmi" otomatis tercentang.
   - Peserta mencentang checkbox persetujuan: tombol "Simpan Biodata Profil" menjadi aktif.
   - Peserta menekan tombol simpan: data tersimpan ke database, redirect kembali dengan pesan sukses "Biodata profil peserta berhasil disimpan".
2. **Sad Path & Validasi**:
   - Peserta memilih file > 2MB: notifikasi peringatan muncul, input direset.
   - Field wajib (Institusi, NIM/NISN, Jenjang, Jurusan, No HP, Email) dikosongkan: validasi form HTML5 dan server-side `PesertaProfilRequest` memunculkan pesan error dengan styling yang jelas.
   - Checkbox belum dicentang: tombol simpan tetap disabled dan tidak dapat diklik.
3. **Unit / Feature Tests**:
   - Jalankan `vendor/bin/phpunit tests/Feature/Peserta/ProfilTest.php` (atau test suite peserta yang relevan).

---

## 7. Risiko & Mitigasi

- **Risiko**: Potensi bentrok style global Gentelella/AdminLTE terhadap input form baru.  
  **Mitigasi**: Berikan selector ter-scope dengan class induk `.profil-container-page` untuk menghindari efek samping ke halaman lain.
- **Risiko**: Peserta SMK memerlukan persetujuan orang tua/wali sesuai UU PDP (SEC-41).  
  **Mitigasi**: Tetap sediakan container persetujuan wali SMK secara kondisional yang menyatu rapi dalam alur tanpa merusak visual referensi.

---

## 8. Kriteria Selesai (Definition of Done)
- [ ] Visual halaman `/peserta/profil` sangat presisi dan selaras dengan gambar referensi `Biodata & Profil Peserta.png`.
- [ ] Tidak ada tag `<style>` atau `<script>` di dalam file Blade (patuh `AGENTS.md`).
- [ ] CSS dan JS berada di `resources/` dan terintegrasi pada `vite.config.js`.
- [ ] Fitur upload foto, preview, dan inisial avatar berfungsi dengan baik.
- [ ] Indikator persentase dan checklist kelengkapan biodata terhitung otomatis dan real-time.
- [ ] Tombol Edit pada card berfungsi memfokuskan input.
- [ ] Checkbox persetujuan mengontrol keaktifan tombol simpan.
- [ ] Data berhasil disimpan dan dimuat ulang dari database secara konsisten.
- [ ] Tampilan responsif di layar desktop, tablet, dan smartphone.
- [ ] Test otomatis lulus tanpa regresi.

