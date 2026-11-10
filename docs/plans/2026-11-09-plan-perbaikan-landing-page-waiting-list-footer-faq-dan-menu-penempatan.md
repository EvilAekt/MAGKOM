# Rencana Kerja: Pembersihan Residu Waiting List, Perbaikan Footer & Layout, Debugging Interaktivitas FAQ, dan Pembaruan Menu Penempatan pada Landing Page MAGKOM

**Tanggal:** 9 November 2026  
**Status:** Menunggu Persetujuan Pengguna (*Pending Approval*)  
**Dokumen Referensi:** PRD MagKom, Design System MagKom, `.agents/rules/AGENTS.md`  

---

## 1. Tujuan

Menyelaraskan seluruh antarmuka dan interaktivitas Landing Page MAGKOM sesuai dengan evolusi sistem pasca-decommissioning backend waiting list:
1. **Eliminasi Total Residu Waiting List pada Landing Page**: Menghapus teks, status badge, tombol aksi, dan logika kondisional waiting list pada blok kuota/formasi serta modal peta alur publik. Jika kuota habis, sistem menampilkan status "Penuh" dengan tombol aksi non-aktif (*disabled*).
2. **Penyesuaian Layout & Redesain Footer Kedinasan yang Rapi**: Menghilangkan efek visual aneh (partikel 10 SVG WiFi melayang dan radar pulse yang menimpa teks) serta menormalkan padding bertingkat yang berlebihan (64px + 56px = 120px gap). Menyusun ulang struktur footer agar memiliki identitas brand resmi (Logo Sleman + MAGKOM), alamat kontak dinas terstruktur, tautan layanan, dan akses akun/medsos secara seimbang.
3. **Investigasi & Perbaikan FAQ yang Tidak Bisa Diklik**: Menyelesaikan akar masalah (*root cause*) di mana skrip JavaScript dimuat ganda (`landing.js` via Vite dan `landing-interactive.js` via `asset()`), yang menyebabkan event listener klik berjalan dua kali secara instan (membuka lalu langsung menutup kembali dalam milidetik yang sama). Menjadikan Vite sebagai satu-satunya *source of truth* asset frontend dan menerapkan *idempotent click binding*.
4. **Pembaruan Menu Landing Page ke "Penempatan"**: Mengubah label menu navigasi utama dari "Kuota" (atau istilah pemilihan jurusan) menjadi **"Penempatan"** (menunjuk ke `#penempatan`), serta menyediakan alias backward compatibility agar tautan `#kuota` tetap bekerja.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Perilaku Kuota Habis**: Sesuai dengan BR-01 s/d BR-04 dan penghapusan antrean waiting list, jurusan yang kuotanya habis (sisa = 0) tidak lagi menyediakan formulir pendaftaran tunggu, melainkan menampilkan badge status `Penuh` dan tombol nonaktif berlabel `Kuota Penuh` (atau `Pendaftaran Ditutup`).
2. **Kesesuaian Anggota Tim**: Jika pendaftar kelompok memilih jumlah anggota yang melebihi sisa kuota (misal sisa 2 kursi tetapi tim beranggotakan 3 orang), tombol aksi menjadi non-aktif dengan label informatif `Sisa Kuota Tidak Cukup (2 Kursi)`, tanpa menawarkan pendaftaran waiting list.
3. **Backward Compatibility Anchor Link**: Section formasi jurusan akan memiliki ID utama `id="penempatan"` dan tetap mempertahankan atribut/anchor `id="kuota"` agar tautan dari tombol Hero ("Daftar Sekarang") atau referensi luar tidak mengalami *broken scroll*.
4. **Eliminasi File Aset Duplikat di Blade**: Mengikuti aturan `.agents/rules/AGENTS.md` Pasal 6 (Vite dan asset loading), file `public/js/landing-interactive.js` serta `public/css/landing-modern.css` tidak boleh dimuat bersamaan dengan bundle `@vite(['...landing.css', '...landing-faq.css', '...landing.js'])`.

### Pertanyaan Terbuka:
*Tidak ada ambiguitas kritis. Seluruh alur telah tervalidasi dengan kode eksisting.*

---

## 3. Justifikasi Perubahan Markup Blade (Sesuai Aturan AGENTS.md Pasal 3)

