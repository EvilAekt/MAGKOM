# Sistem Desain MAGKOM (DS-MagKom)

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-18 |
| Cakupan | Master Light & Dark Tokens (--dm-*), Bento Navy, Bento Grid, Katalog Komponen UI, Animasi GSAP |
| Referensi Implementasi | `resources/css/base/variables.css`, `resources/css/components/`, `public/js/theme-init.js` |

---

## 1. Filosofi dan Identitas Visual

**DS-MagKom** (Design System MagKom) adalah sistem desain antarmuka resmi yang dibangun untuk memastikan konsistensi visual, ergonomi navigasi, aksesibilitas, dan identitas kelembagaan Pemerintah Kabupaten Sleman.

### Karakteristik Visual Inti:
1. **Wibawa Kedinasan**: Mengusung perpaduan biru khas Kominfo Sleman (*Sleman Navy & Royal Blue*) yang dipadukan dengan aksen emas cerah (*Vibrant Gold*).
2. **Keterbacaan Tinggi**: Menggunakan font **Open Sans** yang ramah pembaca untuk seluruh teks antarmuka dan **JetBrains Mono** untuk data telemetri, hash SHA-256, serta nilai kuota.
3. **Ergonomi Bento Grid**: Tata letak modular berbasis blok visual yang adaptif, mengintegrasikan ringkasan progres, aksi cepat, dan kalender kegiatan tanpa memicu kelelahan visual (*visual fatigue*).
4. **Performa Halus (60 FPS)**: Mikro-interaksi dan animasi splash preloader didorong oleh GSAP 3.15 dengan memanipulasi properti transform GPU-accelerated.

---

## 2. Master CSS Design Tokens (`variables.css`)

Sistem mengunci 11 token master CSS yang didefinisikan secara global pada `resources/css/base/variables.css`:

```css
:root {
    /* Brand Utama Pemkab Sleman Kominfo */
    --magkom-primary-navy: #02275D; /* Warna sidebar, header, dan elemen dominan */
    --magkom-royal-blue:   #01347C; /* Warna tombol aksi primer dan state hover */
    --magkom-deep-blue:    #003566; /* Warna latar belakang kontainer kontras */
    --magkom-accent-indigo:#3F51B5; /* Warna aksen sorotan sekunder */

    /* Aksen & Lencana Status */
    --magkom-gold-yellow:  #FFC300; /* Warna aksen lencana, bintang, dan penanda penting */
    --magkom-vibrant-gold: #FFCB05; /* Warna sorotan tombol hero dan state aktif */

    /* Netral & Tipografi */
    --magkom-dark-text:    #212529; /* Teks utama antarmuka (kontras tinggi) */
    --magkom-pure-black:   #000000; /* Bayangan dan kontras absolut */
    --magkom-neutral-gray: #AAAAAA; /* Teks sekunder, metadata tanggal */
    --magkom-border-gray:  #BBBBBB; /* Garis batas kartu dan pemisah tabel */
    --magkom-pure-white:   #FFFFFF; /* Latar belakang kartu dan teks tombol primer */

    /* Indikator Status Semantik */
    --status-success:      #10B981; /* Disetujui, Diterima, Selesai, SLA Hijau */
    --status-warning:      #F59E0B; /* Menunggu Review, SLA Kuning, Draf */
    --status-danger:       #EF4444; /* Ditolak, Terlambat, SLA Merah, Batal */
    --status-info:         #3B82F6; /* Terkirim, Informasi Kuota, Proses */
}
```

### 2.1 Master Dark Mode Tokens (`--dm-*`)
Untuk menjaga ergonomi penglihatan dan konsistensi lintas 4 dashboard peran (Peserta, Pembimbing, Admin, SuperAdmin), sistem menetapkan palet Bento Navy berbasis token `--dm-*` (diadopsi dari rancangan dashboard Pembimbing sebagai acuan emas):

