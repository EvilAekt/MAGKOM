@php
    $footerAlamat = $content['alamat'] ?? \App\Support\Pengaturan::get('landing_footer_alamat', 'Jl Parasamya (Komplek Kantor Bupati Sleman) Beran, Tridadi, Sleman, Yogyakarta 55511');
    $footerTelepon = $content['telepon'] ?? \App\Support\Pengaturan::get('landing_footer_telepon', '(0274) 868405');
    $footerEmail = $content['email'] ?? \App\Support\Pengaturan::get('landing_footer_email', 'kominfo@slemankab.go.id');
    $footerJamLayanan = $content['jam_layanan'] ?? \App\Support\Pengaturan::get('landing_footer_jam_layanan', 'Senin sampai Kamis 07.30 sampai 15.30 WIB, Jumat 07.30 sampai 14.30 WIB');
    $footerCopyright = $content['copyright'] ?? \App\Support\Pengaturan::get('landing_footer_copyright', 'Dinas Komunikasi dan Informatika Kabupaten Sleman. Seluruh hak cipta dilindungi.');

    $defaultFooterMedsos = [
        ['platform' => 'Instagram', 'label' => 'kominfo_sleman', 'url' => 'https://instagram.com/kominfo_sleman'],
        ['platform' => 'Twitter / X', 'label' => 'diskominfosleman', 'url' => 'https://twitter.com/diskominfosleman'],
    ];
    $footerMedsos = !empty($content['medsos']) && is_array($content['medsos'])
        ? $content['medsos']
        : \App\Support\Pengaturan::json('landing_footer_medsos', $defaultFooterMedsos);
    if (empty($footerMedsos)) {
        $footerMedsos = $defaultFooterMedsos;
    }

    $defaultFooterTautan = [
        ['label' => 'Kebijakan Privasi', 'url' => '/kebijakan-privasi'],
        ['label' => 'Situs Kominfo Sleman', 'url' => 'https://kominfo.slemankab.go.id'],
        ['label' => 'Sleman Digital', 'url' => 'https://slemankab.go.id'],
    ];
    $footerTautan = !empty($content['tautan']) && is_array($content['tautan'])
        ? $content['tautan']
        : \App\Support\Pengaturan::json('landing_footer_tautan', $defaultFooterTautan);
    if (empty($footerTautan)) {
        $footerTautan = $defaultFooterTautan;
    }

    $defaultFooterAkun = [
        ['label' => 'Masuk Peserta', 'url' => '/login'],
        ['label' => 'Masuk Pembimbing', 'url' => '/login'],
        ['label' => 'Masuk Admin', 'url' => '/login'],
    ];
    $footerAkun = !empty($content['akun']) && is_array($content['akun'])
        ? $content['akun']
        : \App\Support\Pengaturan::json('landing_footer_akun', $defaultFooterAkun);
    if (empty($footerAkun)) {
        $footerAkun = $defaultFooterAkun;
    }
@endphp

