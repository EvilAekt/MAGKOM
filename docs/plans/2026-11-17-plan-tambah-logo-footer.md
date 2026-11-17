# Plan: Penambahan Logo Kemitraan di Footer (Diskominfo Sleman, AMIKOM, & Manajemen Informatika)

Dokumen ini merinci rencana teknis untuk menambahkan 3 logo identitas institusi/kemitraan pada bagian bawah (*bottom bar*) footer halaman landing, tepat di atas baris hak cipta (*copyright*), dengan referensi gaya tata letak dari **[lenterasleman.com](https://lenterasleman.com/)**.

---

## 1. Analisis Kebutuhan & Desain Referensi

### 1.1 Masalah & Kebutuhan Saat Ini
- Pada bagian bawah footer (`.footer-bottom-bar-clean`), saat ini hanya terdapat satu baris teks hak cipta:
  `© 2026 Dinas Komunikasi dan Informatika Kabupaten Sleman. Seluruh hak cipta dilindungi.`
- Pengguna meminta:
  1. Teks hak cipta digeser ke bagian bawah.
  2. Tepat di atas teks hak cipta, ditambahkan 3 logo berjejer rapi:
     - **Logo 1**: Logo Dinas (Pemerintah Kabupaten / Diskominfo Sleman).
     - **Logo 2**: Logo Universitas AMIKOM Yogyakarta.
     - **Logo 3**: Logo D3 Manajemen Informatika AMIKOM.
  3. Gaya dan bentuk logo mengacu pada visual di hero **lenterasleman.com** (logo Pemkab Sleman, logo-gram Amikom ungu-emas, dan logo MI putih-oranye).

### 1.2 Analisis Sumber Aset (Asset Sourcing)
- **Logo Pemkab / Diskominfo Sleman**:
  - Sudah tersedia di proyek pada `public/images/logo-sleman.png` (resolusi tinggi, transparan).
- **Logo Universitas AMIKOM Yogyakarta**:
  - Diambil dari `https://lenterasleman.com/images/identity/logo-gram.png` (transparan, ukuran $\approx 55\text{ KB}$, rasio simetris).
  - Disimpan lokal di `public/images/logo-amikom.png`.
- **Logo D3 Manajemen Informatika AMIKOM**:
  - Diambil dari `https://lenterasleman.com/images/identity/Logo-MI.png` (transparan, ukuran $\approx 18\text{ KB}$, script putih dengan aksen dot oranye).
  - Disimpan lokal di `public/images/logo-mi.png`.

---

## 2. Rencana Arsitektur & Perubahan Kode

### 2.1 Perubahan Markup Blade (`resources/views/public/blocks/footer.blade.php`)
Di dalam `.footer-bottom-bar-clean`:
1. Menambahkan elemen wrapper `.footer-partner-logos` yang membungkus ketiga logo dalam bentuk tautan resmi yang dapat diklik (*clickable links* dengan atribut `target="_blank"` dan `rel="noopener noreferrer"`).
2. Memposisikan paragraf teks hak cipta di bawah wrapper logo dengan kelas `.footer-copyright-text`.

```html
<!-- Footer Bottom Bar Clean -->
<div class="footer-bottom-bar-clean">
    <!-- Mitra / Kolaborasi Institusi Logos -->
    <div class="footer-partner-logos" aria-label="Identitas Kemitraan Diskominfo Sleman dan AMIKOM">
        <!-- 1. Logo Dinas (Pemkab/Diskominfo Sleman) -->
        <a href="https://kominfo.slemankab.go.id" target="_blank" rel="noopener noreferrer" class="footer-partner-link" aria-label="Kunjungi Website Diskominfo Kabupaten Sleman" title="Dinas Komunikasi dan Informatika Kabupaten Sleman">
            <img src="{{ asset('images/logo-sleman.png') }}" alt="Logo Dinas Komunikasi dan Informatika Kabupaten Sleman" class="footer-partner-img footer-partner-sleman" width="38" height="46" loading="lazy">
        </a>

        <!-- 2. Logo Universitas AMIKOM Yogyakarta -->
        <a href="https://amikom.ac.id" target="_blank" rel="noopener noreferrer" class="footer-partner-link" aria-label="Kunjungi Website Universitas AMIKOM Yogyakarta" title="Universitas AMIKOM Yogyakarta">
            <img src="{{ asset('images/logo-amikom.png') }}" alt="Logo Universitas AMIKOM Yogyakarta" class="footer-partner-img footer-partner-amikom" width="46" height="46" loading="lazy">
        </a>

        <!-- 3. Logo D3 Manajemen Informatika AMIKOM -->
        <a href="https://mi.amikom.ac.id" target="_blank" rel="noopener noreferrer" class="footer-partner-link" aria-label="Kunjungi Website D3 Manajemen Informatika AMIKOM" title="D3 Manajemen Informatika Universitas AMIKOM Yogyakarta">
            <img src="{{ asset('images/logo-mi.png') }}" alt="Logo D3 Manajemen Informatika AMIKOM" class="footer-partner-img footer-partner-mi" width="58" height="46" loading="lazy">
        </a>
    </div>

    <p class="footer-copyright-text">&copy; {{ date('Y') }} {{ $footerCopyright }}</p>
</div>
```

### 2.2 Perubahan CSS (`resources/css/pages/public/landing.css` & `public/css/landing-modern.css`)
1. **Layout `.footer-partner-logos`**:
   - `display: flex; align-items: center; justify-content: center;`
   - `gap: 1.5rem;` (pada mobile: `gap: 1.15rem;`)
   - `margin-bottom: 1rem;`
2. **Interaksi & Ukuran `.footer-partner-link` & `.footer-partner-img`**:
   - `height: 42px;` di desktop, `height: 34px;` di mobile, `object-fit: contain;`
   - Transisi halus saat hover: `opacity: 0.88; transform: translateY(-1px);`
   - Filter drop-shadow lembut: `filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.25));`
3. **Teks Hak Cipta `.footer-copyright-text`**:
   - Warna `#94a3b8` dengan ukuran `0.8125rem` yang kontras dan terbaca jelas di atas latar navy footer `#122846`.

---

## 3. Tahapan Eksekusi

1. **Pengunduhan Aset**:
   - Simpan `logo-amikom.png` dan `logo-mi.png` ke folder `public/images/`.
   - Pastikan berkas transparan dan terisolasi dengan aman.
2. **Update Blade Template**:
   - Ubah `resources/views/public/blocks/footer.blade.php` sesuai rancangan di atas.
3. **Update Styling CSS**:
   - Perbarui `resources/css/pages/public/landing.css` dan `public/css/landing-modern.css`.
4. **Asset Build & Testing**:
   - Jalankan `npm run build` untuk mengompilasi CSS.
   - Jalankan `php artisan test --filter="LandingPageTest"` untuk verifikasi test suite.
   - Jalankan `./vendor/bin/pint --test` untuk memastikan kerapian kode PHP.
5. **Verifikasi Visual**:
   - Ambil screenshot pada resolusi Desktop (1280px) dan Mobile (390px) menggunakan Microsoft Edge Headless CDP.
   - Pastikan ketiga logo tampil proporsional, tajam, rapi, dan tidak ada elemen yang tumpang tindih.

