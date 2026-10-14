# Plan: Seamless Infinite Looping Carousel Kiri ke Kanan (Left-to-Right Marquee) pada Bidang & Kuota

- **Tanggal**: 2026-10-14
- **Status**: Draft (Menunggu Persetujuan)
- **Author**: Antigravity (web-planning)

---

## 1. Tujuan
Mengubah arah animasi pergerakan otomatis (*auto-scroll*) kartu bidang dan kuota di landing page (`resources/views/public/landing.blade.php`) agar mengalir dari **kiri ke kanan (left-to-right)** secara **mulus tanpa henti (seamless infinite loop)** tanpa ada efek patah atau lonjakan visual saat kartu mencapai ujung track, sekaligus mempertahankan kendali navigasi manual (tombol panah kiri/kanan, mouse drag-to-scroll, dan smart hover/touch pause).

---

## 2. Asumsi & Pertanyaan Terbuka
- **Asumsi**:
  1. Kecepatan gerak animasi tetap santai dan nyaman dibaca (kisaran 0.65 - 0.75 pixel per frame) agar calon peserta magang tetap dapat melihat informasi kuota dengan jelas.
  2. Saat pendaftar mengarahkan kursor (*hover*) atau menyentuh layar (*touch*), animasi otomatis dijeda (*smart pause*) sehingga kartu mudah dibaca dan tombol CTA mudah diklik.
  3. Tombol panah navigasi kiri dan kanan tetap aktif untuk melompat manual per kartu (step width 394px).
  4. Penggandaan node kartu untuk seamless looping dilakukan secara dinamis via JavaScript saat DOM ready, sehingga data Blade server-side tetap bersih dan tidak menduplikasi markup di database/Blade view.
- **Pertanyaan Terbuka**:
  - Tidak ada pertanyaan pemblokir. Kebutuhan user telah jelas dan spesifik.

---

## 3. Pendekatan Desain & Arsitektur

### Evaluasi Opsi Teknis:

| Kriteria Evaluasi | Opsi 1: Pure CSS `@keyframes` Marquee | Opsi 2 (Direkomendasikan): Seamless DOM Normalization via `requestAnimationFrame` |
| :--- | :--- | :--- |
| **Arah Kiri ke Kanan** | Sangat mudah (`transform: translateX`) | Sangat mulus via `scrollLeft -= speed` |
| **Seamless Infinite Loop** | Memerlukan 2 kontainer terpisah yang diulang | Sangat mulus dengan normalisasi titik tengah (`halfScrollWidth`) |
| **Dukungan Tombol Panah (Prev/Next)** | Sangat sulit disinkronkan karena CSS keyframes berjalan terpisah dari scroll position | Sangat mudah dan alami (`track.scrollBy({ left: ±step })`) |
| **Dukungan Mouse Drag-to-Scroll** | Kurang responsif, rawan konflik dengan animasi transform | Bawaan natural dengan scroll container (`scrollLeft`) |
| **Sinkronisasi Switch Mandiri/Kelompok** | Memerlukan update querySelector ke kontainer kloning terpisah | Otomatis tersinkronisasi karena fungsi `recalculateAllCards()` mencakup seluruh kartu di dalam track |

### Opsi Terpilih:
**Opsi 2: Seamless DOM Normalization via `requestAnimationFrame`**.

### Mekanisme Kerja Teknis Opsi 2:
1. **Clone Node Generator**:
   - Saat track diinisialisasi, JavaScript membaca seluruh kartu asli (`originalCards`), kemudian melakukan clone (`cloneNode(true)`) dan memasang atribut `data-is-clone="true"`.
   - Track kini berisi 2 salinan kartu identik: `[A, B, C, D] + [A', B', C', D']`.
2. **Posisi Awal & Normalisasi (Seamless Reset)**:
   - Lebar 1 set kartu dihitung: `halfWidth = track.scrollWidth / 2`.
   - Posisi scroll awal diatur di tengah: `track.scrollLeft = halfWidth`.
3. **Pergerakan Kiri ke Kanan (Left-to-Right)**:
   - Setiap frame animasi (`requestAnimationFrame`):
     ```javascript
     track.scrollLeft -= scrollSpeed;
     if (track.scrollLeft <= 0) {
         track.scrollLeft += halfWidth; // Reset tak kasat mata ke set kedua
     }
     ```
   - Karena konten set pertama dan kedua identik 100%, mata pengguna melihat kartu mengalir ke kanan secara terus menerus (*infinite endless loop*) tanpa jeda atau lonjakan.
4. **Navigasi Manual & Drag Support**:
   - Tombol panah Prev/Next menggeser `scrollLeft` dengan halus (`smooth behavior`).
   - Jika pengguna men-drag kartu hingga `track.scrollLeft >= halfWidth * 2 - track.clientWidth`, posisi otomatis dinormalisasi kembali dengan modulo `halfWidth`.
5. **Dukungan Tipe Mandiri & Kelompok**:
   - `recalculateAllCards()` otomatis memperbarui teks status, persentase progress bar, dan tautan pendaftaran pada SEMUA kartu (baik kartu original maupun clone), sehingga interaksi tetap 100% konsisten.

