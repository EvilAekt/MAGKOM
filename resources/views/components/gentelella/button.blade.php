@props([
    'variant' => 'default',
    'icon' => null,
    'type' => 'button',
])

@php
    $variantClass = match($variant) {
        'primary' => 'btn-primary',
        'success' => 'btn-success',
        'danger' => 'btn-danger',
        'warning' => 'btn-warning',
        'info' => 'btn-info',
        default => 'btn-default',
    };
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn ' . $variantClass]) }} style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 600;">
    @if($icon)
        <i class="{{ $icon }}"></i>
    @endif
    {{ $slot }}
</button>
