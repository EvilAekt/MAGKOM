# Rencana Kerja: Penyelarasan Warna Sidebar Dashboard Peserta dengan Super Admin

- **Tanggal Pembuatan**: 2026-11-06
- **Status**: Selesai Diimplementasikan (*Completed*)
- **Penanggung Jawab**: AI Pair Programmer (Antigravity)
- **Referensi Desain**: Dashboard Super Admin (`layouts.dashboard` / Gentelella Sleman Cockpit v4)

---

## 1. Tujuan
Menyelaraskan skema warna, tingkat kontras, hover/active state, dan tampilan visual sidebar pada dashboard peserta (`resources/views/layouts/peserta.blade.php`) agar identik 100% (*pixel & token parity*) dengan sidebar dashboard Super Admin (`resources/views/layouts/dashboard.blade.php`).

Hasil yang diharapkan:
- Sidebar peserta memiliki warna latar belakang (*background*), header emblem, label section, teks menu inaktif, animasi hover, active pill state, serta footer aksi cepat yang konsisten dengan tema Sleman Navy resmi Super Admin.
- Tampilan mode terang (*light mode*) dan mode gelap (*dark mode*) sinkron sempurna antara kedua panel.

---

## 2. Asumsi & Analisis Kode

### 2.1 Analisis Warna Eksisting
| Komponen Sidebar | Dashboard Super Admin (`dashboard.blade.php`) | Dashboard Peserta (`peserta.blade.php`) | Kebutuhan Penyelarasan |
| :--- | :--- | :--- | :--- |
| **Latar Belakang Base (Mode Terang)** | `#111C2E` (Dark Charcoal Slate Gentelella v4) | `#1B262C` (Gentelella Dark Slate) | Samakan ke **`#111C2E`** |
| **Latar Belakang Base (Mode Gelap)** | `#011C44` | `#011C44` | Sudah selaras (pertahankan) |
| **Garis Batas Kanan (*Right Border*)** | `1px solid rgba(50, 130, 184, 0.2)` (Terang)<br>`1px solid rgba(63, 81, 181, 0.2)` (Gelap) | `1px solid rgba(50, 130, 184, 0.2)` (Terang)<br>`1px solid rgba(63, 81, 181, 0.35)` (Gelap) | Samakan intensitas border dark mode ke `rgba(63, 81, 181, 0.2)` |
| **Header Brand Emblem** | `background: rgba(255, 255, 255, 0.08);`<br>`border: 1px solid rgba(255, 255, 255, 0.14);` | Masih memiliki fallback gradient `#0F4C75` di `.sidebar-brand-box` | Samakan background & border emblem ke token Super Admin |
| **Header Separator** | `border-bottom: 1px solid rgba(50, 130, 184, 0.2);`<br>`[data-theme="dark"]: rgba(255, 255, 255, 0.1);` | `border-bottom: 1px solid rgba(50, 130, 184, 0.2);`<br>`[data-theme="dark"]: #1F2937;` | Samakan border header dark mode ke `rgba(255, 255, 255, 0.1)` |
| **Nav Item Link Inaktif** | Text: `#BBBBBB`<br>Icon: `#AAAAAA` | Text: `#BBBBBB`<br>Icon: `#AAAAAA` | Sudah selaras (pertahankan) |
| **Nav Item Link Hover** | `background: rgba(255, 255, 255, 0.08);`<br>`color: #FFFFFF;`<br>`border: 1px solid rgba(255, 255, 255, 0.14);`<br>`border-radius: 8px;`<br>Icon: `#FFCB05` (Gold) | `background-color: rgba(255, 255, 255, 0.08);`<br>`color: #FFFFFF;`<br>`border: 1px solid rgba(255, 255, 255, 0.14);`<br>`border-radius: 8px;`<br>Icon: `#FFCB05` | Sudah selaras (pertahankan) |
| **Nav Item Link Active (Mode Terang)** | `background: #013D8D;`<br>`color: #FFFFFF;`<br>`border: 1px solid rgba(147, 197, 253, 0.35);`<br>Icon: `#FFCB05`;<br>Glow: `activePillBreathe` | `background: #013D8D;`<br>`color: #FFFFFF;`<br>`border: 1px solid rgba(147, 197, 253, 0.35);`<br>Icon: `#FFCB05`;<br>Glow: `activePillBreathe` | Sudah selaras (pertahankan) |
| **Nav Item Link Active (Mode Gelap)** | `background: #0E438A !important;`<br>`box-shadow: 0 4px 14px rgba(1, 30, 75, 0.5);` | Belum ada styling spesifik active pill di mode gelap | Tambahkan rule active pill mode gelap dengan `#0E438A` |
| **Sidebar Footer Toolbar** | Background: `rgba(0, 0, 0, 0.15)` (Terang) / `rgba(0, 0, 0, 0.25)` (Gelap)<br>Border-top: `1px solid rgba(255, 255, 255, 0.12)`<br>Btn circular 38px, hover `rgba(255, 255, 255, 0.18)` | Background: `rgba(0, 0, 0, 0.15)` (Terang) / `rgba(0, 0, 0, 0.25)` (Gelap)<br>Border-top: `1px solid rgba(255, 255, 255, 0.12)`<br>Btn circular 38px, hover `rgba(255, 255, 255, 0.18)` | Sudah selaras (pertahankan) |

