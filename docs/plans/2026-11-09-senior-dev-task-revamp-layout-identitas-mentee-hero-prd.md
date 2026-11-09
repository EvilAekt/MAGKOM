# Senior Developer Task Brief (PRD & Implementation Plan)
## Penyempurnaan Tata Letak (Layout Revamp) Blok Identitas Mentee pada Kartu Hero Profil 360°

- **Target Persona Pelaksana**: Junior Developer / AI Model (Gemini 3.8 Flash / Claude Haiku / Cursor)
- **Modul**: Pembimbing Portal — Halaman Profil 360° Peserta Bimbingan (`/pembimbing/peserta/{id}`)
- **Dokumen Referensi**: Tangkapan Layar `profil-360-mentee-hero-identity-current.png` (`media_1794225909603_e74143ad.png`), PRD MagKom v1.1, Design System MagKom
- **Tanggal Dibuat**: 09 November 2026
- **Status**: Ready for Implementation (Awaiting Confirmation)

---

## 1. Executive Summary & Root Cause Analysis (RCA)

### 1.1 Masalah Tata Letak Saat Ini (Tumpukan 4 Tingkat Vertikal yang Kaku)
Berdasarkan tangkapan layar `profil-360-mentee-hero-identity-current.png`, blok identitas mentee pada sisi kiri kartu hero (`.mentee-hero-left`) saat ini memiliki beberapa kekurangan visual yang signifikan:

```
[ AVATAR RING ]      Naufal Pratama               <-- Tingkat 1: Nama
   (120px)           SMK Negeri 2 Depok Sleman    <-- Tingkat 2: Institusi
                     [ Aktif ]  [ Jalur Umum ]    <-- Tingkat 3: Badges
                     [ Logbook ]   [ Tugas ]      <-- Tingkat 4: Tombol Aksi
```

1. **Ketidakselarasan Tinggi Optik (*Optical Height Mismatch*)**:
   - Avatar ring telah diperbesar menjadi $120\text{px}$.
   - Blok teks identitas di sebelahnya saat ini menumpuk 4 baris ke bawah (`h1` + `p` + `.mentee-badges-row` + `.mentee-action-buttons-row`) dengan total tinggi kumulatif mencapai $\approx 162\text{px}$.
   - Akibatnya, blok teks menjuntai melebihi batas atas dan bawah ring avatar, membuat komposisi sisi kiri kartu terasa berat ke bawah dan tidak seimbang (*vertically uncentered*).
2. **Hierarki Badge Terpisah Sendirian**:
   - Badge status `Aktif` dan `Jalur Umum` ditaruh di baris ketiga di bawah nama sekolah, terpisah dari nama peserta.
   - Pada standar antarmuka modern (GitHub, Linear, Stripe, Apple Developer), status entitas (`Aktif`) dan tipe akun (`Jalur Umum`) idealnya bersanding langsung dengan identitas utama (nama) atau disatukan ke dalam baris metadata ringkas.
3. **Tipografi & Gaya Badge yang Kurang Berdimensi**:
   - Badge `Aktif` saat ini berupa kotak hijau datar tanpa indikator status langsung (*live status indicator dot*).
   - Badge `Jalur Umum` dengan outline tipis terlihat tenggelam di atas latar belakang Deep Navy `#0F3875`.
   - Nama institusi `SMK Negeri 2 Depok Sleman` belum memiliki ikon pembeda visual (*school icon*) sehingga terasa seperti teks biasa.

---

## 2. Pilihan Rencana Tata Letak (Layout Options)

Senior Developer menyiapkan 3 opsi desain tata letak yang dapat dieksekusi murni via CSS tanpa merusak struktur markup Blade:

```
================================================================================
OPSI 1: INLINE HEADER BADGES & OPTICAL ALIGNMENT (SANGAT DIREKOMENDASIKAN)
================================================================================
[ AVATAR RING ]   Naufal Pratama   [● Aktif]  [Jalur Umum]     (Baris 1: Header)
   (120px)        🏫 SMK Negeri 2 Depok Sleman                 (Baris 2: Subtitle)
                  [  Logbook  ]   [   Tugas   ]                (Baris 3: Action)

* Total tinggi blok teks: ~118px (Sejajar sempurna 1:1 dengan avatar ring 120px)
* Hirarki: Sangat kuat, nama langsung terkonfirmasi aktif, ruang bawah lega.
================================================================================
OPSI 2: METADATA SUBTITLE ROW (COMPACT EXECUTIVE STYLE)
================================================================================
[ AVATAR RING ]   Naufal Pratama                               (Baris 1: Nama Besar)
   (120px)        SMK Negeri 2 Depok Sleman • [● Aktif] [Jalur] (Baris 2: Metadata)
                  [  Logbook  ]   [   Tugas   ]                (Baris 3: Action)

* Menekankan nama peserta dengan ukuran font lebih dominan (1.75rem / 28px).
================================================================================
OPSI 3: MODERN BENTO CARD PILL DENGAN LIVE GLOW
================================================================================
Tetap menggunakan 4 baris namun dengan gap mikro terukur (total height 120px),
badge Aktif memiliki pulsing green glow dot (efek real-time), dan tombol aksi
mendapat styling border glassmorphism yang lebih mewah.
================================================================================
```

