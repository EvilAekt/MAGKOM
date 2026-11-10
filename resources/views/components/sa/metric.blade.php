@props(['label', 'value', 'hint' => null, 'icon' => null, 'tone' => 'neutral'])

<div {{ $attributes->merge(['class' => 'sa-metric sa-metric--' . $tone]) }}>
    <div class="sa-metric__header">
        <span class="sa-metric__label">{!! $label !!}</span>
        @if($icon)
            <span class="sa-metric__icon"><x-sa.icon :name="$icon" :size="18" /></span>
        @endif
    </div>
    <div class="sa-metric__value">{!! $value !!}</div>
    @if($hint || isset($sub))
        <div class="sa-metric__hint">
            {!! $hint ?? $sub !!}
        </div>
    @endif
    @if(isset($slot) && trim($slot))
        <div class="sa-metric__hint">
            {{ $slot }}
        </div>
    @endif
</div>
