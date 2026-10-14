<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Fitur Dynamic Security Captcha (SVG Native)
    |--------------------------------------------------------------------------
    |
    | Captcha 4 karakter acak bebas ambiguitas visual untuk form publik (Login & Register).
    | Beroperasi murni di atas PHP native SVG tanpa dependensi ekstensi GD / Imagick.
    |
    */

    'enabled' => env('CAPTCHA_ENABLED', true),

    'length' => (int) env('CAPTCHA_LENGTH', 4),

    // Karakter acak yang mudah dibaca (menghilangkan 0, O, 1, I untuk mencegah kerancuan pengguna)
    'characters' => '23456789ABCDEFGHJKLMNPQRSTUVWXYZ',

    // Masa aktif kode dalam session (detik)
    'ttl' => (int) env('CAPTCHA_TTL', 600),

    // Dimensi gambar vektor SVG
    'width' => 160,
    'height' => 50,

    // Konfigurasi pengujian otomatis
    'strict_testing' => false,
];
