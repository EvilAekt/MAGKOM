# DOKUMEN TUGAS SENIOR DEVELOPER
## Fitur: Full Headless / Dynamic Landing Page CMS (Zero Hardcode)
### Modul Super Admin — MAGKOM

---

**Kepada:** Junior Developer / AI Coding Agent (Gemini 3.8 Flash, Cursor, Copilot)  
**Dari:** Senior Lead Software Engineer  
**Tanggal:** 7 November 2026  
**Status:** Ready for Step-by-Step Implementation  
**Prioritas:** P1 — Core Content Management Enhancement  
**Target:** 100% Konten Landing Page Terkelola Dinamis via Antarmuka CMS Super Admin (Zero Hardcode)

---

## 1. LATAR BELAKANG & TUJUAN BISNIS

Portal **MAGKOM (Magang Kominfo)** melayani ribuan calon peserta magang setiap tahunnya. Seiring perubahan kebijakan kedinasan, tahun anggaran, nomenklatur unit kerja, tautan sosial, alur seleksi, serta instruksi pendaftaran, pengelola instansi (Super Admin) memerlukan kendali penuh untuk memperbarui antarmuka Landing Page publik tanpa harus bergantung pada perubahan kode sumber (*no redeployment required*).

### Masalah yang Diselesaikan:
Sebelumnya, sebagian area Landing Page seperti judul SEO meta, teks header/navbar, teks instruksi pemilihan kuota, label tombol aksi, teks badge tahun, dan teks catatan tim masih berstatus *hardcoded* di view Blade `landing.blade.php`. Jika instansi berganti tahun (misal 2027), mengubah label pertanyaan jenjang, atau menyesuaikan instruksi tim, developer harus mengedit template Blade secara manual.

### Sasaran Utama:
Mengubah **100% seluruh elemen teks, label, judul, subjudul, instruksi, dan tombol pada Landing Page** agar tersambung secara dinamis ke tabel `pengaturan` melalui helper `Pengaturan::get()` dan `Pengaturan::json()`, dengan mekanisme *graceful fallback* ke teks bawaan yang sudah ada.

---

## 2. ARSITEKTUR & PETA KONTEN DINAMIS (ZERO HARDCODE)

Kita membagi seluruh Landing Page menjadi 6 modul terstruktur pada antarmuka CMS Super Admin:

```
[ CMS Super Admin: Kelola Landing Page ]
   ├── Tab 1: Header & SEO Meta
   │     ├── meta_title, meta_desc
   │     ├── header_brand_name, header_brand_sub
   │     └── header_nav_links (Beranda, Kuota, Tahapan, Peta Alur, FAQ)
   ├── Tab 2: Hero & Media Banner
   │     ├── hero_kicker, hero_title, hero_year_badge, hero_period_info, hero_cta_text
   │     ├── label_persyaratan, persyaratan_umum
   │     ├── label_dokumen, dokumen_pendaftaran
   │     └── media_foto (Banner) & media_pdf (Panduan)
   ├── Tab 3: Ketersediaan Kuota & Formasi (Pilihan Interaktif)
   │     ├── kuota_heading, kuota_subheading
   │     ├── q1_jenjang_label, q1_pill_smk_label, q1_pill_mahasiswa_label
   │     ├── q2_bulan_label, q2_empty_text
   │     ├── q3_mode_label, q3_pill_mandiri_label, q3_pill_kelompok_label
   │     ├── team_panel_label, team_note_text
   │     ├── gauge_desc_text
   │     ├── legend_tersedia, legend_hampir, legend_penuh
   │     └── btn_daftar_tersedia, btn_daftar_waiting, btn_daftar_tutup
   ├── Tab 4: Alur & Tahapan Magang (Sinkron Peta Alur)
   │     ├── tahapan_heading, tahapan_summary (4 poin)
   │     ├── btn_panduan_label, btn_peta_alur_label
   │     └── 4 Kartu Checkpoint (01 s/d 04: judul, badge, deskripsi)
   ├── Tab 5: FAQ (Tanya Jawab Accordion)
   │     ├── faq_heading
   │     └── 5 Kategori FAQ (JSON Q/A items)
   └── Tab 6: Footer & Kontak Kedinasan
         ├── footer_alamat, footer_telepon, footer_email, footer_jam_layanan
         ├── footer_medsos (JSON), footer_tautan (JSON), footer_akun (JSON)
         └── footer_copyright
```

