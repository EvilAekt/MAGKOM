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
    $defaultHeroImageUrl = ($cmsHeroImage && \Illuminate\Support\Facades\Storage::disk('public')->exists($cmsHeroImage))
        ? asset('storage/'.$cmsHeroImage)
        : asset('images/hero_dokumentasi_diskominfo.png');

    $rawSlides = $content['slides'] ?? null;
    if (empty($rawSlides) || !is_array($rawSlides)) {
        $rawSlides = \App\Support\Pengaturan::json('landing_hero_slides', []);
    }
    $heroSlides = [];
    if (is_array($rawSlides) && count($rawSlides) > 0) {
        foreach ($rawSlides as $sIdx => $s) {
            $sImg = $s['image'] ?? '';
            $sUrl = ($sImg && \Illuminate\Support\Facades\Storage::disk('public')->exists($sImg))
                ? asset('storage/'.$sImg)
                : (filter_var($sImg, FILTER_VALIDATE_URL) ? $sImg : $defaultHeroImageUrl);

            $sType = $s['type'] ?? null;
            if (!$sType) {
                if (!empty($sImg) && empty($s['title']) && empty($s['description'])) {
                    $sType = 'banner';
                } elseif (empty($sImg) && (!empty($s['title']) || !empty($s['description']))) {
                    $sType = 'announcement';
                } else {
                    $sType = 'photo_text';
                }
            }

            $heroSlides[] = [
                'id' => $s['id'] ?? 'slide-'.($sIdx + 1),
                'type' => $sType,
                'image' => $sImg,
                'image_url' => $sUrl,
                'badge' => $s['badge'] ?? '',
                'title' => $s['title'] ?? '',
                'description' => $s['description'] ?? '',
                'cta_text' => $s['cta_text'] ?? '',
                'cta_url' => $s['cta_url'] ?? '',
            ];
        }
    } else {
        $heroSlides[] = [
            'id' => 'slide-1',
            'type' => 'photo_text',
            'image' => $cmsHeroImage,
            'image_url' => $defaultHeroImageUrl,
            'badge' => 'DOKUMENTASI RESMI',
            'title' => 'Magang Diskominfo Sleman',
            'description' => 'Pengembangan ekosistem digital SPBE, infrastruktur jaringan, dan talenta muda bersama Pemerintah Kabupaten Sleman.',
            'cta_text' => $cmsCta,
            'cta_url' => $cmsCtaUrl,
        ];
    }
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
                            <h2 class="hero-subhead" data-wp-editable="hero_label_persyaratan">
                                {{ $persyaratanWords[0] ?? $cmsLabelPersyaratan }}
                                @if(isset($persyaratanWords[1]))
                                    <br><span class="text-accent">{{ $persyaratanWords[1] }}</span>
                                @endif
                            </h2>
                            <ul class="hero-list" data-wp-editable="hero_persyaratan_list">
                                @foreach($persyaratanList as $pItem)
                                    <li>{{ $pItem }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Kolom 3: Dokumen Pendaftaran -->
                        <div>
                            <h2 class="hero-subhead" data-wp-editable="hero_label_dokumen">
                                {{ $dokumenWords[0] ?? $cmsLabelDokumen }}
                                @if(isset($dokumenWords[1]))
                                    <br><span class="text-accent">{{ $dokumenWords[1] }}</span>
                                @endif
                            </h2>
                            <ul class="hero-list" data-wp-editable="hero_dokumen_list">
                                @foreach($dokumenList as $dItem)
                                    <li>{{ $dItem }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- DESKTOP SLIDES BANNER / FOTO / PENGUMUMAN (Slide 2 s/d N: Dinamis) -->
                @foreach($heroSlides as $sIdx => $slide)
                    @php
                        $sType = $slide['type'] ?? 'photo_text';
                        $slideTitle = !empty($slide['title']) ? $slide['title'] : 'Dokumentasi Kegiatan';
                    @endphp
                    <div class="hero-slide-item hero-slide-desktop" data-slide-role="banner" data-slide-id="{{ $slide['id'] ?? 'slide-'.($sIdx+1) }}" data-slide-type="{{ $sType }}">
                        @if($sType === 'banner')
                            <div class="hero-full-image-slide hero-slide-pure-banner" id="heroFullImageSlide{{ $sIdx === 0 ? '' : '_'.$sIdx }}">
                                @if(!empty($slide['cta_url']))
                                    <a href="{{ $slide['cta_url'] }}" class="hero-banner-link" aria-label="{{ $slideTitle }}">
                                        <img src="{{ $slide['image_url'] }}" alt="{{ $slideTitle }}" class="hero-full-img" loading="lazy">
                                    </a>
                                @else
                                    <img src="{{ $slide['image_url'] }}" alt="{{ $slideTitle }}" class="hero-full-img" loading="lazy">
                                @endif
                            </div>
                        @elseif($sType === 'announcement')
                            <div class="hero-slide-announcement-desktop" id="heroFullImageSlide{{ $sIdx === 0 ? '' : '_'.$sIdx }}">
                                <div class="hero-announcement-content">
                                    @if(!empty($slide['badge']))
                                        <span class="hero-badge-doc">{{ $slide['badge'] }}</span>
                                    @endif
                                    <h2 class="hero-doc-title">{{ $slide['title'] }}</h2>
                                    @if(!empty($slide['description']))
                                        <p class="hero-doc-desc">{{ $slide['description'] }}</p>
                                    @endif
                                    @if(!empty($slide['cta_text']))
                                        <div class="hero-doc-cta-wrap">
                                            <a href="{{ !empty($slide['cta_url']) ? $slide['cta_url'] : '#kuota' }}" class="btn-hero-cta btn-hero-doc-cta">
                                                {{ $slide['cta_text'] }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="hero-full-image-slide" id="heroFullImageSlide{{ $sIdx === 0 ? '' : '_'.$sIdx }}">
                                <img src="{{ $slide['image_url'] }}" alt="{{ $slideTitle }}" class="hero-full-img" id="{{ $sIdx === 0 ? 'heroFullImg' : 'heroFullImg_'.$sIdx }}" loading="lazy">
                                <div class="hero-slide-scrim" aria-hidden="true"></div>
                                <div class="hero-slide-caption-desktop">
                                    @if(!empty($slide['badge']))
                                        <span class="hero-badge-doc">{{ $slide['badge'] }}</span>
                                    @endif
                                    <h2 class="hero-doc-title">{{ $slide['title'] }}</h2>
                                    @if(!empty($slide['description']))
                                        <p class="hero-doc-desc">{{ $slide['description'] }}</p>
                                    @endif
                                    @if(!empty($slide['cta_text']))
                                        <div class="hero-doc-cta-wrap">
                                            <a href="{{ !empty($slide['cta_url']) ? $slide['cta_url'] : '#kuota' }}" class="btn-hero-cta btn-hero-doc-cta">
                                                {{ $slide['cta_text'] }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach

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
                            <h2 class="hero-subhead" data-wp-editable="hero_label_persyaratan">
                                {{ $persyaratanWords[0] ?? $cmsLabelPersyaratan }}
                                @if(isset($persyaratanWords[1]))
                                    <br><span class="text-accent">{{ $persyaratanWords[1] }}</span>
                                @endif
                            </h2>
                        </div>
                        <ul class="hero-list" data-wp-editable="hero_persyaratan_list">
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
                            <h2 class="hero-subhead" data-wp-editable="hero_label_dokumen">
                                {{ $dokumenWords[0] ?? $cmsLabelDokumen }}
                                @if(isset($dokumenWords[1]))
                                    <br><span class="text-accent">{{ $dokumenWords[1] }}</span>
                                @endif
                            </h2>
                        </div>
                        <ul class="hero-list" data-wp-editable="hero_dokumen_list">
                            @foreach($dokumenList as $dItem)
                                <li>{{ $dItem }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- MOBILE SLIDES 4+: Banner Poster / Foto & Info / Pengumuman (Dinamis) -->
                @foreach($heroSlides as $sIdx => $slide)
                    @php
                        $sType = $slide['type'] ?? 'photo_text';
                        $slideTitle = !empty($slide['title']) ? $slide['title'] : 'Dokumentasi Kegiatan';
                    @endphp
                    <div class="hero-slide-item hero-slide-mobile" data-slide-role="banner" data-slide-id="{{ $slide['id'] ?? 'slide-'.($sIdx+1) }}" data-slide-type="{{ $sType }}">
                        @if($sType === 'banner')
                            <div class="hero-mobile-card hero-mobile-card-photo hero-mobile-card-pure-banner">
                                @if(!empty($slide['cta_url']))
                                    <a href="{{ $slide['cta_url'] }}" class="hero-mobile-photo-container hero-mobile-banner-link" aria-label="{{ $slideTitle }}">
                                        <img src="{{ $slide['image_url'] }}" alt="{{ $slideTitle }}" class="hero-mobile-img" loading="lazy">
                                    </a>
                                @else
                                    <div class="hero-mobile-photo-container">
                                        <img src="{{ $slide['image_url'] }}" alt="{{ $slideTitle }}" class="hero-mobile-img" loading="lazy">
                                    </div>
                                @endif
                            </div>
                        @elseif($sType === 'announcement')
                            <div class="hero-mobile-card hero-mobile-card-announcement">
                                <div class="hero-mobile-announcement-body">
                                    @if(!empty($slide['badge']))
                                        <span class="hero-badge-doc">{{ $slide['badge'] }}</span>
                                    @endif
                                    <h3 class="hero-mobile-photo-title">{{ $slide['title'] }}</h3>
                                    @if(!empty($slide['description']))
                                        <p class="hero-mobile-photo-desc">{{ $slide['description'] }}</p>
                                    @endif
                                    @if(!empty($slide['cta_text']))
                                        <div class="hero-mobile-cta-wrap-doc">
                                            <a href="{{ !empty($slide['cta_url']) ? $slide['cta_url'] : '#kuota' }}" class="btn-hero-cta btn-hero-doc-cta">
                                                {{ $slide['cta_text'] }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="hero-mobile-card hero-mobile-card-photo">
                                <div class="hero-mobile-photo-container">
                                    <img src="{{ $slide['image_url'] }}" alt="{{ $slideTitle }}" class="hero-mobile-img" loading="lazy">
                                </div>
                                <div class="hero-mobile-photo-caption">
                                    @if(!empty($slide['badge']))
                                        <span class="hero-badge-doc">{{ $slide['badge'] }}</span>
                                    @endif
                                    <h3 class="hero-mobile-photo-title">{{ $slide['title'] }}</h3>
                                    @if(!empty($slide['description']))
                                        <p class="hero-mobile-photo-desc">{{ $slide['description'] }}</p>
                                    @endif
                                    @if(!empty($slide['cta_text']))
                                        <div class="hero-mobile-cta-wrap-doc">
                                            <a href="{{ !empty($slide['cta_url']) ? $slide['cta_url'] : '#kuota' }}" class="btn-hero-cta btn-hero-doc-cta">
                                                {{ $slide['cta_text'] }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Slider Indicator Dots (Desktop: 1 Info + N Banner, Mobile: 3 Info + N Banner) -->
        <div class="hero-slider-dots hero-dots-desktop" id="heroSliderDotsDesktop">
            <button type="button" class="hero-dot-btn active" data-slide-index="0" aria-label="Ke slide 1 (Informasi Pendaftaran)"></button>
            @foreach($heroSlides as $sIdx => $slide)
                <button type="button" class="hero-dot-btn" data-slide-index="{{ $sIdx + 1 }}" aria-label="Ke slide {{ $sIdx + 2 }} ({{ $slide['title'] }})"></button>
            @endforeach
        </div>
        <div class="hero-slider-dots hero-dots-mobile" id="heroSliderDotsMobile">
            <button type="button" class="hero-dot-btn active" data-slide-index="0" aria-label="Ke slide 1 (Pendaftaran)"></button>
            <button type="button" class="hero-dot-btn" data-slide-index="1" aria-label="Ke slide 2 (Persyaratan)"></button>
            <button type="button" class="hero-dot-btn" data-slide-index="2" aria-label="Ke slide 3 (Dokumen)"></button>
            @foreach($heroSlides as $sIdx => $slide)
                <button type="button" class="hero-dot-btn" data-slide-index="{{ $sIdx + 3 }}" aria-label="Ke slide {{ $sIdx + 4 }} ({{ $slide['title'] }})"></button>
            @endforeach
        </div>
    </div>
</section>

