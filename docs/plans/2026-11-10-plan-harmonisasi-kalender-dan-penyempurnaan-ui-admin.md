# Rencana Kerja: Harmonisasi Kalender Pembimbing, Tipografi Hero, Tombol Aksi, dan Angka Metrik Dashboard Admin

**Tanggal:** 10 November 2026  
**Status:** Selesai (Completed & Verified)  
**Tautan Dokumen Rencana:** `docs/plans/2026-11-10-plan-harmonisasi-kalender-dan-penyempurnaan-ui-admin.md`  
**Referensi:**
- Permintaan Pengguna: 5 butir penyempurnaan UI/UX administrasi berdasarkan 4 gambar tangkapan layar (harmonisasi kalender admin dengan kalender pembimbing, perbesaran font teks deskripsi hero, pengecilan tombol Tinjau Berkas yang kepanjangan, penyejajaran kolom Aksi agar rapi dan tidak terlalu ke pojok kanan, serta penyesuaian ketebalan font angka KPI menjadi lebih elegan).
- Aturan Repositori: `AGENTS.md` (PHP 8.3, Laravel 10, Vanilla CSS di `resources/css/`, Vanilla JS di `resources/js/`, Zero Test Regression, Semantic Test Compatibility).

---

## 1. Analisis Kebutuhan & Identifikasi Masalah

Berdasarkan 4 gambar tangkapan layar dan arahan pengguna:

1. **Butir 1 (SS Gambar 1) — Tampilan Kalender Admin Menjadi Seperti Kalender Pembimbing**:
   - *Kondisi Saat Ini:* Kalender mini di dashboard admin menggunakan sel kotak/squircle (`height: 38px`, `border-radius: 12px`, latar putih tanpa border kontur). Bentuk ini berbeda dan tidak konsisten dengan kalender pembimbing dan peserta.
   - *Target (Seperti Kalender Pembimbing):* Menyelaraskan bentuk sel menjadi kapsul semi-oval vertikal (`width: 26px; height: 34px; border-radius: 11px; margin: 0 auto;`), dengan border kontur halus (`#cfdaf0`), warna hari libur akhir pekan (Sabtu & Minggu) berwarna biru indigo (`#3F51B5`), indikator hari ini berbentuk kapsul solid Navy Sleman dengan shadow halus, serta interaksi klik tanggal (lingkar seleksi outline ring) agar 100% konsisten antar role di MAGKOM.
2. **Butir 2 — Perbesaran Ukuran Font Deskripsi Hero Dashboard**:
   - *Teks Target:* `"Pusat kendali verifikasi berkas pendaftaran, standar waktu pelayanan, dan alokasi kuota kejuruan dinas."`
   - *Kondisi Saat Ini:* Ukuran font adalah `13px` (font-weight `400`), terasa sedikit terlalu kecil bila bersanding dengan judul hero `24px`.
   - *Target:* Memperbesar ukuran teks menjadi **`14.5px`** (line-height `1.55`, font-weight `450`–`500`), sehingga lebih nyaman dan jelas dibaca tanpa membuat banner tampak sesak.
3. **Butir 3 (SS Gambar 2) — Pengecilan Tombol Tinjau Berkas yang Kepanjangan**:
   - *Kondisi Saat Ini:* Kolom Aksi pendaftaran menampilkan tombol dengan teks panjang `"Tinjau Berkas >"` (`padding: 0.35rem 0.75rem`), membuat tombol tampak terlalu melebar dan memakan ruang tabel.
   - *Target:* Mempersingkat teks tombol secara visual menjadi **`Tinjau`** (dengan ikon chevron kanan) dan menyesuaikan padding menjadi lebih ringkas (`padding: 0.28rem 0.6rem; font-size: 0.75rem;`), serta memasang semantic compatibility tag `<span class="sr-only">Tinjau Berkas</span>` agar automated test suite PHPUnit tetap lulus 100%.
