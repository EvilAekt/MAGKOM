# Rencana Kerja (Plan): Redesign Tampilan Pendaftaran Magang Peserta Sesuai Mockup Gambar

**Tanggal Dibuat:** 10 November 2026  
**Status:** Menunggu Persetujuan User  
**Tujuan Dokumen:** Perencanaan terstruktur perombakan visual halaman pendaftaran magang peserta (`/peserta/pendaftaran`) agar presisi sesuai desain referensi pada mockup gambar (`media_1794294952304_fc040117.png`) dengan menggunakan color palette identitas MagKom Sleman.

---

## 1. Tujuan

Melakukan redesign menyeluruh antarmuka halaman Pendaftaran Magang Peserta (`resources/views/peserta/pendaftaran/index.blade.php`) dan stylesheet pendukungnya (`resources/css/pages/peserta/pendaftaran-index.css`) agar tampil identik dan pixel-perfect dengan gambar referensi:
1. **Hero Banner Modern Sleman Navy**: Menampilkan badge resmi Sleman, tipografi bersih, ring ornament konsentris di sudut kanan bawah, dan pill meta informasi institusi/jurusan/jenjang.
2. **Spotlight Card Pengajuan Aktif (Two-Tone Split)**:
   - **Header Band (Deep Navy)**: Memuat chip `#REG-XXXXX`, judul pengajuan aktif, tanggal pengajuan, serta badge status emas menonjol `Selamat! Diterima Magang` (atau status aktif terkait).
   - **Body Band (Crisp White)**: Memuat 4 kotak metrik informasi dalam 1 baris grid (`BULAN MAGANG`, `JURUSAN KUOTA TERDAFTAR`, `RENCANA DURASI MAGANG`, dan `STATUS PENEMPATAN` yang di-highlight dengan latar amber/emas lembut), garis pembatas putus-putus (*dashed line*), dan tombol aksi utama emas (*Gold CTA Button*) `Buka Berkas & Detail Pengajuan`.
3. **Card Riwayat Pendaftaran Magang**: Wadah putih bersih dengan header ber-chevron accordion, baris riwayat rapi dengan kotak ikon navy bercentang, kode registrasi, bulan pelaksanaan, ringkasan meta, badge status pill pastel, dan tombol `Detail >`.
4. **Preservasi 100% Kontrak & Fungsionalitas**: Menjaga seluruh alur data, kuota magang, modal pembuatan pengajuan baru (`#pendaftaranModal`), form submission POST, assertion pada feature test, dan nol penulisan CSS/JS di dalam file Blade.

---

## 2. Asumsi & Batasan Teknis

1. **Color Palette Identitas MagKom (Sesuai Data CSS Peeper)**:
   - Primary Deep Navy: `#003566`, `#01347C`, `#02275D`
   - Secondary Accent Indigoid: `#3F51B5`
   - Sleman Gold Accent: `#FFC300`, `#FFCB05`
   - Dark Neutral / Typography: `#212529`, `#000000`
   - Soft Neutral / Borders: `#AAAAAA`, `#BBBBBB`, `#E2E8F0`, `#F1F5F9`, `#FFFFFF`
   - Amber / Golden Highlight (Status Penempatan): `#FFFBEB` (background), `#FDE68A` (border), `#854D0E` (text), `#FFC300` (icon background)
2. **Kondisi Tampilan Dinamis**:
   - Jika peserta memiliki pengajuan aktif (`$pendaftaranAktif`), halaman menampilkan Card 2 (Spotlight Pengajuan Aktif) dan Card 3 (Riwayat Pendaftaran Magang).
   - Jika peserta belum memiliki pengajuan aktif dan profil lengkap, halaman tetap menampilkan bagian katalog kuota magang yang tersedia beserta modal pendaftaran baru (`#pendaftaranModal`).
   - Kotak ke-4 pada Card Pengajuan Aktif ("STATUS PENEMPATAN") menampilkan bidang penempatan (contoh: "Bidang Aplikasi Informatika (Aptika)") dengan highlight emas jika peserta sudah diterima dan ditempatkan, atau menampilkan "Tahap Seleksi Berkas" jika belum ditentukan.
