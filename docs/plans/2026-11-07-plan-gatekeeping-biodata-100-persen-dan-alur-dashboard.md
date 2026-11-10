# Rencana Kerja: Penegakan Kelengkapan Biodata 100% (Strict 100% Gatekeeping) & Logika Alur Dashboard Peserta

## 1. Masalah & Latar Belakang
Pada dashboard peserta untuk **POV belum diterima magang**:
- Pengguna mendapati bahwa peserta yang **belum mengisi biodata 100%** (misalnya baru 60% karena belum mengunggah pasfoto resmi atau belum mengisi kontak darurat wali) sudah ditandai **✓ Selesai** pada Langkah 1 kartu Panduan Alur Pendaftaran Magang.
- Akibatnya, alur pendaftaran memperbolehkan peserta langsung meloncat ke Langkah 2 (memilih periode & kuota magang) dan bahkan mengajukan pendaftaran, padahal profilnya belum lengkap secara tuntas.
- **Akar Masalah**:
  1. Pada `resources/views/peserta/dashboard/partials/_phase_onboarding.blade.php`, variabel `$isProfilLengkap` dihitung secara parsial (hanya mengecek `nim_nisn`, `institusi`, `jurusan_id`, `jenjang`, `no_hp`), tanpa memverifikasi pilar `foto` dan `darurat` (kontak darurat).
  2. Logika kelengkapan profil terfragmentasi antara `ProfilController.php`, `_phase_onboarding.blade.php`, dan `PendaftaranRequest.php`, sehingga tidak ada satu standar kebenaran tunggal (*Single Source of Truth*).
  3. Belum ada proteksi redirect (*route-level gatekeeping*) pada `PendaftaranController@index` jika peserta dengan profil belum 100% mencoba langsung mengakses URL pendaftaran kuota.

---

## 2. Kriteria Keberhasilan & Definisi 100% Biodata

Sebuah profil peserta dinyatakan **100% Lengkap** jika dan hanya jika **kelima pilar wajib** berikut terisi secara valid:
1. **Akun & Data Diri**: Nama lengkap dan email akun terisi (`$user->nama` & `$user->email`).
2. **Pasfoto Resmi**: Berkas foto profil terunggah (`!empty($profil->foto)`).
3. **Data Akademik & Kampus**: Institusi bukan placeholder (`institusi !== 'Institusi Belum Diatur'`), NIM/NISN bukan placeholder (`nim_nisn !== '-'`), jenjang terisi, dan jurusan terisi (`jurusan_id`).
4. **Kontak Peserta**: Nomor WhatsApp aktif dan bukan placeholder (`no_hp !== '-'`).
5. **Kontak Darurat / Wali**: Nama kontak darurat dan nomor kontak darurat terisi (`!empty($profil->nama_kontak_darurat)` & `!empty($profil->no_kontak_darurat)`).

### Perilaku Antarmuka & Alur:
| Kondisi Profil | Langkah 1: Biodata & Profil | Langkah 2: Periode & Kuota | Langkah 3: Unggah Berkas |
|---|---|---|---|
| **< 100% (Belum Lengkap)** | Status: `active`<br>Badge: `Belum Lengkap (XX%)`<br>Tombol: Buka profil | Status: `is-locked` 🔒<br>Badge: `Terkunci`<br>Link dinonaktifkan (`aria-disabled="true"`) | Status: `is-locked` 🔒<br>Badge: `Terkunci`<br>Link dinonaktifkan |
| **== 100% (Lengkap)** | Status: `completed`<br>Badge: `✓ Selesai` | Status: `active`<br>Badge: `Pilih Kuota`<br>Link aktif dapat diklik | Menyesuaikan apakah kuota/draf sudah dipilih |

---

## 3. Rencana Implementasi Bertahap

