# Rencana Perbaikan Filter Jenis Tugas (`Semua`, `Individu`, `Kelompok`) pada Halaman Tugas Pembimbing

## 1. Tujuan
Memperbaiki logika query, perhitungan counter pill filter, dan tampilan daftar pada halaman **Tugas Magang Pembimbing** (`pembimbing.tugas.index`) agar:
1. **Filter `Semua` (default)**: menampilkan **seluruh** entitas bimbingan, baik **Kelompok** maupun **Individu**.
2. **Filter `Individu`**: hanya menampilkan peserta **Individu** saja (peserta yang mendaftar/ditempatkan secara mandiri, bukan anggota kelompok).
3. **Filter `Kelompok`**: hanya menampilkan daftar **Kelompok** saja.
4. **Counter pada pill filter** (`Semua (X)`, `Individu (Y)`, `Kelompok (Z Kelompok)`) konsisten secara satuan dan akurat mencerminkan jumlah item yang ditampilkan (`X = Y + Z`).

---

## 2. Temuan Investigasi & Akar Masalah
Berdasarkan inspeksi read-only pada codebase branch `test-2`:
1. **`app/Queries/Pembimbing/PenempatanAktifQuery.php` (`buildFiltered`)**:
   - Tidak memfilter `Penempatan` berdasarkan `jenis_tugas` (`individu` vs `kelompok`). Akibatnya, saat tab `Individu` dipilih, `$penempatanList` tetap memuat peserta yang berasal dari jalur **Kelompok**.
   - Padahal definisi kanonik pada `App\Models\Penempatan::isKelompok()` adalah:
     `pendaftaran->tipe === 'kelompok' || ! empty($this->pendaftaran->kelompok_magang_id)`.
2. **`resources/views/pembimbing/tugas/index.blade.php` (baris 118–131 & 337–488)**:
   - Pada mode `semua`, view hanya masuk ke blok `@else` yang me-render `$penempatanList` (kartu peserta), sehingga daftar `Kelompok` (`$kelompokList`) tidak ditampilkan pada tab `Semua`.
   - Label pill filter memakai satuan yang berbeda: `Semua` memakai `$counts['total']` (jumlah tugas), `Individu` memakai `$counts['individu']` (jumlah tugas individu), sedangkan `Kelompok` memakai `$kelompokList->count()` (jumlah entitas kelompok).
3. **`app/Services/Pembimbing/TugasDashboardService.php` (`getDashboardData`)**:
   - Belum memisahkan hitungan entitas `individuCount`, `kelompokCount`, dan `semuaCount` untuk kebutuhan badge pill filter serta dropdown filter peserta.

---

## 3. Asumsi & Pertanyaan Terbuka
- **Asumsi 1**: Pada tab `Semua`, peserta yang sudah tergabung dalam `KelompokMagang` direpresentasikan oleh entitas **Kelompok** pada bagian Kelompok (tidak diduplikasi lagi sebagai peserta Individu), sehingga `Semua = Kelompok + Individu` tetap bersih dan tidak membingungkan.
- **Asumsi 2**: Penentuan peserta `Individu` menggunakan aturan yang konsisten dengan `Penempatan::isKelompok()`, yaitu `Penempatan` yang **tidak** memiliki `pendaftaran` dengan `tipe = 'kelompok'` maupun `kelompok_magang_id IS NOT NULL`.
- **Asumsi 3**: Mode detail satu mentee (`?masuk={penempatan_id}`) dan tampilan tabel administratif tetap berfungsi seperti semula tanpa regresi.

---

## 4. Evaluasi Opsi Desain

| Aspek | Opsi A: Dual-Section pada `Semua` + Filter Entitas Ketat (Direkomendasikan) | Opsi B: Unified Paginator Gabungan `Kelompok` + `Individu` |
|---|---|---|
| **Mekanisme** | `PenempatanAktifQuery` memfilter penempatan individu; tab `Semua` merender bagian **Kelompok** (`$kelompokList`) dan bagian **Individu** (`$penempatanList`); tab `Individu` hanya merender `$penempatanList` individu; tab `Kelompok` hanya merender `$kelompokList`. | Menggabungkan `Collection` dari `KelompokMagang` dan `Penempatan` individu lalu membuat `LengthAwarePaginator` manual. |
| **Kelebihan** | Memanfaatkan komponen tabel kelompok & kartu mentee yang sudah ada; paginasi Eloquent tetap native; mudah dipahami & diuji. | Hanya ada 1 daftar tunggal di tab `Semua`. |
| **Kekurangan** | Tab `Semua` memiliki 2 section (Kelompok dan Individu). | Kompleksitas paginasi custom tinggi, rawan bug saat sorting/filtering, dan format UI baris kelompok vs kartu peserta berbeda. |

