# Plan: Revamp Halaman Profil & Biodata Peserta yang Dinamis, Interaktif, dan Tidak Monoton

| Metadata | Keterangan |
|---|---|
| **Tanggal Pembuatan** | 11 Oktober 2026 |
| **Status Dokumen** | Selesai Diimplementasikan (Completed) |
| **Target Area** | Portal Peserta: Modul Profil & Biodata (`resources/views/peserta/profil/show.blade.php`), Controller (`Peserta\ProfilController`), Model (`PesertaProfil`) |
| **Kebutuhan Pengguna** | Mengubah tampilan profil dan biodata agar **tidak monoton**, lebih segar, interaktif, modern, dan memberikan pengalaman visual (*look & feel*) yang menarik serta ramah pengguna. |

---

## 1. Tujuan
1. **Menghilangkan Kesan Monoton pada Formulir Profil**:
   - Merombak halaman profil dari bentuk kartu formulir datar tunggal (*single flat box*) menjadi arsitektur halaman modern yang berdimensi (*split card / 2-column layout*).
   - Menambahkan **Kop Profil Dinamis (Profile Identity Banner)** dengan cover grafis MagKom, avatar berbingkai, badge status verifikasi, dan indikator persentase kelengkapan biodata (*Profile Completeness Score*).
2. **Menata Informasi Menjadi 3 Seksi Tematik yang Menarik**:
   - **Kartu Identitas & Status Peserta (Kolom Kiri - 35%)**:
     - Avatar inisial/foto profil beresolusi tinggi dengan emblem status magang.
     - Ringkasan ID Peserta: Nama, Email, NISN/NIM, Asal Kampus/Sekolah, dan Jurusan.
     - Widget *Kelengkapan Data*: Checklist visual progres data diri (Data Akun ✓, Akademik ✓, Kontak WhatsApp ✓, Kontak Darurat ✓).
     - Info Keamanan Akun: Waktu login terakhir dan pintasan ganti kata sandi.
   - **Formulir Interaktif Tematik (Kolom Kanan - 65%)**:
     - **Kartu 1: Informasi Akademik & Pendidikan (Graduate/Academic Card)**:
       - Header dengan ikon toga wisuda (`🎓`), form input dengan *leading icon* (ikon gedung sekolah, kartu identitas, dan buku jurusan).
     - **Kartu 2: Kontak Utama & Komunikasi (Communication Card)**:
       - Header dengan ikon pesan/telepon (`📱`), form WhatsApp dengan petunjuk format aktif.
     - **Kartu 3: Kontak Darurat & Wali (Safety & Emergency Card)**:
       - Didesain dengan aksen tameng pengaman (`🛡️`) berlatar lembut untuk menegaskan fungsi perlindungan dan keamanan darurat instansi.
3. **Meningkatkan Interaktivitas & Kenyamanan Pengguna (Micro-Interactions)**:
   - Input fields dilengkapi *input group leading icons* (ikon visual di dalam kolom input).
   - Indikator kelengkapan data otomatis terbarui (*live feedback*).
   - Tombol simpan modern dengan indikator sukses dan proteksi *anti double-submit*.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis:
- **A-1 (Skema Database Tetap Utuh)**: Seluruh kolom pada model `PesertaProfil` (`institusi`, `nim_nisn`, `jenjang`, `jurusan_id`, `no_hp`, `nama_kontak_darurat`, `hubungan_kontak_darurat`, `no_kontak_darurat`) tetap dipertahankan persis seperti skema saat ini. Tidak ada migrasi destruktif pada basis data.
- **A-2 (Kompatibilitas Form Submission & Validasi)**: Form tetap melakukan POST ke `route('peserta.profil.simpan')` dengan validasi `PesertaProfilRequest`. Seluruh nama field input tetap identik sehingga seluruh feature test dan alur registrasi yang bergantung pada profil tetap 100% lulus.
- **A-3 (Frontend Styling)**: Menggunakan Vanilla CSS murni dan token warna resmi MagKom (`--blue-600`, `--neutral-*`, skala tipografi rem) agar performa render instan tanpa dependensi library eksternal.

### Pertanyaan Terbuka:
- Tidak ada pertanyaan pemblokir.

---

## 3. Pendekatan Desain

### 3.1 Struktur Tata Letak (Layout Architecture)
- **Opsi 1: Form Panjang 1 Kolom Datar (Desain Lama).**
  - *Kekurangan*: Monoton, membosankan, terlihat seperti formulir sensus biasa, dan membuat pengguna malas membaca.
