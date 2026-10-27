# Rencana Kerja: Integrasi Menyeluruh Landing Page Baru dengan Seluruh Tampilan Sistem MAGKOM

**Tanggal:** 27 Oktober 2026  
**Status:** Menunggu Persetujuan Pengguna (*Pending Approval*)  
**Konteks:** Menghubungkan ekosistem tampilan landing page baru ke seluruh halaman publik, otentikasi, dan tata letak navigasi sistem MAGKOM Sleman secara dua arah (*two-way navigation*) dan konsisten.

---

## 1. Latar Belakang & Tujuan
Landing page baru yang modern telah selesai dibangun dengan fitur interaktif (Hero slider, Kuota Explorer mandiri/kelompok, roadmap alur, FAQ, dan generator PDF panduan magang). Namun, beberapa halaman lain dalam aplikasi masih memiliki tautan jangkar lama (`#kuota-section`), halaman publik yang terisolasi dari navbar/footer resmi (seperti `kebijakan-privasi.blade.php`), serta footer landing page yang belum memuat *Quick Links* resmi untuk mengakses dokumen legal, verifikasi keabsahan, dan portal akun.

**Tujuan Integrasi:**
1. **Navigasi Dua Arah Mulus (*Seamless Two-Way Integration*)**: Memastikan pengguna dapat berpindah bolak-balik antara landing page baru, halaman registrasi/login, panduan alur, verifikasi dokumen, dan kebijakan privasi tanpa hambatan (*no dead-ends*).
2. **Sinkronisasi Anchor Kuota**: Memperbaiki semua tautan jangkar lama `#kuota-section` di seluruh sistem menjadi `#kuota` agar scroll presisi menuju kalkulator kuota interaktif.
3. **Penyempurnaan Footer Landing Page**: Melengkapi footer landing page dengan menu *Quick Links* (Navigasi Beranda, Kuota, Alur Magang, Verifikasi Dokumen Digital, Kebijakan Privasi, dan Unduh Panduan PDF).
4. **Modernisasi Tampilan Publik Terisolasi**: Mendesain ulang `kebijakan-privasi.blade.php` dengan visual Sleman Smart City (`#1b3860`, `#0751FF`, background elegan, header & footer resmi) agar serasi dengan landing page baru.
5. **Harmonisasi Halaman Autentikasi**: Menyempurnakan tautan "Kembali ke Beranda / Ganti Pilihan" di halaman pendaftaran agar mendarat presisi di `#kuota`.

---

## 2. Analisis Kebutuhan & File Terdampak

| Komponen / Tampilan | File Terkait | Masalah Saat Ini | Rencana Solusi Integrasi |
| :--- | :--- | :--- | :--- |
| **Footer Landing Page** | `resources/views/public/landing.blade.php` | Hanya memuat kontak instansi dan copyright, belum ada *Quick Links* navigasi. | Tambahkan 2 kolom link di footer: (1) Tautan Cepat Magang (Kuota, Alur, Unduh PDF Panduan), (2) Layanan & Informasi (Kebijakan Privasi, Cek Dokumen Sah, Portal Masuk). |
| **Halaman Kebijakan Privasi** | `resources/views/public/kebijakan-privasi.blade.php` | Desain dark neon mandiri kuno (`#0b132b`), terputus dari branding Sleman, tanpa header & footer resmi. | Redesain dengan tema Sleman Smart City, sertakan Header Diskominfo Sleman, tombol kembali ke landing page baru, dan link unduh panduan. |
| **Halaman Panduan Alur** | `resources/views/public/panduan-alur.blade.php` | CTA pendaftaran dan inspector card masih mengarah ke `#kuota-section` (anchor lama). | Ganti semua tautan CTA `#kuota-section` menjadi `{{ route('landing') }}#kuota`. |
| **Layout Navigasi Utama** | `resources/views/layouts/app.blade.php` | Tombol navbar *"Bidang & kuota"* dan *"Daftar magang"* masih mengarah ke `#kuota-section`. | Ubah target tautan menjadi `{{ route('landing') }}#kuota`. |
| **Halaman Verifikasi Dokumen** | `resources/views/public/verifikasi-dokumen.blade.php` | Header & footer masih standar, belum terhubung ke panduan alur & kebijakan privasi. | Tambahkan tautan panduan magang dan kebijakan privasi di footer, serta pastikan tombol kembali mengarah ke portal utama landing page. |
| **Halaman Pendaftaran & Login** | `resources/views/auth/register.blade.php` & `resources/views/layouts/auth.blade.php` | Tombol kembali umum tanpa konteks pemilihan kuota, footer form belum memuat Kebijakan Privasi. | Jika pendaftar datang dari pemilihan kuota, tombol kembali mengarah ke `route('landing') . '#kuota'`. Tambahkan link Kebijakan Privasi di bawah form auth. |

