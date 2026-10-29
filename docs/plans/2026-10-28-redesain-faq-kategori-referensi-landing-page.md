# Rencana Kerja: Redesain Section FAQ Landing Page Sesuai Referensi Gambar (Kategori Bertingkat & Tema MAGKOM)

Dokumen ini merinci rencana teknis untuk memperbarui tampilan section **FAQ (Pertanyaan yang Sering Ditanyakan)** pada Landing Page MAGKOM (`resources/views/public/landing.blade.php`), mengadopsi struktur visual 2-kolom dari referensi gambar dengan penyesuaian data resmi, sistem tipografi, dan palet warna tema MAGKOM.

---

## 1. Tujuan
1. Mentransformasi layout section FAQ landing page dari accordion sederhana satu kolom menjadi **Layout 2-Kolom Premium**:
   - **Kolom Kiri**: Ilustrasi grafis visual bertema FAQ (karakter peserta berseragam dengan balon tanya `?`, tipografi 3D tebal *"FAQ"*, dan siluet gedung kantor pemerintahan).
   - **Kolom Kanan**: Aksen garis atas, judul tegas *"**Bingung?** Berikut Beberapa Pertanyaan Yang Sering Ditanyakan"*, dan sistem **Accordion Kategori Bertingkat (Nested Categorized Accordion)**.
2. Mengelompokkan data FAQ resmi MAGKOM ke dalam 5 kategori terstruktur:
   - **Syarat dan Tata Cara Mendaftar**
   - **Akun & Verifikasi**
   - **Seleksi & Penugasan**
   - **Benefit Magang**
   - **Kewajiban Peserta Magang**
3. Mengadaptasikan skema warna dan tipografi secara harmonis dengan Design System MAGKOM:
   - Kategori Utama: Warna Navy Elegan (`--navy: #1b3860` & `--navy-dark: #122846`), border halus, dan teks putih kontras tinggi.
   - Accordion Pertanyaan Aktif: Warna Aksen Emas Sleman (`--gold: #f59e0b` / `--gold-bright: #fbbf24`), dengan panel jawaban putih bersih dan border terpadu.
   - Tipografi: Lexend (`--font-display`) untuk judul dan Source Sans 3 (`--font-sans`) untuk teks pertanyaan/jawaban.
   - Dukungan penuh untuk Mode Gelap (*Dark Mode*) melalui CSS token `[data-theme="dark"]`.
4. Menjaga standar aksesibilitas keyboard (`aria-expanded`, `aria-controls`, `role="region"`), bebas *layout shift*, dan responsif di smartphone hingga desktop.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
- **Asumsi 1 (Visual Ilustrasi)**: Ilustrasi kolom kiri akan dibangun menggunakan elemen SVG vector murni yang tajam, ringan (bebas dependensi gambar raster eksternal yang berat), dan otomatis beradaptasi dengan skala layar tanpa buram di Retina Display.
- **Asumsi 2 (Data Persistence)**: Data FAQ dikelola secara terstruktur di JavaScript / Blade partial, sehingga memudahkan penambahan atau perubahan pertanyaan di masa mendatang tanpa merusak struktur markup.
- **Asumsi 3 (Interaktivitas Kategori & Sub-pertanyaan)**: Mengklik salah satu kategori utama akan membuka daftar pertanyaan dalam kategori tersebut (secara default, kategori pertama atau kategori dengan pertanyaan terpopuler aktif pertama kali). Mengklik baris pertanyaan emas akan membuka jawaban secara akordeon mulus.

---

## 3. Pendekatan Teknis

### Perbandingan Alternatif Arsitektur

| Kriteria | Opsi A: Pure CSS & Vanilla JS Component (Rekomendasi) | Opsi B: Tabbed Switcher Standar | Opsi C: Modal Pop-up FAQ |
| :--- | :--- | :--- | :--- |
| **Deskripsi** | Accordion bertingkat 2-kolom interaktif dibangun dengan modul CSS terisolasi dan Vanilla JS di `landing-interactive.js`. | Tab navigasi horizontal yang berganti tampilan pertanyaan di bawahnya. | FAQ dipindahkan ke dalam dialog pop-up/modal terpisah. |
| **Kemiripan Referensi** | **100% Identik** dengan layout referensi gambar (Kategori vertikal merah-marun/navy + card pertanyaan emas aktif). | Rendah — membuang estetika vertikal yang diminta referensi. | Tidak sesuai — referensi berada langsung di halaman (*in-section*). |
| **Kecepatan & Performa** | **Maksimal** (0 network request tambahan, ringan, SSR friendly). | Tinggi. | Sedang. |
| **SEO & Crawlability** | **Sangat Baik** (konten FAQ langsung terindeks Google di halaman utama). | Baik. | Buruk (konten di dalam modal sering tidak ter-crawl optimal). |

