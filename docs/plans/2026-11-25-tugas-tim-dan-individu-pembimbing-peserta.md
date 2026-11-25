# Plan & PRD: Pemisahan Tugas Tim dan Tugas Individu (Pembimbing, Peserta, dan Review per Tim)

**Tanggal**: 25 November 2026  
**Disusun oleh**: Senior Developer  
**Target Implementer**: Junior Developer / AI Coding Agent (mis. Gemini 3.8 Flash)  
**Status**: Menunggu Persetujuan User (`Draft Plan`)

---

## 1. Tujuan & Konteks Bisnis (Ringkasan PRD)

Saat ini di modul **Tugas & Progres Magang**, tugas untuk kelompok masih terikat pada penempatan **Ketua Tim** (misalnya tombol **Tugas Kelompok** di detail kelompok mengarah ke `masuk=$ketuaPenempatan->id`, dan form **Buat Tugas Baru** hanya memilih peserta individu per `penempatan_id`). Akibatnya:
- Tugas kelompok terkesan dibebankan ke Ketua Tim saja.
- Pembimbing belum bisa membuat **Tugas Tim** langsung ke nama tim (untuk semua anggota tim) dari alur utama **Buat Tugas Baru** maupun melihat **Daftar Tugas Tim** secara terpisah dari **Tugas Individu** anggota.
- Peserta belum memiliki filter/pilihan untuk memisahkan **Tugas Tim** dan **Tugas Individu** di dashboard tugasnya.
- Halaman **Review Tugas** (`pembimbing.tugas.show`) masih berorientasi individu (menampilkan nama satu peserta, bukan identitas Tim).

### Perilaku Target yang Harus Dicapai
1. **Dua Jenis Tugas yang Jelas**:
   - **Tugas Tim (`tipe = 'kelompok'`)**: Diberikan kepada **Nama Tim (`kelompok_magang_id`)**, bukan kepada Ketua Tim. Semua anggota aktif di tim tersebut (ketua + seluruh anggota) **otomatis dapat melihat** tugas tim ini di dashboard mereka.
   - **Tugas Individu (`tipe = 'individu'`)**: Diberikan kepada **Peserta spesifik (`penempatan_id`)**. Misalnya dalam 1 tim berisi 4 orang, 1 orang bisa diberi tugas individu khusus (project berbeda), dan peserta tersebut **tetap bisa melihat Tugas Tim** sekaligus **Tugas Individu** miliknya.
2. **Dashboard Pembimbing (`/pembimbing/tugas`)**:
   - Saat membuka tampilan sebuah **Tim/Kelompok** (`masuk_kelompok`), tersedia dua tab/pilihan yang jelas:
     - **Tugas Tim**: Menampilkan seluruh tugas tim (`tipe = 'kelompok'`) milik tim tersebut + tombol buat tugas untuk tim ini.
     - **Anggota & Tugas Individu**: Menampilkan daftar anggota tim beserta jumlah/akses ke tugas individu masing-masing anggota.
   - Tombol **"Tugas Kelompok"** di header panel kelompok tidak lagi mengarah ke penugasan Ketua (`masuk=$ketuaPenempatan->id`), melainkan langsung ke tampilan **Tugas Tim** milik kelompok tersebut.
   - Halaman **Buat Tugas Baru (`/pembimbing/tugas/create`)** memiliki pemilih jenis penugasan: **Tugas Individu** (memilih satu/banyak peserta) atau **Tugas Tim** (memilih satu/banyak Tim berdasarkan **Nama Tim**).
3. **Dashboard Peserta (`/peserta/tugas`)**:
   - Peserta melihat **Tugas Tim** (dari tim tempat ia tergabung) DAN **Tugas Individu** (yang ditujukan khusus ke dirinya).
   - Tersedia **filter/tab pilihan** antara `Semua`, `Tugas Tim`, dan `Tugas Individu` (selain filter status `Semua Status / Selesai / Lewat Tenggat`), serta badge penanda yang jelas pada setiap kartu tugas (`Tugas Tim` vs `Tugas Individu`).
