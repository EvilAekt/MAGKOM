# Arsitektur Frontend MAGKOM

| Informasi | Keterangan |
|---|---|
| Status | Verified |
| Terakhir Diverifikasi | 2026-11-18 |
| Cakupan | Blade templating, Vite bundler, Dark Mode Engine, Multi-Bahasa, CSS Tokens, ES Modules, GSAP, Kepatuhan CSP |
| Referensi Implementasi | `resources/views/`, `resources/css/`, `resources/js/`, `public/js/theme-init.js`, `vite.config.js` |

---

## 1. Pendahuluan dan Filosofi Frontend

Frontend MAGKOM dibangun dengan pendekatan **Vanilla Modern & Zero Inline Code**. 

Sistem menolak penggunaan Single Page Application (SPA) monolitik yang berlebihan (seperti React atau Vue penuh) untuk aplikasi kedinasan yang membutuhkan efisiensi pemuatan cepat, rendering server-side yang andal melalui Laravel Blade, serta kepatuhan penuh terhadap standar keamanan siber pemerintah.

### Tiga Pilar Prinsip Frontend MAGKOM:
1. **Zero Inline CSS**: Tidak boleh ada tag `<style>` atau atribut `style="..."` di dalam seluruh berkas Blade.
2. **Zero Inline JavaScript**: Tidak boleh ada tag `<script>` atau atribut event handler inline (`onclick`, `onchange`, `onsubmit`, dll.) di dalam seluruh berkas Blade.
3. **Kepatuhan Mutlak CSP (`script-src 'self'`)**: Seluruh interaktivitas JavaScript dan presentasi visual wajib dikompilasi melalui Vite dan dimuat sebagai modul eksternal terisolasi.

Kepatuhan ini diverifikasi secara otomatis oleh suite pengujian `Tests\Feature\SecurityHeadersProductionTest` pada seluruh **185 file Blade view** di dalam proyek.

---

## 2. Pipeline Pembangunan Aset (Vite 6)

MAGKOM menggunakan **Vite 6.x** bersama **Laravel Vite Plugin 1.3.x** untuk mengelola seluruh aset frontend.

### 2.1 Struktur Entri Aset
Berdasarkan `vite.config.js`, sistem mendaftarkan **87 aset entri mandiri** yang terdiri dari:
- **39 Stylesheet CSS**: Terbagi atas Master Bundle (`app.css`), Design Tokens Layout (`app-layout-tokens.css`, `auth-layout-tokens.css`), dan Stylesheet Scoped per halaman (Admin, Pembimbing, Peserta, SuperAdmin, dan Publik).
- **48 Modul JavaScript**: Terbagi atas Core Engine (`app.js`, `bootstrap.js`), Layanan Realtime SSE (`realtime.js`), dan Skrip Interaktivitas Halaman (seperti Site Editor, Bento Dashboard, Laporan Kinerja, dan Modal Controller).

