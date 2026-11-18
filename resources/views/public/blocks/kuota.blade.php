@php
    $kuotaHeading = $content['heading'] ?? \App\Support\Pengaturan::get('landing_kuota_heading', 'Pilih Penempatan & Ketersediaan Kuota Magang');
    $kuotaSubheading = $content['subheading'] ?? \App\Support\Pengaturan::get('landing_kuota_subheading', 'Pilih jenjang pendidikan, tentukan bulan mulai, lalu pilih bidang penempatan dan jurusanmu untuk langsung mengamankan kuota pendaftaran.');
    $kuotaQ1 = $content['q1_label'] ?? \App\Support\Pengaturan::get('landing_kuota_q1_label', 'Dari mana Anda?');
    $kuotaPillSmk = $content['pill_smk'] ?? \App\Support\Pengaturan::get('landing_kuota_pill_smk', 'Siswa SMK');
    $kuotaPillMhs = $content['pill_mhs'] ?? \App\Support\Pengaturan::get('landing_kuota_pill_mhs', 'Mahasiswa (D3/S1)');
    $kuotaQ2 = $content['q2_label'] ?? \App\Support\Pengaturan::get('landing_kuota_q2_label', 'Kapan Anda siap?');
    $kuotaEmptyNote = $content['empty_note'] ?? \App\Support\Pengaturan::get('landing_kuota_empty_note', 'Belum ada periode magang aktif.');
    $kuotaQ3 = $content['q3_label'] ?? \App\Support\Pengaturan::get('landing_kuota_q3_label', 'Magang untuk berapa orang?');
    $kuotaPillMandiri = $content['pill_mandiri'] ?? \App\Support\Pengaturan::get('landing_kuota_pill_mandiri', 'Individu (1 Orang)');
    $kuotaPillTim = $content['pill_tim'] ?? \App\Support\Pengaturan::get('landing_kuota_pill_tim', 'Kelompok (Tim)');
    $kuotaTeamLabel = $content['team_label'] ?? \App\Support\Pengaturan::get('landing_kuota_team_label', 'Berapa orang dalam tim kamu?');
    $kuotaTeamNote = $content['team_note'] ?? \App\Support\Pengaturan::get('landing_kuota_team_note', 'Cukup 1 perwakilan/ketua yang mendaftar. Data rekan tim dimasukkan langsung di form.');
    $kuotaGaugeDesc = $content['gauge_desc'] ?? \App\Support\Pengaturan::get('landing_kuota_gauge_desc', 'Memuat informasi ketersediaan kuota...');
    $kuotaLegendTersedia = $content['legend_tersedia'] ?? \App\Support\Pengaturan::get('landing_kuota_legend_tersedia', 'Masih banyak');
    $kuotaLegendHampir = $content['legend_hampir'] ?? \App\Support\Pengaturan::get('landing_kuota_legend_hampir', 'Tinggal sedikit');
    $kuotaLegendPenuh = $content['legend_penuh'] ?? \App\Support\Pengaturan::get('landing_kuota_legend_penuh', 'Penuh');
    $kuotaBtnTersedia = $content['btn_tersedia'] ?? \App\Support\Pengaturan::get('landing_kuota_btn_tersedia', 'Daftar Sekarang ➔');
    $kuotaBtnTutup = $content['btn_tutup'] ?? \App\Support\Pengaturan::get('landing_kuota_btn_tutup', 'Pendaftaran Ditutup');

    $bulanTampil = $bulanTampil ?? collect();
    $bidangSmk = $bidangSmk ?? collect();
    $bidangMahasiswa = $bidangMahasiswa ?? collect();
    $matriksBidang = $matriksBidang ?? [];
    $jurusanSmk = $jurusanSmk ?? collect();
    $jurusanMahasiswa = $jurusanMahasiswa ?? collect();
    $matriksKetersediaan = $matriksKetersediaan ?? [];

    $formatStepQuestion = function ($label, $defaultNum, $defaultText) {
        $val = trim($label ?? '');
        if ($val === '') {
            return ['num' => $defaultNum, 'text' => $defaultText, 'full' => $defaultText];
        }
        if (preg_match('/^(\d+)[\.\s\-]+(.*)$/', $val, $matches)) {
            return [
                'num' => $matches[1],
                'text' => trim($matches[2]) ?: $defaultText,
                'full' => $val,
            ];
        }
        return ['num' => $defaultNum, 'text' => $val, 'full' => $val];
    };
    $q1Data = $formatStepQuestion($kuotaQ1, 1, 'Dari mana Anda?');
    $q2Data = $formatStepQuestion($kuotaQ2, 2, 'Kapan Anda siap?');
    $q3Data = $formatStepQuestion($kuotaQ3, 3, 'Magang untuk berapa orang?');
