# Rencana Kerja: Penyesuaian Hierarki Visual & Perbesaran Judul Seksi Penempatan & Kuota Magang

Dokumen ini merinci rencana teknis perbaikan hierarki tipografi dan perbesaran ukuran teks judul utama seksi penempatan kuota (*"Pilih Penempatan & Ketersediaan Kuota Magang"*) pada landing page MAGKOM, agar proporsional, gagah, dan berwibawa sebagai gerbang utama pendaftaran magang Diskominfo Sleman.

---

## 1. Tujuan
1. **Mengembalikan & Memperbesar Skala Tipografi Judul Seksi**: Mengatasi masalah ukuran teks judul seksi yang saat ini tampak kerdil/hampir menyatu dengan ukuran teks paragraf deskripsi (terlihat seperti teks body biasa daripada judul utama seksi).
2. **Mempertegas Hierarki Visual Konten**: Menciptakan kontras visual yang ideal antara Judul Utama Seksi (H2), Subjudul Deskripsi (P), dan Elemen Form Pertanyaan di bawahnya.
3. **Memperbaiki Akar Masalah CSS Specificity**: Menghilangkan efek samping selektor `[data-wp-block-id] h2` di blok engine WordPress-like yang menimpa kelas `.section-heading-lg` dengan `inherit` (16px).
4. **Menjaga Responsivitas Mobile & Tablet**: Memastikan ukuran font menggunakan skala fluida dinamis (`clamp()`) yang nyaman dibaca di layar ponsel (mobile), tablet, maupun monitor desktop lebar.

---

## 2. Asumsi & Investigasi Akar Masalah (Root Cause Analysis)

