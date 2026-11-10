# Master Plan PRD: Eksekusi Redesign Menyeluruh Seluruh Child Pages & Sub-Modul ke Kerangka Gentelella (Fase 3)
## (Buku Panduan Tugas Senior Software Architect → Junior Developer / Gemini 3.8 Flash)

- **Tanggal Rilis**: 22 Oktober 2026
- **Branch Target**: `test-2`
- **Status Dokumen**: 📝 Siap Dieksekusi Bertahap (Sprint Ready)
- **Penulis**: Senior Software Architect
- **Target Pembaca / Pelaksana**: Junior Developer dan Model AI Hemat Biaya (misal: **Gemini 3.8 Flash**).
- **Prinsip Utama Dokumen**: *Atomic tasks*, instruksi deterministik, tanpa abstraksi mengambang, dilengkapi daftar elemen sakral (anti-regresi), resep kode konkret (*Blade snippets*), dan perintah uji mandiri per tugas.

---

## 0. Panduan Etika Kerja & Aturan Sakral (Wajib Dipatuhi 100%)

Sebagai Junior Developer atau Model AI pelaksana, kamu **harus membaca bagian ini sebelum menyentuh file kode apa pun**. Kegagalan mematuhi aturan ini akan merusak pengujian (*test breaking*) dan melanggar arsitektur sistem.

```text
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                        5 ATURAN SAKRAL (GOLDEN GUARDRAILS)                             │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ 1. ZERO BACKEND CODE MUTATION: Dilarang sentuh Controller, Model, DB Migration, Routes.│
│ 2. SACRED DOM IDS & TEXT STRINGS: Dilarang hapus/ubah ID modal, nama form, string test.│
│ 3. ZERO JQUERY & BOOTSTRAP 3: Dilarang impor library usang; wajib Vanilla CSS & JS.   │
│ 4. FORM INTEGRITY: Wajib mempertahankan @csrf, @method('PUT/PATCH/DELETE'), name="..." │
│ 5. VERIFIKASI SEBELUM & SESUDAH: Wajib jalankan 'php artisan test' di setiap tugas.    │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 1. Katalog Komponen Gentelella Standar Proyek (Wajib Digunakan)

Seluruh komponen reusable telah tersedia di `resources/views/components/gentelella/`. Gunakan pola pemanggilan berikut:

```blade
{{-- 1. Header Judul Halaman --}}
<x-gentelella.page-title title="Judul Halaman" description="Deskripsi singkat fungsi halaman.">
    <a href="{{ route('admin.dashboard') }}" class="btn-action-gentelella btn-action-primary">Aksi Cepat</a>
</x-gentelella.page-title>

{{-- 2. Kartu Metrik Angka --}}
<div class="row tile_count">
    <x-gentelella.tile-stat title="Label Metrik" value="128" sub="Keterangan tambahan" />
</div>

{{-- 3. Panel Kartu Konten --}}
<x-gentelella.x-panel title="Judul Panel" subtitle="Keterangan panel">
    <!-- Isi konten tabel atau formulir di sini -->
</x-gentelella.x-panel>

{{-- 4. Toolbar Filter & Pencarian --}}
<x-gentelella.filter-bar action="{{ url()->current() }}" method="GET">
    <div class="gentelella-filter-group">
        <label for="filter-id">Status</label>
        <select name="status" id="filter-id" onchange="this.form.submit()">
            <option value="">Semua Status</option>
        </select>
    </div>
    <div class="gentelella-search-box">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari data..." />
    </div>
</x-gentelella.filter-bar>

{{-- 5. Tabel Data Gentelella --}}
<x-gentelella.table :headers="['No', 'Nama', 'Institusi', 'Status', 'Aksi']">
    @foreach($items as $i => $item)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $item->nama }}</td>
            <td>{{ $item->institusi }}</td>
            <td><x-badge :status="$item->status" /></td>
            <td><x-gentelella.action-btn variant="primary" href="...">Lihat</x-gentelella.action-btn></td>
        </tr>
    @endforeach
    <x-slot:pagination>{{ $items->links() }}</x-slot:pagination>
