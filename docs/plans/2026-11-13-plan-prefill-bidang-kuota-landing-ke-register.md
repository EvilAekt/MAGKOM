# Rencana Implementasi: Koreksi Logika Prefill Bidang & Kuota Landing Page ke Registrasi & Login (Sesuai Tampilan Desain Auth Depth Terbaru)

**Tanggal:** 13 November 2026  
**Status:** Draf Perencanaan (Menunggu Persetujuan Pengguna)  
**Terkait:** Alur Pendaftaran Magang (Landing Page $\rightarrow$ Register $\rightarrow$ Login $\rightarrow$ Peserta Pendaftaran)  
**Dokumentasi Acuan:** `docs/01-PRD-MagKom.md` (BR-01, AC-03A-1), `docs/07-DESIGN-SYSTEM-MAGKOM.md`  

---

## 1. Ringkasan Eksekutif & Akar Permasalahan (Root Cause)

### 1.1 Masalah yang Dilaporkan Pengguna
Pengguna mendapati adanya regresi (penurunan fungsi):
> *"sebelumnya saat memilih bidang dan kuota di landing page di menu registrasi sudah ga mengisi ulang lagi hanya mengisi yang kurang nya saja, tapi sekarang harus mengisi ulang. Buat plan nya untuk membenarkan logika nya dan kalo mau menambahkan lagi sesuaikan dengan tampilan terbaru nya"*

### 1.2 Temuan Investigasi Mendalam (Root Cause Analysis)
Pada pembaruan desain sebelumnya (commit `4730a84` - migrasi ke layout 3D Camera `layouts.auth-depth.blade.php`), view registrasi (`resources/views/auth/register.blade.php`) dan script pendukungnya (`resources/js/pages/auth/auth-depth.js`) ditulis ulang dari versi legacy. Dalam proses redesain visual tersebut, beberapa logika esensial terhapus:

1. **Komponen Kartu Ringkasan Kuota Pilihan Hilang:**  
   Pada versi lama terdapat kartu *Selected Quota Callout Card* di atas form registrasi yang menampilkan: Jurusan, Jenjang, Bidang Penempatan, Periode Mulai Magang, dan Status Kuota Tersedia/Waiting List. Pada tampilan baru kartu ini terhapus, sehingga peserta tidak mendapatkan umpan balik visual bahwa kuotanya sudah dipilih.
2. **Hidden Inputs Redirect / State Hilang:**  
   Elemen form kehilangan 5 input hidden esensial:  
   - `redirect_bulan`
   - `redirect_bidang`
   - `redirect_jurusan`
   - `redirect_tipe`
   - `redirect_jumlah`  
   Akibatnya, saat form di-submit, `AuthController@register` tidak menerima parameter pilihan kuota dari landing page, sehingga data preferensi sesi (`pendaftaran_preferensi`) tidak tersimpan.
3. **Dropdown Jenjang, Bidang, dan Jurusan Tidak Terisi Otomatis (Pre-fill):**  
   - Elemen `<select id="jenjang">`, `<select id="bidang">`, dan `<select id="jurusan">` pada Blade tidak memiliki kondisi `selected` berdasarkan query parameter `request('jenjang')`, `request('bidang')`, dan `request('jurusan')`.
   - Pada `resources/js/pages/auth/auth-depth.js`, fungsi `initCascadingEducationFields()` hanya dipicu oleh event `change` manual dan tidak dijalankan saat halaman pertama kali dimuat (`init on load`).
4. **Tipe Pendaftaran Hardcoded ke "Mandiri":**  
   - `<input type="hidden" name="tipe_pendaftaran" id="tipe_pendaftaran" value="mandiri">` selalu disetel kaku ke `mandiri`. Jika pengunjung landing page mengklik tombol kelompok ("Daftar Kelompok 3 Orang ➔"), pilihan kelompok tidak diaktifkan.
   - Container input `nama_kelompok` dan roster `anggota` untuk jalur kelompok belum dipasang pada view `register.blade.php` baru.
5. **Parameter Terputus Saat Pindah ke Login:**  
   - Tombol/tautan "Sudah punya akun? Login" menggunakan `route('login')` polos tanpa membawa query string (`request()->query()`).
   - Pada `AuthController@login`, tidak ada penangkapan data `redirect_bulan`/`bidang`/`jurusan` untuk disimpan ke dalam session `pendaftaran_preferensi` bagi akun yang sudah ada.