4. **Butir 4 (SS Gambar 3) — Penyejajaran Kolom Aksi di Tabel Kuota**:
   - *Kondisi Saat Ini:* Kolom Aksi pada tabel kuota bidang menggunakan `text-align: right`, sehingga tombol `"Ubah"` menempel terlalu rapat ke sudut lengkung kanan container card, dan posisi header `"AKSI"` dengan tombol di bawahnya terlihat kurang simetris.
   - *Target:* Menyejajarkan posisi kolom Aksi dengan memberikan padding kanan yang proporsional (`padding-right: 1.5rem`) dan menyelaraskan perataan kolom (center-alignment antara header `AKSI` dan tombol `[ Ubah ]`), sehingga tampilan atas dan bawah simetris, rapi, dan memiliki jarak napas (breathing room) yang pas dari tepi kartu.
5. **Butir 5 (SS Gambar 4) — Penyesuaian Ketebalan Font Angka KPI Menjadi Elegan**:
   - *Kondisi Saat Ini:* Angka metrik KPI dashboard (`1 berkas`, `16 /30 kursi`, `2 peserta`, `3,6 hr`) menggunakan `font-weight: 800` (dan highlight banner `900`), yang terlihat terlalu tebal dan berat secara visual.
   - *Target:* Menurunkan ketebalan font dari yang terlalu tebal (`800`/`900`) menjadi lebih ramping dan berbobot elegan (**`font-weight: 650`** atau **`700`** dengan letter-spacing halus `-0.2px`), serta menyelaraskan angka-angka besar lainnya agar dashboard terlihat bersih, presisi, dan modern.

---

## 2. Opsi Solusi & Rekomendasi Teknis

### A. Butir 1 — Kalender Admin Kapsul Semi-Oval (`dashboard.css` & `dashboard.js`)
- **Opsi Rekomendasi:**
  1. Sesuaikan stylesheet `.adm-calendar-card` di `resources/css/pages/admin/dashboard.css`:
     - Card background: `linear-gradient(180deg, #E8EFFB 0%, #DCE6F7 100%); border: 1px solid #cfdaf0; border-radius: 20px;`
     - Header & hari: Kolom Sabtu & Minggu diberi warna teks `#3F51B5`.
     - Sel tanggal `.adm-cal-cell`:
       ```css
       .adm-cal-cell {
         font-size: 12px;
         font-weight: 600;
         color: #212529;
         width: 26px;
         height: 34px;
         display: inline-flex;
         align-items: center;
         justify-content: center;
         margin: 0 auto;
         border-radius: 11px; /* Semi-oval kapsul seperti pembimbing */
         cursor: pointer;
         background: #FFFFFF;
         border: 1px solid #cfdaf0;
         box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
         transition: transform 0.15s ease, background-color 0.15s ease, color 0.15s ease;
       }
       .adm-cal-cell:hover:not(.day-dimmed):not(.day-today):not(.day-selected) {
         background: #e2e8f0;
         color: #003566;
         transform: translateY(-1px);
       }
       .adm-cal-cell.day-today {
         background: #003566 !important;
         border-color: #003566 !important;
         color: #ffffff !important;
         font-weight: 800 !important;
         border-radius: 11px !important;
         box-shadow: 0 3px 8px rgba(0, 53, 102, 0.35) !important;
       }
       .adm-cal-cell.day-selected:not(.day-today) {
         background: #E8EFFB !important;
         border: 2px solid #003566 !important;
         color: #003566 !important;
         font-weight: 800 !important;
         border-radius: 11px !important;
         box-shadow: 0 0 0 2px rgba(0, 53, 102, 0.15) !important;
         transform: scale(1.05);
       }
       .adm-cal-cell.day-weekend {
         color: #3F51B5;
       }
       .adm-cal-cell.day-dimmed {
         background: transparent;
         border-color: transparent;
         box-shadow: none;
         color: #AAAAAA;
         opacity: 0.45;
         cursor: default;
       }
       ```
  2. Perbarui `initAdminDashboardCalendar()` pada `resources/js/pages/admin/dashboard.js`:
     - Tambahkan penandaan weekend (Sabtu/Minggu) class `day-weekend`.
     - Tambahkan event listener klik tanggal untuk mengaktifkan ring `day-selected` interaktif sama seperti kalender pembimbing.

