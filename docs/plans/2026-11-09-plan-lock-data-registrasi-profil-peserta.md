# Rencana Kerja: Penguncian Permanen Data Registrasi (Nama, Institusi/Sekolah, NIM/NISN, Jenjang, Jurusan) pada Profil Peserta

Dokumen ini merupakan panduan teknis dan rencana implementasi terstruktur untuk mengunci secara permanen 5 data registrasi utama peserta pada portal MagKom:
1. **Nama Lengkap Peserta** (`name` / `nama` pada `users`)
2. **Nama Sekolah / Perguruan Tinggi** (`institusi` pada `peserta_profil`)
3. **NIM / NISN** (`nim_nisn` pada `peserta_profil`)
4. **Jenjang Pendidikan** (`jenjang` pada `peserta_profil`)
5. **Jurusan / Program Studi** (`jurusan_id` pada `peserta_profil`)

---

## 1. Tujuan

Memastikan bahwa kelima data identitas akademik dan registrasi yang telah diisikan peserta saat melakukan pendaftaran akun / registrasi awal:
- **Terkunci secara visual dan interaktif (Disabled / Readonly)** di antarmuka web ([resources/views/peserta/profil/show.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/profil/show.blade.php)), tidak ikut terbuka saat tombol *Edit* diklik.
- **Dilindungi secara ketat di sisi backend (Defense-in-Depth)** pada [PesertaProfilRequest.php](file:///d:/MAGKOM/MAGKOM/app/Http/Requests/PesertaProfilRequest.php) dan [ProfilController.php](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Peserta/ProfilController.php), sehingga tidak dapat dimanipulasi melalui browser developer tools (Inspect Element), injeksi request HTTP, maupun API calling.
- **Memberikan kejelasan informasi (UX Transparency)** kepada peserta dengan indikator gembok (🔒) dan keterangan bahwa data tersebut terdaftar secara resmi dari akun awal dan hanya dapat diubah melalui permohonan ke administrator/Dinas Kominfo jika terjadi kesalahan fatal.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Scope Field yang Terkunci Permanen**:
   - `nama` (Nama Lengkap)
   - `institusi` (Nama Sekolah / Perguruan Tinggi)
   - `nim_nisn` (NIM / NISN)
   - `jenjang` (Jenjang Pendidikan: SMK / D3 / S1)
   - `jurusan_id` (Jurusan / Program Studi)
2. **Scope Field yang Tetap BISA Diubah / Diperbarui oleh Peserta**:
   - `foto` (Pasfoto Profil: upload, ganti, hapus)
   - `nama_pembimbing_sekolah` (Nama Pembimbing dari Sekolah/Kampus)
   - `no_hp` (Nomor WhatsApp / Telepon)
   - `alamat` (Alamat Domisili)
   - `nama_kontak_darurat` (Nama Kontak Darurat)
   - `hubungan_kontak_darurat` (Hubungan Kontak Darurat)
   - `no_kontak_darurat` (Nomor Telepon Kontak Darurat)
3. **Perilaku Tombol "Edit" di Section Akademik**:
   - Karena 4 field akademik (`institusi`, `nim_nisn`, `jenjang`, `jurusan_id`) terkunci permanen, tombol "Edit" pada *Informasi Akademik & Pendidikan* hanya akan mengaktifkan input `nama_pembimbing_sekolah`. Jika `nama_pembimbing_sekolah` tidak ingin diubah atau belum diisi, peserta tetap melihat data akademik lainnya dalam keadaan terkunci aman.
4. **Fallback untuk Data Profil Baru (First-time initialization)**:
   - Jika ada akun peserta yang dibuat tanpa data profil awal (misal seeder legacy atau via OAuth khusus), saat pertama kali menyimpan profil, nilai awal yang diinputkan akan disimpan dan kemudian langsung terkunci permanen.

---

## 3. Evaluasi Pendekatan Teknis (Architecture Evaluation)

### Opsi A: Hidden Input Mirroring + Backend Immutable Guard (Direkomendasikan)
- **Mekanisme**:
  - Di Blade, input `institusi` dan `nim_nisn` diberi atribut `readonly` dan styling `.is-permanent-locked`, sedangkan select `jenjang` dan `jurusan_id` diberi atribut `disabled` dan class `.is-permanent-locked`.
  - Disertakan `<input type="hidden">` untuk field `jenjang` dan `jurusan_id` agar payload form submit tetap memuat nilai aslinya secara valid sesuai HTML specification (karena tag `<select disabled>` tidak dikirim oleh browser).
  - Di `PesertaProfilRequest`, tambahkan method `prepareForValidation()` yang secara otomatis mengambil nilai default dari `$this->user()?->profil` apabila field registrasi tidak dikirim atau dimanipulasi.
  - Di `ProfilController@simpan`, nilai `institusi`, `nim_nisn`, `jenjang`, dan `jurusan_id` yang sudah tersimpan di database **diabaikan dari payload update** (`unset`), sehingga data database yang sudah ada tidak pernah ditimpa oleh input pengguna. Untuk `nama`, `ProfilController` memang tidak pernah memperbarui `User::name`, sehingga integritas nama tetap aman.
- **Kelebihan**:
  - **Keamanan OWASP Top 10 Sangat Kuat**: Memblokir serangan manipulasi parameter / mass assignment. Pengguna tidak bisa mengubah data melalui Inspect Element atau tool request seperti cURL/Postman.
  - **Bebas Bug Submit Form**: Nilai tidak hilang saat form disubmit dalam keadaan disabled.
  - **Konsistensi UI/UX**: Tampilan terkunci jelas dengan badge gembok 🔒 yang tidak membingungkan peserta.
- **Kekurangan**:
  - Membutuhkan penambahan beberapa baris hidden input di Blade dan logika `unset`/`prepareForValidation` di controller & request.

### Opsi B: Hanya Validasi di JavaScript (Client-side Only)
- **Mekanisme**:
  - Hanya menonaktifkan input di JavaScript ([profil-show.js](file:///d:/MAGKOM/MAGKOM/resources/js/pages/peserta/profil-show.js)) tanpa proteksi controller dan tanpa hidden input.
- **Kelebihan**:
  - Perubahan kode sedikit lebih ringkas di awal.
- **Kekurangan**:
  - **Sangat Rentan Keamanan (Insecure)**: Pengguna dapat dengan mudah menghapus atribut `disabled` / `readonly` lewat Browser Developer Tools (F12) dan mengirimkan jurusan/jenjang lain untuk mengakali kuota.
  - Jika JavaScript gagal dimuat di browser pengguna, data bisa berubah.

> **Rekomendasi**: Mengadopsi **Opsi A** untuk menjamin kepatuhan aturan keamanan `backend-security.md` dan integritas data kuota pendaftaran.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [resources/views/peserta/profil/show.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/profil/show.blade.php) | Modify | 1. Tambah penanda gembok 🔒 & keterangan *"Terkunci (Data Registrasi)"* pada Nama di header profil.<br>2. Jadikan field `institusi`, `nim_nisn`, `jenjang`, dan `jurusan_id` terkunci permanen (`is-permanent-locked`).<br>3. Tambah hidden input mirror untuk `jenjang` dan `jurusan_id` agar payload form submit selalu valid.<br>4. Update tombol edit akademik agar hanya berfokus pada field pembimbing sekolah. |
| [resources/css/pages/peserta/profil-show.css](file:///d:/MAGKOM/MAGKOM/resources/css/pages/peserta/profil-show.css) | Modify | Tambahkan styling untuk `.is-permanent-locked`, badge gembok status data registrasi, background input netral readonly (`#F8FAFC`), dan kursor `not-allowed`. |
| [resources/js/pages/peserta/profil-show.js](file:///d:/MAGKOM/MAGKOM/resources/js/pages/peserta/profil-show.js) | Modify | Modifikasi `setupSectionLock`: kecualikan elemen dengan atribut `data-permanent-locked="true"` dari daftar input yang di-unlock saat tombol edit diklik. Saat `btnEditAkademik` diklik, hanya `nama_pembimbing_sekolah` yang diaktifkan. |
| [app/Http/Requests/PesertaProfilRequest.php](file:///d:/MAGKOM/MAGKOM/app/Http/Requests/PesertaProfilRequest.php) | Modify | Tambahkan `prepareForValidation()` untuk mengisi otomatis `institusi`, `nim_nisn`, `jenjang`, dan `jurusan_id` dari profil existing jika bernilai kosong. |
| [app/Http/Controllers/Peserta/ProfilController.php](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Peserta/ProfilController.php) | Modify | Lindungi field `institusi`, `nim_nisn`, `jenjang`, dan `jurusan_id` di `simpan()`: jika user telah memiliki profil, nilai ke-4 data ini tidak boleh ditimpa dari request pengguna (immutable). |
| [tests/Feature/PesertaPortalWorkflowTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/PesertaPortalWorkflowTest.php) | Modify | Tambahkan assertion untuk memverifikasi bahwa upaya pengubahan data registrasi (institusi, nim_nisn, jenjang, jurusan, nama) akan diabaikan dan nilai asli di database tetap terjaga. |

---

## 5. Langkah Implementasi Terstruktur

### Tahap 1: Backend Security & Immutability Guard
- [x] **Langkah 1.1**: Pada [PesertaProfilRequest.php](file:///d:/MAGKOM/MAGKOM/app/Http/Requests/PesertaProfilRequest.php), tambahkan method `prepareForValidation()`:
  - Jika user memiliki profil yang sudah tersimpan, pastikan `institusi`, `nim_nisn`, `jenjang`, dan `jurusan_id` diambil dari profil existing apabila request tidak menyertakannya.
- [x] **Langkah 1.2**: Pada [ProfilController.php](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Peserta/ProfilController.php) method `simpan()`:
  - Jika `$profil` sudah ada di database, kunci field registrasi dengan melakukan `unset($validated['institusi'], $validated['nim_nisn'], $validated['jurusan_id'], $validated['jenjang'])` secara permanen, sehingga data yang tersimpan di database tidak dapat diubah oleh peserta melalui request profil.

### Tahap 2: Frontend Blade Markup & Indikator UI
- [x] **Langkah 2.1**: Pada [resources/views/peserta/profil/show.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/profil/show.blade.php):
  - Pada kartu identitas banner atas, tambahkan ikon gembok kecil (🔒) atau badge keterangan di samping nama: *"Identitas Akun Resmi"*.
  - Pada section *Informasi Akademik & Pendidikan*, tambahkan badge informasi:
    `<span class="profil-badge-locked">🔒 Data Registrasi Terkunci</span>`
  - Pada field `institusi`, `nim_nisn`:
    - Pasang atribut `readonly` dan `data-permanent-locked="true"`.
    - Tambahkan badge penanda gembok / keterangan bahwa data terdaftar saat registrasi.
  - Pada field `jenjang` dan `jurusan_id`:
    - Pasang atribut `disabled`, class `is-permanent-locked`, dan `data-permanent-locked="true"`.
    - Tambahkan hidden input mirror:
      `<input type="hidden" name="jenjang" value="{{ $profil?->jenjang?->value ?? $profil?->jenjang }}">`
      `<input type="hidden" name="jurusan_id" value="{{ $profil?->jurusan_id }}">`
  - Sesuaikan tombol `btnEditAkademik`:
    - Beri teks *"Edit Pembimbing"* atau tetap *"Edit"* namun dengan tooltip jelas bahwa yang dapat diedit di bagian akademik hanyalah nama pembimbing sekolah/kampus.

### Tahap 3: Styling CSS Scoped
- [x] **Langkah 3.1**: Pada [resources/css/pages/peserta/profil-show.css](file:///d:/MAGKOM/MAGKOM/resources/css/pages/peserta/profil-show.css):
  - Tambahkan style untuk `.is-permanent-locked` dan `.profil-input-locked`:
    - `background-color: #F8FAFC !important;`
    - `border-color: #E2E8F0 !important;`
    - `color: #475569 !important;`
    - `cursor: not-allowed !important;`
    - `pointer-events: none;` (untuk select)
  - Styling badge status terkunci registrasi dengan palet MagKom (`#02275D` / neutral grey).

### Tahap 4: JavaScript Interactivity
- [x] **Langkah 4.1**: Pada [resources/js/pages/peserta/profil-show.js](file:///d:/MAGKOM/MAGKOM/resources/js/pages/peserta/profil-show.js):
  - Modifikasi `setupSectionLock`:
    - Jangan masukkan elemen yang memiliki atribut `data-permanent-locked="true"` ke dalam daftar field yang di-unlock.
    - Pastikan saat `btnEditAkademik` diklik, hanya `inputPembimbing` yang diaktifkan (`readOnly = false`, `.focus()`).
    - Input `institusi`, `nim_nisn`, `selectJenjang`, dan `selectJurusan` tetap terkunci permanen.

### Tahap 5: Quality Gate & Verifikasi
- [x] **Langkah 5.1**: Kompilasi asset Vite dengan `npm run build`.
- [x] **Langkah 5.2**: Jalankan pengujian PHPUnit:
  - `PesertaPortalWorkflowTest`
  - `PesertaPendaftaranFlowTest`
  - `PendaftaranPesertaTest`
- [x] **Langkah 5.3**: Jalankan linter dan formatting Laravel Pint (`vendor/bin/pint --test`).
- [x] **Langkah 5.4**: Verifikasi visual di browser (responsiveness & tampilan lock).

---

## 6. Strategi Testing

1. **Feature Test (PHPUnit)**:
   - **Happy Path**: Peserta dapat memperbarui pasfoto profil, nama pembimbing sekolah, nomor kontak darurat, dan nomor WhatsApp.
   - **Sad Path (Tampering Attempt)**: Peserta mencoba mengirimkan payload POST dengan `institusi`, `nim_nisn`, `jenjang`, atau `jurusan_id` yang berbeda dari data registrasi awal. Sistem wajib menolak perubahan nilai tersebut dan mempertahankan data asli di database.
   - **Nama Tampering**: Peserta mencoba memalsukan field `nama` di request; nama akun di tabel `users` tidak boleh berubah.
2. **UI Assertion**:
   - Section Informasi Akademik menampilkan status terkunci untuk ke-4 data registrasi.
   - Klik tombol "Edit" di bagian akademik hanya mengaktifkan input nama pembimbing sekolah.

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| Tag `<select disabled>` tidak mengirimkan value di HTTP POST | Validasi `required` di FormRequest bisa gagal jika data kosong | Gunakan hidden input mirror untuk `jenjang` dan `jurusan_id` serta fallback `prepareForValidation()` dari profil existing. |
| Test lama berasumsi profil kosong dapat diisi semua datanya | Test failure pada test suite profil awal | Pastikan logika controller mengizinkan pengisian data jika profil belum pernah ada sebelumnya (first-time save), dan baru menguncinya secara permanen setelah data tersimpan. |
| Pengguna salah menginput data saat registrasi awal | Data salah ketik tidak bisa diperbaiki mandiri | Sediakan keterangan jelas di UI bahwa perbaikan data identitas registrasi dapat diajukan ke administrator melalui bantuan/kontak resmi MagKom. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Data Nama Sekolah/Kampus, NIM/NISN, Jenjang, dan Jurusan berstatus terkunci permanen di tampilan profil peserta.
- [x] Tombol Edit pada bagian akademik tidak membuka kunci ke-4 data registrasi tersebut.
- [x] Backend controller dan FormRequest menjamin data registrasi tidak dapat ditimpa oleh input peserta (defense-in-depth).
- [x] Tidak ada script JS inline atau CSS inline di file Blade.
- [x] Asset Vite berhasil dikompilasi (`npm run build`).
- [x] Seluruh test suite PHPUnit lulus 100% tanpa regresi.
- [x] Standar kode memenuhi PSR-12 dan lulus Laravel Pint (`vendor/bin/pint --test`).