4. **Halaman Review Tugas Pembimbing (`/pembimbing/tugas/{tugas}`)**:
   - Untuk tugas bertipe `kelompok`, review dilakukan **per Tim** (bukan per individu):
     - Header/identitas menampilkan **Nama Tim**, badge **Tugas Tim**, serta daftar anggota tim.
     - Riwayat progres (`progres_log`) menampilkan **nama anggota pengirim** (siapa yang mengunggah progres dari tim tersebut).
     - Penilaian/review (`Terima Progres`, `Minta Revisi`, `Tandai Selesai`) berlaku untuk satu entitas tugas tim tersebut.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis
1. **Skema existing dipertahankan**: Tabel `tugas` sudah memiliki kolom `tipe` (`'individu' | 'kelompok'`), `kelompok_magang_id`, dan tabel pivot `tugas_peserta`. Kita **tidak** membuat tabel tugas baru.
2. **Keanggotaan Tim**: Anggota tim ditentukan dari `kelompok_magang.ketua_user_id` + `kelompok_anggota.user_id` yang memiliki penempatan aktif/relevan di bimbingan pembimbing terkait.
3. **Pengumpulan Progres Tugas Tim**: Karena tugas tim adalah satu pekerjaan bersama tim, **siapa pun anggota tim** dapat mengunggah progres (`ProgresLog`), dan saat satu anggota sudah mengirim progres (status `menunggu`), anggota lain melihat status yang sama (menunggu review / perlu revisi / selesai) agar tidak terjadi duplikasi pengiriman.
4. **Backward Compatibility**: Data tugas lama (`tipe = 'kelompok'` yang dibuat lewat fitur lama *Project Bersama*) tetap kompatibel karena menggunakan kolom yang sama (`tipe = 'kelompok'` + `kelompok_magang_id` + `tugas_peserta`).

### Pertanyaan Terbuka (Opsional untuk Konfirmasi User)
- Saat pembimbing membuat **Tugas Tim** untuk sebuah tim, apakah default-nya **otomatis mencakup seluruh anggota tim** (ketua + semua anggota), atau pembimbing juga boleh mencentang sebagian anggota saja? *(Asumsi default di plan ini: otomatis seluruh anggota tim terpilih saat memilih Nama Tim, dengan opsi opsional melihat daftar anggotanya).*

---

## 3. Pendekatan Teknis & Evaluasi Opsi Arsitektur

### Opsi A: Duplikasi Row `tugas` per Anggota Tim (Tidak Direkomendasikan)
- **Cara kerja**: Saat membuat tugas tim untuk 4 anggota, sistem membuat 4 baris di tabel `tugas` dengan `batch_id` sama.
- **Kekurangan**: Review menjadi per individu (4 kali review untuk 1 pekerjaan tim), progres tidak tersinkron antar anggota, dan melanggar kebutuhan *"review by tim bukan individu"*.

### Opsi B: Satu Row `tugas` per Tugas Tim + Sinkronisasi `tugas_peserta` (Direkomendasikan)
- **Cara kerja**:
  - 1 tugas tim = **1 baris** di tabel `tugas` dengan `tipe = 'kelompok'`, `kelompok_magang_id = <id_tim>`, dan `penempatan_id = null` (atau nullable/opsional untuk representasi bidang, tidak lagi mengikat ke ketua).
  - Seluruh anggota aktif tim dicatat di `tugas_peserta` (`tugas_id`, `user_id`, `penempatan_id`) secara otomatis saat tugas tim dibuat, **dan** query peserta juga mengecek keanggotaan `kelompok_magang_id` sebagai fallback aman jika ada anggota baru/susulan.
  - Tabel `progres_log` ditambah kolom `user_id` (nullable FK ke `users.id`) untuk mencatat anggota tim mana yang mengunggah laporan progres.
