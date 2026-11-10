# Rencana Kerja (Plan): Redesign Halaman Logbook Mingguan Peserta Sesuai Mockup Referensi

**Tanggal Dibuat:** 10 November 2026  
**Status:** Selesai Diimplementasikan (Verified)  
**Tujuan Dokumen:** Perencanaan terstruktur perombakan visual antarmuka menu **Logbook & Jurnal Mingguan Peserta** (`/peserta/logbook`) agar presisi sesuai desain referensi pada mockup gambar (`media_1794303695703_71c03c60.jpg`) menggunakan identitas warna resmi MagKom Sleman (`#003566`, `#01347C`, `#02275D`, `#FFC300`, `#FFCB05`, `#FFFFFF`, `#212529`).

---

## 1. Tujuan & Ringkasan Perubahan

Melakukan redesign menyeluruh antarmuka halaman Logbook Peserta (`resources/views/peserta/logbook/index.blade.php`) dan stylesheet pendukungnya (`resources/css/pages/peserta/logbook-index.css`) agar tampil identik, rapi, dan pixel-perfect dengan gambar referensi:

1. **Hero Banner Sleman Navy dengan Widget Status Emas**:
   - **Kiri**:
     - Judul besar: Nama Bidang Penempatan (contoh: *Bidang Aplikasi Informatika (Aptika)*).
     - Dua pill box transparan berdampingan:
       - `PEMBIMBING` &rarr; *Budi Santoso, S.Kom (Pembimbing Lapangan)*
       - `PERIODE` &rarr; *19 Okt 2026 s/d 17 Jan 2027*
   - **Kanan**:
     - Kartu widget emas Sleman mencolok (`background: #FFC300; border-radius: 16px;`):
       - Label atas: `MINGGU BERJALAN SAAT INI`
       - Headline besar: `Minggu ke-X` (angka tebal deep navy `#003566`)
       - Subtitle: `dari total 13 minggu`

2. **Card "Progres Pelaporan Logbook Mingguan"**:
   - Container kartu putih modern (`border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px 28px;`).
   - Header: Judul kartu di kiri dan badge pill biru muda di kanan (`X dari 13 Minggu Terisi (Y%)`).
   - Progress bar horizontal: Track abu-abu lembut dengan isian batang deep navy `#003566`.
   - Grid Stepper Mingguan (Minggu 1 s/d 13):
     - **Sudah diisi**: Pill navy solid `#003566` berikon centang putih `✓ Minggu X`.
     - **Belum diisi**: Pill berlatar kuning lembut ber-border kuning emas `📅 Minggu X`.
     - **Minggu berjalan**: Pill solid emas `#FFC300` ber-border hitam/navy tegas `⏳ Minggu X`.
     - **Mendatang**: Pill putih ber-border abu-abu lembut `📅 Minggu X`.
   - Legenda status di bagian bawah (Sudah diisi, Belum diisi, Minggu berjalan, Mendatang).

3. **Card "Riwayat Jurnal Mingguan"**:
   - Container kartu putih modern dengan header:
     - Judul: `Riwayat Jurnal Mingguan`.
     - Kanan: Pill count `Total: X Laporan` & Tombol aksi emas Sleman **`+ Isi Logbook Mingguan`** (`#FFC300` dengan font bold).
   - Search bar terintegrasi: Field pencarian elegan dengan ikon kaca pembesar `Cari minggu ke- (contoh: 1, 2) atau kata kunci kegiatan...`.
   - Item Kartu Riwayat Logbook:
     - Kiri: Badge squircle navy dengan teks `MG` dan angka minggu besar (misal `MG 2`).
     - Tengah:
       - Judul: `Logbook Minggu ke-X`.
       - Meta: `📅 Periode: [Rentang Tanggal] • Dilaporkan: [Tanggal]`.
       - Snippet teks kegiatan 1-2 baris dengan pemotongan teks rapi (`...`).
     - Kanan:
       - Badge status navy capsule `✓ Terkirim` / `✓ Disetujui` / `Perlu Revisi`.
       - Tombol outline navy **`✏ Edit`**.
       - Tombol chevron accordion **`[ v ]`** untuk membuka/menutup rincian kegiatan dan lampiran berkas/link.

4. **Preservasi 100% Kontrak Bisnis & Testing**:
   - Mempertahankan fungsionalitas modal tambah (`modalTambahLogbook`) dan edit (`modalEditLogbook`).
   - Mempertahankan alur upload PDF dan input link HTTPS.
   - Mempertahankan fungsi pencarian JavaScript realtime (`handleSearchLogbook`).
   - Menjaga kelulusan seluruh assertion test feature (`PesertaLogbookRevampTest` dan `PesertaClassroomTugasLogbookTest`).
   - Seluruh styling ditulis terpusat di `resources/css/pages/peserta/logbook-index.css` tanpa inline style statis di Blade.

---

## 2. Asumsi & Batasan Teknis

