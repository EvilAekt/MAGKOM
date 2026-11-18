# Rencana Kerja: Perbaikan Tampilan Sidebar Mini (Mode `Esc` / Collapsed) di Seluruh Dashboard MagKom

- **Dokumen Referensi:** PRD MagKom v1.2, Desain Sistem Dashboard MagKom (Sleman Design Tokens), Aturan Frontend MagKom (Rule 3 & 4), `docs/plans/PLAN-MINI-SIDEBAR-FLYOUT-STANDARDIZATION.md`
- **Target Pembaca / Pelaksana:** Full-Stack Developer & AI Assistant
- **Tingkat Kerumitan:** Menengah (*UI Architecture, CSS Scoping, Keyboard Interaction State Machine*)
- **Target Modul:** 
  - `resources/css/layouts/dashboard.css`
  - `resources/css/layouts/dashboard-layout-tokens.css`
  - `resources/js/layouts/dashboard.js`
  - `resources/js/layouts/gentelella.js`
- **Prinsip Utama:** **Blade Read-Only**. Seluruh perbaikan diselesaikan murni melalui CSS dan JS tanpa mengubah markup Blade yang sudah ada.

---

## 1. Latar Belakang & Analisis Akar Masalah (Root Cause Analysis)

Pengguna membandingkan tampilan saat tombol **`Esc`** ditekan di keyboard:
- **Foto 4 (Dashboard Peserta &ndash; Acuan Emas)**: Saat `Esc` ditekan di desktop, sidebar menciut (*collapsed*) menjadi mini-sidebar 72px yang **sangat rapi, simetris, dan bersih**:
  - Logo lambang Sleman berada persis di tengah (*centered*).
  - Teks brand `"MAGKOM"` disembunyikan sepenuhnya tanpa teks bocor.
  - Seluruh item navigasi menjadi *pill* persegi membulat simetris (ukuran 44px &times; 38px) dengan ikon di tengah (*centered*).
  - Teks navigasi dan label seksi disembunyikan sepenuhnya.
  - Tombol footer (Pengaturan, Mode Tema, Logout) tersusun vertikal di tengah.
- **Foto 1 & 2 (Dashboard Pembimbing & Admin &ndash; Cacat Visual)**:
  - Teks brand `"MAGKOM"` tetap muncul di samping logo lambang dan terpotong jelek (*clipped text overflow*).
  - Ikon navigasi condong ke kiri (*left-aligned*), tidak simetris di tengah *pill* 44x38px.
- **Foto 3 (Dashboard SuperAdmin &ndash; Sangat Rusak)**:
  - Tombol grup akordeon (`.nav-group-btn`) untuk **Administrasi**, **Pembimbing**, **Peserta**, dan **Konfigurasi** masih memunculkan teks nama grup dan tanda panah chevron (`>`) yang berhimpitan dan terjepit di tepi sidebar mini!
  - Layout menjadi berantakan dan tidak simetris.
- **Akar Masalah Keyboard Shortcut `Esc`**:
  - Di `resources/js/layouts/peserta-layout.js`:
    ```javascript
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' || e.key === 'Esc') {
            closeProfileMenu();
            toggleSidebarPeserta(false);
        }
    });
    ```
    Pada desktop, `toggleSidebarPeserta(false)` mengabaikan parameter `false` dan langsung memanggil `html.classList.toggle('sidebar-collapsed')`. Akibatnya, setiap kali tombol `Esc` ditekan, sidebar langsung menciut menjadi mini-sidebar (atau melebar kembali).
  - Di `resources/js/layouts/dashboard.js` & `resources/js/layouts/gentelella.js`:
    ```javascript
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            closeActiveFlyout();
            closeActiveTooltip();
            toggleSidebar(false); // <--- parameter false memaksa nav-md (melebar) di desktop!
            ...
        }
    });
    ```
    Pada desktop, pemanggilan dengan parameter `false` memaksa sidebar masuk ke `nav-md` (mode lebar), bukan melakukan toggle `nav-sm` &harr; `nav-md` seperti di Peserta.

---

## 2. Sasaran Perbaikan (Target Deliverables)

1. **Harmonisasi Shortcut `Esc` di Seluruh Dashboard**:
   - Saat tombol `Esc` ditekan di keyboard pada desktop (viewport &ge; 992px):
     - Jika ada flyout menu melayang, tooltip, atau dropdown profil terbuka, tombol `Esc` akan menutup flyout/menu tersebut terlebih dahulu.
     - Jika tidak ada overlay terbuka, tombol `Esc` akan melakukan **toggle** antara mode mini (`nav-sm`, 70px) dan mode penuh (`nav-md`, 230px), sama persis dengan perilaku di Peserta.
   - Pada layar mobile (&le; 1024px), tombol `Esc` tetap menutup laci drawer navigasi.
