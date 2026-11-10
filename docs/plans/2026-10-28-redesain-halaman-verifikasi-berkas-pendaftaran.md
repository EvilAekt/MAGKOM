# Rencana Kerja: Redesain Halaman Verifikasi Berkas Pendaftaran Peserta

**Dokumen**: `docs/plans/2026-10-28-redesain-halaman-verifikasi-berkas-pendaftaran.md`  
**Tanggal**: 28 Oktober 2026  
**Status**: Selesai Diimplementasikan (Verified)  

---

## 1. Tujuan
Menata ulang dan merapikan halaman **Verifikasi Berkas Pendaftaran Peserta** (`resources/views/admin/pendaftaran/index.blade.php`) agar:
1. **Memindahkan tombol panah collapse (`^`)** dari posisi mengambang di tengah ke **sudut kanan paling luar (*far right corner*)** dari card header.
2. **Merapikan layout agar bersih, terstruktur, dan elegan** tanpa tumpukan kartu ganda (*nested card*), meratakan tinggi kontrol form filter (42px), serta menghilangkan duplikasi tombol aksi.
3. **Mengonsistenkan visual dengan Design System MagKom & Dashboard Cockpit**, meliputi:
   - 4 kartu metrik KPI atas yang diperkaya dengan ikon visual SVG dan teks deskriptif (*subText*).
   - Inisial peserta 2 huruf dengan 5 palet warna dinamis (`.cv-avatar-sm .cv-avatar-palette-*`).
   - Status berkas dengan dot badge semantik modern (`.cv-status-dot-badge`).
   - Kode registrasi monospace yang tegas dan rapi.
   - Tombol "Tinjau Berkas &rarr;" yang seragam dan berestetika tinggi.

---

## 2. Analisis Masalah Visual & Asumsi

### A. Investigasi Akar Masalah
1. **Akar Masalah Tombol Panah (`^`) di Tengah Card Header**:
   - Komponen Blade `<x-gentelella.x-panel>` membungkus kartu utama:
     ```blade
     <div class="x_title">
         <h2>{!! $title !!}</h2>
         <ul class="panel_toolbox">...<button class="collapse-link">^</button>...</ul>
         <div class="clearfix"></div>
     </div>
     ```
   - Di file CSS `gentelella-theme.css` dan `dashboard.blade.php`, kontainer `.x_title` diberi styling flexbox:
     ```css
     .x_title {
         display: flex;
         align-items: center;
         justify-content: space-between;
         gap: 1rem;
     }
     ```
   - Karena `<div class="clearfix"></div>` menjadi elemen anak ke-3 di dalam flexbox, aturan `justify-content: space-between` membagi ruang menjadi 3 titik:
     - Elemen 1 (`h2` Judul): berada di titik awal (0% / kiri).
     - Elemen 2 (`.panel_toolbox` yang berisi tombol `^`): terdorong ke titik tengah (~50% / tengah).
     - Elemen 3 (`.clearfix`): berada di titik akhir (100% / kanan).
   - **Solusi Tepat**: Memberikan `margin-left: auto;` pada `.x_title .panel_toolbox` serta menonaktifkan flow `.clearfix` (`.x_title .clearfix { display: none; }`). Dengan demikian, `.panel_toolbox` akan selalu menempel di sudut kanan kartu terlepas dari lebar layar.

2. **Akar Masalah Layout Bertumpuk (*Nested Card*) & Tidak Rapi**:
   - Saat ini `<x-gentelella.x-panel>` (yang sudah memiliki border dan latar putih) membungkus `<div class="adm-filter-card">` (kartu putih ber-border) dan `<div class="adm-table-card">` (kartu putih ber-border dengan header kedua: *"Daftar Berkas Pendaftaran Masuk - 3 Pengajuan - Menampilkan 1-3 dari 3 berkas"*).
   - Hal ini menyebabkan efek "kartu di dalam kartu di dalam kartu" (*triple card nesting*) yang membuat garis border bertumpuk dan margin dalam tidak konsisten.
   - Di dalam toolbar filter: tombol "Terapkan" (biru), "Reset" (kotak putih tipis), dan "Unduh Bundel (ZIP)" (border tipis) memiliki tinggi dan padding yang tidak seimbang (38px vs 42px).
   - Tombol "Unduh Bundel (ZIP)" terduplikasi: sudah ada di page title atas (`<x-gentelella.page-title>`), namun dimuat ulang di dalam form filter bawah.