- **Alasan Pemilihan**:
  - Memanfaatkan skema database yang **sudah ada** (`2026_11_23_000001_upgrade_kelompok_tugas_and_penempatan_tables.php`).
  - Review pembimbing otomatis bersifat **1-untuk-tim** (sekali review, seluruh anggota melihat hasil review yang sama).
  - Tugas individu tetap 100% terpisah (`tipe = 'individu'`, `penempatan_id = <id_peserta>`).

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `database/migrations/2026_11_25_000001_add_user_id_to_progres_log_and_nullable_penempatan_on_tugas.php` | Create | Tambah `user_id` nullable FK (`users.id`) ke `progres_log` untuk mencatat pengirim progres tim; pastikan `tugas.penempatan_id` mendukung `null` untuk tugas tim murni. |
| `app/Models/ProgresLog.php` | Modify | Tambah `'user_id'` ke `$fillable` dan relasi `pelapor(): BelongsTo` ke `User`. |
| `app/Models/Tugas.php` | Modify | Tambah helper `isKelompok(): bool` dan `isIndividu(): bool` agar Blade/Controller/Policy konsisten tanpa string comparison berulang. |
| `app/Policies/TugasPolicy.php` | Modify | Perbaiki `view()`, `update()`, `reviewProgres()`, dan `submitProgres()` agar tugas `tipe = 'kelompok'` mengecek kewenangan pembimbing via `kelompokMagang` (bukan hanya `$tugas->penempatan`), dan peserta dicek via `tugasPeserta` atau keanggotaan `kelompokMagang`. |
| `app/Http/Requests/TugasRequest.php` | Modify | Dukung input `tipe` (`individu` \| `kelompok`) dan `kelompok_magang_ids[]` (wajib jika `tipe = kelompok`, menggantikan wajibnya `penempatan_ids[]` saat mode tim), serta validasi anti-IDOR kepemilikan kelompok oleh pembimbing & batas `deadline` terhadap `tgl_selesai` anggota tim. |
| `app/Actions/Pembimbing/Tugas/CreateBatchTugasAction.php` | Modify | Tambah jalur pembuatan Tugas Tim (`executeForKelompok` atau cabang di `execute`): 1 record `Tugas` per `KelompokMagang` dengan `tipe = 'kelompok'`, `kelompok_magang_id`, isi `tugas_peserta` untuk seluruh anggota aktif tim, audit log, dan kirim notifikasi SSE ke semua anggota tim. |
| `app/Queries/Pembimbing/TugasIndexQuery.php` | Modify | Pastikan filter `masuk_kelompok` / `kelompok_id` dan `jenis_tugas` mengembalikan tugas tim (`tipe = 'kelompok'`) dengan benar tanpa bergantung pada `penempatan_id` ketua. |
| `app/Services/Pembimbing/TugasDashboardService.php` | Modify | Pada mode `masuk_kelompok`, muat `$masukKelompokTugasTim` (daftar tugas `tipe = 'kelompok'` milik tim) dan dukung parameter sub-tab `tab_tim` (`tugas_tim` \| `individu_anggota`), serta hitung metrik tugas tim secara akurat. |
| `app/Http/Controllers/Pembimbing/TugasController.php` | Modify | Update `create()` (kirim daftar `kelompokList` aktif binaan pembimbing + preselected `kelompok_id`/`tipe`), update `store()` (tangani `tipe === 'kelompok'`), update `show()` (load `kelompokMagang.ketua`, `kelompokMagang.anggota.user`, `pesertaDitugaskan`, `progresLogs.pelapor`, dan sibling list tugas tim jika `tipe === 'kelompok'`). |
| `app/Http/Controllers/Peserta/TugasController.php` | Modify | Update `index()`: dukung filter `tipe` (`semua` \| `kelompok` \| `individu`), pastikan query mengambil tugas `individu` milik peserta DAN tugas `kelompok` di mana peserta tergabung (via `tugasPeserta` atau keanggotaan `kelompokMagang`), serta hitung counter per tipe (`totalTim`, `totalIndividu`). Update `unggahProgres()`: simpan `user_id => $user->id` pada `ProgresLog::create(...)`. |
| `resources/views/pembimbing/tugas/index.blade.php` | Modify *(Perlu Izin Blade)* | Pada mode `masuk_kelompok`: ubah tombol **"Tugas Kelompok"** agar mengarah ke tab/tampilan **Tugas Tim** (bukan `masuk=$ketuaPenempatan->id`), dan tambahkan switcher tab **Tugas Tim** vs **Tugas Individu Anggota** beserta daftar kartu tugas tim + tombol cepat `+ Buat Tugas Tim`. |
| `resources/views/pembimbing/tugas/create.blade.php` | Modify *(Perlu Izin Blade)* | Tambah selector **Jenis Tugas (`Tugas Individu` / `Tugas Tim`)** di panel kanan ("Untuk"). Jika memilih `Tugas Tim`, tampilkan pemilih **Nama Tim** (`kelompok_magang_ids[]`); jika `Tugas Individu`, tampilkan pemilih **Peserta** (`penempatan_ids[]`). |
| `resources/views/pembimbing/tugas/show.blade.php` | Modify *(Perlu Izin Blade)* | Jika `$tugas->isKelompok()`, tampilkan **Nama Tim**, badge **Tugas Tim**, daftar anggota tim di header/sidebar, serta tampilkan **nama pengirim (`$log->pelapor?->nama`)** pada setiap log progres. |
| `resources/views/peserta/tugas/index.blade.php` | Modify *(Perlu Izin Blade)* | Tambah pill filter jenis tugas (`Semua`, `Tugas Tim`, `Tugas Individu`) dan tampilkan badge `Tugas Tim` / `Tugas Individu` di setiap baris tugas. |
| `resources/views/peserta/tugas/show.blade.php` | Modify *(Perlu Izin Blade)* | Jika `$tugas->isKelompok()`, tampilkan badge **Tugas Tim** dan nama tim (`$tugas->kelompokMagang?->nama_kelompok`) serta info anggota tim pada metadata tugas. |
| `resources/css/pages/pembimbing/tugas-index.css` & `tugas-create.css` & `tugas-review.css` | Modify | Styling tab Tugas Tim vs Anggota, pemilih Tim di form buat tugas, dan badge/identitas Tim di halaman review. |
| `resources/css/pages/peserta/tugas-index.css` | Modify | Styling pill filter `Tugas Tim` / `Tugas Individu` dan badge tipe tugas pada list tugas peserta. |
| `resources/js/pages/pembimbing/tugas-create.js` | Modify | Logic toggle mode `Tugas Individu` vs `Tugas Tim`, modal/dropdown pemilih Tim, sinkronisasi input hidden `kelompok_magang_ids[]` & `tipe`. |
| `resources/js/pages/peserta/tugas-index.js` | Modify | Logic filter kombinasi antara tab status (`Semua`, `Selesai`, `Lewat Tenggat`) dan tab jenis (`Semua`, `Tugas Tim`, `Tugas Individu`). |
| `tests/Feature/TugasTimDanIndividuTest.php` | Create | Feature test menyeluruh untuk pembuatan tugas tim, visibilitas semua anggota, tugas individu paralel untuk anggota tim, filter peserta, dan review tugas per tim. |

