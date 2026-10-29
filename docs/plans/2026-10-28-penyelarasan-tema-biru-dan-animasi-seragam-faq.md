# Rencana Kerja: Penyelarasan Tema Biru MAGKOM & Animasi Tangan Seragam Kominfo pada Section FAQ

Dokumen ini merinci rencana teknis untuk memperbarui **ilustrasi karakter visual** dan **palet warna informasi** pada section FAQ Landing Page MAGKOM (`resources/views/public/landing.blade.php` & `public/css/landing-faq-redesign.css`). Pembaruan ini bertujuan mengganti warna merah marun referensi lama menjadi **Palet Biru Resmi MAGKOM/Diskominfo**, mendesain ulang karakter dengan seragam dinas Kominfo bertema MagKom, serta menambahkan animasi mikro (*micro-interaction*) pada tangan karakter yang menunjuk.

---

## 1. Tujuan
1. **Redesain Ilustrasi Karakter (Kolom Kiri)**:
   - Mengubah seragam karakter menjadi **Seragam Resmi Kominfo bertema MAGKOM**: kemeja dinas biru navy Diskominfo (`#1b3860` / `#1e40af`) dengan kerah beraksen, badge identitas MagKom di dada, celana formal rapi, dan lanyard ID Card Pemkab Sleman.
   - Mengubah teks 3D besar **FAQ** menjadi **Navy-Blue 3D Extrusion** yang serasi dengan tema portal.
   - Menambahkan animasi CSS keyframe halus pada tangan karakter yang menunjuk (*subtle waving / pointing gesture*) agar ilustrasi terasa hidup, ramah, dan interaktif tanpa mengganggu performa rendering.
2. **Harmonisasi Warna Informasi (Kolom Kanan)**:
   - Mengganti warna merah marun (`#882426`) pada tombol kategori menjadi **Gradient Navy MagKom** (`#1b3860` s.d. `#122846`), senada dengan sticky header dan identitas visual Diskominfo Sleman.
   - Menyelaraskan bar aksen atas (`.faq-accent-bar`) menjadi gradasi biru elektrik (`#0751ff`) ke biru navy.
   - Menyesuaikan hover effect pada kategori menjadi biru berbobot (`#2563eb`) dengan indikator chevron putih bersih.
   - Menjaga kartu sub-pertanyaan accordion dengan aksen Emas Sleman (`--gold: #f59e0b`) yang kontras tinggi dan mudah dibaca.
3. **Standar Kualitas & Kinerja**:
   - Zero layout shift (*CLS = 0*).
   - Animasi berbasis CSS `transform: rotate(...)` yang dioptimalkan GPU (*hardware-accelerated* via `will-change: transform`).
   - Dukungan penuh untuk mode terang (*light mode*) dan mode gelap (*dark mode*).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
- **Asumsi 1 (Animasi Ramah Aksesibilitas)**: Animasi gerakan tangan menggunakan pergerakan lembut (rotasi $±4^\circ$ berulang setiap 2.8 detik) serta menghormati preferensi pengguna via media query `@media (prefers-reduced-motion: reduce)`.
- **Asumsi 2 (Palet Warna)**: Seluruh kode warna diselaraskan dengan variabel warna tema yang sudah didefinisikan di `public/css/landing-modern.css` (`--navy: #1b3860`, `--navy-dark: #122846`, `--accent: #4f46e5`, `--gold: #f59e0b`).
- **Asumsi 3 (Struktur Konten)**: 5 Kategori data yang telah dibuat (*Syarat dan Tata Cara Mendaftar*, *Akun*, *Kuis*, *Benefit Magang*, *Kewajiban Peserta Magang*) tetap dipertahankan karena struktur informasinya sudah lengkap dan tepat sasaran.

---

## 3. Pendekatan Teknis

### Perbandingan Alternatif Implementasi

| Kriteria | Opsi A: CSS Hardware-Accelerated Animation & SVG Tokens (Rekomendasi) | Opsi B: Lottie / GIF Image Eksternal |
| :--- | :--- | :--- |
| **Metode Animasi** | Keyframe CSS pada elemen `<g id="charPointingArm">` di dalam SVG inline. | File animasi JSON Lottie atau file gambar `.gif`. |
| **Ukuran Berkas** | **~0 KB tambahan** (hanya beberapa baris CSS keyframes). | Menambah 150–500 KB aset unduhan tambahan. |
| **Ketajaman & Skalabilitas** | **Selalu tajam (Vektor murni)** di resolusi 4K / Retina. | Potensi buram atau resolusi tidak seimbang. |
| **Kontrol Warna Tema** | Dapat diselaraskan langsung dengan CSS variable `var(--navy)` dan tema gelap. | Warna terkunci di dalam file animasi, sulit beradaptasi dengan mode gelap. |

**Keputusan Teknis Terpilih:**  
Memilih **Opsi A** karena memberikan performa tercepat, ketajaman grafis maksimal di semua layar, dan kemudahan kustomisasi warna secara menyeluruh.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/landing.blade.php` | **Modify** | Memperbarui warna seragam karakter SVG menjadi kemeja dinas biru Kominfo, menambahkan ID badge MAGKOM, mengubah teks 3D FAQ menjadi gradien Navy, dan memasang class/id animasi pada lengan/tangan yang menunjuk. |
| `public/css/landing-faq-redesign.css` | **Modify** | Mengganti warna merah marun tombol kategori menjadi Navy MagKom (`#1b3860`), mengubah accent bar menjadi biru, dan mendefinisikan keyframe animasi gerakan tangan menunjuk `@keyframes pointingGesture`. |
| `tests/Feature/LandingPageFaqRedesignTest.php` | **Modify** | Memperbarui assertion tes untuk memvalidasi elemen animasi tangan, seragam tema Kominfo, dan palet warna navy. |

