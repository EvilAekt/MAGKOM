# Design System MagKom (Magang Kominfo)

**Dokumen Design System, pendamping PRD**
Aturan tampilan, token, komponen, dan pemetaan status untuk portal magang Dinas Kominfo

| | |
|---|---|
| Versi dokumen | 1.0 (Draft untuk review) |
| Tanggal | 9 Oktober 2026 |
| Status | Draft; belum disetujui Product Owner dan desainer |
| Penyusun / Reviewer | *[diisi]* |
| Berlaku untuk | `PRD-MagKom.md` v1.1 (bab 9 dan 10.7) dan `TDD-MagKom.md` |
| Sumber | Ekspor file Figma **MAGKOM Kominfo Sleman** (`.fig`, diekspor 8 Oktober 2026). Nilai diekstrak langsung dari isi file (variabel, frame, dan properti node), bukan dari tangkapan layar. Halaman Figma yang dipakai: **`testing`**, bagian "Design system dan overlay" (Design System v1.0, 30 September 2026) dan layar-layar terbarunya |
| Tautan Figma | Kedua key di PRD 9.1 tidak dapat dibuka tanpa akses; dokumen ini disusun dari berkas ekspor. Tautan final tetap menunggu OQ-13 |

## Cara Membaca Dokumen Ini

**Untuk developer** (sekitar 20 menit): baca bab 2.1, 2.2, 3.3, 4, dan 5. Bab 6 berisi cara memasang token ke Tailwind dan Blade. Bab 7 memuat daftar hal di Figma yang **tidak boleh** dibangun karena di luar PRD.

**Untuk desainer**: baca bab 2.6, 7, dan 8. Isinya temuan kontras, halaman yang belum ada, dan ketidakkonsistenan file.

**Konvensi penulisan.** Dokumen ini memakai penanda PRD (**Wajib**, **Tidak boleh**, **Sebaiknya**, `[Terkunci]`, `[Usulan]`, AC, OQ) dengan tambahan:

| Penanda | Arti |
|---|---|
| `[Figma]` | Nilai diambil apa adanya dari file Figma |
| `[Usulan]` | Pilihan yang ditetapkan dokumen ini karena Figma tidak menetapkan, tidak konsisten, atau melanggar aturan PRD. Boleh dipakai sampai ada keputusan |
| `[Celah]` | Hal yang dibutuhkan PRD tetapi belum ada desainnya di Figma |
| AC-DS-n | Acceptance criteria design system, ditulis "Jika [kondisi], maka [hasil]" dan wajib punya test atau langkah UAT |

**Bila dokumen bertentangan.** Mengikuti PRD 9.1: PRD berlaku untuk perilaku (aturan bisnis dan AC), Figma berlaku untuk tampilan. Dokumen ini berlaku sebagai tafsir tampilan yang sudah dirapikan; bila bertentangan dengan Figma, dokumen ini yang dipakai sampai desainer memperbarui Figma. Laporkan pertentangan ke Product Owner dan desainer.

## Daftar Isi

