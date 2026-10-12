# Plan: Profil, Pengaturan Akun, dan Preferensi Dashboard Administrasi, Pembimbing, dan SuperAdmin

- **Tanggal**: 2026-10-13
- **Status**: Draft (Menunggu Persetujuan)
- **Author**: Antigravity (web-planning)
- **Referensi Acuan**: Modul Profil (`peserta.profil.show`) & Pengaturan (`peserta.pengaturan.index`), Design System DS-MagKom

---

## 1. Tujuan

Menyediakan fitur **Profil Pengguna (Biodata & Identitas Staf)**, **Pengaturan Akun (Ganti Kata Sandi Mandiri & Informasi Akun)**, serta **Preferensi Antarmuka (Tema Terang/Gelap/Ikuti Sistem & Pilihan Bahasa)** pada 3 role staf internal MagKom:
1. **Administrasi (Admin)**
2. **Pembimbing Lapangan**
3. **SuperAdmin**

Seluruh fitur ini mengadopsi standar visual, struktur layout 2 kolom responsif, interaktivitas pasfoto, dan mekanisme penyimpanan preferensi yang telah sukses diimplementasikan pada role **Peserta Magang**, disesuaikan dengan konteks kedinasan staf Pemerintah Kabupaten Sleman.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis & Fungsional:
1. **Data yang Dapat Diubah Mandiri**: Staf (Admin, Pembimbing, SuperAdmin) dapat mengubah secara mandiri:
   - Nama Lengkap
   - Nomor WhatsApp / Kontak Aktif (`no_telp`)
   - Berkas Pasfoto Profil (`foto`)
   - Kata Sandi Akun (dengan verifikasi kata sandi lama)
   - Preferensi Tampilan (Terang / Gelap / Ikuti Sistem) dan Bahasa Portal (Indonesia / English)
2. **Data Terkunci (Read-Only)**: Demi integritas sistem dan mencegah eskalasi wewenang (OWASP Privilege Escalation), kolom **Email Kedinasan**, **Peran/Role**, dan **Unit Kerja / Bidang Dinas** ditampilkan dalam mode *read-only* dengan badge resmi. Perubahan email dan bidang staf tetap menjadi wewenang eksklusif SuperAdmin melalui menu Manajemen Pengguna/Staf (`superadmin.staf.index`).
3. **Penyimpanan Foto Staf**: Berkas foto staf disimpan di disk `public` pada direktori `profil-staf/` dengan sanitasi nama berkas unik acak (`staf_{id}_{timestamp}_{hash}.{ext}`) dan batasan ukuran 2 MB.
4. **Pemisahan Pengaturan SuperAdmin**: SuperAdmin telah memiliki menu `/superadmin/pengaturan` untuk *Parameter Konfigurasi Sistem Global* (Master Kuota, Snapshot, Audit Purge). Oleh karena itu, menu akun pribadi SuperAdmin dialokasikan ke rute `/superadmin/akun` (nama rute: `superadmin.akun.index`) agar tidak bentrok dengan pengaturan sistem.

### Pertanyaan Terbuka (Klarifikasi Opsional):
- *Apakah staf pembimbing memerlukan indikator khusus kapasitas bimbingan (misal: "3 / 5 mentee aktif") pada kartu profil mereka?* *(Saat ini diasumsikan YA dan dimasukkan ke widget kartu identitas pembimbing)*.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Teknis:
- **Opsi A (Single Monolithic Shared View & Controller)**: Menggunakan satu URL generic `/profil` dan `/pengaturan` untuk semua role staf dengan banyak percabangan `if ($role === ...)` di controller dan view Blade.
  - *Kelebihan*: Jumlah file sedikit.
  - *Kekurangan*: Melanggar konvensi repositori MagKom yang memisahkan route prefix (`/admin/*`, `/pembimbing/*`, `/superadmin/*`), layout sidebar berbeda, hak akses bercampur, dan berpotensi regresi middleware role.
