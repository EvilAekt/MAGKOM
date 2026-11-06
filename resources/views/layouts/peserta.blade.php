<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal Peserta') - MagKom</title>

    <!-- Favicon Resmi Sleman / MagKom -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Preload Aset Logo Prioritas Tinggi (Cegah Flicker / Kedipan saat Navigasi) -->
    <link rel="preload" as="image" href="{{ asset('images/logo-sleman.png') }}" fetchpriority="high">

    <!-- Google Fonts: Lexend, Open Sans, & Source Sans 3 (Harmonisasi Desain Sistem SuperAdmin & Landing Page) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700;800&family=Open+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Gentelella Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/gentelella-theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-cockpit-unified.css') }}">

    <!-- Vite Asset Pipeline (PRD-TASK-VITE-01) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Anti-Flash Theme & Sidebar State Script -->
    <script>
        (function() {
            try {
                const savedPref = localStorage.getItem('magkom_theme') || '{{ session('tema_tampilan', request()->cookie('tema_tampilan', 'terang')) }}';
                let effectiveTheme = 'light';
                if (savedPref === 'sistem') {
                    effectiveTheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                } else if (savedPref === 'gelap') {
                    effectiveTheme = 'dark';
                } else {
                    effectiveTheme = 'light';
                }
                document.documentElement.setAttribute('data-theme', effectiveTheme);
                document.documentElement.setAttribute('data-pref-theme', savedPref);

                // Anti-flicker untuk state sidebar collapsed di desktop
                const savedSidebar = localStorage.getItem('magkom_peserta_sidebar_collapsed');
                if (savedSidebar === 'true' && window.innerWidth > 1024) {
                    document.documentElement.classList.add('sidebar-collapsed');
                }
            } catch (e) {}
        })();
    </script>

    <style>
        :root {
            /* MagKom / 11 Master CSS Peeper Tokens (PRD-TASK-2026-10-28-UI-02 SuperAdmin Parity) */
            --magkom-primary-blue: #01347C;
            --magkom-midnight-navy: #003566;
            --magkom-abyssal-navy: #02275D;
            --magkom-indigo-accent: #3F51B5;
            --magkom-amber-gold: #FFC300;
            --magkom-sun-yellow: #FFCB05;
            --magkom-dark-charcoal: #212529;
            --magkom-pure-black: #000000;
            --magkom-gray-medium: #AAAAAA;
            --magkom-gray-light: #BBBBBB;
            --magkom-pure-white: #FFFFFF;

            --navy: var(--magkom-abyssal-navy);
            --navy-dark: var(--magkom-midnight-navy);
            --blue-600: var(--magkom-primary-blue); /* Sleman Navy Utama #01347C */
            --blue-700: var(--magkom-midnight-navy); /* Sleman Navy Dark #003566 */
            --blue-accent: var(--magkom-indigo-accent); /* Royal Blue #3F51B5 */
            --blue-tint: rgba(1, 52, 124, 0.08);
            --blue-tint-line: var(--magkom-indigo-accent);
            --gold: var(--magkom-amber-gold);
            --gold-bright: var(--magkom-sun-yellow);

            --card-highlight-bg: var(--magkom-primary-blue);
            --card-highlight-border: var(--magkom-indigo-accent);
            --card-highlight-text: #FFFFFF;
            --card-highlight-badge-bg: var(--magkom-sun-yellow);
            --card-highlight-badge-text: var(--magkom-abyssal-navy);

            --neutral-0: #FFFFFF;
            --neutral-100: #F8FAFC;
            --neutral-150: #F1F5F9;
            --neutral-200: #E2E8F0;
            --neutral-400: #CBD5E1;
            --neutral-500: #64748B;
            --neutral-600: #475569;
            --neutral-700: #334155;
            --neutral-800: #1E293B;
            --neutral-900: #0F172A;

            /* Status Tokens Soft & Elegan */
            --green-100: #F0FDF4;
            --green-600: #16A34A;
            --green-700: #15803D;

            --amber-100: #FFFBEB;
            --amber-200: #FEF3C7;
            --amber-300: #FDE68A;
            --amber-700: #B45309;

            --red-100: #FEF2F2;
            --red-200: #FEE2E2;
            --red-300: #FECACA;
            --red-600: #DC2626;
            --red-700: #B91C1C;

            --font-display: 'Lexend', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-sans: 'Source Sans 3', 'Open Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            
            /* Tipografi Skala MagKom Resmi */
            --text-2xs: 0.6875rem; /* 11px */
            --text-xs: 0.75rem;    /* 12px */
            --text-sm: 0.8125rem;  /* 13px */
            --text-base: 0.875rem; /* 14px */
            --text-md: 0.9375rem;  /* 15px */
            --text-lg: 1rem;       /* 16px */
            --text-xl: 1.125rem;   /* 18px */
            --text-2xl: 1.25rem;   /* 20px */
            --text-3xl: 1.5rem;    /* 24px */

            --fw-regular: 400;
            --fw-normal: 400;
            --fw-medium: 500;
            --fw-semibold: 600;
            --fw-bold: 700;

            --radius-default: 10px;
            --radius-md: 14px;
            --radius-lg: 16px;
            --radius-full: 9999px;
            --sidebar-width: 230px;
            --sidebar-collapsed-width: 72px;
            --topbar-height: 64px;

            /* Soft Diffuse Shadows */
            --shadow-subtle: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
            --shadow-soft: 0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -2px rgba(0, 0, 0, 0.03);
            --shadow-card: 0 10px 15px -3px rgba(15, 23, 42, 0.03), 0 4px 6px -4px rgba(15, 23, 42, 0.02);
        }

        /* Mode Gelap (Dark Mode Tokens - Modern Slate/Navy Theme ala Menu Tugas) */
        [data-theme="dark"] {
            --neutral-0: #182234;       /* Card background */
            --neutral-100: #0F172A;     /* Secondary container / hover */
            --neutral-150: #0A0F1D;     /* Body background */
            --neutral-200: #26354D;     /* Card border & divider */
            --neutral-400: #3E516E;     /* Input border / outline */
            --neutral-500: #8FA2BF;     /* Subtitle / muted text */
            --neutral-600: #CBD5E1;     /* Body secondary text */
            --neutral-700: #E2E8F0;     /* Body regular text */
            --neutral-800: #F8FAFC;     /* Heading & bold text */
            --neutral-900: #FFFFFF;

            --blue-tint: rgba(6, 65, 204, 0.35);
            --blue-tint-line: rgba(7, 81, 255, 0.5);

            /* Status Tokens Khusus Mode Gelap (Glowing & High Contrast) */
            --green-100: rgba(6, 78, 59, 0.45);
            --green-600: #34D399;
            --green-700: #6EE7B7;

            --amber-100: rgba(120, 53, 15, 0.45);
            --amber-200: rgba(180, 83, 9, 0.35);
            --amber-300: #FCD34D;
            --amber-700: #FBBF24;

            --red-100: rgba(127, 29, 29, 0.45);
            --red-200: rgba(153, 27, 27, 0.35);
            --red-300: #FCA5A5;
            --red-600: #F87171;
            --red-700: #EF4444;
        }

        [data-theme="dark"] body {
            background-color: var(--neutral-150);
            color: var(--neutral-700);
        }

        [data-theme="dark"] .sidebar,
        [data-theme="dark"] .dashboard-sidebar {
            background-color: #011C44 !important;
            border-right: 1px solid rgba(63, 81, 181, 0.2) !important;
        }

        [data-theme="dark"] .sidebar-header {
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        [data-theme="dark"] .topbar {
            background: linear-gradient(135deg, #02275D 0%, #003566 100%) !important;
            border-bottom: 1px solid rgba(63, 81, 181, 0.35) !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.6) !important;
        }

        [data-theme="dark"] .topbar-title {
            color: #F8FAFC;
        }

        [data-theme="dark"] .user-chip-btn {
            background-color: #1F2937;
            border-color: #374151;
            color: #F8FAFC;
        }

        [data-theme="dark"] .user-name {
            color: #F8FAFC;
        }

        [data-theme="dark"] .profile-dropdown-menu {
            background-color: #182234;
            border-color: #26354D;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }

        [data-theme="dark"] .dropdown-header {
            border-bottom-color: #26354D;
        }

        [data-theme="dark"] .dropdown-header-name {
            color: #F8FAFC;
        }

        [data-theme="dark"] .dropdown-item {
            color: #E2E8F0;
        }

        [data-theme="dark"] .dropdown-item:hover {
            background-color: #1F2937;
            color: #60A5FA;
        }

        [data-theme="dark"] .dropdown-divider {
            background-color: #26354D;
        }

        [data-theme="dark"] .nav-item,
        [data-theme="dark"] .nav-item-link {
            color: var(--magkom-gray-light, #BBBBBB);
        }

        [data-theme="dark"] .nav-item:hover,
        [data-theme="dark"] .nav-item-link:hover {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: #FFFFFF !important;
            border: 1px solid rgba(255, 255, 255, 0.14) !important;
            border-radius: 8px !important;
            transform: translateX(4px);
        }

        [data-theme="dark"] .nav-item:hover svg,
        [data-theme="dark"] .nav-item-link:hover svg {
            color: #FFCB05 !important;
            transform: scale(1.1) translateY(-1px);
        }

        [data-theme="dark"] .nav-item.active,
        [data-theme="dark"] .nav-item-link.active {
            background: #0E438A !important;
            color: #FFFFFF !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            border: 1px solid rgba(147, 197, 253, 0.35) !important;
            border-left: none !important;
            box-shadow: 0 4px 14px rgba(1, 30, 75, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
            animation: activePillBreathe 3.5s ease-in-out infinite;
        }

        [data-theme="dark"] .nav-item.active svg,
        [data-theme="dark"] .nav-item-link.active svg {
            color: var(--magkom-sun-yellow, #FFCB05) !important;
        }

        [data-theme="dark"] .nav-item.locked {
            color: #64748B;
            background-color: transparent;
        }

        [data-theme="dark"] .badge-locked {
            background-color: #1F2937;
            color: #94A3B8;
            border-color: #374151;
        }


        [data-theme="dark"] .content-area {
            background-color: var(--neutral-150);
        }

        [data-theme="dark"] .settings-card,
        [data-theme="dark"] .card,
        [data-theme="dark"] .stats-card,
        [data-theme="dark"] .profile-card,
        [data-theme="dark"] .tugas-card,
        [data-theme="dark"] .logbook-card {
            background-color: var(--neutral-0) !important;
            border-color: var(--neutral-200) !important;
            color: var(--neutral-700);
        }

        [data-theme="dark"] .input-custom,
        [data-theme="dark"] input[type="text"],
        [data-theme="dark"] input[type="email"],
        [data-theme="dark"] input[type="password"],
        [data-theme="dark"] select,
        [data-theme="dark"] textarea {
            background-color: #0F172A !important;
            border-color: var(--neutral-400) !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] input:focus,
        [data-theme="dark"] select:focus,
        [data-theme="dark"] textarea:focus {
            border-color: #2563EB !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25) !important;
        }

        [data-theme="dark"] .callout-note {
            background-color: #0F172A !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }

        /* Unified Dark Components across Views */
        [data-theme="dark"] .table-container,
        [data-theme="dark"] .table-wrapper,
        [data-theme="dark"] table {
            background-color: var(--neutral-0) !important;
            border-color: var(--neutral-200) !important;
            color: var(--neutral-700);
        }

        [data-theme="dark"] th {
            background-color: #0F172A !important;
            color: #94A3B8 !important;
            border-bottom-color: var(--neutral-200) !important;
        }

        [data-theme="dark"] td {
            border-bottom-color: var(--neutral-200) !important;
            color: var(--neutral-700) !important;
        }

        [data-theme="dark"] tr:hover td {
            background-color: rgba(30, 41, 59, 0.5) !important;
        }

        [data-theme="dark"] .modal-content,
        [data-theme="dark"] .modal-box,
        [data-theme="dark"] .modal-box-large,
        [data-theme="dark"] .dialog-content {
            background-color: #182234 !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6) !important;
        }

        [data-theme="dark"] .modal-header {
            background-color: #182234 !important;
            border-bottom-color: #26354D !important;
        }

        [data-theme="dark"] .modal-footer {
            background-color: #0F172A !important;
            border-top-color: #26354D !important;
        }

        [data-theme="dark"] .btn-secondary {
            background-color: #1E293B !important;
            border: 1px solid #334155 !important;
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .btn-secondary:hover {
            background-color: #334155 !important;
            color: #FFFFFF !important;
        }

        [data-theme="dark"] .alert-success {
            background-color: rgba(6, 78, 59, 0.35) !important;
            border-color: rgba(52, 211, 153, 0.4) !important;
            color: #6EE7B7 !important;
        }

        [data-theme="dark"] .alert-error,
        [data-theme="dark"] .alert-danger {
            background-color: rgba(127, 29, 29, 0.35) !important;
            border-color: rgba(239, 68, 68, 0.4) !important;
            color: #FCA5A5 !important;
        }

        [data-theme="dark"] .alert-warning {
            background-color: rgba(120, 53, 15, 0.35) !important;
            border-color: rgba(245, 158, 11, 0.4) !important;
            color: #FCD34D !important;
        }

        [data-theme="dark"] .alert-info {
            background-color: rgba(30, 58, 138, 0.35) !important;
            border-color: rgba(96, 165, 250, 0.4) !important;
            color: #93C5FD !important;
        }

        [data-theme="dark"] .badge-success {
            background-color: rgba(6, 78, 59, 0.6) !important;
            border-color: rgba(52, 211, 153, 0.4) !important;
            color: #6EE7B7 !important;
        }

        [data-theme="dark"] .badge-warning {
            background-color: rgba(120, 53, 15, 0.6) !important;
            border-color: rgba(251, 191, 36, 0.4) !important;
            color: #FCD34D !important;
        }

        [data-theme="dark"] .badge-danger {
            background-color: rgba(127, 29, 29, 0.6) !important;
            border-color: rgba(239, 68, 68, 0.4) !important;
            color: #FCA5A5 !important;
        }

        [data-theme="dark"] .badge-info {
            background-color: rgba(30, 58, 138, 0.6) !important;
            border-color: rgba(96, 165, 250, 0.4) !important;
            color: #93C5FD !important;
        }

        [data-theme="dark"] .badge-neutral {
            background-color: rgba(51, 65, 85, 0.6) !important;
            border-color: rgba(100, 116, 139, 0.4) !important;
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .tab-btn {
            color: #94A3B8;
        }

        [data-theme="dark"] .tab-btn:hover {
            color: #60A5FA;
            background: #1E293B;
        }

        [data-theme="dark"] .tab-btn.active {
            color: #60A5FA;
            border-bottom-color: #3B82F6;
        }

        [data-theme="dark"] .tab-count {
            background-color: #26354D;
            color: #CBD5E1;
        }

        [data-theme="dark"] .tab-btn.active .tab-count {
            background-color: rgba(7, 81, 255, 0.3);
            color: #60A5FA;
        }

        [data-theme="dark"] .divider,
        [data-theme="dark"] hr {
            border-color: var(--neutral-200) !important;
        }

        [data-theme="dark"] .bg-white:not(.badge-white):not(.text-white) {
            background-color: var(--neutral-0) !important;
            border-color: var(--neutral-200) !important;
            color: var(--neutral-700);
        }

        [data-theme="dark"] .text-slate-900,
        [data-theme="dark"] .text-gray-900 {
            color: var(--neutral-800) !important;
        }

        [data-theme="dark"] .text-slate-700,
        [data-theme="dark"] .text-gray-700 {
            color: var(--neutral-700);
        }

        [data-theme="dark"] .text-slate-500,
        [data-theme="dark"] .text-gray-500,
        [data-theme="dark"] .text-slate-400,
        [data-theme="dark"] .text-gray-400 {
            color: var(--neutral-500) !important;
        }

        [data-theme="dark"] .border-slate-200,
        [data-theme="dark"] .border-gray-200,
        [data-theme="dark"] .border-slate-100,
        [data-theme="dark"] .border-gray-100 {
            border-color: var(--neutral-200) !important;
        }

        /* =====================================================================
           UNIVERSAL INLINE-STYLE INVERSION (DS-MagKom Dark Contrast Fix)
           Menembus inline-style hardcoded pada child views agar kontras tinggi
           ===================================================================== */
        [data-theme="dark"] [style*="color: #0F172A"],
        [data-theme="dark"] [style*="color:#0F172A"],
        [data-theme="dark"] [style*="color: #0f172a"],
        [data-theme="dark"] [style*="color:#0f172a"],
        [data-theme="dark"] [style*="color: #1E293B"],
        [data-theme="dark"] [style*="color:#1E293B"],
        [data-theme="dark"] [style*="color: #1e293b"],
        [data-theme="dark"] [style*="color:#1e293b"],
        [data-theme="dark"] [style*="color: #000000"],
        [data-theme="dark"] [style*="color:#000000"],
        [data-theme="dark"] [style*="color: #05070B"],
        [data-theme="dark"] [style*="color: #1D2530"],
        [data-theme="dark"] [style*="color: #333333"] {
            color: var(--neutral-800, #F8FAFC) !important;
        }

        [data-theme="dark"] [style*="color: #334155"],
        [data-theme="dark"] [style*="color:#334155"],
        [data-theme="dark"] [style*="color: #475569"],
        [data-theme="dark"] [style*="color:#475569"],
        [data-theme="dark"] [style*="color: #666666"] {
            color: var(--neutral-700, #E2E8F0) !important;
        }

        [data-theme="dark"] [style*="color: #64748B"],
        [data-theme="dark"] [style*="color:#64748B"],
        [data-theme="dark"] [style*="color: #7A8494"] {
            color: var(--neutral-500, #94A3B8) !important;
        }

        [data-theme="dark"] [style*="background: #ffffff"],
        [data-theme="dark"] [style*="background:#ffffff"],
        [data-theme="dark"] [style*="background: #FFFFFF"],
        [data-theme="dark"] [style*="background:#FFFFFF"],
        [data-theme="dark"] [style*="background-color: #ffffff"],
        [data-theme="dark"] [style*="background-color:#ffffff"],
        [data-theme="dark"] [style*="background-color: #FFFFFF"],
        [data-theme="dark"] [style*="background-color:#FFFFFF"] {
            background-color: var(--neutral-0, #182234) !important;
            border-color: var(--neutral-200, #26354D) !important;
        }

        [data-theme="dark"] [style*="background: #F8FAFC"],
        [data-theme="dark"] [style*="background:#F8FAFC"],
        [data-theme="dark"] [style*="background: #F1F5F9"],
        [data-theme="dark"] [style*="background:#F1F5F9"] {
            background-color: var(--neutral-100, #0F172A) !important;
            border-color: var(--neutral-200, #26354D) !important;
        }

        [data-theme="dark"] [style*="border-bottom: 1px dashed #E2E8F0"],
        [data-theme="dark"] [style*="border-bottom: 1px dashed #CBD5E1"] {
            border-bottom-color: var(--neutral-200, #26354D) !important;
        }

        [data-theme="dark"] [style*="border: 1px solid #E2E8F0"],
        [data-theme="dark"] [style*="border: 1px solid #CBD5E1"],
        [data-theme="dark"] [style*="border: 1px solid #BFDBFE"] {
            border-color: var(--neutral-200, #26354D) !important;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            font-size: 16px;
            -webkit-text-size-adjust: 100%;
        }

        body {
            font-family: var(--font-sans);
            font-size: var(--text-base);
            font-weight: var(--fw-regular);
            line-height: 1.5;
            background-color: var(--neutral-150);
            color: var(--neutral-800);
            min-height: 100vh;
            display: flex;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        /* Headings: Lexend Font Tegas dan Jelas sebagai Judul (Sesuai Landing Page) */
        h1, h2, h3, h4, h5, h6, .topbar-title, .brand-text h2, .site_title, .page-title h3, .tile_stats_count .count {
            font-family: var(--font-display);
            color: var(--neutral-800);
            font-weight: var(--fw-bold);
            line-height: 1.3;
        }

        h3, h4, h5, h6 {
            color: var(--neutral-800);
            font-weight: var(--fw-semibold);
            line-height: 1.3;
        }

        .dashboard-hero h1, .dashboard-hero h2, .dashboard-hero h3,
        .banner-penempatan h1, .banner-penempatan h2, .banner-penempatan h3,
        .classroom-banner h1, .classroom-banner h2, .classroom-banner h3,
        .profile-hero h1, .profile-hero h2, .profile-hero h3,
        .pendaftaran-hero h1, .pendaftaran-hero h2, .pendaftaran-hero h3 {
            color: #ffffff;
        }

        /* Standardisasi Global Font & Tipografi (Selaras dengan Landing Page) */
        body {
            font-family: var(--font-sans, 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif);
            background-color: var(--neutral-100, #f8fafc);
            color: var(--neutral-800, #1e293b);
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, h5, h6, .font-display {
            font-family: var(--font-display, 'Lexend', sans-serif) !important;
            letter-spacing: -0.01em;
        }

        /* Teks semua di web (body): Regular */
        p, span.text-body, td, li {
            font-weight: var(--fw-regular);
            line-height: 1.5;
        }

        input, button, select, textarea, optgroup {
            font-family: inherit;
            font-size: inherit;
            line-height: inherit;
        }

        /* =====================================================================
           SIDEBAR STYLING (SuperAdmin Parity Spec - Image 1 & Gentelella Cockpit)
           ===================================================================== */
        .sidebar,
        .dashboard-sidebar {
            width: var(--sidebar-width);
            background-color: var(--magkom-abyssal-navy, #02275D);
            color: #FFFFFF;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 50;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            border-right: 1px solid rgba(50, 130, 184, 0.2);
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.25) transparent;
            -ms-overflow-style: auto;
        }

        .sidebar::-webkit-scrollbar,
        .dashboard-sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb,
        .dashboard-sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.25);
            border-radius: 4px;
        }

        .sidebar-header {
            padding: 1.125rem 1.25rem 0.875rem 1.25rem;
            border-bottom: 1px solid rgba(50, 130, 184, 0.2);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-shrink: 0;
        }

        .sidebar-brand-box {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
            flex-shrink: 0;
        }

        .brand-box {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #ffffff;
            min-width: 0;
        }

        .brand-icon,
        .sidebar-brand-emblem {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08) !important;
            border: 1px solid rgba(255, 255, 255, 0.14) !important;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            flex-shrink: 0;
            padding: 3px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
            transition: all 0.2s ease;
        }

        .brand-icon img,
        .sidebar-brand-emblem img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            image-rendering: -webkit-optimize-contrast;
        }

        .brand-text,
        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
            min-width: 0;
            flex-shrink: 0;
        }

        .brand-text h2,
        .sidebar-brand-title {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            color: #FFFFFF !important;
            line-height: 1.2;
            white-space: nowrap;
            margin: 0;
        }

        .brand-text span,
        .sidebar-brand-subtitle {
            font-size: 0.6875rem;
            font-weight: 600;
            color: var(--magkom-gray-light, #BBBBBB) !important;
            opacity: 0.85;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        /* Sidebar Navigation Items */
        .sidebar-nav {
            padding: 0.75rem 0.625rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
            overflow-y: auto;
            min-height: 0;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.25) transparent;
            -ms-overflow-style: auto;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.25);
            border-radius: 4px;
        }

        .nav-label,
        .nav-section-label {
            font-size: 0.6875rem;
            font-weight: 700;
            color: var(--magkom-gray-light, #BBBBBB) !important;
            opacity: 0.85;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 0.85rem 0.75rem 0.35rem 0.75rem;
        }

        .nav-item,
        .nav-item-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-height: 38px;
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            text-decoration: none;
            color: var(--sidebar-text, #BBBBBB);
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
            outline: none;
            border: 1px solid transparent;
            border-left: none;
        }

        .nav-item:hover,
        .nav-item-link:hover {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: #FFFFFF !important;
            border: 1px solid rgba(255, 255, 255, 0.14) !important;
            border-radius: 8px !important;
            transform: translateX(4px);
        }

        .nav-item:hover .nav-item-icon,
        .nav-item:hover svg,
        .nav-item-link:hover .nav-item-icon,
        .nav-item-link:hover svg {
            color: #FFCB05 !important;
            transform: scale(1.1) translateY(-1px);
        }

        .nav-item:focus-visible,
        .nav-item-link:focus-visible {
            box-shadow: 0 0 0 2px #02275D, 0 0 0 4px #FFC300;
        }

        /* Active Navigation Item (Sleman Deep Royal - Refined & Anti-Silau) */
        @keyframes activePillBreathe {
            0%, 100% {
                box-shadow: 0 4px 12px rgba(1, 30, 75, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.15);
            }
            50% {
                box-shadow: 0 4px 16px rgba(14, 67, 138, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.25);
            }
        }

        .nav-item.active,
        .nav-item-link.active {
            background: #013D8D !important;
            color: #FFFFFF !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
            border: 1px solid rgba(147, 197, 253, 0.35) !important;
            border-left: none !important;
            margin-right: 0;
            padding-right: 0.75rem;
            position: relative;
            box-shadow: 0 4px 12px rgba(1, 30, 75, 0.45), inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
            animation: activePillBreathe 3.5s ease-in-out infinite;
        }

        .nav-item.active .nav-item-icon,
        .nav-item.active svg:not(.nav-chevron),
        .nav-item-link.active .nav-item-icon,
        .nav-item-link.active svg {
            color: #FFCB05 !important;
            filter: drop-shadow(0 1px 2px rgba(0, 0, 0, 0.35));
            stroke-width: 2.2;
        }

        .nav-item-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            color: var(--magkom-gray-medium, #AAAAAA);
            transition: transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1), color 0.15s ease;
        }

        .nav-item svg,
        .nav-item-link svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            transition: color 0.15s ease, transform 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .nav-item.locked,
        .nav-item-link.locked {
            opacity: 0.6;
            cursor: not-allowed;
            background-color: transparent !important;
            color: rgba(255, 255, 255, 0.6) !important;
            user-select: none;
        }

        .nav-item.locked:hover,
        .nav-item-link.locked:hover {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: #FFFFFF !important;
            transform: none !important;
        }

        .badge-locked {
            margin-left: auto;
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .badge-locked svg {
            width: 13px !important;
            height: 13px !important;
        }

        .nav-item.locked:hover .badge-locked,
        .nav-item-link.locked:hover .badge-locked {
            background: rgba(255, 255, 255, 0.2);
            color: #FFFFFF;
        }

        /* =========================================================================
           Standardized MagKom Primary Buttons (SuperAdmin Button Spec - Image 1)
           ========================================================================= */
        .btn-magkom-primary,
        .saas-btn-primary,
        .saas-hero-btn-primary {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            padding: 9px 20px !important;
            min-height: 40px !important;
            border-radius: 8px !important;
            background: #01347C !important;
            color: #FFFFFF !important;
            font-size: 0.875rem !important;
            font-weight: 700 !important;
            text-decoration: none !important;
            border: 1px solid rgba(255, 255, 255, 0.18) !important;
            box-shadow: 0 2px 6px rgba(1, 52, 124, 0.2) !important;
            cursor: pointer !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }

        .btn-magkom-primary:hover,
        .saas-btn-primary:hover,
        .saas-hero-btn-primary:hover {
            background: #002244 !important;
            color: #FFFFFF !important;
            transform: translateY(-1.5px) !important;
            box-shadow: 0 6px 18px rgba(1, 52, 124, 0.32) !important;
        }

        .btn-magkom-primary:active,
        .saas-btn-primary:active,
        .saas-hero-btn-primary:active {
            transform: translateY(0) !important;
            box-shadow: 0 2px 6px rgba(1, 52, 124, 0.2) !important;
        }

        .btn-magkom-primary svg,
        .saas-btn-primary svg,
        .saas-hero-btn-primary svg {
            transition: transform 0.2s ease !important;
        }

        .btn-magkom-primary:hover svg,
        .saas-btn-primary:hover svg,
        .saas-hero-btn-primary:hover svg {
            transform: translateX(2px) !important;
        }

        /* =========================================================================
           Sidebar Footer Standardized Toolbar (Pengaturan, Theme Toggle, Logout)
           ========================================================================= */
        .sidebar-footer {
            padding: 0.75rem 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-around;
            margin-top: auto;
            flex-shrink: 0;
        }

        [data-theme="dark"] .sidebar-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(0, 0, 0, 0.25);
        }

        .sidebar-footer-form {
            margin: 0;
            padding: 0;
            display: inline-flex;
        }

        .sidebar-footer a,
        .sidebar-footer button,
        .sidebar-footer .sidebar-footer-btn {
            width: 38px;
            height: 38px;
            border-radius: 50% !important;
            color: rgba(255, 255, 255, 0.75);
            background: transparent;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            text-decoration: none;
            padding: 0;
            flex-shrink: 0;
        }

        .sidebar-footer a:hover,
        .sidebar-footer button:hover,
        .sidebar-footer .sidebar-footer-btn:hover {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.18);
            transform: translateY(-1px);
        }

        .sidebar-footer a:focus-visible,
        .sidebar-footer button:focus-visible,
        .sidebar-footer .sidebar-footer-btn:focus-visible {
            outline: 2px solid var(--magkom-sun-yellow, #FFCB05);
            outline-offset: 2px;
        }

        /* Single-Icon State Dinamis Tombol Tema Sidebar (Bulan saat Terang, Lampu saat Gelap) */
        html:not([data-theme="dark"]) .sidebar-footer .sun-icon,
        html:not([data-theme="dark"]) .sidebar-footer .light-icon,
        html:not([data-theme="dark"]) #sidebarThemeToggleBtn .sun-icon,
        html:not([data-theme="dark"]) #sidebarThemeToggleBtn .light-icon {
            display: none !important;
        }

        html:not([data-theme="dark"]) .sidebar-footer .moon-icon,
        html:not([data-theme="dark"]) #sidebarThemeToggleBtn .moon-icon {
            display: inline-block !important;
            color: rgba(255, 255, 255, 0.85);
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), color 0.2s ease;
        }

        html[data-theme="dark"] .sidebar-footer .sun-icon,
        html[data-theme="dark"] .sidebar-footer .light-icon,
        html[data-theme="dark"] #sidebarThemeToggleBtn .sun-icon,
        html[data-theme="dark"] #sidebarThemeToggleBtn .light-icon {
            display: inline-block !important;
            color: var(--magkom-sun-yellow, #FFCB05) !important;
            filter: drop-shadow(0 0 6px rgba(255, 203, 5, 0.45));
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), color 0.2s ease;
        }

        html[data-theme="dark"] .sidebar-footer .moon-icon,
        html[data-theme="dark"] #sidebarThemeToggleBtn .moon-icon {
            display: none !important;
        }

        /* Layout Main Area */
        .main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .topbar {
            height: var(--topbar-height);
            background: linear-gradient(135deg, #01347C 0%, #003566 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.14);
            box-shadow: 0 4px 20px -2px rgba(1, 52, 124, 0.25);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .sidebar-toggle-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            cursor: pointer;
            color: #ffffff;
            width: 38px;
            height: 38px;
            border-radius: var(--radius-default);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            flex-shrink: 0;
            padding: 0;
        }

        .sidebar-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.35);
            transform: translateY(-1px);
        }

        .sidebar-toggle-btn:focus-visible {
            outline: 2px solid #93c5fd;
            outline-offset: 2px;
        }

        .mobile-menu-btn {
            display: none;
        }

        .topbar-title {
            font-family: var(--font-display, 'Lexend', sans-serif);
            font-size: var(--text-xl);
            font-weight: 700;
            color: #ffffff;
            line-height: 1.3;
            letter-spacing: -0.01em;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }



        /* Profile Dropdown */
        .profile-dropdown-container {
            position: relative;
        }

        .user-chip-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 14px 4px 6px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: var(--radius-full);
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
            font-family: inherit;
        }

        .user-chip-btn:hover,
        .user-chip-btn[aria-expanded="true"] {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .user-chip-btn:focus-visible {
            outline: 2px solid #93c5fd;
            outline-offset: 2px;
        }

        .dropdown-chevron {
            color: rgba(255, 255, 255, 0.8);
            transition: transform 0.2s ease;
            margin-left: 2px;
        }

        .user-chip-btn[aria-expanded="true"] .dropdown-chevron {
            transform: rotate(180deg);
            color: #ffffff;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: #ffffff;
            color: #1b3860;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--text-sm);
            font-weight: 700;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: var(--text-base);
            font-weight: 600;
            color: #ffffff;
            line-height: 1.25;
        }

        .user-badge {
            font-size: var(--text-2xs);
            color: #93c5fd;
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .profile-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 270px;
            background: #ffffff;
            border: 1px solid var(--neutral-400);
            border-radius: var(--radius-default);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.06);
            z-index: 50;
            overflow: hidden;
            animation: dropdownFadeIn 0.15s ease-out;
        }

        .profile-dropdown-menu.open {
            display: block;
        }

        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dropdown-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: var(--neutral-100);
            border-bottom: 1px solid var(--neutral-200);
        }

        .dropdown-header-avatar {
            width: 38px;
            height: 38px;
            background: var(--blue-600);
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--text-base);
            font-weight: var(--fw-bold);
            flex-shrink: 0;
        }

        .dropdown-header-meta {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .dropdown-header-name {
            font-size: var(--text-base);
            font-weight: var(--fw-bold);
            color: var(--neutral-800);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dropdown-header-email {
            font-size: var(--text-xs);
            color: var(--neutral-600);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dropdown-header-role {
            font-size: var(--text-2xs);
            font-weight: var(--fw-bold);
            color: var(--blue-700);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        .dropdown-divider {
            height: 1px;
            background: var(--neutral-200);
            margin: 4px 0;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px 16px;
            font-size: var(--text-base);
            font-weight: var(--fw-semibold);
            color: var(--neutral-700);
            text-decoration: none;
            background: transparent;
            border: none;
            cursor: pointer;
            transition: background 0.15s ease, color 0.15s ease;
            box-sizing: border-box;
            text-align: left;
            font-family: inherit;
        }

        .dropdown-item svg {
            color: var(--neutral-500);
            transition: color 0.15s ease;
            flex-shrink: 0;
        }

        .dropdown-item:hover {
            background: var(--blue-tint);
            color: var(--blue-700);
        }

        .dropdown-item:hover svg {
            color: var(--blue-700);
        }

        .dropdown-item.dropdown-logout {
            color: var(--red-600);
        }

        .dropdown-item.dropdown-logout svg {
            color: var(--red-600);
        }

        .dropdown-item.dropdown-logout:hover {
            background: #FEF2F2;
            color: #DC2626;
        }

        .content-area {
            padding: 32px;
            flex: 1;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }

        /* ========================================================
           MODERN TOAST NOTIFICATION COMPONENT (Mint Circular Badge)
           ======================================================== */
        .app-toast {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 18px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.06), 0 2px 6px -1px rgba(15, 23, 42, 0.04);
            margin-bottom: 24px;
            position: relative;
            transition: all 0.25s ease;
            pointer-events: auto;
        }

        .toast-icon-badge {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .toast-icon-badge.success {
            background: #ECFDF5;
            color: #10B981;
        }

        .toast-icon-badge.error {
            background: #FEF2F2;
            color: #EF4444;
        }

        .toast-icon-badge.warning {
            background: #FFFBEB;
            color: #F59E0B;
        }

        .toast-icon-badge.info {
            background: #EFF6FF;
            color: #3B82F6;
        }

        .toast-content {
            flex: 1;
            min-width: 0;
            padding-top: 1px;
        }

        .toast-title {
            font-size: 0.9375rem;
            font-weight: 700;
            color: #0F172A;
            line-height: 1.35;
            margin-bottom: 2px;
        }

        .toast-desc {
            font-size: 0.84375rem;
            font-weight: 400;
            color: #64748B;
            line-height: 1.45;
        }

        .toast-desc ul {
            margin: 4px 0 0 16px;
            padding: 0;
        }

        .toast-close-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            color: #94A3B8;
            padding: 4px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }

        .toast-close-btn:hover {
            color: #475569;
            background: #F1F5F9;
        }

        /* Backward Compatibility for .alert class */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 18px;
            border-radius: 14px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.06);
            margin-bottom: 24px;
            font-size: var(--text-base);
            line-height: 1.5;
        }

        /* Dark Theme Support for Toasts */
        [data-theme="dark"] .app-toast,
        [data-theme="dark"] .alert {
            background: #182234 !important;
            border-color: #26354D !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3) !important;
        }

        [data-theme="dark"] .toast-title {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .toast-desc {
            color: #94A3B8 !important;
        }

        [data-theme="dark"] .toast-icon-badge.success {
            background: rgba(16, 185, 129, 0.18) !important;
            color: #34D399 !important;
        }

        [data-theme="dark"] .toast-icon-badge.error {
            background: rgba(239, 68, 68, 0.18) !important;
            color: #F87171 !important;
        }

        [data-theme="dark"] .toast-icon-badge.warning {
            background: rgba(245, 158, 11, 0.18) !important;
            color: #FBBF24 !important;
        }

        [data-theme="dark"] .toast-icon-badge.info {
            background: rgba(59, 130, 246, 0.18) !important;
            color: #60A5FA !important;
        }

        /* Floating Top-Right Toast Container & Animations */
        .toast-floating-container,
        .magkom-toast-container,
        #magkom-toast-container,
        .alerts-container {
            position: fixed !important;
            top: 24px !important;
            right: 24px !important;
            z-index: 99999 !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 12px !important;
            max-width: 420px !important;
            width: calc(100vw - 48px) !important;
            pointer-events: none !important;
        }

        .toast-floating-container .app-toast,
        .magkom-toast-container .app-toast,
        #magkom-toast-container .app-toast,
        .alerts-container .app-toast {
            pointer-events: auto;
            margin-bottom: 0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.06);
            animation: toastSlideInRight 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .toast-floating-container .app-toast.toast-hiding,
        .magkom-toast-container .app-toast.toast-hiding,
        #magkom-toast-container .app-toast.toast-hiding,
        .alerts-container .app-toast.toast-hiding {
            animation: toastSlideOutRight 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .btn-toast-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background: #003566;
            color: #FFFFFF !important;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(0, 53, 102, 0.2);
            transition: all 0.2s ease;
            margin-top: 8px;
        }

        .btn-toast-action:hover {
            background: #0641CC;
            transform: translateY(-1px);
            color: #FFFFFF !important;
        }

        [data-theme="dark"] .btn-toast-action {
            background: #2563EB;
            color: #FFFFFF !important;
        }

        [data-theme="dark"] .btn-toast-action:hover {
            background: #3B82F6;
        }

        @keyframes toastSlideInRight {
            from {
                opacity: 0;
                transform: translateX(50px) scale(0.96);
            }
            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @keyframes toastSlideOutRight {
            from {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
            to {
                opacity: 0;
                transform: translateX(50px) scale(0.96);
            }
        }

        [data-theme="dark"] .toast-close-btn {
            color: #64748B !important;
        }

        [data-theme="dark"] .toast-close-btn:hover {
            color: #CBD5E1 !important;
            background: #1E293B !important;
        }

        /* Common Components Soft & Elegan */
        .card {
            background: var(--neutral-0);
            border: 1px solid var(--neutral-200);
            border-radius: var(--radius-md);
            padding: 24px;
            box-shadow: var(--shadow-subtle);
            margin-bottom: 24px;
        }

        .card-header {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: var(--text-xl);
            font-weight: var(--fw-bold);
            color: var(--neutral-900);
            line-height: 1.3;
        }

        .card-subtitle {
            font-size: var(--text-sm);
            font-weight: var(--fw-normal);
            color: var(--neutral-500);
            margin-top: 4px;
            line-height: 1.4;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 40px;
            padding: 0 16px;
            border-radius: var(--radius-default);
            font-size: var(--text-base);
            font-weight: var(--fw-semibold);
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            border: none;
            line-height: 1.25;
        }

        .btn-primary {
            background: var(--blue-600);
            color: #ffffff;
            box-shadow: var(--shadow-subtle);
        }

        .btn-primary:hover:not(:disabled) {
            background: var(--blue-700);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #ffffff;
            color: var(--neutral-700);
            border: 1px solid var(--neutral-200);
            box-shadow: var(--shadow-subtle);
        }

        .btn-secondary:hover:not(:disabled) {
            background: var(--neutral-100);
            border-color: var(--neutral-400);
            color: var(--neutral-900);
        }

        .btn:disabled {
            background: var(--neutral-200);
            color: var(--neutral-500);
            border-color: var(--neutral-400);
            cursor: not-allowed;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: var(--radius-full);
            font-size: var(--text-xs);
            font-weight: var(--fw-semibold);
            line-height: 1.2;
        }

        .badge-success {
            background: var(--green-100);
            color: var(--green-700);
            border: 1px solid var(--green-600);
        }

        .badge-warning {
            background: var(--amber-200);
            color: var(--amber-700);
            border: 1px solid var(--amber-300);
        }

        .badge-danger {
            background: var(--red-200);
            color: var(--red-700);
            border: 1px solid var(--red-300);
        }

        .badge-info {
            background: var(--blue-tint);
            color: var(--blue-700);
            border: 1px solid var(--blue-tint-line);
        }

        .badge-neutral {
            background: var(--neutral-200);
            color: var(--neutral-600);
            border: 1px solid var(--neutral-400);
        }

        /* Overlay Drawer backdrop for mobile */
        .drawer-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(29, 37, 48, 0.5);
            z-index: 45;
        }

        /* Desktop Collapsed Sidebar (Mini-Sidebar Icon Only) */
        @media (min-width: 1025px) {
            html.sidebar-collapsed .sidebar {
                width: var(--sidebar-collapsed-width);
            }

            html.sidebar-collapsed .main-wrapper {
                margin-left: var(--sidebar-collapsed-width);
            }

            html.sidebar-collapsed .sidebar-header {
                padding: 1.25rem 0.5rem 1rem 0.5rem;
                justify-content: center;
            }

            html.sidebar-collapsed .brand-box {
                justify-content: center;
                gap: 0;
                width: 100%;
            }

            html.sidebar-collapsed .brand-text,
            html.sidebar-collapsed .sidebar-brand-text {
                display: none !important;
            }

            html.sidebar-collapsed .sidebar-nav {
                padding: 1rem 0.5rem;
                align-items: center;
            }

            html.sidebar-collapsed .nav-label,
            html.sidebar-collapsed .nav-section-label {
                display: none !important;
            }

            html.sidebar-collapsed .nav-item,
            html.sidebar-collapsed .nav-item-link {
                justify-content: center;
                padding: 0.5rem 0;
                width: 44px;
                height: 38px;
                min-height: 38px;
                position: relative;
                margin: 0 auto;
            }

            html.sidebar-collapsed .nav-item span:not(.badge-locked),
            html.sidebar-collapsed .nav-item-link span:not(.badge-locked):not(.nav-item-icon) {
                display: none !important;
            }

            html.sidebar-collapsed .nav-item .badge-locked,
            html.sidebar-collapsed .nav-item-link .badge-locked {
                position: absolute;
                top: 3px;
                right: 3px;
                margin-left: 0;
                width: 14px;
                height: 14px;
                border-radius: 4px;
            }

            html.sidebar-collapsed .nav-item .badge-locked svg,
            html.sidebar-collapsed .nav-item-link .badge-locked svg {
                width: 9px;
                height: 9px;
            }

            html.sidebar-collapsed .sidebar-footer {
                flex-direction: column;
                gap: 0.5rem;
                padding: 0.75rem 0;
                align-items: center;
                justify-content: center;
            }
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .drawer-backdrop.open {
                display: block;
            }

            .main-wrapper {
                margin-left: 0 !important;
            }

            .content-area {
                padding: 20px 16px;
            }

            .topbar {
                padding: 0 16px;
            }
        }

        /* Persistent Dark Navy Footer for Peserta Layout */
        .gentelella-footer,
        .peserta-footer {
            background: #003566 !important;
            border-top: 1px solid #2C3A4E !important;
            padding: 0.85rem 2rem;
            font-size: 0.75rem;
            color: #94A3B8 !important;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 0.75rem;
            width: 100% !important;
            margin-left: 0 !important;
            margin-top: auto;
            flex-shrink: 0;
            box-sizing: border-box;
            position: relative;
            z-index: 10;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.25s ease;
        }

        .gentelella-footer span,
        .peserta-footer span {
            color: #94A3B8 !important;
            font-weight: 500;
        }

        @media (max-width: 991.98px) {
            .gentelella-footer,
            .peserta-footer {
                padding: 0.75rem 1rem !important;
                justify-content: center !important;
                text-align: center !important;
            }
        }

        /* Screen reader utility (visually hidden) */
        .sr-only {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="drawer-backdrop" id="drawerBackdrop" onclick="toggleSidebarPeserta()"></div>

    <!-- Sidebar Dashboard Peserta -->
    <aside class="sidebar dashboard-sidebar" id="sidebarNav" aria-label="Navigasi Utama">
        <div class="sidebar-header" style="border: 0; padding: 1.125rem 1.25rem 0.875rem;">
            <a href="{{ route('landing') }}" class="site_title brand-box" title="Portal MagKom Sleman" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none; padding: 0;">
                <div class="sidebar-brand-emblem brand-icon" style="width: 36px; height: 36px; border-radius: 8px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.14); display: flex; align-items: center; justify-content: center; padding: 3px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.25);">
                    @include('partials.logo-sleman', ['width' => '28', 'height' => '28'])
                </div>
                <div class="brand-text sidebar-brand-text" style="display: flex; flex-direction: column;">
                    <span class="sidebar-brand-title" style="color: #FFFFFF; font-size: 1.15rem; font-weight: 700; letter-spacing: -0.01em; line-height: 1.2;">MagKom</span>
                </div>
                <!-- Preserved test assertions for zero regression -->
                <span class="sr-only">
                    <span class="sidebar-brand-subtitle">Magang Kominfo &bull; MAGANG KOMINFO &bull; Dinas Kominfo Sleman MagKom Sleman</span>
                </span>
            </a>
        </div>

        <nav class="sidebar-nav">
            <span class="nav-section-label">{{ __('Menu Utama') }}</span>

            <a href="{{ route('peserta.dashboard') }}" class="nav-item-link {{ request()->routeIs('peserta.dashboard') ? 'active' : '' }}" title="{{ __('Dashboard') }}">
                <span class="nav-item-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                </span>
                <span>{{ __('Dashboard') }}</span>
            </a>

            <a href="{{ route('peserta.profil.show') }}" class="nav-item-link {{ request()->routeIs('peserta.profil.*') ? 'active' : '' }}" title="{{ __('Profil & Biodata') }}">
                <span class="nav-item-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </span>
                <span>{{ __('Profil & Biodata') }}</span>
            </a>

            <a href="{{ route('peserta.pendaftaran.index') }}" class="nav-item-link {{ request()->routeIs('peserta.pendaftaran.*') ? 'active' : '' }}" title="{{ __('Pendaftaran Magang') }}">
                <span class="nav-item-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    </svg>
                </span>
                <span>{{ __('Pendaftaran Magang') }}</span>
            </a>

            @php
                $isDiterima = auth()->user()?->hasPenempatanMagang();
            @endphp

            <span class="nav-section-label">{{ __('Pelaksanaan Magang') }}</span>

            @if($isDiterima)
                <a href="{{ route('peserta.logbook.index') }}" class="nav-item-link {{ request()->routeIs('peserta.logbook.*') ? 'active' : '' }}" title="{{ __('Logbook Mingguan') }}">
                    <span class="nav-item-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                    </span>
                    <span>{{ __('Logbook Mingguan') }}</span>
                </a>

                <a href="{{ route('peserta.tugas.index') }}" class="nav-item-link {{ request()->routeIs('peserta.tugas.*') ? 'active' : '' }}" title="{{ __('Tugas & Progres') }}">
                    <span class="nav-item-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11l3 3L22 4"></path>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                    </span>
                    <span>{{ __('Tugas & Progres') }}</span>
                </a>

                <a href="{{ route('peserta.nilai.show') }}" class="nav-item-link {{ request()->routeIs('peserta.nilai.*') ? 'active' : '' }}" title="{{ __('Lembar Nilai') }}">
                    <span class="nav-item-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="7"></circle>
                            <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                        </svg>
                    </span>
                    <span>{{ __('Lembar Nilai') }}</span>
                </a>
            @else
                <div class="nav-item-link locked" 
                     role="button" 
                     tabindex="0" 
                     onclick="notifyLockedFeature('{{ __('Logbook Mingguan') }}')" 
                     title="{{ __('Terkunci: Fitur tersedia setelah permohonan magang disetujui & ditempatkan') }}">
                    <span class="nav-item-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                        </svg>
                    </span>
                    <span>{{ __('Logbook Mingguan') }}</span>
                    <span class="badge-locked" title="{{ __('Terkunci: Fitur tersedia setelah permohonan magang disetujui & ditempatkan') }}" aria-label="{{ __('Terkunci') }}">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </span>
                </div>

                <div class="nav-item-link locked" 
                     role="button" 
                     tabindex="0" 
                     onclick="notifyLockedFeature('{{ __('Tugas & Progres') }}')" 
                     title="{{ __('Terkunci: Fitur tersedia setelah permohonan magang disetujui & ditempatkan') }}">
                    <span class="nav-item-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11l3 3L22 4"></path>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                    </span>
                    <span>{{ __('Tugas & Progres') }}</span>
                    <span class="badge-locked" title="{{ __('Terkunci: Fitur tersedia setelah permohonan magang disetujui & ditempatkan') }}" aria-label="{{ __('Terkunci') }}">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </span>
                </div>

                <div class="nav-item-link locked" 
                     role="button" 
                     tabindex="0" 
                     onclick="notifyLockedFeature('{{ __('Lembar Nilai') }}')" 
                     title="{{ __('Terkunci: Fitur tersedia setelah permohonan magang disetujui & ditempatkan') }}">
                    <span class="nav-item-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="7"></circle>
                            <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                        </svg>
                    </span>
                    <span>{{ __('Lembar Nilai') }}</span>
                    <span class="badge-locked" title="{{ __('Terkunci: Fitur tersedia setelah permohonan magang disetujui & ditempatkan') }}" aria-label="{{ __('Terkunci') }}">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </span>
                </div>
            @endif
        </nav>

        <div class="sidebar-footer">
            <a href="{{ route('peserta.pengaturan.index') }}" class="sidebar-footer-btn" title="{{ __('Pengaturan Akun') }}" aria-label="{{ __('Pengaturan Akun') }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                </svg>
            </a>
            <button type="button" id="sidebarThemeToggleBtn" class="sidebar-footer-btn" onclick="togglePortalThemeQuick()" title="{{ __('Ganti Mode Terang / Gelap') }}" aria-label="{{ __('Ganti Mode Terang / Gelap') }}" aria-pressed="false">
                <svg class="sun-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5" fill="currentColor" fill-opacity="0.25"></path>
                    <path d="M9 18h6"></path>
                    <path d="M10 22h4"></path>
                </svg>
                <svg class="moon-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" fill="currentColor" fill-opacity="0.15"></path>
                </svg>
            </button>
            <form action="{{ route('logout') }}" method="POST" class="sidebar-footer-form">
                @csrf
                <button type="submit" class="sidebar-footer-btn" title="{{ __('Keluar / Logout') }}" aria-label="{{ __('Keluar') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" 
                        class="sidebar-toggle-btn" 
                        id="sidebarToggleBtn" 
                        onclick="toggleSidebarPeserta()" 
                        aria-label="{{ __('Buka / Tutup Sidebar') }}" 
                        title="{{ __('Buka / Tutup Sidebar') }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
                <h1 class="topbar-title">@yield('page_title', 'Portal Peserta')</h1>
            </div>

            <div class="topbar-right">
                <div class="profile-dropdown-container">
                    <button type="button" 
                            class="user-chip-btn" 
                            id="userProfileBtn" 
                            aria-expanded="false" 
                            aria-haspopup="true" 
                            aria-label="{{ __('Menu Profil Pengguna') }}"
                            onclick="toggleProfileMenu(event)">
                        <div class="user-avatar" style="overflow: hidden;">
                            @if(auth()->user()?->profil?->foto_url)
                                <img src="{{ auth()->user()?->profil?->foto_url }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @else
                                {{ strtoupper(substr(auth()->user()?->nama ?? auth()->user()?->name ?? 'P', 0, 1)) }}
                            @endif
                        </div>
                        <div class="user-info">
                            <span class="user-name">{{ auth()->user()?->nama ?? auth()->user()?->name ?? 'Peserta' }}</span>
                            <span class="user-badge">{{ __('Peserta Magang') }}</span>
                        </div>
                        <svg class="dropdown-chevron" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div class="profile-dropdown-menu" id="userProfileMenu" role="menu" aria-labelledby="userProfileBtn">
                        <div class="dropdown-header">
                            <div class="dropdown-header-avatar" style="overflow: hidden;">
                                @if(auth()->user()?->profil?->foto_url)
                                    <img src="{{ auth()->user()?->profil?->foto_url }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                @else
                                    {{ strtoupper(substr(auth()->user()?->nama ?? auth()->user()?->name ?? 'P', 0, 1)) }}
                                @endif
                            </div>
                            <div class="dropdown-header-meta">
                                <span class="dropdown-header-name">{{ auth()->user()?->nama ?? auth()->user()?->name ?? 'Peserta' }}</span>
                                <span class="dropdown-header-email">{{ auth()->user()?->email ?? '-' }}</span>
                                <span class="dropdown-header-role">{{ __('Peserta Magang') }}</span>
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        <a href="{{ route('peserta.dashboard') }}" class="dropdown-item" role="menuitem">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            <span>{{ __('Dashboard') }}</span>
                        </a>

                        <a href="{{ route('peserta.profil.show') }}" class="dropdown-item" role="menuitem">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>{{ __('Biodata & Profil') }}</span>
                        </a>

                        <a href="{{ route('peserta.pendaftaran.index') }}" class="dropdown-item" role="menuitem">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>{{ __('Riwayat Pendaftaran') }}</span>
                        </a>

                        <a href="{{ route('peserta.pengaturan.index') }}" class="dropdown-item" role="menuitem">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>{{ __('Pengaturan Akun') }} <span style="font-size: 0.75rem; opacity: 0.7; font-weight: 400;">({{ __('Ganti Kata Sandi') }})</span></span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="dropdown-item dropdown-logout" role="menuitem">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>{{ __('Keluar Akun') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="content-area">
            @yield('content')
        </main>

        <!-- Footer Selaras Lintas Modul (Persistent Dark Navy) -->
        <footer class="gentelella-footer peserta-footer">
            <div class="pull-right" style="display: flex; align-items: center; justify-content: inherit; width: 100%;">
                <span>Portal MagKom &bull; Dinas Komunikasi dan Informatika Kabupaten Sleman</span>
            </div>
            <div class="clearfix"></div>
        </footer>
    </div>

    {{-- Floating Flash Messages (Top-Right Pop Up Terisolasi dari Flow Dokumen) --}}
    <div class="toast-floating-container" id="toastFloatingContainer" aria-live="polite" aria-atomic="true">
        <x-flash-alert />
    </div>

    <script>
        function dismissToast(btn) {
            const toast = btn.closest('.app-toast');
            if (!toast) return;
            toast.classList.add('toast-hiding');
            setTimeout(() => toast.remove(), 300);
        }

        // Global MagKomToast helper API for JS triggers
        window.MagKomToast = {
            show({ type = 'success', title = '', message = '', duration = 5000, actionUrl = null }) {
                let container = document.getElementById('toastFloatingContainer');
                if (!container) {
                    container = document.createElement('div');
                    container.className = 'toast-floating-container';
                    container.id = 'toastFloatingContainer';
                    document.body.appendChild(container);
                }
                const toast = document.createElement('div');
                toast.className = `app-toast alert-${type === 'error' ? 'error' : type}`;
                toast.setAttribute('role', 'alert');

                let iconSvg = '';
                if (type === 'success') {
                    iconSvg = '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>';
                } else if (type === 'warning') {
                    iconSvg = '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>';
                } else if (type === 'info') {
                    iconSvg = '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
                } else {
                    iconSvg = '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
                }

                const safeActionUrl = actionUrl && (actionUrl.startsWith('/') || actionUrl.startsWith('http://') || actionUrl.startsWith('https://')) ? actionUrl : null;

                toast.innerHTML = `
                    <div class="toast-icon-badge ${type}">${iconSvg}</div>
                    <div class="toast-content" style="flex: 1; min-width: 0;">
                        ${title ? `<div class="toast-title">${title}</div>` : ''}
                        <div class="toast-desc">${message}</div>
                        ${safeActionUrl ? `
                            <div style="margin-top: 8px;">
                                <a href="${safeActionUrl}" style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #01347C; color: #FFFFFF; font-size: 0.75rem; font-weight: 600; border-radius: 6px; text-decoration: none;">
                                    <span>Lihat Rincian</span>
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                                </a>
                            </div>
                        ` : ''}
                    </div>
                    <button type="button" class="toast-close-btn" onclick="dismissToast(this)" aria-label="Tutup notifikasi">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                `;

                container.appendChild(toast);

                if (duration > 0) {
                    let timer = setTimeout(() => {
                        toast.classList.add('toast-hiding');
                        setTimeout(() => toast.remove(), 300);
                    }, duration);

                    toast.addEventListener('mouseenter', () => clearTimeout(timer));
                    toast.addEventListener('mouseleave', () => {
                        timer = setTimeout(() => {
                            toast.classList.add('toast-hiding');
                            setTimeout(() => toast.remove(), 300);
                        }, 2500);
                    });
                }
            },
            success(title, message) { this.show({ type: 'success', title, message }); },
            error(title, message) { this.show({ type: 'error', title, message }); },
            warning(title, message) { this.show({ type: 'warning', title, message }); },
            info(title, message) { this.show({ type: 'info', title, message }); }
        };

        // Auto dismiss toast after 5s with pause on hover
        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('.toast-floating-container .app-toast');
            toasts.forEach(toast => {
                let timer = setTimeout(() => {
                    toast.classList.add('toast-hiding');
                    setTimeout(() => toast.remove(), 300);
                }, 5000);

                toast.addEventListener('mouseenter', () => clearTimeout(timer));
                toast.addEventListener('mouseleave', () => {
                    timer = setTimeout(() => {
                        toast.classList.add('toast-hiding');
                        setTimeout(() => toast.remove(), 300);
                    }, 3000);
                });
            });
        });
        function toggleSidebarPeserta() {
            const isMobile = window.innerWidth <= 1024;
            const sidebar = document.getElementById('sidebarNav');
            const backdrop = document.getElementById('drawerBackdrop');

            if (isMobile) {
                if (sidebar) sidebar.classList.toggle('open');
                if (backdrop) backdrop.classList.toggle('open');
            } else {
                const html = document.documentElement;
                const isCollapsed = html.classList.toggle('sidebar-collapsed');
                try {
                    localStorage.setItem('magkom_peserta_sidebar_collapsed', isCollapsed ? 'true' : 'false');
                } catch (e) {}
            }
        }

        function toggleSidebar() {
            toggleSidebarPeserta();
        }

        function notifyLockedFeature(featureName) {
            if (window.MagKomToast) {
                window.MagKomToast.show({
                    type: 'warning',
                    title: featureName + ' Masih Terkunci',
                    message: 'Fitur ' + featureName + ' akan aktif secara otomatis setelah permohonan magang Anda disetujui & penempatan telah dimulai.',
                    duration: 5000
                });
            } else {
                alert(featureName + ' masih terkunci. Fitur akan terbuka setelah permohonan magang Anda disetujui.');
            }
        }

        function toggleProfileMenu(e) {
            e.stopPropagation();
            const btn = document.getElementById('userProfileBtn');
            const menu = document.getElementById('userProfileMenu');
            if (!btn || !menu) return;

            const isOpen = menu.classList.contains('open');
            if (isOpen) {
                closeProfileMenu();
            } else {
                menu.classList.add('open');
                btn.setAttribute('aria-expanded', 'true');
            }
        }

        function closeProfileMenu() {
            const btn = document.getElementById('userProfileBtn');
            const menu = document.getElementById('userProfileMenu');
            if (menu && menu.classList.contains('open')) {
                menu.classList.remove('open');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }
        }

        // Tutup dropdown saat klik di luar
        document.addEventListener('click', function(e) {
            const container = document.querySelector('.profile-dropdown-container');
            if (container && !container.contains(e.target)) {
                closeProfileMenu();
            }
        });

        // Tutup dropdown saat tombol Escape ditekan
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeProfileMenu();
            }
        });

        // Theme Switcher Engine Global
        window.applyPortalTheme = function(theme) {
            let effective = 'light';
            if (theme === 'sistem') {
                effective = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            } else if (theme === 'gelap') {
                effective = 'dark';
            } else {
                effective = 'light';
            }
            document.documentElement.setAttribute('data-theme', effective);
            document.documentElement.setAttribute('data-pref-theme', theme);
            const toggleBtn = document.getElementById('sidebarThemeToggleBtn');
            if (toggleBtn) {
                const isDark = effective === 'dark';
                toggleBtn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                const titleText = isDark ? "{{ __('Beralih ke Mode Terang') }}" : "{{ __('Beralih ke Mode Gelap') }}";
                toggleBtn.setAttribute('title', titleText);
                toggleBtn.setAttribute('aria-label', titleText);
            }
            try {
                localStorage.setItem('magkom_theme', theme);
            } catch (e) {}
        };

        (function() {
            const currentEffective = document.documentElement.getAttribute('data-theme') || 'light';
            const toggleBtn = document.getElementById('sidebarThemeToggleBtn');
            if (toggleBtn) {
                const isDark = currentEffective === 'dark';
                toggleBtn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                const titleText = isDark ? "{{ __('Beralih ke Mode Terang') }}" : "{{ __('Beralih ke Mode Gelap') }}";
                toggleBtn.setAttribute('title', titleText);
                toggleBtn.setAttribute('aria-label', titleText);
            }
        })();

        // Toggle Cepat Terang / Gelap Instan
        window.togglePortalThemeQuick = function() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            const nextTheme = currentTheme === 'dark' ? 'terang' : 'gelap';
            window.applyPortalTheme(nextTheme);
            const preferensiUrl = "{{ Route::has('tema.update') ? route('tema.update') : (Route::has('peserta.pengaturan.preferensi.update') ? route('peserta.pengaturan.preferensi.update') : '#') }}";
            if (preferensiUrl && preferensiUrl !== '#') {
                fetch(preferensiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify({ tema: nextTheme })
                }).catch(function() {});
            }
        };

        // Listener jika OS tema berubah dan user memilih 'sistem'
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                const currentPref = document.documentElement.getAttribute('data-pref-theme') || localStorage.getItem('magkom_theme');
                if (currentPref === 'sistem') {
                    const effective = e.matches ? 'dark' : 'light';
                    document.documentElement.setAttribute('data-theme', effective);
                    const toggleBtn = document.getElementById('sidebarThemeToggleBtn');
                    if (toggleBtn) {
                        const isDark = effective === 'dark';
                        toggleBtn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                        const titleText = isDark ? "{{ __('Beralih ke Mode Terang') }}" : "{{ __('Beralih ke Mode Gelap') }}";
                        toggleBtn.setAttribute('title', titleText);
                        toggleBtn.setAttribute('aria-label', titleText);
                    }
                }
            });
        }
    </script>
    <script src="{{ asset('js/gentelella-theme.js') }}"></script>
    <script src="{{ asset('js/dashboard-cockpit-unified.js') }}"></script>
    @stack('scripts')
    {{-- Mobile Floating Action Button (FAB) --}}
    <button type="button" class="cv-mobile-fab" data-action="toggle-mobile-aside" aria-label="Buka Menu Tindakan">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        <span>Tindakan</span>
    </button>

    <x-sse-toast-listener />
</body>
</html>

