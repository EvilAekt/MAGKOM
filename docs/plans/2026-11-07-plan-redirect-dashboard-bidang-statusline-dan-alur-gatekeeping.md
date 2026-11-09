# Rencana Kerja: Redirect Registrasi ke Dashboard, Info Bidang Statusline, dan Gatekeeping Alur Pendaftaran

## 1. Latar Belakang & Kebutuhan Pengguna
Berdasarkan permintaan pengguna terbaru:
1. **Redirect Pasca-Registrasi**:
   - Setelah peserta baru menyelesaikan formulir registrasi akun, sistem harus langsung mengarahkan pengguna ke **Dashboard Peserta** (`route('peserta.dashboard')`), bukan ke menu pendaftaran ataupun profil.
2. **Informasi Pilihan Bidang & Tipe di Statusline Dashboard**:
   - Pada baris status horizontal (*statusline ribbon*) di Dashboard Peserta, selain menampilkan **Status Magang** dan **Tipe Pendaftaran** (Mandiri / Kelompok), harus ada informasi yang jelas mengenai **Bidang Kedinasan** yang dipilih/dituju peserta (berdasarkan penempatan, kuota pendaftaran, atau relasi bidang dari jurusan profil peserta).
3. **Gatekeeping Bertahap pada Alur Stepper (Panduan Alur Pendaftaran Magang)**:
   - **Langkah 1 (Lengkapi Biodata & Profil Peserta)**:
     - Calon peserta magang harus melengkapi seluruh data wajib profil terlebih dahulu sebelum dapat melanjutkan ke tahap pemilihan periode/kuota.
   - **Langkah 2 (Pilih Periode & Kuota Magang)**:
     - Jika Langkah 1 **belum lengkap**: Langkah 2 dalam status **Terkunci (*Locked / Disabled*)**, tidak dapat diakses ataupun diklik untuk memilih kuota.
     - Jika Langkah 1 **sudah lengkap**: Langkah 2 **Terbuka (*Active*)** dan dapat diklik menuju pemilihan kuota.
   - **Langkah 3 (Unggah Berkas & Ajukan Pendaftaran)**:
     - Jika peserta **belum memilih bulan/kuota** (belum ada draf pendaftaran yang dibuat): Langkah 3 dalam status **Terkunci (*Locked / Disabled*)**, tidak dapat diakses ke tahap pemberkasan.
     - Jika peserta **sudah memilih bulan/kuota**: Langkah 3 **Terbuka (*Active*)** dan link langsung mengarahkan peserta ke halaman unggah berkas pendaftaran (`route('peserta.pendaftaran.detail', $pendaftaran)`).

---

## 2. Analisis Arsitektur & Perubahan Komponen

### A. Alur Registrasi (`app/Http/Controllers/Auth/AuthController.php`)
- **Masalah Saat Ini**: Method `register()` mengarahkan pengguna ke `route('peserta.profil.show')`.
- **Perubahan**:
  - Ubah target pengalihan (*redirect*) menjadi:
    ```php
    return redirect()->route('peserta.dashboard')
        ->with('success', $suksesMsg);
    ```
  - Tetap pertahankan penyimpanan session preferensi pendaftaran (`pendaftaran_preferensi`) dan pesan notifikasi sukses.
- **Dampak Pengujian**: Perbarui assertion di `tests/Feature/PendaftaranPesertaTest.php` dari `assertRedirect(route('peserta.profil.show'))` menjadi `assertRedirect(route('peserta.dashboard'))`.

### B. Statusline Dashboard & Ekstraksi Bidang
1. **Controller (`app/Http/Controllers/Peserta/DashboardController.php`)**:
   - Pastikan relasi `$profil->jurusan.bidang` dan `$pendaftaran->kuota.jurusan.bidang` dimuat secara eager-loading.
   - Ekstraksi nama bidang dinas:
     ```php
     $bidangTerpilih = $penempatan?->bidang?->nama_bidang
         ?? $pendaftaran?->kuota?->jurusan?->bidang?->pluck('nama_bidang')->implode(', ')
         ?? $profil?->jurusan?->bidang?->pluck('nama_bidang')->implode(', ')
         ?? 'Menyesuaikan Jurusan';
     ```
   - Kirimkan variabel `$bidangTerpilih` ke tampilan `peserta.dashboard`.

2. **Tampilan Statusline (`resources/views/peserta/dashboard.blade.php`)**:
   - Tambahkan kartu ketiga pada `.saas-statusline`:
     - Icon: Gedung kedinasan / bidang dinas (`building / landmark / office`).
     - Label: `PILIHAN BIDANG` atau `BIDANG KEDINASAN`.
     - Value: `$bidangTerpilih`.
     - Badge/Sub-info: Jurusan terkait jika tersedia.
   - Kolom Tipe Pendaftaran:
     - Menampilkan `Kelompok (X Anggota)` atau `Mandiri (Individu)`.