---

## 3. Tahapan Eksekusi Rinci

### Fase 1: Pembaruan Footer Landing Page Baru
- Memperluas grid footer di `resources/views/public/landing.blade.php`:
  - **Kolom 1: Profil Diskominfo Sleman** (Logo Sleman, nama instansi, alamat, badge Sleman Free WiFi melayang).
  - **Kolom 2: Navigasi Program** (Pilihan Kuota Magang `#kuota`, Peta Alur Magang `route('panduan-alur')`, Unduh Buku Panduan PDF `route('panduan.pdf')`, FAQ `#faq`).
  - **Kolom 3: Layanan & Verifikasi** (Verifikasi Keabsahan Dokumen Digital `url('/verifikasi-dokumen/cek')`, Kebijakan Privasi `route('kebijakan-privasi')`, Portal Masuk Pengguna `route('login')`).
  - **Kolom 4: Kontak & Saluran Aduan** (Email resmi, telepon, jam pelayanan kedinasan).
- Menyesuaikan CSS styling footer di `public/css/landing-modern.css` agar responsive di layar desktop maupun mobile.

### Fase 2: Redesain & Integrasi `kebijakan-privasi.blade.php`
- Menerapkan arsitektur visual modern:
  - Header resmi dengan Logo Sleman dan tombol `&larr; Kembali ke Beranda`.
  - Kartu dokumen privasi berbalut tema Sleman Smart City (`#1b3860`, font Plus Jakarta Sans, tipografi rapi, poin-poin perlindungan data pendaftar & keamanan signed URL dokumen).
  - Tautan kontekstual langsung ke pendaftaran kuota (`route('landing') . '#kuota'`) dan unduh buku panduan resmi.
  - Footer instansi yang selaras dengan landing page.

### Fase 3: Perbaikan Sinkronisasi Anchor `#kuota-section` ke `#kuota`
- Lakukan refactor pada:
  - `resources/views/public/panduan-alur.blade.php`: Ganti `#kuota-section` pada tombol CTA hero, sticky bar, dan node alur pendaftaran menjadi `#kuota`.
  - `resources/views/layouts/app.blade.php`: Ganti `#kuota-section` pada navbar desktop, navbar mobile drawer, dan tombol aksi "Daftar magang" menjadi `{{ route('landing') }}#kuota`.

### Fase 4: Penguatan Navigasi pada Halaman Autentikasi (`auth/login`, `auth/register`)
- Di `resources/views/layouts/auth.blade.php` dan `register.blade.php`:
  - Periksa query string `request()->hasAny(['bulan', 'jurusan', 'tipe', 'jumlah'])`. Jika ada, arahkan tombol *"Kembali ke beranda"* atau *"Ganti pilihan magang"* ke `{{ route('landing') }}#kuota`.
  - Di footer kartu form auth, cantumkan teks legal: *"Dengan mendaftar, Anda menyetujui [Kebijakan Privasi]({{ route('kebijakan-privasi') }}) MagKom Diskominfo Sleman"*.

### Fase 5: Uji Coba & Verifikasi
- Pengujian tautan navigasi bolak-balik:
  - Klik tautan dari Landing Page -> Kebijakan Privasi -> Kembali ke Landing Page.
  - Klik tautan dari Panduan Alur -> Kuota Landing Page (memastikan auto-scroll tepat ke seksi kuota).
  - Klik tombol "Ganti Jurusan/Kuota" dari Form Register -> Kuota Landing Page.
  - Klik link Verifikasi Dokumen dari Footer -> Verifikasi Dokumen -> Kembali ke Portal Utama.
- Validasi rendering dan responsive test pada layar desktop dan mobile.

---

## 4. Keamanan & Konvensi
- Tidak ada data rahasia atau kunci API yang diekspos.
- Menggunakan helper resmi Laravel `route(...)` untuk mencegah *broken links*.
- Seluruh kode sumber, variabel, dan komentar kode tetap ditulis dalam **Bahasa Inggris**.
- Penjelasan dan komunikasi kepada pengguna disampaikan dalam **Bahasa Indonesia**.
