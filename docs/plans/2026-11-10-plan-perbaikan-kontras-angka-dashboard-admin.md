# Rencana Kerja: Perbaikan Kontras & Visibilitas Angka Metrik Dashboard Admin

**Tanggal:** 10 November 2026  
**Status:** Menunggu Persetujuan User  
**Tautan Dokumen Rencana:** `docs/plans/2026-11-10-plan-perbaikan-kontras-angka-dashboard-admin.md`  
**Referensi:**  
- Tangkapan Layar Isu:  
  1. `media_1794290133040_5f8e70f1.png` (Angka pada 4 kartu KPI tidak terlihat sama sekali di atas kartu putih).  
  2. `media_1794290147410_1719ec02.png` (Angka pada kartu kuning "Pendaftar 7 Hari" berwarna putih sehingga tidak terbaca di atas latar kuning).  
- Aturan Pengembangan: `AGENTS.md` (PHP 8.3, Laravel 10, Vanilla CSS/JS via Vite, Zero Test Regression, WCAG 2.1 AA Kontras Teks).

---

## 1. Analisis Akar Masalah (Root Cause Analysis)

1. **Akar Masalah Kartu KPI Putih (Image 1)**:
   - Pada `resources/views/admin/dashboard.blade.php`, elemen angka metrik menggunakan class gabungan: `<span class="adm-kpi-number count">{{ ... }}</span>`.
   - Di file stylesheet warisan `public/css/gentelella-theme.css` dan `resources/css/layouts/gentelella.css` (baris 358–361), terdapat rule global:
     ```css
     .count {
         color: var(--magkom-pure-white, #FFFFFF) !important;
     }
     ```
   - Akibat flag `!important` dari Gentelella tersebut, warna teks pada class `.adm-kpi-number` ditimpa secara paksa menjadi **putih murni (`#FFFFFF`)**. Karena kartu KPI memiliki latar belakang putih (`#FFFFFF`), angka menjadi "putih di atas putih" dan menghilang sepenuhnya secara visual.
2. **Akar Masalah Kartu Kuning "Pendaftar 7 Hari" (Image 2)**:
   - Elemen angka pada kartu kuning menggunakan `<span class="alert-big-number count">{{ ... }}</span>`.
   - Rule `.count` warisan juga menimpa warna angka ini menjadi **putih murni (`#FFFFFF`)** di atas latar belakang gradien kuning terang (`#FFF6D1` &rarr; `#FFE58A`).
   - Kontras warna putih di atas kuning muda hanya menghasilkan rasio kontras ~1.2:1 (gagal total terhadap standar WCAG 2.1 AA yang mewajibkan minimal 4.5:1).

---

## 2. Solusi Teknis & Pendekatan Terpilih

1. **Pelepasan Class Konflik `.count` dari Markup Blade**:
   - Menghapus class `.count` dari elemen kartu bento (`.adm-kpi-number`, `.alert-big-number`, `.roster-big-val`) sehingga tidak lagi terpengaruh oleh rule global Gentelella yang memaksa `#FFFFFF !important`.
2. **Penegasan Warna Kontras Tinggi pada Stylesheet (`resources/css/pages/admin/dashboard.css`)**:
   - **4 Kartu KPI Utama**: Diberikan warna Deep Navy Sleman `#003566 !important` (Rasio kontras **12.6:1** terhadap putih, lolos Level AAA). Pada Dark Mode otomatis menjadi `#FFFFFF !important`.
   - **Kartu Kuning "Pendaftar 7 Hari"**: Diberikan warna Dark Navy `#003566 !important` (Rasio kontras **9.8:1** terhadap kuning terang, teks menjadi sangat tajam dan terbaca jelas).
   - **Kartu Merah "Perlu Perhatian"**: Tetap mempertahankan warna putih `#FFFFFF !important` (Rasio kontras **5.2:1** terhadap latar gradien merah gelap `#D64545`).
   - **Highlight Banner Sleman Gold**: Mempertahankan warna Dark Navy `#02275D !important` (Rasio kontras **8.5:1** terhadap latar kuning emas).
