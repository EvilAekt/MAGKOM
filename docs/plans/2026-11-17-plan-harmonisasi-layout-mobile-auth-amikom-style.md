# Plan: Harmonisasi Grid & Layout Mobile Halaman Login dan Register (Sesuai Referensi Amikom)

Dokumen rencana kerja ini disusun berdasarkan panduan `web-planning` dan aturan repositori `AGENTS.md` untuk menyelaraskan tampilan *mobile* halaman Login dan Register MAGKOM dengan referensi antarmuka Universitas Amikom Yogyakarta (Gambar 1 & 2 referensi user), dengan tetap mempertahankan seluruh konten dan fungsionalitas MAGKOM.

---

## 1. Tujuan

Mengubah tata letak dan struktur visual tampilan *mobile* (`@media (max-width: 768px)`) pada halaman **Login** (`/login`) dan **Register** (`/register`) agar:
1. **Layout Kartu Mengambang Terpusat (*Centered Floating Card Frame*)**:
   - Menghilangkan model *full-bleed bottom sheet* (lembaran putih yang menempel di ujung bawah layar).
   - Menggantinya dengan kartu tunggal yang terpusat rapi di tengah layar ponsel (`max-width: 440px`, sudut membulat `border-radius: 20px`), di atas latar belakang kanvas node/konstelasi biru gelap MAGKOM (persis seperti kartu login Amikom di Gambar 2).
2. **Banner Header Visual di Bagian Atas Kartu (*Top Visual Header Banner*)**:
   - Bagian atas kartu menampilkan banner visual ringkas (tinggi ~140px - 150px) dengan logo Sleman & MAGKOM yang terpusat secara elegan, berlatar belakang gradien Sleman dan grafis orbital radar lembut.
3. **Badan Form Putih Bersih & Terpusat (*Centered White Card Body*)**:
   - Judul form ("Selamat datang" pada Login dan "Register Akun" pada Register) diselaraskan menjadi rata tengah (*centered*), menggantikan format rata kiri sebelumnya.
   - Subjudul teks petunjuk diposisikan rata tengah dengan keterbacaan optimal.
   - Kolom isian (*input fields*), tombol submit utama (*Masuk / Daftar*), garis pemisah (*divider* "atau"), dan tombol Google Sign-In mempertahankan gaya kartu modern dengan sudut melengkung 12px.
4. **Scrollabilitas Khusus Register di Mobile**:
   - Form pendaftaran yang memiliki beberapa isian diatur agar memiliki kontainer formulir yang dapat di-*scroll* secara halus di dalam kartu (`overflow-y: auto`), sehingga seluruh kolom (tipe pendaftaran, bidang, jurusan, data diri, captcha) dapat diisi dengan nyaman di layar ponsel pendek.

---

## 2. Analisis Perbandingan: Eksisting vs Referensi Amikom

| Aspek Visual | Tampilan MAGKOM Sekarang (Gambar 4) | Referensi Amikom Mobile (Gambar 2) | Rencana Implementasi MAGKOM Baru |
| :--- | :--- | :--- | :--- |
| **Bentuk Kontainer** | *Full-bleed bottom sheet* menempel ke sisi kiri, kanan, dan bawah layar (`border-radius: 28px 28px 0 0`). | Kartu utuh melayang di tengah layar dengan margin sisi, memperlihatkan latar belakang gelap konstelasi di sekelilingnya. | **Kartu Mengambang Terpusat**: `width: calc(100% - 28px); max-width: 430px; margin: 18px auto; border-radius: 20px; overflow: hidden; box-shadow: 0 16px 40px rgba(0, 15, 40, 0.5)`. |
| **Header Atas** | Header gelap memanjang penuh di atas dengan teks tagline slide dan grafis radar di pojok kanan. | Banner foto gedung kampus dengan logo universitas terpusat di atas kartu. | **Banner Visual Terpadu**: Banner biru tua proporsional (tinggi ~145px) dengan logo Pemkab Sleman & MAGKOM yang terpusat elegan dengan latar orbital radar. |
| **Perataan Teks Form** | Teks judul "Selamat datang" dan deskripsi rata kiri (`text-align: left !important`). | Teks salam "Hai, Selamat Datang di..." terpusat rapi di tengah kartu (`text-align: center`). | **Rata Tengah (*Centered*)**: Judul dan subjudul form rata tengah dengan tipografi modern dan jarak proporsional. |
| **Transisi & Batas Elemen** | Sambungan antara header atas dan bottom sheet menggunakan overlap minus (`margin-top: -18px`). | Header banner dan bodi kartu putih menyatu dalam 1 kontainer kartu dengan radius melengkung atas & bawah. | **Menyatu Mulus**: Banner dan form dibungkus dalam `<main>` kartu terpadu tanpa patahan visual. |
| **Pengalaman Register** | Panjang formulir menekan layout dan rentan terpotong di layar kecil. | Kontainer kartu proporsional dengan kemampuan scroll internal. | **Internal Smooth Scroll**: Header kartu tetap terpasang, bagian form dapat digulir mandiri secara mulus di layar ponsel. |

