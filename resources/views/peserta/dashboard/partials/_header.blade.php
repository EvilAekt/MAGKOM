{{-- ==========================================================================
   Atmospheric Hero Banner - Dashboard Peserta Magang
   Desain DS-MagKom (Sleman Navy #02275D, Circular Progress Gauge, Gold Sleman)
   Tombol Utama: [+ Kerjakan Tugas] & [📄 Unduh SK]
   ========================================================================== --}}
@php
    /** @var string $fase */
    /** @var \App\Models\Penempatan|null $penempatan */
    /** @var \App\Models\User $user */
    $timKelompok = $kelompokMagang ?? $user->kelompokMagang;
    $namaBidang = $penempatan?->bidang?->nama_bidang ?? 'Aplikasi Informatika (Aptika)';
    $bidangTampil = str_contains(strtolower($namaBidang), 'aplikasi informatika') ? 'Bidang Aptika' : ('Bidang ' . $namaBidang);
    $bidangLabel = strtoupper($bidangTampil);
    $mingguKe = $mingguAktif ?? 1;
    $totMinggu = $totalMinggu ?? 13;
    $progressPct = min(100, (int) round(($mingguKe / max(1, $totMinggu)) * 100));
    $circumference = 213.6;
    $strokeDashoffset = $circumference - ($progressPct / 100 * $circumference);
@endphp

@if($fase === 'aktif' && $penempatan)
    <div class="saas-card saas-hero-banner">
        <div class="saas-hero-inner">
            <div class="saas-hero-left">
                <div class="saas-hero-pretitle">
                    BIDANG {{ $bidangLabel }} - DISKOMINFO SLEMAN
                </div>

                <div class="saas-hero-badges">
                    <span class="saas-badge-status-active" title="Status Penempatan: Magang Aktif">
                        <span class="saas-dot-pulse"></span>
                        Diterima &bull; Magang Aktif<span class="sr-only">Status Penempatan: Magang Aktif</span>
                    </span>
                    @if($timKelompok)
                        <span class="saas-badge-kelompok-hero">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#FFCB05" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <span>Kelompok: <strong>{{ $timKelompok->nama_kelompok }}</strong></span>
                        </span>
                    @endif
                    <span class="saas-badge-gold">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        Pemkab Sleman
                    </span>
                </div>

                <h1 class="saas-header-title">
                    Halo, {{ $user->nama_tampil ?? $user->nama ?? 'Peserta Magang' }}
                </h1>
                
                <p class="saas-header-desc">
                    Peserta Magang &bull; <strong>{{ $bidangTampil }}</strong>
                </p>

                <div class="saas-hero-actions-row">
                    <a href="{{ route('peserta.tugas.index') }}" class="saas-btn-action-gold">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Kerjakan Tugas</span>
                    </a>
                    <a href="{{ route('peserta.penempatan.surat-diterima', $penempatan) }}" class="saas-btn-action-sk" title="Unduh Dokumen Resmi SK Magang (PDF)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="12" y1="18" x2="12" y2="12"></line>
                            <polyline points="9 15 12 18 15 15"></polyline>
                        </svg>
                        <span>Unduh SK</span>
                    </a>
                </div>
            </div>

            <div class="saas-hero-right">
                {{-- Circular Gauge Progress Magang --}}
                <div class="saas-gauge-card">
                    <div class="saas-gauge-visual">
                        <svg class="saas-gauge-svg" width="86" height="86" viewBox="0 0 86 86">
                            <circle class="saas-gauge-bg" cx="43" cy="43" r="34" />
                            <circle class="saas-gauge-bar" cx="43" cy="43" r="34"
                                style="stroke-dasharray: {{ $circumference }}; stroke-dashoffset: {{ $strokeDashoffset }};" />
                        </svg>
                        <div class="saas-gauge-number">
                            {{ $mingguKe }}/{{ $totMinggu }}
                        </div>
                    </div>
                    <div class="saas-gauge-info">
                        <div class="saas-gauge-badge">PROGRES MAGANG</div>
                        <div class="saas-gauge-title">Minggu ke-{{ $mingguKe }}</div>
                        <div class="saas-gauge-sub">dari {{ $totMinggu }} minggu</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="saas-card saas-hero-banner">
        <div class="saas-hero-inner">
            <div class="saas-hero-left">
                <div class="saas-hero-badges">
                    <span class="saas-badge-gold">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        Pemkab Sleman
                    </span>
                    @if($timKelompok)
                        <span class="saas-badge-kelompok-hero">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#FFCB05" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                            <span>Kelompok: <strong>{{ $timKelompok->nama_kelompok }}</strong></span>
                        </span>
                    @else
                        <span class="saas-badge-portal-hero">
                            Portal Pendaftaran & Seleksi Magang
                        </span>
                    @endif
                </div>

                <h1 class="saas-header-title">
                    Halo, {{ $user->nama_tampil ?? $user->nama ?? 'Peserta Magang' }}
                </h1>

                <p class="saas-header-desc">
                    Selamat datang di portal magang Dinas Komunikasi dan Informatika Kabupaten Sleman. Pantau alur pendaftaran, verifikasi berkas, dan hasil seleksi penempatan Anda secara transparan.
                </p>
            </div>

            <div class="saas-hero-right">
                <a href="{{ route('panduan.pdf') }}" target="_blank" rel="noopener noreferrer" class="saas-btn-action-gold">
                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    <span>Unduh Panduan Magang &rarr;</span>
                </a>
            </div>
        </div>
    </div>
@endif
