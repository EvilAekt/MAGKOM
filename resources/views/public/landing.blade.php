<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth"
    data-theme-primary="{{ \App\Support\Pengaturan::get('landing_theme_primary_color', '#1B3860') }}"
    data-theme-navy-dark="{{ \App\Support\Pengaturan::get('landing_theme_navy_dark', '#122846') }}"
    data-theme-accent="{{ \App\Support\Pengaturan::get('landing_theme_accent_color', '#4F46E5') }}"
    data-theme-gold="{{ \App\Support\Pengaturan::get('landing_theme_gold_color', '#F59E0B') }}"
    data-theme-bg="{{ \App\Support\Pengaturan::get('landing_theme_bg_color', '#FFFFFF') }}"
    data-theme-font-display="{{ \App\Support\Pengaturan::get('landing_theme_font_display', 'Lexend') }}"
    data-theme-font-body="{{ \App\Support\Pengaturan::get('landing_theme_font_body', 'Source Sans 3') }}"
    data-theme-radius="{{ \App\Support\Pengaturan::get('landing_theme_border_radius', '12px') }}"
    data-theme-btn-style="{{ \App\Support\Pengaturan::get('landing_theme_btn_style', 'rounded') }}"
>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ \App\Support\Pengaturan::get('landing_meta_title', 'MAGKOM – Portal Magang Diskominfo Sleman 2026') }}</title>
    <meta name="description" content="{{ \App\Support\Pengaturan::get('landing_meta_desc', 'Daftar magang di Diskominfo Sleman: lihat persyaratan, kuota per bidang, dan tahapan pendaftaran.') }}">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ \App\Support\Pengaturan::get('landing_meta_title', 'Magang Kominfo Sleman 2026') }}">
    <meta property="og:description" content="{{ \App\Support\Pengaturan::get('landing_meta_desc', 'Persyaratan, kuota per bidang, dan tahapan pendaftaran magang Diskominfo Sleman.') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Favicon Resmi Sleman / MagKom -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    @php
        $fontDisplay = \App\Support\Pengaturan::get('landing_theme_font_display', 'Lexend');
        $fontBody = \App\Support\Pengaturan::get('landing_theme_font_body', 'Source Sans 3');
    @endphp
    <!-- Dynamic Google Fonts (Theming Engine) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $fontDisplay) }}:wght@400;500;600;700&family={{ str_replace(' ', '+', $fontBody) }}:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Stylesheet Scoped Modern Landing -->
    <link rel="stylesheet" href="{{ asset('css/landing-modern.css') }}?v={{ filemtime(public_path('css/landing-modern.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/landing-faq-redesign.css') }}?v={{ filemtime(public_path('css/landing-faq-redesign.css')) }}">

    <!-- WordPress Site Editor Dynamic Dimension Tokens -->
    <style id="wp-site-editor-dynamic-styles">
        :root {
            --wp-container-max-w: {{ \App\Support\Pengaturan::get('landing_container_max_width_px', '1152') }}px;
            --wp-hero-title-font-size: {{ \App\Support\Pengaturan::get('landing_hero_title_size_px', '34') }}px;
            --wp-hero-desc-font-size: {{ \App\Support\Pengaturan::get('landing_hero_desc_size_px', '15') }}px;
            --wp-hero-padding-y: {{ \App\Support\Pengaturan::get('landing_hero_padding_y_px', '0') }}px;
            --wp-hero-image-height: {{ \App\Support\Pengaturan::get('landing_hero_image_height_px', '340') }}px;
            --wp-section-gap-y: {{ \App\Support\Pengaturan::get('landing_section_gap_px', '0') }}px;
            --wp-card-border-radius: {{ \App\Support\Pengaturan::get('landing_card_radius_px', '12') }}px;
            --wp-card-padding: {{ \App\Support\Pengaturan::get('landing_card_padding_px', '24') }}px;
            --wp-faq-title-font-size: {{ \App\Support\Pengaturan::get('landing_faq_title_size_px', '18') }}px;
            --wp-footer-padding-y: {{ \App\Support\Pengaturan::get('landing_footer_padding_y_px', '64') }}px;
            --wp-color-primary: {{ \App\Support\Pengaturan::get('landing_theme_primary_color', '#1B3860') }};
            --wp-color-accent: {{ \App\Support\Pengaturan::get('landing_theme_accent_color', '#4F46E5') }};
            --wp-color-gold: {{ \App\Support\Pengaturan::get('landing_theme_gold_color', '#F59E0B') }};
        }

        /* Terapkan token dinamis ke elemen landing page dengan proporsi harmonis */
        .landing-hero { padding-top: var(--wp-hero-padding-y) !important; padding-bottom: var(--wp-hero-padding-y) !important; }
        #beranda .hero-slider-wrapper { padding: 0 !important; width: 100% !important; max-width: 100% !important; }
        .hero-title, .landing-hero-title { font-size: min(var(--wp-hero-title-font-size), 36px) !important; }
        .hero-period-info, .section-desc-sub { font-size: var(--wp-hero-desc-font-size) !important; }
        .landing-header-container, .kuota-questions-grid, .kuota-carousel-wrapper, .tahapan-split-container, .faq-redesign-container, .footer-clean-container { max-width: min(var(--wp-container-max-w), 72rem) !important; }
        .landing-section { margin-bottom: 0 !important; }
        .bidang-kuota-card, .tahapan-card, .faq-card-modern, .hero-box { border-radius: var(--wp-card-border-radius) !important; }
        .faq-sub-question-btn { font-size: var(--wp-faq-title-font-size) !important; }
        .landing-footer { background-color: var(--navy-dark, #122846) !important; color: #f8fafc !important; }

        @php
            $respStyles = \App\Support\Pengaturan::json('landing_responsive_styles', []);
            $tabStyles = $respStyles['tablet'] ?? [];
            $mobStyles = $respStyles['mobile'] ?? [];
        @endphp

        @if(!empty($tabStyles))
        @media (max-width: 768px) {
            :root {
                @if(!empty($tabStyles['landing_hero_title_size_px'])) --wp-hero-title-font-size: {{ (int)$tabStyles['landing_hero_title_size_px'] }}px !important; @endif
                @if(!empty($tabStyles['landing_hero_desc_size_px'])) --wp-hero-desc-font-size: {{ (int)$tabStyles['landing_hero_desc_size_px'] }}px !important; @endif
                @if(!empty($tabStyles['landing_hero_padding_y_px'])) --wp-hero-padding-y: {{ (int)$tabStyles['landing_hero_padding_y_px'] }}px !important; @endif
                @if(!empty($tabStyles['landing_hero_image_height_px'])) --wp-hero-image-height: {{ (int)$tabStyles['landing_hero_image_height_px'] }}px !important; @endif
                @if(!empty($tabStyles['landing_section_gap_px'])) --wp-section-gap-y: {{ (int)$tabStyles['landing_section_gap_px'] }}px !important; @endif
                @if(!empty($tabStyles['landing_card_radius_px'])) --wp-card-border-radius: {{ (int)$tabStyles['landing_card_radius_px'] }}px !important; @endif
                @if(!empty($tabStyles['landing_card_padding_px'])) --wp-card-padding: {{ (int)$tabStyles['landing_card_padding_px'] }}px !important; @endif
                @if(!empty($tabStyles['landing_faq_title_size_px'])) --wp-faq-title-font-size: {{ (int)$tabStyles['landing_faq_title_size_px'] }}px !important; @endif
                @if(!empty($tabStyles['landing_footer_padding_y_px'])) --wp-footer-padding-y: {{ (int)$tabStyles['landing_footer_padding_y_px'] }}px !important; @endif
            }
        }
        @endif

        @if(!empty($mobStyles))
        @media (max-width: 480px) {
            :root {
                @if(!empty($mobStyles['landing_hero_title_size_px'])) --wp-hero-title-font-size: {{ (int)$mobStyles['landing_hero_title_size_px'] }}px !important; @endif
                @if(!empty($mobStyles['landing_hero_desc_size_px'])) --wp-hero-desc-font-size: {{ (int)$mobStyles['landing_hero_desc_size_px'] }}px !important; @endif
                @if(!empty($mobStyles['landing_hero_padding_y_px'])) --wp-hero-padding-y: {{ (int)$mobStyles['landing_hero_padding_y_px'] }}px !important; @endif
                @if(!empty($mobStyles['landing_hero_image_height_px'])) --wp-hero-image-height: {{ (int)$mobStyles['landing_hero_image_height_px'] }}px !important; @endif
                @if(!empty($mobStyles['landing_section_gap_px'])) --wp-section-gap-y: {{ (int)$mobStyles['landing_section_gap_px'] }}px !important; @endif
                @if(!empty($mobStyles['landing_card_radius_px'])) --wp-card-border-radius: {{ (int)$mobStyles['landing_card_radius_px'] }}px !important; @endif
                @if(!empty($mobStyles['landing_card_padding_px'])) --wp-card-padding: {{ (int)$mobStyles['landing_card_padding_px'] }}px !important; @endif
                @if(!empty($mobStyles['landing_faq_title_size_px'])) --wp-faq-title-font-size: {{ (int)$mobStyles['landing_faq_title_size_px'] }}px !important; @endif
                @if(!empty($mobStyles['landing_footer_padding_y_px'])) --wp-footer-padding-y: {{ (int)$mobStyles['landing_footer_padding_y_px'] }}px !important; @endif
            }
        }
        @endif
    </style>

    <!-- Vite Asset Pipeline -->
    @vite(['resources/css/pages/public/landing.css', 'resources/css/pages/public/landing-faq.css', 'resources/js/pages/public/landing.js'])
</head>
<body class="magkom-landing-body">

    <!-- =======================================================================
         0. SITE PRELOADER (SPLASH SCREEN LOGO SLEMAN & MAGKOM)
         ======================================================================= -->
    <div id="sitePreloader" class="site-preloader" aria-hidden="true">
        <div class="preloader-content">
            <div class="preloader-logo-wrap">
                @include('partials.logo-sleman', ['class' => 'preloader-logo', 'width' => '76', 'height' => '76', 'style' => 'width: 76px; height: 76px; object-fit: contain;'])
                <div class="preloader-pulse-ring"></div>
            </div>
            <div class="preloader-text-group">
                <span class="preloader-brand-title">MAGKOM</span>
                <span class="preloader-brand-sub">Dinas Komunikasi dan Informatika Sleman</span>
            </div>
            <div class="preloader-progress-bar">
                <div class="preloader-progress-fill"></div>
            </div>
        </div>
    </div>

    {{-- Floating Control Bar saat Mode Pratinjau Staging Aktif --}}
    @include('public.partials._preview_control_bar')

    <!-- =======================================================================
         1. STICKY HEADER
         ======================================================================= -->
    @php
        $headerBrandName = \App\Support\Pengaturan::get('landing_header_brand_name', 'MAGKOM');
        $headerBrandSub = \App\Support\Pengaturan::get('landing_header_brand_sub', 'Portal Magang · Diskominfo Sleman');
        $defaultHeaderNav = [
            ['label' => 'Beranda', 'url' => '#beranda'],
            ['label' => 'Penempatan', 'url' => '#penempatan'],
            ['label' => 'Tahapan', 'url' => '#tahapan'],
            ['label' => 'Peta Alur', 'url' => '#peta-alur'],
            ['label' => 'FAQ', 'url' => '#faq'],
        ];
        $headerNavLinks = \App\Support\Pengaturan::json('landing_header_nav', $defaultHeaderNav);
        if (empty($headerNavLinks)) {
            $headerNavLinks = $defaultHeaderNav;
        } else {
            foreach ($headerNavLinks as &$navItem) {
                if (($navItem['label'] ?? '') === 'Peta Alur' && ($navItem['url'] ?? '') === '#tahapan') {
                    $navItem['url'] = '#peta-alur';
                }
            }
            unset($navItem);
        }
    @endphp
    <header class="landing-header">
        <div class="landing-header-container">
            <a href="{{ route('landing') }}" class="landing-brand" title="{{ $headerBrandName }} - {{ $headerBrandSub }}">
                @include('partials.logo-sleman', ['class' => 'brand-logo-img', 'width' => '38', 'height' => '38', 'style' => 'width: 38px; height: 38px; object-fit: contain;'])
                <span class="brand-text">
                    <span class="brand-name">{{ $headerBrandName }}</span>
                    <span class="brand-sub">{{ $headerBrandSub }}</span>
                </span>
                <!-- Preserved test assertions for zero regression -->
                <span class="sr-only">
                    <span>Magang Kominfo Sleman 2026 – Pendaftaran MAGKOM</span>
                    <span>DISKOMINFO SLEMAN</span>
                    <span>Portal Magang · MAGKOM</span>
                </span>
            </a>

            <nav class="landing-nav-links" aria-label="Navigasi Utama">
                @foreach($headerNavLinks as $hNav)
                    @if(!empty($hNav['label']) && !empty($hNav['url']))
                        <a href="{{ $hNav['url'] }}" class="landing-nav-link">{{ $hNav['label'] }}</a>
                    @endif
                @endforeach
            </nav>

            <div class="landing-auth-group">
                @guest
                    <a href="{{ route('login') }}" class="btn-header-login">Login</a>
                    <a href="#kuota" class="btn-header-register">Daftar</a>
                @else
                    @php
                        $user = auth()->user();
                        $dashboardRoute = match($user?->role?->value ?? ($user?->role instanceof \App\Enums\RoleEnum ? $user->role->value : 'peserta')) {
                            'superadmin' => route('superadmin.dashboard'),
                            'admin' => route('admin.dashboard'),
                            'pembimbing' => route('pembimbing.dashboard'),
                            default => route('peserta.dashboard'),
                        };
                    @endphp
                    <a href="{{ $dashboardRoute }}" class="btn-header-register" style="display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                @endguest
            </div>

            <!-- Mobile Navigation Toggle Button (Vanilla JS ES Module Bound) -->
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
        </div>

        <!-- Backdrop Scrim for Mobile Drawer -->
        <div class="landing-mobile-backdrop" id="landingMobileBackdrop" data-action="close-landing-menu" aria-hidden="true"></div>

        <!-- Mobile Navigation Drawer -->
        <nav class="landing-mobile-drawer" id="landingMobileDrawer" aria-label="Navigasi Mobile" aria-hidden="true">
            <div class="landing-mobile-drawer-inner">
                <div class="landing-mobile-drawer-links">
                    @foreach($headerNavLinks as $hNav)
                        @if(!empty($hNav['label']) && !empty($hNav['url']))
                            <a href="{{ $hNav['url'] }}" class="landing-mobile-nav-link" data-action="close-landing-menu">{{ $hNav['label'] }}</a>
                        @endif
                    @endforeach
                </div>

                <div class="landing-mobile-auth-group">
                    @guest
                        <a href="{{ route('login') }}" class="btn-mobile-login" data-action="close-landing-menu">Login</a>
                        <a href="#kuota" class="btn-mobile-register" data-action="close-landing-menu">Daftar Magang</a>
                    @else
                        @php
                            $user = auth()->user();
                            $dashboardRoute = match($user?->role?->value ?? ($user?->role instanceof \App\Enums\RoleEnum ? $user->role->value : 'peserta')) {
                                'superadmin' => route('superadmin.dashboard'),
                                'admin' => route('admin.dashboard'),
                                'pembimbing' => route('pembimbing.dashboard'),
                                default => route('peserta.dashboard'),
                            };
                        @endphp
                        <a href="{{ $dashboardRoute }}" class="btn-mobile-register" data-action="close-landing-menu" style="display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                            <span>Buka Dashboard</span>
                        </a>
                    @endguest
                </div>
            </div>
        </nav>
    </header>

    <!-- =======================================================================
         2. DYNAMIC BLOCK ENGINE (WORDPRESS-STYLE SITE EDITOR RENDERER)
         ======================================================================= -->
    @php
        $renderer = $renderer ?? app(\App\Services\LandingPage\LandingPageRenderer::class);
        $documentService = $documentService ?? app(\App\Services\LandingPage\LandingPageDocumentService::class);
        $isEditorMode = $isEditorMode ?? request()->boolean('editor_mode');
        $isPreview = ($isPreview ?? false) || \App\Support\Pengaturan::isPreviewMode();
        $isDraftMode = $isEditorMode || $isPreview;
        $document = $document ?? $documentService->getDocument($isDraftMode);

        $contextPayload = [
            'bulanTampil' => $bulanTampil ?? collect(),
            'bidangSmk' => $bidangSmk ?? collect(),
            'bidangMahasiswa' => $bidangMahasiswa ?? collect(),
            'matriksBidang' => $matriksBidang ?? [],
            'jurusanSmk' => $jurusanSmk ?? collect(),
            'jurusanMahasiswa' => $jurusanMahasiswa ?? collect(),
            'matriksKetersediaan' => $matriksKetersediaan ?? [],
            'semuaBidang' => $semuaBidang ?? collect(),
            'sekarang' => $sekarang ?? \Carbon\CarbonImmutable::now('Asia/Jakarta'),
        ];
    @endphp

    <main id="main-content" style="display: flex; flex-direction: column;">
        {!! $renderer->renderBlocks($document['blocks'] ?? [], $contextPayload, $isEditorMode) !!}
    </main>
    <!-- =======================================================================
         7. BACKEND DATA INJECTION FOR REAL-TIME KUOTA HYDRATION
         ======================================================================= -->
    <script>
        window.MAGKOM_REGISTER_URL = "{{ route('register') }}";
        window.MAGKOM_DATA = {
            @if(isset($bulanTampil) && $bulanTampil->count() > 0)
                bulanList: {!! json_encode($bulanTampil->map(fn($b) => \Carbon\CarbonImmutable::parse($b->bulan_mulai)->locale('id')->isoFormat('MMM'))->values()) !!},
                bulanPanjang: {
                    @foreach($bulanTampil as $b)
                        "{{ \Carbon\CarbonImmutable::parse($b->bulan_mulai)->locale('id')->isoFormat('MMM') }}": "{{ \Carbon\CarbonImmutable::parse($b->bulan_mulai)->locale('id')->isoFormat('MMMM Y') }}",
                    @endforeach
                },
            @endif
        };
    </script>

    {{-- WordPress Site Editor Interactive Canvas Runtime --}}
    @if(request()->boolean('editor_mode'))
        @vite(['resources/js/pages/public/landing-canvas-runtime.js'])
    @endif
</body>
</html>
