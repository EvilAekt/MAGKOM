# TASK / PRD: Redesign Tabel Laporan Kinerja ASN & Penyeragaman Tampilan Data Penempatan Peserta
## Dokumen Spesifikasi Teknis & Rencana Kerja (Senior Developer ke Junior Developer / AI Agent)
- **Target Modul**: Dashboard Pembimbing — Laporan Kinerja Mentoring ASN (`/pembimbing/laporan-kinerja`)
- **Role Pengembang**: Junior Developer / AI Assistant
- **Pemberi Task**: Senior Developer MagKom Sleman
- **Prioritas**: UI/UX Modernization, Data Consistency, & Design System Alignment
- **Dokumen Referensi**: Tangkapan Layar `media_1793947608490_cc3ca2bf.jpg` (Gambar 1: Desain Eksisting) & `media_1793947927207_81f582e2.jpg` (Gambar 2: Desain Target)

---

# 1. LATAR BELAKANG & TUJUAN (OBJECTIVES)

Halo Junior Dev / Agent,

Berdasarkan evaluasi terhadap halaman **Laporan Kinerja Pembimbing Lapangan ASN** (`/pembimbing/laporan-kinerja`), antarmuka saat ini (lihat Gambar 1) masih menggunakan styling inline manual dan layout tabel lama yang belum seragam dengan standar komponen desain MagKom (Gentelella Design System). Selain itu, sejalan dengan keputusan arsitektur sebelumnya di mana peran Pembimbing difokuskan pada mentoring, penugasan, dan review catatan logbook (fitur Penilaian Magang telah ditiadakan dari dashboard pembimbing), terdapat dua penyesuaian utama yang harus dikerjakan:

1. **Penyederhanaan Kolom Tabel (Hapus Kolom Nilai Akhir)**:
   - Kolom tabel yang ditampilkan HANYA terdiri dari 6 kolom spesifik:
     1. `Nama Mentee`
     2. `Institusi & Jurusan`
     3. `Bidang Magang`
     4. `Tugas Selesai`
     5. `Logbook`
     6. `Status`
   - Kolom `Nilai Akhir` **DIHAPUS** sepenuhnya dari tabel.
   - Pada kartu ringkasan KPI di atas tabel, kartu ke-4 (*"Rata-rata Nilai Akhir"*) diselaraskan (dihilangkan atau diganti menjadi metrik relevan) agar tidak menampilkan data nilai yang sudah tidak digunakan.

2. **Redesign Tabel Mengadopsi Pola "Data Penempatan Peserta"**:
   - Bagian tabel portofolio bimbingan di-redesign total secara visual mengikuti pola tabel **"Data Penempatan Peserta"** (lihat Gambar 2):
     - Membungkus tabel dengan komponen resmi MagKom `<x-gentelella.x-panel title="Data Penempatan Peserta">` lengkap dengan header chevron collapse di kanan.
     - Menggunakan kelas terstandarisasi `.adm-table-container`, `.adm-table`, dan `.adm-table-footer` dari `resources/css/components/tables.css`.
     - **Kolom Nama Mentee**: Menampilkan Avatar box inisial kotak bulat bernuansa biru modern (`background: linear-gradient(135deg, #0641CC 0%, #2563EB 100%)`, teks putih tebal) disertai nama tebal dan NIM/NISN di bawahnya.
     - **Kolom Status**: Menggunakan styling pill badge resmi (`Aktif`, `Selesai`, `Nonaktif`) dengan border halus dan palet warna pastel.
     - **Paginasi Terstandarisasi**: Mengintegrasikan komponen `<x-gentelella.pagination-bar>` dengan counter `"Menampilkan 1 - 5 dari X peserta magang"`, badge `"Penempatan Aktif"`, pemilih baris `[5] [10] [25]`, dan navigasi lembar halaman.
     - **Catatan Penting**: Konten data tetap merupakan data rekapitulasi portofolio bimbingan (Nama Mentee, Institusi & Jurusan, Bidang Magang, Tugas Selesai, Logbook, Status) tanpa checkbox atau tombol aksi admin.

