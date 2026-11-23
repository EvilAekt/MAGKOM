# Rencana Kerja: Penyempurnaan Tata Letak, Kontrol Aksi, dan Harmonisasi Visual Manajemen Kuota

> **Status:** SELESAI (COMPLETED)  
> **Tanggal:** 23 November 2026  
> **Target Branch:** `test-2`  
> **Dokumen Terkait:** `docs/01-PRD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `.agents/rules/AGENTS.md`  
> **Referensi Gambar Permintaan Pengguna:**
> - **SS 1 (Tombol Hijau "Buka bulan"):** Tombol `[ ✓ Buka bulan ]` di dalam Hero Banner Biru (dihapus karena fungsi aktivasi/penerbitan sudah tersedia di dalam modal *Ubah bulan*).
> - **SS 2 (Judul "Daftar Bulan Magang"):** Judul heading tabel riwayat periode magang (diselaraskan tipografi dan ukurannya dengan heading kartu *Kuota per bidang*).
> - **SS 3 (Bar Selector Bulan Magang):** Bar selector `[ 📅 Bulan Magang: [Dropdown] ]` (diberikan margin/spacing proporsional terhadap tabel di bawahnya, dan dipindahkan ke luar tab agar tampil di kedua tab).
> - **SS 4 (Tombol "Tambah Bulan Magang"):** Tombol biru `[ + Tambah Bulan Magang ]` pada header tabel Daftar Bulan Magang (dihapus karena redundant dengan tombol `[ + Buat bulan baru ]` di Hero Banner).

---

## 1. 🎯 Tujuan & Analisis 5 Poin Permintaan Pengguna

Berdasarkan tinjauan visual dan arahan pengguna:

| No | Poin Permintaan Pengguna | Analisis Teknis & Solusi Rekayasa |
| :--- | :--- | :--- |
| **1** | **Hapus tombol hijau "Buka bulan" (SS 1)** | Di dalam Hero Banner Biru, tombol hijau `.btn-hero-action-success` (`[ ✓ Buka bulan ]`) dihapus. Admin dapat langsung mengaktifkan atau memperbarui status pendaftaran melalui tombol `[ ✏️ Ubah bulan ]` yang membuka modal pengaturan periode lengkap. |
| **2** | **Sesuaikan font & ukuran "Daftar Bulan Magang" seperti "Kuota per bidang" (SS 2)** | Heading kartu *Kuota per bidang* menggunakan komponen `<x-gentelella.x-panel>` dengan class `.x_title h2` (`font-size: 1rem; font-weight: 700; color: #334155; line-height: 1.25rem; letter-spacing: -0.01em;`). Heading `.table-bulan-title` saat ini berukuran `1.125rem` warna `#0F172A`. Kita akan menyelaraskan styling `.table-bulan-title` di `kuota-index.css` agar 100% identik dengan heading *Kuota per bidang*. |
| **3** | **Berikan space/jarak antara bar selector bulan magang (SS 3) dan tabel di bawahnya** | Saat ini elemen `.month-switch-strip` tidak memiliki `margin-bottom` sehingga menempel langsung dengan border kartu tabel di bawahnya. Kita akan menambahkan `margin-bottom: 1.5rem;` dan `margin-top: 1rem;` agar menghasilkan ruang bernapas (breathing room) visual yang elegan dan bersih. |
| **4** | **Hapus tombol "Tambah Bulan Magang" (SS 4)** | Tombol `[ + Tambah Bulan Magang ]` di header kartu `table-bulan-card` dihapus dari Blade. Tindakan pembuatan bulan baru difokuskan sepenuhnya melalui tombol utama `[ ➕ Buat bulan baru ]` yang telah tersedia di Hero Banner Biru atas. |
| **5** | **Tampilkan bar selector bulan magang (SS 3) saat tab "Daftar Bulan Magang" diklik** | Saat ini `.month-switch-strip` diletakkan di dalam container `#panel-kuota-bidang`, sehingga ketika user berpindah ke `#panel-daftar-bulan`, bar selector tersebut ikut tersembunyi (`display: none`). Solusinya: pindahkan `.month-switch-strip` ke tingkat atas (di bawah tab navigasi kategori dan di atas kedua panel), serta perbarui input tersembunyi `name="tab"` pada form dropdown agar tab aktif tetap dipertahankan saat opsi bulan diganti. Dengan demikian, bar selector SS 3 dan Hero Banner tetap tampil di kedua tab navigasi. |

---

## 2. 📊 Visualisasi Tata Letak (Hierarki Arsitektur Antarmuka)

