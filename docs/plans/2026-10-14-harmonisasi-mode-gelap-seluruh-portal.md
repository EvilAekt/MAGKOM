# Plan: Harmonisasi Warna Tampilan Mode Gelap (Dark Mode) Seluruh Portal MagKom

Dokumen rencana kerja teknis untuk melakukan standardisasi dan harmonisasi estetika visual Mode Gelap (*Dark Mode*) di seluruh modul dan portal aplikasi MagKom (Portal Peserta, Portal Admin, Portal Pembimbing, Portal SuperAdmin, Portal Autentikasi, dan Halaman Publik) mengacu pada standar Design System DS-MagKom Bab 2 dan estetika high-contrast slate navy.

---

## 1. Tujuan

Menciptakan pengalaman visual mode malam (*Dark Mode*) yang konsisten, elegan, nyaman di mata (anti-silau), serta memenuhi standar aksesibilitas WCAG 2.1 AA di **seluruh halaman aplikasi MagKom**:
1. **Palet Warna Terpadu**: Mengganti semua elemen yang masih menggunakan background putih solid (`#FFFFFF` / `bg-white`) dan teks abu-abu pudar saat mode gelap aktif menjadi palet *Midnight Deep Slate* (`#0A0F1D` canvas, `#111C2E` / `#182234` card surface, `#0F172A` input, `#26354D` border).
2. **Kontras & Keterbacaan Tinggi**: Memastikan teks heading (`#F8FAFC`), body text (`#CBD5E1`), dan muted text (`#94A3B8`) memiliki rasio kontras minimal 4.5:1 terhadap latarnya.
3. **Cakupan Menyeluruh (Universal Coverage)**:
   - **Master Layouts**: `layouts/dashboard.blade.php`, `layouts/peserta.blade.php`, `layouts/auth.blade.php`, `layouts/app.blade.php`.
   - **Komponen Universal**: Form controls (input, select, textarea), tabel data & thead, modal dialog & backdrop, pagination, status badges, dropdown menu, tabs, alert/toasts.
   - **Seluruh Child Views**: Portal Peserta, Portal Admin, Portal Pembimbing, Portal SuperAdmin, dan seluruh form Autentikasi.
4. **Anti-Flash Theme Switching**: Tidak ada kedipan putih (*white flash*) saat berpindah halaman atau merefresh browser pada mode gelap.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis
1. **Mekanisme Switch Tema**: Sistem menggunakan atribut semantik `data-theme="dark"` pada elemen `<html>` yang disinkronkan dengan `localStorage.getItem('magkom_theme')` ('terang', 'gelap', 'sistem') dan preferensi cookie/session di backend.
2. **Pola Referensi Visual**: Modul *Tugas & Proyek Peserta* (`peserta/tugas/show.blade.php`) dan *Toast Notification* baru yang telah disetujui dijadikan acuan standar warna (border navy tegas `#26354D`, kartu `#182234`, soft badge glow, dan aksen biru `#3B82F6`).
3. **Kompatibilitas Tailwind & Custom CSS**: Aplikasi menggunakan kombinasi utilitas Tailwind dan custom CSS tokens. Seluruh styling dark mode akan diikat pada selector atribut `[data-theme="dark"]` agar bekerja konsisten di seluruh elemen HTML.

### Pertanyaan Terbuka
- Apakah preferensi tema pada halaman Landing Page publik perlu menyertakan tombol toggle manual di navbar (seperti ikon matahari/bulan), atau otomatis mendeteksi preferensi sistem OS pengguna (`prefers-color-scheme`)?
  - *Rekomendasi*: Menyediakan deteksi otomatis preferensi OS (`prefers-color-scheme`) pada halaman publik dengan fallback terang, serta tombol toggle opsional di navbar.

---

## 3. Pendekatan Desain

### Standar Token Warna Mode Gelap (Dark Mode System DS-MagKom)

