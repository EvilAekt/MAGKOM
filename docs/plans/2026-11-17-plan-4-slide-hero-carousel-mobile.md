# Rencana Kerja: Adaptasi Hero Banner Beranda Mobile Menjadi 4-Slide Carousel

Dokumen ini disusun untuk merencanakan transformasi bagian Hero Banner pada Beranda publik MAGKOM versi mobile ($\le 768\text{px}$) agar memiliki 4 slide interaktif sesuai arahan dan gambar referensi mockup yang diberikan pengguna.

---

## 1. Latar Belakang & Analisis Kebutuhan

### 1.1 Kondisi Saat Ini (Sebelum Perubahan)
- Pada tampilan desktop ($> 768\text{px}$), Hero Banner menampilkan kartu 3-kolom (`.hero-box`):
  - Kolom 1: Overview Pendaftaran, Judul Periode, Badge Tahun, Jadwal, dan Tombol "Daftar Sekarang".
  - Kolom 2: Persyaratan Umum.
  - Kolom 3: Dokumen Pendaftaran.
  - Slide 2: Foto Dokumentasi Resmi Diskominfo Sleman.
- Namun pada tampilan mobile ($\le 768\text{px}$), CSS menyembunyikan slide foto ke-2 dan menumpuk (*stacking*) seluruh 3 kolom tersebut secara vertikal ke bawah dalam satu kartu yang sangat panjang (sesuai gambar referensi 1: `media_1794886792488_dd0a4a93.png`), dengan tombol "Daftar Sekarang" berada di posisi paling bawah setelah daftar dokumen.

### 1.2 Target yang Dikehendaki Pengguna (Sesuai Referensi Gambar 2)
1. **Pemisahan Menjadi 4 Slide Carousel di Mobile**:
   - **Slide 1 (Fase Pendaftaran)**: Informasi periode magang, kabupaten, tahun, jadwal magang, dan **Tombol "Daftar Sekarang"** langsung berada di dalam slide ini.
   - **Slide 2 (Fase Persyaratan)**: Kartu "Persyaratan Umum" dengan daftar kriteria peserta magang (Siswa SMK / Mahasiswa D3/D4/S1, WFO di Diskominfo, jadwal & durasi).
   - **Slide 3 (Fase Dokumen)**: Kartu "Dokumen Pendaftaran" dengan daftar berkas yang wajib disiapkan (Surat Pengantar, CV, Transkrip/Rapor, Pas Foto).
   - **Slide 4 (Foto Dokumentasi Lama)**: Foto kegiatan magang resmi Diskominfo Sleman yang sebelumnya ada di slide 2 desktop dimasukkan kembali ke carousel mobile.
2. **Navigasi Carousel Modern**:
   - Tombol panah navigasi kiri (`<`) dan kanan (`>`) berbentuk lingkaran mengambang (*floating circular buttons*) dengan bayangan halus di sisi slider.
   - 4 indikator titik (*dots indicator*) di bagian bawah (`• • • •`) yang menyala aktif sesuai nomor slide yang aktif.
   - Dukungan gesture sentuh (*touch swipe*) halus di perangkat layar sentuh.
3. **Preservasi Desktop ($> 768\text{px}$)**:
   - Layout hero 3-kolom desktop yang sudah stabil tetap dipertahankan 100% tanpa perubahan visual.

---

## 2. Struktur Desain & Konten Tiap Slide di Mobile

```
┌─────────────────────────────────────────────────────────────────┐
│                    CAROUSEL HERO MOBILE                         │
│  [ < ]                                                   [ > ]  │
│                                                                 │
│   ┌─────────────────────────────────────────────────────────┐   │
│   │ SLIDE 1: PENDAFTARAN                                    │   │
│   │ KABUPATEN SLEMAN                                        │   │
│   │ Magang Kominfo Periode II                               │   │
│   │ »»»» 2026                                               │   │
│   │ Pendaftaran 1–15 Okt 2026 · mulai 2 Nov 2026            │   │
│   │                                                         │   │
│   │ [      Daftar Sekarang      ] (CTA Masuk ke Fase 1)     │   │
│   └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│   ┌─────────────────────────────────────────────────────────┐   │
│   │ SLIDE 2: PERSYARATAN                                    │   │
│   │ | Persyaratan Umum                                      │   │
│   │ • Siswa SMK aktif atau mahasiswa aktif D3/D4/S1 ...     │   │
│   │ • Bersedia magang secara luring (WFO) di Diskominfo ... │   │
│   │ • Mengikuti jadwal dan durasi magang ...                │   │
│   └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│   ┌─────────────────────────────────────────────────────────┐   │
│   │ SLIDE 3: DOKUMEN PENDAFTARAN                            │   │
│   │ | Dokumen Pendaftaran                                   │   │
│   │ • Surat Pengantar Sekolah / Perguruan Tinggi            │   │
│   │ • Curriculum Vitae (CV)                                 │   │
│   │ • Transkrip / Rapor Nilai Terakhir                      │   │
│   │ • Pas Foto                                              │   │
│   └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│   ┌─────────────────────────────────────────────────────────┐   │
│   │ SLIDE 4: FOTO KEGIATAN MAGANG                           │   │
│   │ [ Gambar Dokumentasi Resmi Diskominfo Sleman ]          │   │
│   │ Badge: DOKUMENTASI RESMI                                │   │
│   │ Caption: Magang Diskominfo Sleman                       │   │
│   └─────────────────────────────────────────────────────────┘   │
│                                                                 │
│                        ( •  •  •  • )                           │
└─────────────────────────────────────────────────────────────────┘
```

---

## 3. Rencana Implementasi Bertahap

