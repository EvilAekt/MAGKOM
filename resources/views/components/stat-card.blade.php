@props([
    'title',
    'value',
    'subtitle' => null,
    'icon' => null,
    'color' => 'blue',
    'trend' => null,
    'trendPositive' => true,
    'href' => null,
])

@php
    $colorConfig = match ($color) {
        'green', 'emerald' => [
            'bg' => 'bg-emerald-50',
            'border' => 'border-emerald-100 hover:border-emerald-300',
            'iconBg' => 'bg-emerald-100 text-emerald-700',
            'valueText' => 'text-emerald-950',
        ],
        'amber', 'yellow' => [
            'bg' => 'bg-amber-50/60',
            'border' => 'border-amber-100 hover:border-amber-300',
            'iconBg' => 'bg-amber-100 text-amber-700',
            'valueText' => 'text-amber-950',
        ],
        'rose', 'red' => [
            'bg' => 'bg-rose-50/60',
            'border' => 'border-rose-100 hover:border-rose-300',
            'iconBg' => 'bg-rose-100 text-rose-700',
            'valueText' => 'text-rose-950',
        ],
        'purple' => [
            'bg' => 'bg-purple-50/60',
            'border' => 'border-purple-100 hover:border-purple-300',
            'iconBg' => 'bg-purple-100 text-purple-700',
            'valueText' => 'text-purple-950',
        ],
        default => [
            'bg' => 'bg-blue-50/60',
            'border' => 'border-blue-100 hover:border-blue-300',
            'iconBg' => 'bg-blue-100 text-blue-700',
            'valueText' => 'text-blue-950',
        ],
    };

    $wrapperTag = $href ? 'a' : 'div';
@endphp

<{{ $wrapperTag }} 
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => "block p-5 rounded-2xl border transition-all duration-200 bg-white shadow-xs hover:shadow-md {$colorConfig['border']} group"]) }}>
    <div class="flex items-start justify-between gap-3">
        <div class="space-y-1">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block font-sans">{{ $title }}</span>
            <div class="text-2xl sm:text-3xl font-extrabold tracking-tight {{ $colorConfig['valueText'] }} font-sans">
                {{ $value }}
            </div>
            @if ($subtitle)
                <p class="text-xs text-slate-500 font-medium font-sans pt-0.5">{{ $subtitle }}</p>
            @endif
        </div>
        @if ($icon)
            <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform group-hover:scale-105 {{ $colorConfig['iconBg'] }}" aria-hidden="true">
                {{ $icon }}
            </div>
        @endif
    </div>

    @if ($trend)
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-1.5 text-xs font-medium font-sans">
            <span class="{{ $trendPositive ? 'text-emerald-700' : 'text-rose-700' }} inline-flex items-center gap-0.5 font-bold">
                @if ($trendPositive)
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                @else
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                @endif
                {{ $trend }}
            </span>
            <span class="text-slate-400">vs target</span>
        </div>
    @endif
</{{ $wrapperTag }}>
