# Plan: Revamp Card Laporan Rekapitulasi (Gambar 1) & Penyelarasan Garis Card Lebih Jelas/Tebal

**Tanggal**: 04 November 2026  
**Status**: Menunggu Konfirmasi Pengguna  
**Referensi**: Permintaan User, Tangkapan Layar `Gambar 1` (`media_1793772504999_0bd4b2ad.png`) & `Gambar 2` (`media_1793772757025_1a21a984.png`), serta aturan `AGENTS.md`.

---

## 1. Tujuan
1. **Revamp Card "Laporan Rekapitulasi Pendaftaran" (Gambar 1)**:  
   Mengubah tampilan kartu tabel laporan pada `resources/views/admin/laporan/index.blade.php` (khususnya Tab 1 Rekapitulasi Pendaftaran, serta Tab 2 Penempatan & Tab 3 Nilai agar konsisten) yang saat ini menggunakan kontainer `div` inline mentah tanpa aksen khas, menjadi komponen standar resmi **Gentelella Panel (`<x-gentelella.x-panel>`)** lengkap dengan aksen garis biru atas (`border-top: 3.5px solid var(--magkom-primary-blue, #01347C)`), struktur judul & deskripsi (`.x_title`), penempatan tombol ekspor yang rapi, toolbar filter yang terstruktur, dan pembungkus tabel standar (`.table-responsive-gentelella` / `.adm-table-container`).
2. **Mempertebal & Memperjelas Garis Tepi Card (Border Clarity & Thickness Enhancement)**:  
   Menyelaraskan seluruh batas garis tepi (*card borders*) di aplikasi (Dashboard Hero Stats, Aside Panels `Jadwal Pendaftaran` & `Perlu Tindakan`, Data Table Cards, Filter Cards, dan Statusline) dari yang sebelumnya terlalu tipis/pudar (`1px solid #E2E8F0` / `var(--neutral-200)`) menjadi garis yang tegas, bersih, dan tampak jelas (`1.5px solid #CBD5E1` pada mode terang dan `1.5px solid #3F51B5` / `#26354D` pada mode gelap) sesuai referensi garis tepi yang ditunjukkan pengguna pada Gambar 2/Gambar 10.

---

## 2. Asumsi & Kebutuhan Izin Khusus (AGENTS.md Rule 3.3)

> [!IMPORTANT]
> **Pemberitahuan Pengecualian Perubahan Markup Blade**:  
> Sesuai aturan `AGENTS.md` Bagian 3.3, perubahan markup pada Blade hanya dilakukan apabila struktur HTML mutlak diperlukan dan wajib meminta persetujuan pengguna terlebih dahulu:
> - **File Blade**: `resources/views/admin/laporan/index.blade.php` (baris 357–587 pada Tab 1 Rekap Pendaftaran, serta baris 589–660 pada Tab 2 dan 760–830 pada Tab 3).
> - **Alasan**: Saat ini Laporan dibungkus menggunakan `<div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); ...">` mentah dengan tabel inline, bukan kontainer semantik `<x-gentelella.x-panel>`. Perubahan ini diperlukan agar Laporan memiliki struktur visual, aksen top border biru, collapse toolbox, dan tampilan rapi yang identik dengan halaman `admin/penempatan` dan `admin/pendaftaran`.
> - **Jaminan**: Tidak ada penambahan CSS atau JavaScript langsung di dalam file Blade. Semua penataan kelas memanfaatkan CSS terpusat di `resources/css/`.

---

## 3. Evaluasi Pendekatan Teknis

