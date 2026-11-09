# Rencana Implementasi: Fitur Salin Kredensial Awal Peserta Jalur Khusus pada Tabel Slot Khusus SuperAdmin

Dokumen ini memuat rencana kerja teknis untuk mengimplementasikan fitur penyalinan kredensial login (Email dan Kata Sandi Awal) peserta magang jalur khusus langsung dari tabel **"Daftar Peserta Magang Jalur Khusus"** di halaman SuperAdmin Slot Khusus (`superadmin.slot-khusus.index`).

---

## 1. Tujuan

1. **Kemudahan Operasional SuperAdmin**: Memungkinkan SuperAdmin menyalin kredensial pendaftaran peserta magang jalur khusus (Nama, Email, Kata Sandi Awal, Tautan Login) langsung dari baris tabel dengan satu kali klik (*one-click copy to clipboard*) dalam format pesan siap kirim (WhatsApp / Telegram / Email).
2. **Ketersediaan Fleksibel**: Kredensial awal tetap dapat diakses dan disalin kapan saja oleh SuperAdmin tanpa risiko hilang saat halaman di-refresh, sangat membantu jika peserta meminta dikirimkan ulang data akun awalnya.
3. **Jaminan Keamanan & Privasi Peserta (BR-15)**: 
   - Kata sandi awal disimpan secara aman menggunakan **enkripsi dua arah AES-256** (Laravel attribute encryption).
   - Kata sandi awal **otomatis kadaluwarsa / hangus** segera setelah peserta melakukan aktivasi dan mengganti kata sandinya di menu `/password/ganti`.
   - Jika SuperAdmin atau pihak lain mencoba login menggunakan kata sandi awal tersebut setelah diganti, sistem dipastikan **menolak login**.
   - Kata sandi baru pribadi milik peserta tetap 100% rahasia dan tidak dapat diketahui oleh SuperAdmin.

---

## 2. Asumsi & Batasan Teknis

* **Asumsi 1 (Enkripsi DB)**: Kata sandi awal disimpan pada kolom `password_awal` di tabel `users` dengan casting `'password_awal' => 'encrypted'`. Di dalam database MySQL nilainya berupa ciphertext terenkripsi aman, bukan plaintext.
* **Asumsi 2 (Siklus Sandi)**: Otentikasi login Laravel **hanya** memeriksa kolom `users.password` (hash Bcrypt). Kolom `password_awal` murni berfungsi sebagai catatan riwayat kredensial awal yang diterbitkan. Begitu peserta memperbarui password, hash pada `users.password` berganti dan `users.must_change_password` menjadi `false`.
* **Asumsi 3 (Akun Lama/Seeder)**: Untuk akun jalur khusus yang sudah ada di database sebelum fitur ini dibuat (misalnya akun dari seeder yang tidak mencatat sandi awal), sistem akan menampilkan keterangan bahwa sandi awal tidak tercatat, dan tombol salin hanya akan menyalin nama, email, dan tautan login portal.
* **Aturan Repositori (`AGENTS.md`)**:
  - Dilarang menulis JavaScript di `<script>` atau inline `onclick` dalam Blade. Logika salin (*clipboard API*) wajib ditulis modular di `resources/js/`.
  - Dilarang menulis CSS di `<style>` Blade. Semua styling tombol dan badge status diletakkan di `resources/css/`.

---

## 3. Pendekatan Teknis & Evaluasi Arsitektur

### Perbandingan Pendekatan

| Kriteria | Pendekatan 1: Kolom `password_awal` Terenkripsi (Direkomendasikan) | Pendekatan 2: Penyimpanan Sesi Sekali Tampil (Flash Session Only) |
| :--- | :--- | :--- |
| **Kenyamanan SuperAdmin** | **Tinggi**: Kredensial awal tetap bisa disalin kapan saja di tabel selama dibutuhkan. | **Rendah**: Hanya muncul sekali saat redirect; jika tab tertutup, kredensial hilang selamanya. |
| **Keamanan Data** | **Tinggi**: Dienkripsi AES-256 bawaan framework dengan APP_KEY; sandi awal otomatis tidak bisa dipakai login lagi setelah diganti. | **Tinggi**: Tidak disimpan di database. |
| **Privasi Sandi Baru** | **Terjaga Penuh**: SuperAdmin hanya tahu sandi awal, bukan sandi baru peserta. | **Terjaga Penuh**: SuperAdmin tidak tahu sandi baru peserta. |
| **Kesesuaian Kebutuhan User** | **100% Cocok** dengan skenario yang diminta user. | Tidak memenuhi kebutuhan copy dari tabel secara berkala. |

