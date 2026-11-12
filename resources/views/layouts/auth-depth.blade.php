<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'MagKom - Login')</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

  <!-- Google Fonts: Lexend, Source Sans 3 & Open Sans -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700;800&family=Source+Sans+3:wght@400;500;600;700&family=Open+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

  @vite(['resources/css/pages/auth/auth-depth.css', 'resources/js/pages/auth/auth-depth.js'])
</head>
<body>

@php
    $authVariant = $variant ?? 'login';
    $mainHeightStyle = ($authVariant === 'register') ? 'height: 800px' : 'min-height: 800px';
@endphp

<div style="position: relative; overflow: hidden; min-height: 100vh; min-height: 100dvh; box-sizing: border-box; padding: 40px 24px; display: flex; align-items: center; justify-content: center; background: radial-gradient(circle at 50% 45%, #003566 0%, #02275D 55%, #01183A 100%); font-family: 'Plus Jakarta Sans', system-ui, sans-serif; color: #212529">

  <canvas data-auth-canvas aria-hidden="true" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; display: block"></canvas>

  <main style="position: relative; z-index: 1; width: 100%; max-width: 1120px; {{ $mainHeightStyle }}; display: flex; flex-wrap: wrap; border-radius: 28px; overflow: hidden; background: #FFFFFF; box-shadow: 0 30px 70px rgba(0, 20, 50, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.14)">

    @include('auth.partials._left_panel', ['variant' => $authVariant])

    @yield('auth-form')

  </main>
</div>

</body>
</html>

