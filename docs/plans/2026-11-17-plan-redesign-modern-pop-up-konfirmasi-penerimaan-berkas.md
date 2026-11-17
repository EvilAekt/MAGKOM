# Rencana Kerja: Redesign Pop-Up Konfirmasi Penerimaan Berkas (Modern, Profesional & Bebas Gaya AI Generik)

| Informasi Dokumen | Nilai |
| :--- | :--- |
| **Tanggal Pembuatan** | 17 November 2026 |
| **Status Dokumen** | Draf Rencana (Menunggu Persetujuan Pengguna) |
| **Target Fitur** | Modal Konfirmasi Keputusan Pendaftaran Admin (`resources/views/admin/pendaftaran/show.blade.php`) |
| **Branch Target** | `test-2` |
| **Referensi Desain** | `docs/design/design-system.md` (DS-MagKom), `.agents/rules/AGENTS.md` |

---

## 1. Analisis Kritis & Masalah Tampilan Saat Ini

Berdasarkan tinjauan visual pada tangkapan layar pengguna (`media_1794920022085_ffde2e3e.png`), pop-up konfirmasi saat ini memiliki kelemahan estetika yang membuatnya tampak kaku, tidak profesional, dan memiliki pola tipikal komponen buatan AI generik (*prompting template artifacts*):

### A. Pola *Triple-Nested Box* (Kotak di Dalam Kotak di Dalam Kotak)
* **Kondisi Saat Ini**: 
  - Dialog putih modal membungkus kotak besar biru pastel muda (`.magkom-modal-callout`).
  - Di dalam kotak biru tersebut, terdapat kotak putih kecil bergaris (`.magkom-modal-target-box`).
  - Di bawah kotak putih itu, terdapat teks konsekuensi dengan karakter bullet kasar (`• Status pendaftaran...`).
* **Dampak Visual**: Antarmuka terlihat bertumpuk-tumpuk, sempit (*cramped*), tidak memiliki pernapasan visual (*negative space*), dan menyerupai card debug ketimbang modal resmi institusi pemerintah.

### B. Identitas Peserta Sangat Minim & Datar (*Flat Text*)
* **Kondisi Saat Ini**: Calon peserta hanya dituliskan dengan teks hitam biasa dan teks abu-abu pudar di bawahnya (`Aditya Pratama`, `#REG-00344 • Universitas Gadjah Mada`).
* **Ekspektasi Modern**: Informasi entitas user dalam sistem GovTech/Enterprise (seperti Linear, Stripe, Sleman DS-MagKom) seharusnya ditampilkan dalam bentuk **User Identity Strip** yang prestisius:
  - Avatar inisial dengan kontras warna elegan (*Royal Blue / Navy*).
  - Nama calon peserta tebal dan terbaca jelas.
  - Chip/Badge status registrasi berbasis font monospaced (*JetBrains Mono / Plus Jakarta Sans*).
  - Ikon institusi/kampus SVG yang tajam.

### C. Tipografi & Hirarki Pesan Konsekuensi yang Kaku
* **Kondisi Saat Ini**: Pertanyaan *"Apakah Anda yakin menerima berkas calon peserta ini?"* mengambang di atas target box, sementara dampak administratif diletakkan sebagai teks bullet point kecil berwarna biru yang kontrasnya rendah.
* **Ekspektasi Modern**: Konsekuensi keputusan (alokasi kuota & perubahan status) disajikan dalam bentuk **Notice Impact Banner** ramping bersudut halus dengan ikon shield/check SVG yang elegan tanpa bullet mentah.

### D. Header & Tombol Aksi Tidak Proporsional
* **Kondisi Saat Ini**: Icon checkmark berupa badge kotak pastel 42x42px dengan border squircle kaku. Tombol *Batal* terlihat terlalu redup dan kecil, sedangkan tombol *Ya, Terima Berkas* berupa kotak biru solid yang terlalu mendominasi tanpa micro-elevation yang pas.

---

## 2. Solusi Desain Baru: Modern Enterprise GovTech Modal

Rancangan baru dirancang mengikuti kaidah **Design System MagKom (DS-MagKom)**:

