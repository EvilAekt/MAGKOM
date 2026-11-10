# Rencana Eliminasi Scrollbar & Compact Layout Modal Pendaftaran Peserta

**Tanggal**: 7 November 2026  
**Status**: Draf Usulan (Menunggu Konfirmasi User)  
**Tujuan**: Mengeliminasi scrollbar samping vertikal pada popup formulir pendaftaran magang baru ("Daftar Sekarang") di portal peserta, sehingga seluruh konten (dari header, formulir, hingga tombol aksi "Daftar Sekarang") tampil utuh, presisi, dan pas (*fit to screen*) tanpa perlu digulir (*no scroll*).

---

## 1. Analisis Masalah & Visual Evidence

Berdasarkan screenshot pengguna (`media_1794047083181_da98c305.png`):
1. **Muncul Scrollbar Vertikal Tebal**: Terdapat scrollbar vertikal di sisi kanan kartu modal popup formulir pengajuan baru.
2. **Konten Terpotong Vertikal**: Bagian bawah modal ("Estimasi Rentang Pelaksanaan Magang" dan tombol aksi "Batal" serta "Daftar Sekarang") terpotong dan tersembunyi di bawah lipatan layar (*below the fold*), memaksa pengguna menggulir (*scroll*) ke bawah untuk menemukan tombol submit.
3. **Penyebab Utama (*Root Cause*)**:
   - Spacing dan padding vertikal terlalu longgar: padding modal card sebesar `26px 28px`.
   - Spacing antar-elemen form menggunakan `gap: 18px` yang menyita ruang vertikal kumulatif ~72px.
   - Header modal menyita ruang vertikal besar (`margin-bottom: 20px`, `padding-bottom: 16px`, badge ikon `44px`).
   - Teks petunjuk (*hints*) di bawah dropdown dan chips durasi memakan 2–3 baris teks redundan.
   - Kartu ringkasan peserta (*summary card*) menggunakan padding `16px` dengan susunan 3 baris terpisah.
   - Tombol chips durasi magang memiliki tinggi vertikal `min-height: 56px`.
   - Footer modal memiliki `margin-top: 24px` dan `padding-top: 18px`.
   - Card modal memiliki properti `overflow-y: auto` dengan batasan `max-height: 90vh`.

---

## 2. Strategi Solusi: Compact Proportional DS-MagKom Layout

Untuk mencapai target **"tidak ada scroll-nya" (*Zero Scrollbar & Fit-to-Screen*)**, dilakukan restrukturisasi proporsional ruang vertikal tanpa mengurangi nilai estetika dan informasi penting:

### A. Rekayasa Proporsi & Dimensi Vertikal (Micro-Spacing Architecture)
1. **Modal Card Container**:
   - Padding card dipadatkan dari `26px 28px` menjadi `18px 22px`.
   - Tinggi maksimum diatur adaptif dengan `max-height: calc(100vh - 32px)` dan menghilangkan scrollbar (`scrollbar-width: none;` serta `::-webkit-scrollbar { display: none; }`).
   - Total estimasi tinggi modal menyusut dari **~620px** menjadi **~430px–450px**, sehingga aman dan pas di seluruh resolusi layar (laptop 1366x768, monitor 1080p, maupun tablet).

2. **Header Modal Ringkas**:
   - Ikon badge kalender dikecilkan dari `44px` menjadi `36px` dengan radius `10px`.
   - Judul modal disesuaikan ke `1.05rem` / `700 weight`.
   - Margin-bottom dipangkas dari `20px` ke `12px`, dan padding-bottom dari `16px` ke `10px`.
   - Teks subtitle dibuat ringkas 1 baris.

3. **Compact Single-Row Summary Banner**:
   - Padding kartu ringkasan peserta dipadatkan dari `16px` ke `8px 12px`.
   - Informasi jurusan, jenjang, bidang penempatan, dan institusi ditata elegan dalam 2 baris terstruktur:
     - *Baris 1*: Nama Jurusan + Badge Jenjang (S1/D3/SMK).
     - *Baris 2*: Bidang Penempatan &bull; Nama Institusi/Kampus.

4. **Formulir & Dropdown Periode**:
   - Jarak antar elemen form (`gap`) dipangkas dari `18px` ke `10px`.
   - Select box padding diatur ke `7px 12px` (tinggi ~38px).
   - Teks petunjuk (*hint*) disederhanakan menjadi satu baris mikro yang halus (`font-size: 0.72rem`).

5. **Compact Duration Chips (1 s/d 6 Bulan)**:
   - Tinggi tombol chip dipangkas dari `min-height: 56px` menjadi `min-height: 38px` dengan padding `4px 6px`.
   - Angka durasi (`1 Bulan`) dan sub-label (`Standar / Direkomendasikan / Maksimal`) tetap terbaca jelas dengan hierarki tipografi proporsional.
   - Menghemat ruang vertikal hingga **~36px**.

6. **Inline / Compact Preview Box**:
   - Box estimasi tanggal pelaksanaan diubah dari kartu tebal (`padding: 14px 16px`) menjadi status strip kompak (`padding: 6px 12px`):
     `[Ikon Kalender 16px] Estimasi: 01 Des 2026 s/d 28 Feb 2027 (3 Bulan)`.
   - Menghemat ruang vertikal hingga **~35px**.

7. **Compact Modal Footer**:
   - Margin-top dan padding-top dipangkas dari `24px + 18px` menjadi `10px + 10px`.
   - Tombol "Batal" dan "Daftar Sekarang" berukuran proporsional (`padding: 7px 18px; font-size: 0.8125rem`).

---

## 3. Berkas yang Akan Diubah

| Berkas | Ruang Lingkup Perubahan |
|---|---|
| [`resources/css/layouts/dashboard-cockpit.css`](file:///d:/MAGKOM/MAGKOM/resources/css/layouts/dashboard-cockpit.css) | Memperbarui kelas `.pendaftaran-modal-*`, `.duration-chips-grid`, `.duration-chip-btn`, `.pendaftaran-preview-box`, dan menyembunyikan scrollbar bawaan. |
| [`resources/views/peserta/pendaftaran/index.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/pendaftaran/index.blade.php) | Menyederhanakan markup header, summary card baris kompak, hint singkat, dan layout preview box inline. |

---

## 4. Kepatuhan Aturan Repositori (AGENTS.md)

1. **Aturan CSS**: Seluruh styling modal dikelola di `resources/css/layouts/dashboard-cockpit.css` dan dikompilasi via Vite (`npm run build`). Tidak ada inline style baru di Blade.
2. **Aturan Blade**: Perubahan markup Blade bersifat minimal untuk restrukturisasi container form dan teks petunjuk ringkas.
3. **Kontrak Rute & Data**: Form action, CSRF token, data atribut `data-bulan-id`, `data-bulan-mulai`, dan JavaScript handler tetap 100% utuh tanpa perubahan logika backend.

---

## 5. Rencana Verifikasi & Quality Gates

1. **Vite Build**: Menjalankan build aset frontend dengan Node v22:
   ```powershell
   $env:Path = "D:\laragon\bin\nodejs\node-v22;" + $env:Path; & "D:\laragon\bin\nodejs\node-v22\npm.cmd" run build
   ```
2. **Cache View**: Menjalankan `php artisan view:clear`.
3. **Feature Test Suite**: Memastikan 31 tests pada `PendaftaranPesertaTest`, `PesertaFullPortalFeatureTest`, dan `GentelellaDashboardRevampTest` lulus 100%.
4. **Code Styling**: Menjalankan `./vendor/bin/pint --test` untuk verifikasi PSR-12 / Laravel standards.