- **Opsi 2: Layout 2 Kolom Asimetris (Profile Identity Card 35% + Thematic Section Cards 65%) (Direkomendasikan).**
  - *Kelebihan*: Tampilan jauh lebih dinamis, setara portal karier modern (seperti LinkedIn/GitHub/Vercel). Kolom kiri memberikan ringkasan status instan, sedangkan kolom kanan membagi input ke dalam modul-modul tematik yang tidak melelahkan mata.
  - *Rekomendasi*: **Opsi 2**.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
|---|---|---|
| `app/Http/Controllers/Peserta/ProfilController.php` | **Ubah** | Mengirimkan data pendukung ke view (penempatan aktif, riwayat pendaftaran, checklist, dan persentase kelengkapan profil). |
| `resources/views/peserta/profil/show.blade.php` | **Ubah** | Merombak total tampilan: menambahkan Hero Banner Profil, Kartu Identitas Peserta (Kolom Kiri), Kartu Akademik, Kartu Kontak, dan Kartu Kontak Darurat dengan input leading-icons dan desain non-monoton. |
| `tests/Feature/PesertaFullPortalFeatureTest.php` | **Verifikasi** | Memastikan tes pembukaan halaman profil dan penyimpanan data profil tetap lulus 100%. |

---

## 5. Langkah Implementasi Terperinci

### Langkah 1: Penyiapan Data Pendukung di Controller
- [x] Buka [app/Http/Controllers/Peserta/ProfilController.php](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Peserta/ProfilController.php).
- [x] Hitung persentase kelengkapan profil (berdasarkan kelengkapan field wajib: institusi, nim_nisn, jenjang, jurusan_id, no_hp, kontak darurat).
- [x] Ambil relasi pendaftaran dan penempatan aktif untuk ditampilkan pada Kartu Identitas Profil.

### Langkah 2: Merancang UI Profil Non-Monoton di `peserta/profil/show.blade.php`
- [x] **Profile Header Banner**:
  - Banner latar belakang grafis MagKom dengan gradient biru tua berkelas.
  - Avatar lingkaran 84px berbingkai putih dengan inisial nama peserta.
  - Nama lengkap, email, dan chip peran: "Akun Peserta Magang".
- [x] **Kolom Kiri (Identity & Summary Card)**:
  - Kartu Ringkasan Status Magang (Identitas Magang / Calon Peserta / Sedang Aktif di Bidang Aptika/IKP).
  - Progress Bar Kelengkapan Profil (misal: "85% - Profil Hampir Siap").
  - Checklist item data diri (Akun, Akademik, Kontak, Darurat).
- [x] **Kolom Kanan (Thematic Section Cards)**:
  - **Seksi 1: Informasi Akademik & Kampus**:
    - Kolom Nama Sekolah/Kampus dengan ikon gedung `🏫`.
    - Kolom NIM/NISN dengan ikon kartu identitas `💳`.
    - Kolom Jenjang & Jurusan dengan ikon toga wisuda `🎓`.
  - **Seksi 2: Kontak Utama & WhatsApp**:
    - Kolom Nomor WhatsApp dengan ikon ponsel `📱` dan petunjuk komunikasi koordinasi mentor.
  - **Seksi 3: Kontak Darurat & Wali**:
    - Kotak aksen lembut dengan ikon tameng `🛡️`:
    - Kolom Nama Kontak Darurat `👤`.
    - Kolom Hubungan (Orang Tua / Wali / Kakak).
    - Kolom Nomor Telepon Darurat `📞`.
- [x] **Action Bar Simpan**:
  - Tombol simpan modern dengan status icon checkmark dan proteksi anti-double submit.

### Langkah 3: Pengujian & Quality Assurance
- [x] Jalankan feature tests: `PesertaFullPortalFeatureTest`, `PesertaPortalWorkflowTest`, dan `PesertaClassroomTugasLogbookTest`.
- [x] Pastikan tidak ada regresi dan penyimpanan data berfungsi normal (20 passed, 96 assertions).

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Halaman profil memiliki Profile Hero Banner yang menarik dengan avatar berbingkai.
- [x] Tata letak mengadopsi 2 kolom asimetris (Kartu Identitas Kiri 35% & Modul Formulir Kanan 65%).
- [x] Input fields dilengkapi *leading icons* tematik (gedung, kartu, toga, ponsel, tameng).
- [x] Terdapat bar progres kelengkapan profil yang interaktif.
- [x] Form submit ke controller bekerja normal dan berhasil menyimpan perubahan ke database.
- [x] Seluruh unit & feature test yang berhubungan dengan profil lulus 100%.
