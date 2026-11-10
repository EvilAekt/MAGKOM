# Rencana Kerja: Redesain Halaman Pengaturan & Akun Saya (Sesuai Mockup Design System MagKom)

**Tanggal**: 10 November 2026  
**Status**: Draf Usulan (Menunggu Persetujuan User)  
**Dokumen Referensi**: `docs/plans/2026-11-10-plan-redesign-pengaturan-akun-pembimbing.md`  
**Fitur**: Portal Pengaturan Akun & Preferensi (`/pembimbing/pengaturan` & adaptasi `/peserta/pengaturan`)  
**Tangkapan Layar Acuan**: `media_1794313542091_9759c8c1.jpg`

---

## 1. Analisis Kebutuhan & Spesifikasi Visual

Berdasarkan gambar acuan (`media_1794313542091_9759c8c1.jpg`), seluruh menu tampilan pada halaman **Akun & Preferensi Saya** akan diperbarui secara total agar 100% presisi dan selaras dengan *Design System MagKom Sleman*, dengan mempertahankan seluruh data, variabel, dan alur fungsional backend yang sudah berjalan saat ini.

### Struktur Komponen Visual Acuan:

1. **Hero Banner Modern (*Sleman Abyssal Navy*)**:
   - Background: Gradien Sleman Navy (`#003566` / `#02275D`), sudut melengkung `18px`, padding luas `28px 32px`.
   - Kolom Kiri:
     - Judul: **"Akun & Preferensi Saya"** (*Lexend 800, putih, ~1.65rem*).
     - Subtitle: *"Kelola informasi kredensial, keamanan kata sandi, dan tema tampilan portal pembimbing."* (*putih semi-transparan, ~0.875rem*).
     - Baris Kapsul Konteks (*Translucent Context Pills*):
       - Kapsul 1: `PEMBIMBING` (label mikro uppercase) + `{{ $user->nama }} (Pembimbing Lapangan)` (bold).
       - Kapsul 2: `HAK AKSES` (label mikro uppercase) + `Pembimbing Magang` (bold).
   - Kolom Kanan (*Status Widget Sleman Gold*):
     - Background: Sleman Gold (`#FFCB05` / `#FFC300`), sudut melengkung `14px`, padding `18px 22px`.
     - Label: `STATUS AKUN` (uppercase bold ~0.7rem).
     - Nilai Status: `Aktif` (huruf tebal besar ~1.85rem).
     - Sub-label: `Portal pembimbing lapangan` (~0.75rem).
     - Tombol Aksi: `Lihat Profil Lengkap` (`background: #003566; color: #FFFFFF; font-weight: 700; border-radius: 8px; font-size: 0.8125rem;` rute ke `route('pembimbing.profil.show')`).

2. **Tata Letak Grid 2-Kolom (Tengah)**:
   - **Kartu Kiri: Informasi Akun**:
     - Kontainer: Kartu putih bersih (`border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);`).
     - Judul: `Informasi Akun` (bold navy `#003566`, ~1.125rem) & Subtitle: `Ringkasan kredensial dan hak akses pembimbing lapangan.`
     - Baris Identitas Cepat:
       - Avatar lingkaran inisial `BU` (`48px x 48px`, border `1.5px solid #CBD5E1`, background putih, inisial navy tebal).
       - Nama: `{{ $user->nama }} (Pembimbing Lapangan)` (bold `#003566`).
       - Chip Peran: `PEMBIMBING LAPANGAN` (pill badge dengan border tipis `#CBD5E1`, background putih, teks `#003566`).
     - 5 Baris Data Kredensial:
       1. `Nama Lengkap` -> `{{ $user->nama }} (Pembimbing Lapangan)`
       2. `Alamat Email` -> `{{ $user->email }}`
       3. `Nomor Kontak` -> `{{ $user->no_telp ?? 'Belum diisi' }}`
       4. `Hak Akses` -> `Pembimbing Magang`
       5. `Status Akun` -> Badge pill kuning `Aktif` (`background: #FFC300; color: #02275D; font-weight: 800; border-radius: 999px; padding: 3px 12px;`).
     - Kotak Peringatan/Info di Bawah:
       - Kontainer biru muda lembut (`background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 8px; padding: 10px 14px;`).
       - Ikon info `(i)` biru + teks: *"Foto profil dan nomor telepon dinas dapat diperbarui di halaman Profil Saya."* (dengan link aktif ke profil).

   - **Kartu Kanan: Ganti Kata Sandi**:
     - Kontainer: Kartu putih bersih (`border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);`).
     - Judul: `Ganti Kata Sandi` & Subtitle: `Gunakan kombinasi sandi yang aman dan tidak digunakan di sistem lain.`
     - Form Fields:
       1. `Kata sandi saat ini` -> input dengan tombol toggle ikon mata di sisi kanan, placeholder `Masukkan kata sandi saat ini`.
       2. `Kata sandi baru` -> input dengan toggle mata, placeholder `Masukkan kata sandi baru`.
       3. `Konfirmasi kata sandi baru` -> input dengan toggle mata, placeholder `Ulangi kata sandi baru`.
     - Teks Petunjuk: `Minimal 8 karakter, kombinasi huruf dan angka atau simbol.` (`color: #64748B; font-size: 0.75rem;`).
     - Tombol Aksi:
       - Tombol Utama: `Perbarui Kata Sandi` (`background: #003566; color: #FFFFFF; font-weight: 700; border-radius: 8px; padding: 9px 20px;`).
       - Tombol Batal: `Batal` (`background: #FFFFFF; color: #475569; border: 1px solid #CBD5E1; border-radius: 8px; padding: 9px 20px; font-weight: 700;`).