> **Catatan Aturan Proyek (`AGENTS.md`)**: Sesuai aturan repositori, perubahan pada file `.blade.php` memerlukan persetujuan user terlebih dahulu dan **tidak boleh** mengandung tag `<style>`, `<script>`, atau inline event handler (`onclick`, dll.). Semua CSS wajib di `resources/css/` dan semua JS di `resources/js/` (ES Modules) lalu dikompilasi via Vite.

---

## 5. Langkah Implementasi (Urut Berdasarkan Dependensi)

### Tahap 1: Database & Model
- [ ] **1.1 Buat Migration baru** (`database/migrations/2026_11_25_000001_add_user_id_to_progres_log_and_nullable_penempatan_on_tugas.php`):
  - **⚠️ PERHATIAN MIGRASI**: Pastikan aman untuk SQLite (testing) maupun MySQL (dev/prod).
  - Pada tabel `progres_log`: tambah kolom `user_id` (`$table->foreignId('user_id')->nullable()->after('tugas_id')->constrained('users')->nullOnDelete()`).
  - Pada tabel `tugas`: cek apakah `penempatan_id` sudah nullable; jika belum, ubah menjadi `nullable()` (`$table->unsignedBigInteger('penempatan_id')->nullable()->change()`). *(Cek dulu migrasi `2026_10_08_000009_create_tugas_table.php` sebelum menulis `change()`).*
- [ ] **1.2 Update `app/Models/ProgresLog.php`**:
  - Tambahkan `'user_id'` ke array `$fillable`.
  - Tambahkan relasi `public function pelapor(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }`.
- [ ] **1.3 Update `app/Models/Tugas.php`**:
  - Tambahkan method `isKelompok(): bool` (`return $this->tipe === 'kelompok';`) dan `isIndividu(): bool` (`return $this->tipe !== 'kelompok';`).

