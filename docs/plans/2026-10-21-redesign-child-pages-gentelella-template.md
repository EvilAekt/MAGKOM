# Plan Lanjutan: Redesign Seluruh Child Pages & Sub-Modul ke Template Gentelella
## (Master Task Handbook: Senior Software Engineer → Junior Developer / Gemini 3.8 Flash)

- **Tanggal Rilis**: 21 Oktober 2026
- **Branch Target**: `test-2`
- **Status Dokumen**: 📝 Official Master Plan / Siap Eksekusi Bertahap
- **Penulis**: Senior Software Engineer & System Architect
- **Target Pelaksana**: Junior Developer dan/atau AI Model Hemat Biaya (misal: **Gemini 3.8 Flash**).
- **Karakteristik Dokumen**: Sangat eksplisit, deterministik, *step-by-step*, dilengkapi snippet kode nyata, daftar elemen sakral (anti-regresi), dan perintah uji mandiri per tugas.

---

## 0. Golden Rules & Guardrails (WAJIB DIBACA SEBELUM MENULIS KODE)

Sebagai Junior Developer atau Model AI pelaksana, kamu **wajib mematuhi 5 aturan sakral** berikut tanpa kompromi:

### 🚨 Aturan 1: Dilarang Keras Mengubah Business Logic Backend
- **HANYA BOLEH MENGUBAH TAMPILAN** (`resources/views/**/*.blade.php`, `public/css/gentelella-theme.css`, `public/js/gentelella-theme.js`).
- **DILARANG MENGUBAH**:
  - File Controller di `app/Http/Controllers/**`.
  - Nama variabel yang dikirim ke view (seperti `$pendaftaranList`, `$bulanList`, `$kuotaList`, dll.).
  - File Route di `routes/web.php` atau `routes/api.php`.
  - Model Eloquent, Migration, Relasi Database, dan Enums.
  - FormRequest Validation Rules.

### 🚨 Aturan 2: Dilarang Menghapus Elemen Sakral (DOM ID, Form Action, & Teks Assertion)
Unit test PHPUnit di proyek ini memeriksa teks dan atribut tertentu secara ketat (*strict string assertion*). Jika kamu menghapus atau mengganti teks sakral tersebut, test akan langsung merah.
- **DILARANG MENGGANTI** teks sakral seperti:
  - `Tinjau Berkas`, `Diajukan`, `Semua Status`, `Manajemen Kuota Magang`, `Okupansi:`, `Alokasikan Kuota Jurusan`
  - `Antrean Waiting List`, `FIFO`, `Penempatan`, `Keputusan Verifikasi`
  - `Tugas & Proyek Magang`, `Semua Tugas`, `Ditugaskan`, `Sedang Dikerjakan`, `Tugas Anda`, `Panduan Pengerjaan`, `Serahkan / Kirim Progres`
- **DILARANG MENGHAPUS** ID modal atau fungsi JavaScript penting:
  - `id="docPreviewModal"`, `openDocPreview(...)`, `closeDocPreview(...)`
  - `id="modal-surat-preview"`, `openSuratPreview(...)`
  - `id="modal-tambah-kuota"`, `id="modal-edit-kuota"`
- **DILARANG MENGHAPUS** atribut form:
  - `@csrf`
  - `@method('PUT')`, `@method('PATCH')`, `@method('DELETE')`
  - Atribut `name="..."` pada setiap input/select/textarea.

### 🚨 Aturan 3: Zero jQuery & Zero Bootstrap 3 Legacy
- Template Gentelella asli di internet memakai jQuery dan Bootstrap 3 usang. **JANGAN PERNAH MENG-IMPORT JQUERY ATAU BOOTSTRAP 3!**
- Seluruh interaktivitas (modal popup, tab switcher, collapse panel, drawer filter) **wajib menggunakan Vanilla JavaScript murni** atau komponen Blade yang telah disediakan.

### 🚨 Aturan 4: Konsistensi Token Desain MagKom Sleman
- Jangan menggunakan warna heksadesimal baru sembarangan. Gunakan token CSS resmi MagKom:
  - Primary Brand: `var(--blue-700)` (`#0641CC`)
  - Accent / Hover: `var(--blue-600)` (`#0751FF`)
  - Canvas Background: `var(--neutral-150)` (`#F5F7FB`)
  - Card/Panel Background: `var(--neutral-0)` (`#FFFFFF`) / Dark: `#182234`
  - Border: `var(--neutral-200)` (`#EEF0F5`) / Dark: `#26354D`
  - Heading Text: `var(--neutral-800)` (`#1D2530`) / Dark: `#F8FAFC`

### 🚨 Aturan 5: Wajib Menjalankan Test Sebelum dan Sesudah Modifikasi
- Sebelum mengubah suatu file: Jalankan test terkait untuk memastikan status awal hijau (PASS).
- Sesudah mengubah file: Jalankan test kembali. Jika ada test FAIL, **perbaiki kesalahanmu saat itu juga sebelum berpindah ke tugas berikutnya!**

---

## 1. Katalog Komponen Blade Reusable Gentelella (Siap Pakai)

Seluruh komponen reusable telah tersedia di `resources/views/components/gentelella/`. Gunakan komponen ini untuk menjaga keseragaman arsitektur:

