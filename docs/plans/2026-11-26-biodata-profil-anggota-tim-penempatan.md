# Rencana Kerja: Biodata & Profil Lengkap Seluruh Anggota Tim pada Portal Peserta dan Penempatan Administrasi

**Tanggal:** 2026-11-26  
**Branch Saat Ini:** `test-2` (terdapat perubahan lokal aktif — dilindungi dan tidak ditimpa)  
**Status:** Menunggu Persetujuan Pengguna  

---

## 1. Tujuan

Mewujudkan alur terpadu untuk pendaftaran bertipe **Tim (Kelompok)** sehingga:
1. **Aktivasi Login Anggota Saat Diterima**: Akun seluruh rekan anggota tim baru dapat digunakan untuk login setelah pendaftaran tim diputuskan **Diterima** (`StatusPendaftaranEnum::Diterima`) oleh Administrator (`PlacementService::terimaBerkas()`), serta tetap menjamin pembuatan/penautan akun bagi data anggota yang belum memiliki `user_id` (termasuk data seeder demo).
2. **Pengisian Foto & Biodata Mandiri oleh Anggota**: Setiap anggota tim yang telah diterima dapat login ke portal peserta (baik sebelum maupun sesudah penetapan pembimbing lapangan) untuk mengunggah pasfoto dan melengkapi biodata profilnya sendiri (`peserta.profil.show` & `peserta.profil.simpan`), melihat status pendaftaran timnya secara *read-only*, serta melihat daftar anggota tim dengan peran yang akurat (bukan hardcoded sebagai Ketua).
3. **Visibilitas Profil & Biodata Seluruh Anggota di Penempatan Administrasi**: Pada modul Penempatan (`admin.penempatan.antrean.show` untuk antrean belum ditempatkan, serta `admin.penempatan.show` untuk yang sudah ditempatkan), Admin & SuperAdmin (*read-only*) dapat melihat pasfoto, status kelengkapan biodata, dan detail biodata lengkap **seluruh anggota tim (Ketua + semua Anggota)**, bukan hanya Ketua Tim.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis
1. **Skema Database Eksisting Sudah Memadai**: Tidak diperlukan perubahan migrasi/skema database (`⚠️` *zero destructive schema change*). Tabel `users` (kolom `status_aktif`), `peserta_profil`, `kelompok_magang`, dan `kelompok_anggota` (kolom `user_id`, `nama`, `nim_nisn`, `no_hp`, `email`) sudah tersedia.
2. **Kredensial Awal Anggota**: Saat registrasi awal oleh Ketua Tim di `AuthController::register()`, akun `User` anggota tetap dibuat dengan kata sandi yang diinput pada formulir anggota (atau fallback password ketua jika field password anggota kosong), `email_verified_at = now()`, dan `status_aktif = false` hingga pendaftaran berstatus `Diterima`.
3. **Akses Sebelum vs Sesudah Penempatan Pembimbing**:
   - Setelah pendaftaran `Diterima` (status masih di antrean penempatan): anggota **sudah bisa login** untuk melengkapi foto & biodata (`peserta.profil.*`) dan memantau status pendaftaran tim, namun modul pelaksanaan magang (`Logbook`, `Tugas`, `Surat Selesai`) tetap terkunci oleh middleware `peserta.diterima` sampai pembimbing & penempatan ditetapkan (`AssignPesertaAction`).
4. **Kesesuaian Aturan `AGENTS.md`**: Seluruh perubahan CSS tetap di `resources/css/`, seluruh JS tetap di `resources/js/` (ES Modules tanpa inline script/handler), dan perubahan markup Blade pada file-file yang tercantum di Bagian 4 akan dimintakan persetujuan eksplisit bersamaan dengan persetujuan rencana ini.

### Pertanyaan Terbuka (Perlu Konfirmasi Sebelum Eksekusi)
1. **Tombol Detail pada Tab "Sudah Ditempatkan" (`admin.penempatan.index`)**:
   - Pada kondisi codebase `test-2` saat ini, tabel **Belum Ditempatkan** sudah memiliki tombol `Detail` ke `admin.penempatan.antrean.show`, sedangkan tabel **Sudah Ditempatkan** hanya memiliki tombol `Ganti Pembimbing` dan `tests/Feature/AdminPenempatanDuaTahapFeatureTest.php` secara eksplisit menguji bahwa link `admin.penempatan.show` tidak muncul di `admin.penempatan.index`.
   - **Konfirmasi yang dibutuhkan**: Apakah di tab **Sudah Ditempatkan** juga perlu ditambahkan tombol **Detail / Profil Tim** menuju `admin.penempatan.show` (dengan memperbarui assertion pada `AdminPenempatanDuaTahapFeatureTest`), atau fokus tombol Detail tetap di antrean **Belum Ditempatkan** ditambah pembenahan tampilan seluruh anggota pada halaman `admin.penempatan.show`?
