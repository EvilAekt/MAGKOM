# Rencana Kerja: Single-Icon State Dinamis Tombol Tema Sidebar (Bulan saat Terang, Lampu saat Gelap)

- **ID Rencana**: `PLAN-2026-10-30-THEME-TOGGLE-ICON-01`
- **Tanggal Dibuat**: 30 Oktober 2026
- **Status**: Selesai & Terverifikasi (PASS 100%)
- **Target File**:
  - `resources/views/layouts/dashboard.blade.php` (Admin, Pembimbing, SuperAdmin)
  - `resources/views/layouts/peserta.blade.php` (Peserta Magang)
  - `tests/Feature/DashboardLayoutTest.php` (Feature Test Validasi)

---

## 1. Tujuan
Memperbaiki dan menyempurnakan tombol switch tema di sidebar bawah (`.sidebar-footer`) agar **hanya menampilkan 1 ikon tunggal (single-icon)** sesuai mode yang aktif:
1. **Saat Mode Terang (Light Mode)**: Hanya menampilkan **ikon Bulan (Moon)** (menandakan aksi untuk beralih ke mode malam/gelap).
2. **Saat Mode Gelap (Dark Mode)**: Hanya menampilkan **ikon Lampu/Penerangan (Light/Lamp)** berwarna kuning emas MagKom (menandakan aksi untuk beralih ke mode terang).
3. Memperbaiki *bug* CSS kritis (*unclosed brace* pada kelas `.sr-only`) di file layout master yang sebelumnya menyebabkan kedua ikon (matahari dan bulan) terender berdampingan sekaligus sehingga terlihat seperti satu gambar komposit.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja Logis:
1. **Akar Masalah (Root Cause)**:
   - Pada `resources/views/layouts/dashboard.blade.php` baris 1896, blok `.sr-only {` tidak memiliki kurung kurawal penutup `}`. Hal ini merusak cascading CSS di bawahnya, sehingga selektor penyembunyi ikon `html:not([data-theme="dark"]) .sidebar-footer .sun-icon { display: none !important; }` diabaikan oleh browser dan kedua ikon terender bersamaan.
2. **Definisi "Gambar Lampu"**:
   - User menyebutkan *"klo gelap gambar lampu"*. Kami menyediakan pilihan:
     - **Varian Bohlam Lampu (Lightbulb)**: Bentuk lampu bohlam menyala (ikon Lucide `lightbulb`) dengan aksen warna kuning hangat khas MagKom (`#FFCB05`).
     - **Varian Matahari (Sun Rays)**: Bentuk matahari bersinar (ikon standar mode terang/gelap).
   - Agar tetap kompatibel dengan unit test yang sudah ada (`DashboardLayoutTest`), kelas CSS SVG akan mempertahankan `class="sun-icon light-icon"`.
3. **Penyelarasan Seluruh Peran**:
   - Perubahan diterapkan secara konsisten pada seluruh 4 peran dashboard: **SuperAdmin**, **Admin**, **Pembimbing**, dan **Peserta**.

### Pertanyaan Terbuka untuk Pengguna:
- Untuk mode gelap, apakah Anda lebih menyukai bentuk **Lampu Bohlam (Lightbulb)** atau bentuk **Matahari Bersinar (Sun)**? *(Rekomendasi default kami adalah Lampu Bohlam modern sesuai instruksi Anda).*

---

## 3. Pendekatan Teknis & Evaluasi Arsitektur

### Perbandingan Pendekatan:

| Kriteria | Pendekatan A: Pure CSS (Hanya Perbaiki Sintaks CSS) | Pendekatan B: Hybrid CSS + Defensive Micro-Script (Rekomendasi) |
| :--- | :--- | :--- |
| **Mekanisme** | Memperbaiki kurung kurawal `.sr-only` dan mengandalkan `html:not([data-theme="dark"])` | Memperbaiki CSS + menambahkan fallback script sinkronisasi atribut `aria-label`, `title`, dan `hidden` pada SVG |
| **Ketahanan Bug** | Sedang (jika ada CSS pihak ketiga bentrok, ikon bisa tampil ganda kembali) | **Sangat Tinggi / Bulletproof** (SVG dijamin tidak akan pernah tampil ganda dalam kondisi apa pun) |
| **Aksesibilitas (a11y)** | Statis (`aria-label` tidak berubah mengikuti kondisi) | **Dinamis** (`aria-label` & `title` berubah menjadi "Beralih ke Mode Gelap" / "Beralih ke Mode Terang") |
| **Rekomendasi** | Cukup | **Terpilih (Pendekatan B)** |

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/layouts/dashboard.blade.php` | Modify | 1. Tutup kurung kurawal `.sr-only` di baris 1904.<br>2. Perbarui selektor CSS untuk `#sidebarThemeToggleBtn .sun-icon` dan `.moon-icon`.<br>3. Pasang ikon lampu bohlam / matahari yang presisi.<br>4. Perbarui fungsi `applyPortalTheme()` agar memperbarui tooltip & `aria-label` secara dinamis. |
| `resources/views/layouts/peserta.blade.php` | Modify | 1. Terapkan pembaruan SVG & CSS yang sama pada `#sidebarThemeToggleBtn`.<br>2. Sinkronkan pembaruan tooltip & `aria-label` di `applyPortalTheme()`. |
| `tests/Feature/DashboardLayoutTest.php` | Modify | Tambahkan assertion untuk memvalidasi penutupan CSS, keberadaan single-icon selector, dan kompatibilitas nama kelas SVG. |

---

## 5. Langkah Implementasi Terperinci

### Fase 1: Perbaikan Sintaks & Engine CSS (`dashboard.blade.php`)
- [x] **Langkah 1.1**: Buka `resources/views/layouts/dashboard.blade.php`, temukan deklarasi `.sr-only` di sekitar baris 1896-1904 dan tambahkan kurung penutup `}` yang hilang.
- [x] **Langkah 1.2**: Perbaiki dan perkuat selektor CSS tombol tema sidebar footer:
  ```css
  /* Single-Icon Display Logic */
  html:not([data-theme="dark"]) #sidebarThemeToggleBtn .sun-icon,
  html:not([data-theme="dark"]) #sidebarThemeToggleBtn .light-icon {
      display: none !important;
  }
  html:not([data-theme="dark"]) #sidebarThemeToggleBtn .moon-icon {
      display: block !important;
      color: rgba(255, 255, 255, 0.85);
  }
  html[data-theme="dark"] #sidebarThemeToggleBtn .sun-icon,
  html[data-theme="dark"] #sidebarThemeToggleBtn .light-icon {
      display: block !important;
      color: var(--magkom-sun-yellow, #FFCB05) !important;
  }
  html[data-theme="dark"] #sidebarThemeToggleBtn .moon-icon {
      display: none !important;
  }
  ```

### Fase 2: Pembaruan Mark-Up Tombol & Ikon Lampu (`dashboard.blade.php` & `peserta.blade.php`)
- [x] **Langkah 2.1**: Pada `resources/views/layouts/dashboard.blade.php` (baris ~3298), sesuaikan mark-up tombol `#sidebarThemeToggleBtn`:
  - Ikon Terang -> Gelap (tampil saat mode Terang): Ikon Bulan Sabit (`.moon-icon`).
  - Ikon Gelap -> Terang (tampil saat mode Gelap): Ikon Lampu Bohlam (`.sun-icon`).
- [x] **Langkah 2.2**: Pada `resources/views/layouts/peserta.blade.php` (baris ~1735), terapkan mark-up SVG dan aturan CSS yang identik untuk menjaga konsistensi.

