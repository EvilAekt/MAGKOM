<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal MagKom - Informasi Kuota & Pendaftaran Magang Kominfo')</title>
    <meta name="description" content="@yield('meta_description', 'Transparansi kuota pendaftaran magang Dinas Komunikasi dan Informatika untuk siswa SMK dan mahasiswa D3/S1.')">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('og_title', 'Informasi Kuota Magang Dinas Kominfo - MagKom')">
    <meta property="og:description" content="@yield('og_description', 'Lihat ketersediaan kuota magang per jurusan secara langsung dan transparan.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="id_ID">

    <!-- Favicon Resmi Sleman -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts: Open Sans (Design System DS-MagKom Bab 2.2) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

    <!-- Anti-Flash Theme Script -->
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
            } catch (e) {}
        })();
    </script>

    <style>
        :root {
            /* MagKom / Warna Tokens (Design System DS-MagKom Bab 2.1) */
            --blue-600: #0751FF;
            --blue-700: #0641CC;
            --blue-tint: #E6EEFF;
            --blue-tint-line: #B3C8FF;

            --neutral-0: #FFFFFF;
            --neutral-100: #F8FAFC;
            --neutral-150: #F5F7FB;
            --neutral-200: #EEF0F5;
            --neutral-400: #D0D3D9;
            --neutral-500: #7A8494;
            --neutral-600: #666666;
            --neutral-700: #333333;
            --neutral-800: #1D2530;
            --neutral-900: #05070B;

            /* Status Tokens (WCAG 2.1 AA Compliant) */
            --green-100: #E8F7EE;
            --green-600: #16A34A;
            --green-700: #166534;

            --amber-100: #FFFBEB;
            --amber-200: #FEF3C7;
            --amber-300: #FDE68A;
            --amber-700: #92400E;

            --red-100: #FEF2F2;
            --red-200: #FEE2E2;
            --red-300: #FECACA;
            --red-600: #DC2626;
            --red-700: #991B1B;

            /* Semantic Aliases */
            --primary-blue: var(--blue-600);
            --primary-hover: var(--blue-700);
            --gray-50: var(--neutral-100);
            --gray-100: var(--neutral-100);
            --gray-200: var(--neutral-200);
            --gray-300: var(--neutral-400);
            --gray-400: var(--neutral-400);
            --gray-500: var(--neutral-500);
            --gray-600: var(--neutral-600);
            --gray-700: var(--neutral-700);
            --gray-800: var(--neutral-800);
            --gray-900: var(--neutral-900);

            --font-sans: 'Open Sans', Arial, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Dark Mode Tokens & Overrides (DS-MagKom) */
        [data-theme="dark"] {
            --neutral-0: #182234;
            --neutral-100: #0F172A;
            --neutral-150: #0A0F1D;
            --neutral-200: #26354D;
            --neutral-400: #475569;
            --neutral-500: #94A3B8;
            --neutral-600: #CBD5E1;
            --neutral-700: #E2E8F0;
            --neutral-800: #F8FAFC;
            --neutral-900: #FFFFFF;

            --blue-tint: #1E3A8A;
            --blue-tint-line: #3B82F6;
        }

        [data-theme="dark"] body {
            background-color: var(--neutral-150);
            color: var(--neutral-700);
        }

        [data-theme="dark"] .bg-white {
            background-color: var(--neutral-0) !important;
            border-color: var(--neutral-200) !important;
        }

        [data-theme="dark"] .text-slate-900,
        [data-theme="dark"] .text-gray-900 {
            color: var(--neutral-800) !important;
        }

        [data-theme="dark"] .text-slate-700,
        [data-theme="dark"] .text-gray-700 {
            color: var(--neutral-700) !important;
        }

        [data-theme="dark"] .text-slate-500,
        [data-theme="dark"] .text-gray-500 {
            color: var(--neutral-500) !important;
        }

        [data-theme="dark"] .border-slate-200,
        [data-theme="dark"] .border-gray-200 {
            border-color: var(--neutral-200) !important;
        }

        [data-theme="dark"] .header-nav {
            background: var(--neutral-0, #182234) !important;
            border-bottom-color: var(--neutral-200, #26354D) !important;
        }

        [data-theme="dark"] .brand-text h1 {
            color: var(--neutral-900, #FFFFFF) !important;
        }

        [data-theme="dark"] .nav-link {
            color: var(--neutral-600, #CBD5E1) !important;
        }

        [data-theme="dark"] .nav-link:hover,
        [data-theme="dark"] .nav-link.active {
            color: var(--neutral-900, #FFFFFF) !important;
        }

        [data-theme="dark"] .nav-action-link {
            color: var(--neutral-700, #E2E8F0) !important;
        }

        [data-theme="dark"] .nav-action-link:hover {
            color: var(--blue-600, #3B82F6) !important;
        }

        [data-theme="dark"] .mobile-drawer {
            background: var(--neutral-0, #182234) !important;
            border-bottom-color: var(--neutral-200, #26354D) !important;
        }

        [data-theme="dark"] .mobile-toggle {
            color: var(--neutral-800, #F8FAFC) !important;
        }

        [data-theme="dark"] .btn-outline {
            color: var(--neutral-800, #F8FAFC) !important;
            border-color: var(--neutral-400, #475569) !important;
        }

        [data-theme="dark"] .btn-outline:hover {
            background: var(--neutral-100, #0F172A) !important;
        }

        /* Universal Inline-Style Inversion for Public views */
        [data-theme="dark"] [style*="color: #0F172A"],
        [data-theme="dark"] [style*="color:#0F172A"],
        [data-theme="dark"] [style*="color: #1E293B"],
        [data-theme="dark"] [style*="color: #000000"],
        [data-theme="dark"] [style*="color: #333333"] {
            color: var(--neutral-800, #F8FAFC) !important;
        }

        [data-theme="dark"] [style*="color: #334155"],
        [data-theme="dark"] [style*="color: #475569"],
        [data-theme="dark"] [style*="color: #666666"] {
            color: var(--neutral-700, #E2E8F0) !important;
        }

        [data-theme="dark"] [style*="color: #64748B"],
        [data-theme="dark"] [style*="color: #7A8494"] {
            color: var(--neutral-500, #94A3B8) !important;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: var(--font-sans);
        }

        body {
            background-color: var(--neutral-0);
            color: var(--neutral-800);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* =====================================================================
           HEADER NAVBAR (MATCHING FIGMA NODE 562-54980: 1440 Fill x 76)
           ===================================================================== */
        .header-nav {
            background: #ffffff;
            border-bottom: 1px solid var(--neutral-400);
            position: sticky;
            top: 0;
            z-index: 100;
            height: 76px;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .header-nav .container {
            width: 100%;
            max-width: 1360px;
            margin: 0 auto;
            padding: 0 2.5rem;
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        /* Brand Logo: Rounded blue box with 'M' + MagKom KOMINFO PORTAL */
        .brand-link {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            text-decoration: none;
            flex-shrink: 0;
        }

        .brand-logo-img {
            height: 2.75rem;
            width: auto;
            max-width: 2.75rem;
            object-fit: contain;
            flex-shrink: 0;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.08));
            transition: transform 0.2s ease;
        }

        .brand-link:hover .brand-logo-img {
            transform: scale(1.05);
        }

        .brand-text h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--neutral-800);
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .brand-text p {
            font-size: 0.6875rem;
            color: var(--blue-600);
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-top: 0.15rem;
        }

        /* Menu Links: Jarak lega dan proporsional dari Logo */
        .nav-menu {
            display: flex;
            align-items: center;
            gap: 2.25rem; /* ~36px lega */
            margin-left: 4.5rem;
            margin-right: auto;
        }

        .nav-link {
            color: var(--neutral-600);
            text-decoration: none;
            font-size: 0.9375rem;
            font-weight: 600;
            transition: color 0.15s ease;
            position: relative;
            padding: 0.35rem 0;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--neutral-800);
        }

        /* Actions */
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-shrink: 0;
        }

        .nav-action-link {
            color: var(--neutral-800);
            text-decoration: none;
            font-size: 0.9375rem;
            font-weight: 600;
            transition: color 0.15s ease;
        }

        .nav-action-link:hover {
            color: var(--blue-600);
        }

        /* =====================================================================
           LANDING USER ACCOUNT CHIP & FLOATING DROPDOWN MENU
           ===================================================================== */
        .landing-user-menu-wrap {
            position: relative;
            display: inline-block;
        }

        .landing-user-chip-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.3rem 0.85rem 0.3rem 0.45rem;
            background: #ffffff;
            border: 1px solid var(--neutral-400);
            border-radius: 9999px;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            text-align: left;
            color: var(--neutral-800);
            outline: none;
        }

        .landing-user-chip-btn:hover {
            border-color: var(--blue-600);
            background: rgba(7, 81, 255, 0.04);
            box-shadow: 0 2px 8px rgba(7, 81, 255, 0.12);
        }

        .landing-user-chip-btn[aria-expanded="true"] {
            border-color: var(--blue-600);
            box-shadow: 0 0 0 3px rgba(7, 81, 255, 0.15);
        }

        .landing-user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--blue-600);
            color: #ffffff;
            font-size: 0.875rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 1px 3px rgba(7, 81, 255, 0.3);
        }

        .landing-user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .landing-user-meta {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
            max-width: 130px;
        }

        .landing-user-name {
            font-size: 0.8125rem;
            font-weight: 700;
            color: var(--neutral-800);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .landing-role-chip {
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .landing-role-chip.superadmin { color: #7C3AED; }
        .landing-role-chip.admin { color: #0284C7; }
        .landing-role-chip.pembimbing { color: #059669; }
        .landing-role-chip.peserta { color: #0751FF; }

        .landing-user-chevron {
            color: var(--neutral-500);
            transition: transform 0.2s ease;
            flex-shrink: 0;
        }

        .landing-user-chip-btn[aria-expanded="true"] .landing-user-chevron {
            transform: rotate(180deg);
            color: var(--blue-600);
        }

        /* Floating Dropdown Menu Card (DS-MagKom Premium Two-Line Popover) */
        .landing-dropdown-menu {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            width: 325px;
            background: #ffffff;
            border: 1px solid var(--neutral-400);
            border-radius: 1.15rem;
            box-shadow: 0 16px 36px -4px rgba(15, 23, 42, 0.14), 0 4px 12px -2px rgba(15, 23, 42, 0.06);
            display: none;
            flex-direction: column;
            padding: 0.5rem;
            z-index: 1050;
            animation: landingDropdownFade 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .landing-dropdown-menu.show {
            display: flex;
        }

        @keyframes landingDropdownFade {
            from {
                opacity: 0;
                transform: translateY(-8px) scale(0.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .landing-dropdown-header {
            padding: 0.85rem 0.85rem 0.85rem;
            border-bottom: 1px solid var(--neutral-200);
            margin-bottom: 0.4rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: linear-gradient(180deg, #F8FAFC 0%, #FFFFFF 100%);
            border-radius: 0.85rem 0.85rem 0.35rem 0.35rem;
        }

        .landing-dropdown-header-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--blue-600);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            border: 2px solid #BFDBFE;
            box-shadow: 0 2px 6px rgba(7, 81, 255, 0.2);
        }

        .landing-dropdown-header-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .landing-dropdown-header-info {
            flex: 1;
            min-width: 0;
        }

        .landing-dropdown-header-name {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--neutral-800);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
            margin-bottom: 0.15rem;
        }

        .landing-dropdown-header-email {
            font-size: 0.72rem;
            color: var(--neutral-500);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
            margin-bottom: 0.35rem;
        }

        .landing-dropdown-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.15rem 0.55rem;
            border-radius: 9999px;
            background: var(--neutral-200);
        }

        .landing-dropdown-header-badge.peserta { background: #E6EEFF; color: #0641CC; border: 1px solid #B3C8FF; }
        .landing-dropdown-header-badge.pembimbing { background: #E8F7EE; color: #166534; border: 1px solid #A7F3D0; }
        .landing-dropdown-header-badge.admin { background: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; }
        .landing-dropdown-header-badge.superadmin { background: #FAF5FF; color: #6B21A8; border: 1px solid #E9D5FF; }

        .landing-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.65rem 0.75rem;
            color: var(--neutral-800);
            text-decoration: none;
            border-radius: 0.65rem;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
            box-sizing: border-box;
            position: relative;
        }

        .landing-dropdown-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 0.55rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .landing-dropdown-icon-box.dashboard { background: #EFF6FF; border: 1px solid #BFDBFE; color: #1D4ED8; }
        .landing-dropdown-icon-box.profil { background: #FAF5FF; border: 1px solid #E9D5FF; color: #7C3AED; }
        .landing-dropdown-icon-box.setting { background: #F8FAFC; border: 1px solid #E2E8F0; color: #475569; }
        .landing-dropdown-icon-box.logout { background: #FEF2F2; border: 1px solid #FECACA; color: #DC2626; }

        .landing-dropdown-item-body {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
            line-height: 1.25;
        }

        .landing-dropdown-item-title {
            font-size: 0.84rem;
            font-weight: 700;
            color: var(--neutral-800);
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .landing-dropdown-item-desc {
            font-size: 0.72rem;
            color: var(--neutral-500);
            margin-top: 0.15rem;
            line-height: 1.35;
            white-space: normal;
        }

        .landing-dropdown-chevron-right {
            color: var(--neutral-400);
            opacity: 0.6;
            transition: transform 0.2s ease, opacity 0.2s ease, color 0.2s ease;
            flex-shrink: 0;
        }

        .landing-dropdown-item:hover {
            background: var(--neutral-100);
            transform: translateX(2px);
        }

        .landing-dropdown-item:hover .landing-dropdown-icon-box {
            transform: scale(1.05);
        }

        .landing-dropdown-item:hover .landing-dropdown-item-title {
            color: var(--blue-600);
        }

        .landing-dropdown-item:hover .landing-dropdown-chevron-right {
            opacity: 1;
            color: var(--blue-600);
            transform: translateX(2px);
        }

        .landing-dropdown-divider {
            height: 1px;
            background: var(--neutral-200);
            margin: 0.35rem 0.25rem;
        }

        .landing-dropdown-item.logout-action {
            color: #DC2626;
        }

        .landing-dropdown-item.logout-action:hover {
            background: #FEF2F2;
        }

        .landing-dropdown-item.logout-action:hover .landing-dropdown-item-title {
            color: #B91C1C;
        }

        .landing-dropdown-item.logout-action:hover .landing-dropdown-chevron-right {
            color: #DC2626;
        }

        /* Dark mode overrides for landing dropdown */
        [data-theme="dark"] .landing-user-chip-btn {
            background: #182234;
            border-color: #26354D;
            color: #F8FAFC;
        }

        [data-theme="dark"] .landing-user-chip-btn:hover {
            border-color: var(--blue-600);
            background: rgba(7, 81, 255, 0.1);
        }

        [data-theme="dark"] .landing-dropdown-menu {
            background: #182234;
            border-color: #26354D;
            box-shadow: 0 16px 36px -4px rgba(0, 0, 0, 0.5);
        }

        [data-theme="dark"] .landing-dropdown-header {
            background: linear-gradient(180deg, #0F172A 0%, #182234 100%);
            border-bottom-color: #26354D;
        }

        [data-theme="dark"] .landing-dropdown-item-title {
            color: #F8FAFC;
        }

        [data-theme="dark"] .landing-dropdown-item-desc {
            color: #94A3B8;
        }

        [data-theme="dark"] .landing-dropdown-item:hover {
            background: #0F172A;
        }

        [data-theme="dark"] .landing-dropdown-divider {
            background: #26354D;
        }

        [data-theme="dark"] .landing-dropdown-icon-box.setting {
            background: #1E293B;
            border-color: #334155;
            color: #CBD5E1;
        }

        [data-theme="dark"] .landing-dropdown-item.logout-action:hover {
            background: rgba(220, 38, 38, 0.15);
        }

        [data-theme="dark"] .landing-dropdown-item.logout-action:hover {
            background: rgba(220, 38, 38, 0.15);
            color: #F87171;
        }

        /* Standard Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.65rem 1.4rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: 1px solid transparent;
            line-height: 1.25;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: #ffffff;
            border: 1px solid var(--primary-blue);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
            color: #ffffff;
        }

        .btn-outline {
            background: transparent;
            color: #0f172a;
            border: 1px solid var(--gray-300);
        }

        .btn-outline:hover {
            background: var(--gray-100);
            border-color: var(--gray-400);
        }

        .btn-white {
            background: #ffffff;
            color: #0f172a;
            border: 1px solid #ffffff;
            font-weight: 700;
        }

        .btn-white:hover {
            background: #f8fafc;
            color: #000000;
        }

        .btn-ghost-white {
            background: rgba(0, 0, 0, 0.25);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.45);
        }

        .btn-ghost-white:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }

        .btn-disabled {
            background: #e2e8f0;
            color: #94a3b8;
            cursor: not-allowed;
            border: 1px solid #cbd5e1;
        }

        /* Mobile Hamburger */
        .mobile-toggle {
            display: none;
            background: transparent;
            border: none;
            color: #0f172a;
            cursor: pointer;
            padding: 0.5rem;
        }

        .mobile-toggle svg {
            width: 1.75rem;
            height: 1.75rem;
        }

        .mobile-drawer {
            display: none;
            position: fixed;
            top: 72px;
            left: 0;
            right: 0;
            background: #ffffff;
            border-bottom: 1px solid var(--gray-200);
            padding: 1.5rem;
            z-index: 99;
            flex-direction: column;
            gap: 1.25rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .mobile-drawer.open {
            display: flex;
        }

        .mobile-drawer .nav-link {
            font-size: 1.05rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--gray-100);
        }

        .main-content {
            flex: 1;
        }

        /* =====================================================================
           FOOTER (MATCHING FIGMA FOOTER: 1440 Fill x 341 Hug)
           ===================================================================== */
        .footer {
            background: #111827;
            color: #94a3b8;
            padding: 4rem 0 3rem;
            font-size: 0.85rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 2.5rem;
            line-height: 1.6;
        }

        .footer-col-title {
            color: #38bdf8;
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
        }

        .footer-links {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .footer-links a:hover {
            color: #ffffff;
        }

        .footer-address {
            color: #94a3b8;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        @media (max-width: 992px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
            }
        }

        @media (max-width: 768px) {
            .nav-menu, .nav-actions {
                display: none;
            }
            .mobile-toggle {
                display: block;
            }
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Header Navbar (Figma node 562-54980) -->
    <header class="header-nav">
        <div class="container">
            <div class="nav-inner">
                <!-- Brand: MagKom KOMINFO PORTAL -->
                <a href="{{ route('landing') }}" class="brand-link" id="brand-link" title="MagKom Kominfo Sleman">
                    <img src="{{ asset('images/logo-sleman.png') }}" alt="Lambang Kabupaten Sleman" class="brand-logo-img">
                    <div class="brand-text">
                        <h1>MagKom</h1>
                        <p>KOMINFO PORTAL</p>
                    </div>
                </a>

                <!-- Nav Menu -->
                <nav class="nav-menu">
                    <a href="{{ route('landing') }}" class="nav-link active">Beranda</a>
                    <a href="{{ route('landing') }}#kuota-section" class="nav-link">Bidang & kuota</a>
                    <a href="{{ route('landing') }}#alur-section" class="nav-link">Alur magang</a>
                    <a href="{{ route('landing') }}#pengumuman-section" class="nav-link">Pengumuman</a>
                    <a href="#footer" class="nav-link">Hubungi kami</a>
                </nav>

                <!-- Actions -->
                <div class="nav-actions">
                    @auth
                        @php
                            $user = auth()->user();
                            $userRole = $user->role->value ?? 'peserta';
                            $dashboardUrl = match($userRole) {
                                'superadmin' => route('superadmin.dashboard'),
                                'admin' => route('admin.dashboard'),
                                'pembimbing' => route('pembimbing.dashboard'),
                                default => route('peserta.dashboard'),
                            };
                            $profileUrl = match($userRole) {
                                'superadmin' => route('superadmin.profil.show'),
                                'admin' => route('admin.profil.show'),
                                'pembimbing' => route('pembimbing.profil.show'),
                                default => route('peserta.profil.show'),
                            };
                            $settingUrl = match($userRole) {
                                'superadmin' => route('superadmin.akun.index'),
                                'admin' => route('admin.pengaturan.index'),
                                'pembimbing' => route('pembimbing.pengaturan.index'),
                                default => route('peserta.pengaturan.index'),
                            };
                        @endphp
                        <div class="landing-user-menu-wrap" id="landingUserMenuWrap">
                            <button type="button" id="landingUserMenuBtn" class="landing-user-chip-btn" onclick="toggleLandingUserMenu(event)" aria-expanded="false" aria-haspopup="true" aria-label="Menu Akun {{ $user->nama }}">
                                <div class="landing-user-avatar">
                                    @if($user?->foto_url)
                                        <img src="{{ $user->foto_url }}" alt="{{ $user->nama }}">
                                    @else
                                        {{ strtoupper(substr($user?->nama ?? 'U', 0, 1)) }}
                                    @endif
                                </div>
                                <div class="landing-user-meta">
                                    <span class="landing-user-name">{{ $user->nama }}</span>
                                    <span class="landing-role-chip {{ $userRole }}">{{ ucfirst($userRole) }}</span>
                                </div>
                                <svg class="landing-user-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </button>

                            <!-- Floating Dropdown Menu (DS-MagKom Rich Account Card) -->
                            <div id="landingUserDropdown" class="landing-dropdown-menu" role="menu" aria-labelledby="landingUserMenuBtn">
                                <div class="landing-dropdown-header">
                                    <div class="landing-dropdown-header-avatar">
                                        @if($user?->foto_url)
                                            <img src="{{ $user->foto_url }}" alt="{{ $user->nama }}">
                                        @else
                                            {{ strtoupper(substr($user?->nama ?? 'U', 0, 1)) }}
                                        @endif
                                    </div>
                                    <div class="landing-dropdown-header-info">
                                        <div class="landing-dropdown-header-name">{{ $user->nama }}</div>
                                        <div class="landing-dropdown-header-email">{{ $user->email }}</div>
                                        <span class="landing-dropdown-header-badge {{ $userRole }}">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;"></span>
                                            {{ ucfirst($userRole) }}
                                        </span>
                                    </div>
                                </div>

                                <a href="{{ $dashboardUrl }}" class="landing-dropdown-item" role="menuitem">
                                    <div class="landing-dropdown-icon-box dashboard">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="3" y="3" width="7" height="9"></rect>
                                            <rect x="14" y="3" width="7" height="5"></rect>
                                            <rect x="14" y="12" width="7" height="9"></rect>
                                            <rect x="3" y="16" width="7" height="5"></rect>
                                        </svg>
                                    </div>
                                    <div class="landing-dropdown-item-body">
                                        <span class="landing-dropdown-item-title">Dashboard</span>
                                        <span class="landing-dropdown-item-desc">Status pendaftaran, tugas & logbook</span>
                                    </div>
                                    <svg class="landing-dropdown-chevron-right" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>

                                <a href="{{ $profileUrl }}" class="landing-dropdown-item" role="menuitem">
                                    <div class="landing-dropdown-icon-box profil">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="12" cy="7" r="4"></circle>
                                        </svg>
                                    </div>
                                    <div class="landing-dropdown-item-body">
                                        <span class="landing-dropdown-item-title">Profil Saya</span>
                                        <span class="landing-dropdown-item-desc">Kelola biodata diri & riwayat berkas</span>
                                    </div>
                                    <svg class="landing-dropdown-chevron-right" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>

                                <a href="{{ $settingUrl }}" class="landing-dropdown-item" role="menuitem">
                                    <div class="landing-dropdown-icon-box setting">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="3"></circle>
                                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                        </svg>
                                    </div>
                                    <div class="landing-dropdown-item-body">
                                        <span class="landing-dropdown-item-title">Pengaturan</span>
                                        <span class="landing-dropdown-item-desc">Keamanan kata sandi & preferensi</span>
                                    </div>
                                    <svg class="landing-dropdown-chevron-right" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="9 18 15 12 9 6"></polyline>
                                    </svg>
                                </a>

                                <div class="landing-dropdown-divider"></div>

                                <form action="{{ route('logout') }}" method="POST" style="margin: 0; width: 100%;">
                                    @csrf
                                    <button type="submit" class="landing-dropdown-item logout-action" role="menuitem">
                                        <div class="landing-dropdown-icon-box logout">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                                <polyline points="16 17 21 12 16 7"></polyline>
                                                <line x1="21" y1="12" x2="9" y2="12"></line>
                                            </svg>
                                        </div>
                                        <div class="landing-dropdown-item-body">
                                            <span class="landing-dropdown-item-title">Keluar (Logout)</span>
                                            <span class="landing-dropdown-item-desc">Akhiri sesi akun pada perangkat ini</span>
                                        </div>
                                        <svg class="landing-dropdown-chevron-right" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="nav-action-link">Masuk</a>
                        <a href="{{ route('landing') }}#kuota-section" class="btn btn-primary">Daftar magang</a>
                    @endauth
                </div>

                <!-- Mobile Toggle -->
                <button type="button" class="mobile-toggle" onclick="toggleMobileDrawer()" aria-label="Buka Menu">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Drawer -->
        <div class="mobile-drawer" id="mobile-drawer">
            @auth
                @php
                    $mobileUser = auth()->user();
                    $mobileRole = $mobileUser->role->value ?? 'peserta';
                    $mobileDashUrl = match($mobileRole) {
                        'superadmin' => route('superadmin.dashboard'),
                        'admin' => route('admin.dashboard'),
                        'pembimbing' => route('pembimbing.dashboard'),
                        default => route('peserta.dashboard'),
                    };
                    $mobileProfilUrl = match($mobileRole) {
                        'superadmin' => route('superadmin.profil.show'),
                        'admin' => route('admin.profil.show'),
                        'pembimbing' => route('pembimbing.profil.show'),
                        default => route('peserta.profil.show'),
                    };
                    $mobileSettingUrl = match($mobileRole) {
                        'superadmin' => route('superadmin.akun.index'),
                        'admin' => route('admin.pengaturan.index'),
                        'pembimbing' => route('pembimbing.pengaturan.index'),
                        default => route('peserta.pengaturan.index'),
                    };
                @endphp
                <div style="background: var(--neutral-100); border: 1px solid var(--neutral-200); border-radius: 0.875rem; padding: 0.85rem 1rem; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.75rem;">
                    <div class="landing-user-avatar" style="width: 36px; height: 36px; font-size: 0.95rem;">
                        @if($mobileUser?->foto_url)
                            <img src="{{ $mobileUser->foto_url }}" alt="{{ $mobileUser->nama }}">
                        @else
                            {{ strtoupper(substr($mobileUser?->nama ?? 'U', 0, 1)) }}
                        @endif
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-800); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $mobileUser->nama }}</div>
                        <div style="font-size: 0.75rem; color: var(--neutral-500); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $mobileUser->email }}</div>
                    </div>
                    <span class="landing-role-chip {{ $mobileRole }}" style="font-size: 0.6875rem; padding: 0.2rem 0.5rem; background: var(--neutral-200); border-radius: 9999px;">{{ ucfirst($mobileRole) }}</span>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <a href="{{ $mobileDashUrl }}" class="btn btn-primary" style="padding: 0.5rem; font-size: 0.8125rem; justify-content: center;">Dashboard</a>
                    <a href="{{ $mobileProfilUrl }}" class="btn btn-outline" style="padding: 0.5rem; font-size: 0.8125rem; justify-content: center;">Profil</a>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1rem;">
                    <a href="{{ $mobileSettingUrl }}" class="btn btn-outline" style="padding: 0.5rem; font-size: 0.8125rem; justify-content: center;">Pengaturan</a>
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0; width: 100%;">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="width: 100%; padding: 0.5rem; font-size: 0.8125rem; justify-content: center; color: #DC2626; border-color: rgba(220, 38, 38, 0.3);">Keluar</button>
                    </form>
                </div>
                <div style="height: 1px; background: var(--neutral-200); margin-bottom: 0.75rem;"></div>
            @endauth

            <a href="{{ route('landing') }}" class="nav-link" onclick="toggleMobileDrawer()">Beranda</a>
            <a href="{{ route('landing') }}#kuota-section" class="nav-link" onclick="toggleMobileDrawer()">Bidang & kuota</a>
            <a href="{{ route('landing') }}#alur-section" class="nav-link" onclick="toggleMobileDrawer()">Alur magang</a>
            <a href="{{ route('landing') }}#pengumuman-section" class="nav-link" onclick="toggleMobileDrawer()">Pengumuman</a>
            <a href="#footer" class="nav-link" onclick="toggleMobileDrawer()">Hubungi kami</a>
            @guest
                <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
                    <a href="{{ route('login') }}" class="btn btn-outline" style="flex: 1; text-align: center;">Masuk</a>
                    <a href="{{ route('landing') }}#kuota-section" class="btn btn-primary" style="flex: 1; text-align: center;" onclick="toggleMobileDrawer()">Daftar magang</a>
                </div>
            @endguest
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Footer (Figma node 562-54979) -->
    <footer class="footer" id="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Column 1: Alamat -->
                <div>
                    <h3 class="footer-col-title">Alamat</h3>
                    <div class="footer-address">
                        <p>Jl Parasamya (Komplek Kantor Bupati Sleman) Beran, Tridadi, Sleman, Yogyakarta 55511</p>
                        <p>Telepon: (0274) 868405</p>
                        <p>Email: kominfo@slemankab.go.id</p>
                        <p>Senin sampai Kamis 07.30 sampai 15.30 WIB, Jumat 07.30 sampai 14.30 WIB</p>
                    </div>
                </div>

                <!-- Column 2: Media Sosial -->
                <div>
                    <h3 class="footer-col-title">Media Sosial</h3>
                    <ul class="footer-links">
                        <li><a href="https://instagram.com/kominfo_sleman" target="_blank" rel="noopener noreferrer">kominfo_sleman</a></li>
                        <li><a href="https://twitter.com/diskominfosleman" target="_blank" rel="noopener noreferrer">diskominfosleman</a></li>
                    </ul>
                </div>

                <!-- Column 3: Tautan Terkait -->
                <div>
                    <h3 class="footer-col-title">Tautan Terkait</h3>
                    <ul class="footer-links">
                        <li><a href="{{ route('kebijakan-privasi') }}">Kebijakan Privasi</a></li>
                        <li><a href="https://kominfo.slemankab.go.id" target="_blank" rel="noopener noreferrer">Situs Kominfo Sleman</a></li>
                        <li><a href="https://slemankab.go.id" target="_blank" rel="noopener noreferrer">Sleman Digital</a></li>
                    </ul>
                </div>

                <!-- Column 4: Akun -->
                <div>
                    <h3 class="footer-col-title">Akun</h3>
                    <ul class="footer-links">
                        <li><a href="{{ route('login') }}">Masuk Peserta</a></li>
                        <li><a href="{{ route('login') }}">Masuk Pembimbing</a></li>
                        <li><a href="{{ route('login') }}">Masuk Admin</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function toggleMobileDrawer() {
            const drawer = document.getElementById('mobile-drawer');
            if (drawer) {
                drawer.classList.toggle('open');
            }
        }

        function toggleLandingUserMenu(e) {
            if (e) e.stopPropagation();
            const btn = document.getElementById('landingUserMenuBtn');
            const menu = document.getElementById('landingUserDropdown');
            if (!btn || !menu) return;

            const isOpen = menu.classList.contains('show');
            if (isOpen) {
                menu.classList.remove('show');
                btn.setAttribute('aria-expanded', 'false');
            } else {
                menu.classList.add('show');
                btn.setAttribute('aria-expanded', 'true');
            }
        }

        // Close dropdown when clicking outside or pressing Escape
        document.addEventListener('click', function(e) {
            const wrap = document.getElementById('landingUserMenuWrap');
            const menu = document.getElementById('landingUserDropdown');
            const btn = document.getElementById('landingUserMenuBtn');
            if (menu && menu.classList.contains('show') && wrap && !wrap.contains(e.target)) {
                menu.classList.remove('show');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const menu = document.getElementById('landingUserDropdown');
                const btn = document.getElementById('landingUserMenuBtn');
                if (menu && menu.classList.contains('show')) {
                    menu.classList.remove('show');
                    if (btn) btn.setAttribute('aria-expanded', 'false');
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