### 2.2 Struktur Direktori `resources/`
```text
resources/
├── css/
│   ├── app.css                         # Berkas CSS global utama
│   ├── base/
│   │   ├── variables.css               # 11 Master CSS Peeper Tokens & Dark Mode
│   │   └── reset.css                   # Normalisasi CSS dan universal reset
│   ├── components/                     # Modul komponen UI reusable
│   │   ├── buttons.css                 # Standar styling tombol dan varian
│   │   ├── cards.css                   # Kartu bento, kartu statistik
│   │   ├── modals.css                  # Modal konfirmasi aksi kritis
│   │   ├── tables.css                  # Tabel data responsif
│   │   └── notifications.css           # Toast alert dan flash messages
│   ├── layouts/                        # Kerangka layout utama
│   │   ├── app-layout-tokens.css       # Token styling layout publik
│   │   ├── auth-layout-tokens.css      # Token styling halaman login/register
│   │   ├── dashboard.css               # Shell dashboard dan navigasi sidebar
│   │   ├── gentelella.css              # Adaptasi modern layout Gentelella
│   │   └── peserta.css                 # Shell portal peserta
│   └── pages/                          # Stylesheet khusus per halaman (Vite entry)
│       ├── public/                     # landing.css, panduan-alur.css, peta-alur.css
│       ├── superadmin/                 # dashboard.css, audit-log.css, site-editor.css, role-index.css
│       ├── admin/                      # dashboard.css, kuota-index.css, penempatan-index.css, pendaftaran-index.css
│       ├── pembimbing/                 # dashboard.css, tugas-*.css, peserta-*.css, logbook-index.css
│       └── peserta/                    # dashboard.css (Bento), tugas-*.css, logbook-index.css, pemberkasan.css
│
├── js/
│   ├── app.js                          # Entry point JS global
│   ├── bootstrap.js                    # Konfigurasi Axios dan header CSRF
│   ├── components/
│   │   ├── toast.js                    # Modul notifikasi toast anti-XSS
│   │   └── modal.js                    # Modul pengontrol dialog modal konfirmasi
│   ├── layouts/
│   │   ├── dashboard.js                # Interaktivitas sidebar drawer & nav group
│   │   └── gentelella.js               # Pengendali mini-sidebar floating flyout
│   ├── services/
│   │   ├── realtime.js                 # Klien Server-Sent Events (SSE)
│   │   └── superadmin-motion.js        # Engine mikro-interaksi & timeline GSAP
│   └── pages/                          # Modul interaktivitas scoped per halaman (Vite entry)
│       ├── public/                     # landing.js, landing-canvas-runtime.js, peta-alur.js
│       ├── superadmin/                 # dashboard.js, audit-log-index.js, site-editor/editor-app.js
│       ├── admin/                      # dashboard.js, kuota-index.js, penempatan-index.js
│       ├── pembimbing/                 # dashboard.js, tugas-create.js, logbook-index.js
│       └── peserta/                    # dashboard.js, tugas-detail.js, logbook-index.js
│
└── views/                              # Template Blade (185 berkas)
    ├── layouts/                        # Master shell: app, dashboard, peserta, auth
    ├── components/                     # Blade components reusable: navbar, sidebar, alert
    ├── public/                         # Halaman beranda publik, panduan alur, verifikasi berkas
    ├── auth/                           # Formulir login, register, reset password, verifikasi email
    ├── superadmin/                     # Panel kelola parameter, akun, audit log, backup, site editor
    ├── admin/                          # Panel verifikasi pendaftaran, antrean penempatan, kuota
    ├── pembimbing/                     # Panel penugasan batch, review logbook, rekap kinerja
    └── peserta/                        # Panel bento dashboard, upload tugas classroom, logbook H-3
```

---

## 3. Tata Kelola Blade Template

### 3.1 Pemuatan Aset pada Master Layout
Pada master layout (`resources/views/layouts/dashboard.blade.php`, `peserta.blade.php`, `app.blade.php`), dependensi dasar dimuat menggunakan direktif `@vite`:

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MAGKOM')</title>

    {{-- Pemuatan Core Aset via Vite --}}
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    {{-- Penampung Stylesheet Halaman Spesifik --}}
    @stack('styles')
</head>
<body class="theme-light">
    <div class="app-wrapper">
        @include('layouts.partials.sidebar')
        <main class="main-content">
            @yield('content')
        </main>
    </div>

    {{-- Penampung Skrip Halaman Spesifik --}}
    @stack('scripts')
</body>
</html>
```

### 3.2 Pemuatan Scoped Aset per Halaman
Ketika halaman tertentu membutuhkan stylesheet atau skrip interaktif khusus, pengembang menyematkannya melalui `@push`:

```blade
@extends('layouts.dashboard')

@section('title', 'Penugasan Bimbingan Serentak')

@push('styles')
    @vite(['resources/css/pages/pembimbing/tugas-create.css'])
@endpush

