@props([
    'title' => 'Belum Ada Data',
    'description' => 'Tidak ada entri data yang sesuai dengan kriteria penyaringan saat ini.',
    'icon' => null,
])

<div {{ $attributes->merge(['class' => 'p-10 text-center rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 flex flex-col items-center justify-center font-sans']) }} style="text-align: center; padding: 3rem 1.5rem; display: flex; flex-direction: column; align-items: center; justify-content: center;">
    <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex items-center justify-center text-slate-400 mb-4" aria-hidden="true" style="width: 56px; height: 56px; min-width: 56px; min-height: 56px; display: inline-flex; align-items: center; justify-content: center; border-radius: 16px; margin: 0 auto 1rem auto; background: #F8FAFC; border: 1px solid #E2E8F0;">
        @if ($icon)
            {{ $icon }}
        @else
            <svg class="w-8 h-8" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width: 32px; height: 32px; max-width: 32px; max-height: 32px; stroke: #94A3B8;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
        @endif
    </div>
    
    <h3 class="text-base font-bold text-slate-800 tracking-tight" style="font-size: 1.05rem; font-weight: 700; color: #1E293B; margin: 0 0 0.35rem 0;">{{ $title }}</h3>
    <p class="text-sm text-slate-500 max-w-sm mt-1 mb-6 leading-relaxed" style="font-size: 0.85rem; color: #64748B; max-width: 420px; margin: 0 auto; line-height: 1.5;">{{ $description }}</p>
    
    @if ($slot->isNotEmpty())
        <div class="flex items-center gap-3" style="margin-top: 1.25rem; display: inline-flex; align-items: center; justify-content: center; gap: 0.75rem;">
            {{ $slot }}
        </div>
    @endif
</div>
