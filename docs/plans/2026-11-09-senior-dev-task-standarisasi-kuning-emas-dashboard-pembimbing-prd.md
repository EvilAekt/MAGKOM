# Senior Developer Task Brief (PRD & Implementation Plan)
## Penyelarasan Warna Kuning Profil Peserta Bimbingan Mengikuti Kuning Emas Sleman Dashboard Pembimbing

- **Target Persona Pelaksana**: Junior Developer / AI Model (Gemini 3.8 Flash / Claude Haiku / Cursor)
- **Modul**: Pembimbing Portal — Halaman Profil 360° Peserta Bimbingan (`/pembimbing/peserta/{id}`)
- **Dokumen Referensi**: Mockup Roster Dashboard `media_1794223627514_342a7944.png`, `resources/css/pages/pembimbing/dashboard.css`, PRD MagKom v1.1
- **Tanggal Dibuat**: 09 November 2026
- **Status**: Ready for Implementation (Awaiting Confirmation)

---

## 1. Executive Summary & Problem Statement

### 1.1 Latar Belakang Masalah
Saat ini, halaman **Profil Peserta Bimbingan** (`/pembimbing/peserta/{id}`) menggunakan token warna kuning berbasis Tailwind Amber (`#F59E0B`), yang memiliki nuansa agak kemerahan/oranye (*orange-amber*). 

Sementara itu, pada halaman utama **Dashboard Pembimbing** (`/pembimbing/dashboard`), kartu sorotan utama *Highlight Roster Banner* (`.bento-highlight-card` "14/19 PESERTA BIMBINGAN") menggunakan **Kuning Emas Sleman Resmi (`#FFC300`)** dengan variasi terang `#FFD84D`.

Pengguna secara eksplisit meminta agar seluruh warna kuning pada halaman **Profil Peserta Bimbingan** diselaraskan dan disamakan 100% dengan warna kuning yang ada di Dashboard Pembimbing (`#FFC300`), sehingga pengalaman visual di seluruh Portal Pembimbing menjadi konsisten (*Single Source of Truth*).

### 1.2 Target Solusi
Mengubah variabel CSS token kuning di `resources/css/pages/pembimbing/peserta-show.css` dari `#F59E0B` menjadi **`#FFC300`** (Kuning Emas Sleman), memperbarui varian gelap/hover menjadi **`#E6B000`**, serta memperbarui sebaran *box-shadow glow* menjadi `rgba(255, 195, 0, ...)`.

---

## 2. Inventaris Token Warna (Old vs New)

| Token Desain | Nilai Saat Ini (Tailwind Amber) | Nilai Target (Kuning Emas Sleman Dashboard) | Keterangan Penggunaan |
| :--- | :--- | :--- | :--- |
| `--mentee-amber` | `#F59E0B` (Oranye) | **`#FFC300`** (Kuning Emas) | Warna kuning utama untuk seluruh kartu, tombol, badge, dan ring |
| `--mentee-amber-vibrant` | `#F59E0B` | **`#FFC300`** | Varian vibrant identik dengan token utama |
| `--mentee-amber-dark` | `#D97706` | **`#E6B000`** | Warna state hover tombol dan elemen interaktif |
| `--mentee-amber-bright` | (Belum ada) | **`#FFD84D`** | Varian highlight / gradien terang dari dashboard |
| **Glow Box Shadow** | `rgba(245, 158, 11, X)` | **`rgba(255, 195, 0, X)`** | Pendaran cahaya neon disesuaikan dengan spektrum emas |

---

## 3. Daftar Elemen Profil 360° yang Otomatis Berubah Selaras

Karena arsitektur CSS di `peserta-show.css` sudah modular dan terpusat pada variabel CSS token, pembaruan variabel `--mentee-amber` akan secara otomatis dan konsisten mengubah:

1. **Top Action Strip**: Kapsul penanggalan `.date-pill-amber`.
2. **Mentee Hero Card**:
   - Cincin neon avatar `.mentee-avatar-gold-ring` beserta teks inisialnya.
   - Tombol kapsul aksi `.btn-pill-tugas` ([Tugas]).
   - Border pill badge `.badge-jalur-outline` ([Jalur Umum]).
   - Judul header grid `.data-peserta-header` ("DATA PESERTA").