1. [Ringkasan dan Prinsip](#1-ringkasan-dan-prinsip)
2. [Fondasi](#2-fondasi)
3. [Komponen](#3-komponen)
4. [Pemetaan Status PRD ke Tampilan](#4-pemetaan-status-prd-ke-tampilan)
5. [Aturan Wajib dan Acceptance Criteria](#5-aturan-wajib-dan-acceptance-criteria)
6. [Implementasi](#6-implementasi)
7. [Cakupan Figma terhadap PRD](#7-cakupan-figma-terhadap-prd)
8. [Temuan Kualitas File Figma](#8-temuan-kualitas-file-figma)
9. [Pertanyaan Terbuka](#9-pertanyaan-terbuka)
10. [Integrasi ke PRD dan TDD](#10-integrasi-ke-prd-dan-tdd)
- [Lampiran: Rujukan Node Figma](#lampiran-rujukan-node-figma)

---

## 1. Ringkasan dan Prinsip

MagKom adalah aplikasi web server-rendered (Blade) dengan empat peran dan tiga konteks tampilan: **publik** (landing page, Kebijakan Privasi), **auth**, dan **aplikasi** (dashboard dengan sidebar untuk Peserta, Admin, Pembimbing, SuperAdmin). Design system ini menyatukan ketiganya dengan satu palet, satu keluarga huruf, dan satu set komponen.

| Prinsip | Artinya di MagKom | Sumber |
|---|---|---|
| **Status selalu terlihat** | Tidak ada notifikasi (BR-14), sehingga dashboard, badge status, catatan Admin, dan timeline adalah satu-satunya sarana informasi peserta. Setiap status memakai warna **dan** ikon **dan** teks | NFR-U3, DS bab 4 |
| **Netral dulu, biru untuk aksi** | Putih dan abu-abu sebagai dasar; biru hanya untuk aksi, tautan, dan fokus; amber, merah, hijau hanya untuk makna status | `[Figma]` 01.1 |
| **Dapat diakses** | WCAG 2.1 AA, fokus terlihat, target sentuh minimal 44 px, makna tidak bergantung warna | PRD 9.5, `[Figma]` 01.4 |
| **Aman dibangun di stack ini** | Font di-host sendiri, tanpa atribut `style=` inline, tanpa sumber eksternal (CSP produksi di TDD 8.3 hanya mengizinkan `'self'`) | TDD 8.3, bab 6 |
| **Tidak membocorkan slot khusus** | Komponen publik tidak pernah menerima data slot khusus | BR-02, R-08 |

---

## 2. Fondasi

### 2.1 Warna

Palet berisi 30 token di variabel Figma `MagKom/Warna` `[Figma]`. Nama token mengikuti Figma agar serah-terima mudah.

**Netral**

| Token | Hex | Pemakaian |
|---|---|---|
| Neutral 0 | `#FFFFFF` | Permukaan kartu, field, header, modal |
| Neutral 100 | `#F8FAFC` | Latar halaman (publik), kotak ringkasan di dalam modal |
| Neutral 150 | `#F5F7FB` | Latar area kerja aplikasi (dashboard) |
| Neutral 200 | `#EEF0F5` | Latar header tabel, field nonaktif, tombol nonaktif, badge netral, pemisah internal |
| Neutral 400 | `#D0D3D9` | Border kartu, field, tabel, topbar |
| Neutral 500 | `#7A8494` | Ikon, teks nonaktif. **Bukan** untuk teks bantuan (lihat 2.6) |
| Neutral 600 | `#666666` | Teks sekunder, deskripsi, breadcrumb induk |
| Neutral 700 | `#333333` | Teks isi pada beberapa tabel |
| Neutral 800 | `#1D2530` | Teks utama, judul, footer, scrim modal |
| Neutral 900 | `#05070B` | Judul spesimen dokumentasi |

**Biru (aksi, tautan, fokus, informasi)**

| Token | Hex | Pemakaian |
|---|---|---|
| Blue 600 | `#0751FF` | Tombol primer, tautan, border fokus, teks badge info |
| Blue 700 | `#0641CC` | Hover tombol primer, sidebar aplikasi, teks chip peran |
| Blue Tint | `#E6EEFF` | Latar badge info, avatar, item aktif ringan |
| Blue Tint Line | `#B3C8FF` | Border pada elemen berlatar Blue Tint |
| Blue 500 | `#2EA3F2` | Dekoratif di atas latar gelap saja (lihat 2.6) |
| Sky 100 | `#E0F2FE` | Latar badge alternatif (Disetujui, Final, Siap) pada beberapa layar; badge info standar memakai Blue Tint |

**Status**

| Makna | Latar | Border | Teks |
|---|---|---|---|
| Sukses | Green 100 `#E8F7EE` | Green 600 `#16A34A` | Green 700 `#166534` |
| Peringatan | Amber 200 `#FEF3C7` (badge); Amber 100 `#FFFBEB` (alert) | Amber 300 `#FDE68A` | Amber 700 `#92400E` |
| Bahaya | Red 200 `#FEE2E2` (badge, alert); Red 100 `#FEF2F2` | Red 300 `#FECACA`; Red 600 `#DC2626` (field error) | Red 700 `#991B1B`; Red 600 `#DC2626` (pesan error pada latar putih) |

**Lainnya:** Overlay/Scrim `#1D2530` pada 50% opasitas; White/15 `#FFFFFF` pada 15% (garis dan hover di atas sidebar biru).

`[Celah]` Frame "Warna" di Figma hanya menampilkan 24 swatch dan tidak memuat Green 100/600/700, Amber 200, Red 200, Neutral 150, Sky 100, Scrim, dan White/15, padahal semuanya dipakai di layar. Dokumen ini memperlakukan seluruh 30 variabel sebagai token resmi.

**Token semantik** `[Usulan]` (dipakai di kode agar pergantian palet tidak menyentuh view):

| Token semantik | Nilai | Dipakai untuk |
|---|---|---|
| `surface` | Neutral 0 | Kartu, field, modal, header |
| `surface-app` | Neutral 150 | Latar area kerja aplikasi |
| `surface-page` | Neutral 100 | Latar halaman publik dan auth |
| `surface-muted` | Neutral 200 | Header tabel, field nonaktif |
| `border` | Neutral 400 | Border kartu, tabel, topbar |
| `border-control` | Neutral 500 | Border field, checkbox, radio (lihat 2.6) |
| `text` | Neutral 800 | Teks utama |
| `text-muted` | Neutral 600 | Teks sekunder, bantuan, placeholder |
| `action` / `action-hover` | Blue 600 / Blue 700 | Tombol primer, tautan |
| `focus-ring` | Blue 600 pada 20% | Cincin fokus |
| `nav` | Blue 700 | Sidebar |

### 2.2 Tipografi

Keluarga huruf `[Figma]`: **Open Sans** untuk seluruh antarmuka (fallback Arial) dan **JetBrains Mono** (fallback Consolas) secara terbatas untuk token, kode, dan data teknis. Plus Jakarta Sans masih muncul di halaman Figma lama (`Revisi v1` dan `MOCKUP` seluruhnya; `Revisi v2` dan `Revisi v3` sebagian) dan merupakan generasi sebelumnya; font ini **tidak dipakai**.

Skala yang tertulis di dokumentasi Figma `[Figma]`:

| Gaya | Ukuran / tinggi baris / bobot | Pemakaian |
|---|---|---|
| Stat | 32 / 40 / 700 | Angka ringkasan pada kartu statistik |
| H1 | 34 / 44 / 700 | Judul halaman publik |
| H2 | 28 / 36 / 700 | Judul seksi |
| H3 | 22 / 30 / 700 | Judul kartu besar |
| H4 | 18 / 26 / 700 | Judul kartu |
| Body | 14 / 24 / 500 | Teks isi, metadata |
| Caption | 12 / 18 / 500 | Keterangan, teks bantuan |
| Label, Button, Nav | 14 / 20 / 600 | Label field, tombol, menu |

Pemakaian nyata di layar aplikasi terbaru (ukuran terbanyak: 14, 12, 13, 18, 15, 16, 30): judul halaman 30 Bold, judul kartu 18 sampai 20 Bold, isi dan sel tabel 13 sampai 14 Regular, label 13 sampai 14 SemiBold, badge 12 SemiBold, breadcrumb 13, input 16 pada spesimen.

`[Usulan]` penyelarasan:

| Hal | Keputusan | Alasan |
|---|---|---|
| Bobot Body dan Caption | **400 (Regular)**, bukan 500 | File Figma memakai Regular (4.737 teks) dan SemiBold (7.746), dan **tidak satu pun** teks Medium 500. Dokumentasi dan isi file berbeda |
| Tinggi baris Body | 24 untuk paragraf; 20 untuk sel tabel dan komponen padat | Tabel dan badge di layar memakai tinggi baris otomatis |
| Ukuran teks input | 16 px | Sesuai spesimen Figma dan mencegah zoom otomatis Safari iOS pada field < 16 px |
| Judul halaman aplikasi | 30 Bold (di luar skala) | Dipakai konsisten di layar; jadikan gaya "Judul halaman" |
| Bobot yang dimuat | 400, 600, 700 saja | ExtraBold 800 hanya muncul 126 kali; tidak perlu dimuat |

### 2.3 Spasi, Grid, Radius, dan Elevasi

Sistem berbasis 4 px `[Figma]`.

| Aspek | Nilai |
|---|---|
| Skala spasi (px) | 4, 8, 12, 16, 20, 24, 32, 40, 48, 64, 80, 96 |
| Grid desktop | 12 kolom, gap 24, container 1200 sampai 1280 |
| Radius | 0, 4, 8, 12, 16, full (999). **8 adalah default** kartu, field, dan tombol; 12 untuk kotak ringkasan dan callout; 16 untuk modal dan kartu besar; full untuk badge dan avatar |

| Elevasi | Nilai |
|---|---|
| Subtle | `0 1px 3px #0F172A14` |
| Raised | `0 4px 12px #0F172A1A` |
| Floating | `0 10px 24px #0F172A1F` |
| Modal `[Figma]` | `0 24px 48px -8px #0F1C3347` |
| Cincin fokus `[Figma]` | `0 0 0 3px #0751FF33` (tebal 3 px, Blue 600 pada 20%) |

**Breakpoint** `[Celah]`: Figma hanya memiliki dua lebar, **1440** (desktop) dan **390** (ponsel). `[Usulan]` memakai breakpoint bawaan Tailwind (640, 768, 1024, 1280). Sidebar berubah menjadi drawer di bawah 1024.

### 2.4 Ikon

Set ikon: **Lucide** `[Figma]`, kisi 24 px, garis 2 px. Ukuran pakai `[Usulan]`: 14 (di dalam badge), 18 (tombol, field), 20 (alert, menu), 24 (ilustrasi kecil). Ikon dikirim sebagai SVG inline lewat komponen Blade (CSP `img-src 'self' data:` tidak terpengaruh).

Ikon yang sudah ada di Figma: layout-dashboard, users, clipboard-list, file-search, book-open, award, circle-dashed, file-pen, circle-check, circle-x, circle-alert, clock, loader, alarm-clock, plus, pencil, download, search, rotate-ccw, arrow-left, paperclip, eye, undo-2, send, lock, chevron-down, chevron-right, calendar, inbox, triangle-alert, save, x, info, settings, shield-check, scroll-text, list-checks, building-2, user-plus, user-x.

### 2.5 Layout Aplikasi

| Konteks | Struktur | Nilai `[Figma]` |
|---|---|---|
| Publik | Header, konten, footer | Header 80 px putih, border bawah Neutral 400, padding horizontal 96. Footer Neutral 800, padding 56 / 96, tiga kelompok tautan, baris hak cipta |
| Auth | Panel kiri (area visual) dan panel kanan (form), masing-masing 720 px pada 1440 | Panel kanan menggulir bila form panjang (halaman Daftar) |
| Aplikasi desktop | Sidebar + area kerja | Sidebar 256 px, Blue 700, padding 24 / 20, jarak item 4. Topbar putih, border bawah Neutral 400. Konten padding sekitar 28 sampai 32 / 36 sampai 40, jarak antarseksi 20 sampai 24. Latar Neutral 150 |
| Aplikasi ponsel | Header + konten + drawer | Header 68 px, tombol menu 44 x 44, padding konten 16, jarak 18 |

`[Usulan]` nilai tunggal untuk yang tidak konsisten di Figma: tinggi topbar **64** (Figma: 64 dan 68), sidebar **256** (Figma: 256, dan 240 pada dua layar detail tugas Peserta), latar area kerja **Neutral 150** (Figma: 150 pada dashboard, 100 pada frame modal).

### 2.6 Aksesibilitas dan Kontras

Target PRD 9.5: WCAG 2.1 AA untuk halaman publik, auth, dan area Peserta. Rasio di bawah dihitung dengan rumus WCAG 2.1 dari hex pada file. Ambang: 4,5:1 untuk teks normal, 3:1 untuk teks besar dan komponen antarmuka.

**Pasangan yang lulus** (aman dipakai):

| Pasangan | Rasio |
|---|---|
| Neutral 800 di atas putih | 15,45 |
| Neutral 600 di atas putih / Neutral 150 | 5,74 / 5,35 |
| Putih di atas Blue 600 (tombol) / Blue 700 (sidebar, hover) | 5,79 / 7,97 |
| Blue 600 di atas putih (tautan) | 5,79 |
| Badge: Green 700 di Green 100 / Amber 700 di Amber 200 / Red 700 di Red 200 | 6,44 / 6,37 / 6,80 |
| Badge info: Blue 600 di Blue Tint / Blue 700 di Blue Tint | 4,97 / 6,84 |
| Badge netral: Neutral 600 di Neutral 200 | 5,04 |
| Pesan error: Red 600 di atas putih | 4,83 |

**Pasangan yang gagal atau marginal di Figma, beserta perbaikan `[Usulan]`:**

| # | Temuan | Rasio | Perbaikan |
|---|---|---|---|
| K-1 | Teks bantuan, placeholder, dan hak cipta footer memakai Neutral 500 di atas putih | 3,78 (gagal 4,5) | Pakai **Neutral 600** (5,74). Neutral 500 hanya untuk ikon dan teks nonaktif (teks nonaktif dikecualikan WCAG) |
| K-2 | Border field memakai Neutral 400 di atas putih | 1,50 (gagal 3:1 untuk batas komponen, WCAG 1.4.11) | Border field, checkbox, radio, dan toggle pakai **Neutral 500** (3,78). Border kartu dan pemisah tetap Neutral 400 (dekoratif) |
| K-3 | Teks error Red 600 di atas latar Red 200 (alert error) | 3,95 | Teks di dalam alert merah pakai **Red 700** (6,80); Red 600 hanya di atas putih |
| K-4 | Blue 500 `#2EA3F2` sebagai warna teks atau tautan | 2,75 di atas putih | Jangan pakai untuk teks atau tautan. Dekoratif saja |
| K-5 | Green 600 sebagai teks | 3,30 di atas putih | Teks hijau pakai Green 700. Green 600 hanya untuk border dan ikon |
| K-6 | Teks Neutral 400 dan nomor Blue 500 di atas Blue 600 (sampul dokumentasi) | 3,86 dan 2,10 | Hanya pada dokumentasi Figma, bukan antarmuka; abaikan |

**Aturan umum** `[Figma]`: cincin fokus 3 px terlihat di semua permukaan; kontrol interaktif minimal 44 x 44 px; ikon dan teks mendampingi warna pada setiap status. `[Usulan]` tambahan: tautan di dalam paragraf diberi garis bawah (tidak hanya berbeda warna); area staf (Admin, Pembimbing, SuperAdmin) boleh memakai tombol tabel 36 px karena dioptimalkan untuk desktop (PRD 9.5), tetapi publik, auth, dan Peserta tetap 44 px.

---

## 3. Komponen

Bagian ini merangkum komponen yang ada di Figma (halaman `02 UI Components and States`, `Aksi dan tautan`, `03 Content Patterns`, `04 Template Examples`) sebagai spesifikasi yang dapat dibangun.

### 3.1 Tombol dan Tautan

Dimensi `[Figma]`: tinggi 44 (45 di dalam modal), padding horizontal 18 (20 di modal), radius 8, jarak ikon-label 8, label 14 / 20 SemiBold, ikon 18.

| Varian | Latar | Teks | Border | Hover | Nonaktif |
|---|---|---|---|---|---|
| Primer | Blue 600 | Putih | – | Latar Blue 700 | Latar Neutral 200, teks Neutral 500 `[Usulan]` |
| Sekunder | Putih | Blue 600 | 1 px Blue 600 | Border dan teks Blue 700 | Latar Neutral 200, border Neutral 400 |
| Tersier (teks) | Transparan | Blue 600 `[Usulan]` | – | Teks Blue 700 | Teks Neutral 500 |
| Destruktif | Red 700 | Putih | – | `[Celah]` perlu token Red 800 | Seperti primer |
| Batal (di modal) | Putih | Neutral 600 | 1 px Neutral 400 | Latar Neutral 100 `[Usulan]` | – |

Fokus semua varian: cincin 3 px `focus-ring`. Setelah diklik, tombol kirim form dinonaktifkan (PRD 9.4). Tautan inline: Blue 600 SemiBold, tanda panah ↗ untuk tautan keluar `[Figma]`, garis bawah `[Usulan]`.

### 3.2 Form

| Elemen | Spesifikasi |
|---|---|
| Label | 14 SemiBold Neutral 800 di atas field, jarak 8. **Selalu terlihat**; placeholder tidak menggantikan label `[Figma]` |
| Input | Tinggi 48, radius 8, padding horizontal 14, teks 16 Regular, ikon 18 di dalam field bila perlu |
| State default | Latar putih, border 1 px `border-control` (K-2) |
| State fokus | Border 2 px Blue 600 dan cincin 3 px |
| State error | Border 1 px Red 600, ikon circle-alert 18, pesan 12 Red 600 di bawah field; pesan menjelaskan perbaikan, bukan hanya "salah" |
| State nonaktif | Latar Neutral 200, border Neutral 400, teks dan label Neutral 500 |
| Teks bantuan | 12 Regular, **Neutral 600** (K-1), di bawah field |
| Checkbox, radio | Kotak 20 px, jarak ke label 10 |
| Toggle | Tinggi 24, jarak ke label 10 |
| Textarea | Sama dengan input; tinggi minimal 96 |
| Upload berkas | Lihat 3.7 |

Form mengikuti bab 8.2 PRD untuk field dan aturan; nilai yang sudah diisi dipertahankan saat validasi gagal.

### 3.3 Badge Status

Satu komponen, lima varian warna `[Figma]`, dipakai untuk **semua** status di bab 4.

| Varian | Latar | Teks | Contoh label di Figma |
|---|---|---|---|
| `netral` | Neutral 200 | Neutral 600 | Belum, Draft, Ditutup, Peserta |
| `info` | Blue Tint | Blue 700 `[Usulan]` (Figma: 600 dan 700) | Administrasi, Pembimbing, Sisa 5 |
| `sukses` | Green 100 | Green 700 | Aktif, Diterima, Selesai, Dibuka |
| `peringatan` | Amber 200 | Amber 700 | Menunggu, Proses, Periksa, Revisi |
| `bahaya` | Red 200 | Red 700 | Ditolak, Nonaktif, Terlambat |

Bentuk: pil (radius full), tinggi **24** `[Usulan]` (Figma: 20 sampai 28), padding horizontal 10 sampai 12, teks 12 SemiBold, ikon Lucide 14 di kiri dengan jarak 6. Ikon wajib untuk status; chip peran (SuperAdmin, Administrasi, Pembimbing) boleh tanpa ikon.

### 3.4 Umpan Balik: Alert, Banner, Modal

**Alert** `[Figma]`: padding 16, jarak ikon-isi 12, ikon 20, judul dan isi berjarak 4. Empat varian:

| Varian | Latar | Border | Ikon |
|---|---|---|---|
| Info | Neutral 200 | Neutral 400 | info |
| Sukses | Green 100 | Green 600 | circle-check |
| Peringatan | Amber 100 | Amber 700 | triangle-alert |
| Error | Red 200 | Red 600 | circle-x |

`[Usulan]` radius 12 (Figma tidak menetapkan; kotak catatan di modal memakai 12). Teks di alert error memakai Red 700 (K-3).

**Banner** (informasi penting, maintenance, akses terbatas, masa tenggang): memakai warna varian alert, lebar penuh di atas konten. Banner masa tenggang (AC-12-2) adalah varian info dengan sisa hari dan tombol Unduh Lembar Nilai `[Usulan]`.

**Modal konfirmasi** `[Figma]`:

| Bagian | Spesifikasi |
|---|---|
| Lebar, bentuk | 520 px, radius 16, putih, border 1 px Neutral 400, elevasi Modal |
| Scrim | Neutral 800 pada 50% |
| Header | Padding 20 / 24; judul 20 Bold, subjudul 14 Neutral 600; tombol tutup di kanan (`[Usulan]` 44 x 44) |
| Pemisah | 1 px Neutral 200 di bawah header dan di atas footer |
| Isi | Padding 20 / 24, jarak 18. Memuat ringkasan objek (kotak radius 12, Neutral 100, border Neutral 400), field alasan, dan catatan dampak (kotak amber radius 12) |
| Footer | Padding 16 / 24, jarak tombol 12; Batal + tombol aksi (destruktif untuk tolak, batalkan, tetapkan selesai, nonaktifkan) |

Ini memenuhi PRD 9.4: aksi tidak dapat dibatalkan meminta konfirmasi lewat dialog yang menyebut dampaknya. Modal wajib menjebak fokus, menutup dengan Esc, dan mengembalikan fokus ke pemicu `[Usulan]`.

### 3.5 Navigasi

| Komponen | Spesifikasi `[Figma]` |
|---|---|
| Header publik | Logo bulat + nama; menu teks 14 SemiBold dengan garis bawah Blue 600 pada item aktif; "Masuk" (latar Neutral 200) dan "Daftar" (latar Blue 600), keduanya radius 8 |
| Sidebar | 256 px, Blue 700. Item tinggi 44, radius 8, padding 10 / 12, jarak ikon-teks 12; item aktif berlatar putih; di dasar ada penanda "Status Sistem" berborder putih |
| Topbar | Tinggi 64, putih, border bawah Neutral 400; kiri breadcrumb, kanan avatar (38 px, latar Blue Tint, inisial) dan nama pengguna |
| Menu akun | Overlay 220 px, menu per peran (Administrator, Pembimbing, Peserta) |
| Breadcrumb | 13 px; induk Neutral 600 Regular, halaman aktif Neutral 800 Bold, pemisah "/" |
| Pagination | Tinggi 44, jarak 8, ‹ 1 2 3 … 8 ›; keterangan "Menampilkan 1–4 dari 24 peserta". Ukuran halaman 25 (PRD 9.4) |
| Ponsel | Header 68 dengan tombol menu 44 x 44 dan tombol profil 44; menu keluar sebagai drawer |

Isi menu sidebar per peran mengikuti PRD 9.2.

### 3.6 Data: Tabel, Kartu, Timeline, Task Row

| Komponen | Spesifikasi `[Figma]` |
|---|---|
| Tabel | Pembungkus radius 8, border Neutral 400. Header baris 52, latar Neutral 200; baris 52, putih, garis bawah Neutral 400; padding sel horizontal 16. Toolbar di atas: pencarian, filter status, filter lain, ekspor (R2). Pagination di bawah |
| Kartu | Latar putih, border 1 px Neutral 400 (teks Figma menyebut Neutral 200, tetapi nilai di file adalah Neutral 400), radius 8, padding 24, urutan metadata, judul, ringkasan, aksi. Kartu ringkasan dashboard memakai radius 12 sampai 16 |
| Kartu statistik | Angka gaya Stat (32 / 40 / 700), label Caption Neutral 600 |
| Timeline | Item berisi judul, stempel waktu ("24 Sep 2026 · 09:10"), dan keterangan; dipakai untuk catatan Admin dan review Pembimbing. Menjadi sarana utama informasi peserta (NFR-U3) |
| Task row | Kolom Tugas, Kategori, Tenggat, Status (badge). Terlambat dan mendekati deadline mengikuti bab 4 |
| Daftar dokumen | Baris 76, ikon, nama, "PDF · 4,2 MB · Diperbarui ...", tombol Unduh |

### 3.7 Pola Khusus MagKom

| Pola | Spesifikasi dan penyesuaian ke PRD |
|---|---|
| Formulir pendaftaran bertahap | Stepper 3 langkah, tombol Kembali / Lanjut, ringkasan di samping. **Sesuaikan:** langkah dan field mengikuti PRD 8.2 dan 8.4 (jurusan, bulan mulai, durasi, berkas wajib per jenjang), bukan "Bidang minat" dan "Alasan memilih" dari contoh |
| Upload berkas | Area klik atau seret, daftar berkas dengan nama, tipe, ukuran, dan status (**Siap** hijau, **Periksa** amber). Teks bantuan "PDF, JPG, atau PNG, maksimal 2 MB per file" sudah sesuai PRD 8.4 (nilai 2 harus dibaca dari `berkas_maks_mb`, bukan ditulis tetap) |
| Kalender logbook | Grid mingguan, state kosong, terisi, hari ini, nonaktif, terkunci. **Sesuaikan:** aturan tanggal mengikuti BR-09 (hanya rentang magang, tidak di masa depan, mundur maksimal `logbook_maks_mundur_hari`); tanggal di luar aturan tampil terkunci. Tanpa fitur kehadiran (presensi di luar scope, PRD 2.4) |
| Baris rubrik | Kriteria, bobot, skor 0 sampai 100, status. **Sesuaikan:** kriteria, bobot, dan perhitungan mengikuti PRD 8.3 (nilai akhir 0 sampai 100 berbobot), bukan contoh "256 / 400". Status Draft/Final **tidak** boleh digambarkan sebagai terkunci: nilai dapat diubah dengan alasan (BR-10, D-06) |
| Accordion | Untuk informasi tambahan; hanya dibangun bila ada kebutuhan di PRD (lihat 7.2) |
| Mode hanya-lihat | SuperAdmin pada fungsi Pembimbing dan peserta pada masa tenggang: tombol tulis disembunyikan atau dinonaktifkan, disertai banner penjelas (PRD 9.3) |

### 3.8 State

Setiap daftar dan halaman wajib punya state berikut (PRD 12.2 butir 5): **memuat** (skeleton), **terisi**, **kosong** (menjelaskan langkah berikutnya, ikon inbox), **galat** (menyebut penyebab dan langkah berikutnya, tanpa detail teknis), **nonaktif**, dan **berhasil**. Halaman kustom untuk 403, 404, 419, 429 (dengan sisa waktu tunggu, TDD 8.2), dan 500 `[Celah]`.

---

## 4. Pemetaan Status PRD ke Tampilan

Satu-satunya sumber untuk label dan warna status. Label dan varian `[Figma]` bila ada layar yang menampilkannya; `[Usulan]` bila tidak. Ikon wajib (AC-DS-2).

| Enum (PRD 8.1) | Nilai | Label UI | Varian | Ikon | Asal |
|---|---|---|---|---|---|
| Status pendaftaran | `berkas_belum_lengkap` | Berkas belum lengkap | netral | circle-dashed | `[Usulan]` |
| | `diajukan` | Menunggu verifikasi | peringatan | file-search | `[Figma]` label dan warna |
| | `perlu_perbaikan` | Perlu perbaikan | peringatan | file-pen | `[Usulan]` (Figma: "Revisi", "Periksa") |
| | `waiting_list` | Waiting List | **info** | clock | `[Usulan]` (lihat 8, temuan T-4) |
| | `diterima` | Diterima | sukses | circle-check | `[Figma]` |
| | `ditolak` | Ditolak | bahaya | circle-x | `[Figma]` |
| | `dibatalkan` | Dibatalkan | netral | x | `[Usulan]` |
| Status penempatan | `aktif` | Aktif | sukses | circle-check | `[Figma]` |
| | `selesai` | Selesai | info | award | `[Usulan]` |
| | `mengundurkan_diri` | Mengundurkan diri | netral | rotate-ccw | `[Usulan]` |
| | `nonaktif` | Nonaktif | bahaya | lock | `[Figma]` |
| Status tugas | `belum` | Belum | netral | circle-dashed | `[Figma]` |
| | `proses` | Proses | peringatan | loader | `[Figma]` |
| | `selesai` | Selesai | sukses | circle-check | `[Figma]` |
| Turunan tugas (BR-16) | terlambat | Terlambat | bahaya | triangle-alert | `[Figma]` warna dan label |
| | mendekati deadline | Mendekati deadline | peringatan | alarm-clock | `[Usulan]` |
| Review progres | `menunggu` | Menunggu review | peringatan | clock | `[Figma]` |
| | `diterima` | Diterima | sukses | circle-check | `[Figma]` |
| | `perlu_revisi` | Perlu revisi | peringatan | undo-2 | `[Figma]` warna |
| Status logbook | `terkirim` | Terkirim | sukses | send | `[Usulan]` |
| | `perlu_revisi` | Perlu revisi | peringatan | undo-2 | `[Usulan]` |
| Status penilaian | `draft` | Draft | netral | pencil | `[Figma]` warna |
| | `final` | Final | sukses | circle-check | `[Usulan]` (bukan ikon gembok; BR-10) |
| Ketersediaan bulan | `tersedia` | Tersedia | sukses | circle-check | `[Usulan]` (Figma: "Dibuka") |
| | `tutup` | Tutup | netral | lock | `[Usulan]` (Figma: "Ditutup") |
| | `segera` | Segera dibuka | info | calendar | `[Usulan]` |
| Turunan kuota (AC-01-3) | sisa umum 0, status Tersedia | Kuota penuh | peringatan | triangle-alert | `[Usulan]` (Figma: "Penuh" netral, padahal pendaftaran Waiting List masih terbuka) |
| Jalur | `umum`, `khusus` | Umum, Khusus | netral | – | `[Usulan]`; **hanya di area staf**, tidak pernah di tampilan publik (BR-02) |
| Predikat | A sampai E | Sangat Baik, Baik, Cukup, Kurang, Sangat Kurang | info | award | `[Figma]` untuk A; B sampai E `[Usulan]`: seragam info karena predikat adalah hasil, bukan peringatan |
| Predikat belum ada | – | Predikat: belum tersedia | netral | – | `[Figma]` |
| Peran | `superadmin`, `admin`, `pembimbing` | SuperAdmin, Administrasi, Pembimbing | info | – | `[Figma]` |
| | `peserta` | Peserta | netral | – | `[Figma]` |

Label tampilan boleh berbeda dari nilai enum, tetapi **istilah di glosarium PRD** (Diajukan, Waiting List, Perlu Perbaikan) tetap dipakai pada dokumen, audit, dan pesan sistem. Penulisan di UI: "Waiting List" (huruf besar L) seperti PRD.

---

## 5. Aturan Wajib dan Acceptance Criteria

| ID | Acceptance criteria | Cara verifikasi |
|---|---|---|
| AC-DS-1 | Jika sebuah view Blade atau berkas CSS memakai nilai warna heksadesimal literal atau atribut `style=`, maka pemeriksaan CI gagal | Test yang memindai `resources/` dengan pola `#[0-9A-Fa-f]{3,8}` dan `style=` (pengecualian: berkas token dan templat PDF) |
| AC-DS-2 | Jika sebuah status ditampilkan, maka tampil label teks, ikon, dan warna sekaligus, dan makna tetap terbaca tanpa warna | Test komponen dan uji manual dengan filter skala abu-abu |
| AC-DS-3 | Jika sebuah enum status ditambah atau diubah, maka komponen badge wajib punya label, varian, dan ikon untuknya | Feature test yang mengiterasi `cases()` setiap enum status dan memastikan pemetaan tersedia |
| AC-DS-4 | Jika halaman publik, auth, atau Peserta dipindai Lighthouse atau axe, maka tidak ada pelanggaran kontras atau label pada tingkat serius | Pemindaian di CI atau staging; pasangan warna hanya dari 2.6 |
| AC-DS-5 | Jika field gagal validasi, maka muncul border merah, ikon, dan pesan berbahasa Indonesia di dekat field yang dihubungkan dengan `aria-describedby`, dan nilai yang sudah diisi dipertahankan | Feature test dan UAT-2 |
| AC-DS-6 | Jika Peserta, halaman auth, atau halaman publik dibuka pada lebar 360 px, maka tidak ada gulir horizontal pada badan halaman dan semua kontrol berukuran minimal 44 x 44 px | Uji manual dan Lighthouse (PRD 9.5, A-26) |
| AC-DS-7 | Jika aksi tidak dapat dibatalkan (tolak, batalkan, tetapkan selesai, nonaktifkan) dipicu, maka muncul modal konfirmasi pola 3.4 yang menyebut dampaknya, dengan fokus terjebak di dalam modal dan Esc menutupnya | Test browser atau UAT-3 dan UAT-7 |
| AC-DS-8 | Jika komponen publik (kartu bulan, kartu kuota) dirender, maka komponen hanya menerima sisa kuota umum dan tidak ada data slot khusus pada props, view, atau markup | Feature test kebocoran (BR-02, R-08) |
| AC-DS-9 | Jika penempatan dalam masa tenggang atau pengguna adalah SuperAdmin pada fungsi Pembimbing, maka semua tombol tulis tersembunyi atau nonaktif dan banner hanya-lihat tampil | Test tampilan per peran (AC-08-6, AC-12-2) |
| AC-DS-10 | Jika sebuah daftar atau halaman dimuat, maka tersedia state memuat, kosong, dan galat | Daftar periksa Definition of Done (PRD 12.2 butir 5) |
| AC-DS-11 | Jika halaman dimuat di produksi, maka tidak ada permintaan ke host selain `'self'` untuk font, CSS, atau skrip | Uji manual jaringan peramban dan laporan CSP |

---

## 6. Implementasi

Detail kode ada di TDD; bab ini hanya titik sambung desain ke stack (Blade, Vite, Tailwind, Alpine.js, dompdf).

**Tailwind** (ilustrasi; asumsi Tailwind 3 bawaan Breeze 1.x). Pakai `theme.colors` (**bukan** `extend`) agar warna di luar palet tidak dapat dipakai tanpa sengaja.

```js
// tailwind.config.js (ilustrasi)
export default {
  content: ['./resources/views/**/*.blade.php', './resources/js/**/*.js'],
  theme: {
    colors: {
      transparent: 'transparent', white: '#FFFFFF', black: '#05070B',
      neutral: { 0: '#FFFFFF', 100: '#F8FAFC', 150: '#F5F7FB', 200: '#EEF0F5', 400: '#D0D3D9',
                 500: '#7A8494', 600: '#666666', 700: '#333333', 800: '#1D2530', 900: '#05070B' },
      blue:  { tint: '#E6EEFF', line: '#B3C8FF', 500: '#2EA3F2', 600: '#0751FF', 700: '#0641CC' },
      sky:   { 100: '#E0F2FE' },
      green: { 100: '#E8F7EE', 600: '#16A34A', 700: '#166534' },
      amber: { 100: '#FFFBEB', 200: '#FEF3C7', 300: '#FDE68A', 700: '#92400E' },
      red:   { 100: '#FEF2F2', 200: '#FEE2E2', 300: '#FECACA', 600: '#DC2626', 700: '#991B1B' },
    },
    fontFamily: {
      sans: ['"Open Sans"', 'Arial', 'sans-serif'],
      mono: ['"JetBrains Mono"', 'Consolas', 'monospace'],
    },
    fontSize: {
      stat:    ['2rem',     { lineHeight: '2.5rem',   fontWeight: '700' }],
      h1:      ['2.125rem', { lineHeight: '2.75rem',  fontWeight: '700' }],
      h2:      ['1.75rem',  { lineHeight: '2.25rem',  fontWeight: '700' }],
      h3:      ['1.375rem', { lineHeight: '1.875rem', fontWeight: '700' }],
      h4:      ['1.125rem', { lineHeight: '1.625rem', fontWeight: '700' }],
      body:    ['0.875rem', { lineHeight: '1.5rem' }],
      caption: ['0.75rem',  { lineHeight: '1.125rem' }],
      label:   ['0.875rem', { lineHeight: '1.25rem',  fontWeight: '600' }],
    },
    borderRadius: { none: '0', sm: '4px', md: '8px', lg: '12px', xl: '16px', full: '9999px' },
    boxShadow: {
      subtle: '0 1px 3px #0F172A14', raised: '0 4px 12px #0F172A1A',
      floating: '0 10px 24px #0F172A1F', modal: '0 24px 48px -8px #0F1C3347',
      focus: '0 0 0 3px #0751FF33', none: 'none',
    },
  },
};
```

Skala spasi Figma (4 sampai 96) sama dengan skala bawaan Tailwind (1, 2, 3, 4, 5, 6, 8, 10, 12, 16, 20, 24), jadi tidak perlu konfigurasi. Catatan: tampilan bawaan Breeze memakai kelas `gray-*` dan `indigo-*`; view auth bawaan perlu ditata ulang ke token ini.

**Font.** Open Sans (400, 600, 700) dan JetBrains Mono (400, 700) **wajib di-host sendiri** lewat Vite sebagai `woff2`, subset latin, `font-display: swap`. CSP produksi di TDD 8.3 (`default-src 'self'`, `style-src 'self'`) memblokir Google Fonts.

**Tanpa `style=` inline.** CSP `style-src 'self'` menolak atribut inline. Lebar bar progres dan sejenisnya memakai elemen `<progress>` atau kelas utilitas bertingkat, bukan `style="width: 60%"`. Build Alpine yang dipakai harus build CSP (TDD 2).

**Komponen status.** Satu komponen Blade (misalnya `<x-status-badge :status="$enum" />`) membaca label, varian, dan ikon dari satu pemetaan bab 4. Dengan begitu AC-DS-3 dapat diuji otomatis.

**PDF (Lembar Nilai, Surat Keterangan).** dompdf tidak mendukung flex dan grid penuh. Templat PDF dibuat terpisah (`resources/views/pdf`) dengan tabel dan warna dari palet ini; embed Open Sans (TTF) agar tampil sama, dengan Arial sebagai cadangan. Format isi mengikuti PRD 8.5.

---

## 7. Cakupan Figma terhadap PRD

### 7.1 Halaman PRD (bab 9.2 dan 9.3) yang belum ada di Figma `[Celah]`

| Halaman atau state | Dasar PRD |
|---|---|
| Kebijakan Privasi | 9.2, 9.6, NFR-R1 |
| Verifikasi email; lupa dan reset password; ganti password wajib untuk staf | 9.2, 9.3, BR-15 |
| Halaman galat 403, 404, 419, 429, 500 | 9.3 |
| Slot khusus (menu internal) | 9.3, F-06 |
| Master data jurusan dan bulan magang | F-11 (hanya Bidang dan Rubrik yang ada) |
| Arsip peserta | 9.2, BR-11 |
| Banner masa tenggang dan mode baca-saja pada layar Peserta | 9.3, AC-12-2 |
| Logbook terkunci sebelum peserta ditempatkan | 9.3 |
| Label "mendekati deadline" pada daftar tugas | 9.3, BR-16 |
| Kuota penuh pada landing page | AC-01-3 |

**Responsif.** PRD mewajibkan publik, auth, dan Peserta responsif dari 360 px (A-26), tetapi layar ponsel (390 px) hanya ada untuk **Pembimbing** (dashboard, drawer, review progres, penilaian). Tidak ada layar ponsel untuk Landing, Login, Daftar, dan area Peserta, dan tidak ada desain tablet untuk area staf.

### 7.2 Konten di Figma yang **tidak boleh dibangun** (di luar PRD)

| Konten di Figma | Alasan |
|---|---|
| Toggle "Notifikasi email aktif/nonaktif" dan pilihan "menerima pengumuman" | Tanpa notifikasi apa pun (BR-14, D-02) |
| Menu publik "Lowongan, Alur Magang, Pengumuman, FAQ, Kontak"; kartu Berita, Infografis, Video, Layanan; "Satu Data", "Lapor Sleman", "Sleman TV" | Halaman publik PRD hanya landing page (jurusan, bulan, status, sisa kuota) dan Kebijakan Privasi (F-01) |
| Kolom "Bidang" wajib dan "Bidang minat" pada pendaftaran | Bidang opsional pada versi ini (A-03) |
| Simpan draf otomatis tiap 2 menit; tahap "Pilih lowongan" | Tidak ada di F-03; status `berkas_belum_lengkap` sudah menjadi draf |
| Menandai kehadiran pada kalender logbook | Presensi di luar scope (PRD 2.4) |
| Status "Diproses", "Disetujui", "Revisi" untuk pendaftaran | Nilai status PRD ada di 8.1 |
| Rubrik 4 kriteria (30/25/20/25) dan "Total skor 256 / 400" | Rubrik awal 5 kriteria dan nilai akhir 0 sampai 100 (8.3) |
| Draft/Final digambarkan "terkunci" | Nilai dapat diubah (BR-10, D-06) |
| Contoh modal "dihapus permanen, tidak dapat dipulihkan" | Data tidak dihapus (BR-11); pola modal boleh dipakai, teksnya tidak |
| Tombol Export CSV pada toolbar tabel | Hanya R2 (F-17) |
| Bagian Figma "DI LUAR PRD v1.5 - perlu keputusan" (tambah, hapus, bulk action, ekspor data peserta oleh SuperAdmin; 2FA empat langkah) dan bagian "ARSIP" | Ditandai file sendiri sebagai tidak dipakai; SuperAdmin hanya-lihat pada fungsi operasional (A-05) |
| Format tanggal "24 Sep 2026" | PRD 9.4 menetapkan "7 Oktober 2026"; singkatan bulan hanya boleh di tabel sempit `[Usulan]`, menunggu keputusan |

### 7.3 Layar yang sudah ada dan dapat dijadikan acuan

Landing Publik, Login, Daftar, Pendaftaran Magang; Peserta (dashboard sebelum dan sesudah ditempatkan, pendaftaran dengan status menunggu, ditolak, dan waiting list, tugas dan detail tugas, logbook, nilai akhir, profil); Admin (dashboard, kuota, antrean verifikasi, detail pendaftar, penempatan, waiting list, modal tolak, waiting list, dan penempatan, laporan internal); Pembimbing (dashboard, peserta bimbingan, tugas, review progres dan logbook, penilaian draft, final, dan diblokir, lembar nilai, versi ponsel); SuperAdmin (pengguna, role, rubrik, audit log, konfigurasi bidang dan pengaturan umum, buat akun staf, serta state memuat, kosong, dan galat).

---

## 8. Temuan Kualitas File Figma

Temuan yang perlu diselesaikan desainer agar serah-terima ke developer tidak menebak-nebak.

| # | Temuan | Dampak | Tindakan |
|---|---|---|---|
| T-1 | **Variabel warna baru tidak terpasang.** Dari ribuan isian warna di layar terbaru, tidak ada yang terikat ke variabel `MagKom/Warna`; semuanya hex literal | Perubahan palet tidak merambat; developer tidak bisa menelusuri token dari desain | Pasang variabel pada seluruh fill, stroke, dan teks |
| T-2 | **Bagian Pembimbing masih memakai token lama.** Sekitar 430 isian terikat ke variabel `MagKom Brand` (primer `#1B4FA8`, aksen `#0EA5E9`), tanpa `MagKom/Warna`; 32 layar Pembimbing terdampak. Tombol primer Pembimbing jadi berbeda dari Admin dan Peserta (`#0751FF`) | Dua biru primer pada satu produk | Migrasikan ke Blue 600 / Blue 700. Sampai itu selesai, developer memakai token bab 2 dan mengabaikan hex lama |
| T-3 | **Beberapa generasi desain dalam satu file**: `Revisi v1` dan `MOCKUP` (Plus Jakarta Sans, palet lama), `Revisi v2` dan `Revisi v3` (campuran Plus Jakarta Sans dan Open Sans), dan `testing` (Open Sans, palet baru, memuat Design System v1.0) | Salah ambil rujukan | Tandai `testing` sebagai halaman sumber; arsipkan sisanya |
| T-4 | **Waiting List berwarna beda**: biru di layar Admin, amber di layar Peserta | Status yang sama tampak berbeda | Ikuti bab 4 (info). Perbarui layar Peserta |
| T-5 | **Dokumentasi tipografi berbeda dari isi**: tertulis bobot 500, file tidak memakai 500 | Bobot salah dimuat | Ikuti 2.2 |
| T-6 | **Border kartu**: teks tertulis Neutral 200, nilai di file Neutral 400 | Tafsir ganda | Ikuti 3.6 (Neutral 400) |
| T-7 | **Dimensi tidak konsisten**: topbar 64 atau 68; sidebar 256 atau 240; tinggi badge 20 sampai 28; tinggi input 45 atau 48; tombol 44 atau 45; latar 100 atau 150 | Developer harus memilih | Ikuti nilai tunggal di bab 2 dan 3 |
| T-8 | **Kontras** (K-1 sampai K-5 di 2.6) | Gagal WCAG AA pada teks bantuan, placeholder, border field | Terapkan perbaikan 2.6 pada Figma |
| T-9 | **Versi dokumen berbeda**: layar menyebut "PRD v1.5", sedangkan PRD ini v1.1 | Penomoran tidak selaras; sulit memastikan layar mengikuti PRD yang mana | Lihat OQ-15 |
| T-10 | Frame "Warna" tidak memuat seluruh token (2.1) | Token tersembunyi | Lengkapi frame |

---

## 9. Pertanyaan Terbuka

Melanjutkan penomoran PRD 11.4 (OQ-13 sudah ada). Setiap pertanyaan punya pemilik, tenggat, dan nilai bawaan.

| ID | Pertanyaan | Pemilik | Tenggat | Nilai bawaan sampai diputuskan |
|---|---|---|---|---|
| OQ-14 | Setujui perbaikan kontras 2.6 (K-1 sampai K-5) dan terapkan di Figma | Desainer dan Product Owner | Sebelum desain final | Pakai perbaikan 2.6 |
| OQ-15 | Selaraskan penomoran: Figma menyebut "PRD v1.5", PRD terbaru berversi 1.1. Apakah ada versi PRD yang belum dibagikan? | Product Owner dan desainer | Sebelum kickoff | PRD 1.1 berlaku untuk perilaku |
| OQ-16 | Pasang variabel `MagKom/Warna` ke semua layar dan migrasikan 32 layar Pembimbing dari token lama (T-1, T-2) | Desainer | Sebelum handoff gelombang 1 | Developer memakai token bab 2 |
| OQ-17 | Warna badge Waiting List: biru (usulan) atau amber | Desainer dan Product Owner | Sebelum desain final | Biru (info) |
| OQ-18 | Konten template di luar PRD (7.2): hapus dari Figma atau minta masuk scope lewat Product Owner | Product Owner | Sebelum gelombang 1 | Tidak dibangun |
| OQ-19 | Desain yang belum ada: halaman 7.1, layar ponsel Landing, Auth, dan Peserta, serta tablet area staf | Desainer | Sebelum gelombang 1 (halaman) dan sebelum desain final (responsif) | Developer menurunkan dari token dan pola ponsel Pembimbing; ditinjau desainer |
| OQ-20 | Format tanggal: penuh ("7 Oktober 2026") atau singkatan bulan di tabel sempit ("7 Okt 2026") | Product Owner | Sebelum desain final | Penuh di mana pun; singkatan hanya di tabel |
| OQ-21 | Token Red 800 untuk hover tombol destruktif | Desainer | Sebelum desain final | Gelapkan Red 700 sebesar 10% |

---

## 10. Integrasi ke PRD dan TDD

Perubahan kecil yang disarankan agar dokumen ini tertaut. Tidak ada aturan bisnis yang berubah.

**PRD, bab 9.1 (Sumber Desain):** tambahkan baris pada tabel.

```markdown
| Design system | `DS-MagKom.md` (token, komponen, pemetaan status, aturan wajib AC-DS-1 sampai 11) | Berlaku untuk tampilan; bila bertentangan dengan Figma, DS-MagKom.md dipakai sampai Figma diperbarui |
```

**PRD, bab 9.5 (Aksesibilitas):** tambahkan satu kalimat: "Pasangan warna yang boleh dipakai ada di `DS-MagKom.md` bab 2.6; perbaikan kontras K-1 sampai K-5 wajib diterapkan."

**PRD, bab 11.4 dan Lampiran A:** salin OQ-14 sampai OQ-21 ke 11.4. Tambahkan baris di A.1: "Bab 9: ditautkan ke Design System; ditambah AC-DS-1 sampai 11."

**PRD, bab 12.2 (Definition of Done):** butir 5 diperluas: "... empty state dan error state tersedia, dan komponen mengikuti `DS-MagKom.md`."

**TDD, bab 2 (Stack):** pada baris Frontend tambahkan "Token dan komponen: `DS-MagKom.md`; font di-host sendiri (CSP)". **TDD bab 11 (Strategi Pengujian):** tambahkan AC-DS-1, AC-DS-3, dan AC-DS-8 sebagai test otomatis.

---

## Lampiran: Rujukan Node Figma

Untuk menelusuri sumber nilai pada file `MAGKOM Kominfo Sleman`, halaman `testing`, bagian "Design system dan overlay".

| Frame | ID node | Isi |
|---|---|---|
| 01 Foundations | `542:93429` | Sampul, aksesibilitas (01.4) |
| Warna | `542:95114` | Palet (01.1) |
| Tipografi | `542:95068` | Skala huruf (01.2) |
| Tata letak | `542:94975` | Grid, spasi, radius, bayangan (01.3) |
| 02 UI Components and States | `542:93481` | Navigasi, form, feedback, kartu, data, footer (02.1, 02.3 sampai 02.7) |
| Aksi dan tautan | `542:94882` | Tombol, tautan, badge, checkbox, radio, toggle (02.2) |
| 03 Content Patterns | `542:93766` | Pola form, tabel, timeline, task, logbook, rubrik, accordion, uploader, modal, banner, state |
| 04 Template Examples | `542:94358` | Layout Public, Auth, App, Wizard |
| Contoh modal aplikasi | `542:89161` | Admin - Tolak Pendaftar (Modal) |
| Contoh shell aplikasi | `542:85228` | Dashboard Administrasi Kominfo |
| Contoh ponsel | `542:92823` | Pembimbing - Dashboard - Ponsel |
