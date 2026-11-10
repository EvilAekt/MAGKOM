@props([
    'title',
    'description' => null,
    'badge' => null,
])

<div {{ $attributes->merge(['class' => 'page-title']) }}>
    <div class="title_left">
        <h1 class="page-title-heading">
            {{ $title }}
            @if($badge)
                <span class="page-title-badge">{{ $badge }}</span>
            @endif
        </h1>
        @if($description)
            <p class="page-title-sub">{{ $description }}</p>
        @endif
    </div>

    @if(isset($actions) && trim($actions))
        <div class="title_right">
            <div class="page-actions-wrap">
                {{ $actions }}
            </div>
        </div>
    @endif
    <div class="clearfix"></div>
</div>
