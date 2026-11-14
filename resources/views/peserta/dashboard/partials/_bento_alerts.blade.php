{{-- ==========================================================================
   Bento Side Alerts:
   Sisa Hari Magang (Amber Card - X hari + Dot Matrix)
   Reference: Precision Visual Parity with Pembimbing Dashboard
   ========================================================================== --}}
@php
    /** @var int|null $sisaHari */
    /** @var \App\Models\Penempatan|null $penempatan */
    /** @var int|null $durasiBulan */
    /** @var int|null $totalHariMagang */
    /** @var bool|null $isBelumMulai */
    /** @var int|null $hariMenujuMulai */

    $durasiBln = max(1, $durasiBulan ?? ($penempatan?->durasi_bulan ?? 3));
    $totHari = max(1, $totalHariMagang ?? ($durasiBln * 30));
    $sisaHariVal = $sisaHari ?? $totHari;

    // Jumlah bar dinamis sesuai jumlah bulan yang dipilih peserta (misal: 3 bar untuk 3 bulan)
    $totalBars = $durasiBln;

    // Jumlah bar terisi dihitung proporsional terhadap sisa hari magang
    if ($isBelumMulai ?? false) {
        $barsFilled = $totalBars; // Seluruh bar terisi penuh karena waktu magang belum berkurang
    } elseif ($sisaHariVal <= 0) {
        $barsFilled = 0;
    } else {
        $barsFilled = max(1, min($totalBars, (int) ceil(($sisaHariVal / $totHari) * $totalBars)));
    }
@endphp

<div class="bento-side-alerts">
    <!-- Alert: Sisa Hari Magang (Amber) -->
    <div class="bento-alert-card card-alert-amber">
        <div class="alert-card-header">
            <div class="alert-icon-wrap icon-wrap-amber-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                    <line x1="4" y1="22" x2="4" y2="15"></line>
                </svg>
            </div>
            <span class="alert-card-title">Sisa Hari Magang</span>
        </div>
        <div class="alert-card-body">
            <div class="alert-val-row">
                <span class="alert-big-number">{{ $sisaHariVal }}</span>
                <span class="alert-sub-slash">hari</span>
            </div>
            <div class="bento-dot-matrix matrix-alert-bars matrix-alert-dark" aria-label="{{ $sisaHariVal }} hari sisa magang dari durasi {{ $durasiBln }} bulan">
                @for($d = 1; $d <= $totalBars; $d++)
                    <span class="matrix-dot {{ $d <= $barsFilled ? 'dot-filled-dark' : 'dot-faded-dark' }}" title="Bulan ke-{{ $d }} dari {{ $durasiBln }} bulan"></span>
                @endfor
            </div>
            @if($isBelumMulai ?? false)
                <div class="alert-note-pra">
                    Mulai dalam {{ $hariMenujuMulai }} hari &bull; Durasi {{ $durasiBln }} Bulan
                </div>
            @endif
        </div>
    </div>
</div>
