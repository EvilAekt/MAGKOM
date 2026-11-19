@php
    $cmsKicker = $content['kicker'] ?? \App\Support\Pengaturan::get('landing_hero_kicker', 'Kabupaten Sleman');
    $cmsTitle = $content['title'] ?? \App\Support\Pengaturan::get('landing_hero_title');
    $cmsPeriod = $content['period_info'] ?? \App\Support\Pengaturan::get('landing_hero_period_info', 'Pendaftaran 1–15 Okt 2026 · mulai 2 Nov 2026');
    $cmsCta = $content['cta_primary_text'] ?? \App\Support\Pengaturan::get('landing_hero_cta_text', 'Daftar Sekarang');
    $cmsCtaUrl = $content['cta_primary_url'] ?? \App\Support\Pengaturan::get('landing_hero_cta_primary_url', '#kuota');

    $cmsPersyaratan = $content['persyaratan_umum'] ?? \App\Support\Pengaturan::get('landing_persyaratan_umum');
    $persyaratanList = $cmsPersyaratan ? array_filter(array_map('trim', explode("\n", (string) $cmsPersyaratan))) : [
        'Siswa SMK aktif atau mahasiswa aktif D3/D4/S1 dengan jurusan yang relevan',
        'Bersedia magang secara luring (WFO) di kantor Diskominfo Sleman',
        'Mengikuti jadwal dan durasi magang yang telah ditentukan',
    ];

    $cmsDokumen = $content['dokumen_pendaftaran'] ?? \App\Support\Pengaturan::get('landing_dokumen_pendaftaran');
    $dokumenList = $cmsDokumen ? array_filter(array_map('trim', explode("\n", (string) $cmsDokumen))) : [
        'Surat Pengantar Sekolah / Perguruan Tinggi',
        'Curriculum Vitae (CV)',
        'Transkrip / Rapor Nilai Terakhir',
        'Pas Foto',
    ];

    $cmsHeroYear = $content['year'] ?? \App\Support\Pengaturan::get('landing_hero_year', '2026');
    $cmsLabelPersyaratan = $content['label_persyaratan'] ?? \App\Support\Pengaturan::get('landing_label_persyaratan', "Persyaratan Umum");
    $persyaratanWords = explode("\n", str_replace("\r", "", $cmsLabelPersyaratan));
    if (count($persyaratanWords) === 1 && str_contains($cmsLabelPersyaratan, ' ')) {
        $persyaratanWords = explode(' ', $cmsLabelPersyaratan, 2);
    }

    $cmsLabelDokumen = $content['label_dokumen'] ?? \App\Support\Pengaturan::get('landing_label_dokumen', "Dokumen Pendaftaran");
    $dokumenWords = explode("\n", str_replace("\r", "", $cmsLabelDokumen));
    if (count($dokumenWords) === 1 && str_contains($cmsLabelDokumen, ' ')) {
        $dokumenWords = explode(' ', $cmsLabelDokumen, 2);
    }

    $cmsHeroImage = $content['image'] ?? \App\Support\Pengaturan::get('landing_hero_image');
    $heroImageUrl = ($cmsHeroImage && \Illuminate\Support\Facades\Storage::disk('public')->exists($cmsHeroImage))
        ? asset('storage/'.$cmsHeroImage)
        : asset('images/hero_dokumentasi_diskominfo.png');
@endphp

