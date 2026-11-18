# Rencana Kerja: Perbaikan Tata Letak Pop-Up Toast yang Bertumpukan (Stacked / Overlapping)

**Tanggal:** 18 November 2026  
**Status Dokumen:** Menunggu Persetujuan Pengguna  
**ID Rencana:** PLAN-2026-11-18-TOAST-STACK  

---

## 1. Tujuan
Memperbaiki bug tampilan notifikasi melayang (*floating toast notifications*) di dashboard Admin dan seluruh layout aplikasi MagKom di mana pop-up notifikasi kedua (seperti notifikasi pembaruan kuota dari Realtime SSE) muncul menimpa (*overlapping* / menutup) pop-up pertama (notifikasi flash pesan sukses), sehingga seluruh pop-up tersusun berurutan secara vertikal ke bawah (*stacked vertically with uniform gap*) sebagaimana pada layout lainnya.

---

## 2. Investigasi & Analisis Akar Masalah (Root Cause Analysis)

Berdasarkan inspeksi visual tangkapan layar pengguna (`media_1795006460256_ba36b29e.png`) dan penelusuran kode:
1. **Dua Kontainer Mengambang Terpisah (*Dual Floating Containers*):**
   - Di `resources/views/layouts/dashboard.blade.php` (baris 781), notifikasi flash Laravel dirender di dalam:
     ```html
     <div class="alerts-container">
         <x-flash-alert />
     </div>
     ```
   - Di baris 850 layout yang sama, komponen `<x-sse-toast-listener />` menyertakan elemen kontainer SSE terpisah:
     ```html
     <div id="magkom-toast-container" class="magkom-toast-container toast-floating-container alerts-container" ...></div>
     ```
2. **Keduanya Memiliki Koordinat CSS Fixed Identik:**
   - Dalam `resources/css/components/notifications.css` dan `resources/css/layouts/dashboard-layout-tokens.css`:
     ```css
     .alerts-container,
     .magkom-toast-container,
     #magkom-toast-container,
     .toast-floating-container,
     #toastFloatingContainer {
         position: fixed !important;
         top: 24px !important;
         right: 24px !important;
         z-index: 99999 !important;
         display: flex !important;
         flex-direction: column !important;
         gap: 12px !important;
         max-width: 420px !important;
     }
     ```
   - Karena kontainer 1 (`.alerts-container`) dan kontainer 2 (`#magkom-toast-container`) adalah **dua elemen DOM yang terpisah**, aturan `display: flex; flex-direction: column; gap: 12px;` hanya berlaku pada anak masing-masing kontainer.
   - Kedua kontainer tersebut dirender pada koordinat piksel yang sama persis (`top: 24px; right: 24px;`).
3. **Hasil:**
   - Saat Admin memvalidasi berkas, controller mengirim flash message sukses (`session('success')`) yang masuk ke Kontainer 1.
   - Secara bersamaan, backend memicu event SSE (`RealtimeEventTypeEnum::KuotaBerubah`) yang ditangkap oleh `public/js/realtime-sse.js` dan dimasukkan ke Kontainer 2.
   - Kontainer 2 ("Pembaruan Kuota Magang") menimpa tepat di atas Kontainer 1 ("Berkas pendaftaran berhasil diverifikasi"), menutupi notifikasi pertama.
4. **Perbandingan dengan Layout Peserta (`layouts/peserta.blade.php`):**
   - Di layout peserta, flash alert dirender di `<div class="toast-floating-container" id="toastFloatingContainer">`.
   - Engine `realtime-sse.js` mengenali ID `toastFloatingContainer` dan menempatkan notifikasi realtime ke dalam kontainer yang sama, sehingga notifikasi kedua tersusun otomatis di bawah notifikasi pertama.

---

## 3. Asumsi & Evaluasi Pendekatan Teknis

### Asumsi Kerja Logis:
- Semua notifikasi melayang (*floating toasts*) di sudut kanan atas harus berbagi satu kontainer *flex* yang sama di DOM.
- Notifikasi baru yang datang belakangan harus tersusun di bawah notifikasi sebelumnya dengan jarak seragam (`gap: 12px` / `0.75rem`).
- Aturan *auto-dismiss* (fading out setelah 5-7 detik) dan tombol close "X" tetap berfungsi normal untuk setiap item toast.

### Evaluasi Opsi Solusi:

| Kriteria | Opsi A: Client-Side JS/CSS Unification Saja (Zero Blade Touch) | Opsi B: Harmonisasi Layout Blade + JS Fail-Safe Unification (Direkomendasikan) |
|---|---|---|
| **Kepatuhan Aturan Blade** | Sangat ketat (0 perubahan Blade). | Memerlukan penyesuaian atribut `id` pada kontainer di `layouts/dashboard.blade.php`. |
| **Keandalan & Kebersihan DOM** | Mengandalkan mutasi DOM via JS saat `DOMContentLoaded`. | DOM sudah bersih dan seragam sejak SSR HTML, ditambah pengaman JS. |
| **Konsistensi Antar Layout** | Perilaku ditangani oleh JS. | Menyamakan struktur Admin (`dashboard.blade.php`) dengan Peserta (`peserta.blade.php`). |
| **Rekomendasi** | Opsi cadangan jika Blade sama sekali dilarang diedit. | **Rekomendasi Utama**: Bersih, kokoh, dan menyelesaikan akar masalah secara permanen. |

---

## 4. Perubahan File