### Tahap 2: Authorization (`TugasPolicy`) & Validation (`TugasRequest`)
- [ ] **2.1 Update `app/Policies/TugasPolicy.php`**:
  - Buat helper internal `pembimbingOwnsKelompok(User $user, KelompokMagang $kelompok): bool` agar tidak duplikasi query di `view()`, `update()`, dan `reviewProgres()`.
  - Pada `view()`: jika `$tugas->isKelompok()`, peserta boleh melihat jika `status_publikasi === 'dipublikasikan'` DAN (`$tugas->tugasPeserta()->where('user_id', $user->id)->exists()` ATAU user adalah ketua/anggota dari `$tugas->kelompokMagang`). Pembimbing boleh melihat jika `pembimbingOwnsKelompok(...)`.
  - Pada `update()` dan `reviewProgres()`: jika `$tugas->isKelompok()`, gunakan `pembimbingOwnsKelompok(...)` (jangan hanya mengandalkan `$tugas->penempatan` yang bisa bernilai `null`!).
  - Pada `submitProgres()`: jika `$tugas->isKelompok()`, pastikan peserta berstatus penempatan `Aktif`, `status_publikasi === 'dipublikasikan'`, dan merupakan anggota tim tersebut (`tugasPeserta` atau `kelompokMagang`).
  - Pastikan `SuperAdmin` tetap `false` pada semua aksi mutasi (`create`, `update`, `reviewProgres`).
- [ ] **2.2 Update `app/Http/Requests/TugasRequest.php`**:
  - Tambah field validasi:
    - `'tipe' => ['nullable', 'in:individu,kelompok']` (default `'individu'` di `prepareForValidation()`).
    - Jika `tipe === 'kelompok'` (saat create): `'kelompok_magang_ids' => ['required', 'array', 'min:1']`, `'kelompok_magang_ids.*' => ['integer', 'distinct', 'exists:kelompok_magang,id']`, sedangkan `'penempatan_ids'` menjadi `nullable`.
    - Jika `tipe === 'individu'` (saat create): pertahankan aturan existing (`'penempatan_ids' => ['required', 'array', 'min:1']`).
  - Di `withValidator()`: untuk `kelompok_magang_ids`, lakukan validasi **anti-IDOR** bahwa pembimbing memang membimbing kelompok tersebut, dan validasi `deadline` tidak melebihi `tgl_selesai` penempatan aktif anggota kelompok tersebut.

### Tahap 3: Backend Logic (Action, Query, Service, Controller)
- [ ] **3.1 Update `app/Actions/Pembimbing/Tugas/CreateBatchTugasAction.php`**:
  - Buat method `executeForKelompok(Collection|array $kelompokList, array $validated, ?UploadedFile $fileLampiran, User $user): array` (atau tangani cabang `tipe === 'kelompok'`):
    - Untuk setiap `KelompokMagang`:
      - Ambil seluruh anggota tim (`ketua_user_id` + `anggota.user_id`) yang memiliki `Penempatan` berstatus `Aktif`.
      - Buat **1 record `Tugas`** dengan `'tipe' => 'kelompok'`, `'kelompok_magang_id' => $kelompok->id`, `'penempatan_id' => null` (atau null agar tidak terikat ketua).
      - Simpan seluruh anggota aktif ke tabel pivot `tugas_peserta` (`tugas_id`, `user_id`, `penempatan_id`).
      - Catat `AuditService::catat(...)` dengan detail `['aksi' => 'buat_tugas_tim', 'kelompok_magang_id' => $kelompok->id]`.
    - Setelah commit (`DB::afterCommit`), kirim notifikasi `RealtimeEventTypeEnum::TugasBaru` ke **semua anggota tim** (`user_id` masing-masing anggota).
- [ ] **3.2 Update `app/Queries/Pembimbing/TugasIndexQuery.php`**:
  - Tambah dukungan filter `masuk_kelompok` (filter `where('tipe', 'kelompok')->where('kelompok_magang_id', $kelompokId)`).
  - Eager load `progresLogs.pelapor` dan `kelompokMagang.anggota.user` bila diperlukan untuk menghindari N+1 query.
