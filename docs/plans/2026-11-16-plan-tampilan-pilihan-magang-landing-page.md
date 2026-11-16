# Rencana Kerja: Implementasi & Pemurnian Tampilan Komponen "Pilihan Magang dari Landing Page"

**Tanggal:** 16 November 2026  
**Status:** Draf Perencanaan (Menunggu Persetujuan Pengguna)  
**Dokumen Acuan:** `docs/01-PRD-MagKom.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `.agents/rules/AGENTS.md`  
**Aset Referensi:** Gambar `media_1794805787375_2e18fd0e.png`

---

## 1. Tujuan

Mewujudkan komponen kartu ringkasan **"Pilihan Magang dari Landing Page"** yang bersih (*clean*), presisi, dan 100% identik dengan gambar desain acuan (`media_1794805787375_2e18fd0e.png`):
1. **Aksen Line Biru Samping Kotak:** Menampilkan garis vertikal tebal warna Sleman Navy (`#003566`) pada sisi kiri kartu (`border-left: 4px solid #003566;`) yang melengkung selaras dengan sudut rounded kotak.
2. **Kepatuhan Aturan Proyek (`AGENTS.md`):** Memindahkan seluruh inline style yang sebelumnya menempel di Blade ke dalam stylesheet terstruktur di `resources/css/pages/auth/auth-depth.css`.
3. **Modularitas Komponen:** Mengekstrak kartu menjadi partial Blade reusable (`resources/views/auth/partials/_selected_quota_card.blade.php`) agar dapat digunakan secara konsisten pada halaman Registrasi (`register.blade.php`) dan halaman Login (`login.blade.php`).
4. **Data Binding Dinamis & Presisi Tipografi:**
   - Ikon kompas Sleman Navy (`#003566`) dengan jarum/border aksen emas (`#FFC300`).
   - Judul *"Pilihan Magang dari Landing Page"* berdampingan dengan badge pill tipe: `Individu (1 Orang)` atau `Kelompok (X Orang)`.
   - Nama Jurusan tebal & Jenjang (misal: `Administrasi Perkantoran (SMK)` atau `Teknik Komputer dan Jaringan (TKJ) (SMK)`) diikuti penempatan bidang: `• Penempatan: Aplikasi Informatika & E-Government (EGOV)`.
   - Periode mulai magang berbahasa Indonesia resmi (misal: `Desember 2026`).
   - Badge ketersediaan kursi: pill hijau `● Tersedia (X Kursi)` atau pill peringatan `● Waiting List`.
   - Garis pemisah putus-putus (*dashed divider*) dengan tautan aksi kanan bawah: `Ubah Pilihan di Landing Page ↺`.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. Data kuota pilihan berasal dari parameter URL (`?bulan=...&bidang=...&jurusan=...&tipe=...&jumlah=...`) dari landing page atau sesi aktif `session('pendaftaran_preferensi')`.
2. Format penulisan bulan wajib menggunakan lokalisasi bahasa Indonesia resmi Carbon (`translatedFormat('F Y')` -> `Desember 2026`).
3. Kartu ini hanya muncul (*conditional rendering*) apabila pengunjung membuka halaman dengan membawa parameter kuota atau sesi preferensi kuota aktif (`$isKuotaTerpilih`).

---

## 3. Evaluasi Opsi Arsitektur

| Kriteria | Opsi 1: Partial Reusable + Scoped CSS di `resources/css/` (Direkomendasikan) | Opsi 2: Inline Styles Langsung di File Blade |
| :--- | :--- | :--- |
| **Kepatuhan `AGENTS.md`** | **100% Patuh** (Blade bersih dari inline CSS/JS). | **Melanggar** aturan default larangan inline styling Blade. |
| **Modularitas** | **Tinggi:** 1 partial dapat dipakai di `register` & `login`. | **Rendah:** Kode styling dan markup terduplikasi. |
| **Responsivitas** | **Optimal:** Media queries dapat diatur presisi di CSS. | **Terbatas:** Inline style sulit menangani breakpoint mobile. |
| **Dampak Pemeliharaan** | Perubahan visual di masa depan cukup di 1 file CSS. | Memerlukan edit manual di banyak file Blade. |