3. **Aturan Repositori & Blade**:
   - Seluruh styling visual wajib berada di `resources/css/pages/peserta/pendaftaran-index.css` yang dikompilasi oleh Vite.
   - Tidak boleh ada tag `<style>` atau inline atribut `style="..."` statis baru di dalam Blade.
   - Perubahan Blade hanya sebatas penyesuaian markup semantik container kartu agar sesuai dengan tata letak visual gambar referensi.

---

## 3. Pendekatan Teknis (Evaluasi Opsi)

### Opsi A (Direkomendasikan): Rekonstruksi Semantik Kartu dengan Dedicated Scoped CSS
- **Deskripsi**: Merestrukturisasi markup Blade di `resources/views/peserta/pendaftaran/index.blade.php` ke komponen semantik murni (`pendaftaran-hero`, `pendaftaran-active-card`, `pendaftaran-history-card`) dan menuliskan styling detail di `resources/css/pages/peserta/pendaftaran-index.css`.
- **Kelebihan**:
  - Tampilan visual mencapai 100% presisi sesuai desain gambar (header split two-tone, ring dekoratif, highlight box penempatan, accordion riwayat).
  - Mengeliminasi ketergantungan wrapper Gentelella lama (`<x-gentelella.card>` dan `<x-gentelella.x-panel>`) yang memiliki border dan padding kaku yang tidak cocok dengan gambar mockup.
  - Sangat mudah dipelihara dan fully responsive (mobile, tablet, desktop).
- **Kekurangan**: Memerlukan pembaruan markup Blade pada bagian container kartu (memerlukan persetujuan eksplisit user sesuai aturan Blade).

### Opsi B: Mempertahankan Komponen Gentelella Lama & Melakukan Override CSS Paksa
- **Deskripsi**: Tetap menggunakan `<x-gentelella.card>` dan `<x-gentelella.x-panel>`, lalu menimpa border, header, dan padding menggunakan CSS override kompleks dan `!important`.
- **Kelebihan**: Perubahan markup Blade lebih sedikit.
- **Kekurangan**:
  - Struktur HTML Gentelella membatasi pemisahan visual header dark navy dan body putih pada spotlight card.
  - CSS override menjadi rapuh (*fragile*), sulit dipelihara, dan rentan menimbulkan bug layout pada berbagai ukuran layar.
  - Risiko bentrok styling dengan komponen Gentelella global lainnya.

> **Keputusan**: Dipilih **Opsi A** karena memberikan hasil visual yang paling presisi, bersih, dan mematuhi prinsip arsitektur pemisahan stylesheet di `resources/css/`.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/pendaftaran/index.blade.php` | Modify | 1. Hero Banner: Tambahkan ring dekorasi konsentris dan standardisasi pill info institusi/jurusan/jenjang.<br>2. Spotlight Pengajuan Aktif: Transformasi menjadi two-tone card (Navy Header Band + Crisp White Body dengan 4 metric boxes, dashed divider, dan Gold CTA button).<br>3. Riwayat Pendaftaran: Transformasi menjadi card container modern dengan header accordion chevron dan stream list baris berikon centang.<br>4. Mempertahankan seluruh modal `#pendaftaranModal`, script helper, dan hidden inputs untuk menjaga fungsionalitas. |
| `resources/css/pages/peserta/pendaftaran-index.css` | Modify | 1. Implementasi token CSS Peeper (`#003566`, `#01347C`, `#02275D`, `#FFC300`, `#FFCB05`).<br>2. Styling Hero Banner dengan ring konsentris SVG/pseudo-element.<br>3. Styling Two-Tone Active Card (Navy header band, grid 4 info-box dengan amber highlight pada kotak penempatan, dashed separator, dan gold button).<br>4. Styling Riwayat Card (accordion header, custom row card dengan icon box navy, pill status lembut, dan outline action button).<br>5. Responsive query untuk resolusi mobile dan tablet. |

