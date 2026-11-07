{{-- ==========================================================================
   Bento Mini Calendar Widget (Pembimbing Dashboard)
   Dynamic calendar widget with semi-oval capsule pills, today detection,
   outline selected state, and year selection popover.
   ========================================================================== --}}
@php
    $currentDate = \Carbon\Carbon::now();
    $monthName = $currentDate->translatedFormat('F');
    $yearNum = $currentDate->format('Y');
    if (empty($monthName) || str_contains(strtolower($monthName), '1970')) {
        $monthName = 'November';
        $yearNum = '2026';
    }
@endphp

<div class="bento-card bento-calendar-card sc-reveal-side sc-delay-1" id="bentoCalendarCard" data-scroll-reveal>
    <div class="bento-calendar-header">
        <button type="button" class="cal-nav-btn" id="calPrevBtn" aria-label="Bulan Sebelumnya">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>

        <div class="cal-title-container">
            <div class="cal-month-dropdown-wrap">
                <button type="button" class="cal-month-trigger-btn" id="calMonthTriggerBtn" aria-haspopup="true" aria-expanded="false" title="Pilih Bulan">
                    <span id="calMonthTitle">{{ $monthName }}</span>
                    <svg class="cal-dropdown-icon" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div class="cal-month-popover" id="calMonthPopover" role="menu" aria-label="Pilih Bulan" hidden>
                    <div class="cal-month-popover-header">
                        <span class="cal-month-popover-title">Pilih Bulan</span>
                    </div>
                    <div class="cal-month-grid" id="calMonthGrid">
                        {{-- Diisi secara dinamis oleh JavaScript (12 Bulan) --}}
                    </div>
                </div>
            </div>

            <div class="cal-year-dropdown-wrap">
                <button type="button" class="cal-year-trigger-btn" id="calYearTriggerBtn" aria-haspopup="true" aria-expanded="false" title="Pilih Tahun">
                    <span id="calYearVal">{{ $yearNum }}</span>
                    <svg class="cal-dropdown-icon" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div class="cal-year-popover" id="calYearPopover" role="menu" aria-label="Pilih Tahun" hidden>
                    <div class="cal-year-popover-header">
                        <button type="button" class="cal-year-jump-btn" id="calYearDecBtn" aria-label="5 Tahun Sebelumnya">&laquo;</button>
                        <span class="cal-year-popover-title">Pilih Tahun</span>
                        <button type="button" class="cal-year-jump-btn" id="calYearIncBtn" aria-label="5 Tahun Berikutnya">&raquo;</button>
                    </div>
                    <div class="cal-year-grid" id="calYearGrid">
                        {{-- Diisi secara dinamis oleh JavaScript --}}
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="cal-nav-btn" id="calNextBtn" aria-label="Bulan Berikutnya">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
    </div>

    <!-- Day Headers: S S R K J S M (Senin - Minggu) -->
    <div class="bento-calendar-days-row">
        <span>S</span>
        <span>S</span>
        <span>R</span>
        <span>K</span>
        <span>J</span>
        <span class="cal-day-weekend">S</span>
        <span class="cal-day-weekend">M</span>
    </div>

    <!-- Calendar Matrix Grid (Dikelola penuh & dirender dinamis oleh dashboard.js) -->
    <div class="bento-calendar-grid" id="bentoCalendarGrid" role="grid" aria-label="Tanggal Bulan Terpilih">
        {{-- Server fallback awal sebelum hidrasi JS --}}
        @php
            $todayNum = (int)$currentDate->format('j');
            $daysInMonth = (int)$currentDate->daysInMonth;
            $firstDayOfWeek = (int)$currentDate->copy()->startOfMonth()->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
            $prevMonthDays = (int)$currentDate->copy()->subMonth()->daysInMonth;
        @endphp

        @for ($p = $firstDayOfWeek - 1; $p > 0; $p--)
            <span class="cal-date date-muted" tabindex="-1">{{ $prevMonthDays - $p + 1 }}</span>
        @endfor

        @for ($d = 1; $d <= $daysInMonth; $d++)
            @php
                $isToday = ($d === $todayNum);
                $dayCol = ($firstDayOfWeek - 1 + $d - 1) % 7;
                $isWeekend = ($dayCol === 5 || $dayCol === 6);
            @endphp
            <button type="button"
                    class="cal-date {{ $isToday ? 'date-today' : '' }} {{ $isWeekend ? 'date-weekend' : '' }}"
                    data-date="{{ $d }}"
                    aria-label="Tanggal {{ $d }}">
                {{ $d }}
            </button>
        @endfor

        @php
            $totalCells = ($firstDayOfWeek - 1) + $daysInMonth;
            $nextFillers = (7 - ($totalCells % 7)) % 7;
        @endphp
        @for ($n = 1; $n <= $nextFillers; $n++)
            <span class="cal-date date-muted" tabindex="-1">{{ $n }}</span>
        @endfor
    </div>
</div>