---

# 2. ASUMSI KERJA & KEPUTUSAN TEKNIS (WORKING ASSUMPTIONS)

1. **Struktur Kolom Tabel Final (Strict 6 Kolom)**:
   - Header tabel yang dirender:
     1. `Nama Mentee` (dengan avatar inisial, nama peserta, dan NIM/NISN).
     2. `Institusi & Jurusan` (nama universitas/sekolah di baris 1, program studi/jurusan di baris 2).
     3. `Bidang Magang` (nama bidang dinas penempatan, misal Aptika/E-Gov).
     4. `Tugas Selesai` (angka selesai / total tugas, text-align center).
     5. `Logbook` (angka review / total logbook dengan highlight hijau, text-align center).
     6. `Status` (badge pill status penempatan).
2. **Paginasi Server-side pada Controller**:
   - `LaporanKinerjaController::index`:
     - Perhitungan metrik KPI di atas tabel tetap dihitung secara global untuk seluruh mentee milik pembimbing yang login.
     - Data tabel dipaginasi menggunakan `$query->paginate($perPage)->withQueryString()` dengan opsi ukuran halaman `[5, 10, 25]` (default 5, selaras dengan Gambar 2).
     - Menggunakan metode Laravel `LengthAwarePaginator::through(fn(Penempatan $p) => ...)` untuk memetakan hitungan tugas dan logbook per mentee secara bersih tanpa merusak struktur paginator.
3. **Kartu KPI Ringkasan di Atas Tabel**:
   - Menghapus kartu ke-4 *"Rata-rata Nilai Akhir"* dari grid KPI, sehingga tersisa 3 kartu KPI utama:
     - `Total Mentee Dibimbing` (dengan subtext aktif & selesai)
     - `Logbook Direview` (dengan rasio terkirim)
     - `Tugas Diselesaikan` (dengan rasio tugas selesai)
   - Layout grid otomatis menjadi 3 kolom rapi dan proporsional (`grid-template-columns: repeat(auto-fit, minmax(260px, 1fr))`).
4. **Kepatuhan Aturan CSS & Blade**:
   - Dilarang menulis styling inline baru (`style="..."`) di Blade.
   - Menggunakan kelas CSS yang sudah ada di `resources/css/components/tables.css` (`adm-table-container`, `adm-table`, `adm-table-footer`, `avatar-box`, dll).
   - Jika membutuhkan kelas spesifik halaman laporan kinerja, letakkan di `resources/css/pages/pembimbing/laporan-kinerja-index.css` dan daftarkan pada pipeline Vite.

---

# 3. EVALUASI OPSI PENDEKATAN ARSITEKTUR

Kami mengevaluasi dua opsi pendekatan implementasi:

- **Opsi A (Terpilih: Standarisasi Komponen Gentelella & Paginasi Server-Side `through`)**:
  - Menggunakan `<x-gentelella.x-panel title="Data Penempatan Peserta">` dan `<x-gentelella.pagination-bar>`.
  - Menggunakan paginasi server-side di Controller (`per_page` 5, 10, 25) dengan pemetaan `through()`.
  - *Kelebihan*: 100% konsisten dengan desain sistem MagKom (Gambar 2), performa tinggi, URL dapat di-bookmark/share dengan parameter halaman, navigasi rapi tanpa lag.
  - *Kekurangan*: Memerlukan sedikit penyesuaian pada Controller untuk mendukung paginasi dan pemetaan koleksi.