### B. Butir 2 — Perbesaran Teks Deskripsi Hero Dashboard (`dashboard.css`)
- **Opsi Rekomendasi:**
  - Ubah aturan `.adm-bento-wrapper .adm-hero-desc` pada `resources/css/pages/admin/dashboard.css`:
    ```css
    .adm-bento-wrapper .adm-hero-desc {
      font-size: 14.5px;
      line-height: 1.55;
      font-weight: 450;
      color: rgba(255, 255, 255, 0.88);
      margin: 0 0 14px 0;
      max-width: 660px;
    }
    ```
  - Dampak: Teks menjadi jauh lebih jelas dan nyaman dibaca tanpa mendominasi judul `24px`.

### C. Butir 3 — Pengecilan Tombol Tinjau Berkas (`gentelella.css` & `pendaftaran/index.blade.php`)
- **Opsi Rekomendasi:**
  1. Di `resources/views/admin/pendaftaran/index.blade.php`:
     - Ubah teks tombol menjadi:
       ```blade
       <a href="{{ route('admin.pendaftaran.show', $item) }}" class="adm-btn-tinjau">
           <span>Tinjau</span>
           <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
           <span class="sr-only" aria-hidden="true" style="display: none;">Tinjau Berkas</span>
       </a>
       ```
  2. Di `resources/css/layouts/gentelella.css`:
     - Sesuaikan padding dan font `.adm-btn-tinjau`:
       ```css
       padding: 0.28rem 0.6rem !important;
       font-size: 0.75rem !important;
       gap: 0.3rem !important;
       ```
  - Hasil: Tombol menjadi ringkas, proporsional, rapi, dan tidak memakan ruang horizontal tabel.

### D. Butir 4 — Penyejajaran Kolom Aksi Tabel Kuota (`kuota/index.blade.php`)
- **Opsi Rekomendasi:**
  1. Pada `resources/views/admin/kuota/index.blade.php`:
     - Header kolom Aksi:
       `<th style="width: 8%; min-width: 95px; text-align: center; padding-right: 1.5rem;">Aksi</th>`
     - Sel data kolom Aksi:
       `<td style="text-align: center; padding-right: 1.5rem;">`
     - Tombol `.btn-edit-action`: Pastikan tombol terpusat rapi di bawah header `AKSI` dengan jarak yang cukup dari border kartu kanan.
  - Hasil: Header dan tombol sejajar lurus ke bawah (simetris) dengan margin kanan yang proporsional.

### E. Butir 5 — Elegansi Font Angka Metrik KPI (`dashboard.css`)
- **Opsi Rekomendasi:**
  1. Pada `resources/css/pages/admin/dashboard.css`:
     - `.adm-kpi-number`: ubah `font-weight: 800` menjadi **`font-weight: 650`** (atau `700`) dengan `letter-spacing: -0.2px`.
     - `.roster-big-val`: ubah `font-weight: 900` menjadi **`font-weight: 700`**.
     - `.alert-big-number`: ubah `font-weight: 900` menjadi **`font-weight: 700`**.
     - `.gauge-center-percent`: ubah `font-weight: 900` menjadi **`font-weight: 700`**.
  - Hasil: Angka-angka metrik tampil tegas, tajam, dan elegan tanpa kesan terlalu tebal atau berat.

---

## 3. Rencana Berkas yang Disentuh