Sesuai aturan ketat proyek, perubahan Blade hanya boleh dilakukan jika ada kebutuhan struktural nyata:
1. `resources/views/public/blocks/kuota.blade.php`:
   - **Alasan Markup Diperlukan**: Mengganti elemen `<a>` (tautan daftar waiting list) dengan elemen `<button disabled>` (tombol kuota penuh) saat `$sisa === 0`, menghapus atribut `data-is-waiting`, menyematkan id semantik `id="penempatan"`.
2. `resources/views/public/blocks/footer.blade.php`:
   - **Alasan Markup Diperlukan**: Menghapus kontainer elemen visual yang mengganggu (`.footer-wifi-particles`, `.wifi-radar-source`, `.wifi-signal-nodes`), menambahkan blok semantik Brand Identity (Logo Sleman, MAGKOM, dan deskripsi kedinasan).
3. `resources/views/public/landing.blade.php`:
   - **Alasan Markup Diperlukan**: Mengubah teks menu navigasi header dari 'Kuota' menjadi 'Penempatan', serta menghapus tag `<script>` dan `<link>` pemuatan aset legacy ganda (`public/css` dan `public/js`) yang merusak fungsionalitas klik FAQ.
4. `resources/views/public/partials/_peta_alur_modal.blade.php`:
   - **Alasan Markup Diperlukan**: Membersihkan kalimat status seleksi dari kata "Waiting List".

---

## 4. Evaluasi Pendekatan Teknis

| Aspek | Alternatif 1: Solusi Modular Modern (Direkomendasikan) | Alternatif 2: Inline Hotfix (Pembersihan Parsial) |
| :--- | :--- | :--- |
| **Pemuatan Aset & FAQ** | Menghapus skrip duplikat di Blade dan mengonsolidasikan seluruh styling/interaksi di `resources/css/` dan `resources/js/` via Vite bundler. | Mempertahankan kedua skrip tetapi menambahkan pengecekan `e.stopImmediatePropagation()` di kedua file terpisah. |
| **Kelebihan** | Mematuhi 100% aturan arsitektur MAGKOM (`AGENTS.md`), menghemat beban transfer jaringan (menghapus request file ganda), menghilangkan konflik spesifisitas CSS dan listener duplikat secara tuntas. | Terlihat lebih cepat ditulis karena tidak menyentuh tag Blade `<head>`. |
| **Kekurangan** | Memerlukan kompilasi ulang `npm run build` untuk memverifikasi asset manifest. | Sangat rapuh (*fragile*), menyisakan utang teknis (*technical debt*), berisiko timbul bug ganda pada interaksi JavaScript lainnya. |
| **Keputusan** | **Dipilih Alternatif 1**: Standar baku arsitektur Laravel 10 + Vite, memastikan zero conflict dan performa optimal. | Ditolak karena melanggar aturan arsitektur MAGKOM dan menyisakan file duplikat. |

---