### Tahap 1: Pembaruan Markup Template (`resources/views/public/blocks/hero.blade.php`)
- Menyediakan struktur responsif yang membedakan track slide antara Desktop dan Mobile tanpa menduplikasi data CMS:
  - **Di Desktop (`> 768px`)**: Tetap menampilkan 2 slide (Slide 1: Box 3 kolom, Slide 2: Foto dokumentasi).
  - **Di Mobile (`<= 768px`)**: Merender 4 slide terpisah:
    1. Slide Pendaftaran (Info periode + tombol CTA "Daftar Sekarang").
    2. Slide Persyaratan Umum.
    3. Slide Dokumen Pendaftaran.
    4. Slide Foto Dokumentasi Magang.
  - Memperbarui elemen dots indicator `#heroSliderDots` agar merender 4 tombol dot di mobile (`data-slide-index="0"` s/d `"3"`), sementara di desktop menampilkan 2 dot.

### Tahap 2: Penataan Styling Responsif (`resources/css/pages/public/landing.css` & `public/css/landing-modern.css`)
- Menghapus aturan `display: none !important;` yang sebelumnya menyembunyikan slide ke-2, dots, dan tombol navigasi di mobile.
- Memberikan styling pada 4 slide mobile:
  - Setiap slide memiliki tinggi proporsional dan kartu putih elegan (`background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 16px; box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05);`).
  - Slide 1: Tata letak rapi dengan tombol CTA `"Daftar Sekarang"` yang penuh di bawah jadwal pendaftaran.
  - Slide 2 & 3: Border aksen vertikal kiri (`border-left: 3.5px solid var(--accent); padding-left: 10px;`) dengan bullet poin yang nyaman dibaca di layar sentuh.
  - Slide 4: Foto dokumentasi berasio `16:9` / proporsional dengan sudut melengkung `14px`, `object-fit: cover`.
- Tombol navigasi floating samping (`#btnHeroSidePrev` & `#btnHeroSideNext`) ditampilkan rapi di kiri-kanan slider di mobile dengan lingkaran putih dan bayangan lembut.
- Indikator 4 dots ditata di tengah bawah dengan transisi warna aktif navy `#003566` / emas `#FFC300`.

### Tahap 3: Pembaruan Logika Slider (`resources/js/pages/public/landing.js`)
- Mengadaptasi fungsi `initHeroSlider()` agar secara dinamis menghitung jumlah slide aktif berdasarkan breakpoint:
  - Mendeteksi apakah sedang dalam mode mobile (4 slides) atau desktop (2 slides).
  - Mengupdate pergeseran track `translateX(-${currentIndex * 100}%)` sesuai jumlah slide.
  - Menambahkan event listener `touchstart` / `touchend` untuk swipe gesture pada mobile.
  - Mengaktifkan auto-slide interval (misal 6 detik) yang otomatis jeda (*pause*) saat user berinteraksi.

### Tahap 4: Verifikasi & Pengujian Mutu
1. **Asset Build**: Menjalankan `npm run build` dan memastikan `public/hot` tidak ada konflik.
2. **Visual Verification**: Mengambil tangkapan layar headless Edge pada ukuran mobile ($390 \times 844\text{px}$) untuk memvalidasi:
   - Tampilan Slide 1 (Pendaftaran + Tombol Daftar Sekarang).
   - Tampilan Slide 2 (Persyaratan Umum).
   - Tampilan Slide 3 (Dokumen Pendaftaran).
   - Tampilan Slide 4 (Foto Dokumentasi).
   - Transisi dots dan tombol panah kiri-kanan.
3. **Desktop Parity Verification**: Mengambil tangkapan layar desktop ($1280 \times 800\text{px}$) untuk memastikan tampilan 3-kolom desktop tetap utuh dan sempurna.
4. **Automated Testing**: Menjalankan test suite PHPUnit `LandingPageTest` dan `pint --test`.

---

## 4. Matriks File yang Akan Dimodifikasi

| No | File | Jenis Perubahan | Keterangan |
| :---: | :--- | :---: | :--- |
| 1 | `resources/views/public/blocks/hero.blade.php` | Markup Blade | Pemisahan 4 slide modular di mobile & penempatan tombol CTA di Slide 1 |
| 2 | `resources/css/pages/public/landing.css` | Styling CSS | Tata letak 4 slide kartu mobile, navigasi panah floating, dan 4 dots |
| 3 | `public/css/landing-modern.css` | Styling CSS | Penyelarasan token warna dan sinkronisasi fallback |
| 4 | `resources/js/pages/public/landing.js` | JavaScript ES Modules | Logika navigasi slider 4 item di mobile, sinkronisasi dots, dan swipe gesture |

---

## 5. Pertanyaan & Konfirmasi Pilihan Desain

Sebelum eksekusi dimulai, berikut 1 opsi urutan slide yang dapat Anda pertimbangkan:
- **Opsi Standar (Sesuai Permintaan Anda)**:
  - Slide 1: Pendaftaran (dengan Tombol "Daftar Sekarang")
  - Slide 2: Persyaratan Umum
  - Slide 3: Dokumen Pendaftaran
  - Slide 4: Foto Dokumentasi Kegiatan Magang
- **Opsi Alternatif (Foto Kegiatan di Slide 1)**:
  - Slide 1: Foto Dokumentasi Kegiatan Magang
  - Slide 2: Pendaftaran + Tombol "Daftar Sekarang"
  - Slide 3: Persyaratan Umum
  - Slide 4: Dokumen Pendaftaran

> *Secara default, kami akan mengeksekusi **Opsi Standar** sesuai deskripsi Anda.*