| Token Semantik | Light Mode | Dark Mode (`[data-theme="dark"]`) | Deskripsi Peran |
|---|---|---|---|
| `--bg-canvas` | `#F8FAFC` (Slate-50) | `#0A0F1D` (Midnight Deep Slate) | Latar belakang halaman paling dasar |
| `--bg-surface` | `#FFFFFF` (Pure White) | `#111C2E` / `#182234` | Kartu konten, panel sidebar, topbar |
| `--bg-elevated` | `#FFFFFF` | `#1E293B` (Slate-800) | Modal pop-up, dropdown menu, tooltip |
| `--bg-input` | `#FFFFFF` | `#0F172A` (Slate-900) | Input field, select, textarea |
| `--border-subtle` | `#F1F5F9` | `#1E293B` | Garis pembatas halus / divider |
| `--border-default` | `#E2E8F0` | `#26354D` | Border kartu, panel, dan tabel |
| `--border-focus` | `#0751FF` | `#3B82F6` | Outline / border saat interaksi fokus |
| `--text-heading` | `#0F172A` | `#F8FAFC` | Judul halaman, nama pengguna, modal title |
| `--text-body` | `#334155` | `#CBD5E1` | Isi paragraf, nilai tabel, teks formulir |
| `--text-muted` | `#64748B` | `#94A3B8` | Subjudul, label bantuan, timestamp |
| `--badge-blue` | Surface `#EFF6FF`, Text `#1D4ED8` | Surface `rgba(37, 99, 235, 0.20)`, Border `#2563EB`, Text `#93C5FD` | Badge informasi / tugas aktif |
| `--badge-green` | Surface `#ECFDF5`, Text `#047857` | Surface `rgba(16, 185, 129, 0.20)`, Border `#10B981`, Text `#6EE7B7` | Badge sukses / disetujui / aktif |
| `--badge-amber` | Surface `#FFFBEB`, Text `#B45309` | Surface `rgba(217, 119, 6, 0.20)`, Border `#F59E0B`, Text `#FCD34D` | Badge proses / menunggu verifikasi |
| `--badge-red` | Surface `#FEF2F2`, Text `#B91C1C` | Surface `rgba(239, 68, 68, 0.20)`, Border `#EF4444`, Text `#FCA5A5` | Badge revisi / ditolak / peringatan |

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
|---|---|---|
| `resources/views/layouts/dashboard.blade.php` | Ubah | Standarisasi token CSS dark mode menyeluruh: form input, select, textarea, tabel, thead, modal dialog, pagination, dropdown, dan badge. |
| `resources/views/layouts/peserta.blade.php` | Ubah | Standarisasi token CSS dark mode menyeluruh untuk form control, tabel riwayat, modal, dan drawer navigasi. |
| `resources/views/layouts/auth.blade.php` | Ubah | Tambahkan script anti-flash theme di `<head>` dan styling dark mode untuk card auth, formulir input, background midnight slate, dan teks pendukung. |
| `resources/views/layouts/app.blade.php` | Ubah | Tambahkan dukungan token dark mode untuk layout publik (header, navbar, footer). |
| `resources/views/auth/*.blade.php` (6 file) | Ubah | Sesuaikan card login, register, forgot-password, reset-password, verify-email, ganti-password agar serasi dalam mode gelap. |
| `resources/views/peserta/dashboard.blade.php` | Ubah | Harmonisasi kartu KPI metrik, status pengajuan, quick action, timeline aktivitas ke palet dark slate. |
| `resources/views/peserta/pendaftaran/index.blade.php` & `detail.blade.php` | Ubah | Harmonisasi wizard pendaftaran, kartu upload berkas, list verifikasi persyaratan. |
| `resources/views/peserta/logbook/index.blade.php` | Ubah | Harmonisasi modal logbook harian, status mingguan, card preview aktivitas. |
| `resources/views/peserta/nilai/show.blade.php` | Ubah | Harmonisasi lembar nilai, kartu predikat kompetensi, dan tombol sertifikat. |
| `resources/views/admin/dashboard.blade.php` | Ubah | Harmonisasi KPI operasional, grafik antrean verifikasi, dan timeline penempatan. |
| `resources/views/admin/pendaftaran/index.blade.php` & `show.blade.php` | Ubah | Harmonisasi tabel antrean verifikasi, panel preview berkas/PDF viewer, modal reviu/tolak. |
| `resources/views/admin/kuota/index.blade.php` | Ubah | Harmonisasi matriks kuota jurusan/bulan dan modal konfigurasi kuota. |
| `resources/views/admin/penempatan/index.blade.php` & `show.blade.php` | Ubah | Harmonisasi tabel penempatan, floating action bar, modal assign pembimbing. |
| `resources/views/admin/waiting-list/index.blade.php` | Ubah | Harmonisasi tabel waiting list dan kartu kuota otomatis. |
| `resources/views/admin/laporan/index.blade.php` | Ubah | Harmonisasi filter arsip, kartu statistik rekap, dan tabel cetak laporan. |
| `resources/views/pembimbing/dashboard.blade.php` | Ubah | Harmonisasi antrean reviu logbook, kartu metrik peserta bimbingan, agenda aktif. |
| `resources/views/pembimbing/tugas/*.blade.php` (5 file) | Ubah | Harmonisasi form pembuat tugas classroom, template list, modal review pengumpulan. |
| `resources/views/pembimbing/logbook/index.blade.php` | Ubah | Harmonisasi timeline kalender aktivitas mingguan, modal skor dan umpan balik pembimbing. |
| `resources/views/pembimbing/agenda/index.blade.php` | Ubah | Harmonisasi timeline agenda bimbingan tatap muka / daring dan modal form agenda. |
| `resources/views/pembimbing/penilaian/index.blade.php` | Ubah | Harmonisasi rubrik scoring evaluasi peserta dan form kalkulasi predikat. |
| `resources/views/superadmin/dashboard.blade.php` | Ubah | Harmonisasi kartu status server, metrik pengguna, ringkasan log audit. |
| `resources/views/superadmin/akun/index.blade.php` | Ubah | Harmonisasi tabel pengguna/role, modal edit status akun/reset password. |
| `resources/views/superadmin/health/index.blade.php` | Ubah | Harmonisasi meteran indikator health database, disk storage, dan event log. |
| `resources/views/superadmin/backup/index.blade.php` | Ubah | Harmonisasi snapshot database list dan action download. |
| `resources/views/superadmin/audit/index.blade.php` | Ubah | Harmonisasi filter bar audit log, JSON viewer, dan tabel jejak audit. |
| `resources/views/public/landing.blade.php` | Ubah | Harmonisasi matriks transparansi kuota publik, kartu informasi alur, FAQ accordion. |

