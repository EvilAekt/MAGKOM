@php
    $height = (int) ($content['height'] ?? ($styles['desktop']['height'] ?? 40));
@endphp

<div id="{{ $blockId ?? 'block-spacer' }}" class="landing-block-spacer" data-wp-block="spacer" data-wp-block-name="Spacer Jarak" data-wp-block-id="{{ $blockId ?? 'block-spacer' }}" style="order: {{ $order ?? 1 }}; {{ !($visible ?? true) && !($isEditorMode ?? false) ? 'display: none !important;' : '' }} height: {{ $height }}px; width: 100%;"></div>

