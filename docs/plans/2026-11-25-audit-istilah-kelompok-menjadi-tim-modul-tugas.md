# Plan & PRD: Audit & Penyeragaman Istilah "Kelompok" Menjadi "Tim" pada Modul Tugas

**Tanggal**: 25 November 2026  
**Disusun oleh**: Senior Developer  
**Target Implementer**: Junior Developer / AI Coding Agent (mis. Gemini 3.8 Flash)  
**Status**: Menunggu Persetujuan User (`Draft Plan`)

---

## 1. Tujuan & Konteks Bisnis (Ringkasan PRD)

Pada modul **Tugas & Progres Magang**, sebagian tampilan sudah memakai istilah **Tim** (seperti `Tugas Tim`, `Buat Tugas Tim`, `Ketua Tim`), namun masih banyak teks antarmuka (UI) yang memakai kata **Kelompok** — misalnya pill filter **`Kelompok (1 Kelompok)`** di halaman utama pembimbing, judul panel **`Daftar Kelompok Bimbingan Magang`**, pencarian **`Pencarian Kelompok`**, serta pesan kosong **`Belum Ada Kelompok Magang Bimbingan`**.

### Aturan Utama (WAJIB DIPATUHI IMPLEMENTER)
- **UBAH**: Seluruh **teks tampilan UI** (*label, pill, heading, placeholder, empty state, tooltip `title`/`aria-label`, dan pesan validasi/flash user-facing*) pada modul Tugas Pembimbing & Peserta dari kata **"Kelompok"** menjadi **"Tim"**.
- **JANGAN UBAH (STRICTLY DO NOT TOUCH)**:
  - Nama tabel & kolom database (`kelompok_magang`, `kelompok_magang_id`, `nama_kelompok`, `tipe = 'kelompok'`).
  - Nama Model, Relasi, Enum, & Policy (`KelompokMagang`, `KelompokAnggota`, `kelompokMagang()`).
  - Nama route & parameter URL/query (`jenis_tugas=kelompok`, `masuk_kelompok`, `dari_kelompok`, `pembimbing.tugas.kelompok.show`).
  - Variabel PHP internal (`$kelompokList`, `$masukKelompok`, `$showKelompok`, dll.).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis
1. **Lingkup Modul**: Audit difokuskan pada modul **Tugas & Progres Magang** (Pembimbing & Peserta) beserta pesan validasi/flash yang terkait langsung dengan penugasan tim. Halaman administratif lain di luar modul tugas (mis. Pendaftaran Kelompok oleh Admin di `admin/penempatan` atau `admin/surat-selesai`) tidak disentuh agar scope tetap terarah (*Zero Scope Creep*).
2. **Halaman legacy `kelompok-detail.blade.php` & `kelompok-project-create.blade.php`**: Tetap diseragamkan label UI-nya ke **"Tim"** agar konsisten jika masih diakses melalui link lama atau test.

---

## 3. Pendekatan Teknis & Evaluasi Opsi

### Opsi A: Mengubah Copywriting UI + Parameter URL (`jenis_tugas=tim`, dll.)
- **Kekurangan**: Berisiko tinggi memutus link existing, bookmark, JavaScript, controller filter, dan puluhan assertion route di test suite tanpa nilai tambah fungsional.

