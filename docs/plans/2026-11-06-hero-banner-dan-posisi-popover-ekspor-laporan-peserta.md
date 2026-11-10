# TASK RUNBOOK SENIOR DEVELOPER: Standarisasi Hero Banner & Perbaikan Posisi Popover Ekspor Laporan Peserta

- **ID Tugas**: TASK-MAGKOM-PEMBIMBING-LAPORAN-BANNER-EXPORT-01
- **Target Role / Assignee**: Junior Developer / AI Assistant
- **Reviewer / Author**: Senior Software Engineer (Antigravity)
- **Komponen**: Portal Pembimbing MagKom — Modul Laporan Peserta (`/pembimbing/laporan-kinerja`)
- **Status**: Ready for Implementation (Menunggu Persetujuan Pengguna)
- **Tanggal**: 2026-11-06

---

## 1. DESKRIPSI & LATAR BELAKANG PERMASALAHAN

Berdasarkan pengujian antarmuka pengguna pada halaman **Laporan Peserta** (`/pembimbing/laporan-kinerja`), ditemukan dua isu penting yang perlu diperbaiki segera:

1. **Bug Posisi Popover Ekspor (Melenceng ke Kanan & Tidak Bisa Diklik - Gambar 1)**:
   - Saat tombol `Ekspor Rekap` diklik, popover floating card `Ekspor Rekap Cepat` muncul melebar ke arah kanan hingga terpotong di tepi layar (*overflow-x viewport*).
   - Akibatnya, badge format (*Rekomendasi, Legal Sah*), counter baris mentee, serta tombol aksi `Unduh Excel (.xlsx)` dan `Batal` di sebelah kanan terpotong dan tidak dapat diklik oleh pengguna.
2. **Kebutuhan Layout Hero Banner Sesuai Standar MagKom (Gambar 2)**:
   - Saat ini tombol `Ekspor Rekap` berada di baris terpisah (`.laporan-action-bar`) di luar card.
   - Sesuai dengan referensi Gambar 2 (halaman *Direktori Peserta Bimbingan*), tombol aksi harus terintegrasi di dalam **Hero Banner Card** berwarna biru gradasi kedinasan MagKom (`.classroom-banner`), lengkap dengan judul *"Laporan Peserta"*, informasi bidang dinas, nama pembimbing, dan jumlah mentee aktif.

---

## 2. ROOT CAUSE ANALYSIS (RCA) DARI SENIOR DEVELOPER

Sebagai referensi junior developer/AI sebelum menulis kode, berikut adalah akar penyebab masalah yang telah diinvestigasi secara teknis:

1. **Akar Masalah Popover Melenceng (CSS Cascading Conflict)**:
   - File utama `resources/css/app.css` mengimpor `resources/css/pages/admin/dashboard.css`.
   - Di dalam `admin/dashboard.css` baris 735, terdapat aturan global:
     ```css
     .adm-export-popover {
       position: absolute;
       top: calc(100% + 8px);
       left: 0; /* <-- Penyebab utama: memaksa popover merapat ke kiri tombol */
       width: 320px;
     }
     .adm-popover-pointer {
       left: 24px; /* <-- Penyebab pointer panah muncul di kiri */
     }
     ```
   - Dalam aturan spesifikasi CSS (*CSS Cascading Order*), property `left: 0` mengalahkan `right: 0` ketika lebar elemen didefinisikan secara statis (`width: 320px`).
   - Karena tombol `Ekspor Rekap` berada di sisi kanan halaman, popover yang merapat ke `left: 0` tombol akan menjulur 320px ke arah kanan, menembus batas layar browser.
2. **Ketiadaan Struktur Card Banner**:
   - Template `pembimbing/laporan-kinerja/index.blade.php` belum menggunakan struktur `.classroom-banner` dan `@section('content_area_class', 'gentelella-fluid-canvas')` seperti halaman portal pembimbing lainnya (`peserta/index`, `logbook/index`, `tugas/index`).
   - Container banner standar MagKom memiliki aturan bawaan `overflow: hidden`. Jika popover dipindahkan ke dalam banner tanpa override `overflow: visible`, popover akan terpotong oleh batas bawah banner.

---

## 3. EVALUASI OPSI ARSITEKTUR

Senior Developer telah mengevaluasi 2 pendekatan teknis berikut:

| Kriteria Evaluasi | Opsi A: Standarisasi Hero Banner Parity (Rekomendasi Senior Dev) | Opsi B: Card Putih Lepas Khusus Tombol Ekspor |
| :--- | :--- | :--- |
| **Deskripsi** | Menambahkan `.classroom-banner` biru gradasi resmi MagKom Sleman di `laporan-kinerja/index.blade.php` selaras Gambar 2, meletakkan tombol ekspor di baris kanan atas banner (`.banner-top-row`), dan menyetel anti-clipping CSS. | Membuat card putih datar baru hanya untuk menampung tombol ekspor di atas grid tile stat tanpa banner biru. |
| **Konsistensi UI (Gambar 2)** | **Tinggi (100%)**: Selaras penuh dengan `Direktori Peserta`, `Tugas & Progres`, dan `Logbook Peserta`. | **Rendah**: Halaman laporan menjadi satu-satunya halaman tanpa hero banner biru. |
| **Penyelesaian Bug Popover** | **Tuntas**: Mengunci `left: auto !important; right: 0 !important;` pada popover dan pointer panah. | **Parsial**: Membutuhkan penyesuaian CSS manual terpisah tanpa menyatukan layout. |
| **Dampak Koding Blade** | Minimal & terstruktur: Memanfaatkan token dan class yang sudah ada di sistem. | Menambah elemen dan class baru yang tidak terstandarisasi. |

-> **Keputusan Senior Dev**: Wajib gunakan **Opsi A**.

---

## 4. PEMETAAN BERKAS YANG DIUBAH (FILE IMPACT TABLE)

| Berkas | Aksi | Ringkasan Pekerjaan |
| :--- | :---: | :--- |
| `resources/views/pembimbing/laporan-kinerja/index.blade.php` | Modify | 1. Tambahkan `@section('content_area_class', 'gentelella-fluid-canvas')`.<br>2. Ganti `.laporan-action-bar` dengan `.classroom-banner` khas MagKom Sleman selaras Gambar 2.<br>3. Pindahkan `#admExportDropdownWrap` ke dalam sisi kanan `.banner-top-row` di dalam banner.<br>4. Pasang class `.adm-export-popover-right` dan `.adm-pointer-right` pada popover & pointer panah. |
| `resources/css/pages/pembimbing/laporan-kinerja-index.css` | Modify | 1. Tambahkan styling `.classroom-banner` dengan `overflow: visible !important;` dan `z-index: 10;`.<br>2. Styling tombol `.btn-export-rekap-banner`: latar putih bersih, teks navy `#01347C`, font tebal, shadow halus, selaras tombol "+ Beri Tugas Baru" pada Gambar 2.<br>3. Kunci posisi popover: `.adm-export-popover { left: auto !important; right: 0 !important; }` dan `.adm-popover-pointer { left: auto !important; right: 28px !important; }`.<br>4. Pastikan responsivitas mobile: `max-width: calc(100vw - 32px);`. |
| `tests/Feature/PembimbingLaporanKinerjaViewTest.php` | Modify | Tambahkan test assertion untuk memvalidasi keberadaan `classroom-banner`, judul `Laporan Peserta` di dalam banner, ketiadaan clipping, dan tombol ekspor berada di dalam card banner. |

---

## 5. LANGKAH IMPLEMENTASI TERSTRUKTUR (CHECKLIST UNTUK JUNIOR DEV / AI)

### Tahap 1: Struktur Markup Blade (`laporan-kinerja/index.blade.php`)
- [x] **1.1 Pasang Section Canvas Fluid**:
  - Di baris atas file setelah `@section('topbar_title', 'Laporan Peserta')`, tambahkan:
    ```blade
    @section('content_area_class', 'gentelella-fluid-canvas')
    ```