- [ ] **3.3 Update `app/Services/Pembimbing/TugasDashboardService.php`**:
  - Saat `$masukKelompokId` aktif:
    - Ambil `$masukKelompokTugasTim`: semua `Tugas::where('tipe', 'kelompok')->where('kelompok_magang_id', $masukKelompokId)->with(['progresLogs.pelapor', 'pesertaDitugaskan'])->latest()->get()`.
    - Tentukan `$tabTim = $request->input('tab_tim', 'tugas_tim')` (`'tugas_tim'` atau `'anggota'`).
    - Pastikan pada `$kelompokList` (halaman daftar kelompok), tersedia jumlah tugas tim masing-masing kelompok (`tugas_tim_count`) agar kartu kelompok langsung informatif.
- [ ] **3.4 Update `app/Http/Controllers/Pembimbing/TugasController.php`**:
  - Di `create(Request $request)`:
    - Ambil daftar `$kelompokList` yang dibimbing oleh `$user` (hanya kelompok yang punya penempatan aktif).
    - Baca query parameter `?tipe=kelompok&kelompok_id=...` agar tombol **"+ Buat Tugas Tim"** dari halaman detail tim langsung membuka form dalam mode **Tugas Tim** dengan tim tersebut sudah terpilih.
  - Di `store(TugasRequest $request)`:
    - Jika `$request->input('tipe') === 'kelompok'`, ambil `KelompokMagang::with(['ketua', 'anggota.user'])->whereIn('id', $request->input('kelompok_magang_ids', []))->get()` lalu panggil `$this->createBatchAction->executeForKelompok(...)`.
    - Jika `'individu'`, jalankan alur existing `$this->createBatchAction->execute(...)`.
  - Di `show(Request $request, Tugas $tugas)`:
    - Eager load tambahan: `'kelompokMagang.ketua'`, `'kelompokMagang.anggota.user'`, `'pesertaDitugaskan'`, `'progresLogs.pelapor'`.
    - Jika `$tugas->isKelompok()`, `$siblingTugasList` mengambil daftar tugas tim lain milik kelompok yang sama (`where('tipe', 'kelompok')->where('kelompok_magang_id', $tugas->kelompok_magang_id)`).
- [ ] **3.5 Update `app/Http/Controllers/Peserta/TugasController.php`**:
  - Di `index(Request $request)`:
    - Pada query tugas `kelompok`, selain `whereHas('tugasPeserta', ...)`, pastikan juga mencakup tugas tim dari `kelompok_magang_id` tempat user terdaftar sebagai ketua/anggota aktif (agar konsisten walaupun ada sinkronisasi susulan).
    - Eager load `'kelompokMagang'` dan `'progresLogs.pelapor'`.
    - Hitung `$totalTim` dan `$totalIndividu` dari `$allTugas` untuk ditampilkan di badge filter UI Peserta.
  - Di `unggahProgres(ProgresLogRequest $request, Tugas $tugas)`:
    - Tambahkan `'user_id' => $request->user()->id` saat membuat `ProgresLog` baru agar tercatat siapa anggota tim yang mengunggah progres.

### Tahap 4: Frontend (Blade Views, CSS di `resources/css/`, JS di `resources/js/`)
> ⚠️ **PENTING UNTUK IMPLEMENTER**: Minta persetujuan user sebelum mengedit file `.blade.php`. Dilarang menulis `<style>`, `<script>`, atau `onclick=` di file Blade!

- [ ] **4.1 Pembimbing — `resources/views/pembimbing/tugas/index.blade.php` + `resources/css/pages/pembimbing/tugas-index.css`**:
  - Pada header mode `masuk_kelompok`: ubah tombol **"Tugas Kelompok"** agar mengarah ke `route('pembimbing.tugas.index', ['masuk_kelompok' => $masukKelompok->id, 'jenis_tugas' => 'kelompok', 'tab_tim' => 'tugas_tim'])` (bukan ke penugasan Ketua).
  - Tambahkan 2 tab di dalam mode `masuk_kelompok`:
    1. **Tugas Tim (`tab_tim=tugas_tim`)**: Menampilkan daftar tugas `tipe = 'kelompok'` milik tim ini (klik mengarah ke `pembimbing.tugas.show`) + tombol **"+ Buat Tugas Tim"** (`route('pembimbing.tugas.create', ['tipe' => 'kelompok', 'kelompok_id' => $masukKelompok->id])`).
    2. **Anggota & Tugas Individu (`tab_tim=anggota`)**: Menampilkan daftar kartu anggota tim (`Nadia Kusuma [Ketua Tim]`, `Rizky Ramadhan [Anggota]`, dst.) untuk melihat/mengelola tugas individu masing-masing anggota.
