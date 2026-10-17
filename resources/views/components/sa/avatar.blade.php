@props(['name', 'size' => 36])
@php
    $parts = preg_split('/\s+/', trim((string) $name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $initials = mb_strtoupper(mb_substr($parts[0] ?? '?', 0, 1).(isset($parts[1]) ? mb_substr($parts[1], 0, 1) : ''));
@endphp
<span {{ $attributes->merge(['class' => 'sa-avatar sa-avatar--'.$size]) }} aria-hidden="true">{{ $initials }}</span>
