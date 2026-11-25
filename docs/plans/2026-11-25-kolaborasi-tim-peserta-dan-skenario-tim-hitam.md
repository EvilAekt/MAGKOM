# Plan & PRD: Kolaborasi Penuh Anggota Tim pada Tugas Tim & Skenario Data Tim "Hitam Cuih" (3 Orang, Bukan Ketua)

**Tanggal**: 25 November 2026  
**Disusun oleh**: Senior Developer  
**Target Implementer**: Junior Developer / AI Coding Agent (mis. Gemini 3.8 Flash)  
**Status**: Menunggu Persetujuan User (`Draft Plan`)

---

## 1. Tujuan & Konteks Bisnis (Ringkasan PRD)

Berdasarkan kondisi saat ini:
1. Akun peserta **Hitam Cuih** hanya memiliki `6 Tugas Individu` dan `0 Tugas Tim` karena belum tergabung dalam sebuah tim.
2. Pada fitur **Tugas Tim (`tipe = 'kelompok'`)**, semua anggota tim harus memiliki **hak aksi dan visibilitas yang sama** terhadap perubahan progres tugas tim:
   - Jika **salah satu anggota tim** mengunggah progres (file/link), **seluruh anggota tim** (baik Ketua maupun Anggota biasa) langsung melihat perubahan status (*Diserahkan*), melihat file/link yang diunggah, serta melihat **siapa anggota tim yang mengunggahnya**.
   - Selama tugas belum ditandai **Selesai** atau disetujui oleh pembimbing, **siapa pun di dalam tim** (termasuk anggota lain yang bukan pengunggah awal) dapat **membatalkan pengiriman (*Batalkan Pengiriman / Unsubmit*)**, mengubah lampiran/link, atau **mengirim ulang revisi** jika statusnya `Perlu Revisi`.
3. Untuk menguji skenario nyata di aplikasi lokal, user meminta **Hitam Cuih** dimasukkan ke dalam **1 Tim berisi 3 orang** (termasuk Hitam Cuih), di mana **Hitam Cuih bukan Ketua Tim** (hanya Anggota biasa).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis
1. **Tugas Individu Hitam Cuih Dipertahankan**: 6 tugas individu yang sudah dimiliki Hitam Cuih tidak dihapus; keanggotaan tim ditambahkan agar pada dashboard Hitam Cuih terlihat **Tugas Tim** dan **Tugas Individu** sekaligus.
2. **Satu Sumber Kebenaran Progres Tim**: Setiap `Tugas Tim` (`tipe = 'kelompok'`) memiliki 1 alur `ProgresLog` bersama. Saat Anggota A mengunggah progres, Anggota B & Ketua melihat `ProgresLog` terbaru tersebut di halaman detail tugas (`/peserta/tugas/{tugas}`) dan di daftar tugas (`/peserta/tugas`).
3. **Aksi Setara (Tanpa Hak Istimewa Ketua)**: Baik Ketua Tim maupun Anggota Tim memiliki hak yang sama untuk mengunggah progres, membatalkan pengiriman (`batalkanPengiriman`), dan mengunggah revisi pada `Tugas Tim`.

---

## 3. Pendekatan Teknis & Evaluasi Opsi

### Opsi A: Hanya Ubah Data Hitam Cuih Tanpa Memperbaiki Kepemilikan `recent_file_path`
- **Kekurangan**: Jika Anggota A mengunggah file ke Tugas Tim lalu Anggota B menekan **Batalkan Pengiriman** dan mengirim ulang file yang sama (`recent_file_path`), validasi `$isOwned` di `Peserta/TugasController::unggahProgres()` akan gagal diam-diam karena query saat ini hanya memeriksa `tugas.penempatan.peserta_id === $user->id` (padahal Tugas Tim memiliki `penempatan_id = null`!).

### Opsi B: Perbaiki Kolaborasi Tim End-to-End + Seeder Idempoten Tim Hitam (Direkomendasikan)
- **Cara kerja**:
  1. Perbarui `Peserta/TugasController::show()` dan `unggahProgres()` agar `recent_file_path` dan `recentFiles` juga mengakui file progres dari **Tugas Tim** di mana peserta terdaftar (`tugasPeserta`).
  2. Perbarui `resources/views/peserta/tugas/show.blade.php` untuk menampilkan **identitas pengunggah** (`Dikirim oleh: <Nama Anggota>`) dan keterangan kolaborasi tim pada kartu `Tugas Anda` ketika `$tugas->isKelompok()`.
  3. Buat Seeder khusus yang idempoten (`Database\Seeders\HitamTimDemoSeeder`) untuk menempatkan **Hitam Cuih** ke dalam tim 3 orang (1 Ketua + Hitam Cuih + 1 Anggota lain, semua di bawah `Budi Pembimbing`) beserta 1 contoh **Tugas Tim** aktif.