```
+-------------------------------------------------------------------+
|  [ ✓ ]  Konfirmasi Penerimaan Berkas                       [ ✕ ]  |
|         Verifikasi Dokumen Calon Peserta Magang                   |
|-------------------------------------------------------------------|
|                                                                   |
|   +-- [User Identity Card: Clean & Bordered] -----------------+   |
|   |  ( AP )  Aditya Pratama                   [#REG-00344]    |   |
|   |          Universitas Gadjah Mada • Ilmu Komputer          |   |
|   +-----------------------------------------------------------+   |
|                                                                   |
|   +-- [Action Notice Banner] ---------------------------------+   |
|   |  (i) Berkas valid. Status akan diperbarui menjadi Diterima |   |
|   |      dan 1 kuota bidang akan otomatis dialokasikan.       |   |
|   +-----------------------------------------------------------+   |
|                                                                   |
|-------------------------------------------------------------------|
|                                      [ Batal ]  [ Ya, Terima Berkas ] |
+-------------------------------------------------------------------+
```

### Detail Spesifikasi Komponen Baru:

1. **Header Bersih & Proporsional**:
   - Ikon aksi berupa lingkaran modern (*circular emblem*) dengan ring luar halus:
     - Terima: Soft Azure Tint (`#EFF6FF`) dengan ring aksen biru Kominfo (`rgba(1, 52, 124, 0.12)`) dan ikon checkmark biru wibawa (`#01347C`).
     - Tolak: Soft Crimson Tint (`#FEF2F2`) dengan ring merah (`rgba(220, 38, 38, 0.12)`) dan ikon peringatan merah (`#DC2626`).
   - Judul modal menggunakan tipografi tegas (`font-size: 1.15rem`, `font-weight: 700`, `color: #0F172A`).
   - Tombol tutup dialog (`×`) dengan transisi rotasi/opacity halus dan lingkaran hover state abu-abu netral.

2. **User Identity Strip (Kartu Peserta Modern)**:
   - Menghilangkan nesting callout biru luar.
   - Menampilkan profil peserta dalam satu kartu surface putih/neutral-50 dengan border halus (`border: 1px solid var(--neutral-200, #E2E8F0)`), radius 12px, dan padding ergonomis:
     - **Avatar Inisial**: Lingkaran 42px dengan inisial dua huruf (misal: "AP"), latar gradien halus navy-to-blue (`#01347C` / `#1E40AF`) dengan teks putih tebal.
     - **Nama Lengkap**: `font-size: 0.975rem`, `font-weight: 700`, warna gelap kontras tinggi.
     - **Nomor Registrasi**: Ditampilkan sebagai Pill/Chip Badge monospaced (`font-family: JetBrains Mono`, latar `#F1F5F9`, border `#CBD5E1`, warna `#334155`).
     - **Institusi/Sekolah**: Ikon gedung/akademik SVG kecil berdampingan dengan nama institusi pendidikan peserta.

3. **Notice Impact Banner (Konsekuensi Aksi Rapi & Elegan)**:
   - Kotak banner tipis di bawah profil peserta dengan border kiri lembut (`border-left: 3px solid #01347C` untuk terima, atau `#DC2626` untuk tolak).
   - Ikon info/shield SVG minimalis (18x18px) di sisi kiri teks konsekuensi.
   - Teks pesan konsekuensi yang jelas, humanis, dan mudah dipahami tanpa tanda bullet point kaku.

4. **Action Buttons (Footer Seimbang)**:
   - **Tombol Batal**: Tipe *ghost secondary button* dengan border halus (`border: 1px solid #CBD5E1`), warna teks netral gelap (`#334155`), border-radius 10px, padding 0 1.25rem, tinggi 42px, dan efek hover abu-abu terang.
   - **Tombol Konfirmasi**: Tipe *solid primary brand button* dengan warna resmi Sleman Kominfo (`#01347C`), subtle box-shadow bertingkat (`0 4px 12px rgba(1, 52, 124, 0.22)`), border-radius 10px, tinggi 42px, transisi mikro saat ditekan (*scale 0.98*).
   - Saat varian aksi tolak aktif: tombol berubah menjadi Crimson Red (`#DC2626`) dengan efek hover merah gelap (`#B91C1C`).

5. **Dukungan Dark Mode Bento Navy**:
   - Kompatibel penuh dengan tema gelap MAGKOM: latar belakang kanvas `#0E2548`, border `rgba(255, 255, 255, 0.08)`, teks putih kontras `#EAF0FA`, dan kartu identitas peserta bernuansa `#031B40`.