- **Opsi B (Role-Scoped Controllers & Views dengan Shared Logic Component - Terpilih)**:
  - Menyediakan controller khusus per namespace:
    - `App\Http\Controllers\Admin\ProfilController` & `PengaturanController`
    - `App\Http\Controllers\Pembimbing\ProfilController` & `PengaturanController`
    - `App\Http\Controllers\SuperAdmin\ProfilController` & `AkunController`
  - Memanfaatkan `UpdatePasswordSettingRequest` yang di-generalize untuk semua role terautentikasi.
  - Membuat shared Blade component / styling seragam yang mengadopsi 100% pola kartu identitas, widget avatar, form password, dan kartu tema 3-opsi seperti pada Peserta Magang.
  - Menambahkan engine CSS `[data-theme="dark"]` dan script anti-flash FOUC pada `resources/views/layouts/dashboard.blade.php`.
  - *Alasan Pemilihan*: Menjaga konsistensi arsitektur modular yang sudah mapan di codebase MagKom, menjaga keamanan middleware per role, dan memudahkan unit testing terisolasi.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `database/migrations/2026_10_13_000001_add_foto_to_users_table.php` | Create | Migrasi penambahan kolom `foto` (nullable, string 255) pada tabel `users`. |
| `app/Models/User.php` | Modify | Menambahkan `'foto'` ke `$fillable`, menambahkan accessor `foto_url` dengan fallback aman. |
| `app/Http/Requests/UpdatePasswordSettingRequest.php` | Modify | Memperbarui `authorize()` agar berlaku untuk seluruh pengguna terautentikasi (`return (bool) $this->user();`). |
| `app/Http/Requests/StaffProfilRequest.php` | Create | FormRequest validasi pembaruan profil staf (`nama`, `no_telp`, `foto`, `hapus_foto`). |
| `app/Http/Controllers/Admin/ProfilController.php` | Create | Controller halaman profil Administrator (menampilkan data diri, penugasan administrasi, upload foto). |
| `app/Http/Controllers/Admin/PengaturanController.php` | Create | Controller pengaturan akun Administrator (ganti kata sandi mandiri dengan audit log & preferensi tema/bahasa). |
| `app/Http/Controllers/Pembimbing/ProfilController.php` | Create | Controller halaman profil Pembimbing (menampilkan data diri, bidang, statistik mentee aktif, upload foto). |
| `app/Http/Controllers/Pembimbing/PengaturanController.php` | Create | Controller pengaturan akun Pembimbing (ganti password mandiri & preferensi tema/bahasa). |
| `app/Http/Controllers/SuperAdmin/ProfilController.php` | Create | Controller halaman profil SuperAdmin (menampilkan identitas eksekutif, status akses penuh, upload foto). |
| `app/Http/Controllers/SuperAdmin/AkunController.php` | Create | Controller akun & preferensi pribadi SuperAdmin (ganti password mandiri & preferensi tema/bahasa). |
| `routes/web.php` | Modify | Mendaftarkan route profil & pengaturan untuk admin, pembimbing, dan superadmin. |
| `resources/views/layouts/dashboard.blade.php` | Modify | Update avatar topbar, tambahkan link Profil & Pengaturan di dropdown akun sesuai peran, update sidebar Admin, Pembimbing, SuperAdmin, serta integrasi token `[data-theme="dark"]` dan script anti-flash. |
| `resources/views/admin/profil/show.blade.php` | Create | View profil 2 kolom untuk Administrasi (kartu identitas kiri + formulir data diri kanan). |
| `resources/views/admin/pengaturan/index.blade.php` | Create | View pengaturan akun 2 kolom untuk Administrasi (info akun, ganti kata sandi, kartu preferensi tema & bahasa). |
| `resources/views/pembimbing/profil/show.blade.php` | Create | View profil 2 kolom untuk Pembimbing (kartu identitas + widget kapasitas bimbingan + formulir data diri). |
| `resources/views/pembimbing/pengaturan/index.blade.php` | Create | View pengaturan akun 2 kolom untuk Pembimbing (info akun, ganti password, preferensi tema & bahasa). |
| `resources/views/superadmin/profil/show.blade.php` | Create | View profil 2 kolom untuk SuperAdmin (kartu identitas + status tata kelola + formulir data diri). |
| `resources/views/superadmin/akun/index.blade.php` | Create | View pengaturan akun & preferensi untuk SuperAdmin (info akun, ganti password mandiri, preferensi tema & bahasa). |
| `tests/Feature/StaffProfileAndSettingsTest.php` | Create | Automated Feature Test komprehensif menguji akses, validasi, upload foto, ganti password, audit log, dan preferensi tema untuk ketiga role. |

---

## 5. Langkah Implementasi