- **Opsi B (Paginasi Client-Side / Koleksi Murni Tanpa Paginator)**:
  - Tetap mengambil seluruh data mentee dan merender semua baris dalam satu tabel panjang tanpa pagination bar.
  - *Kelebihan*: Controller tidak perlu diubah ke `paginate()`.
  - *Kekurangan*: Tidak memenuhi tampilan pada Gambar 2 yang secara eksplisit memiliki pagination bar `"Menampilkan 1 - 5 dari 14 peserta magang [Penempatan Aktif]"` dan selektor `[5] [10] [25]`. Tabel akan menjadi sangat panjang jika mentee berjumlah puluhan.

-> **Keputusan Senior Dev**: Wajib gunakan **Opsi A**.

---

# 4. PEMETAAN BERKAS YANG DIUBAH (FILE IMPACT TABLE)

| Berkas | Aksi | Ringkasan Pekerjaan |
| :--- | :---: | :--- |
| `app/Http/Controllers/Pembimbing/LaporanKinerjaController.php` | Modify | 1. Implementasikan paginasi dengan filter `per_page` (default 5, opsi 5, 10, 25).<br>2. Gunakan `->paginate($perPage)->withQueryString()->through(...)` untuk `$rekapMentees`.<br>3. Pertahankan kalkulasi KPI global (Total Mentee, Logbook Reviewed, Tugas Selesai). |
| `resources/views/pembimbing/laporan-kinerja/index.blade.php` | Modify | 1. Hapus kartu KPI ke-4 (*Rata-rata Nilai Akhir*).<br>2. Ganti manual table wrapper dengan `<x-gentelella.x-panel title="Data Penempatan Peserta">`.<br>3. Ubah header tabel menjadi 6 kolom: `Nama Mentee`, `Institusi & Jurusan`, `Bidang Magang`, `Tugas Selesai`, `Logbook`, `Status`.<br>4. Tambahkan avatar initial box pada kolom Nama Mentee.<br>5. Tambahkan badge status pill bernuansa pastel.<br>6. Pasang `<x-gentelella.pagination-bar>` di footer tabel. |
| `resources/css/pages/pembimbing/laporan-kinerja-index.css` | Create | Buat stylesheet khusus untuk komponen sel tabel rekapitulasi, badge avatar, styling rasio progres, dan dark theme overrides. |
| `vite.config.js` | Modify | Daftarkan `resources/css/pages/pembimbing/laporan-kinerja-index.css` pada input build Vite. |
| `tests/Feature/PembimbingLaporanKinerjaViewTest.php` | Create | Buat test suite untuk memvalidasi 6 kolom tabel, ketiadaan kolom Nilai Akhir, kehadiran x-panel Data Penempatan Peserta, avatar mentee, dan pagination bar. |

---

# 5. PANDUAN LANGKAH IMPLEMENTASI BERTINGKAT (STEP-BY-STEP RUNBOOK)

Junior Developer / Agent diwajibkan mengeksekusi langkah-langkah di bawah ini secara berurutan:

### Tahap 1: Backend & Controller Logic (`LaporanKinerjaController.php`)
- [x] **1.1 Dukungan Paginasi & Penanganan Ukuran Halaman**:
  - Ambil parameter `per_page` dari request:
    ```php
    $perPage = $request->integer('per_page', 5);
    if (! in_array($perPage, [5, 10, 25], true)) {
        $perPage = 5;
    }
    ```
- [x] **1.2 Pertahankan Metrik Global & Paginasikan Rekapitulasi**:
  - Ambil semua ID penempatan untuk menghitung statistik global KPI (`totalMentee`, `totalMenteeAktif`, `totalMenteeSelesai`, `totalLogbookReviewed`, `totalTugasSelesai`, `rasioTugasSelesai`).
  - Lakukan paginasi pada query:
    ```php
    $rekapMentees = $query->latest('tgl_mulai')
        ->paginate($perPage)
        ->withQueryString()
        ->through(function (Penempatan $p) {
            return [
                'penempatan' => $p,
                'total_tugas' => $p->tugas->count(),
                'tugas_selesai' => $p->tugas->where('status', StatusTugasEnum::Selesai)->count(),
                'total_logbook' => $p->logbooks->count(),
                'logbook_reviewed' => $p->logbooks->whereIn('status', [StatusLogbookEnum::Disetujui, StatusLogbookEnum::PerluRevisi])->count(),
                'status' => $p->status,
            ];
        });
    ```

