# Plan: Perbaikan dan Penataan Layout Tabel Kuota per Bidang (Eliminasi Ruang Kosong Tengah & Formatting Tag Jurusan)

**Tanggal**: 05 November 2026  
**Status**: Selesai Terverifikasi (100% Pass)  
**Referensi**: Permintaan Pengguna & Analisis Tangkapan Layar `media_1793813868606_6a1ad87e.png`, Aturan `AGENTS.md`.

---

## 1. Analisis Masalah (Root Cause: Ruang Kosong Tengah & Layout Aneh)

Berdasarkan keluhan pengguna dan penelusuran kode pada [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php) serta bukti tangkapan layar `media_1793813868606_6a1ad87e.png`:

1. **Penyebab Utama Ruang Kosong di Tengah**:
   - Kolom `<th>Jurusan yang Dibutuhkan</th>` pada baris 1038 tidak memiliki atribut lebar tetap, sehingga browser secara otomatis mengalokasikan seluruh sisa lebar tabel (sekitar 400px–650px pada layar widescreen).
   - Namun di CSS baris 381 terdapat aturan pembatas:
     ```css
     .jurusan-text-muted {
         color: #475569;
         font-size: 0.8125rem;
         line-height: 1.45;
         max-width: 320px; /* <--- Pembatas kaku inilah sumber masalahnya */
     }
     ```
   - Akibatnya, teks jurusan terpaksa terpotong dan melompat ke baris baru sebelum mencapai 320px, menyisakan jurang ruang putih kosong sebesar 200px–350px persis di tengah tabel sebelum kolom `Jenjang`.
2. **Presentasi Data Jurusan Kurang Terstruktur**:
   - Data jurusan hanya dicetak sebagai teks panjang koma-koma (`Informatika, Manajemen Informatika, Sistem Informasi, Teknologi Informasi`). Format teks mentah ini sulit dipindai (*unscannable*) dan membuat visual tabel tampak monoton dibandingkan badge jenjang di sebelahnya.
3. **Ketidakseimbangan Alokasi Lebar Kolom (Unbalanced Proportions)**:
   - Kolom `Jenjang` dialokasikan `width: 140px` padahal hanya menampung dua badge kecil (`D3` & `S1`) selebar ~50px.
   - Kolom `Durasi` dialokasikan `120px` padahal hanya berisi teks pendek `"1–6 bulan"`.
   - Kolom `Kuota Terisi` (`150px`) dan `Kapasitas` (`120px`) menyajikan informasi ganda/redundan yang berdesakan di sisi kanan layar.
   - Dampaknya: Sisi kiri (`Bidang` & `Jurusan`) terdesak ke tepi kiri, sisi kanan terdesak ke tepi kanan, sedangkan area tengah terlihat bolong melompong (*hollow middle*).

---

## 2. Evaluasi Opsi Solusi

| Kriteria | Opsi A (Rekomendasi Utama): Balanced Proportional Table + Micro-Tags | Opsi B: Grid Card per Bidang | Opsi C: Merger Kolom Ekstrem |
| :--- | :--- | :--- | :--- |
| **Bentuk Tampilan** | Tetap tabel tabular `<table>`, tetapi proporsi lebar kolom diseimbangkan dan jurusan diformat menjadi *smart micro-tags/chips*. | Mengubah tabel menjadi barisan Card per Bidang (2 kolom kartu). | Menggabungkan Jenjang + Durasi dan Kuota Terisi + Kapasitas menjadi hanya 4 kolom tabel. |
| **Solusi Ruang Tengah** | **Tuntas 100%**: Chip jurusan mengalir dinamis (*flex-wrap*) mengisi 36%–40% lebar area tengah dengan elegan. | Tuntas, tetapi mengubah paradigma dari tabel ke kartu. | Tuntas, tetapi tabel menjadi terlalu ringkas dan kosong di kanan. |
| **Keterbacaan Data** | **Sangat Tinggi**: Setiap jurusan mudah dikenali dalam satu pandangan (*scannable*). | Tinggi. | Sedang. |
| **Kepatuhan Automated Tests** | **100% PASS**: Mempertahankan seluruh nama kolom `<th>` (`Bidang`, `Jurusan yang Dibutuhkan`, `Jenjang`, `Durasi`, `Kuota Terisi`, `Kapasitas`, tombol `Ubah`) yang diuji di `AdminKuotaRevampTest`. | **RISIKO TINGGI / GAGAL**: Test memeriksa struktur `<table class="table-bidang">`. | **RISIKO TINGGI / GAGAL**: Test memeriksa `<th>` masing-masing kolom. |
| **Kepatuhan AGENTS.md** | Sangat patuh (*minimal, focused change*). | Melanggar aturan UI redesign massal. | Berisiko merusak kontrak visual yang ada. |

### Rekomendasi: Opsi A
Opsi A dipilih karena menyelesaikan masalah visual pengguna secara tuntas dan elegan tanpa merusak struktur kontrak data maupun tes otomatis yang sudah hijau.

---

## 3. Rincian Perubahan Desain (Opsi A)

