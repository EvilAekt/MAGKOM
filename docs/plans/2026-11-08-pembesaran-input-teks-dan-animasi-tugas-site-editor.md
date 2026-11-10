# Rencana Kerja: Pembesaran Ukuran Input Pengetikan & Penerapan Efek Animasi ala "Buat Tugas" pada Inspector Site Editor

**Tanggal:** 8 November 2026  
**Status:** COMPLETED (Terverifikasi 100% Lolos Test & Asset Build)  
**Dokumen Referensi:**
- Tangkapan Layar Pengguna: [Field Judul & Narasi yang Sempit](file:///C:/Users/galan/.gemini/antigravity/brain/52dc3389-7237-4677-b31a-a339848599df/.user_uploaded/media_1794102892845_c2ad5fbf.png)
- Acuan Animasi Sistem: [`resources/css/pages/pembimbing/tugas-create.css`](file:///G:/MAGKOM/resources/css/pages/pembimbing/tugas-create.css) (Baris 295–385 & 410–485)
- Desain Sistem: `docs/07-DESIGN-SYSTEM-MAGKOM.md`

---

## 1. Tujuan

Meningkatkan kenyamanan pengguna (UX) saat mengetik konten teks dan narasi pada panel samping (Inspector) WordPress Site Editor MAGKOM:
1. **Memperbesar Ukuran Input & Textarea:**
   - Memperbesar field input satu baris (`.wp-text-input`) dari yang semula sempit menjadi lebih tinggi (44px), padding lebih lega (`10px 14px`), dan ukuran teks lebih terbaca (14px).
   - Memperbesar area pengetikan paragraf naratif (`.wp-textarea-input`, seperti pada *"Ringkasan Alur Naratif"*, *"Persyaratan Umum"*, dan *"Dokumen Pendaftaran"*) dengan tinggi minimal yang nyaman (`min-height: 130px` hingga `160px`), jarak antar-baris proporsional (`line-height: 1.6`), serta scrollbar kustom yang halus sehingga tidak lagi terasa terhimpit.
2. **Penerapan Efek Animasi ala Modul "Buat Tugas":**
   - Menghadirkan animasi garis bawah fokus (*animated focus indicator bar*) yang mengembang halus dari tengah (`scaleX(0)` ke `scaleX(1)` dengan kurva `cubic-bezier(0.4, 0, 0.2, 1)`).
   - Transisi warna label dinamis saat field aktif (`focus-within` mengubah warna label menjadi biru Sleman `#2563EB`).
   - Efek pendaran fokus (*soft focus glow ring*) `0 0 0 3px rgba(37, 99, 235, 0.15)` dan transisi elevasi mikro yang memanjakan mata saat pengguna mulai mengetik.

---

## 2. Asumsi & Analisis Teknis

### Asumsi Kerja:
1. **Integritas Kontrak JavaScript Tetap Terjaga:**
   - Seluruh atribut `id` (seperti `#input-tahapan-heading`, `#input-tahapan-summary`), class form, dan atribut `data-field` pada `_inspector.blade.php` tidak boleh diubah karena digunakan langsung oleh event listener live-preview dan autosave di `inspector-controller.js` dan `editor-state.js`.
2. **Isolasi Styling di `resources/css/`:**
   - Efek animasi dan dimensi baru diimplementasikan secara terpusat pada `resources/css/pages/superadmin/site-editor.css`.
3. **Acuan Efek dari `tugas-create.css`:**
   - Di `tugas-create.css`, animasi input menggunakan konsep garis aksen bawah `::after` (`transform: scaleX(1); transition: transform 0.28s cubic-bezier(0.4, 0, 0.2, 1);`), background field `#E8EDF5` ke `#DDE3ED`, dan label transisi warna ke `#1A73E8`. Konsep ini diadaptasi ke gaya WordPress Inspector modern dengan background putih bersih `#FFFFFF` dan aksen biru Sleman `#2563EB`.

---

## 3. Evaluasi Pendekatan Teknis

| Parameter | Opsi A: Floating Label Murni ala Material | Opsi B: Modern Animated Outline & Underline Indicator ala "Buat Tugas" (Rekomendasi) |
| :--- | :--- | :--- |
| **Deskripsi** | Merombak total seluruh struktur HTML di dalam 827 baris `_inspector.blade.php` untuk menempatkan `<label>` di dalam container input agar melayang. | Mempertahankan struktur `<label>` di atas input dalam `.wp-field-group`, menambahkan pembungkus animasi garis fokus `::after` atau styling interaktif `focus-within`, dan memperbesar dimensi fisik input. |
| **Kelebihan** | Identik 100% dengan Google Classroom. | ✅ **Sangat ideal untuk panel samping selebar 350px.** Label tetap terbaca jelas tanpa memakan ruang vertikal di dalam kotak teks. Efek animasi garis bawah dan transisi label tetap hadir dengan mulus. Risiko regresi nol. |
| **Dampak Kode** | Risiko tinggi merusak selector JS dan event delegation. | **Aman & Elegan.** Cukup fokus pada pembaruan CSS dan penambahan minimal di CSS. |
| **Rekomendasi** | Kurang direkomendasikan untuk Inspector. | **DIREKOMENDASIKAN (Opsi B)**. |

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/css/pages/superadmin/site-editor.css` | **Modify** | 1. Memperbesar dimensi `.wp-text-input` (tinggi min 44px, font 14px, padding 10px 14px).<br>2. Memperbesar `.wp-textarea-input` (tinggi min 130px, font 14px, line-height 1.6, scrollbar halus).<br>3. Menerapkan efek animasi garis fokus `::after` pada container field dengan transisi `cubic-bezier(0.4, 0, 0.2, 1)`.<br>4. Menambahkan transisi warna label dinamis saat field dalam kondisi `:focus-within`.<br>5. Menambahkan efek pendaran fokus (*soft focus glow ring*) bernuansa biru primer `#2563EB`. |
| `resources/views/superadmin/site-editor/partials/_inspector.blade.php` | **Modify (Opsional)** | Menyesuaikan atribut default `rows="4"` hingga `rows="5"` pada textarea naratif agar secara native lebih tinggi bahkan sebelum CSS termuat. |
| `tests/Feature/LandingPage/FinalQualityAssuranceTest.php` | **Verify** | Memastikan pengujian feature tetap lolos 100%. |

---

## 5. Langkah Implementasi (Checklist)

### Fase 1: Perbesaran Dimensi Form Controls di `site-editor.css`
- [x] **Langkah 1.1:** Perbarui `.wp-text-input`:
  - `min-height: 46px;`
  - `font-size: 14px;`
  - `padding: 10px 14px;`
  - `border-radius: 8px;`
  - `font-weight: 500;`
- [x] **Langkah 1.2:** Perbarui `.wp-textarea-input`:
  - `min-height: 140px;` (khusus teks naratif dan persyaratan)
  - `font-size: 14px;`
  - `line-height: 1.6;`
  - `padding: 12px 14px;`
  - `resize: vertical;`
  - Custom scrollbar styling (lebar 6px, thumb membulat warna `#CBD5E1`, hover `#94A3B8`).
- [x] **Langkah 1.3:** Perbarui `.wp-select-input`:
  - `min-height: 46px;`
  - `font-size: 14px;`
  - `padding: 10px 14px;`

### Fase 2: Penerapan Efek Animasi ala "Buat Tugas"
- [x] **Langkah 2.1:** Tambahkan efek interaktif pada `.wp-field-group`:
  - `position: relative;`
  - Transisi label: `.wp-field-group:focus-within > label { color: #2563EB; font-weight: 600; transform: translateX(2px); transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1); }`
- [x] **Langkah 2.2:** Tambahkan animasi garis fokus bawah (*Expanding Underline Indicator*):
  - Menggunakan pseudo-elemen `::after` pada wrapper/input atau efek animasi border gradient yang mekar saat `:focus-within` dari tengah (`scaleX(0)` ke `scaleX(1)`).
- [x] **Langkah 2.3:** Tambahkan animasi pendaran (*Focus Glow Ring*):
  - `box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.14), 0 2px 4px rgba(0, 0, 0, 0.05);`
  - `border-color: #2563EB;`
  - `background-color: #FFFFFF;`
  - `transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);`

### Fase 3: Kompilasi & Pengujian
- [x] **Langkah 3.1:** Jalankan `npm run build` untuk mengompilasi CSS terbaru melalui Vite.
- [x] **Langkah 3.2:** Jalankan `.\vendor\bin\phpunit tests/Feature/LandingPage/FinalQualityAssuranceTest.php`.
- [x] **Langkah 3.3:** Jalankan `./vendor/bin/pint --test`.
- [x] **Langkah 3.4:** Sinkronisasi knowledge graph via `graphify update .`.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Text input dan textarea memiliki ukuran fisik yang lebih besar dan nyaman untuk pengetikan (font 14px, input min 46px, textarea min 140px).
- [x] Textarea narasi panjang (seperti Ringkasan Alur dan Persyaratan) tidak lagi terpotong sempit dan memiliki scrollbar halus.
- [x] Efek animasi fokus ala "Buat Tugas" aktif saat field diklik/fokus (garis aksen bawah mekar, label bertransisi warna biru, pendaran fokus lembut).
- [x] Seluruh fungsi data-binding, live-preview ke canvas, dan autosave tetap berjalan normal tanpa gangguan JavaScript.
- [x] Aset Vite berhasil terkompilasi tanpa error.
- [x] Seluruh unit & feature test tetap lolos 100%.