@section('content')
    <div class="tugas-create-container">
        {{-- Konten Formulir Penugasan --}}
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/pages/pembimbing/tugas-create.js'])
@endpush
```

### 3.3 Standar Sintaks & Escaping Atribut Komponen Blade
Saat menyisipkan string HTML kompleks atau translasi ke dalam atribut komponen Blade (misalnya `<x-sa.panel :title="...">`), pengembang wajib menghindari tabrakan tanda kutip tunggal (*unescaped nested quotes*).
- **Benar (Escaped Quotes)**:
  ```blade
  <x-sa.panel :title="__('Pelacak Penyelesaian') . '<span class=\'sr-only\'> ' . __('(Off-boarding)') . '</span>'" ...>
  ```
- **Salah (Memicu Syntax Parse Error)**:
  ```blade
  <x-sa.panel :title="__('Pelacak Penyelesaian') . '<span class='sr-only'> ' . __('(Off-boarding)') . '</span>'" ...>
  ```
Pelanggaran aturan ini akan menyebabkan kompilasi view Laravel menghasilkan syntax error `unexpected identifier` dan memunculkan indikator error merah pada explorer IDE.

---

## 4. Sistem Desain & CSS Tokens (DS-MagKom)

MAGKOM mengadopsi variabel CSS native (CSS Custom Properties) yang dipusatkan pada `resources/css/base/variables.css`.

### 4.1 Master Design Tokens
Sistem mengunci 11 warna dasar (*Master CSS Peeper Tokens*) yang mewakili identitas visual Diskominfo Sleman:

```css
:root {
    /* Brand Identitas Sleman Kominfo */
    --magkom-primary-navy: #02275D;
    --magkom-royal-blue:   #01347C;
    --magkom-deep-blue:    #003566;
    --magkom-accent-indigo:#3F51B5;

    /* Aksen & Status */
    --magkom-gold-yellow:  #FFC300;
    --magkom-vibrant-gold: #FFCB05;

    /* Netral & Tipografi */
    --magkom-dark-text:    #212529;
    --magkom-pure-black:   #000000;
    --magkom-neutral-gray: #AAAAAA;
    --magkom-border-gray:  #BBBBBB;
    --magkom-pure-white:   #FFFFFF;

    /* Semantic Status */
    --status-success:      #10B981;
    --status-warning:      #F59E0B;
    --status-danger:       #EF4444;
    --status-info:         #3B82F6;
}
```

### 4.2 Bento Grid Layout Architecture
Dashboard Peserta dan Admin menerapkan arsitektur tata letak **Bento Grid** modern menggunakan native CSS Grid:
- Mengelompokkan kartu informasi (ringkasan status magang, linimasa progres, kalender aktivitas logbook, dan daftar tugas ber-deadline) secara visual ergonomis.
- Tampilan beradaptasi mulus dari grid 4 kolom di layar desktop, 2 kolom pada tablet, hingga 1 kolom linier pada perangkat mobile (`grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));`).

### 4.3 Arsitektur Dark Mode & Anti-Flash Engine (`public/js/theme-init.js`)
Untuk mencegah *Flash of Unstyled Content* (FOUC) atau kedipan kontras saat pengguna berpindah halaman, MAGKOM mengimplementasikan engine inisialisasi tema yang dieksekusi secara sinkron di dalam tag `<head>` sebelum peramban merender elemen `<body>`:

1. **Kepatuhan Ketat CSP**: Skrip diletakkan pada berkas publik terdedikasi `public/js/theme-init.js` dan dimuat via `<script src="{{ asset('js/theme-init.js') }}"></script>` tanpa inline script.
2. **Pagar Pengaman Dua Lapis (Two-Tier Isolation Guard)**:
   Engine tema secara ketat memverifikasi apakah halaman saat ini merupakan salah satu dari 4 portal internal:
   ```javascript
   const isDashboard = document.documentElement.getAttribute('data-portal') === 'dashboard' ||
       /^\/(peserta|pembimbing|admin|superadmin)($|\/)/i.test(window.location.pathname);

   if (!isDashboard) {
       document.documentElement.setAttribute('data-theme', 'light');
       document.documentElement.setAttribute('data-pref-theme', 'terang');
   } else {
       // Evaluasi preferensi pengguna: localStorage -> cookie -> prefers-color-scheme
   }
   ```
3. **Penguncian Statis Halaman Publik & Autentikasi**:
   Seluruh halaman non-dashboard (Landing Page, Login, Register, Lupa Kata Sandi, Verifikasi Email, Peta Alur, dan Kebijakan Privasi) secara mutlak dikunci pada `data-theme="light"` dan `lang="id"`. Engine akan membatalkan pembacaan preferensi gelap jika user membuka halaman publik/auth.
4. **Hierarki Evaluasi Preferensi Tema**:
   - Jika nilai preferensi adalah `'sistem'`: Mengevaluasi `window.matchMedia('(prefers-color-scheme: dark)')`.
   - Jika nilai preferensi adalah `'gelap'`: Menetapkan `data-theme="dark"`.
   - Default: Menetapkan `data-theme="light"`.

### 4.4 Arsitektur Multi-Bahasa Frontend (Localization Parity)
MAGKOM mendukung lokalisasi antarmuka dwibahasa (Bahasa Indonesia `id` dan Bahasa Inggris `en`):
1. **Penyimpanan Terjemahan JSON**: Terpusat pada berkas `lang/id.json` dan `lang/en.json` dengan lebih dari 4.000 pasang frasa yang mencakup seluruh modul dashboard, label metrik, header tabel, formulir, pesan validasi, dan navigasi.
2. **Pemanggilan Standar di View**: Menggunakan helper translasi Laravel `__('Frasa Asli')` atau direktif Blade `@lang('Frasa Asli')`.
3. **Penjaminan Kualitas Paritas**: Suite pengujian unit `Tests\Unit\LangParityTest` memvalidasi bahwa setiap kunci yang ada di `lang/id.json` memiliki padanan identik di `lang/en.json` dan tidak memiliki nilai kosong (*empty strings*).

### 4.5 Standarisasi Shell Multi-Peran (Sidebar & Footer Layout Parity)
Untuk mencegah disparitas visual antarmuka antar role pengguna, seluruh layout shell (`admin`, `pembimbing`, `peserta`, dan `superadmin`) distandarisasi secara seragam:
1. **Palet Warna Sidebar**: Menggunakan token kedinasan `--magkom-abyssal-navy` (`#02275D`) pada tema terang dan `--dm-navy-deep` (`#031B40`) pada tema gelap, dengan garis batas kanan 1px solid yang presisi.
2. **Sinkronisasi Navigasi Keyboard (Tombol Esc) & Hamburger Toggle**:
   - Mengharmonisasi perilaku tombol `Esc` dan hamburger toggle di seluruh 4 layout dashboard. Menekan `Esc` saat drawer atau mini-sidebar flyout aktif akan menutup overlay secara aman tanpa memicu deformasi layout atau konflik state CSS antar modul (`nav-md` vs `nav-sm`).
   - Resolusi *height clamping*: Mengatasi pemotongan tinggi menu navigasi pembimbing dengan memastikan kontainer `.sidebar-menu-wrapper` dan `.menu_section` memiliki ketinggian dinamis yang menyesuaikan viewport (`min-height: calc(100vh - 80px)`).
