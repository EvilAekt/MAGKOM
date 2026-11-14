@php
    $heading = $content['heading'] ?? 'Mengapa Memilih Magang di Kominfo Sleman?';
    $defaultItems = [
        ['title' => 'Mentor Ahli ASN', 'desc' => 'Bimbingan intensif dari praktisi teknologi dan komunikasi bersertifikasi dinas.', 'icon' => '👥'],
        ['title' => 'Proyek Riil & Nyata', 'desc' => 'Terlibat langsung dalam layanan publik berbasis digital Kabupaten Sleman.', 'icon' => '💻'],
        ['title' => 'Penyelesaian & Portofolio', 'desc' => 'Pengalaman kerja riil dan portofolio proyek pemerintahan untuk karir profesional.', 'icon' => '🏆'],
    ];
    $items = !empty($content['items']) && is_array($content['items']) ? $content['items'] : $defaultItems;
@endphp

<section id="{{ $blockId ?? 'block-features' }}" class="landing-section landing-block-features" data-wp-block="features" data-wp-block-name="Keunggulan Program" data-wp-block-id="{{ $blockId ?? 'block-features' }}" style="order: {{ $order ?? 1 }}; {{ !($visible ?? true) && !($isEditorMode ?? false) ? 'display: none !important;' : '' }} padding: 3rem 1rem; max-width: var(--wp-container-max-w); margin-left: auto; margin-right: auto;">
    <h2 class="section-heading-lg" style="text-align: center; margin-bottom: 2rem;">{{ $heading }}</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem;">
        @foreach($items as $item)
            <div class="bidang-kuota-card" style="padding: 1.5rem; border: 1px solid #e2e8f0; border-radius: var(--wp-card-border-radius, 12px); background: #ffffff;">
                <div style="font-size: 32px; margin-bottom: 0.75rem;">{{ $item['icon'] ?? '⭐' }}</div>
                <h4 style="font-size: 18px; font-weight: 700; margin-bottom: 0.5rem; color: #1B3860;">{{ $item['title'] ?? '' }}</h4>
                <p style="font-size: 14px; color: #64748b; line-height: 1.5; margin: 0;">{{ $item['desc'] ?? '' }}</p>
            </div>
        @endforeach
    </div>
</section>