3. **Kartu Bawah Lebar Penuh: Preferensi Antarmuka & Bahasa**:
   - Kontainer: Kartu putih penuh (`border: 1px solid #E2E8F0; border-radius: 16px; padding: 24px;`).
   - Judul: `Preferensi Antarmuka & Bahasa` & Subtitle: `Sesuaikan kenyamanan visual dan bahasa saat membimbing peserta magang.`
   - Tata Letak Form 2 Bagian:
     - Sisi Kiri: `Bahasa Antarmuka`
       - Dropdown Select: `Indonesia (Default)` (`border: 1.5px solid #CBD5E1; border-radius: 8px; height: 42px; font-weight: 600; width: 100%; max-width: 320px;`).
       - Hint: `Bahasa yang digunakan untuk menu dan label evaluasi.`
     - Sisi Kanan: `Mode Tampilan Tema`
       - 3 Kartu Miniatur Interaktif:
         1. **Terang**:
            - Miniatur: Latar putih, border tipis, titik biru muda (`#0284C7`), dan bilah horizontal abu-abu.
            - Judul: `Terang` & Subtitle: `Mode default portal`.
            - State Aktif: Border biru tebal `#003566` / `#0284C7` + ring shadow.
         2. **Gelap**:
            - Miniatur: Latar navy gelap (`#003566` / `#02275D`), titik emas (`#FFC300`), dan bilah abu-abu.
            - Judul: `Gelap` & Subtitle: `Ramah mata untuk malam hari`.
         3. **Ikuti Sistem**:
            - Miniatur: Split diagonal (setengah putih, setengah navy gelap), titik biru, bilah horizontal.
            - Judul: `Ikuti Sistem` & Subtitle: `Menyesuaikan pengaturan OS`.
   - Tombol Aksi di Bawah:
     - `Simpan Preferensi Tampilan` (`background: #003566; color: #FFFFFF; font-weight: 700; border-radius: 8px; padding: 10px 22px;`).

---

## 2. Asumsi Teknis & Keamanan

1. **Integritas Bisnis & Keamanan (SEC-01 s/d SEC-06)**:
   - Validasi kata sandi saat ini (`current_password`), konfirmasi kata sandi, dan enkripsi `Hash::make()` tetap 100% utuh di `Pembimbing\PengaturanController`.
   - Penanganan CSRF token (`@csrf`) dan audit trail penggantian password tetap dipertahankan.
   - Pilihan preferensi bahasa dan tema tetap disimpan ke session dan cookie (`tema_tampilan` & `bahasa_portal`) sehingga sinkron dengan `applyPortalTheme`.
2. **Kepatuhan Aturan Repositori (AGENTS.md)**:
   - **Zero Inline Styles/Scripts di Blade**: Memindahkan seluruh CSS legacy inline di `resources/views/pembimbing/pengaturan/index.blade.php` ke file CSS modular baru: `resources/css/pages/pembimbing/pengaturan-index.css`.
   - **Asset Pipeline Vite**: Menambahkan entry point CSS & JS di `vite.config.js` dan mengompilasi dengan `npm run build`.

---

## 3. Evaluasi Opsi Pendekatan Arsitektur