---

### Tahap 2: Frontend Styling & Asset Pipeline (`Vite` & `CSS`)
- [x] **2.1 Buat CSS Halaman Laporan Kinerja (`resources/css/pages/pembimbing/laporan-kinerja-index.css`)**:
  - Definisikan styling avatar box:
    ```css
    .mentee-avatar-square {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: linear-gradient(135deg, #0641CC 0%, #2563EB 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(6, 65, 204, 0.2);
    }
    ```
  - Definisikan badge status pill pastel selaras dengan `admin/penempatan`:
    ```css
    .status-pill-aktif {
        background: #D1FAE5;
        color: #065F46;
        border: 1px solid #A7F3D0;
    }
    .status-pill-selesai {
        background: #EFF6FF;
        color: #1D4ED8;
        border: 1px solid #DBEAFE;
    }
    .status-pill-nonaktif {
        background: #FEE2E2;
        color: #991B1B;
        border: 1px solid #FECACA;
    }
    ```
  - Tambahkan dukungan dark theme (`[data-theme="dark"]`).
- [x] **2.2 Daftarkan di `vite.config.js`**:
  - Tambahkan `'resources/css/pages/pembimbing/laporan-kinerja-index.css'` ke dalam array input Vite.

---

### Tahap 3: Blade View Refactor (`resources/views/pembimbing/laporan-kinerja/index.blade.php`)
- [x] **3.1 Include CSS Vite di `@push('styles')`**:
  ```blade
  @push('styles')
      @vite(['resources/css/pages/pembimbing/laporan-kinerja-index.css'])
  @endpush
  ```
- [x] **3.2 Sesuaikan KPI Cards Grid**:
  - Hapus kartu ke-4 *"Rata-rata Nilai Akhir"*.
  - Atur container 3 kartu KPI dengan grid responsive yang rapi.
- [x] **3.3 Redesign Card Rekapitulasi Menggunakan `<x-gentelella.x-panel>`**:
  - Ganti wrapper manual dengan:
    ```blade
    <x-gentelella.x-panel title="Data Penempatan Peserta">
    ```
  - Buat tabel dengan kelas `.adm-table` di dalam `.adm-table-container`:
    ```blade
    <div class="adm-table-container">
        <div style="overflow-x: auto; width: 100%;">
            <table class="adm-table admin-table">
                <thead>
                    <tr>
                        <th>Nama Mentee</th>
                        <th>Institusi &amp; Jurusan</th>
                        <th>Bidang Magang</th>
                        <th style="text-align: center;">Tugas Selesai</th>
                        <th style="text-align: center;">Logbook</th>
                        <th style="text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    ...
                </tbody>
            </table>
        </div>
        <div class="adm-table-footer">
            <x-gentelella.pagination-bar 
                :paginator="$rekapMentees" 
                :perPageOptions="[5, 10, 25]" 
                entity="peserta magang"
                badgeText="Penempatan Aktif"
            />
        </div>
    </div>
    </x-gentelella.x-panel>
    ```
- [x] **3.4 Format Sel Baris Data Sesuai Gambar 2**:
  - Baris Nama Mentee: Avatar square inisial + Nama tebal + Subtext `NIM/NISN: ...`.
  - Baris Institusi & Jurusan: Nama kampus tebal + Jurusan di baris bawah.
  - Baris Bidang Magang: Nama bidang dengan tipografi bersih.
  - Baris Tugas Selesai: `X / Y` tugas.
  - Baris Logbook: `A / B` logbook dengan angka A tebal warna hijau emerald.
  - Baris Status: Pill badge pastel (`Aktif`, `Selesai`, `Nonaktif`).

