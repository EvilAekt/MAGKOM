# PRD & Senior Developer Task: Redesign Halaman Detail & Reviu Tugas Pembimbing Ala Google Classroom Workspace

**Target Pelaksana**: Junior Developer / Gemini 3.8 Flash (AI Model)  
**Kode Dokumen**: `PRD-TASK-2026-11-04-PEMBIMBING-TUGAS-REVIEW-CLASSROOM`  
**Status**: Ready for Review & Implementation  
**Tingkat Kerumitan**: Sedang (Frontend Blade, Scoped CSS, Vanilla ES Modules, Controller Preparation, Automated Feature Tests)

---

## 1. Executive Summary & Objective

Tugas ini bertujuan merombak antarmuka halaman **Detail & Reviu Penugasan Pembimbing** (`route('pembimbing.tugas.show', $tugas->id)`) dari format card konvensional lama menjadi **Google Classroom-style Assignment Review & Grading Workspace**.

### Penyesuaian Krusial MagKom vs Google Classroom:
1. **TIDAK ADA INPUT NILAI ANGKA**:
   - Di Google Classroom standar terdapat input numerik seperti `Nilai: [   ] /100`.
   - Di Portal MagKom, pembimbingan magang bersifat **Outcome-Based / Formative Mentoring**. **Tidak ada nilai angka /100**.
2. **Fokus Alur Reviu**:
   - **Melihat Hasil Kerja Peserta**: Pratinjau terstruktur dari berkas lampiran (PDF/ZIP/gambar/dokumen) dan tautan tugas eksternal (Google Drive, Figma, GitHub, web demo).
   - **Kembalikan Tugas (Minta Revisi)**: Jika hasil kerja mentee belum memenuhi standar, pembimbing mengembalikan tugas disertai arahan/catatan revisi yang konstruktif (status: `perlu_revisi`, feedback wajib).
   - **Terima Progres**: Mengonfirmasi bahwa progres pengerjaan diterima dan sesuai (status: `diterima`).
   - **Selesaikan Tugas**: Jika seluruh kriteria tugas telah tuntas dan disetujui, pembimbing menandai tugas sebagai **Selesai** (status tugas: `selesai`).
3. **Keselarasan Visual (Design System)**:
   - Wajib selaras 100% dengan tampilan yang baru saja dibuat di **Buat Tugas** (`create.blade.php`) dan **Edit Tugas** (`edit.blade.php`) menggunakan palet warna Sleman Blue (`#01347C`), container bersih, layout modern, tanpa tag `<style>` dan `<script>` inline di Blade.

---

## 2. Acuan Visual (Visual Reference Mapping)

Berdasarkan 3 gambar yang diunggah pengguna:

| Gambar | Nama File / Sumber | Bagian UI & Penyesuaian Desain |
| :--- | :--- | :--- |
| **Foto 1** | `media_1793775252607_40bdb036.png` (Google Classroom Review) | **Topbar**: Tombol tutup `✕`, Pemilih Mentee `[NP] Naufal Pratama ▾` dengan navigasi panah `<` dan `>`, chip berkas/link peserta `[✓ Link]`, status pengembalian, dan tombol utama **`Kembalikan`**.<br>**Canvas Tengah**: Pratinjau hasil penyerahan peserta (link/berkas) dengan tombol `Buka di jendela baru ↗`.<br>**Sidebar Kanan**: Komentar pribadi/feedback, riwayat penyerahan. **Input nilai `/100` DIHAPUS**, diganti dengan tombol status evaluasi pembimbing. |
| **Foto 2** | `media_1793775269140_eee9144e.jpg` (Existing MagKom Show View) | Menunjukkan tampilan lawas 2-kolom Gentelella dengan badge lama (`Laporan Akhir`, `Kompleksitas: Sedang`) dan formulir review terpisah di bawah. Seluruh bagian ini digantikan oleh Google Classroom Workspace baru. |
| **Foto 3** | `media_1793775374108_6ac40b54.png` (MagKom Create Workspace Baru) | Standar desain acuan: Topbar biru-putih rapi, rounded 12px, font Inter/Figtree, border `#CBD5E1`, tombol action pill, dan form responsif. Tampilan reviu harus memiliki bahasa desain yang serupa. |

---

## 3. Scope & Batasan Pekerjaan (Do's and Don'ts)

### Wajib Dilakukan (DO):
- Buat file CSS baru di `resources/css/pages/pembimbing/tugas-review.css`.
- Buat file JS baru di `resources/js/pages/pembimbing/tugas-review.js`.
- Daftarkan entry point di `vite.config.js`.
- Ubah `resources/views/pembimbing/tugas/show.blade.php` hanya dengan markup semantik Blade tanpa tag `<style>` dan `<script>` inline.
- Sediakan navigasi pemilih mentee se-batch (`batch_id`) dengan panah `<` dan `>` jika tugas dibuat bersamaan untuk multi-mentee, atau mentee bimbingan aktif.
- Pertahankan seluruh route contract, authorization Gate, audit trail, dan enum `StatusReviewProgresEnum` (`menunggu`, `diterima`, `perlu_revisi`).
- Tambahkan automated feature test di `tests/Feature/PembimbingReviewClassroomTest.php`.