```text
[ CONTAINER UTAMA: .adm-kuota-revamp-wrap ]
│
├── 1. HERO BANNER BIRU: Bulan & Kuota (Signature Executive Summary)
│   ├── Kiri:
│   │   ├── Eyebrow: "BULAN & KUOTA" -> Judul: "November 2026 [Ditutup]"
│   │   ├── Grid Indikator: Tanggal buka, Tanggal tutup, Mulai magang
│   │   └── Baris Tombol Aksi:
│   │       ├── [ ✏️ Ubah bulan ] (Frosted glass outline button)
│   │       └── [ ➕ Buat bulan baru ] (Crisp white solid button)
│   │       (❌ Tombol hijau "Buka bulan" SS 1 telah dihapus)
│   └── Kanan:
│       └── Stat Card Okupansi: "50 / 110 peserta" + Progress bar + Status countdown
│
├── 2. TAB NAVIGASI KATEGORI (Tepat di bawah Hero Banner)
│   └── [ 📅 Daftar Bulan Magang (X) ]   [ 🗂️ Kuota Per Bidang (Y) ]
│
├── 3. BAR SELECTOR BULAN MAGANG (SS 3) - DIPINDAHKAN KE SINI (GLOBAL UNTUK KEDUA TAB)
│   └── [ 📅 Bulan Magang: [ November 2026 (Aktif) ▾ ] ]
│       (✨ Memiliki margin-bottom: 1.5rem untuk space lega ke tabel di bawahnya)
│
└── 4. KONTEN PANEL DINAMIS:
    │
    ├── JIKA TAB "DAFTAR BULAN MAGANG" DIPILIH:
    │   └── Kartu: Daftar Bulan Magang
    │       ├── Header: Judul "Daftar Bulan Magang" (font 1rem, tebal 700, warna #334155 identik Kuota per bidang)
    │       │   (❌ Tombol "+ Tambah Bulan Magang" SS 4 telah dihapus)
    │       └── Tabel Riwayat & Jadwal Periode Magang + Status + Aksi Edit / Toggle Aktif
    │
    └── JIKA TAB "KUOTA PER BIDANG" DIPILIH:
        └── Kartu: Kuota per bidang
            ├── Header: "Kuota per bidang" + [ + Tambah kuota ]
            └── Tabel Formasi Unit Kerja Dinas: Bidang, Jurusan, Jenjang, Kuota, Kapasitas, Aksi
```

---

## 3. 📋 Justifikasi Pengecualian Perubahan Blade (Aturan Section 3.3 AGENTS.md)

1. **Mengapa perubahan tidak dapat diselesaikan tanpa modifikasi Blade?**
   - Menghapus elemen tombol `[ ✓ Buka bulan ]` (SS 1) dan `[ + Tambah Bulan Magang ]` (SS 4) memerlukan pembersihan markup Blade terkait.
   - Memastikan bar selector bulan magang (SS 3) tampil pada tab `Daftar Bulan Magang` (Poin 5) memerlukan pemindahan elemen semantik `.month-switch-strip` dari dalam `#panel-kuota-bidang` ke luar panel (di atas kedua panel).
2. **File Blade yang Disentuh**:
   - [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php)
3. **Kepatuhan Larangan Inti**:
   - **TIDAK ADA** tag `<style>` atau `<script>` yang ditulis di Blade.
   - **TIDAK ADA** inline event handler (`onclick`, dsb.).
   - Seluruh styling spacing dan tipografi dikelola di `resources/css/pages/admin/kuota-index.css`.
   - Sinkronisasi state tab pada form dropdown dikelola di `resources/js/pages/admin/kuota-index.js`.

---

## 4. 🗂️ Rincian Perubahan File

