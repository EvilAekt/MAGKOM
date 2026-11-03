# Plan: Perbaikan Navigasi "Lanjut Pos" pada Simulator Peta Alur Interaktif

Dokumen ini adalah rencana kerja teknis untuk memperbaiki bug navigasi "Lanjut Pos" pada Simulator Peta Alur Interaktif (`/peta-alur`), di mana saat tombol lanjut diklik, mobil/posisi malah menghilang dan kembali ke awal (Pos 1), alih-alih melaju mulus ke kanan ke pos berikutnya.

---

## 1. Masalah & Analisis Akar Masalah (Root Cause)

### 1.1 Masalah yang Dilaporkan
- Saat pengguna mengklik tombol **"Lanjut Pos"** (misal "Lanjut Pos 2 ➔" dari Pos 1), mobil/posisi malah menghilang (*fade out*) dan kembali (*reset*) ke Pos 1.
- Perilaku yang seharusnya: Mobil langsung meluncur mulus ke arah kanan, kamera mengikuti pergerakan mobil, dan balon tahapan (webtoon bubble) serta karakter di pos berikutnya tampil aktif.

### 1.2 Akar Masalah Teknis
1. **Dual-Engine / Script Duplication Collision**:
   - File `resources/views/public/peta-alur-interaktif.blade.php` memuat asset Vite via `@vite(['resources/css/pages/public/peta-alur.css', 'resources/js/pages/public/peta-alur.js'])` di `<head>`.
   - Namun di bagian bawah file Blade (baris 946–1898), terdapat tag `<script>` inline sepanjang ~950 baris yang menduplikasi seluruh kode logika engine game.
   - Akibatnya, ada **dua game engine independen** yang berjalan bersamaan di dua scope berbeda (`requestAnimationFrame(gameLoop)` berjalan 2x). Saat tombol lanjut pos diklik pada salah satu instance, instance lainnya yang masih memiliki state `carX = 280` dan `targetCamX = 0` terus menimpa `viewBox` SVG kembali ke koordinat x=0 setiap 16ms.
2. **Pemicu `triggerAutoLoopReset()` yang Keliru**:
   - Fungsi `navigateModalStep(direction)` mengevaluasi `nextStep = currentStep + direction`.
   - Jika `nextStep > 4` atau terjadi desinkronisasi nilai antar instance, fungsi ini memanggil `triggerAutoLoopReset()`.
   - Di dalam `triggerAutoLoopReset()`, mobil disetel `carGroup.style.opacity = '0'` (hilang), dan setelah 400ms mobil dipindahkan kembali ke `carX = 280; currentStep = 1;` (balik lagi ke awal).
3. **Pemuatan CSS Ganda & Inline Event Handlers**:
   - Di Blade terdapat pemuatan CSS ganda (`public/css/peta-alur-fullscreen.css` via `<link>` dan `resources/css/...` via `@vite`).
   - Terdapat inline event handlers `onclick="..."` di elemen SVG Blade yang bertentangan dengan standar arsitektur di `AGENTS.md`.

---

## 2. Pendekatan Solusi (Technical Approach)

Sesuai aturan permanen proyek `AGENTS.md` (bagian 3, 4, 5, dan 14):
1. **Konsolidasi Single Source of Truth ke `resources/js/pages/public/peta-alur.js`**:
   - Hapus script inline ~950 baris dari `resources/views/public/peta-alur-interaktif.blade.php`.
   - Hapus pemuatan CSS ganda (`<link href="{{ asset('css/peta-alur-fullscreen.css') }}">`) di `<head>`, gunakan `@vite` sebagai satu-satunya asset pipeline.
2. **Modernisasi Interaksi & Event Delegation**:
   - Ganti pemanggilan inline `onclick` pada tombol navigasi webtoon bubble dengan atribut data semantik (misal `data-action="next-step"` dan `data-target-step="2"`).
   - Di `resources/js/pages/public/peta-alur.js`, pasang event delegation terstruktur untuk menangani klik tombol pos, klik halte, dan tombol kemudi analog.