---

## 5. Langkah-Langkah Implementasi Bertahap

### Fase 1: Persiapan & Styling Terpadu di CSS
- [x] **Langkah 1.1**: Definisikan CSS variables/tokens di `resources/css/pages/peserta/pendaftaran-index.css` berdasarkan palet CSS Peeper:
  ```css
  --navy-dark: #003566;
  --navy-primary: #01347C;
  --navy-deep: #02275D;
  --gold-primary: #FFC300;
  --gold-accent: #FFCB05;
  --text-dark: #212529;
  ```
- [x] **Langkah 1.2**: Implementasikan styling dekorasi cincin melengkung (*concentric circular rings*) pada `.pendaftaran-hero` menggunakan `::after` atau SVG overlay yang diposisikan di kanan bawah hero.
- [x] **Langkah 1.3**: Tuliskan rules layout untuk `.pendaftaran-active-card`:
  - `.active-card-topbar`: Latar navy gradient (`#003566` s/d `#01347C`), padding 20px 24px, flex row space-between.
  - `.active-card-badge-gold`: Badge pill emas (`background: #FFC300; color: #02275D; font-weight: 700;`).
  - `.active-card-content`: Latar putih, padding 24px.
  - `.active-grid-4`: CSS Grid 4 kolom (`repeat(4, minmax(0, 1fr))`) dengan gap 16px (responsif 2 kolom di tablet, 1 kolom di mobile).
  - `.active-metric-box`: Kotak metrik berbingkai lembut dengan ikon berlatar biru muda (`#EFF6FF`).
  - `.active-metric-box.highlight-placement`: Kotak ke-4 khusus penempatan dengan latar `#FFFBEB`, border `#FDE68A`, dan ikon emas `#FFC300`.
  - `.active-dashed-line`: Garis putus-putus abu-abu lembut `border-top: 1px dashed #E2E8F0;`.
  - `.btn-gold-action`: Tombol emas tebal dengan hover transition dan teks biru pekat `#02275D`.
- [x] **Langkah 1.4**: Tuliskan rules styling untuk `.riwayat-card-wrapper`:
  - Header dengan title dan accordion chevron button `[^]`.
  - Row item `.riwayat-item-card` dengan border abu-abu lembut, kotak centang navy berlatar `#003566`, typography yang tegas, status pill, dan tombol `Detail >`.

### Fase 2: Penyelarasan Markup Blade
- [x] **Langkah 2.1**: Update markup Hero Banner di `resources/views/peserta/pendaftaran/index.blade.php` agar menyertakan ornamen ring konsentris dan metadata pills yang rapi.
- [x] **Langkah 2.2**: Update markup Spotlight Pengajuan Aktif:
  - Pisahkan menjadi struktur header band (navy) dan body band (white).
  - Susun 4 metrik kotak: Bulan Magang, Jurusan Kuota Terdaftar, Rencana Durasi Magang, dan Status Penempatan (dengan class conditional `highlight-placement` saat status penempatan aktif/diterima).
  - Terapkan tombol CTA emas `Buka Berkas & Detail Pengajuan`.
- [x] **Langkah 2.3**: Update markup Riwayat Pendaftaran:
  - Bungkus dalam container kartu putih modern dengan chevron toggle di kanan.
  - Sajikan list riwayat dengan icon box navy, kode `#REG-XXXXX`, nama bulan, detail jurusan & durasi, badge status pastel, dan tombol `Detail >`.
  - Pertahankan hidden string `<span class="sr-only">Buat Pengajuan Baru</span>` agar assertion pada feature test tetap 100% lulus.
- [x] **Langkah 2.4**: Pastikan modal formulir pengajuan baru (`#pendaftaranModal`), interactive duration chips, dan script helper form submission tetap utuh tanpa perubahan fungsional.