---

### Tahap 4: Build, Testing, & Quality Gates
- [x] Jalankan kompilasi Vite: `npm.cmd run build` (pastikan tidak ada error build).
- [x] Jalankan formatting Pint: `php vendor/bin/pint --test`.
- [x] Buat dan jalankan Feature Test baru: `php artisan test tests/Feature/PembimbingLaporanKinerjaViewTest.php`.
- [x] Jalankan test suite regresi eksisting: `php artisan test tests/Feature/AdvancedRoleEnhancementPhaseSixTest.php`.

---

# 6. SKENARIO PENGUJIAN & VERIFIKASI (TESTING STRATEGY)

### Skenario 1: Verifikasi Header Kolom & Ketiadaan Nilai Akhir
- Akses halaman `/pembimbing/laporan-kinerja` sebagai role Pembimbing.
- **Harus Muncul**: `Nama Mentee`, `Institusi & Jurusan`, `Bidang Magang`, `Tugas Selesai`, `Logbook`, `Status`.
- **Tidak Boleh Muncul**: `Nilai Akhir`, `Rata-rata Nilai Akhir`.

### Skenario 2: Verifikasi Redesign Panel & Avatar Mentee
- Panel judul tabel harus bertuliskan `Data Penempatan Peserta` dengan tombol chevron collapse di kanan.
- Setiap baris memiliki avatar box inisial huruf pertama nama mentee (misal `I` untuk Indra, `J` untuk Joko).

### Skenario 3: Verifikasi Pagination Bar & Per-Page Controls
- Footer tabel menampilkan counter `"Menampilkan 1 - 5 dari X peserta magang"`.
- Tombol pemilih per halaman `[5] [10] [25]` dapat diklik dan memperbarui query URL `?per_page=10`.
- Navigasi lembar halaman (Prev / 1 / 2 / Next) berfungsi dengan benar.

---

# 7. RISIKO, EDGE CASES, & MITIGASI

1. **Edge Case: Pembimbing Baru dengan 0 Mentee**:
   - Jika belum ada mentee yang dibimbing, tampilkan state kosong yang ramah (*empty state*) dengan icon dan teks informasi yang elegan di dalam panel.
2. **Edge Case: Dark Mode Contrast**:
   - Pastikan teks avatar, background badge status pastel, dan garis border tabel memiliki kontras yang memenuhi WCAG 2.1 AA di mode gelap.
3. **Risiko Pemecahan Halaman Cetak (Print LPJ)**:
   - Route `/pembimbing/laporan-kinerja/cetak` tetap dipertahankan untuk kebutuhan audit kedinasan dan tidak terpengaruh oleh perubahan pagination pada halaman web view.

---

# 8. KRITERIA SELESAI (DEFINITION OF DONE)

- [x] Kolom pada tabel Laporan Kinerja hanya menampilkan 6 kolom yang diminta (`Nama Mentee`, `Institusi & Jurusan`, `Bidang Magang`, `Tugas Selesai`, `Logbook`, `Status`).
- [x] Kolom `Nilai Akhir` dan kartu KPI `Rata-rata Nilai Akhir` telah dihilangkan dari interface.
- [x] Tabel portofolio bimbingan telah di-redesign menggunakan `<x-gentelella.x-panel title="Data Penempatan Peserta">` dan kelas `.adm-table`.
- [x] Kolom Nama Mentee dilengkapi avatar box inisial dan informasi NIM/NISN selaras dengan Gambar 2.
- [x] Footer tabel dilengkapi `<x-gentelella.pagination-bar>` dengan per-page selector `[5, 10, 25]` dan badge `"Penempatan Aktif"`.
- [x] Tidak ada CSS inline (`style="..."`) baru di template Blade.
- [x] `npm.cmd run build` berhasil 100% tanpa error.
- [x] `php vendor/bin/pint --test` lulus 100%.
- [x] Automated feature test lulus 100%.

