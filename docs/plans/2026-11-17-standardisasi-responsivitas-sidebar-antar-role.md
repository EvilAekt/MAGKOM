# Rencana Kerja Detail: Standarisasi Responsivitas Sidebar Seluruh Dashboard Sesuai Standar Peserta Magang (MagKom)

- **Dokumen Referensi:** PRD MagKom v1.2, Desain Sistem Dashboard MagKom (Sleman Design Tokens), Aturan Frontend MagKom (Rule 3 & 4), `docs/plans/2026-11-17-plan-perbaikan-sidebar-mini-esc-semua-dashboard.md`
- **Target Pembaca / Pelaksana:** Full-Stack Developer & AI Assistant
- **Tingkat Kerumitan:** Menengah (*Responsive Architecture, CSS Scoping, Mobile Drawer State Machine, Cross-Role Consistency*)
- **Status:** **DRAFT PLAN &ndash; MENUNGGU PERSETUJUAN USER**
- **Prinsip Utama:** **Blade Read-Only**. Perubahan diutamakan pada arsitektur CSS di `resources/css/` dan interaktivitas JavaScript di `resources/js/` yang dikompilasi melalui Vite, dengan modifikasi Blade sekecil mungkin hanya untuk class/data-attribute yang benar-benar esensial.

---

## 1. Tujuan

Menyelaraskan dan menstandarisasi responsivitas *sidebar* (laci navigasi samping) pada seluruh portal/peran di MagKom (**Admin, Pembimbing, dan SuperAdmin**) agar memiliki perilaku, estetika, dan kehandalan responsif yang **100% identik** dengan *sidebar* pada portal **Peserta Magang**, yang saat ini telah terbukti responsif secara mulus di berbagai resolusi layar (*desktop*, *tablet*, maupun *smartphone*).

---

## 2. Asumsi & Analisis Akar Masalah (Root Cause Analysis)

### 2.1 Analisis Tangkapan Layar Pengguna
1. **Foto 1 (Sidebar Peserta Magang &ndash; Acuan Standar Responsif)**:
   - Menampilkan *sidebar* Peserta Magang dengan tombol *hamburger toggle* `[ = ]` pada *topbar* (diberi lingkaran kuning).
   - Pada layar sempit/mobile, *sidebar* bekerja sebagai *off-canvas drawer* yang tersembunyi secara rapi di luar layar (`transform: translateX(-100%)`).
   - Saat tombol *toggle* diklik:
     - Laci meluncur mulus ke dalam layar dengan lebar responsif `min(280px, calc(100vw - 36px))`.
     - *Backdrop scrim* semi-transparan muncul dengan efek *blur* (`backdrop-filter: blur(2px)`).
     - *Scroll* halaman latar belakang dikunci (`body.style.overflow = 'hidden'`).
     - Terdapat tombol tutup silang (`&times;`) di kanan atas laci navigasi.
     - Mengklik *backdrop*, tombol silang, link navigasi, atau tombol `Esc` langsung menutup laci secara otomatis.
   - Pada layar *desktop*, tombol *toggle* mengubah *sidebar* menjadi *mini-sidebar* 72px yang proporsional dan bebas kedipan (*zero FOUC*).