- [ ] **4.2 Pembimbing — `resources/views/pembimbing/tugas/create.blade.php` + `tugas-create.css` + `resources/js/pages/pembimbing/tugas-create.js`**:
  - Di sidebar kanan ("Untuk"), tambahkan segmented toggle / radio pilihan **Tipe Penugasan**:
    - `Tugas Individu` (default, menampilkan trigger modal **"Pilih Peserta"** -> `penempatan_ids[]`).
    - `Tugas Tim` (menampilkan trigger modal **"Pilih Tim / Kelompok"** -> `kelompok_magang_ids[]` berisi daftar `nama_kelompok` + jumlah anggota).
  - Di `tugas-create.js`: kelola perpindahan mode (`individu` vs `kelompok`), sinkronkan hidden input `tipe` dan `kelompok_magang_ids[]`, serta validasi sebelum submit agar minimal 1 peserta (mode individu) atau minimal 1 tim (mode tim) dipilih.
- [ ] **4.3 Pembimbing — `resources/views/pembimbing/tugas/show.blade.php` + `tugas-review.css`**:
  - Jika `$tugas->isKelompok()`:
    - Topbar picker & header menampilkan **Nama Tim** (`$tugas->kelompokMagang?->nama_kelompok`), badge **Tugas Tim**, serta chip/daftar nama anggota tim.
    - Pada daftar riwayat kiriman (`progresLogs`), tampilkan label **"Dikirim oleh: {{ $log->pelapor?->nama ?? 'Anggota Tim' }}"**.
    - Tombol aksi review (`Terima Progres`, `Minta Revisi`, `Tandai Selesai`) tetap memproses 1 tugas tim tersebut.
- [ ] **4.4 Peserta — `resources/views/peserta/tugas/index.blade.php` + `resources/css/pages/peserta/tugas-index.css` + `resources/js/pages/peserta/tugas-index.js`**:
  - Tambahkan pill filter jenis tugas di atas/sejajar filter status:
    - `Semua` | `Tugas Tim ({{ $totalTim }})` | `Tugas Individu ({{ $totalIndividu }})`
  - Tambahkan atribut `data-tipe="{{ $tugas->tipe }}"` pada setiap `.assignment-card` dan tampilkan badge visual `Tugas Tim` (biru/ungu) atau `Tugas Individu` (abu/teal) di dalam baris tugas.
  - Perbarui `peserta/tugas-index.js` agar filter status (`all`, `selesai`, `terlambat`) dan filter tipe (`all`, `kelompok`, `individu`) bekerja bersamaan secara client-side tanpa konflik.
- [ ] **4.5 Peserta — `resources/views/peserta/tugas/show.blade.php`**:
  - Jika `$tugas->isKelompok()`, tampilkan badge **Tugas Tim** dan nama tim (`$tugas->kelompokMagang?->nama_kelompok`) di bagian metadata judul tugas, serta informasi bahwa progres tugas ini berlaku untuk seluruh tim.

---

## 6. Strategi Testing

Buat file test baru `tests/Feature/TugasTimDanIndividuTest.php` menggunakan PHPUnit (pola AAA: Arrange-Act-Assert) pada database test terisolasi:

### Skenario Happy Path
1. **Pembimbing membuat Tugas Tim**: POST `/pembimbing/tugas` dengan `tipe = 'kelompok'` dan `kelompok_magang_ids = [$kelompok->id]` membuat **1 record** `Tugas` (`tipe = 'kelompok'`, `kelompok_magang_id = $kelompok->id`, `penempatan_id = null`) dan **N record** `TugasPeserta` untuk ketua + seluruh anggota aktif.
2. **Semua Anggota Tim Melihat Tugas Tim**: Login sebagai Ketua dan login sebagai Anggota biasa, keduanya melihat tugas tim tersebut di `GET /peserta/tugas` dan dapat membuka `GET /peserta/tugas/{tugas}`.
3. **Tugas Individu Paralel di Dalam Tim**: Pembimbing membuat tugas `tipe = 'individu'` hanya untuk Anggota B. Anggota B melihat **Tugas Tim + Tugas Individu miliknya**, sedangkan Ketua dan Anggota A **tidak** melihat Tugas Individu milik Anggota B (tetap melihat Tugas Tim).
4. **Submit & Review Tugas per Tim**: Anggota biasa mengunggah progres ke tugas tim -> `ProgresLog` tersimpan dengan `user_id = $anggota->id`. Pembimbing membuka `GET /pembimbing/tugas/{tugas}` -> melihat identitas Tim + nama pengirim, lalu memanggil `POST /pembimbing/progres/{progres}/review` dan `POST /pembimbing/tugas/{tugas}/selesai` -> status tugas tim berubah untuk seluruh anggota tim.

### Skenario Sad Path & Keamanan
5. **Anti-IDOR Pembimbing**: Pembimbing A tidak bisa membuat Tugas Tim untuk `kelompok_magang_id` yang dibimbing oleh Pembimbing B (422 / 403).
6. **Anti-IDOR Peserta**: Peserta dari tim lain atau peserta individu luar tim mendapat `403` saat mengakses `GET /peserta/tugas/{tugas}` milik tim lain.
7. **SuperAdmin Read-Only**: SuperAdmin mendapat `403` saat mencoba membuat tugas tim atau mereview progres tugas tim.

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **⚠️ Perubahan struktur kolom `tugas.penempatan_id` menjadi nullable** | Kode lama yang mengasumsikan `$tugas->penempatan->pembimbing` selalu non-null bisa memicu `null` pointer atau gagal otorisasi. | Audit seluruh akses `$tugas->penempatan` di `TugasPolicy`, `Pembimbing/TugasController`, `Peserta/TugasController`, dan Blade (`show.blade.php`) menggunakan null-safe operator (`?->`) dan fallback ke `kelompokMagang`. |
| **N+1 Query saat memuat daftar anggota tim & pelapor progres** | Halaman index atau detail tim melambat jika anggota tim banyak. | Gunakan eager loading eksplisit: `with(['kelompokMagang.ketua', 'kelompokMagang.anggota.user', 'progresLogs.pelapor'])`. |
| **Asset Vite tidak ter-compile setelah ubah CSS/JS** | Tampilan/toggle baru tidak berjalan di browser. | Wajib jalankan `npm run build` setelah perubahan di `resources/css/` dan `resources/js/`. |
| **Pelanggaran aturan Blade (`AGENTS.md`)** | Inline CSS/JS di Blade ditolak saat code review. | Pastikan 100% styling berada di `resources/css/pages/...` dan 100% logic JS berada di `resources/js/pages/...`. |

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Migration berjalan bersih tanpa error di database test & lokal.
- [ ] Pembimbing dapat membuat **Tugas Tim** (ditujukan ke Nama Tim, bukan Ketua) dan **Tugas Individu** dari `/pembimbing/tugas/create`.
- [ ] Di `/pembimbing/tugas`, tampilan kelompok memisahkan **Tugas Tim** dan **Anggota & Tugas Individu** dengan jelas; tombol **Tugas Kelompok** tidak lagi mengarah ke Ketua.
- [ ] Di `/peserta/tugas`, semua anggota tim dapat melihat **Tugas Tim**, peserta dengan tugas individu tetap melihat kedua jenis tugas, dan filter **Tugas Tim** vs **Tugas Individu** berfungsi.
- [ ] Di `/pembimbing/tugas/{tugas}`, review tugas tim menampilkan identitas Tim, anggota tim, nama pengirim progres, dan status review berlaku untuk satu tim.
- [ ] Seluruh test di `tests/Feature/TugasTimDanIndividuTest.php` dan regression test modul tugas (`php artisan test`) lulus 100%.
- [ ] `./vendor/bin/pint --test`, `./vendor/bin/phpstan analyse --memory-limit=1G`, dan `npm run build` lulus tanpa error.
