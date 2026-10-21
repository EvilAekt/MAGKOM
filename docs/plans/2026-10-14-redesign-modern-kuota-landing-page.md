# Plan: Redesign Total UI/UX Pemilihan Bidang & Kuota Landing Page (User-Friendly & Ergonomis)

- **Tanggal**: 2026-10-14
- **Status**: Draft (Menunggu Persetujuan)
- **Author**: Antigravity (web-planning + ui-ux-pro-max + impeccable)

---

## 1. Tujuan
Meredesain menyeluruh (*total redesign*) seksi **Bidang dan Kuota** pada landing page (`resources/views/public/landing.blade.php`) untuk mengatasi keluhan *"engga user friendly dan ga enak di mata"*. Menghadirkan antarmuka yang bersih, tenang, modern, dan sangat nyaman dipandang (*low cognitive load, high legibility*), menghentikan animasi konstan yang membuat mata lelah, menyederhanakan kontrol pemilih Mandiri vs Kelompok, serta mempercantik kartu kuota dengan gaya **Modern Bento Cards** yang selaras dengan Design System resmi MagKom Sleman.

---

## 2. Analisis Masalah UX Saat Ini (*Pain Points*)
Berdasarkan audit antarmuka dan prinsip desain `ui-ux-pro-max`:
1. **Cognitive Fatigue dari Animasi Konstan (*Marquee Loop*)**:
   - Kartu yang terus bergerak dari kiri ke kanan secara konstan menyulitkan mata membaca teks jurusan, angka sisa kuota, dan mengklik tombol CTA. Pengguna harus "mengejar" kartu yang bergeser.
2. **Control Bar yang Berat & Kaku (*Visual Clutter*)**:
   - Bilah kontrol atas memiliki latar abu-abu tebal dengan terlalu banyak teks label formal (`JALUR PENDIDIKAN:`, `PENDAFTARAN:`), border tebal, serta badge keterangan panjang di bawahnya yang membuat area terasa penuh sesak.
3. **Hierarki Kartu "Box-in-a-box"**:
   - Di dalam kartu putih terdapat kotak abu-abu lagi (`.card-capacity-box`), di dalamnya ada pill status, progress bar tebal, dan teks bersusun yang membuat kartu terasa sempit dan kaku.
4. **Warna Status yang Kurang Selaras**:
   - Warna amber/kuning pada waiting list terlalu dominan sehingga terkesan seperti peringatan kesalahan (error/warning) daripada informasi antrean yang informatif dan ramah.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi 2 Pendekatan Layout:

| Aspek | Opsi A (Direkomendasikan): Responsive Bento Grid + Clean Segmented Bar | Opsi B: Gentle Static Snap Carousel (Non-Auto-Scrolling) |
| :--- | :--- | :--- |
| **Kenyamanan Mata** | **Sangat Nyaman (10/10)**: Seluruh informasi diam stabil, bebas pusing, mudah discan | **Cukup Nyaman (7/10)**: Statis saat diam, hanya bergerak saat tombol diklik |
| **Kecepatan Memilih** | **Sangat Cepat**: Calon peserta langsung melihat semua jurusan dalam satu pandangan mata tanpa perlu menggeser | **Sedang**: Harus mengklik panah untuk melihat jurusan di luar layar |
| **Responsivitas** | Otomatis beradaptasi sempurna: 3 kolom (desktop), 2 kolom (tablet), 1 kolom (mobile) | Memerlukan handling lebar kartu dan touch drag horizontal |
| **Kerapian Kontrol** | Segmented Control bar tunggal yang ramping (iOS / Linear style), tanpa border tebal | Kontrol bar terpisah dengan tombol navigasi slider |

### Keputusan: **Opsi A (Responsive Bento Grid + Clean Segmented Bar)**
*(Dengan opsi navigasi tab bulan yang halus dan mikro-interaksi hover yang menyenangkan).*

---

## 4. Konsep Desain Baru (The New Visual World)

### 4.1 Header & Control Bar yang Ramping (*Floating Segmented Filter*)
- **Tanpa Kotak Abu-Abu Raksasa**: Kontrol dipusatkan dengan desain floating pill yang ringan di atas latar putih bersih.
- **Tingkat 1: Tab Jalur Pendidikan**:
  - Segmented pill elegan: `Jalur SMK` dan `Jalur Mahasiswa (D3 / S1)`.
  - Background aktif biru lembut dengan teks tajam dan transisi halus.
- **Tingkat 2: Pemilih Mode Pendaftaran**:
  - Switch interaktif: `Mandiri (1 Orang)` vs `Kelompok / Tim`.
  - Jika `Kelompok` aktif, muncul sub-pills jumlah anggota (`2 Orang`, `3 Orang`, `4 Orang`) dengan animasi expand yang halus (*smooth micro-animation*).
- **Indikator Kuota Minimalis**: Teks kecil bersih berikon di tengah: `Memeriksa ketersediaan kuota untuk 3 calon peserta` tanpa banner besar yang memakan tempat.

### 4.2 Kartu Kuota Modern (Bento-Style Cards)
- **Permukaan Kartu**:
  - Putih bersih (`#FFFFFF`) dengan border halus (`#E2E8F0`), sudut melengkung modern (`border-radius: 16px`), dan bayangan lembut (*soft ambient shadow* `0 4px 20px -2px rgba(15, 23, 42, 0.05)`).
  - Hover effect: Elevasi halus naik 4px dengan glow border biru lembut (`transform: translateY(-4px)`).
