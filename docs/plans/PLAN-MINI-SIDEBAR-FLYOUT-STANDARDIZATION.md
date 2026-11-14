# TASK SPECIFICATION & IMPLEMENTATION PRD (v1.0)
## Standarisasi Menu Melayang (*Floating Flyout Submenu*) pada Sidebar Mini (*Mode Kecil / `nav-sm`*)

- **Dokumen Referensi:** PRD MagKom v1.2, Desain Sistem Dashboard MagKom (Sleman Design Tokens), Aturan Frontend MagKom (Rule 3 & 4)
- **Peran Pembuat Dokumen:** Senior Lead Developer
- **Target Pembaca / Pelaksana:** Junior Developer & AI Assistant (Gemini 3.8 Flash / Claude / Cursor)
- **Tingkat Kerumitan:** Menengah (*Intermediate &ndash; UI Architecture, Floating Positioning, Mobile vs Desktop State Machine*)
- **Target Modul:** Layout Dashboard Utama (`resources/css/layouts/`, `resources/js/layouts/`, `resources/views/layouts/dashboard.blade.php`)
- **Task ID:** `TASK-UI-MINI-SIDEBAR-FLYOUT-01`
- **Status Dokumen:** **SELESAI DIEKSEKUSI & DIVERIFIKASI (*COMPLETED & VERIFIED*)**

---

## 1. DIRECTIVE & PESAN DARI SENIOR LEAD DEVELOPER

Halo Junior Developer / AI Assistant! 👋

Perhatikan dua tangkapan layar yang dilaporkan oleh pengguna berikut ini:

| Kondisi Masalah: Sidebar Mode Kecil (`nav-sm`) | Referensi Menu Lengkap saat Mode Penuh (`nav-md`) |
|:---:|:---:|
| !["Sidebar Mode Kecil (Collapsed)"](/C:/Users/galan/.gemini/antigravity/brain/b04d2e86-2b10-4ed5-a980-3894b843e602/.user_uploaded/media_1794627096214_0f2dc869.png) | !["Sidebar Mode Penuh dengan Submenu"](/C:/Users/galan/.gemini/antigravity/brain/b04d2e86-2b10-4ed5-a980-3894b843e602/.user_uploaded/media_1794627152624_4e18e721.png) |

### 🔍 Analisis Akar Masalah (Root Cause Analysis):
1. **Mode Penuh (`nav-md`, lebar ~230px)**:
   Menu yang memiliki anak (seperti **Administrasi**, **Pembimbing**, **Peserta**, dan **Konfigurasi**) menggunakan accordion vertikal inline (`nav-group-btn` &rarr; `nav-sub-menu`). Ini berjalan normal saat sidebar lebar.
2. **Mode Kecil / Mini-Sidebar (`nav-sm`, lebar ~70px)**:
   Saat sidebar diciutkan ke mode ikon kecil:
   - Di file CSS (`gentelella.css` & `dashboard-layout-tokens.css`), tertulis rule:
     ```css
     body.nav-sm .nav-sub-menu {
         display: none !important;
     }
     ```
   - Akibatnya, ketika pengguna mengklik ikon **Administrasi**, **Pembimbing**, atau **Peserta**, submenu tidak muncul sama sekali karena dipaksa `display: none !important;`.
   - Pengguna **TIDAK BISA** memilih submenu apa pun (seperti *Verifikasi Berkas*, *Kuota Magang*, *Penempatan Peserta*, *Laporan & Ekspor*, *Review Logbook*, *Tugas Magang*, *Arsip Peserta*, dst.) saat sidebar dalam kondisi mini!

---

### 💡 Solusi Standar Industri: *Floating Flyout Popover Menu*
Kita akan mengadopsi standar emas antarmuka enterprise modern (seperti VS Code, Slack, AdminLTE, Metronic, dan Tailwind UI):

> [!IMPORTANT]
> **Konsep Floating Flyout**:  
> Saat sidebar dalam mode mini (`body.nav-sm`), mengklik (atau mengarahkan kursor) pada tombol grup menu yang memiliki submenu akan memunculkan **panel menu popover melayang (*Floating Flyout*)** persis di sebelah kanan ikon tersebut (`left: 70px`).  
> Panel ini melayang di atas konten utama (*floating overlay*) dengan z-index tinggi, menampilkan judul grup serta daftar seluruh submenu yang bisa langsung diklik.

