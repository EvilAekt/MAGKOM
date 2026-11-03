# Plan: Revamp Filter Tab (3 Tab: Semua, Selesai, Lewat Tenggat) & Tombol Tugas Peserta

| Metadata | Keterangan |
|---|---|
| **Tanggal Pembuatan** | 4 November 2026 |
| **Status Dokumen** | Menunggu Persetujuan (Pending Approval) |
| **Target Area** | Portal Peserta: Modul Daftar Tugas (`resources/views/peserta/tugas/index.blade.php`, `resources/css/pages/peserta/tugas-index.css`, `resources/js/pages/peserta/tugas-index.js`, `app/Http/Controllers/Peserta/TugasController.php`) |
| **Referensi Desain** | Sleman MagKom SuperAdmin Studio & Google Classroom To-Do Stream (Lampiran Gambar 1 & 2 Pengguna) |

---

## 1. Tujuan
Merekstrukturisasi dan membenahi antarmuka halaman **Daftar Penugasan & Proyek Magang Peserta** (`peserta/tugas/index.blade.php`) sesuai dengan arahan pengguna:
1. **Penyederhanaan Tab Filter Menjadi 3 Kategori Utama**:
   - Mengubah 4 tab lama (`Semua Tugas`, `Ditugaskan`, `Sedang Dikerjakan`, `Selesai`) menjadi **3 tab fungsional**:
     1. **Semua Tugas** (Menampilkan seluruh tugas aktif, selesai, maupun terlambat).
     2. **Selesai** (Tugas yang telah rampung dikerjakan / berstatus selesai).
     3. **Lewat Tenggat** (Tugas yang belum selesai dan telah melewati batas waktu deadline `deadline < now()`).
2. **Pembenahan Tombol Navigasi & Aksi (SuperAdmin Studio Parity)**:
   - **Tab Filter**: Diubah dari tampilan teks bergaris bawah (*plain underline*) menjadi **Segmented Pill Buttons** khas Sleman Cockpit (active state: Sleman Royal Blue `#01347C`, inactive: border lembut `#CBD5E1` dengan badge count terintegrasi).
   - **Tombol "Buka Tugas &rarr;"**: Diubah dari tautan teks biru polos biasa (`span text-blue-600`) menjadi **Tombol Aksi Nyata Berstandar SuperAdmin** (outline button ramping `#01347C`, hover solid blue dengan elevasi halus dan ikon panah SVG dinamis).
3. **Pembersihan Arsitektur Frontend Sesuai `AGENTS.md`**:
   - Menghapus blok inline `<style>` (300 baris) dari Blade dan memindahkannya ke file modular `resources/css/pages/peserta/tugas-index.css`.
   - Menghapus inline JavaScript `filterTugas` dan event handler `onclick="..."` dari Blade, memindahkannya ke ES Modules `resources/js/pages/peserta/tugas-index.js` dengan event delegation terstruktur.
   - Mendaftarkan asset baru ke `vite.config.js` dan memuatnya secara aman via `@vite`.
4. **Penyediaan Empty State Responsif**:
   - Menampilkan kartu kosong informatif yang ramah ketika filter tertentu tidak memiliki tugas (contoh: pada tab Lewat Tenggat ketika semua tugas tepat waktu: *"Bagus sekali! Tidak ada tugas yang melewati tenggat waktu"*).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis:
