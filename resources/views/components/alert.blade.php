@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
    $typeConfig = match ($type) {
        'success' => [
            'wrap' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
            'iconWrap' => 'text-emerald-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
        ],
        'warning' => [
            'wrap' => 'bg-amber-50 border-amber-200 text-amber-800',
            'iconWrap' => 'text-amber-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
        ],
        'error', 'danger' => [
            'wrap' => 'bg-rose-50 border-rose-200 text-rose-800',
            'iconWrap' => 'text-rose-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
        ],
        default => [
            'wrap' => 'bg-blue-50 border-blue-200 text-blue-800',
            'iconWrap' => 'text-blue-600',
            'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
        ],
    };
@endphp

<div {{ $attributes->merge(['class' => "flex items-start gap-3 p-4 rounded-xl border text-sm font-sans {$typeConfig['wrap']}"]) }} role="alert">
    <div class="flex-shrink-0 mt-0.5 {{ $typeConfig['iconWrap'] }}">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            {!! $typeConfig['icon'] !!}
        </svg>
    </div>
    <div class="flex-1">
        @if ($title)
            <h4 class="font-bold text-sm mb-1 leading-snug">{{ $title }}</h4>
        @endif
        <div class="text-xs sm:text-sm leading-relaxed">
            {{ $slot }}
        </div>
    </div>
    @if ($dismissible)
        <button type="button" @click="$el.closest('[role=alert]').remove()" class="flex-shrink-0 text-slate-400 hover:text-slate-600 p-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-400 min-w-[32px] min-h-[32px] flex items-center justify-center" aria-label="Tutup">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
</div>
