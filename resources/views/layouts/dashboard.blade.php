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
            overflow-y: auto;
            border-right: 1px solid rgba(255, 255, 255, 0.12);
        }

        .sidebar-header {
            padding: 1.5rem 1.25rem 1.25rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            display: flex;
            align-items: center;
            gap: 0.75rem;
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
            padding: 1.25rem 0.875rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .nav-section-label {
            font-size: 0.6875rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.75rem 0.75rem 0.375rem 0.75rem;
        }

        .nav-item-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            min-height: 44px;
            padding: 0.625rem 0.75rem;
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

        /* Sidebar Footer / System Badge */
        .sidebar-footer {
            padding: 1.25rem 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(0, 0, 0, 0.08);
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

        .app-alert {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            font-size: 0.875rem;
            line-height: 1.5;
            position: relative;
        }

        .alert-icon {
            flex-shrink: 0;
            margin-top: 2px;
        }

        .alert-body {
            flex: 1;
            min-width: 0;
        }

        .alert-title {
            font-weight: 700;
            margin-bottom: 0.125rem;
        }

        .alert-close-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            color: inherit;
            opacity: 0.75;
            margin-left: 0.5rem;
        }

        .alert-close-btn:hover {
            opacity: 1;
        }

        .app-alert.alert-success {
            background-color: var(--green-100);
            border: 1px solid var(--green-600);
            color: var(--green-700);
        }

        .app-alert.alert-error {
            background-color: var(--red-200);
            border: 1px solid var(--red-600);
            color: var(--red-700);
        }

        .app-alert.alert-warning {
            background-color: var(--amber-100);
            border: 1px solid var(--amber-300);
            color: var(--amber-700);
        }

        .app-alert.alert-info {
            background-color: var(--neutral-200);
            border: 1px solid var(--neutral-400);
            color: var(--neutral-800);
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
    </style>

    @yield('styles')
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
                        <span class="sidebar-brand-title">Portal MagKom</span>
                        <span class="sidebar-brand-subtitle">Dinas Kominfo Sleman</span>
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

                    @elseif($userRole === 'pembimbing')
                        <!-- NAVIGASI PEMBIMBING (PRD 9.2) -->
                        <span class="nav-section-label">Bimbingan Magang</span>

                        <a href="{{ route('pembimbing.tugas.index') }}" class="nav-item-link {{ request()->routeIs('pembimbing.tugas.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 11l3 3L22 4"></path>
                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                </svg>
                            </span>
                            <span>Tugas Peserta</span>
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

                        <a href="{{ route('pembimbing.penilaian.index') }}" class="nav-item-link {{ request()->routeIs('pembimbing.penilaian.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="7"></circle>
                                    <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                                </svg>
                            </span>
                            <span>Penilaian Magang</span>
                        </a>

                    @elseif($userRole === 'admin')
                        <!-- NAVIGASI ADMINISTRATOR (PRD 9.2) -->
                        <span class="nav-section-label">Administrasi Magang</span>

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

                        <a href="{{ route('admin.laporan.pendaftaran') }}" class="nav-item-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
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

                    @elseif($userRole === 'superadmin')
                        <!-- NAVIGASI SUPERADMIN (PRD 9.2) -->
                        <span class="nav-section-label">Konfigurasi Periode</span>

                        <a href="{{ route('superadmin.bulan.index') }}" class="nav-item-link {{ request()->routeIs('superadmin.bulan.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                            </span>
                            <span>Bulan Magang</span>
                        </a>

                        <a href="{{ route('superadmin.slot-khusus.index') }}" class="nav-item-link {{ request()->routeIs('superadmin.slot-khusus.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                    <polyline points="9 12 11 14 15 10"></polyline>
                                </svg>
                            </span>
                            <span>Slot Khusus</span>
                        </a>

                        <span class="nav-section-label">Master & Keamanan</span>

                        <a href="{{ route('superadmin.staf.index') }}" class="nav-item-link {{ request()->routeIs('superadmin.staf.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="8.5" cy="7" r="4"></circle>
                                    <line x1="20" y1="8" x2="20" y2="14"></line>
                                    <line x1="23" y1="11" x2="17" y2="11"></line>
                                </svg>
                            </span>
                            <span>Manajemen Staf</span>
                        </a>

                        <a href="{{ route('superadmin.jurusan.index') }}" class="nav-item-link {{ request()->routeIs('superadmin.jurusan.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 21h18M3 7v14M21 7v14M6 7V3h12v4M9 11h2M13 11h2M9 15h2M13 15h2"></path>
                                </svg>
                            </span>
                            <span>Master Jurusan</span>
                        </a>

                        <a href="{{ route('superadmin.bidang.index') }}" class="nav-item-link {{ request()->routeIs('superadmin.bidang.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect>
                                    <line x1="9" y1="22" x2="9" y2="2"></line>
                                    <line x1="15" y1="12" x2="15" y2="12.01"></line>
                                    <line x1="15" y1="16" x2="15" y2="16.01"></line>
                                </svg>
                            </span>
                            <span>Bidang Dinas</span>
                        </a>

                        <a href="{{ route('superadmin.rubrik.index') }}" class="nav-item-link {{ request()->routeIs('superadmin.rubrik.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                            </span>
                            <span>Rubrik Nilai</span>
                        </a>

                        <a href="{{ route('superadmin.pengaturan.index') }}" class="nav-item-link {{ request()->routeIs('superadmin.pengaturan.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                            </span>
                            <span>Pengaturan Sistem</span>
                        </a>

                        <a href="{{ route('superadmin.audit-log.index') }}" class="nav-item-link {{ request()->routeIs('superadmin.audit-log.*') ? 'active' : '' }}">
                            <span class="nav-item-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </span>
                            <span>Jejak Audit Log</span>
                        </a>
                    @endif
                </nav>
            </div>

            <!-- Footer Sidebar: Indikator Sistem -->
            <div class="sidebar-footer">
                <div class="sidebar-status-box">
                    <span class="status-dot"></span>
                    <div class="status-text-wrap">
                        <span class="status-system-title">Sistem Aktif</span>
                        <span class="status-system-desc">Server Kominfo Sleman</span>
                    </div>
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
                        <div class="user-avatar">
                            {{ strtoupper(substr($currentUser?->nama ?? 'U', 0, 1)) }}
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
                    @if(session('success'))
                        <div class="app-alert alert-success" role="alert">
                            <svg class="alert-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                            <div class="alert-body">
                                <div class="alert-title">Berhasil</div>
                                <div>{{ session('success') }}</div>
                            </div>
                            <button type="button" class="alert-close-btn" onclick="this.closest('.app-alert').remove()" aria-label="Tutup pesan">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="app-alert alert-error" role="alert">
                            <svg class="alert-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>
                            <div class="alert-body">
                                <div class="alert-title">Perhatian</div>
                                <div>{{ session('error') }}</div>
                            </div>
                            <button type="button" class="alert-close-btn" onclick="this.closest('.app-alert').remove()" aria-label="Tutup pesan">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="app-alert alert-warning" role="alert">
                            <svg class="alert-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            <div class="alert-body">
                                <div class="alert-title">Peringatan</div>
                                <div>{{ session('warning') }}</div>
                            </div>
                            <button type="button" class="alert-close-btn" onclick="this.closest('.app-alert').remove()" aria-label="Tutup pesan">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                    @endif

                    @if(isset($errors) && $errors->any())
                        <div class="app-alert alert-error" role="alert">
                            <svg class="alert-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <div class="alert-body">
                                <div class="alert-title">Terdapat Kesalahan Pengisian:</div>
                                <ul style="margin: 0.25rem 0 0 1.25rem; padding: 0;">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <button type="button" class="alert-close-btn" onclick="this.closest('.app-alert').remove()" aria-label="Tutup pesan">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            </button>
                        </div>
                    @endif
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
    </script>

    @yield('scripts')
</body>
</html>