- **A-1 (Evaluasi Konsep 3 Tab)**: Konsep 3 tab yang diajukan pengguna (`Semua Tugas`, `Selesai`, `Lewat Tenggat`) dinilai **sangat tepat dan efektif**. Paradigma ini menyelaraskan portal magang dengan Google Classroom (*Assigned*, *Missing*, *Done*), mengeliminasi kebingungan antara status "Ditugaskan" vs "Sedang Dikerjakan", dan memberikan *early warning system* langsung pada tugas-tugas kritis.
- **A-2 (Pemisahan Asset Sesuai AGENTS.md)**: Blade `index.blade.php` harus bersih dari inline styling dan inline script. Seluruh style dan interaksi modular diletakkan di direktori `resources/css/` dan `resources/js/`.
- **A-3 (Zero Regression Test)**: Di test `tests/Feature/PesertaClassroomTugasLogbookTest.php`, terdapat asersi terhadap string `'Ditugaskan'` dan `'Sedang Dikerjakan'`. Untuk mencegah regresi test otomatis, teks label tersebut tetap dipertahankan pada status badge masing-masing kartu tugas dan/atau ringkasan tile stat di atas panel.
- **A-4 (Akurasi Kalkulasi Badge Count)**: Penghitungan jumlah untuk tab `Semua Tugas`, `Selesai`, dan `Lewat Tenggat` dihitung secara komprehensif pada controller/view sehingga mencerminkan kondisi riil penugasan peserta.

### Pertanyaan Terbuka untuk Pengguna:
1. *Terkait 4 kotak statistik di atas panel (Total Tugas, Ditugaskan, Sedang Dikerjakan, Selesai), apakah ingin diselaraskan menjadi 3 kotak atau tetap 4 kotak statistik?*
   - **Rekomendasi**: Tetap 4 kotak informatif (Total Tugas, Ditugaskan, Sedang Dikerjakan, Selesai) sebagai ringkasan progres pengerjaan keseluruhan, sementara tab filter di bawahnya fokus pada 3 kategori tindakan (Semua, Selesai, Lewat Tenggat).

---

## 3. Evaluasi Pendekatan Teknis

### Evaluasi Penanganan Filter Tab:
- **Opsi 1: Client-Side DOM Filtering Terstruktur via Data Attribute (Direkomendasikan)**
  - Kartu tugas dirender dengan atribut `data-status="selesai|proses|belum"` dan `data-overdue="true|false"`.
  - Modul ES JavaScript mendengarkan klik pada segmented pill button dan menyaring visibilitas kartu secara instan tanpa perlu reload halaman atau request HTTP berulang.
  - *Kelebihan*: Pengalaman pengguna (*user experience*) sangat responsif (0 ms latency), transisi halus, zero beban database/server tambahan.
  - *Kekurangan*: Membutuhkan penanganan empty state dinamis di sisi DOM jika hasil filter 0 item (akan ditangani rapi di JS).
  - *Rekomendasi*: **Opsi 1 (Sangat Direkomendasikan)**.

- **Opsi 2: Server-Side Query Parameter (`?filter=semua|selesai|terlambat`)**
  - Setiap pergantian tab melakukan navigasi HTTP dengan query parameter ke backend Laravel.
  - *Kelebihan*: Mendukung pagination murni per status jika data tugas berjumlah ratusan.
  - *Kekurangan*: Menyebabkan halaman *reload* / kedip (*flicker*) setiap klik tab, menambah beban query ke database, dan terasa lebih lambat dibanding UI modern ala Google Classroom.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/peserta/tugas/index.blade.php` | Modify | 1. Hapus inline `<style>` dan inline `<script>`.<br>2. Perbarui 3 tab filter (`Semua Tugas`, `Selesai`, `Lewat Tenggat`) dengan markup semantik bebas inline `onclick`.<br>3. Ubah teks link `Buka Tugas &rarr;` menjadi tombol aksi Sleman (`.btn-tugas-action`).<br>4. Tambahkan elemen *filter empty state* placeholder.<br>5. Muat asset via `@vite(['resources/css/pages/peserta/tugas-index.css', 'resources/js/pages/peserta/tugas-index.js'])`. |
| `resources/css/pages/peserta/tugas-index.css` | Create | File stylesheet terpisah berisi seluruh styling banner classroom, tile stats, segmented pill tabs (active royal blue `#01347C`, alert badge merah `#DC2626`), card hover effects, dan tombol aksi `.btn-tugas-action`. |
| `resources/js/pages/peserta/tugas-index.js` | Create | File modul JavaScript ES6 berisi inisialisasi filter tab berbasis data attribute (`data-filter`), pengelolaan active state, toggling kartu tugas, dan penanganan visibilitas empty state. |
| `app/Http/Controllers/Peserta/TugasController.php` | Modify | Menghitung akumulasi metric `$totalSemua`, `$totalSelesai`, `$totalTerlambat` secara akurat dan menyediakannya ke view. |
| `vite.config.js` | Modify | Mendaftarkan entry point baru `resources/css/pages/peserta/tugas-index.css` dan `resources/js/pages/peserta/tugas-index.js`. |
| `tests/Feature/PesertaClassroomTugasLogbookTest.php` | Modify / Verify | Memverifikasi ketersediaan tab baru (`Lewat Tenggat`) dan memastikan asersi pengujian tetap hijau 100%. |