</x-gentelella.table>

{{-- 6. Modal Popup Dialog --}}
<x-gentelella.modal id="idModalKu" title="Judul Dialog" size="md">
    <p>Isi dialog modal di sini.</p>
    <x-slot:footer>
        <button type="button" class="btn-action-gentelella btn-action-secondary" onclick="closeGentelellaModal('idModalKu')">Batal</button>
        <button type="submit" class="btn-action-gentelella btn-action-primary">Simpan</button>
    </x-slot:footer>
</x-gentelella.modal>
```

---

## 2. Rincian Paket Tugas Sprint (Step-by-Step Task Execution)

---

### SIKLUS 1: Modul Operasional Seleksi & Penempatan Admin

#### 📌 TUGAS-1.1: Penyelarasan Halaman Detail Verifikasi & In-App PDF Previewer
- **Target File**: `resources/views/admin/pendaftaran/show.blade.php`
- **Tingkat Kesulitan**: 🟡 Sedang (1 Jam)
- **Tujuan**: Merestrukturisasi detail biodata peserta, berkas dokumen, dan form keputusan verifikasi ke dalam 2 kolom grid Gentelella (`col-md-7` dan `col-md-5`).
- **Elemen SAKRAL yang DILARANG HAPUS**:
  - Modal ID: `id="docPreviewModal"`
  - Fungsi JS: `openDocPreview(...)`, `closeDocPreview(...)`
  - Teks: `Keputusan Verifikasi`, `$pendaftaran->nomor_pendaftaran`, `$pendaftaran->user->nama`
  - Input form radio/tombol: `status` (`diterima`, `ditolak`, `perlu_perbaikan`, `waiting_list`), field catatan revisi `catatan`.
- **Langkah Kerja**:
  1. Pasang `<x-gentelella.page-title>` berisi nama pendaftar dan nomor registrasi.
  2. Bungkus kolom kiri dengan `<x-gentelella.x-panel title="Biodata Calon Peserta">` dan `<x-gentelella.x-panel title="Dokumen Persyaratan Magang">`.
  3. Bungkus kolom kanan dengan `<x-gentelella.x-panel title="Keputusan Verifikasi">`.
  4. Pertahankan markup modal `docPreviewModal` di bawah view.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter=AdminChildPagesExtendedVisualTest
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Halaman menampilkan 2 kolom panel Gentelella.
  - [ ] ID `docPreviewModal` dan fungsi `openDocPreview` aktif tanpa error JS console.
  - [ ] `php artisan test --filter=AdminChildPagesExtendedVisualTest` 100% PASS.

---

#### 📌 TUGAS-1.2: Redesign Halaman Penempatan Peserta 2-Tahap & Modal Surat Diterima
- **Target File**:
  - `resources/views/admin/penempatan/index.blade.php`
  - `resources/views/admin/penempatan/show.blade.php`
- **Tingkat Kesulitan**: 🟡 Sedang (1 Jam)
- **Tujuan**: Menyelaraskan alur penempatan peserta (Tahap 1: Penetapan Bidang & Pembimbing, Tahap 2: Penerbitan Surat Diterima) dengan panel Gentelella.
- **Elemen SAKRAL yang DILARANG HAPUS**:
  - Modal ID: `id="modal-surat-preview"`
  - Fungsi JS: `openSuratPreview(...)`, `closeSuratPreview(...)`
  - Teks: `Penempatan`, nama peserta pendaftar
  - Filter: `bulan_magang_id`, `bidang_id`, `jalur`
- **Langkah Kerja**:
  1. Pada `penempatan/index.blade.php`:
     - Tambahkan tile metrik statistik penempatan (Siap Ditempatkan, Aktif Magang, Jalur Khusus).
     - Bungkus tabel peserta siap ditempatkan dengan `<x-gentelella.x-panel title="Peserta Siap Ditempatkan">`.
     - Bungkus tabel peserta aktif magang dengan `<x-gentelella.x-panel title="Daftar Penempatan Aktif">`.
  2. Pada `penempatan/show.blade.php`:
     - Tampilkan profil penugasan mentee dan pembimbing dalam format kartu Gentelella yang bersih.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="AdminPenempatanDuaTahapTest|AdminPenempatanJalurKhususTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Halaman penempatan mengadopsi `<x-gentelella.x-panel>` dan styling Gentelella.
  - [ ] Modal pratinjau surat diterima berfungsi normal.
  - [ ] Test penempatan 100% PASS.

---

#### 📌 TUGAS-1.3: Penyelarasan Halaman Manajemen Kuota Jurusan & Alokasi Bidang
- **Target File**: `resources/views/admin/kuota/index.blade.php`
- **Tingkat Kesulitan**: 🟡 Sedang (1 Jam)
- **Tujuan**: Memastikan tabel kuota per jurusan dan modal dialog tambah/ubah kuota mengadopsi struktur Gentelella tanpa merusak selector modal.
- **Elemen SAKRAL yang DILARANG HAPUS**:
  - Modal IDs: `id="modal-tambah-kuota"`, `id="modal-edit-kuota"`, `id="modal-buat-bulan"`, `id="modal-ubah-bulan"`
  - Teks: `Manajemen Kuota Magang`, `Sistem Informasi`, `Okupansi:`, `Alokasikan Kuota Jurusan`
  - Input: `jumlah`, `jurusan_id[]`, `bulan_magang_id`
- **Langkah Kerja**:
  1. Pastikan baris atas memuat kartu ringkasan kuota dengan teks `Okupansi: ...%`.
  2. Bungkus tabel alokasi kuota per jurusan dengan `<x-gentelella.x-panel title="Alokasi Kuota Jurusan">`.
  3. Pastikan tombol pembuka modal mempertahankan teks `Alokasikan Kuota Jurusan`.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter=AdminKuotaRevampTest
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Modal `modal-tambah-kuota` dan `modal-edit-kuota` beroperasi normal.
  - [ ] Seluruh assertion teks kuota terpenuhi.
  - [ ] Test `AdminKuotaRevampTest` 100% PASS.

---

#### 📌 TUGAS-1.4: Redesign Hub Pelaporan & Statistik Tahunan Admin
- **Target File**:
  - `resources/views/admin/laporan/index.blade.php`
  - `resources/views/admin/laporan/tahunan.blade.php`
- **Tingkat Kesulitan**: 🟢 Mudah (45 Menit)
- **Tujuan**: Menata kartu ekspor CSV/PDF dan analitik tahunan peserta magang ke dalam grid Gentelella.
- **Langkah Kerja**:
  1. Pasang `<x-gentelella.page-title title="Pusat Laporan & Ekspor Data" description="Unduh laporan berkala, statistik penerimaan, dan rekapitulasi dinas." />`.
  2. Tata modul kartu ekspor (Rekap Peserta, Nilai, Demografi Kampus) dalam grid `col-md-4` bergaya Gentelella.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter=AdminLaporanDanArsipTest
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Tampilan halaman laporan rapi dan seragam.
  - [ ] Tombol download CSV/PDF tidak mengalami broken link.
  - [ ] Test laporan 100% PASS.

---

#### 📌 TUGAS-1.5: Redesign Registri Sertifikat Digital & Survei IKM Kepuasan
- **Target File**:
  - `resources/views/admin/sertifikat/index.blade.php`
  - `resources/views/admin/survei/index.blade.php`
- **Tingkat Kesulitan**: 🟢 Mudah (45 Menit)
- **Tujuan**: Menyelaraskan tabel verifikasi nomor sertifikat magang dan visualisasi skor IKM/NPS ke dalam format Gentelella.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="SurveiKepuasanAdminVisualTest|AdminChildPagesExtendedVisualTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Tabel sertifikat dan skor survei mengadopsi `<x-gentelella.x-panel>`.
  - [ ] Empty state dan filter berfungsi normal.
  - [ ] Test survei & sertifikat 100% PASS.

---

### SIKLUS 2: Modul Pembimbing Lapangan (Mentoring & Evaluasi Rubrik)

#### 📌 TUGAS-2.1: Redesign Detail Mentee 360° & Radar Capaian Kompetensi
- **Target File**: `resources/views/pembimbing/peserta/show.blade.php`
- **Tingkat Kesulitan**: 🟡 Sedang (1 Jam)
- **Tujuan**: Mengubah halaman profil 360° anak bimbingan menjadi layout panel Gentelella bertab modern (Tab Ringkasan, Tab Tugas, Tab Logbook, Tab Nilai).
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Informasi peserta: Nama mentee, NIM/NISN, Asal Kampus, Jurusan, Periode Magang
  - Container grafik progres / logbook tracker
  - Tombol aksi: "Beri Tugas", "Isi Penilaian"
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="DeltaDashboardEnhancementTest|PembimbingFullJobDeskTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Profil mentee 360° mengadopsi format Gentelella.
  - [ ] Tab switching berjalan lancar tanpa jQuery.
  - [ ] Test profil mentee 100% PASS.

---

#### 📌 TUGAS-2.2: Redesign Workspace Penugasan Classroom & Bank Template Pembimbing
- **Target File**:
  - `resources/views/pembimbing/tugas/index.blade.php`
  - `resources/views/pembimbing/tugas/create.blade.php`
  - `resources/views/pembimbing/tugas/edit.blade.php`
  - `resources/views/pembimbing/tugas/show.blade.php`
  - `resources/views/pembimbing/tugas/template/index.blade.php`
- **Tingkat Kesulitan**: 🟡 Sedang (1,5 Jam)
- **Tujuan**: Menata antarmuka pembuatan tugas, evaluasi submisi mentee, dan bank template tugas bergaya Google Classroom ke dalam card/panel Gentelella.
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Form input: `judul`, `deskripsi`, `deadline`, `kategori`, `kompleksitas`, upload `lampiran`
  - Tombol aksi reviu progres dan feedback catatan revisi
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="TugasLogbookTest|RichTaskAssignmentTest|PembimbingIdorProtectionTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Form pembuatan dan edit tugas dibungkus `<x-gentelella.x-panel>`.
  - [ ] Evaluasi submisi progres mentee tetap berfungsi.
  - [ ] Test tugas pembimbing 100% PASS.

---

#### 📌 TUGAS-2.3: Redesign Lembar Evaluasi Rubrik & Laporan Kinerja Bimbingan
- **Target File**:
  - `resources/views/pembimbing/penilaian/show.blade.php`
  - `resources/views/pembimbing/laporan-kinerja/index.blade.php`
  - `resources/views/pembimbing/laporan-kinerja/cetak.blade.php`
- **Tingkat Kesulitan**: 🟡 Sedang (1 Jam)
- **Tujuan**: Menyelaraskan formulir input nilai rubrik matriks kompetensi, validasi nilai akhir, riwayat revisi nilai, dan pratinjau lembar nilai cetak.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="ScoringAndCompletionTest|PredikatBatasValidationTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Form rubrik nilai rapi dan terstruktur dalam panel Gentelella.
  - [ ] Fitur ekspor lembar nilai PDF resmi tidak rusak.
  - [ ] Test penilaian 100% PASS.

---

### SIKLUS 3: Modul Peserta Magang (Student Workspace & Pemberkasan)

#### 📌 TUGAS-3.1: Redesign Profil Biodata Dinamis, Rekening & Persetujuan Wali PDP
- **Target File**: `resources/views/peserta/profil/show.blade.php`
- **Tingkat Kesulitan**: 🟢 Mudah (45 Menit)
- **Tujuan**: Menata tampilan profil peserta magang dengan header avatar Gentelella, informasi universitas/sekolah, nomor rekening, dan klausul persetujuan wali sesuai regulasi UU PDP.
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Form fields: `nama`, `nim_nisn`, `institusi`, `jurusan_id`, `no_hp`, `alamat`, `foto`, field kontak darurat / wali
  - Checkbox persetujuan pemrosesan data pribadi (UU PDP)
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="PesertaUuPdpPersetujuanWaliTest|PesertaPortalWorkflowTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Form profil dibungkus panel Gentelella yang bersih.
  - [ ] Upload foto profil dan pembaruan kontak darurat berhasil.
  - [ ] Test profil peserta 100% PASS.

---

#### 📌 TUGAS-3.2: Redesign Alur Pendaftaran Magang & Upload Dokumen Berkas Peserta
- **Target File**:
  - `resources/views/peserta/pendaftaran/index.blade.php`
  - `resources/views/peserta/pendaftaran/detail.blade.php`
- **Tingkat Kesulitan**: 🟡 Sedang (1,5 Jam)
- **Tujuan**: Menyelaraskan wizard pemilihan periode & jurusan, panel upload file berkas (CV, Surat Pengantar, Proposal), dan modal pratinjau dokumen dengan tema Gentelella.
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Modal ID: `id="docPreviewModal"`
  - Fungsi JS: `openDocPreview(...)`, `closeDocPreview(...)`
  - Form upload berkas: input file dengan name sesuai jenis dokumen (cv, surat_pengantar, proposal, transkrip, ktp)
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="PendaftaranPesertaTest|PesertaPendaftaranFlowTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Wizard langkah pendaftaran mengadopsi indikator step Gentelella.
  - [ ] Modal `docPreviewModal` ada dan berfungsi normal.
  - [ ] Test pendaftaran peserta 100% PASS.

---

#### 📌 TUGAS-3.3: Redesign Timeline Logbook Harian Peserta
- **Target File**: `resources/views/peserta/logbook/index.blade.php`
- **Tingkat Kesulitan**: 🟡 Sedang (1 Jam)
- **Tujuan**: Menata banner status penempatan, formulir entri logbook harian, dan feed riwayat aktivitas magang ke dalam arsitektur Gentelella.
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Form input: `tanggal`, `kegiatan`, `kendala`, `tautan_lampiran`, `file_lampiran`
  - Teks filter dan status verifikasi pembimbing (Disetujui, Perlu Revisi)
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="PesertaPortalWorkflowTest|PesertaClassroomTugasLogbookTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Form logbook dibungkus `<x-gentelella.x-panel>`.
  - [ ] Timeline riwayat kegiatan tersusun rapi.
  - [ ] Test logbook peserta 100% PASS.

---

#### 📌 TUGAS-3.4: Redesign Workspace Penugasan Classroom & Transkrip Nilai Peserta
- **Target File**:
  - `resources/views/peserta/tugas/index.blade.php`
  - `resources/views/peserta/tugas/show.blade.php`
  - `resources/views/peserta/nilai/show.blade.php`
  - `resources/views/peserta/survei/index.blade.php`
- **Tingkat Kesulitan**: 🟡 Sedang (1 Jam)
- **Tujuan**: Menyelaraskan halaman daftar tugas, halaman pengumpulan tugas 2-kolom, transkrip capaian nilai, dan kuesioner survei magang.
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Teks sakral di `tugas/index.blade.php`: `Tugas & Proyek Magang`, `Semua Tugas`, `Ditugaskan`, `Sedang Dikerjakan`
  - Teks sakral di `tugas/show.blade.php`: `Tugas Anda`, `Panduan Pengerjaan`, `Serahkan / Kirim Progres`
  - Form pengiriman tugas: field link URL dan upload lampiran
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="PesertaClassroomTugasLogbookTest|ScoringAndCompletionTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Teks sakral `Tugas Anda` dan `Panduan Pengerjaan` tetap utuh.
  - [ ] Form kirim progres tugas berfungsi normal.
  - [ ] Test tugas dan nilai peserta 100% PASS.

---

### SIKLUS 4: Modul SuperAdmin & Observabilitas Tata Kelola

#### 📌 TUGAS-4.1: Redesign Master Bidang Dinas, Pemetaan Jurusan & Slot Khusus
- **Target File**:
  - `resources/views/superadmin/bidang/index.blade.php`
  - `resources/views/superadmin/jurusan/index.blade.php`
  - `resources/views/superadmin/slot-khusus/index.blade.php`
- **Tingkat Kesulitan**: 🟡 Sedang (1 Jam)
- **Tujuan**: Menyelaraskan tabel master Bidang Kominfo Sleman, master Jurusan yang Dibutuhkan, dan alokasi Slot Khusus kemitraan ke dalam `<x-gentelella.x-panel>` dan `<x-gentelella.table>`.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="BidangJurusanMappingTest|SuperAdminPortalVisualTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Tabel master data menggunakan styling Gentelella.
  - [ ] Modal dialog tambah/edit bidang dan jurusan berfungsi normal.
  - [ ] Test bidang & jurusan 100% PASS.

---

#### 📌 TUGAS-4.2: Redesign Tata Kelola Staf, Akun & Matriks Otorisasi RBAC
- **Target File**:
  - `resources/views/superadmin/staf/index.blade.php`
  - `resources/views/superadmin/akun/index.blade.php`
  - `resources/views/superadmin/role/index.blade.php`
  - `resources/views/superadmin/rubrik/index.blade.php`
- **Tingkat Kesulitan**: 🟡 Sedang (1,5 Jam)
- **Tujuan**: Menata daftar staf dinas, reset kata sandi, matriks permission RBAC, dan bobot rubrik evaluasi ke dalam panel Gentelella.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="SuperAdminFigmaDesignAlignmentTest|StaffProfileAndSettingsTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Matriks role dan tabel staf tertata rapi.
  - [ ] Seluruh tombol aksi staf bekerja normal.
  - [ ] Test RBAC & staf 100% PASS.

---

#### 📌 TUGAS-4.3: Redesign Observabilitas: Audit Trail 15-Channel, Telemetri Health, Backup & Monitoring
- **Target File**:
  - `resources/views/superadmin/audit-log/index.blade.php`
  - `resources/views/superadmin/health/index.blade.php`
  - `resources/views/superadmin/arsip/index.blade.php` & `show.blade.php`
  - `resources/views/superadmin/backup/index.blade.php`
  - `resources/views/superadmin/monitoring/index.blade.php` & `show.blade.php`
- **Tingkat Kesulitan**: 🔴 Tinggi (2 Jam)
- **Tujuan**: Menyelaraskan audit trail multi-channel (filter channel, badge severity, keyword search), dashboard telemetri kesehatan server (4 aksi diagnostik), direktori arsip, dan backup database ke dalam kerangka Gentelella.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="SuperAdminMasterArchitectureTest|SuperAdminDashboardAndAuditLogTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Filter 15 channel audit trail dan severity badge berfungsi normal.
  - [ ] 4 aksi diagnostik health check berjalan sukses.
  - [ ] Test observabilitas SuperAdmin 100% PASS.

---

#### 📌 TUGAS-4.4: Redesign Modul Builder Studio Dinamis SuperAdmin
- **Target File**:
  - `resources/views/superadmin/modul-builder/index.blade.php`, `create.blade.php`, `edit.blade.php`
  - `resources/views/superadmin/modul-data/index.blade.php`, `print.blade.php`
- **Tingkat Kesulitan**: 🟡 Sedang (1 Jam)
- **Tujuan**: Menyelaraskan antarmuka pembuat modul dinamis (CRUD builder) dan halaman pratinjau data modul custom ke dalam format Gentelella.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter=ModulDinamisBasefwParityTest
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Builder form dan tabel modul kustom tertata rapi.
  - [ ] CRUD modul dinamis berjalan normal.
  - [ ] Test builder modul 100% PASS.

---

### SIKLUS 5: Modul Pengaturan Terpadu, Quality Gate & Zero-Regression

#### 📌 TUGAS-5.1: Redesign Profil Staf, Ganti Password & Pengaturan Sistem (4 Role)
- **Target File**:
  - `resources/views/admin/profil/show.blade.php`, `admin/pengaturan/index.blade.php`
  - `resources/views/pembimbing/profil/show.blade.php`, `pembimbing/pengaturan/index.blade.php`
  - `resources/views/superadmin/profil/show.blade.php`, `superadmin/pengaturan/index.blade.php`
  - `resources/views/auth/ganti-password.blade.php`
- **Tingkat Kesulitan**: 🟢 Mudah (45 Menit)
- **Tujuan**: Menyeragamkan kartu profil identitas, formulir ganti password terproteksi, serta preferensi tema (Terang / Gelap) dan bahasa di seluruh 4 role.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="StaffProfileAndSettingsTest|LanguageLocalizationTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Seluruh halaman profil dan pengaturan 4 role konsisten.
  - [ ] Ganti kata sandi dan toggle tema bekerja normal.
  - [ ] Test profil & pengaturan 100% PASS.

---

#### 📌 TUGAS-5.2: Quality Gate, Linter Laravel Pint & Full Regression Verification
- **Target Scope**: Seluruh direktori proyek
- **Tingkat Kesulitan**: 🔴 Tinggi (1 Jam)
- **Tujuan**: Memastikan standardisasi gaya kode Laravel Pint, memvalidasi seluruh test suite otomatis (457 test cases) tetap hijau (100% pass), dan mensinkronisasikan Knowledge Graph arsitektur proyek.
- **Langkah Kerja**:
  1. Jalankan linter dan code-styler resmi:
     ```bash
     ./vendor/bin/pint
     ```
  2. Jalankan seluruh test suite otomatis:
     ```bash
     php artisan test
     ```
  3. Perbarui Knowledge Graph arsitektur proyek:
     ```bash
     graphify update .
     ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Pint linter zero violation (`{"tool":"pint","result":"passed"}`).
  - [ ] **Minimal 457 Passed, 0 Failed (100% Green)**.
  - [ ] Knowledge graph tersinkronisasi.