2. **Foto 2 (Sidebar Dashboard Administrasi &ndash; Inkonsistensi & Cacat Responsif)**:
   - Menampilkan *sidebar* Admin (`Dashboard Administra...`) dengan tombol *hamburger toggle* `[ = ]` pada *topbar* (diberi lingkaran kuning).
   - Masih menggunakan basis warisan *Gentelella layout* (`layouts/dashboard.blade.php`), yang memiliki sejumlah kelemahan responsif dibanding Peserta:
     - **Tabrakan Breakpoint (Breakpoint Collision)**: Gentelella memakai ambang batas `min-width: 992px` untuk mode *desktop mini*, sementara `dashboard.css` memakai `max-width: 1024px` untuk mode *mobile drawer*. Pada rentang layar `992px` s/d `1024px` (seperti tablet *landscape* / iPad), kedua *rules* saling bertabrakan dan merusak tata letak.
     - **Kebocoran Margin Hardcoded**: Terdapat deklarasi `margin-left: var(--sidebar-width) !important` pada CSS spesifik peran (seperti `pages/admin/dashboard.css`, `pages/pembimbing/dashboard.css`, dan `pages/superadmin/dashboard.css`) yang tidak di-reset di layar mobile (`<= 1024px`), memicu *horizontal scrollbar* dan konten terpotong.
     - **Jebakan Submenu Akordeon di Mobile**: Pada mode Admin dan SuperAdmin yang memiliki menu akordeon/grup (`.nav-group-container`), ketika *drawer* dibuka di layar mobile sementara kelas `nav-sm` tersisa di `body`, submenu dikunci `display: none !important`, sehingga pengguna mobile tidak dapat mengakses menu operasional atau konfigurasi sama sekali.
     - **Anti-Flash Gap (FOUC)**: Script anti-kedip `public/js/theme-init.js` di dalam `<head>` hanya memulihkan status `magkom_peserta_sidebar_collapsed`, sementara status *sidebar* Admin/Pembimbing/SuperAdmin diabaikan. Akibatnya terjadi lonjakan visual (*layout shift/flicker*) saat halaman di-*refresh*.
     - **Ukuran Target Sentuh (Touch Target)**: Tombol *hamburger* `#menu_toggle` pada dashboard berukuran 36px di beberapa breakpoint, belum memenuhi standar aksesibilitas WCAG 2.1 AA (minimal 44px &times; 44px).

---

## 3. Pendekatan Teknis (Evaluasi Opsi Arsitektur)

