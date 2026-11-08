# PRD & Implementation Plan: CMS Terpadu Landing Page, Alur Magang, FAQ, Footer, & Kebijakan Privasi

**Target Audience:** Junior Developers & Budget AI Agents (Gemini 3.8 Flash, Claude Haiku, dsb.)  
**Document Type:** Senior Developer Task Brief / Technical Architecture & Implementation Plan  
**Repository:** MagKom (Portal Manajemen Magang Kominfo Sleman)  
**Author:** Senior Developer / Engineering Lead  
**Stack Baseline:** PHP 8.3 | Laravel 10 | Blade | Vanilla CSS | Vanilla JS (ES Modules) | Vite | MySQL / SQLite  
**Modul Target:** Modul Super Admin (CMS Landing Page & Pengaturan Publik)  
**Tanggal:** 7 November 2026  
**Referensi Gambar Pengguna:**  
- Gambar 1: Hero Card (Persyaratan Umum & Dokumen Pendaftaran)  
- Gambar 2: Tahapan Pendaftaran Magang (Heading, Ringkasan, 4 Langkah Jalan)  
- Gambar 3: FAQ (Pertanyaan yang Sering Ditanyakan, 5 Kategori Akordion)  
- Gambar 4: Footer (Alamat, Medsos, Tautan Terkait, Akun, Copyright)  
- Gambar 5: Halaman Kebijakan Privasi & Perlindungan Data (`/kebijakan-privasi`)  

---

## 1. Executive Summary & Sasaran Bisnis

Sebelumnya, kita telah membangun CMS dasar untuk teks Hero, upload foto banner, dan upload PDF panduan.

**Namun, kebutuhan instansi sesungguhnya jauh lebih dinamis:**
Dinas Kominfo Sleman memerlukan kemampuan untuk memperbarui seluruh informasi publik di website **secara mandiri tanpa bantuan programmer dan tanpa hardcode di file Blade**. Apabila di masa mendatang terdapat pembaruan:
1. Tahapan alur seleksi berubah (misal seleksi administrasi dipercepat atau ada tes wawancara baru).
2. Teks di Peta Alur Interaktif diselaraskan dengan juknis terbaru.
3. Pertanyaan umum (FAQ) bertambah atau jawabannya disesuaikan dengan aturan baru.
4. Jam operasional kantor, nomor telepon helpdesk, atau tautan medsos berganti.
5. Poin-poin pasal pada halaman Kebijakan Privasi (UU PDP No. 27/2022) direvisi oleh bagian hukum.

Semua bagian ini **HARUS BISA DIUBAH OLEH SUPER ADMIN MELALUI CMS** dan **LANGSUNG TERSINKRONISASI KE HALAMAN PUBLIK SECARA REALTIME**.

---

## 2. Prinsip Arsitektur (Zero-Hardcode & Zero-Hallucination)

Agar implementasi rapi, stabil, dan mudah dipelihara oleh Junior Developer atau AI agent:

1. **Gunakan Tabel `pengaturan` yang Sudah Ada (No Destructive Migration):**
   - Kolom `kunci` (string) dan `nilai` (text) pada tabel `pengaturan` sangat ideal untuk menyimpan string teks maupun struktur JSON (`json_encode` / `json_decode`).
   - Gunakan method `Pengaturan::get()` dan `PengaturanModel::getJson()` yang sudah terintegrasi dengan Redis/File Cache bawaan Laravel (`Cache::rememberForever`).
2. **Graceful Fallback 100% Aman:**
   - Setiap pemanggilan data dinamis di Blade wajib memiliki nilai default bawaan (hardcoded fallback) sehingga jika database kosong atau key belum diset, tampilan publik **TIDAK PERNAH KOSONG ATAU ERROR 500**.
3. **Pemisahan Tab CMS yang Rapi di Super Admin:**
   - Hindari membuat form raksasa yang panjang. Gunakan navigasi tab (Hero & Media, Alur & Tahapan, FAQ, Footer & Kontak, Kebijakan Privasi) dengan tombol simpan per bagian.
