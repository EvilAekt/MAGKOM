# Plan & PRD: Filter Semua, Individu, dan Tim pada Halaman Logbook Peserta (Pembimbing)

**Tanggal**: 25 November 2026  
**Disusun oleh**: Senior Developer  
**Target Implementer**: Junior Developer / AI Coding Agent (mis. Gemini 3.8 Flash)  
**Status**: Menunggu Persetujuan User (`Draft Plan`)

---

## 1. Tujuan & Konteks Bisnis (Ringkasan PRD)

Saat ini halaman **Tugas & Progres Magang** (`/pembimbing/tugas`) sudah memiliki pill filter **`Semua (N) | Individu (N) | Tim (N Tim)`**, di mana memilih sebuah Tim akan menampilkan daftar **Ketua Tim dan seluruh Anggota Tim** sebelum membuka rincian per peserta.  
Sementara itu, halaman **Logbook Peserta** (`/pembimbing/logbook`) masih menampilkan seluruh peserta bercampur rata tanpa pemisahan **Individu** vs **Tim**.

### Perilaku Target yang Harus Dicapai
1. **Pill Filter di `/pembimbing/logbook`** (selaras 100% dengan `/pembimbing/tugas`):
   - **`Semua (total)`**: Menampilkan kartu **Tim** dan kartu peserta **Individu** (non-tim).
   - **`Individu (N)`**: Hanya menampilkan peserta magang jalur individu (tidak termasuk anggota tim).
   - **`Tim (N Tim)`**: Hanya menampilkan daftar kartu **Tim** (binaan pembimbing).
2. **Drill-down Kartu Tim (`masuk_kelompok`)**:
   - Saat pembimbing mengklik salah satu kartu **Tim** (dari tab `Semua` atau `Tim`), halaman menampilkan daftar seluruh **Anggota Tim & Ketua Tim** beserta badge peran (`Ketua Tim` vs `Anggota`) dan ringkasan jumlah logbook masing-masing.
   - Saat pembimbing mengklik salah satu anggota tim (`masuk=<penempatan_id>&dari_kelompok=<kelompok_id>`), halaman membuka rincian **Logbook Mingguan** anggota tersebut untuk direview/diberi catatan, dan tombol kembali (`<`) mengarah kembali ke detail tim (`masuk_kelompok=<kelompok_id>`).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis
1. **Logbook Tetap Per Individu**: Berbeda dengan Tugas Tim, pengisian dan review **Logbook Mingguan** tetap dilakukan per peserta (setiap anggota tim punya logbook masing-masing). Pengelompokan **Tim** di `/pembimbing/logbook` berfungsi untuk memudahkan navigasi pembimbing melihat logbook seluruh anggota dalam satu tim.
2. **Penentuan Peserta Individu vs Tim**: Mengikuti pola `PenempatanAktifQuery` yang sudah ada di modul Tugas (`Penempatan` dianggap jalur individu bila `pendaftaran.kelompok_magang_id` bernilai `null`, dan jalur tim bila `pendaftaran.kelompok_magang_id` tidak `null`).
3. **Konsistensi UI**: Menggunakan gaya pill `.filter-pill` dan kartu `.mentee-classroom-card` yang sudah ada agar identik dengan `/pembimbing/tugas`.

---

## 3. Pendekatan Teknis & Evaluasi Opsi

### Opsi A: Duplikasi Seluruh Query Kelompok Baru di Controller Logbook
- **Kekurangan**: Menambah duplikasi kode di Controller, rawan inkonsistensi hitungan antara halaman Tugas dan halaman Logbook.

### Opsi B: Perluas `PenempatanLogbookQuery` Mengikuti Pola `TugasDashboardService` (Direkomendasikan)
- **Cara kerja**:
  - Perluas `app/Queries/Pembimbing/PenempatanLogbookQuery::getMenteeData()` agar menghitung:
    - `$allPenempatanIndividu` (penempatan aktif tanpa `kelompok_magang_id`) untuk daftar individu & filter dropdown.
    - `$kelompokList` (daftar `KelompokMagang` unik di bawah bimbingan pembimbing, mendukung pencarian `q` nama tim/ketua).
    - `$filterCounts` (`semua`, `individu`, `kelompok`).
    - `$jenisLogbook` (`semua` | `individu` | `kelompok`, default `semua`, otomatis `kelompok` jika `masuk_kelompok` atau `dari_kelompok` aktif).
    - Mode `masuk_kelompok`: `$masukKelompok`, `$masukKelompokMembers` (Ketua + seluruh Anggota), dan `$masukKelompokPenempatan` (lengkap dengan relasi `logbooks` masing-masing).
  - Update `resources/views/pembimbing/logbook/index.blade.php` & `resources/css/pages/pembimbing/logbook-index.css` untuk merender pill filter, kartu Tim, tampilan anggota tim (Ketua Tim + Anggota), dan tombol kembali kontekstual.
