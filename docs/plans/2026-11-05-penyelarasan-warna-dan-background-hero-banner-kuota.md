# Plan: Penyelarasan Warna & Background Card Hero Banner Manajemen Kuota (Signature Sleman Navy Contour)

**Tanggal**: 05 November 2026  
**Status**: Selesai Terverifikasi (100% Pass)  
**Referensi**: Permintaan Pengguna, Bukti Tangkapan Layar `media_1793812609780_c2ee3de9.png` (Kondisi Saat Ini) & `media_1793812630315_d4fac3b8.png` (Standar Konsisten yang Diharapkan), Aturan `AGENTS.md`.

---

## 1. Analisis Masalah (Visual Inconsistency & Cluttered Background)

Berdasarkan 2 tangkapan layar yang diunggah oleh pengguna:

1. **Perbandingan Visual (Screenshot 1 vs Screenshot 2)**:
   - **Screenshot 1 (`media_1793812609780_c2ee3de9.png` - Kondisi Saat Ini)**:
     Kartu Hero Banner Bulan Aktif pada halaman Manajemen Kuota ([`resources/views/admin/kuota/index.blade.php:103`](file:///d:/laragon/www/MAGKOM/resources/views/admin/kuota/index.blade.php#L103)) saat ini menggunakan background gambar `images/dashboard_hero_bg.jpg`:
     ```css
     background: linear-gradient(95deg, rgba(6, 65, 204, 0.96) 0%, rgba(6, 65, 204, 0.88) 45%, rgba(6, 65, 204, 0.38) 100%),
                 url('{{ asset('images/dashboard_hero_bg.jpg') }}') right center / cover no-repeat;
     ```
     Gambar tersebut memuat ilustrasi grafik batang persentase (19%, 50%, 70%, 63%), peta dunia dengan pin lokasi, serta jendela obrolan/avatar bertuliskan *"Shared Team Workspace"*. Ilustrasi ini terlihat generik, membuat tampilan "ramai", dan tidak berkaitan langsung dengan data operasional kuota magang Sleman.
   - **Screenshot 2 (`media_1793812630315_d4fac3b8.png` - Standar Visual yang Diinginkan)**:
     Merupakan signature visual resmi dari **Admin Dashboard Hero Banner (`.cv-hero-banner`)** di [`resources/css/pages/admin/dashboard.css:60`](file:///d:/laragon/www/MAGKOM/resources/css/pages/admin/dashboard.css#L60), yaitu:
     - Latar belakang gradien biru dongker khas Sleman (*Sleman Navy Gradient*: `#06318C` hingga `#061E5B`).
     - Aksen pencahayaan radial lembut (*radial glow*).
     - Pola garis kontur gelombang topografis dan lingkaran konsentris (*topographic contour waves & concentric ellipses* / `--cv-contour`) di sisi kanan bawah.
     - Penampilan bersih, tenang, elegan, dan profesional tanpa gambar ilustrasi yang mengganggu keterbacaan data.

2. **Akar Masalah Teknis**:
   - Hero banner kuota di `admin/kuota/index.blade.php` belum diselaraskan dengan aset signature `--cv-contour` yang telah digunakan di Dashboard Utama Admin dan SuperAdmin.
   - Variabel `--cv-contour` saat ini baru didefinisikan secara lokal di `resources/css/pages/admin/dashboard.css`, belum terdaftar secara global di `resources/css/base/variables.css` maupun `resources/css/components/cards.css`.

---

## 2. Tujuan & Solusi Arsitektur

1. **Eliminasi Gambar Generik `dashboard_hero_bg.jpg`**:
   - Menghilangkan pemanggilan asset `images/dashboard_hero_bg.jpg` pada kelas `.hero-banner-blue`.
2. **Konsistensi Signature Navy Contour (100% Parity dengan Screenshot 2)**:
   - Menerapkan kombinasi latar belakang resmi:
     ```css
     background: var(--cv-contour) right -40px top -20px / 720px auto no-repeat,
                 radial-gradient(80% 120% at 100% 0%, rgba(7, 81, 255, 0.4), transparent 60%),
                 linear-gradient(160deg, #06318C 0%, #061E5B 100%);
     ```
   - Menambahkan border halus `1px solid rgba(255, 255, 255, 0.14)` dan drop shadow halus `0 8px 24px -4px rgba(6, 49, 140, 0.35)`.
3. **Standarisasi Variabel Global di `resources/css/base/variables.css`**:
   - Mendaftarkan `--cv-contour` di level `:root` pada `variables.css` agar dapat digunakan secara modular di seluruh komponen kartu hero tanpa duplikasi kode data-URI SVG.
4. **Dukungan Dark Mode Otomatis**:
   - Menambahkan varian mode gelap yang selaras dengan tema sistem (`#102138` hingga `#0d1b2f`).
5. **Kepatuhan Aturan MAGKOM (`AGENTS.md`)**:
   - Styling dikelola terpusat di `resources/css/components/cards.css` dan dikompilasi melalui Vite.
   - Tidak mengubah struktur informasi atau logika bulan magang (tanggal buka, tutup, kuota terisi tetap utuh).

---

## 3. Rincian Perubahan File

| No | File Target | Aksi | Ringkasan Perubahan |
| :-: | :--- | :---: | :--- |
| 1 | `resources/css/base/variables.css` | **Modify** | Daftarkan variabel global `--cv-contour` di `:root` menggunakan data-URI SVG garis kontur & elips konsentris. |
| 2 | `resources/css/components/cards.css` | **Modify** | Tambahkan kelas `.hero-banner-blue` dengan latar belakang `--cv-contour`, radial glow, dan gradien Sleman Navy (#06318C ke #061E5B) beserta dukungan dark mode. |
| 3 | `resources/views/admin/kuota/index.blade.php` | **Modify** | Perbarui styling scoped `.hero-banner-blue` di blok `<style>` agar merujuk ke variabel `--cv-contour` dan menghapus asset gambar `dashboard_hero_bg.jpg`. |

---

## 4. Langkah Implementasi Terperinci

### Langkah 1: Daftarkan `--cv-contour` di `resources/css/base/variables.css`
Tambahkan token SVG contour di blok `:root`:
```css
--cv-contour: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='600' height='300' viewBox='0 0 600 300' fill='none' stroke='white' stroke-opacity='.09' stroke-width='1.2'%3E%3Cpath d='M-20 220C80 180 120 250 220 200S380 90 480 130 620 60 640 40'/%3E%3Cpath d='M-20 250C90 210 140 280 240 230S390 120 500 160 630 90 650 70'/%3E%3Cpath d='M-20 190C70 150 110 220 200 170S370 60 460 100 610 30 630 10'/%3E%3Cpath d='M-20 280C100 240 160 300 260 260S400 150 520 190 640 120 660 100'/%3E%3Cellipse cx='470' cy='120' rx='60' ry='26'/%3E%3Cellipse cx='470' cy='120' rx='34' ry='14'/%3E%3Cellipse cx='470' cy='120' rx='12' ry='5'/%3E%3C/svg%3E");
```

### Langkah 2: Tambahkan Definisi Kelas `.hero-banner-blue` di `resources/css/components/cards.css`
```css
/* ==========================================================================
   Hero Banner Blue - Manajemen Kuota & Periode (Signature Contour Parity)
   ========================================================================== */
.hero-banner-blue {
    background: var(--cv-contour) right -40px top -20px / 720px auto no-repeat,
                radial-gradient(80% 120% at 100% 0%, rgba(7, 81, 255, 0.4), transparent 60%),
                linear-gradient(160deg, #06318C 0%, #061E5B 100%) !important;
    border-radius: 16px;
    padding: 2rem 2.25rem;
    color: #FFFFFF;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 24px -4px rgba(6, 49, 140, 0.35);
    border: 1px solid rgba(255, 255, 255, 0.14);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1.5rem;
    min-height: 190px;
}

.hero-banner-blue::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    pointer-events: none;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.22);
}

[data-theme="dark"] .hero-banner-blue {
    background: var(--cv-contour) right -40px top -20px / 720px auto no-repeat,
                radial-gradient(80% 120% at 100% 0%, rgba(7, 81, 255, 0.25), transparent 60%),
                linear-gradient(160deg, #102138 0%, #0d1b2f 100%) !important;
    border-color: #26354D;
}
```

### Langkah 3: Selaraskan Blok `<style>` di `resources/views/admin/kuota/index.blade.php`
Perbarui baris 102–125 pada `admin/kuota/index.blade.php` agar mengganti `images/dashboard_hero_bg.jpg` dengan gradien signature dan variabel `--cv-contour`.

### Langkah 4: Kompilasi Asset & Quality Gates
1. Jalankan `npm run build` untuk memproses bundle Vite asset CSS/JS.
2. Jalankan automated tests:
   ```bash
   php artisan test tests/Feature/AdminKuotaRevampTest.php tests/Feature/AdminChildPagesVisualTest.php
   ```
3. Jalankan pengecekan format kode Laravel Pint:
   ```bash
   ./vendor/bin/pint --test
   ```

---

## 5. Kriteria Keberhasilan (Definition of Done)

- [x] Latar belakang Hero Banner pada halaman Manajemen Kuota berubah dari gambar bitmap generik (diagram/peta/avatar) menjadi tampilan signature Sleman Navy dengan garis kontur topografis dan lingkaran konsentris.
- [x] Tampilan 100% konsisten dan seragam dengan Screenshot 2 (Dashboard Admin Hero Banner).
- [x] Seluruh data operasional di dalam banner (badge Bulan Aktif, status Dibuka/Ditutup, Tanggal buka, Tanggal tutup, Mulai magang, Kuota terisi) tetap terbaca tajam dan jelas dengan kontras tinggi.
- [x] Tampilan responsif pada mobile dan desktop tetap rapi.
- [x] Mode gelap (*dark mode*) terakomodasi dengan elegan.
- [x] Seluruh automated feature tests PHPUnit tetap hijau (100% PASS - 14 tests, 118 assertions).
- [x] Formatting Laravel Pint passed.