---

## 3. Pendekatan Teknis & Evaluasi Alternatif

### Perbandingan Opsi:

| Kriteria | Opsi A: Pure CSS Architecture (Direkomendasikan) | Opsi B: Rombak Total Markup Blade |
| :--- | :--- | :--- |
| **Deskripsi** | Menata ulang tata letak dan grid mobile sepenuhnya melalui stylesheet `resources/css/pages/auth/auth-depth.css` pada media query `@media (max-width: 768px)`. | Mengubah markup HTML Blade di `auth-depth.blade.php`, `login.blade.php`, dan `register.blade.php` dengan menambahkan elemen container baru. |
| **Kelebihan** | - Mematuhi aturan ketat repositori **`AGENTS.md` (Rule 3: Blade Read-Only secara default)**.<br>- Tampilan desktop (Gambar 3) tetap 100% terjaga dan stabil tanpa risiko regresi.<br>- Perubahan terarah, bersih, dan cepat diverifikasi. | - Kebebasan merestrukturisasi tag HTML mentah. |
| **Kekurangan** | - Memerlukan penyesuaian selektor CSS yang presisi (`main`, `section:has(.slides)`, `.auth-right-container`). | - Berisiko merusak fungsionalitas Blade yang sudah ada (validasi error, captcha, redirect kuota, Alpine/JS bindings).<br>- Melanggar konvensi repositori. |
| **Rekomendasi** | **Dipilih (Opsi A)** karena memberikan hasil visual identik dengan referensi tanpa menyentuh Blade dan tanpa risiko *breaking changes*. | Ditolak karena melanggar aturan integritas Blade. |

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/auth/auth-depth.css` | `Modify` | - Restrukturisasi media query `@media (max-width: 768px)` dan `@media (max-width: 480px)`.<br>- Ubah kontainer mobile menjadi kartu melayang terpusat (`max-width: 430px`, margin sisi, `border-radius: 20px`).<br>- Rampingkan banner atas kartu menjadi header visual terpadu (tinggi ~145px) dengan logo Sleman & MAGKOM terpusat.<br>- Pusatkan judul dan deskripsi form ("Selamat datang" / "Register Akun") ke posisi `text-align: center`.<br>- Tambahkan *scroll container* halus untuk halaman register di mobile. |
| `resources/js/pages/auth/auth-depth.js` | `Review` | Memastikan animasi kanvas partikel node konstelasi tetap aktif mengalir mulus di latar belakang luar kartu. |

---

## 5. Langkah Implementasi Terperinci

### Tahap 1: Restrukturisasi Kontainer Utama Mobile (Floating Card)
- [ ] Buka `resources/css/pages/auth/auth-depth.css` pada blok `@media (max-width: 768px)`.
- [ ] Atur pembungkus terluar `div:has(> canvas[data-auth-canvas])`:
  - `padding: 16px 14px !important;`
  - `display: flex !important; align-items: center !important; justify-content: center !important; min-height: 100vh !important; min-height: 100dvh !important;`
  - Pastikan kanvas partikel konstelasi tetap terlihat di area margin luar kartu.
- [ ] Atur elemen `<main>`:
  - `width: 100% !important; max-width: 430px !important; margin: 0 auto !important;`
  - `border-radius: 20px !important; overflow: hidden !important;`
  - `background: #FFFFFF !important;`
  - `box-shadow: 0 20px 50px rgba(0, 15, 40, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.12) !important;`
  - `display: flex !important; flex-direction: column !important;`
  - `height: auto !important; min-height: auto !important; max-height: calc(100dvh - 32px) !important;`

