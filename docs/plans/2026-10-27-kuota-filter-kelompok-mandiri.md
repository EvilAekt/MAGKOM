# Rencana Kerja: Integrasi Pilihan Mandiri / Kelompok pada Kuota Explorer

**Tanggal**: 27 Oktober 2026  
**Status**: Selesai Diimplementasikan & Terverifikasi (Production Ready)  
**Dokumen**: `docs/plans/2026-10-27-kuota-filter-kelompok-mandiri.md`  
**Area Terkait**: Filter Kuota Section (`resources/views/public/landing.blade.php`, `public/css/landing-modern.css`, dan `public/js/landing-interactive.js`)

---

## 1. Tujuan
Mengembalikan dan mengintegrasikan kembali fitur pemilihan **Tipe Pendaftaran (Mandiri vs Kelompok)** beserta pemilih **Jumlah Anggota Tim (2–4 Orang)** ke dalam antarmuka Kuota Explorer landing page modern, dengan tetap mempertahankan estetika modern minimalis serta keselarasan query parameter URL menuju formulir registrasi (`/register?bulan=...&jurusan=...&tipe=...&jumlah=...`).

---

## 2. Analisis Kebutuhan & Asumsi Kerja

### A. Kondisi Saat Ini vs Kondisi Sebelumnya
1. **Kondisi Sebelumnya (pada arsip `landing.blade.php.bak`)**:
   - Memiliki 3 kontrol utama:
     - Jenjang (SMK vs Mahasiswa)
     - Periode Bulan Magang
     - Tipe Pendaftaran: **Mandiri (1 Orang)** vs **Kelompok (2–4 Orang)**
   - Jika mode Kelompok dipilih:
     - Muncul selektor jumlah anggota: `2 Orang`, `3 Orang`, `4 Orang`.
     - Muncul banner edukasi bahwa pendaftaran tim **cukup dilakukan oleh 1 akun ketua/perwakilan**.
     - Tombol "Daftar Sekarang" pada kartu jurusan memperhitungkan kecukupan sisa kuota terhadap jumlah anggota rombongan tim.
     - Link CTA membawa query parameter: `?bulan={id}&jurusan={id}&tipe=kelompok&jumlah={count}`.
2. **Kondisi Saat Ini**:
   - Hanya memiliki 2 pertanyaan ("1. Kamu siapa?" dan "2. Mau mulai kapan?").
   - Link pendaftaran di kartu terpasang statis untuk mode mandiri (`&tipe=mandiri&jumlah=1`).
   - Halaman registrasi backend (`/register` pada `AuthController.php` & `register.blade.php`) sebenarnya sudah siap 100% menerima parameter `tipe` dan `jumlah` serta menampilkan badge peringatan ketua tim.

### B. Asumsi Kerja Logis
- Batas jumlah anggota kelompok adalah **2 hingga 4 orang** (sesuai regulasi SOP Magang Diskominfo Sleman yang sudah ada di sistem).
- Default mode adalah **Mandiri (1 Orang)** saat halaman pertama kali dimuat.
- Tata letak pertanyaan akan disusun menjadi 3 kolom rapi di layar desktop (atau 2 baris fleksibel) yang responsif untuk perangkat seluler.

---

## 3. Evaluasi Opsi Pendekatan Teknis

### Opsi A: Integrasi Pertanyaan Ke-3 Dinamis & Reaktif (Disetujui User)
- **Desain & Alur**:
  1. Menambahkan pertanyaan ke-3 di samping pertanyaan 1 & 2 pada grid pertanyaan:
     - **3. Magang sama siapa?**
     - Pill: `[ Sendiri (1 Orang) ]` `[ Bareng Tim / Kelompok ]`
  2. Saat tombol `[ Bareng Tim / Kelompok ]` aktif:
     - Tampil sub-pilihan jumlah anggota: `[ 2 Orang ]` `[ 3 Orang ]` `[ 4 Orang ]` dengan animasi fade-in halus.
     - Tampil kartu informasi mini berdesain rapi: *"Pendaftaran kelompok cukup diwakili oleh 1 akun ketua tim. Berkas rekan tim diunggah di dalam formulir."*
  3. **Reaktivitas Kartu Jurusan**:
     - JavaScript secara dinamis memperbarui link pendaftaran di seluruh kartu jurusan:
       - Mandiri: `url/register?bulan=X&jurusan=Y&tipe=mandiri&jumlah=1`
       - Kelompok: `url/register?bulan=X&jurusan=Y&tipe=kelompok&jumlah={count}`
     - Validasi Kuota Tim: Jika sisa kuota di suatu jurusan lebih kecil dari jumlah anggota tim (misal sisa 2, tetapi dipilih 3 orang), tombol pendaftaran mengarahkan ke Waiting List tim dengan label jelas: `Daftar Waiting List (X Orang) ➔`.
- **Kelebihan**: Sangat informatif, mencegah calon peserta salah paham membuat multi-akun, dan sepenuhnya kompatibel dengan alur registrasi yang sudah ada.
- **Kekurangan**: Memerlukan penyesuaian CSS grid dan sedikit penambahan fungsi di JavaScript.