3. **Inkonsistensi dengan Standar Cockpit Baru**:
   - 4 kartu metrik atas (`tile_count`) saat ini tidak memiliki ikon SVG dan deskripsi bawah (*subText*) tidak muncul karena parameter salah panggil (`sub="..."` alih-alih `subText="..."`).
   - Avatar peserta di tabel saat ini hanya 1 huruf inisial dengan kotak statis biru muda standar, belum mengadopsi 2 huruf inisial dengan rotasi 5 palet warna dinamis (`cv-avatar-palette-1` s.d. `5`) yang sudah dipakai di Dashboard Cockpit.
   - Status peserta masih badge pill biasa, belum memakai dot badge semantik (`cv-status-dot-badge`).

### B. Asumsi Kerja
- Tidak mengubah logika bisnis backend, controller, query pencarian, filter status, maupun paginasi.
- Endpoint dan parameter GET (`q`, `bulan_magang_id`, `jurusan_id`, `sort`, `status`) tetap berfungsi 100% tanpa perubahan URL.
- Mode Gelap (*Dark Mode*) tetap didukung penuh dengan token warna yang kontras dan bersih.

---

## 3. Evaluasi Opsi Pendekatan Teknis

| Parameter | Opsi 1: Superficial CSS Hack | Opsi 2: Clean Unified Card & Cockpit Parity (Rekomendasi) |
| :--- | :--- | :--- |
| **Struktur Card** | Mempertahankan nesting 3 lapis kartu (`x-panel` > `adm-filter-card` > `adm-table-card`), hanya menambal margin. | **Satu Kartu Terpadu Bersih**. Mengintegrasikan filter dan tabel ke dalam alur card yang mulus tanpa garis ganda. |
| **Posisi Tombol Collapse (`^`)** | Override inline margin pada tombol pendaftaran saja. | **Fix Komprehensif di Komponen & Stylesheet**. Mengunci `.panel_toolbox` dengan `margin-left: auto;` sehingga semua halaman Gentelella mendapatkan perbaikan. |
| **Filter & Action Toolbar** | Tombol tetap tidak rata tingginya. | **Grid Form Rapi & Tombol 42px Terpadu**. Pencarian, 3 dropdown, tombol Terapkan & Reset sejajar pada grid proporsional. |
| **Konsistensi Visual** | Masih menggunakan avatar statis dan badge polos lama. | **100% Selaras Dashboard Cockpit**. Menggunakan 4 kartu metrik ber-ikon, avatar dinamis 2 huruf, dan status dot badge. |
| **Kerapian Kode** | Menyisakan 800 baris inline CSS di dalam blade template. | **Kode Ringkas & Maintainable**. Memanfaatkan token kelas bersama di `dashboard-cockpit-unified.css` & `gentelella-theme.css`. |

