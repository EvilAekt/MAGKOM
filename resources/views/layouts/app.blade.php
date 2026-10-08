<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal MagKom - Magang Dinas Komunikasi dan Informatika')</title>
    <meta name="description" content="@yield('meta_description', 'Portal resmi pendaftaran dan manajemen magang Dinas Komunikasi dan Informatika (Kominfo). Cek ketersediaan kuota jurusan SMK dan Mahasiswa secara transparan.')">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="@yield('og_title', 'Portal MagKom - Magang Kominfo')">
    <meta property="og:description" content="@yield('og_description', 'Sistem pendaftaran dan transparansi kuota magang Dinas Komunikasi dan Informatika.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="id_ID">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-900: #0b1a30;
            --primary-800: #102a4e;
            --primary-700: #1a3d6d;
            --primary-600: #1f4e89;
            --primary-500: #2563eb;
            --accent-cyan: #06b6d4;
            --accent-teal: #0d9488;
            --success-500: #10b981;
            --warning-500: #f59e0b;
            --danger-500: #ef4444;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-900: #0f172a;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: var(--gray-900);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
        }

        .header-nav {
            background: rgba(11, 26, 48, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.25rem;
        }

        .nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 4.5rem;
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #ffffff;
        }

        .brand-badge {
            background: linear-gradient(135deg, #2563eb, #06b6d4);
            color: white;
            font-weight: 800;
            font-size: 1.1rem;
            padding: 0.35rem 0.75rem;
            border-radius: 0.5rem;
            letter-spacing: -0.025em;
        }

        .brand-text h1 {
            font-size: 1.125rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.2;
        }

        .brand-text p {
            font-size: 0.75rem;
            color: #94a3b8;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .nav-menu {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .nav-link {
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.15s ease;
        }

        .nav-link:hover {
            color: #ffffff;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.6rem 1.25rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #ffffff;
        }

        .main-content {
            flex: 1;
        }

        .footer {
            background: #0b1a30;
            color: #94a3b8;
            padding: 3rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.875rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 2.5rem;
            margin-bottom: 2rem;
        }

        .footer-bottom {
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            text-align: center;
            font-size: 0.8125rem;
        }

        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            .nav-menu {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <header class="header-nav">
        <div class="container">
            <div class="nav-inner">
                <a href="{{ route('landing') }}" class="brand-link" id="brand-link">
                    <span class="brand-badge">MagKom</span>
                    <div class="brand-text">
                        <h1>Dinas Kominfo</h1>
                        <p>Portal Manajemen Magang</p>
                    </div>
                </a>

                <nav class="nav-menu">
                    <a href="#kuota-section" class="nav-link">Ketersediaan Kuota</a>
                    <a href="#alur-section" class="nav-link">Alur Pendaftaran</a>
                    <a href="#persyaratan-section" class="nav-link">Persyaratan</a>
                    <a href="{{ url('/login') }}" class="btn btn-outline">Masuk</a>
                    <a href="#kuota-section" class="btn btn-primary">Daftar Sekarang</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="main-content">
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <h2 style="color: #ffffff; font-size: 1.125rem; font-weight: 700; margin-bottom: 0.75rem;">Portal MagKom</h2>
                    <p style="line-height: 1.6; max-width: 480px;">
                        Sistem Informasi Manajemen Magang Terintegrasi Dinas Komunikasi dan Informatika. Menyediakan transparansi kuota, pembagian tugas berbasis kompetensi, dan pelaporan logbook harian resmi.
                    </p>
                </div>
                <div>
                    <h3 style="color: #ffffff; font-size: 0.95rem; font-weight: 600; margin-bottom: 0.75rem;">Navigasi</h3>
                    <ul style="list-style: none; line-height: 2;">
                        <li><a href="#kuota-section" style="color: #94a3b8; text-decoration: none;">Cek Kuota</a></li>
                        <li><a href="#alur-section" style="color: #94a3b8; text-decoration: none;">Alur Seleksi</a></li>
                        <li><a href="#persyaratan-section" style="color: #94a3b8; text-decoration: none;">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
                <div>
                    <h3 style="color: #ffffff; font-size: 0.95rem; font-weight: 600; margin-bottom: 0.75rem;">Kontak Resmi</h3>
                    <p style="line-height: 1.6;">
                        Dinas Komunikasi dan Informatika<br>
                        Email: magang@kominfo.go.id<br>
                        Senin - Jumat (08:00 - 16:00 WIB)
                    </p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Dinas Komunikasi dan Informatika. Hak Cipta Dilindungi Undang-Undang.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