2. **Pesan Penolakan Login Anggota Sebelum Diterima**:
   - Saat ini pendaftaran anggota baru tidak memblokir login karena `status_aktif` langsung `true`. Dengan logika baru (`status_aktif = false` sampai pendaftaran tim `Diterima`), apakah pesan error saat anggota mencoba login sebelum diterima cukup menampilkan pesan informatif *"Akun anggota tim Anda akan aktif dan dapat digunakan untuk login setelah pendaftaran tim dinyatakan Diterima oleh Admin."*?

---

## 3. Pendekatan Teknis

### A. Mekanisme Aktivasi Akses Login Anggota Saat Diterima

| Opsi | Deskripsi | Kelebihan | Kekurangan |
| :--- | :--- | :--- | :--- |
| **Opsi 1: Aktivasi via `users.status_aktif` (Direkomendasikan)** | Saat `AuthController::register()` membuat akun anggota tim, set `status_aktif = false`. Saat Admin memutuskan `Diterima` di `PlacementService::terimaBerkas()`, sistem mengaktifkan `status_aktif = true` untuk seluruh anggota (serta membuatkan `User` + `PesertaProfil` dasar bila ada baris `kelompok_anggota` dengan `user_id = null`). | Memanfaatkan seluruh pagar keamanan eksisting (`AuthController::login`, `SocialiteController`, `ResetPasswordController`, `EnsureAccountActive`) tanpa duplikasi middleware; mencegah anggota mengakses reset password atau OAuth sebelum diterima. | Perlu membedakan pesan penolakan login antara anggota tim yang belum diterima vs akun yang dinonaktifkan permanen oleh admin (mudah ditangani dengan query ringan saat gagal/nonaktif). |
| **Opsi 2: Middleware Gate Terpisah di Portal Peserta** | Tetap set `status_aktif = true` saat registrasi, lalu blokir rute peserta di middleware baru bila status `pendaftaran` tim belum `Diterima`. | Tidak menyentuh `status_aktif` saat registrasi. | Anggota tetap bisa login (membuat sesi) lalu langsung dilempar/ditolak; tidak sesuai dengan pernyataan kebutuhan *"ketika peserta sudah diterima maka akun dari anggota sudah bisa login"*; reset password & OAuth tetap terbuka sebelum diterima. |

**Keputusan Terpilih:** **Opsi 1**, karena paling aman secara *security boundary*, konsisten dengan kontrak `EnsureAccountActive`, dan selaras persis dengan perilaku bisnis yang diminta.

---

### B. Penyajian Biodata & Foto Seluruh Anggota Tim di Halaman Detail Antrean (`antrean-show.blade.php`) & Detail Penempatan (`show.blade.php`)

| Opsi | Deskripsi | Kelebihan | Kekurangan |
| :--- | :--- | :--- | :--- |
| **Opsi A: Panel Ringkasan Daftar Anggota + Kartu Profil Lengkap per Anggota (Tabs/Selector + Fallback Stacked View) (Direkomendasikan)** | Di `antrean-show.blade.php` (dan `_show_kelompok.blade.php`), jika pendaftaran bertipe Tim: (1) Hero menampilkan Nama Tim & daftar anggota ringkas; (2) Bagian profil menampilkan **tab/pilihan per anggota** (Ketua + Anggota 1..N) dengan thumbnail foto, badge peran (`Ketua Tim` / `Anggota Tim`), dan indikator status (`Profil Lengkap` / `Belum Lengkap`), atau daftar kartu terstruktur yang menampilkan **foto & seluruh 8 field biodata** milik tiap anggota. | Skalabel untuk 2–4 anggota tanpa membuat halaman memanjang berlebihan; admin dapat berpindah profil anggota dalam 1 klik atau melihat status kelengkapan semua anggota sekaligus; kompatibel penuh dengan aturan CSS di `resources/css/` & JS ES Module di `resources/js/` (serta ramah aksesibilitas keyboard/ARIA `role="tablist"`). | Memerlukan markup tab/panel di Blade dan event listener ringan di JS modular. |
| **Opsi B: Modal Popup Detail per Baris Tabel** | Tabel anggota tetap ringkas, klik tombol "Lihat Profil" membuka modal AJAX/hidden DOM untuk tiap anggota. | Menghemat ruang vertikal. | Lebih banyak klik untuk memeriksa 3–4 orang, lebih kompleks secara state & aksesibilitas fokus modal. |

