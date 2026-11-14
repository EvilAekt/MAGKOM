<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Magang Kominfo Sleman - Informasi Kuota & Pendaftaran')</title>
    <meta name="description" content="@yield('meta_description', 'Transparansi kuota pendaftaran magang Dinas Komunikasi dan Informatika untuk siswa SMK dan mahasiswa D3/S1.')">
    
    <!-- Favicon Resmi Sleman / MagKom -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Preload Aset Logo Prioritas Tinggi (Cegah Flicker / Kedipan saat Navigasi) -->
    <link rel="preload" as="image" href="{{ asset('images/logo-sleman.png') }}" fetchpriority="high">

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
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

    <!-- Vite Asset Pipeline (PRD-TASK-VITE-01) -->
    @vite(['resources/css/app.css', 'resources/css/layouts/app-layout-tokens.css', 'resources/js/app.js'])

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

    @stack('styles')
</head>
<body>
    <!-- Header Navbar (Figma node 562-54980) -->
    <header class="header-nav">
        <div class="container">
            <div class="nav-inner">
                <!-- Brand: MagKom MAGANG KOMINFO -->
                <a href="{{ route('landing') }}" class="brand-link" id="brand-link" title="Magang Kominfo - Dinas Kominfo Sleman">
                    @include('partials.logo-sleman', ['class' => 'brand-logo-img', 'width' => '42', 'height' => '42'])
                    <div class="brand-text">
                        <h1>MagKom</h1>
                        <p>MAGANG KOMINFO</p>
                    </div>
                </a>

                <!-- Nav Menu -->
                <nav class="nav-menu">
                    <a href="{{ route('landing') }}" class="nav-link active">Beranda</a>
                    <a href="{{ route('landing') }}#kuota" class="nav-link">Bidang & kuota</a>
                    <a href="{{ route('panduan-alur') }}" class="nav-link">Alur magang</a>
                    <a href="{{ route('landing') }}#faq" class="nav-link">FAQ</a>
                    <a href="{{ route('landing') }}#kontak" class="nav-link">Hubungi kami</a>
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
                        <a href="{{ route('landing') }}#kuota" class="btn btn-primary">Daftar magang</a>
                    @endauth
                </div>

                <!-- Mobile Toggle -->
                <button type="button" 
                        class="mobile-toggle" 
                        id="appMobileToggle"
                        data-action="toggle-app-drawer" 
                        aria-controls="mobile-drawer"
                        aria-expanded="false" 
                        aria-label="Buka Menu">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Backdrop Scrim for App Layout Mobile Drawer -->
        <div class="mobile-drawer-backdrop" id="appDrawerBackdrop" data-action="close-app-drawer" aria-hidden="true"></div>

        <!-- Mobile Drawer -->
        <div class="mobile-drawer" id="mobile-drawer" aria-label="Navigasi Mobile" aria-hidden="true">
            <div class="mobile-drawer-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--gray-200);">
                <span style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-700);">Menu Navigasi</span>
                <button type="button" class="btn-drawer-close" data-action="close-app-drawer" aria-label="Tutup Menu" style="background: none; border: none; font-size: 1.5rem; line-height: 1; color: var(--neutral-600); cursor: pointer; padding: 4px 8px; border-radius: 6px;">&times;</button>
            </div>
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
                    <a href="{{ $mobileDashUrl }}" class="btn btn-primary" data-action="close-app-drawer" style="padding: 0.5rem; font-size: 0.8125rem; justify-content: center;">Dashboard</a>
                    <a href="{{ $mobileProfilUrl }}" class="btn btn-outline" data-action="close-app-drawer" style="padding: 0.5rem; font-size: 0.8125rem; justify-content: center;">Profil</a>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1rem;">
                    <a href="{{ $mobileSettingUrl }}" class="btn btn-outline" data-action="close-app-drawer" style="padding: 0.5rem; font-size: 0.8125rem; justify-content: center;">Pengaturan</a>
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0; width: 100%;">
                        @csrf
                        <button type="submit" class="btn btn-outline" style="width: 100%; padding: 0.5rem; font-size: 0.8125rem; justify-content: center; color: #DC2626; border-color: rgba(220, 38, 38, 0.3);">Keluar</button>
                    </form>
                </div>
                <div style="height: 1px; background: var(--neutral-200); margin-bottom: 0.75rem;"></div>
            @endauth

            <a href="{{ route('landing') }}" class="nav-link" data-action="close-app-drawer">Beranda</a>
            <a href="{{ route('landing') }}#kuota" class="nav-link" data-action="close-app-drawer">Bidang & kuota</a>
            <a href="{{ route('panduan-alur') }}" class="nav-link" data-action="close-app-drawer">Alur magang</a>
            <a href="{{ route('landing') }}#faq" class="nav-link" data-action="close-app-drawer">FAQ</a>
            <a href="{{ route('landing') }}#kontak" class="nav-link" data-action="close-app-drawer">Hubungi kami</a>
            @guest
                <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
                    <a href="{{ route('login') }}" class="btn btn-outline" data-action="close-app-drawer" style="flex: 1; text-align: center;">Masuk</a>
                    <a href="{{ route('landing') }}#kuota" class="btn btn-primary" data-action="close-app-drawer" style="flex: 1; text-align: center;">Daftar magang</a>
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
