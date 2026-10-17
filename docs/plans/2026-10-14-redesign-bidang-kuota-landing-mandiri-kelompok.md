# Rencana Kerja: Redesign Kartu Bidang & Kuota Landing Page + Fitur Pendaftaran Mandiri & Kelompok (1–4 Orang)

**Tanggal:** 14 Oktober 2026  
**Status Dokumen:** Siap Review / Menunggu Persetujuan Pengguna  
**Referensi Standar:** `.agents/skills/ui-ux-pro-max`, `.agents/skills/impeccable`, Design System MagKom  
**Terkait:** Redesain UI/UX Bagian "Bidang dan kuota" di Halaman Depan (`resources/views/public/landing.blade.php`) & Alur Registrasi  

---

## 1. Ringkasan Eksekutif & Masalah Saat Ini

Pengguna memberikan masukan penting terhadap antarmuka bagian **"Bidang dan kuota"**:
1. **Tampilan Kartu Saat Ini Dinilai Kurang Menarik ("Masih Jelek")**:
   - Di dalam setiap kartu terdapat pengulangan blok per-bulan magang yang kaku (`Mulai: November 2026`, `Mulai: Desember 2026`) dengan teks kuota panjang dan tombol ganda di dalam satu kartu.
   - Desain kartu terkesan seperti tabel formulir administratif biasa, bukan showcase landing page modern berkelas dinas komunikasi dan informatika.
   - Rasio kartu tidak proporsional dan visual hierarchy terasa datar.
2. **Kebutuhan Fitur Baru: Pilihan Mandiri vs Kelompok (1, 2, 3, 4 Orang)**:
   - Calon peserta magang seringkali mendaftar **sendiri (Mandiri - 1 orang)** atau **bersama tim/rekan sekolah/kampus (Kelompok - 2, 3, atau 4 orang)**.
   - Pengguna menginginkan pemilih interaktif (1, 2, 3, 4 orang) di landing page sebelum registrasi, yang langsung mempengaruhi ketersediaan kuota dan meneruskan preferensi tersebut ke formulir pendaftaran akun.

---

## 2. Solusi Desain UI/UX Baru (Berdasarkan Panduan Impeccable & UI-UX-Pro-Max)

### 2.1 Control Bar: Tipe Pendaftaran (Mandiri vs Kelompok) & Anggota
Di atas carousel kartu kuota, tepat di samping/bawah tab jenjang (SMK vs Mahasiswa), kita hadirkan **Control Bar Interaktif**:
- **Pill Switcher Tipe**:
  - `[ 👤 Mandiri (1 Orang) ]`
  - `[ 👥 Kelompok / Tim ]`
- **Pill Selektor Jumlah Anggota** (tampil dengan transisi lembut saat mode kelompok dipilih):
  - `[ 2 Orang ]` `[ 3 Orang ]` `[ 4 Orang ]`
- **Real-Time Dynamic Quota Feedback**:
  - Setiap kartu kuota secara otomatis mengkalkulasi ketersediaan:
    - Jika sisa kuota jurusan $\ge$ jumlah anggota yang dipilih $\rightarrow$ Status: **🟢 Kuota Tersedia ({sisa} Kursi)**. Tombol aksi: `Daftar Kelompok (X Orang)`.
    - Jika sisa kuota $< X$ orang $\rightarrow$ Status: **🟡 Masuk Waiting List**. Kartu menampilkan catatan ramah: *"Sisa {sisa} kursi, pendaftaran {X} orang masuk antrean tunggu"*. Tombol aksi: `Daftar Waiting List (X Orang)`.

---

### 2.2 Anatomi Redesign Kartu Kuota (Premium Card Surface)
Setiap kartu dirombak total menjadi kartu modern, bersih, proporsional, dan berkilau:

```
┌────────────────────────────────────────────────────────┐
│ [SMK / D3·S1]                         [IKP] [Sekretariat]│ <- Chips Header
│                                                        │
│  Teknik Komputer dan Jaringan                          │ <- Judul Bold 700
│  Pengelolaan infrastruktur jaringan & server dinas.   │ <- Deskripsi Ringkas
│                                                        │
│ ┌────────────────────────────────────────────────────┐ │
│ │ PERIODE TERDEKAT: NOVEMBER 2026                   │ │
│ │                                                    │ │
│ │ Sisa Kuota: 3 dari 10 Kursi       [🟢 Tersedia]   │ │
│ │ [████████████████████░░░░░░░░░] 70% Terisi        │ │ <- Visual Capacity Bar
│ └────────────────────────────────────────────────────┘ │
│                                                        │
│ [ ⚡ Daftar Kelompok (3 Orang) ➜                     ] │ <- Single Bold CTA Button
└────────────────────────────────────────────────────────┘
```

1. **Card Top Header**:
   - Badge Jenjang (`SMK` atau `D3 · S1`) dengan style pill netral elegan.
   - Badge Unit Penempatan (`[IKP]`, `[EGOV]`, `[ITI]`, `[Sekretariat]`) dengan styling chip kontras lembut dan tooltip nama bidang lengkap.
2. **Typography Hierarchy**:
   - Judul Program Studi: `font-size: 1.15rem`, `font-weight: 700`, warna `var(--neutral-900)`, line-height rapat dan tegas.
   - Subjudul: Keterangan ringkas kompetensi yang relevan dengan tugas di Diskominfo Sleman.