---

## 2. ARSITEKTUR & SPESIFIKASI INTERAKSI (UI/UX MATRIX)

Terdapat 3 kondisi antarmuka yang wajib didukung secara mulus tanpa konflik:

```
┌────────────────────────────────────────────────────────────────────────┐
│                        KONDISI TAMPILAN SIDEBAR                        │
├───────────────────────────────────┬────────────────────────────────────┤
│ 1. Desktop Mode Penuh (nav-md)    │ 2. Desktop Mode Mini (nav-sm)      │
│    Width: ~230px                  │    Width: ~70px                    │
│    • Accordion vertikal standar   │    • Ikon saja di sidebar          │
│    • Klik membuka anak ke bawah   │    • Klik memunculkan FLYOUT       │
│    • Tidak ada floating popover   │      melayang di sebelah kanan     │
├───────────────────────────────────┴────────────────────────────────────┤
│ 3. Mobile / Tablet (<= 1024px)                                         │
│    Width: 280px drawer overlay dari kiri (off-canvas)                  │
│    • Accordion vertikal standar tetap aktif                            │
│    • Flyout mini dinonaktifkan (karena layar sentuh drawer penuh)      │
└────────────────────────────────────────────────────────────────────────┘
```

### 2.1. Matriks Submenu yang Wajib Tersedia di Mode Mini

| Ikon Navigasi | Judul Flyout | Daftar Submenu yang Muncul di Flyout | Target Route |
|---|---|---|---|
| **Ikon Administrasi** *(Card ID)* | **Administrasi** | 1. Verifikasi Berkas<br>2. Kuota Magang<br>3. Penempatan Peserta<br>4. Laporan & Ekspor | `admin.pendaftaran.index`<br>`admin.kuota.index`<br>`admin.penempatan.index`<br>`admin.laporan.index` |
| **Ikon Pembimbing** *(Clipboard Check)* | **Pembimbing** | 1. Review Logbook<br>2. Tugas Magang<br>3. Laporan Peserta | `pembimbing.logbook.index`<br>`pembimbing.tugas.index`<br>`pembimbing.laporan-kinerja.index` |
| **Ikon Peserta** *(Users Group)* | **Peserta** | 1. Arsip Peserta | `superadmin.arsip.index` |
| **Ikon Konfigurasi** *(Gear)* | **Konfigurasi** | 1. Pengaturan<br>2. Canvas Editor<br>3. Periode Magang<br>4. Jalur Khusus<br>5. Data Jurusan<br>6. Unit Kerja | `superadmin.pengaturan.index`<br>`superadmin.landing.editor`<br>`superadmin.bulan.index`<br>`superadmin.slot-khusus.index`<br>`superadmin.jurusan.index`<br>`superadmin.bidang.index` |
| **Ikon Tunggal** *(Dashboard, Staf, Hak Akses, Audit, Backup)* | *(Langsung Navigasi)* | Menampilkan **Floating Tooltip** label menu di kanan ikon saat di-hover/focus | Sesuai route masing-masing |

---

## 3. SPESIFIKASI DESAIN VISUAL (SLEMAN DESIGN TOKENS)

Panel melayang (*Flyout*) wajib mengikuti gaya elegan tema resmi MagKom Sleman:

- **Posisi**: `position: fixed;` (Bebas dari clipping `overflow: hidden` pada container sidebar).
- **Koordinat**: `left: 72px;` (Persis 2px di sebelah kanan mini-sidebar 70px), `top: {calculated_top}px`.
- **Dimensi**: `min-width: 220px; max-width: 260px;`.
- **Warna Latar & Border**:
  - Background: Solid Dark Slate `#0F172A` (atau `var(--bg-surface)`).
  - Border: `1px solid rgba(255, 255, 255, 0.12)`.
  - Border Radius: `12px` (`var(--radius-lg)`).
  - Shadow: `0 10px 25px -5px rgba(0, 0, 0, 0.6), 0 8px 10px -6px rgba(0, 0, 0, 0.5)`.