### C. Logika Gatekeeping Alur Stepper (`_phase_onboarding.blade.php`)
1. **Kondisi Langkah 1 (Biodata & Profil)**:
   - Evaluasi kelengkapan:
     ```php
     $isProfilLengkap = $profil && !empty($profil->nim_nisn) && $profil->nim_nisn !== '-'
         && !empty($profil->institusi) && $profil->institusi !== 'Institusi Belum Diatur'
         && !empty($profil->jurusan_id) && !empty($profil->jenjang)
         && !empty($profil->no_hp) && $profil->no_hp !== '-';
     ```
   - Status:
     - Jika lengkap: Badge centang hijau (`completed`), link menuju `route('peserta.profil.show')` untuk pratinjau.
     - Jika belum: Badge nomor 1 biru aktif (`active`), link menuju `route('peserta.profil.show')` untuk pengisian.

2. **Kondisi Langkah 2 (Pilih Periode & Kuota)**:
   - Evaluasi: Apakah profil lengkap dan apakah sudah ada pendaftaran?
     ```php
     $hasPendaftaran = (bool) $pendaftaran;
     $canPilihKuota = $isProfilLengkap;
     ```
   - Status:
     - Jika `$hasPendaftaran`: Badge centang hijau (`completed`).
     - Jika `$canPilihKuota && !$hasPendaftaran`: Badge nomor 2 biru aktif (`active`), link menuju `route('peserta.pendaftaran.index')`.
     - Jika `!$canPilihKuota`: Status **Terkunci (`is-locked`)**, `aria-disabled="true"`, pointer-events disabled, badge abu-abu, menampilkan indikator gembok kecil atau label *"Selesaikan Langkah 1 Terlebih Dahulu"*.

3. **Kondisi Langkah 3 (Unggah Berkas & Ajukan Pendaftaran)**:
   - Evaluasi: Apakah sudah memilih periode/kuota (sudah ada `$pendaftaran`)?
     ```php
     $canUnggahBerkas = $hasPendaftaran;
     ```
   - Status:
     - Jika `$hasPendaftaran`: Badge nomor 3 aktif / completed, link mengarah ke detail pendaftaran `route('peserta.pendaftaran.detail', $pendaftaran)`.
     - Jika `!$hasPendaftaran`: Status **Terkunci (`is-locked`)**, `aria-disabled="true"`, pointer-events disabled, badge abu-abu, menampilkan indikator gembok kecil atau label *"Pilih Periode Kuota Terlebih Dahulu"*.

### D. Styling Kunci & Hover Interaktif Stepper
- File CSS / scoped style di `resources/views/peserta/dashboard.blade.php`:
  - Styling `.saas-step-item.is-locked`:
    - `background-color: #F8FAFC; border-color: #E2E8F0; opacity: 0.65; cursor: not-allowed;`
    - Badge nomor terkunci berwarna abu-abu `#94A3B8`.
    - Indikator status gembok halus di sebelah kanan judul langkah.
  - Styling `.saas-step-item.completed`:
    - Border hijau lembut `#10B981` saat hover, badge centang hijau terang.
  - Responsive: Tetap fluid dan rapi di desktop maupun mobile.

---

## 3. Rencana Pengujian & Quality Gates

1. **Pengujian Otomatis (PHPUnit & Pest - PHP 8.3 Laragon)**:
   - `tests/Feature/PendaftaranPesertaTest.php`: Memverifikasi redirect registrasi ke `peserta.dashboard`.
   - `tests/Feature/PesertaFullPortalFeatureTest.php`: Memverifikasi tampilan dashboard dan fase onboarding.
   - `tests/Feature/GentelellaDashboardRevampTest.php`: Memverifikasi komponen x-panel dashboard peserta.
   - `tests/Feature/PesertaPendaftaranFlowTest.php`: Memverifikasi seluruh alur pendaftaran tidak terganggu.
2. **Quality Gate Tooling**:
   - `php artisan view:clear`
   - `./vendor/bin/pint --test`
3. **Verifikasi Browser**:
   - Uji registrasi baru: langsung mendarat di Dashboard Peserta.
   - Uji statusline: melihat 3 kolom (Status Magang, Pilihan Bidang, Tipe Pendaftaran).
   - Uji stepper: langkah 2 dan 3 terkunci sampai langkah sebelumnya diselesaikan.