- **Alasan Pemilihan**: Mengikuti arsitektur existing (`Route -> Controller -> Query -> View`), *zero breaking changes* untuk test logbook yang sudah ada, dan tampilan seragam dengan `/pembimbing/tugas`.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Queries/Pembimbing/PenempatanLogbookQuery.php` | Modify | Pisahkan `penempatanList` khusus individu (saat bukan `masuk` / `masuk_kelompok`), ambil `$kelompokList`, `$filterCounts`, `$jenisLogbook`, serta data detail `$masukKelompok`, `$masukKelompokMembers`, dan `$masukKelompokPenempatan`. |
| `resources/views/pembimbing/logbook/index.blade.php` | Modify *(Perlu Izin Blade)* | Tambah bar pill `Semua (N) | Individu (N) | Tim (N Tim)` di atas filter card; tambah mode tampilan kartu Tim, mode `masuk_kelompok` (daftar Ketua Tim + Anggota beserta chip jumlah logbook), dan tombol kembali `dari_kelompok`. |
| `resources/css/pages/pembimbing/logbook-index.css` | Modify | Tambah styling `.logbook-jenis-filter-nav` dan `.filter-pill` (menyamakan dengan `tugas-index.css`) serta badge peran `Ketua Tim` / `Anggota` dan chip jumlah peserta. |
| `tests/Feature/PembimbingLogbookFilterTimTest.php` | Create | Feature test untuk filter `Semua`, `Individu`, `Tim`, drill-down `masuk_kelompok` (menampilkan Ketua Tim & Anggota), dan navigasi ke logbook anggota (`dari_kelompok`) serta anti-IDOR pembimbing lain. |

---

## 5. Langkah Implementasi (Urut Berdasarkan Dependensi)

### Tahap 1: Query Layer (`app/Queries/Pembimbing/PenempatanLogbookQuery.php`)
- [ ] **1.1 Eager load `pendaftaran.kelompokMagang` dan `anggota`** pada `$penempatanBaseQuery`:
  - `Penempatan::with(['peserta.profil.jurusan', 'bidang', 'pendaftaran', 'logbooks' => fn ($q) => $q->latest('tanggal')])`.
- [ ] **1.2 Pisahkan penempatan individu vs kelompok**:
  - `$individuBaseQuery`: `(clone $penempatanBaseQuery)->whereDoesntHave('pendaftaran', fn ($qp) => $qp->whereNotNull('kelompok_magang_id'))` (atau `whereHas('pendaftaran', fn ($qp) => $qp->whereNull('kelompok_magang_id'))` + `orWhereDoesntHave('pendaftaran')`).
  - Terapkan filter `penempatan_id` dan pencarian `q` pada `$individuBaseQuery` untuk menghasilkan `$penempatanList` (paginasi 10).
- [ ] **1.3 Ambil daftar `$kelompokList` binaan pembimbing**:
  - Kumpulkan `$kelompokIds` unik dari `(clone $penempatanBaseQuery)` yang memiliki `pendaftaran.kelompok_magang_id`.
  - Query `KelompokMagang::with(['ketua.profil.jurusan', 'anggota.user.profil.jurusan'])->withCount(['anggota'])->whereIn('id', $kelompokIds)`.
  - Jika `$request->filled('q')`, filter `nama_kelompok` atau `ketua.nama`. Jika `$request->filled('penempatan_id')`, kosongkan `$kelompokList`.
- [ ] **1.4 Hitung `$filterCounts` & `$jenisLogbook`**:
  - `$filterCounts = ['semua' => $individuCount + $kelompokTotalCount, 'individu' => $individuCount, 'kelompok' => $kelompokTotalCount]`.
  - Resolusi `$jenisLogbook = $request->input('jenis', 'semua')` (otomatis `'kelompok'` jika `masuk_kelompok` atau `dari_kelompok` terisi).
- [ ] **1.5 Siapkan data mode `masuk_kelompok`**:
  - Jika `$masukKelompokId` ada di `$kelompokIds`:
    - Load `$masukKelompok`, susun `$masukKelompokMembers` (Ketua + seluruh anggota unik), dan ambil `$masukKelompokPenempatan` (dengan relasi `logbooks` & `peserta.profil.jurusan`).

### Tahap 2: Frontend (`resources/views/pembimbing/logbook/index.blade.php` & `logbook-index.css`)
> ⚠️ **Catatan `AGENTS.md`**: Minta persetujuan user sebelum mengubah file `.blade.php`. Semua CSS wajib di `resources/css/pages/pembimbing/logbook-index.css`.

- [ ] **2.1 Edit `resources/views/pembimbing/logbook/index.blade.php`**:
  - Tambahkan `.logbook-jenis-filter-nav` di atas `#logbookFilterCard` berisi 3 pill:
    - `Semua ({{ $filterCounts['semua'] ?? 0 }})`
    - `Individu ({{ $filterCounts['individu'] ?? 0 }})`
    - `Tim ({{ $filterCounts['kelompok'] ?? 0 }} Tim)`
  - Tambahkan `<input type="hidden" name="jenis" value="{{ $jenisLogbook }}">` di form filter agar filter pencarian/bulan mempertahankan tab aktif.
  - Sembunyikan `#logbookFilterCard` saat `$isMasukMode || $isMasukKelompokMode`.
  - Pada `<x-slot:title>` & `<x-slot:actions>` panel utama:
    - Jika `$isMasukKelompokMode`: tampilkan nama tim, ketua, institusi, jumlah anggota, dan tombol kembali (`<`) ke daftar tim (`jenis=kelompok`).
    - Jika `$isMasukMode` & `request()->filled('dari_kelompok')`: tombol kembali (`<`) mengarah ke `masuk_kelompok={{ request('dari_kelompok') }}`.
  - Pada isi panel utama:
    - Jika `$isMasukKelompokMode`: tampilkan kartu untuk setiap `$mUser` di `$masukKelompokMembers` dengan badge **`Ketua Tim`** / **`Anggota`**, info institusi & jurusan, chip jumlah logbook (`N Entri`, `Disetujui`, `Menunggu`), dan link ke `masuk={{ $mPenempatan->id }}&dari_kelompok={{ $masukKelompok->id }}`.
    - Jika bukan `masuk` & bukan `masuk_kelompok`: tampilkan kartu Tim (`$kelompokList`) bila `$jenisLogbook` adalah `'semua'` atau `'kelompok'`, dan kartu Individu (`$penempatanList`) bila `'semua'` atau `'individu'`.