### Opsi B: Mengubah Copywriting UI Saja, Kontrak Teknis Tetap (`kelompok`) — **Direkomendasikan**
- **Cara kerja**: Hanya mengganti string yang dilihat pengguna di Blade, pesan validasi/flash, dan assertion teks UI di Feature Test. Seluruh kontrak backend (`tipe = 'kelompok'`, `jenis_tugas=kelompok`, `masuk_kelompok`) tetap utuh 100%.
- **Alasan Pemilihan**: Zero breaking changes, cepat diverifikasi, aman untuk database & API existing.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/pembimbing/tugas/index.blade.php` | Modify *(Perlu Izin Blade)* | Ubah `Kelompok (N Kelompok)` -> `Tim (N Tim)`, `Daftar Kelompok Bimbingan Magang` -> `Daftar Tim Bimbingan Magang`, `Pencarian Kelompok` -> `Pencarian Tim`, dan seluruh label/empty state/tooltip UI dari `Kelompok` ke `Tim`. |
| `resources/views/pembimbing/tugas/create.blade.php` | Modify *(Perlu Izin Blade)* | Ubah placeholder/teks `Pilih tim / kelompok` -> `Pilih tim`, serta pesan kosong `tim/kelompok` -> `tim`. |
| `resources/views/pembimbing/tugas/kelompok-detail.blade.php` | Modify *(Perlu Izin Blade)* | Ubah label UI `KELOMPOK BIMBINGAN MAGANG` -> `TIM BIMBINGAN MAGANG`, `Kembali ke Daftar Kelompok` -> `Kembali ke Daftar Tim`, `PROJECT KELOMPOK` -> `PROJECT TIM`. |
| `resources/views/pembimbing/tugas/kelompok-project-create.blade.php` | Modify *(Perlu Izin Blade)* | Ubah label UI `PENUGASAN PROJECT KELOMPOK` -> `PENUGASAN PROJECT TIM`, `Kembali ke Kelompok` -> `Kembali ke Tim`, `Anggota Kelompok` -> `Anggota Tim`. |
| `app/Http/Requests/TugasRequest.php` | Modify | Seragamkan pesan validasi `kelompok_magang_ids` dari `tim/kelompok` menjadi `tim` saja. |
| `app/Http/Controllers/Pembimbing/TugasController.php` | Modify | Seragamkan pesan error `Tim/kelompok bimbingan tidak ditemukan.` -> `Tim bimbingan tidak ditemukan.` |
| `tests/Feature/PembimbingTugasFilterAndProjectBersamaTest.php` | Modify | Perbarui assertion teks UI lama (`Kelompok (1 Kelompok)` -> `Tim (1 Tim)`, dst.) agar selaras dengan copywriting baru. |

---

## 5. Langkah Implementasi (Checklist Terperinci)

### Tahap 1: Pembaruan Blade UI (`resources/views/pembimbing/tugas/`)
> ⚠️ **Catatan `AGENTS.md`**: Minta persetujuan user sebelum mengubah file `.blade.php`. Jangan menambahkan CSS/JS inline di Blade.

- [ ] **1.1 Edit `resources/views/pembimbing/tugas/index.blade.php`** (ubah teks UI saja, biarkan parameter URL & variabel PHP tetap):
  - Baris 153: `Kelompok ({{ $filterCounts['kelompok'] ?? $kelompokList->count() }} Kelompok)` $\rightarrow$ `Tim ({{ $filterCounts['kelompok'] ?? $kelompokList->count() }} Tim)`
  - Baris 177: `'Pencarian Kelompok'` $\rightarrow$ `'Pencarian Tim'`
  - Baris 179: `'Cari nama kelompok, nama ketua...'` $\rightarrow$ `'Cari nama tim, nama ketua...'`
  - Baris 250-251: `'Kelompok Magang'` $\rightarrow$ `'Tim Magang'`, `'Ketua Kelompok'` $\rightarrow$ `'Ketua Tim'`
  - Baris 271: `Daftar Kelompok Bimbingan Magang` $\rightarrow$ `Daftar Tim Bimbingan Magang`
  - Baris 285-286: `Kembali ke Detail Kelompok` $\rightarrow$ `Kembali ke Detail Tim`
  - Baris 315-316: `Kembali ke Daftar Kelompok` $\rightarrow$ `Kembali ke Daftar Tim`
  - Baris 572: `Belum ada anggota yang terdaftar pada kelompok ini.` $\rightarrow$ `Belum ada anggota yang terdaftar pada tim ini.`
  - Baris 589-590: `'Ketua Kelompok'` $\rightarrow$ `'Ketua Tim'`, `'Kelompok #'` $\rightarrow$ `'Tim #'`
  - Baris 600: `title="Masuk ke kelompok {{ $kNamaKelompok }}"` $\rightarrow$ `title="Masuk ke tim {{ $kNamaKelompok }}"`
  - Baris 726: `Belum Ada Kelompok Magang Bimbingan` $\rightarrow$ `Belum Ada Tim Magang Bimbingan`
  - Baris 730: `Belum Ada Mentee atau Kelompok Bimbingan` $\rightarrow$ `Belum Ada Mentee atau Tim Bimbingan`
  - Baris 735: `Saat ini belum ada peserta magang jalur kelompok yang ditempatkan pada bimbingan Anda.` $\rightarrow$ `Saat ini belum ada peserta magang jalur tim yang ditempatkan pada bimbingan Anda.`
  - Baris 739: `Saat ini belum ada peserta magang maupun kelompok yang dialokasikan dalam bimbingan Anda.` $\rightarrow$ `Saat ini belum ada peserta magang maupun tim yang dialokasikan dalam bimbingan Anda.`

- [ ] **1.2 Edit `resources/views/pembimbing/tugas/create.blade.php`**:
  - Baris 277: `Pilih tim / kelompok` $\rightarrow$ `Pilih tim`
  - Baris 456: `Tidak ada tim/kelompok magang aktif dalam kewenangan Anda saat ini.` $\rightarrow$ `Tidak ada tim magang aktif dalam kewenangan Anda saat ini.`

- [ ] **1.3 Edit `resources/views/pembimbing/tugas/kelompok-detail.blade.php`**:
  - Baris 18: `KELOMPOK BIMBINGAN MAGANG` $\rightarrow$ `TIM BIMBINGAN MAGANG`
  - Baris 32: `Kembali ke Daftar Kelompok` $\rightarrow$ `Kembali ke Daftar Tim`
  - Baris 88: `PROJECT KELOMPOK` $\rightarrow$ `PROJECT TIM`

- [ ] **1.4 Edit `resources/views/pembimbing/tugas/kelompok-project-create.blade.php`**:
  - Baris 4: `@section('topbar_title', 'Buat Project Bersama Kelompok')` $\rightarrow$ `@section('topbar_title', 'Buat Project Bersama Tim')`
  - Baris 18: `PENUGASAN PROJECT KELOMPOK` $\rightarrow$ `PENUGASAN PROJECT TIM`
  - Baris 32: `Kembali ke Kelompok` $\rightarrow$ `Kembali ke Tim`
  - Baris 146 & 150: `Anggota Kelompok` $\rightarrow$ `Anggota Tim`, `dari kelompok ini` $\rightarrow$ `dari tim ini`

### Tahap 2: Pesan Validasi & Controller
- [ ] **2.1 Edit `app/Http/Requests/TugasRequest.php`**:
  - Baris 186-189: Ubah frasa `tim/kelompok` menjadi `tim` (mis. `'Minimal satu tim magang harus dipilih.'`).
- [ ] **2.2 Edit `app/Http/Controllers/Pembimbing/TugasController.php`**:
  - Baris 128 & 132: Ubah `'Tim/kelompok bimbingan tidak ditemukan.'` $\rightarrow$ `'Tim bimbingan tidak ditemukan.'` dan `'Minimal satu tim/kelompok magang harus dipilih.'` $\rightarrow$ `'Minimal satu tim magang harus dipilih.'`

### Tahap 3: Pembaruan Assertion Test
- [ ] **3.1 Edit `tests/Feature/PembimbingTugasFilterAndProjectBersamaTest.php`**:
  - Baris 224: `$responseSemua->assertSee('Kelompok (1 Kelompok)');` $\rightarrow$ `$responseSemua->assertSee('Tim (1 Tim)');`
  - Baris 270: `$responseTugasKetua->assertSee('Kembali ke Detail Kelompok');` $\rightarrow$ `$responseTugasKetua->assertSee('Kembali ke Detail Tim');`
  - Cek seluruh assertion lain di file tersebut (mis. `KELOMPOK BIMBINGAN MAGANG` jika di-assert di `test_pembimbing_can_view_kelompok_detail_and_tabs`) agar ikut disesuaikan ke `Tim`.

---

## 6. Strategi Testing

Jalankan seluruh suite test modul tugas dengan PHP 8.3 aktual (`C:\Users\galan\php83\php.exe`):
- `C:\Users\galan\php83\php.exe artisan test tests/Feature/TugasTimDanIndividuTest.php tests/Feature/PembimbingTugasFilterAndProjectBersamaTest.php tests/Feature/PembimbingClassroomMenteeFirstTugasIndexTest.php tests/Feature/PembimbingFocusedMenteeTugasViewTest.php tests/Feature/PesertaTugasFilterTabsTest.php`
- Pastikan tidak ada lagi kata `"Kelompok"` pada output HTML user-facing halaman `/pembimbing/tugas` dan `/peserta/tugas`.

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Salah mengganti `jenis_tugas=kelompok` atau `tipe='kelompok'`** | Filter & database query tidak mengembalikan data tugas tim. | Hanya ganti teks di dalam tag HTML/string terjemahan, jangan pernah melakukan *Find & Replace All* buta. |
| **Test existing gagal karena masih mencari teks `"Kelompok"`** | CI/CD merah. | Update assertion teks UI di `PembimbingTugasFilterAndProjectBersamaTest.php` dan verifikasi semua test tugas hijau. |

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Pill filter utama menampilkan `Tim (N Tim)` menggantikan `Kelompok (N Kelompok)`.
- [ ] Seluruh heading, placeholder, empty state, tooltip, dan pesan validasi pada modul Tugas menggunakan istilah **Tim** secara konsisten.
- [ ] Tidak ada perubahan pada nama tabel, kolom DB, enum value `'kelompok'`, maupun parameter URL.
- [ ] Seluruh test modul tugas (`php artisan test`) dan `./vendor/bin/pint --test` lulus tanpa error.