- **Badge Bidang & Jenjang Terintegrasi**:
  - Pojok kiri atas: Badge jenjang minimalis (`SMK` atau `D3/S1`) berdampingan dengan chip bidang Kominfo (`IKP`, `E-Gov`, `ITI`, `Sekretariat`) dengan palet warna resmi yang teduh dan elegan.
- **Judul & Deskripsi**:
  - Judul jurusan berukuran 18px SemiBold, kontras tinggi, dengan deskripsi ringkas 2 baris (13px neutral-600).
- **Visual Capacity Gauge Ramping**:
  - Progress bar kapasitas tipis (tinggi 6px) dengan sudut rounded full yang bersih.
  - Hijau emerald untuk kuota melimpah, biru info untuk sisa wajar, amber lembut untuk kuota menipis/waiting list.
  - Teks kapasitas di bawahnya: `Sisa X dari Y kursi` (kiri) dan `Z% Terisi` (kanan) dengan font 13px yang mudah dibaca.
- **Tombol CTA Tunggal yang Mantap**:
  - Tinggi 44px (sesuai standar WCAG touch target).
  - Tombol primer biru royal dengan panah elegan `Daftar Sekarang ➔` atau `Daftar Kelompok (X Orang) ➔`.
  - Tombol waiting list dengan nuansa biru info / soft slate berikon jam `Daftar Waiting List` yang ramah di mata.

---

## 5. Rincian Perubahan Berkas
| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/public/landing.blade.php`](file:///g:/MAGKOM/resources/views/public/landing.blade.php) | Modify | Redesign CSS dan HTML seksi `#kuota-section` (Bento Grid layout, modern segmented filter, visual gauge ramping, tombol CTA elegan, tanpa auto-scroll terus menerus) |
| [`tests/Feature/LandingPageTest.php`](file:///g:/MAGKOM/tests/Feature/LandingPageTest.php) | Verify | Memastikan seluruh 6 pengujian landing page dan 369 seluruh test suite tetap 100% lulus (PASS) |

---

## 6. Langkah Implementasi
- [x] **Langkah 1**: Rancang CSS baru untuk Bento Grid, Segmented Control, dan Card Surface di blok `<style>` seksi kuota pada `landing.blade.php`.
- [x] **Langkah 2**: Perbarui markup HTML Section 2 (`#kuota-section`):
  - Ganti container carousel yang berjalan sendiri dengan **Responsive Bento Grid** (3 kolom di desktop, 2 kolom di tablet, 1 kolom di ponsel).
  - Ganti control bar tebal dengan **Segmented Filter Bar** yang ramping dan bersih.
  - Pasang kartu kuota baru dengan tata letak informasi yang lapang (*spacious*), typography yang jelas, dan visual capacity bar yang ramping.
- [x] **Langkah 3**: Perbarui JavaScript di `@push('scripts')`:
  - Hapus loop scroll konstan (`requestAnimationFrame` scrollLeft) yang membuat mata pusing.
  - Sambungkan segmented control Mandiri vs Kelompok (1, 2, 3, 4 orang) ke fungsi kalkulasi real-time yang langsung memperbarui badge dan tombol kartu secara instan.
  - Pastikan seluruh fungsi tetap terikat ke `window` (`window.setRegistrationMode`, `window.setMemberCount`, `window.switchTab`, `window.switchCardMonth`).
- [x] **Langkah 4**: Bersihkan cache Blade (`php artisan view:clear`).
- [x] **Langkah 5**: Inspeksi visual menggunakan browser subagent (screenshot desktop dan mobile, cek kenyamanan mata, cek responsivitas).
- [x] **Langkah 6**: Jalankan seluruh automated test suite (`php artisan test`) untuk memastikan 369 test 100% hijau.
- [x] **Langkah 7**: Jalankan `graphify update .`.

---

## 7. Strategi Pengujian (Quality Gates)
- **Pengujian Fungsional & Unit**:
  - `php artisan test --filter=LandingPageTest` (6 test lulus).
  - `php artisan test` (seluruh 369 test lulus, 0 failed).
- **Pengujian Visual & Ergonomi UX**:
  - Tampilan diam stabil tanpa gerakan konstan yang mengganggu pembacaan.
  - Keterbacaan teks jurusan dan kuota sangat jelas pada layar desktop maupun mobile.
  - Pemilihan jumlah anggota kelompok (1, 2, 3, 4 orang) langsung memperbarui status seluruh kartu secara instan.
  - Console browser bersih dari error (0 error).

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Seluruh langkah implementasi tercentang `- [x]`.
- [x] Tampilan seksi kuota bersih, lapang, estetik, dan nyaman di mata (*eye-friendly*).
- [x] Tidak ada animasi terus-menerus yang melelahkan mata.
- [x] Kontrol Mandiri vs Kelompok (1, 2, 3, 4 orang) berfungsi mulus dan intuitif.
- [x] Seluruh 369 pengujian otomatis lulus 100%.

---

## 9. Catatan Perubahan & Saran Lanjutan
- **Perubahan selama pengerjaan**: *(Akan diperbarui saat eksekusi)*
- **Saran Lanjutan (Out of Scope)**:
  - Fitur pencarian instan jurusan (*quick search filter*) jika jumlah jurusan bertambah lebih dari 15.