### Opsi B: Pilihan Tipe Hanya Muncul di Dalam Kartu Jurusan (Modal / Dropdown)
- **Desain**: Pilihan mandiri atau kelompok baru ditanyakan ketika user menekan tombol "Daftar Sekarang" pada kartu jurusan melalui pop-up modal.
- **Kekurangan**: Menambah gesekan (*click friction*) bagi user, dan user tidak dapat langsung melihat apakah suatu jurusan memiliki kuota yang cukup untuk kelompok sebelum mengklik kartu.
- **Kesimpulan Rekomendasi**: Dipilih **Opsi A** karena lebih transparan, cepat, dan sesuai dengan pengalaman pengguna sebelumnya.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/landing.blade.php` | Modify | Menambahkan pertanyaan ke-3 (Tipe Pendaftaran: Mandiri vs Kelompok), tombol pilihan jumlah anggota kelompok, dan banner edukasi ketua tim. Menambahkan atribut data (`data-sisa`, `data-is-waiting`, dll.) pada tombol CTA kartu jurusan agar siap dimutasi secara dinamis oleh JS. |
| `public/css/landing-modern.css` | Modify | Menambahkan styling untuk `.kuota-questions-grid` 3 kolom responsif, styling pill kelompok/mandiri, selector jumlah anggota tim, serta banner info ketua kelompok. |
| `public/js/landing-interactive.js` | Modify | Menambahkan state `currentRegMode` ('mandiri'/'kelompok') dan `currentMemberCount` (1-4). Memperbarui fungsi kalkulasi kartu agar URL CTA dan label tombol terupdate secara instan saat user berganti mode/jumlah. |

---

## 5. Langkah-Langkah Implementasi Bertahap

- [ ] **Langkah 1**: Perbarui `resources/views/public/landing.blade.php`:
  - Tambahkan blok pertanyaan ke-3 `kuotaRegModeGroup` (Mandiri vs Kelompok) di dalam `.kuota-questions-grid`.
  - Tambahkan container pemilih jumlah anggota (`#kuotaMemberCountWrapper`) dengan pill `2 Orang`, `3 Orang`, `4 Orang`.
  - Tambahkan banner informasi edukasi `#kuotaGroupEduBanner` di bawah grid pertanyaan atau di atas carousel.
  - Berikan identitas/atribut pada tombol kartu jurusan (`.btn-card-action`) agar mudah diupdate via JavaScript.
- [ ] **Langkah 2**: Perbarui `public/css/landing-modern.css`:
  - Sesuaikan `.kuota-questions-grid` agar mendukung 3 kelompok pertanyaan di layar desktop (`grid-template-columns: repeat(auto-fit, minmax(260px, 1fr))`).
  - Tambahkan style elegan untuk pill jumlah anggota dan card edukasi kelompok.
- [ ] **Langkah 3**: Perbarui `public/js/landing-interactive.js`:
  - Tambahkan event listener untuk pill tipe pendaftaran dan pill jumlah anggota tim.
  - Implementasikan logika pembaruan URL tombol kartu jurusan (`updateCardRegistrationLinks()`).
  - Implementasikan pengecekan sisa kuota terhadap ukuran tim (kuota cukup vs kurang).
- [ ] **Langkah 4**: Uji coba & verifikasi:
  - Bersihkan view cache (`php artisan view:clear`).
  - Verifikasi di browser: klik Mandiri -> periksa link; klik Kelompok -> pilih 3 orang -> periksa link membawa `&tipe=kelompok&jumlah=3`.
  - Pastikan tampilan mobile dan desktop tetap proporsional dan responsif.

---

## 6. Strategi Testing

1. **Uji Fungsionalitas Filter**:
   - Mode Mandiri (Default): Tombol kartu jurusan berlink `&tipe=mandiri&jumlah=1`.
   - Beralih ke Kelompok: Sub-pilihan jumlah anggota (2, 3, 4) muncul; banner edukasi muncul; link berubah menjadi `&tipe=kelompok&jumlah=2`.
   - Memilih 3 atau 4 orang: Link pada kartu berubah sesuai angka yang dipilih.
2. **Uji Kondisi Kuota**:
   - Kasus Kuota Cukup: Jika sisa kuota (misal 5) >= jumlah anggota (3), tombol bertuliskan *"Daftar Kelompok (3 Orang) ➔"*.
   - Kasus Kuota Tidak Cukup: Jika sisa kuota (misal 1 atau 2) < jumlah anggota (3), tombol otomatis beralih menjadi *"Daftar Waiting List (3 Orang) ➔"* dengan keterangan kuota sisa tidak mencukupi untuk rombongan.
3. **Uji Integrasi ke Halaman Register**:
   - Klik tombol pada kartu jurusan dan pastikan tiba di halaman `/register` dengan ringkasan jurusan, bulan, dan badge "Kelompok (X Orang)" / "Mandiri (1 Orang)" terpilih secara akurat.

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Mitigasi |
| :--- | :--- |
| Tampilan pertanyaan menjadi terlalu padat pada layar ponsel | Gunakan layout wrap fleksibel (`flex-wrap: wrap`) dan grid auto-fit dengan spacing yang proporsional. |
| URL pendaftaran salah saat pergantian jenjang/bulan | Satukan fungsi pembaruan link dalam satu fungsi terpusat (`updateCardLinks()`) yang membaca state aktif jenjang, bulan, tipe, dan jumlah. |

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Pertanyaan "3. Tipe pendaftaran?" tampil harmonis di samping pertanyaan jenjang dan bulan.
- [ ] Pilihan jumlah anggota (2, 3, 4 orang) berfungsi mulus dengan transisi elegan saat mode kelompok dipilih.
- [ ] Banner panduan singkat bahwa cukup 1 akun perwakilan kelompok tampil jelas.
- [ ] URL pada tombol pendaftaran dinamis membawa nilai `tipe` dan `jumlah` yang valid ke halaman registrasi.
- [ ] Tombol kartu jurusan secara otomatis memperhitungkan kecukupan kuota rombongan tim.
- [ ] Tidak ada breaking changes pada fitur Hero slider, database matriks kuota, maupun footer Wi-Fi.
