# Rencana Kerja: Integrasi Tabel Periode Magang ke Manajemen Kuota Administrasi

> **Status:** SELESAI & TERVERIFIKASI (COMPLETED & VERIFIED)  
> **Tanggal:** 23 November 2026  
> **Target Branch:** `test-2`  
> **Dokumen Terkait:** `docs/01-PRD-MagKom.md`, `docs/02-TDD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `.agents/rules/AGENTS.md`  
> **Referensi Gambar:** 
> - **SS 1 (Tabel Periode Magang):** Tabel *Daftar Bulan Magang* dengan kolom Bulan Magang, Jadwal Buka Pendaftaran, Override Status, Status Aktif, dan Aksi (Edit, Toggle Aktif).
> - **SS 2 (Tampilan Manajemen Kuota Eksisting):** Hero Banner Biru *Bulan & Kuota*, Filter Dropdown Bulan Magang, dan Card *Kuota per bidang*.

---

## 1. 🎯 Tujuan & Ringkasan Kebutuhan

Menata ulang antarmuka administrasi pada modul **Manajemen Kuota** (`/admin/kuota`) agar menjadi pusat pengelolaan periode dan alokasi kuota terpadu dengan spesifikasi:
1. **Memindahkan Tabel Periode Magang (SS 1)**: Memindahkan tabel *Daftar Bulan Magang* yang saat ini berada di menu Periode Magang (`/admin/bulan`) ke dalam halaman Manajemen Kuota (`/admin/kuota`).
2. **Navigasi 2 Kategori (Segmented Pill Switcher)**: Menambahkan 2 tombol navigasi kategori di bagian atas konten:
   - **Tombol Kategori 1**: `Daftar Bulan Magang` (menampilkan tabel riwayat seluruh periode bulan magang, status operasional, jadwal, dan tindakan konfigurasi).
   - **Tombol Kategori 2**: `Kuota Per Bidang` (menampilkan ringkasan hero banner bulan terpilih, filter dropdown bulan, dan rincian alokasi kuota per unit kerja dinas sesuai Gambar SS 2).
3. **Harmonisasi Tampilan Eksisting (SS 2)**: Menjaga seluruh komponen, interaksi modal, KPI strip, dan tabel kuota per bidang tetap utuh tanpa *regresi visual*, *breaking change*, maupun perubahan alur bisnis pendaftaran.

---

## 2. 🔍 Analisis Komparasi Visual (SS 1 vs SS 2)

### 2.1 Gambar SS 1: Tabel Daftar Bulan Magang (Dari Menu Periode Magang)
- **Komponen Utama**:
  - Judul Panel: `Daftar Bulan Magang` dilengkapi ringkasan total entri.
  - Kolom Tabel:
    1. **Bulan Magang**: Ikon kalender + Nama Bulan (contoh: *Desember 2026*, *November 2026*, *Agustus 2026*) beserta label `ID #xxx`.
    2. **Jadwal Buka Pendaftaran**: Rentang tanggal pendaftaran (contoh: `01 Nov 2026 s/d 25 Nov 2026`).
    3. **Override Status**: Badge neutral `• Otomatis (Jadwal)` atau badge override manual.
    4. **Status Aktif**: Pill badge dengan status dot `• Aktif` (hijau) atau `• Nonaktif` (merah).
    5. **Aksi**: Tombol `Edit` (outline dengan ikon pensil) dan tombol `Nonaktifkan`/`Aktifkan` (danger/success border).
  - Footer Paginasi: Info `Menampilkan 1 sampai 3 dari 3 entri` beserta link paginasi Eloquent.
  - Aksi Tambahan: Tombol `+ Tambah Bulan Magang` yang membuka modal pembuatan periode magang baru.