---

## 3. SKEMA KUNCI PENGATURAN (DATA DICTIONARY)

Seluruh data disimpan pada tabel database `pengaturan` (kolom `kunci` VARCHAR(100) dan `nilai` TEXT) dengan caching otomatis:

### A. Kunci Header & SEO Meta
| Kunci Pengaturan | Tipe | Nilai Bawaan (Fallback) |
|---|---|---|
| `landing_meta_title` | String | `MAGKOM – Portal Magang Diskominfo Sleman 2026` |
| `landing_meta_desc` | String | `Daftar magang di Diskominfo Sleman: lihat persyaratan, kuota per bidang, dan tahapan pendaftaran.` |
| `landing_header_brand_name` | String | `MAGKOM` |
| `landing_header_brand_sub` | String | `Portal Magang · Diskominfo Sleman` |
| `landing_header_nav` | JSON | `[{"label":"Beranda","url":"#beranda"},{"label":"Kuota","url":"#kuota"},{"label":"Tahapan","url":"#tahapan"},{"label":"Peta Alur","url":"#tahapan"},{"label":"FAQ","url":"#faq"}]` |

### B. Kunci Hero Banner & Media
| Kunci Pengaturan | Tipe | Nilai Bawaan (Fallback) |
|---|---|---|
| `landing_hero_kicker` | String | `Kabupaten Sleman` |
| `landing_hero_title` | String | `Magang Kominfo Periode II` |
| `landing_hero_year` | String | `2026` |
| `landing_hero_period_info` | String | `Pendaftaran 1–15 Okt 2026 · mulai 2 Nov 2026` |
| `landing_hero_cta_text` | String | `Daftar Sekarang` |
| `landing_label_persyaratan` | String | `Persyaratan Umum` |
| `landing_persyaratan_umum` | Text | `Siswa SMK aktif atau mahasiswa aktif...\nBersedia magang...\nMengikuti jadwal...` |
| `landing_label_dokumen` | String | `Dokumen Pendaftaran` |
| `landing_dokumen_pendaftaran` | Text | `Surat Pengantar Sekolah...\nCurriculum Vitae (CV)...\nTranskrip / Rapor...\nPas Foto` |
| `landing_hero_image` | String | `(Path Storage foto)` |
| `landing_panduan_pdf` | String | `(Path Storage PDF)` |

### C. Kunci Interaktif Kuota & Formasi
| Kunci Pengaturan | Tipe | Nilai Bawaan (Fallback) |
|---|---|---|
| `landing_kuota_heading` | String | `Ketersediaan Kuota Magang` |
| `landing_kuota_subheading` | String | `Pilih jenjang pendidikan dan bulan mulai magang untuk melihat kuota jurusan yang tersedia.` |
| `landing_kuota_q1_label` | String | `1. Kamu siapa?` |
| `landing_kuota_pill_smk` | String | `Siswa SMK` |
| `landing_kuota_pill_mhs` | String | `Mahasiswa (D3/S1)` |
| `landing_kuota_q2_label` | String | `2. Mau mulai kapan?` |
| `landing_kuota_q3_label` | String | `3. Magang sama siapa?` |
| `landing_kuota_pill_mandiri`| String | `Individu (1 Orang)` |
| `landing_kuota_pill_tim` | String | `Kelompok (Tim)` |
| `landing_kuota_team_label` | String | `Berapa orang dalam tim kamu?` |
| `landing_kuota_team_note` | String | `Cukup 1 perwakilan/ketua yang mendaftar. Data rekan tim dimasukkan langsung di form.` |
| `landing_kuota_gauge_desc` | String | `Memuat informasi ketersediaan kuota...` |
| `landing_kuota_legend_tersedia`| String | `Masih banyak` |
| `landing_kuota_legend_hampir`  | String | `Tinggal sedikit` |
| `landing_kuota_legend_penuh`   | String | `Penuh / Waiting List` |
| `landing_kuota_btn_tersedia`   | String | `Daftar Sekarang ➔` |
| `landing_kuota_btn_waiting`    | String | `Daftar Waiting List ➔` |
| `landing_kuota_btn_tutup`      | String | `Pendaftaran Ditutup` |

