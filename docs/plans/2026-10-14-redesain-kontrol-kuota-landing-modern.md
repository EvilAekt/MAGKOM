# Rencana Kerja: Redesain Bilah Kontrol & Filter "Bidang dan Kuota" Landing Page

- **Tanggal Dokumen**: 2026-10-14  
- **File Target**: `resources/views/public/landing.blade.php`  
- **Status**: Menunggu Konfirmasi Pengguna  

---

## 1. Tujuan
Mendesain ulang secara visual bilah kontrol filter pada bagian **Bidang dan Kuota** di Landing Page publik agar:
1. **Menghilangkan Kesan Kaku & "Kotak di dalam Kotak"**: Menghilangkan border bertingkat (*nested boxes*) dan garis putus-putus (*dashed line*) yang terlihat seperti wireframe/form jadul.
2. **Simetri & Harmoni Proporsional**: Mengatur letak kontrol Jalur Pendidikan, Tipe Pendaftaran, dan Jumlah Tim agar simetris sempurna, seimbang, dan mengalir natural (*fluid*).
3. **Visual Menarik & Bernilai Premium (WOW Effect)**: Menggunakan estetika modern dengan latar gradien halus (*subtle elevation*), segmented pill yang hidup (*vibrant interactive toggles*), iconografi kontekstual, dan transisi animasi mikro yang mulus.
4. **Integrasi Edukasi Tim yang Elegan**: Menyatukan notifikasi "1 Akun Perwakilan Kelompok" secara kontekstual di dalam hub tanpa merusak ritme visual halaman.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi Teknis**:
  - Semua ID dan fungsi JavaScript yang sudah berjalan (`switchTab`, `setRegistrationMode`, `setMemberCount`, `recalculateAllCards`, ID elemen) tetap dipertahankan 100% sehingga tidak ada logika kuota atau carousel yang terganggu.
  - Tampilan responsif di mobile (`max-width: 768px`) akan bertransformasi menjadi susunan stack vertikal yang rapi dan mudah dioperasikan dengan satu jempol (*thumb-friendly*).
- **Pertanyaan Terbuka**: Tidak ada blocker arsitektural.

---

## 3. Pendekatan Desain (Perbandingan Opsi)

### Opsi A: Tipe Dashboard Segmented Command Hub (Rekomendasi Utama)
- **Konsep**: Menghilangkan kartu-kartu kecil di dalam container. Menggantinya dengan satu panel kendali terpadu yang bersih (*single elevated command panel*).
  - **Sisi Kiri**: Filter Jenjang (SMK vs Mahasiswa) dalam pill segmented capsule horizontal dengan icon elegan.
  - **Sisi Kanan**: Filter Tipe Pendaftaran (Mandiri vs Kelompok) yang bila Kelompok dipilih, sub-pemilih anggota (2, 3, 4 orang) langsung mengalir di samping/bawahnya secara menyatu dalam satu baris fleksibel tanpa box putus-putus.
  - **Bilah Bawah**: Live availability radar badge ("🟢 Kuota otomatis dicek") bersanding simetris dengan petunjuk gestur geser kartu di kanan.
  - **Banner Kelompok**: Tersemat di bawah status bar dengan kartu beraksen gradien biru Sleman halus (*soft ambient blue*), bukan warna flat yang kontras tajam.
- **Kelebihan**: Sangat bersih, modern, tidak ada visual noise atau border berlebihan, 100% simetris di desktop maupun mobile.
- **Kekurangan**: Membutuhkan penyesuaian CSS flex/grid agar animasi ekspansi jumlah anggota tim tetap mulus.

### Opsi B: Floating Interactive Segmented Bar (Gaya Apple/macOS Toolbar)
- **Konsep**: Menghilangkan container kotak besar, hanya menyisakan dua kapsul segmented melayang (*floating pills*) di tengah layar secara terpusat (*centered alignment*).
- **Kelebihan**: Tampilan sangat minimalis.
- **Kekurangan**: Ruang untuk menampilkan info penting pendaftaran kelompok dan indikator kuota real-time menjadi terbatas, berisiko membingungkan calon peserta magang yang butuh kejelasan instruksi.

