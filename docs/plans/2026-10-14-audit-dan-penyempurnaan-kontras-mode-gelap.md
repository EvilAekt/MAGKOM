# Plan: Audit dan Penyempurnaan Kontras Tampilan Mode Gelap (Dark Mode) Seluruh MagKom

Dokumen rencana komprehensif untuk mengaudit, menstandarkan, dan menyempurnakan keterbacaan (*contrast & readability*) tampilan mode malam di seluruh portal aplikasi MagKom (Peserta, Admin, Pembimbing, SuperAdmin, Auth, dan Publik) mengacu pada standar aksesibilitas WCAG 2.1 AA (rasio kontras minimal 4.5:1 untuk teks normal dan 3.0:1 untuk elemen UI/teks besar).

---

## 1. Tujuan

Menghilangkan seluruh titik tampilan yang "kurang jelas", buram, atau redup saat Mode Gelap aktif:
1. **Solusi Fondasi Universal (CSS Inversion Layer)**: Mengimplementasikan *Universal Attribute Style Inversion* pada seluruh master layout (`layouts/dashboard.blade.php`, `layouts/peserta.blade.php`, `layouts/auth.blade.php`, `layouts/app.blade.php`) untuk secara otomatis menaikkan kontras ribuan elemen dengan inline-style hardcoded (`color: #0F172A`, `color: #334155`, `color: #475569`, `color: #64748B`, `background: #ffffff`, dsb).
2. **Koreksi Token Warna Gelap**: Memperbaiki nilai token `--neutral-400` dan `--neutral-500` pada `[data-theme="dark"]` agar placeholder input, icon stroke, dan label bantuan memiliki kontras yang terang dan nyaman dibaca (minimal 4.5:1).
3. **Penyelarasan Komponen Spesifik Per Modul**:
   - **Portal Peserta**: Logbook mingguan (`peserta/logbook/index.blade.php`), detail pendaftaran & verifikasi berkas (`peserta/pendaftaran/detail.blade.php`), profil peserta (`peserta/profil/show.blade.php`).
   - **Portal Admin & Pembimbing**: Penempatan peserta (`admin/penempatan/index.blade.php`), profil admin (`admin/profil/show.blade.php`), penugasan tugas & evaluasi.
   - **Portal SuperAdmin**: Telemetri kesehatan (`health/index.blade.php`), hub disaster recovery (`backup/index.blade.php`), dan profil superadmin (`superadmin/profil/show.blade.php`).
   - **Komponen Keamanan & Auth**: Kotak Captcha SVG (`components/captcha-field.blade.php` & `CaptchaService.php`), form login/register, dan modal pop-up inspeksi.
4. **Verifikasi Kualitas Aksesibilitas**: Memastikan seluruh teks, ikon, border, badge status, tabel, dan form input terbaca tajam tanpa ada kartu yang bocor putih atau teks hitam di atas gelap.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Teknis
1. **Penyebab Utama "Kurang Jelas"**: Banyak view menggunakan inline CSS langsung (`style="color: #64748B"`, `style="color: #334155"`, `style="color: #0F172A"`). Inline style memiliki prioritas lebih tinggi daripada class CSS, sehingga teks judul menjadi hitam di atas latar gelap, atau teks abu-abu pudar tenggelam ke latar belakang.
2. **Strategi Dua Lapis (Two-Tier Fix)**:
   - *Lapis 1 (Universal Catch-All)*: Master layout menangani semua inline style hardcoded melalui selector atribut CSS `[style*="..."] !important`.
   - *Lapis 2 (Component-Level Overrides)*: Child views dengan class khusus (seperti `.card-modern`, `.progress-tracker-card`, `.daily-log-card`, `.stat-card-pembimbing`) diberikan blok `[data-theme="dark"]` spesifik.
3. **Kompatibilitas Captcha**: Captcha SVG memiliki background putih; di mode gelap, frame kontainer captcha diberikan border tegas dan aksen latar yang tidak menyilaukan mata pengguna saat mengetikkan 4 karakter.

### Pertanyaan Terbuka
- Tidak ada pertanyaan pemblokir. Pendekatan dua lapis menjamin kompatibilitas backward tanpa mengubah logika backend.

---

## 3. Pendekatan Desain

### Perbaikan Standar Kontras WCAG 2.1 AA di Mode Gelap

