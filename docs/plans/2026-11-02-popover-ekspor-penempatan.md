# Rencana Kerja: Pembuatan Popover Navigasi Ekspor Data (Excel, CSV, PDF) pada Modul Penempatan

**Tanggal Dokumen**: 2026-11-02  
**Modul**: Administrasi MagKom — Data Penempatan Peserta (`admin/penempatan`)  
**Status**: 📋 Menunggu Persetujuan Pengguna  

---

## 1. Tujuan
Memperbarui navigasi tombol ekspor data pada halaman Penempatan (`resources/views/admin/penempatan/index.blade.php`) yang saat ini berupa dua tombol terpisah statis (`[ Ekspor Rekap (.xlsx) ]` hijau dan `[ CSV ]` outline) menjadi satu tombol utama ekspor terpadu (`[ ⬇ Ekspor Data ▾ ]`). Ketika tombol ini diklik, akan muncul pop-up popover kontekstual (mengadopsi pola desain SuperAdmin Audit Log pada Screenshot 2) yang memberikan opsi format unduhan:
1. **Microsoft Excel (`.xlsx`)** — Format tabel bergaris resmi, header tebal & filterable (*Rekomendasi*).
2. **CSV Tabular (`.csv`)** — Data mentah teks pemisah koma terstandarisasi (*Cepat*).
3. **Dokumen Resmi PDF (`.pdf`)** — Rekapitulasi cetak ber-kop resmi Dinas Kominfo Sleman (*Legal Sah*).

---

## 2. Perbandingan Visual (Sebelum vs Target Referensi)

````carousel
![Kondisi Tombol Saat Ini (Dua Tombol Terpisah)](C:/Users/ALDI%20BOYS/.gemini/antigravity/brain/8ac85b61-3384-4008-bb69-7f98f2cbd22d/ekspor-tombol-sebelum.png)
<!-- slide -->
![Target Desain Referensi (Popover Pilihan Format ala SuperAdmin Audit Log)](C:/Users/ALDI%20BOYS/.gemini/antigravity/brain/8ac85b61-3384-4008-bb69-7f98f2cbd22d/ekspor-popover-referensi.png)
````

### Analisis Kesenjangan Desain:
- *Saat Ini*: Ada 2 tombol terpisah di header halaman penempatan (`Ekspor Rekap (.xlsx)` warna hijau dan `CSV` tombol outline). Tombol ini memakan ruang horizontal, kurang fleksibel, dan belum mendukung format dokumen cetak resmi PDF.
- *Target Referensi*: Menyatukan aksi ekspor ke dalam 1 tombol trigger yang memicu jendela popover melayang (*contextual popover*) di bawahnya. Admin dapat memilih format dengan radio card interaktif (Excel, CSV, PDF), melihat estimasi baris data terfilter, lalu menekan tombol unduh dinamis (`Unduh Excel`, `Unduh CSV`, `Unduh PDF`).

---

## 3. Asumsi & Evaluasi Pendekatan Teknis

### Asumsi Kerja:
1. Tombol trigger ekspor diletakkan pada `@section('page_actions')` di kanan atas halaman penempatan.
2. Seluruh filter query pencarian aktif (`q`, `bidang_id`, `pembimbing_id`, `bulan_magang_id`, `status`) secara otomatis diteruskan ke request ekspor file yang dipilih.
3. Backend controller `PenempatanController::exportRekapSuratDiterima` diperluas agar mendukung parameter `format=pdf` menggunakan `Barryvdh\DomPDF\Facade\Pdf` (kertas A4 landscape, kop surat resmi Kominfo Sleman).
4. Interaksi buka/tutup popover menggunakan Vanilla JavaScript mandiri yang mendukung *dismiss on click outside* dan *dismiss on Escape key*.

### Evaluasi Opsi Arsitektur:

| Kriteria | Opsi A: Dropdown Popover Kontekstual (Pola SuperAdmin Audit Log - Direkomendasikan) | Opsi B: Modal Dialog Tengah Layar (Center Backdrop Modal) |
| :--- | :--- | :--- |
| **Kesesuaian dengan Permintaan** | **100% Persis**: Mengikuti Screenshot 2 (pola SuperAdmin Audit Log) dengan pointer accent dan radio cards. | Menyimpang dari referensi visual yang diberikan user. |
| **Kecepatan & Ergonomi (UX)** | **Sangat Cepat**: Muncul langsung di bawah tombol tanpa menggelapkan seluruh layar (*no full backdrop*). | Berat: Harus membuka backdrop hitam penuh di atas halaman. |
| **Penggunaan Ulang Desain** | **Tinggi**: Menggunakan design token dan pola CSS yang sudah terbukti di SuperAdmin Audit Log. | Menambah modal baru yang berpotensi bentrok dengan modal lain. |

> **Rekomendasi**: Menggunakan **Opsi A** (Popover Kontekstual).

---

## 4. Permohonan Izin Perubahan Markup Blade (Sesuai Aturan AGENTS.md Bagian 3.3)

> [!IMPORTANT]
> **Alasan Markup Baru Diperlukan**:  
> Perubahan dari 2 tombol link terpisah menjadi 1 tombol trigger yang membuka floating popover memerlukan penyesuaian markup pada `@section('page_actions')` dan penambahan elemen HTML popover semantik (`role="menu"`, radio cards).

- **File yang akan diubah**: `resources/views/admin/penempatan/index.blade.php`
- **Bagian**: Baris 18–45 (`@section('page_actions')`).
- **Tindakan**:
  - Mengganti 2 tag `<a>` statis dengan wrapper `.adm-export-dropdown-wrap`.
  - Memasang tombol trigger `#btnToggleExportPopover` dengan icon unduh dan chevron panah bawah.
  - Memasang popover dialog `#admExportPopover` dengan 3 kartu radio format (Excel, CSV, PDF), info baris aktif, dan tombol aksi unduh dinamis.
  - Menambahkan script interaksi popover di bagian bawah file atau file JS terkait.

---

## 5. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/penempatan/index.blade.php` | Modify | Mengganti 2 tombol ekspor lama dengan tombol trigger dropdown popover ekspor terpadu + markup popover + event listeners. |
| `app/Http/Controllers/Admin/PenempatanController.php` | Modify | Menambahkan branch `format === 'pdf'` pada method `exportRekapSuratDiterima` untuk menghasilkan file PDF landscape ber-kop dinas. |
| `resources/views/pdf/rekap-penempatan.blade.php` | Create | Template PDF cetak resmi A4 Landscape: Kop Dinas Kominfo Sleman, tabel data penempatan terfilter, stempel/ttd pejabat. |
| `resources/css/pages/admin/dashboard.css` | Modify | Styling komponen popover ekspor (`.adm-export-popover`, `.adm-format-card`, `.adm-popover-pointer`) mendukung light & dark mode. |
| `public/css/dashboard-cockpit-unified.css` | Modify | Sinkronisasi stylesheet fallback untuk runtime dashboard. |
| `tests/Feature/AdminEksporPenempatanPopoverTest.php` | Create | Feature test untuk memverifikasi ekspor format `xlsx`, `csv`, dan `pdf` serta kelengkapan markup popover. |

---

## 6. Langkah-Langkah Implementasi Bertahap

- [x] **Fase 1: Persetujuan Pengguna**
  - [x] Mendapatkan persetujuan eksplisit pengguna atas rencana kerja dan perubahan markup Blade.
- [x] **Fase 2: Backend Controller & Template PDF (`PenempatanController.php` & `pdf/rekap-penempatan.blade.php`)**
  - [x] Buat template Blade PDF: `resources/views/pdf/rekap-penempatan.blade.php` (A4 Landscape, Kop Kominfo Sleman, tabel nomor registrasi, nama, institusi, jurusan, bidang, pembimbing, tanggal, nomor surat).
  - [x] Update `PenempatanController::exportRekapSuratDiterima`: Jika `$format === 'pdf'`, render menggunakan `Barryvdh\DomPDF\Facade\Pdf::loadView(...)` dan stream/download berkas.
  - [x] Tambahkan audit log akses data untuk format PDF.
