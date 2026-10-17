@props(['value', 'max' => 100, 'label' => null, 'tone' => null])

@php
    $percentage = $max > 0 ? min(100, round(($value / $max) * 100)) : 0;
    $computedTone = $tone ?? ($percentage >= 100 ? 'danger' : ($percentage >= 75 ? 'warning' : 'info'));
@endphp

<div class="sa-meter-group">
    @if($label)
        <div class="sa-meter-group__header">
            <span class="sa-meter-group__label">{{ $label }}</span>
            <span class="sa-meter-group__val">{{ $value }} / {{ $max }} ({{ $percentage }}%)</span>
        </div>
    @endif
    <div class="sa-meter" role="meter" aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="{{ $max }}" aria-label="{{ $label ?? 'Meter' }}">
        <div class="sa-meter__fill sa-meter__fill--{{ $computedTone }}" data-v="{{ $percentage }}"></div>
    </div>
</div>