```css
[data-theme="dark"] {
    /* Kanvas & Permukaan Dark Mode (Bento Navy) */
    --dm-canvas:        #061630; /* Latar belakang utama kanvas aplikasi */
    --dm-surface:       #0E2548; /* Permukaan kartu, modal, dan container form */
    --dm-navy-deep:     #031B40; /* Latar sidebar dan topbar gelap */
    --dm-border:        rgba(255, 255, 255, 0.07); /* Garis batas kartu halus */
    --dm-shadow:        0 4px 20px rgba(0, 0, 0, 0.50); /* Elevasi bayangan kontras */

    /* Tipografi Kontras Tinggi Dark Mode */
    --dm-text-title:    #EAF0FA; /* Judul heading dan teks paling menonjol */
    --dm-text-main:     #EAF0FA; /* Teks paragraf utama */
    --dm-text-muted:    #9FB0CC; /* Teks sekunder, label formulir, timestamp */
    --dm-text-subtle:   #5E7194; /* Ikon redup, placeholder, batas non-aktif */

    /* Hero Gradient & Indikator Aksen */
    --dm-hero-gradient: linear-gradient(135deg, #003566 0%, #01347C 100%);
    --dm-danger:        #d64545; /* Peringatan dan status gagal */
    --dm-warning:       #FFC300; /* Peringatan revisi dan status pending */
    --dm-dot-empty:     rgba(255, 255, 255, 0.12); /* Indikator pagination kosong */

    /* Pemetaan Otomatis Variabel Netral ke Token Dark Mode */
    --neutral-0:        var(--dm-surface);       /* #0E2548 */
    --neutral-50:       var(--dm-navy-deep);     /* #031B40 */
    --neutral-100:      var(--dm-surface);       /* #0E2548 */
    --neutral-150:      var(--dm-canvas);        /* #061630 */
    --neutral-200:      var(--dm-border);        /* rgba(255,255,255,0.07) */
    --neutral-400:      var(--dm-text-subtle);   /* #5E7194 */
    --neutral-500:      var(--dm-text-muted);    /* #9FB0CC */
    --neutral-700:      var(--dm-text-main);     /* #EAF0FA */
    --neutral-800:      var(--dm-text-title);    /* #EAF0FA */

    /* Kartu & Shell Terintegrasi */
    --card-surface:     var(--dm-surface);
    --card-border:      var(--dm-border);
    --card-text-primary:var(--dm-text-title);
    --card-text-muted:  var(--dm-text-muted);
    --sidebar-bg:       var(--dm-navy-deep);
}
```

### 2.2 Standar Ergonomi Dark Mode & Kepatuhan WCAG 2.1 AA
1. **Bento Navy Anti-Kelelahan Visual**: Dark mode MAGKOM menghindari warna hitam murni (`#000000`) untuk kanvas latar. Penggunaan `#061630` dan `#0E2548` memberikan nuansa kedinasan modern yang teduh bagi mata saat bekerja di malam hari (*reduces eye strain*).
2. **Rasio Kontras Keterbacaan**: Kombinasi teks `--dm-text-main` (`#EAF0FA`) di atas `--dm-surface` (`#0E2548`) menghasilkan rasio kontras 10.8:1, jauh melampaui batas minimum WCAG 2.1 AA (4.5:1).
3. **Pemberlakuan Khusus Portal Internal**: Sesuai mandat arsitektur, mode gelap hanya aktif di 4 Portal Dashboard internal (`/peserta`, `/pembimbing`, `/admin`, `/superadmin`). Halaman publik dan autentikasi tidak menggunakan dark mode guna mempertahankan wibawa identitas visual Pemkab Sleman.

---

## 3. Tipografi Standar

| Peran Tipografi | Font Family | Bobot (Weight) | Penggunaan Utama |
|---|---|---|---|
| **Antarmuka Utama** | `Open Sans`, sans-serif | 400 (Regular), 600 (Semibold), 700 (Bold) | Paragraf teks, label form, deskripsi tugas, navigasi sidebar. |
| **Heading & Modal** | `Plus Jakarta Sans`, sans-serif | 600, 700 | Judul halaman, dialog konfirmasi modal aksi kritis. |
| **Data & Kode** | `JetBrains Mono`, monospace | 500 (Medium), 700 (Bold) | Hash SHA-256 backup, IP address, waktu latensi, kuota angka. |

---

## 4. Arsitektur Bento Grid Dashboard

Tata letak dashboard Peserta dan Admin menerapkan pola **Bento Grid**:

```
┌──────────────────────────────────────┬───────────────────┐
│ Kartu Hero: Ringkasan Status Magang  │ Status SLA &      │
│ & Linimasa Progres Hari Aktif        │ Indikator Kuota   │
├───────────────────┬──────────────────┴───────────────────┤
│ Kalender & Presensi│ Daftar Tugas Terstruktur            │
│ Logbook Harian    │ Ber-Deadline Mendekati Jatuh Tempo   │
└───────────────────┴──────────────────────────────────────┘
```

- Menggunakan native CSS Grid dengan `grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));`.
- Kartu memiliki efek *hover lift* halus (`transform: translateY(-2px);`) dengan bayangan elevasi ringan (`box-shadow: 0 4px 12px rgba(0,0,0,0.05);`).

---

## 5. Katalog Komponen Antarmuka Utama

### 5.1 Tombol (Buttons - `buttons.css`)
- `.btn-primary`: Tombol biru royal dengan teks putih untuk aksi utama (Simpan, Ajukan, Terbitkan).
- `.btn-secondary`: Tombol outline abu-abu untuk aksi kembali atau pembatalan draf.
- `.btn-danger`: Tombol merah kirmisi untuk aksi destruktif (Tolak Berkas, Hapus Draf).
- `.btn-warning`: Tombol emas kuning untuk aksi revisi (Perlu Perbaikan, Batal Kirim).

### 5.2 Modal Konfirmasi Interaktif (`modals.css`, `confirm-modal.css` & `modal.js`)
- Menggantikan dialog native browser (`confirm()`, `prompt()`) dengan modal bergaya modern berbasis Plus Jakarta Sans dan backdrop blur (`backdrop-filter: blur(4px);`).
- **User Identity Strip & Impact Notice Card**: Pada modal verifikasi berkas admin dan modul penugasan pembimbing, modal dilengkapi strip identitas pengguna (nama, NIM/NISN, institusi, jurusan) serta kartu pemberitahuan dampak aksi yang bebas garis vertikal rigid.
- **Harmonisasi Warna Keputusan**: Aksi penerimaan berkas mengadopsi tema *Sleman Blue* (`#0641CC`) dengan kontras aksen elegan, sedangkan aksi penolakan atau destruktif menggunakan merah kirmisi (`#DC2626`).
- Digunakan pada konfirmasi aksi kritis: Keputusan seleksi berkas, penetapan penempatan, penghapusan master data, eksekusi pemulihan backup basis data, dan penghentian bimbingan.

### 5.3 Lencana Status (Status Badges)
Komponen pill kecil yang menyajikan status secara instan:
- `.badge-success`: Latar hijau lembut dengan teks hijau tua (Disetujui / Selesai).
- `.badge-warning`: Latar kuning lembut dengan teks kuning tua (Menunggu / Meninjau).
- `.badge-danger`: Latar merah lembut dengan teks merah tua (Ditolak / Terlambat).
- `.badge-info`: Latar biru lembut dengan teks biru tua (Diajukan / Diproses).

### 5.4 Notifikasi Toast Anti-XSS (`notifications.css` & `toast.js`)
- Mengambang di sudut kanan atas layar (`position: fixed; top: 20px; right: 20px; z-index: 9999;`).
- Otomatis menghilang setelah 4 detik dengan transisi fade-out halus.
- Dibuat aman dari injeksi XSS dengan manipulasi elemen murni via `textContent`.

### 5.5 Sakelar Tema Terpadu Sidebar Footer (`dashboard.blade.php` & `peserta.blade.php`)
- **Desain Ikon Tunggal**: Tombol di bagian footer sidebar menampilkan satu ikon SVG kontekstual: ikon matahari (`sun-icon`) saat mode aktif adalah gelap, dan ikon bulan (`moon-icon`) saat mode aktif adalah terang.
- **Pengendalian via Atribut HTML**:
  ```css
  html:not([data-theme="dark"]) .sidebar-footer .sun-icon { display: none !important; }
  html:not([data-theme="dark"]) .sidebar-footer .moon-icon { display: inline-block !important; }
  html[data-theme="dark"] .sidebar-footer .sun-icon { display: inline-block !important; }
  html[data-theme="dark"] .sidebar-footer .moon-icon { display: none !important; }
  ```