**Keputusan**: Memilih **Opsi 2 (Clean Unified Card & Cockpit Parity)** demi hasil akhir yang rapi, profesional, terstruktur, dan mudah dirawat.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/components/gentelella/x-panel.blade.php` | **Modify** | Tambahkan kelas pembantu `ms-auto` / `margin-left: auto` pada wrapper `.panel_toolbox` agar tombol collapse selalu terdorong ke sudut kanan luar, serta amankan elemen clearfix agar tidak mengganggu layout flexbox. |
| `public/css/gentelella-theme.css` | **Modify** | Tambahkan aturan inti `.x_title .panel_toolbox { margin-left: auto; }` dan `.x_title .clearfix { display: none; }` untuk menjamin konsistensi di seluruh tampilan admin. |
| `resources/views/layouts/dashboard.blade.php` | **Modify** | Pastikan blok embedded style Gentelella menyelaraskan `.panel_toolbox` ke sudut kanan. |
| `resources/views/admin/pendaftaran/index.blade.php` | **Modify** | - Rapikan 4 kartu metrik atas (`<x-gentelella.tile-stat>`): tambahkan ikon SVG (dokumen, jam, peringatan SLA) dan ubah prop `sub` menjadi `subText`.<br>- Hapus duplikasi header ganda dan bungkus nested ganda pada tabel.<br>- Rapikan filter toolbar: Baris 1 untuk Status Tabs (Semua Status, Diajukan, dll.), Baris 2 untuk Filter Grid (Pencarian, Bulan, Jurusan, Urutan Data) + Tombol Terapkan & Reset setinggi 42px.<br>- Pindahkan tombol "Unduh Bundel (ZIP)" hanya pada header aksi atas halaman.<br>- Perbarui baris tabel: Avatar 2-huruf dengan palet dinamis (`cv-avatar-palette-*`), Badge Status dengan dot semantik (`cv-status-dot-badge`), dan tombol "Tinjau Berkas &rarr;". |

---

## 5. Langkah Implementasi (Setelah Persetujuan)

### Tahap 1: Perbaikan Posisi Tombol Panah Collapse (`^`)
- [x] 1. Edit `resources/views/components/gentelella/x-panel.blade.php`:
  - Berikan styling inline/kelas `margin-left: auto;` pada `.panel_toolbox`.
  - Pastikan `<div class="clearfix"></div>` tidak memecah flex flow.
- [x] 2. Edit `public/css/gentelella-theme.css` dan `resources/views/layouts/dashboard.blade.php`:
  - Tambahkan deklarasi:
    ```css
    .x_title .panel_toolbox {
        margin-left: auto;
    }
    .x_title .clearfix {
        display: none;
    }
    ```

### Tahap 2: Revamp 4 Kartu Metrik KPI Atas
- [x] 3. Perbarui pemanggilan `<x-gentelella.tile-stat>` pada `resources/views/admin/pendaftaran/index.blade.php`:
  - Total Pengajuan: Ikon Dokumen SVG biru (`<svg ...>`), prop `subText="Semua riwayat pendaftaran"`.
  - Menunggu Ditinjau: Ikon Jam / Verifikasi SVG amber, prop `subText="Prioritas antrean FIFO"`.
  - Perlu Revisi Dokumen: Ikon Alert Triangle SVG oranye, prop `subText="Menunggu respon peserta"`.
  - Tertahan > 3 Hari: Ikon Alert Circle SVG merah, prop `subText="Batas SLA verifikasi target"`.
  - Terapkan layout grid 4 kolom sejajar yang responsif di desktop.

### Tahap 3: Penataan Ulang Toolbar Filter & Pencarian
- [x] 4. Tata Zona 1 (Segmented Status Pills):
  - Buat track status horizontal yang lega dengan border-radius 10px, background halus `#F1F5F9`, dan tombol aktif bernuansa biru elegan `#0641CC` dengan counter kontras.
- [x] 5. Tata Zona 2 (Grid Form Filter & Actions):
  - Susun form menjadi CSS Grid responsif:
    - Input Pencarian (Calon Peserta, universitas, no reg): `minmax(240px, 1.8fr)` dengan ikon search di dalam input.
    - Select Bulan Magang: `minmax(160px, 1.2fr)`.
    - Select Jurusan: `minmax(180px, 1.2fr)`.
    - Select Urutan Data (FIFO / Terbaru): `minmax(160px, 1fr)`.
    - Action Buttons: `auto` dengan tinggi tepat `42px` yang sejajar presisi dengan input.
  - Hapus tombol "Unduh Bundel (ZIP)" dari dalam form filter agar tidak berdesakan dan tidak duplikat dengan tombol utama di header atas.

