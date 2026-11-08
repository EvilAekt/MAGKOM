@php
    $heading = $content['heading'] ?? ($content['title'] ?? 'Siap Bergabung Bersama Diskominfo Sleman?');
    $desc = $content['description'] ?? ($content['subtitle'] ?? 'Dapatkan pengalaman berharga di instansi pemerintah dan kembangkan keahlian Anda.');
    $btnText = $content['btn_text'] ?? ($content['primary_btn_text'] ?? 'Daftar Sekarang');
    $btnUrl = $content['btn_url'] ?? ($content['primary_btn_url'] ?? '#kuota');
@endphp

<section id="{{ $blockId ?? 'block-cta' }}" class="landing-section landing-block-cta" data-wp-block="cta_banner" data-wp-block-name="Banner Promosi CTA" data-wp-block-id="{{ $blockId ?? 'block-cta' }}" style="order: {{ $order ?? 1 }}; {{ !($visible ?? true) && !($isEditorMode ?? false) ? 'display: none !important;' : '' }} padding: 3rem 1rem; text-align: center; background: linear-gradient(135deg, #1B3860 0%, #4F46E5 100%); color: #ffffff; border-radius: var(--wp-card-border-radius, 12px); margin: 2rem auto; max-width: var(--wp-container-max-w);">
    <h3 style="font-size: 28px; font-weight: 700; margin-bottom: 0.75rem;">{{ $heading }}</h3>
    <p style="font-size: 16px; opacity: 0.9; margin-bottom: 1.5rem; max-width: 600px; margin-left: auto; margin-right: auto;">{{ $desc }}</p>
    <a href="{{ $btnUrl }}" class="btn-header-register" style="display: inline-block; padding: 12px 28px; font-weight: 700; font-size: 15px; background: #ffffff; color: #1B3860; border-radius: 6px; text-decoration: none;">
        {{ $btnText }}
    </a>
</section>

