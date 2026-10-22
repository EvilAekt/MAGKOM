@props([
    'type' => 'info', // 'info', 'success', 'warning', 'danger'
    'title' => null,
    'icon' => null,
])

@php
    $typeClass = match($type) {
        'success' => 'gentelella-callout-success',
        'warning' => 'gentelella-callout-warning',
        'danger' => 'gentelella-callout-danger',
        default => 'gentelella-callout-info',
    };

    $defaultIcon = match($type) {
        'success' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>',
        'warning' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>',
        'danger' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',
        default => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',
    };
@endphp

<div {{ $attributes->merge(['class' => "gentelella-callout {$typeClass}"]) }} role="alert">
    <div class="gentelella-callout-icon" aria-hidden="true">
        {!! $icon ?? $defaultIcon !!}
    </div>
    <div class="gentelella-callout-content">
        @if($title)
            <h4 class="gentelella-callout-title">{!! $title !!}</h4>
        @endif
        <div class="gentelella-callout-body">
            {{ $slot }}
        </div>
    </div>
</div>
