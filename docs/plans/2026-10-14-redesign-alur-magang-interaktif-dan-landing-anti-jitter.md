# Plan: Redesign Alur Magang Interaktif (Zero-Jitter & Anti-AI-Slop) dan Perbaikan Landing Page Section

- **Tanggal**: 2026-10-14
- **Status**: Selesai (Verified 100% - All Tests Passed & Visual Browser Verified)
- **Author**: Lead Tech Architect & Senior Frontend Specialist (Antigravity web-planning)
- **Target Pelaksana**: Junior Full-Stack Developer / LLM Fast Executor (e.g. Gemini Flash)
- **Dokumen Acuan**:
  - PRD: [`docs/01-PRD-MagKom.md`](file:///g:/MAGKOM/docs/01-PRD-MagKom.md) (BR-01 s/d BR-10, AC-01 s/d AC-12)
  - Design System: [`docs/07-DESIGN-SYSTEM-MAGKOM.md`](file:///g:/MAGKOM/docs/07-DESIGN-SYSTEM-MAGKOM.md) (Bab 2 Tokens, Bab 3 Components)
  - Issue Report: Screenshot `media_1791963070480.png` (Landing page button misplaced) & `media_1791963086810.png` (SVG canvas shaking/jittering & horizontal overflow clipping).

---

## 1. Ringkasan Eksekutif & Akar Masalah (Root Cause Analysis)

### 1.1 Masalah 1: Tombol Landing Page Menggantung Canggung (Screenshot 1)
- **Gejala**: Tombol *"Eksplorasi Peta Alur Interaktif →"* menumpuk di pojok kiri bawah kartu pertama (*Buat Akun*) dengan ukuran kecil dan posisi tidak simetris.
- **Akar Masalah**: Pada [`resources/views/public/landing.blade.php`](file:///g:/MAGKOM/resources/views/public/landing.blade.php), tombol dibungkus dalam tag `<div>` polos tanpa styling flexbox/grid perataan tengah (`text-align: center; justify-content: center;`). Elemen container mewarisi `align-self: flex-start`, sehingga terlempar ke tepi kiri di bawah card 01.
- **Solusi Redesign**:
  1. Buat **Interactive Journey Showcase Bar** di bawah baris 4 kartu alur.
  2. Tempatkan tombol CTA di tengah (*center-aligned*) dengan layout banner yang menyatu (*seamless*), dilengkapi teks informasi nilai tambah: *"Butuh panduan persyaratan berkas, timeline seleksi, dan aturan kuota? Eksplorasi Peta Interaktif &rarr;"*.
  3. Berikan link langsung pada setiap kartu langkah (01-04) agar pengguna dapat langsung melompat ke fase terkait di `/panduan-alur?fase=X`.

### 1.2 Masalah 2: Efek Getar / Shaking Loop pada Graf SVG (Screenshot 2)
- **Gejala**: Saat kursor mouse diarahkan ke node graf alur, elemen berguncang/bergetar hebat secara kontinu (*jitter loop*).
- **Akar Masalah**:
  - Pada [`resources/views/public/panduan-alur.blade.php`](file:///g:/MAGKOM/resources/views/public/panduan-alur.blade.php), setiap grup node SVG memiliki atribut koordinat SVG lokal: `<g id="node-1" transform="translate(40, 50)">`.
  - CSS menerapkan:
    ```css
    .svg-node-group { transition: transform 0.2s ease; }
    .svg-node-group:hover { transform: translateY(-2px); }
    ```
  - Pada SVG DOM browser, CSS `transform` menimpa matriks koordinat SVG lokal. Saat naik 2px, bounding box berpindah dari bawah kursor mouse &rarr; memicu event `mouseleave` &rarr; elemen turun kembali &rarr; kursor menyentuh kembali &rarr; memicu `mouseenter` &rarr; berulang 60–120 kali per detik.
- **Akar Masalah Tambahan (AI Slop & Clipping)**:
  - SVG memiliki lebar tetap yang melebihi kontainer responsif (`min-width: 720px; overflow-x: auto`), menyebabkan scrollbar horizontal tebal di dalam kartu dan sisi kanan/atas node graf terpotong (*clipped*).
  - Estetika visual tampak kaku seperti kotak flowchart skripsi mentah, bukan UI web portal dinas modern berkelas dunia (*state-of-the-art*).

---

## 2. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Teknis:

| Kriteria | Opsi A: Mempertahankan SVG dengan viewBox CSS Fix | Opsi B: HTML5-First Interactive Journey Board + CSS Pipelines (Direkomendasikan) |
| :--- | :--- | :--- |
| **Penyelesaian Bug Getar (Jitter)** | Cukup baik jika transform dihapus total dan diganti `stroke` highlight. Namun event listener SVG tetap rawan repainting lag. | **100% Selesai & Bebas Risiko**. Transisi CSS hover pada elemen HTML standar (`div/button`) menggunakan GPU-accelerated box-shadow & border tanpa mengubah bounding box. |
| **Responsivitas & Layout** | Membutuhkan perhitungan manual viewBox matematis, rawan terpotong di layar 768px-1200px. | **Otomatis Responsif**. Menggunakan CSS Grid & Flexbox, menyesuaikan lebar kontainer secara elegan tanpa memunculkan scrollbar horizontal yang jelek. |
| **Estetika & Sensasi Interaktif** | Cenderung kaku (AI slop kotak-kotak SVG dengan teks tidak wrap). | **Premium & Modern**. Glassmorphism, badge status dinamis, icon SVG tajam, micro-interactions halus, checklist interaktif, selaras Design System DS-MAGKOM. |
| **Aksesibilitas (a11y)** | Sulit dinavigasi keyboard (tabindex pada SVG `<g>` tidak konsisten di browser). | **Luar Biasa**. Setiap node dapat difokuskan via Keyboard (`Tab`, `Enter`, `Space`), atribut `aria-selected` dan `aria-expanded` terbaca screen reader. |

> **Keputusan Arsitektur**: Mengadopsi **Opsi B (HTML5-First Interactive Journey Board)**. Kita akan merancang ulang visual alur magang menjadi papan perjalanan interaktif (*interactive milestone track*) yang modern, elegan, responsif, dan bebas efek getar.

---

## 3. Spesifikasi Desain & Visual (Anti-AI-Slop Standard)

### A. Landing Page Section 3 (`#alur-section`)
1. **Header & Subtitle**: Pertahankan judul *"Alur magang"* dan subtitle *"Empat langkah dari pendaftaran sampai lembar nilai"*.
2. **Kartu 4 Langkah**: Berikan sentuhan glassmorphism modern dengan badge angka yang menyala halus, judul tegas, dan deskripsi ringkas.
3. **Interactive Showcase Bar (Pengganti tombol lama yang menggantung)**:
   - Ditempatkan di bawah 4 kartu dengan margin atas yang lapang (`margin-top: 2.75rem`).
   - Lebar proporsional (`max-width: 820px; margin-inline: auto`).
   - Berupa card kaca transparan (*frosted glass*) dengan perbatasan garis lembut:
     - Kiri: Icon interaktif dan teks ajakan: *"Ingin simulasi alur untuk jalur SMK / Mahasiswa atau cek SLA & aturan kuota?"*
     - Kanan: Tombol utama *"Eksplorasi Peta Interaktif &rarr;"* dengan warna solid Blue-600, shadow hover halus, dan responsivitas penuh di mobile.

### B. Halaman `/panduan-alur` (Interactive Journey Board)
1. **Interactive Journey Pipeline (Kolom Kiri)**:
   - Disusun dalam 4 Tahap Utama (Fase 1: Pendaftaran, Fase 2: Seleksi & Verifikasi, Fase 3: Pelaksanaan, Fase 4: Evaluasi).
   - Setiap fase memiliki kartu milestone interaktif yang menampilkan:
     - Nomor Urut & Icon Representatif
     - Judul Tahap & Sub-label
     - Badge SLA / Pelaksana
     - Indikator Percabangan Status (pada Fase 2: Diterima, Waiting List, Perlu Perbaikan) dengan pill badge interaktif yang dapat diklik!
   - Garis penghubung alur (*connectors*) menggunakan CSS pseudo-element (`::after` line tracker) yang menyala dinamis mengikuti fase yang aktif.
   - **ZERO JITTER GUARANTEE**: Hover state hanya memanipulasi `border-color`, `box-shadow`, dan warna background; **TIDAK MENGGUNAKAN `transform: translateY`** pada kontainer interaktif.
2. **Interactive Node Inspector (Kolom Kanan)**:
   - Tampilan *sticky panel* yang selalu berada di pandangan pengguna saat menggulir (*scroll*).
   - Menampilkan:
     - Badge Fase & Estimasi SLA
     - Judul Tahap Terpilih & Ringkasan Penjelasan
     - Aktor Penanggung Jawab (Peserta / Admin / Pembimbing)
     - Persyaratan Dokumen & Checklist Wajib
     - Callout Aturan Bisnis Kunci PRD (BR-01, BR-05, BR-09, BR-10, AC-12)
     - Tombol Aksi Kontekstual (*"Lihat Kuota Tersedia"*, *"Masuk ke Portal"*, dll.)
3. **Simulator Mode**:
   - Filter Jenjang: **Semua Jenjang**, **SMK**, **Mahasiswa (D3/S1)**.
   - Filter Tipe: **Mandiri (1 Orang)**, **Kelompok (2–5 Orang)**.
   - Mengubah konten checklist dan badge pada inspector secara instan tanpa reload.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Modifikasi Teknis |
| :--- | :--- | :--- |
| [`resources/views/public/landing.blade.php`](file:///g:/MAGKOM/resources/views/public/landing.blade.php) | Modify | 1. Restrukturisasi kontainer CTA alur magang di Section 3: ganti tombol menggantung dengan `alur-cta-showcase` yang terpusat dan elegan.<br>2. Tambahkan styling CSS pendukung yang harmonis dengan tema Sleman.<br>3. Tambahkan link interaktif dari kartu langkah 01–04 menuju target fase di `/panduan-alur`. |
| [`resources/views/public/panduan-alur.blade.php`](file:///g:/MAGKOM/resources/views/public/panduan-alur.blade.php) | Modify | 1. Rombak total area graf dari SVG kaku menjadi **HTML5 Interactive Journey Board** berbasis grid/flex modern.<br>2. Hapus seluruh CSS `transform: translateY` yang memicu getar mouse.<br>3. Hilangkan batasan `min-width: 720px; overflow-x: auto;` sehingga tidak ada lagi scrollbar horizontal di dalam card graf.<br>4. Integrasikan pill percabangan status verifikasi (Diterima, Waiting List, Perlu Perbaikan) yang reaktif mengupdate inspector samping.<br>5. Perbaiki dark mode styling agar kontras dan terbaca sempurna. |
| [`tests/Feature/LandingPageTest.php`](file:///g:/MAGKOM/tests/Feature/LandingPageTest.php) | Modify | Perbarui assertion untuk memverifikasi container CTA baru, teks tombol, dan tautan alur magang interaktif. |
| [`tests/Feature/AlurMagangPageTest.php`](file:///g:/MAGKOM/tests/Feature/AlurMagangPageTest.php) | Modify | Perbarui selector ID/class untuk memvalidasi Interactive Journey Board yang baru (memastikan seluruh node 1-7, percabangan status, simulator, dan inspector teruji 100%). |

---

## 5. Rencana Langkah Implementasi (Task Breakdown untuk Junior / AI Flash)

### Fase 1: Perbaikan Landing Page Section 3
- [x] **Task 1.1**: Buka [`resources/views/public/landing.blade.php`](file:///g:/MAGKOM/resources/views/public/landing.blade.php). Cari elemen di bawah `.alur-steps-row`.
- [x] **Task 1.2**: Hapus pembungkus tombol canggung yang ada saat ini (`<div><a href="..." class="btn-panduan">...</a></div>`).
- [x] **Task 1.3**: Tambahkan komponen baru `.alur-interactive-cta-box` dengan struktur:
  - Container berpusat (`display: flex; justify-content: space-between; align-items: center; margin-top: 2.5rem; background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(12px); border-radius: 1rem; padding: 1.25rem 1.75rem; border: 1px solid rgba(255, 255, 255, 0.15);`).
  - Bagian Kiri: Informasi ringkas *"Pahami alur lengkap, persyaratan berkas, SLA verifikasi, dan simulasi jalur pendidikan secara visual"*.
  - Bagian Kanan: Tombol CTA premium *"Buka Peta Alur Interaktif &rarr;"* dengan warna solid, padding seimbang, dan icon SVG yang presisi.
- [x] **Task 1.4**: Berikan atribut `href="{{ route('panduan-alur') }}?step=1"` (dan seterusnya untuk step 2, 3, 4) pada masing-masing kartu di `.alur-steps-row` agar seluruh kartu intuitif untuk diklik.

### Fase 2: Redesign Halaman `/panduan-alur` (Journey Board Anti-Jitter)
- [x] **Task 2.1**: Buka [`resources/views/public/panduan-alur.blade.php`](file:///g:/MAGKOM/resources/views/public/panduan-alur.blade.php).
- [x] **Task 2.2**: Ganti struktur dalam `.svg-graph-container` dengan arsitektur **HTML5 Journey Canvas**:
  - Bungkus dalam `.journey-board-wrapper` (tanpa horizontal scroll, 100% responsif).
  - Buat 4 blok fase:
    - **Fase 1: Pendaftaran & Kuota** (Milestone 01 Cek Kuota, Milestone 02 Registrasi Akun, Milestone 03 Unggah Berkas).
    - **Fase 2: Seleksi & Keputusan** (Milestone 04 Verifikasi Admin Diskominfo SLA 1-3 Hari, dengan sub-cabang interaktif: *Diterima*, *Waiting List*, *Perlu Perbaikan*).
    - **Fase 3: Pelaksanaan & Monitoring** (Milestone 05 Orientasi & Mentor, Milestone 06 Logbook Harian Maks. H-1).
    - **Fase 4: Evaluasi & Kelulusan** (Milestone 07 Rubrik 100 Poin & Sertifikat Digital QR).
- [x] **Task 2.3**: Buat styling kartu milestone `.journey-node-card`:
  - Gunakan `cursor: pointer; position: relative; border-radius: 12px; padding: 1rem 1.15rem; background: var(--neutral-0); border: 1.5px solid var(--neutral-300); transition: border-color 0.2s ease, box-shadow 0.2s ease;`.
  - **DILARANG MENGGUNAKAN `transform: translateY` PADA HOVER!** Gantilah dengan `box-shadow: 0 8px 20px -2px rgba(7, 81, 255, 0.18); border-color: var(--blue-600);`.
  - Node yang sedang aktif (`.active`) ditandai dengan border warna primer tegas dan indikator dot menyala.
- [x] **Task 2.4**: Sambungkan garis alur dengan CSS connecting lines `.journey-connector-line` (menggunakan border-left/border-top dengan warna aksen lembut dan panah arah) sehingga tata letak mengalir secara visual dari atas ke bawah atau kiri ke kanan secara alami.
- [x] **Task 2.5**: Pastikan event listener `onclick="selectNode(id)"` pada kartu milestone memperbarui panel samping (`#inspectorCard`) secara instan.
- [x] **Task 2.6**: Tangani query parameter URL (`?step=X` atau `?fase=X`) di JavaScript saat halaman dimuat, sehingga jika pengguna mengklik kartu di landing page, node yang relevan langsung terpilih dan di-highlight.

### Fase 3: Testing & Verifikasi
- [x] **Task 3.1**: Jalankan `php artisan view:clear` untuk memastikan cache view terhapus.
- [x] **Task 3.2**: Perbarui dan jalankan `php artisan test tests/Feature/LandingPageTest.php` dan `tests/Feature/AlurMagangPageTest.php`.
- [x] **Task 3.3**: Jalankan seluruh test suite aplikasi (`php artisan test`) untuk memastikan seluruh 377+ test berstatus PASS (100% hijau).
- [x] **Task 3.4**: Lakukan verifikasi browser subagent:
  - Cek landing page: posisi tombol alur magang berada di tengah, tidak menumpuk di bawah kartu 01.
  - Cek halaman `/panduan-alur`: arahkan kursor ke semua node kartu, pastikan **TIDAK ADA GETAR SAMA SEKALI (ZERO JITTER)**.
  - Cek bahwa tidak ada scrollbar horizontal aneh di dalam card alur.
  - Klik masing-masing node dan periksa responsivitas side inspector.
- [x] **Task 3.5**: Sinkronkan knowledge graph dengan `graphify update .`.

---

## 6. Strategi Testing

1. **Automated Tests**:
   - `LandingPageTest`:
     - Memastikan section `#alur-section` memuat showcase bar alur baru.
     - Memastikan tautan ke `route('panduan-alur')` valid dan memiliki parameter navigasi langkah.
   - `AlurMagangPageTest`:
     - Memastikan seluruh milestone 01 sampai 07 ter-render dengan teks PRD akurat.
     - Memastikan badge percabangan verifikasi (Diterima, Waiting List, Perlu Perbaikan) memiliki handler dan id/data-step.
     - Memastikan side inspector memuat detail SLA, Aktor, Checklist berkas, dan Aturan Bisnis.
2. **Visual & Interactive Verification**:
   - Mouse hover pada seluruh node perjalanan untuk memverifikasi hilangnya getaran/jittering.
   - Uji resolusi 1920x1080 (desktop), 1024x768 (tablet), dan 375x812 (mobile) untuk memastikan zero-horizontal overflow.

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Dampak | Strategi Mitigasi |
| :--- | :--- | :--- |
| **Hover Jittering Berulang** | UX buruk dan membuat pusing pengguna | Hapus total penggunaan CSS `transform: translateY` pada elemen pemicu hover; gunakan transisi warna border dan box-shadow murni pada elemen HTML statis. |
| **Horizontal Scrollbar Overflow** | Visual terpotong seperti Screenshot 2 | Ganti container SVG bertetap `min-width: 720px` dengan CSS Grid responsif (`grid-template-columns: repeat(auto-fit, minmax(...))`) yang fleksibel mengikuti lebar parent container. |
| **Regresi Automated Test** | CI/CD pipeline gagal | Selalu gunakan ID elemen yang konsisten (`#node-1`, `#inspectorTitle`, `#inspectorDesc`, dll.) agar kompatibel dengan seluruh assertion unit & feature test. |

---

## 8. Definition of Done (DoD)

- [x] Tombol alur magang di landing page tampil terpusat, simetris, dan proporsional (bukan tombol kecil menggantung di bawah kartu 01).
- [x] Efek getar (*hover jitter loop*) di halaman `/panduan-alur` hilang 100%.
- [x] Tidak ada scrollbar horizontal yang memotong graf di halaman `/panduan-alur`.
- [x] Seluruh tahapan 01-07 dan percabangan status terhubung dinamis ke Side Inspector.
- [x] Seluruh automated test (`php artisan test`) lulus 100% tanpa regresi.
- [x] Verifikasi visual melalui browser subagent mengonfirmasi pengalaman pengguna yang mulus dan elegan.
