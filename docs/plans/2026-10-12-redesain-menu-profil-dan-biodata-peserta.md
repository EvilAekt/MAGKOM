# Rencana Redesain Menu Profil & Biodata Peserta: Ramah Pengguna, Bersih, dan Tetap Keren

## 1. Tujuan
Menyederhanakan antarmuka (*UI/UX*) halaman Biodata & Profil Peserta (`resources/views/peserta/profil/show.blade.php`) agar tidak berantakan (*uncluttered*), menghilangkan duplikasi visual berlebih, serta menyatukan input data (termasuk kontak darurat) ke dalam alur pengisian formulir yang alami dan kohesif, dengan tetap mempertahankan estetika modern (*sleek, elevated Design System MagKom*).

---

## 2. Asumsi & Analisis Masalah Saat Ini

### Mengapa Tampilan Saat Ini Terasa Terlalu Rumit (*Complicated*)?
1. **Visual Duplication & Fragmentasi Kartu**:
   - Terdapat **7 kartu/wadah terpisah** di dalam satu layar (Hero banner, Student ID card, Checklist kelengkapan, Emergency card di kiri, Pasfoto card di kanan, Akun terdaftar card, Akademik card, WhatsApp card).
   - Data peserta (Nama, Institusi, Foto/Inisial) diulang di 3–4 titik berbeda di layar yang sama.
2. **Form Layout Anti-Pattern**:
   - Field form terbelah secara canggung: **Kontak Darurat** berada di kolom kiri bawah, sementara tombol **Simpan** dan field lainnya berada di kolom kanan. Pengguna rentan melewatkan input kontak darurat karena posisinya di luar aliran formulir utama.
3. **Hero Banner Redundant**:
   - Hero banner di atas memakan ketinggian vertikal hingga 190px padahal halaman profil membutuhkan fokus utama pada pemutakhiran data.

### Asumsi Teknis:
- Backend controller (`ProfilController@show`, `ProfilController@simpan`), model `PesertaProfil`, dan request validation `PesertaProfilRequest` sudah stabil dan tidak perlu perubahan skema database (zero breaking DB migration).
- Input field yang wajib tetap sama: `institusi`, `nim_nisn`, `jenjang`, `jurusan_id`, `no_hp`, serta opsional `foto`, `nama_kontak_darurat`, `no_kontak_darurat`.

---

## 3. Evaluasi Opsi Pendekatan Desain

| Kriteria | Opsi A: Tab-Based Form (Tab Data Diri & Tab Kontak) | Opsi B (Direkomendasikan): Modern 2-Column Split (Profile Identity Summary Card & Clean Unified Form) |
| :--- | :--- | :--- |
| **Konsep** | Membagi halaman ke dalam tab "Akademik" dan "Kontak & Darurat". | Kolom Kiri: **Kartu Identitas Modern (Profile Card)** dengan pasfoto interaktif, status penempatan, dan widget ringkas kelengkapan.<br>Kolom Kanan: **Satu Formulir Bersatu (Single Clean Container)** yang dibagi ke dalam 2 seksi elegan (Akademik & Kontak). |
| **Kelebihan** | Layar terlihat sangat pendek. | Semua field input terlihat langsung dalam satu aliran baca (tidak menyembunyikan input di balik tab). Tidak membingungkan saat ada error validasi di tab tersembunyi. |
| **Kekurangan** | User harus berpindah-pindah tab untuk input yang totalnya hanya 7 field; tombol submit harus menangani state multi-tab. | Perlu proporsi grid yang seimbang (misal 320px : 1fr) agar proporsional di layar laptop/tablet. |
| **Estetika / Kecepatan UX** | Cenderung kaku dan memperlambat input. | **Sangat modern, profesional, bersih, dan intuitif (seperti Stripe Dashboard / GitHub / modern HR portal).** |

> **Rekomendasi**: **Opsi B (Modern 2-Column Split Unified Form)**. Solusi ini menjaga halaman tetap ringkas, tidak memecah fokus pengguna, serta menyelesaikan masalah kontak darurat yang sebelumnya terselip di kolom kiri.

---

## 4. Rincian Desain Opsi B yang Diusulkan

### A. Kolom Kiri (Kartu Identitas Peserta & Unggah Foto):
- **Kartu Identitas Vertikal Modern**:
  - Foto profil bulat (80px) dengan tombol overlay upload kamera/ikon edit foto yang intuitif + tombol hapus foto mini.
  - Nama Lengkap (h1 putih/gelap kontras), Badge Status Penempatan (misal: "Peserta Aktif - Bidang Aptika" atau "Calon Peserta Magang").
  - Email terverifikasi & asal kampus/NIM.
  - **Kelengkapan Profil Mini**: Progress bar dinamis (misal: `85% Lengkap`) disertai daftar checklist ringkas tanpa membuat kartu baru.
  - Catatan keamanan/privasi data dalam mikro-banner yang tenang di bawah kartu profil.

