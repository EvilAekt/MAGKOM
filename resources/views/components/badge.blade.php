@props(['status' => 'default', 'label' => null])

@php
    $statusValue = is_object($status) && property_exists($status, 'value') ? $status->value : (string) $status;
    $statusLower = strtolower(trim($statusValue));

    // Pemetaan status domain MagKom ke token Design System 07-DS-MagKom
    $classes = match ($statusLower) {
        'diterima', 'aktif', 'selesai', 'disetujui', 'lulus', 'sudah_dinilai' => 
            'bg-emerald-50 text-emerald-700 border-emerald-200 ring-emerald-500/10',
        
        'menunggu', 'diajukan', 'waiting_list', 'terkirim', 'perlu_perbaikan', 'berkas_belum_lengkap', 'revisi', 'dalam_proses', 'draft' => 
            'bg-amber-50 text-amber-700 border-amber-200 ring-amber-500/10',
            
        'ditolak', 'dibatalkan', 'terlambat', 'mengundurkan_diri', 'gagal', 'nonaktif' => 
            'bg-rose-50 text-rose-700 border-rose-200 ring-rose-500/10',
            
        'bimbingan', 'info', 'diproses', 'penempatan' => 
            'bg-blue-50 text-blue-700 border-blue-200 ring-blue-500/10',
            
        default => 
            'bg-slate-50 text-slate-700 border-slate-200 ring-slate-500/10',
    };

    $dotColor = match ($statusLower) {
        'diterima', 'aktif', 'selesai', 'disetujui', 'lulus', 'sudah_dinilai' => 'bg-emerald-500',
        'menunggu', 'diajukan', 'waiting_list', 'terkirim', 'perlu_perbaikan', 'berkas_belum_lengkap', 'revisi', 'dalam_proses', 'draft' => 'bg-amber-500',
        'ditolak', 'dibatalkan', 'terlambat', 'mengundurkan_diri', 'gagal', 'nonaktif' => 'bg-rose-500',
        'bimbingan', 'info', 'diproses', 'penempatan' => 'bg-blue-500',
        default => 'bg-slate-400',
    };

    $displayLabel = $label ?? ucwords(str_replace('_', ' ', $statusValue));
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border ring-1 ring-inset {$classes} transition-colors"]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}" aria-hidden="true"></span>
    <span>{{ $displayLabel }}</span>
</span>
