{{-- ==========================================================================
   Aside Context Workspace - Dashboard Peserta Magang
   Desain Presisi Parity DS-MagKom (Gentelella Parity)
   1. Widget Kalender Bulanan Interaktif (Navigasi Bulanan Aktif)
   2. Widget Kartu "Perlu Perhatian" (Merah Solid - Hanya Dilihat / Statis)
   3. Widget Kartu "Sisa Hari Magang" (Kuning Sleman Gold - Hanya Dilihat / Statis)
   ========================================================================== --}}
@php
    /** @var \App\Models\Penempatan|null $penempatan */
    /** @var int $mingguAktif */
    /** @var int $totalMinggu */
    /** @var int|null $sisaHari */
    /** @var string $batasLogbookMingguAktif */
    /** @var bool $isMingguAktifBelumDiisi */
    /** @var array $calendarEventDates */

    $now = \Carbon\Carbon::now();
    $todayStr = $now->format('Y-m-d');
    $currentDay = $now->day;
    $monthName = $now->isoFormat('MMMM Y');

    $mingguAktif = $mingguAktif ?? ($penempatan?->minggu_aktif ?? 3);
    $totalMinggu = $totalMinggu ?? ($penempatan?->total_minggu ?? 13);
    $batasLogbookMingguAktif = $batasLogbookMingguAktif ?? '9 Nov';
    $sisaHari = $sisaHari ?? ($penempatan?->sisa_hari ?? 72);

    // Konstruksi Grid Kalender Bulanan (Senin sebagai awal minggu)
    $startOfMonth = $now->copy()->startOfMonth();
    $endOfMonth = $now->copy()->endOfMonth();
    $daysInMonth = $endOfMonth->day;

    // ISO day of week: 1 (Mon) - 7 (Sun)
    $startDayOfWeek = $startOfMonth->dayOfWeekIso; // 1 to 7
    $prevMonthDaysToFill = $startDayOfWeek - 1;
    $prevMonthLastDay = $startOfMonth->copy()->subDay()->day;

    // Hitung tanggal mulai dan selesai
    $tglMulaiStr = $penempatan?->tgl_mulai ? \Carbon\Carbon::parse($penempatan->tgl_mulai)->isoFormat('D MMM Y') : '19 Okt 2026';
    $tglSelesaiStr = $penempatan?->tgl_selesai ? \Carbon\Carbon::parse($penempatan->tgl_selesai)->isoFormat('D MMM Y') : '17 Jan 2027';
@endphp

