<aside id="wp-site-inspector" class="wp-site-inspector" aria-label="Panel Pengaturan Canvas Inspector">
    {{-- HEADER INSPECTOR TUNGGAL TERPUSAT (SUPER MINIMALIS) --}}
    <div class="wp-inspector-top-bar">
        <div class="wp-inspector-header-title">
            <span class="wp-inspector-heading">Pengaturan Blok</span>
        </div>
        <button type="button" id="btn-close-inspector" class="wp-icon-btn wp-inspector-close-btn" title="Tutup Inspector (✕)" aria-label="Tutup Inspector">
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

                    {{-- Persyaratan Umum (Kolom 2 Hero) --}}
                    <div class="wp-field-group">
                        <label for="input-hero-label-persyaratan">Judul Kolom Persyaratan</label>
                        <input type="text" id="input-hero-label-persyaratan" class="wp-text-input" value="{{ $cmsData['landing_label_persyaratan'] }}" data-field="landing_label_persyaratan" placeholder="Persyaratan Umum">
                    </div>

                    <div class="wp-field-group">
                        <label for="input-hero-persyaratan-umum">Daftar Persyaratan Umum</label>
                        <textarea id="input-hero-persyaratan-umum" class="wp-textarea-input" rows="4" data-field="landing_persyaratan_umum" placeholder="Satu baris per persyaratan...">{{ $cmsData['landing_persyaratan_umum'] }}</textarea>
                        <span class="wp-field-help">Pisahkan tiap poin persyaratan dengan baris baru (Enter).</span>
                    </div>

                    {{-- Dokumen Pendaftaran (Kolom 3 Hero) --}}
                    <div class="wp-field-group">
                        <label for="input-hero-label-dokumen">Judul Kolom Dokumen</label>
                        <input type="text" id="input-hero-label-dokumen" class="wp-text-input" value="{{ $cmsData['landing_label_dokumen'] }}" data-field="landing_label_dokumen" placeholder="Dokumen Pendaftaran">
                    </div>

                    <div class="wp-field-group">
                        <label for="input-hero-dokumen-pendaftaran">Daftar Dokumen Pendaftaran</label>
                        <textarea id="input-hero-dokumen-pendaftaran" class="wp-textarea-input" rows="4" data-field="landing_dokumen_pendaftaran" placeholder="Satu baris per dokumen...">{{ $cmsData['landing_dokumen_pendaftaran'] }}</textarea>
                        <span class="wp-field-help">Pisahkan tiap nama dokumen dengan baris baru (Enter).</span>
                    </div>

                    {{-- Ketentuan Ukuran Banner Resmi --}}
                    <div class="wp-banner-guidelines-box">
                        <div class="wp-banner-guidelines-header">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <span>Ketentuan &amp; Rekomendasi Banner</span>
                        </div>
                        <ul class="wp-banner-guidelines-list">
                            <li><strong>Resolusi:</strong> Rekomendasi <strong>1920 × 600 px</strong> s/d <strong>1920 × 720 px</strong> (Landscape rasio ~16:5 / 16:6 atau min. 1200 × 500 px).</li>
                            <li><strong>Format File:</strong> JPG, PNG, atau WebP (Maksimal 2 MB).</li>
                            <li><strong>Safe Zone (Area Aman):</strong> Tempatkan objek dan teks penting di bagian tengah agar tidak terpotong saat layar mengecil (tablet / ponsel).</li>
                        </ul>
                    </div>

                    {{-- Multi-Slide Hero Banner (Dinamis: SuperAdmin dapat menambah slide semau dia) --}}
                    <div class="wp-field-group">
                        <div class="wp-slides-repeater-header">
                            <span class="wp-slides-repeater-title">Daftar Slide Banner (Dokumentasi &amp; Info)</span>
                            <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" id="btn-add-hero-slide" title="Tambah Slide Banner Baru">
                                + Tambah Slide
                            </button>
                        </div>
                        <div id="wp-hero-slides-list" class="wp-hero-slides-list">
                            {{-- Di-render secara dinamis oleh inspector.js --}}
                        </div>
                        <input type="hidden" id="input-hero-image" data-field="landing_hero_image" value="{{ $cmsData['landing_hero_image'] }}">
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
                        <input type="text" id="input-faq-heading" class="wp-text-input" value="{{ $cmsData['landing_faq_heading'] }}" data-field="landing_faq_heading" placeholder="Judul Bagian FAQ">
                    </div>

                    {{-- Pemilih Kategori FAQ --}}
                    <div class="wp-field-group">
                        <label for="select-faq-category">Pilih Kategori FAQ</label>
                        <div class="wp-faq-category-header-row">
                            <select id="select-faq-category" class="wp-select-input" aria-label="Pilih Kategori FAQ">
                                {{-- Diisi secara dinamis oleh inspector.js --}}
                            </select>
                            <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" id="btn-add-faq-category" title="Tambah Kategori Baru">
                                + Kategori
                            </button>
                            <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm text-danger" id="btn-delete-faq-category" title="Hapus Kategori Ini" aria-label="Hapus Kategori">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    </div>

                    <div class="wp-field-group">
                        <label for="input-faq-category-name">Nama Kategori Terpilih</label>
                        <input type="text" id="input-faq-category-name" class="wp-text-input" placeholder="Nama Kategori">
                    </div>

                    {{-- Repeater Butir Pertanyaan FAQ --}}
                    <div class="wp-field-group">
                        <div class="wp-faq-repeater-header">
                            <span class="wp-faq-repeater-title" id="wp-faq-repeater-count">Daftar Pertanyaan</span>
                            <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" id="btn-add-faq-item" title="Tambah Pertanyaan Baru">
                                + Tambah Pertanyaan
                            </button>
                        </div>
                        <div id="wp-faq-items-list" class="wp-faq-items-list">
                            {{-- Di-render secara dinamis oleh inspector.js --}}
                        </div>
                    </div>
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
                        <label for="input-spacer-height">Tinggi Ruang Pemisah (px)</label>
                        <input type="number" id="input-spacer-height" class="wp-text-input" 
                               min="16" max="200" step="4" value="48" 
                               data-block-style="height">
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

        {{-- SUBPANE 2: STYLE (SCOPED BLOCK STYLING) --}}
        <div id="wp-subpane-style" class="wp-subpane" style="display: none;">
            {{-- SCOPED TYPOGRAPHY & ALIGNMENT --}}
            <details class="wp-accordion" open>
                <summary class="wp-accordion-summary">
                    <span class="wp-accordion-heading">Tipografi &amp; Perataan Teks</span>
                    <span class="wp-accordion-arrow">▾</span>
                </summary>
                <div class="wp-accordion-body">
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

            {{-- SCOPED APPEARANCE & COLORS --}}
            <details class="wp-accordion" open>
                <summary class="wp-accordion-summary">
                    <span class="wp-accordion-heading">Batas &amp; Warna Blok</span>
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
                </div>
            </details>
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
</aside>
