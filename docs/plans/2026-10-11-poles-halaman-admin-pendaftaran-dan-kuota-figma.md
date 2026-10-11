# Plan: Poles Tampilan Halaman Anak Admin (Verifikasi Berkas & Manajemen Kuota) Sesuai Figma & PRD

- **Tanggal**: 2026-10-11
- **Status**: Selesai (Completed)
- **Author**: Antigravity (web-implementation)
- **Rujukan**:
  - `docs/01-PRD-MagKom.md` (F-04 Verifikasi Pendaftaran & Berkas, F-05 Manajemen Kuota Jurusan, BR-01 Batas Kuota 12, BR-05 Waiting List, SLA peninjauan ≤ 5 hari kerja)
  - `docs/07-DESIGN-SYSTEM-MAGKOM.md` (Sleman Blue `#0641CC`, palet warna status, typography, spacing, WCAG 2.1 AA)
  - `resources/views/admin/dashboard.blade.php` (Design language dan visual tokens operasional dashboard admin yang baru saja dirombak)

---

## 1. Tujuan
Mempercantik dan memodernisasi dua halaman operasional utama anak Admin:
1. **Indeks Antrean Verifikasi Berkas** (`resources/views/admin/pendaftaran/index.blade.php`):
   - Menghadirkan ringkasan metrik antrean (Queue Stats: Total Pengajuan, Perlu Ditinjau/Diajukan, Perlu Perbaikan, SLA Peringatan > 3 hari kerja).
   - Memodernisasi filter bar dengan *quick filter status pills*, field pencarian terintegrasi, dan dropdown filter periode/jurusan/jalur/urutan FIFO yang presisi.
   - Merombak tabel antrean dengan avatar inisial, badge nomor registrasi `#REG-XXXXX`, badge status resmi dengan ikon, indikator SLA warning, dan tombol aksi "Tinjau Berkas" Sleman Blue.
   - Menyediakan empty state informatif jika hasil filter kosong serta paginasi yang elegan.
2. **Manajemen Kuota Magang Per Jurusan** (`resources/views/admin/kuota/index.blade.php`):
   - Menghadirkan hero card periode bulan magang dilengkapi indikator rentang tanggal pendaftaran dan metrik ringkas (Total Kuota, Terisi, Sisa Kursi, Okupansi Global).
   - Menata ulang tabel kuota jurusan dengan progress bar okupansi bergradasi warna adaptif (Hijau < 75%, Kuning 75-99%, Merah 100% penuh), label jenjang (SMK vs Mahasiswa D3/S1), dan chip status aktif/nonaktif.
   - Mempercantik Modal Dialog Tambah dan Edit Kuota dengan backdrop-blur modern, header biru Sleman, input validation yang jelas (AC-05-3 kuota baru tidak boleh kurang dari kursi yang sudah terisi), dan tombol interaktif yang responsif.

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi**:
  1. Kontrak data dan variabel controller yang ada pada `PendaftaranController::index` (`$pendaftaranList`, `$bulanList`, `$jurusanList`, `$statusEnum`) dan `KuotaController::index` (`$bulanList`, `$selectedBulan`, `$jurusanList`) sudah mencukupi dan diperkaya dengan `$queueStats` agar metrik antrean akurat secara global.
  2. SLA antrean dihitung dari kolom `diajukan_at` (jika `status == 'diajukan'` dan usia pengajuan ≥ 3 hari kerja/kalender, berikan visual highlight peringatan SLA agar sesuai target SLA ≤ 5 hari kerja).
  3. Desain tetap menggunakan Vanilla CSS dan CSS Custom Properties (`var(--blue-700)`, `var(--neutral-xxx)`, dll.) yang diintegrasikan secara terstruktur ke dalam template Blade dengan styling scoped class yang rapi, konsisten dengan `resources/views/admin/dashboard.blade.php`.
- **Pertanyaan Terbuka**:
  - Tidak ada pertanyaan kritis. Seluruh rancangan telah disetujui pengguna.

---

## 3. Pendekatan Desain & Arsitektur
- **Opsi Terpilih**: **Modular Scoped Styling & Component Alignment dengan Admin Dashboard**
  - Mengadopsi design tokens dan styling class yang selaras dengan `admin/dashboard.blade.php` (`.admin-card`, `.admin-stat-card`, `.admin-table`, badge pill system, modern modals).
  - Menyediakan filter pills interaktif yang terhubung langsung dengan parameter URL query string (`?status=diajukan`, dll.) sehingga admin dapat memfilter status hanya dengan satu klik tanpa harus memilih manual dari select box.
  - Mempertahankan semua ID dan atribut selector yang dibutuhkan automated tests (`modal-tambah-kuota`, `modal-edit-kuota`, `Okupansi:`, `Tinjau Berkas`, nama-nama input form).
- **Alasan Pemilihan**:
  - Menghasilkan antarmuka yang sangat kohesif di seluruh portal admin.
  - Menjaga keutuhan 100% test suite eksisting tanpa menimbulkan *regression bug*.
  - Memberikan pengalaman pengguna (UX) yang cepat, intuitif, dan responsif bagi petugas verifikator MagKom.

---

