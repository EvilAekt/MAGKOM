# Plan: Penyesuaian Tata Letak Grid Footer (Landing Page MAGKOM)

Dokumen ini merinci rencana teknis penyesuaian tata letak grid (*layout grid*) pada seksi footer landing page (`#kontak`) berdasarkan permintaan pengguna.

---

## 1. Analisis Visual & Temuan Investigasi

### 1.1 Observasi Gambar 1 & Gambar 2
- **Gambar 1 (`media_1794896818993_b76e3f90.jpg`)**:
  - Tangkapan layar kondisi footer saat ini (area 5 kolom informasi, 3 logo kemitraan, teks hak cipta, dan animasi radar wifi).
- **Gambar 2 (`media_1794897006652_a7f65b64.jpg`)**:
  - Tangkapan layar yang memuat area footer yang sama persis, dengan taskbar Windows terlihat di bagian bawah pada pukul 13:29.
- **Temuan Pixel Analysis**:
  - Struktur teks, urutan 5 kolom (*MAGKOM*, *Tautan Terkait*, *Media Sosial*, *Akun*, *Pengembang*), dan posisi 3 logo pada Gambar 1 dan Gambar 2 memiliki isi yang identik.
  - Perbedaan visual utama terletak pada tampilan layar penuh (keberadaan taskbar) serta bagaimana proporsi tinggi dan sebaran kolom footer terlihat di viewport layar monitor pengguna.

---

## 2. Opsi Penyesuaian Tata Letak Grid Footer

Berdasarkan analisis grid dan frasa pengguna *"segitu bisa di sesuaikan"*, terdapat 3 kemungkinan arah penyesuaian:

### Opsi 1: Penyeimbangan Sebaran Horizontal Kolom Grid (Even Distribution)
Saat ini pada desktop:
- Template kolom: `grid-template-columns: 1.6fr 1.1fr 1fr 1.1fr 1.1fr;` dengan `gap: 2rem;`.
- Kolom *Pengembang* di sisi paling kanan masih memiliki ruang sisa yang cukup renggang terhadap tepi kanan.
- **Solusi Penyesuaian**:
  - Mengubah rasio grid menjadi `minmax(280px, 1.8fr) repeat(4, minmax(130px, 1fr))` dengan `justify-content: space-between` dan `max-width: 80rem;`.
  - Kolom 1 (Profil & Alamat) mendapat lebar yang cukup agar alamat tidak terlalu menumpuk ke bawah, sementara 4 kolom tautan (Tautan Terkait, Media Sosial, Akun, Pengembang) terdistribusi secara seimbang dan simetris ke sisi kanan.

### Opsi 2: Perampingan Vertikal (Compact Height / Pas di Layar)
Pada layar monitor standar (1366x768 atau 1920x1080):
- Saat ini padding footer adalah `padding: 3.5rem 1.25rem 2.75rem;`.
- Jarak antara kolom teks ke garis separator logo kemitraan cukup tinggi.
- **Solusi Penyesuaian**:
  - Kurangi padding atas footer dari `3.5rem` menjadi `2.25rem`.
  - Kurangi padding bottom bar dari `1.75rem` menjadi `1.25rem`.
  - Perkecil margin antar-paragraf alamat dan jarak judul kolom ke tautan, sehingga seluruh area footer pas tampil dalam satu frame pandang di atas taskbar Windows tanpa terpotong.

---

## 3. Detail Perubahan Kode yang Direncanakan

### 3.1 File CSS: `resources/css/pages/public/landing.css` & `public/css/landing-modern.css`

```css
/* Penyesuaian Grid Desktop */
@media (min-width: 1024px) {
    .landing-footer-grid {
        max-width: 80rem; /* Melebarkan kontainer agar proporsional */
        padding: 2.5rem 1.5rem 1.75rem; /* Padding vertikal lebih ringkas */
        grid-template-columns: 1.75fr 1fr 1fr 1fr 1fr; /* 5 kolom seimbang */
        gap: 2.25rem;
    }

    .footer-col-profil {
        max-width: 320px; /* Batasi agar teks alamat rapi */
    }
}

/* Penyesuaian Bottom Bar Logo & Hak Cipta */
.footer-bottom-bar-clean {
    padding: 1.25rem 1.25rem 1.75rem;
}

.footer-partner-logos {
    margin-bottom: 0.75rem;
}
```

### 3.2 File Blade: `resources/views/public/blocks/footer.blade.php`
- Tidak ada perubahan struktur data atau business logic; struktur HTML sudah semantik dan hanya memerlukan penyesuaian kelas/styling jika diperlukan.

---

## 4. Langkah Pengujian & Validasi

1. **Kompilasi Aset**: Jalankan `npm run build`.
2. **Pengujian Fungsional**: Jalankan `php artisan test --filter="LandingPageTest"`.
3. **Format Standar**: Jalankan `./vendor/bin/pint --test`.
4. **Tangkapan Layar Resolusi Pengguna**:
   - Ambil screenshot viewport Desktop (1280x800 & 1366x768) menggunakan Microsoft Edge Headless CDP untuk mencocokkan tampilan langsung dengan layar pengguna.