### Tahap 2: Penataan Banner Visual Atas (Header Card Amikom-Style)
- [ ] Pada selector `section:has(.slides)` (panel visual atas):
  - Atur tinggi menjadi kompak: `height: 145px !important; min-height: 145px !important; max-height: 145px !important;`
  - Atur padding: `16px 20px !important;`
  - Latar belakang: `background: radial-gradient(circle at 50% 30%, #01347C 0%, #003566 60%, #01183A 100%) !important;`
  - Posisikan logo Pemkab Sleman dan teks "MAGKOM DINAS KOMINFO KAB. SLEMAN" terpusat di tengah (`align-items: center; justify-content: center; text-align: center;`).
  - Sembunyikan teks slider yang terlalu panjang di mobile agar banner bersih dan elegan seperti foto kampus Amikom.
  - Posisikan grafis orbital radar lembut sebagai latar belakang dekoratif di belakang logo.

### Tahap 3: Penataan Form Body & Tipografi Terpusat
- [ ] Pada panel form login dan register:
  - Hilangkan efek *bottom-sheet* (`border-radius: 0 !important; margin-top: 0 !important; box-shadow: none !important;`).
  - Padding dalam: `24px 22px 24px !important;`
  - Judul form: `text-align: center !important; font-size: 22px !important; font-weight: 800 !important; margin-bottom: 6px !important; color: #003566 !important;`
  - Deskripsi form: `text-align: center !important; font-size: 13.5px !important; margin: 0 auto 20px auto !important; max-width: 320px !important; color: #5F6B76 !important;`
- [ ] Penyesuaian form register:
  - Berikan `overflow-y: auto !important; max-height: calc(100dvh - 195px) !important;` dengan scrollbar tipis modern agar form panjang mudah digulir.
- [ ] Penataan divider dan tombol sosial:
  - Pertahankan divider garis lembut dengan teks "atau" di tengah.
  - Tombol "Masuk dengan Google" dibuat rapi dengan radius 12px dan shadow lembut.

### Tahap 4: Quality Gate & Pengujian Responsif
- [ ] Jalankan kompilasi Vite: `npm run build`.
- [ ] Jalankan linter dan formatter: `vendor/bin/pint --test`.
- [ ] Jalankan automated test suite autentikasi: `artisan test --filter=Auth`.
- [ ] Verifikasi tampilan mobile di berbagai ukuran viewport (360px, 375px, 390px, 412px) untuk memastikan layout kartu melayang terpusat tampil presisi tanpa overflow horizontal.

---

## 6. Kriteria Selesai (Definition of Done)

- [ ] Pada tampilan mobile, halaman Login dan Register tampil dalam format kartu terpusat melayang (*floating card*) dengan sudut melengkung 20px dan bayangan dalam.
- [ ] Latar belakang kanvas konstelasi/node gelap MAGKOM terlihat membingkai sekeliling kartu.
- [ ] Bagian atas kartu memiliki banner visual rapi (~145px) dengan logo Sleman & MAGKOM terpusat.
- [ ] Judul dan deskripsi formulir tampil rata tengah (*centered*) selaras dengan referensi Amikom.
- [ ] Seluruh isian form, toggle tipe pendaftaran, captcha, dan tombol submit berfungsi 100% normal tanpa perubahan fungsionalitas.
- [ ] Pada halaman register, formulir dapat di-scroll dengan lancar di dalam kartu pada layar ponsel beresolusi pendek.
- [ ] Tampilan desktop tetap utuh 100% tanpa perubahan.
- [ ] `npm run build` dan `vendor/bin/pint --test` lulus sukses.