> **Rekomendasi:** Mengadopsi **Opsi 1** demi menjaga integritas arsitektur kode, konsistensi lintas halaman auth, dan kerapian sesuai standar repositori MAGKOM.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/css/pages/auth/auth-depth.css` | **Modify** | Menambahkan styling scoped `.landing-pilihan-card` dan child elements (header, icon-box, pill, period, status, dashed separator, link, responsive mobile). |
| `resources/views/auth/partials/_selected_quota_card.blade.php` | **Create** | Mengekstrak markup kartu menjadi komponen partial reusable yang bersih dari inline style. |
| `resources/views/auth/register.blade.php` | **Modify** | Mengganti blok inline card lama dengan `@include('auth.partials._selected_quota_card', [...])`. |
| `resources/views/auth/login.blade.php` | **Modify** | Mengintegrasikan `@include('auth.partials._selected_quota_card', [...])` saat parameter kuota tersedia untuk keselarasan visual. |
| `tests/Feature/LandingPageToRegistrationPrefillTest.php` | **Modify** | Memastikan pengujian memvalidasi struktur kelas CSS dan rendering elemen kartu baru. |

---

## 5. Rincian Desain & Token CSS

### Kelas CSS yang Akan Didefinisikan:
```css
/* Card Container */
.landing-pilihan-card {
    background: #F8FAFC;
    border: 1px solid #CBD5E1;
    border-left: 4px solid #003566;
    border-radius: 14px;
    padding: 14px 18px;
    margin-bottom: 22px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    box-shadow: 0 2px 8px rgba(0, 53, 102, 0.04);
}

/* Header & Title */
.lpc-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
}

.lpc-title-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.lpc-icon-box {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: #003566;
    color: #FFC300;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.lpc-title {
    font-size: 0.90625rem; /* 14.5px */
    font-weight: 700;
    color: #003566;
    letter-spacing: -0.01em;
}

/* Badge Tipe */
.lpc-pill-type {
    display: inline-flex;
    align-items: center;
    padding: 3px 12px;
    border-radius: 999px;
    background: #EEF3FB;
    border: 1px solid #D5DEEE;
    color: #003566;
    font-size: 0.75rem; /* 12px */
    font-weight: 600;
}

/* Baris Jurusan & Penempatan */
.lpc-info-row {
    font-size: 0.90625rem;
    font-weight: 700;
    color: #0F172A;
    line-height: 1.4;
    margin-top: 2px;
}

.lpc-penempatan {
    font-weight: 500;
    color: #334155;
}

/* Baris Periode & Ketersediaan */
.lpc-period-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
}

.lpc-period-label {
    font-size: 0.8125rem; /* 13px */
    color: #475569;
}

.lpc-period-value {
    color: #003566;
    font-weight: 700;
}

.lpc-badge-available {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 10px;
    border-radius: 999px;
    background: #DCFCE7;
    border: 1px solid #86EFAC;
    color: #15803D;
    font-size: 0.75rem;
    font-weight: 700;
}

.lpc-dot-available {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #16A34A;
}

.lpc-badge-waiting {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 2px 10px;
    border-radius: 999px;
    background: #FEF3C7;
    border: 1px solid #FCD34D;
    color: #92400E;
    font-size: 0.75rem;
    font-weight: 700;
}

/* Dashed Separator */
.lpc-dashed-divider {
    border: none;
    border-top: 1px dashed #CBD5E1;
    margin: 4px 0 2px 0;
}

/* Action Link */
.lpc-action-row {
    display: flex;
    justify-content: flex-end;
}

.lpc-link-change {
    font-size: 0.78125rem; /* 12.5px */
    font-weight: 700;
    color: #003566;
    text-decoration: underline;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: color 0.15s ease;
}

