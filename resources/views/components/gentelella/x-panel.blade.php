@props([
    'title' => null,
    'subtitle' => null,
    'badge' => null,
    'badgeType' => 'primary', // 'primary', 'success', 'warning', 'danger', 'info'
    'collapsible' => true,
    'closeable' => false,
    'toolbox' => true,
    'actions' => null,
    'id' => null,
])

<div {{ $attributes->merge(['class' => 'x_panel']) }} @if($id) id="{{ $id }}" @endif role="region" @if($title) aria-label="{{ strip_tags($title) }}" @endif>
    @if($title || $badge || $toolbox || isset($actions))
        <div class="x_title">
            @if($title)
                <h2>
                    {!! $title !!}
                    @if($subtitle)
                        <small>{!! $subtitle !!}</small>
                    @endif
                    @if($badge)
                        <span class="x_badge x_badge_{{ $badgeType }}">{{ $badge }}</span>
                    @endif
                </h2>
            @endif

            @if($toolbox)
                <ul class="panel_toolbox" role="toolbar" aria-label="Aksi Panel">
                    @if(isset($actions) && trim($actions))
                        <li class="toolbox_actions">{!! $actions !!}</li>
                    @endif

                    @if($collapsible)
                        <li>
                            <button type="button" class="collapse-link" aria-label="Buka atau Tutup Panel" aria-expanded="true" title="Buka/Tutup Panel">
                                <svg class="panel_chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <polyline points="18 15 12 9 6 15"></polyline>
                                </svg>
                            </button>
                        </li>
                    @endif

                    @if($closeable)
                        <li>
                            <button type="button" class="close-link" aria-label="Sembunyikan Panel" title="Tutup Panel">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </li>
                    @endif
                </ul>
            @endif
            <div class="clearfix"></div>
        </div>
    @endif

    <div class="x_content">
        {{ $slot }}
    </div>
</div>
