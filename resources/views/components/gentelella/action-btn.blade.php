@props([
    'variant' => 'primary', // primary, success, warning, danger
    'href' => null,
    'type' => 'button',
    'title' => null,
])

@php
    $class = 'btn-action-gentelella btn-action-' . $variant;
@endphp

@if($href)
    <a href="{{ $href }}" {{ $title ? 'title='.$title : '' }} {{ $attributes->merge(['class' => $class]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $title ? 'title='.$title : '' }} {{ $attributes->merge(['class' => $class]) }}>
        {{ $slot }}
    </button>
@endif