### 1.1 Header Halaman: `<x-gentelella.page-title>`
Digunakan di baris paling atas `@section('content')` setiap child page.
```blade
<x-gentelella.page-title 
    title="Verifikasi Berkas Pendaftaran" 
    description="Validasi dokumen pendaftar calon peserta magang dan monitoring SLA.">
    <x-slot:actions>
        <a href="{{ route('admin.pendaftaran.export') }}" class="btn-action-gentelella btn-action-primary">
            <svg>...</svg> Ekspor Data
        </a>
    </x-slot:actions>
</x-gentelella.page-title>
```

### 1.2 Panel Konten Kartu: `<x-gentelella.x-panel>`
Wadah utama setiap tabel, formulir, atau modul kartu.
```blade
<x-gentelella.x-panel title="Daftar Permohonan Magang" badge="14 Menunggu">
    <!-- Isi formulir atau tabel di sini -->
</x-gentelella.x-panel>
```

### 1.3 Kartu Metrik Angka: `<x-gentelella.tile-stat>`
Untuk deretan KPI ringkas di atas halaman.
```blade
<div class="row tile_count">
    <x-gentelella.tile-stat 
        title="Total Diajukan" 
        value="48" 
        icon="users" 
        trendText="+12% bln lalu" 
        trendType="success" 
        subText="Periode aktif" />
</div>
```

### 1.4 Filter Toolbar Responsif: `<x-gentelella.filter-bar>`
Untuk baris formulir filter dan pencarian.
```blade
<x-gentelella.filter-bar action="{{ route('admin.pendaftaran.index') }}" method="GET">
    <div class="gentelella-filter-group">
        <label for="filter-status">Status</label>
        <select name="status" id="filter-status" onchange="this.form.submit()">
            <option value="">Semua Status</option>
            <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
        </select>
    </div>
    <div class="gentelella-search-box">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / NIM..." />
    </div>
    <button type="submit" class="btn-action-gentelella btn-action-primary">Cari</button>
</x-gentelella.filter-bar>
```

### 1.5 Tabel Data Gentelella: `<x-gentelella.table>`
Untuk membungkus data list tabel bergaya `jambo_table`.
```blade
<x-gentelella.table :headers="['No', 'Nama Peserta', 'Institusi', 'Status', 'Aksi']">
    @forelse($items as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td class="font-bold">{{ $item->nama }}</td>
            <td>{{ $item->institusi }}</td>
            <td><x-badge :status="$item->status" /></td>
            <td>
                <x-gentelella.action-btn variant="primary" href="{{ route('...', $item->id) }}" title="Tinjau Berkas">
                    Tinjau Berkas
                </x-gentelella.action-btn>
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="text-center">Data tidak ditemukan.</td></tr>
    @endforelse

    <x-slot:pagination>
        {{ $items->links() }}
    </x-slot:pagination>
</x-gentelella.table>
```

### 1.6 Tombol Aksi Terstandarisasi: `<x-gentelella.action-btn>`
Tombol kecil di dalam baris tabel (`primary`, `success`, `warning`, `danger`).
```blade
<x-gentelella.action-btn variant="primary" href="{{ route('admin.pendaftaran.show', $item->id) }}">
    Tinjau Berkas
</x-gentelella.action-btn>
```

---

## 2. Rincian Paket Tugas (Sprint Execution Plan untuk Junior / AI)

---

### PAKET A: Fondasi & Standardisasi Komponen Reusable Blade

#### 📌 TUGAS-A1: Penyempurnaan Komponen `<x-gentelella.modal>` dan Utility Filter (🟢 Mudah, 45 Menit)
- **Tujuan**: Menyediakan modal dialog standar bergaya Gentelella untuk menggantikan modal usang yang tersebar di child pages.
- **File Dibuat / Dimodifikasi**:
  - `resources/views/components/gentelella/modal.blade.php` (Baru)
  - `public/css/gentelella-theme.css`
- **Instruksi Eksekusi**:
  1. Buat file `resources/views/components/gentelella/modal.blade.php` dengan props: `id`, `title`, `size` (default 'md', opsi 'lg', 'xl').
  2. Struktur modal harus mendukung tombol close (x), backdrop overlay, dan slot untuk footer aksi.
  3. Pastikan modal dapat dibuka dan ditutup menggunakan fungsi Javascript native sederhana: `document.getElementById('id').classList.add('show')` atau `style.display = 'block'`.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter=GentelellaDashboardRevampTest
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [x] Komponen `modal.blade.php` tercipta tanpa syntax error.
  - [x] Mendukung Dark Mode (`[data-theme="dark"] .gentelella-modal-content`).
  - [x] Test `GentelellaDashboardRevampTest` 100% PASS.

---

### PAKET B: Sub-Modul Admin Operasional (Command Center Administrasi)

#### 📌 TUGAS-B1: Redesign Halaman Index Verifikasi Berkas Pendaftaran (🟡 Sedang, 1 Jam)
- **Tujuan**: Menyelaraskan halaman daftar berkas pendaftaran dengan kerangka Gentelella (`x_panel`, `filter-bar`, `table`).
- **Target File**: `resources/views/admin/pendaftaran/index.blade.php`
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Teks: `Citra Lestari` (dari data test), `Tinjau Berkas`, `Diajukan`, `Semua Status`
  - Variabel data: `$pendaftarans`, `$bulanList`, `$jurusanList`, `$statusList`
  - Parameter form: `bulan_magang_id`, `jurusan_id`, `status`, `search`