6. **Preferensi Sesi Tidak Terbaca di Dashboard Peserta (Langkah 2 Pemilihan Kuota):**  
   - Pada controller `PendaftaranController@index` dan view `peserta.pendaftaran.index`, kuota pilihan belum otomatis ter-select (`old('kuota_id', $preselectedKuotaId)`) saat peserta masuk ke tahap pemilihan kuota.

---

## 2. Sasaran dan Batasan Teknis

### 2.1 Sasaran
1. **Otomatisasi Penuh (Zero Redundant Input):**  
   Ketika calon peserta memilih jenjang, bidang, jurusan, periode, dan tipe (mandiri/kelompok) di landing page, form register langsung terisi otomatis. Peserta hanya perlu mengisi identitas diri yang belum ada:
   - Nama Lengkap
   - Email
   - Nomor WhatsApp (No. Telp)
   - Kata Sandi
   - Nama Sekolah / Perguruan Tinggi (Institusi)
   - NIM / NISN
   - Kode Captcha
   - *(Jika Kelompok)* Nama Kelompok & Data Rekan Tim.
2. **Harmonisasi Desain Visual DS-MagKom (Auth Depth Suite):**  
   Menghadirkan kembali *Kartu Ringkasan Kuota Pilihan* dengan desain modern yang selaras 100% dengan style auth terbaru: Sleman Navy (`#003566`, `#02275D`), aksen Emas (`#FFC300`), latar Slate Soft (`#F8FAFC`, `#EEF3FB`), border halus (`#D5DEEE`), badge status kuota pills, dan tombol/tautan "Ubah Pilihan di Landing Page ↺".
3. **Preservasi Alur Login:**  
   Jika pengguna sudah memiliki akun dan memilih "Login", pilihan kuota tetap diteruskan lewat query parameter dan disimpan ke dalam session saat login berhasil.
4. **Sinkronisasi ke Halaman Kuota Peserta:**  
   Setelah profil peserta lengkap (100%), saat membuka menu `peserta.pendaftaran.index`, kartu kuota atau dropdown otomatis mengarah ke periode yang dipilih sejak dari landing page.
5. **Kepatuhan Terhadap Aturan Repositori:**  
   - Tidak melakukan migrasi ulang stack (PHP 8.3, Laravel 10, Vanilla JS ES Modules, Vite).
   - Menjaga seluruh assertion pengujian PHPUnit (`AuthDepthVisualParityTest`, `PendaftaranKelompokTest`, `PendaftaranPesertaTest`) tetap **100% HIJAU**.

---

## 3. Rencana Perubahan Komponen & Arsitektur

```
[ Landing Page: #kuota ]
  │ User memilih Jenjang + Bulan + Bidang + Jurusan + Tipe (Mandiri/Kelompok)
  │ Klik tombol CTA: "Daftar Sekarang ➔" atau "Daftar Kelompok (X Orang) ➔"
  ▼
[ URL: /register?bulan=1&bidang=2&jenjang=smk&jurusan=3&tipe=mandiri&jumlah=1 ]
  │
  ├──► A. Pengguna Baru: Halaman Register (/register)
  │      ├─ Render Banner "🎯 Kuota Pilihan dari Landing Page" (Desain Sleman Navy/Gold)
  │      ├─ Hidden inputs: redirect_bulan, redirect_bidang, redirect_jurusan, redirect_tipe, redirect_jumlah
  │      ├─ Auto-select Dropdown: Jenjang, Bidang, Jurusan
  │      ├─ Auto-toggle Tab: Mandiri vs Kelompok (termasuk form nama_kelompok & anggota jika kelompok)
  │      └─ Submit POST /register ──► Simpan Akun + Simpan session('pendaftaran_preferensi')
  │
  └──► B. Pengguna Lama: Tautan "Sudah punya akun? Login"
         ├─ URL: /login?bulan=1&bidang=2&jenjang=smk&jurusan=3... (query param dipertahankan)
         ├─ Banner info ringkas pilihan kuota + Hidden inputs redirect
         └─ Submit POST /login ──► Auth Berhasil ──► Simpan session('pendaftaran_preferensi')
  │
  ▼
[ Portal Peserta: Lengkapi Biodata (100%) ──► Langkah 2: Pilih Kuota (peserta.pendaftaran.index) ]
  └─ Membaca session('pendaftaran_preferensi') ──► Auto-select Kuota Periode Terpilih ──► Ajukan Draf
```

---

## 4. Rincian Teknis Implementasi (File by File)