> **Pilihan Terpilih**: **Opsi A (Dashboard Segmented Command Hub)** karena menyatukan keindahan visual premium, kesimetrisan yang elegan, dan kemudahan navigasi bagi calon pendaftar.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/landing.blade.php` | Ubah | 1. Restrukturisasi CSS `.quota-controls-bar` dan sub-komponennya: hapus nested card, hapus garis dashed, ganti dengan elevated command surface.<br>2. Perbarui markup HTML bilah kontrol agar seimbang simetris.<br>3. Integrasikan selektor anggota tim (2, 3, 4) langsung ke baris toggle mode pendaftaran.<br>4. Perhalus tampilan kartu edukasi kelompok dengan gradien ambient lembut dan icon verified.<br>5. Pastikan dark mode matching sempurna. |

---

## 5. Rencana Langkah Implementasi

- [x] **Langkah 1**: Perbarui CSS pada `landing.blade.php`:
  - Rancang ulang `.quota-controls-bar` dengan latar `linear-gradient`, border lembut `rgba(226, 232, 240, 0.8)`, dan radius `1.5rem`.
  - Hapus class `.quota-filter-card` dan garis dashed `.quota-filter-subhint` yang membuat tampilan kaku.
  - Buat komponen `.filter-capsule-track` yang simetris untuk Jalur Pendidikan dan Tipe Pendaftaran dengan icon kontekstual.
  - Buat styling `.team-size-stepper` yang menyatu dinamis (*inline integrated chip*) saat mode kelompok dipilih.
- [x] **Langkah 2**: Perbarui Markup HTML Kontrol Filter:
  - Susun layout horizontal simetris:
    - Blok 1: **Jenjang Pendidikan** [🎓 SMK] [🏛️ Mahasiswa (D3/S1)]
    - Blok 2: **Format Pendaftaran** [👤 Mandiri (1 Orang)] [👥 Kelompok/Tim]
  - Pertahankan seluruh atribut `id` (`tab-btn-smk`, `tab-btn-mahasiswa`, `mode-btn-mandiri`, `mode-btn-kelompok`, `member-selector-wrap`) dan `onclick` handler.
- [x] **Langkah 3**: Integrasikan Live Status & Banner Edukasi:
  - Buat status bar bawah yang ramping dengan icon live pulse hijau/biru.
  - Modernisasi banner edukasi kelompok (`#banner-kelompok-edukasi`) agar serasi dengan command hub.
- [x] **Langkah 4**: Sinkronisasi JavaScript:
  - Pastikan event klik pada tombol jalur dan tipe pendaftaran memicu transisi visual halus tanpa flicker.
- [x] **Langkah 5**: Sinkronisasi Dark Mode:
  - Pastikan seluruh variabel warna, gradien gelap, dan kontras teks memenuhi WCAG 2.2 AA di mode gelap.
- [x] **Langkah 6**: Verifikasi & Pengujian:
  - Jalankan script verifikasi render HTML lokal.
  - Jalankan test suite PHPUnit untuk memastikan tidak ada fungsionalitas yang terganggu.

---

## 6. Strategi Testing
1. **Verifikasi Output Render**: Memeriksa tag dan struktur HTML via skrip lokal di `http://127.0.0.1:8000`.
2. **Automated Regression Test**: Menjalankan `php artisan test` untuk memastikan 368+ test tetap lulus.
3. **Interaktivitas Manual**: Menguji klik pergantian tab SMK vs Mahasiswa dan Mandiri vs Kelompok (termasuk tombol 2, 3, 4 orang).

---

## 7. Risiko & Mitigasi
- **Risiko**: Ukuran teks tombol di mobile terlalu panjang sehingga bertumpuk tidak teratur.
- **Mitigasi**: Menerapkan media query khusus mobile yang mengubah susunan menjadi 1 kolom dengan label kompak dan touch target nyaman (minimal 44px).

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Tidak ada lagi kotak di dalam kotak (*nested borders*) atau garis putus-putus (*dashed line*) yang kaku.
- [x] Layout simetris dan seimbang antara Jenjang Pendidikan dan Tipe Pendaftaran.
- [x] Pemilih anggota tim (2, 3, 4 orang) terintegrasi mulus saat mode Kelompok aktif tanpa membuat tinggi kartu jomplang.
- [x] Banner edukasi kelompok terpadu elegan dengan aksen visual modern.
- [x] Tampilan di mode terang dan mode gelap teruji rapi dan kontras.
- [x] Seluruh fungsi interaktif (perhitungan sisa kuota, carousel, tombol daftar) tetap berjalan normal.