### Dilarang Keras (DON'T):
- **JANGAN menambahkan input nilai angka** (`/100` atau sejenisnya).
- **JANGAN menulis tag `<style>` atau `<script>` di dalam Blade**.
- **JANGAN menggunakan inline event handler** seperti `onclick="..."`, `onchange="..."`.
- **JANGAN merusak mitigasi keamanan anti-IDOR** (pembimbing hanya boleh melihat dan mereview mentee binaannya sendiri).
- **JANGAN menghapus atau mengubah route/API contract** yang sudah aktif.

---

## 4. Arsitektur Komponen & Wireframe Spesifikasi

```
+-------------------------------------------------------------------------------------------------------+
| TOPBAR REVIU CLASSROOM                                                                                |
| [✕ Tutup]  |  [NP] Naufal Pratama ▾  [ < ] [ > ]  |  [✓ Link Tugas ↗]  |  [Status]  [Kembalikan] [Tandai Selesai] |
+-------------------------------------------------------------------------------------------------------+
|                                                   |                                                   |
| KANVAS UTAMA (KIRI / TENGAH - 70%)                | SIDEBAR EVALUASI & FEEDBACK (KANAN - 30%)         |
| Tab: [ Hasil Kiriman Peserta ] [ Instruksi Tugas ]| [ Nilai & Masukan ]                         [ >> ]|
|                                                   |                                                   |
| Area Pratinjau Kiriman:                           | Informasi Penyerahan:                             |
| - Jika Link: Kartu Tautan + Buka Jendela Baru ↗   | • Diserahkan: 2 Nov 2026, 11:03 WIB               |
| - Jika File: Kartu Berkas + Unduh ⬇               | • Tenggat: 11 Nov 2026 (Tepat Waktu)              |
| - Catatan Progres Pengerjaan Mentee               | • Berkas: [✓ Link Tugas]                          |
|                                                   | ------------------------------------------------- |
| (Jika belum ada kiriman: Empty state ilustrasi    | Form Evaluasi & Pengembalian:                     |
| "Peserta Belum Mengirimkan Hasil Pekerjaan")      | Hasil: [● Diterima] [○ Kembalikan (Revisi)]       |
|                                                   |                                                   |
| Tab Instruksi Tugas:                              | Template Evaluasi Cepat:                          |
| - Rincian deskripsi tugas awal dari pembimbing    | [ Pilih Arahan Evaluasi Cepat... ▾ ]              |
| - Lampiran panduan/referensi dari pembimbing      |                                                   |
|                                                   | Catatan Umpan Balik untuk Mentee:                 |
|                                                   | [ Tuliskan arahan perbaikan...                  ] |
|                                                   |                                                   |
|                                                   | [ Kembalikan ke Mentee ]  [ Terima Progres ]      |
|                                                   | ------------------------------------------------- |
|                                                   | Riwayat Umpan Balik Sebelumnya (Audit Trail)      |
+-------------------------------------------------------------------------------------------------------+
```

---

## 5. Rencana Langkah Implementasi (Step-by-Step Execution Plan)

### Langkah 1: Persiapan Controller & Data Mentee Batch (`app/Http/Controllers/Pembimbing/TugasController.php`)
Di method `show(Request $request, Tugas $tugas)`:
- Ambil daftar tugas saudara satu batch (`$tugas->batch_id`) atau daftar tugas sejenis mentee aktif pembimbing untuk mendukung tombol navigasi mentee Sebelumnya (`<`) dan Selanjutnya (`>`).
- Kirim variabel ke view:
  - `$tugas`: Model penugasan aktif (lengkap dengan `progresLogs.feedback`, `penempatan.peserta.profil.jurusan`).
  - `$siblingTugasList`: Koleksi penugasan lain dalam batch untuk switcher mentee dropdown dan panah `<` / `>`.
  - `$prevTugasId` & `$nextTugasId`: ID tugas sebelum dan sesudah untuk tombol panah cepat.

### Langkah 2: Registrasi Asset Vite (`vite.config.js`)
Daftarkan dua entry point baru:
- `resources/css/pages/pembimbing/tugas-review.css`
- `resources/js/pages/pembimbing/tugas-review.js`