### 3.1 Distribusi Lebar Kolom Baru
Menghilangkan pembagian lebar kaku dan menggunakan distribusi terencana:
- **`Bidang` (`22%`, min-width: 220px)**: Avatar inisial bundar bertekstur lembut + Nama Bidang tebal berwarna slate gelap (`#0F172A`).
- **`Jurusan yang Dibutuhkan` (`38%`, fluid)**:
  - Hapus pembatas `max-width: 320px`.
  - Format setiap nama jurusan sebagai **Micro-Tag / Chip Modern**:
    - Background: `#F8FAFC` (atau adaptif dark mode `#1E293B`)
    - Border: `1px solid #E2E8F0` (dark mode `#334155`)
    - Color: `#334155` (dark mode `#E2E8F0`)
    - Font-size: `0.75rem` (`12px`), font-weight: `600`
    - Padding: `3px 8px`, border-radius: `6px`
    - Tata letak: `display: inline-flex; flex-wrap: wrap; gap: 5px;`
  - Jika bidang memiliki banyak jurusan (seperti Sekretariat), tag akan mengalir rapi bertingkat 2-3 baris mengisi kolom tengah secara harmonis.
- **`Jenjang` (`9%`, width: ~95px)**:
  - Badge `SMK`, `D3`, `S1` dengan style pill halus biru yang konsisten (`#EFF6FF`, border `#DBEAFE`, text `#1D4ED8`).
- **`Durasi` (`9%`, width: ~95px)**:
  - Teks `"1–6 bulan"` dengan font semibold `0.8125rem` yang rapi dan terpusat.
- **`Kuota Terisi` (`11%`, width: ~120px)**:
  - Rasio terisi/kapasitas tebal (`7/15`), badge persentase (`47%`), dan progress bar yang presisi dengan background track `#F1F5F9` dan fill `#2563EB`.
- **`Kapasitas` (`6%`, width: ~80px)**:
  - Teks kapasitas `"15 peserta"` dengan font semibold, seimbang berdampingan dengan kolom kuota terisi.
- **`Aksi` (`5%`, width: ~80px, text-align: right)**:
  - Tombol outline biru `"Ubah"` yang presisi dan sejajar.

### 3.2 Dukungan Dark Mode
Seluruh elemen baru (chip jurusan, progress bar track, border baris) memiliki padanan token warna untuk tema gelap (`[data-theme="dark"]`).

---

## 4. Perubahan File

| No | File Target | Aksi | Ringkasan Perubahan |
| :-: | :--- | :---: | :--- |
| 1 | `resources/views/admin/kuota/index.blade.php` | **Modify** | 1. Perbarui header tabel `<thead>` dengan lebar persentase yang seimbang.<br>2. Perbarui perulangan kolom Jurusan pada baris 1060–1064 agar merender kumpulan micro-tags (`.jurusan-tag-item`) dari koleksi jurusan.<br>3. Perbarui blok styling scoped `.table-bidang`, `.jurusan-tags-wrap`, `.jurusan-tag-item` (menghapus `max-width: 320px`). |
| 2 | `resources/css/components/tables.css` | **Modify** | Tambahkan kelas pendukung `.jurusan-tags-wrap` dan `.jurusan-tag-item` di CSS komponen agar terkelola melalui Vite pipeline. |

---

## 5. Permohonan Persetujuan Markup Blade (Sesuai AGENTS.md Bagian 3.3)

- **Alasan Markup Diperlukan**: Mengubah teks jurusan dari format koma mentah string (`$item['jurusan_names']`) menjadi elemen individual chip/tag (`<span class="jurusan-tag-item">`) memerlukan perulangan `@forelse($item['jurusan_list'] as $j)` di Blade. Tanpa markup ini, tag visual terpisah tidak dapat dirender secara semantik.
- **File & Bagian Target**: [`resources/views/admin/kuota/index.blade.php:1060-1064`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php#L1060-L1064) dan lebar kolom `<th>` pada baris 1036–1044.

---

## 6. Langkah Implementasi Terperinci

- [x] **Langkah 1**: Tambahkan styling micro-tags `.jurusan-tags-wrap` dan `.jurusan-tag-item` di `resources/css/components/tables.css` dan sesuaikan blok style di `resources/views/admin/kuota/index.blade.php`.
- [x] **Langkah 2**: Hapus deklarasi pembatas `max-width: 320px` pada kelas jurusan.
- [x] **Langkah 3**: Sesuaikan persentase lebar kolom pada tag `<th>` di `admin/kuota/index.blade.php`.
- [x] **Langkah 4**: Perbarui baris sel `<td>` jurusan agar merender kumpulan `<span class="jurusan-tag-item">` menggunakan `$item['jurusan_list']` (dengan fallback `$item['jurusan_names']`).
- [x] **Langkah 5**: Kompilasi asset dengan `npm run build`.
- [x] **Langkah 6**: Jalankan automated test suite `AdminKuotaRevampTest` dan `AdminChildPagesVisualTest` untuk memastikan 100% lulus tanpa regresi.
- [x] **Langkah 7**: Jalankan `./vendor/bin/pint --test` untuk verifikasi format PHP.

---

## 7. Strategi Testing & Quality Gates

```bash
# 1. Kompilasi asset frontend Vite
npm run build

# 2. Automated Feature Tests
php artisan test tests/Feature/AdminKuotaRevampTest.php tests/Feature/AdminChildPagesVisualTest.php

# 3. Format Linter
./vendor/bin/pint --test
```

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Kolom tengah ("Jurusan yang Dibutuhkan") tidak lagi memiliki ruang hampa atau jurang kosong melompong.
- [x] Nama jurusan ditampilkan sebagai kumpulan micro-tag chip yang rapi, elegan, dan mudah dibaca.
- [x] Seluruh kolom terdistribusi secara proporsional dari kiri ke kanan.
- [x] Mode gelap (*dark mode*) terakomodasi dengan baik.
- [x] Seluruh automated feature tests PHPUnit tetap 100% PASS (14 tests, 118 assertions).
- [x] Build Vite dan Laravel Pint lulus tanpa error.