| No | File | Jenis Perubahan | Detail Modifikasi |
| :--- | :--- | :--- | :--- |
| 1 | [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php) | **Modify** | • Hapus tombol hijau `publish-draft` (`Buka bulan`).<br>• Hapus tombol `btn-trigger-tambah-bulan` (`Tambah Bulan Magang`) di header tabel periode.<br>• Pindahkan blok `.month-switch-strip` ke atas kedua panel (di bawah navigasi kategori).<br>• Tambahkan input tersembunyi `id="monthSwitchTabInput"` untuk menjaga tab aktif saat submit filter. |
| 2 | [`resources/css/pages/admin/kuota-index.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/kuota-index.css) | **Modify** | • Sesuaikan `.table-bulan-title` (`font-size: 1rem; font-weight: 700; color: #334155; line-height: 1.25rem; letter-spacing: -0.01em;`).<br>• Tambahkan `margin-top: 1rem; margin-bottom: 1.5rem;` pada `.month-switch-strip` untuk memberi jarak lega ke tabel bawahnya. |
| 3 | [`resources/js/pages/admin/kuota-index.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/admin/kuota-index.js) | **Modify** | • Perbarui fungsi `switchKuotaTab` agar memperbarui nilai input `#monthSwitchTabInput` sesuai tab yang sedang aktif (`bulan` atau `kuota`). |
| 4 | [`tests/Feature/AdminKuotaRevampTest.php`](file:///d:/laragon/www/MAGKOM/tests/Feature/AdminKuotaRevampTest.php) | **Modify** | • Perbarui assertion test: hapus pengecekan tombol `Buka bulan` & `Tambah Bulan Magang`.<br>• Pastikan assertion memvalidasi keberadaan `month-switch-strip` dan tombol `Buat bulan baru` pada kedua tab. |
| 5 | `docs/plans/2026-11-23-penyempurnaan-tata-letak-dan-kontrol-manajemen-kuota.md` | **Create** | • Dokumen rencana kerja komprehensif, arsitektur, dan checklist validasi. |

---

## 5. 🚀 Rincian Checklist Implementasi Terurut

### Tahap 1: Pembaruan Styling CSS di `kuota-index.css`
- [x] **1.1**: Sesuaikan selektor `.table-bulan-title` agar font-size `1rem`, font-weight `700`, color `#334155`, dan line-height `1.25rem` selaras dengan `.x_title h2`.
- [x] **1.2**: Tambahkan `margin-top: 1rem; margin-bottom: 1.5rem;` pada `.month-switch-strip` agar terdapat space yang proporsional ke kartu tabel di bawahnya.

### Tahap 2: Penataan Ulang & Pembersihan Markup di `index.blade.php`
- [x] **2.1**: Hapus tombol hijau `btn-hero-action-success` (`Buka bulan`) dari `.hero-banner-actions`.
- [x] **2.2**: Pindahkan blok `.month-switch-strip` dari dalam `#panel-kuota-bidang` ke posisi di antara `.kuota-category-nav-wrapper` dan `#panel-daftar-bulan`.
- [x] **2.3**: Berikan atribut `id="monthSwitchTabInput"` pada `<input type="hidden" name="tab" value="{{ $activeTab }}">` di dalam form dropdown.
- [x] **2.4**: Hapus tombol `btn-trigger-tambah-bulan` (`Tambah Bulan Magang`) pada header `table-bulan-header`.

### Tahap 3: Pembaruan JavaScript di `kuota-index.js`
- [x] **3.1**: Pada fungsi `switchKuotaTab(tabName)`, tambahkan pembaruan nilai elemen `#monthSwitchTabInput` agar query string submit otomatis menyesuaikan tab yang sedang dibuka pengguna.

### Tahap 4: Pembaruan Automated Tests di `AdminKuotaRevampTest.php`
- [x] **4.1**: Sesuaikan assertion pada `test_admin_can_publish_draft_bulan_and_future_period_shows_terjadwal_status` dan `test_kuota_page_displays_two_category_tabs_and_integrated_bulan_magang_table` agar mencerminkan penghapusan tombol `Buka bulan` dan `Tambah Bulan Magang`, serta memastikan `Buat bulan baru` dan selector bar tetap hadir.

### Tahap 5: Verifikasi Kualitas & Quality Gates
- [x] **5.1**: Jalankan kompilasi aset frontend: `npm run build`.
- [x] **5.2**: Jalankan linter Pint: `vendor/bin/pint --test tests/Feature/AdminKuotaRevampTest.php`.
- [x] **5.3**: Jalankan seluruh feature test: `php artisan test tests/Feature/AdminKuotaRevampTest.php tests/Feature/AdminMasterDataFeatureMigrationTest.php` menggunakan PHP 8.3 aktual.
- [x] **5.4**: Lakukan pengujian visual dan verifikasi alur UI pada kedua tab.

---

## 6. 🛡️ Analisis Risiko & Mitigasi

| Potensi Risiko | Tingkat | Mitigasi |
| :--- | :--- | :--- |
| **User mengganti bulan saat berada di tab "Daftar Bulan Magang"** | Rendah | Form GET dropdown membawa `input type="hidden" name="tab" id="monthSwitchTabInput" value="{{ $activeTab }}"`. Ketika user memilih bulan baru, halaman me-refresh dan tetap berada di tab `Daftar Bulan Magang`. |
| **Regresi Test Otomatis akibat tombol yang dihapus** | Rendah | Test suite `AdminKuotaRevampTest` diperbarui secara presisi untuk memverifikasi alur baru tanpa tombol duplikat. |
| **Konsistensi Visual di Dark Mode** | Rendah | `.month-switch-strip` dan `.table-bulan-title` sudah memiliki rule `[data-theme="dark"]` yang terdefinisi di `kuota-index.css`. |

---

## 7. ✅ Kriteria Selesai (Definition of Done)

- [x] Tombol hijau `[ ✓ Buka bulan ]` (SS 1) telah dihapus dari Hero Banner Biru.
- [x] Tipografi judul `Daftar Bulan Magang` (SS 2) berukuran `1rem`, tebal `700`, dan warna `#334155`, selaras dengan judul `Kuota per bidang`.
- [x] Bar selector bulan magang (SS 3) memiliki jarak vertikal (`margin-bottom: 1.5rem`) yang lega terhadap kartu tabel di bawahnya.
- [x] Tombol `[ + Tambah Bulan Magang ]` (SS 4) telah dihapus dari header kartu tabel periode magang.
- [x] Bar selector bulan magang (SS 3) dan Hero Banner Biru tetap tampil ketika pengguna mengklik tab `Daftar Bulan Magang`.
- [x] `npm run build` sukses 100%, linter Pint lulus, dan semua test PHPUnit terkait lulus tanpa regresi.

