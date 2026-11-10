# Rencana Kerja: Navigasi Scroll Langsung ke Tahapan Kanan dan Penghapusan Pop-Up Modal Peta Alur di Landing Page

Dokumen ini merupakan rencana kerja teknis untuk mengarahkan fitur/menu navigasi **"Peta Alur"** agar langsung melakukan *smooth scrolling* ke bagian alur tahapan interaktif di sisi kanan halaman (komponen *TahapanRoad* / `#tahapanRoadWrapper`), serta **menghapus komponen pop-up modal peta alur** (`_peta_alur_modal.blade.php`) secara tuntas dari Landing Page.

---

## 1. Tujuan

1. **Navigasi Langsung ke Tahapan Kanan (Smooth Scroll)**:
   - Ketika pengguna mengklik menu **"Peta Alur"** di navbar atas (tangkapan layar Gambar 1) maupun tombol **"Peta Alur Interaktif"** pada section tahapan (tangkapan layar Gambar 2), halaman akan langsung melakukan *smooth scrolling* ke visualisasi alur tahapan di sisi kanan (`#tahapanRoadWrapper` / kartu tahap 01 Registrasi).
2. **Penghapusan Komponen Pop-Up Modal (Tangkapan Layar Gambar 3)**:
   - Menghapus pemanggilan pop-up modal (`#petaAlurModal`) dari Landing Page (`resources/views/public/landing.blade.php`).
   - Menghapus file partial modal `resources/views/public/partials/_peta_alur_modal.blade.php` serta membersihkan aset CSS/JS modal terkait yang sudah tidak digunakan di landing page.
   - Menghapus event handler `onclick="openPetaAlurModal(event)"` di navbar dan elemen terkait.
