@props(['title' => null, 'subtitle' => null, 'href' => null, 'linkText' => null])

<div {{ $attributes->merge(['class' => 'sa-panel']) }}>
    @if($title || $href)
        <div class="sa-panel__head">
            <div>
                @if($title)
                    <h3 class="sa-panel__title">{!! $title !!}</h3>
                @endif
                @if($subtitle)
                    <p class="sa-panel__sub">{!! $subtitle !!}</p>
                @endif
            </div>
            @if($href && $linkText)
                <a href="{{ $href }}" class="sa-link">{!! $linkText !!}</a>
            @endif
        </div>
    @endif
    {{ $slot }}
</div>