---

## 3. Matriks Referensi Elemen Sakral (Anti-Regresi Cheatsheet)

Junior Developer / Gemini 3.8 Flash **wajib merujuk tabel ini** sebelum mengubah file bersangkutan:

| Target File | Elemen / ID Sakral | Teks Sakral (Strict String Assertion) | Test Penguji |
|---|---|---|---|
| `admin/pendaftaran/index.blade.php` | Filter status dropdown | `Citra Lestari`, `Tinjau Berkas`, `Diajukan`, `Semua Status` | `AdminChildPagesVisualTest` |
| `admin/pendaftaran/show.blade.php` | `id="docPreviewModal"`, `openDocPreview` | `Keputusan Verifikasi`, nomor registrasi | `AdminChildPagesExtendedVisualTest` |
| `admin/kuota/index.blade.php` | `modal-tambah-kuota`, `modal-edit-kuota` | `Manajemen Kuota Magang`, `Okupansi:`, `Alokasikan Kuota Jurusan` | `AdminKuotaRevampTest` |
| `admin/waiting-list/index.blade.php` | Table waiting list | `Antrean Waiting List`, `FIFO` | `AdminSuperAdminPhaseTwoTest` |
| `admin/penempatan/index.blade.php` | `modal-surat-preview`, `openSuratPreview` | `Penempatan`, nama peserta | `AdminChildPagesExtendedVisualTest` |
| `pembimbing/logbook/index.blade.php` | Checkbox bulk approve | `Reviu Logbook`, catatan revisi | `NextLevelEnhancementsTest` |
| `pembimbing/peserta/show.blade.php` | Tab nav ID & chart container | Nama mentee, institusi, capaian bimbingan | `DeltaDashboardEnhancementTest` |
| `peserta/tugas/index.blade.php` | Tab filter tugas | `Tugas & Proyek Magang`, `Semua Tugas`, `Ditugaskan`, `Sedang Dikerjakan` | `PesertaClassroomTugasLogbookTest` |
| `peserta/tugas/show.blade.php` | Form submit progres tugas | `Tugas Anda`, `Panduan Pengerjaan`, `Serahkan / Kirim Progres` | `PesertaClassroomTugasLogbookTest` |
| `superadmin/bulan/index.blade.php` | Modal create/edit bulan | `Bulan Magang`, jadwal buka/tutup | `SuperAdminPortalVisualTest` |

