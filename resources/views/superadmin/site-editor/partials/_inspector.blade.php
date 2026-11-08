<aside id="wp-site-inspector" class="wp-site-inspector" aria-label="Panel Pengaturan Canvas Inspector">
    {{-- TAB SWITCHER UTAMA: HALAMAN VS BLOK DENGAN TOMBOL TUTUP --}}
    <div class="wp-inspector-top-bar">
        <div class="wp-inspector-tabs" role="tablist">
            <button type="button" class="wp-tab-btn" data-tab="page" role="tab" aria-selected="false">
                Page
            </button>
            <button type="button" class="wp-tab-btn is-active" data-tab="block" role="tab" aria-selected="true">
                Block
            </button>
        </div>
        <button type="button" id="btn-close-inspector" class="wp-icon-btn wp-inspector-close-btn" title="Tutup Inspector (✕)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
    </div>

    {{-- KONTEN TAB: BLOK AKTIF --}}
    <div id="wp-tab-content-block" class="wp-tab-pane is-active" role="tabpanel">
        {{-- IDENTITAS BLOK TERPILIH --}}
        <div class="wp-block-header-card">
            <div class="wp-block-badge-row">
                <span class="wp-block-type-icon" id="wp-active-block-icon">🔷</span>
                <div>
                    <h4 id="wp-active-block-title" class="wp-active-block-title">Hero Banner</h4>
                    <p id="wp-active-block-subtitle" class="wp-active-block-desc">Section utama dengan background gambar dan konten utama.</p>
                </div>
            </div>
        </div>

        {{-- SUBTABS BLOK: GENERAL / STYLE / ADVANCED --}}
        <div class="wp-block-subtabs" role="tablist">
            <button type="button" class="wp-subtab-btn is-active" data-subtab="general" role="tab">General</button>
            <button type="button" class="wp-subtab-btn" data-subtab="style" role="tab">Style</button>
            <button type="button" class="wp-subtab-btn" data-subtab="advanced" role="tab">Advanced</button>
        </div>

        {{-- SUBPANE 1: GENERAL (KONTEN BLOK DINAMIS UNTUK SELURUH 9 BLOK) --}}
        <div id="wp-subpane-general" class="wp-subpane is-active">
            {{-- 1. ACCORDION KONTEN HERO --}}
            <details class="wp-accordion block-form-hero" open>
                <summary class="wp-accordion-summary">
                    <span class="wp-accordion-heading">Konten Hero Banner</span>
                    <span class="wp-accordion-arrow">▾</span>
                </summary>
                <div class="wp-accordion-body">
                    <div class="wp-field-group">
                        <label for="input-hero-title">Judul Utama</label>
                        <textarea id="input-hero-title" class="wp-textarea-input" rows="2" data-field="landing_hero_title">{{ $cmsData['landing_hero_title'] }}</textarea>
                    </div>

                    <div class="wp-field-group">
                        <label for="input-hero-kicker">Sub Judul / Kicker</label>
                        <input type="text" id="input-hero-kicker" class="wp-text-input" value="{{ $cmsData['landing_hero_kicker'] }}" data-field="landing_hero_kicker">
                    </div>

                    <div class="wp-field-group">
                        <label for="input-hero-period-info">Info Periode</label>
                        <input type="text" id="input-hero-period-info" class="wp-text-input" value="{{ $cmsData['landing_hero_period_info'] }}" data-field="landing_hero_period_info">
                    </div>

                    <div class="wp-field-group">
                        <label for="input-hero-cta-text">Tombol Utama</label>
                        <div class="wp-input-with-icon">
                            <input type="text" id="input-hero-cta-text" class="wp-text-input" value="{{ $cmsData['landing_hero_cta_text'] }}" data-field="landing_hero_cta_text" placeholder="Teks tombol">
                            <input type="text" id="input-hero-cta-url" class="wp-text-input" value="{{ $cmsData['landing_hero_cta_primary_url'] }}" data-field="landing_hero_cta_primary_url" placeholder="#kuota">
                        </div>
                    </div>

                    <div class="wp-field-group">
                        <label for="input-hero-cta-sec-text">Tombol Sekunder</label>
                        <div class="wp-input-with-icon">
                            <input type="text" id="input-hero-cta-sec-text" class="wp-text-input" value="{{ $cmsData['landing_hero_cta_secondary_text'] }}" data-field="landing_hero_cta_secondary_text" placeholder="Teks tombol">
                            <input type="text" id="input-hero-cta-sec-url" class="wp-text-input" value="{{ $cmsData['landing_hero_cta_secondary_url'] }}" data-field="landing_hero_cta_secondary_url" placeholder="#tahapan">
                        </div>
                    </div>

                    {{-- Background Image dengan Ganti Gambar --}}
                    <div class="wp-field-group">
                        <label>Background Banner</label>
                        <div class="wp-media-preview-box">
                            <img id="wp-hero-banner-thumb" 
                                 src="{{ $cmsData['landing_hero_image'] ? Storage::url($cmsData['landing_hero_image']) : asset('images/gedung-kominfo.jpg') }}" 
                                 alt="Thumbnail Banner Hero" 
                                 onerror="this.src='{{ asset('favicon.png') }}'">
                            <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" id="btn-change-hero-image">
                                Ganti Gambar
                            </button>
                        </div>
                    </div>

                    <div class="wp-field-group wp-toggle-row">
                        <label for="toggle-hero-overlay">Aktifkan Efek Lapisan Overlay</label>
                        <input type="checkbox" id="toggle-hero-overlay" class="wp-toggle-checkbox" data-field="landing_hero_overlay_active" {{ $cmsData['landing_hero_overlay_active'] ? 'checked' : '' }}>
                    </div>
                </div>
            </details>

            {{-- 10. ACCORDION KONTEN KEBIJAKAN PRIVASI --}}

            {{-- 2. ACCORDION KONTEN KUOTA --}}
            <details class="wp-accordion block-form-kuota" style="display: none;">
                <summary class="wp-accordion-summary">
                    <span class="wp-accordion-heading">Pengaturan Kuota &amp; Formasi</span>
                    <span class="wp-accordion-arrow">▾</span>
                </summary>
                <div class="wp-accordion-body">
                    <div class="wp-field-group">
                        <label for="input-kuota-heading">Judul Bagian</label>
                        <input type="text" id="input-kuota-heading" class="wp-text-input" value="{{ $cmsData['landing_kuota_heading'] }}" data-field="landing_kuota_heading">
                    </div>
                    <div class="wp-field-group">
                        <label for="input-kuota-subheading">Deskripsi Petunjuk</label>
                        <textarea id="input-kuota-subheading" class="wp-textarea-input" rows="3" data-field="landing_kuota_subheading">{{ $cmsData['landing_kuota_subheading'] }}</textarea>
                    </div>
                </div>
            </details>

            {{-- 10. ACCORDION KONTEN KEBIJAKAN PRIVASI --}}

            {{-- 3. ACCORDION KONTEN TAHAPAN --}}
            <details class="wp-accordion block-form-tahapan" style="display: none;">
                <summary class="wp-accordion-summary">
                    <span class="wp-accordion-heading">Pengaturan Alur &amp; Tahapan</span>
                    <span class="wp-accordion-arrow">▾</span>
                </summary>
                <div class="wp-accordion-body">
                    <div class="wp-field-group">
                        <label for="input-tahapan-heading">Judul Bagian Alur</label>
                        <input type="text" id="input-tahapan-heading" class="wp-text-input" value="{{ $cmsData['landing_tahapan_heading'] }}" data-field="landing_tahapan_heading">
                    </div>
                    <div class="wp-field-group">
                        <label for="input-tahapan-summary">Ringkasan Alur Naratif</label>
                        <textarea id="input-tahapan-summary" class="wp-textarea-input" rows="4" data-field="landing_tahapan_summary">{{ $cmsData['landing_tahapan_summary'] }}</textarea>
                    </div>

                    {{-- 4 Pos Halte Checkpoint (Sinkron Otomatis ke Landing & Peta Alur) --}}
                    @php
                        $steps = $cmsData['landing_tahapan_steps'] ?? [];
                    @endphp
                    <div class="wp-field-subheading" style="margin-top: 1rem; margin-bottom: 0.5rem; font-weight: 700; font-size: 0.8125rem; color: var(--wp-color-text, #1e293b);">
                        4 Pos Halte Checkpoint (Landing &amp; Peta Alur)
                    </div>
                    @for($i = 0; $i < 4; $i++)
                        @php
                            $stepNum = str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT);
                            $stepData = $steps[$i] ?? ['title' => 'Pos '.$stepNum, 'desc' => ''];
                        @endphp
                        <div class="wp-card-subpanel" style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 0.75rem; margin-bottom: 0.75rem;">
                            <div style="font-weight: 700; font-size: 0.75rem; color: #475569; margin-bottom: 0.35rem;">
                                POS {{ $stepNum }}
                            </div>
                            <div class="wp-field-group" style="margin-bottom: 0.5rem;">
                                <label for="input-step-title-{{ $i }}" style="font-size: 0.75rem;">Judul Pos</label>
                                <input type="text" id="input-step-title-{{ $i }}" class="wp-text-input wp-tahapan-step-title" value="{{ $stepData['title'] ?? '' }}" data-step-index="{{ $i }}" placeholder="Nama pos">
                            </div>
                            <div class="wp-field-group" style="margin-bottom: 0;">
                                <label for="input-step-desc-{{ $i }}" style="font-size: 0.75rem;">Deskripsi Pos</label>
                                <textarea id="input-step-desc-{{ $i }}" class="wp-textarea-input wp-tahapan-step-desc" rows="2" data-step-index="{{ $i }}" placeholder="Deskripsi narasi tahapan">{{ $stepData['desc'] ?? '' }}</textarea>
                            </div>
                        </div>
                    @endfor

                    <div class="wp-field-group">
                        <label for="input-tahapan-btn-peta">Label Tombol Peta Alur</label>
                        <input type="text" id="input-tahapan-btn-peta" class="wp-text-input" value="{{ $cmsData['landing_tahapan_btn_peta'] }}" data-field="landing_tahapan_btn_peta">
                    </div>

                    <div class="wp-field-group">
                        <label for="input-tahapan-btn-pdf">Label Tombol Unduh PDF</label>
                        <input type="text" id="input-tahapan-btn-pdf" class="wp-text-input" value="{{ $cmsData['landing_tahapan_btn_pdf'] }}" data-field="landing_tahapan_btn_pdf">
                    </div>

                    {{-- Kotak Pengelolaan Berkas Buku Panduan PDF --}}
                    <div class="wp-field-group" style="margin-top: 1rem; border-top: 1px dashed #CBD5E1; padding-top: 0.75rem;">
                        <label style="font-weight: 700; display: block; margin-bottom: 0.25rem;">Dokumen Buku Panduan PDF</label>
                        <div class="wp-pdf-status-card" style="background: #F1F5F9; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                <span style="font-size: 1.25rem;">📄</span>
                                <div style="overflow: hidden;">
                                    <div id="wp-pdf-active-name" style="font-weight: 600; font-size: 0.8125rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #0F172A;">
                                        {{ $cmsData['landing_panduan_pdf_name'] ?? 'Panduan-Resmi-Magang.pdf' }}
                                    </div>
                                    <div id="wp-pdf-status-desc" style="font-size: 0.6875rem; color: #64748B;">
                                        {{ $cmsData['landing_panduan_pdf'] ? 'Berkas Kustom Diunggah' : 'Generator PDF Bawaan Sistem' }}
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                                <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" id="btn-choose-pdf-media">
                                    Pilih Media
                                </button>
                                <a href="{{ route('panduan.pdf') }}" target="_blank" class="wp-btn wp-btn-ghost wp-btn-sm" id="btn-preview-pdf-doc" title="Buka Dokumen PDF">
                                    Unduh
                                </a>
                                <button type="button" class="wp-btn wp-btn-danger wp-btn-sm" id="btn-reset-panduan-pdf" title="Kembalikan ke generator PDF resmi bawaan sistem" {{ empty($cmsData['landing_panduan_pdf']) ? 'style=display:none;' : '' }}>
                                    Reset Bawaan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </details>

            {{-- 10. ACCORDION KONTEN KEBIJAKAN PRIVASI --}}

            {{-- 4. ACCORDION KONTEN INFORMASI & PERSYARATAN --}}
            <details class="wp-accordion block-form-informasi" style="display: none;">
                <summary class="wp-accordion-summary">
                    <span class="wp-accordion-heading">Persyaratan &amp; Dokumen</span>
                    <span class="wp-accordion-arrow">▾</span>
                </summary>
                <div class="wp-accordion-body">
                    <div class="wp-field-group">
                        <label for="input-label-persyaratan">Label Persyaratan</label>
                        <input type="text" id="input-label-persyaratan" class="wp-text-input" value="{{ $cmsData['landing_label_persyaratan'] }}" data-field="landing_label_persyaratan">
                    </div>
                    <div class="wp-field-group">
                        <label for="input-persyaratan-umum">Poin Persyaratan Umum (Per Baris)</label>
                        <textarea id="input-persyaratan-umum" class="wp-textarea-input" rows="4" data-field="landing_persyaratan_umum">{{ $cmsData['landing_persyaratan_umum'] }}</textarea>
                    </div>
                    <div class="wp-field-group">
                        <label for="input-label-dokumen">Label Berkas Wajib</label>
                        <input type="text" id="input-label-dokumen" class="wp-text-input" value="{{ $cmsData['landing_label_dokumen'] }}" data-field="landing_label_dokumen">
                    </div>
                    <div class="wp-field-group">
                        <label for="input-dokumen-pendaftaran">Poin Dokumen Administrasi (Per Baris)</label>
                        <textarea id="input-dokumen-pendaftaran" class="wp-textarea-input" rows="4" data-field="landing_dokumen_pendaftaran">{{ $cmsData['landing_dokumen_pendaftaran'] }}</textarea>
                    </div>
                </div>
            </details>

            {{-- 10. ACCORDION KONTEN KEBIJAKAN PRIVASI --}}

            {{-- 5. ACCORDION KONTEN FAQ --}}
            <details class="wp-accordion block-form-faq" style="display: none;">
                <summary class="wp-accordion-summary">
                    <span class="wp-accordion-heading">Pengaturan FAQ</span>
                    <span class="wp-accordion-arrow">▾</span>
                </summary>
                <div class="wp-accordion-body">
                    <div class="wp-field-group">
                        <label for="input-faq-heading">Judul Bagian FAQ</label>
                        <input type="text" id="input-faq-heading" class="wp-text-input" value="{{ $cmsData['landing_faq_heading'] }}" data-field="landing_faq_heading">
                    </div>
                    <p class="wp-field-help">Kelola butir pertanyaan dan jawaban interaktif secara langsung pada kanvas pratinjau atau CMS Form Builder.</p>
                </div>
            </details>

            {{-- 6. ACCORDION KONTEN FOOTER --}}
            <details class="wp-accordion block-form-footer" style="display: none;">
                <summary class="wp-accordion-summary">
                    <span class="wp-accordion-heading">Kontak &amp; Informasi Kedinasan</span>
                    <span class="wp-accordion-arrow">▾</span>
                </summary>
                <div class="wp-accordion-body">
                    <div class="wp-field-group">
                        <label for="input-footer-alamat-block">Alamat Lengkap Kantor</label>
                        <textarea id="input-footer-alamat-block" class="wp-textarea-input" rows="3" data-field="landing_footer_alamat">{{ $cmsData['landing_footer_alamat'] }}</textarea>
                    </div>
                    <div class="wp-field-group">
                        <label for="input-footer-telepon-block">Nomor Telepon Dinas</label>
                        <input type="text" id="input-footer-telepon-block" class="wp-text-input" value="{{ $cmsData['landing_footer_telepon'] }}" data-field="landing_footer_telepon">
                    </div>
                    <div class="wp-field-group">
                        <label for="input-footer-email-block">Email Helpdesk Resmi</label>
                        <input type="email" id="input-footer-email-block" class="wp-text-input" value="{{ $cmsData['landing_footer_email'] }}" data-field="landing_footer_email">
                    </div>
                    <div class="wp-field-group">
                        <label for="input-footer-jam-block">Jam Layanan ASN</label>
                        <input type="text" id="input-footer-jam-block" class="wp-text-input" value="{{ $cmsData['landing_footer_jam_layanan'] }}" data-field="landing_footer_jam_layanan">
                    </div>
                    <div class="wp-field-group">
                        <label for="input-footer-copy-block">Teks Hak Cipta</label>
                        <input type="text" id="input-footer-copy-block" class="wp-text-input" value="{{ $cmsData['landing_footer_copyright'] }}" data-field="landing_footer_copyright">
                    </div>
                </div>
            </details>

            {{-- 10. ACCORDION KONTEN KEBIJAKAN PRIVASI --}}

            {{-- 7. ACCORDION KONTEN CTA BANNER --}}
            <details class="wp-accordion block-form-cta_banner" style="display: none;">
                <summary class="wp-accordion-summary">
                    <span class="wp-accordion-heading">Kotak Ajakan Aksi (CTA Banner)</span>
                    <span class="wp-accordion-arrow">▾</span>
                </summary>
                <div class="wp-accordion-body">
                    <div class="wp-field-group">
                        <label for="input-cta-title">Judul Ajakan</label>
                        <input type="text" id="input-cta-title" class="wp-text-input" data-block-field="title" placeholder="Siap Mengembangkan Kompetensi Digital?">
                    </div>
                    <div class="wp-field-group">
                        <label for="input-cta-subtitle">Subjudul / Penjelasan Singkat</label>
                        <textarea id="input-cta-subtitle" class="wp-textarea-input" rows="2" data-block-field="subtitle" placeholder="Daftarkan diri Anda sekarang dan bergabunglah bersama keluarga besar Diskominfo Sleman."></textarea>
                    </div>
                    <div class="wp-field-group">
                        <label for="input-cta-button-text">Teks Tombol Aksi</label>
                        <input type="text" id="input-cta-button-text" class="wp-text-input" data-block-field="button_text" placeholder="Daftar Sekarang">
                    </div>
                    <div class="wp-field-group">
                        <label for="input-cta-button-url">Tautan Tombol Aksi</label>
                        <input type="text" id="input-cta-button-url" class="wp-text-input" data-block-field="button_url" placeholder="/register">
                    </div>
                </div>
            </details>

            {{-- 10. ACCORDION KONTEN KEBIJAKAN PRIVASI --}}

            {{-- 8. ACCORDION KONTEN FEATURES --}}
            <details class="wp-accordion block-form-features" style="display: none;">
                <summary class="wp-accordion-summary">
                    <span class="wp-accordion-heading">Keunggulan &amp; Fitur Program</span>
                    <span class="wp-accordion-arrow">▾</span>
                </summary>
                <div class="wp-accordion-body">
                    <div class="wp-field-group">
                        <label for="input-features-title">Judul Bagian</label>
                        <input type="text" id="input-features-title" class="wp-text-input" data-block-field="title" placeholder="Mengapa Magang di Kominfo Sleman?">
                    </div>
                    <div class="wp-field-group">
                        <label for="input-features-subtitle">Deskripsi Pengantar</label>
                        <textarea id="input-features-subtitle" class="wp-textarea-input" rows="2" data-block-field="subtitle" placeholder="Pengalaman nyata dalam pelayanan publik dan teknologi informasi pemerintahan."></textarea>
                    </div>
                    <p class="wp-field-help">Kartu keunggulan dapat diedit teks dan ikonnya secara langsung pada pratinjau kanvas.</p>
                </div>
            </details>

            {{-- 9. ACCORDION KONTEN SPACER --}}
            <details class="wp-accordion block-form-spacer" style="display: none;">
                <summary class="wp-accordion-summary">
                    <span class="wp-accordion-heading">Jarak Pemisah (Spacer)</span>
                    <span class="wp-accordion-arrow">▾</span>
                </summary>
                <div class="wp-accordion-body">
                    <div class="wp-field-group">
                        <div class="wp-field-label-row">
                            <label for="slider-spacer-height">Tinggi Ruang Pemisah</label>
                            <span class="wp-pixel-badge" id="display-spacer-height">48px</span>
                        </div>
                        <div class="wp-slider-row">
                            <input type="range" id="slider-spacer-height" class="wp-range-slider" 
                                   min="16" max="200" value="48" 
                                   data-block-style="height" data-unit="px">
                        </div>
                    </div>
                </div>
            </details>

            <details class="wp-accordion block-form-kebijakan" style="display: none;">
                <summary class="wp-accordion-summary">
                    <span class="wp-accordion-heading">Kebijakan Privasi &amp; Perlindungan Data</span>
                    <span class="wp-accordion-arrow">▾</span>
                </summary>
                <div class="wp-accordion-body">
                    <div class="wp-field-group">
                        <label for="input-kebijakan-badge">Badge Dasar Hukum</label>
                        <input type="text" id="input-kebijakan-badge" class="wp-text-input" value="{{ $cmsData['kebijakan_privasi_badge'] }}" data-field="kebijakan_privasi_badge">
                    </div>
                    <div class="wp-field-group">
                        <label for="input-kebijakan-title">Judul Kebijakan</label>
                        <input type="text" id="input-kebijakan-title" class="wp-text-input" value="{{ $cmsData['kebijakan_privasi_title'] }}" data-field="kebijakan_privasi_title">
                    </div>
                    <div class="wp-field-group">
                        <label for="input-kebijakan-updated-at">Tanggal Pembaruan</label>
                        <input type="text" id="input-kebijakan-updated-at" class="wp-text-input" value="{{ $cmsData['kebijakan_privasi_updated_at'] }}" data-field="kebijakan_privasi_updated_at">
                    </div>

                    {{-- Repeater Pasal-pasal Kebijakan Privasi --}}
                    <div class="wp-field-group" style="margin-top: 1rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <label style="font-weight: 700; font-size: 0.8125rem;">Pasal-Pasal Kebijakan</label>
                            <button type="button" id="btn-add-kebijakan-pasal" class="wp-btn wp-btn-secondary wp-btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.6rem;">
                                + Tambah Pasal
                            </button>
                        </div>
                        <div id="wp-kebijakan-sections-container">
                            @foreach($cmsData['kebijakan_privasi_sections'] ?? [] as $idx => $sec)
                                <div class="wp-card-subpanel wp-kebijakan-item-card" data-index="{{ $idx }}" style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 0.75rem; margin-bottom: 0.75rem; position: relative;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                                        <span class="wp-pasal-badge" style="font-weight: 700; font-size: 0.75rem; color: #1E40AF;">Pasal {{ $sec['nomor'] ?? ($idx + 1) }}</span>
                                        <button type="button" class="btn-remove-pasal wp-icon-btn wp-btn-danger" data-index="{{ $idx }}" style="width: 22px; height: 22px; font-size: 11px;" title="Hapus Pasal Ini">✕</button>
                                    </div>
                                    <div class="wp-field-group" style="margin-bottom: 0.4rem;">
                                        <label style="font-size: 0.75rem;">Judul Pasal</label>
                                        <input type="text" class="wp-text-input wp-pasal-judul" value="{{ $sec['judul'] ?? '' }}" placeholder="Contoh: Komitmen Perlindungan Data">
                                    </div>
                                    <div class="wp-field-group" style="margin-bottom: 0.4rem;">
                                        <label style="font-size: 0.75rem;">Isi Narasi Pasal</label>
                                        <textarea class="wp-textarea-input wp-pasal-konten" rows="3" placeholder="Uraian isi pasal">{{ $sec['konten'] ?? '' }}</textarea>
                                    </div>
                                    <div class="wp-field-group" style="margin-bottom: 0.4rem;">
                                        <label style="font-size: 0.75rem;">Butir Poin (Opsional, Per Baris)</label>
                                        <textarea class="wp-textarea-input wp-pasal-items" rows="2" placeholder="Poin 1&#10;Poin 2">{{ isset($sec['items']) && is_array($sec['items']) ? implode("\n", $sec['items']) : '' }}</textarea>
                                    </div>
                                    <div class="wp-field-group" style="margin-bottom: 0;">
                                        <label style="font-size: 0.75rem;">Catatan Khusus / Highlight (Opsional)</label>
                                        <input type="text" class="wp-text-input wp-pasal-highlight" value="{{ $sec['highlight'] ?? '' }}" placeholder="Teks sorotan khusus">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </details>

            {{-- 10. ACCORDION KONTEN KEBIJAKAN PRIVASI --}}
        </div>

        {{-- SUBPANE 2: STYLE (SCOPED BLOCK STYLING DENGAN DUKUNGAN RESPONSIVE) --}}
        <div id="wp-subpane-style" class="wp-subpane" style="display: none;">
            {{-- RESPONSIVE DEVICE SWITCHER KHUSUS STYLE --}}
            <div class="wp-responsive-style-tabs" role="tablist">
                <button type="button" class="wp-style-device-btn is-active" data-device-style="desktop" title="Gaya Khusus Desktop (>= 1024px)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                    <span>Desktop</span>
                </button>
                <button type="button" class="wp-style-device-btn" data-device-style="tablet" title="Gaya Khusus Tablet (768px - 1023px)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"></rect></svg>
                    <span>Tablet</span>
                </button>
                <button type="button" class="wp-style-device-btn" data-device-style="mobile" title="Gaya Khusus Mobile (< 768px)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="2" width="14" height="20" rx="2"></rect></svg>
                    <span>Mobile</span>
                </button>
            </div>

            {{-- SCOPED PADDING & SPACING --}}
            <details class="wp-accordion" open>
                <summary class="wp-accordion-summary">
                    <span class="wp-accordion-heading">Dimensi &amp; Spasi (Pixel)</span>
                    <span class="wp-accordion-arrow">▾</span>
                </summary>
                <div class="wp-accordion-body">
                    <div class="wp-field-group">
                        <div class="wp-field-label-row">
                            <label for="slider-block-padding-y">Padding Vertikal Blok</label>
                            <span class="wp-pixel-badge" id="display-block-padding-y">60px</span>
                        </div>
                        <div class="wp-slider-row">
                            <input type="range" id="slider-block-padding-y" class="wp-range-slider wp-scoped-slider" 
                                   min="0" max="200" value="60" 
                                   data-block-style="padding_y" data-unit="px">
                        </div>
                    </div>

                    <div class="wp-field-group">
                        <div class="wp-field-label-row">
                            <label for="slider-block-margin-y">Margin Vertikal Luar</label>
                            <span class="wp-pixel-badge" id="display-block-margin-y">0px</span>
                        </div>
                        <div class="wp-slider-row">
                            <input type="range" id="slider-block-margin-y" class="wp-range-slider wp-scoped-slider" 
                                   min="0" max="100" value="0" 
                                   data-block-style="margin_y" data-unit="px">
                        </div>
                    </div>
                </div>
            </details>

            {{-- 10. ACCORDION KONTEN KEBIJAKAN PRIVASI --}}

            {{-- SCOPED TYPOGRAPHY & ALIGNMENT --}}
            <details class="wp-accordion" open>
                <summary class="wp-accordion-summary">
                    <span class="wp-accordion-heading">Tipografi &amp; Ukuran Huruf</span>
                    <span class="wp-accordion-arrow">▾</span>
                </summary>
                <div class="wp-accordion-body">
                    <div class="wp-field-group">
                        <div class="wp-field-label-row">
                            <label for="slider-block-font-size">Ukuran Font Judul Blok</label>
                            <span class="wp-pixel-badge" id="display-block-font-size">32px</span>
                        </div>
                        <div class="wp-slider-row">
                            <input type="range" id="slider-block-font-size" class="wp-range-slider wp-scoped-slider" 
                                   min="14" max="72" value="32" 
                                   data-block-style="font_size" data-unit="px">
                        </div>
                    </div>

                    <div class="wp-field-group">
                        <label>Perataan Teks</label>
                        <div class="wp-btn-group-toggle" role="group" aria-label="Perataan Teks">
                            <button type="button" class="wp-btn-toggle wp-scoped-align-btn is-active" data-align="left" title="Rata Kiri">Left</button>
                            <button type="button" class="wp-btn-toggle wp-scoped-align-btn" data-align="center" title="Rata Tengah">Center</button>
                            <button type="button" class="wp-btn-toggle wp-scoped-align-btn" data-align="right" title="Rata Kanan">Right</button>
                        </div>
                    </div>
                </div>
            </details>

            {{-- 10. ACCORDION KONTEN KEBIJAKAN PRIVASI --}}

            {{-- SCOPED APPEARANCE & COLORS --}}
            <details class="wp-accordion">
                <summary class="wp-accordion-summary">
                    <span class="wp-accordion-heading">Batas &amp; Sudut Melengkung (Radius)</span>
                    <span class="wp-accordion-arrow">▾</span>
                </summary>
                <div class="wp-accordion-body">
                    <div class="wp-color-row">
                        <label for="color-block-bg">Background Override</label>
                        <div class="wp-color-picker-wrap">
                            <input type="color" id="color-block-bg" class="wp-scoped-color" value="#ffffff" data-block-style="bg_color">
                            <span class="wp-color-hex" id="hex-block-bg">Inherit</span>
                            <button type="button" class="wp-btn-link-reset" id="btn-reset-block-bg" title="Kembalikan ke warna bawaan tema">✕</button>
                        </div>
                    </div>

                    <div class="wp-color-row">
                        <label for="color-block-text">Warna Teks Override</label>
                        <div class="wp-color-picker-wrap">
                            <input type="color" id="color-block-text" class="wp-scoped-color" value="#0f172a" data-block-style="text_color">
                            <span class="wp-color-hex" id="hex-block-text">Inherit</span>
                            <button type="button" class="wp-btn-link-reset" id="btn-reset-block-text" title="Kembalikan ke warna teks tema">✕</button>
                        </div>
                    </div>

                    <div class="wp-field-group" style="margin-top: 12px;">
                        <div class="wp-field-label-row">
                            <label for="slider-block-radius">Radius Sudut Kartu</label>
                            <span class="wp-pixel-badge" id="display-block-radius">12px</span>
                        </div>
                        <div class="wp-slider-row">
                            <input type="range" id="slider-block-radius" class="wp-range-slider wp-scoped-slider" 
                                   min="0" max="32" value="12" 
                                   data-block-style="card_radius" data-unit="px">
                        </div>
                    </div>
                </div>
            </details>

            {{-- 10. ACCORDION KONTEN KEBIJAKAN PRIVASI --}}
        </div>

        {{-- SUBPANE 3: ADVANCED (ID, ANCHOR, CUSTOM CLASS, VISIBILITY, LOCK, RESET) --}}
        <div id="wp-subpane-advanced" class="wp-subpane" style="display: none;">
            {{-- Block ID Badge --}}
            <div class="wp-field-group wp-advanced-item">
                <label>Block Instance ID</label>
                <div class="wp-badge-copy-wrap">
                    <span id="display-block-id" class="wp-block-id-badge">block-hero-1</span>
                    <button type="button" class="wp-btn wp-btn-secondary wp-btn-xs" id="btn-copy-block-id" title="Salin ID Blok ke Clipboard">
                        Salin
                    </button>
                </div>
                <small class="wp-field-help">ID unik blok ini dalam struktur Page Document.</small>
            </div>

            {{-- HTML Anchor --}}
            <div class="wp-field-group wp-advanced-item">
                <label for="input-block-html-anchor">HTML Anchor (ID Elemen)</label>
                <input type="text" id="input-block-html-anchor" class="wp-text-input" placeholder="contoh: beranda" pattern="^[a-zA-Z0-9_\-]*$">
                <small class="wp-field-help">Memungkinkan navigasi langsung melalui tautan jangkar (contoh: <code>#beranda</code>).</small>
            </div>

            {{-- Custom CSS Class --}}
            <div class="wp-field-group wp-advanced-item">
                <label for="input-block-css-class">Custom CSS Class</label>
                <input type="text" id="input-block-css-class" class="wp-text-input" placeholder="contoh: is-style-featured mb-8" pattern="^[a-zA-Z0-9_\-\s]*$">
                <small class="wp-field-help">Class CSS kustom yang telah disanitasi (hanya huruf, angka, tanda strip, underscore, dan spasi).</small>
            </div>

            {{-- Responsive Visibility --}}
            <div class="wp-field-group wp-advanced-item">
                <label>Visibilitas Perangkat</label>
                <div class="wp-checkbox-list">
                    <label class="wp-checkbox-item">
                        <input type="checkbox" id="vis-desktop" class="wp-vis-checkbox" data-vis="desktop" checked>
                        <span>Tampilkan di Layar Desktop (≥ 1024px)</span>
                    </label>
                    <label class="wp-checkbox-item">
                        <input type="checkbox" id="vis-tablet" class="wp-vis-checkbox" data-vis="tablet" checked>
                        <span>Tampilkan di Layar Tablet (768px – 1023px)</span>
                    </label>
                    <label class="wp-checkbox-item">
                        <input type="checkbox" id="vis-mobile" class="wp-vis-checkbox" data-vis="mobile" checked>
                        <span>Tampilkan di Layar Ponsel (&lt; 768px)</span>
                    </label>
                </div>
            </div>

            {{-- Lock Status & Action --}}
            <div class="wp-field-group wp-advanced-item">
                <label>Status Proteksi Blok</label>
                <div class="wp-lock-status-row">
                    <span id="display-block-lock-badge" class="wp-listview-badge wp-badge-unlocked">🔓 Dapat Dimodifikasi</span>
                    <button type="button" class="wp-btn wp-btn-secondary wp-btn-xs" id="btn-toggle-block-lock">
                        Kunci Blok
                    </button>
                </div>
                <small class="wp-field-help">Blok yang terkunci tidak dapat dihapus atau digeser urutannya.</small>
            </div>

            {{-- Reset Block Styles --}}
            <div class="wp-field-group wp-advanced-item wp-danger-zone">
                <label>Kembalikan Pengaturan Blok</label>
                <button type="button" class="wp-btn wp-btn-outline-danger wp-btn-sm" id="btn-reset-block-styles">
                    Reset Style Blok Ini ke Bawaan
                </button>
                <small class="wp-field-help">Menghapus seluruh style kustom blok ini dan mengembalikannya ke tema global.</small>
            </div>
        </div>
    </div>

    {{-- KONTEN TAB: PAGE (GLOBAL STYLES & PRESETS & BRAND) --}}
    <div id="wp-tab-content-page" class="wp-tab-pane" role="tabpanel" style="display: none;">
        
        {{-- 1. ACCORDION STYLE PRESETS (ONE-CLICK THEMES) --}}
        <details class="wp-accordion" open>
            <summary class="wp-accordion-summary">
                <span class="wp-accordion-heading">Style Presets (Tema Instan)</span>
                <span class="wp-accordion-arrow">▾</span>
            </summary>
            <div class="wp-accordion-body">
                <p class="wp-field-help" style="margin-bottom: 10px;">Pilih preset desain untuk mengubah nuansa visual seluruh halaman tanpa mengubah konten teks.</p>
                
                {{-- Preset Warna --}}
                <div class="wp-field-group">
                    <label class="wp-preset-subheading">Palet Warna Resmi</label>
                    <div class="wp-preset-grid">
                        <button type="button" class="wp-preset-card" data-preset-type="color" data-preset-key="magkom_official">
                            <div class="wp-preset-swatches">
                                <span style="background: #1B3860;"></span>
                                <span style="background: #F59E0B;"></span>
                                <span style="background: #FFFFFF;"></span>
                            </div>
                            <span class="wp-preset-title">MAGKOM Official</span>
                        </button>
                        <button type="button" class="wp-preset-card" data-preset-type="color" data-preset-key="navy_professional">
                            <div class="wp-preset-swatches">
                                <span style="background: #0F172A;"></span>
                                <span style="background: #3B82F6;"></span>
                                <span style="background: #F8FAFC;"></span>
                            </div>
                            <span class="wp-preset-title">Navy Pro</span>
                        </button>
                        <button type="button" class="wp-preset-card" data-preset-type="color" data-preset-key="blue_modern">
                            <div class="wp-preset-swatches">
                                <span style="background: #1E40AF;"></span>
                                <span style="background: #0284C7;"></span>
                                <span style="background: #F0F9FF;"></span>
                            </div>
                            <span class="wp-preset-title">Blue Modern</span>
                        </button>
                        <button type="button" class="wp-preset-card" data-preset-type="color" data-preset-key="neutral_government">
                            <div class="wp-preset-swatches">
                                <span style="background: #334155;"></span>
                                <span style="background: #64748B;"></span>
                                <span style="background: #F8FAFC;"></span>
                            </div>
                            <span class="wp-preset-title">Netral ASN</span>
                        </button>
                    </div>
                </div>

                {{-- Preset Gaya Tombol --}}
                <div class="wp-field-group" style="margin-top: 14px;">
                    <label class="wp-preset-subheading">Bentuk Tombol (Buttons)</label>
                    <div class="wp-preset-btn-row">
                        <button type="button" class="wp-preset-pill-btn" data-preset-type="button" data-preset-key="rounded">Rounded (8px)</button>
                        <button type="button" class="wp-preset-pill-btn" data-preset-type="button" data-preset-key="pill">Pill (Kapsul)</button>
                        <button type="button" class="wp-preset-pill-btn" data-preset-type="button" data-preset-key="sharp">Sharp (2px)</button>
                    </div>
                </div>

                {{-- Preset Gaya Kartu --}}
                <div class="wp-field-group" style="margin-top: 14px;">
                    <label class="wp-preset-subheading">Gaya Kartu (Cards)</label>
                    <div class="wp-preset-btn-row">
                        <button type="button" class="wp-preset-pill-btn" data-preset-type="card" data-preset-key="standard">Standar</button>
                        <button type="button" class="wp-preset-pill-btn" data-preset-type="card" data-preset-key="soft">Soft &amp; Bulat</button>
                        <button type="button" class="wp-preset-pill-btn" data-preset-type="card" data-preset-key="minimal">Minimalis</button>
                    </div>
                </div>

                {{-- Preset Tipografi --}}
                <div class="wp-field-group" style="margin-top: 14px;">
                    <label class="wp-preset-subheading">Keluarga Tipografi</label>
                    <div class="wp-preset-btn-row">
                        <button type="button" class="wp-preset-pill-btn" data-preset-type="typography" data-preset-key="official">Resmi Kedinasan</button>
                        <button type="button" class="wp-preset-pill-btn" data-preset-type="typography" data-preset-key="modern">Modern Tech</button>
                        <button type="button" class="wp-preset-pill-btn" data-preset-type="typography" data-preset-key="compact">Kompak</button>
                    </div>
                </div>
            </div>
        </details>

        {{-- 2. ACCORDION GLOBAL COLORS (10 TOKENS) --}}
        <details class="wp-accordion">
            <summary class="wp-accordion-summary">
                <span class="wp-accordion-heading">Palet Warna Global (10 Token)</span>
                <span class="wp-accordion-arrow">▾</span>
            </summary>
            <div class="wp-accordion-body">
                <div class="wp-color-row">
                    <label for="global-color-primary">Warna Utama (Primary)</label>
                    <div class="wp-color-picker-wrap">
                        <input type="color" id="global-color-primary" value="{{ $cmsData['landing_theme_primary_color'] }}" data-global-color="primary" data-property="--wp-color-primary">
                        <span class="wp-color-hex" id="hex-global-primary">{{ $cmsData['landing_theme_primary_color'] }}</span>
                    </div>
                </div>
                <div class="wp-color-row">
                    <label for="global-color-primary-hover">Primary Hover</label>
                    <div class="wp-color-picker-wrap">
                        <input type="color" id="global-color-primary-hover" value="{{ $cmsData['landing_theme_navy_dark'] }}" data-global-color="primary_hover" data-property="--wp-color-primary-hover">
                        <span class="wp-color-hex" id="hex-global-primary-hover">{{ $cmsData['landing_theme_navy_dark'] }}</span>
                    </div>
                </div>
                <div class="wp-color-row">
                    <label for="global-color-secondary">Warna Sekunder (Surface Alt)</label>
                    <div class="wp-color-picker-wrap">
                        <input type="color" id="global-color-secondary" value="#F1F5F9" data-global-color="secondary" data-property="--wp-color-secondary">
                        <span class="wp-color-hex" id="hex-global-secondary">#F1F5F9</span>
                    </div>
                </div>
                <div class="wp-color-row">
                    <label for="global-color-accent">Warna Aksen (Tombol &amp; Sorot)</label>
                    <div class="wp-color-picker-wrap">
                        <input type="color" id="global-color-accent" value="{{ $cmsData['landing_theme_accent_color'] }}" data-global-color="accent" data-property="--wp-color-accent">
                        <span class="wp-color-hex" id="hex-global-accent">{{ $cmsData['landing_theme_accent_color'] }}</span>
                    </div>
                </div>
                <div class="wp-color-row">
                    <label for="global-color-background">Latar Belakang Halaman</label>
                    <div class="wp-color-picker-wrap">
                        <input type="color" id="global-color-background" value="{{ $cmsData['landing_theme_bg_color'] }}" data-global-color="background" data-property="--wp-color-background">
                        <span class="wp-color-hex" id="hex-global-background">{{ $cmsData['landing_theme_bg_color'] }}</span>
                    </div>
                </div>
                <div class="wp-color-row">
                    <label for="global-color-card">Latar Belakang Kartu / Box</label>
                    <div class="wp-color-picker-wrap">
                        <input type="color" id="global-color-card" value="#FFFFFF" data-global-color="card" data-property="--wp-color-card">
                        <span class="wp-color-hex" id="hex-global-card">#FFFFFF</span>
                    </div>
                </div>
                <div class="wp-color-row">
                    <label for="global-color-text">Warna Teks Utama</label>
                    <div class="wp-color-picker-wrap">
                        <input type="color" id="global-color-text" value="#0F172A" data-global-color="text" data-property="--wp-color-text">
                        <span class="wp-color-hex" id="hex-global-text">#0F172A</span>
                    </div>
                </div>
                <div class="wp-color-row">
                    <label for="global-color-text-muted">Warna Teks Redup (Muted)</label>
                    <div class="wp-color-picker-wrap">
                        <input type="color" id="global-color-text-muted" value="#64748B" data-global-color="text_muted" data-property="--wp-color-text-muted">
                        <span class="wp-color-hex" id="hex-global-text-muted">#64748B</span>
                    </div>
                </div>
                <div class="wp-color-row">
                    <label for="global-color-border">Warna Garis Tepi (Border)</label>
                    <div class="wp-color-picker-wrap">
                        <input type="color" id="global-color-border" value="#E2E8F0" data-global-color="border" data-property="--wp-color-border">
                        <span class="wp-color-hex" id="hex-global-border">#E2E8F0</span>
                    </div>
                </div>
                <div class="wp-color-row">
                    <label for="global-color-success">Warna Sukses / Badge</label>
                    <div class="wp-color-picker-wrap">
                        <input type="color" id="global-color-success" value="{{ $cmsData['landing_theme_gold_color'] }}" data-global-color="success" data-property="--wp-color-success">
                        <span class="wp-color-hex" id="hex-global-success">{{ $cmsData['landing_theme_gold_color'] }}</span>
                    </div>
                </div>
            </div>
        </details>

        {{-- 3. ACCORDION GLOBAL TYPOGRAPHY --}}
        <details class="wp-accordion">
            <summary class="wp-accordion-summary">
                <span class="wp-accordion-heading">Tipografi Keseluruhan</span>
                <span class="wp-accordion-arrow">▾</span>
            </summary>
            <div class="wp-accordion-body">
                <div class="wp-field-group">
                    <label for="global-font-heading">Font Judul / Display</label>
                    <select id="global-font-heading" class="wp-select-input" data-global-typography="heading_font" data-property="--wp-font-display">
                        @foreach(['Lexend', 'Plus Jakarta Sans', 'Inter', 'Montserrat', 'Poppins'] as $f)
                            <option value="{{ $f }}" {{ $cmsData['landing_theme_font_display'] === $f ? 'selected' : '' }}>{{ $f }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="wp-field-group">
                    <label for="global-font-body">Font Teks Konten (Body)</label>
                    <select id="global-font-body" class="wp-select-input" data-global-typography="body_font" data-property="--wp-font-sans">
                        @foreach(['Source Sans 3', 'Inter', 'Plus Jakarta Sans', 'Roboto', 'Open Sans'] as $bf)
                            <option value="{{ $bf }}" {{ $cmsData['landing_theme_font_body'] === $bf ? 'selected' : '' }}>{{ $bf }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="wp-field-group">
                    <div class="wp-field-label-row">
                        <label for="slider-global-base-size">Ukuran Dasar Teks (Base Font Size)</label>
                        <span class="wp-pixel-badge" id="display-global-base-size">16px</span>
                    </div>
                    <div class="wp-slider-row">
                        <input type="range" id="slider-global-base-size" class="wp-range-slider" 
                               min="14" max="20" value="16" 
                               data-global-typography="base_size_px" data-property="--wp-base-font-size" data-unit="px">
                    </div>
                </div>

                <div class="wp-field-group">
                    <div class="wp-field-label-row">
                        <label for="slider-global-line-height">Kerapatan Baris (Line Height)</label>
                        <span class="wp-pixel-badge" id="display-global-line-height">1.5</span>
                    </div>
                    <div class="wp-slider-row">
                        <input type="range" id="slider-global-line-height" class="wp-range-slider" 
                               min="1.2" max="2.0" step="0.05" value="1.5" 
                               data-global-typography="line_height" data-property="--wp-line-height" data-unit="">
                    </div>
                </div>
            </div>
        </details>

        {{-- 4. ACCORDION GLOBAL LAYOUT --}}
        <details class="wp-accordion">
            <summary class="wp-accordion-summary">
                <span class="wp-accordion-heading">Tata Letak Kontainer &amp; Kartu</span>
                <span class="wp-accordion-arrow">▾</span>
            </summary>
            <div class="wp-accordion-body">
                <div class="wp-field-group">
                    <div class="wp-field-label-row">
                        <label for="slider-global-container-max-w">Lebar Maksimum Kontainer</label>
                        <span class="wp-pixel-badge" id="display-global-container-max-w">{{ $cmsData['landing_container_max_width_px'] }}px</span>
                    </div>
                    <div class="wp-slider-row">
                        <input type="range" id="slider-global-container-max-w" class="wp-range-slider" 
                               min="960" max="1600" step="20" value="{{ $cmsData['landing_container_max_width_px'] }}" 
                               data-global-layout="container_max_width_px" data-property="--wp-container-max-w" data-unit="px">
                    </div>
                </div>

                <div class="wp-field-group">
                    <div class="wp-field-label-row">
                        <label for="slider-global-section-gap">Jarak Antar Bagian (Section Gap)</label>
                        <span class="wp-pixel-badge" id="display-global-section-gap">{{ $cmsData['landing_section_gap_px'] }}px</span>
                    </div>
                    <div class="wp-slider-row">
                        <input type="range" id="slider-global-section-gap" class="wp-range-slider" 
                               min="24" max="140" value="{{ $cmsData['landing_section_gap_px'] }}" 
                               data-global-layout="section_gap_px" data-property="--wp-section-gap-y" data-unit="px">
                    </div>
                </div>

                <div class="wp-field-group">
                    <div class="wp-field-label-row">
                        <label for="slider-global-card-radius">Radius Sudut Kartu Bawaan</label>
                        <span class="wp-pixel-badge" id="display-global-card-radius">{{ $cmsData['landing_card_radius_px'] }}px</span>
                    </div>
                    <div class="wp-slider-row">
                        <input type="range" id="slider-global-card-radius" class="wp-range-slider" 
                               min="0" max="32" value="{{ $cmsData['landing_card_radius_px'] }}" 
                               data-global-layout="card_radius_px" data-property="--wp-card-border-radius" data-unit="px">
                    </div>
                </div>

                <div class="wp-field-group">
                    <label for="select-global-card-shadow">Intensitas Bayangan Kartu (Shadow)</label>
                    <select id="select-global-card-shadow" class="wp-select-input" data-global-layout="card_shadow" data-property="--wp-card-shadow">
                        <option value="none">Tanpa Bayangan (Flat Border)</option>
                        <option value="soft" selected>Lembut (Soft - 0 4px 12px rgba(0,0,0,0.06))</option>
                        <option value="medium">Sedang (Medium - 0 8px 24px rgba(0,0,0,0.09))</option>
                        <option value="strong">Tegas (Elevated - 0 16px 32px rgba(0,0,0,0.12))</option>
                    </select>
                </div>
            </div>
        </details>

        {{-- 5. ACCORDION GLOBAL BUTTONS --}}
        <details class="wp-accordion">
            <summary class="wp-accordion-summary">
                <span class="wp-accordion-heading">Gaya Tombol Global</span>
                <span class="wp-accordion-arrow">▾</span>
            </summary>
            <div class="wp-accordion-body">
                <div class="wp-field-group">
                    <div class="wp-field-label-row">
                        <label for="slider-global-btn-radius">Radius Sudut Tombol</label>
                        <span class="wp-pixel-badge" id="display-global-btn-radius">8px</span>
                    </div>
                    <div class="wp-slider-row">
                        <input type="range" id="slider-global-btn-radius" class="wp-range-slider" 
                               min="0" max="32" value="8" 
                               data-global-button="radius_px" data-property="--wp-radius-button" data-unit="px">
                    </div>
                </div>

                <div class="wp-field-group">
                    <div class="wp-field-label-row">
                        <label for="slider-global-btn-padding-y">Padding Vertikal Tombol</label>
                        <span class="wp-pixel-badge" id="display-global-btn-padding-y">10px</span>
                    </div>
                    <div class="wp-slider-row">
                        <input type="range" id="slider-global-btn-padding-y" class="wp-range-slider" 
                               min="6" max="20" value="10" 
                               data-global-button="padding_y_px" data-property="--wp-btn-padding-y" data-unit="px">
                    </div>
                </div>

                <div class="wp-field-group">
                    <div class="wp-field-label-row">
                        <label for="slider-global-btn-padding-x">Padding Horisontal Tombol</label>
                        <span class="wp-pixel-badge" id="display-global-btn-padding-x">20px</span>
                    </div>
                    <div class="wp-slider-row">
                        <input type="range" id="slider-global-btn-padding-x" class="wp-range-slider" 
                               min="12" max="40" value="20" 
                               data-global-button="padding_x_px" data-property="--wp-btn-padding-x" data-unit="px">
                    </div>
                </div>

                <div class="wp-color-row">
                    <label for="global-color-btn-primary-bg">Tombol Primer (Bg)</label>
                    <div class="wp-color-picker-wrap">
                        <input type="color" id="global-color-btn-primary-bg" value="{{ $cmsData['landing_theme_primary_color'] }}" data-global-button="primary_bg" data-property="--wp-btn-primary-bg">
                        <span class="wp-color-hex" id="hex-global-btn-primary-bg">{{ $cmsData['landing_theme_primary_color'] }}</span>
                    </div>
                </div>

                <div class="wp-color-row">
                    <label for="global-color-btn-primary-text">Tombol Primer (Teks)</label>
                    <div class="wp-color-picker-wrap">
                        <input type="color" id="global-color-btn-primary-text" value="#FFFFFF" data-global-button="primary_text" data-property="--wp-btn-primary-text">
                        <span class="wp-color-hex" id="hex-global-btn-primary-text">#FFFFFF</span>
                    </div>
                </div>
            </div>
        </details>

        {{-- 6. ACCORDION BRAND & IDENTITAS KEDINASAN --}}
        <details class="wp-accordion">
            <summary class="wp-accordion-summary">
                <span class="wp-accordion-heading">Identitas Brand &amp; Footer</span>
                <span class="wp-accordion-arrow">▾</span>
            </summary>
            <div class="wp-accordion-body">
                <div class="wp-field-group">
                    <label for="input-brand-name">Nama Brand Navbar</label>
                    <input type="text" id="input-brand-name" class="wp-text-input" value="{{ $cmsData['landing_header_brand_name'] }}" data-field="landing_header_brand_name">
                </div>

                <div class="wp-field-group">
                    <label for="input-brand-sub">Subjudul Brand</label>
                    <input type="text" id="input-brand-sub" class="wp-text-input" value="{{ $cmsData['landing_header_brand_sub'] }}" data-field="landing_header_brand_sub">
                </div>

                <div class="wp-field-group">
                    <label for="input-footer-alamat">Alamat Kantor Dinas</label>
                    <textarea id="input-footer-alamat" class="wp-textarea-input" rows="3" data-field="landing_footer_alamat">{{ $cmsData['landing_footer_alamat'] }}</textarea>
                </div>

                <div class="wp-field-group">
                    <label for="input-footer-email">Email Helpdesk</label>
                    <input type="email" id="input-footer-email" class="wp-text-input" value="{{ $cmsData['landing_footer_email'] }}" data-field="landing_footer_email">
                </div>

                <div class="wp-field-group">
                    <label for="input-footer-copyright">Teks Hak Cipta (Copyright)</label>
                    <input type="text" id="input-footer-copyright" class="wp-text-input" value="{{ $cmsData['landing_footer_copyright'] }}" data-field="landing_footer_copyright">
                </div>
            </div>
        </details>

        {{-- 7. ACCORDION DOKUMEN PANDUAN PDF & KONTEN PUBLIK --}}
        <details class="wp-accordion">
            <summary class="wp-accordion-summary">
                <span class="wp-accordion-heading">Buku Panduan PDF &amp; Konten Publik</span>
                <span class="wp-accordion-arrow">▾</span>
            </summary>
            <div class="wp-accordion-body">
                <div class="wp-field-group">
                    <label>Dokumen Panduan Pendaftaran Magang (PDF)</label>
                    <div class="wp-pdf-status-card" style="background: #F1F5F9; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <span style="font-size: 1.25rem;">📄</span>
                            <div style="overflow: hidden;">
                                <div id="wp-page-pdf-name" style="font-weight: 600; font-size: 0.8125rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #0F172A;">
                                    {{ $cmsData['landing_panduan_pdf_name'] ?? 'Panduan-Resmi-Magang.pdf' }}
                                </div>
                                <div id="wp-page-pdf-status" style="font-size: 0.6875rem; color: #64748B;">
                                    {{ $cmsData['landing_panduan_pdf'] ? 'Berkas Kustom Diunggah' : 'Generator PDF Bawaan Sistem' }}
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                            <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" id="btn-page-choose-pdf">
                                Pilih dari Media Library
                            </button>
                            <a href="{{ route('panduan.pdf') }}" target="_blank" class="wp-btn wp-btn-ghost wp-btn-sm" title="Buka Dokumen PDF">
                                Unduh / Lihat
                            </a>
                            <button type="button" class="wp-btn wp-btn-danger wp-btn-sm" id="btn-page-reset-pdf" title="Kembalikan ke generator PDF resmi bawaan sistem" {{ empty($cmsData['landing_panduan_pdf']) ? 'style=display:none;' : '' }}>
                                Reset Bawaan
                            </button>
                        </div>
                    </div>
                </div>

                <div class="wp-field-group">
                    <label for="input-page-btn-pdf">Teks Tombol Unduh PDF di Landing</label>
                    <input type="text" id="input-page-btn-pdf" class="wp-text-input" value="{{ $cmsData['landing_tahapan_btn_pdf'] }}" data-field="landing_tahapan_btn_pdf">
                </div>

                <div class="wp-field-group">
                    <label for="input-page-btn-peta">Teks Tombol Peta Alur di Landing</label>
                    <input type="text" id="input-page-btn-peta" class="wp-text-input" value="{{ $cmsData['landing_tahapan_btn_peta'] }}" data-field="landing_tahapan_btn_peta">
                </div>
            </div>
        </details>
    </div>
</aside>