<footer id="kontak" class="landing-footer" data-wp-block="footer" data-wp-block-name="Footer & Kontak" data-wp-block-id="{{ $blockId ?? 'block-footer-1' }}" style="order: {{ $order ?? 5 }}; background-color: #122846 !important; {{ !($visible ?? true) && !($isEditorMode ?? false) ? 'display: none !important;' : '' }}">
    <!-- Background Animated Wifi Signal Waves & Floating Nodes -->
    <div class="footer-wifi-particles" aria-hidden="true">
        <!-- Pulsing Concentric Radar / Wifi Wave Source -->
        <div class="wifi-radar-source">
            <div class="wifi-pulse-circle wave-1"></div>
            <div class="wifi-pulse-circle wave-2"></div>
            <div class="wifi-pulse-circle wave-3"></div>
            <div class="wifi-pulse-circle wave-4"></div>
        </div>

        <!-- Floating Golden WiFi Icons -->
        <ul class="wifi-signal-nodes">
            @for($i = 0; $i < 10; $i++)
                <li>
                    <svg class="floating-wifi-svg" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 8.82a15 15 0 0 1 20 0" stroke-width="2.2" stroke="#fbbf24"></path>
                        <path d="M5 12.86a10.5 10.5 0 0 1 14 0" stroke-width="2.2" stroke="#fbbf24"></path>
                        <path d="M8.5 16.9a6 6 0 0 1 7 0" stroke-width="2.2" stroke="#fbbf24"></path>
                        <circle cx="12" cy="20" r="1.5" fill="#fbbf24" stroke="none"></circle>
                    </svg>
                </li>
            @endfor
        </ul>
    </div>

    <div class="landing-footer-grid">
        <!-- Kolom 1: Profil Dinas & Alamat -->
        <div class="footer-col-profil">
            <div class="footer-brand-header">
                @include('partials.logo-sleman', [
                    'class' => 'brand-logo-img',
                    'width' => '36',
                    'height' => '36',
                    'style' => 'width: 36px; height: 36px; object-fit: contain;',
                    'loading' => 'lazy',
                    'fetchpriority' => 'low'
                ])
                <div>
                    <span class="footer-brand-title">MAGKOM</span>
                    <span class="footer-brand-sub">{{ __('Portal Magang · Diskominfo Sleman') }}</span>
                </div>
            </div>
            <div class="footer-address-block">
                <p data-wp-editable="footer_alamat">{{ __($footerAlamat) }}</p>
                <p>{{ __('Telepon') }}: <span data-wp-editable="footer_telepon">{{ $footerTelepon }}</span></p>
                <p>Email: <span data-wp-editable="footer_email">{{ $footerEmail }}</span></p>
            </div>
        </div>

        <!-- Kolom 2: Tautan Terkait (Atas Kiri di Mobile, Paling Kiri di Desktop) -->
        <div class="footer-col-tautan">
            <h3 class="footer-col-title">{{ __('Tautan Terkait') }}</h3>
            <ul class="footer-links-clean">
                @foreach($footerTautan as $tautan)
                    @if(!empty($tautan['label']) && !empty($tautan['url']))
                        @php
                            $isExternal = str_starts_with($tautan['url'], 'http://') || str_starts_with($tautan['url'], 'https://');
                        @endphp
                        <li>
                            <a href="{{ $isExternal ? $tautan['url'] : url($tautan['url']) }}" @if($isExternal) target="_blank" rel="noopener noreferrer" @endif>
                                {{ __($tautan['label']) }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>

        <!-- Kolom 3: Akun (Atas Kanan di Mobile, Kanan Tautan di Desktop) -->
        <div class="footer-col-akun">
            <h3 class="footer-col-title">{{ __('Akun') }}</h3>
            <ul class="footer-links-clean">
                @foreach($footerAkun as $itemAkun)
                    @if(!empty($itemAkun['label']) && !empty($itemAkun['url']))
                        @php
                            $isExternalAkun = str_starts_with($itemAkun['url'], 'http://') || str_starts_with($itemAkun['url'], 'https://');
                        @endphp
                        <li>
                            <a href="{{ $isExternalAkun ? $itemAkun['url'] : url($itemAkun['url']) }}">
                                {{ __($itemAkun['label']) }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>

        <!-- Kolom 4: Media Sosial (Bawah Kiri di Mobile, Kanan Tautan di Desktop) -->
        <div class="footer-col-medsos">
            <h3 class="footer-col-title">{{ __('Media Sosial') }}</h3>
            <ul class="footer-links-clean">
                @foreach($footerMedsos as $medsos)
                    @if(!empty($medsos['label']) && !empty($medsos['url']))
                        <li><a href="{{ $medsos['url'] }}" target="_blank" rel="noopener noreferrer">{{ $medsos['label'] }}</a></li>
                    @endif
                @endforeach
            </ul>
        </div>

    </div>

    <!-- Footer Bottom Bar Clean -->
    <div class="footer-bottom-bar-clean">
        <!-- Mitra / Kolaborasi Institusi Logos -->
        <div class="footer-partner-logos" aria-label="Identitas Kemitraan Diskominfo Sleman dan AMIKOM">
            <!-- 1. Logo Dinas (Pemkab / Diskominfo Sleman) -->
            <a href="https://kominfo.slemankab.go.id" target="_blank" rel="noopener noreferrer" class="footer-partner-link" aria-label="Kunjungi Website Diskominfo Kabupaten Sleman" title="Dinas Komunikasi dan Informatika Kabupaten Sleman">
                <img src="{{ asset('images/logo-sleman.png') }}" alt="Logo Dinas Komunikasi dan Informatika Kabupaten Sleman" class="footer-partner-img footer-partner-sleman" width="38" height="46" loading="lazy">
            </a>

            <!-- 2. Logo Universitas AMIKOM Yogyakarta -->
            <a href="https://amikom.ac.id" target="_blank" rel="noopener noreferrer" class="footer-partner-link" aria-label="Kunjungi Website Universitas AMIKOM Yogyakarta" title="Universitas AMIKOM Yogyakarta">
                <img src="{{ asset('images/logo-amikom.png') }}" alt="Logo Universitas AMIKOM Yogyakarta" class="footer-partner-img footer-partner-amikom" width="46" height="46" loading="lazy">
            </a>

            <!-- 3. Logo D3 Manajemen Informatika AMIKOM -->
            <a href="https://mi.amikom.ac.id" target="_blank" rel="noopener noreferrer" class="footer-partner-link" aria-label="Kunjungi Website D3 Manajemen Informatika AMIKOM" title="D3 Manajemen Informatika Universitas AMIKOM Yogyakarta">
                <img src="{{ asset('images/logo-mi.png') }}" alt="Logo D3 Manajemen Informatika AMIKOM" class="footer-partner-img footer-partner-mi" width="58" height="46" loading="lazy">
            </a>
        </div>

        <p class="footer-copyright-text">&copy; {{ date('Y') }} <span data-wp-editable="footer_copyright">{{ __($footerCopyright) }}</span></p>
    </div>
</footer>