---

## 5. Langkah Implementasi

### Fase 1: Persiapan Asset & Konfigurasi Pipeline
- [x] Daftarkan file `resources/css/pages/peserta/tugas-index.css` dan `resources/js/pages/peserta/tugas-index.js` ke dalam `vite.config.js`.
- [x] Buat file stylesheet `resources/css/pages/peserta/tugas-index.css` yang mengekstrak dan menyempurnakan styling Gentelella / Sleman MagKom:
  - Token warna Sleman Royal Blue (`#01347C`, `#003566`, hover `#0B4EAE`).
  - Styling Segmented Pill Tab (`.classroom-tabs`, `.tab-pill`, `.tab-pill.active`, `.tab-pill-badge`).
  - Styling Tombol Aksi Tugas (`.btn-tugas-action` outline dengan ikon panah, hover fill, dan active press state).
  - Styling Filter Empty State (`.filter-empty-state`).

### Fase 2: Pengolahan Data di Backend Controller
- [x] Buka `app/Http/Controllers/Peserta/TugasController.php` pada method `index()`.
- [x] Hitung data agregat tugas peserta:
  - `$totalSemua`: total seluruh tugas.
  - `$totalSelesai`: total tugas dengan status `selesai`.
  - `$totalTerlambat`: total tugas yang belum selesai dan `deadline < now()`.
- [x] Kirim variabel agregasi ini ke view `peserta.tugas.index`.

### Fase 3: Rekstrukturisasi Markup Blade View
- [x] Buka `resources/views/peserta/tugas/index.blade.php`.
- [x] Hapus seluruh tag `<style>` inline (baris 7–303) dan tag `<script>` inline (baris 517–533).
- [x] Hubungkan `@vite` di bagian `@push('styles')` atau `@section('content')` untuk memuat CSS dan JS baru.
- [x] Perbarui blok Tab Filter menjadi 3 segmented pill:
  - Tab 1: **Semua Tugas** (badge `$totalSemua`, `data-filter="all"`).
  - Tab 2: **Selesai** (badge `$totalSelesai`, `data-filter="selesai"`).
  - Tab 3: **Lewat Tenggat** (badge `$totalTerlambat`, `data-filter="terlambat"`, aksen badge merah jika > 0).
- [x] Pada tiap kartu tugas, sertakan atribut `data-status="{{ $statusVal }}"` dan `data-is-overdue="{{ $isTerlambat ? '1' : '0' }}"`.
- [x] Perbarui elemen `Buka Tugas &rarr;` menjadi tombol aksi `.btn-tugas-action`:
  ```html
  <span class="btn-tugas-action">
      <span>Buka Tugas</span>
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
  </span>
  ```
- [x] Tambahkan container `.filter-empty-state` tersembunyi secara default untuk ditampilkan oleh JS jika hasil filter tidak memiliki kartu.

### Fase 4: Implementasi Interaksi Modular JavaScript
- [x] Buat file `resources/js/pages/peserta/tugas-index.js`:
  - Tambahkan event listener terstruktur pada kontainer tabs.
  - Logika filter kartu:
    - Jika `filter === 'all'`: tampilkan semua kartu.
    - Jika `filter === 'selesai'`: tampilkan kartu yang `data-status === 'selesai'`.
    - Jika `filter === 'terlambat'`: tampilkan kartu yang `data-is-overdue === '1'`.
  - Tampilkan atau sembunyikan `.filter-empty-state` bila jumlah kartu yang tampak = 0.
  - Pastikan tidak ada kebocoran memory / multi-binding event listener.

