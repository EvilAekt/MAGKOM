# Plan: Poles Halaman Verifikasi Detail, Penempatan, dan Waiting List Admin Sesuai Figma & PRD

- **Tanggal**: 2026-10-11
- **Status**: Selesai (Completed)
- **Author**: Antigravity (web-implementation)
- **Rujukan**:
  - `docs/01-PRD-MagKom.md` (F-04 Verifikasi Pendaftaran & Berkas, F-06 Penempatan Magang, F-07 Waiting List, BR-05 Antrean FIFO, BR-13 Penerbitan Surat Diterima, AC-04-1 s.d. AC-04-7)
  - `docs/07-DESIGN-SYSTEM-MAGKOM.md` (Sleman Blue `#0641CC`, badge status resmi, typography Inter/Plus Jakarta Sans, kontras WCAG 2.1 AA)
  - `resources/views/admin/dashboard.blade.php` & `resources/views/admin/pendaftaran/index.blade.php` (Visual design language yang sudah diselaraskan)

---

## 1. Tujuan
Mempercantik dan memodernisasi 3 halaman anak inti operasional Admin:
1. **Detail Verifikasi Berkas & In-App PDF Preview** (`resources/views/admin/pendaftaran/show.blade.php`):
   - Merombak top metadata card dengan nomor registrasi monospaced `#REG-XXXXX`, badge status resmi, dan stempel waktu pengajuan.
   - Merapikan tata letak split-screen (60% panel kiri untuk biodata & kartu berkas berikon mime; 40% panel kanan untuk indikator kuota real-time dan tab formulir keputusan).
   - Mempercantik form penetapan keputusan bertab (*Terima*, *Waiting List*, *Minta Revisi*, *Tolak*) dengan input pembimbing, checklist beban mentee, dan validasi minimal 10 karakter catatan.
   - Menyempurnakan modal dialog in-app document preview dengan backdrop-blur, loading spinner, status error fallback yang ramah, dan tombol unduh berkas asli.
2. **Manajemen Penempatan & Surat Diterima** (`resources/views/admin/penempatan/index.blade.php`):
   - Menghadirkan 4 kartu metrik penempatan (*Total Penempatan*, *Peserta Aktif*, *Selesai Magang*, *Jalur Khusus*).
   - Memodernisasi filter bar (filter status, pembimbing, bidang dinas, dan search peserta).
   - Menata ulang tabel penempatan dengan avatar mentee & pembimbing, chip bidang dinas, badge status, tombol pratinjau surat diterima, dan tombol mutasi pembimbing.
   - Mempercantik modal mutasi pembimbing, modal penempatan manual jalur khusus, dan modal pratinjau surat penerimaan PDF in-app.