4. **Eksekusi Bertahap (Satu per Satu):**
   - Kerjakan secara modular dari Task 1 hingga Task 5 agar setiap perubahan teruji dan dapat diverifikasi secara atomik.

---

## 3. Rencana Pembagian Task (Step-by-Step Task Breakdown)

```
[CMS EXPANSION RUNBOOK]
├── Task 1: Perluasan Support Helper Pengaturan (JSON Getter & Setter di App\Support\Pengaturan)
├── Task 2: CMS Alur & Tahapan Pendaftaran (Sinkronisasi Landing Page & Peta Alur)
├── Task 3: CMS FAQ (Kategori, Pertanyaan, & Jawaban Dinamis)
├── Task 4: CMS Footer & Kontak Kedinasan (Alamat, Telp, Email, Medsos, Tautan, Copyright)
├── Task 5: CMS Halaman Kebijakan Privasi & Perlindungan Data (/kebijakan-privasi)
├── Task 6: Tampilan Antarmuka CMS Tabbed Super Admin (/superadmin/landing)
└── Task 7: Automated Feature Testing & Quality Gate
```

---

## 4. Rincian Teknis Implementasi Per Task

### TASK 1 — Perluasan Helper `App\Support\Pengaturan`
Buka `app/Support/Pengaturan.php`. Tambahkan method:
```php
public static function json(string $kunci, array $default = []): array
{
    return PengaturanModel::getJson($kunci, $default);
}

public static function setJson(string $kunci, array $data): void
{
    PengaturanModel::setNilai($kunci, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}
```

---

### TASK 2 — CMS Alur & Tahapan Pendaftaran (Gambar 2)

**Key Database:**
- `landing_tahapan_heading`: Judul utama tahapan (Default: `"Bingung cara mendaftarnya?, berikut adalah Tahapan Pendaftaran Magang"`).
- `landing_tahapan_summary`: Array ringkasan teks 4 baris di sisi kiri (Pertama..., Kedua..., Ketiga..., Keempat...).
- `landing_tahapan_steps`: Array JSON 4 langkah alur:
  ```json
  [
    {"step": "01", "badge": "REGISTRASI", "title": "REGISTRASI AKUN", "desc": "Calon peserta membuat akun resmi, mengisi data pribadi, dan menunggu akun diverifikasi admin."},
    {"step": "02", "badge": "ADMINISTRASI & SELEKSI", "title": "ADMINISTRASI & SELEKSI", "desc": "Pilih bidang, unggah berkas, lalu admin memverifikasi dan menyeleksi dalam 1–3 hari kerja."},
    {"step": "03", "badge": "PELAKSANAAN MAGANG", "title": "PELAKSANAAN MAGANG", "desc": "Kerjakan tugas, isi logbook harian, dan unggah progres sesuai durasi magang."},
    {"step": "04", "badge": "KELULUSAN", "title": "KELULUSAN & SERTIFIKAT", "desc": "Unduh Lembar Nilai Magang dan sertifikat dalam bentuk PDF dari portal."}
  ]
  ```

**Integrasi View:**
1. `resources/views/public/landing.blade.php`:
   - Hubungkan bagian `#tahapan` untuk me-loop `$tahapanSteps` dan membaca `$tahapanHeading` serta `$tahapanSummary`.
2. `resources/views/public/peta-alur-interaktif.blade.php`:
   - Hubungkan label Pos Checkpoint (Pos 1 s/d Pos 4) dan teks balon ucapan agar mengambil dari `$tahapanSteps` dinamis.

---

### TASK 3 — CMS FAQ (Tanya Jawab) (Gambar 3)