3. **Penyelarasan Pengujian Otomatis**:
   - Memperbarui test suite [`tests/Feature/LandingPagePetaAlurModalTest.php`](file:///d:/MAGKOM/MAGKOM/tests/Feature/LandingPagePetaAlurModalTest.php) agar memverifikasi bahwa menu "Peta Alur" mengarah ke `#tahapanRoadWrapper` dan memastikan pop-up modal (`#petaAlurModal`) sudah tidak lagi di-render pada landing page (`assertDontSee('id="petaAlurModal"')`).

---

## 2. Asumsi & Analisis Teknis

- **Asumsi 1**: Fitur simulasi alur mandiri di halaman penuh tersendiri (`/peta-alur` via `resources/views/public/peta-alur-interaktif.blade.php` dan `/panduan-alur`) tetap dipertahankan untuk kebutuhan halaman panduan alur mandiri jika diakses via route langsung.
- **Asumsi 2**: Pada Landing Page, fokus pengguna diarahkan langsung ke komponen *TahapanRoad SVG Spline Engine* yang sudah ada di sisi kanan section `#tahapan` (animasi mobil berjalan di jalan meliuk 4 tahap).
- **Asumsi 3**: Seluruh aturan ketat `AGENTS.md` dipatuhi: tidak menyisipkan inline script/style di Blade, menjaga arsitektur Vite dan Vanilla JS di `resources/js/`.

---

## 3. Pendekatan Teknis

### Opsi A: Mempertahankan file modal tapi menyembunyikannya (Hanya display: none)
- *Kelemahan*: Menimbulkan *dead code*, beban DOM ekstra di landing page (~1.200 baris HTML/SVG modal), dan tetap berisiko tidak sengaja terpanggil.

### Opsi B: Penghapusan Tuntas Modal dari Landing Page & Transisi ke Anchor Scroll (Direkomendasikan)
- *Keunggulan*:
  - Mengurangi ukuran payload HTML landing page secara signifikan (menghapus ~1.300 baris DOM modal).
  - Meningkatkan performa *Core Web Vitals* dan waktu render Landing Page.
  - Memanfaatkan perilaku anchor *smooth scroll* native (`href="#tahapanRoadWrapper"`) yang dapat ditingkatkan dengan JavaScript handler agar fokus ke kartu checkpoint tahap 01.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| [`resources/views/public/landing.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/public/landing.blade.php) | **Modify** | 1. Ubah menu navbar "Peta Alur" dari `route('panduan-alur')` dengan `onclick="openPetaAlurModal(event)"` menjadi `href="#tahapanRoadWrapper"`.<br>2. Ubah tombol "Peta Alur Interaktif" di sisi kiri tahapan menjadi anchor `href="#tahapanRoadWrapper"`.<br>3. Hapus `@include('public.partials._peta_alur_modal')` di bagian bawah.<br>4. Hapus pemanggilan CSS `landing-peta-alur-modal.css`. |
| [`resources/js/pages/public/landing.js`](file:///d:/MAGKOM/MAGKOM/resources/js/pages/public/landing.js) | **Modify** | Tambahkan smooth scroll controller terarah untuk anchor `#tahapanRoadWrapper` sehingga mobil animasi dan tahapan tahap 01 langsung tersorot dengan halus saat menu Peta Alur diklik. |
| [`public/js/landing-interactive.js`](file:///d:/MAGKOM/MAGKOM/public/js/landing-interactive.js) | **Modify** | Sinkronkan controller scroll dengan `landing.js`. |
| [`resources/views/public/partials/_peta_alur_modal.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/public/partials/_peta_alur_modal.blade.php) | **Delete** | Hapus file pop-up modal karena sudah tidak digunakan lagi. |
| [`tests/Feature/LandingPagePetaAlurModalTest.php`](file:///d:/MAGKOM/MAGKOM/tests/Feature/LandingPagePetaAlurModalTest.php) | **Modify** | Sesuaikan assertion pengujian: pastikan menu Peta Alur mengarah ke anchor `#tahapanRoadWrapper`, tombol scroll berfungsi, dan pop-up modal `petaAlurModal` tidak lagi muncul di Landing Page (`assertDontSee`). |

---

## 5. Langkah Implementasi Terperinci

### Tahap 1: Pembaruan Navigasi & Link Anchor pada Landing Page
- [ ] **Langkah 1.1**: Buka [`resources/views/public/landing.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/public/landing.blade.php).
  - Pada baris 54 (Navbar):
    - Ubah:
      ```html
      <a href="{{ route('panduan-alur') }}" class="landing-nav-link" onclick="if(typeof openPetaAlurModal === 'function') { openPetaAlurModal(event); }">Peta Alur</a>
      ```
    - Menjadi:
      ```html
      <a href="#tahapanRoadWrapper" class="landing-nav-link">Peta Alur</a>
      ```
  - Pada baris 533 (Tombol aksi tahapan):
    - Ubah tombol "Peta Alur Interaktif":
      ```html
      <a href="#tahapanRoadWrapper" class="btn-tahapan-download" style="background: rgba(7, 81, 255, 0.08); color: var(--primary-accent); border: 1.5px solid rgba(7, 81, 255, 0.25); cursor: pointer; text-decoration: none;" title="Lihat Tahapan Alur Pendaftaran Magang">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.4rem; vertical-align: middle;">
              <circle cx="12" cy="12" r="10"></circle>
              <polygon points="12 8 8 12 12 16 12 8"></polygon>
          </svg>
          Peta Alur Interaktif
      </a>
      ```

### Tahap 2: Penghapusan Pop-Up Modal dari Landing Page
- [x] **Langkah 2.1**: Pada [`resources/views/public/landing.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/public/landing.blade.php):
  - Hapus tag stylesheet `landing-peta-alur-modal.css` di baris 29 & baris 33 (directive `@vite`).
  - Hapus `@include('public.partials._peta_alur_modal')` pada baris 1160.
- [x] **Langkah 2.2**: Hapus file partial modal [`resources/views/public/partials/_peta_alur_modal.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/public/partials/_peta_alur_modal.blade.php).

### Tahap 3: Peningkatan Animasi Smooth Scroll ke Tahapan Kanan
- [x] **Langkah 3.1**: Pada [`resources/js/pages/public/landing.js`](file:///d:/MAGKOM/MAGKOM/resources/js/pages/public/landing.js) dan [`public/js/landing-interactive.js`](file:///d:/MAGKOM/MAGKOM/public/js/landing-interactive.js):
  - Tambahkan penanganan klik untuk link anchor `#tahapanRoadWrapper`:
    - Saat diklik, lakukan *smooth scroll* terhitung presisi ke posisi elemen `#tahapanRoadWrapper` dengan memperhitungkan *offset* tinggi *sticky navbar* (`topbarHeight`).
    - Jalankan fungsi animasi penyorotan (*highlight ripple*) pada kartu tahap 01 atau trigger update mobil `initTahapanRoad()`.

### Tahap 4: Build Aset & Verifikasi Testing
- [x] **Langkah 4.1**: Jalankan kompilasi aset:
  - `$env:PATH = "D:\laragon\bin\nodejs\node-v22;$env:PATH"; npm run build`
- [x] **Langkah 4.2**: Perbarui assertion di [`tests/Feature/LandingPagePetaAlurModalTest.php`](file:///d:/MAGKOM/MAGKOM/tests/Feature/LandingPagePetaAlurModalTest.php):
  - Verifikasi menu navbar memuat `href="#tahapanRoadWrapper"`.
  - Verifikasi bahwa modal `id="petaAlurModal"` dan backdrop modal sudah **tidak lagi di-render** (`$response->assertDontSee('id="petaAlurModal"')`).
- [x] **Langkah 4.3**: Jalankan seluruh feature test:
  - `php artisan test tests/Feature/LandingPagePetaAlurModalTest.php`
  - `php artisan test tests/Feature/AlurMagangPageTest.php`

---

## 6. Strategi Testing

1. **Automated Feature Test**:
   - `php artisan test tests/Feature/LandingPagePetaAlurModalTest.php` &rarr; Memastikan respons HTTP 200, menu Peta Alur mengarah ke `#tahapanRoadWrapper`, dan modal dialog `petaAlurModal` tidak ada di DOM.
   - `php artisan test tests/Feature/AlurMagangPageTest.php` &rarr; Memastikan halaman mandiri `/panduan-alur` tetap berjalan normal tanpa terdampak penghapusan modal di landing page.
2. **Pengujian Browser & Interaksi**:
   - Klik menu "Peta Alur" di navbar &rarr; Halaman langsung meluncur mulus ke sisi kanan tahapan pendaftaran magang.
   - Tidak ada pop-up atau modal apa pun yang terbuka di layar.
   - Console browser bebas dari error `openPetaAlurModal is not defined`.

---

## 7. Risiko & Mitigasi

- **Risiko 1**: Potensi error JavaScript `openPetaAlurModal is not defined` jika masih ada kode lama yang memanggil fungsi tersebut.
  - *Mitigasi*: Telusuri dan bersihkan seluruh referensi panggilan `openPetaAlurModal` di Landing Page dan script pendukungnya.
- **Risiko 2**: Posisi scroll terhalang oleh *sticky header* navbar (offset overlap).
  - *Mitigasi*: Berikan `scroll-margin-top: 80px` pada CSS `#tahapanRoadWrapper` atau hitung offset scroll secara presisi di JavaScript.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Menu "Peta Alur" di navbar dan tombol di section tahapan langsung melakukan smooth scroll ke tahapan alur di sisi kanan (`#tahapanRoadWrapper`).
- [x] Pop-up modal peta alur dihapus sepenuhnya dari Landing Page dan tidak lagi muncul.
- [x] Bundle aset Vite berhasil dibangun ulang tanpa dependensi stylesheet modal yang tidak terpakai.
- [x] Seluruh automated feature tests (`LandingPagePetaAlurModalTest` dan `AlurMagangPageTest`) lulus 100% tanpa error.
