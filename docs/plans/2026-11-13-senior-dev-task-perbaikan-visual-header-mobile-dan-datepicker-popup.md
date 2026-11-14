# 📋 SENIOR DEVELOPER TECHNICAL DIRECTIVE & PRD
## Task: Perbaikan Visual & Akurasi Geometris Header Mobile (Tanda 'X' & Drawer Menu) dan Datepicker Popup (Tanggal Terpotong)

- **Author**: Senior Lead Developer (Antigravity Architecture Team)
- **Assignee**: Junior Developer / AI Model (Gemini 3.8 Flash / Autonomous Code Agent)
- **Dokumen Referensi Bukti**: 
  - `media_1794561992811_0ecd22e3.png` (Tanda 'X' & Kotak Drawer Tidak di Tengah & Anomali Layout Drawer)
  - `media_1794562108429_5b96bc81.png` (Datepicker Popup Tanggal 29-31 Terpotong di Layar Bawah)
- **Target File yang Perlu Dimodifikasi**:
  1. [`resources/views/public/landing.blade.php`](file:///g:/MAGKOM/resources/views/public/landing.blade.php) (Markup Ikon SVG Presisi Matematis)
  2. [`resources/css/pages/public/landing.css`](file:///g:/MAGKOM/resources/css/pages/public/landing.css) (CSS Toggle Button & Estetika Drawer)
  3. [`resources/js/pages/pembimbing/tugas-create.js`](file:///g:/MAGKOM/resources/js/pages/pembimbing/tugas-create.js) (Logika Dynamic Positioning Popup)
  4. [`resources/js/pages/pembimbing/tugas-edit.js`](file:///g:/MAGKOM/resources/js/pages/pembimbing/tugas-edit.js) (Logika Dynamic Positioning Popup Edit)
  5. [`resources/css/pages/pembimbing/tugas-create.css`](file:///g:/MAGKOM/resources/css/pages/pembimbing/tugas-create.css) (CSS Compact Calendar Sizing)
- **Status Dokumen**: ⏳ **DRAFT PLAN (Menunggu Persetujuan User Sebelum Eksekusi)**
- **Tanggal**: 13 November 2026
- **Klasifikasi**: UI/UX Defect Remediation & Precision Geometry Alignment

---

## 🎯 1. Ringkasan Eksekutif (TL;DR untuk Junior Developer / AI)

Dokumen ini adalah panduan teknis langkah demi langkah (**PRD & Implementation Guide**) yang dirancang agar Junior Developer atau AI model ringan (seperti **Gemini 3.8 Flash**) dapat memahami akar masalah dan mengeksekusi perbaikan tanpa salah tafsir dan tanpa menyebabkan regresi.

Terdapat **2 area masalah visual utama** yang dilaporkan pengguna pada aplikasi:

```
[MASALAH 1] Header Mobile (media_1794561992811_0ecd22e3.png)
├── Tanda 'X' pada tombol toggle tidak di tengah kotak (off-center ke kanan atas).
├── Kotak toggle memiliki garis border abu-abu tebal yang kaku dan tidak menyatu dengan header.
└── Drawer menu mobile terpotong garis kuning 3px yang kasar di bagian bawah, tombol login redup, dan kurang ruang napas.

[MASALAH 2] Datepicker Popup Tenggat Tugas (media_1794562108429_5b96bc81.png)
├── Tanggal baris terakhir (29, 30, 31) terpotong oleh tepi bawah layar / taskbar Windows.
└── Kalender terlalu jangkung (~390px) dan logika penentuan posisi 'flip' gagal jika ruang atas maupun bawah sama-sama terbatas pada resolusi laptop (tinggi viewport ≤ 768px).
```

---

## 🔬 2. Analisis Mendalam Akar Masalah (Root Cause Analysis / RCA)

### 2.1 Mengapa Tanda 'X' Tidak Berada di Tengah Kotak? (Bukti: `media_1794561992811_0ecd22e3.png`)
1. **Asimetri Pseudo-Element CSS**:
   Pada implementasi sebelumnya, tanda 'X' dibuat dari 3 garis CSS menggunakan `.hamburger-inner`, `.hamburger-inner::before`, dan `.hamburger-inner::after`.
   - `::before` diposisikan dengan `top: -7px`.
   - `::after` diposisikan dengan `bottom: -7px`.
   - Saat state `.is-active` aktif, `::before` ditransformasikan `translateY(7px) rotate(45deg)`, sedangkan `::after` ditransformasikan `translateY(-7px) rotate(-45deg)`.
   - Karena satu elemen menggunakan acuan `top` dan elemen lainnya menggunakan acuan `bottom`, titik sumbu rotasi (*transform-origin*) menjadi asimetris sebesar `1.5px - 2px`.
2. **Font Baseline Shift pada `inline-block`**:
   Wadah `.hamburger-box` bertipe `display: inline-block`. Dalam browser engine berbasis Chromium/WebKit, elemen `inline-block` di dalam flex container terkena pengaruh *vertical baseline alignment*, yang mendorong garis tengah turun sebanyak 1-2px, sehingga persilangan diagonal 'X' tampak melompat ke kanan atas (*off-center*).
3. **Solusi Standar Industri**:
   Gantikan trik pseudo-element CSS dengan **SVG Icon presisi matematis** (atau Flexbox Dual-Line SVG). SVG memiliki `viewBox="0 0 24 24"` dengan garis diagonal yang berpotongan eksak di titik pusat `(12, 12)`. Tombol `.landing-mobile-toggle` menggunakan `display: inline-flex; align-items: center; justify-content: center;` sehingga ikon **dijamin 100% berada tepat di titik tengah geometris**, di semua browser dan semua skala resolusi.

---

### 2.2 Mengapa Drawer Menu Terlihat Canggung & Terpotong Kasar?
1. **Garis Kuning 3px Melintang Kasar**:
   Aturan `border-bottom: 3px solid var(--gold-bright, #FFCB05)` membuat drawer tampak seperti kotak yang terpenggal tiba-tiba di tengah halaman layar smartphone.
2. **Kontras Tombol Login Rendah**:
   Tombol "Login" menggunakan warna dasar transparan gelap dengan teks kuning tipis. Di atas latar belakang drawer yang juga biru dongker gelap, tombol tersebut tampak tenggelam.
3. **Solusi Desain**:
   - Hilangkan border kuning bawah yang kaku. Ganti dengan sudut bawah membulat elegan (`border-radius: 0 0 20px 20px`) dan bayangan lembut multi-layer (`box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.5)`).
   - Ubah tombol "Login" menjadi gaya *modern glass pill*: border putih transparan `1.5px solid rgba(255, 255, 255, 0.35)`, teks putih cerah `font-weight: 700`, dan target sentuh 46px.
   - Tambahkan padding bawah 24px di bawah tombol "Daftar Magang" untuk kenyamanan sentuhan jari (*thumb-friendly*).

---

### 2.3 Mengapa Tanggal 29-31 pada Datepicker Terpotong? (Bukti: `media_1794562108429_5b96bc81.png`)
1. **Kegagalan Logika Cabang Penentuan Posisi**:
   Kode saat ini pada `tugas-create.js`:
   ```javascript
   if (spaceBelow < popupHeight && spaceAbove >= popupHeight) {
       top = rect.top - popupHeight - 8;
   } else if (spaceBelow < popupHeight) {
       top = Math.max(8, window.innerHeight - popupHeight - 16);
   } else {
       top = rect.bottom + 8;
   }
   ```
   Pada laptop standar (resolusi 1366×768), tinggi viewport browser bersih hanya sekitar **600px - 650px** (karena terpotong titlebar Windows, taskbar, dan tab browser).
   Ketika input tenggat tugas berada di tengah layar (misal `rect.top = 350px`), maka:
   - `spaceBelow` = $650 - 390 - 16 = 244\text{px}$ (kurang dari popupHeight 390px).
   - `spaceAbove` = $350 - 16 = 334\text{px}$ (juga KURANG dari popupHeight 390px!).
   Akibatnya, kondisi pertama `spaceAbove >= popupHeight` bernilai **FALSE**.
   Meskipun masuk ke `else if`, jika `window.innerHeight - popupHeight - 16` dihitung sementara `popup.offsetHeight` terbaca saat render awal sebelum font termuat utuh (misal terbaca 340px padahal tinggi aktual 395px), nilai `top` yang dihasilkan tetap menyebabkan baris ke-5 (tanggal 29, 30) terdorong ke bawah melewati batas bawah viewport!
2. **Dimensi Kalender yang Terlalu Jangkung**:
   Tinggi popup saat ini mencapai **390px - 410px** karena padding kontainer besar (`20px 14px 12px`) dan sel tanggal berukuran 38×38px.
3. **Solusi Terpadu**:
   - **Logika Algoritma Baru**: Bandingkan `spaceAbove` vs `spaceBelow`. Tempatkan popup di sisi yang memiliki ruang **LEBIH BESAR**.
   - **Kompensasi Batas Atas/Bawah**: Jika terpaksa dibuka ke bawah, klem `top` agar `top + popupHeight <= window.innerHeight - 16`. Jika dibuka ke atas, klem `top >= 16`.
   - **Optimasi Ukuran Kalender (Compact Mode)**: Kurangi padding kontainer menjadi `14px 12px 10px` dan kurangi ukuran sel tanggal menjadi `32px` pada layar dengan tinggi $\le 800\text{px}$. Tinggi total kalender akan turun dari ~395px menjadi **~315px** (menghemat 80px ruang vertikal!), sehingga tanggal 29, 30, 31 dijamin 100% terlihat jelas di semua laptop.

---

## 📐 3. Diagram Perbandingan Desain (Before vs After)

### 3.1 Tombol Toggle Hamburger & Silang 'X'

```
KONDISI SEBELUMNYA (CACAT)              KONDISI TARGET (SEMPURNA)
┌──────────────────────┐               ┌──────────────────────┐
│  Kotak tebal kaku    │               │  Glass Pill Halus    │
│                      │               │                      │
│             ╲  ╱     │ ◄ Off-Center  │         ╲  ╱         │ ◄ Presisi Matematis
│              ╳       │   (Ke Kanan   │          ╳           │   Tepat di Tengah
│             ╱  ╲     │    Atas)      │         ╱  ╲         │   (x: 22, y: 22)
│                      │               │                      │
└──────────────────────┘               └──────────────────────┘
(CSS Pseudo-element Asimetris)          (SVG Icon / Pure Centered Flex)
```

### 3.2 Datepicker Popup Vertical Clamping

```
KONDISI SEBELUMNYA (TERPOTONG)          KONDISI TARGET (UTUH & TERLIHAT)
┌──────────────────────────────┐        ┌──────────────────────────────┐
│ < Nov >                 2026 │        │ < Nov >                 2026 │
│ M  S  S  R  K  J  S          │        │ M  S  S  R  K  J  S          │
│ 1  2  3  4  5  6  7          │        │ 1  2  3  4  5  6  7          │
│ 8  9  10 11 12 13 14         │        │ 8  9  10 11 12 13 14         │
│ 15 16 17 18 19 20 21         │        │ 15 16 17 18 19 20 21         │
│ 22 23 24 25 26 27 28         │        │ 22 23 24 25 26 27 28         │
╞══════════════════════════════╡        │ 29 30                        │ ◄ Tanggal 29-31
│ 29 30 (TERPOTONG TASKBAR!)   │        └──────────────────────────────┘   TAMPIL UTUH!
└──────────────────────────────┘        ════════════════════════════════ (Batas Taskbar)
```

---

## 🛠️ 4. Spesifikasi Perubahan File (File Modification Table)

| No | File Target | Aksi | Ringkasan Modifikasi Teknis |
| :---: | :--- | :---: | :--- |
| 1 | [`resources/views/public/landing.blade.php`](file:///g:/MAGKOM/resources/views/public/landing.blade.php) | `Modify` | Ganti elemen `.hamburger-box` dengan dual-path SVG icon presisi (`#iconHamburger` dan `#iconCloseX`) yang terpusat matematis di dalam tombol toggle. |
| 2 | [`resources/css/pages/public/landing.css`](file:///g:/MAGKOM/resources/css/pages/public/landing.css) | `Modify` | Perhalus styling `.landing-mobile-toggle`, hapus `border-bottom: 3px solid #FFCB05` pada `.landing-mobile-drawer`, tambahkan `border-radius: 0 0 20px 20px`, perbaiki kontras `.btn-mobile-login`, dan tambah ruang napas tombol CTA. |
| 3 | [`resources/js/pages/pembimbing/tugas-create.js`](file:///g:/MAGKOM/resources/js/pages/pembimbing/tugas-create.js) | `Modify` | Perbaiki fungsi `positionCalendarPopup()` dengan algoritma komparasi ruang (`spaceAbove > spaceBelow`) dan clamping `Math.min(..., window.innerHeight - popupHeight - 16)`. |
| 4 | [`resources/js/pages/pembimbing/tugas-edit.js`](file:///g:/MAGKOM/resources/js/pages/pembimbing/tugas-edit.js) | `Modify` | Terapkan logika algoritma posisi kalender yang identik pada form edit tugas. |
| 5 | [`resources/css/pages/pembimbing/tugas-create.css`](file:///g:/MAGKOM/resources/css/pages/pembimbing/tugas-create.css) | `Modify` | Tambahkan media query compact sizing kalender pada layar dengan tinggi $\le 800\text{px}$ (padding 14px 12px 10px, sel tanggal 32×32px, `max-height: calc(100vh - 32px)`). |

---

## 📝 5. Langkah-Langkah Implementasi Detail (Actionable Checklist)

### Fase 1: Perbaikan Geometris Tombol Toggle & Ikon Silang 'X' (Presisi Matematis)
- [ ] **1.1. Modifikasi Markup Tombol Toggle pada Blade**:
  Buka [`resources/views/public/landing.blade.php`](file:///g:/MAGKOM/resources/views/public/landing.blade.php) pada baris ~216.
  Ganti isi di dalam `<button class="landing-mobile-toggle" ...>`:
  ```html
  <button type="button" 
          class="landing-mobile-toggle" 
          id="landingMobileToggle"
          data-action="toggle-landing-menu" 
          aria-controls="landingMobileDrawer" 
          aria-expanded="false" 
          aria-label="Buka Menu Navigasi">
      <svg class="toggle-icon toggle-icon-menu" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
      </svg>
      <svg class="toggle-icon toggle-icon-close" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
      </svg>
  </button>
  ```
- [ ] **1.2. Modifikasi CSS Toggle Button pada `landing.css`**:
  Buka [`resources/css/pages/public/landing.css`](file:///g:/MAGKOM/resources/css/pages/public/landing.css) pada baris ~406.
  Perbarui aturan tombol toggle:
  ```css
  .landing-mobile-toggle {
      display: none;
      position: relative;
      z-index: 60;
      align-items: center;
      justify-content: center;
      width: 44px;
      height: 44px;
      min-width: 44px;
      min-height: 44px;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.18);
      border-radius: 10px;
      color: #FFFFFF;
      cursor: pointer;
      padding: 0;
      margin-left: 0.5rem;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  }

  .landing-mobile-toggle:hover {
      background: rgba(255, 255, 255, 0.16);
      border-color: rgba(255, 255, 255, 0.35);
  }

  .landing-mobile-toggle .toggle-icon {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      transition: opacity 0.2s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  }

  .landing-mobile-toggle .toggle-icon-menu {
      opacity: 1;
      transform: translate(-50%, -50%) rotate(0deg) scale(1);
      color: #FFFFFF;
  }

  .landing-mobile-toggle .toggle-icon-close {
      opacity: 0;
      transform: translate(-50%, -50%) rotate(-90deg) scale(0.7);
      color: var(--gold-bright, #FFCB05);
  }

  /* State Aktif (Saat Menu Terbuka) */
  .landing-mobile-toggle.is-active {
      background: rgba(255, 203, 5, 0.12);
      border-color: rgba(255, 203, 5, 0.5);
  }

  .landing-mobile-toggle.is-active .toggle-icon-menu {
      opacity: 0;
      transform: translate(-50%, -50%) rotate(90deg) scale(0.7);
  }

  .landing-mobile-toggle.is-active .toggle-icon-close {
      opacity: 1;
      transform: translate(-50%, -50%) rotate(0deg) scale(1);
  }
  ```

---

### Fase 2: Penyempurnaan Estetika Mobile Drawer Menu
- [ ] **2.1. Hapus Cutoff Line & Tambahkan Rounded Elevation pada `landing.css`**:
  Pada [`resources/css/pages/public/landing.css`](file:///g:/MAGKOM/resources/css/pages/public/landing.css) baris ~516:
  ```css
  .landing-mobile-drawer {
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 4.25rem;
      left: 0;
      right: 0;
      background: linear-gradient(180deg, #012b66 0%, #001f4d 100%);
      border-top: 1px solid rgba(255, 255, 255, 0.12);
      border-bottom: none; /* Hilangkan garis kuning 3px yang memotong */
      border-radius: 0 0 24px 24px; /* Sudut membulat lembut */
      box-shadow: 0 24px 48px -12px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.05);
      z-index: 48;
      max-height: calc(100dvh - 4.25rem);
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
      padding: 1.25rem 1.25rem 2rem; /* Padding bawah lebih lega */
      transform: translateY(-12px);
      opacity: 0;
      pointer-events: none;
      visibility: hidden;
      transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease, visibility 0.25s;
  }
  ```
- [ ] **2.2. Perbaiki Kontras Tombol Login & Tombol Daftar**:
  Pada [`resources/css/pages/public/landing.css`](file:///g:/MAGKOM/resources/css/pages/public/landing.css) baris ~595:
  ```css
  .btn-mobile-login {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 46px;
      padding: 0.75rem 1rem;
      border-radius: 12px;
      border: 1.5px solid rgba(255, 255, 255, 0.35);
      background: rgba(255, 255, 255, 0.06);
      color: #FFFFFF; /* Putih bersih, kontras tinggi */
      font-size: 0.975rem;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.2s ease;
  }

  .btn-mobile-login:hover {
      background: rgba(255, 255, 255, 0.15);
      border-color: #FFFFFF;
      color: var(--gold-bright, #FFCB05);
  }

  .btn-mobile-register {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 48px;
      padding: 0.75rem 1rem;
      border-radius: 12px;
      background: var(--gold-bright, #FFCB05);
      color: var(--navy-dark, #02275D);
      font-size: 0.975rem;
      font-weight: 800;
      text-decoration: none;
      box-shadow: 0 4px 16px rgba(255, 203, 5, 0.35);
      transition: all 0.2s ease;
  }
  ```

---

### Fase 3: Perbaikan Posisi & Tampilan Datepicker Tenggat Tugas
- [ ] **3.1. Algoritma Baru pada `resources/js/pages/pembimbing/tugas-create.js`**:
  Perbarui fungsi `positionCalendarPopup()` pada blok kalender tenggat dan kalender jadwal:
  ```javascript
  function positionCalendarPopup() {
      const rect = triggerBtn.getBoundingClientRect();
      const popupWidth = Math.min(358, window.innerWidth - 32);
      let left = rect.left;
      if (left + popupWidth > window.innerWidth - 16) {
          left = window.innerWidth - popupWidth - 16;
      }
      if (left < 16) left = 16;

      const popupHeight = popup.offsetHeight || 330;
      const spaceBelow = window.innerHeight - rect.bottom - 16;
      const spaceAbove = rect.top - 16;

      let top;
      // Jika ruang bawah tidak cukup dan ruang atas lebih luas dari ruang bawah
      if (spaceBelow < popupHeight && spaceAbove > spaceBelow) {
          // Buka ke atas tombol trigger
          top = Math.max(16, rect.top - popupHeight - 8);
      } else {
          // Buka ke bawah tombol trigger, namun pastikan bagian bawah kalender tidak terpotong
          top = Math.max(16, Math.min(rect.bottom + 8, window.innerHeight - popupHeight - 16));
      }

      popup.style.top = `${top}px`;
      popup.style.left = `${left}px`;
      popup.style.width = `${popupWidth}px`;
  }
  ```
- [ ] **3.2. Terapkan Logika yang Sama pada `tugas-edit.js`**:
  Perbarui fungsi `positionCalendarPopup()` pada [`resources/js/pages/pembimbing/tugas-edit.js`](file:///g:/MAGKOM/resources/js/pages/pembimbing/tugas-edit.js).
- [ ] **3.3. CSS Compact Calendar Sizing pada `tugas-create.css`**:
  Pada [`resources/css/pages/pembimbing/tugas-create.css`](file:///g:/MAGKOM/resources/css/pages/pembimbing/tugas-create.css) baris ~1336:
  ```css
  .tugas-calendar-popup {
      position: fixed;
      z-index: 1060;
      width: 358px;
      max-width: calc(100vw - 32px);
      max-height: calc(100vh - 32px);
      overflow-y: auto;
      background: #e9eef7;
      border-radius: 20px;
      box-shadow: 0 12px 36px rgba(0, 0, 0, 0.28), 0 0 0 1px rgba(0, 0, 0, 0.05);
      padding: 16px 14px 14px;
      display: none;
      box-sizing: border-box;
  }

  /* Compact Mode pada Viewport dengan Tinggi Terbatas (Laptop ≤ 800px) */
  @media (max-height: 800px) {
      .tugas-calendar-popup {
          padding: 12px 10px 10px;
          border-radius: 16px;
      }
      .tugas-cal-day {
          width: 32px !important;
          height: 32px !important;
          font-size: 13px !important;
      }
      .tugas-cal-header {
          height: 30px !important;
          margin-bottom: 4px !important;
      }
      .tugas-cal-weekdays {
          margin-bottom: 4px !important;
      }
  }
  ```

---

### Fase 4: Quality Gate & Verifikasi Kompilasi
- [ ] **4.1. Jalankan Vite Build**:
  `npm run build` (Pastikan seluruh CSS dan JS terkompilasi bersih tanpa peringatan).
- [ ] **4.2. Jalankan Code Formatter Check**:
  `./vendor/bin/pint --test` (Pastikan standar PSR-12 tetap 100% terjaga).
- [ ] **4.3. Jalankan PHPUnit Regression Test**:
  `php artisan test` (Pastikan seluruh 926 test cases tetap PASS tanpa ada yang gagal).
- [ ] **4.4. Browser Automation Visual Audit**:
  Gunakan Playwright untuk memvalidasi:
  - Buka landing page pada viewport 375×667.
  - Klik tombol `#landingMobileToggle`.
  - Verifikasi posisi bounding client rect ikon close `toggle-icon-close` berada tepat di titik tengah `(x: 22, y: 22)` relatif terhadap tombol.
  - Verifikasi drawer bawah tidak lagi memiliki garis kuning melintang 3px dan memiliki sudut melengkung 24px.
  - Verifikasi tanggal 29, 30, 31 pada kalender tenggat tampil utuh tanpa terpotong di tepi bawah.

---

## 🛡️ 6. Analisis Risiko & Mitigasi (Risk & Mitigation)

| Potensi Risiko | Tingkat Risiko | Mitigasi yang Ditetapkan |
| :--- | :---: | :--- |
| **Penyusutan Target Sentuh pada Mobile** | Rendah | Tombol toggle tetap dikunci minimal 44×44px sesuai pedoman WCAG 2.1 AA. |
| **Cache Browser Pengguna Menyimpan File Lama** | Sedang | Vite mengelola cache-busting otomatis dengan penamaan hash berkas pada `manifest.json`. Pengguna disarankan melakukan *Hard Refresh* (`Ctrl + F5`). |
| **Kerusakan Logika Pengiriman Form Tugas** | Nihil | Tidak ada input form, nama variabel, atau payload POST yang diubah. Perubahan murni pada perhitungan koordinat visual popover (`style.top` & `style.left`). |
| **Regresi Test Suite PHPUnit** | Nihil | Seluruh assertion Blade dan route contract dipertahankan secara identik. |

---

## ✅ 7. Kriteria Selesai (Definition of Done)

- [ ] Tanda 'X' pada tombol toggle mobile berada tepat 100% di tengah kotak secara optis dan matematis di semua resolusi smartphone.
- [ ] Kotak toggle memiliki visual halus semi-transparan dengan aksen emas cerah saat aktif.
- [ ] Garis kuning kasar 3px di bawah drawer mobile telah dieliminasi dan digantikan oleh sudut melengkung 24px dengan bayangan elegan.
- [ ] Tombol "Login" pada drawer mobile memiliki kontras teks putih bersih yang tegas dan mudah dibaca.
- [ ] Kalender popup tanggal tenggat tugas di halaman pembimbing menampilkan seluruh tanggal (termasuk 29, 30, 31) secara utuh tanpa pernah terpotong oleh batas layar atau taskbar.
- [ ] `npm run build` sukses 100%.
- [ ] `./vendor/bin/pint --test` lolos.
- [ ] `php artisan test` menghasilkan 926 test passed (6.052 assertions).
- [ ] Tidak ada commit atau merge yang dijalankan tanpa instruksi user.

---

> ⚠️ **PERHATIAN UNTUK IMPLEMENTOR**: 
> Dilarang menulis implementasi kode sebelum Pengguna (**User**) menyetujui dokumen perencanaan ini. Tunggu konfirmasi persetujuan resmi.

