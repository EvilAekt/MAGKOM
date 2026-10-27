# Rencana Kerja: Perombakan Tampilan Dashboard Peserta (Soft, Simple, & Elegan ala Landing Page)

**Tanggal:** 27 Oktober 2026  
**Status:** Menunggu Persetujuan Pengguna (*Pending Approval*)  
**Konteks & Masalah:** Pengguna mengidentifikasi bahwa tampilan Dashboard Peserta saat ini masih terasa "terlalu AI" (terlalu banyak ornamen garis SVG abstrak, gradien sci-fi gelap pekat, drop shadow tebal, dan warna neon kontras tinggi). Pengguna meminta tampilan dirombak agar terasa **soft, simple, dan elegan** selaras dengan estetika Landing Page baru (warna Sleman Navy `#1b3860`, aksen emas `#f59e0b`/`#fbbf24`, latar belakang Slate-50 `#f8fafc`, kartu putih bersih `#ffffff`, border tipis `#e2e8f0`, dan diffuse shadow alami).

---

## 1. Analisis Desain: Menghilangkan Kesan "AI Banget" Menuju "Human-Crafted & Elegan"

| Elemen Tampilan | Kondisi Saat Ini ("AI Banget") | Solusi Desain Baru ("Soft, Simple, Elegan ala Landing Page") |
| :--- | :--- | :--- |
| **Hero Banner** (`.dashboard-hero`) | Latar belakang SVG garis gelombang putih acak (`data:image/svg...`), gradien hitam-navy-electric blue berlapis (`#0c1b2e` -> `#1b3860` -> `#0751FF`), drop shadow hitam pekat `rgba(12, 27, 46, 0.45)`. | Latar belakang Sleman Navy solid tenang (`#1b3860` ke `#162e4f`), **menghapus seluruh SVG wave garis-garis alien**, sudut lengkung halus `16px`, soft subtle border, serta aksen badge emas bundar resmi Pemkab Sleman. |
| **Palet Warna Utama** | Kontras tinggi biru neon `#0751FF` dan navy pekat gulita `#0c1b2e`. | Harmonisasi warna landing page: Sleman Navy `#1b3860`, Royal Blue lembut `#2563eb`, Gold badge `#f59e0b`/`#fbbf24`, Neutral surface `#ffffff`, dan Background dashboard `#f8fafc`. |
| **Shadow & Kedalaman** | Shadow gelap pekat tebal (`box-shadow: 0 10px 28px -4px ...`). | *Soft diffuse shadows*: `box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 6px 16px -2px rgba(15, 23, 42, 0.04)`. Terasa ringan, datar, dan modern. |
| **Kartu Metrik & Panel** | Kotak-kotak kaku, warna-warni progress bar neon menusuk mata. | Permukaan putih bersih (`#ffffff`), border halus `1px solid #e2e8f0`, ikon metrik di dalam bulatan warna pastel lembut (*soft tints*), tipografi angka `Lexend` yang seimbang dan tidak berlebihan. |
| **Tombol & Aksi Cepat** | Shadow tebal biru berpijar, sudut bervariasi. | Tombol primer Sleman Blue `#1b3860` atau `#2563eb` dengan teks putih bersih, tombol sekunder berlatar putih dengan border `#e2e8f0` dan teks `#334155`. Border radius proporsional `10px`. |
| **Whitespace & Hirarki** | Terkesan padat, elemen berdesakan (*visual clutter*). | Menambah *breathing room* (padding yang lapang, jarak antar baris rapi, hirarki teks jelas: judul Slate-900 `#0f172a`, teks pendukung Slate-500 `#64748b`). |

---

## 2. Rincian Modifikasi File & Arsitektur

### A. Fondasi Desain & Token Global (`resources/views/layouts/peserta.blade.php`)
1. **Token Warna**:
   - Menyelaraskan warna root token CSS dengan landing page:
     ```css
     --navy: #1b3860;
     --navy-dark: #122846;
     --blue-primary: #2563eb;
     --gold-sleman: #f59e0b;
     --bg-page: #f8fafc;
     --surface-card: #ffffff;
     --border-subtle: #e2e8f0;
     --text-primary: #0f172a;
     --text-secondary: #475569;
     --text-muted: #64748b;
     --shadow-soft: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 12px -2px rgba(15, 23, 42, 0.03);
     ```
2. **Top Bar & Sidebar**:
   - Sidebar navigasi tetap fungsional namun border dan highlight status aktif dibuat lebih halus dan tenang, senada dengan header landing page.
   - Topbar dibuat bersih dengan latar putih/transparan lembut dan bayangan tipis.

