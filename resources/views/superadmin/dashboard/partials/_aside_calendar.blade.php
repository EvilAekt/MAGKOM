{{-- ==========================================================================
   SuperAdmin Mini Calendar Widget (Aside Action Center)
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

<div class="sa-calendar-card" id="saCalendarCard" aria-label="Kalender Kerja Operasional">
    <div class="sa-calendar-header">
        <button type="button" class="sa-cal-nav-btn" id="saCalPrevBtn" aria-label="Bulan Sebelumnya">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>

        <div class="sa-cal-title-container">
            <div class="sa-cal-month-dropdown-wrap">
                <button type="button" class="sa-cal-month-trigger-btn" id="saCalMonthTriggerBtn" aria-haspopup="true" aria-expanded="false" title="Pilih Bulan">
                    <span id="saCalMonthTitle">{{ $monthName }}</span>
                    <svg class="sa-cal-dropdown-icon" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div class="sa-cal-month-popover" id="saCalMonthPopover" role="menu" aria-label="Pilih Bulan" hidden>
                    <div class="sa-cal-month-popover-header">
                        <span class="sa-cal-month-popover-title">Pilih Bulan</span>
                    </div>
                    <div class="sa-cal-month-grid" id="saCalMonthGrid">
                        {{-- Diisi secara dinamis oleh JavaScript (12 Bulan) --}}
                    </div>
                </div>
            </div>

            <div class="sa-cal-year-dropdown-wrap">
                <button type="button" class="sa-cal-year-trigger-btn" id="saCalYearTriggerBtn" aria-haspopup="true" aria-expanded="false" title="Pilih Tahun">
                    <span id="saCalYearVal">{{ $yearNum }}</span>
                    <svg class="sa-cal-dropdown-icon" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div class="sa-cal-year-popover" id="saCalYearPopover" role="menu" aria-label="Pilih Tahun" hidden>
                    <div class="sa-cal-year-popover-header">
                        <button type="button" class="sa-cal-year-jump-btn" id="saCalYearDecBtn" aria-label="5 Tahun Sebelumnya">&laquo;</button>
                        <span class="sa-cal-year-popover-title">Pilih Tahun</span>
                        <button type="button" class="sa-cal-year-jump-btn" id="saCalYearIncBtn" aria-label="5 Tahun Berikutnya">&raquo;</button>
                    </div>
                    <div class="sa-cal-year-grid" id="saCalYearGrid">
                        {{-- Diisi secara dinamis oleh JavaScript --}}
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="sa-cal-nav-btn" id="saCalNextBtn" aria-label="Bulan Berikutnya">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
    </div>

    <!-- Day Headers: S S R K J S M (Senin - Minggu) -->
    <div class="sa-calendar-days-row">
        <span>S</span>
        <span>S</span>
        <span>R</span>
        <span>K</span>
        <span>J</span>
        <span class="sa-day-weekend">S</span>
        <span class="sa-day-weekend">M</span>
    </div>

    <!-- Calendar Matrix Grid (Dikelola penuh & dirender dinamis oleh dashboard.js) -->
    <div class="sa-calendar-grid" id="saCalendarGrid" role="grid" aria-label="Tanggal Bulan Terpilih">
        {{-- Server fallback awal sebelum hidrasi JS --}}
        @php
            $todayNum = (int)$currentDate->format('j');
            $daysInMonth = (int)$currentDate->daysInMonth;
            $firstDayOfWeek = (int)$currentDate->copy()->startOfMonth()->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
            $prevMonthDays = (int)$currentDate->copy()->subMonth()->daysInMonth;
        @endphp

        @for ($p = $firstDayOfWeek - 1; $p > 0; $p--)
            <span class="sa-cal-date date-muted" tabindex="-1">{{ $prevMonthDays - $p + 1 }}</span>
        @endfor

        @for ($d = 1; $d <= $daysInMonth; $d++)
            @php
                $isToday = ($d === $todayNum);
                $dayCol = ($firstDayOfWeek - 1 + $d - 1) % 7;
                $isWeekend = ($dayCol === 5 || $dayCol === 6);
            @endphp
            <button type="button"
                    class="sa-cal-date {{ $isToday ? 'date-today' : '' }} {{ $isWeekend ? 'date-weekend' : '' }}"
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
            <span class="sa-cal-date date-muted" tabindex="-1">{{ $n }}</span>
        @endfor
    </div>
</div>
