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
        ['label' => 'Verifikasi Dokumen Sah', 'url' => '/verifikasi-dokumen/cek'],
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

<footer id="kontak" class="landing-footer" data-wp-block="footer" data-wp-block-name="Footer & Kontak" data-wp-block-id="{{ $blockId ?? 'block-footer-1' }}" style="order: {{ $order ?? 5 }}; {{ !($visible ?? true) && !($isEditorMode ?? false) ? 'display: none !important;' : '' }}">
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
                    <svg class="floating-wifi-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 8.82a15 15 0 0 1 20 0" stroke-width="2.2"></path>
                        <path d="M5 12.86a10.5 10.5 0 0 1 14 0" stroke-width="2.2"></path>
                        <path d="M8.5 16.9a6 6 0 0 1 7 0" stroke-width="2.2"></path>
                        <circle cx="12" cy="20" r="1.5" fill="currentColor" stroke="none"></circle>
                    </svg>
                </li>
            @endfor
        </ul>
    </div>

    <div class="landing-footer-grid">
        <!-- Kolom 1: Alamat -->
        <div>
            <h3 class="footer-col-title">Alamat</h3>
            <div class="footer-address-block">
                <p>{{ $footerAlamat }}</p>
                <p>Telepon: {{ $footerTelepon }}</p>
                <p>Email: {{ $footerEmail }}</p>
                <p>{{ $footerJamLayanan }}</p>
            </div>
        </div>

        <!-- Kolom 2: Media Sosial -->
        <div>
            <h3 class="footer-col-title">Media Sosial</h3>
            <ul class="footer-links-clean">
                @foreach($footerMedsos as $medsos)
                    @if(!empty($medsos['label']) && !empty($medsos['url']))
                        <li><a href="{{ $medsos['url'] }}" target="_blank" rel="noopener noreferrer">{{ $medsos['label'] }}</a></li>
                    @endif
                @endforeach
            </ul>
        </div>

        <!-- Kolom 3: Tautan Terkait -->
        <div>
            <h3 class="footer-col-title">Tautan Terkait</h3>
            <ul class="footer-links-clean">
                @foreach($footerTautan as $tautan)
                    @if(!empty($tautan['label']) && !empty($tautan['url']))
                        @php
                            $isExternal = str_starts_with($tautan['url'], 'http://') || str_starts_with($tautan['url'], 'https://');
                        @endphp
                        <li>
                            <a href="{{ $isExternal ? $tautan['url'] : url($tautan['url']) }}" @if($isExternal) target="_blank" rel="noopener noreferrer" @endif>
                                {{ $tautan['label'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>

        <!-- Kolom 4: Akun -->
        <div>
            <h3 class="footer-col-title">Akun</h3>
            <ul class="footer-links-clean">
                @foreach($footerAkun as $itemAkun)
                    @if(!empty($itemAkun['label']) && !empty($itemAkun['url']))
                        @php
                            $isExternalAkun = str_starts_with($itemAkun['url'], 'http://') || str_starts_with($itemAkun['url'], 'https://');
                        @endphp
                        <li>
                            <a href="{{ $isExternalAkun ? $itemAkun['url'] : url($itemAkun['url']) }}">
                                {{ $itemAkun['label'] }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Footer Bottom Bar Clean -->
    <div class="footer-bottom-bar-clean">
        <p>&copy; {{ date('Y') }} {{ $footerCopyright }}</p>
    </div>
</footer>