### Tahap 4: Refactor Tabel & Harmonisasi Cockpit
- [x] 6. Hapus header ganda *"Daftar Berkas Pendaftaran Masuk"* dan hilangkan border ganda `.adm-table-card` di dalam `x-panel`.
- [x] 7. Harmonisasi baris data tabel:
  - **Avatar**: Hitung 2 huruf inisial nama peserta dan terapkan kelas palet acak berbasis ID (`cv-avatar-palette-{{ ($item->id % 5) + 1 }}`).
  - **No. Registrasi**: Tampilkan kode `#REG-...` dalam monospace chip biru lembut yang proporsional dengan tanggal diajukan di bawahnya.
  - **Status & SLA**: Gunakan `.cv-status-dot-badge` dengan varian status (`success` untuk diterima, `warning` untuk diajukan, `danger` untuk melebihi SLA / ditolak).
  - **Aksi**: Tombol `Tinjau Berkas &rarr;` dengan padding proporsional dan border-radius modern.
  - **Paginasi**: Baris footer paginasi yang bersih dan terpadu di dasar tabel.

---

## 6. Strategi Testing

1. **Automated Feature & Visual Tests**:
   - Jalankan test suite verifikasi pendaftaran:
     ```powershell
     php artisan test --filter="AdminChildPagesVisualTest|AdminSuperAdminPortalTest|AdminTerminologyDesignSystemTest|PendaftaranPesertaTest"
     ```
   - Pastikan seluruh assertion (seperti teks *"Verifikasi Berkas Pendaftaran Peserta"*, *"Tinjau Berkas"*, nomor registrasi, dan filter status) tetap bernilai **PASS (100%)**.

2. **Manual Visual Verification**:
   - Buka URL `http://127.0.0.1:8000/admin/pendaftaran` (dan `http://magkom.test/admin/pendaftaran`).
   - Verifikasi poin-poin utama:
     - [ ] Tombol panah collapse `^` berada tepat di pojok kanan atas kartu, tidak lagi melayang di tengah.
     - [ ] 4 kartu metrik KPI di baris atas tertata rapi dengan ikon dan subjudul penjelas.
     - [ ] Form filter dan tombol aksi memiliki tinggi yang seragam (42px) dan tidak ada duplikasi tombol unduh.
     - [ ] Tabel terlihat bersih, menyatu tanpa border ganda di dalam card, dan avatar menggunakan 2 huruf inisial berpalet warna modern.
     - [ ] Tampilan responsif di layar desktop, tablet, dan smartphone.

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Tingkat Risiko | Mitigasi |
| :--- | :--- | :--- |
| Tombol collapse pada kartu lain terpengaruh perubahan `.x_title` | Rendah | Memberikan `margin-left: auto;` pada `.panel_toolbox` justru merupakan perbaikan standar UI untuk seluruh x-panel Gentelella, sehingga semua kartu di dashboard menjadi seragam di pojok kanan. |
| Parameter filter form GET hilang saat disubmit | Nol | Seluruh nama atribut input (`q`, `bulan_magang_id`, `jurusan_id`, `sort`, `status`) dipertahankan secara identik. |
| Kerusakan tampilan pada Dark Mode | Rendah | Menggunakan variabel CSS semantik yang sudah mendukung `[data-theme="dark"]` (`cv-status-dot-badge`, `cv-avatar-palette-*`, token border dark). |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Tombol panah kecil `^` sukses berpindah ke sudut kanan paling luar card header.
- [x] Tata letak halaman rapi, terstruktur, bebas dari tumpukan kartu ganda (*nested cards*), dan elemen form sejajar.
- [x] 4 kartu metrik KPI atas memiliki ikon visual dan teks deskripsi yang lengkap.
- [x] Tabel pendaftaran memiliki visual yang konsisten dengan Dashboard Cockpit (avatar dinamis 2-huruf, badge status dengan dot indikator, monospace registrasi chip).
- [x] Seluruh automated test suite terkait verifikasi berkas pendaftaran lulus 100%.
- [x] Tampilan halaman terverifikasi di browser dalam kondisi bersih dan elegan.

