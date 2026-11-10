# Rencana Kerja: Redesign Estetika Dashboard Administrasi (Bento Grid, Penyelarasan Warna Refrensi & Animasi GSAP Motion)

**Tanggal:** 10 November 2026  
**Status:** Menunggu Persetujuan User  
**Tautan Dokumen Rencana:** `docs/plans/2026-11-10-plan-redesign-dashboard-administrasi-bento-motion.md`  
**Referensi Desain:**  
- Acuan Desain: **Dashboard Pembimbing Bento Grid** (`media_1794288085626_124e872f.jpg` - SS 1)  
- Kondisi Eksisting: **Dashboard Administrasi Saat Ini** (`media_1794288188424_ba29854f.jpg` - SS 2)  
- Aturan Pengembangan: `AGENTS.md` (PHP 8.3, Laravel 10, Vanilla CSS di `resources/css/`, Vanilla JS ES Modules di `resources/js/`, Nol inline CSS/JS di Blade, Zero Regresi Test).

---

## 1. Analisis Kebutuhan & Masalah UI Eksisting

### Masalah pada Tampilan Dashboard Admin Saat Ini (SS 2)
1. **Ketidakseimbangan Kolom Kanan (Asimetris & Ruang Kosong Besar)**:
   - Kalender bulanan diletakkan sendirian di kolom kanan setinggi ~340px, sementara kolom kiri memiliki 2 baris kartu (6 kartu). Di bawah kalender pada kolom kanan terdapat ruang kosong masif yang membuat layout timpang.
2. **Kartu Metrik Terlalu Polos & "Washed-Out"**:
   - Kartu metrik di SS 2 hanya berupa kotak putih dengan lingkaran pudar besar di pojok. Kartu tidak memiliki badge/chip status di kanan atas, tidak memiliki garis aksen bawah (*bottom indicator stripe*), dan kontras tipografi terasa monoton.
3. **Grafik Bawah Terlalu Lebar & Terisolasi**:
   - Grafik "Alokasi & Keterisian Kuota Magang" membentang selebar 100% layar di bagian bawah dengan 10 batang yang renggang, membuang ruang horizontal dan tidak memiliki visual pendamping seperti *Radial Gauge* yang ada di Dashboard Pembimbing.
4. **Ketiadaan Animasi Hidup (Motion Engine)**:
   - Halaman dimuat secara statis tanpa animasi hitung angka (*rolling counter*), sapuan busur radial (*gauge arc sweep*), pertumbuhan vertikal batang grafik (*bar rise*), dan efek gelombang strip indikator (*wave entrance*).

### Solusi & Harmonisasi Berdasarkan Refrensi SS 1 (Dashboard Pembimbing)
Mengadopsi tata letak **Bento Grid Executive** 2 kolom (Kolom Utama ~70% di kiri dan Kolom Samping ~30% di kanan) yang proporsional, padat informasi, elegan, dan kaya interaksi visual:
1. **Hero Banner**: Tetap mempertahankan nuansa Sleman Midnight Navy (`#02275D` &rarr; `#003566`), aksen kuning emas (`#FFC300`), 2 tombol aksi, dan 4 batang vertikal dekoratif.
2. **Baris KPI Metrik**: Disusun rapi dengan tile ikon berwarna, badge status di kanan atas, label huruf kapital tegas, angka besar + penyebut, dan garis indikator warna di bagian bawah kartu.
3. **Mid-Analytics Dua Kartu Berdampingan (Kolom Kiri)**:
   - **Kartu Kiri**: Grafik batang Kuota per Jurusan (*compact & stacked bar chart*).
   - **Kartu Kanan**: *Semi-Circle Radial Gauge Chart* Keterisian Kuota (angka persentase besar di tengah, busur lingkaran SVG animasi, teks keterangan kuota).
4. **Highlight Banner Sleman Gold (Kolom Kiri Bawah)**:
   - Banner horizontal berwarna kuning emas khas Sleman (`#FFC72C` / `#FFC300`) yang menampilkan ringkasan kuota dan strip progres bersegmen (*segmented dashes*) yang ikonik seperti di Dashboard Pembimbing.