---

## 3. Spesifikasi Teknis Opsi 1 (Rekomendasi Utama)

### 3.1 Arsitektur CSS Grid Murni (Zero Blade Modification)
Dengan memanfaatkan **CSS Grid** pada kontainer `.mentee-hero-identity`, kita dapat menyejajarkan `.mentee-hero-name` dan `.mentee-badges-row` pada baris yang sama tanpa perlu mengubah struktur HTML Blade:

```css
.mentee-hero-identity {
    display: grid;
    grid-template-areas:
        "name badges"
        "institution institution"
        "actions actions";
    grid-template-columns: auto 1fr;
    align-items: center;
    column-gap: 0.85rem;
    row-gap: 0.35rem;
}

.mentee-hero-name {
    grid-area: name;
}

.mentee-badges-row {
    grid-area: badges;
    margin: 0;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.mentee-hero-institution {
    grid-area: institution;
}

.mentee-action-buttons-row {
    grid-area: actions;
    margin-top: 0.65rem;
}
```

### 3.2 Detail Visual Enhancements

1. **Header Row (Nama & Badges)**:
   - Nama: `font-size: 1.625rem; font-weight: 800; color: #FFFFFF; letter-spacing: -0.015em;`.
   - Badge `Aktif`:
     - Tampilan modern: latar emerald lembut `background: rgba(16, 185, 129, 0.18); border: 1px solid rgba(16, 185, 129, 0.45); color: #6EE7B7;`.
     - Dilengkapi titik pulsasi hijau cerah (*live indicator dot* `::before`):
       `width: 6px; height: 6px; border-radius: 50%; background-color: #10B981; box-shadow: 0 0 8px #10B981; margin-right: 6px;`.
   - Badge `Jalur Umum`:
     - Latar emas Sleman transparan: `background: rgba(255, 195, 0, 0.12); border: 1px solid rgba(255, 195, 0, 0.45); color: #FFC300; font-weight: 700;`.
2. **Subtitle Row (Institusi)**:
   - Warna: `#CBD5E1` dengan `font-size: 0.875rem; font-weight: 500;`.
   - Dilengkapi icon sekolah SVG halus atau styling prefix agar langsung terbaca sebagai institusi pendidikan.
3. **Action Row (Tombol Logbook & Tugas)**:
   - Posisi presisi di bawah institusi dengan `gap: 0.85rem`.
   - Tombol `Logbook` dan `Tugas` memiliki tinggi $44\text{px}$, border-radius penuh pill ($9999\text{px}$), dan efek hover elastis.

---

## 4. Tabel Perbandingan Elemen (Current vs Target)