- [ ] **2.2 Edit `resources/css/pages/pembimbing/logbook-index.css`**:
  - Tambahkan rule `.logbook-jenis-filter-nav`, `.filter-pill`, `.filter-pill.active`, dan badge peran tim agar tampilannya konsisten dengan `/pembimbing/tugas`.

### Tahap 3: Testing & Quality Gate
- [ ] **3.1 Buat `tests/Feature/PembimbingLogbookFilterTimTest.php`**:
  - Uji filter `jenis=semua`, `jenis=individu`, dan `jenis=kelompok` (memastikan `Tim (1 Tim)` muncul dan individu tidak bercampur di tab `Tim`).
  - Uji `masuk_kelompok`: menampilkan nama tim, `Ketua Tim`, seluruh anggota tim, dan link ke logbook masing-masing anggota.
  - Uji anti-IDOR: pembimbing lain tidak bisa membuka `masuk_kelompok` milik pembimbing pertama.
- [ ] **3.2 Jalankan `php artisan test`, `./vendor/bin/pint --test`, `./vendor/bin/phpstan analyse`, dan `npm run build`**.

---

## 6. Strategi Testing

- Feature test `PembimbingLogbookFilterTimTest.php` + regresi `PesertaClassroomTugasLogbookTest.php` dan test logbook pembimbing yang sudah ada.
- Pastikan kompatibel dengan test existing (default `jenis = 'semua'` tetap menampilkan seluruh mentee individu maupun tim).

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Test existing `/pembimbing/logbook` mengharapkan anggota tim muncul langsung di list awal** | Assertion di test logbook lama bisa berubah jika sebelumnya menguji anggota tim sebagai kartu individu. | Audit seluruh test existing di `tests/Feature/*Logbook*` sebelum implementasi agar tidak ada regresi. |
| **N+1 Query pada `$masukKelompokMembers` & `logbooks`** | Halaman lambat saat tim besar. | Eager load `logbooks` pada `$masukKelompokPenempatan` dan `ketua`/`anggota.user` pada `KelompokMagang`. |

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Pill filter `Semua (N) | Individu (N) | Tim (N Tim)` tampil dan berfungsi di `/pembimbing/logbook`.
- [ ] Mengklik kartu **Tim** membuka daftar **Ketua Tim & Anggota Tim** beserta status ringkas logbook masing-masing.
- [ ] Mengklik salah satu anggota membuka logbook anggota tersebut dan tombol kembali mengarah kembali ke detail Tim.
- [ ] Seluruh test (`php artisan test`), `./vendor/bin/pint --test`, `./vendor/bin/phpstan analyse`, dan `npm run build` lulus tanpa error.