---

## 5. Langkah Implementasi

### Fase 1: Perubahan CSS & Keyframe Animasi Tangan
- [x] **Langkah 1.1**: Perbarui [public/css/landing-faq-redesign.css](file:///d:/MAGKOM/MAGKOM/public/css/landing-faq-redesign.css):
  - Ubah `.faq-accent-bar` menjadi gradasi biru (`linear-gradient(90deg, #1b3860 0%, #0751ff 100%)`).
  - Ubah `.faq-category-header-btn` dari background `#882426` menjadi gradasi Navy resmi MagKom (`linear-gradient(135deg, #1b3860 0%, #122846 100%)`).
  - Atur efek hover kategori menjadi biru yang lebih terang (`linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)`).
  - Atur state kategori terbuka `.is-expanded` menjadi navy pekat beraksen border biru.
  - Buat animasi keyframe `@keyframes pointingGesture`:
    - Titik rotasi (*transform-origin*) pada sendi bahu lengan kiri.
    - Gerakan mengayun menunjuk secara lembut ($0^\circ \rightarrow -6^\circ \rightarrow +2^\circ \rightarrow 0^\circ$).
    - Pasang `@media (prefers-reduced-motion: reduce)` untuk mematikan animasi jika pengguna sensitif terhadap gerakan.

### Fase 2: Redesain Vektor SVG Seragam Kominfo & Tangan Menunjuk
- [x] **Langkah 2.1**: Perbarui elemen SVG di section `#faq` pada [resources/views/public/landing.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/landing.blade.php):
  - Ubah gradien teks 3D `FAQ`:
    - Bagian depan: Gradien Navy MagKom (`#1b3860` ke `#0284c7`).
    - Bagian sisi 3D: Dark Navy Depth (`#0f1f38` ke `#0a1526`).
  - Ubah kemeja karakter:
    - Warna kemeja dinas: Biru navy Kominfo (`#1b3860`) dengan lis kerah biru muda (`#60a5fa`).
    - Pasang badge/patch kecil MAGKOM di saku dada kiri.
    - Lanyard & ID card disesuaikan dengan warna identitas Diskominfo Sleman.
    - Celana panjang: Warna abu-abu gelap formal (`#334155`).
  - Bungkus lengan & tangan kiri dalam `<g id="charPointingArm" class="faq-pointing-arm">` dengan pose menunjuk ke arah teks FAQ dan informasi di samping kanan.

### Fase 3: Pengujian Otomatis & Visual
- [x] **Langkah 3.1**: Perbarui automated test di [tests/Feature/LandingPageFaqRedesignTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/LandingPageFaqRedesignTest.php):
  - Assert keberadaan class animasi `.faq-pointing-arm` dan grup seragam Kominfo.
  - Assert keberadaan palet warna biru dan layout responsif.
- [x] **Langkah 3.2**: Jalankan feature test suite dengan perintah:
  `php artisan test --filter=LandingPageFaqRedesignTest`
- [x] **Langkah 3.3**: Verifikasi visual di browser lokal (`http://127.0.0.1:8000/#faq`).

---

## 6. Strategi Testing

1. **Automated Feature Testing**:
   - Memastikan view `landing.blade.php` berhasil di-render tanpa error.
   - Assert class `faq-pointing-arm`, elemen visual SVG seragam Kominfo, dan 5 kategori FAQ.
2. **Visual & Motion Testing**:
   - Memastikan gerakan tangan menunjuk berulang secara halus tanpa patah-patah (*smooth continuous loop*).
   - Memastikan warna biru kemeja dan tombol kategori serasi 100% dengan palet header, kuota card, dan tombol pendaftaran landing page.
   - Menguji tampilan pada mode terang dan mode gelap (*data-theme="dark"*).

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Titik pivot transform-origin tangan melenceng saat dianimasikan** | Lengan tampak terlepas dari badan saat bergerak | Tentukan koordinat `transform-origin` secara tepat pada sendi bahu karakter (`transform-origin: 185px 160px;`). |
| **Beban CPU akibat animasi berulang terus menerus** | Penurunan performa pada perangkat berspesifikasi rendah | Gunakan properti `transform` murni (tanpa repainting layout), tambahkan `will-change: transform`, dan terapkan interval jeda lembut. |

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Dokumen rencana disetujui secara resmi oleh pengguna.
- [x] Kemeja karakter pada ilustrasi FAQ berganti menjadi seragam biru dinas Kominfo bertema MAGKOM.
- [x] Tangan karakter yang menunjuk memiliki gerakan animasi mikro yang halus dan alami.
- [x] Teks 3D FAQ berubah menjadi gradasi Navy Blue yang elegan.
- [x] Tombol kategori FAQ di kolom kanan berubah menjadi warna Navy MagKom yang selaras dengan seluruh landing page.
- [x] Seluruh automated feature test (`LandingPageFaqRedesignTest`) lolos 100% (*green/pass*).