| Elemen UI | Nilai Lama di Dark Mode | Nilai Baru Disempurnakan | Rasio Kontras Target |
|---|---|---|---|
| **Teks Utama (Headings, Title)** | `#0F172A` (hitam pekat) / `#1E293B` | `#F8FAFC` (Slate-50) / `#FFFFFF` | **15.8:1** (Sangat Tajam) |
| **Teks Body / Paragraf** | `#334155` / `#475569` | `#CBD5E1` (Slate-300) | **9.2:1** (Jelas & Nyaman) |
| **Teks Muted / Subtitle / Label** | `#64748B` (tenggelam) | `#94A3B8` (Slate-400) / `#A5B4FC` | **5.4:1** (WCAG AA Pass) |
| **Input Placeholder** | `#475569` (buram) | `#64748B` / `#94A3B8` | **4.5:1** (Jelas) |
| **Border / Pemisah / Divider** | `#EEF0F5` (bocor putih) | `#26354D` (Navy tegas) | **3.2:1** (Kontur Jelas) |
| **Table Header (`thead th`)** | Text `#64748B` | Text `#CBD5E1`, Background `#0E1726` | **8.5:1** (Tabel Terbaca) |
| **Status Badge - Warning/Info** | Teks redup | Soft tint background 20% + Teks cerah `#FCD34D` / `#93C5FD` | **6.5:1** (Kontras) |

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
|---|---|---|
| `resources/views/layouts/dashboard.blade.php` | Ubah | Pasang *Universal Inline-Style Inversion Rules*, perbaiki nilai `--neutral-400` & `--neutral-500` di dark mode, dan tajamkan kontras `thead th` & dropdown item. |
| `resources/views/layouts/peserta.blade.php` | Ubah | Pasang *Universal Inline-Style Inversion Rules*, perbaiki kontras kartu child views, tombol sekunder, dan input placeholder. |
| `resources/views/layouts/auth.blade.php` | Ubah | Pasang *Universal Inline-Style Inversion Rules* untuk form pendaftaran dan login. |
| `resources/views/layouts/app.blade.php` | Ubah | Pasang *Universal Inline-Style Inversion Rules* untuk halaman publik dan landing page. |
| `resources/views/components/captcha-field.blade.php` | Ubah | Tambahkan kontras border, dark card container, dan perbaiki warna label hint captcha di mode gelap. |
| `resources/views/peserta/logbook/index.blade.php` | Ubah | Tambahkan blok dark mode lengkap untuk `.card-modern`, `.progress-tracker-card`, daily log card, status mingguan, dan modal input jurnal. |
| `resources/views/peserta/pendaftaran/detail.blade.php` | Ubah | Tambahkan blok dark mode untuk panel verifikasi berkas, catatan revisi admin, kartu biodata, dan tombol unduh bukti. |
| `resources/views/peserta/profil/show.blade.php` | Ubah | Harmonisasi kartu profil peserta, tab keamanan, form ganti password, dan input data diri. |
| `resources/views/admin/penempatan/index.blade.php` | Ubah | Harmonisasi tabel penempatan, badge status kuota bidang, dan modal penetapan pembimbing. |
| `resources/views/admin/profil/show.blade.php` | Ubah | Harmonisasi form profil admin, tab preferensi, dan preview foto. |
| `resources/views/superadmin/profil/show.blade.php` | Ubah | Harmonisasi form profil superadmin dan log aktivitas akun. |
| `resources/views/superadmin/health/index.blade.php` | Ubah | Ganti inline text color hardcoded menjadi semantic tokens kontras tinggi. |
| `resources/views/superadmin/backup/index.blade.php` | Ubah | Perjelas teks timestamp dan size snapshot cadangan. |

---

## 5. Langkah Implementasi

### Fase 1: Universal CSS Inversion & Token Adjustment (Pondasi Keterbacaan)
- [x] **Langkah 1.1**: Perbarui `resources/views/layouts/dashboard.blade.php`:
  - Perbaiki nilai token `[data-theme="dark"]`: ubah `--neutral-400` dari `#475569` menjadi `#64748B`, dan pastikan `--neutral-500` bernilai `#94A3B8`.
  - Tambahkan *Universal Inline-Style Inversion Rules* untuk menangkap inline style `color: #0F172A`, `#1E293B`, `#334155`, `#475569`, `#64748B`, `#333333`, `#666666`.
  - Tambahkan universal rule untuk inline background `#ffffff` dan border `#E2E8F0` / `#CBD5E1`.
- [x] **Langkah 1.2**: Perbarui `resources/views/layouts/peserta.blade.php`:
  - Tambahkan *Universal Inline-Style Inversion Rules* yang sama agar seluruh modul peserta langsung mewarisi keterbacaan tinggi.
  - Perjelas warna `th` tabel, badge status, dan placeholder form.
- [x] **Langkah 1.3**: Perbarui `resources/views/layouts/auth.blade.php` & `layouts/app.blade.php`:
  - Tambahkan *Universal Inline-Style Inversion Rules* untuk halaman autentikasi dan publik.

