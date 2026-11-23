# Plan: Penyempurnaan Copywriting Bar Pratinjau & Spacing Label Topbar Editor

Dokumen ini memuat rencana kerja terperinci untuk menyelesaikan dua isu antarmuka yang dilaporkan pengguna:
1. **SS 1**: Teks bar pratinjau yang terkesan sangat "AI/robotik" (kuning mentereng bertuliskan "MODE PRATINJAU INTERAKTIF" dan deskripsi "Draf Kontainer (Sandbox)").
2. **SS 2**: Label "Pilih Halaman" yang menempel terlalu rapat tanpa jarak/spasi dengan dropdown select "Landing Page (/)".

---

## 1. Analisis Akar Masalah & Rekomendasi Solusi

### Isu 1: Copywriting & Styling Bar Pratinjau (SS 1)
- **Kondisi Saat Ini**:
  - Badge: `• MODE PRATINJAU INTERAKTIF` dengan warna latar kuning/oranye menyala (`#f59e0b`) dan huruf kapital penuh (ALL CAPS).
  - Teks Deskripsi: *"Anda sedang melihat versi Draf Kontainer (Sandbox) yang belum terbit ke publik. Seluruh tombol, formasi kuota, dan tautan aktif untuk pengujian."*
  - **Kelemahan**: Terkesan seperti keluaran prompt AI atau jargon developer backend ("Sandbox", "Kontainer", "Interaktif"). Kurang mencerminkan aplikasi web resmi pemerintahan (SPBE/Dinas Kominfo).
- **Solusi Penyempurnaan**:
  - **Badge Teks**: Diubah menjadi `Pratinjau Draf` (Title Case yang tenang) dengan dot indikator berkedip lembut.
  - **Warna Badge**: Menggunakan aksen amber/gold halus bertema Sleman (`rgba(251, 191, 36, 0.14)` dengan teks `#fde68a` dan border halus `rgba(251, 191, 36, 0.35)`). Tidak lagi kuning mentereng/silau.
  - **Deskripsi**: Diubah menjadi kalimat alami, ringkas, dan resmi:
    *"Halaman ini menampilkan pratinjau draf yang belum diterbitkan ke publik."*
  - **Border Bar**: Border bawah dibuat halus dan elegan (`1px solid rgba(255, 255, 255, 0.12)`).
  - **Toast Notifikasi**: Kalimat toast diubah dari *"Draf berhasil disimpan ke Sandbox"* menjadi *"Draf berhasil disimpan."*

---

### Isu 2: Jarak Rapat Label "Pilih Halaman" dan Dropdown Select (SS 2)
- **Kondisi Saat Ini**:
  - Pada `_topbar.blade.php`:
    `<label for="select-canvas-page" class="sr-only">Pilih Halaman</label>`
  - Karena `.sr-only` belum terdefinisi di stylesheet `site-editor.css`, teks label tampil biasa di layar, dan pembungkus `.canvas-page-selector-wrap` tidak memiliki gap horizontal.
  - Akibatnya, teks `Pilih Halaman` menempel persis pada batas kiri kotak dropdown `[📄 Landing Page (/)]`.
- **Solusi Penyempurnaan**:
  - Beri class eksplisit `.canvas-page-selector-label` pada elemen `<label>`.
  - Beri styling tipografi yang pas: `font-size: 12px; font-weight: 600; color: #64748b; white-space: nowrap;`.
  - Tambahkan jarak `gap: 10px;` pada `.canvas-page-selector-wrap` agar ada ruang bernapas (breathing room) yang proporsional dan sedap dipandang.
  - Tambahkan definisi class utilitas `.sr-only` standar di `site-editor.css` sebagai *fail-safe*.

---

## 2. File yang Terlibat (Scope of Work)

1. `resources/views/public/partials/_preview_control_bar.blade.php`:
   Penyempurnaan teks badge dan deskripsi manusiawi.
2. `resources/css/pages/public/landing.css`:
   Penyempurnaan estetika badge, warna border bar pratinjau, dan kontras teks deskripsi.
3. `resources/views/superadmin/site-editor/partials/_topbar.blade.php`:
   Pemberian class eksplisit `.canvas-page-selector-label` pada label.
4. `resources/css/pages/superadmin/site-editor.css`:
   Pengaturan `gap: 10px` dan styling label `.canvas-page-selector-label` serta definisi `.sr-only`.
5. `resources/js/pages/superadmin/site-editor/editor-state.js` & `app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php`:
   Pembersihan kalimat toast dari kata "Sandbox".

---

## 3. Rencana Eksekusi & Quality Gate

1. **Implementasi Markup & Copywriting**: Perbarui Blade template dan controller.
2. **Implementasi CSS**: Perbarui stylesheet `landing.css` dan `site-editor.css`.
3. **Kompilasi Aset**: Jalankan `npm run build` untuk memverifikasi Vite bundling.
4. **Validasi Sintaks & Style**: Jalankan Laravel Pint (PSR-12) dan PHPStan.
5. **Pengujian Fungsional**: Jalankan PHPUnit feature tests untuk memastikan tidak ada efek samping.

