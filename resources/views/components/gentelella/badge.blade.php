@props([
    'variant' => 'default',
])

@php
    $variantClass = match($variant) {
        'success' => 'badge-success',
        'danger', 'error' => 'badge-danger',
        'warning' => 'badge-warning',
        'info' => 'badge-info',
        'primary' => 'badge-primary',
        'secondary' => 'badge-secondary',
        default => 'badge-default',
    };

    $variantStyles = match($variant) {
        'success' => 'background: #dcfce7; color: #166534; border: 1px solid #86efac;',
        'danger', 'error' => 'background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5;',
        'warning' => 'background: #fef3c7; color: #92400e; border: 1px solid #fde68a;',
        'info' => 'background: #e0f2fe; color: #075985; border: 1px solid #bae6fd;',
        'primary' => 'background: #e6eeff; color: #0641CC; border: 1px solid #b3c8ff;',
        'secondary' => 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;',
        default => 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;',
    };
@endphp

<span {{ $attributes->merge(['class' => 'badge ' . $variantClass]) }} style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.02em; {{ $variantStyles }}">
    {{ $slot }}
</span>
