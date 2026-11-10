# Rencana Kerja: Penyesuaian Pengeditan Data Akademik (Universitas, NIM/NISN, Jurusan, Jenjang) pada Profil & Biodata Peserta

## 1. Konteks & Latar Belakang
Pengguna menyampaikan bahwa pada halaman **Profil & Biodata Peserta**, terdapat 4 data akademik yang diisikan saat registrasi namun saat ini tidak dapat diubah / terkunci:
1. **Universitas / Nama Sekolah / Perguruan Tinggi** (`institusi`)
2. **NIM / NISN** (`nim_nisn`)
3. **Jurusan / Program Studi** (`jurusan_id`)
4. **Jenjang Pendidikan** (`jenjang`)

### Analisis Masalah Saat Ini:
1. **Atribut HTML Statis**: Di [resources/views/peserta/profil/show.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/profil/show.blade.php), input `institusi` dan `nim_nisn` dipasangi atribut `readonly data-locked="true"`, sedangkan select `jenjang` dan `jurusan_id` dipasangi `disabled data-locked="true"`.
2. **CSS Pointer-Events**: Di `profil-show.css`, kelas `.profil-select.is-locked` memiliki `pointer-events: none` dan `cursor: not-allowed` yang memblokir interaksi klik pengguna.
3. **Ketiadaan Filter Dinamis Jenjang ↔ Jurusan**: Di halaman profil belum ada logika JavaScript yang memfilter opsi jurusan berdasarkan jenjang (SMK vs D3/S1), tidak seperti form registrasi yang sudah memiliki `filterJurusanByJenjang()`.
4. **Kebutuhan Bisnis Peserta**: Jika peserta salah memasukkan nama universitas, salah ketik NIM/NISN, atau keliru memilih jurusan/jenjang saat registrasi awal, mereka perlu dapat menyesuaikan/memperbaiki data tersebut sebelum memilih kuota dan mengajukan pendaftaran magang.

---

## 2. Aturan Bisnis & Logika Penyesuaian

### A. Kondisi Izin Pengeditan (Edit Eligibility)
| Status Pendaftaran Peserta | Status 4 Data Akademik | Keterangan & Perilaku UX |
|---|---|---|
| **Belum Ada Pendaftaran / Onboarding** (Draf belum diajukan) | **BISA DIEDIT BEBAS** (Unlocked saat klik "Edit") | Peserta dapat mengklik tombol "Edit" pada section Akademik, lalu mengubah Universitas, NIM/NISN, Jenjang, dan Jurusan secara leluasa. |
| **Sudah Ada Pendaftaran Aktif** (`diajukan`, `waiting_list`, `diterima`, `aktif`) | **TERKUNCI PERMANEN** (Locked) | Data dikunci permanen untuk menjaga integritas data administrasi instansi. Tombol edit disembunyikan / dinonaktifkan, disertai catatan: *"Data akademik terkunci karena Anda telah memiliki pengajuan pendaftaran magang aktif."* |

### B. Interaktivitas Dinamis Form Akademik
1. Ketika tombol **Edit** pada *Informasi Akademik & Pendidikan* diklik:
   - Input `institusi` dan `nim_nisn` berubah menjadi editable (`readOnly = false`).
   - Select `jenjang` dan `jurusan_id` aktif (`disabled = false`, `pointer-events: auto`).
2. Ketika dropdown **Jenjang Pendidikan** diubah (misal dari SMK ke S1, atau sebaliknya):
   - Opsi pada dropdown **Jurusan** otomatis disaring:
     - Jika jenjang = `smk`: Hanya menampilkan jurusan dengan `kategori = 'smk'`.
     - Jika jenjang = `d3` atau `s1`: Hanya menampilkan jurusan dengan `kategori = 'mahasiswa'`.
   - Jika jurusan yang sebelumnya terpilih tidak cocok dengan kategori jenjang yang baru dipilih, dropdown jurusan otomatis di-reset ke opsi `-- Pilih Jurusan / Program Studi --`.

---

## 3. Rencana Implementasi Bertahap

### Tahap 1: Penyesuaian View Blade ([resources/views/peserta/profil/show.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/profil/show.blade.php))
1. Hitung status kunci akademik di Blade:
   ```blade
   @php
       $isAkademikLocked = $pendaftaran && in_array($pendaftaran->status->value, [
           \App\Enums\StatusPendaftaranEnum::Diajukan->value,
           \App\Enums\StatusPendaftaranEnum::WaitingList->value,
           \App\Enums\StatusPendaftaranEnum::Diterima->value,
       ], true);
   @endphp
   ```