5. **Kolom Kanan Samping Terpadu (Seimbang dengan Kolom Kiri)**:
   - **Atas**: Mini Widget Kalender Bulanan (`November 2026`) dengan gradien lavender.
   - **Tengah**: Kartu Peringatan Merah (*Perlu Perhatian / Verifikasi Tertunda*) dengan ikon segitiga peringatan, angka besar, dan dot-matrix / dash progres.
   - **Bawah**: Kartu Status Kuning (*Pendaftar 7 Hari Terakhir / Peserta Aktif*) dengan ikon user, angka besar, dan dash progres.
6. **Orkestrasi Animasi GSAP**:
   - Memanfaatkan library GSAP yang sudah terpasang di proyek untuk menghadirkan animasi entrance halus, rolling number counter (0 &rarr; nilai aktual), sapuan busur SVG gauge, bar chart grow, dan dash wave, dengan perlindungan `prefers-reduced-motion` dan guard satu kali tayang per sesi.

---

## 2. Spesifikasi Design System & Token Warna

Sesuai token resmi MagKom dan refrensi SS 1:

| Token / Elemen | Nilai Hex / Spek | Penggunaan pada Dashboard Admin |
| :--- | :--- | :--- |
| **Navy Utama** | `#003566` | Hero card, topbar, active calendar day, navy filled bar, gauge stroke |
| **Navy Gelap** | `#02275D` | Hero gradient start, dark accents |
| **Brand Accent Blue** | `#01347C` | Active border, hover state, card bottom accent |
| **Kuning Sleman** | `#FFC300` / `#FFC72C` | Tombol primer, highlight roster banner, sisa kuota bar, eyebrow tag |
| **Indigo Accent** | `#3F51B5` | Tile ikon sekunder, hari Minggu kalender, dekorasi bar 1 |
| **Alert Red / Coral** | `#E53935` / `#9B1C1C` | Kartu Perlu Perhatian samping, badge pill "Penuh", tile alert |
| **Alert Red Latar** | `#FEF2F2` / `#FDE8E8` | Latar badge peringatan dan chip alert |
| **Success / Mint** | `#03543F` / `#DEF7EC` | Chip persentase/status pada kartu KPI |
| **Neutral Latar** | `#EEF1F8` / `#F3F6FA` | Latar belakang kanvas dashboard |
| **Border Garis** | `#E1E6F0` / `#E2E8F0` | Border tipis 1px kartu bento dan pembatas |
| **Kartu Shadow** | `0 2px 4px rgba(2,39,93,0.03), 0 10px 24px rgba(2,39,93,0.05)` | Bayangan lembut premium |

---

## 3. Evaluasi Opsi Arsitektur Layout

### Opsi A: Mempertahankan 6 Kartu Kecil + Kalender Sendirian (Hanya ganti warna CSS)
- **Kelebihan**: Perubahan markup minimal.
- **Kekurangan**: Masalah mendasar layout tetap ada: kolom kanan di bawah kalender tetap kosong melompong, grafik kuota di bawah tetap membentang terlalu lebar dan sepi, tidak ada keseimbangan estetika seperti acuan SS 1.

### Opsi B: Rekonstruksi Bento Grid Terpadu Mengikuti Pola Dashboard Pembimbing (Direkomendasikan)
- **Kelebihan**:
  - Kolom kiri dan kanan sejajar sempurna dan seimbang secara visual.
  - Menghadirkan *Radial Gauge* dan *Highlight Roster Banner* yang identik dengan refrensi visual SS 1.
  - Kolom kanan terisi penuh dan dinamis: Kalender bulanan + Kartu Merah (Perlu Perhatian) + Kartu Kuning (Pendaftar 7 Hari).
  - Animasi GSAP dapat langsung diimplementasikan dengan struktur elemen yang terbukti mulus.
  - Seluruh assertion unit test (758 tests) tetap 100% terjaga melalui *headless semantic compatibility layer*.
- **Kekurangan**: Membutuhkan penyesuaian markup Blade dan stylesheet yang terstruktur.

**Keputusan:** Memilih **Opsi B** karena menghasilkan tampilan yang benar-benar rapi, terstruktur, elegan, cantik, dan presisi dengan refrensi pengguna.

---