2. **Pembersihan Header Brand Sleman pada Mode Mini (`body.nav-sm`)**:
   - Sembunyikan teks `"MAGKOM"` (`.sidebar-brand-title` dan `.sidebar-brand-text`).
   - Pusatkan lambang logo Sleman di tengah sidebar (70px).
3. **Penyempurnaan Tombol Navigasi Tunggal (`.nav-item-link`)**:
   - Bentuk setiap item menjadi *pill* simetris: lebar 44px, tinggi 38px, `margin: 0.25rem auto`, sudut membulat 8px, ikon di tengah.
   - Sembunyikan semua teks label navigasi dan label seksi.
4. **Penyempurnaan Tombol Grup Akordeon SuperAdmin (`.nav-group-btn`)**:
   - Ubah tombol grup menjadi *pill* simetris 44px &times; 38px dengan `margin: 0.25rem auto`.
   - Sembunyikan semua teks label grup (`Administrasi`, `Pembimbing`, `Peserta`, `Konfigurasi`).
   - Sembunyikan tanda panah chevron (`>`).
   - Sembunyikan submenu inline (`.nav-sub-menu`) di dalam sidebar (submenu hanya muncul saat tombol diklik/di-hover melalui panel melayang *floating flyout portal* di sebelah kanan sidebar).
5. **Penyelarasan Footer Toolbar**:
   - Susun tombol aksi footer secara vertikal di tengah dengan dimensi konsisten 38px &times; 38px.

---

## 3. Rencana Langkah Eksekusi (Implementation Steps)

### Langkah 1: Harmonisasi Logika `Esc` di `resources/js/layouts/dashboard.js` & `gentelella.js`

- **File**: `resources/js/layouts/dashboard.js`
- **File**: `resources/js/layouts/gentelella.js`
- **Perubahan**:
  Perbarui listener `keydown` tombol `Escape` agar membedakan kondisi desktop vs mobile:
  ```javascript
  document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' || e.key === 'Esc') {
          let closedSomething = false;

          // 1. Tutup flyout melayang jika ada
          if (activeFlyout) {
              closeActiveFlyout();
              closedSomething = true;
          }
          if (activeTooltip) {
              closeActiveTooltip();
              closedSomething = true;
          }

          // 2. Tutup dropdown user menu jika ada
          const menu = document.getElementById('userDropdown');
          const btn = document.getElementById('userMenuBtn');
          if (menu && menu.classList.contains('show')) {
              menu.classList.remove('show');
              if (btn) btn.setAttribute('aria-expanded', 'false');
              closedSomething = true;
          }

          // 3. Pada desktop: jika tidak ada popover yang ditutup, lakukan toggle mini-sidebar
          if (window.innerWidth >= 992) {
              if (!closedSomething) {
                  toggleSidebar(); // Toggle nav-sm <-> nav-md
              }
          } else {
              // Pada mobile: selalu tutup drawer
              toggleSidebar(false);
          }
      }
  });
  ```

---

### Langkah 2: Styling Mode Mini (`body.nav-sm`) di `resources/css/layouts/dashboard.css` & `dashboard-layout-tokens.css`

Tambahkan aturan CSS scoped untuk `body.nav-sm` pada breakpoint desktop (`@media (min-width: 992px)`):

#### A. Header & Brand Emblem Sleman:
```css
@media (min-width: 992px) {
    /* Sembunyikan semua teks judul brand MAGKOM di mode mini */
    body.nav-sm .sidebar-brand-text,
    body.nav-sm .sidebar-brand-title,
    body.nav-sm .site_title div:not(.sidebar-brand-emblem) {
        display: none !important;
    }

    body.nav-sm .sidebar-header {
        padding: 1.25rem 0.5rem 1rem 0.5rem !important;
        justify-content: center !important;
    }

    body.nav-sm .site_title {
        justify-content: center !important;
        gap: 0 !important;
        width: 100% !important;
        padding: 0 !important;
    }

    body.nav-sm .sidebar-brand-emblem {
        margin: 0 auto !important;
    }
}
```

#### B. Tombol Item Tunggal (`.nav-item-link`):
```css
@media (min-width: 992px) {
    body.nav-sm .nav-item-link {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        padding: 0 !important;
        width: 44px !important;
        height: 38px !important;
        min-height: 38px !important;
        margin: 0.25rem auto !important;
        border-radius: 8px !important;
        position: relative !important;
        box-sizing: border-box !important;
    }

    body.nav-sm .nav-item-link span:not(.nav-item-icon) {
        display: none !important;
    }

    body.nav-sm .nav-item-icon {
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
}
```