### Langkah 3: Desain Scoped CSS (`resources/css/pages/pembimbing/tugas-review.css`)
Implementasikan class styling modern:
- `.review-workspace`: Kontainer flex layout memenuhi layar dengan tinggi minimal `calc(100vh - 120px)`.
- `.review-topbar`: Header tetap (sticky) dengan background putih `#FFFFFF`, border-bottom `#E2E8F0`, Sleman Blue badge.
- `.review-mentee-picker`: Dropdown custom elegan dengan avatar inisial mentee, panah prev/next rounded button.
- `.review-canvas`: Background lembut `#F8FAFC` dengan card pratinjau submission di tengah.
- `.review-link-preview-card` & `.review-file-preview-card`: Kartu representasi submission yang bersih dengan tombol aksi `Buka di Tab Baru` dan `Unduh`.
- `.review-sidebar`: Panel kanan lebar 360px–400px dengan border-left `#CBD5E1`, background `#FFFFFF`, scrollable independen.
- `.review-empty-state`: Ilustrasi ikon dan tipografi Google Classroom style saat mentee belum menyerahkan laporan.
- Dark mode compatibility: Variabel warna gelap jika tema `[data-theme="dark"]` aktif.

### Langkah 4: Logika JavaScript ES Modules (`resources/js/pages/pembimbing/tugas-review.js`)
Implementasikan modul ES:
- **Tab Switching**: Toggle antara tab `Hasil Kiriman Peserta` dan `Instruksi Tugas Pembimbing`.
- **Sidebar Collapse Toggle**: Tombol `>>` / `<<` untuk meluaskan kanvas dokumen utama jika pembimbing butuh ruang lebih luas.
- **Template Snippet Inserter**: Memasukkan template teks evaluasi cepat ke dalam textarea umpan balik secara instan.
- **Form Validation & Dynamic Required**:
  - Jika opsi `Kembalikan (Perlu Revisi)` dipilih, textarea feedback otomatis `required` dengan indikator merah `*`.
  - Jika opsi `Diterima` dipilih, feedback bersifat opsional.
- **Keyboard Shortcut Navigasi**:
  - Tombol `[` atau `Alt + Left`: Mentee sebelumnya.
  - Tombol `]` atau `Alt + Right`: Mentee berikutnya.

### Langkah 5: Refaktor View Blade (`resources/views/pembimbing/tugas/show.blade.php`)
- Ganti layout lama dengan struktur `.review-workspace`.
- Load asset via `@push('styles') @vite(['resources/css/pages/pembimbing/tugas-review.css']) @endpush` dan `@push('scripts') @vite(['resources/js/pages/pembimbing/tugas-review.js']) @endpush`.
- Pastikan seluruh tag `<style>` dan `<script>` lama dihapus bersih dari file Blade.
- Hubungkan form review ke route `route('pembimbing.progres.review', $latestProgres->id)`.
- Hubungkan form selesaikan tugas ke route `route('pembimbing.tugas.selesai', $tugas->id)`.

### Langkah 6: Pembuatan Automated Feature Test (`tests/Feature/PembimbingReviewClassroomTest.php`)
Buat pengujian komprehensif:
1. `test_halaman_review_tugas_menampilkan_komponen_classroom_workspace`: Verifikasi topbar, switcher mentee, kanvas kiriman, dan sidebar feedback.
2. `test_pembimbing_bisa_mengembalikan_tugas_dengan_umpan_balik_revisi`: Menguji submit `status_review = perlu_revisi` dengan feedback wajib (menghasilkan status 200/redirect sukses).
3. `test_pembimbing_bisa_menerima_progres_tugas`: Menguji submit `status_review = diterima`.
4. `test_pembimbing_bisa_menandai_tugas_selesai`: Menguji submit ke endpoint selesai.
5. `test_anti_idor_pembimbing_dilarang_mereview_tugas_mentee_pembimbing_lain`: Perlindungan otorisasi tetap tegak.

---

## 6. Verifikasi & Quality Gates

Sebelum menyelesaikan pekerjaan, jalankan urutan quality gate berikut:
1. **PHPUnit Test**:
   ```bash
   php artisan test tests/Feature/PembimbingReviewClassroomTest.php
   php artisan test --filter=Tugas
   ```
2. **Pint Code Formatting**:
   ```bash
   ./vendor/bin/pint --test
   ```
3. **PHPStan Static Analysis**:
   ```bash
   ./vendor/bin/phpstan analyse --memory-limit=1G
   ```
4. **Vite Build**:
   ```bash
   npm run build
   ```
5. **Graphify Sync**:
   ```bash
   python -m graphify update .
   ```

---

## 7. Kriteria Selesai (Definition of Done)

- [ ] Tampilan halaman reviu menyerupai Google Classroom Grading View (Foto 1).
- [ ] Tidak ada input nilai angka `/100` di sidebar.
- [ ] Tersedia fitur melihat kiriman peserta (file/link/catatan).
- [ ] Tersedia fitur mengembalikan tugas (`perlu_revisi`) dengan arahan perbaikan.
- [ ] Tersedia fitur menerima tugas dan menandai tugas selesai (`selesai`).
- [ ] Tersedia navigasi antar mentee (`<` dan `>`) di topbar.
- [ ] Tidak ada kode CSS/JS di dalam file Blade (patuh `AGENTS.md`).
- [ ] Seluruh automated test lulus 100% dan build Vite berhasil tanpa error.