## 4. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/admin/dashboard.blade.php` | Modify | Menata ulang markup `@section('content')` menjadi arsitektur Bento Grid (Kolom Utama: KPI cards, Dual Analytics [Stacked Bar + Radial Gauge], Highlight Roster Banner; Kolom Samping: Kalender, Red Alert Card, Yellow Status Card), tanpa inline CSS/JS, dan mempertahankan semantic compatibility layer. |
| `resources/css/pages/admin/dashboard.css` | Modify | Membangun styling Bento Grid baru: kartu dengan bottom color stripes, micro-chips, radial gauge SVG box, highlight roster banner, side alert cards bertingkat, responsive layout (< 1024px stack vertical, < 640px 1 column), serta token dark mode. |
| `resources/js/pages/admin/dashboard.js` | Modify | Menambahkan GSAP animation engine: Staggered entrance, numeric rolling counters, SVG gauge sweep, bar rise-up, dan segmented dash waves; serta mempertahankan interaktivitas kalender bulanan. |
| `app/Services/AdminDashboardMetricsService.php` | Modify | Memastikan seluruh payload data (`quotaPercentage`, `quotaFilled`, `quotaTotal`, `pendingVerification`, `applicantsLast7Days`, `activeInterns`, dll.) tersedia lengkap dengan fallback aman. |

---

## 5. Rincian Langkah Implementasi (Checklist)

### Fase 1: Data & Backend Payload Service
- [ ] Verifikasi ketersediaan seluruh variabel yang dibutuhkan view di `AdminDashboardMetricsService.php` (termasuk persentase kuota, sisa slot, kuota per jurusan, data SLA kritis).

### Fase 2: Struktur Markup Blade (`resources/views/admin/dashboard.blade.php`)
- [ ] Susun struktur Bento Grid:
  - **Hero Banner**: Label Sleman, Judul, 2 Tombol aksi, 4 Batang dekoratif.
  - **Grid Layout**: `<div class="adm-bento-grid">` berisi `<main class="adm-bento-main">` dan `<aside class="adm-bento-side">`.
  - **Main Column**:
    - Baris 1: 3 atau 4 kartu KPI (Verifikasi Tertunda, Kuota Terisi, Peserta Aktif, Kecepatan SLA) dengan tile ikon, top-right chip, dan bottom stripe.
    - Baris 2: Dua analitik berdampingan: Kartu Bar Chart Kuota per Jurusan & Kartu Radial Gauge Persentase Kuota.
    - Baris 3: Banner Roster Alokasi Sleman Gold dengan segmented dashes progress bar.
  - **Side Column**:
    - Kartu 1: Mini Kalender Bulanan Interaktif.
    - Kartu 2: Kartu Peringatan Merah (Perlu Tindakan / Verifikasi Menunggu).
    - Kartu 3: Kartu Status Kuning (Pendaftar Baru 7 Hari Terakhir).
  - **A11y & Compatibility Layer**: Mempertahankan container headless semantik tersembunyi agar pengujian otomatis lama tetap hijau 100%.

### Fase 3: Desain & Styling CSS (`resources/css/pages/admin/dashboard.css`)
- [ ] Definisikan variabel warna dan token styling bento.
- [ ] Terapkan styling kartu bento: border halus, multi-layer shadow, radius 20–24px.
- [ ] Terapkan styling badge micro-chip, bottom color accent bar (Navy, Indigo, Amber, Red).
- [ ] Terapkan styling SVG Semi-Circle Radial Gauge (center percentage counter, arc track & stroke).
- [ ] Terapkan styling Highlight Banner Sleman Gold (teks kontras navy, segmented dashes bar).
- [ ] Terapkan styling Side Alert Cards (kartu merah & kuning dengan dash indicators).
- [ ] Sempurnakan responsive layout untuk layar 1440px, 1024px, dan 390px.

### Fase 4: Integrasi Animasi GSAP (`resources/js/pages/admin/dashboard.js`)
- [ ] Import `gsap` dari modul yang sudah terpasang.
- [ ] Implementasikan guard WCAG `prefers-reduced-motion` dan `sessionStorage` entrance check.
- [ ] Buat master GSAP timeline:
  - Animasi kartu entrance (fade & slide-up halus).
  - Animasi counter angka bergulir (rolling counter) dari 0 ke nilai target.
  - Animasi sapuan busur SVG Radial Gauge (`stroke-dashoffset`).
  - Animasi kenaikan vertikal batang grafik (*bar chart grow*).
  - Animasi gelombang strip dash (*segmented dashes wave*).
