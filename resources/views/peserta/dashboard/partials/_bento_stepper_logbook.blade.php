{{-- ==========================================================================
   Bento Stepper Mingguan (M1 - M13) - Dashboard Peserta
   Kartu Stepper Horizontal Proporsional & Interaktif
   ========================================================================== --}}
@php
    /** @var array $daftarMinggu */
    /** @var int $mingguAktif */
    /** @var int $totalMinggu */
    /** @var bool|null $isBelumMulai */
    $isPraMagang = ($isBelumMulai ?? false) || ($mingguAktif === 0);
@endphp

<div class="bento-card bento-stepper-card">
    <div class="bento-card-header">
        <div class="bento-title-group">
            <span class="bento-title-indicator"></span>
            <h3 class="bento-card-title">{!! __('Jurnal & Logbook Mingguan') !!}</h3>
        </div>
        @if($isPraMagang)
            <span class="bento-pill-badge badge-neutral">{{ __('Pra-Magang') }}</span>
        @else
            <span class="bento-pill-badge badge-neutral">{{ __('Minggu berjalan') }}: M{{ $mingguAktif }}</span>
        @endif
    </div>

    {{-- Stepper Grid Mingguan M1 s/d M13 (Bisa Diklik Langsung) --}}
    <div class="bento-stepper-chips-wrap">
        @foreach($daftarMinggu as $item)
            @php
                $mKe = $item['minggu_ke'];
                $st = $item['status'];
                $chipClass = 'chip-upcoming';
                if (! $isPraMagang) {
                    if ($mKe < $mingguAktif && ($st === 'disetujui' || $st === 'terkirim')) {
                        $chipClass = 'chip-filled';
                    } elseif ($mKe === $mingguAktif) {
                        $chipClass = 'chip-current';
                    }
                }
            @endphp
            <a href="{{ route('peserta.logbook.index', ['minggu' => $mKe]) }}"
               class="bento-week-chip {{ $chipClass }}"
               title="{{ __('Buka Logbook Minggu :ke (:periode)', ['ke' => $mKe, 'periode' => $item['label_periode']]) }}">
                M{{ $mKe }}
            </a>
        @endforeach
    </div>

    {{-- Legend Stepper --}}
    <div class="bento-stepper-legend">
        <div class="bento-legend-item">
            <span class="bento-legend-swatch swatch-filled"></span>
            <span>{{ __('Sudah diisi') }}</span>
        </div>
        <div class="bento-legend-item">
            <span class="bento-legend-swatch swatch-current"></span>
            <span>{{ __('Minggu berjalan') }}</span>
        </div>
        <div class="bento-legend-item">
            <span class="bento-legend-swatch swatch-upcoming"></span>
            <span>{{ __('Mendatang') }}</span>
        </div>
    </div>
</div>