3. **Antrean Waiting List & Promosi Kuota** (`resources/views/admin/waiting-list/index.blade.php`):
   - Menghadirkan banner edukatif antrean FIFO (BR-05) dengan panel aksi penutupan antrean otomatis awal bulan (AC-04-7).
   - Menampilkan metrik ringkas antrean daftar tunggu per periode dan kuota yang tersedia untuk promosi.
   - Menata tabel antrean berurutan FIFO (Nomor urut #1, #2, stempel waktu presisi, status ketersediaan kuota jurusan).
   - Mempercantik tombol aksi cepat "Promosikan Peserta" (Sleman Blue) dan tombol peninjauan berkas.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi**:
  1. Kontrak endpoint, rute, dan controller eksisting (`admin.pendaftaran.putuskan`, `admin.penempatan.pindah-pembimbing`, `admin.penempatan.surat-diterima.pratinjau`, `admin.waiting-list.tutup`) tetap dipertahankan tanpa perubahan kontrak API agar seluruh backend logic dan audit trail tetap valid.
  2. ID selektor kritis yang digunakan dalam automated feature tests (`docPreviewModal`, `previewIframe`, `openDocPreview`, `modal-surat-preview`, `openSuratPreview`, tab buttons) WAJIB tetap ada dan dipertahankan.
  3. Desain menerapkan scoped CSS modern, konsisten dengan dashboard admin dan indeks antrean pendaftaran yang baru saja dipoles.
- **Pertanyaan Terbuka**:
  - Tidak ada ambiguitas kritis. Seluruh skenario telah tercakup dalam acceptance criteria PRD bab 10 dan feature test `AdminSuperAdminPortalTest`.

---

## 3. Pendekatan Desain & Arsitektur
- **Opsi Terpilih**: **Modular Scoped Styling & Component Alignment dengan Design System Sleman Blue**
  - Menerapkan `.admin-card`, `.admin-stat-card`, `.admin-table`, tab switcher, dan modal container dengan `backdrop-filter: blur(6px)` pada ketiga file tampilan.
  - Mempertahankan split-screen layout responsif pada halaman verifikasi detail (desktop 2-kolom 7:5 / 6:4, mobile tersusun vertikal).
  - Menyediakan visual feedback yang jelas (indikator kuota sisa kursi, beban bimbingan staf, dan konfirmasi modal).
- **Alasan Pemilihan**:
  - Memberikan konsistensi visual 100% di seluruh portal admin.
  - Menghindari regresi fungsi automated testing dan menjaga stabilitas sistem.
  - Meningkatkan kecepatan kerja staf verifikator saat memproses berkas dan surat dinas.
- **Alternatif yang Dipertimbangkan**:
  - *Alternatif A (Hanya mengubah styling warna tombol tanpa merombak layout & modal)*: Ditolak karena modal lama masih terasa kaku dan belum memanfaatkan estetika Figma MagKom yang modern.
  - *Alternatif B (Opsi Terpilih)*: Merombak menyeluruh struktur visual kartu, tabel, tab keputusan, dan dialog modal pratinjau dokumen.

---

## 4. Perubahan File
| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/pendaftaran/show.blade.php` | Modify | Memoles tampilan detail pendaftaran: split-screen modern, kartu biodata & berkas berikon, tab keputusan penerimaan/revisi/penolakan, dan modal in-app PDF preview |
| `resources/views/admin/penempatan/index.blade.php` | Modify | Memoles tampilan penempatan peserta: menambahkan metrik kartu ringkas, tabel penempatan modern dengan avatar pembimbing & mentee, modal pindah pembimbing, dan modal pratinjau surat diterima |
| `resources/views/admin/waiting-list/index.blade.php` | Modify | Memoles tampilan antrean waiting list: banner prinsip FIFO, panel penutupan otomatis awal bulan, urutan prioritas antrean, dan tombol promosi instan |
| `app/Http/Controllers/Admin/PenempatanController.php` | Modify | Menambahkan perhitungan `$penempatanStats` (total, aktif, selesai, jalur khusus) dan filter pencarian fleksibel untuk kartu metrik |
| `tests/Feature/AdminChildPagesExtendedVisualTest.php` | Create | Membuat test suite pendukung untuk memverifikasi rendering elemen baru pada ketiga halaman ini |

---

## 5. Langkah Implementasi

- [x] **Langkah 1**: Buat test pendukung `tests/Feature/AdminChildPagesExtendedVisualTest.php` untuk memvalidasi elemen tampilan baru pada ketiga halaman (detail verifikasi, penempatan, dan waiting list).
- [x] **Langkah 2**: Perbarui `resources/views/admin/pendaftaran/show.blade.php`:
  - Pasang scoped CSS styling yang kohesif (`.admin-card`, `.admin-stat-card`, tab button switcher, modal preview).
  - Percantik kartu biodata calon peserta dengan grid informasi terstruktur dan kontak langsung.
  - Percantik kartu daftar berkas persyaratan dengan ikon jenis berkas, metadata ukuran, dan tombol "Tinjau Berkas" Sleman Blue.
  - Sempurnakan indikator kuota real-time dan tab formulir keputusan (Terima dengan dropdown pembimbing & beban bimbingan, Waiting List jika kuota habis, Minta Revisi minimal 10 karakter, Tolak).
  - Modernisasi dialog modal in-app document preview (`docPreviewModal`).
- [x] **Langkah 3**: Perbarui `resources/views/admin/penempatan/index.blade.php`:
  - Tambahkan kartu metrik ringkas di atas tabel (Total Penempatan, Aktif, Selesai, Jalur Khusus) via controller & view.
  - Modernisasi toolbar filter (status, pembimbing, bidang dinas, pencarian).
  - Sempurnakan tabel penempatan peserta dengan avatar, info durasi magang, chip bidang dinas, dan dropdown/tombol aksi surat penerimaan.
  - Percantik modal dialog pindah pembimbing, modal penempatan manual jalur khusus, dan modal pratinjau surat diterima PDF in-app (`modal-surat-preview`).
- [x] **Langkah 4**: Perbarui `resources/views/admin/waiting-list/index.blade.php`:
  - Desain ulang banner informasi prinsip FIFO antrean dan tombol aksi penutupan antrean awal bulan (AC-04-7).
  - Tambahkan kartu metrik antrean daftar tunggu dan ketersediaan kuota.
  - Tampilkan tabel antrean dengan penomoran urutan prioritas yang mencolok (#1, #2, dst.), waktu pengajuan presisi, dan tombol aksi "Promosikan Peserta".
- [x] **Langkah 5**: Jalankan automated test suite `php artisan test --filter="AdminChildPagesExtendedVisualTest|AdminSuperAdminPortalTest"` untuk membuktikan seluruh pengujian lulus dan tidak ada regresi.
- [x] **Langkah 6**: Gunakan `browser_subagent` untuk memverifikasi secara visual tampilan ketiga halaman di browser (`/admin/pendaftaran/{id}`, `/admin/penempatan`, `/admin/waiting-list`), tangkap screenshot bukti, dan pastikan tidak ada layout breaking.

---

## 6. Strategi Testing
- **Unit & Feature Test**:
  - `tests/Feature/AdminSuperAdminPortalTest.php`: Memastikan `test_admin_can_view_pendaftaran_detail_with_in_app_preview_modal`, `test_admin_decision_flow_terima_places_candidate_atomically`, `test_admin_decision_flow_minta_perbaikan_and_tolak`, `test_admin_waiting_list_promotion_and_bulk_closure`, dan `test_admin_can_manage_penempatan_and_preview_surat_diterima` lulus 100%.
  - `tests/Feature/AdminChildPagesExtendedVisualTest.php`: Memastikan ketiga halaman anak baru merender seluruh metrik, tabel, modal dialog, dan elemen interaktif.
- **UI & Browser Testing**:
  - Uji halaman `/admin/pendaftaran/{id}`: Buka preview dokumen PDF/gambar via tombol "Tinjau Berkas", tes tab keputusan.
  - Uji halaman `/admin/penempatan`: Buka modal surat diterima preview, tes filter status.
  - Uji halaman `/admin/waiting-list`: Verifikasi nomor urut FIFO dan status ketersediaan kuota.

---

## 7. Risiko & Mitigasi
- **Kompatibilitas Selektor Otomatisasi**:
  - *Risiko*: JavaScript modal preview atau tes automated gagal jika ID elemen berubah.
  - *Mitigasi*: Menjaga semua ID eksisting (`docPreviewModal`, `previewIframe`, `previewImage`, `previewLoading`, `previewError`, `modal-surat-preview`, `iframe-surat-preview`) persis seperti semula.
- **Responsivitas Split-Screen**:
  - *Risiko*: Layar laptop kecil atau tablet membuat form keputusan terhimpit.
  - *Mitigasi*: Menggunakan CSS grid responsif dengan breakpoint `@media (max-width: 1024px)` yang secara otomatis mengubah layout menjadi satu kolom tersusun vertikal.
- **Aksesibilitas & Kontras Warna**:
  - *Risiko*: Teks status badge tidak memenuhi WCAG 2.1 AA.
  - *Mitigasi*: Mematuhi panduan warna token `docs/07-DESIGN-SYSTEM-MAGKOM.md` (Green 700 `#166534` pada Green 100 `#E8F7EE`, Amber 800 `#92400E` pada Amber 200 `#FEF3C7`).

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Dokumen rencana disetujui oleh pengguna.
- [x] Halaman `admin/pendaftaran/show.blade.php` berhasil dipoles dengan split-screen modern, biodata lengkap, kartu dokumen berikon, tab keputusan terstandarisasi, dan modal preview PDF elegan.
- [x] Halaman `admin/penempatan/index.blade.php` berhasil dipoles dengan metrik cards, tabel penempatan modern, modal mutasi pembimbing, dan modal surat diterima.
- [x] Halaman `admin/waiting-list/index.blade.php` berhasil dipoles dengan banner FIFO, panel tutup antrean awal bulan, urutan nomor antrean, dan tombol promosi instan.
- [x] Automated tests berjalan 100% lulus tanpa error (`PASS`).
- [x] Verifikasi browser menunjukkan tampilan yang konsisten dan memukau sesuai standar Figma MagKom.

---

## 9. Catatan Perubahan & Hasil Verifikasi
- **Hasil Automated Tests**:
  - `php artisan test --filter="AdminChildPagesExtendedVisualTest|AdminChildPagesVisualTest|AdminSuperAdminPortalTest"`: 14 tests, 80 assertions **PASS** 100% tanpa kegagalan.
- **Hasil Browser Verification**:
  - URL `/admin/pendaftaran/2`: Berhasil dirender dengan split-screen rapi, kartu biodata, badge status pendaftaran, daftar berkas berikon, tab keputusan penerimaan/waiting list/revisi/tolak, serta interaksi buka/tutup modal in-app PDF preview berjalan sempurna.
  - URL `/admin/penempatan`: Berhasil dirender dengan 4 kartu metrik (Total Penempatan, Peserta Aktif, Selesai Magang, Penempatan Manual Jalur Khusus), toolbar filter, avatar mentee & pembimbing, chip bidang dinas, tombol mutasi pembimbing, dan modal pratinjau surat diterima.
  - URL `/admin/waiting-list`: Berhasil dirender dengan banner FIFO, penomoran urutan antrean prioritas `#1, #2`, tombol promosi kuota terintegrasi dengan validasi sisa kuota, serta modal penutupan antrean awal bulan.
- **Artifacts Bukti Pengujian Visual**:
  - Screenshot Detail Pendaftaran & Form Keputusan: `pendaftaran_detail_page_1791727048663.png`
  - Screenshot In-App Document Preview Modal: `document_preview_modal_1791727057197.png`
  - Screenshot Detail Pendaftaran (Modal Ditutup): `detail_page_after_modal_closed_1791727061249.png`
  - Screenshot Manajemen Penempatan & Metrik: `penempatan_index_page_1791727065658.png`
  - Screenshot Antrean Waiting List & FIFO: `waiting_list_index_page_1791727070717.png`
  - Video Rekaman Sesi Browser: `admin_ext_pages_check_1791727039712.webp`

