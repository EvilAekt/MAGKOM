@php
    /** @var string|null $alt */
    /** @var string|null $width */
    /** @var string|null $height */
    /** @var string|null $class */
    /** @var string|null $style */
    /** @var string|null $loading */
    /** @var string|null $fetchpriority */

    $resolvedLoading = $loading ?? 'eager';
    $resolvedPriority = $fetchpriority ?? 'auto';
@endphp
<img src="{{ asset('images/logo-sleman.png') }}"
     alt="{{ $alt ?? 'Lambang Kabupaten Sleman' }}"
     width="{{ $width ?? '32' }}"
     height="{{ $height ?? '32' }}"
     loading="{{ $resolvedLoading }}"
     fetchpriority="{{ $resolvedPriority }}"
     decoding="async"
     @if(!empty($class)) class="{{ $class }}" @endif
     @if(!empty($style)) style="{{ $style }}" @endif>