## 5. Rencana Perubahan Berkas

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/public/landing.blade.php` | **Modify** | 1. Ubah label default menu navigasi dari `'Kuota'` menjadi `'Penempatan'`.<br>2. Hapus link stylesheet legacy ganda (`css/landing-modern.css`, `css/landing-faq-redesign.css`).<br>3. Hapus skrip legacy ganda (`js/landing-interactive.js`). |
| `resources/views/public/blocks/kuota.blade.php` | **Modify** | 1. Tambahkan `id="penempatan"` (dan jaga anchor `#kuota`).<br>2. Ubah legenda kuota penuh dari 'Penuh / Waiting List' menjadi 'Kuota Penuh'.<br>3. Hapus kondisi `$isWaiting` dan tombol `$kuotaBtnWaiting`.<br>4. Ketika sisa kuota = 0, tampilkan badge `Penuh` dan tombol aksi disabled `Kuota Penuh`. |
| `resources/views/public/blocks/footer.blade.php` | **Modify** | 1. Hapus markup `.footer-wifi-particles` (partikel SVG WiFi dan radar wave).<br>2. Tambahkan kolom Brand Profil Sleman / MAGKOM.<br>3. Rapikan struktur grid 4 kolom: Profil Brand, Alamat & Kontak, Tautan Terkait, Akun & Media Sosial. |
| `resources/views/public/partials/_peta_alur_modal.blade.php` | **Modify** | Bersihkan frasa "Waiting List" pada deskripsi status seleksi berkas. |
| `resources/js/pages/public/landing.js` | **Modify** | 1. Bersihkan logika kuota explorer dari penanganan waiting list; ganti dengan tombol nonaktif saat kuota 0 atau kapasitas tim tidak cukup.<br>2. Perbaiki fungsi `initFaqAccordion()` dengan mekanisme *safe idempotent binding* agar event klik kategori dan sub-pertanyaan stabil dan lancar. |
| `resources/css/pages/public/landing.css` | **Modify** | 1. Bersihkan aturan CSS `.footer-wifi-particles`, `.wifi-radar-source`, `.wifi-signal-nodes`, `@keyframes animateWifiFloating`, dan `@keyframes wifiRadarPulse`.<br>2. Normalisasi padding `.landing-footer` dan `.landing-footer-grid` agar tidak terjadi penumpukan gap yang aneh.<br>3. Sempurnakan tampilan kolom Brand dan tipografi footer. |
| `resources/css/pages/public/landing-faq.css` | **Modify** | Pastikan drawer accordion `.faq-questions-drawer` dan pane `.faq-sub-answer-pane` bertransisi secara mulus tanpa benturan `display: none` inline style. |
| `app/Http/Controllers/SuperAdmin/LandingCmsController.php` | **Modify** | Sinkronkan label default navigasi `$defaultHeaderNav` dari `'Kuota'` ke `'Penempatan'`. |
| `app/Http/Controllers/SuperAdmin/LandingCmsSiteEditorController.php` | **Modify** | Sinkronkan label default navigasi `$defaultHeaderNav` dari `'Kuota'` ke `'Penempatan'`. |
| `tests/Feature/LandingPageTest.php` | **Modify** | Sesuaikan test case `test_landing_page_menampilkan_label_waiting_list_...` menjadi verifikasi status kuota penuh dan ketiadaan teks waiting list. |

---

## 6. Langkah-Langkah Implementasi Bertahap

### Tahap 1: Eliminasi Total Fitur Waiting List pada Komponen Publik
- [ ] Buka `resources/views/public/blocks/kuota.blade.php`:
  - Ubah default legenda penuh dari `'Penuh / Waiting List'` menjadi `'Kuota Penuh'`.
  - Hapus variabel `$kuotaBtnWaiting`.
  - Pada perulangan kartu jurusan SMK dan Mahasiswa:
    - Hapus evaluasi `$isWaiting`.
    - Jika `$isAvailable && $sisa > 0`: tombol aksi aktif mengarah ke registrasi.
    - Jika `$sisa === 0`: tampilkan badge status `Penuh` (`status-penuh`), progress bar penuh, dan tombol `<button type="button" class="btn-card-action btn-disabled" disabled>Kuota Penuh</button>`.
    - Hapus atribut `data-is-waiting`.
- [ ] Buka `resources/js/pages/public/landing.js`:
  - Pada fungsi `initKuotaExplorer()`:
    - Hapus variabel `isWaiting` dan kondisional yang menyetel teks tombol menjadi `'Daftar Waiting List ➔'`.
    - Jika sisa kuota = 0, nonaktifkan tombol dengan teks `"Kuota Penuh"`.
    - Jika pendaftaran tim memilih jumlah melebihi sisa kursi (`sisa < currentMemberCount`), nonaktifkan tombol dengan teks `"Kuota Tidak Cukup (${sisa} Kursi)"`.
- [ ] Buka `resources/views/public/partials/_peta_alur_modal.blade.php`:
  - Ubah teks `outcome` seleksi berkas dari:
    `"Pengumuman status: Diterima (Penempatan terbit), Perbaikan Berkas (revisi 3 hari), atau Waiting List."`
    menjadi:
    `"Pengumuman status: Diterima (Penempatan terbit), Perbaikan Berkas (revisi 3 hari), atau Ditolak."`

### Tahap 2: Pembaruan Menu Landing Page Menjadi "Penempatan"
- [ ] Buka `resources/views/public/landing.blade.php`:
  - Perbarui `$defaultHeaderNav`: ubah `['label' => 'Kuota', 'url' => '#kuota']` menjadi `['label' => 'Penempatan', 'url' => '#penempatan']`.
- [ ] Buka `resources/views/public/blocks/kuota.blade.php`:
  - Ubah elemen section menjadi:
    `<section id="penempatan" class="landing-section" data-wp-block="kuota" data-wp-block-name="Kuota & Penempatan" ...>`
  - Tambahkan jangkar tersembunyi `<span id="kuota" class="sr-only" aria-hidden="true"></span>` agar URL `#kuota` tetap berfungsi presisi.
