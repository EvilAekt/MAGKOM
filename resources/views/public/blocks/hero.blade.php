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

<section id="beranda" class="landing-section" data-wp-block="hero" data-wp-block-name="Hero Banner" data-wp-block-id="{{ $blockId ?? 'block-hero-1' }}" style="order: {{ $order ?? 1 }}; {{ !($visible ?? true) && !($isEditorMode ?? false) ? 'display: none !important;' : '' }}">
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
                            <p class="hero-kicker" data-wp-editable="hero_kicker">{{ $cmsKicker }}</p>
                            @if($cmsTitle && trim(str_replace(["\r", "\n"], ' ', $cmsTitle)) !== 'Program Magang Kominfo Sleman')
                                <h1 class="hero-title" data-wp-editable="hero_title">{!! nl2br(e($cmsTitle)) !!}</h1>
                            @else
                                <h1 class="hero-title" data-wp-editable="hero_title">
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
                            <a href="{{ $cmsCtaUrl }}" class="btn-hero-cta">
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

                <!-- SLIDE 2: Dokumentasi Foto Penuh Resmi Diskominfo Sleman -->
                <div class="hero-slide-item">
                    <div class="hero-full-image-slide" id="heroFullImageSlide">
                        <img src="{{ $heroImageUrl }}" 
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