**Key Database:**
- `landing_faq_heading`: Judul section FAQ (Default: `"Bingung? Berikut Beberapa Pertanyaan Yang Sering Ditanyakan"`).
- `landing_faq_data`: Array JSON kategori dan daftar pertanyaan-jawaban:
  ```json
  [
    {
      "id": "syarat",
      "nama": "Syarat dan Tata Cara Mendaftar",
      "items": [
        {"q": "Bagaimana alur dan tata cara mendaftar magang di Diskominfo Sleman?", "a": "..."},
        {"q": "Apakah siswa SMK dan mahasiswa perguruan tinggi memiliki syarat yang berbeda?", "a": "..."},
        {"q": "Apakah bisa mendaftar lebih dari satu bidang atau bulan magang?", "a": "..."}
      ]
    },
    {
      "id": "akun",
      "nama": "Akun",
      "items": [ ... ]
    },
    {
      "id": "kuis",
      "nama": "Kuis",
      "items": [ ... ]
    },
    {
      "id": "benefit",
      "nama": "Benefit Magang",
      "items": [ ... ]
    },
    {
      "id": "kewajiban",
      "nama": "Kewajiban Peserta Magang",
      "items": [ ... ]
    }
  ]
  ```

**Integrasi View:**
- Di `resources/views/public/landing.blade.php` baris 915–1020, gantikan variabel PHP hardcoded dengan:
  ```php
  $faqCategoriesData = \App\Support\Pengaturan::json('landing_faq_data', $defaultFaqData);
  $faqHeading = \App\Support\Pengaturan::get('landing_faq_heading', 'Bingung? Berikut Beberapa Pertanyaan Yang Sering Ditanyakan');
  ```

---

### TASK 4 — CMS Footer & Kontak Kedinasan (Gambar 4)

**Key Database:**
- `landing_footer_alamat`: Teks alamat kantor (Default: `"Jl Parasamya (Komplek Kantor Bupati Sleman) Beran, Tridadi, Sleman, Yogyakarta 55511"`).
- `landing_footer_telepon`: Nomor telepon kantor (Default: `"(0274) 868405"`).
- `landing_footer_email`: Alamat email resmi (Default: `"kominfo@slemankab.go.id"`).
- `landing_footer_jam_layanan`: Jam operasional dinas (Default: `"Senin sampai Kamis 07.30 sampai 15.30 WIB, Jumat 07.30 sampai 14.30 WIB"`).
- `landing_footer_medsos`: Array JSON media sosial (`[{"label": "kominfo_sleman", "url": "https://instagram.com/kominfo_sleman"}, ...]`).
- `landing_footer_tautan`: Array JSON tautan terkait (`[{"label": "Kebijakan Privasi", "url": "/kebijakan-privasi"}, {"label": "Situs Kominfo Sleman", "url": "https://kominfo.slemankab.go.id"}, ...]`).
- `landing_footer_copyright`: Teks hak cipta (Default: `"Dinas Komunikasi dan Informatika Kabupaten Sleman. Seluruh hak cipta dilindungi."`).

**Integrasi View:**
- Di `resources/views/public/landing.blade.php` bagian `footer`, ganti string statis dengan pembacaan dinamis dari `Pengaturan::get()` dan `Pengaturan::json()`.

---

### TASK 5 — CMS Halaman Kebijakan Privasi (Gambar 5)