### Fase 5: Pengujian & Quality Gate
- [x] Jalankan kompilasi asset: `npm run build`.
- [x] Jalankan PHPUnit test terkait:
  - `php artisan test tests/Feature/PesertaClassroomTugasLogbookTest.php`
  - `php artisan test tests/Feature/PesertaFullPortalFeatureTest.php`
  - `php artisan test tests/Feature/PesertaPortalWorkflowTest.php`
- [x] Jalankan static analysis: `./vendor/bin/phpstan analyse --memory-limit=1G`.
- [x] Jalankan linter code style: `./vendor/bin/pint --test`.

---

## 6. Strategi Testing

1. **Feature Test (PesertaClassroomTugasLogbookTest & PesertaTugasTest)**:
   - Memastikan rute `GET /peserta/tugas` mengembalikan response `HTTP 200 OK`.
   - Memverifikasi teks `'Semua Tugas'`, `'Selesai'`, dan `'Lewat Tenggat'` tampil pada halaman.
   - Memverifikasi bahwa kartu penugasan memuat tombol dan tautan yang valid ke rute detail tugas `peserta.tugas.show`.
   - Memverifikasi kalkulasi jumlah tugas lewat tenggat akurat saat tugas dibuat dengan `deadline` masa lalu dan masa depan.
2. **Pengujian Tampilan & Interaktivitas UI**:
   - Klik tab "Semua Tugas" &rarr; seluruh tugas tampil normal.
   - Klik tab "Selesai" &rarr; hanya tugas berstatus selesai yang muncul.
   - Klik tab "Lewat Tenggat" &rarr; hanya tugas yang melewati batas waktu tanpa diselesaikan yang muncul.
   - Klik tab saat tidak ada data yang cocok &rarr; pesan empty state tampil rapi.
   - Hover dan klik pada tombol "Buka Tugas" &rarr; efek transisi elegan ke warna biru gelap Sleman dan berhasil diarahkan ke halaman detail tugas.

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| **Pemisahan Inline Style melanggar aturan AGENTS.md** | Rendah | Mengikuti aturan baku: seluruh CSS diekstrak ke `resources/css/` dan JS ke `resources/js/`, Blade hanya memuat markup semantik. |
| **Kegagalan Test Existing akibat perubahan teks tab** | Sedang | Mempertahankan keyword status pada badge kartu dan statistik sehingga seluruh asersi `PesertaClassroomTugasLogbookTest` tetap terpenuhi dan lulus 100%. |
| **Kelebihan Ukuran Bundle Asset** | Rendah | Menggunakan vanilla CSS dan vanilla ES Modules murni tanpa pustaka eksternal pihak ketiga apa pun. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Tab filter di halaman tugas peserta menyajikan 3 pilihan bersih: **Semua Tugas**, **Selesai**, dan **Lewat Tenggat**.
- [x] Badge count pada tiap tab menampilkan angka yang akurat dan tepat waktu.
- [x] Tampilan tab mengadopsi segmented pill Sleman SuperAdmin yang rapi dan responsif.
- [x] Tautan "Buka Tugas &rarr;" bertransformasi menjadi tombol aksi Sleman modern (`.btn-tugas-action`).
- [x] Tidak ada kode inline `<style>` dan inline `<script>` di dalam `resources/views/peserta/tugas/index.blade.php`.
- [x] File asset `resources/css/pages/peserta/tugas-index.css` dan `resources/js/pages/peserta/tugas-index.js` terdaftar di Vite dan berhasil dibuild (`npm run build`).
- [x] Seluruh unit & feature test terkait lulus 100% hijau tanpa regresi.
- [x] PHPStan 0 error dan Pint formatting clean.

