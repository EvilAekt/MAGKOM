# Rencana Kerja: Pembuatan Halaman Penuh Mandiri (Dedicated Full Page 100vw x 100vh) Peta Alur Interaktif

Dokumen rencana kerja ini merespons keputusan pengguna untuk membuat **Halaman Penuh Mandiri (*Dedicated Full Page*)** bagi simulator peta alur magang interaktif. Hal ini bertujuan menghilangkan seluruh batasan ruang pop-up yang sempit dan terpotong di layar laptop, sehingga diorama animasi lanskap Sleman, Gunung Merapi, awan melayang, mobil dinas sinematik, karakter bergerak, sound FX, dan Floating Mission HUD dapat dinikmati dalam resolusi panorama penuh `100vw x 100vh` tanpa gangguan.

---

## 1. Tujuan
1. **Menghilangkan Batasan Pop-Up yang Terpotong**:
   - Memindahkan pengalaman simulator interaktif dari jendela modal pop-up yang sempit menjadi **Halaman Penuh Layar Mandiri (*Dedicated Full Viewport Page*)** di rute `/peta-alur`.
   - Mengisi 100% lebar dan tinggi layar browser (`100vw × 100vh`) secara responsif dan bebas terpotong di seluruh resolusi layar (laptop 1366x768, monitor 1920x1080, tablet, hingga smartphone).
2. **Kenyamanan Eksplorasi Penuh (*Maximum Immersion & WOW-Factor*)**:
   - Kanvas panorama diorama Sleman membentang luas tanpa terhimpit dialog modal.
   - Karakter animasi (mengetik laptop, stempel berkas, diskusi mentor ASN, selebrasi wisuda kelulusan) dan mobil dinas berlampu sorot tampil leluasa dengan panggung visual yang lega.
   - Floating Mission HUD melayang elegan di sisi layar dengan opsi minimize/expand.
3. **Navigasi Mulus & Integrasi dengan Landing Page**:
   - Tombol **"Peta Alur Interaktif"** pada Landing Page langsung mengarahkan pengguna ke halaman mandiri `/peta-alur`.
   - Pada halaman penuh, disediakan tombol navigasi **"← Kembali ke Beranda"** serta tombol pintas **"Mulai Pendaftaran 🚀"**.
   - Sound FX (Web Audio API Synthesizer) dan Autoplay Tour tetap bekerja optimal.

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi:
- **Asumsi 1**: Rute baru yang digunakan adalah `GET /peta-alur` dengan penamaan route `peta-alur`.
- **Asumsi 2**: Rute panduan teks komprehensif eksisting `GET /panduan-alur` tetap dipertahankan untuk kebutuhan informasi administratif/aturan bisnis (zero regression).
- **Asumsi 3**: Modal partial `_peta_alur_modal.blade.php` tetap dipertahankan untuk kompatibilitas test suite dan fallback cepat jika dibutuhkan.
- **Asumsi 4**: Halaman mandiri ini tidak menggunakan wrapper header/footer website standar yang tebal, melainkan menggunakan layout khusus immersive dashboard (`layouts.simulator` atau view standalone mandiri) agar kanvas simulator benar-benar mengisi `100vw x 100vh` penuh.

---

## 3. Pendekatan Teknis & Perbandingan Arsitektur

| Kriteria | Opsi A: Dedicated Full Page Layout 100vw x 100vh (Rekomendasi) | Opsi B: Memperbesar Modal Menjadi Fullscreen Native |
| :--- | :--- | :--- |
| **Pengalaman Visual** | **100% Bebas Terpotong**; menggunakan URL mandiri `/peta-alur`, browser address bar alami, dan seluruh monitor menjadi kanvas game simulator. | Masih terikat event modal JavaScript dan backdrop overlay gelap di belakangnya. |
| **Kenyamanan Navigasi** | Memiliki URL mandiri yang dapat di-bookmark dan dibagikan langsung ke peserta/sekolah/kampus. | Tidak bisa di-share langsung lewat URL spesifik tanpa parameter hash modal. |
| **Responsivitas Layar** | Kanvas dan HUD menyesuaikan `100vh` secara dinamis dengan CSS Flex/Grid penuh. | Rentan terhimpit batasan `max-height` modal dialog di layar laptop kecil. |
| **Rekomendasi** | **Dipilih Opsi A (Sesuai Pilihan Pengguna)**. | Tidak dipilih. |

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `routes/web.php` | Modify | Tambahkan rute publik `Route::get('/peta-alur', [AlurMagangController::class, 'petaInteraktif'])->name('peta-alur');`. |
| `app/Http/Controllers/Public/AlurMagangController.php` | Modify | Tambahkan method `petaInteraktif()` yang me-render view `public.peta-alur-interaktif`. |
| `resources/views/public/peta-alur-interaktif.blade.php` | Create | Halaman penuh mandiri (100vw x 100vh) yang memuat diorama panorama Sleman 16:9, Floating Mission HUD, Audio FX Controller, Autoplay Tour, dan tombol kembali ke beranda. |
| `resources/views/public/landing.blade.php` | Modify | Ubah tombol "Peta Alur Interaktif" agar mengarahkan link langsung ke `route('peta-alur')`. |
| `public/css/peta-alur-fullscreen.css` | Create | Styling khusus halaman penuh (100vw x 100vh viewport, responsive HUD dock, zero scrollbar overflow). |
| `tests/Feature/LandingPagePetaAlurModalTest.php` | Modify | Perbarui test untuk memvalidasi keberadaan tautan `route('peta-alur')` pada landing page dan respons status 200 pada halaman mandiri `/peta-alur`. |
| `tests/Feature/PetaAlurPageTest.php` | Create | Test suite Feature untuk memastikan rute `/peta-alur` dapat diakses publik, memuat seluruh scene SVG panorama, audio toggle, dan HUD misi. |