### Fase 1: Basis Data & Model (Database & Model Layer)
- [x] **Langkah 1.1**: Buat file migrasi `database/migrations/2026_10_13_000001_add_foto_to_users_table.php` untuk menambahkan kolom `foto` (string 255, nullable, after `email`) pada tabel `users`.
- [x] **Langkah 1.2**: Modifikasi model `app/Models/User.php`:
  - Tambahkan `'foto'` pada array `$fillable`.
  - Buat accessor `getFotoUrlAttribute(): ?string` yang memeriksa keberadaan berkas di `Storage::disk('public')` dan menghasilkan URL publik.
  - Sediakan fallback transparan untuk peserta (`$this->profil?->foto_url`).

### Fase 2: Form Request & Validasi Keamanan (Security & Request Layer)
- [x] **Langkah 2.1**: Ubah otorisasi di `app/Http/Requests/UpdatePasswordSettingRequest.php` dari `isPeserta()` menjadi `return $this->user() !== null;` agar dapat digunakan oleh semua staf.
- [x] **Langkah 2.2**: Buat FormRequest `app/Http/Requests/StaffProfilRequest.php` dengan aturan:
  - `nama`: `['required', 'string', 'max:100']`
  - `no_telp`: `['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]+$/']`
  - `foto`: `['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048']`
  - `hapus_foto`: `['nullable', 'boolean']`

### Fase 3: Controller & Alur Bisnis (Backend Controller Layer)
- [x] **Langkah 3.1**: Buat `app/Http/Controllers/Admin/ProfilController.php`:
  - Method `show()`: mengambil user aktif, data bidang, statistik operasional administrasi.
  - Method `update()`: menangani unggah/hapus pasfoto staf di disk `public/profil-staf`, update `nama` dan `no_telp`, redirect dengan flash message sukses.
- [x] **Langkah 3.2**: Buat `app/Http/Controllers/Admin/PengaturanController.php`:
  - Method `index()`: menampilkan info akun, status sesi login, preferensi tema (`tema_tampilan`) dan bahasa (`bahasa_portal`).
  - Method `updatePassword()`: verifikasi kata sandi lama, pencegahan password sama, enkripsi hash kata sandi baru, pencatatan audit log `ganti_password_mandiri` via `AuditService::auth()`.
  - Method `updatePreferensi()`: validasi bahasa (`id`, `en`) dan tema (`terang`, `gelap`, `sistem`), penyimpanan ke session dan *forever cookie*.
- [x] **Langkah 3.3**: Buat `app/Http/Controllers/Pembimbing/ProfilController.php` & `PengaturanController.php`:
  - Logika serupa dengan pengayaan data mentee aktif pembimbing (`mentee_count`, kapasitas maksimal).
- [x] **Langkah 3.4**: Buat `app/Http/Controllers/SuperAdmin/ProfilController.php` & `AkunController.php`:
  - Menangani profil personal dan akun/preferensi SuperAdmin tanpa mengganggu controller `SuperAdmin\PengaturanController` (pengaturan sistem).

### Fase 4: Routing & Registrasi Rute (Routing Layer)
- [x] **Langkah 4.1**: Tambahkan rute admin di `routes/web.php` dalam grup `prefix('admin')->name('admin.')`:
  - `GET /admin/profil` (`admin.profil.show`)
  - `POST /admin/profil` (`admin.profil.update`)
  - `GET /admin/pengaturan` (`admin.pengaturan.index`)
  - `PUT /admin/pengaturan/password` (`admin.pengaturan.password.update`)
  - `POST /admin/pengaturan/preferensi` (`admin.pengaturan.preferensi.update`)
- [x] **Langkah 4.2**: Tambahkan rute pembimbing di `routes/web.php` dalam grup `prefix('pembimbing')->name('pembimbing.')`:
  - `GET /pembimbing/profil` (`pembimbing.profil.show`)
  - `POST /pembimbing/profil` (`pembimbing.profil.update`)
  - `GET /pembimbing/pengaturan` (`pembimbing.pengaturan.index`)
  - `PUT /pembimbing/pengaturan/password` (`pembimbing.pengaturan.password.update`)
  - `POST /pembimbing/pengaturan/preferensi` (`pembimbing.pengaturan.preferensi.update`)
- [x] **Langkah 4.3**: Tambahkan rute superadmin di `routes/web.php` dalam grup `prefix('superadmin')->name('superadmin.')`:
  - `GET /superadmin/profil` (`superadmin.profil.show`)
  - `POST /superadmin/profil` (`superadmin.profil.update`)
  - `GET /superadmin/akun` (`superadmin.akun.index`)
  - `PUT /superadmin/akun/password` (`superadmin.akun.password.update`)
  - `POST /superadmin/akun/preferensi` (`superadmin.akun.preferensi.update`)

