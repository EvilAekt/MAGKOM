/**
 * WordPress Site Inspector - Logika Kontrol Panel Kanan
 * Mengelola Dynamic Block Forms, Scoped Responsive Styling, Advanced Settings, Global Styles, dan Presets
 */

export class Inspector {
    constructor(canvasBridge, initialGlobalStyles = {}, presets = {}, onStateDirty, initialFaq = []) {
        this.bridge = canvasBridge;
        this.blockManager = null;
        this.onStateDirty = onStateDirty;

        this.activeBlock = 'hero';
        this.activeBlockId = 'block-hero-1';

        this.state = {};
        this.presets = presets || {};
        this.globalStyles = this.normalizeGlobalStyles(initialGlobalStyles);

        this.mediaLibrary = null;
        this.heroSlides = [];
        this.faqCategoriesData = Array.isArray(initialFaq) && initialFaq.length > 0
            ? JSON.parse(JSON.stringify(initialFaq))
            : [];
        this.activeFaqCategoryIndex = 0;

        this.initSubtabs();
        this.initGlobalStyleControls();
        this.initPresets();
        this.initScopedStyleControls();
        this.initAdvancedSettingsControls();
        this.initContentInputs();
        this.initFaqControls();
        this.initBlockQuickSwitcher();
    }

    normalizeGlobalStyles(initial) {
        const defaults = {
            colors: {
                primary: '#1B3860',
                primary_hover: '#122846',
                secondary: '#F1F5F9',
                accent: '#4F46E5',
                background: '#FFFFFF',
                card: '#FFFFFF',
                text: '#0F172A',
                text_muted: '#64748B',
                border: '#E2E8F0',
                success: '#F59E0B',
            },
            typography: {
                heading_font: 'Lexend',
                body_font: 'Source Sans 3',
                base_size_px: 16,
                line_height: 1.5,
            },
            layout: {
                container_max_width_px: 1280,
                section_gap_px: 64,
                card_radius_px: 12,
                card_shadow: 'soft',
            },
            buttons: {
                radius_px: 8,
                padding_y_px: 10,
                padding_x_px: 20,
                primary_bg: '#1B3860',
                primary_text: '#FFFFFF',
            },
        };

        return {
            colors: { ...defaults.colors, ...(initial?.colors || {}) },
            typography: { ...defaults.typography, ...(initial?.typography || {}) },
            layout: { ...defaults.layout, ...(initial?.layout || {}) },
            buttons: { ...defaults.buttons, ...(initial?.buttons || {}) },
        };
    }

    setBlockManager(blockManager) {
        this.blockManager = blockManager;
        const heroBlock = this.blockManager?.layout?.find(b => b.type === 'hero');
        if (heroBlock && heroBlock.content) {
            if (heroBlock.content.image) {
                this.state['landing_hero_image'] = heroBlock.content.image;
            }
            if (heroBlock.content.label_persyaratan) {
                this.state['landing_label_persyaratan'] = heroBlock.content.label_persyaratan;
            }
            if (heroBlock.content.persyaratan_umum) {
                this.state['landing_persyaratan_umum'] = heroBlock.content.persyaratan_umum;
            }
            if (heroBlock.content.label_dokumen) {
                this.state['landing_label_dokumen'] = heroBlock.content.label_dokumen;
            }
            if (heroBlock.content.dokumen_pendaftaran) {
                this.state['landing_dokumen_pendaftaran'] = heroBlock.content.dokumen_pendaftaran;
            }

            if (Array.isArray(heroBlock.content.slides) && heroBlock.content.slides.length > 0) {
                this.heroSlides = JSON.parse(JSON.stringify(heroBlock.content.slides));
            } else {
                const initialImg = heroBlock.content.image || this.state['landing_hero_image'] || '';
                this.heroSlides = [
                    {
                        id: 'slide-1',
                        type: 'photo_text',
                        image: initialImg,
                        badge: 'DOKUMENTASI RESMI',
                        title: 'Magang Diskominfo Sleman',
                        description: 'Pengembangan ekosistem digital SPBE, infrastruktur jaringan, dan talenta muda bersama Pemerintah Kabupaten Sleman.',
                        cta_text: 'Daftar Sekarang',
                        cta_url: '#kuota',
                    }
                ];
            }
            this.heroSlides.forEach(slide => {
                if (!slide.type) {
                    if (slide.image && (!slide.title || slide.title.trim() === '') && (!slide.description || slide.description.trim() === '')) {
                        slide.type = 'banner';
                    } else if (!slide.image && (slide.title || slide.description)) {
                        slide.type = 'announcement';
                    } else {
                        slide.type = 'photo_text';
                    }
                }
            });
            this.state['landing_hero_slides'] = this.heroSlides;
            this.renderHeroSlidesList();
        }

        const faqBlock = this.blockManager?.layout?.find(b => b.type === 'faq');
        if (faqBlock && faqBlock.content && Array.isArray(faqBlock.content.items) && faqBlock.content.items.length > 0 && this.faqCategoriesData.length === 0) {
            this.faqCategoriesData = JSON.parse(JSON.stringify(faqBlock.content.items));
        }
        if (this.faqCategoriesData.length === 0) {
            this.faqCategoriesData = this.getDefaultFaqData();
        }
        this.state['landing_faq_data'] = this.faqCategoriesData;
        this.renderFaqCategoriesSelect();
        this.renderFaqItemsList();

        const tahapanBlock = this.blockManager?.layout?.find(b => b.type === 'tahapan');
        if (tahapanBlock && tahapanBlock.content) {
            if (tahapanBlock.content.heading) {
                this.state['landing_tahapan_heading'] = tahapanBlock.content.heading;
                const hInput = document.getElementById('input-tahapan-heading');
                if (hInput && !hInput.value) hInput.value = tahapanBlock.content.heading;
            }
            if (tahapanBlock.content.summary) {
                this.state['landing_tahapan_summary'] = tahapanBlock.content.summary;
                const sInput = document.getElementById('input-tahapan-summary');
                if (sInput && !sInput.value) sInput.value = tahapanBlock.content.summary;
            }
            if (Array.isArray(tahapanBlock.content.steps) && tahapanBlock.content.steps.length > 0) {
                this.state['landing_tahapan_steps'] = tahapanBlock.content.steps;
                tahapanBlock.content.steps.forEach((step, idx) => {
                    const titleEl = document.getElementById(`input-step-title-${idx}`);
                    const descEl = document.getElementById(`input-step-desc-${idx}`);
                    if (titleEl && !titleEl.value && step.title !== undefined) titleEl.value = step.title;
                    if (descEl && !descEl.value && step.desc !== undefined) descEl.value = step.desc;
                });
            }
        }
        this.syncTahapanSteps(false);

        const thumbEl = document.getElementById('wp-hero-banner-thumb');
        if (thumbEl) {
            thumbEl.onerror = () => {
                thumbEl.src = '/images/gedung-kominfo.jpg';
            };
        }
    }

    initHeroSlidesControls() {
        const addBtn = document.getElementById('btn-add-hero-slide');
        if (!addBtn) return;

        if (addBtn.__hasHeroSlideListener) return;
        addBtn.__hasHeroSlideListener = true;

        addBtn.addEventListener('click', (e) => {
            e.preventDefault();
            this.addHeroSlide();
        });
    }

    setMediaLibrary(mediaLibrary) {
        this.mediaLibrary = mediaLibrary;
        this.initHeroSlidesControls();

        document.getElementById('btn-change-hero-image')?.addEventListener('click', () => {
            if (this.mediaLibrary) {
                this.mediaLibrary.open('image', (selectedMedia) => {
                    this.setHeroImageFromMedia(selectedMedia);
                });
            }
        });

        const handlePdfPick = () => {
            if (this.mediaLibrary) {
                this.mediaLibrary.open('pdf', (selectedPdf) => {
                    this.setPanduanPdfFromMedia(selectedPdf);
                });
            }
        };

        document.getElementById('btn-change-panduan-pdf')?.addEventListener('click', handlePdfPick);
        document.getElementById('btn-choose-pdf-media')?.addEventListener('click', handlePdfPick);

        const handlePdfReset = async () => {
            if (confirm('Apakah Anda yakin ingin mereset berkas panduan PDF kembali ke generator otomatis bawaan sistem?')) {
                await this.resetPanduanPdf();
            }
        };

        document.getElementById('btn-reset-panduan-pdf')?.addEventListener('click', handlePdfReset);
    }

