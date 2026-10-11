# Rencana Kerja: Revamp UI/UX Menu Pendaftaran Magang Peserta

## 1. Tujuan
Mentransformasikan antarmuka menu **Pendaftaran Magang Peserta** (`peserta/pendaftaran/index.blade.php`) dari tampilan tabel statis dan dropdown biasa menjadi antarmuka yang modern, dinamis, visual, dan menarik (selaras dengan standar desain estetika modern MAGKOM pada dashboard dan profil peserta), tanpa mengubah aturan bisnis (BR-03, BR-17, AC-03-1).

---

## 2. Asumsi & Kebutuhan Teknis
- **Aturan Bisnis Tetap Berlaku**:
  - *BR-03 & BR-17*: Peserta hanya dapat memiliki maksimal 1 pengajuan magang aktif pada satu waktu.
  - *AC-03-1*: Profil biodata wajib lengkap sebelum membuat pengajuan baru.
  - *BR-08*: Durasi magang fleksibel antara 1 hingga 6 bulan.
- **Ketersediaan Data Backend**:
  - `PendaftaranController::index` telah menyediakan: `$pendaftaranList`, `$pendaftaranAktif`, `$bulanAktifList`, `$kuotaList`, dan `$profil`.
  - Tidak memerlukan migrasi skema database baru; seluruh pembaruan berfokus pada lapisan presentasi (Blade, CSS responsif, dan interaktivitas JS vanilla).

---

## 3. Evaluasi Opsi Desain

| Kriteria | Opsi A: Facelift Kosmetik Tabel & Modal | Opsi B: Modern Portal Dashboard & Interactive Cards (Rekomendasi) |
| :--- | :--- | :--- |
| **Konsep Visual** | Mempercantik border dan warna tabel riwayat yang ada, modal dropdown tetap biasa. | **Hero Header**, **Active Status Card Showcase**, **Katalog Kuota Visual Card**, **Tabel Riwayat Interaktif**, dan **Modal Berbasis Chip/Radio Card**. |
| **User Experience (UX)** | Pasif, peserta hanya melihat tabel dan pop-up dropdown standar. | Aktif & Informatif: peserta dapat langsung melihat ketersediaan kuota bulan magang untuk jurusannya secara visual, estimasi durasi magang interaktif. |
| **Estetika & Kesan** | Cenderung monoton. | Sangat menarik, modern, profesional (standar portal magang nasional / Kampus Merdeka). |
| **Kompatibilitas** | Aman. | 100% kompatibel dengan route & controller yang ada. |

**Keputusan**: Dipilih **Opsi B** untuk memberikan pengalaman visual yang memukau dan memudahkan peserta memantau serta mengajukan magang.

---

## 4. Rincian Desain Antarmuka Baru

### A. Hero Header Banner
- Banner gradien biru modern dengan watermark tipografis MAGKOM dan ikon kedinasan.
- Menampilkan sambutan personal, asal kampus/institusi peserta, jurusan terdaftar, dan status izin pendaftaran (Siap Mendaftar / Memiliki Pengajuan Aktif / Profil Belum Lengkap).

### B. Showcase Pengajuan Aktif (Jika Ada Pendaftaran Berjalan)
- Kartu spotlight visual dengan *status indicator bar* dan *timeline mini*.
- Menampilkan nomor registrasi `#REG-xxxxx`, periode magang, jurusan kuota, dan estimasi durasi.
- Tombol aksi utama yang mencolok: *"Buka Berkas & Pantau Status Pengajuan →"*.

### C. Kartu Katalog Periode & Kuota Magang Tersedia
- Grid kartu periode magang untuk jurusan peserta (misal: "November 2026", "Desember 2026").
- Indikator ketersediaan kursi kuota (*badge live progress*):
  - Kuota Tersedia (hijau): misal *"Tersisa 4 Kursi"*
  - Kuota Terbatas (kuning): *"Tersisa 1 Kursi"*
  - Kuota Penuh (merah/abu): *"Penuh (Waiting List)"*