- **Header Flyout**:
  - Label judul grup huruf kapital kecil (*uppercase*), font `Plus Jakarta Sans`, 11px, weight 700, color `#94A3B8`, tracking `0.05em`.
  - Divider bawah halus: `border-bottom: 1px solid rgba(255, 255, 255, 0.08)`.
- **Item Link Submenu**:
  - Padding: `8px 12px`.
  - Font Size: `13px`, weight 500, color `#E2E8F0`.
  - Radius: `8px`.
  - Hover state: Background `rgba(255, 255, 255, 0.08)`, color `#93C5FD`.
  - Active state (halaman saat ini): Background `rgba(37, 99, 235, 0.25)`, color `#60A5FA`, font weight 600, dengan indikator titik biru (*active indicator dot*).
- **Z-Index**: `z-index: 1060;` (Di atas header topbar dan konten utama).

---

## 4. RENCANA IMPLEMENTASI TEKNIS (STEP-BY-STEP)

### Langkah 1: Modifikasi CSS (`resources/css/layouts/gentelella.css` & `dashboard-layout-tokens.css`)
Hapus larangan `display: none !important;` khusus untuk state flyout aktif, dan definisikan styling kelas `.nav-sm-flyout-container`:

```css
/* =========================================================================
   STANDARISASI MINI-SIDEBAR FLOATING FLYOUT MENU (nav-sm)
   ========================================================================= */
@media (min-width: 992px) {
    /* Tetap sembunyikan accordion inline bawaan saat nav-sm */
    body.nav-sm .nav-group-container .nav-sub-menu:not(.flyout-active) {
        display: none !important;
    }

    /* Floating Flyout Portal */
    .nav-sm-flyout-portal {
        position: fixed;
        z-index: 1060;
        min-width: 220px;
        background: #0F172A;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        box-shadow: 0 12px 30px -4px rgba(0, 0, 0, 0.55), 0 6px 12px -3px rgba(0, 0, 0, 0.35);
        padding: 0.5rem;
        animation: navSmFlyoutIn 0.15s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: auto;
    }

    @keyframes navSmFlyoutIn {
        from {
            opacity: 0;
            transform: translateX(-6px) scale(0.97);
        }
        to {
            opacity: 1;
            transform: translateX(0) scale(1);
        }
    }

    .nav-sm-flyout-header {
        padding: 0.5rem 0.75rem 0.375rem;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #94A3B8;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        margin-bottom: 0.375rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .nav-sm-flyout-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.825rem;
        color: #CBD5E1;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.15s ease;
        line-height: 1.3;
    }

    .nav-sm-flyout-item:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #93C5FD;
        transform: translateX(2px);
    }

    .nav-sm-flyout-item.active {
        background: rgba(37, 99, 235, 0.22);
        color: #60A5FA;
        font-weight: 600;
    }

    .nav-sm-flyout-item.active::before {
        content: '';
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #3B82F6;
        box-shadow: 0 0 6px #3B82F6;
    }

    /* Floating Tooltip untuk single item link */
    .nav-sm-tooltip {
        position: fixed;
        z-index: 1060;
        background: #1E293B;
        color: #F8FAFC;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.375rem 0.625rem;
        border-radius: 6px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        pointer-events: none;
        white-space: nowrap;
        animation: navSmTooltipIn 0.12s ease-out;
    }

    @keyframes navSmTooltipIn {
        from { opacity: 0; transform: translateX(-4px); }
        to { opacity: 1; transform: translateX(0); }
    }
}
```

---

### Langkah 2: Logika JavaScript Mini-Sidebar Flyout Engine (`resources/js/layouts/dashboard.js`)

Implementasikan fungsi pengelola flyout mandiri yang aman dan modular:

```javascript
/**
 * Inisialisasi Interaktivitas Flyout untuk Sidebar Mini (nav-sm)
 */
export function initMiniSidebarFlyouts() {
    let activeFlyout = null;
    let activeTooltip = null;

    function closeActiveFlyout() {
        if (activeFlyout && activeFlyout.parentNode) {
            activeFlyout.remove();
            activeFlyout = null;
        }
        document.querySelectorAll('.nav-group-btn.flyout-open').forEach(btn => {
            btn.classList.remove('flyout-open');
        });
    }

    function closeActiveTooltip() {
        if (activeTooltip && activeTooltip.parentNode) {
            activeTooltip.remove();
            activeTooltip = null;
        }
    }

    // Tangani Klik pada Tombol Nav Group saat nav-sm
    document.addEventListener('click', function(e) {
        const isNavSm = document.body.classList.contains('nav-sm') && window.innerWidth >= 992;
        
        // 1. Klik di dalam flyout itu sendiri
        if (e.target.closest('.nav-sm-flyout-portal')) {
            // Jika klik link submenu, biarkan navigasi jalan dan tutup flyout
            if (e.target.closest('a')) {
                closeActiveFlyout();
            }
            return;
        }

        // 2. Klik pada tombol grup menu sidebar
        const groupBtn = e.target.closest('.nav-group-btn');
        if (groupBtn) {
            if (isNavSm) {
                e.preventDefault();
                e.stopPropagation();

                const container = groupBtn.closest('.nav-group-container');
                const subMenu = container ? container.querySelector('.nav-sub-menu') : null;
                const labelSpan = groupBtn.querySelector('.nav-group-btn-left span:not(.nav-item-icon)');
                const groupTitle = labelSpan ? labelSpan.textContent.trim() : 'Menu';

                // Jika sudah terbuka pada tombol ini, toggle tutup
                if (groupBtn.classList.contains('flyout-open')) {
                    closeActiveFlyout();
                    return;
                }

                closeActiveFlyout();
                closeActiveTooltip();

                if (!subMenu) return;

                // Hitung posisi tombol di viewport
                const rect = groupBtn.getBoundingClientRect();
                const flyout = document.createElement('div');
                flyout.className = 'nav-sm-flyout-portal';
                flyout.setAttribute('role', 'menu');

                // Header Grup
                const header = document.createElement('div');
                header.className = 'nav-sm-flyout-header';
                header.textContent = groupTitle;
                flyout.appendChild(header);

                // Kloning tautan submenu
                const links = subMenu.querySelectorAll('.nav-sub-item');
                links.forEach(link => {
                    const flyoutLink = document.createElement('a');
                    flyoutLink.href = link.href;
                    flyoutLink.className = 'nav-sm-flyout-item' + (link.classList.contains('active') ? ' active' : '');
                    flyoutLink.textContent = link.textContent.trim();
                    flyout.appendChild(flyoutLink);
                });

                document.body.appendChild(flyout);

                // Posisi tepat di samping sidebar (72px dari kiri)
                const flyoutHeight = flyout.offsetHeight;
                const windowHeight = window.innerHeight;
                let topPos = rect.top;

                // Cegah terpotong di bagian bawah layar
                if (topPos + flyoutHeight > windowHeight - 16) {
                    topPos = Math.max(16, windowHeight - flyoutHeight - 16);
                }

                flyout.style.left = '72px';
                flyout.style.top = topPos + 'px';

                groupBtn.classList.add('flyout-open');
                activeFlyout = flyout;
                return;
            }
        }

        // 3. Klik di luar area -> Tutup flyout
        closeActiveFlyout();
        closeActiveTooltip();
    });

    // Tangani Hover Tooltip pada Single Nav Item (Dashboard, Staf, Hak Akses, dst.)
    document.addEventListener('mouseover', function(e) {
        const isNavSm = document.body.classList.contains('nav-sm') && window.innerWidth >= 992;
        if (!isNavSm) {
            closeActiveTooltip();
            return;
        }

        const navLink = e.target.closest('.nav-item-link');
        if (navLink && !navLink.closest('.nav-group-container')) {
            const spanText = navLink.querySelector('span:not(.nav-item-icon)');
            if (!spanText) return;

            const text = spanText.textContent.trim();
            if (!text) return;

            closeActiveTooltip();

            const rect = navLink.getBoundingClientRect();
            const tooltip = document.createElement('div');
            tooltip.className = 'nav-sm-tooltip';
            tooltip.textContent = text;
            document.body.appendChild(tooltip);

            tooltip.style.left = '72px';
            tooltip.style.top = (rect.top + (rect.height - tooltip.offsetHeight) / 2) + 'px';
            activeTooltip = tooltip;
        }
    });

    document.addEventListener('mouseout', function(e) {
        const navLink = e.target.closest('.nav-item-link');
        if (navLink) {
            closeActiveTooltip();
        }
    });

    // Tutup saat menekan tombol Escape atau scroll window
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            closeActiveFlyout();
            closeActiveTooltip();
        }
    });

    window.addEventListener('scroll', function() {
        if (activeFlyout) closeActiveFlyout();
        if (activeTooltip) closeActiveTooltip();
    }, { passive: true });
}
```

