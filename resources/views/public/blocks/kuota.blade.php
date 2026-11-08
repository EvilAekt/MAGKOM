@php
    $kuotaHeading = $content['heading'] ?? \App\Support\Pengaturan::get('landing_kuota_heading', 'Ketersediaan Kuota Magang');
    $kuotaSubheading = $content['subheading'] ?? \App\Support\Pengaturan::get('landing_kuota_subheading', 'Pilih jenjang pendidikan dan bulan mulai magang untuk melihat kuota jurusan yang tersedia.');
    $kuotaQ1 = $content['q1_label'] ?? \App\Support\Pengaturan::get('landing_kuota_q1_label', '1. Kamu siapa?');
    $kuotaPillSmk = $content['pill_smk'] ?? \App\Support\Pengaturan::get('landing_kuota_pill_smk', 'Siswa SMK');
    $kuotaPillMhs = $content['pill_mhs'] ?? \App\Support\Pengaturan::get('landing_kuota_pill_mhs', 'Mahasiswa (D3/S1)');
    $kuotaQ2 = $content['q2_label'] ?? \App\Support\Pengaturan::get('landing_kuota_q2_label', '2. Mau mulai kapan?');
    $kuotaEmptyNote = $content['empty_note'] ?? \App\Support\Pengaturan::get('landing_kuota_empty_note', 'Belum ada periode magang aktif.');
    $kuotaQ3 = $content['q3_label'] ?? \App\Support\Pengaturan::get('landing_kuota_q3_label', '3. Magang sama siapa?');
    $kuotaPillMandiri = $content['pill_mandiri'] ?? \App\Support\Pengaturan::get('landing_kuota_pill_mandiri', 'Individu (1 Orang)');
    $kuotaPillTim = $content['pill_tim'] ?? \App\Support\Pengaturan::get('landing_kuota_pill_tim', 'Kelompok (Tim)');
    $kuotaTeamLabel = $content['team_label'] ?? \App\Support\Pengaturan::get('landing_kuota_team_label', 'Berapa orang dalam tim kamu?');
    $kuotaTeamNote = $content['team_note'] ?? \App\Support\Pengaturan::get('landing_kuota_team_note', 'Cukup 1 perwakilan/ketua yang mendaftar. Data rekan tim dimasukkan langsung di form.');
    $kuotaGaugeDesc = $content['gauge_desc'] ?? \App\Support\Pengaturan::get('landing_kuota_gauge_desc', 'Memuat informasi ketersediaan kuota...');
    $kuotaLegendTersedia = $content['legend_tersedia'] ?? \App\Support\Pengaturan::get('landing_kuota_legend_tersedia', 'Masih banyak');
    $kuotaLegendHampir = $content['legend_hampir'] ?? \App\Support\Pengaturan::get('landing_kuota_legend_hampir', 'Tinggal sedikit');
    $kuotaLegendPenuh = $content['legend_penuh'] ?? \App\Support\Pengaturan::get('landing_kuota_legend_penuh', 'Penuh / Waiting List');
    $kuotaBtnTersedia = $content['btn_tersedia'] ?? \App\Support\Pengaturan::get('landing_kuota_btn_tersedia', 'Daftar Sekarang ➔');
    $kuotaBtnWaiting = $content['btn_waiting'] ?? \App\Support\Pengaturan::get('landing_kuota_btn_waiting', 'Daftar Waiting List ➔');
    $kuotaBtnTutup = $content['btn_tutup'] ?? \App\Support\Pengaturan::get('landing_kuota_btn_tutup', 'Pendaftaran Ditutup');

    $bulanTampil = $bulanTampil ?? collect();
    $jurusanSmk = $jurusanSmk ?? collect();
    $jurusanMahasiswa = $jurusanMahasiswa ?? collect();
    $matriksKetersediaan = $matriksKetersediaan ?? [];
@endphp

<section id="kuota" class="landing-section" data-wp-block="kuota" data-wp-block-name="Kuota & Formasi" data-wp-block-id="{{ $blockId ?? 'block-kuota-1' }}" style="order: {{ $order ?? 2 }}; padding-top: 3.5rem; padding-bottom: 3.5rem; {{ !($visible ?? true) && !($isEditorMode ?? false) ? 'display: none !important;' : '' }}">
    <h2 class="section-heading-lg">{{ $kuotaHeading }}</h2>
    <p class="section-desc-sub">{{ $kuotaSubheading }}</p>

    <div class="kuota-questions-grid">
        <!-- Pertanyaan 1: Jenjang -->
        <div>
            <p class="question-label">{{ $kuotaQ1 }}</p>
            <div class="pill-group" id="kuotaJenjangGroup">
                <button type="button" class="pill-btn active" data-jenjang-pill="smk">{{ $kuotaPillSmk }}</button>
                <button type="button" class="pill-btn" data-jenjang-pill="mahasiswa">{{ $kuotaPillMhs }}</button>
            </div>
        </div>

        <!-- Pertanyaan 2: Bulan -->
        <div>
            <p class="question-label">{{ $kuotaQ2 }}</p>
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
                    <span style="color: var(--muted-text); font-size: 0.9rem;">{{ $kuotaEmptyNote }}</span>
                @endforelse
            </div>
        </div>

        <!-- Pertanyaan 3: Magang sama siapa? -->
        <div>
            <p class="question-label">{{ $kuotaQ3 }}</p>
            <div class="pill-group" id="kuotaModeGroup">
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
                                {{ $kuotaBtnTersedia }}
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
                                {{ $kuotaBtnWaiting }}
                            </a>
                        @else
                            <button type="button" class="btn-card-action btn-disabled" disabled>
                                {{ $kuotaBtnTutup }}
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
                                {{ $kuotaBtnTersedia }}
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
                                {{ $kuotaBtnWaiting }}
                            </a>
                        @else
                            <button type="button" class="btn-card-action btn-disabled" disabled>
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

