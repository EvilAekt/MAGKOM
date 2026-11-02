# Rencana Kerja: Navigasi Popover Ekspor Laporan Rekapitulasi Pendaftaran (Excel, CSV, PDF)

Dokumen ini memuat rencana kerja teknis untuk merefaktor tombol navigasi ekspor pada panel **"Laporan Rekapitulasi Pendaftaran"** (`admin/laporan?tab=pendaftaran`) agar seragam dengan pola desain dropdown popover kontekstual interaktif yang telah diimplementasikan pada modul Penempatan.

---

## 1. Tujuan

Mengganti 2 tombol ekspor terpisah saat ini:
- `[ 📄 Ekspor Excel (.xlsx) ]` (warna hijau)
- `[ ⬇ Ekspor CSV ]` (warna abu-abu)

menjadi satu tombol terpadu:
- **`[ ⬇ Ekspor Rekap ▾ ]`** (`#btnToggleExportPopoverPendaftaran`)

yang ketika diklik akan memunculkan menu dropdown popover melayang kontekstual dengan 3 pilihan format kartu radio interaktif:
1. **Microsoft Excel (`.xlsx`)** *(Rekomendasi)* — Tabel bergaris rapi & filterable.
2. **CSV Tabular (`.csv`)** *(Cepat)* — Data mentah teks pemisah koma terstandarisasi.
3. **Dokumen Resmi (.pdf)** *(Legal Sah)* — Dokumen cetak A4 landscape ber-kop resmi Dinas Komunikasi dan Informatika Kabupaten Sleman.

---

## 2. Asumsi Kerja & Analisis Visual

1. **Lokasi Elemen Berdasarkan Tangkapan Layar**:
   - Tangkapan layar yang diunggah menunjukkan header card:
     - Judul: `Laporan Rekapitulasi Pendaftaran`
     - Subjudul: `Data calon peserta pendaftaran magang berdasarkan bulan magang dan status keputusan.`
     - Elemen ini terletak di file [`resources/views/admin/laporan/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/laporan/index.blade.php) pada baris 336–362 (`@if($tab === 'pendaftaran')`).
2. **Penempatan Dropdown Popover (Right-Aligned)**:
   - Berbeda dengan modul penempatan di mana tombol ekspor berada di sisi kiri toolbar, pada card Laporan Rekapitulasi Pendaftaran tombol ekspor berada di sudut kanan atas header card (`justify-content: space-between`).
   - Oleh karena itu, popover harus menggunakan alignment sisi kanan (`right: 0; left: auto;` serta pointer `right: 24px; left: auto;`) agar menu melayang tidak terpotong atau meluap ke luar sisi kanan layar (*overflow prevention*).
3. **Jumlah Baris Data Aktif**:
   - Badge jumlah baris pada popover akan memanfaatkan objek paginator `$pendaftaranData->total()` sehingga menampilkan data riil (misal: `3 Baris` atau `150 Baris`).
4. **Backend Support Format PDF**:
   - Method saat ini [`ExportReportService::exportPendaftaran`](file:///d:/laragon/www/MAGKOM/app/Services/ExportReportService.php#L54) baru mendukung `xlsx` dan fallback ke `csv`.
   - Akan ditambahkan branch `$format === 'pdf'` menggunakan library `Barryvdh\DomPDF\Facade\Pdf` berformat A4 Landscape dengan template resmi [`resources/views/pdf/rekap-pendaftaran.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/pdf/rekap-pendaftaran.blade.php).

---

## 3. Evaluasi Pendekatan Teknis

| Parameter | Opsi A: Duplikasi Inline Modal / Alert Standar | Opsi B: Contextual Dropdown Popover (Rekomendasi) |
| :--- | :--- | :--- |
| **Pola Desain** | Menggunakan modal pop-up besar di tengah layar (*blocking full-screen overlay*). | Menggunakan dropdown popover melayang kontekstual tepat di bawah tombol trigger (Pola SuperAdmin Audit Log & Penempatan). |
| **Pengalaman Pengguna (UX)** | Terlalu berat untuk aksi cepat unduh data; menutupi tabel laporan di bawahnya. | Ringan, cepat, fokus, interaktif, dan mempertahankan konteks visual tabel yang sedang dilihat. |
| **Konsistensi Antar Modul** | Tidak konsisten dengan modul SuperAdmin Audit Log dan Admin Penempatan. | **100% Konsisten** secara visual, interaksi, dan design tokens dengan modul yang sudah ada. |
| **Pemeliharaan Kode** | Membutuhkan dependensi backdrop dan modal wrapper baru. | Menggunakan reusable CSS tokens yang sudah ada di `resources/css/pages/admin/dashboard.css`. |