### 4.1 `resources/views/auth/register.blade.php` (Markup Blade)
1. **Kartu Ringkasan Kuota Pilihan (Selected Quota Callout Card):**
   - Menampilkan banner modern di bawah judul "Register Akun" jika `$selectedJurusan` atau `request('jurusan')` tersedia:
     - Ikon kompas/target dengan aksen emas (`#FFC300`).
     - Baris 1: Judul *"🎯 Pilihan Magang dari Landing Page"* & Badge Tipe (*Individu 1 Orang* atau *Kelompok X Orang*).
     - Baris 2: Nama Jurusan tebal & Jenjang (misal: `Rekayasa Perangkat Lunak (SMK)`), serta Bidang Penempatan (`Penempatan: Bidang Aplikasi Informatika`).
     - Baris 3: Periode Mulai (`Bulan November 2026`) & Pill Status Kuota (`🟢 Tersedia X Kursi` atau `🟡 Waiting List`).
     - Tautan: `Ubah Pilihan di Landing Page ↺` mengarah kembali ke `{{ url('/#kuota') }}`.
2. **Input Hidden Kuota & Redirect:**
   - Menyisipkan kembali ke dalam tag `<form action="{{ route('register') }}" ...>`:
     ```blade
     <input type="hidden" name="redirect_bulan" value="{{ request('bulan', $selectedBulan?->id) }}">
     <input type="hidden" name="redirect_bidang" value="{{ request('bidang', $selectedBidang?->id) }}">
     <input type="hidden" name="redirect_jurusan" value="{{ request('jurusan', $selectedJurusan?->id) }}">
     <input type="hidden" name="redirect_tipe" value="{{ request('tipe', $selectedTipe) }}">
     <input type="hidden" name="redirect_jumlah" value="{{ request('jumlah', $selectedJumlah) }}">
     ```
3. **Pre-Selection Dropdown (Jenjang, Bidang, Jurusan):**
   - `jenjang`: Seleksi otomatis opsi `smk` atau `mahasiswa` berdasarkan `old('jenjang', request('jenjang', ...))`.
   - `bidang`: Seleksi otomatis opsi bidang berdasarkan `old('bidang_id', request('bidang', $selectedBidang?->id))`.
   - `jurusan`: Seleksi otomatis opsi jurusan berdasarkan `old('jurusan_id', request('jurusan', $selectedJurusan?->id))`.
4. **Penanganan Tipe Pendaftaran (Individu vs Kelompok):**
   - Mendukung inisialisasi state awal `kelompok` jika `request('tipe') === 'kelompok'` atau `$selectedTipe === 'kelompok'`.
   - Menyediakan container field kelompok (`nama_kelompok` dan data dinamis rekan tim `anggota[i]`) sesuai aturan PRD (AC-03A-1 & BR-03).
5. **Retensi Query Parameter pada Navigasi Login:**
   - Mengubah tautan login menjadi:  
     `<a href="{{ route('login', request()->query()) }}">Login</a>`.

---

### 4.2 `resources/js/pages/auth/auth-depth.js` (JavaScript Interaktivitas)
1. **Eksekusi Otomatis pada Inisialisasi (`initCascadingEducationFields`):**
   - Memperbarui fungsi `initCascadingEducationFields` agar langsung memeriksa nilai awal (`initial value`) dari elemen `#jenjang`, `#bidang`, dan `#jurusan`.
   - Jika `#jenjang` atau `#bidang` sudah terisi dari parameter URL / server-rendered HTML, jalankan `updateJurusan()` secara otomatis saat DOM siap.
   - Memastikan `#jurusan` mempertahankan nilai `selected` yang sesuai dengan `jurusan_id` yang dipilih dari landing page.
2. **Sinkronisasi Tab Kelompok pada Inisialisasi:**
   - Memperbarui `initRegisterTypeToggle()` agar mendeteksi apakah `tipe_pendaftaran` bernilai `kelompok` pada load awal (dari `request('tipe')` atau `old('tipe_pendaftaran')`), dan secara otomatis memicu fungsi `activateKel()`.

---

### 4.3 `resources/views/auth/login.blade.php` & `app/Http/Controllers/Auth/AuthController.php`
1. **Pada `resources/views/auth/login.blade.php`:**
   - Tautan ke register mempertahankan query string:  
     `<a href="{{ route('register', request()->query()) }}">Daftar</a>`.
   - Jika dibuka dengan membawa parameter kuota (`?bulan=...&bidang=...`), sertakan input hidden:
     ```blade
     @if(request('bulan') || request('bidang') || request('jurusan'))
       <input type="hidden" name="redirect_bulan" value="{{ request('bulan') }}">
       <input type="hidden" name="redirect_bidang" value="{{ request('bidang') }}">
       <input type="hidden" name="redirect_jurusan" value="{{ request('jurusan') }}">
       <input type="hidden" name="redirect_tipe" value="{{ request('tipe', 'mandiri') }}">
       <input type="hidden" name="redirect_jumlah" value="{{ request('jumlah', 1) }}">
     @endif
     ```
