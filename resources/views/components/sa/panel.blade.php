@props(['title', 'subtitle' => null, 'href' => null, 'linkLabel' => null])
<section {{ $attributes->merge(['class' => 'sa-card']) }}>
    <header class="sa-card__head">
        <div>
            <h3 class="sa-card__title">{{ $title }}</h3>
            @if($subtitle)
                <p class="sa-card__subtitle">{{ $subtitle }}</p>
            @endif
        </div>
        @if($href && $linkLabel)
            <a href="{{ $href }}" class="sa-link">{{ $linkLabel }} <x-sa.icon name="chevron-right" :size="14" /></a>
        @endif
    </header>
    <div class="sa-card__body">
        {{ $slot }}
    </div>
</section>