**Keputusan Terpilih:** **Opsi A** (dengan struktur **Daftar Ringkas Status Tim** + **Panel Biodata & Foto Interaktif per Anggota** menggunakan tab berbasis JS ES Module yang juga merender seluruh biodata anggota secara semantik di DOM agar tetap mudah diakses *screen reader*, *print*, dan *feature test* tanpa tergantung AJAX tambahan).

---

## 4. Perubahan File

> **Catatan Kepatuhan `AGENTS.md`**: Perubahan pada file Blade di bawah ini membutuhkan persetujuan eksplisit pengguna sebelum eksekusi dimulai. Seluruh styling diletakkan di `resources/css/` dan interaksi JS di `resources/js/`.

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Http/Controllers/Auth/AuthController.php` | Modify | Set `status_aktif = false` untuk akun `User` anggota tim saat registrasi awal; saat login gagal karena akun anggota tim belum diterima, tampilkan pesan informatif bahwa akun aktif setelah pendaftaran diterima. |
| `app/Http/Controllers/Auth/SocialiteController.php` | Modify | Selaraskan pesan penolakan OAuth jika `status_aktif = false` disebabkan pendaftaran tim belum diterima. |
| `app/Services/PlacementService.php` | Modify | Di dalam transaksi `terimaBerkas()`, aktifkan akun seluruh `kelompokMagang->anggota` (`status_aktif = true`, pastikan `email_verified_at`), buatkan/tautkan `User` + `PesertaProfil` dasar jika `user_id` masih kosong, dan kirim notifikasi SSE ke tiap anggota agar segera melengkapi foto & biodata. |
| `database/seeders/TimBelumDitempatkanDemoSeeder.php` | Modify | Pastikan tiap `anggota` pada 3 tim demo berstatus `Diterima` juga dibuatkan `User` (`status_aktif = true`), `PesertaProfil` dasar, dan relasi `kelompok_anggota.user_id` agar langsung siap diuji. |
| `app/Queries/Peserta/Profil/PesertaProfilQuery.php` | Modify | Gunakan `$user->kelompokAktif` dengan eager load `ketua.profil` dan `anggota.user.profil`, serta kirim flag `$isKetuaTim` agar tampilan tim di halaman profil peserta akurat untuk anggota maupun ketua. |
| `app/Actions/Peserta/Profil/UpdatePesertaProfilAction.php` | Modify | Sinkronkan pembaruan `no_hp` dan `email` peserta ke record `KelompokAnggota` terkait (`where('user_id', $lockedUser->id)`) dan fallback jika `PesertaProfil` anggota belum memiliki data akademik awal. |
| `app/Http/Requests/PesertaProfilRequest.php` | Modify | Pastikan `prepareForValidation()` menangani kasus profil anggota yang field akademiknya belum terisi atau sudah terkunci dari registrasi tanpa error validasi tak terduga. |
| `app/Policies/PendaftaranPolicy.php` | Modify | Izinkan anggota yang tergabung dalam `kelompokMagang` melihat detail `Pendaftaran` timnya secara *read-only* (`view`), sementara `update` & `cancel` tetap khusus Ketua Tim. |
| `app/Queries/Peserta/Pendaftaran/PendaftaranIndexQuery.php` | Modify | Sertakan pengecekan keanggotaan kelompok (`kelompokAnggota`) pada `hasPendaftaran` & daftar pendaftaran aktif agar anggota yang sudah diterima tidak diarahkan membuat pendaftaran individu ganda. |
| `resources/views/peserta/profil/show.blade.php` | Modify | Perbaiki kartu "Tim" agar menampilkan peran dinamis (`Ketua Tim` vs `Anggota Tim`), selalu menempatkan Ketua Tim di baris #1, menandai akun yang sedang login `(Anda)`, dan menampilkan foto/status kelengkapan tiap anggota. |
| `app/Http/Controllers/Admin/PenempatanController.php` | Modify | Pada `showAntrean()` dan `show()`, eager load `kelompokMagang.ketua.profil.jurusan` dan `kelompokMagang.anggota.user.profil.jurusan` (serta `pendaftaran.kelompokMagang.*`), lalu susun koleksi terstruktur seluruh anggota tim (Ketua + Anggota) beserta kelengkapan profilnya untuk view. |
| `resources/views/admin/penempatan/antrean-show.blade.php` | Modify | Untuk pendaftaran bertipe Tim: tampilkan identitas tim di hero, panel tab/pemilih profil seluruh anggota tim (Ketua + Anggota) lengkap dengan pasfoto, status kelengkapan profil, dan 8 field biodata masing-masing anggota, serta tabel ringkas daftar anggota yang menampilkan status kelengkapan profil. |
| `resources/views/admin/penempatan/partials/_show_kelompok.blade.php` | Modify | Tambahkan navigasi/switcher profil & biodata seluruh anggota tim (atau kartu rekan tim dengan pratinjau biodata & link ke masing-masing `penempatan` rekan tim) agar administrasi dapat meninjau profil seluruh anggota dari halaman detail penempatan tim. |
| `resources/views/admin/penempatan/index.blade.php` | Modify *(Opsional sesuai jawaban Q1)* | Jika disetujui pada Q1: tambahkan tombol `Detail` menuju `admin.penempatan.show` pada baris tabel `Sudah Ditempatkan`. |
| `resources/css/pages/admin/penempatan-index.css` | Modify | Tambahkan styling scoped (`.antrean-member-tabs`, `.antrean-member-card`, `.antrean-completeness-pill`, dll.) untuk panel profil multi-anggota di `antrean-show` & `_show_kelompok`. |
| `resources/js/pages/admin/penempatan-antrean-show.js` | Modify | Tambahkan handler ES Module yang bersih untuk perpindahan tab profil anggota tim di halaman `antrean-show` (CSP-compliant, tanpa inline JS). |
| `resources/js/pages/admin/penempatan-show.js` *(atau modul existing di `vite.config.js`)* | Modify / Verify | Pastikan jika ada tab switcher di `_show_kelompok.blade.php`, inisialisasi JS tetap melalui bundle Vite existing. |
| `tests/Feature/TimAnggotaLoginBiodataDanPenempatanTest.php` | Create | Feature test komprehensif untuk seluruh alur: anggota gagal login sebelum diterima -> admin menerima -> anggota bisa login & simpan foto + biodata -> admin & superadmin melihat foto + biodata seluruh anggota di halaman antrean & detail penempatan. |
| `tests/Feature/TimBelumDitempatkanDemoSeederTest.php` | Modify | Tambah assertion bahwa seluruh anggota pada tim demo memiliki `user_id`, `status_aktif = true`, dan biodatanya tampil di `admin.penempatan.antrean.show`. |

---

## 5. Langkah Implementasi

### Fase 1: Siklus Hidup Akun Anggota Tim (Registrasi & Penerimaan)
- [ ] **1.1** Perbarui `app/Http/Controllers/Auth/AuthController.php`:
  - Pada `register()`, set `$anggotaUser->status_aktif = false` saat membuat akun anggota tim.
  - Pada `login()`, sebelum `Auth::attempt()` atau ketika autentikasi gagal untuk akun nonaktif (`status_aktif === false`), cek apakah user adalah `Peserta` yang terhubung di `kelompok_anggota` dengan pendaftaran belum berstatus `Diterima`; bila ya, kembalikan pesan error informatif khusus (*"Akun anggota tim akan aktif dan dapat login setelah pendaftaran tim dinyatakan Diterima oleh Admin."*).
- [ ] **1.2** Perbarui `app/Http/Controllers/Auth/SocialiteController.php`:
  - Terapkan pesan informatif yang sama saat akun `status_aktif === false` adalah anggota kelompok yang pendaftarannya belum diterima.
- [ ] **1.3** Perbarui `app/Services/PlacementService.php` pada `terimaBerkas()`:
  - Muat `kelompokMagang.anggota.user` dan `user.profil` di dalam transaksi DB.
  - Untuk setiap anggota di `$p->kelompokMagang->anggota`:
    - Jika `$anggota->user` sudah ada: perbarui `status_aktif = true` dan isi `email_verified_at = $anggota->user->email_verified_at ?? now()`.
    - Jika `$anggota->user_id` masih `null` (misalnya data lama/seeder): cari `User` berdasarkan email anggota atau buat `User` baru (`role = RoleEnum::Peserta`, `status_aktif = true`, `email_verified_at = now()`, `must_change_password = false` atau kata sandi acak aman yang tidak di-log), buatkan `PesertaProfil` dasar mewarisi `jenjang`, `jurusan_id`, `institusi` dari profil ketua / kuota pendaftaran + `nim_nisn` & `no_hp` anggota, lalu simpan `user_id` ke `kelompok_anggota`.
    - Kirim notifikasi `RealtimeEventTypeEnum::StatusPendaftaran` ke `$anggota->user_id` untuk menginformasikan bahwa pendaftaran tim diterima dan mereka dapat login untuk melengkapi foto & biodata.
- [ ] **1.4** Perbarui `database/seeders/TimBelumDitempatkanDemoSeeder.php`:
  - Buat/perbarui `User` (`status_aktif = true`) dan `PesertaProfil` dasar (lengkap variasi: sebagian sudah melengkapi kontak darurat/alamat, sebagian belum agar terlihat realistis) untuk setiap anggota di `TimBelumDitempatkanDemoSeeder`, dan tautkan `user_id` pada `kelompok_anggota`.

### Fase 2: Portal Peserta — Login, Biodata, Foto & Tim untuk Anggota
- [ ] **2.1** Perbarui `app/Queries/Peserta/Profil/PesertaProfilQuery.php`:
  - Ganti pencarian `$kelompokMagang` agar menggunakan `$user->kelompokAktif` dengan eager load `ketua.profil.jurusan` dan `anggota.user.profil.jurusan`.
  - Kembalikan `$isKetuaTim = $kelompokMagang ? ((int) $kelompokMagang->ketua_user_id === (int) $user->id) : true;`.
- [ ] **2.2** Perbarui `app/Actions/Peserta/Profil/UpdatePesertaProfilAction.php` dan `app/Http/Requests/PesertaProfilRequest.php`:
  - Di `UpdatePesertaProfilAction::execute()`, setelah menyimpan `PesertaProfil`, sinkronkan `no_hp` dan `email` ke tabel `kelompok_anggota` (`where('user_id', $lockedUser->id)`) bila user merupakan anggota kelompok.
  - Di `PesertaProfilRequest::prepareForValidation()`, pastikan jika profil anggota sudah punya `institusi`/`nim_nisn`/`jenjang`/`jurusan_id` dari registrasi, field tersebut tetap terkunci aman; jika belum ada (kasus profil baru), gunakan data registrasi kelompok sebagai fallback bila tidak dikirim.
- [ ] **2.3** Perbarui `app/Policies/PendaftaranPolicy.php` dan `app/Queries/Peserta/Pendaftaran/PendaftaranIndexQuery.php`:
  - Pada `PendaftaranPolicy::view()`, izinkan akses baca (`return true`) jika `$pendaftaran->kelompokMagang?->anggota()->where('user_id', $user->id)->exists()`.
  - Pada `PendaftaranIndexQuery::getIndexData()`, deteksi pendaftaran tim milik anggota via `KelompokAnggota` agar anggota melihat status pendaftaran timnya (dan tidak memicu alur daftar baru ganda).
- [ ] **2.4** Perbarui `resources/views/peserta/profil/show.blade.php` *(butuh persetujuan Blade)*:
  - Perbaiki subtitle kartu Tim agar dinamis: menampilkan *"Ketua Tim / Penanggung Jawab"* jika `$isKetuaTim`, atau *"Anggota Tim"* jika anggota.
  - Pada tabel Tim, baris #1 selalu menampilkan data `$kelompokMagang->ketua` (dengan penanda `(Anda)` bila `$isKetuaTim`), dan baris berikutnya menampilkan `$kelompokMagang->anggota` (dengan penanda `(Anda)` pada baris anggota yang sedang login) beserta pasfoto mini / status kelengkapan biodata.

### Fase 3: Penempatan Administrasi — Profil & Biodata Seluruh Anggota Tim
- [ ] **3.1** Perbarui `app/Http/Controllers/Admin/PenempatanController.php`:
  - Pada `showAntrean()`: tambahkan eager loading `'kelompokMagang.ketua.profil.jurusan'` dan `'kelompokMagang.anggota.user.profil.jurusan'`. Susun collection `$anggotaTimProfil` (berisi Ketua di urutan pertama + seluruh Anggota dengan foto URL, status kelengkapan `hitungKelengkapan()`, dan objek `user` + `profil` + fallback field dari `kelompok_anggota` seperti `nama`, `nim_nisn`, `no_hp`, `email`).
  - Pada `show()`: tambahkan eager loading `'pendaftaran.kelompokMagang.ketua.profil.jurusan'` dan `'pendaftaran.kelompokMagang.anggota.user.profil.jurusan'` serta kirim data seluruh anggota tim (beserta link ke `penempatan` masing-masing anggota yang sudah ditempatkan).
- [ ] **3.2** Perbarui `resources/views/admin/penempatan/antrean-show.blade.php` *(butuh persetujuan Blade)*:
  - Untuk pendaftaran bertipe Tim:
    - Tampilkan nama kelompok pada hero title dengan sub-info Ketua & jumlah anggota.
    - Di panel **Biodata & Foto Peserta Tim**: tambahkan bar pemilih anggota (`tab` / tombol pil untuk Ketua & setiap Anggota Tim dengan foto mini + indikator status kelengkapan biodata).
    - Render panel profil lengkap untuk **setiap anggota** (Ketua + Anggota 1..N) berisi foto (atau inisial fallback), nama, peran (`Ketua Tim` / `Anggota Tim`), status kelengkapan biodata, Email Akun, NIM/NISN, Jenjang Pendidikan, Jurusan/Prodi, No. HP/WA, Pembimbing Sekolah/Kampus, Alamat Domisili, dan Kontak Darurat.
    - Perbarui panel tabel **Anggota Tim** di bawahnya agar menampilkan kolom tambahan: Peran, Nama & Email, NIM/NISN, No. HP, serta Badge Status Profil (`Lengkap 100%` / `Perlu Lengkapi`), dan klik baris dapat mengaktifkan tab profil anggota tersebut.
- [ ] **3.3** Perbarui `resources/views/admin/penempatan/partials/_show_kelompok.blade.php` *(butuh persetujuan Blade)*:
  - Tambahkan panel **Profil & Biodata Anggota Tim** (menggunakan pola konsisten dengan `show.blade.php` / `antrean-show.blade.php`) sehingga ketika administrasi membuka detail penempatan anggota/ketua tim, seluruh anggota tim beserta foto, ringkasan biodata, status kelengkapan profil, dan tombol `Lihat Detail Penempatan` ke masing-masing anggota dapat diakses langsung.
- [ ] **3.4** *(Kondisional — menunggu konfirmasi Q1)* Jika disetujui pada Pertanyaan Terbuka #1: tambahkan tombol `Detail` pada `resources/views/admin/penempatan/index.blade.php` di tab `Sudah Ditempatkan` dan sesuaikan `tests/Feature/AdminPenempatanDuaTahapFeatureTest.php`.
- [ ] **3.5** Perbarui aset frontend via Vite:
  - Tambahkan class CSS scoped di `resources/css/pages/admin/penempatan-index.css` untuk tab pemilih anggota tim, badge peran (`Ketua Tim` / `Anggota Tim`), badge kelengkapan biodata (`Lengkap` / `Belum Lengkap`), dan tampilan foto avatar mini di tabel anggota.
  - Perbarui `resources/js/pages/admin/penempatan-antrean-show.js` untuk mengelola interaksi perpindahan tab profil anggota tim (`aria-selected`, kelas aktif, dan `hidden` attribute) serta klik dari tabel anggota ke tab profil terkait (CSP-compliant, tanpa inline JS). Jika `_show_kelompok.blade.php` memakai tab/accordion interaktif, tempatkan script di `resources/js/pages/admin/penempatan-index.js` atau modul terdaftar di `vite.config.js`.
  - Jalankan `npm run build` untuk memverifikasi kompilasi aset Vite.

### Fase 4: Verifikasi & Quality Gate
- [ ] **4.1** Buat/perbarui feature tests di `tests/Feature/TimAnggotaLoginBiodataDanPenempatanTest.php` dan `tests/Feature/TimBelumDitempatkanDemoSeederTest.php`.
- [ ] **4.2** Jalankan seluruh test suite terkait (`php artisan test --filter=...`), `./vendor/bin/phpstan analyse --memory-limit=1G`, `./vendor/bin/pint --test`, dan `npm run build`.
- [ ] **4.3** Lakukan inspeksi visual/browser pada halaman `/admin/penempatan/antrean/{id}`, `/admin/penempatan/{id}`, dan `/peserta/profil` sebagai anggota tim (desktop & mobile).

---

## 6. Strategi Testing

Semua test dijalankan secara terisolasi menggunakan konfigurasi PHPUnit proyek (`RefreshDatabase`):

1. **Alur Registrasi & Pembatasan Login Sebelum Diterima (Sad & Happy Path)**:
   - *Sad Path*: Registrasi tim menghasilkan akun ketua (`status_aktif = true`) dan akun anggota (`status_aktif = false`). Saat pendaftaran masih `diajukan` / `berkas_belum_lengkap`, percobaan login menggunakan email & password anggota ditolak dengan pesan informatif bahwa akun aktif setelah tim diterima.
   - *Happy Path*: Admin memutus `terima` (`PlacementService::terimaBerkas`). Verifikasi `users.status_aktif = true` untuk seluruh anggota tim. Setelah itu anggota berhasil login (`POST /login`) dan masuk ke `peserta.dashboard`.
   - *Backfill Legacy/Seeder*: Jika `kelompok_anggota` belum punya `user_id` saat `terimaBerkas()`, sistem otomatis membuatkan `User` aktif + `PesertaProfil` dasar dan mengisi `user_id`.
2. **Pengisian Foto & Biodata Mandiri oleh Anggota Tim**:
   - Anggota tim mengakses `GET /peserta/profil` (`200 OK`), melihat namanya sendiri sebagai `Anggota` dan ketua sebagai `Ketua Tim` (tidak salah label sebagai Ketua).
   - Anggota mengunggah foto (`UploadedFile::fake()->image('pasfoto.jpg')`) dan mengisi `nama_pembimbing_sekolah`, `no_hp`, `alamat`, `nama_kontak_darurat`, `no_kontak_darurat`, `hubungan_kontak_darurat` via `POST /peserta/profil`. Verifikasi file tersimpan di `storage/public/profil-peserta`, `peserta_profil` terupdate, dan `kelompok_anggota.no_hp` ikut tersinkronisasi.
   - Anggota tim dapat melihat detail pendaftaran timnya (`GET /peserta/pendaftaran/{id}` -> `200 OK`), namun ditolak (`403`) jika mencoba mengubah/mengajukan ulang atau membatalkan pendaftaran ketua, serta ditolak (`403`) mengakses `peserta.tugas.index` / `peserta.logbook.index` sebelum penempatan ditetapkan.
3. **Tampilan Biodata & Foto Seluruh Anggota di Penempatan Administrasi**:
   - Admin membuka `GET /admin/penempatan/antrean/{pendaftaran}`: seluruh nama anggota, NIM/NISN, email, no HP, alamat, pembimbing sekolah, kontak darurat, dan URL foto anggota yang sudah mengunggah foto tampil pada response HTML.
   - SuperAdmin membuka halaman yang sama: tetap dapat melihat seluruh biodata & foto anggota dalam mode `Read-Only` tanpa tombol `Tetapkan Pembimbing`.
   - Non-admin/peserta lain ditolak `403`.
   - Setelah penempatan ditetapkan (`AssignPesertaAction`), Admin & SuperAdmin membuka `GET /admin/penempatan/{penempatan}`: informasi seluruh anggota tim dan profilnya dapat diakses dengan benar (dan tanpa N+1 query berlebihan).
4. **Regresi Eksisting**:
   - Jalankan `RegisterPasswordConfirmationTest`, `AdminPenempatanDuaTahapFeatureTest`, `AdminPenempatanKonektivitasEndToEndTest`, `TugasTimDanIndividuTest`, dan `TimBelumDitempatkanDemoSeederTest` untuk memastikan tidak ada regresi.

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Test eksisting mengasumsikan akun anggota langsung bisa login sebelum diterima** | Test lama gagal jika menguji login anggota pra-penerimaan. | Dari penelusuran saat ini, `RegisterPasswordConfirmationTest` hanya memeriksa `assertDatabaseHas('users', ...)`. Tetap jalankan seluruh `Auth` & `Peserta` test suite setelah implementasi untuk memastikan nol regresi. |
| **Data anggota lama/seeder dengan `user_id = null` atau profil belum dibuat** | Error null pointer di view atau anggota gagal login setelah diterima. | `PlacementService::terimaBerkas()` melakukan *idempotent ensure-or-create* `User` + `PesertaProfil`, dan view `antrean-show.blade.php` menggunakan fallback dari data `kelompok_anggota` (`nama`, `nim_nisn`, `no_hp`, `email`). |
| **N+1 Query saat memuat foto & biodata seluruh anggota tim** | Penurunan performa halaman penempatan. | Gunakan eager loading `kelompokMagang.ketua.profil.jurusan` dan `kelompokMagang.anggota.user.profil.jurusan` di `PenempatanController::showAntrean()` dan `show()`. |
| **Keamanan & Privasi (IDOR / Mass Assignment / File Upload)** | Akses profil orang lain atau unggah berkas berbahaya. | `PesertaProfilRequest` memvalidasi `image` + `mimes:jpeg,png,jpg,webp` + `max:2048` dan mengunci field akademik immutable; `PenempatanController` memproteksi akses via `PenempatanPolicy`; `PendaftaranPolicy` membatasi anggota hanya untuk melihat (`view`) pendaftaran timnya sendiri; tidak ada password/credential plaintext di log/view/audit. |
| **Aksesibilitas & Responsivitas UI (WCAG 2.1 AA)** | Tab profil anggota tidak dapat dinavigasi keyboard atau pecah di mobile. | Gunakan atribut ARIA standar (`role="tablist"`, `role="tab"`, `role="tabpanel"`, `aria-selected`, `hidden`), focus ring yang jelas, dan layout grid responsif di `penempatan-index.css`. |
| **Pelanggaran batas `AGENTS.md` (CSS/JS di Blade atau build ke working tree)** | Ditolak saat review. | Nol `<style>`, nol `<script>`, dan nol `on*` inline handler di Blade. Semua CSS di `resources/css/` dan JS di `resources/js/` via Vite. |

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Akun anggota tim yang baru didaftarkan tidak dapat login sebelum pendaftaran tim berstatus `Diterima`, dan otomatis aktif (`status_aktif = true`) saat admin menerima pendaftaran tim.
- [ ] Anggota tim yang pendaftarannya sudah `Diterima` dapat login, mengakses halaman profil (`peserta.profil.show`), mengunggah pasfoto, serta menyimpan biodata lengkapnya sendiri; peran di kartu tim tampil akurat (`Ketua Tim` vs `Anggota Tim`).
- [ ] Halaman detail antrean penempatan (`admin.penempatan.antrean.show`) dan detail penempatan (`admin.penempatan.show`) menampilkan pasfoto, status kelengkapan profil, dan biodata lengkap seluruh anggota tim (Ketua + seluruh Anggota) bagi Admin & SuperAdmin (*read-only*).
- [ ] `TimBelumDitempatkanDemoSeeder` membuatkan akun & profil anggota yang valid sehingga langsung dapat didemonstrasikan.
- [ ] Tidak ada CSS/JS inline di file Blade dan `npm run build` berjalan sukses.
- [ ] Seluruh PHPUnit Feature Test baru & regresi terkait, Larastan (`phpstan`), dan Pint (`pint --test`) lulus pada runtime PHP 8.3.

---

## Saran Lanjutan (Di Luar Scope Utama)

1. **Pengingat Kelengkapan Biodata Anggota**: Menambahkan indikator pada halaman **Verifikasi Pendaftaran** (`admin.pendaftaran.show`) dan **Daftar Antrean Penempatan** (`admin.penempatan.index`) berupa badge mini seperti `2/3 Profil Tim Lengkap` agar admin langsung mengetahui tim mana yang seluruh anggotanya sudah melengkapi foto & biodata sebelum penempatan.
2. **Notifikasi Email Aktivasi Akun Anggota**: Mengirimkan email notifikasi otomatis (via queue) kepada alamat email masing-masing anggota tim saat pendaftaran tim diterima oleh admin, berisi ajakan untuk login dan melengkapi pasfoto serta biodata.
