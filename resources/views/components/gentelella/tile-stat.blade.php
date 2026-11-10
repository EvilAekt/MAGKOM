@props([
    'title',
    'value',
    'icon' => null,
    'trendText' => null,
    'trendType' => 'success', // 'success', 'warning', 'danger', 'info', 'neutral'
    'subText' => null,
    'col' => 'col-md-3 col-sm-6 col-xs-12',
    'color' => 'teal', // 'teal', 'yellow', 'blue', 'red'
    'trendDirection' => null, // 'up', 'down', 'neutral'
    'sparkBars' => [],
])

@php
    $trendClass = match ($trendType) {
        'success' => 'trend-success',
        'warning' => 'trend-warning',
        'danger' => 'trend-danger',
        'info' => 'trend-info',
        default => 'trend-neutral',
    };

    $effectiveDirection = $trendDirection ?? match ($trendType) {
        'danger' => 'down',
        'warning' => 'down',
        default => 'up',
    };

    $colorClass = match ($color) {
        'teal', 'green', 'primary' => 'teal',
        'yellow', 'warning', 'amber' => 'yellow',
        'blue', 'info' => 'blue',
        'red', 'danger' => 'red',
        default => 'teal',
    };
@endphp

<div {{ $attributes->merge(['class' => "tile_stats_count {$col}"]) }}>
    <div class="tile_stats_inner stat stat-{{ $colorClass }}">
        @if($icon)
            <div class="stat-icon {{ $colorClass }} count_icon" aria-hidden="true">
                @if(str_starts_with(trim($icon), '<svg'))
                    {!! $icon !!}
                @elseif(view()->exists('components.sa.icon'))
                    <x-sa.icon :name="$icon" :size="22" />
                @else
                    {{ $icon }}
                @endif
            </div>
        @endif

        <div class="stat-content">
            <span class="count_top stat-label">
                <span class="count_title">{{ $title }}</span>
            </span>

            <div class="stat-value-row">
                <span class="count stat-value">{{ $value }}</span>
                @if($trendText)
                    <span class="stat-change {{ $effectiveDirection }}" aria-label="Tren {{ $trendText }}">
                        @if($effectiveDirection === 'up')
                            <svg width="10" height="10" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                <path d="M8 3.5l5 5h-3v5H6v-5H3l5-5z"></path>
                            </svg>
                        @elseif($effectiveDirection === 'down')
                            <svg width="10" height="10" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                <path d="M8 12.5l5-5h-3v-5H6v5H3l5 5z"></path>
                            </svg>
                        @endif
                        {{ $trendText }}
                    </span>
                @endif
            </div>

            @if($subText)
                <div class="count_bottom stat-subtext">
                    <span>{{ $subText }}</span>
                </div>
            @endif
        </div>

        @if(!empty($sparkBars))
            <div class="stat-spark sa-spark" aria-hidden="true">
                @foreach($sparkBars as $barHeight)
                    <i style="height: {{ $barHeight }}%"></i>
                @endforeach
            </div>
        @endif

        @if(isset($slot) && trim($slot))
            {{ $slot }}
        @endif
    </div>
</div>

