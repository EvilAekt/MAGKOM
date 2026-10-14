<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal Peserta') - MagKom Kominfo</title>

    <!-- Google Fonts: Open Sans (Design System DS-MagKom Bab 2.2) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">

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

            --font-sans: 'Open Sans', Arial, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            
            /* Tipografi Skala MagKom Resmi (Bab 2.2) */
            --text-2xs: 0.6875rem; /* 11px */
            --text-xs: 0.75rem;    /* 12px */
            --text-sm: 0.8125rem;  /* 13px */
            --text-base: 0.875rem; /* 14px - Standar Body */
            --text-md: 0.9375rem;  /* 15px */
            --text-lg: 1rem;       /* 16px */
            --text-xl: 1.125rem;   /* 18px */
            --text-2xl: 1.25rem;   /* 20px */
            --text-3xl: 1.5rem;    /* 24px */

            --fw-normal: 400;
            --fw-medium: 500;
            --fw-semibold: 600;
            --fw-bold: 700;

            --radius-default: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-full: 9999px;
            --sidebar-width: 256px;
            --topbar-height: 64px;
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

        [data-theme="dark"] .sidebar {
            background-color: #111827;
            border-right-color: #1F2937;
        }

        [data-theme="dark"] .sidebar-header {
            border-bottom-color: #1F2937;
        }

        [data-theme="dark"] .sidebar-footer {
            border-top-color: #1F2937;
        }

        [data-theme="dark"] .topbar {
            background-color: #111827;
            border-bottom-color: #1F2937;
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

        [data-theme="dark"] .nav-item {
            color: #94A3B8;
        }

        [data-theme="dark"] .nav-item:hover {
            background-color: #1F2937;
            color: #F8FAFC;
        }

        [data-theme="dark"] .nav-item.active {
            background-color: rgba(7, 81, 255, 0.2);
            color: #60A5FA;
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

        [data-theme="dark"] .logout-btn {
            background-color: #1F2937;
            border-color: #374151;
            color: #F87171;
        }

        [data-theme="dark"] .logout-btn:hover {
            background-color: #7F1D1D;
            color: #FFFFFF;
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
            font-weight: var(--fw-normal);
            line-height: 1.5;
            background-color: var(--neutral-150);
            color: var(--neutral-800);
            min-height: 100vh;
            display: flex;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        h1, h2, h3, h4, h5, h6 {
            color: var(--neutral-800);
            font-weight: var(--fw-bold);
            line-height: 1.3;
        }

        .dashboard-hero h1, .dashboard-hero h2, .dashboard-hero h3,
        .banner-penempatan h1, .banner-penempatan h2, .banner-penempatan h3,
        .classroom-banner h1, .classroom-banner h2, .classroom-banner h3,
        .profile-hero h1, .profile-hero h2, .profile-hero h3,
        .pendaftaran-hero h1, .pendaftaran-hero h2, .pendaftaran-hero h3 {
            color: #ffffff;
        }

        p {
            line-height: 1.5;
        }

        input, button, select, textarea, optgroup {
            font-family: inherit;
            font-size: inherit;
            line-height: inherit;
        }

        /* Sidebar Styling (Diselaraskan dengan Super Admin / Design System MagKom) */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--blue-700);
            color: var(--neutral-0);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 50;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
            border-right: 1px solid rgba(255, 255, 255, 0.12);
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .sidebar::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }

        .sidebar-header {
            padding: 1.25rem 1.25rem 1rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            display: flex;
            align-items: center;
            gap: 0.75rem;
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

        .brand-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            font-weight: 800;
            font-size: 1.125rem;
            flex-shrink: 0;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .brand-text h2 {
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            color: #FFFFFF;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .brand-text span {
            font-size: 0.6875rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.75);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .sidebar-nav {
            padding: 1rem 0.75rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            overflow-y: auto;
            min-height: 0;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .sidebar-nav::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }

        .nav-label {
            font-size: 0.6875rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.625rem 0.75rem 0.25rem 0.75rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-height: 40px;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.15s ease-in-out;
            outline: none;
        }

        .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.12);
            color: #ffffff;
        }

        .nav-item.active {
            background-color: var(--neutral-0);
            color: var(--blue-700);
            box-shadow: var(--shadow-subtle);
        }

        .nav-item.active svg {
            color: var(--blue-700);
            stroke-width: 2.25;
        }

        .nav-item svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            color: rgba(255, 255, 255, 0.85);
        }

        .nav-item.locked {
            opacity: 0.55;
            cursor: not-allowed;
            background-color: transparent !important;
            color: rgba(255, 255, 255, 0.6) !important;
            user-select: none;
        }

        .nav-item.locked:hover {
            background-color: rgba(255, 255, 255, 0.05) !important;
        }

        .badge-locked {
            margin-left: auto;
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.75);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .nav-item.locked:hover .badge-locked {
            background: rgba(255, 255, 255, 0.22);
            color: #ffffff;
        }

        .sidebar-footer {
            padding: 1rem 0.75rem;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
        }

        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 38px;
            padding: 0.5rem 0.75rem;
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            font-size: 0.8125rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s ease, border-color 0.15s ease;
        }

        .logout-btn:hover {
            background: rgba(220, 38, 38, 0.85);
            border-color: rgba(220, 38, 38, 0.85);
        }

        /* Layout Main Area */
        .main-wrapper {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            height: var(--topbar-height);
            background: var(--neutral-0);
            border-bottom: 1px solid var(--neutral-400);
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

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--neutral-700);
            padding: 8px;
            border-radius: var(--radius-default);
        }

        .mobile-menu-btn:hover {
            background: var(--neutral-200);
        }

        .topbar-title {
            font-size: var(--text-xl);
            font-weight: var(--fw-bold);
            color: var(--neutral-800);
            line-height: 1.3;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        /* Profile Dropdown */
        .profile-dropdown-container {
            position: relative;
        }

        .user-chip-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 12px 4px 6px;
            background: var(--neutral-100);
            border: 1px solid var(--neutral-400);
            border-radius: var(--radius-full);
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: left;
            font-family: inherit;
        }

        .user-chip-btn:hover,
        .user-chip-btn[aria-expanded="true"] {
            background: var(--blue-tint);
            border-color: var(--blue-tint-line);
            box-shadow: 0 2px 6px rgba(7, 81, 255, 0.12);
        }

        .user-chip-btn:focus-visible {
            outline: 2px solid var(--blue-600);
            outline-offset: 2px;
        }

        .dropdown-chevron {
            color: var(--neutral-500);
            transition: transform 0.2s ease;
            margin-left: 2px;
        }

        .user-chip-btn[aria-expanded="true"] .dropdown-chevron {
            transform: rotate(180deg);
            color: var(--blue-700);
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: var(--blue-tint);
            color: var(--blue-700);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--text-sm);
            font-weight: var(--fw-bold);
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: var(--text-base);
            font-weight: var(--fw-semibold);
            color: var(--neutral-800);
            line-height: 1.25;
        }

        .user-badge {
            font-size: var(--text-2xs);
            color: var(--blue-600);
            font-weight: var(--fw-semibold);
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
        .toast-floating-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 420px;
            width: calc(100vw - 48px);
            pointer-events: none;
        }

        .toast-floating-container .app-toast {
            pointer-events: auto;
            margin-bottom: 0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.06);
            animation: toastSlideInRight 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .toast-floating-container .app-toast.toast-hiding {
            animation: toastSlideOutRight 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
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

        /* Common Components */
        .card {
            background: var(--neutral-0);
            border: 1px solid var(--neutral-400);
            border-radius: var(--radius-md);
            padding: 24px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
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
            color: var(--neutral-800);
            line-height: 1.3;
        }

        .card-subtitle {
            font-size: var(--text-sm);
            font-weight: var(--fw-normal);
            color: var(--neutral-600);
            margin-top: 4px;
            line-height: 1.4;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 42px;
            padding: 0 18px;
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
        }

        .btn-primary:hover:not(:disabled) {
            background: var(--blue-700);
        }

        .btn-secondary {
            background: #ffffff;
            color: var(--blue-600);
            border: 1px solid var(--blue-600);
        }

        .btn-secondary:hover:not(:disabled) {
            background: var(--blue-tint);
            border-color: var(--blue-700);
            color: var(--blue-700);
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
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: block;
            }

            .content-area {
                padding: 20px 16px;
            }

            .topbar {
                padding: 0 16px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="drawer-backdrop" id="drawerBackdrop" onclick="toggleSidebar()"></div>

    <!-- Sidebar Dashboard Peserta -->
    <aside class="sidebar" id="sidebarNav">
        <div class="sidebar-header">
            <a href="{{ route('peserta.dashboard') }}" class="brand-box">
                <div class="brand-icon">M</div>
                <div class="brand-text">
                    <h2>MagKom</h2>
                    <span>Portal Peserta</span>
                </div>
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">{{ __('Menu Utama') }}</div>

            <a href="{{ route('peserta.dashboard') }}" class="nav-item {{ request()->routeIs('peserta.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                {{ __('Dashboard') }}
            </a>

            <a href="{{ route('peserta.profil.show') }}" class="nav-item {{ request()->routeIs('peserta.profil.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                {{ __('Profil & Biodata') }}
            </a>

            <a href="{{ route('peserta.pendaftaran.index') }}" class="nav-item {{ request()->routeIs('peserta.pendaftaran.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                {{ __('Pendaftaran Magang') }}
            </a>

            @php
                $isDiterima = auth()->user()?->hasPenempatanMagang();
            @endphp

            <div class="nav-label">{{ __('Pelaksanaan Magang') }}</div>

            @if($isDiterima)
                <a href="{{ route('peserta.logbook.index') }}" class="nav-item {{ request()->routeIs('peserta.logbook.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    {{ __('Logbook Mingguan') }}
                </a>

                <a href="{{ route('peserta.tugas.index') }}" class="nav-item {{ request()->routeIs('peserta.tugas.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    {{ __('Tugas & Progres') }}
                </a>

                <a href="{{ route('peserta.nilai.show') }}" class="nav-item {{ request()->routeIs('peserta.nilai.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                    {{ __('Lembar Nilai') }}
                </a>
            @else
                <div class="nav-item locked" title="{{ __('Terkunci: Fitur tersedia setelah permohonan magang disetujui & ditempatkan') }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span>{{ __('Logbook Mingguan') }}</span>
                    <span class="badge-locked" title="{{ __('Terkunci: Fitur tersedia setelah permohonan magang disetujui & ditempatkan') }}" aria-label="{{ __('Terkunci') }}">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </span>
                </div>

                <div class="nav-item locked" title="{{ __('Terkunci: Fitur tersedia setelah permohonan magang disetujui & ditempatkan') }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <span>{{ __('Tugas & Progres') }}</span>
                    <span class="badge-locked" title="{{ __('Terkunci: Fitur tersedia setelah permohonan magang disetujui & ditempatkan') }}" aria-label="{{ __('Terkunci') }}">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </span>
                </div>

                <div class="nav-item locked" title="{{ __('Terkunci: Fitur tersedia setelah permohonan magang disetujui & ditempatkan') }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                    <span>{{ __('Lembar Nilai') }}</span>
                    <span class="badge-locked" title="{{ __('Terkunci: Fitur tersedia setelah permohonan magang disetujui & ditempatkan') }}" aria-label="{{ __('Terkunci') }}">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </span>
                </div>
            @endif

            <div class="nav-label">{{ __('Tautan Luar') }}</div>

            <a href="{{ route('landing') }}" class="nav-item" target="_blank">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                {{ __('Lihat Kuota Landing ↗') }}
            </a>

            <div class="nav-label">{{ __('Pengaturan') }}</div>

            <a href="{{ route('peserta.pengaturan.index') }}" class="nav-item {{ request()->routeIs('peserta.pengaturan.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ __('Pengaturan Akun') }}
            </a>
        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    {{ __('Keluar Akun') }}
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="mobile-menu-btn" onclick="toggleSidebar()" aria-label="{{ __('Buka Menu') }}">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                            @if(auth()->user()->profil?->foto_url)
                                <img src="{{ auth()->user()->profil->foto_url }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            @else
                                {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name ?? 'P', 0, 1)) }}
                            @endif
                        </div>
                        <div class="user-info">
                            <span class="user-name">{{ auth()->user()->nama ?? auth()->user()->name }}</span>
                            <span class="user-badge">{{ __('Peserta Magang') }}</span>
                        </div>
                        <svg class="dropdown-chevron" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div class="profile-dropdown-menu" id="userProfileMenu" role="menu" aria-labelledby="userProfileBtn">
                        <div class="dropdown-header">
                            <div class="dropdown-header-avatar" style="overflow: hidden;">
                                @if(auth()->user()->profil?->foto_url)
                                    <img src="{{ auth()->user()->profil->foto_url }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                                @else
                                    {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name ?? 'P', 0, 1)) }}
                                @endif
                            </div>
                            <div class="dropdown-header-meta">
                                <span class="dropdown-header-name">{{ auth()->user()->nama ?? auth()->user()->name }}</span>
                                <span class="dropdown-header-email">{{ auth()->user()->email }}</span>
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
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            <span>{{ __('Ganti Kata Sandi') }}</span>
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
            {{-- Floating Flash Messages (Top-Right Pop Up) --}}
            <div class="toast-floating-container" id="toastFloatingContainer" aria-live="polite" aria-atomic="true">
                <x-flash-alert />
            </div>

            @yield('content')
        </main>
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
            show({ type = 'success', title = '', message = '', duration = 5000 }) {
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

                toast.innerHTML = `
                    <div class="toast-icon-badge ${type}">${iconSvg}</div>
                    <div class="toast-content">
                        ${title ? `<div class="toast-title">${title}</div>` : ''}
                        <div class="toast-desc">${message}</div>
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
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebarNav');
            const backdrop = document.getElementById('drawerBackdrop');
            sidebar.classList.toggle('open');
            backdrop.classList.toggle('open');
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
            try {
                localStorage.setItem('magkom_theme', theme);
            } catch (e) {}
        };

        // Listener jika OS tema berubah dan user memilih 'sistem'
        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                const currentPref = document.documentElement.getAttribute('data-pref-theme') || localStorage.getItem('magkom_theme');
                if (currentPref === 'sistem') {
                    document.documentElement.setAttribute('data-theme', e.matches ? 'dark' : 'light');
                }
            });
        }
    </script>
    @stack('scripts')
    <x-sse-toast-listener />
</body>
</html>

