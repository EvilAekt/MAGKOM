# Rencana Kerja: Reposisi Tombol Navigasi Kategori & Tombol Aksi ke Dalam Hero Banner Kuota

> **Status:** SELESAI (COMPLETED)  
> **Tanggal:** 23 November 2026  
> **Target Branch:** `test-2`  
> **Dokumen Terkait:** `docs/01-PRD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `.agents/rules/AGENTS.md`  
> **Referensi Gambar Permintaan Pengguna:**
> - **SS 1 (Tombol Kategori):** Tombol tab navigasi `[ 📅 Daftar Bulan Magang (3) ]` dan `[ 🗂️ Kuota Per Bidang (4) ]`.
> - **SS 2 (Chart / Hero Banner Biru):** Hero Banner Biru *Bulan & Kuota* (menampilkan judul bulan, status, tanggal buka/tutup, mulai magang, dan stat box kuota terisi).
> - **SS 3 (Tombol Aksi Bulan):** Tombol `[ ✏️ Ubah bulan ]` dan `[ ➕ Buat bulan baru ]`.

---

## 1. 🎯 Tujuan & Ringkasan Kebutuhan

Menyesuaikan hierarki visual dan tata letak pada halaman **Manajemen Kuota** (`/admin/kuota`) sesuai instruksi spesifik pengguna:
1. **Pindahkan Tombol SS 1 ke Bawah Chart SS 2**:
   - Memindahkan komponen 2 tombol navigasi kategori (*Daftar Bulan Magang* dan *Kuota Per Bidang*) dari posisinya saat ini (paling atas) ke **di bawah** chart / Hero Banner Biru (Gambar SS 2).
2. **Pindahkan Tombol SS 3 ke Dalam Chart SS 2**:
   - Memindahkan tombol aksi `Ubah bulan` dan `Buat bulan baru` (beserta tombol kondisional `Buka bulan` jika status draft) dari subbar filter bawah ke **di dalam** chart / Hero Banner Biru (Gambar SS 2).
3. **Penyederhanaan Sub-Bar Filter Bulan**:
   - Subbar di bawahnya kini khusus memuat filter dropdown *Bulan Magang:* untuk pemilihan fokus bulan kerja tanpa tombol aksi ganda yang membingungkan.
4. **Pelestarian Logika & Desain Eksisting**:
   - Mempertahankan seluruh event listener JavaScript, modal form (Ubah Bulan, Buat Bulan Baru), styling Sleman Design System, dan memastikan seluruh suite test otomatis tetap lulus 100%.

---

## 2. 📊 Perbandingan Tata Letak (Before vs After)

### 2.1 Tata Letak Saat Ini (Current Layout)
```text
[TOP CONTAINER]
├── 1. Navigasi Kategori (SS 1) [Daftar Bulan Magang | Kuota Per Bidang]  <-- Berada di paling atas
├── 2. Panel 1: Daftar Bulan Magang (SS 1 Table Card)
└── 3. Panel 2: Kuota Per Bidang
    ├── Hero Banner Biru (Chart SS 2) (Hanya teks info & stat box kuota)
    ├── Subbar Filter [Bulan Magang: Dropdown] + [Tombol SS 3: Ubah bulan & Buat bulan baru]
    └── Tabel Kuota Per Bidang
