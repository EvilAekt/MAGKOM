# Sistem Desain MAGKOM (DS-MagKom)

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-15 |
| Cakupan | 11 Master Tokens, Dark Mode, Bento Grid, Katalog Komponen UI, Animasi GSAP |
| Referensi Implementasi | `resources/css/base/variables.css`, `resources/css/components/` |

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

### 5.2 Modal Konfirmasi Interaktif (`modals.css` & `modal.js`)
- Menggantikan dialog native browser (`confirm()`, `prompt()`) dengan modal bergaya modern berbasis Plus Jakarta Sans dan backdrop blur (`backdrop-filter: blur(4px);`).
- Digunakan pada konfirmasi aksi kritis: Penetapan penempatan, penghapusan master data, eksekusi pemulihan backup basis data, dan penghentian bimbingan.

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

---

## 6. Standar Animasi GSAP 3.15 & Preloader Sleman

1. **Preloader Sleman**: Animasi splash pembuka dengan siluet logo Pemerintah Kabupaten Sleman, lingkaran transisi biru navy, dan teks sambutan resmi.
2. **Kepatuhan Kinerja**: Seluruh transisi GSAP memanipulasi properti transform (`scale`, `translate3d`) dan opacity. Animasi pada properti reflow (seperti `width`, `height`, `margin`, `top`, `left`) dilarang keras untuk mencegah layout thrashing.
