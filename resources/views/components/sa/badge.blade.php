@props(['tone' => 'neutral'])
<span {{ $attributes->merge(['class' => 'sa-badge sa-badge--'.$tone]) }}>{{ $slot }}</span>