    initSubtabs() {
        const subtabBtns = document.querySelectorAll('.wp-subtab-btn');
        subtabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                subtabBtns.forEach(b => b.classList.remove('is-active'));
                btn.classList.add('is-active');

                const target = btn.getAttribute('data-subtab');
                document.querySelectorAll('.wp-subpane').forEach(pane => pane.style.display = 'none');

                const targetPane = document.getElementById(`wp-subpane-${target}`);
                if (targetPane) {
                    targetPane.style.display = 'block';
                }
            });
        });
    }

    // =========================================================================
    // TASK 3 - TASK A: DYNAMIC BLOCK INSPECTOR (CONTENT, STYLE, ADVANCED)
    // =========================================================================

    /**
     * Helper resolver ikon SVG stroke profesional (menggantikan emoji)
     */
    getBlockIconSvg(blockType, size = 18) {
        switch (blockType) {
            case 'hero':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>`;
            case 'informasi':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>`;
            case 'kuota':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>`;
            case 'tahapan':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="4.5" r="2.5"/><circle cx="6" cy="19.5" r="2.5"/><circle cx="18" cy="19.5" r="2.5"/><path d="M12 7v5l-4.5 5"/><path d="M12 12l4.5 5"/></svg>`;
            case 'faq':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>`;
            case 'footer':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18"/><path d="M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/><line x1="9" y1="7" x2="9" y2="7.01"/><line x1="15" y1="7" x2="15" y2="7.01"/><line x1="9" y1="11" x2="9" y2="11.01"/><line x1="15" y1="11" x2="15" y2="11.01"/><line x1="9" y1="15" x2="9" y2="15.01"/><line x1="15" y1="15" x2="15" y2="15.01"/></svg>`;
            case 'kebijakan':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>`;
            case 'cta_banner':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>`;
            case 'features':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>`;
            case 'spacer':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="7 8 12 3 17 8"/><polyline points="7 16 12 21 17 16"/><line x1="12" y1="3" x2="12" y2="21"/></svg>`;
            default:
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>`;
        }
    }

    /**
     * Helper resolver ikon SVG tipe slide banner (menggantikan emoji)
     */
    getSlideTypeIconSvg(slideType, size = 13) {
        switch (slideType) {
            case 'banner':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>`;
            case 'photo_text':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="10" y1="10" x2="10" y2="21"/></svg>`;
            case 'announcement':
                return `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>`;
            default:
                return '';
        }
    }

    /**
     * Set aktifkan blok dari kanvas atau list view ke inspector
     */
    setActiveBlock(blockName, blockTitle, blockId = null) {
        this.activeBlock = blockName;
        this.activeBlockId = blockId || `block-${blockName}`;

        const titleEl = document.getElementById('wp-active-block-title');
        const descEl = document.getElementById('wp-active-block-subtitle');
        const iconEl = document.getElementById('wp-active-block-icon');

        if (titleEl) {
            titleEl.textContent = blockTitle || blockName.toUpperCase();
        }

        const blockInfo = {
            hero: { desc: 'Section utama dengan background gambar dan konten utama.' },
            informasi: { desc: 'Persyaratan umum magang dan dokumen administrasi wajib.' },
            kuota: { desc: 'Tabel ketersediaan kuota jurusan dan formasi magang aktif.' },
            tahapan: { desc: 'Pohon 4 checkpoint alur seleksi pendaftaran dan kelulusan.' },
            faq: { desc: 'Daftar tanya jawab akordeon terstruktur per kategori.' },
            footer: { desc: 'Alamat kantor dinas, jam layanan, medsos, dan hak cipta.' },
            cta_banner: { desc: 'Kotak banner promosi ajakan mendaftar magang.' },
            features: { desc: '3 kartu sorot keunggulan program magang kominfo.' },
            spacer: { desc: 'Pemisah jarak kosong vertikal antar section.' },
            kebijakan: { desc: 'Kebijakan privasi dan perlindungan data UU PDP No. 27/2022.' },
        };

        const info = blockInfo[blockName] || { desc: 'Bagian konten landing page.' };
        if (descEl) descEl.textContent = info.desc;
        if (iconEl) iconEl.innerHTML = this.getBlockIconSvg(blockName, 20);

        const switcher = document.getElementById('wp-block-quick-switcher');
        if (switcher && switcher.value !== blockName) {
            switcher.value = blockName;
        }

        const switcherIcon = document.getElementById('wp-block-switcher-icon-prefix');
        if (switcherIcon) {
            switcherIcon.innerHTML = this.getBlockIconSvg(blockName, 14);
        }

        // Tampilkan hanya form akordeon yang sesuai di subpane General
        document.querySelectorAll('#wp-subpane-general .wp-accordion').forEach(acc => {
            const isMatch = acc.classList.contains(`block-form-${blockName}`);
            acc.style.display = isMatch ? 'block' : 'none';
            if (isMatch) acc.open = true;
        });

        // Beralih ke tab Block jika sedang di tab Page
        const blockTabBtn = document.querySelector('.wp-tab-btn[data-tab="block"]');
        if (blockTabBtn && !blockTabBtn.classList.contains('is-active')) {
            blockTabBtn.click();
        }

        // Sinkronisasi data konten blok jika ada di BlockManager
        this.syncContentInputsForActiveBlock();

        // Sinkronisasi scoped styles ke kontrol style
        this.syncScopedControlsForActiveBlock();

        // Sinkronisasi advanced settings
        this.syncAdvancedControlsForActiveBlock();
    }

    getActiveBlockData() {
        if (!this.blockManager) return null;
        return this.blockManager.layout.find(b => b.id === this.activeBlockId || b.type === this.activeBlockId);
    }

    syncContentInputsForActiveBlock() {
        const block = this.getActiveBlockData();
        if (!block) return;

        const content = block.content || {};
        const generalPane = document.getElementById('wp-subpane-general');
        if (!generalPane) return;

        // Cari input khusus block field di dalam accordion blok terkait
        const activeAccordion = generalPane.querySelector(`.block-form-${block.type}`);
        if (!activeAccordion) return;

        activeAccordion.querySelectorAll('[data-block-field]').forEach(input => {
            const fieldKey = input.getAttribute('data-block-field');
            if (content[fieldKey] !== undefined) {
                input.value = content[fieldKey];
            }
        });
    }

    syncScopedControlsForActiveBlock() {
        const block = this.getActiveBlockData();
        const styles = block ? (block.styles || {}) : {};

        // 1. Alignment Buttons
        const alignVal = styles.text_align || 'left';
        document.querySelectorAll('.wp-scoped-align-btn').forEach(btn => {
            btn.classList.toggle('is-active', btn.getAttribute('data-align') === alignVal);
        });

        // 2. Scoped Color Overrides
        const bgVal = styles.bg_color || '';
        const bgPicker = document.getElementById('color-block-bg');
        const bgHex = document.getElementById('hex-block-bg');
        if (bgPicker && bgHex) {
            bgPicker.value = bgVal || '#ffffff';
            bgHex.textContent = bgVal ? bgVal.toUpperCase() : 'Inherit';
        }

        const textVal = styles.text_color || '';
        const textPicker = document.getElementById('color-block-text');
        const textHex = document.getElementById('hex-block-text');
        if (textPicker && textHex) {
            textPicker.value = textVal || '#0f172a';
            textHex.textContent = textVal ? textVal.toUpperCase() : 'Inherit';
        }

        // 3. Spacer Height Input
        const spacerInput = document.getElementById('input-spacer-height');
        if (spacerInput) {
            spacerInput.value = styles.height || block?.content?.height || 48;
        }
    }

    syncAdvancedControlsForActiveBlock() {
        const block = this.getActiveBlockData();
        if (!block) return;

        // ID Badge
        const idBadge = document.getElementById('display-block-id');
        if (idBadge) {
            idBadge.textContent = block.id;
        }

        const settings = block.settings || {};

        // HTML Anchor
        const anchorInput = document.getElementById('input-block-html-anchor');
        if (anchorInput) {
            anchorInput.value = settings.html_anchor || '';
        }

        // Custom CSS Class
        const classInput = document.getElementById('input-block-css-class');
        if (classInput) {
            classInput.value = settings.custom_css_class || '';
        }

        // Lock Badge & Button
        const lockBadge = document.getElementById('display-block-lock-badge');
        const lockBtn = document.getElementById('btn-toggle-block-lock');
        const isSystemLocked = Boolean(block.locked);
        const isUserLocked = Boolean(block.user_locked);

        if (lockBadge) {
            if (isSystemLocked) {
                lockBadge.className = 'wp-listview-badge wp-badge-system-locked';
                lockBadge.textContent = '🔒 Wajib Sistem';
            } else if (isUserLocked) {
                lockBadge.className = 'wp-listview-badge wp-badge-user-locked';
                lockBadge.textContent = '🔒 Terkunci (Admin)';
            } else {
                lockBadge.className = 'wp-listview-badge wp-badge-unlocked';
                lockBadge.textContent = '🔓 Dapat Dimodifikasi';
            }
        }

        if (lockBtn) {
            lockBtn.disabled = isSystemLocked;
            lockBtn.textContent = isUserLocked ? 'Buka Kunci' : 'Kunci Blok';
        }
    }

    // =========================================================================
    // SCOPED STYLES CONTROLS
    // =========================================================================

    initScopedStyleControls() {
        // 1. Alignment Toggles
        document.querySelectorAll('.wp-scoped-align-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.wp-scoped-align-btn').forEach(b => b.classList.remove('is-active'));
                btn.classList.add('is-active');

                const align = btn.getAttribute('data-align');
                this.setScopedBlockStyle('text_align', align);
            });
        });

        // 2. Scoped Colors
        const bgPicker = document.getElementById('color-block-bg');
        bgPicker?.addEventListener('input', (e) => {
            const val = e.target.value.toUpperCase();
            const hexEl = document.getElementById('hex-block-bg');
            if (hexEl) hexEl.textContent = val;
            this.setScopedBlockStyle('bg_color', val);
        });

        document.getElementById('btn-reset-block-bg')?.addEventListener('click', () => {
            const hexEl = document.getElementById('hex-block-bg');
            if (hexEl) hexEl.textContent = 'Inherit';
            this.setScopedBlockStyle('bg_color', null);
        });

        const textPicker = document.getElementById('color-block-text');
        textPicker?.addEventListener('input', (e) => {
            const val = e.target.value.toUpperCase();
            const hexEl = document.getElementById('hex-block-text');
            if (hexEl) hexEl.textContent = val;
            this.setScopedBlockStyle('text_color', val);
        });

        document.getElementById('btn-reset-block-text')?.addEventListener('click', () => {
            const hexEl = document.getElementById('hex-block-text');
            if (hexEl) hexEl.textContent = 'Inherit';
            this.setScopedBlockStyle('text_color', null);
        });

        // 3. Spacer Height Input
        const spacerInput = document.getElementById('input-spacer-height');
        spacerInput?.addEventListener('input', (e) => {
            const val = parseInt(e.target.value, 10);
            if (!isNaN(val)) {
                this.setScopedBlockStyle('height', val);
            }
        });
    }

    setScopedBlockStyle(property, value) {
        const block = this.getActiveBlockData();
        if (!block) return;

        if (!block.styles) {
            block.styles = {};
        }

        if (value === null || value === undefined || value === '') {
            delete block.styles[property];
        } else {
            block.styles[property] = value;
        }

        // Live preview ke iframe canvas via Bridge
        if (this.bridge) {
            this.bridge.updateBlockStyle(block.id, property, value, 'desktop');
        }

        this.notifyDirty();
    }

    // =========================================================================
    // ADVANCED SETTINGS & SECURITY VALIDATIONS
    // =========================================================================

    initAdvancedSettingsControls() {
        // Copy Block ID
        document.getElementById('btn-copy-block-id')?.addEventListener('click', () => {
            if (this.activeBlockId) {
                navigator.clipboard.writeText(this.activeBlockId).then(() => {
                    const btn = document.getElementById('btn-copy-block-id');
                    if (btn) {
                        const origText = btn.textContent;
                        btn.textContent = 'Tersalin!';
                        setTimeout(() => { btn.textContent = origText; }, 1500);
                    }
                }).catch(() => { });
            }
        });

        // HTML Anchor with regex validation ^[a-zA-Z0-9_\-]*$
        const anchorInput = document.getElementById('input-block-html-anchor');
        anchorInput?.addEventListener('input', (e) => {
            const raw = e.target.value;
            const sanitized = raw.replace(/[^a-zA-Z0-9_\-]/g, '');
            if (raw !== sanitized) {
                e.target.value = sanitized;
            }
            this.setBlockSetting('html_anchor', sanitized);
        });

        // Custom CSS Class with regex validation ^[a-zA-Z0-9_\-\s]*$
        const classInput = document.getElementById('input-block-css-class');
        classInput?.addEventListener('input', (e) => {
            const raw = e.target.value;
            const sanitized = raw.replace(/[^a-zA-Z0-9_\-\s]/g, '');
            if (raw !== sanitized) {
                e.target.value = sanitized;
            }
            this.setBlockSetting('custom_css_class', sanitized);
        });

        // Lock Toggle Button in Inspector
        document.getElementById('btn-toggle-block-lock')?.addEventListener('click', () => {
            if (this.blockManager) {
                this.blockManager.toggleLock(this.activeBlockId);
                this.syncAdvancedControlsForActiveBlock();
                this.notifyDirty();
            }
        });

        // Reset Block Styles Button
        document.getElementById('btn-reset-block-styles')?.addEventListener('click', () => {
            if (confirm(`Kembalikan seluruh pengaturan gaya blok ini ke bawaan sistem?`)) {
                this.resetActiveBlockStyles();
            }
        });
    }

    setBlockSetting(key, value) {
        const block = this.getActiveBlockData();
        if (!block) return;

        if (!block.settings) {
            block.settings = {};
        }

        block.settings[key] = value;

        if (this.bridge) {
            this.bridge.setBlockSettings(block.id, block.settings);
        }

        this.notifyDirty();
    }

    resetActiveBlockStyles() {
        const block = this.getActiveBlockData();
        if (!block) return;

        block.styles = {};

        if (this.bridge) {
            this.bridge.resetStyles(block.id);
        }

        this.syncScopedControlsForActiveBlock();
        this.notifyDirty();
    }

    // =========================================================================
    // TASK 3 - TASK B & E: GLOBAL STYLES & PRESETS ENGINE
    // =========================================================================

    initGlobalStyleControls() {
        // 1. Global Colors
        document.querySelectorAll('[data-global-color]').forEach(picker => {
            const key = picker.getAttribute('data-global-color');
            const prop = picker.getAttribute('data-property');
            const hexEl = picker.nextElementSibling;

            // Set initial picker value from this.globalStyles.colors
            if (this.globalStyles.colors[key]) {
                picker.value = this.globalStyles.colors[key];
                if (hexEl) hexEl.textContent = this.globalStyles.colors[key];
            }

            picker.addEventListener('input', (e) => {
                const val = e.target.value.toUpperCase();
                if (hexEl) hexEl.textContent = val;

                this.globalStyles.colors[key] = val;

                if (prop && this.bridge) {
                    this.bridge.updateStyle(prop, val);
                }

                // Sync to legacy keys if applicable
                if (key === 'primary') this.state['landing_theme_primary_color'] = val;
                if (key === 'primary_hover') this.state['landing_theme_navy_dark'] = val;
                if (key === 'accent') this.state['landing_theme_accent_color'] = val;
                if (key === 'background') this.state['landing_theme_bg_color'] = val;
                if (key === 'success') this.state['landing_theme_gold_color'] = val;

                this.notifyDirty();
            });
        });

        // 2. Global Typography
        document.querySelectorAll('[data-global-typography]').forEach(input => {
            const key = input.getAttribute('data-global-typography');
            const prop = input.getAttribute('data-property');
            const unit = input.getAttribute('data-unit') || '';

            input.addEventListener('input', (e) => {
                let val = e.target.value;
                if (key === 'base_size_px') val = parseInt(val, 10);
                if (key === 'line_height') val = parseFloat(val);

                this.globalStyles.typography[key] = val;

                const badge = input.closest('.wp-field-group')?.querySelector('.wp-pixel-badge');
                if (badge) {
                    badge.textContent = `${val}${unit}`;
                }

                if (prop && this.bridge) {
                    const formatted = (key === 'heading_font' || key === 'body_font')
                        ? `'${val}', sans-serif`
                        : `${val}${unit}`;
                    this.bridge.updateStyle(prop, formatted);
                }

                if (key === 'heading_font') this.state['landing_theme_font_display'] = val;
                if (key === 'body_font') this.state['landing_theme_font_body'] = val;

                this.notifyDirty();
            });
        });

        // 3. Global Layout
        document.querySelectorAll('[data-global-layout]').forEach(input => {
            const key = input.getAttribute('data-global-layout');
            const prop = input.getAttribute('data-property');
            const unit = input.getAttribute('data-unit') || '';

            input.addEventListener('input', (e) => {
                let val = e.target.value;
                if (key.endsWith('_px')) val = parseInt(val, 10);

                this.globalStyles.layout[key] = val;

                const badge = input.closest('.wp-field-group')?.querySelector('.wp-pixel-badge');
                if (badge) {
                    badge.textContent = `${val}${unit}`;
                }

                if (prop && this.bridge) {
                    if (key === 'card_shadow') {
                        const shadowMap = {
                            none: 'none',
                            soft: '0 4px 12px rgba(0, 0, 0, 0.06)',
                            medium: '0 8px 24px rgba(0, 0, 0, 0.09)',
                            strong: '0 16px 32px rgba(0, 0, 0, 0.12)',
                        };
                        this.bridge.updateStyle(prop, shadowMap[val] || val);
                    } else {
                        this.bridge.updateStyle(prop, `${val}${unit}`);
                    }
                }

                if (key === 'container_max_width_px') this.state['landing_container_max_width_px'] = val;
                if (key === 'section_gap_px') this.state['landing_section_gap_px'] = val;
                if (key === 'card_radius_px') this.state['landing_card_radius_px'] = val;

                this.notifyDirty();
            });
        });

        // 4. Global Buttons
        document.querySelectorAll('[data-global-button]').forEach(input => {
            const key = input.getAttribute('data-global-button');
            const prop = input.getAttribute('data-property');
            const unit = input.getAttribute('data-unit') || '';

            input.addEventListener('input', (e) => {
                let val = e.target.value;
                if (key.endsWith('_px')) val = parseInt(val, 10);

                this.globalStyles.buttons[key] = val;

                const badge = input.closest('.wp-field-group')?.querySelector('.wp-pixel-badge');
                if (badge) {
                    badge.textContent = `${val}${unit}`;
                }

                const hexEl = input.nextElementSibling;
                if (hexEl && input.type === 'color') {
                    hexEl.textContent = val.toUpperCase();
                }

                if (prop && this.bridge) {
                    this.bridge.updateStyle(prop, `${val}${unit}`);
                }

                this.notifyDirty();
            });
        });
    }

    initPresets() {
        // Color Presets
        document.querySelectorAll('.wp-preset-card[data-preset-type="color"]').forEach(card => {
            card.addEventListener('click', () => {
                const key = card.getAttribute('data-preset-key');
                this.applyColorPreset(key);
            });
        });

        // Pill Presets (Buttons, Cards, Typography)
        document.querySelectorAll('.wp-preset-pill-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const type = btn.getAttribute('data-preset-type');
                const key = btn.getAttribute('data-preset-key');

                const siblings = btn.closest('.wp-preset-btn-row')?.querySelectorAll('.wp-preset-pill-btn');
                siblings?.forEach(s => s.classList.remove('is-active'));
                btn.classList.add('is-active');

                if (type === 'button') this.applyButtonPreset(key);
                if (type === 'card') this.applyCardPreset(key);
                if (type === 'typography') this.applyTypographyPreset(key);
            });
        });
    }

    applyColorPreset(presetKey) {
        const presets = {
            magkom_official: {
                primary: '#1B3860',
                primary_hover: '#122846',
                secondary: '#F1F5F9',
                accent: '#F59E0B',
                background: '#FFFFFF',
                card: '#FFFFFF',
                text: '#0F172A',
                text_muted: '#64748B',
                border: '#E2E8F0',
                success: '#16A34A',
            },
            navy_professional: {
                primary: '#0F172A',
                primary_hover: '#020617',
                secondary: '#F8FAFC',
                accent: '#3B82F6',
                background: '#FFFFFF',
                card: '#F8FAFC',
                text: '#0F172A',
                text_muted: '#475569',
                border: '#CBD5E1',
                success: '#10B981',
            },
            blue_modern: {
                primary: '#1E40AF',
                primary_hover: '#1E3A8A',
                secondary: '#EFF6FF',
                accent: '#0284C7',
                background: '#FFFFFF',
                card: '#FFFFFF',
                text: '#0F172A',
                text_muted: '#64748B',
                border: '#DBEAFE',
                success: '#059669',
            },
            neutral_government: {
                primary: '#334155',
                primary_hover: '#1E293B',
                secondary: '#F1F5F9',
                accent: '#64748B',
                background: '#F8FAFC',
                card: '#FFFFFF',
                text: '#1E293B',
                text_muted: '#64748B',
                border: '#E2E8F0',
                success: '#0D9488',
            },
        };

        const chosen = presets[presetKey];
        if (!chosen) return;

        this.globalStyles.colors = { ...this.globalStyles.colors, ...chosen };

        // Update color pickers UI
        document.querySelectorAll('[data-global-color]').forEach(picker => {
            const k = picker.getAttribute('data-global-color');
            if (chosen[k]) {
                picker.value = chosen[k];
                const hexEl = picker.nextElementSibling;
                if (hexEl) hexEl.textContent = chosen[k];
            }
        });

        // Live preview ke iframe canvas via Bridge
        if (this.bridge) {
            this.bridge.updateGlobalStyles(this.globalStyles);
        }

        // Active state badge
        document.querySelectorAll('.wp-preset-card[data-preset-type="color"]').forEach(c => {
            c.classList.toggle('is-active', c.getAttribute('data-preset-key') === presetKey);
        });

        this.notifyDirty();
    }

    applyButtonPreset(presetKey) {
        const presets = {
            rounded: { radius_px: 8, padding_y_px: 10, padding_x_px: 20 },
            pill: { radius_px: 9999, padding_y_px: 12, padding_x_px: 24 },
            sharp: { radius_px: 2, padding_y_px: 10, padding_x_px: 18 },
        };

        const chosen = presets[presetKey];
        if (!chosen) return;

        this.globalStyles.buttons = { ...this.globalStyles.buttons, ...chosen };

        if (this.bridge) {
            this.bridge.updateGlobalStyles(this.globalStyles);
        }

        this.notifyDirty();
    }

    applyCardPreset(presetKey) {
        const presets = {
            standard: { card_radius_px: 12, card_shadow: 'soft' },
            soft: { card_radius_px: 20, card_shadow: 'medium' },
            minimal: { card_radius_px: 4, card_shadow: 'none' },
        };

        const chosen = presets[presetKey];
        if (!chosen) return;

        this.globalStyles.layout = { ...this.globalStyles.layout, ...chosen };

        const selectShadow = document.getElementById('select-global-card-shadow');
        if (selectShadow) {
            selectShadow.value = chosen.card_shadow;
        }

        if (this.bridge) {
            this.bridge.updateGlobalStyles(this.globalStyles);
        }

        this.notifyDirty();
    }

    applyTypographyPreset(presetKey) {
        const presets = {
            official: { heading_font: 'Lexend', body_font: 'Source Sans 3', base_size_px: 16, line_height: 1.5 },
            modern: { heading_font: 'Plus Jakarta Sans', body_font: 'Inter', base_size_px: 16, line_height: 1.55 },
            compact: { heading_font: 'Inter', body_font: 'Inter', base_size_px: 15, line_height: 1.45 },
        };

        const chosen = presets[presetKey];
        if (!chosen) return;

        this.globalStyles.typography = { ...this.globalStyles.typography, ...chosen };

        const selectHeading = document.getElementById('global-font-heading');
        if (selectHeading) selectHeading.value = chosen.heading_font;

        const selectBody = document.getElementById('global-font-body');
        if (selectBody) selectBody.value = chosen.body_font;

        if (this.bridge) {
            this.bridge.updateGlobalStyles(this.globalStyles);
        }

        this.notifyDirty();
    }

    // =========================================================================
    // GENERAL CONTENT INPUTS & SERIALIZATION
    // =========================================================================

    initContentInputs() {
        // Standard inputs with data-field (legacy CMS key format)
        document.querySelectorAll('[data-field]:not([type="range"]):not([type="color"])').forEach(input => {
            const field = input.getAttribute('data-field');
            if (field) {
                const val = (input.type === 'checkbox') ? (input.checked ? '1' : '0') : input.value;
                this.state[field] = val;
                this.syncFieldToBlockContent(field, val);
            }

            const handleInput = (e) => {
                if (field) {
                    const val = (input.type === 'checkbox') ? (input.checked ? '1' : '0') : e.target.value;
                    this.state[field] = val;
                    this.syncFieldToBlockContent(field, val);
                }
                this.notifyDirty();
            };

            input.addEventListener('input', handleInput);
            input.addEventListener('change', handleInput);
        });

        // Inputs with data-block-field (modern block content)
        document.querySelectorAll('[data-block-field]').forEach(input => {
            const key = input.getAttribute('data-block-field');
            const handleBlockInput = (e) => {
                const block = this.getActiveBlockData();
                if (block) {
                    if (!block.content) block.content = {};
                    block.content[key] = e.target.value;
                }
                this.notifyDirty();
            };
            input.addEventListener('input', handleBlockInput);
            input.addEventListener('change', handleBlockInput);
        });

        this.initTahapanStepInputs();
        this.initKebijakanSectionsInputs();
        this.initHeroSlidesControls();
    }

    syncFieldToBlockContent(field, val) {
        if (!this.blockManager || !this.blockManager.layout) return;

        const fieldMap = {
            // Hero Banner
            landing_hero_title: { blockType: 'hero', contentKey: 'title', editableField: 'hero_title' },
            landing_hero_kicker: { blockType: 'hero', contentKey: 'kicker', editableField: 'hero_kicker' },
            landing_hero_period_info: { blockType: 'hero', contentKey: 'period_info', editableField: 'hero_period_info' },
            landing_hero_cta_text: { blockType: 'hero', contentKey: 'cta_primary_text', editableField: 'hero_cta_text' },
            landing_hero_cta_primary_url: { blockType: 'hero', contentKey: 'cta_primary_url' },
            landing_hero_cta_secondary_text: { blockType: 'hero', contentKey: 'cta_secondary_text' },
            landing_hero_cta_secondary_url: { blockType: 'hero', contentKey: 'cta_secondary_url' },
            landing_hero_overlay_active: { blockType: 'hero', contentKey: 'overlay_active' },
            landing_hero_year: { blockType: 'hero', contentKey: 'year', editableField: 'hero_year' },
            landing_hero_image: { blockType: 'hero', contentKey: 'image' },

            // Persyaratan & Berkas (Hero Banner & Informasi)
            landing_label_persyaratan: { blockType: 'hero', contentKey: 'label_persyaratan', editableField: 'hero_label_persyaratan' },
            landing_persyaratan_umum: { blockType: 'hero', contentKey: 'persyaratan_umum', editableField: 'hero_persyaratan_list' },
            landing_label_dokumen: { blockType: 'hero', contentKey: 'label_dokumen', editableField: 'hero_label_dokumen' },
            landing_dokumen_pendaftaran: { blockType: 'hero', contentKey: 'dokumen_pendaftaran', editableField: 'hero_dokumen_list' },

            // Kuota & Formasi
            landing_kuota_heading: { blockType: 'kuota', contentKey: 'heading', editableField: 'kuota_heading' },
            landing_kuota_subheading: { blockType: 'kuota', contentKey: 'subheading', editableField: 'kuota_subheading' },

            // Tahapan
            landing_tahapan_heading: { blockType: 'tahapan', contentKey: 'heading', editableField: 'tahapan_heading' },
            landing_tahapan_summary: { blockType: 'tahapan', contentKey: 'summary', editableField: 'tahapan_summary' },
            landing_tahapan_btn_peta: { blockType: 'tahapan', contentKey: 'btn_peta_text', editableField: 'tahapan_btn_peta' },
            landing_tahapan_btn_pdf: { blockType: 'tahapan', contentKey: 'btn_pdf_text', editableField: 'tahapan_btn_pdf' },

            // FAQ
            landing_faq_heading: { blockType: 'faq', contentKey: 'heading', editableField: 'faq_heading' },

            // Footer
            landing_footer_alamat: { blockType: 'footer', contentKey: 'alamat', editableField: 'footer_alamat' },
            landing_footer_telepon: { blockType: 'footer', contentKey: 'telepon', editableField: 'footer_telepon' },
            landing_footer_email: { blockType: 'footer', contentKey: 'email', editableField: 'footer_email' },
            landing_footer_jam_layanan: { blockType: 'footer', contentKey: 'jam_layanan', editableField: 'footer_jam_layanan' },
            landing_footer_copyright: { blockType: 'footer', contentKey: 'copyright', editableField: 'footer_copyright' },

            // Kebijakan Privasi
            kebijakan_privasi_badge: { blockType: 'kebijakan', contentKey: 'badge', editableField: 'kebijakan_badge' },
            kebijakan_privasi_title: { blockType: 'kebijakan', contentKey: 'title', editableField: 'kebijakan_title' },
            kebijakan_privasi_updated_at: { blockType: 'kebijakan', contentKey: 'updated_at', editableField: 'kebijakan_updated_at' },
        };

        const mapping = fieldMap[field];
        if (mapping) {
            const targetBlock = this.blockManager.layout.find(b => b.type === mapping.blockType);
            if (targetBlock) {
                if (!targetBlock.content) targetBlock.content = {};
                targetBlock.content[mapping.contentKey] = val;
            }

            if (this.bridge && mapping.editableField) {
                this.bridge.updateText(mapping.editableField, val);
            }

            if (this.bridge && field === 'landing_hero_overlay_active') {
                this.bridge.sendToCanvas({
                    type: 'WP_SET_HERO_OVERLAY',
                    active: val === '1' || val === true || val === 'true',
                });
            }

            if (this.bridge && (field === 'landing_tahapan_heading' || field === 'landing_tahapan_summary')) {
                this.bridge.updateTahapanData({
                    heading: this.state['landing_tahapan_heading'],
                    summary: this.state['landing_tahapan_summary'],
                    steps: this.state['landing_tahapan_steps'],
                });
            }
        }
    }

    initTahapanStepInputs() {
        const stepInputs = document.querySelectorAll('.wp-tahapan-step-title, .wp-tahapan-step-desc');
        stepInputs.forEach(input => {
            input.addEventListener('input', () => {
                this.syncTahapanSteps(true);
            });
        });
    }

    syncTahapanSteps(notify = true) {
        const steps = [];
        for (let i = 0; i < 4; i++) {
            const num = String(i + 1).padStart(2, '0');
            const titleEl = document.getElementById(`input-step-title-${i}`);
            const descEl = document.getElementById(`input-step-desc-${i}`);
            const title = titleEl?.value?.trim() || `Pos ${num}`;
            const desc = descEl?.value?.trim() || '';
            steps.push({
                step: num,
                title: title,
                badge: title.toUpperCase(),
                desc: desc
            });
        }
        this.state['landing_tahapan_steps'] = steps;

        const tahapanBlock = this.blockManager?.layout?.find(b => b.type === 'tahapan') || this.getActiveBlockData();
        if (tahapanBlock) {
            if (!tahapanBlock.content) tahapanBlock.content = {};
            tahapanBlock.content.steps = steps;
        }

        if (this.bridge) {
            this.bridge.updateTahapanData({
                heading: this.state['landing_tahapan_heading'],
                summary: this.state['landing_tahapan_summary'],
                steps: steps,
            });
        }

        if (notify) {
            this.notifyDirty();
        }
    }

    initKebijakanSectionsInputs() {
        const container = document.getElementById('wp-kebijakan-sections-container');
        const btnAdd = document.getElementById('btn-add-kebijakan-pasal');

        if (container) {
            container.addEventListener('input', (e) => {
                if (e.target.matches('.wp-pasal-judul, .wp-pasal-konten, .wp-pasal-items, .wp-pasal-highlight')) {
                    this.syncKebijakanSections();
                }
            });

            container.addEventListener('click', (e) => {
                const removeBtn = e.target.closest('.btn-remove-pasal');
                if (removeBtn) {
                    const card = removeBtn.closest('.wp-kebijakan-item-card');
                    if (card && container.children.length > 1) {
                        card.remove();
                        this.reindexKebijakanCards();
                        this.syncKebijakanSections();
                    } else if (container.children.length <= 1) {
                        this.showLocalToast('Kebijakan Privasi minimal harus memiliki 1 pasal.', false);
                    }
                }
            });
        }

        if (btnAdd && container) {
            btnAdd.addEventListener('click', () => {
                const currentCount = container.querySelectorAll('.wp-kebijakan-item-card').length;
                const nextNum = currentCount + 1;
                const newCard = document.createElement('div');
                newCard.className = 'wp-card-subpanel wp-kebijakan-item-card';
                newCard.setAttribute('data-index', currentCount);
                newCard.style.cssText = 'background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 0.75rem; margin-bottom: 0.75rem; position: relative;';
                newCard.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                        <span class="wp-pasal-badge" style="font-weight: 700; font-size: 0.75rem; color: #1E40AF;">Pasal ${nextNum}</span>
                        <button type="button" class="btn-remove-pasal wp-icon-btn wp-btn-danger" data-index="${currentCount}" style="width: 22px; height: 22px; font-size: 11px;" title="Hapus Pasal Ini">✕</button>
                    </div>
                    <div class="wp-field-group" style="margin-bottom: 0.4rem;">
                        <label style="font-size: 0.75rem;">Judul Pasal</label>
                        <input type="text" class="wp-text-input wp-pasal-judul" placeholder="Contoh: Judul Pasal Baru">
                    </div>
                    <div class="wp-field-group" style="margin-bottom: 0.4rem;">
                        <label style="font-size: 0.75rem;">Isi Narasi Pasal</label>
                        <textarea class="wp-textarea-input wp-pasal-konten" rows="3" placeholder="Uraian isi pasal baru"></textarea>
                    </div>
                    <div class="wp-field-group" style="margin-bottom: 0.4rem;">
                        <label style="font-size: 0.75rem;">Butir Poin (Opsional, Per Baris)</label>
                        <textarea class="wp-textarea-input wp-pasal-items" rows="2" placeholder="Poin 1\nPoin 2"></textarea>
                    </div>
                    <div class="wp-field-group" style="margin-bottom: 0;">
                        <label style="font-size: 0.75rem;">Catatan Khusus / Highlight (Opsional)</label>
                        <input type="text" class="wp-text-input wp-pasal-highlight" placeholder="Teks sorotan khusus">
                    </div>
                `;
                container.appendChild(newCard);
                this.syncKebijakanSections();
            });
        }
    }

    reindexKebijakanCards() {
        const cards = document.querySelectorAll('.wp-kebijakan-item-card');
        cards.forEach((card, idx) => {
            card.setAttribute('data-index', idx);
            const badge = card.querySelector('.wp-pasal-badge');
            if (badge) badge.textContent = `Pasal ${idx + 1}`;
            const removeBtn = card.querySelector('.btn-remove-pasal');
            if (removeBtn) removeBtn.setAttribute('data-index', idx);
        });
    }

    syncKebijakanSections() {
        const sections = [];
        const cards = document.querySelectorAll('.wp-kebijakan-item-card');
        cards.forEach((card, idx) => {
            const judul = card.querySelector('.wp-pasal-judul')?.value?.trim() || '';
            const konten = card.querySelector('.wp-pasal-konten')?.value?.trim() || '';
            const itemsRaw = card.querySelector('.wp-pasal-items')?.value || '';
            const highlight = card.querySelector('.wp-pasal-highlight')?.value?.trim() || '';

            const items = itemsRaw.split('\n').map(s => s.trim()).filter(s => s.length > 0);
            const sectionData = {
                nomor: idx + 1,
                judul: judul,
                konten: konten
            };
            if (items.length > 0) sectionData.items = items;
            if (highlight.length > 0) sectionData.highlight = highlight;

            sections.push(sectionData);
        });
        this.state['kebijakan_privasi_sections'] = sections;
        if (this.bridge) {
            this.bridge.sendToCanvas({
                type: 'WP_UPDATE_KEBIJAKAN_DATA',
                sections: sections,
            });
        }
        this.notifyDirty();
    }

    updateFieldFromInline(fieldName, value) {
        this.state[fieldName] = value;
        const matchingInput = document.querySelector(`[data-field="${fieldName}"]`);
        if (matchingInput) {
            matchingInput.value = value;
        }

        const block = this.getActiveBlockData();
        if (block) {
            if (!block.content) block.content = {};
            const cleanKey = fieldName.replace(/^landing_/, '');
            block.content[cleanKey] = value;
        }

        this.notifyDirty();
    }

    setHeroImageFromMedia(mediaItem) {
        if (!mediaItem) return;

        const resolvedUrl = mediaItem.url || (`/storage/${(mediaItem.path || '').replace(/^\/+/, '')}`);
        this.state['landing_hero_image'] = mediaItem.path;
        const thumbEl = document.getElementById('wp-hero-banner-thumb');
        if (thumbEl) {
            thumbEl.src = resolvedUrl;
            thumbEl.onerror = () => {
                thumbEl.src = '/images/gedung-kominfo.jpg';
            };
        }

        const heroBlock = this.blockManager?.layout?.find(b => b.type === 'hero') || this.getActiveBlockData();
        if (heroBlock) {
            if (!heroBlock.content) heroBlock.content = {};
            heroBlock.content.image = mediaItem.path;
        }

        if (this.bridge) {
            this.bridge.updateHeroImage(resolvedUrl);
        }

        this.notifyDirty();
    }

    setPanduanPdfFromMedia(mediaItem) {
        if (!mediaItem) return;

        this.state['landing_panduan_pdf'] = mediaItem.path;
        this.state['landing_panduan_pdf_name'] = mediaItem.original_name || mediaItem.title || 'Panduan-Magang.pdf';

        const nameEl = document.getElementById('wp-pdf-active-name');
        const descEl = document.getElementById('wp-pdf-status-desc');
        const pageNameEl = document.getElementById('wp-page-pdf-name');
        const pageDescEl = document.getElementById('wp-page-pdf-status');
        const resetBtns = [
            document.getElementById('btn-reset-panduan-pdf'),
            document.getElementById('btn-page-reset-pdf')
        ];

        if (nameEl) nameEl.textContent = this.state['landing_panduan_pdf_name'];
        if (pageNameEl) pageNameEl.textContent = this.state['landing_panduan_pdf_name'];
        if (descEl) descEl.textContent = 'Berkas Kustom Diunggah';
        if (pageDescEl) pageDescEl.textContent = 'Berkas Kustom Diunggah';
        resetBtns.forEach(btn => { if (btn) btn.style.display = 'inline-flex'; });

        this.notifyDirty();
    }

    async resetPanduanPdf() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        try {
            const res = await fetch('/superadmin/site-editor/reset-panduan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();
            if (data.success) {
                this.state['landing_panduan_pdf'] = '';
                this.state['landing_panduan_pdf_name'] = 'Panduan-Resmi-Magang.pdf';

                const nameEl = document.getElementById('wp-pdf-active-name');
                const descEl = document.getElementById('wp-pdf-status-desc');
                const pageNameEl = document.getElementById('wp-page-pdf-name');
                const pageDescEl = document.getElementById('wp-page-pdf-status');
                const resetBtns = [
                    document.getElementById('btn-reset-panduan-pdf'),
                    document.getElementById('btn-page-reset-pdf')
                ];

                if (nameEl) nameEl.textContent = 'Panduan-Resmi-Magang.pdf';
                if (pageNameEl) pageNameEl.textContent = 'Panduan-Resmi-Magang.pdf';
                if (descEl) descEl.textContent = 'Generator PDF Bawaan Sistem';
                if (pageDescEl) pageDescEl.textContent = 'Generator PDF Bawaan Sistem';
                resetBtns.forEach(btn => { if (btn) btn.style.display = 'none'; });

                this.notifyDirty();
                return true;
            }
        } catch (e) {
            console.error('Failed to reset PDF:', e);
        }
        return false;
    }

    renderHeroSlidesList() {
        const container = document.getElementById('wp-hero-slides-list');
        if (!container) return;

        container.innerHTML = '';
        this.heroSlides.forEach((slide, idx) => {
            if (!slide.type) {
                if (slide.image && (!slide.title || slide.title.trim() === '') && (!slide.description || slide.description.trim() === '')) {
                    slide.type = 'banner';
                } else if (!slide.image && (slide.title || slide.description)) {
                    slide.type = 'announcement';
                } else {
                    slide.type = 'photo_text';
                }
            }

            const currentType = slide.type || 'photo_text';
            const card = document.createElement('div');
            card.className = 'wp-slide-card';
            card.setAttribute('data-slide-index', idx);

            const resolvedThumb = slide.image
                ? (slide.image.startsWith('http') || slide.image.startsWith('/') ? slide.image : `/storage/${slide.image.replace(/^\/+/, '')}`)
                : '/images/hero_dokumentasi_diskominfo.png';

            let typeNote = '';
            const infoIconSvg = '<svg class="wp-note-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';

            if (currentType === 'banner') {
                typeNote = `<div class="wp-slide-type-note">${infoIconSvg}<span><strong>Banner Poster</strong>: Gambar desain jadi (Canva/Photoshop/Figma). Teks web tidak menimpa gambar.</span></div>`;
            } else if (currentType === 'photo_text') {
                typeNote = `<div class="wp-slide-type-note">${infoIconSvg}<span><strong>Foto & Info</strong>: Foto dokumentasi kegiatan dengan teks keterangan dan tombol aksi.</span></div>`;
            } else {
                typeNote = `<div class="wp-slide-type-note">${infoIconSvg}<span><strong>Pengumuman</strong>: Kartu informasi pengumuman resmi instansi tanpa memerlukan foto.</span></div>`;
            }

            const showImage = currentType === 'banner' || currentType === 'photo_text';
            const showText = currentType === 'photo_text' || currentType === 'announcement';

            card.innerHTML = `
                <div class="wp-slide-card-header">
                    <span class="wp-slide-card-badge">Slide #${idx + 1}</span>
                    <button type="button" class="wp-btn-delete-slide" data-slide-index="${idx}" title="Hapus Slide Ini" ${this.heroSlides.length <= 1 ? 'disabled' : ''}>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </button>
                </div>
                <div class="wp-slide-type-switcher" role="group" aria-label="Tipe Slide">
                    <button type="button" class="wp-slide-type-btn ${currentType === 'banner' ? 'active' : ''}" data-type="banner" data-slide-index="${idx}" title="Banner Poster: Desain gambar jadi dari Canva / Photoshop / Figma">
                        ${this.getSlideTypeIconSvg('banner', 13)}
                        <span>Banner</span>
                    </button>
                    <button type="button" class="wp-slide-type-btn ${currentType === 'photo_text' ? 'active' : ''}" data-type="photo_text" data-slide-index="${idx}" title="Foto & Teks: Foto dokumentasi kegiatan dengan teks keterangan">
                        ${this.getSlideTypeIconSvg('photo_text', 13)}
                        <span>Foto & Teks</span>
                    </button>
                    <button type="button" class="wp-slide-type-btn ${currentType === 'announcement' ? 'active' : ''}" data-type="announcement" data-slide-index="${idx}" title="Pengumuman: Kartu teks pengumuman resmi tanpa foto">
                        ${this.getSlideTypeIconSvg('announcement', 13)}
                        <span>Pengumuman</span>
                    </button>
                </div>
                ${typeNote}
                ${showImage ? `
                <div class="wp-slide-thumb-row">
                    <img src="${resolvedThumb}" class="wp-slide-thumb-img" id="wp-slide-thumb-${idx}" alt="Banner Slide #${idx + 1}">
                    <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm btn-pick-slide-image" data-slide-index="${idx}">
                        Pilih Gambar
                    </button>
                </div>` : ''}
                ${showText ? `
                <div class="wp-field-group" style="margin-bottom: 0.4rem;">
                    <label style="font-size: 0.72rem;">Label Badge (Opsional)</label>
                    <input type="text" class="wp-text-input input-slide-badge" data-slide-index="${idx}" value="${this.escapeHtml(slide.badge || '')}" placeholder="Contoh: DOKUMENTASI RESMI">
                </div>
                <div class="wp-field-group" style="margin-bottom: 0.4rem;">
                    <label style="font-size: 0.72rem;">Judul Slide</label>
                    <input type="text" class="wp-text-input input-slide-title" data-slide-index="${idx}" value="${this.escapeHtml(slide.title || '')}" placeholder="Judul slide banner">
                </div>
                <div class="wp-field-group" style="margin-bottom: 0.4rem;">
                    <label style="font-size: 0.72rem;">Deskripsi / Keterangan</label>
                    <textarea class="wp-textarea-input input-slide-desc" data-slide-index="${idx}" rows="2" placeholder="Keterangan singkat dokumentasi">${this.escapeHtml(slide.description || '')}</textarea>
                </div>
                <div class="wp-input-with-icon" style="margin-bottom: 0;">
                    <input type="text" class="wp-text-input input-slide-cta-text" data-slide-index="${idx}" value="${this.escapeHtml(slide.cta_text || '')}" placeholder="Teks tombol (opsional)">
                    <input type="text" class="wp-text-input input-slide-cta-url" data-slide-index="${idx}" value="${this.escapeHtml(slide.cta_url || '')}" placeholder="URL (misal: #kuota)">
                </div>` : `
                <div class="wp-field-group" style="margin-bottom: 0.4rem;">
                    <label style="font-size: 0.72rem;">Tautan Klik Banner (Opsional)</label>
                    <input type="text" class="wp-text-input input-slide-cta-url" data-slide-index="${idx}" value="${this.escapeHtml(slide.cta_url || '')}" placeholder="URL tujuan klik (misal: #kuota)">
                </div>`}
            `;
            container.appendChild(card);
        });

        this.bindHeroSlideEvents();
    }

    bindHeroSlideEvents() {
        const container = document.getElementById('wp-hero-slides-list');
        if (!container) return;

        // 0. Ganti Tipe Slide
        container.querySelectorAll('.wp-slide-type-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const idx = parseInt(e.currentTarget.getAttribute('data-slide-index'), 10);
                const type = e.currentTarget.getAttribute('data-type');
                if (!isNaN(idx) && this.heroSlides[idx] && type) {
                    this.heroSlides[idx].type = type;
                    this.renderHeroSlidesList();
                    this.syncHeroSlides();
                    if (this.bridge && typeof this.bridge.navigateHeroSlide === 'function') {
                        this.bridge.navigateHeroSlide(idx);
                    }
                }
            });
        });

        // 1. Pick Image per slide
        container.querySelectorAll('.btn-pick-slide-image').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const idx = parseInt(e.currentTarget.getAttribute('data-slide-index'), 10);
                if (this.mediaLibrary && !isNaN(idx)) {
                    this.mediaLibrary.open('image', (selectedMedia) => {
                        this.setSlideImage(idx, selectedMedia);
                    });
                }
            });
        });

        // 2. Delete Slide
        container.querySelectorAll('.wp-btn-delete-slide').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const idx = parseInt(e.currentTarget.getAttribute('data-slide-index'), 10);
                if (!isNaN(idx)) {
                    this.deleteHeroSlide(idx);
                }
            });
        });

        // 3. Inputs (Badge, Title, Desc, CTA)
        const bindInput = (selector, key) => {
            container.querySelectorAll(selector).forEach(input => {
                input.addEventListener('input', (e) => {
                    const idx = parseInt(e.target.getAttribute('data-slide-index'), 10);
                    if (!isNaN(idx) && this.heroSlides[idx]) {
                        this.heroSlides[idx][key] = e.target.value;
                        this.syncHeroSlides();
                    }
                });
            });
        };

        bindInput('.input-slide-badge', 'badge');
        bindInput('.input-slide-title', 'title');
        bindInput('.input-slide-desc', 'description');
        bindInput('.input-slide-cta-text', 'cta_text');
        bindInput('.input-slide-cta-url', 'cta_url');
    }

    addHeroSlide() {
        const newIdx = this.heroSlides.length + 1;
        const uniqueId = 'slide-' + Date.now() + '-' + Math.random().toString(36).substring(2, 6);

        // Slide baru default 'photo_text' (Foto & Info) lengkap dengan field judul dan keterangan
        this.heroSlides.push({
            id: uniqueId,
            type: 'photo_text',
            image: '/images/hero_dokumentasi_diskominfo.png',
            badge: 'DOKUMENTASI RESMI',
            title: 'Dokumentasi Kegiatan ' + newIdx,
            description: 'Pengembangan ekosistem digital SPBE, infrastruktur jaringan, dan talenta muda bersama Pemerintah Kabupaten Sleman.',
            cta_text: 'Pelajari Lebih Lanjut',
            cta_url: '#kuota',
        });

        const newSlideIndex = this.heroSlides.length - 1;

        this.renderHeroSlidesList();
        this.syncHeroSlides();

        // 1. Geser kanvas iframe langsung ke slide baru
        if (this.bridge && typeof this.bridge.navigateHeroSlide === 'function') {
            this.bridge.navigateHeroSlide(newSlideIndex);
        }

        // 2. Auto-scroll panel inspector ke kartu slide baru + efek flash highlight
        setTimeout(() => {
            const container = document.getElementById('wp-hero-slides-list');
            if (container) {
                const newCard = container.querySelector(`[data-slide-index="${newSlideIndex}"]`);
                if (newCard) {
                    newCard.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    newCard.classList.add('wp-slide-card-new-highlight');
                    setTimeout(() => {
                        newCard.classList.remove('wp-slide-card-new-highlight');
                    }, 1600);

                    // Auto-focus input judul slide baru
                    const titleInput = newCard.querySelector('.input-slide-title');
                    if (titleInput) {
                        titleInput.focus();
                        titleInput.select();
                    }
                }
            }
        }, 80);

        // 3. Tampilkan toast konfirmasi penambahan slide
        this.showLocalToast(`Slide #${newIdx} berhasil ditambahkan.`, true);
    }

    deleteHeroSlide(idx) {
        if (this.heroSlides.length <= 1) return;
        this.heroSlides.splice(idx, 1);
        this.renderHeroSlidesList();
        this.syncHeroSlides();

        const targetSlideIdx = Math.max(0, idx - 1);
        if (this.bridge && typeof this.bridge.navigateHeroSlide === 'function') {
            this.bridge.navigateHeroSlide(targetSlideIdx);
        }
        this.showLocalToast('Slide berhasil dihapus.', true);
    }

    setSlideImage(idx, mediaItem) {
        if (!mediaItem || !this.heroSlides[idx]) return;
        const resolvedPath = mediaItem.path || '';
        const resolvedUrl = mediaItem.url || (`/storage/${resolvedPath.replace(/^\/+/, '')}`);

        this.heroSlides[idx].image = resolvedPath;
        const thumbEl = document.getElementById(`wp-slide-thumb-${idx}`);
        if (thumbEl) {
            thumbEl.src = resolvedUrl;
        }

        if (idx === 0) {
            this.state['landing_hero_image'] = resolvedPath;
            const hiddenInput = document.getElementById('input-hero-image');
            if (hiddenInput) hiddenInput.value = resolvedPath;
        }

        this.syncHeroSlides();

        if (this.bridge && typeof this.bridge.navigateHeroSlide === 'function') {
            this.bridge.navigateHeroSlide(idx);
        }
    }

    showLocalToast(msg, isSuccess = true) {
        if (typeof window.__wpShowToast === 'function') {
            window.__wpShowToast(
                isSuccess ? 'Berhasil' : 'Peringatan',
                msg,
                isSuccess ? 'success' : 'warning',
                4000
            );
            return;
        }

        const toastEl = document.getElementById('wp-toast');
        if (!toastEl) return;
        const msgEl = document.getElementById('wp-toast-msg');
        const iconEl = document.getElementById('wp-toast-icon');

        if (msgEl) msgEl.textContent = msg;
        if (iconEl) iconEl.textContent = isSuccess ? '✓' : '⚠️';

        toastEl.className = `wp-toast ${isSuccess ? 'toast-success' : 'toast-error'}`;
        toastEl.style.display = 'flex';

        if (this._toastTimeout) clearTimeout(this._toastTimeout);
        this._toastTimeout = setTimeout(() => {
            toastEl.style.display = 'none';
        }, 3000);
    }

    syncHeroSlides() {
        this.state['landing_hero_slides'] = this.heroSlides;
        if (this.heroSlides.length > 0 && this.heroSlides[0].image) {
            this.state['landing_hero_image'] = this.heroSlides[0].image;
            const hiddenInput = document.getElementById('input-hero-image');
            if (hiddenInput) hiddenInput.value = this.heroSlides[0].image;
        }

        const heroBlock = this.blockManager?.layout?.find(b => b.type === 'hero') || this.getActiveBlockData();
        if (heroBlock) {
            if (!heroBlock.content) heroBlock.content = {};
            heroBlock.content.slides = this.heroSlides;
            if (this.heroSlides.length > 0 && this.heroSlides[0].image) {
                heroBlock.content.image = this.heroSlides[0].image;
            }
        }

        if (this.bridge) {
            this.bridge.updateHeroSlides(this.heroSlides);
        }

        this.notifyDirty();
    }

    getDefaultFaqData() {
        return [
            {
                id: 'syarat',
                nama: 'Syarat dan Tata Cara Mendaftar',
                items: [
                    {
                        q: 'Bagaimana alur dan tata cara mendaftar magang di Diskominfo Sleman?',
                        a: 'Pendaftaran dilakukan secara mandiri atau kelompok melalui portal MAGKOM: (1) Buat akun portal, (2) Pilih formasi bidang dan bulan pelaksanaan yang masih tersedia kuotanya, (3) Unggah berkas persyaratan (Surat Pengantar Institusi, CV, Proposal, dan Transkrip/Rapor), (4) Tunggu verifikasi admin.'
                    },
                    {
                        q: 'Apakah siswa SMK dan mahasiswa perguruan tinggi memiliki syarat yang berbeda?',
                        a: 'Secara umum sama, namun untuk siswa SMK surat permohonan ditandatangani oleh Kepala Sekolah / Waka Hubin dan dilengkapi izin orang tua, sedangkan mahasiswa melampirkan surat pengantar resmi dari Fakultas/Jurusan.'
                    },
                    {
                        q: 'Apakah bisa mendaftar lebih dari satu bidang atau bulan magang?',
                        a: 'Tidak. Setiap peserta hanya dapat memilih satu bidang formasi dalam satu periode magang agar alokasi kuota adil bagi seluruh pendaftar.'
                    }
                ]
            },
            {
                id: 'akun',
                nama: 'Akun',
                items: [
                    {
                        q: 'Bagaimana jika tidak menerima email verifikasi akun?',
                        a: 'Silakan periksa folder Spam atau Promosi pada email Anda. Jika dalam 15 menit belum masuk, Anda dapat meminta kirim ulang link verifikasi melalui halaman login.'
                    },
                    {
                        q: 'Apakah akun portal bisa digunakan untuk mendaftar periode berikutnya?',
                        a: 'Akun Anda berlaku permanen di portal MAGKOM. Anda dapat menggunakannya kembali untuk melihat riwayat aktivitas atau mendaftar periode berikutnya setelah magang sebelumnya selesai.'
                    }
                ]
            },
            {
                id: 'kuis',
                nama: 'Kuis',
                items: [
                    {
                        q: 'Apakah ada kuis atau tes kompetensi saat proses seleksi?',
                        a: 'Beberapa bidang teknis seperti Aplikasi Informatika (Aptika) dan Persandian dapat menyertakan tes kompetensi dasar atau kuis teknis singkat untuk memastikan kecocokan dengan proyek yang akan dikerjakan.'
                    },
                    {
                        q: 'Berapa lama proses verifikasi berkas oleh admin?',
                        a: 'Proses verifikasi dan kurasi berkas administrasi membutuhkan waktu 1 hingga 3 hari kerja setelah pengajuan dikirimkan.'
                    }
                ]
            },
            {
                id: 'benefit',
                nama: 'Benefit Magang',
                items: [
                    {
                        q: 'Apa saja fasilitas dan benefit yang didapatkan peserta magang?',
                        a: 'Peserta mendapatkan bimbingan intensif dari Mentor ASN profesional, akses modul proyek nyata di pemerintahan, portofolio kerja kedinasan riil, serta pengalaman kerja standar industri.'
                    },
                    {
                        q: 'Apakah program magang di Diskominfo Sleman berbayar atau digaji?',
                        a: 'Program magang Diskominfo Sleman 100% GRATIS (tidak dipungut biaya apa pun) dan bersifat non-komersial tanpa kompensasi uang saku (unpaid government internship).'
                    }
                ]
            },
            {
                id: 'kewajiban',
                nama: 'Kewajiban Peserta Magang',
                items: [
                    {
                        q: 'Apa saja kewajiban dari peserta magang?',
                        a: 'Peserta wajib: (1) Mematuhi jam kerja ASN Diskominfo (Senin–Kamis 07.30–15.30 WIB, Jumat 07.30–14.30 WIB), (2) Mengenakan seragam sekolah/almamater rapi dan bersepatu, (3) Mengerjakan penugasan proyek tepat waktu, (4) Mengisi logbook mingguan secara disiplin, dan (5) Menjaga etika serta kerahasiaan data kedinasan.'
                    },
                    {
                        q: 'Apakah magang bisa dilaksanakan secara daring / WFH?',
                        a: 'Tidak. Seluruh pelaksanaan magang di Diskominfo Sleman dilaksanakan secara luring (Work From Office) di lingkungan kantor Pemkab Sleman untuk memastikan pembimbingan maksimal.'
                    }
                ]
            }
        ];
    }

    initFaqControls() {
        const selectCat = document.getElementById('select-faq-category');
        const inputCatName = document.getElementById('input-faq-category-name');
        const btnAddCat = document.getElementById('btn-add-faq-category');
        const btnDelCat = document.getElementById('btn-delete-faq-category');
        const btnAddItem = document.getElementById('btn-add-faq-item');

        selectCat?.addEventListener('change', (e) => {
            const idx = parseInt(e.target.value, 10);
            if (!isNaN(idx) && this.faqCategoriesData[idx]) {
                this.activeFaqCategoryIndex = idx;
                if (inputCatName) {
                    inputCatName.value = this.faqCategoriesData[idx].nama || '';
                }
                this.renderFaqItemsList();
            }
        });

        inputCatName?.addEventListener('input', (e) => {
            if (this.faqCategoriesData[this.activeFaqCategoryIndex]) {
                this.faqCategoriesData[this.activeFaqCategoryIndex].nama = e.target.value;
                this.renderFaqCategoriesSelect(true);
                this.syncFaqData();
            }
        });

        btnAddCat?.addEventListener('click', () => {
            this.addFaqCategory();
        });

        btnDelCat?.addEventListener('click', () => {
            this.deleteFaqCategory();
        });

        btnAddItem?.addEventListener('click', () => {
            this.addFaqItem();
        });
    }

    renderFaqCategoriesSelect(preserveSelection = false) {
        const selectCat = document.getElementById('select-faq-category');
        const inputCatName = document.getElementById('input-faq-category-name');
        if (!selectCat) return;

        if (this.faqCategoriesData.length === 0) {
            this.faqCategoriesData = this.getDefaultFaqData();
        }

        if (this.activeFaqCategoryIndex >= this.faqCategoriesData.length) {
            this.activeFaqCategoryIndex = Math.max(0, this.faqCategoriesData.length - 1);
        }

        selectCat.innerHTML = '';
        this.faqCategoriesData.forEach((cat, idx) => {
            const opt = document.createElement('option');
            opt.value = idx;
            opt.textContent = `${idx + 1}. ${cat.nama || 'Kategori ' + (idx + 1)}`;
            if (idx === this.activeFaqCategoryIndex) {
                opt.selected = true;
            }
            selectCat.appendChild(opt);
        });

        if (!preserveSelection && inputCatName) {
            inputCatName.value = this.faqCategoriesData[this.activeFaqCategoryIndex]?.nama || '';
        }

        const btnDelCat = document.getElementById('btn-delete-faq-category');
        if (btnDelCat) {
            btnDelCat.disabled = this.faqCategoriesData.length <= 1;
        }
    }

    renderFaqItemsList() {
        const container = document.getElementById('wp-faq-items-list');
        const badgeCount = document.getElementById('wp-faq-items-count-badge');
        if (!container) return;

        const currentCat = this.faqCategoriesData[this.activeFaqCategoryIndex];
        if (!currentCat || !Array.isArray(currentCat.items)) {
            container.innerHTML = '<div class="wp-faq-empty-state">Pilih atau tambahkan kategori FAQ terlebih dahulu.</div>';
            if (badgeCount) badgeCount.textContent = '0 Pertanyaan';
            return;
        }

        if (badgeCount) {
            badgeCount.textContent = `${currentCat.items.length} Pertanyaan`;
        }

        if (currentCat.items.length === 0) {
            container.innerHTML = `
                <div class="wp-faq-empty-state">
                    Belum ada pertanyaan pada kategori ini.<br>
                    Klik tombol <strong>+ Tambah Pertanyaan</strong> di atas untuk menambahkan.
                </div>
            `;
            return;
        }

        container.innerHTML = '';
        currentCat.items.forEach((item, idx) => {
            const card = document.createElement('div');
            card.className = 'wp-faq-item-card';
            card.setAttribute('data-faq-item-index', idx);

            card.innerHTML = `
                <div class="wp-faq-item-card-header">
                    <span class="wp-faq-item-badge">Pertanyaan #${idx + 1}</span>
                    <div style="display: flex; gap: 4px; align-items: center;">
                        <button type="button" class="wp-btn-icon-sm btn-faq-move-up" data-faq-item-index="${idx}" title="Geser Naik" ${idx === 0 ? 'disabled' : ''}>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="18 15 12 9 6 15"></polyline>
                            </svg>
                        </button>
                        <button type="button" class="wp-btn-icon-sm btn-faq-move-down" data-faq-item-index="${idx}" title="Geser Turun" ${idx === currentCat.items.length - 1 ? 'disabled' : ''}>
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                        <button type="button" class="wp-btn-icon-sm btn-faq-delete-item" data-faq-item-index="${idx}" title="Hapus Pertanyaan Ini">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="wp-faq-card-field">
                    <label class="wp-faq-card-label">Pertanyaan</label>
                    <input type="text" class="wp-text-input input-faq-q" data-faq-item-index="${idx}" value="${this.escapeHtml(item.q || '')}" placeholder="Contoh: Bagaimana alur dan tata cara mendaftar magang?">
                </div>
                <div class="wp-faq-card-field" style="margin-bottom: 0;">
                    <label class="wp-faq-card-label">Jawaban</label>
                    <textarea class="wp-textarea-input input-faq-a" data-faq-item-index="${idx}" rows="3" placeholder="Tuliskan rincian jawaban atau penjelasan lengkap di sini...">${this.escapeHtml(item.a || '')}</textarea>
                </div>
            `;
            container.appendChild(card);
        });

        this.bindFaqItemEvents();
    }

    bindFaqItemEvents() {
        const container = document.getElementById('wp-faq-items-list');
        if (!container) return;

        const currentCat = this.faqCategoriesData[this.activeFaqCategoryIndex];
        if (!currentCat || !Array.isArray(currentCat.items)) return;

        // 1. Text Inputs (Question & Answer)
        container.querySelectorAll('.input-faq-q').forEach(input => {
            input.addEventListener('input', (e) => {
                const idx = parseInt(e.target.getAttribute('data-faq-item-index'), 10);
                if (!isNaN(idx) && currentCat.items[idx]) {
                    currentCat.items[idx].q = e.target.value;
                    this.syncFaqData();
                }
            });
        });

        container.querySelectorAll('.input-faq-a').forEach(textarea => {
            textarea.addEventListener('input', (e) => {
                const idx = parseInt(e.target.getAttribute('data-faq-item-index'), 10);
                if (!isNaN(idx) && currentCat.items[idx]) {
                    currentCat.items[idx].a = e.target.value;
                    this.syncFaqData();
                }
            });
        });

        // 2. Delete item
        container.querySelectorAll('.btn-faq-delete-item').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const idx = parseInt(e.currentTarget.getAttribute('data-faq-item-index'), 10);
                if (!isNaN(idx)) {
                    this.deleteFaqItem(idx);
                }
            });
        });

        // 3. Move Up
        container.querySelectorAll('.btn-faq-move-up').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const idx = parseInt(e.currentTarget.getAttribute('data-faq-item-index'), 10);
                if (!isNaN(idx) && idx > 0) {
                    this.moveFaqItem(idx, -1);
                }
            });
        });

        // 4. Move Down
        container.querySelectorAll('.btn-faq-move-down').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const idx = parseInt(e.currentTarget.getAttribute('data-faq-item-index'), 10);
                if (!isNaN(idx) && idx < currentCat.items.length - 1) {
                    this.moveFaqItem(idx, 1);
                }
            });
        });
    }

    addFaqCategory() {
        const catNum = this.faqCategoriesData.length + 1;
        const newCat = {
            id: 'kategori-' + Date.now(),
            nama: 'Kategori FAQ ' + catNum,
            items: [
                {
                    q: 'Pertanyaan baru pada kategori ' + catNum + '?',
                    a: 'Jawaban dan penjelasan untuk pertanyaan baru ini.'
                }
            ]
        };
        this.faqCategoriesData.push(newCat);
        this.activeFaqCategoryIndex = this.faqCategoriesData.length - 1;
        this.renderFaqCategoriesSelect();
        this.renderFaqItemsList();
        this.syncFaqData();
    }

    deleteFaqCategory() {
        if (this.faqCategoriesData.length <= 1) {
            this.showLocalToast('Minimal harus ada 1 kategori FAQ pada sistem.', false);
            return;
        }

        const catName = this.faqCategoriesData[this.activeFaqCategoryIndex]?.nama || 'kategori ini';
        if (!confirm(`Hapus kategori "${catName}" beserta seluruh pertanyaan di dalamnya?`)) {
            return;
        }

        this.faqCategoriesData.splice(this.activeFaqCategoryIndex, 1);
        this.activeFaqCategoryIndex = Math.max(0, this.activeFaqCategoryIndex - 1);
        this.renderFaqCategoriesSelect();
        this.renderFaqItemsList();
        this.syncFaqData();
    }

    addFaqItem() {
        const currentCat = this.faqCategoriesData[this.activeFaqCategoryIndex];
        if (!currentCat) return;

        if (!Array.isArray(currentCat.items)) {
            currentCat.items = [];
        }

        const newNum = currentCat.items.length + 1;
        currentCat.items.push({
            q: 'Pertanyaan baru nomor ' + newNum + '?',
            a: 'Keterangan atau jawaban lengkap mengenai pertanyaan tersebut.'
        });

        this.renderFaqItemsList();
        this.syncFaqData();

        // Auto-focus input pertanyaan yang baru ditambahkan
        const newCard = document.querySelector(`[data-faq-item-index="${currentCat.items.length - 1}"] .input-faq-q`);
        if (newCard) {
            newCard.focus();
            newCard.select();
        }
    }

    deleteFaqItem(idx) {
        const currentCat = this.faqCategoriesData[this.activeFaqCategoryIndex];
        if (!currentCat || !Array.isArray(currentCat.items)) return;

        currentCat.items.splice(idx, 1);
        this.renderFaqItemsList();
        this.syncFaqData();
    }

    moveFaqItem(idx, direction) {
        const currentCat = this.faqCategoriesData[this.activeFaqCategoryIndex];
        if (!currentCat || !Array.isArray(currentCat.items)) return;

        const targetIdx = idx + direction;
        if (targetIdx < 0 || targetIdx >= currentCat.items.length) return;

        const temp = currentCat.items[idx];
        currentCat.items[idx] = currentCat.items[targetIdx];
        currentCat.items[targetIdx] = temp;

        this.renderFaqItemsList();
        this.syncFaqData();
    }

    syncFaqData() {
        this.state['landing_faq_data'] = this.faqCategoriesData;

        const faqBlock = this.blockManager?.layout?.find(b => b.type === 'faq') || this.getActiveBlockData();
        if (faqBlock) {
            if (!faqBlock.content) faqBlock.content = {};
            faqBlock.content.items = this.faqCategoriesData;
        }

        if (this.bridge) {
            this.bridge.updateFaqData(this.faqCategoriesData);
        }

        this.notifyDirty();
    }

    initBlockQuickSwitcher() {
        const headerCard = document.querySelector('.wp-block-header-card');
        if (!headerCard) return;

        let switcherWrap = document.getElementById('wp-block-quick-switcher-wrap');
        if (!switcherWrap) {
            switcherWrap = document.createElement('div');
            switcherWrap.id = 'wp-block-quick-switcher-wrap';
            switcherWrap.className = 'wp-block-quick-switcher-wrap';

            const box = document.createElement('div');
            box.className = 'wp-block-switcher-box';

            const iconPrefix = document.createElement('span');
            iconPrefix.id = 'wp-block-switcher-icon-prefix';
            iconPrefix.className = 'wp-block-switcher-icon-prefix';
            iconPrefix.innerHTML = this.getBlockIconSvg(this.activeBlock, 14);

            const chevron = document.createElement('span');
            chevron.className = 'wp-block-switcher-chevron';
            chevron.innerHTML = `<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>`;

            const select = document.createElement('select');
            select.id = 'wp-block-quick-switcher';
            select.className = 'wp-block-switcher-select';
            select.setAttribute('aria-label', 'Pilih Blok untuk Diedit');

            const blockOptions = [
                { type: 'hero', label: 'Hero Banner' },
                { type: 'informasi', label: 'Persyaratan & Dokumen' },
                { type: 'kuota', label: 'Kuota & Formasi' },
                { type: 'tahapan', label: 'Alur & Tahapan' },
                { type: 'faq', label: 'FAQ (Tanya Jawab)' },
                { type: 'footer', label: 'Footer & Kontak' },
                { type: 'kebijakan', label: 'Kebijakan Privasi' },
            ];

            blockOptions.forEach(optData => {
                const opt = document.createElement('option');
                opt.value = optData.type;
                opt.textContent = optData.label;
                if (optData.type === this.activeBlock) opt.selected = true;
                select.appendChild(opt);
            });

            select.addEventListener('change', (e) => {
                const blockType = e.target.value;
                const blockTitles = {
                    hero: 'Hero Banner',
                    informasi: 'Persyaratan & Dokumen',
                    kuota: 'Kuota & Formasi',
                    tahapan: 'Alur & Tahapan',
                    faq: 'FAQ',
                    footer: 'Footer & Kontak',
                    kebijakan: 'Kebijakan Privasi',
                };
                const title = blockTitles[blockType] || blockType.toUpperCase();
                this.setActiveBlock(blockType, title, `block-${blockType}-1`);
                if (this.bridge) {
                    this.bridge.scrollToBlock(blockType);
                    this.bridge.selectBlockInCanvas(blockType);
                }
            });

            box.appendChild(iconPrefix);
            box.appendChild(select);
            box.appendChild(chevron);
            switcherWrap.appendChild(box);
            headerCard.appendChild(switcherWrap);
        }
    }

    escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    notifyDirty() {
        if (typeof this.onStateDirty === 'function') {
            this.onStateDirty();
        }
    }

    getSerializedState() {
        return {
            ...this.state,
            landing_global_styles: this.globalStyles,
            global_styles: this.globalStyles,
        };
    }

    applySnapshot(snapshot) {
        if (!snapshot || typeof snapshot !== 'object') return;

        if (snapshot.landing_global_styles || snapshot.global_styles) {
            this.globalStyles = this.normalizeGlobalStyles(snapshot.landing_global_styles || snapshot.global_styles);
            if (this.bridge) {
                this.bridge.updateGlobalStyles(this.globalStyles);
            }
        }

        Object.keys(snapshot).forEach(key => {
            this.state[key] = snapshot[key];

            const input = document.querySelector(`[data-field="${key}"]`);
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = snapshot[key] === '1' || snapshot[key] === true;
                } else {
                    input.value = snapshot[key];
                }
            }
        });

        if (Array.isArray(snapshot.landing_faq_data)) {
            this.faqCategoriesData = JSON.parse(JSON.stringify(snapshot.landing_faq_data));
            this.renderFaqCategoriesSelect();
            this.renderFaqItemsList();
            if (this.bridge) {
                this.bridge.updateFaqData(this.faqCategoriesData);
            }
        }

        this.syncScopedControlsForActiveBlock();
    }
}
