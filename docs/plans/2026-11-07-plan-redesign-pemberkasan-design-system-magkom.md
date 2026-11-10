# Rencana Kerja: Redesign Halaman Pemberkasan Pendaftaran (Design System MagKom)

**Tanggal**: 7 November 2026  
**Status**: Usulan Desain / Menunggu Persetujuan Pengguna  
**Konteks**: Memperbarui tampilan antarmuka halaman detail dan pemberkasan pendaftaran peserta magang (`/peserta/pendaftaran/{pendaftaran}`) agar memiliki visual yang modern, profesional, tidak kaku, dan 100% konsisten dengan Design System MagKom serta palet warna resmi Dinas Kominfo Sleman.

---

## 1. Analisis & Masalah Tampilan Saat Ini

| Bagian | Kondisi Saat Ini | Masalah Visual & UX |
|---|---|---|
| **Header & Stepper** | Stepper 3 tahap dengan lingkaran kaku dan font tipis; banner pendaftaran flat | Terasa seperti wireframe/prototipe lama, belum menggunakan ritme visual Design System MagKom. |
| **Kontainer Pemberkasan** | Memakai container x-panel abu-abu standar yang terpisah-pisah | Kurang menyatu dengan bahasa visual modern halaman dashboard dan profil peserta (`profil-main-card`). |
| **Item Dokumen** | Baris dokumen dengan teks dan tombol yang monoton | Tidak ada visual hierarchy yang kuat antara ikon dokumen, nama berkas, status verifikasi, dan tombol aksi. |
| **Bagian Kelompok (Team)** | Kartu personil ketua & anggota berupa box putih polos dengan garis abu-abu tipis | Kurang sentuhan "Team Identity" (avatar personil, role chip, dan progress pill yang elegan). |
| **Bottom Action Bar** | Box konfirmasi pengajuan di bawah masih statis dan terpisah | Kurang tegas sebagai penanda status kelengkapan berkas dan tombol CTA utama pengajuan pendaftaran. |

---

## 2. Referensi Desain Modern & Fondasi Visual

Mengadopsi pola **Modern Verification & Onboarding Portal** (Stripe Identity, GovUK, Linear KYC):
1. **Visual Hierarchy yang Tegas**:
   - Status berkas tidak hanya berupa teks, melainkan kombinasi **Icon + Pill Badge + Warna Status Semantik**.
   - Dokumen belum diunggah menampilkan placeholder visual yang rapi dan elegan, bukan garis putus-putus kosong yang melar.
2. **Design Tokens & Palet Warna Resmi MagKom**:
   - **Primary Navy**: `#02275D` (Dominan header, tombol aksi primer, brand visual).
   - **Navy Medium / Hover**: `#01347C`, `#003566` (Hover state, borders, gradients).
   - **Accent Gold / Sleman Yellow**: `#FFCB05`, `#FFC300` (Aksen status rekomendasi, highlight, border aktif, bintang).
   - **Text & Dark**: `#212529` (Teks utama), `#64748B` / `#666666` (Teks sekunder / muted).
   - **Surface & Backgrounds**:
     - Surface Card: `#FFFFFF`
     - Page Background: `#F8FAFC` / `#F5F7FB`
     - Soft Tint Status: `#ECFDF5` (Sukses/Lengkap), `#FFFBEB` (Belum Lengkap), `#EFF6FF` (Info Tim).
3. **Typography & Radius**:
   - Font: **Open Sans** (Headings, buttons, labels) & **JetBrains Mono** (NIM/NISN, nomor registrasi).
   - Radius: 16px (Container utama), 12px (Card personil & card dokumen), 8px (Tombol aksi), 999px (Pill badges).

---

## 3. Rincian Konsep Perubahan Komponen

### A. Modern Hero Header & Progress Overview
- Banner utama bernuansa **MagKom Navy Gradient** (`linear-gradient(135deg, #02275D 0%, #01347C 100%)`).
- **Sisi Kiri**:
  - Badge nomor registrasi dengan border gold halus: `REG-XXXXX`.
  - Judul halaman yang berbobot: *"Berkas & Persyaratan Pendaftaran Periode [Bulan Tahun]"*.
  - Metadata chips transparan: Bidang penempatan, tipe pendaftaran (Mandiri/Kelompok), dan tanggal pengajuan.
- **Sisi Kanan (Kelengkapan Dokumen Widget)**:
  - Progress card terintegrasi dengan persentase besar yang dinamis (`75% Lengkap`) dan mini progress meter beraksen hijau zamrud atau emas.

### B. Stepper Timeline Modern (Connected Stepper Pills)
- Mengubah stepper lingkaran lama menjadi **Horizontal Stepper Bar yang Halus**:
  - Step 1: *Pengisian & Unggah Berkas* (Active/Complete).
  - Step 2: *Verifikasi oleh Admin Kominfo* (Upcoming/Review).
  - Step 3: *Keputusan Penempatan & SK* (Final).
  - Garis penghubung otomatis berwarna biru saat selesai, dengan ikon checklist yang jelas.

### C. Redesain Item Dokumen (`_doc_card_compact.blade.php`)
Setiap dokumen dibuat dengan layout kartu interaktif yang sangat rapi:
1. **Leading Icon Box (44x44px)**:
   - Ikon dokumen spesifik sesuai jenis berkas (Surat Pengantar, Proposal, CV, Transkrip) dengan latar soft tint (`#EFF6FF`).