## 4. Perubahan File
| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/pendaftaran/index.blade.php` | Modify | Merombak tampilan indeks antrean verifikasi berkas: menambahkan queue summary cards, quick status pills, modern filter toolbar, tabel antrean modern dengan SLA warning, dan empty state estetik |
| `resources/views/admin/kuota/index.blade.php` | Modify | Merombak tampilan manajemen kuota: menambahkan hero selector periode dengan okupansi global, tabel alokasi jurusan dengan linear progress bar okupansi, modal tambah dan edit kuota modern dengan backdrop-blur |
| `app/Http/Controllers/Admin/PendaftaranController.php` | Modify | Menambahkan data array `$queueStats` untuk menghitung ringkasan global antrean dan count masing-masing status |
| `tests/Feature/AdminSuperAdminPortalTest.php` | Verify | Memastikan seluruh assertion verifikasi pendaftaran dan manajemen kuota tetap berjalan 100% lulus |
| `tests/Feature/AdminChildPagesVisualTest.php` | Create | Membuat feature test pendukung untuk memverifikasi rendering elemen baru pada kedua halaman anak admin (metrik antrean, okupansi, quick filter) |

---

## 5. Langkah Implementasi
- [x] **Langkah 1**: Buat test pendukung `tests/Feature/AdminChildPagesVisualTest.php` untuk memvalidasi elemen tampilan baru pada halaman antrean verifikasi dan kuota (status badges, SLA indicator, occupancy gauge, dan modal elements).
- [x] **Langkah 2**: Perbarui `resources/views/admin/pendaftaran/index.blade.php`:
  - Pasang scoped CSS styling (`.admin-card`, `.admin-stat-card`, `.admin-table`, `.quick-filter-pills`, `.status-badge-rich`).
  - Tambahkan kartu ringkasan antrean pendaftaran (Total, Diajukan, Perlu Perbaikan, SLA Kritis).
  - Bangun ulang filter bar: Search input dengan ikon bersih, status quick filter pills, dropdown periode, jurusan, jalur, dan sort FIFO.
  - Sempurnakan tabel antrean: Kolom No. Registrasi badge, Calon Peserta dengan avatar, Jurusan & Jenjang, Periode & Durasi, Status Badge resmi dengan icon, dan tombol Tinjau Berkas Sleman Blue.
  - Tambahkan styling pagination dan empty state ilustratif.
- [x] **Langkah 3**: Perbarui `resources/views/admin/kuota/index.blade.php`:
  - Pasang scoped CSS styling yang konsisten dengan dashboard admin.
  - Tambahkan hero card periode dengan informasi jadwal pendaftaran dan KPI statistik okupansi kursi global.
  - Sempurnakan tabel alokasi kuota per jurusan: badge jenjang (SMK vs Mahasiswa D3/S1), linear progress bar okupansi (`Okupansi: X%`), indikator sisa kursi (tersedia vs penuh), toggle status aktif yang rapi.
  - Perbarui Modal Tambah Kuota (`modal-tambah-kuota`) dan Modal Edit Kuota (`modal-edit-kuota`) dengan styling backdrop blur modern, header biru Sleman, close button interaktif, dan validasi min kuota terisi.
- [x] **Langkah 4**: Jalankan test suite `php artisan test --filter="AdminSuperAdminPortalTest|AdminSuperAdminPhaseThreeTest|AdminChildPagesVisualTest"` untuk memverifikasi fungsionalitas dan tidak ada regresi.
- [x] **Langkah 5**: Buka browser via `browser_subagent` untuk meninjau secara visual halaman `/admin/pendaftaran` dan `/admin/kuota`, ambil screenshot, dan verifikasi kualitas tampilan Figma.

---

## 6. Strategi Testing
- **Unit & Integration Test**:
  - `tests/Feature/AdminSuperAdminPortalTest.php`: Memastikan `test_admin_can_access_pendaftaran_index_with_search_and_filter` dan `test_admin_quota_management_index_renders_occupancy_bars` lulus (PASS).
  - `tests/Feature/AdminChildPagesVisualTest.php`: Memastikan halaman merender seluruh metrik antrean, filter status, tabel kuota dengan okupansi bar, dan modal alokasi kuota (PASS).
- **UI & Visual Verification**:
  - Halaman `/admin/pendaftaran` dan `/admin/kuota` diverifikasi via subagent browser, menghasilkan screenshot bukti:
    - `admin_pendaftaran_index_1791723751649.png`
    - `admin_kuota_index_1791723758902.png`
    - `admin_kuota_modal_check_1791723782538.png`

---

## 7. Risiko & Mitigasi
- Seluruh mitigasi berhasil dipertahankan:
  - Selector automated testing (`modal-tambah-kuota`, `modal-edit-kuota`, `Okupansi:`, dll.) tetap utuh.
  - Layout sepenuhnya adaptif dengan flexbox dan overflow yang aman di mobile maupun desktop.
  - Kontras warna teks memenuhi kriteria WCAG 2.1 AA.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Dokumen rencana disetujui oleh pengguna.
- [x] Halaman `admin/pendaftaran/index.blade.php` berhasil dipoles dengan kartu ringkasan, filter pills, tabel modern, SLA warning, dan tombol aksi Sleman Blue.
- [x] Halaman `admin/kuota/index.blade.php` berhasil dipoles dengan periode card, metrik okupansi, visual progress bar, dan modal dialog kontemporer.
- [x] Automated tests berjalan lancar tanpa error (`27 passed, 141 assertions`).
- [x] Verifikasi visual di browser menunjukkan tampilan yang rapi, modern, dan sesuai Figma Design System.

---

## 9. Catatan Perubahan & Saran Lanjutan
- **Perubahan selama pengerjaan**:
  - Menambahkan perhitungan `$queueStats` pada `PendaftaranController::index` agar 4 kartu metrik dan badge counter pada pills selalu akurat merefleksikan seluruh database secara real-time.
- **Saran Lanjutan (Out of Scope)**:
  - Fitur ekspor data antrean berkas ke spreadsheet Excel/CSV langsung dari toolbar antrean.
  - Filter interaktif berbasis AJAX tanpa reload penuh untuk tabel kuota.