| Parameter | Kondisi Saat Ini (Current) | Target Opsi 1 (Recommended) | Dampak Visual |
| :--- | :--- | :--- | :--- |
| **Susunan Tata Letak** | 4 tingkat vertikal kaku | **3 baris harmonis (Nama + Badges sebaris)** | Mengurangi tinggi tumpukan dari $162\text{px}$ menjadi $118\text{px}$ |
| **Keselarasan Avatar** | Teks menjuntai melebihi avatar | **Sejajar optik 1:1 dengan avatar ring $120\text{px}$** | Komposisi kiri kartu seimbang, proporsional, dan estetis |
| **Badge Status `Aktif`** | Kotak hijau solid polos | **Glassmorphic emerald + glowing live status dot** | Terasa seperti sistem aplikasi modern (*live SaaS presence*) |
| **Badge `Jalur Umum`** | Outline kuning kaku | **Kuning Emas Sleman soft pill dengan border halus** | Konsisten dengan identitas warna Sleman (#FFC300) |
| **Teks Institusi** | Teks biasa tanpa pemisah | **Kontras warna teks jelas (`#CBD5E1`) + icon/spacing rapi** | Informasi sekolah/kampus langsung terbaca jelas |
| **Respon Layar Kecil** | Tumpukan kaku | **Grid otomatis collapse ke 1 kolom pada mobile (<768px)** | 100% responsif tanpa teks terpotong |

---

## 5. Strict Repository Guardrails

1. **Blade Read-Only**: File `resources/views/pembimbing/peserta/show.blade.php` **TIDAK DIUBAH SAMA SEKALI**.
2. **Satu Sumber Kebenaran Styling**: Seluruh penyesuaian dieksekusi secara eksklusif di:
   `resources/css/pages/pembimbing/peserta-show.css`.
3. **No Breaking Tests**: Seluruh selektor kelas (`.mentee-hero-name`, `.mentee-hero-institution`, `.mentee-badges-row`, `.badge-status-aktif`, `.badge-jalur-outline`, `.btn-pill-logbook`, `.btn-pill-tugas`) dipertahankan 100% agar test suite Laravel tetap hijau.
4. **Asset Compilation**: Wajib menjalankan `npm run build` setelah perubahan CSS.
5. **Verifikasi Test Suite**: Jalankan `tests/Feature/PembimbingPortalStandardizationTest.php`.

---

## 6. Step-by-Step Implementation Instructions

### Langkah 1: Perbarui Tata Letak `.mentee-hero-identity` di `peserta-show.css`

Buka file `resources/css/pages/pembimbing/peserta-show.css` sekitar baris 150–205:
Ganti rule `.mentee-hero-identity`, `.mentee-hero-name`, `.mentee-hero-institution`, `.mentee-badges-row`, dan badge-badge terkait:

```css
/* ==========================================================================
   Blok Identitas Mentee (CSS Grid Header Row)
   ========================================================================== */
.mentee-hero-identity {
    display: grid;
    grid-template-areas:
        "name badges"
        "institution institution"
        "actions actions";
    grid-template-columns: auto 1fr;
    align-items: center;
    column-gap: 0.85rem;
    row-gap: 0.35rem;
}

.mentee-hero-name {
    grid-area: name;
    font-size: 1.625rem;
    font-weight: 800;
    color: #FFFFFF;
    margin: 0;
    line-height: 1.2;
    letter-spacing: -0.015em;
    white-space: nowrap;
}

.mentee-badges-row {
    grid-area: badges;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin: 0;
    justify-self: start;
}

.badge-status-pill {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    line-height: 1;
}

/* Badge Aktif dengan Live Glowing Indicator Dot */
.badge-status-aktif {
    background: rgba(16, 185, 129, 0.18);
    border: 1px solid rgba(16, 185, 129, 0.45);
    color: #6EE7B7;
    backdrop-filter: blur(4px);
}

.badge-status-aktif::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background-color: #10B981;
    box-shadow: 0 0 6px #10B981;
    margin-right: 6px;
    display: inline-block;
}

/* Badge Jalur Umum Emas Sleman */
.badge-jalur-outline {
    background: rgba(255, 195, 0, 0.12);
    border: 1px solid rgba(255, 195, 0, 0.45);
    color: var(--mentee-amber);
    backdrop-filter: blur(4px);
}

.mentee-hero-institution {
    grid-area: institution;
    font-size: 0.875rem;
    color: #CBD5E1;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    font-weight: 500;
}

.mentee-action-buttons-row {
    grid-area: actions;
    display: flex;
    gap: 0.85rem;
    margin-top: 0.65rem;
    align-items: center;
}
```

### Langkah 2: Tambahkan Responsive Fallback untuk Mobile (<768px)
Pada bagian `@media (max-width: 768px)` di bagian bawah `peserta-show.css`, pastikan grid kembali menjadi 1 kolom vertikal:

```css
@media (max-width: 768px) {
    .mentee-hero-identity {
        grid-template-areas:
            "name"
            "badges"
            "institution"
            "actions";
        grid-template-columns: 1fr;
        row-gap: 0.5rem;
    }

    .mentee-hero-name {
        white-space: normal;
    }
}
```

---

## 7. Quality Gate & Verifikasi Pengujian

```bash
# 1. Kompilasi asset Vite
npm run build

# 2. Jalankan test suite portal pembimbing
php artisan test tests/Feature/PembimbingPortalStandardizationTest.php

# 3. Jalankan suite pembimbing lengkap
php artisan test --filter=Pembimbing

# 4. Periksa code style Pint
./vendor/bin/pint --test
```

---

## 8. Checklist Verifikasi Akhir

- [ ] Nama peserta dan badge `Aktif` + `Jalur Umum` berada di baris pertama yang sejajar rapi (*inline header row*).
- [ ] Badge `Aktif` memiliki glowing status dot hijau cerah.
- [ ] Badge `Jalur Umum` memiliki aksen Kuning Emas Sleman glassmorphism.
- [ ] Nama institusi berada di baris kedua dengan warna teks kontras `#CBD5E1`.
- [ ] Tombol aksi `Logbook` dan `Tugas` berada di baris ketiga dengan margin atas yang pas.
- [ ] Total tinggi blok identitas ($\approx 118\text{px}$) sejajar secara optik dengan tinggi avatar ring ($120\text{px}$).
- [ ] File Blade `show.blade.php` tetap 100% read-only.
- [ ] `npm run build` sukses tanpa warning.
- [ ] Seluruh feature test PHPUnit lulus 100%.