### 2.2 Gambar SS 2: Tampilan Manajemen Kuota Eksisting
- **Komponen Utama**:
  - Header Topbar: `Manajemen Kuota Magang`.
  - Signature Hero Banner Biru:
    - Informasi bulan terpilih (*November 2026* + pill status *Ditutup*/*Dibuka*).
    - Grid meta: Tanggal buka, Tanggal tutup, Mulai magang.
    - Stat box kanan: Kuota terisi `50 / 110 peserta`, progress bar okupansi global, status countdown pendaftaran.
  - Month Switcher Strip:
    - Dropdown filter: `Bulan Magang [November 2026 (November 2026) (Aktif) v]`.
    - Tombol Aksi: `Ubah bulan` dan `+ Buat bulan baru`.
  - Panel Konten: Card `Kuota per bidang` dengan tombol `+ Tambah kuota` dan tabel alokasi formasi unit kerja dinas (*Aplikasi Informatika*, *IKP*, *ITI*, *Sekretariat*).

---

## 3. ⚖️ Evaluasi Opsi Pendekatan Arsitektur

| Parameter Evaluasi | Opsi 1: Unified View dengan Segmented Pill Switcher (Rekomendasi) | Opsi 2: Sub-Routing Terpisah (`/admin/kuota/bulan` & `/admin/kuota/bidang`) |
| :--- | :--- | :--- |
| **Mekanisme Tampilan** | Single page URL `/admin/kuota` dengan query param opsional `?tab=bulan` atau `?tab=kuota`. Panel dirender server-side dan di-switch seketika via JavaScript client-side tanpa reload halaman. | Dua endpoint controller dan dua file view Blade terpisah dengan sub-navbar statis. |
| **Pengalaman Pengguna (UX)** | **Sangat Cepat & Responsif**: Admin dapat beralih antara melihat daftar bulan dan mengatur alokasi kuota secara instan (0ms delay tanpa round-trip HTTP). | Terdapat jeda reload (full page load) setiap kali admin berpindah tab. |
| **Backward Compatibility** | **100% Terjaga**: URL `/admin/kuota` default tetap membuka tampilan kuota eksisting sehingga seluruh Feature Test (`AdminKuotaRevampTest`, `AdminChildPagesVisualTest`) tetap hijau tanpa modifikasi. Route `/admin/bulan` dapat dialihkan secara halus ke `/admin/kuota?tab=bulan`. | Berpotensi merusak route contract dan memerlukan perubahan assertion pada banyak test suite. |
| **Kompleksitas Kode** | **Rendah - Terkelola**: Logika tab terisolasi di ES Module `kuota-index.js`, styling terpusat di `kuota-index.css`. Query controller dieksekusi secara efisien melalui `KuotaIndexQuery`. | Duplikasi boilerplate Blade layout, subbar, dan manajemen state routing. |

> **Keputusan Arsitektur**: Memilih **Opsi 1 (Unified View dengan Segmented Pill Switcher)** karena memberikan transisi antarmuka paling mulus sesuai Sleman Design System (paritas dengan tab *Sudah Ditempatkan* vs *Belum Ditempatkan* di modul Penempatan Peserta), ramah a11y (WCAG 2.1 AA `role="tablist"`), dan tidak merusak kontrak pengujian yang ada.

---

## 4. 📐 Detail Desain & Spesifikasi Teknis

### 4.1 Tata Letak Navigasi Kategori (Segmented Pill Tab Bar)
Diletakkan di bagian atas container halaman `admin.kuota.index`, tepat di atas area konten:
```html
<div class="kuota-category-nav-wrapper">
    <div class="kuota-nav-tabs" role="tablist" aria-label="Kategori Manajemen Kuota">
        <!-- Tab 1: Daftar Bulan Magang -->
        <button type="button" 
                id="tab-btn-bulan" 
                class="kuota-tab-btn {{ $activeTab === 'bulan' ? 'active' : '' }}" 
                data-action="switch-kuota-tab" 
                data-tab="bulan"
                role="tab" 
                aria-selected="{{ $activeTab === 'bulan' ? 'true' : 'false' }}"
                aria-controls="panel-daftar-bulan">
            <svg class="kuota-tab-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
            <span>Daftar Bulan Magang</span>
            <span class="kuota-tab-badge" id="badge-total-bulan">{{ $totalBulan }}</span>
        </button>

        <!-- Tab 2: Kuota Per Bidang -->
        <button type="button" 
                id="tab-btn-kuota" 
                class="kuota-tab-btn {{ $activeTab === 'kuota' ? 'active' : '' }}" 
                data-action="switch-kuota-tab" 
                data-tab="kuota"
                role="tab" 
                aria-selected="{{ $activeTab === 'kuota' ? 'true' : 'false' }}"
                aria-controls="panel-kuota-bidang">
            <svg class="kuota-tab-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="3" y1="9" x2="21" y2="9"></line>
                <line x1="9" y1="21" x2="9" y2="9"></line>
            </svg>
            <span>Kuota Per Bidang</span>
            <span class="kuota-tab-badge" id="badge-total-bidang">{{ count($bidangList) }}</span>
        </button>
    </div>
</div>
```

### 4.2 Struktur Panel Konten (Two Interchangeable Panels)
1. **Panel 1 (`#panel-daftar-bulan`)**:
   - Menampilkan Card Tabel *Daftar Bulan Magang* persis sesuai Gambar SS 1.
   - Header panel memuat judul card dan tombol aksi `+ Tambah Bulan Magang` (membuka modal tambah bulan).
   - Menampilkan kolom: Bulan Magang (Nama & ID), Jadwal Buka Pendaftaran, Override Status, Status Aktif (Badge Hijau/Merah), dan Aksi (Tombol Edit & Tombol Toggle Aktif/Nonaktif).
   - Menampilkan paginasi 10 entri per halaman (`$daftarBulanPaginator->links()`).
   - Menyertakan modal tambah bulan magang, modal edit bulan magang, dan modal konfirmasi toggle status aktif (mempertahankan proteksi BR-12 & Audit Trail).
2. **Panel 2 (`#panel-kuota-bidang`)**:
   - Menampilkan antarmuka eksisting Manajemen Kuota persis sesuai Gambar SS 2.
   - Signature Hero Banner Biru (*Bulan & Kuota*).
   - Month Switcher Strip (Dropdown filter bulan magang, tombol *Ubah bulan*, tombol *Buat bulan baru*).
   - Card Tabel *Kuota per bidang* (+ tombol *Tambah kuota* dan tombol *Ubah* alokasi per bidang dinas).

### 4.3 Aliran Data Backend (`KuotaIndexQuery` & `KuotaController`)
- Menyesuaikan query pada `App\Queries\Admin\Kuota\KuotaIndexQuery`:
  - Mengambil data paginasi tabel bulan:  
    `$daftarBulanPaginator = BulanMagang::orderBy('bulan_mulai', 'desc')->paginate(10)->withQueryString();`
  - Menyediakan nilai statistik:
    `$totalBulan = BulanMagang::count();`  
    `$statusEnum = StatusKetersediaanEnum::cases();`
  - Menentukan `$activeTab = request()->query('tab', 'kuota');` (validasi: `in:kuota,bulan`).
- Menjaga `$bulanList` eksisting sebagai Eloquent Collection untuk opsi dropdown selector pada tab *Kuota Per Bidang*.

---

## 5. 📋 Justifikasi Pengecualian Perubahan Blade (Aturan 3.3 AGENTS.md)

Sesuai aturan permanen repositori **AGENTS.md Section 3.3 (Pengecualian Perubahan Blade)**:
1. **Mengapa perubahan tidak dapat diselesaikan tanpa markup baru?**
   - Penambahan 2 tombol navigasi kategori (*Daftar Bulan Magang* dan *Kuota Per Bidang*) serta wadah tabel daftar bulan memerlukan penambahan elemen semantik container (`div.kuota-category-nav-wrapper`, `button[role="tab"]`, dan container panel `#panel-daftar-bulan`).
2. **File Blade dan bagian yang diubah**:
   - `resources/views/admin/kuota/index.blade.php`:
     - Menambahkan container navigasi tab kategori di atas konten.
     - Membungkus hero banner, control strip, dan tabel kuota per bidang ke dalam `<div id="panel-kuota-bidang">`.
     - Menambahkan `<div id="panel-daftar-bulan">` yang memuat tabel *Daftar Bulan Magang* (SS 1), tombol tambah bulan, footer paginasi, serta modal edit & konfirmasi toggle bulan.
3. **Kepatuhan Larangan**:
   - **TIDAK ADA** tag `<style>` atau `<script>` yang dimasukkan ke dalam Blade.
   - **TIDAK ADA** inline event handler (`onclick`, dsb.).
   - Seluruh styling ditambahkan ke `resources/css/pages/admin/kuota-index.css`.
   - Seluruh interaksi ditambahkan ke `resources/js/pages/admin/kuota-index.js`.

---

## 6. 🗂️ Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Queries/Admin/Kuota/KuotaIndexQuery.php` | **Modify** | Menambahkan data `$daftarBulanPaginator`, `$totalBulan`, `$statusEnum`, dan `$activeTab` ke dalam return data kuota. |
| `app/Http/Controllers/Admin/KuotaController.php` | **Modify** | Memastikan handler update & toggle status bulan magang terhubung dengan validasi bisnis BR-12 dan audit trail. |
| `resources/views/admin/kuota/index.blade.php` | **Modify** | Menambahkan markup navigasi 2 kategori tab dan container panel tabel *Daftar Bulan Magang* (SS 1). |
| `resources/css/pages/admin/kuota-index.css` | **Modify** | Menambahkan styling komponen `.kuota-nav-tabs`, `.kuota-tab-btn`, `.kuota-tab-badge`, animasi transisi tab, serta penataan tabel Daftar Bulan Magang. |
| `resources/js/pages/admin/kuota-index.js` | **Modify** | Mengintegrasikan modul tab switching (zero-reload dengan update query param), modal handler edit bulan, dan konfirmasi toggle aktif/nonaktif. |
| `tests/Feature/AdminKuotaRevampTest.php` | **Modify / Add** | Menambahkan test assertion untuk memastikan kedua tab kategori ter-render dan tabel daftar bulan magang dapat diakses. |

---

## 7. 🚀 Rincian Checklist Implementasi Terurut

### Tahap 1: Persiapan Data & Backend Query
- [x] **1.1**: Perbarui `app/Queries/Admin/Kuota/KuotaIndexQuery.php` untuk mengambil `$daftarBulanPaginator` (10 per halaman, order by `bulan_mulai desc`) dan data enum status.
- [x] **1.2**: Pastikan route controller `KuotaController` mampu menerima parameter `?tab=bulan` dan meneruskan ke view tanpa mengganggu fungsionalitas filter bulan eksisting.

### Tahap 2: Styling CSS (Sesuai Aturan Repositori)
- [x] **2.1**: Buka `resources/css/pages/admin/kuota-index.css`.
- [x] **2.2**: Tambahkan rule CSS untuk `.kuota-category-nav-wrapper`, `.kuota-nav-tabs`, `.kuota-tab-btn`, `.kuota-tab-btn.active`, dan `.kuota-tab-badge`.
- [x] **2.3**: Tambahkan penataan responsif untuk mobile dan tablet agar tombol tab menyesuaikan lebar layar secara fleksibel.

### Tahap 3: Interaksi JavaScript ES Modules
- [x] **3.1**: Buka `resources/js/pages/admin/kuota-index.js`.
- [x] **3.2**: Implementasikan fungsi `switchKuotaTab(tabName)` yang mengatur visibilitas `#panel-kuota-bidang` dan `#panel-daftar-bulan`, atribut `aria-selected`, serta `history.replaceState`.
- [x] **3.3**: Hubungkan modal tambah bulan, modal edit bulan, dan modal konfirmasi toggle status aktif via event delegation bersih tanpa inline handler.

### Tahap 4: Penyesuaian Markup Blade
- [x] **4.1**: Buka `resources/views/admin/kuota/index.blade.php`.
- [x] **4.2**: Sisipkan komponen navigasi 2 tab kategori di bawah breadcrumbs.
- [x] **4.3**: Bungkus tampilan eksisting (Hero Banner, Switcher Strip, Tabel Kuota per bidang) ke dalam `#panel-kuota-bidang`.
- [x] **4.4**: Tambahkan komponen panel `#panel-daftar-bulan` yang memuat tabel *Daftar Bulan Magang* persis sesuai SS 1 lengkap dengan badge status dan tombol aksi.
- [x] **4.5**: Sertakan modal edit bulan dan modal konfirmasi penonaktifan bulan.

### Tahap 5: Verifikasi, Build Aset, & Pengujian
- [x] **5.1**: Jalankan `npm run build` untuk memverifikasi kompilasi Vite CSS & JS tanpa error.
- [x] **5.2**: Jalankan pengujian PHPUnit terkait: `vendor/bin/phpunit tests/Feature/AdminKuotaRevampTest.php`.
- [x] **5.3**: Jalankan pemeriksaan linter: `vendor/bin/pint --test`.
- [x] **5.4**: Lakukan pengujian manual di browser:
  - Klik tab *Daftar Bulan Magang*: tabel SS 1 muncul seketika, modal edit dan toggle berfungsi.
  - Klik tab *Kuota Per Bidang*: tampilan SS 2 muncul seketika, filter dropdown dan ubah alokasi formasi berfungsi normal.

---

## 8. 🧪 Strategi Testing Komprehensif

### 8.1 Skenario Happy Path
1. **Render Awal Halaman Kuota**: Admin mengakses `/admin/kuota`. Halaman menampilkan 2 tombol tab kategori (*Daftar Bulan Magang* dan *Kuota Per Bidang*). Tab aktif default adalah *Kuota Per Bidang* (menampilkan Hero Banner dan tabel formasi bidang).
2. **Navigasi ke Tab Daftar Bulan**: Mengakses `/admin/kuota?tab=bulan` atau mengklik tab *Daftar Bulan Magang*. Panel berganti menampilkan tabel periode magang (SS 1) dengan status aktif/nonaktif dan paginasi.
3. **Interaksi Modal Edit & Toggle**: Mengklik tombol *Edit* pada salah satu baris bulan magang memicu terbukanya modal edit dengan data terisi secara akurat.

### 8.2 Skenario Sad Path & Batasan Bisnis
1. **Aturan Bisnis BR-12 (Anti-Penonaktifan Bulan Berisi Peserta Aktif)**: Memastikan penonaktifan bulan magang yang memiliki peserta berstatus aktif tetap dicegah dan memunculkan notifikasi peringatan.
2. **Validasi Tanggal Pendaftaran**: Memastikan validasi `tgl_tutup >= tgl_buka` dan `bulan_mulai > tgl_tutup` tetap berjalan ketat pada form tambah dan ubah bulan.

---

## 9. 🛡️ Analisis Risiko & Mitigasi

| Potensi Risiko | Tingkat | Mitigasi |
| :--- | :--- | :--- |
| **N+1 Query pada Paginasi Bulan** | Rendah | Menggunakan eager loading pada relasi yang dibutuhkan serta query count terindeks. |
| **Konflik Variabel `$bulanList`** | Sedang | Memisahkan variabel menjadi `$bulanList` (koleksi opsi dropdown pada tab kuota) dan `$daftarBulanPaginator` (objek paginasi untuk tabel periode magang). |
| **Aksesibilitas (WCAG 2.1 AA)** | Rendah | Menggunakan atribut ARIA semantik: `role="tablist"`, `role="tab"`, `aria-selected`, dan `aria-controls`. |
| **Regresi Test Suite Eksisting** | Sangat Rendah | Seluruh elemen teks dan selector yang diuji pada `AdminKuotaRevampTest` dipertahankan 100%. |

---

## 10. ✅ Kriteria Selesai (Definition of Done)

- [x] Halaman `/admin/kuota` memiliki 2 tombol navigasi kategori: **Daftar Bulan Magang** dan **Kuota Per Bidang**.
- [x] Tabel dari Gambar SS 1 (*Daftar Bulan Magang*) tampil rapi, memiliki pagination, badge status, serta aksi Edit dan Toggle Aktif.
- [x] Tampilan dari Gambar SS 2 (*Manajemen Kuota*) tetap tampil sempurna saat kategori Kuota Per Bidang aktif.
- [x] Perpindahan antar tab berjalan mulus tanpa reload halaman via JavaScript dan sinkron dengan URL browser.
- [x] Tidak ada kode CSS atau JavaScript yang ditulis di dalam file Blade.
- [x] `npm run build` sukses dan seluruh test PHPUnit terkait lulus 100%.