**Keputusan Teknis Terpilih:**  
Memilih **Opsi A (Pure CSS & Vanilla JS Component)** yang secara presisi mereproduksi komposisi visual gambar referensi, diselaraskan dengan palet Navy & Emas MAGKOM.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/landing.blade.php` | **Modify** | Memperbarui markup `#faq` menjadi grid 2-kolom (ilustrasi visual FAQ di kiri dan container judul + kategori di kanan). |
| `public/css/landing-faq-redesign.css` | **Create** | Stylesheet modular untuk FAQ layout 2-kolom, komponen ilustrasi FAQ SVG, kategori vertikal navy, accordion pertanyaan emas, serta dark mode support. |
| `public/js/landing-interactive.js` | **Modify** | Memperbarui data model `faqCategories` (5 kategori data MAGKOM) dan controller interaksi kategori & sub-pertanyaan. |
| `tests/Feature/LandingPageFaqRedesignTest.php` | **Create** | Automated Feature Test untuk menguji struktur FAQ baru, 5 kategori, dan atribut aksesibilitas ARIA. |

---

## 5. Langkah Implementasi

### Fase 1: Desain Stylesheet & Komponen Visual FAQ
- [x] **Langkah 1.1**: Buat stylesheet [public/css/landing-faq-redesign.css](file:///d:/MAGKOM/MAGKOM/public/css/landing-faq-redesign.css):
  - Container `.faq-redesign-container` dengan CSS Grid 2-kolom (`minmax(320px, 1fr) minmax(400px, 1.4fr)`).
  - Kolom kiri: styling ilustrasi visual `.faq-illustration-card` dengan komposisi teks 3D `FAQ`, avatar karakter, dan bubble `?`.
  - Kolom kanan: bar aksen merah/emas `.faq-accent-bar`, judul dengan penekanan font tebal `.faq-hero-title`, dan kontainer kategori `.faq-category-stack`.
  - Item Kategori Navy: `.faq-category-btn` dengan latar gradient navy (`#1b3860` s.d. `#122846`), border pembatas, indikator panah/chevron, dan status aktif.
  - Sub-Pertanyaan Emas: `.faq-question-card` dengan latar belakang emas kontras (`#f59e0b` / `#fbbf24`), teks bold, ikon plus/minus, serta panel jawaban `.faq-answer-drawer` berlatar belakang bersih dengan animasi ekspansi halus.
  - Dark mode adaptif via `[data-theme="dark"]`.
  - Media query responsif: pada layar tablet & HP (<900px), grid beralih menjadi 1 kolom vertikal rapi.

### Fase 2: Struktur Data 5 Kategori & Logic Interaktif
- [x] **Langkah 2.1**: Perbarui modul data FAQ di [public/js/landing-interactive.js](file:///d:/MAGKOM/MAGKOM/public/js/landing-interactive.js):
  - Struktur data baru `faqCategories`:
    1. **Syarat dan Tata Cara Mendaftar** (Surat pengantar, kuota formasi, pemilihan bidang).
    2. **Akun & Verifikasi** (Aktivasi email, kelengkapan biodata, kontak darurat).
    3. **Seleksi & Penugasan** (Timeline verifikasi admin, status waiting list, mekanisme seleksi).
    4. **Benefit Magang** (E-Sertifikat resmi QR-code, bimbingan mentor ASN, pengalaman nyata Pemda).
    5. **Kewajiban Peserta Magang** (Jam kerja ASN luring, tata tertib kantor, pengisian logbook mingguan).
  - Fungsi `renderCategorizedFaq()`:
    - Render tombol 5 kategori.
    - Toggle kategori aktif saat diklik dengan transisi mulus.
    - Render sub-pertanyaan accordion di dalam kategori yang sedang aktif.
    - Aksesibilitas ARIA: `aria-expanded="true/false"`, `role="tab"`, `role="tabpanel"`.

### Fase 3: Integrasi ke Template Landing Page
- [x] **Langkah 3.1**: Modifikasi section `#faq` di [resources/views/public/landing.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/landing.blade.php):
  - Muat stylesheet `landing-faq-redesign.css` di tag `<head>`.
  - Ganti markup kontainer FAQ lama dengan struktur baru.

### Fase 4: Pengujian & Validasi
- [x] **Langkah 4.1**: Buat automated feature test [tests/Feature/LandingPageFaqRedesignTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/LandingPageFaqRedesignTest.php):
  - Memverifikasi halaman merespons HTTP 200.
  - Memverifikasi keberadaan stylesheet `landing-faq-redesign.css`.
  - Memverifikasi teks judul *"Bingung? Berikut Beberapa Pertanyaan Yang Sering Ditanyakan"*.
  - Memverifikasi keberadaan 5 kategori utama data FAQ.
  - Memverifikasi atribut ARIA pada kategori dan accordion pertanyaan.
- [x] **Langkah 4.2**: Eksekusi pengujian dengan perintah `php artisan test --filter=LandingPageFaqRedesignTest`.
- [x] **Langkah 4.3**: Verifikasi visual di browser lokal (`http://127.0.0.1:8000/#faq`).

---

## 6. Strategi Testing

1. **Automated Testing (PHPUnit / Pest)**:
   - Memastikan view landing page ter-render sempurna tanpa error kompilasi Blade.
   - Assert kata kunci 5 kategori ("Syarat dan Tata Cara Mendaftar", "Akun & Verifikasi", "Seleksi & Penugasan", "Benefit Magang", "Kewajiban Peserta Magang").
2. **Interactive & Usability Testing**:
   - Klik kategori 1 s.d. 5: Kategori aktif berubah dan menampilkan daftar pertanyaan yang relevan.
   - Klik kartu pertanyaan emas: Panel jawaban membuka dan menutup (*accordion collapse*).
   - Keyboard Navigation: Tombol kategori dan pertanyaan dapat diakses dengan tombol `Tab` dan dibuka dengan `Enter` / `Space`.
   - Responsivitas Layar: Tampilan rapi di layar lebar (desktop split 2-kolom) dan menyatu di layar kecil (mobile stack).

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Ilustrasi berat atau pecah di layar resolusi tinggi** | Tampilan kolom kiri buram atau memperlambat load page | Gunakan format SVG vector inline/murni yang dioptimasi dengan CSS, sehingga ukuran file sangat kecil (<8KB) dan selalu tajam di resolusi apa pun. |
| **Kontras teks pada kartu pertanyaan emas** | Keterbacaan teks berkurang bagi pengguna tertentu | Gunakan warna teks kontras tinggi (Navy pekat `#0f172a` atau `#1b3860` pada latar emas `#fbbf24`), memenuhi standar kontras WCAG AA (rasio kontras > 4.5:1). |
| **Perbedaan tinggi kolom kiri dan kanan di desktop** | Kolom kiri terlihat kosong jika konten pertanyaan panjang | Terapkan `align-self: center` atau `position: sticky` dengan offset yang proporsional sehingga ilustrasi selalu berada di tengah sejajar pandangan. |

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Dokumen rencana disetujui secara resmi oleh pengguna.
- [x] Stylesheet `public/css/landing-faq-redesign.css` selesai dibuat dengan token tema MAGKOM.
- [x] Kolom kiri menampilkan ilustrasi visual karakter dan tipografi FAQ 3D yang harmonis.
- [x] Kolom kanan menampilkan judul *"Bingung? Berikut Beberapa Pertanyaan Yang Sering Ditanyakan"* dengan aksen bar di atasnya.
- [x] 5 Kategori utama vertikal (Navy) terpasang dan dapat diklik untuk berganti konten.
- [x] Sub-pertanyaan emas (Gold) terintegrasi dan berfungsi sebagai accordion pembuka jawaban.
- [x] Seluruh test pada `LandingPageFaqRedesignTest` berhasil lolos 100% (*passed*).
- [x] Mode gelap (*dark mode*) teruji kontras dan tampil harmonis.
