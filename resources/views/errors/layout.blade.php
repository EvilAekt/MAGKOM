<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Terjadi Kesalahan') - MagKom Sleman</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Tipografi Resmi Open Sans (Design System Bab 2.2) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Gentelella Theme CSS -->
    <link rel="stylesheet" href="{{ asset('css/gentelella-theme.css') }}">

    <style>
        :root {
            --neutral-0: #FFFFFF;
            --neutral-100: #F8FAFC;
            --neutral-150: #F5F7FB;
            --neutral-200: #EEF0F5;
            --neutral-300: #E2E8F0;
            --neutral-400: #D0D3D9;
            --neutral-500: #7A8494;
            --neutral-600: #64748B;
            --neutral-700: #334155;
            --neutral-800: #1E293B;
            --neutral-900: #0F172A;
            --blue-600: #0751FF;
            --blue-700: #0641CC;
            --blue-tint: #E6EEFF;
            --font-sans: 'Open Sans', Arial, -apple-system, BlinkMacSystemFont, sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--neutral-150);
            color: var(--neutral-800);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .error-card {
            background-color: var(--neutral-0);
            border: 1px solid var(--neutral-300);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            max-width: 540px;
            width: 100%;
            padding: 2.75rem 2.25rem;
            text-align: center;
            position: relative;
        }

        .error-brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.75rem;
            padding: 0.35rem 0.85rem;
            background: #F1F5F9;
            border-radius: 9999px;
            font-size: 0.775rem;
            font-weight: 700;
            color: #334155;
            text-decoration: none;
        }

        .error-icon-box {
            width: 76px;
            height: 76px;
            border-radius: 20px;
            margin: 0 auto 1.5rem auto;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }

        .error-code {
            font-size: 3.25rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            line-height: 1.05;
            margin-bottom: 0.5rem;
        }

        .error-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--neutral-900);
            margin-bottom: 0.75rem;
        }

        .error-desc {
            font-size: 0.9375rem;
            color: var(--neutral-600);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .error-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn-action-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background-color: #0641CC;
            color: #FFFFFF;
            font-size: 0.9375rem;
            font-weight: 700;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(6, 65, 204, 0.25);
            min-height: 44px;
            border: none;
            cursor: pointer;
        }

        .btn-action-primary:hover {
            background-color: #0533A8;
            color: #FFFFFF;
            transform: translateY(-1px);
        }

        .btn-action-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background-color: #F1F5F9;
            color: #334155;
            font-size: 0.9375rem;
            font-weight: 600;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.2s ease;
            min-height: 44px;
            border: 1px solid var(--neutral-300);
            cursor: pointer;
        }

        .btn-action-secondary:hover {
            background-color: #E2E8F0;
            color: #0F172A;
        }

        .footer-brand {
            margin-top: 1.75rem;
            font-size: 0.8125rem;
            color: var(--neutral-600);
            text-align: center;
        }

        /* Dark mode overrides */
        [data-theme="dark"] body {
            background-color: #0B132B !important;
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .error-card {
            background-color: #182234 !important;
            border-color: #26354D !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4) !important;
        }

        [data-theme="dark"] .error-brand-badge {
            background: #0E1726 !important;
            color: #94A3B8 !important;
        }

        [data-theme="dark"] .error-title {
            color: #F8FAFC !important;
        }

        [data-theme="dark"] .error-desc {
            color: #94A3B8 !important;
        }

        [data-theme="dark"] .btn-action-secondary {
            background-color: #0E1726 !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }

        [data-theme="dark"] .footer-brand {
            color: #64748B !important;
        }
    </style>
</head>
<body>

    <div class="error-card">
        <div class="error-brand-badge">
            <img src="{{ asset('images/logo-sleman.png') }}" alt="Logo Sleman" width="16" height="16" style="object-fit: contain;">
            <span>Portal MagKom &bull; Diskominfo Sleman</span>
        </div>

        <div class="error-icon-box" style="background-color: @yield('icon_bg', '#E6EEFF'); color: @yield('icon_color', '#0641CC');">
            @yield('icon')
        </div>

        <div class="error-code" style="color: @yield('code_color', '#0641CC');">
            @yield('code')
        </div>

        <h1 class="error-title">
            @yield('headline')
        </h1>

        <p class="error-desc">
            @yield('message')
        </p>

        <div class="error-actions">
            <a href="@yield('button_url', route('landing'))" class="btn-action-primary">
                @yield('button_text', 'Kembali ke Beranda')
            </a>
            <button type="button" onclick="window.history.back()" class="btn-action-secondary">
                Halaman Sebelumnya
            </button>
        </div>
    </div>

    <div class="footer-brand">
        Dinas Komunikasi dan Informatika Kabupaten Sleman &bull; Portal Magang Terpadu
    </div>

</body>
</html>