### Tahap 1: Pembentukan Single Source of Truth di Model `PesertaProfil`
1. Buka berkas [app/Models/PesertaProfil.php](file:///d:/MAGKOM/MAGKOM/app/Models/PesertaProfil.php).
2. Tambahkan method `hitungKelengkapan(): array` yang mengembalikan:
   - `checklist`: array status boolean 5 pilar (`data_diri`, `foto`, `akademik`, `kontak`, `darurat`).
   - `persentase`: persentase bulat (0 - 100%).
   - `is_lengkap`: boolean `true` hanya jika persentase bernilai tepat `100`.
3. Tambahkan helper method `isLengkap(): bool` yang memanggil `return $this->hitungKelengkapan()['is_lengkap'];`.

### Tahap 2: Sinkronisasi Controller Profil & Dashboard
1. **Refactor ProfilController**:
   - Di [app/Http/Controllers/Peserta/ProfilController.php](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Peserta/ProfilController.php), gunakan `$kelengkapan = $profil?->hitungKelengkapan();` sehingga tidak ada duplikasi rumus checklist.
2. **Kirim Data ke DashboardController**:
   - Di [app/Http/Controllers/Peserta/DashboardController.php](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Peserta/DashboardController.php), ambil data kelengkapan profil peserta:
     - `$profilKelengkapan = $profil?->hitungKelengkapan() ?? ['persentase' => 0, 'is_lengkap' => false];`
     - Operkan `$isProfilLengkap = $profilKelengkapan['is_lengkap']` dan `$persentaseProfil = $profilKelengkapan['persentase']` ke view.

### Tahap 3: Update Logika Stepper Alur di Dashboard
1. Buka [resources/views/peserta/dashboard/partials/_phase_onboarding.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard/partials/_phase_onboarding.blade.php).
2. Ganti logika inline lama dengan:
   - `$isProfilLengkap = (bool) ($isProfilLengkap ?? false);`
   - `$persentaseProfil = (int) ($persentaseProfil ?? 0);`
3. Pada Langkah 1:
   - Jika `$isProfilLengkap` bernilai `false`:
     - Tampilkan badge indikator status:
       `<span class="saas-step-state state-action">Belum Lengkap ({{ $persentaseProfil }}%)</span>`
     - Tampilkan nomor langkah `1` (bukan ikon centang hijau).
   - Jika `$isProfilLengkap` bernilai `true`:
     - Tampilkan centang hijau `<polyline points="20 6 9 17 4 12"></polyline>` dan badge `<span class="saas-step-state state-done">✓ Selesai</span>`.
4. Pada Langkah 2:
   - Kondisi `$canPilihKuota` diikat ketat ke `$isProfilLengkap`. Jika profil `< 100%`, Langkah 2 mutlak tampil sebagai elemen `is-locked` (terkunci dengan gembok) dan tidak dapat diklik.

### Tahap 4: Proteksi Berlapis Backend (Controller Guard & Form Request)
1. **Gatekeeping di PendaftaranController@index**:
   - Di [app/Http/Controllers/Peserta/PendaftaranController.php](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Peserta/PendaftaranController.php), jika peserta belum memiliki pendaftaran aktif dan `$profil?->isLengkap()` masih `false`:
     - Redirect ke `route('peserta.profil.show')` dengan pesan flash warning:
       *"Biodata dan profil Anda belum 100% lengkap ({{ $persentaseProfil }}%). Silakan lengkapi pasfoto, data akademik, kontak, dan kontak darurat Anda terlebih dahulu sebelum memilih periode dan kuota magang."*
2. **Validasi Strict di PendaftaranRequest**:
   - Di [app/Http/Requests/PendaftaranRequest.php](file:///d:/MAGKOM/MAGKOM/app/Http/Requests/PendaftaranRequest.php):
     - Ganti validasi parsial dengan pengecekan `$profil?->isLengkap()`. Jika belum 100%, batalkan transaksi dengan pesan error validasi yang jelas.

### Tahap 5: Penyesuaian Test Suite & Quality Gate
1. Periksa test feature terkait registrasi dan pendaftaran (`tests/Feature/PendaftaranPesertaTest.php`, `tests/Feature/PesertaFullPortalFeatureTest.php`).
   - Pastikan factory/seeder tes yang menguji alur pendaftaran melengkapi seluruh 5 pilar biodata agar pengujian lulus sesuai aturan bisnis baru.
2. Jalankan rangkaian tes:
   ```powershell
   & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test tests/Feature/PendaftaranPesertaTest.php tests/Feature/PesertaFullPortalFeatureTest.php
   ```
3. Jalankan pembersihan view dan audit format:
   ```powershell
   & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan view:clear
   & "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" ./vendor/bin/pint --test
   ```

---

## 4. Konfirmasi & Persetujuan
- Rencana ini mengunci pemilihan periode/kuota (Langkah 2) dan pendaftaran sampai biodata peserta mencapai **100%** (termasuk pasfoto resmi dan kontak darurat wali).
- Mohon tinjau apakah definisi 5 pilar kelengkapan di atas sudah sesuai dengan kebutuhan operasional Dinas Kominfo Sleman.