3. **Perbaikan Logika Navigasi & Boundary Guard**:
   - Buat fungsi navigasi pos yang deterministik:
     - Pos 1 -> tombol "Lanjut Pos 2" langsung memanggil `goToStep(2)`.
     - Pos 2 -> tombol "Lanjut Pos 3" langsung memanggil `goToStep(3)`.
     - Pos 3 -> tombol "Lanjut Pos 4" langsung memanggil `goToStep(4)`.
   - Batasi rentang target step dengan ketat menggunakan `Math.max(1, Math.min(4, targetStep))` sehingga tidak akan pernah melompat ke step > 4.
   - `triggerAutoLoopReset()` **HANYA** boleh dipanggil jika mobil sengaja dikemudikan manual sampai melewati ujung timur boulevard (`carX >= 2320`).
4. **Penyempurnaan Transisi Kamera & Animasi Mobil**:
   - Saat mobil meluncur ke pos berikutnya (`driveCarToX`), `targetCamX` mengikuti koordinat `carX` secara mulus (`curCam.x += (targetCamX - curCam.x) * 0.1`).
   - Balon webtoon pos sebelumnya ditutup halus, dan balon pos tujuan diaktifkan begitu mobil mendekati pos target, sehingga tampilan terasa mulus dan responsif.

---

## 3. Daftar File yang Terlibat

| File | Tindakan | Rincian Perubahan |
|---|---|---|
| `resources/views/public/peta-alur-interaktif.blade.php` | Edit | Hapus pemuatan `<link>` CSS legacy ganda, hapus inline `<script>` duplikat (baris 946–1898), ganti inline `onclick` di SVG bubble dengan data attributes semantik. |
| `resources/js/pages/public/peta-alur.js` | Edit | Pasang event listener terstruktur untuk tombol webtoon bubble & halte, perbaiki fungsi navigasi pos agar langsung melaju ke target pos dengan boundary guard aman, pastikan single game loop bersih. |
| `resources/css/pages/public/peta-alur.css` | Review / Edit kecil jika diperlukan | Pastikan styling pointer-events dan transisi smooth bubble/mobil sudah optimal. |

---

## 4. Tahapan Eksekusi (Checklist)

- [ ] **Fase 1: Pembersihan Markup Blade (`resources/views/public/peta-alur-interaktif.blade.php`)**
  - Hapus `<link rel="stylesheet" href="{{ asset('css/peta-alur-fullscreen.css') }}...">` (karena sudah dimuat via `@vite`).
  - Ubah elemen tombol di bubble Pos 1, Pos 2, Pos 3 dari inline `onclick="... navigateModalStep(1);"` menjadi elemen semantik dengan data attributes `class="btn-webtoon-action cursor-pointer" data-step-target="2"`, `data-step-target="3"`, `data-step-target="4"`.
  - Hapus blok `<script>` inline dari baris 946 sampai 1898.
- [ ] **Fase 2: Penguatan Logika JavaScript (`resources/js/pages/public/peta-alur.js`)**
  - Pasang event delegation pada canvas/SVG container untuk menangkap klik `.btn-webtoon-action` dan halte checkpoint `.road-checkpoint-group`.
  - Perbaiki fungsi `navigateModalStep` dan `selectModalStep` agar menggunakan batasan `Math.min(4, Math.max(1, targetStep))`.
  - Pastikan `driveCarToX` menggerakkan mobil ke koordinat pos tujuan (`280`, `880`, `1480`, `2080`) dan kamera mengikuti secara konsisten tanpa tersentak.
- [ ] **Fase 3: Build & Quality Gate**
  - Jalankan build Vite: `npm run build`
  - Jalankan format check Pint: `./vendor/bin/pint --test`
  - Jalankan test PHPUnit: `./vendor/bin/phpunit`
- [ ] **Fase 4: Verifikasi & Laporan Hasil**
  - Verifikasi bahwa klik "Lanjut Pos 2" dari Pos 1 melajukan mobil ke kanan (x = 880) dan kamera mengikuti dengan benar.
  - Verifikasi bahwa klik "Lanjut Pos 3" melajukan mobil ke Pos 3 (x = 1480).
  - Verifikasi bahwa klik "Lanjut Pos 4" melajukan mobil ke Pos 4 (x = 2080).
  - Pastikan efek suara, steering analog, dan fitur lainnya tetap berfungsi normal.

---

## 5. Pertanyaan & Konfirmasi ke Pengguna

Sebelum mengeksekusi perubahan kode:
1. Apakah Anda menyetujui penghapusan script inline duplikat di file Blade dan pemusatan seluruh logika JS interaktif ke `resources/js/pages/public/peta-alur.js` sesuai aturan `AGENTS.md`?
2. Apakah rencana tahapan di atas sudah sesuai dengan ekspektasi Anda?
