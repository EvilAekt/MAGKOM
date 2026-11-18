{{-- DEPRECATED: Seluruh halaman autentikasi telah dimigrasikan ke layouts.auth-depth. File ini dipertahankan hanya sebagai fallback legacy. --}}
<!DOCTYPE html>
<html lang="id" data-theme="light" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', __('Autentikasi - Magang Kominfo Sleman'))</title>
    <meta name="description" content="@yield('meta_description', __('Portal Magang Resmi Dinas Komunikasi dan Informatika Kabupaten Sleman.'))">

    <!-- Favicon Resmi Sleman / MagKom -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Preload Aset Logo Prioritas Tinggi (Cegah Flicker / Kedipan saat Navigasi) -->
    <link rel="preload" as="image" href="{{ asset('images/logo-sleman.png') }}" fetchpriority="high">

    <!-- Google Fonts: Lexend (Heading/Display), Source Sans 3 (Body/Form) & Open Sans (DS Fallback) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700;800&family=Source+Sans+3:wght@400;500;600;700&family=Open+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

    <!-- Vite Asset Pipeline (PRD-TASK-VITE-01) -->
    @vite(['resources/css/app.css', 'resources/css/layouts/auth-layout-tokens.css', 'resources/js/app.js'])

    <!-- Anti-Flash Theme Script (External, CSP compliant) -->
    <script src="{{ asset('js/theme-init.js') }}"></script>

</head>
<body>
    <div class="auth-container">
        <!-- Panel Kiri (Visual & Branding Sleman Presisi Figma) -->
        <aside class="auth-visual-panel" aria-label="{{ __('Branding MagKom Kominfo Sleman') }}">
            <div class="auth-visual-overlay"></div>
            <div class="auth-visual-content">
                <!-- Header Visual (Lambang Sleman & Logo MagKom) -->
                <div style="display: flex; align-items: center; gap: 0.875rem;">
                    @include('partials.logo-sleman', ['width' => '44', 'height' => '44', 'style' => 'width: 44px; height: 44px; object-fit: contain; filter: drop-shadow(0 2px 6px rgba(0,0,0,0.25));'])
                    <div>
                        <div style="font-size: 1.125rem; font-weight: 800; letter-spacing: 0.05em; line-height: 1.2;">MAGKOM</div>
                        <div style="font-size: 0.75rem; font-weight: 600; opacity: 0.9; letter-spacing: 0.04em; text-transform: uppercase;">{{ __('MAGANG KOMINFO SLEMAN') }}</div>
                    </div>
                </div>

                <!-- Bagian Tengah: Headline & Deskripsi (Center Vertikal) -->
                <div class="visual-center-content">
                    <h1 class="visual-headline">
                        {{ __('DINAS KOMUNIKASI DAN INFORMATIKA') }}
                    </h1>
                    <p class="visual-subtext">
                        {{ __('Platform digital terintegrasi untuk administrasi, pemantauan, dan penilaian program magang siswa SMK dan mahasiswa di Kabupaten Sleman.') }}
                    </p>
                </div>

                <!-- Bagian Bawah: Pill di atas Copyright Footer -->
                <div class="visual-bottom-group">
                    <div class="brand-pill">
                        <span class="brand-pill-dot"></span>
                        <span>{{ __('Sistem Magang Terpadu Sleman') }}</span>
                    </div>

                    <div class="visual-footer">
                        &copy; {{ date('Y') }} {{ __('Dinas Komunikasi dan Informatika Kabupaten Sleman. Seluruh hak cipta dilindungi.') }}
                    </div>
                </div>
            </div>
        </aside>

        <!-- Panel Kanan (Form Interaktif) -->
        <main class="auth-form-panel">
            <!-- Header Bar (Back Link) -->
            <div class="auth-form-header-bar">
                @php
                    $isFromKuota = request()->hasAny(['bulan', 'jurusan', 'tipe', 'jumlah']);
                    $authBackUrl = $isFromKuota ? route('landing') . '#kuota' : route('landing');
                    $authBackText = $isFromKuota ? __('Pilih Kuota Lain') : __('Kembali ke beranda');
                @endphp
                <a href="{{ $authBackUrl }}" class="back-link" title="{{ $authBackText }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>{{ $authBackText }}</span>
                </a>
            </div>

            <!-- Konten Form Dinamis -->
            <div class="auth-form-wrapper">
                <div class="alerts-container">
                    <x-flash-alert />
                </div>
                @yield('content')
            </div>

            <!-- Footer Hak Cipta Ringkas Mobile -->
            <div style="max-width: 480px; width: 100%; margin: 1.5rem auto 0 auto; text-align: center; font-size: 0.75rem; color: var(--neutral-400);">
                {{ __('Magang Kominfo') }} &bull; {{ __('Dinas Kominfo Sleman') }} &bull; v1.0
            </div>
        </main>
    </div>
</body>
</html>
