@props(['label', 'value', 'tone' => 'blue', 'icon' => 'users', 'hint' => null, 'meta' => null])
<div {{ $attributes->merge(['class' => 'sa-card sa-stat sa-stat--'.$tone]) }}>
    <div class="sa-stat__main">
        <span class="sa-stat__label">{{ $label }}</span>
        <span class="sa-stat__value">{{ is_numeric($value) ? number_format($value) : $value }}</span>
        @if($hint)<span class="sa-stat__hint">{{ $hint }}</span>@endif
        @if($meta)<span class="sa-stat__meta">{{ $meta }}</span>@endif
    </div>
    <span class="sa-stat__icon"><x-sa.icon :name="$icon" :size="22" /></span>
</div>
