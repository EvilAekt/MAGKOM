# Plan: Pembaruan Judul & Subjudul Seksi Penempatan & Kuota Magang Landing Page

Dokumen ini merinci rencana pembaruan copy (*heading* dan *subheading*) pada seksi penempatan/kuota di landing page MAGKOM, agar lebih representatif sebagai pusat pendaftaran, eksplorasi formasi bidang penempatan, dan aksi pendaftaran peserta.

---

## 1. Latar Belakang & Masalah

- **Kondisi Saat Ini**:
  - **Judul**: `Ketersediaan Kuota Magang`
  - **Subjudul**: `Pilih jenjang pendidikan dan bulan mulai magang untuk melihat kuota jurusan yang tersedia.`
- **Masalah**:
  1. Terlalu pasif dan bernuansa administratif (seperti judul tabel data internal, bukan gerbang pendaftaran).
  2. Seksi ini adalah **jantung utama konversi portal**, di mana calon peserta menjelajahi bidang penempatan, memilih jurusan yang sesuai, memeriksa kuota realtime, dan langsung menekan tombol **Daftar Sekarang**.
  3. Subjudul lama masih menyebut "melihat kuota jurusan yang tersedia", padahal alur seksi ini telah bertransformasi menjadi **Penempatan Bidang** dengan pemilihan **Jurusan Relevan** di dalamnya.
  4. Perlu judul dan deskripsi pengantar yang lebih hidup, menarik, dan selaras dengan menu navigasi *Penempatan*.

---

## 2. Alternatif Solusi Copywriting

Berikut adalah 3 opsi perumusan judul dan subjudul:

### Opsi 1: Action-Driven & Terintegrasi Pendaftaran (Rekomendasi Utama)
- **Judul (Heading)**:
  > **Pilih Penempatan & Ketersediaan Kuota Magang**
- **Subjudul (Subheading)**:
  > **Pilih jenjang pendidikan, tentukan bulan mulai, lalu pilih bidang penempatan dan jurusanmu untuk langsung mengamankan kuota pendaftaran.**
- **Kelebihan**:
  - Menggabungkan elemen menu navigasi (*Penempatan*), transparansi kuota (*Ketersediaan Kuota*), dan ajakan bertindak (*Daftar*).
  - Mengalir alami dari langkah 1 (Kamu siapa?), langkah 2 (Mau mulai kapan?), langkah 3 (Magang sama siapa?), hingga pemilihan kartu bidang & jurusan.

### Opsi 2: Professional & Formasi Karier (Nuansa Pemerintahan Modern)
- **Judul (Heading)**:
  > **Formasi Penempatan & Pendaftaran Kuota Magang**
- **Subjudul (Subheading)**:
  > **Temukan posisi penempatan di lingkungan Diskominfo Sleman yang sesuai dengan kompetensi jurusan Anda, lalu lakukan pendaftaran secara langsung.**
- **Kelebihan**:
  - Terkesan sangat formal, prestisius, dan berbobot sebagai program resmi pemerintah daerah.

### Opsi 3: Ringkas & Langsung ke Sasaran (Punchy & Minimalis)
- **Judul (Heading)**:
  > **Pilih Penempatan & Kuota Pendaftaran**
- **Subjudul (Subheading)**:
  > **Tentukan jadwal magang dan pilih bidang penempatan yang sesuai dengan jurusanmu untuk mendaftar.**
- **Kelebihan**:
  - Singkat, cepat dibaca di layar perangkat mobile (*glanceable*).

---

## 3. Ruang Lingkup Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`app/Services/LandingPage/BlockRegistry.php`](file:///d:/MAGKOM/MAGKOM/app/Services/LandingPage/BlockRegistry.php) | **Modify** | Memperbarui default content `'heading'` dan `'subheading'` pada blok `'kuota'`. |
| [`app/Services/LandingPage/LegacyCmsAdapter.php`](file:///d:/MAGKOM/MAGKOM/app/Services/LandingPage/LegacyCmsAdapter.php) | **Modify** | Memperbarui nilai fallback legacy getter untuk `landing_kuota_heading` dan `landing_kuota_subheading`. |
| [`app/Http/Controllers/SuperAdmin/LandingCmsController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/SuperAdmin/LandingCmsController.php) | **Modify** | Memperbarui fallback getter untuk input form CMS SuperAdmin. |
| [`app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php`](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php) | **Modify** | Memperbarui fallback getter untuk inspector Site Editor visual. |
| [`resources/views/public/blocks/kuota.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/public/blocks/kuota.blade.php) | **Modify** | Memperbarui teks fallback bawaan pada variabel `$kuotaHeading` dan `$kuotaSubheading`. |

*(Catatan: Jika ada draft atau live revision aktif di CMS yang telah mengunci string lama, sistem akan menyelaraskan pengaturan agar teks baru langsung aktif di landing page).*

---

## 4. Langkah-Langkah Implementasi Bertahap

- [x] **Langkah 1**: Konfirmasi pilihan judul & subjudul dengan pengguna (Opsi 1 disetujui pengguna).
- [x] **Langkah 2**: Perbarui nilai default di layer Block Engine & Service:
  - `BlockRegistry.php` (default content blok `kuota`).
  - `LegacyCmsAdapter.php` (fallback getter adapter).
- [x] **Langkah 3**: Perbarui fallback di Controller SuperAdmin CMS:
  - `LandingCmsController.php` & `LandingCmsSiteEditorController.php`.
- [x] **Langkah 4**: Perbarui fallback Blade di `resources/views/public/blocks/kuota.blade.php`.
- [x] **Langkah 5**: Bersihkan cache view Laravel (`php artisan view:clear`) dan periksa database pengaturan live bila diperlukan.
- [x] **Langkah 6**: Jalankan regression testing PHP 8.3 (`artisan test`) dan Laravel Pint (`pint --test`).

---

## 5. Strategi Pengujian (Verification Strategy)

1. **Pengujian Tampilan & Konten Publik**:
   - Memastikan saat mengakses `GET /`, judul baru dan subjudul baru tampil dengan sempurna tanpa pemotongan teks (*wrapping* rapi di desktop dan mobile).
   - Memastikan tautan anchor `#penempatan` dan tombol hero "Daftar Sekarang" tetap mengarahkan dengan mulus ke seksi ini.
2. **Pengujian Kompatibilitas CMS Staging & Site Editor**:
   - Memastikan saat SuperAdmin membuka Site Editor / CMS Staging, field inspector menampilkan judul dan subjudul baru dan tetap dapat diubah jika diperlukan.
3. **Automated Feature Tests**:
   - `tests/Feature/LandingPageTest.php`
   - `tests/Feature/LandingCmsStagingTest.php`
   - `tests/Feature/LandingCmsVisualBuilderAndThemeTest.php`
   - `tests/Feature/LandingPage/FinalQualityAssuranceTest.php`

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Judul seksi penempatan di landing page telah berubah menjadi judul yang lebih dinamis, representatif, dan mengajak mendaftar: **"Pilih Penempatan & Ketersediaan Kuota Magang"**.
- [x] Subjudul telah diselaraskan dengan alur pemilihan jenjang, jadwal, bidang penempatan, dan jurusan: **"Pilih jenjang pendidikan, tentukan bulan mulai, lalu pilih bidang penempatan dan jurusanmu untuk langsung mengamankan kuota pendaftaran."**.
- [x] Semua 72+ automated tests tetap lulus 100% (507 assertions).
- [x] Tidak ada regresi pada tampilan responsif mobile maupun desktop.