2. **Pada `app/Http/Controllers/Auth/AuthController.php` (`login` method):**
   - Menambahkan penangkapan `redirect_bulan`, `redirect_bidang`, dan `redirect_jurusan` saat login berhasil, lalu menyimpannya ke session `pendaftaran_preferensi`:
     ```php
     $preferensiJurusanId = $request->input('redirect_jurusan');
     $preferensiBulanId = $request->input('redirect_bulan');
     $preferensiBidangId = $request->input('redirect_bidang');
     if ($preferensiBulanId || $preferensiJurusanId || $preferensiBidangId) {
         session([
             'pendaftaran_preferensi' => [
                 'bulan_id' => $preferensiBulanId,
                 'bidang_id' => $preferensiBidangId,
                 'jurusan_id' => $preferensiJurusanId,
                 'tipe' => $request->input('redirect_tipe', 'mandiri'),
                 'jumlah' => (int) $request->input('redirect_jumlah', 1),
             ],
         ]);
     }
     ```

---

### 4.4 `app/Http/Controllers/Peserta/PendaftaranController.php` & View `peserta.pendaftaran.index`
1. **Pada `PendaftaranController@index`:**
   - Memeriksa apakah terdapat `session('pendaftaran_preferensi')` atau `request('bulan')`.
   - Mencari objek kuota yang cocok (`$kuotaList->firstWhere('bulan_magang_id', $preferensiBulanId)`) dan mengoper variabel `$preselectedKuotaId` ke view.
2. **Pada `resources/views/peserta/pendaftaran/index.blade.php`:**
   - Mengisi opsi default dropdown:  
     `{{ (old('kuota_id', $preselectedKuotaId ?? request('kuota')) == $kuota->id) ? 'selected' : '' }}`.
   - Menyorot kartu kuota yang sesuai agar peserta langsung melihat bahwa periode pilihan mereka sudah siap diajukan tanpa perlu mencari ulang.

---

## 5. Rencana Pengujian & Quality Gate

Setiap tahapan pengujian berikut akan dijalankan menggunakan PHP 8.3 aktual dan PHPUnit:

1. **Uji Tampilan & Visual Parity Auth Depth:**
   - Menjalankan `php artisan test tests/Feature/AuthDepthVisualParityTest.php` untuk memastikan seluruh teks, label, dan layout 3D tidak ada yang terganggu.
2. **Uji Alur Registrasi Mandiri & Kelompok:**
   - Menjalankan `php artisan test tests/Feature/PendaftaranKelompokTest.php` untuk memastikan registrasi kelompok, nama kelompok, anggota, dan validasi kuota berfungsi sempurna.
   - Menjalankan `php artisan test tests/Feature/PendaftaranPesertaTest.php`.
3. **Uji Integrasi End-to-End Prefill (Fitur Baru):**
   - Menambahkan test case baru di `tests/Feature/LandingPageToRegistrationPrefillTest.php`:
     - Test 1: Mengakses `/register` dengan query parameter dari landing page menghasilkan input form terisi otomatis dan kartu pilihan kuota tampil.
     - Test 2: Mengirim form pendaftaran dengan redirect params berhasil membuat profil peserta dengan jurusan yang sesuai dan menyimpan preferensi ke session.
     - Test 3: Beralih dari `/register` ke `/login` mempertahankan parameter pilihan kuota.
     - Test 4: Login berhasil dengan redirect params menyimpan `pendaftaran_preferensi` di session peserta.
4. **Pemeriksaan Kompilasi Aset Frontend:**
   - Menjalankan `npm run build` untuk memverifikasi tidak ada sintaks error pada JavaScript dan CSS.

---

## 6. Pertanyaan Konfirmasi untuk Pengguna

Sebelum kami mulai mengeksekusi perubahan kode:
1. Apakah Anda menyetujui rencana perbaikan logika prefill dan desain kartu ringkasan kuota di atas?
2. Apakah ada informasi tambahan khusus yang ingin ditampilkan pada kartu ringkasan pilihan di halaman registrasi (misalnya durasi estimasi magang atau catatan lainnya)?