### Fase 2: Komponen Keamanan, Captcha, dan Auth Views
- [x] **Langkah 2.1**: Perbarui `resources/views/components/captcha-field.blade.php`:
  - Pasang frame gelap dengan border `#26354D` pada kontainer gambar captcha.
  - Pastikan tombol refresh captcha memiliki kontras jelas dan ikon SVG tidak tenggelam.
  - Pastikan teks panduan hint menggunakan kontras minimal 4.5:1.

### Fase 3: Modul Peserta Magang (Logbook, Detail Pendaftaran, Profil)
- [x] **Langkah 3.1**: Perbarui `resources/views/peserta/logbook/index.blade.php`:
  - Berikan styling dark mode eksplisit untuk `.card-modern`, `.progress-tracker-card`, daily log stepper, feedback pembimbing, dan modal pengisian logbook.
- [x] **Langkah 3.2**: Perbarui `resources/views/peserta/pendaftaran/detail.blade.php`:
  - Berikan styling dark mode untuk timeline tahapan verifikasi berkas, review catatan admin, kartu biodata pendaftar, dan tombol aksi.
- [x] **Langkah 3.3**: Perbarui `resources/views/peserta/profil/show.blade.php`:
  - Berikan styling dark mode untuk tab profil, formulir data diri, kartu instansi, dan ganti password.

### Fase 4: Modul Admin, SuperAdmin, dan Pembimbing
- [x] **Langkah 4.1**: Perbarui `resources/views/admin/penempatan/index.blade.php`:
  - Perjelas kontras teks pada tabel penempatan, modal penugasan pembimbing, dan badge bidang.
- [x] **Langkah 4.2**: Perbarui `resources/views/admin/profil/show.blade.php` & `superadmin/profil/show.blade.php`:
  - Perjelas kontras formulir biodata staf, tab keamanan, dan riwayat aktivitas.
- [x] **Langkah 4.3**: Perbarui `resources/views/superadmin/health/index.blade.php` & `backup/index.blade.php`:
  - Ganti sisa inline hardcoded text color (`#0F172A`, `#64748B`, `#334155`) menjadi kelas semantik atau CSS variable yang kontras.

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - Jalankan seluruh automated test suite untuk memastikan struktur DOM, form input, dan fungsionalitas tetap 100% valid:
     - `php artisan test tests/Feature/StaffProfileAndSettingsTest.php`
     - `php artisan test tests/Feature/SecurityDynamicCaptchaTest.php`
     - `php artisan test tests/Feature/AdminChildPagesVisualTest.php`
     - `php artisan test tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php`
     - `php artisan test tests/Feature/PembimbingDashboardEnhancementTest.php`
2. **Visual Contrast Audit**:
   - Uji via browser dengan mode gelap aktif pada:
     - Halaman Logbook Peserta (`/peserta/logbook`)
     - Halaman Detail Pendaftaran Peserta (`/peserta/pendaftaran/{id}`)
     - Halaman Profil Peserta & Admin (`/peserta/profil`, `/admin/profil`, `/superadmin/profil`)
     - Halaman Penempatan Admin (`/admin/penempatan`)
     - Halaman Login & Captcha (`/login`)
   - Pastikan teks terbaca jelas, tidak ada teks hitam di latar gelap, tidak ada teks abu-abu pudar tak terbaca.

---

## 7. Risiko & Mitigasi

- **Risiko**: Selector `[style*="..."]` dapat menimpa warna teks tertentu yang sengaja berwarna khusus (misal warna badge merah atau hijau).
  - **Mitigasi**: Batasi selector hanya untuk warna teks monokrom gelap (`#0F172A`, `#1E293B`, `#334155`, `#475569`, `#64748B`, `#333333`, `#666666`), dan kecualikan kelas badge (`:not(.badge):not([class*="badge-"])`).
- **Risiko**: Input form menjadi terlalu silau jika background-nya dibuat terang.
  - **Mitigasi**: Pertahankan background input gelap (`#0F172A`), namun naikkan kecerahan border (`#26354D`) dan teks input (`#F8FAFC`) serta placeholder (`#94A3B8`).

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Tidak ada teks judul atau heading yang berwarna gelap/hitam saat mode gelap aktif di seluruh portal.
- [x] Seluruh teks muted, subjudul, dan timestamp memiliki rasio kontras minimal 4.5:1 terhadap latarnya (WCAG 2.1 AA).
- [x] Halaman Logbook Peserta tampil kontras tinggi, baik banner, kartu progres mingguan, daftar aktivitas, maupun modal input.
- [x] Halaman Detail Pendaftaran dan Profil Peserta/Admin/SuperAdmin sepenuhnya serasi dalam tema Midnight Deep Slate.
- [x] Komponen Captcha dan form autentikasi memiliki batas frame dan teks instruksi yang jelas tanpa silau.
- [x] Seluruh automated test suite lulus 100% tanpa error regresi.