**Key Database:**
- `kebijakan_privasi_badge`: Badge UU (Default: `"Undang-Undang PDP No. 27 Tahun 2022"`).
- `kebijakan_privasi_title`: Judul utama (Default: `"Kebijakan Privasi & Perlindungan Data"`).
- `kebijakan_privasi_updated_at`: Keterangan pembaruan (Default: `"27 Oktober 2026 • Dinas Komunikasi dan Informatika Kabupaten Sleman"`).
- `kebijakan_privasi_sections`: Array JSON pasal/bagian kebijakan:
  ```json
  [
    {
      "nomor": 1,
      "judul": "Komitmen Perlindungan Data Pribadi",
      "konten": "Dinas Komunikasi dan Informatika (Diskominfo) Kabupaten Sleman berkomitmen penuh..."
    },
    {
      "nomor": 2,
      "judul": "Data yang Kami Kumpulkan",
      "konten": "Untuk keperluan seleksi administrasi, penempatan bidang...",
      "items": [
        "Identitas Diri: Nama lengkap, NISN/NIM, NIK...",
        "Kontak: Alamat email aktif dan nomor WhatsApp...",
        "Afiliasi Akademik: Asal sekolah/kampus...",
        "Dokumen Persyaratan: Surat pengantar resmi..."
      ]
    },
    {
      "nomor": 3,
      "judul": "Keamanan Penyimpanan & Tautan Bertanda Tangan (Signed URLs)",
      "konten": "Seluruh berkas dokumen yang diunggah ke dalam sistem MAGKOM disimpan pada media..."
    },
    {
      "nomor": 4,
      "judul": "Hak Subjek Data Pribadi (UU PDP No. 27/2022)",
      "konten": "Peserta memiliki hak penuh atas data pribadinya sesuai amanat UU PDP..."
    },
    {
      "nomor": 5,
      "judul": "Narahubung & Hak Akses",
      "konten": "Untuk pertanyaan, permohonan koreksi data, atau hak penghapusan data..."
    }
  ]
  ```

**Integrasi View:**
- `resources/views/public/kebijakan-privasi.blade.php` membaca `$sections` dan data header dinamis dari `Pengaturan`.

---

### TASK 6 — Antarmuka Dashboard CMS Super Admin (`/superadmin/landing`)

Perbarui view `resources/views/superadmin/landing/index.blade.php` dengan komponen **Tab Navigasi Segmented**:
1. **Tab 1: Hero & Media Banner** (Teks hero, banner JPG/PNG, PDF juknis).
2. **Tab 2: Alur & Tahapan Magang** (Heading, 4 poin deskripsi, teks 4 kartu langkah).
3. **Tab 3: FAQ (Tanya Jawab)** (Heading, form input FAQ format JSON/textarea terstruktur).
4. **Tab 4: Footer & Kontak Kedinasan** (Alamat, telepon, email, jam layanan, list medsos & tautan).
5. **Tab 5: Kebijakan Privasi** (Badge UU, judul, tanggal update, teks pasal kebijakan).

Setiap tab memiliki tombol simpan tersendiri dengan parameter `section` (`hero`, `tahapan`, `faq`, `footer`, `kebijakan`) yang diproses secara modular di `LandingCmsController@update`.

---

## 5. Matriks Pengujian Fitur (Automated Test Suite)

Tambahkan test case pada `tests/Feature/SuperAdminModuleEnhancementTest.php`:
1. `test_superadmin_can_update_tahapan_pendaftaran_cms_and_see_it_on_landing_and_peta_alur()`
2. `test_superadmin_can_update_faq_data_and_verify_accordion_on_landing_page()`
3. `test_superadmin_can_update_footer_contact_and_links_dynamically()`
4. `test_superadmin_can_update_kebijakan_privasi_content_and_verify_public_page()`
5. `test_public_landing_and_kebijakan_privasi_fall_back_safely_when_database_is_empty()`

---

## 6. Acceptance Criteria (Kriteria Keberhasilan)

1. [ ] Seluruh teks dan isi alur pendaftaran, FAQ, footer, dan kebijakan privasi dapat diedit via CMS Super Admin.
2. [ ] Tidak ada satu pun teks pada 5 area di atas yang masih hardcoded mati di Blade view.
3. [ ] Peta Alur Interaktif (`/peta-alur`) otomatis tersinkronisasi dengan perubahan tahapan dari CMS.
4. [ ] Halaman Kebijakan Privasi (`/kebijakan-privasi`) merender data pasal dari CMS secara rapi.
5. [ ] Terdapat fallback default aman jika database belum memiliki nilai kustom (anti-crash 500).
6. [ ] Seluruh test PHPUnit lulus 100% (`php artisan test`).
7. [ ] Kompilasi aset Vite sukses (`npm run build`).
8. [ ] Gaya penulisan kode lulus Pint (`./vendor/bin/pint --test`).