**Keputusan**: Mengadopsi **Pendekatan 1** dengan memanfaatkan Laravel Model Attribute Encryption (`'encrypted'`) pada kolom `password_awal` di tabel `users`.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `database/migrations/2026_11_09_100000_add_password_awal_to_users_table.php` | **Create** | Menambahkan kolom `password_awal` (tipe `text`, nullable) pada tabel `users`. |
| `app/Models/User.php` | **Modify** | Menambahkan `password_awal` ke dalam `$fillable` dan `$casts` sebagai `'encrypted'`. |
| `app/Services/PlacementService.php` | **Modify** | Pada method `tempatkanPesertaBaruManual()`, simpan `$peserta->password_awal = $plainPassword`. |
| `app/Http/Controllers/SuperAdmin/PesertaJalurKhususController.php` | **Modify** | Mengarahkan redirect setelah pendaftaran ke `superadmin.slot-khusus.index` dengan session flash alert kredensial. |
| `resources/views/superadmin/slot-khusus/index.blade.php` | **Modify** | Memperbarui Tabel 2 "Daftar Peserta Magang Jalur Khusus": menambahkan elemen tombol salin kredensial awal dan indikator status sandi (`Sandi Awal Aktif` vs `Sandi Sudah Diganti Peserta`). |
| `resources/css/pages/superadmin/superadmin-common.css` | **Modify** | Menambahkan styling komponen tombol salin kredensial dan badge status kredensial. |
| `resources/js/pages/superadmin/slot-khusus.js` | **Create** | Modul JavaScript ES untuk menangani event klik salin clipboard dengan toast feedback yang rapi. |
| `vite.config.js` | **Modify** | Mendaftarkan entry point baru `resources/js/pages/superadmin/slot-khusus.js`. |
| `tests/Feature/SuperAdminPesertaKhususCredentialCopyTest.php` | **Create** | Feature test untuk memverifikasi penyimpanan terenkripsi, invalidasi sandi awal setelah ganti sandi, dan integritas UI. |

---

## 5. Langkah-Langkah Implementasi

- [x] **Langkah 1: Pembuatan Database Migration**
  - Buat migration `2026_11_09_100000_add_password_awal_to_users_table.php`.
  - Tambahkan kolom `$table->text('password_awal')->nullable()->after('password')->comment('Kata sandi awal terenkripsi');`.
  - Jalankan migrasi menggunakan runtime PHP 8.3 aktual.

- [x] **Langkah 2: Update Model `User.php`**
  - Daftarkan kolom `password_awal` pada array `$fillable`.
  - Daftarkan casting `'password_awal' => 'encrypted'` pada array `$casts` untuk enkripsi/dekripsi transparan otomatis.
  - Sembunyikan dari serialisasi JSON (`$hidden = ['password', 'remember_token', 'password_awal']`).

- [x] **Langkah 3: Update `PlacementService.php`**
  - Pada method `tempatkanPesertaBaruManual`, simpan `$peserta->password_awal = $plainPassword;` bersamaan dengan pembuatan user.

- [x] **Langkah 4: Update `PesertaJalurKhususController.php`**
  - Ubah rute redirect sukses dari `superadmin.monitoring.index` ke `superadmin.slot-khusus.index`.
  - Pertahankan flash session `akun_baru_dibuat`, `nama_peserta`, `email_peserta`, dan `password_baru`.

- [x] **Langkah 5: Penambahan Aset Styling CSS (`superadmin-common.css`)**
  - Tambahkan style untuk:
    - `.sa-btn-copy-kredensial` (tombol salin kompak dengan ikon clipboard).
    - `.sa-badge-credential-active` (status oranye: Sandi Awal Aktif).
    - `.sa-badge-credential-changed` (status hijau: Sandi Telah Diubah Peserta).

- [x] **Langkah 6: Pembuatan Modul JavaScript (`resources/js/pages/superadmin/slot-khusus.js`)**
  - Buat script terstruktur tanpa inline event handler.
  - Tangani event listener pada tombol `.sa-btn-copy-kredensial`.
  - Susun teks format pesan siap kirim:
    ```text
    *AKUN PORTAL MAGANG KOMINFO (MAGKOM)*
    Nama: {nama}
    Email: {email}
    Kata Sandi Awal: {sandi}
    Tautan Login: {url}

    *Catatan:* {pesan_status}
    ```
  - Panggil `navigator.clipboard.writeText()` dan tampilkan toast feedback "Kredensial login berhasil disalin!".