3. **Harmonisasi Branding Judul Merek MAGKOM**:
   - Seluruh elemen identitas nama merek `MAGKOM` pada topbar navigasi internal maupun navbar publik dikunci menggunakan warna putih solid (`#FFFFFF`) terstandarisasi, memastikan kontras optimal di atas latar belakang navbar Abyssal Navy kedinasan.
4. **Footer Persisten Kanan**: Footer dashboard di seluruh peran dikunci rata kanan dengan padding seragam `12px 32px` (desktop), teks `#c9d6ea`, latar belakang `#003566` (terang) dan `#031B40` (gelap), memastikan informasi hak cipta sejajar dengan batas kanan kanvas konten.

---

## 5. Pola Interaktivitas JavaScript (ES Modules)

### 5.1 Standar Penulisan Skrip
1. **ES Modules (`type="module"`)**: Seluruh skrip menggunakan sintaks `import` dan `export` standar ES2022.
2. **Anti-Duplikasi Event Listener**: Setiap listener diikat dengan pengecekan guard atau inisialisasi satu kali saat `DOMContentLoaded`.
3. **Pemberitahuan Aman Anti-XSS**: Komponen toast (`resources/js/components/toast.js`) memanipulasi teks pesan secara strictly aman via `textContent` atau sanitasi DOM, dilarang menyisipkan string mentah ke `innerHTML`.

