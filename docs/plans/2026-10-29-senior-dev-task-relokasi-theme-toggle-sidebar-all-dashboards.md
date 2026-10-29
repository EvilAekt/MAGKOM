# ENGINEERING PRD & TASK SPECIFICATION: Relokasi & Standarisasi Theme Toggle ke Sidebar Bawah (All Dashboards)

- **Document ID**: `PRD-TASK-2026-10-29-UI-03`
- **Target Audience**: Junior Developer & Autonomous AI Agents (Gemini 3.8 Flash / Claude 3.5 Sonnet)
- **Author**: Erwin Smith (Orchestrator & Technical Lead)
- **UI/UX Spec Alignment**: Historia Reiss (Design Systems Lead)
- **Architectural Approval**: Armin Arlert (Software Architect)
- **Reviewer**: Levi Ackerman (Code Reviewer) & Annie Leonhart (QA Lead)
- **Target Dashboards**:
  1. Peserta Magang (`resources/views/layouts/peserta.blade.php`)
  2. Pembimbing Lapangan (`resources/views/layouts/dashboard.blade.php`)
  3. Administrasi / Admin (`resources/views/layouts/dashboard.blade.php`)
  4. SuperAdmin (`resources/views/layouts/dashboard.blade.php`)
- **Target Layout Files**:
  - [`resources/views/layouts/dashboard.blade.php`](file:///G:/MAGKOM/resources/views/layouts/dashboard.blade.php)
  - [`resources/views/layouts/peserta.blade.php`](file:///G:/MAGKOM/resources/views/layouts/peserta.blade.php)

---

## 1. Executive Summary & Objective

Saat ini, mekanisme pergantian mode tema (Terang / Gelap) memiliki inkonsistensi antarmuka di 4 dashboard utama:
1. **Header (Topbar)**: Terdapat tombol ganti tema (`#themeToggleBtn.topbar-theme-toggle-btn`) yang memakan ruang visual topbar di samping profil pengguna.
2. **Sidebar Bawah (`.sidebar-footer`)**:
   - Pada **Dashboard Admin, Pembimbing, dan SuperAdmin** (`dashboard.blade.php`), tombol tema di sidebar bawah hanya menampilkan icon Bulan (*Moon SVG*) secara statis tanpa indikator perubahan tampilan saat beralih ke Mode Terang (*Light Mode*) atau sebaliknya.
   - Pada **Dashboard Peserta** (`peserta.blade.php`), sidebar bawah hanya berisi tombol Logout teks penuh tanpa tombol ganti tema sama sekali.

### Sasaran Utama (Objectives):
1. **Eliminasi Header Theme Toggle**: Hapus tombol toggle tema dari header (`topbar`) di seluruh 4 dashboard (`dashboard.blade.php` dan `peserta.blade.php`).
2. **Standarisasi Sidebar Bawah**: Pindahkan dan seragamkan kontrol toggle tema ke **Sidebar Bawah Kiri (`.sidebar-footer`)** di seluruh 4 dashboard (Peserta, Pembimbing, Admin, SuperAdmin).
3. **Penyediaan Indikator Dinamis (Terang vs Gelap)**: Sediakan perbedaan visual yang kontras dan jelas antara Mode Terang dan Mode Gelap pada tombol tema sidebar (menampilkan icon Sun / Matahari saat mode gelap aktif dan icon Moon / Bulan saat mode terang aktif, dengan hover ring sirkular sesuai referensi screenshot).
4. **Zero-Regression Guarantee**: Memastikan seluruh fungsi session server-side (`route('tema.update')`), localStorage (`magkom_theme`), dan atribut DOM `data-theme` serta `data-pref-theme` tetap beroperasi 100% mulus.

---

## 2. Analisis Arsitektur & Bounded File Scope

### 2.1. Pemetaan File Layout terhadap 4 Dashboard
Portal MagKom menggunakan 2 Master Layout Wrapper untuk melayani 4 peran:
| No | Peran / Dashboard | Layout File Utama | Bagian Header | Bagian Sidebar Footer |
| :--- | :--- | :--- | :--- | :--- |
| 1 | **Peserta Magang** | `resources/views/layouts/peserta.blade.php` | Line ~1774–1787 | Line ~1743–1753 |
| 2 | **Pembimbing** | `resources/views/layouts/dashboard.blade.php` | Line ~3420–3433 | Line ~3364–3377 |
| 3 | **Administrasi (Admin)** | `resources/views/layouts/dashboard.blade.php` | Line ~3420–3433 | Line ~3364–3377 |
| 4 | **SuperAdmin** | `resources/views/layouts/dashboard.blade.php` | Line ~3420–3433 | Line ~3364–3377 |

Dengan demikian, perombakan ini **hanya menyentuh 2 file layout Blade** untuk langsung menstandarisasi seluruh 4 dashboard secara serempak.

### 2.2. Bounded Scope
- **`allowed_files`**:
  - `resources/views/layouts/dashboard.blade.php`
  - `resources/views/layouts/peserta.blade.php`
- **`forbidden_files`**:
  - `app/**` (Backend logic tidak boleh diubah)
  - `routes/**` (Route web & API tidak boleh diubah)
  - `database/**` (Schema database dilarang disentuh)
  - `resources/views/admin/**`, `resources/views/pembimbing/**`, `resources/views/superadmin/**`, `resources/views/peserta/**` (Sub-view tidak perlu diedit karena mewarisi layout master)

---

## 3. Spesifikasi Desain & Visual Tokens (Historia Reiss Design Review)

Berdasarkan referensi visual yang dilampirkan:
- **Attachment 1 (`23b314...png`)**: Menampilkan bar aksi cepat di footer sidebar: `[Pengaturan] [Theme Toggle] [Logout]`.
- **Attachment 2 (`361fc4...png`)**: Menampilkan ikon Sun (Matahari) sirkular dengan hover/ring kontras saat mode gelap aktif (siap berpindah ke terang).
- **Attachment 3 (`f3e7ef...png`)**: Menampilkan ikon Moon (Bulan sabit) sirkular dengan background hover saat mode terang aktif (siap berpindah ke gelap).

### 3.1. Anatomi Tombol Sidebar Theme Toggle
Tombol baru diberi class `.sidebar-theme-toggle-btn` dan id `#sidebarThemeToggleBtn`:
```css
/* Styling Tombol Toggle Tema Sidebar Footer */
.sidebar-footer .sidebar-theme-toggle-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: rgba(255, 255, 255, 0.85);
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    padding: 0;
    position: relative;
}

.sidebar-footer .sidebar-theme-toggle-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.35);
    color: #FFFFFF;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
}

.sidebar-footer .sidebar-theme-toggle-btn:focus-visible {
    outline: 2px solid var(--magkom-sun-yellow, #FFCB05);
    outline-offset: 2px;
}
```

### 3.2. Mekanisme Switching Ikon Sun vs Moon (Pure CSS State)
Sesuai standar DS-MagKom, perubahan ikon tidak membutuhkan manipulasi DOM JS yang berat, melainkan binding murni terhadap atribut `data-theme` pada root elemen `<html>`:
```css
/* Mode Terang (Default): Tampilkan Moon Icon (ajakan beralih ke Gelap), Sembunyikan Sun Icon */
html:not([data-theme="dark"]) .sidebar-theme-toggle-btn .sun-icon {
    display: none !important;
}
html:not([data-theme="dark"]) .sidebar-theme-toggle-btn .moon-icon {
    display: block !important;
    color: #FFFFFF;
}

/* Mode Gelap (Dark): Tampilkan Sun Icon (ajakan beralih ke Terang), Sembunyikan Moon Icon */
html[data-theme="dark"] .sidebar-theme-toggle-btn .sun-icon {
    display: block !important;
    color: var(--magkom-sun-yellow, #FFCB05);
}
html[data-theme="dark"] .sidebar-theme-toggle-btn .moon-icon {
    display: none !important;
}
```

---

## 4. Panduan Eksekusi Langkah-demi-Langkah (Junior Dev / Flash AI Runbook)

### Langkah 1: Modifikasi `resources/views/layouts/dashboard.blade.php` (Admin, Pembimbing, Superadmin)

#### 1.A. Hapus Tombol Toggle di Header
Cari baris ~3420–3433 pada container `.topbar-right`:
```html
<!-- SEBELUM: HAPUS BLOK INI -->
<button type="button" 
        class="topbar-theme-toggle-btn" 
        id="themeToggleBtn" 
        onclick="togglePortalThemeQuick()" 
        aria-label="{{ __('Ganti Mode Terang / Gelap') }}" 
        title="{{ __('Ganti Mode Terang / Gelap') }}">
    <svg class="sun-icon" ...>...</svg>
    <svg class="moon-icon" ...>...</svg>
</button>
```
*Tindakan*: Hapus tombol tersebut sepenuhnya dari `.topbar-right` agar ruang header menjadi rapi dan fokus pada search box serta profil pengguna.

#### 1.B. Perbarui Tombol Toggle di Sidebar Footer
Cari baris ~3364–3377 pada `.sidebar-footer.hidden-small`:
```html
<!-- SEBELUM (Tombol hanya SVG bulan statis) -->
<button type="button" onclick="applyPortalTheme(document.documentElement.getAttribute('data-pref-theme') === 'gelap' ? 'terang' : 'gelap')" title="Ganti Mode Gelap/Terang" aria-label="Ganti Tema">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
</button>
```
Ganti menjadi:
```html
<!-- SESUDAH: Tombol Dual-Icon Responsif Tema -->
<button type="button" 
        class="sidebar-theme-toggle-btn" 
        id="sidebarThemeToggleBtn" 
        onclick="togglePortalThemeQuick()" 
        title="{{ __('Ganti Mode Terang / Gelap') }}" 
        aria-label="{{ __('Ganti Mode Terang / Gelap') }}">
    <svg class="sun-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
    </svg>
    <svg class="moon-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
    </svg>
</button>
```

#### 1.C. Tambahkan / Selaraskan CSS di Blok `<style>` pada `dashboard.blade.php`
Tambahkan deklarasi CSS untuk `.sidebar-theme-toggle-btn` dan switching `.sun-icon` / `.moon-icon` pada blok style sidebar footer (sekitar line 2185–2216).

---

### Langkah 2: Modifikasi `resources/views/layouts/peserta.blade.php` (Peserta Magang)

#### 2.A. Hapus Tombol Toggle di Header
Cari baris ~1774–1787 pada container `.topbar-right`:
```html
<!-- SEBELUM: HAPUS BLOK INI -->
<button type="button" 
        class="topbar-theme-toggle-btn" 
        id="themeToggleBtn" 
        onclick="togglePortalThemeQuick()" 
        aria-label="{{ __('Ganti Mode Terang / Gelap') }}" 
        title="{{ __('Ganti Mode Terang / Gelap') }}">
    <svg class="sun-icon" ...>...</svg>
    <svg class="moon-icon" ...>...</svg>
</button>
```
*Tindakan*: Hapus tombol ini sepenuhnya dari `.topbar-right`.

#### 2.B. Seragamkan Sidebar Footer Peserta dengan 3 Aksi Utama
Cari baris ~1743–1753 pada `.sidebar-footer`:
```html
<!-- SEBELUM: Hanya tombol Keluar teks -->
<div class="sidebar-footer">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="logout-btn" title="{{ __('Keluar Akun') }}">
            ...
            <span>{{ __('Keluar Akun') }}</span>
        </button>
    </form>
</div>
```
Ganti menjadi layout terstandarisasi yang selaras dengan `dashboard.blade.php`:
```html
<!-- SESUDAH: Terstandarisasi 3 Aksi (Pengaturan, Theme Toggle, Keluar) -->
<div class="sidebar-footer">
    <a href="{{ route('peserta.pengaturan.index') }}" 
       class="sidebar-action-btn" 
       title="{{ __('Pengaturan Akun') }}" 
       aria-label="{{ __('Pengaturan Akun') }}">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="3"></circle>
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
        </svg>
    </a>
    <button type="button" 
            class="sidebar-theme-toggle-btn" 
            id="sidebarThemeToggleBtn" 
            onclick="togglePortalThemeQuick()" 
            title="{{ __('Ganti Mode Terang / Gelap') }}" 
            aria-label="{{ __('Ganti Mode Terang / Gelap') }}">
        <svg class="sun-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        <svg class="moon-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
        </svg>
    </button>
    <form method="POST" action="{{ route('logout') }}" style="margin: 0; display: inline;">
        @csrf
        <button type="submit" class="sidebar-action-btn logout-action" title="{{ __('Keluar / Logout') }}" aria-label="{{ __('Keluar') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
        </button>
    </form>
</div>
```

#### 2.C. Styling CSS `.sidebar-footer` pada `peserta.blade.php`
Perbarui CSS `.sidebar-footer` agar menggunakan flexbox dengan `justify-content: space-around;` dan styling tombol serasi dengan `dashboard.blade.php`:
```css
.sidebar-footer {
    padding: 0.75rem 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    justify-content: space-around;
    margin-top: auto;
}

.sidebar-footer .sidebar-action-btn,
.sidebar-footer .sidebar-theme-toggle-btn {
    color: rgba(255, 255, 255, 0.75);
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 8px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.15s ease;
    text-decoration: none;
}

.sidebar-footer .sidebar-action-btn:hover,
.sidebar-footer .sidebar-theme-toggle-btn:hover {
    color: #FFFFFF;
    background: rgba(255, 255, 255, 0.2);
}

.sidebar-footer .sidebar-action-btn.logout-action:hover {
    color: #FCA5A5;
    background: rgba(239, 68, 68, 0.2);
}
```

---

## 5. Acceptance Criteria (Kriteria Keberterimaan)

- [ ] **AC-1 (Header Bersih)**: Tombol `#themeToggleBtn` telah dihapus sepenuhnya dari topbar header di 4 dashboard: Peserta Magang, Pembimbing, Admin, dan SuperAdmin. Header tidak lagi menampilkan tombol toggle tema di sebelah chip profil.
- [ ] **AC-2 (Standarisasi Sidebar Footer 4 Dashboard)**: Sidebar footer di 4 dashboard memiliki penataan seragam (Pengaturan Akun, Toggle Mode Tema, Keluar Akun) yang simetris dan rapi.
- [ ] **AC-3 (Perbedaan Visual Terang vs Gelap)**:
  - Pada **Mode Terang** (`html:not([data-theme="dark"])`): Menampilkan ikon Bulan sabit (*Moon SVG*) putih bersih untuk mengajak beralih ke Gelap.
  - Pada **Mode Gelap** (`html[data-theme="dark"]`): Menampilkan ikon Matahari (*Sun SVG*) berwarna kuning keemasan (`#FFCB05`) untuk mengajak beralih ke Terang.
  - Efek hover menampilkan lingkar sirkular halus (`border-radius: 50%` / rounded pill) sesuai referensi desain screenshot.
- [ ] **AC-4 (Fungsionalitas Toggle & State Persistence)**:
  - Mengklik tombol tema di sidebar sukses memicu `window.togglePortalThemeQuick()`.
  - Atribut `data-theme` dan `data-pref-theme` pada tag `<html>` terupdate seketika tanpa perlu refresh halaman.
  - Nilai tersimpan di `localStorage.getItem('magkom_theme')` dan tersinkronisasi via AJAX `route('tema.update')`.
- [ ] **AC-5 (Responsivitas & Collapsed Sidebar)**:
  - Pada tampilan desktop collapsed (`body.nav-sm` atau `html.sidebar-collapsed`), tombol tetap terpusat dan tidak mengalami overflow/terpotong.
  - Pada tampilan mobile drawer, sidebar footer tetap dapat diakses dengan mudah di bagian bawah drawer navigasi.
- [ ] **AC-6 (Zero Test Regression)**:
  - Semua Feature Test terkait autentikasi, dashboard, dan layout tetap lulus 100% tanpa error syntax Blade.

---

## 6. Protokol Tim & RACI

- **Orchestrator & Lead**: Erwin Smith
- **Implementer**: Eren Yeager
- **Design Review**: Historia Reiss
- **Architecture**: Armin Arlert
- **Reviewer**: Levi Ackerman
- **Quality Assurance**: Annie Leonhart