**Pilihan**: **Opsi B** dipilih untuk menjamin konsistensi 100% desain antar-fitur MagKom sesuai aturan `AGENTS.md`.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/admin/laporan/index.blade.php` | **Modify** | Mengganti 2 tombol ekspor lama di tab pendaftaran (baris 342–361) dengan tombol trigger `#btnToggleExportPopoverPendaftaran` dan popover `#admExportPopoverPendaftaran`, serta menambahkan Vanilla JS handler di `@push('scripts')`. |
| `app/Services/ExportReportService.php` | **Modify** | Menambahkan penanganan format `pdf` pada `exportPendaftaran(...)` dengan `Barryvdh\DomPDF\Facade\Pdf`, audit log `ekspor_pendaftaran_pdf`, dan sanitasi filter. |
| `resources/views/pdf/rekap-pendaftaran.blade.php` | **Create** | Template resmi PDF A4 Landscape: Kop Dinas Kominfo Sleman, tabel data pendaftaran (No. Reg, Nama, Institusi, Jurusan, Bulan Mulai, Durasi, Status, Tanggal), dan kolom pengesahan. |
| `resources/css/pages/admin/dashboard.css` | **Modify** | Menambahkan varian modifier popover right-aligned (`.adm-export-popover-right`) untuk elemen di sisi kanan header. |
| `public/css/dashboard-cockpit-unified.css` | **Modify** | Sinkronisasi CSS fallback cockpit dashboard. |
| `tests/Feature/AdminEksporLaporanPendaftaranPopoverTest.php` | **Create** | Automated Feature test untuk memverifikasi markup popover di halaman laporan serta download response untuk format `xlsx`, `csv`, dan `pdf`. |

---

## 5. Rincian Teknis Implementasi