- **Alasan Pemilihan**: Menjamin kolaborasi edit/batal-kirim antaranggota tim benar-benar berfungsi secara teknis (termasuk *re-attach* file setelah *unsubmit*), sekaligus menyediakan data uji siap pakai di lokal.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Http/Controllers/Peserta/TugasController.php` | Modify | Perbaiki guard `recent_file_path` di `unggahProgres()` dan query `$recentFiles` di `show()` agar mendukung Tugas Tim (`tugasPeserta` dengan `user_id = $user->id`), sehingga anggota tim lain bisa membatalkan & mengirim ulang berkas tim tanpa error kepemilikan. |
| `resources/views/peserta/tugas/show.blade.php` | Modify *(Perlu Izin Blade)* | Tampilkan info **"Diunggah oleh: [Nama Anggota]"** pada kartu `Tugas Anda` jika `$tugas->isKelompok()` & `$latestProgres?->pelapor`, serta teks keterangan bahwa seluruh anggota tim dapat melihat, membatalkan, atau merevisi pengiriman. |
| `resources/css/pages/peserta/tugas-detail.css` | Modify | Tambah styling `.peserta-tim-uploader-note` yang bersih dan konsisten dengan palet biru MagKom. |
| `database/seeders/HitamTimDemoSeeder.php` | Create | Seeder idempoten (`firstOrCreate` / `updateOrCreate`) yang membentuk tim 3 orang (`Ketua Tim` bukan Hitam Cuih, `Hitam Cuih` sebagai Anggota, dan 1 Anggota lain) di bawah pembimbing yang sama, serta membuat 1 `Tugas Tim` untuk tim tersebut. |
| `tests/Feature/TugasTimDanIndividuTest.php` | Modify | Tambah test skenario kolaborasi penuh: Anggota 1 upload -> Ketua & Anggota 2 melihat perubahan & nama pengunggah -> Anggota 2 membatalkan pengiriman (`batalkanPengiriman`) dan mengunggah revisi/file baru -> Pembimbing melihat perubahan terbaru. |

---

## 5. Langkah Implementasi (Urut Berdasarkan Dependensi)

### Tahap 1: Backend Kolaborasi Tim (`app/Http/Controllers/Peserta/TugasController.php`)
- [ ] **1.1 Update `show()` (sekitar baris 133-140)**:
  - Perluas query `$recentFiles` agar mengambil `ProgresLog` dari tugas individu milik user **ATAU** tugas tim yang diikuti user (`whereHas('tugas.tugasPeserta', fn ($qtp) => $qtp->where('user_id', $user->id))`).
- [ ] **1.2 Update `unggahProgres()` (sekitar baris 210-220)**:
  - Pada pengecekan `$isOwned` untuk `$recentPath`, tambahkan kondisi:
    ```php
    || ProgresLog::where('file', $recentPath)
        ->whereHas('tugas.tugasPeserta', function ($q) use ($user) {
            $q->where('user_id', $user?->id);
        })
        ->exists()
    ```
  - Pastikan `batalkanPengiriman()` tetap mengizinkan anggota tim mana pun selama `Gate::authorize('submitProgres', $tugas)` lulus dan `status_review === Menunggu`.

### Tahap 2: Tampilan Detail Tugas Peserta (`show.blade.php` & `tugas-detail.css`)
> ⚠️ **Catatan `AGENTS.md`**: Minta persetujuan user sebelum mengubah file `.blade.php`. Semua CSS wajib diletakkan di `resources/css/pages/peserta/tugas-detail.css`.

- [ ] **2.1 Edit `resources/views/peserta/tugas/show.blade.php`**:
  - Di dalam `.classroom-work-card` (sebelum/sesudah `.submitted-items-container` saat `$latestProgres` ada):
    - Jika `$tugas->isKelompok()`: tampilkan `.peserta-tim-uploader-note` berisi:
      - `Diunggah oleh: {{ $latestProgres->pelapor?->nama ?? 'Anggota Tim' }}`
      - Jika `$bisaKumpulLagi` atau status masih `Menunggu` (belum selesai): keterangan singkat bahwa seluruh anggota tim dapat melihat dan memperbarui pengiriman ini.
- [ ] **2.2 Edit `resources/css/pages/peserta/tugas-detail.css`**:
  - Tambahkan class `.peserta-tim-uploader-note` (background `#EFF6FF`, border `1px solid #BFDBFE`, color `#1E40AF`, border-radius `8px`, padding `8px 10px`, font-size `0.775rem`, margin-bottom `10px`).