#### C. Tombol Grup Akordeon SuperAdmin (`.nav-group-btn`) & Submenu:
```css
@media (min-width: 992px) {
    body.nav-sm .nav-group-btn {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        padding: 0 !important;
        width: 44px !important;
        height: 38px !important;
        min-height: 38px !important;
        margin: 0.25rem auto !important;
        border-radius: 8px !important;
        position: relative !important;
        box-sizing: border-box !important;
        border-left: none !important;
    }

    /* Sembunyikan semua teks judul grup (Administrasi, Pembimbing, Peserta, Konfigurasi) */
    body.nav-sm .nav-group-btn span:not(.nav-item-icon),
    body.nav-sm .nav-group-btn-left span:not(.nav-item-icon) {
        display: none !important;
    }

    /* Sembunyikan tanda panah chevron */
    body.nav-sm .nav-group-chevron,
    body.nav-sm .nav-group-btn svg.nav-group-chevron {
        display: none !important;
    }

    body.nav-sm .nav-group-btn-left {
        width: 100% !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 0 !important;
        margin: 0 !important;
    }

    /* Sembunyikan menu sub-level inline di dalam sidebar mini (muncul via floating flyout) */
    body.nav-sm .nav-sub-menu,
    body.nav-sm .nav-sub-menu.show {
        display: none !important;
    }

    /* Sembunyikan semua label seksi header */
    body.nav-sm .nav-section-label,
    body.nav-sm .menu_section h3 {
        display: none !important;
    }
}
```

#### D. Toolbar Footer Sidebar:
```css
@media (min-width: 992px) {
    body.nav-sm .sidebar-footer {
        flex-direction: column !important;
        gap: 0.5rem !important;
        padding: 0.75rem 0 !important;
        align-items: center !important;
        justify-content: center !important;
    }

    body.nav-sm .sidebar-footer a,
    body.nav-sm .sidebar-footer button,
    body.nav-sm .sidebar-footer .sidebar-footer-btn {
        width: 38px !important;
        height: 38px !important;
        margin: 0 auto !important;
    }
}
```

#### E. Penegasan Lebar Sidebar 70px & Offset Konten:
```css
@media (min-width: 992px) {
    body.nav-sm .dashboard-sidebar,
    body.nav-sm .left_col,
    body.nav-sm #mainSidebar {
        width: 70px !important;
        max-width: 70px !important;
        transition: width 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    body.nav-sm .top_nav,
    body.nav-sm .right_col,
    body.nav-sm .dashboard-content-area,
    body.nav-sm .dashboard-topbar-wrapper,
    body.nav-sm .gentelella-footer {
        margin-left: 70px !important;
        width: calc(100% - 70px) !important;
        max-width: calc(100% - 70px) !important;
        transition: margin-left 0.25s cubic-bezier(0.16, 1, 0.3, 1), width 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }
}
```

---

## 4. Matriks Perbandingan Sebelum vs Sesudah

| Aspek Antarmuka | Kondisi Eksisting (Foto 1, 2, 3) | Target Hasil Perbaikan (Paritas Foto 4) |
|---|---|---|
| **Header Brand** | Logo Sleman + teks "MAGKOM" terpotong | Logo Sleman di tengah, teks "MAGKOM" disembunyikan total |
| **Item Tunggal (`nav-item-link`)** | Ikon mepet ke kiri, tidak terpusat dalam pill simetris | Ikon terpusat rapi dalam pill simetris 44px &times; 38px (`margin: 0 auto`) |
| **Grup Akordeon SuperAdmin** | Teks `"Administrasi >"` dkk terhimpit dan meluap | Ikon terpusat dalam pill 44px &times; 38px, teks & chevron disembunyikan total |
| **Submenu Grup** | Berpotensi terbuka di dalam kolom mini 70px | Tersembunyi di sidebar; terbuka sebagai *Floating Flyout Portal* di sebelah kanan (`left: 72px`) |
| **Tombol `Esc` di Desktop** | Memaksa sidebar kembali lebar (`nav-md`) | Melakukan toggle halus mini-sidebar (`nav-sm` &harr; `nav-md`) |
| **Toolbar Footer** | Spasi horizontal tidak terpusat | Tersusun vertikal simetris di tengah |

---

## 5. Rencana Pengujian & Quality Gate

1. **Build Asset Pipeline**:
   - Jalankan `npm run build` untuk memvalidasi tidak ada syntax error pada CSS/JS.
2. **Automated Test Regression**:
   - Jalankan test suite layout dan otentikasi dashboard:
     ```bash
     php artisan test --filter=Dashboard
     php artisan test --filter=SuperAdmin
     php artisan test --filter=Pembimbing
     php artisan test --filter=Peserta
     ```
   - Pastikan seluruh test assertions (termasuk `.sr-only` brand subtitle) tetap berstatus PASS 100%.
3. **Pemeriksaan Visual**:
   - Uji penekanan tombol `Esc` pada 4 role (Peserta, Pembimbing, Admin, SuperAdmin) di browser desktop.
   - Pastikan saat diciutkan, tampilan SuperAdmin, Pembimbing, dan Admin terlihat simetris dan rapi seperti Dashboard Peserta (Foto 4).