- **Sinkronisasi Otomatis**: Klik pada tombol memicu rotasi preferensi (`terang` <-> `gelap`), memperbarui `localStorage.magkom_theme`, mengeset cookie `tema_tampilan`, dan memanggil endpoint preferensi akun via AJAX secara senyap (*background sync*).

### 5.6 Diagram Batang Vertikal Bento & Track Bergaris Arsir (Vertical Striped Bar Charts)
Komponen visual analitik vertikal (digunakan pada "Distribusi Tugas" di dashboard Pembimbing/Peserta dan "Status Antrean Berkas" di dashboard SuperAdmin):
- **Wadah Bingkai Dalam (*Inner Chart Box*)**: Elemen container ber-radius 14px (`border-radius: 14px; border: 1px solid #E3E8F0;`) pada Light Mode dan `background: #0E2548; border-color: rgba(255, 255, 255, 0.07);` pada Dark Mode.
- **Track Bergaris Arsir Diagonal (*Hatching Stripes Pattern*)**: Track kolom bar memiliki pola diagonal `-45deg` yang konsisten di kedua tema:
  - Light Mode: `background-color: #E5EDF8; background-image: repeating-linear-gradient(-45deg, #E5EDF8, #E5EDF8 6px, #D3E0F2 6px, #D3E0F2 12px); border: 1px solid #CAD6E8;`
  - Dark Mode: `background-color: #132644; background-image: repeating-linear-gradient(-45deg, #132644, #132644 6px, #1C365D 6px, #1C365D 12px); border: 1px solid rgba(76, 132, 232, 0.28);`
- **Geometri Bar (*Pill-Top Flush Bottom*)**: Bagian terisi memiliki sudut melengkung hanya di sisi atas (`border-radius: 12px 12px 0 0`) dan menempel rata pada dasar track (*flush bottom*).
- **Kontras Aksesibilitas Nilai Metrik**: Nilai metrik di dalam bar menggunakan font tebal (`font-weight: 800; font-size: 15px-16px`) berwarna putih `#FFFFFF` untuk bar gelap/biru/merah, serta warna navy pekat `#02275D` khusus untuk bar kuning emas/amber guna menjamin kontras rasio WCAG 2.1 AA.

### 5.7 Kartu Peringatan Kritis SLA / Perlu Tindakan (SLA Alert Cards)
Komponen kartu highlight merah kirmisi untuk menarik atensi terhadap dokumen atau tugas yang melewati batas SLA (SuperAdmin & Pembimbing):
- **Palet Visual**: Latar merah pekat `#DC2626` / `rgba(220, 38, 38, 0.95)` dengan border halus `rgba(255, 255, 255, 0.15)`.
- **Ikon Peringatan**: Wadah ikon segitiga peringatan dengan latar belakang transparan ber-radius halus (`background: rgba(255, 255, 255, 0.18)`).
- **Indikator Titik Segmen (*Segmented Pills/Dots*)**: Baris titik segmen horizontal (`.sa-dots-row`) yang menandai rasio dokumen kritis terhadap batas pagu, dengan titik aktif berwarna putih solid (`#FFFFFF`) dan titik kosong transparan (`rgba(255, 255, 255, 0.2)`).

### 5.8 Komponen Identitas Kemitraan Footer & Kolom Pengembang
Footer publik mengintegrasikan identitas kemitraan tri-institusi resmi di atas baris hak cipta:
- **Tiga Logo Kemitraan**:
  - **Pemkab Sleman (Diskominfo)**: `public/images/logo-sleman.png` (38x46px).
  - **Universitas AMIKOM Yogyakarta**: `public/images/logo-amikom.png` (46x46px, logogram ungu-emas).
  - **D3 Manajemen Informatika AMIKOM**: `public/images/logo-mi.png` (58x46px, tipografi putih aksen oranye).
