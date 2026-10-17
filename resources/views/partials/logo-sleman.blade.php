@php
    static $logoSlemanDataUri = null;
    if ($logoSlemanDataUri === null) {
        $base64Path = public_path('images/logo-sleman.base64.txt');
        if (file_exists($base64Path)) {
            $logoSlemanDataUri = 'data:image/png;base64,' . trim(file_get_contents($base64Path));
        } else {
            $logoSlemanDataUri = asset('images/logo-sleman.png');
        }
    }
@endphp
<img src="{{ $logoSlemanDataUri }}"
     data-src="{{ asset('images/logo-sleman.png') }}"
     alt="{{ $alt ?? 'Lambang Kabupaten Sleman' }}"
     width="{{ $width ?? '32' }}"
     height="{{ $height ?? '32' }}"
     loading="eager"
     fetchpriority="high"
     decoding="sync"
     @if(!empty($class)) class="{{ $class }}" @endif
     @if(!empty($style)) style="{{ $style }}" @endif>