1. **Color Palette Resmi MagKom Sleman**:
   - Deep Navy Primary: `#003566`, `#01347C`, `#02275D`
   - Sleman Gold Accent: `#FFC300`, `#FFCB05`
   - Neutral Slate & Border: `#FFFFFF`, `#F8FAFC`, `#E2E8F0`, `#CBD5E1`, `#64748B`, `#1E293B`, `#0F172A`
   - Status Terlambat/Revisi: `#DC2626`, `#FEE2E2`
   - Status Berjalan/Warning: `#FEF9C3`, `#854D0E`, `#FFC300`
2. **Data & Variabel yang Tersedia di View**:
   - `$penempatan`: Model penempatan aktif beserta relasi `bidang` dan `pembimbing`.
   - `$mingguAktif`: Angka minggu magang saat ini (1 s/d 13).
   - `$totalMinggu`: Total minggu magang (default 13).
   - `$totalTerisi` & `$persentaseTerisi`: Total logbook yang telah dilaporkan dan persentasenya.
   - `$daftarMinggu`: Array berisi 13 minggu magang lengkap dengan `label_periode`, objek `logbook`, dan statusnya.
   - `$logbooks`: Paginasi model `Logbook` milik peserta.
3. **Penyelarasan Blade & CSS**:
   - Perubahan Blade murni untuk struktur semantik komponen kartu, hero, stepper, dan stream logbook.
   - Modal tambah/edit, input file tersembunyi, dan event handler tetap dipelihara untuk backward compatibility dengan modul JS.

---

## 3. Pendekatan Teknis (Evaluasi Opsi)

### Opsi A (Direkomendasikan): Semantik Bersih Selaras Desain Mockup Gambar
- **Deskripsi**:
  - Mengubah struktur layout Blade pada hero banner, tracker card, dan riwayat jurnal menjadi struktur modern sesuai gambar referensi.
  - Memperbarui styling di `resources/css/pages/peserta/logbook-index.css` dengan token Sleman, layout flex split, stepper 4-warna, dan item kartu squircle `MG`.
  - Mempertahankan modal form tambah & edit yang sudah ada tanpa merusak logic validasi dan upload.
- **Kelebihan**:
  - 100% identik dengan gambar mockup user.
  - Sangat bersih, mudah dirawat, dan responsif di mobile/tablet.
  - Mematuhi semua aturan arsitektur MAGKOM.
- **Kekurangan**: Perubahan markup container Blade diajukan dan dimintakan persetujuan terlebih dahulu (terpenuhi melalui plan ini).

### Opsi B: Hanya Override CSS pada Struktur Blade Lama
- **Deskripsi**: Mencoba mengubah tampilan banner dan stream card hanya lewat CSS tanpa mengubah markup HTML.
- **Kekurangan**: Struktur banner lama tidak memiliki container flex untuk widget kartu kuning di sisi kanan, stepper minggu tidak memiliki legend 4-status, dan badge `MG` di kartu riwayat tidak dapat diposisikan presisi.
- **Keputusan**: Dipilih **Opsi A**.

---

## 4. Rincian Perubahan File

| File | Tipe Perubahan | Deskripsi Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/logbook/index.blade.php` | Markup Semantik | 1. Hero banner: Tampilkan judul bidang, 2 pill info (`PEMBIMBING` & `PERIODE`), dan kartu widget emas `Minggu ke-X`.<br>2. Tracker card: Header progress text, progress bar navy, 13 stepper pill (4 state visual), dan legend 4 warna.<br>3. Riwayat jurnal: Header ber-badge total & tombol emas `+ Isi Logbook Mingguan`, search bar full-width, kartu stream ber-badge squircle `MG`, meta periode, snippet kegiatan, status capsule, tombol outline `Edit`, dan chevron accordion.<br>4. Mempertahankan modal tambah/edit dan sub-modal upload. |
| `resources/css/pages/peserta/logbook-index.css` | Styling | 1. Token warna resmi Sleman (`#003566`, `#FFC300`).<br>2. Hero banner split flex + gold status card.<br>3. Modern tracker container + horizontal progress bar navy.<br>4. 4 Visual state week pills (navy done, amber unfilled, gold current, gray upcoming) + legend.<br>5. Riwayat jurnal container + search box + stream card ber-badge squircle `MG`.<br>6. Tombol emas `+ Isi Logbook Mingguan` & tombol outline `Edit`.<br>7. Dukungan Dark Mode komprehensif & responsive breakpoints. |
| `resources/js/pages/peserta/logbook-index.js` | Interaksi JS | Verifikasi dan pastikan interaksi pencarian, accordion toggle kartu, dan trigger modal bekerja mulus dengan markup baru. |

---

## 5. Langkah-Langkah Implementasi Bertahap

### Fase 1: Styling Terpadu di `resources/css/pages/peserta/logbook-index.css`
- [x] **Langkah 1.1**: Terapkan CSS variables token resmi Sleman MagKom di root stylesheet.
- [x] **Langkah 1.2**: Implementasikan styling `.banner-penempatan` dengan split layout (info bidang/pembimbing di kiri dan widget kartu emas `.banner-gold-widget` di kanan).
- [x] **Langkah 1.3**: Implementasikan styling `.progress-tracker-card`:
  - Header dengan judul tegas dan badge persentase biru muda.
  - Progress bar navy horizontal (`.progress-bar-fill`).
  - Stepper 13 minggu dengan 4 variasi kelas: `.is-done` (navy), `.is-unfilled` (kuning lembut), `.is-active-week` (emas solid), dan `.is-upcoming` (putih/abu-abu).
  - Styling legenda 4 warna di bagian bawah.