- [x] **1.2 Bungkus Header dengan Classroom Hero Banner**:
  - Hapus div `.laporan-action-bar` lama.
  - Pasang komponen Hero Banner sebelum `.tiles-grid`:
    ```blade
    @php
        $authUser = auth()->user();
        $bidangNama = $authUser->bidang?->nama_bidang ?? 'Dinas Komunikasi dan Informatika Sleman';
    @endphp

    <div class="classroom-banner laporan-hero-banner">
        <div class="banner-top-row">
            <div>
                <h1 class="banner-title">Laporan Peserta</h1>
                <div class="banner-subtitle">
                    <span>Bidang: <strong>{{ $bidangNama }}</strong></span>
                    <span>&bull;</span>
                    <span>Pembimbing: <strong>{{ $authUser->nama }}</strong></span>
                    <span>&bull;</span>
                    <span><strong>{{ $countAktif }} Mentee Aktif Bimbingan</strong></span>
                </div>
            </div>
            <div>
                {{-- Dropdown Wrap & Popover Ekspor di dalam Card Banner --}}
                <div id="admExportDropdownWrap" class="adm-export-dropdown-wrap">
                    <button type="button" 
                            id="btnToggleExportPopover" 
                            class="btn-export-rekap-banner"
                            aria-expanded="false" 
                            aria-haspopup="menu" 
                            aria-controls="admExportPopover"
                            title="Pilih dan unduh rekap data peserta (Excel, CSV, PDF)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Ekspor Rekap</span>
                        <svg id="admExportCaretSvg" class="adm-export-chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    {{-- Popover Floating Card dengan class .adm-export-popover-right --}}
                    <div id="admExportPopover" 
                         class="adm-export-popover adm-export-popover-right" 
                         style="display: none;" 
                         role="menu" 
                         aria-label="Menu Pilihan Format Ekspor"
                         data-export-url="{{ route('pembimbing.laporan-kinerja.export') }}"
                         data-print-url="{{ route('pembimbing.laporan-kinerja.cetak') }}">
                        <div class="adm-popover-pointer adm-pointer-right"></div>
                        ...
                    </div>
                </div>
            </div>
        </div>
    </div>
    ```

---

### Tahap 2: Styling CSS Anti-Clipping & Banner (`laporan-kinerja-index.css`)
- [x] **2.1 Styling Hero Banner Card**:
  - Tambahkan style `.classroom-banner.laporan-hero-banner`:
    ```css
    .classroom-banner.laporan-hero-banner {
        background: radial-gradient(80% 120% at 100% 0%, rgba(63, 81, 181, 0.45), transparent 60%),
                    linear-gradient(135deg, #01347C 0%, #003566 100%);
        border-radius: 16px;
        padding: 28px 32px;
        color: #ffffff;
        position: relative;
        overflow: visible !important; /* Wajib visible agar popover tidak terpotong */
        box-shadow: 0 4px 20px -2px rgba(1, 52, 124, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.12);
        z-index: 20; /* Berada di atas tiles grid */
        margin-bottom: 0.5rem;
    }
    ```
- [x] **2.2 Styling Tombol Ekspor di Dalam Banner**:
  - Buat style `.btn-export-rekap-banner` menyerupai tombol "+ Beri Tugas Baru" pada Gambar 2:
    ```css
    .btn-export-rekap-banner {
        background: #ffffff;
        color: #01347C;
        text-decoration: none;
        padding: 0.65rem 1.35rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        white-space: nowrap;
    }
    .btn-export-rekap-banner:hover {
        background: #f8fafc;
        color: #002244;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.22);
    }
    ```
- [x] **2.3 Anti-Clipping Popover & Pointer Alignment**:
  - Kunci posisi popover agar menempel di sisi kanan tombol dan mengembang ke arah kiri (aman 100% dari tepi kanan layar):
    ```css
    .adm-export-popover,
    .adm-export-popover.adm-export-popover-right {
        position: absolute;
        top: calc(100% + 12px);
        right: 0 !important;
        left: auto !important; /* Mengesampingkan left: 0 dari app.css/admin/dashboard.css */
        width: 320px;
        max-width: calc(100vw - 32px);
        z-index: 1050;
    }

    .adm-popover-pointer,
    .adm-popover-pointer.adm-pointer-right {
        position: absolute;
        top: -6px;
        right: 28px !important;
        left: auto !important; /* Mengesampingkan left: 24px dari admin/dashboard.css */
        width: 12px;
        height: 12px;
    }
    ```

---

### Tahap 3: Automated Feature Testing (`PembimbingLaporanKinerjaViewTest.php`)
- [x] **3.1 Tambahkan Assertion Pengujian Banner & Popover**:
  - Pastikan test memeriksa:
    1. Halaman merender elemen `.classroom-banner` dan judul `Laporan Peserta` di dalam banner.
    2. Subtitle banner menampilkan nama bidang dan nama pembimbing yang login.
    3. Tombol ekspor `.btn-export-rekap-banner` berada di dalam banner.
    4. Elemen popover memiliki class anti-clipping `.adm-export-popover-right` dan `.adm-pointer-right`.

---

### Tahap 4: Verifikasi & Quality Gates
- [x] **4.1 Kompilasi Aset Vite**:
  - Jalankan `npm.cmd run build` dan pastikan `public/build/assets/laporan-kinerja-index-*.css` berhasil dikompilasi tanpa error.
