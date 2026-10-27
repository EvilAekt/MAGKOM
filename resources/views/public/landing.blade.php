<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('Magang Kominfo Sleman 2026 – Pendaftaran MAGKOM') }}</title>
    <meta name="description" content="{{ __('Daftar magang di Diskominfo Sleman: lihat persyaratan, kuota per bidang, dan tahapan pendaftaran.') }}">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ __('Magang Kominfo Sleman 2026') }}">
    <meta property="og:description" content="{{ __('Persyaratan, kuota per bidang, dan tahapan pendaftaran magang Diskominfo Sleman.') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Favicon Resmi Sleman / MagKom -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

    <!-- Google Fonts: Lexend & Source Sans 3 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&family=Source+Sans+3:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Stylesheet Scoped Modern Landing -->
    <link rel="stylesheet" href="{{ asset('css/landing-modern.css') }}?v={{ filemtime(public_path('css/landing-modern.css')) }}">
</head>
<body class="magkom-landing-body">

    <!-- =======================================================================
         1. STICKY HEADER
         ======================================================================= -->
    <header class="landing-header">
        <div class="landing-header-container">
            <a href="{{ route('landing') }}" class="landing-brand" title="Dinas Komunikasi dan Informatika Kabupaten Sleman">
                @include('partials.logo-sleman', ['class' => 'brand-logo-img', 'width' => '38', 'height' => '38', 'style' => 'width: 38px; height: 38px; object-fit: contain;'])
                <span class="brand-text">
                    <span class="brand-name">DISKOMINFO SLEMAN</span>
                    <span class="brand-sub">Portal Magang · MAGKOM</span>
                </span>
            </a>

            <nav class="landing-nav-links" aria-label="Navigasi Utama">
                <a href="#beranda" class="landing-nav-link">Beranda</a>
                <a href="#kuota" class="landing-nav-link">Kuota</a>
                <a href="#tahapan" class="landing-nav-link">Tahapan</a>
                <a href="{{ route('panduan-alur') }}" class="landing-nav-link">Peta Alur</a>
                <a href="#faq" class="landing-nav-link">FAQ</a>
            </nav>

            <div class="landing-auth-group">
                @guest
                    <a href="{{ route('login') }}" class="btn-header-login">Login</a>
                    <a href="{{ route('register') }}" class="btn-header-register">Daftar</a>
                @else
                    @php
                        $user = auth()->user();
                        $dashboardRoute = match($user?->role?->value ?? ($user?->role instanceof \App\Enums\RoleEnum ? $user->role->value : 'peserta')) {
                            'superadmin' => route('superadmin.dashboard'),
                            'admin' => route('admin.dashboard'),
                            'pembimbing' => route('pembimbing.dashboard'),
                            default => route('peserta.dashboard'),
                        };
                    @endphp
                    <a href="{{ $dashboardRoute }}" class="btn-header-register" style="display: inline-flex; align-items: center; gap: 6px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                @endguest
            </div>
        </div>
    </header>

    <!-- =======================================================================
         2. HERO / BERANDA SECTION
         ======================================================================= -->
    <main>
        <section id="beranda" class="landing-section">
            <div class="hero-slider-wrapper" id="heroSliderWrapper">
                <!-- Floating Side Navigation Buttons (Modern, No-Hover) -->
                <button type="button" id="btnHeroSidePrev" class="hero-side-nav-btn prev" aria-label="Slide sebelumnya">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>
                <button type="button" id="btnHeroSideNext" class="hero-side-nav-btn next" aria-label="Slide selanjutnya">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>

                <!-- Slider Track Container -->
                <div class="hero-slider-track-container">
                    <div class="hero-slider-track" id="heroSliderTrack">
                        <!-- SLIDE 1: Info Pendaftaran (Existing 3-Column Hero Box) -->
                        <div class="hero-slide-item">
                            <div class="hero-box">
                                <!-- Kolom 1: Overview & CTA -->
                                <div>
                                    <p class="hero-kicker">Kabupaten Sleman</p>
                                    <h1 class="hero-title">
                                        Magang <span class="text-accent">Kominfo</span><br>
                                        Periode II
                                    </h1>
                                    <div class="hero-year-badge">
                                        <span class="hero-year-arrows">»»»»</span> 2026
                                    </div>
                                    <p class="hero-period-info">
                                        Pendaftaran 1–15 Okt 2026 · mulai 2 Nov 2026
                                    </p>
                                    <a href="{{ route('register') }}" class="btn-hero-cta">
                                        Daftar Sekarang
                                    </a>
                                </div>

                                <!-- Kolom 2: Persyaratan Umum -->
                                <div>
                                    <h2 class="hero-subhead">
                                        Persyaratan<br>
                                        <span class="text-accent">Umum</span>
                                    </h2>
                                    <ul class="hero-list">
                                        <li>Siswa SMK aktif atau mahasiswa aktif D3/D4/S1 dengan jurusan yang relevan</li>
                                        <li>Bersedia magang secara luring (WFO) di kantor Diskominfo Sleman</li>
                                        <li>Mengikuti jadwal dan durasi magang yang telah ditentukan</li>
                                    </ul>
                                </div>

                                <!-- Kolom 3: Dokumen Pendaftaran -->
                                <div>
                                    <h2 class="hero-subhead">
                                        Dokumen<br>
                                        <span class="text-accent">Pendaftaran</span>
                                    </h2>
                                    <ul class="hero-list">
                                        <li>Surat Pengantar Sekolah / Perguruan Tinggi</li>
                                        <li>Curriculum Vitae (CV)</li>
                                        <li>Transkrip / Rapor Nilai Terakhir</li>
                                        <li>Pas Foto</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- SLIDE 2: Dokumentasi Foto Penuh Resmi Diskominfo Sleman -->
                        <div class="hero-slide-item">
                            <div class="hero-full-image-slide" id="heroFullImageSlide">
                                <img src="{{ asset('images/hero_dokumentasi_diskominfo.png') }}" 
                                     alt="Dokumentasi Resmi Diskominfo Kabupaten Sleman" 
                                     class="hero-full-img" 
                                     id="heroFullImg"
                                     loading="lazy">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slider Indicator Dots (2 Slides: Info & Foto) -->
                <div class="hero-slider-dots" id="heroSliderDots">
                    <button type="button" class="hero-dot-btn active" data-slide-index="0" aria-label="Ke slide 1 (Informasi Pendaftaran)"></button>
                    <button type="button" class="hero-dot-btn" data-slide-index="1" aria-label="Ke slide 2 (Dokumentasi Diskominfo)"></button>
                </div>
            </div>
        </section>

        <!-- =======================================================================
             3. KUOTA PER JURUSAN SECTION (Sinkron Database MagKom)
             ======================================================================= -->
        <section id="kuota" class="landing-section" style="padding-top: 3.5rem; padding-bottom: 3.5rem;">
            <h2 class="section-heading-lg">Ketersediaan Kuota Magang</h2>
            <p class="section-desc-sub">Pilih jenjang pendidikan dan bulan mulai magang untuk melihat kuota jurusan yang tersedia.</p>

            <div class="kuota-questions-grid">
                <!-- Pertanyaan 1: Jenjang -->
                <div>
                    <p class="question-label">1. Kamu siapa?</p>
                    <div class="pill-group" id="kuotaJenjangGroup">
                        <button type="button" class="pill-btn active" data-jenjang-pill="smk">Siswa SMK</button>
                        <button type="button" class="pill-btn" data-jenjang-pill="mahasiswa">Mahasiswa (D3/S1)</button>
                    </div>
                </div>

                <!-- Pertanyaan 2: Bulan -->
                <div>
                    <p class="question-label">2. Mau mulai kapan?</p>
                    <div id="kuotaBulanPillContainer" class="pill-group">
                        @forelse($bulanTampil as $bIdx => $bulan)
                            @php
                                $namaBulanStr = \Carbon\CarbonImmutable::parse($bulan->bulan_mulai)->translatedFormat('F Y');
                            @endphp
                            <button type="button" 
                                    class="pill-btn {{ $bIdx === 0 ? 'active' : '' }}" 
                                    data-bulan-pill="{{ $bulan->id }}"
                                    data-bulan-name="{{ $namaBulanStr }}">
                                {{ $namaBulanStr }}
                            </button>
                        @empty
                            <span style="color: var(--muted-text); font-size: 0.9rem;">Belum ada periode magang aktif.</span>
                        @endforelse
                    </div>
                </div>

                <!-- Pertanyaan 3: Magang sama siapa? -->
                <div>
                    <p class="question-label">3. Magang sama siapa?</p>
                    <div class="pill-group" id="kuotaModeGroup">
                        <button type="button" class="pill-btn active" data-mode-pill="mandiri">Sendiri (1 Org)</button>
                        <button type="button" class="pill-btn" data-mode-pill="kelompok">Kelompok (Tim)</button>
                    </div>
                </div>
            </div>

            <!-- Sub-pilihan Jumlah Tim (Hanya muncul saat Bareng Tim / Kelompok dipilih) -->
            <div id="kuotaTeamSizeWrapper" class="kuota-team-size-panel" style="display: none;">
                <div class="team-size-content">
                    <span class="team-size-label">Berapa orang dalam tim kamu?</span>
                    <div class="team-size-pills" id="kuotaMemberPills">
                        <button type="button" class="team-pill-btn active" data-count="2">2 Orang</button>
                        <button type="button" class="team-pill-btn" data-count="3">3 Orang</button>
                        <button type="button" class="team-pill-btn" data-count="4">4 Orang</button>
                    </div>
                </div>
                <div class="team-edu-note">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <span><strong>Info Tim:</strong> Cukup 1 perwakilan/ketua yang mendaftar. Data rekan tim dimasukkan langsung di form.</span>
                </div>
            </div>

            <!-- Total Seat Ring Meter Banner -->
            <div class="kuota-highlight-banner">
                <div class="gauge-wrapper">
                    <svg width="110" height="110" viewBox="0 0 120 120" class="gauge-svg">
                        <circle cx="60" cy="60" r="52" fill="none" stroke="currentColor" stroke-opacity="0.18" stroke-width="12"></circle>
                        <circle id="kuotaGaugeProgress" cx="60" cy="60" r="52" fill="none" stroke="var(--gold-bright)" stroke-width="12" stroke-linecap="round" stroke-dasharray="326.72" stroke-dashoffset="100" style="transition: stroke-dashoffset 0.8s ease;"></circle>
                    </svg>
                    <span id="kuotaGaugeNumber" class="gauge-center-text">0</span>
                </div>
                <p id="kuotaGaugeDesc" class="gauge-desc">
                    Memuat informasi ketersediaan kuota...
                </p>
            </div>

            <!-- Kuota Carousel Wrapper (Floating Side Nav) -->
            <div class="kuota-carousel-wrapper">
                <button type="button" id="btnCarouselPrev" class="kuota-side-arrow-btn prev" aria-label="Geser ke kiri" title="Geser ke kiri" disabled>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </button>

                <!-- Carousel Track: Rendered from Database -->
                <div id="kuotaCarouselTrack" class="kuota-carousel-track">
                @php
                    $pertamaBulanId = $bulanTampil->first()->id ?? null;
                @endphp

                <!-- KARTU JURUSAN SMK -->
                @foreach($bulanTampil as $bulan)
                    @foreach($jurusanSmk as $jurusan)
                        @php
                            $data = $matriksKetersediaan[$jurusan->id][$bulan->id] ?? null;
                            $status = $data['status'] ?? \App\Enums\StatusKetersediaanEnum::Tutup;
                            $sisa = $data['sisa_kuota'] ?? 0;
                            $total = $data['total_kuota'] ?? 0;
                            $persen = $data['persen_terisi'] ?? 0;
                            $isWaiting = $data['is_waiting_list'] ?? false;
                            $isAvailable = ($status === \App\Enums\StatusKetersediaanEnum::Tersedia);
                            $namaBulanStr = \Carbon\CarbonImmutable::parse($bulan->bulan_mulai)->translatedFormat('F Y');

                            // Badge status
                            if ($isAvailable && !$isWaiting && $sisa > 0) {
                                $badgeClass = ($total > 0 && ($sisa / $total) <= 0.34) ? 'status-hampir' : 'status-tersedia';
                                $badgeLabel = ($total > 0 && ($sisa / $total) <= 0.34) ? 'Tinggal sedikit' : 'Tersedia';
                                $barClass = ($total > 0 && ($sisa / $total) <= 0.34) ? 'bar-hampir' : 'bar-tersedia';
                            } elseif ($isWaiting || ($isAvailable && $sisa === 0)) {
                                $badgeClass = 'status-penuh';
                                $badgeLabel = 'Waiting List';
                                $barClass = 'bar-penuh';
                            } else {
                                $badgeClass = 'status-penuh';
                                $badgeLabel = 'Tutup';
                                $barClass = 'bar-penuh';
                            }
                            $isAktifAwal = ($bulan->id === $pertamaBulanId);
                        @endphp
                        <div class="kuota-card {{ $sisa === 0 ? 'is-penuh' : '' }}"
                             data-jenjang="smk"
                             data-bulan-id="{{ $bulan->id }}"
                             data-jurusan-id="{{ $jurusan->id }}"
                             data-sisa="{{ $sisa }}"
                             data-total="{{ $total }}"
                             data-is-waiting="{{ $isWaiting ? '1' : '0' }}"
                             data-is-available="{{ $isAvailable ? '1' : '0' }}"
                             style="{{ $isAktifAwal ? '' : 'display: none;' }}">
                            <div class="kuota-card-header">
                                <div class="kuota-icon-badge">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                        <path d="M6 8h.01M10 8h.01M14 8h.01"></path>
                                    </svg>
                                </div>
                                <h3 class="kuota-bidang-name">{{ $jurusan->nama_jurusan }}</h3>
                                <span class="kuota-status-pill {{ $badgeClass }}">{{ $badgeLabel }}</span>
                            </div>

                            <div class="kuota-stat-line">
                                <span class="kuota-big-number">{{ $sisa }}</span>
                                <span class="kuota-stat-label">kursi tersisa</span>
                                <span class="kuota-stat-total">dari {{ $total }} kursi</span>
                            </div>

                            <div class="kuota-progress-bar-bg">
                                <div class="kuota-progress-bar-fill {{ $barClass }}" style="width: {{ $persen }}%"></div>
                            </div>

                            <p class="kuota-card-desc">{{ $jurusan->deskripsi ?? 'Kompetensi kejuruan tingkat menengah dengan bimbingan teknis intensif.' }}</p>

                            @if($jurusan->bidang->isNotEmpty())
                                <div class="kuota-jurusan-wrap">
                                    <span class="jurusan-lead">Penempatan:</span>
                                    @foreach($jurusan->bidang as $bdg)
                                        <span class="jurusan-tag">{{ $bdg->nama_bidang }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="kuota-card-footer">
                                @if($isAvailable && !$isWaiting && $sisa > 0)
                                    <a href="{{ url('/register?bulan=' . $bulan->id . '&jurusan=' . $jurusan->id . '&tipe=mandiri&jumlah=1') }}" 
                                       class="btn-card-action btn-primary-action btn-card-cta"
                                       data-base-url="{{ url('/register') }}"
                                       data-bulan-id="{{ $bulan->id }}"
                                       data-jurusan-id="{{ $jurusan->id }}"
                                       data-sisa="{{ $sisa }}"
                                       data-total="{{ $total }}"
                                       data-is-waiting="0"
                                       data-is-available="1">
                                        Daftar Sekarang ➔
                                    </a>
                                @elseif($isWaiting || ($isAvailable && $sisa === 0))
                                    <a href="{{ url('/register?bulan=' . $bulan->id . '&jurusan=' . $jurusan->id . '&tipe=mandiri&jumlah=1') }}" 
                                       class="btn-card-action btn-waitlist-action btn-card-cta"
                                       data-base-url="{{ url('/register') }}"
                                       data-bulan-id="{{ $bulan->id }}"
                                       data-jurusan-id="{{ $jurusan->id }}"
                                       data-sisa="{{ $sisa }}"
                                       data-total="{{ $total }}"
                                       data-is-waiting="1"
                                       data-is-available="{{ $isAvailable ? '1' : '0' }}">
                                        Daftar Waiting List ➔
                                    </a>
                                @else
                                    <button type="button" class="btn-card-action btn-disabled" disabled>
                                        Pendaftaran Ditutup
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endforeach

                <!-- KARTU JURUSAN MAHASISWA -->
                @foreach($bulanTampil as $bulan)
                    @foreach($jurusanMahasiswa as $jurusan)
                        @php
                            $data = $matriksKetersediaan[$jurusan->id][$bulan->id] ?? null;
                            $status = $data['status'] ?? \App\Enums\StatusKetersediaanEnum::Tutup;
                            $sisa = $data['sisa_kuota'] ?? 0;
                            $total = $data['total_kuota'] ?? 0;
                            $persen = $data['persen_terisi'] ?? 0;
                            $isWaiting = $data['is_waiting_list'] ?? false;
                            $isAvailable = ($status === \App\Enums\StatusKetersediaanEnum::Tersedia);
                            $namaBulanStr = \Carbon\CarbonImmutable::parse($bulan->bulan_mulai)->translatedFormat('F Y');

                            // Badge status
                            if ($isAvailable && !$isWaiting && $sisa > 0) {
                                $badgeClass = ($total > 0 && ($sisa / $total) <= 0.34) ? 'status-hampir' : 'status-tersedia';
                                $badgeLabel = ($total > 0 && ($sisa / $total) <= 0.34) ? 'Tinggal sedikit' : 'Tersedia';
                                $barClass = ($total > 0 && ($sisa / $total) <= 0.34) ? 'bar-hampir' : 'bar-tersedia';
                            } elseif ($isWaiting || ($isAvailable && $sisa === 0)) {
                                $badgeClass = 'status-penuh';
                                $badgeLabel = 'Waiting List';
                                $barClass = 'bar-penuh';
                            } else {
                                $badgeClass = 'status-penuh';
                                $badgeLabel = 'Tutup';
                                $barClass = 'bar-penuh';
                            }
                        @endphp
                        <div class="kuota-card {{ $sisa === 0 ? 'is-penuh' : '' }}"
                             data-jenjang="mahasiswa"
                             data-bulan-id="{{ $bulan->id }}"
                             data-jurusan-id="{{ $jurusan->id }}"
                             data-sisa="{{ $sisa }}"
                             data-total="{{ $total }}"
                             data-is-waiting="{{ $isWaiting ? '1' : '0' }}"
                             data-is-available="{{ $isAvailable ? '1' : '0' }}"
                             style="display: none;">
                            <div class="kuota-card-header">
                                <div class="kuota-icon-badge" style="background-color: #EFF6FF; color: #1D4ED8;">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                        <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                                    </svg>
                                </div>
                                <h3 class="kuota-bidang-name">{{ $jurusan->nama_jurusan }}</h3>
                                <span class="kuota-status-pill {{ $badgeClass }}">{{ $badgeLabel }}</span>
                            </div>

                            <div class="kuota-stat-line">
                                <span class="kuota-big-number">{{ $sisa }}</span>
                                <span class="kuota-stat-label">kursi tersisa</span>
                                <span class="kuota-stat-total">dari {{ $total }} kursi</span>
                            </div>

                            <div class="kuota-progress-bar-bg">
                                <div class="kuota-progress-bar-fill {{ $barClass }}" style="width: {{ $persen }}%"></div>
                            </div>

                            <p class="kuota-card-desc">{{ $jurusan->deskripsi ?? 'Program magang keahlian tingkat perguruan tinggi dengan proyek riil dinas.' }}</p>

                            @if($jurusan->bidang->isNotEmpty())
                                <div class="kuota-jurusan-wrap">
                                    <span class="jurusan-lead">Penempatan:</span>
                                    @foreach($jurusan->bidang as $bdg)
                                        <span class="jurusan-tag">{{ $bdg->nama_bidang }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="kuota-card-footer">
                                @if($isAvailable && !$isWaiting && $sisa > 0)
                                    <a href="{{ url('/register?bulan=' . $bulan->id . '&jurusan=' . $jurusan->id . '&tipe=mandiri&jumlah=1') }}" 
                                       class="btn-card-action btn-primary-action btn-card-cta"
                                       data-base-url="{{ url('/register') }}"
                                       data-bulan-id="{{ $bulan->id }}"
                                       data-jurusan-id="{{ $jurusan->id }}"
                                       data-sisa="{{ $sisa }}"
                                       data-total="{{ $total }}"
                                       data-is-waiting="0"
                                       data-is-available="1">
                                        Daftar Sekarang ➔
                                    </a>
                                @elseif($isWaiting || ($isAvailable && $sisa === 0))
                                    <a href="{{ url('/register?bulan=' . $bulan->id . '&jurusan=' . $jurusan->id . '&tipe=mandiri&jumlah=1') }}" 
                                       class="btn-card-action btn-waitlist-action btn-card-cta"
                                       data-base-url="{{ url('/register') }}"
                                       data-bulan-id="{{ $bulan->id }}"
                                       data-jurusan-id="{{ $jurusan->id }}"
                                       data-sisa="{{ $sisa }}"
                                       data-total="{{ $total }}"
                                       data-is-waiting="1"
                                       data-is-available="{{ $isAvailable ? '1' : '0' }}">
                                        Daftar Waiting List ➔
                                    </a>
                                @else
                                    <button type="button" class="btn-card-action btn-disabled" disabled>
                                        Pendaftaran Ditutup
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endforeach
                </div>

                <button type="button" id="btnCarouselNext" class="kuota-side-arrow-btn next" aria-label="Geser ke kanan" title="Geser ke kanan">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>

            <!-- Status Legend -->
            <div class="kuota-legend-row">
                <span class="legend-item">
                    <span class="legend-dot" style="background-color: var(--success);"></span>
                    Masih banyak
                </span>
                <span class="legend-item">
                    <span class="legend-dot" style="background-color: var(--warning);"></span>
                    Tinggal sedikit
                </span>
                <span class="legend-item">
                    <span class="legend-dot" style="background-color: var(--muted-text);"></span>
                    Penuh / Waiting List
                </span>
            </div>
        </section>

        <!-- =======================================================================
             4. TAHAPAN PENDAFTARAN & TAHAPANROAD ENGINE
             ======================================================================= -->
        <section id="tahapan" class="landing-section" style="padding-top: 2rem; padding-bottom: 5rem;">
            <div class="tahapan-layout-grid">
                <!-- Sisi Kiri (Sticky Info) -->
                <div class="tahapan-sticky-left">
                    <span class="step-color-bar"></span>
                    <h2 class="tahapan-main-heading">
                        Bingung cara mendaftarnya?, berikut adalah <strong>Tahapan Pendaftaran Magang</strong>
                    </h2>
                    <div class="tahapan-summary-list">
                        <span class="tahapan-summary-item"><strong>Pertama</strong> calon peserta membuat akun, mengisi data pribadi, dan menunggu verifikasi akun oleh admin.</span>
                        <span class="tahapan-summary-item"><strong>Kedua</strong> calon peserta memilih bidang dan mengunggah berkas, lalu admin melakukan verifikasi dan seleksi. Hasilnya diumumkan di portal MAGKOM.</span>
                        <span class="tahapan-summary-item"><strong>Ketiga</strong> peserta melaksanakan magang, mengerjakan tugas, dan mengisi logbook sesuai durasi yang ditentukan.</span>
                        <span class="tahapan-summary-item"><strong>Keempat</strong> setelah magang selesai, peserta mendapatkan lembar nilai dan sertifikat yang dapat diunduh.</span>
                    </div>
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1.25rem;">
                        <a href="{{ route('panduan.pdf') }}" target="_blank" rel="noopener noreferrer" class="btn-tahapan-download" title="Unduh Petunjuk Teknis & Panduan Resmi Magang Diskominfo Sleman (PDF)">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.4rem; vertical-align: middle;">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Unduh Panduan PDF
                        </a>
                        <a href="{{ route('panduan-alur') }}" class="btn-tahapan-download" style="background: rgba(7, 81, 255, 0.08); color: var(--primary-accent); border: 1.5px solid rgba(7, 81, 255, 0.25);" title="Buka Diagram Alur Interaktif Penuh">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 0.4rem; vertical-align: middle;">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polygon points="12 8 8 12 12 16 12 8"></polygon>
                            </svg>
                            Peta Alur Interaktif
                        </a>
                    </div>
                </div>

                <!-- Sisi Kanan (TahapanRoad Interactive SVG Engine) -->
                <div id="tahapanRoadWrapper" class="tahapan-road-wrapper">
                    <!-- SVG Road Canvas (H = 210, total = 840, T = 80, B = 80) -->
                    <svg width="170" height="1000" viewBox="0 -80 170 1000" class="tahapan-svg-canvas">
                        <defs>
                            <linearGradient id="roadGrad" gradientUnits="userSpaceOnUse" x1="0" y1="0" x2="0" y2="840">
                                <stop offset="0.0" stop-color="#e11d48"></stop>
                                <stop offset="0.25" stop-color="#e11d48"></stop>
                                <stop offset="0.25" stop-color="#6366f1"></stop>
                                <stop offset="0.5" stop-color="#6366f1"></stop>
                                <stop offset="0.5" stop-color="#0284c7"></stop>
                                <stop offset="0.75" stop-color="#0284c7"></stop>
                                <stop offset="0.75" stop-color="#eab308"></stop>
                                <stop offset="1.0" stop-color="#eab308"></stop>
                            </linearGradient>
                        </defs>

                        <!-- Base Asphalt Stroke -->
                        <path id="roadSvgPath" d="M40 -80 L40 0 L40 40 C40 75 120 60 120 105 S40 135 40 170 L40 210 L40 250 C40 285 120 270 120 315 S40 345 40 380 L40 420 L40 460 C40 495 120 480 120 525 S40 555 40 590 L40 630 L40 670 C40 705 120 690 120 735 S40 765 40 800 L40 840 L40 920" 
                              fill="none" stroke="var(--road)" stroke-width="34" stroke-linecap="round"></path>

                        <!-- Road Active Color Gradient Glow Stroke -->
                        <path id="roadGradProgressPath" d="M40 -80 L40 0 L40 40 C40 75 120 60 120 105 S40 135 40 170 L40 210 L40 250 C40 285 120 270 120 315 S40 345 40 380 L40 420 L40 460 C40 495 120 480 120 525 S40 555 40 590 L40 630 L40 670 C40 705 120 690 120 735 S40 765 40 800 L40 840 L40 920" 
                              fill="none" stroke="url(#roadGrad)" stroke-width="34" stroke-linecap="round" stroke-dasharray="1000" stroke-dashoffset="1000" opacity="0.4"></path>

                        <!-- Center Dashed Markings -->
                        <path d="M40 -80 L40 0 L40 40 C40 75 120 60 120 105 S40 135 40 170 L40 210 L40 250 C40 285 120 270 120 315 S40 345 40 380 L40 420 L40 460 C40 495 120 480 120 525 S40 555 40 590 L40 630 L40 670 C40 705 120 690 120 735 S40 765 40 800 L40 840 L40 920" 
                              fill="none" stroke="var(--road-line)" stroke-width="2.5" stroke-dasharray="10 9"></path>

                        <!-- START Indicator -->
                        <g aria-label="Start Node">
                            <circle cx="40" cy="-72" r="22" fill="var(--success)" opacity="0.25"></circle>
                            <line x1="78" y1="-76" x2="78" y2="-36" stroke="var(--foreground)" stroke-width="2.5"></line>
                            <!-- Flag icon -->
                            <path d="M78 -76 L96 -66 L78 -56 Z" fill="var(--success)" stroke="var(--success)" stroke-width="1.5"></path>
                            <text x="106" y="-58" font-family="var(--font-display)" font-size="14" font-weight="700" fill="var(--success)">START</text>
                        </g>

                        <!-- Checkpoint 1 (Registrasi) -->
                        <g data-checkpoint-index="0" class="cursor-pointer" style="cursor: pointer;" role="button" aria-label="Tahap 1 Registrasi">
                            <circle cx="120" cy="105" r="34" fill="#e11d48" opacity="0" class="checkpoint-halo" style="transition: all 0.4s ease;"></circle>
                            <circle cx="120" cy="105" r="26" fill="var(--background)" stroke="#e11d48" stroke-width="3" class="checkpoint-dot" data-step-color="#e11d48" style="transition: all 0.4s ease;"></circle>
                            <!-- UserPlus Icon -->
                            <g transform="translate(108, 93)" stroke="#e11d48" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <line x1="20" y1="8" x2="20" y2="14"></line>
                                <line x1="23" y1="11" x2="17" y2="11"></line>
                            </g>
                        </g>

                        <!-- Checkpoint 2 (Administrasi & Seleksi) -->
                        <g data-checkpoint-index="1" class="cursor-pointer" style="cursor: pointer;" role="button" aria-label="Tahap 2 Administrasi & Seleksi">
                            <circle cx="120" cy="315" r="34" fill="#6366f1" opacity="0" class="checkpoint-halo" style="transition: all 0.4s ease;"></circle>
                            <circle cx="120" cy="315" r="26" fill="var(--background)" stroke="#6366f1" stroke-width="3" class="checkpoint-dot" data-step-color="#6366f1" style="transition: all 0.4s ease;"></circle>
                            <!-- FileCheck2 Icon -->
                            <g transform="translate(108, 303)" stroke="#6366f1" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                                <path d="m3 15 2 2 4-4"></path>
                            </g>
                        </g>

                        <!-- Checkpoint 3 (Pelaksanaan Magang) -->
                        <g data-checkpoint-index="2" class="cursor-pointer" style="cursor: pointer;" role="button" aria-label="Tahap 3 Pelaksanaan Magang">
                            <circle cx="120" cy="525" r="34" fill="#0284c7" opacity="0" class="checkpoint-halo" style="transition: all 0.4s ease;"></circle>
                            <circle cx="120" cy="525" r="26" fill="var(--background)" stroke="#0284c7" stroke-width="3" class="checkpoint-dot" data-step-color="#0284c7" style="transition: all 0.4s ease;"></circle>
                            <!-- Briefcase Icon -->
                            <g transform="translate(108, 513)" stroke="#0284c7" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="14" x="2" y="7" rx="2" ry="2"></rect>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                            </g>
                        </g>

                        <!-- Checkpoint 4 (Kelulusan) -->
                        <g data-checkpoint-index="3" class="cursor-pointer" style="cursor: pointer;" role="button" aria-label="Tahap 4 Kelulusan">
                            <circle cx="120" cy="735" r="34" fill="#eab308" opacity="0" class="checkpoint-halo" style="transition: all 0.4s ease;"></circle>
                            <circle cx="120" cy="735" r="26" fill="var(--background)" stroke="#eab308" stroke-width="3" class="checkpoint-dot" data-step-color="#eab308" style="transition: all 0.4s ease;"></circle>
                            <!-- GraduationCap Icon -->
                            <g transform="translate(108, 723)" stroke="#eab308" stroke-width="2.2" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </g>
                        </g>

                        <!-- FINISH Checkered Flag Line -->
                        <g aria-label="Finish Node">
                            <line x1="78" y1="870" x2="78" y2="916" stroke="var(--foreground)" stroke-width="2.5"></line>
                            <!-- Checkered grid 4x3 -->
                            <rect x="79" y="870" width="7" height="7" fill="var(--foreground)"></rect>
                            <rect x="86" y="870" width="7" height="7" fill="var(--background)" stroke="var(--foreground)" stroke-width="0.5"></rect>
                            <rect x="93" y="870" width="7" height="7" fill="var(--foreground)"></rect>
                            <rect x="100" y="870" width="7" height="7" fill="var(--background)" stroke="var(--foreground)" stroke-width="0.5"></rect>

                            <rect x="79" y="877" width="7" height="7" fill="var(--background)" stroke="var(--foreground)" stroke-width="0.5"></rect>
                            <rect x="86" y="877" width="7" height="7" fill="var(--foreground)"></rect>
                            <rect x="93" y="877" width="7" height="7" fill="var(--background)" stroke="var(--foreground)" stroke-width="0.5"></rect>
                            <rect x="100" y="877" width="7" height="7" fill="var(--foreground)"></rect>

                            <rect x="79" y="884" width="7" height="7" fill="var(--foreground)"></rect>
                            <rect x="86" y="884" width="7" height="7" fill="var(--background)" stroke="var(--foreground)" stroke-width="0.5"></rect>
                            <rect x="93" y="884" width="7" height="7" fill="var(--foreground)"></rect>
                            <rect x="100" y="884" width="7" height="7" fill="var(--background)" stroke="var(--foreground)" stroke-width="0.5"></rect>

                            <text x="114" y="890" font-family="var(--font-display)" font-size="14" font-weight="700" fill="var(--foreground)">FINISH</text>
                        </g>

                        <!-- Animated Mini Car on Track -->
                        <g id="roadCarGroup" transform="translate(40, -80) rotate(90)" style="filter: drop-shadow(0 3px 4px rgba(0, 0, 0, 0.4));">
                            <rect x="-15" y="-9" width="30" height="18" rx="6" fill="#1b3860"></rect>
                            <rect x="3" y="-7" width="7" height="14" rx="2" fill="#fbbf24"></rect>
                            <rect x="-11" y="-7" width="5" height="14" rx="2" fill="#fbbf24" opacity="0.65"></rect>
                            <rect x="12" y="-8" width="3" height="4" rx="1" fill="#ffffff"></rect>
                            <rect x="12" y="4" width="3" height="4" rx="1" fill="#ffffff"></rect>
                        </g>
                    </svg>

                    <!-- Milestone Steps Cards (4 Steps aligned with checkpoints) -->
                    <ol class="tahapan-cards-list">
                        <!-- Step 1 -->
                        <li class="tahapan-card-item">
                            <button type="button" class="tahapan-milestone-card inactive" data-milestone-index="0">
                                <span class="card-triangle-pin" style="background-color: var(--step-1);"></span>
                                <p class="tahapan-number-lg" style="color: var(--step-1);">01</p>
                                <h3 class="tahapan-card-title">Registrasi</h3>
                                <p class="tahapan-card-desc">Calon peserta membuat akun, mengisi data pribadi, dan menunggu akun diverifikasi admin.</p>
                            </button>
                        </li>

                        <!-- Step 2 -->
                        <li class="tahapan-card-item">
                            <button type="button" class="tahapan-milestone-card inactive" data-milestone-index="1">
                                <span class="card-triangle-pin" style="background-color: var(--step-2);"></span>
                                <p class="tahapan-number-lg" style="color: var(--step-2);">02</p>
                                <h3 class="tahapan-card-title">Administrasi &amp; Seleksi</h3>
                                <p class="tahapan-card-desc">Pilih bidang, unggah berkas, lalu admin memverifikasi dan menyeleksi dalam 1–3 hari kerja.</p>
                            </button>
                        </li>

                        <!-- Step 3 -->
                        <li class="tahapan-card-item">
                            <button type="button" class="tahapan-milestone-card inactive" data-milestone-index="2">
                                <span class="card-triangle-pin" style="background-color: var(--step-3);"></span>
                                <p class="tahapan-number-lg" style="color: var(--step-3);">03</p>
                                <h3 class="tahapan-card-title">Pelaksanaan Magang</h3>
                                <p class="tahapan-card-desc">Kerjakan tugas, isi logbook harian, dan unggah progres sesuai durasi magang.</p>
                            </button>
                        </li>

                        <!-- Step 4 -->
                        <li class="tahapan-card-item">
                            <button type="button" class="tahapan-milestone-card inactive" data-milestone-index="3">
                                <span class="card-triangle-pin" style="background-color: var(--step-4);"></span>
                                <p class="tahapan-number-lg" style="color: var(--step-4);">04</p>
                                <h3 class="tahapan-card-title">Kelulusan</h3>
                                <p class="tahapan-card-desc">Unduh Lembar Nilai Magang dan sertifikat dalam bentuk PDF dari portal.</p>
                            </button>
                        </li>
                    </ol>
                </div>
            </div>
        </section>

        <!-- =======================================================================
             5. FAQ SECTION
             ======================================================================= -->
        <section id="faq" class="faq-section-bg">
            <div class="faq-container">
                <h2 class="faq-headline">Pertanyaan yang Sering Ditanyakan</h2>

                <div id="faqAccordionContainer" class="faq-accordion-group">
                    <!-- Di-render oleh landing-interactive.js -->
                </div>

                <div class="faq-footer-action">
                    <button type="button" id="btnFaqToggleAll" class="btn-faq-toggle-all">
                        Lihat Semua Pertanyaan
                    </button>
                </div>
            </div>
        </section>
    </main>

    <!-- =======================================================================
         6. FOOTER WITH ANIMATED WIFI SIGNAL EFFECT (Lentera Parity)
         ======================================================================= -->
    <footer id="kontak" class="landing-footer">
        <!-- Background Animated Wifi Signal Waves & Floating Nodes (Inspired by lentera.menpan.go.id) -->
        <div class="footer-wifi-particles" aria-hidden="true">
            <!-- Pulsing Concentric Radar / Wifi Wave Source -->
            <div class="wifi-radar-source">
                <div class="wifi-pulse-circle wave-1"></div>
                <div class="wifi-pulse-circle wave-2"></div>
                <div class="wifi-pulse-circle wave-3"></div>
                <div class="wifi-pulse-circle wave-4"></div>
            </div>

            <!-- Floating Golden WiFi Icons (Melayang Anggun ke Atas) -->
            <ul class="wifi-signal-nodes">
                <li>
                    <svg class="floating-wifi-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 8.82a15 15 0 0 1 20 0" stroke-width="2.2"></path>
                        <path d="M5 12.86a10.5 10.5 0 0 1 14 0" stroke-width="2.2"></path>
                        <path d="M8.5 16.9a6 6 0 0 1 7 0" stroke-width="2.2"></path>
                        <circle cx="12" cy="20" r="1.5" fill="currentColor" stroke="none"></circle>
                    </svg>
                </li>
                <li>
                    <svg class="floating-wifi-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 8.82a15 15 0 0 1 20 0" stroke-width="2.2"></path>
                        <path d="M5 12.86a10.5 10.5 0 0 1 14 0" stroke-width="2.2"></path>
                        <path d="M8.5 16.9a6 6 0 0 1 7 0" stroke-width="2.2"></path>
                        <circle cx="12" cy="20" r="1.5" fill="currentColor" stroke="none"></circle>
                    </svg>
                </li>
                <li>
                    <svg class="floating-wifi-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 8.82a15 15 0 0 1 20 0" stroke-width="2.2"></path>
                        <path d="M5 12.86a10.5 10.5 0 0 1 14 0" stroke-width="2.2"></path>
                        <path d="M8.5 16.9a6 6 0 0 1 7 0" stroke-width="2.2"></path>
                        <circle cx="12" cy="20" r="1.5" fill="currentColor" stroke="none"></circle>
                    </svg>
                </li>
                <li>
                    <svg class="floating-wifi-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 8.82a15 15 0 0 1 20 0" stroke-width="2.2"></path>
                        <path d="M5 12.86a10.5 10.5 0 0 1 14 0" stroke-width="2.2"></path>
                        <path d="M8.5 16.9a6 6 0 0 1 7 0" stroke-width="2.2"></path>
                        <circle cx="12" cy="20" r="1.5" fill="currentColor" stroke="none"></circle>
                    </svg>
                </li>
                <li>
                    <svg class="floating-wifi-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 8.82a15 15 0 0 1 20 0" stroke-width="2.2"></path>
                        <path d="M5 12.86a10.5 10.5 0 0 1 14 0" stroke-width="2.2"></path>
                        <path d="M8.5 16.9a6 6 0 0 1 7 0" stroke-width="2.2"></path>
                        <circle cx="12" cy="20" r="1.5" fill="currentColor" stroke="none"></circle>
                    </svg>
                </li>
                <li>
                    <svg class="floating-wifi-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 8.82a15 15 0 0 1 20 0" stroke-width="2.2"></path>
                        <path d="M5 12.86a10.5 10.5 0 0 1 14 0" stroke-width="2.2"></path>
                        <path d="M8.5 16.9a6 6 0 0 1 7 0" stroke-width="2.2"></path>
                        <circle cx="12" cy="20" r="1.5" fill="currentColor" stroke="none"></circle>
                    </svg>
                </li>
                <li>
                    <svg class="floating-wifi-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 8.82a15 15 0 0 1 20 0" stroke-width="2.2"></path>
                        <path d="M5 12.86a10.5 10.5 0 0 1 14 0" stroke-width="2.2"></path>
                        <path d="M8.5 16.9a6 6 0 0 1 7 0" stroke-width="2.2"></path>
                        <circle cx="12" cy="20" r="1.5" fill="currentColor" stroke="none"></circle>
                    </svg>
                </li>
                <li>
                    <svg class="floating-wifi-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 8.82a15 15 0 0 1 20 0" stroke-width="2.2"></path>
                        <path d="M5 12.86a10.5 10.5 0 0 1 14 0" stroke-width="2.2"></path>
                        <path d="M8.5 16.9a6 6 0 0 1 7 0" stroke-width="2.2"></path>
                        <circle cx="12" cy="20" r="1.5" fill="currentColor" stroke="none"></circle>
                    </svg>
                </li>
                <li>
                    <svg class="floating-wifi-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 8.82a15 15 0 0 1 20 0" stroke-width="2.2"></path>
                        <path d="M5 12.86a10.5 10.5 0 0 1 14 0" stroke-width="2.2"></path>
                        <path d="M8.5 16.9a6 6 0 0 1 7 0" stroke-width="2.2"></path>
                        <circle cx="12" cy="20" r="1.5" fill="currentColor" stroke="none"></circle>
                    </svg>
                </li>
                <li>
                    <svg class="floating-wifi-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 8.82a15 15 0 0 1 20 0" stroke-width="2.2"></path>
                        <path d="M5 12.86a10.5 10.5 0 0 1 14 0" stroke-width="2.2"></path>
                        <path d="M8.5 16.9a6 6 0 0 1 7 0" stroke-width="2.2"></path>
                        <circle cx="12" cy="20" r="1.5" fill="currentColor" stroke="none"></circle>
                    </svg>
                </li>
            </ul>
        </div>

        <div class="landing-footer-grid">
            <!-- Kolom 1: Alamat -->
            <div>
                <h3 class="footer-col-title">Alamat</h3>
                <div class="footer-address-block">
                    <p>Jl Parasamya (Komplek Kantor Bupati Sleman) Beran, Tridadi, Sleman, Yogyakarta 55511</p>
                    <p>Telepon: (0274) 868405</p>
                    <p>Email: kominfo@slemankab.go.id</p>
                    <p>Senin sampai Kamis 07.30 sampai 15.30 WIB, Jumat 07.30 sampai 14.30 WIB</p>
                </div>
            </div>

            <!-- Kolom 2: Media Sosial -->
            <div>
                <h3 class="footer-col-title">Media Sosial</h3>
                <ul class="footer-links-clean">
                    <li><a href="https://instagram.com/kominfo_sleman" target="_blank" rel="noopener noreferrer">kominfo_sleman</a></li>
                    <li><a href="https://twitter.com/diskominfosleman" target="_blank" rel="noopener noreferrer">diskominfosleman</a></li>
                </ul>
            </div>

            <!-- Kolom 3: Tautan Terkait -->
            <div>
                <h3 class="footer-col-title">Tautan Terkait</h3>
                <ul class="footer-links-clean">
                    <li><a href="{{ route('kebijakan-privasi') }}">Kebijakan Privasi</a></li>
                    <li><a href="https://kominfo.slemankab.go.id" target="_blank" rel="noopener noreferrer">Situs Kominfo Sleman</a></li>
                    <li><a href="https://slemankab.go.id" target="_blank" rel="noopener noreferrer">Sleman Digital</a></li>
                    <li><a href="{{ url('/verifikasi-dokumen/cek') }}">Verifikasi Dokumen Sah</a></li>
                </ul>
            </div>

            <!-- Kolom 4: Akun -->
            <div>
                <h3 class="footer-col-title">Akun</h3>
                <ul class="footer-links-clean">
                    <li><a href="{{ route('login') }}">Masuk Peserta</a></li>
                    <li><a href="{{ route('login') }}">Masuk Pembimbing</a></li>
                    <li><a href="{{ route('login') }}">Masuk Admin</a></li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom Bar Clean -->
        <div class="footer-bottom-bar-clean">
            <p>&copy; {{ date('Y') }} Dinas Komunikasi dan Informatika Kabupaten Sleman. Seluruh hak cipta dilindungi.</p>
        </div>
    </footer>

    <!-- =======================================================================
         7. BACKEND DATA INJECTION FOR REAL-TIME KUOTA HYDRATION
         ======================================================================= -->
    <script>
        window.MAGKOM_REGISTER_URL = "{{ route('register') }}";
        window.MAGKOM_DATA = {
            @if(isset($bulanTampil) && $bulanTampil->count() > 0)
                bulanList: {!! json_encode($bulanTampil->map(fn($b) => \Carbon\CarbonImmutable::parse($b->bulan_mulai)->locale('id')->isoFormat('MMM'))->values()) !!},
                bulanPanjang: {
                    @foreach($bulanTampil as $b)
                        "{{ \Carbon\CarbonImmutable::parse($b->bulan_mulai)->locale('id')->isoFormat('MMM') }}": "{{ \Carbon\CarbonImmutable::parse($b->bulan_mulai)->locale('id')->isoFormat('MMMM Y') }}",
                    @endforeach
                },
            @endif
        };
    </script>

    <!-- Interactive Script -->
    <script src="{{ asset('js/landing-interactive.js') }}?v={{ filemtime(public_path('js/landing-interactive.js')) }}"></script>
</body>
</html>