---

## 5. Langkah Implementasi

### Fase 1: Standarisasi Master Layouts & Universal Components (Foundation)
- [x] **Langkah 1.1**: Perbarui `resources/views/layouts/dashboard.blade.php`:
  - Perkaya token CSS `[data-theme="dark"]` untuk menargetkan seluruh elemen universal (semua `.app-card`, `.card`, input formulir, `select`, `textarea`, `.table`, `th`, `td`, modal dialog `.modal-content`, pagination, dropdown menu, badge status).
- [x] **Langkah 1.2**: Perbarui `resources/views/layouts/peserta.blade.php`:
  - Pastikan token CSS `[data-theme="dark"]` mencakup form controls, table containers, modal windows, dan drawer navigasi mobile.
- [x] **Langkah 1.3**: Perbarui `resources/views/layouts/auth.blade.php`:
  - Pasang script anti-flash theme di `<head>`.
  - Pasang styling `[data-theme="dark"]` untuk canvas (`#0A0F1D`), auth card (`#182234`), border (`#26354D`), label (`#CBD5E1`), input (`#0F172A`), dan button primary.
- [x] **Langkah 1.4**: Perbarui `resources/views/layouts/app.blade.php`:
  - Tambahkan script anti-flash theme dan styling dark mode untuk header navbar, background halaman, dan footer publik.

