{{-- ==========================================================================
   Bento Hero Banner - Sleman Deep Navy (Dashboard Peserta)
   Reference: Bento Grid Parity with Pembimbing Dashboard
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
    $circumference = 213.6; // 2 * PI * 34
    $strokeDashoffset = $circumference - ($progressPct / 100 * $circumference);

    $tglMulaiStr = $penempatan?->tgl_mulai ? \Carbon\Carbon::parse($penempatan->tgl_mulai)->isoFormat('D MMM Y') : '19 Okt 2026';
    $tglSelesaiStr = $penempatan?->tgl_selesai ? \Carbon\Carbon::parse($penempatan->tgl_selesai)->isoFormat('D MMM Y') : '17 Jan 2027';
@endphp

@if($fase === 'aktif' && $penempatan)
    <div class="bento-hero-banner saas-hero-banner">
        <div class="bento-hero-content">
            <span class="bento-hero-tag">
                {{ $bidangLabel }} &bull; DISKOMINFO SLEMAN
            </span>

            <div class="bento-hero-badges">
                <span class="bento-badge-status-active" title="Status Penempatan: Magang Aktif">
                    <span class="bento-dot-pulse"></span>
                    <span>Diterima &bull; Magang Aktif</span>
                    <span class="sr-only">Status Penempatan: Magang Aktif</span>
                </span>
                @if($timKelompok)
                    <span class="bento-badge-kelompok">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#FFCB05" stroke-width="2.2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <span>Kelompok: <strong>{{ $timKelompok->nama_kelompok }}</strong></span>
                    </span>
                @endif
                <span class="bento-badge-gold">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span>Pemkab Sleman</span>
                </span>
            </div>

            <h1 class="bento-hero-title">
                Halo, {{ $user->nama_tampil ?? $user->nama ?? 'Peserta Magang' }}
            </h1>

            <p class="bento-hero-subtitle">
                Peserta Magang &bull; <strong>{{ $bidangTampil }}</strong> &bull; Periode: {{ $tglMulaiStr }} s/d {{ $tglSelesaiStr }}
            </p>

            <div class="bento-hero-actions">
                <a href="{{ route('peserta.tugas.index') }}" class="btn-bento-hero-cta">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Kerjakan Tugas</span>
                </a>
                <a href="{{ route('peserta.penempatan.surat-diterima', $penempatan) }}" class="btn-bento-hero-secondary" title="Unduh Dokumen Resmi SK Magang (PDF)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="12" y1="18" x2="12" y2="12"></line>
                        <polyline points="9 15 12 18 15 15"></polyline>
                    </svg>
                    <span>Unduh SK</span>
                </a>
            </div>
        </div>

        <div class="bento-hero-right">
            <div class="bento-hero-gauge-visual">
                <svg class="bento-hero-gauge-svg" viewBox="0 0 86 86">
                    <circle class="bento-hero-gauge-bg" cx="43" cy="43" r="34"></circle>
                    <circle class="bento-hero-gauge-bar" cx="43" cy="43" r="34"
                            style="stroke-dasharray: {{ $circumference }}; stroke-dashoffset: {{ $strokeDashoffset }};"></circle>
                </svg>
                <div class="bento-hero-gauge-text">{{ $mingguKe }}/{{ $totMinggu }}</div>
            </div>
            <div class="bento-hero-gauge-info">
                <span class="bento-hero-gauge-label">PROGRES MAGANG</span>
                <span class="bento-hero-gauge-val">Minggu ke-{{ $mingguKe }}</span>
                <span class="bento-hero-gauge-sub">dari {{ $totMinggu }} minggu ({{ $progressPct }}%)</span>
            </div>
        </div>
    </div>
@else
    <div class="bento-hero-banner saas-hero-banner">
        <div class="bento-hero-content">
            <div class="bento-hero-badges">
                <span class="bento-badge-gold">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    <span>Pemkab Sleman</span>
                </span>
                <span class="bento-badge-kelompok">Portal Pendaftaran & Seleksi Magang</span>
            </div>

            <h1 class="bento-hero-title">
                Halo, {{ $user->nama_tampil ?? $user->nama ?? 'Peserta Magang' }}
            </h1>

            <p class="bento-hero-subtitle">
                Selamat datang di portal terpadu Dinas Komunikasi dan Informatika Kabupaten Sleman. Pantau tahapan administrasi, kelengkapan berkas, dan hasil seleksi penempatan Anda di sini.
            </p>
        </div>
    </div>
@endif