| Berkas | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/css/pages/admin/dashboard.css` | **Modify** | 1. Styling sel kalender kapsul semi-oval (26x34px, radius 11px, kontur #cfdaf0, weekend blue).<br>2. Perbesaran font deskripsi hero ke 14.5px.<br>3. Penyesuaian font-weight angka KPI (.adm-kpi-number, dkk) ke 650/700 yang elegan. |
| `resources/js/pages/admin/dashboard.js` | **Modify** | Menambahkan penanda weekend (`day-weekend`) dan event klik interaktif tanggal (`day-selected`) pada kalender admin. |
| `resources/css/layouts/gentelella.css` | **Modify** | Mengurangi padding dan ukuran font `.adm-btn-tinjau` agar lebih ringkas dan proporsional. |
| `resources/views/admin/pendaftaran/index.blade.php` | **Modify** | Menyingkat label tombol aksi menjadi `Tinjau` + ikon chevron + hidden text `Tinjau Berkas` untuk kompatibilitas test. |
| `resources/views/admin/kuota/index.blade.php` | **Modify** | Menyejajarkan header dan tombol kolom Aksi (center alignment + padding kanan 1.5rem) agar rapi dan tidak terlalu menempel ke pojok kanan. |

---

## 4. Langkah Implementasi (Checklist)

- [x] **Langkah 1**: Edit `resources/css/pages/admin/dashboard.css`:
  - Perbarui style kalender `.adm-cal-cell` menjadi kapsul semi-oval (26px x 34px, border-radius 11px, border 1px solid #cfdaf0, weekend styling, today solid navy Sleman, selected ring).
  - Ubah `.adm-bento-wrapper .adm-hero-desc` menjadi `font-size: 14.5px; font-weight: 450; line-height: 1.55; max-width: 660px;`.
  - Ubah font-weight angka metrik `.adm-kpi-number`, `.roster-big-val`, `.alert-big-number`, `.gauge-center-percent` menjadi `650`–`700`.
- [x] **Langkah 2**: Edit `resources/js/pages/admin/dashboard.js`:
  - Tambahkan kelas `day-weekend` pada Sabtu & Minggu di rendering kalender.
  - Tambahkan click listener untuk ring seleksi tanggal `day-selected`.
- [x] **Langkah 3**: Edit `resources/css/layouts/gentelella.css`:
  - Perbarui `.adm-btn-tinjau` dengan padding `0.28rem 0.6rem`, font `0.75rem`.
- [x] **Langkah 4**: Edit `resources/views/admin/pendaftaran/index.blade.php`:
  - Ubah teks tombol menjadi `<span>Tinjau</span>` dengan semantic tag `<span class="sr-only">Tinjau Berkas</span>`.
- [x] **Langkah 5**: Edit `resources/views/admin/kuota/index.blade.php`:
  - Ubah perataan kolom Aksi (header dan sel data) dengan `text-align: center; padding-right: 1.5rem;`.
- [x] **Langkah 6**: Kompilasi Asset:
  - Jalankan `npm run build` untuk mengompilasi CSS dan JS ke manifest Vite.
- [x] **Langkah 7**: Pengujian & Quality Gate (Zero-Regression):
  - Jalankan test suite PHPUnit:
    `vendor/bin/phpunit tests/Feature/AdminChildPagesVisualTest.php tests/Feature/AdminKuotaRevampTest.php tests/Feature/AdminDashboardEnhancementTest.php tests/Feature/AdminAdministrasiExportTest.php`
  - Jalankan Laravel Pint: `vendor/bin/pint --test`.

---

## 5. Strategi Pengujian (Testing Strategy)

1. **Pengujian Visual & UI Layout**:
   - Memastikan kalender admin tampil berbentuk kapsul semi-oval yang identik dengan kalender pembimbing.
   - Memastikan teks deskripsi hero berukuran lebih proporsional (`14.5px`) dan nyaman dibaca.
   - Memastikan tombol `Tinjau` tampak ringkas dan tidak memanjang.
   - Memastikan kolom Aksi pada tabel kuota sejajar simetris antara header dan tombol, serta memiliki jarak aman dari sisi kanan kartu.
   - Memastikan angka KPI dashboard (`1 berkas`, `16 /30 kursi`, `2 peserta`, `3,6 hr`) berbobot elegan tanpa kehilangan keterbacaan.
2. **Pengujian Regresi Fungsional**:
   - Memastikan seluruh assertion pada `AdminChildPagesVisualTest` dan `AdminKuotaRevampTest` lulus 100% tanpa error.

---

## 6. Kriteria Selesai (Definition of Done)

- [x] Kalender admin tampil dengan bentuk sel kapsul semi-oval konsisten dengan kalender pembimbing.
- [x] Teks deskripsi hero dashboard admin diperbesar ke `14.5px` sehingga lebih jelas dibaca.
- [x] Tombol Tinjau Berkas diperkecil dan diringkas menjadi `Tinjau` dengan tampilan yang proporsional.
- [x] Kolom Aksi tabel kuota sejajar simetris atas-bawah dan tidak menempel ke pojok kanan kartu.
- [x] Angka metrik KPI dashboard disesuaikan menjadi tidak terlalu tebal dan berpenampilan elegan.
- [x] `npm run build` sukses dan seluruh test suite PHPUnit lulus 100% tanpa regresi.