- [x] **Langkah 7: Pendaftaran Entry Point Vite**
  - Daftarkan `resources/js/pages/superadmin/slot-khusus.js` ke dalam `vite.config.js`.
  - Load script pada file Blade `superadmin/slot-khusus/index.blade.php`.

- [x] **Langkah 8: Update Antarmuka Blade (`superadmin/slot-khusus/index.blade.php`)**
  - Tambahkan banner flash kredensial di atas tabel jika ada session `akun_baru_dibuat`.
  - Pada baris Tabel 2 "Daftar Peserta Magang Jalur Khusus":
    - Tambahkan elemen status kata sandi di bawah kolom Peserta / status.
    - Tambahkan tombol salin kredensial dengan atribut `data-*` semantik (nama, email, sandi awal, status).

- [x] **Langkah 9: Pembuatan Feature Test & Eksekusi Quality Gate**
  - Buat `tests/Feature/SuperAdminPesertaKhususCredentialCopyTest.php`.
  - Uji alur pendaftaran peserta jalur khusus -> verifikasi `password_awal` tersimpan terenkripsi.
  - Uji bahwa sandi awal bisa dipakai login pertama kali.
  - Uji bahwa setelah ganti sandi mandiri di `/password/ganti`, sandi awal **ditolak** untuk login berikutnya.
  - Uji bahwa sandi baru milik peserta **berhasil** login.
  - Jalankan `npm run build` dan automated test PHP 8.3.

---

## 6. Strategi Pengujian (Testing Strategy)

### Skenario Otomatis (Feature Tests)
1. **Happy Path Pendaftaran**: SuperAdmin mendaftarkan peserta jalur khusus -> data tersimpan di tabel `users` dengan `password_awal` terenkripsi dan `must_change_password = true`.
2. **Dekripsi Transparan**: Pemanggilan `$user->password_awal` menghasilkan kata sandi plaintext asli sesuai yang digenerate.
3. **Invalidasi Sandi Awal Setelah Ganti Sandi**:
   - Peserta login dengan `password_awal` -> Berhasil login -> dialihkan ke `/password/ganti`.
   - Peserta mengubah kata sandi menjadi kata sandi baru pribadi.
   - Peserta logout.
   - Percobaan login menggunakan `password_awal` yang lama -> **GAGAL (HTTP 302 dengan error kredensial)**.
   - Percobaan login menggunakan kata sandi baru -> **BERHASIL**.
4. **Rendering Antarmuka**: Halaman `superadmin.slot-khusus.index` dapat dirender (HTTP 200) dan memuat atribut data kredensial yang valid.

---

## 7. Analisis Risiko & Mitigasi

| Potensi Risiko | Tingkat Risiko | Mitigasi |
| :--- | :--- | :--- |
| Kebocoran kata sandi di database | Rendah | Menggunakan enkripsi simetris bawaan framework (`'password_awal' => 'encrypted'`), nilai di kolom SQL berupa string acak base64 yang tidak bisa dibaca tanpa `APP_KEY`. |
| SuperAdmin bisa login ke akun peserta yang sudah aktif | Nol | Login hanya memeriksa hash `users.password`. Begitu peserta mengganti kata sandi, kata sandi awal yang dipegang SuperAdmin otomatis tidak valid. |
| Pelanggaran Aturan Blade (`AGENTS.md`) | Nol | Seluruh fungsi copy clipboard diisolasi di file JS ES Modules terpisah (`resources/js/pages/superadmin/slot-khusus.js`) dan dikompilasi lewat Vite tanpa atribut `onclick`. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Migration tabel `users` kolom `password_awal` berhasil dieksekusi.
- [x] Model `User` mengenkripsi dan mendekripsi `password_awal` secara otomatis dan transparan.
- [x] Alur pendaftaran peserta jalur khusus menyimpan kata sandi awal ke dalam database.
- [x] Tabel "Daftar Peserta Magang Jalur Khusus" menyediakan tombol salin kredensial dengan format teks siap kirim.
- [x] Indikator status kata sandi menampilkan status yang tepat (`Sandi Awal Aktif` vs `Sandi Sudah Diganti Peserta`).
- [x] Percobaan login menggunakan kata sandi awal setelah peserta mengganti sandi terbukti **ditolak** oleh sistem.
- [x] Seluruh pengujian otomatis di `tests/Feature/SuperAdminPesertaKhususCredentialCopyTest.php` dan `AdminPenempatanJalurKhususTest.php` berstatus hijau (PASS) pada runtime PHP 8.3 aktual.
- [x] Aset frontend terkompilasi bersih via `npm run build`.