<section id="{{ !empty($settings['html_anchor']) ? $settings['html_anchor'] : 'beranda' }}" class="landing-section {{ $settings['custom_css_class'] ?? '' }}" data-wp-block="hero" data-wp-block-name="Hero Banner" data-wp-block-id="{{ $blockId ?? 'block-hero-1' }}" style="order: {{ $order ?? 1 }}; {{ !($visible ?? true) && !($isEditorMode ?? false) ? 'display: none !important;' : '' }}">
    <span id="beranda" class="sr-only" aria-hidden="true"></span>
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
                <!-- ============================================== -->
                <!-- DESKTOP SLIDES (Tampil hanya di layar >= 768px) -->
                <!-- ============================================== -->
                <!-- DESKTOP SLIDE 1: Info 3 Kolom Pendaftaran, Persyaratan & Dokumen -->
                <div class="hero-slide-item hero-slide-desktop">
                    <div class="hero-box">
                        <!-- Kolom 1: Overview & CTA -->
                        <div>
                            <p class="hero-kicker" data-wp-editable="hero_kicker">{{ $cmsKicker }}</p>
                            @if(!empty($cmsTitle))
                                <h1 class="hero-title" data-wp-editable="hero_title">{!! nl2br(e($cmsTitle)) !!}</h1>
                            @else
                                <h1 class="hero-title" data-wp-editable="hero_title">
                                     Magang <span class="text-accent">Kominfo</span><br>
                                     Periode II
                                </h1>
                            @endif
                            <div class="hero-year-badge">
                                <span class="hero-year-arrows">»»»»</span> <span data-wp-editable="hero_year">{{ $cmsHeroYear }}</span>
                            </div>
                            @if($cmsPeriod)
                                <p class="hero-period-info" data-wp-editable="hero_period_info">
                                    {{ $cmsPeriod }}
                                </p>
                            @endif
                            <a href="{{ $cmsCtaUrl }}" class="btn-hero-cta btn-hero-cta-desktop" data-wp-editable="hero_cta_text">
                                {{ $cmsCta }}
                            </a>
                        </div>

                        <!-- Kolom 2: Persyaratan Umum -->
                        <div>
                            <h2 class="hero-subhead">
                                {{ $persyaratanWords[0] ?? $cmsLabelPersyaratan }}
                                @if(isset($persyaratanWords[1]))
                                    <br><span class="text-accent">{{ $persyaratanWords[1] }}</span>
                                @endif
                            </h2>
                            <ul class="hero-list">
                                @foreach($persyaratanList as $pItem)
                                    <li>{{ $pItem }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Kolom 3: Dokumen Pendaftaran -->
                        <div>
                            <h2 class="hero-subhead">
                                {{ $dokumenWords[0] ?? $cmsLabelDokumen }}
                                @if(isset($dokumenWords[1]))
                                    <br><span class="text-accent">{{ $dokumenWords[1] }}</span>
                                @endif
                            </h2>
                            <ul class="hero-list">
                                @foreach($dokumenList as $dItem)
                                    <li>{{ $dItem }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- DESKTOP SLIDE 2: Dokumentasi Foto Penuh Resmi Diskominfo Sleman -->
                <div class="hero-slide-item hero-slide-desktop">
                    <div class="hero-full-image-slide" id="heroFullImageSlide">
                        <img src="{{ $heroImageUrl }}" 
                             alt="Dokumentasi Resmi Diskominfo Kabupaten Sleman" 
                             class="hero-full-img" 
                             id="heroFullImg"
                             loading="lazy">
                        <div class="hero-slide-caption-mobile">
                            <span class="hero-badge-doc">DOKUMENTASI RESMI</span>
                            <h2 class="hero-doc-title">Magang Diskominfo Sleman</h2>
                            <p class="hero-doc-desc">Pengembangan ekosistem digital SPBE, infrastruktur jaringan, dan talenta muda bersama Pemerintah Kabupaten Sleman.</p>
                            <a href="{{ $cmsCtaUrl }}" class="btn-hero-cta btn-hero-doc-cta">
                                {{ $cmsCta }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- MOBILE SLIDES (Tampil hanya di layar < 768px)   -->
                <!-- 4 Slide: 1. Pendaftaran + CTA, 2. Syarat,      -->
                <!--          3. Dokumen, 4. Foto Dokumentasi       -->
                <!-- ============================================== -->
                <!-- MOBILE SLIDE 1: Pendaftaran + Tombol Daftar Sekarang -->
                <div class="hero-slide-item hero-slide-mobile">
                    <div class="hero-mobile-card hero-mobile-card-pendaftaran">
                        <p class="hero-kicker">{{ $cmsKicker }}</p>
                        @if(!empty($cmsTitle))
                            <h1 class="hero-title">{!! nl2br(e($cmsTitle)) !!}</h1>
                        @else
                            <h1 class="hero-title">
                                 Magang <span class="text-accent">Kominfo</span><br>
                                 Periode II
                            </h1>
                        @endif
                        <div class="hero-year-badge">
                            <span class="hero-year-arrows">»»»»</span> {{ $cmsHeroYear }}
                        </div>
                        @if($cmsPeriod)
                            <p class="hero-period-info">
                                {{ $cmsPeriod }}
                            </p>
                        @endif
                        <div class="hero-mobile-cta-wrap">
                            <a href="{{ $cmsCtaUrl }}" class="btn-hero-cta btn-hero-cta-mobile">
                                {{ $cmsCta }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- MOBILE SLIDE 2: Persyaratan Umum -->
                <div class="hero-slide-item hero-slide-mobile">
                    <div class="hero-mobile-card hero-mobile-card-detail">
                        <div class="hero-mobile-card-header">
                            <h2 class="hero-subhead">
                                {{ $persyaratanWords[0] ?? $cmsLabelPersyaratan }}
                                @if(isset($persyaratanWords[1]))
                                    <br><span class="text-accent">{{ $persyaratanWords[1] }}</span>
                                @endif
                            </h2>
                        </div>
                        <ul class="hero-list">
                            @foreach($persyaratanList as $pItem)
                                <li>{{ $pItem }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- MOBILE SLIDE 3: Dokumen Pendaftaran -->
                <div class="hero-slide-item hero-slide-mobile">
                    <div class="hero-mobile-card hero-mobile-card-detail">
                        <div class="hero-mobile-card-header">
                            <h2 class="hero-subhead">
                                {{ $dokumenWords[0] ?? $cmsLabelDokumen }}
                                @if(isset($dokumenWords[1]))
                                    <br><span class="text-accent">{{ $dokumenWords[1] }}</span>
                                @endif
                            </h2>
                        </div>
                        <ul class="hero-list">
                            @foreach($dokumenList as $dItem)
                                <li>{{ $dItem }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- MOBILE SLIDE 4: Foto Kegiatan Magang Resmi Diskominfo -->
                <div class="hero-slide-item hero-slide-mobile">
                    <div class="hero-mobile-card hero-mobile-card-photo">
                        <div class="hero-mobile-photo-container">
                            <img src="{{ $heroImageUrl }}" 
                                 alt="Dokumentasi Resmi Diskominfo Kabupaten Sleman" 
                                 class="hero-mobile-img" 
                                 loading="lazy">
                        </div>
                        <div class="hero-mobile-photo-caption">
                            <span class="hero-badge-doc">DOKUMENTASI RESMI</span>
                            <h3 class="hero-mobile-photo-title">Magang Diskominfo Sleman</h3>
                            <p class="hero-mobile-photo-desc">Pengembangan ekosistem digital SPBE, tata kelola data, dan layanan sistem pemerintahan berbasis elektronik.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slider Indicator Dots (Desktop: 2 dots, Mobile: 4 dots) -->
        <div class="hero-slider-dots hero-dots-desktop" id="heroSliderDotsDesktop">
            <button type="button" class="hero-dot-btn active" data-slide-index="0" aria-label="Ke slide 1 (Informasi Pendaftaran)"></button>
            <button type="button" class="hero-dot-btn" data-slide-index="1" aria-label="Ke slide 2 (Dokumentasi Diskominfo)"></button>
        </div>
        <div class="hero-slider-dots hero-dots-mobile" id="heroSliderDotsMobile">
            <button type="button" class="hero-dot-btn active" data-slide-index="0" aria-label="Ke slide 1 (Pendaftaran)"></button>
            <button type="button" class="hero-dot-btn" data-slide-index="1" aria-label="Ke slide 2 (Persyaratan)"></button>
            <button type="button" class="hero-dot-btn" data-slide-index="2" aria-label="Ke slide 3 (Dokumen)"></button>
            <button type="button" class="hero-dot-btn" data-slide-index="3" aria-label="Ke slide 4 (Foto Kegiatan)"></button>
        </div>
    </div>
</section>