### D. Kunci Tahapan, FAQ, Footer, dan Kebijakan Privasi
*(Sudah dikembangkan pada iterasi sebelumnya, diperluas dengan label tombol dan link akun)*
| Kunci Pengaturan | Tipe | Nilai Bawaan (Fallback) |
|---|---|---|
| `landing_tahapan_btn_pdf` | String | `Unduh Panduan PDF` |
| `landing_tahapan_btn_peta`| String | `Peta Alur Interaktif` |
| `landing_footer_akun` | JSON | `[{"label":"Masuk Peserta","url":"/login"},{"label":"Masuk Pembimbing","url":"/login"},{"label":"Masuk Admin","url":"/login"}]` |

---

## 4. BREAKDOWN TUGAS BERTAPAH (STEP-BY-STEP RUNBOOK)

Junior Developer / AI wajib menyelesaikan tugas ini secara berurutan:

### **Langkah 1: Perluas Controller CMS (`LandingCmsController.php`)**
- Tambahkan parsing dan data default untuk section:
  1. `header_seo`: Meta title, meta description, brand name, brand subtitle, navbar links.
  2. `kuota_copy`: Heading, subheading, label Q1-Q3, pills, team info note, legend labels, button CTA labels.
  3. Perluas `update()` untuk menangani `section === 'header_seo'` dan `section === 'kuota'`.
  4. Catat seluruh audit trail aksi Super Admin saat memperbarui konfigurasi.

### **Langkah 2: Dinamisasi Total `resources/views/public/landing.blade.php`**
- Ganti `<title>` dan `<meta name="description">` dengan helper `Pengaturan::get()`.
- Ganti brand title, subtext, nav links dengan binding dinamis.
- Ganti Section Kuota (Heading, sub, Q1 label, pills, Q2 label, Q3 label, mode pills, team label, team note, gauge desc, legend, card action buttons).
- Ganti label tombol download PDF dan Peta Alur pada Section Tahapan.
- Ganti Kolom Akun di footer dengan loop dinamis `landing_footer_akun`.

### **Langkah 3: Pembaruan Antarmuka CMS Super Admin (`superadmin/landing/index.blade.php`)**
- Tambahkan tab navigasi:
  - Tab 1: Header & SEO Meta
  - Tab 2: Hero & Media Banner
  - Tab 3: Kuota & Formasi (Interactive Copy)
  - Tab 4: Alur & Tahapan
  - Tab 5: FAQ
  - Tab 6: Footer & Kontak
- Buat input form yang bersih, terorganisir dengan label jelas dan petunjuk bantuan.

### **Langkah 4: Automated Testing & Verifikasi Mutu**
- Tambahkan skenario feature test di `tests/Feature/SuperAdminModuleEnhancementTest.php`:
  1. `test_superadmin_can_update_header_and_seo_meta()`
  2. `test_superadmin_can_update_kuota_section_copy()`
  3. Verifikasi response HTML landing page memuat teks baru yang disimpan.
- Jalankan pemeriksaan kualitas:
  1. `php artisan test --filter=SuperAdminModuleEnhancementTest`
  2. `./vendor/bin/pint --test`
  3. `npm run build`

---

## 5. QUALITY GATE CHECKLIST

- [ ] Seluruh teks di landing page dapat diubah dari CMS tanpa hardcode.
- [ ] Fallback default data bekerja aman (anti-HTTP 500 saat database baru/kosong).
- [ ] Validasi form terisolasi per section.
- [ ] XSS Sanitization: menggunakan Blade escaping default `{{ }}` pada seluruh input dinamis.
- [ ] Audit Trail tercatat rapi pada tabel `audit_log`.
- [ ] Semua automated tests hijau (PASS).