- **Resep Implementasi**:
  1. Bungkus konten teratas dengan `<x-gentelella.page-title title="Seleksi & Verifikasi Berkas" description="Pusat validasi dokumen pendaftar dan pengawasan batas waktu (SLA) permohonan magang." />`.
  2. Tambahkan row `tile_count` untuk metrik cepat: Total Pendaftar, Menunggu Verifikasi, Perlu Perbaikan, Diterima.
  3. Bungkus form pencarian dengan `<x-gentelella.filter-bar>`:
     - Pertahankan select `name="status"` dengan opsi `Semua Status` dan `Diajukan`.
     - Pertahankan input teks pencarian `name="search"`.
  4. Bungkus tabel list dengan `<x-gentelella.x-panel title="Daftar Permohonan Magang">` dan `<x-gentelella.table>`.
  5. Pastikan tombol aksi ke detail memiliki teks atau judul `Tinjau Berkas`.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter=AdminChildPagesVisualTest
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [x] Halaman mengadopsi `<x-gentelella.x-panel>` dan `<x-gentelella.table>`.
  - [x] Seluruh filter bulan, jurusan, status, dan pencarian tetap berfungsi normal.
  - [x] `php artisan test --filter=AdminChildPagesVisualTest` PASS.

---

#### 📌 TUGAS-B2: Redesign Halaman Detail Verifikasi & In-App Document Previewer (🟡 Sedang, 1,5 Jam)
- **Tujuan**: Menata tampilan 2 kolom untuk verifikasi profil pendaftar, pratinjau dokumen, dan form keputusan verifikasi.
- **Target File**: `resources/views/admin/pendaftaran/show.blade.php`
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Modal ID: `id="docPreviewModal"`
  - Fungsi JS: `openDocPreview(...)`, `closeDocPreview(...)`
  - Teks: `Keputusan Verifikasi`, nama pendaftar (`$pendaftaran->user->nama`), nomor registrasi (`$pendaftaran->nomor_pendaftaran`)
  - Form Action & Method: Form verifikasi dengan method POST/PUT ke route keputusan verifikasi.
  - Radio/Button: Pilihan status (Terima, Tolak, Perbaikan, Waiting List) dan input catatan revisi.
- **Resep Implementasi**:
  1. Pasang `<x-gentelella.page-title>` berisi nama peserta dan nomor pendaftaran, lengkap dengan tombol kembali.
  2. Bagi layout menjadi 2 kolom:
     - **Kolom Kiri (col-md-7)**:
       - Panel 1: `<x-gentelella.x-panel title="Biodata & Informasi Pendidikan">` (NIM, Kampus, Jurusan, No HP, Alamat).
       - Panel 2: `<x-gentelella.x-panel title="Dokumen Persyaratan">` (Daftar file PDF/Gambar yang diunggah dengan tombol preview).
     - **Kolom Kanan (col-md-5)**:
       - Panel 3: `<x-gentelella.x-panel title="Keputusan Verifikasi">` (Form radio status, field catatan verifikator, tombol Simpan Keputusan).
  3. Tempatkan modal pratinjau dokumen dengan `id="docPreviewModal"` di bagian bawah view.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter=AdminChildPagesExtendedVisualTest
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] ID `docPreviewModal` dan fungsi `openDocPreview` tetap ada dan aktif.
  - [ ] Form keputusan verifikasi mengirimkan payload yang sesuai tanpa error CSRF.
  - [ ] `php artisan test --filter=AdminChildPagesExtendedVisualTest` PASS.

---

#### 📌 TUGAS-B3: Redesign Halaman Manajemen Kuota Jurusan (🟡 Sedang, 1,5 Jam)
- **Tujuan**: Menyelaraskan tabel alokasi kuota per jurusan dan modal tambah/ubah kuota dengan tema Gentelella.
- **Target File**: `resources/views/admin/kuota/index.blade.php`
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Modal IDs: `id="modal-tambah-kuota"`, `id="modal-edit-kuota"`
  - Teks: `Manajemen Kuota Magang`, `Sistem Informasi`, `Okupansi:`, `Alokasikan Kuota Jurusan`
  - Form inputs: `jumlah`, `jurusan_id`, `bulan_magang_id`
- **Resep Implementasi**:
  1. Pasang `<x-gentelella.page-title title="Manajemen Kuota Magang" description="Pengaturan kapasitas dan alokasi penerimaan peserta magang per jurusan." />`.
  2. Pasang kartu ringkasan kuota atas: Total Kapasitas, Kuota Terisi, Sisa Kuota, dan Rasio Okupansi (`Okupansi: ...%`).
  3. Bungkus filter periode bulan dengan `<x-gentelella.filter-bar>`.
  4. Bungkus tabel alokasi dengan `<x-gentelella.x-panel title="Alokasi Kuota Jurusan">`.
  5. Sediakan tombol dengan teks `Alokasikan Kuota Jurusan` untuk membuka modal `modal-tambah-kuota`.
  6. Pastikan modal edit kuota memiliki id `modal-edit-kuota`.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="AdminKuotaRevampTest|AdminChildPagesVisualTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Modal `modal-tambah-kuota` dan `modal-edit-kuota` ada di DOM dan berfungsi.
  - [ ] Teks `Okupansi:` dan `Alokasikan Kuota Jurusan` tetap ada.
  - [ ] Test kuota revamping 100% PASS.

---