### Fase 2: Harmonisasi Portal Autentikasi (Auth Views)
- [x] **Langkah 2.1**: Selaraskan dark mode pada `resources/views/auth/login.blade.php` & `register.blade.php`.
- [x] **Langkah 2.2**: Selaraskan dark mode pada `resources/views/auth/forgot-password.blade.php`, `reset-password.blade.php`, `verify-email.blade.php`, dan `ganti-password.blade.php`.

### Fase 3: Harmonisasi Portal Peserta Magang
- [x] **Langkah 3.1**: Selaraskan dark mode pada Dashboard Peserta (`resources/views/peserta/dashboard.blade.php`).
- [x] **Langkah 3.2**: Selaraskan dark mode pada Menu Pendaftaran & Detail Pendaftaran (`resources/views/peserta/pendaftaran/index.blade.php` & `detail.blade.php`).
- [x] **Langkah 3.3**: Selaraskan dark mode pada Menu Logbook Mingguan & Modal Pengisian (`resources/views/peserta/logbook/index.blade.php`).
- [x] **Langkah 3.4**: Selaraskan dark mode pada Menu Lembar Nilai & Sertifikat (`resources/views/peserta/nilai/show.blade.php`).
- [x] **Langkah 3.5**: Selaraskan dark mode pada Menu Survei (`resources/views/peserta/survei/index.blade.php`).

### Fase 4: Harmonisasi Portal Admin
- [x] **Langkah 4.1**: Selaraskan dark mode pada Dashboard Admin (`resources/views/admin/dashboard.blade.php`).
- [x] **Langkah 4.2**: Selaraskan dark mode pada Verifikasi Berkas Pendaftaran & Pratinjau Dokumen (`resources/views/admin/pendaftaran/index.blade.php` & `show.blade.php`).
- [x] **Langkah 4.3**: Selaraskan dark mode pada Manajemen Kuota & Modal Kuota (`resources/views/admin/kuota/index.blade.php`).
- [x] **Langkah 4.4**: Selaraskan dark mode pada Penempatan Peserta & Floating Batch Reassign Bar (`resources/views/admin/penempatan/index.blade.php` & `show.blade.php`).
- [x] **Langkah 4.5**: Selaraskan dark mode pada Waiting List (`resources/views/admin/waiting-list/index.blade.php`).
- [x] **Langkah 4.6**: Selaraskan dark mode pada Laporan & Arsip (`resources/views/admin/laporan/index.blade.php`).

### Fase 5: Harmonisasi Portal Pembimbing Lapangan
- [x] **Langkah 5.1**: Selaraskan dark mode pada Dashboard Pembimbing (`resources/views/pembimbing/dashboard.blade.php`).
- [x] **Langkah 5.2**: Selaraskan dark mode pada Manajemen Tugas Classroom (`resources/views/pembimbing/tugas/*.blade.php`).
- [x] **Langkah 5.3**: Selaraskan dark mode pada Reviu Logbook Peserta & Modal Evaluasi (`resources/views/pembimbing/logbook/index.blade.php`).
- [x] **Langkah 5.4**: Selaraskan dark mode pada Agenda Bimbingan (`resources/views/pembimbing/agenda/index.blade.php`).
- [x] **Langkah 5.5**: Selaraskan dark mode pada Penilaian Akhir & Rubrik Kompetensi (`resources/views/pembimbing/penilaian/index.blade.php`).

### Fase 6: Harmonisasi Portal SuperAdmin & Publik
- [x] **Langkah 6.1**: Selaraskan dark mode pada Dashboard Eksekutif SuperAdmin (`resources/views/superadmin/dashboard.blade.php`).
- [x] **Langkah 6.2**: Selaraskan dark mode pada Manajemen Akun & Pengguna (`resources/views/superadmin/akun/index.blade.php`).
- [x] **Langkah 6.3**: Selaraskan dark mode pada Health Status, Backup, & Audit Log (`resources/views/superadmin/health/index.blade.php`, `backup/index.blade.php`, `audit-log/index.blade.php`).
- [x] **Langkah 6.4**: Selaraskan dark mode pada Landing Page Informasi Kuota (`resources/views/public/landing.blade.php` & `kebijakan-privasi.blade.php`).

