@props([
    'id',
    'title' => null,
    'size' => 'md', // sm, md, lg, xl
])

@php
    $sizeClasses = [
        'sm' => 'gentelella-modal-sm',
        'md' => 'gentelella-modal-md',
        'lg' => 'gentelella-modal-lg',
        'xl' => 'gentelella-modal-xl',
    ][$size] ?? 'gentelella-modal-md';
@endphp

<div id="{{ $id }}" class="gentelella-modal-backdrop" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title" tabindex="-1">
    <div class="gentelella-modal-dialog {{ $sizeClasses }}">
        <div class="gentelella-modal-content">
            @if($title || isset($header))
                <div class="gentelella-modal-header">
                    @if(isset($header))
                        {{ $header }}
                    @else
                        <h4 class="gentelella-modal-title" id="{{ $id }}-title">{{ $title }}</h4>
                    @endif
                    <button type="button" class="gentelella-modal-close" aria-label="Tutup" onclick="closeGentelellaModal('{{ $id }}')">&times;</button>
                </div>
            @endif

            <div class="gentelella-modal-body">
                {{ $slot }}
            </div>

            @if(isset($footer))
                <div class="gentelella-modal-footer">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