---

## 3. Rencana Perubahan Berkas (Scope of Changes)

Pekerjaan ini sepenuhnya mematuhi aturan `.agents/rules/AGENTS.md` (tidak menulis CSS/JS di Blade, CSS murni di `resources/css/`, JS murni di `resources/js/`):

### A. Lapisan Tampilan (Blade View)
* File: [`resources/views/admin/pendaftaran/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/show.blade.php) (baris 506-570)
  - Menyederhanakan struktur HTML modal konfirmasi:
    - Menghapus container pembungkus ganda `.magkom-modal-callout`.
    - Memasang struktur **Identity Card** peserta yang memuat elemen avatar inisial, badge nomor registrasi, dan nama institusi.
    - Memasang struktur **Impact Notice Banner** untuk teks konsekuensi.
  - Memastikan seluruh ID elemen DOM yang digunakan oleh JS tetap utuh (`confirmModalTitle`, `confirmModalSubtitle`, `confirmModalExecuteBtn`, `confirmModalCancelBtn`, `confirmModalCloseBtn`).

### B. Lapisan Gaya (CSS Components)
* File: [`resources/css/components/confirm-modal.css`](file:///d:/laragon/www/MAGKOM/resources/css/components/confirm-modal.css)
  - Merancang ulang class CSS modal:
    - `.magkom-modal-dialog`: radius 16px, padding ergonomis, bayangan elevasi modern (`box-shadow: 0 24px 48px -12px rgba(2, 39, 93, 0.18)`).
    - `.magkom-modal-identity-card`: kartu profil peserta dengan tata letak flexbox modern.
    - `.magkom-modal-avatar`: lingkaran avatar inisial dengan styling profesional.
    - `.magkom-modal-reg-badge`: pill chip untuk nomor registrasi.
    - `.magkom-modal-impact-banner`: kotak info dampak keputusan yang bersih.
    - Varian warna dinamis (`.variant-primary`, `.variant-danger`, `.variant-warning`, `.variant-success`).
    - Dark mode overrides menggunakan token master `--dm-*`.

### C. Lapisan Interaksi (JavaScript Frontend)
* File: [`resources/js/pages/admin/pendaftaran-show.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/admin/pendaftaran-show.js)
  - Menyesuaikan fungsi `openConfirmModal(action)` agar:
    - Mendukung penyesuaian teks dan icon pada banner konsekuensi baru.
    - Menghasilkan avatar inisial secara dinamis dari nama peserta yang sedang aktif.
    - Menjamin transisi buka/tutup modal tetap mulus dengan animasi CSS.

---

## 4. Rencana Pengujian & Quality Gate

1. **Verifikasi Visual & Responsivitas**:
   - Tampilan modal konfirmasi "Terima Berkas" di mode terang (Light Mode).
   - Tampilan modal konfirmasi "Tolak Berkas" di mode terang (Light Mode).
   - Uji tampilan pada Bento Navy Dark Mode (`[data-theme="dark"]`).
   - Uji tampilan pada layar mobile / viewport kecil (< 480px).
2. **Pengujian Fungsional**:
   - Klik tombol "Batal" dan tombol close (`×`): modal tertutup tanpa submit form.
   - Klik tombol "Ya, Terima Berkas": form keputusan tersubmit dengan action terima berkas.
   - Klik tombol "Tolak": modal terbuka dengan varian danger (merah), teks penolakan, dan submit berjalan sesuai alur tolak.
3. **Kompilasi Aset**:
   - Menjalankan `npm run build` untuk memverifikasi tidak ada error build pada Vite.
4. **Automated Test Suite**:
   - `tests/Feature/AdminPendaftaranPerbaikanFlowTest.php`
   - `tests/Feature/AdminVerifikasiKategoriDanUploadSuratTest.php`
   - `vendor/bin/pint --test`

---

## 5. Permintaan Persetujuan Pengguna

Apakah rencana kerja desain ulang pop-up konfirmasi penerimaan berkas di atas sudah sesuai dengan ekspektasi Anda? 
Jika disetujui, saya akan segera melanjutkan ke tahap implementasi kode pada file Blade, CSS, dan JavaScript terkait.