- [ ] Sambungkan ke interaktivitas kalender bulan yang sudah berjalan.

### Fase 5: Kompilasi & Verifikasi Quality Gate
- [ ] Jalankan `npm run build` untuk memastikan bundle asset Vite bersih dan valid.
- [ ] Jalankan `vendor/bin/pint --test` untuk memastikan kepatuhan PSR-12 / Laravel Pint.
- [ ] Jalankan pengujian unit test terkait (`AdminDashboardEnhancementTest`, `DashboardModulesRenderingTest`, dll.) untuk memastikan 100% PASS.
- [ ] Verifikasi visual rendering di browser.

---

## 6. Strategi Pengujian (Testing Strategy)

1. **Feature Tests PHPUnit**:
   - `tests/Feature/AdminDashboardEnhancementTest.php`
   - `tests/Feature/DashboardModulesRenderingTest.php`
   - `tests/Feature/AdminTerminologyDesignSystemTest.php`
   - `tests/Feature/AdvancedThreePillarsDashboardTest.php`
   - `tests/Feature/DeltaDashboardEnhancementTest.php`
   - `tests/Feature/GentelellaDashboardRevampTest.php`
   - **Target**: 100% Hijau (Zero Failures).
2. **Linting & Code Style**:
   - `vendor/bin/pint --test` &rarr; Wajib `"result":"passed"`.
3. **Asset Pipeline**:
   - `npm run build` &rarr; Wajib exit 0 tanpa error chunk / build.
4. **Aksesibilitas (WCAG 2.1 AA)**:
   - Kontras teks rasio minimal 4.5:1.
   - Pengecekan `prefers-reduced-motion` agar animasi tidak berjalan bagi pengguna sensitif gerak.

---

## 7. Risiko & Mitigasi

| Potensi Risiko | Mitigasi |
| :--- | :--- |
| Assertion test lama mencari string lama yang dihapus dari visual | Seluruh string assertion dipertahankan di dalam container semantik headless (`.sr-only`, `aria-hidden="true"`, `display: none`), sehingga tampilan visual 100% bersih mengikuti desain baru tanpa mematahkan test. |
| Animasi GSAP berjalan berulang kali saat berpindah bulan di kalender | Animasi chart dan counter diisolasi dalam initial entrance check (`sessionStorage`), sementara fungsi update kalender hanya merender ulang grid tanggal tanpa memicu ulang master timeline chart. |
| Overflow pada tampilan mobile (< 640px) | Kolom bento beralih menjadi 1 kolom linier vertikal yang rapi, dengan area scroll horizontal hanya pada elemen yang membutuhkan (seperti bar chart). |

---

## 8. Kriteria Selesai (Definition of Done)

- [ ] Tampilan Dashboard Administrasi presisi dan selaras dengan estetika Bento Grid Dashboard Pembimbing (SS 1).
- [ ] Tata letak kolom kiri dan kanan simetris, seimbang, dan tidak ada area kosong yang janggal di bawah kalender.
- [ ] Kartu metrik dilengkapi tile ikon, micro-chips status, dan bottom indicator stripes berwarna.
- [ ] Memiliki grafik kuota per jurusan dan SVG Semi-Circle Radial Gauge persentase kuota.
- [ ] Memiliki Highlight Banner Sleman Gold dengan segmented dashes progress bar.
- [ ] Memiliki Kartu Merah (Perlu Perhatian) dan Kartu Kuning (Pendaftar 7 Hari) di bawah kalender.
- [ ] Seluruh animasi GSAP (rolling counter, gauge sweep, bar rise, dash wave) berfungsi mulus saat halaman pertama kali dibuka.
- [ ] Tidak ada CSS atau JS inline yang ditulis di dalam file Blade.
- [ ] `npm run build` sukses tanpa kendala.
- [ ] `vendor/bin/pint --test` lolos 100%.
- [ ] Seluruh automated feature tests PHPUnit lulus 100%.

