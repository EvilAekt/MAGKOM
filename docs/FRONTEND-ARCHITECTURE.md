# Arsitektur Frontend & Panduan Aset Vite MagKom

**Sistem:** Magang Kominfo Sleman (MAGKOM)  
**Bundler & Pipeline:** Vite 6.x + Laravel Vite Plugin 1.3.x  
**Teknologi:** Blade Templates + Scoped Vanilla CSS + Modular Vanilla JavaScript (ES Modules) + GSAP 3.15  
**Prinsip Utama:** Zero UI Modification (100% Visual Parity), Kepatuhan Ketat CSP (`script-src 'self'`), Strict Separation of Concerns  

---

## 1. Pengelolaan Aset Frontend dengan Vite 6

Pengelolaan stylesheet dan script frontend MAGKOM dipusatkan sepenuhnya di dalam direktori `resources/`, menggantikan ketergantungan pada pemuatan file manual dari `public/css/` dan `public/js/` atau blok inline `<style>` dan `<script>` di Blade.

### 1.1 Struktur Direktori Frontend
```text
resources/
├── css/
│   ├── app.css                         <-- Bundled Master Stylesheet
│   │
│   ├── base/                           <-- Fondasi, Token & Reset
│   │   ├── variables.css               <-- 11 Master CSS Peeper Tokens & Dark Mode
│   │   └── reset.css                   <-- Resets & Universal Style Inversion
│   │
│   ├── components/                     <-- Komponen Reusable UI
│   │   ├── buttons.css
│   │   ├── cards.css
│   │   ├── modals.css
│   │   ├── tables.css
│   │   ├── pagination.css
│   │   └── notifications.css           <-- Toast & Flash Alerts
│   │
│   ├── layouts/                        <-- Structural Layout Styling
│   │   ├── dashboard.css               <-- Main Dashboard Shell & Sidebar
│   │   ├── dashboard-cockpit.css       <-- Cockpit Widgets & Floating Drawer
│   │   ├── gentelella.css              <-- Gentelella Modern Styling
│   │   ├── peserta.css
│   │   └── public.css
│   │
│   └── pages/                          <-- Page-Scoped Stylesheets (Vite Entry Points)
│       ├── public/                     <-- landing.css, landing-faq.css, panduan-alur.css, peta-alur.css
│       ├── superadmin/                 <-- dashboard.css, audit-log.css, site-editor.css, role-index.css, superadmin-common.css
│       ├── admin/                      <-- dashboard.css
│       ├── pembimbing/                 <-- dashboard.css, peserta-index.css, peserta-show.css (Profil 360),
│       │                                   tugas-create.css, tugas-edit.css, tugas-index.css, tugas-review.css,
│       │                                   logbook-index.css, laporan-kinerja-index.css
│       └── peserta/                    <-- dashboard.css, pendaftaran-index.css, pemberkasan.css, profil-show.css,
│                                           tugas-index.css, tugas-detail.css, logbook-index.css
│
└── js/
    ├── app.js                          <-- Main JS Entry Point
    ├── bootstrap.js                    <-- Axios & CSRF Configuration
    │
    ├── components/                     <-- Shared UI Logic
    │   ├── toast.js                    <-- Secure DOM XSS-Free Toast Component
    │   └── modal.js                    <-- Modal Controller
    │
    ├── layouts/                        <-- Shell Interactions
    │   ├── dashboard.js                <-- Sidebar Drawer, NavGroup, Theme Toggle
    │   ├── dashboard-cockpit.js
    │   └── gentelella.js
    │
    ├── services/                       <-- Network & Realtime & Animasi
    │   ├── realtime.js                 <-- Secure SSE Client (RFC 8895)
    │   └── superadmin-motion.js        <-- GSAP Timeline & Micro-Interactions
    │
    └── pages/                          <-- Page-Specific Interactivity (Vite Entry Points)
        ├── public/                     <-- landing.js, landing-canvas-runtime.js, panduan-alur.js, peta-alur.js
        ├── superadmin/                 <-- dashboard.js, cms-builder.js, site-editor/editor-app.js, role-index.js, slot-khusus.js
        ├── pembimbing/                 <-- dashboard.js, peserta-index.js, peserta-show.js, tugas-create.js, tugas-edit.js,
        │                                   tugas-index.js, tugas-review.js, logbook-index.js, laporan-kinerja-index.js
        └── peserta/                    <-- tugas-detail.js, tugas-index.js, logbook-index.js, profil-show.js
```

