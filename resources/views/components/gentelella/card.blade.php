@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'badge' => null,
    'badgeType' => 'primary',
    'hoverable' => false,
])

@php
    $badgeClass = match ($badgeType) {
        'success' => 'x_badge_success',
        'warning' => 'x_badge_warning',
        'danger' => 'x_badge_danger',
        'info' => 'x_badge_info',
        default => 'x_badge_primary',
    };
@endphp

<div {{ $attributes->merge(['class' => 'gentelella-card' . ($hoverable ? ' gentelella-card-hover' : '')]) }}>
    @if($title || $badge || isset($actions))
        <div class="gentelella-card-header">
            <div class="gentelella-card-title-wrap">
                @if($icon)
                    <span class="gentelella-card-icon" aria-hidden="true">
                        @if(str_starts_with(trim($icon), '<svg'))
                            {!! $icon !!}
                        @elseif(view()->exists('components.sa.icon'))
                            <x-sa.icon :name="$icon" :size="18" />
                        @else
                            {{ $icon }}
                        @endif
                    </span>
                @endif
                <div>
                    @if($title)
                        <h4 class="gentelella-card-title">{!! $title !!}</h4>
                    @endif
                    @if($subtitle)
                        <small class="gentelella-card-subtitle">{!! $subtitle !!}</small>
                    @endif
                </div>
            </div>

            <div class="gentelella-card-header-actions">
                @if($badge)
                    <span class="x_badge {{ $badgeClass }}">{{ $badge }}</span>
                @endif
                @if(isset($actions))
                    {{ $actions }}
                @endif
            </div>
        </div>
    @endif

    <div class="gentelella-card-body">
        {{ $slot }}
    </div>

    @if(isset($footer) && trim($footer))
        <div class="gentelella-card-footer">
            {{ $footer }}
        </div>
    @endif
</div>