#### 📌 TUGAS-B4: Redesign Halaman Waiting List FIFO & Penempatan Peserta (🟡 Sedang, 1,5 Jam)
- **Target File**:
  - `resources/views/admin/waiting-list/index.blade.php`
  - `resources/views/admin/penempatan/index.blade.php`
  - `resources/views/admin/penempatan/show.blade.php`
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Teks: `Antrean Waiting List`, `FIFO`, `Penempatan`, `modal-surat-preview`, `openSuratPreview`
- **Resep Implementasi**:
  1. Pada `waiting-list/index.blade.php`:
     - Tampilkan panel penjelasan metode alokasi FIFO dengan badge antrean.
     - Bungkus tabel waiting list dalam `<x-gentelella.x-panel title="Antrean Waiting List (FIFO)">`.
     - Sediakan tombol eksekusi alokasi cepat jika kuota tersedia.
  2. Pada `penempatan/index.blade.php`:
     - Bungkus tabel peserta yang siap ditempatkan dan sudah ditempatkan dalam panel terpisah.
     - Pertahankan modal `modal-surat-preview` dan fungsi JS `openSuratPreview`.
  3. Pada `penempatan/show.blade.php`:
     - Tampilkan kartu penugasan pembimbing lapangan, penetapan bidang, dan tanggal periode magang.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="AdminPenempatanDuaTahapTest|AdminPenempatanJalurKhususTest|AdminChildPagesExtendedVisualTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [x] Teks `Antrean Waiting List`, `FIFO`, dan modal preview surat tetap ada.
  - [x] Alur penempatan dua tahap dan jalur khusus tetap berfungsi.
  - [x] Semua test penempatan dan waiting list 100% PASS.

---

#### 📌 TUGAS-B5: Redesign Halaman Laporan Hub, Statistik & Sertifikat Admin (🟢 Mudah, 1 Jam)
- **Target File**:
  - `resources/views/admin/laporan/index.blade.php`
  - `resources/views/admin/laporan/tahunan.blade.php`
  - `resources/views/admin/sertifikat/index.blade.php`
  - `resources/views/admin/survei/index.blade.php`
- **Instruksi Eksekusi**:
  1. Pada halaman Laporan: Tata kartu KPI tahunan ke dalam grid Gentelella (`col-md-3`), bungkus form filter rentang tanggal dan tombol unduh laporan ke dalam `<x-gentelella.x-panel>`.
  2. Pada halaman Sertifikat: Tata daftar peserta lulus magang dengan tabel Gentelella, lengkap dengan status penerbitan nomor sertifikat resmi.
  3. Pada halaman Survei: Tampilkan visualisasi skor kepuasan (IKM/NPS) dalam tile count dan feedback card.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="AdminLaporanDanArsipTest|SurveiKepuasanAdminVisualTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Semua halaman laporan, sertifikat, dan survei mengadopsi Gentelella.
  - [ ] Fitur ekspor PDF/CSV tidak rusak.
  - [ ] Test laporan & survei 100% PASS.

---

### PAKET C: Sub-Modul Pembimbing Lapangan (Mentoring & Evaluasi)

#### 📌 TUGAS-C1: Redesign Direktori Mentee 360° & Detail Bimbingan (🟡 Sedang, 1,5 Jam)
- **Target File**:
  - `resources/views/pembimbing/peserta/index.blade.php`
  - `resources/views/pembimbing/peserta/show.blade.php`
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Data mentee: Nama, Kampus/Sekolah, Jurusan, Periode Magang
  - Tab navigasi detail mentee: Tab Ringkasan, Tab Tugas, Tab Logbook, Tab Nilai
  - Chart canvas atau elemen monitoring progres mentee
- **Resep Implementasi**:
  1. Pada `pembimbing/peserta/index.blade.php`:
     - Tampilkan tile statistik mentee aktif dan kapasitas bimbingan.
     - Bungkus daftar mentee dalam tabel Gentelella dengan avatar foto, progress bar masa magang, dan tombol Detail Mentee.
  2. Pada `pembimbing/peserta/show.blade.php`:
     - Header profil mentee lengkap dengan kontak, asal kampus, dan pembimbing pendamping.
     - Panel tab terintegrasi untuk riwayat tugas yang dikerjakan, logbook mingguan, dan rekap capaian kompetensi.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="DeltaDashboardEnhancementTest|PembimbingPortalFeatureTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [x] Halaman profil detail mentee 360° render sempurna.
  - [x] Tab switching berjalan tanpa jQuery.
  - [x] Test pembimbing peserta 100% PASS.

---

#### 📌 TUGAS-C2: Redesign Antrean Reviu Logbook Harian & Bulk Approve (🟡 Sedang, 1,5 Jam)
- **Target File**: `resources/views/pembimbing/logbook/index.blade.php`
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Form bulk approve: Checkbox pilih semua, tombol "Setujui Terpilih"
  - Modal feedback/catatan revisi untuk logbook yang membutuhkan perbaikan
  - Status logbook: Terkirim, Disetujui, Perlu Revisi
- **Resep Implementasi**:
  1. Pasang `<x-gentelella.page-title title="Reviu Logbook Harian" description="Pusat validasi laporan aktivitas harian peserta magang dan pemberian umpan balik bimbingan." />`.
  2. Tempatkan panel antrean logbook pending dengan badge tanggal pengisian.
  3. Bungkus tabel logbook dengan `<x-gentelella.table>` yang mendukung checkbox baris untuk aksi persetujuan massal (*bulk approval*).
  4. Sediakan modal catatan revisi yang responsif.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="NextLevelEnhancementsTest|PembimbingFullJobDeskTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [x] Fitur bulk approve logbook tetap berfungsi normal.
  - [x] Modal input catatan revisi terbuka dengan benar saat tombol revisi ditekan.
  - [x] Test logbook pembimbing 100% PASS.