### 5.2 Integrasi Animasi GSAP 3.15
Sistem memanfaatkan **GSAP 3.15.x** untuk menghasilkan transisi mikro yang elegan tanpa menurunkan performa browser:
- **Preloader Splash Screen**: Animasi pembuka berlogo khas Pemerintah Kabupaten Sleman pada kunjungan pertama portal publik dan dashboard peserta.
- **Kepatuhan Kinerja**: Seluruh animasi memanipulasi properti transform (`transform: translate3d(...)`) dan transparansi (`opacity`), menghindari layout thrashing yang memicu penurunan frame rate (*jank*).

### 5.3 Klien Server-Sent Events (SSE)
Pembaruan data realtime dikonsumsi melalui `resources/js/services/realtime.js`:
- Membuka koneksi `EventSource` ke rute `/realtime/stream`.
- Mengatur *automatic reconnection* dengan backoff strategi saat koneksi jaringan terputus.
- Membatasi konsumsi koneksi agar ramah terhadap resource browser klien dan pool worker web server.

### 5.4 Arsitektur Canvas Bridge Runtime & Site Editor Click-to-Edit
Modul Canvas Editor SuperAdmin (`/superadmin/site-editor`) menerapkan pola koordinasi frame tanpa perantara drawer:
1. **Pemisahan Konteks Runtime**:
   - `editor-app.js` bertindak sebagai *master orchestrator* pada parent frame yang mengelola state draf dokumen, riwayat versi, dan panel Inspector kanan.
   - `landing-canvas-runtime.js` berjalan secara terisolasi di dalam iframe kanvas preview (`/preview/landing`), memantau interaksi hover, seleksi klik pada blok section, dan floating toolbar.
   - `canvas-bridge.js` menyediakan abstraksi pesan aman (`postMessage`) dua arah antar-frame.
2. **Alur Interaksi Click-to-Edit Terintegrasi**:
   - Ketika elemen mana pun di dalam kanvas diklik, event click dipetakan ke section terdekat beratribut `data-block-id`.
   - Runtime mengirimkan payload pesan `BLOCK_SELECTED` ke parent frame.
   - Parent frame menerima sinyal, mengaktifkan status visual terpilih, dan secara otomatis menampilkan panel Inspector sisi kanan (`#wp-inspector`) dengan tab pengaturan (General, Style, Advanced) yang relevan langsung terfokus.
   - Drawer Inserter blok dan tombol `+` dihilangkan sepenuhnya, membebaskan DOM dari 15 KB kode mati dan menyederhanakan alur kerja pengguna.

### 5.5 Formulir Registrasi Tim Dinamis & Penguncian Kuota (`auth-depth.js`)
Formulir pendaftaran akun peserta (`resources/js/pages/auth/auth-depth.js`) menghadirkan interaktivitas reaktif di sisi klien:
1. **Dynamic Team Roster Cards**: Ketika peserta memilih jalur `kelompok`, skrip merender kartu input dinamis untuk anggota tim (maksimal 4 orang: 1 ketua + 3 anggota) dengan validasi real-time terhadap NISN/NIM, nama lengkap, dan email agar tidak terjadi data duplikat dalam satu rombongan.
2. **Preselected Quota Locking**: Skrip mengevaluasi parameter query URL (`?bulan=...&jurusan=...`), mengunci dropdown pilihan formasi dan tipe pendaftaran agar konsisten dengan pilihan peserta di Landing Page, serta menampilkan kartu ringkasan status formasi secara elegan.