- **Grid Navigasi Simetris 5 Kolom Desktop**: `grid-template-columns: 1.75fr 1fr 1fr 1fr 1fr;` membagi kolom: Profil Dinas, Tautan Terkait, Media Sosial, Akun, dan kolom profil **Tim Pengembang**.
- **Grid Simetris 2x2 Kolom Mobile**: Pada perangkat layar sentuh ($\le 768\text{px}$), kolom navigasi tertata rapi dalam 2 baris 2 kolom simetris tanpa ruang kosong.

### 5.9 Komponen Linimasa Jalan Lurus Tahapan & Tombol Sejajar Mobile (`#tahapan`)
- **Geometri Jalan Lurus Sumbu X Terpusat ($X = 48$)**:
  - Lintasan SVG aspal `#roadSvgPath` membentang vertikal lurus `d="M48 -80 L48 920"` dengan marka putus-putus kuning di tengah.
  - Mobil `#roadCarGroup` bergerak turun lurus dengan rotasi sudut stabil $90^\circ$ saat pengguna melakukan scrolling.
  - Empat simpul checkpoint melingkar (Buat Akun, Pilih Bidang & Berkas, Verifikasi & Penempatan, Mulai Bimbingan) terpasang presisi tepat di atas sumbu lurus $X = 48$.
- **Tombol Aksi Sejajar Mobile**:
  - Kontainer `.tahapan-action-buttons` menggunakan CSS Grid 2-kolom seimbang (`grid-template-columns: 1fr 1fr; gap: 0.5rem;`) pada tampilan mobile, menyandingkan tombol Unduh Panduan PDF dan Peta Alur Interaktif secara ringkas dan sejajar.

### 5.10 Unifikasi Token Sidebar Navy & Perataan Kanan Footer Lintas Peran
- **Standarisasi Warna Sidebar 4 Dashboard**: Seluruh sidebar (SuperAdmin, Admin, Pembimbing, Peserta) diseragamkan dengan token Master Bento Navy (`--sidebar-navy-bg: #0B132B`, `--sidebar-navy-surface: #1C2541`).
- **Footer Copyright Rata Kanan**: Teks hak cipta di footer seluruh dashboard peran diformat rata kanan (`text-align: right`) untuk konsistensi visual enterprise.

### 5.11 Komponen Formulir Kartu Tim Dinamis (Dynamic Team Roster Cards)
- Form registrasi jalur kelompok menyediakan antarmuka kartu tim reaktif di sisi klien (`auth-depth.js`).
- Setiap kartu anggota memuat field input NISN/NIM, Nama Lengkap, dan Email, dengan tombol aksi hapus anggota dinamis dan validasi pencegahan duplikasi data antar-anggota rombongan.

### 5.12 Standardisasi Judul Merek MAGKOM Solid White & Layout Navigation Sync
- **Branding MAGKOM Putih Solid**: Judul identitas merek `MAGKOM` pada topbar seluruh 4 dashboard peran dan navbar landing page dikunci secara tegas menggunakan warna putih solid `#FFFFFF` (`.nav_title .site_title`, `.brand-title`), menghilangkan disparitas warna teks dan menjamin ketajaman kontras di atas latar belakang navbar Abyssal Navy kedinasan.
- **Sinkronisasi Event Keyboard Esc & Hamburger Toggle**: Interaksi penekanan tombol `Esc` secara konsisten menutup mini-sidebar flyout atau drawer di semua dashboard peran tanpa memicu deformasi layout atau desinkronisasi kelas CSS (`nav-md` vs `nav-sm`).
- **Resolusi Container Height Clamping**: Memastikan kontainer `.sidebar-menu-wrapper` dan `.menu_section` memiliki kalkulasi tinggi dinamis yang beradaptasi dengan isi menu navigasi sehingga tidak terjadi pemotongan elemen (*truncation*) pada menu pembimbing.

---

## 6. Standar Animasi GSAP 3.15 & Preloader Sleman

1. **Preloader Sleman**: Animasi splash pembuka dengan siluet logo Pemerintah Kabupaten Sleman, lingkaran transisi biru navy, dan teks sambutan resmi.
2. **Kepatuhan Kinerja**: Seluruh transisi GSAP memanipulasi properti transform (`scale`, `translate3d`) dan opacity. Animasi pada properti reflow (seperti `width`, `height`, `margin`, `top`, `left`) dilarang keras untuk mencegah layout thrashing.