### A. Markup Blade (`resources/views/admin/laporan/index.blade.php`)
```blade
<div class="adm-export-dropdown-wrap" style="position: relative;">
    <button type="button" 
            id="btnToggleExportPopoverPendaftaran" 
            class="adm-btn-export-main" 
            aria-expanded="false" 
            aria-haspopup="menu" 
            aria-controls="admExportPopoverPendaftaran"
            title="Pilih dan unduh rekap data pendaftaran (Excel, CSV, PDF)">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
        </svg>
        <span>Ekspor Rekap</span>
        <svg class="adm-export-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="6 9 12 15 18 9"></polyline>
        </svg>
    </button>

    {{-- Dropdown Popover Melayang (Right-Aligned) --}}
    <div id="admExportPopoverPendaftaran" class="adm-export-popover adm-export-popover-right" style="display: none;" role="menu" aria-label="Menu Pilihan Format Ekspor Pendaftaran">
        <div class="adm-popover-pointer adm-pointer-right"></div>
        <div class="adm-popover-header">
            <div>
                <div class="adm-popover-title">Ekspor Rekap Cepat</div>
                <div class="adm-popover-desc">Data pendaftaran hasil filter aktif</div>
            </div>
            <span class="adm-popover-row-count tabular-nums">
                {{ number_format($pendaftaranData->total()) }} Baris
            </span>
        </div>

        <div class="adm-popover-formats" role="radiogroup" aria-label="Format Berkas">
            {{-- Format 1: Excel (.xlsx) --}}
            <label class="adm-format-card adm-format-selected" id="cardFormatPendaftaranXlsx" onclick="handleAdmExportPendaftaranFormatChange('xlsx')">
                <div class="adm-format-content">
                    <input type="radio" name="popover_export_pendaftaran_format" id="radioFormatPendaftaranXlsx" value="xlsx" checked class="adm-format-radio" onchange="handleAdmExportPendaftaranFormatChange('xlsx')">
                    <div class="adm-format-details">
                        <div class="adm-format-name-wrap">
                            <span class="adm-format-name">Microsoft Excel</span>
                            <span class="badge-format-ext badge-ext-xlsx">.xlsx</span>
                        </div>
                        <p class="adm-format-desc">Tabel resmi bergaris rapi &amp; filterable</p>
                    </div>
                </div>
                <div class="adm-format-meta">
                    <span class="adm-badge-recommend">Rekomendasi</span>
                </div>
            </label>

            {{-- Format 2: CSV Tabular (.csv) --}}
            <label class="adm-format-card" id="cardFormatPendaftaranCsv" onclick="handleAdmExportPendaftaranFormatChange('csv')">
                <div class="adm-format-content">
                    <input type="radio" name="popover_export_pendaftaran_format" id="radioFormatPendaftaranCsv" value="csv" class="adm-format-radio" onchange="handleAdmExportPendaftaranFormatChange('csv')">
                    <div class="adm-format-details">
                        <div class="adm-format-name-wrap">
                            <span class="adm-format-name">CSV Tabular</span>
                            <span class="badge-format-ext badge-ext-csv">.csv</span>
                        </div>
                        <p class="adm-format-desc">Data mentah teks pemisah koma terstandarisasi</p>
                    </div>
                </div>
                <div class="adm-format-meta">
                    <span class="adm-badge-quick">Cepat</span>
                </div>
            </label>

            {{-- Format 3: Dokumen Resmi PDF (.pdf) --}}
            <label class="adm-format-card" id="cardFormatPendaftaranPdf" onclick="handleAdmExportPendaftaranFormatChange('pdf')">
                <div class="adm-format-content">
                    <input type="radio" name="popover_export_pendaftaran_format" id="radioFormatPendaftaranPdf" value="pdf" class="adm-format-radio" onchange="handleAdmExportPendaftaranFormatChange('pdf')">
                    <div class="adm-format-details">
                        <div class="adm-format-name-wrap">
                            <span class="adm-format-name">Dokumen Resmi (.pdf)</span>
                            <span class="badge-format-ext badge-ext-pdf">.pdf</span>
                        </div>
                        <p class="adm-format-desc">Rekapitulasi cetak ber-kop resmi kedinasan</p>
                    </div>
                </div>
                <div class="adm-format-meta">
                    <span class="adm-badge-legal">Legal Sah</span>
                </div>
            </label>
        </div>

        <div class="adm-popover-cta">
            <button type="button" class="adm-btn-popover-cancel" onclick="toggleAdmExportPopoverPendaftaran(false)">
                Batal
            </button>
            <button type="button" id="btnDoExportDownloadPendaftaran" class="adm-btn-popover-download" onclick="triggerPendaftaranExport()">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span id="btnExportDownloadTextPendaftaran">Unduh Excel (.xlsx)</span>
            </button>
        </div>
    </div>
</div>
```

### B. JavaScript Interactivity di `@push('scripts')`
- Mengatur buka/tutup popover dengan status boolean eksplisit (`shouldOpen`).
- Mengubah teks tombol unduh secara dinamis saat kartu format diklik.
- Mengumpulkan parameter pencarian aktif (`bulan_magang_id`, `status`, `jurusan_id`, `dari_tanggal`, `sampai_tanggal`) dan menambahkan `format=xlsx|csv|pdf` ke route `admin.laporan.pendaftaran`.
- Menutup popover otomatis saat klik di luar popover (*click-outside*) atau saat tombol `Escape` ditekan.

---

## 6. Langkah-Langkah Implementasi Bertahap

- [x] **Fase 1: Persetujuan Pengguna**
  - [x] Mendapatkan persetujuan pengguna untuk rencana kerja dan perubahan markup Blade.