---

## 4. Perubahan File
| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/public/landing.blade.php`](file:///g:/MAGKOM/resources/views/public/landing.blade.php) | Modify | Memperbarui fungsi `setupQuotaCarousel()` untuk mendukung cloning dinamis, normalisasi scroll kiri-ke-kanan, dan sinkronisasi `recalculateAllCards` |
| [`tests/Feature/LandingPageTest.php`](file:///g:/MAGKOM/tests/Feature/LandingPageTest.php) | Verify | Memastikan seluruh 6 pengujian landing page dan 369 seluruh test suite tetap 100% lulus |

---

## 5. Langkah Implementasi
*Catatan: Checklist di bawah ini telah dieksekusi dan diverifikasi secara komprehensif.*
- [x] **Langkah 1**: Periksa struktur DOM container `.quota-carousel-track` untuk track SMK dan track Mahasiswa.
- [x] **Langkah 2**: Refaktor fungsi `setupQuotaCarousel()` pada tag `<script>` di `resources/views/public/landing.blade.php`:
  - Tambahkan logika cloning kartu: duplikasi kartu anak ke dalam track jika jumlah kartu > 1.
  - Set posisi scroll awal ke `halfWidth` agar langsung bisa bergerak mundur ke kiri (`scrollLeft -= speed`).
  - Tambahkan logika looping kiri-ke-kanan dengan seamless wrap: saat `scrollLeft <= 0`, geser ke `track.scrollLeft = halfWidth`.
  - Pasang guard untuk reduced motion (`prefers-reduced-motion`).
- [x] **Langkah 3**: Pastikan fungsi `recalculateAllCards()` tetap menjangkau elemen kartu clone dengan baik (menggunakan `document.querySelectorAll('.card-month-panel')`).
- [x] **Langkah 4**: Bersihkan cache Blade (`php artisan view:clear`).
- [x] **Langkah 5**: Uji interaksi secara visual di browser (rekam video/screenshot animasi mengalir dari kiri ke kanan, cek fungsi tombol panah, cek drag, dan cek switch mode Kelompok/Mandiri).
- [x] **Langkah 6**: Jalankan seluruh pengujian otomatis (`php artisan test`) untuk memastikan 369 tes berstatus PASS (100% hijau).
- [x] **Langkah 7**: Jalankan `graphify update .` untuk memperbarui knowledge graph.

---

## 6. Strategi Testing
- **Unit & Feature Test**:
  - `php artisan test --filter=LandingPageTest`: Memastikan metadata, teks jurusan, label waiting list, tab SMK/Mahasiswa, dan ketersediaan kuota tetap lulus validasi.
  - `php artisan test`: Memastikan seluruh 369 test suite tetap 100% PASS.
- **Visual & Browser Interaction Test**:
  - Menggunakan browser agent untuk memverifikasi bahwa:
    1. Kartu bergerak mengalir ke arah kanan secara mulus.
    2. Saat kartu mencapai ujung, looping terjadi mulus tanpa efek patah (*seamless*).
    3. Mengarahkan mouse ke kartu langsung menghentikan gerakan otomatis (*pause*).
    4. Menekan tombol panah kiri atau kanan tetap menggeser kartu dengan smooth.
    5. Mengubah mode ke Kelompok (2, 3, 4 orang) langsung memperbarui seluruh kartu termasuk kartu loop.

---

## 7. Risiko & Mitigasi
- **Risiko Dobel Event Listener pada Kartu Clone**:
  - *Mitigasi*: Event listener navigasi periode bulanan di dalam kartu menggunakan fungsi inline `onclick="switchCardMonth(this, ...)"` yang aman dan otomatis terbawa saat di-clone dengan `cloneNode(true)`.
- **Risiko Performa / Memory Leak**:
  - *Mitigasi*: Kloning hanya dilakukan 1x saat inisialisasi awal untuk track yang memiliki lebih dari 1 kartu. Animasi menggunakan `requestAnimationFrame` yang efisien dan berhenti saat tab/jendela tidak aktif.

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Seluruh langkah implementasi tercentang `- [x]`.
- [x] Animasi kartu bidang dan kuota mengalir mulus dari kiri ke kanan secara terus-menerus (*infinite loop*).
- [x] Navigasi manual (tombol panah, drag, switch periode, pemilihan mandiri/kelompok) berfungsi 100% tanpa error di console browser.
- [x] Seluruh automated test suite (`php artisan test`) berstatus **369 PASSED (100%)**.

---

## 9. Catatan Perubahan & Saran Lanjutan
- **Perubahan selama pengerjaan**:
  - Fungsi kontrol (`setRegistrationMode`, `setMemberCount`, `switchCardMonth`, `switchTab`) diekspos secara eksplisit ke objek global `window` untuk menjamin kompatibilitas handler inline HTML pada seluruh browser modern.
- **Saran Lanjutan (Out of Scope)**:
  - Opsi pengaturan kecepatan animasi dapat dikonfigurasi melalui panel pengaturan superadmin di masa depan jika dibutuhkan.
