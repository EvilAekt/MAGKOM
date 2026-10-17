@props(['href', 'label', 'count', 'tone' => 'info'])
<a href="{{ $href }}" class="sa-chip sa-chip--{{ $tone }}">
    <span class="sa-chip__count">{{ number_format($count) }}</span>
    <span class="sa-chip__label">{{ $label }}</span>
    <x-sa.icon name="chevron-right" :size="14" />
</a>