- [ ] Buka `app/Http/Controllers/SuperAdmin/LandingCmsController.php` & `LandingCmsSiteEditorController.php`:
  - Perbarui array `$defaultHeaderNav` agar default label sinkron: `['label' => 'Penempatan', 'url' => '#penempatan']`.

### Tahap 3: Perbaikan Fungsionalitas Klik FAQ (Debugging Dual-Execution)
- [ ] Buka `resources/views/public/landing.blade.php`:
  - Hapus baris `<script src="{{ asset('js/landing-interactive.js') }}..."></script>`.
  - Hapus baris `<link rel="stylesheet" href="{{ asset('css/landing-modern.css') }}...">` dan `<link rel="stylesheet" href="{{ asset('css/landing-faq-redesign.css') }}...">`.
  - Pastikan asset pipeline hanya mengandalkan `@vite(['resources/css/pages/public/landing.css', 'resources/css/pages/public/landing-faq.css', 'resources/js/pages/public/landing.js'])`.
- [ ] Buka `resources/js/pages/public/landing.js`:
  - Sempurnakan fungsi `initFaqAccordion()`:
    - Cegah inisialisasi ganda dengan `if (stackContainer.dataset.faqInitialized) return; stackContainer.dataset.faqInitialized = 'true';`.
    - Pastikan klik pada `.faq-category-header-btn` mengubah atribut `aria-expanded` dan class `is-expanded` dengan menghapus `display: none` pada drawer terkait secara eksplisit.
    - Pastikan klik pada `.faq-sub-question-btn` membuka answer pane dan memutar toggle icon SVG (+ menjadi x atau chevron) dengan bersih tanpa benturan.

### Tahap 4: Redesain Layout Footer Kedinasan yang Rapi & Profesional
- [ ] Buka `resources/views/public/blocks/footer.blade.php`:
  - Hapus elemen `.footer-wifi-particles`, `.wifi-radar-source`, dan seluruh perulangan 10 SVG `.floating-wifi-svg`.
  - Rombak layout `.landing-footer-grid` menjadi 4 kolom berimbang:
    - **Kolom 1 (Identitas & Brand Dinas)**: Logo Sleman, teks "MAGKOM", sub-nama "Portal Magang Diskominfo Sleman", serta deskripsi singkat peruntukan portal.
    - **Kolom 2 (Alamat & Jam Operasional)**: Alamat kompleks bupati, telepon, email, dan jam layanan publik.
    - **Kolom 3 (Tautan Resmi)**: Kebijakan Privasi, Portal Pemkab Sleman, Website Diskominfo, Verifikasi Dokumen TTE.
    - **Kolom 4 (Akses Cepat & Medsos)**: Tautan masuk (Peserta, Pembimbing, Admin) dan tombol link media sosial resmi (Instagram & X/Twitter).
- [ ] Buka `resources/css/pages/public/landing.css`:
  - Hapus blok kode CSS animasi partikel wifi: baris 1367 hingga 1552 (`.footer-wifi-particles`, `.wifi-radar-source`, `.wifi-pulse-circle`, `.wifi-signal-nodes`, `@keyframes wifiRadarPulse`, `@keyframes animateWifiFloating`).
  - Sesuaikan padding `.landing-footer` menjadi terukur (`padding: 3.5rem 1.5rem 2rem;`), menghapus redundansi padding bertingkat.
  - Tambahkan styling elegan untuk kolom Brand Footer (`.footer-brand-wrap`, `.footer-brand-logo`, `.footer-brand-desc`).

### Tahap 5: Kompilasi Aset, Sinkronisasi Mirror, dan Quality Gate
- [ ] Jalankan `npm run build` untuk memverifikasi build Vite tidak menghasilkan error.
- [ ] Salin file CSS dan JS yang telah dimodifikasi ke `public/` sebagai mirrored asset bila diperlukan oleh environment server lokal:
  - Salin `resources/js/pages/public/landing.js` ke `public/js/landing-interactive.js`.
  - Salin `resources/css/pages/public/landing.css` ke `public/css/landing-modern.css`.
  - Salin `resources/css/pages/public/landing-faq.css` ke `public/css/landing-faq-redesign.css`.
