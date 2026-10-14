<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard - Portal MagKom Sleman')</title>
    <meta name="description" content="@yield('meta_description', 'Dashboard Resmi Portal Magang Dinas Komunikasi dan Informatika Kabupaten Sleman.')">

    <!-- Google Fonts: Open Sans (Design System DS-MagKom Bab 2.2) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400;1,600&display=swap" rel="stylesheet">

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
            --blue-500: #2EA3F2;
            --blue-tint: #E6EEFF;
            --blue-tint-line: #B3C8FF;
            --sky-100: #E0F2FE;

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

            /* Status Tokens */
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

            /* Focus & Elevation */
            --focus-ring: 0 0 0 3px rgba(7, 81, 255, 0.25);
            --shadow-subtle: 0 1px 3px rgba(15, 23, 42, 0.08);
            --shadow-raised: 0 4px 12px rgba(15, 23, 42, 0.10);
            --shadow-modal: 0 24px 48px -8px rgba(15, 28, 51, 0.28);
            --sidebar-width: 256px;
            --topbar-height: 64px;

            --font-sans: 'Open Sans', Arial, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Dark Mode Tokens & Overrides (DS-MagKom) */
        [data-theme="dark"] {
            --neutral-0: #1E293B;
            --neutral-100: #0F172A;
            --neutral-150: #0B1120;
            --neutral-200: #334155;
            --neutral-400: #475569;
            --neutral-500: #94A3B8;
            --neutral-600: #CBD5E1;
            --neutral-700: #E2E8F0;
            --neutral-800: #F8FAFC;
            --neutral-900: #FFFFFF;

            --blue-tint: #1E3A8A;
            --blue-tint-line: #3B82F6;
            --sky-100: #1E293B;
        }

        [data-theme="dark"] body {
            background-color: var(--neutral-150);
            color: var(--neutral-700);
        }

        [data-theme="dark"] .dashboard-topbar {
            background-color: var(--neutral-0);
            border-bottom-color: var(--neutral-200);
        }

        [data-theme="dark"] .user-name-text {
            color: var(--neutral-800);
        }

        [data-theme="dark"] .user-dropdown-menu {
            background-color: var(--neutral-0);
            border-color: var(--neutral-200);
        }

        [data-theme="dark"] .dropdown-item-link {
            color: var(--neutral-700);
        }

        [data-theme="dark"] .dropdown-item-link:hover {
            background-color: var(--neutral-100);
            color: #FFFFFF;
        }

        [data-theme="dark"] .dropdown-divider {
            border-top-color: var(--neutral-200);
        }

        [data-theme="dark"] .breadcrumb-trail a {
            color: var(--neutral-500);
        }

        [data-theme="dark"] .breadcrumb-current {
            color: var(--neutral-800);
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--neutral-150);
            color: var(--neutral-800);
            min-height: 100vh;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* Layout Grid Container */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
            width: 100%;
            position: relative;
        }

        /* =====================================================================
           SIDEBAR STYLING (Blue 700 #0641CC, 256px)
           ===================================================================== */
        .dashboard-sidebar {
            width: var(--sidebar-width);
            background-color: var(--blue-700);
            color: #FFFFFF;
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

        .dashboard-sidebar::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }

        .sidebar-top-container {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-height: 0;
            overflow: hidden;
        }

        .sidebar-header {
            padding: 1.25rem 1.25rem 1rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-shrink: 0;
        }

        .sidebar-brand-emblem {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            flex-shrink: 0;
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .sidebar-brand-title {
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: -0.01em;
            color: #FFFFFF;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-brand-subtitle {
            font-size: 0.6875rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.75);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        /* Sidebar Navigation Items */
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

        .nav-section-label {
            font-size: 0.6875rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.625rem 0.75rem 0.25rem 0.75rem;
        }

        .nav-item-link {
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

        .nav-item-link:hover {
            background-color: rgba(255, 255, 255, 0.12);
            color: #FFFFFF;
        }

        .nav-item-link:focus-visible {
            box-shadow: 0 0 0 2px #FFFFFF, 0 0 0 4px var(--blue-600);
        }

        /* Active Navigation Item (Figma: Surface Neutral 0, Text Blue 700) */
        .nav-item-link.active {
            background-color: var(--neutral-0);
            color: var(--blue-700);
            box-shadow: var(--shadow-subtle);
        }

        .nav-item-link.active svg {
            color: var(--blue-700);
            stroke-width: 2.25;
        }

        .nav-item-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            color: rgba(255, 255, 255, 0.85);
        }

        /* Collapsible Sub-menu Navigation (Figma Node 560-24892) */
        .nav-group-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            min-height: 40px;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            border: none;
            background: transparent;
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.875rem;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            text-align: left;
            outline: none;
        }

        .nav-group-btn:hover {
            background-color: rgba(255, 255, 255, 0.12);
            color: #FFFFFF;
        }

        .nav-group-btn.has-active {
            color: #FFFFFF;
            background-color: rgba(255, 255, 255, 0.08);
        }

        .nav-group-btn-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .nav-group-chevron {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            color: rgba(255, 255, 255, 0.6);
            transform-origin: center;
        }

        .nav-group-btn[aria-expanded="true"] .nav-group-chevron {
            transform: rotate(90deg);
            color: #FFFFFF;
        }

        .nav-sub-menu {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
            padding-left: 0.5rem;
            margin-top: 0;
            margin-bottom: 0;
            border-left: 2px solid rgba(255, 255, 255, 0.2);
            margin-left: 1.5rem;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transition: max-height 0.25s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease, margin 0.2s ease, visibility 0.2s;
        }

        .nav-sub-menu.show {
            max-height: 500px;
            opacity: 1;
            visibility: visible;
            margin-top: 0.125rem;
            margin-bottom: 0.375rem;
        }

        .nav-sub-item {
            display: flex;
            align-items: center;
            min-height: 36px;
            padding: 0.375rem 0.625rem;
            border-radius: 6px;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.8125rem;
            font-weight: 500;
            transition: all 0.15s ease;
        }

        .nav-sub-item:hover {
            color: #FFFFFF;
            background-color: rgba(255, 255, 255, 0.12);
        }

        .nav-sub-item.active {
            background-color: var(--neutral-0);
            color: var(--blue-700);
            font-weight: 700;
        }

        /* Pulse green dot status */
        @keyframes pulseDot {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
        .status-dot-pulse {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #22C55E;
            animation: pulseDot 2s infinite;
            flex-shrink: 0;
            display: inline-block;
        }

        /* Sidebar Footer / System Badge */
        .sidebar-footer {
            padding: 1rem 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(0, 0, 0, 0.08);
            flex-shrink: 0;
        }

        .sidebar-status-box {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            padding: 0.625rem 0.75rem;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #22C55E;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.25);
            flex-shrink: 0;
        }

        .status-text-wrap {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .status-system-title {
            font-size: 0.75rem;
            font-weight: 700;
            color: #FFFFFF;
            line-height: 1.2;
        }

        .status-system-desc {
            font-size: 0.6875rem;
            color: rgba(255, 255, 255, 0.7);
        }

        /* =====================================================================
           MAIN WRAPPER & TOPBAR
           ===================================================================== */
        .dashboard-main {
            flex: 1;
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - var(--sidebar-width));
        }

        .dashboard-topbar {
            height: var(--topbar-height);
            background-color: var(--neutral-0);
            border-bottom: 1px solid var(--neutral-400);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
            min-width: 0;
        }

        .mobile-toggle-btn {
            display: none;
            width: 44px;
            height: 44px;
            border: 1px solid var(--neutral-400);
            background: var(--neutral-0);
            border-radius: 8px;
            color: var(--neutral-800);
            align-items: center;
            justify-content: center;
            cursor: pointer;
            outline: none;
        }

        .mobile-toggle-btn:focus-visible {
            box-shadow: var(--focus-ring);
        }

        /* Breadcrumb Navigation */
        .breadcrumb-trail {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8125rem;
            color: var(--neutral-600);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .breadcrumb-trail a {
            color: var(--neutral-600);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.15s;
        }

        .breadcrumb-trail a:hover {
            color: var(--blue-600);
            text-decoration: underline;
        }

        .breadcrumb-separator {
            color: var(--neutral-400);
            font-weight: 400;
        }

        .breadcrumb-current {
            color: var(--neutral-800);
            font-weight: 700;
        }

        /* Topbar Right (User Info & Menu) */
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
            position: relative;
        }

        .user-menu-trigger {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.375rem 0.5rem;
            border-radius: 8px;
            border: 1px solid transparent;
            background: transparent;
            cursor: pointer;
            text-align: left;
            transition: all 0.15s ease-in-out;
            min-height: 44px;
            outline: none;
        }

        .user-menu-trigger:hover {
            background-color: var(--neutral-100);
            border-color: var(--neutral-200);
        }

        .user-menu-trigger:focus-visible {
            box-shadow: var(--focus-ring);
            border-color: var(--blue-600);
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background-color: var(--blue-tint);
            border: 1px solid var(--blue-tint-line);
            color: var(--blue-700);
            font-weight: 700;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .user-meta-wrap {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .user-name-text {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--neutral-800);
            line-height: 1.25;
            max-width: 160px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Role Badges (Design System Bab 3.3) */
        .role-chip-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.125rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.6875rem;
            font-weight: 600;
            line-height: 1.2;
            width: fit-content;
        }

        .role-chip-badge.superadmin,
        .role-chip-badge.admin,
        .role-chip-badge.pembimbing {
            background-color: var(--blue-tint);
            color: var(--blue-700);
            border: 1px solid var(--blue-tint-line);
        }

        .role-chip-badge.peserta {
            background-color: var(--neutral-200);
            color: var(--neutral-600);
            border: 1px solid var(--neutral-400);
        }

        .user-chevron-icon {
            color: var(--neutral-500);
            transition: transform 0.2s ease;
        }

        /* Dropdown Menu Overlay (220px, Floating Elevation) */
        .user-dropdown-menu {
            position: absolute;
            top: calc(100% + 0.5rem);
            right: 0;
            width: 220px;
            background-color: var(--neutral-0);
            border: 1px solid var(--neutral-400);
            border-radius: 12px;
            box-shadow: var(--shadow-raised);
            padding: 0.5rem;
            display: none;
            flex-direction: column;
            gap: 0.25rem;
            z-index: 60;
        }

        .user-dropdown-menu.show {
            display: flex;
            animation: dropdownFadeIn 0.15s ease-out;
        }

        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dropdown-item-link,
        .dropdown-item-btn {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            min-height: 40px;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
            text-decoration: none;
            color: var(--neutral-700);
            font-size: 0.8125rem;
            font-weight: 600;
            background: transparent;
            border: none;
            width: 100%;
            cursor: pointer;
            text-align: left;
            transition: all 0.15s;
        }

        .dropdown-item-link:hover,
        .dropdown-item-btn:hover {
            background-color: var(--neutral-100);
            color: var(--blue-600);
        }

        .dropdown-item-btn.logout-action:hover {
            background-color: var(--red-100);
            color: var(--red-700);
        }

        .dropdown-divider {
            height: 1px;
            background-color: var(--neutral-200);
            margin: 0.25rem 0;
        }

        /* =====================================================================
           CONTENT WORKSPACE AREA
           ===================================================================== */
        .dashboard-content-area {
            flex: 1;
            padding: 2rem 2.5rem;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .dashboard-content-area {
                padding: 1.25rem 1rem;
            }
        }

        /* Page Header Block */
        .page-header-row {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1.75rem;
        }

        @media (min-width: 768px) {
            .page-header-row {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }

        .page-title-text {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--neutral-800);
            letter-spacing: -0.015em;
            line-height: 1.2;
        }

        .page-desc-text {
            font-size: 0.875rem;
            color: var(--neutral-600);
            margin-top: 0.25rem;
        }

        .page-actions-wrap {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        /* =====================================================================
           GLOBAL FLASH ALERTS & STATUS BANNERS (Design System Bab 3.4)
           ===================================================================== */
        .alerts-container {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        /* ========================================================
           MODERN TOAST NOTIFICATION COMPONENT (Floating Top-Right)
           ======================================================== */
        .alerts-container {
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

        .alerts-container .app-alert,
        .alerts-container .app-toast {
            pointer-events: auto;
            margin-bottom: 0;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), 0 8px 10px -6px rgba(0, 0, 0, 0.06);
            animation: toastSlideInRight 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .alerts-container .app-alert.toast-hiding,
        .alerts-container .app-toast.toast-hiding {
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

        .app-alert,
        .app-toast {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 18px;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            font-size: 0.875rem;
            line-height: 1.5;
            position: relative;
            transition: all 0.25s ease;
        }

        .alert-icon-badge,
        .toast-icon-badge {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .alert-icon-badge.success,
        .toast-icon-badge.success {
            background: #ECFDF5;
            color: #10B981;
        }

        .alert-icon-badge.error,
        .toast-icon-badge.error {
            background: #FEF2F2;
            color: #EF4444;
        }

        .alert-icon-badge.warning,
        .toast-icon-badge.warning {
            background: #FFFBEB;
            color: #F59E0B;
        }

        .alert-icon-badge.info,
        .toast-icon-badge.info {
            background: #EFF6FF;
            color: #3B82F6;
        }

        .alert-body,
        .toast-content {
            flex: 1;
            min-width: 0;
            padding-top: 1px;
        }

        .alert-title,
        .toast-title {
            font-size: 0.9375rem;
            font-weight: 700;
            color: #0F172A;
            line-height: 1.35;
            margin-bottom: 2px;
        }

        .alert-desc,
        .toast-desc {
            font-size: 0.84375rem;
            font-weight: 400;
            color: #64748B;
            line-height: 1.45;
        }

        .alert-desc ul,
        .toast-desc ul {
            margin: 4px 0 0 16px;
            padding: 0;
        }

        .alert-close-btn,
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

        .alert-close-btn:hover,
        .toast-close-btn:hover {
            color: #475569;
            background: #F1F5F9;
        }

        /* Dark Theme Support for Alerts */
        [data-theme="dark"] .app-alert,
        [data-theme="dark"] .app-toast {
            background: #182234 !important;
            border-color: #26354D !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3) !important;
        }

        [data-theme="dark"] .alert-title,
        [data-theme="dark"] .toast-title {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .alert-desc,
        [data-theme="dark"] .toast-desc {
            color: #94A3B8 !important;
        }

        [data-theme="dark"] .alert-icon-badge.success,
        [data-theme="dark"] .toast-icon-badge.success {
            background: rgba(16, 185, 129, 0.18) !important;
            color: #34D399 !important;
        }

        [data-theme="dark"] .alert-icon-badge.error,
        [data-theme="dark"] .toast-icon-badge.error {
            background: rgba(239, 68, 68, 0.18) !important;
            color: #F87171 !important;
        }

        [data-theme="dark"] .alert-icon-badge.warning,
        [data-theme="dark"] .toast-icon-badge.warning {
            background: rgba(245, 158, 11, 0.18) !important;
            color: #FBBF24 !important;
        }

        [data-theme="dark"] .alert-icon-badge.info,
        [data-theme="dark"] .toast-icon-badge.info {
            background: rgba(59, 130, 246, 0.18) !important;
            color: #60A5FA !important;
        }

        [data-theme="dark"] .alert-close-btn,
        [data-theme="dark"] .toast-close-btn {
            color: #64748B !important;
        }

        [data-theme="dark"] .alert-close-btn:hover,
        [data-theme="dark"] .toast-close-btn:hover {
            color: #CBD5E1 !important;
            background: #1E293B !important;
        }

        /* Banner Masa Tenggang Peserta (PRD AC-12-2) */
        .grace-period-banner {
            background-color: var(--sky-100);
            border: 1px solid var(--blue-tint-line);
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.75rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        @media (min-width: 768px) {
            .grace-period-banner {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }

        .grace-banner-left {
            display: flex;
            align-items: flex-start;
            gap: 0.875rem;
        }

        .grace-banner-title {
            font-size: 0.9375rem;
            font-weight: 700;
            color: var(--blue-700);
        }

        .grace-banner-desc {
            font-size: 0.8125rem;
            color: var(--neutral-700);
            margin-top: 0.125rem;
        }

        .grace-banner-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: var(--blue-600);
            color: #FFFFFF;
            padding: 0.625rem 1.125rem;
            border-radius: 8px;
            font-size: 0.8125rem;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.15s;
            white-space: nowrap;
            min-height: 44px;
        }

        .grace-banner-btn:hover {
            background-color: var(--blue-700);
        }

        /* Backdrop Scrim untuk Mobile Drawer */
        .mobile-drawer-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(29, 37, 48, 0.5);
            z-index: 45;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .mobile-drawer-backdrop.active {
            display: block;
            opacity: 1;
        }

        /* =====================================================================
           RESPONSIVE BREAKPOINTS (Mobile < 1024px)
           ===================================================================== */
        @media (max-width: 1023.98px) {
            .dashboard-sidebar {
                transform: translateX(-100%);
                box-shadow: var(--shadow-modal);
            }

            .dashboard-sidebar.drawer-open {
                transform: translateX(0);
            }

            .dashboard-main {
                margin-left: 0;
                width: 100%;
            }

            .mobile-toggle-btn {
                display: inline-flex;
            }
        }

        /* =====================================================================
           DASHBOARD UNIFIED TAB CONTROLLER & QUICK ACTIONS
           ===================================================================== */
        .dashboard-tab-bar {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: #F1F5F9;
            padding: 0.375rem;
            border-radius: 10px;
            border: 1px solid var(--neutral-300);
            overflow-x: auto;
            scrollbar-width: none;
        }

        .dashboard-tab-bar::-webkit-scrollbar {
            display: none;
        }

        .tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--neutral-600);
            background: transparent;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            white-space: nowrap;
            outline: none;
        }

        .tab-btn:hover {
            color: var(--neutral-900);
            background: rgba(255, 255, 255, 0.6);
        }

        .tab-btn.active {
            color: var(--blue-700);
            background: #FFFFFF;
            font-weight: 700;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .tab-btn .tab-badge {
            font-size: 0.6875rem;
            font-weight: 700;
            padding: 0.1rem 0.45rem;
            border-radius: 999px;
            background: #E2E8F0;
            color: var(--neutral-700);
        }

        .tab-btn.active .tab-badge {
            background: #DBEAFE;
            color: #1D4ED8;
        }

        .tab-content-panel {
            display: none;
            animation: fadeInTab 0.2s ease-in-out;
        }

        .tab-content-panel.active {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        @keyframes fadeInTab {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .quick-action-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.45rem 0.75rem;
            background: var(--neutral-0);
            border: 1px solid var(--neutral-400);
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--neutral-800);
            text-decoration: none;
            transition: all 0.15s ease-in-out;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }

        .quick-action-chip:hover {
            background: #F8FAFC;
            border-color: var(--blue-600);
            color: var(--blue-600);
            transform: translateY(-1px);
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border-width: 0;
        }
    </style>

    @yield('styles')
    @stack('styles')
</head>
<body>
    @php
        /** @var \App\Models\User $currentUser */
        $currentUser = auth()->user();
        $userRole = $currentUser?->role?->value ?? 'peserta';

        // Pengecekan masa tenggang khusus Peserta (PRD AC-12-2)
        $penempatanGracePeriod = null;
        $graceDaysRemaining = null;
        if ($currentUser && $currentUser->isPeserta()) {
            $penempatanGracePeriod = $currentUser->penempatanPeserta()
                ->where('status', \App\Enums\StatusPenempatanEnum::Selesai)
                ->where('tgl_nonaktif', '>=', now()->toDateString())
                ->first();

            if ($penempatanGracePeriod && $penempatanGracePeriod->tgl_nonaktif) {
                $graceDaysRemaining = (int) now()->startOfDay()->diffInDays($penempatanGracePeriod->tgl_nonaktif, false);
            }
        }
    @endphp

    <div class="dashboard-container">
        <!-- Backdrop Scrim Drawer Mobile -->
        <div id="sidebarBackdrop" class="mobile-drawer-backdrop" onclick="toggleSidebar(false)"></div>

        <!-- =====================================================================
             SIDEBAR NAVIGASI
             ===================================================================== -->
        <aside id="mainSidebar" class="dashboard-sidebar" aria-label="Navigasi Utama">
            <div class="sidebar-top-container">
                <!-- Header Brand Sleman -->
                <div class="sidebar-header">
                    <div class="sidebar-brand-emblem">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <div class="sidebar-brand-text">
                        <span class="sidebar-brand-title">MagKom</span>
                        <span class="sidebar-brand-subtitle">KOMINFO PORTAL</span>
                        <span class="sr-only">Portal MagKom - Dinas Kominfo Sleman</span>
                    </div>
                </div>

                <!-- Menu Item Dinamis Berdasarkan Peran -->
                <nav class="sidebar-nav">
                    @if($userRole === 'peserta')
                        <!-- NAVIGASI PESERTA (PRD 9.2) -->
                        <span class="nav-section-label">Area Peserta</span>

                        <a href="{{ route('peserta.profil.show') }}" class="nav-item-link {{ request()->routeIs('peserta.profil.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </span>
                            <span>Profil Saya</span>
                        </a>

                        <a href="{{ route('peserta.pendaftaran.index') }}" class="nav-item-link {{ request()->routeIs('peserta.pendaftaran.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                                </svg>
                            </span>
                            <span>Pendaftaran Magang</span>
                        </a>

                        @if(auth()->user()?->hasPenempatanMagang())
                            <a href="{{ route('peserta.logbook.index') }}" class="nav-item-link {{ request()->routeIs('peserta.logbook.*') ? 'active' : '' }}">
                                <span class="nav-item-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                    </svg>
                                </span>
                                <span>Logbook Harian</span>
                            </a>

                            <a href="{{ route('peserta.tugas.index') }}" class="nav-item-link {{ request()->routeIs('peserta.tugas.*') ? 'active' : '' }}">
                                <span class="nav-item-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 11l3 3L22 4"></path>
                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                    </svg>
                                </span>
                                <span>Tugas & Progres</span>
                            </a>

                            <a href="{{ route('peserta.nilai.show') }}" class="nav-item-link {{ request()->routeIs('peserta.nilai.*') ? 'active' : '' }}">
                                <span class="nav-item-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="8" r="7"></circle>
                                        <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                                    </svg>
                                </span>
                                <span>Lembar Nilai</span>
                            </a>
                        @else
                            <div class="nav-item-link locked" style="opacity: 0.6; cursor: not-allowed;" title="Terkunci: Fitur tersedia setelah permohonan magang disetujui & ditempatkan">
                                <span class="nav-item-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                    </svg>
                                </span>
                                <span>Logbook Harian</span>
                            </div>

                            <div class="nav-item-link locked" style="opacity: 0.6; cursor: not-allowed;" title="Terkunci: Fitur tersedia setelah permohonan magang disetujui & ditempatkan">
                                <span class="nav-item-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 11l3 3L22 4"></path>
                                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                    </svg>
                                </span>
                                <span>Tugas & Progres</span>
                            </div>

                            <div class="nav-item-link locked" style="opacity: 0.6; cursor: not-allowed;" title="Terkunci: Fitur tersedia setelah permohonan magang disetujui & ditempatkan">
                                <span class="nav-item-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="8" r="7"></circle>
                                        <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                                    </svg>
                                </span>
                                <span>Lembar Nilai</span>
                            </div>
                        @endif

                    @elseif($userRole === 'pembimbing')
                        <!-- NAVIGASI PEMBIMBING (PRD 9.2 & FIGMA 560-41611) -->
                        <span class="nav-section-label">Bimbingan Magang</span>

                        <a href="{{ route('pembimbing.dashboard') }}" class="nav-item-link {{ request()->routeIs('pembimbing.dashboard') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                            </span>
                            <span>Dashboard</span>
                        </a>

                        <a href="{{ route('pembimbing.peserta.index') }}" class="nav-item-link {{ request()->routeIs('pembimbing.peserta.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </span>
                            <span>Peserta Bimbingan</span>
                        </a>

                        <a href="{{ route('pembimbing.tugas.index') }}" class="nav-item-link {{ request()->routeIs('pembimbing.tugas.index', 'pembimbing.tugas.show', 'pembimbing.tugas.create', 'pembimbing.tugas.edit') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 11l3 3L22 4"></path>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                </svg>
                            </span>
                            <span>Tugas Peserta</span>
                        </a>

                        <a href="{{ route('pembimbing.tugas.template.index') }}" class="nav-item-link {{ request()->routeIs('pembimbing.tugas.template.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                    <line x1="8" y1="21" x2="16" y2="21"></line>
                                    <line x1="12" y1="17" x2="12" y2="21"></line>
                                </svg>
                            </span>
                            <span>Bank Template</span>
                        </a>

                        <a href="{{ route('pembimbing.logbook.index') }}" class="nav-item-link {{ request()->routeIs('pembimbing.logbook.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                                </svg>
                            </span>
                            <span>Reviu Logbook</span>
                        </a>

                        <a href="{{ route('pembimbing.agenda.index') }}" class="nav-item-link {{ request()->routeIs('pembimbing.agenda.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </span>
                            <span>Agenda Mentoring</span>
                        </a>

                        <a href="{{ route('pembimbing.penilaian.index') }}" class="nav-item-link {{ request()->routeIs('pembimbing.penilaian.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="7"></circle>
                                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                                </svg>
                            </span>
                            <span>Penilaian Magang</span>
                        </a>

                        <a href="{{ route('pembimbing.laporan-kinerja.index') }}" class="nav-item-link {{ request()->routeIs('pembimbing.laporan-kinerja.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </span>
                            <span>Laporan Kinerja ASN</span>
                        </a>

                        <span class="nav-section-label">PENGATURAN</span>

                        <a href="{{ route('pembimbing.profil.show') }}" class="nav-item-link {{ request()->routeIs('pembimbing.profil.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </span>
                            <span>Profil Saya</span>
                        </a>

                        <a href="{{ route('pembimbing.pengaturan.index') }}" class="nav-item-link {{ request()->routeIs('pembimbing.pengaturan.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                            </span>
                            <span>Pengaturan Akun</span>
                        </a>

                    @elseif($userRole === 'admin')
                        <!-- NAVIGASI ADMINISTRATOR (PRD 9.2) -->
                        <span class="nav-section-label">Administrasi Magang</span>

                        <a href="{{ route('admin.dashboard') }}" class="nav-item-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                            </span>
                            <span>Dashboard Admin</span>
                        </a>

                        <a href="{{ route('admin.pendaftaran.index') }}" class="nav-item-link {{ request()->routeIs('admin.pendaftaran.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <circle cx="10" cy="13" r="2"></circle>
                                    <path d="m20 17-1.09-1.09a2 2 0 0 0-2.82 0L10 22"></path>
                                </svg>
                            </span>
                            <span>Verifikasi Berkas</span>
                        </a>

                        <a href="{{ route('admin.kuota.index') }}" class="nav-item-link {{ request()->routeIs('admin.kuota.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="3" y1="9" x2="21" y2="9"></line>
                                    <line x1="9" y1="21" x2="9" y2="9"></line>
                                </svg>
                            </span>
                            <span>Manajemen Kuota</span>
                        </a>

                        <a href="{{ route('admin.waiting-list.index') }}" class="nav-item-link {{ request()->routeIs('admin.waiting-list.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                            </span>
                            <span>Waiting List</span>
                        </a>

                        <a href="{{ route('admin.penempatan.index') }}" class="nav-item-link {{ request()->routeIs('admin.penempatan.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </span>
                            <span>Penempatan</span>
                        </a>

                        <a href="{{ route('admin.laporan.index') }}" class="nav-item-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </span>
                            <span>Laporan & Ekspor</span>
                        </a>

                        @if(\App\Support\Pengaturan::bool('fitur_sertifikat_aktif', true))
                        <a href="{{ route('admin.sertifikat.index') }}" class="nav-item-link {{ request()->routeIs('admin.sertifikat.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="7"></circle>
                                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                                </svg>
                            </span>
                            <span>Sertifikat</span>
                        </a>
                        @endif

                        @if(\App\Support\Pengaturan::bool('fitur_survei_aktif', true))
                        <a href="{{ route('admin.survei.index') }}" class="nav-item-link {{ request()->routeIs('admin.survei.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                            </span>
                            <span>Survei Kepuasan</span>
                        </a>
                        @endif

                        <span class="nav-section-label">PENGATURAN</span>

                        <a href="{{ route('admin.profil.show') }}" class="nav-item-link {{ request()->routeIs('admin.profil.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </span>
                            <span>Profil Saya</span>
                        </a>

                        <a href="{{ route('admin.pengaturan.index') }}" class="nav-item-link {{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                            </span>
                            <span>Pengaturan Akun</span>
                        </a>

                    @elseif($userRole === 'superadmin')
                        <!-- NAVIGASI SUPERADMIN (Figma Node 560-24892 & PRD 9.2) -->
                        <a href="{{ route('superadmin.dashboard') }}" class="nav-item-link {{ request()->routeIs('superadmin.dashboard') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                            </span>
                            <span>Dashboard</span>
                        </a>

                        <!-- GRUP 1: MODUL OPERASIONAL -->
                        <span class="nav-section-label">MODUL OPERASIONAL</span>

                        @php
                            $isOperasionalAdminActive = request()->routeIs('admin.pendaftaran.*') || request()->routeIs('admin.kuota.*') || request()->routeIs('admin.waiting-list.*') || request()->routeIs('admin.penempatan.*') || request()->routeIs('admin.laporan.*') || request()->routeIs('admin.sertifikat.*') || request()->routeIs('admin.survei.*');
                        @endphp
                        <div class="nav-group-container">
                            <button type="button" class="nav-group-btn {{ $isOperasionalAdminActive ? 'has-active' : '' }}" onclick="toggleNavGroup(this)" aria-expanded="{{ $isOperasionalAdminActive ? 'true' : 'false' }}">
                                <span class="nav-group-btn-left">
                                    <span class="nav-item-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                                            <line x1="9" y1="22" x2="9" y2="2"></line>
                                            <line x1="15" y1="12" x2="15" y2="12.01"></line>
                                            <line x1="15" y1="16" x2="15" y2="16.01"></line>
                                        </svg>
                                    </span>
                                    <span>Administrasi</span>
                                </span>
                                <svg class="nav-group-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </button>
                            <div class="nav-sub-menu {{ $isOperasionalAdminActive ? 'show' : '' }}">
                                <a href="{{ route('admin.pendaftaran.index') }}" class="nav-sub-item {{ request()->routeIs('admin.pendaftaran.*') ? 'active' : '' }}">Verifikasi Berkas</a>
                                <a href="{{ route('admin.kuota.index') }}" class="nav-sub-item {{ request()->routeIs('admin.kuota.*') ? 'active' : '' }}">Kuota Magang</a>
                                <a href="{{ route('admin.waiting-list.index') }}" class="nav-sub-item {{ request()->routeIs('admin.waiting-list.*') ? 'active' : '' }}">Waiting List</a>
                                <a href="{{ route('admin.penempatan.index') }}" class="nav-sub-item {{ request()->routeIs('admin.penempatan.*') ? 'active' : '' }}">Penempatan</a>
                                <a href="{{ route('admin.laporan.index') }}" class="nav-sub-item {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">Laporan & Ekspor</a>
                                @if(\App\Support\Pengaturan::bool('fitur_sertifikat_aktif', true))
                                <a href="{{ route('admin.sertifikat.index') }}" class="nav-sub-item {{ request()->routeIs('admin.sertifikat.*') ? 'active' : '' }}">Sertifikat</a>
                                @endif
                                @if(\App\Support\Pengaturan::bool('fitur_survei_aktif', true))
                                <a href="{{ route('admin.survei.index') }}" class="nav-sub-item {{ request()->routeIs('admin.survei.*') ? 'active' : '' }}">Survei Kepuasan</a>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('pembimbing.penilaian.index') }}" class="nav-item-link {{ request()->routeIs('pembimbing.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                                    <path d="M9 14l2 2 4-4"></path>
                                </svg>
                            </span>
                            <span>Pembimbing</span>
                        </a>

                        @php
                            $isOperasionalPesertaActive = request()->routeIs('superadmin.monitoring.*') || request()->routeIs('superadmin.arsip.*');
                        @endphp
                        <div class="nav-group-container">
                            <button type="button" class="nav-group-btn {{ $isOperasionalPesertaActive ? 'has-active' : '' }}" onclick="toggleNavGroup(this)" aria-expanded="{{ $isOperasionalPesertaActive ? 'true' : 'false' }}">
                                <span class="nav-group-btn-left">
                                    <span class="nav-item-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                    </span>
                                    <span>Peserta</span>
                                </span>
                                <svg class="nav-group-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </button>
                            <div class="nav-sub-menu {{ $isOperasionalPesertaActive ? 'show' : '' }}">
                                <a href="{{ route('superadmin.monitoring.index') }}" class="nav-sub-item {{ request()->routeIs('superadmin.monitoring.*') ? 'active' : '' }}">Monitoring Peserta</a>
                                <a href="{{ route('superadmin.arsip.index') }}" class="nav-sub-item {{ request()->routeIs('superadmin.arsip.*') ? 'active' : '' }}">Arsip Peserta</a>
                            </div>
                        </div>

                        <!-- GRUP 2: SISTEM -->
                        <span class="nav-section-label">SISTEM</span>

                        <a href="{{ route('superadmin.staf.index') }}" class="nav-item-link {{ request()->routeIs('superadmin.staf.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="8.5" cy="7" r="4"></circle>
                                    <line x1="20" y1="8" x2="20" y2="14"></line>
                                    <line x1="23" y1="11" x2="17" y2="11"></line>
                                </svg>
                            </span>
                            <span>Pengguna<span class="sr-only"> / Manajemen Staf</span></span>
                        </a>

                        <a href="{{ route('superadmin.role.index') }}" class="nav-item-link {{ request()->routeIs('superadmin.role.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                    <polyline points="9 12 11 14 15 10"></polyline>
                                </svg>
                            </span>
                            <span>Role</span>
                        </a>

                        <a href="{{ route('superadmin.rubrik.index') }}" class="nav-item-link {{ request()->routeIs('superadmin.rubrik.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                            </span>
                            <span>Rubrik</span>
                            <span class="sr-only">Rubrik Nilai</span>
                        </a>

                        <a href="{{ route('superadmin.audit-log.index') }}" class="nav-item-link {{ request()->routeIs('superadmin.audit-log.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </span>
                            <span>Audit Log</span>
                            <span class="sr-only">Jejak Audit Log</span>
                        </a>

                        @php
                            $isConfigActive = request()->routeIs('superadmin.pengaturan.*') || request()->routeIs('superadmin.bulan.*') || request()->routeIs('superadmin.slot-khusus.*') || request()->routeIs('superadmin.jurusan.*') || request()->routeIs('superadmin.bidang.*');
                        @endphp
                        <div class="nav-group-container">
                            <button type="button" class="nav-group-btn {{ $isConfigActive ? 'has-active' : '' }}" onclick="toggleNavGroup(this)" aria-expanded="{{ $isConfigActive ? 'true' : 'false' }}">
                                <span class="nav-group-btn-left">
                                    <span class="nav-item-icon">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="3"></circle>
                                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                        </svg>
                                    </span>
                                    <span>Konfigurasi</span>
                                </span>
                                <svg class="nav-group-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </button>
                            <div class="nav-sub-menu {{ $isConfigActive ? 'show' : '' }}">
                                <a href="{{ route('superadmin.pengaturan.index') }}" class="nav-sub-item {{ request()->routeIs('superadmin.pengaturan.*') ? 'active' : '' }}">Pengaturan Sistem</a>
                                <a href="{{ route('superadmin.bulan.index') }}" class="nav-sub-item {{ request()->routeIs('superadmin.bulan.*') ? 'active' : '' }}">Bulan Magang</a>
                                <a href="{{ route('superadmin.slot-khusus.index') }}" class="nav-sub-item {{ request()->routeIs('superadmin.slot-khusus.*') ? 'active' : '' }}">Slot Khusus</a>
                                <a href="{{ route('superadmin.jurusan.index') }}" class="nav-sub-item {{ request()->routeIs('superadmin.jurusan.*') ? 'active' : '' }}">Master Jurusan</a>
                                <a href="{{ route('superadmin.bidang.index') }}" class="nav-sub-item {{ request()->routeIs('superadmin.bidang.*') ? 'active' : '' }}">Bidang Dinas</a>
                            </div>
                        </div>

                        <!-- GRUP 3: AKUN & PROFIL PRIBADI -->
                        <span class="nav-section-label">AKUN PRIBADI</span>

                        <a href="{{ route('superadmin.profil.show') }}" class="nav-item-link {{ request()->routeIs('superadmin.profil.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </span>
                            <span>Profil Saya</span>
                        </a>

                        <a href="{{ route('superadmin.akun.index') }}" class="nav-item-link {{ request()->routeIs('superadmin.akun.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                            </span>
                            <span>Akun & Preferensi</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Footer Sidebar: Indikator Sistem Internal Kominfo (Figma Node 560-24892) -->
            <div class="sidebar-footer">
                <div class="sidebar-status-box" style="padding: 0.5rem 0.75rem; display: flex; align-items: center; gap: 0.625rem; background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 8px;">
                    <span class="status-dot-pulse"></span>
                    <span style="font-size: 0.75rem; font-weight: 500; color: rgba(255, 255, 255, 0.85); letter-spacing: 0.01em;">Sistem internal Kominfo</span>
                </div>
            </div>
        </aside>

        <!-- =====================================================================
             AREA KERJA UTAMA & TOPBAR
             ===================================================================== -->
        <div class="dashboard-main">
            <!-- Topbar Header -->
            <header class="dashboard-topbar">
                <div class="topbar-left">
                    <!-- Tombol Hamburger Mobile Drawer (44x44 target sentuh) -->
                    <button type="button" class="mobile-toggle-btn" onclick="toggleSidebar(true)" aria-label="Buka navigasi menu">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"></line>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <line x1="3" y1="18" x2="21" y2="18"></line>
                        </svg>
                    </button>

                    <!-- Breadcrumb Trail -->
                    <nav class="breadcrumb-trail" aria-label="Breadcrumb">
                        <a href="{{ route('landing') }}">Beranda</a>
                        <span class="breadcrumb-separator">/</span>
                        @hasSection('breadcrumbs')
                            @yield('breadcrumbs')
                        @else
                            <span class="breadcrumb-current">Dashboard</span>
                        @endif
                    </nav>
                </div>

                <!-- Topbar Menu Pengguna -->
                <div class="topbar-right">
                    <button type="button" id="userMenuBtn" class="user-menu-trigger" onclick="toggleUserMenu()" aria-expanded="false" aria-haspopup="true">
                        <div class="user-avatar" style="overflow: hidden; padding: 0;">
                            @if($currentUser?->foto_url)
                                <img src="{{ $currentUser->foto_url }}" alt="{{ $currentUser->nama }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                {{ strtoupper(substr($currentUser?->nama ?? 'U', 0, 1)) }}
                            @endif
                        </div>
                        <div class="user-meta-wrap">
                            <span class="user-name-text">{{ $currentUser?->nama ?? 'Pengguna' }}</span>
                            <span class="role-chip-badge {{ $userRole }}">
                                {{ ucfirst($userRole) }}
                            </span>
                        </div>
                        <svg class="user-chevron-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    <!-- Dropdown Menu Akun -->
                    <div id="userDropdown" class="user-dropdown-menu" role="menu" aria-labelledby="userMenuBtn">
                        @if($userRole === 'peserta')
                            <a href="{{ route('peserta.profil.show') }}" class="dropdown-item-link" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span>Profil Saya</span>
                            </a>
                            <a href="{{ route('peserta.pengaturan.index') }}" class="dropdown-item-link" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                                <span>Pengaturan Akun</span>
                            </a>
                        @elseif($userRole === 'admin')
                            <a href="{{ route('admin.profil.show') }}" class="dropdown-item-link" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span>Profil Saya</span>
                            </a>
                            <a href="{{ route('admin.pengaturan.index') }}" class="dropdown-item-link" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                                <span>Pengaturan Akun</span>
                            </a>
                        @elseif($userRole === 'pembimbing')
                            <a href="{{ route('pembimbing.profil.show') }}" class="dropdown-item-link" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span>Profil Saya</span>
                            </a>
                            <a href="{{ route('pembimbing.pengaturan.index') }}" class="dropdown-item-link" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                                <span>Pengaturan Akun</span>
                            </a>
                        @elseif($userRole === 'superadmin')
                            <a href="{{ route('superadmin.profil.show') }}" class="dropdown-item-link" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span>Profil Saya</span>
                            </a>
                            <a href="{{ route('superadmin.akun.index') }}" class="dropdown-item-link" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                                <span>Akun & Preferensi</span>
                            </a>
                        @endif

                        <a href="{{ route('landing') }}" class="dropdown-item-link" role="menuitem">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                <polyline points="9 22 9 12 15 12 15 22"></polyline>
                            </svg>
                            <span>Ke Beranda Publik</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <!-- Form Logout Resmi POST -->
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="dropdown-item-btn logout-action" role="menuitem">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span>Keluar (Logout)</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Container Konten Dinamis -->
            <main class="dashboard-content-area" id="mainContent">
                <!-- =====================================================================
                     GLOBAL FLASH ALERTS
                     ===================================================================== -->
                <div class="alerts-container">
                    <x-flash-alert />
                </div>

                <!-- =====================================================================
                     BANNER MASA TENGGANG PESERTA (PRD AC-12-2)
                     ===================================================================== -->
                @if($penempatanGracePeriod)
                    <div class="grace-period-banner" role="region" aria-label="Status Masa Tenggang Magang">
                        <div class="grace-banner-left">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--blue-700); flex-shrink: 0; margin-top: 2px;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            <div>
                                <div class="grace-banner-title">Program Magang Telah Selesai (Masa Tenggang)</div>
                                <div class="grace-banner-desc">
                                    Periode magang Anda telah berakhir. Akun berstatus hanya-lihat dan memiliki sisa 
                                    <strong>{{ max(0, $graceDaysRemaining ?? 0) }} hari</strong> sebelum dinonaktifkan permanen oleh sistem.
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('peserta.nilai.unduh') }}" class="grace-banner-btn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            <span>Unduh Lembar Nilai</span>
                        </a>
                    </div>
                @endif

                <!-- Page Header (Judul Halaman & Tombol Aksi) -->
                @hasSection('page_title')
                    <div class="page-header-row">
                        <div>
                            <h1 class="page-title-text">@yield('page_title')</h1>
                            @hasSection('page_description')
                                <p class="page-desc-text">@yield('page_description')</p>
                            @endif
                        </div>
                        @hasSection('page_actions')
                            <div class="page-actions-wrap">
                                @yield('page_actions')
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Slot Konten Utama Halaman -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Script Interaktivitas Ringan (Vanilla JS) -->
    <script>
        // Toggle Sidebar Mobile Drawer
        function toggleSidebar(forceState) {
            const sidebar = document.getElementById('mainSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (!sidebar || !backdrop) return;

            const shouldOpen = typeof forceState === 'boolean' 
                ? forceState 
                : !sidebar.classList.contains('drawer-open');

            if (shouldOpen) {
                sidebar.classList.add('drawer-open');
                backdrop.classList.add('active');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.remove('drawer-open');
                backdrop.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        // Toggle Accordion Sub-Menu Sidebar (Figma Node 560-24892)
        function toggleNavGroup(btn) {
            if (!btn) return;
            const isExpanded = btn.getAttribute('aria-expanded') === 'true';
            const subMenu = btn.nextElementSibling;
            if (subMenu) {
                if (isExpanded) {
                    btn.setAttribute('aria-expanded', 'false');
                    subMenu.classList.remove('show');
                } else {
                    btn.setAttribute('aria-expanded', 'true');
                    subMenu.classList.add('show');
                }
            }
        }

        // Toggle User Profile Dropdown
        function toggleUserMenu() {
            const menu = document.getElementById('userDropdown');
            const btn = document.getElementById('userMenuBtn');
            if (!menu || !btn) return;

            const isExpanded = menu.classList.contains('show');
            if (isExpanded) {
                menu.classList.remove('show');
                btn.setAttribute('aria-expanded', 'false');
            } else {
                menu.classList.add('show');
                btn.setAttribute('aria-expanded', 'true');
            }
        }

        // Event Listener: Klik Luar & Tombol Escape
        document.addEventListener('click', function(e) {
            const menu = document.getElementById('userDropdown');
            const btn = document.getElementById('userMenuBtn');
            if (menu && btn && !btn.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.remove('show');
                btn.setAttribute('aria-expanded', 'false');
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                toggleSidebar(false);
                const menu = document.getElementById('userDropdown');
                const btn = document.getElementById('userMenuBtn');
                if (menu && btn) {
                    menu.classList.remove('show');
                    btn.setAttribute('aria-expanded', 'false');
                }
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

        if (window.matchMedia) {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
                const currentPref = document.documentElement.getAttribute('data-pref-theme') || localStorage.getItem('magkom_theme');
                if (currentPref === 'sistem') {
                    document.documentElement.setAttribute('data-theme', e.matches ? 'dark' : 'light');
                }
            });
        }

        // Floating Toast / Alert Dismiss Logic
        function dismissToast(btn) {
            const toast = btn.closest('.app-alert') || btn.closest('.app-toast');
            if (!toast) return;
            toast.classList.add('toast-hiding');
            setTimeout(() => toast.remove(), 300);
        }

        // Global MagKomToast helper API for JS triggers
        window.MagKomToast = {
            show({ type = 'success', title = '', message = '', duration = 5000 }) {
                let container = document.querySelector('.alerts-container');
                if (!container) {
                    container = document.createElement('div');
                    container.className = 'alerts-container';
                    const mainContent = document.getElementById('mainContent') || document.body;
                    mainContent.insertBefore(container, mainContent.firstChild);
                }
                const toast = document.createElement('div');
                toast.className = `app-toast app-alert alert-${type === 'error' ? 'error alert-danger' : type}`;
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
                    <div class="toast-icon-badge alert-icon-badge ${type}">${iconSvg}</div>
                    <div class="toast-content alert-body">
                        ${title ? `<div class="toast-title alert-title">${title}</div>` : ''}
                        <div class="toast-desc alert-desc">${message}</div>
                    </div>
                    <button type="button" class="toast-close-btn alert-close-btn" onclick="dismissToast(this)" aria-label="Tutup notifikasi">
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

        // Auto dismiss alert toast after 5s with pause on hover
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alerts-container .app-alert, .alerts-container .app-toast');
            alerts.forEach(alert => {
                let timer = setTimeout(() => {
                    alert.classList.add('toast-hiding');
                    setTimeout(() => alert.remove(), 300);
                }, 5000);

                alert.addEventListener('mouseenter', () => clearTimeout(timer));
                alert.addEventListener('mouseleave', () => {
                    timer = setTimeout(() => {
                        alert.classList.add('toast-hiding');
                        setTimeout(() => alert.remove(), 300);
                    }, 3000);
                });
            });
        });
    </script>

    @yield('scripts')
    @stack('scripts')
    <x-sse-toast-listener />
</body>
</html>