---

#### 📌 TUGAS-C3: Redesign Manajemen Penugasan Google Classroom & Bank Template (🟡 Sedang, 1,5 Jam)
- **Target File**:
  - `resources/views/pembimbing/tugas/index.blade.php`
  - `resources/views/pembimbing/tugas/create.blade.php`
  - `resources/views/pembimbing/tugas/edit.blade.php`
  - `resources/views/pembimbing/tugas/show.blade.php`
  - `resources/views/pembimbing/tugas/template/index.blade.php`
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Form fields tugas: `judul`, `deskripsi`, `deadline`, `kategori`, `kompleksitas`, `lampiran`
  - Status tugas: Belum Dikerjakan, Sedang Dikerjakan, Selesai, Terlambat
  - Tombol evaluasi submisi tugas peserta dan form nilai
- **Resep Implementasi**:
  1. Tata tampilan daftar tugas menyerupai grid kartu tugas modern yang rapi.
  2. Formulir pembuatan tugas (`create.blade.php` & `edit.blade.php`) dibungkus dalam `<x-gentelella.x-panel>` dengan input datepicker dan select dropdown bertema MagKom.
  3. Halaman detail tugas (`show.blade.php`) memuat rekap peserta yang sudah mengumpulkan beserta attachment link/file mereka.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="TugasLogbookTest|RichTaskAssignmentTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Pembuatan dan pengeditan tugas berjalan normal.
  - [ ] Akses bank template tugas tetap dapat dipanggil.
  - [ ] Test tugas pembimbing 100% PASS.

---

#### 📌 TUGAS-C4: Redesign Agenda Bimbingan, Evaluasi Rubrik & Laporan Kinerja (🟡 Sedang, 1,5 Jam)
- **Target File**:
  - `resources/views/pembimbing/agenda/index.blade.php`
  - `resources/views/pembimbing/penilaian/index.blade.php`
  - `resources/views/pembimbing/penilaian/show.blade.php`
  - `resources/views/pembimbing/laporan-kinerja/index.blade.php`
  - `resources/views/pembimbing/laporan-kinerja/cetak.blade.php`
- **Instruksi Eksekusi**:
  1. Pada halaman Agenda: Tata jadwal bimbingan dan notulensi pertemuan ke dalam timeline card Gentelella.
  2. Pada halaman Penilaian: Tata form matriks rubrik kompetensi (Integritas, Keahlian Teknis, Komunikasi, dll.) dengan kalkulasi angka otomatis.
  3. Pada halaman Laporan Kinerja: Tampilkan resume capaian bimbingan per semester/tahun.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="ScoringAndCompletionTest|AdvancedThreePillarsDashboardTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [x] Pengisian skor penilaian rubrik tersimpan dengan benar.
  - [x] Notulensi agenda bimbingan dapat diupdate.
  - [x] Test scoring dan agenda 100% PASS.

---

### PAKET D: Sub-Modul Peserta Magang (Student Workspace)

#### 📌 TUGAS-D1: Redesign Profil Peserta, Data Rekening & Persetujuan Wali (🟢 Mudah, 1 Jam)
- **Target File**:
  - `resources/views/peserta/profil/show.blade.php`
  - `resources/views/peserta/pengaturan/index.blade.php`
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Form fields: `nama`, `nim_nisn`, `institusi`, `jurusan_id`, `jenjang`, `no_hp`, `alamat`, `foto`
  - Data rekening & kontak darurat / persetujuan wali (UU PDP)
- **Resep Implementasi**:
  1. Tampilkan kartu profil atas dengan avatar besar, badge status magang, dan ringkasan institusi.
  2. Bagi formulir data diri ke dalam panel ber-tab rapi: Data Pokok, Data Akademik, Data Rekening/Bank, dan Persetujuan Privasi.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="PesertaUuPdpPersetujuanWaliTest|PesertaFullPortalFeatureTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Update data diri dan unggah foto profil berjalan mulus.
  - [ ] Persetujuan privasi UU PDP tidak terhapus.
  - [ ] Test profil peserta 100% PASS.

---

#### 📌 TUGAS-D2: Redesign Alur Pendaftaran Magang & Upload Dokumen Berkas (🟡 Sedang, 1,5 Jam)
- **Target File**:
  - `resources/views/peserta/pendaftaran/index.blade.php`
  - `resources/views/peserta/pendaftaran/detail.blade.php`
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Alur status: Pendaftaran Baru, Unggah Berkas, Menunggu Verifikasi, Diterima/Ditolak
  - Form upload dokumen: CV, Surat Pengantar Kampus, Proposal, KTP/KTM, Transkrip Nilai
- **Resep Implementasi**:
  1. Tampilkan progress tracker langkah pendaftaran (Step Wizard bergaya Gentelella).
  2. Bungkus form unggah berkas dalam `<x-gentelella.x-panel>` dengan validasi ukuran file, tipe file (PDF), dan status approved/revisi.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="PendaftaranPesertaTest|PesertaPendaftaranFlowTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Seluruh input file upload tetap berfungsi dan terkirim ke backend.
  - [ ] Tampilan status pendaftaran jelas dan informatif.
  - [ ] Test alur pendaftaran 100% PASS.

---

