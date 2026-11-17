# Rencana Kerja: Memperkecil & Menyejajarkan Tombol Aksi Tahapan pada Tampilan Mobile

Dokumen ini merancang perbaikan tata letak pada seksi **Alur & Tahapan Magang** (`#tahapan`), khususnya dua tombol aksi utama:
1. Tombol Merah: **Unduh Panduan PDF**
2. Tombol Biru Muda: **Peta Alur Interaktif**

---

## 1. Pahami Permintaan & Kebutuhan

- **Masalah Saat Ini**:
  Pada tampilan mobile ($\le 768\text{px}$), kedua tombol memiliki *padding* dan ukuran teks desktop yang besar (`padding: 0.7rem 1.5rem`, `font-size: 0.925rem`). Akibatnya, kedua tombol tidak muat dalam satu baris horizontal dan tertekuk ke bawah (*stacked vertically* / atas-bawah), memakan banyak ruang vertikal pada layar ponsel.
- **Tujuan Akhir**:
  Memperkecil ukuran kedua tombol secara proporsional (*compact padding*, ukuran teks ~`0.78rem` / `12.5px`, ikon ~`15px`) dan menyusunnya **sejajar berdampingan** (1 baris, 2 kolom seimbang) pada tampilan mobile, tanpa mengubah tampilan dan ukuran desktop.

---

## 2. Investigasi Codebase (Read-Only)

1. **Markup** ([tahapan.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/blocks/tahapan.blade.php)):
   - Baris 41: `<div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1.25rem;">` (menggunakan inline style statis).
   - Baris 42: Tombol `Unduh Panduan PDF` menggunakan kelas `.btn-tahapan-download`.
   - Baris 50: Tombol `Peta Alur Interaktif` menggunakan kelas `.btn-tahapan-download` ditambah inline `style="background: rgba(7, 81, 255, 0.08); color: var(--primary-accent); border: 1.5px solid rgba(7, 81, 255, 0.25); ..."`.
