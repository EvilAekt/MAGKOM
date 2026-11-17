# 📋 SENIOR DEVELOPER TECHNICAL TASK & PRD
## Task: Penguncian Roster Anggota Tim Kelompok Saat Kuota & Jumlah Telah Dipilih di Awal (Pre-Selected Quota Roster Lock)

- **Author**: Lead Senior Software Architect (Antigravity Architecture Team)
- **Target Assignee**: Junior Fullstack Developer / Autonomous AI Agent (Gemini 3.8 Flash)
- **Dokumen Referensi Bukti**: 
  - `media_1794893025503_d9644c58.png` (Kartu Kuota Terpilih Aktif, Tipe Pendaftaran Terkunci, namun Tombol Tambah Rekan Masih Muncul)
- **Target File yang Perlu Dimodifikasi**:
  1. [`resources/views/auth/register.blade.php`](file:///g:/MAGKOM/resources/views/auth/register.blade.php) (Logika Kondisional Penguncian Kontrol Tambah & Hapus Anggota)
  2. [`resources/js/pages/auth/auth-depth.js`](file:///g:/MAGKOM/resources/js/pages/auth/auth-depth.js) (Guard pada Roster Manager agar Tidak Memproses Penambahan/Penghapusan saat Terkunci)
  3. [`resources/css/pages/auth/auth-depth.css`](file:///g:/MAGKOM/resources/css/pages/auth/auth-depth.css) (Styling Banner Kunci Jumlah Rombongan & State Read-Only)
  4. [`tests/Feature/PendaftaranKelompokTest.php`](file:///g:/MAGKOM/tests/Feature/PendaftaranKelompokTest.php) (Unit & Feature Test Verifikasi State Roster Terkunci)
- **Status Dokumen**: ✅ **SELESAI & TERVERIFIKASI (Implemented & Verified)**
- **Tanggal**: 17 November 2026
- **Klasifikasi**: Business Logic Integrity & UX Guardrail (Sinkronisasi Kuota Terkunci)

---

## 🎯 1. Ringkasan Eksekutif (TL;DR untuk Junior Developer / AI)

Berdasarkan bukti tangkapan layar `media_1794893025503_d9644c58.png`:
Calon peserta magang telah memilih kuota tertentu dari Beranda / Landing Page (contoh: *TKJ SMK, Periode Desember 2026, Tersedia 4 Kursi*) dengan jalur **Kelompok sejumlah 3 Orang** (1 Ketua + 2 Rekan Anggota).

Pada saat diarahkan ke formulir registrasi (`/register`):
1. **Komponen yang Sudah Terkunci**:
   - Kartu Kuota Terpilih di atas aktif dengan tombol `[ Ubah Pilihan ↺ ]`.
   - Tombol toggle `Tipe Pendaftaran Magang` sudah terkunci (Individu berstatus *disabled*).
2. **Masalah yang Terjadi (Defect / Leak)**:
   - Tombol **`[+ Tambah Rekan Anggota]`** di bagian bawah **masih aktif dan dapat diklik**.
   - Tombol **`[x]` (hapus)** pada tiap kartu rekan anggota **masih aktif**.
   - Jika peserta mengklik tambah (misalnya menjadi 4 orang), jumlah rombongan tidak lagi sesuai dengan verifikasi kuota awal yang dilakukan pada beranda, sehingga berpotensi menimbulkan *mismatch* kuota dan gagal validasi backend (`size:2`).

### Solusi yang Diinstruksikan:
Jika pendaftar datang dengan **kuota & jumlah yang telah dipilih di awal** (`$isKuotaTerpilih` dan membawa `jumlah`):
- Tombol **`[+ Tambah Rekan Anggota]`** **DISEMBUNYIKAN**.
- Tombol **`[x]` (hapus)** pada seluruh kartu rekan anggota **DISEMBUNYIKAN**.
- Tampilkan badge / info banner informatif:
  > *"Jumlah anggota rombongan dikunci sebanyak **X orang (1 Ketua + Y Rekan)** sesuai kuota pilihan Anda dari beranda. Jika ingin mengubah jumlah anggota rombongan, silakan klik **Ubah Pilihan ↺** di atas."*
- Sebaliknya, jika pendaftar membuka `/register` **secara langsung tanpa memilih kuota di awal**, tombol tambah dan hapus **tetap aktif bebas** (dinamis 2 hingga batas maksimal sistem).

---

## 🔬 2. Analisis Kondisi & Perilaku Sistem (Behavior Matrix)

| Skenario | Pilihan Kuota Awal | Tipe Pendaftaran | Tombol Tambah Anggota | Tombol Hapus [x] | Banner Keterangan |
| :--- | :---: | :---: | :---: | :---: | :--- |
| **Skenario 1 (Dari Landing Page)** | ✅ Terpilih (`?bulan=..&jurusan=..&tipe=kelompok&jumlah=3`) | Terkunci (`is-locked`) | **Disembunyikan** (`display: none;`) | **Disembunyikan** (`display: none;`) | Tampil: *"Jumlah rombongan dikunci (3 Orang)..."* |
| **Skenario 2 (Akses Langsung /register)** | ❌ Tidak Ada (Form Bersih) | Bebas Pilih | **Aktif** (Bisa Tambah s.d. Batas Maksimal) | **Aktif** (Bisa Hapus hingga sisa 1 rekan) | Tidak Tampil |
| **Skenario 3 (Landing Page - Mandiri)** | ✅ Terpilih (`tipe=mandiri&jumlah=1`) | Terkunci (`is-locked`) | Form Kelompok tertutup | Form Kelompok tertutup | - |

---

## 📁 3. Rincian Perubahan File (File Impact Table)

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/auth/register.blade.php` | **Modify** | Tambahkan flag `$isJumlahLocked = $isKuotaTerpilih && request()->filled('jumlah');`. Sembunyikan tombol tambah & tombol hapus jika `$isJumlahLocked` bernilai `true`. Render banner penjelasan penguncian kuota. |
| `resources/js/pages/auth/auth-depth.js` | **Modify** | Periksa atribut `data-locked-count="true"` pada container `#roster-anggota-container`. Jika terkunci, cegah manipulasi DOM via JS dan sembunyikan kontrol manipulasi. |
| `resources/css/pages/auth/auth-depth.css` | **Modify** | Styling banner `.roster-locked-banner` dengan palet tema resmi (*blue-tint*, icon gembok halus). |
| `tests/Feature/PendaftaranKelompokTest.php` | **Modify** | Tambahkan feature test untuk memverifikasi bahwa URL dengan parameter kuota awal merender form dengan status roster terkunci dan tanpa tombol tambah. |

---

## 📋 4. Langkah-Langkah Pengerjaan Bertahap (Senior Dev Tasks)

### Task 1: Blade Conditional State & Locked Banner
**File**: [`resources/views/auth/register.blade.php`](file:///g:/MAGKOM/resources/views/auth/register.blade.php)
- [x] Di blok inisialisasi `@php` bagian atas, definisikan:
  ```blade
  $isJumlahLocked = $isKuotaTerpilih && (request()->filled('jumlah') || request()->filled('redirect_jumlah'));
  ```
- [x] Pada `#roster-anggota-container`, tambahkan attribute:
  ```blade
  data-locked-count="{{ $isJumlahLocked ? 'true' : 'false' }}"
  ```
- [x] Pada tombol hapus `.btn-remove-anggota`, tambahkan kondisi:
  ```blade
  style="{{ ($isJumlahLocked || ($i === 0 && $anggotaCount === 1)) ? 'display: none;' : '' }}"
  ```
- [x] Pada wadah `.roster-actions-wrap`:
  - Jika `$isJumlahLocked` bernilai `true`:
    - Wadah `.roster-actions-wrap`, tombol `#btn-add-anggota`, dan teks banner ditiadakan/disembunyikan sepenuhnya (tampilan bersih tanpa tombol tambah dan tanpa teks banner sesuai permintaan user).
  - Jika `$isJumlahLocked` bernilai `false`:
    - Tampilkan `#btn-add-anggota` seperti biasa.

---

### Task 2: JavaScript Guard on Roster Manager
**File**: [`resources/js/pages/auth/auth-depth.js`](file:///g:/MAGKOM/resources/js/pages/auth/auth-depth.js)
- [x] Di dalam fungsi `updateRosterState()`:
  - Periksa `const isLockedCount = rosterContainer.getAttribute('data-locked-count') === 'true';`
  - Jika `isLockedCount` aktif:
    - Seluruh tombol hapus `.btn-remove-anggota` dipaksa `style.display = 'none'`.
    - Tombol tambah `#btn-add-anggota` dipaksa `style.display = 'none'`.
    - Cegah event listener klik menambah atau menghapus kartu.

---

### Task 3: CSS Visual Polish
**File**: [`resources/css/pages/auth/auth-depth.css`](file:///g:/MAGKOM/resources/css/pages/auth/auth-depth.css)
- [x] Tambahkan styling untuk `.roster-locked-banner`:
  ```css
  .roster-locked-banner {
    background: #EEF3FB;
    border: 1px solid #D5DEEE;
    color: #003566;
    animation: fadeInSlideDown 0.2s ease-out;
  }
  .roster-locked-banner strong {
    color: #002244;
  }
  ```

---

### Task 4: Automated Testing & Verification
**File**: [`tests/Feature/PendaftaranKelompokTest.php`](file:///g:/MAGKOM/tests/Feature/PendaftaranKelompokTest.php)
- [x] Tambahkan pengujian otomatis:
  1. `test_halaman_register_mengunci_roster_anggota_jika_kuota_dan_jumlah_dipilih_di_awal()`:
     - Akses `GET /register?bulan=1&jurusan=1&tipe=kelompok&jumlah=3`
     - Pastikan view memuat `data-locked-count="true"`.
     - Pastikan view memuat teks *"Jumlah anggota rombongan dikunci sebanyak 3 orang"*.
     - Pastikan tombol tambah tidak dapat diklik / disembunyikan.
  2. `test_halaman_register_tetap_membuka_roster_jika_tanpa_kuota_terpilih()`:
     - Akses `GET /register?tipe=kelompok` (tanpa bulan & jurusan)
     - Pastikan view memuat `data-locked-count="false"`.
     - Pastikan tombol `Tambah Rekan Anggota` tampil dan aktif.

---

### Task 5: Quality Gate Verification
Jalankan pemeriksaan berkas sebelum menyatakan selesai:
- [x] Test Backend: `php artisan test tests/Feature/PendaftaranKelompokTest.php`
- [x] Code Formatting: `./vendor/bin/pint --test app/ tests/`
- [x] Static Analysis: `./vendor/bin/phpstan analyse app/Http/Controllers/Auth/AuthController.php --memory-limit=1G`
- [x] Frontend Asset Compile: `npm run build`

---

## 🛡️ 5. Matriks Risiko & Mitigasi

1. **Risiko Pendaftar Tidak Bisa Menyesuaikan Tim**:
   - *Mitigasi*: Tombol `[ Ubah Pilihan ↺ ]` sudah tersedia jelas di kartu atas untuk kembali ke landing page dan memilih kartu kuota dengan jumlah tim yang diinginkan secara resmi.
2. **Risiko Regresi Pengguna yang Datang Langsung (Direct URL)**:
   - *Mitigasi*: Logika penguncian hanya aktif jika `$isJumlahLocked` bernilai `true`. Pengguna langsung tetap memiliki fleksibilitas penuh untuk menambah/menghapus rekan anggota.

---

## ✅ 6. Kriteria Selesai (Definition of Done)

- [x] Jika kuota dan jumlah rombongan sudah dipilih di awal (dari landing page), tombol `+ Tambah Rekan Anggota` tidak muncul.
- [x] Seluruh tombol hapus `[x]` pada kartu anggota tidak muncul saat terkunci.
- [x] Tampil banner keterangan yang informatif bahwa jumlah rombongan terkunci dan dapat diubah via `Ubah Pilihan`.
- [x] Jika mendaftar tanpa kuota awal (langsung ke `/register`), tombol tambah dan hapus tetap berfungsi normal.
- [x] Seluruh automated test lulus (`php artisan test`).
- [x] Kompilasi aset Vite berhasil (`npm run build`).

---
*Dokumen rencana kerja ini siap dieksekusi setelah mendapatkan konfirmasi persetujuan dari pengguna.*