```

### 2.2 Tata Letak Target (Target Layout)
```text
[TOP CONTAINER]
├── 1. Hero Banner Biru (Chart SS 2) - Unified Signature Header
│   ├── Kiri:
│   │   ├── Eyebrow & Title: "BULAN & KUOTA" -> "November 2026 [Ditutup]"
│   │   ├── Grid Meta: Tanggal buka, Tanggal tutup, Mulai magang
│   │   └── [BARU DI DALAM HERO] Baris Tombol Aksi (SS 3):
│   │       ├── [ ✏️ Ubah bulan ] (Frosted glass outline button)
│   │       ├── [ ➕ Buat bulan baru ] (Crisp white solid button)
│   │       └── (Jika Draft: [ Buka bulan ] success button)
│   └── Kanan:
│       └── Stat Box: "Kuota terisi 50/110 peserta" + Progress bar + Status countdown
│
├── 2. Navigasi Kategori (SS 1) [Daftar Bulan Magang (X) | Kuota Per Bidang (Y)]  <-- DIPINDAHKAN KE SINI (DI BAWAH HERO BANNER)
│
├── 3. Panel Konten Dinamis (Tergantung Tab Aktif):
│   ├── JIKA TAB "KUOTA PER BIDANG" AKTIF:
│   │   ├── Subbar Minimalis: Dropdown Filter [Bulan Magang: November 2026 v]
│   │   └── Card Tabel "Kuota per bidang" (Alokasi formasi unit kerja dinas)
│   │
│   └── JIKA TAB "DAFTAR BULAN MAGANG" AKTIF:
│       └── Card Tabel "Daftar Bulan Magang" (Tabel riwayat periode, status, jadwal, paginasi)
```

---

## 3. ⚖️ Evaluasi Opsi Pendekatan Arsitektur

| Parameter Evaluasi | Opsi 1: Hero Banner Permanen di Atas Tab Switcher (Rekomendasi Utama) | Opsi 2: Hero Banner Hanya di Dalam Panel Kuota |
| :--- | :--- | :--- |
| **Kesesuaian Instruksi User** | **100% Sesuai**: User meminta tombol SS 1 pindah ke bawah chart SS 2. Ini menempatkan Hero Banner di atas dan tombol tab di bawahnya. | Kurang sesuai, karena jika tombol tab diletakkan di luar/atas, posisi tombol SS 1 tidak berada di bawah chart SS 2. |
| **Nilai Informasi (UX Value)** | **Tinggi**: Hero Banner Biru berfungsi sebagai *Executive Summary* gelombang magang aktif yang selalu memberikan konteks instan bagi admin, baik saat mengelola daftar periode maupun alokasi kuota per bidang. | Rendah: Informasi ringkasan gelombang hilang saat admin berpindah ke tab daftar periode. |
| **Integrasi Tombol Aksi (SS 3)** | Tombol `Ubah bulan` dan `Buat bulan baru` berada langsung di dalam Hero Banner, memperjelas bahwa tombol tersebut mengubah parameter gelombang yang sedang ditampilkan di banner tersebut. | Tombol terpisah dari kartu ringkasannya. |
| **Respon & Ergonomi** | Elegan di desktop maupun mobile. Tombol di dalam hero memanfaatkan kontras warna tinggi (putih & frosted glass) di atas latar biru signature. | Memerlukan ruang vertikal tambahan di luar hero. |

> **Keputusan**: Memilih **Opsi 1** karena secara presisi mengeksekusi instruksi pengguna dan memberikan tata visual yang bersih, hierarkis, dan profesional.

---

## 4. 📐 Detail Desain & Spesifikasi Teknis

### 4.1 Posisi Tombol Aksi SS 3 di Dalam Hero Banner Biru (SS 2)
Tombol ditempatkan di dalam `.hero-left-content`, tepat di bawah `.hero-meta-grid`:
```html
<div class="hero-banner-actions">
    @if(! auth()->user()?->isSuperAdmin())
        @if($selectedBulan && ! $selectedBulan->aktif)
            <button type="button" class="btn-hero-action-success" data-kuota-action="publish-draft">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>{{ __('Buka bulan') }}</span>
            </button>
        @endif

        @if($selectedBulan)
            <button type="button" data-action="open-ubah-bulan" class="btn-hero-action-outline">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"></path>
                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                </svg>
                <span>{{ __('Ubah bulan') }}</span>
            </button>
        @endif

        <button type="button" data-action="open-buat-bulan" class="btn-hero-action-solid">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>{{ __('Buat bulan baru') }}</span>
        </button>
    @else
        <span class="kuota-active-chip" style="background: rgba(255, 255, 255, 0.15); color: #FFFFFF; border: 1px solid rgba(255, 255, 255, 0.3);">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <span>Mode Lihat Saja</span>
        </span>
    @endif