---

## 6. Strategi Testing

1. **Automated Feature & Visual Tests**:
   - Jalankan seluruh rangkaian test suite untuk memastikan markup yang diselaraskan tidak merusak fungsionalitas dan selektor assertions:
     - `php artisan test tests/Feature/StaffProfileAndSettingsTest.php`
     - `php artisan test tests/Feature/PesertaPortalWorkflowTest.php`
     - `php artisan test tests/Feature/AdminChildPagesVisualTest.php`
     - `php artisan test tests/Feature/AdminChildPagesExtendedVisualTest.php`
     - `php artisan test tests/Feature/DashboardLayoutTest.php`
     - `php artisan test tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`
2. **Manual Visual QA & Contrast Audit**:
   - Buka browser dan ubah preferensi ke Mode Gelap (*Dark Mode*).
   - Periksa seluruh portal (Peserta, Pembimbing, Admin, SuperAdmin, Auth, Landing):
     - Pastikan tidak ada kartu yang tersisa dengan background putih menyilaukan.
     - Pastikan seluruh tabel memiliki garis pembatas border navy `#26354D` dan teks terbaca jelas.
     - Buka modal dialog di setiap portal (modal logbook, modal tugas, modal verifikasi berkas, modal penempatan) dan pastikan backdrop gelap dengan isi dialog kontras sempurna.
     - Uji transisi perpindahan halaman untuk memastikan nihil kedipan putih (*zero white flash*).

---

## 7. Risiko & Mitigasi

- **Risiko**: Masih ada elemen child view dengan class utility Tailwind statis seperti `bg-white`, `text-gray-900`, atau `border-gray-200` yang mengabaikan dark mode.
  - **Mitigasi**: Di master layout `layouts/dashboard.blade.php` dan `layouts/peserta.blade.php`, pasang aturan CSS spesifik `[data-theme="dark"]` yang secara cerdas meng-override komponen utama (kartu, form, tabel, modal), dan lakukan penyesuaian kelas di child view terkait.
- **Risiko**: Text placeholder input menjadi terlalu gelap sehingga tidak terlihat, atau border form menghilang di latar gelap.
  - **Mitigasi**: Berikan token input spesifik: background `#0F172A`, border `#26354D`, text `#F8FAFC`, placeholder `#64748B`, dan focus ring `#3B82F6`.
- **Risiko**: Badge warna warni (merah, kuning, hijau) menjadi menyilaukan jika memakai warna solid terang di dark mode.
  - **Mitigasi**: Terapkan teknik *tinted dark badges* (background dengan transparansi 15-20% `rgba(...)`, teks aksen cerah, border halus 1px).

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Seluruh halaman portal Peserta tampil harmonis, nyaman, dan kontras tinggi di Mode Gelap.
- [x] Seluruh halaman portal Admin tampil harmonis di Mode Gelap (termasuk verifikasi berkas, kuota, penempatan, dan laporan).
- [x] Seluruh halaman portal Pembimbing tampil harmonis di Mode Gelap (termasuk penugasan, reviu logbook, agenda, dan penilaian).
- [x] Seluruh halaman portal SuperAdmin tampil harmonis di Mode Gelap (termasuk dashboard eksekutif, manajemen akun, audit log, backup, dan health monitor).
- [x] Halaman autentikasi (login, register, reset password, verify email) sepenuhnya mendukung Mode Gelap tanpa kartu putih silau.
- [x] Seluruh modal pop-up, form control, tabel, dan badge status di semua modul selaras dalam palet slate navy.
- [x] Tidak ada kedipan putih (*white flash*) saat refresh atau navigasi halaman.
- [x] Seluruh automated test suite Laravel lulus 100% tanpa error regresi.