---

## 5. Langkah Implementasi Terstruktur

### Tahap 1: Konfigurasi Routing & Controller
- [x] Buka [routes/web.php](file:///d:/MAGKOM/MAGKOM/routes/web.php).
- [x] Daftarkan rute baru:
  ```php
  Route::get('/peta-alur', [AlurMagangController::class, 'petaInteraktif'])->name('peta-alur');
  ```
- [x] Buka [app/Http/Controllers/Public/AlurMagangController.php](file:///d:/MAGKOM/MAGKOM/app/Http/Controllers/Public/AlurMagangController.php).
- [x] Tambahkan method:
  ```php
  public function petaInteraktif(): \Illuminate\Contracts\View\View
  {
      return view('public.peta-alur-interaktif');
  }
  ```

### Tahap 2: Pembuatan View Halaman Penuh Mandiri (100vw x 100vh)
- [x] Buat file baru [resources/views/public/peta-alur-interaktif.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/peta-alur-interaktif.blade.php):
  - Memuat struktur HTML mandiri dengan meta tags SEO dan judul `"Peta Alur Interaktif - Diskominfo Sleman"`.
  - Topbar navigasi Sleman Smart City: Tombol `"← Kembali ke Beranda"`, filter jenjang (Semua/SMK/Mahasiswa), filter tipe (Mandiri/Kelompok), filter bulan, tombol `"Audio FX"`, dan tombol `"Tour Otomatis"`.
  - Viewport kanvas panorama 16:9 yang membentang luas tanpa terpotong.
  - Floating Mission HUD yang melayang anggun di sisi kanan dengan tombol minimize/expand.
  - Web Audio Synthesizer (engine whoosh, chime ding, fanfare, dan ambient synth).
  - Bottom tab bar timeline 4 tahap dengan status langkah.

### Tahap 3: Pembuatan Styling CSS Halaman Penuh
- [x] Buat file stylesheet baru [public/css/peta-alur-fullscreen.css](file:///d:/MAGKOM/MAGKOM/public/css/peta-alur-fullscreen.css):
  - Atur `body { margin: 0; width: 100vw; height: 100vh; overflow: hidden; background: #060c17; font-family: 'Lexend', sans-serif; }`.
  - Tata letak `display: flex; flex-direction: column; height: 100vh;`.
  - Kanvas SVG `flex: 1; width: 100%; height: 100%; object-fit: contain;` sehingga di layar monitor mana pun grafis proporsional dan tidak akan pernah terpotong.
  - Floating Mission HUD glassmorphism dengan scrollbar internal yang mulus.

### Tahap 4: Penyesuaian Tombol pada Landing Page
- [x] Buka [resources/views/public/landing.blade.php](file:///d:/MAGKOM/MAGKOM/resources/views/public/landing.blade.php).
- [x] Perbarui tombol "Peta Alur Interaktif" di section tahapan magang:
  ```html
  <a href="{{ route('peta-alur') }}" class="btn-tahapan-download" title="Buka Simulasi Peta Alur Interaktif Layar Penuh">
      ...
      <span>Peta Alur Interaktif</span>
  </a>
  ```

### Tahap 5: Pengujian Otomatis & Verifikasi Visual
- [x] Buat file test baru [tests/Feature/PetaAlurPageTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/PetaAlurPageTest.php):
  - Uji akses publik HTTP 200 pada URL `/peta-alur`.
  - Verifikasi seluruh elemen kanvas panorama (`modalRoadCanvas`, `modalMerapiBackdrop`, `modalRoadCarGroup`, `modalCharacterScene`, dll).
  - Verifikasi elemen audio controller dan floating mission HUD.
- [x] Perbarui [tests/Feature/LandingPagePetaAlurModalTest.php](file:///d:/MAGKOM/MAGKOM/tests/Feature/LandingPagePetaAlurModalTest.php) agar tetap kompatibel.
- [x] Jalankan test suite:
  - `php artisan test --filter=PetaAlurPageTest`
  - `php artisan test --filter=LandingPagePetaAlurModalTest`
  - `php artisan test --filter=AlurMagang`
- [x] Uji responsivitas di peramban via `http://magkom.test/peta-alur`.

---

## 6. Kriteria Selesai (Definition of Done)
- [x] Terdapat rute baru `/peta-alur` yang dapat diakses langsung oleh publik.
- [x] Halaman `/peta-alur` tampil dalam format layar penuh 100% monitor (`100vw x 100vh`) tanpa batasan pop-up dan sama sekali tidak ada bagian animasi yang terpotong.
- [x] Tombol pada landing page langsung membuka halaman penuh ini.
- [x] Terdapat tombol `"← Kembali ke Beranda"` di pojok kiri atas untuk navigasi yang nyaman.
- [x] Seluruh fitur animasi (Merapi, awan melayang, mobil berlampu sorot, karakter bergerak 4 tahap, kamera auto-zoom, Web Audio FX, checklist interaktif di Mission HUD, dan Tour Otomatis) berfungsi 100% sempurna.
- [x] Seluruh automated test lulus 100% hijau.