</div>
```

### 4.2 Styling Tombol di Dalam Hero Banner (`resources/css/pages/admin/kuota-index.css`)
- `.hero-banner-actions`: `display: flex; align-items: center; gap: 0.75rem; margin-top: 1.5rem; flex-wrap: wrap;`
- `.btn-hero-action-outline`:
  - Latar semi-transparan: `background: rgba(255, 255, 255, 0.16);`
  - Border halus: `border: 1px solid rgba(255, 255, 255, 0.32);`
  - Teks putih: `color: #FFFFFF; font-weight: 700;`
  - Efek hover: `background: rgba(255, 255, 255, 0.28); transform: translateY(-1px);`
- `.btn-hero-action-solid`:
  - Latar putih solid: `background: #FFFFFF;`
  - Teks warna brand biru Sleman: `color: #06318C; font-weight: 700;`
  - Shadow lembut: `box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);`
  - Efek hover: `background: #F8FAFC; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);`
- `.btn-hero-action-success`:
  - Warna hijau aksen: `background: #16A34A; border: 1px solid rgba(255, 255, 255, 0.3); color: #FFFFFF;`

### 4.3 Posisi Tombol Navigasi Kategori (SS 1)
Diletakkan tepat di bawah Hero Banner Biru:
```html
<div class="kuota-category-nav-wrapper" style="margin-top: 1.5rem; margin-bottom: 0.5rem;">
    <div class="kuota-nav-tabs" role="tablist" aria-label="Kategori Manajemen Kuota">
        ...
    </div>
</div>
```

---

## 5. 📋 Justifikasi Pengecualian Perubahan Blade (Aturan Section 3.3 AGENTS.md)

1. **Mengapa perubahan tidak dapat diselesaikan tanpa markup baru?**
   - Reposisi struktur visual memerlukan pemindahan container Hero Banner ke luar pembungkus panel, pemindahan tombol aksi SS 3 ke dalam elemen semantik `.hero-banner-actions`, dan pemindahan container `.kuota-category-nav-wrapper` ke posisi di bawah Hero Banner.
2. **File Blade yang diubah**:
   - [`resources/views/admin/kuota/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php)
3. **Kepatuhan Larangan**:
   - **TIDAK ADA** `<style>` atau `<script>` baru di Blade.
   - **TIDAK ADA** inline event handler (`onclick`, dsb.).
   - Seluruh styling baru dikelola di [`resources/css/pages/admin/kuota-index.css`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/kuota-index.css).
   - Seluruh interaksi JavaScript tetap ditangani secara modular di [`resources/js/pages/admin/kuota-index.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/admin/kuota-index.js).

---

## 6. 🗂️ Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/kuota/index.blade.php` | **Modify** | 1. Memindahkan Hero Banner Biru (SS 2) ke atas.<br>2. Menyematkan tombol aksi (SS 3: `Ubah bulan` & `Buat bulan baru`) ke dalam Hero Banner.<br>3. Memindahkan tombol navigasi kategori (SS 1) ke posisi di bawah Hero Banner.<br>4. Merampingkan `.month-switch-strip` di bawah tab kuota. |
| `resources/css/pages/admin/kuota-index.css` | **Modify** | Menambahkan styling `.hero-banner-actions`, `.btn-hero-action-outline`, `.btn-hero-action-solid`, `.btn-hero-action-success`, dan penyesuaian spacing antar komponen. |
| `tests/Feature/AdminKuotaRevampTest.php` | **Modify** | Memastikan assertion letak elemen dan aksi tombol tetap lulus 100%. |
| `docs/plans/2026-11-23-reposisi-tombol-kategori-dan-aksi-hero-kuota.md` | **Create** | Dokumen rencana kerja teknis dan spesifikasi arsitektur. |

---