- [ ] Perbarui unit test di `tests/Feature/LandingPageTest.php`:
  - Ganti nama test `test_landing_page_menampilkan_label_waiting_list_jika_kuota_habis_dan_tombol_tetap_aktif` menjadi `test_landing_page_menampilkan_label_kuota_penuh_jika_kuota_habis_dan_tombol_nonaktif`.
  - Ubah assertion:
    - `$response->assertSee('Kuota Penuh');`
    - `$response->assertDontSee('Daftar Waiting List');`
    - `$response->assertDontSee('data-is-waiting="1"');`
- [ ] Jalankan verifikasi pengujian:
  - `& "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" artisan test tests/Feature/LandingPageTest.php tests/Feature/LandingPageFaqRedesignTest.php`
  - Verifikasi formatting dengan Laravel Pint:
    `& "D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe" ./vendor/bin/pint --test`

---

## 7. Strategi Testing

| Kategori | Skenario Pengujian | Hasil yang Diharapkan |
| :--- | :--- | :--- |
| **Happy Path Kuota** | Mengakses Landing Page publik saat kuota tersedia | Kartu jurusan menampilkan badge status hijau/kuning, sisa kursi, dan tombol CTA "Daftar Sekarang" aktif dengan parameter URL yang valid. |
| **Zero Waiting List** | Mengakses jurusan yang kuotanya telah terisi penuh (0 kursi tersisa) | Kartu menampilkan badge "Penuh", tombol aksi `<button disabled>` berlabel "Kuota Penuh", tidak ada teks atau atribut "Waiting List". |
| **Interaktivitas FAQ** | Pengguna mengklik tombol kategori FAQ dan tombol sub-pertanyaan | Accordion kategori membuka drawer pertanyaan dengan animasi lancar; klik pada sub-pertanyaan membuka jawaban teks tanpa tertutup otomatis. |
| **Navigasi Menu** | Memeriksa navbar landing page di desktop & mobile | Menu navigasi menampilkan "Penempatan" dengan URL `#penempatan`, dan saat diklik halaman bergulir mulus ke section kuota/formasi. |
| **Tata Letak Footer** | Memeriksa tampilan bagian bawah halaman | Footer bersih tanpa ikon WiFi kuning melayang yang menutupi teks, padding proporsional, menampilkan identitas brand MagKom dan 4 kolom rapi. |
| **Regression Test** | Menjalankan seluruh test suite Landing Page | 100% test suite Landing Page lolos (*green/pass*) di lingkungan PHP 8.3. |

---

## 8. Risiko & Mitigasi

1. **Risiko**: Tautan luar atau riwayat bookmark yang menunjuk ke `#kuota` menjadi tidak merespons jika id section diubah menjadi `#penempatan`.  
   *Mitigasi*: Sematkan anchor alias `<span id="kuota" class="sr-only"></span>` tepat di dalam section `#penempatan`, sehingga browser tetap dapat menggulir ke titik yang tepat baik melalui `#penempatan` maupun `#kuota`.
2. **Risiko**: Broken test pada `LandingPageTest.php` karena assertion lama masih mencari string "Waiting List".  
   *Mitigasi*: Perbarui assertion test secara terencana pada Tahap 5 agar menguji perilaku kuota penuh yang baru.
3. **Risiko**: Inkonsistensi cache browser pada asset frontend.  
   *Mitigasi*: Vite menangani *cache-busting* otomatis melalui file hash manifest pada `npm run build`.

---

## 9. Kriteria Selesai (Definition of Done)

- [ ] Tidak ada lagi kata "Waiting List" atau tombol antrean tunggu pada antarmuka Landing Page MAGKOM.
- [ ] Jurusan dengan kuota 0 menampilkan badge `Penuh` dan tombol disabled `Kuota Penuh`.
- [ ] Menu navigasi landing page menampilkan label `Penempatan` dan mengarah ke section yang tepat.
- [ ] FAQ dapat diklik dan berfungsi membuka/menutup kategori serta sub-pertanyaan secara lancar.
- [ ] Footer bersih dari partikel animasi mengganggu, memiliki identitas brand dinas, dan memiliki layout 4 kolom yang proporsional.
- [ ] Tidak ada pemuatan ganda antara file legacy `public/` dan bundle `resources/` via `@vite`.
- [ ] Seluruh test di `tests/Feature/LandingPageTest.php` dan `tests/Feature/LandingPageFaqRedesignTest.php` lulus 100% pada PHP 8.3.
- [ ] Tidak ada uncommitted file rahasia (.env) dan working tree lokal pengguna tetap aman.