### B. Perombakan Scoped Style Dashboard (`resources/views/peserta/dashboard.blade.php`)
1. **`.dashboard-hero`**:
   - Hapus properti `background: url("data:image/svg+xml...")`.
   - Ubah latar ke:
     ```css
     background: linear-gradient(135deg, #1b3860 0%, #152d4e 100%);
     border-radius: 16px;
     box-shadow: 0 4px 20px -2px rgba(27, 56, 96, 0.12);
     border: 1px solid rgba(255, 255, 255, 0.12);
     ```
   - Berikan sentuhan badge emas elegan di sudut sapaan untuk menegaskan status kedinasan tanpa terlihat ramai.
2. **`.stepper-list` & `.step-item` (Fase Onboarding)**:
   - Desain ulang step tracker menjadi kartu bernuansa minimalis dengan nomor bulat yang soft (lingkaran abu/biru pastel).
3. **`.info-strip` & `.item-row` (Pendaftaran & Tugas)**:
   - Hilangkan border kasar, ganti dengan baris minimalis modern dengan divider tipis `#f1f5f9`.
   - Badge status deadline dibuat pill pastel (`#f1f5f9` untuk normal, `#fef2f2` untuk urgent).

### C. Perombakan Komponen Partials Dashboard
1. **`resources/views/peserta/dashboard/partials/_header.blade.php`**:
   - Tata letak sapaan selamat datang dibuat lebih bersahabat dan bersih.
   - Tombol aksi utama di header (Logbook, Tugas, SK) dibuat rapi dengan ukuran proporsional.
2. **`resources/views/peserta/dashboard/partials/_aside.blade.php`**:
   - Kartu profil Pembimbing Lapangan dibuat dengan layout kartu minimalis: avatar inisial dengan latar navy lembut, teks nama pejabat/pembimbing jelas, informasi email dan ruangan dalam wadah pastel sejuk `#f8fafc`.
   - Tombol "Aksi Cepat Peserta" menggunakan grid kartu ikon modern yang bersih.
   - Ketentuan logbook (BR-09) dibalut dalam card peringatan lembut warna amber pastel `#fffbeb` dengan border `#fef3c7`.
3. **`resources/views/peserta/dashboard/partials/_phase_aktif.blade.php`**:
   - 4 Kartu KPI (Tugas, Logbook, Sisa Hari, Status Evaluasi):
     - Background putih bersih `#ffffff`.
     - Ikon dibungkus rounded box warna pastel (soft blue, soft green, soft amber, soft indigo).
     - Angka metrik menggunakan font `Lexend` dengan ukuran yang pas dan rapi.
     - Progress bar mikro diperlembut warnanya (`#3b82f6`, `#10b981`, `#f59e0b`).
4. **`resources/views/peserta/dashboard/partials/_phase_onboarding.blade.php` & `_phase_seleksi.blade.php`**:
   - Kartu panduan alur dibuat sederhana, informatif, dan tidak mengintimidasi pengguna baru.

### D. Penerapan Konsistensi ke Seluruh Tampilan Peserta
- Seluruh sub-halaman peserta (Profil, Form Pendaftaran, Logbook Harian, Daftar Tugas, Lembar Nilai, dan Pengaturan) secara otomatis mewarisi token CSS global dari `layouts/peserta.blade.php` sehingga seluruh antarmuka terasa satu kesatuan (*cohesive*) dengan landing page.

---

## 3. Rencana Langkah Kerja (Execution Steps)

1. **Langkah 1: Perbarui Design Tokens di `layouts/peserta.blade.php`**
   - Masukkan token warna landing page (`--navy: #1b3860`, `--blue-primary: #2563eb`, dll.).
   - Haluskan style default Gentelella card, panel, dan sidebar.

2. **Langkah 2: Rombak Total Scoped CSS di `peserta/dashboard.blade.php`**
   - Hapus pola wave SVG AI.
   - Ubah warna hero banner, kartu KPI, timeline, stepper, dan quick actions menjadi soft & simple.

3. **Langkah 3: Perbarui Markup & Class di Komponen Partials**
   - Sesuaikan `_header.blade.php`, `_aside.blade.php`, `_phase_aktif.blade.php`, `_phase_onboarding.blade.php`, dan `_phase_seleksi.blade.php`.

4. **Langkah 4: Validasi & Uji Visual**
   - Kompilasi seluruh template Blade (`php artisan view:cache` & `php artisan view:clear`).
   - Inspeksi hasil render visual menggunakan browser untuk memastikan kesan kaku "AI banget" sudah 100% hilang dan digantikan oleh nuansa soft, bersih, dan elegan.

---

## 4. Konvensi & Standar Keamanan
- Penjelasan dan laporan ke pengguna disampaikan dalam **Bahasa Indonesia**.
- Kode sumber, CSS, dan penamaan variabel tetap dalam **Bahasa Inggris**.
- Tidak ada modifikasi file di luar lingkup direktori `d:\MAGKOM\MAGKOM`.
