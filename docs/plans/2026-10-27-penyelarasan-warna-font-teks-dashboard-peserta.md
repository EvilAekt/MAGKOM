# Rencana Kerja: Implementasi Warna, Font, dan Teks Landing Page ke Tampilan Dashboard Peserta

**Tanggal:** 27 Oktober 2026  
**Status:** Menunggu Persetujuan Pengguna (*Pending Approval*)  
**Konteks:** Menyelaraskan identitas visual (warna, font *Lexend* & *Source Sans 3*, dan gaya teks komunikasi resmi) dari Landing Page baru ke dalam seluruh tampilan Dashboard Peserta MAGKOM Sleman.

---

## 1. Tujuan & Sasaran
Membawa estetika modern, terpercaya, dan profesional dari landing page baru ke dalam portal dashboard peserta sehingga pengalaman pengguna (*user experience*) terasa berkesinambungan dan tidak mengalami *visual shock* saat beralih dari landing page ke dashboard.

---

## 2. Analisis Perubahan: Warna, Font, dan Teks

### A. Font & Tipografi (Font Pairing Landing Page)
- **Display / Heading Font (`Lexend`)**:
  - Diterapkan pada seluruh judul halaman (`h1`, `h2`, `h3`), nama instansi sidebar, heading hero banner, angka metrik KPI (`.count`), judul kartu/panel, dan tombol aksi utama.
- **Body / Content Font (`Source Sans 3`)**:
  - Diterapkan pada teks paragraf, keterangan tabel, form input, modal, dan deskripsi tugas/logbook.
- **Integrasi di Layout**:
  - Memuat Google Fonts: `Lexend:wght@500;600;700;800` dan `Source Sans 3:wght@400;500;600;700` di [layouts/peserta.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/peserta.blade.php).
  - Menetapkan token CSS:
    ```css
    --font-display: 'Lexend', sans-serif;
    --font-sans: 'Source Sans 3', 'Open Sans', sans-serif;
    ```

### B. Palet Warna (Sleman Smart City & Electric Blue)
- **Background Utama**: Slate Ultra-Light `#f8fafc` (mode terang) dan Deep Navy `#0c1b2e` (mode gelap).
- **Hero Greeting Banner (`.dashboard-hero`)**:
  - Gradien Sleman Navy & Electric Blue: `linear-gradient(135deg, #0c1b2e 0%, #1b3860 65%, #0751FF 100%)`.
  - Ornamen garis halus berpadu dengan aksen dot emas (`#fbbf24`) dan badge status *live pulsing* seperti pada footer landing page.
- **Aksen Gold & Blue**:
  - Tombol aksi primer menggunakan `#0751FF` dengan efek hover `#0641cc` dan shadow halus.
  - Badge prestasi dan skor kelulusan menggunakan aksen emas `#f59e0b` / `#fbbf24`.
- **Card & Panel Dashboard**:
  - Permukaan putih bersih (`#ffffff`), border halus `rgba(226, 232, 240, 0.8)`, sudut melengkung modern `border-radius: 14px`, dan shadow lembut `box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.05)`.

### C. Teks & Gaya Bahasa (Copywriting Harmonization)
- **Header Dashboard**:
  - Judul: **Dashboard Peserta MAGKOM**
  - Subjudul: **Portal Magang Terpadu · Dinas Komunikasi dan Informatika Kabupaten Sleman**
- **Banner Hero**:
  - Badge Status: **"MAGANG RESMI PEMKAB SLEMAN &bull; STATUS: AKTIF"**
  - Salam Pembuka: **"Selamat Datang, [Nama Peserta]!"**
  - Penempatan: Nama Bidang lengkap kedinasan Diskominfo Sleman.
- **Tombol Aksi Cepat (*Quick Actions*)**:
  - `+ Isi Logbook Mingguan`
  - `Lihat Tugas Bimbingan`
  - `Cek Lembar Nilai & SK`
- **Fase Pendaftaran / Onboarding & Seleksi**:
  - Teks diselaraskan dengan istilah resmi landing page (*Sendiri/Kelompok*, *SK Diterima Resmi*, *Buku Panduan PDF*).

---

## 3. File yang Terdampak & Rincian Modifikasi

| File | Komponen | Rencana Perubahan |
| :--- | :--- | :--- |
| `resources/views/layouts/peserta.blade.php` | Head, Font Links, CSS Tokens, Sidebar Branding | 1. Memuat Google Fonts `Lexend` & `Source Sans 3`.<br>2. Menerapkan `--font-display` dan `--font-sans`.<br>3. Menyelaraskan teks sidebar branding "DISKOMINFO SLEMAN &bull; MAGKOM". |
| `resources/views/peserta/dashboard.blade.php` | Stylesheet Scoped Dashboard Peserta | 1. Memperbarui gradien `.dashboard-hero` ke tema Sleman Navy `#0c1b2e` - `#1b3860` - `#0751FF`.<br>2. Menerapkan font `Lexend` pada judul hero, angka metrik, dan tombol.<br>3. Memperbarui warna kartu KPI, progress bar, dan badge. |
| `resources/views/peserta/dashboard/partials/_header.blade.php` | Page Title & Hero Greeting Banner | 1. Penyelarasan teks judul & deskripsi kedinasan.<br>2. Badge status magang aktif dengan dot hijau melayang.<br>3. Teks sapaan dan penempatan yang serasi dengan landing page. |
| `resources/views/peserta/dashboard/partials/_aside.blade.php` | Profil Pembimbing & Aksi Cepat | 1. Penyelarasan tipografi font `Lexend` pada judul kartu.<br>2. Pewarnaan tombol aksi cepat Electric Blue & Emerald. |
| `resources/views/peserta/dashboard/partials/_phase_aktif.blade.php` | Metrik KPI & Tab Progres | 1. Mempercantik progress bar dan angka hitungan metrik dengan font `Lexend`.<br>2. Teks label KPI yang informatif dan lugas. |

---

## 4. Tahap Pengujian & Validasi
1. Mengosongkan cache view Blade (`php artisan view:clear`).
2. Uji render tampilan dashboard peserta pada mode terang dan mode gelap.
3. Memastikan tidak ada regresi fungsi (tombol pengisian logbook, modal drawer, dan link tugas tetap berfungsi 100%).
4. Verifikasi responsivitas pada layar desktop dan ponsel pintar.

---

## 5. Konvensi Bahasa & Keamanan
- Seluruh kode sumber, variabel, dan kelas CSS tetap ditulis dalam **Bahasa Inggris**.
- Penjelasan dan laporan disampaikan dalam **Bahasa Indonesia**.
- Tidak ada modifikasi file di luar lingkup direktori `d:\MAGKOM\MAGKOM`.
