@props(['text', 'icon' => 'info'])

<div {{ $attributes->merge(['class' => 'sa-empty']) }}>
    <div class="sa-empty__icon">
        <x-sa.icon :name="$icon" :size="24" />
    </div>
    <p class="sa-empty__text">{{ $text }}</p>
</div>