---

## 3. Evaluasi Opsi Pendekatan Teknis

### Opsi 1: Penyelarasan Terfokus pada Token & Styling Sidebar Peserta (Direkomendasikan)
- **Cara Kerja**:
  1. Perbarui deklarasi warna dasar sidebar di `resources/views/layouts/peserta.blade.php` dari `#1B262C` menjadi `#111C2E` untuk menyamai SuperAdmin.
  2. Bersihkan fallback warna gradien lama (`#0F4C75`) pada brand emblem logo di header sidebar.
  3. Tambahkan override mode gelap untuk active navigation pill (`#0E438A`) dan selaraskan border header mode gelap ke `rgba(255, 255, 255, 0.1)`.
  4. Periksa dan selaraskan token di `resources/css/base/variables.css` jika ada referensi sidebar peserta yang menggunakan token lawas.
- **Kelebihan**:
  - Resiko regresi nol (*zero regression*).
  - Tidak mengubah struktur DOM, layout grid, maupun interaksi JavaScript toggle sidebar.
  - Tampilan langsung identik dan konsisten saat berpindah dari halaman admin ke peserta.
- **Kekurangan**:
  - Perlu memverifikasi tampilan pada mode terang dan mode gelap secara teliti.

### Opsi 2: Penggabungan Penuh CSS Sidebar ke Satu File Terpusat
- **Cara Kerja**: Memindahkan seluruh CSS sidebar kedua layout ke satu file stylesheet terpusat di `resources/css/layouts/sidebar-unified.css`.
- **Kelebihan**: Mengeliminasi duplikasi deklarasi CSS di Blade.
- **Kekurangan**: Berisiko tinggi (*high risk*) merusak spesifisitas CSS Gentelella pada layout SuperAdmin yang memiliki dependency kompleks dengan bootstrap legacy. Melanggar prinsip *minimal, focused change* di `AGENTS.md`.

> **Keputusan**: Menggunakan **Opsi 1** karena terfokus, aman, dan menjaga stabilitas sistem baseline.

---

## 4. Rincian Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/layouts/peserta.blade.php` | Modify | Update warna dasar sidebar `.sidebar, .dashboard-sidebar` menjadi `#111C2E`, perbaiki emblem logo, border header mode gelap, dan active pill mode gelap (`#0E438A`) |
| `resources/css/base/variables.css` | Modify | Pastikan `--sidebar-bg` dan token terkait mencerminkan `#111C2E` untuk konsistensi seluruh aplikasi |

---

## 5. Langkah Implementasi (Checklist)