| Parameter | Opsi A: Komponen Gentelella Resmi & Token Border CSS Terpusat (Rekomendasi) | Opsi B: CSS Hack / Overrides Inline Tanpa Ubah Blade |
| :--- | :--- | :--- |
| **Konsistensi Visual** | Sangat Tinggi — 100% identik dengan kartu di Dashboard & Penempatan (top accent biru, header title/subtitle rapi). | Rendah — Masih berupa div mentah tanpa header Gentelella resmi. |
| **Kualitas Garis Border** | Konsisten di seluruh sistem (`1.5px solid #CBD5E1`) via `cards.css`, `tables.css`, dan `dashboard.css`. | Terisolasi dan berisiko timpang dengan elemen lain. |
| **Kepatuhan AGENTS.md** | Mematuhi prinsip komponen reusable, zero CSS di Blade, dan memohon izin eksplisit perubahan markup. | Melanggar prinsip kerapian kode dengan mengandalkan selector CSS atribut yang rapuh. |
| **Dampak Regresi** | Nol — Logika controller, route, dan ekspor data tidak tersentuh sama sekali. | Berpotensi memengaruhi elemen div lain yang memiliki inline style mirip. |

**Keputusan**: Memilih **Opsi A**.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/laporan/index.blade.php` | Modify | Bungkus Tab 1, 2, dan 3 menggunakan `<x-gentelella.x-panel class="has-table" title="..." subtitle="...">`, letakkan tombol aksi ekspor di slot header/toolbox, rapikan filter form dan bungkus tabel dalam container standar. |
| `resources/css/components/cards.css` | Modify | Ubah border dasar card (`.x_panel`, `.card`, `.gentelella-card`, `.tile_stats_inner.stat`, `.cv-card`, `.sa-panel`) dari `1px solid ...` menjadi `1.5px solid #CBD5E1` (mode terang) dan `1.5px solid #3F51B5` (mode gelap). |
| `resources/css/components/tables.css` | Modify | Perbarui border `.x_panel` dan `.adm-table-container` menjadi `1.5px solid #CBD5E1`. |
| `resources/css/pages/admin/dashboard.css` | Modify | Pertebal border pada `.tile_stats_inner.stat`, `.adm-schedule-card`, `.cv-statusline`, `.cv-attention-card`, dan card aside menjadi `1.5px solid #CBD5E1`. |
| `resources/views/admin/penempatan/index.blade.php` | Modify | Perbarui class `.admin-card` dari `1px solid var(--neutral-200, #E2E8F0)` menjadi `1.5px solid #CBD5E1` agar selaras dengan seluruh panel. |
| `public/css/dashboard-cockpit-unified.css` | Modify | Sinkronisasi penyesuaian border card `1.5px solid #CBD5E1` untuk lingkungan cockpit terpadu. |

---

## 5. Langkah Implementasi Bertahap

### Fase 1: Standarisasi Garis Tepi Card (Border Thickness & Visibility) di CSS
- [ ] Edit `resources/css/components/cards.css`:
  - Pertebal border `.x_panel`, `.card`, `.gentelella-card`, `.cv-card`, `.gentelella-filter-bar`, `.sa-panel` menjadi `1.5px solid #CBD5E1` (mode terang) dan `1.5px solid #3F51B5` (mode gelap).
  - Pertebal border `.tile_stats_inner.stat` (Hero KPI cards) menjadi `1.5px solid #CBD5E1 !important` (mode terang) dan `1.5px solid #3F51B5 !important` (mode gelap).
- [ ] Edit `resources/css/components/tables.css`:
  - Perbarui border `.x_panel` dan `.adm-table-container` menjadi `1.5px solid #CBD5E1`.
- [ ] Edit `resources/css/pages/admin/dashboard.css`:
  - Pertebal border `.adm-metric-card-col .tile_stats_inner.stat`, `.adm-schedule-card`, `.cv-statusline`, `.cv-attention-card` menjadi `1.5px solid #CBD5E1`.
- [ ] Edit `public/css/dashboard-cockpit-unified.css`:
  - Sinkronisasikan nilai border card menjadi `1.5px solid #CBD5E1`.