---

## 4. Ringkasan Eksekutif Jadwal Sprint

| Siklus | Nama Siklus Pekerjaan | Target Sub-Modul | Jumlah Tugas | Estimasi Waktu |
|---|---|---|---|---|
| **Siklus 1** | Seleksi & Penempatan Admin | Pendaftaran, Kuota, Penempatan, Laporan, Sertifikat | 5 Tugas (1.1–1.5) | 4,5 Jam |
| **Siklus 2** | Pembimbing Lapangan | Mentee 360°, Classroom Tugas, Bank Template, Rubrik | 3 Tugas (2.1–2.3) | 3,5 Jam |
| **Siklus 3** | Peserta Magang | Profil/Wali, Pendaftaran/Upload, Logbook, Classroom | 4 Tugas (3.1–3.4) | 4,25 Jam |
| **Siklus 4** | SuperAdmin & Observabilitas | Master Data, RBAC Staf, Audit 15-Ch, Modul Builder | 4 Tugas (4.1–4.4) | 5,5 Jam |
| **Siklus 5** | Quality Gate & Finishing | Profil/Pengaturan 4 Role, Pint, Full Suite, Graphify | 2 Tugas (5.1–5.2) | 1,75 Jam |
| **TOTAL** | **Eksekusi Fase 3 Menyeluruh** | **Seluruh Halaman Aplikasi MagKom** | **18 Tugas Terstruktur** | **± 19,5 Jam** |