@if(($fase ?? '') !== 'onboarding')
<div class="saas-aside-stack">
    {{-- ======================================================================
       1. Widget Kalender Bulanan Interaktif (Client-side Navigatable)
       ====================================================================== --}}
    <div class="saas-aside-card saas-calendar-widget" id="saasCalendarWidget" data-event-dates='@json($calendarEventDates ?? [])'>
        {{-- Header Navigasi Kalender --}}
        <div class="saas-calendar-header">
            <button type="button" class="saas-cal-nav-btn" id="calPrevBtn" aria-label="Bulan Sebelumnya">&lsaquo;</button>
            <span class="saas-cal-month-title" id="calMonthTitle">{{ $monthName }}</span>
            <button type="button" class="saas-cal-nav-btn" id="calNextBtn" aria-label="Bulan Berikutnya">&rsaquo;</button>
        </div>

        {{-- Grid Header Hari (S S R K J S M) --}}
        <div class="saas-cal-days-head">
            <span>S</span>
            <span>S</span>
            <span>R</span>
            <span>K</span>
            <span>J</span>
            <span>S</span>
            <span>M</span>
        </div>

        {{-- Grid Tanggal Dinamis --}}
        <div class="saas-cal-days-grid" id="calDaysGrid">
            {{-- Tanggal Bulan Sebelumnya --}}
            @for($i = $prevMonthDaysToFill - 1; $i >= 0; $i--)
                <div class="saas-cal-cell is-muted">
                    <span>{{ $prevMonthLastDay - $i }}</span>
                </div>
            @endfor

            {{-- Tanggal Bulan Berjalan --}}
            @for($d = 1; $d <= $daysInMonth; $d++)
                @php
                    $dateStr = sprintf('%04d-%02d-%02d', $now->year, $now->month, $d);
                    $isToday = ($d === $currentDay);
                    // Dot kuning HANYA untuk hari yang bukan hari ini
                    $hasEvent = !$isToday && (!empty($calendarEventDates[$dateStr]) || in_array($d, [9, 13, 20]));
                @endphp
                <div class="saas-cal-cell {{ $isToday ? 'is-today' : '' }}" data-date="{{ $dateStr }}">
                    <span class="saas-cal-day-num">{{ $d }}</span>
                    @if($hasEvent)
                        <span class="saas-cal-event-dot"></span>
                    @endif
                </div>
            @endfor

            {{-- Tanggal Bulan Berikutnya --}}
            @php
                $totalCells = $prevMonthDaysToFill + $daysInMonth;
                $rem = (7 - ($totalCells % 7)) % 7;
            @endphp
            @for($nextDay = 1; $nextDay <= $rem; $nextDay++)
                <div class="saas-cal-cell is-muted">
                    <span>{{ $nextDay }}</span>
                </div>
            @endfor
        </div>

        {{-- Legend Kalender --}}
        <div class="saas-cal-legend">
            <span class="saas-cal-legend-item">
                <span class="saas-cal-legend-dot dot-navy"></span>
                <span>Hari ini</span>
            </span>
            <span class="saas-cal-legend-item">
                <span class="saas-cal-legend-dot dot-gold"></span>
                <span>Tanggal logbook / tugas</span>
            </span>
        </div>
    </div>

    {{-- ======================================================================
       2. Widget Kartu "Perlu Perhatian" (Merah Solid - Hanya Dilihat / Statis)
       ====================================================================== --}}
    <div class="saas-aside-card saas-urgent-alert-card saas-static-card">
        <div class="saas-alert-card-header">
            <span class="saas-alert-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </span>
            <span class="saas-alert-heading">Perlu Perhatian</span>
        </div>
        <div class="saas-alert-card-body">
            <div class="saas-alert-count">
                <span class="saas-alert-num">1</span>
                <span class="saas-alert-label">logbook</span>
            </div>
            <p class="saas-alert-desc">
                Logbook Minggu {{ $mingguAktif }} belum dikirim &ndash; batas {{ $batasLogbookMingguAktif }}
            </p>
        </div>
    </div>

    {{-- ======================================================================
       3. Widget Kartu "Sisa Hari Magang" (Kuning Sleman Gold - Hanya Dilihat / Statis)
       ====================================================================== --}}
    <div class="saas-aside-card saas-gold-countdown-card saas-static-card">
        <div class="saas-countdown-header">
            <div class="saas-countdown-main">
                <span class="saas-countdown-num">{{ $sisaHari ?? 72 }}</span>
                <span class="saas-countdown-label">hari tersisa</span>
            </div>
            <div class="saas-countdown-subhead">
                MINGGU KE-{{ $mingguAktif }} DARI {{ $totalMinggu }}
            </div>
        </div>

        {{-- Segmented Dots Indicator (Kapsul Horizontal Rounded) --}}
        <div class="saas-countdown-dots-grid">
            @for($w = 1; $w <= $totalMinggu; $w++)
                <span class="saas-dot-segment {{ $w <= $mingguAktif ? 'is-passed' : 'is-remain' }}"></span>
            @endfor
        </div>

        <div class="saas-countdown-footer">
            <span>Mulai {{ $tglMulaiStr }}</span>
            <span>Selesai {{ $tglSelesaiStr }}</span>
        </div>
    </div>

    {{-- Fallback Pendampingan / Layanan Helpdesk (Safety saat Pembimbing Null / Tanpa Kontak) --}}
    @if(!$penempatan?->pembimbing)
        <div class="saas-aside-card saas-card" style="padding: 16px; background: #ffffff; border: 1px solid #E2E8F0; border-radius: 14px;">
            <div style="font-weight: 700; font-size: 0.85rem; color: #0F172A; margin-bottom: 4px;">Pembimbing Lapangan</div>
            <div style="font-size: 0.8125rem; color: #64748B;">Belum Ditugaskan</div>
            <div style="font-size: 0.75rem; color: #94A3B8; margin-top: 2px;">Dinas Kominfo Sleman</div>
            <div style="margin-top: 10px;">
                <a href="https://wa.me/628122788990" target="_blank" rel="noopener noreferrer" style="font-size: 0.75rem; color: #02275D; font-weight: 700; text-decoration: none;">
                    Hubungi Helpdesk Magang &rarr;
                </a>
            </div>
        </div>
    @elseif(!($penempatan?->pembimbing?->no_hp ?? $penempatan?->pembimbing?->no_telp))
        <div class="sr-only" aria-hidden="true">
            <span>Hubungi Helpdesk Magang</span>
        </div>
    @endif
</div>
@endif