- [x] **Langkah 1.4**: Implementasikan styling `.riwayat-jurnal-card` dan item kartu `.weekly-card`:
  - Header dengan badge total count dan tombol emas `.btn-tambah-logbook-gold`.
  - Search box modern ber-border bersih.
  - Item kartu: squircle badge navy `.week-number-squircle` (`MG` di atas, angka di bawah), baris judul, meta periode, snippet kegiatan, badge status navy capsule, tombol outline `Edit`, dan chevron toggle.
  - Detail accordion content yang rapi untuk teks kegiatan dan lampiran berkas/link.
- [x] **Langkah 1.5**: Tambahkan rules responsive breakpoint (< 1024px dan < 768px) serta Dark Mode lengkap.

### Fase 2: Penyelarasan Markup di `resources/views/peserta/logbook/index.blade.php`
- [x] **Langkah 2.1**: Susun ulang struktur Hero Banner:
  - Judul bidang besar, 2 box info transparan (`PEMBIMBING` & `PERIODE`), dan kartu widget emas sisi kanan.
- [x] **Langkah 2.2**: Susun ulang struktur Tracker Card:
  - Header tracker, progress bar navy, 13 week chips sesuai logika 4-status, dan baris legenda.
- [x] **Langkah 2.3**: Susun ulang container Riwayat Jurnal Mingguan:
  - Action bar atas ber-badge total dan tombol emas `+ Isi Logbook Mingguan`.
  - Search input box.
  - Kartu penugasan ber-squircle `MG`, judul, meta, preview kegiatan, badge status, tombol outline `Edit`, dan chevron accordion toggle.
  - Pertahankan modal tambah (`modalTambahLogbook`) dan modal edit (`modalEditLogbook`).

### Fase 3: Kompilasi Aset & Pengujian Quality Gate
- [x] **Langkah 3.1**: Jalankan kompilasi aset Vite (`npm run build`).
- [x] **Langkah 3.2**: Jalankan pengujian feature test suite:
  ```powershell
  & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test tests/Feature/PesertaLogbookRevampTest.php tests/Feature/PesertaClassroomTugasLogbookTest.php
  ```
- [x] **Langkah 3.3**: Jalankan linter Laravel Pint (`./vendor/bin/pint --test`).
- [x] **Langkah 3.4**: Verifikasi visual di browser pada `http://127.0.0.1:8000/peserta/logbook`.

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - `PesertaLogbookRevampTest`: Memastikan render judul halaman, string riwayat utama, ketiadaan tombol lama (buka/tutup semua), keberadaan modal compact dan input file/link tetap lulus 100%.
   - `PesertaClassroomTugasLogbookTest`: Memastikan feed logbook, submit logbook mingguan, dan pencegahan duplikasi minggu tetap berjalan tanpa regresi.
2. **Visual Inspection**:
   - Memastikan widget emas minggu berjalan di banner atas tampil tajam dan kontras.
   - Memastikan 13 chip minggu memiliki 4 warna status yang tepat sesuai data aktual peserta.
   - Memastikan squircle `MG` di setiap baris riwayat tampil simetris dan rapi.
   - Memastikan tombol emas `+ Isi Logbook Mingguan` dan tombol outline `Edit` membuka modal dengan benar.

---

## 7. Analisis Risiko & Mitigasi

| Risiko Potensial | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Kegagalan Test Assertion** | Pipeline test merah | Mempertahankan seluruh teks kunci (`Logbook & Jurnal Mingguan Peserta`, `Riwayat Jurnal Mingguan`, `+ Isi Logbook Mingguan`) dan ID modal (`modalTambahLogbook`, `modalEditLogbook`). |
| **Gangguan Interaksi Accordion** | Konten detail tidak terbuka | Menjaga event trigger `toggleCard(id)` dan kelas `is-expanded` di accordion header. |
| **Pelanggaran Aturan Blade** | Pelanggaran standar repositori | 100% styling ditulis di `resources/css/pages/peserta/logbook-index.css`, nol inline styling di Blade. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] File rencana `docs/plans/2026-11-10-plan-redesign-logbook-peserta.md` telah disetujui oleh user.
- [x] Hero Banner menampilkan nama bidang, 2 box info transparan (Pembimbing & Periode), dan kartu widget emas minggu berjalan.
- [x] Card Tracker Logbook menampilkan progress bar navy, 13 week chips ber-4 status warna, dan legenda 4 warna.
- [x] Card Riwayat Jurnal menampilkan search bar, tombol emas `+ Isi Logbook Mingguan`, dan item logbook ber-squircle `MG`, status capsule, tombol outline `Edit`, dan chevron accordion.
- [x] Semua styling berada di `resources/css/pages/peserta/logbook-index.css` dan dikompilasi oleh Vite (`npm run build`).
- [x] Seluruh pengujian fitur logbook lulus 100% tanpa regresi.

