@php
    $labelPersyaratan = $content['label_persyaratan'] ?? 'Persyaratan Umum';
    $persyaratanText = $content['persyaratan_umum'] ?? '';
    $persyaratanList = $persyaratanText ? array_filter(array_map('trim', explode("\n", (string) $persyaratanText))) : [];

    $labelDokumen = $content['label_dokumen'] ?? 'Dokumen Pendaftaran';
    $dokumenText = $content['dokumen_pendaftaran'] ?? '';
    $dokumenList = $dokumenText ? array_filter(array_map('trim', explode("\n", (string) $dokumenText))) : [];
@endphp

<section id="{{ $blockId ?? 'block-informasi' }}" class="landing-section landing-block-informasi" data-wp-block="informasi" data-wp-block-name="Persyaratan & Berkas" data-wp-block-id="{{ $blockId ?? 'block-informasi' }}" style="order: {{ $order ?? 1 }}; {{ !($visible ?? true) && !($isEditorMode ?? false) ? 'display: none !important;' : '' }} padding-top: 3rem; padding-bottom: 3rem; max-width: var(--wp-container-max-w); margin-left: auto; margin-right: auto;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
        <!-- Kartu Persyaratan Umum -->
        <div class="bidang-kuota-card" style="padding: 2rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: var(--wp-card-border-radius, 12px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                <span style="font-size: 1.75rem;">📋</span>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">{{ $labelPersyaratan }}</h3>
            </div>
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($persyaratanList as $pItem)
                    <li style="display: flex; align-items: flex-start; gap: 0.5rem; color: #475569; font-size: 0.95rem; line-height: 1.5;">
                        <span style="color: #10b981; font-weight: bold;">✓</span>
                        <span>{{ $pItem }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Kartu Dokumen Pendaftaran -->
        <div class="bidang-kuota-card" style="padding: 2rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: var(--wp-card-border-radius, 12px); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                <span style="font-size: 1.75rem;">📁</span>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0;">{{ $labelDokumen }}</h3>
            </div>
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($dokumenList as $dItem)
                    <li style="display: flex; align-items: flex-start; gap: 0.5rem; color: #475569; font-size: 0.95rem; line-height: 1.5;">
                        <span style="color: #4f46e5; font-weight: bold;">▪</span>
                        <span>{{ $dItem }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