- [x] **Langkah 1: Modifikasi Background & Border Sidebar Peserta**
  - Ubah deklarasi `.sidebar, .dashboard-sidebar` dari `background-color: #1B262C;` menjadi `background-color: #111C2E;` di [layouts/peserta.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/layouts/peserta.blade.php#L623).
  - Pastikan batas kanan sidebar pada mode gelap menggunakan `border-right: 1px solid rgba(63, 81, 181, 0.2) !important;`.

- [x] **Langkah 2: Penyelarasan Header Brand & Logo Emblem**
  - Bersihkan latar belakang gradien lama di `.sidebar-brand-box` agar menggunakan `rgba(255, 255, 255, 0.08)` dengan border `rgba(255, 255, 255, 0.14)` yang identik dengan `.sidebar-brand-emblem` SuperAdmin.
  - Perbarui border bawah `.sidebar-header` pada mode gelap menjadi `border-bottom-color: rgba(255, 255, 255, 0.1);`.

- [x] **Langkah 3: Penyelarasan Active Navigation Pill Mode Gelap**
  - Tambahkan rule `[data-theme="dark"] .nav-item.active, [data-theme="dark"] .nav-item-link.active` dengan background `#0E438A !important;` dan box shadow `0 4px 14px rgba(1, 30, 75, 0.5)`.

- [x] **Langkah 4: Verifikasi & Audit Token Global**
  - Periksa file [resources/css/base/variables.css](file:///d:/MAGKOM/MAGKOM/resources/css/base/variables.css) dan pastikan token `--sidebar-bg` selaras dengan `#111C2E`.

- [x] **Langkah 5: Quality Gate & Visual Testing**
  - Jalankan `npm run build` untuk memverifikasi pipeline Vite tidak ada error.
  - Jalankan PHPUnit test terkait untuk memastikan tidak ada dampak pada backend/auth: `php artisan test --filter=PendaftaranPesertaTest`.
  - Lakukan pengecekan visual di browser pada dashboard peserta (`http://127.0.0.1:8000/peserta/dashboard`) baik mode terang maupun mode gelap.

---

## 6. Strategi Testing

1. **Pengujian Visual (Side-by-Side Comparison)**:
   - Bandingkan tampilan sidebar pada URL:
     - Super Admin: `http://127.0.0.1:8000/admin/dashboard`
     - Peserta: `http://127.0.0.1:8000/peserta/dashboard`
   - Pastikan warna latar `#111C2E` identik tanpa ada perbedaan kontras.
   - Uji transisi tema (Light Mode <-> Dark Mode) melalui tombol di toolbar footer sidebar.
2. **Pengujian Fungsional & Interaksi**:
   - Uji klik menu aktif dan hover state: pastikan ikon emas (`#FFCB05`), teks putih, dan animasi pernapasan (*breathing animation*) tetap bekerja normal.
   - Uji toggle collapse sidebar (mode mobile drawer maupun desktop collapse).
3. **Automated Test Suite**:
   - Jalankan `vendor/bin/pint --test` untuk memastikan kebersihan format kode.
   - Jalankan `php artisan test` pada modul autentikasi dan pendaftaran.

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Kontras Teks Terhadap Background Baru** | Rendah | Warna `#111C2E` sedikit lebih gelap dan pekat dari `#1B262C`, sehingga rasio kontras teks `#BBBBBB` dan `#FFFFFF` justru meningkat (melebihi standar WCAG 2.2 AA). |
| **Perbedaan Tampilan di Monitor / Layar Tertentu** | Rendah | Menggunakan kode warna hex absolut (`#111C2E`) yang sudah terbukti stabil pada Super Admin sejak baseline rilis sebelumnya. |
| **Potensi Gangguan pada Skrip Toggle Sidebar** | Nol | Hanya memodifikasi nilai warna CSS, tidak mengubah atribut ID, kelas JS, atau event handler. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Warna latar belakang sidebar dashboard peserta berubah menjadi `#111C2E` pada mode terang.
- [x] Warna latar belakang sidebar peserta pada mode gelap tetap konsisten `#011C44`.
- [x] Brand emblem logo di header sidebar selaras dengan Super Admin (tidak ada sisa gradien usang).
- [x] Active pill navigation di mode terang (`#013D8D`) dan mode gelap (`#0E438A`) identik dengan Super Admin.
- [x] Tidak ada error build Vite (`npm run build`).
- [x] Seluruh unit/feature test terkait tetap berstatus PASS.

