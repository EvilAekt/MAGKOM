# ARSITEKTUR FRONTEND MAGKOM
**Sistem:** Magang Kominfo Sleman (MAGKOM)  
**Bundler & Pipeline:** Vite 6 + Laravel Vite Plugin  
**Teknologi:** Blade Templates + Scoped Vanilla CSS + Modular Vanilla JavaScript  
**Prinsip Utama:** Zero UI Modification (100% Visual Parity), CSP Compliance, Strict Separation of Concerns  

---

## 1. PENGELOLAAN ASSET DENGAN VITE

Pengelolaan stylesheet dan script frontend MAGKOM dipusatkan sepenuhnya di dalam direktori `resources/`, menggantikan ketergantungan pada pemuatan file manual dari `public/css/` dan `public/js/` atau blok inline `<style>` dan `<script>` di Blade.

### 1.1 Struktur Direktori Frontend
```text
resources/
│
├── css/
│   ├── app.css                         <-- Bundled Master Stylesheet
│   │
│   ├── base/                           <-- Foundation, Tokens & Reset
│   │   ├── variables.css               <-- 11 Master CSS Peeper Tokens & Dark Mode
│   │   └── reset.css                   <-- Resets & Universal Style Inversion
│   │
│   ├── components/                     <-- Reusable UI Components
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
│   └── pages/                          <-- Page-Scoped Stylesheets
│       ├── public/
│       │   ├── landing.css
│       │   ├── landing-faq.css
│       │   ├── landing-peta-alur-modal.css
│       │   ├── panduan-alur.css
│       │   └── peta-alur.css
│       │
│       ├── admin/
│       │   └── dashboard.css
│       │
│       └── superadmin/
│           ├── dashboard.css
│           ├── audit-log.css
│           └── modul-builder.css
│
├── js/
│   ├── app.js                          <-- Main JS Entry Point
│   ├── bootstrap.js                    <-- Axios & CSRF Configuration
│   │
│   ├── components/                     <-- Shared UI Logic
│   │   ├── toast.js                    <-- Secure DOM XSS-Free MagKomToast
│   │   └── modal.js
│   │
│   ├── layouts/                        <-- Shell Interactions
│   │   ├── dashboard.js                <-- Sidebar Drawer, NavGroup, Theme Toggle
│   │   ├── dashboard-cockpit.js
│   │   └── gentelella.js
│   │
│   ├── services/                       <-- Network & Realtime
│   │   └── realtime.js                 <-- Secure SSE Client (RFC 8895)
│   │
│   └── pages/                          <-- Page-Specific Interactivity
│       ├── public/
│       │   ├── landing.js
│       │   ├── panduan-alur.js
│       │   └── peta-alur.js
│       │
│       └── superadmin/
│           └── dashboard.js
```

---

## 2. PENGGUNAAN BLADE & VITE DIRECTIVES

### 2.1 Pemuatan Asset pada Layout
Pada master layout (`resources/views/layouts/dashboard.blade.php`, `peserta.blade.php`, `auth.blade.php`, `app.blade.php`), aset dimuat melalui direktif `@vite`:

```blade
<!-- Vite Production Pipeline -->
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

Untuk halaman yang memiliki stylesheet atau script spesifik (misal: simulator peta alur atau audit log SuperAdmin):
```blade
@vite([
    'resources/css/pages/superadmin/audit-log.css'
])
```

### 2.2 Migrasi Inline Event Handler ke Event Delegation
Pola pemanggilan fungsi langsung pada HTML yang rentan terhadap CSP:
```html
<!-- Format Lama (Raw Event Handler) -->
<button onclick="dismissToast(this)">Tutup</button>
<button onclick="toggleSidebar()">Menu</button>

<!-- Format Bersih (Data Attribute + Event Delegation) -->
<button data-action="dismiss-toast">Tutup</button>
<button data-action="toggle-sidebar">Menu</button>
```
Logika ditangani oleh listener terpusat di `resources/js/layouts/dashboard.js` dan `resources/js/components/toast.js`.

---

## 3. PANDUAN MENAMBAHKAN HALAMAN BARU

1. **Membuat File Blade:** Tempatkan file view di `resources/views/{role}/{fitur}/index.blade.php`. Turunkan dari `@extends('layouts.dashboard')`.
2. **Menambahkan Style Khusus (jika diperlukan):**
   - Buat file di `resources/css/pages/{role}/{fitur}.css`.
   - Daftarkan entry point pada `vite.config.js` di array `input`.
   - Panggil menggunakan `@vite(['resources/css/pages/{role}/{fitur}.css'])` di dalam `@push('styles')`.
3. **Menambahkan Interaktivitas JavaScript:**
   - Buat file di `resources/js/pages/{role}/{fitur}.js`.
   - Jangan gunakan interpolasi `{{ $variabel }}` di dalam file JS eksternal. Gunakan atribut data HTML:
     ```blade
     <div id="workspace" data-api-url="{{ route('api.endpoint') }}" data-id="{{ $item->id }}"></div>
     ```
   - Daftarkan file JS pada `vite.config.js`.
4. **Validasi Build:** Jalankan `npm run build` untuk memverifikasi kompilasi manifest Vite tanpa error.