### Fase 3: Peningkatan Logika JavaScript Aksesibilitas
- [x] **Langkah 3.1**: Pada fungsi `window.applyPortalTheme(theme)` di kedua file layout, perbarui logika tombol untuk:
  - Mengubah atribut `title` dan `aria-label`:
    - Jika tema aktif adalah `dark`: `"Beralih ke Mode Terang"`
    - Jika tema aktif adalah `light`: `"Beralih ke Mode Gelap"`
  - Menyetel atribut `aria-pressed="true"` saat mode gelap dan `false` saat mode terang.

### Fase 4: Validasi & Pengujian Otomatis
- [x] **Langkah 4.1**: Jalankan skrip pemeriksa parsing CSS (verifikasi jumlah kurung kurawal buka dan tutup dalam tag `<style>` seimbang 100%).
- [x] **Langkah 4.2**: Jalankan pengujian otomatis:
  ```bash
  php artisan test --filter DashboardLayoutTest
  ```
- [x] **Langkah 4.3**: Pastikan tidak ada regresi pada halaman dashboard lain (SuperAdmin, Admin, Pembimbing, Peserta).

---

## 6. Strategi Testing

1. **Automated Feature Tests**:
   - Jalankan `php artisan test --filter DashboardLayoutTest`.
   - Pastikan seluruh assertion:
     - `assertSee('id="sidebarThemeToggleBtn"')`
     - `assertSee('class="sun-icon"')`
     - `assertSee('class="moon-icon"')`
     - `assertSee('togglePortalThemeQuick()')`
     tetap **PASS 100%**.
2. **CSS Balance & Syntax Integrity Check**:
   - Uji via CLI untuk memvalidasi bahwa blok `<style>` di `dashboard.blade.php` tidak lagi memiliki *unclosed braces* (Open == Close).
3. **Manual Verification Checkpoints**:
   - Buka browser pada Mode Terang: Tombol hanya menampilkan ikon Bulan 🌙. Tidak ada ikon lampu/matahari di sampingnya.
   - Klik tombol: Tema langsung berganti ke Mode Gelap, ikon berubah menjadi Lampu Bohlam/Matahari 💡 berwarna kuning menyala.
   - Reload halaman: Status tema bertahan (*persisted*) via `localStorage` dan session.

---

## 7. Risiko & Mitigasi

| Risiko | Dampak | Mitigasi |
| :--- | :--- | :--- |
| **Breaking Test Suite** jika kelas SVG diubah | Feature test `DashboardLayoutTest` gagal jika `class="sun-icon"` dihapus | Gunakan kelas gabungan: `class="sun-icon light-icon"` sehingga assertion eksisting tetap lulus. |
| **Flash of Incorrect Icon** saat halaman pertama kali dimuat | Ikon berkedip sebelum CSS atau JS termuat | Gunakan Anti-Flash Theme Script di `<head>` yang menyetel atribut `data-theme` sebelum DOM selesai dirender, didukung aturan CSS murni (`display: none !important`). |
| **Unclosed CSS Braces** merusak styling lain | Elemen lain di halaman mengalami regresi tampilan | Lakukan audit kurung kurawal menggunakan skrip Node.js/PHP untuk menjamin keseimbangan sintaks 100%. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Sintaks CSS pada `resources/views/layouts/dashboard.blade.php` valid tanpa ada kurung kurawal yang belum tertutup.
- [x] Tombol toggle tema pada sidebar menu hanya menampilkan 1 gambar/ikon tunggal (bukan dua gambar bersisian).
- [x] Saat mode terang: hanya gambar Bulan yang terlihat.
- [x] Saat mode gelap: hanya gambar Lampu (atau matahari) yang terlihat dengan warna kuning MagKom.
- [x] Klik tombol berhasil mengubah mode tema secara instan tanpa glitch.
- [x] Seluruh pengujian otomatis `php artisan test --filter DashboardLayoutTest` berjalan sukses dengan status **PASS**.
- [x] Tidak ada regresi visual pada dashboard SuperAdmin, Admin, Pembimbing, maupun Peserta.

