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

    <style>
        :root {
            --neutral-0: #FFFFFF;
            --neutral-100: #F8FAFC;
            --neutral-150: #F5F7FB;
            --neutral-200: #EEF0F5;
            --neutral-400: #D0D3D9;
            --neutral-600: #666666;
            --neutral-800: #1D2530;
            --blue-600: #0751FF;
            --blue-700: #0641CC;
            --blue-tint: #E6EEFF;
            --blue-tint-line: #B3C8FF;
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
            padding: 1.5rem;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .error-card {
            background-color: var(--neutral-0);
            border: 1px solid var(--neutral-400);
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(29, 37, 48, 0.08);
            max-width: 520px;
            width: 100%;
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .error-icon-box {
            width: 72px;
            height: 72px;
            border-radius: 20px;
            margin: 0 auto 1.5rem auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-code {
            font-size: 2.75rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1.1;
            margin-bottom: 0.5rem;
        }

        .error-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--neutral-800);
            margin-bottom: 0.75rem;
        }

        .error-desc {
            font-size: 0.9375rem;
            color: var(--neutral-600);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn-action-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background-color: var(--blue-700);
            color: #FFFFFF;
            font-size: 0.9375rem;
            font-weight: 700;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(6, 65, 204, 0.25);
            min-height: 44px;
        }

        .btn-action-primary:hover {
            background-color: #0533A8;
            transform: translateY(-1px);
        }

        .footer-brand {
            margin-top: 1.5rem;
            font-size: 0.775rem;
            color: var(--neutral-600);
        }
    </style>
</head>
<body>

    <div class="error-card">
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

        <div>
            <a href="@yield('button_url', route('landing'))" class="btn-action-primary">
                @yield('button_text', 'Kembali ke Beranda')
            </a>
        </div>
    </div>

    <div class="footer-brand">
        Dinas Komunikasi dan Informatika Kabupaten Sleman &bull; Portal MagKom
    </div>

</body>
</html>