| Parameter | Opsi A: Modifikasi Parsial di dalam Blade (Legacy Style) | Opsi B: Modular Vite Asset Architecture (Direkomendasikan) |
|---|---|---|
| **Penyimpanan CSS** | Menimpa `<style>` tag di dalam file Blade | Membuat `resources/css/pages/pembimbing/pengaturan-index.css` terkelola Vite |
| **Penyimpanan JS** | Inline `<script>` di Blade | Membuat `resources/js/pages/pembimbing/pengaturan-index.js` modular |
| **Kepatuhan Aturan AGENTS.md** | Melanggar aturan pemisahan CSS/JS dari Blade | 100% patuh aturan AGENTS.md Bagian 3, 4, 5, dan 6 |
| **Performa & Cache** | CSS tidak di-minifikasi oleh bundler Vite | CSS dan JS ter-minify, gzipped, dan di-cache efisien |
| **Rekomendasi** | Tidak Direkomendasikan | **Sangat Direkomendasikan** |

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
|---|---|---|
| [`resources/css/pages/pembimbing/pengaturan-index.css`](file:///d:/MAGKOM/MAGKOM/resources/css/pages/pembimbing/pengaturan-index.css) | **Create** | Berkas stylesheet khusus halaman Pengaturan Akun: Hero banner navy, gold status widget, kartu bento putih, input password wrapper, custom select bahasa, dan interactive theme cards (lengkap dengan Dark Mode). |
| [`resources/js/pages/pembimbing/pengaturan-index.js`](file:///d:/MAGKOM/MAGKOM/resources/js/pages/pembimbing/pengaturan-index.js) | **Create** | Logika interaktif: Toggle visibilitas kata sandi (show/hide), pemilihan kartu tema visual, dan inisialisasi tema aktif. |
| [`vite.config.js`](file:///d:/MAGKOM/MAGKOM/vite.config.js) | **Modify** | Mendaftarkan entry point `resources/css/pages/pembimbing/pengaturan-index.css` dan `resources/js/pages/pembimbing/pengaturan-index.js`. |
| [`resources/views/pembimbing/pengaturan/index.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/pembimbing/pengaturan/index.blade.php) | **Modify** | Restrukturisasi markup: Mengganti page-title standar dengan hero banner navy & widget emas, menyelaraskan 2-kolom kartu informasi & password, membersihkan `<style>` dan `<script>` inline, serta memuat asset via `@vite`. |

---

## 5. Rencana Langkah Implementasi Terstruktur

- [ ] **Langkah 1**: Buat berkas CSS `resources/css/pages/pembimbing/pengaturan-index.css` yang mendefinisikan:
  - `.settings-hero-banner`, `.hero-context-pill`, `.banner-gold-widget`
  - `.settings-grid-2col`, `.settings-white-card`
  - `.profile-identity-box`, `.avatar-circle`, `.role-chip-pill`
  - `.credential-rows`, `.badge-status-gold`, `.alert-info-box`
  - `.input-password-group`, `.toggle-eye-btn`, `.btn-action-navy`, `.btn-action-outline`
  - `.pref-horizontal-grid`, `.theme-cards-grid`, `.theme-preview-card`, `.theme-mini-box`
  - Dark mode overrides (`[data-theme="dark"]`)
- [ ] **Langkah 2**: Buat berkas JS `resources/js/pages/pembimbing/pengaturan-index.js` untuk fungsi toggle mata password dan pemilihan tema kartu radio.
- [ ] **Langkah 3**: Daftarkan kedua berkas tersebut ke dalam array `input` di `vite.config.js`.
- [ ] **Langkah 4**: Perbarui markup `resources/views/pembimbing/pengaturan/index.blade.php` agar merefleksikan tata letak baru sesuai gambar, menghapus blok `<style>` dan `<script>` inline lama, lalu memuat asset via `@vite`.
- [ ] **Langkah 5**: Kompilasi aset Vite frontend dengan Node.js v22 (`npm run build`).
- [ ] **Langkah 6**: Bersihkan cache tampilan Blade (`php artisan view:clear`).
- [ ] **Langkah 7**: Jalankan pengujian otomatis untuk memverifikasi fungsionalitas:
  - `php artisan test tests/Feature/StaffProfileAndSettingsTest.php`
  - `php artisan test tests/Feature/LanguageLocalizationTest.php`
  - `php artisan test tests/Feature/DashboardLayoutTest.php`
- [ ] **Langkah 8**: Jalankan pemeriksaan standar kode Laravel Pint (`./vendor/bin/pint --test`).

---

## 6. Kriteria Selesai (Definition of Done)

- [ ] Tampilan halaman pengaturan akun 100% identik dengan gambar referensi (`media_1794313542091_9759c8c1.jpg`).
- [ ] Hero banner navy dengan kapsul konteks pembimbing dan widget status emas tampil presisi.
- [ ] Kartu Informasi Akun memuat inisial/foto, nama, role chip, 5 baris kredensial, dan info alert di bawahnya.
- [ ] Kartu Ganti Kata Sandi memiliki input interaktif dengan ikon mata, hint, dan tombol submit navy + batal.
- [ ] Kartu Preferensi Antarmuka & Bahasa memiliki dropdown bahasa dan 3 kartu tema visual (Terang, Gelap, Ikuti Sistem) yang berfungsi saat diklik.
- [ ] Tidak ada lagi inline `<style>` atau `<script>` di dalam file Blade; semua asset dikompilasi via Vite.
- [ ] Seluruh Feature Test (`StaffProfileAndSettingsTest`, `LanguageLocalizationTest`, `DashboardLayoutTest`) lulus 100%.
- [ ] Laravel Pint lulus tanpa peringatan.

