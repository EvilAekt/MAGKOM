# Master Engineering Plan: Halaman Kebijakan Privasi & Syarat Ketentuan serta Penanganan Akun Terkunci Sementara (Account Lockout & Rate Limit)

**Tanggal Dokumen**: 2026-11-11  
**Status**: DRAFT FOR APPROVAL (Menunggu Persetujuan Pengguna)  
**Tingkat Prioritas**: P1 (Kepatuhan Hukum Privasi Data, Perlindungan Keamanan Brute-Force, dan Penyelarasan UX Autentikasi)  

---

## 1. Tujuan

Rencana kerja ini bertujuan untuk mengimplementasikan dua pilar antarmuka dan keamanan penting pada ekosistem autentikasi MagKom:

1. **📜 Halaman & Modal Kebijakan Privasi serta Syarat & Ketentuan**:
   - Memutakhirkan dan memperluas halaman publik [`/kebijakan-privasi`](file:///g:/MAGKOM/resources/views/public/kebijakan-privasi.blade.php) agar memuat dua domain regulasi esensial: **Kebijakan Privasi & Perlindungan Data Pribadi (UU PDP No. 27/2022)** dan **Syarat & Ketentuan Magang (Tata Tertib, Hak, dan Kewajiban Peserta)** secara terpadu.
   - Menyediakan **Modal Drawer Cepat (In-Page Modal)** pada form Login dan Register sehingga calon peserta yang sedang mengisi data pendaftaran dapat membaca ringkasan klausul privasi dan syarat ketentuan secara instan tanpa kehilangan data input akibat reload atau berpindah tab.
   - Menghubungkan seluruh tautan `#privasi` di halaman Login dan Register ke modal baca cepat serta tautan resmi halaman lengkap.

2. **⏳ Penanganan Akun Terkunci Sementara (Account Lockout & Rate Limit Peringatan Keamanan)**:
   - Menghadirkan penanganan ramah pengguna saat sistem mendeteksi aktivitas mencurigakan atau salah memasukkan kredensial login $\ge 5$ kali per menit (*brute-force protection*).
   - Menampilkan **Kartu Peringatan Keamanan Interaktif (Inline Lockout Banner)** di halaman Login lengkap dengan **Live Countdown Timer** (*hitung mundur detik real-time*, misal: *"Akses akun dibekukan sementara. Silakan tunggu 58 detik sebelum mencoba kembali"*).
   - Menyediakan tombol pemulihan cepat **"Lupa Kata Sandi Anda?"** yang langsung mengarahkan pengguna ke alur pemulihan resmi jika mereka memang lupa password.
   - Menyelaraskan tampilan halaman error [`resources/views/errors/429.blade.php`](file:///g:/MAGKOM/resources/views/errors/429.blade.php) (*Too Many Requests*) agar mengadopsi estetika *Auth Depth* resmi MagKom (Sleman Navy/Gold) dan bebas dari kesan error teknis server yang membingungkan.

---

## 2. Asumsi & Pertanyaan Terbuka

- **Asumsi 1 (Pengaturan CMS & Kebijakan)**: Kebijakan privasi telah didukung oleh sistem CMS pengaturan dinamis ([`\App\Support\Pengaturan`](file:///g:/MAGKOM/resources/views/public/kebijakan-privasi.blade.php#L39)), sehingga struktur baru untuk Syarat & Ketentuan dapat memanfaatkan fallback default yang lengkap dan tetap dapat dikustomisasi oleh SuperAdmin.
- **Asumsi 2 (Ambang Batas Keamanan Lockout)**: Sesuai konfigurasi `RouteServiceProvider`, ambang batas percobaan login adalah **5 percobaan per menit per email + IP** (`throttle:login`). Lockout ini bersifat sementara (60 detik) untuk mencegah penyerangan otomatis (*rate limiting*).
- **Asumsi 3 (Zero Data Loss pada Pendaftaran)**: Modal baca cepat di form Register dirancang agar saat calon peserta mengetuk link "Kebijakan Privasi", form register tidak tertutup atau di-reset, melainkan modal melayang mulus di atas formulir.

---

## 3. Evaluasi Pendekatan Teknis

### Kebutuhan 1: Kebijakan Privasi & Syarat Ketentuan

#### Opsi 1A (Rekomendasi Utama): Arsitektur Dual-Layer (Modal Baca Cepat di Form Auth + Halaman Publik Lengkap dengan Tab Navigasi)
- **Mekanisme**:
  - Di halaman publik `/kebijakan-privasi`: Menambahkan navigasi tab bergaya modern antara **"Kebijakan Privasi (UU PDP)"** dan **"Syarat & Ketentuan Magang"**.
  - Di form Login & Register: Menambahkan komponen modal Blade ringan `_modal_privasi_syarat.blade.php` dengan backdrop gelap dan drawer scrollable elegan. Saat link diklik, modal terbuka seketika. Terdapat pula tombol *"Buka Halaman Lengkap ↗"*.
- **Kelebihan**:
  - Calon peserta tidak kehilangan data pendaftaran yang sudah diketik di form Register.
  - Memenuhi standar kepatuhan hukum privasi (UU PDP) dengan transparansi tinggi.
  - Sesuai kaidah WCAG 2.1 AA (dapat ditutup dengan tombol ESC atau klik di luar).
- **Kekurangan**: Memerlukan komponen Blade modal parsial.

#### Opsi 1B: Mengarahkan Tautan Langsung ke Tab Browser Baru (`target="_blank"`)
- **Kelebihan**: Implementasi cepat tanpa modal.
- **Kekurangan**: Mengganggu alur pengguna (*context switching*), membuka tab baru yang berpotensi membingungkan pengguna ponsel/smartphone.

---

### Kebutuhan 2: Akun Terkunci Sementara (Account Lockout & Rate Limit)

#### Opsi 2A (Rekomendasi Utama): Penanganan Cerdas di Controller dengan Flash State & Live JS Countdown + Modern 429 Fallback
- **Mekanisme**:
  - Pada [`AuthController@login`](file:///g:/MAGKOM/app/Http/Controllers/Auth/AuthController.php#L45), lakukan pengecekan status `RateLimiter::tooManyAttempts('login', ...)` sebelum validasi agar dapat menangkap jumlah detik sisa (`RateLimiter::availableIn(...)`).
  - Kirimkan data sesi `lockout_seconds` ke halaman login jika akun terkunci.
  - Di tampilan `login.blade.php`, render kartu peringatan bertema *Security Guard*:
    ```html
    <div class="lockout-alert" data-seconds="{{ session('lockout_seconds') }}">
      <strong>Akses Akun Terkunci Sementara</strong>
      <p>Terlalu banyak percobaan masuk yang gagal. Demi keamanan, silakan tunggu <span id="countdown">60</span> detik.</p>
    </div>
    ```
  - JavaScript di `auth-depth.js` secara otomatis menghitung mundur per detik dan mengaktifkan kembali tombol masuk saat waktu habis.
  - Halaman `errors/429.blade.php` diperbarui dengan visual kartu Sleman beraksen gembok/keamanan.
- **Kelebihan**:
  - Sangat ramah pengguna (*human-friendly security feedback*).
  - Mengurangi kepanikan pengguna awam dan mengarahkan mereka secara konstruktif ke solusi (menunggu atau reset password).
  - Tetap 100% aman karena pembatasan di server tetap aktif.
- **Kekurangan**: Memerlukan sedikit logika penanganan state di controller.

#### Opsi 2B: Hanya Mengandalkan Halaman Error 429 Bawaan Laravel
- **Kelebihan**: Tidak mengubah controller.
- **Kekurangan**: Pengguna hanya melihat layar error statis yang terkesan sistem sedang rusak.

**Keputusan Arsitektur**: Menggunakan **Opsi 1A** dan **Opsi 2A**.

---

## 4. Rincian Perubahan File

| No | File | Aksi | Ringkasan Perubahan |
|---|---|---|---|
| 1 | [`resources/views/public/kebijakan-privasi.blade.php`](file:///g:/MAGKOM/resources/views/public/kebijakan-privasi.blade.php) | **Modify** | Menambahkan tab navigasi interaktif untuk beralih antara "Kebijakan Privasi (UU PDP No. 27/2022)" dan "Syarat & Ketentuan Magang Diskominfo Sleman" (tata tertib, kewajiban, hak, dan pembatalan magang). |
| 2 | [`resources/views/auth/partials/_modal_privasi_syarat.blade.php`](file:///g:/MAGKOM/resources/views/auth/partials/_modal_privasi_syarat.blade.php) | **Create** | Komponen modal Blade mandiri untuk membaca ringkasan Kebijakan Privasi dan Syarat Ketentuan langsung di atas formulir pendaftaran/login tanpa reload. |
| 3 | [`resources/views/auth/register.blade.php`](file:///g:/MAGKOM/resources/views/auth/register.blade.php) | **Modify** | Menghubungkan link `Kebijakan Privasi` ke pemicu modal `data-action="open-modal-privasi"` dan menyertakan include modal di akhir halaman. |
| 4 | [`resources/views/auth/login.blade.php`](file:///g:/MAGKOM/resources/views/auth/login.blade.php) | **Modify** | Menghubungkan link privasi footer ke modal, serta menambahkan kontainer peringatan akun terkunci interaktif jika terdapat flash `lockout_seconds`. |
| 5 | [`resources/views/errors/429.blade.php`](file:///g:/MAGKOM/resources/views/errors/429.blade.php) | **Modify** | Memperbarui desain visual halaman error 429 dengan tema resmi MagKom (ikon keamanan shield/lock, penjelasan ramah, countdown, dan tombol pemulihan sandi). |
| 6 | [`app/Http/Controllers/Auth/AuthController.php`](file:///g:/MAGKOM/app/Http/Controllers/Auth/AuthController.php) | **Modify** | Memeriksa limit rate limiter pada `login()` untuk memberikan feedback sisa detik penguncian akun secara presisi via session flash. |
| 7 | [`resources/js/pages/auth/auth-depth.js`](file:///g:/MAGKOM/resources/js/pages/auth/auth-depth.js) | **Modify** | Menambahkan fungsi modular `initPrivacyTermsModal()` (buka/tutup modal, ESC key) dan `initLockoutCountdown()` (hitung mundur live detik pada banner login). |
| 8 | [`resources/css/pages/auth/auth-depth.css`](file:///g:/MAGKOM/resources/css/pages/auth/auth-depth.css) | **Modify** | Menambahkan styling modal privasi (`.auth-modal-backdrop`, `.auth-modal-card`, scrollbar kustom) dan styling alert peringatan lockout. |
| 9 | [`tests/Feature/AuthSecurityAndPolicyTest.php`](file:///g:/MAGKOM/tests/Feature/AuthSecurityAndPolicyTest.php) | **Create** | Rangkaian pengujian otomatis untuk memvalidasi aksesibilitas Kebijakan Privasi & Syarat Ketentuan serta proteksi brute-force lockout. |

---

## 5. Langkah-Langkah Implementasi

- [ ] **Langkah 1: Perluas Halaman Publik `resources/views/public/kebijakan-privasi.blade.php`**
  - Tambahkan switch tab header: **Tab 1: Kebijakan Privasi & PDP** dan **Tab 2: Syarat & Ketentuan Magang**.
  - Muat pasal-pasal standar Syarat & Ketentuan:
    1. Syarat Umum Peserta (kualifikasi SMK/Mahasiswa, integritas, dan dokumen sah).
    2. Tata Tertib & Jam Kerja Kedinasan (kehadiran, pakaian rapi kedinasan, etika birokrasi).
    3. Kewajiban Logbook Mingguan & Penyelesaian Tugas Pembimbing.
    4. Kerahasiaan Informasi dan Keamanan Data Pemerintah (Non-Disclosure).
    5. Ketentuan Evaluasi, Sertifikat Magang, dan Sanksi Pelanggaran.

- [ ] **Langkah 2: Buat Komponen Modal di `resources/views/auth/partials/_modal_privasi_syarat.blade.php`**
  - Desain modal popup ber-z-index tinggi (`z-index: 9999`) dengan latar belakang semi-transparan gelap (`rgba(1, 24, 58, 0.7)`).
  - Kartu dialog putih elegan sudut melengkung `18px`, tombol tutup silang `[×]`, dan navigasi tab ringkas antara Privasi dan Syarat Ketentuan.
  - Tombol aksi: *"Saya Mengerti & Setuju"* dan *"Lihat Dokumen Lengkap ↗"*.

- [ ] **Langkah 3: Integrasikan Modal pada `login.blade.php` dan `register.blade.php`**
  - Hubungkan link `Kebijakan Privasi` di form register dan login dengan atribut `data-action="open-modal-privasi"`.
  - Sisipkan `@include('auth.partials._modal_privasi_syarat')` di bagian bawah form.

- [ ] **Langkah 4: Tambahkan Penanganan Lockout Cerdas pada `AuthController.php`**
  - Pada awal method `login()`, cek apakah upaya login telah melebihi batas menggunakan `RateLimiter::tooManyAttempts($throttleKey, 5)`.
  - Jika ya:
    - Ambil sisa detik penahanan: `$seconds = RateLimiter::availableIn($throttleKey);`.
    - Catat audit trail percobaan login berlebih (*brute-force prevention*).
    - Lakukan `return back()->with('lockout_seconds', $seconds)->with('error_lockout', ...)->withInput();`.
  - Jika belum melampaui limit, catat percobaan gagal dengan `RateLimiter::hit($throttleKey, 60);`.
  - Saat login berhasil, bersihkan limiter dengan `RateLimiter::clear($throttleKey);`.

- [ ] **Langkah 5: Tampilkan Inline Alert Lockout & Timer di `login.blade.php`**
  - Tambahkan blok komponen peringatan lockout di bagian atas input email pada form login yang hanya muncul jika `session('lockout_seconds')` aktif.
  - Tampilkan icon gembok pengaman, judul peringatan, dan elemen hitung mundur detik: `<span data-lockout-timer>{{ session('lockout_seconds') }}</span> detik`.
  - Tampilkan link cepat ke pemulihan kata sandi (`route('password.request')`).

- [ ] **Langkah 6: Implementasikan Logika JS & Styling di `auth-depth.js` dan `auth-depth.css`**
  - `initPrivacyTermsModal()`: Buka modal saat link diklik, tutup saat klik tombol silang / klik backdrop / tombol ESC.
  - `initLockoutCountdown()`: Jika elemen timer terdeteksi, kurangi nilai detik setiap 1000ms. Saat mencapai 0, ubah teks menjadi *"Anda sekarang dapat mencoba masuk kembali."* dan aktifkan kembali tombol submit.

- [ ] **Langkah 7: Selaraskan Tampilan Halaman Error `resources/views/errors/429.blade.php`**
  - Perbarui layout error 429 dengan warna navy-gold resmi MagKom, badge status keamanan, pesan informatif bahwa ini adalah perlindungan brute-force, dan tombol kembali ke Login serta Lupa Kata Sandi.

- [ ] **Langkah 8: Jalankan Quality Gates Lengkap**
  - `npm run build` (Kompilasi aset)
  - `vendor/bin/pint --test` (Format PSR-12)
  - `phpstan analyse --memory-limit=1G` (Analisis statis)
  - `artisan test tests/Feature/AuthSecurityAndPolicyTest.php` (Pengujian fungsional)

---

## 6. Strategi Testing

1. **Feature Test Otomatis (`tests/Feature/AuthSecurityAndPolicyTest.php`)**:
   - `test_halaman_kebijakan_privasi_dan_syarat_ketentuan_dapat_diakses_publik`: Memastikan respons HTTP 200, konten pasal privasi dan syarat ketentuan tampil lengkap.
   - `test_modal_kebijakan_privasi_tersedia_di_halaman_login_dan_register`: Memastikan markup modal dan pemicu data-action ada di view.
   - `test_percobaan_login_melebihi_batas_memicu_peringatan_lockout_dan_sisa_waktu`: Mensimulasikan 5 kali kegagalan login berturut-turut, memverifikasi kemunculan session `lockout_seconds` dan pesan keamanan.
   - `test_halaman_error_429_memuat_desain_dan_tautan_pemulihan`: Memverifikasi respons dan elemen halaman 429.

---

## 7. Risiko & Mitigasi

- **Risiko Manipulasi Klien (Client-side Countdown Bypassing)**: Timer countdown di frontend murni sebagai panduan visual pengguna. Validasi keamanan utama tetap berada di sisi backend (`RateLimiter` Laravel di level IP dan email), sehingga manipulasi JavaScript tidak dapat menembus proteksi sistem.
- **Risiko Aksesibilitas**: Modal dilengkapi atribut aksesibilitas ARIA (`role="dialog"`, `aria-modal="true"`, `aria-labelledby`) dan navigasi keyboard (penutupan dengan tombol ESC).

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Halaman `/kebijakan-privasi` memuat dua tab interaktif: Kebijakan Privasi (UU PDP) dan Syarat & Ketentuan Magang.
- [ ] Calon peserta di halaman Register dan Login dapat membaca ringkasan privasi & ketentuan melalui modal popup tanpa reload/pindah halaman.
- [ ] Saat gagal login 5 kali berturut-turut, form login menampilkan kartu peringatan akun terkunci dengan countdown detik live.
- [ ] Tombol submit login dinonaktifkan secara anggun selama waktu pembekuan sementara berlangsung.
- [ ] Halaman error HTTP 429 memiliki tampilan selaras dengan standar visual portal MagKom.
- [ ] Kompilasi aset Vite `npm run build` sukses.
- [ ] Standar format PSR-12 Pint dan PHPStan Level 5 lulus 100%.
- [ ] Rangkaian tes otomatis pada `AuthSecurityAndPolicyTest.php` lulus sepenuhnya.