---

### Langkah 3: Integrasi pada Layout Blade (`resources/views/layouts/dashboard.blade.php`)
Di `resources/views/layouts/dashboard.blade.php`:
1. Pastikan fungsi `initMiniSidebarFlyouts()` dipanggil saat `DOMContentLoaded`.
2. Sempurnakan fungsi inline `toggleNavGroup(btn)` agar saat `document.body.classList.contains('nav-sm')`, fungsi tersebut tidak memicu display inline yang rusak, melainkan menyerahkan ke flyout handler.
3. Tetap pertahankan kompatibilitas CSP dan non-inline modular pattern.

---

## 5. QUALITY GATES & VERIFIKASI PENGUJIAN

Setelah implementasi, lakukan checklist pengujian berikut:

1. **Test Navigasi Mode Kecil (`nav-sm`)**:
   - Ciutkan sidebar via tombol hamburger di topbar.
   - Klik ikon **Administrasi** &rarr; Flyout muncul di `left: 72px` dengan opsi: *Verifikasi Berkas*, *Kuota Magang*, *Penempatan Peserta*, *Laporan & Ekspor*.
   - Klik salah satu opsi &rarr; Halaman berpindah ke route terkait dengan benar.
   - Klik ikon **Pembimbing** &rarr; Flyout muncul dengan opsi: *Review Logbook*, *Tugas Magang*, *Laporan Peserta*.
   - Klik ikon **Peserta** &rarr; Flyout muncul dengan opsi: *Arsip Peserta*.
   - Klik ikon **Konfigurasi** &rarr; Flyout muncul dengan opsi: *Pengaturan*, *Canvas Editor*, *Periode Magang*, *Jalur Khusus*, *Data Jurusan*, *Unit Kerja*.
2. **Test Tooltip Single Item**:
   - Hover pada ikon **Dashboard**, **Data Staf**, **Hak Akses**, **Audit Log**, **Backup Database** &rarr; Tooltip label muncul rapi di sebelah kanan.
3. **Test Dismiss / Auto-Close**:
   - Klik di luar flyout atau tekan tombol `Esc` &rarr; Flyout langsung menghilang mulus.
4. **Test Mode Penuh (`nav-md`)**:
   - Perbesar sidebar kembali &rarr; Accordion inline vertikal berjalan normal seperti semula. Tidak ada flyout yang salah muncul.
5. **Test Mobile Drawer (`<= 1024px`)**:
   - Buka tampilan di layar smartphone/tablet &rarr; Buka sidebar drawer &rarr; Accordion berjalan normal di dalam drawer.
6. **Automated Quality Gates**:
   - `npm run build` &rarr; Build berhasil tanpa error.
   - `./vendor/bin/pint --test` &rarr; Code style passed.
   - `./vendor/bin/phpstan analyse` &rarr; Static analysis 0 error.
   - `php artisan test --filter=SuperAdminPortalVisualTest` &rarr; Passed.

---

## 6. DEFINITION OF DONE (DoD)

- [x] Seluruh submenu (`Administrasi`, `Pembimbing`, `Peserta`, `Konfigurasi`) dapat diakses dan diklik 100% pada sidebar mode mini (`nav-sm`).
- [x] Flyout melayang (*Floating Popover*) memiliki posisi presisi, desain resmi Sleman Dark Slate, dan z-index tinggi tanpa terpotong (*no clipping*).
- [x] Submenu yang sedang aktif ditandai dengan styling *active badge/dot* yang jelas.
- [x] Mode penuh (`nav-md`) dan mode mobile drawer tetap bekerja normal tanpa regresi.
- [x] Semua file CSS dan JS dikelola oleh Vite di `resources/` (tidak ada inline CSS/JS baru di Blade).
- [x] Build aset dan automated tests lulus 100%.