---

## 2. Penggunaan Blade & Direktif Vite

### 2.1 Pemuatan Aset pada Master Layout
Pada master layout (`resources/views/layouts/dashboard.blade.php`, `peserta.blade.php`, `auth.blade.php`, `app.blade.php`), bundle dasar dimuat melalui direktif `@vite`:

```blade
<!-- Vite Base Pipeline -->
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

### 2.2 Pemuatan Scoped Aset per Halaman
Untuk halaman yang memiliki kebutuhan stylesheet atau script interaktif khusus (misalnya Profil 360 Mentee atau Site Editor):

```blade
@push('styles')
    @vite([
        'resources/css/pages/pembimbing/peserta-show.css'
    ])
@endpush

@push('scripts')
    @vite([
        'resources/js/pages/pembimbing/peserta-show.js'
    ])
@endpush
```

### 2.3 Aturan Anti-Inline: Data Attributes & Event Delegation
Sistem menerapkan Content Security Policy ketat (`script-src 'self'`). Pemanggilan fungsi langsung melalui inline event handler pada markup HTML **DILARANG**:

```html
<!-- DILARANG: Inline Event Handler (Melanggar CSP) -->
<button onclick="dismissToast(this)">Tutup</button>
<button onclick="openModal('modal-staf')">Tambah</button>

<!-- BENAR: Data Attribute + Event Delegation di File JS Modular -->
<button data-action="dismiss-toast">Tutup</button>
<button data-modal-target="modal-staf" data-action="open-modal">Tambah</button>
```

---

## 3. Integrasi Animasi & Mikro-Interaksi GSAP 3.15

MAGKOM menggunakan pustaka resmi **GSAP (GreenSock Animation Platform) 3.15.1** untuk mikro-interaksi antarmuka tanpa merusak performa peramban:
- **Dashboard Telemetri & Bento Cards**: Transisi *staggered entrance animation* saat memuat kartu metrik.
- **Profil 360 Mentee**: Animasi rotasi halus pada donut chart capaian tugas dan animasi progres durasi magang.
- **Micro-Interactions**: Efek hover interaktif pada bar penugasan dan status badge.
- **Kepatuhan Preferensi Aksesibilitas**: Seluruh animasi GSAP menghormati media query `prefers-reduced-motion: reduce`. Jika pengguna mengaktifkan preferensi ini di tingkat OS, durasi animasi otomatis disetel ke `0` (*instant transition*).

---

## 4. Panduan Menambahkan Halaman Baru

1. **Membuat File Blade:** Tempatkan file view di `resources/views/{role}/{fitur}/index.blade.php`. Turunkan dari `@extends('layouts.dashboard')`.
2. **Menambahkan Style Khusus (jika diperlukan):**
   - Buat file di `resources/css/pages/{role}/{fitur}.css`.
   - Daftarkan entry point pada array `input` di [vite.config.js](../vite.config.js).
   - Muat via `@push('styles')` dengan direktif `@vite(['resources/css/pages/...'])`.
3. **Menambahkan Script Khusus:**
   - Buat file di `resources/js/pages/{role}/{fitur}.js`.
   - Gunakan atribut HTML5 `data-*` untuk mentransfer data dinamis dari server ke JavaScript. Dilarang menyisipkan tag `<script>` inline berisi variabel Blade.
   - Daftarkan file JS pada [vite.config.js](../vite.config.js).
4. **Validasi Kompilasi:** Jalankan `npm run build` untuk memastikan seluruh manifest terkompilasi bersih tanpa error.
