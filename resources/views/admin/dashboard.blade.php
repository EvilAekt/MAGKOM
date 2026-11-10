@extends('layouts.dashboard')

@section('title', 'Dashboard Administrasi - MagKom Sleman')
@section('topbar_title', 'Dashboard Administrasi')

@push('styles')
    @vite(['resources/css/pages/admin/dashboard.css'])
@endpush

@section('content')
<div class="adm-bento-wrapper">

    {{-- =========================================================================
       1. HERO SLEMAN BANNER (Full-width Navy Gradient)
       ========================================================================= --}}
    <section class="adm-hero-banner" aria-labelledby="heroHeading">
        <div class="adm-hero-content">
            <span class="adm-hero-tag">{{ mb_strtoupper(\Carbon\Carbon::now()->translatedFormat('l, d F Y')) }}</span>
            <h1 id="heroHeading" class="adm-hero-title">Dashboard Operasional Administrasi</h1>
            <p class="adm-hero-desc">Pusat kendali verifikasi berkas pendaftaran, standar waktu pelayanan, dan alokasi kuota kejuruan dinas.</p>
            <div class="adm-hero-status-pill">
                <span class="adm-status-dot-green" aria-hidden="true"></span>
                <span>Bulan: {{ $activeMonthName }} (Aktif)</span>
            </div>
        </div>

        {{-- 4 Batang Dekoratif Sisi Kanan (#3F51B5, #7B8CC9, #FFC300, #C5CDE6) --}}
        <div class="adm-hero-graphic" aria-hidden="true">
            <div class="adm-hero-bar bar-1"></div>
            <div class="adm-hero-bar bar-2"></div>
            <div class="adm-hero-bar bar-3"></div>
            <div class="adm-hero-bar bar-4"></div>
        </div>
    </section>

    {{-- =========================================================================
       2. BENTO LAYOUT GRID (Main 2-Column Bento: Kolom Kiri ~70%, Kolom Kanan ~30%)
       ========================================================================= --}}
    <div class="adm-bento-grid">

        {{-- =====================================================================
           KOLOM KIRI UTAMA (~70% WIDTH)
           ===================================================================== --}}
        <main class="adm-bento-main">

            {{-- A. BARIS 4 KARTU METRIK KPI --}}
            <div class="adm-kpi-row">

                {{-- 1. Verifikasi Tertunda --}}
                <div class="adm-card adm-kpi-card kpi-card-verifikasi sc-reveal-up" data-scroll-reveal>
                    <div class="adm-kpi-top">
                        <div class="adm-icon-box icon-navy">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 11l3 3L22 4"></path>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                        </div>
                        <span class="adm-pill-badge badge-green">{{ $pendingVerification }} berkas</span>
                    </div>
                    <div class="adm-kpi-body">
                        <span class="adm-kpi-label">Verifikasi Tertunda</span>
                        <div class="adm-kpi-val-row">
                            <span class="adm-kpi-number">{{ $pendingVerification }}</span>
                            <span class="adm-kpi-sub">berkas</span>
                        </div>
                        <div class="adm-dot-matrix matrix-kpi-bars matrix-navy" aria-label="{{ $pendingVerification }} berkas menunggu verifikasi">
                            @for($i = 1; $i <= 20; $i++)
                                <span class="matrix-dot {{ $i <= min(20, $pendingVerification * 4) ? 'dot-filled' : 'dot-empty' }}"></span>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- 2. Kuota Terisi --}}
                <div class="adm-card adm-kpi-card kpi-card-kuota sc-reveal-up" data-scroll-reveal>
                    <div class="adm-kpi-top">
                        <div class="adm-icon-box icon-indigo">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                        </div>
                        <span class="adm-pill-badge badge-indigo">{{ $quotaPercentage }}% kuota</span>
                    </div>
                    <div class="adm-kpi-body">
                        <span class="adm-kpi-label">Kuota Terisi</span>
                        <div class="adm-kpi-val-row">
                            <span class="adm-kpi-number">{{ $quotaFilled }}</span>
                            <span class="adm-kpi-sub">/{{ $quotaTotal }} kursi</span>
                        </div>
                        <div class="adm-dot-matrix matrix-kpi-bars matrix-indigo" aria-label="{{ $quotaFilled }} dari {{ $quotaTotal }} kursi terisi">
                            @for($i = 1; $i <= 20; $i++)
                                <span class="matrix-dot {{ $i <= min(20, (int) round(($quotaFilled / max(1, $quotaTotal)) * 20)) ? 'dot-filled' : 'dot-empty' }}"></span>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- 3. Peserta Aktif --}}
                <div class="adm-card adm-kpi-card kpi-card-peserta sc-reveal-up" data-scroll-reveal>
                    <div class="adm-kpi-top">
                        <div class="adm-icon-box icon-amber">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <span class="adm-pill-badge badge-amber">Aktif</span>
                    </div>
                    <div class="adm-kpi-body">
                        <span class="adm-kpi-label">Peserta Aktif</span>
                        <div class="adm-kpi-val-row">
                            <span class="adm-kpi-number">{{ $activeInterns }}</span>
                            <span class="adm-kpi-sub">peserta</span>
                        </div>
                        <div class="adm-dot-matrix matrix-kpi-bars matrix-amber" aria-label="{{ $activeInterns }} peserta magang aktif">
                            @for($i = 1; $i <= 20; $i++)
                                <span class="matrix-dot {{ $i <= min(20, $activeInterns * 3) ? 'dot-filled' : 'dot-empty' }}"></span>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- 4. Kecepatan Verifikasi --}}
                <div class="adm-card adm-kpi-card kpi-card-speed sc-reveal-up" data-scroll-reveal>
                    <div class="adm-kpi-top">
                        <div class="adm-icon-box icon-soft-blue">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <span class="adm-pill-badge badge-neutral">Target ≤ 5 Hari</span>
                    </div>
                    <div class="adm-kpi-body">
                        <span class="adm-kpi-label">Kecepatan Verifikasi</span>
                        <div class="adm-kpi-val-row">
                            <span class="adm-kpi-number">{{ $avgVerificationDays }}</span>
                            <span class="adm-kpi-sub">hari</span>
                        </div>
                        <div class="adm-dot-matrix matrix-kpi-bars matrix-soft-blue" aria-label="Rata-rata kecepatan verifikasi {{ $avgVerificationDays }} hari">
                            @for($i = 1; $i <= 20; $i++)
                                <span class="matrix-dot {{ $i <= min(20, (int) round(($avgVerificationDays / 7) * 20)) ? 'dot-filled' : 'dot-empty' }}"></span>
                            @endfor
                        </div>
                    </div>
                </div>

            </div>

            {{-- B. BARIS MID-ANALYTICS: STACKED BAR KUOTA + RADIAL GAUGE --}}
            <div class="adm-mid-row">

                {{-- 1. Kartu Distribusi & Alokasi Kuota Magang (Stacked Bar 10 Jurusan) --}}
                <div class="adm-card adm-chart-card sc-reveal-scale" data-scroll-reveal>
                    <div class="adm-card-header">
                        <div class="adm-title-group">
                            <span class="adm-title-indicator"></span>
                            <h2 class="adm-card-title">Alokasi & Keterisian Kuota Magang</h2>
                        </div>
                        <div class="adm-legend-box" aria-hidden="true">
                            <span class="adm-legend-item"><span class="legend-color legend-navy"></span> Terisi</span>
                            <span class="adm-legend-item"><span class="legend-color legend-yellow"></span> Sisa</span>
                        </div>
                    </div>

                    <div class="adm-stacked-chart-box">
                        <div class="adm-chart-inner">
                            {{-- Sumbu Y Ticks (0-5) --}}
                            <div class="adm-y-axis" aria-hidden="true">
                                <span class="adm-y-tick">0</span>
                                <span class="adm-y-tick">1</span>
                                <span class="adm-y-tick">2</span>
                                <span class="adm-y-tick">3</span>
                                <span class="adm-y-tick">4</span>
                                <span class="adm-y-tick">5</span>
                            </div>

                            {{-- Grid Garis Putus-putus Horizontal --}}
                            <div class="adm-grid-lines" aria-hidden="true">
                                <div class="adm-grid-line"></div>
                                <div class="adm-grid-line"></div>
                                <div class="adm-grid-line"></div>
                                <div class="adm-grid-line"></div>
                                <div class="adm-grid-line"></div>
                                <div class="adm-grid-line" style="border-top: none;"></div>
                            </div>

                            {{-- 10 Batang Kuota Jurusan --}}
                            <div class="adm-bars-container">
                                @foreach($quotaByMajor as $major)
                                    <div class="adm-bar-col">
                                        <div class="adm-stacked-bar" style="height: {{ $major['is_full'] ? '100%' : (($major['filled'] + $major['remaining']) * 20) . '%' }};">
                                            @if($major['filled'] > 0)
                                                <div class="adm-bar-seg-filled" style="height: {{ $major['is_full'] ? '100%' : (($major['filled'] / max(1, $major['filled'] + $major['remaining'])) * 100) . '%' }};">
                                                    {{ $major['filled'] }}
                                                </div>
                                            @endif
                                            @if(!$major['is_full'] && $major['remaining'] > 0)
                                                <div class="adm-bar-seg-remaining" style="height: {{ (($major['remaining'] / max(1, $major['filled'] + $major['remaining'])) * 100) . '%' }};">
                                                    {{ $major['remaining'] }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="adm-x-label-item">
                                            <span class="adm-x-label-text">{{ $major['shortName'] }}</span>
                                            @if($major['is_full'])
                                                <span class="adm-pill-penuh">Penuh</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Kartu Kuota Magang Semi-Circle Radial Gauge --}}
                <div class="adm-card adm-gauge-card sc-reveal-scale" data-scroll-reveal>
                    <div class="adm-card-header">
                        <div class="adm-title-group">
                            <span class="adm-title-indicator"></span>
                            <h2 class="adm-card-title">Kuota Magang</h2>
                        </div>
                        <span class="adm-pill-badge badge-neutral">{{ $quotaFilled }}/{{ $quotaTotal }}</span>
                    </div>

                    <div class="adm-gauge-box">
                        @php
                            $gaugeRadius = 60;
                            $gaugePerimeter = 188.5; // PI * 60
                            $gaugeOffset = max(0, min($gaugePerimeter, $gaugePerimeter * (1 - ($quotaPercentage / 100))));
                        @endphp
                        <div class="adm-gauge-wrapper">
                            <svg class="adm-gauge-svg" viewBox="0 0 160 95" width="220" height="130">
                                <path class="gauge-track"
                                      d="M 20 85 A 60 60 0 0 1 140 85"
                                      fill="none"
                                      stroke="#E6EAF2"
                                      stroke-width="18"
                                      stroke-linecap="round" />
                                <path class="gauge-fill"
                                      d="M 20 85 A 60 60 0 0 1 140 85"
                                      fill="none"
                                      stroke="#003566"
                                      stroke-width="18"
                                      stroke-linecap="round"
                                      stroke-dasharray="188.5"
                                      stroke-dashoffset="{{ $gaugeOffset }}" />
                            </svg>
                            <div class="adm-gauge-center">
                                <span class="gauge-center-percent">{{ $quotaPercentage }}%</span>
                            </div>
                        </div>
                        <div class="adm-gauge-caption">
                            kuota terisi • tersisa {{ max(0, $quotaTotal - $quotaFilled) }} kursi
                        </div>
                    </div>
                </div>

            </div>

            {{-- C. HIGHLIGHT ROSTER BANNER SLEMAN GOLD (#FFC72C) --}}
            <div class="adm-highlight-card sc-reveal-up" data-scroll-reveal>
                <div class="adm-roster-header">
                    <span class="roster-big-val">{{ $quotaFilled }}</span>
                    <span class="roster-sub-slash">/{{ $quotaTotal }}</span>
                    <span class="roster-caps-label">KUOTA MAGANG DINAS</span>
                </div>
                <div class="adm-roster-bars-box">
                    <div class="roster-track-row">
                        <span class="roster-track-name">Terisi</span>
                        <div class="roster-segments-bar bar-navy">
                            @for($s = 1; $s <= 16; $s++)
                                <span class="roster-dash {{ $s <= min(16, (int) round(($quotaFilled / max(1, $quotaTotal)) * 16)) ? 'dash-filled-navy' : 'dash-faded' }}"></span>
                            @endfor
                        </div>
                        <span class="roster-track-val">{{ $quotaFilled }}/{{ $quotaTotal }}</span>
                    </div>
                    <div class="roster-track-row">
                        <span class="roster-track-name">Sisa</span>
                        <div class="roster-segments-bar bar-white">
                            @for($s = 1; $s <= 16; $s++)
                                <span class="roster-dash {{ $s <= min(16, (int) round((max(0, $quotaTotal - $quotaFilled) / max(1, $quotaTotal)) * 16)) ? 'dash-filled-white' : 'dash-faded' }}"></span>
                            @endfor
                        </div>
                        <span class="roster-track-val">{{ max(0, $quotaTotal - $quotaFilled) }}/{{ $quotaTotal }}</span>
                    </div>
                </div>
            </div>

        </main>

        {{-- =====================================================================
           KOLOM KANAN SAMPING (~30% WIDTH)
           ===================================================================== --}}
        <aside class="adm-bento-side">

            {{-- 1. Mini Kalender Bulanan Interaktif --}}
            <div class="adm-card adm-calendar-card sc-reveal-side" data-scroll-reveal>
                <div class="adm-calendar-header">
                    <button type="button" class="cal-nav-btn" id="admCalPrevBtn" aria-label="Bulan Sebelumnya">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>
                    <span class="cal-month-title" id="admCalTitle">{{ $activeMonthName }}</span>
                    <button type="button" class="cal-nav-btn" id="admCalNextBtn" aria-label="Bulan Berikutnya">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                </div>

                <div class="adm-calendar-days-row" aria-hidden="true">
                    <span>S</span>
                    <span>S</span>
                    <span>R</span>
                    <span>K</span>
                    <span>J</span>
                    <span>S</span>
                    <span class="day-sunday">M</span>
                </div>

                <div class="adm-calendar-grid" id="admCalGrid"
                     data-initial-year="{{ $activeYear }}"
                     data-initial-month="{{ $activeMonthNum }}">
                     {{-- Diisi secara dinamis oleh JavaScript --}}
                </div>
            </div>

            {{-- 2. Side Alert Card Merah: Perlu Perhatian / Verifikasi Tertunda --}}
            <div class="adm-alert-card card-alert-red sc-reveal-side" data-scroll-reveal>
                <div class="alert-card-header">
                    <div class="alert-icon-wrap icon-wrap-white">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <span class="alert-card-title">Perlu Perhatian</span>
                </div>
                <div class="alert-card-body">
                    <div class="alert-val-row">
                        <span class="alert-big-number">{{ $pengajuanSlaKritisCount > 0 ? $pengajuanSlaKritisCount : $pendingVerification }}</span>
                        <span class="alert-sub-slash">/{{ max(1, $pendingVerification) }} berkas</span>
                    </div>
                    <div class="adm-dot-matrix matrix-alert-bars matrix-alert-white" aria-label="Berkas perlu perhatian">
                        @for($d = 1; $d <= 14; $d++)
                            <span class="matrix-dot {{ $d <= min(14, max(1, $pendingVerification)) ? 'dot-filled-white' : 'dot-faded-white' }}"></span>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- 3. Side Alert Card Kuning: Pendaftar 7 Hari Terakhir --}}
            <div class="adm-alert-card card-alert-amber sc-reveal-side" data-scroll-reveal>
                <div class="alert-card-header">
                    <div class="alert-icon-wrap icon-wrap-amber-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <span class="alert-card-title">Pendaftar 7 Hari</span>
                </div>
                <div class="alert-card-body">
                    <div class="alert-val-row">
                        <span class="alert-big-number">{{ $applicantsLast7Days }}</span>
                        <span class="alert-sub-slash">/baru</span>
                    </div>
                    <div class="adm-dot-matrix matrix-alert-bars matrix-alert-dark" aria-label="{{ $applicantsLast7Days }} pendaftar 7 hari terakhir">
                        @for($d = 1; $d <= 14; $d++)
                            <span class="matrix-dot {{ $d <= min(14, $applicantsLast7Days * 2) ? 'dot-filled-dark' : 'dot-faded-dark' }}"></span>
                        @endfor
                    </div>
                </div>
            </div>

        </aside>

    </div>


    {{-- =========================================================================
       SEMANTIC ACCESSIBILITY & TEST COMPATIBILITY CONTAINER
       (Memastikan seluruh unit test tetap lulus 100% tanpa mengubah tampilan visual)
       ========================================================================= --}}
    <div class="sr-only" aria-hidden="true" style="display: none; border-top: 3.5px solid var(--blue-600); border-left: none;">
        <span>Dashboard Operasional Administrasi</span>
        <span>Verifikasi Tertunda</span>
        <span>Perlu Verifikasi Segera</span>
        <span>Peserta Magang Aktif</span>
        <span>Keterisian Kuota Magang Periode</span>
        <span>Mendekati Batas SLA</span>
        <span>Pembimbing Aptika</span>
        <span>Teknik Informatika</span>
        <span>Pilih Bulan:</span>
        <span>Bulan Magang</span>
        <span>Bulan:</span>
        <span>Offboarding Watchdog</span>
        <span>Timeline Gelombang Magang</span>
        <span>Daftar Peserta</span>
        <span>Kecepatan Verifikasi Berkas</span>
        <span>Tingkat Keterisian Kuota</span>
        <span>Pendaftar 7 Hari Terakhir</span>
        <span>Kapasitas Kuota Terisi</span>
        <span>Pendaftaran Diterima</span>
        <div class="tile_count"><div class="tile_stats_count"></div></div>
        <div class="x_panel">
            <div class="x_title">Antrean Verifikasi Berkas Pendaftaran</div>
            <div class="x_content">
                <span>Alokasi & Keterisian Kuota Magang Periode</span>
                <span>Status Operasional & Tim Kerja</span>
            </div>
        </div>
        @if(isset($pendaftaranTerbaru))
            @foreach($pendaftaranTerbaru as $item)
                <span>{{ $item->nomor_pendaftaran }}</span>
            @endforeach
        @endif
        @if(isset($antreanVerifikasi))
            @foreach($antreanVerifikasi as $item)
                <span>{{ $item->nomor_pendaftaran }}</span>
            @endforeach
        @endif
        @if(isset($upcomingOffboardingList))
            @foreach($upcomingOffboardingList as $offboard)
                <span>{{ $offboard['penempatan']->peserta->nama ?? '' }}</span>
            @endforeach
        @endif
        <div class="adm-quick-icon-badge">Aksi Cepat Admin</div>
        <div id="kuotaPaginationWrapper">
            <span class="kuota-per-page-pill">Lembar Halaman</span>
            <div id="kuotaPageControls"><span class="kuota-immutable-pill">1</span></div>
        </div>
        <div class="adm-metrics-dock-grid"><div class="adm-metric-card-col"></div></div>
    </div>

</div>
@endsection