2. **CSS** ([landing.css](file:///d:/MAGKOM/MAGKOM/resources/css/pages/public/landing.css)):
   - Baris 2227: `.btn-tahapan-download` memiliki `margin-top: 1.75rem`, `padding: 0.7rem 1.5rem`, `font-size: 0.925rem`.
   - Belum ada aturan CSS scoped responsif di media query mobile untuk menyusun kedua tombol dalam 2 kolom sejajar.

---

## 3. Evaluasi Opsi Pendekatan Teknis

| Parameter | Opsi A: Flexbox `nowrap` dengan Auto Width | Opsi B (Rekomendasi): Grid 2-Kolom Seimbang (`grid-template-columns: 1fr 1fr`) |
| :--- | :--- | :--- |
| **Deskripsi** | Mengatur `flex-wrap: nowrap` dan membiarkan tombol mengikuti panjang teks masing-masing. | Mengatur kontainer menjadi grid 2 kolom (`1fr 1fr`) dengan gap 8px, sehingga kedua tombol memiliki lebar 50% yang persis sama. |
| **Keseimbangan Visual** | Kurang simetris (tombol PDF lebih pendek dari tombol Peta Alur). | **Sangat Simetris & Rapi** (keduanya terbagi 50:50 dengan batas tepi yang sejajar). |
| **Ketahanan Responsif** | Rentan teks terpotong di layar 360px jika padding tidak seragam. | **Sangat Stabil** (kedua tombol mengisi separuh lebar layar dengan padding ringkas). |
| **Rekomendasi** | Cukup | **Direkomendasikan (Opsi B)** |

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/public/blocks/tahapan.blade.php` | Modify | 1. Bersihkan inline styling pada kontainer menjadi kelas semantik `.tahapan-action-buttons`.<br>2. Pindahkan styling tombol kedua dari inline style ke kelas `.btn-tahapan-action.btn-tahapan-peta` dan tombol pertama ke `.btn-tahapan-action.btn-tahapan-pdf`. |
| `resources/css/pages/public/landing.css` | Modify | 1. Definisikan `.tahapan-action-buttons`, `.btn-tahapan-action`, `.btn-tahapan-pdf`, dan `.btn-tahapan-peta` untuk desktop.<br>2. Pada `@media (max-width: 768px)`, terapkan `grid-template-columns: 1fr 1fr; gap: 0.5rem;` pada kontainer.<br>3. Perkecil padding tombol mobile (`padding: 0.55rem 0.5rem;`), font-size (`0.78125rem`), dan ukuran ikon (`15x15px`). |
| `public/css/landing-modern.css` | Modify | Sinkronisasi aturan CSS fallback landing modern. |

---

## 5. Langkah Implementasi Terstruktur

- [ ] **Langkah 1: Perapian Markup di `tahapan.blade.php`**
  - Ubah kontainer tombol menjadi:
    ```html
    <div class="tahapan-action-buttons">
        <a href="{{ route('panduan.pdf') }}" target="_blank" rel="noopener noreferrer" class="btn-tahapan-action btn-tahapan-pdf" title="Unduh Petunjuk Teknis & Panduan Resmi Magang Diskominfo Sleman (PDF)">
            <svg class="btn-tahapan-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <span>{{ $tahapanBtnPdf }}</span>
        </a>
        <a href="{{ route('peta-alur') }}" class="btn-tahapan-action btn-tahapan-peta" title="Buka Simulasi Peta Alur Interaktif Layar Penuh">
            <svg class="btn-tahapan-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"></circle>
                <polygon points="12 8 8 12 12 16 12 8"></polygon>
            </svg>
            <span>{{ $tahapanBtnPeta }}</span>
        </a>
    </div>
    ```

- [ ] **Langkah 2: Aturan CSS di `landing.css` & `landing-modern.css`**
  - **Desktop ($\ge 768\text{px}$)**:
    - `.tahapan-action-buttons`: `display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1.5rem;`
    - `.btn-tahapan-action`: `padding: 0.7rem 1.35rem; font-size: 0.925rem; font-weight: 600; border-radius: 8px;`
  - **Mobile ($\le 768\text{px}$)**:
    - `.tahapan-action-buttons`: `display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 0.5rem !important; width: 100% !important; margin-top: 1.15rem !important;`
    - `.btn-tahapan-action`:
      - `padding: 0.55rem 0.4rem !important;`
      - `font-size: 0.78125rem !important;` (~12.5px)
      - `gap: 0.3rem !important;`
      - `text-align: center !important;`
      - `white-space: nowrap !important;`
      - `border-radius: 8px !important;`
    - `.btn-tahapan-icon`: `width: 15px !important; height: 15px !important; flex-shrink: 0 !important;`

- [ ] **Langkah 3: Build & Quality Gate**
  - Kompilasi aset via Vite: `npm run build`.
  - Jalankan automated tests: `php artisan test --filter="LandingPageTest"`.
  - Jalankan formatter: `./vendor/bin/pint --test`.

- [ ] **Langkah 4: Verifikasi Visual Browser (CDP Headless Edge)**
  - Ambil tangkapan layar mobile ($390 \times 844\text{px}$) pada seksi `#tahapan` untuk membuktikan kedua tombol tersusun sejajar 1 baris 2 kolom dan tampak proporsional.
  - Ambil tangkapan layar desktop ($1280 \times 800\text{px}$) untuk memastikan tampilan desktop tetap konsisten.

---

## 6. Kriteria Selesai (Definition of Done)

- [ ] Kedua tombol ("Unduh Panduan PDF" dan "Peta Alur Interaktif") tersusun sejajar berdampingan (1 baris, 2 kolom seimbang) pada layar mobile.
- [ ] Ukuran tombol lebih ringkas, proporsional, dan tidak tertekuk menjadi dua baris.
- [ ] Tidak ada lagi inline style statis di Blade file `tahapan.blade.php`.
- [ ] Tampilan desktop tetap normal dan tidak mengalami regresi.
- [ ] Seluruh automated test (`LandingPageTest` & `Pint`) lulus 100%.