### B. Kolom Kanan (Formulir Tunggal yang Bersih & Elegan):
- Satu kartu utama (`card-modern`) yang solid dan elegan:
  - **Header Formulir**: Judul "Perbarui Biodata Peserta" dengan instruksi singkat.
  - **Grup 1: Data Akademik**:
    - Asal Sekolah/Kampus (Full width).
    - NIM / NISN & Jenjang Pendidikan (2 kolom sejajar).
    - Jurusan / Program Studi (Full width).
  - **Divider Halus (Border tipis dengan label seksi)**.
  - **Grup 2: Kontak & Jalur Darurat (Kohesif)**:
    - Nomor WhatsApp / Telepon Utama (Full width dengan icon WA/telepon).
    - Grid 2 kolom: Nama Kontak Darurat (Wali/Orang Tua) & Nomor Telepon Darurat.
  - **Footer Aksi (Sticky atau Bottom-aligned)**:
    - Tombol "Simpan Perubahan" yang jelas, dengan status spinner saat submit dan proteksi anti-double submit.

---

## 5. Rencana Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/profil/show.blade.php` | Ubah | Restrukturisasi tata letak menjadi 2-kolom terpadu (*Profile Identity Card* di kiri dan *Unified Modern Form* di kanan), merapikan CSS token, dan menghilangkan fragmentasi 7 kartu menjadi 2 kartu utama yang estetis. |
| `docs/plans/2026-10-12-redesain-menu-profil-dan-biodata-peserta.md` | Buat | Dokumen master plan arsitektur UI/UX menu profil peserta. |

---

## 6. Langkah Implementasi Terurut

- [x] **Langkah 1**: Buat markup kerangka layout 2-kolom baru di `resources/views/peserta/profil/show.blade.php`:
  - Kolom kiri: Profile Identity Card (Avatar upload, nama, badge penempatan, checklist progres kelengkapan).
  - Kolom kanan: Unified Form Card (Seksi Akademik + Seksi Kontak & Darurat).
- [x] **Langkah 2**: Refaktor styling CSS menggunakan token Design System MagKom (`--blue-600`, `--neutral-100`, radius, shadow halus, typography Open Sans) agar terlihat modern, bersih, dan tidak monoton.
- [x] **Langkah 3**: Integrasikan preview foto profil instan (live client-side preview via FileReader) dengan tombol *Upload/Pilih Foto* dan *Hapus Foto* yang rapi di bawah avatar.
- [x] **Langkah 4**: Pastikan handling error validasi Laravel (`@error`) terpasang rapi pada setiap input field dengan styling pesan kesalahan yang jelas.
- [x] **Langkah 5**: Uji responsivitas pada layar mobile (< 768px), tablet (< 1024px), dan desktop.
- [x] **Langkah 6**: Jalankan regression test suite portal peserta (`php artisan test --filter=PesertaPortalWorkflowTest`) untuk memverifikasi fungsionalitas simpan data, upload foto, dan hapus foto tetap 100% lulus.

---

## 7. Strategi Testing
1. **Automated Feature Tests**:
   - `tests/Feature/PesertaPortalWorkflowTest.php` (uji route profil, upload foto, simpan biodata, validasi kontak darurat).
   - `tests/Feature/PesertaFullPortalFeatureTest.php`.
2. **Visual & Responsive Testing**:
   - Verifikasi tampilan desktop (1280px+), tablet (768px - 1024px), dan mobile (375px - 640px).
   - Verifikasi kontras warna teks dan elemen form untuk kepatuhan aksesibilitas (WCAG AA).

---

## 8. Risiko & Mitigasi
- **Risiko**: Input kontak darurat tidak tersimpan jika form terpisah.
  - **Mitigasi**: Seluruh input field dimasukkan ke dalam satu tag `<form>` yang membungkus kolom kanan secara utuh.
- **Risiko**: Form file upload (`enctype="multipart/form-data"`) tidak mengirim file foto jika input file di luar form.
  - **Mitigasi**: Letakkan input file di dalam elemen `<form>` yang sama atau gunakan attribute `form="formBiodataPeserta"`.

---

## 9. Kriteria Selesai (Definition of Done)
- [x] Tidak ada lagi 7 kartu terpisah yang membingungkan; diganti dengan 2 kontainer utama yang bersih dan terpadu.
- [x] Tidak ada duplikasi data visual (nama/avatar) yang berulang-ulang di banyak kotak.
- [x] Kontak darurat berada langsung di dalam alur formulir utama (bersama kontak WhatsApp).
- [x] Live preview upload foto profil berjalan lancar.
- [x] Seluruh automated test portal peserta lulus 100%.
- [x] Desain responsif di semua ukuran layar dan tampilan terasa segar, modern, serta ramah pengguna (*user-friendly*).