| No | File | Aksi | Ringkasan Perubahan |
|---|---|---|---|
| 1 | `resources/views/layouts/dashboard.blade.php` | Modify | Menyelaraskan kontainer flash alert agar memiliki `id="toastFloatingContainer"` dan class `toast-floating-container alerts-container` persis seperti pada `layouts/peserta.blade.php`. |
| 2 | `public/js/realtime-sse.js` | Modify | Memperbarui `ensureToastContainer()` agar mendeteksi kontainer yang sudah ada, memindahkan seluruh toast yang tersebar ke satu kontainer utama, dan mencegah pembuatan kontainer fixed ganda. |
| 3 | `resources/js/services/realtime.js` | Modify | Menjaga sinkronisasi ES Module `realtime.js` dengan `public/js/realtime-sse.js`. |
| 4 | `resources/css/components/notifications.css` | Modify | Memastikan styling container tunggal memiliki `gap: 12px !important`, `flex-direction: column !important`, serta `margin: 0 !important` pada setiap kartu toast. |
| 5 | `resources/css/layouts/dashboard-layout-tokens.css` | Modify | Menyelaraskan token toast container agar seragam dengan `notifications.css`. |

---

## 5. Langkah Implementasi Terperinci

### Tahap 1: Penyelarasan Layout Blade Admin
- [ ] Di `resources/views/layouts/dashboard.blade.php` (baris 781-783), ubah:
  ```blade
  <div class="alerts-container">
      <x-flash-alert />
  </div>
  ```
  menjadi:
  ```blade
  <div class="toast-floating-container alerts-container" id="toastFloatingContainer" aria-live="polite" aria-atomic="true">
      <x-flash-alert />
  </div>
  ```
  *(Dengan ini, layout admin dan layout peserta menggunakan struktur dan ID yang identik).*

### Tahap 2: Penguatan Logika JavaScript Pemersatu Kontainer (`realtime-sse.js` & `realtime.js`)
- [ ] Perbarui method `ensureToastContainer()`:
  - Cari kontainer utama: prioritas `document.getElementById('toastFloatingContainer')`, lalu `document.getElementById('magkom-toast-container')`, dst.
  - Periksa apakah ada kontainer mengambang lain di DOM yang memiliki class `.alerts-container` atau `.toast-floating-container`.
  - Jika ada kontainer sekunder yang berisi elemen `.app-toast` atau `.app-alert`, pindahkan seluruh elemen anak tersebut (*append*) ke dalam kontainer utama.
  - Hapus atau bersihkan kontainer sekunder yang kosong agar tidak ada dua elemen fixed mengambang pada koordinat yang sama.
- [ ] Pastikan pemanggilan `showToast()` memasukkan notifikasi baru dengan `this.toastContainer.appendChild(toast)`, sehingga otomatis berada di urutan terbawah (*stacked below*).

### Tahap 3: Verifikasi Styling CSS
- [ ] Pastikan kontainer memiliki aturan:
  - `position: fixed !important; top: 24px !important; right: 24px !important;`
  - `display: flex !important; flex-direction: column !important; gap: 12px !important;`
  - `pointer-events: none !important;` (kontainer tidak menghalangi klik)
  - Anak `.app-toast` memiliki `pointer-events: auto !important; margin: 0 !important;`.

### Tahap 4: Pengujian & Quality Gate
- [ ] Uji skenario ganda: Buka halaman verifikasi berkas pendaftaran calon peserta di browser.
- [ ] Lakukan aksi verifikasi (Terima/Tolak) untuk memicu flash message sekaligus event SSE.
- [ ] Verifikasi visual:
  - Notifikasi 1 ("Berkas pendaftaran berhasil...") tampil di posisi teratas.
  - Notifikasi 2 ("Pembaruan Kuota Magang...") tampil tepat di bawahnya dengan jarak 12px, tanpa tertimpa atau terpotong.
  - Keduanya dapat ditutup dengan tombol "X" atau otomatis menghilang (*fade out*) setelah batas waktu.
- [ ] Jalankan kompilasi aset frontend: `npm run build`.
- [ ] Jalankan test suite PHPUnit: `vendor/bin/phpunit tests/Feature/AdminVerifikasiKategoriDanUploadSuratTest.php`.

---

## 6. Strategi Testing

1. **Manual / Visual Simulation Test**:
   - Mensimulasikan render HTML dengan 2 notifikasi sekaligus di dalam kontainer untuk memastikan kalkulasi CSS flex column dan gap 12px bekerja sempurna.
2. **Feature Testing**:
   - Memastikan seluruh endpoint verifikasi pendaftaran (`admin.pendaftaran.putuskan`) tetap mengembalikan redirect dengan `with('success', ...)` yang valid.
3. **Regression Testing**:
   - Memastikan modal keputusan pendaftaran (yang sebelumnya dirapikan) tidak terpengaruh oleh perubahan kontainer toast.

---

## 7. Risiko & Mitigasi

- **Risiko:** Perubahan kecil markup Blade di `layouts/dashboard.blade.php`.
  - **Mitigasi:** Perubahan hanya menambahkan atribut `id="toastFloatingContainer"` dan class `toast-floating-container` pada div pembungkus yang sudah ada, tanpa mengubah struktur tata letak internal.
- **Risiko:** Konflik listener realtime jika SSE belum aktif atau fallback ke polling.
  - **Mitigasi:** `ensureToastContainer()` menangani kedua moda (SSE dan Polling) secara transparan karena menggunakan kontainer DOM yang sama.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Tidak ada lagi 2 pop-up yang saling menimpa pada koordinat `top: 24px; right: 24px`.
- [x] Pop-up kedua (notifikasi kuota atau notifikasi lainnya) tampil tertata rapi persis di bawah pop-up pertama dengan jarak yang proporsional.
- [x] Keduanya memiliki tombol tutup mandiri dan animasi keluar (*slide out*) yang mulus.
- [x] Build Vite (`npm run build`) berhasil tanpa error.
- [x] Seluruh pengujian otomatis PHPUnit lulus.