#### 📌 TUGAS-D3: Redesign Timeline Logbook Harian Peserta (🟡 Sedang, 1 Jam)
- **Target File**: `resources/views/peserta/logbook/index.blade.php`
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Form tambah/edit entri logbook: `tanggal`, `kegiatan`, `kendala`, `tautan_lampiran`, `file_lampiran`
  - Timeline feed entri logbook dan status verifikasi pembimbing
- **Resep Implementasi**:
  1. Pasang `<x-gentelella.page-title title="Logbook Harian Magang" description="Catat aktivitas harian, kendala yang dihadapi, dan pantau umpan balik dari pembimbing dinas." />`.
  2. Sediakan panel formulir pengisian logbook di bagian atas yang simpel dan mudah diisi.
  3. Tampilkan riwayat logbook dalam format timeline vertikal yang rapi.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="PesertaPortalWorkflowTest|ReleaseTwoFeaturesTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Simpan dan update logbook harian berhasil.
  - [ ] Lampiran file dan tautan https tetap tersimpan.
  - [ ] Test logbook peserta 100% PASS.

---

#### 📌 TUGAS-D4: Redesign Workspace Tugas Classroom, Nilai & Survei Peserta (🟡 Sedang, 1,5 Jam)
- **Target File**:
  - `resources/views/peserta/tugas/index.blade.php`
  - `resources/views/peserta/tugas/show.blade.php`
  - `resources/views/peserta/nilai/show.blade.php`
  - `resources/views/peserta/survei/index.blade.php`
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Teks: `Tugas & Proyek Magang`, `Semua Tugas`, `Ditugaskan`, `Sedang Dikerjakan`, `Tugas Anda`, `Panduan Pengerjaan`, `Serahkan / Kirim Progres`
  - Form submisi tugas: input link Google Drive/GitHub, input catatan progres, upload attachment
  - Tampilan transkrip nilai dan tombol cetak sertifikat
- **Resep Implementasi**:
  1. Pada `tugas/index.blade.php`:
     - Tampilkan tab filter: `Semua Tugas`, `Ditugaskan`, `Sedang Dikerjakan`, `Selesai`.
     - Tampilkan kartu tugas dengan deadline yang jelas dan status badge.
  2. Pada `tugas/show.blade.php`:
     - Terapkan layout 2 kolom: Kiri (Panduan Pengerjaan dari pembimbing), Kanan (Panel `Tugas Anda` untuk submit tugas/link progres).
  3. Pada `nilai/show.blade.php` & `survei/index.blade.php`:
     - Transkrip nilai mata uji rubrik disajikan dalam tabel Gentelella resmi.
     - Form kuesioner survei kepuasan magang dengan bintang rating yang elegan.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="PesertaClassroomTugasLogbookTest|ScoringAndCompletionTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Teks sakral `Tugas Anda` dan `Panduan Pengerjaan` tetap ada di DOM.
  - [ ] Form submit tugas dapat mengirimkan link dan lampiran.
  - [ ] Test tugas classroom dan penilaian 100% PASS.

---

### PAKET E: Sub-Modul SuperAdmin & Tata Kelola Master Data

#### 📌 TUGAS-E1: Redesign Master Bulan Magang & Slot Khusus Kemitraan (🟡 Sedang, 1 Jam)
- **Target File**:
  - `resources/views/superadmin/bulan/index.blade.php`
  - `resources/views/superadmin/slot-khusus/index.blade.php`
- **Elemen SAKRAL yang DILARANG HILANG**:
  - Modal tambah bulan & edit bulan
  - Parameter tanggal buka, tutup, bulan mulai, status draft/dibuka
  - Tabel slot khusus instansi mitra
- **Resep Implementasi**:
  1. Bungkus tabel master bulan magang ke dalam `<x-gentelella.x-panel title="Master Periode Bulan Magang">`.
  2. Selaraskan modal buat bulan baru dan ubah bulan dengan komponen modal Gentelella.
  3. Tampilkan slot khusus kemitraan (MoU kampus/SMK) dalam panel terpisah dengan tombol kelola alokasi.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="SuperAdminPortalVisualTest|AdminKuotaRevampTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [x] Pembuatan dan update bulan magang tetap berjalan normal.
  - [x] Modal popups beroperasi dengan mulus.
  - [x] Test master bulan 100% PASS.

---

#### 📌 TUGAS-E2: Redesign Master Bidang Dinas & Pemetaan Jurusan (🟡 Sedang, 1 Jam)
- **Target File**:
  - `resources/views/superadmin/bidang/index.blade.php`
  - `resources/views/superadmin/jurusan/index.blade.php`
- **Instruksi Eksekusi**:
  1. Tata tabel Master Bidang Kominfo (Aptika, IKP, E-Gov, Sekretariat) dengan badge status aktif.
  2. Tata tabel Master Jurusan yang Dibutuhkan lengkap dengan pemetaan kategori (Mahasiswa/Siswa SMK) dan relasi kuota bidang.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="BidangJurusanMappingTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Tambah/edit bidang dan jurusan berfungsi tanpa kendala.
  - [ ] Relasi pemetaan jurusan ke bidang tidak berubah.
  - [ ] Test bidang jurusan 100% PASS.

---

#### 📌 TUGAS-E3: Redesign Staf, Matriks Role RBAC & Rubrik Penilaian (🟡 Sedang, 1,5 Jam)
- **Target File**:
  - `resources/views/superadmin/staf/index.blade.php`
  - `resources/views/superadmin/akun/index.blade.php`
  - `resources/views/superadmin/role/index.blade.php`
  - `resources/views/superadmin/rubrik/index.blade.php`