- [x] **4.2 Code Formatting**:
  - Jalankan `php vendor/bin/pint --test` dan pastikan zero styling issues.
- [x] **4.3 Automated PHPUnit Test**:
  - Jalankan seluruh suite test pembimbing:
    ```powershell
    php artisan test tests/Feature/PembimbingLaporanKinerjaViewTest.php tests/Feature/AdvancedRoleEnhancementPhaseSixTest.php tests/Feature/PembimbingFocusedMenteeTugasViewTest.php tests/Feature/PembimbingFocusedMenteeLogbookViewTest.php tests/Feature/PembimbingReviewClassroomTest.php tests/Feature/PembimbingPortalStandardizationTest.php
    ```

---

## 6. STRATEGI TESTING & SKENARIO PENGUJIAN

### Skenario Otomatis (PHPUnit Feature Test):
1. **Skenario 1 (Happy Path - Hero Banner Render)**:
   - Akses `/pembimbing/laporan-kinerja` sebagai pembimbing.
   - Assert: Response status 200, terdapat class `classroom-banner`, memuat teks judul *"Laporan Peserta"*, memuat nama bidang dinas, dan memuat *"X Mentee Aktif Bimbingan"*.
2. **Skenario 2 (Anti-Clipping Class Check)**:
   - Assert: Terdapat selector `#admExportPopover.adm-export-popover-right` dan `.adm-popover-pointer.adm-pointer-right`.
3. **Skenario 3 (Regression Check)**:
   - Pastikan 4 tile stat Gentelella (*Total Mentee*, *Peserta Aktif*, *Perlu Perhatian*, *Alumni Selesai*) dan tabel *Data Penempatan Peserta* tetap utuh dan berfungsi normal.

### Skenario Verifikasi Visual:
- Buka browser pada resolusi Desktop (1440x900) & Laptop (1280x720):
  - Klik tombol `Ekspor Rekap` di pojok kanan card banner.
  - Pastikan popover mengembang ke bawah-kiri dengan rapi, pointer panah tepat menunjuk ke tombol, dan seluruh isi kartu popover (termasuk badge dan tombol download) berada 100% di dalam layar tanpa ada yang terpotong.
  - Klik opsi *CSV Tabular* & *Dokumen Resmi (.pdf)* lalu klik Batal / klik di luar untuk memastikan interaksi klik berjalan lancar tanpa terhalang boundary overflow.

---

## 7. RISIKO, EDGE CASES, & MITIGASI

1. **Risiko Clipping Overflow Banner**:
   - *Risiko*: Class `.classroom-banner` bawaan memiliki `overflow: hidden`, yang akan memotong popover jika dipasang di dalamnya.
   - *Mitigasi*: Menambahkan selector spesifik `.classroom-banner.laporan-hero-banner { overflow: visible !important; }`.
2. **Risiko Layering Z-Index**:
   - *Risiko*: Popover berada di belakang kartu Tile Stat Gentelella.
   - *Mitigasi*: Memberikan `z-index: 20` pada `.classroom-banner` dan `z-index: 1050` pada `.adm-export-popover`.
3. **Risiko Layar Sempit / Mobile (Viewport < 400px)**:
   - *Risiko*: Popover 320px meluap di layar ponsel sangat kecil.
   - *Mitigasi*: Properti `max-width: calc(100vw - 32px)` dan media query responsif yang memastikan popover fleksibel mengikuti lebar viewport.

---

## 8. KRITERIA SELESAI (DEFINITION OF DONE)

- [x] Header halaman Laporan Peserta telah menggunakan Hero Banner Card biru (`.classroom-banner`) selaras Gambar 2.
- [x] Tombol `Ekspor Rekap` telah dipindahkan ke sisi kanan dalam card banner dengan styling putih elegan kontras.
- [x] Popover `Ekspor Rekap Cepat` terbukti tidak melenceng ke kanan (`left: auto !important; right: 0 !important;`) dan 100% clickable.
- [x] Pointer panah popover sejajar rapi di bawah tombol ekspor (`right: 28px !important;`).
- [x] Kompilasi aset Vite (`npm.cmd run build`) sukses 100%.
- [x] Formatting linter (`php vendor/bin/pint --test`) lulus 100%.
- [x] Seluruh automated feature test (35 tests, 263 assertions) lulus hijau 100%.
- [x] Kode terisolasi bersih di Blade, CSS, dan JS tanpa inline script / style.