**Keputusan Desain**: Memilih **Opsi A** karena paling pragmatis, modular, konsisten dengan UI yang sudah ada di `index.blade.php`, dan menjaga paginasi tetap native.

---

## 5. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
|---|---|---|
| `app/Queries/Pembimbing/PenempatanAktifQuery.php` | Ubah | Menambahkan filter `individu` (mengecualikan penempatan jalur kelompok) pada `buildFiltered` saat `jenis_tugas` bernilai `individu` atau `semua`, serta helper scope/method agar reusable untuk menghitung total individu. |
| `app/Services/Pembimbing/TugasDashboardService.php` | Ubah | Menghitung metrik `filterCounts` (`semua`, `individu`, `kelompok`) berbasis entitas (peserta individu + kelompok bimbingan), menyaring `$allPenempatan` agar selaras dengan tab aktif, dan mengirimkannya ke view. |
| `resources/views/pembimbing/tugas/index.blade.php` | Ubah | Memperbarui angka pada pill filter (`Semua`, `Individu`, `Kelompok`) agar memakai `filterCounts`; memperbarui kondisi rendering panel utama agar mode `semua` menampilkan bagian **Kelompok** dan bagian **Individu**, mode `individu` hanya **Individu**, dan mode `kelompok` hanya **Kelompok**; menyesuaikan empty state dan visibilitas filter dropdown. |
| `tests/Feature/PembimbingTugasFilterAndProjectBersamaTest.php` | Ubah | Memperbarui & menambah skenario uji untuk memverifikasi isi dan angka counter pada tab `semua`, `individu`, dan `kelompok` (memastikan peserta kelompok tidak bocor ke daftar individu dan individu tidak muncul di tab kelompok). |

---

## 6. Langkah Implementasi
- [x] **Langkah 1 — Perbarui `PenempatanAktifQuery`**:
  - Tambahkan method/filter pada `app/Queries/Pembimbing/PenempatanAktifQuery.php` untuk menyaring `Penempatan` khusus jalur individu (tidak memiliki `pendaftaran` dengan `tipe = 'kelompok'` atau `kelompok_magang_id IS NOT NULL`), dan terapkan di `buildFiltered()` ketika `jenis_tugas` adalah `'individu'` atau `'semua'` (kecuali jika sedang dalam mode pencarian spesifik atau sesuai kebutuhan `semua`).
- [x] **Langkah 2 — Perbarui `TugasDashboardService`**:
  - Di `app/Services/Pembimbing/TugasDashboardService.php`, hitung jumlah entitas `individuCount` (jumlah penempatan individu aktif tanpa filter `q`/`penempatan_id`, atau konsisten dengan scope pembimbing) dan `kelompokCount` (`$kelompokListTotal->count()`), lalu kirim `$filterCounts = ['semua' => $individuCount + $kelompokCount, 'individu' => $individuCount, 'kelompok' => $kelompokCount]` ke view.
  - Pastikan ketika `$jenisTugas === 'kelompok'`, query `$penempatanList` tidak perlu memuat daftar individu di tampilan utama, dan pastikan pencarian `q` tetap bekerja baik untuk `kelompokList` maupun `penempatanList` pada mode `semua`.
- [x] **Langkah 3 — Perbarui View `resources/views/pembimbing/tugas/index.blade.php`**:
  - Ganti sumber angka pada pill filter:
    - `Semua ({{ $filterCounts['semua'] ?? 0 }})`
    - `Individu ({{ $filterCounts['individu'] ?? 0 }})`
    - `Kelompok ({{ $filterCounts['kelompok'] ?? 0 }} Kelompok)`
  - Pada bagian konten utama (di luar `$isMasukMode`):
    - Jika `jenisTugas === 'semua'`: tampilkan bagian **Daftar Kelompok Bimbingan** dan bagian **Daftar Peserta Individu**.
    - Jika `jenisTugas === 'individu'`: tampilkan hanya bagian **Daftar Peserta Individu**.
    - Jika `jenisTugas === 'kelompok'`: tampilkan hanya bagian **Daftar Kelompok Bimbingan**.
    - **Penyelarasan Desain Kelompok (Clean Card)**: Daftar kelompok tidak lagi menggunakan format tabel administratif kotak dengan banyak kolom, melainkan disesuaikan 100% menggunakan format kartu bersih (clean classroom card) persis seperti kartu peserta individu (avatar icon kelompok di kiri, nama kelompok & info ketua/institusi, badge jumlah peserta di kanan, serta chevron icon interaktif).
  - Pastikan tampilan tabel administratif (`#classroomTableContainer` / `#tableMenteesSummaryView`) tetap konsisten dengan `$penempatanList` yang sudah terfilter.
