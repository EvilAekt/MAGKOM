{{-- ==========================================================================
   Atmospheric Hero Banner - Dashboard Peserta Magang
   Desain DS-MagKom (Sleman Navy #1b3860 ke #152d4e, Soft-Simple-Elegan)
   Zero Alien SVG Waves, Subtle Border, Gold Sleman Accent
   ========================================================================== --}}
@php
    /** @var string $fase */
    /** @var \App\Models\Penempatan|null $penempatan */
    /** @var \App\Models\User $user */
@endphp
@if($fase === 'aktif' && $penempatan)
    <div class="saas-card saas-hero-banner">
        <div class="saas-hero-inner">
            <div class="saas-hero-left">
                <div class="saas-hero-badges">
                    <span class="saas-badge-status-active">
                        <span class="saas-dot-pulse"></span>
                        Status Penempatan: Magang Aktif
                    </span>
                    <span class="saas-badge-gold">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                        Pemkab Sleman
                    </span>
                    <span class="saas-hero-date">
                        {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                    </span>
                </div>

                <h1 class="saas-header-title">
                    Halo, {{ $user->nama ?? 'Peserta Magang' }}
                </h1>
                
                <p class="saas-header-desc">
                    Peserta Magang di <strong>{{ $penempatan->bidang->nama_bidang ?? 'Dinas Komunikasi dan Informatika Kabupaten Sleman' }}</strong>
                </p>

                <div class="saas-header-periode">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>Periode: <strong>{{ \Carbon\Carbon::parse($penempatan->tgl_mulai ?? $penempatan->tanggal_mulai)->isoFormat('D MMMM Y') }} &ndash; {{ \Carbon\Carbon::parse($penempatan->tgl_selesai ?? $penempatan->tanggal_selesai)->isoFormat('D MMMM Y') }}</strong></span>
                </div>
            </div>

            <div class="saas-hero-right">
                <div class="saas-hero-action-buttons">
                    <a href="{{ route('peserta.tugas.index') }}" class="saas-hero-btn-secondary">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span>Lihat Tugas</span>
                    </a>

                    <a href="{{ route('peserta.logbook.index') }}" class="saas-hero-btn-primary" title="Isi Logbook Mingguan">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span>+ Isi Logbook Mingguan</span>
                    </a>
                </div>

                <a href="{{ route('peserta.penempatan.surat-diterima.pratinjau', $penempatan) }}" target="_blank" rel="noopener noreferrer" class="saas-hero-link-sk">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Unduh SK Magang (PDF) &rarr;</span>
                </a>
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
                    <span class="saas-badge-portal-hero">
                        Portal Pendaftaran & Seleksi Magang
                    </span>
                    <span class="saas-hero-date">
                        {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                    </span>
                </div>

                <h1 class="saas-header-title">
                    Halo, {{ $user->nama ?? 'Peserta Magang' }}
                </h1>

                <p class="saas-header-desc">
                    Selamat datang di portal magang Dinas Komunikasi dan Informatika Kabupaten Sleman. Pantau alur pendaftaran, verifikasi berkas, dan hasil seleksi penempatan Anda secara transparan.
                </p>
            </div>

            <div class="saas-hero-right">
                <a href="{{ route('panduan.pdf') }}" target="_blank" rel="noopener noreferrer" class="saas-hero-btn-secondary">
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
