# Rencana Kerja: Revamp Total Antarmuka Mobile (Gaya LENTERA Sleman)

Dokumen ini memetakan perbaikan menyeluruh terhadap masalah visual antarmuka mobile yang dialami pengguna pada sistem MAGKOM, khususnya pada **Landing Page (Hero Banner & Navigasi Slider)**, **Katalog Kuota**, **Autentikasi (Login & Register)**, dan **Pemberkasan Peserta**, mengacu pada prinsip kenyamanan aplikasi mobile modern Pemerintah Kabupaten Sleman (*LENTERA Sleman*).

---

## 1. Analisis Akar Masalah (Berdasarkan Temuan Visual)

Berdasarkan inspeksi tangkapan layar perangkat mobile yang diunggah pengguna:

1. **Akar Masalah 1: Tombol Navigasi Slider Menabrak Teks Konten**:
   - Di mobile (`@media (max-width: 768px)`), tombol `.hero-side-nav-btn.prev` dan `.hero-side-nav-btn.next` memiliki posisi `position: absolute; top: 50%; transform: translateY(-50%); left: 0.5rem; right: 0.5rem;`.
   - Karena konten kolom bertumpuk vertikal ke bawah sehingga kotak hero menjadi sangat tinggi ($\sim 750\text{px}$), titik `top: 50%` tepat berada di atas teks "Persyaratan Umum". Akibatnya, tombol panah bulat putih melayang tepat di atas teks dan menutupi poin-poin persyaratan.
   - **Solusi LENTERA Sleman**: Pada perangkat layar sentuh, navigasi slider dilakukan menggunakan **touch swipe horizontal** dan **dot indicators di bawah card**. Tombol panah samping floating di tengah layar wajib disembunyikan (`display: none;`) pada layar mobile.

2. **Akar Masalah 2: Pemaksaan `text-align: center !important` dari Konfigurasi Block**:
   - Konfigurasi blok hero di database memiliki `"mobile": { "text_align": "center" }` yang menghasilkan CSS `[data-wp-block-id] { text-align: center !important; }`.
   - Hal ini memaksa elemen `<ul class="hero-list">` di-center, sehingga bullet point terlempar ke tengah kiri secara tidak alami dan baris teks panjang terpotong (*clipped*).
   - **Solusi LENTERA Sleman**:
     - Headline utama (Judul Magang, Periode, Tombol CTA) terpusat rapi.
     - Seksi Persyaratan Umum dan Dokumen Pendaftaran dibungkus dalam **sub-card modern** berlatar belakang putih dengan teks rata kiri alami (`text-align: left !important;`) dan aksen garis warna di setiap sub-judul.

3. **Akar Masalah 3: Card Hero Monolitik Super Panjang**:
   - Menjejalkan 3 kolom desktop ke dalam satu box abu-abu panjang tanpa pemisah visual membuat halaman terasa kaku dan sesak di HP.
   - **Solusi LENTERA Sleman**: Membagi hero slide menjadi hierarki visual bertingkat:
     - **Kartu Atas (Highlight Banner)**: Identitas Sleman, Judul Magang, Badge Tahun, Tanggal Pendaftaran, dan Tombol "Daftar Sekarang" selebar layar (*full-width CTA*).
     - **Kartu Bawah (Info & Persyaratan)**: Dua kartu modular terpisah dengan header beraksen warna (*accent bar*) untuk Persyaratan Umum dan Dokumen Pendaftaran.

---

## 2. Rencana Arsitektur & Styling Baru

### 2.1. Hero Section Mobile (`resources/css/pages/public/landing.css`)
```css
@media (max-width: 768px) {
    /* 1. Sembunyikan tombol panah samping yang menabrak teks di HP */
    .hero-side-nav-btn {
        display: none !important;
    }

    /* 2. Format ulang Hero Box menjadi kartu bertingkat yang lega */
    .hero-box {
        padding: 1.25rem 1rem !important;
        gap: 1rem !important;
        background: #F8FAFC !important;
        border: 1px solid #E2E8F0 !important;
        border-radius: 16px !important;
    }

    /* 3. Kolom Headline Utama */
    .hero-box > div:first-child {
        text-align: center !important;
        padding-bottom: 1rem;
        border-bottom: 1px dashed #CBD5E1;
    }

    .hero-title {
        font-size: 1.65rem !important;
        line-height: 1.2 !important;
    }

    .hero-year-badge {
        justify-content: center !important;
    }

    .btn-hero-cta {
        width: 100% !important;
        min-height: 46px !important;
        font-size: 1rem !important;
    }

    /* 4. Sub-Card Modular untuk Persyaratan & Dokumen */
    .hero-box > div:nth-child(2),
    .hero-box > div:nth-child(3) {
        background: #FFFFFF !important;
        border: 1px solid #E2E8F0 !important;
        border-radius: 12px !important;
        padding: 1rem 1.15rem !important;
        text-align: left !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }

    .hero-subhead {
        font-size: 1.1rem !important;
        margin-bottom: 0.65rem !important;
        text-align: left !important;
        border-left: 3.5px solid var(--accent, #4F46E5);
        padding-left: 8px;
    }

    .hero-list {
        padding-left: 1.15rem !important;
        text-align: left !important;
        font-size: 0.85rem !important;
    }

    .hero-list li {
        margin-bottom: 0.45rem !important;
        line-height: 1.45 !important;
    }
}
```

---

## 3. Rincian File yang Akan Dimodifikasi

| No | File | Peran / Aksi |
|---|---|---|
| 1 | `resources/css/pages/public/landing.css` | **Modify**: Sembunyikan `.hero-side-nav-btn` pada mobile, pisahkan kolom hero menjadi kartu modular putih, dan reset `text-align: left` untuk persyaratan & dokumen. |
| 2 | `public/css/landing-modern.css` | **Modify**: Sinkronisasi stylesheet landing publik agar konsisten dengan `resources/css/pages/public/landing.css`. |
| 3 | `app/Services/LandingPage/LandingPageRenderer.php` | **Modify**: Cegah injeksi `text-align: center !important` pada elemen list (`ul`, `ol`, `li`) di dalam blok hero pada mode mobile. |

---

## 4. Tahapan Eksekusi Bertahap

- [ ] **Langkah 1**: Perbaiki CSS Hero Mobile di `resources/css/pages/public/landing.css`.
- [ ] **Langkah 2**: Perbaiki aturan Scoped CSS Renderer di `LandingPageRenderer.php` agar list tetap rata kiri alami.
- [ ] **Langkah 3**: Sinkronkan ke `public/css/landing-modern.css` dan jalankan `npm run build`.
- [ ] **Langkah 4**: Jalankan verifikasi otomatis Pint dan PHPUnit test.
- [ ] **Langkah 5**: Verifikasi visual multi-device (iPhone SE 375px, Android 360px, Galaxy 412px).