---

## 5. Kriteria Selesai Sprint (Definition of Done)

Sebelum branch `test-2` dapat digabungkan (*merged*) ke `main`/`master`, seluruh checklist berikut wajib terpenuhi 100%:
- [x] Seluruh halaman turunan di 4 role telah mengadopsi standar komponen Gentelella (`<x-gentelella.x-panel>`, `<x-gentelella.table>`, `<x-gentelella.filter-bar>`, `<x-gentelella.action-btn>`, `<x-gentelella.modal>`).
- [x] Warna utama konsisten dengan brand MagKom Sleman (Deep Navy `#0641CC` & Blue `#0751FF`) dan logo resmi Sleman terpajang dengan proporsional.
- [x] Tidak ada penambahan dependensi jQuery atau Bootstrap 3 legacy.
- [x] Seluruh modal dialog dan interaktivitas sidebar berjalan responsif di desktop & mobile tanpa error console JavaScript.
- [x] Seluruh child page mendukung tampilan Dark Mode (`[data-theme="dark"]`).
- [x] Tidak ada perubahan pada logika backend, controller, model, migrasi, dan routing.
- [x] Kode terformat rapi sesuai standar Laravel Pint (`./vendor/bin/pint`).
- [x] Seluruh pengujian otomatis via `php artisan test` **100% Lulus (Minimal 457 Passed, 0 Failed, 0 Regressions)**.
- [x] Knowledge graph proyek tersinkronisasi via `graphify update .`.