@endphp

<section id="penempatan" class="landing-section" data-wp-block="kuota" data-wp-block-name="Kuota & Penempatan" data-wp-block-id="{{ $blockId ?? 'block-kuota-1' }}" style="order: {{ $order ?? 2 }}; padding-top: 1.5rem; padding-bottom: 2.5rem; {{ !($visible ?? true) && !($isEditorMode ?? false) ? 'display: none !important;' : '' }}">
    <span id="kuota" class="sr-only" aria-hidden="true"></span>
    <h2 class="section-heading-lg">{{ $kuotaHeading }}</h2>
    <p class="section-desc-sub">{{ $kuotaSubheading }}</p>

    <div class="kuota-questions-grid">
        <!-- Pertanyaan 1: Jenjang -->
        <div class="kuota-question-col">
            <p class="question-label">
                <span class="sr-only">{{ $q1Data['full'] }}</span>
                <span class="question-step-badge" aria-hidden="true">{{ $q1Data['num'] }}</span>
                <span class="question-title-text" aria-hidden="true">{{ $q1Data['text'] }}</span>
            </p>
            <div class="pill-group kuota-pills-row" id="kuotaJenjangGroup">
                <button type="button" class="pill-btn active" data-jenjang-pill="smk">{{ $kuotaPillSmk }}</button>
                <button type="button" class="pill-btn" data-jenjang-pill="mahasiswa">{{ $kuotaPillMhs }}</button>
            </div>
        </div>

        <!-- Pertanyaan 2: Bulan -->
        <div class="kuota-question-col">
            <p class="question-label">
                <span class="sr-only">{{ $q2Data['full'] }}</span>
                <span class="question-step-badge" aria-hidden="true">{{ $q2Data['num'] }}</span>
                <span class="question-title-text" aria-hidden="true">{{ $q2Data['text'] }}</span>
            </p>
            <div id="kuotaBulanPillContainer" class="pill-group kuota-pills-row kuota-bulan-pills-row">
                @forelse($bulanTampil as $bIdx => $bulan)
                    @php
                        $namaBulanStr = \Carbon\CarbonImmutable::parse($bulan->bulan_mulai)->translatedFormat('F Y');
                    @endphp
                    <button type="button" 
                            class="pill-btn {{ $bIdx === 0 ? 'active' : '' }}" 
                            data-bulan-pill="{{ $bulan->id }}"
                            data-bulan-name="{{ $namaBulanStr }}">
                        <svg class="pill-btn-cal-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>{{ $namaBulanStr }}</span>
                    </button>
                @empty
                    <span style="color: var(--muted-text); font-size: 0.9rem;">{{ $kuotaEmptyNote }}</span>
                @endforelse
            </div>
        </div>

        <!-- Pertanyaan 3: Magang sama siapa? -->
        <div class="kuota-question-col">
            <p class="question-label">
                <span class="sr-only">{{ $q3Data['full'] }}</span>
                <span class="question-step-badge" aria-hidden="true">{{ $q3Data['num'] }}</span>
                <span class="question-title-text" aria-hidden="true">{{ $q3Data['text'] }}</span>
            </p>
            <div class="pill-group kuota-pills-row" id="kuotaModeGroup">
                <button type="button" class="pill-btn active" data-mode-pill="mandiri">{{ $kuotaPillMandiri }}</button>
                <button type="button" class="pill-btn" data-mode-pill="kelompok">{{ $kuotaPillTim }}</button>
            </div>
        </div>
    </div>

    <!-- Sub-pilihan Jumlah Tim (Hanya muncul saat Bareng Tim / Kelompok dipilih) -->
    <div id="kuotaTeamSizeWrapper" class="kuota-team-size-panel" style="display: none;">
        <div class="team-size-content">
            <span class="team-size-label">{{ $kuotaTeamLabel }}</span>
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
            <span><strong>Info Tim:</strong> {{ $kuotaTeamNote }}</span>
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
            {{ $kuotaGaugeDesc }}
        </p>
    </div>

    <!-- Mobile Swipe Hint Bar (Affordance geser kartu bidang) -->
    <div class="kuota-swipe-hint-bar" id="kuotaSwipeHintBar">
        <span class="kuota-counter-badge" id="kuotaCounterBadge">Bidang 1 dari 4</span>
        <span class="kuota-swipe-hint-text">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
            Geser untuk pilihan bidang lain
        </span>
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

        <!-- KARTU PENEMPATAN / BIDANG (SMK) -->
        @foreach($bulanTampil as $bulan)
            @foreach($bidangSmk as $bidang)
                @php
                    $bData = $matriksBidang[$bidang->id]['smk'][$bulan->id] ?? null;
                    if (!$bData) continue;

                    $jurusanList = $bData['jurusan_list'] ?? collect();
                    $jDetail = $bData['jurusan_detail'] ?? [];
                    $firstJurusanId = $jurusanList->first()?->id ?? '';
                    $initJurusanData = $firstJurusanId ? ($jDetail[$firstJurusanId] ?? null) : null;

                    $sisa = $initJurusanData ? $initJurusanData['sisa'] : ($bData['sisa_kuota'] ?? 0);
                    $total = $initJurusanData ? $initJurusanData['total'] : ($bData['total_kuota'] ?? 0);
                    $persen = $initJurusanData ? $initJurusanData['persen'] : ($bData['persen_terisi'] ?? 0);
                    $isAvailable = $bData['is_available'] ?? false;
                    $badgeLabel = $initJurusanData ? $initJurusanData['badge_label'] : ($sisa > 0 ? 'Tersedia' : 'Penuh');
                    $badgeClass = $initJurusanData ? $initJurusanData['badge_class'] : ($sisa > 0 ? 'status-tersedia' : 'status-penuh');
                    $barClass = $initJurusanData ? $initJurusanData['bar_class'] : ($sisa > 0 ? 'bar-tersedia' : 'bar-penuh');

                    $isAktifAwal = ($bulan->id === $pertamaBulanId);
                @endphp
                <div class="kuota-card {{ $sisa === 0 ? 'is-penuh' : '' }}"
                     data-jenjang="smk"
                     data-bulan-id="{{ $bulan->id }}"
                     data-bidang-id="{{ $bidang->id }}"
                     data-jurusan-id="{{ $firstJurusanId }}"
                     data-sisa="{{ $sisa }}"
                     data-total="{{ $total }}"
                     data-is-available="{{ $isAvailable ? '1' : '0' }}"
                     style="{{ $isAktifAwal ? '' : 'display: none;' }}">
                    <div class="kuota-card-header">
                        <div class="kuota-icon-badge">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                <path d="M6 8h.01M10 8h.01M14 8h.01"></path>
                            </svg>
                        </div>
                        <h3 class="kuota-bidang-name">{{ $bidang->nama_bidang }}</h3>
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

                    <p class="kuota-card-desc">{{ $bidang->deskripsi ?? 'Penempatan tugas teknis dan operasional kejuruan pada Dinas Kominfo.' }}</p>

                    @if($jurusanList->isNotEmpty())
                        <div class="kuota-jurusan-wrap" role="group" aria-label="Pilih Jurusan yang Sesuai">
                            <span class="jurusan-lead">
                                Penempatan: Jurusan yang Sesuai
                                <small class="jurusan-lead-hint">(Klik jurusan Anda)</small>
                            </span>
                            <div class="jurusan-pills-list">
                                @foreach($jurusanList as $jur)
                                    @php
                                        $jd = $jDetail[$jur->id] ?? null;
                                        $jSisa = $jd ? $jd['sisa'] : 0;
                                        $jTotal = $jd ? $jd['total'] : 0;
                                        $jPersen = $jd ? $jd['persen'] : 0;
                                        $jBadgeLabel = $jd ? $jd['badge_label'] : ($jSisa > 0 ? 'Tersedia' : 'Penuh');
                                        $jBadgeClass = $jd ? $jd['badge_class'] : ($jSisa > 0 ? 'status-tersedia' : 'status-penuh');
                                        $jBarClass = $jd ? $jd['bar_class'] : ($jSisa > 0 ? 'bar-tersedia' : 'bar-penuh');
                                        $isFirst = ($jur->id === $firstJurusanId);
                                    @endphp
                                    <button type="button" 
                                            class="jurusan-pill-btn {{ $isFirst ? 'active' : '' }}"
                                            data-jurusan-id="{{ $jur->id }}"
                                            data-jurusan-name="{{ $jur->nama_jurusan }}"
                                            data-sisa="{{ $jSisa }}"
                                            data-total="{{ $jTotal }}"
                                            data-persen="{{ $jPersen }}"
                                            data-badge-label="{{ $jBadgeLabel }}"
                                            data-badge-class="{{ $jBadgeClass }}"
                                            data-bar-class="{{ $jBarClass }}"
                                            role="radio"
                                            aria-checked="{{ $isFirst ? 'true' : 'false' }}"
                                            title="Pilih {{ $jur->nama_jurusan }} ({{ $jSisa }} kursi tersisa)">
                                        <span class="pill-check-icon" aria-hidden="true">✓</span>
                                        <span class="jurusan-tag">{{ $jur->nama_jurusan }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="kuota-card-footer">
                        @if($isAvailable && $sisa > 0)
                            <a href="{{ url('/register?bulan=' . $bulan->id . '&bidang=' . $bidang->id . '&jenjang=smk' . ($firstJurusanId ? '&jurusan=' . $firstJurusanId : '') . '&tipe=mandiri&jumlah=1') }}" 
                               class="btn-card-action btn-primary-action btn-card-cta"
                               data-base-url="{{ url('/register') }}"
                               data-bulan-id="{{ $bulan->id }}"
                               data-bidang-id="{{ $bidang->id }}"
                               data-jurusan-id="{{ $firstJurusanId }}"
                               data-jenjang="smk"
                               data-sisa="{{ $sisa }}"
                               data-total="{{ $total }}"
                               data-is-available="1">
                                {{ $kuotaBtnTersedia }}
                            </a>
                        @elseif($isAvailable && $sisa === 0)
                            <button type="button" class="btn-card-action btn-disabled btn-card-cta" disabled>
                                Kuota Penuh
                            </button>
                        @else
                            <button type="button" class="btn-card-action btn-disabled btn-card-cta" disabled>
                                {{ $kuotaBtnTutup }}
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        @endforeach

        <!-- KARTU PENEMPATAN / BIDANG (MAHASISWA) -->
        @foreach($bulanTampil as $bulan)
            @foreach($bidangMahasiswa as $bidang)
                @php
                    $bData = $matriksBidang[$bidang->id]['mahasiswa'][$bulan->id] ?? null;
                    if (!$bData) continue;

                    $jurusanList = $bData['jurusan_list'] ?? collect();
                    $jDetail = $bData['jurusan_detail'] ?? [];
                    $firstJurusanId = $jurusanList->first()?->id ?? '';
                    $initJurusanData = $firstJurusanId ? ($jDetail[$firstJurusanId] ?? null) : null;

                    $sisa = $initJurusanData ? $initJurusanData['sisa'] : ($bData['sisa_kuota'] ?? 0);
                    $total = $initJurusanData ? $initJurusanData['total'] : ($bData['total_kuota'] ?? 0);
                    $persen = $initJurusanData ? $initJurusanData['persen'] : ($bData['persen_terisi'] ?? 0);
                    $isAvailable = $bData['is_available'] ?? false;
                    $badgeLabel = $initJurusanData ? $initJurusanData['badge_label'] : ($sisa > 0 ? 'Tersedia' : 'Penuh');
                    $badgeClass = $initJurusanData ? $initJurusanData['badge_class'] : ($sisa > 0 ? 'status-tersedia' : 'status-penuh');
                    $barClass = $initJurusanData ? $initJurusanData['bar_class'] : ($sisa > 0 ? 'bar-tersedia' : 'bar-penuh');
                @endphp
                <div class="kuota-card {{ $sisa === 0 ? 'is-penuh' : '' }}"
                     data-jenjang="mahasiswa"
                     data-bulan-id="{{ $bulan->id }}"
                     data-bidang-id="{{ $bidang->id }}"
                     data-jurusan-id="{{ $firstJurusanId }}"
                     data-sisa="{{ $sisa }}"
                     data-total="{{ $total }}"
                     data-is-available="{{ $isAvailable ? '1' : '0' }}"
                     style="display: none;">
                    <div class="kuota-card-header">
                        <div class="kuota-icon-badge" style="background-color: #EFF6FF; color: #1D4ED8;">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                            </svg>
                        </div>
                        <h3 class="kuota-bidang-name">{{ $bidang->nama_bidang }}</h3>
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

                    <p class="kuota-card-desc">{{ $bidang->deskripsi ?? 'Penempatan proyek nyata keahlian bidang teknologi informasi dan komunikasi.' }}</p>

                    @if($jurusanList->isNotEmpty())
                        <div class="kuota-jurusan-wrap" role="group" aria-label="Pilih Jurusan yang Sesuai">
                            <span class="jurusan-lead">
                                Penempatan: Jurusan yang Sesuai
                                <small class="jurusan-lead-hint">(Klik jurusan Anda)</small>
                            </span>
                            <div class="jurusan-pills-list">
                                @foreach($jurusanList as $jur)
                                    @php
                                        $jd = $jDetail[$jur->id] ?? null;
                                        $jSisa = $jd ? $jd['sisa'] : 0;
                                        $jTotal = $jd ? $jd['total'] : 0;
                                        $jPersen = $jd ? $jd['persen'] : 0;
                                        $jBadgeLabel = $jd ? $jd['badge_label'] : ($jSisa > 0 ? 'Tersedia' : 'Penuh');
                                        $jBadgeClass = $jd ? $jd['badge_class'] : ($jSisa > 0 ? 'status-tersedia' : 'status-penuh');
                                        $jBarClass = $jd ? $jd['bar_class'] : ($jSisa > 0 ? 'bar-tersedia' : 'bar-penuh');
                                        $isFirst = ($jur->id === $firstJurusanId);
                                    @endphp
                                    <button type="button" 
                                            class="jurusan-pill-btn {{ $isFirst ? 'active' : '' }}"
                                            data-jurusan-id="{{ $jur->id }}"
                                            data-jurusan-name="{{ $jur->nama_jurusan }}"
                                            data-sisa="{{ $jSisa }}"
                                            data-total="{{ $jTotal }}"
                                            data-persen="{{ $jPersen }}"
                                            data-badge-label="{{ $jBadgeLabel }}"
                                            data-badge-class="{{ $jBadgeClass }}"
                                            data-bar-class="{{ $jBarClass }}"
                                            role="radio"
                                            aria-checked="{{ $isFirst ? 'true' : 'false' }}"
                                            title="Pilih {{ $jur->nama_jurusan }} ({{ $jSisa }} kursi tersisa)">
                                        <span class="pill-check-icon" aria-hidden="true">✓</span>
                                        <span class="jurusan-tag">{{ $jur->nama_jurusan }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="kuota-card-footer">
                        @if($isAvailable && $sisa > 0)
                            <a href="{{ url('/register?bulan=' . $bulan->id . '&bidang=' . $bidang->id . '&jenjang=s1' . ($firstJurusanId ? '&jurusan=' . $firstJurusanId : '') . '&tipe=mandiri&jumlah=1') }}" 
                               class="btn-card-action btn-primary-action btn-card-cta"
                               data-base-url="{{ url('/register') }}"
                               data-bulan-id="{{ $bulan->id }}"
                               data-bidang-id="{{ $bidang->id }}"
                               data-jurusan-id="{{ $firstJurusanId }}"
                               data-jenjang="mahasiswa"
                               data-sisa="{{ $sisa }}"
                               data-total="{{ $total }}"
                               data-is-available="1">
                                {{ $kuotaBtnTersedia }}
                            </a>
                        @elseif($isAvailable && $sisa === 0)
                            <button type="button" class="btn-card-action btn-disabled btn-card-cta" disabled>
                                Kuota Penuh
                            </button>
                        @else
                            <button type="button" class="btn-card-action btn-disabled btn-card-cta" disabled>
                                {{ $kuotaBtnTutup }}
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

    <!-- Kuota Carousel Indicator Dots (Mobile & Interactive) -->
    <div class="kuota-carousel-dots" id="kuotaCarouselDots" role="tablist" aria-label="Navigasi Bidang Magang"></div>

    <!-- Status Legend -->
    <div class="kuota-legend-row">
        <span class="legend-item">
            <span class="legend-dot" style="background-color: var(--success);"></span>
            {{ $kuotaLegendTersedia }}
        </span>
        <span class="legend-item">
            <span class="legend-dot" style="background-color: var(--warning);"></span>
            {{ $kuotaLegendHampir }}
        </span>
        <span class="legend-item">
            <span class="legend-dot" style="background-color: var(--muted-text);"></span>
            {{ $kuotaLegendPenuh }}
        </span>
    </div>
</section>

