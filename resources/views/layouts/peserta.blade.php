<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal Peserta') - MagKom Kominfo</title>

    <!-- Google Fonts: Open Sans (Design System DS-MagKom Bab 2.2) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

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
            --radius-default: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-full: 9999px;
            --sidebar-width: 256px;
            --topbar-height: 64px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: var(--font-sans);
        }

        body {
            background-color: var(--neutral-150);
            color: var(--neutral-800);
            min-height: 100vh;
            display: flex;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--blue-700);
            color: var(--neutral-0);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 50;
            transition: transform 0.25s ease-in-out;
        }

        .sidebar-header {
            padding: 24px 20px 20px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }

        .brand-box {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #ffffff;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: #ffffff;
            color: var(--blue-700);
            border-radius: var(--radius-default);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
        }

        .brand-text h2 {
            font-size: 16px;
            font-weight: 700;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }

        .brand-text span {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.75);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .sidebar-nav {
            padding: 20px 16px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
            overflow-y: auto;
        }

        .nav-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: rgba(255, 255, 255, 0.6);
            padding: 12px 12px 6px 12px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: var(--radius-default);
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.15s ease;
        }

        .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }

        .nav-item.active {
            background-color: #ffffff;
            color: var(--blue-700);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .nav-item.active svg {
            color: var(--blue-700);
        }

        .nav-item svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            color: rgba(255, 255, 255, 0.8);
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }

        .logout-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-default);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .logout-btn:hover {
            background: rgba(220, 38, 38, 0.8);
            border-color: rgba(220, 38, 38, 0.8);
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
            font-size: 18px;
            font-weight: 700;
            color: var(--neutral-800);
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
            font-size: 13px;
            font-weight: 700;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--neutral-800);
            line-height: 1.2;
        }

        .user-badge {
            font-size: 11px;
            color: var(--blue-600);
            font-weight: 600;
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
            font-size: 15px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .dropdown-header-meta {
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .dropdown-header-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--neutral-800);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dropdown-header-email {
            font-size: 11px;
            color: var(--neutral-600);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dropdown-header-role {
            font-size: 10px;
            font-weight: 700;
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
            font-size: 13px;
            font-weight: 600;
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

        /* Flash Messages */
        .alert {
            padding: 14px 18px;
            border-radius: var(--radius-default);
            margin-bottom: 24px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-success {
            background: var(--green-100);
            border: 1px solid var(--green-600);
            color: var(--green-700);
        }

        .alert-error {
            background: var(--red-100);
            border: 1px solid var(--red-600);
            color: var(--red-700);
        }

        .alert-warning {
            background: var(--amber-100);
            border: 1px solid var(--amber-300);
            color: var(--amber-700);
        }

        .alert-info {
            background: var(--blue-tint);
            border: 1px solid var(--blue-tint-line);
            color: var(--blue-700);
        }

        .alert svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            margin-top: 1px;
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
            font-size: 18px;
            font-weight: 700;
            color: var(--neutral-800);
        }

        .card-subtitle {
            font-size: 13px;
            color: var(--neutral-600);
            margin-top: 4px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            height: 44px;
            padding: 0 20px;
            border-radius: var(--radius-default);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
            border: none;
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
            font-size: 12px;
            font-weight: 600;
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
            <a href="{{ route('peserta.profil.show') }}" class="brand-box">
                <div class="brand-icon">M</div>
                <div class="brand-text">
                    <h2>MagKom</h2>
                    <span>Portal Peserta</span>
                </div>
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Menu Utama</div>

            <a href="{{ route('peserta.profil.show') }}" class="nav-item {{ request()->routeIs('peserta.profil.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Profil & Biodata
            </a>

            <a href="{{ route('peserta.pendaftaran.index') }}" class="nav-item {{ request()->routeIs('peserta.pendaftaran.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Pendaftaran Magang
            </a>

            <div class="nav-label">Pelaksanaan Magang</div>

            <a href="{{ route('peserta.logbook.index') }}" class="nav-item {{ request()->routeIs('peserta.logbook.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                Logbook Harian
            </a>

            <a href="{{ route('peserta.tugas.index') }}" class="nav-item {{ request()->routeIs('peserta.tugas.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                Tugas & Progres
            </a>

            <a href="{{ route('peserta.nilai.show') }}" class="nav-item {{ request()->routeIs('peserta.nilai.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
                Lembar Nilai
            </a>

            <div class="nav-label">Tautan Luar</div>

            <a href="{{ route('landing') }}" class="nav-item" target="_blank">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                Lihat Kuota Landing ↗
            </a>
        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Keluar Akun
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="mobile-menu-btn" onclick="toggleSidebar()" aria-label="Buka Menu">
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
                            aria-label="Menu Profil Pengguna"
                            onclick="toggleProfileMenu(event)">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name ?? 'P', 0, 1)) }}
                        </div>
                        <div class="user-info">
                            <span class="user-name">{{ auth()->user()->nama ?? auth()->user()->name }}</span>
                            <span class="user-badge">Peserta Magang</span>
                        </div>
                        <svg class="dropdown-chevron" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div class="profile-dropdown-menu" id="userProfileMenu" role="menu" aria-labelledby="userProfileBtn">
                        <div class="dropdown-header">
                            <div class="dropdown-header-avatar">
                                {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name ?? 'P', 0, 1)) }}
                            </div>
                            <div class="dropdown-header-meta">
                                <span class="dropdown-header-name">{{ auth()->user()->nama ?? auth()->user()->name }}</span>
                                <span class="dropdown-header-email">{{ auth()->user()->email }}</span>
                                <span class="dropdown-header-role">Peserta Magang</span>
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        <a href="{{ route('peserta.profil.show') }}" class="dropdown-item" role="menuitem">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Biodata & Profil</span>
                        </a>

                        <a href="{{ route('peserta.pendaftaran.index') }}" class="dropdown-item" role="menuitem">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Riwayat Pendaftaran</span>
                        </a>

                        <a href="{{ route('password.ganti') }}" class="dropdown-item" role="menuitem">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                            </svg>
                            <span>Ganti Kata Sandi</span>
                        </a>

                        <div class="dropdown-divider"></div>

                        <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="dropdown-item dropdown-logout" role="menuitem">
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Keluar Akun</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="content-area">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error" role="alert">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning" role="alert">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>{{ session('warning') }}</div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
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
    </script>
    @stack('scripts')
</body>
</html>