### Tahap 3: Seeder Data Tim "Hitam Cuih" (3 Orang, Bukan Ketua)
- [ ] **3.1 Buat `database/seeders/HitamTimDemoSeeder.php`**:
  - Cari user **Hitam Cuih** berdasarkan nama/email (`User::where('nama', 'like', '%Hitam Cuih%')->firstOrFail()`) beserta `Penempatan` aktifnya (untuk mengambil `bidang_id`, `pembimbing_id`, `tgl_mulai`, `tgl_selesai`).
  - Buat/ambil 2 user rekan tim secara idempoten (`firstOrCreate` berdasarkan email):
    1. **Ketua Tim** (mis. `Ketua Tim Sleman` / `ketua.tim.sleman@magkom.test`, role `peserta`, `PesertaProfil` aktif).
    2. **Anggota 2** (mis. `Anggota Tim Sleman` / `anggota.tim.sleman@magkom.test`, role `peserta`, `PesertaProfil` aktif).
  - Buat/ambil `KelompokMagang` (mis. `Tim Kolaborasi Digital Sleman`) dengan `ketua_user_id = $ketuaUser->id` (**bukan** Hitam Cuih!).
  - Daftarkan `Hitam Cuih` dan `Anggota 2` ke `kelompok_anggota` (`kelompok_magang_id`, `user_id`, `nama`, `email`).
  - Buat/ambil `Pendaftaran` kelompok (`kelompok_magang_id`, `tipe_pendaftaran = 'kelompok'`, `status = 'diterima'`) dan hubungkan `Penempatan` aktif untuk Ketua, Anggota 2, dan update `pendaftaran_id` pada penempatan Hitam Cuih (atau buat penempatan tim yang terhubung tanpa menghapus tugas individu Hitam Cuih).
    - **⚠️ PERHATIAN DATA**: Jangan menghapus `Penempatan` existing milik Hitam Cuih agar 6 `Tugas Individu` miliknya tidak hilang! Cukup perbarui `pendaftaran_id` pada penempatan aktif Hitam Cuih ke `$pendaftaranKelompok->id` (atau pastikan relasi `pendaftaran.kelompok_magang_id` menunjuk ke tim tersebut).
  - Buat 1 `Tugas` tim (`tipe = 'kelompok'`, `kelompok_magang_id = $kelompok->id`, `penempatan_id = null`) dan masukkan **ketiga user** (Ketua, Hitam Cuih, Anggota 2) ke `tugas_peserta`.
- [ ] **3.2 Jalankan seeder di database lokal** *(hanya setelah disetujui user)*:
  - `php artisan db:seed --class=HitamTimDemoSeeder`

### Tahap 4: Testing & Quality Gate
- [ ] **4.1 Tambah test di `tests/Feature/TugasTimDanIndividuTest.php`**:
  - `test_all_team_members_can_see_unsubmit_and_revise_each_others_team_submission`:
    1. Anggota 1 upload file/link ke Tugas Tim.
    2. Ketua dan Anggota 2 membuka `GET /peserta/tugas/{tugas}` $\rightarrow$ melihat status `Diserahkan`, link/file yang sama, dan nama pengunggah (`Anggota 1`).
    3. Anggota 2 memanggil `POST /peserta/tugas/{tugas}/batal-kirim` $\rightarrow$ berhasil (redirect + `ProgresLog` terhapus).
    4. Anggota 2 mengunggah ulang progres baru (`POST /peserta/tugas/{tugas}/progres`) $\rightarrow$ Ketua & Anggota 1 langsung melihat kiriman terbaru dari Anggota 2.
- [ ] **4.2 Jalankan `php artisan test`, `./vendor/bin/pint --test`, dan `npm run build`**.

---

## 6. Strategi Testing

- Feature test `TugasTimDanIndividuTest` menggunakan SQLite `:memory:` (terisolasi).
- Verifikasi manual di browser lokal dengan login sebagai **Hitam Cuih** setelah `HitamTimDemoSeeder` dijalankan:
  - Pastikan badge **`Tugas Tim (1)`** dan **`Tugas Individu (6)`** tampil berdampingan.
  - Pastikan di halaman detail Tugas Tim, pengunggah terlihat dan anggota lain bisa membatalkan / mengubah kiriman.

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **⚠️ Mengubah `pendaftaran_id` penempatan Hitam Cuih memutus data pendaftaran lama** | Riwayat pendaftaran individu lama tidak tertaut ke penempatan aktif. | Cek terlebih dahulu apakah `pendaftaran_id` lama punya data penting; jika aman, simpan `kelompok_magang_id` di pendaftaran baru atau buat `KelompokAnggota` + `TugasPeserta` tanpa merusak tugas individu yang ada. |
| **Anggota tim mencoba membatalkan tugas yang sudah berstatus `Selesai`** | Mengubah tugas yang sudah dinilai pembimbing. | Guard existing di `batalkanPengiriman()` tetap dipertahankan (`status === Selesai` atau `status_review !== Menunggu` ditolak dengan 422/error). |

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Semua anggota tim memiliki aksi yang sama pada Tugas Tim (lihat perubahan, unggah progres, batalkan pengiriman, kirim revisi).
- [ ] Halaman detail tugas peserta menampilkan nama anggota pengunggah pada Tugas Tim.
- [ ] Akun **Hitam Cuih** tergabung dalam tim berisi 3 orang dan berstatus **Anggota** (bukan Ketua), dengan minimal 1 Tugas Tim aktif dan 6 Tugas Individu tetap utuh.
- [ ] Seluruh test (`php artisan test`), `./vendor/bin/pint --test`, dan `npm run build` lulus tanpa error.