### Fase 2: Revamp Card Laporan Rekapitulasi (Gambar 1) di Blade
- [ ] Edit `resources/views/admin/laporan/index.blade.php`:
  - Ganti container pembungkus Tab 1 (`pendaftaran`) menjadi `<x-gentelella.x-panel class="has-table" title="Laporan Rekapitulasi Pendaftaran" subtitle="Data calon peserta pendaftaran magang berdasarkan bulan magang dan status keputusan.">`.
  - Tempatkan tombol trigger popover ekspor rekapitulasi secara rapi di dalam slot header/toolbox `<x-slot:actions>`.
  - Rapikan toolbar filter pendaftaran menggunakan kelas kontainer filter ber-border `1.5px solid #CBD5E1`.
  - Rapikan tabel preview dengan membungkusnya ke dalam `.table-responsive-gentelella` / `.adm-table-container` ber-border rapi dengan striping dan header kolom kontras.
  - Terapkan struktur `<x-gentelella.x-panel>` serupa pada Tab 2 (Penempatan) dan Tab 3 (Nilai) agar seluruh modul Laporan serasi.
- [ ] Edit `resources/views/admin/penempatan/index.blade.php`:
  - Perbarui style `.admin-card` agar bordernya seragam `1.5px solid #CBD5E1`.

### Fase 3: Kompilasi Aset & Quality Gate
- [ ] Jalankan kompilasi frontend Vite: `npm run build`.
- [ ] Jalankan automated feature tests:
  - `php artisan test tests/Feature/AdminTablePaginationStandardTest.php`
  - `php artisan test tests/Feature/AdminDashboardEnhancementTest.php`
  - `php artisan test tests/Feature/AdminChildPagesVisualTest.php`
- [ ] Jalankan PHPUnit test suite keseluruhan: `php artisan test`.
- [ ] Jalankan linter formatting: `./vendor/bin/pint --test`.

---

## 6. Strategi Pengujian (Testing Strategy)
- **Feature Tests**: Memastikan halaman Laporan, Penempatan, dan Dashboard tetap mengembalikan HTTP 200, paginasi berjalan normal, filter berjalan presisi, dan ekspor rekap tetap berfungsi.
- **Visual & Contrast Verification**: Memverifikasi bahwa garis tepi card di browser terlihat kontras, tegas, proporsional, dan tidak pudar baik pada latar belakang abu-abu muda (`#F8FAFC`) maupun putih.

---

## 7. Risiko & Mitigasi
- **Risiko**: Perubahan markup Blade di `laporan/index.blade.php` dapat memutus event listener JavaScript untuk popover ekspor (`btnToggleExportPopoverPendaftaran`).
  - **Mitigasi**: Seluruh ID elemen, data attributes, event handlers, dan fungsi JavaScript ekspor (`toggleAdmExportPopoverPendaftaran`, `triggerPendaftaranExport`, dsb.) dipertahankan 100% tanpa mengubah nama atau alur skrip.
- **Risiko**: Border `1.5px` tampak aneh jika tidak konsisten di sebagian card.
  - **Mitigasi**: Pembaruan dilakukan serentak pada komponen pondasi (`cards.css`, `tables.css`, `dashboard.css`, `.admin-card`), sehingga seluruh kartu di seluruh halaman otomatis memiliki ketebalan dan warna garis tepi yang sama persis.

---

## 8. Definisi Selesai (Definition of Done)
- [ ] Card di Laporan Rekapitulasi Pendaftaran (Gambar 1) tampil rapi menggunakan `<x-gentelella.x-panel>` dengan garis aksen biru atas.
- [ ] Garis tepi seluruh card, stat cards, dan tabel di aplikasi dipertebal menjadi `1.5px solid #CBD5E1` dan terlihat jelas sesuai referensi Gambar 2/Gambar 10.
- [ ] `npm run build` sukses tanpa warning/error.
- [ ] Semua pengujian PHPUnit lulus hijau (29+ tests passed).
- [ ] Format kode lolos `./vendor/bin/pint --test`.