3. **Visual Kuota Tracker (Highlight Periode Aktif)**:
   - Menyorot **Periode Pendaftaran Aktif Terdekat** dengan visual yang kuat.
   - Terdapat **Kapasitas Kuota Bar** (progress bar persentase keterisian) yang memberikan kejelasan visual langsung kepada calon pendaftar.
   - Jika terdapat periode berikutnya (misal Desember), ditampilkan dalam mini-badge subtil: `Periode berikutnya: Des 2026`.
4. **Single Actionable CTA Button**:
   - Satu tombol aksi yang besar, jelas, dan memikat di bagian dasar kartu.
   - Menghilangkan tombol ganda yang membingungkan.
   - Label tombol dinamis mengikuti seleksi:
     - `Daftar Mandiri (1 Orang)`
     - `Daftar Kelompok (X Orang)`
     - `Daftar Waiting List (X Orang)`
5. **Micro-Interactions & Elevasi**:
   - Kartu menggunakan background putih murni dengan border halus `rgba(226, 232, 240, 0.9)`.
   - Hover state: Kartu melayang ke atas (`translateY(-6px)`), bayangan lembut membesar (`box-shadow: 0 20px 35px -10px rgba(7, 81, 255, 0.12)`), dan border bersinar biru lembut.

---

### 2.3 Horizontal Carousel Experience (Penyempurnaan Gerakan)
- Lebar kartu terstandar: `360px` di desktop, `85vw` di mobile.
- Tombol navigasi panah kiri (`‹`) dan kanan (`›`) dipercantik dengan floating glassmorphism, background putih blur, dan hover animasi membesar.
- Animasi gerak otomatis (*continuous smooth motion*) tetap berjalan lembut saat idle, dan langsung berhenti sejenak saat kursor mouse menyentuh kartu (*smart pause on hover*).

---

## 3. Integrasi Alur Data ke Halaman Pendaftaran (Registration Flow)

1. **URL Parameter Passing**:
   Saat calon pendaftar menekan tombol CTA pada kartu, sistem mengarahkan ke:
   ```
   /register?bulan={bulan_id}&jurusan={jurusan_id}&tipe={mandiri|kelompok}&jumlah={1|2|3|4}
   ```
2. **Halaman Register (`resources/views/auth/register.blade.php`)**:
   - Membaca parameter `tipe` dan `jumlah` dari query string.
   - Menampilkan **Pemberitahuan Pilihan Magang (Infobox Ringkas)** di bagian atas form registrasi:
     *Contoh:* `"Pilihan: Jalur Mahasiswa · Informatika · Kelompok (3 Orang) · Periode November 2026"`.
   - Menyimpan preferensi pendaftaran ke sesi pendaftar agar saat akun selesai dibuat, calon peserta langsung diarahkan ke form pengisian anggota/biodata yang sesuai.

---

## 4. Rincian Berkas yang Akan Diubah

| Berkas | Perubahan |
|---|---|
| [`resources/views/public/landing.blade.php`](file:///g:/MAGKOM/resources/views/public/landing.blade.php) | • Tambah kontrol filter Tipe Pendaftaran (Mandiri vs Kelompok) dan Pemilih Jumlah Anggota (1, 2, 3, 4).<br>• Redesain total kartu `.figma-quota-card` dengan kapasitas bar visual, chip bidang modern, dan single CTA.<br>• Update JavaScript dynamic recalculation & URL generator. |
| [`resources/views/auth/register.blade.php`](file:///g:/MAGKOM/resources/views/auth/register.blade.php) | • Menampilkan badge/infobox konfirmasi pilihan jurusan, periode, dan jumlah anggota kelompok dari landing page. |

---

## 5. Rencana Pengujian (Quality Assurance)

1. **Visual & Usability Testing**:
   - Menguji interaktivitas pemilih Mandiri vs Kelompok (klik 1, 2, 3, 4 orang) dan memastikan status kartu kuota berubah secara instan.
   - Menguji pergeseran carousel manual via tombol panah dan kehalusan autoplay.
   - Memeriksa tampilan di layar desktop (1920px & 1366px), tablet (768px), dan smartphone (375px).
2. **Automated Test Suite**:
   - Menjalankan `php artisan test` untuk memastikan 369 automated tests tetap **100% PASS**.
3. **Knowledge Graph**:
   - Menjalankan `graphify update .` pasca modifikasi.

---

## 6. Kriteria Selesai (Definition of Done)

- [ ] Control bar di landing page memiliki opsi **Mandiri (1 Orang)** dan **Kelompok (2, 3, 4 Orang)**.
- [ ] Kartu kuota bidang di-redesign menjadi modern, bersih, proporsional, dan memiliki visual progress kapasitas kuota.
- [ ] Tombol CTA pada kartu secara dinamis menampilkan label sesuai jumlah anggota dan status ketersediaan.
- [ ] Tombol CTA meneruskan parameter `tipe`, `jumlah`, `jurusan`, dan `bulan` ke halaman register.
- [ ] Halaman register menampilkan banner ringkasan pilihan magang calon peserta.
- [ ] 369 automated tests tetap 100% hijau (PASSED).
