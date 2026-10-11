<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
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

        .brand-logo-icon {
            width: 2.65rem;
            height: 2.65rem;
            background: var(--blue-600);
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 800;
            font-size: 1.35rem;
            box-shadow: 0 2px 6px rgba(7, 81, 255, 0.25);
            flex-shrink: 0;
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
                    <div class="brand-logo-icon">M</div>
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
                            $dashboardUrl = match($user->role->value ?? '') {
                                'superadmin' => route('superadmin.dashboard'),
                                'admin' => route('admin.dashboard'),
                                'pembimbing' => route('pembimbing.dashboard'),
                                default => route('peserta.pendaftaran.index'),
                            };
                        @endphp
                        <a href="{{ $dashboardUrl }}" class="nav-action-link">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline" style="padding: 0.45rem 0.85rem; font-size: 0.825rem;">Keluar</button>
                        </form>
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
            <a href="{{ route('landing') }}" class="nav-link" onclick="toggleMobileDrawer()">Beranda</a>
            <a href="{{ route('landing') }}#kuota-section" class="nav-link" onclick="toggleMobileDrawer()">Bidang & kuota</a>
            <a href="{{ route('landing') }}#alur-section" class="nav-link" onclick="toggleMobileDrawer()">Alur magang</a>
            <a href="{{ route('landing') }}#pengumuman-section" class="nav-link" onclick="toggleMobileDrawer()">Pengumuman</a>
            <a href="#footer" class="nav-link" onclick="toggleMobileDrawer()">Hubungi kami</a>
            <div style="display: flex; gap: 0.75rem; margin-top: 0.5rem;">
                <a href="{{ route('login') }}" class="btn btn-outline" style="flex: 1; text-align: center;">Masuk</a>
                <a href="{{ route('landing') }}#kuota-section" class="btn btn-primary" style="flex: 1; text-align: center;" onclick="toggleMobileDrawer()">Daftar magang</a>
            </div>
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
    </script>
    @stack('scripts')
</body>
</html>
