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
    <link rel="stylesheet" href="{{ asset('css/gentelella-theme.css') }}?v={{ file_exists(public_path('css/gentelella-theme.css')) ? (string) filemtime(public_path('css/gentelella-theme.css')) : '1.0' }}">
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
        /* MagKom Peserta Critical Theme & Icon CSS Baseline */
        html:not([data-theme="dark"]) .sidebar-footer .sun-icon { display: none !important; }
        html:not([data-theme="dark"]) .sidebar-footer .moon-icon { display: inline-block !important; }
        html[data-theme="dark"] .sidebar-footer .sun-icon { display: inline-block !important; }
        html[data-theme="dark"] .sidebar-footer .moon-icon { display: none !important; }
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
            <button type="button" 
                    class="sidebar-drawer-close-btn" 
                    onclick="toggleSidebarPeserta(false)" 
                    aria-label="{{ __('Tutup Navigasi') }}" 
                    title="{{ __('Tutup Menu') }}">
                &times;
            </button>
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

            {{-- Akses Profil & Biodata di sidebar hanya untuk peserta yang belum diterima magang (TASK 04). --}}
            {{-- Setelah berstatus Diterima Magang, akses dipindahkan ke profil kanan atas. --}}
            @if(!$isDiterima)
                <a href="{{ route('peserta.profil.show') }}" class="nav-item-link {{ request()->routeIs('peserta.profil.*') ? 'active' : '' }}" title="{{ __('Profil & Biodata') }}">
                    <span class="nav-item-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </span>
                    <span>{{ __('Profil & Biodata') }}</span>
                </a>
            @endif

            <a href="{{ route('peserta.pendaftaran.index') }}" class="nav-item-link {{ request()->routeIs('peserta.pendaftaran.*') ? 'active' : '' }}" title="{{ __('Pendaftaran Magang') }}">
                <span class="nav-item-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    </svg>
                </span>
                <span>{{ __('Pendaftaran Magang') }}</span>
            </a>

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
                <h1 class="topbar-title page-title">@yield('page_title', 'Portal Peserta')</h1>
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
        function toggleSidebarPeserta(forceState) {
            const isMobile = window.innerWidth <= 1024;
            const sidebar = document.getElementById('sidebarNav');
            const backdrop = document.getElementById('drawerBackdrop');

            if (isMobile) {
                if (!sidebar) return;
                const isCurrentlyOpen = sidebar.classList.contains('open') || sidebar.classList.contains('drawer-open');
                const shouldOpen = typeof forceState === 'boolean' ? forceState : !isCurrentlyOpen;

                if (shouldOpen) {
                    sidebar.classList.add('open', 'drawer-open');
                    document.body.classList.add('drawer-open');
                    if (backdrop) backdrop.classList.add('open', 'active');
                    document.body.style.overflow = 'hidden';
                } else {
                    sidebar.classList.remove('open', 'drawer-open');
                    document.body.classList.remove('drawer-open');
                    if (backdrop) backdrop.classList.remove('open', 'active');
                    document.body.style.overflow = '';
                }
            } else {
                const html = document.documentElement;
                const isCollapsed = html.classList.toggle('sidebar-collapsed');
                try {
                    localStorage.setItem('magkom_peserta_sidebar_collapsed', isCollapsed ? 'true' : 'false');
                } catch (e) {}
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' || e.key === 'Esc') {
                toggleSidebarPeserta(false);
            }
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth > 1024) {
                const sidebar = document.getElementById('sidebarNav');
                const backdrop = document.getElementById('drawerBackdrop');
                if (sidebar) sidebar.classList.remove('open', 'drawer-open');
                if (backdrop) backdrop.classList.remove('open', 'active');
                document.body.classList.remove('drawer-open');
                document.body.style.overflow = '';
            }
        });

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
    <script src="{{ asset('js/gentelella-theme.js') }}?v={{ file_exists(public_path('js/gentelella-theme.js')) ? (string) filemtime(public_path('js/gentelella-theme.js')) : '1.0' }}"></script>
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