.lpc-link-change:hover {
    color: #02275D;
}
```

---

## 6. Langkah Implementasi Bertahap

- [x] **Langkah 1: Penyusunan Token & Kelas CSS di `resources/css/pages/auth/auth-depth.css`**
  - Menambahkan selector `.landing-pilihan-card` dan seluruh styling turunan sesuai token warna dan tata letak gambar desain.
  - Menambahkan aturan responsif mobile (`@media (max-width: 480px)`).

- [x] **Langkah 2: Pembuatan Partial Reusable Blade `resources/views/auth/partials/_selected_quota_card.blade.php`**
  - Membangun markup semantik Blade tanpa inline style.
  - Memastikan data binding nama jurusan, kategori jenjang, nama bidang penempatan, tanggal mulai (Carbon ID), status kursi, tipe pendaftaran, dan link ubah pilihan berfungsi dinamis.

- [x] **Langkah 3: Integrasi pada View `resources/views/auth/register.blade.php`**
  - Menggantikan elemen div inline lama dengan include partial baru.
  - Memastikan hidden inputs (`redirect_bulan`, `redirect_bidang`, dll.) tetap utuh.

- [x] **Langkah 4: Integrasi pada View `resources/views/auth/login.blade.php`**
  - Menampilkan partial kartu pilihan jika dibuka dengan parameter kuota landing page, menggantikan banner teks lama.

- [x] **Langkah 5: Kompilasi Aset Frontend**
  - Menjalankan `npm run build` dengan Vite.
  - Memverifikasi output manifest dan bundle CSS/JS.

- [x] **Langkah 6: Pengujian & Quality Gates**
  - Menjalankan PHPUnit suite:
    - `LandingPageToRegistrationPrefillTest.php`
    - `AuthDepthVisualParityTest.php`
    - `PendaftaranPesertaTest.php`
  - Menjalankan `vendor/bin/pint --test` untuk verifikasi formatting PSR-12.
  - Melakukan pengecekan respons HTTP 200 via `http://127.0.0.1:8000/register?bulan=1&bidang=1&jurusan=1`.

---

## 7. Strategi Testing

1. **Happy Path:**
   - Membuka URL `/register?bulan=1&bidang=1&jurusan=1&tipe=mandiri` -> Kartu tampil dengan garis biru samping, judul, jurusan, bulan, badge hijau kursi tersedia, dan tautan ubah.
   - Membuka URL `/register?bulan=1&bidang=1&jurusan=1&tipe=kelompok&jumlah=3` -> Badge tipe berubah menjadi `Kelompok (3 Orang)`.
   - Mengklik tautan "Ubah Pilihan di Landing Page ↺" -> Mengarahkan ke `/#kuota`.
2. **Sad Path & Edge Cases:**
   - Membuka `/register` tanpa query parameter -> Kartu tidak ditampilkan sama sekali (`$isKuotaTerpilih` false).
   - Membuka dengan bulan yang kuotanya habis (0 kursi) -> Badge status berganti menjadi `Waiting List` berlatar kuning.
   - Layar mobile (360px - 480px) -> Elemen membungkus rapi (*wrap*) tanpa pemotongan teks atau scrollbar horizontal tak terduga.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Komponen kartu memiliki line biru vertikal di sisi kiri (`border-left: 4px solid #003566;`) persis gambar referensi.
- [x] Seluruh CSS didefinisikan di `resources/css/pages/auth/auth-depth.css`, tanpa inline style di Blade.
- [x] Format bulan menggunakan lokalisasi bahasa Indonesia (contoh: "Desember 2026").
- [x] Partial Blade reusable dibuat dan terintegrasi di `register.blade.php` serta `login.blade.php`.
- [x] `npm run build` sukses tanpa error.
- [x] Seluruh pengujian PHPUnit terkait lulus 100%.
- [x] Kode lulus pengecekan Laravel Pint.