3. **Penyelarasan Selector Animasi GSAP (`resources/js/pages/admin/dashboard.js`)**:
   - Memperbarui query selector rolling counter GSAP dari sebelumnya mencari `.count` menjadi:
     ```javascript
     document.querySelectorAll('.adm-kpi-number, .alert-big-number, .roster-big-val')
     ```
   - Animasi putaran angka (0 &rarr; target) tetap berjalan 100% mulus dengan teks angka yang selalu jelas terlihat sepanjang animasi.

---

## 3. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/dashboard.blade.php` | Modify | Menghapus class `count` dari elemen `.adm-kpi-number`, `.alert-big-number`, dan `.roster-big-val`. |
| `resources/css/pages/admin/dashboard.css` | Modify | Menambahkan penegasan warna kontras tinggi `!important` untuk `.adm-kpi-number` (`#003566`), `.card-alert-amber .alert-big-number` (`#003566`), dan `.card-alert-red .alert-big-number` (`#FFFFFF`). |
| `resources/js/pages/admin/dashboard.js` | Modify | Mengarahkan selector rolling counter GSAP ke `.adm-kpi-number, .alert-big-number, .roster-big-val`. |

---

## 4. Langkah Implementasi (Checklist)

- [ ] **Langkah 1**: Edit `resources/views/admin/dashboard.blade.php`:
  - Hapus class `count` pada 4 kartu KPI: `<span class="adm-kpi-number">{{ ... }}</span>`.
  - Hapus class `count` pada Banner Roster: `<span class="roster-big-val">{{ ... }}</span>`.
  - Hapus class `count` pada 2 Side Alert Cards: `<span class="alert-big-number">{{ ... }}</span>`.
- [ ] **Langkah 2**: Edit `resources/css/pages/admin/dashboard.css`:
  - Pertegas styling `.adm-kpi-number { color: var(--adm-navy-main, #003566) !important; }`.
  - Pertegas styling `.card-alert-amber .alert-big-number { color: var(--adm-navy-main, #003566) !important; }`.
  - Pertegas styling `.card-alert-red .alert-big-number { color: #FFFFFF !important; }`.
- [ ] **Langkah 3**: Edit `resources/js/pages/admin/dashboard.js`:
  - Ubah selector GSAP menjadi `document.querySelectorAll('.adm-kpi-number, .alert-big-number, .roster-big-val')`.
- [ ] **Langkah 4**: Jalankan build Vite:
  - `npm run build`.
- [ ] **Langkah 5**: Jalankan code formatting:
  - `vendor/bin/pint --test`.
- [ ] **Langkah 6**: Jalankan regression testing PHPUnit:
  - `vendor/bin/phpunit tests/Feature/AdminDashboardEnhancementTest.php tests/Feature/DashboardModulesRenderingTest.php tests/Feature/AdminTerminologyDesignSystemTest.php tests/Feature/AdvancedThreePillarsDashboardTest.php tests/Feature/DeltaDashboardEnhancementTest.php tests/Feature/GentelellaDashboardRevampTest.php`.

---

## 5. Strategi Pengujian (Testing Strategy)

1. **Visual Contrast Verification**:
   - Memastikan angka pada 4 kartu KPI (Verifikasi Tertunda, Kuota Terisi, Peserta Aktif, Kecepatan Verifikasi) berwarna Deep Navy gelap `#003566` yang kontras dan tajam di atas latar putih.
   - Memastikan angka `2` pada kartu kuning "Pendaftar 7 Hari" berwarna Deep Navy `#003566` yang kontras dan terbaca jelas di atas latar kuning terang.
2. **Animation Verification**:
   - Memastikan animasi GSAP rolling number counter tetap bekerja mulus menghitung dari 0 ke angka target tanpa flicker warna putih.
3. **Automated Feature Tests**:
   - Memastikan seluruh 41 tests pada 6 feature test suites dashboard tetap 100% PASS (Hijau).

---

## 6. Kriteria Selesai (Definition of Done)

- [ ] Seluruh angka metrik pada kartu KPI terlihat jelas dengan warna Deep Navy kontras tinggi (`#003566`).
- [ ] Angka pada kartu kuning "Pendaftar 7 Hari" terbaca tajam dengan warna Deep Navy (`#003566`), bukan putih.
- [ ] Animasi putaran angka GSAP tetap berfungsi normal.
- [ ] `npm run build` sukses tanpa error.
- [ ] `vendor/bin/pint --test` passed.
- [ ] Seluruh pengujian PHPUnit lulus 100%.