- [x] **Fase 2: Backend Service & Template PDF (`ExportReportService.php` & `pdf/rekap-pendaftaran.blade.php`)**
  - [x] Buat template Blade PDF: `resources/views/pdf/rekap-pendaftaran.blade.php` (A4 Landscape, Kop resmi Kominfo Sleman, tabel nomor pendaftaran, nama, institusi, jurusan, bulan, durasi, status, tanggal diajukan).
  - [x] Update `ExportReportService::exportPendaftaran`: Tambahkan branch `if ($format === 'pdf')` menggunakan `Barryvdh\DomPDF\Facade\Pdf::loadView(...)`.
  - [x] Tambahkan pencatatan audit trail akses data untuk format PDF.
- [x] **Fase 3: Refactoring Markup Blade (`admin/laporan/index.blade.php`)**
  - [x] Ganti 2 tombol ekspor lama di tab pendaftaran dengan komponen dropdown popover ekspor terpadu.
  - [x] Tambahkan Vanilla JS handler di `@push('scripts')`.
- [x] **Fase 4: Penambahan CSS Modifier Right-Aligned**
  - [x] Tambahkan kelas `.adm-export-popover-right` (`right: 0; left: auto;`) dan pointer `.adm-pointer-right` (`right: 24px; left: auto;`) di `resources/css/pages/admin/dashboard.css`.
  - [x] Sinkronisasikan ke `public/css/dashboard-cockpit-unified.css`.
- [x] **Fase 5: Asset Compilation & Quality Gate**
  - [x] Jalankan `npm run build` untuk mengompilasi CSS/JS Vite.
  - [x] Jalankan `php vendor/bin/pint --dirty --test` untuk memastikan kebersihan kode.
  - [x] Buat automated test `tests/Feature/AdminEksporLaporanPendaftaranPopoverTest.php` dan jalankan dengan `php artisan test`.
- [x] **Fase 6: Verifikasi Visual dengan Playwright MCP**
  - [x] Akses browser pada halaman `http://magkom.test/admin/laporan?tab=pendaftaran`.
  - [x] Klik tombol "Ekspor Rekap", verifikasi popover terbuka dengan rapi tanpa overflow.
  - [x] Klik kartu Excel, CSV, dan PDF untuk memastikan pemilihan dan teks tombol berganti.
  - [x] Tes auto-close tombol Batal, klik di luar, dan tombol Escape.

---

## 7. Strategi Testing

1. **Automated Feature Test (`AdminEksporLaporanPendaftaranPopoverTest.php`)**:
   - `test_laporan_pendaftaran_menampilkan_popover_ekspor_dengan_3_format()`: Memastikan markup popover, tombol trigger, dan 3 kartu format ter-render di Blade.
   - `test_ekspor_laporan_pendaftaran_format_xlsx_berhasil()`: Memastikan file Excel terunduh dengan Content-Type `spreadsheetml.sheet`.
   - `test_ekspor_laporan_pendaftaran_format_csv_berhasil()`: Memastikan file CSV terunduh dengan Content-Type `text/csv`.
   - `test_ekspor_laporan_pendaftaran_format_pdf_berhasil_dan_tercatat_di_audit_log()`: Memastikan file PDF terunduh dengan Content-Type `application/pdf` dan tercatat di tabel `audit_log`.
2. **Quality Gate**:
   - `php vendor/bin/pint --dirty --test` ➔ `0 issues`.
   - `npm run build` ➔ `0 errors`.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Tombol ekspor pada card "Laporan Rekapitulasi Pendaftaran" berubah menjadi satu tombol utama `[ ⬇ Ekspor Rekap ▾ ]`.
- [x] Popover melayang muncul di bawah tombol dengan alignment rapi tanpa meluap ke luar sisi kanan layar.
- [x] Popover memuat 3 pilihan format radio card (Excel, CSV, PDF) lengkap dengan badge ekstensi, deskripsi, dan tag rekomendasi/legal.
- [x] Tombol CTA unduh di bagian bawah popover menyesuaikan label secara dinamis (`Unduh Excel (.xlsx)`, `Unduh CSV (.csv)`, `Unduh PDF (.pdf)`).
- [x] Pengunduhan format PDF pendaftaran menghasilkan dokumen landscape ber-kop dinas resmi Kominfo Sleman.
- [x] Popover dapat ditutup dengan tombol "Batal", menekan tombol Escape, atau klik di area luar popover.
- [x] Seluruh automated test hijau (`100% passed`).
- [x] Code formatting Pint bersih.

