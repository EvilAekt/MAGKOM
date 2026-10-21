@props([
    'title',
    'value',
    'icon' => null,
    'trendText' => null,
    'trendType' => 'success', // 'success', 'warning', 'danger', 'info', 'neutral'
    'subText' => null,
    'col' => 'col-md-3 col-sm-6 col-xs-12',
])

@php
    $trendClass = match ($trendType) {
        'success' => 'trend-success',
        'warning' => 'trend-warning',
        'danger' => 'trend-danger',
        'info' => 'trend-info',
        default => 'trend-neutral',
    };
@endphp

<div {{ $attributes->merge(['class' => "tile_stats_count {$col}"]) }}>
    <div class="tile_stats_inner">
        <span class="count_top">
            @if($icon)
                <span class="count_icon" aria-hidden="true">
                    @if(str_starts_with(trim($icon), '<svg'))
                        {!! $icon !!}
                    @elseif(view()->exists('components.sa.icon'))
                        <x-sa.icon :name="$icon" :size="16" />
                    @else
                        {{ $icon }}
                    @endif
                </span>
            @endif
            <span class="count_title">{{ $title }}</span>
        </span>

        <div class="count">{{ $value }}</div>

        @if($trendText || $subText || (isset($slot) && trim($slot)))
            <span class="count_bottom">
                @if($trendText)
                    <i class="trend-indicator {{ $trendClass }}">
                        @if($trendType === 'success')
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="18 15 12 9 6 15"></polyline></svg>
                        @elseif($trendType === 'danger')
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        @endif
                        {{ $trendText }}
                    </i>
                @endif
                @if($subText)
                    <span class="count_subtext">{{ $subText }}</span>
                @endif
                {{ $slot }}
            </span>
        @endif
    </div>
</div>