### Temuan Investigasi Kode (Read-Only Inspection):
1. **Elemen HTML Terkait**:
   Pada [`resources/views/public/blocks/kuota.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/public/blocks/kuota.blade.php#L30-L34):
   ```html
   <section id="penempatan" class="landing-section" data-wp-block="kuota" data-wp-block-id="{{ $blockId ?? 'block-kuota-1' }}" ...>
       <span id="kuota" class="sr-only" aria-hidden="true"></span>
       <h2 class="section-heading-lg">{{ $kuotaHeading }}</h2>
       <p class="section-desc-sub">{{ $kuotaSubheading }}</p>
   ```
2. **Konflik Spesifisitas CSS (Root Cause)**:
   - Di [`resources/css/pages/public/landing.css`](file:///d:/MAGKOM/MAGKOM/resources/css/pages/public/landing.css#L774-L782):
     ```css
     .section-heading-lg {
         font-family: var(--font-display);
         font-size: clamp(2rem, 3.2vw, 2.5rem);
         font-weight: 750;
         ...
     }
     ```
     Ukuran ini sudah dirancang besar (`2rem` s/d `2.5rem`).
   - **Namun**, pada baris 2166–2171 (`resources/css/pages/public/landing.css` dan `public/css/landing-modern.css`):
     ```css
     [data-wp-block-id] h1,
     [data-wp-block-id] h2,
     [data-wp-block-id] .hero-main-title,
     [data-wp-block-id] .section-title {
         font-size: var(--block-font-size, inherit);
     }
     ```
   - Karena `<section>` memiliki atribut `data-wp-block-id="block-kuota-1"`, selektor `[data-wp-block-id] h2` (spesifisitas `0, 1, 1`) **mengalahkan** kelas `.section-heading-lg` (spesifisitas `0, 1, 0`)!
   - Karena `--block-font-size` tidak didefinisikan secara khusus pada level blok, nilainya jatuh ke fallback `inherit` (16px), sehingga judul utama seksi ini mengecil secara drastis menjadi seukuran teks biasa.

### Asumsi Kerja:
- Ukuran judul yang diinginkan pengguna adalah judul seksi yang mencolok, tegas, dan modern dengan ukuran tampilan desktop ~36px–42px (`2.25rem`–`2.625rem`) dan mobile ~26px–30px (`1.625rem`–`1.875rem`).
- Perubahan ini tidak memerlukan perubahan struktur Blade markup karena struktur semantik HTML `<h2>` dan `<p>` sudah tepat.

---

## 3. Pendekatan Teknis (Evaluasi Opsi)

| Kriteria | Opsi 1 (Rekomendasi Utama): Scoped CSS Specificity & Typo Scale | Opsi 2: Inline Style di Blade |
|---|---|---|
| **Metode** | Perbaiki aturan blok CMS di `landing.css` agar tidak menimpa judul spesifik, dan perkuat spesifisitas selektor `.section-heading-lg` serta `#penempatan .section-heading-lg` dengan tipografi fluida. | Menambahkan atribut `style="font-size: 2.5rem;"` langsung di elemen `<h2>` di file Blade. |
| **Kepatuhan Aturan `AGENTS.md`** | **100% Patuh**: Semua styling dikelola di `resources/css/` dan dibundle via Vite. | **Melanggar Aturan**: Dilarang menggunakan inline CSS di Blade untuk styling statis. |
| **Responsivitas Layar** | Menggunakan `clamp()` fluida dan media queries untuk tampilan mobile, tablet, dan desktop yang sempurna. | Kaku/statis (tidak adaptif saat dibuka di HP). |
| **Dampak ke CMS / Site Editor** | Site editor dan preview mode tetap berfungsi normal. | Nilai font-size statis mengunci kontrol kustomisasi. |

> **Rekomendasi**: Pilih **Opsi 1**.

### Detail Spesifikasi Desain Tipografi (Opsi 1):
```css
/* Spesifisitas scoped untuk judul utama seksi penempatan & blok umum (disesuaikan agar proporsional dan tidak mendominasi) */
#penempatan .section-heading-lg,
.landing-section .section-heading-lg,
.section-heading-lg {
    font-family: var(--font-display, 'Lexend', sans-serif);
    font-size: clamp(1.375rem, 1.8vw, 1.75rem) !important; /* 22px s/d 28px */
    font-weight: 700;
    letter-spacing: -0.02em;
    line-height: 1.3;
    color: var(--navy, #003566);
    margin: 0;
}

/* Penyesuaian subjudul agar proporsional dan tidak bersaing dengan judul utama */
#penempatan .section-desc-sub,
.section-desc-sub {
    margin-top: 0.5rem;
    margin-bottom: 1.75rem;
    font-size: clamp(0.925rem, 1.1vw, 1rem); /* 14.8px s/d 16px */
    line-height: 1.6;
    color: #475569;
    max-width: 860px;
}
```

Dan penyesuaian aturan universal block engine pada baris 2166 `landing.css`:
```css
/* Pastikan [data-wp-block-id] hanya meng-override jika --block-font-size benar-benar didefinisikan */
[data-wp-block-id][style*="--block-font-size"] h1,
[data-wp-block-id][style*="--block-font-size"] h2,
[data-wp-block-id][style*="--block-font-size"] .hero-main-title,
[data-wp-block-id][style*="--block-font-size"] .section-title {
    font-size: var(--block-font-size);
}
```

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| [`resources/css/pages/public/landing.css`](file:///d:/MAGKOM/MAGKOM/resources/css/pages/public/landing.css) | **Modify** | Perkuat selektor `.section-heading-lg` & `#penempatan .section-heading-lg` dengan font fluida `clamp(2rem, 3.5vw, 2.75rem)`, font-weight 800, serta amankan rule baris 2166 dari fallback `inherit`. |
| [`public/css/landing-modern.css`](file:///d:/MAGKOM/MAGKOM/public/css/landing-modern.css) | **Modify** | Sinkronisasi perbaikan stylesheet scoped yang sama untuk kompatibilitas aset langsung. |

*Catatan*: File Blade [`resources/views/public/blocks/kuota.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/public/blocks/kuota.blade.php) tidak perlu diubah strukturnya sesuai mandat pemisahan tanggung jawab CSS di `resources/css/`.

---

## 5. Langkah Implementasi Bertahap

- [x] **Langkah 1: Perbarui Styling Heading di `resources/css/pages/public/landing.css`**
  - Perkuat selektor `#penempatan .section-heading-lg` dan `.section-heading-lg` dengan `clamp(2rem, 3.5vw, 2.75rem)`, `font-weight: 800`, dan `line-height: 1.2`.
  - Sesuaikan selektor `[data-wp-block-id] h2` pada baris 2166 agar tidak menimpa heading secara agresif jika `--block-font-size` tidak ditentukan.
- [x] **Langkah 2: Sinkronkan ke `public/css/landing-modern.css`**
  - Pastikan stylesheet statis `public/css/landing-modern.css` mencerminkan perbaikan yang sama persis.
- [x] **Langkah 3: Kompilasi Aset Vite**
  - Jalankan `npm run build` untuk memverifikasi build CSS & JS berjalan bersih tanpa error.
- [x] **Langkah 4: Validasi Frontend & Visual Parity**
  - Refresh tampilan di browser pada resolusi Desktop (1440px / 1200px), Tablet (768px), dan Mobile (375px) untuk memastikan judul tampak menonjol, tajam, dan proporsional.

---

## 6. Strategi Testing

1. **Automated Feature Test**:
   - Jalankan `php artisan test tests/Feature/LandingPageTest.php` untuk memastikan response landing page tetap `200 OK` dan seluruh assertion konten tetap hijau.
   - Jalankan `php artisan test tests/Feature/LandingPage/CoreBlockEngineTest.php` untuk memastikan rendering blok CMS tidak terganggu.
2. **Visual & Responsive Testing**:
   - Verifikasi bahwa teks `"Pilih Penempatan & Ketersediaan Kuota Magang"` tampil dominan sebagai judul utama seksi penempatan.
   - Verifikasi kontras antara H2 dan deskripsi subjudul di bawahnya.
   - Periksa bahwa font size mengecil secara anggun saat viewport diperkecil ke ukuran mobile tanpa teks terpotong (*no horizontal overflow*).

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Selektor CSS memengaruhi seksi blok lain yang menggunakan H2 | Rendah | Menggunakan selektor scoped spesifik `#penempatan .section-heading-lg` dan mempertahankan class `.section-heading-lg` yang hanya dipakai di landing block resmi. |
| Cache browser pengguna menyimpan CSS lama | Rendah | `landing.blade.php` sudah memiliki *cache-busting query string* `?v={{ filemtime(...) }}` dan Vite HMR aktif di background. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Teks judul seksi *"Pilih Penempatan & Ketersediaan Kuota Magang"* tampil besar, tebal (*bold/800*), gagah, dan berwibawa sesuai peruntukannya sebagai judul utama seksi.
- [x] Subjudul deskripsi di bawahnya memiliki proporsi yang rapi dan mudah dibaca.
- [x] Tidak ada aturan CSS atau inline style yang disisipkan sembarangan di file Blade.
- [x] `npm run build` sukses 100%.
- [x] Seluruh test suite landing page lulus hijau (`PASS`).

