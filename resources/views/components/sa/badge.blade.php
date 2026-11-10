@props(['tone' => 'neutral', 'icon' => null])

<span {{ $attributes->merge(['class' => 'sa-badge sa-badge--' . $tone]) }}>
    @if($icon)
        <x-sa.icon :name="$icon" :size="12" />
    @endif
    <span>{{ $slot }}</span>
</span>
