# Senior Developer Task Brief (PRD & Implementation Plan)
## Penyelarasan Layout Fluid Rata Kiri-Kanan (Edge-to-Edge) pada Halaman Profil Peserta Bimbingan

- **Target Persona Pelaksana**: Junior Developer / AI Model (Gemini 3.8 Flash / Claude Haiku / Cursor)
- **Modul**: Pembimbing Portal — Halaman Profil 360° Peserta Bimbingan (`/pembimbing/peserta/{id}`)
- **Dokumen Referensi**: Tangkapan Layar `media_1794224189998_87470f6d.jpg`, PRD MagKom v1.1, Design System MagKom
- **Tanggal Dibuat**: 09 November 2026
- **Status**: Ready for Implementation (Awaiting Confirmation)

---

## 1. Executive Summary & Root Cause Analysis (RCA)

### 1.1 Masalah Saat Ini (Dead Space Kiri-Kanan & Tepi Tidak Selaras)
Berdasarkan tangkapan layar `media_1794224189998_87470f6d.jpg`, halaman Profil Peserta Bimbingan memiliki masalah tampilan:
1. **Dead Space Kosong di Kiri dan Kanan**: Seluruh kartu konten terperangkap di tengah halaman dengan batas kaku `max-width: 1440px; margin: 0 auto;`, sehingga menyisakan ruang kosong berwarna biru muda yang sangat lebar di pinggir kiri dan kanan layar. Sementara itu, topbar di atasnya membentang penuh (rata kiri-kanan).
2. **Double-Padding Akumulatif**: Kontainer induk `.dashboard-content-area` sudah memiliki padding bawaan `1.5rem 2rem`. Di dalamnya, `.mentee-profile-page` menambahkan padding lagi sebesar `1.5rem`, menyebabkan konten tertekan semakin sempit ke dalam.
3. **Ketidakselarasan Garis Tepi Antar-Section**: Keempat section utama (Date Action Bar, Mentee Hero Card, 3 KPI Summary Grid, dan Riwayat Penugasan) perlu dipastikan memiliki titik awal kiri ($x_{left}$) dan titik akhir kanan ($x_{right}$) yang **100% rata dan presisi (*flush aligned*)**.

### 1.2 Target Solusi
1. Mengubah kontainer `.mentee-profile-page` menjadi **Fluid Full-Width (`width: 100%; max-width: none; margin: 0; padding: 0;`)**, selaras dengan pola tata letak Dashboard Pembimbing (`.bento-dashboard-wrapper`).
2. Menghilangkan double-padding dan mempercayakan jarak tepian layar sepenuhnya kepada padding standar workspace `.dashboard-content-area` (`1.5rem 2rem`).
3. Memastikan seluruh 4 baris kartu membentang rata dari batas kiri hingga batas kanan workspace secara simetris dan rapi.

---

## 2. Tabel Perbandingan Parameter Layout (Current vs Target)

| Parameter Layout | Kondisi Saat Ini (Current) | Target Penyelarasan (Target) | Dampak Desain |
| :--- | :--- | :--- | :--- |
| **Lebar Kontainer (`.mentee-profile-page`)** | `max-width: 1440px; margin: 0 auto;` | **`width: 100%; max-width: none; margin: 0;`** | Menghilangkan celah kosong (*dead space*) di kiri dan kanan layar; konten membentang rata mengikuti layar |
| **Padding Inner Page** | `padding: 1.5rem;` | **`padding: 0;`** | Menghilangkan padding ganda; tepi kartu rata persis dengan margin workspace portal |
| **Penyelarasan Tepi Kiri & Kanan** | Bervariasi karena double padding & bayangan | **100% Flush Aligned (Satu Garis Lurus Kiri & Kanan)** | Garis tepi Date Bar, Hero Card, KPI Grid, dan Kanban Board berada di sumbu $X$ yang sama |
| **Distribusi Elemen Dalam Kartu** | Standar flex | **`justify-content: space-between;`** | Elemen kiri menempel rata kiri, elemen kanan menempel rata kanan |

---

## 3. Strict Guardrails & Aturan Repositori

1. **Blade Read-Only**: File `resources/views/pembimbing/peserta/show.blade.php` **TIDAK DIUBAH SAMA SEKALI**.
2. **Satu Sumber Perubahan**: Seluruh perubahan dieksekusi murni pada `resources/css/pages/pembimbing/peserta-show.css`.
3. **Asset Compilation**: Wajib menjalankan `npm run build` setelah perubahan CSS.
4. **Verifikasi Test Suite**: Jalankan `tests/Feature/PembimbingPortalStandardizationTest.php` dengan runtime PHP 8.3 aktual.

---

## 4. Instruksi Langkah Demi Langkah untuk Junior Developer / AI

### Langkah 1: Perbarui Aturan Kontainer Halaman di `peserta-show.css`
Buka file `resources/css/pages/pembimbing/peserta-show.css` sekitar baris 35–45, perbarui rule `.mentee-profile-page`:

```css
.mentee-profile-page {
    width: 100%;
    max-width: none;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    color: var(--mentee-text-navy);
    background-color: var(--mentee-canvas-bg);
    min-height: 100%;
}
```

### Langkah 2: Pastikan Seluruh Kartu Memiliki `width: 100%` dan `box-sizing: border-box`
Pastikan keempat section kartu membentang penuh 100% dari kiri ke kanan:
```css
/* 1. Date Action Bar */
.mentee-date-action-bar {
    width: 100%;
    box-sizing: border-box;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
}

/* 2. Hero Card */
.mentee-hero-card {
    width: 100%;
    box-sizing: border-box;
    margin-bottom: 1.25rem;
}

/* 3. KPI Grid */
.mentee-kpi-summary-grid {
    width: 100%;
    box-sizing: border-box;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.25rem;
}

/* 4. Riwayat Penugasan Kanban */
.mentee-kanban-board-card {
    width: 100%;
    box-sizing: border-box;
}
```

### Langkah 3: Kompilasi Asset Vite
Jalankan perintah build frontend:
```bash
npm run build
```

### Langkah 4: Verifikasi Test Suite
Jalankan pengujian unit dan fitur:
```bash
& "F:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe" artisan test tests/Feature/PembimbingPortalStandardizationTest.php
```

---

## 5. Kriteria Penerimaan (Acceptance Criteria)

- [ ] Seluruh kartu di halaman Profil Peserta Bimbingan membentang rata dari batas kiri ke batas kanan workspace layar tanpa celah kosong (*dead space*) di pinggir kiri dan kanan.
- [ ] Garis tepi kiri dari Date Bar, Hero Card, KPI Grid (Progres Tugas), dan Riwayat Penugasan berada pada garis vertikal yang sama persis (rata kiri).
- [ ] Garis tepi kanan dari Date Bar (tombol Kembali), Hero Card, KPI Grid (kartu Logbook), dan Riwayat Penugasan berada pada garis vertikal yang sama persis (rata kanan).
- [ ] File Blade `show.blade.php` tidak mengalami penambahan inline style atau script.
- [ ] `npm run build` sukses 100%.
- [ ] Seluruh unit dan feature test PHPUnit lolos 100%.