- [x] **Fase 3: Refactoring Markup Blade (`admin/penempatan/index.blade.php`)**
  - [x] Hapus dua tombol statis lama di baris 19–45.
  - [x] Pasang tombol trigger `.adm-btn-export-main` dengan label *"Ekspor Rekap"* dan icon chevron.
  - [x] Pasang container popover `.adm-export-popover` memuat 3 pilihan kartu format:
    1. Microsoft Excel (`.xlsx`)
    2. CSV Tabular (`.csv`)
    3. Dokumen Resmi Cetak (`.pdf`)
  - [x] Pasang Vanilla JS handler: Toggle buka/tutup, pemilihan kartu radio, penyesuaian teks tombol unduh, trigger download dengan query string aktif, serta auto-close saat klik di luar popover atau tekan tombol Escape.
- [x] **Fase 4: Penyelarasan Styling CSS & Dark Mode**
  - [x] Tambahkan aturan CSS scoped untuk `.adm-export-dropdown-wrap`, `.adm-export-popover`, `.adm-format-card`, `.adm-popover-header`, `.adm-popover-cta` di `resources/css/pages/admin/dashboard.css`.
  - [x] Sinkronisasikan ke `public/css/dashboard-cockpit-unified.css`.
  - [x] Pastikan tampilan optimal pada mode terang (`#FFFFFF`) dan mode gelap (`#111827` / `#182234`).
- [x] **Fase 5: Asset Compilation & Quality Gate**
  - [x] Jalankan `npm run build` untuk mengompilasi CSS/JS Vite.
  - [x] Jalankan `php vendor/bin/pint --dirty --test` untuk memastikan kebersihan kode.
  - [x] Jalankan automated tests (PHPUnit) untuk ekspor format `xlsx`, `csv`, dan `pdf`.
- [x] **Fase 6: Verifikasi Visual dengan Playwright MCP**
  - [x] Akses browser pada halaman `http://magkom.test/admin/penempatan`.
  - [x] Klik tombol "Ekspor Rekap", verifikasi popover terbuka dengan rapi.
  - [x] Klik pilihan Excel, CSV, dan PDF untuk memastikan radio card aktif berpindah warna.
  - [x] Klik tombol unduh dan verifikasi file terunduh dengan benar.

---

## 7. Strategi Testing

1. **Automated Feature Test (`AdminEksporPenempatanPopoverTest.php`)**:
   - `test_admin_can_export_penempatan_as_xlsx()` ➔ Memastikan file Excel terunduh dengan header MIME `spreadsheetml.sheet`.
   - `test_admin_can_export_penempatan_as_csv()` ➔ Memastikan file CSV terunduh dengan teks terpisahkan koma dan sanitasi formula.
   - `test_admin_can_export_penempatan_as_pdf()` ➔ Memastikan file PDF terunduh dengan MIME `application/pdf` dan kop surat resmi.
   - `test_penempatan_page_renders_export_popover_markup()` ➔ Memastikan tombol trigger dan popover dialog ter-render di Blade.
2. **Quality Gate**:
   - `php vendor/bin/pint --dirty --test` (0 issues).
   - `npm run build` (build sukses 0 error).
3. **Manual / Visual Testing**:
   - Browser Playwright diuji pada resolusi desktop dan mobile.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Dua tombol ekspor statis lama telah digantikan oleh satu tombol utama `[ ⬇ Ekspor Rekap ▾ ]`.
- [x] Saat tombol diklik, popover melayang muncul di bawah tombol persis seperti referensi Screenshot 2.
- [x] Popover memuat 3 pilihan format radio card (Excel, CSV, PDF) lengkap dengan badge ekstensi, deskripsi, dan tag rekomendasi/legal.
- [x] Tombol CTA unduh di bagian bawah popover menyesuaikan label secara dinamis (`Unduh Excel (.xlsx)`, `Unduh CSV`, `Unduh PDF`).
- [x] Pengunduhan format PDF berjalan lancar dan menghasilkan dokumen landscape ber-kop dinas Kominfo.
- [x] Popover dapat ditutup dengan tombol "Batal", menekan tombol Escape, atau klik di sembarang area luar popover.
- [x] Semua automated tests hijau (`100% passed`).
- [x] Code formatting Pint bersih.

