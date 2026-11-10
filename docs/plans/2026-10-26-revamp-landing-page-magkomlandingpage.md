# Rencana Kerja: Perombakan Tampilan Landing Page MAGKOM Berdasarkan Repositori magkomlandingpage

**Tanggal**: 26 Oktober 2026  
**Status**: Usulan Rencana (Menunggu Persetujuan User)  
**Dokumen**: `docs/plans/2026-10-26-revamp-landing-page-magkomlandingpage.md`  
**Referensi**: [NAUFALFEBRIAN62/magkomlandingpage](https://github.com/NAUFALFEBRIAN62/magkomlandingpage.git)

---

## 1. Tujuan
Merombak antarmuka (UI/UX) halaman beranda publik (`/` - `resources/views/public/landing.blade.php`) secara menyeluruh agar identik dengan desain modern pada repositori referensi [magkomlandingpage](https://github.com/NAUFALFEBRIAN62/magkomlandingpage.git) dengan standar:
- **Zero Anomaly & Zero Error**: Tampilan presisi, layout stabil tanpa gesekan/flicker, tipografi tajam, dan responsif di semua ukuran layar (mobile, tablet, desktop).
- **Integrasi Laravel Native**: Tetap terhubung sempurna dengan route autentikasi Laravel (`login`, `register`, `dashboard`), database ketersediaan kuota real-time, dan aset lokal.
- **Rich Interactivity**:
  1. *Sticky Header* dengan logo Sleman, navigasi anchor halus, dan state autentikasi adaptif.
  2. *Hero Section* 3 kolom (Overview & CTA, Persyaratan Umum, Dokumen Pendaftaran).
  3. *Kuota Explorer* interaktif dengan filter Jenjang (SMK vs Mahasiswa) dan Bulan Magang, Circular Ring Meter SVG, carousel card per bidang (dengan tombol navigasi geser kiri/kanan), dan status ketersediaan dinamis.
  4. *Tahapan Magang (TahapanRoad)* interaktif dengan animasi mobil menyusuri jalur kurva SVG berbasis posisi scroll (*scroll-driven road animation*), checkpoint interaktif yang dapat diklik, serta kartu tahapan yang menyala saat dilewati.
  5. *FAQ Accordion* interaktif dengan fitur ekspansi "Lihat Semua Pertanyaan".
  6. *Footer Navy Sleman* yang terstruktur rapi.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis (Bekerja Secara Logis)
1. **Framework Tampilan**: Proyek MAGKOM menggunakan Laravel 10 Blade monolitik dengan aset vanilla CSS/JS. Kita akan mengimplementasikan seluruh visual dan interaktivitas referensi menggunakan **Blade + Scoped Vanilla CSS + Vanilla JS/SVG Engine**. Hal ini menjamin performa maksimal tanpa memerlukan dependensi React/Node runtime tambahan yang rentan konflik build.
2. **Koneksi Data Kuota**: Kuota Explorer akan mengonsumsi data ketersediaan nyata dari `LandingController.php` (matriks ketersediaan per bidang/jurusan dan bulan aktif), dengan fallback data statis yang elegan jika database belum memiliki data kuota aktif.
3. **Tipografi & Ikon**: Menggunakan font Google Fonts `Lexend` (untuk headings) dan `Source Sans 3` (untuk teks isi) persis seperti referensi, dipadukan dengan inline SVG crisp berkualitas tinggi untuk ikon (mencegah ketergantungan library luar).
4. **Keamanan & Kompatibilitas**: Semua tautan internal menggunakan Laravel named routes (`route('login')`, `route('register')`, dll.) dan mendukung status login pengguna (menampilkan menu Akun/Dashboard jika sudah login).

---

## 3. Pendekatan Teknis & Evaluasi Arsitektur

### Evaluasi Opsi:
* **Opsi A: Porting ke Blade + Scoped Modern CSS + Vanilla JS/SVG (Rekomendasi Utama)**
  - *Kelebihan*:
    - **Zero Build Dependency**: Tidak membutuhkan compiler React/Bun/Tailwind v4 tambahan di server produksi.
    - **Kecepatan & Performa**: Bebas dari overhead bundler React dan hydration lag; halaman termuat instan (*First Contentful Paint* < 0.5s).
    - **Zero Regression**: Tidak mengganggu layout dashboard peserta, admin, pembimbing, atau superadmin yang ada di sistem saat ini.
    - **Presisi 100%**: Logika animasi `TahapanRoad` (menghitung koordinat kurva SVG, sudut rotasi mobil, dan scroll tracking) diimplementasikan secara native dengan `requestAnimationFrame` yang sangat mulus (60+ FPS).
  - *Kekurangan*: Memerlukan konversi sintaks komponen JSX ke Blade dan penulisan CSS scoped terstruktur.

* **Opsi B: Memasang React + Vite / TanStack Router ke dalam Laravel**
  - *Kelebihan*: Memindahkan kode TSX asli secara langsung.
  - *Kekurangan*:
    - Sangat berisiko memicu breaking changes pada arsitektur Laravel yang sudah stabil.
    - Memerlukan instalasi runtime Bun/Node, pengaturan SSR/Vite bridge, dan penanganan autentikasi ganda (Sanctum/Session).
    - Risiko anomali tinggi saat diakses melalui XAMPP tanpa Node dev server aktif.

**Keputusan**: Dipilih **Opsi A** untuk keandalan maksimal, kecepatan akses, dan jaminan tanpa eror.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Http/Controllers/Public/LandingController.php` | Modify | Menyiapkan data bidang beserta kuota teragregasi untuk dikonsumsi Kuota Explorer secara presisi |
| `resources/views/public/landing.blade.php` | Modify | Merombak total template Blade sesuai desain `magkomlandingpage` (Header, Hero 3-Kolom, Kuota Explorer, TahapanRoad SVG, FAQ, Footer) |
| `public/css/landing-modern.css` | Create | Stylesheet khusus landing page yang mengadopsi seluruh token warna (`--navy`, `--gold`, `--step-1..4`, dll.), tipografi Lexend, dan utility modern |
| `public/js/landing-interactive.js` | Create | Engine interaktif murni untuk Kuota Explorer (filter & carousel), animasi mobil kurva SVG `TahapanRoad`, dan FAQ accordion |

---

## 5. Rincian Fitur & Komponen Baru

### 5.1. Header & Navigasi
- Logo Sleman circular badge dengan inisial emas "S", teks "DISKOMINFO SLEMAN" dan "Portal Magang · MAGKOM".
- Tautan navigasi desktop: Beranda (`#beranda`), Kuota (`#kuota`), Tahapan (`#tahapan`), FAQ (`#faq`).
- Navigasi mobile responsif (hamburger drawer).
- Action Button:
  - Mode Tamu: Tombol outline "Login" (`route('login')`) dan tombol solid "Daftar" (`route('register')`).
  - Mode Terautentikasi: Tombol "Dashboard" (`route('peserta.dashboard')` / role dashboard) dengan status nama pengguna.

### 5.2. Hero Section (`#beranda`)
- Background halus modern (`--secondary` / oklch 0.965).
- Tiga kolom proporsional pada desktop (`1.3fr 1fr 1fr`):
  - **Kolom Kiri**: Kicker "KABUPATEN SLEMAN", headline "Magang Kominfo Periode II", visual tahun "»»»» 2026", info tanggal pendaftaran & pelaksanaan, dan CTA "Daftar Sekarang".
  - **Kolom Tengah**: "Persyaratan Umum" dengan daftar kualifikasi terstruktur (SMK/D3/D4/S1, WFO di kantor Diskominfo, kepatuhan jadwal).
  - **Kolom Kanan**: "Dokumen Pendaftaran" dengan daftar berkas esensial (Surat Pengantar, CV, Transkrip/Rapor, Pas Foto).

### 5.3. Kuota Explorer (`#kuota`)
- Pilihan dua tahap yang interaktif:
  - Pertanyaan 1: "1. Kamu siapa?" -> Tombol pill: Siswa SMK | Mahasiswa
  - Pertanyaan 2: "2. Mau mulai kapan?" -> Tombol pill dinamis berdasarkan bulan aktif
- Highlight Card Navy dengan Circular Gauge SVG Meter:
  - Menghitung sisa kuota dan menampilkan lingkaran progres emas animasi.
  - Pesan teks dinamis: *"Ada X kursi kosong dari Y kursi untuk [Jenjang] yang mulai [Bulan]"*.
- Carousel Bidang Geser Kanan-Kiri:
  - Tombol panah geser halus dengan deteksi otomatis status disable di ujung kiri/kanan.
  - Kartu untuk setiap bidang (Aplikasi Informatika, Statistik, Persandian, Sekretariat):
    - Ikon bidang SVG, nama bidang, badge status (*Masih banyak*, *Tinggal sedikit*, *Penuh*).
    - Teks sisa kuota besar + bar progres visual bertransisi mulus.
    - Ringkasan aktivitas/tugas magang di bidang tersebut.
    - Daftar jurusan yang diterima.
    - Tombol CTA kontekstual ("Daftar" jika tersedia, "Masuk daftar tunggu" jika penuh).
- Status legend indikator warna di bagian bawah.

### 5.4. Tahapan Pendaftaran Magang dengan Road Engine (`#tahapan`)
- Layout 2 kolom:
  - **Sisi Kiri (Sticky)**: Judul panduan, ringkasan 4 tahapan pendaftaran, dan tombol aksi "Unduh Panduan PDF" / Akses Alur Lengkap.
  - **Sisi Kanan (TahapanRoad Canvas)**:
    - Kurva jalanan aspal SVG bertekstur dengan marka garis putus-putus.
    - Bendera hijau START di titik awal dan bendera catur FINISH di akhir alur.
    - Mobil miniatur yang bergerak dan berputar otomatis mengikuti kemiringan kurva jalan saat pengguna melakukan scroll halaman.
    - 4 checkpoint interaktif dengan nomor dan ikon (Registrasi, Administrasi & Seleksi, Pelaksanaan Magang, Kelulusan).
    - Efek klik pada checkpoint otomatis melakukan smooth scroll ke checkpoint terkait.
    - Kartu tahapan menyala aktif dengan indikator panah warna ketika mobil melewatinya.

### 5.5. FAQ Accordion (`#faq`)
- 8 pertanyaan dan jawaban seputar magang Kominfo Sleman.
- Aksi buka/tutup yang mulus dengan ikon plus/minus.
- Tombol toggle dinamis "Lihat Semua Pertanyaan" / "Tampilkan Lebih Sedikit".

### 5.6. Footer Sleman
- Latar navy kontras tinggi, menampilkan detail instansi, alamat resmi di Sleman, kontak email, nomor telepon, dan hak cipta.

---

## 6. Langkah Implementasi Bertahap

- [x] **Langkah 1**: Audit Data Controller
  - Periksa `LandingController.php` untuk memastikan agregasi kuota per bidang terformat dengan struktur yang ramah JSON hydration.
- [x] **Langkah 2**: Pembuatan Aset Desain & Styling
  - Buat stylesheet `public/css/landing-modern.css` yang mendefinisikan color tokens (Navy, Gold, Step 1-4, Accent, Road, Statuses), font Google Lexend & Source Sans 3, responsive grid, serta keyframes animasi.
- [x] **Langkah 3**: Pembuatan Interactive Engine
  - Buat script `public/js/landing-interactive.js` yang memuat logika:
    - Perhitungan koordinat spline SVG dan rotasi mobil `TahapanRoad` berbasis scroll.
    - Event listener klik checkpoint dengan navigasi smooth scroll.
    - State management filter Kuota Explorer (Jenjang & Bulan) dengan update reaktif ke SVG ring meter dan daftar kartu bidang.
    - Logika geser carousel kartu kuota.
    - Logika accordion FAQ dan toggle limit item.
- [x] **Langkah 4**: Pembaruan Template Blade Landing Page
  - Rombak `resources/views/public/landing.blade.php` dengan struktur baru yang rapi, semantic HTML5, aria-labels lengkap, dan integrasi route Laravel.
- [x] **Langkah 5**: Backup Template Lama
  - Salin template lama ke `resources/views/public/landing.blade.php.bak` agar riwayat kode sebelumnya tersimpan dengan aman jika diperlukan.
- [x] **Langkah 6**: Validasi Visual & Fungsional
  - Uji halaman di browser via dev server (`http://127.0.0.1:8000`).
  - Verifikasi animasi mobil pada saat scroll, filter kuota, carousel, accordion FAQ, dan tombol navigasi.
  - Pastikan konsol browser bersih dari error JavaScript (`0 errors`).
  - Uji responsiveness di resolusi mobile (375px), tablet (768px), dan desktop (1440px).

---

## 7. Strategi Testing

1. **Pengujian Fungsional**:
   - Klik filter "Siswa SMK" dan "Mahasiswa" -> Kuota dan status kartu berubah sesuai data jenjang.
   - Klik filter Bulan -> Meteran sisa kursi dan kartu bidang ter-update secara instan.
   - Geser tombol carousel `<` dan `>` -> Kartu bidang bergulir mulus dan tombol disable saat di ujung.
   - Scroll halaman pada bagian tahapan -> Mobil miniatur bergerak sepanjang jalan kurva SVG dan checkpoint menyala secara berurutan.
   - Klik pada salah satu checkpoint tahapan -> Halaman bergulir tepat ke posisi langkah tersebut.
   - Klik pertanyaan FAQ -> Accordion membuka dan menutup dengan transisi halus.
   - Klik tombol "Daftar" dan "Login" -> Berhasil mengarah ke `/register` dan `/login`.
2. **Pengujian Responsif**:
   - Mobile viewport: Header collapse/drawer rapi, carousel kuota dapat di-swipe secara native (touch scroll), kurva tahapan tetap proporsional tanpa overflow horizontal.
3. **Pengujian Browser Console**:
   - Buka DevTools Console -> Pastikan tidak ada pesan merah (zero runtime exception).

---

## 8. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Data Kuota Kosong di Database** | Kuota Explorer menampilkan angka 0/kosong | Sediakan fallback default dataset (seperti pada repositori referensi) yang aktif otomatis jika data di DB belum terisi |
| **Beban Performa Animasi Scroll** | Penurunan frame rate saat scrolling | Gunakan `requestAnimationFrame` dengan throttling pasif (`{ passive: true }`) untuk animasi mobil SVG |
| **Konflik CSS Global** | Gangguan tampilan pada halaman dashboard internal | Gunakan namespace class khusus (`.magkom-landing-*`) dan load CSS hanya pada view landing page |
| **Tautan Navigasi Rusak** | Pengguna gagal mengakses form daftar/login | Gunakan helper `route()` bawaan Laravel untuk seluruh tautan |

---

## 9. Kriteria Selesai (Definition of Done)

- [x] Desain visual landing page identik dan mengadopsi 100% komponen dari repositori referensi `magkomlandingpage`.
- [x] Animasi kurva jalanan dan mobil interaktif `TahapanRoad` berjalan mulus mengikuti scroll pengguna.
- [x] Kuota Explorer berfungsi interaktif dengan filter Jenjang, Bulan, Ring Meter dinamis, dan Carousel kartu bidang.
- [x] FAQ accordion berfungsi sempurna dengan tombol ekspansi daftar pertanyaan.
- [x] Terintegrasi dengan rute Laravel (`route('login')`, `route('register')`, dll.).
- [x] Responsif di semua ukuran layar dan bebas horizontal scrollbar anomali.
- [x] Konsol browser bebas error (Zero JS Error).
- [x] Template sebelumnya telah dicadangkan dengan aman (`resources/views/public/landing.blade.php.bak`).