2. Pada elemen section akademik:
   - Jika `$isAkademikLocked`:
     - Tampilkan badge / alert informatif bahwa data akademik tidak dapat diubah karena pendaftaran aktif sedang diproses.
     - Sembunyikan tombol `btnEditAkademik` atau beri atribut disabled dengan tooltip jelas.
   - Jika belum terkunci (`!$isAkademikLocked`):
     - Pastikan tombol `btnEditAkademik` siap membuka kunci seluruh 4 input.
     - Simpan kategori jurusan pada setiap `<option>` di select `jurusan_id`:
       `<option value="{{ $jurusan->id }}" data-kategori="{{ $jurusan->kategori }}">...`

### Tahap 2: Penguatan Interaktivitas di JavaScript ([resources/js/pages/peserta/profil-show.js](file:///d:/MAGKOM/MAGKOM/resources/js/pages/peserta/profil-show.js))
1. **Fungsi Filter Jurusan Dinamis**:
   - Implementasikan fungsi `filterJurusanByJenjang(selectedJenjang)` yang menyembunyikan/menonaktifkan opsi jurusan yang tidak sesuai kategori jenjang.
   - Pasang event listener `change` pada `selectJenjang` yang langsung memanggil filter ini dan menghitung ulang kelengkapan profil.
2. **Penyempurnaan Lock & Unlock**:
   - Pastikan saat `btnEditAkademik` diklik:
     - `inputInstitusi.readOnly = false`
     - `inputNimNisn.readOnly = false`
     - `selectJenjang.disabled = false`
     - `selectJurusan.disabled = false`
     - Hapus class `.is-locked` sehingga styling kursor dan warna kembali normal.
   - Sebelum form disubmit, pastikan `selectJenjang.disabled = false` dan `selectJurusan.disabled = false` agar payload terkirim lengkap ke backend tanpa terpotong.

### Tahap 3: Backend Security Guard & Request Validation
1. **Form Request ([app/Http/Requests/PesertaProfilRequest.php](file:///d:/MAGKOM/MAGKOM/app/Http/Requests/PesertaProfilRequest.php))**:
   - Tambahkan validasi kesesuaian kategori jurusan dengan jenjang yang dipilih:
     - Jika jenjang = `smk`, pastikan `jurusan.kategori === 'smk'`.
     - Jika jenjang = `d3` atau `s1`, pastikan `jurusan.kategori === 'mahasiswa'`.
2. **Controller ([app/Http/Controllers/Peserta/ProfilController.php](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Peserta/ProfilController.php))**:
   - Cek apakah peserta memiliki pendaftaran aktif:
     - Jika memiliki pendaftaran aktif dan mencoba mengubah jenjang/jurusan/institusi/NIM, tolak pembaruan field tersebut atau kembalikan error dengan flash warning.
     - Jika belum ada pendaftaran aktif, simpan perubahan 4 data akademik tersebut ke `peserta_profil` secara lengkap.

### Tahap 4: Verifikasi & Quality Gate
1. Rebuild frontend asset dengan Vite:
   ```powershell
   npm run build
   ```
2. Jalankan rangkaian Feature Test (PHPUnit):
   ```powershell
   & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test tests/Feature/PendaftaranPesertaTest.php tests/Feature/PesertaFullPortalFeatureTest.php
   ```
3. Uji kasus:
   - Peserta baru dapat mengedit Universitas, NIM/NISN, Jenjang, dan Jurusan melalui tombol Edit.
   - Pilihan jurusan otomatis terfilter sesuai jenjang (SMK vs Mahasiswa).
   - Data berhasil tersimpan ke database dan persentase profil terupdate secara akurat.
4. Periksa code style dengan Laravel Pint:
   ```powershell
   & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" ./vendor/bin/pint --test
   ```

---

## 4. Konfirmasi Sebelum Eksekusi
Apakah alur di atas (data akademik dapat diedit bebas selama belum ada pengajuan pendaftaran aktif, dan otomatis terkunci jika pendaftaran sudah diajukan) telah sesuai dengan kebutuhan Anda?

