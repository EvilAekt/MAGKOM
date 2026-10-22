@props([
    'title',
    'value' => 0,
    'max' => 100,
    'unit' => null,
    'percentage' => null,
    'status' => null,
    'subtext' => null,
    'icon' => null,
])

@php
    $calcPercent = $percentage ?? ($max > 0 ? min(100, (int) round(($value / $max) * 100)) : 0);
    $barColorClass = match(true) {
        $calcPercent >= 100 => 'metric-bar-danger',
        $calcPercent >= 75 => 'metric-bar-warning',
        default => 'metric-bar-success',
    };
    $statusColorClass = match(true) {
        $calcPercent >= 100 => 'metric-status-danger',
        $calcPercent >= 75 => 'metric-status-warning',
        default => 'metric-status-success',
    };
@endphp

<div {{ $attributes->merge(['class' => 'gentelella-metric-card']) }}>
    <div class="gentelella-metric-header">
        <div class="gentelella-metric-title-group">
            @if($icon)
                <span class="gentelella-metric-icon" aria-hidden="true">
                    @if(str_starts_with(trim($icon), '<svg'))
                        {!! $icon !!}
                    @else
                        {{ $icon }}
                    @endif
                </span>
            @endif
            <span class="gentelella-metric-title">{{ $title }}</span>
        </div>
        @if($status)
            <span class="gentelella-metric-status {{ $statusColorClass }}">{{ $status }}</span>
        @else
            <span class="gentelella-metric-percent">{{ $calcPercent }}%</span>
        @endif
    </div>

    <div class="gentelella-metric-counter-row">
        <div class="gentelella-metric-val">
            <strong>{{ $value }}</strong>
            @if($max && $max !== 100)
                <span class="gentelella-metric-max">/ {{ $max }}</span>
            @endif
            @if($unit)
                <span class="gentelella-metric-unit">{{ $unit }}</span>
            @endif
        </div>
        @if($status && $calcPercent)
            <span class="gentelella-metric-percent-sub">{{ $calcPercent }}%</span>
        @endif
    </div>

    <div class="gentelella-metric-progress-track">
        <div class="gentelella-metric-progress-fill {{ $barColorClass }}" style="width: {{ $calcPercent }}%;"></div>
    </div>

    @if($subtext || (isset($slot) && trim($slot)))
        <div class="gentelella-metric-footer">
            @if($subtext)
                <small class="gentelella-metric-subtext">{{ $subtext }}</small>
            @endif
            {{ $slot }}
        </div>
    @endif
</div>
