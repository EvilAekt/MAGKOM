<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'MagKom - Login')</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

  @vite(['resources/css/pages/auth/auth-depth.css', 'resources/js/pages/auth/auth-depth.js'])
</head>
<body>

@php
    $authVariant = $variant ?? 'login';
    $mainHeightStyle = ($authVariant === 'register') ? 'height: 800px' : 'min-height: 800px';
@endphp

<div style="position: relative; overflow: hidden; min-height: 980px; box-sizing: border-box; padding: 60px 40px; display: flex; align-items: center; justify-content: center; background: radial-gradient(circle at 50% 45%, #003566 0%, #02275D 55%, #01183A 100%); font-family: 'Plus Jakarta Sans', system-ui, sans-serif; color: #212529">

  <canvas data-auth-canvas aria-hidden="true" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; display: block"></canvas>

  <main style="position: relative; z-index: 1; width: 100%; max-width: 1120px; {{ $mainHeightStyle }}; display: flex; flex-wrap: wrap; border-radius: 28px; overflow: hidden; background: #FFFFFF; box-shadow: 0 30px 70px rgba(0, 20, 50, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.14)">

    @include('auth.partials._left_panel', ['variant' => $authVariant])

    @yield('auth-form')

  </main>
</div>

</body>
</html>