- **Instruksi Eksekusi**:
  1. Tata manajemen pengguna & staf dalam tabel Gentelella dengan filter role, status aktif, dan tombol reset password.
  2. Matriks otorisasi RBAC 4 role (SuperAdmin, Admin, Pembimbing, Peserta) disajikan dalam grid hak akses yang jelas.
  3. Master rubrik penilaian disajikan dengan bobot persentase dan skala capaian kompetensi.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="SuperAdminFeatureToggleTest|StaffProfileAndSettingsTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Matriks role dan tabel staf tertata rapi.
  - [ ] Update rubrik dan manajemen akun berjalan normal.
  - [ ] Test staf dan rubrik 100% PASS.

---

#### 📌 TUGAS-E4: Redesign Observabilitas: Audit Trail, Health Telemetry, Backup & Modul Builder (🔴 Tinggi, 2 Jam)
- **Target File**:
  - `resources/views/superadmin/audit-log/index.blade.php`
  - `resources/views/superadmin/health/index.blade.php`
  - `resources/views/superadmin/arsip/index.blade.php`
  - `resources/views/superadmin/backup/index.blade.php`
  - `resources/views/superadmin/monitoring/index.blade.php`
  - `resources/views/superadmin/modul-builder/index.blade.php`, `create.blade.php`, `edit.blade.php`
  - `resources/views/superadmin/modul-data/index.blade.php`, `print.blade.php`
- **Instruksi Eksekusi**:
  1. Halaman Audit Log: Filter 15 channel audit, badge severity (Info, Warning, Danger), dan tabel riwayat transaksi data.
  2. Halaman Health Telemetry: Panel status performa server (Database connection, Redis/Cache, Disk Storage, App Version) dengan status lampu hijau/merah.
  3. Modul Builder: Tampilan visual formulir pembuat modul dinamis dengan live preview tabel.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="SuperAdminMasterArchitectureTest|SuperAdminDashboardAndAuditLogTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Filter audit trail 15 channel tetap berfungsi normal.
  - [ ] Panel health check dapat menjalankan pengecekan on-demand.
  - [ ] Test arsitektur SuperAdmin 100% PASS.

---

### PAKET F: Modul Pengaturan & Profil Terpadu (4 Role)

#### 📌 TUGAS-F1: Redesign Halaman Profil, Ganti Password & Pengaturan Sistem (🟢 Mudah, 1 Jam)
- **Target File**:
  - `resources/views/admin/profil/show.blade.php`, `resources/views/admin/pengaturan/index.blade.php`
  - `resources/views/pembimbing/profil/show.blade.php`, `resources/views/pembimbing/pengaturan/index.blade.php`
  - `resources/views/superadmin/profil/show.blade.php`, `resources/views/superadmin/pengaturan/index.blade.php`
  - `resources/views/auth/ganti-password.blade.php`
- **Instruksi Eksekusi**:
  1. Selaraskan kartu profil identitas staf dan form ganti password ke dalam format panel Gentelella.
  2. Halaman pengaturan sistem memuat toggle preferensi notifikasi, pilihan tema (Terang / Gelap), dan preferensi bahasa.
- **Perintah Verifikasi**:
  ```bash
  php artisan test --filter="StaffProfileAndSettingsTest|LanguageLocalizationTest"
  ```
- **Checklist Kriteria Penerimaan (DoD)**:
  - [ ] Form update profil dan ubah kata sandi bekerja normal.
  - [ ] Pengaturan tema dan bahasa tersimpan ke localStorage/session.
  - [ ] Test profil dan pengaturan 100% PASS.

---

### PAKET G: Quality Gate, Linter Pint & Verifikasi Regresi 100% Green

#### 📌 TUGAS-G1: Pembersihan Format Kode, Regression Test & Sinkronisasi Graph (🔴 Tinggi, 1,5 Jam)
- **Target File**: Seluruh workspace
- **Instruksi Eksekusi**:
  1. Jalankan linter dan code-styler resmi Laravel Pint:
     ```bash
     ./vendor/bin/pint
     ```
  2. Jalankan seluruh test suite otomatis aplikasi (wajib 100% PASS tanpa 1 pun failure):
     ```bash
     php artisan test
     ```
  3. Periksa status git diff untuk memastikan tidak ada perubahan file backend yang tidak disengaja:
     ```bash
     git status
     ```
  4. Perbarui Knowledge Graph arsitektur proyek:
     ```bash
     graphify update .
     ```
- **Kriteria Verifikasi**:
  - Semua test passed (Target: minimal **457 Passed, 0 Failed**).
  - Pint linter zero warning/error.
  - Knowledge graph terbarui.

---

## 3. Matriks ID Sakral & Teks Assertion Test (Panduan Anti-Regresi)

Junior Developer / Model AI **wajib merujuk tabel ini** sebelum mengubah file bersangkutan. Pastikan elemen berikut **TIDAK TERHAPUS**:

| Target View File | ID / Class Sakral | Teks Sakral (Strict String Assertion) | Test Penguji |
|---|---|---|---|
| `admin/pendaftaran/index.blade.php` | Form filter status & search | `Citra Lestari`, `Tinjau Berkas`, `Diajukan`, `Semua Status` | `AdminChildPagesVisualTest` |
| `admin/pendaftaran/show.blade.php` | `id="docPreviewModal"`, `openDocPreview` | `Keputusan Verifikasi`, nomor pendaftaran | `AdminChildPagesExtendedVisualTest` |
| `admin/kuota/index.blade.php` | `modal-tambah-kuota`, `modal-edit-kuota` | `Manajemen Kuota Magang`, `Okupansi:`, `Alokasikan Kuota Jurusan` | `AdminChildPagesVisualTest` |
| `admin/waiting-list/index.blade.php` | Filter jurusan & bulan | `Antrean Waiting List`, `FIFO` | `AdminChildPagesExtendedVisualTest` |
| `admin/penempatan/index.blade.php` | `modal-surat-preview`, `openSuratPreview` | `Penempatan`, nama peserta | `AdminChildPagesExtendedVisualTest` |
| `pembimbing/logbook/index.blade.php` | Checkbox bulk approve | `Reviu Logbook`, catatan revisi | `NextLevelEnhancementsTest` |
| `pembimbing/peserta/show.blade.php` | Tab nav ID & chart container | Nama mentee, institusi, capaian bimbingan | `DeltaDashboardEnhancementTest` |
| `peserta/tugas/index.blade.php` | Tab filter tugas | `Tugas & Proyek Magang`, `Semua Tugas`, `Ditugaskan`, `Sedang Dikerjakan` | `PesertaClassroomTugasLogbookTest` |
| `peserta/tugas/show.blade.php` | Form kirim progres / link | `Tugas Anda`, `Panduan Pengerjaan`, `Serahkan / Kirim Progres` | `PesertaClassroomTugasLogbookTest` |
| `superadmin/bulan/index.blade.php` | Modal create/edit bulan | `Bulan Magang`, tanggal buka & tutup | `SuperAdminPortalVisualTest` |

---

## 4. SOP & Protokol Eksekusi untuk Junior Dev / AI (Runbook)

Ikuti alur kerja 6 langkah ini untuk **setiap tugas**:
1. **Langkah 1**: Baca rincian tugas dan cek tabel elemen sakral di Bab 3.
2. **Langkah 2**: Jalankan perintah uji verifikasi sebelum menyentuh file (pastikan saat ini hijau).
3. **Langkah 3**: Lakukan perubahan tampilan pada file target dengan membungkus elemen ke dalam komponen Gentelella (`<x-gentelella.*>`).
4. **Langkah 4**: Jalankan kembali perintah verifikasi:
   - Jika hijau (PASS): Lanjut ke Langkah 5.
   - Jika merah (FAIL): Periksa diff, pastikan teks assertion atau modal ID tidak sengaja terhapus, lalu perbaiki sampai hijau.
5. **Langkah 5**: Centang checklist `[x]` pada dokumen plan ini.
6. **Langkah 6**: Lanjutkan ke tugas berikutnya secara berurutan. Jangan melompat tugas!

---

## 5. Ringkasan Sprint & Estimasi Waktu

| Paket | Nama Paket Modul | Jumlah Tugas | Estimasi Waktu | Tingkat Kesulitan |
|---|---|---|---|---|
| **Paket A** | Fondasi Komponen Reusable Blade | 1 Tugas (A1) | 45 Menit | 🟢 Mudah |
| **Paket B** | Sub-Modul Admin Operasional | 5 Tugas (B1–B5) | 6,5 Jam | 🟡 Sedang |
| **Paket C** | Sub-Modul Pembimbing Lapangan | 4 Tugas (C1–C4) | 6 Jam | 🟡 Sedang |
| **Paket D** | Sub-Modul Peserta Magang | 4 Tugas (D1–D4) | 5 Jam | 🟡 Sedang |
| **Paket E** | Sub-Modul SuperAdmin & Master Data | 4 Tugas (E1–E4) | 5,5 Jam | 🟡 Sedang / 🔴 Tinggi |
| **Paket F** | Modul Profil & Pengaturan Terpadu | 1 Tugas (F1) | 1 Jam | 🟢 Mudah |
| **Paket G** | Quality Gate, Linter & Full Suite | 1 Tugas (G1) | 1,5 Jam | 🔴 Tinggi |
| **TOTAL** | **7 Paket Sprint Bertahap** | **20 Tugas Terstruktur** | **± 26,5 Jam** | **100% Zero-Regression** |

---

## 6. Kriteria Selesai Sprint (Definition of Done)

Sebelum branch `test-2` dapat diajukan untuk Pull Request / Merge ke branch utama, pastikan:
- [ ] Seluruh halaman turunan di 4 role telah mengadopsi standar komponen Gentelella (`<x-gentelella.x-panel>`, `<x-gentelella.table>`, `<x-gentelella.filter-bar>`, `<x-gentelella.action-btn>`).
- [ ] Seluruh tabel, formulir, dan tombol aksi konsisten menggunakan warna resmi Kominfo Sleman (Deep Navy `#0641CC` & Blue `#0751FF`).
- [ ] Seluruh popup modal dialog berfungsi normal menggunakan Vanilla JavaScript tanpa jQuery.
- [ ] Seluruh child page responsif sempurna pada resolusi Mobile (390px), Tablet (768px), dan Desktop (1440px).
- [ ] Seluruh child page mendukung tampilan Dark Mode (`[data-theme="dark"]`).
- [ ] Tidak ada perubahan pada file backend Controller, Routing, Model, dan Database Migrations.
- [ ] Kode terformat rapi sesuai standar Laravel Pint (`./vendor/bin/pint`).
- [ ] Seluruh test suite PHPUnit berjalan hijau (**Minimal 457 Passed, 0 Failed, 100% Green**).
- [ ] Graphify knowledge graph terbarui (`graphify update .`).
