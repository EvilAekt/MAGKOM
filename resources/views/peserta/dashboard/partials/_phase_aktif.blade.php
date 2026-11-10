{{-- ==========================================================================
   Phase Aktif Workspace - Dashboard Peserta Magang
   Desain Presisi Parity DS-MagKom (Gentelella Parity)
   1. Widget Logbook Mingguan (Summary 2 Kotak Statis & Stepper M1-M13)
   2. Widget Tugas dari Pembimbing (Summary 3 Kotak Statis & Segmented Progress Bar)
   ========================================================================== --}}
@php
    /** @var array $daftarMinggu */
    /** @var int $mingguAktif */
    /** @var int $totalMinggu */
    /** @var int $logbookSudahDiisi */
    /** @var int $logbookBelumDiisi */
    /** @var int $totalTugas */
    /** @var int $tugasSelesai */
    /** @var int $tugasLewatTenggat */
    /** @var \Illuminate\Support\Collection $tugasMendesak */

    $pctTugasSelesai = $totalTugas > 0 ? min(100, (int) round(($tugasSelesai / $totalTugas) * 100)) : 33;
@endphp

<div class="saas-phase-aktif-container">
    {{-- ======================================================================
       1. Widget Logbook Mingguan
       ====================================================================== --}}
    <div class="saas-content-card">
        <div class="saas-card-title-bar">
            <div class="saas-title-with-accent">
                <span class="saas-accent-line"></span>
                <h2 class="saas-card-heading">Logbook Mingguan</h2>
                <span class="saas-pill-tag">Minggu {{ $mingguAktif }} dari {{ $totalMinggu }}</span>
            </div>
        </div>

        {{-- Summary 2 Kotak Statis (Hanya Dilihat / Read-Only) --}}
        <div class="saas-logbook-summary-grid">
            <div class="saas-stat-box box-blue saas-static-box">
                <div class="saas-stat-indicator blue"></div>
                <div class="saas-stat-body">
                    <span class="saas-stat-num">{{ $logbookSudahDiisi }}</span>
                    <span class="saas-stat-desc">Sudah diisi</span>
                </div>
            </div>
            <div class="saas-stat-box box-amber saas-static-box">
                <div class="saas-stat-indicator amber"></div>
                <div class="saas-stat-body">
                    <span class="saas-stat-num">{{ $logbookBelumDiisi }}</span>
                    <span class="saas-stat-desc">Belum diisi</span>
                </div>
            </div>
        </div>

        {{-- Stepper Grid Mingguan M1 s/d M13 (Bisa Diklik) --}}
        <div class="saas-stepper-container">
            <div class="saas-stepper-grid">
                @foreach($daftarMinggu as $item)
                    @php
                        $mKe = $item['minggu_ke'];
                        $st = $item['status'];
                        $isCurrent = ($mKe === $mingguAktif);
                        $pillClass = 'upcoming';
                        if ($mKe < $mingguAktif && ($st === 'disetujui' || $st === 'terkirim')) {
                            $pillClass = 'filled';
                        } elseif ($mKe === $mingguAktif) {
                            $pillClass = 'current-week';
                        }
                    @endphp
                    <a href="{{ route('peserta.logbook.index', ['minggu' => $mKe]) }}"
                       class="saas-week-pill {{ $pillClass }}"
                       title="Buka Logbook Minggu {{ $mKe }} ({{ $item['label_periode'] }})">
                        M{{ $mKe }}
                    </a>
                @endforeach
            </div>

            {{-- Legend Stepper --}}
            <div class="saas-stepper-legend">
                <span class="saas-legend-item">
                    <span class="saas-legend-swatch filled"></span>
                    <span>Sudah diisi</span>
                </span>
                <span class="saas-legend-item">
                    <span class="saas-legend-swatch unfilled"></span>
                    <span>Belum diisi</span>
                </span>
                <span class="saas-legend-item">
                    <span class="saas-legend-swatch upcoming"></span>
                    <span>Mendatang</span>
                </span>
            </div>
        </div>
    </div>

    {{-- ======================================================================
       2. Widget Tugas dari Pembimbing
       ====================================================================== --}}
    <div class="saas-content-card">
        <div class="saas-card-title-bar">
            <div class="saas-title-with-accent">
                <span class="saas-accent-line"></span>
                <h2 class="saas-card-heading">Tugas dari Pembimbing</h2>
            </div>
        </div>

        {{-- Summary 3 Kotak Statis (Hanya Dilihat / Read-Only) --}}
        <div class="saas-tugas-summary-grid">
            <div class="saas-stat-box box-gray saas-static-box">
                <div class="saas-stat-indicator gray"></div>
                <div class="saas-stat-body">
                    <span class="saas-stat-num">{{ $totalTugas }}</span>
                    <span class="saas-stat-desc">Total tugas</span>
                </div>
            </div>
            <div class="saas-stat-box box-blue-soft saas-static-box">
                <div class="saas-stat-indicator blue"></div>
                <div class="saas-stat-body">
                    <span class="saas-stat-num">{{ $tugasSelesai }}</span>
                    <span class="saas-stat-desc">Sudah dikerjakan</span>
                </div>
            </div>
            <div class="saas-stat-box box-red-soft saas-static-box">
                <div class="saas-stat-indicator red"></div>
                <div class="saas-stat-body">
                    <span class="saas-stat-num">{{ $tugasLewatTenggat }}</span>
                    <span class="saas-stat-desc">Lewat tenggat</span>
                </div>
            </div>
        </div>

        {{-- Segmented Progress Bar (3 Kapsul Rounded Clean Sesuai Mockup Referensi) --}}
        <div class="saas-tugas-segmented-track">
            @php
                $segCount = max(1, $totalTugas);
            @endphp
            @for($ti = 1; $ti <= $segCount; $ti++)
                <span class="saas-tugas-segment {{ $ti <= $tugasSelesai ? 'is-done' : 'is-pending' }}"></span>
            @endfor
        </div>
    </div>
</div>
