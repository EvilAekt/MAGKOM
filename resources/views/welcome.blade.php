{{--
    DEPRECATED ORPHAN TEMPLATE: welcome.blade.php
    Berkas ini adalah template bawaan awal framework yang tidak digunakan lagi.
    Landing page resmi Portal MagKom dilayani oleh:
    Route: '/' -> App\Http\Controllers\Public\LandingController -> resources/views/public/landing.blade.php
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="0;url={{ route('landing') }}">
    <title>MAGKOM – Portal Magang Diskominfo Sleman</title>
</head>
<body>
    <p>Mengalihkan ke <a href="{{ route('landing') }}">Portal Resmi MAGKOM Diskominfo Sleman</a>...</p>
</body>
</html>