### Fase 5: Tampilan Antarmuka & Penyelarasan Layout (UI & Blade Layer)
- [x] **Langkah 5.1**: Perbarui `resources/views/layouts/dashboard.blade.php`:
  - Tambahkan skrip anti-flash FOUC di bagian `<head>` untuk membaca preferensi tema dari cookie/localStorage.
  - Tambahkan CSS token `[data-theme="dark"]` untuk sidebar, topbar, cards, tabel, dan form controls.
  - Perbarui avatar topbar agar menampilkan foto profil jika ada (`$currentUser->foto_url`), atau inisial jika kosong.
  - Perbarui dropdown user topbar: tampilkan tombol navigasi "Profil Saya" dan "Pengaturan Akun" sesuai role yang sedang login.
  - Tambahkan menu navigasi Profil dan Pengaturan di sidebar Admin, Pembimbing, dan SuperAdmin.
- [x] **Langkah 5.2**: Buat view profil:
  - `resources/views/admin/profil/show.blade.php`
  - `resources/views/pembimbing/profil/show.blade.php`
  - `resources/views/superadmin/profil/show.blade.php`
  - Menerapkan layout 2 kolom:
    - Kolom kiri: Identity Card (cover gradient, preview avatar lingkaran dengan tombol kamera interaktif, nama, role chip, email badge, widget statistik penugasan, privacy notice).
    - Kolom kanan: Form data diri (Nama Lengkap, No WhatsApp, Unit Kerja / Bidang [read-only], Status Akun [read-only], tombol simpan dengan spinner anti double-submit).
- [x] **Langkah 5.3**: Buat view pengaturan & preferensi:
  - `resources/views/admin/pengaturan/index.blade.php`
  - `resources/views/pembimbing/pengaturan/index.blade.php`
  - `resources/views/superadmin/akun/index.blade.php`
  - Menerapkan grid 2 kolom:
    - Kartu Informasi Akun (mini avatar, nama, email, peran, status aktif, riwayat login terakhir, callout tautan ke menu profil).
    - Kartu Ganti Kata Sandi (password lama, password baru, konfirmasi, toggle mata tampil/sembunyi, validasi live).
    - Kartu Preferensi (pilihan bahasa interaktif, 3 kartu opsi tema: Terang, Gelap, Ikuti Sistem dengan outline biru aktif dan live preview instan).

### Fase 6: Pengujian & Quality Gate (Testing & Validation Layer)
- [x] **Langkah 6.1**: Buat test `tests/Feature/StaffProfileAndSettingsTest.php`:
  - Uji halaman Profil dan Pengaturan dapat dibuka dengan status 200 oleh Admin, Pembimbing, dan SuperAdmin.
  - Uji proteksi otorisasi antar-role (peserta tidak bisa akses profil staf, guest diarahkan ke login).
  - Uji update biodata dan upload foto profil staf (penyimpanan berkas di storage, hapus berkas lama).
  - Uji penggantian password: validasi password lama salah, password baru sama dengan password lama, password baru sukses terenkripsi, status `must_change_password` menjadi false, dan pencatatan audit log `ganti_password_mandiri`.
  - Uji update preferensi tema dan bahasa tersimpan di session/cookie.
- [x] **Langkah 6.2**: Jalankan pengujian otomatis menggunakan PHPUnit/Pest untuk memverifikasi tidak ada regresi pada seluruh test suite dashboard yang sudah ada.

---

## 6. Strategi Testing

### Automated Unit & Feature Tests (`tests/Feature/StaffProfileAndSettingsTest.php`):
1. `test_admin_can_view_and_update_profile()`: Verifikasi akses halaman profil admin dan update nama + nomor HP.
2. `test_admin_can_upload_and_delete_profile_photo()`: Verifikasi berkas pasfoto tersimpan di disk `public` dan berkas lama terhapus saat diganti.
3. `test_staff_can_change_password_with_audit_trail()`: Verifikasi alur ganti password staf mencatat event ke tabel `audit_logs` dengan tipe yang tepat.
4. `test_staff_cannot_reuse_current_password()`: Verifikasi validasi penolakan password yang sama.
5. `test_pembimbing_can_update_appearance_preferences()`: Verifikasi cookie `tema_tampilan` dan `bahasa_portal` tersimpan dengan benar.
6. `test_superadmin_has_distinct_routes_for_personal_account_and_system_config()`: Memastikan tidak ada tabrakan antara `superadmin.akun.index` dan `superadmin.pengaturan.index`.
7. `test_topbar_dropdown_renders_correct_profile_links_for_each_role()`: Memastikan dropdown topbar menampilkan link yang sesuai untuk tiap role.