2. **Detail & Meta Dokumen**:
   - Judul dokumen tebal dan kontras (`#212529`).
   - Badge `Wajib` (merah muda anggun) atau `Opsional` (abu-abu netral).
   - Keterangan format dan kapasitas maksimal (PDF, JPG • Maks 2 MB).
3. **Interactive File State**:
   - **Kondisi Belum Diunggah**:
     - Status pill kuning lembut: `● Belum Diunggah`.
     - Tombol aksi: `[ + Pilih Berkas ]` dengan styling outline Navy modern (`border: 1.5px solid #01347C; color: #01347C; background: #FFFFFF; font-weight: 700; border-radius: 8px`). Hover bertransisi ke Navy penuh dengan bayangan halus.
   - **Kondisi Berkas Terunggah**:
     - Kartu mendapat border hijau lembut (`#A7F3D0`) dan background tint halus.
     - Chip berkas terunggah: Ikon dokumen PDF/Gambar, nama file asli (truncated dengan tooltip), ukuran dalam KB, dan tanggal unggah.
     - Action buttons: `[Lihat]` (Eye), `[Unduh]` (Download), `[Ganti]` (Replace).
   - **State Pemilihan File (File Selected)**:
     - Konfirmasi instan sebelum unggah: preview nama berkas, tombol `Batal`, dan tombol `Unggah Sekarang` (`#02275D` dengan teks putih dan efek spin saat loading).

### D. Redesain Section Kelompok (Team Roster Bento Cards)
Khusus pendaftaran kelompok:
1. **Info Banner Tim Terpadu**:
   - Header biru elegan yang menegaskan pembagian berkas: Berkas Bersama Tim vs Berkas Personal Anggota.
2. **Blok Berkas Bersama Tim**:
   - Berisi Surat Pengantar & Proposal Magang dalam kartu berseri yang rapi.
3. **Blok Roster Personil Tim**:
   - Setiap personil (Ketua dan tiap Anggota) ditampilkan dalam **Member Profile Bento Card**:
     - **Header Personil**: Avatar inisial dengan badge gold untuk Ketua (`Ketua Tim (Anda)`) dan badge slate untuk Anggota. Menampilkan nama, NIM/NISN, serta status badge kelengkapan di sisi kanan: `✓ 2/2 Berkas Siap` (Hijau) atau `Belum Lengkap` (Amber).
     - **Body Personil**: Menampilkan 2 berkas wajib masing-masing (CV & Transkrip) dengan rapi, berjarak pas, dan mudah diakses.

### E. Action Bar Bawah (Submission Confirmation Bar)
- Mengubah box bawah menjadi **Sticky / Floating Ready Bar**:
  - Jika berkas belum lengkap: Menampilkan teks panduan dan tombol `Ajukan Pendaftaran` disabled dengan tooltip berkas yang belum diisi.
  - Jika seluruh berkas wajib lengkap: Berubah menjadi warna sukses (`#ECFDF5`), menampilkan checklist centang hijau tebal, dan tombol `Ajukan Pendaftaran Sekarang` berwarna Navy `#02275D` dengan hover aksen emas `#FFCB05`.

---

## 4. Rincian File yang Akan Dimodifikasi

1. **`resources/css/pages/peserta/pemberkasan.css`** (File CSS khusus halaman pemberkasan yang scoped, rapi, dan terstruktur sesuai AGENTS.md).
2. **`resources/views/peserta/pendaftaran/detail.blade.php`** (Restrukturisasi markup semantik hero, stepper, team roster, dan submission card).
3. **`resources/views/peserta/pendaftaran/partials/_doc_card_compact.blade.php`** (Restrukturisasi item kartu dokumen dengan leading icon, status pill, dan tombol aksi proporsional).
4. **`vite.config.js`** & **`resources/css/app.css`** (Pendaftaran entry point asset CSS baru bila diperlukan agar terkompilasi optimal).

---

## 5. Rencana Pengujian & Quality Gate

1. **Uji Tampilan & Desain Konsistensi**:
   - Memastikan seluruh warna sesuai dengan palet identitas MagKom (`#02275D`, `#01347C`, `#003566`, `#FFCB05`, `#212529`).
   - Memastikan tidak ada lagi dropzone yang melar kosong atau layout yang kaku.
   - Menguji tampilan pada desktop, tablet, dan smartphone (responsif dan mobile-friendly).
2. **Uji Interaksi & Fitur**:
   - Verifikasi unggah berkas (PDF, JPG, PNG) untuk pendaftaran mandiri dan kelompok.
   - Verifikasi pratinjau file in-app modal dan unduh berkas bertanda tangan URL privat.
   - Verifikasi tombol ganti berkas dan reset form.
   - Verifikasi validasi kelengkapan berkas sebelum finalisasi pengajuan.
3. **Automated Verification**:
   - `php artisan test --filter=PendaftaranPesertaTest` (Pastikan seluruh 6 skenario hijau).
   - `./vendor/bin/pint --test` (Pastikan kepatuhan PSR-12 & Laravel Pint).
   - `npm run build` (Kompilasi asset Vite tanpa peringatan/error).