## 7. 🚀 Rincian Checklist Implementasi Terurut

### Tahap 1: Styling CSS Baru di `kuota-index.css`
- [x] **1.1**: Tambahkan styling `.hero-banner-actions` untuk wadah tombol di dalam Hero Banner.
- [x] **1.2**: Tambahkan kelas button `.btn-hero-action-outline` (glass effect), `.btn-hero-action-solid` (white contrast), dan `.btn-hero-action-success`.
- [x] **1.3**: Sesuaikan margin `.kuota-category-nav-wrapper` agar memiliki jarak vertikal yang proporsional di bawah Hero Banner.

### Tahap 2: Penataan Ulang Struktur Markup di `index.blade.php`
- [x] **2.1**: Pindahkan Hero Banner Biru (`@if($selectedBulan) ... @endif`) ke atas, di atas `.kuota-category-nav-wrapper`.
- [x] **2.2**: Sisipkan tombol aksi SS 3 (`Ubah bulan`, `Buat bulan baru`, `Buka bulan`) ke dalam `.hero-left-content` di dalam Hero Banner.
- [x] **2.3**: Letakkan komponen navigasi kategori (SS 1) tepat di bawah Hero Banner Biru.
- [x] **2.4**: Bersihkan kontainer `.month-switch-strip` di dalam `#panel-kuota-bidang` sehingga hanya memuat form dropdown filter bulan magang.

### Tahap 3: Verifikasi Fungsionalitas & Build
- [x] **3.1**: Jalankan `npm run build` untuk memvalidasi kompilasi aset Vite.
- [x] **3.2**: Jalankan pengujian linter Pint: `vendor/bin/pint --test`.
- [x] **3.3**: Jalankan seluruh feature test terkait menggunakan PHP 8.3: `php artisan test tests/Feature/AdminKuotaRevampTest.php`.
- [x] **3.4**: Pastikan tombol `Ubah bulan` membuka modal ubah bulan, `Buat bulan baru` membuka modal buat bulan baru, dan tombol tab kategori berganti panel dengan mulus.

---

## 8. 🛡️ Analisis Risiko & Mitigasi

| Potensi Risiko | Tingkat | Mitigasi |
| :--- | :--- | :--- |
| **Keterbacaan Teks Tombol di dalam Banner Biru** | Rendah | Menggunakan kontras rasio tinggi: background putih solid dengan teks biru gelap (`#06318C`), dan outline dengan border semi-transparan putih yang memenuhi standar WCAG 2.1 AA. |
| **Regresi Selektor Event Listener JavaScript** | Sangat Rendah | Seluruh tombol tetap mempertahankan atribut `data-action="open-ubah-bulan"`, `data-action="open-buat-bulan"`, dan `data-kuota-action="publish-draft"` sehingga modul JS `kuota-index.js` tetap berfungsi normal tanpa perubahan handler. |
| **Responsivitas pada Layar Mobile (< 640px)** | Rendah | `.hero-banner-actions` menggunakan `flex-wrap: wrap` sehingga tombol akan tersusun rapi ke bawah jika lebar layar menyempit. |

---

## 9. ✅ Kriteria Selesai (Definition of Done)

- [x] Tombol Navigasi Kategori (SS 1: `Daftar Bulan Magang` & `Kuota Per Bidang`) berada tepat **di bawah** chart / Hero Banner Biru (SS 2).
- [x] Tombol Aksi (SS 3: `Ubah bulan` & `Buat bulan baru`) berada tepat **di dalam** chart / Hero Banner Biru (SS 2).
- [x] Subbar di bawah tab Kuota Per Bidang tersisa rapi hanya dengan dropdown filter bulan magang.
- [x] Semua modal (Ubah bulan, Buat bulan baru) tetap dapat dibuka dan berfungsi 100%.
- [x] Tidak ada kode CSS atau JavaScript yang ditulis di dalam file Blade.
- [x] `npm run build` sukses dan seluruh test PHPUnit terkait lulus 100%.