| Aspek | Opsi A: Standarisasi Berbasis Token & State Machine Terpadu (Direkomendasikan) | Opsi B: Perombakan Total Markup Blade `dashboard.blade.php` |
| :--- | :--- | :--- |
| **Deskripsi** | Menyelaraskan seluruh *breakpoint* CSS (`1024px`), menstandarisasi *state machine* JS (`dashboard.js`), memperbaiki *media query reset*, dan menambahkan anti-flicker pada `theme-init.js` tanpa merombak struktur HTML Blade. | Mengganti seluruh markup `dashboard.blade.php` agar 100% meniru struktur DOM `peserta.blade.php`. |
| **Dampak Risiko** | **Sangat Rendah**: Aman dari regresi backend dan pengujian otomatis (PHPUnit). | **Sangat Tinggi**: Berpotensi merusak 39 *blade view* turunan (Admin, Pembimbing, SuperAdmin) dan mematahkan selektor Gentelella yang diuji PHPUnit. |
| **Kepatuhan Aturan** | **100% Patuh**: Menghormati *Rule 3: Blade Read-Only* dan *Rule 4-5: CSS/JS di resources/*. | Melanggar *Rule 3* (.agents/rules/AGENTS.md) mengenai larangan refactoring Blade massal. |
| **Konsistensi UI/UX** | **Identik 100%**: Pengalaman responsif mobile, tablet, dan desktop menjadi seragam di seluruh portal. | Identik, namun berisiko menimbulkan *breaking changes* pada sub-komponen dashboard. |

> **Rekomendasi:** Dipilih **Opsi A**. Solusi ini menyelesaikan seluruh akar masalah responsivitas secara elegan, tangguh, dan aman tanpa menyentuh struktur internal 39 view turunan.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/css/layouts/dashboard.css` | `Modify` | Harmonisasi *breakpoint* responsif menjadi `1024px`, ukuran laci `min(280px, calc(100vw - 36px))`, target sentuh tombol *toggle* 44x44px, dan normalisasi akordeon mobile. |
| `resources/css/layouts/gentelella.css` | `Modify` | Menyelaraskan breakpoint `@media (min-width: 992px)` ke `1025px` agar tidak terjadi tabrakan aturan dengan `@media (max-width: 1024px)`. |
| `resources/css/layouts/dashboard-layout-tokens.css` | `Modify` | Menyelaraskan token dimensi *sidebar*: `--sidebar-width: 230px`, `--sidebar-collapsed-width: 72px`, dan `--topbar-height: 64px`. |
| `resources/css/pages/admin/dashboard.css` | `Modify` | Tambahkan media query reset pada `@media (max-width: 1024px)` untuk `.gentelella-footer` agar tidak memaksa *margin-left* di mobile. |
| `resources/css/pages/pembimbing/dashboard.css` | `Modify` | Tambahkan media query reset pada `@media (max-width: 1024px)` untuk `.gentelella-footer`. |
| `resources/css/pages/superadmin/dashboard.css` | `Modify` | Tambahkan media query reset pada `@media (max-width: 1024px)` untuk `.top_nav`, `.dashboard-content-area`, dan `.gentelella-footer`. |
| `resources/js/layouts/dashboard.js` | `Modify` | Refaktor fungsi `toggleSidebar()`: implementasi laci mobile `<= 1024px` dengan *body scroll lock*, penutupan otomatis via link klik/ESC/*backdrop*, perbaikan akordeon di mobile, dan sinkronisasi preferensi *desktop collapse*. |
| `public/js/theme-init.js` | `Modify` | Tambahkan pembacaan synchronous status *sidebar collapsed* untuk peran non-peserta di `<head>` guna mencegah FOUC / kedipan saat navigasi. |
| `resources/views/layouts/dashboard.blade.php` | `Modify` (Minimal) | Tambahkan class semantik `sidebar-brand-text` pada container teks judul brand Sleman agar konsisten dengan `peserta.blade.php`. |

---

## 5. Langkah Implementasi Terstruktur

### Fase 1: Standarisasi Breakpoint & Token CSS Utama
- [x] **1.1. Perbarui Token Dimensi di `resources/css/layouts/dashboard-layout-tokens.css`**:
  - Pastikan `--sidebar-width: 230px;`, `--sidebar-collapsed-width: 72px;`, dan `--topbar-height: 64px;` terdefinisi konsisten pada `:root` dan `[data-theme="dark"]`.
- [x] **1.2. Resolusi Konflik Breakpoint di `resources/css/layouts/gentelella.css`**:
  - Ubah batas media query desktop menciut dari `@media (min-width: 992px)` menjadi `@media (min-width: 1025px)`.
  - Pastikan tidak ada aturan desktop yang aktif pada resolusi &le; 1024px.
- [x] **1.3. Sempurnakan Aturan Drawer Mobile di `resources/css/layouts/dashboard.css`**:
  - Pastikan `@media (max-width: 1024px)` menerapkan:
    - *Sidebar off-canvas*: `transform: translateX(-100%); width: min(280px, calc(100vw - 36px)) !important; z-index: 1050;`.
    - *Active drawer*: `transform: translateX(0) !important;`.
    - *Backdrop scrim*: `display: block; opacity: 1; backdrop-filter: blur(2px); z-index: 1040;`.
    - *Tombol close silang*: `.sidebar-drawer-close-btn` tampil `inline-flex` dengan dimensi 44px &times; 44px.
    - *Tombol hamburger*: `#menu_toggle, .topbar-toggle-btn` memiliki dimensi sentuh minimal 44px &times; 44px.
    - *Reset layout utama*: `margin-left: 0 !important; width: 100% !important;` untuk `.right_col`, `.dashboard-content-area`, `.top_nav`, dan `.gentelella-footer`.
- [x] **1.4. Pembebasan Akordeon Submenu di Layar Mobile**:
  - Tambahkan aturan CSS spesifik pada `dashboard.css`:
    ```css
    @media (max-width: 1024px) {
        .dashboard-sidebar .nav-sub-menu {
            display: none;
        }
        .dashboard-sidebar .nav-sub-menu.show {
            display: block !important;
        }
    }
    ```
    sehingga pembukaan grup menu di mobile tidak lagi terblokir oleh kelas desktop `nav-sm`.

### Fase 2: Pembersihan Margin Spesifik Peran (Page-Level CSS Resets)
- [x] **2.1. Reset Footer Admin di `resources/css/pages/admin/dashboard.css`**:
  - Tambahkan blok `@media (max-width: 1024px)` untuk memastikan `.gentelella-footer` memiliki `margin-left: 0 !important; width: 100% !important; max-width: 100% !important;`.
- [x] **2.2. Reset Footer Pembimbing di `resources/css/pages/pembimbing/dashboard.css`**:
  - Tambahkan blok `@media (max-width: 1024px)` untuk me-reset margin dan lebar `.gentelella-footer`.
- [x] **2.3. Reset Topbar & Canvas SuperAdmin di `resources/css/pages/superadmin/dashboard.css`**:
  - Pastikan pada `@media (max-width: 1024px)`, selektor `body.role-superadmin .top_nav`, `.dashboard-content-area`, dan `.gentelella-footer` memiliki `margin-left: 0 !important; width: 100% !important;`.

### Fase 3: Standardisasi Interaksi & State Machine JavaScript
- [x] **3.1. Refaktor Logika Toggle di `resources/js/layouts/dashboard.js`**:
  - Sinkronkan ambang batas deteksi: `const isMobile = window.innerWidth <= 1024;`.
  - Pada **Mobile / Tablet (`<= 1024px`)**:
    - Toggle kelas `drawer-open` dan `open` pada sidebar.
    - Toggle kelas `drawer-open` pada `document.body`.
    - Toggle kelas `active` dan `open` pada elemen *backdrop*.
    - Kunci *scroll* layar saat terbuka: `document.body.style.overflow = shouldOpen ? 'hidden' : '';`.
  - Pada **Desktop (`> 1024px`)**:
    - Toggle kelas `nav-sm` &harr; `nav-md` pada `document.body`.
    - Simpan status ke `localStorage.setItem('magkom_sidebar_state', isSm ? 'sm' : 'md')`.
    - Tutup semua *flyout portal* dan *tooltip* aktif.
- [x] **3.2. Penanganan Penutupan Otomatis (Auto-Close) Terpadu**:
  - Tangani klik pada tombol silang laci (`.sidebar-drawer-close-btn`).
  - Tangani klik pada area luar (*backdrop scrim* `#sidebarBackdrop`).
  - Tangani penekanan tombol keyboard `Escape`:
    - Jika ada *flyout/dropdown* terbuka, tutup terlebih dahulu.
    - Di layar mobile: tutup laci navigasi.
    - Di layar desktop: lakukan *toggle* mode mini-sidebar (paritas Peserta).
  - Tangani klik link navigasi pada mobile: otomatis panggil `toggleSidebar(false)` saat item menu di-klik, kecuali tombol pembuka akordeon (`.nav-group-btn`).
- [x] **3.3. Penanganan Resize Layar & Rotasi Perangkat**:
  - Pasang listener `window.addEventListener('resize', ...)`:
    - Jika layar membesar ke `> 1024px`, otomatis bersihkan kelas `drawer-open`, `open`, dan pulihkan `body.style.overflow = ''`.
- [x] **3.4. Normalisasi Interaktivitas Akordeon di Mobile**:
  - Pada fungsi `toggleNavGroup(btn)`:
    - Izinkan *inline collapse/expand* (`subMenu.classList.toggle('show')`) saat berada di mobile atau saat sidebar dalam mode penuh di desktop.
    - Panggil *floating flyout* (`triggerNavSmFlyout`) HANYA jika `window.innerWidth > 1024` DAN `body.classList.contains('nav-sm')`.

### Fase 4: Pencegahan FOUC / Anti-Flicker (`theme-init.js`)
- [x] **4.1. Tambahkan Pemeriksaan Status Sidebar di `<head>`**:
  - Di `public/js/theme-init.js`, tambahkan penanganan untuk portal non-peserta:
    ```javascript
    const savedAdminSidebar = localStorage.getItem('magkom_sidebar_state');
    if (savedAdminSidebar === 'sm' && window.innerWidth > 1024) {
        document.documentElement.classList.add('nav-sm');
    }
    ```
  - Pastikan CSS mengenali `html.nav-sm body` atau `html.nav-sm` untuk mencegah hentakan visual saat reload di Admin, Pembimbing, dan SuperAdmin.

### Fase 5: Penyesuaian Minimal Blade & Verifikasi Aset
- [x] **5.1. Penyesuaian Elemen Brand di `resources/views/layouts/dashboard.blade.php`**:
  - Tambahkan kelas `sidebar-brand-text` pada container teks judul Sleman agar selektor penyembunyian teks saat mini-sidebar bekerja 100% konsisten dengan Peserta.
- [x] **5.2. Kompilasi Aset Frontend (Vite Build)**:
  - Jalankan `npm run build` untuk memvalidasi bahwa seluruh CSS dan JS terkompilasi bersih tanpa *syntax error* atau *broken imports*.

---

## 6. Strategi Testing & Verifikasi

### 6.1 Skenario Pengujian Lintas Perangkat (Responsive Matrix)
1. **Smartphone Viewport (375px &times; 667px, 390px &times; 844px, 412px &times; 915px)**:
   - Sidebar default tersembunyi 100% di luar layar.
   - Tidak ada *horizontal scrollbar* pada body halaman.
   - Klik tombol *hamburger* di topbar: drawer meluncur masuk, backdrop blur muncul, body scroll terkunci.
   - Buka menu akordeon (misal: "Administrasi" di SuperAdmin): submenu terbuka ke bawah (*inline*) tanpa terpotong.
   - Klik salah satu link menu: laci otomatis tertutup dan halaman berpindah.
   - Klik backdrop atau tombol silang: laci tertutup seketika.
2. **Tablet Viewport (768px &times; 1024px &ndash; iPad Portrait & Landscape)**:
   - Pada resolusi 768px dan 1024px: Sidebar tetap beroperasi dalam mode laci off-canvas yang stabil.
   - Tidak terjadi tabrakan antara mode mini-sidebar dan laci mobile.
3. **Desktop Viewport (1280px &times; 720px, 1440px &times; 900px, 1920px &times; 1080px)**:
   - Default: Sidebar penuh (230px) dengan teks dan icon sejajar sempurna.
   - Klik tombol *hamburger*: Sidebar menciut ke 72px (mini-sidebar), teks nama tersembunyi, logo Sleman berada tepat di tengah, icon navigasi menjadi pill simetris 44px &times; 38px.
   - Pada role Admin/SuperAdmin: Klik grup menu memunculkan flyout portal melayang di kanan sidebar.
   - Tekan tombol `Escape`: Mini-sidebar kembali mengembang ke ukuran 230px.
   - Tekan `F5` (Refresh): Tidak ada kedipan/flicker FOUC.

### 6.2 Automated Quality Gates
- [ ] **PHPUnit / Pest Test Suite**:
  - Jalankan `php artisan test` untuk memastikan seluruh *feature tests* otentikasi, otorisasi, dan rendering view tetap lulus (100% hijau).
- [ ] **Laravel Pint Formatting**:
  - Jalankan `./vendor/bin/pint --test` untuk memastikan kode PHP tetap memenuhi standar PSR-12.
- [ ] **Vite Asset Build**:
  - Jalankan `npm run build` dan pastikan tidak ada peringatan atau error pada bundler.

---

## 7. Risiko & Mitigasi

| Risiko Potensial | Dampak | Strategi Mitigasi |
| :--- | :---: | :--- |
| **Breaking Layout pada 39 View Dashboard** | Tinggi | Jangan ubah struktur markup Blade utama. Terapkan seluruh perbaikan melalui stylesheet terpusat (`dashboard.css`, `gentelella.css`) dan Javascript (`dashboard.js`). |
| **Submenu Akordeon Tidak Berfungsi di Mobile** | Sedang | Pisahkan logika akordeon: saat di mobile (`<= 1024px`), akordeon dibuka secara *inline accordion*, bukan memanggil *flyout portal* desktop. |
| **FOUC / Lonjakan Visual saat Refresh Halaman** | Rendah | Tambahkan inisialisasi state *sidebar* sinkron di `public/js/theme-init.js` sebelum DOM selesai di-render. |
| **Pelanggaran CSP (Content Security Policy)** | Sedang | Seluruh kode JS murni menggunakan *event listener* / *delegation* di file `.js` terpisah, tidak menggunakan atribut *inline* `onclick`. |

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Breakpoint responsif pada `layouts/dashboard.blade.php` (Admin, Pembimbing, SuperAdmin) telah diselaraskan ke `1024px` identik dengan `layouts/peserta.blade.php`.
- [x] Di layar mobile (&le; 1024px), tombol *hamburger* membuka *sidebar* sebagai *off-canvas drawer* berlatar *backdrop blur* dengan *body scroll lock* yang bekerja mulus.
- [x] Di layar desktop (&gt; 1024px), tombol *hamburger* melakukan *toggle* mini-sidebar 72px yang simetris, rapi, dan bebas kebocoran teks.
- [x] Seluruh menu akordeon pada Admin dan SuperAdmin dapat diakses dan dibuka dengan lancar baik di layar mobile maupun desktop.
- [x] Tombol tutup silang (`&times;`), klik *backdrop*, tombol `Escape`, dan klik link navigasi otomatis menutup laci di layar mobile.
- [x] Transisi halaman bebas FOUC / kedipan saat *refresh* di desktop.
- [x] `npm run build` berhasil tanpa error.
- [x] Seluruh test PHPUnit (`php artisan test`) lulus 100% hijau tanpa regresi.