- [x] **Langkah 4 — Tambah & Jalankan Automated Tests**:
  - Perbarui `tests/Feature/PembimbingTugasFilterAndProjectBersamaTest.php` untuk skenario gabungan (ada peserta individu + ada kelompok dengan anggota) dan verifikasi ketiga tab (`semua`, `individu`, `kelompok`).
  - Jalankan `php artisan test --filter=PembimbingTugas` dan `./vendor/bin/pint --test` serta `./vendor/bin/phpstan analyse`.

---

## 7. Strategi Testing
- **Feature Test (PHPUnit)**:
  - `test_pembimbing_can_filter_tugas_by_semua_individu_and_kelompok`: menyiapkan minimal 1 peserta individu dan 1 kelompok (beserta anggotanya), lalu menguji:
    - `?jenis_tugas=semua`: melihat nama kelompok dan nama peserta individu, serta counter `Semua (2)`, `Individu (1)`, `Kelompok (1 Kelompok)`.
    - `?jenis_tugas=individu`: melihat nama peserta individu dan **tidak** melihat tabel kelompok maupun peserta jalur kelompok pada daftar individu.
    - `?jenis_tugas=kelompok`: melihat nama kelompok dan **tidak** melihat peserta individu.
  - Menjalankan test suite terkait (`PembimbingTugasFilterAndProjectBersamaTest`, `PembimbingFocusedMenteeTugasViewTest`, `PembimbingClassroomMenteeFirstTugasIndexTest`).
- **Static Analysis & Code Style**:
  - `./vendor/bin/pint --test`
  - `./vendor/bin/phpstan analyse --memory-limit=1G`

---

## 8. Risiko & Mitigasi
- **Risiko**: Peserta lama di database yang `pendaftaran_id`-nya `null` (misalnya hasil seeder atau penempatan manual admin).
  - **Mitigasi**: Gunakan `whereDoesntHave('pendaftaran', fn ($q) => $q->where('tipe', 'kelompok')->orWhereNotNull('kelompok_magang_id'))` agar penempatan tanpa `pendaftaran` tetap aman dianggap sebagai individu (selaras dengan `Penempatan::isKelompok()` yang mengembalikan `false` jika tidak ada pendaftaran kelompok).
- **Risiko**: Regresi pada test UI yang sudah ada (`PembimbingUIRegressionVerificationTest`, `PembimbingFocusedMenteeTugasViewTest`).
  - **Mitigasi**: Jalankan seluruh test fitur di namespace `Pembimbing` setelah implementasi.
- **Risiko**: Duplikasi kode query filter individu antara `PenempatanAktifQuery` dan `TugasDashboardService`.
  - **Mitigasi**: Pusatkan logika filter individu pada method reusable di `PenempatanAktifQuery` agar tidak ada duplikasi.

---

## 9. Kriteria Selesai (Definition of Done)
- [x] Tab `Semua` menampilkan daftar Kelompok dan daftar Peserta Individu secara lengkap, dengan counter `Semua (N)` sama dengan `Individu + Kelompok`.
- [x] Tab `Individu` hanya menampilkan peserta individu, tanpa peserta jalur kelompok.
- [x] Tab `Kelompok` hanya menampilkan daftar kelompok, tanpa peserta individu.
- [x] Seluruh test PHPUnit terkait lulus tanpa error.
- [x] `pint --test` dan `phpstan` lulus tanpa error baru.

---

## Saran Lanjutan (Di Luar Scope Saat Ini)
- Menambahkan badge kecil (`Individu` / `Kelompok`) pada dropdown `Filter Nama Peserta` jika suatu saat dibutuhkan filter lintas jalur pada halaman yang sama.