3. **Kartu KPI Baris 1**:
   - **Progres Tugas**: Segmen donat selesai dan dot indikator `.dot-selesai`.
   - **Periode Magang**: Latar belakang kartu utama `.kpi-card-yellow` dan bilah waktu.
4. **Riwayat Penugasan (Kanban Board)**:
   - Tombol `.btn-kelola-tugas` ("Kelola Tugas $\rightarrow$").
   - Dot indikator legenda `.dot-kanban-proses`.
   - Bilah segmen tengah `.segment-proses`.
   - Dot indikator kolom pill `.status-indicator-dot.dot-proses`.
   - Seluruh kartu tugas dalam pengerjaan `.card-style-proses`.
   - Pita aksen atas kartu selesai `.card-style-selesai::before`.

---

## 4. Strict Guardrails & Aturan Repositori

1. **Blade Read-Only**: File `resources/views/pembimbing/peserta/show.blade.php` **TIDAK PERLU DIUBAH SAMA SEKALI**.
2. **Satu Sumber Perubahan**: Seluruh perubahan terisolasi murni pada `resources/css/pages/pembimbing/peserta-show.css`.
3. **Asset Compilation**: Wajib menjalankan `npm run build` setelah perubahan CSS.
4. **Verifikasi Test Suite**: Jalankan `tests/Feature/PembimbingPortalStandardizationTest.php` dengan runtime PHP 8.3 aktual.

---

## 5. Instruksi Langkah Demi Langkah untuk Junior Developer / AI

### Langkah 1: Perbarui Variabel CSS Token di `peserta-show.css`
Buka file `resources/css/pages/pembimbing/peserta-show.css` pada blok `:root` (baris 6–10), perbarui:

```css
:root {
    --mentee-amber: #FFC300;
    --mentee-amber-vibrant: #FFC300;
    --mentee-amber-bright: #FFD84D;
    --mentee-amber-dark: #E6B000;
    --mentee-navy-card: #0F3875;
    --mentee-navy-deep: #0F2C59;
    --mentee-navy-darker: #0A1E3F;
    --mentee-kpi-blue: #28487A;
    --mentee-kpi-badge: #4E6992;
    --mentee-royal-blue: #2563EB;
    --mentee-danger: #EF4444;
    --mentee-success: #10B981;
    --mentee-surface: #FFFFFF;
    --mentee-canvas-bg: #E8F1FA;
    --mentee-gray-track: #F1F5F9;
    --mentee-border: #DCE3EC;
    --mentee-text-navy: #0F172A;
    --mentee-text-slate: #1E293B;
    --mentee-text-muted: #64748B;
    --mentee-text-light-blue: #93C5FD;
}
```

### Langkah 2: Perbarui Nilai `rgba(245, 158, 11, ...)` Menjadi `rgba(255, 195, 0, ...)`
Cari dan ganti efek pendaran cahaya (*glow effect*) pada 4 selektor:
1. `.date-pill-amber`: `box-shadow: 0 2px 6px rgba(255, 195, 0, 0.2);`
2. `.mentee-avatar-gold-ring`: `box-shadow: 0 0 24px rgba(255, 195, 0, 0.35);`
3. `.btn-pill-tugas:hover`: `box-shadow: 0 4px 14px rgba(255, 195, 0, 0.35);`
4. `.kpi-card-yellow`: `box-shadow: 0 6px 18px rgba(255, 195, 0, 0.25);`

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

## 6. Kriteria Penerimaan (Acceptance Criteria)

- [ ] Seluruh elemen berwarna kuning di halaman Profil Peserta Bimbingan menggunakan warna `#FFC300` (Kuning Emas Sleman), identik dengan kartu bento dashboard pembimbing.
- [ ] Efek pendaran bayangan (*box-shadow*) memancarkan warna emas `rgba(255, 195, 0, ...)`.
- [ ] State hover pada tombol kuning menggunakan `#E6B000`.
- [ ] File Blade `show.blade.php` tidak mengalami penambahan inline style atau script.
- [ ] `npm run build` sukses 100%.
- [ ] Seluruh unit dan feature test PHPUnit lolos 100%.