### UI & Manual Verification:
1. **Navigasi Dropdown Topbar**: Login sebagai Admin, Pembimbing, dan SuperAdmin, klik avatar di topbar, klik "Profil Saya" -> menuju halaman profil masing-masing.
2. **Navigasi Sidebar**: Verifikasi menu Profil dan Pengaturan muncul di sidebar pada masing-masing dashboard.
3. **Live Preview Tema**: Pada menu Pengaturan -> Preferensi, klik kartu "Gelap" -> tampilan dashboard seketika beralih ke Dark Mode tanpa reload. Klik "Terang" -> kembali terang. Refresh halaman -> tema tetap bertahan (zero flicker).
4. **Upload Pasfoto**: Unggah gambar JPEG/PNG -> avatar langsung berganti secara live preview, tekan Simpan -> foto tersimpan dan muncul di topbar.
5. **Ganti Password**: Uji input password salah, uji password baru valid -> muncul alert sukses dan user dapat login dengan password baru.

---

## 7. Risiko & Mitigasi

- **Risiko Tabrakan Rute SuperAdmin**: SuperAdmin sudah memiliki route `/superadmin/pengaturan` untuk pengaturan master sistem.
  - *Mitigasi*: Pisahkan rute akun pribadi SuperAdmin menjadi `/superadmin/akun` (`superadmin.akun.*`), sedangkan `/superadmin/pengaturan` tetap utuh untuk konfigurasi sistem dinas.
- **Risiko Akses Foto Publik (Storage Security)**: Berkas foto profil ditaruh di `storage/app/public/profil-staf/`.
  - *Mitigasi*: Validasi ekstensi dan tipe MIME ketat (`image/jpeg,image/png,image/webp`), sanitasi nama file acak, dan jalankan `php artisan storage:link` jika symlink belum ada.
- **Risiko Password Brute Force / Bypass**:
  - *Mitigasi*: Menggunakan `Hash::check()`, validasi regex minimal 8 karakter huruf + angka, dan mencatat IP serta aktivitas di `audit_logs`.
- **Risiko Theme Flash (FOUC)**: Halaman berkedip putih saat me-load Dark Mode.
  - *Mitigasi*: Memasang inline script di bagian paling atas `<head>` pada `layouts/dashboard.blade.php` sebelum body dirender, membaca `localStorage` atau cookie tema seketika.

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Seluruh file migrasi, model, FormRequest, controller, view, dan routes terimplementasi dengan rapi.
- [ ] Dashboard Administrasi memiliki menu & halaman Profil dan Pengaturan Akun yang fungsional.
- [ ] Dashboard Pembimbing memiliki menu & halaman Profil dan Pengaturan Akun yang fungsional.
- [ ] Dashboard SuperAdmin memiliki menu & halaman Profil Saya dan Akun & Preferensi Pribadi yang fungsional.
- [ ] Fitur pasfoto profil (upload, preview live, hapus) bekerja mulus di ketiga role dan muncul di avatar topbar.
- [ ] Fitur ganti kata sandi mandiri berhasil diuji dengan verifikasi kata sandi lama dan audit log tercatat.
- [ ] Fitur preferensi tema (Terang, Gelap, Ikuti Sistem) dan bahasa berjalan responsif dan tersimpan permanen.
- [ ] Seluruh automated test di `tests/Feature/StaffProfileAndSettingsTest.php` lulus 100% tanpa error atau regresi.

---

## 9. Catatan Perubahan & Saran Lanjutan

- **Perubahan selama pengerjaan**: *(Belum ada - status Draft menunggu persetujuan)*
- **Saran Lanjutan (Out of Scope)**:
  - Integrasi 2FA (Two-Factor Authentication / OTP Authenticator) khusus untuk akun SuperAdmin dan Administrator di masa mendatang demi standar keamanan SPBE level lanjut.
  - Fitur crop/zoom pasfoto berbasis kanvas sebelum upload.