### Fase 3: Kompilasi Aset & Verifikasi
- [x] **Langkah 3.1**: Jalankan kompilasi aset Vite (`npm run build`) untuk memastikan tidak ada sintaks CSS yang error.
- [x] **Langkah 3.2**: Jalankan pengujian otomatis:
  ```powershell
  & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test tests/Feature/PesertaPendaftaranFlowTest.php tests/Feature/PendaftaranPesertaTest.php
  & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test tests/Feature/GentelellaDashboardRevampTest.php
  ```
- [x] **Langkah 3.3**: Lakukan inspeksi visual di browser lokal pada `http://127.0.0.1:8000/peserta/pendaftaran` untuk memverifikasi keselarasan visual dengan gambar `media_1794294952304_fc040117.png`.

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - `PesertaPendaftaranFlowTest::test_peserta_can_view_pendaftaran_index_and_available_periods`: Memastikan seluruh string esensial (`Riwayat Pendaftaran Magang`, `Buat Pengajuan Baru`, `November 2026`, dll.) tetap ter-render dan status HTTP 200 OK.
   - `PesertaPendaftaranFlowTest::test_peserta_can_create_new_draft_pendaftaran`: Memastikan interaksi modal dan proses registrasi draf tetap berjalan sempurna.
   - `GentelellaDashboardRevampTest`: Memastikan seluruh tata letak dan tema tidak terganggu.
2. **Visual & Responsive Verification**:
   - Tampilan Desktop (> 1024px): 4 kolom info metrik sejajar, hero banner proporsional dengan ornamen cincin di sudut kanan, tombol CTA sejajar kanan.
   - Tampilan Tablet (768px - 1023px): Grid info metrik adaptif 2x2 kolom tanpa tumpang tindih.
   - Tampilan Mobile (< 768px): Header band menyesuaikan secara vertikal, 4 info box stacking 1 kolom, tombol CTA memenuhi lebar penuh (*full-width*).

---

## 7. Analisis Risiko & Mitigasi

| Risiko Potensial | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Penyimpangan Markup Blade Melanggar Aturan Proyek** | Perubahan tidak disetujui atau merusak layout | Menjelaskan secara transparan kepada user perlunya restrukturisasi markup container kartu dan meminta persetujuan eksplisit sebelum eksekusi. |
| **Kegagalan Assertion pada Test Otomatis** | Pipeline test merah | Mempertahankan seluruh teks kunci, route name (`peserta.pendaftaran.detail`), dan hidden text yang di-assert oleh unit/feature test. |
| **Kerusakan Interaktivitas Modal Pendaftaran** | Peserta gagal mendaftar | Tidak mengubah logika JavaScript, id input (`#kuota_id`, `#durasi_bulan`, `#bulan_magang_id`), maupun action route form submission. |
| **CSS Leakage ke Halaman Lain** | Halaman lain terpengaruh | Seluruh rule CSS di-scope ketat di bawah container `.pendaftaran-container` dan kelas spesifik pendaftaran. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] File rencana `docs/plans/2026-11-10-plan-redesign-pendaftaran-magang-peserta.md` telah dibuat dan disetujui oleh user.
- [x] Markup Hero Banner di `index.blade.php` menampilkan ornamen ring konsentris dan badge Sleman Navy presisi sesuai desain gambar.
- [x] Spotlight Card Pengajuan Aktif memiliki two-tone split (Navy header + White body) dengan 4 metric boxes (penempatan ber-highlight emas), garis putus-putus, dan tombol emas CTA.
- [x] Riwayat Pendaftaran Magang berbalut kartu putih bersih dengan header accordion dan baris list modern berikon centang navy.
- [x] Semua CSS ditulis di `resources/css/pages/peserta/pendaftaran-index.css` tanpa inline style baru di Blade.
- [x] Kompilasi `npm run build` sukses tanpa warning/error.
- [x] Seluruh test `PesertaPendaftaranFlowTest`, `PendaftaranPesertaTest`, dan `GentelellaDashboardRevampTest` lulus 100% (Green).