- Tombol langsung *"Pilih Periode Ini"* yang membuka formulir dengan periode terpilih otomatis.

### D. Riwayat Pendaftaran yang Disempurnakan
- Desain *card-table* responsif dengan *hover state*, avatar nomor registrasi, tanggal pembuatan dengan ikon kalender, dan *badge status* bernuansa modern yang seragam.
- *Empty state* artistik yang ramah dan mengajak pengguna mendaftar jika belum memiliki riwayat.

### E. Modal Pengajuan Magang Interaktif (Bila Mendaftar Baru)
- Ganti dropdown biasa dengan pemilih periode berbasis kartu/radio box visual.
- Pilihan durasi magang (1 s/d 6 bulan) menggunakan *interactive pill buttons / chips* dengan pratinjau rentang tanggal langsung (*live preview range*) yang jelas dan informatif.

---

## 5. Rencana Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `docs/plans/2026-10-11-revamp-menu-pendaftaran-magang-peserta.md` | Buat | Dokumen perencanaan arsitektur dan UI/UX revamp pendaftaran magang. |
| `resources/views/peserta/pendaftaran/index.blade.php` | Ubah | Merombak layout UI menjadi Hero Header, Showcase Pengajuan Aktif, Visual Kuota Grid, Modern History Table, dan Interaktif Modal. |
| `tests/Feature/PesertaPortalWorkflowTest.php` | Verifikasi | Memastikan seluruh alur pengajuan pendaftaran dan kelengkapan dokumen tetap lulus 100%. |

---

## 6. Langkah-Langkah Implementasi
- [x] **Langkah 1**: Perbarui struktur CSS internal di `resources/views/peserta/pendaftaran/index.blade.php` (komponen hero, active showcase card, kuota cards, radio chips, responsive grid).
- [x] **Langkah 2**: Implementasikan Hero Banner dinamis dan Kartu Showcase Pengajuan Aktif.
- [x] **Langkah 3**: Implementasikan Bagian Katalog Kuota Bulan Magang visual berdasarkan jurusan peserta.
- [x] **Langkah 4**: Terapkan Tabel Riwayat Pendaftaran modern dan Empty State artistik.
- [x] **Langkah 5**: Revamp Modal Formulir Pendaftaran Baru dengan pemilih durasi berbasis Pill/Chip dan kalkulasi rentang tanggal real-time.
- [x] **Langkah 6**: Jalankan automated test suite `tests/Feature/PesertaPortalWorkflowTest.php` dan `tests/Feature/PesertaFullPortalFeatureTest.php`.
- [x] **Langkah 7**: Verifikasi tampilan langsung pada browser lokal port 8000.

---

## 7. Strategi Pengujian (QA Strategy)
1. **Automated Feature Tests**:
   - `PesertaPortalWorkflowTest`: Verifikasi halaman pendaftaran terbuka sukses, validasi form, dan alur pendaftaran (PASS).
   - `PesertaFullPortalFeatureTest`: Verifikasi izin akses dan alur tahapan status (PASS).
2. **Visual & Behavioral Check**:
   - Skenario 1: Peserta baru tanpa pengajuan (tampil katalog kuota + form aktif).
   - Skenario 2: Peserta dengan pengajuan aktif (tampil kartu showcase aktif + tombol pengajuan baru terkunci dengan pesan edukatif).
   - Skenario 3: Peserta dengan profil belum lengkap (tampil banner edukasi + tombol CTA ke profil).

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Halaman `peserta/pendaftaran` memiliki desain visual modern, tidak monoton, dan selaras dengan tema portal peserta.
- [x] Kuota magang jurusan peserta dapat dilihat dengan jelas dan intuitif.
- [x] Modal pendaftaran baru lebih interaktif dengan pemilihan durasi yang mudah dipahami.
- [x] Seluruh aturan bisnis (1 pendaftaran aktif, profil wajib lengkap) tetap berfungsi tanpa celah.
- [x] Seluruh unit dan feature tests lolos 100% (Green).
