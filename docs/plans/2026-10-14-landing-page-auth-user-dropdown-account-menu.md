# Plan: Penggantian Tombol Dashboard dengan Dropdown Akun Pengguna pada Landing Page saat Login

- **Tanggal**: 2026-10-14
- **Status**: Completed (Implementasi Berhasil & 100% Terverifikasi)
- **Author**: Antigravity (web-implementation)
- **Target File**: [resources/views/layouts/app.blade.php](file:///g:/MAGKOM/resources/views/layouts/app.blade.php), [tests/Feature/LandingPageTest.php](file:///g:/MAGKOM/tests/Feature/LandingPageTest.php)

---

## 1. Tujuan
Mengganti tombol navigasi "Dashboard" dan "Keluar" polos di header landing page ([resources/views/layouts/app.blade.php](file:///g:/MAGKOM/resources/views/layouts/app.blade.php)) menjadi **komponen Tampilan Akun (User Account Chip)** interaktif yang muncul hanya ketika pengguna dalam kondisi sudah login (`@auth`). 

Ketika tampilan akun diklik, sistem akan menampilkan menu dropdown melayang (*floating account dropdown menu*) yang menyajikan opsi:
1. **Dashboard** (Mengarahkan ke dashboard sesuai role aktif).
2. **Profil** (Mengarahkan ke halaman profil/biodata pengguna sesuai role aktif).
3. **Setting / Pengaturan** (Mengarahkan ke halaman pengaturan akun & preferensi sesuai role aktif).
4. **Keluar (Logout)** (Formulir POST resmi yang aman dari CSRF).

Jika pengguna belum login (`@guest`), antarmuka tetap menampilkan tombol standar publik: **Masuk** dan **Daftar magang**.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi:
1. **Pemetaan URL Berdasarkan Role**:
   - **Peserta**:
     - Dashboard: `route('peserta.dashboard')`
     - Profil: `route('peserta.profil.show')`
     - Setting: `route('peserta.pengaturan.index')`
   - **Pembimbing**:
     - Dashboard: `route('pembimbing.dashboard')`
     - Profil: `route('pembimbing.profil.show')`
     - Setting: `route('pembimbing.pengaturan.index')`
   - **Admin**:
     - Dashboard: `route('admin.dashboard')`
     - Profil: `route('admin.profil.show')`
     - Setting: `route('admin.pengaturan.index')`
   - **SuperAdmin**:
     - Dashboard: `route('superadmin.dashboard')`
     - Profil: `route('superadmin.profil.show')`
     - Setting: `route('superadmin.akun.index')`
2. **Avatar & Inisial Pengguna**:
   - Menggunakan accessor `$user->foto_url` jika pengguna memiliki foto profil yang valid di penyimpanan publik.
   - Jika tidak ada foto profil, sistem menampilkan inisial 1 huruf kapital dari nama pengguna (`strtoupper(substr($user->nama, 0, 1))`) dengan background warna *primary blue* (`#0751FF`).
3. **Mobile Responsiveness**:
   - Komponen akun juga harus tercermin rapi di menu mobile (*Mobile Drawer*) saat pengguna membuka portal melalui perangkat smartphone atau tablet.

### Pertanyaan Terbuka:
- Tidak ada ambiguitas kritis yang memblokir. Seluruh rute role dan komponen desain telah terverifikasi dalam codebase.

---

## 3. Pendekatan Desain & Arsitektur

### Opsi yang Dipertimbangkan:
- **Opsi A (Direkomendasikan)**: Vanilla CSS & Vanilla JavaScript terintegrasi di `resources/views/layouts/app.blade.php` mengikuti pola Design System MagKom yang sudah sukses diterapkan di `layouts/dashboard.blade.php` dan `layouts/peserta.blade.php`.
  - *Kelebihan*: Zero-dependency tambahan, performa sangat cepat, transisi CSS native, tidak ada konflik pustaka, konsisten 100% dengan dark mode dan token warna MagKom.
  - *Kekurangan*: Membutuhkan penulisan beberapa baris CSS dan JS native untuk manajemen event klik luar dan accessibility `aria-expanded`.
- **Opsi B**: Mengadopsi library pihak ketiga (seperti Alpine.js / Bootstrap dropdown).
  - *Kelebihan*: Sedikit baris JS manual.
  - *Kekurangan*: Menambah overhead dependensi baru, merusak konsistensi stack vanilla MagKom, berisiko konflik dengan script anti-flash theme.

### Keputusan:
Dipilih **Opsi A**.

---

## 4. Rincian Desain Antarmuka (UI/UX)

```
[ Desktop Navbar ]
+--------------------------------------------------------------------------------------------------+
| [Logo] MagKom KOMINFO PORTAL     Beranda   Bidang & Kuota   Alur Magang   Pengumuman             |
|                                                                                                  |
|                                         +------------------------------------------------------+ |
|                                         | [Avatar] Budi Santoso  [PESERTA]  [v]                | |
|                                         +------------------------------------------------------+ |
|                                                    | (Saat diklik terbuka popover dropdown)      |
|                                                    v                                             |
|                                         +------------------------------------------------------+ |
|                                         | Budi Santoso                                         | |
|                                         | budi.santoso@gmail.com                               | |
|                                         |------------------------------------------------------| |
|                                         | [Icon] Dashboard          (Ringkasan Magang)         | |
|                                         | [Icon] Profil Saya        (Kelola Data & Berkas)     | |
|                                         | [Icon] Pengaturan         (Password & Preferensi)    | |
|                                         |------------------------------------------------------| |
|                                         | [Icon] Keluar (Logout)    (Merah / Destruktif)       | |
|                                         +------------------------------------------------------+ |
+--------------------------------------------------------------------------------------------------+
```

### Spesifikasi Visual:
1. **Trigger Chip**:
   - Height: 44px, padding: 0.35rem 0.85rem, border-radius: 9999px (pill-shaped).
   - Border: 1px solid `#E2E8F0` (light) / `#26354D` (dark).
   - Background: `#FFFFFF` (light) / `#182234` (dark).
   - Avatar: 32px x 32px lingkaran, object-fit cover / center text.
   - Nama: Font-size 0.875rem, font-weight 700, max-width 130px with ellipsis.
   - Role Chip: Font-size 0.6875rem, font-weight 700, padding 0.15rem 0.5rem, border-radius 9999px.
   - Chevron: 16px x 16px SVG, berputar 180° saat dropdown aktif.
2. **Dropdown Menu**:
   - Width: 260px, border-radius: 1rem, elevated soft shadow `0 12px 32px -4px rgba(15, 23, 42, 0.12)`.
   - Animasi buka: `dropdownFadeIn` (0.15s cubic-bezier(0.16, 1, 0.3, 1)).
   - Setiap item menu memiliki ikon SVG 18px, label berbobot tegas, dan deskripsi mini informatif.
3. **Interaktivitas**:
   - Klik trigger untuk toggle.
   - Klik di luar menu (*click outside*) menutup dropdown.
   - Tekan tombol `Escape` menutup dropdown.

---

## 5. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/layouts/app.blade.php`](file:///g:/MAGKOM/resources/views/layouts/app.blade.php) | Modify | 1. Tambahkan CSS untuk styling User Account Trigger, Role Chip, dan Dropdown Menu.<br>2. Ganti blok `@auth` pada `.nav-actions` desktop dengan User Account Chip + Dropdown (Dashboard, Profil, Pengaturan, Logout).<br>3. Perbarui `#mobile-drawer` agar menampilkan user card & menu lengkap saat pengguna login.<br>4. Tambahkan script JS native untuk handle toggle dropdown, click outside, dan tombol Escape. |
| [`tests/Feature/LandingPageTest.php`](file:///g:/MAGKOM/tests/Feature/LandingPageTest.php) | Modify | Tambahkan unit feature test untuk memastikan: <br>- Pengguna tamu melihat tombol "Masuk" dan "Daftar magang".<br>- Pengguna terotentikasi (Peserta/Admin/Pembimbing/SuperAdmin) melihat User Account Chip, link Dashboard, Profil, Pengaturan, dan formulir Logout yang sesuai. |

---

## 6. Langkah Implementasi

- [x] **Langkah 1**: Tambahkan CSS khusus User Account Chip dan Dropdown Menu di tag `<style>` pada [`resources/views/layouts/app.blade.php`](file:///g:/MAGKOM/resources/views/layouts/app.blade.php), lengkap dengan dukungan tema Terang (*Light*) dan Gelap (*Dark Mode*).
- [x] **Langkah 2**: Refaktor blok `@auth` di elemen desktop `.nav-actions` pada [`resources/views/layouts/app.blade.php`](file:///g:/MAGKOM/resources/views/layouts/app.blade.php) dengan struktur markup User Account Trigger, Header Dropdown info pengguna, item navigasi (Dashboard, Profil, Pengaturan), dan tombol Logout.
- [x] **Langkah 3**: Sesuaikan elemen `#mobile-drawer` pada [`resources/views/layouts/app.blade.php`](file:///g:/MAGKOM/resources/views/layouts/app.blade.php) agar jika `@auth`, menampilkan profil card pengguna beserta tombol akses langsung ke Dashboard, Profil, Pengaturan, dan Logout.
- [x] **Langkah 4**: Tambahkan script JavaScript native di bagian bawah [`resources/views/layouts/app.blade.php`](file:///g:/MAGKOM/resources/views/layouts/app.blade.php) untuk mengontrol `toggleLandingUserMenu()`, `click outside listener`, dan penanganan keyboard `Escape`.
- [x] **Langkah 5**: Buat tes otomatis baru di [`tests/Feature/LandingPageTest.php`](file:///g:/MAGKOM/tests/Feature/LandingPageTest.php) untuk menguji respons landing page saat pengguna login untuk memastikan link Dashboard, Profil, dan Pengaturan ter-render secara presisi.
- [x] **Langkah 6**: Bersihkan cache view (`php artisan view:clear`) dan jalankan test suite (`php artisan test`) untuk memastikan 100% test lulus tanpa regresi.
- [x] **Langkah 7**: Lakukan inspeksi visual melalui subagent browser pada halaman landing page saat kondisi login dan logout.
- [x] **Langkah 8**: Jalankan `graphify update .` untuk memperbarui knowledge graph proyek.

---

## 7. Strategi Testing

### Automated Test:
- Menjalankan `php artisan test tests/Feature/LandingPageTest.php`.
- Memastikan test baru:
  1. `test_landing_page_menampilkan_menu_akun_dan_dropdown_saat_sudah_login_sebagai_peserta()`
  2. `test_landing_page_menampilkan_menu_akun_staf_saat_login_sebagai_admin()`
  3. Memastikan seluruh 369 tests eksisting tetap berstatus **100% PASS**.

### Manual & Visual Verification:
- Buka browser dengan akun peserta yang sudah login.
- Klik tombol akun di navbar landing page dan pastikan dropdown menu muncul secara mulus.
- Klik link "Dashboard" $\rightarrow$ berpindah ke Dashboard peserta.
- Klik link "Profil" $\rightarrow$ berpindah ke Profil peserta.
- Klik link "Pengaturan" $\rightarrow$ berpindah ke Pengaturan peserta.
- Klik "Keluar" $\rightarrow$ berhasil logout dan tombol navbar kembali menjadi "Masuk" dan "Daftar magang".

---

## 8. Risiko & Mitigasi

- **Risiko**: Nilai `$user->role` yang null atau unexpected pada database lama.
  - *Mitigasi*: Gunakan fallback default `match($user->role->value ?? '')` mengarah ke rute aman peserta.
- **Risiko**: Tabrakan z-index dropdown dengan sticky navbar atau elemen hero slider.
  - *Mitigasi*: Berikan `z-index: 1050` pada `.landing-user-dropdown-menu` dan `position: relative` pada kontainer pemicu.
- **Risiko**: Kinerja atau layout shift saat rendering foto profil.
  - *Mitigasi*: Berikan fixed width dan height (32px x 32px) dengan fallback inisial teks bawaan SVG/CSS.

---

## 9. Kriteria Selesai (Definition of Done)

- [ ] Seluruh checklist langkah implementasi tercentang `- [x]`.
- [ ] Tombol "Dashboard" digantikan oleh User Account Chip + Dropdown (Dashboard, Profil, Pengaturan, Logout) saat login.
- [ ] Tampilan tamu (`@guest`) tetap bersih dengan tombol "Masuk" dan "Daftar magang".
- [ ] Seluruh unit/feature test otomatis (termasuk test baru) berstatus 100% PASS.
- [ ] Verifikasi visual browser berhasil dan terdokumentasi.
